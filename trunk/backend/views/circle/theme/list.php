<?php
use yii\helpers\Html;
use yii\bootstrap\ActiveForm;
use kartik\grid\GridView;
use backend\assets\AppAsset;
use yii\widgets\LinkPager;
use backend\components\Tool;

$this->title = $circle_name.'/主题列表';
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
               <?=$form->field($model,'User_id')->input('number',['style'=>'width:200px']) ?>
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
            echo GridView::widget([
                'id' => 'select',
                'dataProvider' => $dataProvider,
                    'toolbar' => [],    
            'panel'=>[
                'type'=>'primary',
                'heading'=>"主题列表"
            ],
           'panelHeadingTemplate' => '<div class="pull-right">总共' .$pagination->totalCount.'条数据</div>
                                        <h3 class="panel-title">
                                            {heading}
                                        </h3>
                                        <div class="clearfix"></div>',
           'panelBeforeTemplate' => '<div class="pull-left">
                                        <div class="btn-toolbar kv-grid-toolbar" role="toolbar">
                                            {toolbar}
                                        </div>
                                      </div>
                                      <div class="clearfix"></div>',
                'columns' => [
                    ['label' => '圈子主题ID',  'value' =>'id'],
                    ['label' => '用户ID',     'value' =>'User_id'],
                    ['label' => '主题内容',    'value' =>'content'],
                    ['label' => '所属圈子',    'value' =>'name'],
                    ['label' => '主题评论总数', 'value' =>'commentNumber'],
                    ['label' => '作者',       'value' => function($data){
                       return $data['nickName']?$data['nickName']:"管理员";
                        }],
                    ['label' => '发布时间',    'value' =>'createTime'],
                    ['label' => '主题类型',    'value' => function($data){
                            $ragin = Tool::THEME_TYPE;
                            return $ragin[$data['sysAdmin']];
                        }],
                    ['label' => '是否推荐',    'value' => function($data){
                            $ragin = Tool::THEME_TOP;
                            return $ragin[$data['top']];
                        }],
                    [
                        'label'=>'更多操作',
                        'format'=>'raw',
                        'value' => function($data){
                            return Html::Button('编辑', ['class' => 'btn btn-primary update','name' => 'signup-button', 'key' => $data['id']]).
                            Html::Button('删除', ['class' => 'btn btn-primary delete','name' => 'signup-button', 'value'=>$data['User_id'] , 'key' => $data['id'], 'style' => 'margin-left: 20px']).
                            Html::Button('查看评论', ['class' => 'btn btn-primary comment','name' => 'signup-button', 'key' => $data['id'], 'style' => 'margin-left: 20px;']);
                        }
                    ]
                ],
            ]);
     
        ?>
    </div>
</div>

<?php echo LinkPager::widget([
        'pagination' => $pagination,
        'firstPageLabel'=>"首页",
        'prevPageLabel'=>'上一页',
        'nextPageLabel'=>'下一页',
        'lastPageLabel'=>'末页',
        ]);

?>
<script>
var circle_id   = <?=$circle_id?>;
var circle_name ="<?=$circle_name?>";
</script>
<?=AppAsset::addCss($this,'@web/css/wdialog.css')?>
<?=AppAsset::addScript($this,'@web/js/lib/wdialog.js')?>
<?=AppAsset::addScript($this,'@web/js/lib/circle/theme/list.js')?>
