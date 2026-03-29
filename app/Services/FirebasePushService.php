<?php

namespace App\Services;

use Google\Auth\Credentials\ServiceAccountCredentials;
use Illuminate\Support\Facades\Http;
use App\Models\User;

class FirebasePushService
{
    public static function send($fcmToken, $title, $body, $data = [])
    {
        $credentialsPath = base_path(env('FIREBASE_CREDENTIALS_PATH'));

        $credentials = new ServiceAccountCredentials(
            'https://www.googleapis.com/auth/firebase.messaging',
            json_decode(file_get_contents($credentialsPath), true)
        );

        $authToken = $credentials->fetchAuthToken();
        $accessToken = $authToken['access_token'];
        $projectId = env('FIREBASE_PROJECT_ID');

        $response = Http::withToken($accessToken)->post(
            "https://fcm.googleapis.com/v1/projects/{$projectId}/messages:send",
            [
                'message' => [
                    'token' => $fcmToken,
                    'notification' => [
                        'title' => $title,
                        'body' => $body,
                    ],
                    'data' => array_map('strval', $data),
                    'webpush' => [
                        'notification' => [
                            'icon' => '/images/logo.png',
                            'click_action' => url('/dashboard')
                        ]
                    ]
                ]
            ]
        );

        \Log::info('FCM RESPONSE', [
            'token' => $fcmToken,
            'response' => $response->json()
        ]);

        // 🔥 AUTO REMOVE DEAD TOKENS
        if ($response->failed()) {
            $json = $response->json();
            if (
                isset($json['error']['details'][0]['errorCode']) &&
                $json['error']['details'][0]['errorCode'] === 'UNREGISTERED'
            ) {
                User::where('fcm_token', $fcmToken)
                    ->update(['fcm_token' => null]);
            }
        }

        return $response->successful();
    }
}
