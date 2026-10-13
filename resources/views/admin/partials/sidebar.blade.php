<aside class="sidebar">
    <div class="logo-area">
        @include('partials.lavea-logo')
    </div>
    @php
        $navigationPrefix = auth()->user()->role === 'manager' ? 'manager' : 'admin';
        $navigationItems = [
            ['dashboard', 'Dashboard', 'layout-dashboard', 'dashboard'],
            ['customers.index', 'Customers', 'users', 'customers.*'],
            ['staff.index', 'Staff', 'users', 'staff.*'],
            ['services.index', 'Services', 'package', 'services.*'],
            ['orders.index', 'Records', 'clipboard-list', 'orders.*'],
            ['payments.index', 'Payments / Transactions', 'credit-card', 'payments.*'],
            ['schedules.index', 'Schedule', 'calendar-days', 'schedules.*'],
            ['reports', 'Reports', 'bar-chart-3', 'reports'],
            ['profile', 'My Profile', 'user-round', 'profile'],
            ['settings', 'Settings', 'settings', 'settings*'],
        ];
    @endphp
    <nav class="menu" aria-label="{{ $navigationPrefix === 'admin' ? 'Admin' : 'Manager' }} navigation">
        @foreach ($navigationItems as [$navigationRoute, $navigationLabel, $navigationIcon, $navigationPattern])
            @if (($navigationPrefix === 'admin' && $navigationRoute !== 'schedules.index') || ($navigationPrefix === 'manager' && ! in_array($navigationRoute, ['managers.index', 'reports'])))
                <a href="{{ route($navigationPrefix.'.'.$navigationRoute) }}" @class(['active' => request()->routeIs($navigationPrefix.'.'.$navigationPattern)]) aria-label="{{ $navigationLabel }}" @if (request()->routeIs($navigationPrefix.'.'.$navigationPattern)) aria-current="page" @endif>
                    <i data-lucide="{{ $navigationIcon }}"></i>
                    <span>{{ $navigationRoute === 'settings' ? 'Account Settings' : $navigationLabel }}</span>
                </a>
            @endif
        @endforeach
    </nav>
</aside>
