(() => {
    const sidebar = document.querySelector('.dashboard .sidebar');
    const toggle = document.querySelector('.lavea-sidebar-toggle');
    if (!sidebar || !toggle) return;

    sidebar.id = 'lavea-sidebar';
    const mobile = window.matchMedia('(max-width: 900px)');
    const backdrop = document.createElement('div');
    backdrop.className = 'lavea-sidebar-backdrop';
    backdrop.setAttribute('aria-hidden', 'true');
    document.body.append(backdrop);
    let desktopOpen = true;
    let mobileOpen = false;
    try {
        desktopOpen = sessionStorage.getItem('lavea-sidebar-collapsed') !== 'true';
    } catch {}

    const render = () => {
        const open = mobile.matches ? mobileOpen : desktopOpen;
        document.body.classList.toggle('lavea-sidebar-collapsed', !desktopOpen);
        document.body.classList.toggle('lavea-sidebar-open', mobile.matches && mobileOpen);
        toggle.setAttribute('aria-expanded', String(open));
        toggle.setAttribute('aria-label', mobile.matches ? (open ? 'Close navigation' : 'Open navigation') : (open ? 'Collapse navigation' : 'Expand navigation'));
        sidebar.inert = mobile.matches && !mobileOpen;
        sidebar.querySelectorAll('.menu a').forEach(link => {
            if (!mobile.matches && !desktopOpen) {
                link.title = link.querySelector('span')?.textContent.trim() || link.getAttribute('aria-label') || link.textContent.trim();
            } else {
                link.removeAttribute('title');
            }
        });
    };
    const closeMobile = (restoreFocus = true) => {
        mobileOpen = false;
        render();
        if (restoreFocus) toggle.focus();
    };
    toggle.addEventListener('click', () => {
        if (mobile.matches) {
            mobileOpen = !mobileOpen;
        } else {
            desktopOpen = !desktopOpen;
            try {
                sessionStorage.setItem('lavea-sidebar-collapsed', String(!desktopOpen));
            } catch {}
        }
        render();
        if (mobile.matches && mobileOpen) sidebar.querySelector('a')?.focus();
    });
    backdrop.addEventListener('click', () => closeMobile());
    document.addEventListener('click', event => {
        if (mobile.matches && mobileOpen && !sidebar.contains(event.target) && !toggle.contains(event.target)) {
            closeMobile(false);
        }
    });
    sidebar.addEventListener('click', event => {
        if (mobile.matches && event.target.closest('a')) closeMobile(false);
    });
    document.addEventListener('keydown', event => {
        if (!mobile.matches || !mobileOpen) return;
        if (event.key === 'Escape') {
            event.preventDefault();
            closeMobile();
        }
        if (event.key === 'Tab') {
            const controls = [toggle, ...sidebar.querySelectorAll('a[href], button:not(:disabled)')];
            const current = controls.indexOf(document.activeElement);
            event.preventDefault();
            controls[(current + (event.shiftKey ? -1 : 1) + controls.length) % controls.length].focus();
        }
    });
    mobile.addEventListener('change', () => {
        const focusWasInSidebar = sidebar.contains(document.activeElement);
        mobileOpen = false;
        render();
        if (focusWasInSidebar && sidebar.inert) toggle.focus();
    });
    render();
})();
