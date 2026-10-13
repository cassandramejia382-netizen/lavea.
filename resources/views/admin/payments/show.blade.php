@extends('admin.layout', ['title' => 'Payment Details'])

@push('styles')

<style>
.lavea-admin-content * {
    margin: 0;
    padding: 0;
}
.lavea-admin-content .avatar {
    width: 38px;
    height: 38px;
    background: #e8eefb;
    border-radius: 50%;
    display: flex;
    align-items: center;
    justify-content: center;
}
.lavea-admin-content .avatar svg {
    width: 18px;
}
.lavea-admin-content .page-header {
    display: flex;
    justify-content: space-between;
    align-items: center;
    margin-bottom: 25px;
}
.lavea-admin-content .page-header h1 {
    font-size: 26px;
    color: #10204a;
}
.lavea-admin-content .page-header p {
    color: #7a879d;
    font-size: 13px;
    margin-top: 6px;
}
.lavea-admin-content .back-btn {
    background: white;
    color: #526078;
    border: 1px solid #dfe5ef;
    padding: 11px 18px;
    border-radius: 8px;
    text-decoration: none;
    font-size: 13px;
}
.lavea-admin-content .back-btn:hover {
    background: #f1f4f9;
}
.lavea-admin-content .card {
    background: white;
    border: 1px solid #e7ebf2;
    border-radius: 10px;
    padding: 28px;
    max-width: 950px;
}
.lavea-admin-content .payment-header {
    display: flex;
    justify-content: space-between;
    align-items: center;
    padding-bottom: 22px;
    border-bottom: 1px solid #edf0f5;
    margin-bottom: 25px;
}
.lavea-admin-content .payment-id {
    font-size: 18px;
    font-weight: 700;
    color: #172554;
}
.lavea-admin-content .payment-id span {
    color: #7a879d;
    font-size: 12px;
    font-weight: 400;
}
.lavea-admin-content .status {
    display: inline-block;
    padding: 7px 12px;
    border-radius: 6px;
    font-size: 11px;
    font-weight: 600;
}
.lavea-admin-content .confirmed {
    background: #e5f8ef;
    color: #168458;
}
.lavea-admin-content .pending {
    background: #fff4d6;
    color: #a56a00;
}
.lavea-admin-content .rejected {
    background: #ffe9ec;
    color: #d14a5b;
}
.lavea-admin-content .grid {
    display: grid;
    grid-template-columns: 1fr 1fr;
    gap: 22px 35px;
}
.lavea-admin-content .item {
    border-bottom: 1px solid #edf0f5;
    padding-bottom: 15px;
}
.lavea-admin-content .label {
    color: #7a879d;
    font-size: 11px;
    margin-bottom: 7px;
}
.lavea-admin-content .value {
    color: #263550;
    font-size: 14px;
    font-weight: 500;
}
.lavea-admin-content .amount-box {
    margin-top: 25px;
    background: #f4f7ff;
    border: 1px solid #dce5fb;
    border-radius: 9px;
    padding: 20px;
    display: flex;
    justify-content: space-between;
    align-items: center;
}
.lavea-admin-content .amount-label {
    color: #66738b;
    font-size: 13px;
}
.lavea-admin-content .amount {
    color: #4169c8;
    font-size: 25px;
    font-weight: 700;
}
.lavea-admin-content .notes {
    margin-top: 25px;
}
.lavea-admin-content .notes-content {
    background: #f8f9fc;
    border-radius: 8px;
    padding: 15px;
    color: #526078;
    font-size: 13px;
    line-height: 1.6;
    min-height: 55px;
}
.lavea-admin-content .actions {
    display: flex;
    gap: 10px;
    margin-top: 25px;
}
.lavea-admin-content .edit-btn {
    display: inline-flex;
    align-items: center;
    gap: 7px;
    background: #4169c8;
    color: white;
    padding: 11px 19px;
    border-radius: 8px;
    text-decoration: none;
    font-size: 13px;
}
.lavea-admin-content .edit-btn svg {
    width: 16px;
    height: 16px;
}
.lavea-admin-content .edit-btn:hover {
    background: #3459b1;
}
@media(max-width: 800px) {
    .lavea-admin-content .grid {
        grid-template-columns: 1fr;
    }
}
</style>
@endpush

@section('content')
@if ($errors->any())
    <div class="error-list no-print" role="alert" style="margin-bottom:16px">
        @foreach ($errors->all() as $message)<p>{{ $message }}</p>@endforeach
    </div>
