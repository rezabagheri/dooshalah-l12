<?php

namespace App\Livewire;

use App\Enums\UserRole;
use App\Enums\UserStatus;
use App\Models\User;
use App\Notifications\GeneralNotification;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Mail;
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

    public function mount($id): void
    {
        $this->user = User::with(['bornCountry', 'livingCountry'])->findOrFail($id);
        $this->status = $this->user->status;
        $this->role = $this->user->role;
        $this->originalStatus = $this->status;
        $this->originalRole = $this->role;
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
                $this->user->notify(new GeneralNotification(
                    subject: 'Your Account Has Been Updated',
                    message: 'Your account has been updated: ' . implode(', ', $changes) . '.',
                    actionUrl: url('/dashboard'),
                    actionText: 'View Dashboard'
                ));
                $this->dispatch('show-toast', [
                    'message' => "Updated " . implode(', ', $changes) . " for {$this->user->display_name}",
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
                    'message' => "Updated " . implode(', ', $changes) . ", but failed to notify user: " . $e->getMessage(),
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
                $message->to($this->user->email)
                        ->subject($this->emailSubject)
                        ->from(config('mail.from.address'), config('mail.from.name'));
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
                'message' => "Failed to send email: " . $e->getMessage(),
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
