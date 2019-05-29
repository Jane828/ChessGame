<?php


use \GatewayWorker\Lib\Gateway;

include_once(dirname(__DIR__) . '/Module/Verification.class.php');
include_once(dirname(__DIR__) . '/Module/Socket.class.php');
include_once(dirname(__DIR__) . '/Module/Redis.class.php');
require_once dirname(__DIR__) . '/base.class.model.php';

class Public_Model extends Base_Model {

    //扣除房卡或退还房卡
    protected function balanceTicket($room_id, $account_id, $spend_ticket_count) {
        $timestamp = time();
        $MMYSQL    = $this->initMysql();
        $MMYSQL->update(Room_Ticket)->set("ticket_count", "ticket_count-" . $spend_ticket_count)->where("account_id=" . $account_id)->query();

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

        $journal_array['extra'] = "";

        if ($spend_ticket_count > 0) {
            $journal_array['disburse'] = $spend_ticket_count;
            $journal_array['abstract'] = "暗宝";        //摘要
        } else {
            $journal_array['income']   = -$spend_ticket_count;
            $journal_array['abstract'] = "暗宝房卡退还";        //摘要
        }

        $journal_array['balance'] = $balance - $spend_ticket_count;
        if ($journal_array['balance'] < 0) {
            $this->writeLog("balance negative balance: " . $balance . " account_id: " . $account_id . " room_id: " . $room_id);
            $journal_array['balance'] = 0;
        }
        $journal_id = $MMYSQL->insertReturnID(Room_Ticket_Journal, $journal_array);
    }


    //获取房间玩法设置
    protected function queryRoomSetting($room_id) {
        $Redis_Model = Redis_Model::getModelObject();
        $replyArr    = array("[roomid]" => $room_id);
        $Room_Key    = strtr(Redis_Const::Room_Key, $replyArr);
        $result      = $Redis_Model->hgetallField($Room_Key);
        return $result;
    }

    /**
     * 函数描述：获取当前环境之前对局的每一局结果
     * @param $room_id
     * @return array
     * author
     * date 2019/1/12
     */
    protected function queryRoomGameEndData($room_id, $game_type) {
        $MMYSQL     = $this->initMysql();
        $result     = array();
        $game_sql   = 'select game_result from ' . Room_GameResult . ' where room_id = ' . $room_id . ' and game_type=' . $game_type;
        $game_query = $MMYSQL->query($game_sql);
        if (!is_array($game_query)) {
            return $result;
        }

        foreach ($game_query as $result_str) {
            $game_result         = json_decode($result_str["game_result"], TRUE);
            $game_data           = $game_result["gData"];
            $player_data         = $game_result["pAry"];
            $prize               = $game_result["prize"];
            $return_player_array = array();

            foreach ($player_data as $player) {
                $return_player_array[] = array(
                    "name" => $player['n'],
                    "account_id" => $player['p'],
                    "chips" => $player['a'],
                    "chip" => $player['c'],
                    "score" => $player['s'],
                );
            }

            $result[] = array(
                "players" => $return_player_array,
                "total_num" => $game_data['tnum'],
                "game_num" => $game_data['gnum'],
                "prize" => $prize,
            );
        }

        return $result;
    }

    protected function setHashTransaction($room_id) {
        $Redis_Model = Redis_Model::getModelObject();
        $replyArr    = array("[roomid]" => $room_id);
        $key         = strtr(Redis_Const::Room_Key, $replyArr);

        $redisAuth = $Redis_Model->pingRedisAuth();
        if ($redisAuth === G_CONST::EMPTY_STRING || !is_object($redisAuth)) {
            $this->logMessage('error', "function(existsKey):redisAuth is empty string" . " in file" . __FILE__ . " on Line " . __LINE__);
            return FALSE;
        }

        $success = FALSE;
        $options = array(
            'cas' => TRUE,    // Initialize with support for CAS operations
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
                $this->writeLog("room_status != 1");
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
            'cas' => TRUE,    // Initialize with support for CAS operations
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
                $this->logMessage('error', "function(setCircleTransaction):play_circle error "
                                         . $check_circle . " paly_circle：" . $paly_circle . "in file" . __FILE__ . " on Line " . __LINE__);
                $success = FALSE;
            }
        });
        return $success;
    }


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

    //判断是否抢庄
    protected function needGrab($banker_mode, $game_num) {
        if (($banker_mode == Game::BankerMode_FixedBanker && $game_num >= 1) || $banker_mode == Game::BankerMode_RoomownerGrab) {
            return FALSE;
        }

        return TRUE;
    }

    //开局
    protected function startGame($room_id, $passive_by_timer = FALSE) {
        $timestamp   = time();
        $Redis_Model = Redis_Model::getModelObject();
        $replyArr    = array("[roomid]" => $room_id);

        $Room_Key = strtr(Redis_Const::Room_Key, $replyArr);
        $Play_Key = strtr(Redis_Const::Play_Key, $replyArr);

        //开始准备倒计时和读取庄家模式
        $room_data          = $this->queryRoomData($room_id);
        $game_num           = $room_data['gnum'];
        $room_status        = $room_data['stat'];
        $banker_mode        = $room_data['bankermode'];
        $spend_ticket_count = $room_data['ticketcnt'];

        $this->logMessage('error',
                          "startGame--room_status " . $room_status .
                          "passive_by_timer" . "$passive_by_timer" . "game_num:" . $game_num);
        //如果房间状态正在游戏中，直接返回
        if ($room_status == Game::RoomStatus_Playing) {
            return FALSE;
        }

        //修复异常状态的用户
        $this->startGameFixAccountStatus($room_id);

        //如果开房当庄，更新庄家信息
        if ($game_num == 0 && $banker_mode == Game::BankerMode_RoomownerGrab) {
            $owner_id = $this->queryCreator($room_id);
            $this->updateCircle($room_id, -1);
            $this->updateBanker($room_id, $owner_id);
        }

        //房间庄家模式固定庄家和开房当庄，需要庄家在线并开始才能玩游戏
        $is_grap = $this->needGrab($banker_mode, $game_num);
        if (!$is_grap) {
            $banker_id            = $this->queryBanker($room_id);
            $banker_online_status = $this->queryOnlineStatus($room_id, $banker_id);
            $banker_status        = $this->queryAccountStatus($room_id, $banker_id);
            $this->writeLog("banker_status :$banker_status ");

            if ($banker_status != Game::AccountStatus_Ready || $banker_online_status == 0) {
                $this->writeLog("[$room_id] 庄家(" . $banker_id . ") 未准备 不能开局");
                return FALSE;
            }
        }

        $ready_user    = $this->queryReadyUser($room_id);
        $watch_user    = $this->queryWatchUser($room_id);
        $in_room_array = $this->queryInRoomUser($room_id);
        $ready_count   = count($ready_user);
        $online_count  = count($in_room_array);
        $watch_count   = count($watch_user);

        //准备人数 大于等于2 且 准备人数 等于 在线人数
        $this->writeLog("[$room_id] 准备:${ready_count}人   " . "在线:${online_count}人   " . "观战:${watch_count}人");

        if ($ready_count >= 2) {
            if ($passive_by_timer || ($ready_count + $watch_count) == $online_count) {

                $ready_in_room_user = array_intersect($ready_user, $in_room_array); //在房的已准备用户
                if (count($ready_in_room_user) < 2) {
                    $this->writeLog("在房" . $room_id . "的已准备用户数量:" . count($ready_in_room_user) . " 不能开局 in file" . __FILE__ . " on Line " . __LINE__);
                    return FALSE;
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
                $setting = $this->queryRoomSetting($room_id);

                //扣房卡 begin
                $room_members  = $this->queryRoomMembers($room_id);
                $readyUser_ary = array();
                foreach ($room_members as $account_id) {
                    if (in_array($account_id, $ready_in_room_user)) {
                        $readyUser_ary[] = $account_id;
                    }
                }

                $ticket_checked_user = $this->queryTicketCheckedUser($room_id);
                $to_check_user_array = array_diff($readyUser_ary, $ticket_checked_user);
                //目前只支持创建房间扣房卡，不支持AA
                if (count($to_check_user_array) > 0) {
                    foreach ($to_check_user_array as $account_id) {
                        $mkv[$account_id] = $spend_ticket_count;
                    }
                    //添加到用户
                    $TicketChecked_Key = strtr(Redis_Const::TicketChecked_Key, $replyArr);
                    $Redis_Model->hmsetField($TicketChecked_Key, $mkv);
                }
                //扣房卡 end

                $Chip_Key = strtr(Redis_Const::Chip_Key, $replyArr);

                //删除每局玩家总筹码hash
                $Redis_Model->deleteKey($Chip_Key);
                //删除每局玩家抢庄hash
                if ($banker_mode == Game::BankerMode_FreeGrab) {
                    $Grab_Key = strtr(Redis_Const::Grab_Key, $replyArr);
                    $Redis_Model->deleteKey($Grab_Key);
                }

                //设置每局玩家顺序list
                $play_member = $readyUser_ary;

                //删除每局玩家顺序list 再装入本局玩家
                $PlayMember_Key = strtr(Redis_Const::PlayMember_Key, $replyArr);
                $Redis_Model->deleteKey($PlayMember_Key);
                $push_result = $Redis_Model->pushList($is_rpush = 0, $is_pushx = 0, $PlayMember_Key, $play_member); //lpush

                //重设每局游戏参数 todo
                $this->initChipArea($room_id, $play_member);

                $Room_mkv[Redis_CONST::Room_Field_Status] = Game::RoomStatus_Playing;  //房间状态，1等待、2进行中、3关闭
                $banker_id                                = $this->queryBanker($room_id);
                if (!$is_grap) {
                    $Room_mkv[Redis_CONST::Room_Field_Banker] = $banker_id;
                }
                $mset_result = $Redis_Model->hmsetField($Room_Key, $Room_mkv);

                //房间轮数与局数更新
                $this->updateGameNumberRound($room_id);

                //设置用户状态
                if ($is_grap) {
                    $account_status = Game::AccountStatus_Choose;
                    $limit_Time     = $this->queryGameCountDownGrab($room_id);
                } else {
                    $account_status = Game::AccountStatus_Put;
                    $limit_Time     = 0;

                }

                $player_status = array();
                foreach ($play_member as $player_id) {
                    if ($player_id == $banker_id) {
                        $player_status[] = array(
                            "account_id" => $player_id,
                            "account_status" => $account_status,         //等待庄家放暗宝
                            "online_status" => $this->queryOnlineStatus($room_id, $player_id),
                            "playing_status" => Game::PlayingStatus_puting, // 放暗宝中
                            "limit_time" => $limit_Time
                        );
                    } else {
                        $player_status[] = array(
                            "account_id" => $player_id,
                            "account_status" => $account_status,         //
                            "online_status" => $this->queryOnlineStatus($room_id, $player_id),
                            "playing_status" => Game::PlayingStatus_Waiting, //等待别人中
                            "limit_time" => $limit_Time
                        );
                    }
                    //更新用户状态为抢庄或者等待放宝
                    $this->updateAccountStatus($room_id, $player_id, $account_status);
                    $this->writeLog("xxxxxx[$room_id]:$player_id  status : $account_status");
                }

                //推送开始
                $arr = array("result" => 0, "operation" => "GameStart", "data" => $player_status,
                    "result_message" => "游戏开始了", "limit_time" => $limit_Time, "game_num" => $game_num + 1);

                $this->pushMessageToGroup($room_id, $arr);

                if ($game_num == 0) {
                    //设置开局时间
                    $this->updateStartTime($room_id);
                }

                //设置自动不叫庄定时器
                if ($is_grap) {
                    //设置叫庄回合
                    $this->updateCircle($room_id, Game::Circle_Grab);
                    $this->setupGrabPassiveTimer($room_id);
                } else {
                    //抢庄结束设置
                    $this->grabPassiveOpt($room_id);
                }

            } else {
                if ($this->getTimerTime($room_id) == -1) {
                    $this->writeLog("[$room_id]" . "setupStartGamePassiveTimer");
                    $this->setupStartGamePassiveTimer($room_id);
                }
            }
        }

        return TRUE;
    }

    //抢庄回合结束，开启放宝回合
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
        //选择庄家，开始放宝回合
        $this->startPutRound($room_id);

        return TRUE;
    }


