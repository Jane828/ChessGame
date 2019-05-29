<?php

use \GatewayWorker\Lib\Gateway;  

require_once dirname(__DIR__) . '/public.class.model.php';
class Room_Model extends Public_Model
{
	
	/***************************
			common_function	
	***************************/
	
	
	/*
		断线
	*/
	public function userDisconnected($arrData)
	{
		$timestamp = time();

		if(!isset($arrData['gaming_roomid']) || trim($arrData['gaming_roomid']) == G_CONST::EMPTY_STRING)
		{
			return false;
		}
		if(!isset($arrData['account_id']) || trim($arrData['account_id']) == G_CONST::EMPTY_STRING)
		{
			return false;
		}
		
		$REMOTE_ADDR = "unknow";
		if(isset($arrData['REMOTE_ADDR']))
		{
			$REMOTE_ADDR = $arrData['REMOTE_ADDR'];
		}
		
		$room_id 		= $arrData['gaming_roomid'];
		$account_id 	= $arrData['account_id'];
		$client_id 		= $arrData['client_id'];

		$clients_of_groupid = Gateway::getClientSessionsByGroup($room_id);
		$clients_of_groupid = array_keys($clients_of_groupid);
		
		if($this->queryOnlineStatus($room_id, $account_id)){
			$this->writeLog("[$room_id] [$account_id] is still in room");
			return true;
		}
		
		$Redis_Model = Redis_Model::getModelObject();
		$replyArr = array("[roomid]"=>$room_id,"[accountid]"=>$account_id);

        //没开局玩过的用户离线，自动从房间中退出
        $RoomSequence_Key = strtr(Redis_Const::RoomSequence_Key, $replyArr);
        $RoomAudience_Key = strtr(Redis_Const::RoomAudience_Key, $replyArr);

        //在游戏中断开连接
        $rsSeq_score = $Redis_Model->getZscore($RoomSequence_Key,$account_id);
        if(Redis_CONST::DATA_NONEXISTENT !== $rsSeq_score) {
            if ($this->queryTicketChecked($room_id, $account_id) == 0) {
                $RoomSequence_Key = strtr(Redis_Const::RoomSequence_Key, $replyArr);
                $zrem_result = $Redis_Model->zremSet($RoomSequence_Key, array($account_id));

                $account_status = Game::AccountStatus_Initial;
            } elseif ($this->queryAccountStatus($room_id, $account_id) == Game::AccountStatus_Watch) {
                $account_status = Game::AccountStatus_Notready;
            } else {
                $account_status = $this->queryAccountStatus($room_id, $account_id);
                if ($account_status == Game::AccountStatus_Ready) {    //准备状态下断线， 变为未准备

                    $account_status = Game::AccountStatus_Notready;
                }
            }
            $this->updateAccountStatus($room_id, $account_id, $account_status);
            $this->writeLog("[$room_id] ($account_id) 离线   REMOTE_ADDR:" . $REMOTE_ADDR);

            //获取房间状态
            $room_status = $this->queryRoomStatus($room_id);
            if ($room_status === Redis_CONST::DATA_NONEXISTENT || $room_status == Game::RoomStatus_Closed) {    //房间已经废弃或未激活
                return true;
            }

            if ($room_status == Game::RoomStatus_Waiting) {
                $ready_count = $this->queryReadyCount($room_id);
                if ($ready_count < 2) {
                    //取消倒计时
                    $this->deleteRoomTimer($room_id);
                    $arr = array("result" => 0, "operation" => "CancelStartLimitTime", "data" => array(), "result_message" => "取消开局倒计时");
                    $this->pushMessageToGroup($room_id, $arr);
                } else {
                    $this->startGame($room_id);
                }
            }

            $clients_of_groupid = Gateway::getClientSessionsByGroup($room_id);
            //判断是否全部人离线
            if (count($clients_of_groupid) == 0) {
                $this->setupClearRoomPassiveTimer($room_id);
            }
        }

        //在观战中断开连接
        $rsAud_score = $Redis_Model->getZscore($RoomAudience_Key,$account_id);
        if(Redis_CONST::DATA_NONEXISTENT !== $rsAud_score){
            $this->updateAudienceInfo($room_id, $account_id, Game::AudienceStatus_off);
        }

		return true; 
	}
	
	
	
	/***************************
			logic_function	
	***************************/
	
	/*
		创建房间
	*/
	public function createRoom($arrData)
	{
		$timestamp = time();
		$result = array();

		$data 			= $arrData['data'];
		$operation 		= $arrData['operation'];
		$account_id 	= $arrData['account_id'];
		$session 		= $arrData['session'];

		$Verification_Model = Verification_Model::getModelObject();
		if(false == $Verification_Model->checkRequestSession($account_id,$session))
		{
			return OPT_CONST::NO_RETURN;
		}
		
		if(!isset($data['data_key']) || trim($data['data_key']) == G_CONST::EMPTY_STRING)
		{
			$this->logMessage('error', "function(createRoom):lack of data_key"." in file".__FILE__." on Line ".__LINE__);
			return $this->_missingPrameterArr($operation,"data_key");
		}

		$data_key = $data['data_key'];
		$banker_scoreType = Config::BankerScore_1;
		$banker_score = Config::BankerScore_1;

		//获取维护公告
		$alert_type = -1;
		$alert_text = "";
		$announcement_result = $this->getGameAnnouncement($account_id);
		if(is_array($announcement_result))
		{
			$alert_type = $announcement_result['alert_type'];
			$alert_text = $announcement_result['alert_text'];
		}
		if($alert_type == 4)
		{
			$result['alert_type'] = $alert_type;
			return array("result"=>"-1","operation"=>$operation,"data"=>$result,"result_message"=>$alert_text); 
		}


		//庄家类型
		if(isset($data['banker_mode']) && in_array($data['banker_mode'], [Game::BankerMode_FreeGrab,Game::BankerMode_SeenGrab,Game::BankerMode_NoBanker,Game::BankerMode_FixedBanker]))
		{
			$banker_mode = $data['banker_mode'];
		}
		else
		{
			$this->logMessage('error', "function(createRoom):用户".$account_id." banker_mode error："." in file".__FILE__." on Line ".__LINE__);
			return array("result"=>"-1","operation"=>$operation,"data"=>$result,"result_message"=>"错误模式"); 
		}

		if($banker_mode == Game::BankerMode_FixedBanker)
		{
			//庄家类型
			if(isset($data['banker_score_type']) && in_array($data['banker_score_type'], [1,2,3,4]))
			{
				$banker_scoreType = $data['banker_score_type'];
			}
			if($banker_scoreType == 1)
			{
				$banker_score = Config::BankerScore_1;
			}
			else if($banker_scoreType == 2)
			{
				$banker_score = Config::BankerScore_2;
			}
			else if($banker_scoreType == 3)
			{
				$banker_score = Config::BankerScore_3;
			}
			else if($banker_scoreType == 4)
			{
				$banker_score = Config::BankerScore_4;
			}
			else
			{
				$this->logMessage('error', "function(createRoom):用户".$account_id." banker_scoreType error："." in file".__FILE__." on Line ".__LINE__);
				return array("result"=>"-1","operation"=>$operation,"data"=>$result,"result_message"=>"上庄分数规则错误"); 
			}
		}

		//消耗房卡类型
		if(isset($data['ticket_type']) && $data['ticket_type'] == 1)
		{
			$spend_ticket_count = Game::Rule_TicketType_1;
		}
		else if(isset($data['ticket_type']) && $data['ticket_type'] == 2)
		{
			$spend_ticket_count = Game::Rule_TicketType_2;
		}
		else
		{
			$this->logMessage('error', "function(createRoom):用户".$account_id." ticket_type error："." in file".__FILE__." on Line ".__LINE__);
			return array("result"=>"-1","operation"=>$operation,"data"=>$result,"result_message"=>"房卡规则错误"); 
		}

		//默认底分
		if(isset($data['score_type']) && $data['score_type'] == 1)
		{
			$base_score = Config::Rule_ScoreType_1;
		}
		else if(isset($data['score_type']) && $data['score_type'] == 2)
		{
			$base_score = Config::Rule_ScoreType_2;
		}
		else if(isset($data['score_type']) && $data['score_type'] == 3)
		{
			$base_score = Config::Rule_ScoreType_3;
		}
		else if(isset($data['score_type']) && $data['score_type'] == 4)
		{
			$base_score = Config::Rule_ScoreType_4;
		}
		else if(isset($data['score_type']) && $data['score_type'] == 5)
		{
			$base_score = Config::Rule_ScoreType_5;
		}else if(isset($data['score_type']) && $data['score_type'] == 6){
		    $base_score = Config::Rule_ScoreType_6;
        }
		else
		{
			$this->logMessage('error', "function(createRoom):用户".$account_id." score_type error："." in file".__FILE__." on Line ".__LINE__);
			return array("result"=>"-1","operation"=>$operation,"data"=>$result,"result_message"=>"分数规则错误"); 
		}

		//规则
		$is_joker = (isset($data['is_joker']) && $data['is_joker'] == 1) ? 1 : 0;
		$is_bj = (isset($data['is_bj']) && $data['is_bj'] == 1) ? 1 : 0;

		$pay_type = Config::Ticket_Mode;

		//每轮局数
		$eachRound_totalNum = $spend_ticket_count * Config::GameNum_EachRound;

		$MMYSQL = $this->initMysql();
		//判断房卡余额
		$my_ticket_count = $MMYSQL->select("ticket_count")->from("room_ticket")->where("account_id=".$account_id)->single();
		if($my_ticket_count >= $spend_ticket_count){

		} else {
			$this->writeLog("($account_id) 牌券不足");
			$result['alert_type'] = 1;	//1房卡不足
			return array("result"=>"1","operation"=>$operation,"data"=>$result,"result_message"=>"房卡不足"); 
		}

		//判断房间申请记录是否存在
		$room_where = 'data_key="'.$data_key.'"';
		$room_sql = 'select room_id,room_number,account_id,is_close from '.Room.' where '.$room_where;
		$room_query = $MMYSQL->query($room_sql);
		if(!is_array($room_query) || count($room_query) == 0 )
		{
			$room_aid = $account_id;
			$is_close = G_CONST::IS_FALSE;
			
			$r_array['create_time'] = $timestamp;
			$r_array['create_appid'] = "aid_".$account_id;
			$r_array['update_time'] = $timestamp;
			$r_array['update_appid'] = "aid_".$account_id;
			$r_array['is_delete'] = G_CONST::IS_FALSE;
			$r_array['data_key'] = $data_key;
			$r_array['account_id'] = $room_aid;
			$r_array['is_close'] = G_CONST::IS_FALSE;
			$r_array['game_type'] = Game::Game_Type;
			
			$MMYSQL->insertReturnID(Room,$r_array);
			
			$room_id = $MMYSQL->select("room_id")->from(Room)->where('data_key="'.$data_key.'"')->single();
			if($room_id > 0)
			{
				$room_number = 10000 + $room_id;
			}
			else
			{
				$this->logMessage('error', "function(createRoom):用户".$account_id." 创建房间失败："." in file".__FILE__." on Line ".__LINE__);
				return array("result"=>"-1","operation"=>$operation,"data"=>$result,"result_message"=>"创建房间失败"); 
			}

			$num_updateSql = 'update '.Room.' set room_number="'.$room_number.'" where room_id='.$room_id;
			$MMYSQL->query($num_updateSql);
		}
		else
		{
			$room_id = $room_query[0]['room_id'];
			$room_number = $room_query[0]['room_number'];
			$is_close = $room_query[0]['is_close'];
		}
		

		//添加房间信息到redis
		$Redis_Model = Redis_Model::getModelObject();
		
		$replyArr = array("[roomid]"=>$room_id);
		$Room_Key = strtr(Redis_Const::Room_Key, $replyArr);
		
		$r_mkv[Redis_Const::Room_Field_Number] = $room_number;	//房间号
		$r_mkv[Redis_Const::Room_Field_GameRound] = 1;			//游戏轮数
		$r_mkv[Redis_Const::Room_Field_GameNum] = 0;			//游戏局数
		$r_mkv[Redis_Const::Room_Field_Status] = Game::RoomStatus_Waiting;				//房间状态，1等待、2进行中、3关闭
		$r_mkv[Redis_Const::Room_Field_DefaultScore] = $base_score;		//开局默认分数
		$r_mkv[Redis_Const::Room_Field_ActiveUser] = -1;		//当前操作用户
		$r_mkv[Redis_Const::Room_Field_ActiveTimer] = -1;		//当前生效timer
		$r_mkv[Redis_Const::Room_Field_BaseScore] = $base_score;		//当前生效timer
		$r_mkv[Redis_Const::Room_Field_TotalNum] = $eachRound_totalNum;		//每轮总局数
		$r_mkv[Redis_Const::Room_Field_TicketCount] = $spend_ticket_count;		//每轮消耗房卡数量
		$r_mkv[Redis_Const::Room_Field_Creator] = $account_id;		//创建用户
		$r_mkv[Redis_Const::Room_Field_Scoreboard] = "";		//积分榜
		$r_mkv[Redis_Const::Room_Field_Paytype] = $pay_type;		///AA,2房主扣卡

		$r_mkv[Redis_Const::Room_Field_ActiveUser] = -1;		//当前操作用户
		$r_mkv[Redis_Const::Room_Field_ActiveTimer] = -1;		//当前生效timer
		$r_mkv[Redis_Const::Room_Field_ReadyTime] = -1;		//房间倒计时

		$r_mkv[Redis_Const::Room_Field_StartTime] = -1;		//开局时间

        $r_mkv[Redis_Const::Room_Field_Is_Joker]= $is_joker;
        $r_mkv[Redis_Const::Room_Field_Is_Bj]   = $is_bj;

		$r_mkv[Redis_Const::Room_Field_BankerMode] = $banker_mode;		//庄家类型
		$r_mkv[Redis_Const::Room_Field_BankerScoreType] = $banker_scoreType;	//上庄分数类型
		$r_mkv[Redis_Const::Room_Field_BankerScore] = $banker_score;	//上庄分数
			
		$Redis_Model->hmsetField($Room_Key,$r_mkv);

		if(Game::PaymentType_Creator == $pay_type)
		{
			$this->writeLog("[$room_id] ($account_id) 消耗房卡:".$spend_ticket_count);
			//扣除房卡
			$this->balanceTicket($room_id, $account_id, $spend_ticket_count);
		}
		
		$result['room_id'] = $room_id;
		$result['room_number'] = $room_number;
		$result['is_close'] = $is_close;
		$result['banker_mode'] = $banker_mode;
		$this->writeLog("[$room_id] ($account_id) 创建房间".$room_number);
		return array("result"=>OPT_CONST::SUCCESS,"operation"=>$operation,"data"=>$result,"result_message"=>"创建房间");  
	}
	
	

