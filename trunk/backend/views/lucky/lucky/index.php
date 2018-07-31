<?php
use yii\helpers\Html;
use kartik\form\ActiveForm;
use kartik\form\ActiveField;
use kartik\grid\GridView;
use backend\assets\AppAsset;
use yii\jui\DatePicker;
use yii\widgets\LinkPager;
use backend\components\Tool;

$this->title = '红包列表';
$this->params['breadcrumbs'][] = $this->title;
?>
<div class="row">
    <div class="col-lg-10">
        <?php $form = ActiveForm::begin([
                        'id'      => $model->formName(),
                        'action'  => ['/lucky/lucky/list'],
                        'fieldConfig' => [
                            'template'    => "<div class='col-xs-2 text-left' style = 'width:120px;'>{label}</div>
                                                    <div class='col-xs-2' style = 'float:left;width:220px;'>{input}</div>",
                        ],/**/
                        'method'  => 'get',
                ]); ?>
        <?= $form->field($model, 'status')->dropDownList($luckyStatus, ['style'=>'width:150px']); ?>
        <?= $form->field($model, 'name')->input('text',['style'=>'width:250px','maxlength' => 20]) ?>
        <div class="form-group">
            <?= Html::submitButton('查询', ['class' => 'btn btn-primary', 'name' => 'signup-button', 'style' => 'margin-left:100px;width:100px']) ?>
        </div>
        
    </div>
    <?php ActiveForm::end(); ?>
    
</div>
<div>
    <?=GridView::widget([
        'toolbar'=> [
            [
                'content'=> Html::Button('新增红包', ['class' => 'btn btn-primary addLucky',
                            'name' => 'signup-button', 'style'=>'width:100px;float: left; margin-right: 20px'])
                            
                 ] ,
        ],
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
        'dataProvider' => $dataProvider,
        'columns' => [
                        ['label' => 'ID','value' =>'id'],
                        ['label' => '抵用卷名称','value' =>'couponName','options'=>['width'=>"160px"]],
                        ['label' => '详细内容','value' =>'brief','options'=>['width'=>"230px"],],
                        [
                            'label' => '类型',
                            'value' =>function($data){
                                           $luckyKind = Tool::LUCKY_KIND;
                                           return $luckyKind[$data['kind']];
                                      },
                            'options'=>['width'=>"75px"]
                              //在搜索条件（过滤条件）中使用下拉框来搜索
                            /* 'filter' => [ '1' => '满额减', '2' => '折扣', '3' => '全场通用'],
                            //or
                            'filter' => Html::activeDropDownList($model,
                                          'kind',[ '1' => '满额减', '2' => '折扣', '3' => '全场通用']
                            ) */
                        ],
                        ['label' => '总数量','value' =>'totalQty','options'=>['width'=>"65px"]],
                        ['label' => '发放数量','value' =>'sendOutQty','options'=>['width'=>"65px"]],
                        ['label' => '红包编码','value' =>'couponCode'],
                        [
                            'label' => '状态',
                            'value' =>function($data){
                                               $luckyStatus = Tool::LUCKY_STATUS;
                                               return $luckyStatus[$data['status']];
                                          }
                        ],
                        [
                            'label' => '金额/折扣',
                            'value' =>function($data){
                                            if ($data['kind'] == 2) {
                                                $discount = $data['discount'] * 10 ;
                                                return $discount.'折';
                                            } else {
                                                return $data['discountPrice'];
                                            }
                                      }
                        ],
                        [
                            'label' => '使用条件',
                            'value' => function($data){
                                           return '>'.$data['conditionPrice'];
                                      }
                                        
                        ],
                        [
                            'label' => '使用期限',
                            'value' => function($data){
                                            if ($data['expireDays'] > 0) {
                                                return '发放后'.$data['expireDays'].'天内有效';
                                            } else {
                                                return date('Y-m-d', strtotime($data['expireFrom'])).
                                                      '   '.date('Y-m-d', strtotime($data['expireTo']));
                                            }
                                       },
                            'options'=>['width'=>"100px"],   //设置样式
                        ],
                        [
                            'label'=>'更多操作',
                            'format'=>'raw',
                            'value' => function($data){
                                            return 
                        Html::Button('编辑', ['class' => 'btn btn-primary update','name' => 'signup-button', 'key' => $data['id']]).
                        Html::Button('删除', ['class' => 'btn btn-primary delete','name' => 'signup-button', 'key' => $data['id'], 'style' => 'margin-left: 5px']);
                                       },
                            'options'=>['width'=>"180px"],   //设置样式
                        ]
                    ],
                ]); ?>
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

<?=AppAsset::addCss($this,'@web/css/wdialog.css')?>
<?=AppAsset::addScript($this,'@web/js/lib/wdialog.js')?>
<?=AppAsset::addScript($this,'@web/js/lib/lucky/index.js')?>