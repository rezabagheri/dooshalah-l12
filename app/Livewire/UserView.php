<?php

namespace App\Livewire;

use App\Enums\UserRole;
use App\Enums\UserStatus;
use App\Models\User;
use App\Notifications\GeneralNotification;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Mail;
use Livewire\Attributes\On;
use Livewire\Component;

class UserView extends Component
{
    public User $user;
    public $status;
    public $role;
    public $originalStatus;
    public $originalRole;
    public $emailSubject = '';
    public $emailBody = '';
    public $selectedMedia = null;
    public $currentIndex = 0;
    public $mediaFilter = 'all';

    public function mount($id): void
    {
        $this->user = User::with([
            'bornCountry',
            'livingCountry',
            'media.media',
            'userAnswers.question.options', // لود کردن پاسخ‌ها و سوال‌ها و گزینه‌ها
        ])->findOrFail($id);
        $this->status = $this->user->status;
        $this->role = $this->user->role;
        $this->originalStatus = $this->status;
        $this->originalRole = $this->role;
    }

    #[On('open-image-modal')]
    public function openImageModal($mediaId)
    {
        Log::info('openImageModal called', ['mediaId' => $mediaId]);
        $this->selectedMedia = $this->user->media()->findOrFail($mediaId);
        $this->currentIndex = $this->user->media->search(fn($item) => $item->id === $mediaId);
        $this->dispatch('show-image-modal');
        $this->dispatch('update-buttons', ['mediaId' => $mediaId, 'isApproved' => $this->selectedMedia->is_approved]);
    }

    #[On('carousel-changed')]
    public function updateSelectedMedia($mediaId)
    {
        Log::info('carousel-changed called', ['mediaId' => $mediaId]);
        $this->selectedMedia = $this->user->media()->findOrFail($mediaId);
        $this->currentIndex = $this->user->media->search(fn($item) => $item->id === $mediaId);
        $this->dispatch('update-buttons', ['mediaId' => $mediaId, 'isApproved' => $this->selectedMedia->is_approved]);
    }

    public function approveMedia($userMediaId)
    {
        $userMedia = $this->user->media()->findOrFail($userMediaId);
        $userMedia->update(['is_approved' => true]);
        $this->dispatch('show-toast', [
            'message' => "Media approved for {$this->user->display_name}",
            'type' => 'success',
        ]);
        if ($this->selectedMedia && $this->selectedMedia->id === $userMediaId) {
            $this->selectedMedia = $userMedia;
        }
        $this->dispatch('media-updated', ['mediaId' => $userMediaId, 'isApproved' => true]);
    }

    public function unapproveMedia($userMediaId)
    {
        $userMedia = $this->user->media()->findOrFail($userMediaId);
        $userMedia->update(['is_approved' => false]);
        $this->dispatch('show-toast', [
            'message' => "Media unapproved for {$this->user->display_name}",
            'type' => 'warning',
        ]);
        if ($this->selectedMedia && $this->selectedMedia->id === $userMediaId) {
            $this->selectedMedia = $userMedia;
        }
        $this->dispatch('media-updated', ['mediaId' => $userMediaId, 'isApproved' => false]);
    }

    public function setMediaFilter($filter)
    {
        Log::info('Media filter changed', ['filter' => $filter]);
        $this->mediaFilter = $filter;
    }

    public function getFilteredMedia()
    {
        $media = $this->user->media;
        Log::info('Filtering media', ['filter' => $this->mediaFilter, 'total' => $media->count()]);
        if ($this->mediaFilter === 'approved') {
            return $media->where('is_approved', true);
        } elseif ($this->mediaFilter === 'not_approved') {
            return $media->where('is_approved', false);
        }
        return $media; // 'all'
    }

