@extends('staff.layout', ['title' => 'Payment Details'])
@section('content')
<div class="page-head no-print">
    <div><h1>Payment #LVE-PAY-{{ str_pad($payment->id, 6, '0', STR_PAD_LEFT) }}</h1><div class="muted">Transaction for Order #LVE-{{ str_pad($payment->order_id, 4, '0', STR_PAD_LEFT) }}</div></div>
    <div class="actions">
        <a href="{{ route('manager.payments.index') }}" class="lavea-back-link"><svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.8" stroke-linecap="round" stroke-linejoin="round" aria-hidden="true"><path d="m12 19-7-7 7-7M5 12h14" /></svg>Back to Payments</a>
    </div>
</div>

<div class="card no-print">
    <div class="grid" style="grid-template-columns:repeat(3,minmax(0,1fr))">
        @foreach([
            ['Customer', $payment->order?->customer?->name ?? '—'],
            ['Service', $payment->order?->service?->service_name ?? '—'],
            ['Quantity / Weight', ($payment->order?->quantity ?? '—').' '.($payment->order?->service?->billingUnit() ?? 'kg')],
            ['Order Total', '₱'.number_format($payment->order?->total ?? 0, 2)],
            ['Amount Paid', '₱'.number_format($payment->amount, 2)],
            ['Payment Method', $payment->payment_method],
            ['Reference Number', $payment->reference_number ?: '—'],
            ['Payment Date', $payment->payment_date?->format('M d, Y') ?? '—'],
            ['Status', $payment->status],
            ['Processed By', $payment->processedBy?->name ?? 'Not recorded'],
        ] as [$label, $value])
            <div><div class="muted">{{ $label }}</div><strong>{{ $value }}</strong></div>
        @endforeach
    </div>
    @can('print-receipt', $payment)
        <div class="actions no-print" style="margin-top:25px">
            <a class="btn" href="{{ route('payments.receipt', $payment) }}" target="_blank" rel="noopener"><i data-lucide="printer" aria-hidden="true"></i>Print Receipt</a>
        </div>
    @endcan
</div>

@if($payment->status === 'Pending')
    <p class="muted no-print" style="margin-top:16px">Waiting for Admin approval.</p>
@elseif($payment->status === 'Rejected')
    <div class="actions no-print" style="margin-top:16px"><a class="btn" href="{{ route('manager.payments.edit', $payment) }}">Correct and Resubmit</a></div>
@endif

@can('print-receipt', $payment)
    @include('partials.payment-receipt', ['payment' => $payment])
@endcan
@include('partials.payment-proof', ['payment' => $payment])
@endsection
