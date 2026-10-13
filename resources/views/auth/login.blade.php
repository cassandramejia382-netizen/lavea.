<!DOCTYPE html>
<html lang="en">
<head>
    @include('partials.theme-assets')
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Sign In | LAVEA</title>
<link rel="stylesheet" href="{{ asset('css/lavea-auth.css') }}?v={{ filemtime(public_path('css/lavea-auth.css')) }}">
<link rel="stylesheet" href="{{ asset('css/lavea-responsive.css') }}?v={{ filemtime(public_path('css/lavea-responsive.css')) }}">
</head>
<body class="login-page">
<div class="login-shell">

<section class="card">
    <header class="login-brand-header">
        <div><strong>LAVEA</strong><span>Laundry Management System</span></div>
        <a aria-label="Back to home" title="Back to home" href="{{ route('home') }}" class="login-back"><svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.8" stroke-linecap="round" stroke-linejoin="round" aria-hidden="true"><path d="m12 19-7-7 7-7M5 12h14" /></svg>Back</a>
    </header>
    <main>

        <h1>Sign In</h1>
        <p>Enter your email and password to continue.</p>

        @if (session('status')) <div class="notice">{{ session('status') }}</div> @endif
        @if ($errors->any()) <div class="error" role="alert">{{ $errors->first() }}</div> @endif

        <form action="{{ route('login.submit') }}" method="POST">
            @csrf
            <label for="email">Email</label>
            <input id="email" name="email" type="email" value="{{ old('email') }}" autocomplete="email" required autofocus>
            <label for="password">Password</label>
            <div class="password-field">
                <input id="password" name="password" type="password" autocomplete="current-password" required>
                <button class="password-toggle" type="button" aria-label="Show password" aria-controls="password" aria-pressed="false">
                    <svg data-password-show viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.8" stroke-linecap="round" stroke-linejoin="round" aria-hidden="true"><path d="M2 12s3.5-7 10-7 10 7 10 7-3.5 7-10 7S2 12 2 12Z"/><circle cx="12" cy="12" r="3"/></svg>
                    <svg data-password-hide viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.8" stroke-linecap="round" stroke-linejoin="round" aria-hidden="true" hidden><path d="m3 3 18 18M10.6 5.1 12 5c6.5 0 10 7 10 7a19 19 0 0 1-3 3.8M6.5 6.5A21 21 0 0 0 2 12s3.5 7 10 7a12 12 0 0 0 5.5-1.5M9.9 9.9a3 3 0 0 0 4.2 4.2"/></svg>
                </button>
            </div>
            <div class="login-options">
                <label class="remember"><input name="remember" type="checkbox" value="1" @checked(old('remember'))> Remember me</label>
                <a href="{{ route('password.request') }}">Forgot Password?</a>
            </div>
            <button class="sign-in-button" type="submit">Sign In</button>
        </form>
        <p class="signup-link">Don't have an account? <a href="{{ route('register') }}">Sign Up</a></p>
    </main>
</section>
</div>
<script>
    (() => {
        const password = document.getElementById('password');
        const toggle = document.querySelector('.password-toggle');
        toggle.addEventListener('click', () => {
            const showPassword = password.type === 'password';
            password.type = showPassword ? 'text' : 'password';
            toggle.setAttribute('aria-label', showPassword ? 'Hide password' : 'Show password');
            toggle.setAttribute('aria-pressed', String(showPassword));
            toggle.querySelector('[data-password-show]').hidden = showPassword;
            toggle.querySelector('[data-password-hide]').hidden = !showPassword;
        });
    })();
</script>
</body>
</html>
