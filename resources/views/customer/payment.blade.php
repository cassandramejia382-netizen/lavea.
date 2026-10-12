@extends('customer.layout', ['title' => 'Payment Details'])
@section('content')
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
    @include('partials.payment-receipt', ['payment' => $payment])
@endif
@if ($payment->status === 'Completed' && request()->boolean('print'))
    <script>
        window.addEventListener('load', () => window.print(), { once: true });
    </script>
@endif
@endsection
