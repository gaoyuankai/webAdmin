<?php
use yii\helpers\Html;
use yii\bootstrap\ActiveForm;

$this->title = 'Lucky_message';
$this->params['breadcrumbs'][] = $this->title;
?>
<h6><?php // var_dump($model);?></h6>
<div class="lucky-message">
    <div class="row">
        <div class="col-lg-10">
            <?php $form = ActiveForm::begin(['id' => 'lucky-form']); ?>
                <?= $form->field($model, 'usernames') ?>
                <?= $form->field($model, 'lucky')->dropDownList($lucky); ?>
                <?= $form->field($model, 'style')->checkboxList(['0'=>'短信','1'=>'站内信']) ?>
                <?= $form->field($model, 'title')->input("text",['maxlength' => 30])?>
                <?= $form->field($model, 'message')->textarea(['rows'=>5,'maxlength' => 400]) ?>
                <div class="form-group">
                    <!-- <?//echo Html::submitButton('发送', ['class' => 'btn btn-primary', 'name' => 'login-button']) ?> -->
                </div>
            <?php ActiveForm::end(); ?>
        </div>
    </div>
</div>
<script>
    var user_data = <?=  json_encode($model->data)?>;
    var lucky_config = <?=json_encode($model->lucky_config)?>;
</script>