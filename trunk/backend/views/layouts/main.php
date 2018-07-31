<?php

/* @var $this \yii\web\View */
/* @var $content string */

use backend\assets\AppAsset;
use yii\helpers\Html;
use yii\bootstrap\Nav;
use yii\bootstrap\NavBar;
use yii\widgets\Breadcrumbs;
use common\widgets\Alert;
use yii\helpers\Url;
use backend\components\AdminConfig;
//use Yii;
//use kartik\nav\NavX;
//use kartik\dropdown\DropdownX;

$mycompany = '上海金苹果教育投资有限公司';
AppAsset::register($this);
?>
<?php $this->beginPage() ?>
<!DOCTYPE HTML>
<html lang="<?= Yii::$app->language ?>">
<head>
    <meta charset="<?= Yii::$app->charset ?>">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <?= Html::csrfMetaTags() ?>
    <title><?= Html::encode($this->title) ?></title>
    <?php $this->head() ?>
</head>
<body>
<?php $this->beginBody() ?>

<div class="wrap clearfix">
    <?php
    NavBar::begin([
        'brandLabel' => $mycompany,
        'brandUrl' => Yii::$app->homeUrl,
        'options' => [
            'class' => 'navbar-inverse navbar-fixed-top',
        ],
    ]);
    $menuItems = [
        ['label' => '主页', 'url' => ['/site/index']],
    ];
    if (Yii::$app->user->isGuest) {
        $menuItems[] = ['label' => 'Login', 'url' => ['/site/login']];
    } else {
        $menuItems[] = [
            'label' => '注销 (' . Yii::$app->user->identity->username . ')',
            'url' => ['/site/logout'],
            'linkOptions' => ['data-method' => 'post']
        ];
        $menuItems[] = [
        'label' => '修改密码',
        'url' => ['/site/changepassword'],
        'linkOptions' => ['data-method' => 'post']
        ];
    }
    echo Nav::widget([
        'options' => ['class' => 'navbar-nav navbar-right'],
        'items' => $menuItems,
    ]);
    NavBar::end();
    ?>

