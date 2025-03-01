<?php

namespace App\Livewire\Layouts;

use Livewire\Volt\Component;
use Illuminate\Contracts\View\View;
use Illuminate\Support\Facades\Route;

class Breadcrumb extends Component
{
    public function render(): View
    {
        $currentRoute = Route::currentRouteName();
        $breadcrumbs = [];

        // اگه توی صفحه‌ی dashboard یا home هستیم، breadcrumb رو خالی نگه می‌داریم
        if (in_array($currentRoute, ['dashboard', 'home'])) {
            return view('components.layouts.breadcrumb', ['breadcrumbs' => []]);
        }

        // آیتم پایه: Dashboard
        $breadcrumbs[] = [
            'label' => 'Dashboard',
            'route' => 'dashboard',
            'active' => false,
        ];

        // ساخت آیتم‌های بعدی بر اساس روت فعلی
        if ($currentRoute) {
            $routeParts = explode('.', $currentRoute);
            $label = count($routeParts) > 1 ? ucfirst($routeParts[1]) : ucfirst($routeParts[0]);
            $breadcrumbs[] = [
                'label' => $label,
                'route' => $currentRoute,
                'active' => true,
            ];
        }

        return view('components.layouts.breadcrumb', [
            'breadcrumbs' => $breadcrumbs,
        ]);
    }
}
