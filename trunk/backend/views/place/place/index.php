<?php
use yii\helpers\Html;
use yii\bootstrap\ActiveForm;
use kartik\grid\GridView;
use backend\assets\AppAsset;
use yii\widgets\LinkPager;
use backend\components\Tool;

$this->title = '场馆管理';
$this->params['breadcrumbs'][] = $this->title;
?>
<div class="site-signup">
    <h1><?php //echo var_dump(json_encode($model)) ?></h1>

    <div class="row">
        <div class="col-lg-15" >
            <?php $form = ActiveForm::begin([
                'method'      =>'get',
                'options'     => ['data-pjax' => true],
                'fieldConfig' => [
                'template'    => "<div class='col-xs-3 col-sm-1'>{label}</div>
                                   <div class='col-xs-7 col-sm-2  text-left' style = 'margin-right:20px'>{input}</div>
                                  ",]/**/
                ]); ?>

                <?= $form->field($model, 'venueName') ?>
                <?= $form->field($model, 'area')->dropDownList(Tool::RAGIN, ['style'=>'width:120px','prompt' => ''])?>

                <div class="form-group">
                    <?php
                        echo Html::submitButton('查询', ['class' => 'btn btn-primary', 'name' => 'signup-button', 'style'=>'width:100px']);
                        echo Html::button('新增', ['class' => 'btn btn-primary add', 'name' => 'signup-button', 'style'=>'width:100px;margin-left:50px']);
                    ?>
                    
                </div>

            <?php ActiveForm::end(); ?>
        </div>
    </div>
    <div>
        <?php
                echo GridView::widget([
                    'id' => 'select',
                    'toolbar' => [],
                    'panel'=>[
                        'type'=>'primary',
                        'heading'=>$this->title
                    ],
                    'panelHeadingTemplate' => '<div class="pull-right">总共' .$pagination->totalCount.'条数据</div>
                            <h3 class="panel-title">
                                {heading}
                            </h3>
                            <div class="clearfix"></div>',
                    'dataProvider' => $dataProvider,
                    'columns' => [
                            ['label' => 'ID','value' =>'id'],
                            ['label' => '活动场所','value' =>'venueName'],
                            ['label' => '活动地点','value' =>'venueAddr',],
                            [
                                'label' => '经纬度',
                                'value' => function($data){
                                                return '['.$data['longitude'].','.$data['latitude'].']';
                                            },
                            ],
                            [
                                'label' => '区域',
                                'value' => function($data){
                                                $ragin = Tool::RAGIN;
                                                return $ragin[$data['Region_districtId']];
                                            },
                            ],
                            [
                                'label'=>'更多操作',
                                'format'=>'raw',
                                'value' => function($data, $key){
                                    //return $button_str;
                                    return Html::Button('编辑', ['class' => 'btn btn-primary update','name' => 'signup-button', 'key' => $key]).
                                            Html::Button('删除', ['class' => 'btn btn-primary delete','name' => 'signup-button', 'key' => $data['id'], 'style' => 'margin-left: 40px']);
                                }
                            ]
                    ],
                ]);
        ?>
    </div>
</div>
<?php 
     echo LinkPager::widget([
         'pagination' => $pagination,
         'firstPageLabel'=>"首页",
         'prevPageLabel'=>'上一页',
         'nextPageLabel'=>'下一页',
         'lastPageLabel'=>'末页',
     ]);
?>
<script>
    var place_data = <?=  $dataProvider->allModels ? json_encode($dataProvider->allModels) : '{}'?>;
</script>


<?=AppAsset::addCss($this,'@web/css/wdialog.css')?>
<?=AppAsset::addScript($this,'@web/js/lib/wdialog.js')?>
<?=AppAsset::addScript($this,'@web/js/lib/place/index.js')?>