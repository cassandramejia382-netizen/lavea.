<!DOCTYPE html>
<html lang="en">
<head>
    @include('partials.theme-assets')
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Confirm Sign Out | LAVEA</title>
    <style>
        * { box-sizing: border-box; }
        body { margin: 0; min-height: 100vh; display: grid; place-items: center; padding: 24px; background: #f4f6fa; color: #111d38; font-family: Arial, sans-serif; }
        .card { width: min(100%, 440px); overflow: hidden; border-radius: 16px; background: white; box-shadow: 0 18px 55px #111d3817; }
        header { padding: 30px; background: #111d38; color: white; }
        header strong { display: block; font-size: 28px; letter-spacing: 3px; }
        header span { display: block; margin-top: 6px; color: #c6d0e2; font-size: 13px; }
        main { padding: 32px; }
        h1 { margin: 0 0 8px; font-size: 24px; }
        p { color: #718099; font-size: 14px; line-height: 1.5; }
        .actions { display: flex; gap: 12px; margin-top: 24px; }
        .actions form { flex: 1; }
        button, .cancel { flex: 1; min-height: 46px; display: flex; align-items: center; justify-content: center; border: 0; border-radius: 8px; font: inherit; font-size: 14px; font-weight: 700; text-decoration: none; cursor: pointer; }
        button { width: 100%; background: #111d38; color: white; }
        .cancel { background: #edf0f5; color: #111d38; }
    </style>
</head>
<body>
    <section class="card">
        <header><strong>LAVEA</strong><span>Laundry Management System</span></header>
        <main>
            <h1>Sign out?</h1>
            <p>Are you sure you want to sign out of your LAVEA account?</p>
            <div class="actions">
                @if (auth()->user()->role === 'admin')
                    <a class="cancel" href="{{ route('admin.dashboard') }}">Cancel</a>
                @elseif (auth()->user()->role === 'staff')
                    <a class="cancel" href="{{ route('staff.dashboard') }}">Cancel</a>
                @elseif (auth()->user()->role === 'customer' || auth()->user()->role === 'user')
                    <a class="cancel" href="{{ route('customer.dashboard') }}">Cancel</a>
                @else
                    <a class="cancel" href="{{ route('home') }}">Cancel</a>
                @endif

                <form method="POST" action="{{ route('logout') }}">
                    @csrf
                    <button type="submit">Sign Out</button>
                </form>
            </div>
        </main>
    </section>
</body>
</html>
