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
    'prefix' => 'pulse',

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

];
