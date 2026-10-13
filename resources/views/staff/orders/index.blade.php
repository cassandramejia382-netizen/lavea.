@extends('staff.layout', ['title' => 'Records'])
@section('content')
<div class="page-head"><div><h1>Records</h1><div class="muted">Create and track customer laundry records.</div></div><a class="btn" href="{{ route('manager.orders.create') }}">+ Create Record</a></div>
<form method="GET" action="{{ route('manager.orders.index') }}" style="display:flex;gap:8px;margin-bottom:17px"><input class="search-input" name="search" value="{{ $search }}" placeholder="Record number, customer, service or status"><button class="btn">Search</button></form>
<div class="card table-wrap">@include('staff.partials.orders-table', ['orders' => $orders])</div><div style="margin-top:15px">{{ $orders->links('customer.pagination') }}</div>
@endsection
