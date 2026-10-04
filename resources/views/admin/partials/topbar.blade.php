<header class="lavea-admin-topbar">
    @if (!empty($hideSearch))
        <div class="lavea-search-spacer" aria-hidden="true"></div>
    @elseif (!empty($customerSearch))
        <form class="lavea-search-box" method="GET" action="{{ route(auth()->user()->role === 'staff' ? 'staff.customers.index' : 'admin.customers.index') }}">
            <button type="submit" aria-label="Search customers">
                <i data-lucide="search"></i>
            </button>
            <input type="search" name="search" value="{{ request('search') }}" placeholder="Search here...">
        </form>
    @elseif (!empty($orderSearch))
        <form class="lavea-search-box" method="GET" action="{{ route(($orderRoutePrefix ?? 'admin.orders').'.index') }}">
            <button type="submit" aria-label="Search orders">
                <i data-lucide="search"></i>
            </button>
            <input type="search" name="search" value="{{ request('search') }}" placeholder="Search here...">
        </form>
    @elseif (!empty($paymentSearch))
        <form class="lavea-search-box" method="GET" action="{{ route(auth()->user()->role === 'staff' ? 'staff.payments.index' : 'admin.payments.index') }}">
            <button type="submit" aria-label="Search payments">
                <i data-lucide="search"></i>
            </button>
            <input type="search" name="search" value="{{ request('search') }}" placeholder="Search here...">
        </form>
    @else
        <div class="lavea-search-box">
            <i data-lucide="search"></i>
            <input type="text" placeholder="Search here...">
        </div>
    @endif

    <div class="lavea-top-right">
        @include('admin.partials.notifications')

        @if (in_array(auth()->user()->role, ['staff', 'customer', 'user']) || request()->routeIs('admin.dashboard'))
            <button type="button" class="lavea-notification-button" id="dashboard-theme-toggle" aria-label="Toggle dark mode">
                <i data-lucide="sun-moon"></i>
            </button>
        @endif

        <div class="lavea-admin-profile">
            <div class="lavea-admin-avatar">
                <i data-lucide="user"></i>
            </div>
            <div>
                @if (in_array(auth()->user()->role, ['customer', 'user']))
                    <strong>{{ auth()->user()->name }}</strong>
                    <span>Customer Account</span>
                @elseif (auth()->user()->role === 'staff')
                    <strong>{{ auth()->user()->name }}</strong>
                    <span>Staff Account</span>
                @else
                    <strong>Admin</strong>
                    <span>System Administrator</span>
                @endif
            </div>
            <i data-lucide="chevron-down"></i>
        </div>

        <form method="GET" action="{{ route('logout.confirm') }}">
            <button type="submit" class="lavea-sign-out">Sign Out</button>
        </form>
    </div>
</header>

<div class="lavea-admin-topbar-spacer" aria-hidden="true"></div>

<script>
    lucide.createIcons();

    var dashboardThemeToggle = document.getElementById('dashboard-theme-toggle');

    if (dashboardThemeToggle) {
        dashboardThemeToggle.addEventListener('click', function () {
            var theme = document.documentElement.dataset.laveaTheme === 'dark' ? 'light' : 'dark';
            document.cookie = 'lavea_theme=' + theme + '; path=/; max-age=31536000; SameSite=Lax';
            window.location.reload();
        });
    }
</script>

