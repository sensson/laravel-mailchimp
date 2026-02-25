<?php

return [
    'oauth' => [
        'client_id' => env('MAILCHIMP_CLIENT_ID'),
        'client_secret' => env('MAILCHIMP_CLIENT_SECRET'),
        'redirect_uri' => env('MAILCHIMP_REDIRECT_URI'),
    ],
];
