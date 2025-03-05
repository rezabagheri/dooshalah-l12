<?php

namespace App\Http\Controllers;

use App\Models\FcmToken;
use Illuminate\Http\Request;

class ChatController extends Controller
{
    public function saveFcmToken(Request $request)
    {
        $request->validate([
            'token' => 'required|string',
        ]);

        $userId = auth()->id();
        $token = $request->input('token');

        FcmToken::updateOrCreate(
            ['user_id' => $userId, 'token' => $token],
            ['user_id' => $userId, 'token' => $token]
        );

        return response()->json(['message' => 'FCM token saved successfully']);
    }
}
