<?php

include_once dirname(__DIR__).'/public_models.php';		//加载数据库操作类
class Payment_Common_Model extends Public_Models
{
	public function __construct()
	{
		header('Content-Type: text/html; charset=utf-8'); 
		parent::__construct();
	}
	
	
	/*
		 生成随机字符串
	 */
	 protected function createNonceStr()
	 {
		$mtime=explode(' ',microtime());  
		$mTimestamp = $mtime[1] . substr($mtime[0],2,3);
		
		$order_code = $mTimestamp;
		
		
		for($i=0;$i<6;$i++)
		{
			$order_code .= rand(0,9);
		}
		
		return md5($order_code);
	 }
	 
	 
	/*
		 生成签名
	*/
	protected function getSign($parameters,$api_key="")
	{
		$stringA = "";
		
		$i = 0;
		foreach($parameters as $key=>$value)
		{
			if($i == 0)
			{
				$stringA .= $key."=".$value;
			}
			else
			{
				$stringA .= "&".$key."=".$value;
			}
			$i++;
		}
		
		if($api_key == "")
		{
			$api_key = Payment_CONST::WX_API_Key;
		}
		
		$stringSignTemp = $stringA."&key=".$api_key;
		$sign = strtoupper(MD5($stringSignTemp));
		return $sign;
	}
	
	
	/*
		生成订单号
	*/
	protected function _buildOrderNO($pre="G")
	{
		$mtime=explode(' ',microtime());  
		$mTimestamp = $mtime[1] . substr($mtime[0],2,3);
		
		$order_code = $pre.$mTimestamp;
		
		
		for($i=0;$i<6;$i++)
		{
			$order_code .= rand(0,9);
		}
		
		return $order_code;
	}
	
	/**
     * GET 请求
     */
    protected function _getRequest($url) {
        $ch = curl_init();
        curl_setopt($ch, CURLOPT_TIMEOUT, 30);
        curl_setopt($ch, CURLOPT_URL, $url);
        curl_setopt($ch, CURLOPT_SSL_VERIFYPEER,FALSE);
        curl_setopt($ch, CURLOPT_SSL_VERIFYHOST,FALSE);
        curl_setopt($ch, CURLOPT_HEADER, FALSE);
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, TRUE);
        $res = curl_exec($ch);
        curl_close($ch);

