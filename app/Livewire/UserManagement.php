<?php

namespace App\Livewire;

use App\Models\User;
use Illuminate\Support\Facades\Hash;
use Livewire\Component;
use Livewire\WithPagination;

class UserManagement extends Component
{
    use WithPagination;

    public $search = '';
    public $sortBy = 'id';
    public $sortDirection = 'asc';
    public $perPage = 10;

    public $selectedUserId;
    public $newPassword;
    public $newPassword_confirmation; // تغییر از newPasswordConfirmation

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
        $this->newPassword_confirmation = ''; // تغییر از newPasswordConfirmation
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

    public function render()
    {
        $users = User::query()
            ->when($this->search, function ($query) {
                $query->where('first_name', 'like', '%' . $this->search . '%')
                      ->orWhere('last_name', 'like', '%' . $this->search . '%')
                      ->orWhere('email', 'like', '%' . $this->search . '%');
            })
            ->orderBy($this->sortBy, $this->sortDirection)
            ->paginate($this->perPage);

        return view('livewire.user-management', [
            'users' => $users,
            'page_title' => 'User Management',
        ]);
    }
}
