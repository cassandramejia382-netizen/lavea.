@extends('staff.layout', ['title' => 'Edit Payment'])
@section('content')
<div class="page-head"><div><h1>Edit Payment</h1><div class="muted">Update transaction details and payment status.</div></div></div>
<form class="card form-grid" method="POST" action="{{ route('staff.payments.update', $payment) }}">@csrf
@method('PUT')
<div class="field full"><label>Order</label><strong>#LVE-{{ str_pad($payment->order_id, 4, '0', STR_PAD_LEFT) }} &mdash; {{ $payment->order->customer->name ?? 'Customer' }}</strong></div>
<div class="field"><label for="amount">Amount Paid (₱) *</label><input id="amount" type="number" name="amount" min="0.01" step="0.01" required value="{{ old('amount', $payment->amount) }}"></div>
<div class="field"><label for="payment_method">Payment Method *</label><select id="payment_method" name="payment_method" required>@foreach(['Cash','GCash','Card','Bank Transfer'] as $method)<option @selected(old('payment_method', $payment->payment_method)===$method)>{{ $method }}</option>@endforeach</select></div>
<div class="field"><label for="reference_number">Reference Number</label><input id="reference_number" name="reference_number" value="{{ old('reference_number', $payment->reference_number) }}"></div>
<div class="field"><label for="payment_date">Payment Date *</label><input id="payment_date" type="date" name="payment_date" max="{{ today()->toDateString() }}" required value="{{ old('payment_date', $payment->payment_date?->toDateString()) }}"></div>
<div class="field"><label for="status">Payment Status *</label><select id="status" name="status" required>@foreach(['Completed','Pending','Failed'] as $status)<option @selected(old('status', $payment->status)===$status)>{{ $status }}</option>@endforeach</select></div>
<div class="field full"><label for="notes">Notes</label><textarea id="notes" name="notes">{{ old('notes', $payment->notes) }}</textarea></div>
<div class="actions field full"><button class="btn">Save Payment</button><a class="btn secondary" href="{{ route('staff.payments.index') }}">Cancel</a></div></form>
@endsection
