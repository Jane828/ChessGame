<?php


use \GatewayWorker\Lib\Gateway;

include_once(dirname(__DIR__) . '/Module/Verification.class.php');
include_once(dirname(__DIR__) . '/Module/Socket.class.php');
include_once(dirname(__DIR__) . '/Module/Redis.class.php');
require_once dirname(__DIR__) . '/base.class.model.php';

class Public_Model extends Base_Model {

    protected function setHashTransaction($room_id) {
        $Redis_Model = Redis_Model::getModelObject();
        $replyArr    = array("[roomid]" => $room_id);
        $key         = strtr(Redis_Const::Room_Key, $replyArr);

        $redisAuth = $Redis_Model->pingRedisAuth();
        if ($redisAuth === G_CONST::EMPTY_STRING || !is_object($redisAuth)) {
            $this->logMessage('error', "function(existsKey):redisAuth is empty string" . " in file" . __FILE__ . " on Line " . __LINE__);
            return FALSE;
        }

        $success = FALSE;
        $options = array(
            'cas'   => TRUE,    // Initialize with support for CAS operations
            'watch' => $key,    // Key that needs to be WATCHed to detect changes
            'retry' => 3,       // Number of retries on aborted transactions, after
            // which the client bails out with an exception.
        );

        $redisAuth->transaction($options, function ($tx) use ($key, &$success) {
            $room_status = $tx->hget($key, Redis_Const::Room_Field_Status);
            if (isset($room_status) && $room_status == 1) {
                $tx->multi();   // With CAS, MULTI *must* be explicitly invoked.
                $tx->hmset($key, array(Redis_Const::Room_Field_Status => 2));
                $success = TRUE;

            } else {
                $this->writeLog("room_status != 1");
                $success = FALSE;
            }
        });
        return $success;
    }

    //推消息
    protected function pushMessageToGroup($room_id, $msg_arr, $exclude_client_id = NULL) {
        $msg = $this->_JSON($msg_arr);
        Gateway::sendToGroup($room_id, $msg, $exclude_client_id);
    }

    protected function pushMessageToAccount($account_id, $msg_arr) {
        $msg          = $this->_JSON($msg_arr);
        Gateway::sendToUid($account_id, $msg);
    }

    protected function pushMessageToCurrentClient($msg_arr) {
        $msg = $this->_JSON($msg_arr);
        Gateway::sendToCurrentClient($msg);
    }

    /**
     * 数组转JSON格式
     */
    protected function _JSON($array) {
        $this->__arrayRecursive($array, 'urlencode', TRUE);
        $json = json_encode($array);
        return urldecode($json);
    }

    private function __arrayRecursive(&$array, $function, $apply_to_keys_also = FALSE) {
        static $recursive_counter = 0;
        if (++$recursive_counter > 1000) {
            $this->logMessage('error', "function(_JSON):recursive_counter>1000" . " in file" . __FILE__ . " on Line " . __LINE__);
            return;
            die('possible deep recursion attack');
        }
        foreach ($array as $key => $value) {
            if (is_array($value)) {
                $this->__arrayRecursive($array[$key], $function, $apply_to_keys_also);
            } else if (is_object($value)) {
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
    protected function _splitJsonString($jsonString) {
        if (empty($jsonString)) {
            return OPT_CONST::JSON_FALSE;
        }
        //判断是否为JSON格式
        if (is_null(json_decode($jsonString))) {
            //不是json格式
            return OPT_CONST::JSON_FALSE;
        } else {
            //分拆JSON字符串
            return json_decode($jsonString, TRUE);
        }
    }


    /**
     * 返回缺参结果
     */
    protected function _missingPrameterArr($operation, $prameter) {
        return array('result' => OPT_CONST::MISSING_PARAMETER, 'operation' => $operation, 'data' => array("missing_parameter" => $prameter), 'result_message' => "缺少参数");
    }

    /**
     * 返回非法参数结果
     */
    protected function _invalidPrameterArr($operation, $prameter) {
        return array('result' => OPT_CONST::MISSING_PARAMETER, 'operation' => $operation, 'data' => array("invalid_parameter" => $prameter), 'result_message' => "非法参数");
    }


    /**
     * 判断数据格式是否正确
     */
    protected function _checkRequestFormat($requestAry) {
        if (!isset($requestAry['msgType'])) {
            $this->logMessage('error', "function(checkRequestFormat):lack of msgType" . " in file" . __FILE__ . " on Line " . __LINE__);
            return array('result' => -20, 'operation' => "", 'data' => array("missing_parameter" => "msgType"), 'result_message' => "缺少参数");
        }
        if (!isset($requestAry['content'])) {
            $this->logMessage('error', "function(checkRequestFormat):lack of content" . " in file" . __FILE__ . " on Line " . __LINE__);
            return array('result' => -20, 'operation' => "", 'data' => array("missing_parameter" => "content"), 'result_message' => "缺少参数");
        }

        return OPT_CONST::POSTARRAY_TRUE;
    }


    /**
     * 判断数据格式是否正确
     */
    protected function _checkPostArray($postArr) {

        if (!isset($postArr['operation'])) {
            $this->logMessage('error', "function(checkPostArray):lack of operation" . " in file" . __FILE__ . " on Line " . __LINE__);
            return array('result' => -20, 'operation' => "", 'data' => array(), 'result_message' => "缺少参数");
        } else $operation = $postArr['operation'];
        if (!isset($postArr['data'])) {
            $this->logMessage('error', "function(checkPostArray):lack of data" . " in file" . __FILE__ . " on Line " . __LINE__);
            return array('result' => -20, 'operation' => $operation, 'data' => array(), 'result_message' => "缺少参数");
        }
        if (!isset($postArr['account_id'])) {
            $this->logMessage('error', "function(checkPostArray):lack of account_id" . " in file" . __FILE__ . " on Line " . __LINE__);
            return array('result' => -20, 'operation' => $operation, 'data' => array(), 'result_message' => "缺少参数");
        }
        if (!isset($postArr['session'])) {
            $this->logMessage('error', "function(checkPostArray):lack of session" . " in file" . __FILE__ . " on Line " . __LINE__);
            return array('result' => -20, 'operation' => $operation, 'data' => array(), 'result_message' => "缺少参数");
        }
        return OPT_CONST::POSTARRAY_TRUE;
    }


    /**
     * 生成微秒
     */
    protected function getMicroTimestamp() {
        $mtime      = explode(' ', microtime());
        $mTimestamp = $mtime[1] . substr($mtime[0], 2, 3);

        return $mTimestamp;
    }

    /**
     * 判断请求链接合法性
     */
    protected function checkRequestClientLegal($client_id, $room_id, $account_id) {
        return TRUE;
        //绑定用户UID
        $replyArr = array("[roomid]" => $room_id, "[accountid]" => $account_id);
        $room_aid = strtr(Game::RoomUser_UID, $replyArr);

        $client_array = Gateway::getClientIdByUid($room_aid);
        if (is_array($client_array) && count($client_array) > 0) {
            foreach ($client_array as $bind_client) {
                if ($bind_client == $client_id) {
                    return TRUE;
                }
            }
        }
        return FALSE;
    }


}

?>
