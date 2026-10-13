<!DOCTYPE html>
<html lang="en">
<head>
    @include('partials.theme-assets')
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Verify Email | LAVEA</title>
    <link rel="stylesheet" href="{{ asset('css/lavea-auth.css') }}?v={{ filemtime(public_path('css/lavea-auth.css')) }}">
    <link rel="stylesheet" href="{{ asset('css/lavea-responsive.css') }}?v={{ filemtime(public_path('css/lavea-responsive.css')) }}">
</head>
<body class="login-page verification-page">
<div class="login-shell">
    <section class="card" aria-labelledby="verification-title">
        <header class="login-brand-header">
            <div><strong>LAVEA</strong><span>Laundry Management System</span></div>
        </header>
        <main>
            <div class="verification-intro">
                <div class="verification-icon" aria-hidden="true">
                    <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.7" stroke-linecap="round" stroke-linejoin="round"><rect x="3" y="5" width="18" height="14" rx="3"/><path d="m3 7 9 6 9-6"/></svg>
                </div>
                <h1 id="verification-title">Verify your email</h1>
                <p>Enter the 6-digit verification code sent to</p>
                <div class="verification-email">{{ Auth::user()->email }}</div>
            </div>
            @if(session('status'))
                <div class="notice" role="status">{{ session('status') }}</div>
            @endif
            @if(session('email_error'))
                <div class="verification-alert" role="alert">{{ session('email_error') }}</div>
            @endif
            @if($errors->any())
                <div id="verification-errors" class="verification-alert" role="alert">
                    @foreach($errors->all() as $error)
                        <div>{{ $error }}</div>
                    @endforeach
                </div>
            @endif
            <form method="POST" action="{{ route('verification.verify') }}">
                @csrf
                <label for="verification_code">Verification code</label>
                <input
                    id="verification_code"
                    type="text"
                    name="verification_code"
                    class="code-input"
                    placeholder="000000"
                    maxlength="6"
                    minlength="6"
                    inputmode="numeric"
                    pattern="[0-9]{6}"
                    autocomplete="one-time-code"
                    aria-describedby="verification-help{{ $errors->any() ? ' verification-errors' : '' }}"
                    @if($errors->has('verification_code')) aria-invalid="true" @endif
                    required
                >
                <p id="verification-help" class="verification-help">Check your inbox or spam folder for your code.</p>
                <button type="submit" class="sign-in-button verify-btn">Verify Email</button>
            </form>
            <div class="verification-footer">
                <p>Didn't receive the code?</p>
                <form method="POST" action="{{ route('verification.send') }}">
                    @csrf
                    <button type="submit" class="resend-btn">Resend Verification Code</button>
                </form>
                <form method="POST" action="{{ route('logout') }}" data-confirm-logout>
                    @csrf
                    <button class="logout" type="submit">Sign Out</button>
                </form>
            </div>
        </main>
    </section>
</div>
<script>
    const codeInput = document.querySelector('.code-input');
    codeInput.addEventListener('input', function () {
        this.value = this.value.replace(/\D/g, '').slice(0, 6);
    });
</script>
@include('partials.logout-dialog')
</body>
</html>
