@extends('staff.layout', ['title' => 'Payment Details'])
@section('content')
<div class="page-head no-print">
    <div><h1>Payment #LVE-PAY-{{ str_pad($payment->id, 6, '0', STR_PAD_LEFT) }}</h1><div class="muted">Transaction for Order #LVE-{{ str_pad($payment->order_id, 4, '0', STR_PAD_LEFT) }}</div></div>
    <div class="actions">
        <a class="btn secondary" href="{{ route('staff.payments.index') }}">Back to Payments</a>
        @if($payment->status === 'Completed')<button class="btn" type="button" onclick="window.print()">Print Receipt</button>@endif
    </div>
</div>

<div class="card no-print">
    <div class="grid" style="grid-template-columns:repeat(3,minmax(0,1fr))">
        @foreach([
            ['Customer', $payment->order?->customer?->name ?? '—'],
            ['Service', $payment->order?->service?->service_name ?? '—'],
            ['Quantity / Weight', ($payment->order?->quantity ?? '—').' kg'],
            ['Order Total', '₱'.number_format($payment->order?->total ?? 0, 2)],
            ['Amount Paid', '₱'.number_format($payment->amount, 2)],
            ['Payment Method', $payment->payment_method],
            ['Reference Number', $payment->reference_number ?: '—'],
            ['Payment Date', $payment->payment_date?->format('M d, Y') ?? '—'],
            ['Status', $payment->status],
            ['Processed By', $payment->order?->staff?->name ?? auth()->user()->name],
        ] as [$label, $value])
            <div><div class="muted">{{ $label }}</div><strong>{{ $value }}</strong></div>
        @endforeach
    </div>
</div>

@if($payment->status === 'Pending')
    <div class="card no-print" style="margin-top:16px">
        <h2>Review customer payment</h2>
        <p class="muted">Confirm only after the payment is verified as received.</p>
        <div class="actions">
            <form method="POST" action="{{ route('staff.payments.review', $payment) }}">
                @csrf @method('PATCH')<input type="hidden" name="status" value="Completed"><button class="btn" type="submit">Confirm Payment</button>
            </form>
            <form method="POST" action="{{ route('staff.payments.review', $payment) }}" onsubmit="return confirm('Mark this payment as failed?')">
                @csrf @method('PATCH')<input type="hidden" name="status" value="Failed"><button class="btn danger" type="submit">Mark Failed</button>
            </form>
        </div>
    </div>
@endif

@if($payment->status === 'Completed')
    <article class="print-only" style="max-width:700px;margin:0 auto;padding:28px;color:#111;font-family:Arial,sans-serif">
        <header style="text-align:center;border-bottom:2px solid #111;padding-bottom:18px">
            <h1 style="font-size:26px;margin:0">LAVEA Laundry Shop</h1>
            <p>Official Payment Receipt</p>
            <p>Transaction #LVE-PAY-{{ str_pad($payment->id, 6, '0', STR_PAD_LEFT) }}</p>
        </header>
        <section style="padding:14px 0;border-bottom:1px solid #bbb">
            <p><strong>Order Number:</strong> #LVE-{{ str_pad($payment->order_id, 4, '0', STR_PAD_LEFT) }}</p>
            <p><strong>Customer:</strong> {{ $payment->order?->customer?->name ?? '—' }}</p>
            <p><strong>Laundry Service:</strong> {{ $payment->order?->service?->service_name ?? '—' }}</p>
            <p><strong>Quantity / Weight:</strong> {{ $payment->order?->quantity ?? '—' }} kg</p>
            <p><strong>Order Date:</strong> {{ $payment->order?->order_date?->format('M d, Y') ?? '—' }}</p>
            <p><strong>Pickup / Delivery:</strong> {{ $payment->order?->pickup_date?->format('M d, Y') ?? '—' }} / {{ $payment->order?->delivery_date?->format('M d, Y') ?? '—' }}</p>
        </section>
        <section style="padding:14px 0;border-bottom:1px solid #bbb">
            <p><strong>Total Amount:</strong> &#8369;{{ number_format($payment->order?->total ?? 0, 2) }}</p>
            <p><strong>Amount Paid:</strong> &#8369;{{ number_format($payment->amount, 2) }}</p>
            <p><strong>Payment Method:</strong> {{ $payment->payment_method }}</p>
            <p><strong>Reference Number:</strong> {{ $payment->reference_number ?: '—' }}</p>
            <p><strong>Payment Date:</strong> {{ $payment->payment_date?->format('F d, Y') ?? '—' }}</p>
            <p><strong>Payment Status:</strong> {{ $payment->status }}</p>
        </section>
        <p style="text-align:center;padding-top:18px">Thank you for choosing LAVEA!</p>
    </article>
@endif
@endsection
