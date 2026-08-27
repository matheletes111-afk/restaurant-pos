<?php
require 'vendor/autoload.php';
$app = require_once 'bootstrap/app.php';
$app->make('Illuminate\Contracts\Console\Kernel')->bootstrap();

$restaurant = App\Models\RestaurantMaster::first();
if ($restaurant) {
    echo "Found restaurant: {$restaurant->name}\n";
    $activeSub = $restaurant->active_subscription;
    if ($activeSub) {
        echo "Active Subscription ID: {$activeSub->id}\n";
    } else {
        echo "No active subscription found (which is valid depending on status)\n";
    }
} else {
    echo "No restaurants found!\n";
}
