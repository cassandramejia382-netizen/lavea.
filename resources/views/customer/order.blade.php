@extends('customer.layout', ['title' => 'Order Details'])
@section('content')
<div class="page-head"><div><h1>Record #LVE-{{ str_pad($order->id, 4, '0', STR_PAD_LEFT) }}</h1><div class="muted">Your laundry order details.</div></div><div class="actions">@if($balance > 0)<a class="btn" href="{{ route('customer.payments.checkout', $order) }}">Pay Now · &#8369;{{ number_format($balance, 2) }}</a>@elseif($order->payments->where('status', 'Pending')->isNotEmpty())<span class="pill" data-status="Pending">Payment Pending</span>@endif<a class="btn secondary" href="{{ route('customer.orders.index') }}">Records</a></div></div>
<div class="card"><div class="form-grid">
    @foreach (['Service' => $order->service?->service_name ?? 'Unavailable service', 'Quantity' => $order->quantity, 'Order Date' => $order->order_date?->format('M d, Y') ?? '—', 'Pickup Date' => $order->pickup_date?->format('M d, Y') ?? '—', 'Delivery Date' => $order->delivery_date?->format('M d, Y') ?? '—', 'Status' => $order->status, 'Payment Status' => $order->payment_status, 'Total' => '₱'.number_format($order->total, 2)] as $label => $value)
        <div class="field"><span class="muted">{{ $label }}</span><strong>{{ $value }}</strong></div>
    @endforeach
</div></div>
@endsection
