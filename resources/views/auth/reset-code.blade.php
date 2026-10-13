<!DOCTYPE html>
<html lang="en"><head>
    @include('partials.theme-assets')<meta charset="UTF-8"><meta name="viewport" content="width=device-width, initial-scale=1.0"><title>Forgot Password | LAVEA</title><style>
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
    font-family: Arial,sans-serif;
}
.card {
    width: min(100%,440px);
    padding: 32px;
    border-radius: 16px;
    background: #fff;
    box-shadow: 0 18px 55px #111d3817;
}
strong {
    font-size: 26px;
    letter-spacing: 3px;
}
h1 {
    margin: 22px 0 8px;
    font-size: 24px;
}
p {
    color: #718099;
    font-size: 14px;
    line-height: 1.6;
}
label {
    display: block;
    margin: 20px 0 8px;
    font-size: 13px;
    font-weight: 700;
}
input {
    width: 100%;
    height: 46px;
    padding: 0 13px;
    border: 1px solid #d9e0eb;
    border-radius: 8px;
    font: inherit;
}
button {
    width: 100%;
    height: 46px;
    margin-top: 20px;
    border: 0;
    border-radius: 8px;
    background: #111d38;
    color: #fff;
    font-weight: 700;
}
a {
    color: #3459b1;
    font-weight: 700;
    text-decoration: none;
}
.msg {
    margin: 16px 0;
    padding: 12px;
    border-radius: 8px;
    background: #edf4ff;
    color: #214a8d;
    font-size: 13px;
}
.err {
    color: #b42318;
    font-size: 13px;
}
</style><link rel="stylesheet" href="{{ asset('css/lavea-responsive.css') }}?v={{ filemtime(public_path('css/lavea-responsive.css')) }}">
</head>
<body class="auth-page"><main class="card"><strong>LAVEA</strong><h1>Check your email</h1><p>Enter the 6-digit code. It expires in 10 minutes.</p>
@if(session('status'))<div class="msg">{{ session('status') }}</div>@endif
@if($errors->any())<div class="err">{{ $errors->first() }}</div>@endif
<form method="POST" action="{{ route('password.code.verify') }}">@csrf<label for="code">Reset code</label><input id="code" name="code" type="text" inputmode="numeric" autocomplete="one-time-code" pattern="[0-9]{6}" maxlength="6" required autofocus style="text-align:center;letter-spacing:8px;font-size:22px"><button type="submit">Verify Code</button></form><p><a href="{{ route('password.request') }}">Request a new code</a></p>
<p><a href="{{ route('login') }}">Back to Sign In</a></p></main></body></html>
