<?php

namespace App\Services;

class FirebasePushService
{
    public static function send($fcmToken, $title, $body, $data = [], $clickAction = null)
    {
        return false;
    }

    public static function sendToUser($userId, $title, $body, $data = [], $clickAction = null)
    {
        return false;
    }

    public static function sendToUsers(array $userIds, $title, $body, $data = [], $clickAction = null)
    {
        return false;
    }

    public static function sendToRestaurantStaff($restaurantId, $title, $body, $data = [], $clickAction = null)
    {
        return false;
    }

    public static function sendToKitchenStaff($restaurantId, $title, $body, $data = [], $clickAction = null)
    {
        return false;
    }
}
