<?php

// The public site (WEB_URL) calls the API from the browser for forms; everything else is same-origin.
return [
    'paths' => ['api/*'],
    'allowed_methods' => ['GET', 'POST', 'OPTIONS'],
    'allowed_origins' => array_values(array_filter([env('WEB_URL'), env('APP_URL'), 'http://localhost:3000', 'http://127.0.0.1:3011'])),
    'allowed_origins_patterns' => [],
    'allowed_headers' => ['*'],
    'exposed_headers' => [],
    'max_age' => 3600,
    'supports_credentials' => false,
];
