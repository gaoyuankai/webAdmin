<?php
namespace  backend\models\active;

use yii\base\Model;
use Yii;
use yii\web\UploadedFile;
use backend\components\FileUtil;
use common\models\GaBaseClient;
use common\models\UploadImage;

/**
 * Active Form
 */
class ActiveForm extends Model
{
    public $id;                   //活动id
    public $name = '';            //(string) '活动名称, 30个字之内',    (必填)
    public $status = 0;           //活动状态
    public $brief = '';           //(string) '活动简介, 200个字之内',   (必填)
    public $description = '';     //(string) '活动详细介绍, 1万字以内',  (必填)
    public $activityKind = 2;     //(int) '活动类型, 1:其他, 2:自营',   (必填)
    public $Venues_id = '';       //(int) '活动地点id'                 (必填)
    public $sponsor = '';         //(string) '活动主办方, 30个字之内'   (必填)
    public $highlights = '';      //(string) ‘活动亮点, 多个用逗号分隔, 30个字以内’,    (必填)
    public $stars =5;            //(int) '活动星数, 默认为 5 星',      (非必填)
    public $ability = "";         //(array(int)) '活动能力培养项, 1：学习能力， 2： 动手能力， 3： 交流能力，
                                  //  4：创造能力， 5：抗压能力， 6：团队协作能力'   (必填)
    public $ageGroup = '';        //(array(int)) '活动适应年龄段, 1:3-6， 2:7-10， 3:11-14， 4:15-17',       (必填)
    public $periodInfo = '';      //(string) '活动周期描述',            (必填)
    public $insurance = 0;        //(int) '是否需要保险, 0:不需要, 1:需要', (必填)
    public $priceKind = 1;       //(int) '活动价格类型， 1：一大一小（打包）， 2：大人小孩分开计算， 3：只有小孩', 
    //当 priceKind 为 2 时, adultPrice 和 kidPrice 为必填, totalPrice 不必填写
    public $adultPrice = '';      //(float) '成人价格', (当 priceKind 为 2 时必填)
    public $kidPrice = '';        //(float) '孩子价格', (当 priceKind 为 2 时必填)
    //当 priceKind 为 1 或 3 时, totalPrice 为必填, adultPrice 和 kidPrice 不必填写
    public $totalPrice = '';      //(float) '活动总价', (当 priceKind 为 1 或 3 时必填)
    public $activitySchedule = [];//场次内容
    public $picUrls = '';         //(array) ['图片地址', '图片地址']， (必填)
    public $serverRes = '';       //服务器返回的错误
    public $type;                 //修改的模块编号
    public $cover;                //封面图片
    public $oldCover   = "";      //旧封面图
    public $olddescriptionPic = "";
    public $descriptPic = "" ;         //活动详细介绍中的新图
    
    public function rules()
    {
        return [
            [
                [
                'name', 'brief', 'description', 'activityKind', 'Venues_id',
                'sponsor', 'highlights', 'stars', 'ability', 'ageGroup',
                'periodInfo', 'insurance', 'priceKind', 'kidPrice', 'activitySchedule',
                'picUrls', 'status', 'adultPrice', 'totalPrice'
                 ], 'safe','on' => ['add']
            ],
            //添加与更新必填字段
            [
                [
                    'name', 'brief', 'Venues_id',
                    'sponsor', 'highlights', 'stars', 'ability', 'ageGroup',
                    'periodInfo', 'insurance','activitySchedule',
                 ], 'required','skipOnEmpty' => false,'on' => ['add']
            ],
            [['name', 'activityKind', 'insurance', 'stars', 'ageGroup',
              'ability',  'sponsor','Venues_id','highlights', 'periodInfo', 'brief'],'required','skipOnEmpty' => false,'on' => ['updatetype1']
            ],
            [['periodInfo','brief','name','sponsor'], 'trim' , 'on' => ['add','updatetype1']],
            ['description', 'trim' ,'on' => ['add','updatetype2']],
            ['periodInfo','string', 'length' => [1, 50],'on' => ['add','updatetype1']],
            ['brief', 'string','length' => [1, 100] , 'on' => ['add' , 'updatetype1']],
            [['name','sponsor'], 'string', 'length' => [1, 30],'on' => ['add','updatetype1']],
            ['description', 'string','on' => ['add','updatetype2']],
            ['description', 'required','on' => ['add','updatetype2']],
            ['description', 'validateDescription','skipOnEmpty' => false,'on' => ['add','updatetype2']],
            ['highlights', 'validateHighlights','on' => ['add','updatetype1']],
            ['picUrls', 'required','on' => ['add']],
            ['priceKind', 'required','on' => ['add','updatetype3']],
            ['priceKind', 'validatePriceKind','skipOnEmpty' => false,'on' => ['add','updatetype3']],
            ['picUrls', 'validatePicUrls','skipOnEmpty' => false,'on' => ['add']],
            ['cover', 'required','skipOnEmpty' => false, 'on' => ['add']],
            ['cover', 'validateCover','skipOnEmpty' => true,'on' => ['updatetype5']],
            ['picUrls', 'validatePicUrls','skipOnEmpty' => true,'on' => ['updatetype5']],
        ];
    }

