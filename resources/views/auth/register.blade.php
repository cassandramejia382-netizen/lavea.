<!DOCTYPE html>
<html lang="en">
<head>
    @include('partials.theme-assets')
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Sign Up | LAVEA</title>
    <style>
        * { box-sizing: border-box; }
        body { margin: 0; min-height: 100vh; display: grid; place-items: center; padding: 24px; background: #f4f6fa; color: #111d38; font-family: Arial, sans-serif; }
        .card { width: min(100%, 520px); overflow: hidden; border-radius: 16px; background: white; box-shadow: 0 18px 55px #111d3817; }
        header { padding: 25px 30px; background: #111d38; color: white; }
        header strong { display: block; font-size: 27px; letter-spacing: 3px; }
        header span { display: block; margin-top: 6px; color: #c6d0e2; font-size: 13px; }
        main { padding: 28px 32px; }
        h1 { margin: 0 0 8px; font-size: 24px; }
        p { color: #718099; font-size: 14px; }
        label { display: block; margin: 15px 0 7px; font-size: 13px; font-weight: 700; }
        input, textarea { width: 100%; padding: 12px; border: 1px solid #d9e0eb; border-radius: 8px; font: inherit; }
        input { height: 44px; }
        textarea { min-height: 76px; resize: vertical; }
        button { width: 100%; height: 46px; margin-top: 22px; border: 0; border-radius: 8px; background: #111d38; color: white; font-weight: 700; cursor: pointer; }
        a { color: #3459b1; font-weight: 700; text-decoration: none; }
        .error { margin: 5px 0 0; color: #b42318; font-size: 12px; }
        .notice { margin: 14px 0; padding: 12px; border-radius: 8px; background: #fff1f0; color: #b42318; font-size: 13px; }
        .login { margin-top: 20px; text-align: center; font-size: 13px; }
        @media (min-width: 520px) { .grid { display: grid; grid-template-columns: 1fr 1fr; gap: 0 16px; } }
    </style>
</head>
<body>
<section class="card">
    <header><strong>LAVEA</strong><span>Laundry Management System</span></header>
    <main>
        <h1>Create Customer Account</h1>
        <p>Sign up to get started. We’ll email you a link to verify your address.</p>
        @if ($errors->any()) <div class="notice" role="alert">Please review the highlighted fields.</div> @endif
        <form method="POST" action="{{ route('register.submit') }}">
            @csrf
            <div class="grid">
                <div><label for="name">Full Name</label><input id="name" name="name" value="{{ old('name') }}" autocomplete="name" required>@error('name')<div class="error">{{ $message }}</div>@enderror</div>
                <div><label for="email">Email</label><input id="email" name="email" type="email" value="{{ old('email') }}" autocomplete="email" required>@error('email')<div class="error">{{ $message }}</div>@enderror</div>
                <div><label for="phone">Phone Number</label><input id="phone" name="phone" type="tel" value="{{ old('phone') }}" autocomplete="tel" required>@error('phone')<div class="error">{{ $message }}</div>@enderror</div>
                <div><label for="password">Password</label><input id="password" name="password" type="password" autocomplete="new-password" required>@error('password')<div class="error">{{ $message }}</div>@enderror</div>
            </div>
            <label for="address">Address</label><textarea id="address" name="address" autocomplete="street-address" required>{{ old('address') }}</textarea>@error('address')<div class="error">{{ $message }}</div>@enderror
            <label for="password_confirmation">Confirm Password</label><input id="password_confirmation" name="password_confirmation" type="password" autocomplete="new-password" required>
            <button type="submit">Create Account</button>
        </form>
        <div class="login">Already have an account? <a href="{{ route('login') }}">Sign In</a></div>
    </main>
</section>
</body>
</html>
