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

    'postmark' => [
        'token' => env('POSTMARK_TOKEN'),
    ],

    'ses' => [
        'key' => env('AWS_ACCESS_KEY_ID'),
        'secret' => env('AWS_SECRET_ACCESS_KEY'),
        'region' => env('AWS_DEFAULT_REGION', 'us-east-1'),
    ],

    'resend' => [
        'key' => env('RESEND_KEY'),
    ],

    'slack' => [
        'notifications' => [
            'bot_user_oauth_token' => env('SLACK_BOT_USER_OAUTH_TOKEN'),
            'channel' => env('SLACK_BOT_USER_DEFAULT_CHANNEL'),
        ],
    ],

    'lead' => [
        'sales_email' => env('SALES_EMAIL', env('ADMIN_EMAIL')),
        'google_sheet_webhook' => env('GOOGLE_SHEET_LEAD_WEBHOOK'),
        'recaptcha_site_key' => env('RECAPTCHA_SITE_KEY'),
        'recaptcha_secret_key' => env('RECAPTCHA_SECRET_KEY'),
        'recaptcha_min_score' => (float) env('RECAPTCHA_MIN_SCORE', 0.5),
    ],

    'odoo' => [
        'api_key' => env('ODOO_API_KEY'),
        'webhook_url' => env('ODOO_WEBHOOK_URL'),
    ],

];
