<?php
namespace  backend\models\lucky;

use yii\base\Model;
use Yii;
use common\models\GaBaseClient;
/**
 * 红包记录model
 */
class LuckyRecordForm extends Model
{
    public $User_id;
    public $createTime;
    public $couponName;
    public $status;
    
    public function rules()
    {
        return [
            [['status','couponName','createTime','User_id'], 'safe'],
        ];
    }
    
    public function attributeLabels()
    {
        return [
            'User_id'     => '用户id',
            'createTime'  => '发送时间',
            'couponName'  => '红包名称',
            'status'      => '状态',
        ];
    }
    
    public function select($page, $size)
    {
        $where = [];
        if ($this->User_id) {
            $where['User_id'] = $this->User_id;
        }
        if ($this->createTime) {
            $times = explode(' 到 ', $this->createTime);
            $where['createTime'] = ['>='=>$times[0], '<=' => $times[1]];
        }
        if ($this->couponName) {
            $where['couponName'] = $this->couponName;
        }
        if (!empty($this->status) || $this->status === '0') {
            $where['status'] = $this->status;
        }
        $ret = GaBaseClient::getInstance()->getDistributedCouponList([
            'where'  => $where,
            'order'  => ['id' => 'desc'],
            'offset' => ($page - 1) * $size,
            'limit'  => $size
        ]);
        if ($ret['status']) {
            return $ret['data'];
        }
        return false;
    }
}