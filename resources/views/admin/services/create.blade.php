<!DOCTYPE html>
<html lang="en">

<head>
    @include('partials.theme-assets')
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">

    <title>LAVEA | Add Service</title>

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
            width: calc(100% - 255px);
            min-height: 100vh;
        }

        .content {
            padding: 30px;
        }

        .page-header {
            margin-bottom: 25px;
        }

        .page-header h2 {
            font-size: 26px;
            color: #10204a;
        }

        .page-header p {
            color: #7a879d;
            font-size: 13px;
            margin-top: 6px;
        }

        .card {
            background: white;
            border: 1px solid #e7ebf2;
            border-radius: 10px;
            padding: 25px;
            max-width: 850px;
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
            font-size: 12px;
            font-weight: 600;
            color: #44526d;
            margin-bottom: 7px;
        }

        input,
        textarea {
            width: 100%;
            min-height: 42px;
            border: 1px solid #dfe5ef;
            border-radius: 8px;
            padding: 10px 12px;
            outline: none;
            font-size: 13px;
            color: #172554;
            background: white;
        }

        input:focus,
        textarea:focus {
            border-color: #4169c8;
        }

        textarea {
            min-height: 110px;
            resize: vertical;
        }

        .error {
            color: #e05263;
            font-size: 11px;
            margin-top: 5px;
        }

        .form-actions {
            display: flex;
            justify-content: flex-end;
            gap: 10px;
            margin-top: 25px;
            padding-top: 20px;
            border-top: 1px solid #edf0f5;
        }

        .btn {
            height: 40px;
            padding: 0 17px;
            border-radius: 8px;
            border: none;
            text-decoration: none;
            display: inline-flex;
            align-items: center;
            justify-content: center;
            gap: 7px;
            font-size: 13px;
            cursor: pointer;
        }

        .btn svg {
            width: 16px;
            height: 16px;
        }

        .btn-cancel {
            background: #f1f3f7;
            color: #596579;
        }

        .btn-cancel:hover {
            background: #e5e8ee;
        }

        .btn-primary {
            background: #4169c8;
            color: white;
        }

        .btn-primary:hover {
            background: #3459b1;
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
        }
    </style>
    <link rel="stylesheet" href="{{ asset('css/admin-topbar.css') }}">
</head>

<body>

<div class="layout">

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
            <a href="{{ route('admin.services.index') }}" class="active">
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

    <main class="main">
        @include('admin.partials.topbar')

        <section class="content">
            <div class="page-header">
                <h2>Add Service</h2>
                <p>Enter the service information below.</p>
            </div>

            <div class="card">
                <form action="{{ route('admin.services.store') }}" method="POST" enctype="multipart/form-data">
                    @csrf

                    <div class="form-grid">
                        <div class="form-group full">
                            <label for="service_name">Service Name</label>
                            <input
                                type="text"
                                id="service_name"
                                name="service_name"
                                value="{{ old('service_name') }}"
                                placeholder="Example: Wash & Fold"
                                required
                            >
                            @error('service_name')
                                <div class="error">{{ $message }}</div>
                            @enderror
                        </div>

                        <div class="form-group">
                            <label for="price">Price</label>
                            <input
                                type="number"
                                id="price"
                                name="price"
                                value="{{ old('price') }}"
                                step="0.01"
                                min="0"
                                placeholder="Example: 150.00"
                                required
                            >
                            @error('price')
                                <div class="error">{{ $message }}</div>
                            @enderror
                        </div>

                        <div class="form-group full">
                            <label for="description">Description</label>
                            <textarea
                                id="description"
                                name="description"
                                placeholder="Enter service description..."
                            >{{ old('description') }}</textarea>
                            @error('description')
                                <div class="error">{{ $message }}</div>
                            @enderror
                        </div>

                        <div class="form-group full">
                            <label for="image">Service Image</label>
                            <input
                                type="file"
                                id="image"
                                name="image"
                                accept=".jpg,.jpeg,.png"
                            >
                            @error('image')
                                <div class="error">{{ $message }}</div>
                            @enderror
                        </div>
                    </div>

                    <div class="form-actions">
                        <a href="{{ route('admin.services.index') }}" class="btn btn-cancel">Cancel</a>
                        <button type="submit" class="btn btn-primary">
                            <i data-lucide="plus"></i>
                            Save Service
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
