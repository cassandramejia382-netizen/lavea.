<!DOCTYPE html>
<html lang="en">
<head>
    @include('partials.theme-assets')

    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">

    <title>Verify Email | LAVEA</title>

    <style>
        * {
            box-sizing: border-box;
        }

        body {
            margin: 0;
            min-height: 100vh;
            display: grid;
            place-items: center;
            padding: 24px;
            background: #f4f6fa;
            color: #111d38;
            font-family: Arial, sans-serif;
        }

        .card {
            width: min(100%, 500px);
            padding: 36px;
            border-radius: 16px;
            background: #ffffff;
            box-shadow: 0 18px 55px #111d3817;
            text-align: center;
        }

        strong {
            font-size: 27px;
            letter-spacing: 3px;
        }

        h1 {
            margin: 24px 0 10px;
            font-size: 24px;
        }

        p {
            color: #718099;
            font-size: 14px;
            line-height: 1.65;
        }

        .email {
            color: #111d38;
            font-weight: 700;
        }

        .code-input {
            width: 100%;
            height: 58px;
            margin: 18px 0 10px;
            padding: 0 15px;
            border: 1px solid #d6dce7;
            border-radius: 10px;
            outline: none;
            text-align: center;
            font-size: 25px;
            font-weight: 700;
            letter-spacing: 8px;
            color: #111d38;
        }

        .code-input:focus {
            border-color: #4169c8;
            box-shadow: 0 0 0 3px #4169c81a;
        }

        button {
            height: 44px;
            padding: 0 20px;
            margin: 12px 5px;
            border: 0;
            border-radius: 8px;
            background: #111d38;
            color: #ffffff;
            font-weight: 700;
            cursor: pointer;
        }

        button:hover {
            opacity: 0.92;
        }

        .verify-btn {
            width: 100%;
            margin-top: 15px;
        }

        .resend-btn {
            background: #edf1f7;
            color: #111d38;
        }

        .logout {
            background: #edf1f7;
            color: #111d38;
        }

        .msg {
            margin: 18px 0;
            padding: 12px;
            border-radius: 8px;
            background: #edf4ff;
            color: #214a8d;
            font-size: 13px;
        }

        .err {
            margin: 18px 0;
            padding: 12px;
            border-radius: 8px;
            background: #fff1f0;
            color: #b42318;
            font-size: 13px;
        }

        .error-list {
            margin: 18px 0;
            padding: 12px;
            border-radius: 8px;
            background: #fff1f0;
            color: #b42318;
            font-size: 13px;
            text-align: left;
        }

        .small-text {
            margin-top: 18px;
            font-size: 13px;
            color: #8a95a8;
        }

        @media (max-width: 500px) {
            .card {
                padding: 28px 22px;
            }

            .code-input {
                font-size: 22px;
                letter-spacing: 6px;
            }
        }
    </style>
</head>

<body>

<main class="card">

    <strong>LAVEA</strong>

    <h1>Verify your email</h1>

    <p>
        We sent a <strong style="font-size: 14px; letter-spacing: 0;">6-digit verification code</strong>
        to your email address.
    </p>

    <p class="email">
        {{ Auth::user()->email }}
    </p>

    @if(session('status'))
        <div class="msg">
            {{ session('status') }}
        </div>
    @endif

    @if(session('email_error'))
        <div class="err">
            {{ session('email_error') }}
        </div>
    @endif

    @if($errors->any())
        <div class="error-list">
            @foreach($errors->all() as $error)
                <div>{{ $error }}</div>
            @endforeach
        </div>
    @endif

    <!-- Verification Code Form -->
    <form method="POST" action="{{ route('verification.verify') }}">
        @csrf

        <input
            type="text"
            name="verification_code"
            class="code-input"
            placeholder="000000"
            maxlength="6"
            minlength="6"
            inputmode="numeric"
            pattern="[0-9]{6}"
            autocomplete="one-time-code"
            required
        >

        <button
            type="submit"
            class="verify-btn"
        >
            Verify Email
        </button>
    </form>

    <p class="small-text">
        Didn't receive the code? You can request a new one.
    </p>

    <!-- Resend Code -->
    <form method="POST" action="{{ route('verification.send') }}">
        @csrf

        <button
            type="submit"
            class="resend-btn"
        >
            Resend Verification Code
        </button>
    </form>

    <!-- Sign Out -->
    <form method="GET" action="{{ route('logout.confirm') }}">
        <button
            class="logout"
            type="submit"
        >
            Sign Out
        </button>
    </form>

</main>

<script>
    const codeInput = document.querySelector('.code-input');

    codeInput.addEventListener('input', function () {
        this.value = this.value.replace(/\D/g, '').slice(0, 6);
    });
</script>

</body>
</html>