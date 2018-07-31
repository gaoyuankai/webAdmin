<?php
use yii\helpers\Html;
use yii\helpers\Url;

use backend\assets\AppAsset;

use kartik\form\ActiveForm;
use kartik\form\ActiveField;
use kartik\daterange\DateRangePicker;
use yii\widgets\LinkPager;
use backend\components\Tool;
use kartik\grid\GridView;
use kartik\export\ExportMenu;



$this->title = '保险信息列表';
$this->params['breadcrumbs'][] = $this->title;
?>
<div class="row">
<div class="col-lg-10" style = "height:100px;">
<?php $form = ActiveForm::begin([
        'id'   => $model->formName(),
        'action'     => ['user/user/additioninfo'],
        'type'       => ActiveForm::TYPE_VERTICAL,
        'formConfig' => ['deviceSize' => ActiveForm::SIZE_SMALL],
        'method'     => 'get',
                ]); ?>
        <div style="position:absolute;">
            <?= $form->field($model, 'User_id')->input('number',['style'=>'width:100px','maxlength' => 20]) ?>
        </div>
        <div style="position:absolute;margin-left:120px">
            <?= $form->field($model, 'Activity_id')->input('number',['style'=>'width:100px','maxlength' => 20]) ?>
        </div>
        <div style="position:absolute; left:250px;">
            <?= $form->field($model, 'activityDate', [
                    'addon'=>['prepend'=>['content'=>'<i class="glyphicon glyphicon-calendar"></i>']],
                    'options'=>['class'=>'drp-container form-group','style'=>'width:300px']
                ])->widget(DateRangePicker::classname(), [
                    'useWithAddon'=>true,
                    'convertFormat'=>true,
                    'pluginOptions'=>[
                                        'timePicker'=>true,
                                        'timePickerIncrement'=>10,
                                        'locale'=>[
                                                    'format'=>'Y-m-d',
                                                    'separator'=>' 到 ',
                                                  ],
                                     ]
                ]);?>
        </div>
        <div style="position:absolute; left:570px;">
            <?= $form->field($model, 'ActivitySchedule_id')->input('number',['style'=>'width:100px','maxlength' => 20]) ?>
        </div>
        <div style="position:absolute; left:680px;">
            <?= $form->field($model, 'status')->dropDownList(Tool::TRADE_STATUS, ['style'=>'width:100px','prompt' => '']); ?>
        </div>
         <div style="position:absolute; left:800px;">
            <?= $form->field($model, 'orderNumber')->input('number',['style'=>'width:180px','maxlength' => 20]) ?>
        </div>
        <div style="position:absolute; left:1000px; top: 25px">
            <?= Html::submitButton('查询', ['class' => 'btn btn-primary', 'name' => 'signup-button', 'style'=>'width:100px']) ?>
        </div>
        <?php ActiveForm::end(); ?>
    </div>
    
</div>
<div>


<?php 
    $gridColumns = [
                        [
                            'class' => 'yii\grid\CheckboxColumn',
                            'checkboxOptions' => function ($model, $key, $index, $column) {
                                return ['value' => $key];
                            }
                        ],
                        ['label' => '用户ID','value' =>'User_id','options'=>['width'=>"55px"]],
                        ['label' => '订单编号','value' =>'orderNumber','options'=>['width'=>"180px"],],
                        ['label' => '订单状态','value' => function($data){
                            $ragin = Tool::TRADE_STATUS;
                            return $ragin[$data['status']];
                        },'options'=>['width'=>"655px"]],
                        ['label' => '活动ID','value' =>'Activity_id','options'=>['width'=>"165px"]],
                        ['label' => '活动时间','value' =>'activityDate','options'=>['width'=>"165px"]],
                        ['label' => '活动场次','value' =>'activityTime','options'=>['width'=>"165px"]],
                        ['label' => '活动场次ID','value' =>'ActivitySchedule_id','options'=>['width'=>"165px"]],
                        ['label' => '投保人','value' =>'name','options'=>['width'=>"165px"]],
                        ['label' => '联系电话','value' =>'contactPhone','options'=>['width'=>"165px"]],
                        ['label' => '联系人','value' =>'contact','options'=>['width'=>"165px"]],
                        ['label' => '证件类型','value' =>'type','options'=>['width'=>"165px"]],
                        ['label' => '证件号码','value' => 'identityNumber','options'=>['width'=>"165px"]
                            ],
                    ];
?>
<?php 
    if (isset(Yii::$app->request->get()['show']) && Yii::$app->request->get()['show']) {
        $title = '页';
        $url   = Url::current(['show' => '']);
    } else {
        $title = '全部';
        $url   = Url::current(['show' => $pagination->totalCount]);
    }
?>


<?=ExportMenu::widget([
            'dataProvider' => $dataProvider,
            'columns' => $gridColumns,
            'target' => ExportMenu::TARGET_BLANK,
            'hiddenColumns'=>[0],
            'noExportColumns'=>[0],
            'fontAwesome' => true,
            'dropdownOptions' => [
                    'label' => '导出',
                    'class' => 'btn btn-default'
            ]
        ]) . 
        Html::Button('发短消息', ['class' => 'btn btn-primary smessage', 'name' => 'signup-button', 'style'=>'width:100px;float: left; margin-right: 20px']) . "\n" .
        '<div class="btn-group">
            <a title="显示所有数据" href="'. $url .'" class="btn btn-default" > '.$title.'</a>
        </div>' . "\n" .
        GridView::widget([
        'id' => 'select',
                    'dataProvider' => $dataProvider,
                    'panelHeadingTemplate' => '<div class="pull-right">总共' .$pagination->totalCount.'条数据</div>
                                                <h3 class="panel-title">
                                                    {heading}
                                                </h3>
                                                <div class="clearfix"></div>',
                    'panelBeforeTemplate' => '<div class="pull-left">
                                                    <div class="btn-toolbar kv-grid-toolbar" role="toolbar">
                                                        {toolbar}{export}
                                                    </div>    
                                              </div>
                                              <div class="clearfix"></div>',
        'columns' => $gridColumns
    ]);
?>

</div>

<script>
    var data = <?=  $dataProvider->allModels ? json_encode($dataProvider->allModels) : '{}'?>;
</script>
<?=AppAsset::addCss($this,'@web/css/wdialog.css')?>
<?=AppAsset::addScript($this,'@web/js/lib/wdialog.js')?>
<?=AppAsset::addScript($this,'@web/js/lib/user/select.js')?>