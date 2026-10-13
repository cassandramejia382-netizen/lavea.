@extends('staff.layout', ['title' => 'Record Payment'])
@section('content')
<div class="page-head"><div><h1>Record Payment</h1><div class="muted">Connect this transaction to its customer order.</div></div></div>
<form class="card form-grid" method="POST" enctype="multipart/form-data" action="{{ route('manager.payments.store') }}">@csrf
<div class="field full"><label for="order_id">Order *</label><select id="order_id" name="order_id" required><option value="">Select an order</option>@foreach($orders as $order)<option value="{{ $order->id }}" data-total="{{ $order->total }}" @selected(old('order_id',request('order_id'))==$order->id)>#LVE-{{ str_pad($order->id,4,'0',STR_PAD_LEFT) }} — {{ $order->customer->name ?? 'Customer' }} · {{ $order->service->service_name ?? 'Service' }} · Due ₱{{ number_format($order->total,2) }}</option>@endforeach</select></div>
<div class="field"><label for="amount">Amount Paid (₱) *</label><input id="amount" type="number" name="amount" min="0.01" step="0.01" required value="{{ old('amount') }}"></div>
<div class="field"><label for="payment_method">Payment Method *</label><select id="payment_method" name="payment_method" required>@foreach(['Cash','GCash','Card','Bank Transfer'] as $method)<option @selected(old('payment_method','Cash')===$method)>{{ $method }}</option>@endforeach</select></div>
@include('partials.gcash-payment')
<div class="field"><label for="reference_number">Reference Number (required for transfers)</label><input id="reference_number" name="reference_number" maxlength="255" value="{{ old('reference_number') }}"></div>
<div class="field"><label for="payment_date">Payment Date *</label><input id="payment_date" type="date" name="payment_date" max="{{ today()->toDateString() }}" required value="{{ old('payment_date',today()->toDateString()) }}"></div>
<div class="field"><label>Payment Status</label><span class="muted">Pending - waiting for Admin approval</span></div>
<div class="field full"><label for="proof_image">Proof of Payment</label><input id="proof_image" type="file" name="proof_image" accept="image/jpeg,image/png,image/webp"><small class="muted">JPG, PNG, or WEBP up to 10 MB. Required for GCash and Bank Transfer.</small>@error('proof_image')<span class="error-list">{{ $message }}</span>@enderror</div>
<div class="field full"><label for="notes">Notes</label><textarea id="notes" name="notes">{{ old('notes') }}</textarea></div>
<div class="actions field full"><button class="btn">Save Payment</button><a class="btn secondary" href="{{ route('manager.payments.index') }}">Cancel</a></div></form>
@endsection
