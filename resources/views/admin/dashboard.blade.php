@extends('admin.layout', ['title' => 'Admin Dashboard'])

@push('styles')

<style>
.lavea-admin-content * { margin: 0; padding: 0; }
.lavea-admin-content .notification-dot {
            position: absolute;
            width: 7px;
            height: 7px;
            background: #ef4444;
            border-radius: 50%;
            top: 0;
            right: 0;
        }
.lavea-admin-content .welcome {
            display: flex;
            justify-content: space-between;
            align-items: flex-start;
            margin-bottom: 22px;
        }
.lavea-admin-content .welcome h1 {
            font-size: 26px;
            color: #10204a;
            margin-bottom: 7px;
        }
.lavea-admin-content .welcome p {
            color: #7a879d;
            font-size: 13px;
        }
.lavea-admin-content .date {
            text-align: right;
            color: #687691;
            font-size: 12px;
        }
.lavea-admin-content .date strong {
            display: block;
            color: #53627e;
            margin-bottom: 5px;
        }
.lavea-admin-content .stats {
            display: grid;
            grid-template-columns: repeat(4, 1fr);
            gap: 17px;
            margin-bottom: 22px;
        }
.lavea-admin-content .stat-card {
            background: white;
            border: 1px solid #e7ebf2;
            border-radius: 10px;
            padding: 19px;
            display: flex;
            gap: 14px;
            align-items: flex-start;
        }
.lavea-admin-content .stat-icon {
            width: 45px;
            height: 45px;
            border-radius: 50%;
            display: flex;
            align-items: center;
            justify-content: center;
            flex-shrink: 0;
        }
.lavea-admin-content .stat-icon svg {
            width: 21px;
        }
.lavea-admin-content .blue {
            background: #e7efff;
            color: #3973e6;
        }
.lavea-admin-content .green {
            background: #e4f8ef;
            color: #18a56c;
        }
.lavea-admin-content .purple {
            background: #f0e9ff;
            color: #7c4bd8;
        }
.lavea-admin-content .orange {
            background: #fff2df;
            color: #ef941e;
        }
.lavea-admin-content .stat-title {
            font-size: 12px;
            color: #52617d;
            margin-bottom: 8px;
        }
.lavea-admin-content .stat-number {
            font-size: 23px;
            font-weight: 700;
            color: #12214a;
        }
.lavea-admin-content .stat-change {
            font-size: 10px;
            color: #13a16a;
            margin-top: 7px;
        }
.lavea-admin-content .stat-sub {
            font-size: 10px;
            color: #8b96a9;
            margin-top: 3px;
        }
.lavea-admin-content .dashboard-grid {
            display: grid;
            grid-template-columns: 2fr 1fr;
            gap: 17px;
            margin-bottom: 22px;
        }
.lavea-admin-content .card {
            background: white;
            border: 1px solid #e7ebf2;
            border-radius: 10px;
            padding: 20px;
        }
.lavea-admin-content .card-header {
            display: flex;
            justify-content: space-between;
            align-items: center;
            margin-bottom: 20px;
        }
.lavea-admin-content .card-header h2 {
            font-size: 16px;
            color: #12214a;
        }
.lavea-admin-content .filter {
            border: 1px solid #dfe5ef;
            background: white;
            border-radius: 7px;
            padding: 8px 11px;
            font-size: 11px;
            color: #52617d;
        }
.lavea-admin-content .chart {
            height: 245px;
            display: flex;
            align-items: flex-end;
            gap: 18px;
            padding: 15px 5px 0;
            border-bottom: 1px solid #e6eaf1;
            position: relative;
        }
.lavea-admin-content .chart::before,
.lavea-admin-content .chart::after {
            content: "";
            position: absolute;
            left: 0;
            right: 0;
            border-top: 1px dashed #edf0f5;
        }
.lavea-admin-content .chart::before {
            top: 55px;
        }
.lavea-admin-content .chart::after {
            top: 130px;
        }
.lavea-admin-content .bar-wrap {
            flex: 1;
            height: 100%;
            display: flex;
            flex-direction: column;
            justify-content: flex-end;
            align-items: center;
            position: relative;
            z-index: 1;
        }
