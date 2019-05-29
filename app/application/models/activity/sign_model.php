<?php

include_once 'common_model.php';		//加载数据库操作类
class Sign_Model extends Activity_Common_Model
{
	public function __construct()
	{
		header('Content-Type: text/html; charset=utf-8'); 
		parent::__construct();
	}
	
	/************************************************
					common function
	*************************************************/

	//这个星期的星期一  
	// @$timestamp ，某个星期的某一个时间戳，默认为当前时间  
	// @is_return_timestamp ,是否返回时间戳，否则返回时间格式  
	protected function getMonday($timestamp=0,$is_return_timestamp=true){  
	    static $cache ;  
	    $id = $timestamp.$is_return_timestamp;  
	    if(!isset($cache[$id])){  
	        if(!$timestamp) $timestamp = time();  
	        $monday_date = date('Y-m-d', $timestamp-86400*date('w',$timestamp)+(date('w',$timestamp)>0?86400:-/*6*86400*/518400));  
	        if($is_return_timestamp){  
	            $cache[$id] = strtotime($monday_date);  
	        }else{  
	            $cache[$id] = $monday_date;  
	        }  
	    }  
	    return $cache[$id];  
	    
	}
	
	
	/************************************************
					logic function
	*************************************************/
	
	/*
		签到列表
		
		参数：
			account_id : account_id
		
		返回结果：
			
		
	*/
	public function getSignList($arrData)
	{
		$timestamp = time();
		$result = array();
		
		if(!isset($arrData['account_id']) || $arrData['account_id'] == G_CONST::EMPTY_STRING)
		{
			log_message('error', "function(getSignList):lack of account_id"." in file".__FILE__." on Line ".__LINE__);
			return $this->missingPrameterArr("account_id");
		}
		if(!isset($arrData['dealer_num']) || $arrData['dealer_num'] == G_CONST::EMPTY_STRING)
        {
            log_message('error', "function(getSignList):lack of dealer_num"." in file".__FILE__." on Line ".__LINE__);
            return $this->missingPrameterArr("dealer_num");
        }
        $dealer_num = $arrData['dealer_num'];
        $DelaerConst = "Dealer_".$dealer_num;
        $dealerDB = $DelaerConst::DBConst_Name; 

		$account_id = $arrData['account_id'];		
		
		$monday_timestamp = $this->getMonday($timestamp);
		
		//获取红包记录
		$sign_where = 'start_timestamp<='.$timestamp.' and end_timestamp>'.$timestamp.' and is_delete=0';
		$sign_sql = 'select activity_id,refresh_timestamp,day,content,ticket_count from '.Act_Sign.' where '.$sign_where.' order by day asc';
		$sign_query = $this->getDataBySql($dealerDB,0,$sign_sql);
		if(DB_CONST::DATA_NONEXISTENT != $sign_query)
		{
			$partakeCount_where = 'account_id='.$account_id.' and partake_timestamp>='.$monday_timestamp;
			$partakeCount_sql = 'select count(partake_id) as count from '.Act_SignPartake.' where '.$partakeCount_where.'';
			$partakeCount_query = $this->getDataBySql($dealerDB,1,$partakeCount_sql);
			$partake_count = $partakeCount_query['count'];
			
			foreach($sign_query as $item)
			{
				$activity_id = $item['activity_id'];
				$refresh_timestamp = $item['refresh_timestamp'];
				$day = $item['day'];
				$array['activity_id'] = $item['activity_id'];
				$array['day'] = $item['day'];
				$array['content'] = $item['content'];
				$array['ticket_count'] = $item['ticket_count'];
				$array['is_sign'] = G_CONST::IS_FALSE;
				
				if($partake_count >= $day)
				{
					$array['is_sign'] = G_CONST::IS_TRUE;
				}
				
				
				/*
				$partake_timestamp = $monday_timestamp + ($day - 1) * 86400 + $refresh_timestamp;
				
				$partake_where = 'activity_id='.$activity_id.' and account_id='.$account_id.' and partake_timestamp='.$partake_timestamp;
				$partake_sql = 'select partake_id from '.Act_SignPartake.' where '.$partake_where.'';
				$partake_query = $this->getDataBySql(1,$partake_sql);
				if(DB_CONST::DATA_NONEXISTENT != $partake_query)
				{
					$array['is_sign'] = G_CONST::IS_TRUE;
				}
				*/
				$result[] = $array;
			}
		}

		$today_issign = G_CONST::IS_FALSE;

		//判断今天是否已签到
		$today_timestamp = strtotime(date("Y-m-d",$timestamp));
		
		$partakeCount_where = 'account_id='.$account_id.' and partake_timestamp='.$today_timestamp;
		$partakeCount_sql = 'select count(partake_id) as count from '.Act_SignPartake.' where '.$partakeCount_where.'';
		$partakeCount_query = $this->getDataBySql($dealerDB,1,$partakeCount_sql);
		if($partakeCount_query["count"] > 0)
		{
			$today_issign = G_CONST::IS_TRUE;
		}
		
		return array('result'=>OPT_CONST::SUCCESS,'data'=>$result,'result_message'=>"签到列表","today_issign"=>$today_issign);
	}
	
	
	
