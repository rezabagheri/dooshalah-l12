<?php

namespace App\Http\Controllers;

use Kreait\Firebase\Messaging\CloudMessage;
use Kreait\Firebase\Messaging\Notification;
use Kreait\Laravel\Firebase\Facades\Firebase;

class TestFirebaseController extends Controller
{
    public function testNotification()
    {
        try {
            $messaging = Firebase::messaging();

            $notification = Notification::create('Test Notification', 'This is a test notification from Firebase!');

            // برای تست، یه پیام به یه موضوع (topic) فرستاده می‌شه
            $message = CloudMessage::withTarget('topic', 'test')
                ->withNotification($notification);

            $messaging->send($message);

            return response()->json(['message' => 'Test notification sent']);
        } catch (\Exception $e) {
            return response()->json(['error' => $e->getMessage()], 500);
        }
    }
}
