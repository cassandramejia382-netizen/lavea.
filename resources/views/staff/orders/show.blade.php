@extends('staff.layout', ['title' => 'Record Details'])
@section('content')
<style>
.record-back-link { display:inline-flex;align-items:center;gap:7px;padding:9px 12px;border:1px solid #dfe5ef;border-radius:9px;background:#fff;color:#4169c8;font-size:12px;font-weight:600;text-decoration:none;white-space:nowrap; }
.record-back-link svg { width:16px;height:16px; }
.record-back-link:hover { background:#edf2ff;border-color:#cbd7f2; }
.record-back-link:focus-visible { outline:2px solid #7393e6;outline-offset:3px; }
html[data-lavea-theme="dark"] .record-back-link { background:#1f2937;color:#bfdbfe; }
@media(max-width:600px) { .record-back-link { min-height:44px; } }
</style>
<div class="page-head"><div><h1>Record #LVE-{{ str_pad($order->id, 4, '0', STR_PAD_LEFT) }}</h1><div class="muted">Record details, fulfillment, and payment.</div></div><a class="record-back-link" href="{{ route('staff.orders.index') }}"><i data-lucide="arrow-left" aria-hidden="true"></i>Back to Records</a></div>
<div class="card"><div class="grid" style="grid-template-columns:repeat(3,1fr)">@foreach([['Customer',$order->customer->name ?? '—'],['Service',$order->service->service_name ?? '—'],['Quantity / Weight',$order->quantity.' '.($order->service?->billingUnit() ?? 'kg')],['Record Date',$order->order_date?->format('M d, Y') ?? '—'],['Pickup Date',$order->pickup_date?->format('M d, Y') ?? '—'],['Delivery Date',$order->delivery_date?->format('M d, Y') ?? '—'],['Record Status',$order->status],['Payment Status',$order->payment_status],['Total','₱'.number_format($order->total,2)]] as [$label,$value])<div><div class="muted">{{ $label }}</div><strong>{{ $value }}</strong></div>@endforeach</div><div style="margin-top:16px"><div class="muted">Notes</div>{{ $order->notes ?: 'No notes' }}</div></div>
@endsection
