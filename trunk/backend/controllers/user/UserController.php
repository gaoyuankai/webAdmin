<?php
namespace backend\controllers\user;

use Yii;
use backend\models\user\UserForm;
use yii\base\InvalidParamException;
use yii\web\BadRequestHttpException;
use yii\web\Controller;
use yii\filters\VerbFilter;
use yii\filters\AccessControl;
use yii\web\Response;
use common\models\GaBaseClient;
use yii\helpers\Url;
use yii\data\Pagination;
use yii\data\ActiveDataProvider;
use yii\data\ArrayDataProvider;
use backend\models\message\MessageForm;
use backend\models\lucky\LuckyForm;
use backend\models\user\AdditioninfoForm;
use yii\helpers\ArrayHelper;
/**
 * User controller
 * 用户管理
 */
class UserController extends Controller{
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
    
    //用户列表
    public function actionSelect()
    {
            $model = new UserForm();
            $page  = Yii::$app->request->get('page', 1);
            $users = ['list' => [], 'count' => 0];
            $model->load(Yii::$app->request->get());
            $users = $model->select($page, self::PAGE_SIZE);
            $provider = new ArrayDataProvider([
                                'allModels' => $users['list'],
                            ]);
            $pages    = new Pagination([
                                'totalCount' => $users['count'],
                                'defaultPageSize' => self::PAGE_SIZE
                            ]);
            return $this->render('select', [
                    'model'        => $model,
                    'dataProvider' => $provider,
                    'pagination'   => $pages,
            ]);
    }
    
    //返回可发红包数组
    private function getLucky(){
        //显示用的数组
        $display_data = []; 
        //所有数据
        $data = [];
        //获取可用时间内的红包
        $ret = GaBaseClient::getInstance()->getCouponList([
                                       'where'  =>[
                                                'expireTo' => array('>'=>date("Y-m-d H:i:s",time())),
                                                'totalQty' => ['>' => 'sendOutQty'],
                                            ],
                                       ]);
        //获取无期限的红包
        $ret_forever = GaBaseClient::getInstance()->getCouponList([
                        'where'  =>[
                                    'expireTo' => '0000-00-00 00:00:00',
                                    'totalQty' => ['>' => 'sendOutQty'],
                            ],
                        ]);
        if ($ret['status'] && isset($ret['data']['list']) && is_array($ret['data']['list'])) {
            foreach ($ret['data']['list'] as $lucky){
                $couponCount = $lucky['totalQty'] - $lucky['sendOutQty'];
                if ($couponCount > 0) {
                    $display_data[$lucky['id']] = $lucky['couponName'].' (剩余'.$couponCount.'个)';
                    $data[$lucky['id']] = $lucky;
                }
            }
        }
        if ($ret_forever['status'] && isset($ret_forever['data']['list']) && is_array($ret_forever['data']['list'])) {
            foreach ($ret_forever['data']['list'] as $lucky){
                $couponCount = $lucky['totalQty'] - $lucky['sendOutQty'];
                if ($couponCount > 0) {
                    $display_data[$lucky['id']] = $lucky['couponName'].' (剩余'.$couponCount.'个)';
                    $data[$lucky['id']] = $lucky;
                }
            }
        }
        return ['display' => $display_data, 'data' => $data];
    }
    
    //弹出发送消息红包面板
    public function actionMessage(){
        $model = new MessageForm();
        if (Yii::$app->request->isAjax) {
            $model->setData(Yii::$app->request->post('ids'));
            Yii::$app->response->format = Response::FORMAT_JSON;
            if ($model->usernames != 'null') {
                $view = $this->renderPartial('message', [
                                'model' => $model,
                ]);
                return ['code' => 1, 'data' => $view];
            } else {
                return ['code' => 0, 'msg' => Yii::t('yii', 'error', ['name' => '请选择用户'])];
            }
        }
    }
    
    //发送消息
    public function actionSendmessage()
    {
        $model = new MessageForm();
        if (Yii::$app->request->isAjax) {
            Yii::$app->response->format = Response::FORMAT_JSON;
            if ($model->load(Yii::$app->request->post()['updateDatas'], '')) {
                return $model->message();
            }
        }
    }
    
