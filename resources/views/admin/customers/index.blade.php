@extends('admin.layout', ['title' => 'Customers'])

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
            color: #4169c8;
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
.lavea-admin-content .add-btn svg {
            width: 16px;
            height: 16px;
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
.lavea-admin-content .customer {
            display: flex;
            align-items: center;
            gap: 11px;
        }
.lavea-admin-content .customer-avatar {
            width: 38px;
            height: 38px;
            background: #e8eefb;
            color: #4169c8;
            border-radius: 50%;
            display: flex;
            align-items: center;
            justify-content: center;
            flex-shrink: 0;
        }
.lavea-admin-content .customer-avatar svg {
            width: 17px;
            height: 17px;
        }
.lavea-admin-content .customer-name {
            font-weight: 600;
            color: #172554;
        }
.lavea-admin-content .customer-email {
            font-size: 10px;
            color: #8995aa;
            margin-top: 3px;
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
            border: none;
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
    

        <!-- TOPBAR -->
        


        <!-- CONTENT -->
        

            <div class="page-header">

                <div>
                    <h1>Customers</h1>
                    <p>Manage your laundry customers.</p>
                </div>

                @include('admin.partials.current-date')

            </div>


            @if(session('success'))

                <div class="success">
                    {{ session('success') }}
                </div>

            @endif


            <!-- CUSTOMER TABLE -->
            <div class="card">

                <div class="table-container">

                    <table>

                        <thead>

                            <tr>
                                <th>Customer</th>
                                <th>Phone</th>
                                <th>Address</th>
                                <th>View</th>
                            </tr>

                        </thead>

                        <tbody>

                            @forelse($customers as $customer)

                                <tr>

                                    <td>

                                        <div class="customer">

                                            <div class="customer-avatar">
                                                <i data-lucide="user"></i>
                                            </div>

                                            <div>

                                                <div class="customer-name">
                                                    {{ $customer->name }}
                                                </div>

                                                <div class="customer-email">
                                                    {{ $customer->email ?: 'No email' }}
                                                </div>

                                            </div>

                                        </div>

                                    </td>

                                    <td>
                                        {{ $customer->phone }}
                                    </td>

                                    <td>
                                        {{ $customer->address ?: 'No address' }}
                                    </td>

                                    <td>

                                        <div class="actions">

                                            <!-- VIEW -->
                                            <a
                                                href="{{ route('admin.customers.show', $customer->id) }}"
                                                class="action view"
                                                title="View"
                                                aria-label="View customer details"
                                            >
                                                <i data-lucide="eye"></i>
                                            </a>

                                        </div>

                                    </td>

                                </tr>

                            @empty

                                <tr>

                                    <td colspan="4">

                                        <div class="empty">

                                            <i data-lucide="users-round"></i>

                                            <p>No customers found.</p>

                                        </div>

                                    </td>

                                </tr>

                            @endforelse

                        </tbody>

                    </table>

                </div>

            </div>

        

    

@endsection
