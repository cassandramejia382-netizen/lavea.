@extends('staff.layout', ['title' => $service->service_name])

@section('content')
<div class="page-head">
    <div>
        <h1>{{ $service->service_name }}</h1>
        <div class="muted">Service details and pricing</div>
    </div>
    <a class="btn secondary" href="{{ route('staff.services.index') }}">&larr; Back to Services</a>
</div>

<article class="card" style="display:grid;grid-template-columns:minmax(0,360px) minmax(0,1fr);gap:30px;align-items:center;max-width:1050px;padding:28px">
    @if ($imageUrl = $service->imageUrl())
        <img src="{{ $imageUrl }}" alt="{{ $service->service_name }}" style="display:block;width:100%;height:280px;object-fit:contain;background:#f5f7fb;border-radius:12px">
    @else
        <div style="height:280px;background:#e7efff;border-radius:12px;display:grid;place-items:center;color:#4169c8">
            <i data-lucide="washing-machine" style="width:48px;height:48px"></i>
        </div>
    @endif

    <div>
        <span class="pill" data-status="Active">Service</span>
        <h2 style="margin:14px 0 10px;color:#12214a">{{ $service->service_name }}</h2>
        <p class="muted" style="line-height:1.7;margin:0 0 22px">{{ $service->description ?: 'No description provided.' }}</p>
        <div class="muted" style="font-size:12px;margin-bottom:4px">PRICE</div>
        <strong style="font-size:24px;color:#12214a">₱{{ number_format($service->price, 2) }}</strong>
    </div>
</article>

<style>
    @media (max-width: 700px) {
        .content article.card[style*="grid-template-columns"] {
            grid-template-columns: minmax(0, 1fr) !important;
            gap: 20px !important;
        }
    }
</style>
@endsection
