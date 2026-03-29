<?php

namespace App\Services;

use Kreait\Firebase\Factory;
use Kreait\Firebase\Messaging\CloudMessage;
use Kreait\Firebase\Messaging\Notification;
use Kreait\Firebase\Messaging\WebPushConfig;
use Kreait\Firebase\Messaging\WebPushNotification;
use App\Models\User;
use App\Models\FcmToken;
use Illuminate\Support\Facades\Log;

class WebNotificationService
{
    protected $messaging;

    public function __construct()
    {
        try {
            $credentialsPath = base_path(env('FIREBASE_CREDENTIALS_PATH'));
            
            if (!file_exists($credentialsPath)) {
                throw new \Exception("Firebase credentials file not found at: " . $credentialsPath);
            }

            $factory = (new Factory)->withServiceAccount($credentialsPath);
            $this->messaging = $factory->createMessaging();
            
        } catch (\Exception $e) {
            Log::error('Firebase initialization error: ' . $e->getMessage());
            throw $e;
        }
    }

    /**
     * Send web push notification to specific user
     */
    public function sendToUser($userId, $title, $body, $data = [], $icon = null, $sound = null)
    {
        try {
            $tokens = FcmToken::where('user_id', $userId)
                ->where('device_type', 'web')
                ->pluck('token')
                ->toArray();

            if (empty($tokens)) {
                Log::info("No web FCM tokens found for user ID: " . $userId);
                return false;
            }

            $vapidKey = env('FIREBASE_VAPID_KEY');
            $icon = $icon ?? env('WEB_NOTIFICATION_ICON', '/images/logo.png');
            $sound = $sound ?? env('WEB_NOTIFICATION_SOUND');

            $webPushConfig = WebPushConfig::new()
                ->withVapidKey($vapidKey);
            
            if ($icon) {
                $webPushConfig = $webPushConfig->withNotification(
                    WebPushNotification::create($title, $body)
                        ->withIcon($icon)
                );
            }

            $notification = Notification::create($title, $body);
            
            $message = CloudMessage::new()
                ->withNotification($notification)
                ->withData(array_merge($data, [
                    'click_action' => 'OPEN_ORDER_DETAILS',
                    'icon' => $icon,
                    'sound' => $sound,
                    'timestamp' => now()->toISOString()
                ]))
                ->withWebPushConfig($webPushConfig);

            $sendReport = $this->messaging->sendMulticast($message, $tokens);
            
            Log::info('Web notification sent to user ' . $userId . ': ' . 
                     $sendReport->successes()->count() . ' successful');
            
            return $sendReport->successes()->count() > 0;
            
        } catch (\Exception $e) {
            Log::error('Web Push Error for user ' . $userId . ': ' . $e->getMessage());
            return false;
        }
    }

    /**
     * Send web notification to all kitchen staff
     */
    public function notifyKitchenStaffWeb($restaurantId, $title, $body, $data = [])
    {
        try {
            $kitchenStaffIds = User::where('restaurant_id', $restaurantId)
                ->where('role_type', 'Kitchen Staff')
                ->where('status', 'A')
                ->pluck('id')
                ->toArray();

            if (empty($kitchenStaffIds)) {
                Log::info("No kitchen staff found for restaurant ID: " . $restaurantId);
                return false;
            }

            $tokens = FcmToken::whereIn('user_id', $kitchenStaffIds)
                ->where('device_type', 'web')
                ->pluck('token')
                ->toArray();

            if (empty($tokens)) {
                Log::info("No web FCM tokens found for kitchen staff");
                return false;
            }

            $vapidKey = env('FIREBASE_VAPID_KEY');
            $icon = env('WEB_NOTIFICATION_ICON', '/images/logo.png');

            $notificationData = array_merge($data, [
                'type' => 'new_order',
                'restaurant_id' => $restaurantId,
                'click_action' => 'OPEN_ORDER_DETAILS',
                'icon' => $icon,
                'timestamp' => now()->toISOString()
            ]);

            $webPushConfig = WebPushConfig::new()
                ->withVapidKey($vapidKey)
                ->withNotification(
                    WebPushNotification::create($title, $body)
                        ->withIcon($icon)
                        ->withBadge('/images/badge.png')
                );

            $notification = Notification::create($title, $body);
            
            $message = CloudMessage::new()
                ->withNotification($notification)
                ->withData($notificationData)
                ->withWebPushConfig($webPushConfig);

            $sendReport = $this->messaging->sendMulticast($message, $tokens);
            
            Log::info('Web notification sent to ' . count($kitchenStaffIds) . ' kitchen staff: ' . 
                     $sendReport->successes()->count() . ' successful');
            
            return $sendReport->successes()->count() > 0;
            
        } catch (\Exception $e) {
            Log::error('Kitchen Staff Web Notification Error: ' . $e->getMessage());
            return false;
        }
    }

    /**
     * Send notification to all devices (web + mobile)
     */
    public function notifyKitchenStaffAll($restaurantId, $title, $body, $data = [])
    {
        try {
            $kitchenStaffIds = User::where('restaurant_id', $restaurantId)
                ->where('role_type', 'Kitchen Staff')
                ->where('status', 'A')
                ->pluck('id')
                ->toArray();

            if (empty($kitchenStaffIds)) {
                return false;
            }

            $tokens = FcmToken::whereIn('user_id', $kitchenStaffIds)
                ->pluck('token')
                ->toArray();

            if (empty($tokens)) {
                return false;
            }

            $notification = Notification::create($title, $body);
            
            $message = CloudMessage::new()
                ->withNotification($notification)
                ->withData(array_merge($data, [
                    'click_action' => 'OPEN_ORDER_DETAILS',
                    'timestamp' => now()->toISOString()
                ]));

            $sendReport = $this->messaging->sendMulticast($message, $tokens);
            
            return $sendReport->successes()->count() > 0;
            
        } catch (\Exception $e) {
            Log::error('Kitchen Staff Notification Error: ' . $e->getMessage());
            return false;
        }
    }
}