	/*
		用房卡激活房间
	*/
	public function activateRoom($arrData)
	{
		$result = array();

		$data 			= $arrData['data'];
		$operation 		= $arrData['operation'];
		$account_id 	= $arrData['account_id'];
		$session 		= $arrData['session'];

		$Verification_Model = Verification_Model::getModelObject();
		if(false == $Verification_Model->checkRequestSession($account_id,$session))
		{
			return OPT_CONST::NO_RETURN;
		}
		
		if(!(isset($data['room_number'])&& $data['room_number'] > 0) )
		{
			$this->logMessage('error', "function(activateRoom):lack of room_number"." in file".__FILE__." on Line ".__LINE__);
			return $this->_missingPrameterArr($operation,"room_number");
		}
		
		$room_number = $data['room_number'];
		$banker_scoreType = Config::BankerScore_1;		//默认无上限
		$banker_score = Config::BankerScore_1;
		//庄家类型
		if(isset($data['banker_mode']) && in_array($data['banker_mode'], [Game::BankerMode_FreeGrab,Game::BankerMode_SeenGrab,Game::BankerMode_TenGrab,Game::BankerMode_NoBanker,Game::BankerMode_FixedBanker]))
		{
			$banker_mode = $data['banker_mode'];
		}
		else
		{
			$this->logMessage('error', "function(activateRoom):用户".$account_id." banker_mode error："." in file".__FILE__." on Line ".__LINE__);
			return array("result"=>"-1","operation"=>$operation,"data"=>$result,"result_message"=>"错误模式"); 
		}

		if($banker_mode == Game::BankerMode_FixedBanker)
		{
			//庄家类型
			if(isset($data['banker_score_type']) && in_array($data['banker_score_type'], [1,2,3,4]))
			{
				$banker_scoreType = $data['banker_score_type'];
			}
			if($banker_scoreType == 1)
			{
				$banker_score = Config::BankerScore_1;
			}
			else if($banker_scoreType == 2)
			{
				$banker_score = Config::BankerScore_2;
			}
			else if($banker_scoreType == 3)
			{
				$banker_score = Config::BankerScore_3;
			}
			else if($banker_scoreType == 4)
			{
				$banker_score = Config::BankerScore_4;
			}
			else
			{
				$this->logMessage('error', "function(activateRoom):用户".$account_id." banker_scoreType error："." in file".__FILE__." on Line ".__LINE__);
				return array("result"=>"1","operation"=>$operation,"data"=>$result,"result_message"=>"上庄分数规则错误"); 
			}
		}

		//消耗房卡类型
		if(isset($data['ticket_type']) && $data['ticket_type'] == 1)
		{
			$spend_ticket_count = Game::Rule_TicketType_1;
		}
		else if(isset($data['ticket_type']) && $data['ticket_type'] == 2)
		{
			$spend_ticket_count = Game::Rule_TicketType_2;
		}
		else
		{
			$this->logMessage('error', "function(activateRoom):用户".$account_id." ticket_type error："." in file".__FILE__." on Line ".__LINE__);
			return array("result"=>"1","operation"=>$operation,"data"=>$result,"result_message"=>"房卡规则错误"); 
		}

		//默认底分
		if(isset($data['score_type']) && $data['score_type'] == 1)
		{
			$base_score = Config::Rule_ScoreType_1;
		}
		else if(isset($data['score_type']) && $data['score_type'] == 2)
		{
			$base_score = Config::Rule_ScoreType_2;
		}
		else if(isset($data['score_type']) && $data['score_type'] == 3)
		{
			$base_score = Config::Rule_ScoreType_3;
		}
		else if(isset($data['score_type']) && $data['score_type'] == 4)
		{
			$base_score = Config::Rule_ScoreType_4;
		}
		else if(isset($data['score_type']) && $data['score_type'] == 5)
		{
			$base_score = Config::Rule_ScoreType_5;
		}
        else if(isset($data['score_type']) && $data['score_type'] == 6)
        {
            $base_score = Config::Rule_ScoreType_6;
        }
		else
		{
			$this->logMessage('error', "function(activateRoom):用户".$account_id." score_type error："." in file".__FILE__." on Line ".__LINE__);
			return array("result"=>"1","operation"=>$operation,"data"=>$result,"result_message"=>"分数规则错误"); 
		}

		//规则
		$is_joker = (isset($data['is_joker']) && $data['is_joker'] == 1) ? 1 : 0;
		$is_bj = (isset($data['is_bj']) && $data['is_bj'] == 1) ? 1 : 0;

		$pay_type = Config::Ticket_Mode;

		//每轮局数
		$eachRound_totalNum = $spend_ticket_count * Config::GameNum_EachRound;	

		$MMYSQL = $this->initMysql();
		//判断房间申请记录是否存在
		$room_where = 'room_number='.$room_number;
		$room_sql = 'select room_id,account_id,is_close from '.Room.' where '.$room_where;
		$room_query = $MMYSQL->query($room_sql);
		if(!is_array($room_query) || count($room_query) == 0 )
		{
			$this->writeLog("function(activateRoom):room($room_number) not exist"." in file".__FILE__." on Line ".__LINE__);
			return array("result"=>1,"operation"=>$operation,"data"=>$result,"result_message"=>"房间不存在");  
		}else{
			$room_id = $room_query[0]['room_id'];
		}


		$room_status = $this->queryRoomStatus($room_id);
		if($room_status != Game::RoomStatus_Closed){
			return array("result"=>OPT_CONST::FAILED,"operation"=>$operation,"data"=>$result,"result_message"=>"房间已经被激活"); 
		}

		//判断房卡余额
		$my_ticket_count = $MMYSQL->select("ticket_count")->from("room_ticket")->where("account_id=".$account_id)->single();
		if($my_ticket_count >= $spend_ticket_count){
			
		} else {
			$this->writeLog("[$room_id] ($account_id) 牌券不足");
			$result['alert_type'] = 1;	//1房卡不足
			return array("result"=>"1","operation"=>$operation,"data"=>$result,"result_message"=>"房卡不足"); 
		}


		//添加房间信息到redis
		$Redis_Model = Redis_Model::getModelObject();
		
		$replyArr = array("[roomid]"=>$room_id);
		$Room_Key = strtr(Redis_Const::Room_Key, $replyArr);

		//激活房间，事务
		$success = $this->activateRoomTransaction($room_id);
		if(!$success){
			$this->writeLog("并发 activate game，忽略。room id:".$room_id."。in file".__FILE__." on Line ".__LINE__);
			return array("result"=>OPT_CONST::FAILED,"operation"=>$operation,"data"=>$result,"result_message"=>"房间已经被激活"); 
		}

		
		$r_mkv[Redis_Const::Room_Field_GameNum] = 0;			//游戏局数
		$r_mkv[Redis_Const::Room_Field_DefaultScore] = $base_score;		//开局默认分数
		$r_mkv[Redis_Const::Room_Field_ActiveUser] = -1;		//当前操作用户
		$r_mkv[Redis_Const::Room_Field_ActiveTimer] = -1;		//当前生效timer
		$r_mkv[Redis_Const::Room_Field_BaseScore] = $base_score;		//当前生效timer
		$r_mkv[Redis_Const::Room_Field_TicketCount] = $spend_ticket_count;		//每轮消耗房卡数量
		$r_mkv[Redis_Const::Room_Field_TotalNum] = $eachRound_totalNum;		//每轮总局数
		$r_mkv[Redis_Const::Room_Field_Creator] = $account_id;		//创建用户
		$r_mkv[Redis_Const::Room_Field_Scoreboard] = "";		//积分榜
		$r_mkv[Redis_Const::Room_Field_Paytype] = $pay_type;		///AA,2房主扣卡

        $r_mkv[Redis_Const::Room_Field_Is_Joker]= $is_joker;
        $r_mkv[Redis_Const::Room_Field_Is_Bj]   = $is_bj;

		$r_mkv[Redis_Const::Room_Field_BankerMode] = $banker_mode;		//庄家类型
		$r_mkv[Redis_Const::Room_Field_BankerScoreType] = $banker_scoreType;	//上庄分数类型
		$r_mkv[Redis_Const::Room_Field_BankerScore] = $banker_score;	//上庄分数
		$Redis_Model->hmsetField($Room_Key,$r_mkv);


		if(Game::PaymentType_Creator == $pay_type)
		{
			$this->logMessage('error', "function(activateRoom):PaymentType_Creator: ".$spend_ticket_count." in file".__FILE__." on Line ".__LINE__);
			//扣除房卡
			$this->balanceTicket($room_id, $account_id, $spend_ticket_count);
		}
		
		$result['room_id'] = $room_id;
		
		$this->writeLog("[$room_id] ($account_id) 激活房间");
		return array("result"=>OPT_CONST::SUCCESS,"operation"=>$operation,"data"=>$result,"result_message"=>"激活房间");  
	}


