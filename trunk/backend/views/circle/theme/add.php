<?php
use yii\helpers\Html;
use backend\assets\AppAsset;
use yii\helpers\Url;
use kartik\form\ActiveForm;
use kartik\form\ActiveField;
use backend\components\Tool;
use kartik\file\FileInput;

$this->title = $circle_name.'/添加主题';
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
               <?=$form->field($model, 'content')->textarea(['rows'=>5, 'style'=>'width:500px', 'placeholder'=>'字数限制200字','maxlength' => 200])?>
               <?=$form->field($model, 'sysAdmin')->dropDownList(Tool::THEME_TYPE, ['style'=>'width:120px'])?>
               <?=$form->field($model, 'top')->radioList(Tool::THEME_TOP, ['inline'=>true])?>
               <?=$form->field($model, 'themePictures[]')->widget(FileInput::classname(), [
                       'options' => ['multiple' => true,'accept' => 'image/*'],
                       'pluginOptions' => ['uploadUrl' => Url::to(['circle/theme/uploadimg']),
                       'allowedFileExtensions' => ['jpg','jpeg','png'],
                       'showUpload' => false,
                       'overwriteInitial' => false,
                       'maxFileCount' => 9,
                       'minFileCount' => 0,
                        'layoutTemplates'=>['actions'=>
                        '<div class="file-actions">
                                        <div class="file-footer-buttons">{delete}</div>
                                        </div>',
                         ],
                   ]])->hint('图片上传，图片张数不超过9张');?>
            <div class="form-group">
                   <?php
                       echo Html::submitButton('新增', 
                            [
                                    'class' => 'btn btn-primary add',
                                    'name'  => 'signup-button',
                                    'style' => 'width:100px'
                            ]);
                   ?>
            </div>
            <input type="hidden" name="Circle_id" value="<?php echo $circle_id?>" />
            <?php ActiveForm::end(); ?>
        </div>
	</div>
</div>

<?=AppAsset::addCss($this,'@web/css/wdialog.css')?>
<?=AppAsset::addScript($this,'@web/js/lib/wdialog.js')?>