<div id="leftnav" class="col-md-2" style=" margin-top:90px; float:left;">
<?php if (!Yii::$app->user->getIsGuest()) {?>
    <div id = 'left_nav' class="list-group" style="width:210px;" >
        <!-- 活动管理 -->
        <a class="list-group-item" data-toggle="collapse" href="#activecollapse" aria-expanded="false" aria-controls="collapseExample">活动管理<span class="caret"></span></a>
        <div class="collapse" id="activecollapse">
            <div class="well">
                <div class="list-group">
                    <a href="<?= Url::toRoute(['active/show_active_list'])?>" class="list-group-item" >活动列表</a>
                    <a href="<?= Url::toRoute(['active/comment/list'])?>" class="list-group-item">活动评论列表</a>
                    <a href="<?= Url::toRoute(['active/edit/add'])?>" class="list-group-item">添加活动</a>
                </div>
            </div>
        </div>
        <!-- 用户管理 -->
        <a class="list-group-item" data-toggle="collapse" href="#usercollapse" aria-expanded="false" aria-controls="collapseExample">用户管理<span class="caret"></span></a>
        <div class="collapse" id="usercollapse">
            <div class="well">
                <div class="list-group">
                    <a href="<?= Url::toRoute(['user/user/select'])?>" class="list-group-item">用户列表</a>
                </div>
            </div>
        </div>
        <!-- 订单管理 -->
         <a class="list-group-item" data-toggle="collapse" href="#tradecollapse" aria-expanded="false" aria-controls="collapseExample">订单管理<span class="caret"></span></a>
        <div class="collapse" id="tradecollapse">
            <div class="well">
                <div class="list-group">
                    <a href="<?= Url::toRoute(['trade/trade/list'])?>" class="list-group-item">订单列表</a>
                    <a href="<?= Url::toRoute(['user/user/additioninfo'])?>" class="list-group-item">保险信息</a>
                </div>
            </div>
        </div>
        <!-- 红包管理 -->
         <a class="list-group-item" data-toggle="collapse" href="#luckycollapse" aria-expanded="false" aria-controls="collapseExample">红包管理<span class="caret"></span></a>
        <div class="collapse" id="luckycollapse">
            <div class="well">
                <div class="list-group">
                    <a href="<?= Url::toRoute(['lucky/lucky/list'])?>" class="list-group-item">红包列表</a>
                    <a href="<?= Url::toRoute(['lucky/record/list'])?>" class="list-group-item">红包记录查询</a>
                </div>
            </div>
        </div>
        <!-- 场馆管理 -->
        <a class="list-group-item" data-toggle="collapse" href="#placecollapse" aria-expanded="false" aria-controls="collapseExample">场馆管理<span class="caret"></span></a>
        <div class="collapse" id="placecollapse">
            <div class="well">
                <div class="list-group">
                    <a href="<?= Url::toRoute(['place/place/list'])?>" class="list-group-item">场馆列表</a>
                </div>
            </div>
        </div>
        <!-- 网站配置管理 -->
        <a class="list-group-item" data-toggle="collapse" href="#bannercollapse" aria-expanded="false" aria-controls="collapseExample">网站配置<span class="caret"></span></a>
        <div class="collapse" id="bannercollapse">
            <div class="well">
                <div class="list-group">
                    <a href="<?= Url::toRoute(['banner/list'])?>" class="list-group-item">配置列表</a>
                    <a href="<?= Url::toRoute(['banner/add'])?>" class="list-group-item">添加配置</a>
                </div>
            </div>
        </div>
        <!-- 圈子管理 -->
        <a class="list-group-item" data-toggle="collapse" href="#circlecollapse" aria-expanded="false" aria-controls="collapseExample">圈子管理<span class="caret"></span></a>
        <div class="collapse" id="circlecollapse">
            <div class="well">
                <div class="list-group">
                    <a href="<?= Url::toRoute(['circle/circle/list'])?>" class="list-group-item">圈子列表</a>
                    <a href="<?= Url::toRoute(['circle/comment/list'])?>" class="list-group-item">主题评论列表</a>
                </div>
            </div>
        </div>
        <!-- 消息管理 -->
        <a class="list-group-item" data-toggle="collapse" href="#messagecollapse" aria-expanded="false" aria-controls="collapseExample">消息管理<span class="caret"></span></a>
        <div class="collapse" id="messagecollapse">
            <div class="well">
                <div class="list-group">
                    <a href="<?= Url::toRoute(['message/message/list'])?>" class="list-group-item">消息管理</a>
                </div>
            </div>
        </div>
        <!-- 删除缓存 -->
        <a class="list-group-item" data-toggle="collapse" href="#delcache" aria-expanded="false" aria-controls="collapseExample">删除缓存<span class="caret"></span></a>
        <div class="collapse" id="delcache">
            <div class="well">
                <div class="list-group">
                    <a href="<?= Url::toRoute(['site/delcache', 'key' => 'cache_index'])?>" class="list-group-item">主页</a>
                </div>
            </div>
        </div>
        <?php if (AdminConfig::checkIsAdmin()) {?>
        <!-- 管理员管理 -->
            <a class="list-group-item" data-toggle="collapse" href="#admincollapse" aria-expanded="false" aria-controls="collapseExample">管理员管理<span class="caret"></span></a>
            <div class="collapse" id="admincollapse">
                <div class="well">
                    <div class="list-group">
                        <a href="<?= Url::to(['site/adminlist'])?>" class="list-group-item">管理员管理</a>
                    </div>
                </div>
            </div>
        <?php }?>
    </div>
<?php }?>
	
</div>
 	<div class="container" style="float: left;">
        <?= Breadcrumbs::widget([
            'links' => isset($this->params['breadcrumbs']) ? $this->params['breadcrumbs'] : [],
        ]) ?>
        <?= Alert::widget() ?>
        <?= $content ?>
    </div>
</div>


<footer class="footer">
    <div class="container">
        <p class="pull-left">&copy;  <?= $mycompany.' '.date('Y') ?></p>

        <p class="pull-right"><?= Yii::powered() ?></p>
    </div>
</footer>
<?=AppAsset::addScript($this,'@web/js/lib/left/left_nav.js')?>
<?php $this->endBody() ?>


<script>
        var route = '<?=urlencode(Yii::$app->requestedRoute)?>';
</script>
</body>
</html>
<?php $this->endPage() ?>

