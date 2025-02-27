<?php

namespace App\Livewire\Layouts;

use App\Models\Menu;
use Livewire\Volt\Component;
use Illuminate\Contracts\View\View;

class Sidebar extends Component
{
    public function render(): View
    {
        // return view('components.layouts.sidebar', [
        //     'menuItems' => $this->menuItems,
        // ]);

        $menu = Menu::where('slug', 'sidebar')->with(['items' => function ($query) {
            $query->whereNull('parent_id')->orderBy('order')->with('children');
        }])->firstOrFail();

        return view('components.layouts.sidebar', [
            'menuItems' => $menu->items,
        ]);
    }
}
