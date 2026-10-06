@if (request()->cookie('lavea_theme') === 'dark')
    <link rel="stylesheet" href="{{ asset('css/lavea-theme.css') }}">
@endif
<script>
    document.documentElement.dataset.laveaTheme = @json(request()->cookie('lavea_theme') === 'dark' ? 'dark' : 'light');
    document.documentElement.style.colorScheme = document.documentElement.dataset.laveaTheme;
</script>
