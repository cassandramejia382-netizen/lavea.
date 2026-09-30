﻿<!DOCTYPE html>
<html lang="en">
<head>
    @include('partials.theme-assets')
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">

    <title>Reports - LAVEA</title>

    <script src="https://unpkg.com/lucide@latest"></script>

    <style>
        * {
            margin: 0;
            padding: 0;
            box-sizing: border-box;
            font-family: Arial, Helvetica, sans-serif;
        }

        body {
            background: #f5f7fb;
            color: #1f2937;
        }

        /* SIDEBAR */
.sidebar {
    width: 255px;
    background: #111d38;
    color: white;
    padding: 25px 16px;
    position: fixed;
    top: 0;
    bottom: 0;
    left: 0;
}

.logo {
    padding: 5px 15px 35px;
}

.logo h1 {
    font-size: 27px;
    letter-spacing: 3px;
    font-weight: 700;
}

.logo p {
    color: #aebbd3;
    font-size: 11px;
    margin-top: 4px;
}

.menu {
    display: flex;
    flex-direction: column;
    gap: 6px;
}

.menu a {
    text-decoration: none;
    color: #dce5f7;
    padding: 13px 15px;
    border-radius: 8px;
    display: flex;
    align-items: center;
    gap: 14px;
    font-size: 14px;
    font-weight: 400;
    line-height: 1;
}

.menu a:hover,
.menu a.active {
    background: #4169c8;
    color: white;
}

