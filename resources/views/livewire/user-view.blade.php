<div class="container-fluid">
    <div class="row mb-3">
        <div class="col">
            <h2>User Details</h2>
            <a href="{{ route('admin.user') }}" class="btn btn-secondary btn-sm">
                <i class="bi bi-arrow-left me-1"></i> Back to Users
            </a>
        </div>
    </div>
    <div class="row">
        @include('livewire.partials.user-details')
        <div class="col-md-9">
            @include('livewire.partials.user-profile-pictures')
            @include('livewire.partials.user-interests')
        </div>
    </div>
</div>
