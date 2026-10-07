@extends('customer.layout', ['title' => 'Pay for Order'])
@push('styles')
<style>
    .customer-checkout { width:100%;max-width:1040px;margin:0 auto; }
    .customer-checkout-heading { display:flex;align-items:center;gap:13px;margin:0 0 18px; }
    .customer-checkout-heading-icon { display:grid;place-items:center;flex:none;width:44px;height:44px;border-radius:12px;background:#eaf0ff;color:#4169c8; }
    .customer-checkout-heading-icon svg { width:22px;height:22px; }
    .customer-checkout-heading h1 { margin:0 0 4px;color:#10204a;font-size:23px; }
    .customer-checkout-heading p { margin:0;color:#78869d;font-size:14px; }
    .customer-checkout-grid { display:grid;grid-template-columns:minmax(250px,.78fr) minmax(0,1.22fr);gap:16px;align-items:start; }
    .customer-checkout-panel { padding:21px;border:1px solid #e6eaf2;border-radius:13px;background:#fff;box-shadow:0 5px 18px rgb(23 37 84 / 4%); }
    .customer-checkout-panel h2 { margin:0 0 15px;color:#172554;font-size:17px; }
    .checkout-service { display:flex;align-items:center;gap:12px;padding:13px;border:1px solid #e8edf6;border-radius:10px;background:#f8faff; }
    .checkout-service-icon { display:grid;place-items:center;flex:none;width:39px;height:39px;border-radius:10px;background:#e8efff;color:#4169c8; }
    .checkout-service-icon svg { width:19px;height:19px; }
    .checkout-service-label { color:#78869d;font-size:12px; }
    .checkout-service strong { display:block;margin-top:3px;color:#172554;font-size:15px; }
    .checkout-facts { display:grid;gap:12px;margin-top:17px; }
    .checkout-fact { display:flex;justify-content:space-between;gap:10px;color:#78869d;font-size:14px; }
    .checkout-fact strong { color:#263550;text-align:right;font-size:14px; }
    .checkout-balance { margin-top:16px;padding:15px;border:1px solid #dce6fb;border-radius:10px;background:linear-gradient(135deg,#f1f5ff,#f8faff); }
    .checkout-balance span { display:block;color:#697997;font-size:13px; }
    .checkout-balance strong { display:block;margin-top:4px;color:#3159b2;font-size:26px; }
    .checkout-form { display:grid;grid-template-columns:1fr 1fr;gap:14px; }
    .checkout-form .field.full { grid-column:1/-1; }
    .checkout-form .field { gap:7px; }
    .checkout-form .field label { font-size:14px; }
    .checkout-form .field input,.checkout-form .field select,.checkout-form .field textarea { padding:11px 12px;border-radius:8px;font-size:14px; }
    .checkout-form .field textarea { min-height:82px; }
    .checkout-form .field small { font-size:12px;line-height:1.45; }
    .checkout-methods { display:grid;grid-template-columns:1fr 1fr;gap:9px; }
    .checkout-method-option { position:relative;display:flex;align-items:center;gap:10px;min-height:62px;padding:11px 12px;border:1px solid #e1e7f0;border-radius:9px;background:#fff;cursor:pointer;transition:border-color 150ms ease,background 150ms ease,box-shadow 150ms ease; }
    .checkout-method-option:hover { border-color:#b8c9ee;background:#f9fbff; }
    .checkout-method-option.is-selected { border-color:#5277cf;background:#f2f6ff;box-shadow:0 0 0 2px rgb(65 105 200 / 9%); }
    .checkout-method-option input { position:absolute;width:1px;height:1px;opacity:0; }
    .checkout-method-option:focus-within { outline:2px solid #4169c8;outline-offset:2px; }
    .checkout-method-icon { display:grid;place-items:center;flex:none;width:34px;height:34px;border-radius:9px;background:#edf2ff;color:#4169c8; }
    .checkout-method-icon svg { width:17px;height:17px; }
    .checkout-method-option strong { color:#263550;font-size:13px; }
    .checkout-notice { display:flex;align-items:flex-start;gap:9px;padding:11px 12px;border:1px solid #e4eaf5;border-radius:9px;background:#f7f9fd;color:#64728b;font-size:12px;line-height:1.5; }
    .checkout-notice svg { flex:none;width:17px;height:17px;color:#4169c8; }
    .checkout-actions { display:flex;align-items:center;gap:9px; }
    .checkout-actions .btn { min-height:40px;padding:10px 15px;font-size:13px; }
    @media(max-width:680px) { .customer-checkout-grid { grid-template-columns:1fr; } }
    @media(max-width:460px) { .checkout-form { grid-template-columns:1fr; } .checkout-form .field.full { grid-column:auto; } }
    @media(prefers-reduced-motion:reduce) { .checkout-method-option { transition:none; } }
</style>
@endpush
@section('content')
<div class="customer-checkout">
    <header class="customer-checkout-heading">
        <span class="customer-checkout-heading-icon"><i data-lucide="credit-card"></i></span>
        <div><h1>Pay for Order #LVE-{{ str_pad($order->id, 4, '0', STR_PAD_LEFT) }}</h1><p>Review the balance and choose how you want to pay.</p></div>
    </header>

    <div class="customer-checkout-grid">
        <section class="customer-checkout-panel">
            <h2>Order summary</h2>
            <div class="checkout-service">
                <span class="checkout-service-icon"><i data-lucide="washing-machine"></i></span>
                <div><span class="checkout-service-label">Laundry service</span><strong>{{ $order->service?->service_name ?? 'Laundry service' }}</strong></div>
            </div>
            <div class="checkout-facts">
                <div class="checkout-fact"><span>Weight</span><strong>{{ $order->quantity }} kg</strong></div>
                <div class="checkout-fact"><span>Order total</span><strong>&#8369;{{ number_format($order->total, 2) }}</strong></div>
                <div class="checkout-fact"><span>Order status</span><strong>{{ $order->status }}</strong></div>
            </div>
            <div class="checkout-balance"><span>Remaining balance</span><strong>&#8369;{{ number_format($balance, 2) }}</strong></div>
        </section>

        <section class="customer-checkout-panel">
            <h2>Payment details</h2>
            <form class="checkout-form" method="POST" action="{{ route('customer.payments.checkout.store', $order) }}">
                @csrf
                <div class="field">
                    <label for="amount">Amount to pay</label>
                    <input id="amount" type="number" name="amount" min="0.01" max="{{ number_format($balance, 2, '.', '') }}" step="0.01" value="{{ old('amount', number_format($balance, 2, '.', '')) }}" required>
                    <small class="muted">Pay some or all of the balance.</small>
                </div>
                <fieldset class="field full" style="border:0;padding:0;margin:0">
                    <legend style="margin-bottom:6px;font-size:12px;font-weight:600;color:#34415a">Payment method</legend>
                    <div class="checkout-methods">
                        @foreach([['GCash','smartphone'],['Bank Transfer','building-2'],['Cash','banknote'],['Card','credit-card']] as [$method,$icon])
                            <label class="checkout-method-option {{ old('payment_method', 'GCash') === $method ? 'is-selected' : '' }}">
                                <input type="radio" name="payment_method" value="{{ $method }}" @checked(old('payment_method', 'GCash') === $method) required>
                                <span class="checkout-method-icon"><i data-lucide="{{ $icon }}"></i></span>
                                <strong>{{ $method }}</strong>
                            </label>
                        @endforeach
                    </div>
                </fieldset>
                <div class="field full" id="reference-field">
                    <label for="reference_number">Transfer reference number <span id="reference-required">*</span></label>
                    <input id="reference_number" name="reference_number" maxlength="255" value="{{ old('reference_number') }}" placeholder="Enter the reference from your transfer">
                    <small class="muted">Required for GCash and bank transfers. Cash and card payments are confirmed at the shop.</small>
                </div>
                <div class="field full">
                    <label for="notes">Note <span class="muted" style="font-weight:400">(optional)</span></label>
                    <textarea id="notes" name="notes" maxlength="2000" placeholder="Add a note for staff">{{ old('notes') }}</textarea>
                </div>
                <div class="checkout-notice field full"><i data-lucide="info"></i><span>Your payment will remain <strong>Pending</strong> until staff verifies it. The order balance updates after confirmation.</span></div>
                <div class="checkout-actions field full">
                    <button class="btn" type="submit">Submit Payment</button>
                    <a class="btn secondary" href="{{ route('customer.payments.index') }}">Cancel</a>
                </div>
            </form>
        </section>
    </div>
</div>

<script>
    const paymentMethodOptions = document.querySelectorAll('.checkout-method-option');
    const referenceInput = document.getElementById('reference_number');
    const referenceRequired = document.getElementById('reference-required');
    const updatePaymentMethod = () => {
        const selectedMethod = document.querySelector('input[name="payment_method"]:checked')?.value;
        const requiresReference = ['GCash', 'Bank Transfer'].includes(selectedMethod);
        referenceInput.required = requiresReference;
        referenceRequired.hidden = !requiresReference;
        referenceInput.placeholder = requiresReference ? 'Enter the reference from your transfer' : 'Optional';
        paymentMethodOptions.forEach((option) => {
            option.classList.toggle('is-selected', option.querySelector('input').checked);
        });
    };
    paymentMethodOptions.forEach((option) => option.addEventListener('change', updatePaymentMethod));
    updatePaymentMethod();
</script>
@endsection
