@extends('admin.layout', ['title' => 'Add Staff'])

@push('styles')

<style>
.lavea-admin-content * { margin: 0; padding: 0; }

.lavea-admin-content .topbar-title h1 {
            font-size: 22px;
            color: #10204a;
        }
.lavea-admin-content .topbar-title p {
            color: #8995aa;
            font-size: 12px;
            margin-top: 4px;
        }
.lavea-admin-content .avatar {
            width: 38px;
            height: 38px;
            background: #e8eefb;
            color: #4169c8;
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
            margin-bottom: 25px;
        }
.lavea-admin-content .page-header h2 {
            font-size: 26px;
            color: #10204a;
        }
.lavea-admin-content .page-header p {
            color: #7a879d;
            font-size: 13px;
            margin-top: 6px;
        }
.lavea-admin-content .card {
            background: white;
            border: 1px solid #e7ebf2;
            border-radius: 10px;
            padding: 25px;
            max-width: 850px;
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
            font-size: 12px;
            font-weight: 600;
            color: #44526d;
            margin-bottom: 7px;
        }
.lavea-admin-content input,
.lavea-admin-content select {
            width: 100%;
            height: 42px;
            border: 1px solid #dfe5ef;
            border-radius: 8px;
            padding: 0 12px;
            outline: none;
            font-size: 13px;
            color: #172554;
            background: white;
        }
.lavea-admin-content input:focus,
.lavea-admin-content select:focus {
            border-color: #4169c8;
        }
.lavea-admin-content .error {
            color: #e05263;
            font-size: 11px;
            margin-top: 5px;
        }
.lavea-admin-content .form-actions {
            display: flex;
            justify-content: flex-end;
            gap: 10px;
            margin-top: 25px;
            padding-top: 20px;
            border-top: 1px solid #edf0f5;
        }
.lavea-admin-content .btn {
            height: 40px;
            padding: 0 17px;
            border-radius: 8px;
            border: none;
            text-decoration: none;
            display: inline-flex;
            align-items: center;
            justify-content: center;
            gap: 7px;
            font-size: 13px;
            cursor: pointer;
        }
.lavea-admin-content .btn svg {
            width: 16px;
            height: 16px;
        }
.lavea-admin-content .btn-cancel {
            background: #f1f3f7;
            color: #596579;
        }
.lavea-admin-content .btn-cancel:hover {
            background: #e5e8ee;
        }
.lavea-admin-content .btn-primary {
            background: #4169c8;
            color: white;
        }
.lavea-admin-content .btn-primary:hover {
            background: #3459b1;
        }
@media(max-width: 800px) {
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


    <!-- =========================
         SIDEBAR
    ========================= -->

    


    <!-- =========================
         MAIN
    ========================= -->

    

        <!-- TOPBAR -->
        


        <!-- CONTENT -->
        

            <div class="page-header">

                <h2>Add Staff</h2>

                <p>
                    Enter the staff member's information below.
                </p>

            </div>


            <!-- FORM CARD -->
            <div class="card">

                <form
                    action="{{ route('admin.staff.store') }}"
                    method="POST"
                >

                    @csrf

                    <div class="form-grid">

                        <!-- NAME -->
                        <div class="form-group full">

                            <label for="name">
                                Full Name
                            </label>

                            <input
                                type="text"
                                id="name"
                                name="name"
                                value="{{ old('name') }}"
                                placeholder="Enter full name"
                                required
                            >

                            @error('name')
                                <div class="error">
                                    {{ $message }}
                                </div>
                            @enderror

                        </div>


                        <!-- EMAIL -->
                        <div class="form-group">

                            <label for="email">
                                Email
                            </label>

                            <input
                                type="email"
                                id="email"
                                name="email"
                                value="{{ old('email') }}"
                                placeholder="Enter email address"
                                required
                            >

                            @error('email')
                                <div class="error">
                                    {{ $message }}
                                </div>
                            @enderror

                        </div>


                        <!-- PASSWORD -->
                        <div class="form-group">

                            <label for="password">
                                Password
                            </label>

                            <input
                                type="password"
                                id="password"
                                name="password"
                                placeholder="Enter password (at least 8 characters)"
                                autocomplete="new-password"
                                required
                            >

                            @error('password')
                                <div class="error">
                                    {{ $message }}
                                </div>
                            @enderror

                        </div>


                        <!-- CONFIRM PASSWORD -->
                        <div class="form-group">

                            <label for="password_confirmation">
                                Confirm Password
                            </label>

                            <input
                                type="password"
                                id="password_confirmation"
                                name="password_confirmation"
                                placeholder="Re-enter password"
                                autocomplete="new-password"
                                required
                            >

                        </div>


                        <!-- PHONE -->
                        <div class="form-group">

                            <label for="phone">
                                Phone Number
                            </label>

                            <input
                                type="text"
                                id="phone"
                                name="phone"
                                value="{{ old('phone') }}"
                                placeholder="Enter phone number"
                            >

                            @error('phone')
                                <div class="error">
                                    {{ $message }}
                                </div>
                            @enderror

                        </div>


                        <!-- ROLE -->
                        <div class="form-group">

                            <label for="role">
                                Role
                            </label>

                            <select
                                id="role"
                                name="role"
                                required
                            >

                                <option value="">
                                    Select role
                                </option>

                                <option
                                    value="Staff"
                                    {{ old('role') == 'Staff' ? 'selected' : '' }}
                                >
                                    Staff
                                </option>

                                <option
                                    value="Manager"
                                    {{ old('role') == 'Manager' ? 'selected' : '' }}
                                >
                                    Manager
                                </option>

                            </select>

                            @error('role')
                                <div class="error">
                                    {{ $message }}
                                </div>
                            @enderror

                        </div>


                        <!-- STATUS -->
                        <div class="form-group">

                            <label for="status">
                                Status
                            </label>

                            <select
                                id="status"
                                name="status"
                                required
                            >

                                <option
                                    value="Active"
                                    {{ old('status', 'Active') == 'Active' ? 'selected' : '' }}
                                >
                                    Active
                                </option>

                                <option
                                    value="Inactive"
                                    {{ old('status') == 'Inactive' ? 'selected' : '' }}
                                >
                                    Inactive
                                </option>

                            </select>

                            @error('status')
                                <div class="error">
                                    {{ $message }}
                                </div>
                            @enderror

                        </div>

                    </div>


                    <!-- BUTTONS -->
                    <div class="form-actions">

                        <a
                            href="{{ route('admin.staff.index') }}"
                            class="btn btn-cancel"
                        >
                            Cancel
                        </a>

                        <button
                            type="submit"
                            class="btn btn-primary"
                        >
                            <i data-lucide="user-plus"></i>
                            Add Staff
                        </button>

                    </div>

                </form>

            </div>

        

    

@endsection