    //弹出发送红包面板
    public function actionLucky()
    {
        $model = new LuckyForm();
        if (Yii::$app->request->isAjax) {
            Yii::$app->response->format = Response::FORMAT_JSON;
            $data = Yii::$app->request->post('ids');
            if (count($data) <= 0) {
                return ['code' => 0, 'msg' => '请选择用户'];
            }
            $model->data      = ArrayHelper::index($data, 'nickName');
            $model->usernames = implode(',',ArrayHelper::getColumn($data, 'nickName'));
            if ($model->usernames != 'null') {
                $lucky = $this->getLucky();
                $model->lucky_config = $lucky['data'];
                $view = $this->renderPartial('lucky', [
                                                'model'        => $model,
                                                'lucky'        => $lucky['display'],
                                            ]);
                return ['code' => 1, 'data' => $view];
            } else {
                return ['code' => 0, 'msg' => Yii::t('yii', 'error', ['name' => '请选择用户'])];
            }
        }
    }
    
    //发送红包
    public function actionSendlucky()
    {
        $model = new LuckyForm();
        if (Yii::$app->request->isAjax) {
            Yii::$app->response->format = Response::FORMAT_JSON;
            if($model->load(Yii::$app->request->post()['updateDatas'], '')){
                return $model->sendlucky();
            }
        }
    }
    
    
    //专属服务配置，用户补充信息，保险列表等
    public function actionAdditioninfo()
    {   
        $model  = new AdditioninfoForm();
        $model->setScenario('select');
        $page   = Yii::$app->request->get('page', 1);
        $show   = Yii::$app->request->get('show', '');
        $User_id      = "";
        $activityDate = [];
        $Activity_id  = "";
        $status       = "";
        $ActivitySchedule_id ="";
        //订单列表链接过来的订单号
        $orderNumber = Yii::$app->request->get('orderNumber',"");
        $model->orderNumber = $orderNumber;
        //查询传来的筛选字段
        if (isset(Yii::$app->request->get()["AdditioninfoForm"])) {
            $User_id             = Yii::$app->request->get()["AdditioninfoForm"]['User_id'];
            $Activity_id         = Yii::$app->request->get()["AdditioninfoForm"]['Activity_id'];
            $ActivitySchedule_id = Yii::$app->request->get()["AdditioninfoForm"]['ActivitySchedule_id'];
            $activityDate        = Yii::$app->request->get()["AdditioninfoForm"]['activityDate'];
            $status              = Yii::$app->request->get()["AdditioninfoForm"]['status'];
            $orderNumber         = Yii::$app->request->get()["AdditioninfoForm"]['orderNumber'];
            //时间提交后处理成数组
            if($activityDate){
                //分割成数组
                $time  = explode("到",$activityDate);
                $start = strtotime($time[0]);
                $end   = strtotime($time[1]);
                if($start>$end){
                    echo "<script>alert('选择的评论起始时间应该小于结束时间');</script>";
                    echo "<script>location.href = window.location.pathname+'?r=circle/comment/list'</script>";
                    exit;
                }
                //有空格去除
                $activityDate = [ '>=' => trim($time[0]),
                                  '<=' => trim($time[1])
                ];
            }
            $model->orderNumber = $orderNumber;
        }
        //获取保险信息
        $param = [
            'where'        => [
                'User_id'             => $User_id,
                'ActivitySchedule_id' => $ActivitySchedule_id,
                'activityDate'        => $activityDate,
                'Activity_id'         => $Activity_id,
                'status'              => $status,
                'orderNumber'         => $orderNumber
             ],
        ];
        if ($show) {
                $param['offset'] = 0;
                $param['limit']  = (int) $show;
            } else {
                $param['offset'] = ($page - 1) * self::PAGE_SIZE;
                $param['limit']  = self::PAGE_SIZE;
            }
        $ret = GaBaseClient::getInstance()->getOrderInsuranceList($param);
        $data=[];
        foreach($ret['data']['list'] as $v){
            if($v['identityNumber']){
                $data[]=$v;
            }
        }
        //获取接口数据报错
        if (!$ret['status']) {
            echo "<script>alert('获取数据错误');</script>";
            echo "<script>location.href = window.location.pathname+'?r=user/user/additioninfo'</script>";
            exit;
        }
        $provider = new ArrayDataProvider([
                'allModels' => $data,
                ]);
        $pages = new Pagination([
                'totalCount'      => count($data),
                'defaultPageSize' => self::PAGE_SIZE
                ]);

        return $this->render('additioninfo', [
                'model'        => $model,
                'dataProvider' => $provider,
                'pagination'   => $pages,
         ]);
        }
}