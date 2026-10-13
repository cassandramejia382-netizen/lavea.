@extends('staff.layout', ['title' => 'Customers'])
@section('content')
<style>
.customer-icon-actions {
    display: flex;
    align-items: center;
    gap: 7px;
}
.customer-icon-actions .customer-action {
    display: inline-flex;
    align-items: center;
    justify-content: center;
    width: 34px;
    height: 34px;
    padding: 0;
    border: 0;
    border-radius: 7px;
    text-decoration: none;
    cursor: pointer;
}
.customer-icon-actions .customer-action svg {
    width: 16px;
    height: 16px;
}
.customer-icon-actions .customer-view {
    background: #edf3ff;
    color: #3973e6;
}
.customer-icon-actions .customer-delete {
    background: #fff0f1;
    color: #e05263;
}
.customer-icon-actions .customer-action:hover {
    filter: brightness(.96);
}
.customer-icon-actions .customer-action:focus-visible {
    outline: 2px solid #4169c8;
    outline-offset: 3px;
}
</style>
<div class="page-head"><div><h1>Customers</h1><div class="muted">Find customers, view their orders, or register walk-ins.</div></div><a class="btn" href="{{ route('staff.customers.create') }}">+ Add Customer</a></div>
<form method="GET" action="{{ route('staff.customers.index') }}" style="display:flex;gap:8px;margin-bottom:17px"><input class="search-input" type="search" name="search" value="{{ $search }}" placeholder="Name, email or phone"><button class="btn">Search</button></form>
<div class="card table-wrap"><table><thead><tr><th>Name</th><th>Email</th><th>Phone</th><th>Address</th><th></th></tr></thead><tbody>@forelse($customers as $customer)<tr><td>{{ $customer->name }}</td><td>{{ $customer->email ?: '—' }}</td><td>{{ $customer->phone ?: '—' }}</td><td>{{ $customer->address ?: '—' }}</td><td><div class="customer-icon-actions"><a class="customer-action customer-view" href="{{ route('staff.customers.show', $customer) }}" aria-label="View {{ $customer->name }}" title="View customer"><i data-lucide="eye" aria-hidden="true"></i></a><form method="POST" action="{{ route('staff.customers.destroy', $customer) }}" onsubmit="return confirm('Delete this customer? Customers with order history cannot be deleted.')">@csrf @method('DELETE')<button class="customer-action customer-delete" type="submit" aria-label="Delete {{ $customer->name }}" title="Delete customer"><i data-lucide="trash-2" aria-hidden="true"></i></button></form></div></td></tr>@empty<tr><td colspan="5" class="empty">No customers found.</td></tr>@endforelse</tbody></table></div>
<div style="margin-top:15px">{{ $customers->links() }}</div>
@endsection
