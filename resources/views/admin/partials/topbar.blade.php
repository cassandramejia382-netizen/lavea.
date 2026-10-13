<header class="lavea-admin-topbar">
    <div class="lavea-topbar-brand">
        <button class="lavea-sidebar-toggle" type="button" aria-label="Toggle navigation" aria-controls="lavea-sidebar" aria-expanded="false">
            <svg width="22" height="22" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" aria-hidden="true"><path d="m14 6-6 6 6 6" /></svg>
        </button>
    </div>
    <script src="{{ asset('sidebar-navigation.js') }}?v={{ filemtime(public_path('sidebar-navigation.js')) }}" defer></script>
    <div class="lavea-search-spacer" aria-hidden="true"></div>

    <div class="lavea-top-right">
        @include('admin.partials.notifications')

        @if (in_array(auth()->user()->role, ['manager', 'admin', 'customer']))
            @php
                $accountRoutePrefix = in_array(auth()->user()->role, ['customer']) ? 'customer' : auth()->user()->role;
            @endphp
            <details class="staff-account-menu">
                <summary class="lavea-admin-profile" aria-label="{{ ucfirst(auth()->user()->role) }} account menu">
                    <div class="lavea-admin-avatar" style="flex:0 0 39px;width:39px;height:39px;min-width:39px;min-height:39px;overflow:hidden">
                        @if (auth()->user()->profile_photo_path)
                            <img src="{{ asset('storage/'.auth()->user()->profile_photo_path) }}" alt="" style="width:100%;height:100%;object-fit:cover">
                        @else
                            <svg xmlns="http://www.w3.org/2000/svg" width="21" height="21" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" aria-hidden="true" style="display:block;flex:none;width:21px;height:21px">
                                <path d="M20 21v-2a4 4 0 0 0-4-4H8a4 4 0 0 0-4 4v2" />
                                <circle cx="12" cy="7" r="4" />
                            </svg>
                        @endif
                    </div>
                    <div>
                        <strong>{{ auth()->user()->role === 'admin' ? 'Admin' : auth()->user()->name }}</strong>
                        <span>{{ auth()->user()->role === 'admin' ? 'System Administrator' : (auth()->user()->role === 'manager' ? 'Manager Account' : 'Customer Account') }}</span>
                    </div>
                    <i data-lucide="chevron-down"></i>
                </summary>
                <nav class="staff-account-dropdown" aria-label="{{ ucfirst(auth()->user()->role) }} account">
                    <a href="{{ route($accountRoutePrefix.'.profile') }}"><i data-lucide="user-round"></i><span>My Profile</span></a>
                    <a href="{{ route($accountRoutePrefix.'.settings') }}"><i data-lucide="settings"></i><span>Account Settings</span></a>
                    <form method="POST" action="{{ route('logout') }}" data-confirm-logout>
                        @csrf
                        <button class="logout-link" type="submit" style="display:flex;align-items:center;gap:10px;width:100%;padding:11px 12px;border:0;border-radius:7px;background:transparent;color:#a73545;font:inherit;font-size:13px;text-align:left;cursor:pointer"><i data-lucide="log-out"></i><span>Log Out</span></button>
                    </form>
                </nav>
            </details>
        @else
        <div class="lavea-admin-profile">
            <div class="lavea-admin-avatar" style="flex:0 0 39px;width:39px;height:39px;min-width:39px;min-height:39px">
                <svg xmlns="http://www.w3.org/2000/svg" width="21" height="21" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" aria-hidden="true" style="display:block;flex:none;width:21px;height:21px">
                    <path d="M20 21v-2a4 4 0 0 0-4-4H8a4 4 0 0 0-4 4v2" />
                    <circle cx="12" cy="7" r="4" />
                </svg>
            </div>
            <div>
                @if (in_array(auth()->user()->role, ['customer']))
                    <strong>{{ auth()->user()->name }}</strong>
                    <span>Customer Account</span>
                @elseif (auth()->user()->role === 'manager')
                    <strong>{{ auth()->user()->name }}</strong>
                    <span>Manager Account</span>
                @else
                    <strong>Admin</strong>
                    <span>System Administrator</span>
                @endif
            </div>
            <i data-lucide="chevron-down" class="lavea-account-arrow"></i>
        </div>
        @endif

    </div>
</header>

<div class="lavea-admin-topbar-spacer" aria-hidden="true"></div>

<script>
    if (typeof lucide !== 'undefined') {
        lucide.createIcons();
    }

    (() => {
        const accountMenu = document.querySelector('.lavea-admin-topbar .staff-account-menu');

        if (!accountMenu) {
            return;
        }

        document.addEventListener('click', (event) => {
            if (!accountMenu.contains(event.target)) {
                accountMenu.open = false;
            }
        });

        document.addEventListener('keydown', (event) => {
            if (event.key === 'Escape' && accountMenu.open) {
                accountMenu.open = false;
                accountMenu.querySelector('summary').focus();
            }
        });
    })();

</script>
