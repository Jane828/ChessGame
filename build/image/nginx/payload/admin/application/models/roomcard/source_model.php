<?php

include_once __DIR__.'/common_model.php';		//加载数据库操作类
class Source_Model extends Roomcard_Common_Model
{
	public function __construct()
	{
		header('Content-Type: text/html; charset=utf-8'); 
		parent::__construct();
	}


	/*
		查询用户房卡记录
	*/
	public function searchAccountRoomCard($arrData)
	{
		$result = array();
		$timestamp = time();

		if(!isset($arrData['dealer_num']) || $arrData['dealer_num'] === "" )
		{
			log_message('error', "function(searchAccountRoomCard):lack of dealer_num"." in file".__FILE__." on Line ".__LINE__);
			return $this->missingPrameterArr("dealer_num");
		}
		if(!isset($arrData['page']))
		{
			log_message('error', "function(searchAccountRoomCard):lack of page"." in file".__FILE__." on Line ".__LINE__);
			return $this->missingPrameterArr("page");
		}
		if(!isset($arrData['keyword']))
		{
			log_message('error', "function(searchAccountRoomCard):lack of keyword"." in file".__FILE__." on Line ".__LINE__);
			return $this->missingPrameterArr("keyword");
		}

		if($arrData['dealer_num'] == "-1")
		{
			return array('result'=>OPT_CONST::FAILED,'data'=>$result,'result_message'=>"代理商尚未部署成功");
		}
		$dealer_num = $arrData['dealer_num'];
		$DelaerConst = "Dealer_".$dealer_num;
		if($dealer_num == "-1" || !class_exists($DelaerConst))
		{
			return array('result'=>OPT_CONST::FAILED,'data'=>$result,'result_message'=>"代理商尚未部署成功");
		}
        $dealerDB = $DelaerConst::DBConst_Name;	

		$keyword = $arrData['keyword'];
		$page = $arrData['page'];
		if($page == 0 || $page == "")
		{
			$page = 1;
		}

		$limit = 10;
		$offset = ($page - 1) * $limit;
		$sum_page = 1;

		$account_where = 'is_delete=0';
		if($keyword != "")
		{
			$account_where .= ' and nickname like "%'.$keyword.'%"';
		}
		
		$account_sql = 'select account_id,nickname,headimgurl from '.WX_Account.' where '.$account_where.' order by create_time desc limit '.$offset.','.$limit;
		$account_query = $this->getDataBySql($dealerDB,0,$account_sql);
		if($account_query != DB_CONST::DATA_NONEXISTENT)
		{
			$count_sql = 'select count(account_id) as count from '.WX_Account.' where '.$account_where;
			$count_query = $this->getDataBySql($dealerDB,1,$count_sql);
			$sum_page = ceil($count_query['count'] / $limit);

			foreach($account_query as $account_item)
			{
				$account_id = $account_item['account_id'];
				$nickname = $account_item['nickname'];
				$headimgurl = $account_item['headimgurl'];

				$ticket_count = 0;
				//获取房卡数
				$ticket_sql = 'select ticket_count from '.Room_Ticket.' where account_id='.$account_id;
				$ticket_query = $this->getDataBySql($dealerDB,1,$ticket_sql);
				if($ticket_query != DB_CONST::DATA_NONEXISTENT)
				{
					$ticket_count = $ticket_query['ticket_count'];
				}

				$array['account_id'] = $account_item['account_id'];
				$array['nickname'] = $account_item['nickname'];
				$array['headimgurl'] = $account_item['headimgurl'];
				$array['ticket_count'] = $ticket_count;

				//判断是否绑定直营代理
				$array['is_agent'] = 0;
				$bind_sql = 'select data_id from '.Agent_Bind.' where account_id='.$array['account_id'].' and is_delete=0';
				$bind_query = $this->getDataBySql($dealerDB,1,$bind_sql);
				if($bind_query != DB_CONST::DATA_NONEXISTENT)
				{
					$array['is_agent'] = 1;
				}


				$result[] = $array;
			}	
		}

		return array('result'=>OPT_CONST::SUCCESS,'data'=>$result,'result_message'=>"获取用户明细",'sum_page'=>$sum_page);
	}


