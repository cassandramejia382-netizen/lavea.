﻿<!DOCTYPE html>
<html lang="en">

<head>
    @include('partials.theme-assets')
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">

    <title>LAVEA | Payments</title>

    <script src="https://unpkg.com/lucide@latest"></script>

    <style>
        * {
            box-sizing: border-box;
            margin: 0;
            padding: 0;
            font-family: Arial, Helvetica, sans-serif;
        }

        body {
            background: #f5f7fb;
            color: #172554;
        }

        .layout {
            display: flex;
            min-height: 100vh;
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

        /* MAIN */
        .main {
            margin-left: 255px;
            width: calc(100% - 255px);
        }

        /* TOPBAR */
        .topbar {
            height: 75px;
            background: white;
            border-bottom: 1px solid #e8ecf3;
            display: flex;
            justify-content: space-between;
            align-items: center;
            padding: 0 28px;
        }

        .search {
            width: 400px;
            height: 40px;
            border: 1px solid #dfe5ef;
            border-radius: 8px;
            display: flex;
            align-items: center;
            padding: 0 12px;
            color: #8995aa;
        }

        .search svg {
            width: 18px;
            height: 18px;
        }

        .search input {
            border: none;
            outline: none;
            margin-left: 10px;
            width: 100%;
        }

        .admin {
            display: flex;
            align-items: center;
            gap: 10px;
        }

        .avatar {
            width: 38px;
            height: 38px;
            background: #e8eefb;
            border-radius: 50%;
            display: flex;
            align-items: center;
            justify-content: center;
        }

        .avatar svg {
            width: 18px;
            height: 18px;
        }

        .admin strong {
            display: block;
            font-size: 13px;
        }

        .admin span {
            font-size: 10px;
            color: #8995aa;
        }

        /* CONTENT */
        .content {
            padding: 30px;
        }

        .page-header {
            display: flex;
            justify-content: space-between;
            align-items: center;
            margin-bottom: 25px;
        }

        .page-header h1 {
            font-size: 26px;
            color: #10204a;
        }

        .page-header p {
            color: #7a879d;
            font-size: 13px;
            margin-top: 6px;
        }

        .add-btn {
            background: #4169c8;
            color: white;
            padding: 11px 17px;
            border-radius: 8px;
            text-decoration: none;
            font-size: 13px;
            display: flex;
            align-items: center;
            gap: 8px;
        }

        .add-btn:hover {
            background: #3459b1;
        }

        .success {
            background: #e6f8ef;
            color: #168458;
            padding: 13px 16px;
            border-radius: 8px;
            margin-bottom: 20px;
            font-size: 13px;
        }

        .filters {
            display: flex;
            align-items: center;
            gap: 10px;
            margin-bottom: 18px;
        }

        .filters select,
        .filter-btn {
            height: 40px;
            border: 1px solid #dfe5ef;
            border-radius: 8px;
            padding: 0 12px;
            background: white;
            color: #44526d;
            font-size: 13px;
        }

        .filter-btn {
            background: #4169c8;
            color: white;
            border: none;
            cursor: pointer;
        }

        /* CARD */
        .card {
            background: white;
            border: 1px solid #e7ebf2;
            border-radius: 10px;
            padding: 20px;
        }

        .table-container {
            overflow-x: auto;
        }

        table {
            width: 100%;
            border-collapse: collapse;
            min-width: 1000px;
        }

        th {
            background: #f8f9fc;
            color: #718099;
            font-size: 11px;
            text-align: left;
            padding: 13px;
        }

        td {
            padding: 14px 13px;
            border-bottom: 1px solid #edf0f5;
            font-size: 12px;
            color: #44526d;
        }

        .payment-id {
            font-weight: bold;
            color: #172554;
        }

        .customer-name {
            font-weight: bold;
            color: #172554;
        }

        .service-name {
            color: #44526d;
        }

        .amount {
            font-weight: bold;
            color: #172554;
            white-space: nowrap;
        }

        .method {
            color: #44526d;
        }

        .reference {
            color: #718099;
        }

        .date {
            white-space: nowrap;
        }

        /* STATUS */
        .status {
            display: inline-block;
            padding: 6px 10px;
            border-radius: 6px;
            font-size: 11px;
            font-weight: 600;
        }

        .completed {
            background: #e5f8ef;
            color: #168458;
        }

        .pending {
            background: #fff4d6;
            color: #a56a00;
        }

        .failed {
            background: #ffe9ec;
            color: #d14a5b;
        }

        /* ACTIONS */
        .actions {
            display: flex;
            gap: 7px;
        }

        .action {
            width: 34px;
            height: 34px;
            border-radius: 7px;
            display: flex;
            align-items: center;
            justify-content: center;
            text-decoration: none;
        }

        .action svg {
            width: 16px;
            height: 16px;
        }

        .view {
            background: #edf3ff;
            color: #3973e6;
        }

        .edit {
            background: #f0eaff;
            color: #7545d0;
        }

        .delete {
            background: #fff0f1;
            color: #e05263;
            border: none;
            cursor: pointer;
        }

        .empty {
            text-align: center;
            padding: 50px 20px;
            color: #8995aa;
        }

        .empty svg {
            width: 40px;
            height: 40px;
            margin-bottom: 10px;
        }

        /* RESPONSIVE */
        @media(max-width: 800px) {

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
            }

            .search {
                width: 200px;
            }
        }
    </style>
    <link rel="stylesheet" href="{{ asset('css/admin-topbar.css') }}">
