<?php
namespace  backend\models\circle;

use Yii;
use yii\base\Model;
use yii\web\UploadedFile;
use backend\components\FileUtil;
use common\models\GaBaseClient;
use common\models\UploadImage;

class CircleForm extends Model
{
    public $id;                   //圈子id
    public $name;                 //'圈子名称',   (必填)
    public $brief;                // (string) '圈子简介导语',   (必填)
    public $activityId;           //(int) '对应活动ID',    (必填)
    public $status=1;             //正常与失效--默认正常 0|1
    public $circlePicture;        // (string) '圈子图片',   (必填)
    public $linkPicture;          //(string) '圈子关联活动图片' (必填)
    public $circleThemeCount;     //圈子主题数量
    public $circleMembersCount;   //圈子成员总数
    public $themeCommentCount;    //圈子总评论数
    public $oldcirclePicture="";
    public $oldlinkPicture="";
    public $serverRes;  //服务器返回的错误
    
    public function rules()
    {
        return [
            [['name', 'brief', 'activityId','status'],'filter', 'filter' => 'trim' , 'on' => ['update', 'add']],
            [['name', 'brief', 'activityId',], 'required', 'skipOnEmpty' => false ,'on' => ['add']],
            ['id','required','on' => ['update']],
            ['id','integer','min'=>0, 'on' => ['update']],
            ['name',  'trim' , 'on' => ['update', 'add','select']],
            ['name', 'string', 'length' => [1, 30], 'on' => ['update', 'add']],
            ['brief', 'string', 'length' => [1, 60], 'on' => ['update', 'add']],
            ['activityId', 'integer','min'=> 0 , 'on' => ['update', 'add']],
            ['status', 'boolean','on' => ['update', 'add']],
            [['circlePicture','linkPicture'], 'validatePicUrls', 'skipOnEmpty' => false,'on' => ['update','add']],
        ];
    }
    
    public function attributeLabels()
    {
        return [
            'id'                 => '',
            'name'               => '圈子名称',
            'brief'              => '导语',
            'activityId'         => '圈子对应活动id',
            'status'             => '圈子状态',
            'circlePicture'      => '圈子主页图片',
            'linkPicture'        => '活动页面图片',
            'circleThemeCount'   => '圈子主题数量',
            'circleMembersCount' => '圈子成员总数',
            'themeCommentCount'  => '圈子总评论数'
        ];
    }
    
    
    public function scenarios()
    {
        return [
            'select' => ['name'],
            'update' => ['id','name', 'brief', 'activityId','status','circlePicture','linkPicture','circleThemeCount','themeCommentCount'],
            'add'    => ['name', 'brief', 'activityId','status','circlePicture','linkPicture','circleThemeCount','themeCommentCount'],
        ];
    }
    
    //验证图片数量及大小---并对circlePicture属性赋值
    public function validatePicUrls($attribute,$param)
    {
        //getInstances是数组，值为图片对象，getInstance是图片对象,如果没有上传图片则返回空
        $scenario = $this->getScenario();
       //上传图片只会是一张或者没有上传图片
        if ($scenario == 'add') {
            if (!($this->circlePicture && $this->linkPicture)) {
                return $this->addError($attribute, '图片只能是一张');
            }
            if (!UploadImage::is_img($this->circlePicture->tempName)) {
                $this->addError($attribute, "圈子图片请上传图片格式文件");
                return false;
            }
            if (!UploadImage::is_img($this->linkPicture->tempName)) {
                $this->addError($attribute, "活动图片请上传图片格式文件");
                return false;
            }
            //图片对象才有size属性
           if ($this->circlePicture->size > 1024*500 || $this->linkPicture->size > 1024*500 ) {
                return $this->addError($attribute, '图片不能大于500K');
            }
        }
        //更新圈子有图时验证
        if ($scenario == 'update') {
            if ($this->circlePicture) {
                    //判断上传文件类型
                if (!UploadImage::is_img($this->circlePicture->tempName)) {
                    $this->addError($attribute, "请上传图片格式文件");
                    return false;
                }
                if ($this->circlePicture->size > 1024*500) {
                    return $this->addError($attribute, '图片不能大于500K');
                }
            }
            if ($this->linkPicture) {
                //判断上传文件类型
                if (!UploadImage::is_img($this->linkPicture->tempName)) {
                    $this->addError($attribute, "请上传图片格式文件");
                    return false;
                }
                if ($this->linkPicture->size > 1024*500) {
                    return $this->addError($attribute, '图片不能大于500K');
                }
            }
        }
    }
    
