(() => {
    document.querySelectorAll('[data-password-target]').forEach(toggle => {
        const password = document.getElementById(toggle.dataset.passwordTarget);
        toggle.addEventListener('click', () => {
            const showPassword = password.type === 'password';
            password.type = showPassword ? 'text' : 'password';
            toggle.setAttribute('aria-label', showPassword ? 'Hide password' : 'Show password');
            toggle.setAttribute('aria-pressed', String(showPassword));
            toggle.querySelector('[data-password-show]').hidden = showPassword;
            toggle.querySelector('[data-password-hide]').hidden = !showPassword;
        });
    });
})();
