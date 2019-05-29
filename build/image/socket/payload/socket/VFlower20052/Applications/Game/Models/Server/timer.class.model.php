<?php

use \GatewayWorker\Lib\Gateway;  

require_once dirname(__DIR__) . '/public.class.model.php';
class Server_Timer_Model extends Public_Model
{
	
	/*
		
	*/
	public function setTimerInfo($arrData)
	{
		$operation = $arrData['operation'];
		$room_id = $arrData['room_id'];
		$data = $arrData['data'];

		$timer_id = $data['timer_id'];
		//保存timer_id
		$this->setTimerId($room_id, $timer_id);
		
		return true;
	}
	

	public function startGamePassive($arrData)
	{
		$operation = $arrData['operation'];
		$room_id = $arrData['room_id'];
		$data = $arrData['data'];

		$this->writeLog("[$room_id] 60s/10s被动开局");

		$timer_id = $data['timer_id'];

		$current_timer_id = $this->queryTimerId($room_id);
		if($current_timer_id == $timer_id){
			$this->updateTimer($room_id, -1);
		} else {
			//timer对不上，返回
			$this->writeLog("function(startGamePassive):timer对不上($current_timer_id != $timer_id)"." in file".__FILE__." on Line ".__LINE__);
			return false;
		}
		
		$this->startGame($room_id, true);

		return true;
	}

	public function discardPassive($arrData)
	{
		$operation = $arrData['operation'];
		$room_id = $arrData['room_id'];
		$data = $arrData['data'];

		$timer_id = $data['timer_id'];
		$account_id = $data['account_id'];

		$this->writeLog("[$room_id] ($account_id) 20s被动弃牌");

		$current_timer_id = $this->queryTimerId($room_id);
		if($current_timer_id == $timer_id){
			$this->updateTimer($room_id, -1);
		} else {
			//timer对不上，返回
			$this->writeLog("function(discardPassive):timer对不上($current_timer_id != $timer_id)"." in file".__FILE__." on Line ".__LINE__);
			return false;
		}


		$active_user = $this->queryActiveUser($room_id);
		if($active_user != $account_id){
			$this->writeLog("function(discardPassive):未轮到你操作"." in file".__FILE__." on Line ".__LINE__);
			return false;
		}
		
		//设置用户状态
		$status = Game::AccountStatus_Giveup;
		$this->updateAccountStatus($room_id, $account_id, $status);
		$next_account_id = $this->removeFromPlayMember($room_id, $account_id);

		$playCount = $this->queryPlayMemberCount($room_id);
		if($playCount < 2){	//剩者为胜
			//显示本局输赢
			$this->showWin($room_id, $account_id, $next_account_id, 2);  //win_type==2 弃牌而剩

		} else {
			
			$this->notyUserToBet($room_id, $next_account_id);
		}
		
		return true;
	}


