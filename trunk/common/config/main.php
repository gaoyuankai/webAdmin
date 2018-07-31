<?php
return [
        'language'   => 'zh-CN',
        'charset'    => 'UTF-8',
        'vendorPath' => dirname(dirname(__DIR__)) . '/vendor',
        'components' => [
                'cache' => [
                        'class' => 'yii\caching\FileCache'
                ],
                // 'session' => [
                // 'class' => 'yii\redis\Session',
                // 'redis' => [
                // 'hostname' => '10.21.168.128',
                // 'port' => 6379,
                // 'database' => 0
                // ]
                // ],
                'redis' => [
                    'class' => 'yii\redis\Connection',
                    'hostname' => '120.55.166.21',
                    'password' => 'Goldapple*dev500',
                    'port' => 6379,
                    'database' => 0
                ],
                'mongodb' => [
                    'class' => '\yii\mongodb\Connection',
                    'dsn' => 'mongodb://10.21.168.128:27017/logger'
                ]
        ]
];