        return $res;
    }
	
	
	//将XML转为array
    protected function xmlToArray($xml)
    {    
        //禁止引用外部xml实体
        libxml_disable_entity_loader(true);
        $values = json_decode(json_encode(simplexml_load_string($xml, 'SimpleXMLElement', LIBXML_NOCDATA)), true);        
        return $values;
    }
	
	protected function arrtoxml($arr,$dom=0,$item=0){
	    if (!$dom){
	        $dom = new DOMDocument();
	    }
	    if(!$item){
	        $item = $dom->createElement("xml"); 
	        $dom->appendChild($item);
	    }
	    foreach ($arr as $key=>$val){
	        $itemx = $dom->createElement(is_string($key)?$key:"item");
	        $item->appendChild($itemx);
	        if (!is_array($val)){
	            $text = $dom->createTextNode($val);
	            $itemx->appendChild($text);
	             
	        }else {
	            arrtoxml($val,$dom,$itemx);
	        }
	    }
	    return $dom->saveXML();
	}
	
		
	/*
		支付渠道验证通过
		
		验证支付信息
	*/
	protected function updatePaymentInfo($arrData,$dealerDB)
	{
		$timestamp = time();
		$out_trade_no = $arrData["out_trade_no"];
		$total_fee = $arrData["total_fee"];
		$dealer_num = $arrData["dealer_num"];
		$discount = 0;
		if(isset($arrData["discount"]))
		{
			$discount = $arrData["discount"];
		}
		
		//判断订单号是否已支付，总金额是否相等
		$order_where = 'order_no="'.$out_trade_no.'" and status='.Payment_CONST::OrderStatus_WaitForPay.' and is_pay='.G_CONST::IS_FALSE.' limit 1';
		$order_sql = 'select account_id,order_id,total_price from '.Payment_Order.' where '.$order_where;
		$order_query = $this->getDataBySql($dealerDB,1,$order_sql);
		if($order_query == DB_CONST::DATA_NONEXISTENT)
		{
			log_message('error', "function(updatePaymentInfo):can not find order($out_trade_no)"." in file".__FILE__." on Line ".__LINE__);
			return false;		//请不要修改或删除
		}
		
		log_message('error', "function(updatePaymentInfo):total_price : ".$order_query['total_price']." in file".__FILE__." on Line ".__LINE__);
		
		$order_id = $order_query['order_id'];
		$total_price = $order_query['total_price'];
		$account_id = $order_query['account_id'];
		
		if($total_price != $total_fee)
		{
			log_message('error', "function(updatePaymentInfo):order_id($order_id) total_price($total_price) total_fee($total_fee) : "." in file".__FILE__." on Line ".__LINE__);
			return false;		//请不要修改或删除
		}
		
		//判断订单商品是否存在
		$goods_where = 'order_id='.$order_id;
		$goods_sql = 'select goods_id,ticket_count,count from '.Payment_OrderGoods.' where '.$goods_where.'';
		$goods_query = $this->getDataBySql($dealerDB,1,$goods_sql);
		if(DB_CONST::DATA_NONEXISTENT == $goods_query)
		{
			log_message('error', "function(updatePaymentInfo):order goods is not exist:$order_id"." in file".__FILE__." on Line ".__LINE__);
			return false;		//请不要修改或删除
		}
		$ticket_count = $goods_query['ticket_count'];
		$count = $goods_query['count'];
		
		
		//验证通过
		//修改入库状态
		$update_array['update_time'] = $timestamp;
		$update_array['pay_time'] = $timestamp;
		$update_array['status'] = Payment_CONST::OrderStatus_Paid;
		$update_array['is_pay'] = G_CONST::IS_TRUE;
		$update_query = $this->updateFunc($dealerDB,"order_id",$order_id,Payment_Order,$update_array);
		
		if($update_query != DB_CONST::SUCCESS)
		{
			log_message('error', "function(updatePaymentInfo):update_query : fail "." in file".__FILE__." on Line ".__LINE__);
			return false;		//请不要修改或删除
		}
		else
		{
			$add_count = $ticket_count * $count;
			
			$update_str = 'update_time='.$timestamp.',update_appid="aid_'.$account_id.'",ticket_count=ticket_count+'.$add_count;
			$update_where = 'account_id='.$account_id;
			$update_query = $this->changeNodeValue($dealerDB,Room_Ticket,$update_str,$update_where);

			//房卡流水账
			$journal_ary['journal_type'] = Game_CONST::JournalType_Income;
			$journal_ary['account_id'] = $account_id;
			$journal_ary['object_type'] = Game_CONST::ObjectType_Recharge;
			$journal_ary['object_id'] = $order_id;
			$journal_ary['ticket_count'] = $add_count;
			$journal_ary['extra'] = "";
			$this->updateRoomTicketJournal($journal_ary,$dealerDB);


			//房卡库存流水账
			$i_journal_ary['journal_type'] = Game_CONST::JournalType_Disburse;
			$i_journal_ary['account_id'] = $account_id;
			$i_journal_ary['object_type'] = Game_CONST::DObjectType_Sale;
			$i_journal_ary['object_id'] = $order_id;
			$i_journal_ary['ticket_count'] = $add_count;
			$i_journal_ary['extra'] = "";
			$this->updateInventoryJournal($i_journal_ary,$dealerDB);

			//扣除房卡库存
			$updateInventory_str = 'update_time='.$timestamp.',update_appid="aid_'.$account_id.'",inventory_count=inventory_count-'.$ticket_count;
			$updateInventory_where = 'is_delete=0';
			$updateInventory_query = $this->changeNodeValue($dealerDB,D_Account,$updateInventory_str,$updateInventory_where);

			
			$DelaerConst = "Dealer_".$dealer_num;
			//分销提成
			if($DelaerConst::Is_Dist == 1)
			{
				//结算
				$commission_ary['ticket_count'] = $add_count;
				$commission_ary['order_id'] = $order_id;
				$commission_ary['account_id'] = $account_id;
				$commission_ary['dealerDB'] = $dealerDB;
				$this->balanceOrderCommission($commission_ary);
			}

			//分销提成
			if($DelaerConst::Is_Guild == 1)
			{
				//结算
				$commission_ary['ticket_count'] = $add_count;
				$commission_ary['order_id'] = $order_id;
				$commission_ary['account_id'] = $account_id;
				$commission_ary['dealerDB'] = $dealerDB;
				$commission_ary['total_price'] = $total_price;
				$this->balanceGuildCommission($commission_ary);
			}

			log_message('error', "function(updatePaymentInfo):update_query : success "." in file".__FILE__." on Line ".__LINE__);
			return true;		//请不要修改或删除
		}
	}


	/*
		判断是否需要提成结算
	*/
	protected function balanceGuildCommission($arrData)
	{	
		$timestamp = time();
		
		$ticket_count = $arrData['ticket_count'];
		$order_id = $arrData['order_id'];
		$account_id = $arrData['account_id'];
		$dealerDB = $arrData['dealerDB'];
		$total_price = $arrData['total_price'];
		$object_type = 1;	//1订单提成

		//判断是否加入公会
		$member_where = 'account_id='.$account_id.' and is_delete=0';
		$member_sql = 'select group_id,level,vice_president from '.Guild_Member.' where '.$member_where.' order by create_time desc limit 1';
		$member_query = $this->getDataBySql($dealerDB,1,$member_sql);
		if(DB_CONST::DATA_NONEXISTENT == $member_query)
		{
			//未进入公会
			return true;
		}
		$group_id = $member_query['group_id'];
		$vice_president = $member_query['vice_president'];

		//获取公会提成比例
		$commission_sql = 'select guild_rate,vice_rate from '.Guild_CommissionRate.' where is_delete=0 and type=1';
		$commission_query = $this->getDataBySql($dealerDB,1,$commission_sql);
		if(DB_CONST::DATA_NONEXISTENT == $commission_query)
		{
			//没提成记录
			return true;
		}
		$guild_rate = $commission_query['guild_rate'];
		$vice_rate = $commission_query['vice_rate'];

		$guild_commission = round(($total_price * $guild_rate / 100),2);
		$vice_commission = 0;
		if($vice_president > 0)
		{
			$vice_commission = round(($total_price * $vice_rate / 100),2);
		}
		
		if($guild_commission > 0 || $vice_commission > 0)
		{
			$record_array['create_time'] = $timestamp;
			$record_array['create_appid'] = "aid_".$account_id;
			$record_array['update_time'] = $timestamp;
			$record_array['update_appid'] = "aid_".$account_id;
			$record_array['is_delete'] = 0;
			$record_array['customer'] = $account_id;
			$record_array['group_id'] = $group_id;
			$record_array['object_type'] = $object_type;
			$record_array['object_id'] = $order_id;
			$record_array['vice_president'] = $vice_president;
			$record_array['ticket_count'] = $ticket_count;
			$record_array['price'] = $total_price;
			$record_array['vice_commission'] = $vice_commission;
			$record_array['guild_commission'] = $guild_commission;
			$record_id = $this->getInsertID($dealerDB,Guild_CommissionRecord, $record_array);
			unset($record_array);

			$balance = 0;
			$balance_sql = 'select balance from '.Guild_Balance.' where group_id='.$group_id.' and is_delete=0 order by balance_id desc limit 1';
			$balance_query = $this->getDataBySql($dealerDB,1,$balance_sql);
			if($balance_sql != DB_CONST::DATA_NONEXISTENT)
			{
				$balance = $balance_query['balance'];
			}
			$balance += $guild_commission;

			$balance_array['create_time'] = $timestamp;
			$balance_array['create_appid'] = "aid_".$account_id;
			$balance_array['update_time'] = $timestamp;
			$balance_array['update_appid'] = "aid_".$account_id;
			$balance_array['is_delete'] = 0;
			$balance_array['group_id'] = $group_id;
			$balance_array['object_id'] = $record_id;
			$balance_array['object_type'] = 1;
			$balance_array['income'] = $guild_commission;
			$balance_array['balance'] = $balance;
			$balance_array['abstract'] = "会员充值提成";
			$balance_id = $this->getInsertID($dealerDB,Guild_Balance, $balance_array);
			unset($balance_array);

			if($vice_president > 0 && $vice_commission > 0)
			{
				$balance = 0;
				$balance_sql = 'select balance from '.Guild_ViceBalance.' where group_id='.$group_id.' and vice_president='.$vice_president.' and is_delete=0 order by balance_id desc limit 1';
				$balance_query = $this->getDataBySql($dealerDB,1,$balance_sql);
				if($balance_sql != DB_CONST::DATA_NONEXISTENT)
				{
					$balance = $balance_query['balance'];
				}
				
				$balance += $vice_commission;

				$balance_array['create_time'] = $timestamp;
				$balance_array['create_appid'] = "aid_".$account_id;
				$balance_array['update_time'] = $timestamp;
				$balance_array['update_appid'] = "aid_".$account_id;
				$balance_array['is_delete'] = 0;
				$balance_array['group_id'] = $group_id;
				$balance_array['vice_president'] = $vice_president;
				$balance_array['object_id'] = $record_id;
				$balance_array['object_type'] = 1;
				$balance_array['income'] = $vice_commission;
				$balance_array['balance'] = $balance;
				$balance_array['abstract'] = "会员充值提成";
				$balance_id = $this->getInsertID($dealerDB,Guild_ViceBalance, $balance_array);
				unset($balance_array);
			}
		}

		return true;
	}




	
	/*
		判断是否需要提成结算
	*/
	protected function balanceOrderCommission($arrData)
	{	
		$timestamp = time();
		
		$ticket_count = $arrData['ticket_count'];
		$order_id = $arrData['order_id'];
		$account_id = $arrData['account_id'];
		$dealerDB = $arrData['dealerDB'];
		$object_type = Game_CONST::CommissionType_Order;	//1订单提成

		//判断用户是否已绑定上家
		$dist_where = 'account_id='.$account_id.' and is_delete=0';
		$dist_sql = 'select intro_aid_1,intro_aid_2 from '.Dist_Account.' where '.$dist_where.'';
		$dist_query = $this->getDataBySql($dealerDB,1,$dist_sql);
		if(DB_CONST::DATA_NONEXISTENT == $dist_query)
		{
			return true;
		}
		$intro_aid_1 = $dist_query['intro_aid_1'];
		$intro_aid_2 = $dist_query['intro_aid_2'];

		log_message('error', "function(balanceOrderCommission):intro_aid_1 : ".$intro_aid_1." in file".__FILE__." on Line ".__LINE__);
		log_message('error', "function(balanceOrderCommission):intro_aid_2 : ".$intro_aid_2." in file".__FILE__." on Line ".__LINE__);

		//获取提成规则
		$commission_where = 'type='.Game_CONST::CommissionType_Order.' and is_delete=0';
		$commission_sql = 'select commission_1,commission_2 from '.Dist_Commission.' where '.$commission_where.'';
		$commission_query = $this->getDataBySql($dealerDB,1,$commission_sql);
		if(DB_CONST::DATA_NONEXISTENT == $commission_query)
		{
			return true;
		}
		$commission_1 = $commission_query['commission_1'];
		$commission_2 = $commission_query['commission_2'];

		log_message('error', "function(balanceOrderCommission):commission_1 : ".$commission_1." in file".__FILE__." on Line ".__LINE__);
		log_message('error', "function(balanceOrderCommission):commission_2 : ".$commission_2." in file".__FILE__." on Line ".__LINE__);

		if($intro_aid_1 > 0 && $commission_1 > 0)
		{
			//计算上线1提成
			$ticket_commission_1 = floor($ticket_count * $commission_1/10000);
			if($ticket_commission_1 > 0)
			{
				//添加提成记录
				$commission_ary['create_time'] = $timestamp;
				$commission_ary['create_appid'] = "aid_".$account_id;
				$commission_ary['update_time'] = $timestamp;
				$commission_ary['update_appid'] = "aid_".$account_id;
				$commission_ary['is_delete'] = 0;
				$commission_ary['account_id'] = $intro_aid_1;
				$commission_ary['object_type'] = $object_type;
				$commission_ary['object_id'] = $order_id;
				$commission_ary['object_aid'] = $account_id;
				$commission_ary['ticket_count'] = $ticket_count;
				$commission_ary['commission_count'] = $ticket_commission_1;
				$record_id = $this->getInsertID($dealerDB,Dist_CommissionRecord, $commission_ary);
				unset($commission_ary);

				//添加流水
				$update_str = 'update_time='.$timestamp.',update_appid="aid_'.$intro_aid_1.'",ticket_count=ticket_count+'.$ticket_commission_1;
				$update_where = 'account_id='.$intro_aid_1;
				$update_query = $this->changeNodeValue($dealerDB,Room_Ticket,$update_str,$update_where);

				//房卡流水账
				$journal_ary['journal_type'] = Game_CONST::JournalType_Income;
				$journal_ary['account_id'] = $intro_aid_1;
				$journal_ary['object_type'] = Game_CONST::ObjectType_Commission;
				$journal_ary['object_id'] = $record_id;
				$journal_ary['ticket_count'] = $ticket_commission_1;
				$journal_ary['extra'] = "";
				$this->updateRoomTicketJournal($journal_ary,$dealerDB);
				unset($journal_ary);
			}
		}

		if($intro_aid_2 > 0 && $commission_2 > 0)
		{
			//计算上线2提成
			$ticket_commission_2 = floor($ticket_count * $commission_2/10000);
			if($ticket_commission_1 > 0)
			{
				//添加提成记录
				$commission_ary['create_time'] = $timestamp;
				$commission_ary['create_appid'] = "aid_".$account_id;
				$commission_ary['update_time'] = $timestamp;
				$commission_ary['update_appid'] = "aid_".$account_id;
				$commission_ary['is_delete'] = 0;
				$commission_ary['account_id'] = $intro_aid_2;
				$commission_ary['object_type'] = $object_type;
				$commission_ary['object_id'] = $order_id;
				$commission_ary['object_aid'] = $account_id;
				$commission_ary['ticket_count'] = $ticket_count;
				$commission_ary['commission_count'] = $ticket_commission_2;
				$record_id = $this->getInsertID($dealerDB,Dist_CommissionRecord, $commission_ary);
				unset($commission_ary);

				//添加流水
				$update_str = 'update_time='.$timestamp.',update_appid="aid_'.$intro_aid_2.'",ticket_count=ticket_count+'.$ticket_commission_2;
				$update_where = 'account_id='.$intro_aid_2;
				$update_query = $this->changeNodeValue($dealerDB,Room_Ticket,$update_str,$update_where);

				//房卡流水账
				$journal_ary['journal_type'] = Game_CONST::JournalType_Income;
				$journal_ary['account_id'] = $intro_aid_2;
				$journal_ary['object_type'] = Game_CONST::ObjectType_Commission;
				$journal_ary['object_id'] = $record_id;
				$journal_ary['ticket_count'] = $ticket_commission_2;
				$journal_ary['extra'] = "";
				$this->updateRoomTicketJournal($journal_ary,$dealerDB);
				unset($journal_ary);
			}
		}

		return true;
	}


	



	





	
}