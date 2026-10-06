<section class="settings-card" id="{{ $settingsRole === 'admin' ? 'admin-profile' : 'profile-information' }}" aria-labelledby="profile-heading">
    <h3 id="profile-heading">Profile Information</h3>
    <p>Update the name and email used for your {{ ucfirst($settingsRole) }} account.</p>

    <form method="POST" action="{{ route($settingsRole.'.settings.profile') }}">
        @csrf
        <div class="form-group">
            <label for="account_name">Name</label>
            <input id="account_name" name="name" type="text" value="{{ old('name', auth()->user()->name) }}" maxlength="255" autocomplete="name" required>
            @error('name')<div class="field-error">{{ $message }}</div>@enderror
        </div>
        <div class="form-group">
            <label for="account_email">Email</label>
            <input id="account_email" name="email" type="email" value="{{ old('email', auth()->user()->email) }}" maxlength="255" autocomplete="email" required>
            @error('email')<div class="field-error">{{ $message }}</div>@enderror
            @if ($settingsRole === 'customer')
                <small class="settings-note">Changing your email requires verification of your new address.</small>
            @endif
        </div>
        @if ($customerProfile)
            <div class="form-group">
                <label for="account_phone">Phone Number</label>
                <input id="account_phone" name="phone" type="tel" value="{{ old('phone', $customerProfile->phone) }}" maxlength="20" autocomplete="tel" required>
                @error('phone')<div class="field-error">{{ $message }}</div>@enderror
            </div>
            <div class="form-group">
                <label for="account_address">Address</label>
                <textarea id="account_address" name="address" maxlength="255" autocomplete="street-address" required>{{ old('address', $customerProfile->address) }}</textarea>
                @error('address')<div class="field-error">{{ $message }}</div>@enderror
            </div>
        @elseif ($settingsRole === 'customer')
            <p class="settings-note">Contact the shop to link your account to your customer contact details.</p>
        @endif
        <button class="btn" type="submit">Update Profile</button>
    </form>
</section>

<section class="settings-card" id="change-password" aria-labelledby="security-heading">
    <h3 id="security-heading">Security</h3>
    <p>Change your password using your current password.</p>
    <form method="POST" action="{{ route($settingsRole.'.settings.password') }}">
        @csrf
        <div class="form-group">
            <label for="current_password">Current Password</label>
            <input id="current_password" name="current_password" type="password" autocomplete="current-password" required>
            @error('current_password')<div class="field-error">{{ $message }}</div>@enderror
        </div>
        <div class="form-group">
            <label for="new_password">New Password</label>
            <input id="new_password" name="password" type="password" minlength="8" autocomplete="new-password" required>
            @error('password')<div class="field-error">{{ $message }}</div>@enderror
        </div>
        <div class="form-group">
            <label for="password_confirmation">Confirm New Password</label>
            <input id="password_confirmation" name="password_confirmation" type="password" minlength="8" autocomplete="new-password" required>
        </div>
        <button class="btn" type="submit">Change Password</button>
    </form>
</section>

<section class="settings-card" id="appearance" aria-labelledby="appearance-heading">
    <h3 id="appearance-heading">Appearance</h3>
    <p>Choose Light Mode or Dark Mode for LAVEA on this browser.</p>
    <div class="theme-options" role="group" aria-label="Appearance theme">
        @foreach (['light' => ['sun', 'Light Mode'], 'dark' => ['moon', 'Dark Mode']] as $theme => [$icon, $label])
            <form class="theme-form" method="POST" action="{{ route($settingsRole.'.settings.appearance') }}">
                @csrf
                <input type="hidden" name="theme" value="{{ $theme }}">
                <button class="theme-button {{ request()->cookie('lavea_theme', 'light') === $theme ? 'active' : '' }}" type="submit" aria-pressed="{{ request()->cookie('lavea_theme', 'light') === $theme ? 'true' : 'false' }}">
                    <i data-lucide="{{ $icon }}"></i> {{ $label }}
                </button>
            </form>
        @endforeach
    </div>
    <p class="theme-status">{{ request()->cookie('lavea_theme', 'light') === 'dark' ? 'Dark Mode is selected.' : 'Light Mode is selected.' }}</p>
</section>

<section class="settings-card" id="notifications" aria-labelledby="notifications-heading">
    <h3 id="notifications-heading">Notifications</h3>
    <p>Choose how unread notifications appear in your topbar.</p>
    <form method="POST" action="{{ route($settingsRole.'.settings.notifications') }}">
        @csrf
        <input type="hidden" name="show_notification_badge" value="0">
        <label class="settings-preference" for="show_notification_badge">
            <input id="show_notification_badge" name="show_notification_badge" type="checkbox" value="1" @checked(old('show_notification_badge', auth()->user()->show_notification_badge ?? true)) aria-describedby="notification-preference-note">
            Show unread notification count
        </label>
        @error('show_notification_badge')<div class="field-error">{{ $message }}</div>@enderror
        <small id="notification-preference-note" class="settings-note">Your notification inbox remains available from the bell icon.</small>
        <button class="btn" type="submit">Save Notification Preferences</button>
    </form>
</section>
