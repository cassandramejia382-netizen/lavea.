@extends('customer.layout', ['title' => 'Services'])
@section('content')
<div class="page-head"><div><h1>Laundry Services</h1><div class="muted">Explore our laundry services, availability and current prices.</div></div></div>
@include('customer.services-grid')
<div style="margin-top:18px">{{ $services->links('customer.pagination') }}</div>
@endsection
