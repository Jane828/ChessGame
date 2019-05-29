<?php

use \GatewayWorker\Lib\Gateway;

require_once dirname(__DIR__) . '/public.class.model.php';

class Room_Model extends Public_Model
{

    /***************************
     * common function
     ***************************/


    /*
        断线
    */
    public function userDisconnected($arrData)
    {
        if (!isset($arrData['gaming_roomid']) || trim($arrData['gaming_roomid']) == G_CONST::EMPTY_STRING) {
            return false;
        }
        if (!isset($arrData['account_id']) || trim($arrData['account_id']) == G_CONST::EMPTY_STRING) {
            return false;
        }

        $room_id    = $arrData['gaming_roomid'];
        $account_id = $arrData['account_id'];
        $client_id  = $arrData['client_id'];

        if ($this->queryOnlineStatus($room_id, $account_id)) {
            $this->writeLog("92:[$room_id]: [$account_id] is still in room");
            return true;
        }

        $Redis_Model = Redis_Model::getModelObject();
        $replyArr    = array("[roomid]" => $room_id, "[accountid]" => $account_id);

        //没开局玩过的用户离线，自动从房间中退出
        if ($this->queryTicketChecked($room_id, $account_id) == 0) {
            $RoomSequence_Key = strtr(Redis_Const::RoomSequence_Key, $replyArr);
            $zrem_result      = $Redis_Model->zremSet($RoomSequence_Key, array($account_id));

            $account_status = Game::AccountStatus_Initial;
        } else {
            $account_status = $this->queryAccountStatus($room_id, $account_id);

            if ($account_status == Game::AccountStatus_Ready) {    //准备状态下断线， 变为未准备

                $account_status = Game::AccountStatus_Notready;
            }
        }

        $this->updateAccountStatus($room_id, $account_id, $account_status);
        $this->writeLog("[$room_id] ($account_id) 离线");

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

        //判断是否全部人离线
        $clients_of_groupid = Gateway::getClientSessionsByGroup($room_id);
        if (count($clients_of_groupid) == 0) {
            $this->setupClearRoomPassiveTimer($room_id);
        }

        return true;
    }

    // 创建房间
    public function createRoom($arrData)
    {
        $timestamp = time();
        $result    = array();

        $data       = $arrData['data'];
        $operation  = $arrData['operation'];
        $account_id = $arrData['account_id'];

        $session            = $arrData['session'];
        $Verification_Model = Verification_Model::getModelObject();
        if (false == $Verification_Model->checkRequestSession($account_id, $session)) {
            return OPT_CONST::NO_RETURN;
        }

        if (!isset($data['data_key']) || trim($data['data_key']) == G_CONST::EMPTY_STRING) {
            $this->logMessage('error', "function(createRoom):lack of data_key" . " in file" . __FILE__ . " on Line " . __LINE__);
            return $this->_missingPrameterArr($operation, "data_key");
        }

        if (!isset($data['seen'])) {
            $this->logMessage('error', "function(createRoom):lack of seen" . " in file" . __FILE__ . " on Line " . __LINE__);
            return $this->_missingPrameterArr($operation, "seen");
        }

        if (!isset($data['ticket_count'])) {
            $this->logMessage('error', "function(createRoom):lack of ticket_count" . " in file" . __FILE__ . " on Line " . __LINE__);
            return $this->_missingPrameterArr($operation, "ticket_count");
        }
        if (!isset($data['chip_type'])) {
            $this->logMessage('error', "function(createRoom):lack of chip_type" . " in file" . __FILE__ . " on Line " . __LINE__);
            return $this->_missingPrameterArr($operation, "chip_type");
        }
        if (!isset($data['disable_pk_100'])) {
            $this->logMessage('error', "function(createRoom):lack of disable_pk_100" . " in file" . __FILE__ . " on Line " . __LINE__);
            return $this->_missingPrameterArr($operation, "disable_pk_100");
        }
        if (!isset($data['upper_limit']) || !in_array($data['upper_limit'], [500, 1000, 2000])) {
            $this->logMessage('error', "function(createRoom):lack of upper_limit" . " in file" . __FILE__ . " on Line " . __LINE__);
            return $this->_missingPrameterArr($operation, "upper_limit");
        }
        // 准入规则
        if (isset($data['bean_type']) && in_array($data['bean_type'], [1, 2, 3, 4])) {
            $bean_type = $data['bean_type'];
        } else {
            return array("result" => "-1", "operation" => $operation, "data" => $result, "result_message" => "准入规则错误");
        }

        $data_key           = $data['data_key'];
        $spend_ticket_count = $data['ticket_count'];
        $total_num          = Config::GameNum_EachRound * $spend_ticket_count;
        $chip_type          = $data['chip_type'];
        $disable_pk_100     = $data['disable_pk_100'];
        $upper_limit        = $data['upper_limit'];
        $seen               = $data['seen']; // 看牌需下注
        $game_type          = Game::Game_Type;

        $session            = $arrData['session'];
        $Verification_Model = Verification_Model::getModelObject();
        if (false == $Verification_Model->checkRequestSession($account_id, $session)) {
            return OPT_CONST::NO_RETURN;
        }

        //获取维护公告
        $alert_type          = -1;
        $announcement_result = $this->getGameAnnouncement($account_id, $game_type);
        if (is_array($announcement_result)) {
            $alert_type = $announcement_result['alert_type'];
            $alert_text = $announcement_result['alert_text'];
        }
        if ($alert_type == 4) {
            $result['alert_type'] = $alert_type;
            return array("result" => "-1", "operation" => $operation, "data" => $result, "result_message" => $alert_text);
        }

        $MMYSQL = $this->initMysql();

        //判断房卡余额
        $my_ticket_count = $MMYSQL->select("ticket_count")->from("room_ticket")->where("account_id=" . $account_id)->single();
        $this->writeLog("($account_id) 牌券:" . $my_ticket_count);
        if ($my_ticket_count < $spend_ticket_count) {
            $this->writeLog("($account_id) 牌券不足");
            $result['alert_type'] = 1;    //1房卡不足
            return array("result" => "1", "operation" => $operation, "data" => $result, "result_message" => "房卡不足");
        }

        // 判断欢乐豆是否充足
        if (2 == $bean_type) {
            $min_bean = Config::Bean_Type_2;
        } elseif (3 == $bean_type) {
            $min_bean = Config::Bean_Type_3;
        } elseif (4 == $bean_type) {
            $min_bean = Config::Bean_Type_4;
        } else {
            $min_bean = Config::Bean_Type_1;
        }
        $clubPlayer = $MMYSQL->select('club_id,player_bean')->from(Club_Players)
            ->where('player_id=' . $account_id . ' AND player_status=1 AND is_last=1')->row();
        if (empty($clubPlayer)) {
            return ['result' => 1, 'operation' => $operation, 'data' => $result, 'result_message' => '请选择一个公会'];
        }
        if ($clubPlayer['player_bean'] < $min_bean) {
            $result['alert_type'] = 1;    //1欢乐豆不足
            return array("result" => "1", "operation" => $operation, "data" => $result, "result_message" => "您的欢乐豆不足");
        }
        $club_id = $clubPlayer['club_id'];

        //判断房间申请记录是否存在
        $room_query = $MMYSQL->select('room_id,room_number,account_id,is_close')->from(Room)->where('data_key="'.$data_key.'"')->row();
        if (empty($room_query)) {
            $is_close = G_CONST::IS_FALSE;
            $r_array['create_time']  = $timestamp;
            $r_array['create_appid'] = "aid_" . $account_id;
            $r_array['update_time']  = $timestamp;
            $r_array['update_appid'] = "aid_" . $account_id;
            $r_array['is_delete']    = G_CONST::IS_FALSE;
            $r_array['data_key']     = $data_key;
            $r_array['account_id']   = $account_id;
            $r_array['is_close']     = G_CONST::IS_FALSE;
            $r_array['game_type']    = Game::Game_Type;
            $r_array['club_id']      = $club_id;
            $r_array['bean_type']    = $bean_type;

            $room_id = $MMYSQL->insertReturnID(Room, $r_array);
            if ($room_id > 0) {
                $room_number = 10000 + $room_id;
            } else {
                $this->logMessage('error', "function(createRoom):用户" . $account_id . " 创建房间失败：" . " in file" . __FILE__ . " on Line " . __LINE__);
                return array("result" => "-1", "operation" => $operation, "data" => $result, "result_message" => "创建房间失败");
            }

            $MMYSQL->update(Room)->cols(['room_number'=>$room_number])->where('room_id='.$room_id)->query();
        } else {
            $room_id     = $room_query['room_id'];
            $room_number = $room_query['room_number'];
            $is_close    = $room_query['is_close'];
        }

        //添加房间信息到redis
        $Redis_Model = Redis_Model::getModelObject();

        $replyArr = array("[roomid]" => $room_id);
        $Room_Key = strtr(Redis_Const::Room_Key, $replyArr);

        $r_mkv[Redis_Const::Room_Field_Number]       = $room_number;    //房间号
        $r_mkv[Redis_Const::Room_Field_GameRound]    = 1;            //游戏轮数
        $r_mkv[Redis_Const::Room_Field_GameNum]      = 0;            //游戏第几局
        $r_mkv[Redis_Const::Room_Field_TotalNum]     = $total_num;
        $r_mkv[Redis_Const::Room_Field_Status]       = Game::RoomStatus_Waiting;                //房间状态，1等待、2进行中、3关闭
        $r_mkv[Redis_Const::Room_Field_DefaultScore] = Game::Default_Score;        //开局默认分数

        $r_mkv[Redis_Const::Room_Field_TicketCount]  = $spend_ticket_count;
        $r_mkv[Redis_Const::Room_Field_ChipType]     = $chip_type;
        $r_mkv[Redis_Const::Room_Field_DisablePk100] = $disable_pk_100;
        $r_mkv[Redis_Const::Room_Field_UpperLimit]   = $upper_limit;
        $r_mkv[Redis_Const::Room_Field_Creator]      = $account_id;  //房间创建者

        $r_mkv[Redis_Const::Room_Field_StartTime] = -1;
        $r_mkv[Redis_Const::Room_Field_Seen]      = $seen;
        $r_mkv[Redis_Const::Room_Field_BeanType]  = $bean_type; // 准入规则
        $r_mkv[Redis_Const::Room_Field_ClubId]    = $club_id; // 公会ID

        $Redis_Model->hmsetField($Room_Key, $r_mkv);

        $result['room_id']     = $room_id;
        $result['room_number'] = $room_number;
        $result['is_close']    = $is_close;

        //扣除房卡  房主扣卡模式
        if (Config::Ticket_Mode == 2) {
            $this->balanceTicket($room_id, $account_id, $spend_ticket_count);
        }

        $this->writeLog("[$room_id] ($account_id) 创建房间" . $room_number);
        return array("result" => OPT_CONST::SUCCESS, "operation" => $operation, "data" => $result, "result_message" => "创建房间");
    }

