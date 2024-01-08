<?php
return [
    'driver' => env('FCM_PROTOCOL', 'http'),
    'log_enabled' => false,
    'http' => [
        'sender_id' => env('FCM_SENDER_ID', '501550267254'),
        'server_key' => env('FCM_SERVER_KEY', 'AAAAdMa5u3Y:APA91bHzLFdP1PJJvbfANtgdgmoOaNVruv33sLtBFoVrLy-ePB4miVQqeXZLsVbhc8pa6rdN-L7kieO_M-r-A5OiMsC7jDavCfiBpV8C4CzwBoR4HmjtMYr7WEVXAse72pLRyJym4EU1'),
        'server_send_url' => 'https://fcm.googleapis.com/fcm/send',
        'server_group_url' => 'https://android.googleapis.com/gcm/notification',
        'timeout' => 30.0, // in second
    ],
];