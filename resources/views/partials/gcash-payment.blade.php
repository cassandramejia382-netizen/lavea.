@php($gcashAccount = \App\GCashAccount::details())
<style>.gcash-payment[hidden] { display:none !important; }.gcash-payment > .notes-content { width:100%; }</style>
<div class="field full gcash-payment" data-gcash-payment data-available="{{ $gcashAccount['available'] ? 'true' : 'false' }}" hidden>
    <div class="notes-content" style="padding:16px;border:1px solid #dfe5ef;border-radius:10px;min-width:0">
        @if ($gcashAccount['available'])
            <strong>Shop GCash Account</strong>
            <p style="margin-top:10px">GCash number: <strong data-gcash-number>{{ $gcashAccount['number'] }}</strong></p>
            <p style="margin-top:6px;overflow-wrap:anywhere">Account name: <strong>{{ $gcashAccount['name'] }}</strong></p>
            <p style="margin-top:6px">Amount to pay: <strong data-gcash-amount></strong></p>
            <button class="btn secondary" type="button" data-copy-gcash style="margin-top:12px">Copy Number</button>
            <span data-copy-status role="status" aria-live="polite" style="margin-left:8px"></span>
            @if ($gcashAccount['qr_url'])
                <img src="{{ $gcashAccount['qr_url'] }}" alt="Shop GCash QR code" style="display:block;width:220px;max-width:100%;margin-top:16px;padding:8px;background:#fff;border-radius:8px">
            @endif
            <p style="margin-top:14px;line-height:1.5">Send your payment to this GCash account. After paying, enter the reference number and upload your payment screenshot.</p>
        @else
            <p role="status">GCash payment is currently unavailable</p>
        @endif
    </div>
</div>
<script>
(() => {
    const panel = document.querySelector('[data-gcash-payment]');
    const form = panel.closest('form');
    const amount = form.querySelector('[name="amount"]');
    const paymentMethod = () => form.querySelector('[name="payment_method"]:checked')?.value ?? form.querySelector('select[name="payment_method"]')?.value;
    const available = panel.dataset.available === 'true';
    const render = () => {
        const selected = paymentMethod() === 'GCash';
        panel.hidden = !selected;
        const amountText = panel.querySelector('[data-gcash-amount]');
        if (amountText) amountText.textContent = amount.value && Number.isFinite(Number(amount.value))
            ? '\u20B1' + Number(amount.value).toLocaleString('en-PH', {minimumFractionDigits:2, maximumFractionDigits:2}) : 'Enter an amount in pesos';
        form.querySelectorAll('button[type="submit"], button:not([type])').forEach(button => button.disabled = selected && !available);
    };
    form.addEventListener('change', render);
    amount.addEventListener('input', render);
    form.addEventListener('submit', event => {
        if (paymentMethod() === 'GCash' && !available) { event.preventDefault(); render(); }
    });
    panel.querySelector('[data-copy-gcash]')?.addEventListener('click', async () => {
        const number = panel.querySelector('[data-gcash-number]').textContent.trim();
        const status = panel.querySelector('[data-copy-status]');
        try {
            if (!navigator.clipboard?.writeText) throw new Error('Clipboard unavailable');
            await navigator.clipboard.writeText(number);
            status.textContent = 'Number copied';
        } catch {
            const focused = document.activeElement;
            const textarea = document.createElement('textarea');
            textarea.value = number;
            textarea.style.position = 'fixed'; textarea.style.left = '-9999px';
            document.body.append(textarea); textarea.select();
            let copied = false;
            try { copied = document.execCommand('copy'); } catch {}
            textarea.remove(); focused?.focus();
            status.textContent = copied ? 'Number copied' : 'Select the number above and copy it manually.';
        }
    });
    render();
    document.addEventListener('DOMContentLoaded', render, {once:true});
})();
</script>
