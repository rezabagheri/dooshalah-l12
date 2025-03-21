<?php

use App\Http\Controllers\ChatController;
use App\Http\Controllers\PaymentController;
use App\Livewire\Chat;
use App\Livewire\Settings\Interests;
use Illuminate\Support\Facades\Route;
use Livewire\Livewire;
use Livewire\Volt\Volt;

Route::get('/test-firebase', [App\Http\Controllers\TestFirebaseController::class, 'testNotification']);

Route::get('/', function () {
    return view('welcome');
})->name('home');

Route::middleware('guest')->group(function () {
    Route::get('/', fn() => redirect()->route('login'))->name('home');
    Route::get('/login', \App\Livewire\Auth\Login::class)->name('login');
    Route::get('/register', \App\Livewire\Auth\Register::class)->name('register');
    Route::get('/password/reset', \App\Livewire\Auth\ForgotPassword::class)->name('password.request');
    //Volt::route('login', 'auth.login')->name('login');
});

Route::get('/reset-password/{token}', \App\Livewire\Auth\ResetPassword::class)
    ->middleware('guest')
    ->name('password.reset');

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
    //Volt::route('settings/interests', 'settings.interests')->name('settings.interests');
    Route::get('settings/interests', Interests::class)->name('settings.interests');

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
        Route::get('/friends/my-friends', \App\Livewire\FriendsIndex::class)->name('friends.my-friends');
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

    //Chat Route
    Route::post('/chat/save-fcm-token', [ChatController::class, 'saveFcmToken'])->name('chat.save-fcm-token');
    Route::get('/chat', \App\Livewire\Chat::class)->name('chat');


    // Admin routes
    Route::prefix('admin')->name('admin.')->group(function () {
        Route::get('/dashboard', \App\Livewire\AdminDashboard::class)->name('dashboard');
        Route::get('/user', \App\Livewire\UserManagement::class)->name('user');
    });

    // Redirect /admin to admin.dashboard
    Route::get('/admin', fn () => redirect()->route('admin.dashboard'));

});


Route::prefix('admin')->name('admin.')->middleware(['auth'])->group(function () {
    Route::get('/dashboard', \App\Livewire\AdminDashboard::class)->name('dashboard');
    Route::get('/user', \App\Livewire\UserManagement::class)->name('user');
    Route::get('/user/show/{id}', \App\Livewire\UserView::class)->name('user.show');
    Route::get('/user/reset-password/{id}', fn ($id) => 'Reset password for user ' . $id)->name('user.reset-password'); // موقت
});
require __DIR__ . '/auth.php';
