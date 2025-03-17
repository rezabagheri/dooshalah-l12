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

        // Admin Panel (Top-level item)
        $adminPanel = MenuItem::updateOrCreate(
            [
                'menu_id' => $sidebar->id,
                'label' => 'Admin Panel',
            ],
            [
                'route' => '#',
                'icon' => 'bi bi-shield-check',
                'order' => 0,
                'has_divider' => true,
                'roles' => 'admin,super_admin', // Only for Admin and SuperAdmin
            ]
        );

        // Admin Dashboard (Sub-item)
        MenuItem::updateOrCreate(
            [
                'menu_id' => $sidebar->id,
                'parent_id' => $adminPanel->id,
                'label' => 'Dashboard',
            ],
            [
                'route' => 'admin.dashboard',
                'icon' => 'bi bi-speedometer',
                'order' => 1,
                'roles' => 'admin,super_admin',
            ]
        );

        // User Management (Sub-item)
        MenuItem::updateOrCreate(
            [
                'menu_id' => $sidebar->id,
                'parent_id' => $adminPanel->id,
                'label' => 'User Management',
            ],
            [
                'route' => 'admin.user',
                'icon' => 'bi bi-people',
                'order' => 2,
                'roles' => 'admin,super_admin',
            ]
        );

        // Dashboard (For all users)
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

        // Settings (Top-level item)
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

        // Sub-items for Settings
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

        // Friends (Top-level item)
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

        // Sub-items for Friends
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

        // Support
        MenuItem::updateOrCreate(
            [
                'menu_id' => $sidebar->id,
                'label' => 'Support',
            ],
            [
                'route' => 'support',
                'icon' => 'bi bi-headset',
                'order' => 5,
                'has_divider' => true,
            ]
        );

        // Payment History
        MenuItem::updateOrCreate(
            [
                'menu_id' => $sidebar->id,
                'label' => 'Payment History',
            ],
            [
                'route' => 'payments.history',
                'icon' => 'bi bi-receipt',
                'order' => 4,
                'has_divider' => true,
            ]
        );

        // Notifications
        MenuItem::updateOrCreate(
            [
                'menu_id' => $sidebar->id,
                'label' => 'Notifications',
            ],
            [
                'route' => 'notifications',
                'icon' => 'bi bi-bell-fill',
                'order' => 6,
                'has_divider' => true,
            ]
        );

        // Messages
        MenuItem::updateOrCreate(
            [
                'menu_id' => $sidebar->id,
                'label' => 'Messages',
            ],
            [
                'route' => 'messages.inbox',
                'icon' => 'bi bi-envelope-fill',
                'order' => 7,
                'has_divider' => true,
            ]
        );

        $this->command->info('Sidebar menu seeded successfully!');
    }
}
