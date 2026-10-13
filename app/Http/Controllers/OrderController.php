<?php

namespace App\Http\Controllers;

use App\Models\Customer;
use App\Models\Order;
use App\Models\Service;
use App\Models\User;
use App\Notifications\SystemNotification;
use App\OrderStaffAssignment;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Validation\Rule;
use Illuminate\Validation\ValidationException;

class OrderController extends Controller
{
    public function __construct(private OrderStaffAssignment $assignment) {}

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

        $orderRoutePrefix = auth()->user()->role === 'manager' ? 'manager.orders' : 'admin.orders';
        $canManageOrders = auth()->user()->role === 'manager';

        return view($canManageOrders ? 'staff.orders.index' : 'admin.orders.index', compact('orders', 'search', 'orderRoutePrefix', 'canManageOrders'));
    }

    /**
     * Show form for creating a new order.
     */
    public function create()
    {
        $order = new Order;
        $customers = Customer::all();
        $services = Service::where('is_active', true)->get();
        $orderRoutePrefix = 'manager.orders';

        return view('staff.orders.form', compact(
            'customers',
            'services',
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
            'service_id' => ['required', Rule::exists('services', 'id')->where('is_active', true)],
            'staff_id' => ['nullable', 'integer'],
            'quantity' => 'required|numeric|min:1',
            'order_date' => 'required|date',
            'pickup_date' => 'nullable|date|after_or_equal:today|after_or_equal:order_date',
            'delivery_date' => ['nullable', 'date', 'after_or_equal:today', $request->filled('pickup_date') ? 'after_or_equal:pickup_date' : 'after_or_equal:order_date'],
            'status' => ['required', Rule::in(Order::STATUSES)],
            'notes' => 'nullable|string|max:2000',
        ]);

        $order = DB::transaction(function () use ($details, $request): Order {
            $service = Service::query()->lockForUpdate()->findOrFail($details['service_id']);
            if (! $service->is_active) {
                throw ValidationException::withMessages(['service_id' => 'This service is no longer active. Choose an active service.']);
            }
            $request->validate(['quantity' => $service->quantityRules()]);
            $staffId = $this->assignment->validateSelection($service, isset($details['staff_id']) ? (int) $details['staff_id'] : null, $details['status']);

            return Order::create(array_merge($details, [
                'staff_id' => $staffId,
                'total' => $service->price * $details['quantity'],
                'payment_status' => 'Unpaid',
            ]));
        });
        foreach (User::where('role', 'admin')->get() as $admin) {
            $admin->notify(new SystemNotification(
                'New Order',
                'Manager added order #'.$order->id.'.',
                route('admin.orders.show', $order->id)
            ));
        }

        return redirect()
            ->route('manager.orders.index')
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

        $orderRoutePrefix = auth()->user()->role === 'manager' ? 'manager.orders' : 'admin.orders';
        $canManageOrders = auth()->user()->role === 'manager';

        return view($canManageOrders ? 'staff.orders.show' : 'admin.orders.show', compact('order', 'orderRoutePrefix', 'canManageOrders'));
    }

    /**
     * Show form for editing an order.
     */
    public function edit(Order $order)
    {
        $order->load('staff');
        $customers = Customer::all();
        $services = Service::where('is_active', true)->orWhere('id', $order->service_id)->get();
        $orderRoutePrefix = 'manager.orders';

        return view('staff.orders.form', compact(
            'order',
            'customers',
            'services',
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
            'service_id' => ['required', Rule::exists('services', 'id')->where(fn ($query) => $query->where('is_active', true)->orWhere('id', $order->service_id))],
            'staff_id' => ['nullable', 'integer'],
            'quantity' => 'required|numeric|min:1',
            'order_date' => 'required|date',
            'pickup_date' => 'nullable|date|after_or_equal:today|after_or_equal:order_date',
            'delivery_date' => ['nullable', 'date', 'after_or_equal:today', $request->filled('pickup_date') ? 'after_or_equal:pickup_date' : 'after_or_equal:order_date'],
            'status' => ['required', Rule::in(Order::STATUSES)],
            'notes' => 'nullable|string|max:2000',
        ]);

        DB::transaction(function () use ($details, $request, $order): void {
            $lockedOrder = Order::query()->lockForUpdate()->findOrFail($order->id);
            $service = Service::query()->lockForUpdate()->findOrFail($details['service_id']);
            if (! $service->is_active && $service->id !== (int) $lockedOrder->service_id) {
                throw ValidationException::withMessages(['service_id' => 'This service is no longer active. Choose an active service.']);
            }
            $request->validate(['quantity' => $service->quantityRules()]);
            $staffId = array_key_exists('staff_id', $details) ? $details['staff_id'] : ($lockedOrder->status !== 'Pending' ? $lockedOrder->staff_id : null);
            $staffId = $this->assignment->validateSelection($service, $staffId === null ? null : (int) $staffId, $details['status'], $lockedOrder);
            $lockedOrder->update(array_merge($details, [
                'staff_id' => $staffId,
                'total' => $service->price * $details['quantity'],
            ]));
        });
        foreach (User::where('role', 'admin')->get() as $admin) {
            $admin->notify(new SystemNotification(
                'Order Updated',
                'Manager updated order #'.$order->id.'.',
                route('admin.orders.show', $order->id)
            ));
        }

        return redirect()
            ->route('manager.orders.show', $order)
            ->with('success', 'Order updated successfully.');
    }

    public function destroy(Order $order)
    {
        $order->delete();

        return redirect()->route('manager.orders.index')->with('success', 'Order deleted successfully.');
    }
}
