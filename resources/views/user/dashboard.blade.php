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

        /* =========================
           MAIN DASHBOARD
        ========================= */

        .dashboard {
            display: flex;
            min-height: 100vh;
        }

        /* =========================
           SIDEBAR
        ========================= */

        .sidebar {
            width: 255px;
            background: #111d38;
            color: #fff;
            padding: 25px 16px;
            display: flex;
            flex-direction: column;
            position: fixed;
            inset: 0 auto 0 0;
            overflow-y: auto;
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
        }

        .menu a.active {
            background: #4169c8;
            color: #fff;
        }

        .menu svg {
            width: 19px;
            height: 19px;
        }

        /* =========================
           ADMIN PROFILE
        ========================= */

        .admin-profile {
            margin-top: auto;
            border-top: 1px solid #2a3b5d;
            padding: 20px 5px 5px;
            display: flex;
            align-items: center;
            gap: 12px;
        }

        .profile-icon,
        .avatar {
            width: 40px;
            height: 40px;
            background: #e7edf9;
            color: #26395e;
            border-radius: 50%;
            display: flex;
            align-items: center;
            justify-content: center;
            flex-shrink: 0;
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

        /* =========================
           MAIN AREA
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
            min-height: 75px;
            background: #fff;
            border-bottom: 1px solid #e8ecf3;
            display: flex;
            align-items: center;
            justify-content: space-between;
            padding: 14px 28px;
            gap: 18px;
        }

        .search-box {
            width: min(430px, 50%);
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
            flex-shrink: 0;
        }

        .search-box input {
            border: 0;
            outline: 0;
            margin-left: 10px;
            width: 100%;
            font-size: 13px;
            min-width: 0;
        }

        .top-right {
            display: flex;
            align-items: center;
            gap: 25px;
        }

        .notification {
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
        }

        .top-profile strong {
            font-size: 13px;
            display: block;
        }

        .top-profile span {
            font-size: 10px;
            color: #8792a6;
        }

        /* =========================
           CONTENT
        ========================= */

        .content {
            padding: 28px;
        }

        .welcome {
            display: flex;
            justify-content: space-between;
            align-items: flex-start;
            margin-bottom: 22px;
            gap: 15px;
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

        /* =========================
           STAT CARDS
        ========================= */

        .stats {
            display: grid;
            grid-template-columns: repeat(4, minmax(0, 1fr));
            gap: 17px;
            margin-bottom: 22px;
        }

        .stat-card {
            background: #fff;
            border: 1px solid #e7ebf2;
            border-radius: 10px;
            padding: 19px;
            display: flex;
            gap: 14px;
            align-items: flex-start;
            min-width: 0;
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
            overflow-wrap: anywhere;
        }

        .stat-sub {
            font-size: 10px;
            color: #8b96a9;
            margin-top: 7px;
        }

        /* =========================
           DASHBOARD GRID
        ========================= */

        .dashboard-grid,
        .bottom-grid {
            display: grid;
            grid-template-columns: minmax(0, 2fr) minmax(280px, 1fr);
            gap: 17px;
            margin-bottom: 22px;
            align-items: stretch;
        }

        .bottom-grid {
            margin-bottom: 0;
        }

        /* =========================
           CARD
        ========================= */

        .card {
            background: #fff;
            border: 1px solid #e7ebf2;
            border-radius: 10px;
            padding: 20px;
            min-width: 0;
            height: 100%;
            display: flex;
            flex-direction: column;
        }

        .card-header {
            display: flex;
            justify-content: space-between;
            align-items: center;
            margin-bottom: 20px;
            gap: 12px;
        }

        .card-header h2 {
            font-size: 16px;
            color: #12214a;
        }

        .card-subtitle {
            color: #8a96aa;
            font-size: 11px;
            margin-top: 5px;
        }

        .filter {
            border: 1px solid #dfe5ef;
            background: #fff;
            border-radius: 7px;
            padding: 8px 11px;
            font-size: 11px;
            color: #52617d;
            outline: none;
        }

        /* =========================
           SALES OVERVIEW
        ========================= */

        .sales-content {
            display: flex;
            flex-direction: column;
            flex: 1;
        }

        .sales-total {
            margin-bottom: 5px;
        }

        .sales-total span {
            display: block;
            color: #8a96aa;
            font-size: 11px;
            margin-bottom: 5px;
        }

        .sales-total strong {
            font-size: 24px;
            color: #12214a;
        }

        .chart {
            height: 230px;
            display: flex;
            align-items: flex-end;
            gap: 16px;
            padding: 20px 5px 0;
            border-bottom: 1px solid #e6eaf1;
            position: relative;
            flex: 1;
            margin-top: 10px;
        }

        .chart:before,
        .chart:after {
            content: "";
            position: absolute;
            left: 0;
            right: 0;
            border-top: 1px dashed #edf0f5;
        }

        .chart:before {
            top: 65px;
        }

        .chart:after {
            top: 135px;
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
            min-width: 0;
        }

        .bar-value {
            font-size: 9px;
            color: #6d7b95;
            margin-bottom: 5px;
        }

        .bar {
            width: 55%;
            background: #4169c8;
            border-radius: 5px 5px 0 0;
            min-height: 10px;
            transition: 0.2s;
        }

        .bar:hover {
            opacity: 0.75;
        }

        .bar-label {
            margin-top: 8px;
            font-size: 9px;
            color: #8994a8;
        }

        /* =========================
           ORDER STATUS
        ========================= */

        .status-card {
            min-height: 330px;
        }

        .status-container {
            display: flex;
            flex: 1;
            flex-direction: column;
            align-items: center;
            justify-content: space-between;
            min-height: 0;
        }

        .donut {
            width: 155px;
            height: 155px;
            border-radius: 50%;
            margin: 5px auto 18px;

            background:
                conic-gradient(
                    #f4b740 0deg 90deg,
                    #4f86ee 90deg 180deg,
                    #2cae83 180deg 315deg,
                    #ed6475 315deg 360deg
                );

            display: flex;
            align-items: center;
            justify-content: center;
            flex-shrink: 0;
        }

        .donut-center {
            width: 105px;
            height: 105px;
            background: #fff;
            border-radius: 50%;

            display: flex;
            align-items: center;
            justify-content: center;
            flex-direction: column;
        }

        .donut-center strong {
            font-size: 25px;
            color: #12214a;
        }

        .donut-center span {
            font-size: 9px;
            color: #8792a6;
            margin-top: 3px;
        }

        .status-list {
            width: 100%;
            margin-top: auto;
        }

        .status-row {
            display: flex;
            justify-content: space-between;
            align-items: center;
            font-size: 11px;
            padding: 8px 0;
            border-bottom: 1px solid #f0f2f6;
        }

        .status-row:last-child {
            border-bottom: none;
        }

        .status-name {
            display: flex;
            align-items: center;
            gap: 9px;
            color: #52617d;
        }

        .status-row strong {
            color: #172554;
        }

        .status-dot {
            width: 8px;
            height: 8px;
            border-radius: 50%;
            flex-shrink: 0;
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

        /* =========================
           RECENT ORDERS
        ========================= */

        .recent-orders-card {
            overflow: hidden;
        }

        .table-wrap {
            overflow-x: auto;
        }

        .orders-table {
            width: 100%;
            border-collapse: collapse;
            min-width: 700px;
        }

        .orders-table th {
            background: #f7f9fc;
            color: #75829a;
            font-size: 9px;
            font-weight: 600;
            letter-spacing: 0.4px;
            padding: 13px 10px;
            text-align: left;
            white-space: nowrap;
        }

        .orders-table td {
            padding: 14px 10px;
            border-bottom: 1px solid #edf0f5;
            font-size: 10px;
            color: #44526d;
            white-space: nowrap;
        }

        .orders-table tbody tr {
            transition: 0.2s;
        }

        .orders-table tbody tr:hover {
            background: #fafbfe;
        }

        .orders-table td strong {
            color: #172554;
        }

        .order-number {
            color: #4169c8 !important;
            font-size: 10px;
        }

        .status-badge {
            display: inline-block;
            padding: 5px 9px;
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

        .badge-cancelled {
            background: #ffe7eb;
            color: #d7475b;
        }

        .empty-orders {
            text-align: center !important;
            padding: 30px !important;
            color: #8a96aa !important;
        }

        .view-all {
            color: #4169c8;
            text-decoration: none;
            font-size: 11px;
            font-weight: 600;
        }

        .view-all:hover {
            text-decoration: underline;
        }

        /* =========================
           RECENT ACTIVITY
        ========================= */

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

        /* =========================
           RESPONSIVE
        ========================= */

        @media (max-width: 1100px) {

            .stats {
                grid-template-columns: repeat(2, minmax(0, 1fr));
            }

            .dashboard-grid,
            .bottom-grid {
                grid-template-columns: minmax(0, 1fr);
            }
        }

        @media (max-width: 700px) {

            .sidebar {
                width: 70px;
                padding: 20px 10px;
            }

            .logo-area {
                padding: 5px 0 25px;
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
                padding: 13px 10px;
            }

            .admin-profile {
                justify-content: center;
            }

            .main {
                margin-left: 70px;
                width: calc(100% - 70px);
            }

            .topbar {
                padding: 12px 15px;
            }

            .search-box {
                width: 180px;
            }

            .top-right {
                gap: 12px;
            }

            .top-profile {
                padding-left: 10px;
            }

            .top-profile > div:not(.avatar),
            .top-profile > svg {
                display: none;
            }

            .content {
                padding: 15px;
            }

            .welcome {
                flex-direction: column;
                gap: 10px;
            }

            .date {
                text-align: left;
            }
        }

        @media (max-width: 460px) {

            .stats {
                grid-template-columns: 1fr;
            }

            .search-box {
                width: 130px;
            }

            .top-profile {
                border: 0;
                padding: 0;
            }

            .top-right {
                gap: 10px;
            }

            .card {
                padding: 15px;
            }

            .chart {
                gap: 8px;
            }

            .bar-label {
                font-size: 8px;
            }
        }

    </style>
</head>

<body>

<div class="dashboard">

    <!-- =========================
         SIDEBAR
    ========================== -->

    <aside class="sidebar">

        <div class="logo-area">
            <div class="logo">LAVEA</div>
            <div class="logo-subtitle">
                Laundry Management System
            </div>
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
                <i data-lucide="user-round"></i>
                <span>Staff</span>
            </a>

            <a href="{{ route('admin.services.index') }}">
                <i data-lucide="shirt"></i>
                <span>Services</span>
            </a>

            <a href="{{ route('admin.orders.index') }}">
                <i data-lucide="clipboard-list"></i>
                <span>Orders</span>
            </a>

            <a href="{{ route('admin.payments.index') }}">
                <i data-lucide="credit-card"></i>
                <span>Payments</span>
            </a>

            <a href="{{ route('admin.schedules.index') }}">
                <i data-lucide="calendar-days"></i>
                <span>Schedule</span>
            </a>

            <a href="{{ route('admin.reports.index') }}">
                <i data-lucide="bar-chart-3"></i>
                <span>Reports</span>
            </a>

            <a href="{{ route('admin.settings.index') }}">
                <i data-lucide="settings"></i>
                <span>Settings</span>
            </a>

            <a href="{{ route('logout.confirm') }}" aria-label="Sign Out">
                <i data-lucide="log-out"></i>
                <span>Sign Out</span>
            </a>
        </nav>

        <div class="admin-profile">

            <div class="profile-icon">
                <i data-lucide="user"></i>
            </div>

            <div class="profile-info">
                <strong>Administrator</strong>
                <span>Admin Account</span>
            </div>

        </div>

    </aside>


    <!-- =========================
         MAIN
    ========================== -->

    <main class="main">

        <!-- TOPBAR -->

        <header class="topbar">

            <div class="search-box">

                <i data-lucide="search"></i>

                <input
                    type="text"
                    placeholder="Search..."
                >

            </div>

            <div class="top-right">

                <div class="notification">

                    <i data-lucide="bell"></i>

                    <span class="notification-dot"></span>

                </div>

                <div class="top-profile">

                    <div class="avatar">
                        <i data-lucide="user"></i>
                    </div>

                    <div>
                        <strong>Administrator</strong>
                        <span>Admin</span>
                    </div>

                </div>

            </div>

        </header>


        <!-- CONTENT -->

        <section class="content">

            <!-- WELCOME -->

            <div class="welcome">

                <div>

                    <h1>
                        Welcome back, Admin
                    </h1>

                    <p>
                        Here's what's happening with your laundry business today.
                    </p>

                </div>

                <div class="date">

                    <strong>
                        {{ now()->format('F d, Y') }}
                    </strong>

                    {{ now()->format('l') }}

                </div>

            </div>


            <!-- STATISTICS -->

            <div class="stats">

                <!-- TOTAL ORDERS -->

                <div class="stat-card">

                    <div class="stat-icon blue">
                        <i data-lucide="shopping-bag"></i>
                    </div>

                    <div>

                        <div class="stat-title">
                            Total Orders
                        </div>

                        <div class="stat-number">
                            {{ $stats['orders'] ?? 0 }}
                        </div>

                        <div class="stat-sub">
                            All orders
                        </div>

                    </div>

                </div>


                <!-- CUSTOMERS -->

                <div class="stat-card">

                    <div class="stat-icon green">
                        <i data-lucide="users"></i>
                    </div>

                    <div>

                        <div class="stat-title">
                            Customers
                        </div>

                        <div class="stat-number">
                            {{ $stats['customers'] ?? 0 }}
                        </div>

                        <div class="stat-sub">
                            Registered customers
                        </div>

                    </div>

                </div>


                <!-- STAFF -->

                <div class="stat-card">

                    <div class="stat-icon purple">
                        <i data-lucide="user-round"></i>
                    </div>

                    <div>

                        <div class="stat-title">
                            Staff
                        </div>

                        <div class="stat-number">
                            {{ $stats['staff'] ?? 0 }}
                        </div>

                        <div class="stat-sub">
                            Active staff
                        </div>

                    </div>

                </div>


                <!-- INCOME -->

                <div class="stat-card">

                    <div class="stat-icon orange">
                        <i data-lucide="wallet"></i>
                    </div>

                    <div>

                        <div class="stat-title">
                            Income
                        </div>

                        <div class="stat-number">
                            ₱{{ number_format($stats['income'] ?? 0, 2) }}
                        </div>

                        <div class="stat-sub">
                            Total income
                        </div>

                    </div>

                </div>

            </div>


            <!-- =================================
                 SALES OVERVIEW + ORDER STATUS
            ================================== -->

            <div class="dashboard-grid">

                <!-- =========================
                     SALES OVERVIEW
                ========================== -->

                <div class="card sales-card">

                    <div class="card-header">

                        <div>

                            <h2>
                                Sales Overview
                            </h2>

                            <p class="card-subtitle">
                                Daily laundry sales
                            </p>

                        </div>

                        <select class="filter">

                            <option>
                                Last 7 Days
                            </option>

                            <option>
                                Last 30 Days
                            </option>

                            <option>
                                This Year
                            </option>

                        </select>

                    </div>


                    <div class="sales-content">

                        <div class="sales-total">

                            <span>
                                Total Sales
                            </span>

                            <strong>
                                ₱{{ number_format($stats['income'] ?? 0, 2) }}
                            </strong>

                        </div>


                        <div class="chart">

                            @php

                                $salesBars = [
                                    35,
                                    55,
                                    50,
                                    78,
                                    60,
                                    88,
                                    100
                                ];

                            @endphp


                            @foreach($salesBars as $index => $height)

                                <div class="bar-wrap">

                                    <div class="bar-value">
                                        {{ $height }}%
                                    </div>

                                    <div
                                        class="bar"
                                        style="height: {{ $height }}%;"
                                    ></div>

                                    <div class="bar-label">
                                        {{ now()->subDays(6 - $index)->format('M d') }}
                                    </div>

                                </div>

                            @endforeach

                        </div>

                    </div>

                </div>


                <!-- =========================
                     ORDER STATUS
                ========================== -->

                <div class="card status-card">

                    <div class="card-header">

                        <div>

                            <h2>
                                Order Status
                            </h2>

                            <p class="card-subtitle">
                                Current order progress
                            </p>

                        </div>

                    </div>


                    <div class="status-container">

                        <div class="donut">

                            <div class="donut-center">

                                <strong>
                                    {{ $stats['orders'] ?? 0 }}
                                </strong>

                                <span>
                                    Total Orders
                                </span>

                            </div>

                        </div>


                        <div class="status-list">

                            <!-- PENDING -->

                            <div class="status-row">

                                <div class="status-name">

                                    <span class="status-dot pending"></span>

                                    <span>
                                        Pending
                                    </span>

                                </div>

                                <strong>
                                    {{ $stats['pending'] ?? 0 }}
                                </strong>

                            </div>


                            <!-- IN PROGRESS -->

                            <div class="status-row">

                                <div class="status-name">

                                    <span class="status-dot progress"></span>

                                    <span>
                                        In Progress
                                    </span>

                                </div>

                                <strong>
                                    {{ $stats['in_progress'] ?? 0 }}
                                </strong>

                            </div>


                            <!-- COMPLETED -->

                            <div class="status-row">

                                <div class="status-name">

                                    <span class="status-dot completed"></span>

                                    <span>
                                        Completed
                                    </span>

                                </div>

                                <strong>
                                    {{ $stats['completed'] ?? 0 }}
                                </strong>

                            </div>


                            <!-- CANCELLED -->

                            <div class="status-row">

                                <div class="status-name">

                                    <span class="status-dot cancelled"></span>

                                    <span>
                                        Cancelled
                                    </span>

                                </div>

                                <strong>
                                    {{ $stats['cancelled'] ?? 0 }}
                                </strong>

                            </div>

                        </div>

                    </div>

                </div>

            </div>


            <!-- =================================
                 RECENT ORDERS + RECENT ACTIVITY
            ================================== -->

            <div class="bottom-grid">

                <!-- =========================
                     RECENT ORDERS
                ========================== -->

                <div class="card recent-orders-card">

                    <div class="card-header">

                        <div>

                            <h2>
                                Recent Orders
                            </h2>

                            <p class="card-subtitle">
                                Latest laundry transactions
                            </p>

                        </div>

                        <a
                            href="{{ route('admin.orders.index') }}"
                            class="view-all"
                        >
                            View All
                        </a>

                    </div>


                    <div class="table-wrap">

                        <table class="orders-table">

                            <thead>

                                <tr>

                                    <th>
                                        ORDER
                                    </th>

                                    <th>
                                        CUSTOMER
                                    </th>

                                    <th>
                                        SERVICE
                                    </th>

                                    <th>
                                        STATUS
                                    </th>

                                    <th>
                                        TOTAL
                                    </th>

                                    <th>
                                        DATE
                                    </th>

                                </tr>

                            </thead>


                            <tbody>

                                @forelse($recentOrders ?? [] as $order)

                                    <tr>

                                        <td>

                                            <strong class="order-number">

                                                #LVE-{{
                                                    str_pad(
                                                        $order->id,
                                                        4,
                                                        '0',
                                                        STR_PAD_LEFT
                                                    )
                                                }}

                                            </strong>

                                        </td>


                                        <td>

                                            {{ $order->customer->name ?? 'N/A' }}

                                        </td>


                                        <td>

                                            {{ $order->service->service_name ?? 'N/A' }}

                                        </td>


                                        <td>

                                            @php

                                                $status = strtolower(
                                                    $order->status ?? 'pending'
                                                );

                                            @endphp


                                            @if($status === 'completed')

                                                <span class="status-badge badge-completed">
                                                    Completed
                                                </span>

                                            @elseif($status === 'in progress')

                                                <span class="status-badge badge-progress">
                                                    In Progress
                                                </span>

                                            @elseif($status === 'cancelled')

                                                <span class="status-badge badge-cancelled">
                                                    Cancelled
                                                </span>

                                            @else

                                                <span class="status-badge badge-pending">
                                                    Pending
                                                </span>

                                            @endif

                                        </td>


                                        <td>

                                            <strong>
                                                ₱{{ number_format($order->total ?? 0, 2) }}
                                            </strong>

                                        </td>


                                        <td>

                                            {{
                                                $order->order_date
                                                ? \Carbon\Carbon::parse(
                                                    $order->order_date
                                                )->format('M d, Y')
                                                : 'N/A'
                                            }}

                                        </td>

                                    </tr>


                                @empty

                                    <tr>

                                        <td
                                            colspan="6"
                                            class="empty-orders"
                                        >
                                            No recent orders found.
                                        </td>

                                    </tr>

                                @endforelse

                            </tbody>

                        </table>

                    </div>

                </div>


                <!-- =========================
                     RECENT ACTIVITY
                ========================== -->

                <div class="card">

                    <div class="card-header">

                        <div>

                            <h2>
                                Recent Activity
                            </h2>

                            <p class="card-subtitle">
                                Latest system updates
                            </p>

                        </div>

                    </div>


                    <div class="activity">

                        <!-- ACTIVITY 1 -->

                        <div class="activity-item">

                            <div class="activity-icon">

                                <i data-lucide="shopping-bag"></i>

                            </div>

                            <div>

                                <div class="activity-text">
                                    New order activity recorded.
                                </div>

                                <div class="activity-time">
                                    Today
                                </div>

                            </div>

                        </div>


                        <!-- ACTIVITY 2 -->

                        <div class="activity-item">

                            <div class="activity-icon">

                                <i data-lucide="user-plus"></i>

                            </div>

                            <div>

                                <div class="activity-text">
                                    Customer activity updated.
                                </div>

                                <div class="activity-time">
                                    Today
                                </div>

                            </div>

                        </div>


                        <!-- ACTIVITY 3 -->

                        <div class="activity-item">

                            <div class="activity-icon">

                                <i data-lucide="shirt"></i>

                            </div>

                            <div>

                                <div class="activity-text">
                                    Laundry service information updated.
                                </div>

                                <div class="activity-time">
                                    Recently
                                </div>

                            </div>

                        </div>


                        <!-- ACTIVITY 4 -->

                        <div class="activity-item">

                            <div class="activity-icon">

                                <i data-lucide="credit-card"></i>

                            </div>

                            <div>

                                <div class="activity-text">
                                    Payment records are being monitored.
                                </div>

                                <div class="activity-time">
                                    Recently
                                </div>

                            </div>

                        </div>

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