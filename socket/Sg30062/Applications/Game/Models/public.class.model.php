<?php


use \GatewayWorker\Lib\Gateway;

include_once(dirname(__DIR__) . '/Module/Verification.class.php');
include_once(dirname(__DIR__) . '/Module/Socket.class.php');
include_once(dirname(__DIR__) . '/Module/Redis.class.php');
require_once dirname(__DIR__) . '/base.class.model.php';

class Public_Model extends Base_Model {

    /***********************************
     * logic_function
     ***********************************/
    //开局
    protected function startGame($room_id, $passive_by_timer = FALSE) {
        $timestamp   = time();
        $Redis_Model = Redis_Model::getModelObject();
        $replyArr    = array("[roomid]" => $room_id);

        $Room_Key = strtr(Redis_Const::Room_Key, $replyArr);
        $Play_Key = strtr(Redis_Const::Play_Key, $replyArr);

        $room_data          = $this->queryRoomData($room_id);
        $game_num           = $room_data[Redis_CONST::Room_Field_GameNum];
        $room_status        = $room_data[Redis_CONST::Room_Field_Status];
        $banker_mode        = $room_data[Redis_CONST::Room_Field_BankerMode];
        $spend_ticket_count = $room_data[Redis_CONST::Room_Field_TicketCount];

        //$room_status = $this->queryRoomStatus($room_id);
        if ($room_status == Game::RoomStatus_Playing) {
            return FALSE;
        }

        //修复异常状态的用户
        $this->startGameFixAccountStatus($room_id);

        $ready_user    = $this->queryReadyUser($room_id);
        $watch_user    = $this->queryWatchUser($room_id);
        $in_room_array = $this->queryInRoomUser($room_id);
        $ready_count   = count($ready_user);
        $online_count  = count($in_room_array);
        $watch_count   = count($watch_user);

        //获取房间庄家模式
        //$banker_mode = $this->queryBankerMode($room_id);
        $banker_id = $this->queryBanker($room_id);

        //大于一个回合，庄家不准备不开始游戏
        //游戏局数
        //$game_num = $this->queryGameNumber($room_id);
        if (($banker_mode == Game::BankerMode_FixedBanker || $banker_mode == Game::BankerMode_TenGrab) && $game_num >= 1) {
            $banker_online_status = $this->queryOnlineStatus($room_id, $banker_id);
            $banker_status        = $this->queryAccountStatus($room_id, $banker_id);
            if ($banker_status != Game::AccountStatus_Ready || $banker_online_status == 0) {
                $this->writeLog("[$room_id] 庄家(" . $banker_id . ") 未准备 不能开局");
                return FALSE;
            }
        }


        //准备人数 大于等于2 且 准备人数 等于 在线人数
        $this->writeLog("[$room_id] 准备:" . $ready_count . "人  " . "在线:" . $online_count . "人 " . "观战:${watch_count}人");

        if ($ready_count >= 2) {
            if ($passive_by_timer || ($ready_count + $watch_count) == $online_count) {

                $room_members = $this->queryRoomMembers($room_id);

                $ready_in_room_user = array_intersect($ready_user, $in_room_array); //在房的已准备用户
                if (count($ready_in_room_user) < 2) {
                    $this->logMessage('error', "function(startGame):在房" . $room_id . "的已准备用户数量:" . count($ready_in_room_user) . " 不能开局 in file" . __FILE__ . " on Line " . __LINE__);
                    return FALSE;
                }
                $readyUser_ary = array();
                foreach ($room_members as $account_id) {
                    if (in_array($account_id, $ready_in_room_user)) {
                        $readyUser_ary[] = $account_id;
                    }
                }

                $success = $this->setHashTransaction($room_id);
                if (!$success) {
                    $this->writeLog("并发 start game，忽略。room id:" . $room_id . "。in file" . __FILE__ . " on Line " . __LINE__);
                    return FALSE;
                }

                if (!$passive_by_timer) {
                    //取消倒计时
                    $this->deleteRoomTimer($room_id);
                }

                //开始游戏
                //扣房卡
                $ticket_checked_user = $this->queryTicketCheckedUser($room_id);
                $to_check_user_array = array_diff($readyUser_ary, $ticket_checked_user);

                if (count($to_check_user_array) > 0) {
                    $pay_type = $this->queryPayType($room_id);
                    //$spend_ticket_count = $this->queryTicketCount($room_id);

                    if (Game::PaymentType_AA == $pay_type) {
                        $MMYSQL = $this->initMysql();
                        $in_str = implode(",", $to_check_user_array);
                        $res    = $MMYSQL->update(Room_Ticket)->set("ticket_count", "ticket_count-" . $spend_ticket_count)->where("account_id in (" . $in_str . " ) ")->query();

                        foreach ($to_check_user_array as $account_id) {
                            //获取流水账越
                            $balance       = 0;
                            $balance_where = 'account_id=' . $account_id . ' and is_delete=0';
                            $balance_sql   = 'select balance from ' . Room_Ticket_Journal . ' where ' . $balance_where . ' order by journal_id desc';

                            $balance_query = $MMYSQL->query($balance_sql);
                            if (is_array($balance_query) && count($balance_query) > 0) {
                                $balance = $balance_query[0]['balance'];
                            }

                            //添加到流水账
                            $journal_array['create_time']  = $timestamp;
                            $journal_array['create_appid'] = "aid_" . $account_id;
                            $journal_array['update_time']  = $timestamp;
                            $journal_array['update_appid'] = "aid_" . $account_id;
                            $journal_array['is_delete']    = G_CONST::IS_FALSE;
                            $journal_array['account_id']   = $account_id;
                            $journal_array['object_id']    = $room_id;
                            $journal_array['object_type']  = 3;  //游戏

                            $journal_array['extra']    = "";
                            $journal_array['abstract'] = "6人三公";        //摘要

                            $journal_array['disburse'] = $spend_ticket_count;
                            $journal_array['balance']  = $balance - $spend_ticket_count;
                            if ($journal_array['balance'] < 0) {
                                $this->logMessage('error', "function(startGame):balance negative balance: " . $balance . " account_id: " . $account_id . " room_id: " . $room_id . " in file" . __FILE__ . " on Line " . __LINE__);

                                $journal_array['balance'] = 0;
                            }
                            $journal_id = $MMYSQL->insertReturnID(Room_Ticket_Journal, $journal_array);

                            $mkv[$account_id] = $spend_ticket_count;
                        }
                    } else {
                        foreach ($to_check_user_array as $account_id) {
                            $mkv[$account_id] = $spend_ticket_count;
                        }
                    }

                    //添加到用户
                    $TicketChecked_Key = strtr(Redis_Const::TicketChecked_Key, $replyArr);
                    $Redis_Model->hmsetField($TicketChecked_Key, $mkv);
		    $Redis_Model->expireKey($TicketChecked_Key, G_CONST::REDIS_EXPIRE_SECOND);
                }


                $Multiples_Key     = strtr(Redis_Const::Multiples_Key, $replyArr);
                $ShowCard_Key      = strtr(Redis_Const::ShowCard_Key, $replyArr);
                $Card_Key          = strtr(Redis_Const::Card_Key, $replyArr);
                $PlayMember_Key    = strtr(Redis_Const::PlayMember_Key, $replyArr);
                $AccountStatus_Key = strtr(Redis_Const::AccountStatus_Key, $replyArr);


                //删除每局玩家筹码hash
                $Redis_Model->deleteKey($Multiples_Key);
                //删除每局玩家摊牌标识hash
                $Redis_Model->deleteKey($ShowCard_Key);
                //删除每局玩家手牌hash
                $Redis_Model->deleteKey($Card_Key);


                //删除每局玩家抢庄hash
                if ($banker_mode == Game::BankerMode_FreeGrab || $banker_mode == Game::BankerMode_SeenGrab) {
                    $Grab_Key = strtr(Redis_Const::Grab_Key, $replyArr);
                    $Redis_Model->deleteKey($Grab_Key);
                }

                $Multiples_mkv  = array();
                $Card_mkv       = array();
                $play_member    = array();  //游戏玩家队列
                $RoomStatus_mvk = array();

                $readyUserCount = count($readyUser_ary);  //游戏中玩家数量

                $is_dealCard  = 0;
                $is_joker     = $room_data[Redis_CONST::Room_Field_Is_Joker] ? 1 : 0;
                $player_cards = $this->dealCard($readyUserCount, $is_joker);  //发牌

                if (count($player_cards) != $readyUserCount) {
                    $this->logMessage('error', "function(startGame):dealCard count error readyUserCount: " . $readyUserCount . " player_cards: " . count($player_cards) . " room_id: " . $room_id . " in file" . __FILE__ . " on Line " . __LINE__);
                    $is_dealCard = 1;
                }
                foreach ($player_cards as $p_card) {
                    if ($p_card == "") {
                        $this->logMessage('error', "function(startGame):player_cards error readyUserCount: " . " room_id: " . $room_id . " in file" . __FILE__ . " on Line " . __LINE__);
                        $is_dealCard = 1;
                        break;
                    }
                }
                if ($is_dealCard == 1) {
                    $this->logMessage('error', "function(startGame):重新发牌 " . " room_id: " . $room_id . " in file" . __FILE__ . " on Line " . __LINE__);
                    $player_cards = $this->dealCard($readyUserCount, $is_joker);  //发牌
                }

                //获取抢庄倒计时
                $countDown_grab = $this->queryGameCountDownGrab($room_id);
                $limit_time     = $countDown_grab > 0 ? $countDown_grab : Game::LimitTime_Grab;
                $player_status  = array();
                for ($i = 0; $i < $readyUserCount; $i++) {
                    $account_id = $readyUser_ary[$i];
                    //玩家状态变成选择抢庄状态
                    $RoomStatus_mvk[$account_id] = Game::AccountStatus_Choose;

                    $this->writeLog("[$room_id] ($account_id) 手牌：" . $player_cards[$i]);

                    //设置每局玩家手牌hash
                    $Card_mkv[$account_id] = $player_cards[$i];

                    $player_status[] = array(
                        "account_id"     => $account_id,
                        "account_status" => Game::AccountStatus_Choose,         //5; 初始是选择抢庄
                        "online_status"  => $this->queryOnlineStatus($room_id, $account_id),
                        "limit_time"     => $limit_time
                    );

                    $singleCard_mkv              = array();
                    $singleCard_mkv[$account_id] = $player_cards[$i];
                    $Redis_Model->hmsetField($Card_Key, $singleCard_mkv);

                    //获取卡牌
                    $player_cardAry = $this->queryCardInfo($room_id, $account_id);
                    if (!is_array($player_cardAry) || $player_cardAry[0] == "-1") {
                        //var_dump($player_cardAry);
                        $this->logMessage('error', "function(startGame):set card error:room_id :" . $room_id . " account_id:" . $account_id . " in file" . __FILE__ . " on Line " . __LINE__);
                        $Redis_Model->hmsetField($Card_Key, $singleCard_mkv);
                    }
                    unset($singleCard_mkv);
                    unset($player_cardAry);
                }

                //设置每局玩家顺序list
                $play_member = $readyUser_ary;

                //$mset_result = $Redis_Model->hmsetField($Chip_Key,$Chip_mkv);
                // $mset_result = $Redis_Model->hmsetField($Card_Key,$Card_mkv);
                // if(OPT_CONST::DATA_NONEXISTENT == $mset_result)
                // {
                // 	$mset_result = $Redis_Model->hmsetField($Card_Key,$Card_mkv);
                // }

                $mset_result = $Redis_Model->hmsetField($AccountStatus_Key, $RoomStatus_mvk);

                //删除每局玩家顺序list 再装入本局玩家
                $Redis_Model->deleteKey($PlayMember_Key);
                $push_result = $Redis_Model->pushList($is_rpush = 0, $is_pushx = 0, $PlayMember_Key, $play_member); //lpush

                //重设每局游戏参数
                //$parameter_ary[Redis_CONST::Play_Field_Banker] = -1;
                $parameter_ary[Redis_CONST::Play_Field_Circle]     = -1;
                $parameter_ary[Redis_CONST::Play_Field_BankerMult] = 1;
                $mset_result                                       = $Redis_Model->hmsetField($Play_Key, $parameter_ary);
		$Redis_Model->expireKey($Play_Key, G_CONST::REDIS_EXPIRE_SECOND);
		$Redis_Model->expireKey($Card_Key, G_CONST::REDIS_EXPIRE_SECOND);
		$Redis_Model->expireKey($PlayMember_Key, G_CONST::REDIS_EXPIRE_SECOND);

                //房间轮数与局数更新
                $this->updateGameNumberRound($room_id);


                //推送开始
                $arr = array("result" => 0, "operation" => "GameStart", "data" => $player_status, "result_message" => "游戏开始了", "limit_time" => $limit_time, "game_num" => $game_num + 1);
                $this->pushMessageToGroup($room_id, $arr);

                $player_array = $this->queryPlayMember($room_id);
                foreach ($player_array as $player_id) {
                    $card_info = $this->queryCardInfo($room_id, $player_id);
                    if ($banker_mode != Game::BankerMode_SeenGrab) {
                        $card_info[0] = "-1";
                        $card_info[1] = "-1";
                    }
                    $card_info[2] = "-1";
                    $player_cards = array(
                        "account_id" => $player_id,
                        "cards"      => $card_info,
                    );
                    $card_arr     = array("result" => 0, "operation" => "MyCards", "data" => $player_cards, "result_message" => "用户手牌");

                    $replyArr = array("[roomid]" => $room_id, "[accountid]" => $player_id);
                    $room_aid = strtr(Game::RoomUser_UID, $replyArr);
                    $this->pushMessageToAccount($room_aid, $card_arr);
                }

                if ($game_num == 0) {
                    //设置开局时间
                    $this->updateStartTime($room_id);
                }

                //设置叫庄回合
                $this->updateCircle($room_id, Game::Circle_Grab);
                //设置自动不叫庄定时器
                $this->setupGrabPassiveTimer($room_id, $banker_mode);


            } else {

                if (($banker_mode == Game::BankerMode_FixedBanker || $banker_mode == Game::BankerMode_TenGrab) && $game_num >= 1) {
                    if ($Redis_Model->hgetField($Room_Key, Redis_Const::Room_Field_ReadyTime) == -1) {
                        //只有第二个人的准备才触发倒计时
                        $is_pushTimer = TRUE;
                        $this->setupStartGamePassiveTimer($room_id, $is_pushTimer);
                    }
                } else {
                    if ($Redis_Model->hgetField($Room_Key, Redis_Const::Room_Field_ReadyTime) == -1) {
                        //只有第二个人的准备才触发倒计时
                        $is_pushTimer = TRUE;
                        $this->setupStartGamePassiveTimer($room_id, $is_pushTimer);
                    }
                }
            }
        }

        return TRUE;
    }


    //抢庄回合结束，开启下注回合
    protected function grabPassiveOpt($room_id) {
        //设置用户状态
        $Redis_Model       = Redis_Model::getModelObject();
        $replyArr          = array("[roomid]" => $room_id);
        $AccountStatus_Key = strtr(Redis_Const::AccountStatus_Key, $replyArr);
        $mkv               = array();
        $player_array      = $this->queryPlayMember($room_id);
        if (is_array($player_array) && count($player_array)) {
            foreach ($player_array as $account_id) {
                $pre_status = $this->queryAccountStatus($room_id, $account_id);
                if ($pre_status == Game::AccountStatus_Choose) {
                    $mkv[$account_id] = Game::AccountStatus_Notgrab;    //默认不抢
                }
            }
        }
        if (count($mkv) > 0) {
            $mset_result = $Redis_Model->hmsetField($AccountStatus_Key, $mkv);  //用户状态
        }
        //选择庄家，开始下注回合
        $this->startBetRound($room_id);

        return TRUE;
    }

    /*
		选择庄家，推送下注回合
    */
    protected function startBetRound($room_id) {
        $check_circle  = Game::Circle_Grab;
        $update_circle = Game::Circle_Bet;
        $success       = $this->setCircleTransaction($room_id, $check_circle, $update_circle);
        if (!$success) {
            $this->logMessage('error', "function(startBetRoundGrab):并发忽略。room id:" . $room_id . " in file" . __FILE__ . " on Line " . __LINE__);
            return FALSE;
        }

        //获取房间庄家模式
        $banker_mode = $this->queryBankerMode($room_id);
        if ($banker_mode == Game::BankerMode_FreeGrab || $banker_mode == Game::BankerMode_SeenGrab || $banker_mode == Game::BankerMode_TenGrab) {
            //自由抢庄,明牌抢庄，牛牛上庄
            return $this->startBetRoundGrab($room_id, $banker_mode);
        } else if ($banker_mode == Game::BankerMode_NoBanker) {
            //通比牛牛
            return $this->startBetRoundNoBanker($room_id, $banker_mode);
        } else if ($banker_mode == Game::BankerMode_FixedBanker) {
            //固定庄家
            return $this->startBetRoundFixed($room_id, $banker_mode);
        } else {
            $this->logMessage('error', "function(startBetRound):banker_mode: " . $banker_mode . " error  | room_id: " . $room_id . " in file" . __FILE__ . " on Line " . __LINE__);
            return TRUE;
        }
    }

