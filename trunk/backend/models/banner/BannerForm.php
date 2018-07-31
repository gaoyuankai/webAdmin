<?php
namespace  backend\models\banner;

use yii\base\Model;
use Yii;
use yii\web\UploadedFile;
use backend\components\FileUtil;
use common\models\GaBaseClient;
use common\models\UploadImage;

/**
 * Banner Form
 */
class BannerForm extends Model
{
    public $title;      //(string) 'baner标题, 30个字之内',    (必填)
    public $type = 1;     //(int) '类型, 1:首页轮播图， 2:首页活动, 3:首页圈子话题,4:手机轮播图
    public $url;        //(int) 'banner链接'     (必填)
    public $picture;    //， (必填)
    public $status=0;   //是否启用，0未启用，1已启用
    public $asociateActivityidORassociateCircleid;        //关联活动id
    public $id;         //配置id
    public $kind=1;     //关联类型
    public $sort;       //活动显示位置
    public $serverRes;  //服务器返回的错误
    public $oldPic = "";
   
    public function rules()
    {
        return [
                   [[ 'type',  'status', 'sort', ],
                   'filter', 'filter' => 'trim' , 'on' => ['update', 'add1','add2' , 'add3' ,'add4','select']],
                   [['type', 'sort'], 'required', 'on' => ['update', 'add1','add2' , 'add3' ,'add4']],
                   ['type',  'in','range'=>[1,2,3,4],'on' => ['update', 'add1','add2' , 'add3' ,'add4']],
                   ['status', 'boolean','on' => ['update', 'add1','add2' , 'add3' ,'add4']],
                   ['sort',  'integer','min'=>1, 'max' => 255,'on' => ['update', 'add1','add2' , 'add3' ,'add4']],
                   ['title', 'string', 'length' => [1, 20], 'skipOnEmpty' => true, 'on' => ['update', 'add1']],
                   ['title', 'validateTitle','skipOnEmpty' => false, 'on' => ['update', 'add1']],
                   ['url', 'url', 'skipOnEmpty' => true,'on' => ['update', 'add1']],
                   ['url', 'validateUrl','skipOnEmpty' => false,'on' => ['update', 'add1']],
                   ['id', 'integer','min'=>0, 'on' => ['update']],
                   ['asociateActivityidORassociateCircleid', 'integer', 'skipOnEmpty' => true, 'on' => ['update', 'add2' , 'add3' ,'add4']],
                   ['asociateActivityidORassociateCircleid', 'validateAssociateId','skipOnEmpty' => false, 'on' => ['update', 'add2' , 'add3' ,'add4']],
                   ['kind', 'in','range' => [1,2],'skipOnEmpty' => true,'on' => ['update', 'add2']],
                   ['kind', 'validateKind','skipOnEmpty' => false,'on' => ['update', 'add2']],
                   ['picture', 'validatePicUrls', 'on' => ['add1','add2' , 'add3' ,'add4','update']],
               ];
    }
    
    public function attributeLabels()
    {
        return [
            'id'               => '',
            'asociateActivityidORassociateCircleid' => '关联id',
            'title'            => '标题',
            'type'             => '类型',
            'url'              => '链接',
            'status'           => '是否启用',
            'picture'          => '图片地址',
            'sort'             => '排序',
            'kind'             => '关联类型'
        ];
    }
    public function scenarios()
    {
        return [
            'select' => ['type'],
            'update' => ['id','asociateActivityidORassociateCircleid', 'title', 'oldPic','type', 'url', 'picture', 'status', 'sort', 'kind'],
            'add1'   => [ 'title', 'type', 'url', 'picture', 'status', 'sort'],
            'add2'   => ['asociateActivityidORassociateCircleid',  'type' , 'picture', 'status', 'sort', 'kind'],
            'add3'   => ['asociateActivityidORassociateCircleid',  'type',  'picture', 'status', 'sort'] ,
            'add4'   => ['asociateActivityidORassociateCircleid',  'type', 'picture', 'status', 'sort', ],
        ];
    }
    
    
    //验证标题
    public function validateTitle($attribute,$param)
    {
        $scenario = $this->getScenario();
        if($scenario == 1){
            if($this->title == ""){
                return $this->addError($attribute, '标题不能为空');
            }
        }
    }
    //验证链接
    public function validateUrl($attribute,$param)
    {
        $scenario = $this->getScenario();
        if($scenario == 1){
            if($this->url == "")
                return $this->addError($attribute, '链接不能为空');
        }
    }
    //验证关联活动或圈子id
    public function validateAssociateId($attribute,$param)
    {
        $scenario = $this->getScenario();
        if($scenario ==2|| $scenario == 3|| $scenario == 4){
            if($this->asociateActivityidORassociateCircleid == ""){
                return $this->addError($attribute, '关联id不能为空');
            }
        }
    }
    //验证类型--活动或者活动圈子
    public function validateKind($attribute,$param)
    {
        $scenario = $this->getScenario();
        if($scenario == 2){
            if($this->kind == ""){
                return $this->addError($attribute, '关联类型不能为空');
            } 
        }
    }
    
