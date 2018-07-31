<?php
use yii\helpers\Html;

use backend\assets\AppAsset;

use kartik\form\ActiveForm;
use kartik\form\ActiveField;
use kartik\daterange\DateRangePicker;
use yii\widgets\LinkPager;
use backend\components\Tool;
use kartik\grid\GridView;

use kartik\detail\DetailView;



$this->title = '主题评论列表';
$this->params['breadcrumbs'][] = $this->title;

?>
<div class="site-signup">
    <div class="row">
        <div class="col-lg-15" style = "height:100px;" >
               <?php $form = ActiveForm::begin([
                       'action'     => ['/circle/comment/list'],
                       'options'     => ['data-pjax' => true],
                        'method'     => 'post',
                ]); ?>
                <div style="position:absolute;margin-left:20px">
                <?=$form->field($model,'CircleTheme_id')->input('number',['style'=>'width:100px'])?>
                </div>
                <div style="position:absolute;margin-left:140px">
                <?=$form->field($model,'User_id')->input('number',['style'=>'width:180px'])?>
                </div>
               
                <?= $form->field($model, 'createTime', [
                        'addon'=>['prepend'=>['content'=>'<i class="glyphicon glyphicon-calendar"></i>']],
                        'options'=>['class'=>'drp-container form-group','style'=>'margin-left:380px;width:400px']
                    ])->widget(DateRangePicker::classname(), [
                        'useWithAddon'=>true,
                        'convertFormat'=>true,
                        'pluginOptions'=>[
                                            'timePicker'=>true,
                                            'timePickerIncrement'=>10,
                                            'locale'=>[
                                                        'format'=>'Y-m-d H:m:s',
                                                        'separator'=>' 到 ',
                                                      ],
                                         ]
                    ]);?>
                <div  style="margin-left:1040px;margin-top:-50px;">
                    <?php
                        echo Html::submitButton('查询', ['class' => 'btn btn-primary', 'name' => 'signup-button','style'=>'width:100px;']);
                    ?>
                </div>
                
            <?php ActiveForm::end(); ?>
        </div>
      
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
                'heading'=>$this->title
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
                    ['label' => '用户ID','value' =>'User_id'],
                    ['label' => '评论内容','value' =>'comment'],
                    ['label' => '评论时间','value' =>'createTime'],
                    ['label' => '圈子名称','value' =>'Circle_id'],
                    [
                        'label'=>'更多操作',
                        'format'=>'raw',
                        'value' => function($data){
                            return Html::Button('删除', ['class' => 'btn btn-primary delete','name' => 'signup-button', 'value'=>$data['User_id'],'key' => $data['id'], 'style' => 'margin-left: 40px']);
                            
                        }
                    ]
                ],
            ]);
     
        ?>
    </div>


<?php echo LinkPager::widget([
        'pagination' => $pagination,
        'firstPageLabel'=>"首页",
        'prevPageLabel'=>'上一页',
        'nextPageLabel'=>'下一页',
        'lastPageLabel'=>'末页',
        ]);
?>

<?=AppAsset::addCss($this,'@web/css/wdialog.css')?>
<?=AppAsset::addScript($this,'@web/js/lib/wdialog.js')?>
<?=AppAsset::addScript($this,'@web/js/lib/circle/comment/list.js')?>
