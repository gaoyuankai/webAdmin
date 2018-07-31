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
$this->title = '添加活动';
$this->params['breadcrumbs'][] = $this->title;
?>
<h3>
<?php 
    if($res) {
        echo "<script>alert('添加成功')</script>";
    }
?>
</h3>
<div class="site-signup">
    <div class="row">
        <div class="col-lg-15" >
            <?php $form = ActiveForm::begin([
                    'id'         => $model->formName(),
                    'type' => ActiveForm::TYPE_HORIZONTAL,
                    'formConfig' => ['labelSpan' => 2, 'deviceSize' => ActiveForm::SIZE_SMALL],
                    'options' => [ 'enctype' => 'multipart/form-data' ]
                ]); ?>
                <?php echo $form->errorSummary($model); ?>
                <?= $form->field($model, 'name')->input('text',['style'=>'width:420px', 'maxlength' => 30]) ?>
                <!--<?//echo $form->field($model, 'status')->radioList(Tool::ACTIVE_STATUS, ['inline'=>true])?>-->
                <?= $form->field($model, 'activityKind')->radioList(Tool::ACTIVE_ACTIVITYKIND, ['inline'=>true])?>
                <?= $form->field($model, 'insurance')->radioList(Tool::ACTIVE_INSURANCE, ['inline'=>true]);?>
                <?= $form->field($model, 'stars')->dropDownList(Tool::ACTIVE_STARS, ['style'=>'width:120px']);?>
                <?= $form->field($model, 'ageGroup')->checkboxList(Tool::ACTIVE_AGEGROUP, ['inline'=>true]);?>
                <?= $form->field($model, 'ability')->checkboxList(Tool::ACTIVE_ABILITY, ['inline'=>true]);?>
                <?= $form->field($model, 'sponsor')->input('text',['style'=>'width:420px', 'maxlength' => 30]) ?>
                <?= $form->field($model, 'Venues_id')->input('number',['style'=>'width:200px']) ?>
                <?= $form->field($model, 'highlights')->input('text',['style'=>'width:420px', 'placeholder'=>'以英文逗号进行分割']); ?>
                <?= $form->field($model, 'priceKind')->radioList(Tool::ACTIVE_PRICEKIND, ['inline'=>true]);?>
                <?= $form->field($model, 'adultPrice')->input('number',['style'=>'width:200px', 'id' => 'adultPrice'])->hint('活动价格类型 为大人小孩分开计算 时必填') ?>
                <?= $form->field($model, 'kidPrice')->input('number',['style'=>'width:200px', 'id' => 'kidPrice'])->hint('活动价格类型 为大人小孩分开计算 时必填') ?>
                <?= $form->field($model, 'totalPrice')->input('number',['style'=>'width:200px', 'id' => 'totalPrice'])->hint('活动价格类型 <b>不是</b>为大人小孩分开计算 时必填') ?>
                <?= $form->field($model, 'brief')->textarea(['rows'=>5, "maxlength" => 100 ,'style'=>'width:420px', 'placeholder'=>'字数限制100字']) ?>
                <?= $form->field($model, 'periodInfo')->input('text',['style'=>'width:420px', 'maxlength' => 50]) ?>
                <?php echo Html::Button('添加场次', ['class' => 'btn btn-primary addSchedule', 'name' => 'signup-button', 'style'=>'width:100px']);?>
                <div style = "margin-top: 10px">
                <?php 
                    echo "\n";
                    Pjax::begin(['id'=>'grid']);?>
                        <?php
                        $provider = new ArrayDataProvider([
                                        'allModels' => is_array($model->activitySchedule) ? $model->activitySchedule : json_decode($model->activitySchedule, true),
                                    ]);
                        echo $this->render('scheduletable', [
                            'dataProvider' => $provider,
                        ]) ?>
                <?php    Pjax::end();
                ?>
                </div>
                <?= $form->field($model, 'description')->widget(\crazydb\ueditor\UEditor::className()) ?>
                <?=$form->field($model, 'cover')->widget(FileInput::classname(), [
                       'options' => ['accept' => 'image/*'],
                       'pluginOptions' => [
                       'allowedFileExtensions' => ['jpg','jpeg','png'],
                           'showUpload' => false,
                           'maxFileCount' => 1,
                           'minFileCount' => 1,
                     ]
             ])->hint('封面图片上传，图片张数不超过1张,图片最大为500K');?>
                   
                    <?= $form->field($model, 'picUrls[]')->widget(FileInput::classname(), [
                        'options'=>[
                            'multiple'=>true,
                            'accept' => 'image/*'
                        ],
                        'pluginOptions' => [
                            'allowedFileExtensions'=>['jpg', 'gif', 'png', 'bmp'],
                            'showUpload'       => false,
                            'overwriteInitial' => false,
                            'maxFileCount'     => 5,
                            'minFileCount'     => 2
                        ]
                    ])->hint('图片上传，2≤图片张数≤5，图片最大为500K');?>
                    
                <div class="form-group">
                    <?php
                       echo Html::submitButton('发布', ['class' => 'btn btn-primary', 'name' => 'signup-button', 'style'=>'width:100px']); 
                    ?>
                    
                </div>
                
            <?php ActiveForm::end(); ?>
        </div>
    </div>
</div>
<?=AppAsset::addCss($this,'@web/css/wdialog.css')?>
<?=AppAsset::addScript($this,'@web/js/lib/wdialog.js')?>
<?=AppAsset::addScript($this,'@web/js/lib/active/edit/index.js')?>