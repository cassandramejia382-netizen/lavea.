@extends('admin.layout', ['title' => 'View Schedule - LAVEA'])

@push('styles')

<style>
.lavea-admin-content * { margin: 0; padding: 0; }
.lavea-admin-content .menu-title {
            font-size: 11px;
            color: #7f8ca8;
            padding: 0 15px 10px;
            text-transform: uppercase;
            letter-spacing: 1px;
        }
.lavea-admin-content .page-header {
            display: flex;
            align-items: center;
            justify-content: space-between;
            margin-bottom: 25px;
        }
.lavea-admin-content .page-header h2 {
            font-size: 24px;
            color: #111827;
            margin-bottom: 6px;
        }
.lavea-admin-content .page-header p {
            color: #6b7280;
            font-size: 14px;
        }
.lavea-admin-content .header-actions {
            display: flex;
            gap: 10px;
        }
.lavea-admin-content .btn {
            display: inline-flex;
            align-items: center;
            gap: 8px;
            padding: 10px 16px;
            border-radius: 7px;
            text-decoration: none;
            font-size: 14px;
            border: none;
            cursor: pointer;
        }
.lavea-admin-content .btn svg {
            width: 17px;
            height: 17px;
        }
.lavea-admin-content .btn-back {
            background: white;
            color: #374151;
            border: 1px solid #d1d5db;
        }
.lavea-admin-content .btn-back:hover {
            background: #f9fafb;
        }
.lavea-admin-content .btn-edit {
            background: #4169c8;
            color: white;
        }
.lavea-admin-content .btn-edit:hover {
            background: #3558ad;
        }
.lavea-admin-content .card {
            background: white;
            border: 1px solid #e5e7eb;
            border-radius: 10px;
            overflow: hidden;
        }
.lavea-admin-content .card-header {
            padding: 20px 25px;
            border-bottom: 1px solid #e5e7eb;
        }
.lavea-admin-content .card-header h3 {
            font-size: 17px;
            color: #111827;
        }
.lavea-admin-content .card-header p {
            font-size: 13px;
            color: #6b7280;
            margin-top: 4px;
        }
.lavea-admin-content .details {
            padding: 25px;
            display: grid;
            grid-template-columns: repeat(2, 1fr);
            gap: 22px 35px;
        }
.lavea-admin-content .detail-item {
            border-bottom: 1px solid #eef0f4;
            padding-bottom: 15px;
        }
.lavea-admin-content .detail-label {
            font-size: 12px;
            color: #6b7280;
            margin-bottom: 7px;
            text-transform: uppercase;
            letter-spacing: 0.5px;
        }
.lavea-admin-content .detail-value {
            font-size: 15px;
            color: #111827;
            font-weight: 500;
        }
.lavea-admin-content .status {
            display: inline-block;
            padding: 5px 11px;
            border-radius: 20px;
            font-size: 12px;
            font-weight: 600;
        }
.lavea-admin-content .status-scheduled {
            background: #e8eefc;
            color: #4169c8;
        }
.lavea-admin-content .status-on-time {
            background: #e7f6ed;
            color: #198754;
        }
.lavea-admin-content .status-completed {
            background: #dcfce7;
            color: #15803d;
        }
.lavea-admin-content .status-cancelled {
            background: #fee2e2;
            color: #dc2626;
        }
.lavea-admin-content .notes {
            grid-column: 1 / -1;
        }
.lavea-admin-content .notes .detail-value {
            line-height: 1.6;
            color: #4b5563;
            font-weight: normal;
        }
@media (max-width: 700px) {
.lavea-admin-content .details {
                grid-template-columns: 1fr;
            }
.lavea-admin-content .notes {
                grid-column: auto;
            }
.lavea-admin-content .page-header {
                align-items: flex-start;
                gap: 15px;
            }
}
</style>
@endpush

@section('content')
<!-- SIDEBAR -->
    


    <!-- TOPBAR -->
    


    <!-- MAIN CONTENT -->
    

        <div class="page-header">

            <div>
                <h2>Schedule Details</h2>
                <p>View the complete information for this schedule.</p>
            </div>

            <div class="header-actions">

                <a href="{{ route($routePrefix.'.index') }}" class="btn btn-back">
                    <i data-lucide="arrow-left"></i>
                    Back
                </a>

                @if (auth()->user()->role === 'staff')
                    <a href="{{ route($routePrefix.'.edit', $schedule->id) }}" class="btn btn-edit">
                        <i data-lucide="pencil"></i>
                        Edit Schedule
                    </a>
                @endif

            </div>

        </div>


        <!-- DETAILS CARD -->
        <div class="card">

            <div class="card-header">
                <h3>Schedule Information</h3>
                <p>Details about the selected pickup or delivery schedule.</p>
            </div>

            <div class="details">

                <!-- ID -->
                <div class="detail-item">

                    <div class="detail-label">
                        Schedule ID
                    </div>

                    <div class="detail-value">
                        #{{ $schedule->id }}
                    </div>

                </div>

                <div class="detail-item">
                    <div class="detail-label">Assigned Staff</div>
                    <div class="detail-value">{{ $schedule->order->staff->name ?? 'Not assigned' }}</div>
                </div>


                <!-- ORDER -->
                <div class="detail-item">

                    <div class="detail-label">
                        Order
                    </div>

                    <div class="detail-value">
                        #{{ $schedule->order_id }}
                    </div>

                </div>


                <!-- CUSTOMER -->
                <div class="detail-item">

                    <div class="detail-label">
                        Customer
                    </div>

                    <div class="detail-value">
                        {{ $schedule->order->customer->name ?? 'N/A' }}
                    </div>

                </div>


                <!-- SERVICE -->
                <div class="detail-item">

                    <div class="detail-label">
                        Service
                    </div>

                    <div class="detail-value">
                        {{ $schedule->order->service->service_name ?? 'N/A' }}
                    </div>

                </div>


                <!-- TYPE -->
                <div class="detail-item">

                    <div class="detail-label">
                        Schedule Type
                    </div>

                    <div class="detail-value">
                        {{ $schedule->type }}
                    </div>

                </div>


                <!-- DATE -->
                <div class="detail-item">

                    <div class="detail-label">
                        Schedule Date
                    </div>

                    <div class="detail-value">
                        {{ $schedule->schedule_date?->format('M d, Y') }}
                    </div>

                </div>


                <!-- TIME -->
                <div class="detail-item">

                    <div class="detail-label">
                        Schedule Time
                    </div>

                    <div class="detail-value">
                        {{ \Carbon\Carbon::parse($schedule->schedule_time)->format('h:i A') }}
                    </div>

                </div>


                <!-- STATUS -->
                <div class="detail-item">

                    <div class="detail-label">
                        Status
                    </div>

                    <div class="detail-value">

                        @php
                            $statusClass = match($schedule->status) {
                                'Scheduled' => 'status-scheduled',
                                'On Time' => 'status-on-time',
                                'Completed' => 'status-completed',
                                'Cancelled' => 'status-cancelled',
                                default => 'status-scheduled',
                            };
                        @endphp

                        <span class="status {{ $statusClass }}">
                            {{ $schedule->status }}
                        </span>

                    </div>

                </div>


                <!-- NOTES -->
                <div class="detail-item notes">

                    <div class="detail-label">
                        Notes
                    </div>

                    <div class="detail-value">
                        {{ $schedule->notes ?: 'No notes added.' }}
                    </div>

                </div>

            </div>

        </div>
@endsection
