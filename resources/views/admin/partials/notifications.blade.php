@php
    $notifications = auth()->user()->notifications()->latest()->take(8)->get();
    $unreadCount = auth()->user()->unreadNotifications()->count();
@endphp

<div class="lavea-notification">
    <button type="button" class="lavea-notification-button" id="notification-toggle" aria-label="Notifications" aria-expanded="false" aria-controls="notification-panel">
        <i data-lucide="bell"></i>
        @if ($unreadCount > 0)
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
            <div class="lavea-notification-item {{ $notification->read_at ? '' : 'is-unread' }}">
                @if ($notificationUrl)
                    <a class="lavea-notification-title" href="{{ $notificationUrl }}">
                        {{ $notification->data['title'] ?? 'Notification' }}
                    </a>
                @else
                    <strong class="lavea-notification-title">{{ $notification->data['title'] ?? 'Notification' }}</strong>
                @endif

                <p>{{ $notification->data['message'] ?? '' }}</p>
                <small>{{ $notification->created_at->diffForHumans() }}</small>

                @if (! $notification->read_at)
                    <form method="POST" action="{{ route('notifications.read', $notification->id) }}">
                        @csrf
                        <button type="submit" class="lavea-notification-read">Mark as read</button>
                    </form>
                @endif
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

        toggle.addEventListener('click', function () {
            panel.hidden = !panel.hidden;
            toggle.setAttribute('aria-expanded', panel.hidden ? 'false' : 'true');
        });

        document.addEventListener('click', function (event) {
            if (!toggle.contains(event.target) && !panel.contains(event.target)) {
                panel.hidden = true;
                toggle.setAttribute('aria-expanded', 'false');
            }
        });
    })();
</script>
