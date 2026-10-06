@extends('admin.layout', ['title' => 'Schedule | LAVEA'])

@push('styles')

<style>
.lavea-admin-content * { margin: 0; padding: 0; }

.lavea-admin-content .avatar {
            width: 40px;
            height: 40px;
            background: #e9eefb;
            border-radius: 50%;
            display: flex;
            align-items: center;
            justify-content: center;
            color: #4169c8;
        }
.lavea-admin-content .page-header {
            display: flex;
            justify-content: space-between;
            align-items: center;
            margin-bottom: 25px;
        }
.lavea-admin-content .page-header h2 {
            font-size: 24px;
            color: #17233d;
        }
.lavea-admin-content .page-header p {
            color: #8a94a6;
            font-size: 13px;
            margin-top: 5px;
        }
.lavea-admin-content .add-btn {
            background: #4169c8;
            color: white;
            text-decoration: none;
            border: none;
            padding: 11px 17px;
            border-radius: 7px;
            font-size: 13px;
            display: flex;
            align-items: center;
            gap: 8px;
        }
.lavea-admin-content .add-btn:hover {
            background: #355ab0;
        }
.lavea-admin-content .add-btn i {
            width: 17px;
        }
.lavea-admin-content .alert {
            background: #eaf7ef;
            border: 1px solid #c9ead5;
            color: #267344;
            padding: 12px 15px;
            border-radius: 7px;
            margin-bottom: 20px;
            font-size: 13px;
        }
.lavea-admin-content .card {
            background: white;
            border: 1px solid #e5e7eb;
            border-radius: 10px;
            overflow: hidden;
        }
.lavea-admin-content .card-header {
            padding: 20px 22px;
            border-bottom: 1px solid #e5e7eb;
        }
.lavea-admin-content .card-header h3 {
            font-size: 16px;
            color: #17233d;
        }
.lavea-admin-content .card-header p {
            font-size: 12px;
            color: #8a94a6;
            margin-top: 4px;
        }
.lavea-admin-content .table-wrapper {
            overflow-x: auto;
        }
.lavea-admin-content table {
            width: 100%;
            border-collapse: collapse;
        }
.lavea-admin-content th {
            background: #f8f9fc;
            color: #697386;
            font-size: 11px;
            text-transform: uppercase;
            letter-spacing: 0.4px;
            text-align: left;
            padding: 14px 18px;
            border-bottom: 1px solid #e5e7eb;
            white-space: nowrap;
        }
.lavea-admin-content td {
            padding: 15px 18px;
            border-bottom: 1px solid #eef0f4;
            font-size: 13px;
            color: #374151;
            white-space: nowrap;
        }
.lavea-admin-content tr:last-child td {
            border-bottom: none;
        }
.lavea-admin-content tr:hover td {
            background: #fafbfe;
        }
.lavea-admin-content .customer-name {
            font-weight: 600;
            color: #17233d;
        }
.lavea-admin-content .order-number {
            color: #4169c8;
            font-weight: 600;
        }
.lavea-admin-content .schedule-type {
            font-weight: 600;
        }
.lavea-admin-content .status {
            display: inline-block;
            padding: 5px 9px;
            border-radius: 20px;
            font-size: 11px;
            font-weight: 600;
        }
.lavea-admin-content .status-scheduled {
            background: #fff5d9;
            color: #9a6b00;
        }
.lavea-admin-content .status-on-time {
            background: #e7f0ff;
            color: #315db8;
        }
.lavea-admin-content .status-completed {
            background: #e8f7ee;
            color: #267344;
        }
.lavea-admin-content .status-cancelled {
            background: #fdecec;
            color: #b33a3a;
        }
.lavea-admin-content .actions {
            display: flex;
            align-items: center;
            gap: 6px;
        }
.lavea-admin-content .action-btn {
            width: 32px;
            height: 32px;
            border-radius: 6px;
            display: flex;
            align-items: center;
            justify-content: center;
            text-decoration: none;
            border: none;
            cursor: pointer;
        }
.lavea-admin-content .action-btn i {
            width: 15px;
            height: 15px;
        }
.lavea-admin-content .view-btn {
            background: #e9eefb;
            color: #4169c8;
        }