.lavea-admin-content .bar {
            width: 65%;
            background: #5b8def;
            border-radius: 5px 5px 0 0;
            min-height: 15px;
        }
.lavea-admin-content .bar-label {
            margin-top: 8px;
            font-size: 9px;
            color: #8994a8;
        }
.lavea-admin-content .status-container {
            display: flex;
            flex-direction: column;
            align-items: center;
        }
.lavea-admin-content .donut {
            width: 150px;
            height: 150px;
            border-radius: 50%;
            display: flex;
            align-items: center;
            justify-content: center;
            margin-bottom: 20px;
        }
.lavea-admin-content .donut-center {
            width: 103px;
            height: 103px;
            background: white;
            border-radius: 50%;
            display: flex;
            align-items: center;
            justify-content: center;
            flex-direction: column;
        }
.lavea-admin-content .donut-center strong {
            font-size: 23px;
        }
.lavea-admin-content .donut-center span {
            font-size: 10px;
            color: #8792a6;
            margin-top: 3px;
        }
.lavea-admin-content .status-list {
            width: 100%;
        }
.lavea-admin-content .status-row {
            display: flex;
            justify-content: space-between;
            align-items: center;
            font-size: 11px;
            padding: 6px 0;
        }
.lavea-admin-content .status-name {
            display: flex;
            align-items: center;
            gap: 8px;
        }
.lavea-admin-content .status-dot {
            width: 8px;
            height: 8px;
            border-radius: 50%;
        }
.lavea-admin-content .pending {
            background: #f4b740;
        }
.lavea-admin-content .progress {
            background: #4f86ee;
        }
.lavea-admin-content .completed {
            background: #2cae83;
        }
.lavea-admin-content .cancelled {
            background: #ed6475;
        }
.lavea-admin-content .bottom-grid {
            display: grid;
            grid-template-columns: 2fr 1fr;
            gap: 17px;
        }
.lavea-admin-content .orders-table {
            width: 100%;
            border-collapse: collapse;
        }
.lavea-admin-content .orders-table th {
            background: #f8f9fc;
            color: #718099;
            font-size: 10px;
            font-weight: 500;
            padding: 11px 8px;
            text-align: left;
        }
.lavea-admin-content .orders-table td {
            padding: 12px 8px;
            border-bottom: 1px solid #edf0f5;
            font-size: 10px;
            color: #44526d;
        }
.lavea-admin-content .status-badge {
            display: inline-block;
            padding: 5px 8px;
            border-radius: 20px;
            font-size: 9px;
        }
.lavea-admin-content .badge-progress {
            background: #e5efff;
            color: #3973e6;
        }
.lavea-admin-content .badge-completed {
            background: #e4f8ef;
            color: #139363;
        }
.lavea-admin-content .badge-pending {
            background: #fff3da;
            color: #d48a10;
        }
.lavea-admin-content .view-all {
            color: #3973e6;
            text-decoration: none;
            font-size: 11px;
        }
.lavea-admin-content .activity {
            display: flex;
            flex-direction: column;
            gap: 17px;
        }
.lavea-admin-content .activity-item {
            display: flex;
            gap: 11px;
            align-items: flex-start;
        }
.lavea-admin-content .activity-icon {
            width: 32px;
            height: 32px;
            border-radius: 50%;
            background: #edf3ff;
            color: #4b7fe8;
            display: flex;
            align-items: center;
            justify-content: center;
            flex-shrink: 0;
        }
.lavea-admin-content .activity-icon svg {
            width: 15px;
        }
.lavea-admin-content .activity-text {
            font-size: 10px;
            color: #44526d;
            line-height: 1.5;
        }
.lavea-admin-content .activity-time {
            color: #9aa4b5;
            font-size: 9px;
            margin-top: 2px;
        }
