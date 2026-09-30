﻿<!DOCTYPE html>
<html lang="en">

<head>
    @include('partials.theme-assets')
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">

    <title>LAVEA | Orders</title>

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
            min-width: 950px;
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

        .customer-name {
            font-weight: bold;
            color: #172554;
        }

        .service-name {
            color: #44526d;
        }

        .staff-name {
            color: #44526d;
        }

        .date {
            white-space: nowrap;
        }

        .price {
            font-weight: bold;
            color: #172554;
            white-space: nowrap;
        }

        .status {
            display: inline-block;
            padding: 6px 10px;
            border-radius: 6px;
            font-size: 11px;
            font-weight: 600;
        }

        .pending {
            background: #fff4d6;
            color: #a56a00;
        }

        .processing {
            background: #e9f1ff;
            color: #3565b8;
        }

        .ready {
            background: #e9e7ff;
            color: #654bc4;
        }

        .completed {
            background: #e5f8ef;
            color: #168458;
        }

        .cancelled {
            background: #ffe9ec;
            color: #d14a5b;
        }

        .payment {
            font-size: 11px;
            font-weight: 600;
        }

        .paid {
            color: #168458;
        }

        .partial {
            color: #a56a00;
        }

        .unpaid {
            color: #d14a5b;
        }

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
            @if (auth()->user()->role === 'staff')
                <a href="{{ route('staff.dashboard') }}">
                    <i data-lucide="layout-dashboard"></i>
                    <span>Dashboard</span>
                </a>
                <a href="{{ route($orderRoutePrefix.'.index') }}" class="active">
                    <i data-lucide="clipboard-list"></i>
                    <span>Orders</span>
                </a>
            @else

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

            <a href="{{ route('admin.orders.index') }}" class="active">
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

            <a href="{{ route('admin.reports') }}">
    <i data-lucide="bar-chart-3"></i>
    <span>Reports</span>
</a>

           <a href="{{ route('admin.settings') }}">
    <i data-lucide="settings"></i>
    <span>Settings</span>
</a>
            @endif

        </nav>

    </aside>


    <!-- MAIN -->

    <main class="main">

        @include('admin.partials.topbar', ['orderSearch' => true, 'orderRoutePrefix' => $orderRoutePrefix])


        <section class="content">

            <div class="page-header">

                <div>
                    <h1>Orders</h1>
                    <p>View and monitor customer laundry orders and schedules.</p>
                </div>

                <div class="lavea-page-header-actions">
                    @if ($canManageOrders)
                        <a href="{{ route($orderRoutePrefix.'.create') }}" class="add-btn">
                            <i data-lucide="plus"></i>
                            Add Order
                        </a>
                        <a href="{{ route('staff.payments.create') }}" class="add-btn">
                            <i data-lucide="credit-card"></i>
                            Add Payment
                        </a>
                    @endif
                    @include('admin.partials.current-date')
                </div>

            </div>


            @if(session('success'))

                <div class="success">
                    {{ session('success') }}
                </div>

            @endif


            <div class="card">

                <div class="table-container">

                    <table>

                        <thead>

                            <tr>

                                <th>Customer</th>

                                <th>Service</th>

                                <th>Staff</th>

                                <th>Qty</th>

                                <th>Order Date</th>

                                <th>Pickup</th>

                                <th>Delivery</th>

                                <th>Total</th>

                                <th>Status</th>

                                <th>Payment</th>

                                <th>{{ $canManageOrders ? 'Actions' : 'View' }}</th>

                            </tr>

                        </thead>


                        <tbody>

                            @forelse($orders as $order)

                                <tr>

                                    <td>

                                        <span class="customer-name">
                                            {{ $order->customer->name ?? 'N/A' }}
                                        </span>

                                    </td>


                                    <td>

                                        <span class="service-name">
                                            {{ $order->service->service_name ?? 'N/A' }}
                                        </span>

                                    </td>


                                    <td>

                                        <span class="staff-name">
                                            {{ $order->staff->name ?? 'Unassigned' }}
                                        </span>

                                    </td>


                                    <td>
                                        {{ number_format($order->quantity, 2) }}
                                    </td>


                                    <td class="date">

                                        {{ $order->order_date
                                            ? $order->order_date->format('M d, Y')
                                            : 'â€”'
                                        }}

                                    </td>


                                    <td class="date">

                                        {{ $order->pickup_date
                                            ? $order->pickup_date->format('M d, Y')
                                            : 'â€”'
                                        }}

                                    </td>


                                    <td class="date">

                                        {{ $order->delivery_date
                                            ? $order->delivery_date->format('M d, Y')
                                            : 'â€”'
                                        }}

                                    </td>


                                    <td class="price">

                                        ₱{{ number_format($order->total, 2) }}

                                    </td>


                                    <td>

                                        <span class="status
                                            {{ strtolower($order->status) }}">

                                            {{ $order->status }}

                                        </span>

                                    </td>


                                    <td>

                                        <span class="payment
                                            {{ strtolower($order->payment_status) }}">

                                            {{ $order->payment_status }}

                                        </span>

                                    </td>


                                    <td>

                                        <div class="actions">

                                            <a
                                                href="{{ route($orderRoutePrefix.'.show', $order->id) }}"
                                                class="action view"
                                                title="View"
                                            >
                                                <i data-lucide="eye"></i>
                                            </a>

                                            @if ($canManageOrders)
                                                <a href="{{ route($orderRoutePrefix.'.edit', $order->id) }}" class="action edit" title="Edit">
                                                    <i data-lucide="pencil"></i>
                                                </a>
                                            @endif


                                        </div>

                                    </td>

                                </tr>

                            @empty

                                <tr>

                                    <td colspan="10">

                                        <div class="empty">

                                            <i data-lucide="clipboard-x"></i>

                                            <p>No orders found.</p>

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

