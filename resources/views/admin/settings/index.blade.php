<!DOCTYPE html>
<html lang="en">
<head>
    @include('partials.theme-assets')
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">

    <title>Settings - LAVEA</title>

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
            width: calc(100% - 255px);
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
            color: #111827;
            margin-bottom: 6px;
        }

        .page-header p {
            color: #6b7280;
            font-size: 14px;
        }

        /* SETTINGS CARD */
        .settings-card {
            background: white;
            border: 1px solid #e5e7eb;
            border-radius: 14px;
            padding: 26px;
            box-shadow: 0 5px 18px rgba(17, 29, 56, 0.04);
        }

        .settings-layout {
            display: grid;
            grid-template-columns: 220px minmax(0, 1fr);
            gap: 22px;
            max-width: 1200px;
        }

        .settings-nav {
            position: sticky;
            top: 95px;
            height: fit-content;
            padding: 18px 12px;
            border: 1px solid #e5e7eb;
            border-radius: 12px;
            background: white;
        }

        .settings-nav h3 {
            padding: 4px 10px 12px;
            color: #8490a3;
            font-size: 11px;
            letter-spacing: .7px;
            text-transform: uppercase;
        }

        .settings-nav a {
            display: flex;
            align-items: center;
            gap: 10px;
            padding: 11px 10px;
            border-radius: 8px;
            color: #46536a;
            font-size: 13px;
            text-decoration: none;
        }

        .settings-nav a:hover {
            background: #f0f4fb;
            color: #244b9a;
        }

        .settings-nav svg {
            width: 17px;
            height: 17px;
        }

        .settings-content {
            display: grid;
            gap: 16px;
            min-width: 0;
        }

        .settings-card h3 {
            font-size: 17px;
            color: #111827;
            margin-bottom: 6px;
        }

        .settings-card > p {
            font-size: 13px;
            color: #6b7280;
            margin-bottom: 25px;
        }

        .form-group {
            margin-bottom: 18px;
        }

        .form-group label {
            display: block;
            font-size: 13px;
            font-weight: 600;
            color: #374151;
            margin-bottom: 7px;
        }

        .form-group input {
            width: 100%;
            padding: 11px 12px;
            border: 1px solid #d1d5db;
            border-radius: 7px;
            outline: none;
            font-size: 14px;
        }

        .form-group textarea {
            width: 100%;
            min-height: 90px;
            padding: 11px 12px;
            border: 1px solid #d1d5db;
            border-radius: 7px;
            outline: none;
            font-size: 14px;
            resize: vertical;
        }

        .form-group input:focus {
            border-color: #4169c8;
        }

        .btn {
            background: #4169c8;
            color: white;
            border: none;
            padding: 10px 18px;
            border-radius: 7px;
            font-size: 14px;
            cursor: pointer;
        }

        .btn:hover {
            background: #3558ad;
        }

        .setting-message {
            max-width: 800px;
            padding: 12px 15px;
            margin-bottom: 16px;
            border-radius: 8px;
            background: #e6f8ef;
            color: #168458;
            font-size: 13px;
        }

        .field-error {
            margin-top: 5px;
            color: #b42318;
            font-size: 12px;
        }

        .theme-options {
            display: flex;
            width: fit-content;
            gap: 6px;
            padding: 5px;
            margin: 14px 0 10px;
            border: 1px solid #e1e6ef;
            border-radius: 10px;
            background: #f5f7fb;
        }

        .theme-form {
            margin: 0;
        }

        .theme-button {
            display: flex;
            align-items: center;
            justify-content: center;
            gap: 9px;
            min-width: 135px;
            padding: 11px 16px;
            border: 0;
            border-radius: 7px;
            background: transparent;
            color: #526078;
            font-size: 14px;
            font-weight: 600;
            cursor: pointer;
        }

        .theme-button svg {
            width: 17px;
            height: 17px;
        }

        .theme-button.active {
            background: white;
            color: #244b9a;
            box-shadow: 0 2px 6px rgba(17, 29, 56, 0.12);
        }

        .theme-button:hover:not(.active) {
            background: #e9eef8;
        }

        .theme-button:focus-visible {
            outline: 2px solid #4169c8;
            outline-offset: 2px;
        }

        .theme-status {
            margin: 12px 0 0;
            color: #718099;
            font-size: 12px;
        }

        @media (max-width: 900px) {
            .settings-layout {
                grid-template-columns: 1fr;
            }

            .settings-nav {
                position: static;
            }

            .settings-nav-links {
                display: grid;
                grid-template-columns: repeat(2, minmax(0, 1fr));
            }
        }

        @media (max-width: 600px) {
            .settings-card {
                padding: 20px;
            }

            .theme-options {
                width: 100%;
            }

            .theme-form {
                flex: 1;
            }

            .theme-button {
                width: 100%;
                min-width: 0;
                padding: 10px;
            }
        }

        @media (max-width: 800px) {
            .main {
                margin-left: 70px;
                width: calc(100% - 70px);
                padding: 30px;
            }

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

            <a href="{{ route('admin.settings') }}" class="active">
                <i data-lucide="settings"></i>
                <span>Settings</span>
            </a>

        </nav>

    </aside>

    <!-- TOPBAR -->
    @include('admin.partials.topbar', ['hideSearch' => true])

    <!-- MAIN -->
    <main class="main">

        <div class="page-header">
            <div>
                <h2>Settings</h2>
                <p>Manage your LAVEA system settings.</p>
            </div>
            @include('admin.partials.current-date')
        </div>

        @if (session('shop_success'))
            <div class="setting-message">{{ session('shop_success') }}</div>
        @endif
        @if (session('profile_success'))
            <div class="setting-message">{{ session('profile_success') }}</div>
        @endif
        @if (session('password_success'))
            <div class="setting-message">{{ session('password_success') }}</div>
        @endif
        @if (session('theme_success'))
            <div class="setting-message">{{ session('theme_success') }}</div>
        @endif

        <div class="settings-layout">
            <nav class="settings-nav" aria-label="Settings sections">
                <h3>Settings Menu</h3>
                <div class="settings-nav-links">
                    <a href="#shop-information"><i data-lucide="store"></i>Shop Information</a>
                    <a href="#admin-profile"><i data-lucide="user-round"></i>Admin Profile</a>
                    <a href="#change-password"><i data-lucide="lock-keyhole"></i>Change Password</a>
                    <a href="#appearance"><i data-lucide="palette"></i>Appearance</a>
                </div>
            </nav>

            <div class="settings-content">
                <div class="settings-card" id="shop-information">
                    <h3>Shop Information</h3>
                    <p>Update your laundry shop contact information.</p>

                    <form method="POST" action="{{ route('admin.settings.shop') }}">
                        @csrf
                        <div class="form-group">
                            <label for="shop_name">Shop Name</label>
                            <input id="shop_name" name="shop_name" type="text" value="{{ old('shop_name', $shopSettings->shop_name ?? 'LAVEA Laundry Shop') }}" required>
                            @error('shop_name')<div class="field-error">{{ $message }}</div>@enderror
                        </div>
                        <div class="form-group">
                            <label for="contact_number">Contact Number</label>
                            <input id="contact_number" name="contact_number" type="text" value="{{ old('contact_number', $shopSettings->contact_number ?? '') }}" placeholder="Enter contact number">
                            @error('contact_number')<div class="field-error">{{ $message }}</div>@enderror
                        </div>
                        <div class="form-group">
                            <label for="address">Shop Address</label>
                            <textarea id="address" name="address" placeholder="Enter shop address">{{ old('address', $shopSettings->address ?? '') }}</textarea>
                            @error('address')<div class="field-error">{{ $message }}</div>@enderror
                        </div>
                        <button class="btn" type="submit">Save Shop Information</button>
                    </form>
                </div>

                <div class="settings-card" id="admin-profile">
                    <h3>Admin Profile</h3>
                    <p>Update the name and email used for your Admin account.</p>

                    <form method="POST" action="{{ route('admin.settings.profile') }}">
                        @csrf
                        <div class="form-group">
                            <label for="admin_name">Name</label>
                            <input id="admin_name" name="name" type="text" value="{{ old('name', auth()->user()->name) }}" required>
                            @error('name')<div class="field-error">{{ $message }}</div>@enderror
                        </div>
                        <div class="form-group">
                            <label for="admin_email">Email</label>
                            <input id="admin_email" name="email" type="email" value="{{ old('email', auth()->user()->email) }}" required>
                            @error('email')<div class="field-error">{{ $message }}</div>@enderror
                        </div>
                        <button class="btn" type="submit">Save Admin Profile</button>
                    </form>
                </div>

                <div class="settings-card" id="change-password">
                    <h3>Change Password</h3>
                    <p>Enter your current password and choose a new password.</p>

                    <form method="POST" action="{{ route('admin.settings.password') }}">
                        @csrf
                        <div class="form-group">
                            <label for="current_password">Current Password</label>
                            <input id="current_password" name="current_password" type="password" autocomplete="current-password" required>
                            @error('current_password')<div class="field-error">{{ $message }}</div>@enderror
                        </div>
                        <div class="form-group">
                            <label for="new_password">New Password</label>
                            <input id="new_password" name="password" type="password" autocomplete="new-password" required>
                            @error('password')<div class="field-error">{{ $message }}</div>@enderror
                        </div>
                        <div class="form-group">
                            <label for="password_confirmation">Confirm New Password</label>
                            <input id="password_confirmation" name="password_confirmation" type="password" autocomplete="new-password" required>
                        </div>
                        <button class="btn" type="submit">Change Password</button>
                    </form>
                </div>

                <div class="settings-card" id="appearance">
                    <h3>Appearance</h3>
                    <p>Choose Light Mode or Dark Mode for LAVEA. Your choice also applies to the Login page.</p>
                    <div class="theme-options" role="group" aria-label="Appearance theme">
                        <form class="theme-form" method="POST" action="{{ route('admin.settings.appearance') }}">
                            @csrf
                            <input type="hidden" name="theme" value="light">
                            <button class="theme-button {{ request()->cookie('lavea_theme', 'light') === 'light' ? 'active' : '' }}" type="submit">
                                <i data-lucide="sun"></i> Light Mode
                            </button>
                        </form>
                        <form class="theme-form" method="POST" action="{{ route('admin.settings.appearance') }}">
                            @csrf
                            <input type="hidden" name="theme" value="dark">
                            <button class="theme-button {{ request()->cookie('lavea_theme', 'light') === 'dark' ? 'active' : '' }}" type="submit">
                                <i data-lucide="moon"></i> Dark Mode
                            </button>
                        </form>
                    </div>
                    <p class="theme-status">{{ request()->cookie('lavea_theme', 'light') === 'dark' ? 'Dark Mode is selected.' : 'Light Mode is selected.' }}</p>
                </div>
            </div>
        </div>

    </main>

    <script>
        lucide.createIcons();
    </script>

</body>
</html>

