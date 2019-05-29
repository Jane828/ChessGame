<?php

use \GatewayWorker\Lib\Gateway;  

require_once dirname(__DIR__) . '/public.class.model.php';
class Server_Timer_Model extends Public_Model
{
	
	/***************************
			common function	
	***************************/
	
	
	
	
	
	/***************************
			logic function	
	***************************/
		
	
	/*
		
	*/
	public function updateTimerID($arrData)
	{
		//echo "updateTimerID";
		$operation = $arrData['operation'];
		$room_id = $arrData['room_id'];
		$data = $arrData['data'];

		$timer_id = $data['timer_id'];
		//保存timer_id
		$this->updateRoomTimerId($room_id, $timer_id);
		
		return true;
	}
	
	
	public function timeTigger($arrData)
	{
		//echo "timeTigger";
		print_r($arrData);
		
		return true;
	}

	public function a_callback_OP($arrData)
	{
		//echo "timeTigger";
		print_r($arrData);
		
		return true;
	}

	public function startGamePassive($arrData)
	{

		$operation = $arrData['operation'];
		$room_id = $arrData['room_id'];
		$data = $arrData['data'];

		$this->writeLog("[$room_id] 自动开局");

		$timer_id = $data['timer_id'];

		$current_timer_id = $this->queryRoomTimerId($room_id);
		if($current_timer_id == $timer_id){
			$this->updateRoomTimerId($room_id, -1);
			$this->updateReadyTime($room_id, -1);
		}
		else
		{
			$this->writeLog("function(startGamePassive):timer_id($current_timer_id) error:".$timer_id." in file".__FILE__." on Line ".__LINE__);
			return false;
		}

		$this->startGame($room_id, true);

		return true;
	}

	//抢庄时间结束自动进入下注模式
	public function grabPassive($arrData)
	{

		$operation = $arrData['operation'];
		$room_id = $arrData['room_id'];
		$data = $arrData['data'];

		$this->writeLog("[$room_id] 自动开启下注回合");

		$timer_id = $data['timer_id'];

		$current_timer_id = $this->queryRoomTimerId($room_id);
		if($current_timer_id == $timer_id){
			$this->updateRoomTimerId($room_id, -1);
			$this->updateReadyTime($room_id, -1);
		}
		else
		{
			$this->writeLog("function(grabPassive):timer_id($current_timer_id) error:".$timer_id." in file".__FILE__." on Line ".__LINE__);
			return false;
		}

		$this->grabPassiveOpt($room_id);
		// //设置用户状态
		// $Redis_Model = Redis_Model::getModelObject();
		// $replyArr = array("[roomid]"=>$room_id);
		// $AccountStatus_Key = strtr(Redis_Const::AccountStatus_Key, $replyArr);
		// $mkv = array();
		// $player_array = $this->queryPlayMember($room_id);
		// if(is_array($player_array) && count($player_array)){
		// 	foreach ($player_array as $account_id) {
		// 		$pre_status = $this->queryAccountStatus($room_id, $account_id);
		// 		if($pre_status == Game::AccountStatus_Choose)
		// 		{
		// 			$mkv[$account_id] = Game::AccountStatus_Notgrab;	//默认不抢
		// 		}
		// 	}
		// }
		// if(count($mkv) > 0)
		// {
		// 	$mset_result = $Redis_Model->hmsetField($AccountStatus_Key,$mkv);  //用户状态
		// }
		// //选择庄家，开始下注回合
		// $this->startBetRound($room_id);
		
		return true;
	}

	//下注时间结束自动进入摊牌模式
	public function betPassive($arrData)
	{
		$operation = $arrData['operation'];
		$room_id = $arrData['room_id'];
		$data = $arrData['data'];

		$this->writeLog("[$room_id] 自动开启摊牌回合");

		$timer_id = $data['timer_id'];

		$current_timer_id = $this->queryRoomTimerId($room_id);
		if($current_timer_id == $timer_id){
			$this->updateRoomTimerId($room_id, -1);
			$this->updateReadyTime($room_id, -1);
		}
		else
		{
			$this->writeLog("function(betPassive):timer_id($current_timer_id) error:".$timer_id." in file".__FILE__." on Line ".__LINE__);
			return false;
		}

		$this->betPassiveOpt($room_id);
		
		return true;
	}

	//摊牌时间结束自动进入结算
	public function showPassive($arrData)
	{
		$operation = $arrData['operation'];
		$room_id = $arrData['room_id'];
		$data = $arrData['data'];

		$this->writeLog("[$room_id] 自动开启结算回合");

		$timer_id = $data['timer_id'];

		$current_timer_id = $this->queryRoomTimerId($room_id);
		if($current_timer_id == $timer_id){
			$this->updateRoomTimerId($room_id, -1);
			$this->updateReadyTime($room_id, -1);
		}
		else
		{
			$this->writeLog("function(showPassive):timer_id($current_timer_id) error:".$timer_id." in file".__FILE__." on Line ".__LINE__);
			return false;
		}

		//进入结算环节
		$this->startWinRound($room_id);
		
		return true;
	}


