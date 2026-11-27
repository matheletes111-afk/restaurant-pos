<?php

namespace App\Services;

class WebNotificationService
{
    public function sendToUser($userId, $title, $body, $data = [], $icon = null, $sound = null)
    {
        return false;
    }

    public function notifyKitchenStaffWeb($restaurantId, $title, $body, $data = [])
    {
        return false;
    }

    public function notifyRestaurantStaffWeb($restaurantId, $title, $body, $data = [])
    {
        return false;
    }

    public function notifyKitchenStaffAll($restaurantId, $title, $body, $data = [])
    {
        return false;
    }
}
