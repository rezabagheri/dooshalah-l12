<div class="container mt-4">
    <div class="card">
        <div class="card-header">
            <h2 class="card-title">Notifications</h2>
            <div class="card-tools">
                <button wire:click="markAllAsRead" class="btn btn-primary" wire:loading.attr="disabled">
                    <i class="bi bi-check-all me-2"></i>
                    <span wire:loading.remove>Mark All as Read</span>
                    <span wire:loading>Marking...</span>
                </button>
            </div>
        </div>
        <div class="card-body">
            @if ($notifications->isEmpty())
                <p class="text-muted">You have no notifications yet.</p>
            @else
                <table class="table table-striped">
                    <thead>
                        <tr>
                            <th>Type</th>
                            <th>Title</th>
                            <th>Content</th>
                            <th>Date</th>
                            <th>Status</th>
                            <th>Actions</th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach ($notifications as $notification)
                            <tr>
                                <td>
                                    <i class="{{ $this->getIconForType($notification->type) }} me-2"></i>
                                    {{ $notification->type->label() }}
                                </td>
                                <td>{{ $notification->title }}</td>
                                <td>{{ $notification->content }}</td>
                                <td>{{ $notification->created_at->diffForHumans() }}</td>
                                <td>
                                    <span class="badge {{ $notification->is_read ? 'bg-success' : 'bg-warning' }}">
                                        {{ $notification->is_read ? 'Read' : 'Unread' }}
                                    </span>
                                </td>
                                <td>
                                    @if ($notification->action_url)
                                        <a href="{{ $notification->action_url }}" class="btn btn-sm btn-info">
                                            <i class="bi bi-eye"></i> View
                                        </a>
                                    @endif
                                    @if (!$notification->is_read)
                                        <button wire:click="markAsRead({{ $notification->id }})" class="btn btn-sm btn-primary">
                                            <i class="bi bi-check"></i> Mark as Read
                                        </button>
                                    @endif
                                </td>
                            </tr>
                        @endforeach
                    </tbody>
                </table>
            @endif
        </div>
    </div>
</div>
