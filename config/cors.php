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
    | For mobile app integration, we need to allow cross-origin requests
    | and support authentication with Sanctum tokens.
    |
    */

    // API paths that need CORS support
    'paths' => [
        'api/*',
        'sanctum/csrf-cookie',
        'login',
        'logout',
        'user',
    ],

    // Allow all HTTP methods for API calls
    'allowed_methods' => [
        'GET',
        'POST',
        'PUT',
        'PATCH',
        'DELETE',
        'OPTIONS',
    ],

    // For development - allow all origins
    // For production, replace with your specific mobile app domains
    'allowed_origins' => [
        'http://localhost:3000',      // React Native/Expo dev
        'http://localhost:19006',     // React Native Web dev
        'http://127.0.0.1:3000',     // Alternative localhost
        'http://127.0.0.1:19006',    // Alternative RN Web
        // Add your mobile app domains here for production:
        // 'https://your-app-domain.com',
        // 'https://api.your-app.com',
    ],

    // For production, you can use patterns to match multiple subdomains
    'allowed_origins_patterns' => [
        // '*.your-domain.com',
        // '*.your-mobile-app.com',
    ],

    // Allow all headers that mobile apps might need
    'allowed_headers' => [
        'Accept',
        'Authorization',
        'Content-Type',
        'X-Requested-With',
        'X-CSRF-TOKEN',
        'X-API-KEY',
        'Origin',
        'Access-Control-Request-Method',
        'Access-Control-Request-Headers',
    ],

    // Expose headers that might be useful for mobile apps
    'exposed_headers' => [
        'Authorization',
        'X-Total-Count',
        'X-Per-Page',
        'X-Current-Page',
    ],

    // Cache preflight requests for 1 hour (3600 seconds)
    'max_age' => 3600,

    // Enable credentials support for Sanctum authentication
    'supports_credentials' => true,

];
