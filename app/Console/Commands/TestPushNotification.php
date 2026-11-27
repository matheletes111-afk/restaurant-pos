<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use App\Services\FirebaseNotificationService;

class TestPushNotification extends Command
{
    protected $signature = 'fcm:test {userId}';
    protected $description = 'Send a test push notification to a specific user';

    public function handle()
    {
        $userId = (int) $this->argument('userId');
        $this->info("Sending test notification to User ID: {$userId}...");

        $result = FirebaseNotificationService::sendToUsers(
            [$userId],
            "Test Push Notification",
            "Firebase push notification is working perfectly!",
            ['target_url' => url('/')]
        );

        $this->info("Success: {$result['success']} | Failed: {$result['failure']}");
    }
}
