<?php
namespace  backend\models\lucky;

use yii\base\Model;
use Yii;
use common\models\GaBaseClient;

/**
 * 红包列表model
 */
class LuckyListForm extends Model
{
    //const LUCKY_STATUS = ['1' => '正常', '0' => '失效'];
    //const LUCKY_KIND = [ '1' => '满额减', '2' => '折扣', '3' => '全场通用'];
    public $status = "";                //抵用券状态
    public $name = '';                 //抵用券名称
    public $data = [];                 //单个抵用券数据（用于update）
    public $couponName = '';           //红包名称
    public $brief = '';                //红包描述
    public $expireFrom = '';           //有效期开始时间
    public $expireTo = '';             //有效期结束时间
    public $totalQty = 0;              //红包总数量
    public $kind = 1;                  //红包类型 1:满xx减xx, 2:指定折扣, 3:全场通用',
    public $discountPrice = 0;         //折扣价格, 例: 100.00 或者 折扣数
    public $discountValue = 0;         //折扣价格, 例: 100.00 或者 折扣数, 例: 0.80 => (8折)
    public $discount = 0;              //例: 0.80 => (8折)'
    public $conditionPrice = 0;        //红包使用条件, 满xx金额可以使用',
    public $expireDays ;               //从发放日起， 有效天数
    public $id = 0;                    //红包id
    public $couponCode = '' ;          //(唯一)红包编号
    public $sendOutQty = 0;            //红包发放数量
    public $isExpire = 0;              //红包是否固定时间
    
    public function rules()
    {
        return [
            [
                ['couponName', 'brief', 'totalQty', 'discountValue', 'conditionPrice'
                     ,'kind' ,'status', 'expireFrom', 'expireTo']
                , 'required','skipOnEmpty' => false, 'on' => ['update', 'add']
            ],
            [['couponName', 'name' , 'brief'],  'trim' , 'on' => ['update', 'add', 'select']],
            [['isExpire'], 'boolean' ,'on' => ['update', 'add']],
            [['expireFrom', 'expireTo'], 'default', 'value' => '0000-00-00 00:00:00', 'on' => ['update', 'add']],
            ['couponName', 'string', 'length' => [1, 28], 'on' => ['update', 'add']],
            ['brief', 'string', 'length' => [1, 200], 'on' => ['update', 'add']],
            [['kind', 'status', 'totalQty', 'expireDays'], 'integer', 'on' => ['update', 'add']],
            ['expireDays', 'integer', 'min' => 1, 'max' => 365 ,'on' => ['update', 'add']],
            ['totalQty', 'integer', 'min' => 1, 'on' => ['update', 'add']],
            ['discountPrice' ,'validatediscountPrice', 'on' => ['update', 'add']],
            ['conditionPrice', 'validateConditionPrice','skipOnEmpty' => false , 'on' => ['update','add']], 
            ['conditionPrice', 'validateConditionPrice', 'on' => ['update', 'add']],
            ['expireTo', 'validateExpireTo', 'on' => ['update', 'add']],
        ];
    }
    
    
    //验证红包结束时间是否大于开始时间
    public function validateExpireTo($attribute, $params)
    {
        if ($this->isExpire === 0) {
            $to = strtotime($this->$attribute);
            $from = strtotime($this->expireFrom);
            if ($to < $from) {
                $this->addError($attribute, '结束时间应该不小于开始时间');
            }
        }
    }
    
    //验证红包使用条件
    public function validateConditionPrice($attribute, $params)
    {
        if ($this->kind == 1 ) {
            if ($this->$attribute <= $this->discountPrice) {
                $this->addError($attribute, '使用条件最低范围应高于红包金额');
            }
        }
    }
    
