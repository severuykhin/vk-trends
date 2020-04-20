<?php
$params = array_merge(
    require(__DIR__ . '/../../common/config/params.php'),
    require(__DIR__ . '/../../common/config/params-local.php'),
    require(__DIR__ . '/params.php'),
    require(__DIR__ . '/params-local.php')
);

return [
    'id' => 'app-api',
    'basePath' => dirname(__DIR__),
    'bootstrap' => ['log'],
    'controllerNamespace' => 'api\controllers',
    'components' => [
        'log' => [
            'traceLevel' => YII_DEBUG ? 3 : 0,
            'targets' => [
                [
                    'class' => 'yii\log\FileTarget',
                    'levels' => ['error', 'warning'],
                ],
            ],
        ],
        'urlManager' => [
            'enablePrettyUrl' => true,
            'enableStrictParsing' => true,
            'showScriptName' => false,
            'rules' => [
                '' => 'site/index',
                '/group/summary' => 'group/summary',
                '/api/group/summary' => 'group/summary',
                
                '/group/vportrait' => 'group/vportrait',
                '/api/group/vportrait' => 'group/vportrait',

                '/group/postsvportrait' => 'group/posts-portrait',
                '/api/group/postsvportrait' => 'group/posts-portrait',

                '/group/top-commentators' => 'group/top-commentators',
                '/api/group/top-commentators' => 'group/top-commentators',

                '/trends/search' => 'trends/search',
                '/api/trends/search' => 'trends/search'
            ]
        ],
        'errorHandler' => [
            'errorAction' => 'site/error',
        ],
    ],
    'params' => $params,
];
