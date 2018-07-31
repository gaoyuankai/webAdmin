<?php
namespace  backend\models\active\place;

use yii\base\Model;
use Yii;
use common\models\GaBaseClient;
use backend\components\Tool;

/**
 * 场馆管理
 */
class PlaceForm extends Model
{
    public $venueName;                //场馆名称
    public $id;                       //场地ID
    public $area;                     //地区（暂定只有上海）
    public $venueAddr;                //场馆地址
    public $longitude;                //经度
    public $latitude;                 //纬度
    public $Region_provinceId = 207;  //所在省份ID
    public $Region_cityId     = 207;  //所在城市ID
    public $Region_districtId;        //所在区域ID
    public $data;                     //场馆数据
    
    public function rules()
    {
        return [
                [
                    ['venueName', 'venueAddr', 'longitude', 'Region_provinceId', 'Region_cityId'
                        ,'latitude' ,'Region_districtId'] , 
                      'required','skipOnEmpty' => false, 'on' => ['update', 'add']
                ],
                [['venueName', 'venueAddr', 'longitude', 'Region_provinceId', 'Region_cityId'
                        ,'latitude' ,'Region_districtId'],
                    'filter', 'filter' => 'trim' , 'on' => ['update', 'add', 'select']],
                ['venueName', 'string', 'length' => [1, 30] , 'on' => ['update', 'add']],
                ['venueAddr', 'string', 'length' => [1, 50] , 'on' => ['update', 'add']],
                ['id', 'required', 'skipOnEmpty' => false, 'on' => ['update', 'delete']],
            ];
            
    }
    
    public function scenarios()
    {
        return [
            'select' => ['venueName','area'],
            'update' => ['id', 'venueName', 'venueAddr', 'longitude', 'Region_provinceId', 
                            'Region_cityId', 'latitude', 'Region_districtId'],
            'add'    => ['id', 'venueName', 'venueAddr', 'longitude', 'Region_provinceId',
                            'Region_cityId', 'latitude', 'Region_districtId'],
            'delete' => ['id'],
        ];
    }
    
    public function attributeLabels()
    {
        return [
            'id'                      => '',
            'area'                    => '区域',
            'venueName'               => '场馆名称',
            'venueAddr'               => '场馆地址',
            'longitude'               => '经度',
            'latitude'                => '纬度',
            'Region_provinceId'       => '所在省份ID',
            'Region_cityId'           => '所在城市ID',
            'Region_districtId'       => '所在区域ID',
        ];
    }
    
    public function select($page, $size)
    {
        $where = [];
        if ($this->area) {
            $where['Region_districtId'] = $this->area;
        }
        if ($this->venueName) {
            $where['venueName'] = $this->venueName;
        }
        $ret = GaBaseClient::getInstance()->getVenueList([
            'where'  => $where,
            'offset' => ($page - 1) * $size,
            'limit'  => $size
        ]);
        if ($ret['status']) {
            return $ret['data'];
        }
    }
    
    public function update()
    {
        if ($this->validate()) {
            $info['venueName']  = $this->venueName;
            $info['venueAddr']  = $this->venueAddr;
            $info['longitude']  = $this->longitude;
            $info['latitude']   = $this->latitude;
            $info['provinceId'] = $this->Region_provinceId;
            $info['cityId']     = $this->Region_cityId;
            $info['districtId'] = $this->Region_districtId;
            $ret = GaBaseClient::getInstance()->modifyVenue($this->id, $info);
            if($ret['status']) {
                return ['code' => 1, 'msg' => '修改成功'];
            } else {
                return ['code' => 0, 'msg' => $ret['message']];
            }
        } else {
            Tool::echoError($this);
        }
    }
    
    public function add()
    {
        if ($this->validate()) {
            $info['venueName']  = $this->venueName;
            $info['venueAddr']  = $this->venueAddr;
            $info['longitude']  = $this->longitude;
            $info['latitude']   = $this->latitude;
            $info['provinceId'] = $this->Region_provinceId;
            $info['cityId']     = $this->Region_cityId;
            $info['districtId'] = $this->Region_districtId;
            $ret = GaBaseClient::getInstance()->createVenue($info);
            if($ret['status']) {
                return ['code' => 1, 'msg' => '创建成功'];
            } else {
                return ['code' => 0, 'msg' => $ret['message']];
            }
        } else {
            Tool::echoError($this);
        }
    }
    
    public function delete()
    {
        if ($this->validate()) {
            $info = ['venueId'  => $this->id];
            $ret = GaBaseClient::getInstance()->deleteVenue($info);
            if($ret['status']) {
                return ['code' => 1, 'msg' => '删除成功'];
            } else {
                return ['code' => 0, 'msg' => $ret['message']];
            }
        } else {
            Tool::echoError($this);
        }
    }
}