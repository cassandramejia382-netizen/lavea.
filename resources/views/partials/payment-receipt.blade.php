<style>
    #payment-receipt { display:none; }
    @media print {
        @page { size:A4; margin:18mm; }
        .sidebar, .lavea-admin-topbar, .lavea-admin-topbar-spacer, .no-print, .flash, .error-list { display:none !important; }
        html, body { background:#fff !important; }
        .main { margin:0 !important;width:100% !important; }
        .content { padding:0 !important; }
        .lavea-admin-content > :not(#payment-receipt) { display:none !important; }
        #payment-receipt { display:block !important;max-width:720px;margin:0 auto;padding:0;background:#fff !important;color:#172554 !important;font:14px/1.5 Arial,Helvetica,sans-serif;print-color-adjust:exact;-webkit-print-color-adjust:exact; }
        #payment-receipt * { box-sizing:border-box;color:inherit; }
        #payment-receipt .receipt-header { display:flex;justify-content:space-between;gap:24px;padding-bottom:24px;border-bottom:3px solid #4169c8; }
        #payment-receipt .receipt-brand { font-size:32px;font-weight:800;letter-spacing:5px;color:#172554; }
        #payment-receipt .receipt-subtitle { color:#64748b;font-size:12px; }
        #payment-receipt h1 { margin:0 0 4px;font-size:22px;letter-spacing:0;color:#172554; }
        #payment-receipt .receipt-identity { text-align:right; }
        #payment-receipt .receipt-status { display:inline-block;margin-top:10px;padding:4px 12px;border:1px solid #cbd5e1;border-radius:20px;font-size:11px;font-weight:700; }
        #payment-receipt .receipt-info { display:grid;grid-template-columns:1fr 1fr;gap:20px;margin:24px 0; }
        #payment-receipt .receipt-label { display:block;margin-bottom:5px;color:#64748b;font-size:10px;font-weight:700;letter-spacing:1px;text-transform:uppercase; }
        #payment-receipt .receipt-value { font-size:14px;font-weight:600;overflow-wrap:anywhere; }
        #payment-receipt table { width:100%;min-width:0;border-collapse:collapse;margin:24px 0; }
        #payment-receipt th { padding:12px;background:#f1f5fb;color:#64748b;text-align:left;font-size:10px;letter-spacing:1px;text-transform:uppercase; }
        #payment-receipt td { padding:16px 12px;border-bottom:1px solid #e2e8f0;font-size:14px; }
        #payment-receipt .receipt-amount { display:flex;justify-content:space-between;align-items:center;gap:20px;padding:20px;border:1px solid #dce6fa;border-radius:12px;background:#f1f5ff; }
        #payment-receipt .receipt-amount strong { font-size:30px;color:#3159b2; }
        #payment-receipt .receipt-note { margin-top:20px;padding:12px 16px;border-left:3px solid #d6a342;background:#fffaf0;color:#715820;font-size:12px; }
        #payment-receipt .receipt-footer { margin-top:32px;padding-top:16px;border-top:1px solid #e2e8f0;text-align:center;color:#64748b;font-size:12px; }
        #payment-receipt .receipt-header, #payment-receipt .receipt-amount, #payment-receipt tr { break-inside:avoid; }
    }
</style>
<div id="payment-receipt" class="receipt">
    <div class="receipt-header">
        <div><div class="receipt-brand">LAVEA</div><div class="receipt-subtitle">Laundry Management System</div></div>
        <div class="receipt-identity"><h1>Payment Receipt</h1><div class="receipt-subtitle">#LVE-PAY-{{ str_pad($payment->id, 6, '0', STR_PAD_LEFT) }}</div><span class="receipt-status">{{ $payment->status === 'Completed' ? 'Verified payment' : 'Pending verification' }}</span></div>
    </div>
    <div class="receipt-info">
        <div><span class="receipt-label">Customer</span><div class="receipt-value">{{ $payment->order?->customer?->name ?? 'Not available' }}</div></div>
        <div><span class="receipt-label">Payment date</span><div class="receipt-value">{{ $payment->payment_date?->format('F d, Y') ?? 'Not available' }}</div></div>
        <div><span class="receipt-label">Payment method</span><div class="receipt-value">{{ $payment->payment_method }}</div></div>
        <div><span class="receipt-label">Reference number</span><div class="receipt-value">{{ $payment->reference_number ?: 'Not provided' }}</div></div>
    </div>
    <table><thead><tr><th>Order</th><th>Service</th><th>Weight</th><th>Order total</th></tr></thead><tbody><tr>
        <td>#LVE-{{ str_pad($payment->order_id, 4, '0', STR_PAD_LEFT) }}</td>
        <td>{{ $payment->order?->service?->service_name ?? 'Not available' }}</td>
        <td>{{ $payment->order?->quantity ?? 'Not available' }} kg</td>
        <td>&#8369;{{ number_format($payment->order?->total ?? 0, 2) }}</td>
    </tr></tbody></table>
    <div class="receipt-amount"><div><span class="receipt-label">{{ $payment->status === 'Completed' ? 'Amount paid' : 'Payment amount' }}</span><span>{{ $payment->payment_method }}</span></div><strong>&#8369;{{ number_format($payment->amount, 2) }}</strong></div>
    @if($payment->status !== 'Completed')
        <div class="receipt-note">Payment is pending staff verification. This receipt does not confirm that payment has been received.</div>
    @endif
    <div class="receipt-footer">Thank you for choosing LAVEA.<br>Keep this receipt for your records.</div>
</div>
