<?php

namespace App\Livewire\Layouts;

use Livewire\Volt\Component;

class Auth extends Component
{
    public string $bodyClass = 'login-page bg-body-secondary';

    public function render()
    {
        return view('components.layouts.auth', [
            'bodyClass' => $this->bodyClass,
        ]);
    }
}
