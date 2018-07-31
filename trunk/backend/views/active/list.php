<?php

use backend\assets\AppAsset;  
use yii\widgets\LinkPager;
use yii\helpers\Html;
use kartik\grid\GridView;



$this->title = '活动列表';
?>
<?php
?>   
    
    <?php
    echo GridView::widget([
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
            'dataProvider' => $dataProvider,
            'columns' => [
                [
                    'class' => 'yii\grid\ActionColumn',
                    'template' => '{view}',
                    /* 'buttons' => [
                            'view' => function ($data) {
                                return Html::a('<span class="glyphicon glyphicon-list"></span>', $data, [
                                        'title' => Yii::t('app', 'Area'),
                                        ]);
                            }
                    ], */
                    'urlCreator' => function ($action, $data) {
                         if ($action === 'view') {
                             return ['view', 'id' => $data['id']];
                        }  
                    },
                    /* 'class' => 'backend\components\ActionColumn',
                    'template' => '{user-view:view} {user-update:update} {user-del:delete} {user-diy-btn:diy}',
                     'buttons' => [
                            // 自定义按钮
                        'diy' => function ($url, $model, $key) {
                            $options = [
                                'title' => Yii::t('yii', 'View'),
                                'aria-label' => Yii::t('yii', 'View'),
                                'data-pjax' => '0',
                            ];
                            return Html::a('<span class="glyphicon glyphicon-refresh"></span>', $url, $options);
                        },
                    ] */
                ],
            
                // 数据提供者中所含数据所定义的简单的列
                // 使用的是模型的列的数据
                 [
                'label' => '活动ID',
                'value' =>'id'
                ],
                [
                'label' => '活动名称',
                'value' => 'name',
                
                ],
                [
                //'label' => '活动类型, 1:其他, 2:自营',
                'label' => '活动类型',
                'value' => 'activityKind',
                ],
                [
                'label' => '活动亮点, 以逗号分隔',
                'value' => 'highlights',
                ],
                [
                'label' => '活动价格',
                'value' => 'totalPrice',
                ],
                [
                'label' => '活动星数',
                'value' => 'stars',
                ],
                [
                'label' => '活动场地名称',
                'value' => 'venueName',
                ],
                ],
            ]);
    
    
echo LinkPager::widget([
        'pagination' => $pagination,
        'firstPageLabel'=>"首页",
        'prevPageLabel'=>'上一页',
        'nextPageLabel'=>'下一页',
        'lastPageLabel'=>'末页',
        ]);
?>