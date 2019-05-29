<?php

include_once 'common_model.php';		//加载数据库操作类
class Dealer_Model extends Dealer_Common_Model
{
	public function __construct()
	{
		header('Content-Type: text/html; charset=utf-8'); 
		parent::__construct();
	}
	
	/************************************************
					common function
	*************************************************/
	protected function getRedEnvelopByCode($code,$dealerDB)
	{
		//获取红包记录
		$redenvelop_where = 'code="'.$code.'"';
		$redenvelop_sql = 'select redenvelop_id,account_id,ticket_count,redenvelop_count,is_receive,content from '.Act_Redenvelop.' where '.$redenvelop_where;
		$redenvelop_query = $this->getDataBySql($dealerDB,1,$redenvelop_sql);

		return $redenvelop_query;
	}
	
	protected function getRedEnvelopByID($redenvelop_id,$dealerDB)
	{
		//获取红包记录
		$redenvelop_where = 'redenvelop_id="'.$redenvelop_id.'"';
		$redenvelop_sql = 'select redenvelop_id,account_id,ticket_count,redenvelop_count,is_receive,content,code from '.Act_Redenvelop.' where '.$redenvelop_where;
		$redenvelop_query = $this->getDataBySql($dealerDB,1,$redenvelop_sql);

		return $redenvelop_query;
	}
	
	
	protected function createRedEnvelopCode($account_id)
	{
		$mtime=explode(' ',microtime());  
		$mTimestamp = $mtime[1] . substr($mtime[0],2,3);
		
		$code = $account_id.':'.$mTimestamp;
		
		for($i=0;$i<4;$i++)
		{
			$code .= rand(0,9);
		}
		
		return md5($code);
	}



	


	/*
		获取代理商信息
		
		参数：
			account_id : account_id
		
		返回结果：
			
		
	*/
	public function getDealerData($arrData)
	{
		$timestamp = time();
		$result = array();
		
		if(!isset($arrData['account_id']) || $arrData['account_id'] == G_CONST::EMPTY_STRING)
		{
			log_message('error', "function(getDealerData):lack of account_id"." in file".__FILE__." on Line ".__LINE__);
			return $this->missingPrameterArr("account_id");
		}
		if(!isset($arrData['dealer_num']) || $arrData['dealer_num'] == G_CONST::EMPTY_STRING)
		{
			log_message('error', "function(getDealerData):lack of dealer_num"." in file".__FILE__." on Line ".__LINE__);
			return $this->missingPrameterArr("dealer_num");
		}

		$dealer_num = $arrData['dealer_num'];
		$account_id = $arrData['account_id'];

		$dealerDB = "dealer_".$dealer_num;


		$bind_sql = 'select dealer_screct from '.D_Bind.' where account_id='.$account_id;
		$bind_query = $this->getDataBySql($dealerDB,1,$bind_sql);
		if(DB_CONST::DATA_NONEXISTENT == $bind_query)
		{
			return array('result'=>OPT_CONST::FAILED,'data'=>$result,'result_message'=>"无代理商信息");
		}
		$dealer_screct = $bind_query['dealer_screct'];


		$inventory_count = 0;
		$account_where = 'dealer_num="'.$dealer_num.'" and is_delete=0';
		$account_sql = 'select inventory_count from '.D_Account.' where '.$account_where.'';
		$account_query = $this->getDataBySql($dealerDB,1,$account_sql);
		if(DB_CONST::DATA_NONEXISTENT != $account_query)
		{
			$inventory_count = $account_query['inventory_count'];
		}

		$result['dealer_screct'] = $dealer_screct;
		$result['inventory_count'] = $inventory_count;

		return array('result'=>OPT_CONST::SUCCESS,'data'=>$result,'result_message'=>"获取代理商信息");
	}




