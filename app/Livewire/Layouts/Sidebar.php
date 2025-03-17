<?php

namespace App\Livewire\Layouts;

use App\Models\Menu;
use Livewire\Volt\Component;
use Illuminate\Contracts\View\View;

class Sidebar extends Component
{
    public function render(): View
    {
        $menu = Menu::where('slug', 'sidebar')
            ->with(['items' => function ($query) {
                $query->whereNull('parent_id')
                    ->orderBy('order')
                    ->with(['children' => function ($query) {
                        $query->orderBy('order');
                    }]);
            }])
            ->firstOrFail();

        // Filter menu items based on user role
        $filteredItems = $menu->items->filter(function ($item) {
            // Check if the top-level item is accessible
            if (!$item->isAccessible()) {
                return false;
            }

            // Filter children if the item has any
            if ($item->children->isNotEmpty()) {
                $item->setRelation('children', $item->children->filter(fn ($child) => $child->isAccessible()));
            }

            return true;
        });

        return view('components.layouts.sidebar', [
            'menuItems' => $filteredItems,
        ]);
    }
}
