<?php

return [

    /*
    |--------------------------------------------------------------------------
    | Third Party Services
    |--------------------------------------------------------------------------
    |
    | This file is for storing the credentials for third party services such
    | as Mailgun, Postmark, AWS and more. This file provides the de facto
    | location for this type of information, allowing packages to have
    | a conventional file to locate the various service credentials.
    |
    */

    'mailgun' => [
        'domain' => env('MAILGUN_DOMAIN'),
        'secret' => env('MAILGUN_SECRET'),
        'endpoint' => env('MAILGUN_ENDPOINT', 'api.mailgun.net'),
        'scheme' => 'https',
    ],

    'postmark' => [
        'token' => env('POSTMARK_TOKEN'),
    ],

    'ses' => [
        'key' => env('AWS_ACCESS_KEY_ID'),
        'secret' => env('AWS_SECRET_ACCESS_KEY'),
        'region' => env('AWS_DEFAULT_REGION', 'us-east-1'),
    ],

    'ai' => [
        'url' => env('AI_SERVICE_URL', 'http://localhost:8001'),
        'api_key' => env('AI_SERVICE_API_KEY'),
    ],

    'blockchain' => [
        'network' => env('BLOCKCHAIN_NETWORK', 'ethereum'),
        'rpc_url' => env('BLOCKCHAIN_RPC_URL'),
        'contract_address' => env('BLOCKCHAIN_CONTRACT_ADDRESS'),
        'private_key' => env('BLOCKCHAIN_PRIVATE_KEY'),
    ],

    'ipfs' => [
        'gateway' => env('IPFS_GATEWAY', 'https://ipfs.io'),
        'api_url' => env('IPFS_API_URL'),
        'api_key' => env('IPFS_API_KEY'),
    ],

]; 