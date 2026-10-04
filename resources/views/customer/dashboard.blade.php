@extends('customer.layout', ['title' => 'Dashboard'])
@section('content')
<div class="page-head"><div><h1>Welcome, {{ auth()->user()->name }}</h1><div class="muted">Track your laundry, payments, and services in one place.</div></div><div class="muted">{{ now()->format('F d, Y') }}</div></div>
@if(session('status'))<div class="flash">{{ session('status') }}</div>@endif
<div class="grid">
    @foreach ([['Total Orders', $stats['total'], 'clipboard-list'], ['Pending Orders', $stats['pending'], 'clock-3'], ['Completed Orders', $stats['completed'], 'circle-check'], ['Total Payments', '₱'.number_format($stats['payments'], 2), 'credit-card']] as [$label, $value, $icon])
        <div class="stat-card"><i data-lucide="{{ $icon }}" style="color:#4169c8;width:22px"></i><div class="muted" style="margin-top:12px">{{ $label }}</div><strong>{{ $value }}</strong>@if($label === 'Total Payments')<small class="muted">Completed payments</small>@endif</div>
    @endforeach
</div>
<div class="card" style="margin-bottom:24px">
    <div class="page-head"><h2>Recent Orders</h2><a class="btn secondary" href="{{ route('customer.orders.index') }}">View all</a></div>
    @include('customer.orders-table', ['orders' => $recentOrders])
</div>
<div class="page-head"><div><h1 style="font-size:20px">Available Services</h1><div class="muted">Fresh, clean laundry starts here.</div></div><a class="btn secondary" href="{{ route('customer.services.index') }}">View all</a></div>
@include('customer.services-grid')
@endsection
