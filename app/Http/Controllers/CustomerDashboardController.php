<?php

namespace App\Http\Controllers;

use App\Models\Order;
use App\Models\Payment;
use App\Models\Service;
use App\Models\User;
use App\Notifications\SystemNotification;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Storage;
use Illuminate\View\View;

class CustomerDashboardController extends Controller
{
    public function index(Request $request): View
    {
        $orders = $this->customerOrders($request);

        return view('customer.dashboard', [
            'stats' => [
                'total' => (clone $orders)->count(),
                'pending' => (clone $orders)->where('status', 'Pending')->count(),
                'completed' => (clone $orders)->where('status', 'Completed')->count(),
                'payments' => $this->customerPayments($request)->where('status', 'Completed')->sum('amount'),
            ],
            'recentOrders' => $orders->with('service')->latest('order_date')->latest('id')->take(6)->get(),
            'services' => Service::orderBy('service_name')->take(6)->get(),
        ]);
    }

    public function orders(Request $request): View
    {
        $orderStatus = $request->query('order_status', '');
        $paymentStatus = $request->query('payment_status', '');
        $validOrderStatuses = ['Pending', 'Processing', 'Completed', 'Cancelled'];
        $validPaymentStatuses = ['Unpaid', 'Partial', 'Paid'];
        $orders = $this->customerOrders($request)->with('service');

        if (in_array($orderStatus, $validOrderStatuses, true)) {
            $orders->where('status', $orderStatus);
        } else {
            $orderStatus = '';
        }

        if (in_array($paymentStatus, $validPaymentStatuses, true)) {
            $orders->where('payment_status', $paymentStatus);
        } else {
            $paymentStatus = '';
        }

        return view('customer.orders', [
            'orders' => $orders->latest('order_date')->latest('id')->paginate(15)->withQueryString(),
            'orderStatus' => $orderStatus,
            'paymentStatus' => $paymentStatus,
            'recordStats' => [
                'processing' => $this->customerOrders($request)->where('status', 'Processing')->count(),
                'paid' => $this->customerOrders($request)->where('payment_status', 'Paid')->count(),
                'unpaid' => $this->customerOrders($request)->where('payment_status', 'Unpaid')->count(),
            ],
        ]);
    }

    public function showOrder(Request $request, int $order): View
    {
        $order = $this->customerOrders($request)
            ->with(['service', 'payments' => function (HasMany $query): void {
                $query->whereIn('status', ['Completed', 'Pending']);
            }])
            ->findOrFail($order);

        return view('customer.order', [
            'order' => $order,
            'balance' => $this->paymentBalance($order),
        ]);
    }

    public function createOrderPayment(Request $request, int $order): View|RedirectResponse
    {
        $order = $this->loadCustomerPayableOrder($request, $order);
        $balance = $this->paymentBalance($order);

        if ($balance <= 0) {
            return redirect()->route('customer.orders.show', $order)->with('success', 'There is no remaining balance to pay for this order.');
        }

        return view('customer.pay-order', compact('order', 'balance'));
    }

    public function storeOrderPayment(Request $request, int $order): RedirectResponse
    {
        $order = $this->loadCustomerPayableOrder($request, $order);
        $balance = $this->paymentBalance($order);
        abort_if($balance <= 0, 403, 'There is no remaining balance to pay for this order.');

        $details = $request->validate([
            'amount' => ['required', 'numeric', 'min:0.01'],
            'payment_method' => ['required', 'in:Cash,GCash,Card,Bank Transfer'],
            'reference_number' => ['nullable', 'string', 'max:255'],
            'notes' => ['nullable', 'string', 'max:2000'],
        ]);

        if ((float) $details['amount'] > $balance) {
            return back()->withErrors(['amount' => 'The payment cannot be more than the remaining balance of ₱'.number_format($balance, 2).'.'])->withInput();
        }

        if (in_array($details['payment_method'], ['GCash', 'Bank Transfer'], true) && blank($details['reference_number'] ?? null)) {
            return back()->withErrors(['reference_number' => 'Enter the transfer reference number so staff can verify your payment.'])->withInput();
        }

        $payment = Payment::create([
            'order_id' => $order->id,
            'amount' => $details['amount'],
            'payment_method' => $details['payment_method'],
            'reference_number' => $details['reference_number'] ?? null,
            'payment_date' => today(),
            'status' => 'Pending',
            'notes' => $details['notes'] ?? null,
        ]);

        foreach (User::where('role', 'admin')->get() as $admin) {
            $admin->notify(new SystemNotification(
                'Customer Payment Submitted',
                $order->customer->name.' submitted a payment for order #'.$order->id.'.',
                route('admin.payments.show', $payment->id)
            ));
        }

        if ($assignedStaffUser = $order->staff?->user) {
            $assignedStaffUser->notify(new SystemNotification(
                'Customer Payment Submitted',
                $order->customer->name.' submitted a payment for order #'.$order->id.'.',
                route('staff.payments.show', $payment->id)
            ));
        }

        return redirect()->route('customer.payments.index')->with('success', 'Your payment was submitted for staff verification.');
    }

    public function services(): View
    {
        return view('customer.services', ['services' => Service::orderBy('service_name')->paginate(12)]);
    }

    public function showService(Service $service): View
    {
        return view('customer.service-detail', compact('service'));
    }

