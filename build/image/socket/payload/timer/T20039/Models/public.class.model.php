 <?php


include_once(dirname(__DIR__) .'/Module/Socket.class.php');
require_once dirname(__DIR__) . '/base.class.model.php';

class Public_Model extends Base_Model
{

    /************************
        protected function
    ************************/
    /*
        数组转JSON格式
    */
    protected function _JSON($array)
    {
        $this->__arrayRecursive($array, 'urlencode', true);
        $json = json_encode($array);
        return urldecode($json);
    }
    private function __arrayRecursive(&$array, $function, $apply_to_keys_also = false)
    {
        static $recursive_counter = 0;
        if (++$recursive_counter > 1000) {
            die('possible deep recursion attack');
        }
        foreach ($array as $key => $value) {
            if (is_array($value)) {
                $this->__arrayRecursive($array[$key], $function, $apply_to_keys_also);
            } elseif (is_object($value)) {
                $array[$key] = $value;
            } else {
                $array[$key] = $function($value);
            }

            if ($apply_to_keys_also && is_string($key)) {
                $new_key = $function($key);
                if ($new_key != $key) {
                    $array[$new_key] = $array[$key];
                    unset($array[$key]);
                }
            }
        }
        $recursive_counter--;
    }

    /**
    * 拆解接收的json字符串
    * @param string $splitJsonString json字符串
    */
    protected function _splitJsonString($jsonString)
    {
        if (empty($jsonString)) {
            return OPT_CONST::JSON_FALSE;
        }
        //判断是否为JSON格式
        if (is_null(json_decode($jsonString))) {
            //不是json格式
            return OPT_CONST::JSON_FALSE;
        } else {
            //分拆JSON字符串
            return json_decode($jsonString, true);
        }
    }


    /*
        返回缺参结果
    */
    protected function _missingPrameterArr($operation, $prameter)
    {
        return array('result'=>OPT_CONST::MISSING_PARAMETER,'operation'=>$operation,'data'=>array("missing_parameter"=>$prameter),'result_message'=>"缺少参数");
    }



    /*
        判断数据格式是否正确
    */
    protected function _checkRequestFormat($requestAry)
    {
        if (!isset($requestAry['msgType'])) {
            $this->logMessage('error', "function(checkRequestFormat):lack of msgType"." in file".__FILE__." on Line ".__LINE__);
            return array('result'=>-20,'operation'=>"",'data'=>array("missing_parameter"=>"msgType"),'result_message'=>"缺少参数");
        }
        if (!isset($requestAry['content'])) {
            $this->logMessage('error', "function(checkRequestFormat):lack of content"." in file".__FILE__." on Line ".__LINE__);
            return array('result'=>-20,'operation'=>"",'data'=>array("missing_parameter"=>"content"),'result_message'=>"缺少参数");
        }

        return OPT_CONST::POSTARRAY_TRUE;
    }


    /*
        判断数据格式是否正确
    */
    protected function _checkPostArray($postArr)
    {
        if (!isset($postArr['room_id'])) {
            $this->logMessage('error', "function(checkPostArray):lack of room_id"." in file".__FILE__." on Line ".__LINE__);
            return array('result'=>-20,'operation'=>"",'data'=>array("missing_parameter"=>"room_id"),'result_message'=>"缺少参数");
        }
        if (!isset($postArr['operation'])) {
            $this->logMessage('error', "function(checkPostArray):lack of operation"." in file".__FILE__." on Line ".__LINE__);
            return array('result'=>-20,'operation'=>"",'data'=>array("missing_parameter"=>"operation"),'result_message'=>"缺少参数");
        } else {
            $operation = $postArr['operation'];
        }
        if (!isset($postArr['data'])) {
            $this->logMessage('error', "function(checkPostArray):lack of data"." in file".__FILE__." on Line ".__LINE__);
            return array('result'=>-20,'operation'=>$operation,'data'=>array("missing_parameter"=>"data"),'result_message'=>"缺少参数");
        }

        return OPT_CONST::POSTARRAY_TRUE;
    }
}
?>
