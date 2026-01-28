<?php

namespace App\Services;

use App\Models\User;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;

class FirebaseService
{
    protected $projectId;
    protected $credentialsPath;

    public function __construct()
    {
        $this->projectId = env('FIREBASE_PROJECT_ID');
        $this->credentialsPath = base_path(env('FIREBASE_CREDENTIALS_PATH', 'storage/app/firebase/service-account.json'));
    }

    /**
     * Send a notification to a specific user
     */
    public function sendToUser(User $user, $title, $body, $data = [])
    {
        if (!$user->fcm_token) {
            return false;
        }

        return $this->sendNotification($user->fcm_token, $title, $body, $data);
    }

    /**
     * Send notification to a specific FCM token
     */
    public function sendNotification($token, $title, $body, $data = [])
    {
        $accessToken = $this->getAccessToken();
        if (!$accessToken) {
            Log::error('Firebase Notification Error: Could not generate access token.');
            return false;
        }

        $url = "https://fcm.googleapis.com/v1/projects/{$this->projectId}/messages:send";

        $message = [
            'message' => [
                'token' => $token,
                'notification' => [
                    'title' => $title,
                    'body' => $body,
                ],
                'data' => array_map('strval', $data), // Data must be strings
                'android' => [
                    'notification' => [
                        'sound' => 'default',
                    ],
                ],
                'apns' => [
                    'payload' => [
                        'aps' => [
                            'sound' => 'default',
                        ],
                    ],
                ],
            ],
        ];

        $response = Http::withToken($accessToken)
            ->post($url, $message);

        if ($response->successful()) {
            return true;
        }

        Log::error('Firebase Notification Error: ' . $response->body());
        return false;
    }

    /**
     * Generate OAuth2 Access Token manually from Service Account JSON
     */
    private function getAccessToken()
    {
        if (!file_exists($this->credentialsPath)) {
            Log::error("Firebase Credentials file not found at: {$this->credentialsPath}");
            return null;
        }

        $credentials = json_decode(file_get_contents($this->credentialsPath), true);
        if (!$credentials) {
            Log::error("Invalid Firebase Credentials JSON.");
            return null;
        }

        $header = base64url_encode(json_encode(['alg' => 'RS256', 'typ' => 'JWT']));
        $now = time();
        $payload = base64url_encode(json_encode([
            'iss' => $credentials['client_email'],
            'scope' => 'https://www.googleapis.com/auth/cloud-platform',
            'aud' => $credentials['token_uri'],
            'exp' => $now + 3600,
            'iat' => $now,
        ]));

        $signature = '';
        $success = openssl_sign("$header.$payload", $signature, $credentials['private_key'], 'SHA256');

        if (!$success) {
            Log::error("Failed to sign Firebase JWT.");
            return null;
        }

        $jwt = "$header.$payload." . base64url_encode($signature);

        $response = Http::asForm()->post($credentials['token_uri'], [
            'grant_type' => 'urn:ietf:params:oauth:grant-type:jwt-bearer',
            'assertion' => $jwt,
        ]);

        if ($response->successful()) {
            return $response->json('access_token');
        }

        Log::error("Failed to exchange Firebase JWT for access token: " . $response->body());
        return null;
    }
}

/**
 * Helper function for base64url encoding
 */
if (!function_exists('base64url_encode')) {
    function base64url_encode($data)
    {
        return str_replace(['+', '/', '='], ['-', '_', ''], base64_encode($data));
    }
}
