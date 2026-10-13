<?php

namespace App\Http\Controllers;

use App\Models\Order;
use App\Models\Payment;
use App\Models\User;
use App\Notifications\SystemNotification;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Storage;
use Illuminate\View\View;
use Symfony\Component\HttpFoundation\StreamedResponse;

class PaymentController extends Controller
{
    public function proof(Request $request, Payment $payment): StreamedResponse
    {
        $user = $request->user();
        if (! in_array($user->role, ['admin', 'staff'], true)) {
            abort_unless(in_array($user->role, ['customer', 'user'], true)
                && $user->customerProfile?->id === $payment->order?->customer_id, 404);
        }

        $path = $payment->proof_image_path;
        abort_unless(is_string($path) && preg_match('/\Apayment-proofs\/[a-zA-Z0-9_-]+\.(?:jpg|jpeg|png|webp)\z/i', $path), 404);
        abort_unless(Storage::disk('local')->exists($path), 404);

        return Storage::disk('local')->response($path, null, ['Cache-Control' => 'private, no-store', 'X-Content-Type-Options' => 'nosniff']);
    }

    public function index(Request $request)
    {
        $search = trim($request->query('search', ''));
        $status = $request->query('status', '');

        $payments = Payment::with('order.customer');

        if ($search !== '') {
            $payments->where(function ($query) use ($search) {
                $query->whereHas('order.customer', function ($customerQuery) use ($search) {
                    $customerQuery->where('name', 'like', '%'.$search.'%');
                })->orWhere('payment_method', 'like', '%'.$search.'%')
                    ->orWhere('status', 'like', '%'.$search.'%');

                if (is_numeric($search)) {
                    $query->orWhere('order_id', $search);
                }
            });
        }

        if ($status !== '') {
            $payments->where('status', $status);
        }

        $payments = $payments->latest()->paginate(15)->withQueryString();

        if ($request->user()->role === 'staff') {
            return view('staff.payments.index', compact('payments', 'search', 'status'));
        }

        return view('admin.payments.index', compact('payments', 'search', 'status'));
    }

    public function create()
    {
        $orders = Order::with(['customer', 'service'])
            ->latest()
            ->get();

        $paymentRoutePrefix = 'staff.payments';

        return view('staff.payments.create', compact('orders', 'paymentRoutePrefix'));
    }

    public function store(Request $request)
    {
        $request->validate([
            'order_id' => 'required|exists:orders,id',
            'amount' => 'required|numeric|min:0.01',
            'payment_method' => 'required|in:Cash,GCash,Card,Bank Transfer',
            'reference_number' => 'nullable|string|max:255',
            'payment_date' => 'required|date|before_or_equal:today',
            'status' => 'required|in:Completed,Pending,Failed',
            'notes' => 'nullable|string',
        ]);

        $payment = Payment::create([
            'order_id' => $request->order_id,
            'amount' => $request->amount,
            'payment_method' => $request->payment_method,
            'reference_number' => $request->reference_number,
            'payment_date' => $request->payment_date,
            'status' => $request->status,
            'notes' => $request->notes,
        ]);

        $this->updateOrderPaymentStatus($request->order_id);

        foreach (User::where('role', 'admin')->get() as $admin) {
            $admin->notify(new SystemNotification(
                'Payment Recorded',
                'Staff recorded a payment for order #'.$payment->order_id.'.',
                route('admin.payments.show', $payment->id)
            ));
        }

        if ($payment->status === 'Completed') {
            return redirect()->route('staff.payments.show', $payment);
        }

        return redirect()->route('staff.orders.index')->with('success', 'Payment added successfully.');
    }

    public function show(Payment $payment)
    {
        $payment->load([
            'order.customer',
            'order.service',
            'order.staff',
        ]);

        if (auth()->user()->role === 'staff') {
            return view('staff.payments.show', compact('payment'));
        }

        return view('admin.payments.show', compact('payment'));
    }

    public function reviewCustomerPayment(Request $request, Payment $payment): RedirectResponse
    {
        abort_unless($payment->status === 'Pending', 403, 'Only pending payments can be reviewed.');

        $details = $request->validate([
            'status' => ['required', 'in:Completed,Failed'],
        ]);

        $payment->update(['status' => $details['status']]);
        $this->updateOrderPaymentStatus($payment->order_id);

        $payment->load('order.customer.user');
        $customerUser = $payment->order?->customer?->user;

        if ($customerUser !== null) {
            $customerUser->notify(new SystemNotification(
                'Payment '.$details['status'],
                'Your payment for order #'.$payment->order_id.' was marked '.$details['status'].'.',
                route('customer.payments.show', $payment->id)
            ));
        }

        $paymentRoutePrefix = $request->user()->role === 'admin' ? 'admin.payments' : 'staff.payments';

        return redirect()->route($paymentRoutePrefix.'.show', $payment)->with('success', 'Payment marked '.$details['status'].'.');
    }

    public function edit(Payment $payment): View
    {
        $payment->load('order.customer', 'order.service');

        return view('staff.payments.edit', compact('payment'));
    }

    public function update(Request $request, Payment $payment): RedirectResponse
    {
        $details = $request->validate([
            'amount' => ['required', 'numeric', 'min:0.01', 'decimal:0,2'],
            'payment_method' => ['required', 'in:Cash,GCash,Card,Bank Transfer'],
            'reference_number' => ['nullable', 'string', 'max:255'],
            'payment_date' => ['required', 'date', 'before_or_equal:today'],
            'status' => ['required', 'in:Completed,Pending,Failed'],
            'notes' => ['nullable', 'string', 'max:2000'],
        ]);

        DB::transaction(function () use ($payment, $details): void {
            Order::whereKey($payment->order_id)->lockForUpdate()->firstOrFail();
            $payment->update($details);
            $this->updateOrderPaymentStatus($payment->order_id);
        });

        return redirect()->route('staff.payments.index')->with('success', 'Payment updated successfully.');
    }

    public function destroy(Payment $payment): RedirectResponse
    {
        $proofPath = $payment->proof_image_path;
        DB::transaction(function () use ($payment): void {
            Order::whereKey($payment->order_id)->lockForUpdate()->firstOrFail();
            $payment->delete();
            $this->updateOrderPaymentStatus($payment->order_id);
        });

        if (is_string($proofPath) && preg_match('/\Apayment-proofs\/[a-zA-Z0-9_-]+\.(?:jpg|jpeg|png|webp)\z/i', $proofPath)
            && ! Payment::where('proof_image_path', $proofPath)->exists()) {
            Storage::disk('local')->delete($proofPath);
        }

        return redirect()->route('staff.payments.index')->with('success', 'Payment deleted successfully.');
    }

    private function updateOrderPaymentStatus(int $orderId): void
    {
        $order = Order::find($orderId);

        if (! $order) {
            return;
        }

        $paidAmount = Payment::where('order_id', $orderId)
            ->where('status', 'Completed')
            ->sum('amount');

        if ($paidAmount <= 0) {
            $status = 'Unpaid';
        } elseif ($paidAmount >= $order->total) {
            $status = 'Paid';
        } else {
            $status = 'Partial';
        }

        $order->update([
            'payment_status' => $status,
        ]);
    }
}