    public function attributeLabels()
    {
        return [
            'id'               => '',
            'name'             => '活动名称',
            'brief'            => '活动简介',
            'description'      => '活动详细介绍',
            'activityKind'     => '活动类型',
            'Venues_id'        => '活动地点id',
            'sponsor'          => '活动主办方',
            'highlights'       => '活动亮点',
            'stars'            => '活动星数',
            'ability'          => '活动能力培养',
            'ageGroup'         => '活动适应年龄段',
            'periodInfo'       => '活动周期描述',
            'insurance'        => '保险',
            'priceKind'        => '活动价格类型',
            'adultPrice'       => '成人价格',
            'kidPrice'         => '孩子价格',
            'totalPrice'       => '活动总价',
            'activitySchedule' => '场次内容',
            'picUrls'          => '图片地址',
            'status'           => '活动状态',
            'cover'            => '封面图片'
        ];
    }
    
    //设置场景
    public function scenarios()
    {
        return [
        'updatetype1' =>['id','name', 'activityKind', 'insurance', 'stars', 'ageGroup',
                        'ability',  'sponsor','Venues_id','highlights', 'periodInfo', 'brief'],
        'updatetype2' => ['id','description' , 'olddescriptionPic'],
        'updatetype3' => ['id','priceKind', 'kidPrice','adultPrice', 'totalPrice'],
        'updatetype4' => ['id','activitySchedule'],
        'updatetype5' => ['id','picUrls','cover'],
        'add'         => ['name', 'brief', 'description', 'activityKind', 'Venues_id','sponsor', 'highlights', 'stars', 
                         'ability', 'ageGroup','periodInfo', 'insurance', 'priceKind', 'kidPrice', 'activitySchedule',
                         'picUrls', 'status', 'adultPrice', 'totalPrice', 'cover'],
        ];
        
        
    }
    //验证活动详细介绍
    public function validateDescription($attribute,$param)
    {
        //获取当前场景
        $scenario = $this->getScenario();
        if ( $scenario == 'add' || $scenario == 'updatetype2') {
            if (!$this->description) {
                return $this->addError($attribute, '活动详细介绍不能为空');
            }
        }
    }
    //验证图片数量及大小
    public function validatePicUrls($attribute,$param)
    {
        //获取当前场景
        $scenario = $this->getScenario();
        //如果是添加
       if ( $scenario == 'add' ) {
           if (count($this->picUrls) > 5 || count($this->picUrls) < 2) {
               return $this->addError($attribute, '活动图片不能少于2张或多于5张');
           }
           foreach($this->picUrls as $pic) {
               //判断上传文件类型
               if (!UploadImage::is_img($pic->tempName)) {
                   $this->addError($attribute, "活动图片请上传图片格式文件");
                   return false;
               }
               if ($pic->size > 1024*500) {
                   return $this->addError($attribute, '活动图片不能大于500k');
               }
           }
       }
       //如果是更新，老图肯定符合大小规则
       if ( $scenario == 'updatetype5' && $this->picUrls) {
           foreach($this->picUrls as $pic) {
               if (!UploadImage::is_img($pic->tempName)) {
                   $this->addError($attribute, "活动图片请上传图片格式文件");
                   return false;
               }
               if ($pic->size > 1024*500) {
                   return $this->addError($attribute, '活动图片不能大于500k');
               }
           }
       }
    }
    
