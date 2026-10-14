@extends(($routePrefix ?? 'manager') === 'admin' ? 'admin.layout' : 'staff.layout', ['title' => $customer->exists ? 'Edit Customer' : 'Add Customer'])
@php($customerRoutePrefix = $routePrefix ?? 'manager')
@section('content')
@if($customerRoutePrefix === 'admin')
<style>
.lavea-admin-content .page-head { display:flex;align-items:center;justify-content:space-between;gap:16px;margin-bottom:24px; }
.lavea-admin-content .form-grid { display:grid;grid-template-columns:repeat(2,minmax(0,1fr));gap:18px;padding:24px;background:white;border:1px solid #e7ebf2;border-radius:10px; }
.lavea-admin-content .field.full { grid-column:1/-1; }
.lavea-admin-content .field label { display:block;margin-bottom:8px; }
.lavea-admin-content .field input,.lavea-admin-content .field textarea { width:100%;box-sizing:border-box;padding:11px;border:1px solid #dfe5ef;border-radius:7px;font:inherit; }
.lavea-admin-content .actions { display:flex;gap:10px; }
.lavea-admin-content .btn { padding:10px 16px;border:0;border-radius:7px;background:#4169c8;color:white;text-decoration:none;cursor:pointer; }
.lavea-admin-content .error-list { color:#e05263;margin-bottom:12px; }
@media(max-width:600px) { .lavea-admin-content .form-grid { grid-template-columns:1fr; } }
</style>
@if($errors->any())<div role="alert" class="error-list">@foreach($errors->all() as $message)<div>{{ $message }}</div>@endforeach</div>@endif
@endif
<div class="page-head"><div><h1>{{ $customer->exists ? 'Edit Customer' : 'Register Customer' }}</h1><div class="muted">Customer contact and address details.</div></div><a href="{{ route($customerRoutePrefix.'.customers.index') }}" class="lavea-back-link"><svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.8" stroke-linecap="round" stroke-linejoin="round" aria-hidden="true"><path d="m12 19-7-7 7-7M5 12h14" /></svg>Back</a></div>
<form class="card form-grid" method="POST" action="{{ $customer->exists ? route($customerRoutePrefix.'.customers.update', $customer) : route('manager.customers.store') }}">@csrf @if($customer->exists)@method('PUT')@endif
<div class="field full"><label for="name">Customer Name *</label><input id="name" name="name" required value="{{ old('name', $customer->name) }}"></div>
<div class="field"><label for="email">Email</label><input id="email" type="email" name="email" value="{{ old('email', $customer->email) }}" @readonly($customerRoutePrefix === 'admin' && $customer->user !== null)>@error('email')<div class="error-list">{{ $message }}</div>@enderror</div>
<div class="field"><label for="phone">Phone Number</label><input id="phone" name="phone" value="{{ old('phone', $customer->phone) }}"></div>
<div class="field full"><label for="address">Address</label><textarea id="address" name="address">{{ old('address', $customer->address) }}</textarea></div>
<div class="actions field full"><button class="btn">{{ $customer->exists ? 'Save Changes' : 'Add Customer' }}</button><a class="btn secondary" href="{{ route($customerRoutePrefix.'.customers.index') }}">Cancel</a></div></form>
@endsection

