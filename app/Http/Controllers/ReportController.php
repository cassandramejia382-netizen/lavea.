<?php

namespace App\Http\Controllers;

use App\Models\Order;
use App\Models\Payment;
use App\Models\Service;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Http\Request;
use Illuminate\Validation\Rule;
use Illuminate\View\View;

class ReportController extends Controller
{
    public function index(Request $request): View
    {
        $filters = $request->validate([
            'report_type' => ['nullable', Rule::in(['all', 'orders', 'payments', 'services'])],
            'from_date' => ['nullable', 'date_format:Y-m-d'],
            'to_date' => array_filter(['nullable', 'date_format:Y-m-d', $request->filled('from_date') ? 'after_or_equal:from_date' : null]),
        ]);
        $reportType = $filters['report_type'] ?? 'all';
        $fromDate = $filters['from_date'] ?? null;
        $toDate = $filters['to_date'] ?? null;
        $orderQuery = $this->filterDates(Order::query(), 'order_date', $fromDate, $toDate);
        $paymentQuery = $this->filterDates(Payment::query(), 'payment_date', $fromDate, $toDate);
        $summary = [
            'orders' => (clone $orderQuery)->count(),
            'customers' => (clone $orderQuery)->distinct()->count('customer_id'),
            'services' => (clone $orderQuery)->distinct()->count('service_id'),
            'payments' => (clone $paymentQuery)->count(),
            'income' => (clone $paymentQuery)->where('status', 'Confirmed')->sum('amount'),
            'order_value' => (clone $orderQuery)->sum('total'),
        ];
        $orders = in_array($reportType, ['all', 'orders'], true)
            ? (clone $orderQuery)->with(['customer', 'service', 'staff'])->orderByDesc('order_date')->orderByDesc('id')->paginate(15, ['*'], 'orders_page')->withQueryString()
            : null;
        $payments = in_array($reportType, ['all', 'payments'], true)
            ? (clone $paymentQuery)->with(['order.customer', 'order.service'])->orderByDesc('payment_date')->orderByDesc('id')->paginate(15, ['*'], 'payments_page')->withQueryString()
            : null;
        $serviceOrders = fn (Builder $query): Builder => $this->filterDates($query, 'order_date', $fromDate, $toDate);
        $services = in_array($reportType, ['all', 'services'], true)
            ? Service::withCount(['orders as report_order_count' => $serviceOrders])
                ->withSum(['orders as report_quantity' => $serviceOrders], 'quantity')
                ->withSum(['orders as report_order_value' => $serviceOrders], 'total')
                ->orderBy('service_name')->paginate(15, ['*'], 'services_page')->withQueryString()
            : null;

        return view('admin.reports.index', compact('reportType', 'fromDate', 'toDate', 'summary', 'orders', 'payments', 'services'));
    }

    private function filterDates(Builder $query, string $column, ?string $fromDate, ?string $toDate): Builder
    {
        if ($fromDate !== null) {
            $query->whereDate($column, '>=', $fromDate);
        }
        if ($toDate !== null) {
            $query->whereDate($column, '<=', $toDate);
        }

        return $query;
    }
}
