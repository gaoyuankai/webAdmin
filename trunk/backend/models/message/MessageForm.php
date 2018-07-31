<?php
namespace  backend\models\message;

use yii\base\Model;
use Yii;
use yii\helpers\ArrayHelper;
use common\models\GaBaseClient;

/**
 * 消息发送model
 */
class MessageForm extends Model
{
    public $usernames;            //用户组
    public $message;              //信息
    public $style = array();      //0: 短信，1：站内信
    public $title;                //标题
    public $data;                 //用户数据
    
    public function rules()
    {
        return [
            [['message','usernames'], 'filter', 'filter' => 'trim'],
            [['style', 'usernames', 'data'], 'safe'],
            [['message','usernames', 'title'], 'required'],
            ['message', 'string', 'length' => [1, 200],],
            ['title', 'string', 'length' => [1, 30],],
        ];
    }
    
    public function attributeLabels()
    {
        return [
            'usernames' => '发送对象',
            'message'   => '发送内容',
            'style'     => '发送类型',
            'title'     => '标题',
            'data'      => '',
        ];
    }

    
    public function setData($data)
    {
        if (is_array($data)) {
            if(isset($data[0]['nickName'])){
                $this->data = ArrayHelper::index($data, 'nickName');
                $this->usernames = implode(',',ArrayHelper::getColumn($data, 'nickName'));
            } else if(isset($data[0]['User_id'])){
                $this->data = ArrayHelper::index($data, 'User_id');
                $this->usernames = implode(',',ArrayHelper::getColumn($data, 'User_id'));
            }
        } else if ($data == 'all') {
            $this->usernames = $data;
        }
    }
    
    //验证发送信息时的数据，并发送站内信和短信
    public function message(){
        if ($this->validate()) {
            if (!$this->style) {
                return ['code' => 0, 'msg' => '请选择发送类型'];
            }
          if ($this->usernames != 'all') {
                $usernames = explode(",", $this->usernames);
                if (!is_array($usernames) || count($usernames) <= 0) return ['code' => 0, 'msg' => '请选择用户'];
                $real_data = [];//需要发送的用户
                $phone_data = [];//需要发送的用户中有手机的
                $no_phone_data = [];//需要发送的用户中无手机的
                foreach($usernames as $username){
                    if (!isset($this->data[$username])) {
                        return ['code' => 0, 'msg' => '数据错误请重新选择'];
                    }
                    
                    //用户列表发送短信
                    if (isset($this->data[$username]['phone']) ) {
                        empty($this->data[$username]['phone'])?$no_phone_data[]= $username:$phone_data[] = $this->data[$username];
                    }
                    //订单列表发送短信
                    if (isset($this->data[$username]['contactPhone'])) {
                        empty($this->data[$username]['contactPhone'])?$no_phone_data[]= $username:$phone_data[] = $this->data[$username];
                    }
                    //所有要发送消息用户名数据
                    $real_data[] = $this->data[$username];
                }
                if (isset($real_data[0]['User_id'])) {
                    $ids = ArrayHelper::getColumn($real_data, 'User_id');
                } else if(isset($real_data[0]['id'])){
                    $ids = ArrayHelper::getColumn($real_data, 'id');
                }
            } else {
                $ids = 'all';
            }
            //发送站内信
            if(in_array('1', $this->style)) {
                $ret = GaBaseClient::getInstance()->createSysMsg([
                        'toUserIds' => $ids,
                        'title'     => $this->title,
                        'content'   => $this->message
                        ]);
                if(!$ret['status']) {
                    //站内信发送失败直接返回
                    return ['code' => 0, 'msg' =>$ret['message']];
                }
            }
            //给所有用户发短信
            if($ids == 'all' && in_array('0', $this->style) ) {
                return ['code' => 0, 'msg' => "发送消息对象为all时，只能发送站内信"];
            }
            //发送短信
            if($ids != 'all' && in_array('0', $this->style) ) {
                //存在有手机用户
                if( count($phone_data) > 0){
                    if (isset($phone_data[0]['phone'])) {
                        $phones = ArrayHelper::getColumn($phone_data, 'phone');
                    } else if (isset($phone_data[0]['contactPhone'])) {
                        $phones = ArrayHelper::getColumn($phone_data, 'contactPhone');
                    }
                    $ret = GaBaseClient::getInstance()->sendCustomSms([
                            'phones'  => ltrim(implode(',',$phones),","),
                            'content' => $this->message
                            ]);
                    //短信发送失败直接返回
                    if(!$ret['status']) {
                        return ['code' => 0, 'msg' =>$ret['message']];
                    } else {
                        //获取短信发送失败的用户
                        $senderror = [];
                        $phone_data = ArrayHelper::index($phone_data, 'phone');
                        foreach($ret['data'] as $phone => $res) {
                            //短信发送失败的手机号返回0，成功返回1
                            if (!$res) {
                                $senderror[] = $phone_data[$phone]['nickName'];
                            }
                        }
                    }
                    //处理短信错误信息
                    if(count($no_phone_data) > 0 || count($senderror) > 0) {
                    
                        //处理无手机用户
                        if (count($no_phone_data) > 0) {
                            $msg = '（'.implode(',',$no_phone_data).'）无手机号 ，短信发送失败';
                        }
                        if (count($senderror) > 0) {
                            $msg .= '（'.implode(',',$senderror).'）发送失败';
                        }
                    }
                } else {
                    //全部是无手机用户1
                    return ['code' => 0, 'msg' => "所选用户均无手机，短信发送失败！"];
                }
            }
            if (!isset($msg)) {
                $msg = '发送成功';
            }
            return ['code' => 1, 'msg' => $msg];
        }
        $err = $this->getFirstErrors();
        return ['code' => 0, 'msg' => array_shift($err)];
    }
}