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
use kartik\export\ExportMenu;

$this->title = '订单列表';
$this->params['breadcrumbs'][] = $this->title;
?>
<div class="row">
    <div class="col-lg-10" style = "height:100px;"> 
        <?php $form = ActiveForm::begin([
                        'id'         => $model->formName(),
                        'action'     => ['/trade/trade/list'],
                        'type'       => ActiveForm::TYPE_VERTICAL,
                        'formConfig' => ['deviceSize' => ActiveForm::SIZE_SMALL],
                        'method'     => 'get',
                ]); ?>
        <div style="position:absolute;">
            <?= $form->field($model, 'User_id')->input('number',['style'=>'width:150px','maxlength' => 20]) ?>
        </div>
        <div style="position:absolute; left:190px;">
            <?= $form->field($model, 'status')->dropDownList(Tool::TRADE_STATUS, ['style'=>'width:150px','prompt' => '']); ?>
        </div>
        <div style="position:absolute; left:360px;">
            <?= $form->field($model, 'createTime', [
                    'addon'=>['prepend'=>['content'=>'<i class="glyphicon glyphicon-calendar"></i>']],
                    'options'=>['class'=>'drp-container form-group','style'=>'width:230px']
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
        </div>
        <div style="position:absolute; left:610px;">
            <?= $form->field($model, 'Activity_id')->input('number',['style'=>'width:150px','maxlength' => 20]) ?>
        </div>
        <div style="position:absolute; left:780px;">
            <?= $form->field($model, 'activityTime', [
                    'addon'=>['prepend'=>['content'=>'<i class="glyphicon glyphicon-calendar"></i>']],
                    'options'=>['class'=>'drp-container form-group','style'=>'width:230px']
                ])->widget(DateRangePicker::classname(), [
                    'useWithAddon'=>true,
                    'convertFormat'=>true,
                    'pluginOptions'=>[
                                        'locale'=>[
                                                    'format'=>'Y-m-d',
                                                    'separator'=>' 到 ',
                                                  ]
                                     ]
                ]);?>
        </div>
        <div style="position:absolute; left:1020px; top: 25px">
            <?= Html::submitButton('查询', ['class' => 'btn btn-primary', 'name' => 'signup-button']) ?>
        </div>
        <div style="position:absolute; left:1085px; top: 25px">
            <?= Html::Button('发消息', ['class' => 'btn btn-primary smessage', 'name' => 'signup-button']) ?>
        </div>
        <?php ActiveForm::end(); ?>
    </div>
    
</div>
<div style="width:1600px">
    <?php
        $gridColumns = [
                            [
                                'class' => 'yii\grid\CheckboxColumn',
                                'checkboxOptions' => function ($model, $key, $index, $column) {
                                    return ['value' => $key];
                                }
                            ],
                            ['label' => '订单流水号','value' =>function($data){
                                                    return isset($data['payment']['tradeNumber'])?$data['payment']['tradeNumber']:"";
                                                }],
                            ['label' => '用户id','value' =>'User_id','options'=>['width'=>"50px"]],
                            ['label' => '订单编码','value' =>'orderNumber','options'=>['width'=>"150px"],],
                            ['label' => '活动ID','value' => 'Activity_id','options'=>['width'=>"50px"]],
                            ['label' => '活动时间','value' =>'activityDate','options'=>['width'=>"100px"]],
                            [
                                'label' => '数量',
                                'value' => function($data){
                                                    return '大人 x'.$data['adultQty']."\n\r".'孩子 x'.$data['kidQty'];
                                                },
                                 'options'=>['width'=>"100px"]
                            ],
                            [
                                'label' => '应付金额',
                                'value' => function($data){
                                                    $str = $data['totalPrice']."元";
                                                    if ($data['couponPrice'] > 0) {
                                                        $str .= "\n\r" . '（含' . $data['couponPrice'] . '元红包）';
                                                    }
                                                    return $str;
                                                },
                                'options'=>['width'=>"150px"]
                            ],
                            [
                                'label' => '实付金额',
                                'value' =>function($data){
                                                   return sprintf("%.2f", $data['totalPrice'] - $data['couponPrice']);
                                              },
                                'options'=>['width'=>"150px"]
                            ],
                            [
                                'label' => '订单状态',
                                'value' =>function($data){
                                                $STATUS = Tool::TRADE_STATUS;
                                                return $STATUS[$data['status']];
                                          },
                                 'options'=>['width'=>"50px"]
                            ],
                            ['label' => '活动场次','value' =>'activityTime','options'=>['width'=>"100px"]],
                            ['label' => '创建时间','value' => 'createTime','options'=>['width'=>"150px"],],
                            ['label' => '申请退款时间','value' => function($data){
                                                if($data['status'] == 5||$data['status'] == 6||$data['status'] == 7){
                                                  return   isset($data['refund']['createTime'])?$data['refund']['createTime']:"";
                                                  }
                                                else {
                                                    return "";
                                                }
                                          },'options'=>['width'=>"100px"],],
                            [
                                'label'=>'更多操作',
                                'format'=>'raw',
                                'value' => function($data, $key){
                                                $button_str = Html::Button('详情', ['class' => 'btn btn-primary detail','name' => 'signup-button', 'key' => $key]);
                                                if ($data['status'] == 5) {
                                                    $button_str .= Html::Button('退款处理', ['class' => 'btn btn-primary refund','name' => 'signup-button', 'key' => $key, 'style' => 'margin-left: 5px']);
                                                } elseif ($data['status'] == 6) {
                                                    $button_str .= Html::Button('退款中', ['class' => 'btn btn-primary refund','name' => 'signup-button', 'key' => $key, 'style' => 'margin-left: 5px']);
                                                    
                                                }elseif ($data['status'] == 7) {
                                                    $button_str .= Html::Button('退款完成', ['class' => 'btn btn-primary refund','name' => 'signup-button', 'key' => $key, 'style' => 'margin-left: 5px']);
                                                    
                                                } elseif($data['insurance']){
                                                    $button_str .=Html::Button('保险信息', ['class' => 'btn btn-primary insurance','name' => 'signup-button', 'key' => $data['orderNumber'], 'style' => 'margin-left: 5px']);
                                                }
                                                return $button_str;
                                           },
                                 'options'=>['width'=>"310px"],
                            ]
                    ]
    ?>
    <?php 
        if (isset(Yii::$app->request->get()['show']) && Yii::$app->request->get()['show']) {
            $title = '页';
            $url   = Url::current(['show' => '']);
        } else {
            $title = '全部';
            $url   = Url::current(['show' => $pagination->totalCount]);
        }
    ?>
    <?= ExportMenu::widget([
            'dataProvider' => $dataProvider,
            'columns' => $gridColumns,
            'target' => ExportMenu::TARGET_BLANK,
            'hiddenColumns'=>[0, 13],
            'noExportColumns'=>[0, 13],
            'fontAwesome' => true,
            'dropdownOptions' => [
                    'label' => '导出',
                    'class' => 'btn btn-default'
            ]
        ]) . 
        '<div class="btn-group">
            <a title="显示所有数据" href="'. $url .'" class="btn btn-default" > '.$title.'</a>
        </div>' . "\n" .
        
        GridView::widget([
            'dataProvider' => $dataProvider,
            'panelHeadingTemplate' => '<div class="pull-right">总共' .$pagination->totalCount.'条数据</div>
                                <h3 class="panel-title">
                                    {heading}
                                </h3>
                                <div class="clearfix"></div>',
            'columns' => $gridColumns,
        ]); 
    ?>
</div>
<?php 
    if ($dataProvider->allModels && !isset(Yii::$app->request->get()['show'])) {
        echo LinkPager::widget([
            'pagination' => $pagination,
            'firstPageLabel'=>"首页",
            'prevPageLabel'=>'上一页',
            'nextPageLabel'=>'下一页',
            'lastPageLabel'=>'末页',
        ]);
    }
?>
<script>
    var trade_data = <?=  $dataProvider->allModels ? json_encode($dataProvider->allModels) : '{}'?>;
</script>
</body>
<?=AppAsset::addCss($this,'@web/css/wdialog.css')?>
<?=AppAsset::addScript($this,'@web/js/lib/wdialog.js')?>
<?=AppAsset::addScript($this,'@web/js/lib/trade/index.js')?>