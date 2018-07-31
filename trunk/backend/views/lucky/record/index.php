<?php
use yii\helpers\Html;
//use yii\bootstrap\ActiveForm;
use kartik\grid\GridView;
use backend\assets\AppAsset;
use yii\widgets\LinkPager;
use backend\components\Tool;
use kartik\form\ActiveForm;
use kartik\daterange\DateRangePicker;

$this->title = '红包发送记录列表';
$this->params['breadcrumbs'][] = $this->title;
?>
<div class="site-signup">
    <div class="row">
        <div class="col-lg-10" style = "height:100px;"> 
            <?php $form = ActiveForm::begin([
                            'id'         => $model->formName(),
                            'action'     => ['/lucky/record/list'],
                            'type'       => ActiveForm::TYPE_VERTICAL,
                            'formConfig' => ['deviceSize' => ActiveForm::SIZE_SMALL],
                            'method'     => 'get',
                    ]); ?>
            <div style="position:absolute;">
                <?= $form->field($model, 'User_id')->input('number',['style'=>'width:150px','maxlength' => 20]) ?>
            </div>
            <div style="position:absolute; left:210px;">
                <?= $form->field($model, 'couponName')->input('text',['style'=>'width:150px','maxlength' => 20]) ?>
                
            </div>
            <div style="position:absolute; left:400px;">
                <?= $form->field($model, 'createTime', [
                        'addon'=>['prepend'=>['content'=>'<i class="glyphicon glyphicon-calendar"></i>']],
                        'options'=>['class'=>'drp-container form-group','style'=>'width:380px']
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
            <div style="position:absolute; left:810px;">
                <?= $form->field($model, 'status')->dropDownList(Tool::LUCKY_USE_STATUS, ['style'=>'width:150px','prompt' => '']); ?>
            </div>
            <div style="position:absolute; left:1020px; top: 25px">
                <?= Html::submitButton('查询', ['class' => 'btn btn-primary', 'name' => 'signup-button','style'=>'width:90px']) ?>
            </div>
            <?php ActiveForm::end(); ?>
        </div>
    </div>
</div>
<div>
    <?=GridView::widget([
        'toolbar' => [],
        'panel'=>[
            'type'=>'primary',
            'heading'=>$this->title
        ],
        'panelHeadingTemplate' => '<div class="pull-right">总共' .$pagination->totalCount.'条数据</div>
                                        <h3 class="panel-title">
                                            {heading}
                                        </h3>
                                        <div class="clearfix"></div>',
        'dataProvider' => $dataProvider,
        'columns' => [
                        //['label' => 'ID','value' =>'id'],
                        ['label' => '红包名称','value' =>'couponName','options'=>['width'=>"100px"],],
                        ['label' => '用户ID','value' =>'User_id','options'=>['width'=>"70px"]],
                        [
                            'label' => '订单状态',
                            'value' =>function($data){
                                //return json_encode($data);
                                $STATUS = Tool::LUCKY_USE_STATUS;
                                return $STATUS[$data['status']];
                            },
                            'options'=>['width'=>"70px"]
                        ],
                        ['label' => '发放时间','value' => 'createTime','options'=>['width'=>"100px"]],
                        ['label' => '使用时间','value' => 'useTime','options'=>['width'=>"100px"]],
                        ['label' => '说明','value' => 'brief','options'=>['width'=>"250px"]],
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