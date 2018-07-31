<?php
namespace  backend\models\circle;

use Yii;
use yii\base\Model;
use yii\web\UploadedFile;
use backend\components\FileUtil;
use common\models\GaBaseClient;
use common\models\UploadImage;

class ThemeForm extends Model
{
    public $id;                   //圈子主题ID
    public $Circle_id;            //所属圈子ID(必填)
    public $name;                 //(string) '圈子名称',   (必填)
    public $User_id;              //(int) '作者，主题发布者id',    (必填)
    public $createTime;           //主题发布时间
    public $top = 0;              //是否推荐置顶， 0：不置顶， 1：置顶
    public $themePictures;        //主题图片--最多九张图片，可以不添加图片
    public $commentNumber;        //主题评论总数
    public $content;              //主题内容
    public $sysAdmin = 0;         //是否管理员  1官方话题0普通话题--默认1，不超过200字
    public $nickName = "";          //作者昵称
    public $serverRes;  //服务器返回的错误
    
    public function rules()
    {
        return [
            [['Circle_id', 'content', 'activityId','status'], 'required', 'on' => ['add']],
            ['User_id','integer','min'=>0, 'on' => ['select']],
            ['name', 'string', 'on' => ['add']],
            ['content', 'string', 'length' => [1, 200], 'on' => ['add']],
            ['activityId', 'integer','min'=>0, 'on' => ['add']],
            ['status', "boolean",'on' => ['add','update']],
            ['themePictures', 'validatePicUrls', 'skipOnEmpty' => true,'on' => ['add']],
        ];
    }
    
    public function attributeLabels()
    {
        return [
            'id'            => '',
            'Circle_id'     => '所属圈子ID',
            'name'          => '所属圈子',
            'User_id'       => '用户ID',
            'createTime'    => '创建时间',
            'top'           => '是否推荐',
            'themePictures' => '主题图片',
            'content'       => '主题内容',
            'sysAdmin'      => '主题类型',
            'commentNumber' => '主题总评论数',
            'nickName'      => '作者',
        ];
    }
    
    
    public function scenarios()
    {
        return [
            'select' => ['User_id'],
            'update' => ['id','name', 'Circle_id', 'User_id','top','themePictures','content','sysAdmin','commentNumber'],
            'add'    => ['name', 'Circle_id', 'User_id','top','themePictures','content','sysAdmin','commentNumber'],
        ];
    }
    
    //验证图片数量及大小---并对themePicture属性赋值
    public function validatePicUrls($attribute,$param)
    {
        //getInstances是数组，值为图片对象，getInstance是图片对象,如果没有上传图片则返回空
        //给$this->themePicture赋值，post提交并没有image值
        $scenario = $this->getScenario();
        if ($scenario == 'add') {
            //有图片才验证
            if ($this->themePictures) {
                if (count($this->themePictures) > 9) {
                    return $this->addError($attribute, '活动图片不能多于9张');
                }
                //需求没要求图片大小
                foreach($this->themePictures as $pic) {
                    //验证图片文件格式
                    if (!UploadImage::is_img($pic->tempName)) {
                        $this->addError($attribute, "请上传图片格式文件");
                        return false;
                    }
                    if ($pic->size > 1024*500) {
                        return $this->addError($attribute, '活动图片不能大于500k');
                    }
                }
            }
        }
    }
    
    /**
     * 处理picUrl
     */
    private function picUrlHandle()
    {
        $model  = new UploadImage();
        $returnArr = [];
        if ($this->themePictures) {
                foreach ($this->themePictures as $picUrl) {
                    $result = $model->uploadImg($picUrl->tempName,  Yii::$app->params['imgServerDomin'].'/api/v1/circleTheme',true);
                    //有图片上传出错
                    if (!$result['status']){
                        $this->addError('serverRes', $result['message']);
                        return $result;
                    }
                    //上传成功将图片路径存入数组
                    $returnArr[] = Yii::$app->params['imgServerDomin'].'/img/'.$result['data'];
                }
         }
         return ['status' => true,'data'=> $returnArr];
    }
    
    public function add()
    {
        $this->themePictures = UploadedFile::getInstances($this, 'themePictures');
        if ($this->validate()) {
            $data = [];
            $time = time();
            //有图才添加
            if ($this->themePictures) {
                $this->themePictures = $this->picUrlHandle();
                if (!$this->themePictures['status']) {
                    //图片上传报错
                    $this->addError("serverRes", $this->themePictures['message']);
                    return false;
                }
                $data['pictures'] = $this->themePictures['data'];
            }
            $data['circleId']  = $this->Circle_id;
            //当前用户id
            $data['userId']    = 1;
            $data['content']   = $this->content;
            $data['sysAdmin']  = $this->sysAdmin;
            $data['top']       = $this->top;
            $ret = GaBaseClient::getInstance()->createCircleTheme($data);
            if (!$ret['status']) {
                $model     = new UploadImage();
                $imgStr = "";
                //添加失败--删除已上传图片
                $deleteUrl = '/api/v1/deleteFiles/1';
                //多图$this->themePictures['data']数组中图片路径带域名。
                foreach ($this->themePictures['data'] as $themePicture) {
                    //,拼接要删除图片路径
                    $arr = parse_url($themePicture);
                    if (!$arr) {
                        $this->addError("serverRes", '圈子图片路径解析失败，请检查图片路径');
                        return false;
                    }
                    $imgPath = ltrim($arr['path'],'/img/');
                    $imgStr .= $imgPath.",";
                }
                $imgStr = rtrim($imgStr,',');
                //已上传图片删除
                if ($imgStr) {
                    $deleteUrl = Yii::$app->params['imgServerDomin'] .'/api/v1/deleteFiles/1';
                    $res       = $model->deleteImg( $imgStr , $deleteUrl );
                    if (!$res['status']) {
                        $this->addError("serverRes", $res['message']);
                        return false;
                    }
                }
                $this->addError('serverRes', $ret['message']);
                return false;
            }
            return true;
        }
        //验证失败
        return false;
    }
    
}
