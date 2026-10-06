@extends('admin.layout', ['title' => 'Add Schedule | LAVEA'])

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
.lavea-admin-content .card {
            background: white;
            border: 1px solid #e5e7eb;
            border-radius: 10px;
            padding: 25px;
            max-width: 900px;
        }
.lavea-admin-content .form-grid {
            display: grid;
            grid-template-columns: 1fr 1fr;
            gap: 20px;
        }
.lavea-admin-content .form-group {
            display: flex;
            flex-direction: column;
        }
.lavea-admin-content .form-group.full {
            grid-column: 1 / -1;
        }
.lavea-admin-content label {
            font-size: 13px;
            font-weight: 600;
            color: #374151;
            margin-bottom: 8px;
        }
.lavea-admin-content label span {
            color: #d64545;
        }
.lavea-admin-content input,
.lavea-admin-content select,
.lavea-admin-content textarea {
            width: 100%;
            border: 1px solid #dfe3ea;
            border-radius: 7px;
            padding: 11px 12px;
            font-size: 13px;
            outline: none;
            background: white;
            color: #374151;
        }
.lavea-admin-content input:focus,
.lavea-admin-content select:focus,
.lavea-admin-content textarea:focus {
            border-color: #4169c8;
            box-shadow: 0 0 0 2px rgba(65,105,200,0.08);
        }
.lavea-admin-content textarea {
            resize: vertical;
            min-height: 100px;
        }
.lavea-admin-content .error {
            color: #c23c3c;
            font-size: 11px;
            margin-top: 5px;
        }
.lavea-admin-content .form-actions {
            display: flex;
            justify-content: flex-end;
            gap: 10px;
            margin-top: 25px;
            padding-top: 20px;
            border-top: 1px solid #e5e7eb;
        }
.lavea-admin-content .btn {
            border: none;
            border-radius: 7px;
            padding: 11px 18px;
            font-size: 13px;
            text-decoration: none;
            cursor: pointer;
        }
.lavea-admin-content .cancel-btn {
            background: #eef1f5;
            color: #4b5563;
        }
.lavea-admin-content .save-btn {
            background: #4169c8;
            color: white;
        }
.lavea-admin-content .save-btn:hover {
            background: #355ab0;
        }
@media (max-width: 800px) {
.lavea-admin-content .form-grid {
                grid-template-columns: 1fr;
            }
.lavea-admin-content .form-group.full {
                grid-column: auto;
            }
}
</style>
@endpush

@section('content')
@php($routePrefix = 'admin.schedules')


    <!-- SIDEBAR -->
    


    <!-- MAIN -->
    

        <!-- TOPBAR -->
        


        <!-- CONTENT -->
        

            <div class="page-header">

                <h2>Add Schedule</h2>

                <p>
                    Create a new pickup or delivery schedule.
                </p>

            </div>


            <div class="card">

                <form
                    action="{{ route($routePrefix.'.store') }}"
                    method="POST"
                >

                    @csrf

                    <div class="form-grid">

                        <!-- ORDER -->
                        <div class="form-group full">

                            <label>
                                Order <span>*</span>
                            </label>

                            <select name="order_id" required>

                                <option value="">
                                    Select Order
                                </option>

                                @foreach($orders as $order)

                                    <option
                                        value="{{ $order->id }}"
                                        {{ old('order_id') == $order->id ? 'selected' : '' }}
                                    >
                                        #LVE-{{ str_pad($order->id, 4, '0', STR_PAD_LEFT) }}
                                        -
                                        {{ $order->customer->name ?? 'N/A' }}
                                        -
                                        {{ $order->service->service_name ?? 'N/A' }}
                                    </option>

                                @endforeach

                            </select>

                            @error('order_id')
                                <div class="error">
                                    {{ $message }}
                                </div>
                            @enderror

                        </div>

                        <!-- TYPE -->
                        <div class="form-group">

                            <label>
                                Schedule Type <span>*</span>
                            </label>

                            <select name="type" required>

                                <option value="">
                                    Select Type
                                </option>

                                <option
                                    value="Pickup"
                                    {{ old('type') == 'Pickup' ? 'selected' : '' }}
                                >
                                    Pickup
                                </option>

                                <option
                                    value="Delivery"
                                    {{ old('type') == 'Delivery' ? 'selected' : '' }}
                                >
                                    Delivery
                                </option>

                            </select>

                            @error('type')
                                <div class="error">
                                    {{ $message }}
                                </div>
                            @enderror

                        </div>


                        <!-- STATUS -->
                        <div class="form-group">

                            <label>
                                Status <span>*</span>
                            </label>

                            <select name="status" required>

                                <option
                                    value="Scheduled"
                                    {{ old('status', 'Scheduled') == 'Scheduled' ? 'selected' : '' }}
                                >
                                    Scheduled
                                </option>

                                <option
                                    value="On Time"
                                    {{ old('status') == 'On Time' ? 'selected' : '' }}
                                >
                                    On Time
                                </option>

                                <option
                                    value="Completed"
                                    {{ old('status') == 'Completed' ? 'selected' : '' }}
                                >
                                    Completed
                                </option>

                                <option
                                    value="Cancelled"
                                    {{ old('status') == 'Cancelled' ? 'selected' : '' }}
                                >
                                    Cancelled
                                </option>

                            </select>

                            @error('status')
                                <div class="error">
                                    {{ $message }}
                                </div>
                            @enderror

                        </div>


                        <!-- DATE -->
                        <div class="form-group">

                            <label>
                                Schedule Date <span>*</span>
                            </label>

                            <input
                                type="date"
                                name="schedule_date"
                                value="{{ old('schedule_date') }}"
                                min="{{ now()->toDateString() }}"
                                required
                            >

                            @error('schedule_date')
                                <div class="error">
                                    {{ $message }}
                                </div>
                            @enderror

                        </div>


                        <!-- TIME -->
                        <div class="form-group">

                            <label>
                                Schedule Time <span>*</span>
                            </label>

                            <input
                                type="time"
                                name="schedule_time"
                                value="{{ old('schedule_time') }}"
                                required
                            >

                            @error('schedule_time')
                                <div class="error">
                                    {{ $message }}
                                </div>
                            @enderror

                        </div>


                        <!-- NOTES -->
                        <div class="form-group full">

                            <label>
                                Notes
                            </label>

                            <textarea
                                name="notes"
                                placeholder="Enter additional notes..."
                            >{{ old('notes') }}</textarea>

                            @error('notes')
                                <div class="error">
                                    {{ $message }}
                                </div>
                            @enderror

                        </div>

                    </div>


                    <!-- BUTTONS -->
                    <div class="form-actions">

                        <a
                            href="{{ route($routePrefix.'.index') }}"
                            class="btn cancel-btn"
                        >
                            Cancel
                        </a>

                        <button
                            type="submit"
                            class="btn save-btn"
                        >
                            Save Schedule
                        </button>

                    </div>

                </form>

            </div>

        

    

@endsection