	//清理房间
	public function clearRoomPassive($arrData)
	{
		$operation = $arrData['operation'];
		$room_id = $arrData['room_id'];
		$data = $arrData['data'];

		$timer_id = $data['timer_id'];

		$this->writeLog("[$room_id] 5分钟已过去 自动清房间");

/*
		$current_timer_id = $this->queryRoomTimerId($room_id);
		if($current_timer_id == $timer_id){
			$this->updateRoomTimerId($room_id, -1);
			$this->updateReadyTime($room_id, -1);
		} else {
			//timer对不上，返回
			$this->writeLog("function(clearRoomPassive):timer_id($current_timer_id) error:".$timer_id." in file".__FILE__." on Line ".__LINE__);
			return false;
		}
*/

		$clients_of_groupid = Gateway::getClientSessionsByGroup($room_id);
		//判断是否全部人离线
		if(count($clients_of_groupid) > 0)
		{
			$this->writeLog("[$room_id] 房间有人不清理");
			return false;
		}

		$Redis_Model = Redis_Model::getModelObject();
		$replyArr = array("[roomid]"=>$room_id);
		$Room_Key = strtr(Redis_Const::Room_Key, $replyArr);

		$room_data = $this->queryRoomData($room_id);
		$game_num = $room_data['gnum'];
		$start_time = $room_data[Redis_CONST::Room_Field_StartTime];
		//$round = $room_data[Redis_Const::Room_Field_GameRound];
		$creator = $room_data[Redis_Const::Room_Field_Creator];
		$banker_mode = $room_data[Redis_Const::Room_Field_BankerMode];
		$banker_score = $room_data['bankerscore'];

		//$game_num = $this->queryGameNumber($room_id);
		//$creator = $this->queryCreator($room_id);

		$pay_type = $this->queryPayType($room_id);
		if(Game::PaymentType_Creator == $pay_type && $game_num==0 && $creator>0)	//退换给房主
		{
			$this->writeLog("[$room_id] (".$creator.") 退还房卡");
			$spend_ticket_count = $this->queryTicketCount($room_id);
			$this->balanceTicket($room_id, $creator, - $spend_ticket_count);
		}

		//保存积分榜
		if($game_num > 0){

			$round = $Redis_Model->hincrbyField($Room_Key, Redis_Const::Room_Field_GameRound, 1);

			$game_info['room_id'] = $room_id;
			$game_info['game_type'] = Game::Game_Type;
			$game_info['dealer_num'] = Config::Dealer_Num;
			$game_info['round'] = $round-1;

			$this->writeLog("[$room_id] 第".($round-1)."轮 结束!");

			$MMYSQL = $this->initMysql();

			$banker_id = $this->queryBanker($room_id);
			//$banker_score = $this->queryBankerScore($room_id);

			//获取积分榜
			$RoomScore_Key = strtr(Redis_Const::RoomScore_Key, $replyArr);
			$scoreboard = $Redis_Model->hgetallField($RoomScore_Key);
			$board_json_str = "";
			$ticket_checked_user = $this->queryTicketCheckedUser($room_id);

			foreach ($scoreboard as $key => $value) {
				if(!in_array($key, $ticket_checked_user)){
					//未扣房卡的用户不显示在积分榜上
					unset($scoreboard[$key]);
				}
			}
			$save_scoreboard = $scoreboard;

			foreach ($scoreboard as $account_id => $score) {
				$account_sql = 'select nickname from '.WX_Account.' where account_id ='.$account_id;
				$name = $MMYSQL->single($account_sql);
				if($banker_mode == Game::BankerMode_FixedBanker && $banker_id == $account_id)
				{
					$score -= $banker_score;
					$save_scoreboard[$account_id] = $score;
				}
				$name_board[] = array('name'=>$name, 'score'=>$score, 'account_id'=>$account_id);

                $account_array = [];
                $account_array['account_id'] = $account_id;
                $account_array['room_id']    = $room_id;
                $account_array['game_type']  = Game::Game_Type;
                $account_array['score']      = $score;
                $account_array['over_time']   = time();

                $MMYSQL->insertReturnID(Room_Account,$account_array);
			}
			$balance_scoreboard = array('time'=>time(), 'scoreboard'=>$name_board,'game_num'=>$game_num);


			$board_json_str = json_encode($save_scoreboard);
			$balance_board_json_str = json_encode($balance_scoreboard['scoreboard']);

			//规则文本
			$rule_text = $this->formatRuleText($room_id,$room_data);

			$board_array['start_time'] = $start_time;
			$board_array['create_time'] = time();
			$board_array['is_delete'] = G_CONST::IS_FALSE;
			$board_array['game_type'] = Game::Game_Type;  //游戏6 麻将
			$board_array['room_id'] = $room_id;
			$board_array['round'] = $round-1;
			$board_array['game_num'] = $game_num;
			$board_array['board'] = $board_json_str;
			$board_array['balance_board'] = $balance_board_json_str;
			$board_array['rule_text'] = $rule_text;
			$board_id = $MMYSQL->insertReturnID(Room_Scoreboard,$board_array);

			//保存用户积分
			$game_info['score_board'] = $save_scoreboard;
			$this->saveAccountGameScore($game_info);

            $this->initRoomData($room_id);
		}else{
		    //关闭并且删除房间
            $this->initRoomData($room_id, true);
        }


		
		$this->writeLog("[$room_id] 自动清理完毕!");

		return true;
	}







}