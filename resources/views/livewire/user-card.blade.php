<div>
    <div class="card bg-light d-flex flex-fill shadow-sm card-hover-effect">
        <!-- Header -->
        <div class="card-header border-bottom-0">
            <h5 class="card-title">
                {{ $user->display_name }}
                <i class="bi {{ $user->gender === \App\Enums\Gender::Male ? 'bi-gender-male' : 'bi-gender-female' }} ms-1"></i>
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
                    @elseif ($friendship->status === \App\Enums\FriendshipStatus::Pending && $friendship->user_id === auth()->id())
                        <button wire:click="cancelFriendship" class="btn btn-secondary btn-sm">
                            <i class="bi bi-x-circle me-1"></i> Remove Request
                        </button>
                    @elseif ($friendship->status === \App\Enums\FriendshipStatus::Pending && $friendship->target_id === auth()->id())
                        <button wire:click="acceptFriendship" class="btn btn-success btn-sm">
                            <i class="bi bi-check-circle me-1"></i> Accept Request
                        </button>
                        <button wire:click="rejectFriendship" class="btn btn-danger btn-sm">
                            <i class="bi bi-x-circle me-1"></i> Reject
                        </button>
                    @endif
                @else
                    <button wire:click="sendFriendshipRequest" class="btn btn-dark btn-sm">
                        <i class="bi bi-person-plus me-1"></i> Send Request
                    </button>
                @endif

                @if (!$block)
                    <button wire:click="block" class="btn btn-warning btn-sm">
                        <i class="bi bi-lock me-1"></i> Block
                    </button>
                    <button wire:click="report" class="btn btn-danger btn-sm" data-bs-toggle="modal" data-bs-target="#reportUserModal{{ $user->id }}">
                        <i class="bi bi-exclamation-triangle me-1"></i> Report
                    </button>
                @endif
            </div>

            <div class="d-md-none text-end">
                <div class="dropdown">
                    <button class="btn btn-dark btn-sm dropdown-toggle" type="button" id="dropdownMenuButton{{ $user->id }}" data-bs-toggle="dropdown" aria-expanded="false">
                        <i class="bi bi-gear"></i>
                    </button>
                    <ul class="dropdown-menu dropdown-menu-end" aria-labelledby="dropdownMenuButton{{ $user->id }}">
                        @if ($block)
                            <li><button wire:click="unblock" class="dropdown-item">Unblock</button></li>
                        @elseif ($friendship)
                            @if ($friendship->status === \App\Enums\FriendshipStatus::Accepted)
                                <li><button wire:click="unfriend" class="dropdown-item">Unfriend</button></li>
                            @elseif ($friendship->status === \App\Enums\FriendshipStatus::Pending && $friendship->user_id === auth()->id())
                                <li><button wire:click="cancelFriendship" class="dropdown-item">Remove Request</button></li>
                            @elseif ($friendship->status === \App\Enums\FriendshipStatus::Pending && $friendship->target_id === auth()->id())
                                <li><button wire:click="acceptFriendship" class="dropdown-item">Accept Request</button></li>
                                <li><button wire:click="rejectFriendship" class="dropdown-item">Reject</button></li>
                            @endif
                        @else
                            <li><button wire:click="sendFriendshipRequest" class="dropdown-item">Send Request</button></li>
                        @endif

                        @if (!$block)
                            <li><button wire:click="block" class="dropdown-item">Block</button></li>
                            <li><button wire:click="report" class="dropdown-item" data-bs-toggle="modal" data-bs-target="#reportUserModal{{ $user->id }}">Report</button></li>
                        @endif
                    </ul>
                </div>
            </div>
        </div>
    </div>

    <!-- مودال گزارش -->
    <div class="modal fade" id="reportUserModal{{ $user->id }}" tabindex="-1" aria-labelledby="reportUserModalLabel{{ $user->id }}" aria-hidden="true">
        <div class="modal-dialog">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title" id="reportUserModalLabel{{ $user->id }}">Report {{ $user->display_name }}</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <div class="modal-body">
                    @if (session('report-success'))
                        <div class="alert alert-success">
                            {{ session('report-success') }}
                        </div>
                    @else
                        <form wire:submit.prevent="submitReport">
                            <div class="mb-3">
                                <label for="reportReason{{ $user->id }}" class="form-label">Reason for Report</label>
                                <input type="text" class="form-control" id="reportReason{{ $user->id }}" wire:model="reportReason" placeholder="e.g., Inappropriate behavior" required>
                                @error('reportReason') <span class="text-danger">{{ $message }}</span> @enderror
                            </div>
                            <div class="mb-3">
                                <label for="reportDescription{{ $user->id }}" class="form-label">Description</label>
                                <textarea class="form-control" id="reportDescription{{ $user->id }}" rows="4" wire:model="reportDescription" placeholder="Please provide details..." required></textarea>
                                @error('reportDescription') <span class="text-danger">{{ $message }}</span> @enderror
                            </div>
                            <div class="modal-footer">
                                <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancel</button>
                                <button type="submit" class="btn btn-danger">Submit Report</button>
                            </div>
                        </form>
                    @endif
                </div>
            </div>
        </div>
    </div>

    <script>
        document.addEventListener('livewire:initialized', () => {
            Livewire.on('user-reported', () => {
                sessionStorage.setItem('report-success', 'Your report has been submitted successfully!');
                window.location.reload();
            });
            Livewire.on('close-modal', () => {
                bootstrap.Modal.getInstance(document.getElementById('reportUserModal{{ $user->id }}')).hide();
            });
        });

        window.addEventListener('load', () => {
            const successMessage = sessionStorage.getItem('report-success');
            if (successMessage) {
                Livewire.dispatch('report-success', { message: successMessage });
                sessionStorage.removeItem('report-success');
            }
        });
    </script>
</div>