    //验证封面图片
    public function validateCover($attribute,$param)
    {
        //获取当前场景
        $scenario = $this->getScenario();
        if ( $scenario == 'add' && !$this->cover){
                return $this->addError($attribute, '封面图必须为一张');
        }
        //验证文件类型
        if (($scenario == 'add' || $scenario == 'updatetype5') && $this->cover){
            //判断上传文件类型
               if (!UploadImage::is_img($this->cover->tempName)) {
                           $this->addError($attribute, "封面图片请上传图片格式文件");
                           return false;
               }
        }
        //只取一张
        if ( $scenario == 'add' || ($scenario == 'updatetype5' && $this->cover)) {
                if ($this->cover->size > 1024*500) {
                    return $this->addError($attribute, '封面图片不能大于500k');
                }
        }
    }
    
    //验证活动价格类型
    public function validatePriceKind($attribute,$param)
    {
        //获取当前场景
        $scenario = $this->getScenario();
        //添加时必须验证或更新时有价格类型
        if ($scenario == 'add' || ($scenario == 'updatetype3' && $this->priceKind)) {
        switch ($this->priceKind) {
            case '2': 
                if ($this->kidPrice <= 0 || $this->adultPrice <= 0) {
                    return $this->addError($attribute, '当活动价格类型为大人小孩分开算时，必须填写成人价格和孩子价格');
                }
                break;
            default:
                if ($this->totalPrice <= 0) {
                    return $this->addError($attribute, '当活动价格类型不为大人小孩分开算时，必须填写活动总价');
                }
                break;
         }
        }
    }
    
    //验证活动亮点格式
    public function validateHighlights($attribute,$param)
    {
        $scenario = $this->getScenario();
        if ($scenario == 'add' || $scenario == 'updatetype1') {
            $highs = explode(',', $this->$attribute);
            if (!is_array($highs) || count($highs) <= 0) {
              $this->addError($attribute, '活动亮点格式不正确！');
            }
        }
    }
    
    //添加活动
    public function addActive()
    {
        //图片属性赋值
        $this->picUrls = UploadedFile::getInstances($this, 'picUrls');
        $this->cover   = UploadedFile::getInstance($this, 'cover');
        if (count($this->picUrls) < 2 || count($this->picUrls) > 5){
            $this->addError('serverRes', '上传图片要2到5张');
            return false;
        }
        if (!$this->cover){
            $this->addError('serverRes', '封面图必须上传');
            return false;
        }
        if ($this->validate()) {
            $data = [];
            $this->activitySchedule = json_decode($this->activitySchedule,true);
            $this->description      = $this->descriptionHandle();
            $this->picUrls          = $this->picUrlHandle();
            $this->cover            = $this->picUrlHandleCover();
            if (!$this->picUrls['status']) {
                //其他图片添加报错
                $this->addError("serverRes", $this->picUrls['message']);
                return false;
            }
            if (!$this->cover['status']) {
                //封面图片添加报错
                $this->addError("serverRes", $this->cover['message']);
                return false;
            }
            $data['cover'] = Yii::$app->params['imgServerDomin'].'/img/'.$this->cover['data'];
            $data['picUrls'] = array_merge([$data['cover']],$this->picUrls['data']);
            if (!$this->description) {
               $this->addError('serverRes', "活动详细介绍不能为空");
               return false;
            }
            $data_attrs = ['name', 'brief', 'activityKind', 'Venues_id',
                'sponsor', 'highlights', 'stars', 'ability', 'ageGroup',
                'periodInfo', 'insurance', 'priceKind', 'kidPrice', 'activitySchedule',
                'status', 'adultPrice', 'totalPrice', 'description'];
            foreach ($data_attrs as $attr) {
                $data[$attr] = $this->$attr;
            }
            $ret = GaBaseClient::getInstance()->createNewActivity($data);
            if (!$ret['status']) {
                 $this->addError('serverRes', $ret['message']);
                 return false;
            }
         //添加活动成功
            return true;
        }
        //活动验证失败
        return false;
    }
    
