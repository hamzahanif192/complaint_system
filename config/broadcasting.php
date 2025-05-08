<?php

return [

    'default' => env('BROADCAST_DRIVER', 'log'), // ✅ Correct line

    'connections' => [

        'pusher' => [
            'driver' => 'pusher',
            'key' => env('PUSHER_APP_KEY'),
            'secret' => env('PUSHER_APP_SECRET'),
            'app_id' => env('PUSHER_APP_ID'),
            'options' => [
                'cluster' => env('PUSHER_APP_CLUSTER'),
                'useTLS' => true,
            ],
        ],

        'log' => [
            'driver' => 'log', // ✅ So Laravel can log broadcasts
        ],

        'null' => [
            'driver' => 'null',
        ],

    ],

];