    /*
		选择庄家，用户抢庄
    */
    protected function startBetRoundGrab($room_id, $banker_mode) {
        $grab_array = array();
        $mult_array = array();
        $max_mult   = 1;

        //获取所有游戏用户
        $player_array = $this->queryPlayMember($room_id);
        foreach ($player_array as $player_id) {
            $banker_mult = $this->queryGrabMultiples($room_id, $player_id);
            if ($banker_mult != FALSE) {
                $mult_array[$banker_mult][] = $player_id;
                if ($banker_mult > $max_mult) {
                    $max_mult = $banker_mult;
                }
            }

            //设置用户状态为下注中
            $this->updateAccountStatusNotNoty($room_id, $player_id, Game::AccountStatus_Bet);
        }

        if (isset($mult_array[$max_mult]) && count($mult_array[$max_mult]) > 0) {
            $grab_array = $mult_array[$max_mult];
        }


        $is_only = G_CONST::IS_FALSE;
        if (count($grab_array) == 1) {
            $is_only = G_CONST::IS_TRUE;
        }

        if (count($grab_array) == 0) {
            $grab_array = $player_array;
        }

        if (count($grab_array) == 0) {
            return TRUE;
        }

        //选择庄家
        $banker_num = rand(0, count($grab_array) - 1);
        $banker_id  = $grab_array[$banker_num];


        //设置banker
        $this->updateBanker($room_id, $banker_id);
        //设置banker 倍数
        $this->updateBankerMultiples($room_id, $max_mult);

        //获取下注倒计时
        $countDown_bet = $this->queryGameCountDownBet($room_id);
        $limit_time    = $countDown_bet > 0 ? $countDown_bet : Game::LimitTime_Betting;
        $player_status = array();
        foreach ($player_array as $player_id) {
            $is_banker = G_CONST::IS_FALSE;
            if ($player_id == $banker_id) {
                $is_banker = G_CONST::IS_TRUE;
            }

            $player_status[] = array(
                "account_id"     => $player_id,
                "account_status" => Game::AccountStatus_Bet,         //5; 初始是选择抢庄
                "online_status"  => $this->queryOnlineStatus($room_id, $player_id),
                "limit_time"     => $limit_time,
                "is_banker"      => $is_banker
            );
        }
        //设置自动下注定时器
        $this->setupBetPassiveTimer($room_id, $is_only, $banker_mode);

        //通知用户下注
        $arr = array("result" => 0, "operation" => "StartBet", "data" => $player_status, "result_message" => "投注开始了", "grab_array" => $grab_array, "limit_time" => $limit_time);
        $this->pushMessageToGroup($room_id, $arr);

        return TRUE;
    }

    /*
		无庄家
    */
    protected function startBetRoundNoBanker($room_id, $banker_mode) {
        //设置自动下注定时器
        $this->setupBetPassiveTimer($room_id, $is_only = 0, $banker_mode);

        return TRUE;
    }

    /*
		固定庄家
    */
    protected function startBetRoundFixed($room_id, $banker_mode) {
        $grab_array = array();
        $mult_array = array();
        $max_mult   = 1;

        $game_num = $this->queryGameNumber($room_id);
        //获取所有游戏用户
        $player_array = $this->queryPlayMember($room_id);

        if ($game_num > 1) {
            $banker_id = $this->queryBanker($room_id);
            $is_only   = G_CONST::IS_TRUE;

            foreach ($player_array as $player_id) {
                //设置用户状态为下注中
                $this->updateAccountStatusNotNoty($room_id, $player_id, Game::AccountStatus_Bet);
            }
        } else {
            foreach ($player_array as $player_id) {
                $banker_mult = $this->queryGrabMultiples($room_id, $player_id);

                if ($banker_mult != FALSE) {
                    $mult_array[$banker_mult][] = $player_id;
                    if ($banker_mult > $max_mult) {
                        $max_mult = $banker_mult;
                    }
                }
                //设置用户状态为下注中
                $this->updateAccountStatusNotNoty($room_id, $player_id, Game::AccountStatus_Bet);
            }

            if (isset($mult_array[$max_mult]) && count($mult_array[$max_mult]) > 0) {
                $grab_array = $mult_array[$max_mult];
            }

            $is_only = G_CONST::IS_FALSE;
            if (count($grab_array) == 1) {
                $is_only = G_CONST::IS_TRUE;
            }

            if (count($grab_array) == 0) {
                //无人上庄，重新开局
                //设置房间轮数为0
                $Redis_Model                            = Redis_Model::getModelObject();
                $replyArr                               = array("[roomid]" => $room_id);
                $Room_Key                               = strtr(Redis_Const::Room_Key, $replyArr);
                $r_mkv[Redis_Const::Room_Field_GameNum] = 0;            //游戏局数
                $r_mkv[Redis_Const::Room_Field_Status]  = Game::RoomStatus_Waiting;
                $mset_result                            = $Redis_Model->hmsetField($Room_Key, $r_mkv);

                $ticket_checked_user = $this->queryTicketCheckedUser($room_id);
                foreach ($ticket_checked_user as $player_id) {
                    //设置用户状态为已准备
                    $this->updateAccountStatusNotNoty($room_id, $player_id, Game::AccountStatus_Ready);
                }

                $this->startGame($room_id);
                return TRUE;
            }

            //选择庄家
            $banker_num = rand(0, count($grab_array) - 1);
            $banker_id  = $grab_array[$banker_num];

            //设置banker
            $this->updateBanker($room_id, $banker_id);
        }

        //设置banker分数
        if ($game_num == 1) {
            $banker_score = $this->queryBankerScore($room_id);
            $this->updateAccountScore($room_id, $banker_id, $banker_score);
        }

        //获取下注倒计时
        $countDown_bet = $this->queryGameCountDownBet($room_id);
        $limit_time    = $countDown_bet > 0 ? $countDown_bet : Game::LimitTime_Betting;
        $player_status = array();
        foreach ($player_array as $player_id) {
            $is_banker = G_CONST::IS_FALSE;
            if ($player_id == $banker_id) {
                $is_banker = G_CONST::IS_TRUE;
            }

            $player_status[] = array(
                "account_id"     => $player_id,
                "account_status" => Game::AccountStatus_Bet,         //5; 初始是选择抢庄
                "online_status"  => $this->queryOnlineStatus($room_id, $player_id),
                "limit_time"     => $limit_time,
                "is_banker"      => $is_banker,
                "account_score"  => $this->queryAccountScore($room_id, $player_id)
            );
        }
        //设置自动下注定时器
        $this->setupBetPassiveTimer($room_id, $is_only, $banker_mode);

        //通知用户下注
        $arr = array("result" => 0, "operation" => "StartBet", "data" => $player_status, "result_message" => "投注开始了", "grab_array" => $grab_array, "limit_time" => $limit_time);
        $this->pushMessageToGroup($room_id, $arr);

        return TRUE;
    }


    //下注时间结束自动进入摊牌模式
    public function betPassiveOpt($room_id) {
        $multiples = 1;
        //设置用户状态
        $Redis_Model  = Redis_Model::getModelObject();
        $replyArr     = array("[roomid]" => $room_id);
        $mkv          = array();
        $player_array = $this->queryPlayMember($room_id);
        if (is_array($player_array) && count($player_array)) {
            foreach ($player_array as $account_id) {
                $multiples_result = $this->queryPlayerMultiples($room_id, $account_id);
                if ($multiples_result <= 0) {
                    $this->updatePlayerMultiples($room_id, $account_id, $multiples);
                }
            }
        }
        //是否摊牌回合
        $this->startShowRound($room_id);
        return TRUE;
    }

    /*
		发牌，推送摊牌回合
    */
    protected function startShowRound($room_id) {
        $check_circle  = Game::Circle_Bet;
        $update_circle = Game::Circle_Show;
        $success       = $this->setCircleTransaction($room_id, $check_circle, $update_circle);
        if (!$success) {
            $this->logMessage('error', "function(startShowRound):并发忽略。room id:" . $room_id . " in file" . __FILE__ . " on Line " . __LINE__);
            return FALSE;
        }

        //获取摊牌倒计时
        $countDown_show = $this->queryGameCountDownShow($room_id);
        $limit_time     = $countDown_show > 0 ? $countDown_show : Game::LimitTime_Show;
        $player_status  = array();
        //获取所有游戏用户
        $player_array = $this->queryPlayMember($room_id);
        foreach ($player_array as $player_id) {
            //设置用户状态为未摊牌
            $this->updateAccountStatusNotNoty($room_id, $player_id, Game::AccountStatus_Notshow);

            //推送手牌
            $card_info    = $this->queryCardInfo($room_id, $player_id);
            $cards_result = $this->calculateCardValue($room_id, $player_id, $card_info, "");
            $card_type    = $cards_result['card_type'];
            $card_text    = $cards_result['card_text'];

            $player_status[] = array(
                "account_id"     => $player_id,
                "account_status" => Game::AccountStatus_Notshow,         //5; 初始是选择抢庄
                "online_status"  => $this->queryOnlineStatus($room_id, $player_id),
                "limit_time"     => $limit_time,
                "cards"          => $card_info,
                "card_type"      => $card_type,
                "multiples"      => $this->queryPlayerMultiples($room_id, $player_id)
            );

            $this->writeLog("[$room_id] ($player_id) 牌型:" . $card_text . ",点数:" . $card_type);
        }

        //设置自动摊牌定时器
        $this->setupShowPassiveTimer($room_id);

        //设置下注回合
        //$this->updateCircle($room_id,Game::Circle_Show);

        //通知用户下注
        $arr = array("result" => 0, "operation" => "StartShow", "data" => $player_status, "result_message" => "摊牌开始了", "limit_time" => $limit_time);
        $this->pushMessageToGroup($room_id, $arr);

        return TRUE;
    }


    /*
		结算，推送胜负结果
    */
    protected function startWinRound($room_id) {
        $check_circle  = Game::Circle_Show;
        $update_circle = -1;
        $success       = $this->setCircleTransaction($room_id, $check_circle, $update_circle);
        if (!$success) {
            $this->logMessage('error', "function(startWinRound):并发忽略。room id:" . $room_id . " in file" . __FILE__ . " on Line " . __LINE__);
            return FALSE;
        }

        //获取房间庄家模式
        $banker_mode = $this->queryBankerMode($room_id);
        if ($banker_mode == Game::BankerMode_FreeGrab || $banker_mode == Game::BankerMode_SeenGrab) {
            //两两比分
            return $this->startWinRoundSingle($room_id, $banker_mode);
        } else if ($banker_mode == Game::BankerMode_NoBanker) {
            //所有人比分
            return $this->startWinRoundAll($room_id, $banker_mode);
        } else if ($banker_mode == Game::BankerMode_FixedBanker) {
            //两两比分,固定庄家
            return $this->startWinRoundFixed($room_id, $banker_mode);
        } else {
            $this->logMessage('error', "function(startBetRound):banker_mode: " . $banker_mode . " error  | room_id: " . $room_id . " in file" . __FILE__ . " on Line " . __LINE__);
            return TRUE;
        }
    }


    /*
		结算，推送胜负结果
    */
    protected function startWinRoundFixed($room_id, $banker_mode) {
        $Redis_Model   = Redis_Model::getModelObject();
        $replyArr      = array("[roomid]" => $room_id);
        $Room_Key      = strtr(Redis_Const::Room_Key, $replyArr);
        $RoomScore_Key = strtr(Redis_Const::RoomScore_Key, $replyArr);


        //获取庄家ID
        $banker_id         = $this->queryBanker($room_id);
        $banker_score      = 0;
        $banker_lose_score = 0;
        $banker_win_score  = 0;

        $winner_array = array();
        $loser_array  = array();

        //获取参赛用户
        $player_array = $this->queryPlayMember($room_id);
        foreach ($player_array as $player_id) {
            if ($player_id == $banker_id) {
                continue;
            }
            //比牌
            $compareResult = $this->compareCards($room_id, $banker_id, $player_id);
            if ($compareResult > 0) {
                $winner_array[]    = array("account_id" => $player_id, "score" => $compareResult);    //闲家赢
                $banker_lose_score += $compareResult;
            } else {
                $loser_array[]    = array("account_id" => $player_id, "score" => $compareResult);    //庄家赢
                $banker_win_score += -$compareResult;
            }
            $banker_score += -$compareResult;
        }

        $is_break          = 0;
        $banker_score_type = $this->queryBankerScoreType($room_id);
        if ($banker_score_type > 1) {
            //有限分数
            //获取庄家当前积分
            $banker_origin_score = $this->queryAccountScore($room_id, $banker_id);
            if ($banker_origin_score + $banker_win_score - $banker_lose_score <= 0) {
                //需要强制结算
                $is_break = 1;
            }
        }

        if ($is_break == 1) {
            //需要强制结算
            $banker_capital_score = $banker_origin_score + $banker_win_score;

            //获取胜利用户手牌大小
            $winner_ranking_array      = array();
            $winner_rankingScore_array = array();
            foreach ($winner_array as $winner_item) {
                $account_id = $winner_item['account_id'];

                $cards_result = $this->calculateCardValue($room_id, $account_id);
                $card_value   = $cards_result['value'];

                $winner_ranking_array[$account_id]      = $card_value;
                $winner_rankingScore_array[$account_id] = $winner_item['score'];
            }
            arsort($winner_ranking_array);

            //$this->logMessage('error', "function(startWinRoundFixed):房间(".$room_id.")庄家(".$banker_id.")输 :".$banker_score." 爆庄 in file".__FILE__." on Line ".__LINE__);
            //$this->logMessage('error', "function(startWinRoundFixed):庄家可扣分 :".$banker_capital_score." in file".__FILE__." on Line ".__LINE__);

            //$this->logMessage('error', "function(startWinRoundFixed):胜利用户手牌大小 :".json_encode($winner_ranking_array)." in file".__FILE__." on Line ".__LINE__);
            //$this->logMessage('error', "function(startWinRoundFixed):胜利用户赢的分数 :".json_encode($winner_rankingScore_array)." in file".__FILE__." on Line ".__LINE__);

            $winner_array = array();

            //按照排名给分
            foreach ($winner_ranking_array as $account_id => $card_type) {
                if ($banker_capital_score - $winner_rankingScore_array[$account_id] > 0) {
                    $winner_array[]       = array("account_id" => $account_id, "score" => $winner_rankingScore_array[$account_id]);    //闲家赢
                    $banker_capital_score -= $winner_rankingScore_array[$account_id];
                } else {
                    $winner_array[]       = array("account_id" => $account_id, "score" => $banker_capital_score);    //闲家赢
                    $banker_capital_score = 0;
                }
            }

            $banker_score = -$banker_origin_score;
        }

        //计算积分
        foreach ($winner_array as $winner) {
            $player_id     = $winner['account_id'];
            $compareResult = $winner['score'];
            $mset_result   = $Redis_Model->hincrbyField($RoomScore_Key, $player_id, $compareResult); //个人总分
        }
        foreach ($loser_array as $loser) {
            $player_id     = $loser['account_id'];
            $compareResult = $loser['score'];
            $mset_result   = $Redis_Model->hincrbyField($RoomScore_Key, $player_id, $compareResult); //个人总分
        }

        if ($banker_score >= 0) {
            $winner_array[] = array("account_id" => $banker_id, "score" => $banker_score);
        } else {
            $loser_array[] = array("account_id" => $banker_id, "score" => $banker_score);
        }
        //庄家
        $mset_result = $Redis_Model->hincrbyField($RoomScore_Key, $banker_id, $banker_score); //个人总分

        //$this->logMessage('error', "function(startWinRoundFixed):胜利用户 :".json_encode($winner_array)." in file".__FILE__." on Line ".__LINE__);
        //$this->logMessage('error', "function(startWinRoundFixed):失败用户 :".json_encode($loser_array)." in file".__FILE__." on Line ".__LINE__);

        //$total_num = $this->queryTotalNum($room_id);
        //$game_num = $this->queryGameNumber($room_id);

        $arrData['room_id']      = $room_id;
        $arrData['banker_mode']  = $banker_mode;
        $arrData['winner_array'] = $winner_array;
        $arrData['loser_array']  = $loser_array;
        $arrData['is_break']     = $is_break;
        $this->dealGameResult($arrData);

        if ($is_break == 1) {
            $room_data = $this->queryRoomData($room_id);
            $game_num  = $room_data[Redis_CONST::Room_Field_GameNum];
            $total_num = $room_data[Redis_CONST::Room_Field_TotalNum];

            if ($game_num < $total_num) {
                $arrData['room_id']      = $room_id;
                $arrData['banker_mode']  = $banker_mode;
                $arrData['winner_array'] = $winner_array;
                $arrData['loser_array']  = $loser_array;
                $arrData['type']         = 2;
                $this->breakRoom($arrData);
            }
        }

        return TRUE;
    }

    /*
		结算，推送胜负结果
    */
    protected function startWinRoundSingle($room_id, $banker_mode) {
        $Redis_Model   = Redis_Model::getModelObject();
        $replyArr      = array("[roomid]" => $room_id);
        $Room_Key      = strtr(Redis_Const::Room_Key, $replyArr);
        $RoomScore_Key = strtr(Redis_Const::RoomScore_Key, $replyArr);

        //获取庄家ID
        $banker_id    = $this->queryBanker($room_id);
        $banker_score = 0;

        $winner_array = array();
        $loser_array  = array();

        //获取参赛用户
        $player_array = $this->queryPlayMember($room_id);
        foreach ($player_array as $player_id) {
            if ($player_id == $banker_id) {
                continue;
            }

            //比牌 闲家与庄家比
            $compareResult = $this->compareCards($room_id, $banker_id, $player_id);
            if ($compareResult > 0) {
                $winner_array[] = array("account_id" => $player_id, "score" => $compareResult);     //闲家赢
            } else {
                $loser_array[] = array("account_id" => $player_id, "score" => $compareResult);      //庄家赢
            }

            $banker_score += -$compareResult;

            //闲家
            $mset_result = $Redis_Model->hincrbyField($RoomScore_Key, $player_id, $compareResult);  //个人总分

            //$this->logMessage('error', "function(broadcastVoice):player mset_result:".$mset_result." in file".__FILE__." on Line ".__LINE__);
        }

        if ($banker_score >= 0) {
            $winner_array[] = array("account_id" => $banker_id, "score" => $banker_score);
        } else {
            $loser_array[] = array("account_id" => $banker_id, "score" => $banker_score);
        }
        //庄家
        $mset_result = $Redis_Model->hincrbyField($RoomScore_Key, $banker_id, $banker_score); //个人总分

        //$this->logMessage('error', "function(broadcastVoice):banker mset_result:".$mset_result." in file".__FILE__." on Line ".__LINE__);

        $arrData['room_id']      = $room_id;
        $arrData['banker_mode']  = $banker_mode;
        $arrData['winner_array'] = $winner_array;
        $arrData['loser_array']  = $loser_array;
        $arrData['is_break']     = 0;
        $this->dealGameResult($arrData);

        return TRUE;
    }


