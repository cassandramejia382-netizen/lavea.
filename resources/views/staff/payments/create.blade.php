@extends('staff.layout', ['title' => 'Record Payment'])
@section('content')
<div class="page-head"><div><h1>Record Payment</h1><div class="muted">Connect this transaction to its customer order.</div></div></div>
<form class="card form-grid" method="POST" action="{{ route('staff.payments.store') }}">@csrf
<div class="field full"><label for="order_id">Order *</label><select id="order_id" name="order_id" required><option value="">Select an order</option>@foreach($orders as $order)<option value="{{ $order->id }}" data-total="{{ $order->total }}" @selected(old('order_id',request('order_id'))==$order->id)>#LVE-{{ str_pad($order->id,4,'0',STR_PAD_LEFT) }} — {{ $order->customer->name ?? 'Customer' }} · {{ $order->service->service_name ?? 'Service' }} · Due ₱{{ number_format($order->total,2) }}</option>@endforeach</select></div>
<div class="field"><label for="amount">Amount Paid (₱) *</label><input id="amount" type="number" name="amount" min="0.01" step="0.01" required value="{{ old('amount') }}"></div>
<div class="field"><label for="payment_method">Payment Method *</label><select id="payment_method" name="payment_method" required>@foreach(['Cash','GCash','Card','Bank Transfer'] as $method)<option @selected(old('payment_method','Cash')===$method)>{{ $method }}</option>@endforeach</select></div>
<div class="field"><label for="reference_number">Reference Number</label><input id="reference_number" name="reference_number" value="{{ old('reference_number') }}"></div>
<div class="field"><label for="payment_date">Payment Date *</label><input id="payment_date" type="date" name="payment_date" max="{{ today()->toDateString() }}" required value="{{ old('payment_date',today()->toDateString()) }}"></div>
<div class="field"><label for="status">Payment Status *</label><select id="status" name="status" required>@foreach(['Completed','Pending','Failed'] as $status)<option @selected(old('status','Completed')===$status)>{{ $status }}</option>@endforeach</select></div>
<div class="field full"><label for="notes">Notes</label><textarea id="notes" name="notes">{{ old('notes') }}</textarea></div>
<div class="actions field full"><button class="btn">Save Payment</button><a class="btn secondary" href="{{ route('staff.payments.index') }}">Cancel</a></div></form>
@endsection
