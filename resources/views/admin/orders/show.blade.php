<!DOCTYPE html>
<html lang="en">
<head>
    @include('partials.theme-assets')
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Order Details - LAVEA</title>

    <script src="https://unpkg.com/lucide@latest"></script>
    <link rel="stylesheet" href="{{ asset('css/admin-topbar.css') }}">

    <style>
        * {
            box-sizing: border-box;
            margin: 0;
            padding: 0;
        }

        body {
            font-family: Arial, sans-serif;
            background: #f5f7fb;
            color: #1d2942;
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

        .main {
            margin-left: 255px;
            padding: 35px 30px;
        }

        .top {
            display: flex;
            justify-content: space-between;
            align-items: center;
            margin-bottom: 30px;
        }

        .top h2 {
            font-size: 25px;
            color: #17233d;
        }

        .top p {
            color: #7c879b;
            font-size: 14px;
            margin-top: 6px;
        }

        .back-btn {
            text-decoration: none;
            background: white;
            color: #34415c;
            border: 1px solid #dfe4ed;
            padding: 11px 18px;
            border-radius: 8px;
            font-size: 14px;
        }

        .back-btn:hover {
            background: #f0f3f8;
        }

        .card {
            background: white;
            border-radius: 12px;
            padding: 28px;
            border: 1px solid #e5e9f0;
            max-width: 1000px;
        }

        .card-title {
            font-size: 18px;
            font-weight: 600;
            color: #17233d;
            margin-bottom: 22px;
        }

        .grid {
            display: grid;
            grid-template-columns: repeat(2, 1fr);
            gap: 20px 35px;
        }

        .item {
            border-bottom: 1px solid #edf0f5;
            padding-bottom: 14px;
        }

        .label {
            color: #7b879c;
            font-size: 12px;
            margin-bottom: 6px;
        }

        .value {
            color: #1c2942;
            font-size: 15px;
            font-weight: 500;
        }

        .total-box {
            margin-top: 25px;
            background: #f4f7ff;
            border: 1px solid #dce5fb;
            border-radius: 10px;
            padding: 20px;
            display: flex;
            justify-content: space-between;
            align-items: center;
        }

        .total-label {
            color: #66738b;
            font-size: 14px;
        }

        .total {
            font-size: 24px;
            font-weight: 700;
            color: #4169c8;
        }

        .status,
        .payment {
            display: inline-block;
            padding: 6px 11px;
            border-radius: 20px;
            font-size: 12px;
            font-weight: 600;
        }

        .pending {
            background: #fff4d8;
            color: #9a6a00;
        }

        .processing {
            background: #e7f0ff;
            color: #315da8;
        }

        .ready {
            background: #e8f8f0;
            color: #20845a;
        }

        .completed {
            background: #e4f7ed;
            color: #18794e;
        }

        .cancelled {
            background: #fde9e9;
            color: #b33a3a;
        }

        .paid {
            background: #e4f7ed;
            color: #18794e;
        }

        .partial {
            background: #fff4d8;
            color: #9a6a00;
        }

        .unpaid {
            background: #fde9e9;
            color: #b33a3a;
        }

        .notes {
            margin-top: 25px;
        }

        .notes-content {
            margin-top: 8px;
            background: #f8f9fc;
            border-radius: 8px;
            padding: 15px;
            color: #526078;
            font-size: 14px;
            line-height: 1.6;
            min-height: 60px;
        }

        .actions {
            margin-top: 25px;
            display: flex;
            gap: 10px;
        }

        .edit-btn {
            text-decoration: none;
            background: #4169c8;
            color: white;
            padding: 11px 20px;
            border-radius: 8px;
            font-size: 14px;
        }

        .edit-btn:hover {
            background: #3458ad;
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
                padding: 30px;
            }

            .grid {
                grid-template-columns: 1fr;
            }
        }
    </style>
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

        <a href="#">
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

    <div class="top">
        <div>
            <h2>Order Details</h2>
            <p>View complete information about this order.</p>
        </div>

        <a href="{{ route($orderRoutePrefix.'.index') }}" class="back-btn">
            Back to Orders
        </a>
    </div>

    <div class="card">

        <div class="card-title">
            Order #{{ $order->id }}
        </div>

        <div class="grid">

            <div class="item">
                <div class="label">Customer</div>
                <div class="value">
                    {{ $order->customer->name ?? 'N/A' }}
                </div>
            </div>

            <div class="item">
                <div class="label">Service</div>
                <div class="value">
                    {{ $order->service->service_name ?? 'N/A' }}
                </div>
            </div>

            <div class="item">
                <div class="label">Assigned Staff</div>
                <div class="value">
                    {{ $order->staff->name ?? 'Not Assigned' }}
                </div>
            </div>

            <div class="item">
                <div class="label">Quantity</div>
                <div class="value">
                    {{ number_format($order->quantity, 2) }}
                </div>
            </div>

            <div class="item">
                <div class="label">Order Date</div>
                <div class="value">
                    {{ $order->order_date ? $order->order_date->format('F d, Y') : 'N/A' }}
                </div>
            </div>

            <div class="item">
                <div class="label">Pickup Date</div>
                <div class="value">
                    {{ $order->pickup_date ? $order->pickup_date->format('F d, Y') : 'N/A' }}
                </div>
            </div>

            <div class="item">
                <div class="label">Delivery Date</div>
                <div class="value">
                    {{ $order->delivery_date ? $order->delivery_date->format('F d, Y') : 'N/A' }}
                </div>
            </div>

            <div class="item">
                <div class="label">Status</div>
                <div class="value">
                    @php
                        $statusClass = strtolower($order->status);
                    @endphp

                    <span class="status {{ $statusClass }}">
                        {{ $order->status }}
                    </span>
                </div>
            </div>

            <div class="item">
                <div class="label">Payment Status</div>
                <div class="value">
                    @php
                        $paymentClass = strtolower($order->payment_status);
                    @endphp

                    <span class="payment {{ $paymentClass }}">
                        {{ $order->payment_status }}
                    </span>
                </div>
            </div>

        </div>

        <div class="total-box">
            <div class="total-label">
                Total Amount
            </div>

            <div class="total">
                ₱{{ number_format($order->total, 2) }}
            </div>
        </div>

        <div class="notes">

            <div class="label">Notes</div>

            <div class="notes-content">
                {{ $order->notes ?: 'No notes added.' }}
            </div>

        </div>

    </div>

</main>

<script>
    lucide.createIcons();
</script>

</body>
</html>
