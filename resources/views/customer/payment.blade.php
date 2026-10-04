@extends('customer.layout', ['title' => 'Payment Details'])
@section('content')
<div class="page-head"><div><h1>Payment #{{ $payment->id }}</h1><div class="muted">Your payment record.</div></div><a class="btn secondary" href="{{ route('customer.payments.index') }}">Payments</a></div>
<div class="card"><div class="form-grid">
    <div class="field"><span class="muted">Order</span><a href="{{ route('customer.orders.show', $payment->order_id) }}">#{{ $payment->order_id }}</a></div>
    @foreach (['Service' => $payment->order?->service?->service_name ?? 'Unavailable service', 'Payment Date' => $payment->payment_date?->format('M d, Y') ?? '—', 'Method' => $payment->payment_method, 'Reference Number' => $payment->reference_number ?: '—', 'Status' => $payment->status, 'Amount' => '₱'.number_format($payment->amount, 2)] as $label => $value)
        <div class="field"><span class="muted">{{ $label }}</span><strong>{{ $value }}</strong></div>
    @endforeach
</div></div>
@endsection
