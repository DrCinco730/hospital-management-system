<div class="notifications-list" id="notificationsList">
    @if($record->isEmpty())
        <p class="no-notifications">No new notifications</p>
    @else
        @foreach($record as $notification)
            <div class="notification-card">
                <div class="notification-icon">
                    <i class="fas fa-info-circle"></i>
                </div>
                <div class="notification-content">
                    <div class="notification-header">
                        <strong class="notification-type">
                            {{ str_replace(["App\\Models\\", "_"], " ", $notification->event_type) }}
                        </strong>
                        <span class="notification-action">{{ ucfirst($notification->action) }}</span>
                    </div>
                    <p class="notification-description">
                        {{ str_replace(["App\\Models\\", "_"], " ", $notification->description) }}
                    </p>
                    <p class="notification-date">
                        {{ \Carbon\Carbon::parse($notification->occurred_at)->format('Y-m-d H:i') }}
                    </p>
                </div>
            </div>
            @if(isset($notification['meta_data']['attributes']))
                <div class="meta-data">
                    <strong>Details:</strong>
                    <ul>
                        @foreach($notification['meta_data']['attributes'] as $key => $value)
                            <li><strong>{{ $key }}:</strong> {{ $value }}</li>
                        @endforeach
                    </ul>
                </div>
            @endif
        @endforeach

    @endif
</div>

<!-- Custom Pagination -->
<div class="custom-pagination">
    @if ($record->onFirstPage())
        <span class="pagination-link disabled">&laquo; First</span>
        <span class="pagination-link disabled">&lsaquo; Prev</span>
    @else
        <button id="page" onclick="fetchRecords('{{ $record->url(1) }}')" class="pagination-link">&laquo; First</button>
        <button id="page" onclick="fetchRecords('{{ $record->previousPageUrl() }}')" class="pagination-link">&lsaquo; Prev</button>
    @endif

    @foreach ($record->getUrlRange(max(1, $record->currentPage() - 2), min($record->lastPage(), $record->currentPage() + 2)) as $page => $url)
        <button id="page" onclick="fetchRecords('{{ $url }}')" class="pagination-link {{ $page == $record->currentPage() ? 'active' : '' }}">{{ $page }}</button>
    @endforeach

    @if ($record->hasMorePages())
        <button id="page" onclick="fetchRecords('{{ $record->nextPageUrl() }}')" class="pagination-link">Next &rsaquo;</button>
        <button id="page" onclick="fetchRecords('{{ $record->url($record->lastPage()) }}')" class="pagination-link">Last &raquo;</button>
    @else
        <span class="pagination-link disabled">Next &rsaquo;</span>
        <span class="pagination-link disabled">Last &raquo;</span>
    @endif
</div>


<!-- Styles for Notifications and Pagination -->
<style>
    /* Notifications Styling */
    .notifications-list {
        display: flex;
        flex-direction: column;
        gap: 15px;
        margin-top: 20px;
        font-family: 'Poppins', sans-serif;
    }

    .no-notifications {
        text-align: center;
        font-size: 1rem;
        color: #888;
    }

    .notification-card {
        display: flex;
        align-items: flex-start;
        gap: 15px;
        padding: 15px;
        background-color: #f9f9f9;
        border-radius: 8px;
        box-shadow: 0px 4px 8px rgba(0, 0, 0, 0.1);
    }

    .notification-icon {
        color: #3490dc;
        font-size: 1.5rem;
    }

    .notification-content {
        flex: 1;
    }

    .notification-header {
        display: flex;
        justify-content: space-between;
        align-items: center;
        margin-bottom: 5px;
    }

    .notification-type {
        font-weight: bold;
        color: #333;
    }

    .notification-action {
        font-size: 0.9rem;
        color: #777;
    }

    .notification-description {
        margin: 5px 0;
        color: #555;
    }

    .notification-date {
        font-size: 0.85rem;
        color: #aaa;
    }

    /* Custom Pagination Styling */
    .custom-pagination {
        display: flex;
        justify-content: center;
        gap: 5px;
        margin-top: 20px;
        padding: 10px;
        font-family: 'Poppins', sans-serif;
    }

    .pagination-link {
        padding: 8px 12px;
        font-size: 0.9rem;
        color: #007bff;
        background-color: #fff;
        border: 1px solid #ddd;
        border-radius: 4px;
        text-decoration: none;
        transition: background-color 0.3s ease, color 0.3s ease;
    }

    .pagination-link.active {
        background-color: #007bff;
        color: #fff;
        font-weight: bold;
    }

    .pagination-link:hover:not(.active) {
        background-color: #e0e7ff;
        color: #0056b3;
    }

    .pagination-link.disabled {
        color: #ccc;
        pointer-events: none;
    }
    .meta-data {
        margin-top: 10px;
        padding: 10px;
        background-color: #f1f1f1;
        border-left: 3px solid #007bff;
        border-radius: 5px;
        font-size: 0.9rem;
        color: #555;
    }

    .meta-data strong {
        font-weight: bold;
        color: #333;
    }

    .meta-data-list {
        margin: 5px 0 0;
        padding-left: 20px;
        list-style-type: disc;
    }

    .meta-data-list li {
        margin-bottom: 5px;
    }

</style>


<!-- Styles for Meta Data -->
<style>
    .meta-data {
        margin-top: 10px;
        padding: 10px;
        background-color: #f1f1f1;
        border-left: 3px solid #007bff;
        border-radius: 5px;
        font-size: 0.95rem;
        color: #333;
    }

    .meta-data strong {
        font-weight: bold;
        color: #333;
    }

    .meta-data-list {
        margin: 5px 0 0;
        padding-left: 20px;
        list-style-type: disc;
    }

    .meta-data-list li {
        margin-bottom: 5px;
    }

    .meta-data-list li strong {
        color: #000;
    }
</style>

<script>
    updateNotificationCount({{$count_notification}});
</script>
