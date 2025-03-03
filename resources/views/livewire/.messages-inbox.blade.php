<div class="container-fluid mt-4">
    <div class="row">
        <!-- سایدبار دوم -->
        <div class="col-md-2">
            <div class="card">
                <div class="card-header">
                    <h3 class="card-title">Mailbox</h3>
                </div>
                <div class="card-body p-0">
                    <ul class="nav nav-pills flex-column">
                        <li class="nav-item">
                            <button wire:click="setViewMode('inbox')" class="nav-link {{ $viewMode == 'inbox' ? 'active' : '' }}">
                                <i class="bi bi-inbox me-2"></i> Inbox
                            </button>
                        </li>
                        <li class="nav-item">
                            <button wire:click="setViewMode('sent')" class="nav-link {{ $viewMode == 'sent' ? 'active' : '' }}">
                                <i class="bi bi-send me-2"></i> Sent
                            </button>
                        </li>
                        <li class="nav-item">
                            <button wire:click="setViewMode('draft')" class="nav-link {{ $viewMode == 'draft' ? 'active' : '' }}">
                                <i class="bi bi-pencil me-2"></i> Draft
                            </button>
                        </li>
                    </ul>
                </div>
            </div>
        </div>

        <!-- لیست پیام‌ها -->
        <div class="col-md-4">
            <div class="card">
                <div class="card-header">
                    <h3 class="card-title">{{ ucfirst($viewMode) }}</h3>
                    <div class="card-tools">
                        <button type="button" class="btn btn-tool" wire:click="$refresh">
                            <i class="bi bi-arrow-clockwise"></i>
                        </button>
                    </div>
                </div>
                <div class="card-body p-0">
                    @if ($messages->isEmpty())
                        <p class="text-muted p-3">No {{ $viewMode }} messages yet.</p>
                    @else
                        <ul class="list-group list-group-flush">
                            @foreach ($messages as $message)
                                <li class="list-group-item {{ $selectedMessageId == $message->id ? 'active' : '' }}" wire:click="selectMessage({{ $message->id }})" style="cursor: pointer;">
                                    <div class="d-flex justify-content-between">
                                        <div>
                                            <strong>{{ $viewMode == 'sent' || $viewMode == 'draft' ? ($message->receiver->display_name ?? 'Unknown') : ($message->sender->display_name ?? 'Unknown') }}</strong>
                                            <p class="text-muted mb-0">{{ \Illuminate\Support\Str::limit($message->subject, 30) }}</p>
                                        </div>
                                        <small class="text-muted">{{ $message->sent_at ? $message->sent_at->diffForHumans() : 'Draft' }}</small>
                                    </div>
                                    @if ($viewMode == 'inbox' && !$message->read_at)
                                        <span class="badge bg-primary position-absolute top-0 end-0 mt-2 me-2">New</span>
                                    @endif
                                </li>
                            @endforeach
                        </ul>
                    @endif
                </div>
            </div>
        </div>

        <!-- نمایش پیام و فرم ارسال -->
        <div class="col-md-6">
            <div class="card">
                <div class="card-header">
                    <h3 class="card-title">Message</h3>
                </div>
                <div class="card-body">
                    @if ($selectedMessage)
                        <div class="mb-4">
                            <h5>{{ $selectedMessage->subject }}</h5>
                            <p class="text-muted">
                                {{ $viewMode == 'sent' || $viewMode == 'draft' ? 'To' : 'From' }}:
                                {{ $viewMode == 'sent' || $viewMode == 'draft' ? ($selectedMessage->receiver->display_name ?? 'Unknown') : ($selectedMessage->sender->display_name ?? 'Unknown') }}
                                - {{ $selectedMessage->sent_at ? $selectedMessage->sent_at->format('Y-m-d H:i') : 'Draft' }}
                            </p>
                            <hr>
                            <p>{{ $selectedMessage->message }}</p>
                        </div>
                    @else
                        <p class="text-muted">Select a message to view its content.</p>
                    @endif

                    <form wire:submit.prevent="sendMessage">
                        <div class="mb-3">
                            <label for="newSubject" class="form-label">Subject</label>
                            <input type="text" class="form-control" id="newSubject" wire:model="newSubject" required>
                            @error('newSubject') <span class="text-danger">{{ $message }}</span> @enderror
                        </div>
                        <div class="mb-3">
                            <label for="newMessage" class="form-label">Message</label>
                            <textarea class="form-control" id="newMessage" rows="4" wire:model="newMessage" required></textarea>
                            @error('newMessage') <span class="text-danger">{{ $message }}</span> @enderror
                        </div>
                        <div class="text-end">
                            <button type="submit" class="btn btn-primary">
                                <i class="bi bi-send me-2"></i> Send Message
                            </button>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>

    <style>
        .nav-link {
            border-radius: 0;
        }
        .nav-link.active {
            background-color: #007bff;
            color: white;
        }
    </style>
</div>
