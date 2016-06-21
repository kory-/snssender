<?php

/**
 * snssender config
 */

return [
    'region' => env('AWS_SNS_REGION'),
    'version' => env('AWS_SNS_REGION', 'latest'),
    'credentials' => [
        'key' => env('AWS_SNS_ACCESSKEY'),
        'secret' => env('AWS_SNS_SECRETKEY')
    ]
];
