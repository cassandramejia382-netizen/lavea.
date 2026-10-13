@extends('admin.layout', ['title' => 'Reports - LAVEA'])

@push('styles')

<style>
.lavea-admin-content * {
    margin: 0;
    padding: 0;
}
.lavea-admin-content .page-header {
    display: flex;
    justify-content: space-between;
    align-items: center;
    margin-bottom: 25px;
}
.lavea-admin-content .page-header h2 {
    font-size: 24px;
    color: #111827;
    margin-bottom: 6px;
}
.lavea-admin-content .page-header p {
    color: #6b7280;
    font-size: 14px;
}
.lavea-admin-content .filter-card {
    background: white;
    border: 1px solid #e5e7eb;
    border-radius: 10px;
    padding: 20px;
    margin-bottom: 25px;
}
.lavea-admin-content .filter-title {
    font-size: 15px;
    font-weight: 600;
    margin-bottom: 15px;
    color: #111827;
}
.lavea-admin-content .filter-row {
    display: flex;
    gap: 15px;
    align-items: end;
}
.lavea-admin-content .form-group {
    flex: 1;
}
.lavea-admin-content .form-group label {
    display: block;
    font-size: 12px;
    color: #6b7280;
    margin-bottom: 7px;
}
.lavea-admin-content .form-group select,
.lavea-admin-content .form-group input {
    width: 100%;
    padding: 10px 12px;
    border: 1px solid #d1d5db;
    border-radius: 7px;
    outline: none;
    font-size: 14px;
    background: white;
}
.lavea-admin-content .form-group select:focus,
.lavea-admin-content .form-group input:focus {
    border-color: #4169c8;
}
.lavea-admin-content .btn {
    display: inline-flex;
    align-items: center;
    justify-content: center;
    gap: 8px;
    padding: 10px 18px;
    border-radius: 7px;
    border: none;
    cursor: pointer;
    font-size: 14px;
    text-decoration: none;
}
.lavea-admin-content .btn svg {
    width: 17px;
    height: 17px;
}
.lavea-admin-content .btn-primary {
    background: #4169c8;
    color: white;
}
.lavea-admin-content .btn-primary:hover {
    background: #3558ad;
}
.lavea-admin-content .report-grid {
    display: grid;
    grid-template-columns: repeat(4, 1fr);
    gap: 18px;
    margin-bottom: 25px;
}
.lavea-admin-content .report-card {
    background: white;
    border: 1px solid #e5e7eb;
    border-radius: 10px;
    padding: 20px;
}
.lavea-admin-content .report-card-header {
    display: flex;
    align-items: center;
    justify-content: space-between;
    margin-bottom: 15px;
}
.lavea-admin-content .report-card-icon {
    width: 40px;
    height: 40px;
    border-radius: 8px;
    background: #e8eefc;
    color: #4169c8;
    display: flex;
    align-items: center;
    justify-content: center;
}
.lavea-admin-content .report-card-icon svg {
    width: 20px;
    height: 20px;
}
.lavea-admin-content .report-card h3 {
    font-size: 25px;
    color: #111827;
    margin-bottom: 5px;
}
.lavea-admin-content .report-card p {
    font-size: 13px;
    color: #6b7280;
}
.lavea-admin-content .table-card {
    background: white;
    border: 1px solid #e5e7eb;
    border-radius: 10px;
    overflow: hidden;
}
.lavea-admin-content .table-header {
    padding: 20px 25px;
    border-bottom: 1px solid #e5e7eb;
}
.lavea-admin-content .table-header h3 {
    font-size: 17px;
    color: #111827;
}
.lavea-admin-content .table-header p {
    font-size: 13px;
    color: #6b7280;
    margin-top: 4px;
}
.lavea-admin-content table {
    width: 100%;
    border-collapse: collapse;
}
.lavea-admin-content th {
    text-align: left;
    padding: 14px 20px;
    font-size: 12px;
    color: #6b7280;
    background: #f9fafb;
    border-bottom: 1px solid #e5e7eb;
    text-transform: uppercase;
}
.lavea-admin-content td {
    padding: 16px 20px;
    font-size: 13px;
    border-bottom: 1px solid #eef0f4;
    color: #374151;
}
.lavea-admin-content tr:last-child td {
    border-bottom: none;
}
.lavea-admin-content .status {
    display: inline-block;
    padding: 5px 10px;
    border-radius: 20px;
    font-size: 11px;
    font-weight: 600;
}
.lavea-admin-content .completed {
    background: #dcfce7;
    color: #15803d;
}
.lavea-admin-content .pending {
    background: #fef3c7;
    color: #b45309;
}
.lavea-admin-content .cancelled {
    background: #fee2e2;
    color: #dc2626;
}
.lavea-admin-content .empty {
    text-align: center;
    padding: 40px;
    color: #9ca3af;
}
@media (max-width: 1000px) {
    .lavea-admin-content .report-grid {
        grid-template-columns: repeat(2, 1fr);
    }
    .lavea-admin-content .filter-row {
        flex-wrap: wrap;
    }
    .lavea-admin-content .form-group {
        min-width: 200px;
    }
}
@media (max-width: 700px) {
    .lavea-admin-content .report-grid {
        grid-template-columns: 1fr;
    }
    .lavea-admin-content .filter-row {
        display: block;
    }
    .lavea-admin-content .form-group {
        margin-bottom: 12px;
    }
    .lavea-admin-content .filter-row .btn {
        width: 100%;
    }
    .lavea-admin-content table {
        min-width: 700px;
    }
    .lavea-admin-content .table-card {
        overflow-x: auto;
    }
}
</style>
@endpush

