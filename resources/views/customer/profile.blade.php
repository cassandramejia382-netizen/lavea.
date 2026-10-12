@extends('customer.layout', ['title' => 'My Profile'])
@section('content')
@include('partials.profile-form', ['profileRole' => 'customer', 'profileRoleLabel' => 'Customer Account', 'profilePhone' => $customer?->phone, 'profileAddress' => $customer?->address, 'profileLinked' => $customer !== null])
@endsection