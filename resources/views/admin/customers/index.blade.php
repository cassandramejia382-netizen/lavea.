<!DOCTYPE html>
<html lang="en">

<head>
    @include('partials.theme-assets')
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">

    <title>LAVEA | Customers</title>

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
            width: 17px;
            height: 17px;
        }

        .search button {
            border: none;
            background: transparent;
            color: inherit;
            display: flex;
            cursor: pointer;
            padding: 0;
        }

        .search input {
            border: none;
            outline: none;
            margin-left: 10px;
            width: 100%;
            font-size: 13px;
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
            color: #4169c8;
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

        .add-btn svg {
            width: 16px;
            height: 16px;
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

        .customer {
            display: flex;
            align-items: center;
            gap: 11px;
        }

        .customer-avatar {
            width: 38px;
            height: 38px;
            background: #e8eefb;
            color: #4169c8;
            border-radius: 50%;
            display: flex;
            align-items: center;
            justify-content: center;
            flex-shrink: 0;
        }

        .customer-avatar svg {
            width: 17px;
            height: 17px;
        }

        .customer-name {
            font-weight: 600;
            color: #172554;
        }

        .customer-email {
            font-size: 10px;
            color: #8995aa;
            margin-top: 3px;
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
            border: none;
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

            <!-- DASHBOARD -->
            <a href="{{ route('admin.dashboard') }}">
                <i data-lucide="layout-dashboard"></i>
                <span>Dashboard</span>
            </a>

            <!-- CUSTOMERS ACTIVE -->
            <a href="{{ route('admin.customers.index') }}" class="active">
                <i data-lucide="users"></i>
                <span>Customers</span>
            </a>

            <!-- STAFF -->
            <a href="{{ route('admin.staff.index') }}">
                <i data-lucide="user-round-cog"></i>
                <span>Staff</span>
            </a>

            <!-- SERVICES -->
            <a href="{{ route('admin.services.index') }}">
                <i data-lucide="package"></i>
                <span>Services</span>
            </a>

            <!-- ORDERS -->
            <a href="{{ route('admin.orders.index') }}">
                <i data-lucide="clipboard-list"></i>
                <span>Orders</span>
            </a>

            <!-- PAYMENTS -->
            <a href="{{ route('admin.payments.index') }}">
    <i data-lucide="credit-card"></i>
    <span>Payments / Transactions</span>
</a>
            <!-- SCHEDULE -->
           <a href="{{ route('admin.schedules.index') }}">
    <i data-lucide="calendar-days"></i>
    <span>Schedule</span>
</a>

            <!-- REPORTS -->
      <a href="{{ route('admin.reports') }}">
    <i data-lucide="bar-chart-3"></i>
    <span>Reports</span>
</a>

            <!-- SETTINGS -->
            <a href="{{ route('admin.settings') }}">
    <i data-lucide="settings"></i>
    <span>Settings</span>
</a>

        </nav>

    </aside>


    <!-- MAIN -->
    <main class="main">

        <!-- TOPBAR -->
        @include('admin.partials.topbar', ['customerSearch' => true])


        <!-- CONTENT -->
        <section class="content">

            <div class="page-header">

                <div>
                    <h1>Customers</h1>
                    <p>Manage your laundry customers.</p>
                </div>

                @include('admin.partials.current-date')

            </div>


            @if(session('success'))

                <div class="success">
                    {{ session('success') }}
                </div>

            @endif


            <!-- CUSTOMER TABLE -->
            <div class="card">

                <div class="table-container">

                    <table>

                        <thead>

                            <tr>
                                <th>Customer</th>
                                <th>Phone</th>
                                <th>Address</th>
                                <th>View</th>
                            </tr>

                        </thead>

                        <tbody>

                            @forelse($customers as $customer)

                                <tr>

                                    <td>

                                        <div class="customer">

                                            <div class="customer-avatar">
                                                <i data-lucide="user"></i>
                                            </div>

                                            <div>

                                                <div class="customer-name">
                                                    {{ $customer->name }}
                                                </div>

                                                <div class="customer-email">
                                                    {{ $customer->email ?: 'No email' }}
                                                </div>

                                            </div>

                                        </div>

                                    </td>

                                    <td>
                                        {{ $customer->phone }}
                                    </td>

                                    <td>
                                        {{ $customer->address ?: 'No address' }}
                                    </td>

                                    <td>

                                        <div class="actions">

                                            <!-- VIEW -->
                                            <a
                                                href="{{ route('admin.customers.show', $customer->id) }}"
                                                class="action view"
                                                title="View"
                                                aria-label="View customer details"
                                            >
                                                <i data-lucide="eye"></i>
                                            </a>

                                        </div>

                                    </td>

                                </tr>

                            @empty

                                <tr>

                                    <td colspan="4">

                                        <div class="empty">

                                            <i data-lucide="users-round"></i>

                                            <p>No customers found.</p>

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


