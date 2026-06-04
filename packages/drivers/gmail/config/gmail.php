<?php

return [
    'oauth' => [
        'client_id' => env('GMAIL_CLIENT_ID', ''),
        'client_secret' => env('GMAIL_CLIENT_SECRET', ''),
        'redirect_uri' => env('GMAIL_REDIRECT_URI', env('APP_URL').'/sanvex/gmail/callback'),
        'success_redirect' => env('GMAIL_SUCCESS_REDIRECT', '/'),
    ],
];
