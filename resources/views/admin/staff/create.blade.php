@extends(auth()->user()->role === 'manager' ? 'staff.layout' : 'admin.layout', ['title' => ($isEditing ?? false) ? 'Edit Staff' : 'Add Staff'])

@push('styles')

<style>
#custom-position-field[hidden] { display: none !important; }
.lavea-admin-content * {
    margin: 0;
    padding: 0;
}
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
@php($staffRoutePrefix = auth()->user()->role.'.staff')
<div class="lavea-admin-content">

    <!-- =========================
         SIDEBAR
    ========================= -->

    <!-- =========================
         MAIN
    ========================= -->

        <!-- TOPBAR -->

        <!-- CONTENT -->

            <div class="page-header">

                <h2>{{ ($isEditing ?? false) ? 'Edit Staff' : 'Add Staff' }}</h2>

                <p>
                    {{ ($isEditing ?? false) ? 'Update the staff member\'s information below.' : 'Enter the staff member\'s information below.' }}
                </p>

            </div>

            <!-- FORM CARD -->
            <div class="card">

                <form
                    action="{{ ($isEditing ?? false) ? route($staffRoutePrefix.'.update', $staff) : route($staffRoutePrefix.'.store') }}"
                    method="POST"
                >

                    @csrf
                    @if ($isEditing ?? false)
                        @method('PUT')
                    @endif

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
                                value="{{ old('name', $staff->name ?? '') }}"
                                placeholder="Enter full name"
                                required
                            >

                            @error('name')
                                <div class="error">
                                    {{ $message }}
                                </div>
                            @enderror

                        </div>

                        <div class="form-group">

                            <label for="phone">
                                Phone Number
                            </label>

                            <input
                                type="text"
                                id="phone"
                                name="phone"
                                value="{{ old('phone', $staff->phone ?? '') }}"
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
                                Job Position
                            </label>

                            @php($currentPosition = $staff->role ?? '')
                            @php($selectedPosition = old('role', in_array($currentPosition, \App\Models\Staff::JOB_POSITIONS, true) ? $currentPosition : ($currentPosition !== '' ? 'Other' : '')))
                            <select id="role" name="role" required>
                                <option value="">Select a job position</option>
                                @foreach(\App\Models\Staff::JOB_POSITIONS as $position)
                                    <option value="{{ $position }}" @selected($selectedPosition === $position)>{{ $position }}</option>
                                @endforeach
                                <option value="Other" @selected($selectedPosition === 'Other')>Other</option>
                            </select>
                            @error('role')
                                <div class="error">
                                    {{ $message }}
                                </div>
                            @enderror

                        </div>

                        <div class="form-group" id="custom-position-field" @if($selectedPosition !== 'Other') hidden @endif>
                            <label for="custom_position">Custom Job Position</label>
                            <input id="custom_position" name="custom_position" type="text" maxlength="100" value="{{ old('custom_position', !in_array($currentPosition, \App\Models\Staff::JOB_POSITIONS, true) ? $currentPosition : '') }}" placeholder="Enter job position" @required($selectedPosition === 'Other') @disabled($selectedPosition !== 'Other')>
                            @error('custom_position')<div class="error">{{ $message }}</div>@enderror
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
                                    {{ old('status', $staff->status ?? 'Active') == 'Active' ? 'selected' : '' }}
                                >
                                    Active
                                </option>

                                <option
                                    value="Inactive"
                                    {{ old('status', $staff->status ?? 'Active') == 'Inactive' ? 'selected' : '' }}
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
                            href="{{ route($staffRoutePrefix.'.index') }}"
                            class="btn btn-cancel"
                        >
                            Cancel
                        </a>

                        <button
                            type="submit"
                            class="btn btn-primary"
                        >
                            <i data-lucide="{{ ($isEditing ?? false) ? 'save' : 'plus' }}"></i>
                            {{ ($isEditing ?? false) ? 'Save Changes' : 'Add Staff' }}
                        </button>

                    </div>

                </form>

            </div>

</div>
<script>
    (() => {
        const position = document.getElementById('role');
        const field = document.getElementById('custom-position-field');
        const input = document.getElementById('custom_position');
        const renderPosition = () => {
            const isOther = position.value === 'Other';
            field.hidden = !isOther;
            input.disabled = !isOther;
            input.required = isOther;
        };
        position.addEventListener('change', renderPosition);
        renderPosition();
    })();
</script>
@endsection