@media (max-width: 1100px) {
.lavea-admin-content .stats {
                grid-template-columns: repeat(2, 1fr);
            }
.lavea-admin-content .dashboard-grid,
.lavea-admin-content .bottom-grid {
                grid-template-columns: 1fr;
            }
}
@media (max-width: 600px) {
.lavea-admin-content .stats {
                grid-template-columns: 1fr;
            }
.lavea-admin-content .welcome {
                flex-direction: column;
                gap: 10px;
            }
.lavea-admin-content .date {
                text-align: left;
            }
.lavea-admin-content .orders-table {
                min-width: 650px;
            }
.lavea-admin-content .card {
                overflow-x: auto;
            }
}
</style>
@endpush

@section('content')
<!-- SIDEBAR -->
    


    <!-- MAIN -->
    

        <!-- TOPBAR -->
        


        <!-- CONTENT -->
        

            <!-- WELCOME -->
            <div class="welcome">

                <div>
                    <h1>Good evening, Admin!</h1>
                    <p>Here's what's happening with your laundry business today.</p>
                </div>

                @include('admin.partials.current-date')

            </div>


            <!-- STAT CARDS -->
            <div class="stats">

                <div class="stat-card">

                    <div class="stat-icon blue">
                        <i data-lucide="shopping-cart"></i>
                    </div>

                    <div>
                        <div class="stat-title">Total Orders</div>
                        <div class="stat-number">{{ $stats['orders'] }}</div>
                        <div class="stat-change">&uarr; 12%</div>
                        <div class="stat-sub">vs. last week</div>
                    </div>

                </div>


                <div class="stat-card">

                    <div class="stat-icon green">
                        <i data-lucide="users"></i>
                    </div>

                    <div>
                        <div class="stat-title">Customers</div>
                        <div class="stat-number">{{ $stats['customers'] }}</div>
                        <div class="stat-change">&uarr; 8%</div>
                        <div class="stat-sub">vs. last week</div>
                    </div>

                </div>


                <div class="stat-card">

                    <div class="stat-icon purple">
                        <i data-lucide="user-round"></i>
                    </div>

                    <div>
                        <div class="stat-title">Staff</div>
                        <div class="stat-number">{{ $stats['staff'] }}</div>
                        <div class="stat-change">&uarr; 2%</div>
                        <div class="stat-sub">vs. last week</div>
                    </div>

                </div>


                <div class="stat-card">

                    <div class="stat-icon orange">
                        <i data-lucide="philippine-peso"></i>
                    </div>

                    <div>
                        <div class="stat-title">Total Income</div>
                        <div class="stat-number">
                            &#8369;{{ number_format($stats['income'], 2) }}
                        </div>
                        <div class="stat-change">&uarr; 15%</div>
                        <div class="stat-sub">vs. last week</div>
                    </div>

                </div>

            </div>


            <!-- SALES + STATUS -->
            <div class="dashboard-grid">

                <div class="card">

                    <div class="card-header">
                        <h2>Sales Overview</h2>

                        <form method="GET" action="{{ route('admin.dashboard') }}">
                        <select class="filter" name="period" onchange="this.form.submit()">
                            <option value="7days" {{ $period === '7days' ? 'selected' : '' }}>Last 7 Days</option>
                            <option value="30days" {{ $period === '30days' ? 'selected' : '' }}>Last 30 Days</option>
                            <option value="year" {{ $period === 'year' ? 'selected' : '' }}>This Year</option>
                        </select>
                        </form>
                    </div>

                    <div class="chart">
                        @foreach ($salesBars as $bar)
                            <div class="bar-wrap">
                                <div class="bar" style="height: {{ $bar['height'] }}%;" title="₱{{ number_format($bar['amount'], 2) }}"></div>
                                <div class="bar-label">{{ $bar['label'] }}</div>
                            </div>
                        @endforeach
                    </div>

                </div>


                <!-- ORDER STATUS -->
                <div class="card">

                    <div class="card-header">
                        <h2>Order Status</h2>
                    </div>

                    <div class="status-container">

                        <div class="donut" style="@if ($stats['orders'] > 0) background: conic-gradient(#f4b740 0deg {{ $statusDegrees['pending'] }}deg, #4f86ee {{ $statusDegrees['pending'] }}deg {{ $statusDegrees['progress'] }}deg, #2cae83 {{ $statusDegrees['progress'] }}deg {{ $statusDegrees['completed'] }}deg, #ed6475 {{ $statusDegrees['completed'] }}deg 360deg); @else background: #edf0f5; @endif">

                            <div class="donut-center">
                                <strong>{{ $stats['orders'] }}</strong>
                                <span>Total Orders</span>
                            </div>

                        </div>

                        <div class="status-list">

                            <div class="status-row">
                                <div class="status-name">
                                    <span class="status-dot pending"></span>
                                    Pending
                                </div>
                                <strong>{{ $stats['pending'] }}</strong>
                            </div>

                            <div class="status-row">
                                <div class="status-name">
                                    <span class="status-dot progress"></span>
                                    In Progress
                                </div>
                                <strong>{{ $stats['in_progress'] }}</strong>
                            </div>

                            <div class="status-row">
                                <div class="status-name">
                                    <span class="status-dot completed"></span>
                                    Completed
                                </div>
                                <strong>{{ $stats['completed'] }}</strong>
                            </div>

                            <div class="status-row">
                                <div class="status-name">
                                    <span class="status-dot cancelled"></span>
                                    Cancelled
                                </div>
                                <strong>{{ $stats['cancelled'] }}</strong>
                            </div>

                        </div>

                    </div>

                </div>

            </div>


            <!-- RECENT ORDERS + ACTIVITY -->
            <div class="bottom-grid">

                <!-- RECENT ORDERS -->
                <div class="card">

                    <div class="card-header">

                        <h2>Recent Orders</h2>

                        <a href="{{ route('admin.orders.index') }}" class="view-all">
                            View All
                        </a>

                    </div>

                    <table class="orders-table">

                        <thead>
                            <tr>
                                <th>Order No.</th>
                                <th>Customer</th>
                                <th>Service</th>
                                <th>Status</th>
                                <th>Total</th>
                                <th>Date</th>
                            </tr>
                        </thead>

                        <tbody>

                            @foreach($recentOrders as $order)

                                <tr>

                                    <td>#LVE-{{ str_pad($order->id, 4, '0', STR_PAD_LEFT) }}</td>

                                    <td>{{ $order->customer->name ?? 'N/A' }}</td>

                                    <td>{{ $order->service->service_name ?? 'N/A' }}</td>

                                    <td>

                                        @if($order->status === 'Completed')

                                            <span class="status-badge badge-completed">
                                                Completed
                                            </span>

                                        @elseif($order->status === 'Pending')

                                            <span class="status-badge badge-pending">
                                                Pending
                                            </span>

                                        @elseif(in_array($order->status, ['Processing', 'Ready', 'In Progress']))

                                            <span class="status-badge badge-progress">
                                                In Progress
                                            </span>

                                        @else
                                            <span class="status-badge badge-progress">
                                                {{ $order->status }}
                                            </span>

                                        @endif

                                    </td>

                                    <td>
                                        ₱{{ number_format($order->total, 2) }}
                                    </td>

                                    <td>
                                        {{ $order->order_date ? \Illuminate\Support\Carbon::parse($order->order_date)->format('M d, Y') : 'N/A' }}
                                    </td>

                                </tr>

                            @endforeach

                        </tbody>

                    </table>

                </div>


                <!-- RECENT ACTIVITY -->
                <div class="card">

                    <div class="card-header">
                        <h2>Recent Activity</h2>
                    </div>

                    <div class="activity">
                        @forelse ($recentActivities as $activity)
                            <div class="activity-item">
                                <div class="activity-icon">
                                    <i data-lucide="{{ $activity['icon'] }}"></i>
                                </div>
                                <div class="activity-text">
                                    <strong>{{ $activity['title'] }}</strong>
                                    <div class="activity-time">
                                        {{ $activity['details'] }} · {{ $activity['date']->format('M d, h:i A') }}
                                    </div>
                                </div>
                            </div>
                        @empty
                            <div class="activity-item">
                                <div class="activity-text"><strong>No recent activity</strong></div>
                            </div>
                        @endforelse
                    </div>

                </div>

            </div>
@endsection
