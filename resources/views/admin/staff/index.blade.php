<!DOCTYPE html>
<html lang="en">

<head>
    @include('partials.theme-assets')
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">

    <title>LAVEA | Staff</title>

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
            padding: 0 30px 30px;
        }

        /* =========================
           TOPBAR
        ========================= */

        .topbar {
            height: 75px;
            background: white;
            border: 1px solid #e8ecf3;
            border-radius: 10px;
            display: flex;
            justify-content: space-between;
            align-items: center;
            padding: 0 28px;
            margin-bottom: 25px;
        }

        .title h1 {
            font-size: 26px;
            color: #10204a;
        }

        .title p {
            color: #7a879d;
            font-size: 13px;
            margin-top: 6px;
        }

        .admin {
            display: flex;
            align-items: center;
            gap: 10px;
            color: #172554;
            font-size: 13px;
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
            font-weight: 600;
            font-size: 13px;
        }

        /* =========================
           SUCCESS
        ========================= */

        .success {
            background: #e6f8ef;
            color: #168458;
            padding: 13px 16px;
            border-radius: 8px;
            margin-bottom: 20px;
            font-size: 13px;
        }

        .email-error {
            background: #fff1f0;
            color: #b42318;
            padding: 13px 16px;
            border-radius: 8px;
            margin-bottom: 20px;
            font-size: 13px;
        }

        /* =========================
           CARD
        ========================= */

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
            font-size: 19px;
            color: #172554;
        }

        /* =========================
           BUTTONS
        ========================= */

        .btn {
            border: none;
            border-radius: 8px;
            padding: 10px 14px;
            text-decoration: none;
            cursor: pointer;
            font-size: 13px;
            display: inline-flex;
            align-items: center;
            justify-content: center;
            gap: 6px;
        }

        .btn svg {
            width: 16px;
            height: 16px;
        }

        .btn-primary {
            background: #4169c8;
            color: white;
        }

        .btn-primary:hover {
            background: #3459b1;
        }

        .btn-view {
            background: #edf3ff;
            color: #3973e6;
        }

        .btn-edit {
            background: #f0eaff;
            color: #7545d0;
        }

        .btn-delete {
            background: #fff0f1;
            color: #e05263;
        }

        /* =========================
           TABLE
        ========================= */

        .table-container {
            overflow-x: auto;
        }

        table {
            width: 100%;
            border-collapse: collapse;
        }

        th {
            text-align: left;
            padding: 13px;
            background: #f8f9fc;
            color: #718099;
            font-size: 11px;
            font-weight: 600;
        }

        td {
            padding: 14px 13px;
            border-bottom: 1px solid #edf0f5;
            font-size: 12px;
            color: #44526d;
        }

        td strong {
            color: #172554;
            font-weight: 600;
        }

        /* =========================
           BADGES
        ========================= */

        .badge {
            padding: 5px 10px;
            border-radius: 20px;
            font-size: 11px;
            font-weight: 600;
        }

        .active-badge {
            background: #e6f8ef;
            color: #168458;
        }

        .inactive-badge {
            background: #fff0f1;
            color: #e05263;
        }

        /* =========================
           ACTIONS
        ========================= */

        .actions {
            display: flex;
            gap: 7px;
        }

        .actions form {
            display: inline;
        }

        .actions .btn {
            width: 34px;
            height: 34px;
            padding: 0;
        }

        /* =========================
           EMPTY
        ========================= */

        .empty {
            text-align: center;
            padding: 50px 20px;
            color: #8995aa;
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
                padding: 0 30px 30px;
            }

            .topbar {
                padding: 0 18px;
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


    <!-- =========================
         MAIN CONTENT
    ========================= -->

    <main class="main">

        <!-- TOPBAR -->
        @include('admin.partials.topbar')

    <div class="lavea-staff-heading">
            <div>
                <h1>Staff</h1>
                <p>Manage LAVEA staff members and their roles.</p>
            </div>
            @include('admin.partials.current-date')
        </div>

        <!-- SUCCESS MESSAGE -->
        @if(session('success'))

            <div class="success">
                {{ session('success') }}
            </div>

        @endif

        @if(session('email_error'))
            <div class="email-error">{{ session('email_error') }}</div>
        @endif


        <!-- STAFF CARD -->
        <div class="card">

            <div class="card-header">

                <h2>Staff List</h2>

                <a
                    href="{{ route('admin.staff.create') }}"
                    class="btn btn-primary"
                >
                    <i data-lucide="plus"></i>
                    Add Staff
                </a>

            </div>


            <div class="table-container">

                <table>

                    <thead>

                        <tr>
                            <th>Name</th>
                            <th>Email</th>
                            <th>Phone</th>
                            <th>Role</th>
                            <th>Status</th>
                            <th>Actions</th>
                        </tr>

                    </thead>


                    <tbody>

                        @forelse($staff as $member)

                            <tr>

                                <td>
                                    <strong>
                                        {{ $member->name }}
                                    </strong>
                                </td>

                                <td>
                                    {{ $member->email ?? '—' }}
                                </td>

                                <td>
                                    {{ $member->phone ?? '—' }}
                                </td>

                                <td>
                                    {{ $member->role }}
                                </td>

                                <td>

                                    @if($member->status === 'Active')

                                        <span class="badge active-badge">
                                            Active
                                        </span>

                                    @else

                                        <span class="badge inactive-badge">
                                            Inactive
                                        </span>

                                    @endif

                                </td>

                                <td>

                                    <div class="actions">

                                        @if($member->user)
                                            <form action="{{ route('admin.staff.invitation', $member) }}" method="POST">
                                                @csrf
                                                <button type="submit" class="btn btn-view" title="Resend password setup link" aria-label="Resend password setup link">
                                                    <i data-lucide="mail"></i>
                                                </button>
                                            </form>
                                        @endif

                                        <!-- VIEW -->
                                        <a
                                            href="{{ route('admin.staff.show', $member->id) }}"
                                            class="btn btn-view"
                                            title="View"
                                        >
                                            <i data-lucide="eye"></i>
                                        </a>


                                        <!-- EDIT -->
                                        <a
                                            href="{{ route('admin.staff.edit', $member->id) }}"
                                            class="btn btn-edit"
                                            title="Edit"
                                        >
                                            <i data-lucide="pencil"></i>
                                        </a>


                                        <!-- DELETE -->
                                        <form
                                            action="{{ route('admin.staff.destroy', $member->id) }}"
                                            method="POST"
                                            onsubmit="return confirm('Delete this staff member?');"
                                        >

                                            @csrf
                                            @method('DELETE')

                                            <button
                                                type="submit"
                                                class="btn btn-delete"
                                                title="Delete"
                                            >
                                                <i data-lucide="trash-2"></i>
                                            </button>

                                        </form>

                                    </div>

                                </td>

                            </tr>

                        @empty

                            <tr>

                                <td
                                    colspan="6"
                                    class="empty"
                                >
                                    No staff members found.
                                </td>

                            </tr>

                        @endforelse

                    </tbody>

                </table>

            </div>

        </div>

    </main>

</div>


<script>
    lucide.createIcons();
</script>

</body>
</html>