	protected function activateRoomTransaction($room_id)
	{
		$Redis_Model = Redis_Model::getModelObject();
		$replyArr = array("[roomid]"=>$room_id);
		$key = strtr(Redis_Const::Room_Key, $replyArr);

		$redisAuth = $Redis_Model->pingRedisAuth();
		if($redisAuth === G_CONST::EMPTY_STRING || !is_object($redisAuth))
		{
			$this->logMessage('error', "function(setHashTransaction):redisAuth is empty string"." in file".__FILE__." on Line ".__LINE__);
			return false;
		}

	    $success = false;
	    $options = array(
	        'cas'   => true,    // Initialize with support for CAS operations
	        'watch' => $key,    // Key that needs to be WATCHed to detect changes
	        'retry' => 3,       // Number of retries on aborted transactions, after
	                            // which the client bails out with an exception.
	    );

	    $redisAuth->transaction($options, function ($tx) use ($key, &$success) {
	        $room_status = $tx->hget($key, Redis_Const::Room_Field_Status);
	        if (isset($room_status) && $room_status == Game::RoomStatus_Closed) {
	            $tx->multi();   // With CAS, MULTI *must* be explicitly invoked.
	            $tx->hmset($key, array(Redis_Const::Room_Field_Status => Game::RoomStatus_Waiting));
	            $success =  true;

	        } else {
	        	$this->logMessage('error', "function(activateRoomTransaction):room_status error "." in file".__FILE__." on Line ".__LINE__);
	        	$success =  false;
	        }
	    });
	    return $success;
	}
	
