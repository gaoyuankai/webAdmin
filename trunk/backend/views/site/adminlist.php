<?php
use yii\helpers\Html;
use kartik\form\ActiveForm;
use kartik\form\ActiveField;
//use yii\grid\GridView;
use backend\assets\AppAsset;
use yii\widgets\LinkPager;
use backend\components\AdminConfig;
use kartik\grid\GridView;

$this->title = '管理员管理';
$this->params['breadcrumbs'][] = $this->title;
?>
<div class="site-signup">
    <div class="row">
        <div class="col-lg-15">
            <?php $form = ActiveForm::begin([
                'method'      =>'get',
                //'options'     => ['class' => 'form-horizontal'],
                'fieldConfig' => [
                'template'    => "<div class='col-xs-2 text-left' style = 'width:120px;'>{label}</div>
                                            <div class='col-xs-2' style = 'float:left'>{input}</div>
                                                    ",]/**/
                ]); ?>

                <?= $form->field($model, 'username')->input('text', ['style'=>'width:150px']);?>
                <?= $form->field($model, 'status')->dropDownList(AdminConfig::ADMIN_STATUS, ['style'=>'width:150px','prompt' => '']); ?>
                <?= $form->field($model, 'role')->dropDownList(AdminConfig::ADMIN_ROLE, ['style'=>'width:150px','prompt' => '']); ?>
                

                <div class="form-group">
                    <?= Html::submitButton('查询', ['class' => 'btn btn-primary', 'name' => 'signup-button']) ?>
                    <?= Html::Button('创建', ['class' => 'btn btn-primary add', 'name' => 'signup-button', 'style'=>'margin-left:20px']) ?>
                </div>
            <?php ActiveForm::end(); ?>
        </div>
    </div>
</div>
<div style= "margin-top:50px">
        <?php
        echo GridView::widget([
                'id' => 'select',
                'dataProvider' => $dataProvider,
                'autoXlFormat'=>true,
                'panel'=>[
                    'type'=>'primary',
                    'heading'=>'管理员列表'
                ],
                'columns' => [
                    ['label' => '管理员ID','value' =>'id'],
                    ['label' => '用户名','value' =>'username',],
                    [
                        'label' => '状态',
                        'value' =>function($data){
                                $STATUS = AdminConfig::ADMIN_STATUS;
                                return $STATUS[$data['status']];
                          },
                    ],
                    [
                        'label' => '权限',
                        'value' =>function($data){
                            $ROLE = AdminConfig::ADMIN_ROLE;
                            return $ROLE[$data['role']];
                        },
                    ],
                    ['label' => '创建日期','value' => 'createTime',],
                    [
                        'label'=>'更多操作',
                        'format'=>'raw',
                        'value' => function($data, $key){
                            $button_str = Html::Button('编辑', ['class' => 'btn btn-primary update',
                                                                'name' => 'signup-button', 'admin_data' => json_encode($data), 'style' => 'margin-left: 10px']);
                            return $button_str;
                            //Html::Button('详情', ['class' => 'btn btn-primary detail','name' => 'signup-button', 'orderNumber' => $data['orderNumber']]).
                            //Html::Button('退款', ['class' => 'btn btn-primary delete','name' => 'signup-button', 'key' => $data['id'], 'style' => 'margin-left: 5px']);
                        }
                    ]
                ]
            ]);
        ?>
<?php 
echo LinkPager::widget([
         'pagination' => $pagination,
         'firstPageLabel'=>"首页",
         'prevPageLabel'=>'上一页',
         'nextPageLabel'=>'下一页',
         'lastPageLabel'=>'末页',
    ]);
?>
</div>
<script>
    var admin_data = <?=  $dataProvider->allModels ? json_encode($dataProvider->allModels) : '{}'?>;
</script>
<?=AppAsset::addCss($this,'@web/css/wdialog.css')?>
<?=AppAsset::addScript($this,'@web/js/lib/wdialog.js')?>
<?=AppAsset::addScript($this,'@web/js/lib/site/adminlist.js')?>