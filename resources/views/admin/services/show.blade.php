@extends('admin.layout', ['title' => $service->service_name])

@push('styles')
<style>
.lavea-admin-content .service-detail-page { width: 100%; max-width: 1120px; margin: 10px auto; }
.lavea-admin-content .service-detail-heading { display: flex; align-items: center; justify-content: space-between; gap: 16px; margin-bottom: 20px; }
.lavea-admin-content .service-detail-heading h1 { margin: 0; color: #10204a; font-size: 26px; }
.lavea-admin-content .service-detail-heading p { margin: 6px 0 0; color: #7a879d; }
.lavea-admin-content .service-detail-actions { display: flex; gap: 10px; }
.lavea-admin-content .service-detail-link { display: inline-flex; align-items: center; gap: 7px; padding: 10px 14px; border: 1px solid #dfe5ef; border-radius: 8px; background: white; color: #34415a; text-decoration: none; font-size: 13px; }
.lavea-admin-content .service-detail-link.primary { border-color: #4169c8; background: #4169c8; color: white; }
.lavea-admin-content .service-detail-card { display: grid; grid-template-columns: minmax(0, 360px) minmax(0, 1fr); align-items: center; gap: 30px; padding: 26px; border: 1px solid #e5e9f1; border-radius: 14px; background: white; box-shadow: 0 8px 24px rgb(16 32 74 / 5%); }
.lavea-admin-content .service-detail-image { display: grid; width: 100%; height: 280px; place-items: center; overflow: hidden; border-radius: 12px; background: #f5f7fb; color: #4169c8; }
.lavea-admin-content .service-detail-image img { display: block; width: 100%; height: 100%; object-fit: contain; }
.lavea-admin-content .service-detail-image svg { width: 48px; height: 48px; }
.lavea-admin-content .service-detail-label { margin: 0 0 8px; color: #4169c8; font-size: 11px; font-weight: 700; letter-spacing: .08em; text-transform: uppercase; }
.lavea-admin-content .service-detail-card h2 { margin: 0 0 12px; color: #12214a; font-size: 24px; }
.lavea-admin-content .service-detail-description { margin: 0 0 24px; color: #718099; font-size: 14px; line-height: 1.7; }
.lavea-admin-content .service-detail-price-label { margin: 0 0 4px; color: #718099; font-size: 11px; font-weight: 700; letter-spacing: .08em; }
.lavea-admin-content .service-detail-price { color: #12214a; font-size: 24px; font-weight: 700; }
@media (max-width: 700px) {
    .lavea-admin-content .service-detail-heading { align-items: flex-start; flex-direction: column; }
    .lavea-admin-content .service-detail-card { grid-template-columns: minmax(0, 1fr); gap: 20px; padding: 18px; }
    .lavea-admin-content .service-detail-image { height: 240px; }
}
</style>
@endpush

@section('content')
<div class="service-detail-page">
    <header class="service-detail-heading">
        <div>
            <h1>{{ $service->service_name }}</h1>
            <p>Service information and pricing</p>
        </div>
        <div class="service-detail-actions">
            <a class="service-detail-link" href="{{ route('admin.services.index') }}">&larr; Back to Services</a>
            <a class="service-detail-link primary" href="{{ route('admin.services.edit', $service) }}">Edit Service</a>
        </div>
    </header>

    <article class="service-detail-card">
        <div class="service-detail-image">
            @if ($imageUrl = $service->imageUrl())
                <img src="{{ $imageUrl }}" alt="{{ $service->service_name }}">
            @else
                <i data-lucide="washing-machine" aria-label="No service image available"></i>
            @endif
        </div>
        <div>
            <p class="service-detail-label">Laundry service</p>
            <h2>{{ $service->service_name }}</h2>
            <p class="service-detail-description">{{ $service->description ?: 'No description available.' }}</p>
            <p class="service-detail-price-label">PRICE</p>
            <div class="service-detail-price">&#8369;{{ number_format($service->price, 2) }}</div>
        </div>
    </article>
</div>
@endsection
