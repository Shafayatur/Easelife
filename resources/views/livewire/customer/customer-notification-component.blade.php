<div class="customer-notifications-container">
    <div class="card">
        <div class="card-header bg-primary text-white">
            <h5 class="mb-0">
                Booking Notifications 
                @if($unreadCount > 0)
                    <span class="badge bg-danger ml-2">{{ $unreadCount }}</span>
                @endif
            </h5>
        </div>
        
        <div class="card-body p-0">
            @forelse($notifications as $notification)
                <div 
                    wire:click="viewNotificationDetails({{ $notification->id }})"
                    class="notification-item list-group-item list-group-item-action 
                        {{ $notification->status === 'unread' ? 'bg-light font-weight-bold' : '' }}"
                >
                    <div class="d-flex w-100 justify-content-between">
                        <h6 class="mb-1">
                            @if($notification->status === 'unread')
                                <span class="badge bg-primary mr-2">New</span>
                            @endif
                            {{ $notification->type === 'booking_accepted' ? 'Booking Accepted' : 'Booking Rejected' }}
                        </h6>
                        <small class="text-muted">
                            {{ $notification->created_at->diffForHumans() }}
                        </small>
                    </div>
                    <p class="mb-1">{{ $notification->message }}</p>
                </div>
            @empty
                <div class="text-center py-4 text-muted">
                    <p>No booking notifications</p>
                </div>
            @endforelse
        </div>
    </div>

    @if($selectedNotification)
        <div class="modal fade show" tabindex="-1" style="display: block; background: rgba(0,0,0,0.5);">
            <div class="modal-dialog">
                <div class="modal-content">
                    <div class="modal-header">
                        <h5 class="modal-title">
                            {{ $selectedNotification->type === 'booking_accepted' ? 'Booking Accepted' : 'Booking Rejected' }}
                        </h5>
                        <button 
                            type="button" 
                            class="close" 
                            wire:click="$set('selectedNotification', null)"
                        >
                            <span>&times;</span>
                        </button>
                    </div>
                    <div class="modal-body">
                        <p>{{ $selectedNotification->message }}</p>
                        @if($selectedNotification->relatedModel)
                            <hr>
                            <strong>Booking Details:</strong>
                            <p>
                                Service: {{ $selectedNotification->relatedModel->service->name }}<br>
                                Date: {{ \Carbon\Carbon::parse($selectedNotification->relatedModel->date)->format('d M Y') }}<br>
                                Time: {{ \Carbon\Carbon::parse($selectedNotification->relatedModel->time)->format('h:i A') }}
                            </p>
                        @endif
                    </div>
                </div>
            </div>
        </div>
    @endif
</div>

<style>
.notification-item {
    cursor: pointer;
    transition: background-color 0.3s ease;
}
.notification-item:hover {
    background-color: #f8f9fa !important;
}
</style>

<script>
    document.addEventListener('livewire:load', function () {
        // Optional: Add any additional JavaScript interactions
    });
</script>
