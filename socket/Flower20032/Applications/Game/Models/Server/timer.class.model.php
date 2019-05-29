<?php

use \GatewayWorker\Lib\Gateway;

require_once dirname(__DIR__) . '/public.class.model.php';

class Server_Timer_Model extends Public_Model {

    /*

    */
    public function setTimerInfo($arrData) {
        $operation = $arrData['operation'];
        $room_id   = $arrData['room_id'];
        $data      = $arrData['data'];

        $timer_id = $data['timer_id'];
        //保存timer_id
        $this->setTimerId($room_id, $timer_id);

        return TRUE;
    }


    public function startGamePassive($arrData) {
        $operation = $arrData['operation'];
        $room_id   = $arrData['room_id'];
        $data      = $arrData['data'];

        $this->writeLog("[$room_id] 60s/10s被动开局");

        $timer_id = $data['timer_id'];

        $current_timer_id = $this->queryTimerId($room_id);
        if ($current_timer_id == $timer_id) {
            $this->updateTimer($room_id, -1);
        } else {
            //timer对不上，返回
            $this->writeLog("function(startGamePassive):timer对不上($current_timer_id != $timer_id)" . " in file" . __FILE__ . " on Line " . __LINE__);
            return FALSE;
        }

        $this->startGame($room_id, TRUE);

        return TRUE;
    }

    public function discardPassive($arrData) {
        $operation = $arrData['operation'];
        $room_id   = $arrData['room_id'];
        $data      = $arrData['data'];

        $timer_id   = $data['timer_id'];
        $account_id = $data['account_id'];

        $this->writeLog("[$room_id] ($account_id) 20s被动弃牌");

        $current_timer_id = $this->queryTimerId($room_id);
        if ($current_timer_id == $timer_id) {
            $this->updateTimer($room_id, -1);
        } else {
            //timer对不上，返回
            $this->writeLog("function(discardPassive):timer对不上($current_timer_id != $timer_id)" . " in file" . __FILE__ . " on Line " . __LINE__);
            return FALSE;
        }


        $active_user = $this->queryActiveUser($room_id);
        if ($active_user != $account_id) {
            $this->writeLog("function(discardPassive):未轮到你操作" . " in file" . __FILE__ . " on Line " . __LINE__);
            return FALSE;
        }

        //设置用户状态
        $status = Game::AccountStatus_Giveup;
        $this->updateAccountStatus($room_id, $account_id, $status);
        $next_account_id = $this->removeFromPlayMember($room_id, $account_id);

        $playCount = $this->queryPlayMemberCount($room_id);
        if ($playCount < 2) {    //剩者为胜
            //显示本局输赢
            $this->showWin($room_id, $account_id, $next_account_id, 2);  //win_type==2 弃牌而剩

        } else {

            $this->notyUserToBet($room_id, $next_account_id);
        }

        return TRUE;
    }


    public function clearRoomPassive($arrData) {
        $operation = $arrData['operation'];
        $room_id   = $arrData['room_id'];
        $data      = $arrData['data'];

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
        if (count($clients_of_groupid) > 0) {
            $this->writeLog("function(clearRoomPassive):房间($room_id)有人不清理");
            return FALSE;
        }

        $MMYSQL        = $this->initMysql();
        $Redis_Model   = Redis_Model::getModelObject();
        $replyArr      = array("[roomid]" => $room_id);
        $RoomScore_Key = strtr(Redis_Const::RoomScore_Key, $replyArr);

        $game_num = $this->queryGameNumber($room_id);
        $creator  = $this->queryCreator($room_id);

        if (Config::Ticket_Mode == 2 && $game_num == 0 && $creator > 0) {
            $spend_ticket_count = $this->queryTicketCount($room_id);
            $this->balanceTicket($room_id, $creator, -$spend_ticket_count);
        }

        //保存积分榜
        if ($game_num > 0) {

            $Room_Key = strtr(Redis_Const::Room_Key, $replyArr);
            $round    = $Redis_Model->hincrbyField($Room_Key, Redis_Const::Room_Field_GameRound, 1);
            $this->writeLog("[$room_id] 第" . ($round - 1) . "轮 结束!");

            $setting = $this->queryRoomSetting($room_id);
            //$round = $setting['ground'];
            $game_info['room_id'] = $room_id;
//			$game_info['game_type'] = Game::Game_Type;
            $game_info['game_type']  = $this->queryGameType($room_id);
            $game_info['dealer_num'] = Config::Dealer_Num;
            $game_info['round']      = $round - 1;

            $scoreboard = array();
            if (isset($setting['scoreboard']) && $setting['scoreboard']) {
                $board_json_str = $setting['scoreboard'];
                $scoreboard     = json_decode($board_json_str, TRUE); //转为关联数组
            } else {
                //积分榜为空
                $scoreboard     = array();
                $board_json_str = "";
            }
            $name_board = array();
            $game_type  = $this->queryGameType($room_id);
            foreach ($scoreboard as $account_id => $score) {
                $account_sql  = 'select nickname from ' . WX_Account . ' where account_id =' . $account_id;
                $name         = $MMYSQL->single($account_sql);
                $name_board[] = array('name' => $name, 'score' => $score, 'account_id' => $account_id);

                $account_array               = [];
                $account_array['account_id'] = $account_id;
                $account_array['room_id']    = $room_id;
//                $account_array['game_type']  = Game::Game_Type;
                $account_array['game_type'] = $game_type;
                $account_array['score']     = $score;
                $account_array['over_time'] = time();

                $MMYSQL->insertReturnID(Room_Account, $account_array);
            }

            //规则文本
            $rule_text              = $this->formatRuleText($room_id);
            $balance_scoreboard     = array('time' => time(), 'scoreboard' => $name_board, 'game_num' => $game_num);
            $balance_board_json_str = json_encode($balance_scoreboard['scoreboard']);

            $start_time = $this->queryStartTime($room_id);
            $game_type  = $this->queryGameType($room_id);

            $board_array['start_time']  = $start_time;
            $board_array['create_time'] = time();
            $board_array['is_delete']   = G_CONST::IS_FALSE;
//			$board_array['game_type'] = Game::Game_Type;
            $board_array['game_type']     = $game_type;
            $board_array['room_id']       = $room_id;
            $board_array['round']         = $round - 1;
            $board_array['game_num']      = $game_num;
            $board_array['rule_text']     = $rule_text;
            $board_array['board']         = $board_json_str;
            $board_array['balance_board'] = $balance_board_json_str;
            $board_id                     = $MMYSQL->insertReturnID(Room_Scoreboard, $board_array);

            //保存用户积分
            $game_info['score_board'] = $scoreboard;
            $this->saveAccountGameScore($game_info);

            //清理房间
            $this->clearRoom($room_id);
        } else {
            //清理房间
            $this->clearRoom($room_id, TRUE);
        }

        return TRUE;
    }
}