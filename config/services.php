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

    'youdao' => [
        'appid' => env('YOUDAO_APPID', ''),
        'key' => env('YOUDAO_KEY', ''),
    ],

    'baidu' => [
        'appid' => env('BAIDU_APPID', ''),
        'key' => env('BAIDU_KEY', ''),
    ],
];
