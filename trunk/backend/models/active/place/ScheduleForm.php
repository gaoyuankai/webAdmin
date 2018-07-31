<?php
namespace backend\models\active\place;

use yii\base\Model;
use Yii;
use common\models\GaBaseClient;
use yii\helpers\ArrayHelper;
/**
 * 活动场次
 */
class ScheduleForm extends Model
{
    public $id;              //活动场地id
    public $minNumber;       //(int) '最少成团人数',                  (必填)
    public $stock;           //(int) '活动库存数， 以小孩人数为准',         (必填)
    public $activityDate;    //(date) '活动场次日期, 例: 2016-01-11',  (必填)
    public $activityTime;    //(time) '活动场次时间, 例: 10:00:00',    (必填)
    public $regStart;        //(datetime) '报名开始时间, 例: 2016-01-11 00:00:00', (必填)
    public $regEnd;          //(datetime) '报名结束时间, 例: 2016-01-12 00:00:00', (必填)
    
    public function rules()
    {
        return [
            [
                ['minNumber', 'stock', 'activityDate', 'regStart'
                     ,'regEnd' ], 'required','skipOnEmpty' => false,'on' =>['add','update']
            ],
            [['stock'], 'integer', 'min' => 1, 'on' =>['add','update']],
            ['minNumber', 'validateMinNumber' , 'on' =>['add','update']],
            ['regStart', 'validateRegStart' , 'on' =>['add','update']],
            ['id', 'integer', 'min' => 0 , 'on' =>['delete','update']],
        ];
    }
    
    //验证成团人数
    public function validateMinNumber($attribute, $params)
    {
        $scenario = $this->getScenario();
        if ($scenario == "add" || $scenario == "update") {
            if ($this->minNumber > 0  && $this->minNumber > $this->stock) {
                $this->addError($attribute, '成团人数不可大于单场人数');
            }
        }
    }
    
    //验证活动结束时间是否大于开始时间
    public function validateRegStart($attribute, $params)
    {
        $scenario = $this->getScenario();
        if ($scenario == "add" || $scenario == "update") {
            $start = strtotime($this->$attribute);
            $end = strtotime($this->regEnd);
            $activityDate = strtotime($this->activityDate);
            if ($activityDate <= strtotime('today')) {
                return $this->addError($attribute, '活动开始时间不能晚于今天');
            }
            if ($end < $start) {
                return $this->addError($attribute, '报名结束时间应该大于开始时间');
            } 
            if ($end <= $start) {
                return $this->addError($attribute, '报名结束时间应该大于开始时间');
            }
            if ($start > $activityDate || $end > $activityDate) {
                return $this->addError($attribute, '报名时间应该小于活动开始时间');
            }
            if ($end  < strtotime('today')) {
                return $this->addError($attribute, '报名结束时间应该大于当前时间');
            }
        }
    }
    
    //设置场景
    public function scenarios()
    {
        return [
        'update' => ['id', 'minNumber', 'stock', 'activityTime' ,'activityDate', 'regStart','regEnd','stock'],
        'add'    => ['minNumber', 'stock', 'activityTime' , 'activityDate', 'regStart','regEnd','stock'],
        'delete' => ['id'],
        ];
    }
    
    public function attributeLabels()
    {
        return [
            'minNumber'        => '最少成团人数',
            'stock'            => '活动库存数',
            'activityDate'     => '活动场次日期',
            'activityTime'     => '活动场次时间',
            'regStart'         => '报名开始时间',
            'regEnd'           => '报名结束时间',
        ];
    }
}