	/*
		签到列表
		
		参数：
			account_id : account_id
		
		返回结果：
			
		
	*/
	public function signInOpt($arrData)
	{
		$timestamp = time();
		$result = array();
		
		if(!isset($arrData['account_id']) || $arrData['account_id'] == G_CONST::EMPTY_STRING)
		{
			log_message('error', "function(signInOpt):lack of account_id"." in file".__FILE__." on Line ".__LINE__);
			return $this->missingPrameterArr("account_id");
		}
		if(!isset($arrData['dealer_num']) || $arrData['dealer_num'] == G_CONST::EMPTY_STRING)
        {
            log_message('error', "function(signInOpt):lack of dealer_num"." in file".__FILE__." on Line ".__LINE__);
            return $this->missingPrameterArr("dealer_num");
        }
        $dealer_num = $arrData['dealer_num'];
        $DelaerConst = "Dealer_".$dealer_num;
        $dealerDB = $DelaerConst::DBConst_Name; 

		$account_id = $arrData['account_id'];		
		
		$monday_timestamp = $this->getMonday($timestamp);
		$today_timestamp = strtotime(date("Y-m-d",$timestamp));
		

		$partakeCount_where = 'account_id='.$account_id.' and partake_timestamp='.$today_timestamp;
		$partakeCount_sql = 'select count(partake_id) as count from '.Act_SignPartake.' where '.$partakeCount_where.'';
		$partakeCount_query = $this->getDataBySql($dealerDB,1,$partakeCount_sql);
		if($partakeCount_query["count"] > 0)
		{
			return array('result'=>OPT_CONST::FAILED,'data'=>$result,'result_message'=>"今天已签到，请明天再来");
		}

		
		$partakeCount_where = 'account_id='.$account_id.' and partake_timestamp>='.$monday_timestamp;
		$partakeCount_sql = 'select count(partake_id) as count from '.Act_SignPartake.' where '.$partakeCount_where.'';
		$partakeCount_query = $this->getDataBySql($dealerDB,1,$partakeCount_sql);
		$partake_count = $partakeCount_query['count'];
		
		$day = $partake_count + 1;
		if($day > 7)
		{
			return array('result'=>OPT_CONST::FAILED,'data'=>$result,'result_message'=>"今天已签到，请明天再来");
		}
		//$day = ceil(($today_timestamp - $monday_timestamp)/86400) + 1;
		
		//获取红包记录
		$sign_where = 'start_timestamp<='.$timestamp.' and end_timestamp>'.$timestamp.' and day='.$day.' and is_delete=0';
		$sign_sql = 'select activity_id,refresh_timestamp,day,content,ticket_count from '.Act_Sign.' where '.$sign_where;
		$sign_query = $this->getDataBySql($dealerDB,1,$sign_sql);
		if(DB_CONST::DATA_NONEXISTENT == $sign_query)
		{
			return array('result'=>OPT_CONST::FAILED,'data'=>$result,'result_message'=>"签到任务已过期");
		}
		
		$activity_id = $sign_query['activity_id'];
		$refresh_timestamp = $sign_query['refresh_timestamp'];
		$ticket_count = $sign_query['ticket_count'];
		
		//$partake_timestamp = $monday_timestamp + ($day - 1) * 86400 + $refresh_timestamp;
		$partake_timestamp = $today_timestamp;
		
		//$partake_where = 'activity_id='.$activity_id.' and account_id='.$account_id.' and partake_timestamp='.$partake_timestamp;
		$partake_where = 'account_id='.$account_id.' and partake_timestamp='.$partake_timestamp;
		$partake_sql = 'select partake_id from '.Act_SignPartake.' where '.$partake_where.'';
		$partake_query = $this->getDataBySql($dealerDB,1,$partake_sql);
		if(DB_CONST::DATA_NONEXISTENT != $partake_query)
		{
			return array('result'=>OPT_CONST::FAILED,'data'=>$result,'result_message'=>"今天已签到，请明天再来");
		}
		
		$insert_array['create_time'] = $timestamp;
		$insert_array['create_appid'] = "aid_".$account_id;
		$insert_array['update_time'] = $timestamp;
		$insert_array['update_appid'] = "aid_".$account_id;;
		$insert_array['is_delete'] = G_CONST::IS_FALSE;
		$insert_array['account_id'] = $account_id;
		$insert_array['activity_id'] = $activity_id;
		$insert_array['ticket_count'] = $ticket_count;
		$insert_array['partake_timestamp'] = $partake_timestamp;
		$partake_id = $this->getInsertID($dealerDB,Act_SignPartake, $insert_array);
		
		//将房卡添加到自己账户
		$updateTicket_str = 'update_time='.$timestamp.',update_appid="aid_'.$account_id.'",ticket_count=ticket_count+'.$ticket_count;
		$updateTicket_where = 'account_id='.$account_id;
		$updateTicket_query = $this->changeNodeValue($dealerDB,Room_Ticket,$updateTicket_str,$updateTicket_where);

		//房卡流水账
		$journal_ary['journal_type'] = Game_CONST::JournalType_Income;
		$journal_ary['account_id'] = $account_id;
		$journal_ary['object_type'] = Game_CONST::ObjectType_Sign;
		$journal_ary['object_id'] = $partake_id;
		$journal_ary['ticket_count'] = $ticket_count;
		$journal_ary['extra'] = "";
		$this->updateRoomTicketJournal($journal_ary,$dealerDB);

		
		
		$result['activity_id'] = $activity_id;
		
		return array('result'=>OPT_CONST::FAILED,'data'=>$result,'result_message'=>"签到成功");
	}
	



	
}