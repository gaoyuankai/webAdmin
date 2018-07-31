<?php
use yii\helpers\Html;
use yii\bootstrap\ActiveForm;
use kartik\grid\GridView;
use backend\assets\AppAsset;
use yii\widgets\LinkPager;
use backend\components\Tool;

$this->title = '配置列表';
$this->params['breadcrumbs'][] = $this->title;
?>
<div class="site-signup">
    <div class="row">
        <div class="col-lg-15" >
            <?php $form = ActiveForm::begin([
                        'method'      =>'get',
                        'options'     => ['data-pjax' => true],
                        'fieldConfig' => [ 'template' => 
                                "<div class='col-xs-3 col-sm-1'>{label}</div><div class='col-xs-7 col-sm-2  text-left' style = 'margin-right:20px'>{input}</div>"
                        ]
                ]); ?>

                <?= $form->field($model, 'type')->dropDownList(Tool::BANNER_TYPE, ['inline'=>true,'style'=>'width:180px','prompt' => ''])?>
                <div class="form-group">
                    <?php
                        echo Html::submitButton('查询', ['class' => 'btn btn-primary', 'name' => 'signup-button', 'style'=>'margin-left:500px;width:100px']);
                        echo Html::button('新增', ['class' => 'btn btn-primary add', 'name' => 'signup-button', 'style'=>'width:100px;margin-left:50px']);
                    ?>
                </div>
            <?php ActiveForm::end(); ?>
        </div>
    </div>
     
    <div>
        <?php
        if($type==1){
            echo GridView::widget([
                'id' => 'select',
                'dataProvider' => $dataProvider,
                'columns' => [
                    ['label' => 'ID','value' =>'id'],
                    ['label' => '类型','value' =>'是否启用',
                        'value' => function($data){
                            $ragin = Tool::BANNER_TYPE;
                            return $ragin[$data['type']];}
                    ],
                    ['label' => '标题','value' =>'items.title'],
                    ['label' => '链接','value' =>'items.url'],
                    [
                        'label' => '是否启用',
                        'value' => function($data){
                            $ragin = Tool::BANNER_STATUS;
                            return $ragin[$data['status']];
                        },
                    ],
                    ['label' => '序号','value' =>'sort'],
                    [
                        'label'=>'更多操作',
                        'format'=>'raw',
                        'value' => function($data) {
                            return Html::Button('编辑', ['class' => 'btn btn-primary update','name' => 'signup-button', 'key' => $data['id']]).
                            Html::Button('删除', ['class' => 'btn btn-primary delete','name' => 'signup-button', 'key' => $data['id'], 'style' => 'margin-left: 40px']);
                        }
                    ]
                ],
            ]);
        }
         
        //手机轮播图
        if($type==2){
            echo GridView::widget([
                'id' => 'select',
                'dataProvider' => $dataProvider,
                'columns' => [
                    ['label' => 'ID','value' =>'id'],
                    ['label' => '类型','value' =>'是否启用',
                        'value' => function($data){
                            $ragin = Tool::BANNER_TYPE;
                            return $ragin[$data['type']];}
                    ],
                    [
                        'label' => '关联类型',
                        'value' => function($data){
                            $ragin = Tool::BANNER_ATYPE;
                            return $ragin[$data['items']['kind']];
                        }
                    ],
                    ['label' => '关联id','value' =>'items.id'],
                    [
                        'label' => '是否启用',
                        'value' => function($data){
                             $ragin = Tool::BANNER_STATUS;
                             return $ragin[$data['status']];
                        }
                     ],
                     ['label' => '序号','value' =>'sort',],
                     [
                        'label'=>'更多操作',
                        'format'=>'raw',
                        'value' => function($data){
                            return Html::Button('编辑', ['class' => 'btn btn-primary update','name' => 'signup-button', 'key' => $data['id']]).
                            Html::Button('删除', ['class' => 'btn btn-primary delete','name' => 'signup-button', 'key' => $data['id'], 'style' => 'margin-left: 40px']);
                        }
                    ]
                ],
            ]);
        }
         
        //首页活动
        if($type==3){
            echo GridView::widget([
                'id' => 'select',
                'dataProvider' => $dataProvider,
                'columns' => [
                   ['label' => 'ID','value' =>'id'],
                    ['label' => '类型','value' =>'是否启用',
                        'value' => function($data){
                            $ragin = Tool::BANNER_TYPE;
                            return $ragin[$data['type']];}
                    ],
                    ['label' => '关联活动id','value' =>'items.id'],
                    [
                        'label' => '是否启用',
                        'value' => function($data){
                            $ragin = Tool::BANNER_STATUS;
                            return $ragin[$data['status']];
                        }
                     ],
                     ['label' => '序号','value' =>'sort',],
                     [
                        'label'=>'更多操作',
                        'format'=>'raw',
                        'value' => function($data){
                            return Html::Button('编辑', ['class' => 'btn btn-primary update','name' => 'signup-button', 'key' => $data['id']]).
                            Html::Button('删除', ['class' => 'btn btn-primary delete','name' => 'signup-button', 'key' => $data['id'], 'style' => 'margin-left: 40px']);
                        }
                    ]
                ],
            ]);
        }
         
        //首页活动圈子
        if($type==4){
            echo GridView::widget([
                'id' => 'select',
                'dataProvider' => $dataProvider,
                'columns' => [
                    ['label' => 'ID','value' =>'id'],
                    ['label' => '类型','value' =>'是否启用',
                    'value' => function($data){
                        $ragin = Tool::BANNER_TYPE;
                        return $ragin[$data['type']];}
                    ],
                    ['label' => '关联圈子主题id','value' =>'items.id'],
                    [
                        'label' => '是否启用',
                        'value' => function($data){
                            $ragin = Tool::BANNER_STATUS;
                            return $ragin[$data['status']];
                        }
                     ],
                     ['label' => '序号','value' =>'sort',],
                     [
                        'label'=>'更多操作',
                        'format'=>'raw',
                        'value' => function($data){
                            return Html::Button('编辑', ['class' => 'btn btn-primary update','name' => 'signup-button', 'key' => $data['id']]).
                            Html::Button('删除', ['class' => 'btn btn-primary delete','name' => 'signup-button', 'key' => $data['id'], 'style' => 'margin-left: 40px']);
                        }
                    ]
                ],
            ]);
        }
        ?>
    </div>
</div>
<script type="text/javascript">
     var type=<?=$type?>;  
</script>


<?=AppAsset::addCss($this,'@web/css/wdialog.css')?>
<?=AppAsset::addScript($this,'@web/js/lib/wdialog.js')?>
<?=AppAsset::addScript($this,'@web/js/lib/banner/edit/list.js')?>