    /*
		结算，推送胜负结果
    */
    protected function startWinRoundAll($room_id, $banker_mode) {
        $Redis_Model   = Redis_Model::getModelObject();
        $replyArr      = array("[roomid]" => $room_id);
        $RoomScore_Key = strtr(Redis_Const::RoomScore_Key, $replyArr);

        //计算最大手牌用户
        $win_result = $this->calculateMaxValue($room_id);

        $winner = $win_result['account_id'];
        $score  = $win_result['score'];

        $win_scord    = 0;
        $winner_array = array();
        $loser_array  = array();

        $player_array = $this->queryPlayMember($room_id);
        foreach ($player_array as $player_id) {
            if ($player_id != $winner) {
                $lose_score    = -$score;
                $loser_array[] = array("account_id" => $player_id, "score" => $lose_score);

                $mset_result = $Redis_Model->hincrbyField($RoomScore_Key, $player_id, $lose_score); //个人总分

                $win_scord += $score;
            }
        }

        $winner_array[] = array("account_id" => $winner, "score" => $win_scord);
        //赢家
        $mset_result = $Redis_Model->hincrbyField($RoomScore_Key, $winner, $win_scord); //个人总分

        $arrData['room_id']      = $room_id;
        $arrData['banker_mode']  = $banker_mode;
        $arrData['winner_array'] = $winner_array;
        $arrData['loser_array']  = $loser_array;
        $arrData['is_break']     = 0;
        $this->dealGameResult($arrData);

        return TRUE;
    }

    /*
		处理游戏结果
    */
    protected function dealGameResult($arrData) {
        $room_id      = $arrData['room_id'];
        $banker_mode  = $arrData['banker_mode'];
        $winner_array = $arrData['winner_array'];
        $loser_array  = $arrData['loser_array'];
        $is_break     = $arrData['is_break'];

        $Redis_Model = Redis_Model::getModelObject();
        $replyArr    = array("[roomid]" => $room_id);
        $Room_Key    = strtr(Redis_Const::Room_Key, $replyArr);

        //获取积分榜
        $RoomScore_Key = strtr(Redis_Const::RoomScore_Key, $replyArr);
        $scoreboard    = $Redis_Model->hgetallField($RoomScore_Key);

        $board_json_str = "";

        $ticket_checked_user = $this->queryTicketCheckedUser($room_id);

        //$player_array = $this->queryPlayMember($room_id);
        foreach ($scoreboard as $key => $value) {
            if (!in_array($key, $ticket_checked_user)) {
                //未扣房卡的用户不显示在积分榜上
                unset($scoreboard[$key]);
            }
        }
        $save_scoreboard = $scoreboard;

        $room_data    = $this->queryRoomData($room_id);
        $game_num     = $room_data[Redis_CONST::Room_Field_GameNum];
        $total_num    = $room_data[Redis_CONST::Room_Field_TotalNum];
        $banker_score = $room_data[Redis_CONST::Room_Field_BaseScore];
        $start_time   = $room_data[Redis_CONST::Room_Field_StartTime];
        $round        = $room_data[Redis_CONST::Room_Field_GameRound];

        //$total_num = $this->queryTotalNum($room_id);
        //$game_num = $this->queryGameNumber($room_id);

        $banker_id     = $this->queryBanker($room_id);
        $game_bankerid = $banker_id;

        $MMYSQL             = $this->initMysql();
        $balance_scoreboard = "-1";
        //保存积分榜
        if (is_array($scoreboard)) {
            $mkv[Redis_Const::Room_Field_Scoreboard] = json_encode($scoreboard);
            $Redis_Model->hmsetField($Room_Key, $mkv);

            if ($game_num >= $total_num) {
                foreach ($scoreboard as $account_id => $score) {
                    $account_sql = 'select nickname from ' . WX_Account . ' where account_id =' . $account_id;
                    $name        = $MMYSQL->single($account_sql);

                    if ($banker_mode == Game::BankerMode_FixedBanker && $banker_id == $account_id) {
                        $score                        -= $banker_score;
                        $save_scoreboard[$account_id] = $score;
                    }

                    $name_board[] = array('name' => $name, 'score' => $score, 'account_id' => $account_id);

                    $account_array               = [];
                    $account_array['account_id'] = $account_id;
                    $account_array['room_id']    = $room_id;
                    $account_array['game_type']  = Game::Game_Type;
                    $account_array['score']      = $score;
                    $account_array['over_time']  = time();

                    $MMYSQL->insertReturnID(Room_Account, $account_array);
                }
                $balance_scoreboard = array('time' => time(), 'scoreboard' => $name_board, 'game_num' => $game_num);
            }
        }

        //获取积分榜
        //$scoreboard = $this->queryScoreboard($room_id);

        //是否能主动下庄,0否1是
        $can_break = 0;
        if ($banker_mode == Game::BankerMode_FixedBanker && $game_num >= 3) {
            $can_break = Config::Can_BreakRoom;
        }


        //牛牛上庄，计算牛牛用户设置庄家
        $banker_id = -1;

        if ($game_num >= $total_num) {
            $is_break = 0;
        }

        $msg_arr = array(
            'result'         => 0,
            'operation'      => 'Win',
            'result_message' => "获胜+积分榜",
            'data'           => array(
                'winner_array'       => $winner_array,
                'loser_array'        => $loser_array,
                'score_board'        => $scoreboard,
                'game_num'           => $game_num,
                'total_num'          => $total_num,
                'balance_scoreboard' => $balance_scoreboard,
                'can_break'          => $can_break,
                'is_break'           => $is_break,
                'banker_id'          => $banker_id,
            )
        );

        $this->writeLog("[$room_id] " . json_encode($msg_arr));
        $this->pushMessageToGroup($room_id, $msg_arr);


        //保存当局游戏结果
        $game_info['room_id']    = $room_id;
        $game_info['game_type']  = Game::Game_Type;
        $game_info['dealer_num'] = Config::Dealer_Num;
        $game_info['round']      = $round;
        $game_info['game_num']   = $game_num;
        $game_info['total_num']  = $total_num;
        $game_info['banker_id']  = $game_bankerid;
        $game_info['extra']      = "";
        $this->saveGameResult($game_info, $winner_array, $loser_array, $room_data);

        //修改房间状态为等待准备
        $this->updateRoomStatus($room_id, Game::RoomStatus_Waiting);   //房间状态，1等待、2进行中、3关闭

        if ($game_num >= $total_num) {  //最后一局

            $round = $Redis_Model->hincrbyField($Room_Key, Redis_Const::Room_Field_GameRound, 1);

            $this->writeLog("[$room_id] 第" . ($round - 1) . "轮 结束!");

            //规则文本
            $rule_text = $this->formatRuleText($room_id, $room_data);

            $board_json_str         = json_encode($save_scoreboard);
            $balance_board_json_str = json_encode($balance_scoreboard['scoreboard']);
            //保存积分榜

            $board_array['start_time']    = $start_time;
            $board_array['create_time']   = time();
            $board_array['is_delete']     = G_CONST::IS_FALSE;
            $board_array['game_type']     = Game::Game_Type;  //游戏类型
            $board_array['room_id']       = $room_id;
            $board_array['round']         = $round - 1;
            $board_array['board']         = $board_json_str;
            $board_array['balance_board'] = $balance_board_json_str;
            $board_array['game_num']      = $game_num;
            $board_array['rule_text']     = $rule_text;
            $board_id                     = $MMYSQL->insertReturnID(Room_Scoreboard, $board_array);

            //保存用户积分
            $game_info['score_board'] = $save_scoreboard;
            $this->saveAccountGameScore($game_info);


            //重置房间
            $this->initRoomData($room_id);
            return TRUE;

        } else {

            $this->resetAllAccountStatus($room_id);  //重设所有用户状态为已

            $r_mkv[Redis_Const::Room_Field_ActiveUser]  = -1;        //当前操作用户
            $r_mkv[Redis_Const::Room_Field_ActiveTimer] = -1;        //当前生效timer
            $r_mkv[Redis_Const::Room_Field_ReadyTime]   = -1;        //房间倒计时
            $mset_result                                = $Redis_Model->hmsetField($Room_Key, $r_mkv);

            $Multiples_Key  = strtr(Redis_Const::Multiples_Key, $replyArr);
            $Card_Key       = strtr(Redis_Const::Card_Key, $replyArr);
            $PlayMember_Key = strtr(Redis_Const::PlayMember_Key, $replyArr);
            $Play_Key       = strtr(Redis_Const::Play_Key, $replyArr);

            //删除每局玩家筹码hash
            $Redis_Model->deleteKey($Multiples_Key);
            //删除每局玩家手牌hash
            $Redis_Model->deleteKey($Card_Key);
            //删除每局玩家顺序list
            $Redis_Model->deleteKey($PlayMember_Key);

            //重设每局游戏参数
            if ($banker_mode == Game::BankerMode_FreeGrab || $banker_mode == Game::BankerMode_SeenGrab || $banker_mode == Game::BankerMode_NoBanker) {
                $this->writeLog("[$room_id] banker_mode:" . $banker_mode);

                $parameter_ary[Redis_CONST::Play_Field_Banker] = -1;
            }
            $parameter_ary[Redis_CONST::Play_Field_Circle] = -1;
            $mset_result                                   = $Redis_Model->hmsetField($Play_Key, $parameter_ary);

            return TRUE;
        }
    }


    protected function breakRoom($arrData) {
        $room_id      = $arrData['room_id'];
        $banker_mode  = $arrData['banker_mode'];
        $winner_array = $arrData['winner_array'];
        $loser_array  = $arrData['loser_array'];
        $type         = $arrData['type'];

        $Redis_Model = Redis_Model::getModelObject();
        $replyArr    = array("[roomid]" => $room_id);
        $Room_Key    = strtr(Redis_Const::Room_Key, $replyArr);

        //获取积分榜
        $RoomScore_Key = strtr(Redis_Const::RoomScore_Key, $replyArr);

        $scoreboard     = $Redis_Model->hgetallField($RoomScore_Key);
        $board_json_str = "";

        $ticket_checked_user = $this->queryTicketCheckedUser($room_id);


        //$player_array = $this->queryPlayMember($room_id);
        foreach ($scoreboard as $key => $value) {
            if (!in_array($key, $ticket_checked_user)) {
                //未扣房卡的用户不显示在积分榜上
                unset($scoreboard[$key]);
            }
        }
        $save_scoreboard = $scoreboard;

        $room_data    = $this->queryRoomData($room_id);
        $game_num     = $room_data[Redis_CONST::Room_Field_GameNum];
        $total_num    = $room_data[Redis_CONST::Room_Field_TotalNum];
        $banker_score = $room_data[Redis_CONST::Room_Field_BankerScore];
        $start_time   = $room_data[Redis_CONST::Room_Field_StartTime];
        $round        = $room_data[Redis_CONST::Room_Field_GameRound];
        //$total_num = $this->queryTotalNum($room_id);
        //$game_num = $this->queryGameNumber($room_id);

        $MMYSQL = $this->initMysql();

        $banker_id = $this->queryBanker($room_id);
        //$banker_score = $this->queryBankerScore($room_id);

        foreach ($scoreboard as $account_id => $score) {
            $account_sql = 'select nickname from ' . WX_Account . ' where account_id =' . $account_id;
            $name        = $MMYSQL->single($account_sql);
            if ($banker_id == $account_id) {
                $score                        -= $banker_score;
                $save_scoreboard[$account_id] = $score;
            }
            $name_board[] = array('name' => $name, 'score' => $score, 'account_id' => $account_id);
        }
        $balance_scoreboard = array('time' => time(), 'scoreboard' => $name_board, 'game_num' => $game_num);


        //是否能主动下庄,0否1是
        $can_break    = 0;
        $winner_array = array();
        $loser_array  = array();

        $msg_arr = array(
            'result'         => 0,
            'operation'      => 'BreakRoom',
            'result_message' => "解散房间",
            'data'           => array(
                'winner_array'       => $winner_array,
                'loser_array'        => $loser_array,
                'score_board'        => $scoreboard,
                'game_num'           => $game_num,
                'total_num'          => $total_num,
                'balance_scoreboard' => $balance_scoreboard,
                'can_break'          => $can_break,
                'type'               => $type
            )
        );

        $this->writeLog("[$room_id] " . json_encode($msg_arr));
        //var_dump($msg_arr);
        $this->pushMessageToGroup($room_id, $msg_arr);


        $game_info['room_id']    = $room_id;
        $game_info['game_type']  = Game::Game_Type;
        $game_info['dealer_num'] = Config::Dealer_Num;
        $game_info['round']      = $round;

        $round = $Redis_Model->hincrbyField($Room_Key, Redis_Const::Room_Field_GameRound, 1);
        $this->writeLog("[$room_id] 第" . ($round - 1) . "轮 结束!");

        $board_json_str         = json_encode($save_scoreboard);
        $balance_board_json_str = json_encode($balance_scoreboard['scoreboard']);

        //规则文本
        $rule_text = $this->formatRuleText($room_id, $room_data);

        //保存积分榜
        $board_array['start_time']    = $start_time;
        $board_array['create_time']   = time();
        $board_array['is_delete']     = G_CONST::IS_FALSE;
        $board_array['game_type']     = Game::Game_Type;  //游戏1 炸金花
        $board_array['room_id']       = $room_id;
        $board_array['round']         = $round - 1;
        $board_array['board']         = $board_json_str;
        $board_array['balance_board'] = $balance_board_json_str;
        $board_array['game_num']      = $game_num;
        $board_array['rule_text']     = $rule_text;
        $board_id                     = $MMYSQL->insertReturnID(Room_Scoreboard, $board_array);

        //保存用户积分
        $game_info['score_board'] = $save_scoreboard;
        $this->saveAccountGameScore($game_info);

        //重置房间
        $this->initRoomData($room_id);
        return TRUE;
    }


    /*
		保存游戏结果
    */
    protected function saveGameResult($game_info, $winner_array, $loser_array, $room_data) {
        $timestamp  = time();
        $MMYSQL     = $this->initMysql();
        $room_id    = $game_info['room_id'];
        $game_type  = $game_info['game_type'];
        $dealer_num = $game_info['dealer_num'];

        $game_data['time']  = time();
        $game_data['rid']   = $game_info['room_id'];
        $game_data['rnd']   = $game_info['round'];
        $game_data['gnum']  = $game_info['game_num'];
        $game_data['tnum']  = $game_info['total_num'];
        $game_data['bid']   = $game_info['banker_id'];
        $game_data['bmult'] = $this->queryBankerMultiples($room_id);    //庄家倍数
        //$game_data['extra'] = $game_info['extra'];

        //获取所有人手牌
        $card_array = $this->queryAllCardInfo($room_id);
        //获取所有人倍数
        $multiples_array = $this->queryPlayerMultiplesArray($room_id);
        $playing_array   = array();    //游戏中玩家
        $player_array    = array();    //玩家当局数据

        foreach ($winner_array as $winner_item) {
            $player_id  = $winner_item['account_id'];
            $array['p'] = $player_id;
            $array['s'] = $winner_item['score'];
            $array['c'] = $card_array[$player_id];
            if ($player_id == $game_data['bid']) {
                $array['m'] = 0;
            } else {
                $array['m'] = $multiples_array[$player_id];
            }

            $card_type  = '0';
            $card_text  = '';
            $card_times = 1;
            $cards      = explode(",", $card_array[$player_id]);
            if (count($cards) == 3) {
                $card_result = $this->calculateCardValue($room_id, $player_id, $cards, $room_data);
                $card_type   = $card_result['card_type'];
                $card_text   = $card_result['card_text'];
                $card_times  = $card_result['card_times'];
            }
            $array['ct']         = $card_type;
            $array['card_text']  = $card_text;
            $array['card_times'] = $card_times;

            $player_array[] = $array;
            unset($array);
            $playing_array[] = (int)$player_id;
        }
        foreach ($loser_array as $loser_item) {
            $player_id  = $loser_item['account_id'];
            $array['p'] = $player_id;
            $array['s'] = $loser_item['score'];
            $array['c'] = $card_array[$player_id];
            if ($player_id == $game_data['bid']) {
                $array['m'] = 0;
            } else {
                $array['m'] = $multiples_array[$player_id];
            }

            $card_type  = "0";
            $card_text  = '';
            $card_times = 1;
            $cards      = explode(",", $card_array[$player_id]);
            if (count($cards) == 3) {
                $card_result = $this->calculateCardValue($room_id, $player_id, $cards, $room_data);
                $card_type   = $card_result['card_type'];
                $card_text   = $card_result['card_text'];
                $card_times  = $card_result['card_times'];
            }
            $array['ct']         = $card_type;
            $array['card_text']  = $card_text;
            $array['card_times'] = $card_times;

            $player_array[] = $array;
            unset($array);
            $playing_array[] = (int)$player_id;
        }
        //获取曾经进行游戏用户数组
        $ticket_array = $this->queryTicketCheckedUser($room_id);

        //未参与游戏用户
        $no_playing_array = array_diff($ticket_array, $playing_array);
        foreach ($no_playing_array as $player_id) {
            $array['p']     = $player_id;
            $array['s']     = 0;
            $array['c']     = "";
            $array['m']     = -1;
            $array['ct']    = "1";
            $array['cp']    = "0";
            $player_array[] = $array;
            unset($array);
        }

        $return_player_array = array();
        foreach ($player_array as $index => $item) {
            //获取用户当前名字
            $account_sql               = 'select nickname from ' . WX_Account . ' where account_id =' . $item['p'];
            $name                      = $MMYSQL->single($account_sql);
            $player_array[$index]['n'] = $name;
            $player_data               = $player_array[$index];
            if (!($player_data['c'] == "" && $player_data['m'] == -1)) {
                $return_player_array[] = array(
                    "name"       => $player_data['n'],
                    "account_id" => $player_data['p'],
                    "card_type"  => $player_data['ct'],
                    "card_text"  => $player_data['card_text'],
                    "card_times" => $player_data['card_times'],
                    "card"       => $player_data['c'],
                    "score"      => $player_data['s'],
                    "mutiple"    => $player_data['p'] == $game_data['bid'] ? $game_data['bmult'] : $player_data['m'],
                    "is_banker"  => $player_data['p'] == $game_data['bid'] ? 1 : 0,
                );
            }
        }

        $push_result = array(
            "players"   => $return_player_array,
            "total_num" => $game_data['tnum'],
            "game_num"  => $game_data['gnum'],
        );
        $arr         = array("result" => 0, "operation" => "GameEndData", "data" => $push_result, "result_message" => "本局结算结果");
        $this->pushMessageToGroup($room_id, $arr);

        $save_game_result['pAry']  = $player_array;
        $save_game_result['gData'] = $game_data;
        $save_game_result_json     = json_encode($save_game_result);

        $room_id = $game_data['rid'];
        $round   = $game_data['rnd'];

        $array['create_time'] = $timestamp;
        $array['game_type']   = $game_type;
        $array['room_id']     = $room_id;
        $array['round']       = $round;
        $array['game_result'] = $save_game_result_json;

        $result_id = $MMYSQL->insertReturnID(Room_GameResult, $array);
        unset($array);

        return TRUE;
    }

