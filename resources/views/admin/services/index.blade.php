﻿<!DOCTYPE html>
<html lang="en">

<head>
    @include('partials.theme-assets')
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">

    <title>LAVEA | Services</title>

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
            height: 18px;
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

        .page-header h1 {
            font-size: 26px;
            color: #10204a;
        }

        .page-header p {
            color: #7a879d;
            font-size: 13px;
            margin-top: 6px;
        }

        /* Match the Customers page header dimensions exactly. */
        #services-page-header {
            display: flex;
            justify-content: space-between;
            align-items: center;
            width: 100%;
            margin-bottom: 25px;
        }

        #services-page-header h1 {
            font-size: 26px;
            color: #10204a;
        }

        #services-page-header p {
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

        .add-btn:hover {
            background: #3459b1;
        }

        /* SUCCESS */

        .success {
            background: #e6f8ef;
            color: #168458;
            padding: 13px 16px;
            border-radius: 8px;
            margin-bottom: 20px;
            font-size: 13px;
        }

        /* CARD */

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

        .service-image {
            display: block;
            width: 55px;
            height: 55px;
            border-radius: 8px;
            object-fit: cover;
            object-position: center;
            background: #eef2f8;
        }

        .no-image {
            width: 55px;
            height: 55px;
            border-radius: 8px;
            background: #eef2f8;
            display: flex;
            align-items: center;
            justify-content: center;
            color: #8995aa;
        }

        .price {
            font-weight: bold;
            color: #172554;
        }

        .description {
            max-width: 280px;
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
        }

        .action svg {
            width: 16px;
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
            border: none;
            cursor: pointer;
        }

        .empty {
            text-align: center;
            padding: 50px 20px;
            color: #8995aa;
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

            <a href="{{ route('admin.dashboard') }}">
                <i data-lucide="layout-dashboard"></i>
                <span>Dashboard</span>
            </a>

            <a href="{{ route('admin.customers.index') }}">
                <i data-lucide="users"></i>
                <span>Customers</span>
            </a>

            <!-- STAFF -->
            <a href="{{ route('admin.staff.index') }}">
                <i data-lucide="user-round-cog"></i>
                <span>Staff</span>
            </a>

            <!-- SERVICES ACTIVE -->
            <a href="{{ route('admin.services.index') }}" class="active">
                <i data-lucide="package"></i>
                <span>Services</span>
            </a>

            <!-- ORDERS -->
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

        @include('admin.partials.topbar')


        <section class="content">

            <div class="page-header" id="services-page-header">
                <div>
                    <h1>Services</h1>
                    <p>Manage laundry services and pricing.</p>
                </div>

                @include('admin.partials.current-date')
            </div>


            <!-- SUCCESS MESSAGE -->
            @if(session('success'))

                <div class="success">
                    {{ session('success') }}
                </div>

            @endif


            <!-- SERVICE TABLE -->
            <div class="card">

                <div class="card-header">
                    <h2>Services List</h2>
                    <a href="{{ route('admin.services.create') }}" class="btn btn-primary">
                        <i data-lucide="plus"></i>
                        Add Service
                    </a>
                </div>

                <div class="table-container">

                    <table>

                        <thead>
                            <tr>
                                <th>Image</th>
                                <th>Service</th>
                                <th>Price</th>
                                <th>Description</th>
                                <th>Actions</th>
                            </tr>
                        </thead>

                        <tbody>

                            @forelse($services as $service)

                                <tr>

                                    <td>

                                        @if($service->image && file_exists(public_path('uploads/services/' . basename($service->image))))

                                            <img
                                                src="{{ asset('uploads/services/' . basename($service->image)) }}"
                                                class="service-image"
                                                alt="{{ $service->service_name }}"
                                            >

                                        @else

                                            <div class="no-image">
                                                <i data-lucide="package"></i>
                                            </div>

                                        @endif

                                    </td>

                                    <td>
                                        <strong>{{ $service->service_name }}</strong>
                                    </td>

                                    <td class="price">
                                        ₱{{ number_format($service->price, 2) }}
                                    </td>

                                    <td class="description">
                                        {{ $service->description ?: 'No description' }}
                                    </td>

                                    <td>

                                        <div class="actions">

                                            <!-- VIEW -->
                                            <a
                                                href="{{ route('admin.services.show', $service->id) }}"
                                                class="action view"
                                                title="View"
                                            >
                                                <i data-lucide="eye"></i>
                                            </a>

                                            <!-- EDIT -->
                                            <a
                                                href="{{ route('admin.services.edit', $service->id) }}"
                                                class="action edit"
                                                title="Edit"
                                            >
                                                <i data-lucide="pencil"></i>
                                            </a>

                                            <!-- DELETE -->
                                            <form
                                                action="{{ route('admin.services.destroy', $service->id) }}"
                                                method="POST"
                                                onsubmit="return confirm('Are you sure you want to delete this service?')"
                                            >

                                                @csrf
                                                @method('DELETE')

                                                <button
                                                    type="submit"
                                                    class="action delete"
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
                                    <td colspan="5">

                                        <div class="empty">

                                            <i
                                                data-lucide="package-x"
                                                style="width:40px;height:40px;margin-bottom:10px;"
                                            >
                                            </i>

                                            <p>No services found.</p>

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

