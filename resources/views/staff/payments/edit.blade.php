@extends('staff.layout', ['title' => 'Edit Payment'])
@section('content')
<div class="page-head"><div><h1>Edit Payment</h1><div class="muted">Correct the rejected transaction and submit it for Admin approval.</div></div></div>
<form class="card form-grid" method="POST" enctype="multipart/form-data" action="{{ route('manager.payments.update', $payment) }}">@csrf
@method('PUT')
<div class="field full"><label>Order</label><strong>#LVE-{{ str_pad($payment->order_id, 4, '0', STR_PAD_LEFT) }} &mdash; {{ $payment->order->customer->name ?? 'Customer' }}</strong></div>
<div class="field"><label for="amount">Amount Paid (₱) *</label><input id="amount" type="number" name="amount" min="0.01" step="0.01" required value="{{ old('amount', $payment->amount) }}"></div>
<div class="field"><label for="payment_method">Payment Method *</label><select id="payment_method" name="payment_method" required>@foreach(['Cash','GCash','Card','Bank Transfer'] as $method)<option @selected(old('payment_method', $payment->payment_method)===$method)>{{ $method }}</option>@endforeach</select></div>
@include('partials.gcash-payment')
<div class="field"><label for="reference_number">Reference Number (required for transfers)</label><input id="reference_number" name="reference_number" maxlength="255" value="{{ old('reference_number', $payment->reference_number) }}"></div>
<div class="field"><label for="payment_date">Payment Date *</label><input id="payment_date" type="date" name="payment_date" max="{{ today()->toDateString() }}" required value="{{ old('payment_date', $payment->payment_date?->toDateString()) }}"></div>
<div class="field"><label>Payment Status</label><span class="muted">Pending - waiting for Admin approval</span></div>
<div class="field full"><label for="proof_image">Proof of Payment</label><input id="proof_image" type="file" name="proof_image" accept="image/jpeg,image/png,image/webp"><small class="muted">JPG, PNG, or WEBP up to 10 MB. Required for GCash and Bank Transfer unless an existing proof is shown below.</small>@error('proof_image')<span class="error-list">{{ $message }}</span>@enderror</div>
<div class="field full"><label for="notes">Notes</label><textarea id="notes" name="notes">{{ old('notes', $payment->notes) }}</textarea></div>
<div class="actions field full"><button class="btn">Resubmit Payment</button><a class="btn secondary" href="{{ route('manager.payments.index') }}">Cancel</a></div></form>
@include('partials.payment-proof', ['payment' => $payment])
@endsection
