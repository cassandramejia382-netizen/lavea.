@if (request()->cookie('lavea_theme') === 'dark')
    <link rel="stylesheet" href="{{ asset('css/lavea-theme.css') }}">
@endif
