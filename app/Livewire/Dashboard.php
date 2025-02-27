<?php

namespace App\Livewire;

use Livewire\Volt\Component;
use Livewire\Attributes\Layout;
use Illuminate\Contracts\View\View;

class Dashboard extends Component
{
    public string $bodyClass = 'dashboard-page'; // متغیر برای Dashboard

    #[Layout('components.layouts.clean')]
    public function render(): View
    {
        return view('livewire.dashboard', [
            'bodyClass' => $this->bodyClass,
        ])->layout('components.layouts.clean', [
            'bodyClass' => $this->bodyClass,
        ]);
    }
}