    protected function saveAccountGameScore($game_info) {
        if (Config::Is_SendGameScore == 0) {
            return;
        }

        $game_type  = $game_info['game_type'];
        $dealer_num = $game_info['dealer_num'];

        $game_result['time']  = time();
        $game_result['rid']   = $game_info['room_id'];
        $game_result['rnd']   = $game_info['round'];
        $game_result['board'] = $game_info['score_board'];

        $game_result_str = json_encode($game_result);
        $push_array      = array($game_result_str);

        //连接日志系统
        $Redis_Model = Redis_Model::getModelObjectLogs();
        $replyArr    = array("[dealernum]" => $dealer_num, "[gametype]" => $game_type);

        $GameScore_Key = strtr(Redis_Const::GameScore_Key, $replyArr);
        $push_result   = $Redis_Model->pushListLogs($is_rpush = 0, $is_pushx = 0, $GameScore_Key, $push_array); //lpush


        //推送到结果处理器
        $msg_array = array(
            'operation'  => "PullGameScore",
            'dealer_num' => $dealer_num,
            'game_type'  => $game_type,
            'data'       => array()
        );
        $this->sendToGameResultProcessor($msg_array);

        return TRUE;
    }


    /*
		推推送给游戏结果处理器
    */
    public function sendToGameResultProcessor($msg_array = "") {
        $message = $this->_JSON($msg_array);

        $socket_name  = "Processor";
        $Socket_Model = Socket_Model::getModelObject($socket_name);
        $Socket_Model->sendMessageToSocket($socket_name, $message);

        return TRUE;


        // if($msg_array == "")
        // {
        // 	$this->writeLog("function(sendToGameResultProcessor): msg_array is empty string"." in file".__FILE__." on Line ".__LINE__);
        // 	return false;
        // }

        // $service_port = Config::Processor_Port;
        // $address = Config::Processor_Address;

        // try{
        // 	$socket = socket_create(AF_INET, SOCK_STREAM, SOL_TCP);
        // 	if ($socket === false)
        // 	{
        // 		$this->writeLog("function(sendToGameResultProcessor):socket_create() failed:".socket_strerror(socket_last_error())." in file".__FILE__." on Line ".__LINE__);
        // 		return false;
        // 	}
        // 	else
        // 	{
        // 		$result = socket_connect($socket, $address, $service_port);
        // 		if($result === false)
        // 		{
        // 			$this->writeLog("function(sendToGameResultProcessor):socket_connect() failed.\nReason: ($result) " . socket_strerror(socket_last_error($socket)) ." in file".__FILE__." on Line ".__LINE__);
        // 			return false;
        // 		}
        // 		else
        // 		{
        // 			//$message = '{"operation":"CtrlCntr-1","data":{"order_code":"'.$order_code.'"},"app_ver":"2.0.0"}';
        // 			$message = $this->_JSON($msg_array);
        // 			//echo $message.PHP_EOL;
        // 			$message .= "\n";

        // 			$result = socket_write($socket, $message, strlen($message));
        // 			if($result == false)
        // 			{
        // 				$this->writeLog("function(sendToGameResultProcessor):socket_write failed :".$result." in file".__FILE__." on Line ".__LINE__);
        // 				return false;
        // 			}
        // 		}
        // 		socket_close($socket);
        // 	}
        // }catch(Exception $e){
        // 	$this->writeLog("function(sendToGameResultProcessor): catch:"." in file".__FILE__." on Line ".__LINE__);
        // }

        // return true;
    }


    //重新设置所有用户状态为未准备
    protected function resetAllAccountStatus($room_id, $reset_status = Game::AccountStatus_Notready) {
        $Redis_Model       = Redis_Model::getModelObject();
        $replyArr          = array("[roomid]" => $room_id);
        $AccountStatus_Key = strtr(Redis_Const::AccountStatus_Key, $replyArr);

        $mkv     = array();
        $members = $this->queryRoomMembers($room_id);
        if (is_array($members) && count($members)) {
            foreach ($members as $account_id) {
                $pre_status = $this->queryAccountStatus($room_id, $account_id);
                if ($pre_status == Game::AccountStatus_Watch)
                    continue;
                $status = ($pre_status == Game::AccountStatus_Initial) ? Game::AccountStatus_Initial : $reset_status;
                if ($status != Game::AccountStatus_Initial) {
                    $is_online = $this->queryOnlineStatus($room_id, $account_id);
                    if ($is_online == 0) {
                        $status = Game::AccountStatus_Notready;
                    }
                }
                $mkv[$account_id] = $status;
            }
        }
        if (count($mkv) > 0) {
            $mset_result = $Redis_Model->hmsetField($AccountStatus_Key, $mkv);  //用户状态
        }
    }

    //重新设置所有用户分数为0
    protected function resetAllAccountScore($room_id) {

        $Redis_Model   = Redis_Model::getModelObject();
        $replyArr      = array("[roomid]" => $room_id);
        $RoomScore_Key = strtr(Redis_Const::RoomScore_Key, $replyArr);

        $mkv     = array();
        $members = $this->queryRoomMembers($room_id);
        if (is_array($members) && count($members)) {
            foreach ($members as $account_id) {
                $mkv[$account_id] = 0;
            }
        }
        if (count($mkv) > 0) {
            $mset_result = $Redis_Model->hmsetField($RoomScore_Key, $mkv);  //用户状态
        }
    }

    //广播用户状态改变
    protected function notyUpdateAccountStatusToGroup($room_id, $noty_arr, $exclude_client_id = NULL) {

        if (!isset($noty_arr['account_id']) || !isset($noty_arr['account_status'])) {
            $this->writeLog("function(notyUpdateAccountStatusToGroup):lack of params" . " in file" . __FILE__ . " on Line " . __LINE__);
            return FALSE;
        }

        $msg_arr = array("result" => "0", "operation" => "UpdateAccountStatus", "data" => $noty_arr, "result_message" => "用户状态改变");
        $this->pushMessageToGroup($room_id, $msg_arr, $exclude_client_id = NULL);
    }

    //修复用户状态,加入房间
    protected function joinRoomFixAccountStatus($room_id, $account_id) {
        //判断是否已扣房卡
        $ticket_checked = $this->queryTicketChecked($room_id, $account_id);
        if ($ticket_checked >= 1) {
            $account_status = Game::AccountStatus_Notready;
        } else {
            $account_status = Game::AccountStatus_Initial;
        }
        $this->updateAccountStatusNotNoty($room_id, $account_id, $account_status);

        return $account_status;
    }

    //修复用户状态,用户状态
    // protected function playOptFixAccountStatus($room_id,$account_id)
    // {
    // 	$room_status = $this->queryRoomStatus($room_id);
    // 	if($room_status == Game::RoomStatus_Waiting)
    // 	{
    // 		$account_status = $this->queryAccountStatus($room_id, $account_id);
    // 		if($account_status != Game::AccountStatus_Initial && $account_status != Game::AccountStatus_Notready && $account_status != Game::AccountStatus_Ready)
    // 		{
    // 			//判断是否已扣房卡
    // 			$ticket_checked = $this->queryTicketChecked($room_id, $account_id);
    // 			if($ticket_checked >= 1)
    // 			{
    // 				$account_status = Game::AccountStatus_Notready;
    // 			}
    // 			else
    // 			{
    // 				$account_status = Game::AccountStatus_Initial;
    // 			}
    // 			$this->logMessage('error', "function(startGameFixAccountStatus):room_id : ".$room_id." account_id: ".$account_id." status error :".$account_status." in file".__FILE__." on Line ".__LINE__);
    // 			$this->updateAccountStatus($room_id, $account_id, $account_status);
    // 			return false;
    // 		}
    // 	}
    // 	return true;
    // }

    //修复用户状态,加入房间
    protected function startGameFixAccountStatus($room_id) {
        $Redis_Model       = Redis_Model::getModelObject();
        $replyArr          = array("[roomid]" => $room_id);
        $AccountStatus_Key = strtr(Redis_Const::AccountStatus_Key, $replyArr);

        $result = $Redis_Model->hgetallField($AccountStatus_Key);
        if (Redis_CONST::DATA_NONEXISTENT !== $result) {
            foreach ($result as $account_id => $account_status) {
                if ($account_status != Game::AccountStatus_Initial && $account_status != Game::AccountStatus_Notready && $account_status != Game::AccountStatus_Ready && $account_status != Game::AccountStatus_Watch) {
                    $this->logMessage('error', "function(startGameFixAccountStatus):room_id : " . $room_id . " account_id: " . $account_id . " status error :" . $account_status . " in file" . __FILE__ . " on Line " . __LINE__);
                    $this->updateAccountStatus($room_id, $account_id, Game::AccountStatus_Notready);
                }
            }
        }
        return TRUE;
    }


    /***********************************
     * card_function
     ***********************************/
    /*
		计算手牌最大用户
	*/
    protected function calculateMaxValue($room_id) {
        $room_data  = $this->queryRoomData($room_id);
        $base_score = $room_data[Redis_CONST::Room_Field_BaseScore];

        $max_player = -1;
        $max_value  = -1;
        $card_times = 1;
        //获取所有游戏用户
        $player_array = $this->queryPlayMember($room_id);
        foreach ($player_array as $player_id) {
            $player_result = $this->calculateCardValue($room_id, $player_id, "", $room_data);
            if ($player_result['card_value'] > $max_value) {
                $max_player = $player_id;
                $card_times = $player_result['card_times'];
            }
        }

        //获取闲家叫分倍数
        $multiples = 1;

        $score = $base_score * $multiples * $card_times;

        return array("account_id" => $max_player, "score" => $score);
    }

    /*
		比牌
	*/
    protected function compareCards($room_id, $banker_id, $player_id) {
        $room_data = $this->queryRoomData($room_id);
        $baseScore = $room_data[Redis_CONST::Room_Field_BaseScore];

        $playerTimes = $this->queryPlayerMultiples($room_id, $player_id);
        $bankerTimes = $this->queryBankerMultiples($room_id);

        $bankerCard = $this->calculateCardValue($room_id, $banker_id, "", $room_data);
        $playerCard = $this->calculateCardValue($room_id, $player_id, "", $room_data);

        // 返回 闲家 得分
        // 得分 = 底分 * 玩家抢庄倍数（默认1）* 庄家抢庄倍数（默认1）* 赢家牌的倍数

        return $bankerCard['card_value'] < $playerCard['card_value']
            ? $baseScore * $playerTimes * $bankerTimes * $playerCard['card_times']
            : -$baseScore * $playerTimes * $bankerTimes * $bankerCard['card_times'];
    }

    protected function calculateCardValue($room_id, $player_id, $cards = "", $room_data = "") {
        //获取房间规则
        if ($room_data == "") {
            $room_data = $this->queryRoomData($room_id);
        }
        $is_bj = $room_data[Redis_CONST::Room_Field_Is_Bj] ? 1 : 0;

        $Redis_Model = Redis_Model::getModelObject();
        $replyArr    = array("[roomid]" => $room_id);
        if ($cards == "") {
            $Card_Key = strtr(Redis_Const::Card_Key, $replyArr);
            $card_str = $Redis_Model->hgetField($Card_Key, $player_id);

            $cards = explode(",", $card_str);
            if (count($cards) != 3) {
                $this->logMessage('error', "function(calculateCardValue):1st cards count error room_id:" . $room_id . " player_id:" . $player_id . " in file" . __FILE__ . " on Line " . __LINE__);
                $Card_Key = strtr(Redis_Const::Card_Key, $replyArr);
                $card_str = $Redis_Model->hgetField($Card_Key, $player_id);
                $cards    = explode(",", $card_str);
            }
        }

        return $this->_cardValue($cards, $is_bj);
    }

    /**
     * 计算牌型、牌点、牌值、倍数
     *
     * @param array $cards 牌      比如：[B5,B11,D6]
     * @param bool  $is_bj 是否开启了暴玖
     * @return array
     */
    protected function _cardValue($cards, $is_bj = FALSE) {
        $result = [
            'card_type'  => 0,  // 牌型
            'card_point' => 0,  // 点数
            'card_value' => 0,  // 牌值 牌型.公牌数.最大单牌值
            'card_text'  => '', // 牌型
            'card_times' => 1,  // 倍数
        ];

        if (3 != count($cards)) {
            return $result;
        }

        $gong     = $big_joker = $small_joker = $point = $maxCard = 0; // 公牌数 有无大王 有无小王 点数 最大单牌值
        $numbers  = [];
        $flipPile = $this->pile(TRUE);

        foreach ($cards as $card) {
            $number = substr($card, 1);
            // 最大单牌索引值
            if (isset($flipPile[$card]) && $maxCard < $flipPile[$card]) {
                $maxCard = $flipPile[$card];
            }
            // J、Q、K 公牌
            if (10 < $number && $number < 14) {
                $gong += 1;
            }
            if ('X0' == $card) {
                $big_joker = 1;
            }
            if ('Y0' == $card) {
                $small_joker = 1;
            }
            $point     += $number > 9 ? 0 : $number;
            $numbers[] = $number;
        }

        $point    %= 10;
        $numCount = array_count_values($numbers);

        if ($big_joker && $small_joker) {
            $result['card_type']  = 16;
            $result['card_text']  = '天公';
            $result['card_times'] = Game::Rule_TG_Multiple;
        } else if ($is_bj && isset($numCount[3]) && $numCount[3] == 3) {
            $result['card_type']  = 15;
            $result['card_text']  = '暴玖';
            $result['card_times'] = Game::Rule_BJ_Multiple;
        } else if (3 == $gong && 3 == max($numCount)) {
            $result['card_type']  = 14;
            $result['card_text']  = '大三公';
            $result['card_times'] = Game::Rule_DSG_Multiple;
        } else if (0 == $gong && 3 == max($numCount)) {
            $result['card_type']  = 13;
            $result['card_text']  = '小三公';
            $result['card_times'] = Game::Rule_XSG_Multiple;
        } else if (1 == $big_joker) {
            $result['card_type']  = 12;
            $result['card_text']  = '雷公';
            $result['card_times'] = Game::Rule_LG_Multiple;
        } else if (3 == $gong) {
            $result['card_type']  = 11;
            $result['card_text']  = '三公';
            $result['card_times'] = Game::Rule_SG_Multiple;
        } else if (1 == $small_joker) {
            $result['card_type']  = 10;
            $result['card_text']  = '地公';
            $result['card_times'] = Game::Rule_DG_Multiple;
        } else if (9 == $point) {
            $result['card_type']  = $point;
            $result['card_text']  = '九点';
            $result['card_times'] = Game::Rule_Card9_Multiple;
        } else if (8 == $point) {
            $result['card_type']  = $point;
            $result['card_text']  = '八点';
            $result['card_times'] = Game::Rule_Card8_Multiple;
        } else if (7 == $point) {
            $result['card_type']  = $point;
            $result['card_text']  = '七点';
            $result['card_times'] = Game::Rule_Card7_Multiple;
        } else {
            $temp                 = [6 => '六点', 5 => '五点', 4 => '四点', 3 => '三点', 2 => '二点', 1 => '一点', 0 => '零点'];
            $result['card_type']  = $point;
            $result['card_text']  = isset($temp[$point]) ? $temp[$point] : '';
            $result['card_times'] = Game::Rule_Card0_Multiple;
        }

        $result['card_value'] = 1000 * $result['card_type'] + 100 * $gong + $maxCard;

        return $result;
    }

    // 发牌 全随机规则
    protected function dealCard($player_count, $is_joker = FALSE) {
        $cards = $this->pile();

        if (!$is_joker) {
            unset($cards[52], $cards[53]);
        }

        shuffle($cards);

        $indexes = array_rand($cards, 3 * $player_count);

        shuffle($indexes);

        $num          = 0;
        $player_cards = [];

        for ($i = 0; $i < $player_count; $i++) {
            $player_cards[] = $cards[$indexes[$num]] . ',' . $cards[$indexes[$num + 1]] . ',' . $cards[$indexes[$num + 2]];
            $num            += 3;
        }

        return $player_cards;
    }

