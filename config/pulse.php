<?php

return [
    /*
    |--------------------------------------------------------------------------
    | Route Prefix
    |--------------------------------------------------------------------------
    |
    | This prefix is applied to the Pulse API routes and should match what the
    | Pulse JS client expects when issuing requests to the Laravel backend.
    |
    */
    'prefix' => 'api',

    /*
    |--------------------------------------------------------------------------
    | Route Middleware
    |--------------------------------------------------------------------------
    |
    | Customize the middleware stack protecting the Pulse API routes. By
    | default these routes use the standard "api" middleware group.
    |
    */
    'middleware' => ['api'],

    /*
    |--------------------------------------------------------------------------
    | Endpoint Path
    |--------------------------------------------------------------------------
    |
    | Configure the endpoint within the Pulse route group that accepts incoming
    | requests. The default "pulse" combined with the "api" prefix produces the
    | canonical "/api/pulse" route, but it can be adjusted as needed.
    |
    */
    'endpoint' => 'pulse',
];
