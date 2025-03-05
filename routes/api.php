<?php
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\ChatController;

Route::middleware('auth:sanctum')->group(function () {
    Route::post('/save-fcm-token', [ChatController::class, 'saveFcmToken']);
});
