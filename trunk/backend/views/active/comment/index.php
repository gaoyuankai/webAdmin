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


$this->title = '活动评论查询';
$this->params['breadcrumbs'][] = $this->title;
?>

<div class="row">
    <div class="col-lg-10" style = "height:100px;"> 
        <?php $form = ActiveForm::begin([
                        'id'         => $model->formName(),
                        'action'     => ['/active/comment/list'],
                        'type'       => ActiveForm::TYPE_VERTICAL,
                        'formConfig' => ['deviceSize' => ActiveForm::SIZE_SMALL],
                        'method'     => 'get',
                ]); ?>
        <div style="position:absolute;">
            <?= $form->field($model, 'User_id')->input('number',['style'=>'width:150px','maxlength' => 20]) ?>
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
            <?= $form->field($model, 'Activity_id')->input('number',['style'=>'width:150px','maxlength' => 20]) ?>
        </div>
        <div style="position:absolute; left:780px;">
            <?= $form->field($model, 'top')->dropDownList(Tool::ACTIVE_COMMENT_TOP, ['style'=>'width:150px','prompt' => '']); ?>
        </div>
        <div style="position:absolute; left:1020px; top: 25px">
            <?= Html::submitButton('查询', ['class' => 'btn btn-primary', 'name' => 'signup-button', 'style'=>'width:100px']) ?>
        </div>
        <?php ActiveForm::end(); ?>
    </div>
</div>
<div>
<?=GridView::widget([
        'dataProvider' => $dataProvider,
        'autoXlFormat'=>true,
        'toolbar' => [],
        'export'=>[
            'fontAwesome'=>true,
            'showConfirmAlert'=>false,
            'target'=>GridView::TARGET_BLANK,
            'filename' => 'wenjian1',
        ],
        'panel'=>[
            'type'=>'primary',
            'heading'=>$this->title,
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
                        //['label' => 'ID','value' =>'id'],
                        /* [
                            'class' => 'yii\grid\CheckboxColumn',
                            'checkboxOptions' => function ($model, $key, $index, $column) {
                                return ['value' => $key];
                            }
                        ], */
                        ['label' => '用户ID','value' =>'User_id','options'=>['width'=>"45px"]],
                        ['label' => '评论内容','value' =>'content','options'=>['width'=>"600px"],],
                        ['label' => '评论时间','value' => 'createTime','options'=>['width'=>"165px"]],
                        ['label' => '活动ID','value' =>'Activity_id','options'=>['width'=>"45px"]],
                        [
                            'label' => '是否置顶','value' =>function($data) {
                                                                $top_config = Tool::ACTIVE_COMMENT_TOP;
                                                                return $top_config[$data['top']];
                                                            },
                            'options'=>['width'=>"85px"]
                        ],
                        [
                            'label'=>'更多操作',
                            'format'=>'raw',
                            'value' => function($data, $key){
                                            if($data['display'] == 1) {
                                                $button_str = Html::Button('屏蔽', ['class' => 'btn btn-primary deleteone',
                                                                    'name' => 'signup-button', 'key' => $key]);
                                            } else {
                                                $button_str = Html::Button('取消屏蔽', ['class' => 'btn btn-primary deleteone',
                                                                    'name' => 'signup-button', 'key' => $key]);
                                            }
                                            $button_str .= Html::Button('编辑', ['class' => 'btn btn-primary edit',
                                                                'name' => 'signup-button', 'key' => $key, 'style' => 'margin-left: 10px']);
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
    var comment_data = <?=  $dataProvider->allModels ? json_encode($dataProvider->allModels) : '{}'?>;
</script>
<?=AppAsset::addCss($this,'@web/css/wdialog.css')?>
<?=AppAsset::addScript($this,'@web/js/lib/wdialog.js')?>
<?=AppAsset::addScript($this,'@web/js/lib/active/comment/index.js')?>