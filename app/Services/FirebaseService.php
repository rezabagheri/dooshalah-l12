<?php

namespace App\Services;

use Google\Client;
use GuzzleHttp\Client as GuzzleClient;
use GuzzleHttp\Exception\GuzzleException;
use Illuminate\Support\Facades\Log;

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
        try {
            $this->httpClient = new GuzzleClient();
            $this->googleClient = new Client();
            $credentialsPath = storage_path(env('FIREBASE_CREDENTIALS'));
            Log::info('Attempting to load Firebase credentials', ['path' => $credentialsPath]);
            if (!file_exists($credentialsPath)) {
                Log::error('Firebase credentials file not found', ['path' => $credentialsPath]);
                throw new \Exception('Firebase credentials file not found at: ' . $credentialsPath);
            }
            if (!is_readable($credentialsPath)) {
                Log::error('Firebase credentials file not readable', ['path' => $credentialsPath]);
                throw new \Exception('Firebase credentials file not readable at: ' . $credentialsPath);
            }
            $this->googleClient->setAuthConfig($credentialsPath);
            $this->googleClient->addScope('https://www.googleapis.com/auth/firebase.messaging');
            $this->projectId = env('FIREBASE_PROJECT_ID');
            if (!$this->projectId) {
                Log::error('FIREBASE_PROJECT_ID not set in .env');
                throw new \Exception('FIREBASE_PROJECT_ID not set');
            }
            Log::info('FirebaseService initialized successfully', ['project_id' => $this->projectId]);
        } catch (\Exception $e) {
            Log::error('Failed to initialize FirebaseService', ['error' => $e->getMessage()]);
            throw $e;
        }
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
        try {
            $accessToken = $this->googleClient->fetchAccessTokenWithAssertion()['access_token'];
            Log::info('Fetched access token for FCM', ['token' => substr($accessToken, 0, 10) . '...']);
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
            $result = json_decode($response->getBody()->getContents(), true);
            Log::info('Notification sent successfully', ['response' => $result]);
            return $result;
        } catch (GuzzleException $e) {
            Log::error('Failed to send Firebase notification', ['error' => $e->getMessage()]);
            throw $e;
        }
    }
}
