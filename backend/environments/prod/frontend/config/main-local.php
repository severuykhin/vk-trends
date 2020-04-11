<?php

$config = [
    'components' => [
        'request' => [
            // !!! insert a secret key in the following (if it is empty) - this is required by cookie validation
            'cookieValidationKey' => '',
            'baseUrl' => '',
            'csrfParam' => '_csrf',
            'csrfCookie' => [
                'httpOnly' => true,
                'path' => '',
            ],
        ],
        'assetManager' => [
            'bundles' => false
        ],
    ],
];

return $config;