@section('content')
        <div class="page-header">
            <div>
                <h2>Reports</h2>
                <p>View laundry business performance and transaction reports.</p>
            </div>
            @include('admin.partials.current-date')
        </div>

        <!-- FILTER -->
        <div class="filter-card">

            <div class="filter-title">
                Report Filter
            </div>

            <form class="filter-row" method="GET" action="{{ route('admin.reports') }}">

                <div class="form-group">
                    <label for="report_type">Report Type</label>

                    <select id="report_type" name="report_type">
                        @foreach(['all' => 'All Reports', 'orders' => 'Orders Report', 'payments' => 'Payments Report', 'services' => 'Services Report'] as $value => $label)
                            <option value="{{ $value }}" @selected(old('report_type', $reportType) === $value)>{{ $label }}</option>
                        @endforeach
                    </select>
                </div>

                <div class="form-group">
                    <label for="from_date">From Date</label>

                    <input id="from_date" name="from_date" type="date" value="{{ old('from_date', $fromDate) }}">
                </div>

                <div class="form-group">
                    <label for="to_date">To Date</label>

                    <input id="to_date" name="to_date" type="date" value="{{ old('to_date', $toDate) }}">
                </div>

                <button class="btn btn-primary" type="submit">
                    <i data-lucide="filter"></i>
                    Generate
                </button>

            </form>

        </div>

        @if($errors->any())
            <div class="filter-card" role="alert">
                @foreach($errors->all() as $error)
                    <p>{{ $error }}</p>
                @endforeach
            </div>
        @endif

        <!-- SUMMARY -->
        <div class="report-grid">

            <div class="report-card">

                <div class="report-card-header">
                    <div>
                        <h3>{{ $summary['orders'] }}</h3>
                        <p>Total Orders</p>
                    </div>

                    <div class="report-card-icon">
                        <i data-lucide="shopping-bag"></i>
                    </div>
                </div>

            </div>

            <div class="report-card">

                <div class="report-card-header">
                    <div>
                        <h3>{{ $summary['customers'] }}</h3>
                        <p>Customers with Orders</p>
                    </div>

                    <div class="report-card-icon">
                        <i data-lucide="users"></i>
                    </div>
                </div>

            </div>

            <div class="report-card">

                <div class="report-card-header">
                    <div>
                        <h3>{{ $summary['services'] }}</h3>
                        <p>Services Used</p>
                    </div>

                    <div class="report-card-icon">
                        <i data-lucide="shirt"></i>
                    </div>
                </div>

            </div>

            <div class="report-card">

                <div class="report-card-header">
                    <div>
                        <h3>{{ $summary['payments'] }}</h3>
                        <p>Total Payments</p>
                    </div>

                    <div class="report-card-icon">
                        <i data-lucide="credit-card"></i>
                    </div>
                </div>

            </div>

        </div>

        @if($orders !== null)
        <section class="table-card" style="margin-bottom:24px" id="orders-report">
            <div class="table-header">
                <h3>Orders Report</h3>
                <p>Filtered by order date. Order value: &#8369;{{ number_format($summary['order_value'], 2) }}.</p>
            </div>
            <div class="table-wrap">
                <table>
                    <thead><tr><th>Order ID</th><th>Customer</th><th>Service</th><th>Assigned Staff</th><th>Order Date</th><th>Status</th><th>Total</th></tr></thead>
                    <tbody>
                        @forelse($orders as $order)
                            <tr>
                                <td><a href="{{ route('admin.orders.show', $order) }}">#{{ $order->id }}</a></td>
                                <td>{{ $order->customer?->name ?? 'N/A' }}</td>
                                <td>{{ $order->service?->service_name ?? 'N/A' }}</td>
                                <td>{{ $order->staff?->name ?? 'Not assigned' }}</td>
                                <td>{{ $order->order_date?->format('M d, Y') ?? 'N/A' }}</td>
                                <td><span class="status {{ $order->status === 'Completed' ? 'completed' : ($order->status === 'Cancelled' ? 'cancelled' : 'pending') }}">{{ $order->status }}</span></td>
                                <td>&#8369;{{ number_format($order->total, 2) }}</td>
                            </tr>
                        @empty
                            <tr><td colspan="7" class="empty">No orders match the selected date range.</td></tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
            {{ $orders->links() }}
        </section>
        @endif
        @if($payments !== null)
        <section class="table-card" style="margin-bottom:24px" id="payments-report">
            <div class="table-header">
                <h3>Payments Report</h3>
                <p>Filtered by payment date. Confirmed income: &#8369;{{ number_format($summary['income'], 2) }}. Pending and Rejected payments are excluded from income.</p>
            </div>
            <div class="table-wrap">
                <table>
                    <thead><tr><th>Payment ID</th><th>Order ID</th><th>Customer</th><th>Payment Date</th><th>Method</th><th>Reference</th><th>Status</th><th>Amount</th></tr></thead>
                    <tbody>
                        @forelse($payments as $payment)
                            <tr>
                                <td><a href="{{ route('admin.payments.show', $payment) }}">#{{ $payment->id }}</a></td>
                                <td>#{{ $payment->order_id }}</td>
                                <td>{{ $payment->order?->customer?->name ?? 'N/A' }}</td>
                                <td>{{ $payment->payment_date?->format('M d, Y') ?? 'N/A' }}</td>
                                <td>{{ $payment->payment_method }}</td>
                                <td>{{ $payment->reference_number ?: 'N/A' }}</td>
                                <td><span class="status {{ $payment->status === 'Confirmed' ? 'completed' : ($payment->status === 'Rejected' ? 'cancelled' : 'pending') }}">{{ $payment->status }}</span></td>
                                <td>&#8369;{{ number_format($payment->amount, 2) }}</td>
                            </tr>
                        @empty
                            <tr><td colspan="8" class="empty">No payments match the selected date range.</td></tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
            {{ $payments->links() }}
        </section>
        @endif
        @if($services !== null)
        <section class="table-card" id="services-report">
            <div class="table-header">
                <h3>Services Report</h3>
                <p>Service usage for orders in the selected order-date range. Order value is separate from confirmed income.</p>
            </div>
            <div class="table-wrap">
                <table>
                    <thead><tr><th>Service</th><th>Price</th><th>Availability</th><th>Orders</th><th>Quantity</th><th>Order Value</th></tr></thead>
                    <tbody>
                        @forelse($services as $service)
                            <tr>
                                <td>{{ $service->service_name }}</td>
                                <td>&#8369;{{ number_format($service->price, 2) }} / {{ $service->billingUnit() }}</td>
                                <td>{{ $service->is_active ? 'Active' : 'Inactive' }}</td>
                                <td>{{ $service->report_order_count }}</td>
                                <td>{{ number_format($service->report_quantity ?? 0, 2) }} {{ $service->billingUnit() }}</td>
                                <td>&#8369;{{ number_format($service->report_order_value ?? 0, 2) }}</td>
                            </tr>
                        @empty
                            <tr><td colspan="6" class="empty">No services available for the report.</td></tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
            {{ $services->links() }}
        </section>
        @endif
@endsection
