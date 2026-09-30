<!DOCTYPE html>
<html lang="en">
<head>
    @include('partials.theme-assets')
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">

    <title>Edit Schedule | LAVEA</title>

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

        .sidebar {
            width: 255px;
            background: #111d38;
            min-height: 100vh;
            position: fixed;
            left: 0;
            top: 0;
            bottom: 0;
        }

        .logo {
            padding: 28px 25px;
            border-bottom: 1px solid rgba(255,255,255,0.08);
        }

        .logo h1 {
            color: white;
            font-size: 28px;
            letter-spacing: 2px;
        }

        .logo p {
            color: #9eacc8;
            font-size: 12px;
            margin-top: 4px;
        }

        .menu {
            padding: 20px 14px;
        }

        .menu a {
            display: flex;
            align-items: center;
            gap: 13px;
            color: #dce5f7;
            text-decoration: none;
            padding: 13px 14px;
            margin-bottom: 5px;
            border-radius: 8px;
            font-size: 14px;
            transition: 0.2s;
        }

        .menu a:hover,
        .menu a.active {
            background: #4169c8;
            color: white;
        }

        .menu i {
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

        .content {
            padding: 30px;
        }

        .page-header {
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

        .card {
            background: white;
            border: 1px solid #e5e7eb;
            border-radius: 10px;
            padding: 25px;
            max-width: 900px;
        }

        .form-grid {
            display: grid;
            grid-template-columns: 1fr 1fr;
            gap: 20px;
        }

        .form-group {
            display: flex;
            flex-direction: column;
        }

        .form-group.full {
            grid-column: 1 / -1;
        }

        label {
            font-size: 13px;
            font-weight: 600;
            color: #374151;
            margin-bottom: 8px;
        }

        label span {
            color: #d64545;
        }

        input,
        select,
        textarea {
            width: 100%;
            border: 1px solid #dfe3ea;
            border-radius: 7px;
            padding: 11px 12px;
            font-size: 13px;
            outline: none;
            background: white;
            color: #374151;
        }

        input:focus,
        select:focus,
        textarea:focus {
            border-color: #4169c8;
            box-shadow: 0 0 0 2px rgba(65,105,200,0.08);
        }

        textarea {
            resize: vertical;
            min-height: 100px;
        }

        .error {
            color: #c23c3c;
            font-size: 11px;
            margin-top: 5px;
        }

        .form-actions {
            display: flex;
            justify-content: flex-end;
            gap: 10px;
            margin-top: 25px;
            padding-top: 20px;
            border-top: 1px solid #e5e7eb;
        }

        .btn {
            border: none;
            border-radius: 7px;
            padding: 11px 18px;
            font-size: 13px;
            text-decoration: none;
            cursor: pointer;
        }

        .cancel-btn {
            background: #eef1f5;
            color: #4b5563;
        }

        .save-btn {
            background: #4169c8;
            color: white;
        }

        .save-btn:hover {
            background: #355ab0;
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

            .form-grid {
                grid-template-columns: 1fr;
            }

            .form-group.full {
                grid-column: auto;
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


    <!-- MAIN -->
    <main class="main">

        @include('admin.partials.topbar')


        <section class="content">

            <div class="page-header">

                <h2>Edit Schedule</h2>

                <p>
                    Update the pickup or delivery schedule.
                </p>

            </div>


            <div class="card">

                <form
                    action="{{ route($routePrefix.'.update', $schedule->id) }}"
                    method="POST"
                >

                    @csrf
                    @method('PUT')

                    <div class="form-grid">

                        <!-- ORDER -->
                        <div class="form-group full">

                            <label>
                                Order <span>*</span>
                            </label>

                            <select name="order_id" required>

                                <option value="">
                                    Select Order
                                </option>

                                @foreach($orders as $order)

                                    <option
                                        value="{{ $order->id }}"
                                        {{ old('order_id', $schedule->order_id) == $order->id ? 'selected' : '' }}
                                    >
                                        #LVE-{{ str_pad($order->id, 4, '0', STR_PAD_LEFT) }}
                                        -
                                        {{ $order->customer->name ?? 'N/A' }}
                                        -
                                        {{ $order->service->service_name ?? 'N/A' }}
                                    </option>

                                @endforeach

                            </select>

                            @error('order_id')
                                <div class="error">
                                    {{ $message }}
                                </div>
                            @enderror

                        </div>

                        <!-- TYPE -->
                        <div class="form-group">

                            <label>
                                Schedule Type <span>*</span>
                            </label>

                            <select name="type" required>

                                <option value="Pickup"
                                    {{ old('type', $schedule->type) == 'Pickup' ? 'selected' : '' }}>
                                    Pickup
                                </option>

                                <option value="Delivery"
                                    {{ old('type', $schedule->type) == 'Delivery' ? 'selected' : '' }}>
                                    Delivery
                                </option>

                            </select>

                            @error('type')
                                <div class="error">
                                    {{ $message }}
                                </div>
                            @enderror

                        </div>


                        <!-- STATUS -->
                        <div class="form-group">

                            <label>
                                Status <span>*</span>
                            </label>

                            <select name="status" required>

                                <option value="Scheduled"
                                    {{ old('status', $schedule->status) == 'Scheduled' ? 'selected' : '' }}>
                                    Scheduled
                                </option>

                                <option value="On Time"
                                    {{ old('status', $schedule->status) == 'On Time' ? 'selected' : '' }}>
                                    On Time
                                </option>

                                <option value="Completed"
                                    {{ old('status', $schedule->status) == 'Completed' ? 'selected' : '' }}>
                                    Completed
                                </option>

                                <option value="Cancelled"
                                    {{ old('status', $schedule->status) == 'Cancelled' ? 'selected' : '' }}>
                                    Cancelled
                                </option>

                            </select>

                            @error('status')
                                <div class="error">
                                    {{ $message }}
                                </div>
                            @enderror

                        </div>


                        <!-- DATE -->
                        <div class="form-group">

                            <label>
                                Schedule Date <span>*</span>
                            </label>

                            <input
                                type="date"
                                name="schedule_date"
                                value="{{ old('schedule_date', $schedule->schedule_date?->format('Y-m-d')) }}"
                                min="{{ now()->toDateString() }}"
                                required
                            >

                            @error('schedule_date')
                                <div class="error">
                                    {{ $message }}
                                </div>
                            @enderror

                        </div>


                        <!-- TIME -->
                        <div class="form-group">

                            <label>
                                Schedule Time <span>*</span>
                            </label>

                            <input
                                type="time"
                                name="schedule_time"
                                value="{{ old('schedule_time', $schedule->schedule_time) }}"
                                required
                            >

                            @error('schedule_time')
                                <div class="error">
                                    {{ $message }}
                                </div>
                            @enderror

                        </div>


                        <!-- NOTES -->
                        <div class="form-group full">

                            <label>
                                Notes
                            </label>

                            <textarea
                                name="notes"
                                placeholder="Enter additional notes..."
                            >{{ old('notes', $schedule->notes) }}</textarea>

                            @error('notes')
                                <div class="error">
                                    {{ $message }}
                                </div>
                            @enderror

                        </div>

                    </div>


                    <div class="form-actions">

                        <a
                            href="{{ route($routePrefix.'.index') }}"
                            class="btn cancel-btn"
                        >
                            Cancel
                        </a>

                        <button
                            type="submit"
                            class="btn save-btn"
                        >
                            Update Schedule
                        </button>

                    </div>

                </form>

            </div>

        </section>

    </main>

</div>

<script>
    lucide.createIcons();
</script>

</body>
</html>


