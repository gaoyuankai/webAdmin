<?php
namespace backend\controllers\lucky;

use Yii;
use yii\base\InvalidParamException;
use yii\web\BadRequestHttpException;
use yii\web\Controller;
use yii\filters\VerbFilter;
use yii\filters\AccessControl;
use yii\web\Response;
use common\models\GaBaseClient;
use yii\helpers\Url;
use yii\data\Pagination;
use yii\data\ArrayDataProvider;
use backend\models\lucky\LuckyForm;
use backend\models\lucky\LuckyListForm;
use backend\components\Tool;

/**
 * Lucky controller
 * 红包管理
 */
class LuckyController extends Controller{
    const  PAGE_SIZE = 20;
    
    public function behaviors()
    {
        return [
            'access' => [
                'class' => AccessControl::className(),
                'rules' => [
                    [
                        'allow' => false,
                        'roles' => ['?'],
                    ],
                    [
                        'allow' => true,
                        'roles' => ['@'],
                    ],
                ],
            ],
            'verbs' => [
                'class'   => VerbFilter::className(),
                'actions' => [
                    'logout' => ['post'],
                ],
            ],
        ];
    }
    
    //获取红包列表
    public function actionList() 
    {
        $model = new LuckyListForm();
        $page  = Yii::$app->request->get('page', 1);
        $model->setScenario('select');
        $model->load(Yii::$app->request->get());
        $luckys   = $model->select($page,self::PAGE_SIZE);
        $provider = new ArrayDataProvider([
            'allModels' => isset($luckys) ? $luckys['list'] : [],
        ]);
        $pages    = new Pagination([
            'totalCount'      => isset($luckys) ? $luckys['count'] : 0,
            'defaultPageSize' => self::PAGE_SIZE
        ]);
        return $this->render('index', [
            'model' => $model,
            'dataProvider'=>$provider,
            'pagination' => $pages,
            'luckyStatus' => Tool::LUCKY_STATUS,
       ]);
    }
    
    //更新红包
    public function actionUpdate()
    {
        $model = new LuckyListForm();
        Yii::$app->response->format = Response::FORMAT_JSON;
        //根据红包id修改红包信息
        if (Yii::$app->request->isAjax && Yii::$app->request->post()) {
            $post   = Yii::$app->request->post();
            $action = $post['action'];
            $model->setScenario($action);
            if ($model->load(Yii::$app->request->post())) {
                $model->isExpire = $post['LuckyListForm']['isExpire'];
                //非固定时间
                if ($model->isExpire == 1) {
                    if ($model->expireDays <= 0) {
                        return ['code' => 0, 'msg' => "有效天数为正整数"];
                    }
                    $model->expireFrom = $model->expireTo = "0000-00-00";
                }
                //固定时间
                if ($model->isExpire === 0) {
                    $flag1 = $this->checkDateIsValid($model->expireFrom,array("Y-m-d"));
                    $flag2 = $this->checkDateIsValid($model->expireTo,array("Y-m-d"));
                    if (!($flag1 && $flag2)) {
                        return ['code' => 0, 'msg' => "日期格式不正确"];
                    }
                }
                if ($model->kind == 2){
                    $model->discount = $model->discountPrice;
                }
                if ($model->$action()) {
                  return ['code' => 1, 'msg' => "操作成功"];
                }
                $err = $model->getFirstErrors();
                return ['code' => 0, 'msg' => array_shift($err)];
             }
           //Load失败
           $err = $model->getFrstErrors();
           return ['code' => 0, 'msg' => array_shift($err)];
        }
        //弹框显示
        if (Yii::$app->request->get()) {
            $action =Yii::$app->request->get('action');
            //如果是更新
            if ($action == "update") {
              $id = Yii::$app->request->get('id');
              //获取单条数据
              $ret = GaBaseClient::getInstance()->getCouponById([ "where" => [ 'id' => $id ] ]);
              //获取接口数据报错
              if (!$ret['status']) {
                  return ['code' => 0, 'msg' => $ret['message']];
              }
              //获取红包信息数据为空
              if (!$ret['data']['info']) {
                  return ['code' => 0, 'msg' => "没有该红包信息"];
              }
              $info = [];
              $info['LuckyListForm'] = $ret['data']['info'];
              $info['LuckyListForm']['isExpire'] = 0;
              //红包有有效天数则为非固定时间,expireDays存在并且不小于一天
              if (isset($info['LuckyListForm']['expireDays']) && $info['LuckyListForm']['expireDays'] > 0) {
                  $info['LuckyListForm']['isExpire'] = 1;
              }
              if ($info['LuckyListForm']['isExpire'] === 0){
                  //固定时间去除红包有有效天数
                  if (isset($info['LuckyListForm']['expireDays'])) {
                      unset($info['LuckyListForm']['expireDays']);
                  }
              }
              if ($info['LuckyListForm']['kind'] == 2){
                      //显示折扣数
                      $info['LuckyListForm']['discountPrice'] = $info['LuckyListForm']['discount'];
              }
              $model->setScenario($action);
              $model->load($info);
              $model->isExpire = $info['LuckyListForm']['isExpire'];
              $model->expireFrom = substr($model->expireFrom,0,10);
              $model->expireTo   = substr($model->expireTo,0,10);
            }
                //添加时显示时间为空
                if ($model->expireFrom == "0000-00-00") {
                    $model->expireFrom = $model->expireTo = "";
                }
                $view = $this->renderPartial('update', [
                        'model' => $model,
                ]);
                return ['code' => 1, 'data' => $view];
         }
    }
    
    //删除红包
    public function actionDel()
    {
        if (Yii::$app->request->isAjax) {
            Yii::$app->response->format = Response::FORMAT_JSON;
            $id     = Yii::$app->request->post()['id'];
            $ret    = GaBaseClient::getInstance()->deleteCoupon(['id' => $id]);
            if(!$ret['status']){
                return ['code' => 0, 'msg' => $ret['message']];
            }
            return ['code' => 1, 'msg' => "删除成功" ];
        }
    }
    
    //校验日期的有效性
    public function checkDateIsValid($date, $formats = array("Y-m-d", "Y/m/d")) {
        $unixTime = strtotime($date);
        if (!$unixTime) { //strtotime转换不对，日期格式显然不对。
            return false;
        }
        //校验日期的有效性，只要满足其中一个格式就OK
        foreach ($formats as $format) {
            if (date($format, $unixTime) == $date) {
                return true;
            }
        }
        return false;
    }
}