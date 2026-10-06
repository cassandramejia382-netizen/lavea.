<!DOCTYPE html>
<html lang="en" data-lavea-theme="{{ request()->cookie('lavea_theme', 'light') }}">
<head>
    @include('partials.theme-assets')
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>LAVEA | {{ $title ?? 'Admin' }}</title>
    <script src="https://unpkg.com/lucide@latest"></script>
    @stack('styles')
    <link rel="stylesheet" href="{{ asset('css/admin-layout.css') }}?v={{ filemtime(public_path('css/admin-layout.css')) }}">
    <link rel="stylesheet" href="{{ asset('css/admin-topbar.css') }}?v={{ filemtime(public_path('css/admin-topbar.css')) }}">
</head>
<body>
    <div class="dashboard">
        @include('admin.partials.sidebar')
        <main class="main">
            @include('admin.partials.topbar')
            <section class="content lavea-admin-content">
                @yield('content')
            </section>
        </main>
    </div>
    <script>
        if (typeof lucide !== 'undefined') {
            lucide.createIcons();
        }
    </script>
    @stack('scripts')
</body>
</html>