    protected function pile($is_flip = FALSE) {
        $pile = [
            'D1', 'C1', 'B1', 'A1',
            'D2', 'C2', 'B2', 'A2',
            'D3', 'C3', 'B3', 'A3',
            'D4', 'C4', 'B4', 'A4',
            'D5', 'C5', 'B5', 'A5',
            'D6', 'C6', 'B6', 'A6',
            'D7', 'C7', 'B7', 'A7',
            'D8', 'C8', 'B8', 'A8',
            'D9', 'C9', 'B9', 'A9',
            'D10', 'C10', 'B10', 'A10',
            'D11', 'C11', 'B11', 'A11',
            'D12', 'C12', 'B12', 'A12',
            'D13', 'C13', 'B13', 'A13',
            'X0', 'Y0'
        ];

        return $is_flip ? array_flip($pile) : $pile;
    }

    /***********************************
     * timer_function
     ***********************************/

    //设置 自动摊牌 定时器
    protected function setupShowPassiveTimer($room_id) {
        $banker_mode    = $this->queryBankerMode($room_id);
        $countDown_show = $this->queryGameCountDownShow($room_id);
        $limit_time     = $countDown_show > 0 ? $countDown_show : Game::LimitTime_Show;
        $limit_time     += 1;
        if ($banker_mode == Game::BankerMode_NoBanker) {
            $limit_time += 1;
        }

        $this->updateReadyTime($room_id, time());
        $callback_array = array(
            'operation' => "ShowPassive",
            'room_id'   => $room_id,
            'data'      => array()
        );
        $arr            = array(
            'operation' => "BuildTimer",
            'room_id'   => $room_id,
            'data'      => array(
                'limit_time'     => $limit_time,
                'callback_array' => $callback_array
            )
        );
        $this->sendToTimerServer($arr);
    }

    //设置 自动叫分 定时器
    protected function setupBetPassiveTimer($room_id, $is_only = 0, $banker_mode) {
        $countDown_bet = $this->queryGameCountDownBet($room_id);
        $limit_time    = $countDown_bet > 0 ? $countDown_bet : Game::LimitTime_Betting;
        //$limit_time = Game::LimitTime_Betting;
        $no_countDown = 0;    //没有倒计时

        //通比牛牛没有下注
        if ($banker_mode == Game::BankerMode_NoBanker) {
            $no_countDown = 1;
        }


        if ($no_countDown <= 0) {
            if ($is_only != 1) {
                $limit_time = $limit_time + 5;
            } else {
                $limit_time = $limit_time + 1;
            }

            $this->updateReadyTime($room_id, time());
            $callback_array = array(
                'operation' => "BetPassive",
                'room_id'   => $room_id,
                'data'      => array()
            );
            $arr            = array(
                'operation' => "BuildTimer",
                'room_id'   => $room_id,
                'data'      => array(
                    'limit_time'     => $limit_time,
                    'callback_array' => $callback_array
                )
            );
            $this->sendToTimerServer($arr);
        } else {
            //下注结束设置
            $this->betPassiveOpt($room_id);
        }
    }

    //设置 自动不叫庄 定时器
    protected function setupGrabPassiveTimer($room_id, $banker_mode) {
        $countDown_grab = $this->queryGameCountDownGrab($room_id);
        $limit_time     = $countDown_grab > 0 ? $countDown_grab : Game::LimitTime_Grab;
        $limit_time     += 1;
        $no_countDown   = 0;    //没有倒计时

        switch ($banker_mode) {
            case Game::BankerMode_FreeGrab:
                //$limit_time = Game::LimitTime_Grab_1;
                $no_countDown = 0;
                break;
            case Game::BankerMode_SeenGrab:
                //$limit_time = Game::LimitTime_Grab_2;
                $no_countDown = 0;
                break;
            case Game::BankerMode_TenGrab:
                //$limit_time = Game::LimitTime_Grab_3;
                $no_countDown = 1;
                break;
            case Game::BankerMode_NoBanker:
                //$limit_time = Game::LimitTime_Grab_4;
                $no_countDown = 1;
                break;
            case Game::BankerMode_FixedBanker:
                $game_num = $this->queryGameNumber($room_id);
                if ($game_num > 1) {
                    //$limit_time = Game::LimitTime_Grab_5;
                    $no_countDown = 1;
                } else {
                    //return true;
                    $no_countDown = 0;
                }
                break;
        }

        if ($no_countDown <= 0) {
            $this->updateReadyTime($room_id, time());
            $callback_array = array(
                'operation' => "GrabPassive",
                'room_id'   => $room_id,
                'data'      => array()
            );
            $arr            = array(
                'operation' => "BuildTimer",
                'room_id'   => $room_id,
                'data'      => array(
                    'limit_time'     => $limit_time,
                    'callback_array' => $callback_array
                )
            );
            $this->sendToTimerServer($arr);
        } else {
            //抢庄结束设置
            $this->grabPassiveOpt($room_id);
        }
    }


    //设置 自动开局 定时器
    protected function setupStartGamePassiveTimer($room_id, $is_pushTimer = FALSE) {

        $this->updateReadyTime($room_id, time());
        //$limit_time = $this->queryGameNumber($room_id) > 0 ? Game::LimitTime_Ready : Game::LimitTime_StartGame;
        $countDown_ready = $this->queryGameCountDownReady($room_id);
        $limit_time      = $countDown_ready > 0 ? $countDown_ready : Game::LimitTime_Ready;
        $limit_time      += 1;
        $callback_array  = array(
            'operation' => "StartGamePassive",
            'room_id'   => $room_id,
            'data'      => array()
        );
        $arr             = array(
            'operation' => "BuildTimer",
            'room_id'   => $room_id,
            'data'      => array(
                'limit_time'     => $limit_time,
                'callback_array' => $callback_array
            )

        );
        $this->sendToTimerServer($arr);

        if ($is_pushTimer) {
            $arr = array("result" => 0, "operation" => "StartLimitTime", "data" => array('limit_time' => $limit_time - 1), "result_message" => "开始倒计时");
            $this->pushMessageToGroup($room_id, $arr);
        }
    }

    //设置 清理房间 定时器
    protected function setupClearRoomPassiveTimer($room_id) {

        $callback_array = array(
            'operation' => "ClearRoomPassive",
            'room_id'   => $room_id,
            'data'      => array()
        );
        $arr            = array(
            'operation' => "BuildTimer",
            'room_id'   => $room_id,
            'data'      => array(
                'limit_time'     => Game::LimitTime_ClearRoom,
                'callback_array' => $callback_array
            )
        );
        $this->sendToTimerServer($arr);
        $this->updateReadyTime($room_id, -1);
    }

    //取消定时器
    protected function deleteRoomTimer($room_id) {

        $timer_id = $this->queryRoomTimerId($room_id);
        if ($timer_id > 0) {
            $arr = array(
                'operation' => "DeleteTimer",
                'room_id'   => $room_id,
                'data'      => array(
                    'timer_id' => $timer_id
                )

            );
            $this->sendToTimerServer($arr);
        }
        $this->updateRoomTimerId($room_id, -1);
        $this->updateReadyTime($room_id, -1);
    }

    /*
		推给定时器服务器
    */
    public function sendToTimerServer($msg_array = "") {
        $message = $this->_JSON($msg_array);

        $socket_name  = "Timer";
        $Socket_Model = Socket_Model::getModelObject($socket_name);
        $Socket_Model->sendMessageToSocket($socket_name, $message);

        return TRUE;

        // if($msg_array == "")
        // {
        // 	$this->writeLog("function(sendToTimerServer): msg_array is empty string"." in file".__FILE__." on Line ".__LINE__);
        // 	return false;
        // }

        // $service_port = Config::Timer_Port;
        // $address = Config::Timer_Address;

        // $socket = socket_create(AF_INET, SOCK_STREAM, SOL_TCP);
        // if ($socket === false)
        // {
        // 	$this->writeLog("function(sendToTimerServer):socket_create() failed:".socket_strerror(socket_last_error())." in file".__FILE__." on Line ".__LINE__);
        // 	return false;
        // }
        // else
        // {
        // 	$result = socket_connect($socket, $address, $service_port);
        // 	if($result === false)
        // 	{
        // 		$this->writeLog("function(sendToTimerServer):socket_connect() failed.\nReason: ($result) " . socket_strerror(socket_last_error($socket)) ." in file".__FILE__." on Line ".__LINE__);
        // 		return false;
        // 	}
        // 	else
        // 	{
        // 		//$message = '{"operation":"CtrlCntr-1","data":{"order_code":"'.$order_code.'"},"app_ver":"2.0.0"}';
        // 		$message = $this->_JSON($msg_array);
        // 		//echo $message.PHP_EOL;
        // 		$message .= "\n";

        // 		$result = socket_write($socket, $message, strlen($message));
        // 		if($result == false)
        // 		{
        // 			$this->writeLog("function(sendToTimerServer):socket_write failed :".$result." in file".__FILE__." on Line ".__LINE__);
        // 			return false;
        // 		}
        // 	}
        // 	socket_close($socket);
        // }

        // return true;
    }


    /***********************************
     * database_function
     ***********************************/
    //保存开始准备时间
    protected function updateReadyTime($room_id, $ready_time) {

        $Redis_Model                            = Redis_Model::getModelObject();
        $replyArr                               = array("[roomid]" => $room_id);
        $Room_Key                               = strtr(Redis_Const::Room_Key, $replyArr);
        $mkv[Redis_Const::Room_Field_ReadyTime] = $ready_time;    //开始倒计时的时间点
        $mset_result                            = $Redis_Model->hmsetField($Room_Key, $mkv);
    }

    //显示房间准备倒计时的数值
    protected function showRoomCountdown($room_id) {

        $Redis_Model = Redis_Model::getModelObject();
        $replyArr    = array("[roomid]" => $room_id);
        $Room_Key    = strtr(Redis_Const::Room_Key, $replyArr);

        $ready_time = $Redis_Model->hgetField($Room_Key, Redis_Const::Room_Field_ReadyTime);

        if ($ready_time > 0) {

            $circle = $this->queryCircle($room_id);

            if ($circle == Game::Circle_Grab) {
                $countDown_grab = $this->queryGameCountDownGrab($room_id);
                $limit_time     = $countDown_grab > 0 ? $countDown_grab : Game::LimitTime_Grab;
            } else if ($circle == Game::Circle_Bet) {
                $countDown_bet = $this->queryGameCountDownBet($room_id);
                $limit_time    = $countDown_bet > 0 ? $countDown_bet : Game::LimitTime_Betting;
            } else if ($circle == Game::Circle_Show) {
                $countDown_show = $this->queryGameCountDownShow($room_id);
                $limit_time     = $countDown_show > 0 ? $countDown_show : Game::LimitTime_Show;
            } else {
                $countDown_ready = $this->queryGameCountDownReady($room_id);
                $limit_time      = $countDown_ready > 0 ? $countDown_ready : Game::LimitTime_Ready;
                //$limit_time = $this->queryGameNumber($room_id) > 0 ? Game::LimitTime_Ready : Game::LimitTime_StartGame;
            }

            $countdown = $limit_time - time() + $ready_time;

            if ($countdown > 0) {
                $arr = array("result" => 0, "operation" => "StartLimitTime", "data" => array('limit_time' => $countdown), "result_message" => "开始倒计时");
                $this->pushMessageToCurrentClient($arr);
            }
        }
        return;
    }

    //显示房间准备倒计时的数值
    protected function pushRoomCountdown($room_id, $ready_time, $game_num) {

        if ($ready_time > 0) {

            $circle = $this->queryCircle($room_id);

            if ($circle == Game::Circle_Grab) {
                $countDown_grab = $this->queryGameCountDownGrab($room_id);
                $limit_time     = $countDown_grab > 0 ? $countDown_grab : Game::LimitTime_Grab;
            } else if ($circle == Game::Circle_Bet) {
                $countDown_bet = $this->queryGameCountDownBet($room_id);
                $limit_time    = $countDown_bet > 0 ? $countDown_bet : Game::LimitTime_Betting;
            } else if ($circle == Game::Circle_Show) {
                $countDown_show = $this->queryGameCountDownShow($room_id);
                $limit_time     = $countDown_show > 0 ? $countDown_show : Game::LimitTime_Show;
            } else {
                $countDown_ready = $this->queryGameCountDownReady($room_id);
                $limit_time      = $countDown_ready > 0 ? $countDown_ready : Game::LimitTime_Ready;
                //$limit_time = $game_num > 0 ? Game::LimitTime_Ready : Game::LimitTime_StartGame;
            }

            $countdown = $limit_time - time() + $ready_time;

            if ($countdown > 0) {
                $arr = array("result" => 0, "operation" => "StartLimitTime", "data" => array('limit_time' => $countdown), "result_message" => "开始倒计时");
                $this->pushMessageToCurrentClient($arr);
            }
        }
        return;
    }


    //保存定时器id
    protected function updateRoomTimerId($room_id, $timer_id) {

        $Redis_Model = Redis_Model::getModelObject();
        $replyArr    = array("[roomid]" => $room_id);
        $Room_Key    = strtr(Redis_Const::Room_Key, $replyArr);

        $mkv[Redis_Const::Room_Field_ActiveTimer] = $timer_id;
        $mset_result                              = $Redis_Model->hmsetField($Room_Key, $mkv);
    }

    //获取定时器id
    protected function queryRoomTimerId($room_id) {

        $Redis_Model = Redis_Model::getModelObject();
        $replyArr    = array("[roomid]" => $room_id);
        $Room_Key    = strtr(Redis_Const::Room_Key, $replyArr);

        $result = $Redis_Model->hgetField($Room_Key, Redis_Const::Room_Field_ActiveTimer);
        return $result;
    }

    //获取房主id
    protected function queryCreator($room_id) {
        $Redis_Model = Redis_Model::getModelObject();
        $replyArr    = array("[roomid]" => $room_id);
        $Room_Key    = strtr(Redis_Const::Room_Key, $replyArr);
        $result      = $Redis_Model->hgetField($Room_Key, Redis_Const::Room_Field_Creator);
        return $result;
    }

    //更新房间状态
    protected function updateRoomStatus($room_id, $status) {

        $Redis_Model = Redis_Model::getModelObject();
        $replyArr    = array("[roomid]" => $room_id);
        $Room_Key    = strtr(Redis_Const::Room_Key, $replyArr);

        $mkv[Redis_Const::Room_Field_Status] = $status;
        $mset_result                         = $Redis_Model->hmsetField($Room_Key, $mkv);
    }

    //获取房间用户有序集合
    protected function queryRoomMembers($room_id) {
        $Redis_Model               = Redis_Model::getModelObject();
        $replyArr                  = array("[roomid]" => $room_id);
        $RoomSequence_Key          = strtr(Redis_Const::RoomSequence_Key, $replyArr);
        $zrange_array['is_zrange'] = TRUE;
        $zrange_array['key']       = $RoomSequence_Key;
        $zrange_array['start']     = 0;
        $zrange_array['stop']      = -1;
        $members                   = $Redis_Model->zrangeSet($zrange_array);
        if (is_array($members)) {
            return $members;
        } else {
            return array();
        }

    }

    //获取房间每轮需要消耗的房卡数
    protected function queryTicketCount($room_id) {

        $Redis_Model = Redis_Model::getModelObject();
        $replyArr    = array("[roomid]" => $room_id);
        $Room_Key    = strtr(Redis_Const::Room_Key, $replyArr);

        $result = $Redis_Model->hgetField($Room_Key, Redis_Const::Room_Field_TicketCount);
        return $result > 0 ? $result : 1;
    }

    //获取房间房卡支付方式
    protected function queryPayType($room_id) {

        $Redis_Model = Redis_Model::getModelObject();
        $replyArr    = array("[roomid]" => $room_id);
        $Room_Key    = strtr(Redis_Const::Room_Key, $replyArr);

        $result = $Redis_Model->hgetField($Room_Key, Redis_Const::Room_Field_Paytype);
        return $result > 0 ? $result : Config::Ticket_Mode;
    }

    //获取房间数据
    protected function queryRoomData($room_id) {
        $respond = [
            Redis_CONST::Room_Field_Number          => '-1',
            Redis_CONST::Room_Field_GameRound       => '-1',
            Redis_CONST::Room_Field_GameNum         => '-1',
            Redis_CONST::Room_Field_Status          => '-1',
            Redis_CONST::Room_Field_DefaultScore    => '-1',
            Redis_CONST::Room_Field_ActiveUser      => '-1',
            Redis_CONST::Room_Field_ActiveTimer     => '-1',
            Redis_CONST::Room_Field_ReadyTime       => '-1',
            Redis_CONST::Room_Field_Creator         => '-1',
            Redis_CONST::Room_Field_Paytype         => Config::Ticket_Mode,
            Redis_CONST::Room_Field_Scoreboard      => '',
            Redis_CONST::Room_Field_BaseScore       => Game::Default_Score,
            Redis_CONST::Room_Field_TicketCount     => Game::Default_SpendTicketCount,
            Redis_CONST::Room_Field_TotalNum        => Config::GameNum_EachRound,
            Redis_CONST::Room_Field_Is_Joker        => 0,
            Redis_CONST::Room_Field_Is_Bj           => 0,
            Redis_CONST::Room_Field_BankerMode      => Game::BankerMode_FreeGrab,
            Redis_CONST::Room_Field_BankerScoreType => 1,
            Redis_CONST::Room_Field_BankerScore     => Config::BankerScore_1,
            Redis_CONST::Room_Field_StartTime       => -1
        ];

        $Redis_Model = Redis_Model::getModelObject();
        $replyArr    = array("[roomid]" => $room_id);
        $Room_Key    = strtr(Redis_Const::Room_Key, $replyArr);
        $result      = $Redis_Model->hgetallField($Room_Key);

        if ($result == Redis_CONST::DATA_NONEXISTENT) {
            return $respond;
        }

        foreach ($respond as $key => $val) {
            $respond[$key] = isset($result[$key]) ? $result[$key] : $val;
        }

        return $result;
    }

