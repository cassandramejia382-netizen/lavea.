@extends('customer.layout', ['title' => 'Payments'])
@section('content')
<div class="page-head"><div><h1>Payments</h1><div class="muted">Payment history for your laundry orders.</div></div></div>
<div class="card"><div class="table-wrap"><table>
    <thead><tr><th>Payment</th><th>Order</th><th>Service</th><th>Date</th><th>Method</th><th>Status</th><th>Amount (₱)</th></tr></thead>
    <tbody>@forelse($payments as $payment)
        <tr><td><a href="{{ route('customer.payments.show', $payment) }}">#{{ $payment->id }}</a></td><td><a href="{{ route('customer.orders.show', $payment->order_id) }}">#{{ $payment->order_id }}</a></td><td>{{ $payment->order?->service?->service_name ?? 'Unavailable service' }}</td><td>{{ $payment->payment_date?->format('M d, Y') ?? '—' }}</td><td>{{ $payment->payment_method }}</td><td><span class="pill">{{ $payment->status }}</span></td><td>₱{{ number_format($payment->amount, 2) }}</td></tr>
    @empty<tr><td colspan="7" class="empty">No payments recorded yet.</td></tr>@endforelse</tbody>
</table></div><div style="margin-top:18px">{{ $payments->links('customer.pagination') }}</div></div>
@endsection
