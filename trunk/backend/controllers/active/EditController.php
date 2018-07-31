<?php
namespace backend\controllers\active;

use Yii;
use yii\base\InvalidParamException;
use yii\web\BadRequestHttpException;
use yii\web\Controller;
use yii\filters\VerbFilter;
use yii\filters\AccessControl;
use yii\web\Response;

use common\models\GaBaseClient;
use yii\helpers\Url;
use backend\models\active\ActiveForm;
use backend\models\active\place\ScheduleForm;
use backend\components\Tool;
use yii\data\ArrayDataProvider;
use backend\components\FileUtil;
use yii\web\UploadedFile;
use backend\models\Upload;
use backend\controllers\ActiveController;
use common\models\UploadImage;
/**
 * 活动编辑类
 */
class EditController extends Controller
{
    //添加活动
    public function actionAdd()
    {
        $model = new ActiveForm();
        $model->setScenario('add');
        $res   = false;
        if ($model->load(Yii::$app->request->post())) {
            if ($model->addActive()) {
                $model = new ActiveForm();
                $res   = true;
            } else {
                $err = $model->getFirstErrors();
                $model->cover   = "";
                $model->picUrls = [];
                echo "<script>alert('添加失败！".array_shift($err)."');</script>";
            }
        }
        return $this->render('add', [
                    'model'  => $model,
                    'res'    => $res,
        ]);
    }
    

    //活动场次添加与更新时弹框
    public function actionSchedule()
    {
        $model = new ScheduleForm();
        Yii::$app->response->format = Response::FORMAT_JSON;
        if (isset(Yii::$app->request->post()['schedule_data'])) {
            //获取场次所有数据
            $schedule = Yii::$app->request->post()['schedule_data'];
        } else {
            $schedule = [];
        }
        //更新时
        if (isset(Yii::$app->request->post()['action']) && Yii::$app->request->post()['action'] == 'update') {
            $info   = [];
            $post   = Yii::$app->request->post();
            $action = $post['action'];
            $key    = $post['key'];
            if (!isset($schedule[$key])) {
                return ['code' => 0, 'msg' => '无此活动信息'];
            }
            $schedule[$key]['activityDate'] = $schedule[$key]['activityDate']." ".$schedule[$key]['activityTime'];
            $info['ScheduleForm'] = $schedule[$key];
            //更新时视图显示
            $model->setScenario('update');
            $model->load($info);
        }
        $view  =  $this->renderPartial('schedule', [
                    'model'     => $model,
                    'schedule'  => $schedule,
                    'action'    => empty($action) ? 'add' : $action,
                    'key'       => empty($key)    ? '0'   : $key,
        ]);
        return ['code' => 1, 'data' => $view];
    }
    
    //添加活动时场次增删改操作
    public function actionSchedule_validate()
    {
        $model = new ScheduleForm();
        $schedules = [];
        if (Yii::$app->request->isAjax) {
            Yii::$app->response->format = Response::FORMAT_JSON;
             if ($post = Yii::$app->request->post()) {
                 //更新与添加时将当前记录数据赋值到model中，以便验证，添加场次中编辑与添加完成后是一起添加场次的
             	  if (isset($post['minNumber'])){ 
             	    $info = [];
             	    $model->setScenario('add');
             	    $activeityDate        = explode(' ', $post['activityDate']);
             	    $info['ScheduleForm'] = $post;
             	    $info['ScheduleForm']['activityDate'] = $activeityDate[0];
             	    $info['ScheduleForm']['activityTime'] = $activeityDate[1];
             	    $model->load($info);
                 }
                 if (isset($post['schedule_data'])) {
                     $schedules = $post['schedule_data'];
                 }
                 if(isset($post['key'])) {
                 	$key        = $post['key'];
                 	$model->id  = isset($post['schedule_data'][$key]['id'])?$post['schedule_data'][$key]['id']:"";
                     //删除场次
                     if (isset($post['action']) && $post['action'] == 'delete') {
                         $model->setScenario('delete');
                         unset($schedules[$key]);
                     } else {
                         $model->setScenario('update');
                         if ($model->validate()) {
                          //修改场次,注意日期问题
                          //将修改记录数据返回给视图页
                            $schedule = [];
                            $schedule['id']            = $model->id;
                            $schedule['minNumber']     = $model->minNumber;
                            $schedule['stock']         = $model->stock;
                            $schedule['activityDate']  = $model->activityDate;
                            $schedule['activityTime']  = $model->activityTime;
                            $schedule['regStart']      = $model->regStart;
                            $schedule['regEnd']        = $model->regEnd;
                            $schedules[$key] = $schedule; //修改场次
                         } else {
                             return Tool::echoError($model);
                         }
                     } 
                 } else { 
                     //增加场次
                     $model->setScenario('add');
                     if ($model->validate()) {
                         if ($schedules && !is_array($schedules)){
                              $schedules = json_decode($schedules, true);
                         }
                         //将新增记录数据返回给视图页
                         $schedule = [];
                         $schedule['minNumber']     = $model->minNumber;
                         $schedule['stock']         = $model->stock;
                         $schedule['activityDate']  = $model->activityDate;
                         $schedule['activityTime']  = $model->activityTime;
                         $schedule['regStart']      = $model->regStart;
                         $schedule['regEnd']        = $model->regEnd;
                         if (!is_array($schedules)) {
                             $schedules = [];
                         }
                         $schedules[] = $schedule; 
                     } else {
                         return Tool::echoError($model);
                     }
                 }
                    $provider = new ArrayDataProvider([
                                        'allModels' => $schedules,
                    ]);
                    $view = $this->renderPartial('scheduletable', [
                            'schedules'    => $schedules,
                            'dataProvider' => $provider,
                    ]);
                    return ['code' => 1, 'data' => $view];
            } 
            return ['code' => 0, 'msg' => json_encode($model)];
        }
    }
    
