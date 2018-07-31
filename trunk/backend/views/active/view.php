<?php

use yii\helpers\Html;
use yii\widgets\DetailView;
use yii\grid\GridView;
use yii\helpers\ArrayHelper;
use backend\components\Tool;
use backend\assets\AppAsset;
use yii\helpers\Url;
//$this->title = '活动详情';
$this->params['breadcrumbs'][] = $this->title;
?>
<?php $id=$model['id'];?>
<div class="test-view">
    <?= DetailView::widget([
            'model' => $model,
            'attributes' => [
                    [
                    'label'=> '<a href="' . Url::toRoute(['active/edit/update', 'type' => 1 ,'id' => $id]) . '">编辑</a>',
                    'value'=>"",
                    ],
                    ['label' => '活动ID' ,'value' =>$model['id']],
                    ['label' => '活动名称', 'value' =>$model['name'],],
                    ['label' => '活动类型, 1:其他, 2:自营', 'value' => $model['activityKind']==1?"其他":"自营",],
                    ['label' => '是否需要保险', 'value' =>$model['insurance']==1?"需要保险":"不需要保险" ],
                    ['label' => '活动星数', 'value' =>$model['stars'],],
                    ['label' => '活动适应年龄信息', 'value' =>implode(',', $model['ageRange']),],
                    ['label' => '活动能力培养', 'value' =>implode(',', $model['ability']),],
                    ['label' => '活动主办方名字', 'value' =>$model['sponsor'],],
                    ['label' => '活动地点ID', 'value' =>$model['venue']['id'],],
                    ['label' => '活动地点名称', 'value' =>$model['venue']['venueName'],],
                    ['label' => '活动地址', 'value' =>$model['venue']['venueAddr'],],
                    ['label' => '活动场地经度', 'value' =>$model['venue']['longitude'],],
                    ['label' => '活动场地纬度', 'value' =>$model['venue']['latitude'],],
                    ['label' => '活动亮点, 以逗号分隔', 'value' => $model['highlights'],],
                    ['label' => '活动周期描述', 'value' => $model['periodInfo'],],
                    ['label' => '活动简介', 'value' => $model['brief'],],
                    [
                    'label'=>'<a  href="' . Url::toRoute(['active/edit/update', 'type' => 2 ,'id' => $id]) . '">编辑</a>',
                            'value'=>"",
                    
                    ],
                    ['label' => '活动详细介绍， 包括图片','value' => $model['description'],'format' => 'html'],
                    [
                    'label'=>'<a id="link" href="' . Url::toRoute(['active/edit/update', 'type' => 3 ,'id' => $id ,'flag'=>$flag]) . '">编辑</a>',
                            'value'=>"",
                    ],
                    ['label' => '活动创建时间', 'value' =>$model['createTime'],],
                    ['label' => '活动价格类型', 'value' =>$model['priceKind'],],
                    ['label' => '当 priceKind 为 2 时， 成人的单价', 'value' =>$model['adultPrice'],],
                    ['label' => '当 priceKind 为 2 时， 孩子的单价', 'value' =>$model['kidPrice'],],
                    ['label' => '所有类型的总价或唯一价', 'value' =>$model['totalPrice'],],
                    [
                    'label'=>'<a href="' . Url::toRoute(['active/edit/update', 'type' => 4 ,'id' => $id]) . '">编辑</a>',
                            'value'=>"",
                    
                    ],
                    [
                        'attribute'=>'活动场次信息',
                        'format'=>'raw',
                        'value'=>GridView::widget([
                                'dataProvider' => $schedule,
                                'layout'=>'{items}',
                                'columns' => [
                                        ['label' => '活动场次Id','value' =>'id'],
                                        ['label' => '成团人数','value' =>'minNumber'],
                                        ['label' => '活动库存数','value' =>'stock'],
                                        ['label' => '活动场次日期','value' =>'activityDate'],
                                        ['label' => '活动场次时间','value' =>'activityTime'],
                                        ['label' => '报名开始时间','value' =>'regStart'],
                                        ['label' => '报名结束时间','value' =>'regEnd'],
                                ],
              
                        ]),
                    ],
                ],
            'template' => "<tr><th style = 'width: 240px'>{label}</th><td>{value}</td></tr>", 
           ]); 
           
            ?>
</div>

<a href="<?=Url::toRoute(['active/edit/update', 'type' => 5 ,'id' => $id])?>">编辑</a><br><br>
 <span><b>封面图：</b></span><div style="margin-left:65px"><img src="<?php echo array_shift($model['picUrls'])?>"   width=200px height=200px /></div>
    <span><b>活动图片：</b></span>
  <div>
     <div style="margin-left:65px">
     <?php
     if($model['picUrls']){
     foreach ($model['picUrls'] as $picture) {?>
     <img src="<?php echo $picture?>"   width=200px height=200px />
     <?php }}?>
     </div>
  </div>
  <script>
    var flag=<?=$flag?>;
  </script>
<?=AppAsset::addCss($this,'@web/css/wdialog.css')?>
<?=AppAsset::addScript($this,'@web/js/lib/wdialog.js')?>
<?=AppAsset::addScript($this,'@web/js/lib/active/edit/view.js')?>