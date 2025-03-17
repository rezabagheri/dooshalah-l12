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
                    <button class="btn btn-danger btn-sm" data-bs-toggle="modal" data-bs-target="#reportUserModal{{ $user->id }}">
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
                            <li><button class="dropdown-item" data-bs-toggle="modal" data-bs-target="#reportUserModal{{ $user->id }}"><i class="bi bi-exclamation-triangle me-1"></i> Report</button></li>
                        @endif
                    </ul>
                </div>
            </div>
        </div>
    </div>

    <!-- مودال گزارش -->
    <div class="modal fade" id="reportUserModal{{ $user->id }}" tabindex="-1"
        aria-labelledby="reportUserModalLabel{{ $user->id }}" aria-hidden="true">
        <div class="modal-dialog">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title" id="reportUserModalLabel{{ $user->id }}">Report {{ $user->display_name }}</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <div class="modal-body">
                    <form id="reportForm{{ $user->id }}">
                        <div class="mb-3">
                            <label for="reportReason{{ $user->id }}" class="form-label">Reason for Report</label>
                            <input type="text" class="form-control" id="reportReason{{ $user->id }}"
                                name="reportReason" placeholder="e.g., Inappropriate behavior">
                        </div>
                        <div class="mb-3">
                            <label for="reportDescription{{ $user->id }}" class="form-label">Description</label>
                            <textarea class="form-control" id="reportDescription{{ $user->id }}" rows="4"
                                name="reportDescription" placeholder="Please provide details..."></textarea>
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

    <!-- مودال خطا -->
    <div class="modal fade" id="reportErrorModal{{ $user->id }}" tabindex="-1"
        aria-labelledby="reportErrorModalLabel{{ $user->id }}" aria-hidden="true">
        <div class="modal-dialog">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title" id="reportErrorModalLabel{{ $user->id }}">Error</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <div class="modal-body">
                    <div id="errorMessages{{ $user->id }}" class="text-danger"></div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-bs-target="#reportUserModal{{ $user->id }}"
                        data-bs-toggle="modal">Back to Report</button>
                </div>
            </div>
        </div>
    </div>

    @once
        <script>
            document.addEventListener('DOMContentLoaded', () => {
                const forms = document.querySelectorAll('[id^="reportForm"]');
                const modals = document.querySelectorAll('[id^="reportUserModal"]');

                // تابع پاکسازی سایه و فرم
                function cleanupModal(modalElement) {
                    const backdrop = document.querySelector('.modal-backdrop');
                    if (backdrop) {
                        backdrop.remove();
                    }
                    document.body.classList.remove('modal-open');
                    document.body.style.overflow = 'auto';
                    document.body.style.paddingRight = '';
                    const form = modalElement?.querySelector('form');
                    if (form) {
                        form.reset();
                    }
                }

                // مدیریت بستن مودال‌ها
                modals.forEach(modalElement => {
                    modalElement.addEventListener('hidden.bs.modal', () => {
                        console.log('Modal closed: ' + modalElement.id);
                        cleanupModal(modalElement);
                    });
                });

                // ارسال فرم
                forms.forEach(form => {
                    form.addEventListener('submit', (e) => {
                        e.preventDefault();
                        const userId = form.id.replace('reportForm', '');
                        const data = {
                            reportReason: form.querySelector('#reportReason' + userId).value,
                            reportDescription: form.querySelector('#reportDescription' + userId).value,
                        };
                        @this.call('submitReport', data);
                    });
                });

                // بستن مودال با رویداد
                Livewire.on('close-modal', () => {
                    console.log('Close modal event received');
                    modals.forEach(modalElement => {
                        const modalInstance = bootstrap.Modal.getInstance(modalElement);
                        if (modalInstance) {
                            modalInstance.hide();
                            setTimeout(() => cleanupModal(modalElement), 300);
                        }
                    });
                });

                // نمایش مودال خطا
                Livewire.on('show-error-modal', (event) => {
                    console.log('Show error modal event received', event);
                    const errors = event.detail?.errors || [];
                    const userId = event.detail?.userId;
                    if (userId) {
                        const errorContainer = document.getElementById('errorMessages' + userId);
                        if (errorContainer) {
                            errorContainer.innerHTML = errors.map(error => `<p>${error}</p>`).join('');
                        }
                        const reportModal = bootstrap.Modal.getInstance(document.getElementById('reportUserModal' + userId));
                        if (reportModal) {
                            reportModal.hide();
                        }
                        const errorModal = new bootstrap.Modal(document.getElementById('reportErrorModal' + userId));
                        errorModal.show();
                    }
                });

                // وقتی Livewire DOM رو آپدیت می‌کنه
                Livewire.on('morph.updated', () => {
                    console.log('Livewire updated DOM');
                    modals.forEach(modalElement => {
                        const modalInstance = bootstrap.Modal.getInstance(modalElement);
                        if (modalInstance && modalElement.classList.contains('show')) {
                            console.log('Keeping modal open after Livewire update: ' + modalElement.id);
                            modalInstance.show();
                        }
                    });
                });

                // رویدادهای بلاک و آنبلاک
                Livewire.on('user-blocked', () => {
                    console.log('User blocked event received');
                });

                Livewire.on('user-unblocked', () => {
                    console.log('User unblocked event received');
                });

                // موفقیت گزارش
                Livewire.on('report-success', (event) => {
                    console.log('Report success event received', event);
                    const userId = event.detail?.userId;
                    if (userId) {
                        const reportModal = bootstrap.Modal.getInstance(document.getElementById('reportUserModal' + userId));
                        if (reportModal) {
                            reportModal.hide();
                            setTimeout(() => cleanupModal(document.getElementById('reportUserModal' + userId)), 300);
                        }
                    }
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
            /* سبز برای آنلاین */
        }

        .status-indicator.offline {
            background-color: #6c757d;
            /* خاکستری برای آفلاین */
        }
    </style>
</div>
