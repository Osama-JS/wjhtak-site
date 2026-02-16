<?php

namespace App\Services;

use App\Models\User;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Cache;

class FirebaseService
{
    protected $projectId;
    protected $credentialsPath;

    public function __construct()
    {
        $this->projectId = config('services.firebase.project_id', env('FIREBASE_PROJECT_ID'));
        $this->credentialsPath = base_path(config('services.firebase.credentials_path', env('FIREBASE_CREDENTIALS_PATH', 'storage/app/firebase/service-account.json')));
    }

    /**
     * Send a notification to a specific user.
     */
    public function sendToUser(User $user, string $title, string $body, array $data = []): bool
    {
        if (!$user->fcm_token) {
            Log::info("Firebase: User #{$user->id} has no FCM token, skipping push.");
            return false;
        }

        return $this->sendNotification($user->fcm_token, $title, $body, $data);
    }

    /**
     * Send notification to multiple users.
     */
    public function sendToMultiple(array $tokens, string $title, string $body, array $data = []): array
    {
        $results = ['success' => 0, 'failure' => 0, 'skipped' => 0];

        // Filter out empty tokens
        $validTokens = array_filter($tokens);

        if (empty($validTokens)) {
            return $results;
        }

        foreach ($validTokens as $token) {
            $sent = $this->sendNotification($token, $title, $body, $data);
            if ($sent) {
                $results['success']++;
            } else {
                $results['failure']++;
            }
        }

        Log::info("Firebase: Batch send completed", $results);
        return $results;
    }

    /**
     * Send notification to a specific FCM token.
     */
    public function sendNotification(string $token, string $title, string $body, array $data = []): bool
    {
        $accessToken = $this->getAccessToken();
        if (!$accessToken) {
            Log::error('Firebase: Could not generate access token.');
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
                'data' => array_map('strval', $data),
                'android' => [
                    'notification' => [
                        'sound' => 'default',
                        'channel_id' => $data['type'] ?? 'general',
                    ],
                ],
                'apns' => [
                    'payload' => [
                        'aps' => [
                            'sound' => 'default',
                            'badge' => 1,
                        ],
                    ],
                ],
            ],
        ];

        try {
            $response = Http::withToken($accessToken)->post($url, $message);

            if ($response->successful()) {
                return true;
            }

            $errorBody = $response->json();
            $errorCode = $errorBody['error']['details'][0]['errorCode'] ?? '';

            // Handle invalid/expired tokens
            if (in_array($errorCode, ['UNREGISTERED', 'INVALID_ARGUMENT'])) {
                Log::warning("Firebase: Token invalid/unregistered, should be cleaned up. Token: " . substr($token, 0, 20) . '...');
            } else {
                Log::error("Firebase: Send failed [{$response->status()}]", [
                    'error' => $errorBody,
                ]);
            }

            return false;
        } catch (\Exception $e) {
            Log::error("Firebase: Exception during send — {$e->getMessage()}");
            return false;
        }
    }

    /**
     * Generate OAuth2 Access Token with caching (valid ~55 min).
     */
    private function getAccessToken(): ?string
    {
        return Cache::remember('firebase_access_token', 3300, function () {
            if (!file_exists($this->credentialsPath)) {
                Log::error("Firebase: Credentials file not found at: {$this->credentialsPath}");
                return null;
            }

            $credentials = json_decode(file_get_contents($this->credentialsPath), true);
            if (!$credentials || empty($credentials['client_email']) || empty($credentials['private_key'])) {
                Log::error("Firebase: Invalid credentials JSON.");
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
                Log::error("Firebase: Failed to sign JWT.");
                return null;
            }

            $jwt = "$header.$payload." . base64url_encode($signature);

            $response = Http::asForm()->post($credentials['token_uri'], [
                'grant_type' => 'urn:ietf:params:oauth:grant-type:jwt-bearer',
                'assertion' => $jwt,
            ]);

            if ($response->successful()) {
                Log::info("Firebase: Access token generated successfully.");
                return $response->json('access_token');
            }

            Log::error("Firebase: Token exchange failed — " . $response->body());
            return null;
        });
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
