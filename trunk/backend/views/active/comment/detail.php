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
use kartik\detail\DetailView;

$this->title = '活动评论查询';
$this->params['breadcrumbs'][] = $this->title;
?>
<div>
<h2><?php //var_dump($model);?></h2>
<?php 
echo DetailView::widget([
                    'model'=>$model,
                    'condensed'=>true,
                    'hover'=>true,
                    'mode'=>DetailView::MODE_VIEW,
                    'buttons1' => '',
                    'panel'=>[
                        'heading'=>'订单号  :  ' . $model->SalesOrder_orderNumber,
                        'type'=>DetailView::TYPE_INFO,
                    ],
                    'attributes'=>[
                        [
                            'attribute' => 'content',
                            'valueColOptions'=>['style'=>'width:180px']
                        ]
                    ]
                ]);
?>
</div>
<div class="row">
    <div class="col-lg-10">
        <?php $form = ActiveForm::begin([
                    'id'     => $model->formName(),
                    'type' => ActiveForm::TYPE_HORIZONTAL,
                    'formConfig' => ['labelSpan' => 3, 'deviceSize' => ActiveForm::SIZE_SMALL]
                    //'action' => ['/lucky/lucky/update'],
                ]); 
            ?>
        <?= $form->field($model, 'top')->radioList(Tool::ACTIVE_COMMENT_TOP, ['inline'=>true]);?>
        <?= $form->field($model, 'id')->hiddenInput() ?>
        
    </div>
    <?php ActiveForm::end(); ?>
</div>
<script>
    //记录原先数据
    var lastData = {'top' : <?php echo $model->top;?>};
</script>