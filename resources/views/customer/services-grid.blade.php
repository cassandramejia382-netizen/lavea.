<style>
.customer-services {
    gap: 20px;
    align-items: stretch;
}
.customer-services .service-card {
    display: flex;
    flex-direction: column;
    padding: 10px;
    border: 1px solid #e4e9f2;
    border-radius: 16px;
    background: #fff;
    color: inherit;
    text-decoration: none;
    box-shadow: 0 3px 12px rgb(23 37 84 / 3%);
}
.customer-services .service-card:focus-visible {
    outline: 2px solid #4169c8;
    outline-offset: 4px;
}
.customer-services .service-media {
    position: relative;
    aspect-ratio: 16 / 10;
    overflow: hidden;
    border-radius: 10px;
    background: #f5f7fb;
}
.customer-services .service-media img {
    display: block;
    width: 100%;
    height: 100%;
    object-fit: contain;
    transition: transform 350ms cubic-bezier(.2,.7,.3,1);
}
@media(hover:hover) and (pointer:fine) {
    .customer-services .service-media:hover img {
        transform: scale(1.06);
    }
}
.customer-services .service-placeholder {
    display: grid;
    place-items: center;
    height: 100%;
    color: #4169c8;
}
.customer-services .service-placeholder svg {
    width: 36px;
    height: 36px;
}
.customer-services .service-body {
    display: flex;
    flex: 1;
    flex-direction: column;
    padding: 18px 10px 10px;
}
.customer-services .service-title-row {
    display: flex;
    align-items: flex-start;
    justify-content: space-between;
    gap: 12px;
}
.customer-services .service-title-row h2 {
    margin: 0;
    color: #172554;
    font-size: 17px;
    line-height: 1.4;
    overflow-wrap: anywhere;
}
.customer-services .service-status {
    display: inline-flex;
    align-items: center;
    gap: 5px;
    flex: none;
    padding: 4px 8px;
    border-radius: 6px;
    background: #edf8f2;
    color: #29815b;
    font-size: 10px;
    font-weight: 600;
}
.customer-services .service-status::before {
    content: '';
    width: 5px;
    height: 5px;
    border-radius: 50%;
    background: currentColor;
}
.customer-services .service-status[data-active="false"] {
    background: #fff1f2;
    color: #be123c;
}
.customer-services .service-description {
    margin: 10px 0 20px;
    color: #78869d;
    font-size: 13px;
    line-height: 1.65;
}
.customer-services .service-footer {
    display: flex;
    align-items: center;
    justify-content: space-between;
    gap: 12px;
    margin-top: auto;
    padding-top: 14px;
    border-top: 1px solid #edf0f5;
}
.customer-services .service-price {
    color: #172554;
    font-size: 19px;
    font-weight: 700;
    letter-spacing: -.3px;
}
.customer-services .service-price small {
    color: #78869d;
    font-size: 11px;
    font-weight: 400;
    letter-spacing: 0;
}
.customer-services .service-details {
    display: inline-flex;
    align-items: center;
    gap: 5px;
    color: #4169c8;
    font-size: 11px;
    font-weight: 600;
    white-space: nowrap;
}
.customer-services .service-details svg {
    width: 14px;
    height: 14px;
}
html[data-lavea-theme="dark"] .customer-services .service-card {
    background: #172554;
    border-color: #34415a;
}
html[data-lavea-theme="dark"] .customer-services .service-title-row h2,
    html[data-lavea-theme="dark"] .customer-services .service-price {
    color: #e7edf9;
}
html[data-lavea-theme="dark"] .customer-services .service-footer {
    border-color: #34415a;
}
@media(prefers-reduced-motion:reduce) {
    .customer-services .service-media img {
        transition: none;
    }
    .customer-services .service-media:hover img {
        transform: none;
    }
}
</style>
<div class="service-grid customer-services">
    @forelse ($services as $service)
        <a href="{{ route('customer.services.show', $service) }}" class="card service-card">
            <div class="service-media">
                @if ($imageUrl = $service->imageUrl())
                    <img src="{{ $imageUrl }}" alt="{{ $service->service_name }}" loading="lazy">
                @else
                    <div class="service-placeholder"><i data-lucide="washing-machine" aria-label="No service image"></i></div>
                @endif
            </div>
            <div class="service-body">
                <div class="service-title-row">
                    <h2>{{ $service->service_name }}</h2>
                    <span class="service-status" data-active="{{ $service->is_active ? 'true' : 'false' }}">{{ $service->is_active ? 'Active' : 'Inactive' }}</span>
                </div>
                <p class="service-description">{{ $service->description ?: 'Contact the shop for more details about this service.' }}</p>
                <div class="service-footer">
                    <span class="service-price">&#8369;{{ number_format($service->price, 2) }} <small>/ {{ $service->billingUnit() }}</small></span>
                    <span class="service-details">View details <i data-lucide="arrow-up-right" aria-hidden="true"></i></span>
                </div>
            </div>
        </a>
    @empty
        <div class="card empty">No services are currently available.</div>
    @endforelse
</div>