//获取游戏中的用户
    protected function queryPlayMember($room_id) {
        $Redis_Model    = Redis_Model::getModelObject();
        $replyArr       = array("[roomid]" => $room_id);
        $PlayMember_Key = strtr(Redis_Const::PlayMember_Key, $replyArr);
        $players        = $Redis_Model->lrangeList($PlayMember_Key);
        return $players;
    }


    /*
            选择庄家，用户抢庄
        */
    protected function startPutRoundGrab($room_id, $grap) {

        if ($grap == 1) {

            $grab_array = $this->queryGrabUser($room_id);
            $is_only    = G_CONST::IS_FALSE;
            if (count($grab_array) == 1) {
                $is_only = G_CONST::IS_TRUE;
            }

            //获取所有游戏用户
            $player_array = $this->queryPlayMember($room_id);

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
        } else {
            //获取所有游戏用户
            $player_array = $this->queryPlayMember($room_id);
            $banker_id    = $this->queryBanker($room_id);
            $grab_array   = array();
        }

        foreach ($player_array as $player_id) {
            //设置用户状态为放宝中
            $this->updateAccountStatusNotNoty($room_id, $player_id, Game::AccountStatus_Put);
        }

        //获取放宝倒计时
        $limit_time    = $this->queryGameCountDownPut($room_id);
        $player_status = array();
        foreach ($player_array as $player_id) {
            $is_banker = G_CONST::IS_FALSE;
            if ($player_id == $banker_id) {
                $is_banker = G_CONST::IS_TRUE;
            }

            $player_status[] = array(
                "account_id" => $player_id,
                "account_status" => Game::AccountStatus_Put,
                "online_status" => $this->queryOnlineStatus($room_id, $player_id),
                "limit_time" => $limit_time,
                "is_banker" => $is_banker
            );
        }
        //设置自动放宝定时器
        if (count($grab_array) == 1) {
            $is_only = G_CONST::IS_TRUE;
        }
        $this->setupPutPassiveTimer($room_id,$is_only);

        //通知用户放宝中
        $arr = array("result" => 0, "operation" => "StartPut", "data" => $player_status, "result_message" => "放宝开始了", "grab_array" => $grab_array, "limit_time" => $limit_time);
        $this->pushMessageToGroup($room_id, $arr);

        return TRUE;
    }


    /*
		选择庄家，推送放宝回合
    */
    protected function startPutRound($room_id) {

        //获取房间庄家模式
        $banker_mode = $this->queryBankerMode($room_id);
        $game_num    = $this->queryGameNumber($room_id);

        if ($banker_mode == Game::BankerMode_FreeGrab || ($banker_mode == Game::BankerMode_FixedBanker && $game_num == 1)) {
            $check_circle = Game::Circle_Grab;
        } else {
            $check_circle = -1;
        }

        $update_circle = Game::Circle_Put;
        $success       = $this->setCircleTransaction($room_id, $check_circle, $update_circle);
        if (!$success) {
            $this->logMessage('error', "function(startPutRound):并发忽略。room id:" . $room_id . " in file" . __FILE__ . " on Line " . __LINE__);
            return FALSE;
        }

        if ($banker_mode == Game::BankerMode_FreeGrab || ($banker_mode == Game::BankerMode_FixedBanker && $game_num == 1)) {
            //自由抢庄,固定庄家第一次需要抢庄
            return $this->startPutRoundGrab($room_id, 1);
        } else {
            //庄家已经确定，设置放宝倒计时
            //$this->setupPutPassiveTimer($room_id);
            $this->startPutRoundGrab($room_id, 0);
        }
    }


    //设置放宝定时器
    protected function setupPutPassiveTimer($room_id,$is_only) {
        $limit_time = $this->queryGameCountDownPut($room_id);

        $callback_array = array(
            'operation' => "PutPassive",
            'room_id' => $room_id,
            'data' => array()
        );
        if (!$is_only) $limit_time = $limit_time + 4 ; //for front-show grab movie
        $arr            = array(
            'operation' => "BuildTimer",
            'room_id' => $room_id,
            'data' => array(
                'limit_time' => $limit_time,
                'callback_array' => $callback_array
            )
        );
        $this->setTimerTime($room_id);    //分开、提前设置时间
        $this->sendToTimerServer($arr);

    }

    protected function putPassiveOpt($room_id, $area) {

        //获取所有游戏用户
        $player_array = $this->queryPlayMember($room_id);
        foreach ($player_array as $player_id) {
            //设置用户状态为放宝中
            $this->updateAccountStatusNotNoty($room_id, $player_id, Game::AccountStatus_Bet);
        }
        $this->updatePrize($room_id, $area);

        //获取放宝倒计时
        $limit_time    = $this->queryGameCountDownBet($room_id);
        $player_status = array();
        $banker_id     = $this->queryBanker($room_id);
        foreach ($player_array as $player_id) {
            $is_banker = G_CONST::IS_FALSE;
            if ($player_id == $banker_id) {
                $is_banker = G_CONST::IS_TRUE;
            }

            $player_status[] = array(
                "account_id" => $player_id,
                "account_status" => Game::AccountStatus_Bet,
                "online_status" => $this->queryOnlineStatus($room_id, $player_id),
                "limit_time" => $limit_time,
                "is_banker" => $is_banker
            );
        }
        //设置自动放宝定时器
        $this->setupBetPassiveTimer($room_id);
        $bet_array = $this->getChipArray($room_id);// 查询已经下注所有玩家的信息
        //通知用户放宝中
        $arr           = array("result" => 0, "operation" => "StartBet", "data" => $player_status, "result_message" => "下注开始了", "bet_array" => $bet_array, "limit_time" => $limit_time);
        $check_circle  = Game::Circle_Put;
        $update_circle = Game::Circle_Bet;
        $success       = $this->setCircleTransaction($room_id, $check_circle, $update_circle);
        if (!$success) {
            $this->logMessage('error', "function(putPassiveOpt):并发忽略。room id:" . $room_id . " in file" . __FILE__ . " on Line " . __LINE__);
            return FALSE;
        }
        $this->pushMessageToGroup($room_id, $arr);

        return TRUE;
    }

    //设置放宝定时器
    protected function setupBetPassiveTimer($room_id) {
        $limit_time     = $this->queryGameCountDownBet($room_id);
        $callback_array = array(
            'operation' => "BetPassive",
            'room_id' => $room_id,
            'data' => array()
        );
        $arr            = array(
            'operation' => "BuildTimer",
            'room_id' => $room_id,
            'data' => array(
                'limit_time' => $limit_time,
                'callback_array' => $callback_array
            )
        );
        $this->setTimerTime($room_id);    //分开、提前设置时间
        $this->sendToTimerServer($arr);
        $this->writeLog("setupBetPassiveTimer");

    }

    //下注时间结束自动进入开奖模式
    public function betPassiveOpt($room_id) {

        //是否开奖回合
        $check_circle  = Game::Circle_Bet;
        $update_circle = Game::Circle_Show;
        $success       = $this->setCircleTransaction($room_id, $check_circle, $update_circle);
        if (!$success) {
            $this->logMessage('error', "function(startShowRound):并发忽略。room id:" . $room_id . " in file" . __FILE__ . " on Line " . __LINE__);
            return FALSE;
        }

        //获取摊牌倒计时
        $limit_time    = $this->queryGameCountDownShow($room_id);
        $player_status = array();
        //获取所有游戏用户
        $player_array = $this->queryPlayMember($room_id);
        foreach ($player_array as $player_id) {
            //设置用户状态为未摊牌
            $this->updateAccountStatusNotNoty($room_id, $player_id, Game::AccountStatus_Notshow);

            //推送开奖结果
            $prize           = $this->queryPrize($room_id);
            $player_status[] = array(
                "account_id" => $player_id,
                "account_status" => Game::AccountStatus_Notshow,         //5; 初始是选择抢庄
                "online_status" => $this->queryOnlineStatus($room_id, $player_id),
                "limit_time" => $limit_time,
            );

            $this->writeLog("[$room_id] ($player_id) 开奖结果:" . $prize);
        }

        //设置自动摊牌定时器
        $this->setupShowPassiveTimer($room_id);

        //通知用户下注
        $arr = array("result" => 0, "operation" => "StartShow", "data" => $player_status, "prize" => $prize, "result_message" => "开奖开始了", "limit_time" => $limit_time);
        $this->pushMessageToGroup($room_id, $arr);

        return TRUE;
    }


    //设置 自动开奖 定时器
    protected function setupShowPassiveTimer($room_id) {
        $limit_time     = $this->queryGameCountDownShow($room_id);
        $callback_array = array(
            'operation' => "ShowPassive",
            'room_id' => $room_id,
            'data' => array()
        );

        $arr = array(
            'operation' => "BuildTimer",
            'room_id' => $room_id,
            'data' => array(
                'limit_time' => $limit_time + 1,
                'callback_array' => $callback_array
            )
        );
        $this->setTimerTime($room_id);
        $this->sendToTimerServer($arr);
        $this->writeLog("setupShowPassiveTimer");

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
        $Redis_Model   = Redis_Model::getModelObject();
        $replyArr      = array("[roomid]" => $room_id);
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
            $this->updateAccountStatusNotNoty($room_id, $player_id, Game::AccountStatus_Show);
            if ($player_id == $banker_id) {
                continue;
            }
            //比牌
            $compareResult = $this->comparePrize($room_id, $player_id);
            if ($compareResult > 0) {
                $winner_array[]    = array("account_id" => $player_id, "score" => $compareResult);    //闲家赢
                $banker_lose_score += $compareResult;
            } else {
                $loser_array[]    = array("account_id" => $player_id, "score" => $compareResult);    //庄家赢
                $banker_win_score += -$compareResult;
            }
            $banker_score += -$compareResult;
            //闲家
            $mset_result = $Redis_Model->hincrbyField($RoomScore_Key, $player_id, $compareResult);  //个人总分
        }
        //庄家输赢状态
        if ($banker_score >= 0) {
            $winner_array[] = array("account_id" => $banker_id, "score" => $banker_score);
        } else {
            $loser_array[] = array("account_id" => $banker_id, "score" => $banker_score);
        }
        //庄家
        $mset_result = $Redis_Model->hincrbyField($RoomScore_Key, $banker_id, $banker_score); //个人总分
        $this->logMessage('error', "function(startWinRound):banker mset_result:" . $mset_result . " in file" . __FILE__ . " on Line " . __LINE__);
        $prize = $this->queryPrize($room_id);
        //广播用户状态改变
        $msg_arr = array(
            "result" => "0",
            "operation" => "StopShow",
            'account_status' => Game::AccountStatus_Show,
            "prize" => $prize,
            "result_message" => "用户状态改变"
        );

        $this->pushMessageToGroup($room_id, $msg_arr, $exclude_client_id = NULL);

        //通知输赢结果

        $arrData['room_id']      = $room_id;
        $arrData['banker_id']    = $banker_id;
        $arrData['winner_array'] = $winner_array;
        $arrData['loser_array']  = $loser_array;
        $arrData['is_break']     = 0;
        $this->dealGameResult($arrData);

        return TRUE;
    }

    /*
           处理游戏结果，把本局的游戏关键数据保存在mysql数据库中，方便后续战绩和积分榜查询
       */
    protected function dealGameResult($arrData) {
        $room_id      = $arrData['room_id'];
        $banker_id    = $arrData['banker_id'];
        $winner_array = $arrData['winner_array'];
        $loser_array  = $arrData['loser_array'];
        $is_break     = $arrData['is_break'];

        $Redis_Model = Redis_Model::getModelObject();
        $replyArr    = array("[roomid]" => $room_id);
        $Room_Key    = strtr(Redis_Const::Room_Key, $replyArr);

        //获取积分榜
        $RoomScore_Key = strtr(Redis_Const::RoomScore_Key, $replyArr);
        $scoreboard    = $Redis_Model->hgetallField($RoomScore_Key);

        $board_json_str      = "";
        $ticket_checked_user = $this->queryTicketCheckedUser($room_id);

        foreach ($scoreboard as $key => $value) {
            if (!in_array($key, $ticket_checked_user)) {
                //未扣房卡的用户不显示在积分榜上
                unset($scoreboard[$key]);
            }
        }
        $save_scoreboard = $scoreboard;

        $room_data = $this->queryRoomData($room_id);
        $game_num  = $room_data['gnum'];
        $total_num = $room_data['totalnum'];
        //$banker_score = $room_data['bankerscore'];
        $start_time = $room_data[Redis_CONST::Room_Field_StartTime];
        $round      = $room_data['ground'];
        $game_type  = $room_data[Redis_CONST::Room_Field_GameType];

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

                    $name_board[] = array('name' => $name, 'score' => $score, 'account_id' => $account_id);

                    $account_array               = [];
                    $account_array['account_id'] = $account_id;
                    $account_array['room_id']    = $room_id;
                    $account_array['game_type']  = $game_type;
                    $account_array['score']      = $score;
                    $account_array['over_time']  = time();

                    $MMYSQL->insertReturnID(Room_Account, $account_array);
                }
                $balance_scoreboard = array('time' => time(), 'scoreboard' => $name_board, 'game_num' => $game_num);
            }
        }

        if ($game_num >= $total_num) {
            $is_break = 0;
        }
        $prize   = $this->queryPrize($room_id);
        $msg_arr = array(
            'result' => 0,
            'operation' => 'Win',
            'result_message' => "获胜+积分榜",
            'data' => array(
                'winner_array' => $winner_array,
                'loser_array' => $loser_array,
                'score_board' => $scoreboard,
                'game_num' => $game_num,
                'total_num' => $total_num,
                'balance_scoreboard' => $balance_scoreboard,
                'is_break' => $is_break,
                'banker_id' => $banker_id,
                'prize' => $prize,
            )
        );

        $this->pushMessageToGroup($room_id, $msg_arr);

        //保存当局游戏结果
        $game_info['room_id']   = $room_id;
        $game_info['game_type'] = $game_type;
        $game_info['round']     = $round;
        $game_info['game_num']  = $game_num;
        $game_info['total_num'] = $total_num;
        $game_info['banker_id'] = $game_bankerid;
        $game_info['prize']     = $prize;
        $game_info['extra']     = "";
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
            $board_array['game_type']     = $game_type;  //游戏类型
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

            $r_mkv[Redis_Const::Room_Field_ActiveTimer] = -1;        //当前生效timer
            $r_mkv[Redis_Const::Room_Field_StartTime]   = -1;        //房间倒计时
            $mset_result                                = $Redis_Model->hmsetField($Room_Key, $r_mkv);

            //删除一些变量 Todo ,每局游戏结束后，恢复无效状态
            $PlayMember_Key = strtr(Redis_Const::PlayMember_Key, $replyArr);
            $Play_Key       = strtr(Redis_Const::Play_Key, $replyArr);
            $chipArr        = strtr(Redis_Const::Chip_Array_Key, $replyArr);
            //$accountStatus = strtr(Redis_Const::AccountStatus_Key, $replyArr);

            //删除每局玩家顺序list
            $Redis_Model->deleteKey($PlayMember_Key);
            //清空每局中所有下注详细的信息
            $Redis_Model->deleteKey($chipArr);

            $banker_mode = $this->queryBankerMode($room_id);
            //重设每局游戏参数
            if ($banker_mode == Game::BankerMode_FreeGrab) {
                $this->writeLog("[$room_id] banker_mode:" . $banker_mode);

                $this->updateBanker($room_id, -1);
            }
            $parameter_ary[Redis_CONST::Play_Field_Circle] = -1;
            $mset_result                                   = $Redis_Model->hmsetField($Play_Key, $parameter_ary);

            return TRUE;
        }
    }

    //重置房间信息 todo
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
        $Room_Key   = strtr(Redis_CONST::Room_Key, $replyArr);
        $box_number = $Redis_Model->hgetField($Room_Key, Redis_CONST::Room_field_box);
        if ($box_number && $box_number != Game::ORDINARY_ROOM) {
            $replyArr["[boxnumber]"] = $box_number;
            //清除包厢空房记录
            $BoxEmptyRoom_Key = strtr(Redis_CONST::BoxEmptyRoom_Key, $replyArr);
            $srem_result      = $Redis_Model->sremSet($BoxEmptyRoom_Key, array("$room_id"));
            if ($srem_result == OPT_CONST::DATA_NONEXISTENT) {
                $this->logMessage('error', "function(initRoomData):delete BoxEmptyRoom_Key  box:[$box_number] rid:[$room_id] fail;" . " in file " . __FILE__ . " on Line " . __LINE__);
            }

            //清除玩家包厢房间游戏记录
            $zrange_array['is_zrange'] = TRUE;
            $zrange_array['key']       = $RoomSequence_Key;
            $zrange_array['start']     = 0;
            $zrange_array['stop']      = -1;
            $members                   = $Redis_Model->zrangeSet($zrange_array);
            if (is_array($members)) {
                foreach ($members as $key => $account_id) {
                    $BoxAccount_Key = strtr(Redis_Const::BoxAccount_Key, array("[accountid]" => $account_id));
                    $hdel_result    = $Redis_Model->hdelFiled($BoxAccount_Key, $box_number);
                    if (!$hdel_result) {
                        $this->logMessage('error', "function(initRoomData):delete BoxAccount_Key box:[$box_number] rid:[$room_id] aid:[$account_id] fail;" . " in file " . __FILE__ . " on Line " . __LINE__);
                    }
                }
            }
        }

        $Room_mkv[Redis_Const::Room_Field_GameNum]    = 0;                            //游戏局数
        $Room_mkv[Redis_Const::Room_Field_Status]     = Game::RoomStatus_Closed;      //房间状态，1等待、2进行中、3未激活
        $Room_mkv[Redis_Const::Room_Field_Scoreboard] = "";                           //积分榜清零
        $Room_mkv[Redis_Const::Room_Field_Creator]    = -1;                           //创建者清空

        $Room_mkv[Redis_Const::Room_Field_ActiveTimer] = -1;                            //当前生效timer
        $Room_mkv[Redis_Const::Room_Field_StartTime]   = -1;                              //房间倒计时


        $mset_result = $Redis_Model->hmsetField($Room_Key, $Room_mkv);

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
            $MMYSQL->update(Room)->set("is_close", "1")->set('is_delete', ($is_delete ? '1' : '0'))->set('room_config', NULL)->where("room_id=" . $room_id)->query();

            $Redis_Model->deleteKey($Room_Key);
        }

        return TRUE;
    }


    /**
     * 函数描述：计算每个玩家的分数
     * @param $room_id
     * @param $player_id
     * @return
     * author  lihuai
     * date 2019/3/2
     */
    protected function comparePrize($room_id, $player_id) {
        $room_data = $this->queryRoomData($room_id);
        $prize     = $this->queryPrize($room_id);

        //先获取中奖的分数
        if ($prize < Game::Rule_Exit) {
            $other_side = $prize + Game::Rule_Step;
        } else {
            $other_side = $prize - Game::Rule_Step;
        }
        $chiparray = array(Game::Rule_Enter, Game::Rule_Dragon, Game::Rule_Exit, Game::Rule_Tiger);
        $score     = 0;
        $First     = $room_data[Redis_CONST::Room_Field_Firstlossrate];
        $second    = $room_data[Redis_CONST::Room_Field_Secondlossrate];
        $three     = $room_data[Redis_CONST::Room_Field_Threelossrate];
        foreach ($chiparray as $side) {
            $Chips = $this->queryChipArea($room_id, $player_id, $side);
            if ($Chips == Redis_CONST::DATA_NONEXISTENT) {
                continue;
            }
            if ($side == $prize) //与开奖结果相同的筹码
            {
                $score += $Chips[Game::Rule_Center] * $First;
                $score += ($Chips[Game::Rule_LeftAngle] + $Chips[Game::Rule_RightAngle] + $Chips[Game::Rule_Bunch]) * $three;
                $score += ($Chips[Game::Rule_LeftStick] + $Chips[Game::Rule_RightStick] + $Chips[Game::Rule_Same]) * $second;

            } else if ($side == $other_side) { //与开奖结果相对的筹码
                $score += $Chips[Game::Rule_Bunch] * $three;
                $score -= $Chips[Game::Rule_Center];
                $score -= $Chips[Game::Rule_LeftAngle];
                $score -= $Chips[Game::Rule_RightAngle];
                $score -= $Chips[Game::Rule_LeftStick];
                $score -= $Chips[Game::Rule_RightStick];
            } else {  //其它两边的筹码
                $score -= $Chips[Game::Rule_Center];
                $score -= $Chips[Game::Rule_Bunch];
                $score -= $Chips[Game::Rule_Same];
                //奖的左边
                if ((($prize + 1) == $side) || (($prize + 1 - 4) == $side)) {
                    $score -= $Chips[Game::Rule_LeftAngle];
                    $score -= $Chips[Game::Rule_LeftStick];
                }

                //奖的右边
                if ((($prize - 1) == $side) || (($prize - 1 + 4) == $side)) {
                    $score -= $Chips[Game::Rule_RightStick];
                    $score -= $Chips[Game::Rule_RightAngle];
                }

            }
        }

        return (int)($score);

    }


    /**
     * 函数描述：获取房间抢庄倒计时
     * @param $room_id
     * @return bool
     * author
     * date 2019/1/14
     */
    protected function queryGameCountDownGrab($room_id) {

        $Redis_Model = Redis_Model::getModelObject();
        $replyArr    = array("[roomid]" => $room_id);
        $Room_Key    = strtr(Redis_Const::Room_Key, $replyArr);

        $result     = $Redis_Model->hgetField($Room_Key, Redis_Const::Room_Field_CountDown_Grab);
        $limit_time = $result > 0 ? $result : Game::LimitTime_Grab;

        return $limit_time;
    }

    /**
     * 函数描述：获取房开奖间倒计时
     * @param $room_id
     * @return bool
     * author
     * date 2019/1/14
     */
    protected function queryGameCountDownShow($room_id) {

        $Redis_Model = Redis_Model::getModelObject();
        $replyArr    = array("[roomid]" => $room_id);
        $Room_Key    = strtr(Redis_Const::Room_Key, $replyArr);

        $result     = $Redis_Model->hgetField($Room_Key, Redis_Const::Room_Field_CountDown_Show);
        $limit_time = $result > 0 ? $result : Game::LimitTime_Show;

        return $limit_time;
    }

    //设置 自动不叫庄 定时器
    protected function setupGrabPassiveTimer($room_id) {
        $limit_time = $this->queryGameCountDownGrab($room_id);
        $limit_time += 1;

        $callback_array = array(
            'operation' => "GrabPassive",
            'room_id' => $room_id,
            'data' => array()
        );
        $arr            = array(
            'operation' => "BuildTimer",
            'room_id' => $room_id,
            'data' => array(
                'limit_time' => $limit_time,
                'callback_array' => $callback_array
            )
        );
        $this->setTimerTime($room_id);    //分开、提前设置时间
        $this->writeLog("setupGrabPassiveTimer start");
        $this->sendToTimerServer($arr);
    }


    //设置 自动开局 定时器
    protected function setupStartGamePassiveTimer($room_id) {

        $limit_time = $this->queryGameCountDownReady($room_id);

        $callback_array = array(
            'operation' => "StartGamePassive",
            'room_id' => $room_id,
            'data' => array()
        );
        $arr            = array(
            'operation' => "BuildTimer",
            'room_id' => $room_id,
            'data' => array(
                'limit_time' => $limit_time + 1,
                'callback_array' => $callback_array
            )

        );
        $this->sendToTimerServer($arr);

        $this->setTimerTime($room_id);    //分开、提前设置时间
        $arr = array("result" => 0, "operation" => "StartLimitTime", "data" => array('limit_time' => $limit_time), "result_message" => "开始倒计时");
        $this->pushMessageToGroup($room_id, $arr);
    }


    //设置 清理房间 定时器
    protected function setupClearRoomPassiveTimer($room_id) {


        $timer_id = $this->queryTimerId($room_id);
        if ($timer_id == -1) {
            $callback_array = array(
                'operation' => "ClearRoomPassive",
                'room_id' => $room_id,
                'data' => array()
            );
            $arr            = array(
                'operation' => "BuildTimer",
                'room_id' => $room_id,
                'data' => array(
                    'limit_time' => Game::LimitTime_ClearRoom,
                    'callback_array' => $callback_array
                )
            );
            $this->sendToTimerServer($arr);
            $this->writeLog("[$room_id] 设置自动清扫房间定时器");
        } else {
            $this->writeLog("[$room_id] 所有玩家离开房间，但是还在游戏中不启动清理房间定时器");
        }

        //$this->setTimerTime($room_id);	//分开、提前设置时间   (这个清扫定时器就暂不设置时间了)
    }


    //删除定时器
    protected function deleteRoomTimer($room_id) {

        $timer_id = $this->queryTimerId($room_id);
        if ($timer_id > 0) {
            $arr = array(
                'operation' => "DeleteTimer",
                'room_id' => $room_id,
                'data' => array(
                    'timer_id' => $timer_id
                )

            );
            $this->sendToTimerServer($arr);
        }
        $this->writeLog("deleteRoomTimer [$timer_id]");
        $this->updateTimer($room_id, -1);
    }

    //查询倒计时
    protected function queryCountdown($room_id, $limit) {

        $Redis_Model = Redis_Model::getModelObject();
        $replyArr    = array("[roomid]" => $room_id);
        $Play_Key    = strtr(Redis_Const::Play_Key, $replyArr);

        $timer_time = $Redis_Model->hgetField($Play_Key, Redis_Const::Play_Field_TimerTime);

        if ($timer_time > 0) {
            $countdown = $limit - time() + $timer_time;
            return $countdown > 0 ? $countdown : 0;
        } else {

            return 0;
        }
    }

    //设置或取消(timer_id  -1)定时器
    protected function updateTimer($room_id, $timer_id) {

        $Redis_Model = Redis_Model::getModelObject();
        $replyArr    = array("[roomid]" => $room_id);
        $Play_Key    = strtr(Redis_Const::Play_Key, $replyArr);

        $mkv[Redis_Const::Play_Field_TimerId] = $timer_id;
        if ($timer_id > 0) {
            $mkv[Redis_Const::Play_Field_TimerTime] = time();
        } else {
            $mkv[Redis_Const::Play_Field_TimerTime] = -1;
        }

        $mset_result = $Redis_Model->hmsetField($Play_Key, $mkv);
    }

    //获取定时器id
    protected function queryRoomTimerId($room_id) {

        $Redis_Model = Redis_Model::getModelObject();
        $replyArr    = array("[roomid]" => $room_id);
        $Room_Key    = strtr(Redis_Const::Room_Key, $replyArr);

        $result = $Redis_Model->hgetField($Room_Key, Redis_Const::Room_Field_ActiveTimer);
        return $result;
    }

    protected function setTimerTime($room_id) {
        $Redis_Model                            = Redis_Model::getModelObject();
        $replyArr                               = array("[roomid]" => $room_id);
        $Play_Key                               = strtr(Redis_Const::Play_Key, $replyArr);
        $mkv[Redis_Const::Play_Field_TimerTime] = time();
        $mset_result                            = $Redis_Model->hmsetField($Play_Key, $mkv);
    }

    protected function getTimerTime($room_id) {
        $Redis_Model = Redis_Model::getModelObject();
        $replyArr    = array("[roomid]" => $room_id);
        $Play_Key    = strtr(Redis_Const::Play_Key, $replyArr);
        $timer_time  = $Redis_Model->hgetField($Play_Key, Redis_Const::Play_Field_TimerTime);
        return $timer_time > 0 ? $timer_time : -1;
    }

    protected function getTimerLeft($room_id, $account_status) {

        $time = $this->getTimerTime($room_id);
        if ($time == -1) {
            return 0;
        }

        $now  = time();
        $time = $now - $time;
        $time > 0 ? $time : 0;

        //依据状态获取倒计时时间
        switch ($account_status) {
            case Game::AccountStatus_Notready:
                $timeCount = $this->queryGameCountDownReady($room_id);
                break;
            case Game::AccountStatus_Ready:
            case Game::AccountStatus_Choose:
                $timeCount = $this->queryGameCountDownGrab($room_id);
                break;
            case Game::AccountStatus_Put:
                $timeCount = $this->queryGameCountDownPut($room_id);
                break;
            case Game::AccountStatus_Bet:
                $timeCount = $this->queryGameCountDownBet($room_id);
                break;
            case Game::AccountStatus_Notshow:
            case Game::AccountStatus_Show:
                $timeCount = $this->queryGameCountDownShow($room_id);
                break;
            default:
                $timeCount = 0;
                break;

        }

        if ($timeCount > 0) {
            $lefttime = $timeCount - $time;
        } else {
            $lefttime = 0;
        }

        return $lefttime > 0 ? $lefttime : 0;
    }


    protected function setTimerId($room_id, $timer_id) {
        $Redis_Model                          = Redis_Model::getModelObject();
        $replyArr                             = array("[roomid]" => $room_id);
        $Play_Key                             = strtr(Redis_Const::Play_Key, $replyArr);
        $mkv[Redis_Const::Play_Field_TimerId] = $timer_id;
        $mset_result                          = $Redis_Model->hmsetField($Play_Key, $mkv);
    }

    //获取定时器id
    protected function queryTimerId($room_id) {

        $Redis_Model = Redis_Model::getModelObject();
        $replyArr    = array("[roomid]" => $room_id);
        $Play_Key    = strtr(Redis_Const::Play_Key, $replyArr);
        $result      = $Redis_Model->hgetField($Play_Key, Redis_Const::Play_Field_TimerId);
        return $result > 0 ? $result : -1;
    }

    //更新房间状态
    protected function updateRoomStatus($room_id, $status) {

        $Redis_Model = Redis_Model::getModelObject();
        $replyArr    = array("[roomid]" => $room_id);
        $Room_Key    = strtr(Redis_Const::Room_Key, $replyArr);

        $mkv[Redis_Const::Room_Field_Status] = $status;
        $mset_result                         = $Redis_Model->hmsetField($Room_Key, $mkv);
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

                $mkv[$account_id] = $status;
            }
        }
        $mset_result = $Redis_Model->hmsetField($AccountStatus_Key, $mkv);  //用户状态
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
        $mset_result = $Redis_Model->hmsetField($RoomScore_Key, $mkv);  //用户状态
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

    //从Redis数据库获取房间数据
    protected function queryRoomData($room_id) {

        $Redis_Model = Redis_Model::getModelObject();
        $replyArr    = array("[roomid]" => $room_id);
        $Room_Key    = strtr(Redis_Const::Room_Key, $replyArr);

        $result = $Redis_Model->hgetallField($Room_Key);
        if ($result == Redis_CONST::DATA_NONEXISTENT) {
            return FALSE;
        }

        if (!isset($result['creator'])) {
            $result['creator'] = "-1";
        }
        if (!isset($result['ticketcnt'])) {
            $result['ticketcnt'] = Game::Default_SpendTicketCount;
        }
        if (!isset($result['chiptype'])) {
            $result['chiptype'] = Game::Chip_Array;
        }

        if (!isset($result['totalnum'])) {
            $result['totalnum'] = Config::GameNum_EachRound * $result['ticketcnt'];
        }

        if (!isset($result['countDown_ready'])) {
            $result['countDown_ready'] = Game::LimitTime_Ready;
        }
        if (!isset($result['countDown_grab'])) {
            $result['countDown_grab'] = Game::LimitTime_Grab;
        }
        if (!isset($result['countDown_put'])) {
            $result['countDown_put'] = Game::LimitTime_puting;
        }
        if (!isset($result['countDown_bet'])) {
            $result['countDown_bet'] = Game::LimitTime_Betting;
        }
        if (!isset($result['countDown_show'])) {
            $result['countDown_show'] = Game::LimitTime_Show;
        }

        if (!isset($result[Redis_CONST::Room_Field_BankerMode])) {
            $result[Redis_CONST::Room_Field_BankerMode] = Game::BankerMode_FreeGrab;
        }

        if (!isset($result[Redis_CONST::Room_Field_StartTime])) {
            $result[Redis_CONST::Room_Field_StartTime] = -1;
        }

        if (!isset($result[Redis_CONST::Room_Field_GameType])) {
            $result[Redis_CONST::Room_Field_GameType] = Game::Game_DarkPo10_Type;
        }

        if (!isset($result[Redis_CONST::Room_Field_Firstlossrate])) {
            $result[Redis_CONST::Room_Field_Firstlossrate] = Game::Default_Firstlossrate;
        }
        if (!isset($result[Redis_CONST::Room_Field_Secondlossrate])) {
            $result[Redis_CONST::Room_Field_Secondlossrate] = Game::Default_Secondlossrate;
        }

        if (!isset($result[Redis_CONST::Room_Field_Threelossrate])) {
            $result[Redis_CONST::Room_Field_Threelossrate] = Game::Default_Threelossrate;
        }
        return $result;
    }

    //获取房间状态
    protected function queryRoomStatus($room_id) {

        $Redis_Model = Redis_Model::getModelObject();
        $replyArr    = array("[roomid]" => $room_id);
        $Room_Key    = strtr(Redis_Const::Room_Key, $replyArr);

        $result = $Redis_Model->hgetField($Room_Key, Redis_Const::Room_Field_Status);
        return $result;
    }

    //获取房间每轮需要消耗的房卡数
    protected function queryTicketCount($room_id) {

        $Redis_Model = Redis_Model::getModelObject();
        $replyArr    = array("[roomid]" => $room_id);
        $Room_Key    = strtr(Redis_Const::Room_Key, $replyArr);

        $result = $Redis_Model->hgetField($Room_Key, Redis_Const::Room_Field_TicketCount);
        return $result > 0 ? $result : 1;
    }

    //获取房间一轮总局数
    protected function queryTotalNum($room_id) {

        $Redis_Model = Redis_Model::getModelObject();
        $replyArr    = array("[roomid]" => $room_id);
        $Room_Key    = strtr(Redis_Const::Room_Key, $replyArr);

        $result = $Redis_Model->hgetField($Room_Key, Redis_Const::Room_Field_TotalNum);
        return $result;
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

        $game_num = $Redis_Model->hincrbyField($Room_Key, Redis_Const::Room_Field_GameNum, 1);
        $this->writeLog("[$room_id] 新的一局 :" . $game_num);

        if ($game_num == 1) {
            //设置开局时间
            $this->updateStartTime($room_id);
        }

    }

    /**
     * 函数描述：获取房间准备倒计时
     * @param $room_id
     * @return bool
     * date 2019/1/14
     */
    protected function queryGameCountDownReady($room_id) {

        $Redis_Model = Redis_Model::getModelObject();
        $replyArr    = array("[roomid]" => $room_id);
        $Room_Key    = strtr(Redis_Const::Room_Key, $replyArr);

        $result     = $Redis_Model->hgetField($Room_Key, Redis_Const::Room_Field_CountDown_ready);
        $limit_time = $result > 0 ? $result : Game::LimitTime_Ready;
        return $limit_time;
    }


    /**
     * 函数描述：获取房间下注倒计时
     * @param $room_id
     * @return bool
     * date 2019/1/14
     */
    protected function queryGameCountDownPut($room_id) {

        $Redis_Model = Redis_Model::getModelObject();
        $replyArr    = array("[roomid]" => $room_id);
        $Room_Key    = strtr(Redis_Const::Room_Key, $replyArr);

        $result     = $Redis_Model->hgetField($Room_Key, Redis_Const::Room_Field_CountDown_Put);
        $limit_time = $result > 0 ? $result : Game::LimitTime_puting;

        return $limit_time;
    }


    /**
     * 函数描述：获取房间下注倒计时
     * @param $room_id
     * @return bool
     * date 2019/1/14
     */
    protected function queryGameCountDownBet($room_id) {

        $Redis_Model = Redis_Model::getModelObject();
        $replyArr    = array("[roomid]" => $room_id);
        $Room_Key    = strtr(Redis_Const::Room_Key, $replyArr);

        $result     = $Redis_Model->hgetField($Room_Key, Redis_Const::Room_Field_CountDown_Bet);
        $limit_time = $result > 0 ? $result : Game::LimitTime_Betting;

        return $limit_time;
    }

    //更新用户状态
    protected function updateAccountStatus($room_id, $account_id, $status) {

        $Redis_Model       = Redis_Model::getModelObject();
        $replyArr          = array("[roomid]" => $room_id);
        $AccountStatus_Key = strtr(Redis_Const::AccountStatus_Key, $replyArr);

        $mkv[$account_id] = $status;
        $mset_result      = $Redis_Model->hmsetField($AccountStatus_Key, $mkv);

        //广播用户状态改变
        $noty_arr = array(
            'account_id' => $account_id,
            'account_status' => $status,
            'online_status' => $this->queryOnlineStatus($room_id, $account_id)
        );
        $this->writeLog("[$room_id] ($account_id) 状态改变 " . $status);
        $this->notyUpdateAccountStatusToGroup($room_id, $noty_arr);
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
            'account_id' => $account_id,
            'account_status' => $status,
            'online_status' => $online_status,
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
        $msg_arr = array("result" => "0", "operation" => "UpdateAudienceInfo", "audience" => $noty_arr,
            "result_message" => "观众状态改变");
        $this->pushMessageToGroup($room_id, $msg_arr, $exclude_client_id = NULL);
        if ($status == Game::AudienceStatus_off) {
            $zrem_result = $Redis_Model->zremSet($RoomAudience_Key, array($account_id));
            $zrem_result = $Redis_Model->zremSetbyscore($RoomAudienceInfo_Key, $rsAud_score, $rsAud_score);
        }
    }


    //获取在线状态
    protected function queryOnlineStatus($room_id, $account_id) {

        $replyArr     = array("[roomid]" => $room_id, "[accountid]" => $account_id);
        $RoomUser_UID = strtr(Game::RoomUser_UID, $replyArr);

        return Gateway::isUidOnline($RoomUser_UID);
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

    //获取所有选择抢庄的人
    protected function queryGrabUser($room_id) {

        $Redis_Model       = Redis_Model::getModelObject();
        $replyArr          = array("[roomid]" => $room_id);
        $AccountStatus_Key = strtr(Redis_Const::AccountStatus_Key, $replyArr);

        $grab_user_array = [];
        $result          = $Redis_Model->hgetallField($AccountStatus_Key);
        if (Redis_CONST::DATA_NONEXISTENT !== $result) {
            foreach ($result as $account_id => $status) {
                if ($status == Game::AccountStatus_Grab) {
                    $grab_user_array[] = $account_id;
                }
            }
        }
        return $grab_user_array;
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

    //更新用户状态,不通知
    protected function updateAccountStatusNotNoty($room_id, $account_id, $status) {

        $Redis_Model       = Redis_Model::getModelObject();
        $replyArr          = array("[roomid]" => $room_id);
        $AccountStatus_Key = strtr(Redis_Const::AccountStatus_Key, $replyArr);

        $mkv[$account_id] = $status;
        $mset_result      = $Redis_Model->hmsetField($AccountStatus_Key, $mkv);
        return TRUE;
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


    //获取房间积分榜 json string
    protected function queryRoomScoreboard($room_id) {

        $Redis_Model = Redis_Model::getModelObject();
        $replyArr    = array("[roomid]" => $room_id);
        $Room_Key    = strtr(Redis_Const::Room_Key, $replyArr);

        $result = $Redis_Model->hgetField($Room_Key, Redis_Const::Room_Field_Scoreboard);
        return $result;
    }

    //获取叫分基准
    protected function queryPrize($room_id) {
        $Redis_Model = Redis_Model::getModelObject();
        $replyArr    = array("[roomid]" => $room_id);
        $Play_Key    = strtr(Redis_Const::Play_Key, $replyArr);
        $result      = $Redis_Model->hgetField($Play_Key, Redis_Const::Play_Field_Prize);
        return $result;
    }

    //设置放宝
    protected function updatePrize($room_id, $prize) {

        $Redis_Model = Redis_Model::getModelObject();
        $replyArr    = array("[roomid]" => $room_id);
        $Play_Key    = strtr(Redis_Const::Play_Key, $replyArr);

        $mkv[Redis_Const::Play_Field_Prize] = $prize;
        $mset_result                        = $Redis_Model->hmsetField($Play_Key, $mkv);
    }

    //初始化用户下注的信息
    protected function initChipArea($room_id, $play_member) {

        $Redis_Model = Redis_Model::getModelObject();
        $chips       = array(Game::Rule_Enter, Game::Rule_Dragon, Game::Rule_Exit, Game::Rule_Tiger);
        $replArr     = array("[roomid]" => $room_id);

        //清除每个账号的下的筹码
        foreach ($play_member as $account_id) {
            foreach ($chips as $area) {
                switch ($area) {
                    case Game::Rule_Enter:
                        $Chip_Key = strtr(Redis_Const::Enter_Chip_Area_Key, $replArr);
                        break;
                    case Game::Rule_Dragon:
                        $Chip_Key = strtr(Redis_Const::Dragon_Chip_Area_Key, $replArr);
                        break;
                    case Game::Rule_Exit:
                        $Chip_Key = strtr(Redis_Const::Exit_Chip_Area_Key, $replArr);
                        break;
                    case Game::Rule_Tiger:
                        $Chip_Key = strtr(Redis_Const::Tiger_Chip_Area_Key, $replArr);
                        break;
                }
                $chipArray                  = array(0, 0, 0, 0, 0, 0, 0, 0);
                $chipArray_mkv              = array();
                $chipArray_mkv[$account_id] = implode(",", $chipArray);
                $Redis_Model->hmsetField($Chip_Key, $chipArray_mkv);
            }
        }
        //清除庄家的看到的所有的闲家的筹码
        $chipArray = array(0, 0, 0, 0, 0, 0, 0, 0);

        $chipArray_mkv                             = array();
        $chipArray_mkv[Redis_Const::Enter_All_Key] = implode(",", $chipArray);
        $Chip_Key                                  = strtr(Redis_Const::Enter_All_Key, $replArr);
        $Redis_Model->hmsetField($Chip_Key, $chipArray_mkv);

        $chipArray_mkv                            = array();
        $chipArray_mkv[Redis_Const::Exit_All_Key] = implode(",", $chipArray);
        $Chip_Key                                 = strtr(Redis_Const::Exit_All_Key, $replArr);
        $Redis_Model->hmsetField($Chip_Key, $chipArray_mkv);

        $chipArray_mkv                             = array();
        $chipArray_mkv[Redis_Const::Tiger_All_Key] = implode(",", $chipArray);
        $Chip_Key                                  = strtr(Redis_Const::Tiger_All_Key, $replArr);
        $Redis_Model->hmsetField($Chip_Key, $chipArray_mkv);

        $chipArray_mkv                              = array();
        $chipArray_mkv[Redis_Const::Dragon_All_Key] = implode(",", $chipArray);
        $Chip_Key                                   = strtr(Redis_Const::Dragon_All_Key, $replArr);
        $Redis_Model->hmsetField($Chip_Key, $chipArray_mkv);

    }


    //获取用户下注的信息
    protected function queryChipArea($room_id, $account_id, $area) {
        if (!(Game::Rule_Enter <= $area && $area <= Game::Rule_Tiger)) {
            return Redis_CONST::DATA_NONEXISTENT;
        }

        $Redis_Model = Redis_Model::getModelObject();

        $replArr = array("[roomid]" => $room_id);
        switch ($area) {
            case Game::Rule_Enter:
                $Chip_Key = strtr(Redis_Const::Enter_Chip_Area_Key, $replArr);
                break;
            case Game::Rule_Dragon:
                $Chip_Key = strtr(Redis_Const::Dragon_Chip_Area_Key, $replArr);
                break;
            case Game::Rule_Exit:
                $Chip_Key = strtr(Redis_Const::Exit_Chip_Area_Key, $replArr);
                break;
            case Game::Rule_Tiger:
                $Chip_Key = strtr(Redis_Const::Tiger_Chip_Area_Key, $replArr);
                break;
            default:
                return Redis_CONST::DATA_NONEXISTENT;
        }
        $chip_str = $Redis_Model->hgetField($Chip_Key, $account_id);
        $chips    = explode(",", $chip_str);
        $arrLen   = count($chips);
        for ($x = 0; $x < $arrLen; $x++) {
            $chips[$x] = (int)$chips[$x];
        }
        return $chips;
    }

    protected function updateChipArea($room_id, $account_id, $area, $subarea, $score) {
        if ($score <= 0) {
            return FALSE;
        }

        $Redis_Model = Redis_Model::getModelObject();

        $replyArr = array("[roomid]" => $room_id);
        //更新详细的分数
        switch ($area) {
            case Game::Rule_Enter:
                $Chip_Key = strtr(Redis_Const::Enter_Chip_Area_Key, $replyArr);
                break;
            case Game::Rule_Dragon:
                $Chip_Key = strtr(Redis_Const::Dragon_Chip_Area_Key, $replyArr);

                break;
            case Game::Rule_Exit:
                $Chip_Key = strtr(Redis_Const::Exit_Chip_Area_Key, $replyArr);

                break;
            case Game::Rule_Tiger:
                $Chip_Key = strtr(Redis_Const::Tiger_Chip_Area_Key, $replyArr);
                break;
            default:
                $this->writeLog("function(updateChipArea):area invalid" . " in file" . __FILE__ . " on Line " . __LINE__);
                return FALSE;
        }

        $chip_str = $Redis_Model->hgetField($Chip_Key, $account_id);
        if ($chip_str == Redis_CONST::DATA_NONEXISTENT) {
            $chipArray = array(0, 0, 0, 0, 0, 0);
        } else {
            $chipArray = explode(",", $chip_str);
        }

        $chipArray[$subarea] = (int)($chipArray[$subarea]) + $score;
        ///////////
        $chipArray_mkv              = array();
        $chipArray_mkv[$account_id] = implode(",", $chipArray);
        $Redis_Model->hmsetField($Chip_Key, $chipArray_mkv);
        return TRUE;
    }

    //更新所有玩家的筹码信息，方便庄家查询
    protected function updateAllChipArea($room_id, $area, $subarea, $score) {
        if ($score <= 0) {
            return FALSE;
        }

        $Redis_Model = Redis_Model::getModelObject();

        $replyArr = array("[roomid]" => $room_id);
        //更新详细的分数
        switch ($area) {
            case Game::Rule_Enter:
                $area     = Redis_Const::Enter_All_Key;
                $Chip_Key = strtr($area, $replyArr);
                break;
            case Game::Rule_Dragon:
                $area     = Redis_Const::Dragon_All_Key;
                $Chip_Key = strtr($area, $replyArr);

                break;
            case Game::Rule_Exit:
                $area     = Redis_Const::Exit_All_Key;
                $Chip_Key = strtr($area, $replyArr);

                break;
            case Game::Rule_Tiger:
                $area     = Redis_Const::Tiger_All_Key;
                $Chip_Key = strtr($area, $replyArr);
                break;
            default:
                $this->writeLog("function(updateChipArea):area invalid" . " in file" . __FILE__ . " on Line " . __LINE__);
                return FALSE;
        }

        $chip_str = $Redis_Model->hgetField($Chip_Key, $area);
        if ($chip_str == Redis_CONST::DATA_NONEXISTENT) {
            $chipArray = array(0, 0, 0, 0, 0, 0);
        } else {
            $chipArray = explode(",", $chip_str);
        }

        $chipArray[$subarea] = (int)($chipArray[$subarea]) + $score;
        ///////////
        $chip_str             = implode(",", $chipArray);
        $parameter_ary[$area] = $chip_str;
        $mset_result          = $Redis_Model->hmsetField($Chip_Key, $parameter_ary);
        return TRUE;
    }


    public function updateChipArray($room_id, $chip) {
        $Redis_Model = Redis_Model::getModelObject();
        $replyArr    = array("[roomid]" => $room_id);
        $ChipArr_Key = strtr(Redis_Const::Chip_Array_Key, $replyArr);
        $chipArr     = $Redis_Model->harrayGet($ChipArr_Key);
        if ($chipArr != FALSE) {
            $chipArr[] = $chip;
            $Redis_Model->harraySet($ChipArr_Key, $chipArr);

        } else {
            $chipArr[] = $chip;
            $Redis_Model->harraySet($ChipArr_Key, $chipArr);
        }

        return;
    }

    public function getChipArray($room_id) {
        $Redis_Model = Redis_Model::getModelObject();
        $replyArr    = array("[roomid]" => $room_id);
        $ChipArr_Key = strtr(Redis_Const::Chip_Array_Key, $replyArr);
        $chipArr     = $Redis_Model->harrayGet($ChipArr_Key);

        return $chipArr;
    }

    public function updateChip($room_id, $account_id, $score) {
        $Redis_Model = Redis_Model::getModelObject();
        $replyArr    = array("[roomid]" => $room_id);
        $Chip_Key    = strtr(Redis_Const::Chip_Key, $replyArr);
        $mset_result = $Redis_Model->hincrbyField($Chip_Key, $account_id, $score);
        return;
    }


    // 获取玩家当局下注总分
    public function queryChip($room_id, $account_id) {
        $Redis_Model = Redis_Model::getModelObject();
        $replyArr    = array("[roomid]" => $room_id);
        $Chip_Key    = strtr(Redis_Const::Chip_Key, $replyArr);
        $chip        = $Redis_Model->hgetField($Chip_Key, $account_id);
        return $chip > 0 ? $chip : 0;
    }

    //获取分数池分数
    protected function queryPoolScore($room_id) {
        $Redis_Model = Redis_Model::getModelObject();
        $replyArr    = array("[roomid]" => $room_id);
        $Play_Key    = strtr(Redis_Const::Play_Key, $replyArr);
        $result      = $Redis_Model->hgetField($Play_Key, Redis_Const::Play_Field_PoolScore);
        return $result > 0 ? $result : 0;
    }

    protected function queryGameType($room_id) {
        $Redis_Model = Redis_Model::getModelObject();
        $replyArr    = array("[roomid]" => $room_id);
        $Room_Key    = strtr(Redis_Const::Room_Key, $replyArr);
        $result      = $Redis_Model->hgetField($Room_Key, Redis_Const::Room_Field_GameType);
        return $result > 0 ? $result : -1;
    }

    //获取庄家account_id
    protected function queryBanker($room_id) {
        $Redis_Model = Redis_Model::getModelObject();
        $replyArr    = array("[roomid]" => $room_id);
        $Room_Key    = strtr(Redis_Const::Room_Key, $replyArr);
        $result      = $Redis_Model->hgetField($Room_Key, Redis_Const::Room_Field_Banker);
        return $result > 0 ? $result : -1;
    }

    //设置庄家account_id
    protected function updateBanker($room_id, $account_id) {
        $Redis_Model = Redis_Model::getModelObject();
        $replyArr    = array("[roomid]" => $room_id);
        $Play_Key    = strtr(Redis_Const::Room_Key, $replyArr);

        $parameter_ary[Redis_CONST::Room_Field_Banker] = $account_id;
        $mset_result                                   = $Redis_Model->hmsetField($Play_Key, $parameter_ary);
        return TRUE;
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


    //获取房主id
    protected function queryCreator($room_id) {
        $Redis_Model = Redis_Model::getModelObject();
        $replyArr    = array("[roomid]" => $room_id);
        $Room_Key    = strtr(Redis_Const::Room_Key, $replyArr);
        $result      = $Redis_Model->hgetField($Room_Key, Redis_Const::Room_Field_Creator);
        return $result;
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

    //获取开局时间
    protected function queryStartTime($room_id) {
        $Redis_Model = Redis_Model::getModelObject();
        $replyArr    = array("[roomid]" => $room_id);
        $Room_Key    = strtr(Redis_Const::Room_Key, $replyArr);
        $result      = $Redis_Model->hgetField($Room_Key, Redis_Const::Room_Field_StartTime);
        return $result > 0 ? $result : -1;
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


    //获取游戏中的人数
    protected function queryPlayMemberCount($room_id) {
        $Redis_Model    = Redis_Model::getModelObject();
        $replyArr       = array("[roomid]" => $room_id);
        $PlayMember_Key = strtr(Redis_Const::PlayMember_Key, $replyArr);
        $result         = $Redis_Model->llenList($PlayMember_Key);
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
        $Play_Key                                = strtr(Redis_Const::Play_Key, $replyArr);
        $mkv[Redis_Const::Play_Field_ActiveUser] = $active_id;
        $mset_result                             = $Redis_Model->hmsetField($Play_Key, $mkv);

        return $active_id;
    }

    //推消息
    protected function pushMessageToGroup($room_id, $msg_arr, $exclude_client_id = NULL) {
        $msg = $this->_JSON($msg_arr);
        Gateway::sendToGroup($room_id, $msg, $exclude_client_id);
    }

    protected function pushMessageToAccount($room_id, $account_id, $msg_arr) {
        $replyArr     = array("[roomid]" => $room_id, "[accountid]" => $account_id);
        $RoomUser_UID = strtr(Game::RoomUser_UID, $replyArr);
        $msg          = $this->_JSON($msg_arr);
        Gateway::sendToUid($RoomUser_UID, $msg);
    }

    protected function pushMessageToCurrentClient($msg_arr) {
        $msg = $this->_JSON($msg_arr);
        Gateway::sendToCurrentClient($msg);
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


    protected function clearRoom($room_id, $is_delete = FALSE) {
        //清理房间
        $Redis_Model = Redis_Model::getModelObject();
        $replyArr    = array("[roomid]" => $room_id);

        $Room_Key          = strtr(Redis_Const::Room_Key, $replyArr);
        $RoomSequence_Key  = strtr(Redis_Const::RoomSequence_Key, $replyArr);
        $RoomScore_Key     = strtr(Redis_Const::RoomScore_Key, $replyArr);
        $AccountStatus_Key = strtr(Redis_Const::AccountStatus_Key, $replyArr);
        $TicketChecked_Key = strtr(Redis_Const::TicketChecked_Key, $replyArr);

        $Play_Key       = strtr(Redis_Const::Play_Key, $replyArr);
        $PlayMember_Key = strtr(Redis_Const::PlayMember_Key, $replyArr);
        $Card_Key       = strtr(Redis_Const::Card_Key, $replyArr);
        $Chip_Key       = strtr(Redis_Const::Chip_Key, $replyArr);
        //删除每轮游戏参数
        $Redis_Model->deleteKey($RoomSequence_Key);
        $Redis_Model->deleteKey($RoomScore_Key);
        $Redis_Model->deleteKey($AccountStatus_Key);
        $Redis_Model->deleteKey($TicketChecked_Key);
        //删除每局游戏参数
        $Redis_Model->deleteKey($Play_Key);
        $Redis_Model->deleteKey($PlayMember_Key);
        $Redis_Model->deleteKey($Card_Key);
        $Redis_Model->deleteKey($Chip_Key);

        if (Config::Room_IsReuse == 1) {    //可以重复利用房间
            $r_mkv[Redis_Const::Room_Field_GameNum]      = 0;            //游戏局数
            $r_mkv[Redis_Const::Room_Field_DefaultScore] = Game::Default_Score;        //开局默认分数
            $r_mkv[Redis_Const::Room_Field_Scoreboard]   = "";        //积分榜清零
            $r_mkv[Redis_Const::Room_Field_Creator]      = -1;        //创建者清空
            $r_mkv[Redis_Const::Room_Field_Status]       = Game::RoomStatus_Closed;    //房间状态，1等待、2进行中、3未激活
            $r_mkv[Redis_Const::Room_Field_StartTime]    = -1;
            $mset_result                                 = $Redis_Model->hmsetField($Room_Key, $r_mkv);
        } else {  //玩一轮即销毁
            $MMYSQL = $this->initMysql();

            $MMYSQL->update(Room)->set('is_close', '1')->set('is_delete', ($is_delete ? '1' : '0'))->set('room_config', NULL)->where('room_id=' . $room_id)->query();

            $Redis_Model->deleteKey($Room_Key);
        }
        $this->writeLog("[$room_id] 房间清理完毕!");
    }

    //整理规则内容文本
    protected function formatRuleText($room_id) {
        $rule_text       = "";
        $setting         = $this->queryRoomSetting($room_id);
        $ticket_count    = isset($setting[Redis_Const::Room_Field_TicketCount]) ? $setting[Redis_Const::Room_Field_TicketCount] : 1;
        $chip_type       = isset($setting[Redis_Const::Room_Field_ChipType]) ? explode(",", $setting[Redis_Const::Room_Field_ChipType]) : array(10, 20, 30, 50, 100);
        $upper_limit     = isset($setting[Redis_Const::Room_Field_UpperLimit]) ? $setting[Redis_Const::Room_Field_UpperLimit] : 1000;
        $game_type       = isset($setting[Redis_CONST::Room_Field_GameType]) ? $setting[Redis_CONST::Room_Field_GameType] : 61;
        $first_lossrate  = isset($setting[Redis_CONST::Room_Field_Firstlossrate]) ? $setting[Redis_CONST::Room_Field_Firstlossrate] : 3;
        $second_lossrate = isset($setting[Redis_CONST::Room_Field_Secondlossrate]) ? $setting[Redis_CONST::Room_Field_Secondlossrate] : 2;
        $three_lossrate  = isset($setting[Redis_CONST::Room_Field_Threelossrate]) ? $setting[Redis_CONST::Room_Field_Threelossrate] : 1;


        //局数房卡规则
        $ticket_type_text = (Config::GameNum_EachRound * $ticket_count) . "局/" . $ticket_count . "张房卡";
        $rule_text        .= $ticket_type_text;


        switch ($game_type) {
            case Game::Game_DarkPo10_Type:
                $game_name = "10人暗宝";
                break;
            case Game::Game_DarkPo13_Type:
                $game_name = "13人暗宝";
                break;
            case Game::Game_DarkPo16_Type:
                $game_name = "16人暗宝";
                break;
            default:
                break;
        }
        $rule_text .= ",";
        $rule_text .= $game_name;

        //筹码类型
        $rule_text .= ",";
        $chip_str  = implode("/", $chip_type);
        $rule_text .= "筹码:" . $chip_str;

        //封顶上限
        if ($upper_limit > 0) {
            $rule_text .= ",上限:" . $upper_limit . "分";
        } else {
            $rule_text .= ",上限:无";
        }
        //赔率设置
        $rule_text .= ",赔率设置：";
        $rule_text .= "龙虎出入：1:" . $first_lossrate;
        $rule_text .= "同,粘：1:" . $second_lossrate;
        $rule_text .= "角,串：1:" . $three_lossrate;

        return $rule_text;
    }

    //依据筹码分布位置获取描述信息
    protected function getChipDes($area, $subarea, $chip) {

        switch ($area) {
            case Game::Rule_Enter:
                if ($subarea == Game::Rule_Center) {
                    $result = "入 ";
                } else if ($subarea == Game::Rule_LeftAngle) {
                    $result = "龙-入-角 ";
                } else if ($subarea == Game::Rule_RightAngle) {
                    $result = "虎-入-角 ";
                } else if ($subarea == Game::Rule_Bunch) {
                    $result = "入-串 ";
                } else if ($subarea == Game::Rule_LeftStick) {
                    $result = "入-粘-入1 ";
                } else if ($subarea == Game::Rule_RightStick) {
                    $result = "入-粘-入2 ";
                } else if ($subarea == Game::Rule_Same) {
                    $result = "入-同 ";
                }
                break;
            case Game::Rule_Dragon:
                if ($subarea == Game::Rule_Center) {
                    $result = "龙 ";
                } else if ($subarea == Game::Rule_LeftAngle) {
                    $result = "龙-出-角 ";
                } else if ($subarea == Game::Rule_RightAngle) {
                    $result = "龙-入-角 ";
                } else if ($subarea == Game::Rule_Bunch) {
                    $result = "龙-串 ";
                } else if ($subarea == Game::Rule_LeftStick) {
                    $result = "龙-粘-龙1 ";
                } else if ($subarea == Game::Rule_RightStick) {
                    $result = "龙-粘-龙2 ";
                } else if ($subarea == Game::Rule_Same) {
                    $result = "龙-同 ";
                }
                break;
            case Game::Rule_Exit:
                if ($subarea == Game::Rule_Center) {
                    $result = "出 ";
                } else if ($subarea == Game::Rule_LeftAngle) {
                    $result = "虎-出-角 ";
                } else if ($subarea == Game::Rule_RightAngle) {
                    $result = "龙-出-角 ";
                } else if ($subarea == Game::Rule_Bunch) {
                    $result = "出-串 ";
                } else if ($subarea == Game::Rule_LeftStick) {
                    $result = "出-粘-出2 ";
                } else if ($subarea == Game::Rule_RightStick) {
                    $result = "出-粘-出1 ";
                } else if ($subarea == Game::Rule_Same) {
                    $result = "出-同 ";
                }
                break;
            case Game::Rule_Tiger:
                if ($subarea == Game::Rule_Center) {
                    $result = "虎 ";
                } else if ($subarea == Game::Rule_LeftAngle) {
                    $result = "虎-入-角 ";
                } else if ($subarea == Game::Rule_RightAngle) {
                    $result = "虎-出-角 ";
                } else if ($subarea == Game::Rule_Bunch) {
                    $result = "虎-串 ";
                } else if ($subarea == Game::Rule_LeftStick) {
                    $result = "虎-粘-虎2 ";
                } else if ($subarea == Game::Rule_RightStick) {
                    $result = "虎-粘-虎1 ";
                } else if ($subarea == Game::Rule_Same) {
                    $result = "虎-同 ";
                }
                break;
            default:
                $this->writeLog("This is error area,subarea,chip" . $area . $subarea . $chip);
                break;
        }
        $replyArr = array("name" => $result, "chip" => $chip);
        return $replyArr;
    }

    protected function getAllPlayChip($room_id) {

        $chip_array  = array();
        $Redis_Model = Redis_Model::getModelObject();
        $rules       = array(Game::Rule_Enter, Game::Rule_Dragon, Game::Rule_Exit, Game::Rule_Tiger);

        $replyArr = array("[roomid]" => $room_id);

        foreach ($rules as $rule) {
            switch ($rule) {
                case Game::Rule_Enter:
                    $enter_Chip_key = strtr(Redis_Const::Enter_All_Key, $replyArr);
                    $chipstr        = $Redis_Model->hgetField($enter_Chip_key, Redis_Const::Enter_All_Key);
                    break;
                case Game::Rule_Dragon:
                    $dragon_Chip_key = strtr(Redis_Const::Dragon_All_Key, $replyArr);
                    $chipstr         = $Redis_Model->hgetField($dragon_Chip_key, Redis_Const::Dragon_All_Key);
                    break;
                case Game::Rule_Exit:
                    $exit_Chip_key = strtr(Redis_Const::Exit_All_Key, $replyArr);
                    $chipstr       = $Redis_Model->hgetField($exit_Chip_key, Redis_Const::Exit_All_Key);
                    break;
                case Game::Rule_Tiger:
                    $tiger_Chip_key = strtr(Redis_Const::Tiger_All_Key, $replyArr);
                    $chipstr        = $Redis_Model->hgetField($tiger_Chip_key, Redis_Const::Tiger_All_Key);
                    break;
            }
            $Chips = explode(",", $chipstr);
            foreach ($Chips as $index => $chip) {
                if ($chip > 0) {
                    $chip_str     = $this->getChipDes($rule, $index, $chip);
                    $chip_array[] = $chip_str;
                }
            }
        }
        return $chip_array;
    }

    protected function getPlayChip($room_id, $player_id) {

        $chip_array  = array();
        $Redis_Model = Redis_Model::getModelObject();
        $rules       = array(Game::Rule_Enter, Game::Rule_Dragon, Game::Rule_Exit, Game::Rule_Tiger);

        $replyArr = array("[roomid]" => $room_id);

        foreach ($rules as $rule) {
            switch ($rule) {
                case Game::Rule_Enter:
                    $enter_Chip_key = strtr(Redis_Const::Enter_Chip_Area_Key, $replyArr);
                    $chipstr        = $Redis_Model->hgetField($enter_Chip_key, $player_id);
                    break;
                case Game::Rule_Dragon:
                    $dragon_Chip_key = strtr(Redis_Const::Dragon_Chip_Area_Key, $replyArr);
                    $chipstr         = $Redis_Model->hgetField($dragon_Chip_key, $player_id);
                    break;
                case Game::Rule_Exit:
                    $exit_Chip_key = strtr(Redis_Const::Exit_Chip_Area_Key, $replyArr);
                    $chipstr       = $Redis_Model->hgetField($exit_Chip_key, $player_id);
                    break;
                case Game::Rule_Tiger:
                    $tiger_Chip_key = strtr(Redis_Const::Tiger_Chip_Area_Key, $replyArr);
                    $chipstr        = $Redis_Model->hgetField($tiger_Chip_key, $player_id);
                    break;
            }
            $Chips = explode(",", $chipstr);
            foreach ($Chips as $index => $chip) {
                if ($chip > 0) {
                    $chip_str     = $this->getChipDes($rule, $index, $chip);
                    $chip_array[] = $chip_str;
                }
            }
        }
        return $chip_array;
    }


    /*
       保存暗宝游戏结果
   */
    protected function saveGameResult($game_info, $winner_array, $loser_array, $room_data) {
        $timestamp = time();
        $MMYSQL    = $this->initMysql();
        $room_id   = $game_info['room_id'];
        $game_type = $game_info['game_type'];

        $game_data['time']  = time();
        $game_data['rid']   = $game_info['room_id'];
        $game_data['rnd']   = $game_info['round'];
        $game_data['gnum']  = $game_info['game_num'];
        $game_data['tnum']  = $game_info['total_num'];
        $game_data['bid']   = $game_info['banker_id'];
        $game_data['prize'] = $game_info['prize'];
        $Redis_Model        = Redis_Model::getModelObject();

        $replyArr   = array("[roomid]" => $room_id);
        $Chip_Key   = strtr(Redis_Const::Chip_Key, $replyArr);
        $chip_array = $Redis_Model->hgetallField($Chip_Key);


        //获取所有人倍数
        $playing_array = array();    //游戏中玩家
        $player_array  = array();    //玩家当局数据
        foreach ($winner_array as $winner_item) {
            $player_id  = $winner_item['account_id'];
            $array['p'] = $player_id;
            $array['s'] = $winner_item['score'];
            $array['a'] = $this->getPlayChip($room_id, $player_id);//详细的筹码位置信息
            if ($chip_array != Redis_CONST::DATA_NONEXISTENT) {
                $array['c'] = array_key_exists($player_id, $chip_array) ? $chip_array[$player_id] : "0";//总筹码
            } else {
                $array['c'] = "0";
            }
            $player_array[] = $array;
            unset($array);
            $playing_array[] = (int)$player_id;
        }
        foreach ($loser_array as $loser_item) {
            $player_id  = $loser_item['account_id'];
            $array['p'] = $player_id;  //用户账号
            $array['s'] = $loser_item['score']; //得分
            $array['a'] = $this->getPlayChip($room_id, $player_id);//详细的筹码位置信息
            if ($chip_array != Redis_CONST::DATA_NONEXISTENT) {
                $array['c'] = array_key_exists($player_id, $chip_array) ? $chip_array[$player_id] : "0";//总筹码
            } else {
                $array['c'] = "0";
            }
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
            $array['c']     = "0";
            $array['a']     = $this->getPlayChip($room_id, $player_id);
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
            if (($player_data['c'] != "0") || ($player_data['s'] != 0)) {
                $return_player_array[] = array(
                    "name" => $player_data['n'],
                    "account_id" => $player_data['p'],
                    "chip" => $player_data['c'],
                    "score" => $player_data['s'],
                    "chips" => $player_data['a'],
                    "is_banker" => $player_data['p'] == $game_data['bid'] ? 1 : 0,
                );
            }
        }
        $push_result = array(
            "players" => $return_player_array,
            "total_num" => $game_data['tnum'],
            "game_num" => $game_data['gnum'],
            "prize" => $game_info['prize'],
        );
        $arr         = array("result" => 0, "operation" => "GameEndData", "data" => $push_result, "result_message" => "本局结算结果");
        $this->pushMessageToGroup($room_id, $arr);

        $save_game_result['pAry']  = $player_array;
        $save_game_result['gData'] = $game_data;
        $save_game_result['prize'] = $game_info['prize'];
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
            'operation' => "PullGameScore",
            'dealer_num' => $dealer_num,
            'game_type' => $game_type,
            'data' => array()
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
    }


    /**
     * 推给定时器服务器
     */
    public function sendToTimerServer($msg_array = "") {
        $message = $this->_JSON($msg_array);

        $socket_name  = "Timer";
        $Socket_Model = Socket_Model::getModelObject($socket_name);
        $Socket_Model->sendMessageToSocket($socket_name, $message);

        return TRUE;


    }


    /**
     * 数组转JSON格式
     */
    protected function _JSON($array) {
        $this->__arrayRecursive($array, 'urlencode', TRUE);
        $json = json_encode($array);
        return urldecode($json);
    }

    private function __arrayRecursive(&$array, $function, $apply_to_keys_also = FALSE) {
        static $recursive_counter = 0;
        if (++$recursive_counter > 1000) {
            $this->logMessage('error', "function(_JSON):recursive_counter>1000" . " in file" . __FILE__ . " on Line " . __LINE__);
            return;
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


    /**
     * 返回缺参结果
     */
    protected function _missingPrameterArr($operation, $prameter) {
        return array('result' => OPT_CONST::MISSING_PARAMETER, 'operation' => $operation, 'data' => array("missing_parameter" => $prameter), 'result_message' => "缺少参数");
    }

    /**
     * 返回非法参数结果
     */
    protected function _invalidPrameterArr($operation, $prameter) {
        return array('result' => OPT_CONST::MISSING_PARAMETER, 'operation' => $operation, 'data' => array("invalid_parameter" => $prameter), 'result_message' => "非法参数");
    }


    /**
     * 判断数据格式是否正确
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


    /**
     * 判断数据格式是否正确
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


    /**
     * 生成微秒
     */
    protected function getMicroTimestamp() {
        $mtime      = explode(' ', microtime());
        $mTimestamp = $mtime[1] . substr($mtime[0], 2, 3);

        return $mTimestamp;
    }

    /**
     * 判断请求链接合法性
     */
    protected function checkRequestClientLegal($client_id, $room_id, $account_id) {
        return TRUE;
        //绑定用户UID
        $replyArr = array("[roomid]" => $room_id, "[accountid]" => $account_id);
        $room_aid = strtr(Game::RoomUser_UID, $replyArr);

        $client_array = Gateway::getClientIdByUid($room_aid);
        if (is_array($client_array) && count($client_array) > 0) {
            foreach ($client_array as $bind_client) {
                if ($bind_client == $client_id) {
                    return TRUE;
                }
            }
        }
        return FALSE;
    }


}

?>
