@if($payment->proof_image_path)
<section class="card no-print" style="margin-top:16px;margin-bottom:16px">
    <h2 style="margin-bottom:12px">Proof of payment</h2>
    <a href="{{ route('payments.proof', $payment) }}" target="_blank" rel="noopener">
        <img src="{{ route('payments.proof', $payment) }}" alt="Proof of payment for payment #{{ $payment->id }}" style="display:block;max-width:100%;width:320px;max-height:360px;object-fit:contain;border-radius:9px">
        <span>View full image</span>
    </a>
</section>
@endif
