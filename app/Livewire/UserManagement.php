<?php

namespace App\Livewire;

use App\Models\User;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Password;
use Livewire\Component;
use Livewire\WithPagination;
use Exception;

class UserManagement extends Component
{
    use WithPagination;

    public $search = '';
    public $sortBy = 'id';
    public $sortDirection = 'asc';
    public $perPage = 10;

    public $selectedUserId;
    public $newPassword;
    public $newPassword_confirmation;

    public function updatingSearch()
    {
        $this->resetPage();
    }

    public function sort($column)
    {
        if ($this->sortBy === $column) {
            $this->sortDirection = $this->sortDirection === 'asc' ? 'desc' : 'asc';
        } else {
            $this->sortBy = $column;
            $this->sortDirection = 'asc';
        }
    }

    public function openResetPasswordModal($userId)
    {
        $this->selectedUserId = $userId;
        $this->newPassword = '';
        $this->newPassword_confirmation = '';
        $this->dispatch('open-modal');
    }

    public function resetPassword()
    {
        $this->validate([
            'newPassword' => 'required|min:8|confirmed',
        ]);

        $user = User::findOrFail($this->selectedUserId);
        $user->password = Hash::make($this->newPassword);
        $user->save();

        $this->dispatch('close-modal');
        session()->flash('message', 'Password reset successfully for ' . $user->display_name);
    }

    public function sendResetPasswordLink($userId)
    {
        $user = User::findOrFail($userId);

        try {
            $status = Password::sendResetLink(['email' => $user->email]);

            if ($status === Password::RESET_LINK_SENT) {
                session()->flash('message', 'Reset password link sent to ' . $user->display_name . ' successfully.');
            } else {
                session()->flash('error', 'Failed to send reset password link to ' . $user->display_name . ': ' . __($status));
            }
        } catch (Exception $e) {
            $errorMessage = $e->getMessage();
            if (str_contains($errorMessage, '550')) {
                session()->flash('error', 'Cannot send reset password link to ' . $user->display_name . '. The email address may be blacklisted.');
            } else {
                session()->flash('error', 'An error occurred while sending the reset password link to ' . $user->display_name . '. Please try again later.');
            }
        }
    }

    public function render()
    {
        $users = User::query()
            ->when($this->search, function ($query) {
                $query->where('first_name', 'like', '%' . $this->search . '%')
                      ->orWhere('last_name', 'like', '%' . $this->search . '%')
                      ->orWhere('display_name', 'like', '%' . $this->search . '%')
                      ->orWhere('email', 'like', '%' . $this->search . '%');
            })
            ->orderBy($this->sortBy, $this->sortDirection)
            ->paginate($this->perPage);

        return view('livewire.user-management', [
            'users' => $users,
        ]);
    }
}
