<?php
namespace backend\controllers\circle;

use Yii;
use yii\filters\AccessControl;
use yii\filters\VerbFilter;
use yii\web\Response;
use yii\web\Controller;
use yii\data\Pagination;

use common\models\GaBaseClient;
use backend\models\circle\CircleForm;
use yii\data\ArrayDataProvider;
use backend\components\Tool;
use common\models\UploadImage;

/**圈子管理
 * Circle controller
*/
class CircleController extends Controller
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
    
     //圈子列表
    public function actionList()
    {
        $model  = new CircleForm();
        $model->setScenario('select');
        //查询圈子名字
        $name = isset(Yii::$app->request->get()['CircleForm']['name'])?Yii::$app->request->get()['CircleForm']['name']:"";
        $page = Yii::$app->request->get('page', 1);
        //获取圈子列表-----若无查询结果则返回['data']['list']="";
        $ret = GaBaseClient::getInstance()->getCircleList([
            'where'  => ['name' => $name],
            'offset' => ($page - 1) * $this->size,
            'limit'  => $this->size
        ]);
        if ($ret['status']) {
            $provider = new ArrayDataProvider([
                'allModels' => $ret['data'] ? $ret['data']['list'] : [],
            ]);
            $pages = new Pagination([
                'totalCount'      => $ret['data']['count'],
                'defaultPageSize' => $this->size
            ]);
            return $this->render('list', [
                'model'        => $model,
                'data'         => $ret['data']['list'],
                'dataProvider' => $provider,
                'pagination'   => $pages,
            ]);
        } else {
            //获取接口数据出错则跳转到报错信息页
            echo "<script>alert('".$ret['message']."');</script>";
            echo "<script>location.href = window.location.pathname+'?r=circle/circle/list'</script>";
            exit;
        }
    }
    
    //添加圈子
    public function actionAdd()
    {
        $model = new CircleForm();
        if (Yii::$app->request->post()) {
            $model->setScenario('add');
            if ($model->load(Yii::$app->request->post())) {
                if ($model->add()) {
                    echo "<script>alert('添加成功');</script>";
                    echo "<script>location.href = window.location.pathname+'?r=circle/circle/list'</script>";
                    exit;
                } else {
                   $err = $model->getFirstErrors();
                   $model->linkPicture   = "";
                   $model->circlePicture = "";
                   echo "<script>alert('添加失败！".array_shift($err)."');</script>";
                }
            }
        }
        return $this->render('add', [
            'model'  => $model,
            'action' => 'add'
        ]);
    }
    
    //删除圈子---有成员则提示不能删除
    public function actionDel()
    {
        if (Yii::$app->request->isAjax) {
            Yii::$app->response->format = Response::FORMAT_JSON;
            $id = Yii::$app->request->post()['id'];
            //只获取圈子相关信息,只显示一条
            $ret = GaBaseClient::getInstance()->getCircleById([
                    'where' => ['id' => $id],
            ]);
            //获取接口数据报错
            if (!$ret['status']) {
                return ['code' => 0, 'msg' => $ret['message']];
            }
            //获取接口圈子数据为空
            if (!$ret['data']['item']) {
                return ['code' => 0, 'msg' => "没有该圈子信息"];
            }
            $oldlinkPicture   = $ret['data']['item']['linkPicture'];
            $oldcirclePicture = $ret['data']['item']['circlePicture'];
            $ret = GaBaseClient::getInstance()->deleteCircle(['circleId' => $id]);
            if(!$ret['status']){
                return ['code' => 0, 'msg' => $ret['message'] ];
            }
            //删除老图片
            $arr1= parse_url($oldcirclePicture);
            if (!$arr1) {
                return ['code' => 0, 'msg' => "圈子图片路径解析失败，请检查图片路径" ];
            }
            $arr2= parse_url($oldlinkPicture);
            if (!$arr2) {
                return ['code' => 0, 'msg' => "活动主页图片路径解析失败，请检查图片路径" ];
            }
            //获取接口需要的旧图片路径
            $imgPath1 = trim($arr1['path'],'/img/');
            $imgPath2 = trim($arr2['path'],'/img/');
            $deleteUrl = Yii::$app->params['imgServerDomin'] .'/api/v1/deleteFiles/1';
            $model  = new UploadImage();
            $res = $model->deleteImg($imgPath1 .','. $imgPath2 , $deleteUrl );
            if (!$res['status']) {
               return ['code' => 0, 'msg' => $res['message'] ];
            }
            return ['code' => 1, 'msg' => "删除成功" ];
        }
    }
    
    //更新圈子
    public function actionUpdate()
    {
        $model = new CircleForm();
        //列表页链接传来的id
        $id    = Yii::$app->request->get('id');
        //只获取圈子相关信息,只显示一条
        $ret = GaBaseClient::getInstance()->getCircleById([
                'where' => ['id' => $id],
        ]);
        //获取接口数据报错
        if (!$ret['status']) {
            echo "<script>alert('".$ret['message']."');</script>";
            echo "<script>location.href = window.location.pathname+'?r=circle/circle/list'</script>";
            exit;
        }
        //获取接口圈子数据为空
        if (!$ret['data']['item']) {
            echo "<script>alert('没有该圈子信息');</script>";
            echo "<script>location.href = window.location.pathname+'?r=circle/circle/list'</script>";
            exit;
        }
        $oldlinkPicture   = $ret['data']['item']['linkPicture'];
        $oldcirclePicture = $ret['data']['item']['circlePicture'];
        if (Yii::$app->request->post()) {
            $model->setScenario('update');
            if ($model->load(Yii::$app->request->post())) {
                $model->oldcirclePicture = $oldcirclePicture;
                $model->oldlinkPicture   = $oldlinkPicture;
                if ($model->update()) {
                    echo "<script>alert('修改成功');</script>";
                    echo "<script>location.href = window.location.pathname+'?r=circle/circle/list'</script>";
                    exit;
                } else {
                    $err = $model->getFirstErrors();
                    echo "<script>alert('修改失败！".array_shift($err)."');</script>";
                }
            }
        }
            //重写model中属性，可自动显示在视图中
            $model->id            = $ret['data']['item']['id'];
            $model->name          = $ret['data']['item']['name'];
            $model->brief         = $ret['data']['item']['brief'] ;
            $model->activityId    = $ret['data']['item']['Activity_id'];
            $model->status        = $ret['data']['item']['status'] ;
            $model->linkPicture   = $oldlinkPicture;
            $model->circlePicture = $oldcirclePicture;
            return $this->render('add', [
                'model'  => $model,
                'action' => 'update',
            ]);
    }
}