    //编辑活动时场次修改操作，单条修改
    public function actionSchedule_update()
    {
        $model = new ScheduleForm();
        $model->setScenario('update');
        if (Yii::$app->request->isAjax && Yii::$app->request->post()){
            Yii::$app->response->format = Response::FORMAT_JSON;
            //验证提交的场次数据
            $post = Yii::$app->request->post();
            $scheduledata = [];
            $scheduledata['ScheduleForm'] = $post;
            $activeityDate = explode(' ', $post['activityDate']);
            $scheduledata['ScheduleForm']['activityDate'] = $activeityDate[0];
            $scheduledata['ScheduleForm']['activityTime'] = $activeityDate[1];
            $model->load($scheduledata);
            if ($model->validate()) {
                $key = $scheduledata['ScheduleForm']['key'];
                $ret = GaBaseClient::getInstance()->modifyActivitySchedule($scheduledata['ScheduleForm']);
                if ($ret['status']) {
                   return ['code' => 1, "msg" => "修改成功"];
                } else {
                   return ['code' => 0, "msg" => $ret['message']];
                } 
            } else {
                $err = $model->getFirstErrors();
                return ['code' => 0, "msg" => array_shift($err)];
            }
        }
    }
    
    
    //编辑活动时场次添加操作，单条修改
    public function actionSchedule_add()
    {
        $model = new ScheduleForm();
        $model->setScenario('add');
        if (Yii::$app->request->isAjax && Yii::$app->request->post()) {
            Yii::$app->response->format = Response::FORMAT_JSON;
            //验证提交的场次数据
            $post = Yii::$app->request->post();
            $scheduledata=[];
            $scheduledata['ScheduleForm'] = $post;
            $activeityDate   = explode(' ', $post['activityDate']);
            $scheduledata['ScheduleForm']['activityDate'] = $activeityDate[0];
            $scheduledata['ScheduleForm']['activityTime'] = $activeityDate[1];
            $model->load($scheduledata);
            if ($model->validate()) {
                $scheduleinfo['activitySchedule'] = $scheduledata['ScheduleForm'];
                $ret = GaBaseClient::getInstance()-> createActivitySchedule([
                        'activityId'       => $post['activityId'],
                        'activitySchedule' => $scheduleinfo,
                ]);
                if ($ret['status']) {
                    return ['code' => 1, "msg" => "添加成功"];
                } else {
                    $err = $model->getFirstErrors();
                    return ['code' => 0, "msg" => array_shift($err)];
                }
            }
            $err = $model->getFirstErrors();
            return ['code' => 0, "msg" => array_shift($err)];
        }
    }
    
    //编辑活动时场次删除操作，单条删除
    public function actionSchedule_del()
    {
        if (Yii::$app->request->isAjax && Yii::$app->request->post()) {
            Yii::$app->response->format = Response::FORMAT_JSON;
            $post = Yii::$app->request->post();
            $ret  = GaBaseClient::getInstance()->deleteActivitySchedule([
                    'activityId' => $post['activityId'],
                    'scheduleId' => $post['schedule_id'] ,
            ]);
            if (!$ret['status']) {
                return ['code' => 0, 'msg' => $ret['message'] ];
            }
            return ['code' => 1, 'msg' => "删除成功" ];
        }
    }
    
