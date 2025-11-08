<?php
/**
 * -------------------------------------------------------------
 * Application Global Settings
 * -------------------------------------------------------------
 * 
 * This configuration file defines core runtime settings used
 * across the entire application.
 * 
 * - Maintenance page configuration
 * - Session lifetime and timeout limits
 * - CORS allowed origins (for API and cross-domain requests)
 * - Request rate limiting
 * 
 * The `allowed-origin` key is used by the CorsMiddleware to
 * determine which domains are authorized to access the API.
 * 
 * @package config
 * @version 1.0.0
 * @author   dev-iadicola
 */

return [

    /**
     * Maintenance page configuration
     * 
     * Use this section when your app is in maintenance mode.
     * To change the state, update the `MAINTENANCE` variable 
     * in your private `.env` file.   
     */
    "pages" => [
        "MAINTENANCE" => "coming-soon",
    ],

    /**
     * Session Lifetime and Timeout.
     * 
     * To increase or decrease the session duration, modify 
     * the corresponding environment variables in your `.env` file:
     * 
     * - SESSION_LIFETIME
     * - SESSION_LIFETIME_AUTH
     * - TIMEOUT_SESSION
     *
     * `lifetime` defines the maximum session validity (cookie and file),
     * while `timeout` defines the user inactivity limit.
     * 
     * These parameters are primarily handled by the 
     * AuthMiddleware and CsrfMiddleware.
     */
    "session" => [
        'lifetime'       =>  env('SESSION_LIFETIME', 3600),       // 1 hour
        'auth-lifetime'  =>  env('SESSION_LIFETIME_AUTH', 3600),  // 1 hour
        'timeout'        =>  env('TIMEOUT_SESSION', 900),         // 15 minutes
    ],

    /**
     * CORS (Cross-Origin Resource Sharing) settings.
     * 
     * Define which origins are allowed to access your API endpoints.
     * Used by the CorsMiddleware to validate incoming requests.
     */
    'allowed-origin' => [
        'http://localhost:3000',
        'http://localhost:*',
    ],

    /**
     * Request rate limiting.
     * 
     * Controls how many requests a client can make within 
     * a specific time window.
     * 
     * Example:
     * - max: 100 requests
     * - window: 60 seconds
     */
    'request' => [
        'max'    =>  env('MAX_REQUEST', 100),
        'window' =>  env('WINDOW', 60),
    ],
];
