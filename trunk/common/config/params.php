<?php
return [
    'adminEmail' => 'admin@example.com',
    'supportEmail' => 'support@example.com',
    'user.passwordResetTokenExpire' => 3600,
        
    'apiConfig' => [
            'host'   =>'120.55.166.147',
            'clientID' =>'admin',
            'clientKey' =>'26f2fdd378dfr68k9m536323t3cfs542'
    ],
    //公共目录路径
    'public_path' => str_replace('\\', '/', dirname(dirname(dirname(dirname(__FILE__))))),
    //图片服务器域名
    'imgServerDomin' => 'http://devimg.lexuetao.com',
    'imgUrl'         => 'devimg.lexuetao.com',
    //'imgServerCreateActivityImgPath'  => str_replace('\\', '/', dirname(dirname(dirname(dirname(__FILE__))))) . '/lexuetao-image/upload/activity/create/description/',
];
