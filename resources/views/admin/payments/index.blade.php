@extends('admin.layout', ['title' => 'Payments'])

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
.lavea-admin-content .filters {
            display: flex;
            align-items: center;
            gap: 10px;
            margin-bottom: 18px;
        }
.lavea-admin-content .filters select,
.lavea-admin-content .filter-btn {
            height: 40px;
            border: 1px solid #dfe5ef;
            border-radius: 8px;
            padding: 0 12px;
            background: white;
            color: #44526d;
            font-size: 13px;
        }
.lavea-admin-content .filter-btn {
            background: #4169c8;
            color: white;
            border: none;
            cursor: pointer;
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
            min-width: 1000px;
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
.lavea-admin-content .payment-id {
            font-weight: bold;
            color: #172554;
        }
.lavea-admin-content .customer-name {
            font-weight: bold;
            color: #172554;
        }
.lavea-admin-content .service-name {
            color: #44526d;
        }
.lavea-admin-content .amount {
            font-weight: bold;
            color: #172554;
            white-space: nowrap;
        }
.lavea-admin-content .method {
            color: #44526d;
        }
.lavea-admin-content .reference {
            color: #718099;
        }
.lavea-admin-content .date {
            white-space: nowrap;
        }
.lavea-admin-content .status {
            display: inline-block;
            padding: 6px 10px;
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
                    <h1>Payments / Transactions</h1>
                    <p>View and monitor customer payments and transactions.</p>
                </div>

                <div class="lavea-page-header-actions">
                    @include('admin.partials.current-date')
                </div>

            </div>


            @if(session('success'))

                <div class="success">
                    {{ session('success') }}
                </div>

            @endif


            <form method="GET" action="{{ route('admin.payments.index') }}" class="filters">
                <select name="status" aria-label="Filter by payment status">
                    <option value="">All statuses</option>
                    @foreach (['Completed', 'Pending', 'Failed'] as $paymentStatus)
                        <option value="{{ $paymentStatus }}" @selected($status === $paymentStatus)>{{ $paymentStatus }}</option>
                    @endforeach
                </select>
                @if ($search !== '')
                    <input type="hidden" name="search" value="{{ $search }}">
                @endif
                <button type="submit" class="filter-btn">Filter</button>
            </form>

            <div class="card">

                <div class="table-container">

                    <table>

                        <thead>

                            <tr>

                                <th>Customer</th>

                                <th>Order ID</th>

                                <th>Amount</th>

                                <th>Payment Method</th>

                                <th>Payment Date</th>

                                <th>Status</th>

                                <th>View Details</th>

                            </tr>

                        </thead>


                        <tbody>

                            @forelse($payments as $payment)

                                <tr>

                                    <td>

                                        <span class="customer-name">
                                            {{ $payment->order->customer->name ?? 'N/A' }}
                                        </span>

                                    </td>


                                    <td>#{{ $payment->order_id }}</td>


                                    <td>

                                        <span class="amount">
                                            ₱{{ number_format($payment->amount, 2) }}
                                        </span>

                                    </td>


                                    <td>

                                        <span class="method">
                                            {{ $payment->payment_method }}
                                        </span>

                                    </td>


                                    <td class="date">

                                        {{ $payment->payment_date
                                            ? $payment->payment_date->format('M d, Y')
                                            : '—'
                                        }}

                                    </td>


                                    <td>

                                        <span class="status
                                            {{ strtolower($payment->status) }}">

                                            {{ $payment->status }}

                                        </span>

                                    </td>


                                    <td>

                                        <a href="{{ route('admin.payments.show', $payment->id) }}" class="action view" title="View Details">
                                            <i data-lucide="eye"></i>
                                        </a>

                                    </td>

                                </tr>


                            @empty

                                <tr>

                                    <td colspan="7">

                                        <div class="empty">

                                            <i data-lucide="credit-card"></i>

                                            <p>No payment transactions found.</p>

                                        </div>

                                    </td>

                                </tr>

                            @endforelse

                        </tbody>

                    </table>

                </div>

            </div>

        

    

@endsection
