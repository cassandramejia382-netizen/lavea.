<!DOCTYPE html>
<html lang="en">
<head>
    @include('partials.theme-assets')
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">

    <title>Schedule | LAVEA</title>

    <script src="https://unpkg.com/lucide@latest"></script>

    <style>
        * {
            margin: 0;
            padding: 0;
            box-sizing: border-box;
            font-family: Arial, sans-serif;
        }

        body {
            background: #f5f7fb;
            color: #1f2937;
        }

        .layout {
            display: flex;
            min-height: 100vh;
        }

        /* SIDEBAR */
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
            border-bottom: 1px solid #e5e7eb;
            display: flex;
            align-items: center;
            justify-content: space-between;
            padding: 0 35px;
        }

        .search {
            width: 400px;
            position: relative;
        }

        .search i {
            position: absolute;
            left: 13px;
            top: 11px;
            width: 18px;
            color: #8a94a6;
        }

        .search input {
            width: 100%;
            height: 40px;
            border: 1px solid #e1e5eb;
            border-radius: 8px;
            padding: 0 15px 0 42px;
            outline: none;
            font-size: 14px;
        }

        .search input:focus {
            border-color: #4169c8;
        }

        .admin {
            display: flex;
            align-items: center;
            gap: 11px;
        }

        .avatar {
            width: 40px;
            height: 40px;
            background: #e9eefb;
            border-radius: 50%;
            display: flex;
            align-items: center;
            justify-content: center;
            color: #4169c8;
        }

        .admin strong {
            display: block;
            font-size: 14px;
        }

        .admin span {
            display: block;
            font-size: 11px;
            color: #8a94a6;
            margin-top: 2px;
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

        .page-header h2 {
            font-size: 24px;
            color: #17233d;
        }

        .page-header p {
            color: #8a94a6;
            font-size: 13px;
            margin-top: 5px;
        }

        .add-btn {
            background: #4169c8;
            color: white;
            text-decoration: none;
            border: none;
            padding: 11px 17px;
            border-radius: 7px;
            font-size: 13px;
            display: flex;
            align-items: center;
            gap: 8px;
        }

        .add-btn:hover {
            background: #355ab0;
        }

        .add-btn i {
            width: 17px;
        }

        /* ALERT */
        .alert {
            background: #eaf7ef;
            border: 1px solid #c9ead5;
            color: #267344;
            padding: 12px 15px;
            border-radius: 7px;
            margin-bottom: 20px;
            font-size: 13px;
        }

        /* CARD */
        .card {
            background: white;
            border: 1px solid #e5e7eb;
            border-radius: 10px;
            overflow: hidden;
        }

        .card-header {
            padding: 20px 22px;
            border-bottom: 1px solid #e5e7eb;
        }

        .card-header h3 {
            font-size: 16px;
            color: #17233d;
        }

        .card-header p {
            font-size: 12px;
            color: #8a94a6;
            margin-top: 4px;
        }

        /* TABLE */
        .table-wrapper {
            overflow-x: auto;
        }

        table {
            width: 100%;
            border-collapse: collapse;
        }

        th {
            background: #f8f9fc;
            color: #697386;
            font-size: 11px;
            text-transform: uppercase;
            letter-spacing: 0.4px;
            text-align: left;
            padding: 14px 18px;
            border-bottom: 1px solid #e5e7eb;
            white-space: nowrap;
        }

        td {
            padding: 15px 18px;
            border-bottom: 1px solid #eef0f4;
            font-size: 13px;
            color: #374151;
            white-space: nowrap;
        }

        tr:last-child td {
            border-bottom: none;
        }

        tr:hover td {
            background: #fafbfe;
        }

        .customer-name {
            font-weight: 600;
            color: #17233d;
        }

        .order-number {
            color: #4169c8;
            font-weight: 600;
        }

        .schedule-type {
            font-weight: 600;
        }

        /* STATUS */
        .status {
            display: inline-block;
            padding: 5px 9px;
            border-radius: 20px;
            font-size: 11px;
            font-weight: 600;
        }

        .status-scheduled {
            background: #fff5d9;
            color: #9a6b00;
        }

        .status-on-time {
            background: #e7f0ff;
            color: #315db8;
        }

        .status-completed {
            background: #e8f7ee;
            color: #267344;
        }

        .status-cancelled {
            background: #fdecec;
            color: #b33a3a;
        }

        /* ACTIONS */
        .actions {
            display: flex;
            align-items: center;
            gap: 6px;
        }

        .action-btn {
            width: 32px;
            height: 32px;
            border-radius: 6px;
            display: flex;
            align-items: center;
            justify-content: center;
            text-decoration: none;
            border: none;
            cursor: pointer;
        }

        .action-btn i {
            width: 15px;
            height: 15px;
        }

        .view-btn {
            background: #e9eefb;
            color: #4169c8;
        }

        .edit-btn {
            background: #fff4df;
            color: #a66b00;
        }

        .delete-btn {
            background: #fdeaea;
            color: #c23c3c;
        }

        .action-btn:hover {
            opacity: 0.8;
        }

        .empty {
            text-align: center;
            padding: 45px 20px;
            color: #8a94a6;
            font-size: 13px;
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
            }

            .search {
                width: 280px;
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

            <a href="{{ route('staff.orders.index') }}">
                <i data-lucide="clipboard-list"></i>
                <span>Orders</span>
            </a>

            <a href="{{ route('staff.schedules.index') }}" class="active">
                <i data-lucide="calendar-days"></i>
                <span>Schedule</span>
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

            <a href="{{ route('admin.payments.index') }}">
                <i data-lucide="credit-card"></i>
                <span>Payments / Transactions</span>
            </a>

            <a href="{{ route('admin.schedules.index') }}" class="active">
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

        <!-- TOPBAR -->
        @include('admin.partials.topbar')


        <!-- CONTENT -->
        <section class="content">

            <div class="page-header">

                <div>
                    <h2>Schedule</h2>
                    <p>{{ auth()->user()->role === 'staff' ? 'Manage pickup and delivery schedules.' : 'View pickup and delivery schedules.' }}</p>
                </div>

                <div class="lavea-page-header-actions">
                    @if (auth()->user()->role === 'staff')
                    <a href="{{ route($routePrefix.'.create') }}" class="add-btn">
                        <i data-lucide="plus"></i>
                        Add Schedule
                    </a>
                    @endif
                    @include('admin.partials.current-date')
                </div>

            </div>


            @if(session('success'))
                <div class="alert">
                    {{ session('success') }}
                </div>
            @endif


            <div class="card">

                <div class="card-header">
                    <h3>Schedule List</h3>
                    <p>Pickup and delivery schedule records.</p>
                </div>

                <div class="table-wrapper">

                    <table>

                        <thead>
                            <tr>
                                <th>ID</th>
                                <th>Order</th>
                                <th>Customer</th>
                                <th>Service</th>
                                <th>Type</th>
                                <th>Date</th>
                                <th>Time</th>
                                <th>Assigned Staff</th>
                                <th>Status</th>
                                <th>Actions</th>
                            </tr>
                        </thead>

                        <tbody>

                            @forelse($schedules as $schedule)

                                <tr>

                                    <td>
                                        {{ $schedule->id }}
                                    </td>

                                    <td>
                                        <span class="order-number">
                                            #LVE-{{ str_pad($schedule->order_id, 4, '0', STR_PAD_LEFT) }}
                                        </span>
                                    </td>

                                    <td>
                                        <span class="customer-name">
                                            {{ $schedule->order->customer->name ?? 'N/A' }}
                                        </span>
                                    </td>

                                    <td>
                                        {{ $schedule->order->service->service_name ?? 'N/A' }}
                                    </td>

                                    <td>
                                        <span class="schedule-type">
                                            {{ $schedule->type }}
                                        </span>
                                    </td>

                                    <td>
                                        {{ $schedule->schedule_date?->format('M d, Y') }}
                                    </td>

                                    <td>
                                        {{ $schedule->schedule_time }}
                                    </td>

                                    <td>
                                        {{ $schedule->order->staff->name ?? 'Not assigned' }}
                                    </td>

                                    <td>

                                        @if($schedule->status === 'Scheduled')

                                            <span class="status status-scheduled">
                                                Scheduled
                                            </span>

                                        @elseif($schedule->status === 'On Time')

                                            <span class="status status-on-time">
                                                On Time
                                            </span>

                                        @elseif($schedule->status === 'Completed')

                                            <span class="status status-completed">
                                                Completed
                                            </span>

                                        @else

                                            <span class="status status-cancelled">
                                                Cancelled
                                            </span>

                                        @endif

                                    </td>

                                    <td>

                                        <div class="actions">

                                            <a
                                                href="{{ route($routePrefix.'.show', $schedule->id) }}"
                                                class="action-btn view-btn"
                                                title="View"
                                            >
                                                <i data-lucide="eye"></i>
                                            </a>

                                            @if (auth()->user()->role === 'staff')
                                                <a
                                                    href="{{ route($routePrefix.'.edit', $schedule->id) }}"
                                                    class="action-btn edit-btn"
                                                    title="Edit"
                                                >
                                                    <i data-lucide="pencil"></i>
                                                </a>
                                            @endif

                                        </div>

                                    </td>

                                </tr>

                            @empty

                                <tr>
                                    <td colspan="10" class="empty">
                                        No schedules found.
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

