<div class="container">
    <div class="row">
        <div class="col-md-12">
            <div class="card">
                <div class="card-header">
                    <h3 class="mb-0">
                        Notifications 
                        @if($unreadCount > 0)
                            <span class="badge bg-danger">{{ $unreadCount }} Unread</span>
                        @endif
                    </h3>
                </div>
                <div class="card-body">
                    @if($notifications->isEmpty())
                        <div class="alert alert-info text-center">
                            No notifications at the moment.
                        </div>
                    @else
                        <div class="list-group">
                            @foreach($notifications as $notification)
                                <div class="list-group-item list-group-item-action 
                                    {{ $notification->status == 'unread' ? 'list-group-item-primary' : '' }}"
                                    wire:click="viewServiceRequest('{{ $notification->id }}')">
                                    <div class="d-flex w-100 justify-content-between">
                                        <h5 class="mb-1">{{ $notification->message }}</h5>
                                        <small>{{ $notification->created_at->diffForHumans() }}</small>
                                    </div>
                                    @if($notification->status == 'unread')
                                        <button 
                                            class="btn btn-sm btn-outline-secondary mt-2"
                                            wire:click.stop="markAsRead('{{ $notification->id }}')"
                                        >
                                            Mark as Read
                                        </button>
                                    @endif
                                </div>
                            @endforeach
                        </div>
                    @endif
                </div>
            </div>
        </div>
    </div>
</div>

<style>
.list-group-item-action {
    cursor: pointer;
    transition: background-color 0.3s ease;
}
.list-group-item-action:hover {
    background-color: #f8f9fa;
}
</style>
