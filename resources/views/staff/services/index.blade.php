@extends('staff.layout', ['title' => 'Services'])
@section('content')
<div class="page-head"><div><h1>Laundry Services</h1><div class="muted">Services and pricing configured by the administrator.</div></div></div>
<div class="service-grid">@forelse($services as $service)<article class="card service-card">
@if($imageUrl = $service->imageUrl())<img src="{{ $imageUrl }}" alt="{{ $service->service_name }}" loading="lazy" style="display:block;width:100%;height:140px;object-fit:cover;background:#e7efff">@else<div class="service-placeholder" style="height:140px;background:#e7efff;display:grid;place-items:center;color:#4169c8"><i data-lucide="washing-machine" style="width:40px;height:40px"></i></div>@endif
<div class="service-body"><div class="actions" style="justify-content:space-between"><h2 style="margin:0">{{ $service->service_name }}</h2><span class="pill" data-status="Active">Active</span></div><p class="muted" style="line-height:1.5;margin:10px 0 14px">{{ $service->description ?: 'No description provided.' }}</p><strong>₱{{ number_format($service->price, 2) }}</strong></div></article>@empty<div class="card empty">No services are currently available.</div>@endforelse</div>
@endsection
