<?php

use Illuminate\Support\Facades\Route;
use Livewire\Volt\Volt;

Route::get('/', function () {
    return view('welcome');
})->name('home');

Route::middleware('guest')->group(function () {
    Route::get('/', fn() => redirect()->route('login'))->name('home');
    Volt::route('login', 'auth.login')->name('login');
});

Route::view('dashboard', 'dashboard')
    ->middleware(['auth', 'verified'])
    ->name('dashboard');

Route::middleware(['auth'])->group(function () {
    Route::get('/plans/upgrade', \App\Livewire\PlansUpgrade::class)->name('plans.upgrade');
    Route::redirect('settings', 'settings/profile');

    Volt::route('settings/profile', 'settings.profile')->name('settings.profile');
    Volt::route('settings/password', 'settings.password')->name('settings.password');
    Volt::route('settings/appearance', 'settings.appearance')->name('settings.appearance');
    Volt::route('settings/security', 'settings.security')->name('settings.security');
    Volt::route('settings/photos', 'settings.photos')->name('settings.photos');
    Volt::route('settings/interests', 'settings.interests')->name('settings.interests');

    // Friends Routes
    Route::middleware(['auth'])->group(function () {
        Route::get('/friends', \App\Livewire\FriendsIndex::class)->name('friends.index');
        Route::get('/friends/suggestions', \App\Livewire\FriendsIndex::class)
            ->middleware('feature:view_suggestions')
            ->name('friends.suggestions');
        Route::get('/friends/my-friends', \App\Livewire\FriendsIndex::class)
            ->middleware('feature:view_suggestions') // حداقل برای دیدن دوستان
            ->name('friends.my-friends');
        Route::get('/friends/pending', \App\Livewire\FriendsIndex::class)
            ->middleware('feature:send_request')
            ->name('friends.pending');
        Route::get('/friends/received', \App\Livewire\FriendsIndex::class)
            ->middleware('feature:accept_request')
            ->name('friends.received');
        Route::get('/friends/blocked', \App\Livewire\FriendsIndex::class)
            ->middleware('feature:block_user')
            ->name('friends.blocked');
        Route::get('/friends/reports', \App\Livewire\FriendsIndex::class)
            ->middleware('feature:report_user')
            ->name('friends.reports');
    });
    Route::get('/dashboard', \App\Livewire\Dashboard::class)->name('dashboard');
    Route::post('/logout', fn() => auth()->logout() && redirect('/login'))->name('logout');
});

require __DIR__.'/auth.php';


