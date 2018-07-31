<?php
use yii\helpers\Html;
use backend\assets\AppAsset;
use yii\helpers\Url;
use kartik\form\ActiveForm;
use kartik\form\ActiveField;
use backend\components\Tool;
use kartik\file\FileInput;

$this->title = "圈子操作";
$this->params['breadcrumbs'][] = $this->title;
?>
<h3></h3>
<div class="site-signup">
	<div class="row">
		<div class="col-lg-15">
            <?php
                $form = ActiveForm::begin([
                    'id'   => $model->formName(),
                    'type' => ActiveForm::TYPE_HORIZONTAL,
                    'formConfig' => [
                            'labelSpan' => 2,
                            'deviceSize' => ActiveForm::SIZE_SMALL
                    ],
                    'options' => [
                            'enctype' => 'multipart/form-data',
                    ]
                ]);
            ?>
               <?=$form->field($model, 'id')->hiddenInput() ?>
               <?=$form->field($model, 'name')->input('text',['inline'=>true,'style'=>'width:200px' ,'maxlength' => 30])?>
               <?=$form->field($model, 'activityId')->input('number',['style'=>'width:200px'])?>
               <?=$form->field($model, 'status')->radioList(Tool::CIRCLE_STATUS, ['inline'=>true])?>
               <?=$form->field($model, 'brief')->textarea(['rows'=>2, 'style'=>'width:500px','maxlength' => 60, 'placeholder'=>'字数限制60字'])?>
               <?php if($action == "update"){
                   $pic_config = [
                       'options' => ['multiple' => true,'accept' => 'image/*'],
                       'pluginOptions' => [
                           'allowedFileExtensions' => ['jpg','jpeg','png'],
                           'showUpload' => false,
                           'overwriteInitial' => true,
                           'maxFileCount' => 1,
                           'minFileCount' => 0,
                       ],
                   ];
               ?>
               <?php if ($model->circlePicture) {
                        $pic_config['pluginOptions']['initialPreview'] = [Html::img($model->circlePicture, ['class'=>'file-preview-image'])."<input type='hidden' name='old1' value='mark1'",];
                    }
               ?>
               <?= $form->field($model, 'circlePicture')->widget(FileInput::classname(), $pic_config)->hint('图片上传，图片张数=1，图片最大为500K');?>
               <?php if($model->linkPicture) {
                        $pic_config['pluginOptions']['initialPreview'] = [Html::img($model->linkPicture, ['class'=>'file-preview-image'])."<input type='hidden' name='old2' value='mark2'",];
                }?>
               <?= $form->field($model, 'linkPicture')->widget(FileInput::classname(), $pic_config)->hint('图片上传，图片张数=1，图片最大为500K');?>
               <?php }?>
               <?php if($action == "add"){
                $pic_config = [
                       'options' => ['multiple' => true,'accept' => 'image/*'],
                       'pluginOptions' => [
                           'allowedFileExtensions' => ['jpg','jpeg','png'],
                           'showUpload' => false,
                           'overwriteInitial' => true,
                           'maxFileCount' => 1,
                           'minFileCount' => 1,
                       ],
                    ];
                ?>
               <?= $form->field($model, 'circlePicture')->widget(FileInput::classname(), $pic_config)->hint('图片上传，图片张数=1，图片最大为500K');?>
               <?= $form->field($model, 'linkPicture')->widget(FileInput::classname(), $pic_config)->hint('图片上传，图片张数=1，图片最大为500K');?>
               <?php }?>
            <div class="form-group">
                   <?php
                       echo Html::submitButton($action=="add"?"新增":"更新", 
                            [
                                    'class' => 'btn btn-primary add',
                                    'name'  => 'signup-button',
                                    'style' => 'width:100px'
                            ]);
                   ?>
                   </div>
            <?php ActiveForm::end(); ?>
        </div>
	</div>
</div>

<?=AppAsset::addCss($this,'@web/css/wdialog.css')?>
<?=AppAsset::addScript($this,'@web/js/lib/wdialog.js')?>