    public function actionImgdelete()
    {    //ajax删除预览图片
        if(Yii::$app->request->post()){
            Yii::$app->response->format = Response::FORMAT_JSON;
            return ["ok"];
        }
    }
    
    //正则获取所有旧图
    private function descriptionPic($description)
    {
        preg_match_all('/\ssrc="(http:\/\/'.Yii::$app->params['imgUrl'].'\/img\/activity\/[A-Za-z0-9]{32})"[\s\/]/',
        $description, $matches);
        return $matches[1];
    }
    
    public function actionUpdate()
    {
        $model = new ActiveForm();
        //view页面传来的活动id与编辑的区域类型
        $get   = Yii::$app->request->get();
        $id    = $get['id'];
        $type  = $get['type'];
        //价格是否可编辑，flag=1不可编辑，flag=2可编辑
        $flag  = isset($get['flag'])?$get['flag']:"";
        if (!($id && $type)) {
            echo "<script>alert('非法访问');</script>";
            echo "<script>location.href = window.location.pathname+'?r=active/show_active_list'</script>";
            exit;
        };
        $ret = GaBaseClient::getInstance()->getActivityDetailBg(['where' => ['id' => $id]]);
        if (!$ret['status']) {
            echo "<script>alert('".$ret['message']."');</script>";
            echo "<script>location.href = window.location.pathname+'?r=active/show_active_list'</script>";
            exit;
        }
        $model->setScenario('updatetype'.$type);
        if (Yii::$app->request->post()) {
            $post = Yii::$app->request->post()['ActiveForm'];
            if ($flag == 1 && ($post['priceKind'] || $post['totalPrice'] || $post['adultPrice'] || $post['kidPrice'] )) {
                echo "<script>alert('活动已经产生购买！不能修改');</script>";
                echo "<script>location.href = window.location.pathname+'?r=active/view&id=$id'</script>";
                exit;
            }
            if ($model->load(Yii::$app->request->post())) {
                $model->type = $type;
                $oldPic      = $ret['data']['picUrls'];
                $model->setScenario('updatetype'.$type);
                if ($model->updateActive($oldPic)) {
                    if ($type == 2) {
                        $description              = $ret['data']['description'];
                        $olddescriptionPic        = $this->descriptionPic($description);
                        $model->olddescriptionPic = $olddescriptionPic;
                        $alldescriptionPic        = $this->descriptionPic($model->description);
                        $imgStr = "";
                        foreach ($olddescriptionPic as $onedescriptionPic) {
                            //如果原来旧图不在上传的旧图中，则就要删除
                            if (!in_array($onedescriptionPic,$alldescriptionPic)){
                                $arr = parse_url($onedescriptionPic);
                                if (!$arr) {
                                    return ['status'=>false,'message' => '圈子图片路径解析失败，请检查图片路径'];
                                }
                                $imgPath = ltrim($arr['path'],'/img/');
                                $imgStr .= $imgPath.",";
                            }
                        }
                        $imgStr = rtrim($imgStr,',');
                        if ($imgStr) {
                            $model2 = new UploadImage();
                            $deleteUrl = Yii::$app->params['imgServerDomin'] .'/api/v1/deleteFiles/1';
                            $res       = $model2->deleteImg( $imgStr , $deleteUrl );
                            if (!$res['status']) {
                                echo "<script>alert('活动详细图片删除失败！".$res['message']."');</script>";
                                echo "<script>location.href = window.location.pathname+'?r=active/edit/update&type=$type&id=$id'</script>";
                                exit;
                            }
                        }
                        
                    }
                   
                    echo "<script>alert('修改成功');</script>";
                    echo "<script>location.href = window.location.pathname+'?r=active/view&id=$id'</script>";
                    exit;
                }
                //获取模型报错信息
                $err = $model->getFirstErrors();
                echo "<script>alert('修改失败！".array_shift($err)."');</script>";
                echo "<script>location.href = window.location.pathname+'?r=active/edit/update&type=$type&id=$id'</script>";
                exit;
            }
           
        }
            //处理提交的数据用于给模型属性赋值
            $data = [];
            foreach ($ret as $k=>$v){
                if ($k == "data") {
                    $data['ActiveForm']=$v;
                }
            }
            $model->load($data);
            //编辑区域类型一时给年龄段与活动地点id赋值
            if ($type == 1) {
                $model->ageGroup  = $ret['data']['ageRange'];
                $model->Venues_id = $ret['data']['venue']['id'];
            }
            return $this->render('update', [
                    'model' => $model,
                    'data'  => $ret['data']['schedule'],
                    'type'  => $type,
                    'id'    => $model->id
            ]);
    }
}