	/*
		进入房间
	*/
	public function joinRoom($arrData)
	{
		$timestamp = time();
		$result = array();
		$return = array();
		
		$data 			= $arrData['data'];
		$operation 		= $arrData['operation'];
		$account_id 	= $arrData['account_id'];
		$client_id 		= $arrData['client_id'];

		$session 		= $arrData['session'];
		$Verification_Model = Verification_Model::getModelObject();
		if(false == $Verification_Model->checkRequestSession($account_id,$session))
		{
			return OPT_CONST::NO_RETURN;
		}
		
		if(!(isset($data['room_number'])&& $data['room_number'] > 0) )
		{
			$this->logMessage('error', "function(joinRoom):lack of room_number"." in file".__FILE__." on Line ".__LINE__);
			return $this->_missingPrameterArr($operation,"room_number");
		}
		
		$room_number = $data['room_number'];
		
		$MMYSQL = $this->initMysql();
		//判断房间申请记录是否存在
		$room_where = 'room_number='.$room_number;
		$room_sql = 'select room_id,account_id,is_close from '.Room.' where '.$room_where;
		$room_query = $MMYSQL->query($room_sql);
		if(!is_array($room_query) || count($room_query) == 0 )
		{
			$this->logMessage('error', "function(joinRoom):room($room_number) not exist"." in file".__FILE__." on Line ".__LINE__);
			return array("result"=>OPT_CONST::FAILED,"operation"=>$operation,"data"=>$result,"result_message"=>"房间不存在");  
		}
		else
		{
			$room_id = $room_query[0]['room_id'];
			if($room_query[0]['is_close'])
			{
				$result['room_status'] = 4;
				return array("result"=>"0","operation"=>$operation,"data"=>$result,"result_message"=>"房间已关闭"); 
			}
		}

		
		$Redis_Model = Redis_Model::getModelObject();
		$replyArr = array("[roomid]"=>$room_id,"[accountid]"=>$account_id);


		$RoomSequence_Key = strtr(Redis_Const::RoomSequence_Key, $replyArr);
		
		//总分数
		$RoomScore_Key = strtr(Redis_Const::RoomScore_Key, $replyArr);
		$RoomScore_Field_User = strtr(Redis_Const::RoomScore_Field_User, $replyArr);
			
		$rsSeq_score = $Redis_Model->getZscore($RoomSequence_Key,$account_id);
		//判断用户是否已加入房间
		if(Redis_CONST::DATA_NONEXISTENT !== $rsSeq_score)		//已加入
		{
			//获取分数
			$rScore_score = $Redis_Model->hgetField($RoomScore_Key,$RoomScore_Field_User);
			//获取用户所在位置
			$serial_num = $rsSeq_score;

			$account_status = $this->queryAccountStatus($room_id, $account_id);
            if ($account_status == Game::AccountStatus_Watch){
                $account_status = Game::AccountStatus_Notready;
                $this->updateAccountStatus($room_id, $account_id, $account_status);
            }
		}
		else	//未加入
		{
			//判断游戏人数
			$user_count = 0;
			//获取房间所有用户
			$sset_array['key'] = $RoomSequence_Key;
			$sset_array['WITHSCORES'] = "WITHSCORES";
			$gamer_query = $Redis_Model->getSortedSetLimitByAry($sset_array);
			if(Redis_CONST::DATA_NONEXISTENT !== $gamer_query)
			{
				$user_count = count($gamer_query);
			}
			if($user_count >= Config::GameUser_MaxCount)
			{
				$this->writeLog("function(joinRoom):room($room_number) 人数已满"." in file".__FILE__." on Line ".__LINE__);
				$result['alert_type'] = 2;
				return array("result"=>"1","operation"=>$operation,"data"=>$result,"result_message"=>"房间人数已满");  
			}
			
			$serial_num = -1;
			for($i=1;$i<=Config::GameUser_MaxCount;$i++)
			{
				if(array_search($i,$gamer_query) === false)
				{
					$serial_num = $i;
					break;
				}
			}
			
			if($serial_num == -1)
			{
				$this->logMessage('error', "function(joinRoom):serial_num($serial_num) 无法找到空桌位"." in file".__FILE__." on Line ".__LINE__);
				return array("result"=>OPT_CONST::FAILED,"operation"=>$operation,"data"=>$result,"result_message"=>"数据错误");  
			}


			$success = $this->joinRoomTransaction($room_id,$account_id,$serial_num);
			if(!$success){
				$this->writeLog("并发 join room，忽略。room id:".$room_id.",serial_num:".$serial_num.",account_id:".$account_id."。in file".__FILE__." on Line ".__LINE__);
				$result['alert_type'] = 2;
				return array("result"=>"1","operation"=>$operation,"data"=>$result,"result_message"=>"房间人数已满"); 
			}

			//$rSeq_mkv[$serial_num] = $account_id;
			//$zadd_result = $Redis_Model->zaddSet($RoomSequence_Key,$rSeq_mkv);
			
			//添加默认分数
			$rScore_score = 0;
			$rScore_mkv[$RoomScore_Field_User] = $rScore_score;
			$hmset_result = $Redis_Model->hmsetField($RoomScore_Key,$rScore_mkv);

			//首次进房初始状态
			$account_status =  Game::AccountStatus_Initial;
			$AccountStatus_Key = strtr(Redis_Const::AccountStatus_Key, $replyArr);
			$mkv[$account_id] = $account_status;
			$mset_result = $Redis_Model->hmsetField($AccountStatus_Key,$mkv);

        }

        //更新观战座位
        $RoomAudience_Key = strtr(Redis_Const::RoomAudience_Key, $replyArr);
        $rsAud_score = $Redis_Model->getZscore($RoomAudience_Key,$account_id);
        if (Redis_CONST::DATA_NONEXISTENT !== $rsAud_score){
            $this->updateAudienceInfo($room_id, $account_id, Game::AudienceStatus_off);
        }

		//绑定用户UID
		$room_aid = strtr(Game::RoomUser_UID, $replyArr);
		
		// //解绑当前客户端
		/*
		$client_array = Gateway::getClientIdByUid($room_aid);
		if(is_array($client_array) && count($client_array) > 0)
		{
			foreach($client_array as $bind_client)
			{
				$this->logMessage('error', "function(joinRoom):room_aid($room_aid) 多客户的登陆"." in file".__FILE__." on Line ".__LINE__);
				//推送强制下线
				$forceLogout_message = $this->_JSON(array("result"=>"-202","result_message"=>"您的账号在另一地点登陆，您被迫下线。")); 
				Gateway::sendToClient($bind_client, $forceLogout_message);
				//解绑连接
				//Gateway::closeClient($bind_client);
				Gateway::unbindUid($bind_client, $room_aid);
				Gateway::leaveGroup($bind_client, $room_id);
			}
		}
		*/
		
		Gateway::bindUid($client_id, $room_aid);
		//Gateway::bindUid($client_id, $account_id);
		Gateway::joinGroup($client_id, $room_id);
		
		$room_data = $this->queryRoomData($room_id);
		$game_num = $room_data[Redis_CONST::Room_Field_GameNum];
		$room_status = $room_data[Redis_CONST::Room_Field_Status];
		$room_ary['scoreboard'] = $room_data[Redis_CONST::Room_Field_Scoreboard];
		if($room_ary['scoreboard'] !== ""){
			$room_ary['scoreboard'] = json_decode($room_ary['scoreboard']);
		} else {
			$room_ary['scoreboard'] = new stdClass();
		}
		$room_ary['total_num'] = $room_data[Redis_CONST::Room_Field_TotalNum];
		$room_ary['base_score'] = $room_data[Redis_CONST::Room_Field_BaseScore];
		$banker_mode = $room_data[Redis_CONST::Room_Field_BankerMode];
		$ready_time = $room_data[Redis_CONST::Room_Field_ReadyTime];


		if($room_status == Game::RoomStatus_Waiting && $account_status != Game::AccountStatus_Initial && $account_status != Game::AccountStatus_Notready && $account_status != Game::AccountStatus_Ready)
		{
			//游戏未开始，用户状态不为 未准备/准备 ，状态异常，修复
			$account_status = $this->joinRoomFixAccountStatus($room_id,$account_id);
		}

		//$game_num = $this->queryGameNumber($room_id);
		//$room_status = $this->queryRoomStatus($room_id);
		$room_ary['room_id'] = $room_id;
		$room_ary['room_number'] = $room_number;
		$room_ary['room_status'] = $room_status;
		$room_ary['account_score'] = $rScore_score;
		$room_ary['account_status'] = $account_status;
		$room_ary['online_status'] = 1;
		$room_ary['serial_num'] = $serial_num;
		$room_ary['game_num'] = $game_num;
		//$room_ary['scoreboard'] = $this->queryScoreboard($room_id);
		//$room_ary['total_num'] = $this->queryTotalNum($room_id);
		//$room_ary['base_score'] = $this->queryBaseScore($room_id);


		//获取房间庄家模式
		//$banker_mode = $this->queryBankerMode($room_id);

		$card_info = array();
		$card_type = 0;
		if($account_status==Game::AccountStatus_Choose || $account_status == Game::AccountStatus_Notgrab || $account_status == Game::AccountStatus_Grab || $account_status == Game::AccountStatus_Bet)
		{
			$card_info = $this->queryCardInfo($room_id, $account_id);
			if($banker_mode != Game::BankerMode_SeenGrab)
			{
				$card_info[0] = "-1";
				$card_info[1] = "-1";
			}
			$card_info[2] = "-1";
		}
		else if($account_status==Game::AccountStatus_Show || $account_status == Game::AccountStatus_Notshow){	//已经看过牌的
			$card_info = $this->queryCardInfo($room_id, $account_id);
			$cards_result = $this->calculateCardValue($room_id, $account_id);
			$card_type = $cards_result['card_type'];
		}
		$room_ary['cards'] = $card_info;
		$room_ary['card_type'] = $card_type;
		$room_ary['can_break'] = 0;


		//是否庄家
		$is_banker = G_CONST::IS_FALSE;
		$banker_id = $this->queryBanker($room_id);
		if($banker_id == $account_id)
		{
			$is_banker = G_CONST::IS_TRUE;
		}
		//是否能主动下庄,0否1是
		if($banker_mode == Game::BankerMode_FixedBanker && $game_num >= 3 && $is_banker==G_CONST::IS_TRUE)
		{
			$room_ary['can_break'] = Config::Can_BreakRoom;
		}

		//推送房间信息
		$room_return = array("result"=>OPT_CONST::SUCCESS,"operation"=>$operation,"data"=>$room_ary,"result_message"=>"入房成功");
		$this->pushMessageToCurrentClient($room_return);
		
		//返回所有玩家状态给进房玩家
		$allGamer = $this->getGamerInfo($room_id);
        //增加观战的玩家
        $allAudience = $this->getAudienceInfo($room_id);
		if(is_array($allGamer) && is_array($allAudience))
		{
			$currentGamer_return = array("result"=>OPT_CONST::SUCCESS,"operation"=>"AllGamerInfo","data"=>$allGamer,"audience"=>$allAudience,"result_message"=>"所有玩家状态");
			$this->pushMessageToCurrentClient($currentGamer_return);

		}

		//推送当前玩家状态给其他玩家
		$currentGamer = $this->getGamerInfo($room_id,$account_id);
        //增加观战的玩家
        $curentAudience = $this->getAudienceInfo($room_id,$account_id);
		if(is_array($currentGamer))
		{
			$currentGamer_return = array("result"=>OPT_CONST::SUCCESS,"operation"=>"UpdateGamerInfo","data"=>$currentGamer,"audience"=>$curentAudience,"result_message"=>"某玩家状态");
			$this->pushMessageToGroup($room_id, $currentGamer_return, $client_id);
		}

		//显示房间目前的倒计时
		//$this->showRoomCountdown($room_id);
		$this->pushRoomCountdown($room_id,$ready_time,$game_num);

		//保存用户当前房间,用户ID
		$_SESSION['gaming_roomid'] = $room_id;
		$_SESSION['account_id'] = $account_id;

		$this->writeLog("[$room_id] ($account_id) 进入房间");
		return OPT_CONST::NO_RETURN;
	}

	protected function joinRoomTransaction($room_id,$account_id,$serial_num)
	{
		$Redis_Model = Redis_Model::getModelObject();
		$replyArr = array("[roomid]"=>$room_id);
		$key = strtr(Redis_Const::RoomSequence_Key, $replyArr);

		$redisAuth = $Redis_Model->pingRedisAuth();
		if($redisAuth === G_CONST::EMPTY_STRING || !is_object($redisAuth))
		{
			$this->logMessage('error', "function(joinRoomTransaction):redisAuth is empty string"." in file".__FILE__." on Line ".__LINE__);
			return false;
		}

	    $success = false;
	    $options = array(
	        'cas'   => true,    // Initialize with support for CAS operations
	        'watch' => $key,    // Key that needs to be WATCHed to detect changes
	        'retry' => 3,       // Number of retries on aborted transactions, after
	                            // which the client bails out with an exception.
	    );

	    $redisAuth->transaction($options, function ($tx) use ($key,$serial_num,$account_id, &$success) {
	    	$where = array('withscores'=>TRUE);
	    	$result = $tx->zrevrangebyscore($key,$serial_num,$serial_num,$where);
	        if(is_array($result) && count($result) == 0) {
	        	$tx->multi();   // With CAS, MULTI *must* be explicitly invoked.
				$zadd_result = $tx->zadd($key,$serial_num,$account_id);
	            $success =  true;
	        } else {
	        	//echo "room_status != 1".PHP_EOL;
	            $this->logMessage('error', "function(joinRoomTransaction):zrevrangebyscore error "." in file".__FILE__." on Line ".__LINE__);
	        	$success =  false;
	        }
	    });
	    return $success;
	}


