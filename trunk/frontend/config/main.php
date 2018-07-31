<?php
$params = array_merge(
    require(__DIR__ . '/../../common/config/params.php'),
    require(__DIR__ . '/../../common/config/params-local.php'),
    require(__DIR__ . '/params.php'),
    require(__DIR__ . '/params-local.php')
);

return [
    'id' => 'app-frontend',
    'basePath' => dirname(__DIR__),
    'bootstrap' => ['log'],
    'controllerNamespace' => 'frontend\controllers',
//     'language'=>'zh-CN',
    'modules' => [
            'admin' => [
                    'class' => 'frontend\modules\admin\Module',
            ],
            'abc' => [
                    'class' => 'frontend\modules\abc\test',
            ],
    ],
    'components' => [
        'session' => [
            'class' => 'yii\redis\Session',
            'redis' => [
                'hostname' => '10.21.168.128',
                'port' => 6379,
                'database' => 0,
            ]
        ],
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
                    'password' => '',
                    'charset' => 'utf8',
                    'attributes' => [
                            // use a smaller connection timeout
                            PDO::ATTR_TIMEOUT => 10,
                    ],
            ],
            
            // 配置主服务器组
            'masters' => [
                    ['dsn' => 'mysql:host=localhost;dbname=yii2advanced']
            ],

            // 配置从服务器
            'slaveConfig' => [
                    'username' => 'root',
                    'password' => '',
                    'charset' => 'utf8',
                    'attributes' => [
                            // use a smaller connection timeout
                            PDO::ATTR_TIMEOUT => 10,
                    ],
            ],
            // 配置从服务器组
            'slaves' => [
                    ['dsn' => 'mysql:host=localhost;dbname=yii2advanced2'],
            ],
        ],
        'db2' => [
                'class' => 'yii\db\Connection',
                'dsn' => 'mysql:host=localhost;dbname=yii2advanced',
                'username' => 'root',
                'password' => '',
                'charset' => 'utf8',
        ],
            
//         'db' => [
//             'class' => 'yii\db\Connection',
//             'dsn' => 'mysql:host=localhost;dbname=yii2advanced',
//             'username' => 'root',
//             'password' => '',
//             'charset' => 'utf8',
//         ],
//         'secondDb' => [
//                 'class' => 'yii\db\Connection',
//                 'dsn' => 'mysql:host=localhost;dbname=yii2advanced2',
//                 'username' => 'root',
//                 'password' => '',
//                 'charset' => 'utf8',
//         ]

        /*
        'urlManager' => [
            'enablePrettyUrl' => true,
            'showScriptName' => false,
            'suffix' => '.html',
            'rules' => [
            ],
        ],
        */
    ],
    'params' => $params,
];
