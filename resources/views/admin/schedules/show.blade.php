<!DOCTYPE html>
<html lang="en">
<head>
    @include('partials.theme-assets')
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>View Schedule - LAVEA</title>

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
            position: fixed;
            left: 0;
            top: 0;
            width: 255px;
            height: 100vh;
            background: #111d38;
            color: white;
            padding: 25px 15px;
        }

        .logo {
            padding: 0 15px 30px;
        }

        .logo h1 {
            font-size: 24px;
            font-weight: 700;
            letter-spacing: 1px;
        }

        .logo p {
            font-size: 12px;
            color: #aeb9cf;
            margin-top: 4px;
        }

        .menu {
            margin-top: 10px;
        }

        .menu-title {
            font-size: 11px;
            color: #7f8ca8;
            padding: 0 15px 10px;
            text-transform: uppercase;
            letter-spacing: 1px;
        }

        .menu a {
            display: flex;
            align-items: center;
            gap: 13px;
            text-decoration: none;
            color: #b9c3d7;
            padding: 12px 15px;
            margin-bottom: 5px;
            border-radius: 7px;
            font-size: 14px;
            transition: 0.2s;
        }

        .menu a:hover {
            background: #1d2d50;
            color: white;
        }

        .menu a.active {
            background: #4169c8;
            color: white;
        }

        .menu svg {
            width: 18px;
            height: 18px;
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
            padding: 30px 30px 40px;
        }

        .page-header {
            display: flex;
            align-items: center;
            justify-content: space-between;
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

        .header-actions {
            display: flex;
            gap: 10px;
        }

        .btn {
            display: inline-flex;
            align-items: center;
            gap: 8px;
            padding: 10px 16px;
            border-radius: 7px;
            text-decoration: none;
            font-size: 14px;
            border: none;
            cursor: pointer;
        }

        .btn svg {
            width: 17px;
            height: 17px;
        }

        .btn-back {
            background: white;
            color: #374151;
            border: 1px solid #d1d5db;
        }

        .btn-back:hover {
            background: #f9fafb;
        }

        .btn-edit {
            background: #4169c8;
            color: white;
        }

        .btn-edit:hover {
            background: #3558ad;
        }

        /* CARD */
        .card {
            background: white;
            border: 1px solid #e5e7eb;
            border-radius: 10px;
            overflow: hidden;
        }

        .card-header {
            padding: 20px 25px;
            border-bottom: 1px solid #e5e7eb;
        }

        .card-header h3 {
            font-size: 17px;
            color: #111827;
        }

        .card-header p {
            font-size: 13px;
            color: #6b7280;
            margin-top: 4px;
        }

        .details {
            padding: 25px;
            display: grid;
            grid-template-columns: repeat(2, 1fr);
            gap: 22px 35px;
        }

        .detail-item {
            border-bottom: 1px solid #eef0f4;
            padding-bottom: 15px;
        }

        .detail-label {
            font-size: 12px;
            color: #6b7280;
            margin-bottom: 7px;
            text-transform: uppercase;
            letter-spacing: 0.5px;
        }

        .detail-value {
            font-size: 15px;
            color: #111827;
            font-weight: 500;
        }

        .status {
            display: inline-block;
            padding: 5px 11px;
            border-radius: 20px;
            font-size: 12px;
            font-weight: 600;
        }

        .status-scheduled {
            background: #e8eefc;
            color: #4169c8;
        }

        .status-on-time {
            background: #e7f6ed;
            color: #198754;
        }

        .status-completed {
            background: #dcfce7;
            color: #15803d;
        }

        .status-cancelled {
            background: #fee2e2;
            color: #dc2626;
        }

        .notes {
            grid-column: 1 / -1;
        }

        .notes .detail-value {
            line-height: 1.6;
            color: #4b5563;
            font-weight: normal;
        }

        /* RESPONSIVE */
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

            .topbar {
                left: 70px;
            }

            .main {
                margin-left: 70px;
            }

            .search {
                width: 200px;
            }
        }

        @media (max-width: 700px) {
            .main {
                padding: 30px 30px 30px;
            }

            .admin-text {
                display: none;
            }

            .details {
                grid-template-columns: 1fr;
            }

            .notes {
                grid-column: auto;
            }

            .page-header {
                align-items: flex-start;
                gap: 15px;
            }
        }
    </style>
    <link rel="stylesheet" href="{{ asset('css/admin-topbar.css') }}">
</head>

