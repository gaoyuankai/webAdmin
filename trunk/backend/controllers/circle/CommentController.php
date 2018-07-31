<?php
namespace backend\controllers\circle;

use Yii;
use yii\filters\AccessControl;
use yii\filters\VerbFilter;
use yii\web\Response;
use yii\web\Controller;
use yii\data\Pagination;

use common\models\GaBaseClient;
use backend\models\circle\CommentForm;
use yii\data\ArrayDataProvider;
use backend\components\Tool;


/**主题评论管理
 * Comment controller
*/
class CommentController extends Controller
{
    public $size = 10;
    
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
    
    //主题评论列表
    public function actionList()
    {
        $model  = new CommentForm();
        $model->setScenario('select');
        $page = Yii::$app->request->get('page', 1);
        //列表页链接传来的主题id
        $id         = Yii::$app->request->get('id',"");
        $user_id    = "";
        $createTime = [];
        //查询传来的主题user_id,主题id,创建评论时间段
        if (Yii::$app->request->post()) {
            $user_id    = Yii::$app->request->post()["CommentForm"]['User_id'];
            $id         = Yii::$app->request->post()["CommentForm"]['CircleTheme_id'];
            $createTime = Yii::$app->request->post()["CommentForm"]['createTime'];
            $model->User_id = $user_id;
            $model->Circle_id = $id;
            //有时间提交
            if ($createTime) {
               //分割成数组
               $time  = explode("到",$createTime);
               $start = strtotime($time[0]);
               $end   = strtotime($time[1]);
               if($start>$end){
                   echo "<script>alert('选择的评论起始时间应该小于结束时间');</script>";
                   echo "<script>location.href = window.location.pathname+'?r=circle/comment/list'</script>";
                   exit;
               }
               //有空格去除
               $createTime = [ 
                   '>=' => trim($time[0]),
                   '<=' => trim($time[1])
               ];
            }
        }
        //获取评论列表
            $ret = GaBaseClient::getInstance()->getThemeCommentListBg([
                'where'  => [
                     'CircleTheme_id' => $id ,
                     'User_id'        => $user_id,
                     'createTime'     => $createTime
                 ],
                'offset' => ($page - 1) * $this->size,
                'limit'  => $this->size,
             ]);
            //获取接口数据报错
            if (!$ret['status']) {
                echo "<script>alert('".$ret['message']."');</script>";
                echo "<script>location.href = window.location.pathname+'?r=circle/comment/list'</script>";
                exit;
            }
            //有数据且是链接过来的才显示主题id
            if($ret['data']['list'] && $id!==""){
               $model->CircleTheme_id = $ret['data']['list'][0]['CircleTheme_id'];
            }
            
            $provider = new ArrayDataProvider([
                'allModels' => $ret['data']['list'] ,
            ]);
            $pages = new Pagination([
                'totalCount'      => $ret['data']['count'],
                'defaultPageSize' => $this->size
            ]);
            return $this->render('list', [
                'model'        => $model,
                'dataProvider' => $provider,
                'pagination'   => $pages,
            ]);
        
    }
    
    //删除主题评论
    public function actionDel()
    {
        if (Yii::$app->request->isAjax) {
            Yii::$app->response->format = Response::FORMAT_JSON;
            //此时需要登录用户id,根据id删除---管理员可以删除所有
            $id       = Yii::$app->request->post()['id'];
            $user_id  = Yii::$app->request->post()['user_id'];
            $ret = GaBaseClient::getInstance()->deleteThemeComment(['themeCommentid' => $id,'userId' => $user_id]);
            if(!$ret['status']){
               return ['code' => 0, 'msg' => "删除失败" ];
            }
            return ['code' => 1, 'msg' => "删除成功" ];
        }
    }
    
}