.menu svg {
    width: 19px;
    height: 19px;
    flex-shrink: 0;
    stroke-width: 2;
}
        /* TOPBAR */
        .topbar {
            position: fixed;
            top: 0;
            left: 255px;
            right: 0;
            height: 75px;
            background: white;
            border-bottom: 1px solid #e5e7eb;
            display: flex;
            align-items: center;
            justify-content: space-between;
            padding: 0 30px;
            z-index: 10;
        }

        .search {
            width: 400px;
            position: relative;
        }

        .search input {
            width: 100%;
            padding: 11px 15px 11px 40px;
            border: 1px solid #e5e7eb;
            border-radius: 7px;
            outline: none;
            font-size: 14px;
        }

        .search svg {
            position: absolute;
            left: 13px;
            top: 11px;
            width: 18px;
            color: #9ca3af;
        }

        .admin-info {
            display: flex;
            align-items: center;
            gap: 12px;
        }

        .admin-avatar {
            width: 38px;
            height: 38px;
            background: #4169c8;
            color: white;
            border-radius: 50%;
            display: flex;
            align-items: center;
            justify-content: center;
            font-weight: bold;
        }

        .admin-text strong {
            display: block;
            font-size: 14px;
        }

        .admin-text span {
            font-size: 12px;
            color: #9ca3af;
        }

        /* MAIN */
        .main {
            margin-left: 255px;
            width: calc(100% - 255px);
            padding: 30px;
        }

        .page-header {
            display: flex;
            justify-content: space-between;
            align-items: center;
            margin-bottom: 25px;
        }

        .page-header h2 {
            font-size: 24px;
            color: #111827;
            margin-bottom: 6px;
        }

        .page-header p {
            color: #6b7280;
            font-size: 14px;
        }

        /* FILTER CARD */
        .filter-card {
            background: white;
            border: 1px solid #e5e7eb;
            border-radius: 10px;
            padding: 20px;
            margin-bottom: 25px;
        }

        .filter-title {
            font-size: 15px;
            font-weight: 600;
            margin-bottom: 15px;
            color: #111827;
        }

        .filter-row {
            display: flex;
            gap: 15px;
            align-items: end;
        }

        .form-group {
            flex: 1;
        }

        .form-group label {
            display: block;
            font-size: 12px;
            color: #6b7280;
            margin-bottom: 7px;
        }

        .form-group select,
        .form-group input {
            width: 100%;
            padding: 10px 12px;
            border: 1px solid #d1d5db;
            border-radius: 7px;
            outline: none;
            font-size: 14px;
            background: white;
        }

        .form-group select:focus,
        .form-group input:focus {
            border-color: #4169c8;
        }

        .btn {
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

        .btn svg {
            width: 17px;
            height: 17px;
        }

        .btn-primary {
            background: #4169c8;
            color: white;
        }

        .btn-primary:hover {
            background: #3558ad;
        }

        /* REPORT CARDS */
        .report-grid {
            display: grid;
            grid-template-columns: repeat(4, 1fr);
            gap: 18px;
            margin-bottom: 25px;
        }

        .report-card {
            background: white;
            border: 1px solid #e5e7eb;
            border-radius: 10px;
            padding: 20px;
        }

        .report-card-header {
            display: flex;
            align-items: center;
            justify-content: space-between;
            margin-bottom: 15px;
        }

        .report-card-icon {
            width: 40px;
            height: 40px;
            border-radius: 8px;
            background: #e8eefc;
            color: #4169c8;
            display: flex;
            align-items: center;
            justify-content: center;
        }

        .report-card-icon svg {
            width: 20px;
            height: 20px;
        }

        .report-card h3 {
            font-size: 25px;
            color: #111827;
            margin-bottom: 5px;
        }

        .report-card p {
            font-size: 13px;
            color: #6b7280;
        }

        /* TABLE CARD */
        .table-card {
            background: white;
            border: 1px solid #e5e7eb;
            border-radius: 10px;
            overflow: hidden;
        }

        .table-header {
            padding: 20px 25px;
            border-bottom: 1px solid #e5e7eb;
        }

        .table-header h3 {
            font-size: 17px;
            color: #111827;
        }

        .table-header p {
            font-size: 13px;
            color: #6b7280;
            margin-top: 4px;
        }

        table {
            width: 100%;
            border-collapse: collapse;
        }

        th {
            text-align: left;
            padding: 14px 20px;
            font-size: 12px;
            color: #6b7280;
            background: #f9fafb;
            border-bottom: 1px solid #e5e7eb;
            text-transform: uppercase;
        }

        td {
            padding: 16px 20px;
            font-size: 13px;
            border-bottom: 1px solid #eef0f4;
            color: #374151;
        }

        tr:last-child td {
            border-bottom: none;
        }

        .status {
            display: inline-block;
            padding: 5px 10px;
            border-radius: 20px;
            font-size: 11px;
            font-weight: 600;
        }

        .completed {
            background: #dcfce7;
            color: #15803d;
        }

        .pending {
            background: #fef3c7;
            color: #b45309;
        }

        .cancelled {
            background: #fee2e2;
            color: #dc2626;
        }

        .empty {
            text-align: center;
            padding: 40px;
            color: #9ca3af;
        }

        /* RESPONSIVE */
        @media (max-width: 1000px) {
            .report-grid {
                grid-template-columns: repeat(2, 1fr);
            }

            .filter-row {
                flex-wrap: wrap;
            }

            .form-group {
                min-width: 200px;
            }
        }

        @media (max-width: 800px) {
            .sidebar {
                width: 70px;
                padding: 20px 10px;
            }

            .logo h1 {
                font-size: 17px;
                text-align: center;
            }

            .logo p,
            .menu span {
                display: none;
            }

            .menu a {
                justify-content: center;
            }

            .main {
                margin-left: 70px;
                width: calc(100% - 70px);
                padding: 30px;
            }

            .admin-text {
                display: none;
            }
        }

        @media (max-width: 700px) {
            .report-grid {
                grid-template-columns: 1fr;
            }

            .filter-row {
                display: block;
            }

            .form-group {
                margin-bottom: 12px;
            }

            .filter-row .btn {
                width: 100%;
            }

            table {
                min-width: 700px;
            }

            .table-card {
                overflow-x: auto;
            }
        }
    </style>
    <link rel="stylesheet" href="{{ asset('css/admin-topbar.css') }}">
</head>

<body>

    <!-- SIDEBAR -->
   <!-- SIDEBAR -->
<aside class="sidebar">

    <div class="logo">
        <h1>LAVEA</h1>
        <p>Laundry Made Easy</p>
    </div>

    <nav class="menu">

        <a href="{{ route('admin.dashboard') }}">
            <i data-lucide="layout-dashboard"></i>
            <span>Dashboard</span>
        </a>

        <a href="{{ route('admin.customers.index') }}">
            <i data-lucide="users"></i>
            <span>Customers</span>
        </a>

        <a href="{{ route('admin.staff.index') }}">
            <i data-lucide="user-round-cog"></i>
            <span>Staff</span>
        </a>

        <a href="{{ route('admin.services.index') }}">
            <i data-lucide="package"></i>
            <span>Services</span>
        </a>

        <a href="{{ route('admin.orders.index') }}">
            <i data-lucide="clipboard-list"></i>
            <span>Orders</span>
        </a>

        <a href="{{ route('admin.payments.index') }}">
            <i data-lucide="credit-card"></i>
            <span>Payments / Transactions</span>
        </a>

        <a href="{{ route('admin.schedules.index') }}">
            <i data-lucide="calendar-days"></i>
            <span>Schedule</span>
        </a>

        <a href="{{ route('admin.reports') }}" class="active">
            <i data-lucide="bar-chart-3"></i>
            <span>Reports</span>
        </a>

        <a href="{{ route('admin.settings') }}">
    <i data-lucide="settings"></i>
    <span>Settings</span>
</a>

    </nav>

</aside>    


    <!-- TOPBAR -->
    @include('admin.partials.topbar')


    <!-- MAIN -->
    <main class="main">

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

    </main>


    <script>
        lucide.createIcons();
    </script>

</body>
</html>


