@php
    $notifications = auth()->user()->notifications()->latest()->take(8)->get();
    $unreadCount = auth()->user()->unreadNotifications()->count();
@endphp

<div class="lavea-notification" data-unread-count="{{ $unreadCount }}">
    <button type="button" class="lavea-notification-button" id="notification-toggle" aria-label="Notifications" aria-expanded="false" aria-controls="notification-panel">
        <i data-lucide="bell"></i>
        @if ($unreadCount > 0 && (auth()->user()->show_notification_badge ?? true))
            <span class="lavea-notification-count">{{ $unreadCount > 99 ? '99+' : $unreadCount }}</span>
        @endif
    </button>

    <div class="lavea-notification-dropdown" id="notification-panel" hidden>
        <div class="lavea-notification-heading">Notifications</div>

        @forelse ($notifications as $notification)
            @php
                $notificationUrl = $notification->data['url'] ?? null;
                $notificationPath = $notificationUrl ? parse_url($notificationUrl, PHP_URL_PATH) : null;
                if (auth()->user()->role === 'staff' && is_string($notificationPath) && preg_match('#^/admin/(orders|payments)/(\d+)$#', $notificationPath, $matches)) {
                    $notificationUrl = route('staff.'.$matches[1].'.show', $matches[2]);
                }
            @endphp
            <div class="lavea-notification-item {{ $notification->read_at ? '' : 'is-unread' }}" @if (! $notification->read_at) data-read-url="{{ route('notifications.read', $notification->id) }}" @endif>
                @if ($notificationUrl)
                    <a class="lavea-notification-title" href="{{ $notificationUrl }}">
                        {{ $notification->data['title'] ?? 'Notification' }}
                    </a>
                @else
                    <strong class="lavea-notification-title">{{ $notification->data['title'] ?? 'Notification' }}</strong>
                @endif

                <p>{{ $notification->data['message'] ?? '' }}</p>
                <small>{{ $notification->created_at->diffForHumans() }}</small>

            </div>
        @empty
            <p class="lavea-notification-empty">No notifications yet.</p>
        @endforelse
    </div>
</div>

<script>
    (function () {
        var toggle = document.getElementById('notification-toggle');
        var panel = document.getElementById('notification-panel');

        if (!toggle || !panel) {
            return;
        }

        const notificationContainer = toggle.closest('.lavea-notification');
        const csrfToken = @json(csrf_token());

        function markVisibleNotificationsAsRead() {
            panel.querySelectorAll('[data-read-url]').forEach(async function (item) {
                if (item.dataset.readPending) {
                    return;
                }

                item.dataset.readPending = 'true';

                try {
                    const response = await fetch(item.dataset.readUrl, {
                        method: 'POST',
                        credentials: 'same-origin',
                        headers: {
                            'X-CSRF-TOKEN': csrfToken,
                            'Accept': 'application/json',
                        },
                    });

                    if (!response.ok || response.redirected) {
                        return;
                    }

                    delete item.dataset.readUrl;
                    item.classList.remove('is-unread');
                    const remaining = Math.max(0, Number(notificationContainer.dataset.unreadCount) - 1);
                    notificationContainer.dataset.unreadCount = String(remaining);
                    const badge = toggle.querySelector('.lavea-notification-count');

                    if (badge) {
                        if (remaining === 0) {
                            badge.remove();
                        } else {
                            badge.textContent = remaining > 99 ? '99+' : String(remaining);
                        }
                    }
                } catch (error) {
                    // Keep unread notifications available for retry on the next opening.
                } finally {
                    delete item.dataset.readPending;
                }
            });
        }

        toggle.addEventListener('click', function () {
            panel.hidden = !panel.hidden;
            toggle.setAttribute('aria-expanded', panel.hidden ? 'false' : 'true');
            if (!panel.hidden) {
                markVisibleNotificationsAsRead();
            }
        });

        document.addEventListener('click', function (event) {
            if (!toggle.contains(event.target) && !panel.contains(event.target)) {
                panel.hidden = true;
                toggle.setAttribute('aria-expanded', 'false');
            }
        });
    })();
</script>
