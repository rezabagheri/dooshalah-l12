<?php

namespace App\Livewire\Layouts;

use Livewire\Volt\Component;
use Illuminate\Contracts\View\View;

class Auth extends Component
{
    public string $bodyClass = 'login-page bg-body-secondary';

    public function toggleDarkMode(): void
    {
        $this->bodyClass = $this->bodyClass === 'login-page bg-body-secondary' ? 'login-page dark-mode' : 'login-page bg-body-secondary';
    }

    public function render(): View
    {
        return view('components.layouts.auth', [
            'bodyClass' => $this->bodyClass,
        ])->layout('components.layouts.clean', [
            'bodyClass' => $this->bodyClass,
        ]);
    }

    public static function layoutName(): string
    {
        return 'livewire.auth-layout';
    }
}