    /**
     * 处理picUrl
     */
    private function picUrlHandle($picture)
    {
       if ($picture == "circlePicture") {
           $pic    = $this->circlePicture;
           $oldPic = $this->oldcirclePicture;
        } else {
           $pic = $this->linkPicture;
           $oldPic = $this->oldlinkPicture;
        }
        if (!$pic) {
            return ['status' => false, 'message' => "请确认上传了图片" ];
        }
        $model     = new UploadImage();
        if ($pic && $oldPic) {
            $arr = parse_url($oldPic);
            if (!$arr) {
               return ['status' => false, 'message' => "图片路径解析失败，请检查图片路径" ];
            }
            //编辑时图片删除
            //获取接口需要的旧图片路径
            $oldPic    = ltrim($arr['path'],'/img/');
            $deleteUrl = '/api/v1/deleteFiles/1';
            $res       = $model->deleteImg($oldPic , Yii::$app->params['imgServerDomin'] . $deleteUrl ,true);
            if (!$res['status']) {
                return $res;
            }
        }
        //编辑与添加时图片上传
        $result = $model->uploadImg($pic->tempName,  Yii::$app->params['imgServerDomin'].'/api/v1/circle',true);
        return $result;
    }
    
    public function add()
    {
        //给$this->circlePicture赋值
        $this->circlePicture = UploadedFile::getInstance($this, 'circlePicture');
        $this->linkPicture   = UploadedFile::getInstance($this, 'linkPicture');
        if ($this->validate()) {
            $data = [];
            //存放图片处理函数返回值
            //获取返回的图片在网页上的地址
            $this->circlePicture = $this->picUrlHandle('circlePicture');
            $this->linkPicture   = $this->picUrlHandle('linkPicture');
            if (!$this->circlePicture['status']) {
                $this->addError("serverRes", $this->circlePicture['message']);
                return false;
            }
            if (!$this->linkPicture['status']) {
                $this->addError("serverRes", $this->linkPicture['message']);
                return false;
            }
            $data['name']          = $this->name;
            $data['brief']         = $this->brief;
            $data['circlePicture'] = Yii::$app->params['imgServerDomin'].'/img/'.$this->circlePicture['data'];
            $data['linkPicture']   = Yii::$app->params['imgServerDomin'].'/img/'.$this->linkPicture['data'];
            $data['activityId']    = $this->activityId;
            $data['status']        = $this->status;
            $ret = GaBaseClient::getInstance()->createCircle($data);
            if (!$ret['status']) {
                $model     = new UploadImage();
                //添加失败
                $deleteUrl = '/api/v1/deleteFiles/1';
                $res       = $model->deleteImg($this->linkPicture['data'] , Yii::$app->params['imgServerDomin'] . $deleteUrl ,true);
                if (!$res['status']) {
                    $this->addError("serverRes", $res['message']);
                    return false;
                }
                $res2       = $model->deleteImg($this->circlePicture['data'] , Yii::$app->params['imgServerDomin'] . $deleteUrl ,true);
                if (!$res['status']) {
                    $this->addError("serverRes", $res2['message']);
                    return false;
                }
                $this->addError('serverRes', $ret['message']);
                return false;
            }
            return true;
        }
        return false;
    }
    
    public function update()
    {
        //给$this->circlePicture赋值
        $this->circlePicture = UploadedFile::getInstance($this, 'circlePicture');
        $this->linkPicture   = UploadedFile::getInstance($this, 'linkPicture');
        if ($this->validate()) {
            $data = [];
            //有圈子图片
            if ($this->circlePicture) {
                $this->circlePicture = $this->picUrlHandle('circlePicture');
                //print_r($this->circlePicture);
                //die;
                if (!$this->circlePicture['status']) {
                    $this->addError("serverRes", $this->circlePicture['message']);
                    return false;
                }
                $data['circlePicture'] = Yii::$app->params['imgServerDomin'].'/img/'.$this->circlePicture['data'];
            } else {
                //无新图上传
                if (empty(Yii::$app->request->post("old1"))){
                    $this->addError("serverRes", "必须上传1张圈子图片");
                    return false;
                }
            }
            //有主题图片
            if ($this->linkPicture) {
                $this->linkPicture   = $this->picUrlHandle('linkPicture');
                if (!$this->linkPicture['status']) {
                    $this->addError("serverRes", $this->linkPicture['message']);
                    return false;
                }
                //获取返回的图片在网页上的地址
                $data['linkPicture']   = Yii::$app->params['imgServerDomin'].'/img/'.$this->linkPicture['data'];
            } else {
                //无新图上传
                if (empty(Yii::$app->request->post("old2"))){
                    $this->addError("serverRes", "必须上传1张活动图片");
                    return false;
                }
            }
            $data['id']         = $this->id;
            $data['name']       = $this->name;
            $data['brief']      = $this->brief;
            $data['activityId'] = $this->activityId;
            $data['status']     = $this->status;
            $ret = GaBaseClient::getInstance()->modifyCircle($data);
            if (!$ret['status']) {
                $this->addError('serverRes', $ret['message']);
                return false;
            }
            return true;
        }
        //验证失败
        return false;
    }
}