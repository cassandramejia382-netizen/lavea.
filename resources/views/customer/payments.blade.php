@extends('customer.layout', ['title' => 'Payments'])
@push('styles')
<style>
    .customer-payments-page { width:100%;max-width:1180px;margin:0 auto; }
    .customer-payments-heading { margin:0 0 18px; }
    .customer-payments-heading h1 { margin:0 0 4px;color:#10204a;font-size:23px; }
    .customer-payments-heading p { margin:0;color:#78869d;font-size:13px; }
    .payment-overview { display:grid;grid-template-columns:repeat(3,minmax(0,1fr));gap:12px;margin-bottom:16px; }
    .payment-overview-card { display:flex;align-items:center;gap:12px;padding:15px 16px;border:1px solid #e6eaf2;border-radius:11px;background:#fff;box-shadow:0 3px 12px rgb(23 37 84 / 3%); }
    .payment-overview-icon { display:grid;place-items:center;flex:none;width:38px;height:38px;border-radius:10px;background:#edf2ff;color:#4169c8; }
    .payment-overview-icon svg { width:18px;height:18px; }
    .payment-overview-card:nth-child(2) .payment-overview-icon { background:#eaf8f1;color:#21865a; }
    .payment-overview-card:nth-child(3) .payment-overview-icon { background:#fff5e5;color:#b47713; }
    .payment-overview-label { color:#78869d;font-size:12px; }
    .payment-overview-value { display:block;margin-top:3px;color:#172554;font-size:19px;font-weight:700; }
    .payment-section { margin-bottom:16px;padding:17px;border:1px solid #e6eaf2;border-radius:11px;background:#fff;box-shadow:0 3px 12px rgb(23 37 84 / 3%); }
    .payment-section-heading { display:flex;align-items:center;justify-content:space-between;gap:12px;margin-bottom:13px; }
    .payment-section-heading h2 { margin:0;font-size:16px;color:#172554; }
    .payment-section-heading p { margin:4px 0 0;color:#78869d;font-size:12px; }
    .payable-order-grid { display:grid;grid-template-columns:repeat(auto-fit,minmax(245px,1fr));gap:11px; }
    .payable-order-card { padding:14px;border:1px solid #e9edf5;border-radius:9px;background:linear-gradient(145deg,#fff,#f9fbff);transition:transform 160ms ease,border-color 160ms ease,box-shadow 160ms ease; }
    .payable-order-card:hover { transform:translateY(-2px);border-color:#cbd8f4;box-shadow:0 7px 16px rgb(23 37 84 / 7%); }
    .payable-order-top { display:flex;align-items:center;justify-content:space-between;gap:8px;margin-bottom:9px; }
    .payable-order-number { color:#4169c8;font-size:12px;font-weight:700;text-decoration:none; }
    .payable-order-number:hover { text-decoration:underline; }
    .payable-order-service { margin:0 0 12px;color:#263550;font-size:14px;font-weight:600; }
    .payable-order-meta { display:flex;justify-content:space-between;gap:8px;margin-bottom:12px;color:#78869d;font-size:12px; }
    .payable-order-meta strong { color:#263550;font-size:13px; }
    .payable-order-card .btn { width:100%;justify-content:center;padding:9px 12px;font-size:12px; }
    .payment-search { display:flex;flex-wrap:wrap;gap:9px;margin-bottom:12px;padding:12px;border:1px solid #e6eaf2;border-radius:10px;background:#fff; }
    .payment-search input,.payment-search select { min-height:38px;padding:8px 11px;border:1px solid #dfe5ef;border-radius:7px;background:#fff;color:#263550;font:inherit;font-size:13px; }
    .payment-search input { flex:1;min-width:220px; }
    .payment-search select { min-width:150px; }
    .payment-search .btn { min-height:38px;padding:8px 13px;font-size:12px; }
    .payment-history-card { overflow:hidden;padding:0; }
    .payment-history-title { display:flex;justify-content:space-between;align-items:center;padding:15px 17px;border-bottom:1px solid #edf0f5; }
    .payment-history-title h2 { margin:0;color:#172554;font-size:15px; }
    .payment-history-title span { color:#78869d;font-size:12px; }
    .payment-history-card th { padding:11px 12px;font-size:10px;letter-spacing:.04em; }
    .payment-history-card td { padding:12px;font-size:12px; }
    .payment-history-card td a { color:#4169c8;text-decoration:none;font-weight:600; }
    .payment-history-card td a:hover { text-decoration:underline; }
    .payment-history-pagination { padding:0 16px 14px; }
    @media(max-width:700px) { .payment-overview { grid-template-columns:1fr;gap:8px; } .customer-payments-heading h1 { font-size:21px; } }
    @media(prefers-reduced-motion:reduce) { .payable-order-card { transition:none; } }
</style>
@endpush
@section('content')
<div class="customer-payments-page">
    <header class="customer-payments-heading">
        <h1>Payments</h1>
        <p>Pay an outstanding order and review your payment history.</p>
    </header>

    <section class="payment-overview" aria-label="Payment summary">
        <article class="payment-overview-card"><span class="payment-overview-icon"><i data-lucide="receipt-text"></i></span><div><span class="payment-overview-label">Payment records</span><strong class="payment-overview-value">{{ $paymentCount }}</strong></div></article>
        <article class="payment-overview-card"><span class="payment-overview-icon"><i data-lucide="circle-check"></i></span><div><span class="payment-overview-label">Completed payments</span><strong class="payment-overview-value">&#8369;{{ number_format($completedTotal, 2) }}</strong></div></article>
        <article class="payment-overview-card"><span class="payment-overview-icon"><i data-lucide="clock-3"></i></span><div><span class="payment-overview-label">Pending payments</span><strong class="payment-overview-value">&#8369;{{ number_format($pendingTotal, 2) }}</strong></div></article>
    </section>

    <section class="payment-section">
        <div class="payment-section-heading">
            <div><h2>Orders to pay</h2><p>Choose an order to submit a full or partial payment.</p></div>
            <i data-lucide="wallet-cards" style="width:19px;height:19px;color:#4169c8"></i>
        </div>
        @if($payableOrders->isNotEmpty())
            <div class="payable-order-grid">
                @foreach($payableOrders as $order)
                    @php
                        $reservedPaymentAmount = $order->payments->sum('amount');
                        $remainingBalance = max(0, round((float) $order->total - (float) $reservedPaymentAmount, 2));
                        $pendingPaymentAmount = $order->payments->where('status', 'Pending')->sum('amount');
                    @endphp
                    <article class="payable-order-card">
                        <div class="payable-order-top">
                            <a class="payable-order-number" href="{{ route('customer.orders.show', $order) }}">ORDER #LVE-{{ str_pad($order->id, 4, '0', STR_PAD_LEFT) }}</a>
                            @if($remainingBalance > 0)<span class="pill" data-status="Unpaid">{{ $order->payment_status }}</span>@else<span class="pill" data-status="Pending">Pending review</span>@endif
                        </div>
                        <h3 class="payable-order-service">{{ $order->service?->service_name ?? 'Laundry service' }}</h3>
                        <div class="payable-order-meta"><span>Order total</span><strong>&#8369;{{ number_format($order->total, 2) }}</strong></div>
                        @if($remainingBalance > 0)
                            <div class="payable-order-meta"><span>Remaining balance</span><strong>&#8369;{{ number_format($remainingBalance, 2) }}</strong></div>
                            <a class="btn" href="{{ route('customer.payments.checkout', $order) }}">Pay now</a>
                        @else
                            <div class="payable-order-meta"><span>Awaiting verification</span><strong>&#8369;{{ number_format($pendingPaymentAmount, 2) }}</strong></div>
                            <a class="btn secondary" href="{{ route('customer.orders.show', $order) }}">View record</a>
                        @endif
                    </article>
                @endforeach
            </div>
        @else
            <div style="display:flex;align-items:center;gap:10px;padding:14px;border-radius:9px;background:#f7f9fc;color:#78869d;font-size:13px"><i data-lucide="badge-check" style="width:18px;height:18px;color:#21865a"></i>All your orders are paid in full.</div>
        @endif
    </section>

    <form class="payment-search" method="GET" action="{{ route('customer.payments.index') }}">
        <input name="search" value="{{ $search }}" placeholder="Search order, service, method or reference" aria-label="Search payments">
        <select name="status" aria-label="Filter payment status">
            <option value="">All statuses</option>
            @foreach(['Completed', 'Pending', 'Failed'] as $option)
                <option value="{{ $option }}" @selected($status === $option)>{{ $option }}</option>
            @endforeach
        </select>
        <button class="btn" type="submit"><i data-lucide="search" style="width:14px;height:14px"></i>Search</button>
        @if($search !== '' || $status !== '')<a class="btn secondary" href="{{ route('customer.payments.index') }}">Clear</a>@endif
    </form>

    <section class="card payment-history-card">
        <div class="payment-history-title"><h2>Payment history</h2><span>{{ $payments->total() }} transactions</span></div>
        <div class="table-wrap"><table>
            <thead><tr><th>Payment</th><th>Order</th><th>Service</th><th>Date</th><th>Method</th><th>Status</th><th>Amount</th></tr></thead>
            <tbody>
                @forelse($payments as $payment)
                    <tr>
                        <td><a href="{{ route('customer.payments.show', $payment) }}">#LVE-PAY-{{ str_pad($payment->id, 6, '0', STR_PAD_LEFT) }}</a></td>
                        <td><a href="{{ route('customer.orders.show', $payment->order_id) }}">#LVE-{{ str_pad($payment->order_id, 4, '0', STR_PAD_LEFT) }}</a></td>
                        <td>{{ $payment->order?->service?->service_name ?? 'Unavailable service' }}</td>
                        <td>{{ $payment->payment_date?->format('M d, Y') ?? '—' }}</td>
                        <td>{{ $payment->payment_method }}</td>
                        <td><span class="pill" data-status="{{ $payment->status }}">{{ $payment->status }}</span></td>
                        <td>&#8369;{{ number_format($payment->amount, 2) }}</td>
                    </tr>
                @empty
                    <tr><td colspan="7" class="empty">No payment records match your search.</td></tr>
                @endforelse
            </tbody>
        </table></div>
        <div class="payment-history-pagination">{{ $payments->links('customer.pagination') }}</div>
    </section>
</div>
@endsection
