<?php

use \GatewayWorker\Lib\Gateway;  

require_once dirname(__DIR__) . '/public.class.model.php';
class Play_Model extends Public_Model
{

	/*
		抢庄
	*/
	public function grabBanker($arrData)
	{
		$timestamp = time();
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

		if(!isset($data['room_id']) || $data['room_id']<=0)
		{
			$this->logMessage('error', "function(grabBanker):lack of room_id"." in file".__FILE__." on Line ".__LINE__);
			return $this->_missingPrameterArr($operation,"room_id");
		}
		if(!isset($data['is_grab']) || $data['is_grab'] === "")
		{
			$this->logMessage('error', "function(grabBanker):lack of is_grab"." in file".__FILE__." on Line ".__LINE__);
			return $this->_missingPrameterArr($operation,"is_grab");
		}

		$multiples = 1;	//抢庄倍数 默认1倍
		if(isset($data['multiples']) && in_array($data['multiples'], [Config::GrabCanBet_1,Config::GrabCanBet_2,Config::GrabCanBet_3]))
		{
			$multiples = $data['multiples'];
		}
		
		$room_id = $data['room_id'];
		$is_grab = $data['is_grab'];

		//获取当前游戏回合
		$circle = $this->queryCircle($room_id);
		if($circle != Game::Circle_Grab)
		{
			$this->logMessage('error', "function(grabBanker):account($account_id) circle($circle) error "." in file".__FILE__." on Line ".__LINE__);
			return array("result"=>OPT_CONST::FAILED,"operation"=>$operation,"data"=>$result,"result_message"=>"用户状态异常");
		}


		$room_data = $this->queryRoomData($room_id);
		$game_num = $room_data['gnum'];
		$room_status = $room_data['stat'];
		$banker_mode = $room_data['bankermode'];

		//获取房间庄家模式
		//$banker_mode = $this->queryBankerMode($room_id);
		if($banker_mode == Game::BankerMode_TenGrab || $banker_mode == Game::BankerMode_NoBanker)
		{
			$this->logMessage('error', "function(grabBanker): banker_mode($banker_mode) can not grab "." in file".__FILE__." on Line ".__LINE__);
			return array("result"=>OPT_CONST::FAILED,"operation"=>$operation,"data"=>$result,"result_message"=>"该模式不能抢庄");
		}
		//$game_num = $this->queryGameNumber($room_id);
		if($banker_mode == Game::BankerMode_FixedBanker && $game_num > 1)
		{
			$this->logMessage('error', "function(grabBanker): banker_mode($banker_mode) can not grab "." in file".__FILE__." on Line ".__LINE__);
			return array("result"=>OPT_CONST::FAILED,"operation"=>$operation,"data"=>$result,"result_message"=>"庄家已确定");
		}

		$account_status = $this->queryAccountStatus($room_id, $account_id);
		if(Redis_CONST::DATA_NONEXISTENT === $account_status || $account_status != Game::AccountStatus_Choose)
		{
			$this->logMessage('error', "function(grabBanker):account($account_id) status($account_status) error "." in file".__FILE__." on Line ".__LINE__);
			return array("result"=>OPT_CONST::FAILED,"operation"=>$operation,"data"=>$result,"result_message"=>"用户状态异常");
		}

		if($is_grab == G_CONST::IS_FALSE)
		{
			//不抢庄
			$status = Game::AccountStatus_Notgrab;
		}
		else if($is_grab == G_CONST::IS_TRUE)
		{
			//抢庄
			$status = Game::AccountStatus_Grab;
			//添加用户抢庄倍数
			$this->updateGrabMultiples($room_id, $account_id,$multiples);
		}
		else
		{
			$this->logMessage('error', "function(grabBanker):is grab error "." in file".__FILE__." on Line ".__LINE__);
			return array("result"=>OPT_CONST::FAILED,"operation"=>$operation,"data"=>$result,"result_message"=>"参数异常");
		}
		//设置用户状态
		$this->updateAccountStatus($room_id, $account_id, $status,$multiples);

		//是否开始叫分
		$is_bet = G_CONST::IS_TRUE;
		//获取所有游戏用户
		$player_array = $this->queryPlayMember($room_id);
		//获取游戏用户状态
		$account_array = $this->queryAccountStatusArray($room_id);

		foreach($player_array as $player_id)
		{
			if(!isset($account_array[$player_id]))
			{
				$is_bet = G_CONST::IS_FALSE;
				break;
			}
			$player_status = $account_array[$player_id];
			//获取游戏用户状态
			//$player_status = $this->queryAccountStatus($room_id, $player_id);
			if(!in_array($player_status, [Game::AccountStatus_Notgrab,Game::AccountStatus_Grab]))
			{
				$is_bet = G_CONST::IS_FALSE;
			}
		}

		if($is_bet == G_CONST::IS_TRUE)
		{
			$this->deleteRoomTimer($room_id);
			//选择庄家，开始下注回合
			$this->startBetRound($room_id);

		}

		return OPT_CONST::NO_RETURN;
	}



