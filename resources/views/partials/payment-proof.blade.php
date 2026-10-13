@if($payment->rejection_reason)
<section class="card no-print" style="margin-top:16px;margin-bottom:16px">
    <h2>Rejection reason</h2>
    <p style="margin-top:8px;overflow-wrap:anywhere">{{ $payment->rejection_reason }}</p>
</section>
@endif
@if($payment->proof_image_path)
<section class="card no-print" style="margin-top:16px;margin-bottom:16px">
    <h2 style="margin-bottom:12px">Proof of payment</h2>
    <a href="{{ route('payments.proof', $payment) }}" target="_blank" rel="noopener">
        <img src="{{ route('payments.proof', $payment) }}" alt="Proof of payment for payment #{{ $payment->id }}" style="display:block;max-width:100%;width:320px;max-height:360px;object-fit:contain;border-radius:9px">
    </a>
    <a class="btn lavea-proof-button" href="{{ route('payments.proof', $payment) }}" target="_blank" rel="noopener">
        <span>View</span>
    </a>
</section>
@endif
