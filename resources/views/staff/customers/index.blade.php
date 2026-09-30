@extends('staff.layout', ['title' => 'Customers'])
@section('content')
<div class="page-head"><div><h1>Customers</h1><div class="muted">Find customers, view their orders, or register walk-ins.</div></div><a class="btn" href="{{ route('staff.customers.create') }}">+ Add Customer</a></div>
<form method="GET" action="{{ route('staff.customers.index') }}" style="display:flex;gap:8px;margin-bottom:17px"><input class="search-input" type="search" name="search" value="{{ $search }}" placeholder="Name, email or phone"><button class="btn">Search</button></form>
<div class="card table-wrap"><table><thead><tr><th>Name</th><th>Email</th><th>Phone</th><th>Address</th><th></th></tr></thead><tbody>@forelse($customers as $customer)<tr><td>{{ $customer->name }}</td><td>{{ $customer->email ?: '—' }}</td><td>{{ $customer->phone ?: '—' }}</td><td>{{ $customer->address ?: '—' }}</td><td><div class="actions"><a class="btn secondary" href="{{ route('staff.customers.show', $customer) }}">View</a><a class="btn secondary" href="{{ route('staff.customers.edit', $customer) }}">Edit</a><a class="btn" href="{{ route('staff.orders.create', ['customer_id' => $customer->id]) }}">New Order</a></div></td></tr>@empty<tr><td colspan="5" class="empty">No customers found.</td></tr>@endforelse</tbody></table></div>
<div style="margin-top:15px">{{ $customers->links() }}</div>
@endsection