    public function storeServiceOrder(Request $request, Service $service): RedirectResponse
    {
        $customer = $request->user()->customerProfile;
        abort_if($customer === null, 404);

        $details = $request->validate([
            'quantity' => ['required', 'integer', 'min:1', 'max:9'],
            'pickup_date' => ['required', 'date', 'after_or_equal:today'],
            'notes' => ['nullable', 'string', 'max:2000'],
        ]);

        $order = Order::create([
            'customer_id' => $customer->id,
            'service_id' => $service->id,
            'quantity' => $details['quantity'],
            'order_date' => today(),
            'pickup_date' => $details['pickup_date'],
            'status' => 'Pending',
            'payment_status' => 'Unpaid',
            'total' => $service->price * $details['quantity'],
            'notes' => $details['notes'] ?? null,
        ]);

        foreach (User::where('role', 'admin')->get() as $admin) {
            $admin->notify(new SystemNotification(
                'New Customer Order',
                $customer->name.' placed order #'.$order->id.'.',
                route('admin.orders.show', $order->id)
            ));
        }

        foreach (User::where('role', 'staff')->get() as $staffUser) {
            $staffUser->notify(new SystemNotification(
                'New Customer Order',
                $customer->name.' ordered '.$service->service_name.' (order #'.$order->id.').',
                route('staff.orders.show', $order->id)
            ));
        }

        return redirect()->route('customer.services.show', $service)->with('success', 'Your order was placed successfully.');
    }

    public function payments(Request $request): View
    {
        $search = trim($request->query('search', ''));
        $status = $request->query('status', '');
        $payments = $this->customerPayments($request);

        if ($search !== '') {
            $payments->where(function (Builder $query) use ($search): void {
                $query->where('payment_method', 'like', '%'.$search.'%')
                    ->orWhere('status', 'like', '%'.$search.'%')
                    ->orWhere('reference_number', 'like', '%'.$search.'%');

                if (is_numeric($search)) {
                    $query->orWhere('id', $search)->orWhere('order_id', $search);
                }

                $query->orWhereHas('order.service', function (Builder $serviceQuery) use ($search): void {
                    $serviceQuery->where('service_name', 'like', '%'.$search.'%');
                });
            });
        }

        if (in_array($status, ['Completed', 'Pending', 'Failed'], true)) {
            $payments->where('status', $status);
        }

        $payableOrders = $this->customerOrders($request)
            ->where('payment_status', '!=', 'Paid')
            ->with(['service', 'payments' => function (HasMany $query): void {
                $query->whereIn('status', ['Completed', 'Pending']);
            }])
            ->latest('order_date')
            ->get();

        return view('customer.payments', [
            'payments' => $payments->with('order.service')->latest('payment_date')->latest('id')->paginate(15)->withQueryString(),
            'search' => $search,
            'status' => $status,
            'paymentCount' => $this->customerPayments($request)->count(),
            'completedTotal' => $this->customerPayments($request)->where('status', 'Completed')->sum('amount'),
            'pendingTotal' => $this->customerPayments($request)->where('status', 'Pending')->sum('amount'),
            'payableOrders' => $payableOrders,
        ]);
    }

    public function showPayment(Request $request, int $payment): View
    {
        return view('customer.payment', [
            'payment' => $this->customerPayments($request)->with('order.customer', 'order.service', 'order.staff')->findOrFail($payment),
        ]);
    }

    public function profile(Request $request): View
    {
        return view('customer.profile', ['customer' => $request->user()->customerProfile]);
    }

    public function updateProfile(Request $request): RedirectResponse
    {
        $customer = $request->user()->customerProfile;
        abort_if($customer === null, 404);

        $details = $request->validate([
            'name' => ['required', 'string', 'max:255'],
            'phone' => ['required', 'string', 'max:20'],
            'address' => ['required', 'string', 'max:255'],
            'profile_photo' => ['nullable', 'image', 'mimes:jpg,jpeg,png,webp', 'max:5120'],
        ]);

        $user = $request->user();
        $previousPhoto = $user->profile_photo_path;
        $hasChanges = $user->name !== $details['name']
            || $customer->phone !== $details['phone']
            || $customer->address !== $details['address']
            || $request->hasFile('profile_photo');

        if (! $hasChanges) {
            return redirect()->route('customer.profile');
        }

        $newPhoto = $request->file('profile_photo')?->store('customer-profiles', 'public');
        unset($details['profile_photo']);

        DB::transaction(function () use ($request, $customer, $details, $newPhoto): void {
            $customer->update($details);
            $request->user()->update(['name' => $details['name']]);

            if ($newPhoto !== null) {
                $request->user()->profile_photo_path = $newPhoto;
                $request->user()->save();
            }
        });

        if ($newPhoto !== null && $previousPhoto !== null) {
            Storage::disk('public')->delete($previousPhoto);
        }

        return redirect()->route('customer.profile')->with('profile_saved', 'Your profile was saved successfully.');
    }

    /** @return Builder<Order> */
    private function customerOrders(Request $request): Builder
    {
        return Order::query()->where('customer_id', $request->user()->customerProfile?->id)->whereNotNull('customer_id');
    }

    /** @return Builder<Payment> */
    private function customerPayments(Request $request): Builder
    {
        return Payment::query()->whereIn('order_id', $this->customerOrders($request)->select('id'));
    }

    private function loadCustomerPayableOrder(Request $request, int $orderId): Order
    {
        return $this->customerOrders($request)
            ->with(['customer', 'service', 'staff.user', 'payments' => function (HasMany $query): void {
                $query->whereIn('status', ['Completed', 'Pending']);
            }])
            ->findOrFail($orderId);
    }

    private function paymentBalance(Order $order): float
    {
        $reservedAmount = $order->payments->sum('amount');

        return max(0, round((float) $order->total - (float) $reservedAmount, 2));
    }
}
