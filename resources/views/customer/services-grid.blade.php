<style>
    .service-card {
        transition: transform 180ms ease, box-shadow 180ms ease, border-color 180ms ease;
    }

    .service-card:hover {
        transform: translateY(-4px);
        border-color: #cbd7f2;
        box-shadow: 0 10px 24px rgb(16 32 74 / 10%);
    }

    .service-card img {
        transition: transform 220ms ease;
    }

    .service-card:hover img {
        transform: scale(1.025);
    }

    @media (prefers-reduced-motion: reduce) {
        .service-card,
        .service-card img {
            transition: none;
        }
    }
</style>
<div class="service-grid">
    @forelse($services as $service)
        <article class="card service-card">
            @if($imageUrl = $service->imageUrl())
                <img src="{{ $imageUrl }}" alt="{{ $service->service_name }}" loading="lazy" style="display:block;width:100%;height:140px;object-fit:contain;background:#f4f6fa">
            @else
                <div class="service-placeholder" style="height:140px;background:#e7efff;display:grid;place-items:center;color:#4169c8"><i data-lucide="washing-machine" style="width:40px;height:40px"></i></div>
            @endif
            <div class="service-body">
                <h2><a href="{{ route('customer.services.show', $service) }}" style="color:inherit;text-decoration:none">{{ $service->service_name }}</a></h2>
                <p class="muted" style="line-height:1.5">{{ $service->description ?: 'Contact the shop for more details about this service.' }}</p>
                <strong>₱{{ number_format($service->price, 2) }} / kg</strong>
                <div style="margin-top:14px"><a class="btn" href="{{ route('customer.services.show', $service) }}">View service</a></div>
            </div>
        </article>
    @empty
        <div class="card empty">No services are currently available.</div>
    @endforelse
</div>
