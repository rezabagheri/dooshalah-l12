<x-messages.layout heading="Compose New Message" subheading="Send a new message to someone">
    <div>
        @if (session('success'))
            <div class="alert alert-success">
                {{ session('success') }}
            </div>
        @endif

        <form wire:submit="sendMessage" class="form-horizontal">
            <div class="row mb-3">
                <label for="searchTerm" class="col-sm-3 col-form-label">To</label>
                <div class="col-sm-9 position-relative">
                    <input type="text" class="form-control" id="searchTerm" wire:input.debounce.300ms="updateSearch($event.target.value)" placeholder="Search for a recipient" autocomplete="off">
                    <!-- دیباگ برای چک کردن مقدار searchTerm -->
                    <div class="text-muted mt-1">Current search: {{ $searchTerm ?: 'None' }}</div>
                    @if ($searchTerm)
                        <div class="position-absolute w-100 bg-white border rounded shadow-sm mt-1 autocomplete-list" style="max-height: 200px; overflow-y: auto; z-index: 1000;">
                            @if (empty($filteredRecipients))
                                <div class="p-2 text-muted">No matching recipients found</div>
                            @else
                                @foreach ($filteredRecipients as $user)
                                    <div class="autocomplete-item" wire:click="selectRecipient({{ $user['id'] }})">
                                        <img src="{{ isset($user['profile_picture']) && !empty($user['profile_picture']['media']['path']) ? asset('storage/' . $user['profile_picture']['media']['path']) : asset('/dist/assets/img/user2-160x160.jpg') }}"
                                             style="width: 20px; height: 20px; margin-right: 10px;" alt="{{ $user['display_name'] }}">
                                        <span>{{ $user['display_name'] }}</span>
                                    </div>
                                @endforeach
                            @endif
                        </div>
                    @endif
                    <input type="hidden" name="receiverId" wire:model="receiverId" required>
                    @error('receiverId') <span class="text-danger">{{ $message }}</span> @enderror
                </div>
            </div>
            <div class="row mb-3">
                <label class="col-sm-3 col-form-label">Selected Recipient</label>
                <div class="col-sm-9">
                    <span>
                        @php
                            $selectedRecipient = $receiverId ? $recipients->firstWhere('id', $receiverId) : null;
                        @endphp
                        {{ $selectedRecipient ? $selectedRecipient->display_name : 'None selected' }}
                    </span>
                </div>
            </div>
            <div class="row mb-3">
                <label for="subject" class="col-sm-3 col-form-label">Subject</label>
                <div class="col-sm-9">
                    <input type="text" class="form-control" id="subject" wire:model="subject" required>
                    @error('subject') <span class="text-danger">{{ $message }}</span> @enderror
                </div>
            </div>
            <div class="row mb-3">
                <label for="message" class="col-sm-3 col-form-label">Message</label>
                <div class="col-sm-9">
                    <textarea class="form-control" id="message" rows="6" wire:model="message" required></textarea>
                    @error('message') <span class="text-danger">{{ $message }}</span> @enderror

                    <!-- بخش ایموجی‌ها -->
                    <div class="mt-2 d-flex flex-wrap gap-2">
                        @foreach ($emojis as $emoji)
                            <span class="emoji" wire:click="addEmoji('{{ $emoji['unicode'] }}')"
                                  style="font-size: 24px; cursor: pointer; transition: transform 0.2s ease-in-out;"
                                  title="Add {{ $emoji['name'] }} emoji">
                                {{ $emoji['unicode'] }}
                            </span>
                        @endforeach
                    </div>
                </div>
            </div>
            <div class="row">
                <div class="col-sm-9 offset-sm-3">
                    <button type="submit" class="btn btn-primary">
                        <i class="bi bi-send me-2"></i> Send Message
                    </button>
                </div>
            </div>
        </form>
    </div>
</x-messages.layout>
