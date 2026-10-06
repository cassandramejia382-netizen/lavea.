@extends('admin.layout', ['title' => 'Staff'])

@push('styles')

<style>
.lavea-admin-content * { margin: 0; padding: 0; }

.lavea-admin-content .title h1 {
            font-size: 26px;
            color: #10204a;
        }
.lavea-admin-content .title p {
            color: #7a879d;
            font-size: 13px;
            margin-top: 6px;
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
            font-weight: 600;
            font-size: 13px;
        }
.lavea-admin-content .success {
            background: #e6f8ef;
            color: #168458;
            padding: 13px 16px;
            border-radius: 8px;
            margin-bottom: 20px;
            font-size: 13px;
        }
.lavea-admin-content .email-error {
            background: #fff1f0;
            color: #b42318;
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
.lavea-admin-content .card-header {
            display: flex;
            justify-content: space-between;
            align-items: center;
            margin-bottom: 20px;
        }
.lavea-admin-content .card-header h2 {
            font-size: 19px;
            color: #172554;
        }
.lavea-admin-content .btn {
            border: none;
            border-radius: 8px;
            padding: 10px 14px;
            text-decoration: none;
            cursor: pointer;
            font-size: 13px;
            display: inline-flex;
            align-items: center;
            justify-content: center;
            gap: 6px;
        }
.lavea-admin-content .btn svg {
            width: 16px;
            height: 16px;
        }
.lavea-admin-content .btn-primary {
            background: #4169c8;
            color: white;
        }
.lavea-admin-content .btn-primary:hover {
            background: #3459b1;
        }
.lavea-admin-content .btn-view {
            background: #edf3ff;
            color: #3973e6;
        }
.lavea-admin-content .btn-edit {
            background: #f0eaff;
            color: #7545d0;
        }
.lavea-admin-content .btn-delete {
            background: #fff0f1;
            color: #e05263;
        }
.lavea-admin-content .table-container {
            overflow-x: auto;
        }
.lavea-admin-content table {
            width: 100%;
            border-collapse: collapse;
        }
.lavea-admin-content th {
            text-align: left;
            padding: 13px;
            background: #f8f9fc;
            color: #718099;
            font-size: 11px;
            font-weight: 600;
        }
.lavea-admin-content td {
            padding: 14px 13px;
            border-bottom: 1px solid #edf0f5;
            font-size: 12px;
            color: #44526d;
        }
.lavea-admin-content td strong {
            color: #172554;
            font-weight: 600;
        }
.lavea-admin-content .badge {
            padding: 5px 10px;
            border-radius: 20px;
            font-size: 11px;
            font-weight: 600;
        }
.lavea-admin-content .active-badge {
            background: #e6f8ef;
            color: #168458;
        }
.lavea-admin-content .inactive-badge {
            background: #fff0f1;
            color: #e05263;
        }
.lavea-admin-content .actions {
            display: flex;
            gap: 7px;
        }
.lavea-admin-content .actions form {
            display: inline;
        }
.lavea-admin-content .actions .btn {
            width: 34px;
            height: 34px;
            padding: 0;
        }
.lavea-admin-content .empty {
            text-align: center;
            padding: 50px 20px;
            color: #8995aa;
        }
</style>
@endpush

@section('content')


    <!-- =========================
         SIDEBAR
    ========================= -->

    


    <!-- =========================
         MAIN CONTENT
    ========================= -->

    

        <!-- TOPBAR -->
        

    <div class="lavea-staff-heading">
            <div>
                <h1>Staff</h1>
                <p>Manage LAVEA staff members and their roles.</p>
            </div>
            @include('admin.partials.current-date')
        </div>

        <!-- SUCCESS MESSAGE -->
        @if(session('success'))

            <div class="success">
                {{ session('success') }}
            </div>

        @endif

        @if(session('email_error'))
            <div class="email-error">{{ session('email_error') }}</div>
        @endif


        <!-- STAFF CARD -->
        <div class="card">

            <div class="card-header">

                <h2>Staff List</h2>

                <a
                    href="{{ route('admin.staff.create') }}"
                    class="btn btn-primary"
                >
                    <i data-lucide="plus"></i>
                    Add Staff
                </a>

            </div>


            <div class="table-container">

                <table>

                    <thead>

                        <tr>
                            <th>Name</th>
                            <th>Email</th>
                            <th>Phone</th>
                            <th>Role</th>
                            <th>Status</th>
                            <th>Actions</th>
                        </tr>

                    </thead>


                    <tbody>

                        @forelse($staff as $member)

                            <tr>

                                <td>
                                    <strong>
                                        {{ $member->name }}
                                    </strong>
                                </td>

                                <td>
                                    {{ $member->email ?? '—' }}
                                </td>

                                <td>
                                    {{ $member->phone ?? '—' }}
                                </td>

                                <td>
                                    {{ $member->role }}
                                </td>

                                <td>

                                    @if($member->status === 'Active')

                                        <span class="badge active-badge">
                                            Active
                                        </span>

                                    @else

                                        <span class="badge inactive-badge">
                                            Inactive
                                        </span>

                                    @endif

                                </td>

                                <td>

                                    <div class="actions">

                                        @if($member->user)
                                            <form action="{{ route('admin.staff.invitation', $member) }}" method="POST">
                                                @csrf
                                                <button type="submit" class="btn btn-view" title="Resend password setup link" aria-label="Resend password setup link">
                                                    <i data-lucide="mail"></i>
                                                </button>
                                            </form>
                                        @endif

                                        <!-- VIEW -->
                                        <a
                                            href="{{ route('admin.staff.show', $member->id) }}"
                                            class="btn btn-view"
                                            title="View"
                                        >
                                            <i data-lucide="eye"></i>
                                        </a>


                                        <!-- EDIT -->
                                        <a
                                            href="{{ route('admin.staff.edit', $member->id) }}"
                                            class="btn btn-edit"
                                            title="Edit"
                                        >
                                            <i data-lucide="pencil"></i>
                                        </a>


                                        <!-- DELETE -->
                                        <form
                                            action="{{ route('admin.staff.destroy', $member->id) }}"
                                            method="POST"
                                            onsubmit="return confirm('Delete this staff member?');"
                                        >

                                            @csrf
                                            @method('DELETE')

                                            <button
                                                type="submit"
                                                class="btn btn-delete"
                                                title="Delete"
                                            >
                                                <i data-lucide="trash-2"></i>
                                            </button>

                                        </form>

                                    </div>

                                </td>

                            </tr>

                        @empty

                            <tr>

                                <td
                                    colspan="6"
                                    class="empty"
                                >
                                    No staff members found.
                                </td>

                            </tr>

                        @endforelse

                    </tbody>

                </table>

            </div>

        </div>

    

@endsection