	/*
		扣除用户房卡
	*/
	public function deductAccountRoomCard($arrData)
	{
		$result = array();
		$timestamp = time();

		if(!isset($arrData['dealer_id']) || $arrData['dealer_id'] === "" )
		{
			log_message('error', "function(deductAccountRoomCard):lack of dealer_id"." in file".__FILE__." on Line ".__LINE__);
			return $this->missingPrameterArr("dealer_id");
		}
		if(!isset($arrData['dealer_num']) || $arrData['dealer_num'] === "" )
		{
			log_message('error', "function(deductAccountRoomCard):lack of dealer_num"." in file".__FILE__." on Line ".__LINE__);
			return $this->missingPrameterArr("dealer_num");
		}
		if(!isset($arrData['account_id']) || $arrData['account_id'] === "" )
		{
			log_message('error', "function(deductAccountRoomCard):lack of account_id"." in file".__FILE__." on Line ".__LINE__);
			return $this->missingPrameterArr("account_id");
		}
		if(!isset($arrData['count']) || $arrData['count'] === "" )
		{
			log_message('error', "function(deductAccountRoomCard):lack of count"." in file".__FILE__." on Line ".__LINE__);
			return $this->missingPrameterArr("count");
		}

		if($arrData['dealer_num'] == "-1")
		{
			return array('result'=>OPT_CONST::FAILED,'data'=>$result,'result_message'=>"代理商尚未部署成功");
		}
		$dealer_num = $arrData['dealer_num'];
		$DelaerConst = "Dealer_".$dealer_num;
		if($dealer_num == "-1" || !class_exists($DelaerConst))
		{
			return array('result'=>OPT_CONST::FAILED,'data'=>$result,'result_message'=>"代理商尚未部署成功");
		}
        $dealerDB = $DelaerConst::DBConst_Name;	

		$dealer_id = $arrData['dealer_id'];
		$account_id = $arrData['account_id'];
		$adjust_count = $arrData['count'];
		if($adjust_count <= 0)
		{
			return array('result'=>OPT_CONST::FAILED,'data'=>$result,'result_message'=>"扣除数额必须大于0");
		}

		//原有房卡
		$origin_count = 0;
		//获取房卡数
		$ticket_sql = 'select ticket_count from '.Room_Ticket.' where account_id='.$account_id;
		$ticket_query = $this->getDataBySql($dealerDB,1,$ticket_sql);
		if($ticket_query != DB_CONST::DATA_NONEXISTENT)
		{
			$origin_count = $ticket_query['ticket_count'];
		}
		if($adjust_count >= $origin_count)
		{
			$adjust_count = $origin_count;
		}
		//调整后剩余房卡
		$balance_count = $origin_count - $adjust_count;

		if($origin_count > 0)
		{
			//添加扣除记录
			$adjust_array['create_time'] = $timestamp;
			$adjust_array['create_appid'] = "dealerid_".$dealer_id;
			$adjust_array['update_time'] = $timestamp;
			$adjust_array['update_appid'] = "dealerid_".$dealer_id;
			$adjust_array['is_delete'] = G_CONST::IS_FALSE;
			$adjust_array['account_id'] = $account_id;
			$adjust_array['origin_count'] = $origin_count;
			$adjust_array['adjust_count'] = $adjust_count;
			$adjust_array['balance_count'] = $balance_count;
			$data_id = $this->getInsertID($dealerDB,Room_TicketAdjustment, $adjust_array);

			//减少房卡
			$update_str = 'update_time='.$timestamp.',update_appid="dealerid_'.$dealer_id.'",ticket_count=ticket_count-'.$adjust_count;
			$update_where = 'account_id='.$account_id;
			$update_query = $this->changeNodeValue($dealerDB,Room_Ticket,$update_str,$update_where);

			//添加房卡流水
			$jounal_array['journal_type'] = Game_CONST::JournalType_Disburse;
			$jounal_array['account_id'] = $account_id;
			$jounal_array['object_type'] = Game_CONST::ObjectType_Adjustment;
			$jounal_array['object_id'] = $data_id;
			$jounal_array['ticket_count'] = $adjust_count;
			$jounal_array['extra'] = "";
			$this->updateRoomTicketJournal($jounal_array,$dealerDB);
		}

		return array('result'=>OPT_CONST::SUCCESS,'data'=>$result,'result_message'=>"扣除房卡成功");
	}


