@extends('staff.layout', ['title' => 'My Profile'])
@section('content')
@include('partials.profile-form', ['profileRole' => 'staff', 'profileRoleLabel' => 'Staff Account', 'profilePhone' => $staff?->phone, 'profileAddress' => $profileAddress])
@endsection