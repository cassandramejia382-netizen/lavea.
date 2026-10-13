<!DOCTYPE html>
<html lang="en">
<head>
    @include('partials.theme-assets')
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Sign Up | LAVEA</title>
<link rel="stylesheet" href="{{ asset('css/lavea-auth.css') }}?v={{ filemtime(public_path('css/lavea-auth.css')) }}">
<link rel="stylesheet" href="{{ asset('css/lavea-responsive.css') }}?v={{ filemtime(public_path('css/lavea-responsive.css')) }}">
</head>
<body class="login-page register-page">
<section class="card">
    <header class="login-brand-header">
        <div><strong>LAVEA</strong><span>Laundry Management System</span></div>
        <a aria-label="Back to home" title="Back to home" href="{{ route('home') }}" class="login-back"><svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.8" stroke-linecap="round" stroke-linejoin="round" aria-hidden="true"><path d="m12 19-7-7 7-7M5 12h14" /></svg>Back</a>
    </header>
    <main>
        <h1>Sign Up</h1>
        <p>Create your account to manage your laundry orders.</p>
        @if ($errors->any()) <div class="notice" role="alert">Please review the highlighted fields.</div> @endif
        <form method="POST" action="{{ route('register.submit') }}">
            @csrf
            <div class="grid">
                <div class="field field-full">
                    <label for="name">Full Name</label>
                    <input id="name" name="name" value="{{ old('name') }}" autocomplete="name" maxlength="255" required>
                    @error('name')<div class="error">{{ $message }}</div>@enderror
                </div>
                <div class="field">
                    <label for="email">Email</label>
                    <input id="email" name="email" type="email" value="{{ old('email') }}" autocomplete="email" maxlength="255" required>
                    @error('email')<div class="error">{{ $message }}</div>@enderror
                </div>
                <div class="field">
                    <label for="phone">Phone Number</label>
                    <input id="phone" name="phone" type="tel" value="{{ old('phone') }}" autocomplete="tel" maxlength="20" required>
                    @error('phone')<div class="error">{{ $message }}</div>@enderror
                </div>
                <div class="field field-full">
                    <label for="address">Address</label>
                    <textarea id="address" name="address" autocomplete="street-address" maxlength="255" required>{{ old('address') }}</textarea>
                    @error('address')<div class="error">{{ $message }}</div>@enderror
                </div>
                <div class="field">
                    <label for="password">Password</label>
                    <div class="password-field">
                        <input id="password" name="password" type="password" autocomplete="new-password" minlength="8" required>
                        <button class="password-toggle" type="button" aria-label="Show password" aria-controls="password" data-password-target="password" aria-pressed="false">
                            <svg data-password-show viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.8" stroke-linecap="round" stroke-linejoin="round" aria-hidden="true"><path d="M2 12s3.5-7 10-7 10 7 10 7-3.5 7-10 7S2 12 2 12Z"/><circle cx="12" cy="12" r="3"/></svg>
                            <svg data-password-hide viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.8" stroke-linecap="round" stroke-linejoin="round" aria-hidden="true" hidden><path d="m3 3 18 18M10.6 5.1 12 5c6.5 0 10 7 10 7a19 19 0 0 1-3 3.8M6.5 6.5A21 21 0 0 0 2 12s3.5 7 10 7a12 12 0 0 0 5.5-1.5M9.9 9.9a3 3 0 0 0 4.2 4.2"/></svg>
                        </button>
                    </div>
                    @error('password')<div class="error">{{ $message }}</div>@enderror
                </div>
                <div class="field">
                    <label for="password_confirmation">Confirm Password</label>
                    <div class="password-field">
                        <input id="password_confirmation" name="password_confirmation" type="password" autocomplete="new-password" minlength="8" required>
                        <button class="password-toggle" type="button" aria-label="Show password" aria-controls="password_confirmation" data-password-target="password_confirmation" aria-pressed="false">
                            <svg data-password-show viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.8" stroke-linecap="round" stroke-linejoin="round" aria-hidden="true"><path d="M2 12s3.5-7 10-7 10 7 10 7-3.5 7-10 7S2 12 2 12Z"/><circle cx="12" cy="12" r="3"/></svg>
                            <svg data-password-hide viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.8" stroke-linecap="round" stroke-linejoin="round" aria-hidden="true" hidden><path d="m3 3 18 18M10.6 5.1 12 5c6.5 0 10 7 10 7a19 19 0 0 1-3 3.8M6.5 6.5A21 21 0 0 0 2 12s3.5 7 10 7a12 12 0 0 0 5.5-1.5M9.9 9.9a3 3 0 0 0 4.2 4.2"/></svg>
                        </button>
                    </div>
                </div>
            </div>
            <button class="sign-in-button" type="submit">Create Account</button>
        </form>
        <div class="signup-link">Already have an account? <a href="{{ route('login') }}">Sign In</a></div>
    </main>
</section>
<script>
    const registrationForm = document.querySelector('form[action="{{ route('register.submit') }}"]');
    const registrationButton = registrationForm.querySelector('button[type="submit"]');
    registrationForm.addEventListener('submit', () => {
        registrationButton.disabled = true;
        registrationButton.setAttribute('aria-busy', 'true');
    });
    window.addEventListener('pageshow', () => {
        registrationButton.disabled = false;
        registrationButton.removeAttribute('aria-busy');
    });
    document.querySelectorAll('[data-password-target]').forEach(toggle => {
        const password = document.getElementById(toggle.dataset.passwordTarget);
        toggle.addEventListener('click', () => {
            const showPassword = password.type === 'password';
            password.type = showPassword ? 'text' : 'password';
            toggle.setAttribute('aria-label', showPassword ? 'Hide password' : 'Show password');
            toggle.setAttribute('aria-pressed', String(showPassword));
            toggle.querySelector('[data-password-show]').hidden = showPassword;
            toggle.querySelector('[data-password-hide]').hidden = !showPassword;
        });
    });
</script>
</body>
</html>
