<div>
    <div class="card bg-light d-flex flex-fill shadow-sm card-hover-effect">
        <!-- Header -->
        <div class="card-header border-bottom-0">
            <h5 class="card-title">
                {{ $user->display_name }}
                <i class="bi {{ $user->gender === \App\Enums\Gender::Male ? 'bi-gender-male' : 'bi-gender-female' }} ms-1"></i>
                <span class="status-indicator {{ $user->last_seen && now()->diffInMinutes($user->last_seen) <= 5 ? 'online' : 'offline' }} ms-1"></span>
            </h5>
            <p class="card-text"><small class="text-muted">({{ intval($user->birth_date->diffInYears(now())) }} years old)</small></p>
        </div>

        <!-- Body -->
        <div class="card-body pt-2">
            <div class="row">
                <div class="col-7">
                    <p class="text-muted text-sm"><b>Interests:</b><br>
                        @foreach ($visibleInterests as $interest)
                            <strong>{{ $interest['label'] }}: </strong> {{ $interest['value'] }}<br>
                        @endforeach
                    </p>
                    <span class="badge {{ $matchPercentage >= 70 ? 'bg-success' : ($matchPercentage >= 50 ? 'bg-warning' : 'bg-danger') }} text-white">
                        {{ $matchPercentage }}% Match
                    </span>
                </div>
                <div class="col-5 text-center">
                    <img src="{{ $user->profilePicture()?->media->path ? asset('storage/' . $user->profilePicture()->media->path) : '/dist/assets/img/user2-160x160.jpg' }}"
                        alt="{{ $user->display_name }}" class="img-fluid">
                </div>
            </div>
        </div>

        <!-- Footer -->
        <div class="card-footer">
            <div class="d-none d-md-inline">
                @if ($block)
                    <button wire:click="unblock" class="btn btn-warning btn-sm">
                        <i class="bi bi-unlock me-1"></i> Unblock
                    </button>
                @elseif ($friendship)
                    @if ($friendship->status === \App\Enums\FriendshipStatus::Accepted)
                        <button wire:click="unfriend" class="btn btn-danger btn-sm">
                            <i class="bi bi-person-dash me-1"></i> Unfriend
                        </button>
                        <button wire:click="startChat" class="btn btn-primary btn-sm">
                            <i class="bi bi-chat-text me-1"></i> Send Chat
                        </button>
                    @elseif ($friendship->status === \App\Enums\FriendshipStatus::Pending && $friendship->user_id === auth()->id())
                        <button wire:click="cancelFriendship" class="btn btn-secondary btn-sm">
                            <i class="bi bi-x-circle me-1"></i> Remove Request
                        </button>
                        <button wire:click="startChat" class="btn btn-primary btn-sm">
                            <i class="bi bi-chat-text me-1"></i> Send Chat
                        </button>
                    @elseif ($friendship->status === \App\Enums\FriendshipStatus::Pending && $friendship->target_id === auth()->id())
                        <button wire:click="acceptFriendship" class="btn btn-success btn-sm">
                            <i class="bi bi-check-circle me-1"></i> Accept Request
                        </button>
                        <button wire:click="rejectFriendship" class="btn btn-danger btn-sm">
                            <i class="bi bi-x-circle me-1"></i> Reject
                        </button>
                        <button wire:click="startChat" class="btn btn-primary btn-sm">
                            <i class="bi bi-chat-text me-1"></i> Send Chat
                        </button>
                    @endif
                @else
                    <button wire:click="sendFriendshipRequest" class="btn btn-dark btn-sm">
                        <i class="bi bi-person-plus me-1"></i> Send Request
                    </button>
                    <button wire:click="startChat" class="btn btn-primary btn-sm">
                        <i class="bi bi-chat-text me-1"></i> Send Chat
                    </button>
                @endif

                @if (!$block)
                    <button wire:click="blockUser" class="btn btn-warning btn-sm">
                        <i class="bi bi-lock me-1"></i> Block
                    </button>
                    <button wire:click="openReportModal" class="btn btn-danger btn-sm">
                        <i class="bi bi-exclamation-triangle me-1"></i> Report
                    </button>
                @endif
            </div>

            <div class="d-md-none text-end">
                <div class="dropdown">
                    <button class="btn btn-dark btn-sm dropdown-toggle" type="button"
                        id="dropdownMenuButton{{ $user->id }}" data-bs-toggle="dropdown" aria-expanded="false">
                        <i class="bi bi-gear"></i>
                    </button>
                    <ul class="dropdown-menu dropdown-menu-end" aria-labelledby="dropdownMenuButton{{ $user->id }}">
                        @if ($block)
                            <li><button wire:click="unblock" class="dropdown-item"><i class="bi bi-unlock me-1"></i> Unblock</button></li>
                        @elseif ($friendship)
                            @if ($friendship->status === \App\Enums\FriendshipStatus::Accepted)
                                <li><button wire:click="unfriend" class="dropdown-item"><i class="bi bi-person-dash me-1"></i> Unfriend</button></li>
                                <li><button wire:click="startChat" class="dropdown-item"><i class="bi bi-chat-text me-1"></i> Send Chat</button></li>
                            @elseif ($friendship->status === \App\Enums\FriendshipStatus::Pending && $friendship->user_id === auth()->id())
                                <li><button wire:click="cancelFriendship" class="dropdown-item"><i class="bi bi-x-circle me-1"></i> Remove Request</button></li>
                                <li><button wire:click="startChat" class="dropdown-item"><i class="bi bi-chat-text me-1"></i> Send Chat</button></li>
                            @elseif ($friendship->status === \App\Enums\FriendshipStatus::Pending && $friendship->target_id === auth()->id())
                                <li><button wire:click="acceptFriendship" class="dropdown-item"><i class="bi bi-check-circle me-1"></i> Accept Request</button></li>
                                <li><button wire:click="rejectFriendship" class="dropdown-item"><i class="bi bi-x-circle me-1"></i> Reject</button></li>
                                <li><button wire:click="startChat" class="dropdown-item"><i class="bi bi-chat-text me-1"></i> Send Chat</button></li>
                            @endif
                        @else
                            <li><button wire:click="sendFriendshipRequest" class="dropdown-item"><i class="bi bi-person-plus me-1"></i> Send Request</button></li>
                            <li><button wire:click="startChat" class="dropdown-item"><i class="bi bi-chat-text me-1"></i> Send Chat</button></li>
                        @endif
                        @if (!$block)
                            <li><button wire:click="blockUser" class="dropdown-item"><i class="bi bi-lock me-1"></i> Block</button></li>
                            <li><button wire:click="openReportModal" class="dropdown-item"><i class="bi bi-exclamation-triangle me-1"></i> Report</button></li>
                        @endif
                    </ul>
                </div>
            </div>
        </div>
    </div>

    <!-- مودال گزارش -->
    <div wire:ignore.self class="modal fade" id="reportUserModal{{ $user->id }}" tabindex="-1"
         aria-labelledby="reportUserModalLabel{{ $user->id }}" aria-hidden="true">
        <div class="modal-dialog">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title" id="reportUserModalLabel{{ $user->id }}">Report {{ $user->display_name }}</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <div class="modal-body">
                    <form wire:submit.prevent="submitReport">
                        <div class="mb-3">
                            <label for="reportReason{{ $user->id }}" class="form-label">Reason for Report</label>
                            <input type="text" class="form-control" id="reportReason{{ $user->id }}"
                                   wire:model="reportReason" placeholder="e.g., Inappropriate behavior">
                            @error('reportReason') <span class="text-danger">{{ $message }}</span> @enderror
                        </div>
                        <div class="mb-3">
                            <label for="reportDescription{{ $user->id }}" class="form-label">Description</label>
                            <textarea class="form-control" id="reportDescription{{ $user->id }}" rows="4"
                                      wire:model="reportDescription" placeholder="Please provide details..."></textarea>
                            @error('reportDescription') <span class="text-danger">{{ $message }}</span> @enderror
                        </div>
                        <div class="modal-footer">
                            <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancel</button>
                            <button type="submit" class="btn btn-danger">Submit Report</button>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>

    @once
        <script>
            document.addEventListener('livewire:init', () => {
                window.addEventListener('open-report-modal', () => {
                    const modal = new bootstrap.Modal(document.getElementById('reportUserModal{{ $user->id }}'));
                    modal.show();
                });

                window.addEventListener('close-report-modal', () => {
                    const modal = bootstrap.Modal.getInstance(document.getElementById('reportUserModal{{ $user->id }}'));
                    modal?.hide();
                });
            });
        </script>
    @endonce

    <!-- استایل برای نشانگر وضعیت -->
    <style>
        .status-indicator {
            display: inline-block;
            width: 10px;
            height: 10px;
            border-radius: 50%;
            margin-left: 5px;
        }

        .status-indicator.online {
            background-color: #28a745;
        }

        .status-indicator.offline {
            background-color: #6c757d;
        }
    </style>
</div>