    /*
           进入观战
       */
    public function audienceWatch($arrData)
    {
        $result         = array();
        $data 			= $arrData['data'];
        $operation 		= $arrData['operation'];
        $account_id 	= $arrData['account_id'];
        $client_id 		= $arrData['client_id'];

        $session 		= $arrData['session'];
        $Verification_Model = Verification_Model::getModelObject();
        if(false == $Verification_Model->checkRequestSession($account_id,$session))
        {
            return OPT_CONST::NO_RETURN;
        }

        if(!(isset($data['room_number']) && $data['room_number'] > 0) )
        {
            $this->logMessage('error', "function(audienceWatch):lack of room_number"." in file".__FILE__." on Line ".__LINE__);
            return $this->_missingPrameterArr($operation,"room_number");
        }

        $room_number = $data['room_number'];

        $MMYSQL      = $this->initMysql();
        //判断房间申请记录是否存在
        $room_where = 'room_number='.$room_number;
        $room_sql   = 'select room_id,account_id,is_close from '.Room.' where '.$room_where;
        $room_row   = $MMYSQL->row($room_sql);

        if( !$room_row ) {
            $this->logMessage('error', "function(audienceWatch):room($room_number) not exist"." in file".__FILE__." on Line ".__LINE__);
            return array("result"=>OPT_CONST::FAILED,"operation"=>$operation,"data"=>$result,"result_message"=>"房间不存在");
        } else {
            $room_id = $room_row['room_id'];
            if($room_row['is_close'])
            {
                $result['room_status'] = 4;
                return array("result"=>"0","operation"=>$operation,"data"=>$result,"result_message"=>"房间已关闭");
            }
        }


        $Redis_Model = Redis_Model::getModelObject();
        $replyArr    = array("[roomid]"=>$room_id,"[accountid]"=>$account_id);

        $RoomSequence_Key = strtr(Redis_Const::RoomSequence_Key, $replyArr);
        //总分数
        $RoomScore_Key = strtr(Redis_Const::RoomScore_Key, $replyArr);
        $RoomScore_Field_User = strtr(Redis_Const::RoomScore_Field_User, $replyArr);


        $rsSeq_score = $Redis_Model->getZscore($RoomSequence_Key,$account_id);
        $seat_num = 1;

        //获取房间庄家
        $banker_id = $this->queryBanker($room_id);
        if ($banker_id == $account_id) {
            return array("result" => 0, "operation" => $operation, "data" => array('room_status' => 4 , 'to_joinRoom' => 1), "result_message" => "庄家不允许加入观战");
        }

        $ticketStatus   = $this->queryTicketChecked($room_id, $account_id);
        $account_status = $this->queryAccountStatus($room_id, $account_id);

        if(Redis_CONST::DATA_NONEXISTENT !== $rsSeq_score)		//已加入游戏
        {
            //没开局玩过的用户离线，自动从房间中退出
            if (!$ticketStatus) {
                $RoomSequence_Key = strtr(Redis_Const::RoomSequence_Key, $replyArr);
                $zrem_result = $Redis_Model->zremSet($RoomSequence_Key, array($account_id));
                $account_status = Game::AccountStatus_Initial;
                $seat_num = $rsSeq_score;
                $this->updateAccountStatus2($room_id, $account_id, $account_status, 0);
            } else {
                if($account_status != Game::AccountStatus_Notready && $account_status != Game::AccountStatus_Initial && $account_status != Game::AccountStatus_Ready){
                    return array("result" => 0, "operation" => $operation, "data" => array('room_status' => 4 , 'to_joinRoom' => 1), "result_message" => "游戏中不允许加入观战");
                }

                $account_status = Game::AccountStatus_Watch;
                $seat_num = $rsSeq_score;

                //设置用户状态
                $this->updateAccountStatus2($room_id, $account_id, $account_status, 1);
                $this->writeLog("[$room_id] ($account_id) 离线");
            }
        }


        //获取玩家信息
        $info = array();
        $account_where = 'account_id="'.$account_id.'"';
        $account_sql = 'select nickname,headimgurl from '.WX_Account.' where '.$account_where;
        $account_query = $MMYSQL->query($account_sql);
        if(!is_array($account_query) || count($account_query) == 0 )
        {
            $this->logMessage('error', "function(getGamerInfo):account($account_id) not exist"." in file".__FILE__." on Line ".__LINE__);
            return false;
        }
        $info['account_id']	= $account_id;
        $info['nickname']   = $account_query[0]['nickname'];
        $info['headimgurl']	= $account_query[0]['headimgurl'];
        $info['seat_num']   = $seat_num;
        $info['status']     = Game::AudienceStatus_on;

        //加入观战
        $audience_count       = 0;
        $RoomAudience_key     = strtr(Redis_CONST::RoomAudience_Key, $replyArr);
        $RoomAudienceInfo_key = strtr(Redis_CONST::RoomAudienceInfo_Key, $replyArr);
        $sset_array['key']        = $RoomAudience_key;
        $sset_array['WITHSCORES'] = "WITHSCORES";
        $gamer_query = $Redis_Model->getSortedSetLimitByAry($sset_array);
        if(Redis_CONST::DATA_NONEXISTENT !== $gamer_query)
        {
            $audience_count = count($gamer_query);
        }
        if($audience_count >= Game::GameAudience_MaxCount)
        {
            $this->writeLog("function(audienceWatch):room($room_number) 人数已满"." in file".__FILE__." on Line ".__LINE__);
            $result['alert_type'] = 2;
            return array("result"=>"1","operation"=>$operation,"data"=>$result,"result_message"=>"观战人数已满");
        }


        $serial_num = -1;
        for($i = 1;$i <= Game::GameAudience_MaxCount; $i++)
        {
            if(array_search($i,$gamer_query) === false)
            {
                $serial_num = $i;
                break;
            }
        }
        if($serial_num == -1)
        {
            $this->logMessage('error', "function(audienceWatch):serial_num($serial_num) 无法找到空观战位"." in file".__FILE__." on Line ".__LINE__);
            return array("result"=>OPT_CONST::FAILED,"operation"=>$operation,"data"=>$result,"result_message"=>"数据错误");
        }

        $info['serial_num']             = $serial_num;
        $rAudience_mkv[$serial_num]     = $account_id;
        $rAudienceInfo_mkv[$serial_num] = json_encode($info);
        $Redis_Model->zaddSet($RoomAudience_key,$rAudience_mkv);
        $Redis_Model->zaddSet($RoomAudienceInfo_key,$rAudienceInfo_mkv);

        //绑定用户UID
        $room_aid = strtr(Game::RoomUser_UID, $replyArr);
        Gateway::bindUid($client_id, $room_aid);
        Gateway::joinGroup($client_id, $room_id);

        //推送房间信息
        $room_data      = $this->queryRoomData($room_id);
        $rScore_score   = $Redis_Model->hgetField($RoomScore_Key,$RoomScore_Field_User); //获取分数
        $game_num       = $room_data[Redis_CONST::Room_Field_GameNum];
        $room_status    = $room_data[Redis_CONST::Room_Field_Status];
        $room_ary['scoreboard'] = $room_data[Redis_CONST::Room_Field_Scoreboard];
        if($room_ary['scoreboard'] !== ""){
            $room_ary['scoreboard'] = json_decode($room_ary['scoreboard']);
        } else {
            $room_ary['scoreboard'] = new stdClass();
        }
        $room_ary['total_num']   = $room_data[Redis_CONST::Room_Field_TotalNum];
        $room_ary['base_score']  = $room_data[Redis_CONST::Room_Field_BaseScore];
        $banker_mode             = $room_data[Redis_CONST::Room_Field_BankerMode];
        $room_ary['room_id']     = $room_id;
        $room_ary['room_number'] = $room_number;
        $room_ary['room_status'] = $room_status;
        $room_ary['account_score']  = $rScore_score;
        $room_ary['account_status'] = $account_status;
        $room_ary['online_status']  = 1;
        $room_ary['serial_num']  = $serial_num;
        $room_ary['game_num']    = $game_num;
        $room_ary['seat_num']    = $seat_num;  //观战视角位置

        $card_info = array();
        $card_type = 0;
        if($account_status==Game::AccountStatus_Choose || $account_status == Game::AccountStatus_Notgrab || $account_status == Game::AccountStatus_Grab || $account_status == Game::AccountStatus_Bet)
        {
            $card_info = $this->queryCardInfo($room_id, $account_id);
            if($banker_mode != Game::BankerMode_SeenGrab)
            {
                $card_info[0] = "-1";
                $card_info[1] = "-1";
            }
            $card_info[2] = "-1";
        }
        else if($account_status==Game::AccountStatus_Show || $account_status == Game::AccountStatus_Notshow){	//已经看过牌的
            $card_info = $this->queryCardInfo($room_id, $account_id);
            $cards_result = $this->calculateCardValue($room_id, $account_id);
            $card_type = $cards_result['card_type'];
        }
        $room_ary['cards'] = $card_info;
        $room_ary['card_type'] = $card_type;
        $room_ary['can_break'] = 0;

        //是否庄家
        $is_banker = G_CONST::IS_FALSE;
        $banker_id = $this->queryBanker($room_id);
        if($banker_id == $account_id)
        {
            $is_banker = G_CONST::IS_TRUE;
        }

        //是否能主动下庄,0否1是
        if($banker_mode == Game::BankerMode_FixedBanker && $game_num >= 3 && $is_banker==G_CONST::IS_TRUE)
        {
            $room_ary['can_break'] = Config::Can_BreakRoom;
        }

        $room_return = array("result"=>OPT_CONST::SUCCESS,"operation"=>$operation,"data"=>$room_ary,"result_message"=>"加入观战成功");
        $this->pushMessageToCurrentClient($room_return);

        //返回所有玩家状态给进房玩家
        $allGamer       = $this->getGamerInfo($room_id);
        $allAudience    = $this->getAudienceInfo($room_id);
        if(is_array($allGamer) && is_array($allAudience))
        {
            $currentGamer_return = array("result"=>OPT_CONST::SUCCESS,"operation"=>"AllGamerInfo","data"=>$allGamer,"audience"=>$allAudience,"result_message"=>"所有玩家状态");
            $this->pushMessageToCurrentClient($currentGamer_return);

        }

        //推送当前观战玩家状态给其他玩家
        $curentGamer    = $ticketStatus ? $this->getGamerInfo($room_id,$account_id) : array();
        $curentAudience = $this->getAudienceInfo($room_id,$account_id);
        if(is_array($curentAudience))
        {
            $currentGamer_return = array("result"=>OPT_CONST::SUCCESS,"operation"=>"UpdateAudienceInfo","audience"=>$curentAudience,"result_message"=>"某玩家状态");
            count($curentGamer) > 0 && $currentGamer_return['data'] = $curentGamer;
            $this->pushMessageToGroup($room_id, $currentGamer_return, $client_id);
        }

        $room_status = $this->queryRoomStatus($room_id);
        if ($room_status == Game::RoomStatus_Waiting) {
            $ready_count = $this->queryReadyCount($room_id);
            if ($ready_count < 2) {
                //取消倒计时
                $this->deleteRoomTimer($room_id);
                $arr = array("result" => 0, "operation" => "CancelStartLimitTime", "data" => array(), "result_message" => "取消开局倒计时");
                $this->pushMessageToGroup($room_id, $arr);
            } else {
                $this->startGame($room_id);
            }
        }

        //保存用户当前房间,用户ID
        $_SESSION['gaming_roomid'] = $room_id;
        $_SESSION['account_id']    = $account_id;

        $this->writeLog("[$room_id] ($account_id) 进入观战");
        return OPT_CONST::NO_RETURN;

    }

