<?php

namespace App\Http\Controllers;

use App\Models\Order;
use App\Models\Schedule;
use Illuminate\Http\Request;

class ScheduleController extends Controller
{
    public function staffIndex()
    {
        $schedules = Schedule::with(['order.customer', 'order.service'])
            ->orderBy('schedule_date')->orderBy('schedule_time')->get();

        return view('staff.schedules.index', compact('schedules'));
    }

    public function index()
    {
        $schedules = Schedule::with([
            'order.customer',
            'order.service',
            'order.staff',
        ])
            ->orderBy('schedule_date')
            ->orderBy('schedule_time')
            ->get();

        $routePrefix = auth()->user()->role === 'manager' ? 'manager.schedules' : 'admin.schedules';

        return view('admin.schedules.index', compact('schedules', 'routePrefix'));
    }

    public function create()
    {
        $orders = Order::with([
            'customer',
            'service',
        ])
            ->latest()
            ->get();

        $routePrefix = auth()->user()->role.'.schedules';

        return view('admin.schedules.create', compact('orders', 'routePrefix'));
    }

    public function store(Request $request)
    {
        $request->validate([
            'order_id' => 'required|exists:orders,id',
            'type' => 'required|in:Pickup,Delivery',
            'schedule_date' => 'required|date|after_or_equal:today',
            'schedule_time' => 'required',
            'status' => 'required|in:Scheduled,On Time,Completed,Cancelled',
            'notes' => 'nullable|string',
        ]);

        Schedule::create([
            'order_id' => $request->order_id,
            'type' => $request->type,
            'schedule_date' => $request->schedule_date,
            'schedule_time' => $request->schedule_time,
            'status' => $request->status,
            'notes' => $request->notes,
        ]);

        return redirect()->route(auth()->user()->role.'.schedules.index')->with('success', 'Schedule added successfully.');
    }

    public function show(Schedule $schedule)
    {
        $schedule->load([
            'order.customer',
            'order.service',
            'order.staff',
        ]);

        $routePrefix = auth()->user()->role === 'manager' ? 'manager.schedules' : 'admin.schedules';

        return view('admin.schedules.show', compact('schedule', 'routePrefix'));
    }

    public function edit(Schedule $schedule)
    {
        $orders = Order::with([
            'customer',
            'service',
        ])
            ->latest()
            ->get();

        $routePrefix = auth()->user()->role.'.schedules';

        return view('admin.schedules.edit', compact(
            'schedule',
            'orders',
            'routePrefix',
        ));
    }

    public function update(Request $request, Schedule $schedule)
    {
        $request->validate([
            'order_id' => 'required|exists:orders,id',
            'type' => 'required|in:Pickup,Delivery',
            'schedule_date' => 'required|date|after_or_equal:today',
            'schedule_time' => 'required',
            'status' => 'required|in:Scheduled,On Time,Completed,Cancelled',
            'notes' => 'nullable|string',
        ]);

        $schedule->update([
            'order_id' => $request->order_id,
            'type' => $request->type,
            'schedule_date' => $request->schedule_date,
            'schedule_time' => $request->schedule_time,
            'status' => $request->status,
            'notes' => $request->notes,
        ]);

        return redirect()->route(auth()->user()->role.'.schedules.index')->with('success', 'Schedule updated successfully.');
    }

    public function destroy(Schedule $schedule)
    {
        $schedule->delete();

        return redirect()
            ->route(auth()->user()->role.'.schedules.index')
            ->with('success', 'Schedule deleted successfully.');
    }
}
