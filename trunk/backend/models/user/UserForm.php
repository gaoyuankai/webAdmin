<?php
namespace backend\models\user;

use yii\base\Model;
use Yii;
use common\models\GaBaseClient;
use yii\helpers\ArrayHelper;

/**
 * 用户表
 */
class UserForm extends Model
{
    public $username;        //用户名
    public $areas;           //地区缓存
    public $nick;            //昵称
    public $phone;           //电话
    public $email;           //email
    public $area;            //地区
    public $phoneStauts="";     //手机状态-0表示无手机-1表示有手机
    public static $RAGIN = ["208"=>"请选择","209"=>"黄浦区","210"=>"卢湾区",
                                            "211"=>"徐汇区","212"=>"长宁区","213"=>"静安区",
                                            "214"=>"普陀区","215"=>"闸北区","216"=>"虹口区",
                                            "217"=>"杨浦区","218"=>"宝山区","219"=>"闵行区",
                                            "220"=>"嘉定区","221"=>"松江区","222"=>"金山区",
                                            "223"=>"青浦区","224"=>"南汇区","225"=>"奉贤区",
                                            "226"=>"浦东新区","227"=>"崇明县","228"=>"其他",
                                            ];
    public static $sex         = ['未知','男','女'];

    /**
     * @inheritdoc
     */
    public function rules()
    {
        return [
            [['phone','nick','email','phoneStauts'], 'safe'],
        ];
    }
    
    public function attributeLabels()
    {
        return [
                'email'       => 'email',
                'phoneStauts' => "手机状态",
                'nick'        => '昵称',
                'phone'       => '手机',
        ];
    }

    /**
     * @return User|null the saved model or null if saving fails
     */
    public function select($page, $size)
    {
        $where = array();
        if (!empty($this->email)) {
              $where['email'] = $this->email;
        }
        if (!empty($this->phone)) {
            $where['phone'] = $this->phone;
        }
        if (!empty($this->nick)) {
            $where['nickName'] = $this->nick;
        }
        $ret = GaBaseClient::getInstance()->getUserList([
            'where'  => $where,
            'order'  => ['id' => 'desc'],
            'offset' => ($page - 1) * $size,
            'limit'  => $size
        ]);
        //选择有手机号的
        if($this->phoneStauts === '1'){
           foreach ($ret['data']['list'] as $k=>$v) {
               if(!$v['phone']){
                   unset($ret['data']['list'][$k]);
               }
           }
        }
        //选择无手机号的
        if($this->phoneStauts === '0'){
            foreach ($ret['data']['list'] as $k=>$v) {
                if($v['phone']){
                    unset($ret['data']['list'][$k]);
                }
            }
        }
        if ($ret['status']) {
            foreach ($ret['data']['list'] as $k=>$user){
                    if (isset(self::$RAGIN[$user['Region_districtId']])) {
                        $ret['data']['list'][$k]['region'] = self::$RAGIN[$user['Region_districtId']];
                    }
                    $ret['data']['list'][$k]['sex'] = self::$sex[$ret['data']['list'][$k]['sex']];
            }
            //设置区县
            /* if (isset(self::$RAGIN[$ret['data']['info']['Region_districtId']])) {
                $ret['data']['info']['region'] = self::$RAGIN[$ret['data']['info']['Region_districtId']];
            }
            $ret['data']['info']['sex'] = self::$sex[$ret['data']['info']['sex']]; */
            return $ret['data'];
        }
    }
}