	/*
		生成红包
		
		参数：
			account_id : account_id
			ticket_count : 
		
		返回结果：
			
		
	*/
	public function createRedEnvelopOpt($arrData)
	{
		$timestamp = time();
		$result = array();
		
		if(!isset($arrData['account_id']) || $arrData['account_id'] == G_CONST::EMPTY_STRING)
		{
			log_message('error', "function(createRedEnvelopOpt):lack of account_id"." in file".__FILE__." on Line ".__LINE__);
			return $this->missingPrameterArr("account_id");
		}
		if(!isset($arrData['ticket_count']) || $arrData['ticket_count'] == G_CONST::EMPTY_STRING)
		{
			log_message('error', "function(createRedEnvelopOpt):lack of ticket_count"." in file".__FILE__." on Line ".__LINE__);
			return $this->missingPrameterArr("ticket_count");
		}
		if(!isset($arrData['dealer_num']) || $arrData['dealer_num'] == G_CONST::EMPTY_STRING)
		{
			log_message('error', "function(createRedEnvelopOpt):lack of dealer_num"." in file".__FILE__." on Line ".__LINE__);
			return $this->missingPrameterArr("dealer_num");
		}
		if(!isset($arrData['dealer_screct']) || $arrData['dealer_screct'] == G_CONST::EMPTY_STRING)
		{
			log_message('error', "function(createRedEnvelopOpt):lack of dealer_screct"." in file".__FILE__." on Line ".__LINE__);
			return $this->missingPrameterArr("dealer_screct");
		}
		if(!isset($arrData['content']))
		{
			log_message('error', "function(createRedEnvelopOpt):lack of content"." in file".__FILE__." on Line ".__LINE__);
			return $this->missingPrameterArr("content");
		}
		
		$account_id = $arrData['account_id'];	
		$ticket_count = $arrData['ticket_count'];	
		$content = $arrData['content'];	
		$dealer_num = $arrData['dealer_num'];
		$dealer_screct = $arrData['dealer_screct'];
		
		$DelaerConst = "Dealer_".$dealer_num;
        $dealerDB = $DelaerConst::DBConst_Name;


        $account_where = 'account_id='.$account_id.' and dealer_screct="'.$dealer_screct.'" and is_delete=0';
        $account_sql = 'select bind_id from '.D_Bind.' where '.$account_where;
        $account_query = $this->getDataBySql($dealerDB,1,$account_sql);
        if(DB_CONST::DATA_NONEXISTENT == $account_query)
		{
			return array('result'=>OPT_CONST::FAILED,'data'=>$result,'result_message'=>"无代理商信息");
		}

		//获取用户剩余房卡
		$inventory_count = 0;
		$account_where = 'dealer_num="'.$dealer_num.'" and is_delete=0';
		$account_sql = 'select inventory_count from '.D_Account.' where '.$account_where.'';
		$account_query = $this->getDataBySql($dealerDB,1,$account_sql);
		if(DB_CONST::DATA_NONEXISTENT != $account_query)
		{
			$inventory_count = $account_query['inventory_count'];
		}

		if($ticket_count > $inventory_count)
		{
			return array('result'=>OPT_CONST::FAILED,'data'=>$result,'result_message'=>"房卡不足");
		}
		
		$code = $this->createRedEnvelopCode($account_id);
		
		
		//房卡流水账
		// $journal_ary['journal_type'] = Game_CONST::JournalType_Disburse;
		// $journal_ary['account_id'] = $account_id;
		// $journal_ary['object_type'] = Game_CONST::ObjectType_RedEnvelop;
		// $journal_ary['object_id'] = $redenvelop_id;
		// $journal_ary['ticket_count'] = $ticket_count;
		// $journal_ary['extra'] = "";
		// $this->updateRoomTicketJournal($journal_ary,$dealerDB);
		
		//添加红包记录
		$insert_array['create_time'] = $timestamp;
		$insert_array['create_appid'] = "aid_".$account_id;
		$insert_array['update_time'] = $timestamp;
		$insert_array['update_appid'] = "aid_".$account_id;
		$insert_array['is_delete'] = G_CONST::IS_FALSE;
		$insert_array['account_id'] = $account_id;
		$insert_array['ticket_count'] = $ticket_count;
		$insert_array['redenvelop_count'] = 1;
		$insert_array['code'] = $code;
		$insert_array['content'] = $content;
		$insert_array['type'] = Game_CONST::RedenvelopType_Dealer;

		$redenvelop_id = $this->getInsertID($dealerDB,Act_Redenvelop, $insert_array);

		//房卡库存流水账
		$i_journal_ary['journal_type'] = Game_CONST::JournalType_Disburse;
		$i_journal_ary['account_id'] = $account_id;
		$i_journal_ary['object_type'] = Game_CONST::DObjectType_RedEnvelop;
		$i_journal_ary['object_id'] = $redenvelop_id;
		$i_journal_ary['ticket_count'] = $ticket_count;
		$i_journal_ary['extra'] = "";
		$this->updateInventoryJournal($i_journal_ary,$dealerDB);

		//减少自己账户上的房卡
		$updateTicket_str = 'update_time='.$timestamp.',update_appid="aid_'.$account_id.'",inventory_count=inventory_count-'.$ticket_count;
		$updateTicket_where = 'dealer_num="'.$dealer_num.'"';
		$updateTicket_query = $this->changeNodeValue($dealerDB,D_Account,$updateTicket_str,$updateTicket_where);

		

		
		$result['code'] = $code;
		
		return array('result'=>OPT_CONST::SUCCESS,'data'=>$result,'result_message'=>"生成红包成功");
	}	