    //获取观战用户的信息
    protected function getAudienceInfo($room_id,$account_id=-1)
    {
        $result = array();

        $Redis_Model = Redis_Model::getModelObject();
        $MMYSQL = $this->initMysql();

        $replyArr = array("[roomid]"=>$room_id,"[accountid]"=>$account_id);

        //房间玩家集合
        $RoomAudience_Key = strtr(Redis_Const::RoomAudience_Key, $replyArr);
        $RoomAudienceInfo_Key = strtr(Redis_Const::RoomAudienceInfo_Key, $replyArr);

        //获取房间所有用户
        $sset_array['key'] = $RoomAudience_Key;
        $sset_array['WITHSCORES'] = "WITHSCORES";
        $audience_query = $Redis_Model->getSortedSetLimitByAry($sset_array);

        $sset_array['key'] = $RoomAudienceInfo_Key;
        $sset_array['WITHSCORES'] = "WITHSCORES";
        $audienceInfo_query = $Redis_Model->getSortedSetLimitByAry($sset_array);

        if(Redis_CONST::DATA_NONEXISTENT !== $audience_query && Redis_CONST::DATA_NONEXISTENT !== $audienceInfo_query)
        {
            if ($account_id == -1){
                foreach($audienceInfo_query as $audienceInfo=>$serial_num) {
                    $result[] = json_decode($audienceInfo);
                }
            }else if(isset($audience_query[$account_id])){
                $serial_tmp = $audience_query[$account_id];
                foreach($audienceInfo_query as $audienceInfo=>$serial_num){
                    if($serial_tmp == $serial_num){
                        $result = json_decode($audienceInfo, true);
                        break;
                    }
                }
            }
        }

        return $result;
    }


	/*
		刷新房间
	*/
	public function pullRoomInfo($arrData)
	{
		$timestamp = time();
		$result = array();
		$return = array();
		
		$data 			= $arrData['data'];
		$operation 		= $arrData['operation'];
		$account_id 	= $arrData['account_id'];
		$client_id 		= $arrData['client_id'];

		$session 		= $arrData['session'];
		$Verification_Model = Verification_Model::getModelObject();
		if(false == $Verification_Model->checkRequestSession($account_id,$session))
		{
			return OPT_CONST::NO_RETURN;
		}
		
		if(!(isset($data['room_id'])&& $data['room_id'] > 0) )
		{
			$this->logMessage('error', "function(pullRoomInfo):lack of room_id"." in file".__FILE__." on Line ".__LINE__);
			return $this->_missingPrameterArr($operation,"room_id");
		}

		$room_id = $data['room_id'];
		$MMYSQL = $this->initMysql();
		
		$Redis_Model = Redis_Model::getModelObject();
		$replyArr = array("[roomid]"=>$room_id,"[accountid]"=>$account_id);

		$RoomSequence_Key = strtr(Redis_Const::RoomSequence_Key, $replyArr);
		
		//总分数
		$RoomScore_Key = strtr(Redis_Const::RoomScore_Key, $replyArr);
		$RoomScore_Field_User = strtr(Redis_Const::RoomScore_Field_User, $replyArr);
			
		$rsSeq_score = $Redis_Model->getZscore($RoomSequence_Key,$account_id);
		//判断用户是否已加入房间
		if(Redis_CONST::DATA_NONEXISTENT !== $rsSeq_score)		//已加入
		{
			//获取分数
			$rScore_score = $Redis_Model->hgetField($RoomScore_Key,$RoomScore_Field_User);
			//获取用户所在位置
			$serial_num = $rsSeq_score;

			$account_status = $this->queryAccountStatus($room_id, $account_id);
		}
		else	//未加入
		{
			$this->logMessage('error', "function(pullRoomInfo):account($account_id) not join room"." in file".__FILE__." on Line ".__LINE__);
			return array("result"=>OPT_CONST::FAILED,"operation"=>$operation,"data"=>$result,"result_message"=>"用户未加入房间"); 
		}

		$room_data = $this->queryRoomData($room_id);
		$game_num = $room_data[Redis_CONST::Room_Field_GameNum];
		$room_ary['total_num'] = $room_data[Redis_CONST::Room_Field_TotalNum];
		$room_ary['base_score'] = $room_data[Redis_CONST::Room_Field_BaseScore];
		$banker_mode = $room_data[Redis_CONST::Room_Field_BankerMode];

		//获取房间当前局数
		$room_ary['account_score'] = $rScore_score;
		$room_ary['account_status'] = $account_status;
		$room_ary['serial_num'] = $serial_num;
		$room_ary['ticket_checked'] = $this->queryTicketChecked($room_id, $account_id);

		$card_info = array();
		$card_type = 0;
		if($account_status==Game::AccountStatus_Choose || $account_status == Game::AccountStatus_Notgrab || $account_status == Game::AccountStatus_Grab || $account_status == Game::AccountStatus_Bet)
		{
			$card_info = $this->queryCardInfo($room_id, $account_id);
			if($banker_mode != Game::BankerMode_SeenGrab)
			{
				$card_info[0] = "-1";
				$card_info[1] = "-1";
			}
			$card_info[2] = "-1";
		}
		else if($account_status==Game::AccountStatus_Show || $account_status == Game::AccountStatus_Notshow){	//已经看过牌的
			$card_info = $this->queryCardInfo($room_id, $account_id);
			$cards_result = $this->calculateCardValue($room_id, $account_id);
			$card_type = $cards_result['card_type'];
		}
		$room_ary['cards'] = $card_info;
		$room_ary['card_type'] = $card_type;
		$room_ary['can_break'] = 0;

		//是否庄家
		$is_banker = G_CONST::IS_FALSE;
		$banker_id = $this->queryBanker($room_id);
		if($banker_id == $account_id)
		{
			$is_banker = G_CONST::IS_TRUE;
		}
		//是否能主动下庄,0否1是
		if($banker_mode == Game::BankerMode_FixedBanker && $game_num >= 3 && $is_banker==G_CONST::IS_TRUE)
		{
			$room_ary['can_break'] = Config::Can_BreakRoom;
		}

		//$room_ary['base_score'] = $this->queryBaseScore($room_id);
				
		//返回所有玩家状态给进房玩家
		$allGamer = $this->getGamerInfo($room_id);
		if(!is_array($allGamer))
		{
			$this->logMessage('error', "function(pullRoomInfo):room($room_number) no player"." in file".__FILE__." on Line ".__LINE__);
			return array("result"=>OPT_CONST::FAILED,"operation"=>$operation,"data"=>$result,"result_message"=>"房间没有其他用户"); 
		}

		$pull_return = array("result"=>OPT_CONST::SUCCESS,"operation"=>$operation,"data"=>$room_ary,"result_message"=>"拉取房间信息","all_gamer_info"=>$allGamer);
		$this->pushMessageToCurrentClient($pull_return);

		$this->logMessage('error', "function(pullRoomInfo):用户 $account_id 拉取房间 $room_id 信息"." in file".__FILE__." on Line ".__LINE__);
		$this->logMessage('error', "function(pullRoomInfo):pull_return:".json_encode($pull_return). " in file".__FILE__." on Line ".__LINE__);
		
		return OPT_CONST::NO_RETURN;
	}


	
	/*
		获取房间所有用户
	*/
	protected function getGamerInfo($room_id,$account_id=-1)
	{
		$result = array();
		
		$Redis_Model = Redis_Model::getModelObject();
		$MMYSQL = $this->initMysql();
		
		$replyArr = array("[roomid]"=>$room_id,"[accountid]"=>$account_id);

		//房间玩家集合
		$RoomSequence_Key = strtr(Redis_Const::RoomSequence_Key, $replyArr);
		$banker_mode = $this->queryBankerMode($room_id);
		
		//获取房间所有用户
		$sset_array['key'] = $RoomSequence_Key;
		$sset_array['WITHSCORES'] = "WITHSCORES";
		$gamer_query = $Redis_Model->getSortedSetLimitByAry($sset_array);
		if(Redis_CONST::DATA_NONEXISTENT !== $gamer_query)
		{
			foreach($gamer_query as $gamer_id=>$serial_num)
			{
				//获取玩家信息
				$account_where = 'account_id="'.$gamer_id.'"';
				$account_sql = 'select nickname,headimgurl from '.WX_Account.' where '.$account_where;
				$account_query = $MMYSQL->query($account_sql);
				if(!is_array($account_query) || count($account_query) == 0 )
				{
					$this->logMessage('error', "function(getGamerInfo):account($gamer_id) not exist"." in file".__FILE__." on Line ".__LINE__);
					return false;
				}
				$info['serial_num'] = $serial_num;
				$info['account_id'] = $gamer_id;
				$info['nickname'] = $account_query[0]['nickname'];
				$info['headimgurl'] = $account_query[0]['headimgurl'];
				
				
				//获取玩家当前积分
				$rScore_score = $this->queryAccountScore($room_id, $gamer_id);
				if(Redis_CONST::DATA_NONEXISTENT === $rScore_score)
				{
					$this->logMessage('error', "function(getGamerInfo):account($gamer_id) score not exist"." in file".__FILE__." on Line ".__LINE__);
					return false;
				}
				$info['account_score'] = $rScore_score;
				
				//获取玩家当前状态
				$rStatus = $this->queryAccountStatus($room_id, $gamer_id);
				if(Redis_CONST::DATA_NONEXISTENT === $rStatus)
				{
					$this->logMessage('error', "function(getGamerInfo):account($gamer_id) status not exist"." in file".__FILE__." on Line ".__LINE__);
					return false;
				}
				$info['account_status'] = $rStatus;
				$info['online_status'] = $this->queryOnlineStatus($room_id, $gamer_id);
				$info['ticket_checked'] = $this->queryTicketChecked($room_id, $gamer_id);

				//是否庄家
				$is_banker = G_CONST::IS_FALSE;
				$banker_id = $this->queryBanker($room_id);
				if($banker_id == $gamer_id)
				{
					$is_banker = G_CONST::IS_TRUE;
				}
				$info['is_banker'] = $is_banker;

				$card_info = array();
				$card_type = 0;
				if($rStatus == Game::AccountStatus_Show || $rStatus == Game::AccountStatus_Notshow){	//已经看过牌的
					$card_info = $this->queryCardInfo($room_id, $gamer_id);
					$cards_result = $this->calculateCardValue($room_id, $gamer_id,$card_info);
					$card_type = $cards_result['card_type'];
				}

				$info['cards'] = $card_info;
				$info['card_type'] = $card_type;
				$info['multiples'] = $this->queryPlayerMultiples($room_id, $gamer_id);

				$info['banker_multiples'] = "";
				if($banker_mode == Game::BankerMode_SeenGrab)
				{
					$banker_mult = $this->queryBankerMultiples($room_id);
					if($banker_mult != false)
					{
						$info['banker_multiples'] = $banker_mult;
					}
					
				}

				if ($banker_mode == Game::BankerMode_SeenGrab && $info['account_status'] == Game::AccountStatus_Grab) {
                    $info['banker_multiples'] = $this->queryGrabMultiples($room_id, $gamer_id);
                }

				if($account_id == $gamer_id)
				{
					return $info;
				}
				
				$result[] = $info;
			}
		}
		
		return $result;
	}


