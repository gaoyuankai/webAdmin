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
use backend\components\AdminConfig;

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
                    'formConfig' => ['labelSpan' => 3, 'deviceSize' => ActiveForm::SIZE_SMALL],
                ]); 
            ?>
            <?= $form->field($model, 'id')->hiddenInput() ?>
            <?= $form->field($model, 'username')->input('text',['style'=>'width:250px','maxlength' => 30, 'nt' => 'username'])  ?>
            <?= $form->field($model, 'role')->dropDownList(AdminConfig::ADMIN_ROLE, ['style'=>'width:120px', 'nt' => 'role'])?>
            <?= $form->field($model, 'status')->dropDownList(AdminConfig::ADMIN_STATUS, ['style'=>'width:120px', 'nt' => 'status'])?>
            <?php 
                if($action == 'add') {
                    echo $form->field($model, 'password')->passwordInput(['style'=>'width:250px', 'nt' => 'password']);
                    echo $form->field($model, 'repassword')->passwordInput(['style'=>'width:250px', 'nt' => 'repassword']);
                }
            ?>
        </div>
</div>
<?php $this->endBody() ?>
</body>
</html>
<?php $this->endPage() ?>