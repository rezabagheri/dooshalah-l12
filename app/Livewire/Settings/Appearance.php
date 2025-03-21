<?php

namespace App\Livewire\Settings;

use Illuminate\Support\Facades\Auth;
use Livewire\Component;

class Appearance extends Component
{
    public string $theme = 'light';

    public function mount(): void
    {
        // اگه تم تو دیتابیس یا تنظیمات کاربر ذخیره می‌شه، از اونجا بخون
        $this->theme = Auth::user()->theme ?? 'light'; // فرض می‌کنیم یه ستون 'theme' تو جدول users داری
    }

    public function updateAppearance(): void
    {
        $validated = $this->validate([
            'theme' => ['required', 'in:light,dark'],
        ]);

        $user = Auth::user();
        $user->theme = $validated['theme'];
        $user->save();

        $this->dispatch('appearance-updated', theme: $this->theme);
    }

    public function render()
    {
        return view('livewire.settings.appearance');
    }
}
