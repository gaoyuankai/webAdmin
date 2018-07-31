<?php
namespace backend\controllers\trade;

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
use backend\models\trade\TradeForm;
use backend\components\Tool;

/**
 * Trade controller
 * 订单管理
 */
class TradeController extends Controller{
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
    
    public function actionList()
    {
        //要获取订单对应的保险是否有
        $model = new TradeForm();
        $data  = [];
        $model->setScenario('select');
        $page   = Yii::$app->request->get('page', 1);
        $show   = Yii::$app->request->get('show', '');
        $model->load(Yii::$app->request->get());
        $trades   = $model->select($page,self::PAGE_SIZE,$show);
        $provider = new ArrayDataProvider([
            'allModels' => $trades['list'],
        ]);
        $pages    = new Pagination([
            'totalCount'      => $trades['count'],
            'defaultPageSize' => self::PAGE_SIZE
        ]);
     
        return $this->render('list', [
            'model'        => $model,
            'dataProvider' =>$provider,
            'pagination'   => $pages,
        ]);
    }
    
    public function actionDetail()
    {
        $model = new TradeForm();
        if (Yii::$app->request->isAjax) {
            Yii::$app->response->format = Response::FORMAT_JSON;
            if (isset(Yii::$app->request->post()['data'])) {
                $data = Yii::$app->request->post()['data'];
                $detail = $model->getDetail($data);
                if ($detail === true) {
                    $view = $this->renderPartial('detail', [
                        'model'       => $model,
                        'tradeStatus' => Tool::TRADE_STATUS,
                        'isDetail'    => true,
                    ]);
                    return ['code' => 1, 'data' => $view];
                } else {
                    return ['code' => 0, 'msg' => $detail];
                }
            }
        }
    }
    
    public function actionRefund()
    {
        $model = new TradeForm();
        if (Yii::$app->request->isAjax) {
            Yii::$app->response->format = Response::FORMAT_JSON;
            if (Yii::$app->request->post()['data']) {
                $data   = Yii::$app->request->post()['data'];
                $detail = $model->getRefund($data);
                if ($detail === true) {
                    $view = $this->renderPartial('detail', [
                        'model'       => $model,
                        'tradeStatus' => Tool::TRADE_STATUS,
                    ]);
                    return ['code' => 1, 'status' => $model->status ,'data' => $view];
                } else {
                    return ['code' => 0, 'msg' => $detail];
                }
            }
        }
    }
    
    public function actionRefundhandle()
    {
        $model = new TradeForm();
        if (Yii::$app->request->isAjax) {
            Yii::$app->response->format = Response::FORMAT_JSON;
            $refundData = Yii::$app->request->post()['refundData'];
            if ($refundData) {
                return $model->refundHandle($refundData);
            }
        }
    }
}