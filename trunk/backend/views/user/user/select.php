<?php
use yii\helpers\Html;
use yii\bootstrap\ActiveForm;
use kartik\grid\GridView;
use backend\assets\AppAsset;
use yii\widgets\LinkPager;
use backend\components\Tool;

$this->title = '用户管理';
$this->params['breadcrumbs'][] = $this->title;
?>
<div class="site-signup">

    <div class="row">
        <div class="col-lg-15">
            <?php $form = ActiveForm::begin([
                'method'      =>'get',
                'options'     => ['data-pjax' => true],
                'fieldConfig' => [
                'template'    => "<div class='col-xs-3 col-sm-1' style = 'width:100px;left:10px;margin:0px;padding:0px;'>{label}</div>
                                            <div class='col-xs-7 col-sm-2 ' style = 'width:150px;left:0px;margin:0px;padding:0px;'>{input}</div>
                                                    ",]
                ]); ?>
                <?= $form->field($model, 'email')->input('text',['style'=>'margin:0px;padding:0px;']) ?>
                <?= $form->field($model, 'phone')->input('number',['style'=>'margin:0px;padding:0px;']) ?>
                <?= $form->field($model, 'nick') ->input('text',['style'=>'margin:0px;padding:0px;']) ?>
                <?= $form->field($model, 'phoneStauts')->dropDownList(Tool::PHONE_STATUS, ['prompt' => '','style'=>'width:80px;left:20px;margin:0px;padding:0px;'])?>
                <div class="form-group">
                    <?= Html::submitButton('查询', ['class' => 'btn btn-primary', 'name' => 'signup-button']) ?>
                </div>

            <?php ActiveForm::end(); ?>
        </div>
    </div>
    <div style= "margin-top:50px">
        <?php
            if (!empty($dataProvider->allModels)) {
                //echo Html::submitButton('发短消息', ['class' => 'btn btn-primary smessage', 'name' => 'signup-button']);
                //echo ' | ';
                //echo Html::submitButton('发红包', ['class' => 'btn btn-primary shongbao', 'name' => 'signup-button']);
                echo GridView::widget([
                    'id' => 'select',
                    'dataProvider' => $dataProvider,
                    'autoXlFormat'=>true,
                    'toolbar'=> [
                        [
                            'content'=> Html::Button('发短消息', ['class' => 'btn btn-primary smessage',
                                            'name' => 'signup-button', 'style'=>'width:100px;float: left; margin-right: 20px'])
                                        .Html::Button('发红包', ['class' => 'btn btn-primary shongbao',
                                            'name' => 'signup-button', 'style'=>'width:100px;float: left']),
                        ] ,
                    ],
                    'panel'=>[
                        'type'=>'primary',
                        'heading'=>$this->title
                    ],
                    'panelHeadingTemplate' => '<div class="pull-right">总共' .$pagination->totalCount.'条数据</div>
                                                <h3 class="panel-title">
                                                    {heading}
                                                </h3>
                                                <div class="clearfix"></div>',
                    'panelBeforeTemplate' => '<div class="pull-left">
                                                    <div class="btn-toolbar kv-grid-toolbar" role="toolbar">
                                                        {toolbar}
                                                    </div>    
                                              </div>
                                              <div class="clearfix"></div>',
                    'columns' => [
                            [
                                'class' => 'yii\grid\CheckboxColumn',
                                'checkboxOptions' => function ($model, $key, $index, $column) {
                                    return ['value' => $key];
                                }
                            ],
                            ['label' => '用户ID','value' =>'id'],
                            ['label' => '昵称','value' =>'nickName',],
                            ['label' => 'email','value' => 'email',],
                            ['label' => '手机',    'value' => 'phone',],
                            ['label' => '性别','value' => 'sex',],
                            ['label' => '区县','value' => 'region',],
                            ['label' => '注册时间','value' => 'regTime',],
                    ]
                ]);
            } else {
                    echo '<h5>无用户数据</h5>';
            }
        ?>
    </div>
</div>
<?php 
     echo LinkPager::widget([
         'pagination' => $pagination,
         'firstPageLabel'=>"首页",
         'prevPageLabel'=>'上一页',
         'nextPageLabel'=>'下一页',
         'lastPageLabel'=>'末页',
     ]);
?>
<script>
    var data = <?=  $dataProvider->allModels ? json_encode($dataProvider->allModels) : '{}'?>;
</script>


<?=AppAsset::addCss($this,'@web/css/wdialog.css')?>
<?=AppAsset::addScript($this,'@web/js/lib/wdialog.js')?>
<?=AppAsset::addScript($this,'@web/js/lib/user/select.js')?>