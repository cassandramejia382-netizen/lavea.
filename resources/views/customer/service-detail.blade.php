@extends('customer.layout', ['title' => $service->service_name])

@push('styles')
<style>
.customer-service-page {
    width: 100%;
    max-width: 900px;
    margin: 0 auto;
}
.customer-service-heading {
    display: flex;
    align-items: center;
    justify-content: space-between;
    gap: 12px;
    margin-bottom: 14px;
}
.customer-service-heading h1 {
    margin: 0;
    color: #10204a;
    font-size: 21px;
}
.customer-service-heading p {
    margin: 4px 0 0;
    color: #7a879d;
}
.customer-service-back {
    color: #4169c8;
    text-decoration: none;
    font-weight: 600;
}
.customer-service-summary {
    display: grid;
    grid-template-columns: minmax(200px, 280px) minmax(0, 1fr);
    gap: 18px;
    align-items: center;
    padding: 17px;
    margin-bottom: 12px;
}
.customer-service-image {
    display: flex;
    width: 100%;
    min-height: 180px;
    max-height: 300px;
    align-items: center;
    justify-content: center;
    overflow: hidden;
    border-radius: 9px;
    background: #f4f6fa;
    color: #4169c8;
}
.customer-service-image img {
    display: block;
    width: auto;
    height: auto;
    max-width: 100%;
    max-height: 300px;
    object-fit: contain;
}
.customer-service-image svg {
    width: 42px;
    height: 42px;
}
.customer-service-label {
    margin: 0 0 6px;
    color: #4169c8;
    font-size: 10px;
    font-weight: 700;
    letter-spacing: .06em;
    text-transform: uppercase;
}
.customer-service-summary h2 {
    margin: 0 0 7px;
    color: #12214a;
    font-size: 18px;
}
.customer-service-description {
    margin: 0 0 12px;
    color: #718099;
    line-height: 1.5;
}
.customer-service-price {
    color: #12214a;
    font-size: 18px;
    font-weight: 700;
}
.customer-service-order {
    padding: 17px;
}
.customer-service-order h2 {
    margin-bottom: 4px;
}
.customer-service-order > p {
    margin: 0 0 13px;
}
.customer-service-order .form-grid {
    gap: 12px;
}
@media (max-width: 680px) {
    .customer-service-heading {
        align-items: flex-start;
        flex-direction: column;
    }
    .customer-service-summary {
        grid-template-columns: minmax(0, 1fr);
        gap: 12px;
        padding: 14px;
    }
    .customer-service-image {
        min-height: 170px;
        max-height: 230px;
    }
    .customer-service-image img {
        max-height: 230px;
    }
    .customer-service-order {
        padding: 14px;
    }
}
</style>
@endpush

@section('content')
<div class="customer-service-page">
    <header class="customer-service-heading">
        <div><h1>{{ $service->service_name }}</h1><p>Service details and order form</p></div>
        <a href="{{ route('customer.services.index') }}" class="lavea-back-link"><svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.8" stroke-linecap="round" stroke-linejoin="round" aria-hidden="true"><path d="m12 19-7-7 7-7M5 12h14" /></svg>All services</a>
    </header>

    <article class="card customer-service-summary">
        <div class="customer-service-image">
            @if ($imageUrl = $service->imageUrl())
                <img src="{{ $imageUrl }}" alt="{{ $service->service_name }}">
            @else
                <i data-lucide="washing-machine" aria-label="No service image available"></i>
            @endif
        </div>
        <div>
            <p class="customer-service-label">Laundry service</p>
            <span class="service-status" data-active="{{ $service->is_active ? 'true' : 'false' }}" style="display:inline-block;padding:4px 8px;border-radius:6px;font-size:12px;font-weight:600;background:{{ $service->is_active ? '#edf8f2' : '#fff1f2' }};color:{{ $service->is_active ? '#29815b' : '#be123c' }}">{{ $service->is_active ? 'Active' : 'Inactive' }}</span>
            <h2>{{ $service->service_name }}</h2>
            <p class="customer-service-description">{{ $service->description ?: 'Contact the shop for more details about this service.' }}</p>
            <div class="customer-service-price">&#8369;{{ number_format($service->price, 2) }} <span class="muted" style="font-size:13px;font-weight:400">per {{ $service->billingUnit() }}</span></div>
        </div>
    </article>

    @if ($service->is_active)
    <section class="card customer-service-order">
        <h2>Place an order</h2>
        <p class="muted">Choose your quantity and pickup or delivery details.</p>
        <form class="form-grid" method="POST" action="{{ route('customer.services.order', $service) }}">
            @csrf
            <div class="field">
                <label for="quantity">{{ $service->quantityLabel() }}</label>
                <div style="display:flex;align-items:center;gap:9px"><input id="quantity" type="number" name="quantity" min="1" @if($service->billingUnit() === 'kg') max="9" @endif step="{{ $service->billingUnit() === 'item' ? '1' : '0.01' }}" value="{{ old('quantity', 1) }}" required><span>{{ $service->billingUnit() }}</span></div>
                <small class="muted">{{ $service->billingUnit() === 'kg' ? 'Maximum: 9 kg' : ($service->billingUnit() === 'set' ? 'Enter a quantity with up to two decimal places.' : 'Enter a whole number.') }}</small>
            </div>
            <div class="field">
                <label>Estimated price</label>
                <strong id="price-preview" style="font-size:18px">&#8369;0.00</strong>
                <small class="muted">Calculated from your selected quantity</small>
            </div>
            @include('partials.order-fulfillment-fields')
            <div class="field full">
                <label for="notes">Special instructions</label>
                <textarea id="notes" name="notes" maxlength="2000" placeholder="Add any special instructions for your order">{{ old('notes') }}</textarea>
            </div>
            <div class="actions field full"><button class="btn" type="submit">Order this service</button></div>
        </form>
    </section>
    @else
    <section class="card customer-service-order">
        <h2>Currently unavailable</h2>
        <p class="muted">This service is inactive. Please choose another available service.</p>
        <a class="btn" href="{{ route('customer.services.index') }}">Browse services</a>
    </section>
    @endif
</div>
@if ($service->is_active)
<script>
    const serviceWeight = document.getElementById('quantity');
    const pricePreview = document.getElementById('price-preview');
    const pricePerKg = {{ (float) $service->price }};
    const updatePrice = () => {
        pricePreview.textContent = '\u20B1' + (pricePerKg * Number(serviceWeight.value || 0)).toLocaleString('en-PH', { minimumFractionDigits: 2, maximumFractionDigits: 2 });
    };
    serviceWeight.addEventListener('input', updatePrice);
    updatePrice();
</script>
@endif
@endsection
