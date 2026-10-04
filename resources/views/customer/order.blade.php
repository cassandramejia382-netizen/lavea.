@extends('customer.layout', ['title' => 'Order Details'])
@section('content')
<div class="page-head"><div><h1>Order #{{ $order->id }}</h1><div class="muted">Your laundry order details.</div></div><a class="btn secondary" href="{{ route('customer.orders.index') }}">My Orders</a></div>
<div class="card"><div class="form-grid">
    @foreach (['Service' => $order->service?->service_name ?? 'Unavailable service', 'Quantity' => $order->quantity, 'Order Date' => $order->order_date?->format('M d, Y') ?? '—', 'Pickup Date' => $order->pickup_date?->format('M d, Y') ?? '—', 'Delivery Date' => $order->delivery_date?->format('M d, Y') ?? '—', 'Status' => $order->status, 'Payment Status' => $order->payment_status, 'Total' => '₱'.number_format($order->total, 2)] as $label => $value)
        <div class="field"><span class="muted">{{ $label }}</span><strong>{{ $value }}</strong></div>
    @endforeach
</div></div>
@endsection