    /*
        进入房间
    */
    public function joinRoom($arrData)
    {
        $result = array();

        $data       = $arrData['data'];
        $operation  = $arrData['operation'];
        $account_id = $arrData['account_id'];
        $client_id  = $arrData['client_id'];

        $session            = $arrData['session'];
        $Verification_Model = Verification_Model::getModelObject();
        if (false == $Verification_Model->checkRequestSession($account_id, $session)) {
            return OPT_CONST::NO_RETURN;
        }

        if (!(isset($data['room_number']) && $data['room_number'] > 0)) {
            $this->logMessage('error', "function(joinRoom):lack of room_number" . " in file" . __FILE__ . " on Line " . __LINE__);
            return $this->_missingPrameterArr($operation, "room_number");
        }

        $room_number = $data['room_number'];

        $MMYSQL = $this->initMysql();

        //判断房间申请记录是否存在
        $roomInfo = $MMYSQL->select('room_id,is_close,club_id,bean_type')->from(Room)->where('room_number=' . $room_number)->row();
        if (empty($roomInfo)) {
            return ['result' => OPT_CONST::FAILED, 'operation' => $operation, 'data' => $result, 'result_message' => '房间不存在'];
        }
        if ($roomInfo['is_close']) {
            $result['room_status'] = 4;
            return ['result' => OPT_CONST::FAILED, 'operation' => $operation, 'data' => $result, 'result_message' => '房间已关闭'];
        }
        $room_id = $roomInfo['room_id'];

        // 与房主同一个公会，且豆数满足准入规则
        $result['is_enough'] = 0;
        $result['is_member'] = 0;
        $clubPlayer          = $MMYSQL->select('player_bean')->from(Club_Players)->where('club_id=' . $roomInfo['club_id'] . ' and player_id=' . $account_id . ' and player_status=1')->row();
        if (empty($clubPlayer)) {
            return ['result' => 0, 'operation' => $operation, 'data' => $result, 'result_message' => '您不在此公会中'];
        }
        $result['is_member'] = 1;

        $beanTypes = [1=>Config::Bean_Type_1, 2=>Config::Bean_Type_2, 3=>Config::Bean_Type_3, 4=>Config::Bean_Type_4];
        $minBean   = isset($beanTypes[$roomInfo['bean_type']]) ? $beanTypes[$roomInfo['bean_type']] : $beanTypes[1];
        if ($clubPlayer['player_bean'] < $minBean) {
            return ['result' => 0, 'operation' => $operation, 'data' => $result, 'result_message' => '您的欢乐豆不足'];
        }
        $result['is_enough'] = 1;

        $Redis_Model = Redis_Model::getModelObject();
        $replyArr    = array("[roomid]" => $room_id, "[accountid]" => $account_id);

        $RoomSequence_Key = strtr(Redis_Const::RoomSequence_Key, $replyArr);

        //总分数
        $RoomScore_Key        = strtr(Redis_Const::RoomScore_Key, $replyArr);
        $RoomScore_Field_User = strtr(Redis_Const::RoomScore_Field_User, $replyArr);

        $rsSeq_score = $Redis_Model->getZscore($RoomSequence_Key, $account_id);
        //判断用户是否已加入房间
        if (Redis_CONST::DATA_NONEXISTENT !== $rsSeq_score)        //已加入
        {
            //获取分数
            $rScore_score = $Redis_Model->hgetField($RoomScore_Key, $RoomScore_Field_User);
            //获取用户所在位置
            $serial_num = $rsSeq_score;

            $account_status = $this->queryAccountStatus($room_id, $account_id);
        } else    //未加入
        {
            //判断游戏人数
            $user_count = 0;
            //获取房间所有用户
            $sset_array['key']        = $RoomSequence_Key;
            $sset_array['WITHSCORES'] = "WITHSCORES";
            $gamer_query              = $Redis_Model->getSortedSetLimitByAry($sset_array);
            if (Redis_CONST::DATA_NONEXISTENT !== $gamer_query) {
                $user_count = count($gamer_query);
            }
            if ($user_count >= Game::GameUser_MaxCount) {
                $this->writeLog("function(joinRoom):room($room_number) 人数已满" . " in file" . __FILE__ . " on Line " . __LINE__);
                $result['alert_type'] = 2;    //2人数已满
                return array("result" => "1", "operation" => $operation, "data" => $result, "result_message" => "房间人数已满");
            }

            $serial_num = -1;
            for ($i = 1; $i <= Game::GameUser_MaxCount; $i++) {
                if (array_search($i, $gamer_query) === false) {
                    $serial_num = $i;
                    break;
                }
            }

            if ($serial_num == -1) {
                $this->logMessage('error', "function(joinRoom):serial_num($serial_num) 无法找到空桌位" . " in file" . __FILE__ . " on Line " . __LINE__);
                return array("result" => OPT_CONST::FAILED, "operation" => $operation, "data" => $result, "result_message" => "数据错误");
            }

            $rSeq_mkv[$serial_num] = $account_id;
            $zadd_result           = $Redis_Model->zaddSet($RoomSequence_Key, $rSeq_mkv);

            //添加默认分数
            $rScore_score                      = 0;
            $rScore_mkv[$RoomScore_Field_User] = $rScore_score;
            $hmset_result                      = $Redis_Model->hmsetField($RoomScore_Key, $rScore_mkv);

            //首次进房初始状态
            $account_status    = Game::AccountStatus_Initial;
            $AccountStatus_Key = strtr(Redis_Const::AccountStatus_Key, $replyArr);
            $mkv[$account_id]  = $account_status;
            $mset_result       = $Redis_Model->hmsetField($AccountStatus_Key, $mkv);

        }

        //绑定用户UID
        $RoomUser_UID = strtr(Game::RoomUser_UID, $replyArr);

        //解绑当前客户端
        /*
        $client_array = Gateway::getClientIdByUid($RoomUser_UID);
        if(is_array($client_array) && count($client_array) > 0)
        {
            foreach($client_array as $bind_client)
            {
                $this->logMessage('error', "function(joinRoom):RoomUser_UID($RoomUser_UID) 多客户的登陆"." in file".__FILE__." on Line ".__LINE__);
                //推送强制下线
                $forceLogout_message = $this->_JSON(array("result"=>"-202","result_message"=>"您的账号在另一地点登陆，您被迫下线。"));
                Gateway::sendToClient($bind_client, $forceLogout_message);
                //解绑连接
                //Gateway::closeClient($bind_client);
                Gateway::unbindUid($bind_client, $RoomUser_UID);
                Gateway::leaveGroup($bind_client, $room_id);
            }
        }
        */

        Gateway::bindUid($client_id, $RoomUser_UID);
        Gateway::joinGroup($client_id, $room_id);

        //获取房间当前局数
        $game_num                = $this->queryGameNumber($room_id);
        $room_status             = $this->queryRoomStatus($room_id);
        $room_ary['room_id']     = $room_id;
        $room_ary['room_number'] = $room_number;
        $room_ary['room_status'] = $room_status;
        $room_ary['account_score']  = $rScore_score;
        $room_ary['account_bean']   = $this->queryNowBean($room_id, $account_id);
        $room_ary['account_status'] = $account_status;
        $room_ary['online_status']  = $this->queryOnlineStatus($room_id, $account_id);

        $room_ary['serial_num'] = $serial_num;
        $room_ary['game_num']   = $game_num;
        $room_ary['total_num']  = $this->queryTotalNum($room_id);

        $room_ary['ticket_checked'] = $this->queryTicketChecked($room_id, $account_id);
        $room_ary['scoreboard']     = $this->queryScoreboard($room_id);

        $card_info = array();
        $card_type = 0;
        if ($this->querySeenCard($room_id, $account_id)) {    //已经看过牌的
            $card_info = $this->queryCardInfo($room_id, $account_id);
            $value     = $this->caculateCardValue($room_id, $account_id);
            $card_type = substr($value, 0, 1);
        }

        $room_ary['cards']     = $card_info;
        $room_ary['card_type'] = $card_type;

        $room_ary['benchmark']  = $this->queryBenchmark($room_id);
        $room_ary['pool_score'] = $this->queryPoolScore($room_id);


        $setting                    = $this->queryRoomSetting($room_id);
        $room_ary['ticket_count']   = isset($setting[Redis_Const::Room_Field_TicketCount]) ? $setting[Redis_Const::Room_Field_TicketCount] : 1;
        $room_ary['chip_type']      = isset($setting[Redis_Const::Room_Field_ChipType]) ? $setting[Redis_Const::Room_Field_ChipType] : 1;
        $room_ary['disable_pk_100'] = isset($setting[Redis_Const::Room_Field_DisablePk100]) ? $setting[Redis_Const::Room_Field_DisablePk100] : 0;
        $room_ary['upper_limit']    = isset($setting[Redis_Const::Room_Field_UpperLimit]) ? $setting[Redis_Const::Room_Field_UpperLimit] : 500;
        $room_ary['bean_type']      = isset($setting[Redis_Const::Room_Field_BeanType]) ? $setting[Redis_Const::Room_Field_BeanType] : 1;
        $room_ary['seen']           = isset($setting[Redis_Const::Room_Field_Seen]) ? $setting[Redis_Const::Room_Field_Seen] : 0;

        // 当局下注总分
        $room_ary['chip'] = $this->queryChip($room_id, $account_id);

        //推送房间信息
        $room_return = array("result" => OPT_CONST::SUCCESS, "operation" => $operation, "data" => $room_ary, "result_message" => "入房成功");
        $this->pushMessageToCurrentClient($room_return);

        //返回所有玩家状态给进房玩家
        $allGamer = $this->getGamerInfo($room_id);
        if (is_array($allGamer)) {
            $currentGamer_return = array("result" => OPT_CONST::SUCCESS, "operation" => "AllGamerInfo", "data" => $allGamer, "result_message" => "所有玩家状态");
            $this->pushMessageToCurrentClient($currentGamer_return);
        }

        $this->notyfyPlayingStatus($room_id, $account_id);

        //推送当前玩家状态给其他玩家
        $currentGamer = $this->getGamerInfo($room_id, $account_id);
        if (is_array($currentGamer)) {
            $currentGamer_return = array("result" => OPT_CONST::SUCCESS, "operation" => "UpdateGamerInfo", "data" => $currentGamer, "result_message" => "某玩家状态");
            $this->pushMessageToGroup($room_id, $currentGamer_return, $client_id);
        }

        //显示房间目前的倒计时
        $limit = $game_num > 0 ? Game::LimitTime_Ready : Game::LimitTime_StartGame;

        if ($room_status == Game::RoomStatus_Waiting && ($countdown = $this->queryCountdown($room_id, $limit)) > 0) {
            $arr = array("result" => 0, "operation" => "StartLimitTime", "data" => array('limit_time' => $countdown), "result_message" => "开始倒计时");
            $this->pushMessageToCurrentClient($arr);
        }

        //保存用户当前房间,用户ID
        $_SESSION['gaming_roomid'] = $room_id;
        $_SESSION['account_id']    = $account_id;

        $this->writeLog("[$room_id] $account_id 进入房间");
        return OPT_CONST::NO_RETURN;
    }

