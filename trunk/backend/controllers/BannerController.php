<?php
namespace backend\controllers;

use Yii;
use yii\web\Controller;
use yii\filters\VerbFilter;
use yii\filters\AccessControl;
use yii\web\Response;

use common\models\GaBaseClient;
use backend\models\banner\BannerForm;
use yii\data\ArrayDataProvider;
use backend\components\Tool;
use common\models\UploadImage;

/**网站配置管理
 * Banner controller
 */
class BannerController extends Controller
{
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
                'class' => VerbFilter::className(),
                'actions' => ['logout' => ['post'],],
            ],
        ];
    }

    public function actionList()
    {
        $model = new BannerForm();
        if(Yii::$app->request->get()){
            //下拉框如果查询则改变type
            $type = isset(Yii::$app->request->get()['BannerForm']['type']) ? Yii::$app->request->get()['BannerForm']['type'] : 1;
        }
        $ret = GaBaseClient::getInstance()->getHomePageConfig([
            'where'  => ['type' => ['in' => [$type]]],
        ]);
        
        if ($ret['status']) {
            $provider = new ArrayDataProvider([
                'allModels' => $ret['data'] ? $ret['data'][$type] : [],
            ]);
            $model->type = $type;
            return $this->render('list', [
                'model'        => $model,
                'dataProvider' => $provider,
                'type'         => $type,
            ]);
        } else {
            //获取接口数据出错
            echo "<script>alert('".$ret['message']."');</script>";
            echo "<script>location.href = window.location.pathname+'?r=banner/list'</script>";
            exit;
        }
    }
    
    //添加网站配置
    public function actionAdd()
    {
        $model = new BannerForm();
        if (Yii::$app->request->post()) {
            $type = Yii::$app->request->post()['BannerForm']['type'];
            $model->setScenario('add'.$type);
            if ($model->load(Yii::$app->request->post())) {
                if ($model->add()) {
                    echo "<script>alert('添加成功');</script>";
                    echo "<script>location.href = window.location.pathname+'?r=banner/list&type=$model->type'</script>";
                    exit;
                } else {
                        $err = $model->getFirstErrors();
                        $model->picture = "";
                        echo "<script>alert('添加失败！".array_shift($err)."');</script>";
                }
            }
        }
        return $this->render('add', [
           'model'  => $model,
           'action' => 'add',
        ]);
    }
    
    //删除网站配置
    public function actionDel()
    {
        if (Yii::$app->request->isAjax) {
            Yii::$app->response->format = Response::FORMAT_JSON;
            $id     = Yii::$app->request->post()['id'];
            $ret    = GaBaseClient::getInstance()->getHomePageConfigById(['where' => ['id' => $id]]);
            $oldPic = $ret['data']['picture'];
            $ret    = GaBaseClient::getInstance()->deleteHomePageConfig(['id' => $id]);
            if(!$ret['status']){
                return ['code' => 0, 'msg' => "删除失败" ];
            }
            $arr= parse_url($oldPic);
            if (!$arr) {
                return ['code' => 0, 'msg' => "图片路径解析失败，请检查图片路径" ];
            }
            //获取接口需要的旧图片路径
            $imgPath   = ltrim($arr['path'],'/img/');
            $deleteUrl = Yii::$app->params['imgServerDomin'] .'/api/v1/deleteFiles/1';
            $model     = new UploadImage();
            $res       = $model->deleteImg($imgPath , $deleteUrl );
            if (!$res['status']) {
               return ['code' => 0, 'msg' => $res['message'] ];
            }
            return ['code' => 1, 'msg' => "删除成功" ];
        }
    }
    
    public function actionUpdate()
    {
        $model = new BannerForm();
        //列表页链接传来的id
        $id  = Yii::$app->request->get('id');
        $ret = GaBaseClient::getInstance()->getHomePageConfigById(['where' => ['id' => $id]]);
        $model->type    = $ret['data']['type'];
        if (!$ret['status']) {
            echo "<script>alert('".$ret['message']."');</script>";
            echo "<script>location.href = window.location.pathname+'?r=banner/list'</script>";
            exit;
        }
        $oldPic = $ret['data']['picture'];
        if (Yii::$app->request->post()) {
           $post = Yii::$app->request->post();
           //类型不能修改
           if (isset($post['BannerForm']['type'])) {
               if ($post['BannerForm']['type'] != $model->type) {
                   echo "<script>alert('不要篡改表单哦');</script>";
                   echo "<script>location.href = window.location.pathname+'?r=banner/list'</script>";
                   exit;
               }
           }
            $model->setScenario('update');
            if ($model->load(Yii::$app->request->post())) {
                $model->oldPic = $oldPic;
                if ($model->update()) {
                   echo "<script>alert('修改成功');</script>";
                   echo "<script>location.href = window.location.pathname+'?r=banner/list'</script>";
                   exit;
                } else {
                  $err = $model->getFirstErrors();
                  echo "<script>alert('修改失败！".array_shift($err)."');</script>";
                }
            }
        }
        $model->id      = $id;
        $model->picture = $oldPic;
        $model->status  = $ret['data']['status'];
        $model->kind    = empty($ret['data']['items']['kind'])?1:$ret['data']['items']['kind'];
        return $this->render('update', [
            'model'  => $model,
            'data'   => $ret['data'],
        ]);
        
    }
   
}