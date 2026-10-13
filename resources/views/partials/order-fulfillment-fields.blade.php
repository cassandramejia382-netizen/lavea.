@php
    $fulfillmentOrder = $order ?? null;
    $fulfillmentMethod = old('fulfillment_method', $fulfillmentOrder?->fulfillmentMethod() ?? 'Pickup');
@endphp
<div class="field full">
    <label for="fulfillment_method">Pickup or Delivery</label>
    <select id="fulfillment_method" name="fulfillment_method" required>
        <option value="Pickup" @selected($fulfillmentMethod === 'Pickup')>Pickup</option>
        <option value="Delivery" @selected($fulfillmentMethod === 'Delivery')>Delivery</option>
    </select>
    @error('fulfillment_method')<span class="error-list">{{ $message }}</span>@enderror
</div>
<div class="field full" data-fulfillment="Pickup" @if($fulfillmentMethod !== 'Pickup') hidden @endif>
    <label for="pickup_date">Pickup date</label>
    <input id="pickup_date" name="pickup_date" type="date" min="{{ today()->toDateString() }}" value="{{ old('pickup_date', $fulfillmentOrder?->pickup_date?->toDateString() ?? '') }}" @required($fulfillmentMethod === 'Pickup') @disabled($fulfillmentMethod !== 'Pickup')>
    @error('pickup_date')<span class="error-list">{{ $message }}</span>@enderror
</div>
<div class="field full" data-fulfillment="Delivery" @if($fulfillmentMethod !== 'Delivery') hidden @endif>
    <label for="delivery_date">Delivery date</label>
    <input id="delivery_date" name="delivery_date" type="date" min="{{ today()->toDateString() }}" value="{{ old('delivery_date', $fulfillmentOrder?->delivery_date?->toDateString() ?? '') }}" @required($fulfillmentMethod === 'Delivery') @disabled($fulfillmentMethod !== 'Delivery')>
    @error('delivery_date')<span class="error-list">{{ $message }}</span>@enderror
</div>
<div class="field full" data-fulfillment="Delivery" @if($fulfillmentMethod !== 'Delivery') hidden @endif>
    <label for="delivery_address">Delivery address</label>
    <textarea id="delivery_address" name="delivery_address" maxlength="1000" placeholder="Enter your complete delivery address" @required($fulfillmentMethod === 'Delivery') @disabled($fulfillmentMethod !== 'Delivery')>{{ old('delivery_address', $fulfillmentOrder?->delivery_address ?? auth()->user()->customerProfile?->address ?? '') }}</textarea>
    @error('delivery_address')<span class="error-list">{{ $message }}</span>@enderror
    <small class="muted">An active Delivery Rider will be assigned automatically when you save.</small>
</div>
<style>[data-fulfillment][hidden] { display: none !important; }</style>
<script>
    (() => {
        const method = document.getElementById('fulfillment_method');
        const updateFields = () => {
            document.querySelectorAll('[data-fulfillment]').forEach(field => {
                const active = field.dataset.fulfillment === method.value;
                field.hidden = !active;
                field.querySelectorAll('input, textarea').forEach(input => {
                    input.disabled = !active;
                    input.required = active;
                });
            });
        };
        method.addEventListener('change', updateFields);
        updateFields();
    })();
</script>
