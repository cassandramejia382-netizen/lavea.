<!doctype html>
<html lang="en" data-lavea-theme="{{ request()->cookie('lavea_theme', 'light') }}">
<head>
    @include('partials.theme-assets')
    <meta charset="utf-8"><meta name="viewport" content="width=device-width, initial-scale=1">
    <title>{{ $title ?? 'Staff Portal' }} | LAVEA</title>
    <script src="https://unpkg.com/lucide@latest"></script>
    <link rel="stylesheet" href="{{ asset('css/admin-topbar.css') }}">
    <style>
        *{box-sizing:border-box}body{margin:0;background:#f5f7fb;color:#172554;font:14px Arial,Helvetica,sans-serif}.dashboard{display:flex;min-height:100vh}.sidebar{width:255px;background:#111d38;color:white;padding:25px 16px;position:fixed;inset:0 auto 0 0;display:flex;flex-direction:column}.logo-area{padding:5px 15px 35px}.logo{font-size:27px;font-weight:700;letter-spacing:3px}.logo-subtitle{font-size:11px;color:#aebbd3;margin-top:4px}.menu{display:flex;flex-direction:column;gap:6px}.menu a{color:#dce5f7;text-decoration:none;display:flex;align-items:center;gap:14px;padding:13px 15px;border-radius:8px}.menu a:hover,.menu a.active{background:#4169c8;color:white}.menu svg{width:19px;height:19px}.staff-profile{margin-top:auto;border-top:1px solid #2a3b5d;padding:20px 5px 5px;display:flex;align-items:center;gap:12px}.staff-avatar{width:40px;height:40px;background:#e7edf9;color:#26395e;border-radius:50%;display:grid;place-items:center;flex-shrink:0}.staff-info strong{display:block;font-size:13px}.staff-info span{display:block;color:#9eabc2;font-size:10px;margin-top:3px}.main{margin-left:255px;width:calc(100% - 255px);min-height:100vh}.lavea-admin-topbar{left:255px}.lavea-top-right{gap:14px}.content{padding:30px}.page-head{display:flex;align-items:center;justify-content:space-between;margin:0 0 22px}.page-head h1{margin:0 0 6px;font-size:26px;color:#10204a}.muted{color:#7a879d}.card,.stat-card{background:#fff;border:1px solid #e7ebf2;border-radius:10px;padding:20px}.grid{display:grid;grid-template-columns:repeat(4,minmax(0,1fr));gap:17px;margin-bottom:20px}.stat-card strong{display:block;font-size:27px;margin-top:10px;color:#12214a}.cols{display:grid;grid-template-columns:2fr 1fr;gap:17px;margin-bottom:18px}.card h2{font-size:16px;margin:0 0 17px;color:#12214a}.table-wrap{overflow-x:auto}table{width:100%;border-collapse:collapse;min-width:600px}th,td{text-align:left;padding:12px 10px;border-bottom:1px solid #edf0f5;font-size:13px}th{font-size:11px;text-transform:uppercase;color:#687691}td{color:#34415a}.btn{display:inline-flex;align-items:center;gap:6px;background:#4169c8;color:#fff;padding:10px 14px;border:0;border-radius:7px;text-decoration:none;cursor:pointer;font-weight:600}.btn.secondary{background:#fff;color:#34415a;border:1px solid #dfe5ef}.btn.danger{background:#a73545}.form-grid{display:grid;grid-template-columns:1fr 1fr;gap:17px}.field{display:flex;flex-direction:column;gap:7px}.field.full{grid-column:1/-1}.field label{font-weight:600;font-size:13px;color:#34415a}.field input,.field select,.field textarea,.search-input{border:1px solid #dfe5ef;background:white;border-radius:7px;padding:11px;color:#263550;width:100%}.field textarea{min-height:90px}.actions{display:flex;gap:8px;align-items:center}.flash{margin-bottom:17px;padding:12px 15px;background:#e5f8ef;color:#168458;border-radius:7px}.error-list{color:#b42335;margin:0 0 14px;padding-left:20px}.service-grid{display:grid;grid-template-columns:repeat(3,minmax(0,1fr));gap:17px}.service-card{overflow:hidden;padding:0}.service-card img{width:100%;height:160px;object-fit:cover}.service-card .service-body{padding:17px}.pill{display:inline-block;padding:5px 9px;border-radius:12px;background:#e7efff;color:#3459b1;font-size:11px}.empty{color:#7a879d;padding:18px;text-align:center}.print-only{display:none}
        @media(max-width:900px){.sidebar{width:70px;padding:20px 8px}.logo-area{padding:5px 4px 25px}.logo{font-size:16px;text-align:center}.logo-subtitle,.menu span,.staff-info{display:none}.menu a{justify-content:center;padding:13px 6px}.staff-profile{justify-content:center;padding:14px 0}.staff-avatar{width:38px;height:38px}.main{margin-left:70px;width:calc(100% - 70px)}.lavea-admin-topbar{left:70px;padding:0 12px}.grid{grid-template-columns:repeat(2,1fr)}.cols{grid-template-columns:1fr}.service-grid{grid-template-columns:repeat(2,1fr)}.lavea-search-box,.lavea-search-spacer{flex-basis:200px;width:200px}}
        @media(max-width:600px){.content{padding:18px}.grid,.form-grid,.service-grid{grid-template-columns:1fr}.field.full{grid-column:auto}.lavea-admin-topbar{height:64px}.lavea-admin-topbar-spacer{height:64px}.lavea-search-box,.lavea-search-spacer{display:none}.lavea-top-right{gap:8px}.lavea-admin-profile div:nth-child(2),.lavea-admin-profile>svg:last-child{display:none}.lavea-sign-out{padding:8px}.page-head{align-items:flex-start;gap:12px}}
        @media print{.sidebar,.lavea-admin-topbar,.lavea-admin-topbar-spacer,.no-print{display:none!important}.main{margin:0;width:100%}.content{padding:0}.print-only{display:block}}
    </style>
    @stack('styles')
</head>
<body>
<div class="dashboard">
    <aside class="sidebar"><div class="logo-area"><div class="logo">LAVEA</div><div class="logo-subtitle">Laundry Made Easy</div></div>
        <nav class="menu">
            @php
                $isCustomerPortal = ($portal ?? 'staff') === 'customer';
                $navigation = $isCustomerPortal
                    ? [['dashboard','layout-dashboard','Dashboard'],['orders','clipboard-list','My Orders'],['services','package','Services'],['payments','credit-card','Payments'],['profile','user-round','Profile']]
                    : [['dashboard','layout-dashboard','Dashboard'],['customers','users','Customers'],['services','package','Services'],['orders','clipboard-list','Records'],['payments','credit-card','Payments'],['schedules','calendar-days','Schedule']];
                $portalPrefix = $isCustomerPortal ? 'customer' : 'staff';
                $navigation[] = ['settings', 'settings', 'Settings'];
            @endphp
            @foreach ($navigation as [$key,$icon,$label])
                @php
                    $navigationRoute = in_array($key, ['dashboard', 'profile', 'settings']) ? $portalPrefix.'.'.$key : $portalPrefix.'.'.$key.'.index';
                    $active = request()->routeIs($navigationRoute, $portalPrefix.'.'.$key.'.*');
                @endphp
                <a href="{{ route($navigationRoute) }}" class="{{ $active ? 'active' : '' }}" aria-label="{{ $label }}" @if($active) aria-current="page" @endif><i data-lucide="{{ $icon }}"></i><span>{{ $label }}</span></a>
            @endforeach
            <a href="{{ route('logout.confirm') }}" aria-label="Sign Out"><i data-lucide="log-out"></i><span>Sign Out</span></a>
        </nav>
        @if ($isCustomerPortal)
            <div class="staff-profile"><div class="staff-avatar"><i data-lucide="user-round"></i></div><div class="staff-info"><strong>{{ auth()->user()->name }}</strong><span>Customer Account</span></div></div>
        @endif
    </aside>
    <main class="main">
        @php
            $customerSearch = request()->routeIs('staff.customers.*');
            $orderSearch = request()->routeIs('staff.orders.*');
            $paymentSearch = request()->routeIs('staff.payments.*');
            $hideSearch = request()->routeIs('staff.customers.index', 'staff.orders.index', 'staff.payments.index')
                || ! ($customerSearch || $orderSearch || $paymentSearch);
            $orderRoutePrefix = 'staff.orders';
        @endphp
        @include('admin.partials.topbar')
        <section class="content">
            @if (session('success'))<div class="flash">{{ session('success') }}</div>@endif
            @if (session('error'))<div class="flash flash-error" style="background:#ffe9ec;color:#b42335">{{ session('error') }}</div>@endif
            @if ($errors->any())<ul class="error-list">@foreach ($errors->all() as $error)<li>{{ $error }}</li>@endforeach</ul>@endif
            @yield('content')
        </section>
    </main>
</div>
<script>
    lucide.createIcons();
</script>
</body></html>