    public function validatediscountPrice($attribute, $params)
    {
        if ($this->kind == 2) {
            if($this->discountPrice > 1 || $this->discountPrice <=0) {
                $this->addError($attribute, '当红包为指定折扣时，折扣数在0到1之间');
            }
        }
    }
    public function scenarios()
    {
        return [
            'select'   => ['status','name','couponName'],
            'update'   => ['id','couponName', 'kind','expireFrom', 'expireTo','brief', 'totalQty', 
                            'couponCode' , 'discountPrice' , 'discountValue', 'conditionPrice','status', 'expireDays'],
            'add'      => ['couponName', 'kind','expireFrom', 'expireTo','brief', 'totalQty', 
                            'couponCode'  , 'discountPrice' , 'discountValue', 'conditionPrice','status', 'expireDays'],
        ];
    }
    
    public function attributeLabels()
    {
        return [
            'id'                    => '',
            'status'                => '红包状态',
            'name'                  => '红包名称',
            'couponName'            => '红包名称',
            'brief'                 => '红包描述',
            'expireFrom'            => '使用开始时间',
            'expireTo'              => '使用结束时间',
            'totalQty'              => '红包总数量',
            'kind'                  => '红包类型',
            'discountPrice'         => '优惠价格',
            'conditionPrice'        => '红包使用条件',
            'expireDays'            => '有效天数',
            'couponCode'            => '红包编号',
            'isExpire'              => '日期类型',
            'serverRes'             => '服务器错误',
        ];
    }
    
    //红包添加
    public function add()
    {
        if ($this->validate()) {
            $update_attr = ['status','couponName','brief','expireFrom','expireTo','totalQty',
                              'kind' ,'discountValue','conditionPrice','couponCode'];
            $update_data = [];
            foreach($update_attr as $attr) {
                $update_data[$attr] = $this->$attr;
            }
            //非固定时间才添加expireDays
            if ($this->isExpire == 1) {
                $update_data['expireDays'] = $this->expireDays;
            }
            if ($this->isExpire == 0) {
                $update_data['expireFrom'] .= " 00:00:00";
                $update_data['expireTo'] .= " 23:59:59";
            }
            $update_data['discountValue'] = $this->discountPrice;
            $ret = GaBaseClient::getInstance()->createCoupon($update_data);
            if($ret['status']) {
                return true;
            } else {
                return $this->addError('serverRes', $ret['message']);
            }
        }
         return false;
    }
    
    //修改红包
    public function update() 
    {
        if ($this->validate()) {
            $update_attr = ['id', 'status','couponName','brief','expireFrom','expireTo','totalQty',
                             'discountValue' , 'kind' , 'conditionPrice', 'couponCode'];
            $update_data = [];
            foreach($update_attr as $attr) {
                $update_data[$attr] = $this->$attr;
            }
            //非固定时间才添加expireDays
            if ($this->isExpire == 1) { 
                $update_data['expireDays'] = $this->expireDays;
            }
            if ($this->isExpire == 0) {
                $update_data['expireFrom'] = $update_data['expireFrom']." 00:00:00";
                $update_data['expireTo'] .= " 23:59:59";
            }
            $update_data['discountValue'] = $this->discountPrice;
            $ret = GaBaseClient::getInstance()->modifyCoupon($update_data);
            if($ret['status']) {
                return true;
            } else {
                 return $this->addError('serverRes', $ret['message']);
            }
        }
        return false;
    }
    
    //红包查询
    public function select ($page, $size) 
    {
        if ($this->validate()) {
            $where = [];
            if (!empty($this->name)) {
                $where['couponName'] = $this->name;
            }
            if ($this->status === " " || $this->status === 0 || $this->status === 1) {
                $where['status'] = $this->status;
            }
            $ret = GaBaseClient::getInstance()->getCouponList([
                'where'  =>$where,
                'order'  => ['id' => 'desc'],
                'offset' => ($page - 1) * $size,
                'limit'  => $size,
            ]);
            if ($ret['status']) {
                return $ret['data'];
            }
        }
        return false;
    }
}