<?php
namespace backend\controllers\circle;

use Yii;
use yii\filters\AccessControl;
use yii\filters\VerbFilter;
use yii\web\Response;
use yii\web\Controller;
use yii\data\Pagination;

use common\models\GaBaseClient;
use backend\models\circle\ThemeForm;
use yii\data\ArrayDataProvider;
use backend\components\Tool;
use common\models\UploadImage;

/**主题管理
 * Theme controller
*/
class ThemeController extends Controller
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
                'actions' => ['logout' => ['post'],],
            ],
        ];
    }

    //主题列表
    public function actionList()
    {
        $model  = new ThemeForm();
        $model->setScenario('select');
        //列表页链接传来的圈子id
        if (Yii::$app->request->get()) {
            $id          = Yii::$app->request->get('id');
            $page        = Yii::$app->request->get('page', 1);
            $circle_name = Yii::$app->request->get('circle_name');
            $userid = isset(Yii::$app->request->get()["ThemeForm"]["User_id"])?Yii::$app->request->get()["ThemeForm"]["User_id"]:"";
            //只获取圈子相关信息,只显示一条
            $ret = GaBaseClient::getInstance()->getCircleThemeList([
                'where'         => ['Circle_id' => $id , 'User_id' => $userid],
                'getCircleInfo' => 0,
                'getComments'   => 1,
                'offset'        => ($page - 1) * $this->size,
                'limit'         => $this->size,
             ]);
            //获取接口数据报错
            if (!$ret['status']) {
                echo "<script>alert('".$ret['status']."');</script>";
                echo "<script>location.href = window.location.pathname+'?r=circle/circle/list'</script>";
                exit;
            }
            if($ret['data']['list']){
                foreach ($ret['data']['list'] as $k=>$v){
                    $ret['data']['list'][$k]['content'] = mb_substr($v['content'],0,20);
                }
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
                'circle_id'    => $id,
                'circle_name'  => $circle_name
            ]);
        }
    }

    //图片上传action
    public function actionUploadimg()
    {

    }

    //添加主题
    public function actionAdd()
    {
        //只能添加自己的主题
        $model = new ThemeForm();
        $model->setScenario('add');
        if (Yii::$app->request->post()) {
            $model->setScenario('add');
            if ($model->load(Yii::$app->request->post())) {
                $model->Circle_id = Yii::$app->request->post('Circle_id');
                if ($model->add()) {
                    echo "<script>alert('添加成功');</script>";
                    echo "<script>location.href = window.location.pathname+'?r=circle/circle/list'</script>";
                } else {
                    $err = $model->getFirstErrors();
                    $model->themePictures = [];
                    echo "<script>alert('添加失败！".array_shift($err)."');</script>";
                }
            }
        }
        return $this->render('add', [
            'model'       => $model,
            'circle_id'   => Yii::$app->request->get('circle_id'),
            'circle_name' => Yii::$app->request->get('circle_name'),
        ]);
    }

    //删除主题
    public function actionDel()
    {
        if (Yii::$app->request->isAjax) {
            Yii::$app->response->format = Response::FORMAT_JSON;
            //此时需要主题id,发布主题者的id进行删除
            $id      = Yii::$app->request->post()['id'];
            $user_id = Yii::$app->request->post()['user_id'];
            $ret = GaBaseClient::getInstance()->getCircleThemeById([
                    'where'        => ['id' => $id],
                    'themePicture' => 1
             ]);
            $oldPictures = $ret['data']['list'][0]['themePictures'];
            
            //获取接口数据报错
            if (!$ret['status']) {
                return ['code' => 0, 'msg' => $ret['message'] ];
            }
            //获取主题信息数据为空
            if (!$ret['data']['list']) {
                return ['code' => 0, 'msg' => "没有该主题信息" ];
            }
            //删除主题数据
            $ret = GaBaseClient::getInstance()->deleteCircleTheme(['circleThemeId' => $id , 'userId' => $user_id]);
            if(!$ret['status']){
                return ['code' => 0, 'msg' => $ret['message'] ];
            }
            
            //有图片则删除图片
            if ($oldPictures) {
                $imgStr = "";
                foreach ( $oldPictures as $oldPicture) {
                   $arr = parse_url($oldPicture);
                    if (!$arr) {
                        return ['code'=> 0,'msg' => '圈子图片路径解析失败，请检查图片路径'];
                    }
                    $imgPath = ltrim($arr['path'],'/img/');
                    $imgStr .= $imgPath.",";
                }
               
                $imgStr = rtrim($imgStr,',');
                if ($imgStr) {
                    $model     = new UploadImage();
                    $deleteUrl = Yii::$app->params['imgServerDomin'] .'/api/v1/deleteFiles/1';
                    $res       = $model->deleteImg( $imgStr , $deleteUrl );
                    //图片删除失败
                    if (!$res['status']) {
                        return ['code' => 0, 'msg' => $res['message'] ];
                    }
                }
            }
        return ['code' => 1, 'msg' => "删除成功" ];
        }
      }

    //更新主题
    public function actionUpdate()
    {
        $model = new ThemeForm();
        Yii::$app->response->format = Response::FORMAT_JSON;
        //根据主题id和推荐状态修改主题信息
        if (Yii::$app->request->post()) {
            $id  = (int)Yii::$app->request->post('id');
            $top = (int)Yii::$app->request->post('top');
            $ret = GaBaseClient::getInstance()->modifyCircleTheme(['id' => $id,'top' => $top]);
            if($ret['status']){
                return ['code' => 1, 'msg' => "修改成功"];
            }
            return ['code' => 0, 'msg' => "修改失败"];
        }
        //页面显示
        if (Yii::$app->request->get()) {
            $id  = Yii::$app->request->get('id');
            $ret = GaBaseClient::getInstance()->getCircleThemeById([
                'where'        => ['id' => $id],
                'themePicture' => 1
            ]);
            //获取接口数据报错
            if (!$ret['status']) {
                echo "<script>alert('".$ret['message']."');</script>";
                echo "<script>location.href = window.location.pathname+'?r=circle/circle/list'</script>";
                exit;
            }
            //获取主题信息数据为空
            if (!$ret['data']['list']) {
                echo "<script>alert('没有该主题信息');</script>";
                echo "<script>location.href = window.location.pathname+'?r=circle/circle/list'</script>";
                exit;
            }
           
            $model->id            = $ret['data']['list'][0]['id'];
            $model->Circle_id     = $ret['data']['list'][0]['Circle_id'];
            $model->content       = $ret['data']['list'][0]['content'];
            $model->top           = $ret['data']['list'][0]['top'];
            $model->sysAdmin      = $ret['data']['list'][0]['sysAdmin'];
            $model->themePictures = empty($ret['data']['list'][0]['themePictures'])?"":$ret['data']['list'][0]['themePictures'];
            $view = $this->renderPartial('update', [
                'model' => $model,
            ]);
            return ['code' => 1, 'data' => $view];
        } else {
            return ['code' => 0, 'msg' => "获取主题信息失败"];
        }
    }
    
}
