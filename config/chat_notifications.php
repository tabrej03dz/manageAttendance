<?php
// return [
//     'fcm_enabled' => env('CHAT_FCM_ENABLED', false),
//     'project_id' => env('CHAT_FIREBASE_PROJECT_ID'),
//     'credentials' => env('CHAT_FIREBASE_CREDENTIALS', storage_path('app/firebase/service-account.json')),
// ];


return [
    'fcm_enabled' => env('CHAT_FCM_ENABLED', false),

    'project_id' => env('CHAT_FIREBASE_PROJECT_ID'),

    'credentials' => env(
        'CHAT_FIREBASE_CREDENTIALS',
        storage_path('app/firebase/service-account.json')
    ),

    'web' => [
        'api_key' => env('FIREBASE_WEB_API_KEY'),
        'auth_domain' => env('FIREBASE_WEB_AUTH_DOMAIN'),
        'project_id' => env('FIREBASE_WEB_PROJECT_ID'),
        'storage_bucket' => env('FIREBASE_WEB_STORAGE_BUCKET'),
        'messaging_sender_id' => env('FIREBASE_WEB_MESSAGING_SENDER_ID'),
        'app_id' => env('FIREBASE_WEB_APP_ID'),
        'vapid_key' => env('FIREBASE_WEB_VAPID_KEY'),
    ],
];
