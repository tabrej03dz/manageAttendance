<?php
return [
    'fcm_enabled' => env('CHAT_FCM_ENABLED', false),
    'project_id' => env('CHAT_FIREBASE_PROJECT_ID'),
    'credentials' => env('CHAT_FIREBASE_CREDENTIALS', storage_path('app/firebase/service-account.json')),
];
