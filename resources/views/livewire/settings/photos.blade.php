<x-settings.layout heading="Photos" subheading="Manage your photo album">
    <div class="row mb-4">
        <div class="col-12">
            <div class="card card-primary card-outline card-hover-effect">
                <div class="card-header">
                    <h5 class="card-title">{{ __('Upload New Photo') }}</h5>
                </div>
                <div class="card-body">
                    <form wire:submit="uploadPhoto">
                        <div class="input-group">
                            <input wire:model="photo" type="file" class="form-control" id="photo" accept="image/*">
                            <button type="submit" class="btn btn-primary">
                                <i class="bi bi-upload me-1"></i> {{ __('Upload') }}
                            </button>
                        </div>
                        @if ($photo)
                            <div class="mt-2" id="photo-preview" style="max-height: 200px;">
                                <img src="{{ $photo->temporaryUrl() }}" class="img-fluid" alt="Photo Preview">
                            </div>
                        @endif
                        @error('photo') <span class="text-danger mt-1 d-block">{{ $message }}</span> @enderror
                    </form>
                </div>
            </div>
        </div>
    </div>

    <div class="row">
        @forelse (Auth::user()->media as $media)
            <div class="col-md-4 mb-3">
                <div class="card position-relative card-hover-effect {{ !$media->is_approved ? 'border-warning card-not-approved' : '' }}">
                    <div class="card-img-top overflow-hidden" style="max-height: 200px;">
                        <img src="{{ asset('storage/' . $media->media->path) }}"
                             class="img-fluid w-100 h-100 object-fit-contain" alt="User Photo">
                    </div>
                    @if (!$media->is_approved)
                        <div class="ribbon-wrapper ribbon-sm">
                            <div class="ribbon bg-warning text-sm">
                                Not Approved
                            </div>
                        </div>
                    @endif
                    <div class="card-body text-center">
                        <small class="text-muted">Uploaded: {{ $media->created_at->format('d M Y') }}</small>
                    </div>
                    <div class="card-footer d-flex justify-content-center gap-2">
                        <button wire:click="setProfilePicture({{ $media->media_id }})"
                                class="btn {{ $media->is_profile ? 'btn-success' : 'btn-outline-primary' }} btn-sm">
                            <i class="bi bi-person-check me-1"></i>
                            {{ $media->is_profile ? 'Current' : 'Set Profile' }}
                        </button>
                        <button wire:click="confirmDelete({{ $media->media_id }})"
                                class="btn btn-danger btn-sm">
                            <i class="bi bi-trash me-1"></i> Delete
                        </button>
                    </div>
                </div>
            </div>
        @empty
            <p class="text-muted">No photos available in your album.</p>
        @endforelse
    </div>

    <!-- Modal تأیید حذف -->
    <div class="modal fade" id="deleteModal" tabindex="-1" aria-labelledby="deleteModalLabel" aria-hidden="true">
        <div class="modal-dialog">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title" id="deleteModalLabel">Confirm Deletion</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <div class="modal-body">
                    Are you sure you want to delete this photo?
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancel</button>
                    <button wire:click="deletePhoto" class="btn btn-danger">Delete</button>
                </div>
            </div>
        </div>
    </div>
</x-settings.layout>
