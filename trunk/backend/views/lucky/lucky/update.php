<?php
use yii\helpers\Html;
//use yii\bootstrap\ActiveForm;
use yii\grid\GridView;
use backend\assets\AppAsset;
//use yii\jui\DatePicker;
use kartik\form\ActiveForm;
use kartik\form\ActiveField;
use kartik\datecontrol\DateControl;
use kartik\datetime\DateTimePicker;
use kartik\date\DatePicker;
use backend\components\Tool;

$this->title = 'select';
$this->params['breadcrumbs'][] = $this->title;
AppAsset::register($this);
//->hint(\Yii::t('app', 'Staff/Student Number'))
?>
<?php $this->beginPage() ?>
<!DOCTYPE html>
<html lang="<?= Yii::$app->language ?>">
<head>
    <meta charset="<?= Yii::$app->charset ?>">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <?= Html::csrfMetaTags() ?>
    <title><?= Html::encode($this->title) ?></title>
    <?php $this->head() ?>
</head>
<body>
<?php $this->beginBody() ?>
<div class="row">
        <div class="col-lg-10"> 
            <?php $form = ActiveForm::begin([
                    'id'     => $model->formName(),
                    'type' => ActiveForm::TYPE_HORIZONTAL,
                    'formConfig' => ['labelSpan' => 3, 'deviceSize' => ActiveForm::SIZE_SMALL]
                ]); 
            ?>
            <?= $form->field($model, 'id')->hiddenInput() ?>
            <?= $form->field($model, 'couponName')->input('text',['style'=>'width:250px','maxlength' => 20])  ?>
            <?= $form->field($model, 'couponCode')
                    ->input('text',['style'=>'width:250px', 'placeholder'=>'编码以日期+红包类型+红包金额'])
                    ->hint('如：'.date('Ymd').'MANJIAN20');
            ?>
            <?= $form->field($model, 'brief')->textarea(['rows'=>5,'maxlength' => 200]) ?>
            <?= $form->field($model, 'isExpire')->radioList(Tool::LUCKY_ISEXPIRE, ['inline'=>true]);?>
            <?= $form->field($model, 'expireFrom')
                            ->widget(DatePicker::classname(), 
                            [
                                'value' => date('Y-m-d'),
                                'pluginOptions' => [
                                    'autoclose'=>true,
                                    'format' => 'yyyy-mm-dd',
                                    'todayHighlight' => true,
                                    'todayBtn' => true,
                                ]
                            ])->hint('红包使用开始时间默认时分秒为: 00:00:00 ');?>
            <?= $form->field($model, 'expireTo')
                            ->widget(DatePicker::classname(),
                            [
                                'value' => date('Y-m-d'),
                                'pluginOptions' => [
                                'autoclose'=>true,
                                'format' => 'yyyy-mm-dd',
                                'todayHighlight' => true,
                                'todayBtn' => true,
                                ]
                            ])->hint('红包使用结束时间默认时分秒为: 23:59:59 ');
            ?>
            <?= $form->field($model, 'expireDays')->input('number',['style'=>'width:250px', 'placeholder'=>'领取后几天过期'])?>
            <?= $form->field($model, 'totalQty')->input('number',['style'=>'width:250px'])?>
            <?= $form->field($model, 'status')->radioList(Tool::LUCKY_STATUS, ['inline'=>true]) ?>
            <?= $form->field($model, 'kind')->radioList(Tool::LUCKY_KIND, ['inline'=>true])?>
            <?= $form->field($model, 'discountPrice')->input('text',['style'=>'width:250px'])->hint('优惠价格例: 100.00;折扣数例: 0.80 => (8折)')?>
            <?= $form->field($model, 'conditionPrice', [
                    'addon' => [
                                'append' => ['content'=>'元', 'options'=>['style' => 'float:left;width:33px;height:34px']],
                                'prepend' => ['content'=>'>=', 'options'=>['style' => 'height:34px']]
                            ],
                ])
            ->input('number',['style'=>'width:185px'])->hint('使用条件最低范围应高于红包金额');?>
         </div>
            <?php ActiveForm::end();?>
            
</div>
<?php $this->endBody() ?>
<script type="text/javascript">
<!--
document.getElementById("luckylistform-expirefrom").style.width = '170px'; 
document.getElementById("luckylistform-expireto").style.width = '170px'; 
var expirefrom  = $(".form-group.field-luckylistform-expirefrom");
var expireto    = $(".form-group.field-luckylistform-expireto");
var expiredays  = $(".form-group.field-luckylistform-expiredays");
$(function(){
	$value = <?=$model['isExpire']?>;
	if($value == 0) {
    	expirefrom.show();
    	expireto.show();
    	expiredays.hide();
    }
    if ($value == 1) {
    	expirefrom.hide();
    	expireto.hide();
    	expiredays.show();
    }
    $kind = <?=$model['kind']?>;
    if ($kind == 2) {
      $("label[for=luckylistform-discountprice]").html("折扣数");
    }
});
$('input[name="LuckyListForm[kind]"]').click(function(){  
	$kind = $('input[name="LuckyListForm[kind]"]:checked').val();
	if ($kind == 2) {
	      $("label[for=luckylistform-discountprice]").html("折扣数");
	} else {
		 $("label[for=luckylistform-discountprice]").html("优惠价格");
	}
});

$('input[name="LuckyListForm[isExpire]"]').click(function(){  
	$value = $('input[name="LuckyListForm[isExpire]"]:checked').val();
    if($value == 0) {
    	expirefrom.show();
    	expireto.show();
    	expiredays.hide();
    } else {
    	expirefrom.hide();
    	expireto.hide();
    	expiredays.show();
    }
});
</script>
</body>
</html>
<?php $this->endPage() ?>


