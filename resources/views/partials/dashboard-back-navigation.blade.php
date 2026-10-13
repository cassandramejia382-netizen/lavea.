@php
    $dashboardBackRole = in_array(auth()->user()->role, ['customer', 'user'], true) ? 'customer' : auth()->user()->role;
    $dashboardBackUrl = route($dashboardBackRole.'.dashboard', [], false);
@endphp
<script>
    (() => {
        const dashboardUrl = @json($dashboardBackUrl);
        const returnToDashboard = () => window.location.replace(dashboardUrl);
        const currentPagePath = window.location.pathname;
        let currentSectionHash = window.location.hash;

        if (!history.state?.laveaDashboardBack) {
            history.pushState({ ...history.state, laveaDashboardBack: true }, '', window.location.href);
        }

        window.addEventListener('popstate', () => {
            if (window.location.pathname === currentPagePath && window.location.hash !== currentSectionHash) {
                currentSectionHash = window.location.hash;
                return;
            }

            returnToDashboard();
        });
        window.addEventListener('hashchange', () => {
            currentSectionHash = window.location.hash;
        });
        window.addEventListener('pageshow', event => {
            const navigation = performance.getEntriesByType('navigation')[0];
            if ((event.persisted || navigation?.type === 'back_forward') && window.location.pathname !== dashboardUrl) {
                returnToDashboard();
            }
        });
    })();
</script>
