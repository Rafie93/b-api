<?php

return [
    'driver' => env('FCM_PROTOCOL', 'http'),
    'log_enabled' => true,

    'http' => [
        'server_key' => env('FCM_SERVER_KEY', 'AAAAJoMcb3g:APA91bFb0WDN7oOS2wGctiXzZqBwa5choFQKPfqP6FvzwtmHZeSDq2ayqFRPD8PAlknewJtNE6ZHXh2txzHnoawz7_JPdNJ_38iQJNNWq3DahHCx8hv-fkMnWrFM5GqYQU6zPd7OVRUw'),
        'sender_id' => env('FCM_SENDER_ID', '165408436088'),
        'server_send_url' => 'https://fcm.googleapis.com/fcm/send',
        'server_group_url' => 'https://android.googleapis.com/gcm/notification',
        'timeout' => 30.0, // in second
    ],
];
