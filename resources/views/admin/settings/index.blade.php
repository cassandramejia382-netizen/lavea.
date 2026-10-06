@extends('admin.layout', ['title' => 'Settings - LAVEA'])

@push('styles')
<link rel="stylesheet" href="{{ asset('css/lavea-settings.css') }}">
<style>
.lavea-admin-content * { margin: 0; padding: 0; }
.lavea-admin-content .page-header {
            display: flex;
            justify-content: space-between;
            align-items: center;
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
                    <a href="#admin-profile"><i data-lucide="user-round"></i>Profile Information</a>
                    <a href="#change-password"><i data-lucide="lock-keyhole"></i>Security</a>
                    <a href="#appearance"><i data-lucide="palette"></i>Appearance</a>
                    <a href="#notifications"><i data-lucide="bell"></i>Notifications</a>
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
            </div>
        </div>
@endsection
