@extends('staff.layout', ['title' => 'Payments'])
@section('content')
<style>
.payment-actions svg { width:17px;height:17px; }
.payment-actions .btn { padding:9px;border:0;transition:background-color 160ms ease; }
.payment-actions .btn-view { background:#edf3ff;color:#3973e6; }
.payment-actions .btn-view:hover { background:#dfeaff; }
.payment-actions .btn-edit { background:#efe9ff;color:#8043e8; }
.payment-actions .btn-edit:hover { background:#e4d9ff; }
.payment-actions .btn:focus-visible { outline:2px solid #4169c8;outline-offset:3px; }
.payment-actions .danger { background:#fff1f2;color:#be123c; }
.payment-actions form { margin:0; }
.pill[data-status="Confirmed"] { background:#dcfce7;color:#15803d; }
.pill[data-status="Pending"] { background:#fef3c7;color:#b45309; }
.pill[data-status="Rejected"] { background:#fee2e2;color:#dc2626; }
</style>
<div class="page-head"><div><h1>Payments</h1><div class="muted">Submitted payments and Admin approval status.</div></div><a class="btn" href="{{ route('manager.payments.create') }}">+ Record Payment</a></div>
<form method="GET" action="{{ route('manager.payments.index') }}" style="display:flex;gap:8px;margin-bottom:17px"><input class="search-input" name="search" value="{{ $search }}" placeholder="Customer, order, method or status"><select class="search-input" name="status"><option value="">All statuses</option>@foreach(['Confirmed','Pending','Rejected'] as $option)<option @selected($status===$option)>{{ $option }}</option>@endforeach</select><button class="btn">Search</button></form>
<div class="card table-wrap"><table><thead><tr><th>Transaction</th><th>Order</th><th>Customer</th><th>Date</th><th>Method</th><th>Status</th><th>Amount</th><th>Actions</th></tr></thead><tbody>@forelse($payments as $payment)<tr><td>#{{ $payment->id }}</td><td>#LVE-{{ str_pad($payment->order_id,4,'0',STR_PAD_LEFT) }}</td><td>{{ $payment->order->customer->name ?? '—' }}</td><td>{{ $payment->payment_date?->format('M d, Y') }}</td><td>{{ $payment->payment_method }}</td><td><span class="pill" data-status="{{ $payment->status }}">{{ $payment->status }}</span></td><td>₱{{ number_format($payment->amount,2) }}</td><td><div class="actions payment-actions"><a class="btn btn-view" href="{{ route('manager.payments.show',$payment) }}" title="View payment" aria-label="View payment {{ $payment->id }}"><i data-lucide="eye"></i></a>@if($payment->status === 'Rejected')<a class="btn btn-edit" href="{{ route('manager.payments.edit', $payment) }}" title="Correct and resubmit payment" aria-label="Correct and resubmit payment {{ $payment->id }}"><i data-lucide="pencil"></i></a>@endif</div></td></tr>@empty<tr><td colspan="8" class="empty">No payment records match your search.</td></tr>@endforelse</tbody></table></div><div style="margin-top:15px">{{ $payments->links('customer.pagination') }}</div>
@endsection
