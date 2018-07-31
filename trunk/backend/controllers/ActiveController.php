<?php
namespace backend\controllers;

use Yii;
use yii\base\InvalidParamException;
use yii\web\BadRequestHttpException;
use yii\web\Controller;
use yii\filters\VerbFilter;
use yii\filters\AccessControl;
use yii\web\Response;

use common\models\GaBaseClient;
use backend\models\active\CommentListForm;
use backend\models\active\BannerForm;
use yii\helpers\Url;
use yii\data\Pagination;
use yii\data\ActiveDataProvider;
use yii\data\ArrayDataProvider;
/**
 * Active controller
 */
class ActiveController extends Controller
{
    const  PAGE_SIZE = 20;
    private $_active_data = array();
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
                        'allow' => false,
                        'roles' => ['?'],
                    ],
                    [
                        'actions' => ['logout','index','show_active_list','view','commentlist','commentdel','dailog','add'],
                        'allow' => true,
                        'roles' => ['@'],
                    ],
                    
                 ],
            ],
            'verbs' => [
                'class' => VerbFilter::className(),
                'actions' => [
                        'logout' => ['post'],
                    ],
            ],
        ];
    }
      //活动列表  
     public function actionShow_active_list(){
            $page = Yii::$app->request->get('page', 1);
            $ret  = GaBaseClient::getInstance()->getActivityList([
                    'where'  => [
                    //'activityKind' => 2
                    ],
                    'order'  => ['id' => 'desc'],
                    'offset' => ($page - 1) * self::PAGE_SIZE,
                    'limit'  => self::PAGE_SIZE
                    ]);
            if ($ret['status']) {
                $data =$ret['data']['list'];
                $provider = new ArrayDataProvider([
                                    'allModels' => $data,
                                ]);
                $pages = new Pagination([
                                    'totalCount' => $data =$ret['data']['count'],
                                    'defaultPageSize' => self::PAGE_SIZE
                                ]);
                return $this->render('list', [
                                    'dataProvider' => $provider,
                                    'pagination'   => $pages,
                                ]);
            } else {
                echo "<script>alert('".$ret['message']."');</script>";
                echo "<script>location.href = window.location.pathname+'?r=active/show_active_list'</script>";
                exit;
            }
        }
        
        //活动详细
        public function actionView($id)
        {
            if ($id) {
                $ret = GaBaseClient::getInstance()->getActivityDetailBg([
                        'where'  =>[
                               'id' => $id,
                            ],
                ]);
                if (!$ret['status']) {
                    echo "<script>alert('".$ret['message']."');</script>";
                    echo "<script>location.href = window.location.pathname+'?r=active/show_active_list'</script>";
                    exit;
                }
                if ($ret['status']) {
                    $schedule = new ArrayDataProvider([
                                'allModels' => $ret['data']['schedule'],
                                'pagination' => [
                                'pageSize' => count( $ret['data']['schedule']),
                                ],
                            ]);
                    $ret1 = GaBaseClient::getInstance()->getOrderList([]);
                    if (!$ret1['status']) {
                        echo "<script>alert('".$ret['message']."');</script>";
                        echo "<script>location.href = window.location.pathname+'?r=active/show_active_list'</script>";
                        exit;
                    }
                    $active_id=[];
                    foreach ($ret1['data']['list'] as $v) {
                        $active_id[]=$v['Activity_id'];
                    }
                    //订单中的活动id包含该活动id则返回1，视图页面不能编辑该活动的价格区域
                    $flag = in_array($id,$active_id)?1:2;
                    return $this->render('view', [
                            'model'    => $ret['data'],
                            'schedule' => $schedule,
                            'flag'     => $flag
                    ]);
                }
            }
            $this->redirect('index.php?r=site/error&msg=杨帆', '非法访问');
        }
}