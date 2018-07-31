<?php
use kartik\grid\GridView;
use yii\helpers\Html;
use backend\assets\AppAsset;
?>
<div>
<?=GridView::widget([
                    'dataProvider' => $dataProvider,
                    'columns' => [
                        ['label' => '场次ID','value' =>function($data){
                                               return isset($data['id'])?$data['id']:"";
                        }],
                        ['label' => '成团人数','value' =>'minNumber'],
                        ['label' => '活动库存数','value' =>'stock','options'=>['width'=>"130px"],],
                        ['label' => '活动场次日期','value' =>'activityDate'],
                        ['label' => '活动场次时间','value' =>'activityTime'],
                        ['label' => '报名开始时间','value' =>'regStart'],
                        ['label' => '报名结束时间','value' =>'regEnd'],
                        [
                            'label'=>'更多操作',
                            'format'=>'raw',
                            'value' => function($data, $key){
                                return
                                Html::Button('编辑', ['id'=>'1234', 'class' => 'btn btn-primary update','name' => 'signup-button', 'key' => $key]).
                                Html::Button('删除', ['class' => 'btn btn-primary delete','name' => 'signup-button', 'key' => $key, 'style' => 'margin-left: 5px']);
                            }
                        ]
                    ],
                ]); ?>
<div hidden class= "form-group field-activeform-activitySchedule">
    <div class="col-sm-10">
        <input type= "text" id="activeform-activitySchedule" class="form-control"
            name = "ActiveForm[activitySchedule]" value= '<?= $dataProvider->allModels ? json_encode($dataProvider->allModels) : '';?>'>
    </div>
</div>
<script type="text/javascript">
    var schedule_data = <?= $dataProvider->allModels ? json_encode($dataProvider->allModels) : '{}'; ?>;
</script>
</div>
