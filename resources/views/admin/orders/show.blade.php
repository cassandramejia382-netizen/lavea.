@extends('admin.layout', ['title' => 'Order Details - LAVEA'])

@push('styles')

<style>
.lavea-admin-content * { margin: 0; padding: 0; }
.lavea-admin-content .top {
            display: flex;
            justify-content: space-between;
            align-items: center;
            margin-bottom: 30px;
        }
.lavea-admin-content .top h2 {
            font-size: 25px;
            color: #17233d;
        }
.lavea-admin-content .top p {
            color: #7c879b;
            font-size: 14px;
            margin-top: 6px;
        }
.lavea-admin-content .back-btn {
            text-decoration: none;
            background: white;
            color: #34415c;
            border: 1px solid #dfe4ed;
            padding: 11px 18px;
            border-radius: 8px;
            font-size: 14px;
        }
.lavea-admin-content .back-btn:hover {
            background: #f0f3f8;
        }
.lavea-admin-content .card {
            background: white;
            border-radius: 12px;
            padding: 28px;
            border: 1px solid #e5e9f0;
            max-width: 1000px;
        }
.lavea-admin-content .card-title {
            font-size: 18px;
            font-weight: 600;
            color: #17233d;
            margin-bottom: 22px;
        }
.lavea-admin-content .grid {
            display: grid;
            grid-template-columns: repeat(2, 1fr);
            gap: 20px 35px;
        }
.lavea-admin-content .item {
            border-bottom: 1px solid #edf0f5;
            padding-bottom: 14px;
        }
.lavea-admin-content .label {
            color: #7b879c;
            font-size: 12px;
            margin-bottom: 6px;
        }
.lavea-admin-content .value {
            color: #1c2942;
            font-size: 15px;
            font-weight: 500;
        }
.lavea-admin-content .total-box {
            margin-top: 25px;
            background: #f4f7ff;
            border: 1px solid #dce5fb;
            border-radius: 10px;
            padding: 20px;
            display: flex;
            justify-content: space-between;
            align-items: center;
        }
.lavea-admin-content .total-label {
            color: #66738b;
            font-size: 14px;
        }
.lavea-admin-content .total {
            font-size: 24px;
            font-weight: 700;
            color: #4169c8;
        }
.lavea-admin-content .status,
.lavea-admin-content .payment {
            display: inline-block;
            padding: 6px 11px;
            border-radius: 20px;
            font-size: 12px;
            font-weight: 600;
        }
.lavea-admin-content .pending {
            background: #fff4d8;
            color: #9a6a00;
        }
.lavea-admin-content .processing {
            background: #e7f0ff;
            color: #315da8;
        }
.lavea-admin-content .ready {
            background: #e8f8f0;
            color: #20845a;
        }
.lavea-admin-content .completed {
            background: #e4f7ed;
            color: #18794e;
        }
.lavea-admin-content .cancelled {
            background: #fde9e9;
            color: #b33a3a;
        }
.lavea-admin-content .paid {
            background: #e4f7ed;
            color: #18794e;
        }
.lavea-admin-content .partial {
            background: #fff4d8;
            color: #9a6a00;
        }
.lavea-admin-content .unpaid {
            background: #fde9e9;
            color: #b33a3a;
        }
.lavea-admin-content .notes {
            margin-top: 25px;
        }
.lavea-admin-content .notes-content {
            margin-top: 8px;
            background: #f8f9fc;
            border-radius: 8px;
            padding: 15px;
            color: #526078;
            font-size: 14px;
            line-height: 1.6;
            min-height: 60px;
        }
.lavea-admin-content .actions {
            margin-top: 25px;
            display: flex;
            gap: 10px;
        }
.lavea-admin-content .edit-btn {
            text-decoration: none;
            background: #4169c8;
            color: white;
            padding: 11px 20px;
            border-radius: 8px;
            font-size: 14px;
        }
.lavea-admin-content .edit-btn:hover {
            background: #3458ad;
        }
@media (max-width: 800px) {
.lavea-admin-content .grid {
                grid-template-columns: 1fr;
            }
}
</style>
@endpush

@section('content')
<div class="top">
        <div>
            <h2>Order Details</h2>
            <p>View complete information about this order.</p>
        </div>

        <a href="{{ route($orderRoutePrefix.'.index') }}" class="back-btn">
            Back to Orders
        </a>
    </div>

    <div class="card">

        <div class="card-title">
            Order #{{ $order->id }}
        </div>

        <div class="grid">

            <div class="item">
                <div class="label">Customer</div>
                <div class="value">
                    {{ $order->customer->name ?? 'N/A' }}
                </div>
            </div>

            <div class="item">
                <div class="label">Service</div>
                <div class="value">
                    {{ $order->service->service_name ?? 'N/A' }}
                </div>
            </div>

            <div class="item">
                <div class="label">Assigned Staff</div>
                <div class="value">
                    {{ $order->staff->name ?? 'Not Assigned' }}
                </div>
            </div>

            <div class="item">
                <div class="label">Quantity</div>
                <div class="value">
                    {{ number_format($order->quantity, 2) }}
                </div>
            </div>

            <div class="item">
                <div class="label">Order Date</div>
                <div class="value">
                    {{ $order->order_date ? $order->order_date->format('F d, Y') : 'N/A' }}
                </div>
            </div>

            <div class="item">
                <div class="label">Pickup Date</div>
                <div class="value">
                    {{ $order->pickup_date ? $order->pickup_date->format('F d, Y') : 'N/A' }}
                </div>
            </div>

            <div class="item">
                <div class="label">Delivery Date</div>
                <div class="value">
                    {{ $order->delivery_date ? $order->delivery_date->format('F d, Y') : 'N/A' }}
                </div>
            </div>

            <div class="item">
                <div class="label">Status</div>
                <div class="value">
                    @php
                        $statusClass = strtolower($order->status);
                    @endphp

                    <span class="status {{ $statusClass }}">
                        {{ $order->status }}
                    </span>
                </div>
            </div>

            <div class="item">
                <div class="label">Payment Status</div>
                <div class="value">
                    @php
                        $paymentClass = strtolower($order->payment_status);
                    @endphp

                    <span class="payment {{ $paymentClass }}">
                        {{ $order->payment_status }}
                    </span>
                </div>
            </div>

        </div>

        <div class="total-box">
            <div class="total-label">
                Total Amount
            </div>

            <div class="total">
                ₱{{ number_format($order->total, 2) }}
            </div>
        </div>

        <div class="notes">

            <div class="label">Notes</div>

            <div class="notes-content">
                {{ $order->notes ?: 'No notes added.' }}
            </div>

        </div>

    </div>
@endsection
