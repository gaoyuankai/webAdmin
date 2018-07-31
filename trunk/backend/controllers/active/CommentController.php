<?php
namespace backend\controllers\active;

use Yii;
use yii\base\InvalidParamException;
use yii\web\BadRequestHttpException;
use yii\web\Controller;
use yii\filters\VerbFilter;
use yii\filters\AccessControl;
use yii\web\Response;
use backend\models\active\CommentForm;
use yii\data\ArrayDataProvider;
use yii\data\Pagination;

/**
 * 活动评论类
 */
class CommentController extends Controller
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
        $model = new CommentForm();
        $page  = Yii::$app->request->get('page', 1);
        $model->setScenario('select');
        $model->load(Yii::$app->request->get());
        $comments = $model->select($page,self::PAGE_SIZE);
        if (!isset($comments) || !$comments) {
            $comments = ['list' => [], 'count' => 0];
        }
        $provider = new ArrayDataProvider([
            'allModels' => $comments['list'],
        ]);
        $pages = new Pagination([
            'totalCount'      => $comments['count'],
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
        $model = new CommentForm();
        if (Yii::$app->request->isAjax) {
            Yii::$app->response->format = Response::FORMAT_JSON;
            $comment = Yii::$app->request->post()['comment'];
            $model->setScenario('delete');
            $model->load(Yii::$app->request->post()['comment'], '');
            $check = $model->delete($comment);
            if($check === true) {
                return ['code' => 1, 'msg' => '成功'];
            } else {
                return ['code' => 0, 'msg' => $check];
            }
        }
        return ['code' => 0, 'msg' => '屏蔽失败'];
    }
    
    public function actionDetail()
    {
        $model = new CommentForm();
        if (Yii::$app->request->isAjax) {
            Yii::$app->response->format = Response::FORMAT_JSON;
            $model->setScenario('detail');
            if (isset(Yii::$app->request->post()['comment'])) {
                $model->load(Yii::$app->request->post()['comment'], '');
                $view = $this->renderPartial('detail', [
                    'model' => $model,
                ]);
                return ['code' => 1, 'data' => $view];
            }
        }
        return ['code' => 0, 'msg' => '内部错误'];
    }
    
    //评论编辑
    public function actionEdit()
    {
        $model = new CommentForm();
        if (Yii::$app->request->isAjax) {
            Yii::$app->response->format = Response::FORMAT_JSON;
            if (isset(Yii::$app->request->post()['editData'])) {
                $model->setScenario('edit');
                $model->load(Yii::$app->request->post()['editData'], '');
                $check = $model->edit(Yii::$app->request->post()['editData']['lastData']);
                if($check === true) {
                    return ['code' => 1, 'msg' => '置顶成功'];
                } else if($check === false){
                    return ['code' => 0];
                } else {
                    return ['code' => 0, 'msg' => $check];
                }
            }
        }
        return ['code' => 0];
    }
}