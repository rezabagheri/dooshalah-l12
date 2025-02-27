<?php

namespace App\Livewire\Layouts;

use Livewire\Volt\Component;
use Illuminate\Contracts\View\View;

class Sidebar extends Component
{
    public array $menuItems = [
        [
            'route' => 'dashboard',
            'icon' => 'bi bi-speedometer',
            'label' => 'Dashboard',
        ],
        [
            'route' => '#', // بعداً روت واقعی می‌ذاریم
            'icon' => 'fas fa-cog',
            'label' => 'Settings',
        ],
        [
            'route' => '#', // بعداً روت واقعی می‌ذاریم
            'icon' => 'fas fa-users',
            'label' => 'Users',
        ],
    ];

    public function render(): View
    {
        return view('components.layouts.sidebar', [
            'menuItems' => $this->menuItems,
        ]);
    }
}
