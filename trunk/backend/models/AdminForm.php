<?php
namespace backend\models;

use Yii;
use common\models\User;
use yii\base\DynamicModel;
use yii\web\IdentityInterface;
use yii\base\Model;
use backend\components\AdminConfig;
use yii\helpers\ArrayHelper;
use backend\components\Tool;

class AdminForm extends Model
{
    public $status;                //管理员状态 0 ： 禁用  1：使用中
    public $role;                  //管理员权限
    public $id;                    //id
    public $username;              //管理员用户名
    public $password;              //创建时输入的密码
    public $repassword;            //创建时第二次密码
    
    public function rules()
    {
        return [
            ['username', 'filter', 'filter' => 'trim', 'on' => ['add', 'update']],
            ['username', 'required', 'on' => ['add', 'update']],
            [
                'username', 
                'unique', 
                'targetClass' => '\common\models\User', 
                'message' => 'This username has already been taken.', 
                'on' => ['add']
            ],
            ['username', 'string', 'length' => [1, 20], 'on' => ['add', 'update']],
            ['repassword', 'required', 'on' => ['add','changepw']],
            ['repassword','compare','compareAttribute'=>'password','message' => Yii::t('yii', 'repasswordCompare'), 'on' => ['add','changepw']],
            ['password', 'required', 'on' => ['add','changepw']],
            ['password', 'string', 'min' => 6, 'on' => ['add','changepw']],
            ['role', 'default', 'value' => AdminConfig::AUTHORITY_NORMAL, 'on' => ['add', 'update']],
            ['status', 'default', 'value' => AdminConfig::ADMIN_STATUS_NORMAL, 'on' => ['add', 'update']],
            ['role', 'in', 'range' => array_keys(AdminConfig::ADMIN_ROLE), 'on' => ['add', 'update']],
            ['status', 'in', 'range' => array_keys(AdminConfig::ADMIN_STATUS), 'on' => ['add', 'update']],
        ];
    }
    
    public function scenarios()
    {
        return [
            'add'      => ['username', 'repassword','password' , 'role', 'status'],
            'update'   => ['username', 'password' , 'role', 'status', 'id'],
            'select'   => ['username', 'status', 'role'],
            'changepw' => ['repassword','password'],
        ];
    }
    
    public function attributeLabels()
    {
        return [
            'username'       => '用户名',
            'repassword'     => '确认密码',
            'password'       => '设置密码',
            'role'           => '管理员权限',
            'status'         => '管理员状态',
            'id'             => '',
        ];
    }
    
    public function changepw()
    {
        if ($this->validate()) {
            $id = \Yii::$app->user->id;
            $user = User::findIdentity($id);
            $newPass = Yii::$app->getSecurity()->generatePasswordHash($this->password);
            $user->setPassword($this->password);
            return $user->save();
        }
        return false;
    }
    
    public function update()
    {
        if ($this->validate()) {
            $user = User::findOne($this->id);
            if($user) {
                $user->status = $this->status;
                $user->role = $this->role;
                $user->save();
                return ['code' => 1, 'msg' => '修改成功'];
            }
            return ['code' => 0, 'msg' => '数据有误'];
        }
        return ['code' => 234, 'msg' => Tool::echoError($this)];
    }
    
    public function add()
    {
        if ($this->validate()) {
            $user = new User();
            $user->username = $this->username;
            $user->role     = $this->role;
            $user->status   = $this->status;
            $user->setPassword($this->password);
            $user->generateAuthKey();
            if ($user->save()) {
                return ['code' => 1, 'msg' => '创建成功'];
            }
            return ['code' => 0, 'msg' => '数据有误'];
        }
        return ['code' => 0, 'msg' => Tool::echoError($this)];
    }
    
    public function select($page,$size)
    {
        $admins = User::find()->limit($size)->offset($page-1)->orderBy('role')->asArray()->all();
        $count  = User::find()->count();
        return ['list' => $admins, 'count' => $count];
    }
    
}