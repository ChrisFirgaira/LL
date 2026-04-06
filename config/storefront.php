<?php

return [
    'announcement' => env('STOREFRONT_ANNOUNCEMENT', 'Free shipping on orders over $150'),

    'contact' => [
        'to_address' => env('CONTACT_FORM_TO_ADDRESS', env('MAIL_FROM_ADDRESS', 'hello@example.com')),
        'to_name' => env('CONTACT_FORM_TO_NAME', env('APP_NAME', 'Lunar Store')),
        'display_email' => env('STOREFRONT_CONTACT_EMAIL', env('CONTACT_FORM_TO_ADDRESS', env('MAIL_FROM_ADDRESS', 'hello@example.com'))),
        'display_phone' => env('STOREFRONT_CONTACT_PHONE'),
    ],

    'shipping' => [
        'standard_rate' => (int) env('STOREFRONT_STANDARD_SHIPPING_RATE', 995),
        'express_rate' => (int) env('STOREFRONT_EXPRESS_SHIPPING_RATE', 1995),
        'free_over' => (int) env('STOREFRONT_FREE_SHIPPING_OVER', 15000),
    ],
];
