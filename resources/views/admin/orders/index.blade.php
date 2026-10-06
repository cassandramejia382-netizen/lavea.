@extends('admin.layout', ['title' => 'Orders'])

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
            height: 18px;
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
.lavea-admin-content .add-btn {
            background: #4169c8;
            color: white;
            padding: 11px 17px;
            border-radius: 8px;
            text-decoration: none;
            font-size: 13px;
            display: flex;
            align-items: center;
            gap: 8px;
        }
.lavea-admin-content .add-btn:hover {
            background: #3459b1;
        }
.lavea-admin-content .success {
            background: #e6f8ef;
            color: #168458;
            padding: 13px 16px;
            border-radius: 8px;
            margin-bottom: 20px;
            font-size: 13px;
        }
.lavea-admin-content .card {
            background: white;
            border: 1px solid #e7ebf2;
            border-radius: 10px;
            padding: 20px;
        }
.lavea-admin-content .table-container {
            overflow-x: auto;
        }
.lavea-admin-content table {
            width: 100%;
            border-collapse: collapse;
            min-width: 950px;
        }
.lavea-admin-content th {
            background: #f8f9fc;
            color: #718099;
            font-size: 11px;
            text-align: left;
            padding: 13px;
        }
.lavea-admin-content td {
            padding: 14px 13px;
            border-bottom: 1px solid #edf0f5;
            font-size: 12px;
            color: #44526d;
        }
.lavea-admin-content .customer-name {
            font-weight: bold;
            color: #172554;
        }
.lavea-admin-content .service-name {
            color: #44526d;
        }
.lavea-admin-content .staff-name {
            color: #44526d;
        }
.lavea-admin-content .date {
            white-space: nowrap;
        }
.lavea-admin-content .price {
            font-weight: bold;
            color: #172554;
            white-space: nowrap;
        }
.lavea-admin-content .status {
            display: inline-block;
            padding: 6px 10px;
            border-radius: 6px;
            font-size: 11px;
            font-weight: 600;
        }
.lavea-admin-content .pending {
            background: #fff4d6;
            color: #a56a00;
        }
.lavea-admin-content .processing {
            background: #e9f1ff;
            color: #3565b8;
        }
.lavea-admin-content .ready {
            background: #e9e7ff;
            color: #654bc4;
        }
.lavea-admin-content .completed {
            background: #e5f8ef;
            color: #168458;
        }
.lavea-admin-content .cancelled {
            background: #ffe9ec;
            color: #d14a5b;
        }
.lavea-admin-content .payment {
            font-size: 11px;
            font-weight: 600;
        }
.lavea-admin-content .paid {
            color: #168458;
        }
.lavea-admin-content .partial {
            color: #a56a00;
        }
.lavea-admin-content .unpaid {
            color: #d14a5b;
        }
.lavea-admin-content .actions {
            display: flex;
            gap: 7px;
        }
.lavea-admin-content .action {
            width: 34px;
            height: 34px;
            border-radius: 7px;
            display: flex;
            align-items: center;
            justify-content: center;
            text-decoration: none;
        }
.lavea-admin-content .action svg {
            width: 16px;
            height: 16px;
        }
.lavea-admin-content .view {
            background: #edf3ff;
            color: #3973e6;
        }
.lavea-admin-content .edit {
            background: #f0eaff;
            color: #7545d0;
        }
.lavea-admin-content .delete {
            background: #fff0f1;
            color: #e05263;
            border: none;
            cursor: pointer;
        }
.lavea-admin-content .empty {
            text-align: center;
            padding: 50px 20px;
            color: #8995aa;
        }
.lavea-admin-content .empty svg {
            width: 40px;
            height: 40px;
            margin-bottom: 10px;
        }
</style>
@endpush

@section('content')


    <!-- SIDEBAR -->

    


    <!-- MAIN -->

    

        


        

            <div class="page-header">

                <div>
                    <h1>Orders</h1>
                    <p>View and monitor customer laundry orders and schedules.</p>
                </div>

                <div class="lavea-page-header-actions">
                    @if ($canManageOrders)
                        <a href="{{ route($orderRoutePrefix.'.create') }}" class="add-btn">
                            <i data-lucide="plus"></i>
                            Add Order
                        </a>
                        <a href="{{ route('staff.payments.create') }}" class="add-btn">
                            <i data-lucide="credit-card"></i>
                            Add Payment
                        </a>
                    @endif
                    @include('admin.partials.current-date')
                </div>

            </div>


            @if(session('success'))

                <div class="success">
                    {{ session('success') }}
                </div>

            @endif


            <div class="card">

                <div class="table-container">

                    <table>

                        <thead>

                            <tr>

                                <th>Customer</th>

                                <th>Service</th>

                                <th>Staff</th>

                                <th>Qty</th>

                                <th>Order Date</th>

                                <th>Pickup</th>

                                <th>Delivery</th>

                                <th>Total</th>

                                <th>Status</th>

                                <th>Payment</th>

                                <th>{{ $canManageOrders ? 'Actions' : 'View' }}</th>

                            </tr>

                        </thead>


                        <tbody>

                            @forelse($orders as $order)

                                <tr>

                                    <td>

                                        <span class="customer-name">
                                            {{ $order->customer->name ?? 'N/A' }}
                                        </span>

                                    </td>


                                    <td>

                                        <span class="service-name">
                                            {{ $order->service->service_name ?? 'N/A' }}
                                        </span>

                                    </td>


                                    <td>

                                        <span class="staff-name">
                                            {{ $order->staff->name ?? 'Unassigned' }}
                                        </span>

                                    </td>


                                    <td>
                                        {{ number_format($order->quantity, 2) }}
                                    </td>


                                    <td class="date">

                                        {{ $order->order_date
                                            ? $order->order_date->format('M d, Y')
                                            : 'â€”'
                                        }}

                                    </td>


                                    <td class="date">

                                        {{ $order->pickup_date
                                            ? $order->pickup_date->format('M d, Y')
                                            : 'â€”'
                                        }}

                                    </td>


                                    <td class="date">

                                        {{ $order->delivery_date
                                            ? $order->delivery_date->format('M d, Y')
                                            : 'â€”'
                                        }}

                                    </td>


                                    <td class="price">

                                        ₱{{ number_format($order->total, 2) }}

                                    </td>


                                    <td>

                                        <span class="status
                                            {{ strtolower($order->status) }}">

                                            {{ $order->status }}

                                        </span>

                                    </td>


                                    <td>

                                        <span class="payment
                                            {{ strtolower($order->payment_status) }}">

                                            {{ $order->payment_status }}

                                        </span>

                                    </td>


                                    <td>

                                        <div class="actions">

                                            <a
                                                href="{{ route($orderRoutePrefix.'.show', $order->id) }}"
                                                class="action view"
                                                title="View"
                                            >
                                                <i data-lucide="eye"></i>
                                            </a>

                                            @if ($canManageOrders)
                                                <a href="{{ route($orderRoutePrefix.'.edit', $order->id) }}" class="action edit" title="Edit">
                                                    <i data-lucide="pencil"></i>
                                                </a>
                                            @endif


                                        </div>

                                    </td>

                                </tr>

                            @empty

                                <tr>

                                    <td colspan="10">

                                        <div class="empty">

                                            <i data-lucide="clipboard-x"></i>

                                            <p>No orders found.</p>

                                        </div>

                                    </td>

                                </tr>

                            @endforelse

                        </tbody>

                    </table>

                </div>

            </div>

        

    

@endsection
