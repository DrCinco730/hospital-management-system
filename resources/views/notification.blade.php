<div class="notifications-list" id="notificationsList">
    @foreach($record as $notification)
        <div class="notification-card">
            <div class="notification-icon">
                <i class="fas fa-info-circle"></i>
            </div>
            <div class="notification-content">
                <div class="notification-header">
                    <strong class="notification-type">{{ str_replace(["App\\Models\\", "_"], " ", $notification->event_type) }}</strong>
                    <span class="notification-action">{{ $notification->action }}</span>
                </div>
                <p class="notification-description">{{ str_replace(["App\\Models\\", "_"], " ", $notification->description) }}</p>
                <p class="notification-date">
                    {{ \Carbon\Carbon::parse($notification->occurred_at)->format('Y-m-d H:i') }}
                </p>
            </div>
        </div>
    @endforeach
</div>

{{--<div id="notificationCount" style="display: none;">{{ count($notifications) }}</div>--}}

<script>
    document.addEventListener('DOMContentLoaded', function() {
        {{--updateNotificationCount({{ count($notifications) }});--}}

        function updateNotificationCount(count) {
            const notificationCount = document.getElementById('notificationCount');
            notificationCount.innerText = count;
            notificationCount.style.display = count > 0 ? 'inline-block' : 'none';
        }


    });
</script>
