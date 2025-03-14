<?php

namespace App\Services;

use GuzzleHttp\Client;

class FirebaseService
{
    protected $client;
    protected $serverKey;

    public function __construct()
    {
        $this->client = new Client();
        $this->serverKey = env('FIREBASE_SERVER_KEY');
    }

    public function sendNotification($to, $title, $body)
    {
        $response = $this->client->post('https://fcm.googleapis.com/fcm/send', [
            'headers' => [
                'Authorization' => 'key=' . $this->serverKey,
                'Content-Type' => 'application/json',
            ],
            'json' => [
                'to' => $to,
                'notification' => [
                    'title' => $title,
                    'body' => $body,
                ],
            ],
        ]);

        return json_decode($response->getBody(), true);
    }
}
