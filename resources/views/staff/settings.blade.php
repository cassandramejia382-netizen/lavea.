@extends('staff.layout', ['portal' => $settingsRole, 'title' => 'Settings'])

@push('styles')
    <link rel="stylesheet" href="{{ asset('css/lavea-settings.css') }}">
@endpush

@section('content')
    <div class="page-head settings-page-head">
        <div><h1>Settings</h1><div class="muted">Manage your personal account and preferences.</div></div>
    </div>

    @foreach (['profile_success', 'password_success', 'theme_success', 'notification_success'] as $message)
        @if (session($message))
            <div class="setting-message" role="status">{{ session($message) }}</div>
        @endif
    @endforeach

    <div class="settings-layout">
        <nav class="settings-nav" aria-label="Settings sections">
            <h3>Settings Menu</h3>
            <div class="settings-nav-links">
                <a href="#profile-information"><i data-lucide="user-round"></i>Profile Information</a>
                <a href="#change-password"><i data-lucide="lock-keyhole"></i>Security</a>
                <a href="#appearance"><i data-lucide="palette"></i>Appearance</a>
                <a href="#notifications"><i data-lucide="bell"></i>Notifications</a>
            </div>
        </nav>
        <div class="settings-content">
            @include('partials.account-settings')
        </div>
    </div>
@endsection
