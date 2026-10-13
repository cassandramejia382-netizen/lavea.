@extends('admin.layout', ['title' => 'Settings - LAVEA'])

@push('styles')
<link rel="stylesheet" href="{{ asset('css/lavea-settings.css') }}?v={{ filemtime(public_path('css/lavea-settings.css')) }}">
        <style>
.main > .lavea-admin-content {
    padding: 16px;
}
.lavea-admin-content .page-header {
    display: flex;
    justify-content: space-between;
    align-items: center;
    margin-bottom: 26px;
}
.lavea-admin-content .page-header h2 {
    font-size: 24px;
    color: #10204a;
    margin-bottom: 6px;
}
.lavea-admin-content .page-header p {
    color: #6b7280;
    font-size: 14px;
}
.lavea-admin-content .settings-card h3 {
    font-size: 17px;
}
.lavea-admin-content .settings-card > p {
    font-size: 14px;
}
</style>
@endpush

@section('content')
<!-- SIDEBAR -->

    <!-- TOPBAR -->

    <!-- MAIN -->

        <div class="page-header">
            <div>
                <h2>Settings</h2>
                <p>Manage your LAVEA system settings.</p>
            </div>
            @include('admin.partials.current-date')
        </div>

        @if (session('shop_success'))
            <div class="setting-message">{{ session('shop_success') }}</div>
        @endif
        @if (session('profile_success'))
            <div class="setting-message">{{ session('profile_success') }}</div>
        @endif
        @if (session('password_success'))
            <div class="setting-message">{{ session('password_success') }}</div>
        @endif
        @if (session('theme_success'))
            <div class="setting-message">{{ session('theme_success') }}</div>
        @endif

        @if (session('notification_success'))
            <div class="setting-message">{{ session('notification_success') }}</div>
        @endif

        <div class="settings-layout">
            <nav class="settings-nav" aria-label="Settings sections">
                <h3>Settings Menu</h3>
                <div class="settings-nav-links">
                    <a href="#shop-information"><i data-lucide="store"></i>Shop Information</a>
                    <a href="#profile-information"><i data-lucide="user-round"></i>Profile Information</a>
                    <a href="#change-password"><i data-lucide="lock-keyhole"></i>Security</a>
                    <a href="#appearance"><i data-lucide="palette"></i>Appearance</a>
                    <a href="#notifications"><i data-lucide="bell"></i>Notifications</a>
                    <a href="#gcash-account"><i data-lucide="wallet"></i>GCash Account</a>
                </div>
            </nav>

            <div class="settings-content">
                <div class="settings-card" id="shop-information">
                    <h3>Shop Information</h3>
                    <p>Update your laundry shop contact information.</p>

                    <form method="POST" action="{{ route('admin.settings.shop') }}">
                        @csrf
                        <div class="form-group">
                            <label for="shop_name">Shop Name</label>
                            <input id="shop_name" name="shop_name" type="text" value="{{ old('shop_name', $shopSettings->shop_name ?? 'LAVEA Laundry Shop') }}" required>
                            @error('shop_name')<div class="field-error">{{ $message }}</div>@enderror
                        </div>
                        <div class="form-group">
                            <label for="contact_number">Contact Number</label>
                            <input id="contact_number" name="contact_number" type="text" value="{{ old('contact_number', $shopSettings->contact_number ?? '') }}" placeholder="Enter contact number">
                            @error('contact_number')<div class="field-error">{{ $message }}</div>@enderror
                        </div>
                        <div class="form-group">
                            <label for="address">Shop Address</label>
                            <textarea id="address" name="address" placeholder="Enter shop address">{{ old('address', $shopSettings->address ?? '') }}</textarea>
                            @error('address')<div class="field-error">{{ $message }}</div>@enderror
                        </div>
                        <button class="btn" type="submit">Save Shop Information</button>
                    </form>
                </div>

                @include('partials.account-settings')
                <section class="settings-card" id="gcash-account" aria-labelledby="gcash-account-heading">
                    <h3 id="gcash-account-heading">GCash Payment Account</h3>
                    <p>Set the shop account customers should pay. Leave both account fields empty to disable GCash.</p>
                    @if (session('gcash_success'))<div class="setting-message">{{ session('gcash_success') }}</div>@endif
                    <form method="POST" enctype="multipart/form-data" action="{{ route('admin.settings.gcash') }}">
                        @csrf
                        <div class="form-group">
                            <label for="gcash_number">GCash Number</label>
                            <input id="gcash_number" name="gcash_number" type="tel" maxlength="20" value="{{ old('gcash_number', $shopSettings->gcash_number ?? '') }}" placeholder="09XXXXXXXXX or +639XXXXXXXXX">
                            @error('gcash_number')<div class="field-error">{{ $message }}</div>@enderror
                        </div>
                        <div class="form-group">
                            <label for="gcash_account_name">GCash Account Name</label>
                            <input id="gcash_account_name" name="gcash_account_name" maxlength="255" value="{{ old('gcash_account_name', $shopSettings->gcash_account_name ?? '') }}">
                            @error('gcash_account_name')<div class="field-error">{{ $message }}</div>@enderror
                        </div>
                        <div class="form-group">
                            <label for="gcash_qr">GCash QR Image (optional)</label>
                            <input id="gcash_qr" name="gcash_qr" type="file" accept="image/jpeg,image/png,image/webp">
                            <small class="settings-note">JPG, PNG, or WEBP up to 5 MB.</small>
                            @error('gcash_qr')<div class="field-error">{{ $message }}</div>@enderror
                        </div>
                        @if ($shopSettings->gcash_qr_path ?? null)
                            @php($gcashAccount = \App\GCashAccount::details())
                            @if ($gcashAccount['qr_url'])<img src="{{ $gcashAccount['qr_url'] }}" alt="Saved shop GCash QR code" style="display:block;width:180px;max-width:100%;margin-bottom:12px">@endif
                            <label class="settings-preference"><input type="checkbox" name="remove_gcash_qr" value="1">Remove saved QR image</label>
                        @endif
                        <button class="btn" type="submit">Save GCash Account</button>
                    </form>
                </section>
            </div>
        </div>
@endsection
