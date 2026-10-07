@extends('customer.layout', ['title' => 'Records'])
@section('content')
<style>
    .customer-records { max-width: 1120px; margin: 0 auto; }
    .customer-record-stats { display:grid;grid-template-columns:repeat(3,minmax(0,1fr));gap:12px;margin-bottom:16px; }
    .customer-record-stat { display:flex;align-items:center;gap:12px;padding:14px 16px;border:1px solid #e7ebf2;border-radius:10px;background:#fff; }
    .customer-record-stat-icon { display:grid;place-items:center;width:36px;height:36px;border-radius:9px;background:#edf2ff;color:#4169c8; }
    .customer-record-stat-icon svg { width:18px;height:18px; }
    .customer-record-stat strong { display:block;color:#172554;font-size:18px; }
    .customer-record-stat span { color:#7a879d;font-size:12px; }
    .customer-record-filters { display:flex;flex-wrap:wrap;gap:9px;padding:14px;margin-bottom:13px;border:1px solid #e7ebf2;border-radius:10px;background:#fff; }
    .customer-record-filters select { min-width:175px;padding:9px 11px;border:1px solid #dfe5ef;border-radius:7px;background:#fff;color:#263550;font:inherit; }
    @media(max-width:680px) { .customer-record-stats { grid-template-columns:1fr;gap:8px; } }
</style>
<div class="customer-records">
    <div class="page-head"><div><h1>Records</h1><div class="muted">Review your laundry order and payment statuses.</div></div></div>

    <div class="customer-record-stats">
        <div class="customer-record-stat"><div class="customer-record-stat-icon"><i data-lucide="loader-circle"></i></div><div><strong>{{ $recordStats['processing'] }}</strong><span>Processing</span></div></div>
        <div class="customer-record-stat"><div class="customer-record-stat-icon"><i data-lucide="circle-check"></i></div><div><strong>{{ $recordStats['paid'] }}</strong><span>Paid</span></div></div>
        <div class="customer-record-stat"><div class="customer-record-stat-icon"><i data-lucide="wallet"></i></div><div><strong>{{ $recordStats['unpaid'] }}</strong><span>Unpaid</span></div></div>
    </div>

    <form class="customer-record-filters" method="GET" action="{{ route('customer.orders.index') }}">
        <select name="order_status" aria-label="Filter by order status">
            <option value="">All order statuses</option>
            @foreach(['Pending', 'Processing', 'Completed', 'Cancelled'] as $option)
                <option value="{{ $option }}" @selected($orderStatus === $option)>{{ $option }}</option>
            @endforeach
        </select>
        <select name="payment_status" aria-label="Filter by payment status">
            <option value="">All payment statuses</option>
            @foreach(['Unpaid', 'Partial', 'Paid'] as $option)
                <option value="{{ $option }}" @selected($paymentStatus === $option)>{{ $option }}</option>
            @endforeach
        </select>
        <button class="btn" type="submit">Apply filters</button>
        @if($orderStatus !== '' || $paymentStatus !== '')
            <a class="btn secondary" href="{{ route('customer.orders.index') }}">Clear</a>
        @endif
    </form>

    <div class="card">@include('customer.orders-table')<div style="margin-top:15px">{{ $orders->links('customer.pagination') }}</div></div>
</div>
@endsection
