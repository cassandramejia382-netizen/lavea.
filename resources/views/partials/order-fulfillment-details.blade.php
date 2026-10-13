@if($order->fulfillment_method || $order->delivery_address)
<section class="card" style="margin-top:16px">
    <div class="field">
        <span class="muted">Pickup or Delivery</span>
        <strong>{{ $order->fulfillmentMethod() }}</strong>
    </div>
    @if($order->fulfillmentMethod() === 'Delivery')
        <div class="field" style="margin-top:12px">
            <span class="muted">Delivery Rider</span>
            <strong>{{ $order->deliveryRider?->name ?? 'Not assigned' }}</strong>
        </div>
        <div class="field" style="margin-top:12px">
            <span class="muted">Delivery address</span>
            <strong style="overflow-wrap:anywhere;white-space:pre-line">{{ $order->delivery_address }}</strong>
        </div>
    @endif
</section>
@endif