	/*
		选择叫分倍数
	*/
	public function chooseMultiples($arrData)
	{
		$timestamp = time();
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

		if(!isset($data['room_id']) || $data['room_id']<=0)
		{
			$this->logMessage('error', "function(chooseChip):lack of room_id"." in file".__FILE__." on Line ".__LINE__);
			return $this->_missingPrameterArr($operation,"room_id");
		}

        $timesArr = Game::TimesTypes;
        $timesKey = array_keys($timesArr);
        $times_type = $this->queryTimesType($data['room_id']);
        $multiples = $timesArr[$timesKey[0]][0];
        if (isset($timesArr[$times_type]) && in_array($data['multiples'], $timesArr[$times_type])) {
            $multiples = $data['multiples'];
        }

		$room_id = $data['room_id'];

		//获取当前游戏回合
		$circle = $this->queryCircle($room_id);
		if($circle != Game::Circle_Bet)
		{
			$this->logMessage('error', "function(grabBanker):account($account_id) circle($circle) error "." in file".__FILE__." on Line ".__LINE__);
			return array("result"=>OPT_CONST::FAILED,"operation"=>$operation,"data"=>$result,"result_message"=>"用户状态异常");
		}

		//判断用户状态
		$account_status = $this->queryAccountStatus($room_id, $account_id);
		if(Redis_CONST::DATA_NONEXISTENT === $account_status || $account_status != Game::AccountStatus_Bet)
		{
			$this->logMessage('error', "function(grabBanker):account($account_id) status($account_status) error "." in file".__FILE__." on Line ".__LINE__);
			return array("result"=>OPT_CONST::FAILED,"operation"=>$operation,"data"=>$result,"result_message"=>"用户状态异常");
		}

		$banker_id = $this->queryBanker($room_id);
		if($banker_id == $account_id)
		{
			$this->logMessage('error', "function(grabBanker):banker can not chooseMultiples "." in file".__FILE__." on Line ".__LINE__);
			return array("result"=>OPT_CONST::FAILED,"operation"=>$operation,"data"=>$result,"result_message"=>"用户状态异常");
		}


		//判断用户是否已叫分
		$multiples_result = $this->queryPlayerMultiples($room_id, $account_id);
		if($multiples_result > 0)
		{
			$this->logMessage('error', "function(chooseMultiples):player has choose :".$multiples_result." in file".__FILE__." on Line ".__LINE__);
			return array("result"=>OPT_CONST::FAILED,"operation"=>$operation,"data"=>$result,"result_message"=>"参数异常");
		}

		$this->updatePlayerMultiples($room_id, $account_id,$multiples);

		//通知用户叫倍数
		$msg_arr = array(
			'result' => 0,
			'operation' => 'UpdateAccountMultiples',
			'result_message' => "下注",
			'data' => array(
				'account_id' => $account_id,
				'multiples'  => $multiples
				)
			);
		$this->pushMessageToGroup($room_id, $msg_arr);

		//获取庄家ID
		//$banker_id = $this->queryBanker($room_id);

		$is_show = G_CONST::IS_TRUE;
		//判断是否全部已叫倍数
		$player_array = $this->queryPlayMember($room_id);
		foreach($player_array as $player_id)
		{
			if($player_id != $banker_id)
			{
				//获取游戏用户状态
				$player_status = $this->queryPlayerMultiples($room_id, $player_id);
				if($player_status <= 0)
				{
					$is_show = G_CONST::IS_FALSE;
					break;
				}
			}
		}

		if($is_show == G_CONST::IS_TRUE)
		{
			$this->deleteRoomTimer($room_id);
			//是否摊牌回合
			$this->startShowRound($room_id);
		}


		return OPT_CONST::NO_RETURN;
	}


