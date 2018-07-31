<?php
namespace backend\controllers\message;

use Yii;
use yii\base\InvalidParamException;
use yii\web\BadRequestHttpException;
use yii\web\Controller;
use yii\filters\VerbFilter;
use yii\filters\AccessControl;
use yii\web\Response;

use yii\data\Pagination;
use yii\data\ActiveDataProvider;
use yii\data\ArrayDataProvider;
use backend\models\message\MessageListForm;
/**
 * Active controller
 */
class MessageController extends Controller
{
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
        $model = new MessageListForm();
        $page  = Yii::$app->request->get('page', 1);
        $model->setScenario('select');
        $model->load(Yii::$app->request->get());
        $messages = $model->select($page,self::PAGE_SIZE);
        $provider = new ArrayDataProvider([
            'allModels' => $messages['list'],
        ]);
        $pages    = new Pagination([
            'totalCount'      => $messages['count'],
            'defaultPageSize' => self::PAGE_SIZE
        ]);
        return $this->render('index', [
            'model'        => $model,
            'dataProvider' => $provider,
            'pagination'   => $pages,
        ]);
    }
    
    public function actionDelete()
    {
        $model = new MessageListForm();
        if (Yii::$app->request->isAjax) {
            Yii::$app->response->format = Response::FORMAT_JSON;
            $ids = Yii::$app->request->post()['ids'];
            $check = $model->delete($ids);
            if($check === true) {
                return ['code' => 1, 'msg' => '删除成功'];
            } else {
                return ['code' => 0, 'msg' => $check];
            }
        }
        return ['code' => 0, 'msg' => '删除失败'];
    }
}