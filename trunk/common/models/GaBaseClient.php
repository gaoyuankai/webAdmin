<?php
namespace common\models;

use Yii;

class GaBaseClient
{
    /**
     * apiHost Host Address
     * Corresponding to the IP address of the API Server in the HOST file
     */
    private $apiHost   = '';
 
    /**
     * clientID
     * Every platform has their own clientID
     * For the development environment, default value is 'www.yiban.cn'
     */
    private $clientID  = '';
 
    /**
     * clientKey
     * Corresponding to the clientID
     */
    private $clientKey = '';
 
    /**
     * Files
     */
    private $files = array();
 
    /**
     * clientObj
     */
    private static $clientObj;
 
    /**
     * curlObj
     */
    private static $ch;
 
    /**
     * setTimeour
     */
    public static $timeOut = 20;
 
    const GET_METHOD  = 'GET';
    const POST_METHOD = 'POST';
    const PUT_METHOD  = 'PUT';
    const DEL_METHOD  = 'DELETE';
 
    /**
     * singleton
     */
    public static function getInstance()
    {
        if (empty(self::$clientObj)) {
            self::$clientObj = new self;
        }
 
        return self::$clientObj;
    }
 
    /**
     * magic function to get API
     */
    public function __call($func, $args)
    {
        $data  = array (
                     'clientID' => $this->clientID,
                     'passwd'   => $this->clientKey,
                     'func'     => $func,
                     'args'     => json_encode($args)
                 ) + $this->files;
 
        $result = $this->post($data, !$this->files);
 
        $resultArray = json_decode($result, true);
 
        if (empty($resultArray)) {
            //you may add log here to the data before json_decode, which may help
            //Log($result, 'filePath.log');
            return array('status' => false, 'data' => 1007, 'message' => '数据获取有误, 请查看日志');
        }
 
        return $resultArray;
    }
 
    /**
     * @param $args
     */
    public function _addFiles($files = array())
    {
        $this->files = is_array($files) ? $files : array($files);
 
        array_walk_recursive($this->files, function (&$item, $key) {
            $item = ((version_compare(PHP_VERSION, '5.5.0') >= 0) && class_exists('CurlFile'))
                        ? new CurlFile($item) : '@' . $item;
        });
        return $this->files;
    }
 
    /**
     * @param  $module  module name
     */
    private function __construct()
    {
        //add your own config file here
        $config = Yii::$app->params['apiConfig'];
        
        $this->apiHost   = $config['host'];
        $this->clientID  = $config['clientID'];
        $this->clientKey = $config['clientKey'];
    }
 
    /**
     * curl PUT
     * @param string $url
     * @param array $params
     */
    protected function put($params, $httpBuild = true)
    {
        if ($httpBuild) {
            $params = http_build_query($params);
        }
        return self::curlRequest($this->apiHost, $params, self::PUT_METHOD);
    }
 
    /**
     * curl POST
     * @param sring $url
     * @param array $params
     */
    protected function post($params, $httpBuild = true)
    {
        if ($httpBuild) {
            $params = http_build_query($params);
        }
        return self::curlRequest($this->apiHost, $params, self::POST_METHOD);
    }
 
    /**
     * curl GET
     * @param sring $url
     */
    protected function get()
    {
        return self::curlRequest($this->apiHost, '', self::GET_METHOD);
    }
 
    /**
     * curl http request
     * @param  array $data
     * @return mixed
     */
    private static function curlRequest($url = '', $params = '', $type = self::GET_METHOD)
    {
        if (empty(self::$ch)) {
            self::$ch = curl_init();
        }
 
        curl_setopt (self::$ch, CURLOPT_URL, $url);
        curl_setopt (self::$ch, CURLOPT_HEADER, 0);
        curl_setopt (self::$ch, CURLOPT_RETURNTRANSFER, 1);
        curl_setopt (self::$ch, CURLOPT_FORBID_REUSE, 1);
        curl_setopt (self::$ch, CURLOPT_TIMEOUT, self::$timeOut);
 
        switch ($type) {
            case "GET"    : curl_setopt(self::$ch, CURLOPT_HTTPGET, true);
                            break;
            case "POST"   : curl_setopt(self::$ch, CURLOPT_POST,true);
                            curl_setopt(self::$ch, CURLOPT_POSTFIELDS, $params);
                            break;
            case "PUT"    : curl_setopt (self::$ch, CURLOPT_CUSTOMREQUEST, self::PUT_METHOD);
                            curl_setopt (self::$ch, CURLOPT_POSTFIELDS, $params);
                            break;
            case "DELETE" : curl_setopt (self::$ch, CURLOPT_CUSTOMREQUEST, self::DEL_METHOD);
                            curl_setopt (self::$ch, CURLOPT_POSTFIELDS, $params);
                            break;
        }
        $result = curl_exec(self::$ch);
 
        if (empty($result)) {
            return json_encode(array('status' => false, 'data' => 1006, 'message' => '连接到基础API失败'));
        }
 
        return $result;
    }
}