.lavea-admin-content .edit-btn {
            background: #fff4df;
            color: #a66b00;
        }
.lavea-admin-content .delete-btn {
            background: #fdeaea;
            color: #c23c3c;
        }
.lavea-admin-content .action-btn:hover {
            opacity: 0.8;
        }
.lavea-admin-content .empty {
            text-align: center;
            padding: 45px 20px;
            color: #8a94a6;
            font-size: 13px;
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
                    <h2>Schedule</h2>
                    <p>{{ auth()->user()->role === 'staff' ? 'Manage pickup and delivery schedules.' : 'View pickup and delivery schedules.' }}</p>
                </div>

                <div class="lavea-page-header-actions">
                    @if (auth()->user()->role === 'staff')
                    <a href="{{ route($routePrefix.'.create') }}" class="add-btn">
                        <i data-lucide="plus"></i>
                        Add Schedule
                    </a>
                    @endif
                    @include('admin.partials.current-date')
                </div>

            </div>


            @if(session('success'))
                <div class="alert">
                    {{ session('success') }}
                </div>
            @endif


            <div class="card">

                <div class="card-header">
                    <h3>Schedule List</h3>
                    <p>Pickup and delivery schedule records.</p>
                </div>

                <div class="table-wrapper">

                    <table>

                        <thead>
                            <tr>
                                <th>ID</th>
                                <th>Order</th>
                                <th>Customer</th>
                                <th>Service</th>
                                <th>Type</th>
                                <th>Date</th>
                                <th>Time</th>
                                <th>Assigned Staff</th>
                                <th>Status</th>
                                <th>Actions</th>
                            </tr>
                        </thead>

                        <tbody>

                            @forelse($schedules as $schedule)

                                <tr>

                                    <td>
                                        {{ $schedule->id }}
                                    </td>

                                    <td>
                                        <span class="order-number">
                                            #LVE-{{ str_pad($schedule->order_id, 4, '0', STR_PAD_LEFT) }}
                                        </span>
                                    </td>

                                    <td>
                                        <span class="customer-name">
                                            {{ $schedule->order->customer->name ?? 'N/A' }}
                                        </span>
                                    </td>

                                    <td>
                                        {{ $schedule->order->service->service_name ?? 'N/A' }}
                                    </td>

                                    <td>
                                        <span class="schedule-type">
                                            {{ $schedule->type }}
                                        </span>
                                    </td>

                                    <td>
                                        {{ $schedule->schedule_date?->format('M d, Y') }}
                                    </td>

                                    <td>
                                        {{ $schedule->schedule_time }}
                                    </td>

                                    <td>
                                        {{ $schedule->order->staff->name ?? 'Not assigned' }}
                                    </td>

                                    <td>

                                        @if($schedule->status === 'Scheduled')

                                            <span class="status status-scheduled">
                                                Scheduled
                                            </span>

                                        @elseif($schedule->status === 'On Time')

                                            <span class="status status-on-time">
                                                On Time
                                            </span>

                                        @elseif($schedule->status === 'Completed')

                                            <span class="status status-completed">
                                                Completed
                                            </span>

                                        @else

                                            <span class="status status-cancelled">
                                                Cancelled
                                            </span>

                                        @endif

                                    </td>

                                    <td>

                                        <div class="actions">

                                            <a
                                                href="{{ route($routePrefix.'.show', $schedule->id) }}"
                                                class="action-btn view-btn"
                                                title="View"
                                            >
                                                <i data-lucide="eye"></i>
                                            </a>

                                            @if (auth()->user()->role === 'staff')
                                                <a
                                                    href="{{ route($routePrefix.'.edit', $schedule->id) }}"
                                                    class="action-btn edit-btn"
                                                    title="Edit"
                                                >
                                                    <i data-lucide="pencil"></i>
                                                </a>
                                            @endif

                                        </div>

                                    </td>

                                </tr>

                            @empty

                                <tr>
                                    <td colspan="10" class="empty">
                                        No schedules found.
                                    </td>
                                </tr>

                            @endforelse

                        </tbody>

                    </table>

                </div>

            </div>

        

    

@endsection
