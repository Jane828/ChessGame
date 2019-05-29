<?php

include_once 'common_model.php';		//加载数据库操作类
class Ticket_Model extends Exchange_Common_Model
{
	public function __construct()
	{
		header('Content-Type: text/html; charset=utf-8'); 
		parent::__construct();
	}
	
	/************************************************
					common function
	*************************************************/
	

	protected function createRedEnvelopCode($count)
	{
		$code = "";

		$chars = 'abcdefghijklmnopqrstuvwxyzABCDEFGHIJKLMNOPQRSTUVWXYZ0123456789'; 
		for($i=0;$i<$count;$i++)
		{
			$code .= $chars[ mt_rand(0, strlen($chars) - 1) ]; 
		}
		
		return $count;
	}


	/*
		兑换房卡
		
		参数：
			account_id : account_id
		
		返回结果：
			
		
	*/
	public function exchangeTicketOpt($arrData)
	{
		$timestamp = time();
		$result = array();
		
		if(!isset($arrData['dealer_num']) || $arrData['dealer_num'] == G_CONST::EMPTY_STRING)
		{
			log_message('error', "function(exchangeTicketOpt):lack of dealer_num"." in file".__FILE__." on Line ".__LINE__);
			return $this->missingPrameterArr("dealer_num");
		}
		if(!isset($arrData['account_id']) || $arrData['account_id'] == G_CONST::EMPTY_STRING)
		{
			log_message('error', "function(exchangeTicketOpt):lack of account_id"." in file".__FILE__." on Line ".__LINE__);
			return $this->missingPrameterArr("account_id");
		}
		if(!isset($arrData['code']) || $arrData['code'] == G_CONST::EMPTY_STRING)
		{
			log_message('error', "function(exchangeTicketOpt):lack of code"." in file".__FILE__." on Line ".__LINE__);
			return $this->missingPrameterArr("code");
		}
		if(!isset($arrData['pwd']) || $arrData['pwd'] == G_CONST::EMPTY_STRING)
		{
			log_message('error', "function(exchangeTicketOpt):lack of pwd"." in file".__FILE__." on Line ".__LINE__);
			return $this->missingPrameterArr("pwd");
		}

		$dealer_num = $arrData['dealer_num'];
		$account_id = $arrData['account_id'];
		$code = strtoupper($arrData['code']);
		$pwd = strtoupper($arrData['pwd']);
		
		$DelaerConst = "Dealer_".$dealer_num;
        $dealerDB = $DelaerConst::DBConst_Name;

		//判断兑换码是否存在
		$data_sql = 'select data_id,pwd,ticket_count,account_id from '.Exchange_Ticket.' where code="'.$code.'" and is_delete=0';
		$data_query = $this->getDataBySql($dealerDB,1,$data_sql);
        if(DB_CONST::DATA_NONEXISTENT == $data_query)
		{
			return array('result'=>OPT_CONST::FAILED,'data'=>$result,'result_message'=>"无效兑换码");
		}
		if($data_query['pwd'] != $pwd)
		{
			return array('result'=>OPT_CONST::FAILED,'data'=>$result,'result_message'=>"兑换密码错误");
		}
		if($data_query['account_id'] > 0)
		{
			return array('result'=>OPT_CONST::FAILED,'data'=>$result,'result_message'=>"兑换码已被兑换");
		}
		$data_id = $data_query['data_id'];
		$ticket_count = $data_query['ticket_count'];

		//验证通过
		$update_array['update_time'] = $timestamp;
		$update_array['update_appid'] = "aid_".$account_id;
		$update_array['account_id'] = $account_id;
		$update_query = $this->updateFunc($dealerDB,"data_id",$data_id,Exchange_Ticket,$update_array);
		unset($update_array);

		//添加库存
		$update_str = 'update_time='.$timestamp.',update_appid="aid_'.$account_id.'",ticket_count=ticket_count+'.$ticket_count;
		$update_where = 'account_id='.$account_id;
		$update_query = $this->changeNodeValue($dealerDB,Room_Ticket,$update_str,$update_where);

		//房卡流水账
		$journal_ary['journal_type'] = Game_CONST::JournalType_Income;
		$journal_ary['account_id'] = $account_id;
		$journal_ary['object_type'] = Game_CONST::ObjectType_Exchange;
		$journal_ary['object_id'] = $data_id;
		$journal_ary['ticket_count'] = $ticket_count;
		$journal_ary['extra'] = "";
		$this->updateRoomTicketJournal($journal_ary,$dealerDB);

		$result['ticket_count'] = $ticket_count;
		
		return array('result'=>OPT_CONST::SUCCESS,'data'=>$result,'result_message'=>"兑换成功");
	}
	

	/*
		兑换房卡列表
		
		参数：
			account_id : account_id
		
		返回结果：
			
		
	*/
	public function exchangeTicketList($arrData)
	{
		$timestamp = time();
		$result = array();

		if(!isset($arrData['account_id']) || $arrData['account_id'] == G_CONST::EMPTY_STRING)
		{
			log_message('error', "function(exchangeTicketList):lack of account_id"." in file".__FILE__." on Line ".__LINE__);
			return $this->missingPrameterArr("code");
		}
		if(!isset($arrData['page']) || $arrData['page'] == G_CONST::EMPTY_STRING)
		{
			log_message('error', "function(exchangeTicketList):lack of page"." in file".__FILE__." on Line ".__LINE__);
			return $this->missingPrameterArr("page");
		}
		if(!isset($arrData['dealer_num']) || $arrData['dealer_num'] == G_CONST::EMPTY_STRING)
        {
            log_message('error', "function(exchangeTicketList):lack of dealer_num"." in file".__FILE__." on Line ".__LINE__);
            return $this->missingPrameterArr("dealer_num");
        }
        $dealer_num = $arrData['dealer_num'];
		$DelaerConst = "Dealer_".$dealer_num;
        $dealerDB = $DelaerConst::DBConst_Name;	
		
		$account_id = $arrData['account_id'];		
		$page = $arrData['page'];
		
		$limit = 20;
		$offset = ($page - 1) * $limit;
		
		$sum_page = 1;

		//判断兑换码是否存在
		$data_where = 'account_id='.$account_id.' and is_delete=0';
		$data_sql = 'select update_time,code,type,ticket_count from '.Exchange_Ticket.' where '.$data_where.' order by update_time desc limit '.$offset.','.$limit;
		$data_query = $this->getDataBySql($dealerDB,0,$data_sql);
		if(DB_CONST::DATA_NONEXISTENT != $data_query)
		{
			$count_sql = 'select count(data_id) as sum_count from '.Exchange_Ticket.' where '.$data_where;
			$count_query = $this->getDataBySql($dealerDB,1,$count_sql);
			$sum_count = $count_query['sum_count'];
			$sum_page = ceil($sum_count/$limit);

			foreach($data_query as $item)
			{
				$array['exchange_time'] = date("m-d H:i",$item['update_time']);
				$array['code'] = $item['code'];
				$array['type'] = $item['type'];
				$array['ticket_count'] = $item['ticket_count'];

				$result[] = $array;
				unset($array);
			}

		}
		return array('result'=>OPT_CONST::SUCCESS,'data'=>$result,'result_message'=>"房卡兑换列表","sum_page"=>$sum_page,"page"=>$page);
	}

	
}