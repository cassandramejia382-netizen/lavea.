<style>
#logout-dialog {
    box-sizing: border-box;
    width: min(380px,calc(100% - 32px));
    margin: auto;
    padding: 0;
    border: 1px solid #e6eaf2;
    border-radius: 18px;
    background: #fff;
    color: #172554;
    box-shadow: 0 20px 60px #111d3826;
    font: 14px Arial,Helvetica,sans-serif;
    text-align: left;
    overflow: hidden;
}
#logout-dialog::backdrop {
    background: rgb(17 29 56 / 35%);
    backdrop-filter: blur(6px);
}
#logout-dialog .logout-dialog-body {
    padding: 24px 24px 20px;
    background: #fff;
}
#logout-dialog .logout-dialog-heading {
    display: flex;
    align-items: center;
    gap: 12px;
    margin-bottom: 12px;
}
#logout-dialog .logout-dialog-icon {
    display: grid;
    place-items: center;
    flex: none;
    width: 40px;
    height: 40px;
    border: 1px solid #e8edf5;
    border-radius: 12px;
    background: #f7f9fc;
    color: #526987;
}
#logout-dialog .logout-dialog-heading h2 {
    margin: 0;
}
#logout-dialog h2 {
    margin: 0 0 8px;
    font-size: 19px;
    font-weight: 700;
    letter-spacing: -.4px;
    color: #111d38;
}
#logout-dialog p {
    margin: 0;
    color: #718099;
    font-size: 13px;
    line-height: 1.65;
}
#logout-dialog .logout-dialog-actions {
    display: flex;
    justify-content: flex-end;
    gap: 8px;
    margin-top: 22px;
    padding-top: 16px;
    border-top: 1px solid #edf0f5;
}
#logout-dialog button {
    flex: 0 0 auto;
    min-height: 40px;
    padding: 9px 18px;
    border: 1px solid #dfe5ef;
    border-radius: 8px;
    background: #fff;
    color: #34415a;
    font: inherit;
    font-size: 13px;
    font-weight: 600;
    cursor: pointer;
    transition: background 150ms ease,box-shadow 150ms ease;
}
#logout-dialog button[data-logout-confirm] {
    border-color: #253958;
    background: #253958;
    color: #fff;
}
#logout-dialog button:hover {
    background: #f5f7fc;
}
#logout-dialog button[data-logout-confirm]:hover {
    background: #344d74;
}
#logout-dialog button:focus-visible {
    outline: 2px solid #9ab5f1;
    outline-offset: 2px;
}
#logout-dialog button:disabled {
    opacity: .65;
    cursor: wait;
}
html[data-lavea-theme="dark"] #logout-dialog {
    background: #172554;
    color: #e7edf9;
    border-color: #34415a;
}
html[data-lavea-theme="dark"] #logout-dialog h2 {
    color: #e7edf9;
}
html[data-lavea-theme="dark"] #logout-dialog p {
    color: #b5c2d8;
}
html[data-lavea-theme="dark"] #logout-dialog .logout-dialog-body {
    background: #172554;
}
html[data-lavea-theme="dark"] #logout-dialog .logout-dialog-icon {
    background: #263c67;
    color: #b9ceff;
}
html[data-lavea-theme="dark"] #logout-dialog button[data-logout-cancel] {
    background: #223454;
    border-color: #34415a;
    color: #e7edf9;
}
html[data-lavea-theme="dark"] #logout-dialog .logout-dialog-actions {
    border-color: #34415a;
}
@media(prefers-reduced-motion:reduce) {
    #logout-dialog button {
        transition: none;
    }
}
</style>
<dialog id="logout-dialog" aria-labelledby="logout-dialog-title" aria-describedby="logout-dialog-description">
    <div class="logout-dialog-body">
    <div class="logout-dialog-heading">
        <span class="logout-dialog-icon" aria-hidden="true"><svg width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.8" stroke-linecap="round" stroke-linejoin="round"><path d="M9 21H5a2 2 0 0 1-2-2V5a2 2 0 0 1 2-2h4M16 17l5-5-5-5M21 12H9" /></svg></span>
        <h2 id="logout-dialog-title">Confirm logout</h2>
    </div>
    <p id="logout-dialog-description">Are you sure you want to log out?</p>
    <div class="logout-dialog-actions">
        <button type="button" data-logout-cancel autofocus>Cancel</button>
        <button type="button" data-logout-confirm>Logout</button>
    </div>
    </div>
</dialog>
<script>
    (() => {
        const dialog = document.getElementById('logout-dialog');
        let logoutForm;
        document.querySelectorAll('form[data-confirm-logout]').forEach(form => {
            form.addEventListener('submit', event => {
                event.preventDefault();
                logoutForm = form;
                dialog.showModal();
            });
        });
        dialog.querySelector('[data-logout-cancel]').addEventListener('click', () => dialog.close());
        dialog.querySelector('[data-logout-confirm]').addEventListener('click', event => {
            if (!logoutForm) return;
            event.currentTarget.disabled = true;
            event.currentTarget.textContent = 'Logging out…';
            HTMLFormElement.prototype.submit.call(logoutForm);
        });
        dialog.addEventListener('click', event => {
            const bounds = dialog.getBoundingClientRect();
            if (event.target === dialog && (event.clientX < bounds.left || event.clientX > bounds.right || event.clientY < bounds.top || event.clientY > bounds.bottom)) dialog.close();
        });
    })();
</script>
