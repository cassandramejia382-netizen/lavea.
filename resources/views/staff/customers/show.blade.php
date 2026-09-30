@extends('staff.layout', ['title' => 'Customer Details'])
@section('content')
<div class="page-head"><div><h1>{{ $customer->name }}</h1><div class="muted">Customer details and order history.</div></div><div class="actions"><a class="btn secondary" href="{{ route('staff.customers.edit', $customer) }}">Edit</a><a class="btn" href="{{ route('staff.orders.create', ['customer_id' => $customer->id]) }}">Create Order</a></div></div>
<div class="card" style="margin-bottom:18px"><div class="grid" style="grid-template-columns:repeat(3,1fr);margin:0"><div><div class="muted">Email</div><strong>{{ $customer->email ?: '—' }}</strong></div><div><div class="muted">Phone</div><strong>{{ $customer->phone ?: '—' }}</strong></div><div><div class="muted">Address</div><strong>{{ $customer->address ?: '—' }}</strong></div></div></div>
<div class="card"><h2>Order History</h2>@include('staff.partials.orders-table', ['orders' => $customer->orders])</div>
@endsection