    //整理规则内容文本
    protected function formatRuleText($room_id, $room_data = "") {
        $rule_ary = $room_data;

        if (empty($rule_ary)) {
            $rule_ary = $this->queryRoomData($room_id);
        }

        $rule_text = "";

        //局数房卡规则
        if ($rule_ary[Redis_CONST::Room_Field_TicketCount] == 1) {
            $spend_ticket_count = Game::Rule_TicketType_1;
        } else {
            $spend_ticket_count = Game::Rule_TicketType_2;
        }

        $ticket_type_text = (Config::GameNum_EachRound * $spend_ticket_count) . "局/" . $spend_ticket_count . "张房卡";
        $rule_text        .= $ticket_type_text;

        //游戏类型
        $rule_text .= ",";
        switch ($rule_ary[Redis_CONST::Room_Field_BankerMode]) {
            case Game::BankerMode_FreeGrab:
                $rule_text .= "自由抢庄";
                break;
            case Game::BankerMode_SeenGrab:
                $rule_text .= "明牌抢庄";
                break;
            case Game::BankerMode_TenGrab:
                $rule_text .= "牛牛上庄";
                break;
            case Game::BankerMode_NoBanker:
                $rule_text .= "通比牛牛";
                break;
            case Game::BankerMode_FixedBanker:
                $rule_text .= "固定庄家";
                $rule_text .= ",";
                $rule_text .= "上庄" . $rule_ary[Redis_CONST::Room_Field_BankerScore] . "分";
                break;
        }

        //底分
        $rule_text .= ",";
        switch ($rule_ary[Redis_CONST::Room_Field_BaseScore]) {
            case 1:
                $rule_text .= "底分" . Config::Rule_ScoreType_1 . "分";
                break;
            case 2:
                $rule_text .= "底分" . Config::Rule_ScoreType_2 . "分";
                break;
            case 3:
                $rule_text .= "底分" . Config::Rule_ScoreType_3 . "分";
                break;
            case 4:
                $rule_text .= "底分" . Config::Rule_ScoreType_4 . "分";
                break;
            case 5:
                $rule_text .= "底分" . Config::Rule_ScoreType_5 . "分";
                break;
        }


        if ($rule_ary[Redis_CONST::Room_Field_Is_Joker] == 1) {
            $rule_text .= ",";
            $rule_text .= "天公x9-雷公x7-地公x7";
        }
        if ($rule_ary[Redis_CONST::Room_Field_Is_Bj] == 1) {
            $rule_text .= ",";
            $rule_text .= "暴玖x9";
        }

        return $rule_text;
    }


    //获取房间一轮总局数
    protected function queryTotalNum($room_id) {

        $Redis_Model = Redis_Model::getModelObject();
        $replyArr    = array("[roomid]" => $room_id);
        $Room_Key    = strtr(Redis_Const::Room_Key, $replyArr);

        $result = $Redis_Model->hgetField($Room_Key, Redis_Const::Room_Field_TotalNum);
        return $result;
    }

    //获取房间状态
    protected function queryRoomStatus($room_id) {

        $Redis_Model = Redis_Model::getModelObject();
        $replyArr    = array("[roomid]" => $room_id);
        $Room_Key    = strtr(Redis_Const::Room_Key, $replyArr);

        $result = $Redis_Model->hgetField($Room_Key, Redis_Const::Room_Field_Status);
        if ($result === FALSE) {
            $this->initRoomData($room_id);
            $result = $Redis_Model->hgetField($Room_Key, Redis_Const::Room_Field_Status);
        }
        return $result;
    }

    //获取房间状态
    protected function queryBankerMode($room_id) {

        $Redis_Model = Redis_Model::getModelObject();
        $replyArr    = array("[roomid]" => $room_id);
        $Room_Key    = strtr(Redis_Const::Room_Key, $replyArr);

        $result = $Redis_Model->hgetField($Room_Key, Redis_Const::Room_Field_BankerMode);
        if ($result === FALSE) {
            $result = Game::BankerMode_FreeGrab;
        }
        return $result;
    }

    //更新游戏开局时间
    protected function updateStartTime($room_id) {
        $Redis_Model = Redis_Model::getModelObject();
        $replyArr    = array("[roomid]" => $room_id);
        $Room_Key    = strtr(Redis_Const::Room_Key, $replyArr);

        $mkv[Redis_Const::Room_Field_StartTime] = time();
        $mset_result                            = $Redis_Model->hmsetField($Room_Key, $mkv);
        return $mset_result;
    }


    //重置房间信息
    protected function initRoomData($room_id, $is_delete = FALSE) {
        $Redis_Model = Redis_Model::getModelObject();
        $replyArr    = array("[roomid]" => $room_id);

        //关闭房间
        $Room_Key = strtr(Redis_Const::Room_Key, $replyArr);

        //$Chip_Key = strtr(Redis_Const::Chip_Key, $replyArr);
        //$SeenCard_Key = strtr(Redis_Const::SeenCard_Key, $replyArr);
        $Card_Key          = strtr(Redis_Const::Card_Key, $replyArr);
        $PlayMember_Key    = strtr(Redis_Const::PlayMember_Key, $replyArr);
        $Play_Key          = strtr(Redis_Const::Play_Key, $replyArr);
        $AccountStatus_Key = strtr(Redis_Const::AccountStatus_Key, $replyArr);

        $RoomScore_Key     = strtr(Redis_Const::RoomScore_Key, $replyArr);
        $TicketChecked_Key = strtr(Redis_Const::TicketChecked_Key, $replyArr);

        $RoomSequence_Key = strtr(Redis_Const::RoomSequence_Key, $replyArr);
        $Grab_Key         = strtr(Redis_Const::Grab_Key, $replyArr);

        //判断是否为包厢房间,清除空房记录、玩家包厢房间游戏记录
        $Room_Key = strtr(Redis_CONST::Room_Key, $replyArr);
        $box_number = $Redis_Model->hgetField($Room_Key, Redis_CONST::Room_field_box);
        if($box_number && $box_number != Game::ORDINARY_ROOM){
            $replyArr["[boxnumber]"] = $box_number;
            //清除包厢空房记录
            $BoxEmptyRoom_Key = strtr(Redis_CONST::BoxEmptyRoom_Key, $replyArr);
            $srem_result = $Redis_Model->sremSet($BoxEmptyRoom_Key, array("$room_id"));
            if($srem_result == OPT_CONST::DATA_NONEXISTENT){
                $this->logMessage('error', "function(initRoomData):delete BoxEmptyRoom_Key  box:[$box_number] rid:[$room_id] fail;" . " in file " . __FILE__ . " on Line " . __LINE__);
            }

            //清除玩家包厢房间游戏记录
            $zrange_array['is_zrange'] = TRUE;
            $zrange_array['key']       = $RoomSequence_Key;
            $zrange_array['start']     = 0;
            $zrange_array['stop']      = -1;
            $members                   = $Redis_Model->zrangeSet($zrange_array);
            if(is_array($members)){
                foreach($members as $key=>$account_id){
                    $BoxAccount_Key = strtr(Redis_Const::BoxAccount_Key, array("[accountid]"=>$account_id));
                    $hdel_result = $Redis_Model->hdelFiled($BoxAccount_Key, $box_number);
                    if(!$hdel_result){
                        $this->logMessage('error', "function(initRoomData):delete BoxAccount_Key box:[$box_number] rid:[$room_id] aid:[$account_id] fail;" . " in file " . __FILE__ . " on Line " . __LINE__);
                    }
                }
            }
        }

        $Room_mkv[Redis_Const::Room_Field_GameNum]      = 0;                            //游戏局数
        $Room_mkv[Redis_Const::Room_Field_Status]       = Game::RoomStatus_Closed;      //房间状态，1等待、2进行中、3未激活
        $Room_mkv[Redis_Const::Room_Field_DefaultScore] = Game::Default_Score;          //开局默认分数
        $Room_mkv[Redis_Const::Room_Field_Scoreboard]   = "";                           //积分榜清零
        $Room_mkv[Redis_Const::Room_Field_Creator]      = -1;                           //创建者清空

        $Room_mkv[Redis_Const::Room_Field_ActiveUser]  = -1;                            //当前操作用户
        $Room_mkv[Redis_Const::Room_Field_ActiveTimer] = -1;                            //当前生效timer
        $Room_mkv[Redis_Const::Room_Field_ReadyTime]   = -1;                            //房间倒计时

        $Room_mkv[Redis_Const::Room_Field_StartTime] = -1;                              //房间倒计时

        //$Room_mkv[Redis_Const::Room_Field_RuleType] = 1;                              //牛7倍率
        //$Room_mkv[Redis_Const::Room_Field_Card7] = 1;                                 //牛7倍率
        //$Room_mkv[Redis_Const::Room_Field_Card8] = 1;                                 //牛8倍率
        //$Room_mkv[Redis_Const::Room_Field_Card9] = 1;                                 //牛9倍率
        //$Room_mkv[Redis_Const::Room_Field_Card10] = 1;                                //牛10倍率
        //$Room_mkv[Redis_Const::Room_Field_CardFive] = 1;                              //五花牛倍率
        //$Room_mkv[Redis_Const::Room_Field_CardBomb] = 1;                              //炸弹牛倍率
        //$Room_mkv[Redis_Const::Room_Field_CardTiny] = 1;                              //五小牛倍率

        $mset_result = $Redis_Model->hmsetField($Room_Key, $Room_mkv);


        //删除每局玩家筹码hash
        //$Redis_Model->deleteKey($Chip_Key);
        //删除每局玩家看牌标识hash
        //$Redis_Model->deleteKey($SeenCard_Key);
        //删除每局玩家手牌hash
        $Redis_Model->deleteKey($Card_Key);
        //删除每局玩家顺序list
        $Redis_Model->deleteKey($PlayMember_Key);
        //删除每局游戏参数
        $Redis_Model->deleteKey($Play_Key);
        //删除用户状态hash
        $Redis_Model->deleteKey($AccountStatus_Key);
        //删除用户状态hash
        $Redis_Model->deleteKey($RoomScore_Key);
        $Redis_Model->deleteKey($TicketChecked_Key);
        $Redis_Model->deleteKey($RoomSequence_Key);
        $Redis_Model->deleteKey($Grab_Key);

        if (Config::Room_IsReuse == 0) {
            $MMYSQL = $this->initMysql();
            //房间设置成已关闭
            $MMYSQL->update(Room)->set("is_close", "1")->set('is_delete', ($is_delete ? '1' : '0'))->set('room_config', null)->where("room_id=" . $room_id)->query();

            $Redis_Model->deleteKey($Room_Key);
        }

        return TRUE;
    }

    //获取房间当前局数
    protected function queryGameNumber($room_id) {

        $Redis_Model = Redis_Model::getModelObject();
        $replyArr    = array("[roomid]" => $room_id);
        $Room_Key    = strtr(Redis_Const::Room_Key, $replyArr);

        $result = $Redis_Model->hgetField($Room_Key, Redis_Const::Room_Field_GameNum);
        return $result;
    }

    //自增长房间当前局数、轮数
    protected function updateGameNumberRound($room_id) {

        $Redis_Model = Redis_Model::getModelObject();
        $replyArr    = array("[roomid]" => $room_id);
        $Room_Key    = strtr(Redis_Const::Room_Key, $replyArr);

        $gnum = $Redis_Model->hincrbyField($Room_Key, Redis_Const::Room_Field_GameNum, 1);
        //echo  "新的一局 :".$gnum."   房间:".$room_id."   ".date("Y-m-d H:i:s").PHP_EOL;

        $this->writeLog("[$room_id] 新的一局 :" . $gnum);

        // $total_num = $this->queryTotalNum($room_id);

        // if($gnum > $total_num){
        // 	$Redis_Model->hmsetField($Room_Key,array(Redis_Const::Room_Field_GameNum=>1)); //局数置1
        // 	$ground = $Redis_Model->hincrbyField($Room_Key, Redis_Const::Room_Field_GameRound, 1);
        // 	//echo  "新的一轮 :".$ground.PHP_EOL;
        // 	$this->logMessage('error', "function(updateGameNumberRound):新的一轮 :".$ground." in file".__FILE__." on Line ".__LINE__);

        // 	//添加房间信息到redis
        // 	$r_mkv[Redis_Const::Room_Field_Status] = Game::RoomStatus_Waiting;				//房间状态，1等待、2进行中、3关闭
        // 	$r_mkv[Redis_Const::Room_Field_ActiveUser] = -1;		//当前操作用户
        // 	$r_mkv[Redis_Const::Room_Field_ActiveTimer] = -1;		//当前生效timer
        // 	$mset_result = $Redis_Model->hmsetField($Room_Key,$r_mkv);

        // 	$RoomScore_Key = strtr(Redis_Const::RoomScore_Key, $replyArr);
        // 	$Redis_Model->deleteKey($RoomScore_Key);
        // 	$AccountStatus_Key = strtr(Redis_Const::AccountStatus_Key, $replyArr);
        // 	$Redis_Model->deleteKey($AccountStatus_Key);

        // 	$members = $this->queryRoomMembers($room_id);
        // 	$status_mkv = array();
        // 	$score_mkv = array();
        // 	if(count($members) > 0){
        // 		foreach ($members as $account_id) {
        // 			$status_mkv[$account_id] = Game::AccountStatus_Initial;   //新的一轮，用户状态变为初始
        // 			$score_mkv[$account_id] = 0;
        // 		}
        // 	}
        // 	$mset_result = $Redis_Model->hmsetField($AccountStatus_Key,$status_mkv);
        // 	$mset_result = $Redis_Model->hmsetField($RoomScore_Key,$score_mkv);
        // }
    }

    /**
     * 函数描述：获取房间准备倒计时
     * @param $room_id
     * @return bool
     * author 黄欣仕
     * date 2019/1/14
     */
    protected function queryGameCountDownReady($room_id) {

        $Redis_Model = Redis_Model::getModelObject();
        $replyArr    = array("[roomid]" => $room_id);
        $Room_Key    = strtr(Redis_Const::Room_Key, $replyArr);

        $result = $Redis_Model->hgetField($Room_Key, Redis_Const::Room_Field_CountDown_Ready);
        return $result;
    }

    /**
     * 函数描述：获取房间下注倒计时
     * @param $room_id
     * @return bool
     * author 黄欣仕
     * date 2019/1/14
     */
    protected function queryGameCountDownBet($room_id) {

        $Redis_Model = Redis_Model::getModelObject();
        $replyArr    = array("[roomid]" => $room_id);
        $Room_Key    = strtr(Redis_Const::Room_Key, $replyArr);

        $result = $Redis_Model->hgetField($Room_Key, Redis_Const::Room_Field_CountDown_Bet);
        return $result;
    }

    /**
     * 函数描述：获取房间摊牌倒计时
     * @param $room_id
     * @return bool
     * author 黄欣仕
     * date 2019/1/14
     */
    protected function queryGameCountDownShow($room_id) {

        $Redis_Model = Redis_Model::getModelObject();
        $replyArr    = array("[roomid]" => $room_id);
        $Room_Key    = strtr(Redis_Const::Room_Key, $replyArr);

        $result = $Redis_Model->hgetField($Room_Key, Redis_Const::Room_Field_CountDown_Show);
        return $result;
    }

    /**
     * 函数描述：获取房间抢庄倒计时
     * @param $room_id
     * @return bool
     * author 黄欣仕
     * date 2019/1/14
     */
    protected function queryGameCountDownGrab($room_id) {

        $Redis_Model = Redis_Model::getModelObject();
        $replyArr    = array("[roomid]" => $room_id);
        $Room_Key    = strtr(Redis_Const::Room_Key, $replyArr);

        $result = $Redis_Model->hgetField($Room_Key, Redis_Const::Room_Field_CountDown_Grab);
        return $result;
    }

    //更新用户状态
    protected function updateAccountStatus($room_id, $account_id, $status, $multiples = "") {

        $Redis_Model       = Redis_Model::getModelObject();
        $replyArr          = array("[roomid]" => $room_id);
        $AccountStatus_Key = strtr(Redis_Const::AccountStatus_Key, $replyArr);

        $mkv[$account_id] = $status;
        $mset_result      = $Redis_Model->hmsetField($AccountStatus_Key, $mkv);

        //广播用户状态改变
        $noty_arr = array(
            'account_id'       => $account_id,
            'account_status'   => $status,
            'online_status'    => $this->queryOnlineStatus($room_id, $account_id),
            'banker_multiples' => $multiples
        );
        //echo "用户".$account_id. " 状态改变 ". $status.PHP_EOL;
        $this->writeLog("[$room_id] ($account_id) 状态改变" . $status);
        $this->notyUpdateAccountStatusToGroup($room_id, $noty_arr);
    }


    //更新用户状态,不通知
    protected function updateAccountStatusNotNoty($room_id, $account_id, $status) {

        $Redis_Model       = Redis_Model::getModelObject();
        $replyArr          = array("[roomid]" => $room_id);
        $AccountStatus_Key = strtr(Redis_Const::AccountStatus_Key, $replyArr);

        $mkv[$account_id] = $status;
        $mset_result      = $Redis_Model->hmsetField($AccountStatus_Key, $mkv);
        return TRUE;
    }

    //更新用户状态
    protected function updateAccountStatus2($room_id, $account_id, $status, $online_status) {

        $Redis_Model       = Redis_Model::getModelObject();
        $replyArr          = array("[roomid]" => $room_id);
        $AccountStatus_Key = strtr(Redis_Const::AccountStatus_Key, $replyArr);

        $mkv[$account_id] = $status;
        $mset_result      = $Redis_Model->hmsetField($AccountStatus_Key, $mkv);

        //广播用户状态改变
        $noty_arr = array(
            'account_id'     => $account_id,
            'account_status' => $status,
            'online_status'  => $online_status,
        );
        $this->writeLog("[$room_id] ($account_id) 状态改变 " . $status);
        $this->notyUpdateAccountStatusToGroup($room_id, $noty_arr);
    }

