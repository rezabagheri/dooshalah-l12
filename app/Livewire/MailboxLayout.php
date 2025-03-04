<?php

namespace App\Livewire;

use Livewire\Component;

class MailboxLayout extends Component
{
    public $viewMode = 'inbox';

    public function mount($viewMode = 'inbox')
    {
        $this->viewMode = $viewMode;
    }

    public function render()
    {
        return view('livewire.mailbox-layout')->layout('components.layouts.app');
    }
}
