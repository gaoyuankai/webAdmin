<?php

class vote
{
    public $dbName = 'yii2advanced';
    
    public function index() 
    {
        $db = $this->getDb();
        $rs = $db->query("select * from vote");
        $result_arr = $rs->fetchAll();
        var_dump($result_arr);
    }
    
    public function detail()
    {
        $id    = $_GET['id'];
        $order = $_GET['order'];
        $db = $this->getDb();
        $rs = $db->query("select * from voteOption order by $order");
        $result_arr = $rs->fetchAll();
        var_dump($result_arr);
    }
    
    public function addVote()
    {
        $title = $_REQUEST['title'];
        $db = $this->getDb();
        
        if ($db->exec("insert into vote(title) values($title)")) {
            echo '成功';
        }
        echo '失败';
    }
    
    public function updateVote()
    {
        $title = $_REQUEST['title'];
        $db = $this->getDb();
        if ($db->exec("UPDATE vote SET title = $title")) {
            echo '成功';
        }
        echo '失败';
    }
    
    public function addVoteOption()
    {
        $voteId = $_REQUEST['voteId'];
        $title  = $_REQUEST['title'];
    
        $db = $this->getDb();
        if ($db->exec("insert into voteOption(voteId,title) values($voteId, $title)")) {
            echo '成功';
        }
        echo '失败';
    }
    
    public function vote()
    {
        $id = $_REQUEST['id'];
        $db = $this->getDb();
        if ($db->exec("UPDATE voteOption SET num = num+1 where id = $id")) {
            echo '成功';
        }
        echo '失败';
    }
    
    public function getDb()
    {
        return $db = new PDO('mysql:host=127.0.0.1;dbname='.$this->dbName, 'root', '');
    }
    
    
    
    
    
    
    
    
    
}