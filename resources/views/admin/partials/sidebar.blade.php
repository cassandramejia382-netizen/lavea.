<aside class="sidebar">
    <div class="logo-area">
        <div class="logo">LAVEA</div>
        <div class="logo-subtitle">Laundry Made Easy</div>
    </div>
    @php
        $navigationPrefix = auth()->user()->role === 'staff' ? 'staff' : 'admin';
        $navigationItems = [
            ['dashboard', 'Dashboard', 'layout-dashboard', 'dashboard'],
            ['customers.index', 'Customers', 'users', 'customers.*'],
            ['staff.index', 'Staff', 'user-round-cog', 'staff.*'],
            ['services.index', 'Services', 'package', 'services.*'],
            ['orders.index', 'Orders', 'clipboard-list', 'orders.*'],
            ['payments.index', 'Payments / Transactions', 'credit-card', 'payments.*'],
            ['schedules.index', 'Schedule', 'calendar-days', 'schedules.*'],
            ['reports', 'Reports', 'bar-chart-3', 'reports'],
            ['settings', 'Settings', 'settings', 'settings*'],
        ];
    @endphp
    <nav class="menu" aria-label="{{ $navigationPrefix === 'admin' ? 'Admin' : 'Staff' }} navigation">
        @foreach ($navigationItems as [$navigationRoute, $navigationLabel, $navigationIcon, $navigationPattern])
            @if ($navigationPrefix === 'admin' || ! in_array($navigationRoute, ['staff.index', 'reports']))
                <a href="{{ route($navigationPrefix.'.'.$navigationRoute) }}" @class(['active' => request()->routeIs($navigationPrefix.'.'.$navigationPattern)]) aria-label="{{ $navigationLabel }}" @if (request()->routeIs($navigationPrefix.'.'.$navigationPattern)) aria-current="page" @endif>
                    <i data-lucide="{{ $navigationIcon }}"></i>
                    <span>{{ $navigationLabel }}</span>
                </a>
            @endif
        @endforeach
        <a href="{{ route('logout.confirm') }}" aria-label="Sign Out">
            <i data-lucide="log-out"></i>
            <span>Sign Out</span>
        </a>
    </nav>
</aside>