	/*
		查询房卡来源
	*/
	public function getRoomCardSource($arrData)
	{
		$result = array();
		$timestamp = time();

		if(!isset($arrData['dealer_num']) || $arrData['dealer_num'] === "" )
		{
			log_message('error', "function(getRoomCardSource):lack of dealer_num"." in file".__FILE__." on Line ".__LINE__);
			return $this->missingPrameterArr("dealer_num");
		}
		if(!isset($arrData['account_id']))
		{
			log_message('error', "function(getRoomCardSource):lack of account_id"." in file".__FILE__." on Line ".__LINE__);
			return $this->missingPrameterArr("account_id");
		}

		 if(!isset($arrData['from'])|| $arrData['from'] === "")
		 {
		 	log_message('error', "function(getRoomCardSource):lack of from"." in file".__FILE__." on Line ".__LINE__);
		 	return $this->missingPrameterArr("from");
		 }
		 if(!isset($arrData['to'])|| $arrData['to'] === "")
		 {
		 	log_message('error', "function(getRoomCardSource):lack of to"." in file".__FILE__." on Line ".__LINE__);
		 	return $this->missingPrameterArr("to");
		 }

		if($arrData['dealer_num'] == "-1")
		{
			return array('result'=>OPT_CONST::FAILED,'data'=>$result,'result_message'=>"代理商尚未部署成功");
		}
		$dealer_num = $arrData['dealer_num'];
		$DelaerConst = "Dealer_".$dealer_num;
		if($dealer_num == "-1" || !class_exists($DelaerConst))
		{
			return array('result'=>OPT_CONST::FAILED,'data'=>$result,'result_message'=>"代理商尚未部署成功");
		}
        $dealerDB = $DelaerConst::DBConst_Name;	

		//$account_id = $arrData['account_id'];
		$search_aid = $arrData['account_id'];

		 $from_timestamp = strtotime($arrData['from']);
		 $to_timestamp = strtotime($arrData['to']) + 86400;
        $whereTime = 'and r.create_time >= '.$from_timestamp.' and r.create_time <='.$to_timestamp;


        $sum_page = 1;


		$account_sql = 'SELECT a.account_id as account_id,acc.nickname as nickname,acc.headimgurl as headimgurl,sum(r.ticket_count) as sum_count, min(r.create_time) as from_time, max(r.create_time) as to_time FROM `'.Act_RedenvelopReceive.'` as r,'.Act_Redenvelop.' as a,'.WX_Account.' as acc WHERE r.`redenvelop_id`=a.`redenvelop_id` and a.account_id=acc.account_id and r.account_id="'.$search_aid.'" '.$whereTime.' group by a.account_id order by sum_count desc';
		$account_query = $this->getDataBySql($dealerDB,0,$account_sql);
		if($account_query != DB_CONST::DATA_NONEXISTENT)
		{
			foreach($account_query as $account_item)
			{
				$account_id = $account_item['account_id'];
				$nickname = $account_item['nickname'];
				$headimgurl = $account_item['headimgurl'];

				if($search_aid == $account_id)
				{
					continue;
				}

				$ticket_count = 0;
				//获取房卡数
				$ticket_sql = 'select ticket_count from '.Room_Ticket.' where account_id='.$account_id;
				$ticket_query = $this->getDataBySql($dealerDB,1,$ticket_sql);
				if($ticket_query != DB_CONST::DATA_NONEXISTENT)
				{
					$ticket_count = $ticket_query['ticket_count'];
				}

				$array['account_id'] = $account_item['account_id'];
				$array['nickname'] = $account_item['nickname'];
				$array['headimgurl'] = $account_item['headimgurl'];
				$array['ticket_count'] = $ticket_count;
				$array['sum_count'] = $account_item['sum_count'];
                $array['from_date'] = date('Y-m-d', $account_item['from_time']);
                $array['to_date'] = date('Y-m-d', $account_item['to_time']);

				//判断是否绑定直营代理
				$array['is_agent'] = 0;
				$bind_sql = 'select data_id from '.Agent_Bind.' where account_id='.$array['account_id'].' and is_delete=0';
				$bind_query = $this->getDataBySql($dealerDB,1,$bind_sql);
				if($bind_query != DB_CONST::DATA_NONEXISTENT)
				{
					$array['is_agent'] = 1;
				}


				$result[] = $array;
			}	
		}

		return array('result'=>OPT_CONST::SUCCESS,'data'=>$result,'result_message'=>"查询房卡来源",'sum_page'=>$sum_page);
	}

