<?php
$params = array_merge(
    require(__DIR__ . '/../../common/config/params.php'),
    require(__DIR__ . '/../../common/config/params-local.php'),
    require(__DIR__ . '/params.php'),
    require(__DIR__ . '/params-local.php')
);

return [
    'id' => 'app-backend',
    'basePath' => dirname(__DIR__),
    'controllerNamespace' => 'backend\controllers',
    'bootstrap' => ['log'],
    'components' => [
        'user' => [
            'identityClass' => 'common\models\User',
            'enableAutoLogin' => true,
        ],
        'log' => [
            'traceLevel' => YII_DEBUG ? 3 : 0,
            'targets' => [
                [
                    'class' => 'yii\log\FileTarget',
                    'levels' => ['error', 'warning'],
                ],
            ],
        ],
        'errorHandler' => [
            'errorAction' => 'site/error',
        ],
        'db' => [
            'class' => 'yii\db\Connection',
            
            // 配置主服务器
            'masterConfig' => [
                    'username' => 'root',
                    'password' => 'Goldapple*dev500',
                    'charset' => 'utf8',
                    'attributes' => [
                            // use a smaller connection timeout
                            PDO::ATTR_TIMEOUT => 10,
                    ],
            ],
            
            // 配置主服务器组
            'masters' => [
                    ['dsn' => 'mysql:host=120.55.166.21;dbname=lexuetao']
            ],

            // 配置从服务器
            'slaveConfig' => [
                    'username' => 'root',
                    'password' => 'Goldapple*dev500',
                    'charset' => 'utf8',
                    'attributes' => [
                            // use a smaller connection timeout
                            PDO::ATTR_TIMEOUT => 10,
                    ],
            ],
            // 配置从服务器组
            'slaves' => [
                    ['dsn' => 'mysql:host=120.55.166.21;dbname=lexuetao'],
            ],
        ],

    ],
    'params' => $params,
];
