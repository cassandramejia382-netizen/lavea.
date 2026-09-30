<?php

namespace App\Http\Controllers;

use App\Models\Customer;
use App\Models\Order;
use App\Models\Service;
use App\Models\Staff;
use App\Models\User;
use App\Notifications\SystemNotification;
use Illuminate\Http\Request;

class OrderController extends Controller
{
    /**
     * Display all orders.
     */
    public function index(Request $request)
    {
        $search = trim($request->query('search', ''));

        $orders = Order::with([
            'customer',
            'service',
            'staff',
        ]);

        if ($search !== '') {
            $orders->where(function ($query) use ($search) {
                $query->whereHas('customer', function ($customerQuery) use ($search) {
                    $customerQuery->where('name', 'like', '%'.$search.'%');
                })->orWhereHas('service', function ($serviceQuery) use ($search) {
                    $serviceQuery->where('service_name', 'like', '%'.$search.'%');
                })->orWhereHas('staff', function ($staffQuery) use ($search) {
                    $staffQuery->where('name', 'like', '%'.$search.'%');
                })->orWhere('status', 'like', '%'.$search.'%')
                    ->orWhere('payment_status', 'like', '%'.$search.'%');

                if (is_numeric($search)) {
                    $query->orWhere('id', $search);
                }
            });
        }

        $orders = $orders->latest()->paginate(15)->withQueryString();

        $orderRoutePrefix = auth()->user()->role === 'staff' ? 'staff.orders' : 'admin.orders';
        $canManageOrders = auth()->user()->role === 'staff';

        return view($canManageOrders ? 'staff.orders.index' : 'admin.orders.index', compact('orders', 'search', 'orderRoutePrefix', 'canManageOrders'));
    }

    /**
     * Show form for creating a new order.
     */
    public function create()
    {
        $order = new Order;
        $customers = Customer::all();
        $services = Service::all();
        $staff = Staff::where('status', 'Active')->orderBy('name')->get();
        $orderRoutePrefix = 'staff.orders';

        return view('staff.orders.form', compact(
            'customers',
            'services',
            'staff',
            'orderRoutePrefix',
            'order'
        ));
    }

    /**
     * Store a new order.
     */
    public function store(Request $request)
    {
        $details = $request->validate([
            'customer_id' => 'required|exists:customers,id',
            'service_id' => 'required|exists:services,id',
            'staff_id' => 'nullable|exists:staff,id',
            'quantity' => 'required|numeric|min:1',
            'order_date' => 'required|date',
            'pickup_date' => 'nullable|date|after_or_equal:today|after_or_equal:order_date',
            'delivery_date' => 'nullable|date|after_or_equal:today|after_or_equal:pickup_date',
            'status' => 'required|in:Pending,Processing,Completed,Cancelled',
            'notes' => 'nullable|string|max:2000',
        ]);

        $service = Service::findOrFail($request->service_id);

        $total = $service->price * $request->quantity;

        $order = Order::create([
            'customer_id' => $request->customer_id,
            'service_id' => $request->service_id,
            'staff_id' => $request->user()->staffProfile?->id,
            'quantity' => $request->quantity,
            'order_date' => $request->order_date,
            'pickup_date' => $request->pickup_date,
            'delivery_date' => $request->delivery_date,
            'total' => $total,
            'status' => $request->status,
            'payment_status' => 'Unpaid',
            'notes' => $details['notes'] ?? null,
        ]);

        foreach (User::where('role', 'admin')->get() as $admin) {
            $admin->notify(new SystemNotification(
                'New Order',
                'Staff added order #'.$order->id.'.',
                route('admin.orders.show', $order->id)
            ));
        }

        return redirect()
            ->route('staff.orders.index')
            ->with('success', 'Order added successfully.');
    }

    /**
     * Display one order.
     */
    public function show(Order $order)
    {
        $order->load([
            'customer',
            'service',
            'staff',
        ]);

        $orderRoutePrefix = auth()->user()->role === 'staff' ? 'staff.orders' : 'admin.orders';
        $canManageOrders = auth()->user()->role === 'staff';

        return view($canManageOrders ? 'staff.orders.show' : 'admin.orders.show', compact('order', 'orderRoutePrefix', 'canManageOrders'));
    }

    /**
     * Show form for editing an order.
     */
    public function edit(Order $order)
    {
        $customers = Customer::all();
        $services = Service::all();
        $staff = Staff::where('status', 'Active')->orderBy('name')->get();
        $orderRoutePrefix = 'staff.orders';

        return view('staff.orders.form', compact(
            'order',
            'customers',
            'services',
            'staff',
            'orderRoutePrefix'
        ));
    }

    /**
     * Update an order.
     */
    public function update(Request $request, Order $order)
    {
        $details = $request->validate([
            'customer_id' => 'required|exists:customers,id',
            'service_id' => 'required|exists:services,id',
            'staff_id' => 'nullable|exists:staff,id',
            'quantity' => 'required|numeric|min:1',
            'order_date' => 'required|date',
            'pickup_date' => 'nullable|date|after_or_equal:today|after_or_equal:order_date',
            'delivery_date' => 'nullable|date|after_or_equal:today|after_or_equal:pickup_date',
            'status' => 'required|in:Pending,Processing,Completed,Cancelled',
            'notes' => 'nullable|string|max:2000',
        ]);

        $service = Service::findOrFail($request->service_id);

        $total = $service->price * $request->quantity;

        $order->update([
            'customer_id' => $request->customer_id,
            'service_id' => $request->service_id,
            'staff_id' => $order->staff_id ?? $request->user()->staffProfile?->id,
            'quantity' => $request->quantity,
            'order_date' => $request->input('order_date', $order->order_date),
            'pickup_date' => $request->input('pickup_date', $order->pickup_date),
            'delivery_date' => $request->input('delivery_date', $order->delivery_date),
            'total' => $total,
            'status' => $request->status,
            'payment_status' => $order->payment_status,
            'notes' => $details['notes'] ?? null,
        ]);

        foreach (User::where('role', 'admin')->get() as $admin) {
            $admin->notify(new SystemNotification(
                'Order Updated',
                'Staff updated order #'.$order->id.'.',
                route('admin.orders.show', $order->id)
            ));
        }

        return redirect()
            ->route('staff.orders.show', $order)
            ->with('success', 'Order updated successfully.');
    }
}