	/*
		我的红包列表
		
		参数：
			account_id : account_id
		
		返回结果：
			
		
	*/
	public function getRedEnvelopList($arrData)
	{
		$timestamp = time();
		$result = array();
		
		if(!isset($arrData['account_id']) || $arrData['account_id'] == G_CONST::EMPTY_STRING)
		{
			log_message('error', "function(getRedEnvelopList):lack of account_id"." in file".__FILE__." on Line ".__LINE__);
			return $this->missingPrameterArr("code");
		}
		if(!isset($arrData['page']) || $arrData['page'] == G_CONST::EMPTY_STRING)
		{
			log_message('error', "function(getRedEnvelopList):lack of page"." in file".__FILE__." on Line ".__LINE__);
			return $this->missingPrameterArr("page");
		}
		if(!isset($arrData['dealer_num']) || $arrData['dealer_num'] == G_CONST::EMPTY_STRING)
        {
            log_message('error', "function(getRedEnvelopList):lack of dealer_num"." in file".__FILE__." on Line ".__LINE__);
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
		
		//获取红包记录
		$redenvelop_where = 'account_id="'.$account_id.'" and type='.Game_CONST::RedenvelopType_Dealer;
		$redenvelop_sql = 'select create_time,redenvelop_id,account_id,ticket_count,redenvelop_count,is_receive,code,is_return from '.Act_Redenvelop.' where '.$redenvelop_where.' order by create_time desc limit '.$offset.','.$limit;
		$redenvelop_query = $this->getDataBySql($dealerDB,0,$redenvelop_sql);
		if(DB_CONST::DATA_NONEXISTENT != $redenvelop_query)
		{
			$count_sql = 'select count(redenvelop_id) as sum_count from '.Act_Redenvelop.' where '.$redenvelop_where;
			$count_query = $this->getDataBySql($dealerDB,1,$count_sql);
			$sum_count = $count_query['sum_count'];
			$sum_page = ceil($sum_count/$limit);
			
			foreach($redenvelop_query as $item)
			{
				$redenvelop_id = $item['redenvelop_id'];
				$array['ticket_count'] = $item['ticket_count'];
				$array['create_time'] = $item['create_time'];
				$array['is_receive'] = $item['is_receive'];
				$array['code'] = $item['code'];
				$array['is_return'] = $item['is_return'];
				
				$array['receive_nickname'] = G_CONST::EMPTY_STRING;
				$array['receive_headimgurl'] = G_CONST::EMPTY_STRING;
				$array['receive_time'] = G_CONST::EMPTY_STRING;
				
				if($item['is_receive'] == G_CONST::IS_TRUE)
				{
					$receive_sql = 'select account_id,create_time from '.Act_RedenvelopReceive.' where redenvelop_id='.$redenvelop_id.' and is_delete=0';
					$receive_query = $this->getDataBySql($dealerDB,1,$receive_sql);
					
					$receive_aid = $receive_query['account_id'];
					$array['receive_time'] = $receive_query['create_time'];
					
					$account_where = 'account_id='.$account_id;
					$account_sql = 'select account_id,nickname,headimgurl from '.WX_Account.' where '.$account_where.'';
					$account_query = $this->getDataBySql($dealerDB,1,$account_sql);
					
					$array['receive_nickname'] = $account_query['nickname'];
					$array['receive_headimgurl'] = $account_query['headimgurl'];
				}
				
				$result[] = $array;
			}
		}
		
		return array('result'=>OPT_CONST::SUCCESS,'data'=>$result,'result_message'=>"代理商的红包列表","sum_page"=>$sum_page);
	}




	/*
		获取游戏ip和port
	*/
	public function getGameSocketInfo($arrData)
	{
		$timestamp = time();
		$result = array();
		
		if(!isset($arrData['dealer_num']) || $arrData['dealer_num'] == G_CONST::EMPTY_STRING)
		{
			log_message('error', "function(getRedEnvelopList):lack of dealer_num"." in file".__FILE__." on Line ".__LINE__);
			return $this->missingPrameterArr("dealer_num");
		}
		if(!isset($arrData['game_type']) || $arrData['game_type'] == G_CONST::EMPTY_STRING)
		{
			log_message('error', "function(getRedEnvelopList):lack of game_type"." in file".__FILE__." on Line ".__LINE__);
			return $this->missingPrameterArr("game_type");
		}
		$dealer_num = $arrData['dealer_num'];
		$DelaerConst = "Dealer_".$dealer_num;
        $dealerDB = $DelaerConst::DBConst_Name;	
		
		$game_type = $arrData['game_type'];

		$ip_host = "";
		$ip_port = "-1";

		$data_sql = 'select ip_host,ip_port from '.Game_List.' where game_type='.$game_type.' and is_delete=0';
		$data_query = $this->getDataBySql($dealerDB,1,$data_sql);
		if(DB_CONST::DATA_NONEXISTENT != $data_query)
		{
			$ip_host = $data_query['ip_host'];
			$ip_port = $data_query['ip_port'];
		}

		$result['ip_host'] = $ip_host;
		$result['ip_port'] = $ip_port;

		return array('result'=>OPT_CONST::SUCCESS,'data'=>$result,'result_message'=>"获取游戏ip和port");
	}



	/*
		获取游戏ip和port
	*/
	public function getGameWssSocket($arrData)
	{
		$timestamp = time();
		$result = new stdClass();
		
		if(!isset($arrData['dealer_num']) || $arrData['dealer_num'] == G_CONST::EMPTY_STRING)
		{
			log_message('error', "function(getGameWssSocket):lack of dealer_num"." in file".__FILE__." on Line ".__LINE__);
			return $this->missingPrameterArr("dealer_num");
		}
		if(!isset($arrData['game_type']) || $arrData['game_type'] === G_CONST::EMPTY_STRING)
		{
			log_message('error', "function(getGameWssSocket):lack of game_type"." in file".__FILE__." on Line ".__LINE__);
			return $this->missingPrameterArr("game_type");
		}
		$dealer_num = $arrData['dealer_num'];
		$DelaerConst = "Dealer_".$dealer_num;
        $dealerDB = $DelaerConst::DBConst_Name;	

		$game_type = $arrData['game_type'];

		$wss_array = array();

		$data_sql = 'select game_type,domain_host,domain_port from '.Game_List.' where is_delete=0';
		if($game_type > 0)
		{
			$data_sql .= ' and game_type='.$game_type;
		}
		$data_query = $this->getDataBySql($dealerDB,0,$data_sql);
		if(DB_CONST::DATA_NONEXISTENT != $data_query)
		{
			foreach($data_query as $data_item)
			{
				$wss_array[$data_item['game_type']] = $data_item['domain_host'].":".$data_item['domain_port'];
			}
			$result = $wss_array;

			return array('result'=>OPT_CONST::SUCCESS,'data'=>$result,'result_message'=>"获取游戏socket");
		}
		else
		{
			log_message('error', "function(getGameWssSocket):lack of dealer_num"." in file".__FILE__." on Line ".__LINE__);
			return array('result'=>OPT_CONST::FAILED,'data'=>$result,'result_message'=>"无游戏列表");
		}
		
	}

	
}