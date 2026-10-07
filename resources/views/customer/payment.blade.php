@extends('customer.layout', ['title' => 'Payment Details'])
@section('content')
<style>
    @media print {
        .no-print { display: none !important; }
        .customer-payment-receipt { display: block !important; }
        .main { margin: 0 !important; width: 100% !important; }
        .content { padding: 0 !important; }
    }
    .customer-payment-receipt { display: none; }
</style>
<div class="page-head no-print">
    <div><h1>Payment #LVE-PAY-{{ str_pad($payment->id, 6, '0', STR_PAD_LEFT) }}</h1><div class="muted">Payment details for Order #LVE-{{ str_pad($payment->order_id, 4, '0', STR_PAD_LEFT) }}.</div></div>
    <div class="actions">
        <a class="btn secondary" href="{{ route('customer.payments.index') }}">Back to Payments</a>
        @if($payment->status === 'Completed')
            <button class="btn" type="button" onclick="window.print()">Print Receipt</button>
        @endif
    </div>
</div>

<div class="card no-print">
    <div class="grid" style="grid-template-columns:repeat(3,minmax(0,1fr))">
        @foreach([
            ['Order', '#LVE-'.str_pad($payment->order_id, 4, '0', STR_PAD_LEFT)],
            ['Service', $payment->order?->service?->service_name ?? 'Unavailable service'],
            ['Weight', ($payment->order?->quantity ?? '—').' kg'],
            ['Order total', '₱'.number_format($payment->order?->total ?? 0, 2)],
            ['Amount paid', '₱'.number_format($payment->amount, 2)],
            ['Method', $payment->payment_method],
            ['Reference number', $payment->reference_number ?: '—'],
            ['Payment date', $payment->payment_date?->format('M d, Y') ?? '—'],
            ['Status', $payment->status],
        ] as [$label, $value])
            <div><div class="muted">{{ $label }}</div><strong>{{ $value }}</strong></div>
        @endforeach
    </div>
    @if($payment->order)
        <div style="margin-top:16px"><a class="btn secondary" href="{{ route('customer.orders.show', $payment->order_id) }}">View Order</a></div>
    @endif
</div>

@if($payment->status === 'Completed')
    <article class="customer-payment-receipt" style="max-width:700px;margin:0 auto;padding:28px;color:#111;font-family:Arial,sans-serif">
        <header style="text-align:center;border-bottom:2px solid #111;padding-bottom:16px">
            <h1 style="font-size:24px;margin:0">LAVEA Laundry Shop</h1>
            <p>Official Payment Receipt</p>
            <p>Transaction #LVE-PAY-{{ str_pad($payment->id, 6, '0', STR_PAD_LEFT) }}</p>
        </header>
        <section style="padding:12px 0;border-bottom:1px solid #bbb">
            <p><strong>Order:</strong> #LVE-{{ str_pad($payment->order_id, 4, '0', STR_PAD_LEFT) }}</p>
            <p><strong>Customer:</strong> {{ $payment->order?->customer?->name ?? auth()->user()->name }}</p>
            <p><strong>Service:</strong> {{ $payment->order?->service?->service_name ?? 'Unavailable service' }}</p>
            <p><strong>Weight:</strong> {{ $payment->order?->quantity ?? '—' }} kg</p>
            <p><strong>Pickup date:</strong> {{ $payment->order?->pickup_date?->format('M d, Y') ?? '—' }}</p>
        </section>
        <section style="padding:12px 0;border-bottom:1px solid #bbb">
            <p><strong>Order total:</strong> &#8369;{{ number_format($payment->order?->total ?? 0, 2) }}</p>
            <p><strong>Amount paid:</strong> &#8369;{{ number_format($payment->amount, 2) }}</p>
            <p><strong>Payment method:</strong> {{ $payment->payment_method }}</p>
            <p><strong>Reference number:</strong> {{ $payment->reference_number ?: '—' }}</p>
            <p><strong>Payment date:</strong> {{ $payment->payment_date?->format('F d, Y') ?? '—' }}</p>
            <p><strong>Status:</strong> {{ $payment->status }}</p>
        </section>
        <p style="text-align:center;padding-top:14px">Thank you for choosing LAVEA!</p>
    </article>
@endif
@endsection
