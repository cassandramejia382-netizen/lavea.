<?php

namespace App\Http\Controllers;

use App\Models\Order;
use App\Models\Payment;
use App\Models\Service;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
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
        return view('customer.orders', [
            'orders' => $this->customerOrders($request)->with('service')->latest('order_date')->latest('id')->paginate(15),
        ]);
    }

    public function showOrder(Request $request, int $order): View
    {
        return view('customer.order', [
            'order' => $this->customerOrders($request)->with('service')->findOrFail($order),
        ]);
    }

    public function services(): View
    {
        return view('customer.services', ['services' => Service::orderBy('service_name')->paginate(12)]);
    }

    public function payments(Request $request): View
    {
        return view('customer.payments', [
            'payments' => $this->customerPayments($request)->with('order.service')->latest('payment_date')->latest('id')->paginate(15),
        ]);
    }

    public function showPayment(Request $request, int $payment): View
    {
        return view('customer.payment', [
            'payment' => $this->customerPayments($request)->with('order.service')->findOrFail($payment),
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
        ]);

        DB::transaction(function () use ($request, $customer, $details): void {
            $customer->update($details);
            $request->user()->update(['name' => $details['name']]);
        });

        return redirect()->route('customer.profile')->with('success', 'Your profile has been updated.');
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
}
