<?php
namespace common\models;

use yii;
use yii\base\Model;
use yii\web\UploadedFile;

/**
 * UploadForm is the model behind the upload form.
 */
class UploadImage extends Model
{
    /**
     * @var UploadedFile file attribute
     */
    public $file;
    public static $imgTypeArr = ['jpg','jpeg','png'];
    /**
     * curlObj
     */
    private static $ch;
    
    /**
     * setTimeour
     */
    public static $timeOut = 20;
    
    const GET_METHOD  = 'GET';
    const POST_METHOD = 'POST';
    const PUT_METHOD  = 'PUT';
    const DEL_METHOD  = 'DELETE';
    
    /**
     * @return array the validation rules.
     */
    public function rules()
    {
        return [
            [['file'], 'file', 'extensions' => ['png', 'jpg', 'gif', 'jpeg'], 'maxSize' => 1024*500],
        ];
    }
    
    
    public function attributeLabels()
    {
        return [
                'file'   => '图片',
        ];
    }
    
    //剪切图片
    public function clipsize($src_img, $dst_img, $clip_x, $clip_y, $clip_w, $clip_h, $dst_w, $dst_h)
    {
        // 获取原图尺寸
        list($src_w,$src_h,$imgtype)=@getimagesize($src_img);
    
        if(empty($src_w) || empty($src_h) || empty($imgtype)){
            return ['code' => 0, 'msg' => '获取文件失败，请重试'];
        }
        if($src_w < $clip_w || $src_h < $clip_h){
            return ['code' => 0, 'msg' => '选取的图片尺寸不能大于原图'];
        }
        if($clip_w < $dst_w){
            $dst_w = $clip_w;
        }
        if($clip_h < $dst_h){
            $dst_h = $clip_h;
        }
    
        //1 为 GIF 格式、 2 为 JPEG/JPG 格式、3 为 PNG 格式
        if($imgtype !=1 && $imgtype != 2 && $imgtype != 3){
            $fileType =array('.gif'=>1,'.png'=>3,'.jpg'=>2,'.jpeg'=>2);
            $current_type = strtolower(strrchr($src_img, '.'));
            if(!in_array($current_type, $config['fileType'])){
                return ['code' => 0, 'msg' => '类型不支持，请重试'];
            }
            $imgtype = $fileType[$current_type];
        }
        
        // 剪裁
        $source = NULL;
        if($imgtype == 1){
            $source=@imagecreatefromgif($src_img);
        }else if($imgtype ==2){
            $source=@imagecreatefromjpeg($src_img);
        }else if($imgtype == 3){
            $source=@imagecreatefrompng($src_img);
        }
        if(empty($source)){
            return ['code' => 0, 'msg' => '类型不支持，请重试'];
        }
        $croped=@imagecreatetruecolor($clip_w, $clip_h);//($w, $h);
        if(empty($croped)){
            return ['code' => 0, 'msg' => '裁剪失败1，请重试'];
        }
        $ret = @imagecopy($croped, $source, 0, 0, $clip_x, $clip_y, $clip_w, $clip_h);
        if($ret == false){
            return ['code' => 0, 'msg' => '裁剪失败2，请重试'];
        }
    
        // 缩放
        //$scale = $dst_w/$clip_w;
        $target = @imagecreatetruecolor($dst_w, $dst_h);
        if(empty($target)){
            return ['code' => 0, 'msg' => '裁剪失败3，请重试'];
        }
        $final_w = $dst_w;//intval($w*$scale);
        $final_h = $dst_h;//intval($h*$scale);
        $ret = @imagecopyresampled($target, $croped, 0, 0, 0, 0, $final_w, $final_h, $clip_w, $clip_h);//$w, $h);
        if($ret == false){
            return ['code' => 0, 'msg' => '裁剪失败4，请重试'];
        }
    
        // 保存
        $ret = @imagejpeg($target, $dst_img);
        if($ret == false){
            return ['code' => 0, 'msg' => '裁剪失败5，请重试'];
        }
        $ret = @imagedestroy($target);
        if($ret == false){
            return ['code' => 0, 'msg' => '裁剪失败6，请重试'];
        }
        return ['code' => 1, 'msg' => '成功'];
    }
    
