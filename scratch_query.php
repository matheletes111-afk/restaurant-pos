<?php
require 'vendor/autoload.php';
$app = require_once 'bootstrap/app.php';
$app->make('Illuminate\Contracts\Console\Kernel')->bootstrap();

$sub = DB::table('subscriptions')->latest()->first();
if ($sub) {
    echo "=== LATEST SUBSCRIPTION ===\n";
    echo "ID: {$sub->id}\n";
    echo "User ID (Restaurant ID): {$sub->user_id}\n";
    echo "Plan ID: {$sub->plan_id}\n";
    echo "Status: {$sub->status}\n";
    echo "Start Date: {$sub->start_date}\n";
    echo "End Date: {$sub->end_date}\n";
    echo "Renewal Date: {$sub->renewal_date}\n";
    echo "Razorpay Sub ID: {$sub->razorpay_subscription_id}\n";
} else {
    echo "No subscriptions found!\n";
}
