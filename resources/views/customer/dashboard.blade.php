@extends('customer.layout', ['title' => 'Dashboard'])
@push('styles')
<style>
.customer-overview {
    --overview-border: #e4e9f2;
    --overview-muted: #78869d;
    --overview-ink: #172554;
}
.customer-overview .overview-heading {
    display: flex;
    align-items: center;
    justify-content: space-between;
    gap: 18px;
    margin-bottom: 24px;
}
.customer-overview .overview-heading h1 {
    margin: 0 0 7px;
    font-size: 26px;
    letter-spacing: -.7px;
    color: var(--overview-ink);
}
.customer-overview .overview-heading p {
    margin: 0;
    color: var(--overview-muted);
    font-size: 13px;
    line-height: 1.6;
}
.customer-overview .overview-date {
    display: flex;
    align-items: center;
    gap: 9px;
    padding: 9px 12px;
    border: 1px solid var(--overview-border);
    border-radius: 10px;
    background: #fff;
    color: var(--overview-muted);
    font-size: 11px;
    white-space: nowrap;
}
.customer-overview .overview-date svg {
    width: 15px;
    height: 15px;
}
.customer-overview .overview-stats {
    display: grid;
    grid-template-columns: repeat(4,minmax(0,1fr));
    gap: 16px;
    margin-bottom: 24px;
}
.customer-overview .overview-stat {
    padding: 18px;
    border: 1px solid var(--overview-border);
    border-radius: 14px;
    background: #fff;
    box-shadow: 0 3px 12px rgb(23 37 84 / 3%);
    min-width: 0;
}
.customer-overview .stat-top {
    display: flex;
    align-items: center;
    justify-content: space-between;
    gap: 10px;
    margin-bottom: 16px;
}
.customer-overview .stat-symbol {
    display: grid;
    place-items: center;
    width: 36px;
    height: 36px;
    border-radius: 10px;
    background: #edf2ff;
    color: #4169c8;
}
.customer-overview .stat-symbol svg {
    width: 19px;
    height: 19px;
}
.customer-overview .stat-caption {
    color: var(--overview-muted);
    font-size: 10px;
}
.customer-overview .overview-stat[data-kind="pending"] .stat-symbol {
    background: #fff5e6;
    color: #b7791f;
}
.customer-overview .overview-stat[data-kind="completed"] .stat-symbol {
    background: #edf8f2;
    color: #29815b;
}
.customer-overview .overview-stat[data-kind="payments"] .stat-symbol {
    background: #f1edff;
    color: #7852c9;
}
.customer-overview .stat-label {
    display: block;
    color: var(--overview-muted);
    font-size: 12px;
    margin-bottom: 7px;
}
.customer-overview .stat-value {
    display: block;
    font-size: 25px;
    letter-spacing: -.6px;
    line-height: 1.2;
    color: var(--overview-ink);
    overflow-wrap: anywhere;
}
.customer-overview .overview-panel {
    padding: 0;
    border: 1px solid var(--overview-border);
    border-radius: 16px;
    background: #fff;
    overflow: hidden;
    margin-bottom: 26px;
    box-shadow: 0 3px 12px rgb(23 37 84 / 3%);
}
.customer-overview .panel-heading {
    display: flex;
    align-items: center;
    justify-content: space-between;
    gap: 14px;
    padding: 20px;
}
.customer-overview .panel-heading h2,.customer-overview .services-heading h2 {
    margin: 0 0 5px;
    color: var(--overview-ink);
    font-size: 17px;
    letter-spacing: -.2px;
}
.customer-overview .panel-heading p,.customer-overview .services-heading p {
    margin: 0;
    color: var(--overview-muted);
    font-size: 12px;
    line-height: 1.5;
}
.customer-overview .overview-link {
    display: inline-flex;
    align-items: center;
    gap: 7px;
    color: #4169c8;
    font-size: 12px;
    font-weight: 600;
    text-decoration: none;
    white-space: nowrap;
}
.customer-overview .overview-link svg {
    width: 15px;
    height: 15px;
}
.customer-overview .overview-link:hover {
    text-decoration: underline;
}
.customer-overview .overview-panel th {
    background: #f8fafd;
    padding: 12px 20px;
    color: var(--overview-muted);
    font-size: 10px;
    text-transform: uppercase;
    letter-spacing: .4px;
}
.customer-overview .overview-panel td {
    padding: 16px 20px;
    font-size: 12px;
    border-color: #edf0f5;
}
.customer-overview .overview-panel td:first-child a {
    color: #4169c8;
    font-weight: 600;
    text-decoration: none;
}
.customer-overview .overview-panel td:first-child a:hover {
    text-decoration: underline;
}
.customer-overview .overview-panel td:nth-child(6) {
    font-weight: 600;
    color: var(--overview-ink);
    white-space: nowrap;
}
.customer-overview .overview-panel tbody tr:hover {
    background: #fbfcff;
}
.customer-overview .overview-panel .pill {
    padding: 5px 9px;
    font-size: 10px;
    border-radius: 7px;
}
.customer-overview .pill[data-status="Completed"],.customer-overview .pill[data-status="Paid"] {
    background: #edf8f2;
    color: #29815b;
}
.customer-overview .pill[data-status="Pending"],.customer-overview .pill[data-status="Unpaid"] {
    background: #fff5e6;
    color: #a66a17;
}
.customer-overview .pill[data-status="Cancelled"] {
    background: #fff1f2;
    color: #be123c;
}
.customer-overview .services-heading {
    display: flex;
    align-items: center;
    justify-content: space-between;
    gap: 14px;
    margin-bottom: 16px;
}
html[data-lavea-theme="dark"] .customer-overview {
    --overview-border: #34415a;
    --overview-muted: #b6c2d3;
    --overview-ink: #e7edf9;
}
html[data-lavea-theme="dark"] .customer-overview .overview-stat,html[data-lavea-theme="dark"] .customer-overview .overview-date {
    background: #1f2937;
}
html[data-lavea-theme="dark"] .customer-overview .stat-symbol {
    background: #263a60;
    color: #bfdbfe !important;
}
html[data-lavea-theme="dark"] .customer-overview .overview-stat[data-kind="pending"] .stat-symbol {
    background: #493516;
    color: #fde68a !important;
}
html[data-lavea-theme="dark"] .customer-overview .overview-stat[data-kind="completed"] .stat-symbol {
    background: #123a2c;
    color: #a7f3d0 !important;
}
html[data-lavea-theme="dark"] .customer-overview .overview-stat[data-kind="payments"] .stat-symbol {
    background: #352653;
    color: #ddd6fe !important;
}
@media(max-width:1100px) {
    .customer-overview .overview-stats {
        grid-template-columns: repeat(2,minmax(0,1fr));
    }
}
@media(max-width:600px) {
    .customer-overview .overview-heading {
        align-items: flex-start;
        flex-direction: column;
        gap: 12px;
    }
    .customer-overview .overview-heading h1 {
        font-size: 23px;
    }
    .customer-overview .overview-stats {
        gap: 10px;
    }
    .customer-overview .overview-stat {
        padding: 14px;
    }
    .customer-overview .stat-caption {
        display: none;
    }
    .customer-overview .stat-value {
        font-size: 22px;
    }
    .customer-overview .panel-heading {
        padding: 16px;
    }
    .customer-overview .overview-panel th,.customer-overview .overview-panel td {
        padding-left: 14px;
        padding-right: 14px;
    }
}
</style>
@endpush
@section('content')
<div class="customer-overview">
    <header class="overview-heading">
        <div><h1>Welcome, {{ auth()->user()->name }}</h1><p>Track your laundry, payments, and services in one place.</p></div>
        <div class="overview-date"><i data-lucide="calendar-days" aria-hidden="true"></i>{{ now()->format('F d, Y') }}</div>
    </header>
    <div class="overview-stats">
        @foreach ([['Total Orders', $stats['total'], 'clipboard-list', 'total', 'All orders'], ['Pending Orders', $stats['pending'], 'clock-3', 'pending', 'Awaiting service'], ['Completed Orders', $stats['completed'], 'circle-check', 'completed', 'Finished laundry'], ['Total Payments', "\u{20B1}".number_format($stats['payments'], 2), 'credit-card', 'payments', 'Completed payments']] as [$label, $value, $icon, $kind, $caption])
            <div class="overview-stat" data-kind="{{ $kind }}">
                <div class="stat-top"><span class="stat-symbol"><i data-lucide="{{ $icon }}" aria-hidden="true"></i></span><span class="stat-caption">{{ $caption }}</span></div>
                <span class="stat-label">{{ $label }}</span><strong class="stat-value">{{ $value }}</strong>
            </div>
        @endforeach
    </div>
    <section class="overview-panel">
        <div class="panel-heading"><div><h2>Recent Orders</h2><p>Your latest laundry orders at a glance.</p></div><a class="overview-link" href="{{ route('customer.orders.index') }}">View all <i data-lucide="arrow-up-right" aria-hidden="true"></i></a></div>
        @include('customer.orders-table', ['orders' => $recentOrders])
    </section>
    <div class="services-heading"><div><h2>Available Services</h2><p>Fresh, clean laundry starts here.</p></div><a class="overview-link" href="{{ route('customer.services.index') }}">View all <i data-lucide="arrow-up-right" aria-hidden="true"></i></a></div>
    @include('customer.services-grid')
</div>
@endsection
