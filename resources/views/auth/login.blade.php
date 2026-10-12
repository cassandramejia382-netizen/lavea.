<!DOCTYPE html>
<html lang="en">
<head>
    @include('partials.theme-assets')
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Sign In | LAVEA</title>
    <style>
        * { box-sizing: border-box; }
        body { margin: 0; min-height: 100vh; display: grid; place-items: center; padding: 24px; background: #f4f6fa; color: #111d38; font-family: Arial, sans-serif; }
        .card { width: min(100%, 440px); overflow: hidden; border-radius: 16px; background: white; box-shadow: 0 18px 55px #111d3817; }
        header { padding: 30px; background: #111d38; color: white; }
        header strong { display: block; font-size: 28px; letter-spacing: 3px; }
        header span { display: block; margin-top: 6px; color: #c6d0e2; font-size: 13px; }
        main { padding: 32px; }
        .login-shell { position:relative;width:min(100%,440px); }
        .login-brand-header { display:flex;align-items:flex-start;gap:10px; }
        .back-home { position:relative;left:-6px;display:inline-flex;align-items:center;justify-content:center;flex:none;width:26px;height:26px;margin-top:3px;padding:5px;border:1px solid #ffffff30;border-radius:6px;background:#ffffff0a;color:#dce6fa; }
        .back-home .back-label { display:none; }
        @media(max-width:600px) { .login-brand-header { padding:24px 20px;gap:10px; } .login-brand-header strong { font-size:24px; } .login-brand-header span { font-size:11px; } }
        .back-home svg { width:14px;height:14px; }
        .back-home:hover { background:#ffffff20;color:#fff; }
        .back-home:focus-visible { outline:2px solid #4169c8;outline-offset:3px; }
        html[data-lavea-theme="dark"] .back-home { background:#273449;border-color:#64748b;color:#bfdbfe; }
        @media(prefers-reduced-motion:reduce) { .back-home { transition:none; } }
        h1 { margin: 0 0 8px; font-size: 25px; }
        p { color: #718099; font-size: 14px; }
        label { display: block; margin: 20px 0 8px; font-size: 13px; font-weight: 700; }
        input { width: 100%; height: 46px; padding: 0 13px; border: 1px solid #d9e0eb; border-radius: 8px; font: inherit; }
        input:focus { outline: 3px solid #111d3822; border-color: #111d38; }
        button { width: 100%; height: 47px; margin-top: 24px; border: 0; border-radius: 8px; background: #111d38; color: white; font-size: 14px; font-weight: 700; cursor: pointer; }
        .links { margin-top: 20px; display: flex; justify-content: space-between; gap: 12px; font-size: 13px; }
        a { color: #111d38; font-weight: 700; text-decoration: none; }
        .notice { margin: 14px 0; padding: 12px; border-radius: 8px; background: #edf0f5; color: #111d38; font-size: 13px; }
        .error { margin: 14px 0; color: #b42318; font-size: 13px; }
        .remember { display: flex; align-items: center; gap: 8px; margin-top: 16px; color: #64748b; font-size: 13px; }
        .remember input { width: 16px; height: 16px; }
    </style>
<link rel="stylesheet" href="{{ asset('css/lavea-responsive.css') }}?v={{ filemtime(public_path('css/lavea-responsive.css')) }}">
</head>
<body class="login-page">
<div class="login-shell">

<section class="card">
    <header class="login-brand-header"><a class="back-home" aria-label="Back to home" title="Back to home" href="{{ route('home') }}"><svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.8" stroke-linecap="round" stroke-linejoin="round" aria-hidden="true"><path d="m12 19-7-7 7-7M5 12h14" /></svg><span class="back-label">Back</span></a><div><strong>LAVEA</strong><span>Laundry Management System</span></div></header>
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
            <input id="password" name="password" type="password" autocomplete="current-password" required>
            <label class="remember"><input name="remember" type="checkbox" value="1"> Remember me</label>
            <button type="submit">Sign In</button>
        </form>
        <div class="links"><a href="{{ route('password.request') }}">Forgot Password?</a><span></span></div>
        <p style="text-align:center;margin:24px 0 0">Don't have an account? <a href="{{ route('register') }}">Sign Up</a></p>
    </main>
</section>
</div>
</body>
</html>
