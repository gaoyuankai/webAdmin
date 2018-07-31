<?php
namespace  backend\models\circle;

use yii\base\Model;
use Yii;
use common\models\GaBaseClient;

class CommentForm extends Model
{
    public $User_id;      //评论者用户ID,
    public $nickName;     //评论者用户昵称,
    public $comment;      //评论内容,
    public $createTime;   //评论时间
    public $circle_name;  //圈子名称
    public $theme_id;     //主题id
    public $Circle_id;    //圈子id
    public $CircleTheme_id;  //圈子id
   
    public function rules()
    {
        return [
            [['User_id','theme_id','createTime'],'safe', 'on' => ['select']],
            [['User_id','theme_id'],'integer','min'=>0,'on' => ['select']],
        ];
    }
    
    public function scenarios()
    {
        return [
         'select' => ['User_id','theme_id'],
        ];
    }
    
    public function attributeLabels()
    {
        return [
            'CircleTheme_id' => '主题ID',
            'User_id'        => '评论者ID',
            'createTime'     => '评论时间',
        ];
    }
}