<!DOCTYPE html>
<html lang="en">

<head>
    @include('partials.theme-assets')
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">

    <title>LAVEA | Add Staff</title>

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

        /* =========================
           SIDEBAR
        ========================= */

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

        /* =========================
           MAIN
        ========================= */

        .main {
            margin-left: 255px;
            width: calc(100% - 255px);
            min-height: 100vh;
        }

        /* =========================
           TOPBAR
        ========================= */

        .topbar {
            height: 75px;
            background: white;
            border-bottom: 1px solid #e8ecf3;
            display: flex;
            justify-content: space-between;
            align-items: center;
            padding: 0 28px;
        }

        .topbar-title h1 {
            font-size: 22px;
            color: #10204a;
        }

        .topbar-title p {
            color: #8995aa;
            font-size: 12px;
            margin-top: 4px;
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
            color: #4169c8;
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

        /* =========================
           CONTENT
        ========================= */

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

        /* =========================
           FORM CARD
        ========================= */

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
        select {
            width: 100%;
            height: 42px;
            border: 1px solid #dfe5ef;
            border-radius: 8px;
            padding: 0 12px;
            outline: none;
            font-size: 13px;
            color: #172554;
            background: white;
        }

        input:focus,
        select:focus {
            border-color: #4169c8;
        }

        .error {
            color: #e05263;
            font-size: 11px;
            margin-top: 5px;
        }

        /* =========================
           BUTTONS
        ========================= */

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

        /* =========================
           RESPONSIVE
        ========================= */

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

    <!-- =========================
         SIDEBAR
    ========================= -->

    <aside class="sidebar">

        <div class="logo">
            <h1>LAVEA</h1>
            <p>Laundry Made Easy</p>
        </div>

        <nav class="menu">

            <!-- DASHBOARD -->
            <a href="{{ route('admin.dashboard') }}">
                <i data-lucide="layout-dashboard"></i>
                <span>Dashboard</span>
            </a>

            <!-- CUSTOMERS -->
            <a href="{{ route('admin.customers.index') }}">
                <i data-lucide="users"></i>
                <span>Customers</span>
            </a>

            <!-- STAFF -->
            <a href="{{ route('admin.staff.index') }}" class="active">
                <i data-lucide="user-round-cog"></i>
                <span>Staff</span>
            </a>

            <!-- SERVICES -->
            <a href="{{ route('admin.services.index') }}">
                <i data-lucide="package"></i>
                <span>Services</span>
            </a>

            <!-- ORDERS -->
            <a href="#">
                <i data-lucide="clipboard-list"></i>
                <span>Orders</span>
            </a>

            <!-- PAYMENTS -->
            <a href="#">
                <i data-lucide="credit-card"></i>
                <span>Payments / Transactions</span>
            </a>

            <!-- SCHEDULE -->
            <a href="#">
                <i data-lucide="calendar-days"></i>
                <span>Schedule</span>
            </a>

            <!-- REPORTS -->
            <a href="#">
                <i data-lucide="bar-chart-3"></i>
                <span>Reports</span>
            </a>

            <!-- SETTINGS -->
            <a href="#">
                <i data-lucide="settings"></i>
                <span>Settings</span>
            </a>

        </nav>

    </aside>


    <!-- =========================
         MAIN
    ========================= -->

    <main class="main">

        <!-- TOPBAR -->
        @include('admin.partials.topbar')


        <!-- CONTENT -->
        <section class="content">

            <div class="page-header">

                <h2>Add Staff</h2>

                <p>
                    Enter the staff member's information below.
                </p>

            </div>


            <!-- FORM CARD -->
            <div class="card">

                <form
                    action="{{ route('admin.staff.store') }}"
                    method="POST"
                >

                    @csrf

                    <div class="form-grid">

                        <!-- NAME -->
                        <div class="form-group full">

                            <label for="name">
                                Full Name
                            </label>

                            <input
                                type="text"
                                id="name"
                                name="name"
                                value="{{ old('name') }}"
                                placeholder="Enter full name"
                                required
                            >

                            @error('name')
                                <div class="error">
                                    {{ $message }}
                                </div>
                            @enderror

                        </div>


                        <!-- EMAIL -->
                        <div class="form-group">

                            <label for="email">
                                Email
                            </label>

                            <input
                                type="email"
                                id="email"
                                name="email"
                                value="{{ old('email') }}"
                                placeholder="Enter email address"
                                required
                            >

                            @error('email')
                                <div class="error">
                                    {{ $message }}
                                </div>
                            @enderror

                        </div>


                        <!-- PHONE -->
                        <div class="form-group">

                            <label for="phone">
                                Phone Number
                            </label>

                            <input
                                type="text"
                                id="phone"
                                name="phone"
                                value="{{ old('phone') }}"
                                placeholder="Enter phone number"
                            >

                            @error('phone')
                                <div class="error">
                                    {{ $message }}
                                </div>
                            @enderror

                        </div>


                        <!-- ROLE -->
                        <div class="form-group">

                            <label for="role">
                                Role
                            </label>

                            <select
                                id="role"
                                name="role"
                                required
                            >

                                <option value="">
                                    Select role
                                </option>

                                <option
                                    value="Staff"
                                    {{ old('role') == 'Staff' ? 'selected' : '' }}
                                >
                                    Staff
                                </option>

                                <option
                                    value="Manager"
                                    {{ old('role') == 'Manager' ? 'selected' : '' }}
                                >
                                    Manager
                                </option>

                            </select>

                            @error('role')
                                <div class="error">
                                    {{ $message }}
                                </div>
                            @enderror

                        </div>


                        <!-- STATUS -->
                        <div class="form-group">

                            <label for="status">
                                Status
                            </label>

                            <select
                                id="status"
                                name="status"
                                required
                            >

                                <option
                                    value="Active"
                                    {{ old('status', 'Active') == 'Active' ? 'selected' : '' }}
                                >
                                    Active
                                </option>

                                <option
                                    value="Inactive"
                                    {{ old('status') == 'Inactive' ? 'selected' : '' }}
                                >
                                    Inactive
                                </option>

                            </select>

                            @error('status')
                                <div class="error">
                                    {{ $message }}
                                </div>
                            @enderror

                        </div>

                    </div>


                    <!-- BUTTONS -->
                    <div class="form-actions">

                        <a
                            href="{{ route('admin.staff.index') }}"
                            class="btn btn-cancel"
                        >
                            Cancel
                        </a>

                        <button
                            type="submit"
                            class="btn btn-primary"
                        >
                            <i data-lucide="user-plus"></i>
                            Add Staff
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


