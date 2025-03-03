<div class="card">
    <div class="card-header">
        <h3 class="card-title">Compose New Message</h3>
    </div>
    <div class="card-body">
        @if (session('success'))
            <div class="alert alert-success">
                {{ session('success') }}
            </div>
        @endif

        <form wire:submit.prevent="sendMessage">
            <div class="mb-3">
                <label for="receiverId" class="form-label">To</label>
                <select class="form-control" id="receiverId" wire:model="receiverId" required>
                    <option value="">Select a recipient</option>
                    @foreach ($users as $user)
                        <option value="{{ $user->id }}">{{ $user->display_name }}</option>
                    @endforeach
                </select>
                @error('receiverId') <span class="text-danger">{{ $message }}</span> @enderror
            </div>
            <div class="mb-3">
                <label for="subject" class="form-label">Subject</label>
                <input type="text" class="form-control" id="subject" wire:model="subject" required>
                @error('subject') <span class="text-danger">{{ $message }}</span> @enderror
            </div>
            <div class="mb-3">
                <label for="message" class="form-label">Message</label>
                <textarea class="form-control" id="message" rows="6" wire:model="message" required></textarea>
                @error('message') <span class="text-danger">{{ $message }}</span> @enderror
            </div>
            <div class="text-end">
                <button type="submit" class="btn btn-primary">
                    <i class="bi bi-send me-2"></i> Send Message
                </button>
            </div>
        </form>
    </div>
</div>
