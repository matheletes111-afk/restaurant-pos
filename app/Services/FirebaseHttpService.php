<?php

namespace App\Services;

use App\Models\User;
use App\Models\FcmToken;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;

class FirebaseHttpService
{
    protected $serverKey;
    protected $projectId;

    public function __construct()
    {
        // Get these from Firebase Console:
        // 1. Go to Firebase Console
        // 2. Project Settings > Cloud Messaging
        // 3. Copy "Server key"
        $this->serverKey = env('FIREBASE_SERVER_KEY');
        
        // Your Firebase Project ID
        $this->projectId = env('FIREBASE_PROJECT_ID', 'restaurant-app-72ecd');
    }

    /**
     * Send notification to all kitchen staff
     */
    public function notifyKitchenStaff($restaurantId, $title, $body, $data = [])
    {
        try {
            // Get all kitchen staff user IDs
            $kitchenStaffIds = User::where('restaurant_id', $restaurantId)
                ->where('role_type', 'Kitchen Staff')
                ->where('status', 'A')
                ->pluck('id')
                ->toArray();

            if (empty($kitchenStaffIds)) {
                Log::info("No kitchen staff found for restaurant: " . $restaurantId);
                return false;
            }

            // Get all FCM tokens for kitchen staff
            $tokens = FcmToken::whereIn('user_id', $kitchenStaffIds)
                ->pluck('token')
                ->toArray();

            if (empty($tokens)) {
                Log::info("No FCM tokens found for kitchen staff");
                return false;
            }

            // Send notification to each token
            $successCount = 0;
            foreach ($tokens as $token) {
                if ($this->sendToDevice($token, $title, $body, $data)) {
                    $successCount++;
                }
            }

            Log::info("Notifications sent to kitchen staff: {$successCount}/" . count($tokens) . " successful");
            return $successCount > 0;

        } catch (\Exception $e) {
            Log::error('Kitchen Staff Notification Error: ' . $e->getMessage());
            return false;
        }
    }

    /**
     * Send notification to specific device token
     */
    private function sendToDevice($token, $title, $body, $data = [])
    {
        try {
            if (!$this->serverKey) {
                Log::error('Firebase Server Key not configured');
                return false;
            }

            $url = 'https://fcm.googleapis.com/fcm/send';

            $payload = [
                'to' => $token,
                'notification' => [
                    'title' => $title,
                    'body' => $body,
                    'sound' => 'default',
                    'click_action' => 'OPEN_ORDER_DETAILS'
                ],
                'data' => array_merge($data, [
                    'type' => 'new_order',
                    'timestamp' => now()->toISOString()
                ]),
                'android' => [
                    'priority' => 'high'
                ],
                'apns' => [
                    'payload' => [
                        'aps' => [
                            'sound' => 'default',
                            'badge' => 1,
                            'content-available' => 1
                        ]
                    ]
                ]
            ];

            // For web push
            if (strpos($token, 'web:') !== false) {
                $payload['webpush'] = [
                    'notification' => [
                        'title' => $title,
                        'body' => $body,
                        'icon' => '/images/logo.png',
                        'vibrate' => [200, 100, 200]
                    ]
                ];
            }

            $response = Http::withHeaders([
                'Authorization' => 'key=' . $this->serverKey,
                'Content-Type' => 'application/json',
            ])->timeout(30)->post($url, $payload);

            if ($response->successful()) {
                $result = $response->json();
                
                if (isset($result['success']) && $result['success'] == 1) {
                    Log::info('Notification sent successfully to: ' . substr($token, 0, 20) . '...');
                    return true;
                } else {
                    Log::warning('FCM response error: ' . json_encode($result));
                    return false;
                }
            } else {
                Log::error('FCM HTTP error: ' . $response->status() . ' - ' . $response->body());
                return false;
            }

        } catch (\Exception $e) {
            Log::error('FCM Send Error: ' . $e->getMessage());
            return false;
        }
    }

    /**
     * Send notification to specific user
     */
    public function sendToUser($userId, $title, $body, $data = [])
    {
        try {
            $tokens = FcmToken::where('user_id', $userId)
                ->pluck('token')
                ->toArray();

            if (empty($tokens)) {
                Log::info("No FCM tokens found for user: " . $userId);
                return false;
            }

            $successCount = 0;
            foreach ($tokens as $token) {
                if ($this->sendToDevice($token, $title, $body, $data)) {
                    $successCount++;
                }
            }

            return $successCount > 0;

        } catch (\Exception $e) {
            Log::error('Send to User Error: ' . $e->getMessage());
            return false;
        }
    }
}