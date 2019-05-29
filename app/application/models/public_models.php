<?php


require_once('db_models.php');
class Public_Models extends Db_Models
{
	public function __construct()
	{
		header('Content-Type: text/html; charset=utf-8'); 
		parent::__construct();
		$this->load->helper('url');
	}
	


	public function checkIPAddress()
	{
		return 1;
		$client_ip = $_SERVER["REMOTE_ADDR"];
		
		$jsonString = file_get_contents("http://ip.taobao.com/service/getIpInfo.php?ip=".$client_ip);
		
		$jsonArray = $this->splitJsonString($jsonString);
		if(isset($jsonArray['code']) && $jsonArray['code'] == "0")
		{
			if(isset($jsonArray['data']['country_id']) && $jsonArray['data']['country_id'] != "CN")
			{
				return 2;	//英文
			}
		}
		
		return 1;	//中文
	}
	
	
	
	
	
	
	/*
		检查用户是否已登录
	*/
	protected function missingPrameterArr($prameter)
	{
		return array('result'=>OPT_CONST::MISSING_PARAMETER,'data'=>array("missing_parameter"=>$prameter),'result_message'=>"缺少参数");
	}
	
	
	
	
	
	/*
		拆解接收的json字符串
	*/
	protected function splitJsonString($jsonString)
	{
		if(empty($jsonString))
		{
			return false;
		}
		//判断是否为JSON格式
		if(is_null(json_decode($jsonString)))
		{
			log_message('error', "function(splitJsonString):jsonString :".$jsonString." in file".__FILE__." on Line ".__LINE__);
			//不是json格式
			return false;
		}
		else
		{
			//分拆JSON字符串
			//return json_decode($jsonString,true);
			$jsonArray = json_decode($jsonString,true);
			if(isset($jsonArray['u_id']) && trim($jsonArray['u_id']) === G_CONST::EMPTY_STRING)
			{
				$jsonArray['u_id'] = 0;
			}
			return $jsonArray;
		}
	}
	
	
	/*
		数组转JSON格式
	*/
	public function JSON($array) 
	{  
		$this->arrayRecursive($array, 'urlencode', true);  
		$json = json_encode($array);  
		return urldecode($json);  
	}
	private function arrayRecursive(&$array, $function, $apply_to_keys_also = false)  
	{  
		static $recursive_counter = 0;  
		if (++$recursive_counter > 1000) {  
			die('possible deep recursion attack');  
		}  
		foreach ($array as $key => $value) {  
			if (is_array($value)) {  
				$this->arrayRecursive($array[$key], $function, $apply_to_keys_also);  
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
	
	
	
	
	
	
	/*
		判断是否绑定设备
	
	protected function checkWxAccountIsBind($open_id)
	{
		$is_bind = G_CONST::IS_FALSE;
		
		$query_account = $this->getData(Wechat_Account,'','bind_account',array('open_id'=>$open_id),1);
		if($query_account != DB_CONST::DATA_NONEXISTENT)
		{
			if($query_account['bind_account'] != "")
			{
				$is_bind = G_CONST::IS_TRUE;
			}
		}
		
		return $is_bind;
	}*/
	
	
	
	/*
		判断用户是否关注微信公众账号
	
	protected function checkIsSubscribe($open_id = "")
	{
		$is_subscribe = G_CONST::IS_FALSE;
		
		$Wechat_Model = new Wechat_Model();
		$update_result = $Wechat_Model->updateWechatAccount($open_id);
		if($update_result == -1)
		{
			//获取access_token失败
			log_message('error', "function(checkWxAccountExist):openid($open_id) can not get info"." in file".__FILE__." on Line ".__LINE__);
			return $is_subscribe;
		}
		$is_subscribe = $update_result['is_subscribe'];
		
		
		// $wxaccount_where = 'open_id="'.$openid.'"';
		// $wxaccount_sql = 'select is_subscribe from '.Wechat_Account.' where '.$wxaccount_where.'';
		// $wxaccount_query = $this->getDataBySql(1,$wxaccount_sql);
		// if($wxaccount_query != DB_CONST::DATA_NONEXISTENT)
		// {
		// 	$is_subscribe = $wxaccount_query['is_subscribe'];
		// }
		
		return $is_subscribe;
	}*/
	
	
	
	
	
	protected function getAccountByOpenid($open_id,$dealerDB)
	{
		$account_where = 'open_id="'.$open_id.'"';
		$account_sql = 'select account_id,open_id,nickname,headimgurl from '.WX_Account.' where '.$account_where.'';
		$account_query = $this->getDataBySql($dealerDB,1,$account_sql);
		return $account_query;
	}
	
	
	protected function getAccountByAccountid($account_id,$dealerDB)
	{
		$account_where = 'account_id='.$account_id;
		$account_sql = 'select account_id,open_id,nickname,headimgurl from '.WX_Account.' where '.$account_where.'';
		$account_query = $this->getDataBySql($dealerDB,1,$account_sql);
		return $account_query;
	}


	/*
		获取库存数量
	*/
	protected function getTicketInventory($dealerDB)
	{
		//获取代理商信息
		$inventory_count = 0;
		$account_where = 'is_delete=0';
		$account_sql = 'select inventory_count from '.D_Account.' where '.$account_where.'';
		$account_query = $this->getDataBySql($dealerDB,1,$account_sql);
		if($account_query != DB_CONST::DATA_NONEXISTENT)
		{
			$inventory_count = $account_query['inventory_count'];
		}

		return $inventory_count;
	}


	/*
		获取代理商账号 by code
	
	protected function getDealerByCode($code)
	{
		$account_where = 'code="'.$code.'"';
		$account_sql = 'select dealer_id,code,commission,ticket_count from '.D_Account.' where '.$account_where.'';
		$account_query = $this->getDataBySql(1,$account_sql);
		return $account_query;
	}
*/
	/*
		获取代理商账号 by id
	
	protected function getDealerByID($dealer_id)
	{
		$account_where = 'dealer_id="'.$dealer_id.'"';
		$account_sql = 'select dealer_id,code,commission,ticket_count from '.D_Account.' where '.$account_where.'';
		$account_query = $this->getDataBySql(1,$account_sql);
		return $account_query;
	}
*/


	/*
		判断用户是否已绑定代理商
	
	protected function getDealerBind($account_id)
	{
		//判断账号是否已兑换过房卡
		$exchange_sql = 'select bind_id,dealer_id from '.D_Bind.' where account_id='.$account_id;
		$exchange_query = $this->getDataBySql(1,$exchange_sql);
		return $exchange_query;
	}
*/

	/*
		添加提成记录
	
	protected function addDealerCommissionData($arrData)
	{
		$timestamp = time();

		$account_id = $arrData['account_id'];
		$order_id = $arrData['order_id'];
		$total_price = $arrData['total_price'];

		//判断用户是否已绑定代理商
		$exchange_query = $this->getDealerBind($account_id);
		if(DB_CONST::DATA_NONEXISTENT == $exchange_query)
		{
			log_message('error', "function(addDealerCommissionData):can get dealer bind : ".$account_id." in file".__FILE__." on Line ".__LINE__);
			return true;	//无绑定记录
		}
		$dealer_id = $exchange_query['dealer_id'];

		//获取代理商信息
		$dealer_query = $this->getDealerByID($dealer_id);
		if(DB_CONST::DATA_NONEXISTENT == $dealer_query)
		{
			log_message('error', "function(addDealerCommissionData):can get dealer info"." in file".__FILE__." on Line ".__LINE__);
			return true;	//无代理商记录
		}
		$commission = $dealer_query['commission'];

		//提成金额
		$income_price = round((($total_price * $commission)/100),2);

		log_message('error', "function(addDealerCommissionData):income_price : ".$income_price." in file".__FILE__." on Line ".__LINE__);

		//添加提成记录
		$commission_array['create_time'] = $timestamp;
		$commission_array['create_appid'] = "aid_".$account_id;
		$commission_array['update_time'] = $timestamp;
		$commission_array['update_appid'] = "aid_".$account_id;
		$commission_array['is_delete'] = G_CONST::IS_FALSE;
		$commission_array['dealer_id'] = $dealer_id;
		$commission_array['account_id'] = $account_id;
		$commission_array['order_id'] = $order_id;
		$commission_array['order_price'] = $total_price;
		$commission_array['commission_price'] = $income_price;
		$commission_id = $this->getInsertID(D_Commission, $commission_array);


		$extra = "充值用户：".$account_id;
		$abstract = "充值提成";

		//获取流水账越
		$balance = 0;
		$balance_where = 'dealer_id='.$dealer_id.' and is_delete=0';
		$balance_sql = 'select balance from '.D_Journal.' where '.$balance_where.' order by update_time desc';
		$balance_query = $this->getDataBySql(1,$balance_sql);
		if(DB_CONST::DATA_NONEXISTENT != $balance_query)
		{
			$balance = $balance_query['balance'];
		}

		//添加到流水账
		$journal_array['create_time'] = $timestamp;
		$journal_array['create_appid'] = "aid_".$account_id;
		$journal_array['update_time'] = $timestamp;
		$journal_array['update_appid'] = "aid_".$account_id;
		$journal_array['is_delete'] = G_CONST::IS_FALSE;
		$journal_array['dealer_id'] = $dealer_id;
		$journal_array['object_id'] = $order_id;
		$journal_array['object_type'] = Payment_CONST::JournalType_Commission;	//销售提成
		$journal_array['income'] = $income_price;
		$journal_array['balance'] = $balance + $income_price;
		$journal_array['extra'] = $extra;
		$journal_array['abstract'] = $abstract;		//摘要
		$journal_id = $this->getInsertID(D_Journal, $journal_array);

		return true;
	}
*/


	/*
		添加房卡流水记录
	*/
	protected function updateRoomTicketJournal($arrData,$dealerDB)
	{
		$timestamp = time();

		$journal_type = $arrData['journal_type'];	//1入账，2出账
		$account_id = $arrData['account_id'];
		$object_type = $arrData['object_type'];
		$object_id = $arrData['object_id'];
		$ticket_count = $arrData['ticket_count'];
		$extra = $arrData['extra'];

		//获取流水账越
		$balance = 0;
		$balance_where = 'account_id='.$account_id.' and is_delete=0';
		$balance_sql = 'select balance from '.Room_TicketJournal.' where '.$balance_where.' order by journal_id desc';
		$balance_query = $this->getDataBySql($dealerDB,1,$balance_sql);
		if(DB_CONST::DATA_NONEXISTENT != $balance_query)
		{
			$balance = $balance_query['balance'];
		}

		switch($object_type)
		{
			case Game_CONST::ObjectType_Newuser:
				$abstract = "用户首次登陆";
				break;
			case Game_CONST::ObjectType_Recharge:
				$abstract = "购买房卡";
				break;
			case Game_CONST::ObjectType_Game:
				$abstract = "游戏消耗";
				break;
			case Game_CONST::ObjectType_Dealer:
				$abstract = "绑定代理商";
				break;
			case Game_CONST::ObjectType_Sign:
				$abstract = "每日签到";
				break;
			case Game_CONST::ObjectType_RedEnvelop:
				$abstract = "红包活动";
				break;
			case Game_CONST::ObjectType_Luckdraw:
				$abstract = "幸运转盘";
				break;
			case Game_CONST::ObjectType_SlotMachine:
				$abstract = "老虎机抽奖";
				break;
			case Game_CONST::ObjectType_Commission:
				$abstract = "销售提成";
				break;
			case Game_CONST::ObjectType_BindAccount:
				$abstract = "绑定手机转移房卡";
				break;
			case Game_CONST::ObjectType_Exchange:
				$abstract = "兑换码兑换房卡";
				break;
            case Game_CONST::ObjectType_Manage:
                $abstract = '开启管理功能';
                break;
            case Game_CONST::ObjectType_Transfer:
                $abstract = '转移房卡';
                break;
			default :
				log_message('error', "function(updateRoomTicketJournal):object_type error : ".$object_type." in file".__FILE__." on Line ".__LINE__);
				return false;
				break;
		}


		//添加到流水账
		$journal_array['create_time'] = $timestamp;
		$journal_array['create_appid'] = "aid_".$account_id;
		$journal_array['update_time'] = $timestamp;
		$journal_array['update_appid'] = "aid_".$account_id;
		$journal_array['is_delete'] = G_CONST::IS_FALSE;
		$journal_array['account_id'] = $account_id;
		$journal_array['object_id'] = $object_id;
		$journal_array['object_type'] = $object_type;
		$journal_array['journal_type'] = $journal_type;
		$journal_array['extra'] = $extra;
		$journal_array['abstract'] = $abstract;		//摘要

		if($journal_type == Game_CONST::JournalType_Income)
		{
			$journal_array['income'] = $ticket_count;
			$journal_array['balance'] = $balance + $ticket_count;
		}
		else if($journal_type == Game_CONST::JournalType_Disburse)
		{
			$journal_array['disburse'] = $ticket_count;
			$journal_array['balance'] = $balance - $ticket_count;
			if($journal_array['balance'] < 0)
			{
				log_message('error', "function(updateRoomTicketJournal):balance negative balance: ".$balance." in file".__FILE__." on Line ".__LINE__);
				log_message('error', "function(updateRoomTicketJournal):balance negative account_id: ".$account_id." in file".__FILE__." on Line ".__LINE__);
				log_message('error', "function(updateRoomTicketJournal):balance negative object_type: ".$object_type." in file".__FILE__." on Line ".__LINE__);
				log_message('error', "function(updateRoomTicketJournal):balance negative object_id: ".$object_id." in file".__FILE__." on Line ".__LINE__);
				log_message('error', "function(updateRoomTicketJournal):balance negative ticket_count: ".$ticket_count." in file".__FILE__." on Line ".__LINE__);
				$journal_array['balance'] = 0;
			}
		}
		else
		{
			log_message('error', "function(updateRoomTicketJournal):journal_type error : ".$journal_type." in file".__FILE__." on Line ".__LINE__);
			return false;
		}
		$journal_id = $this->getInsertID($dealerDB,Room_TicketJournal, $journal_array);

		return true;
	} 


	
	/*
		添加代理商流水记录
	*/
	protected function updateInventoryJournal($arrData,$dealerDB)
	{
		$timestamp = time();

		$journal_type = $arrData['journal_type'];	//1入账，2出账
		$account_id = $arrData['account_id'];
		$object_type = $arrData['object_type'];
		$object_id = $arrData['object_id'];
		$ticket_count = $arrData['ticket_count'];
		$extra = $arrData['extra'];

		//获取流水账越
		$balance = 0;
		$balance_where = 'is_delete=0';
		$balance_sql = 'select balance from '.D_Journal.' where '.$balance_where.' order by journal_id desc';
		$balance_query = $this->getDataBySql($dealerDB,1,$balance_sql);
		if(DB_CONST::DATA_NONEXISTENT != $balance_query)
		{
			$balance = $balance_query['balance'];
		}
		else
		{
			$balance = $this->getTicketInventory($dealerDB);
		}

		switch($object_type)
		{	
			case Game_CONST::DObjectType_Balance:
				$abstract = "调整房卡";
				break;
			case Game_CONST::DObjectType_Recharge:
				$abstract = "后台充值";
				break;
			case Game_CONST::DObjectType_Sale:
				$abstract = "购买房卡";
				break;
			case Game_CONST::DObjectType_RedEnvelop:
				$abstract = "房卡红包";
				break;
			default :
				log_message('error', "function(updateInventoryJournal):object_type error : ".$object_type." in file".__FILE__." on Line ".__LINE__);
				return false;
				break;
		}

		//添加到流水账
		$journal_array['create_time'] = $timestamp;
		$journal_array['create_appid'] = "aid_".$account_id;
		$journal_array['update_time'] = $timestamp;
		$journal_array['update_appid'] = "aid_".$account_id;
		$journal_array['is_delete'] = G_CONST::IS_FALSE;
		//$journal_array['account_id'] = $account_id;
		$journal_array['object_id'] = $object_id;
		$journal_array['object_type'] = $object_type;
		
		$journal_array['extra'] = $extra;
		$journal_array['abstract'] = $abstract;		//摘要

		if($journal_type == Game_CONST::JournalType_Income)
		{
			$journal_array['income'] = $ticket_count;
			$journal_array['balance'] = $balance + $ticket_count;
		}
		else if($journal_type == Game_CONST::JournalType_Disburse)
		{
			$journal_array['disburse'] = $ticket_count;
			$journal_array['balance'] = $balance - $ticket_count;
			if($journal_array['balance'] < 0)
			{
				log_message('error', "function(updateInventoryJournal):balance negative balance: ".$balance." in file".__FILE__." on Line ".__LINE__);
				log_message('error', "function(updateInventoryJournal):balance negative account_id: ".$account_id." in file".__FILE__." on Line ".__LINE__);
				log_message('error', "function(updateInventoryJournal):balance negative object_type: ".$object_type." in file".__FILE__." on Line ".__LINE__);
				log_message('error', "function(updateInventoryJournal):balance negative object_id: ".$object_id." in file".__FILE__." on Line ".__LINE__);
				log_message('error', "function(updateInventoryJournal):balance negative ticket_count: ".$ticket_count." in file".__FILE__." on Line ".__LINE__);
				$journal_array['balance'] = 0;
			}
		}
		else
		{
			log_message('error', "function(updateInventoryJournal):journal_type error : ".$journal_type." in file".__FILE__." on Line ".__LINE__);
			return false;
		}
		$journal_id = $this->getInsertID($dealerDB,D_Journal, $journal_array);

		return true;
	}

	/**
	 * 获取用户剩余房卡
	 * @param $account_id
	 * @return int
	 */
	protected function getAccountTicket($account_id){
		$ticket_where = 'account_id='.$account_id.' and is_delete=0 limit 1';
		$ticket_sql = 'select ticket_count from '.Room_Ticket.' where '.$ticket_where;
		return $this->getDataBySql(Game_CONST::DBConst_Name,1,$ticket_sql);
	}

	/**
	 * 获取用户信息
	 * @param $account_id
	 * @return int
	 */
	protected function getAccount($account_id){
		$ticket_where = 'account_id='.$account_id.' limit 1';
		$account_sql = 'select * from '.WX_Account.' where '.$ticket_where;
		return $this->getDataBySql(Game_CONST::DBConst_Name,1,$account_sql);
	}

    /**
     * 获得一个数据库对象
     * @return mixed
     */
    public function db(){
        return $this->load->database(Game_CONST::DBConst_Name, TRUE);
    }

    /**
     * 函数描述：发送消息给websocket的服务端，即时推送需要
     * @param $operation
     * @param $data 数组数据
     * author 黄欣仕
     * date 2019/2/28
     */
    public function sendToFrontWebsocket($operation, $data) {
        $client = stream_socket_client(G_CONST::FRONT_WEBSOCKET_SERVER);
        $req    = array(
            "operation" => $operation,
            "data" => $data,
        );

        fwrite($client, json_encode($req));
        fwrite($client, "\r\n");

        fclose($client);
    }
	
}