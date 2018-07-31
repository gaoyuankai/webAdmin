<?php
use yii\helpers\Html;
//use yii\bootstrap\ActiveForm
use backend\assets\AppAsset;
use kartik\detail\DetailView;
use kartik\form\ActiveForm;

$this->title = '编辑主题';
$this->params['breadcrumbs'][] = $this->title;
?>
<div class="row">
    <div class="col-lg-10" >
    <div>
    <?php echo DetailView::widget([
                    'model' => $model,
                    'mode'  => DetailView::MODE_VIEW,
                    'attributes'=>[
                                    //'name',
                                    [
                                        'columns' => [
                                           
                                            [
                                                'attribute'=>'content',
                                                'value' => $model->content,
                                                'labelColOptions'=>['style'=>'width:70px'],
                                                'valueColOptions'=>['style'=>'width:180px']
                                            ],
                                            
                                            
                                        ],
                                        
                                    ],
                                    [
                                    'columns' => [
                                                    [
                                                            'attribute'=>'sysAdmin',
                                                            'value' => $model->sysAdmin=="1"?"官方主题":"普通主题",
                                                            'labelColOptions'=>['style'=>'width:70px'],
                                                            'valueColOptions'=>['style'=>'width:180px']
                                                      ],
                                    
                                       ],
                                     
                                    ],
                            ]    
                                  
                ]);
    ?>
    </div>
    <?php if($model->themePictures){?> 
    <span><b>主题图片：</b></span>
  <div>
     <div style="margin-left:65px">
     <?php 
     foreach ($model->themePictures as $picture) {?>
     <img src="<?php echo $picture?>"   width=200px height=200px />
   <?php }}?>
   </div>
       </div>
            <div style="width:800px;border:solid 1px 000000;margin-left:20px">
               <b>是否置顶：</b><input type='radio' name="top" <?php echo $model->top=="1"?'checked':""  ?> value="1"/>推荐
               <input type='radio' name="top" <?php echo $model->top=="0"?'checked':""  ?>  value="0" />不推荐
            </div>
       </div>
   </div>
<script>
var oTable=document.getElementsByClassName('kv-child-table');
oTable.item(0).setAttribute("width", "100%"); 
</script>