    protected function notyfyPlayingStatus($room_id, $account_id)
    {
        $Redis_Model = Redis_Model::getModelObject();
        $replyArr    = array("[roomid]" => $room_id, "[accountid]" => $account_id);
        $active_user = $this->queryActiveUser($room_id);
        if ($active_user != -1) {

            $can_open = 0;
            $pk_user  = array();
            if ($this->pkChipConditon($room_id, $active_user)) {
                $pk_user = $this->queryPkUser($room_id, $active_user);
                if (count($pk_user) > 0) {
                    $can_open = 1;
                }
            }
            $chip = $this->queryChip($room_id, $account_id);
            //通知:哪位正进行下注
            $noty_arr = array(
                'account_id'     => $active_user,
                'playing_status' => Game::PlayingStatus_Betting,
                'limit_time'     => $this->queryCountdown($room_id, Game::LimitTime_Betting),
                'can_open'       => $can_open,
                'pk_user'        => $pk_user,
                'chip'           => $chip
            );
            $msg_arr  = array("result" => 0, "operation" => "NotyChooseChip", "data" => $noty_arr, "result_message" => "通知下注");
            $this->pushMessageToCurrentClient($msg_arr);
        }
    }


    /*
        拉取房间信息
    */
    public function pullRoomInfo($arrData)
    {
        $result = array();

        $data       = $arrData['data'];
        $operation  = $arrData['operation'];
        $account_id = $arrData['account_id'];
        $client_id  = $arrData['client_id'];

        $session            = $arrData['session'];
        $Verification_Model = Verification_Model::getModelObject();
        if (false == $Verification_Model->checkRequestSession($account_id, $session)) {
            return OPT_CONST::NO_RETURN;
        }

        if (!(isset($data['room_id']) && $data['room_id'] > 0)) {
            $this->logMessage('error', "function(pullRoomInfo):lack of room_id" . " in file" . __FILE__ . " on Line " . __LINE__);
            return $this->_missingPrameterArr($operation, "room_id");
        }

        $room_id = $data['room_id'];

        $is_legal = $this->checkRequestClientLegal($client_id, $room_id, $account_id);
        if (!$is_legal) {
            $this->logMessage('error', "function(checkRequestClientLegal):illegal opt: account_id($account_id) room_id($room_id)" . " in file" . __FILE__ . " on Line " . __LINE__);
            //推送强制下线
            $illegal_message = $this->_JSON(array("result" => "-203", "result_message" => "illegal opt"));
            Gateway::sendToCurrentClient($illegal_message);
            return OPT_CONST::NO_RETURN;
        }

        $Redis_Model = Redis_Model::getModelObject();
        $replyArr    = array("[roomid]" => $room_id, "[accountid]" => $account_id);

        $RoomSequence_Key = strtr(Redis_Const::RoomSequence_Key, $replyArr);

        //总分数
        $RoomScore_Key        = strtr(Redis_Const::RoomScore_Key, $replyArr);
        $RoomScore_Field_User = strtr(Redis_Const::RoomScore_Field_User, $replyArr);

        $rsSeq_score = $Redis_Model->getZscore($RoomSequence_Key, $account_id);
        //判断用户是否已加入房间
        if (Redis_CONST::DATA_NONEXISTENT !== $rsSeq_score)        //已加入
        {
            //获取分数
            $rScore_score = $Redis_Model->hgetField($RoomScore_Key, $RoomScore_Field_User);
            //获取用户所在位置
            $serial_num = $rsSeq_score;

            $account_status = $this->queryAccountStatus($room_id, $account_id);
        } else    //未加入
        {
            $this->logMessage('error', "function(pullRoomInfo):account($account_id) not join room" . " in file" . __FILE__ . " on Line " . __LINE__);
            return array("result" => OPT_CONST::FAILED, "operation" => $operation, "data" => $result, "result_message" => "用户未加入房间");
        }

        $room_ary['account_score']  = $rScore_score;
        $room_ary['account_bean']  = $this->queryNowBean($room_id, $account_id);
        $room_ary['account_status'] = $account_status;
        $room_ary['online_status']  = $this->queryOnlineStatus($room_id, $account_id);

        $room_ary['serial_num'] = $serial_num;

        $room_ary['ticket_checked'] = $this->queryTicketChecked($room_id, $account_id);

        $card_info = array();
        $card_type = 0;
        if ($this->querySeenCard($room_id, $account_id)) {    //已经看过牌的
            $card_info = $this->queryCardInfo($room_id, $account_id);
            $value     = $this->caculateCardValue($room_id, $account_id);
            $card_type = substr($value, 0, 1);
        }

        $room_ary['cards']     = $card_info;
        $room_ary['card_type'] = $card_type;

        $room_ary['benchmark']  = $this->queryBenchmark($room_id);
        $room_ary['pool_score'] = $this->queryPoolScore($room_id);

        $setting               = $this->queryRoomSetting($room_id);
        $room_ary['chip_type'] = isset($setting[Redis_Const::Room_Field_ChipType]) ? $setting[Redis_Const::Room_Field_ChipType] : 1;

        //返回所有玩家状态
        $allGamer = $this->getGamerInfo($room_id);
        if (!is_array($allGamer)) {
            $this->logMessage('error', "function(pullRoomInfo):room($room_id) no player" . " in file" . __FILE__ . " on Line " . __LINE__);
            return array("result" => OPT_CONST::FAILED, "operation" => $operation, "data" => $result, "result_message" => "房间没有其他用户");
        }

        $pull_return = array("result" => OPT_CONST::SUCCESS, "operation" => $operation, "data" => $room_ary, "result_message" => "拉取房间信息", "all_gamer_info" => $allGamer);

        $this->pushMessageToCurrentClient($pull_return);

        $this->notyfyPlayingStatus($room_id, $account_id);

        $this->logMessage('error', "function(pullRoomInfo):用户$account_id 拉取房间$room_id 信息" . " in file" . __FILE__ . " on Line " . __LINE__);
        $this->logMessage('error', "function(pullRoomInfo):pull_return:" . json_encode($pull_return) . " in file" . __FILE__ . " on Line " . __LINE__);

        return OPT_CONST::NO_RETURN;
    }

