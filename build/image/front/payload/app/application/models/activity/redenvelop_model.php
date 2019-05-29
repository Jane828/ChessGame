<?php

include_once 'common_model.php';		//加载数据库操作类
class RedEnvelop_Model extends Activity_Common_Model
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
		$redenvelop_sql = 'select redenvelop_id,account_id,ticket_count,redenvelop_count,is_receive,content,is_return from '.Act_Redenvelop.' where '.$redenvelop_where;
		$redenvelop_query = $this->getDataBySql($dealerDB,1,$redenvelop_sql);

		return $redenvelop_query;
	}
	
	protected function getRedEnvelopByID($redenvelop_id,$dealerDB)
	{
		//获取红包记录
		$redenvelop_where = 'redenvelop_id="'.$redenvelop_id.'"';
		$redenvelop_sql = 'select redenvelop_id,account_id,ticket_count,redenvelop_count,is_receive,content,code,is_return from '.Act_Redenvelop.' where '.$redenvelop_where;
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
	
	
	
	/************************************************
					logic function
	*************************************************/
	

	/*
		获取红包信息
		
		参数：
			code : 红包信息
		
		返回结果：
			
		
	*/
	public function getRedEnvelopData($arrData)
	{
		$timestamp = time();
		$result = array();

		if(!isset($arrData['code']) || $arrData['code'] == G_CONST::EMPTY_STRING)
		{
			log_message('error', "function(getRedEnvelopData):lack of code"." in file".__FILE__." on Line ".__LINE__);
			return $this->missingPrameterArr("code");
		}
		$code = $arrData['code'];	
        $dealerDB = Game_CONST::DBConst_Name;
		
		//获取红包记录
		$redenvelop_query = $this->getRedEnvelopByCode($code,$dealerDB);
		if(DB_CONST::DATA_NONEXISTENT == $redenvelop_query)
		{
			return array('result'=>OPT_CONST::FAILED,'data'=>$result,'result_message'=>"红包不存在");
		}

		$account_id = $redenvelop_query['account_id'];
		$redenvelop_id = $redenvelop_query['redenvelop_id'];
		$is_receive = $redenvelop_query['is_receive'];
		
		$account_where = 'account_id='.$account_id;
		$account_sql = 'select account_id,nickname,headimgurl from '.WX_Account.' where '.$account_where.'';
		$account_query = $this->getDataBySql($dealerDB,1,$account_sql);
		if(DB_CONST::DATA_NONEXISTENT == $account_query)
		{
			return array('result'=>OPT_CONST::FAILED,'data'=>$result,'result_message'=>"用户不存在");
		}
		
		//$result['redenvelop_id'] = $redenvelop_id;
		$result['is_receive'] = $is_receive;
		$result['content'] = $redenvelop_query['content'];
		$result['ticket_count'] = $redenvelop_query['ticket_count'];
		$result['nickname'] = $account_query['nickname'];
		$result['headimgurl'] = $account_query['headimgurl'];
		
		$result['receive_nickname'] = G_CONST::EMPTY_STRING;
		$result['receive_headimgurl'] = G_CONST::EMPTY_STRING;
		$result['receive_time'] = G_CONST::EMPTY_STRING;
			
		if($is_receive == G_CONST::IS_TRUE)
		{
			$receive_sql = 'select account_id,create_time from '.Act_RedenvelopReceive.' where redenvelop_id='.$redenvelop_id.' and is_delete=0';
			$receive_query = $this->getDataBySql($dealerDB,1,$receive_sql);
			
			$receive_aid = $receive_query['account_id'];
			$result['receive_time'] = $receive_query['create_time'];
			
			$account_where = 'account_id='.$receive_aid;
			$account_sql = 'select account_id,nickname,headimgurl from '.WX_Account.' where '.$account_where.'';
			$account_query = $this->getDataBySql($dealerDB,1,$account_sql);
			
			$result['receive_nickname'] = $account_query['nickname'];
			$result['receive_headimgurl'] = $account_query['headimgurl'];
			
			return array('result'=>OPT_CONST::SUCCESS,'data'=>$result,'result_message'=>"红包已被领取");
		}
		else
		{
			return array('result'=>OPT_CONST::SUCCESS,'data'=>$result,'result_message'=>"点击领取红包");
		}
	}
	
	
	
	/*
		获取红包信息
		
		参数：
			code : 红包信息
		
		返回结果：
			
		
	*/
	public function receiveRedEnvelopOpt($arrData)
	{
		$timestamp = time();
		$result = array();
		
		if(!isset($arrData['account_id']) || $arrData['account_id'] == G_CONST::EMPTY_STRING)
		{
			log_message('error', "function(receiveRedEnvelopOpt):lack of account_id"." in file".__FILE__." on Line ".__LINE__);
			return $this->missingPrameterArr("code");
		}
		if(!isset($arrData['code']) || $arrData['code'] == G_CONST::EMPTY_STRING)
		{
			log_message('error', "function(receiveRedEnvelopOpt):lack of code"." in file".__FILE__." on Line ".__LINE__);
			return $this->missingPrameterArr("code");
		}

		$account_id = $arrData['account_id'];	
		$code = $arrData['code'];

        $dealerDB = Game_CONST::DBConst_Name;

        // 开启事务，行锁
        $db_obj = $this->db();
        $db_obj->trans_strict(false);
        $db_obj->trans_start();

        //获取红包记录
        $redenvelop_sql = 'select redenvelop_id,ticket_count,is_receive from '.Act_Redenvelop." where code='{$code}' for update";
        $redenvelop_query = $this->getDataBySql($dealerDB,1,$redenvelop_sql);
		if(DB_CONST::DATA_NONEXISTENT == $redenvelop_query)
		{
			return array('result'=>OPT_CONST::FAILED,'data'=>$result,'result_message'=>"红包不存在");
		}
		
		$redenvelop_id = $redenvelop_query['redenvelop_id'];
		$ticket_count = $redenvelop_query['ticket_count'];
		$is_receive = $redenvelop_query['is_receive'];
		
		if($is_receive == G_CONST::IS_TRUE)
		{
			return array('result'=>OPT_CONST::FAILED,'data'=>$result,'result_message'=>"红包已被领取");
		}
		

		//更新红包领取状态
		$updateRE_str = 'update_time='.$timestamp.',update_appid="aid_'.$account_id.'",is_receive='.G_CONST::IS_TRUE;
		$updateRE_where = 'redenvelop_id='.$redenvelop_id;
		$updateRE_query = $this->changeNodeValue($dealerDB,Act_Redenvelop,$updateRE_str,$updateRE_where);
		
		//添加领取记录
		$insert_array['create_time'] = $timestamp;
		$insert_array['create_appid'] = "aid_".$account_id;
		$insert_array['update_time'] = $timestamp;
		$insert_array['update_appid'] = "aid_".$account_id;
		$insert_array['is_delete'] = G_CONST::IS_FALSE;
		$insert_array['redenvelop_id'] = $redenvelop_id;
		$insert_array['account_id'] = $account_id;
		$insert_array['ticket_count'] = $ticket_count;
		$insert_array['redenvelop_count'] = 1;
		
		$receive_id = $this->getInsertID($dealerDB,Act_RedenvelopReceive, $insert_array);
		
		//将房卡添加到自己账户
		$updateTicket_str = 'update_time='.$timestamp.',update_appid="aid_'.$account_id.'",ticket_count=ticket_count+'.$ticket_count;
		$updateTicket_where = 'account_id='.$account_id;
		$updateTicket_query = $this->changeNodeValue($dealerDB,Room_Ticket,$updateTicket_str,$updateTicket_where);

		//房卡流水账
		$journal_ary['journal_type'] = Game_CONST::JournalType_Income;
		$journal_ary['account_id'] = $account_id;
		$journal_ary['object_type'] = Game_CONST::ObjectType_RedEnvelop;
		$journal_ary['object_id'] = $receive_id;
		$journal_ary['ticket_count'] = $ticket_count;
		$journal_ary['extra'] = "";
		$this->updateRoomTicketJournal($journal_ary,$dealerDB);

        $db_obj->trans_complete();

        return array('result'=>OPT_CONST::SUCCESS,'data'=>$result,'result_message'=>"领取成功");
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
			return $this->missingPrameterArr("code");
		}
		if(!isset($arrData['ticket_count']) || $arrData['ticket_count'] == G_CONST::EMPTY_STRING)
		{
			log_message('error', "function(createRedEnvelopOpt):lack of code"." in file".__FILE__." on Line ".__LINE__);
			return $this->missingPrameterArr("code");
		}
	
		if(!isset($arrData['content']))
		{
			log_message('error', "function(createRedEnvelopOpt):lack of content"." in file".__FILE__." on Line ".__LINE__);
			return $this->missingPrameterArr("content");
		}
		
		$account_id = $arrData['account_id'];	
		$ticket_count = $arrData['ticket_count'];	
		$content = $arrData['content'];	
		
        $dealerDB = Game_CONST::DBConst_Name;
        
        if($ticket_count <= 0)
        {
	        return array('result'=>OPT_CONST::FAILED,'data'=>$result,'result_message'=>"房卡数错误");
        }

		//获取用户剩余房卡
		$ticket_where = 'account_id='.$account_id.' and is_delete=0';
		$ticket_sql = 'select ticket_count from '.Room_Ticket.' where '.$ticket_where;
		$ticket_query = $this->getDataBySql($dealerDB,1,$ticket_sql);
		if(DB_CONST::DATA_NONEXISTENT == $ticket_query)
		{
			return array('result'=>OPT_CONST::FAILED,'data'=>$result,'result_message'=>"房卡不足");
		}
		
		if($ticket_count > $ticket_query['ticket_count'])
		{
			return array('result'=>OPT_CONST::FAILED,'data'=>$result,'result_message'=>"房卡不足");
		}
		
		$code = $this->createRedEnvelopCode($account_id);
		
		//减少自己账户上的房卡
		$updateTicket_str = 'update_time='.$timestamp.',update_appid="aid_'.$account_id.'",ticket_count=ticket_count-'.$ticket_count;
		$updateTicket_where = 'account_id='.$account_id;
		$updateTicket_query = $this->changeNodeValue($dealerDB,Room_Ticket,$updateTicket_str,$updateTicket_where);
		
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
		$insert_array['type'] = Game_CONST::RedenvelopType_User;
		$redenvelop_id = $this->getInsertID($dealerDB,Act_Redenvelop, $insert_array);
		
		
		//房卡流水账
		$journal_ary['journal_type'] = Game_CONST::JournalType_Disburse;
		$journal_ary['account_id'] = $account_id;
		$journal_ary['object_type'] = Game_CONST::ObjectType_RedEnvelop;
		$journal_ary['object_id'] = $redenvelop_id;
		$journal_ary['ticket_count'] = $ticket_count;
		$journal_ary['extra'] = "";
		$this->updateRoomTicketJournal($journal_ary,$dealerDB);

		
		$result['code'] = $code;
		
		return array('result'=>OPT_CONST::SUCCESS,'data'=>$result,'result_message'=>"生成红包成功");
	}	
		
		
		
	/*
		我的红包列表
		
		参数：
			account_id : account_id
		
		返回结果：
			
		
	*/
	public function getSendRedList($arrData)
	{
		$timestamp = time();
		$result = array();
		
		if(!isset($arrData['account_id']) || $arrData['account_id'] == G_CONST::EMPTY_STRING)
		{
			log_message('error', "function(getSendRedList):lack of account_id"." in file".__FILE__." on Line ".__LINE__);
			return $this->missingPrameterArr("code");
		}
		if(!isset($arrData['page']) || $arrData['page'] == G_CONST::EMPTY_STRING)
		{
			log_message('error', "function(getSendRedList):lack of page"." in file".__FILE__." on Line ".__LINE__);
			return $this->missingPrameterArr("page");
		}
		
        $dealerDB = Game_CONST::DBConst_Name;	
		
		$account_id = $arrData['account_id'];		
		$page = $arrData['page'];
		
		$limit = 20;
		$offset = ($page - 1) * $limit;
		
		$sum_page = 1;
		
		//获取红包记录
		$redenvelop_where = 'account_id="'.$account_id.'" and type='.Game_CONST::RedenvelopType_User;
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
		
		return array('result'=>OPT_CONST::SUCCESS,'data'=>$result,'result_message'=>"我的红包列表","sum_page"=>$sum_page);
	}
	
	
	
	
	/*
		我收取的红包列表
		
		参数：
			account_id : account_id
		
		返回结果：
			
		
	*/
	public function getReceiveRedList($arrData)
	{
		$timestamp = time();
		$result = array();
		
		if(!isset($arrData['account_id']) || $arrData['account_id'] == G_CONST::EMPTY_STRING)
		{
			log_message('error', "function(getReceiveRedList):lack of account_id"." in file".__FILE__." on Line ".__LINE__);
			return $this->missingPrameterArr("code");
		}
		if(!isset($arrData['page']) || $arrData['page'] == G_CONST::EMPTY_STRING)
		{
			log_message('error', "function(getReceiveRedList):lack of page"." in file".__FILE__." on Line ".__LINE__);
			return $this->missingPrameterArr("page");
		}

        $dealerDB = Game_CONST::DBConst_Name;	

		$account_id = $arrData['account_id'];		
		$page = $arrData['page'];
		
		$limit = 20;
		$offset = ($page - 1) * $limit;
		
		$sum_page = 1;
		
		
		//接收记录
		$receive_where = 'account_id="'.$account_id.'" and is_delete=0';
		$receive_sql = 'select receive_id,redenvelop_id,create_time,ticket_count from '.Act_RedenvelopReceive.' where '.$receive_where.' order by create_time desc limit '.$offset.','.$limit;
		$receive_query = $this->getDataBySql($dealerDB,0,$receive_sql);
		if(DB_CONST::DATA_NONEXISTENT != $receive_query)
		{
			$count_sql = 'select count(redenvelop_id) as sum_count from '.Act_RedenvelopReceive.' where '.$receive_where;
			$count_query = $this->getDataBySql($dealerDB,1,$count_sql);
			$sum_count = $count_query['sum_count'];
			$sum_page = ceil($sum_count/$limit);
			
			foreach($receive_query as $item)
			{
				$redenvelop_id = $item['redenvelop_id'];
				
				//获取红包记录
				$redenvelop_query = $this->getRedEnvelopByID($redenvelop_id,$dealerDB);
				if(DB_CONST::DATA_NONEXISTENT == $redenvelop_query)
				{
					continue;
				}
				
				$redenvelop_aid = $redenvelop_query['account_id'];
				
				$account_where = 'account_id='.$redenvelop_aid;
				$account_sql = 'select account_id,nickname,headimgurl from '.WX_Account.' where '.$account_where.'';
				$account_query = $this->getDataBySql($dealerDB,1,$account_sql);
				
				$array['receive_id'] = $item['receive_id'];
				$array['receive_time'] = $item['create_time'];
				$array['ticket_count'] = $item['ticket_count'];
				
				$array['code'] = $redenvelop_query['code'];
				$array['nickname'] = $account_query['nickname'];
				
				$result[] = $array;
				
			}
		}
		
		
		return array('result'=>OPT_CONST::SUCCESS,'data'=>$result,'result_message'=>"收取的红包列表","sum_page"=>$sum_page);
	}



	/*
		查询退款红包
		
		参数：
		
		返回结果：

	*/
	public function searchRefundRedenvelop($arrData)
	{
		$timestamp = time();
		$result = array();
		
		if(!isset($arrData['dealer_num']) || $arrData['dealer_num'] == G_CONST::EMPTY_STRING)
        {
            log_message('error', "function(searchRefundRedenvelop):lack of dealer_num"." in file".__FILE__." on Line ".__LINE__);
            return $this->missingPrameterArr("dealer_num");
        }


        $dealer_num = $arrData['dealer_num'];
		$DelaerConst = "Dealer_".$dealer_num;
        $dealerDB = $DelaerConst::DBConst_Name;	

        $expire_timestamp = $timestamp - 86400;
        //$expire_timestamp = $timestamp - 60;

        //获取红包记录
		$redenvelop_where = 'create_time<='.$expire_timestamp.' and is_delete=0 and is_receive=0 and is_return=0';
		$redenvelop_sql = 'select redenvelop_id,type,account_id,ticket_count from '.Act_Redenvelop.' where '.$redenvelop_where;
		$redenvelop_query = $this->getDataBySql($dealerDB,0,$redenvelop_sql);

		if(DB_CONST::DATA_NONEXISTENT == $redenvelop_query)
		{
			log_message('error', "function(searchRefundRedenvelop):dealer_num:".$dealer_num." count:0 in file".__FILE__." on Line ".__LINE__);
			return 0;
		}

		log_message('error', "function(searchRefundRedenvelop):dealer_num:".$dealer_num." count:".count($redenvelop_query)." in file".__FILE__." on Line ".__LINE__);

		foreach($redenvelop_query as $redenvelop)
		{
			$type = $redenvelop['type'];
			$redenvelop_id = $redenvelop['redenvelop_id'];
			$account_id = $redenvelop['account_id'];
			$ticket_count = $redenvelop['ticket_count'];

			if($type == Game_CONST::RedenvelopType_User)
			{
				//退还给用户
				//将房卡添加到自己账户
				$updateTicket_str = 'update_time='.$timestamp.',update_appid="aid_'.$account_id.'",ticket_count=ticket_count+'.$ticket_count;
				$updateTicket_where = 'account_id='.$account_id;
				$updateTicket_query = $this->changeNodeValue($dealerDB,Room_Ticket,$updateTicket_str,$updateTicket_where);


				//房卡流水账
				$journal_ary['journal_type'] = Game_CONST::JournalType_Income;
				$journal_ary['account_id'] = $account_id;
				$journal_ary['object_type'] = Game_CONST::ObjectType_RedEnvelop;
				$journal_ary['object_id'] = $redenvelop_id;
				$journal_ary['ticket_count'] = $ticket_count;
				$journal_ary['extra'] = "过期红包退回";
				$this->updateRoomTicketJournal($journal_ary,$dealerDB);
			}
			else if($type == Game_CONST::RedenvelopType_Dealer)
			{
				//查看用户代理商
				//减少自己账户上的房卡
				$updateTicket_str = 'update_time='.$timestamp.',update_appid="aid_'.$account_id.'",inventory_count=inventory_count+'.$ticket_count;
				$updateTicket_where = 'dealer_num="'.$dealer_num.'"';
				$updateTicket_query = $this->changeNodeValue($dealerDB,D_Account,$updateTicket_str,$updateTicket_where);

				//房卡库存流水账
				$i_journal_ary['journal_type'] = Game_CONST::JournalType_Income;
				$i_journal_ary['account_id'] = $account_id;
				$i_journal_ary['object_type'] = Game_CONST::DObjectType_RedEnvelop;
				$i_journal_ary['object_id'] = $redenvelop_id;
				$i_journal_ary['ticket_count'] = $ticket_count;
				$i_journal_ary['extra'] = "过期红包退回";
				$this->updateInventoryJournal($i_journal_ary,$dealerDB);

			}
			else
			{
				continue;
			}


			//添加领取记录
			$insert_array['create_time'] = $timestamp;
			$insert_array['create_appid'] = "aid_".$account_id;
			$insert_array['update_time'] = $timestamp;
			$insert_array['update_appid'] = "aid_".$account_id;
			$insert_array['is_delete'] = G_CONST::IS_FALSE;
			$insert_array['redenvelop_id'] = $redenvelop_id;
			$insert_array['account_id'] = $account_id;
			$insert_array['ticket_count'] = $ticket_count;
			$insert_array['redenvelop_count'] = 1;
			
			$receive_id = $this->getInsertID($dealerDB,Act_RedenvelopReceive, $insert_array);

			//更新红包领取状态
			$updateRE_str = 'update_time='.$timestamp.',update_appid="aid_'.$account_id.'",is_receive='.G_CONST::IS_TRUE.',is_return='.G_CONST::IS_TRUE;
			$updateRE_where = 'redenvelop_id='.$redenvelop_id;
			$updateRE_query = $this->changeNodeValue($dealerDB,Act_Redenvelop,$updateRE_str,$updateRE_where);
		}


		return count($redenvelop_query);
    }


	
}