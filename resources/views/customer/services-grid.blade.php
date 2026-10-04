<div class="service-grid">
    @forelse($services as $service)
        <article class="card service-card">
            @if($service->image)
                <img src="{{ asset('uploads/services/'.$service->image) }}" alt="{{ $service->service_name }}" loading="lazy">
            @else
                <div style="height:140px;background:#e7efff;display:grid;place-items:center;color:#4169c8"><i data-lucide="washing-machine" style="width:40px;height:40px"></i></div>
            @endif
            <div class="service-body"><h2>{{ $service->service_name }}</h2><p class="muted" style="line-height:1.5">{{ $service->description ?: 'Contact the shop for more details about this service.' }}</p><strong>₱{{ number_format($service->price, 2) }}</strong></div>
        </article>
    @empty
        <div class="card empty">No services are currently available.</div>
    @endforelse
</div>