	/*
		摊牌
	*/
	public function showCard($arrData)
	{
		$timestamp = time();
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

		if(!isset($data['room_id']) || $data['room_id']<=0)
		{
			$this->logMessage('error', "function(showCard):lack of room_id"." in file".__FILE__." on Line ".__LINE__);
			return $this->_missingPrameterArr($operation,"room_id");
		}
		
		$room_id = $data['room_id'];

		//获取当前游戏回合
		$circle = $this->queryCircle($room_id);
		if($circle != Game::Circle_Show)
		{
			$this->logMessage('error', "function(grabBanker):account($account_id) circle($circle) error "." in file".__FILE__." on Line ".__LINE__);
			return array("result"=>OPT_CONST::FAILED,"operation"=>$operation,"data"=>$result,"result_message"=>"用户状态异常");
		}

		//判断用户状态	
		$account_status = $this->queryAccountStatus($room_id, $account_id);
		if(Redis_CONST::DATA_NONEXISTENT === $account_status || $account_status != Game::AccountStatus_Notshow)
		{
			$this->logMessage('error', "function(grabBanker):account($account_id) status($account_status) error "." in file".__FILE__." on Line ".__LINE__);
			return array("result"=>OPT_CONST::FAILED,"operation"=>$operation,"data"=>$result,"result_message"=>"用户状态异常");
		}

		//修改用户状态
		$this->updateAccountStatus($room_id, $account_id, Game::AccountStatus_Show);

		$card_info = $this->queryCardInfo($room_id, $account_id);
		$cards_result = $this->caculateCardValue($room_id, $account_id,$card_info);
		$combo_array = $cards_result['combo_array'];
		$combo_point = $cards_result['combo_point'];
		$card_type = $cards_result['card_type'];

		//通知用户修改状态
		$msg_arr = array(
			'result' => 0,
			'operation' => 'UpdateAccountShow',
			'result_message' => "摊牌",
			'data' => array(
				'account_id' => $account_id,
				'cards'  => $card_info,
				'card_type'  => $card_type,
				'combo_array'  => $combo_array,
				'combo_point'  => $combo_point
				)
			);
		$this->pushMessageToGroup($room_id, $msg_arr);

		//判断是否结算
		$is_win = G_CONST::IS_TRUE;
		//判断是否全部已叫倍数
		$player_array = $this->queryPlayMember($room_id);
		foreach($player_array as $player_id)
		{
			//获取游戏用户状态
			$player_status = $this->queryAccountStatus($room_id, $player_id);
			if($player_status != Game::AccountStatus_Show)
			{
				$is_win = G_CONST::IS_FALSE;
				break;
			}
		}

		if($is_win == G_CONST::IS_TRUE)
		{
			$this->deleteRoomTimer($room_id);
			//进入结算环节
			$this->startWinRound($room_id);
		}

		return OPT_CONST::NO_RETURN;
	}


