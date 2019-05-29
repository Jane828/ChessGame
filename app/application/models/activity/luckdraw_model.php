<?php

include_once 'common_model.php';		//加载数据库操作类
class Luckdraw_Model extends Activity_Common_Model
{
	public function __construct()
	{
		header('Content-Type: text/html; charset=utf-8'); 
		parent::__construct();
	}
	
	/************************************************
					common function
	*************************************************/
	
	protected function randFortuneWheel($bet,$option_1,$option_2)
	{
		$return = array();
		$result_ary_1 = array(1,2,3,4,5,6,7,8,9,10,11,12);
		$result_ary_2 = array(1,2,3,4,5);
		
		$reward = 0;
		
		$result = mt_rand(1,360);
		if($result == 1)
		{
			$times = 12 * 5;
		}
		else if($result >= 10 && $result < 20)
		{
			$times = 12;
		}
		else if($result >= 100 && $result < 136)
		{
			$times = 5;
		}
		else
		{
			$times = 0;
		}
		
		switch($times)
		{
			case 60:
				$result_1 = $option_1;
				$result_2 = $option_2;
				break;
			case 12:
				$result_1 = $option_1;
				unset($result_ary_2[$option_2-1]);
				$result_2 = $result_ary_2[array_rand($result_ary_2,1)];
				break;
			case 5:
				unset($result_ary_1[$option_1-1]);
				$result_1 = $result_ary_1[array_rand($result_ary_1,1)];
				$result_2 = $option_2;
				break;
			case 0:
			default:
				unset($result_ary_1[$option_1-1]);
				$result_1 = $result_ary_1[array_rand($result_ary_1,1)];
				unset($result_ary_2[$option_2-1]);
				$result_2 = $result_ary_2[array_rand($result_ary_2,1)];
				break;
		}
		
		$reward = $bet * $times;
		
		$return['reward'] = $reward;
		$return['result_1'] = $result_1;
		$return['result_2'] = $result_2;
		
		return $return;
	}
	
	
	/************************************************
					logic function
	*************************************************/
	

	
	
