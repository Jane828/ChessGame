<?php

include_once __DIR__.'/common_model.php';		//加载数据库操作类
class Slot_Model extends Roomcard_Common_Model
{
	public function __construct()
	{
		header('Content-Type: text/html; charset=utf-8'); 
		parent::__construct();
	}


	/*
		获取水果机概况数据
	*/
	public function getSlotSummary($arrData)
	{
		$result = array();
		$timestamp = time();

		if(!isset($arrData['dealer_num']) || $arrData['dealer_num'] === "" )
		{
			log_message('error', "function(getSlotSummary):lack of dealer_num"." in file".__FILE__." on Line ".__LINE__);
			return $this->missingPrameterArr("dealer_num");
		}
		if(!isset($arrData['from'])|| $arrData['from'] === "")
		{
			log_message('error', "function(getSlotSummary):lack of from"." in file".__FILE__." on Line ".__LINE__);
			return $this->missingPrameterArr("from");
		}
		if(!isset($arrData['to'])|| $arrData['to'] === "")
		{
			log_message('error', "function(getSlotSummary):lack of to"." in file".__FILE__." on Line ".__LINE__);
			return $this->missingPrameterArr("to");
		}

		if($arrData['dealer_num'] == "-1")
		{
			return array('result'=>OPT_CONST::SUCCESS,'data'=>$result,'result_message'=>"获取水果机概况数据");
		}
		$dealer_num = $arrData['dealer_num'];
		$DelaerConst = "Dealer_".$dealer_num;
		if($dealer_num == "-1" || !class_exists($DelaerConst))
		{
			return array('result'=>OPT_CONST::SUCCESS,'data'=>$result,'result_message'=>"获取水果机概况数据");
		}
        $dealerDB = $DelaerConst::DBConst_Name;	

		$from = $arrData['from'];
		$to = $arrData['to'];

		$from_timestamp = strtotime($arrData['from']);
		$to_timestamp = strtotime($arrData['to']) + 86400;


		$sum_bet = 0;
		$sum_reward = 0;
		//查看投注额	
		$where = 'create_time>='.$from_timestamp.' and create_time<'.$to_timestamp.' and is_delete=0';
		$sql = 'select sum(bet_count) as sum_bet,sum(reward) as sum_reward from activity_slotmachine where '.$where;
		$query = $this->getDataBySql($dealerDB,1,$sql);
		if($query != DB_CONST::DATA_NONEXISTENT)
		{
			$sum_bet = $query['sum_bet'];
			if($sum_bet==null)
				$sum_bet=0;
			$sum_reward = $query['sum_reward'];
			if($sum_reward==null)
				$sum_reward=0;
		}

		$balance = $sum_bet - $sum_reward;

		$result['sum_bet'] = $sum_bet;
		$result['sum_reward'] = $sum_reward;
		$result['balance'] = $balance;

		return array('result'=>OPT_CONST::SUCCESS,'data'=>$result,'result_message'=>"概况数据");
	}


	/*
		获取水果机概况数据
	*/
	public function getSlotList($arrData)
	{
		$result = array();
		$timestamp = time();

		if(!isset($arrData['dealer_num']) || $arrData['dealer_num'] === "" )
		{
			log_message('error', "function(getSlotSummary):lack of dealer_num"." in file".__FILE__." on Line ".__LINE__);
			return $this->missingPrameterArr("dealer_num");
		}
		if(!isset($arrData['from'])|| $arrData['from'] === "")
		{
			log_message('error', "function(getSlotSummary):lack of from"." in file".__FILE__." on Line ".__LINE__);
			return $this->missingPrameterArr("from");
		}
		if(!isset($arrData['to'])|| $arrData['to'] === "")
		{
			log_message('error', "function(getSlotSummary):lack of to"." in file".__FILE__." on Line ".__LINE__);
			return $this->missingPrameterArr("to");
		}
		if(!isset($arrData['page']))
		{
			log_message('error', "function(getPlayDetailList):lack of page"." in file".__FILE__." on Line ".__LINE__);
			return $this->missingPrameterArr("page");
		}

		if($arrData['dealer_num'] == "-1")
		{
			return array('result'=>OPT_CONST::SUCCESS,'data'=>$result,'result_message'=>"获取开局明细统计",'sum_page'=>1);
		}
		$dealer_num = $arrData['dealer_num'];
		$DelaerConst = "Dealer_".$dealer_num;
		if($dealer_num == "-1" || !class_exists($DelaerConst))
		{
			return array('result'=>OPT_CONST::SUCCESS,'data'=>$result,'result_message'=>"获取开局明细统计",'sum_page'=>1);
		}
        $dealerDB = $DelaerConst::DBConst_Name;	

		$from = $arrData['from'];
		$to = $arrData['to'];
		$page = $arrData['page'];
		if($page == 0 || $page == "")
		{
			$page = 1;
		}

		$from_timestamp = strtotime($arrData['from']);
		$to_timestamp = strtotime($arrData['to']) + 86400;

		$limit = 10;
		$offset = ($page - 1) * $limit;
		$sum_page = 1;

		$where = 'create_time>='.$from_timestamp.' and create_time<'.$to_timestamp.' and is_delete=0';
		$sql = 'select create_time,account_id,bet_count,reward from '.Act_SlotMachine.' where '.$where.' order by create_time desc limit '.$offset.','.$limit;
		$query = $this->getDataBySql($dealerDB,0,$sql);
		if($query != DB_CONST::DATA_NONEXISTENT)
		{
			$count_sql = 'select count(create_time) as count from '.Act_SlotMachine.' where '.$where;
			$count_sql .= ' and is_delete=0';
			$count_query = $this->getDataBySql($dealerDB,1,$count_sql);
			$sum_page = ceil($count_query['count'] / $limit);

			foreach($query as $item)
			{
				$array['time'] = date('Y-m-d H:i:s',$item['create_time']);
				$array['bet_count'] = $item['bet_count'];
				$array['reward'] = $item['reward'];
				$array['balance'] = $item['bet_count'] - $array['reward'];
				$array['nickname'] = "";
				$account_sql = 'select nickname,headimgurl from '.WX_Account.' where account_id='.$item['account_id'].' limit 1';
				$account_query = $this->getDataBySql($dealerDB,1,$account_sql);
				if($account_query != DB_CONST::DATA_NONEXISTENT)
				{
					$array['nickname'] = $account_query['nickname'];
				}
				$result[]  = $array;
			}

		}
		return array('result'=>OPT_CONST::SUCCESS,'data'=>$result,'result_message'=>"获取水果机结果列表",'sum_page'=>$sum_page);
	}
}