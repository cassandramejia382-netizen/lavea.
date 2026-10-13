(() => {
    const configuration = document.getElementById('staff-assignment-config');
    if (!configuration) return;
    const config = JSON.parse(configuration.textContent);
    const service = document.getElementById('service_id');
    const staff = document.getElementById('assigned_staff');
    const message = document.getElementById('assignment-message');
    const configure = document.getElementById('assignment-config-link');
    const refresh = document.getElementById('refresh-assignment');
    const submit = staff.form.querySelector('button:not([type]), button[type="submit"]');
    let available = false;
    let controller;
    if (config.locked) {
        service.disabled = true;
        message.textContent = 'Existing staff assignment is preserved because this order has started or closed.';
        return;
    }
    const loadAssignment = async () => {
        controller?.abort();
        controller = new AbortController();
        const pendingController = controller;
        available = false;
        submit.disabled = true;
        staff.value = '';
        configure.hidden = true;
        const option = service.selectedOptions[0];
        if (!service.value || !option?.dataset.staffUrl) {
            message.textContent = 'Choose a service to assign staff automatically.';
            return;
        }
        configure.href = option.dataset.configUrl;
        message.textContent = 'Checking staff availability and workload...';
        try {
            const response = await fetch(option.dataset.staffUrl, { signal: controller.signal, credentials: 'same-origin', headers: { Accept: 'application/json' }, cache: 'no-store' });
            if (!response.ok) throw new Error('Staff availability could not be loaded.');
            const result = await response.json();
            if (pendingController !== controller) return;
            const current = String(service.value) === String(config.originalServiceId)
                ? result.staff.find(worker => String(worker.id) === String(config.originalStaffId))
                : null;
            const assigned = current || result.staff[0];
            if (!result.position) {
                message.textContent = 'Configure the required job position before automatic assignment.';
                configure.hidden = false;
            } else if (assigned) {
                staff.value = assigned.name;
                available = true;
                message.textContent = 'Automatically assigned to ' + assigned.name + '. Availability is rechecked when saving.';
            } else {
                message.textContent = 'No active ' + result.position + ' is available. Update staff availability and refresh the assignment.';
            }
            submit.disabled = !available;
        } catch (error) {
            if (error.name === 'AbortError') return;
            if (pendingController !== controller) return;
            message.textContent = 'Unable to check staff availability. Refresh the assignment and try again.';
        }
    };
    service.addEventListener('change', loadAssignment);
    refresh?.addEventListener('click', loadAssignment);
    staff.form.addEventListener('submit', event => {
        if (!available) event.preventDefault();
    });
    loadAssignment();
})();
