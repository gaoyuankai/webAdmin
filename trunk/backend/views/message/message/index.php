<?php
use yii\helpers\Html;
//use yii\grid\GridView;
use backend\assets\AppAsset;
use yii\helpers\Url;
use kartik\form\ActiveForm;
use kartik\form\ActiveField;
use kartik\daterange\DateRangePicker;
use yii\widgets\LinkPager;
use backend\components\Tool;
use kartik\grid\GridView;
use kartik\select2\Select2;


$this->title = '消息管理';
$this->params['breadcrumbs'][] = $this->title;
?>

<div class="row">
    <div class="col-lg-10" style = "height:100px;"> 
        <?php $form = ActiveForm::begin([
                        'id'         => $model->formName(),
                        'action'     => ['/message/message/list'],
                        'type'       => ActiveForm::TYPE_VERTICAL,
                        'formConfig' => ['deviceSize' => ActiveForm::SIZE_SMALL],
                        'method'     => 'get',
                ]); ?>
        <div style="position:absolute;">
            <?= $form->field($model, 'to_User_id')->input('number',['style'=>'width:150px','maxlength' => 20]) ?>
        </div>
        <div style="position:absolute; left:190px;">
            <?= $form->field($model, 'createTime', [
                    'addon'=>['prepend'=>['content'=>'<i class="glyphicon glyphicon-calendar"></i>']],
                    'options'=>['class'=>'drp-container form-group','style'=>'width:400px']
                ])->widget(DateRangePicker::classname(), [
                    'useWithAddon'=>true,
                    'convertFormat'=>true,
                    'pluginOptions'=>[
                                        'timePicker'=>true,
                                        'timePickerIncrement'=>10,
                                        'locale'=>[
                                                    'format'=>'Y-m-d H:m:s',
                                                    'separator'=>' 到 ',
                                                  ],
                                     ]
                ]);?>
        </div>
        <div style="position:absolute; left:610px;">
            <?= $form->field($model, 'title')->input('text',['style'=>'width:250px','maxlength' => 20]) ?>
        </div>
        <div style="position:absolute; left:880px; top: 25px">
            <?= Html::submitButton('查询', ['class' => 'btn btn-primary', 'name' => 'signup-button', 'style'=>'width:100px']) ?>
        </div>
        <div style="position:absolute; left:1020px; top: 25px">
            <?= Html::Button('发消息', ['class' => 'btn btn-primary smessage', 'name' => 'signup-button', 'style'=>'width:100px']) ?>
        </div>
        <?php ActiveForm::end(); ?>
    </div>
</div>
<div>
<?=GridView::widget([
        'dataProvider' => $dataProvider,
        'autoXlFormat'=>true,
        'toolbar'=> [
            [
                'content'=> Html::Button('批量删除', ['class' => 'btn btn-primary deletes', 
                                'name' => 'signup-button', 'style'=>'width:100px;float: left']),
            ],
        ],
        'export'=>[
            'fontAwesome'=>true,
            'showConfirmAlert'=>false,
            'target'=>GridView::TARGET_BLANK,
            'filename' => 'wenjian1',
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
                        ['label' => '接受者ID','value' =>'to_User_id','options'=>['width'=>"55px"]],
                        ['label' => '标题','value' =>'title','options'=>['width'=>"180px"],],
                        ['label' => '内容','value' => 'content','options'=>['width'=>"655px"]],
                        ['label' => '发送时间','value' =>'createTime','options'=>['width'=>"165px"]],
                        [
                            'label'=>'更多操作',
                            'format'=>'raw',
                            'value' => function($data, $key){
                                            $button_str = Html::Button('删除', ['class' => 'btn btn-primary deleteone','name' => 'signup-button', 'messageId' => $data['id']]);
                                            return $button_str;
                             }
                        ]
                    ],
                ]); ?>
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
    var message_data = <?=  $dataProvider->allModels ? json_encode($dataProvider->allModels) : '{}'?>;
</script>
<?=AppAsset::addCss($this,'@web/css/wdialog.css')?>
<?=AppAsset::addScript($this,'@web/js/lib/wdialog.js')?>
<?=AppAsset::addScript($this,'@web/js/lib/message/index.js')?>