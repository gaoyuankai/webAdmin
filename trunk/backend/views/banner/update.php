<?php
use yii\helpers\Html;
use backend\assets\AppAsset;
use yii\helpers\Url;
use kartik\form\ActiveForm;
use kartik\form\ActiveField;
use backend\components\Tool;
use kartik\file\FileInput;

$this->title = '修改配置';
$this->params['breadcrumbs'][] = $this->title;

?>

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
                            'enctype' => 'multipart/form-data'
                    ]
                ]);
            ?>
               <?=$form->field($model, 'id')->hiddenInput() ?>
               <div style="margin-left:140px"><b>类型</b>&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;<?=Tool::BANNER_TYPE[$model->type]?></div>
               <?=$form->field($model, 'status')->radioList(Tool::BANNER_STATUS, ['inline'=>true])?>
               <?=$form->field($model, 'title')->input('text',['style'=>'width:200px', 'maxlength' => 20,'value'=>empty($data['items']['title'])?"":$data['items']['title']])?>
               <?=$form->field($model, 'url')->input('text',['style'=>'width:200px','value'=>empty($data['items']['url'])?"":$data['items']['url']])?>
               <?=$form->field($model, 'kind')->radioList(Tool::BANNER_ATYPE, ['inline'=>true])?>
               <?=$form->field($model, 'asociateActivityidORassociateCircleid')->input('number',['style'=>'width:200px','value'=>empty($data['items']['id'])?"":$data['items']['id']])?>
               <?=$form->field($model, 'sort')->input('number',['style'=>'width:200px','value'=>$data['sort']])?> 
                <?php 
                $pic_config = [
                       'options' => ['multiple' => true,'accept' => 'image/*'],
                       'pluginOptions' => [
                           'allowedFileExtensions' => ['jpg','jpeg','png'],
                           'showUpload' => false,
                           'overwriteInitial'    => true,
                           'showRemove' => true,
                           'maxFileCount' => 1,
                           'layoutTemplates'=>['actions'=>
                           '<div class="file-actions">
                                       <div class="file-footer-buttons">{delete}</div>
                                        </div>',
                                        ],
                       ],
                    ];
                ?>
               <?php if ($model->picture) {
                        $pic_config['pluginOptions']['initialPreview'] = [Html::img($model->picture, ['class'=>'file-preview-image'])."<input type='hidden' name='old' value='mark'",];
                    }
               ?>
               <?= $form->field($model, 'picture')->widget(FileInput::classname(), $pic_config)->hint('图片上传，图片张数=1，图片最大为1M');?>
                   <div class="form-group">
                   <?php
                       echo Html::submitButton('更新', 
                            [
                                    'class' => 'btn btn-primary add',
                                    'name' => 'signup-button',
                                    'style' => 'width:100px'
                            ]);
                   ?>
                   </div>
            <?php ActiveForm::end(); ?>
        </div>
	</div>
</div>

<script>
     var type = "<?=$data['type']?>"
</script>
<?=AppAsset::addCss($this,'@web/css/wdialog.css')?>
<?=AppAsset::addScript($this,'@web/js/lib/wdialog.js')?>
<?=AppAsset::addScript($this,'@web/js/lib/banner/edit/upd.js')?>