@extends('staff.layout', ['title' => 'Dashboard'])

@section('content')
<style>
    .staff-overview { max-width:1320px;margin:0 auto; }
    .staff-overview .overview-header { display:flex;align-items:center;justify-content:space-between;gap:20px;margin-bottom:24px; }
    .staff-overview .overview-eyebrow { display:block;margin-bottom:7px;color:#4169c8;font-size:10px;font-weight:700;letter-spacing:1.4px;text-transform:uppercase; }
    .staff-overview .overview-header h1 { margin:0 0 7px;font-size:26px;letter-spacing:-.7px;color:#142442; }
    .staff-overview .overview-header p { margin:0;color:#78869d;font-size:13px;line-height:1.6; }
    .staff-overview .overview-date { display:flex;align-items:center;gap:10px;padding:10px 14px;border:1px solid #e4e9f2;border-radius:10px;background:#fff;color:#526987;font-size:12px;white-space:nowrap; }
    .staff-overview .overview-date svg { width:17px;height:17px;color:#4169c8; }
    .staff-overview .overview-stats { display:grid;grid-template-columns:repeat(4,minmax(0,1fr));gap:14px;margin-bottom:24px; }
    .staff-overview .overview-stat { padding:18px;border:1px solid #e4e9f2;border-radius:14px;background:#fff;box-shadow:0 3px 12px #17255405; }
    .staff-overview .overview-stat-top { display:flex;align-items:center;justify-content:space-between;gap:8px;margin-bottom:16px; }
    .staff-overview .overview-stat-label { color:#6c7c94;font-size:12px;font-weight:500; }
    .staff-overview .overview-stat-icon { display:grid;place-items:center;width:34px;height:34px;border-radius:10px;background:var(--stat-bg);color:var(--stat-color); }
    .staff-overview .overview-stat-icon svg { width:18px;height:18px; }
    .staff-overview .overview-stat strong { display:block;color:#172554;font-size:30px;line-height:1;letter-spacing:-1px; }
    .staff-overview .overview-stat small { display:block;margin-top:9px;color:#8995aa;font-size:11px; }
    .staff-overview .overview-panel { min-width:0;overflow:hidden;padding:0;border:1px solid #e4e9f2;border-radius:14px;background:#fff;box-shadow:0 3px 12px #17255405; }
    .staff-overview .overview-panel-head { display:flex;align-items:center;justify-content:space-between;flex-wrap:wrap;gap:12px;padding:19px 20px;border-bottom:1px solid #edf0f5; }
    .staff-overview .overview-panel-head h2 { margin:0 0 4px;font-size:16px;color:#172554;letter-spacing:-.2px; }
    .staff-overview .overview-panel-head p { margin:0;color:#8995aa;font-size:11px; }
    .staff-overview .overview-link { display:inline-flex;align-items:center;gap:5px;color:#4169c8;font-size:12px;font-weight:600;text-decoration:none; }
    .staff-overview .overview-link svg { width:14px;height:14px; }
    .staff-overview .overview-link:hover { text-decoration:underline; }
    .staff-overview .overview-link:focus-visible { outline:2px solid #4169c8;outline-offset:4px; }
    .staff-overview .overview-panel th { padding:13px 18px;background:#fafbfd;font-size:10px;letter-spacing:.5px; }
    .staff-overview .overview-panel td { padding:14px 18px;font-size:12px; }
    .staff-overview .overview-panel td > a { color:#4169c8;font-weight:600;text-decoration:none; }
    .staff-overview .overview-panel tr:last-child td { border-bottom:0; }
    .staff-overview .pill[data-status="Completed"] { background:#edf8f2;color:#25805a; }
    .staff-overview .pill[data-status="Pending"] { background:#fff6e7;color:#a57321; }
    .staff-overview .pill[data-status="Processing"] { background:#edf3ff;color:#4169c8; }
    .staff-overview .overview-bottom { display:grid;grid-template-columns:minmax(0,1.7fr) minmax(0,1fr);gap:18px;margin-top:20px; }
    .staff-overview .overview-payment { display:flex;align-items:center;gap:12px;padding:16px 20px;border-bottom:1px solid #edf0f5;text-decoration:none;color:#172554; }
    .staff-overview .overview-payment:last-child { border-bottom:0; }
    .staff-overview .overview-payment:hover { background:#fafbfd; }
    .staff-overview .overview-payment-icon { display:grid;place-items:center;flex:none;width:34px;height:34px;border-radius:10px;background:#edf8f2;color:#25805a; }
    .staff-overview .overview-payment-icon svg { width:16px;height:16px; }
    .staff-overview .overview-payment-copy { flex:1;min-width:0; }
    .staff-overview .overview-payment-copy strong { display:block;font-size:12px; }
    .staff-overview .overview-payment-copy span { display:block;margin-top:4px;color:#8995aa;font-size:11px;overflow-wrap:anywhere; }
    .staff-overview .overview-payment-amount { flex:none;font-size:12px;font-weight:700; }
    html[data-lavea-theme="dark"] .staff-overview .overview-stat,
    html[data-lavea-theme="dark"] .staff-overview .overview-panel,
    html[data-lavea-theme="dark"] .staff-overview .overview-date { background:#172554;border-color:#34415a; }
    html[data-lavea-theme="dark"] .staff-overview h1,
    html[data-lavea-theme="dark"] .staff-overview h2,
    html[data-lavea-theme="dark"] .staff-overview strong,
    html[data-lavea-theme="dark"] .staff-overview .overview-payment-amount { color:#e7edf9; }
    html[data-lavea-theme="dark"] .staff-overview .overview-panel th,
    html[data-lavea-theme="dark"] .staff-overview .overview-payment:hover { background:#223454; }
    @media(max-width:1000px) { .staff-overview .overview-bottom { grid-template-columns:minmax(0,1fr); } .staff-overview .overview-stats { grid-template-columns:repeat(2,minmax(0,1fr)); } }
    @media(max-width:600px) {
        .staff-overview .overview-header { align-items:flex-start;flex-direction:column;gap:14px; }
        .staff-overview .overview-header h1 { font-size:23px;overflow-wrap:anywhere; }
        .staff-overview .overview-stats { gap:10px; }
        .staff-overview .overview-stat { padding:14px; }
        .staff-overview .overview-stat-top { align-items:flex-start;flex-direction:column-reverse;gap:10px; }
        .staff-overview .overview-panel-head { padding:16px; }
        .staff-overview .overview-payment { padding:14px; }
    }
    @media(max-width:360px) { .staff-overview .overview-stats { grid-template-columns:minmax(0,1fr); } }
</style>
<div class="staff-overview">
    <header class="overview-header">
        <div><span class="overview-eyebrow">Staff overview</span><h1>Welcome, {{ auth()->user()->name }}</h1><p>Keep track of your shop's records and payments.</p></div>
        <div class="overview-date"><i data-lucide="calendar-days" aria-hidden="true"></i>{{ now()->format('D, M d, Y') }}</div>
    </header>
    <div class="overview-stats">
        @foreach ([['Total Records', $stats['total'], 'clipboard-list', '#edf3ff', '#4169c8', 'All laundry records'], ['Pending Records', $stats['pending'], 'clock-3', '#fff6e7', '#a57321', 'Waiting to be processed'], ['Processing Records', $stats['processing'], 'washing-machine', '#f2edff', '#8060c9', 'Currently in progress'], ['Completed Records', $stats['completed'], 'circle-check', '#edf8f2', '#25805a', 'Finished laundry records']] as [$label, $value, $icon, $background, $color, $caption])
            <article class="overview-stat" style="--stat-bg:{{ $background }};--stat-color:{{ $color }}">
                <div class="overview-stat-top"><span class="overview-stat-label">{{ $label }}</span><span class="overview-stat-icon"><i data-lucide="{{ $icon }}" aria-hidden="true"></i></span></div>
                <strong>{{ $value }}</strong><small>{{ $caption }}</small>
            </article>
        @endforeach
    </div>
    <section class="overview-panel">
        <div class="overview-panel-head"><div><h2>Recent Records</h2><p>The latest laundry activity</p></div><a class="overview-link" href="{{ route('staff.orders.index') }}">View all records <i data-lucide="arrow-up-right" aria-hidden="true"></i></a></div>
        @include('staff.partials.orders-table', ['orders' => $recentOrders])
    </section>
    <div class="overview-bottom">
        <section class="overview-panel">
            <div class="overview-panel-head"><div><h2>Today's Records</h2><p>Laundry orders placed today</p></div></div>
            @include('staff.partials.orders-table', ['orders' => $todayOrders])
        </section>
        <section class="overview-panel">
            <div class="overview-panel-head"><div><h2>Today's Payments</h2><p>Recent payment activity</p></div><a class="overview-link" href="{{ route('staff.payments.index') }}">View all <i data-lucide="arrow-up-right" aria-hidden="true"></i></a></div>
            @forelse($recentPayments as $payment)
                <a class="overview-payment" href="{{ route('staff.payments.show', $payment) }}"><span class="overview-payment-icon"><i data-lucide="credit-card" aria-hidden="true"></i></span><div class="overview-payment-copy"><strong>Payment #{{ $payment->id }}</strong><span>{{ $payment->order->customer->name ?? 'Customer' }}</span></div><span class="overview-payment-amount">&#8369;{{ number_format($payment->amount, 2) }}</span></a>
            @empty
                <div class="empty">No payments recorded today.</div>
            @endforelse
        </section>
    </div>
</div>
@endsection