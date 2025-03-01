<?php

use App\Models\Media;
use App\Models\UserMedia;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Storage;
use Livewire\Attributes\Validate;
use Livewire\WithFileUploads;
use Livewire\Volt\Component;

new class extends Component {
    use WithFileUploads;

    #[Validate('image|max:10240')]
    public $photo;
    public $photoToDelete = null;
    public $photoPreview = null; // متغیر جدید برای پیش‌نمایش

    public function uploadPhoto(): void
    {
        $this->validate();

        $user = Auth::user();
        $fileName = 'user_' . $user->id . '_' . uniqid() . '.' . $this->photo->getClientOriginalExtension();

        if (!Storage::disk('public')->exists('user-profiles')) {
            Storage::disk('public')->makeDirectory('user-profiles');
        }

        try {
            $path = Storage::disk('public')->putFileAs('user-profiles', $this->photo, $fileName);
            $fullPath = storage_path('app/public/user-profiles/' . $fileName);

            if (!file_exists($fullPath)) {
                throw new \Exception('File was not stored at: ' . $fullPath);
            }

            $media = Media::create([
                'path' => 'user-profiles/' . $fileName,
                'original_name' => $this->photo->getClientOriginalName(),
                'type' => 'image',
                'mime_type' => $this->photo->getMimeType(),
                'size' => $this->photo->getSize(),
            ]);

            UserMedia::create([
                'user_id' => $user->id,
                'media_id' => $media->id,
                'is_profile' => false,
                'is_approved' => false,
                'order' => $user->media()->count(),
            ]);

            $this->reset(['photo', 'photoPreview']); // ریست پیش‌نمایش بعد از آپلود
            $this->dispatch('photo-uploaded');
        } catch (\Exception $e) {
            \Log::error('Photo upload failed: ' . $e->getMessage());
            $this->dispatch('error', 'Failed to upload the photo: ' . $e->getMessage());
            return;
        }
    }

    public function updatedPhoto($value): void
    {
        // وقتی فایل انتخاب می‌شه، پیش‌نمایش رو آپدیت می‌کنیم
        if ($this->photo) {
            $this->photoPreview = null; // ریست موقت
            $this->dispatch('preview-photo');
        }
    }

    public function confirmDelete($mediaId): void
    {
        \Log::info('Confirm delete triggered for media ID: ' . $mediaId);
        $this->photoToDelete = $mediaId;
        $this->dispatch('show-delete-modal');
    }

    public function deletePhoto(): void
    {
        if (!$this->photoToDelete) {
            $this->dispatch('close-delete-modal');
            return;
        }

        $user = Auth::user();
        $media = UserMedia::where('user_id', $user->id)->where('media_id', $this->photoToDelete)->firstOrFail();

        if ($media->is_profile && $user->media()->where('is_profile', true)->count() <= 1) {
            $this->dispatch('error', 'Please set another photo as your profile picture before deleting this one.');
            $this->photoToDelete = null;
            $this->dispatch('close-delete-modal');
            return;
        }

        Storage::disk('public')->delete($media->media->path);
        $media->delete();
        $media->media()->delete();

        $this->photoToDelete = null;
        $this->dispatch('photo-deleted');
        $this->dispatch('close-delete-modal');
    }

    public function setProfilePicture($mediaId): void
    {
        $user = Auth::user();

        UserMedia::where('user_id', $user->id)->where('is_profile', true)->update(['is_profile' => false]);
        $media = UserMedia::where('user_id', $user->id)->where('media_id', $mediaId)->firstOrFail();
        $media->is_profile = true;
        $media->save();

        $this->dispatch('profile-picture-updated');
    }
}; ?>

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
                        @if ($photoPreview)
                            <div class="mt-2" id="photo-preview" style="max-height: 200px;">
                                <img src="{{ $photoPreview }}" class="img-fluid" alt="Photo Preview">
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

    @script
    <script>
        document.getElementById('photo').addEventListener('change', function(event) {
            console.log('File input changed');
            const file = event.target.files[0];
            if (file) {
                const reader = new FileReader();
                reader.onload = function(e) {
                    console.log('File loaded: ', e.target.result);
                    $wire.set('photoPreview', e.target.result); // آپدیت متغیر Livewire
                };
                reader.readAsDataURL(file);
            }
        });
    </script>
    @endscript
</x-settings.layout>
