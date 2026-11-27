<?php

namespace App\Services;

use App\Models\UserFcmToken;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;

class FirebaseNotificationService
{
    /**
     * Base64URL encode helper for JWT
     */
    protected static function base64UrlEncode(string $data): string
    {
        return rtrim(strtr(base64_encode($data), '+/', '-_'), '=');
    }

    /**
     * Generate OAuth2 Access Token using Google Service Account Private Key
     */
    public static function getAccessToken(): ?string
    {
        $path = base_path(config('services.firebase.service_account_path', 'storage/firebase/service-account.json'));

        if (!file_exists($path)) {
            Log::error("FCM Error: Service account JSON file not found at {$path}");
            return null;
        }

        $serviceAccount = json_decode(file_get_contents($path), true);
        if (!$serviceAccount || empty($serviceAccount['private_key']) || empty($serviceAccount['client_email'])) {
            Log::error("FCM Error: Invalid service account JSON.");
            return null;
        }

        $now = time();
        $jwtHeader = self::base64UrlEncode(json_encode(['alg' => 'RS256', 'typ' => 'JWT']));
        $jwtClaim = self::base64UrlEncode(json_encode([
            'iss'   => $serviceAccount['client_email'],
            'scope' => 'https://www.googleapis.com/auth/firebase.messaging',
            'aud'   => 'https://oauth2.googleapis.com/token',
            'iat'   => $now,
            'exp'   => $now + 3600,
        ]));

        $dataToSign = $jwtHeader . '.' . $jwtClaim;
        $signature = '';
        openssl_sign($dataToSign, $signature, $serviceAccount['private_key'], 'SHA256');
        $jwt = $dataToSign . '.' . self::base64UrlEncode($signature);

        $response = Http::asForm()->post('https://oauth2.googleapis.com/token', [
            'grant_type' => 'urn:ietf:params:oauth:grant-type:jwt-bearer',
            'assertion'  => $jwt,
        ]);

        return $response->json('access_token');
    }

    /**
     * Send push notification to an array of User IDs
     */
    public static function sendToUsers(array $userIds, string $title, string $message, array $data = []): array
    {
        $tokens = UserFcmToken::whereIn('user_id', $userIds)->pluck('fcm_token')->unique()->toArray();

        if (empty($tokens)) {
            Log::info("FCM: No active device tokens found for User IDs: " . implode(',', $userIds));
            return ['success' => 0, 'failure' => 0];
        }

        return self::sendViaHttpV1($tokens, $title, $message, $data);
    }

    /**
     * Send via Google FCM HTTP v1 REST API
     */
    public static function sendViaHttpV1(array $tokens, string $title, string $message, array $data = []): array
    {
        $accessToken = self::getAccessToken();
        if (!$accessToken) {
            return ['success' => 0, 'failure' => count($tokens)];
        }

        $projectId = config('services.firebase.project_id');
        $url = "https://fcm.googleapis.com/v1/projects/{$projectId}/messages:send";
        $clickAction = $data['target_url'] ?? ($data['click_action'] ?? url('/'));
        $icon = asset('logo.png');

        $sentCount = 0;
        $failedCount = 0;
        $invalidTokens = [];

        foreach ($tokens as $token) {
            $payload = [
                'message' => [
                    'token' => $token,
                    'notification' => [
                        'title' => $title,
                        'body'  => $message,
                    ],
                    'data' => array_merge($data, [
                        'title'        => $title,
                        'body'         => $message,
                        'click_action' => $clickAction,
                    ]),
                    'webpush' => [
                        'headers' => [
                            'Urgency' => 'high',
                        ],
                        'notification' => [
                            'title' => $title,
                            'body'  => $message,
                            'icon'  => $icon,
                            'badge' => $icon,
                            'require_interaction' => true,
                        ],
                        'fcm_options' => [
                            'link' => $clickAction,
                        ]
                    ]
                ]
            ];

            try {
                $response = Http::withToken($accessToken)
                    ->withHeaders(['Content-Type' => 'application/json; UTF-8'])
                    ->timeout(10)
                    ->post($url, $payload);

                if ($response->successful()) {
                    $sentCount++;
                } else {
                    $failedCount++;
                    $resBody = $response->json();
                    $errorCode = $resBody['error']['details'][0]['errorCode'] ?? ($resBody['error']['status'] ?? '');
                    if (in_array($errorCode, ['UNREGISTERED', 'INVALID_ARGUMENT', 'NOT_FOUND'])) {
                        $invalidTokens[] = $token;
                    }
                }
            } catch (\Exception $e) {
                $failedCount++;
                Log::error("FCM Send Exception: " . $e->getMessage());
            }
        }

        // Automatic cleanup of expired or uninstalled device tokens
        if (!empty($invalidTokens)) {
            UserFcmToken::whereIn('fcm_token', $invalidTokens)->delete();
        }

        return ['success' => $sentCount, 'failure' => $failedCount];
    }
}
