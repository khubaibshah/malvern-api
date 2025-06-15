<?php

return [

    /*
    |--------------------------------------------------------------------------
    | Cross-Origin Resource Sharing (CORS) Configuration
    |--------------------------------------------------------------------------
    |
    | Here you may configure your settings for cross-origin resource sharing
    | or "CORS". This determines what cross-origin operations may execute
    | in web browsers. You are free to adjust these settings as needed.
    |
    | To learn more: https://developer.mozilla.org/en-US/docs/Web/HTTP/CORS
    |
    */

    'paths' => ['api/*', 'sanctum/csrf-cookie', 'api/login', 'api/logout', 'api/register', 'admin/*', 'scs/*'],

    'allowed_methods' => ['*'],

    'allowed_origins' => ['http://localhost:5173',
    'http://localhost:*',
    'http://localhost:4002',
     'http://localhost:5174',
     'https://malvernadmin.netlify.app', 
    'https://scsltd.netlify.app'],

    'allowed_origins_patterns' => [],

    'allowed_headers' => ['*'],

    'exposed_headers' => [],

    'max_age' => 0,

    'supports_credentials' => true,

];
