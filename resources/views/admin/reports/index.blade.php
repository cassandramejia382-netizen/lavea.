@extends('admin.layout', ['title' => 'Reports - LAVEA'])

@push('styles')

<style>
.lavea-admin-content * { margin: 0; padding: 0; }
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
<!-- SIDEBAR -->
   <!-- SIDEBAR -->
    


    <!-- TOPBAR -->
    


    <!-- MAIN -->
    

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

            <div class="filter-row">

                <div class="form-group">
                    <label>Report Type</label>

                    <select>
                        <option>All Reports</option>
                        <option>Orders Report</option>
                        <option>Payments Report</option>
                        <option>Services Report</option>
                        <option>Schedule Report</option>
                    </select>
                </div>

                <div class="form-group">
                    <label>From Date</label>

                    <input type="date" min="{{ now()->toDateString() }}">
                </div>

                <div class="form-group">
                    <label>To Date</label>

                    <input type="date" min="{{ now()->toDateString() }}">
                </div>

                <button class="btn btn-primary" type="button">
                    <i data-lucide="filter"></i>
                    Generate
                </button>

            </div>

        </div>


        <!-- SUMMARY -->
        <div class="report-grid">

            <div class="report-card">

                <div class="report-card-header">
                    <div>
                        <h3>{{ \App\Models\Order::count() }}</h3>
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
                        <h3>{{ \App\Models\Customer::count() }}</h3>
                        <p>Total Customers</p>
                    </div>

                    <div class="report-card-icon">
                        <i data-lucide="users"></i>
                    </div>
                </div>

            </div>


            <div class="report-card">

                <div class="report-card-header">
                    <div>
                        <h3>{{ \App\Models\Service::count() }}</h3>
                        <p>Total Services</p>
                    </div>

                    <div class="report-card-icon">
                        <i data-lucide="shirt"></i>
                    </div>
                </div>

            </div>


            <div class="report-card">

                <div class="report-card-header">
                    <div>
                        <h3>{{ \App\Models\Payment::count() }}</h3>
                        <p>Total Payments</p>
                    </div>

                    <div class="report-card-icon">
                        <i data-lucide="credit-card"></i>
                    </div>
                </div>

            </div>

        </div>


        <!-- RECENT REPORT -->
        <div class="table-card">

            <div class="table-header">
                <h3>Recent Orders Report</h3>
                <p>Latest laundry orders recorded in the system.</p>
            </div>

            @php
                $orders = \App\Models\Order::with([
                    'customer',
                    'service'
                ])
                ->latest()
                ->take(10)
                ->get();
            @endphp

            @if($orders->count() > 0)

                <table>

                    <thead>
                        <tr>
                            <th>Order ID</th>
                            <th>Customer</th>
                            <th>Service</th>
                            <th>Order Date</th>
                            <th>Status</th>
                            <th>Total</th>
                        </tr>
                    </thead>

                    <tbody>

                        @foreach($orders as $order)

                            <tr>

                                <td>
                                    #{{ $order->id }}
                                </td>

                                <td>
                                    {{ $order->customer->name ?? 'N/A' }}
                                </td>

                                <td>
                                    {{ $order->service->service_name ?? 'N/A' }}
                                </td>

                                <td>
                                    {{ $order->order_date?->format('M d, Y') ?? 'N/A' }}
                                </td>

                                <td>

                                    @php
                                        $statusClass = match($order->status) {
                                            'Completed' => 'completed',
                                            'Cancelled' => 'cancelled',
                                            default => 'pending',
                                        };
                                    @endphp

                                    <span class="status {{ $statusClass }}">
                                        {{ $order->status }}
                                    </span>

                                </td>

                                <td>
                                    ₱{{ number_format($order->total ?? 0, 2) }}
                                </td>

                            </tr>

                        @endforeach

                    </tbody>

                </table>

            @else

                <div class="empty">
                    No orders available for the report.
                </div>

            @endif

        </div>
@endsection
