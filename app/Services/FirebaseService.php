<?php

namespace App\Services;

use Google\Client;
use GuzzleHttp\Client as GuzzleClient;
use GuzzleHttp\Exception\GuzzleException;

/**
 * Class FirebaseService
 *
 * A service class to handle sending notifications via Firebase Cloud Messaging (FCM) using HTTP v1 API.
 *
 * @package App\Services
 */
class FirebaseService
{
    /**
     * The HTTP client instance used to send requests to FCM.
     *
     * @var GuzzleClient
     */
    protected $httpClient;

    /**
     * The Google API client instance used to generate access tokens.
     *
     * @var Client
     */
    protected $googleClient;

    /**
     * The FCM project ID retrieved from environment variables.
     *
     * @var string
     */
    protected $projectId;

    /**
     * FirebaseService constructor.
     *
     * Initializes the HTTP client, Google client, and retrieves the FCM project ID.
     */
    public function __construct()
    {
        $this->httpClient = new GuzzleClient();
        $this->googleClient = new Client();
        $this->googleClient->setAuthConfig(storage_path(env('FIREBASE_CREDENTIALS')));
        $this->googleClient->addScope('https://www.googleapis.com/auth/firebase.messaging');
        $this->projectId = env('FIREBASE_PROJECT_ID');
    }

    /**
     * Send a notification to a specific FCM token using HTTP v1 API.
     *
     * @param string $to The FCM token of the recipient device.
     * @param string $title The title of the notification.
     * @param string $body The body content of the notification.
     * @return array The response from FCM as an associative array.
     * @throws GuzzleException If the request to FCM fails.
     */
    public function sendNotification(string $to, string $title, string $body): array
    {
        $accessToken = $this->googleClient->fetchAccessTokenWithAssertion()['access_token'];

        $response = $this->httpClient->post(
            "https://fcm.googleapis.com/v1/projects/{$this->projectId}/messages:send",
            [
                'headers' => [
                    'Authorization' => "Bearer {$accessToken}",
                    'Content-Type' => 'application/json',
                ],
                'json' => [
                    'message' => [
                        'token' => $to,
                        'notification' => [
                            'title' => $title,
                            'body' => $body,
                        ],
                    ],
                ],
            ]
        );

        return json_decode($response->getBody()->getContents(), true);
    }
}
