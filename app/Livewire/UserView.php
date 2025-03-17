<?php

namespace App\Livewire;

use App\Models\User;
use Livewire\Component;

class UserView extends Component
{
    public User $user;

    public function mount($id): void
    {
        $this->user = User::findOrFail($id);
    }

    public function render()
    {
        return view('livewire.user-view');//->layout('layouts.admin');
    }
}
