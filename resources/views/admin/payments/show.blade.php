@extends('admin.layout', ['title' => 'Payment Details'])

@push('styles')

<style>
.lavea-admin-content * { margin: 0; padding: 0; }
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
.lavea-admin-content .completed {
            background: #e5f8ef;
            color: #168458;
        }
.lavea-admin-content .pending {
            background: #fff4d6;
            color: #a56a00;
        }
.lavea-admin-content .failed {
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
            background: #4169c8;
            color: white;
            padding: 11px 19px;
            border-radius: 8px;
            text-decoration: none;
            font-size: 13px;
        }
.lavea-admin-content .edit-btn:hover {
            background: #3459b1;
        }
.lavea-admin-content .receipt {
            display: none;
        }
@media print {
.lavea-admin-content * {
                visibility: hidden !important;
            }
.lavea-admin-content #payment-receipt,
.lavea-admin-content #payment-receipt * {
                visibility: visible !important;
            }
.lavea-admin-content #payment-receipt {
                display: block !important;
                position: absolute;
                top: 0;
                left: 0;
                width: 100%;
                padding: 24px;
                background: white !important;
                color: #111 !important;
                font-family: Arial, Helvetica, sans-serif;
            }
.lavea-admin-content #payment-receipt h1 {
                margin-bottom: 20px;
            }
.lavea-admin-content #payment-receipt p {
                padding: 10px 0;
                border-bottom: 1px solid #ddd;
            }
}
@media(max-width: 800px) {
.lavea-admin-content .grid {
                grid-template-columns: 1fr;
            }
}
</style>
@endpush

@section('content')
<div class="page-header">

            <div>

                <h1>Payment Details</h1>

                <p>
                    View complete information about this transaction.
                </p>

            </div>

            <a
                href="{{ auth()->user()->role === 'staff' ? route('staff.orders.index') : route('admin.payments.index') }}"
                class="back-btn"
            >
                {{ auth()->user()->role === 'staff' ? 'Back to Orders' : 'Back to Payments' }}
            </a>

        </div>


        <div class="card">

            <div class="payment-header">

                <div class="payment-id">

                    Payment #{{ $payment->id }}

                    <span>
                        â€” Order #{{ $payment->order->id ?? 'N/A' }}
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
                        {{ $payment->reference_number ?: 'â€”' }}
                    </div>

                </div>


                <div class="item">

                    <div class="label">
                        Payment Date
                    </div>

                    <div class="value">

                        {{ $payment->payment_date
                            ? $payment->payment_date->format('F d, Y')
                            : 'â€”'
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
                <div class="actions">
                    <form method="POST" action="{{ route('admin.payments.review', $payment) }}">@csrf @method('PATCH')<input type="hidden" name="status" value="Completed"><button class="edit-btn" type="submit">Confirm Payment</button></form>
                    <form method="POST" action="{{ route('admin.payments.review', $payment) }}" onsubmit="return confirm('Mark this payment as failed?')">@csrf @method('PATCH')<input type="hidden" name="status" value="Failed"><button class="back-btn" type="submit">Mark Failed</button></form>
                </div>
            @endif

            @if ($payment->status === 'Completed')
                <div class="actions">
                    <button type="button" class="edit-btn" onclick="window.print()">Print Receipt</button>
                </div>

                <div id="payment-receipt" class="receipt">
                    <h1>LAVEA Payment Receipt</h1>
                    <p><strong>Order ID:</strong> #{{ $payment->order->id ?? 'N/A' }}</p>
                    <p><strong>Customer:</strong> {{ $payment->order->customer->name ?? 'N/A' }}</p>
                    <p><strong>Service:</strong> {{ $payment->order->service->service_name ?? 'N/A' }}</p>
                    <p><strong>Amount:</strong> ₱{{ number_format($payment->amount, 2) }}</p>
                    <p><strong>Payment Method:</strong> {{ $payment->payment_method }}</p>
                    <p><strong>Payment Date:</strong> {{ $payment->payment_date ? $payment->payment_date->format('F d, Y') : '—' }}</p>
                    <p><strong>Payment Status:</strong> {{ $payment->status }}</p>
                </div>
            @endif


        </div>
@endsection
