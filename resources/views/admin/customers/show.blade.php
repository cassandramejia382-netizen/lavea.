@extends('admin.layout', ['title' => 'Customer Details'])

@push('styles')

<style>
.lavea-admin-content .container { max-width: 720px; margin: 32px auto; padding: 0 20px; }
.lavea-admin-content .back { display: inline-block; color: #4169c8; text-decoration: none; font-size: 13px; line-height: 1.5; }
.lavea-admin-content .back:hover { text-decoration: underline; }
.lavea-admin-content .back:focus-visible { outline: 2px solid #4169c8; outline-offset: 4px; border-radius: 3px; }
.lavea-admin-content .card { margin-top: 16px; background: white; border: 1px solid #e5e9f1; border-radius: 12px; padding: 24px; }
.lavea-admin-content .customer-header { display: flex; align-items: center; gap: 14px; padding-bottom: 20px; border-bottom: 1px solid #e5e9f1; }
.lavea-admin-content .avatar { flex: 0 0 52px; width: 52px; height: 52px; background: #e8eefb; color: #4169c8; border-radius: 50%; display: flex; align-items: center; justify-content: center; }
.lavea-admin-content .avatar svg { width: 26px; height: 26px; stroke-width: 2; }
.lavea-admin-content h1 { min-width: 0; margin: 0; font-size: 26px; line-height: 1.25; overflow-wrap: anywhere; }
.lavea-admin-content .details { display: grid; grid-template-columns: minmax(0, 1fr) minmax(0, 1fr); column-gap: 28px; margin: 0; }
.lavea-admin-content .detail { min-width: 0; padding: 16px 0; border-bottom: 1px solid #edf0f5; }
.lavea-admin-content .detail:last-child { border-bottom: 0; }
.lavea-admin-content .label { color: #64748b; font-size: 11px; font-weight: 600; letter-spacing: .4px; margin-bottom: 6px; }
.lavea-admin-content .value { margin: 0; color: #263552; font-size: 14px; font-weight: 500; line-height: 1.5; overflow-wrap: anywhere; }
.lavea-admin-content .customer-updated { margin: 0; padding-top: 14px; border-top: 1px solid #e5e9f1; color: #64748b; font-size: 12px; line-height: 1.5; }
@media (max-width: 600px) {
.lavea-admin-content .container { margin: 24px auto; padding: 0 16px; }
.lavea-admin-content .card { padding: 20px; }
.lavea-admin-content .details { grid-template-columns: minmax(0, 1fr); }
.lavea-admin-content h1 { font-size: 23px; }
}
</style>
@endpush

@section('content')
<div class="container">

    <a
        href="{{ route('admin.customers.index') }}"
        class="back"
    >
        &larr; Back to Customers
    </a>


    <div class="card">
        <div class="customer-header">
            <div class="avatar" aria-hidden="true"><i data-lucide="user"></i></div>
            <h1>{{ $customer->name }}</h1>
        </div>

        <dl class="details">
            <div class="detail">
                <dt class="label">CUSTOMER ID</dt>
                <dd class="value">{{ $customer->id }}</dd>
            </div>
            <div class="detail">
                <dt class="label">PHONE NUMBER</dt>
                <dd class="value">{{ $customer->phone ?: 'No phone provided' }}</dd>
            </div>
            <div class="detail">
                <dt class="label">EMAIL</dt>
                <dd class="value">{{ $customer->email ?: 'No email provided' }}</dd>
            </div>
            <div class="detail">
                <dt class="label">ADDRESS</dt>
                <dd class="value">{{ $customer->address ?: 'No address provided' }}</dd>
            </div>
            <div class="detail">
                <dt class="label">CUSTOMER SINCE</dt>
                <dd class="value">{{ $customer->created_at?->format('F d, Y') ?: 'Not available' }}</dd>
            </div>
        </dl>

        <p class="customer-updated muted">Last updated: {{ $customer->updated_at?->format('F d, Y') ?: 'Not available' }}</p>
    </div>
</div>
@endsection
