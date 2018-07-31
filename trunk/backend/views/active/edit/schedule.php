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
use kartik\date\DatePicker;
use kartik\datetime\DateTimePicker;
$this->title = 'select';
$this->params['breadcrumbs'][] = $this->title;
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
<div class="site-signup">
    <div class="row">
        <div class="col-lg-15" >
            <?php $form = ActiveForm::begin([
                    'id'         => $model->formName(),
                    'type' => ActiveForm::TYPE_HORIZONTAL,
                    'formConfig' => ['labelSpan' => 2, 'deviceSize' => ActiveForm::SIZE_SMALL]
                ]); ?>
                <?= $form->field($model, 'minNumber')->input('number',['style'=>'width:200px']) ?>
                <?= $form->field($model, 'stock')->input('number',['style'=>'width:200px', 'placeholder'=>'人数以小孩人数为准'])?>
                <?php //echo $form->field($model, 'activityDate')->input('date',['style'=>'width:250px'])?>
                <?= $form->field($model, 'activityDate')
                            ->widget(DateTimePicker::classname(), 
                            [
                                //'type'=>DatePicker::TYPE_COMPONENT_APPEND,
                                'value' => date('Y-m-d h:i:s'),
                                'pluginOptions' => [
                                    'autoclose'=>true,
                                    'format' => 'yyyy-mm-dd hh:ii:00',
                                    'todayHighlight' => true,
                                    'todayBtn' => true,
                                ]
                            ]);?>
                <?= $form->field($model, 'regStart')
                            ->widget(DatePicker::classname(), 
                            [
                                //'type'=>DatePicker::TYPE_COMPONENT_APPEND,
                                'value' => date('Y-m-d'),
                                'pluginOptions' => [
                                    'autoclose'=>true,
                                    'format' => 'yyyy-mm-dd 00:00:00',
                                    'todayHighlight' => true,
                                    'todayBtn' => true,
                                ]
                            ])->hint('<b>开始时间为当天凌晨</b>');?>
              <?= $form->field($model, 'regEnd')
                            ->widget(DatePicker::classname(),
                                            [
                                            //'type'=>DatePicker::TYPE_COMPONENT_APPEND,
                                            'value' => date('Y-m-d'),
                                            'pluginOptions' => [
                                            'autoclose'=>true,
                                            'format' => 'yyyy-mm-dd 00:00:00',
                                            'todayHighlight' => true,
                                            'todayBtn' => true,
                                            ]
                            ])->hint('<b>结束时间为前一天的24点</br>如果为2015-12-28 23:59:59,请填写2015-12-29</b>');?>
                <?php //= $form->field($model, 'activityTime')->input('time',['style'=>'width:250px'])?>
                <?php //= $form->field($model, 'regStart')->input('date',['style'=>'width:250px'])->hint('<b>开始时间为当天凌晨</b>')?>
                <?php //= $form->field($model, 'regEnd')->input('date',['style'=>'width:250px'])->hint('<b>结束时间为前一天的24点</br>如果为2015-12-28 23:59:59,请填写2015-12-29</b>')?>
           <?php ActiveForm::end(); ?>
        </div>
    </div>
</div>
<?php $this->endBody() ?>
</body>
</html>
<?php $this->endPage() ?>
<script type="text/javascript">
document.getElementById("scheduleform-activitydate").style.width = '170px';
document.getElementById("scheduleform-regstart").style.width = '170px'; 
document.getElementById("scheduleform-regend").style.width = '170px'; 

var schedule_key = <?= $key; ?>;
</script>
