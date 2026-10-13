@extends('admin.layout', ['title' => 'My Profile'])
@section('content')
@include('partials.profile-form', ['profileRole' => 'admin', 'profileRoleLabel' => 'Administrator', 'profilePhone' => $profilePhone, 'profileAddress' => $profileAddress])
@endsection
