@extends('staff.layout', ['title' => isset($order) && $order->exists ? 'Edit Record' : 'Create Record'])
@section('content')
@php
    $editing = isset($order) && $order->exists;
    $assignmentLocked = $editing && $order->status !== 'Pending';
    $assignmentConfig = ['locked' => $assignmentLocked, 'originalServiceId' => $order->service_id, 'originalStaffId' => $order->staff_id];
@endphp
<div class="page-head"><div><h1>{{ $editing ? 'Edit Record' : 'Create Laundry Record' }}</h1><div class="muted">Search and select a registered customer.</div></div></div>
<form class="card form-grid" method="POST" action="{{ $editing ? route('manager.orders.update', $order) : route('manager.orders.store') }}">@csrf @if($editing)@method('PUT')@endif
<div class="field"><label for="customer_id">Customer *</label><select id="customer_id" name="customer_id" required><option value="">Select a customer</option>@foreach($customers as $customer)<option value="{{ $customer->id }}" @selected(old('customer_id', request('customer_id', $order->customer_id ?? '')) == $customer->id)>{{ $customer->name }} {{ $customer->phone ? '· '.$customer->phone : '' }}</option>@endforeach</select><small><a href="{{ route('manager.customers.index') }}">Search customers</a></small></div>
<div class="field"><label for="service_id">Laundry Service *</label><select id="service_id" name="service_id" required><option value="">Select a service</option>@foreach($services as $service)<option value="{{ $service->id }}" data-price="{{ $service->price }}" data-unit="{{ $service->billingUnit() }}" data-staff-url="{{ route('manager.services.staff-options', $service) }}" data-config-url="{{ route('manager.services.show', $service) }}" @selected(old('service_id', $order->service_id ?? '') == $service->id)>{{ $service->service_name }} — ₱{{ number_format($service->price, 2) }}</option>@endforeach</select></div>
<div class="field"><label for="quantity">Quantity / Weight *</label><input id="quantity" type="number" name="quantity" min="1" step="0.01" required value="{{ old('quantity', $order->quantity ?? 1) }}"></div><div class="field"><label>Estimated Total</label><strong id="total-preview">₱0.00</strong></div>
<div class="field"><label for="order_date">Record Date *</label><input id="order_date" type="date" name="order_date" required value="{{ old('order_date', isset($order) && $order->order_date ? $order->order_date->format('Y-m-d') : today()->toDateString()) }}"></div>
<div class="field"><label for="pickup_date">Pickup Date</label><input id="pickup_date" type="date" name="pickup_date" min="{{ today()->toDateString() }}" value="{{ old('pickup_date', isset($order) && $order->pickup_date ? $order->pickup_date->format('Y-m-d') : '') }}"></div>
<div class="field"><label for="delivery_date">Delivery Date</label><input id="delivery_date" type="date" name="delivery_date" min="{{ today()->toDateString() }}" value="{{ old('delivery_date', isset($order) && $order->delivery_date ? $order->delivery_date->format('Y-m-d') : '') }}"></div>
<div class="field"><label for="status">Status *</label><select id="status" name="status" required>@foreach(\App\Models\Order::STATUSES as $status)<option @selected(old('status', $order->status ?? 'Pending') === $status)>{{ $status }}</option>@endforeach</select></div>
<div class="field"><label for="assigned_staff">Assigned Staff</label>
    <input id="assigned_staff" type="text" value="{{ $editing ? $order->staff?->name : '' }}" placeholder="Select a service first" aria-describedby="assignment-message" readonly>
    <small id="assignment-message" role="status" aria-live="polite">Staff is assigned automatically based on service and workload.</small>
    <a id="assignment-config-link" hidden>Configure required job position</a>
    @unless($assignmentLocked)<div><button type="button" id="refresh-assignment" class="btn secondary">Refresh Assignment</button></div>@endunless
    <small><a href="{{ route('manager.staff.index') }}">Manage staff availability and job positions</a>. The Manager informs the assigned worker and updates progress.</small>
    @if($assignmentLocked)<input type="hidden" name="service_id" value="{{ $order->service_id }}">@endif
</div>
<div class="field full"><label for="notes">Notes</label><textarea id="notes" name="notes">{{ old('notes', $order->notes ?? '') }}</textarea></div>
<div class="actions field full"><button class="btn">{{ $editing ? 'Save Record' : 'Create Record' }}</button><a class="btn secondary" href="{{ route('manager.orders.index') }}">Cancel</a></div>
</form>

<script id="staff-assignment-config" type="application/json">@json($assignmentConfig)</script>
<script src="{{ asset('order-staff-assignment.js') }}?v={{ filemtime(public_path('order-staff-assignment.js')) }}" defer></script>
<script>const service=document.getElementById('service_id'),quantity=document.getElementById('quantity'),preview=document.getElementById('total-preview');function updateTotal(){const unit=service.selectedOptions[0]?.dataset.unit||'kg';quantity.step=unit==='item'?'1':'0.01';document.querySelector('label[for="quantity"]').textContent=unit==='kg'?'Weight (kg) *':unit==='set'?'Number of sets *':'Number of items *';const price=Number(service.selectedOptions[0]?.dataset.price||0);preview.textContent='₱'+(price*Number(quantity.value||0)).toLocaleString('en-PH',{minimumFractionDigits:2,maximumFractionDigits:2})}service.addEventListener('change',updateTotal);quantity.addEventListener('input',updateTotal);updateTotal();</script>
@endsection
