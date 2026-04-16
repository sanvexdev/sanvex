<?php

return [
    'multi_tenancy' => false,
    'drivers' => [],
    'kek' => env('SANVEX_KEK'),
    'queue' => env('SANVEX_QUEUE', false),
    'log_channel' => env('SANVEX_LOG_CHANNEL', 'default'),
    'webhook_path' => env('SANVEX_WEBHOOK_PATH', '/api/webhooks'),
    'permissions' => [
        'default_mode' => 'cautious',
        'approval_url' => null,
        'approval_ttl' => 3600,
    ],
    'retry' => [
        'times' => 3,
        'sleep_ms' => 1000,
    ],

    'mcp' => [
        /*
         * Allow the RunScriptTool to execute arbitrary PHP expressions.
         * MUST remain false in production. Only enable in trusted, isolated environments.
         */
        'allow_run_script' => env('SANVEX_MCP_ALLOW_RUN_SCRIPT', false),
    ],
];