	/*
		进房之前的查询
	*/
	public function prepareJoinRoom($arrData)
	{
		$result = array();
		
		$data 			= $arrData['data'];
		$operation 		= $arrData['operation'];
		$account_id 	= $arrData['account_id'];

		$session 		= $arrData['session'];
		$Verification_Model = Verification_Model::getModelObject();
		if(false == $Verification_Model->checkRequestSession($account_id,$session))
		{
			return OPT_CONST::NO_RETURN;
		}
		
		if(!(isset($data['room_number'])&& $data['room_number'] > 0) )
		{
			$this->logMessage('error', "function(PrepareJoinRoom):lack of room_number"." in file".__FILE__." on Line ".__LINE__);
			return $this->_missingPrameterArr($operation,"room_number");
		}
		
		$room_number = $data['room_number'];
		
		$MMYSQL = $this->initMysql();
		//判断房间申请记录是否存在
		$room_where = 'room_number='.$room_number;
		$room_sql = 'select room_id,account_id,is_close from '.Room.' where '.$room_where;
		$room_query = $MMYSQL->query($room_sql);
		if(!is_array($room_query) || count($room_query) == 0 )
		{
			$this->writeLog("function(PrepareJoinRoom):room($room_number) not exist"." in file".__FILE__." on Line ".__LINE__);
			return array("result"=>OPT_CONST::FAILED,"operation"=>$operation,"data"=>$result,"result_message"=>"房间不存在");  
		}else{
			$room_id = $room_query[0]['room_id'];
			if($room_query[0]['is_close'])
			{
				$result['room_status'] = 4;
				return array("result"=>"0","operation"=>$operation,"data"=>$result,"result_message"=>"房间已关闭"); 
			}
		}

		$Redis_Model = Redis_Model::getModelObject();
		$replyArr = array("[roomid]"=>$room_id,"[accountid]"=>$account_id);
		$RoomSequence_Key = strtr(Redis_Const::RoomSequence_Key, $replyArr);

		$alert_text = "";
		$user_count = 0;

		$room_data = $this->queryRoomData($room_id);
        $spend_ticket_count = $room_data[Redis_CONST::Room_Field_TicketCount];

        $room_ary['room_status'] = $room_data[Redis_CONST::Room_Field_Status];
        $room_ary['banker_mode'] = $room_data[Redis_CONST::Room_Field_BankerMode];

		//规则
		$room_ary['user_count'] = $user_count;
		$room_ary['alert_text'] = $alert_text;
		$room_ary['ticket_type'] = $room_data[Redis_CONST::Room_Field_TicketCount];
		$room_ary['score_type'] = $room_data[Redis_CONST::Room_Field_BaseScore];
		$room_ary['is_joker'] = $room_data[Redis_CONST::Room_Field_Is_Joker];
		$room_ary['is_bj'] = $room_data[Redis_CONST::Room_Field_Is_Bj];

		//获取维护公告
		$alert_type = -1;
		$alert_text = "";
		$announcement_result = $this->getGameAnnouncement($account_id);
		if(is_array($announcement_result))
		{
			$alert_type = $announcement_result['alert_type'];
			$alert_text = $announcement_result['alert_text'];
		}
		if($alert_type == 4)
		{
			$room_ary['alert_type'] = $alert_type;
			return array("result"=>"1","operation"=>$operation,"data"=>$room_ary,"result_message"=>$alert_text); 
		}

		if(Game::PaymentType_AA == Config::Ticket_Mode)
		{
			$my_ticket_count = $MMYSQL->select("ticket_count")->from("room_ticket")->where("account_id=".$account_id)->single();

			if($my_ticket_count >= $spend_ticket_count || $this->queryTicketChecked($room_id, $account_id) >= 1){
				
			} else {
				$this->writeLog("[$room_id] ($account_id) 牌券不足");
				$room_ary['alert_type'] = 1;
				return array("result"=>"1","operation"=>$operation,"data"=>$room_ary,"result_message"=>"房卡不足"); 
			}
		}


		//房间人数
		$room_users = array();
		$is_member = false;
		//获取房间所有用户
		$sset_array['key'] = $RoomSequence_Key;
		$gamer_query = $Redis_Model->getSortedSetLimitByAry($sset_array);
		if(Redis_CONST::DATA_NONEXISTENT !== $gamer_query)
		{
			foreach($gamer_query as $gamer_id)
			{
				//获取玩家信息
				$account_where = 'account_id="'.$gamer_id.'"';
				$account_sql = 'select account_id,nickname from '.WX_Account.' where '.$account_where;
				$row = $MMYSQL->row($account_sql);
				if($row){
					$room_users[] = $row['nickname'];
					$user_count++;
					if($row['account_id'] == $account_id){
						$is_member = true;
					}
				} 
			}
		}

		if($user_count == 0 || $is_member){
			$alert_text = "";
		} else {
			if($user_count >= Config::GameUser_MaxCount){
				$this->writeLog("[$room_id] ($account_id)  PrepareJoinRoom 人数已满");
				$room_ary['alert_type'] = 2;
				return array("result"=>"1","operation"=>$operation,"data"=>$room_ary,"result_message"=>"房间人数已满");  
			} 

			$user_str = implode("、", $room_users);
			$alert_text = "房间中有".$user_str."，是否加入？";
		}

		$room_ary['user_count'] = $user_count;
		$room_ary['alert_text'] = $alert_text;

		return array("result"=>"0","operation"=>$operation,"data"=>$room_ary,"result_message"=>"进房询问");
	}
	
	
	/*
		准备操作
	*/
	public function readyStart($arrData)
	{
		$data 			= $arrData['data'];
		$operation 		= $arrData['operation'];
		$account_id 	= $arrData['account_id'];
		$session 		= $arrData['session'];

		$Verification_Model = Verification_Model::getModelObject();
		if(false == $Verification_Model->checkRequestSession($account_id,$session))
		{
			return OPT_CONST::NO_RETURN;
		}
		
		if(!isset($data['room_id']) || trim($data['room_id']) == G_CONST::EMPTY_STRING)
		{
			$this->logMessage('error', "function(readyStart):lack of room_id"." in file".__FILE__." on Line ".__LINE__);
			return $this->_missingPrameterArr($operation,"room_id");
		}
		
		$room_id = $data['room_id'];
		
		$Redis_Model = Redis_Model::getModelObject();
		$replyArr = array("[roomid]"=>$room_id,"[accountid]"=>$account_id);
		
		//获取玩家当前状态，是否未准备
		$rStatus = $this->queryAccountStatus($room_id, $account_id);
		if(Redis_CONST::DATA_NONEXISTENT === $rStatus || !in_array($rStatus, [Game::AccountStatus_Initial, Game::AccountStatus_Notready])  )
		{
			//广播用户状态改变
			$this->updateAccountStatus($room_id, $account_id, $rStatus);
			return OPT_CONST::NO_RETURN;
		}
		
		
		//更新用户状态
		$rStatus = Game::AccountStatus_Ready;
		$this->updateAccountStatus($room_id, $account_id, $rStatus);

		$this->startGame($room_id);
		
		return OPT_CONST::NO_RETURN;
	}
	