<body>

    <!-- SIDEBAR -->
    <aside class="sidebar">

        <div class="logo">
            <h1>LAVEA</h1>
            <p>Laundry Made Easy</p>
        </div>

        <div class="menu">

            @if (auth()->user()->role === 'staff')
            <a href="{{ route('staff.dashboard') }}">
                <i data-lucide="layout-dashboard"></i>
                <span>Dashboard</span>
            </a>
            <a href="{{ route('staff.orders.index') }}">
                <i data-lucide="shopping-bag"></i>
                <span>Orders</span>
            </a>
            <a href="{{ route('staff.schedules.index') }}" class="active">
                <i data-lucide="calendar-days"></i>
                <span>Schedule</span>
            </a>
            @else

            <div class="menu-title">Main Menu</div>

            <a href="{{ route('admin.dashboard') }}">
                <i data-lucide="layout-dashboard"></i>
                <span>Dashboard</span>
            </a>

            <a href="{{ route('admin.customers.index') }}">
                <i data-lucide="users"></i>
                <span>Customers</span>
            </a>

            <a href="{{ route('admin.staff.index') }}">
                <i data-lucide="user-round"></i>
                <span>Staff</span>
            </a>

            <a href="{{ route('admin.services.index') }}">
                <i data-lucide="shirt"></i>
                <span>Services</span>
            </a>

            <a href="{{ route('admin.orders.index') }}">
                <i data-lucide="shopping-bag"></i>
                <span>Orders</span>
            </a>

            <a href="{{ route('admin.payments.index') }}">
                <i data-lucide="credit-card"></i>
                <span>Payments</span>
            </a>

            <a href="{{ route('admin.schedules.index') }}" class="active">
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

        </div>
    </aside>


    <!-- TOPBAR -->
    @include('admin.partials.topbar')


    <!-- MAIN CONTENT -->
    <main class="main">

        <div class="page-header">

            <div>
                <h2>Schedule Details</h2>
                <p>View the complete information for this schedule.</p>
            </div>

            <div class="header-actions">

                <a href="{{ route($routePrefix.'.index') }}" class="btn btn-back">
                    <i data-lucide="arrow-left"></i>
                    Back
                </a>

                @if (auth()->user()->role === 'staff')
                    <a href="{{ route($routePrefix.'.edit', $schedule->id) }}" class="btn btn-edit">
                        <i data-lucide="pencil"></i>
                        Edit Schedule
                    </a>
                @endif

            </div>

        </div>


        <!-- DETAILS CARD -->
        <div class="card">

            <div class="card-header">
                <h3>Schedule Information</h3>
                <p>Details about the selected pickup or delivery schedule.</p>
            </div>

            <div class="details">

                <!-- ID -->
                <div class="detail-item">

                    <div class="detail-label">
                        Schedule ID
                    </div>

                    <div class="detail-value">
                        #{{ $schedule->id }}
                    </div>

                </div>

                <div class="detail-item">
                    <div class="detail-label">Assigned Staff</div>
                    <div class="detail-value">{{ $schedule->order->staff->name ?? 'Not assigned' }}</div>
                </div>


                <!-- ORDER -->
                <div class="detail-item">

                    <div class="detail-label">
                        Order
                    </div>

                    <div class="detail-value">
                        #{{ $schedule->order_id }}
                    </div>

                </div>


                <!-- CUSTOMER -->
                <div class="detail-item">

                    <div class="detail-label">
                        Customer
                    </div>

                    <div class="detail-value">
                        {{ $schedule->order->customer->name ?? 'N/A' }}
                    </div>

                </div>


                <!-- SERVICE -->
                <div class="detail-item">

                    <div class="detail-label">
                        Service
                    </div>

                    <div class="detail-value">
                        {{ $schedule->order->service->service_name ?? 'N/A' }}
                    </div>

                </div>


                <!-- TYPE -->
                <div class="detail-item">

                    <div class="detail-label">
                        Schedule Type
                    </div>

                    <div class="detail-value">
                        {{ $schedule->type }}
                    </div>

                </div>


                <!-- DATE -->
                <div class="detail-item">

                    <div class="detail-label">
                        Schedule Date
                    </div>

                    <div class="detail-value">
                        {{ $schedule->schedule_date?->format('M d, Y') }}
                    </div>

                </div>


                <!-- TIME -->
                <div class="detail-item">

                    <div class="detail-label">
                        Schedule Time
                    </div>

                    <div class="detail-value">
                        {{ \Carbon\Carbon::parse($schedule->schedule_time)->format('h:i A') }}
                    </div>

                </div>


                <!-- STATUS -->
                <div class="detail-item">

                    <div class="detail-label">
                        Status
                    </div>

                    <div class="detail-value">

                        @php
                            $statusClass = match($schedule->status) {
                                'Scheduled' => 'status-scheduled',
                                'On Time' => 'status-on-time',
                                'Completed' => 'status-completed',
                                'Cancelled' => 'status-cancelled',
                                default => 'status-scheduled',
                            };
                        @endphp

                        <span class="status {{ $statusClass }}">
                            {{ $schedule->status }}
                        </span>

                    </div>

                </div>


                <!-- NOTES -->
                <div class="detail-item notes">

                    <div class="detail-label">
                        Notes
                    </div>

                    <div class="detail-value">
                        {{ $schedule->notes ?: 'No notes added.' }}
                    </div>

                </div>

            </div>

        </div>

    </main>


    <script>
        lucide.createIcons();
    </script>

</body>
</html>

