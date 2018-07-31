<?php

use yii\helpers\Html;
use yii\grid\GridView;
use yii\widgets\Pjax;

/* @var $this yii\web\View */
/* @var $searchModel app\models\CountriesSearch */
/* @var $dataProvider yii\data\ActiveDataProvider */

$this->title = 'pjax';
$this->params['breadcrumbs'][] = $this->title;
?>
<div class="countries-index">
 
    <h1><?= Html::encode($this->title) ?></h1>
 
    <p>
        <?= Html::a(Yii::t('app', 'Create {modelClass}', [
    'modelClass' => 'UserForm',
]), ['create'], ['class' => 'btn btn-success']) ?>
    </p>

<!-- Render create form -->    
    <?= $this->render('_form', [
        'model' => $model,
    	//'area'=>$area,
    ]) ?>
<div style= "margin-top:300px">
	<?php Pjax::begin(['id' => 'user']) ?>
	    <?= GridView::widget([
	        'dataProvider' => $dataProvider,
	        //'filterModel' => $searchModel,
	        'columns' => [
	            ['class' => 'yii\grid\SerialColumn'],
	            'id',
	            'name',
	            ['class' => 'yii\grid\ActionColumn'],
	        ],
	    ]); ?>
	<?php Pjax::end() ?>
</div>
</div>