    //{"operation":"TestDouble","account_id":"4","data":{"room_id":"792","serial_num":"6"}}
    public function testDouble($arrData)
    {
        $timestamp = time();
        $result    = array();
        $return    = array();

        $data       = $arrData['data'];
        $operation  = $arrData['operation'];
        $account_id = $arrData['account_id'];
        $client_id  = $arrData['client_id'];

        $session            = $arrData['session'];
        $Verification_Model = Verification_Model::getModelObject();
        if (false == $Verification_Model->checkRequestSession($account_id, $session)) {
            return OPT_CONST::NO_RETURN;
        }

        if (!isset($data['room_id']) || trim($data['room_id']) == G_CONST::EMPTY_STRING) {
            $this->logMessage('error', "function(testDouble):lack of room_id" . " in file" . __FILE__ . " on Line " . __LINE__);
            return $this->_missingPrameterArr($operation, "room_id");
        }

        $room_id    = $data['room_id'];
        $serial_num = $data['serial_num'];

        $currentGamer_test               = $this->getGamerInfo($room_id, $account_id);
        $currentGamer_test['serial_num'] = $serial_num;
        $msg_arr                         = array("result" => OPT_CONST::SUCCESS, "operation" => "UpdateGamerInfo", "data" => $currentGamer_test, "result_message" => "某玩家状态");

        $replyArr = array("[roomid]" => $room_id, "[accountid]" => $account_id);
        $room_aid = strtr(Game::RoomUser_UID, $replyArr);

        print_r($msg_arr);

        $this->pushMessageToAccount($room_id, $account_id, $msg_arr);
        return OPT_CONST::NO_RETURN;
    }


