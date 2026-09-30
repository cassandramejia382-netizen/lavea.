@extends('staff.layout', ['title' => 'Dashboard'])

@section('content')
<div class="page-head"><div><h1>Welcome, {{ auth()->user()->name }}</h1><div class="muted">Here’s your laundry shop activity for today.</div></div><div class="muted" style="text-align:right"><strong>{{ now()->format('F d, Y') }}</strong><br>{{ now()->format('l') }}</div></div>
<div class="grid">
    @foreach ([['Total Orders', $stats['total'], 'clipboard-list'], ['Pending Orders', $stats['pending'], 'clock-3'], ['Processing Orders', $stats['processing'], 'washing-machine'], ['Completed Orders', $stats['completed'], 'circle-check']] as [$label,$value,$icon])
        <div class="stat-card"><i data-lucide="{{ $icon }}" style="color:#4169c8;width:22px"></i><div class="muted" style="margin-top:12px">{{ $label }}</div><strong>{{ $value }}</strong></div>
    @endforeach
</div>
<div class="cols">
    <div class="card"><div class="page-head"><h2>Recent Orders</h2><a class="btn secondary" href="{{ route('staff.orders.index') }}">View all</a></div>
        @include('staff.partials.orders-table', ['orders' => $recentOrders])
    </div>
    <div class="card"><h2>Today's Schedule</h2>
        @forelse($todaySchedule as $item)<div style="padding:12px 0;border-bottom:1px solid #edf0f5"><strong>{{ $item->type }} · {{ $item->schedule_time }}</strong><div class="muted">{{ $item->order->customer->name ?? 'Customer' }} · Order #{{ $item->order_id }}</div><span class="pill">{{ $item->status }}</span></div>@empty<div class="empty">No assigned schedule today.</div>@endforelse
    </div>
</div>
<div class="cols">
    <div class="card"><h2>Today's Orders</h2>@include('staff.partials.orders-table', ['orders' => $todayOrders])</div>
    <div class="card"><h2>Today's Payments</h2>
        @forelse($recentPayments as $payment)<div style="padding:11px 0;border-bottom:1px solid #edf0f5"><a href="{{ route('staff.payments.show', $payment) }}"><strong>Payment #{{ $payment->id }}</strong></a><div class="muted">{{ $payment->order->customer->name ?? 'Customer' }} · ₱{{ number_format($payment->amount, 2) }}</div></div>@empty<div class="empty">No payments recorded today.</div>@endforelse
    </div>
</div>
@endsection
