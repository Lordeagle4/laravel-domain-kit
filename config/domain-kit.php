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
        'events' => false,
        'listeners' => false,
        'jobs' => false,
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
    | Controller Action Generation
    |--------------------------------------------------------------------------
    |
    | When using make:domain:controller with --action, choose how action
    | classes are organized:
    | - flat:   App\Domains\Users\Actions\CreateUser
    | - nested: App\Domains\Users\Actions\User\Create
    |
    */
    'controller_actions' => [
        'style' => 'flat',
    ],

    /*
    |--------------------------------------------------------------------------
    | AI / MCP Metadata
    |--------------------------------------------------------------------------
    */

    'ai_metadata' => true,
];
