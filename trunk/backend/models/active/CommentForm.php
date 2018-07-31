<?php
namespace  backend\models\active;

use yii\base\Model;
use Yii;
use yii\helpers\ArrayHelper;
use common\models\GaBaseClient;
/**
 * 消息列表model
 */
class CommentForm extends Model
{
    public $id;
    public $User_id;                //评论者用户ID
    public $Activity_id;            //活动ID
    public $createTime;             //评论时间
    public $top;                    //是否置顶
    public $content;                //评论内容
    public $SalesOrder_orderNumber; //订单号
    public $display;                 //是否屏蔽
    
    public function rules()
    {
        return [
            [['User_id', 'Activity_id', 'createTime', 'top'], 'safe', 'on' => ['select']],
        ];
    }
    
    public function scenarios()
    {
        return [
            'select'   => ['User_id','Activity_id', 'createTime', 'top'],
            'delete'   => ['id', 'User_id','Activity_id', 'createTime', 'content', 'top', 'SalesOrder_orderNumber', 'display'],
            'detail'   => ['id', 'User_id','Activity_id', 'createTime', 'content', 'top', 'SalesOrder_orderNumber', 'display'],
            'edit'     => ['top', 'id'],
        ];
    }
    
    public function attributeLabels()
    {
        return [
            'id'                    => '',
            'User_id'               => '用户ID',
            'createTime'            => '评论时间',
            'Activity_id'           => '活动ID',
            'content'               => '评论内容',
            'top'                   => '是否推荐到评论首位',
            'SalesOrder_orderNumber'=> '订单号',
        ];
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
            if (!empty($this->User_id)) {
                $where['User_id'] = $this->User_id;
            }
            if (!empty($this->createTime)) {
                $createTimes = explode(' 到 ', $this->createTime);
                $where['createTime'] = ['>'=>$createTimes[0], '<' => $createTimes[1]];
            }
            if (!empty($this->Activity_id)) {
                $where['Activity_id'] = $this->Activity_id;
            }
            if (!empty($this->top) || $this->top === '0') {
                $where['top'] = $this->top;
            }
            $ret = GaBaseClient::getInstance()->getAllActivityCommentList([
                'where'  => $where,
                'order'  => ['id' => 'desc'],
                'offset' => ($page - 1) * $size,
                'limit'  => $size
            ]);
            if ($ret['status']) {
                return $ret['data'];
            }
        }
        return false;
    }
    
    /**
     * 消息屏蔽
     * @param array $comment 一条消息数据
     * @return boolean|unknown
     */
    public function delete($comment)
    {
        $info = [
            'id' => $this->id,
            'display' => $this->display == 0 ? 1 : 0
        ];
        $ret = GaBaseClient::getInstance()->modifyActivityComment($info);
        if ($ret['status']) {
            return true;
        } else {
            return $ret['message'];
        }
    }
    
    /**
     * 消息编辑
     * @param array $lastData 一条消息最初的数据
     * @return boolean|unknown
     */
    public function edit($lastData)
    {
        if($this->top != $lastData['top']) {
            $info = [
                'id'  => $this->id,
                'top' => $this->top,
            ];
            $ret = GaBaseClient::getInstance()->modifyActivityComment($info);
            if ($ret['status']) {
                return true;
            } else {
                return $ret['message'];
            }
        }
        return null;
    }
}