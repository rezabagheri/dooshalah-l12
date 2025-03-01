<?php

namespace App\Livewire;

use Livewire\Volt\Component;
use Livewire\Attributes\Layout;
use Illuminate\Contracts\View\View;

class Dashboard extends Component
{
    public string $bodyClass = 'dashboard-page';

    #[Layout('components.layouts.app')]
    public function render(): View
    {
        return view('livewire.dashboard', [
            'bodyClass' => $this->bodyClass,
            'page_title' => 'Dashboard',
        ])->layout('components.layouts.clean', [
            'bodyClass' => $this->bodyClass,
        ]);
    }
}
