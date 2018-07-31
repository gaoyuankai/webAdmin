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
use backend\models\lucky\LuckyRecordForm;

/**
 * Lucky controller
 * 红包发放记录管理
 */
class RecordController extends Controller{
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
    
    //获取发放记录列表
    public function actionList()
    {
        $model = new LuckyRecordForm();
        $page  = Yii::$app->request->get('page', 1);
        $model->load(Yii::$app->request->get());
        $records  = $model->select($page, self::PAGE_SIZE);
        $provider = new ArrayDataProvider([
            'allModels' => $records['list'],
        ]);
        $pages = new Pagination([
            'totalCount'      => $records['count'],
            'defaultPageSize' => self::PAGE_SIZE
        ]);
        return $this->render('index', [
            'model'        => $model,
            'dataProvider' =>$provider,
            'pagination'   => $pages,
        ]);
    }
}