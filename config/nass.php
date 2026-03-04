<?php

return [

    /*
    |--------------------------------------------------------------------------
    | Nass Merchant Credentials
    |--------------------------------------------------------------------------
    |
    | Your Nass merchant username and password used to authenticate
    | with the Nass Payment Gateway API.
    |
    */

    'username' => env('NASS_USERNAME', ''),

    'password' => env('NASS_PASSWORD', ''),

    /*
    |--------------------------------------------------------------------------
    | Nass API Base URL
    |--------------------------------------------------------------------------
    |
    | The base URL for the Nass Payment Gateway API. Switch between
    | UAT and production environments using environment variables.
    |
    | UAT:        https://uat-gateway.nass.iq:9746
    | Production: https://gateway.nass.iq:9746
    |
    */

    'base_url' => env('NASS_BASE_URL', 'https://gateway.nass.iq:9746'),

    /*
    |--------------------------------------------------------------------------
    | HTTP Client Timeout
    |--------------------------------------------------------------------------
    |
    | The timeout in seconds for HTTP requests to the Nass API.
    |
    */

    'timeout' => env('NASS_TIMEOUT', 30),

];
