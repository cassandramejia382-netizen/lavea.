@extends('customer.layout', ['title' => 'Edit Order'])
@section('content')
<div class="page-head"><div><h1>Edit Order #{{ $order->id }}</h1><div class="muted">{{ $order->service->service_name }}</div></div><a class="btn secondary" href="{{ route('customer.orders.index') }}">&larr; Back to Records</a></div>
<form class="card form-grid" method="POST" action="{{ route('customer.orders.update', $order) }}">
    @csrf
    @method('PUT')
    <div class="field">
        <label for="quantity">{{ $order->service->quantityLabel() }} ({{ $order->service->billingUnit() }})</label>
        <input id="quantity" name="quantity" type="number" min="1" @if($order->service->billingUnit() === 'kg') max="9" @endif step="{{ $order->service->billingUnit() === 'kg' ? '0.01' : '1' }}" value="{{ old('quantity', $order->quantity) }}" required>
        @error('quantity')<span class="error-list">{{ $message }}</span>@enderror
    </div>
    <div class="field">
        <label for="pickup_date">Pickup date</label>
        <input id="pickup_date" name="pickup_date" type="date" min="{{ today()->toDateString() }}" value="{{ old('pickup_date', $order->pickup_date?->toDateString()) }}" required>
        @error('pickup_date')<span class="error-list">{{ $message }}</span>@enderror
    </div>
    <div class="field full">
        <label for="notes">Special instructions</label>
        <textarea id="notes" name="notes" maxlength="2000">{{ old('notes', $order->notes) }}</textarea>
        @error('notes')<span class="error-list">{{ $message }}</span>@enderror
    </div>
    <div class="field full"><span class="muted">Price: &#8369;{{ number_format($order->service->price, 2) }} per {{ $order->service->billingUnit() }}. The total is recalculated when you save.</span></div>
    <div class="actions field full"><button class="btn" type="submit">Save changes</button><a class="btn secondary" href="{{ route('customer.orders.index') }}">Cancel editing</a></div>
</form>
@endsection
