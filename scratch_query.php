<?php
require 'vendor/autoload.php';
$app = require_once 'bootstrap/app.php';
$app->make('Illuminate\Contracts\Console\Kernel')->bootstrap();

$user = DB::table('users')->where('email', 'developersayan2002@gmail.com')->first();
if ($user) {
    echo "Logged-in User Email: {$user->email}\n";
    echo "Logged-in User Phone: {$user->phone}\n";
    
    $restaurant = DB::table('restaurant_master')->where('id', $user->restaurant_id)->first();
    if ($restaurant) {
        echo "Restaurant Owner ID: {$restaurant->owner_id}\n";
        $owner = DB::table('users')->where('id', $restaurant->owner_id)->first();
        if ($owner) {
            echo "Owner Name: {$owner->name}\n";
            echo "Owner Email: {$owner->email}\n";
            echo "Owner Phone: {$owner->phone}\n";
        } else {
            echo "Owner user not found!\n";
        }
    } else {
        echo "Restaurant not found!\n";
    }
} else {
    echo "Test user not found!\n";
}
