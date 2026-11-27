<?php

return [

    /*
    |--------------------------------------------------------------------------
    | Third Party Services
    |--------------------------------------------------------------------------
    |
    | This file is for storing the credentials for third party services such
    | as Mailgun, Postmark, AWS and more. This file provides the de facto
    | location for this type of information, allowing packages to have
    | a conventional file to locate the various service credentials.
    |
    */

    'mailgun' => [
        'domain' => env('MAILGUN_DOMAIN'),
        'secret' => env('MAILGUN_SECRET'),
        'endpoint' => env('MAILGUN_ENDPOINT', 'api.mailgun.net'),
        'scheme' => 'https',
    ],

    'postmark' => [
        'token' => env('POSTMARK_TOKEN'),
    ],

    'ses' => [
        'key' => env('AWS_ACCESS_KEY_ID'),
        'secret' => env('AWS_SECRET_ACCESS_KEY'),
        'region' => env('AWS_DEFAULT_REGION', 'us-east-1'),
    ],

    'razorpay' => [
        'key_id' => env('RAZORPAY_KEY_ID'),
        'key_secret' => env('RAZORPAY_KEY_SECRET'),
        'webhook_secret' => env('RAZORPAY_WEBHOOK_SECRET'),
    ],

    'firebase' => [
        'api_key'               => env('FIREBASE_API_KEY'),
        'auth_domain'           => env('FIREBASE_AUTH_DOMAIN'),
        'project_id'            => env('FIREBASE_PROJECT_ID'),
        'storage_bucket'        => env('FIREBASE_STORAGE_BUCKET'),
        'messaging_sender_id'   => env('FIREBASE_MESSAGING_SENDER_ID'),
        'app_id'                => env('FIREBASE_APP_ID'),
        'vapid_key'             => env('FIREBASE_VAPID_KEY'),
        'service_account_path'  => env('FIREBASE_CREDENTIALS', 'storage/firebase/service-account.json'),
    ],

    'whatsapp' => [
        'verify_token' => env('WHATSAPP_VERIFY_TOKEN', 'billbite_wa_verify_token_2026'),
        'app_secret'   => env('META_APP_SECRET'),
    ],

];