	/*
		下庄
	*/
	public function gameOver($arrData)
	{
		$timestamp = time();
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

		if(!isset($data['room_id']) || $data['room_id']<=0)
		{
			$this->logMessage('error', "function(gameOver):lack of room_id"." in file".__FILE__." on Line ".__LINE__);
			return $this->_missingPrameterArr($operation,"room_id");
		}
		
		$room_id = $data['room_id'];

		$room_data = $this->queryRoomData($room_id);
		$game_num = $room_data['gnum'];
		$room_status = $room_data['stat'];
		$banker_mode = $room_data['bankermode'];

		//庄家模式
		//$banker_mode = $this->queryBankerMode($room_id);
		if($banker_mode != Game::BankerMode_FixedBanker)
		{
			$this->logMessage('error', "function(gameOver):account($account_id) banker_mode($banker_mode)  "." in file".__FILE__." on Line ".__LINE__);
			return array("result"=>OPT_CONST::FAILED,"operation"=>$operation,"data"=>$result,"result_message"=>"房间模式不允许下庄");
		}

		//游戏局数
		//$game_num = $this->queryGameNumber($room_id);
		if($game_num < 3)
		{
			$this->logMessage('error', "function(gameOver):account($account_id) game_num($game_num)  "." in file".__FILE__." on Line ".__LINE__);
			return array("result"=>OPT_CONST::FAILED,"operation"=>$operation,"data"=>$result,"result_message"=>"局数大于3局才能下庄");
		}


		//判断用户状态	
		$account_status = $this->queryAccountStatus($room_id, $account_id);
		if(Redis_CONST::DATA_NONEXISTENT === $account_status || $account_status != Game::AccountStatus_Notready)
		{
			$this->logMessage('error', "function(gameOver):account($account_id) status($account_status) error "." in file".__FILE__." on Line ".__LINE__);
			return array("result"=>OPT_CONST::FAILED,"operation"=>$operation,"data"=>$result,"result_message"=>"用户状态异常");
		}

		$this->writeLog("[$room_id] ($account_id) 解散房间");
		//解散房间
		$array['room_id'] = $room_id;
		$array['banker_mode'] = $banker_mode;
		$array['winner_array'] = array();
		$array['loser_array'] = array();
		$array['type'] = 1;
		$this->breakRoom($array);

		

		return OPT_CONST::NO_RETURN;
	}



	/*
		发送声音
	*/
	public function broadcastVoice($arrData)
	{
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
		
		if(!isset($data['room_id']) || $data['room_id']<=0)
		{
			$this->logMessage('error', "function(broadcastVoice):lack of room_id"." in file".__FILE__." on Line ".__LINE__);
			return $this->_missingPrameterArr($operation,"room_id");
		}
		if(!isset($data['voice_num']) || $data['voice_num'] === "")
		{
			$this->logMessage('error', "function(broadcastVoice):lack of voice_num"." in file".__FILE__." on Line ".__LINE__);
			return $this->_missingPrameterArr($operation,"voice_num");
		}
		
		$room_id = $data['room_id'];
		$voice_num = $data['voice_num'];
		
		$this->writeLog("[$room_id] (".$account_id. ") 说". $voice_num);

		$msg_arr = array("result"=>"0","operation"=>$operation,"data"=>array(
			'account_id'=>$account_id,
			'voice_num' =>$voice_num
			),"result_message"=>"发送声音");
		$this->pushMessageToGroup($room_id, $msg_arr, $client_id);
		return OPT_CONST::NO_RETURN;
	}


	/*
		发送声音
	*/
	public function speakVoice($arrData)
	{
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
		
		if(!isset($data['room_id']) || $data['room_id']<=0)
		{
			$this->logMessage('error', "function(broadcastVoice):lack of room_id"." in file".__FILE__." on Line ".__LINE__);
			return $this->_missingPrameterArr($operation,"room_id");
		}
		
		$room_id = $data['room_id'];
		$local_id = $data['local_id'];
		
		//echo "用户".$account_id. " 说话". $local_id.PHP_EOL;
		$this->logMessage('error', "function(speakVoice):用户".$account_id. " 说话"." in file".__FILE__." on Line ".__LINE__);

		$msg_arr = array("result"=>"0","operation"=>$operation,"data"=>array(
			'account_id'=>$account_id,
			'local_id' =>$local_id
			),"result_message"=>"发送声音");
		$this->pushMessageToGroup($room_id, $msg_arr, $client_id);
		return OPT_CONST::NO_RETURN;
	}

	/*
	         观察员用于调试收取房间消息
	*/
	public function observer($arrData)
	{
		$data                   = $arrData['data'];
		$operation              = $arrData['operation'];
		$client_count = Gateway::getAllClientCount();
		return array("operation"=>$operation,"data"=>array('client_count'=>$client_count),"result_message"=>"当前在线连接总数");
	}
	
}