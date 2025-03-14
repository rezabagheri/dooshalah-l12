<?php

use App\Http\Controllers\PaymentController;
use Illuminate\Support\Facades\Route;
use Livewire\Livewire;
use Livewire\Volt\Volt;



Route::get('/test-firebase', [App\Http\Controllers\TestFirebaseController::class, 'testNotification']);

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

    //Volt::route('/messages', 'messages-inbox')->name('messages.inbox');
    //Volt::route('/messages/sent', 'messages-sent')->name('messages.sent');
    //Volt::route('/messages/drafts', 'messages-drafts')->name('messages.drafts');
    //Route::get('/messages/compose', \App\Livewire\MessagesCompose::class)->name('messages.compose');
    //Volt::route('/messages/read/{id}', 'messages-read')->name('messages.read');


    Volt::route('/messages', 'messages-inbox')->name('messages.inbox');

    Volt::route('/messages/sent', 'messages-sent')->name('messages.sent');

    Volt::route('/messages/drafts', 'messages-drafts')->name('messages.drafts');

    Route::get('/messages/compose', \App\Livewire\MessagesCompose::class)->name('messages.compose');

    Volt::route('/messages/read/{id}', 'messages-read')->name('messages.read');

    // Friends Routes
    Route::middleware(['auth'])->group(function () {
        Route::get('/friends', \App\Livewire\FriendsIndex::class)->name('friends.index');
        Route::get('/friends/suggestions', \App\Livewire\FriendsIndex::class)->name('friends.suggestions');
        Route::get('/friends/my-friends', \App\Livewire\FriendsIndex::class)
            ->name('friends.my-friends');
        Route::get('/friends/pending', \App\Livewire\FriendsIndex::class)->name('friends.pending');
        Route::get('/friends/received', \App\Livewire\FriendsIndex::class)->name('friends.received');
        Route::get('/friends/blocked', \App\Livewire\FriendsIndex::class)->name('friends.blocked');
        Route::get('/friends/reports', \App\Livewire\FriendsIndex::class)->name('friends.reports');
    });
    Route::get('/dashboard', \App\Livewire\Dashboard::class)->name('dashboard');
    Route::post('/logout', fn() => auth()->logout() && redirect('/login'))->name('logout');

    Route::get('/plans/upgrade', \App\Livewire\PlansUpgrade::class)->name('plans.upgrade');
    Route::get('/plans/payment/callback/{payment_id}', \App\Livewire\PaymentCallback::class)->name('plans.payment.callback');
    Route::get('/plans/payment/cancel/{payment_id}', \App\Livewire\PaymentCallback::class)->name('plans.payment.cancel');

    Route::get('/support', \App\Livewire\SupportPage::class)->name('support');
    Route::get('/payments', \App\Livewire\PaymentHistory::class)->name('payments.history');
    Route::get('/notifications', \App\Livewire\NotificationsPage::class)->name('notifications');


});



Route::middleware('auth')->get('/api/get-token', function () {
    $user = auth()->user();
    $token = $user->createToken('api-token')->plainTextToken;
    return response()->json(['token' => $token]);
});
require __DIR__ . '/auth.php';
