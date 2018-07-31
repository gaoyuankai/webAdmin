<?php
namespace  backend\models\lucky;

use yii\base\Model;
use Yii;
use backend\components\Tool;
use common\models\GaBaseClient;
use backend\models\message\MessageForm;

/**
 * 红包管理model
 */
class LuckyForm extends Model
{
    public $usernames;               //用户组
    public $message;                 //信息
    public $style = array();         //0: 短信，1：站内信
    public $lucky;                   //红包id
    public $lucky_config = array();  //红包配置数据缓存
    public $title;                   //标题
    public $data;                    //用户数据
    
    public function rules()
    {
        return [
            ['message', 'filter', 'filter' => 'trim'],
            [['style','lucky_config', 'data', 'title'], 'safe'],
            [['usernames','lucky'], 'required'],
            ['message', 'string','skipOnEmpty' => true, 'length' => [1, 255]],
        ];
    }
    
    public function attributeLabels()
    {
        return [
            'usernames'     => '发送对象',
            'message'       => '发送内容',
            'style'         => '发送类型',
            'lucky'         => '红包',
            'lucky_config'  => '',
            'data'          => '',
            'title'         => '标题',
        ];
    }
    
    public function sendlucky(){
        if ($this->validate()) {
            $usernames = explode(",", $this->usernames);
            if (!is_array($usernames) || count($usernames) <= 0) {
                return ['code' => 0, 'msg' => '请选择用户'];
            }
            if (!isset($this->lucky_config[$this->lucky])) {
                return ['code' => 0, 'msg' => '没有所选择的的红包'];
            }
            $lucky_config = $this->lucky_config[$this->lucky];
            if ($lucky_config['totalQty'] - $lucky_config['sendOutQty'] < count($usernames)) {
                return ['code' => 0, 'msg' => '所选的用户数量大于红包库存'];
            }
            $lucky_uids = [];
            foreach($usernames as $username){
                if (!isset($this->data[$username])) {
                    return ['code' => 0, 'msg' => '数据错误请重新选择'];
                }
                $lucky_uids[] = $this->data[$username]['id'];
            }
            $ret = GaBaseClient::getInstance()->distributeCoupon([
                'userIds'  => $lucky_uids,
                'couponId' => $this->lucky
            ]);
            if (!$ret['status']) {
                return ['code' => 0, 'msg' => '红包发送失败'];
            }
            if ($ret['data']['count'] != count($usernames)) {
                return ['code' => 0, 'msg' => '有部分用户未收到红包'];
            }
            //发送消息
            if (is_array($this->style) && count($this->style) > 0) {
                $message = new MessageForm();
                $message->usernames = $this->usernames;
                $message->message   = $this->message;
                $message->title     = $this->title;
                $message->data      = $this->data;
                $message->style     = $this->style;
                return $message->message();
            } else {
                return ['code' => 1, 'msg' => '发送成功'];
            }
        }
        return Tool::echoError($this);
    }
}
