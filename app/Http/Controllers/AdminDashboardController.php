<?php

namespace App\Http\Controllers;

use App\Models\Customer;
use App\Models\Order;
use App\Models\Payment;
use App\Models\Service;
use App\Models\Staff;
use Illuminate\Http\Request;

class AdminDashboardController extends Controller
{
    public function index(Request $request)
    {
        $period = $request->query('period', '7days');

        if (! in_array($period, ['7days', '30days', 'year'])) {
            $period = '7days';
        }

        // Total Orders
        $totalOrders = Order::count();

        // Total Customers
        $totalCustomers = Customer::count();

        // Total Staff
        $totalStaff = Staff::count();

        // Total Income from completed orders
        $totalIncome = Order::where('status', 'Completed')
            ->sum('total');

        // Order Status
        $pendingOrders = Order::where('status', 'Pending')->count();

        $inProgressOrders = Order::whereIn('status', ['Processing', 'Ready', 'In Progress'])->count();

        $completedOrders = Order::where('status', 'Completed')->count();

        $cancelledOrders = Order::where('status', 'Cancelled')->count();

        // Recent Orders
        $recentOrders = Order::with(['customer', 'service'])
            ->latest()
            ->take(5)
            ->get();

        // Recent activity from saved records
        $activities = [];

        foreach (Order::with('customer')->latest()->take(5)->get() as $order) {
            $activities[] = [
                'icon' => 'shopping-cart',
                'title' => 'New order #LVE-'.str_pad($order->id, 4, '0', STR_PAD_LEFT),
                'details' => $order->customer->name ?? 'Customer not found',
                'date' => $order->created_at,
            ];
        }

        foreach (Customer::latest('updated_at')->take(5)->get() as $customer) {
            $wasUpdated = $customer->updated_at && $customer->created_at->ne($customer->updated_at);
            $activities[] = [
                'icon' => 'user-plus',
                'title' => $wasUpdated ? 'Customer updated' : 'Customer added',
                'details' => $customer->name,
                'date' => $customer->updated_at,
            ];
        }

        foreach (Payment::with('order.customer')->latest('updated_at')->take(5)->get() as $payment) {
            $wasUpdated = $payment->updated_at && $payment->created_at->ne($payment->updated_at);
            $activities[] = [
                'icon' => 'credit-card',
                'title' => $wasUpdated ? 'Payment updated' : 'Payment recorded',
                'details' => $payment->status.' · ₱'.number_format($payment->amount, 2),
                'date' => $payment->updated_at,
            ];
        }

        foreach (Staff::latest('updated_at')->take(5)->get() as $staffMember) {
            $wasUpdated = $staffMember->updated_at && $staffMember->created_at->ne($staffMember->updated_at);
            $activities[] = [
                'icon' => 'user-cog',
                'title' => $wasUpdated ? 'Staff updated' : 'Staff added',
                'details' => $staffMember->name,
                'date' => $staffMember->updated_at,
            ];
        }

        foreach (Service::latest('updated_at')->take(5)->get() as $service) {
            $wasUpdated = $service->updated_at && $service->created_at->ne($service->updated_at);
            $activities[] = [
                'icon' => 'package-plus',
                'title' => $wasUpdated ? 'Service updated' : 'Service added',
                'details' => $service->service_name,
                'date' => $service->updated_at,
            ];
        }

        usort($activities, function ($firstActivity, $secondActivity) {
            return $secondActivity['date']->timestamp <=> $firstActivity['date']->timestamp;
        });

        $recentActivities = array_slice($activities, 0, 5);

        // Sales for the selected date range
        if ($period === 'year') {
            $startDate = now()->startOfYear();
        } elseif ($period === '30days') {
            $startDate = now()->subDays(29)->startOfDay();
        } else {
            $startDate = now()->subDays(6)->startOfDay();
        }

        $endDate = now()->endOfDay();
        $salesByDate = Payment::where('status', 'Completed')
            ->whereBetween('payment_date', [$startDate->toDateString(), $endDate->toDateString()])
            ->selectRaw('payment_date, SUM(amount) as total')
            ->groupBy('payment_date')
            ->pluck('total', 'payment_date');

        $salesBars = [];

        if ($period === 'year') {
            $monthDate = now()->startOfYear();

            for ($month = 1; $month <= now()->month; $month++) {
                $monthKey = $monthDate->format('Y-m');
                $salesBars[$monthKey] = [
                    'label' => $monthDate->format('M'),
                    'amount' => 0,
                ];
                $monthDate->addMonth();
            }

            foreach ($salesByDate as $date => $amount) {
                $monthKey = substr($date, 0, 7);
                $salesBars[$monthKey]['amount'] += (float) $amount;
            }
        } else {
            $dayCount = $period === '30days' ? 30 : 7;
            $dayDate = $startDate->copy();

            for ($day = 0; $day < $dayCount; $day++) {
                $dateKey = $dayDate->toDateString();
                $salesBars[$dateKey] = [
                    'label' => $period === '30days' ? $dayDate->format('d') : $dayDate->format('M d'),
                    'amount' => (float) ($salesByDate[$dateKey] ?? 0),
                ];
                $dayDate->addDay();
            }
        }

        $highestSales = max(array_column($salesBars, 'amount'));

        foreach ($salesBars as $key => $bar) {
            $salesBars[$key]['height'] = $highestSales > 0
                ? ($bar['amount'] / $highestSales) * 100
                : 0;
        }

        $statusDegrees = [
            'pending' => 0,
            'progress' => 0,
            'completed' => 0,
        ];

        if ($totalOrders > 0) {
            $statusDegrees['pending'] = ($pendingOrders / $totalOrders) * 360;
            $statusDegrees['progress'] = $statusDegrees['pending'] + ($inProgressOrders / $totalOrders) * 360;
            $statusDegrees['completed'] = $statusDegrees['progress'] + ($completedOrders / $totalOrders) * 360;
        }

        // Dashboard Statistics
        $stats = [
            'orders' => $totalOrders,
            'customers' => $totalCustomers,
            'staff' => $totalStaff,
            'income' => $totalIncome,
            'pending' => $pendingOrders,
            'in_progress' => $inProgressOrders,
            'completed' => $completedOrders,
            'cancelled' => $cancelledOrders,
        ];

        return view('admin.dashboard', compact(
            'stats',
            'recentOrders',
            'recentActivities',
            'salesBars',
            'period',
            'statusDegrees'
        ));
    }
}
