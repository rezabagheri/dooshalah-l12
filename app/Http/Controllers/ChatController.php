<?php

namespace App\Http\Controllers;

use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

/**
 * Class ChatController
 *
 * Handles HTTP requests related to chat functionality in the application.
 *
 * @package App\Http\Controllers
 */
class ChatController extends Controller
{
    /**
     * Store the FCM token for the authenticated user.
     *
     * @param Request $request The incoming HTTP request containing the FCM token.
     * @return JsonResponse A JSON response indicating the success of the operation.
     */
    public function saveFcmToken(Request $request): JsonResponse
    {
        // Validate the incoming request
        $request->validate([
            'fcm_token' => 'required|string',
        ]);

        // Update the authenticated user's FCM token
        $user = Auth::user();
        $user->update(['fcm_token' => $request->input('fcm_token')]);

        // Return a success response
        return response()->json([
            'success' => true,
            'message' => 'FCM token saved successfully.',
        ]);
    }
}
