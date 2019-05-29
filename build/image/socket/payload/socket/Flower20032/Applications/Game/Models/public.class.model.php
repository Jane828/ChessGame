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
            $journal_array['abstract'] = "炸金花";        //摘要
        } else {
            $journal_array['income']   = -$spend_ticket_count;
            $journal_array['abstract'] = "炸金花房卡退还";        //摘要
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
                $this->writeLog("room_status != 1");
                $success = FALSE;
            }
        });
        return $success;
    }

    //开局
    protected function startGame($room_id, $passive_by_timer = FALSE) {
        $timestamp   = time();
        $Redis_Model = Redis_Model::getModelObject();
        $replyArr    = array("[roomid]" => $room_id);

        $Room_Key = strtr(Redis_Const::Room_Key, $replyArr);
        $Play_Key = strtr(Redis_Const::Play_Key, $replyArr);

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
                //			if($passive_by_timer || $ready_count == $online_count){
                $room_members = $this->queryRoomMembers($room_id);

                $pre_readyUser_ary  = array();        //已准备用户数组-前
                $rear_readyUser_ary = array();        //已准备用户数组-后
                $is_rear            = TRUE;

                $ready_in_room_user = array_intersect($ready_user, $in_room_array); //在房的已准备用户
                if (count($ready_in_room_user) < 2) {
                    $this->writeLog("在房" . $room_id . "的已准备用户数量:" . count($ready_in_room_user) . " 不能开局 in file" . __FILE__ . " on Line " . __LINE__);
                    return FALSE;
                }

                $banker_id = $this->queryBanker($room_id);

                foreach ($room_members as $account_id) {
                    if (in_array($account_id, $ready_in_room_user)) {
                        if ($is_rear) {
                            $rear_readyUser_ary[] = $account_id;
                        } else {
                            $pre_readyUser_ary[] = $account_id;
                        }
                    }

                    if ($account_id == $banker_id) {
                        $is_rear = FALSE;
                    }
                }

                $readyUser_ary = array_merge($pre_readyUser_ary, $rear_readyUser_ary);

                $next_banker_id = $readyUser_ary[0];

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

                //扣房卡
                $ticket_checked_user = $this->queryTicketCheckedUser($room_id);
                $to_check_user_array = array_diff($readyUser_ary, $ticket_checked_user);
                if (count($to_check_user_array) > 0) {

                    $spend_ticket_count = isset($setting[Redis_Const::Room_Field_TicketCount]) ? $setting[Redis_Const::Room_Field_TicketCount] : 1;

                    foreach ($to_check_user_array as $account_id) {

                        if (Config::Ticket_Mode == 1) {
                            //扣除房卡
                            $this->balanceTicket($room_id, $account_id, $spend_ticket_count);
                        }
                        $mkv[$account_id] = 1;
                    }

                    $TicketChecked_Key = strtr(Redis_Const::TicketChecked_Key, $replyArr);
                    $Redis_Model->hmsetField($TicketChecked_Key, $mkv);
                }


                $Chip_Key          = strtr(Redis_Const::Chip_Key, $replyArr);
                $SeenCard_Key      = strtr(Redis_Const::SeenCard_Key, $replyArr);
                $Card_Key          = strtr(Redis_Const::Card_Key, $replyArr);
                $PlayMember_Key    = strtr(Redis_Const::PlayMember_Key, $replyArr);
                $AccountStatus_Key = strtr(Redis_Const::AccountStatus_Key, $replyArr);

                //删除每局玩家筹码hash
                $Redis_Model->deleteKey($Chip_Key);
                //删除每局玩家看牌标识hash
                $Redis_Model->deleteKey($SeenCard_Key);
                //删除每局玩家手牌hash
                $Redis_Model->deleteKey($Card_Key);


                $Chip_mkv       = array();
                $Card_mkv       = array();
                $play_member    = array();  //游戏玩家队列
                $RoomStatus_mvk = array();

                $game_type = $this->queryGameType($room_id);
                $isBigCard = Game::Big_Flower == $game_type ? TRUE : FALSE;

                $readyUserCount = count($readyUser_ary);  //游戏中玩家数量
                $debug = getenv("DEBUG_MODE");
                if($debug == "1") {
                    $player_cards = $this->dealTestCard($readyUserCount,$isBigCard);  //发牌
                }else {
                    $player_cards   = $this->dealCard($readyUserCount, $isBigCard);  //发牌
                }

                for ($i = 0; $i < $readyUserCount; $i++) {
                    $account_id = $readyUser_ary[$i];
                    //玩家状态变成闷牌状态
                    $RoomStatus_mvk[$account_id] = Game::AccountStatus_Invisible;

                    //设置每局玩家手牌hash
                    $Card_mkv[$account_id] = $player_cards[$i];
                }

                //设置每局玩家顺序list
                $play_member = $readyUser_ary;

                //$mset_result = $Redis_Model->hmsetField($Chip_Key,$Chip_mkv);
                $mset_result = $Redis_Model->hmsetField($Card_Key, $Card_mkv);
                $mset_result = $Redis_Model->hmsetField($AccountStatus_Key, $RoomStatus_mvk);

                //删除每局玩家顺序list 再装入本局玩家
                $Redis_Model->deleteKey($PlayMember_Key);
                $push_result = $Redis_Model->pushList($is_rpush = 0, $is_pushx = 0, $PlayMember_Key, $play_member); //lpush

                //重设每局游戏参数
                $chip_type = isset($setting[Redis_Const::Room_Field_ChipType]) ? explode(",", $setting[Redis_Const::Room_Field_ChipType]) : array(2, 4, 5, 8);
                asort($chip_type);
                $parameter_ary[Redis_CONST::Play_Field_PoolScore]  = 0;// Game::Default_Score * $ready_count;
                $parameter_ary[Redis_CONST::Play_Field_Benchmark]  = $chip_type[0] < 4 ? Game::Default_Score : 10;
                $parameter_ary[Redis_CONST::Play_Field_ActiveUser] = $next_banker_id;

                $mset_result = $Redis_Model->hmsetField($Play_Key, $parameter_ary);

                $Room_mkv[Redis_CONST::Room_Field_Status] = 2;  //房间状态，1等待、2进行中、3关闭
                $Room_mkv[Redis_CONST::Room_Field_Banker] = $next_banker_id;
                $mset_result                              = $Redis_Model->hmsetField($Room_Key, $Room_mkv);

                //房间轮数与局数更新
                $this->updateGameNumberRound($room_id);

                //底注
                foreach ($readyUser_ary as $account_id) {
                    $default_score = isset($setting[Redis_Const::Room_Field_DefaultScore])?$setting[Redis_Const::Room_Field_DefaultScore] : Game::Default_Score;
                    $this->balanceScore($room_id, $account_id, $default_score);
                }

                $player_status = array();
                foreach ($play_member as $player_id) {
                    $chip     = $this->queryChip($room_id, $player_id);
                    $can_look = $this->seenChipConditon($room_id);
                    if ($player_id == $next_banker_id) {
                        $player_status[] = array(
                            "account_id"     => $player_id,
                            "account_status" => Game::AccountStatus_Invisible,         //5; 初始是闷牌状态
                            "online_status"  => $this->queryOnlineStatus($room_id, $player_id),
                            "playing_status" => Game::PlayingStatus_Betting, //2;  下注中
                            "limit_time"     => Game::LimitTime_Betting,
                            'can_open'       => 0,
                            'can_look'       => $can_look,
                            'chip'           => $chip
                        );
                    } else {
                        $player_status[] = array(
                            "account_id"     => $player_id,
                            "account_status" => Game::AccountStatus_Invisible,         //5; 初始是闷牌状态
                            "online_status"  => $this->queryOnlineStatus($room_id, $player_id),
                            "playing_status" => Game::PlayingStatus_Waiting, //1 等待别人中
                            "limit_time"     => 0,
                            'chip'           => $chip
                        );
                    }
                }

                //设置自动弃牌定时器
                $this->setupDiscardPassiveTimer($room_id, $next_banker_id);

                //推送开始
                $arr = array("result" => 0, "operation" => "GameStart", "data" => $player_status, "result_message" => "游戏开始了");
                $this->pushMessageToGroup($room_id, $arr);

            } else {
                if ($this->getTimerTime($room_id) == -1) {
                    // $this->queryTimerId($room_id) == -1   只有第二个人的准备才触发倒计时   $ready_count == 2 ||
                    $this->setupStartGamePassiveTimer($room_id);
                }
            }
        }

        return TRUE;
    }


    //设置 自动弃牌 定时器
    protected function setupDiscardPassiveTimer($room_id, $account_id) {

        $callback_array = array(
            'operation' => "DiscardPassive",
            'room_id'   => $room_id,
            'data'      => array(
                'account_id' => $account_id
            )
        );
        $arr            = array(
            'operation' => "BuildTimer",
            'room_id'   => $room_id,
            'data'      => array(
                'limit_time'     => Game::LimitTime_Betting,
                'callback_array' => $callback_array
            )
        );
        $this->sendToTimerServer($arr);

        $this->setTimerTime($room_id);    //分开、提前设置时间
    }

    //设置 自动开局 定时器
    protected function setupStartGamePassiveTimer($room_id) {

        $limit_time = $this->queryGameNumber($room_id) > 0 ? Game::LimitTime_Ready : Game::LimitTime_StartGame;

        $callback_array = array(
            'operation' => "StartGamePassive",
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

        $this->setTimerTime($room_id);    //分开、提前设置时间

        $arr = array("result" => 0, "operation" => "StartLimitTime", "data" => array('limit_time' => $limit_time), "result_message" => "开始倒计时");
        $this->pushMessageToGroup($room_id, $arr);
    }


    //设置 清理房间 定时器
    protected function setupClearRoomPassiveTimer($room_id) {

        $this->writeLog("[$room_id] 设置自动清扫房间定时器");
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

        //$this->setTimerTime($room_id);	//分开、提前设置时间   (这个清扫定时器就暂不设置时间了)
    }


    //删除定时器
    protected function deleteRoomTimer($room_id) {

        $timer_id = $this->queryTimerId($room_id);
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

    protected function setTimerId($room_id, $timer_id) {
        $Redis_Model                          = Redis_Model::getModelObject();
        $replyArr                             = array("[roomid]" => $room_id);
        $Play_Key                             = strtr(Redis_Const::Play_Key, $replyArr);
        $mkv[Redis_Const::Play_Field_TimerId] = $timer_id;
        $mset_result                          = $Redis_Model->hmsetField($Play_Key, $mkv);
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

    //更新用户状态
    protected function updateAccountStatus($room_id, $account_id, $status) {

        $Redis_Model       = Redis_Model::getModelObject();
        $replyArr          = array("[roomid]" => $room_id);
        $AccountStatus_Key = strtr(Redis_Const::AccountStatus_Key, $replyArr);

        $mkv[$account_id] = $status;
        $mset_result      = $Redis_Model->hmsetField($AccountStatus_Key, $mkv);

        //广播用户状态改变
        $noty_arr = array(
            'account_id'     => $account_id,
            'account_status' => $status,
            'online_status'  => $this->queryOnlineStatus($room_id, $account_id)
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
                if ($status == 1) {
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

    //获取用户是否已经看牌
    protected function querySeenCard($room_id, $account_id) {

        $Redis_Model  = Redis_Model::getModelObject();
        $replyArr     = array("[roomid]" => $room_id);
        $SeenCard_Key = strtr(Redis_Const::SeenCard_Key, $replyArr);
        $result       = $Redis_Model->hgetField($SeenCard_Key, $account_id);
        return $result == 1 ? 1 : 0;
    }

    //获取用户是否已经扣除房卡
    protected function queryTicketChecked($room_id, $account_id) {

        $Redis_Model       = Redis_Model::getModelObject();
        $replyArr          = array("[roomid]" => $room_id);
        $TicketChecked_Key = strtr(Redis_Const::TicketChecked_Key, $replyArr);
        $result            = $Redis_Model->hgetField($TicketChecked_Key, $account_id);
        return $result == 1 ? 1 : 0;
    }

    //获取用户积分
    protected function queryAccountScore($room_id, $account_id) {

        $Redis_Model   = Redis_Model::getModelObject();
        $replyArr      = array("[roomid]" => $room_id);
        $RoomScore_Key = strtr(Redis_Const::RoomScore_Key, $replyArr);

        $result = $Redis_Model->hgetField($RoomScore_Key, $account_id);
        return $result;
    }

    //下注平衡积分
    protected function balanceScore($room_id, $account_id, $score) {

        $Redis_Model   = Redis_Model::getModelObject();
        $replyArr      = array("[roomid]" => $room_id);
        $RoomScore_Key = strtr(Redis_Const::RoomScore_Key, $replyArr);
        $Play_Key      = strtr(Redis_Const::Play_Key, $replyArr);
        $Chip_Key      = strtr(Redis_Const::Chip_Key, $replyArr);

        $mset_result = $Redis_Model->hincrbyField($RoomScore_Key, $account_id, 0 - $score); //总分 减少
        $mset_result = $Redis_Model->hincrbyField($Play_Key, Redis_Const::Play_Field_PoolScore, $score);    //分数池 增加
        $mset_result = $Redis_Model->hincrbyField($Chip_Key, $account_id, $score);
    }

    //获胜平衡积分  account_id 下注者
    protected function balanceWinnerScore($room_id, $winners, $account_id = -1) {

        $winner_score_dict = array();
        $count             = count($winners);
        if ($count < 1) {
            return array();
        }

        $Redis_Model   = Redis_Model::getModelObject();
        $replyArr      = array("[roomid]" => $room_id);
        $RoomScore_Key = strtr(Redis_Const::RoomScore_Key, $replyArr);
        $Play_Key      = strtr(Redis_Const::Play_Key, $replyArr);
        $Chip_Key      = strtr(Redis_Const::Chip_Key, $replyArr);

        $pool_score   = $Redis_Model->hgetField($Play_Key, Redis_Const::Play_Field_PoolScore);
        $per_score    = intval($pool_score / $count);
        $remain_score = $pool_score - $per_score * ($count - 1);

        foreach ($winners as $winner_id) {
            if ($winner_id == $account_id) {
                $win_score = $remain_score;
            } else {
                $win_score = $per_score;
            }
            $Redis_Model->hincrbyField($RoomScore_Key, $winner_id, $win_score); //个人总分 增加
            $winner_score_dict[$winner_id] = $win_score;
        }
        $Redis_Model->hmsetField($Play_Key, array(Redis_Const::Play_Field_PoolScore => 0));    //分数池 清零

        return $winner_score_dict;
    }

    /**
     * 函数描述： 平摊喜牌分数,
     * @param $room_id
     * @param $joycard_arr 喜牌的赢家
     * @param $joyscore    喜牌分数
     * @return array 喜牌分摊结果,
     *                     author 黄欣仕
     *                     date 2018/12/28
     */
    function balanceJoycardScore($room_id, $joycard_arr, $joyscore) {
        $Redis_Model   = Redis_Model::getModelObject();
        $replyArr      = array("[roomid]" => $room_id);
        $Chip_Key      = strtr(Redis_Const::Chip_Key, $replyArr);
        $chip_array    = $Redis_Model->hgetallField($Chip_Key);
        $RoomScore_Key = strtr(Redis_Const::RoomScore_Key, $replyArr);
        shuffle($joycard_arr);
        $joycard_arr_flip = array_flip($joycard_arr);
        $loser_arr        = array_diff_key($chip_array, $joycard_arr_flip);
        $score_count      = 0;

        $balance_joy_card_score = array();
        foreach ($loser_arr as $account_id => $value) {
            $balance_joy_card_score[$account_id] = 0 - $joyscore;
            $this->balanceScore($room_id, $account_id, $joyscore);
            $score_count += $joyscore;
        }

        $count          = count($joycard_arr_flip);
        $quotient_score = intval($score_count / $count);
        $remain_score   = $score_count % $count;
        $this->writeLog("flip:" . json_encode($joycard_arr_flip));
        foreach ($joycard_arr_flip as $account_id => $value) {
            if ($remain_score > 0) {
                $inc = $quotient_score + 1;
                $remain_score--;
            } else {
                $inc = $quotient_score;
            }

            $balance_joy_card_score[$account_id] = $inc;
        }
        return $balance_joy_card_score;
    }

    protected function caculateCardValue($room_id, $player_id) {
        $Redis_Model = Redis_Model::getModelObject();
        $replyArr    = array("[roomid]" => $room_id);
        $Card_Key    = strtr(Redis_Const::Card_Key, $replyArr);
        $card_str    = $Redis_Model->hgetField($Card_Key, $player_id);

        $cards = explode(",", $card_str);
        $value = $this->_cardValue($cards);
        return $value;
    }

    protected function _cardValue($cards) {

        if (count($cards) != 3) {
            return 0;
        }
        $value  = 0;
        $suits  = array();
        $points = array();
        foreach ($cards as $card) {
            $suits[]  = substr($card, 0, 1);
            $points[] = substr($card, 1);
        }

        //A替换成数值14
        for ($i = 0; $i < count($points); $i++) {
            if ($points[$i] == 1) {
                $points[$i] = 14;
            }
        }

        rsort($points, SORT_NUMERIC);  //大到小排序
        $points_val_str = str_pad($points[0], 2, "0", STR_PAD_LEFT) . str_pad($points[1], 2, "0", STR_PAD_LEFT) . str_pad($points[2], 2, "0", STR_PAD_LEFT);
        //牌型  0未知(因别人弃牌而胜) 1高牌 2对子 3顺子 4同花 5同花顺 6三条
        if ($points[0] == $points[2]) { //三条
            $value = Game::CartType_Santiao . $points_val_str;
        } else if ($points[0] == $points[1]) { //对子
            $value = Game::CartType_Duizi . $points_val_str;
        } else if ($points[1] == $points[2]) { //对子
            $value = Game::CartType_Duizi . str_pad($points[2], 2, "0", STR_PAD_LEFT) . str_pad($points[1], 2, "0", STR_PAD_LEFT) . str_pad($points[0], 2, "0", STR_PAD_LEFT);
        } else if ($points[0] == $points[2] + 2) { //顺子
            $card_type = ($suits[0] == $suits[1] && $suits[1] == $suits[2]) ? Game::CartType_Tonghuashun : Game::CartType_Shunzi;
            $value     = $card_type . $points_val_str;
        } else if ($points[0] == 14 && $points[1] == 3 && $points[2] == 2) { //顺子
            $card_type = ($suits[0] == $suits[1] && $suits[1] == $suits[2]) ? Game::CartType_Tonghuashun : Game::CartType_Shunzi;
            $value     = $card_type . "010203";
        } else if ($suits[0] == $suits[1] && $suits[1] == $suits[2]) {  //同花
            $value = Game::CartType_Tonghua . $points_val_str;
        } else {    //高牌
            $value = Game::CartType_Gaopai . $points_val_str;
        }
        return $value;
    }

    protected function queryCardInfo($room_id, $player_id) {
        $Redis_Model = Redis_Model::getModelObject();
        $replyArr    = array("[roomid]" => $room_id);
        $Card_Key    = strtr(Redis_Const::Card_Key, $replyArr);
        $card_str    = $Redis_Model->hgetField($Card_Key, $player_id);
        $this->writeLog("[$room_id] ($player_id) 手牌:" . $card_str);
        if (Redis_CONST::DATA_NONEXISTENT === $card_str || "" == trim($card_str)) {  //异常状态返回空数组
            $this->writeLog("function(queryCardInfo):异常状态返回空数组" . " in file" . __FILE__ . " on Line " . __LINE__);
            return array();
        }
        $cards = explode(",", $card_str);
        sort($cards);
        return $cards;
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

    //获取房主id
    protected function queryCreator($room_id) {
        $Redis_Model = Redis_Model::getModelObject();
        $replyArr    = array("[roomid]" => $room_id);
        $Room_Key    = strtr(Redis_Const::Room_Key, $replyArr);
        $result      = $Redis_Model->hgetField($Room_Key, Redis_Const::Room_Field_Creator);
        return $result;
    }

    //获取激活的操作中的用户
    protected function queryActiveUser($room_id) {
        $Redis_Model = Redis_Model::getModelObject();
        $replyArr    = array("[roomid]" => $room_id);
        $Play_Key    = strtr(Redis_Const::Play_Key, $replyArr);
        $result      = $Redis_Model->hgetField($Play_Key, Redis_Const::Play_Field_ActiveUser);
        return $result > 0 ? $result : -1;
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

    //轮流
    protected function takeTurns($room_id) {
        $Redis_Model    = Redis_Model::getModelObject();
        $replyArr       = array("[roomid]" => $room_id);
        $PlayMember_Key = strtr(Redis_Const::PlayMember_Key, $replyArr);
        $result         = $Redis_Model->rotationList($PlayMember_Key);

        $list = $Redis_Model->lrangeList($PlayMember_Key, -1, -1);  //队列最右边是当前操作用户
        if ($list) {
            $account_id = $list[0];
        } else {
            $account_id = -1;
        }

        $Play_Key                                = strtr(Redis_Const::Play_Key, $replyArr);
        $mkv[Redis_Const::Play_Field_ActiveUser] = $account_id;

        $mset_result = $Redis_Model->hmsetField($Play_Key, $mkv);

        return $account_id;
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


    //是否经过了第一轮下注，经过了才能比牌
    protected function pkChipConditon($room_id, $account_id) {

        $Redis_Model = Redis_Model::getModelObject();
        $replyArr    = array("[roomid]" => $room_id);

        $setting = $this->queryRoomSetting($room_id);

        $min_score_to_pk = isset($setting[Redis_Const::Room_Field_MinScore_PK]) ? $setting[Redis_Const::Room_Field_MinScore_PK] : 0;
        if ($min_score_to_pk && $this->queryPoolScore($room_id) < $min_score_to_pk) {
            return 0;
        }

        $first_round_ban_pk = isset($setting[Redis_Const::Room_Field_First_Round_Ban_PK]) ? $setting[Redis_Const::Room_Field_First_Round_Ban_PK] : 0;
        if ($first_round_ban_pk == 0) {
            $Chip_Key      = strtr(Redis_Const::Chip_Key, $replyArr);
            $betScore      = $Redis_Model->hgetField($Chip_Key, $account_id);
            $default_score = isset($setting[Redis_Const::Room_Field_DefaultScore]) ? $setting[Redis_Const::Room_Field_DefaultScore] : Game::Default_Score;
            if ($betScore <= $default_score) {
                return 0;
            }
        }
        return 1;
    }

    /**
     * 函数描述：当前房间是否可以开始看牌
     * @param $room_id
     * @return int 0 不能看牌 1可以看牌
     * author 黄欣仕
     * date 2018/12/27
     */
    protected function seenChipConditon($room_id) {
        $setting           = $this->queryRoomSetting($room_id);
        $min_score_to_seen = isset($setting[Redis_Const::Room_Field_MinScore_Seen]) ? $setting[Redis_Const::Room_Field_MinScore_Seen] : 0;
        if ($min_score_to_seen && $this->queryPoolScore($room_id) < $min_score_to_seen) {
            return 0;
        }

        return 1;
    }

    //通知用户下注
    protected function notyUserToBet($room_id, $account_id) {

        $can_open = 0;
        $pk_user  = array();
        if ($this->pkChipConditon($room_id, $account_id)) {
            $pk_user = $this->queryPkUser($room_id, $account_id);
            if (count($pk_user) > 0) {
                $can_open = 1;
            }
        }
        $can_look = $this->seenChipConditon($room_id);

        //$this->writeLog("轮到用户".$account_id. "下注 (can_open:". $can_open .")");

        //设置自动弃牌定时器
        $this->setupDiscardPassiveTimer($room_id, $account_id);

        $chip = $this->queryChip($room_id, $account_id);

        $noty_arr = array(
            'account_id'     => $account_id,
            'playing_status' => Game::PlayingStatus_Betting,
            'limit_time'     => Game::LimitTime_Betting,
            'can_open'       => $can_open,
            'can_look'       => $can_look,
            'pk_user'        => $pk_user,
            'chip'           => $chip
        );
        $msg_arr  = array("result" => "0", "operation" => "NotyChooseChip", "data" => $noty_arr, "result_message" => "通知下注");
        $this->pushMessageToGroup($room_id, $msg_arr);
    }

    public function queryPkUser($room_id, $account_id) {
        $Redis_Model    = Redis_Model::getModelObject();
        $replyArr       = array("[roomid]" => $room_id);
        $PlayMember_Key = strtr(Redis_Const::PlayMember_Key, $replyArr);
        $players        = $Redis_Model->lrangeList($PlayMember_Key);

        $other_players = array();
        foreach ($players as $player_id) {
            if ($player_id != $account_id) {
                $other_players[] = $player_id;
            }
        }
        $other_count = count($other_players);
        if ($other_count == 1) {
            $pk_user = $other_players;
        } else {
            $pk_user = array();
            foreach ($other_players as $player_id) {
                $status = $this->queryAccountStatus($room_id, $player_id);
                if ($status == Game::AccountStatus_Visible) {    //对方是看牌状态才能比牌
                    $pk_user[] = $player_id;
                }
            }
            if (count($pk_user) < $other_count) { //有人闷牌
                $setting        = $this->queryRoomSetting($room_id);
                $disable_pk_men = isset($setting[Redis_Const::Room_Field_DisablePkMen]) ? $setting[Redis_Const::Room_Field_DisablePkMen] : 0;
                if ($disable_pk_men) {
                    $pk_user = array();
                }
            }
        }
        return $pk_user;
    }

    /*
        一局输赢结果
        win_type 结局   1开牌   2弃牌    3封顶自动开牌
    */

    public function showWin($room_id, $account_id, $next_account_id, $win_type) {
        $Redis_Model = Redis_Model::getModelObject();

        $replyArr       = array("[roomid]" => $room_id);
        $PlayMember_Key = strtr(Redis_Const::PlayMember_Key, $replyArr);
        $players        = $Redis_Model->lrangeList($PlayMember_Key);
        $Room_Key       = strtr(Redis_Const::Room_Key, $replyArr);

        $player_cards       = array();  //索引数组
        $winner_score_dict  = array();
        $joycard_score_dict = array();

        $extra_rewards = $Redis_Model->hgetField($Room_Key, Redis_CONST::Room_Field_Extra_Rewards);

        if (1 == $win_type) {    //开牌结局
            $cards1 = $this->queryCardInfo($room_id, $account_id);
            $value1 = $this->_cardValue($cards1);

            $cards2 = $this->queryCardInfo($room_id, $next_account_id);
            $value2 = $this->_cardValue($cards2);

            if ($value1 > $value2) {
                $winner_id = $account_id;
                $card_type = substr($value1, 0, 1);
            } else {
                $winner_id = $next_account_id;
                $card_type = substr($value2, 0, 1);
            }

            $player_cards[] = array(
                'account_id' => $account_id,
                'cards'      => $cards1
            );
            $player_cards[] = array(
                'account_id' => $next_account_id,
                'cards'      => $cards2
            );

            if ($winner_id > 0) {
                if ($extra_rewards > 0 and $card_type == Game::CartType_Tonghuashun or $card_type == Game::CartType_Santiao) {
                    $joycard_score_dict = $this->balanceJoycardScore($room_id, [$winner_id], $extra_rewards);
                }
                $winner_score_dict = $this->balanceWinnerScore($room_id, [$winner_id], $account_id);
            }

        } else if (2 == $win_type) {    //弃牌结局
            $winner_id      = ($next_account_id != -1) ? $next_account_id : $account_id;
            $cards          = $this->queryCardInfo($room_id, $winner_id);
            $value          = $this->_cardValue($cards);
            $card_type      = substr($value, 0, 1);
            $player_cards[] = array(
                'account_id' => $winner_id,
                'cards'      => $cards
            );

            if ($winner_id > 0) {
                if ($extra_rewards > 0 and $card_type == Game::CartType_Tonghuashun or $card_type == Game::CartType_Santiao) {
                    $joycard_score_dict = $this->balanceJoycardScore($room_id, [$winner_id], $extra_rewards);
                }
                $winner_score_dict = $this->balanceWinnerScore($room_id, [$winner_id], $account_id);
            }
        } else { //封顶自动开牌
            $winners          = array();
            $players          = $Redis_Model->lrangeList($PlayMember_Key);
            $card_value_array = array();  //关联数组
            $max_value        = 0;
            foreach ($players as $player_id) {

                $cards                        = $this->queryCardInfo($room_id, $player_id);
                $value                        = $this->_cardValue($cards);
                $card_value_array[$player_id] = $value;
                if ($value > $max_value) {
                    $max_value = $value;
                    $card_type = substr($max_value, 0, 1);
                }
                //手牌
                $player_cards[] = array(
                    'account_id' => $player_id,
                    'cards'      => $cards
                );
            }

            $this->writeLog("[$room_id] choose Winner:");
            foreach ($card_value_array as $winner_id => $value) {
                $this->writeLog("[$room_id] " . $winner_id . " => " . $value);
                if ($value == $max_value) {
                    $winners[] = $winner_id;
                    $this->writeLog("[$room_id] --------------");
                }
            }

            $card_type = substr($max_value, 0, 1);
            if ($extra_rewards > 0 and $card_type == Game::CartType_Tonghuashun or $card_type == Game::CartType_Santiao) {
                $joycard_score_dict = $this->balanceJoycardScore($room_id, $winners, $extra_rewards);
            }
            $winner_score_dict = $this->balanceWinnerScore($room_id, $winners, $account_id);
        }

        if (count($winner_score_dict) > 0) {
            $RoomScore_Key = strtr(Redis_Const::RoomScore_Key, $replyArr);
            $scoreboard    = $Redis_Model->hgetallField($RoomScore_Key);

            //保存积分榜
            if (is_array($scoreboard)) {
                $ticket_checked_user = $this->queryTicketCheckedUser($room_id);
                foreach ($scoreboard as $account_id => $score) {
                    if (!in_array($account_id, $ticket_checked_user)) {
                        //未扣房卡的用户不显示在积分榜上
                        unset($scoreboard[$account_id]);
                    }
                }
                $board_json_str = json_encode($scoreboard);

            } else {
                $scoreboard     = array();
                $board_json_str = "";
            }
            $mkv[Redis_Const::Room_Field_Scoreboard] = $board_json_str;
            $Redis_Model->hmsetField($Room_Key, $mkv);


            $start_time = $this->queryStartTime($room_id);
            $game_num   = $this->queryGameNumber($room_id);
            $total_num  = $this->queryTotalNum($room_id);
            $msg_arr    = array(
                'result'         => 0,
                'operation'      => 'Win',
                'result_message' => "获胜+积分榜",
                'data'           => array(
                    'player_cards'       => $player_cards,
                    'card_type'          => $card_type,
                    'score_board'        => $scoreboard,
                    'game_num'           => $game_num,
                    'total_num'          => $total_num,
                    'winner_score_dict'  => $winner_score_dict,
                    'joycard_score_dict' => $joycard_score_dict
                )
            );

            $this->pushMessageToGroup($room_id, $msg_arr);

            //保存当局游戏结果
            $setting = $this->queryRoomSetting($room_id);
            $round   = $setting[Redis_Const::Room_Field_GameRound];

            $game_info['room_id'] = $room_id;
            //			$game_info['game_type'] = Game::Game_Type;
            $game_info['game_type']  = $this->queryGameType($room_id);
            $game_info['dealer_num'] = Config::Dealer_Num;
            $game_info['round']      = $round;
            $game_info['game_num']   = $game_num;
            $game_info['total_num']  = $total_num;
            $game_info['extra']      = "";
            $this->saveGameResult($game_info, $winner_score_dict);


            $this->updateRoomStatus($room_id, 1);   //房间状态，1等待、2进行中、3关闭
            if ($game_num == $total_num) {  //最后一局

                $MMYSQL = $this->initMysql();

                $round = $Redis_Model->hincrbyField($Room_Key, Redis_Const::Room_Field_GameRound, 1);
                $this->writeLog("[$room_id] 第" . ($round - 1) . "轮 结束!");

                $name_board = array();
                foreach ($scoreboard as $account_id => $score) {
                    $account_sql  = 'select nickname from ' . WX_Account . ' where account_id =' . $account_id;
                    $name         = $MMYSQL->single($account_sql);
                    $name_board[] = array('name' => $name, 'score' => $score, 'account_id' => $account_id);

                    $account_array               = [];
                    $account_array['account_id'] = $account_id;
                    $account_array['room_id']    = $room_id;
                    $account_array['game_type']  = $this->queryGameType($room_id);
                    $account_array['score']      = $score;
                    $account_array['over_time']  = time();

                    $MMYSQL->insertReturnID(Room_Account, $account_array);
                }

                //规则文本
                $rule_text              = $this->formatRuleText($room_id);
                $balance_scoreboard     = array('time' => time(), 'scoreboard' => $name_board, 'game_num' => $game_num);
                $balance_board_json_str = json_encode($balance_scoreboard['scoreboard']);
                //保存积分榜
                $board_array['start_time']    = $start_time;
                $board_array['create_time']   = time();
                $board_array['is_delete']     = G_CONST::IS_FALSE;
                $board_array['game_type']     = $this->queryGameType($room_id);  //游戏类型
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

                $this->resetAllAccountStatus($room_id);  //重设所有用户状态为未准备

                $Play_Key       = strtr(Redis_Const::Play_Key, $replyArr);
                $PlayMember_Key = strtr(Redis_Const::PlayMember_Key, $replyArr);
                $Card_Key       = strtr(Redis_Const::Card_Key, $replyArr);
                $Chip_Key       = strtr(Redis_Const::Chip_Key, $replyArr);
                $SeenCard_Key   = strtr(Redis_Const::SeenCard_Key, $replyArr);
                //删除每局游戏参数
                $Redis_Model->deleteKey($Play_Key);
                $Redis_Model->deleteKey($PlayMember_Key);
                $Redis_Model->deleteKey($Card_Key);
                $Redis_Model->deleteKey($Chip_Key);
                $Redis_Model->deleteKey($SeenCard_Key);
            }

        } else {
            $this->logMessage('error', "数据错误:function(openCard):have no winner!");
        }
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
        $SeenCard_Key   = strtr(Redis_Const::SeenCard_Key, $replyArr);

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
        $Redis_Model->deleteKey($SeenCard_Key);

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
            $MMYSQL->update(Room)->set('is_close', '1')->set('is_delete', ($is_delete ? '1' : '0'))->where('room_id=' . $room_id)->query();

            $Redis_Model->deleteKey($Room_Key);
        }
        $this->writeLog("[$room_id] 房间清理完毕!");
    }

    //整理规则内容文本
    protected function formatRuleText($room_id, $room_data = "") {
        $rule_text          = "";
        $setting            = $this->queryRoomSetting($room_id);
        $ticket_count       = isset($setting[Redis_Const::Room_Field_TicketCount]) ? $setting[Redis_Const::Room_Field_TicketCount] : 1;
        $chip_type          = isset($setting[Redis_Const::Room_Field_ChipType]) ? explode(",", $setting[Redis_Const::Room_Field_ChipType]) : array(2, 4, 5, 8);
        $compare_progress   = isset($setting[Redis_Const::Room_Field_MinScore_PK]) ? $setting[Redis_Const::Room_Field_MinScore_PK] : 0;
        $seen_progress      = isset($setting[Redis_Const::Room_Field_MinScore_Seen]) ? $setting[Redis_Const::Room_Field_MinScore_Seen] : 0;
        $first_round_ban_pk = isset($setting[Redis_Const::Room_Field_First_Round_Ban_PK]) ? $setting[Redis_Const::Room_Field_First_Round_Ban_PK] : FALSE;
        $disable_pk_men     = isset($setting[Redis_Const::Room_Field_DisablePkMen]) ? $setting[Redis_Const::Room_Field_DisablePkMen] : 0;
        $upper_limit        = isset($setting[Redis_Const::Room_Field_UpperLimit]) ? $setting[Redis_Const::Room_Field_UpperLimit] : 1000;
        $extra_rewards      = isset($setting[Redis_Const::Room_Field_Extra_Rewards]) ? $setting[Redis_Const::Room_Field_Extra_Rewards] : 0;
        $default_score      = isset($setting[Redis_CONST::Room_Field_DefaultScore]) ? $setting[Redis_CONST::Room_Field_DefaultScore] : Game::Default_Score;

        //局数房卡规则
        $ticket_type_text = (Config::GameNum_EachRound * $ticket_count) . "局/" . $ticket_count . "张房卡";
        $rule_text        .= $ticket_type_text;

        // 模式
        //        $rule_text .= 2 == $mode ? ',大牌金花' : ',经典金花';

        //筹码类型
        $rule_text .= ",";

        $chip_str  = array_reduce($chip_type, function ($key, $val) {
            return $val . "/" . 2 * $val . ",";
        });
        $rule_text .= "筹码:" . $chip_str;

        $rule_text .= "底分" . $default_score;

        if ($first_round_ban_pk) {
            $rule_text .= ",首轮允许比牌";
        } else {
            $rule_text .= ",首轮禁止比牌";
        }


        if ($compare_progress > 0) {
            $rule_text .= ",积分池" . $compare_progress . "分以下不能比牌";
        }

        if ($seen_progress > 0) {
            $rule_text .= ",积分池" . $seen_progress . "分以下不能看牌";
        }

        if ($disable_pk_men) {
            $rule_text .= ",闷牌禁止比牌";
        }

        if ($extra_rewards > 0) {
            $rule_text .= ",喜牌:" . $extra_rewards . "分";
        } else {
            $rule_text .= ",喜牌:无";
        }

        //封顶上限
        if ($upper_limit > 0) {
            $rule_text .= ",上限:" . $upper_limit . "分";
        } else {
            $rule_text .= ",上限:无";
        }

        return $rule_text;
    }


    /*
        保存炸金花游戏结果
    */
    protected function saveGameResult($game_info, $winner_score_dict) {
        $timestamp  = time();
        $MMYSQL     = $this->initMysql();
        $room_id    = $game_info['room_id'];
        $game_type  = $game_info['game_type'];
        $dealer_num = $game_info['dealer_num'];

        $game_data['time'] = time();
        $game_data['rid']  = $game_info['room_id'];
        $game_data['rnd']  = $game_info['round'];
        $game_data['gnum'] = $game_info['game_num'];
        $game_data['tnum'] = $game_info['total_num'];

        //获取所有人手牌
        $Redis_Model = Redis_Model::getModelObject();
        $replyArr    = array("[roomid]" => $room_id);
        $Card_Key    = strtr(Redis_Const::Card_Key, $replyArr);
        $card_array  = $Redis_Model->hgetallField($Card_Key);

        $player_array = array();    //玩家当局数据

        $Chip_Key   = strtr(Redis_Const::Chip_Key, $replyArr);
        $chip_array = $Redis_Model->hgetallField($Chip_Key);

        $room_members = $this->queryRoomMembers($room_id);

        foreach ($room_members as $player_id) {
            if (!isset($card_array[$player_id])) {
                continue;
            }
            $array['p'] = $player_id;
            $array['s'] = isset($chip_array[$player_id]) ? $chip_array[$player_id] : 0;
            $array['c'] = $card_array[$player_id];
            $array['w'] = -$array['s'];// 默认输了下注的分数
            if (!empty($winner_score_dict)) {
                foreach ($winner_score_dict as $winner_id => $winner_score) {
                    if ($player_id == $winner_id) {
                        $array['w'] = $winner_score - $array['s']; // 如果是赢家则赢了 得分-下注 的分数
                    }
                }
            }

            $card_type = "1";
            $value     = $this->caculateCardValue($room_id, $player_id);
            $card_type = substr($value, 0, 1);

            $array['ct'] = $card_type;

            //获取用户当前名字
            $account_sql = 'select nickname from ' . WX_Account . ' where account_id =' . $player_id;
            $name        = $MMYSQL->single($account_sql);
            $array['n']  = $name;

            $player_array[] = $array;
            unset($array);
        }

        $save_game_result['pAry']  = $player_array;
        $save_game_result['gData'] = $game_data;
        $save_game_result_json     = json_encode($save_game_result);

        $round                = $game_data['rnd'];
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

    //发牌 特殊牌形专用测试
    protected function dealTestCard($player_count, $isBigCard = FALSE) {

        $player_cards_test   = array();
        $player_cards = array();
        if($isBigCard) {
            $player_cards_test[] = "A1,B1,C1";     //黑桃A 红桃A 梅花A (豹子)
            $player_cards_test[] = "A10,B10,D10";  //黑桃10 红桃10 方块10 (豹子)
            $player_cards_test[] = "A11,A12,A13";  //黑桃J 黑桃Q 黑桃K (同花顺)
            $player_cards_test[] = "C10,C12,C13";  //梅花10 梅花Q 梅花K (顺子)
            $player_cards_test[] = "D12,B12,C11";   //方块Q 红桃Q 梅花J (对子)
            $player_cards_test[] = "D1,B11,B12";   //红桃J 红桃Q 方块A (高牌)
        } else {

            $player_cards_test[] = "A1,B1,C1";     //黑桃A 红桃A 梅花A (豹子)
            $player_cards_test[] = "A2,B2,D2";     //黑桃2 红桃2 方块2 (豹子)
            $player_cards_test[] = "C3,C4,C5";     //梅花3 梅花4 梅花5 (同花顺)
            $player_cards_test[] = "B3,B4,B5";    //红桃3 红桃4 红桃5  (同花顺)
            $player_cards_test[] = "A3,A5,A6";     //黑桃3 黑桃5 黑桃6 (同花)
            $player_cards_test[] = "D3,D5,D6";     //方块3 方块5 方块6 (同花)
            $player_cards_test[] = "A7,B8,A9";     //黑桃7 红桃8 黑桃9 (顺子)
            $player_cards_test[] = "B7,A8,B9";     //红桃7 黑桃8 红桃9 (顺子)
            $player_cards_test[] = "A10,B10,C9";   //黑桃10 红桃10 梅花9 (对子)
            $player_cards_test[] = "C10,D10,D9";   //梅花10 方块10 方块9 (对子)
            $player_cards_test[] = "A4,A11,C12";   //梅花10 方块10 方块9 (高牌)
            $player_cards_test[] = "B6,B11,D13";   //黑桃6  黑桃J  方块Q (高牌)

        }

        $count_card = count($player_cards_test) - 1;
        for ($i=0; $i < $player_count; $i++) {
            $tmp = $player_cards_test[rand(0, $count_card)];
            if(in_array($tmp,$player_cards)){
                $i--;
            }else{
                $player_cards[] = $tmp;
            }
        }
        return $player_cards;
    }

    //发牌 全随机规则
    protected function dealCard($player_count, $isBigCard = FALSE) {
        $player_cards = array();
        $allCard      = array("A1", "A2", "A3", "A4", "A5", "A6", "A7", "A8", "A9", "A10", "A11", "A12", "A13",
                              "B1", "B2", "B3", "B4", "B5", "B6", "B7", "B8", "B9", "B10", "B11", "B12", "B13",
                              "C1", "C2", "C3", "C4", "C5", "C6", "C7", "C8", "C9", "C10", "C11", "C12", "C13",
                              "D1", "D2", "D3", "D4", "D5", "D6", "D7", "D8", "D9", "D10", "D11", "D12", "D13"
        );
        $bigCard      = array(
            'A1', 'A10', 'A11', 'A12', 'A13',
            'B1', 'B10', 'B11', 'B12', 'B13',
            'C1', 'C10', 'C11', 'C12', 'C13',
            'D1', 'D10', 'D11', 'D12', 'D13'
        );
        $Card         = $isBigCard ? $bigCard : $allCard;
        shuffle($Card);
        $indexs = array_rand($Card, 3 * $player_count);
        shuffle($indexs);
        $num = 0;
        for ($i = 0; $i < $player_count; $i++) {
            $card_str = $Card[$indexs[$num]] . ',' . $Card[$indexs[$num + 1]] . ',' . $Card[$indexs[$num + 2]];

            $player_cards[] = $card_str;
            $num            += 3;
            $this->writeLog($card_str);
        }
        return $player_cards;
    }

    //发牌 全随机规则 + 特殊牌
    protected function dealCard_teshu($player_count) {

        $types = array();
        $r     = mt_rand(0, 59);
        if ($r < 59) {    //正常
            $player_cards = $this->dealCard($player_count);
            return $player_cards;
        } /*else if(0){	//特殊一  无高牌

			for ($i=0; $i < $player_count; $i++) {
			    $type = $this->random_card_type_without_gaopai();
			    $types[] = $type;
			}

		}*/ else if ($r == 59) {    //特殊二  至少有两份同花

            $types[] = 4;
            $types[] = 4;
            for ($i = 0; $i < $player_count - 2; $i++) {
                $type    = $this->random_card_type();
                $types[] = $type;
            }
        } else {
            $this->logMessage('error', "function(dealCard_teshu):发牌随机函数有问题" . " in file" . __FILE__ . " on Line " . __LINE__);
            $player_cards = $this->dealCard($player_count);
            return $player_cards;
        }
        return $this->_getCardByType($types);
    }

    //发牌 高概率规则
    protected function dealCardHighProbability($player_count) {

        $types = array();

        $r = mt_rand(0, 44);
        if ($r < 40) {    //正常
            for ($i = 0; $i < $player_count; $i++) {
                $type    = $this->random_card_type();
                $types[] = $type;
            }
        } else if ($r < 43) {    //特殊一  无高牌

            for ($i = 0; $i < $player_count; $i++) {
                $type    = $this->random_card_type_without_gaopai();
                $types[] = $type;
            }

        } else {    //特殊二  至少有两份同花

            $types[] = 4;
            $types[] = 4;
            for ($i = 0; $i < $player_count - 2; $i++) {
                $type    = $this->random_card_type();
                $types[] = $type;
            }
        }
        return $this->_getCardByType($types);
    }

    protected function _getCardByType($card_type) {
        $Card = array(
            ["A1", "A2", "A3", "A4", "A5", "A6", "A7", "A8", "A9", "A10", "A11", "A12", "A13"],
            ["B1", "B2", "B3", "B4", "B5", "B6", "B7", "B8", "B9", "B10", "B11", "B12", "B13"],
            ["C1", "C2", "C3", "C4", "C5", "C6", "C7", "C8", "C9", "C10", "C11", "C12", "C13"],
            ["D1", "D2", "D3", "D4", "D5", "D6", "D7", "D8", "D9", "D10", "D11", "D12", "D13"]
        );

        $player_cards = array();

        rsort($card_type);  //牌型降序
        echo "牌型降序:" . implode(',', $card_type) . PHP_EOL;
        foreach ($card_type as $type) {
            $choose = FALSE;
            switch ($type) {
                case 6:
                    //选三条
                    $point = mt_rand(0, 12);
                    $s     = array_rand(['A', 'B', 'C', 'D'], 3);
                    for ($i = 0; $i < 13; $i++) {
                        $p = ($point + $i) % 13;
                        if (isset($Card[$s[0]][$p]) && isset($Card[$s[1]][$p]) && isset($Card[$s[2]][$p])) {
                            $player_cards[] = $Card[$s[0]][$p] . ',' . $Card[$s[1]][$p] . ',' . $Card[$s[2]][$p];
                            unset($Card[$s[0]][$p]);
                            unset($Card[$s[1]][$p]);
                            unset($Card[$s[2]][$p]);
                            $choose = TRUE;
                            break;
                        }
                    }
                    if ($choose) {
                        break;
                    }

                case 5:
                    //选同花顺
                    $point = mt_rand(0, 12);
                    $suit  = mt_rand(0, 3);
                    for ($i = 0; $i < 13; $i++) {
                        if ($choose) {
                            break;  //只取一瓢
                        }
                        $p1 = ($point + $i) % 13;
                        $p2 = ($point + $i + 1) % 13;
                        $p3 = ($point + $i + 2) % 13;
                        if ($p1 == 12) {      //K 1 2 不是顺
                            continue;
                        }
                        for ($j = 0; $j < 4; $j++) {
                            $s = ($suit + $j) % 4;
                            if (isset($Card[$s][$p1]) && isset($Card[$s][$p2]) && isset($Card[$s][$p3])) {
                                $player_cards[] = $Card[$s][$p1] . ',' . $Card[$s][$p2] . ',' . $Card[$s][$p3];
                                unset($Card[$s][$p1]);
                                unset($Card[$s][$p2]);
                                unset($Card[$s][$p3]);
                                $choose = TRUE;
                                break;
                            }
                        }
                    }
                    if ($choose) {
                        break;
                    }

                case 4:
                    //选同花
                    $suit = mt_rand(0, 3);
                    for ($j = 0; $j < 4; $j++) {
                        if ($choose) {
                            break;  //只取一瓢
                        }
                        $s = ($suit + $j) % 4;
                        if (count($Card[$s]) > 3) {  //同花色牌大于3
                            for ($times = 0; $times < 10; $times++) {
                                $indexs = array_rand($Card[$s], 3);
                                $p1     = $indexs[0];
                                $p2     = $indexs[1];
                                $p3     = $indexs[2];
                                if (($p1 + 2 != $p3) && !(0 == $p1 && 11 == $p2 && 12 == $p3)) {    //非顺子
                                    $player_cards[] = $Card[$s][$p1] . ',' . $Card[$s][$p2] . ',' . $Card[$s][$p3];
                                    unset($Card[$s][$p1]);
                                    unset($Card[$s][$p2]);
                                    unset($Card[$s][$p3]);
                                    $choose = TRUE;
                                    break;
                                }
                            }
                        }
                    }
                    if ($choose) {
                        break;
                    }

                case 3:
                    //选顺子
                    $point = mt_rand(0, 12);
                    for ($i = 0; $i < 13; $i++) {
                        if ($choose) {
                            break;  //只取一瓢
                        }
                        $p1 = ($point + $i) % 13;
                        $p2 = ($point + $i + 1) % 13;
                        $p3 = ($point + $i + 2) % 13;
                        if ($p1 == 12) {      //K 1 2 不是顺
                            continue;
                        }

                        $suit1 = array();
                        for ($j = 0; $j < 4; $j++) {
                            if (isset($Card[$j][$p1])) {
                                $suit1[$j] = $j;
                            }
                        }
                        $suit2 = array();
                        for ($j = 0; $j < 4; $j++) {
                            if (isset($Card[$j][$p2])) {
                                $suit2[$j] = $j;
                            }
                        }
                        $suit3 = array();
                        for ($j = 0; $j < 4; $j++) {
                            if (isset($Card[$j][$p3])) {
                                $suit3[$j] = $j;
                            }
                        }

                        if (0 == count($suit1) || 0 == count($suit2) || 0 == count($suit3)) {
                            continue;
                        }
                        for ($times = 0; $times < 10; $times++) {
                            $s1 = array_rand($suit1);
                            $s2 = array_rand($suit2);
                            $s3 = array_rand($suit3);
                            if (!($s1 == $s2 && $s2 == $s3)) {
                                $player_cards[] = $Card[$s1][$p1] . ',' . $Card[$s2][$p2] . ',' . $Card[$s3][$p3];
                                unset($Card[$s1][$p1]);
                                unset($Card[$s2][$p2]);
                                unset($Card[$s3][$p3]);
                                $choose = TRUE;
                                break;
                            }
                        }
                    }
                    if ($choose) {
                        break;
                    }

                case 2:
                    //选对子
                    $point = mt_rand(0, 12);
                    for ($i = 0; $i < 13; $i++) {
                        if ($choose) {
                            break;
                        }

                        $p = ($point + $i) % 13;

                        $suit1 = array();
                        for ($j = 0; $j < 4; $j++) {
                            if (isset($Card[$j][$p])) {
                                $suit1[$j] = $j;
                            }
                        }
                        if (count($suit1) >= 2) {
                            $s = array_rand($suit1, 2);

                            $point3 = mt_rand(0, 12);
                            for ($i = 0; $i < 13; $i++) {
                                $p3 = ($point3 + $i) % 13;
                                if ($p3 == $p) {
                                    continue;
                                }

                                $suit3 = array();
                                for ($j = 0; $j < 4; $j++) {
                                    if (isset($Card[$j][$p3])) {
                                        $suit3[$j] = $j;
                                    }
                                }
                                if (count($suit3) > 0) {
                                    $s3             = array_rand($suit3);
                                    $player_cards[] = $Card[$s[0]][$p] . ',' . $Card[$s[1]][$p] . ',' . $Card[$s3][$p3];
                                    unset($Card[$s[0]][$p]);
                                    unset($Card[$s[1]][$p]);
                                    unset($Card[$s3][$p3]);
                                    $choose = TRUE;
                                    break;
                                }
                            }
                        }
                    }
                    if ($choose) {
                        break;
                    }

                case 1:
                    //选高牌
                    $remain = array_merge($Card[0], $Card[1], $Card[2], $Card[3]);
                    if (count($remain) < 3) {
                        break;
                    }
                    for ($times = 0; $times < 50; $times++) {

                        $indexs = array_rand($remain, 3);
                        $card1  = $remain[$indexs[0]];
                        $card2  = $remain[$indexs[1]];
                        $card3  = $remain[$indexs[2]];

                        $suit1 = substr($card1, 0, 1);
                        $suit2 = substr($card2, 0, 1);
                        $suit3 = substr($card3, 0, 1);
                        $p1    = substr($card1, 1) - 1;
                        $p2    = substr($card2, 1) - 1;
                        $p3    = substr($card3, 1) - 1;
                        $p     = [$p1, $p2, $p3];
                        sort($p);

                        if ($p[0] == $p[1] || $p[1] == $p[2]) {  //对子甚至是三条
                            continue;
                        }
                        if ($suit1 == $suit2 && $suit2 == $suit3) {   //同花
                            continue;
                        }
                        if ($p[0] + 2 == $p[2] || ($p[0] == 0 && $p[1] == 11 && $p[2] == 12)) {    //顺子
                            continue;
                        }
                        $suit_to_s = array(
                            'A' => 0, 'B' => 1, 'C' => 2, 'D' => 3
                        );
                        $s1        = $suit_to_s[$suit1];
                        $s2        = $suit_to_s[$suit2];
                        $s3        = $suit_to_s[$suit3];

                        $player_cards[] = $Card[$s1][$p1] . ',' . $Card[$s2][$p2] . ',' . $Card[$s3][$p3];
                        unset($Card[$s1][$p1]);
                        unset($Card[$s2][$p2]);
                        unset($Card[$s3][$p3]);
                        $choose = TRUE;
                        break;
                    }
                    if ($choose) {
                        break;
                    }

                default:
                    $remain = array_merge($Card[0], $Card[1], $Card[2], $Card[3]);
                    if (count($remain) < 3) {
                        break;
                    }
                    $indexs = array_rand($remain, 3);
                    $card1  = $remain[$indexs[0]];
                    $card2  = $remain[$indexs[1]];
                    $card3  = $remain[$indexs[2]];

                    $suit1          = substr($card1, 0, 1);
                    $suit2          = substr($card2, 0, 1);
                    $suit3          = substr($card3, 0, 1);
                    $p1             = substr($card1, 1) - 1;
                    $p2             = substr($card2, 1) - 1;
                    $p3             = substr($card3, 1) - 1;
                    $suit_to_s      = array(
                        'A' => 0, 'B' => 1, 'C' => 2, 'D' => 3
                    );
                    $s1             = $suit_to_s[$suit1];
                    $s2             = $suit_to_s[$suit2];
                    $s3             = $suit_to_s[$suit3];
                    $player_cards[] = $Card[$s1][$p1] . ',' . $Card[$s2][$p2] . ',' . $Card[$s3][$p3];
                    unset($Card[$s1][$p1]);
                    unset($Card[$s2][$p2]);
                    unset($Card[$s3][$p3]);
                    break;
            }
        }
        print_r($player_cards);
        shuffle($player_cards);
        return $player_cards;
    }


    protected function random_card_type() {
        //8 7 60 60 270 595
        $p1 = Game::Probability_Santiao;
        $p2 = $p1 + Game::Probability_Tonghuashun;
        $p3 = $p2 + Game::Probability_Tonghua;
        $p4 = $p3 + Game::Probability_Shunzi;
        $p5 = $p4 + Game::Probability_Duizi;

        $r = mt_rand(0, 999);
        if ($r < $p1) {
            return 6;
        } else if ($r < $p2) {
            return 5;
        } else if ($r < $p3) {
            return 4;
        } else if ($r < $p4) {
            return 3;
        } else if ($r < $p5) {
            return 2;
        } else {
            return 1;
        }
    }

    protected function random_card_type_without_gaopai() {
        //8 7 60 60 270 595
        $p1 = Game::Probability_Santiao;
        $p2 = $p1 + Game::Probability_Tonghuashun;
        $p3 = $p2 + Game::Probability_Tonghua;
        $p4 = $p3 + Game::Probability_Shunzi;
        $p5 = $p4 + Game::Probability_Duizi;

        $r = mt_rand(0, $p5 - 1);
        if ($r < $p1) {
            return 6;
        } else if ($r < $p2) {
            return 5;
        } else if ($r < $p3) {
            return 4;
        } else if ($r < $p4) {
            return 3;
        } else {
            return 2;
        }
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

    /*
        判断请求链接合法性
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
