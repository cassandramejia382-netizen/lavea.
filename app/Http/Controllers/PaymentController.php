<?php

namespace App\Http\Controllers;

use App\GCashAccount;
use App\Models\Order;
use App\Models\Payment;
use App\Models\User;
use App\Notifications\SystemNotification;
use App\PaymentSubmission;
use Illuminate\Database\UniqueConstraintViolationException;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Gate;
use Illuminate\Support\Facades\Storage;
use Illuminate\Validation\ValidationException;
use Illuminate\View\View;
use Symfony\Component\HttpFoundation\StreamedResponse;
use Throwable;

class PaymentController extends Controller
{
    public function receipt(Request $request, Payment $payment): View
    {
        Gate::authorize('print-receipt', $payment);
        $payment->load(['order.customer', 'order.service', 'order.staff', 'processedBy']);

        $view = match ($request->user()->role) {
            'admin' => 'admin.payments.show',
            'manager' => 'staff.payments.show',
            default => 'customer.payment',
        };

        return view($view, ['payment' => $payment, 'autoPrint' => true]);
    }

    public function proof(Request $request, Payment $payment): StreamedResponse
    {
        $user = $request->user();
        if (! in_array($user->role, ['admin', 'manager'], true)) {
            abort_unless(in_array($user->role, ['customer'], true)
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

        if ($request->user()->role === 'manager') {
            return view('staff.payments.index', compact('payments', 'search', 'status'));
        }

        return view('admin.payments.index', compact('payments', 'search', 'status'));
    }

    public function create()
    {
        $orders = Order::with(['customer', 'service'])
            ->latest()
            ->get();

        $paymentRoutePrefix = 'manager.payments';

        return view('staff.payments.create', compact('orders', 'paymentRoutePrefix'));
    }

    public function store(Request $request): RedirectResponse
    {
        abort_unless($request->user()->role === 'manager', 403);
        $details = $this->validateSubmission($request);
        $order = Order::findOrFail($request->integer('order_id'));
        $proofPath = $request->hasFile('proof_image') && $details['payment_method'] !== 'Cash'
            ? $request->file('proof_image')->store('payment-proofs', 'local') : null;
        if ($proofPath === false) {
            throw ValidationException::withMessages(['proof_image' => 'The proof image could not be saved.']);
        }
        try {
            $payment = PaymentSubmission::submit($order, array_merge($details, [
                'processed_by' => $request->user()->id, 'proof_image_path' => $proofPath,
            ]));
        } catch (Throwable $exception) {
            if ($proofPath !== null && ! Payment::where('proof_image_path', $proofPath)->exists()) {
                Storage::disk('local')->delete($proofPath);
            }
            throw $exception;
        }
        $this->notifyAdmins($payment);

        return redirect()->route('manager.payments.show', $payment)->with('success', 'Payment submitted for Admin approval.');
    }

    public function show(Payment $payment)
    {
        $payment->load([
            'order.customer',
            'order.service',
            'order.staff',
            'processedBy',
        ]);

        if (auth()->user()->role === 'manager') {
            return view('staff.payments.show', compact('payment'));
        }

        $duplicatePayments = collect();
        $remainingBalance = max(0, $payment->order->total - $payment->order->payments()->where('status', 'Confirmed')->sum('amount'));
        if ($payment->status === 'Pending' && $payment->payment_method !== 'Cash' && filled($payment->reference_number)) {
            $duplicatePayments = Payment::whereKeyNot($payment->id)->where('status', 'Confirmed')
                ->where('payment_method', $payment->payment_method)
                ->whereRaw('LOWER(reference_number) = ?', [strtolower(trim($payment->reference_number))])->get(['id', 'order_id']);
        }

        return view('admin.payments.show', compact('payment', 'duplicatePayments', 'remainingBalance'));
    }

    public function reviewCustomerPayment(Request $request, Payment $payment): RedirectResponse
    {
        abort_unless($request->user()->role === 'admin', 403);
        $details = $request->validate([
            'status' => ['required', 'in:Confirmed,Rejected'],
            'rejection_reason' => ['required_if:status,Rejected', 'nullable', 'string', 'max:2000'],
        ]);
        try {
            DB::transaction(function () use ($request, $payment, $details): void {
                $order = Order::whereKey($payment->order_id)->lockForUpdate()->firstOrFail();
                $payment = Payment::whereKey($payment->id)->lockForUpdate()->firstOrFail();
                abort_unless($payment->status === 'Pending', 409, 'This payment has already been reviewed.');
                if ($details['status'] === 'Confirmed') {
                    $reviewErrors = [];
                    $reference = trim($payment->reference_number ?? '');
                    if ($payment->payment_method !== 'Cash' && $reference !== '') {
                        $duplicate = Payment::whereKeyNot($payment->id)->where('status', 'Confirmed')
                            ->where('payment_method', $payment->payment_method)
                            ->whereRaw('LOWER(reference_number) = ?', [strtolower($reference)])->pluck('id');
                        if ($duplicate->isNotEmpty()) {
                            $reviewErrors['reference_number'] = 'This reference belongs to confirmed payment(s) #'.implode(', #', $duplicate->all()).'. Payment #'.$payment->id.' remains Pending and needs a valid, unused reference.';
                        }
                        $payment->submission_key = hash('sha256', $payment->payment_method.'|'.strtolower($reference));
                    }
                    if (in_array($payment->payment_method, ['GCash', 'Bank Transfer'], true)
                        && (! $payment->proof_image_path || ! Storage::disk('local')->exists($payment->proof_image_path))) {
                        $reviewErrors['proof_image'] = 'An image proof is required before this payment can be confirmed.';
                    }
                    $paid = $order->payments()->where('status', 'Confirmed')->sum('amount');
                    if ((int) round(($paid + $payment->amount) * 100) > (int) round($order->total * 100)) {
                        $reviewErrors['amount'] = 'Payment #'.$payment->id.' is '.number_format($payment->amount, 2).' but the order has only '.number_format(max(0, $order->total - $paid), 2).' remaining. Correct the payment amount before confirmation.';
                    }
                    if ($reviewErrors !== []) {
                        throw ValidationException::withMessages($reviewErrors);
                    }
                }
                $payment->update([
                    'status' => $details['status'],
                    'rejection_reason' => $details['status'] === 'Rejected' ? trim($details['rejection_reason']) : null,
                    'reviewed_by' => $request->user()->id, 'reviewed_at' => now(),
                ]);
                $order->refreshPaymentStatus();
            }, 3);
        } catch (UniqueConstraintViolationException $exception) {
            throw ValidationException::withMessages(['reference_number' => 'This transfer reference was already processed.']);
        }
        $payment->refresh()->load('order.customer.user', 'processedBy');
        foreach (collect([$payment->order?->customer?->user, $payment->processedBy])->filter()->unique('id') as $recipient) {
            $prefix = $recipient->role === 'manager' ? 'manager' : 'customer';
            $recipient->notify(new SystemNotification('Payment '.$payment->status,
                'Payment #'.$payment->id.' was '.$payment->status.'.'.($payment->rejection_reason ? ' Reason: '.$payment->rejection_reason : ''),
                route($prefix.'.payments.show', $payment)));
        }

        return redirect()->route('admin.payments.show', $payment)->with('success', 'Payment '.$payment->status.'.');
    }

    public function edit(Payment $payment): View
    {
        abort_unless(auth()->user()->role === 'manager' && $payment->status === 'Rejected', 403, 'Only rejected payments can be corrected.');
        $payment->load('order.customer', 'order.service');

        return view('staff.payments.edit', compact('payment'));
    }

    public function update(Request $request, Payment $payment): RedirectResponse
    {
        abort_unless($request->user()->role === 'manager' && $payment->status === 'Rejected', 403, 'Only rejected payments can be resubmitted.');
        $details = $this->validateSubmission($request, $payment);
        $oldProof = $payment->proof_image_path;
        $newProof = $request->hasFile('proof_image') && $details['payment_method'] !== 'Cash'
            ? $request->file('proof_image')->store('payment-proofs', 'local') : null;
        if ($newProof === false) {
            throw ValidationException::withMessages(['proof_image' => 'The proof image could not be saved.']);
        }
        try {
            $payment = PaymentSubmission::submit($payment->order, array_merge($details, [
                'proof_image_path' => $details['payment_method'] === 'Cash' ? null : ($newProof ?? $oldProof),
                'processed_by' => $request->user()->id,
            ]), $payment);
        } catch (Throwable $exception) {
            if ($newProof !== null && ! Payment::where('proof_image_path', $newProof)->exists()) {
                Storage::disk('local')->delete($newProof);
            }
            throw $exception;
        }
        if ($oldProof && $oldProof !== $payment->proof_image_path && ! Payment::where('proof_image_path', $oldProof)->exists()) {
            Storage::disk('local')->delete($oldProof);
        }
        $this->notifyAdmins($payment);

        return redirect()->route('manager.payments.show', $payment)->with('success', 'Corrected payment resubmitted for Admin approval.');
    }

    /** @return array{amount: numeric-string|int|float, payment_method: string, reference_number?: ?string, payment_date: string, notes?: ?string} */
    private function validateSubmission(Request $request, ?Payment $payment = null): array
    {
        if ($request->input('payment_method') === 'GCash' && ! GCashAccount::details()['available']) {
            throw ValidationException::withMessages(['payment_method' => 'GCash payment is currently unavailable']);
        }
        $hasProof = $payment?->proof_image_path && Storage::disk('local')->exists($payment->proof_image_path);
        $requiresProof = in_array($request->input('payment_method'), ['GCash', 'Bank Transfer'], true) && ! $hasProof;
        $details = $request->validate([
            'order_id' => $payment === null ? ['required', 'exists:orders,id'] : ['exclude'],
            'amount' => ['required', 'numeric', 'decimal:0,2', 'min:0.01', 'max:99999999.99'],
            'payment_method' => ['required', 'in:Cash,GCash,Card,Bank Transfer'],
            'reference_number' => ['required_if:payment_method,GCash,Bank Transfer', 'nullable', 'string', 'max:255'],
            'payment_date' => ['required', 'date', 'before_or_equal:today'],
            'proof_image' => ['exclude_if:payment_method,Cash', $requiresProof ? 'required' : 'nullable', 'image', 'mimes:jpg,jpeg,png,webp', 'extensions:jpg,jpeg,png,webp', 'max:10240'],
            'notes' => ['nullable', 'string', 'max:2000'],
        ]);
        unset($details['proof_image'], $details['order_id']);

        return $details;
    }

    private function notifyAdmins(Payment $payment): void
    {
        foreach (User::where('role', 'admin')->get() as $admin) {
            $admin->notify(new SystemNotification('Payment Submitted',
                'Payment #'.$payment->id.' is awaiting approval for order #'.$payment->order_id.'.',
                route('admin.payments.show', $payment)));
        }
    }

    public function destroy(Payment $payment): RedirectResponse
    {
        abort(403, 'Payment history must be preserved.');
    }
}
