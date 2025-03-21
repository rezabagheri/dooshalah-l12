<?php

use App\Http\Controllers\ChatController;
use App\Livewire\Settings\Interests;
use Illuminate\Support\Facades\Route;
use Livewire\Volt\Volt;

Route::get('/test-firebase', [App\Http\Controllers\TestFirebaseController::class, 'testNotification']);

// صفحه اصلی برای کاربران غیرلاگین
Route::get('/', function () {
    return view('welcome');
})->name('home');

// مسیرهای عمومی (برای کاربران مهمان)
Route::middleware('guest')->group(function () {
    Route::get('/', fn() => redirect()->route('login'))->name('home');
    Route::get('/login', \App\Livewire\Auth\Login::class)->name('login');
    Route::get('/register', \App\Livewire\Auth\Register::class)->name('register');
    Route::get('/password/reset', \App\Livewire\Auth\ForgotPassword::class)->name('password.request');
    Route::get('/reset-password/{token}', \App\Livewire\Auth\ResetPassword::class)->name('password.reset');

});

// مسیرهای تأیید (قبل از ورود کامل)
Route::get('/password/confirm', \App\Livewire\Auth\ConfirmPassword::class)->name('password.confirm');
Route::get('/email/verify', \App\Livewire\Auth\VerifyEmail::class)->name('verification.notice');

// مسیرهای محافظت‌شده (نیاز به ورود و تأیید ایمیل)
Route::middleware(['auth', 'verified'])->group(function () {
    Route::get('/dashboard', \App\Livewire\Dashboard::class)->name('dashboard');
    Route::post('/logout', fn() => auth()->logout() && redirect('/login'))->name('logout');


    Route::redirect('/settings', '/settings/profile');
    Route::get('/settings/security', \App\Livewire\Settings\Security::class)->name('settings.security');
    Route::get('/settings/profile', \App\Livewire\Settings\Profile::class)->name('settings.profile');
    Route::get('/settings/appearance', \App\Livewire\Settings\Appearance::class)->name('settings.appearance');
    Route::get('/settings/photos', \App\Livewire\Settings\Photos::class)->name('settings.photos');
    // تنظیمات (با Volt)
    //Volt::route('settings/profile', 'settings.profile')->name('settings.profile');
    Volt::route('settings/password', 'settings.password')->name('settings.password');
    //Volt::route('settings/appearance', 'settings.appearance')->name('settings.appearance');
    //Volt::route('settings/security', 'settings.security')->name('settings.security');
    //Volt::route('settings/photos', 'settings.photos')->name('settings.photos');
    Route::get('/settings/interests', Interests::class)->name('settings.interests');

    // پیام‌ها (با Volt)
    Volt::route('/messages', 'messages-inbox')->name('messages.inbox');
    Volt::route('/messages/sent', 'messages-sent')->name('messages.sent');
    Volt::route('/messages/drafts', 'messages-drafts')->name('messages.drafts');
    Route::get('/messages/compose', \App\Livewire\MessagesCompose::class)->name('messages.compose');
    Volt::route('/messages/read/{id}', 'messages-read')->name('messages.read');

    // دوستان
    Route::get('/friends', \App\Livewire\FriendsIndex::class)->name('friends.index');
    Route::get('/friends/suggestions', \App\Livewire\FriendsIndex::class)->name('friends.suggestions');
    Route::get('/friends/my-friends', \App\Livewire\FriendsIndex::class)->name('friends.my-friends');
    Route::get('/friends/pending', \App\Livewire\FriendsIndex::class)->name('friends.pending');
    Route::get('/friends/received', \App\Livewire\FriendsIndex::class)->name('friends.received');
    Route::get('/friends/blocked', \App\Livewire\FriendsIndex::class)->name('friends.blocked');
    Route::get('/friends/reports', \App\Livewire\FriendsIndex::class)->name('friends.reports');

    // پلن‌ها و پرداخت
    Route::get('/plans/upgrade', \App\Livewire\PlansUpgrade::class)->name('plans.upgrade');
    Route::get('/plans/payment/callback/{payment_id}', \App\Livewire\PaymentCallback::class)->name('plans.payment.callback');
    Route::get('/plans/payment/cancel/{payment_id}', \App\Livewire\PaymentCallback::class)->name('plans.payment.cancel');
    Route::get('/payments', \App\Livewire\PaymentHistory::class)->name('payments.history');

    // پشتیبانی و اعلان‌ها
    Route::get('/support', \App\Livewire\SupportPage::class)->name('support');
    Route::get('/notifications', \App\Livewire\NotificationsPage::class)->name('notifications');

    // چت
    Route::post('/chat/save-fcm-token', [ChatController::class, 'saveFcmToken'])->name('chat.save-fcm-token');
    Route::get('/chat', \App\Livewire\Chat::class)->name('chat');

    // ادمین
    Route::prefix('admin')->name('admin.')->group(function () {
        Route::get('/dashboard', \App\Livewire\AdminDashboard::class)->name('dashboard');
        Route::get('/user', \App\Livewire\UserManagement::class)->name('user');
        Route::get('/user/show/{id}', \App\Livewire\UserView::class)->name('user.show');
        Route::get('/user/reset-password/{id}', fn ($id) => 'Reset password for user ' . $id)->name('user.reset-password'); // موقت
    });
    Route::get('/admin', fn () => redirect()->route('admin.dashboard'));
});

require __DIR__ . '/auth.php';
