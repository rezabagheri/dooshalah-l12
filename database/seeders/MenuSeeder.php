<?php

namespace Database\Seeders;

use App\Models\Menu;
use App\Models\MenuItem;
use Illuminate\Database\Seeder;

class MenuSeeder extends Seeder
{
    public function run(): void
    {
        $sidebar = Menu::updateOrCreate(
            ['slug' => 'sidebar'],
            ['name' => 'Sidebar Menu']
        );

        // Dashboard
        $dashboard = MenuItem::updateOrCreate(
            [
                'menu_id' => $sidebar->id,
                'label' => 'Dashboard',
            ],
            [
                'route' => 'dashboard',
                'icon' => 'bi bi-speedometer',
                'order' => 1,
                'has_divider' => true,
            ]
        );

        // Sub Dashboard
        MenuItem::updateOrCreate(
            [
                'menu_id' => $sidebar->id,
                'parent_id' => $dashboard->id,
                'label' => 'Sub Dashboard',
            ],
            [
                'route' => '#',
                'icon' => 'bi bi-gauge',
                'order' => 1,
            ]
        );

        // Settings (منوی اصلی)
        $settings = MenuItem::updateOrCreate(
            [
                'menu_id' => $sidebar->id,
                'label' => 'Settings',
            ],
            [
                'route' => '#',
                'icon' => 'bi bi-gear-fill',
                'order' => 2,
                'has_divider' => true,
            ]
        );

        // زیرمنوهای Settings
        MenuItem::updateOrCreate(
            [
                'menu_id' => $sidebar->id,
                'parent_id' => $settings->id,
                'label' => 'Profile',
            ],
            [
                'route' => 'settings.profile',
                'icon' => 'bi bi-person',
                'order' => 1,
            ]
        );

        MenuItem::updateOrCreate(
            [
                'menu_id' => $sidebar->id,
                'parent_id' => $settings->id,
                'label' => 'Password',
            ],
            [
                'route' => 'settings.password',
                'icon' => 'bi bi-lock',
                'order' => 2,
            ]
        );

        MenuItem::updateOrCreate(
            [
                'menu_id' => $sidebar->id,
                'parent_id' => $settings->id,
                'label' => 'Security',
            ],
            [
                'route' => 'settings.security',
                'icon' => 'bi bi-shield-lock',
                'order' => 3,
            ]
        );

        MenuItem::updateOrCreate(
            [
                'menu_id' => $sidebar->id,
                'parent_id' => $settings->id,
                'label' => 'Photos',
            ],
            [
                'route' => 'settings.photos',
                'icon' => 'bi bi-camera',
                'order' => 4,
            ]
        );

        MenuItem::updateOrCreate(
            [
                'menu_id' => $sidebar->id,
                'parent_id' => $settings->id,
                'label' => 'Interests',
            ],
            [
                'route' => 'settings.interests',
                'icon' => 'bi bi-heart',
                'order' => 5,
            ]
        );

        MenuItem::updateOrCreate(
            [
                'menu_id' => $sidebar->id,
                'parent_id' => $settings->id,
                'label' => 'Appearance',
            ],
            [
                'route' => 'settings.appearance',
                'icon' => 'bi bi-palette',
                'order' => 6,
            ]
        );

        // Friends (منوی اصلی)
        $friends = MenuItem::updateOrCreate(
            [
                'menu_id' => $sidebar->id,
                'label' => 'Friends',
            ],
            [
                'route' => 'friends.index',
                'icon' => 'bi bi-people',
                'order' => 3,
                'has_divider' => true,
            ]
        );

        // زیرمنوهای Friends
        MenuItem::updateOrCreate(
            [
                'menu_id' => $sidebar->id,
                'parent_id' => $friends->id,
                'label' => 'Browse',
            ],
            [
                'route' => 'friends.suggestions',
                'icon' => 'bi bi-search',
                'order' => 1,
            ]
        );

        MenuItem::updateOrCreate(
            [
                'menu_id' => $sidebar->id,
                'parent_id' => $friends->id,
                'label' => 'My Friends',
            ],
            [
                'route' => 'friends.my-friends',
                'icon' => 'bi bi-person-check',
                'order' => 2,
            ]
        );

        MenuItem::updateOrCreate(
            [
                'menu_id' => $sidebar->id,
                'parent_id' => $friends->id,
                'label' => 'Pending Requests',
            ],
            [
                'route' => 'friends.pending',
                'icon' => 'bi bi-hourglass-split',
                'order' => 3,
            ]
        );

        MenuItem::updateOrCreate(
            [
                'menu_id' => $sidebar->id,
                'parent_id' => $friends->id,
                'label' => 'Received Requests',
            ],
            [
                'route' => 'friends.received',
                'icon' => 'bi bi-envelope',
                'order' => 4,
            ]
        );

        MenuItem::updateOrCreate(
            [
                'menu_id' => $sidebar->id,
                'parent_id' => $friends->id,
                'label' => 'Blocked Users',
            ],
            [
                'route' => 'friends.blocked',
                'icon' => 'bi bi-person-lock',
                'order' => 5,
            ]
        );

        MenuItem::updateOrCreate(
            [
                'menu_id' => $sidebar->id,
                'parent_id' => $friends->id,
                'label' => 'Reports',
            ],
            [
                'route' => 'friends.reports',
                'icon' => 'bi bi-exclamation-triangle',
                'order' => 6,
            ]
        );

        $this->command->info('Sidebar menu seeded successfully!');
    }
}