    /*
        获取房间所有用户
    */
    protected function getGamerInfo($room_id, $account_id = -1)
    {
        $result = array();

        $Redis_Model = Redis_Model::getModelObject();
        $MMYSQL      = $this->initMysql();

        $replyArr = array("[roomid]" => $room_id, "[accountid]" => $account_id);

        //房间玩家集合
        $RoomSequence_Key = strtr(Redis_Const::RoomSequence_Key, $replyArr);

        //获取房间所有用户
        $sset_array['key']        = $RoomSequence_Key;
        $sset_array['WITHSCORES'] = "WITHSCORES";
        $gamer_query              = $Redis_Model->getSortedSetLimitByAry($sset_array);
        if (Redis_CONST::DATA_NONEXISTENT !== $gamer_query) {
            foreach ($gamer_query as $gamer_id => $serial_num) {
                //获取玩家信息
                $account_where = 'account_id="' . $gamer_id . '"';
                $account_sql   = 'select nickname,headimgurl from ' . WX_Account . ' where ' . $account_where;
                $account_query = $MMYSQL->query($account_sql);
                if (!is_array($account_query) || count($account_query) == 0) {
                    $this->logMessage('error', "function(getGamerInfo):account($gamer_id) not exist" . " in file" . __FILE__ . " on Line " . __LINE__);
                    return false;
                }
                $info['serial_num'] = $serial_num;
                $info['account_id'] = $gamer_id;
                $info['nickname']   = $account_query[0]['nickname'];
                $info['headimgurl'] = $account_query[0]['headimgurl'];


                //获取玩家当前积分
                $rScore_score = $this->queryAccountScore($room_id, $gamer_id);
                if (Redis_CONST::DATA_NONEXISTENT === $rScore_score) {
                    $this->logMessage('error', "function(getGamerInfo):account($gamer_id) score not exist" . " in file" . __FILE__ . " on Line " . __LINE__);
                    return false;
                }
                $info['account_score'] = $rScore_score;
                $info['account_bean'] = $this->queryNowBean($room_id, $gamer_id);

                //获取玩家当前状态
                $rStatus = $this->queryAccountStatus($room_id, $gamer_id);
                if (Redis_CONST::DATA_NONEXISTENT === $rStatus) {
                    $this->logMessage('error', "function(getGamerInfo):account($gamer_id) status not exist" . " in file" . __FILE__ . " on Line " . __LINE__);
                    return false;
                }
                $info['account_status'] = $rStatus;
                $info['online_status']  = $this->queryOnlineStatus($room_id, $gamer_id);
                $info['ticket_checked'] = $this->queryTicketChecked($room_id, $gamer_id);

                if ($account_id == $gamer_id) {
                    return $info;
                }

                $result[] = $info;

            }
        }

        return $result;
    }

