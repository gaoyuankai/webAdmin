<?php
namespace backend\models\trade;

use yii\base\Model;
use Yii;
use common\models\GaBaseClient;

/**
 * 订单表
 */
class TradeForm extends Model
{
    //const STATUS = ['1'=>'待付款', '2'=>'待出行', '3'=>'待评价', '4'=>'订单已完成'
                    //, '5'=>'申请退款', '6'=>'退款中', '7'=>'退款成功', '8'=>'订单已关闭'];
    public $createTime   = '';         //订单时间
    public $User_id      = 0;          //用户名
    public $Activity_id  = 0;          //活动名
    public $activityTime = '';         //活动时间
    public $status       = 0;          //订单状态
    public $orderNumber  = 0;          //订单号
    public $id           = 0;          //订单ID
    public $activityDate = '';         //活动日期
    public $adultPrice   = 0;          //成人价格
    public $adultQty     = 0;          //成人数量
    public $kidPrice     = 0;          //孩子价格
    public $kidQty       = 0;          //孩子数量
    public $totalPrice   = 0;          //订单总价
    public $couponPrice  = 0;          //红包金额
    public $contact      = '';         //联系人
    public $contactPhone = 0;          //联系电话
    public $userNote     = '';         //用户备注
    public $name         = '';         //活动名称
    public $applyReason  = '';         //退款理由
    public $adminNote    = '';         //管理员备注
    public $refund    ;                //退款信息
    public $payment ;                  //支付信息
    public function rules()
    {
        return [
            [['status'], 'safe', 'on' => ['select']],
            ['orderNumber', 'required', 'skipOnEmpty' => false, 'on' => ['detail']]
         ];
    }
    
    public function attributeLabels()
    {
        return [
            'User_id'        => '用户id',
            'createTime'     => '订单时间',
            'Activity_id'    => '活动id',
            'activityTime'   => '活动时间',
            'status'         => '订单状态',
            'orderNumber'    => '订单号',
            'id'             => '订单ID',
            'activityDate'   => '活动日期',
            'adultPrice'     => '成人价格',
            'adultQty'       => '成人数量',
            'kidPrice'       => '孩子价格',
            'kidQty'         => '孩子数量',
            'totalPrice'     => '订单总价',
            'couponPrice'    => '红包金额',
            'contact'        => '联系人',
            'contactPhone'   => '联系电话',
            'userNote'       => '用户备注',
            'name'           => '活动名称',
            'price'          => '单价',
            'quantity'       => '数量',
            'actTotalPrice'  => '实付',
            'applyReason'    => '退款理由',
            'adminNote'      => '管理员备注',
            'actTotalPrice'  => '实付金额'
        ];
    }
    
    public function __get ($props) 
    {
        if ($props == 'price') {
            $return_string = '';
            if ($this->adultPrice > 0) {
                $return_string .= '大人 ￥'.$this->adultPrice;
                if ($this->kidPrice > 0) {
                    $return_string .= ' | ';
                }
            }
            if ($this->kidPrice > 0) {
                $return_string .= '小孩 ￥'.$this->kidPrice;
            }
            return $return_string;
        } else if ($props == 'quantity') {
            $return_string = '';
            if ($this->adultQty > 0) {
                $return_string .= '大人 *'.$this->adultQty;
                if ($this->kidQty > 0) {
                    $return_string .= ' | ';
                }
            }
            if ($this->kidQty > 0) {
                $return_string .= '小孩 *'.$this->kidQty;
            }
            return $return_string;
        } else if ($props == 'actTotalPrice') {
            $return_string = '';
            if ($this->totalPrice > 0) {
                $return_string .= sprintf("%.2f", $this->totalPrice - $this->couponPrice).'元';
            }
            if ($this->couponPrice > 0) {
                $return_string .= ' （红包抵：'.$this->couponPrice.'元）';
            }
            return $return_string;
        }
    }
    
    public function scenarios()
    {
        return [
            'select' => ['User_id', 'createTime','Activity_id', 'activityTime', 'status'],
        ];
    }
    
    //获取订单详细页
    public function getDetail($data)
    {
        foreach ($data as $k => $v) {
            if(isset($this->$k)){
                $this->$k = $v;
            }
        }
        return true;
    }
    
    public function getRefund($data)
    {
        $this->getDetail($data);
        $ret = GaBaseClient::getInstance()->getUserRefundReason(['where' => ['orderNumber'=>$this->orderNumber]]);
        if ($ret['status']) {
            $this->applyReason = $ret['data']['applyReason'];
            $this->adminNote   = $ret['data']['adminReason'];
            return true;
        }
        return false;
    }
    
    //退款处理
    public function refundHandle($refundData)
    {
        $status      = $refundData['status'];      //订单状态
        $User_id     = $refundData['User_id'];     //用户id
        $orderNumber = $refundData['orderNumber']; //订单号
        $adminNote   = $refundData['adminNote'];   //管理员备注
        if ($status == 5) {//将状态从申请退款改为退款中
           /*  if (!$adminNote) {
                return ['code' => 0, 'msg' => '退款时必须填写管理员备注！'];
            } */
            $info = [
                        'User_id'     => $User_id,
                        'action'      => 1,            //1为同意
                        'orderNumber' => $orderNumber,
                        'adminReason' => $adminNote
                    ];
            
            $ret = GaBaseClient::getInstance()->dealRefund($info);
            if ($ret['status']) {
                return ['code' => 1, 'msg' => '操作成功' ];
            } else {
                return ['code' => 0, 'msg' => $ret['message']];
            }
        } else if ($status == 6) {//将状态从退款中改为退款完成
            $info = [
                        'User_id'     => $User_id,
                        'orderNumber' => $orderNumber,
                    ];
            $ret = GaBaseClient::getInstance()->finishRefund($info);
            if ($ret['status']) {
                return ['code' => 1, 'msg' => '退款完成' ];
            } else {
                return ['code' => 0, 'msg' => $ret['message']];
            }
        }
        return ['code' => 0, 'msg' => '数据有误，请刷新订单'];
    }
    
    //订单查询
    public function select($page, $size, $show)
    {
        if ($this->validate()) {
            $where = [];
            if (!empty($this->createTime)) {
                $createTimes = explode(' 到 ', $this->createTime);
                $where['createTime'] = ['>='=>$createTimes[0], '<=' => $createTimes[1]];
            }
            if (!empty($this->activityTime)) {
                $activityTimes = explode(' 到 ', $this->activityTime);
                $where['activityDate'] = ['>='=>$activityTimes[0], '<=' => $activityTimes[1]];
            }
            if (!empty($this->User_id)) {
                $where['User_id'] = $this->User_id;
            }
            if (!empty($this->Activity_id)) {
                $where['Activity_id'] = $this->Activity_id;
            }
            if (!empty($this->status) && $this->status != '') {
                $where['status'] = $this->status;
            }
            $param = [
                'where'          => $where,
                'getInsurance'   => 1,
                'order'          => ['createTime' => 'desc'],
                'getRefundInfo'  => 1,
                'getPaymentInfo' => 1,
            ];
            if ($show) {
                $param['offset'] = 0;
                $param['limit']  = (int) $show;
            } else {
                $param['offset'] = ($page - 1) * $size;
                $param['limit']  = $size;
            }
            $ret = GaBaseClient::getInstance()->getOrderList($param);
            if ($ret['status']) {
                return $ret['data'];
            }
        }
        return ['list' => [], 'count' => 0];
    }
}