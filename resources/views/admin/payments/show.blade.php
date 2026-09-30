﻿<!DOCTYPE html>
<html lang="en">

<head>
    @include('partials.theme-assets')
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">

    <title>LAVEA | Payment Details</title>

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
        }

        .menu a:hover,
        .menu a.active {
            background: #4169c8;
            color: white;
        }

        .menu svg {
            width: 19px;
            height: 19px;
        }

        .main {
            margin-left: 255px;
            width: calc(100% - 255px);
        }

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
        }

        .admin strong {
            display: block;
            font-size: 13px;
        }

        .admin span {
            font-size: 10px;
            color: #8995aa;
        }

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

        .back-btn {
            background: white;
            color: #526078;
            border: 1px solid #dfe5ef;
            padding: 11px 18px;
            border-radius: 8px;
            text-decoration: none;
            font-size: 13px;
        }

        .back-btn:hover {
            background: #f1f4f9;
        }

        .card {
            background: white;
            border: 1px solid #e7ebf2;
            border-radius: 10px;
            padding: 28px;
            max-width: 950px;
        }

        .payment-header {
            display: flex;
            justify-content: space-between;
            align-items: center;
            padding-bottom: 22px;
            border-bottom: 1px solid #edf0f5;
            margin-bottom: 25px;
        }

        .payment-id {
            font-size: 18px;
            font-weight: 700;
            color: #172554;
        }

        .payment-id span {
            color: #7a879d;
            font-size: 12px;
            font-weight: 400;
        }

        .status {
            display: inline-block;
            padding: 7px 12px;
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

        .grid {
            display: grid;
            grid-template-columns: 1fr 1fr;
            gap: 22px 35px;
        }

        .item {
            border-bottom: 1px solid #edf0f5;
            padding-bottom: 15px;
        }

        .label {
            color: #7a879d;
            font-size: 11px;
            margin-bottom: 7px;
        }

        .value {
            color: #263550;
            font-size: 14px;
            font-weight: 500;
        }

        .amount-box {
            margin-top: 25px;
            background: #f4f7ff;
            border: 1px solid #dce5fb;
            border-radius: 9px;
            padding: 20px;
            display: flex;
            justify-content: space-between;
            align-items: center;
        }

        .amount-label {
            color: #66738b;
            font-size: 13px;
        }

        .amount {
            color: #4169c8;
            font-size: 25px;
            font-weight: 700;
        }

        .notes {
            margin-top: 25px;
        }

        .notes-content {
            background: #f8f9fc;
            border-radius: 8px;
            padding: 15px;
            color: #526078;
            font-size: 13px;
            line-height: 1.6;
            min-height: 55px;
        }

        .actions {
            display: flex;
            gap: 10px;
            margin-top: 25px;
        }

        .edit-btn {
            background: #4169c8;
            color: white;
            padding: 11px 19px;
            border-radius: 8px;
            text-decoration: none;
            font-size: 13px;
        }

        .edit-btn:hover {
            background: #3459b1;
        }

        .receipt {
            display: none;
        }

        @media print {
            body * {
                visibility: hidden !important;
            }

            #payment-receipt,
            #payment-receipt * {
                visibility: visible !important;
            }

            #payment-receipt {
                display: block !important;
                position: absolute;
                top: 0;
                left: 0;
                width: 100%;
                padding: 24px;
                background: white !important;
                color: #111 !important;
                font-family: Arial, Helvetica, sans-serif;
            }

            #payment-receipt h1 {
                margin-bottom: 20px;
            }

            #payment-receipt p {
                padding: 10px 0;
                border-bottom: 1px solid #ddd;
            }
        }

        @media(max-width: 800px) {

            .sidebar {
                width: 70px;
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

            .grid {
                grid-template-columns: 1fr;
            }
        }
    </style>
    <link rel="stylesheet" href="{{ asset('css/admin-topbar.css') }}">
</head>

<body>

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

        <a href="{{ route('staff.orders.index') }}" class="active">
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

        <a href="{{ route('admin.orders.index') }}">
            <i data-lucide="clipboard-list"></i>
            <span>Orders</span>
        </a>

        <a href="{{ route('admin.payments.index') }}" class="active">
            <i data-lucide="credit-card"></i>
            <span>Payments / Transactions</span>
        </a>

        <a href="#">
            <i data-lucide="calendar-days"></i>
            <span>Schedule</span>
        </a>

        <a href="#">
            <i data-lucide="bar-chart-3"></i>
            <span>Reports</span>
        </a>

        <a href="#">
            <i data-lucide="settings"></i>
            <span>Settings</span>
        </a>
        @endif

    </nav>

</aside>


<main class="main">

    @include('admin.partials.topbar')


    <section class="content">

        <div class="page-header">

            <div>

                <h1>Payment Details</h1>

                <p>
                    View complete information about this transaction.
                </p>

            </div>

            <a
                href="{{ auth()->user()->role === 'staff' ? route('staff.orders.index') : route('admin.payments.index') }}"
                class="back-btn"
            >
                {{ auth()->user()->role === 'staff' ? 'Back to Orders' : 'Back to Payments' }}
            </a>

        </div>


        <div class="card">

            <div class="payment-header">

                <div class="payment-id">

                    Payment #{{ $payment->id }}

                    <span>
                        â€” Order #{{ $payment->order->id ?? 'N/A' }}
                    </span>

                </div>


                <span class="status {{ strtolower($payment->status) }}">

                    {{ $payment->status }}

                </span>

            </div>


            <div class="grid">

                <div class="item">

                    <div class="label">
                        Customer
                    </div>

                    <div class="value">
                        {{ $payment->order->customer->name ?? 'N/A' }}
                    </div>

                </div>


                <div class="item">

                    <div class="label">
                        Service
                    </div>

                    <div class="value">
                        {{ $payment->order->service->service_name ?? 'N/A' }}
                    </div>

                </div>


                <div class="item">

                    <div class="label">
                        Order Total
                    </div>

                    <div class="value">
                        ₱{{ number_format($payment->order->total ?? 0, 2) }}
                    </div>

                </div>


                <div class="item">

                    <div class="label">
                        Payment Method
                    </div>

                    <div class="value">
                        {{ $payment->payment_method }}
                    </div>

                </div>


                <div class="item">

                    <div class="label">
                        Reference Number
                    </div>

                    <div class="value">
                        {{ $payment->reference_number ?: 'â€”' }}
                    </div>

                </div>


                <div class="item">

                    <div class="label">
                        Payment Date
                    </div>

                    <div class="value">

                        {{ $payment->payment_date
                            ? $payment->payment_date->format('F d, Y')
                            : 'â€”'
                        }}

                    </div>

                </div>

            </div>


            <div class="amount-box">

                <div class="amount-label">
                    Payment Amount
                </div>

                <div class="amount">
                    ₱{{ number_format($payment->amount, 2) }}
                </div>

            </div>


            <div class="notes">

                <div class="label">
                    Notes
                </div>

                <div class="notes-content">
                    {{ $payment->notes ?: 'No notes added.' }}
                </div>

            </div>

            @if ($payment->status === 'Completed')
                <div class="actions">
                    <button type="button" class="edit-btn" onclick="window.print()">Print Receipt</button>
                </div>

                <div id="payment-receipt" class="receipt">
                    <h1>LAVEA Payment Receipt</h1>
                    <p><strong>Order ID:</strong> #{{ $payment->order->id ?? 'N/A' }}</p>
                    <p><strong>Customer:</strong> {{ $payment->order->customer->name ?? 'N/A' }}</p>
                    <p><strong>Service:</strong> {{ $payment->order->service->service_name ?? 'N/A' }}</p>
                    <p><strong>Amount:</strong> ₱{{ number_format($payment->amount, 2) }}</p>
                    <p><strong>Payment Method:</strong> {{ $payment->payment_method }}</p>
                    <p><strong>Payment Date:</strong> {{ $payment->payment_date ? $payment->payment_date->format('F d, Y') : '—' }}</p>
                    <p><strong>Payment Status:</strong> {{ $payment->status }}</p>
                </div>
            @endif


        </div>

    </section>

</main>


<script>
    lucide.createIcons();
</script>

</body>
</html>

