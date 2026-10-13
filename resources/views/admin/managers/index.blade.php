@extends('admin.layout', ['title' => 'Manager Accounts'])
@push('styles')
<link rel="stylesheet" href="{{ asset('css/lavea-settings.css') }}?v={{ filemtime(public_path('css/lavea-settings.css')) }}">
<style>
.manager-heading{display:flex;align-items:center;justify-content:space-between;gap:12px;flex-wrap:wrap;margin-bottom:24px}.manager-heading h1{font-size:26px;margin:0 0 7px}.manager-heading p{margin:0;color:#7a879d;font-size:13px}.manager-table{width:100%;border-collapse:collapse;font-size:14px}.manager-table th,.manager-table td{padding:14px;text-align:left;border-bottom:1px solid #e8ecf3}.manager-table th{color:#7a879d;font-size:12px}.manager-actions{display:flex;gap:10px;align-items:center}.manager-actions .btn{text-decoration:none;display:inline-flex;align-items:center;justify-content:center}.manager-search{display:flex;gap:10px;flex-wrap:wrap;margin-bottom:18px}.manager-search input{min-width:0;flex:1;padding:10px 12px;border:1px solid #dce3ef;border-radius:8px;font:inherit}.manager-heading .btn{white-space:nowrap;text-decoration:none}
</style>
@endpush
@section('content')
<div class="manager-heading"><div><h1>Manager Accounts</h1><p>Manage dashboard login accounts separately from laundry staff records.</p></div><a class="btn btn-primary" href="{{ route('admin.managers.create') }}">Add Manager</a></div>
@if(session('success'))<div class="setting-message" role="status">{{ session('success') }}</div>@endif
<section class="settings-card manager-accounts">
<form class="manager-search" method="GET" action="{{ route('admin.managers.index') }}"><input type="search" name="search" value="{{ $search }}" placeholder="Search name or email" aria-label="Search managers"><button class="btn btn-primary" type="submit">Search</button></form>
<div class="table-wrap"><table class="manager-table"><thead><tr><th>Name</th><th>Email</th><th>Phone</th><th>Login Role</th><th>Actions</th></tr></thead><tbody>@forelse($managers as $manager)<tr><td>{{ $manager->name }}</td><td>{{ $manager->email }}</td><td>{{ $manager->phone ?: '?' }}</td><td>Manager</td><td><div class="manager-actions"><a class="lavea-back-link" href="{{ route('admin.managers.edit', $manager) }}">Edit Account</a></div></td></tr>@empty<tr><td colspan="5">No manager accounts found.</td></tr>@endforelse</tbody></table></div>
{{ $managers->links() }}
</section>
@endsection
