<?php
namespace Database\Seeders;
use App\Models\Menu;
use App\Models\MenuItem;
use Illuminate\Database\Seeder;

class MenuSeeder extends Seeder
{
    public function run(): void
    {
        $sidebar = Menu::create(['name' => 'Sidebar Menu', 'slug' => 'sidebar']);

        $dashboard = MenuItem::create([
            'menu_id' => $sidebar->id,
            'label' => 'Dashboard',
            'route' => 'dashboard',
            'icon' => 'bi bi-speedometer',
            'order' => 1,
            'has_divider' => true,
        ]);

        MenuItem::create([
            'menu_id' => $sidebar->id,
            'label' => 'Settings',
            'route' => '#',
            'icon' => 'bi bi-gear-fill',
            'order' => 2,
        ]);

        MenuItem::create([
            'menu_id' => $sidebar->id,
            'parent_id' => $dashboard->id,
            'label' => 'Sub Dashboard',
            'route' => '#',
            'icon' => 'bi bi-gauge',
            'order' => 1,
        ]);
    }
}