    //验证图片数量及大小
    public function validatePicUrls($attribute,$param)
    {   
        $scenario = $this->getScenario();
        //验证文件类型
        if (($scenario == 1 || $scenario == 2 || $scenario == 3|| $scenario == 4 || $scenario == 'update') && $this->picture){
            //判断上传文件类型
            if (!UploadImage::is_img($this->picture->tempName)) {
                $this->addError($attribute, "请上传图片格式文件");
                return false;
            }
        }
        //上传图片只会是一张或者没有上传图片--update不用验证
        if ($scenario != 'update') {
            if (!$this->picture) {
                return $this->addError($attribute, '图片只能是一张');
            }
            //图片对象才有size属性
            if ($this->picture->size > 1024*1024) {
                return $this->addError($attribute, '图片不能大于500K');
            }
        }
        if ($scenario == 'update' && $this->picture) {
            //图片对象才有size属性
            if ($this->picture->size > 1024*1024) {
                return $this->addError($attribute, '图片不能大于500K');
            }
        }
       
    }
    /**
     * 处理picture
     */
    private function picUrlHandle()
    {
        $model  = new UploadImage();
        $result = $model->uploadImg($this->picture->tempName,  Yii::$app->params['imgServerDomin'].'/api/v1/homepage',true);
        if ($this->picture && $this->oldPic) {
            $arr= parse_url($this->oldPic);
            if (!$arr) {
                return ['status' => false, 'message' => "图片路径解析失败，请检查图片路径" ];
            }
            
            //获取接口需要的旧图片路径
            $oldPic = ltrim($arr['path'],'/img/');
            $deleteUrl = '/api/v1/deleteFiles/1';
            $res = $model->deleteImg($oldPic , Yii::$app->params['imgServerDomin'] . $deleteUrl ,true);
            if (!$res['status']) {
                return $res;
            }
        }
        return $result;
    }
    
    public function update()
    {
        $this->picture = UploadedFile::getInstance($this, 'picture');
        if ($this->validate()) {
            $data = [];
            if ($this->picture) {
                $this->picture = $this->picUrlHandle();
                if (!$this->picture['status']) {
                    //图片更新报错
                    $this->addError("serverRes", $this->picture['message']);
                    return false;
                }
                $data['picture'] = Yii::$app->params['imgServerDomin'].'/img/'.$this->picture['data'];
            } else {
                //无新图上传
                if (empty(Yii::$app->request->post("old"))){
                    $this->addError("serverRes", "必须上传1张图片");
                    return false;
                }
            }
            $data['id']     = $this->id;
            $data['type']   = $this->type;
            $data['status'] = $this->status;
            $data['sort']   = $this->sort;
            //根据类型添加不同字段
            if ($this->type==1) {
                $data['items'] = [
                    'url'   => $this->url,
                    'title' => $this->title
                ];
            }
            if ($this->type == 2) {
                
                $data['items'] = [
                    'kind' => $this->kind,
                    'id'   => $this->asociateActivityidORassociateCircleid
                ];
                
            }
            if ($this->type == 3 || $this->type == 4) {
                $data['items'] = [
                    'id' => $this->asociateActivityidORassociateCircleid
                ];
            }
            $ret = GaBaseClient::getInstance()->modifyHomePageConfig($data);
            if($ret['status']) {
                return true;
            } else {
                $this->addError("serverRes", $ret['message']);
                return false;
            }
        } else {
            //验证失败
            return false;
        }
    }
    
    public function add()
    {
        //获取图片临时信息
        $this->picture = UploadedFile::getInstance($this, 'picture');
        if ($this->validate()) {
            $data = [];
            //无图
            if (!$this->picture) {
                $this->addError("serverRes", "必须上传图片！");
                return false;
            }
            //获取返回的图片在网页上的地址
            $this->picture   = $this->picUrlHandle();
            //图片上传接口错误
            if (!$this->picture['status']) {
                $this->addError("serverRes", $this->picture['message']);
                return false;
            }
            $data['type']    = $this->type;
            $data['picture'] = Yii::$app->params['imgServerDomin'].'/img/'.$this->picture['data'];
            $data['status']  = $this->status;
            $data['sort']    = $this->sort;
            //根据类型添加不同字段
            if ($this->type==1) {
                $data['items'] = [   
                    'url'   => $this->url,
                    'title' => $this->title
                ];
            }
            if ($this->type == 2) {
                $data['items'] = [
                    'kind' => $this->kind,
                    'id'   => $this->asociateActivityidORassociateCircleid
                ];
            }
            if ($this->type == 3 || $this->type == 4) {
                $data['items'] = [
                    'id' => $this->asociateActivityidORassociateCircleid
                ];
            }
            $ret = GaBaseClient::getInstance()->createHomePageConfig($data);
            if (!$ret['status']) {
                //添加失败
                $model     = new UploadImage();
                $deleteUrl = '/api/v1/deleteFiles/1';
                $res       = $model->deleteImg($this->picture['data'] , Yii::$app->params['imgServerDomin'] . $deleteUrl ,true);
                if (!$res['status']) {
                    $this->addError("serverRes", $res['message']);
                    return false;
                }
                 $this->addError('serverRes', $ret['message']);
                 return false;
            }
            return true;
        }
        return false;
    }
}
