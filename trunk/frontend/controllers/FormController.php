<?php

namespace frontend\controllers;

use Yii;
use frontend\models\Test;

class FormController extends \yii\web\Controller
{
    
    public function actionIndex()
    {
        $model = new Test();
    
        if ($model->load(Yii::$app->request->post())) {
            if ($model->validate()) {
                // form inputs are valid, do something here
                return;
            }
        }
    
        return $this->render('index', [
                'model' => $model,
        ]);
    }

}

