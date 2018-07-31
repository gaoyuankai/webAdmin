<?php
namespace common\models;

use Yii;

class Createobj
{
    public function __construct($config = []) {
        if (!empty($config)) {
            Yii::configure($this, $config);
        }
    }
}