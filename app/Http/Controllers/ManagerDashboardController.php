<?php

namespace App\Http\Controllers;

use App\Models\Order;
use App\Models\Payment;
use Illuminate\Http\Request;

class ManagerDashboardController extends Controller
{
    public function index(Request $request)
    {
        $orders = Order::query();

        $stats = [
            'total' => (clone $orders)->count(),
            'pending' => (clone $orders)->where('status', 'Pending')->count(),
            'processing' => (clone $orders)->whereIn('status', ['Processing', 'Ready', 'In Progress'])->count(),
            'completed' => (clone $orders)->where('status', 'Completed')->count(),
        ];

        $recentOrders = (clone $orders)->with(['customer', 'service', 'staff'])->latest()->take(6)->get();
        $todayOrders = (clone $orders)->with(['customer', 'service', 'staff'])->whereDate('order_date', today())->latest()->take(6)->get();
        $paymentsQuery = Payment::with(['order.customer', 'order.service'])->whereDate('payment_date', today());

        return view('staff.dashboard', [
            'stats' => $stats,
            'recentOrders' => $recentOrders,
            'todayOrders' => $todayOrders,
            'recentPayments' => $paymentsQuery->latest()->take(5)->get(),
        ]);
    }
}
