<div class="container mt-4">
    <div class="card">
        <div class="card-header">
            <h2 class="card-title">Support Center</h2>
        </div>
        <div class="card-body">
            @if (session('success'))
                <div class="alert alert-success">
                    {{ session('success') }}
                </div>
            @endif

            <div class="row">
                <div class="col-md-6">
                    <h4>Contact Information</h4>
                    <p class="text-muted">We’re here to help! Reach out to us via the following methods:</p>
                    <ul class="list-unstyled">
                        <li><i class="bi bi-envelope me-2"></i> Email: <a href="mailto:support@yourapp.com">support@yourapp.com</a></li>
                        <li><i class="bi bi-telephone me-2"></i> Phone: +1 (123) 456-7890</li>
                        <li><i class="bi bi-clock me-2"></i> Hours: Monday - Friday, 9 AM - 5 PM</li>
                    </ul>
                </div>

                <div class="col-md-6">
                    <h4>Send Us a Message</h4>
                    <form wire:submit.prevent="submit">
                        <div class="mb-3">
                            <label for="name" class="form-label">Your Name</label>
                            <input type="text" class="form-control" id="name" wire:model="name" required>
                            @error('name') <span class="text-danger">{{ $message }}</span> @enderror
                        </div>
                        <div class="mb-3">
                            <label for="email" class="form-label">Your Email</label>
                            <input type="email" class="form-control" id="email" wire:model="email" required>
                            @error('email') <span class="text-danger">{{ $message }}</span> @enderror
                        </div>
                        <div class="mb-3">
                            <label for="message" class="form-label">Message</label>
                            <textarea class="form-control" id="message" rows="5" wire:model="message" required></textarea>
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
        </div>
    </div>
</div>
