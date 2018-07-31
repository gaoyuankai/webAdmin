<?php
use yii\helpers\Html;
use kartik\form\ActiveForm;
use kartik\select2\Select2;
use backend\assets\AppAsset;

$this->title = 'Lucky_message';
$this->params['breadcrumbs'][] = $this->title;
AppAsset::register($this);
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
<div class="user-message">
    <div class="row">
        <div class="col-lg-10">
  <?php $form = ActiveForm::begin(['id' => 'message-form']); ?>
                <?= $form->field($model, 'usernames')->hint('all为所有用户，为all时只能发送站内信。用户列表只能在选中的用户中编辑')?>
                
                <?= $form->field($model, 'title')->input("text",['maxlength' => 30]) ?>
                <?= $form->field($model, 'message')->textarea(['rows'=>5,'maxlength' => 200]) ?>
                <?= $form->field($model, 'style')->checkboxList(
                                $model->usernames == 'all' ? ['1'=>'站内信'] : ['0'=>'短信','1'=>'站内信']
                                ); ?>
                <div class="form-group">
                    <!-- <?//echo Html::submitButton('发送', ['class' => 'btn btn-primary', 'name' => 'login-button']) ?> -->
                </div>

            <?php ActiveForm::end(); ?>
        </div>
    </div>
</div>
<?php $this->endBody() ?>
</body>
<script>
    var user_data = <?=  json_encode($model->data)?>;
</script>
</html>
<?php $this->endPage() ?>
<?=AppAsset::addScript($this,'@web/dist/css/select2.min.css')?>
<?=AppAsset::addScript($this,'@web/dist/js/select2.min.js')?>