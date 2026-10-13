@extends('staff.layout', ['title' => 'My Profile'])
@section('content')
@include('partials.profile-form', ['profileRole' => 'manager', 'profileRoleLabel' => 'Manager Account', 'profilePhone' => $profilePhone, 'profileAddress' => $profileAddress])
@endsection
