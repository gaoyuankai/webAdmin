<?php
namespace backend\components;

use \Yii;
/**
 * 数据库操作及权限
 * 存放静态方法及数据
 */
class AdminConfig
{
    const AUTHORITY_ADMIN          = 10;                        //管理员权限 - admin
    const AUTHORITY_DELETE         = 8;                         //管理员权限 - 增删改查
    const AUTHORITY_INSERT         = 7;                         //管理员权限 - 增改查
    const AUTHORITY_UPDATE         = 6;                         //管理员权限 - 改查
    const AUTHORITY_SELECT         = 4;                         //管理员权限 - 查
    const AUTHORITY_NORMAL         = 1;                         //管理员权限 - 最低
    const ADMIN_STATUS_NORMAL      = 1;                         //管理员状态 - 正常
    const ADMIN_STATUS_FORBIT      = 0;                         //管理员状态 - 禁用
    const Default_Page_Limit       = 20;
    
    //const ADMIN_STATUS             = ['0' => '禁用','1' => '正常'];
    const ADMIN_ROLE               = [
                                          self::AUTHORITY_ADMIN  => '超级管理员',
                                          self::AUTHORITY_DELETE => '增删改查',
                                          self::AUTHORITY_INSERT => '增改查',
                                          self::AUTHORITY_UPDATE => '改查',
                                          self::AUTHORITY_SELECT => '查',
                                          self::AUTHORITY_NORMAL => '用户',
                                     ];
    const ADMIN_STATUS             = [
                                          self::ADMIN_STATUS_NORMAL => '正常',
                                          self::ADMIN_STATUS_FORBIT => '禁用',
                                     ];

    public static function checkIsAdmin()
    {
        return Yii::$app->user->identity->role >= AdminConfig::AUTHORITY_ADMIN;
    }
}