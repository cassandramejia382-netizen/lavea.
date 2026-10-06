<header class="lavea-admin-topbar">
    @if (auth()->user()->role === 'admin' || !empty($hideSearch))
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
            <button type="submit" aria-label="Search records">
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

        <div class="lavea-admin-profile">
            <div class="lavea-admin-avatar" style="flex:0 0 39px;width:39px;height:39px;min-width:39px;min-height:39px">
                <svg xmlns="http://www.w3.org/2000/svg" width="21" height="21" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" aria-hidden="true" style="display:block;flex:none;width:21px;height:21px">
                    <path d="M20 21v-2a4 4 0 0 0-4-4H8a4 4 0 0 0-4 4v2" />
                    <circle cx="12" cy="7" r="4" />
                </svg>
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

    </div>
</header>

<div class="lavea-admin-topbar-spacer" aria-hidden="true"></div>

<script>
    if (typeof lucide !== 'undefined') {
        lucide.createIcons();
    }

</script>

