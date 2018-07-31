<?php
namespace backend\controllers;

use Yii;
use yii\filters\AccessControl;
use yii\web\Controller;
use common\models\LoginForm;
use yii\filters\VerbFilter;
use backend\models\user\SignupForm;
use yii\web\Response;
use yii\web\Request;
use common\models\User;
use backend\components\AdminConfig;
use backend\models\AdminForm;
use yii\data\ArrayDataProvider;
use yii\data\Pagination;
/**
 * Site controller
 */
class SiteController extends Controller
{
    /**
     * @inheritdoc
     */
    public function behaviors()
    {
        return [
            'access' => [
                'class' => AccessControl::className(),
                'rules' => [
                    [
                        'actions' => ['login','error'],
                        'allow'   => true,
                        'roles'   => ['?'],
                    ],
                    [
                        'actions' => [
                            'logout', 'index', 'error', 
                            'adminlist', 'dialog', 
                            'updateadmin', 'changepassword',
                            'delcache'
                        ],
                        'allow'   => true,
                        'roles'   => ['@'],
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

    /**
     * @inheritdoc
     */
    public function actions()
    {
        return [
            'error' => [
                'class' => 'yii\web\ErrorAction',
            ],
        ];
    }
    
    public function actionDelcache()
    {
        $key = Yii::$app->request->get('key');
        Yii::$app->redis->set($key, time());
        echo "<script>alert('成功');</script>";
        echo "<script>location.href = window.location.pathname+'?r=site/index'</script>";
        exit;
    }

    public function actionIndex()
    {
        return $this->render('index');
    }
    
    /**
     * 注册用户.
     */
    public function actionSignup()
    {
    	$model = new SignupForm();
    	$user = $model->signup();
    	return;
    	if ($model->load(Yii::$app->request->post())) {
    		if ($user = $model->signup()) {
    			if (Yii::$app->getUser()->login($user)) {
    				return $this->goHome();
    			}
    		}
    	}
    	return $this->render('signup', [
			'model' => $model,
    	]);
    }

    /**
     * 用户登录
     * @return Ambigous <\yii\web\Response, \yii\web\$this, \yii\web\Response>|Ambigous <string, string>
     */
    public function actionLogin()
    {
        if (!\Yii::$app->user->isGuest) {
            return $this->goHome();
        }
        //$this->layout = false;
        $model = new LoginForm();
        if ($model->load(Yii::$app->request->post()) && $model->login()) {
            return $this->goBack();
        } else {
            return $this->render('login', [
                'model' => $model,
            ]);
        }
    }

    //用户登出
    public function actionLogout()
    {
        Yii::$app->user->logout();

        return $this->goHome();
    }
    
    //定制报错页面
    public function actionError()
    {
        return $this->render('error', [
            'message'  => Yii::$app->request->get('message'),
        ]);
    }
    
    public function actionAdminlist()
    {
        $model  = new AdminForm();
        $page   = Yii::$app->request->get('page', 1);
        $model->setScenario('select');
        $admins = ['list' => [], 'count'=>0];
        if (isset(Yii::$app->request->get()['AdminForm'])) {
            $model->load(Yii::$app->request->get()['AdminForm'], '');
        }
        $admins = $model->select($page,AdminConfig::Default_Page_Limit);
        $provider = new ArrayDataProvider([
            'allModels' => $admins['list'],
        ]);
        $pages    = new Pagination([
            'totalCount'      => $admins['count'],
            'defaultPageSize' => AdminConfig::Default_Page_Limit,
        ]);
        return $this->render('adminlist', [
            'model'        => $model,
            'dataProvider' => $provider,
            'pagination'   => $pages,
        ]);
    }
    public function actionDialog()
    {
        $model = new AdminForm();
        if (Yii::$app->request->isAjax) {
            Yii::$app->response->format = Response::FORMAT_JSON;
            $adminData = [];
            if (isset(Yii::$app->request->post()['adminData'])) {
                $adminData = json_decode(Yii::$app->request->post()['adminData'], true);
            }
            $action    = Yii::$app->request->post('action');
            $model->setScenario($action);
            $model->load($adminData, '');
            $view = $this->renderPartial('adminupdate', [
                'model'   => $model,
                'action'  => $action,
            ]);
            return ['code' => 1, 'data' => $view];
        }
    }
    
    public function actionUpdateadmin()
    {
        $model = new AdminForm();
        if (Yii::$app->request->isAjax) {
            Yii::$app->response->format = Response::FORMAT_JSON;
            $adminData = Yii::$app->request->post()['updateDatas'];
            $action    = Yii::$app->request->post('actions');
            $model->setScenario($action);
            $model->load($adminData, '');
            return $model->$action();
        }
    }
    
    public function actionChangepassword()
    {
        $model = new AdminForm();
        $model->username = Yii::$app->user->identity->username;
        $model->setScenario('changepw');
        $check = null;
        if($model->load(Yii::$app->request->post())) {
            $check = $model->changepw();
        }
        return $this->render('changepw', [
            'model' => $model,
            'check' => $check,
        ]);
    }
    
    public function actionTest(){
        $model = new AdminForm();
        return $this->render('test', [
            'model' => $model,
        ]);
    }
    
    public function actionTestalert()
    {
        $model = new AdminForm();
        return $this->renderPartial('testalert', [
            'action' => 'add',
            'model'  => $model,
        ]);
    }
}
