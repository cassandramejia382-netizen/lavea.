<!DOCTYPE html>
<html lang="en">

<head>
    @include('partials.theme-assets')
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">

    <title>LAVEA | Admin Dashboard</title>

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
            color: #172554;
        }

        .dashboard {
            display: flex;
            min-height: 100vh;
        }

        /* ================= SIDEBAR ================= */

        .sidebar {
            width: 255px;
            background: #111d38;
            color: white;
            padding: 25px 16px;
            display: flex;
            flex-direction: column;
            position: fixed;
            left: 0;
            top: 0;
            bottom: 0;
        }

        .logo-area {
            padding: 5px 15px 35px;
        }

        .logo {
            font-size: 27px;
            font-weight: 700;
            letter-spacing: 3px;
        }

        .logo-subtitle {
            font-size: 11px;
            color: #aebbd3;
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
            display: flex;
            align-items: center;
            gap: 14px;
            padding: 13px 15px;
            border-radius: 8px;
            font-size: 14px;
            transition: 0.2s;
        }

        .menu a:hover {
            background: #1d3159;
            color: white;
        }

        .menu a.active {
            background: #4169c8;
            color: white;
        }

        .menu svg {
            width: 19px;
            height: 19px;
        }

        .admin-profile {
            margin-top: auto;
            border-top: 1px solid #2a3b5d;
            padding: 20px 5px 5px;
            display: flex;
            align-items: center;
            gap: 12px;
        }

        .profile-icon {
            width: 40px;
            height: 40px;
            background: #e7edf9;
            color: #26395e;
            border-radius: 50%;
            display: flex;
            align-items: center;
            justify-content: center;
        }

        .profile-icon svg {
            width: 22px;
        }

        .profile-info strong {
            display: block;
            font-size: 13px;
        }

        .profile-info span {
            display: block;
            color: #9eabc2;
            font-size: 10px;
            margin-top: 3px;
        }

        /* ================= MAIN ================= */

        .main {
            margin-left: 255px;
            width: calc(100% - 255px);
            min-height: 100vh;
        }

        /* ================= TOPBAR ================= */

        .topbar {
            height: 75px;
            background: white;
            border-bottom: 1px solid #e8ecf3;
            display: flex;
            align-items: center;
            justify-content: space-between;
            padding: 0 28px;
        }

        .search-box {
            width: 430px;
            height: 40px;
            border: 1px solid #dfe5ef;
            border-radius: 8px;
            display: flex;
            align-items: center;
            padding: 0 13px;
            color: #8a96aa;
        }

        .search-box svg {
            width: 18px;
        }

        .search-box input {
            border: none;
            outline: none;
            margin-left: 10px;
            width: 100%;
            font-size: 13px;
        }

        .top-right {
            display: flex;
            align-items: center;
            gap: 25px;
        }

        .notification {
            color: #172554;
            position: relative;
        }

        .notification svg {
            width: 21px;
        }

        .notification-dot {
            position: absolute;
            width: 7px;
            height: 7px;
            background: #ef4444;
            border-radius: 50%;
            top: 0;
            right: 0;
        }

        .top-profile {
            display: flex;
            align-items: center;
            gap: 10px;
            border-left: 1px solid #e3e7ef;
            padding-left: 20px;
        }

        .top-profile .avatar {
            width: 39px;
            height: 39px;
            background: #e8eefb;
            border-radius: 50%;
            display: flex;
            align-items: center;
            justify-content: center;
        }

        .top-profile .avatar svg {
            width: 21px;
        }

        .top-profile strong {
            font-size: 13px;
            display: block;
        }

        .top-profile span {
            font-size: 10px;
            color: #8792a6;
        }

        /* ================= CONTENT ================= */

        .content {
            padding: 30px;
        }

        .welcome {
            display: flex;
            justify-content: space-between;
            align-items: flex-start;
            margin-bottom: 22px;
        }

        .welcome h1 {
            font-size: 26px;
            color: #10204a;
            margin-bottom: 7px;
        }

        .welcome p {
            color: #7a879d;
            font-size: 13px;
        }

        .date {
            text-align: right;
            color: #687691;
            font-size: 12px;
        }

        .date strong {
            display: block;
            color: #53627e;
            margin-bottom: 5px;
        }

        /* ================= STAT CARDS ================= */

        .stats {
            display: grid;
            grid-template-columns: repeat(4, 1fr);
            gap: 17px;
            margin-bottom: 22px;
        }

        .stat-card {
            background: white;
            border: 1px solid #e7ebf2;
            border-radius: 10px;
            padding: 19px;
            display: flex;
            gap: 14px;
            align-items: flex-start;
        }

        .stat-icon {
            width: 45px;
            height: 45px;
            border-radius: 50%;
            display: flex;
            align-items: center;
            justify-content: center;
            flex-shrink: 0;
        }

        .stat-icon svg {
            width: 21px;
        }

        .blue {
            background: #e7efff;
            color: #3973e6;
        }

        .green {
            background: #e4f8ef;
            color: #18a56c;
        }

        .purple {
            background: #f0e9ff;
            color: #7c4bd8;
        }

        .orange {
            background: #fff2df;
            color: #ef941e;
        }

        .stat-title {
            font-size: 12px;
            color: #52617d;
            margin-bottom: 8px;
        }

        .stat-number {
            font-size: 23px;
            font-weight: 700;
            color: #12214a;
        }

        .stat-change {
            font-size: 10px;
            color: #13a16a;
            margin-top: 7px;
        }

        .stat-sub {
            font-size: 10px;
            color: #8b96a9;
            margin-top: 3px;
        }

        /* ================= GRID ================= */

        .dashboard-grid {
            display: grid;
            grid-template-columns: 2fr 1fr;
            gap: 17px;
            margin-bottom: 22px;
        }

        .card {
            background: white;
            border: 1px solid #e7ebf2;
            border-radius: 10px;
            padding: 20px;
        }

        .card-header {
            display: flex;
            justify-content: space-between;
            align-items: center;
            margin-bottom: 20px;
        }

        .card-header h2 {
            font-size: 16px;
            color: #12214a;
        }

        .filter {
            border: 1px solid #dfe5ef;
            background: white;
            border-radius: 7px;
            padding: 8px 11px;
            font-size: 11px;
            color: #52617d;
        }

        /* ================= CHART ================= */

        .chart {
            height: 245px;
            display: flex;
            align-items: flex-end;
            gap: 18px;
            padding: 15px 5px 0;
            border-bottom: 1px solid #e6eaf1;
            position: relative;
        }

        .chart::before,
        .chart::after {
            content: "";
            position: absolute;
            left: 0;
            right: 0;
            border-top: 1px dashed #edf0f5;
        }

        .chart::before {
            top: 55px;
        }

        .chart::after {
            top: 130px;
        }

        .bar-wrap {
            flex: 1;
            height: 100%;
            display: flex;
            flex-direction: column;
            justify-content: flex-end;
            align-items: center;
            position: relative;
            z-index: 1;
        }

        .bar {
            width: 65%;
            background: #5b8def;
            border-radius: 5px 5px 0 0;
            min-height: 15px;
        }

        .bar-label {
            margin-top: 8px;
            font-size: 9px;
            color: #8994a8;
        }

        /* ================= ORDER STATUS ================= */

        .status-container {
            display: flex;
            flex-direction: column;
            align-items: center;
        }

        .donut {
            width: 150px;
            height: 150px;
            border-radius: 50%;
            display: flex;
            align-items: center;
            justify-content: center;
            margin-bottom: 20px;
        }

        .donut-center {
            width: 103px;
            height: 103px;
            background: white;
            border-radius: 50%;
            display: flex;
            align-items: center;
            justify-content: center;
            flex-direction: column;
        }

        .donut-center strong {
            font-size: 23px;
        }

        .donut-center span {
            font-size: 10px;
            color: #8792a6;
            margin-top: 3px;
        }

        .status-list {
            width: 100%;
        }

        .status-row {
            display: flex;
            justify-content: space-between;
            align-items: center;
            font-size: 11px;
            padding: 6px 0;
        }

        .status-name {
            display: flex;
            align-items: center;
            gap: 8px;
        }

        .status-dot {
            width: 8px;
            height: 8px;
            border-radius: 50%;
        }

        .pending {
            background: #f4b740;
        }

        .progress {
            background: #4f86ee;
        }

        .completed {
            background: #2cae83;
        }

        .cancelled {
            background: #ed6475;
        }

        /* ================= BOTTOM GRID ================= */

        .bottom-grid {
            display: grid;
            grid-template-columns: 2fr 1fr;
            gap: 17px;
        }

        /* ================= TABLE ================= */

        .orders-table {
            width: 100%;
            border-collapse: collapse;
        }

        .orders-table th {
            background: #f8f9fc;
            color: #718099;
            font-size: 10px;
            font-weight: 500;
            padding: 11px 8px;
            text-align: left;
        }

        .orders-table td {
            padding: 12px 8px;
            border-bottom: 1px solid #edf0f5;
            font-size: 10px;
            color: #44526d;
        }

        .status-badge {
            display: inline-block;
            padding: 5px 8px;
            border-radius: 20px;
            font-size: 9px;
        }

        .badge-progress {
            background: #e5efff;
            color: #3973e6;
        }

        .badge-completed {
            background: #e4f8ef;
            color: #139363;
        }

        .badge-pending {
            background: #fff3da;
            color: #d48a10;
        }

        .view-all {
            color: #3973e6;
            text-decoration: none;
            font-size: 11px;
        }

        /* ================= ACTIVITY ================= */

        .activity {
            display: flex;
            flex-direction: column;
            gap: 17px;
        }

        .activity-item {
            display: flex;
            gap: 11px;
            align-items: flex-start;
        }

        .activity-icon {
            width: 32px;
            height: 32px;
            border-radius: 50%;
            background: #edf3ff;
            color: #4b7fe8;
            display: flex;
            align-items: center;
            justify-content: center;
            flex-shrink: 0;
        }

        .activity-icon svg {
            width: 15px;
        }

        .activity-text {
            font-size: 10px;
            color: #44526d;
            line-height: 1.5;
        }

        .activity-time {
            color: #9aa4b5;
            font-size: 9px;
            margin-top: 2px;
        }

        /* ================= RESPONSIVE ================= */

        @media (max-width: 1100px) {
            .stats {
                grid-template-columns: repeat(2, 1fr);
            }

            .dashboard-grid,
            .bottom-grid {
                grid-template-columns: 1fr;
            }
        }

        @media (max-width: 800px) {
            .sidebar {
                width: 70px;
                padding: 20px 10px;
            }

            .logo {
                font-size: 18px;
                text-align: center;
            }

            .logo-subtitle,
            .menu span,
            .profile-info {
                display: none;
            }

            .menu a {
                justify-content: center;
            }

            .admin-profile {
                justify-content: center;
            }

            .main {
                margin-left: 70px;
                width: calc(100% - 70px);
            }

            .search-box {
                width: 250px;
            }
        }

        @media (max-width: 600px) {
            .stats {
                grid-template-columns: 1fr;
            }

            .topbar {
                padding: 0 15px;
            }

            .search-box {
                width: 180px;
            }

            .content {
                padding: 30px;
            }

            .welcome {
                flex-direction: column;
                gap: 10px;
            }

            .date {
                text-align: left;
            }

            .orders-table {
                min-width: 650px;
            }

            .card {
                overflow-x: auto;
            }
        }
    </style>
    <link rel="stylesheet" href="{{ asset('css/admin-topbar.css') }}">
