<?php
namespace backend\models\user;

use common\models\User;
use yii\base\Model;
use Yii;

/**保险信息模型
 * Additioninfo form
 */
class AdditioninfoForm extends Model
{
    /*//'id'                  => '订单ID',
    //'ActivitySchedule_id' => '活动场次ID',*/
    
    public $User_id;                     //用户ID
    public $orderNumber;                 //订单编号
    public $status = "";                 //订单状态1:待付款, 2:待出行, 3:待评价,4:订单已完成, 5:申请退款, 6:退款中,7:退款成功, 8:订单已关闭',
    public $Activity_id;                 //活动ID
    public $activityDate;                //活动时间
    public $activityTime;                //活动场次
    public $name;                        //投保人
    public $contactPhone;                //联系电话
    public $contact;                     //联系人
    public $type;                        //证件类型1：身份证
    public $identityNumber;              //证件号码
    public $ActivitySchedule_id;         //活动场次id
    
    public function rules()
    {
        return [
            [['User_id','Activity_id','status','ActivitySchedule_id'],'safe', 'on' => ['select']],
            [['User_id','Activity_id','ActivitySchedule_id'],'integer','min'=>0,'on' => ['select']],
        ];
    }
    
    public function attributeLabels()
    {
        return [
            'User_id'             => '用户ID',
            'status'              => '订单状态',
            'Activity_id'         => '活动ID',
            'ActivitySchedule_id' => '活动场次ID',
            'activityDate'        => '活动日期',
            'orderNumber'         => '订单编号',
        ];
    }
    
    public function scenarios()
    {
        return [
            'select' => ['User_id','Activity_id','status','activityDate','ActivitySchedule_id'],
        ];
    }
}