    public function getFormattedAnswers()
    {
        $formatted = [];
        foreach ($this->user->userAnswers as $userAnswer) {
            $question = $userAnswer->question;
            if (!$question->is_visible) {
                continue; // فقط سوال‌های قابل‌نمایش
            }

            $answer = $userAnswer->answer; // مستقیم مقدار خام JSON
            if ($answer === null || $answer === '') {
                $formatted[$question->id] = [
                    'label' => $question->answer_label ?? $question->question,
                    'value' => 'No answer provided',
                ];
                continue;
            }

            switch ($question->answer_type) {
                case 'string':
                case 'number':
                    $formatted[$question->id] = [
                        'label' => $question->answer_label ?? $question->question,
                        'value' => $answer,
                    ];
                    break;
                case 'boolean':
                    $formatted[$question->id] = [
                        'label' => $question->answer_label ?? $question->question,
                        'value' => $answer === '1' || $answer === true || $answer === 1 ? 'Yes' : 'No',
                    ];
                    break;
                case 'single':
                    $option = $question->options->firstWhere('option_value', $answer);
                    $formatted[$question->id] = [
                        'label' => $question->answer_label ?? $question->question,
                        'value' => $option ? $option->option_value : $answer, // اگه گزینه پیدا نشد، مقدار خام رو نشون بده
                    ];
                    break;
                case 'multiple':
                    $values = [];
                    foreach ((array) $answer as $value) {
                        $option = $question->options->firstWhere('option_value', $value);
                        $values[] = $option ? $option->option_value : $value; // اگه گزینه پیدا نشد، مقدار خام
                    }
                    $formatted[$question->id] = [
                        'label' => $question->answer_label ?? $question->question,
                        'value' => implode(', ', $values) ?: 'No options selected',
                    ];
                    break;
            }
        }
        return $formatted;
    }

    public function updateUser()
    {
        $this->validate([
            'status' => ['required', 'in:' . implode(',', array_column(UserStatus::cases(), 'value'))],
            'role' => ['required', 'in:' . implode(',', array_column(UserRole::cases(), 'value'))],
        ]);

        $changes = [];
        if ($this->status !== $this->originalStatus) {
            $this->user->status = $this->status;
            $changes[] = "status to '{$this->getStatusLabel()}'";
        }
        if ($this->role !== $this->originalRole) {
            $this->user->role = $this->role;
            $changes[] = "role to '{$this->getRoleLabel()}'";
        }

        if (!empty($changes)) {
            $this->user->save();

            try {
                $this->user->notify(new GeneralNotification(subject: 'Your Account Has Been Updated', message: 'Your account has been updated: ' . implode(', ', $changes) . '.', actionUrl: url('/dashboard'), actionText: 'View Dashboard'));
                $this->dispatch('show-toast', [
                    'message' => 'Updated ' . implode(', ', $changes) . " for {$this->user->display_name}",
                    'type' => 'success',
                ]);
            } catch (\Exception $e) {
                Log::error('Failed to send update notification', [
                    'user_id' => $this->user->id,
                    'email' => $this->user->email,
                    'changes' => $changes,
                    'error' => $e->getMessage(),
                ]);
                $this->dispatch('show-toast', [
                    'message' => 'Updated ' . implode(', ', $changes) . ', but failed to notify user: ' . $e->getMessage(),
                    'type' => 'danger',
                ]);
            }

            $this->originalStatus = $this->status;
            $this->originalRole = $this->role;
        }
    }

    public function sendEmail()
    {
        $this->validate([
            'emailSubject' => 'required|string|max:255',
            'emailBody' => 'required|string|max:2000',
        ]);

        try {
            Mail::raw($this->emailBody, function ($message) {
                $message->to($this->user->email)->subject($this->emailSubject)->from(config('mail.from.address'), config('mail.from.name'));
            });

            $this->dispatch('show-toast', [
                'message' => "Email sent to {$this->user->email}",
                'type' => 'success',
            ]);
            $this->dispatch('close-contact-modal');
            $this->reset(['emailSubject', 'emailBody']);
        } catch (\Exception $e) {
            Log::error('Failed to send email', [
                'user_id' => $this->user->id,
                'email' => $this->user->email,
                'subject' => $this->emailSubject,
                'error' => $e->getMessage(),
            ]);
            $this->dispatch('show-toast', [
                'message' => 'Failed to send email: ' . $e->getMessage(),
                'type' => 'danger',
            ]);
        }
    }

    public function isDirty(): bool
    {
        return $this->status !== $this->originalStatus || $this->role !== $this->originalRole;
    }

    private function getStatusLabel(): string
    {
        return is_object($this->status) && method_exists($this->status, 'label') ? $this->status->label() : $this->status;
    }

    private function getRoleLabel(): string
    {
        return is_object($this->role) && method_exists($this->role, 'label') ? $this->role->label() : $this->role;
    }

    public function render()
    {
        return view('livewire.user-view', [
            'statuses' => UserStatus::cases(),
            'roles' => UserRole::cases(),
        ]);
    }
}
