<?php

return [
    /*
    |--------------------------------------------------------------------------
    | Key Encryption Key (KEK)
    |--------------------------------------------------------------------------
    |
    | Used by the Sanvex EncryptionService to encrypt Data Encryption Keys (DEKs).
    | Should be base64 encoded and 32 bytes long (e.g. from `php artisan key:generate`).
    |
    */
    'kek' => env('SANVEX_KEK', env('APP_KEY')),

    /*
    |--------------------------------------------------------------------------
    | Registered Drivers
    |--------------------------------------------------------------------------
    |
    | Define the list of driver classes that should be auto-registered with
    | Sanvex. Custom drivers can be added to this array.
    |
    */
    'drivers' => [
        // \Sanvex\Core\Drivers\ExampleDriver::class
    ],

    /*
    |--------------------------------------------------------------------------
    | Permissions Configuration
    |--------------------------------------------------------------------------
    |
    | Configuration for the Sanvex PermissionGuard module.
    |
    */
    'permissions' => [
        'approval_url' => env('SANVEX_APPROVAL_URL', '/sanvex/approve'),
    ],

    /*
    |--------------------------------------------------------------------------
    | AI MCP Configuration
    |--------------------------------------------------------------------------
    */
    'mcp' => [
        'enable_server' => env('SANVEX_MCP_ENABLE_SERVER', false),
        'allow_run_script' => env('SANVEX_MCP_ALLOW_RUN_SCRIPT', false),
    ],
];