	/*
		查询房卡去向
	*/
	public function getRoomCardGone($arrData)
	{
		$result = array();
		$timestamp = time();

		if(!isset($arrData['dealer_num']) || $arrData['dealer_num'] === "" )
		{
			log_message('error', "function(getRoomCardGone):lack of dealer_num"." in file".__FILE__." on Line ".__LINE__);
			return $this->missingPrameterArr("dealer_num");
		}
		if(!isset($arrData['account_id']))
		{
			log_message('error', "function(getRoomCardGone):lack of account_id"." in file".__FILE__." on Line ".__LINE__);
			return $this->missingPrameterArr("account_id");
		}

		 if(!isset($arrData['from'])|| $arrData['from'] === "")
		 {
		 	log_message('error', "function(getRoomCardSource):lack of from"." in file".__FILE__." on Line ".__LINE__);
		 	return $this->missingPrameterArr("from");
		 }
		 if(!isset($arrData['to'])|| $arrData['to'] === "")
		 {
		 	log_message('error', "function(getRoomCardSource):lack of to"." in file".__FILE__." on Line ".__LINE__);
		 	return $this->missingPrameterArr("to");
		 }

		if($arrData['dealer_num'] == "-1")
		{
			return array('result'=>OPT_CONST::FAILED,'data'=>$result,'result_message'=>"代理商尚未部署成功");
		}
		$dealer_num = $arrData['dealer_num'];
		$DelaerConst = "Dealer_".$dealer_num;
		if($dealer_num == "-1" || !class_exists($DelaerConst))
		{
			return array('result'=>OPT_CONST::FAILED,'data'=>$result,'result_message'=>"代理商尚未部署成功");
		}
        $dealerDB = $DelaerConst::DBConst_Name;	

		//$account_id = $arrData['account_id'];
		$search_aid = $arrData['account_id'];

        $from_timestamp = strtotime($arrData['from']);
        $to_timestamp = strtotime($arrData['to']) + 86400;
        $whereTime = 'and r.create_time >= '.$from_timestamp.' and r.create_time <='.$to_timestamp;

		$sum_page = 1;


		$account_sql = 'SELECT r.account_id as account_id,acc.nickname as nickname,acc.headimgurl as headimgurl,sum(r.ticket_count) as sum_count, min(r.create_time) as from_time, max(r.create_time) as to_time FROM `'.Act_RedenvelopReceive.'` as r,'.Act_Redenvelop.' as a,'.WX_Account.' as acc WHERE r.`redenvelop_id`=a.`redenvelop_id` and r.account_id=acc.account_id and a.account_id="'.$search_aid.'" '.$whereTime.' group by r.account_id order by sum_count desc';
		$account_query = $this->getDataBySql($dealerDB,0,$account_sql);
		if($account_query != DB_CONST::DATA_NONEXISTENT)
		{
			foreach($account_query as $account_item)
			{
				$account_id = $account_item['account_id'];
				$nickname = $account_item['nickname'];
				$headimgurl = $account_item['headimgurl'];

				if($search_aid == $account_id)
				{
					continue;
				}

				$ticket_count = 0;
				//获取房卡数
				$ticket_sql = 'select ticket_count from '.Room_Ticket.' where account_id='.$account_id;
				$ticket_query = $this->getDataBySql($dealerDB,1,$ticket_sql);
				if($ticket_query != DB_CONST::DATA_NONEXISTENT)
				{
					$ticket_count = $ticket_query['ticket_count'];
				}

				$array['account_id'] = $account_item['account_id'];
				$array['nickname'] = $account_item['nickname'];
				$array['headimgurl'] = $account_item['headimgurl'];
				$array['ticket_count'] = $ticket_count;
				$array['sum_count'] = $account_item['sum_count'];
				$array['from_date'] = date('Y-m-d', $account_item['from_time']);
				$array['to_date'] = date('Y-m-d', $account_item['to_time']);

				//判断是否绑定直营代理
				$array['is_agent'] = 0;
				$bind_sql = 'select data_id from '.Agent_Bind.' where account_id='.$array['account_id'].' and is_delete=0';
				$bind_query = $this->getDataBySql($dealerDB,1,$bind_sql);
				if($bind_query != DB_CONST::DATA_NONEXISTENT)
				{
					$array['is_agent'] = 1;
				}

				$result[] = $array;
			}	
		}

		return array('result'=>OPT_CONST::SUCCESS,'data'=>$result,'result_message'=>"查询房卡去向",'sum_page'=>$sum_page);
	}