    //更新观众状态
    protected function updateAudienceInfo($room_id, $account_id, $status) {

        $Redis_Model          = Redis_Model::getModelObject();
        $replyArr             = array("[roomid]" => $room_id);
        $RoomAudience_Key     = strtr(Redis_Const::RoomAudience_Key, $replyArr);
        $RoomAudienceInfo_Key = strtr(Redis_Const::RoomAudienceInfo_Key, $replyArr);
        $rsAud_score          = $Redis_Model->getZscore($RoomAudience_Key, $account_id);

        //广播观众状态改变
        $arrData['key'] = $RoomAudienceInfo_Key;
        $arrData['min'] = $rsAud_score;
        $arrData['max'] = $rsAud_score;

        $AudienceInfo       = $Redis_Model->getSortedSetLimitByAry($arrData);
        $noty_arr           = json_decode($AudienceInfo[0], TRUE);
        $noty_arr['status'] = $status;

        $this->writeLog("[$room_id] ($account_id) 状态改变 " . $status);
        $msg_arr = array("result" => "0", "operation" => "UpdateAudienceInfo", "audience" => $noty_arr, "result_message" => "观众状态改变");
        $this->pushMessageToGroup($room_id, $msg_arr, $exclude_client_id = NULL);
        if ($status == Game::AudienceStatus_off) {
            $zrem_result = $Redis_Model->zremSet($RoomAudience_Key, array($account_id));
            $zrem_result = $Redis_Model->zremSetbyscore($RoomAudienceInfo_Key, $rsAud_score, $rsAud_score);
        }
    }


    //获取转入观战的人
    protected function queryWatchUser($room_id) {

        $Redis_Model       = Redis_Model::getModelObject();
        $replyArr          = array("[roomid]" => $room_id);
        $AccountStatus_Key = strtr(Redis_Const::AccountStatus_Key, $replyArr);

        $watch_user_array = [];
        $result           = $Redis_Model->hgetallField($AccountStatus_Key);
        if (Redis_CONST::DATA_NONEXISTENT !== $result) {
            foreach ($result as $account_id => $status) {
                if ($status == Game::AccountStatus_Watch) {
                    $watch_user_array[] = $account_id;
                }
            }
        }
        return $watch_user_array;
    }

    //获取在线状态
    protected function queryOnlineStatus($room_id, $account_id) {

        $replyArr = array("[roomid]" => $room_id, "[accountid]" => $account_id);
        $room_aid = strtr(Game::RoomUser_UID, $replyArr);

        $is_online = Gateway::isUidOnline($room_aid);
        return $is_online > 0 ? 1 : 0;
    }

    //获取准备人数
    protected function queryReadyCount($room_id) {

        $Redis_Model       = Redis_Model::getModelObject();
        $replyArr          = array("[roomid]" => $room_id);
        $AccountStatus_Key = strtr(Redis_Const::AccountStatus_Key, $replyArr);

        $result = $Redis_Model->hgetallField($AccountStatus_Key);
        $count  = 0;
        if (Redis_CONST::DATA_NONEXISTENT !== $result) {
            foreach ($result as $account_id => $status) {
                if ($status == Game::AccountStatus_Ready) {
                    $count++;
                }
            }
        }
        return $count;
    }

    //获取准备的人
    protected function queryReadyUser($room_id) {

        $Redis_Model       = Redis_Model::getModelObject();
        $replyArr          = array("[roomid]" => $room_id);
        $AccountStatus_Key = strtr(Redis_Const::AccountStatus_Key, $replyArr);

        $ready_user_array = [];
        $result           = $Redis_Model->hgetallField($AccountStatus_Key);
        if (Redis_CONST::DATA_NONEXISTENT !== $result) {
            foreach ($result as $account_id => $status) {
                if ($status == Game::AccountStatus_Ready) {
                    $ready_user_array[] = $account_id;
                }
            }
        }
        return $ready_user_array;
    }

    //获取在房的人
    protected function queryInRoomUser($room_id) {

        $in_room_user_array = [];
        $room_members       = $this->queryRoomMembers($room_id);

        foreach ($room_members as $account_id) {
            if ($this->queryOnlineStatus($room_id, $account_id)) {
                $in_room_user_array[] = $account_id;
            }
        }
        return $in_room_user_array;
    }

    //获取扣除了房卡的人
    protected function queryTicketCheckedUser($room_id) {

        $Redis_Model       = Redis_Model::getModelObject();
        $replyArr          = array("[roomid]" => $room_id);
        $TicketChecked_Key = strtr(Redis_Const::TicketChecked_Key, $replyArr);

        $user_array = [];
        $result     = $Redis_Model->hgetallField($TicketChecked_Key);
        if (Redis_CONST::DATA_NONEXISTENT !== $result) {
            foreach ($result as $account_id => $status) {
                if ($status >= 1) {
                    $user_array[] = $account_id;
                }
            }
        }
        return $user_array;
    }

    //获取用户状态
    protected function queryAccountStatus($room_id, $account_id) {

        $Redis_Model       = Redis_Model::getModelObject();
        $replyArr          = array("[roomid]" => $room_id);
        $AccountStatus_Key = strtr(Redis_Const::AccountStatus_Key, $replyArr);

        $result = $Redis_Model->hgetField($AccountStatus_Key, $account_id);
        return $result;
    }

    //获取用户状态
    protected function queryAccountStatusArray($room_id) {
        $Redis_Model       = Redis_Model::getModelObject();
        $replyArr          = array("[roomid]" => $room_id);
        $AccountStatus_Key = strtr(Redis_Const::AccountStatus_Key, $replyArr);

        $result = $Redis_Model->hgetallField($AccountStatus_Key);
        if (is_array($result)) {
            return $result;
        } else {
            return array();
        }
    }


    //获取用户是否已经摊牌
    protected function queryShowCard($room_id, $account_id) {

        $Redis_Model  = Redis_Model::getModelObject();
        $replyArr     = array("[roomid]" => $room_id);
        $ShowCard_Key = strtr(Redis_Const::ShowCard_Key, $replyArr);
        $result       = $Redis_Model->hgetField($ShowCard_Key, $account_id);
        return $result == 1 ? 1 : 0;
    }

    //获取用户是否已经扣除房卡
    protected function queryTicketChecked($room_id, $account_id) {

        $Redis_Model       = Redis_Model::getModelObject();
        $replyArr          = array("[roomid]" => $room_id);
        $TicketChecked_Key = strtr(Redis_Const::TicketChecked_Key, $replyArr);
        $result            = $Redis_Model->hgetField($TicketChecked_Key, $account_id);
        return $result >= 1 ? 1 : 0;
    }

    //获取用户积分
    protected function queryAccountScore($room_id, $account_id) {

        $Redis_Model   = Redis_Model::getModelObject();
        $replyArr      = array("[roomid]" => $room_id);
        $RoomScore_Key = strtr(Redis_Const::RoomScore_Key, $replyArr);

        $result = $Redis_Model->hgetField($RoomScore_Key, $account_id);
        return $result;
    }

    //设置用户积分
    protected function updateAccountScore($room_id, $account_id, $score) {
        $Redis_Model   = Redis_Model::getModelObject();
        $replyArr      = array("[roomid]" => $room_id);
        $RoomScore_Key = strtr(Redis_Const::RoomScore_Key, $replyArr);

        $parameter_ary[$account_id] = $score;
        $mset_result                = $Redis_Model->hmsetField($RoomScore_Key, $parameter_ary);
        return TRUE;
    }

    //下注平衡积分
    protected function balanceScore($room_id, $account_id, $score) {

        $Redis_Model   = Redis_Model::getModelObject();
        $replyArr      = array("[roomid]" => $room_id);
        $RoomScore_Key = strtr(Redis_Const::RoomScore_Key, $replyArr);
        $Play_Key      = strtr(Redis_Const::Play_Key, $replyArr);
        $Chip_Key      = strtr(Redis_Const::Chip_Key, $replyArr);

        $mset_result = $Redis_Model->hincrbyField($RoomScore_Key, $account_id, 0 - $score);                 //总分 减少
        $mset_result = $Redis_Model->hincrbyField($Play_Key, Redis_Const::Play_Field_PoolScore, $score);    //分数池 增加
        $mset_result = $Redis_Model->hincrbyField($Chip_Key, $account_id, $score);
    }

    //获胜平衡积分
    protected function balanceWinnerScore($room_id, $account_id) {

        $winners = array();
        if (!is_array($account_id)) {
            $winners = [$account_id];
        } else {
            $winners = $account_id;
        }

        $count = count($winners);

        $Redis_Model   = Redis_Model::getModelObject();
        $replyArr      = array("[roomid]" => $room_id);
        $RoomScore_Key = strtr(Redis_Const::RoomScore_Key, $replyArr);
        $Play_Key      = strtr(Redis_Const::Play_Key, $replyArr);
        $Chip_Key      = strtr(Redis_Const::Chip_Key, $replyArr);

        $pool_score = $Redis_Model->hgetField($Play_Key, Redis_Const::Play_Field_PoolScore);
        $per_score  = $pool_score / $count;

        foreach ($winners as $winner_id) {
            $mset_result = $Redis_Model->hincrbyField($RoomScore_Key, $winner_id, $per_score);                  //个人总分 增加
        }
        $mset_result = $Redis_Model->hmsetField($Play_Key, array(Redis_Const::Play_Field_PoolScore => 0));    //分数池 清零
        //$Redis_Model->deleteKey($Chip_Key);

        return $per_score;
    }


    protected function queryCardInfo($room_id, $player_id) {
        $Redis_Model = Redis_Model::getModelObject();
        $replyArr    = array("[roomid]" => $room_id);
        $Card_Key    = strtr(Redis_Const::Card_Key, $replyArr);
        $card_str    = $Redis_Model->hgetField($Card_Key, $player_id);
        //echo "手牌:".$card_str.PHP_EOL;
        if (Redis_CONST::DATA_NONEXISTENT === $card_str || "" == trim($card_str)) {  //异常状态返回空数组

            $this->writeLog("function(queryCardInfo):1st 异常状态返回空数组 room_id:" . $room_id . ",player_id:" . $player_id . " in file" . __FILE__ . " on Line " . __LINE__);
            $card_str = $Redis_Model->hgetField($Card_Key, $player_id);
            if (Redis_CONST::DATA_NONEXISTENT === $card_str || "" == trim($card_str)) {  //异常状态返回空数组
                $this->writeLog("function(queryCardInfo):2nd 异常状态返回空数组 room_id:" . $room_id . ",player_id:" . $player_id . " in file" . __FILE__ . " on Line " . __LINE__);
                $cards    = array();
                $cards[0] = "-1";
                $cards[1] = "-1";
                $cards[2] = "-1";
                return $cards;
            }
        }
        $cards = explode(",", $card_str);

        //sort($cards);

        return $cards;
    }


    protected function queryAllCardInfo($room_id) {
        $Redis_Model = Redis_Model::getModelObject();
        $replyArr    = array("[roomid]" => $room_id);
        $Card_Key    = strtr(Redis_Const::Card_Key, $replyArr);

        $result = $Redis_Model->hgetallField($Card_Key);
        return $result;
    }


    //返回积分榜
    protected function queryScoreboard($room_id) {

        $Redis_Model = Redis_Model::getModelObject();
        $replyArr    = array("[roomid]" => $room_id);
        $Room_Key    = strtr(Redis_Const::Room_Key, $replyArr);

        $result = $Redis_Model->hgetField($Room_Key, Redis_Const::Room_Field_Scoreboard);
        if ($result !== Redis_Const::DATA_NONEXISTENT) {
            $arr = json_decode($result);
        } else {
            $arr = new stdClass();
        }
        return $arr;
    }


    //获取房间状态
    protected function queryRoomScoreboard($room_id) {

        $Redis_Model = Redis_Model::getModelObject();
        $replyArr    = array("[roomid]" => $room_id);
        $Room_Key    = strtr(Redis_Const::Room_Key, $replyArr);

        $result = $Redis_Model->hgetField($Room_Key, Redis_Const::Room_Field_Scoreboard);
        return $result;
    }


    //获取叫分基准
    protected function queryBenchmark($room_id) {
        $Redis_Model = Redis_Model::getModelObject();
        $replyArr    = array("[roomid]" => $room_id);
        $Play_Key    = strtr(Redis_Const::Play_Key, $replyArr);
        $result      = $Redis_Model->hgetField($Play_Key, Redis_Const::Play_Field_Benchmark);
        return $result;
    }

    //设置叫分基准
    protected function updateBenchmark($room_id, $benchmark) {
        if ($benchmark < 0) {
            return FALSE;
        }

        $Redis_Model = Redis_Model::getModelObject();
        $replyArr    = array("[roomid]" => $room_id);
        $Play_Key    = strtr(Redis_Const::Play_Key, $replyArr);

        $mkv[Redis_Const::Play_Field_Benchmark] = $benchmark;
        $mset_result                            = $Redis_Model->hmsetField($Play_Key, $mkv);
    }

    //获取底分
    protected function queryBaseScore($room_id) {
        $Redis_Model = Redis_Model::getModelObject();
        $replyArr    = array("[roomid]" => $room_id);
        $Room_Key    = strtr(Redis_Const::Room_Key, $replyArr);
        $result      = $Redis_Model->hgetField($Room_Key, Redis_Const::Room_Field_BaseScore);
        return $result;
    }

    //设置底分
    protected function updateBaseScore($room_id, $score) {
        if ($score < 0) {
            return FALSE;
        }
        $Redis_Model = Redis_Model::getModelObject();
        $replyArr    = array("[roomid]" => $room_id);
        $Room_Key    = strtr(Redis_Const::Room_Key, $replyArr);

        $mkv[Redis_Const::Room_Field_BaseScore] = $score;
        $mset_result                            = $Redis_Model->hmsetField($Room_Key, $mkv);
    }


    //获取庄家account_id
    protected function queryBanker($room_id) {
        $Redis_Model = Redis_Model::getModelObject();
        $replyArr    = array("[roomid]" => $room_id);
        $Play_Key    = strtr(Redis_Const::Play_Key, $replyArr);
        $result      = $Redis_Model->hgetField($Play_Key, Redis_Const::Play_Field_Banker);
        return $result;
    }

    //设置庄家account_id
    protected function updateBanker($room_id, $account_id) {
        $Redis_Model = Redis_Model::getModelObject();
        $replyArr    = array("[roomid]" => $room_id);
        $Play_Key    = strtr(Redis_Const::Play_Key, $replyArr);

        $parameter_ary[Redis_CONST::Play_Field_Banker] = $account_id;
        $mset_result                                   = $Redis_Model->hmsetField($Play_Key, $parameter_ary);
        return TRUE;
    }

    //获取回合
    protected function queryCircle($room_id) {
        $Redis_Model = Redis_Model::getModelObject();
        $replyArr    = array("[roomid]" => $room_id);
        $Play_Key    = strtr(Redis_Const::Play_Key, $replyArr);
        $result      = $Redis_Model->hgetField($Play_Key, Redis_Const::Play_Field_Circle);
        return $result;
    }

    //设置回合
    protected function updateCircle($room_id, $circle) {
        $Redis_Model = Redis_Model::getModelObject();
        $replyArr    = array("[roomid]" => $room_id);
        $Play_Key    = strtr(Redis_Const::Play_Key, $replyArr);

        $parameter_ary[Redis_CONST::Play_Field_Circle] = $circle;
        $mset_result                                   = $Redis_Model->hmsetField($Play_Key, $parameter_ary);
        return TRUE;
    }

    /**
     * 函数描述：获取当前环境之前对局的每一局结果
     * @param $room_id
     * @return array
     * author 黄欣仕
     * date 2019/1/12
     */
    protected function queryRoomGameEndData($room_id) {
        $MMYSQL     = $this->initMysql();
        $result     = array();
        $game_sql   = 'select game_result from ' . Room_GameResult . ' where room_id = ' . $room_id;
        $game_query = $MMYSQL->query($game_sql);
        if (!is_array($game_query)) {
            return $result;
        }

        foreach ($game_query as $result_str) {
            $game_result = json_decode($result_str["game_result"], TRUE);
            $game_data   = $game_result["gData"];
            $player_data = $game_result["pAry"];
	    $return_player_array = array();

            foreach ($player_data as $player) {
                if (!($player['c'] == "" && $player['m'] == -1)) {
                    $return_player_array[] = array(
                        "name"       => $player['n'],
                        "account_id" => $player['p'],
                        "card_type"  => $player['ct'],
                        "card_text"  => $player['card_text'],
                        "card_times" => $player['card_times'],
                        "card"       => $player['c'],
                        "score"      => $player['s'],
                        "mutiple"    => $player['p'] == $game_data['bid'] ? $game_data['bmult'] : $player['m'],
                        "is_banker"  => $player['p'] == $game_data['bid'] ? 1 : 0,
                    );
                }
            }

            $result[] = array(
                "players"   => $return_player_array,
                "total_num" => $game_data['tnum'],
                "game_num"  => $game_data['gnum'],
            );
        }

        return $result;
    }

    //获取游戏中的人数
    protected function queryPlayMemberCount($room_id) {
        $Redis_Model    = Redis_Model::getModelObject();
        $replyArr       = array("[roomid]" => $room_id);
        $PlayMember_Key = strtr(Redis_Const::PlayMember_Key, $replyArr);
        $result         = $Redis_Model->llenList($PlayMember_Key);
        return $result;
    }

    //获取游戏中的用户
    protected function queryPlayMember($room_id) {
        $Redis_Model    = Redis_Model::getModelObject();
        $replyArr       = array("[roomid]" => $room_id);
        $PlayMember_Key = strtr(Redis_Const::PlayMember_Key, $replyArr);
        $players        = $Redis_Model->lrangeList($PlayMember_Key);
        return $players;
    }

    //获取房间玩法设置
    protected function queryRoomSetting($room_id) {
        $Redis_Model = Redis_Model::getModelObject();
        $replyArr    = array("[roomid]" => $room_id);
        $Room_Key    = strtr(Redis_Const::Room_Key, $replyArr);
        $result      = $Redis_Model->hgetallField($Room_Key);
        return $result;
    }