</head>

<body>

<div class="dashboard">

    <!-- SIDEBAR -->
    <aside class="sidebar">

        <div class="logo-area">
            <div class="logo">LAVEA</div>
            <div class="logo-subtitle">Laundry Made Easy</div>
        </div>

        <nav class="menu">

            <a href="{{ route('admin.dashboard') }}" class="active">
                <i data-lucide="layout-dashboard"></i>
                <span>Dashboard</span>
            </a>

            <a href="{{ route('admin.customers.index') }}">
                <i data-lucide="users"></i>
                <span>Customers</span>
            </a>

            <a href="{{ route('admin.staff.index') }}">
                <i data-lucide="user-round-plus"></i>
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

        <!-- TOPBAR -->
        @include('admin.partials.topbar')


        <!-- CONTENT -->
        <section class="content">

            <!-- WELCOME -->
            <div class="welcome">

                <div>
                    <h1>Good evening, Admin!</h1>
                    <p>Here's what's happening with your laundry business today.</p>
                </div>

                <div class="date">
                    <strong>{{ now()->format('F d, Y') }}</strong>
                    {{ now()->format('l') }}
                </div>

            </div>


            <!-- STAT CARDS -->
            <div class="stats">

                <div class="stat-card">

                    <div class="stat-icon blue">
                        <i data-lucide="shopping-cart"></i>
                    </div>

                    <div>
                        <div class="stat-title">Total Orders</div>
                        <div class="stat-number">{{ $stats['orders'] }}</div>
                        <div class="stat-change">&uarr; 12%</div>
                        <div class="stat-sub">vs. last week</div>
                    </div>

                </div>


                <div class="stat-card">

                    <div class="stat-icon green">
                        <i data-lucide="users"></i>
                    </div>

                    <div>
                        <div class="stat-title">Customers</div>
                        <div class="stat-number">{{ $stats['customers'] }}</div>
                        <div class="stat-change">&uarr; 8%</div>
                        <div class="stat-sub">vs. last week</div>
                    </div>

                </div>


                <div class="stat-card">

                    <div class="stat-icon purple">
                        <i data-lucide="user-round"></i>
                    </div>

                    <div>
                        <div class="stat-title">Staff</div>
                        <div class="stat-number">{{ $stats['staff'] }}</div>
                        <div class="stat-change">&uarr; 2%</div>
                        <div class="stat-sub">vs. last week</div>
                    </div>

                </div>


                <div class="stat-card">

                    <div class="stat-icon orange">
                        <i data-lucide="philippine-peso"></i>
                    </div>

                    <div>
                        <div class="stat-title">Total Income</div>
                        <div class="stat-number">
                            &#8369;{{ number_format($stats['income'], 2) }}
                        </div>
                        <div class="stat-change">&uarr; 15%</div>
                        <div class="stat-sub">vs. last week</div>
                    </div>

                </div>

            </div>


            <!-- SALES + STATUS -->
            <div class="dashboard-grid">

                <div class="card">

                    <div class="card-header">
                        <h2>Sales Overview</h2>

                        <form method="GET" action="{{ route('admin.dashboard') }}">
                        <select class="filter" name="period" onchange="this.form.submit()">
                            <option value="7days" {{ $period === '7days' ? 'selected' : '' }}>Last 7 Days</option>
                            <option value="30days" {{ $period === '30days' ? 'selected' : '' }}>Last 30 Days</option>
                            <option value="year" {{ $period === 'year' ? 'selected' : '' }}>This Year</option>
                        </select>
                        </form>
                    </div>

                    <div class="chart">
                        @foreach ($salesBars as $bar)
                            <div class="bar-wrap">
                                <div class="bar" style="height: {{ $bar['height'] }}%;" title="₱{{ number_format($bar['amount'], 2) }}"></div>
                                <div class="bar-label">{{ $bar['label'] }}</div>
                            </div>
                        @endforeach
                    </div>

                </div>


                <!-- ORDER STATUS -->
                <div class="card">

                    <div class="card-header">
                        <h2>Order Status</h2>
                    </div>

                    <div class="status-container">

                        <div class="donut" style="@if ($stats['orders'] > 0) background: conic-gradient(#f4b740 0deg {{ $statusDegrees['pending'] }}deg, #4f86ee {{ $statusDegrees['pending'] }}deg {{ $statusDegrees['progress'] }}deg, #2cae83 {{ $statusDegrees['progress'] }}deg {{ $statusDegrees['completed'] }}deg, #ed6475 {{ $statusDegrees['completed'] }}deg 360deg); @else background: #edf0f5; @endif">

                            <div class="donut-center">
                                <strong>{{ $stats['orders'] }}</strong>
                                <span>Total Orders</span>
                            </div>

                        </div>

                        <div class="status-list">

                            <div class="status-row">
                                <div class="status-name">
                                    <span class="status-dot pending"></span>
                                    Pending
                                </div>
                                <strong>{{ $stats['pending'] }}</strong>
                            </div>

                            <div class="status-row">
                                <div class="status-name">
                                    <span class="status-dot progress"></span>
                                    In Progress
                                </div>
                                <strong>{{ $stats['in_progress'] }}</strong>
                            </div>

                            <div class="status-row">
                                <div class="status-name">
                                    <span class="status-dot completed"></span>
                                    Completed
                                </div>
                                <strong>{{ $stats['completed'] }}</strong>
                            </div>

                            <div class="status-row">
                                <div class="status-name">
                                    <span class="status-dot cancelled"></span>
                                    Cancelled
                                </div>
                                <strong>{{ $stats['cancelled'] }}</strong>
                            </div>

                        </div>

                    </div>

                </div>

            </div>


            <!-- RECENT ORDERS + ACTIVITY -->
            <div class="bottom-grid">

                <!-- RECENT ORDERS -->
                <div class="card">

                    <div class="card-header">

                        <h2>Recent Orders</h2>

                        <a href="{{ route('admin.orders.index') }}" class="view-all">
                            View All
                        </a>

                    </div>

                    <table class="orders-table">

                        <thead>
                            <tr>
                                <th>Order No.</th>
                                <th>Customer</th>
                                <th>Service</th>
                                <th>Status</th>
                                <th>Total</th>
                                <th>Date</th>
                            </tr>
                        </thead>

                        <tbody>

                            @foreach($recentOrders as $order)

                                <tr>

                                    <td>#LVE-{{ str_pad($order->id, 4, '0', STR_PAD_LEFT) }}</td>

                                    <td>{{ $order->customer->name ?? 'N/A' }}</td>

                                    <td>{{ $order->service->service_name ?? 'N/A' }}</td>

                                    <td>

                                        @if($order->status === 'Completed')

                                            <span class="status-badge badge-completed">
                                                Completed
                                            </span>

                                        @elseif($order->status === 'Pending')

                                            <span class="status-badge badge-pending">
                                                Pending
                                            </span>

                                        @elseif(in_array($order->status, ['Processing', 'Ready', 'In Progress']))

                                            <span class="status-badge badge-progress">
                                                In Progress
                                            </span>

                                        @else
                                            <span class="status-badge badge-progress">
                                                {{ $order->status }}
                                            </span>

                                        @endif

                                    </td>

                                    <td>
                                        ₱{{ number_format($order->total, 2) }}
                                    </td>

                                    <td>
                                        {{ $order->order_date ? \Illuminate\Support\Carbon::parse($order->order_date)->format('M d, Y') : 'N/A' }}
                                    </td>

                                </tr>

                            @endforeach

                        </tbody>

                    </table>

                </div>


                <!-- RECENT ACTIVITY -->
                <div class="card">

                    <div class="card-header">
                        <h2>Recent Activity</h2>
                    </div>

                    <div class="activity">
                        @forelse ($recentActivities as $activity)
                            <div class="activity-item">
                                <div class="activity-icon">
                                    <i data-lucide="{{ $activity['icon'] }}"></i>
                                </div>
                                <div class="activity-text">
                                    <strong>{{ $activity['title'] }}</strong>
                                    <div class="activity-time">
                                        {{ $activity['details'] }} · {{ $activity['date']->format('M d, h:i A') }}
                                    </div>
                                </div>
                            </div>
                        @empty
                            <div class="activity-item">
                                <div class="activity-text"><strong>No recent activity</strong></div>
                            </div>
                        @endforelse
                    </div>

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

