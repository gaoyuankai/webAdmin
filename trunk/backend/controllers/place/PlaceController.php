<?php
namespace backend\controllers\place;

use Yii;
use yii\base\InvalidParamException;
use yii\web\BadRequestHttpException;
use yii\web\Controller;
use yii\filters\VerbFilter;
use yii\filters\AccessControl;
use yii\web\Response;
use yii\data\Pagination;
use yii\data\ArrayDataProvider;
use backend\models\active\place\PlaceForm;
/**
 * place controller
 * 场馆管理
 */
class PlaceController extends Controller{
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
        $model = new PlaceForm();
        $page  = Yii::$app->request->get('page', 1);
        $model->setScenario('select');
        $model->load(Yii::$app->request->get());
        $place = $model->select($page, self::PAGE_SIZE);
        if(!isset($place)) {
            $place = ['list' => [], 'count' => 0];
        }
        $provider = new ArrayDataProvider([
            'allModels' => $place['list'],
        ]);
        $pages    = new Pagination([
            'totalCount'      => $place['count'],
            'defaultPageSize' => self::PAGE_SIZE
        ]);
        return $this->render('index', [
            'model'        => $model,
            'dataProvider' => $provider,
            'pagination'   => $pages,
        ]);
    }
    
    //弹出更新框
    public function actionDailog()
    {
        $model = new PlaceForm();
        if (Yii::$app->request->isAjax) {
            Yii::$app->response->format = Response::FORMAT_JSON;
            $placeData = Yii::$app->request->post('placeData');
            $action    = Yii::$app->request->post('action');
            $model->setScenario($action);
            if (($action == 'update' && $model->load($placeData, '')) || $action == 'add') {
                $view = $this->renderPartial('update', [
                    'model' => $model,
                   // 'data'  =>$model->data,
                ]);
                return ['code' => 1, 'data' => $view];
            } else {
                return ['code' => 0, 'msg'  => Yii::t('yii', 'error', ['name' => '12312312'])];
            }
        }
    }
    
    //更新场馆提交
    public function actionUpdatesubmit() 
    {
        $model = new PlaceForm();
        if (Yii::$app->request->isAjax) {
            Yii::$app->response->format = Response::FORMAT_JSON;
            $action = Yii::$app->request->post()['actions'];
            $model->setScenario($action);
            if($model->load(Yii::$app->request->post()['updateDatas'], '')){
                return $model->$action();
            }
        }
    }
    
    public function actionDelete()
    {
        $model = new PlaceForm();
        if (Yii::$app->request->isAjax) {
            Yii::$app->response->format = Response::FORMAT_JSON;
            $model->setScenario('delete');
            if($model->load(Yii::$app->request->post(), '')){
                return $model->delete();
            }
        }
    }
}