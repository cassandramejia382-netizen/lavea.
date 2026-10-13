@extends('customer.layout', ['title' => 'Payment Details'])
@section('content')
<div class="page-head no-print">
    <div><h1>Payment #LVE-PAY-{{ str_pad($payment->id, 6, '0', STR_PAD_LEFT) }}</h1><div class="muted">Payment details for Order #LVE-{{ str_pad($payment->order_id, 4, '0', STR_PAD_LEFT) }}.</div></div>
    <div class="actions">
        <a href="{{ route('customer.payments.index') }}" class="lavea-back-link"><svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.8" stroke-linecap="round" stroke-linejoin="round" aria-hidden="true"><path d="m12 19-7-7 7-7M5 12h14" /></svg>Back to Payments</a>
        @can('print-receipt', $payment)
            <a class="btn" href="{{ route('payments.receipt', $payment) }}" target="_blank" rel="noopener"><i data-lucide="printer" aria-hidden="true"></i>Print Receipt</a>
        @endcan
    </div>
</div>

<div class="card no-print">
    <div class="grid" style="grid-template-columns:repeat(3,minmax(0,1fr))">
        @foreach([
            ['Order', '#LVE-'.str_pad($payment->order_id, 4, '0', STR_PAD_LEFT)],
            ['Service', $payment->order?->service?->service_name ?? 'Unavailable service'],
            [$payment->order?->service?->quantityLabel() ?? 'Quantity', ($payment->order?->quantity ?? '—').' '.($payment->order?->service?->billingUnit() ?? 'kg')],
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
    @endif
</div>

@if($payment->status === 'Pending')
    <p class="muted no-print">Payment is pending Admin approval.</p>
@endif
@can('print-receipt', $payment)
    @include('partials.payment-receipt', ['payment' => $payment])
@endcan
@include('partials.payment-proof', ['payment' => $payment])
@endsection
