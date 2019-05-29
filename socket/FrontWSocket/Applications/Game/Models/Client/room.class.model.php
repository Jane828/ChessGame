<?php

use \GatewayWorker\Lib\Gateway;

require_once dirname(__DIR__) . '/public.class.model.php';

class Front_Model extends Public_Model {

    /***************************
     * common function
     ***************************/


    /*
        断线
    */
    public function userDisconnected($arrData) {
        if (!isset($arrData['account_id']) || trim($arrData['account_id']) == G_CONST::EMPTY_STRING) {
            return FALSE;
        }
	if (!isset($arrData['ws_group']) || trim($arrData['ws_group']) == G_CONST::EMPTY_STRING) {
            return FALSE;
        }

	$client_id = $arrData['client_id'];
	$account_id = $arrData['account_id'];
	$ws_group = $arrData['ws_group'];
	
	Gateway::unbindUid($client_id, $account_id);
	Gateway::leaveGroup($client_id, $ws_group);
	
        return TRUE;
    }



    /***************************
     * logic function
     ***************************/

    public function initConnect($arrData) {
        $data       = $arrData['data'];
        $operation  = $arrData['operation'];
        $account_id = $arrData['account_id'];
        $client_id  = $arrData['client_id'];
        $ws_group = $data["group"];

        if (!in_array($ws_group, array(G_CONST::SOCKET_GROUB_FRIEND))){
            return array("result" => OPT_CONST::FAILED, "operation" => "InitConnect", "data" => array("group"), "result_message" => "参数错误");
        }

        Gateway::bindUid($client_id, $account_id);
        Gateway::joinGroup($client_id, $ws_group);
	$_SESSION['account_id'] = $account_id;
	$_SESSION['ws_group'] = $ws_group;

        return array("result" => OPT_CONST::SUCCESS, "operation" => "InitConnect", "data" => array(), "result_message" => "初始化连接成功");
    }


}
