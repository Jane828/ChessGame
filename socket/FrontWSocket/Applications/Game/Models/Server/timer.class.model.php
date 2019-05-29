<?php

use \GatewayWorker\Lib\Gateway;

require_once dirname(__DIR__) . '/public.class.model.php';

class Server_Timer_Model extends Public_Model {
    public function joinGroupNotify($arrData) {
        $data      = $arrData["data"];
        $operation = $arrData["operation"];
        if (!isset($data["account_id"])) {
            log_message("error", $operation . " miss data account_id " . __FILE__);
            return $this->_missingPrameterArr("joinGroupNotify", "account_id");
        }

        $account_id = $data["account_id"];
        if ($account_id < 1) {
            log_message("error", $operation ."invalid account_id" . __FILE__);
            return FALSE;
        }

        $arr = array("result" => 0, "operation" => $operation, "data" => array(), "result_message" => "好友申请通知");
        $this->pushMessageToAccount($account_id, $arr);

        return TRUE;
    }

    public function deleteMemberNotify($arrData) {
        $data      = $arrData["data"];
        $operation = $arrData["operation"];
        if (!isset($data["account_id"])) {
            log_message("error", $operation ." miss data account_id " . __FILE__);
            return $this->_missingPrameterArr("deleteMemberNotify", "account_id");
        }
        if (!isset($data["user_code"])) {
            log_message("error", $operation ." miss data user_code " . __FILE__);
            return $this->_missingPrameterArr("deleteMemberNotify", "user_code");
        }

        $account_id = $data["account_id"];
        $user_code = $data["user_code"];
        if ($account_id < 1) {
            log_message("error", $operation ."invalid account_id" . __FILE__);
            return FALSE;
        }

        $arr = array("result" => 0, "operation" => $operation, "data" => array("user_code"=>$user_code), "result_message" => "好友删除通知");
        $this->pushMessageToAccount($account_id, $arr);

        return TRUE;
    }

    public function addMemberNotify($arrData) {
        $data      = $arrData["data"];
        $operation = $arrData["operation"];
        if (!isset($data["account_id"])) {
            log_message("error", $operation ." miss data account_id ". __FILE__);
            return $this->_missingPrameterArr("addMemberNotify", "account_id");
        }
        if (!isset($data["user_code"])) {
            log_message("error", $operation ." miss data user_code" . __FILE__);
            return $this->_missingPrameterArr("addMemberNotify", "user_code");
        }

        $account_id = $data["account_id"];
        $user_code = $data["user_code"];
        if ($account_id < 1) {
            log_message("error", $operation ."invalid account_id" . __FILE__);
            return FALSE;
        }

        $arr = array("result" => 0, "operation" => $operation, "data" => array("user_code"=>$user_code), "result_message" => "好友添加通知");
        $this->pushMessageToAccount($account_id, $arr);

        return TRUE;
    }
}