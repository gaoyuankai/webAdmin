<?php
namespace  backend\models\message;

use yii\base\Model;
use Yii;
use yii\helpers\ArrayHelper;
use common\models\GaBaseClient;

/**
 * 消息列表model
 */
class MessageListForm extends Model
{
    public $id;          //消息id
    public $createTime;  //发送时间
    public $User_id;     //发送者id
    public $to_User_id;  //接受者id
    public $title;       //标题
    public $content;     //发送内容
    
    public function rules()
    {
        return [
            [['createTime', 'to_User_id', 'title', 'content'], 'safe', 'on' => ['select']],
           
        ];
    }
    
    public function scenarios()
    {
        return [
            'select'   => ['createTime','to_User_id', 'title'],
        ];
    }
    
    public function attributeLabels()
    {
        return [
            'id'                    => '',
            'User_id'               => '发送者id',
            'createTime'            => '发送时间',
            'title'                 => '标题',
            'content'               => '发送内容',
            'to_User_id'            => '接收者id'
        ];
    }
    
    /**
     * 消息删除
     * $ids     [uid]
     */
    public function delete($ids)
    {
        if (!is_array($ids) || count($ids) <= 0) {
            return '选择用户出错';
        }
        $where = ['where' => ['id' => $ids]];
        $ret   = GaBaseClient::getInstance()->deleteSysMessages($where);
        if ($ret['status']) {
            return true;
        } else {
            return $ret['message'];
        }
    }
    
    /**
     * 消息查询
     * @param int $page 当前页数
     * @param int $size 一页的条数
     */
    public function select ($page, $size)
    {
        if ($this->validate()) {
            $where = [];
            if (!empty($this->to_User_id)) {
                $where['to_User_id'] = $this->to_User_id;
            }
            if (!empty($this->createTime)) {
                    $createTimes = explode(' 到 ', $this->createTime);
                    $where['createTime'] = ['>'=>$createTimes[0], '<' => $createTimes[1]];
            }
            if (!empty($this->title)) {
                $where['title'] = $this->title;
            }
            $ret = GaBaseClient::getInstance()->getSysMessageList([
                'where'  => $where,
                'order'  => ['createTime' => 'desc'],
                'offset' => ($page - 1) * $size,
                'limit'  => $size
            ]);
            if ($ret['status']) {
                return $ret['data'];
            }
        }
        return false;
    }
}