    //从游戏中队列踢出
    protected function removeFromPlayMember($room_id, $account_id) {
        $Redis_Model    = Redis_Model::getModelObject();
        $replyArr       = array("[roomid]" => $room_id);
        $PlayMember_Key = strtr(Redis_Const::PlayMember_Key, $replyArr);

        $Redis_Model->lremList($PlayMember_Key, 0, $account_id);

        $list = $Redis_Model->lrangeList($PlayMember_Key, -1, -1);  //队列最右边是当前操作用户
        if ($list) {
            $active_id = $list[0];
        } else {
            $active_id = -1;
        }
        $Room_Key                                = strtr(Redis_Const::Room_Key, $replyArr);
        $mkv[Redis_Const::Room_Field_ActiveUser] = $active_id;
        //echo "轮流到:".$active_id.PHP_EOL;
        $this->logMessage('error', "function(removeFromPlayMember):轮流到:" . $active_id . " in file" . __FILE__ . " on Line " . __LINE__);
        $mset_result = $Redis_Model->hmsetField($Room_Key, $mkv);

        return $active_id;
    }

    //获取用户叫分倍数
    protected function queryPlayerMultiples($room_id, $account_id) {

        $Redis_Model   = Redis_Model::getModelObject();
        $replyArr      = array("[roomid]" => $room_id);
        $Multiples_Key = strtr(Redis_Const::Multiples_Key, $replyArr);
        $multiples     = $Redis_Model->hgetField($Multiples_Key, $account_id);
        return $multiples;
    }

    //获取用户叫分倍数
    protected function queryPlayerMultiplesArray($room_id) {

        $Redis_Model   = Redis_Model::getModelObject();
        $replyArr      = array("[roomid]" => $room_id);
        $Multiples_Key = strtr(Redis_Const::Multiples_Key, $replyArr);
        $result        = $Redis_Model->hgetallField($Multiples_Key);
        if (is_array($result)) {
            return $result;
        } else {
            return array();
        }
    }

    //设置用户叫分倍数
    protected function updatePlayerMultiples($room_id, $account_id, $multiples) {

        $Redis_Model      = Redis_Model::getModelObject();
        $replyArr         = array("[roomid]" => $room_id);
        $Multiples_Key    = strtr(Redis_Const::Multiples_Key, $replyArr);
        $mkv[$account_id] = $multiples;
        $Redis_Model->hmsetField($Multiples_Key, $mkv);
	$Redis_Model->expireKey($Multiples_Key, G_CONST::REDIS_EXPIRE_SECOND);
        return TRUE;
    }


    //是否经过了第一轮下注，经过了才能比牌
    protected function hasFirstBet($room_id, $account_id) {

        $Redis_Model = Redis_Model::getModelObject();
        $replyArr    = array("[roomid]" => $room_id);
        $Chip_Key    = strtr(Redis_Const::Chip_Key, $replyArr);
        $betScore    = $Redis_Model->hgetField($Chip_Key, $account_id);
        return $betScore > Game::Default_Score ? 1 : 0;
    }

    //获取用户叫分倍数
    protected function queryGrabMultiples($room_id, $account_id) {

        $Redis_Model = Redis_Model::getModelObject();
        $replyArr    = array("[roomid]" => $room_id);
        $Grab_Key    = strtr(Redis_Const::Grab_Key, $replyArr);
        $multiples   = $Redis_Model->hgetField($Grab_Key, $account_id);
        return $multiples;
    }

    //设置用户叫分倍数
    protected function updateGrabMultiples($room_id, $account_id, $multiples) {

        $Redis_Model      = Redis_Model::getModelObject();
        $replyArr         = array("[roomid]" => $room_id);
        $Grab_Key         = strtr(Redis_Const::Grab_Key, $replyArr);
        $mkv[$account_id] = $multiples;
        $Redis_Model->hmsetField($Grab_Key, $mkv);
	$Redis_Model->expireKey($Grab_Key, G_CONST::REDIS_EXPIRE_SECOND);
        return TRUE;
    }

    //获取庄家叫庄倍数
    protected function queryBankerMultiples($room_id) {

        $Redis_Model = Redis_Model::getModelObject();
        $replyArr    = array("[roomid]" => $room_id);
        $Play_Key    = strtr(Redis_Const::Play_Key, $replyArr);
        $result      = $Redis_Model->hgetField($Play_Key, Redis_Const::Play_Field_BankerMult);
        return $result > 0 ? $result : 1;
    }

    //设置用户叫分倍数
    protected function updateBankerMultiples($room_id, $multiples) {

        $Redis_Model = Redis_Model::getModelObject();
        $replyArr    = array("[roomid]" => $room_id);
        $Play_Key    = strtr(Redis_Const::Play_Key, $replyArr);

        $parameter_ary[Redis_CONST::Play_Field_BankerMult] = $multiples;
        $mset_result                                       = $Redis_Model->hmsetField($Play_Key, $parameter_ary);
        return TRUE;
    }


    //获取用户叫分倍数
    protected function queryBankerScore($room_id) {
        $Redis_Model = Redis_Model::getModelObject();
        $replyArr    = array("[roomid]" => $room_id);
        $Room_Key    = strtr(Redis_Const::Room_Key, $replyArr);
        $result      = $Redis_Model->hgetField($Room_Key, Redis_Const::Room_Field_BankerScore);
        return $result;
    }

    //设置用户叫分倍数
    protected function updateBankerScore($room_id, $score) {
        if ($score < 0) {
            return FALSE;
        }
        $Redis_Model = Redis_Model::getModelObject();
        $replyArr    = array("[roomid]" => $room_id);
        $Room_Key    = strtr(Redis_Const::Room_Key, $replyArr);

        $mkv[Redis_Const::Room_Field_BankerScore] = $score;
        $mset_result                              = $Redis_Model->hmsetField($Room_Key, $mkv);
        return TRUE;
    }

    //获取用户叫分倍数
    protected function queryBankerScoreType($room_id) {
        $Redis_Model = Redis_Model::getModelObject();
        $replyArr    = array("[roomid]" => $room_id);
        $Room_Key    = strtr(Redis_Const::Room_Key, $replyArr);
        $result      = $Redis_Model->hgetField($Room_Key, Redis_Const::Room_Field_BankerScoreType);
        return $result;
    }




    /***********************************
     * commom_function
     ***********************************/

    //推消息
    protected function pushMessageToGroup($room_id, $msg_arr, $exclude_client_id = NULL) {
        $msg = $this->_JSON($msg_arr);
        Gateway::sendToGroup($room_id, $msg, $exclude_client_id);
    }

    protected function pushMessageToAccount($account_id, $msg_arr) {
        $msg = $this->_JSON($msg_arr);
        Gateway::sendToUid($account_id, $msg);
    }

    protected function pushMessageToCurrentClient($msg_arr) {
        $msg = $this->_JSON($msg_arr);
        Gateway::sendToCurrentClient($msg);
    }

    //扣除房卡或退还房卡
    protected function balanceTicket($room_id, $account_id, $spend_ticket_count) {
        $timestamp = time();
        $MMYSQL    = $this->initMysql();

        if ($spend_ticket_count > 0) {
            $MMYSQL->update(Room_Ticket)->set("ticket_count", "ticket_count-" . $spend_ticket_count)->where("account_id=" . $account_id)->query();
        } else {
            $MMYSQL->update(Room_Ticket)->set("ticket_count", "ticket_count+" . abs($spend_ticket_count))->where("account_id=" . $account_id)->query();
        }


        //获取流水账
        $balance       = 0;
        $balance_where = 'account_id=' . $account_id . ' and is_delete=0';
        $balance_sql   = 'select balance from ' . Room_Ticket_Journal . ' where ' . $balance_where . ' order by journal_id desc';

        $balance_query = $MMYSQL->query($balance_sql);
        if (is_array($balance_query) && count($balance_query) > 0) {
            $balance = $balance_query[0]['balance'];
        }

        //添加到流水账
        $journal_array['create_time']  = $timestamp;
        $journal_array['create_appid'] = "aid_" . $account_id;
        $journal_array['update_time']  = $timestamp;
        $journal_array['update_appid'] = "aid_" . $account_id;
        $journal_array['is_delete']    = G_CONST::IS_FALSE;
        $journal_array['account_id']   = $account_id;
        $journal_array['object_id']    = $room_id;
        $journal_array['object_type']  = 3;  //游戏

        $journal_array['extra']    = "";
        $journal_array['abstract'] = "6人三公";        //摘要

        if ($spend_ticket_count > 0) {
            $journal_array['disburse'] = abs($spend_ticket_count);
        } else {
            $journal_array['income'] = abs($spend_ticket_count);
        }
        $journal_array['balance'] = $balance - $spend_ticket_count;
        if ($journal_array['balance'] < 0) {
            $journal_array['balance'] = 0;
        }
        $journal_id = $MMYSQL->insertReturnID(Room_Ticket_Journal, $journal_array);
        return;
    }


    protected function getGameAnnouncement($account_id) {
        //var_dump($account_id);
        $timestamp = time();

        $MMYSQL             = $this->initMysql();
        $game_type          = Game::Game_Type;
        $announcement_where = 'game_type=' . $game_type . ' and announce_time<=' . $timestamp . ' and end_time>' . $timestamp . ' and is_delete=0';
        $announcement_sql   = 'select announce_time,service_time,end_time,announce_text,service_text from ' . Game_Announcement . ' where ' . $announcement_where;
        $announcement_query = $MMYSQL->query($announcement_sql);
        if (is_array($announcement_query) && count($announcement_query) > 0) {
            $announce_time = $announcement_query[0]['announce_time'];
            $service_time  = $announcement_query[0]['service_time'];
            $end_time      = $announcement_query[0]['end_time'];
            $announce_text = $announcement_query[0]['announce_text'];
            $service_text  = $announcement_query[0]['service_text'];

            if ($timestamp >= $service_time) {
                $array['alert_type'] = 4;
                $array['alert_text'] = $service_text;
            } else {
                $array['alert_type'] = 4;
                $array['alert_text'] = $announce_text;
            }

            //获取白名单用户
            $whilelist_sql   = 'select data_id from ' . Game_Whilelist . ' where account_id=' . $account_id . ' and is_delete=0';
            $whilelist_query = $MMYSQL->query($whilelist_sql);
            if (is_array($whilelist_query) && count($whilelist_query) > 0) {
                $this->logMessage('error', "function(getGameAnnouncement):白名单用户:" . $account_id . " in file" . __FILE__ . " on Line " . __LINE__);
                return TRUE;
            } else {
                return $array;
            }
        }

        return TRUE;
    }


    protected function setHashTransaction($room_id) {
        $Redis_Model = Redis_Model::getModelObject();
        $replyArr    = array("[roomid]" => $room_id);
        $key         = strtr(Redis_Const::Room_Key, $replyArr);

        $redisAuth = $Redis_Model->pingRedisAuth();
        if ($redisAuth === G_CONST::EMPTY_STRING || !is_object($redisAuth)) {
            $this->logMessage('error', "function(setHashTransaction):redisAuth is empty string" . " in file" . __FILE__ . " on Line " . __LINE__);
            return FALSE;
        }

        $success = FALSE;
        $options = array(
            'cas'   => TRUE,    // Initialize with support for CAS operations
            'watch' => $key,    // Key that needs to be WATCHed to detect changes
            'retry' => 3,       // Number of retries on aborted transactions, after
            // which the client bails out with an exception.
        );

        $redisAuth->transaction($options, function ($tx) use ($key, &$success) {
            $room_status = $tx->hget($key, Redis_Const::Room_Field_Status);
            if (isset($room_status) && $room_status == 1) {
                $tx->multi();   // With CAS, MULTI *must* be explicitly invoked.
                $tx->hmset($key, array(Redis_Const::Room_Field_Status => 2));
                $success = TRUE;

            } else {
                //echo "room_status != 1".PHP_EOL;
                $this->logMessage('error', "function(setHashTransaction):room_status error " . " in file" . __FILE__ . " on Line " . __LINE__);
                $success = FALSE;
            }
        });
        return $success;
    }

    //设置游戏圈数
    protected function setCircleTransaction($room_id, $check_circle, $update_circle) {

        $Redis_Model = Redis_Model::getModelObject();
        $replyArr    = array("[roomid]" => $room_id);
        $key         = strtr(Redis_Const::Play_Key, $replyArr);

        $redisAuth = $Redis_Model->pingRedisAuth();
        if ($redisAuth === G_CONST::EMPTY_STRING || !is_object($redisAuth)) {
            $this->logMessage('error', "function(setCircleTransaction):redisAuth is empty string" . " in file" . __FILE__ . " on Line " . __LINE__);
            return FALSE;
        }

        $success = FALSE;
        $options = array(
            'cas'   => TRUE,    // Initialize with support for CAS operations
            'watch' => $key,    // Key that needs to be WATCHed to detect changes
            'retry' => 3,       // Number of retries on aborted transactions, after
            // which the client bails out with an exception.
        );

        $redisAuth->transaction($options, function ($tx) use ($key, $check_circle, $update_circle, &$success) {
            $paly_circle = $tx->hget($key, Redis_CONST::Play_Field_Circle);
            if (isset($paly_circle) && $paly_circle == $check_circle) {
                $tx->multi();   // With CAS, MULTI *must* be explicitly invoked.
                $tx->hmset($key, array(Redis_Const::Play_Field_Circle => $update_circle));
                $success = TRUE;

            } else {
                //echo "paly_circle != ".$check_circle.PHP_EOL;
                $this->logMessage('error', "function(setCircleTransaction):play_circle error " . $check_circle . " in file" . __FILE__ . " on Line " . __LINE__);
                $success = FALSE;
            }
        });
        return $success;
    }


    /*
		数组转JSON格式
	*/
    protected function _JSON($array) {
        $this->__arrayRecursive($array, 'urlencode', TRUE);
        $json = json_encode($array);
        return urldecode($json);
    }

    private function __arrayRecursive(&$array, $function, $apply_to_keys_also = FALSE) {
        static $recursive_counter = 0;
        if (++$recursive_counter > 1000) {
            die('possible deep recursion attack');
        }
        foreach ($array as $key => $value) {
            if (is_array($value)) {
                $this->__arrayRecursive($array[$key], $function, $apply_to_keys_also);
            } else if (is_object($value)) {
                $array[$key] = $value;
            } else {
                $array[$key] = $function($value);
            }

            if ($apply_to_keys_also && is_string($key)) {
                $new_key = $function($key);
                if ($new_key != $key) {
                    $array[$new_key] = $array[$key];
                    unset($array[$key]);
                }
            }
        }
        $recursive_counter--;
    }

    /**
     * 拆解接收的json字符串
     * @param string $splitJsonString json字符串
     */
    protected function _splitJsonString($jsonString) {
        if (empty($jsonString)) {
            return OPT_CONST::JSON_FALSE;
        }
        //判断是否为JSON格式
        if (is_null(json_decode($jsonString))) {
            //不是json格式
            return OPT_CONST::JSON_FALSE;
        } else {
            //分拆JSON字符串
            return json_decode($jsonString, TRUE);
        }
    }

    /*
		返回缺参结果
    */
    protected function _missingPrameterArr($operation, $prameter) {
        return array('result' => OPT_CONST::MISSING_PARAMETER, 'operation' => $operation, 'data' => array("missing_parameter" => $prameter), 'result_message' => "缺少参数");
    }


    /*
		判断数据格式是否正确
    */
    protected function _checkRequestFormat($requestAry) {
        if (!isset($requestAry['msgType'])) {
            $this->logMessage('error', "function(checkRequestFormat):lack of msgType" . " in file" . __FILE__ . " on Line " . __LINE__);
            return array('result' => -20, 'operation' => "", 'data' => array("missing_parameter" => "msgType"), 'result_message' => "缺少参数");
        }
        if (!isset($requestAry['content'])) {
            $this->logMessage('error', "function(checkRequestFormat):lack of content" . " in file" . __FILE__ . " on Line " . __LINE__);
            return array('result' => -20, 'operation' => "", 'data' => array("missing_parameter" => "content"), 'result_message' => "缺少参数");
        }

        return OPT_CONST::POSTARRAY_TRUE;
    }

    /*
		判断数据格式是否正确
    */
    protected function _checkPostArray($postArr) {

        if (!isset($postArr['operation'])) {
            $this->logMessage('error', "function(checkPostArray):lack of operation" . " in file" . __FILE__ . " on Line " . __LINE__);
            return array('result' => -20, 'operation' => "", 'data' => array(), 'result_message' => "缺少参数");
        } else $operation = $postArr['operation'];
        if (!isset($postArr['data'])) {
            $this->logMessage('error', "function(checkPostArray):lack of data" . " in file" . __FILE__ . " on Line " . __LINE__);
            return array('result' => -20, 'operation' => $operation, 'data' => array(), 'result_message' => "缺少参数");
        }
        if (!isset($postArr['account_id'])) {
            $this->logMessage('error', "function(checkPostArray):lack of account_id" . " in file" . __FILE__ . " on Line " . __LINE__);
            return array('result' => -20, 'operation' => $operation, 'data' => array(), 'result_message' => "缺少参数");
        }
        if (!isset($postArr['session'])) {
            $this->logMessage('error', "function(checkPostArray):lack of session" . " in file" . __FILE__ . " on Line " . __LINE__);
            return array('result' => -20, 'operation' => $operation, 'data' => array(), 'result_message' => "缺少参数");
        }
        return OPT_CONST::POSTARRAY_TRUE;
    }


    /*
		生成微秒
    */
    protected function getMicroTimestamp() {
        $mtime      = explode(' ', microtime());
        $mTimestamp = $mtime[1] . substr($mtime[0], 2, 3);

        return $mTimestamp;
    }


}

?>