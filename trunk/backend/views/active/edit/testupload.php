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
use yii\widgets\Pjax;
use kartik\file\FileInput;
use yii\data\ArrayDataProvider;
$this->title = 'select';
$this->params['breadcrumbs'][] = $this->title;
?>
<div class="site-signup">
    <div class="row">
        <div class="col-lg-15" >
            <?php $form = ActiveForm::begin([
                    'id'         => $model->formName(),
                    'type' => ActiveForm::TYPE_HORIZONTAL,
                    'formConfig' => ['labelSpan' => 2, 'deviceSize' => ActiveForm::SIZE_SMALL],
                    'options' => [ 'enctype' => 'multipart/form-data' ]
                ]); ?>
            <?= $form->field($model, 'picUrls[]')->widget(FileInput::classname(), [
                        'options'=>[
                            'multiple'=>true,
                            'accept' => 'image/*'
                        ],
                        'pluginOptions' => [
                            //'uploadUrl' => Url::to(['active/edit/uploadimg']),
                            'allowedFileExtensions'=>['jpg', 'gif', 'png', 'bmp'],
                            'showUpload' => false,
                            'overwriteInitial' => false,
                            'maxFileCount' => 6,
                            'minFileCount' => 3,
                            /* 'showPreview' => true,
                            'showCaption' => false,
                            'showRemove' => false,
                            'showUpload' => false,
                        ]
                    ])->hint('图片上传，3≤图片张数≤6，图片最大为500K');?>
                    
                <div class="form-group">
                    <?php
                       echo Html::submitButton('发布', ['class' => 'btn btn-primary', 'name' => 'signup-button', 'style'=>'width:100px']);
                        //echo Html::button('新增', ['class' => 'btn btn-primary add', 'name' => 'signup-button', 'style'=>'width:100px;margin-left:50px']);
                    ?>
                    
                </div>
                
            <?php ActiveForm::end(); ?>
        </div>
    </div>
</div>