</head>

<body>

<div class="layout">

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

            <a href="{{ route('admin.payments.index') }}" class="active">
                <i data-lucide="credit-card"></i>
                <span>Payments / Transactions</span>
            </a>

            <a href="{{ route('admin.schedules.index') }}">
    <i data-lucide="calendar-days"></i>
    <span>Schedule</span>
</a>

            <a href="{{ route('admin.reports') }}">
    <i data-lucide="bar-chart-3"></i>
    <span>Reports</span>
</a>

           <a href="{{ route('admin.settings') }}">
    <i data-lucide="settings"></i>
    <span>Settings</span>
</a>

        </nav>

    </aside>


    <!-- MAIN -->

    <main class="main">

        @include('admin.partials.topbar', ['paymentSearch' => true])


        <section class="content">

            <div class="page-header">

                <div>
                    <h1>Payments / Transactions</h1>
                    <p>View and monitor customer payments and transactions.</p>
                </div>

                <div class="lavea-page-header-actions">
                    @include('admin.partials.current-date')
                </div>

            </div>


            @if(session('success'))

                <div class="success">
                    {{ session('success') }}
                </div>

            @endif


            <form method="GET" action="{{ route('admin.payments.index') }}" class="filters">
                <select name="status" aria-label="Filter by payment status">
                    <option value="">All statuses</option>
                    @foreach (['Completed', 'Pending', 'Failed'] as $paymentStatus)
                        <option value="{{ $paymentStatus }}" @selected($status === $paymentStatus)>{{ $paymentStatus }}</option>
                    @endforeach
                </select>
                @if ($search !== '')
                    <input type="hidden" name="search" value="{{ $search }}">
                @endif
                <button type="submit" class="filter-btn">Filter</button>
            </form>

            <div class="card">

                <div class="table-container">

                    <table>

                        <thead>

                            <tr>

                                <th>Customer</th>

                                <th>Order ID</th>

                                <th>Amount</th>

                                <th>Payment Method</th>

                                <th>Payment Date</th>

                                <th>Status</th>

                                <th>View Details</th>

                            </tr>

                        </thead>


                        <tbody>

                            @forelse($payments as $payment)

                                <tr>

                                    <td>

                                        <span class="customer-name">
                                            {{ $payment->order->customer->name ?? 'N/A' }}
                                        </span>

                                    </td>


                                    <td>#{{ $payment->order_id }}</td>


                                    <td>

                                        <span class="amount">
                                            ₱{{ number_format($payment->amount, 2) }}
                                        </span>

                                    </td>


                                    <td>

                                        <span class="method">
                                            {{ $payment->payment_method }}
                                        </span>

                                    </td>


                                    <td class="date">

                                        {{ $payment->payment_date
                                            ? $payment->payment_date->format('M d, Y')
                                            : '—'
                                        }}

                                    </td>


                                    <td>

                                        <span class="status
                                            {{ strtolower($payment->status) }}">

                                            {{ $payment->status }}

                                        </span>

                                    </td>


                                    <td>

                                        <a href="{{ route('admin.payments.show', $payment->id) }}" class="action view" title="View Details">
                                            <i data-lucide="eye"></i>
                                        </a>

                                    </td>

                                </tr>


                            @empty

                                <tr>

                                    <td colspan="7">

                                        <div class="empty">

                                            <i data-lucide="credit-card"></i>

                                            <p>No payment transactions found.</p>

                                        </div>

                                    </td>

                                </tr>

                            @endforelse

                        </tbody>

                    </table>

                </div>

            </div>

        </section>

    </main>

</div>


<script>
    lucide.createIcons();
</script>

</body>

</html>

