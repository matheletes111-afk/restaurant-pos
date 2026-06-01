<?php

namespace App\Services;

class FirebaseNotificationService
{
    public function registerToken($userId, $token, $deviceType)
    {
        return true;
    }

    public function unregisterToken($token)
    {
        return true;
    }
}