@endif
<div class="page-header">

            <div>

                <h1>Payment Details</h1>

                <p>
                    View complete information about this transaction.
                </p>

            </div>

            <a
                href="{{ auth()->user()->role === 'manager' ? route('manager.orders.index') : route('admin.payments.index') }}"
                class="lavea-back-link"
            >
                <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.8" stroke-linecap="round" stroke-linejoin="round" aria-hidden="true"><path d="m12 19-7-7 7-7M5 12h14" /></svg>{{ auth()->user()->role === 'manager' ? 'Back to Orders' : 'Back to Payments' }}
            </a>

        </div>

        <div class="card">

            <div class="payment-header">

                <div class="payment-id">

                    Payment #{{ $payment->id }}

                    <span>
                        &mdash; Order #{{ $payment->order->id ?? 'N/A' }}
                    </span>

                </div>

                <span class="status {{ strtolower($payment->status) }}">

                    {{ $payment->status }}

                </span>

            </div>

            <div class="grid">

                <div class="item">

                    <div class="label">
                        Customer
                    </div>

                    <div class="value">
                        {{ $payment->order->customer->name ?? 'N/A' }}
                    </div>

                </div>

                <div class="item">

                    <div class="label">
                        Service
                    </div>

                    <div class="value">
                        {{ $payment->order->service->service_name ?? 'N/A' }}
                    </div>

                </div>

                <div class="item">

                    <div class="label">
                        Order Total
                    </div>

                    <div class="value">
                        ₱{{ number_format($payment->order->total ?? 0, 2) }}
                    </div>

                </div>

                <div class="item">

                    <div class="label">
                        Payment Method
                    </div>

                    <div class="value">
                        {{ $payment->payment_method }}
                    </div>

                </div>

                <div class="item">

                    <div class="label">
                        Reference Number
                    </div>

                    <div class="value">
                        {{ $payment->reference_number ?: 'Not provided' }}
                    </div>

                </div>

                <div class="item">

                    <div class="label">
                        Payment Date
                    </div>

                    <div class="value">

                        {{ $payment->payment_date
                            ? $payment->payment_date->format('F d, Y')
                            : 'Not available'
                        }}

                    </div>

                </div>

            </div>

            <div class="amount-box">

                <div class="amount-label">
                    Payment Amount
                </div>

                <div class="amount">
                    ₱{{ number_format($payment->amount, 2) }}
                </div>

            </div>

            <div class="notes">

                <div class="label">
                    Notes
                </div>

                <div class="notes-content">
                    {{ $payment->notes ?: 'No notes added.' }}
                </div>

            </div>

            @if ($payment->status === 'Pending')
                @if ($duplicatePayments->isNotEmpty() || $payment->amount > $remainingBalance)
                    <div class="notes-content no-print" role="alert" style="margin-bottom:16px">
                        <strong>Payment #{{ $payment->id }} is still Pending and needs correction.</strong>
                        @if ($duplicatePayments->isNotEmpty())
                            <p style="margin-top:8px">This reference is already used by:
                                @foreach ($duplicatePayments as $duplicate)
                                    <a href="{{ route('admin.payments.show', $duplicate) }}">Confirmed Payment #{{ $duplicate->id }} (Order #{{ $duplicate->order_id }})</a>{{ $loop->last ? '.' : ', ' }}
                                @endforeach
                            </p>
                        @endif
                        @if ($payment->amount > $remainingBalance)
                            <p style="margin-top:8px">Submitted amount: &#8369;{{ number_format($payment->amount, 2) }}. Remaining order balance: &#8369;{{ number_format($remainingBalance, 2) }}.</p>
                        @endif
                        <p style="margin-top:8px">Reject with a reason so the Manager can correct the amount, reference, and proof, then resubmit for review.</p>
                    </div>
                @endif
                <div class="actions">
                    <form method="POST" action="{{ route('admin.payments.review', $payment) }}">@csrf @method('PATCH')<input type="hidden" name="status" value="Confirmed"><button class="edit-btn" type="submit" @disabled($duplicatePayments->isNotEmpty() || $payment->amount > $remainingBalance)>Confirm Payment</button></form>
                    <form method="POST" action="{{ route('admin.payments.review', $payment) }}" style="display:flex;gap:10px;flex-wrap:wrap;align-items:center">
                        @csrf @method('PATCH')<input type="hidden" name="status" value="Rejected">
                        <label for="rejection_reason">Rejection reason</label>
                        <input id="rejection_reason" name="rejection_reason" maxlength="2000" value="{{ old('rejection_reason') }}" required style="padding:10px;border:1px solid #dfe5ef;border-radius:8px;max-width:100%;min-width:0">
                        <button class="back-btn" type="submit">Reject Payment</button>
                        @error('rejection_reason')<span class="error">{{ $message }}</span>@enderror
                    </form>
                </div>
            @endif

            @can('print-receipt', $payment)
                <div class="actions">
                    <a class="edit-btn" href="{{ route('payments.receipt', $payment) }}" target="_blank" rel="noopener"><i data-lucide="printer" aria-hidden="true"></i>Print Receipt</a>
                </div>

            @endcan

        </div>
@include('partials.payment-proof', ['payment' => $payment])
@can('print-receipt', $payment)
    @include('partials.payment-receipt', ['payment' => $payment])
@endcan
@endsection
