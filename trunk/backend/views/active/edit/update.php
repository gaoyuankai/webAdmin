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
$this->title = '更新活动';
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
                <?php echo $form->errorSummary($model); ?>
                <a href="?r=active/view&id=<?=$id?>"><?php echo Html::button('返回活动详情页', ['class' => 'btn btn-primary', 'name' => 'signup-button', 'style'=>'width:130px'])?></a>
                <?= $form->field($model, 'id')->hiddenInput() ?>
              <?php if($type==1){?>
                <?= $form->field($model, 'name')->input('text',['style'=>'width:420px', 'maxlength' => 30])?>
                <?= $form->field($model, 'activityKind')->radioList(Tool::ACTIVE_ACTIVITYKIND, ['inline'=>true])?>
                <?= $form->field($model, 'insurance')->radioList(Tool::ACTIVE_INSURANCE, ['inline'=>true]);?>
                <?= $form->field($model, 'stars')->dropDownList(Tool::ACTIVE_STARS, ['style'=>'width:120px']);?>
                <?= $form->field($model, 'ageGroup')->checkboxList(Tool::ACTIVE_AGEGROUP,  ['inline'=>true ]);?>
                <?= $form->field($model, 'ability')->checkboxList(Tool::ACTIVE_ABILITY, ['inline'=>true]);?>
                <?= $form->field($model, 'sponsor')->input('text',['style'=>'width:420px', 'maxlength' => 30]) ?>
                <?= $form->field($model, 'Venues_id')->input('number',['style'=>'width:200px']) ?>
                <?= $form->field($model, 'highlights')->input('text',['style'=>'width:420px', 'placeholder'=>'以英文逗号进行分割']); ?>
                <?= $form->field($model, 'brief')->textarea(['rows'=>5, "maxlength" => 100 , 'style'=>'width:420px', 'placeholder'=>'字数限制100字']) ?>
                <?= $form->field($model, 'periodInfo')->input('text',['style'=>'width:420px', 'maxlength' => 50]) ?>
              <?php }?>
              
              <?php if($type==2){?>
                 <?=$form->field($model, 'description')->widget(\crazydb\ueditor\UEditor::className()) ?>
              <?php }?>
              <?php if($type==5){?>
                    <?php
                    $pic_config = [
                           'options' => ['multiple' => true,'accept' => 'image/*'],
                           'pluginOptions' => [
                               'uploadUrl'=>"http://local.yii2.cn/",
                               'allowedFileExtensions' => ['jpg','jpeg','png'],
                               'validateInitialCount'=> true,
                               'showRemove' => true,
                               'showUpload' => false,
                               'layoutTemplates'=>['actions'=>
                                        '<div class="file-actions">
                                        <div class="file-footer-buttons">{delete}</div>
                                        </div>',
                                ],
                           ],
                        ];
                    ?>
                     <?php
                     //封面图配置
                     $pic_config1=$pic_config;
                     $pic_config1['pluginOptions']['overwriteInitial'] = true;
                     $pic_config1['pluginOptions']['maxFileCount'] = 1;
                     //多图配置
                     $pic_config['pluginOptions']['overwriteInitial'] = false;
                     $pic_config['pluginOptions']['maxFileCount'] = 5;
                     $cover=array_shift($model->picUrls);
                     
                     $pic_config1['pluginOptions']['initialPreview']=Html::img($cover, ['class'=>'file-preview-image'])."<input type='hidden' name='cover' value='".$cover."'";;
                         for ($i=0,$j=count($model->picUrls);$i<$j;$i++){
                             $pic_config['pluginOptions']['initialPreview'][$i]=Html::img($model->picUrls[$i], ['class'=>'file-preview-image'])."<input type='hidden' name='old[]' value='".$model->picUrls[$i]."'";
                             $pic_config['pluginOptions']['initialPreviewConfig'][$i]=[ 'width'=>"120px", 'url'=> "/index.php?r=active/edit/imgdelete", 'key'=> $i,];
                          }
                         
                     ?>
                    <?= $form->field($model, 'cover')->widget(FileInput::classname(), $pic_config1)->hint('封面图片上传，图片张数多1张,图片最大为500K');?>
                    <?= $form->field($model, 'picUrls[]')->widget(FileInput::classname(), $pic_config)->hint('图片上传，图片张数最少2张，最多5张，图片最大为500K');?>
                <?php }?>
                
                <?php if($type==3){?>
                    <?= $form->field($model, 'priceKind')->radioList(Tool::ACTIVE_PRICEKIND, ['inline'=>true]);?>
                    <?= $form->field($model, 'adultPrice')->input('number',['style'=>'width:200px', 'id' => 'adultPrice'])->hint('活动价格类型 为大人小孩分开计算 时必填') ?>
                    <?= $form->field($model, 'kidPrice')->input('number',['style'=>'width:200px', 'id' => 'kidPrice'])->hint('活动价格类型 为大人小孩分开计算 时必填') ?>
                    <?= $form->field($model, 'totalPrice')->input('number',['style'=>'width:200px', 'id' => 'totalPrice'])->hint('活动价格类型 <b>不是</b>为大人小孩分开计算 时必填') ?> 
                <?php }?>
                <?php if($type==4){?>
                    <?php echo Html::Button('添加场次', ['class' => 'btn btn-primary addSchedule', 'name' => 'signup-button', 'style'=>'width:100px']);?>
                    <div style = "margin-top: 10px">
                    <?php 
                        echo "\n";
                        Pjax::begin(['id'=>'grid']);?>
                            <?php
                            $provider1 = new ArrayDataProvider([
                                            'allModels' => $data,
                                        ]);
                            echo $this->render('scheduletable', [
                                'dataProvider' => $provider1,
                            ]) ?>
                    <?php    Pjax::end();
                    ?>
                    </div>
                <?php }?>
                
                <div class="form-group">
                
                    <?php if($type!=4){
                       echo Html::submitButton('保存', ['class' => 'btn btn-primary', 'name' => 'signup-button', 'style'=>'width:100px']);
                        //echo Html::button('新增', ['class' => 'btn btn-primary add', 'name' => 'signup-button', 'style'=>'width:100px;margin-left:50px']);
                    }?>
                    
                </div>
                
            <?php ActiveForm::end(); ?>
        </div>
    </div>
</div>
<script type="text/javascript">
           //当前活动id
           var activityId='<?=$id?>';
          //获取当前编辑区域
          var type = <?=$type?>;
           if(type==1){
           var ageGroup  = <?php print_r(json_encode($model->ageGroup))?>;
           var ability   = <?php print_r(json_encode($model->ability))?>;
           }
           if(type==3){
              var priceKind = '<?=$model['priceKind']?>';
            }

</script>
<?=AppAsset::addCss($this,'@web/css/wdialog.css')?>
<?=AppAsset::addScript($this,'@web/js/lib/wdialog.js')?>
<?=AppAsset::addScript($this,'@web/js/lib/active/edit/update.js')?>