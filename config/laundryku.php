<?php

return [
    'currency' => env('APP_CURRENCY', 'IDR'),

    'delivery_fee' => (int) env('DELIVERY_FEE', 15000),

    'price_approval_threshold_percent' => (int) env(
        'PRICE_CHANGE_APPROVAL_THRESHOLD',
        10,
    ),

    'wa_base_url' => env('WA_BASE_URL', 'https://wa.me'),

    'uploads' => [
        'disk' => env('UPLOAD_DISK', 'local'),
        'max_kilobytes' => (int) env('UPLOAD_MAX_KB', 5120),
        'mimes' => ['jpg', 'jpeg', 'png', 'webp', 'pdf'],
    ],
];
