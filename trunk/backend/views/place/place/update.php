<?php
use yii\helpers\Html;
//use yii\bootstrap\ActiveForm;
use yii\grid\GridView;
use backend\assets\AppAsset;
use kartik\form\ActiveForm;
use kartik\form\ActiveField;
use backend\components\Tool;

$this->title = 'select';
$this->params['breadcrumbs'][] = $this->title;
?>

<div class="row">
        <div class="col-lg-10"> 
            <?php $form = ActiveForm::begin([
                    'id'     => $model->formName(),
                    'type' => ActiveForm::TYPE_HORIZONTAL,
                    'formConfig' => ['labelSpan' => 3, 'deviceSize' => ActiveForm::SIZE_SMALL],
                ]); 
            ?>
            <?= $form->field($model, 'id')->hiddenInput() ?>
            <?= $form->field($model, 'venueName')->input('text',['style'=>'width:250px','maxlength' => 30, 'nt' => 'test'])  ?>
            <?= $form->field($model, 'venueAddr')->input('text',['style'=>'width:250px','maxlength' => 50])  ?>
            <?= $form->field($model, 'longitude')->input('text',['style'=>'width:250px'])?>
            <?= $form->field($model, 'latitude')->input('text',['style'=>'width:250px'])?>
            <?= $form->field($model, 'Region_districtId')->dropDownList(Tool::RAGIN, ['style'=>'width:120px','prompt' => ''])?>
            
            
            <!-- <div class="form-group">
                <?php //= Html::submitButton('查询', ['class' => 'btn btn-primary add', 'name' => 'signup-button']) ?>
            </div> -->
         </div>
            <?php ActiveForm::end();?>
</div>
