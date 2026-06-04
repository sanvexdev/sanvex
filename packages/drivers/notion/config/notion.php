<?php

return [
    'auth_type' => env('NOTION_AUTH_TYPE', 'api_key'),
    'oauth' => [
        'client_id' => env('NOTION_CLIENT_ID', ''),
        'client_secret' => env('NOTION_CLIENT_SECRET', ''),
        'redirect_uri' => env('NOTION_REDIRECT_URI', env('APP_URL').'/sanvex/notion/callback'),
        'success_redirect' => env('NOTION_SUCCESS_REDIRECT', '/'),
    ],
];