    //添加活动
    public function updateActive($oldPic)
    {
        $data       = [];
        $data['id'] = $this->id;
        //编辑基础区域
        if ($this->type == 1) {
            if ($this->validate()) {
                    $data_attrs = ['name', 'activityKind', 'insurance', 'stars', 'ageGroup',
                                  'ability',  'sponsor','Venues_id','highlights', 'periodInfo', 'brief'
                    ];
                    foreach ($data_attrs as $attr) {
                        $data[$attr] = $this->$attr;
                    }
                    $ret = GaBaseClient::getInstance()->modifyActivity($data);
                    //失败
                    if (!$ret['status']) {
                        $this->addError('serverRes', $ret['message']);
                        return false;
                    }
                return true;
            }
           return false;
        }
        
        //编辑活动简介详细描述区域
        if ($this->type == 2){
            if ($this->validate()) {
                $this->description = $this->descriptionHandle();
                if (!$this->description) {
                    //错误信息已存入
                    return false;
                }
                $data['description'] = $this->description;
                $ret = GaBaseClient::getInstance()->modifyActivity($data);
                //失败
                if (!$ret['status']) {
                    $this->addError('serverRes', $ret['message']);
                    return false;
                }
                return true;
            }
            return false;
        }
        
        //编辑活动价格模块
        if ($this->type == 3) {
            if ($this->validate()) {
              $data_attrs = ['priceKind', 'kidPrice','adultPrice', 'totalPrice',];
                foreach ($data_attrs as $attr) {
                    $data[$attr] = $this->$attr;
                }
                $ret = GaBaseClient::getInstance()->modifyActivity($data);
                if (!$ret['status']) {
                    $this->addError('serverRes', $ret['message']);
                    return false;
                }
                return true;
            }
            //验证失败
            return false;
        }
        
        //编辑图片区域
     if ($this->type == 5) {
            //新增图片属性赋值
            $this->picUrls = UploadedFile::getInstances($this, 'picUrls');
            $this->cover   = UploadedFile::getInstance($this, 'cover');
            //提交的旧图地址
            $old           = Yii::$app->request->post('old',[]);
            if ($this->validate()) {
                //取出旧图中封面图
                $oldCover = array_shift($oldPic);
                if ($this->cover) {
                    //有封面图上传
                    $this->cover   = $this->picUrlHandleCover($oldCover);
                    if (!$this->cover['status']) {
                        //封面图片更新报错
                        $this->addError("serverRes", $this->cover['message']);
                        return false;
                    }
                    $data['cover'] = $cover[0] = Yii::$app->params['imgServerDomin'].'/img/'.$this->cover['data'];
                } else {
                    $data['cover'] = $cover[0] = Yii::$app->request->post('cover');
                }
                
                //无论是否有新图上传
                $picUrls2 = $this->picUrlHandle($oldPic,$old);
                if (!$picUrls2['status']) {
                    //封面图片更新报错
                    $this->addError("serverRes", $picUrls2['message']);
                    return false;
                }
                //有新图上传
                if ($this->picUrls) {
                    $pic = array_merge($cover,$picUrls2['data']);
                    //有新图和旧图
                    if ($old) {
                       $pic=  array_merge($cover,$picUrls2['data'],$old);
                       if  (count($picUrls2['data'])+count($old) >5 || count($picUrls2['data'])+count($old) < 2) {
                           $this->addError('serverRes', "上传图片必须是2到5张");
                           return false;
                       }
                    }
                    
                } else {
                    //只有旧图
                    $pic = array_merge($cover,$old);
                    if  (count($old) > 5 || count($old) < 2){
                        $this->addError('serverRes', "上传图片必须是2到5张");
                        return false;
                    }
                }
                $data['picUrls'] = $pic;
                if  (count($data['cover'])!=1) {
                    $this->addError('serverRes', "上传封面图片必须是1张");
                    return false;
                }
                $ret = GaBaseClient::getInstance()->modifyActivity($data);
                //失败
                if (!$ret['status']) {
                    $this->addError('serverRes', $ret['message']);
                    return false;
                }
                //取出原来旧图中封面图
                $oldCover = array_shift($oldPic);
              
                //成功
                return true;
            }
            //验证失败
            return false;
        }
    }
    
