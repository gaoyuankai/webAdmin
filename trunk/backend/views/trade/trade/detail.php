<?php
use yii\helpers\Html;
//use yii\bootstrap\ActiveForm
use backend\assets\AppAsset;
use kartik\detail\DetailView;
use kartik\form\ActiveForm;

$this->title = 'detail';
$this->params['breadcrumbs'][] = $this->title;
//->hint(\Yii::t('app', 'Staff/Student Number'))
?>
<div class="row">
    <div class="col-lg-10" >
    <?php echo DetailView::widget([
                    'model' => $model,
                    'mode'=>DetailView::MODE_VIEW,
                    'attributes'=>[
                                    //'name',
                                    [
                                        'columns' => [
                                           
                                            [
                                                'attribute'=>'orderNumber',
                                                'value' => $model->orderNumber,
                                                'labelColOptions'=>['style'=>'width:70px'],
                                                'valueColOptions'=>['style'=>'width:180px']
                                            ],
                                            [
                                                'attribute'=>'createTime',
                                                'value' => $model->createTime,
                                                'labelColOptions'=>['style'=>'width:70px'],
                                                'valueColOptions'=>['style'=>'width:180px']
                                            ],
                                            [
                                                'attribute'=>'status',
                                                'value' => $tradeStatus[$model->status],
                                                'labelColOptions'=>['style'=>'width:70px'],
                                                'valueColOptions'=>['style'=>'width:180px']
                                            ],
                                        ],
                                        
                                    ],
                                    [
                                        'columns' => [
                                            [
                                                'attribute'=>'contact',
                                                'value' => $model->contact,
                                                'labelColOptions'=>['style'=>'width:70px'],
                                                'valueColOptions'=>['style'=>'width:180px']
                                            ],
                                            [
                                                'attribute'=>'contactPhone',
                                                'value' => $model->contactPhone,
                                                'labelColOptions'=>['style'=>'width:70px'],
                                                'valueColOptions'=>['style'=>'width:180px']
                                            ],
                                        ]
                                    ],
                            ],
                ]);
    ?>
    <?php echo DetailView::widget([
                    'model' => $model,
                    'mode'=>DetailView::MODE_VIEW,
                    'attributes'=>[
                                    //'name',
                                    [
                                        'columns' => [
                                           
                                            [
                                                'attribute'=>'name',
                                                'value' => $model->name,
                                                'labelColOptions'=>['style'=>'width:70px'],
                                                'valueColOptions'=>['style'=>'width:180px']
                                            ],
                                            [
                                                'attribute'=>'activityTime',
                                                'value' => $model->activityDate.' '.$model->activityTime,
                                                'labelColOptions'=>['style'=>'width:70px'],
                                                'valueColOptions'=>['style'=>'width:180px']
                                            ],
                                            [
                                                'attribute'=>'price',
                                                'value' => $model->price,
                                                'labelColOptions'=>['style'=>'width:70px'],
                                                'valueColOptions'=>['style'=>'width:180px']
                                            ],
                                        ],
                                        
                                    ],
                                    [
                                        'columns' => [
                                            [
                                                'attribute'=>'quantity',
                                                'value' => $model->quantity,
                                                'labelColOptions'=>['style'=>'width:70px'],
                                                'valueColOptions'=>['style'=>'width:180px']
                                            ],
                                            [
                                                'attribute'=>'totalPrice',
                                                'value' => $model->totalPrice,
                                                'labelColOptions'=>['style'=>'width:70px'],
                                                'valueColOptions'=>['style'=>'width:180px']
                                            ],
                                            [
                                                'attribute'=>'actTotalPrice',
                                                'value' => $model->actTotalPrice,
                                                'labelColOptions'=>['style'=>'width:70px'],
                                                'valueColOptions'=>['style'=>'width:180px']
                                            ],
                                        ]
                                    ],
                            ],
                ]);
    ?>
    <?php 
            echo DetailView::widget([
                        'model' => $model,
                        'mode'=>DetailView::MODE_VIEW,
                        'attributes'=>[
                            'userNote',
                        ],
                    ]);
            if(in_array($model->status, [5,6]) && !isset($isDetail)) {
                echo DetailView::widget([
                        'model' => $model,
                        'mode'=>DetailView::MODE_VIEW,
                        'attributes'=>[
                                        'orderNumber',
                                        'applyReason',
                                        [
                                            'attribute'=>'status',
                                            'value' => $model->status == 5 ? '处理退款申请' : '完成退款操作',
                                        ],
                                ],
                    ]);
                if($model->status == 5) {
                    $form = ActiveForm::begin([
                        'id' => $model->formName(),
                    ]);
                    echo $form->field($model, 'adminNote')->textarea(['rows'=>5]);
                    ActiveForm::end();
                } else {
                    echo DetailView::widget([
                            'model' => $model,
                            'mode'=>DetailView::MODE_VIEW,
                            'attributes'=>[
                                            'adminNote',
                            ],
                        ]);
                }
            }
    ?>
    </div>
</div>
<script>
    var refund_model = <?=  json_encode($model)?>;
</script>