    //单图上传与修改
    //保存图片$imgPath临时图片路径，$url图片保存接口路径，旧图路径-->返回数组
    //失败status为false,成功status为true,此时data为图片路径,message为请求状态信息,$url为'/'开头的路径
    public function uploadImg($imgPath  , $url , $httpBuild = true)
    {
        if (!($imgPath && file_exists($imgPath)) ) {
            return ['status' => false, 'message' => '未找到上传的图片'];
        }
        $params = ['image' => base64_encode(file_get_contents($imgPath))];
        if ($httpBuild){
            $params = http_build_query($params);
        }
      return json_decode(self::curlRequest($url, $params, self::POST_METHOD),true);
    }
    
    //单图删除
    public function deleteImg($imgPath , $deleteUrl = "" , $httpBuild = true)
    {
        if (!$imgPath) {
            return ['status' => false, 'message' => '未找到要删除的图片'];
        }
        //将旧图路径传至接口
        $params = ['files' => $imgPath];
        if ($httpBuild){
            $params = http_build_query($params);
        }
        return json_decode(self::curlRequest($deleteUrl, $params, self::DEL_METHOD),true) ;
    }
    
    //验证文件类型
    static public function checkFileType($filename)
    {
        $file     = fopen($filename, 'rb');
        $bin      = fread($file, 2);
        fclose($file);
        $strInfo  = @unpack("c2chars", $bin);
        $typeCode = intval($strInfo['chars1'] . $strInfo['chars2']);
        $fileType = '';
        switch ($typeCode)
        {
            case 7790   : $fileType = 'exe';  break;
            case 7784   : $fileType = 'midi'; break;
            case 8297   : $fileType = 'rar';  break;
            case 255216 : $fileType = 'jpg';  break;
            case 7173   : $fileType = 'gif';  break;
            case 6677   : $fileType = 'bmp';  break;
            case 13780  : $fileType = 'png';  break;
            case 103117 : $fileType = 'txt';  break;
            default     : $fileType = 'unknown' . $typeCode;  break;
        }
        if ($strInfo['chars1'] == '-1' && $strInfo['chars2'] == '-40') {
            return 'jpg';
        }
        if ($strInfo['chars1'] == '-119' && $strInfo['chars2'] == '80') {
            return 'png';
        }
        return $fileType;
    }
    
    //验证是否是图片文件
    public static function is_img($filename){
        $fileType = self::checkFileType($filename);
        if (!in_array($fileType, self::$imgTypeArr)) {
            return false;
        }
        return true;
    }
    
    /**
     * curl http request
     * @param  array $data
     * @return mixed
     */
    private static function curlRequest($url = '', $params = '', $type = self::GET_METHOD)
    {

        /*if (empty(self::$ch)) {*/
            self::$ch = curl_init();
        /*}*/
        curl_setopt (self::$ch, CURLOPT_URL, $url);
        curl_setopt (self::$ch, CURLOPT_HEADER, 0);
        curl_setopt (self::$ch, CURLOPT_RETURNTRANSFER, 1);
        curl_setopt (self::$ch, CURLOPT_FORBID_REUSE, 1);
        curl_setopt (self::$ch, CURLOPT_TIMEOUT, self::$timeOut);
        switch ($type) {
            case "GET"    : curl_setopt(self::$ch, CURLOPT_HTTPGET, true);
            break;
            case "POST"   : curl_setopt(self::$ch, CURLOPT_POST,true);
            curl_setopt(self::$ch, CURLOPT_POSTFIELDS, $params);
            break;
            case "PUT"    : curl_setopt (self::$ch, CURLOPT_CUSTOMREQUEST, self::PUT_METHOD);
            curl_setopt (self::$ch, CURLOPT_POSTFIELDS, $params);
            break;
            case "DELETE" : curl_setopt (self::$ch, CURLOPT_CUSTOMREQUEST, self::DEL_METHOD);
            curl_setopt (self::$ch, CURLOPT_POSTFIELDS, $params);
            break;
        }
        $result = curl_exec(self::$ch);
        if (empty($result)) {
            return json_encode(array('status' => false, 'data' => 1006, 'message' => '连接到API失败'));
        } else {
            return $result;
        }
    }
}