    /**
     * 处理picUrls---更新与添加时图片上传
     */
    private function picUrlHandle($oldPic = "", $old = "")
    {
        $model     = new UploadImage();
        $returnArr = [];
        $imgStr    = "";
        //无论是否有新图上传
        //删除旧图
       if (count($oldPic)!=count($old) ) {
                //删除旧图片
                foreach ($oldPic as $onePic) {
                    //如果原来旧图不在上传的旧图中，则就要删除
                    if (!in_array($onePic,$old)){
                        $arr = parse_url($onePic);
                        if (!$arr) {
                            return ['status'=>false,'message' => '圈子图片路径解析失败，请检查图片路径'];
                        }
                        $imgPath = ltrim($arr['path'],'/img/');
                        $imgStr .= $imgPath.",";
                    }
                }
                $imgStr = rtrim($imgStr,',');
                if ($imgStr) {
                    $deleteUrl = Yii::$app->params['imgServerDomin'] .'/api/v1/deleteFiles/1';
                    $res       = $model->deleteImg( $imgStr , $deleteUrl );
                    if (!$res['status']) {
                       return $res;
                    }
                }
        }
        
        if ($this->picUrls) {
            foreach ($this->picUrls as $picUrl) {
                $result = $model->uploadImg($picUrl->tempName,  Yii::$app->params['imgServerDomin'].'/api/v1/activity',true);
                
                //有图片上传出错
                if (!$result['status']){
                    $this->addError('picUrls', $result['message']);
                    return $result;
                }
                //上传成功将图片路径存入数组
                $returnArr[] = Yii::$app->params['imgServerDomin'].'/img/'.$result['data'];
            }
        }
        return ['status' => true,'data'=> $returnArr];
    }
    
    private function picUrlHandleCover($oldCover = "")
    {
        $model  = new UploadImage();
        //编辑时删除封面图
        if ($this->cover && $oldCover) {
            $arr= parse_url($oldCover);
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
        //添加与更新时上传图片
        $result = $model->uploadImg($this->cover->tempName,  Yii::$app->params['imgServerDomin'].'/api/v1/activity',true);
        return $result;
    }
    
    /**
     * 活动详细介绍，正则处理description替换成网页上需求的
     */
    private function descriptionHandle()
    {
        $this->description = preg_replace('/font-size:[ ]*\d+[px]?[;]?/', ' ', $this->description );
        preg_match_all('/\ssrc="(\/upload\/image\/\d{16}\.[a-z]{3})"[\s\/]/',
        $this->description, $matches);
        $new = [];
        foreach ($matches[1] as $v) {
            //处理图片
            $url = $this->picToYdfs($v);
            if (!$url) {
                return false;
            }
            $new[] = '"' . $url . '"';
        }
        //有新图片---用于删除图片
        if ($new) {
           $this->descriptPic = ['status' => true, 'data' =>$new ];
        }   
        array_walk($matches[1], function (&$value) {
            $value = '/"' . str_replace('/', '\/', $value) . '"/';
        });
        $newContent = preg_replace($matches[1], $new, $this->description);
        return $newContent;
    }
    
    /**
     * 处理description中图片链接
     */
    private function picToYdfs($v)
    {
        $model  = new UploadImage();
        $tepImg = rtrim($_SERVER['DOCUMENT_ROOT'],'/').$v;
        //判断上传文件类型
        if (!UploadImage::is_img($tepImg)) {
            //删除临时文件
            @unlink($tepImg);
            $this->addError("serverRes", "封面图片请上传图片格式文件");
            return false;
        }
        $result = $model->uploadImg($tepImg,  Yii::$app->params['imgServerDomin'].'/api/v1/activity',true);
        if (!$result['status']) {
            $this->addError("serverRes", $result['message']);
            return false;
        }
        $fileutil   = new FileUtil();
        //图片上传后返回的图片路径
        $return_url = Yii::$app->params['imgServerDomin'] ."/img/".$result['data'];
        $check      = $fileutil->copyFile(rtrim($_SERVER['DOCUMENT_ROOT'],'/') ."/img/".$result['data'], $tepImg,true);
        if ($check) {
            $fileutil->unlinkFile($tepImg);
            return $return_url;
        }
        //处理图片失败
        //删除临时文件
        @unlink($tepImg);
        $this->addError("serverRes", '处理上传的图片出错！');
        return false;
    }
}