    // 进房之前的查询
    public function prepareJoinRoom($arrData)
    {
        $result = array();

        $data       = $arrData['data'];
        $operation  = $arrData['operation'];
        $account_id = $arrData['account_id'];

        $session            = $arrData['session'];
        $Verification_Model = Verification_Model::getModelObject();
        if (false == $Verification_Model->checkRequestSession($account_id, $session)) {
            return OPT_CONST::NO_RETURN;
        }

        if (!(isset($data['room_number']) && $data['room_number'] > 0)) {
            $this->logMessage('error', "function(PrepareJoinRoom):lack of room_number" . " in file" . __FILE__ . " on Line " . __LINE__);
            return $this->_missingPrameterArr($operation, "room_number");
        }

        $room_number = $data['room_number'];

        $MMYSQL = $this->initMysql();

        //判断房间申请记录是否存在
        $roomInfo = $MMYSQL->select('room_id,is_close,club_id,bean_type')->from(Room)->where('room_number=' . $room_number)->row();
        if (empty($roomInfo)) {
            return ['result' => OPT_CONST::FAILED, 'operation' => $operation, 'data' => $result, 'result_message' => '房间不存在'];
        }
        if ($roomInfo['is_close']) {
            $result['room_status'] = 4;
            return ['result' => OPT_CONST::FAILED, 'operation' => $operation, 'data' => $result, 'result_message' => '房间已关闭'];
        }
        $room_id = $roomInfo['room_id'];

        // 与房主同一个公会，且豆数满足准入规则
        $result['is_enough'] = 0; // 豆数是否充足
        $result['is_member'] = 0; // 是否同一个公会
        $clubPlayer          = $MMYSQL->select('player_bean')
            ->from(Club_Players)
            ->where('club_id=' . $roomInfo['club_id'] . ' and player_id=' . $account_id . ' and player_status=1')
            ->row();
        if (empty($clubPlayer)) {
            return ['result' => 0, 'operation' => $operation, 'data' => $result, 'result_message' => '您不在此公会中'];
        }
        $beanTypes = [1=>Config::Bean_Type_1, 2=>Config::Bean_Type_2, 3=>Config::Bean_Type_3, 4=>Config::Bean_Type_4];
        $minBean   = isset($beanTypes[$roomInfo['bean_type']]) ? $beanTypes[$roomInfo['bean_type']] : $beanTypes[1];
        if ($clubPlayer['player_bean'] < $minBean) {
            return ['result' => 0, 'operation' => $operation, 'data' => $result, 'result_message' => '您的欢乐豆不足'];
        }

        //房间参数
        $user_count     = 0;
        $alert_text     = "";
        $room_status    = $this->queryRoomStatus($room_id);
        $setting        = $this->queryRoomSetting($room_id);
        $ticket_count   = isset($setting[Redis_Const::Room_Field_TicketCount]) ? $setting[Redis_Const::Room_Field_TicketCount] : 1;
        $chip_type      = isset($setting[Redis_Const::Room_Field_ChipType]) ? $setting[Redis_Const::Room_Field_ChipType] : 1;
        $disable_pk_100 = isset($setting[Redis_Const::Room_Field_DisablePk100]) ? $setting[Redis_Const::Room_Field_DisablePk100] : 0;
        $upper_limit    = isset($setting[Redis_Const::Room_Field_UpperLimit]) ? $setting[Redis_Const::Room_Field_UpperLimit] : 500;
        $bean_type      = isset($setting[Redis_Const::Room_Field_BeanType]) ? $setting[Redis_Const::Room_Field_BeanType] : 1;
        $seen           = isset($setting[Redis_Const::Room_Field_Seen]) ? $setting[Redis_Const::Room_Field_Seen] : 0;
        $game_type      = Game::Game_Type;
        $room_array     = array(
            'user_count'     => $user_count,
            'alert_text'     => $alert_text,
            'room_status'    => $room_status,
            'ticket_count'   => $ticket_count,
            'chip_type'      => $chip_type,
            'disable_pk_100' => $disable_pk_100,
            'upper_limit'    => $upper_limit,
            'seen'           => $seen,
            'bean_type'      => $bean_type,
            'is_enough'      => 1,
            'is_member'      => 1,
        );

        //获取维护公告
        $alert_type          = -1;
        $announcement_result = $this->getGameAnnouncement($account_id, $game_type);
        if (is_array($announcement_result)) {
            $alert_type = $announcement_result['alert_type'];
            $alert_text = $announcement_result['alert_text'];
        }
        if ($alert_type == 4) {
            $room_array['alert_type'] = $alert_type;
            return array("result" => "1", "operation" => $operation, "data" => $room_array, "result_message" => $alert_text);
        }

        //判断房卡余额
        if (Config::Ticket_Mode == 1) {
            $spend_ticket_count = $this->queryTicketCount($room_id);
            $my_ticket_count    = $MMYSQL->select("ticket_count")->from("room_ticket")->where("account_id=" . $account_id)->single();
            $this->writeLog("[$room_id] ($account_id) 牌券:" . $my_ticket_count);
            if ($my_ticket_count >= $spend_ticket_count || $this->queryTicketChecked($room_id, $account_id) >= 1) {

            } else {
                $this->writeLog("[$room_id] ($account_id) 牌券不足");
                $room_array['alert_type'] = 1;    //1房卡不足
                return array("result" => "1", "operation" => $operation, "data" => $room_array, "result_message" => "房卡不足");
            }
        }


        $Redis_Model      = Redis_Model::getModelObject();
        $replyArr         = array("[roomid]" => $room_id, "[accountid]" => $account_id);
        $RoomSequence_Key = strtr(Redis_Const::RoomSequence_Key, $replyArr);

        //房间人
        $room_users = array();
        $is_member  = false;
        //获取房间所有用户
        $sset_array['key'] = $RoomSequence_Key;
        $gamer_query       = $Redis_Model->getSortedSetLimitByAry($sset_array);
        if (Redis_CONST::DATA_NONEXISTENT !== $gamer_query) {
            foreach ($gamer_query as $gamer_id) {
                //获取玩家信息
                $account_where = 'account_id="' . $gamer_id . '"';
                $account_sql   = 'select account_id,nickname from ' . WX_Account . ' where ' . $account_where;
                $row           = $MMYSQL->row($account_sql);
                if ($row) {
                    $room_users[] = $row['nickname'];
                    $room_array['user_count']++;
                    if ($row['account_id'] == $account_id) {
                        $is_member = true;
                    }
                }
            }
        }
        if ($room_array['user_count'] == 0 || $is_member) {

        } else {
            if ($room_array['user_count'] >= Game::GameUser_MaxCount) {
                $this->writeLog("[$room_id] ($account_id)  PrepareJoinRoom 人数已满");
                $room_array['alert_type'] = 2;    //2人数已满
                return array("result" => "1", "operation" => $operation, "data" => $room_array, "result_message" => "房间人数已满");
            }
            $user_str                 = implode("、", $room_users);
            $room_array['alert_text'] = "房间中有" . $user_str . "，是否加入？";
        }
        return array("result" => "0", "operation" => $operation, "data" => $room_array, "result_message" => "进房询问");
    }