	/*
		查询对方收红包明细
	*/
	public function getReceiveRecord($arrData)
	{
		$result = array();
		$timestamp = time();

		if(!isset($arrData['dealer_num']) || $arrData['dealer_num'] === "" )
		{
			log_message('error', "function(getReceiveRecord):lack of dealer_num"." in file".__FILE__." on Line ".__LINE__);
			return $this->missingPrameterArr("dealer_num");
		}
		if(!isset($arrData['account_id']))
		{
			log_message('error', "function(getReceiveRecord):lack of account_id"." in file".__FILE__." on Line ".__LINE__);
			return $this->missingPrameterArr("account_id");
		}
		if(!isset($arrData['receive_account_id']))
		{
			log_message('error', "function(getReceiveRecord):lack of receive_account_id"." in file".__FILE__." on Line ".__LINE__);
			return $this->missingPrameterArr("receive_account_id");
		}
		if(!isset($arrData['page']))
		{
			log_message('error', "function(getReceiveRecord):lack of page"." in file".__FILE__." on Line ".__LINE__);
			return $this->missingPrameterArr("page");
		}
	
		if($arrData['dealer_num'] == "-1")
		{
			return array('result'=>OPT_CONST::FAILED,'data'=>$result,'result_message'=>"代理商尚未部署成功");
		}
		$dealer_num = $arrData['dealer_num'];
		$DelaerConst = "Dealer_".$dealer_num;
		if($dealer_num == "-1" || !class_exists($DelaerConst))
		{
			return array('result'=>OPT_CONST::FAILED,'data'=>$result,'result_message'=>"代理商尚未部署成功");
		}
        $dealerDB = $DelaerConst::DBConst_Name;	

        $page = $arrData['page'];
        if($page == 0 || $page == "")
        {
        	$page = 1;
        }

        $limit = 10;
        $offset = ($page - 1) * $limit;
        $sum_page = 1;

		$account_id = $arrData['account_id'];
		$receive_account_id = $arrData['receive_account_id'];
		
		$sum_page = 1;

		$account_sql = 'SELECT r.account_id as receive_account_id,acc.nickname as nickname,acc.headimgurl as headimgurl,a.ticket_count as ticket_count FROM `'.Act_RedenvelopReceive.'` as r,'.Act_Redenvelop.' as a,'.WX_Account.' as acc WHERE r.`redenvelop_id`=a.`redenvelop_id` and r.account_id=acc.account_id and a.account_id="'.$account_id.'"  order by a.create_time desc limit '.$offset.','.$limit;
		$account_query = $this->getDataBySql($dealerDB,0,$account_sql);
		if($account_query != DB_CONST::DATA_NONEXISTENT)
		{
			foreach($account_query as $account_item)
			{
				$receive_account_id = $account_item['receive_account_id'];
				$nickname = $account_item['nickname'];
				$headimgurl = $account_item['headimgurl'];

				$array['account_id'] = $account_id;
				$array['receive_account_id'] = $account_item['receive_account_id'];
				$array['nickname']           = $account_item['nickname'];
				$array['headimgurl']         = $account_item['headimgurl'];
				$array['ticket_count']       = $ticket_count;

				//判断是否绑定直营代理
				$array['is_agent'] = 0;
				$bind_sql = 'select data_id from '.Agent_Bind.' where account_id='.$array['account_id'].' and is_delete=0';
				$bind_query = $this->getDataBySql($dealerDB,1,$bind_sql);
				if($bind_query != DB_CONST::DATA_NONEXISTENT)
				{
					$array['is_agent'] = 1;
				}

				$result[] = $array;
			}	
		}

		return array('result'=>OPT_CONST::SUCCESS,'data'=>$result,'result_message'=>"查询对方收红包明细",'page'=>$page,'sum_page'=>$sum_page);
	}


}