	/*
		幸运大转盘抽奖
		
		参数：
			open_id : 抽奖账号
			option_1 : 生肖，1-12
			option_2 : 五行，1-12
			ticket_count : 房卡数
			
		
		返回结果：
			
		
	*/
	public function fortuneWheelOpt($arrData)
	{
		$timestamp = time();
		$result = array();
		
		if(!isset($arrData['open_id']) || $arrData['open_id'] == G_CONST::EMPTY_STRING)
		{
			log_message('error', "function(fortuneWheelOpt):lack of open_id"." in file".__FILE__." on Line ".__LINE__);
			return $this->missingPrameterArr("open_id");
		}
		if(!isset($arrData['option_1']) || $arrData['option_1'] == G_CONST::EMPTY_STRING)
		{
			log_message('error', "function(receiveRedEnvelopOpt):lack of option_1"." in file".__FILE__." on Line ".__LINE__);
			return $this->missingPrameterArr("option_1");
		}
		if(!isset($arrData['option_2']) || $arrData['option_2'] == G_CONST::EMPTY_STRING)
		{
			log_message('error', "function(receiveRedEnvelopOpt):lack of option_2"." in file".__FILE__." on Line ".__LINE__);
			return $this->missingPrameterArr("option_2");
		}
		if(!isset($arrData['bet']) || $arrData['bet'] == G_CONST::EMPTY_STRING)
		{
			log_message('error', "function(receiveRedEnvelopOpt):lack of bet"." in file".__FILE__." on Line ".__LINE__);
			return $this->missingPrameterArr("bet");
		}
		if(!isset($arrData['dealer_num']) || $arrData['dealer_num'] == G_CONST::EMPTY_STRING)
        {
            log_message('error', "function(getReceiveList):lack of dealer_num"." in file".__FILE__." on Line ".__LINE__);
            return $this->missingPrameterArr("dealer_num");
        }
        $dealer_num = $arrData['dealer_num'];
		$DelaerConst = "Dealer_".$dealer_num;
        $dealerDB = $DelaerConst::DBConst_Name;	
		
		$open_id = $arrData['open_id'];	
		$option_1 = $arrData['option_1'];	
		$option_2 = $arrData['option_2'];
		$bet = $arrData['bet'];
		
		
		$account_query = $this->getAccountByOpenid($open_id,$dealerDB);
		if(DB_CONST::DATA_NONEXISTENT == $account_query)
		{
			return array('result'=>OPT_CONST::FAILED,'data'=>$result,'result_message'=>"用户不存在");
		}
		
		$account_id = $account_query['account_id'];
		
		//获取用户剩余房卡
		$ticket_where = 'account_id='.$account_id.' and is_delete=0';
		$ticket_sql = 'select ticket_count from '.Room_Ticket.' where '.$ticket_where;
		$ticket_query = $this->getDataBySql($dealerDB,1,$ticket_sql);
		if(DB_CONST::DATA_NONEXISTENT == $ticket_query)
		{
			return array('result'=>OPT_CONST::FAILED,'data'=>$result,'result_message'=>"房卡不足");
		}
		
		if($bet > $ticket_query['ticket_count'])
		{
			return array('result'=>OPT_CONST::FAILED,'data'=>$result,'result_message'=>"房卡不足");
		}
		
		//减少自己账户上的房卡
		$updateTicket_str = 'update_time='.$timestamp.',update_appid="aid_'.$account_id.'",ticket_count=ticket_count-'.$bet;
		$updateTicket_where = 'account_id='.$account_id;
		$updateTicket_query = $this->changeNodeValue($dealerDB,Room_Ticket,$updateTicket_str,$updateTicket_where);
		
		//开始抽奖
		$result_array = $this->randFortuneWheel($bet,$option_1,$option_2);
		$reward = $result_array['reward'];
		$result_1 = $result_array['result_1'];	//生肖开奖结果
		$result_2 = $result_array['result_2'];	//五行开奖结果
		
		
		//添加抽奖记录
		$insert_array['create_time'] = $timestamp;
		$insert_array['create_appid'] = "aid_".$account_id;
		$insert_array['update_time'] = $timestamp;
		$insert_array['update_appid'] = "aid_".$account_id;
		$insert_array['is_delete'] = G_CONST::IS_FALSE;
		
		$insert_array['account_id'] = $account_id;
		$insert_array['option_1'] = $option_1;
		$insert_array['option_2'] = $option_2;
		$insert_array['bet'] = $bet;
		$insert_array['result_1'] = $result_1;
		$insert_array['result_2'] = $result_2;
		$insert_array['reward'] = $reward;
			
		$data_id = $this->getInsertID($dealerDB,Act_Fortunewheel, $insert_array);
		
		//房卡流水账
		$journal_ary['journal_type'] = Game_CONST::JournalType_Disburse;
		$journal_ary['account_id'] = $account_id;
		$journal_ary['object_type'] = Game_CONST::ObjectType_Luckdraw;
		$journal_ary['object_id'] = $data_id;
		$journal_ary['ticket_count'] = $bet;
		$journal_ary['extra'] = "";
		$this->updateRoomTicketJournal($journal_ary,$dealerDB);



		if($reward > 0)
		{
			//将房卡添加到自己账户
			$updateTicket_str = 'update_time='.$timestamp.',update_appid="aid_'.$account_id.'",ticket_count=ticket_count+'.$reward;
			$updateTicket_where = 'account_id='.$account_id;
			$updateTicket_query = $this->changeNodeValue($dealerDB,Room_Ticket,$updateTicket_str,$updateTicket_where);

			//房卡流水账
			$journal_ary['journal_type'] = Game_CONST::JournalType_Income;
			$journal_ary['account_id'] = $account_id;
			$journal_ary['object_type'] = Game_CONST::ObjectType_Luckdraw;
			$journal_ary['object_id'] = $data_id;
			$journal_ary['ticket_count'] = $reward;
			$journal_ary['extra'] = "";
			$this->updateRoomTicketJournal($journal_ary,$dealerDB);
		}
		
		$result['result_1'] = $result_1;
		$result['result_2'] = $result_2;
		$result['reward'] = $reward;
		
		return array('result'=>OPT_CONST::SUCCESS,'data'=>$result,'result_message'=>"幸运大转盘抽奖");
	}
		
		
		
}