	/*
		历史积分榜
	*/
	public function historyScoreboard($arrData)
	{
		$result = array();
		
		$data 			= $arrData['data'];
		$operation 		= $arrData['operation'];
		$account_id 	= $arrData['account_id'];
		$client_id 		= $arrData['client_id'];
		
		$session 		= $arrData['session'];
		$Verification_Model = Verification_Model::getModelObject();
		if(false == $Verification_Model->checkRequestSession($account_id,$session))
		{
			return OPT_CONST::NO_RETURN;
		}

		$MMYSQL = $this->initMysql();

		if(isset($data['room_number']))
		{
			$room_number = $data['room_number'];
			//判断房间申请记录是否存在
			$room_where = 'room_number="'.$room_number.'"';
			$room_sql = 'select room_id,account_id,is_close from '.Room.' where '.$room_where;
			$room_query = $MMYSQL->query($room_sql);
			if(!is_array($room_query) || count($room_query) == 0 )
			{
				$this->writeLog("function(lastScoreboard):room($room_number) not exist"." in file".__FILE__." on Line ".__LINE__);
				return array("result"=>OPT_CONST::FAILED,"operation"=>$operation,"data"=>$result,"result_message"=>"房间不存在");  
			}else{
				$room_id = $room_query[0]['room_id'];
			}
		}
		else
		{
			if(!(isset($data['room_id'])&& $data['room_id'] > 0) )
			{
				$this->logMessage('error', "function(historyScoreboard):lack of room_id"." in file".__FILE__." on Line ".__LINE__);
				return $this->_missingPrameterArr($operation,"room_id");
			}
			$room_id = $data['room_id'];
		}
		
		$scoreboards = array();

		$sql = 'select * from room_scoreboard where room_id='.$room_id.' and game_type='.Game::Game_Type.' order by create_time desc limit 10';
		$query = $MMYSQL->query($sql);
		if(!is_array($query) )
		{
			$this->writeLog("function(historyScoreboard):room($room_id) not exist"." in file".__FILE__." on Line ".__LINE__);
		}else{
			foreach ($query as $row) {

				$name_board = array();
				$scoreboard = json_decode($row['board']);
				$create_time = $row['create_time'];
				
				if($scoreboard){
					foreach ($scoreboard as $account_id => $score) {
						$account_sql = 'select nickname from '.WX_Account.' where account_id ='.$account_id;
						$name = $MMYSQL->single($account_sql);
						$name_board[] = array('name'=>$name, 'score'=>$score);
					}
					$scoreboards[] = array('time'=>$create_time, 'scoreboard'=>$name_board);
				}
			}
		}
		
		return array("result"=>"0","operation"=>$operation,"data"=>$scoreboards,"result_message"=>"历史积分榜");  
	}

	/*
		最后一局积分榜
	*/
	public function lastScoreboard($arrData)
	{
		$result = array();
		
		$data 			= $arrData['data'];
		$operation 		= $arrData['operation'];
		$account_id 	= $arrData['account_id'];
		$session 		= $arrData['session'];

		$Verification_Model = Verification_Model::getModelObject();
		if(false == $Verification_Model->checkRequestSession($account_id,$session))
		{
			return OPT_CONST::NO_RETURN;
		}
		
		if(!(isset($data['room_number'])&& $data['room_number'] > 0) )
		{
			$this->logMessage('error', "function(lastScoreboard):lack of room_number"." in file".__FILE__." on Line ".__LINE__);
			return $this->_missingPrameterArr($operation,"room_number");
		}

		
		$room_number = $data['room_number'];
		$MMYSQL = $this->initMysql();
		//判断房间申请记录是否存在
		$room_where = 'room_number='.$room_number;
		$room_sql = 'select room_id,account_id,is_close from '.Room.' where '.$room_where;
		$room_query = $MMYSQL->query($room_sql);
		if(!is_array($room_query) || count($room_query) == 0 )
		{
			$this->writeLog("function(lastScoreboard):room($room_number) not exist"." in file".__FILE__." on Line ".__LINE__);
			return array("result"=>OPT_CONST::FAILED,"operation"=>$operation,"data"=>$result,"result_message"=>"房间不存在");  
		}else{
			$room_id = $room_query[0]['room_id'];
		}

		$scoreboards = new stdClass();
		$sql = 'select * from room_scoreboard where room_id='.$room_id.' and game_type='.Game::Game_Type.' order by create_time desc limit 1';
		$query = $MMYSQL->query($sql);
		if(!is_array($query) )
		{
			$this->writeLog("function(lastScoreboard):room($room_id) not exist"." in file".__FILE__." on Line ".__LINE__); 
		}else{
			foreach ($query as $row) {

				$name_board = array();
				$scoreboard = json_decode($row['board']);
				$create_time = $row['create_time'];
				$game_num = $row['game_num'];
				if($game_num <= 0)
				{
					$game_num = Config::GameNum_EachRound;
				}

				$total_num = "";
				$rule_text = $row['rule_text'];
				$rule_text_array = explode('局/',$rule_text); 
				if(is_array($rule_text_array) && count($rule_text_array) > 0)
				{
					$total_num = $rule_text_array[0];
				}

				if($scoreboard){
					foreach ($scoreboard as $account_id => $score) {
						$account_sql = 'select nickname from '.WX_Account.' where account_id ='.$account_id;
						$name = $MMYSQL->single($account_sql);
						$name_board[] = array('name'=>$name, 'score'=>$score, 'account_id'=>$account_id);
					}
					$scoreboards = array('time'=>$create_time, 'scoreboard'=>$name_board,'game_num'=>$game_num,'total_num'=>$total_num);
				}
			}
		}
		
		return array("result"=>"0","operation"=>$operation,"data"=>$scoreboards,"result_message"=>"历史积分榜");  
	}


	/*
		上传经纬度信息
	*/
	public function uploadGeo($arrData)
	{
		$data 			= $arrData['data'];
		$operation 		= $arrData['operation'];
		$account_id 	= $arrData['account_id'];
		$session 		= $arrData['session'];

		$Verification_Model = Verification_Model::getModelObject();
		if(false == $Verification_Model->checkRequestSession($account_id,$session))
		{
			return OPT_CONST::NO_RETURN;
		}
		
		if(!(isset($data['room_id'])&& $data['room_id'] > 0) )
		{
			$this->logMessage('error', "function(uploadGeo):lack of room_id"." in file".__FILE__." on Line ".__LINE__);
			return $this->_missingPrameterArr($operation,"room_id");
		}

		if(!isset($data['longitude']) || !isset($data['latitude']) )
		{
			$this->logMessage('error', "function(uploadGeo):lack of longitude or latitude"." in file".__FILE__." on Line ".__LINE__);
			return $this->_missingPrameterArr($operation,"longitude/latitude");
		}
		
		$room_id = $data['room_id'];
		$longitude = $data['longitude'];
		$latitude = $data['latitude'];

		$this->updateAccountGeo($room_id, $account_id, $longitude, $latitude);
		
		$msg_arr = array("result"=>0,"operation"=>"AccountGeo","data"=>array(
			'account_id'=>$account_id,
			'longitude'=>$longitude,
			'latitude'=>$latitude,
			),"result_message"=>"地理位置");

		$this->pushMessageToGroup($room_id, $msg_arr);
	}




	//{"operation":"TestDouble","account_id":"8","data":{"room_id":"8"}}
	// public function testDouble($arrData)
	// {
	// 	$timestamp = time();
	// 	$result = array();
	// 	$return = array();
		
	// 	$data 			= $arrData['data'];
	// 	$operation 		= $arrData['operation'];
	// 	$account_id 	= $arrData['account_id'];
	// 	$client_id 		= $arrData['client_id'];
		
	// 	if(!isset($data['room_id']) || trim($data['room_id']) == G_CONST::EMPTY_STRING)
	// 	{
	// 		$this->logMessage('error', "function(testDouble):lack of room_id"." in file".__FILE__." on Line ".__LINE__);
	// 		return $this->_missingPrameterArr($operation,"room_id");
	// 	}

	// 	$room_id = $data['room_id'];

	// 	$currentGamer_test = $this->getGamerInfo($room_id,$account_id);
	// 	$currentGamer_test['serial_num'] = 6;
	// 	$currentGamer_returnTest = array("result"=>OPT_CONST::SUCCESS,"operation"=>"UpdateGamerInfo","data"=>$currentGamer_test,"result_message"=>"某玩家状态");

	// 	$replyArr = array("[roomid]"=>$room_id,"[accountid]"=>$account_id);
	// 	$room_aid = strtr(Game::RoomUser_UID, $replyArr);

	// 	$this->pushMessageToAccount($room_aid, $currentGamer_returnTest);

	// 	return OPT_CONST::NO_RETURN;
	// }
	
	
	
}