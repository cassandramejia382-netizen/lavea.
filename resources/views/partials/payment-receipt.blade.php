<style>
    #payment-receipt { display: none; }
    @media print {
        .sidebar, .lavea-admin-topbar, .lavea-admin-topbar-spacer, .no-print { display: none !important; }
        .main { margin: 0 !important; width: 100% !important; }
        .content { padding: 0 !important; }
        #payment-receipt {
            display: block !important;
            width: 100%;
            padding: 24px;
            background: white !important;
            color: #111 !important;
            font-family: Arial, Helvetica, sans-serif;
            font-size: 14px;
            line-height: 1.5;
        }
        #payment-receipt h1 { margin: 0 0 20px; font-size: 26px; color: #111 !important; }
        #payment-receipt p { margin: 0; padding: 10px 0; border-bottom: 1px solid #ddd; color: #111 !important; }
    }
</style>
<div id="payment-receipt" class="receipt">
    <h1>LAVEA Payment Receipt</h1>
    <p><strong>Order ID:</strong> #{{ $payment->order?->id ?? 'N/A' }}</p>
    <p><strong>Customer:</strong> {{ $payment->order?->customer?->name ?? 'N/A' }}</p>
    <p><strong>Service:</strong> {{ $payment->order?->service?->service_name ?? 'N/A' }}</p>
    <p><strong>Amount:</strong> &#8369;{{ number_format($payment->amount, 2) }}</p>
    <p><strong>Payment Method:</strong> {{ $payment->payment_method }}</p>
    <p><strong>Payment Date:</strong> {{ $payment->payment_date?->format('F d, Y') ?? '—' }}</p>
    <p><strong>Payment Status:</strong> {{ $payment->status }}</p>
</div>