    protected function getGameAnnouncement($account_id, $game_type)
    {
        $timestamp = time();

        $MMYSQL = $this->initMysql();

        $announcement_where = 'game_type=' . $game_type . ' and announce_time<=' . $timestamp . ' and end_time>' . $timestamp . ' and is_delete=0';
        $announcement_sql   = 'select announce_time,service_time,end_time,announce_text,service_text from ' . Game_Announcement . ' where ' . $announcement_where;
        $announcement_query = $MMYSQL->row($announcement_sql);
        if (is_array($announcement_query) && count($announcement_query) > 0) {
            $service_time  = $announcement_query['service_time'];
            $announce_text = $announcement_query['announce_text'];
            $service_text  = $announcement_query['service_text'];

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
                return true;
            } else {
                return $array;
            }
        }

        return true;
    }


    /*
        准备操作
    */
    public function readyStart($arrData)
    {
        $result    = array();

        $data       = $arrData['data'];
        $operation  = $arrData['operation'];
        $account_id = $arrData['account_id'];
        $client_id  = $arrData['client_id'];

        $session            = $arrData['session'];
        $Verification_Model = Verification_Model::getModelObject();
        if (false == $Verification_Model->checkRequestSession($account_id, $session)) {
            return OPT_CONST::NO_RETURN;
        }

        if (!isset($data['room_id']) || trim($data['room_id']) == G_CONST::EMPTY_STRING) {
            $this->logMessage('error', "function(readyStart):lack of room_id" . " in file" . __FILE__ . " on Line " . __LINE__);
            return $this->_missingPrameterArr($operation, "room_id");
        }

        $room_id = $data['room_id'];

        $is_legal = $this->checkRequestClientLegal($client_id, $room_id, $account_id);
        if (!$is_legal) {
            $this->logMessage('error', "function(checkRequestClientLegal):illegal opt: account_id($account_id) room_id($room_id)" . " in file" . __FILE__ . " on Line " . __LINE__);
            //推送强制下线
            $illegal_message = $this->_JSON(array("result" => "-203", "result_message" => "illegal opt"));
            Gateway::sendToCurrentClient($illegal_message);
            return OPT_CONST::NO_RETURN;
        }

        $Redis_Model = Redis_Model::getModelObject();
        $replyArr    = array("[roomid]" => $room_id, "[accountid]" => $account_id);

        //获取玩家当前状态，是否未准备
        $rStatus = $this->queryAccountStatus($room_id, $account_id);
        if (Redis_CONST::DATA_NONEXISTENT === $rStatus || !in_array($rStatus, [Game::AccountStatus_Initial, Game::AccountStatus_Notready])) {
            $this->writeLog("function(readyStart):account($account_id) status($rStatus) invalid" . " in file" . __FILE__ . " on Line " . __LINE__);
            return array("result" => OPT_CONST::FAILED, "operation" => $operation, "data" => $result, "result_message" => "状态异常");
        }

        $MMYSQL = $this->initMysql();

        //判断房间申请记录是否存在
        $roomInfo = $MMYSQL->select('is_close,club_id,bean_type')->from(Room)->where('room_id=' . $room_id)->row();
        if (empty($roomInfo)) {
            return ['result' => OPT_CONST::FAILED, 'operation' => $operation, 'data' => $result, 'result_message' => '房间不存在'];
        }
        if ($roomInfo['is_close']) {
            return ['result' => OPT_CONST::FAILED, 'operation' => $operation, 'data' => $result, 'result_message' => '房间已关闭'];
        }

        // 与房主同一个公会，且豆数满足准入规则
        $clubPlayer = $MMYSQL->select('data_id,player_bean')->from(Club_Players)->where('club_id=' . $roomInfo['club_id'] . ' and player_id=' . $account_id . ' and player_status=1')->row();
        if (empty($clubPlayer)) {
            return ['result' => OPT_CONST::FAILED, 'operation' => $operation, 'data' => $result, 'result_message' => '您不在此公会中'];
        }

        $beanTypes = [1=>Config::Bean_Type_1, 2=>Config::Bean_Type_2, 3=>Config::Bean_Type_3, 4=>Config::Bean_Type_4];
        $minBean   = isset($beanTypes[$roomInfo['bean_type']]) ? $beanTypes[$roomInfo['bean_type']] : $beanTypes[1];
        if ($clubPlayer['player_bean'] < $minBean) {
            return ['result' => OPT_CONST::FAILED, 'operation' => $operation, 'data' => $result, 'result_message' => '您的欢乐豆不足'];
        }

        $MMYSQL->update(Club_Players)->cols(['is_gaming' => 1])->where('data_id=' . $clubPlayer['data_id'])->query();

        //更新用户状态
        $rStatus = Game::AccountStatus_Ready;
        $this->updateAccountStatus($room_id, $account_id, $rStatus);

        $this->startGame($room_id);

        return OPT_CONST::NO_RETURN;
    }

    /*
        取消准备操作
    */
    public function readyCancel($arrData)
    {
        $timestamp = time();
        $result    = array();
        $return    = array();

        $data       = $arrData['data'];
        $operation  = $arrData['operation'];
        $account_id = $arrData['account_id'];
        $client_id  = $arrData['client_id'];

        $session            = $arrData['session'];
        $Verification_Model = Verification_Model::getModelObject();
        if (false == $Verification_Model->checkRequestSession($account_id, $session)) {
            return OPT_CONST::NO_RETURN;
        }

        if (!isset($data['room_id']) || trim($data['room_id']) == G_CONST::EMPTY_STRING) {
            $this->logMessage('error', "function(readyCancel):lack of room_id" . " in file" . __FILE__ . " on Line " . __LINE__);
            return $this->_missingPrameterArr($operation, "room_id");
        }

        $room_id = $data['room_id'];

        $Redis_Model = Redis_Model::getModelObject();
        $replyArr    = array("[roomid]" => $room_id, "[accountid]" => $account_id);

        //设置当前状态，已准备
        //更新用户状态
        $rStatus = Game::AccountStatus_Notready;
        $this->updateAccountStatus($room_id, $account_id, $rStatus);

        $ready_count = $this->queryReadyCount($room_id);
        if ($ready_count < 2) {
            //取消倒计时
            $this->deleteRoomTimer($room_id);
            $arr = array("result" => 0, "operation" => "CancelStartLimitTime", "data" => array(), "result_message" => "取消开局倒计时");
            $this->pushMessageToGroup($room_id, $arr);
        }

        return OPT_CONST::NO_RETURN;
    }

    /*
        历史积分榜
    */
    public function historyScoreboard($arrData)
    {
        $result = array();

        $data       = $arrData['data'];
        $operation  = $arrData['operation'];
        $account_id = $arrData['account_id'];
        $client_id  = $arrData['client_id'];

        $session            = $arrData['session'];
        $Verification_Model = Verification_Model::getModelObject();
        if (false == $Verification_Model->checkRequestSession($account_id, $session)) {
            return OPT_CONST::NO_RETURN;
        }

        $MMYSQL = $this->initMysql();
        if (isset($data['room_number']) && $data['room_number'] > 0) {
            $room_number = $data['room_number'];
            //判断房间申请记录是否存在
            $room_where = 'room_number=' . $room_number;
            $room_sql   = 'select room_id,account_id,is_close from ' . Room . ' where ' . $room_where;
            $room_query = $MMYSQL->query($room_sql);
            if (!is_array($room_query) || count($room_query) == 0) {
                $this->writeLog("function(lastScoreboard):room($room_number) not exist" . " in file" . __FILE__ . " on Line " . __LINE__);
                return array("result" => OPT_CONST::FAILED, "operation" => $operation, "data" => $result, "result_message" => "房间不存在");
            } else {
                $room_id = $room_query[0]['room_id'];
            }
        } else if (isset($data['room_id']) && $data['room_id'] > 0) {
            $room_id = $data['room_id'];
        } else {
            $this->logMessage('error', "function(historyScoreboard):lack of room_id" . " in file" . __FILE__ . " on Line " . __LINE__);
            return $this->_missingPrameterArr($operation, "room_id");
        }

        $scoreboards = array();
        $game_type = Game::Game_Type;

//		$sql = 'select * from room_scoreboard where room_id='.$room_id.' and game_type='.Game::Game_Type.' order by create_time desc limit 10';
        $sql   = 'select * from room_scoreboard where room_id=' . $room_id . ' and game_type=' . $game_type . ' order by create_time desc limit 10';
        $query = $MMYSQL->query($sql);
        if (!is_array($query)) {
            $this->writeLog("function(historyScoreboard):room($room_id) not exist" . " in file" . __FILE__ . " on Line " . __LINE__);
            //return array("result"=>OPT_CONST::FAILED,"operation"=>$operation,"data"=>$result,"result_message"=>"没积分榜");
        } else {
            foreach ($query as $row) {

                $name_board  = array();
                $scoreboard  = json_decode($row['board']);
                $create_time = $row['create_time'];

                if ($scoreboard) {
                    foreach ($scoreboard as $account_id => $score) {
                        $account_sql  = 'select nickname from ' . WX_Account . ' where account_id =' . $account_id;
                        $name         = $MMYSQL->single($account_sql);
                        $name_board[] = array('name' => $name, 'score' => $score);
                    }
                    $scoreboards[] = array('time' => $create_time, 'scoreboard' => $name_board);
                }
            }
        }
        return array("result" => "0", "operation" => $operation, "data" => $scoreboards, "result_message" => "历史积分榜");
    }

    /*
        最后一局积分榜
    */
    public function lastScoreboard($arrData)
    {
        $result = array();

        $data       = $arrData['data'];
        $operation  = $arrData['operation'];
        $account_id = $arrData['account_id'];
        $client_id  = $arrData['client_id'];

        $session            = $arrData['session'];
        $Verification_Model = Verification_Model::getModelObject();
        if (false == $Verification_Model->checkRequestSession($account_id, $session)) {
            return OPT_CONST::NO_RETURN;
        }

        if (!(isset($data['room_number']) && $data['room_number'] > 0)) {
            $this->logMessage('error', "function(lastScoreboard):lack of room_number" . " in file" . __FILE__ . " on Line " . __LINE__);
            return $this->_missingPrameterArr($operation, "room_number");
        }

        $room_number = $data['room_number'];
        $MMYSQL      = $this->initMysql();
        //判断房间申请记录是否存在
        $room_where = 'room_number=' . $room_number;
        $room_sql   = 'select room_id,account_id,is_close from ' . Room . ' where ' . $room_where;
        $room_query = $MMYSQL->query($room_sql);
        if (!is_array($room_query) || count($room_query) == 0) {
            $this->writeLog("function(lastScoreboard):room($room_number) not exist" . " in file" . __FILE__ . " on Line " . __LINE__);
            return array("result" => OPT_CONST::FAILED, "operation" => $operation, "data" => $result, "result_message" => "房间不存在");
        } else {
            $room_id = $room_query[0]['room_id'];
        }
        $game_type = Game::Game_Type;

        $scoreboards = new stdClass();
//		$sql = 'select board,create_time,game_num,rule_text from room_scoreboard where room_id='.$room_id.' and game_type='.Game::Game_Type.' order by create_time desc limit 1';
        $sql   = 'select board,create_time,game_num,rule_text from room_scoreboard where room_id=' . $room_id . ' and game_type=' . $game_type . ' order by create_time desc limit 1';
        $query = $MMYSQL->query($sql);
        if (!is_array($query)) {
            $this->writeLog("function(lastScoreboard):room($room_id) not exist" . " in file" . __FILE__ . " on Line " . __LINE__);
        } else {
            foreach ($query as $row) {

                $name_board  = array();
                $scoreboard  = json_decode($row['board']);
                $create_time = $row['create_time'];
                $game_num    = $row['game_num'];
                if ($game_num <= 0) {
                    $game_num = Config::GameNum_EachRound;
                }

                $total_num       = "";
                $rule_text       = $row['rule_text'];
                $rule_text_array = explode('局/', $rule_text);
                if (is_array($rule_text_array) && count($rule_text_array) > 0) {
                    $total_num = $rule_text_array[0];
                }

                if ($scoreboard) {
                    foreach ($scoreboard as $account_id => $score) {
                        $account_sql  = 'select nickname from ' . WX_Account . ' where account_id =' . $account_id;
                        $name         = $MMYSQL->single($account_sql);
                        $name_board[] = array('name' => $name, 'score' => $score, 'account_id' => $account_id);
                    }
                    $scoreboards = array('time' => $create_time, 'scoreboard' => $name_board, 'game_num' => $game_num, 'total_num' => $total_num);
                }
            }
        }

        return array("result" => "0", "operation" => $operation, "data" => $scoreboards, "result_message" => "历史积分榜");
    }

}