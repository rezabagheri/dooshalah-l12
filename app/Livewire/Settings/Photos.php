<?php

namespace App\Livewire\Settings;

use App\Models\Media;
use App\Models\UserMedia;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Storage;
use Livewire\WithFileUploads;
use Livewire\Component;

class Photos extends Component
{
    use WithFileUploads;

    public $photo;
    public $photoToDelete = null;

    public function uploadPhoto(): void
    {
        $this->validate([
            'photo' => ['image', 'max:10240'], // حداکثر 10MB
        ]);

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

            $this->reset(['photo']);
            $this->dispatch('photo-uploaded');
        } catch (\Exception $e) {
            \Log::error('Photo upload failed: ' . $e->getMessage());
            $this->dispatch('error', 'Failed to upload the photo: ' . $e->getMessage());
            return;
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
        $this->dispatch('photo-deleted'); // باید کار کنه
        $this->dispatch('close-delete-modal');
        $this->reset();
    }

    public function setProfilePicture($mediaId): void
    {
        $user = Auth::user();

        UserMedia::where('user_id', $user->id)
            ->where('is_profile', true)
            ->update(['is_profile' => false]);
        $media = UserMedia::where('user_id', $user->id)->where('media_id', $mediaId)->firstOrFail();
        $media->is_profile = true;
        $media->save();

        $this->dispatch('profile-picture-updated'); // باید کار کنه
    }

    public function render()
    {
        return view('livewire.settings.photos');
    }
}
