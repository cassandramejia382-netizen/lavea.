@extends('customer.layout', ['title' => 'My Orders'])
@section('content')
<div class="page-head"><div><h1>My Orders</h1><div class="muted">Follow the progress of your laundry orders.</div></div></div>
<div class="card">@include('customer.orders-table')<div style="margin-top:18px">{{ $orders->links('customer.pagination') }}</div></div>
@endsection
