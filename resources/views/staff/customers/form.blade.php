@extends('staff.layout', ['title' => $customer->exists ? 'Edit Customer' : 'Add Customer'])
@section('content')
<div class="page-head"><div><h1>{{ $customer->exists ? 'Edit Customer' : 'Register Customer' }}</h1><div class="muted">Customer contact and address details.</div></div><a class="btn secondary" href="{{ route('staff.customers.index') }}">Back</a></div>
<form class="card form-grid" method="POST" action="{{ $customer->exists ? route('staff.customers.update', $customer) : route('staff.customers.store') }}">@csrf @if($customer->exists)@method('PUT')@endif
<div class="field full"><label for="name">Customer Name *</label><input id="name" name="name" required value="{{ old('name', $customer->name) }}"></div>
<div class="field"><label for="email">Email</label><input id="email" type="email" name="email" value="{{ old('email', $customer->email) }}"></div>
<div class="field"><label for="phone">Phone Number</label><input id="phone" name="phone" value="{{ old('phone', $customer->phone) }}"></div>
<div class="field full"><label for="address">Address</label><textarea id="address" name="address">{{ old('address', $customer->address) }}</textarea></div>
<div class="actions field full"><button class="btn">{{ $customer->exists ? 'Save Changes' : 'Add Customer' }}</button><a class="btn secondary" href="{{ route('staff.customers.index') }}">Cancel</a></div></form>
@endsection