	public function clearRoomPassive($arrData)
	{
		$operation = $arrData['operation'];
		$room_id = $arrData['room_id'];
		$data = $arrData['data'];

		$timer_id = $data['timer_id'];

		$this->writeLog("[$room_id] 触发60s自动清房间");

		// $current_timer_id = $this->queryTimerId($room_id);
		// if($current_timer_id == $timer_id){
		// 	$this->updateTimer($room_id, -1);
		// } else {
		// 	//timer对不上，返回
		// 	$this->writeLog("function(clearRoomPassive):timer对不上($current_timer_id != $timer_id)"." in file".__FILE__." on Line ".__LINE__);
		// 	return false;
		// }

		$clients_of_groupid = Gateway::getClientSessionsByGroup($room_id);
		//判断是否全部人离线
		if(count($clients_of_groupid) > 0)
		{
			$this->writeLog("function(clearRoomPassive):房间($room_id)有人不清理");
			return false;
		}

		$MMYSQL = $this->initMysql();
		$Redis_Model = Redis_Model::getModelObject();
		$replyArr = array("[roomid]"=>$room_id);
		$RoomScore_Key = strtr(Redis_Const::RoomScore_Key, $replyArr);

		$game_num = $this->queryGameNumber($room_id);
		$creator = $this->queryCreator($room_id);

		if(Config::Ticket_Mode==2 && $game_num==0 && $creator>0){
			$spend_ticket_count = $this->queryTicketCount($room_id);
			$this->balanceTicket($room_id, $creator, - $spend_ticket_count);
		}
		
		//保存积分榜
		if($game_num > 0){

			$Room_Key = strtr(Redis_Const::Room_Key, $replyArr);
			$round = $Redis_Model->hincrbyField($Room_Key, Redis_Const::Room_Field_GameRound, 1);
			$this->writeLog("[$room_id] 第".($round-1)."轮 结束!");

			$setting = $this->queryRoomSetting($room_id);
			//$round = $setting['ground'];
			$game_info['room_id'] = $room_id;
			$game_info['game_type'] = Game::Game_Type;
			$game_info['dealer_num'] = Config::Dealer_Num;
			$game_info['round'] = $round-1;
			
			if(isset($setting['scoreboard']) && $setting['scoreboard']){
				$board_json_str = $setting['scoreboard'];
				$scoreboard = json_decode($board_json_str, true); //转为关联数组
			} else {
				//积分榜为空
				$scoreboard = array();
				$board_json_str = "";
			}

			$name_board = array();

			// 按得分倒序排
			arsort($scoreboard, SORT_NUMERIC);

            // 计算消耗记录
            $club_id = $MMYSQL->select('club_id')->from(Room)->where('room_id='.$room_id)->single();
            $club_id = $club_id > 0 ? $club_id : 0;
            $set = $MMYSQL->select('winner1,winner2,winner3')->from(Club_ConsumesSets)->where('club_id', $club_id)->row();
            if (empty($set)) {
                $set = ['winner1'=>0,'winner2'=>0, 'winner3'=>0];
            }
            $i          = 1;
            $top[1] = $top[2] = 0;
            $dt = date('Y-m-d H:i:s');

			foreach ($scoreboard as $account_id => $score) {
                // 公会成员
                $clubPlayer = $MMYSQL->select('data_id,player_bean')->from(Club_Players)->where('club_id=' . $club_id . ' and player_id=' . $account_id)->row();
                if (empty($clubPlayer)) {
                    continue;
                }
                $consume = 0;
                $now_bean = $clubPlayer['player_bean'];
                if ($i == 1) {
                    if ($score > 0 && $set['winner1'] > 0) {
                        $top[1] = $score;
                        $consume  = floor($score * $set['winner1'] * 0.01); // 消耗豆数
                        $now_bean -= $consume;
                        $MMYSQL->update(Club_Players)->cols(['player_bean' => $now_bean, 'is_gaming' => 0, 'updated_at' => $dt])
                            ->where('data_id=' . $clubPlayer['data_id'])->query();

                        $consume_ary1 = [
                            'club_id'    => $club_id,
                            'player_id'  => $account_id,
                            'game_type'  => Game::Game_Type,
                            'room_id'    => $room_id,
                            'score'      => $score,
                            'rate'       => $set['winner1'],
                            'bean'       => $consume,
                            'created_at' => $dt,
                            'updated_at' => $dt
                        ];
                        $MMYSQL->insertReturnID(Club_Consumes, $consume_ary1);
                    }
                } elseif ($i == 2) {
                    if ($score > 0 && $set['winner2'] > 0) {
                        $top[2] = $score;
                        // 并列第一名
                        $rate = $top[2] == $top[1] ? $set['winner1'] : $set['winner2'];
                        $consume  = floor($score * $rate * 0.01); // 消耗豆数
                        $now_bean -= $consume;
                        $MMYSQL->update(Club_Players)->cols(['player_bean' => $now_bean, 'is_gaming' => 0, 'updated_at' => $dt])
                            ->where('data_id=' . $clubPlayer['data_id'])->query();

                        $consume_ary2 = [
                            'club_id'    => $club_id,
                            'player_id'  => $account_id,
                            'game_type'  => Game::Game_Type,
                            'room_id'    => $room_id,
                            'score'      => $score,
                            'rate'       => $rate,
                            'bean'       => $consume,
                            'created_at' => $dt,
                            'updated_at' => $dt
                        ];
                        $MMYSQL->insertReturnID(Club_Consumes, $consume_ary2);
                    }
                } elseif ($i == 3) {
                    if ($score > 0 && $set['winner3'] > 0) {
                        if ($score == $top[2]) {
                            $rate = $top[2] == $top[1] ? $set['winner1'] : $set['winner2']; // 并列第二名 或 三人并列第一名
                        } else {
                            $rate = $set['winner3'];
                        }
                        $consume  = floor($score * $rate * 0.01); // 消耗豆数
                        $now_bean -= $consume;
                        $MMYSQL->update(Club_Players)->cols(['player_bean' => $now_bean, 'is_gaming' => 0, 'updated_at' => $dt])
                            ->where('data_id=' . $clubPlayer['data_id'])->query();
                        $consume_ary3 = [
                            'club_id'    => $club_id,
                            'player_id'  => $account_id,
                            'game_type'  => Game::Game_Type,
                            'room_id'    => $room_id,
                            'score'      => $score,
                            'rate'       => $rate,
                            'bean'       => $consume,
                            'created_at' => $dt,
                            'updated_at' => $dt
                        ];
                        $MMYSQL->insertReturnID(Club_Consumes, $consume_ary3);
                    }
                }
                $i++;
                // 变化日志
                $real_score   = $score - $consume; // 实际得分
                $log_ary = [
                    'log_type'    => $score < 0 ? 4 : 3,
                    'content'     => $score < 0 ? '6人金花输' . (-$real_score) . '豆' : '6人金花赢' . $real_score . '豆',
                    'club_id'     => $club_id,
                    'player_id'   => $account_id,
                    'game_type'   => Game::Game_Type,
                    'room_id'     => $room_id,
                    'before_bean' => $now_bean - $real_score,
                    'change_bean' => $real_score,
                    'after_bean'  => $now_bean,
                    'created_at'  => $dt,
                    'updated_at'  => $dt
                ];
                $MMYSQL->insertReturnID(Club_Beans, $log_ary);


				$account_sql = 'select nickname from '.WX_Account.' where account_id ='.$account_id;
				$name = $MMYSQL->single($account_sql);
				$name_board[] = array('name'=>$name, 'score'=>$score, 'consume'=>$consume, 'account_id'=>$account_id);
                $account_array = [];
                $account_array['account_id'] = $account_id;
                $account_array['room_id']    = $room_id;
                $account_array['game_type']  = Game::Game_Type;
                $account_array['score']      = $score;
                $account_array['over_time']   = time();

                $MMYSQL->insertReturnID(Room_Account,$account_array);
			}

			//规则文本
			$rule_text = $this->formatRuleText($room_id);
			$balance_scoreboard = array('time'=>time(), 'scoreboard'=>$name_board,'game_num'=>$game_num);
			$balance_board_json_str = json_encode($balance_scoreboard['scoreboard']);

			$start_time = $this->queryStartTime($room_id);

			$board_array['start_time'] = $start_time;
			$board_array['create_time'] = time();
			$board_array['is_delete'] = G_CONST::IS_FALSE;
			$board_array['game_type'] = Game::Game_Type;
			$board_array['room_id'] = $room_id;
			$board_array['round'] = $round - 1;
			$board_array['game_num'] = $game_num;
			$board_array['rule_text'] = $rule_text;
			$board_array['board'] = $board_json_str;
			$board_array['balance_board'] = $balance_board_json_str;
			$board_id = $MMYSQL->insertReturnID(Room_Scoreboard,$board_array);

			//保存用户积分
			$game_info['score_board'] = $scoreboard;
			$this->saveAccountGameScore($game_info);

            //清理房间
            $this->clearRoom($room_id);
		}else{
            //清理房间
            $this->clearRoom($room_id, true);
        }

		return true;
	}
}