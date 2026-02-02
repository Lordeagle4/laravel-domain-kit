<?php

declare(strict_types=1);

return [

    /*
    |--------------------------------------------------------------------------
    | Domain Generation Defaults
    |--------------------------------------------------------------------------
    |
    | Determines which components are generated when running:
    | php artisan make:domain Orders
    |
    */

    'generate' => [
        'events' => true,
        'listeners' => true,
        'jobs' => true,
        'actions' => true,
        'controllers' => true,
        'models' => true,
        'policies' => false,
    ],

    /*
    |--------------------------------------------------------------------------
    | Auto Registration
    |--------------------------------------------------------------------------
    */

    'auto_register_events' => false,

    /*
    |--------------------------------------------------------------------------
    | AI / MCP Metadata
    |--------------------------------------------------------------------------
    */

    'ai_metadata' => true,
];
