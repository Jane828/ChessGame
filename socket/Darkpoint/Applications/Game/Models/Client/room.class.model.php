<?php

use \GatewayWorker\Lib\Gateway;

require_once dirname(__DIR__) . '/public.class.model.php';

class Room_Model extends Public_Model {

    /***************************
     * common function
     ***************************/


    /*
        断线
    */
    public function userDisconnected($arrData) {
        $timestamp = time();

        if (!isset($arrData['gaming_roomid']) || trim($arrData['gaming_roomid']) == G_CONST::EMPTY_STRING) {
            return FALSE;
        }
        if (!isset($arrData['account_id']) || trim($arrData['account_id']) == G_CONST::EMPTY_STRING) {
            return FALSE;
        }

        $REMOTE_ADDR = "unknow";
        if (isset($arrData['REMOTE_ADDR'])) {
            $REMOTE_ADDR = $arrData['REMOTE_ADDR'];
        }

        $room_id    = $arrData['gaming_roomid'];
        $account_id = $arrData['account_id'];
        $client_id  = $arrData['client_id'];

        $clients_of_groupid = Gateway::getClientSessionsByGroup($room_id);
        $clients_of_groupid = array_keys($clients_of_groupid);

        if ($this->queryOnlineStatus($room_id, $account_id)) {
            $this->writeLog("[$room_id] [$account_id] is still in room");
            return TRUE;
        }

        $Redis_Model = Redis_Model::getModelObject();
        $replyArr    = array("[roomid]" => $room_id, "[accountid]" => $account_id);

        //没开局玩过的用户离线，自动从房间中退出
        $RoomSequence_Key = strtr(Redis_Const::RoomSequence_Key, $replyArr);
        $RoomAudience_Key = strtr(Redis_Const::RoomAudience_Key, $replyArr);

        //在游戏中断开连接
        $rsSeq_score = $Redis_Model->getZscore($RoomSequence_Key, $account_id);
        if (Redis_CONST::DATA_NONEXISTENT !== $rsSeq_score) {
            if ($this->queryTicketChecked($room_id, $account_id) == 0) {
                $RoomSequence_Key = strtr(Redis_Const::RoomSequence_Key, $replyArr);
                $zrem_result      = $Redis_Model->zremSet($RoomSequence_Key, array($account_id));
                if($zrem_result == OPT_CONST::DATA_NONEXISTENT){
                    $this->logMessage('error', "function(userDisconnected):delete RoomSequence_Key rid:[$account_id];".' in file ' . __FILE__ . ' on Line ' . __LINE_);
                }

                //判断是否为包厢房间,恢复包厢空房记录
                $Room_Key   = strtr(Redis_CONST::Room_Key, $replyArr);
                $box_number = $Redis_Model->hgetField($Room_Key, Redis_CONST::Room_field_box);
                if ($box_number && $box_number != Game::ORDINARY_ROOM) {
                    $replyArr["[boxnumber]"] = $box_number;
                    $BoxAccount_Key          = strtr(Redis_CONST::BoxAccount_Key, $replyArr);
                    $BoxEmptyRoom_Key        = strtr(Redis_CONST::BoxEmptyRoom_Key, $replyArr);
                    $hdel_result             = $Redis_Model->hdelFiled($BoxAccount_Key, $box_number);
                    if (!$hdel_result) {
                        $this->logMessage('error', "function(userDisconnected):delete BoxAccount_Key box:[$box_number] rid:[$room_id] aid:[$account_id] fail;" . " in file " . __FILE__ . " on Line " . __LINE__);
                    }
                    $rsRoom_set = $Redis_Model->saddSet($BoxEmptyRoom_Key, array("$room_id"));
                    if ($rsRoom_set == OPT_CONST::DATA_NONEXISTENT) {
                        $this->logMessage('error', "function(userDisconnected):set BoxEmptyRoom_Key  box:[$box_number] rid:[$room_id] fail;" . " in file " . __FILE__ . " on Line " . __LINE__);
                    }
                }

                $account_status = Game::AccountStatus_Initial;

            } else if ($this->queryAccountStatus($room_id, $account_id) == Game::AccountStatus_Watch) {
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
                return TRUE;
            }

            if ($room_status == Game::RoomStatus_Waiting) {
                $ready_count = $this->queryReadyCount($room_id);
                if ($ready_count < 2) {
                    //取消倒计时
                    $this->deleteRoomTimer($room_id);
                    $arr = array("result" => 0, "operation" => "CancelStartLimitTime", "data" => array(), "result_message" => "用户离线取消开局倒计时");
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
        }

        //在观战中断开连接
        $rsAud_score = $Redis_Model->getZscore($RoomAudience_Key, $account_id);
        if (Redis_CONST::DATA_NONEXISTENT !== $rsAud_score) {
            $this->updateAudienceInfo($room_id, $account_id, Game::AudienceStatus_off);
        }
        return TRUE;
    }



    /***************************
     * logic function
     ***************************/
    //获取房间的最大人数
    protected function getRoomMaxMember($game_type) {
        switch ($game_type) {
            case Game::Game_DarkPo13_Type:
                $maxCount = Game::GameUser_MaxCount13;
                break;
            case Game::Game_DarkPo16_Type:
                $maxCount = Game::GameUser_MaxCount16;
                break;
            default:
                $maxCount = Game::GameUser_MaxCount10;
                break;
        }

        return $maxCount;
    }

    /**
     * 加入包厢
     * @param $arrData
     */
    public function joinbox($arrData) {
        $data       = $arrData['data'];
        $operation  = $arrData['operation'];
        $account_id = $arrData['account_id'];
        $client_id  = $arrData['client_id'];

        $session            = $arrData['session'];
        $Verification_Model = Verification_Model::getModelObject();
        if (FALSE == $Verification_Model->checkRequestSession($account_id, $session)) {
            return OPT_CONST::NO_RETURN;
        }

        //判断是否有包厢房间游戏记录
        $Redis_Model    = Redis_Model::getModelObject();
        $box_number     = $data["box_number"];
        $replyArr       = array("[boxnumber]" => $box_number, "[accountid]" => $account_id);
        $BoxAccount_Key = strtr(Redis_CONST::BoxAccount_Key, $replyArr);

        $rsroom_id = $Redis_Model->hgetField($BoxAccount_Key, $box_number);
        if ($rsroom_id) {
            $replyArr["[roomid]"] = $rsroom_id;
            $Room_Key             = strtr(Redis_CONST::Room_Key, $replyArr);
            $r_mkv                = array(Redis_CONST::Room_Field_Status, Redis_CONST::Room_Field_Number, Redis_CONST::Room_Field_BankerMode);
            $mget_result          = $Redis_Model->hmgetField($Room_Key, $r_mkv);
            if ($mget_result && isset($mget_result[0]) && $mget_result[0] != Game::RoomStatus_Closed) {
                $result = array(
                    "is_close" => G_CONST::IS_FALSE,
                    "room_status" => $mget_result[0],
                    "room_number" => $mget_result[1],
                    "banker_mode" => $mget_result[2]
                );
                return array("result" => OPT_CONST::SUCCESS, "operation" => $operation, "data" => $result, "result_message" => "加入包厢房间");
            }
        }

        //判断是否有权限
        $MMYSQL     = $this->initMysql();
        $box_info   = $MMYSQL->select("box_id, account_id, box_number, game_type, status, box_name, config")->from(Box_Info)->where("box_number=" . $box_number)->row();
        $manager_id = $box_info["account_id"];
        if (empty($box_info)) {
            $this->logMessage('error', "function(joinbox):the box not found;" . " in file " . __FILE__ . " on Line " . __LINE__);
            return array("result" => OPT_CONST::FAILED, "operation" => $operation, "data" => [], "result_message" => "包厢不存在");
        }

        if ($manager_id != $account_id) {
            $join_where = 'account_id=' . $account_id . ' and manager_id=' . $manager_id . ' and status=1 and is_delete=0';
            $join_query = $MMYSQL->select("member_id")->from(Manage_Member)->where($join_where)->row();
            if (empty($join_query)) {
                return array('result' => OPT_CONST::FAILED, 'data' => [], 'result_message' => "好友才能加入管理员包厢");
            }
        }

        //判断包厢是否有空房间
        $BoxEmptyRoom_Key = strtr(Redis_CONST::BoxEmptyRoom_Key, $replyArr);
        $rsroom_score     = $Redis_Model->smembersSet($BoxEmptyRoom_Key);
        if ($rsroom_score && is_array($rsroom_score)) {
            foreach ($rsroom_score as $key => $value) {
                $replyArr["[roomid]"] = $value;
                $Room_Key             = strtr(Redis_CONST::Room_Key, $replyArr);
                $r_mkv                = array(Redis_CONST::Room_Field_Status, Redis_CONST::Room_Field_Number, Redis_CONST::Room_Field_BankerMode);
                $mget_result          = $Redis_Model->hmgetField($Room_Key, $r_mkv);
                if ($mget_result && isset($mget_result[0]) && $mget_result[0] != Game::RoomStatus_Closed) {
                    $result = array(
                        "is_close" => G_CONST::IS_FALSE,
                        "room_status" => $mget_result[0],
                        "room_number" => $mget_result[1],
                        "banker_mode" => $mget_result[2]
                    );
                    return array("result" => OPT_CONST::SUCCESS, "operation" => $operation, "data" => $result, "result_message" => "加入包厢房间");
                } else {
                    $Redis_Model->sremSet($BoxEmptyRoom_Key, array("$value"));
                }
            }
        }
        //判断包厢是否暂停
        if ($box_info['status'] != Game::Enable_Box) {
            return array('result' => OPT_CONST::FAILED, 'data' => [], 'result_message' => "管理员暂停使用该包厢");
        }

        //获取包厢配置
        $box_config = isset($box_info['config']) ? json_decode($box_info['config'], TRUE) : NULL;
        if (empty($box_config)) {
            $this->logMessage('error', 'function(joinbox): box config not found;' . ' in file ' . __FILE__ . ' on Line ' . __LINE__);
            return array("result" => OPT_CONST::FAILED, "operation" => $operation, "data" => [], "result_message" => "包厢配置不存在");
        }

        //创建新房间
        $box_config['data_key'] = time() . rand(10000, 99999);
        $arrData['data']        = $box_config;
        $arrData['manager_id']  = $manager_id;
        $result                 = $this->createRoom($arrData, G_CONST::IS_TRUE);
        if (!is_array($result)) {
            $this->logMessage('error', 'function(joinbox):create box_room fail;' . ' in file ' . __FILE__ . ' on Line ' . __LINE__);
            return $result;
        } else if ($result["result"] == OPT_CONST::SUCCESS) {
            //保存包厢-房间记录
            $r_array['box_id']      = $box_info['box_id'];
            $r_array['box_number']  = intval($box_info['box_number']);
            $r_array['room_id']     = $result['data']['room_id'];
            $r_array['room_number'] = $result['data']['room_number'];
            $r_array['create_time'] = date('Y-m-d H:i:s');
            $MMYSQL->insertReturnID(Box_Room, $r_array);

            //记录包厢空房间
            $newroom_id = $result['data']['room_id'];
            $rsRoom_set = $Redis_Model->saddSet($BoxEmptyRoom_Key, array("$newroom_id"));
            if ($rsRoom_set != OPT_CONST::SUCCESS) {
                $this->logMessage('error', "function(joinbox):set box_empty_room fail:key:[$BoxEmptyRoom_Key],room_id:[$newroom_id];" . ' in file ' . __FILE__ . ' on Line ' . __LINE__);
            }
        }
        $result["operation"] = $operation;
        return $result;
    }

    /*
         在配置文件中读取允许创建房间的账号列表，如果账号在列表中，运行创建房间，否则不允许创建房间
     */
    public function allowCreateRoom($account_id) {
        $accountids = file_get_contents("/data/conf/whitelist.txt");
        if($accountids == FALSE){
            echo "allowCreateRoom no whitelist ,allow all accountid";
            return TRUE;
        }
        $list = explode(",", $accountids);
        //删除空行和重复账号
        $list = array_filter($list);
        $list = array_unique ($list);
        var_dump($list);
        if(in_array($account_id,$list))
        {
            echo "allow CreateRoom  whitelist";
            return TRUE;
        }

        return FALSE;
    }

    /*
        创建房间
    */
    public function createRoom($arrData, $bflag = G_CONST::IS_FALSE) {

        $timestamp  = time();
        $result     = array();
        $return     = array();
        $data       = $arrData['data'];
        $operation  = $arrData['operation'];
        $account_id = $arrData['account_id'];

        $session            = $arrData['session'];
        $Verification_Model = Verification_Model::getModelObject();
        if (FALSE == $Verification_Model->checkRequestSession($account_id, $session)) {
            return OPT_CONST::NO_RETURN;
        }


        //使用包厢创建者的id创建房间
        if ($bflag == G_CONST::IS_TRUE && isset($arrData['manager_id'])) {
            $account_id = $arrData['manager_id'];
        }

        $allow = $this->allowCreateRoom((string)$account_id);
        if(!$allow){
            return array("result" => OPT_CONST::PERMISSION_DENIED, "operation" => $operation, "result_message" => "非白名单用户。不允许建房!");
        }

        $request_key = array(
            "data_key",
            "game_type",            // 游戏类型
            "ticket_count",         // 扣除的房卡数量
            "banker_mode",          // 抢庄模式
            "chip_type",            // 筹码组
            "upper_limit",          // 上限
            "first_lossrate",       //赔率设置:龙虎出入
            "second_lossrate",     //赔率设:同粘
            "three_lossrate",        // 赔率设置:角串
            "countDown",            // 倒计时组
        );

        $diff = array_diff_key(array_flip($request_key), $data);

        if (count($diff) > 0) {
            $diff_key = implode(",", array_keys($diff));
            $this->logMessage('error', "function(createRoom):lack of " . $diff_key . " in file" . __FILE__ . " on Line " . __LINE__);
            return $this->_missingPrameterArr($operation, $diff_key);
        }

        if (trim($data['data_key']) == G_CONST::EMPTY_STRING) {
            $this->logMessage('error', "function(createRoom):invalid param of data_key " . " in file" . __FILE__ . " on Line " . __LINE__);
            return $this->_invalidPrameterArr($operation, "data_key");
        }

        if (!(Game::BankerMode_FreeGrab <= $data['banker_mode'] && $data['banker_mode'] <= Game::BankerMode_RoomownerGrab)) {
            $this->logMessage('error', "function(createRoom):invalid param of banker_mode " . " in file" . __FILE__ . " on Line " . __LINE__);
            return $this->_invalidPrameterArr($operation, "upper_limit");
        }


        if (!(0 <= $data['upper_limit'] && $data['upper_limit'] <= 2000)) {
            $this->logMessage('error', "function(createRoom):invalid param of upper_limit " . " in file" . __FILE__ . " on Line " . __LINE__);
            return $this->_invalidPrameterArr($operation, "upper_limit");
        }

        if (!is_array($data["countDown"]) || count($data["countDown"]) != 5) {
            $this->logMessage('error', "function(createRoom):invalid param of countDown " . " in file" . __FILE__ . " on Line " . __LINE__);
            return $this->_invalidPrameterArr($operation, "countDown");
        }

        $data_key           = $data['data_key'];
        $spend_ticket_count = $data['ticket_count'];
        $total_num          = Config::GameNum_EachRound * $spend_ticket_count;
        $chip_type          = $data['chip_type'];
        $upper_limit        = $data['upper_limit'];
        $game_type          = $data['game_type'];
        $first_lossrate     = $data['first_lossrate'];//tiger dragon exit ,enter loss rate
        $second_lossrate    = $data['second_lossrate'];// same，stick  loss rate
        $three_lossrate     = $data['three_lossrate']; //angle gorge  loss rate
        $countDown_ready    = $data["countDown"][0] <= 30 ? $data["countDown"][0] : Game::LimitTime_Ready;
        $countDown_grab     = $data["countDown"][1] <= 30 ? $data["countDown"][1] : Game::LimitTime_Grab;
        $countDown_put  = $data["countDown"][2] <= 30 ? $data["countDown"][2] : Game::LimitTime_puting;
        $countDown_bet  = $data["countDown"][3] <= 10 ? $data["countDown"][3] : Game::LimitTime_Betting;
        $countDown_show = $data["countDown"][4] <= 10 ? $data["countDown"][4] : Game::LimitTime_Show;
        $banker_mode    = $data['banker_mode'];

        $session            = $arrData['session'];
        $Verification_Model = Verification_Model::getModelObject();
        if (FALSE == $Verification_Model->checkRequestSession($account_id, $session)) {
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
        if ($my_ticket_count >= $spend_ticket_count) {

        } else {
            $this->writeLog("($account_id) 牌券不足");
            $result['alert_type'] = 1;    //1房卡不足
            return array("result" => "1", "operation" => $operation, "data" => $result, "result_message" => "房卡不足");
        }

        //判断房间申请记录是否存在
        $room_where = 'data_key="' . $data_key . '"';
        $room_sql   = 'select room_id,room_number,account_id,is_close from ' . Room . ' where ' . $room_where;
        $room_query = $MMYSQL->query($room_sql);
        if (!is_array($room_query) || count($room_query) == 0) {
            $room_aid = $account_id;
            $is_close = G_CONST::IS_FALSE;

            $r_array['create_time']    = $timestamp;
            $r_array['create_appid']   = "aid_" . $account_id;
            $r_array['update_time']    = $timestamp;
            $r_array['update_appid']   = "aid_" . $account_id;
            $r_array['is_delete']      = G_CONST::IS_FALSE;
            $r_array['data_key']       = $data_key;
            $r_array['account_id']     = $room_aid;
            $r_array['is_close']       = G_CONST::IS_FALSE;
            $r_array['game_type']      = $game_type;
            $r_array['player_max_num'] = $this->getRoomMaxMember($game_type);
            $r_array['box_number']     = isset($data["box_number"]) ? $data["box_number"] : 0;
            $r_array['room_config']    = json_encode($data);

            $room_id = $MMYSQL->insertReturnID(Room, $r_array);
            if ($room_id > 0) {
                $room_number = 10000 + $room_id;
            } else {
                $this->logMessage('error', "function(createRoom):用户" . $account_id . " 创建房间失败：" . " in file" . __FILE__ . " on Line " . __LINE__);
                return array("result" => "-1", "operation" => $operation, "data" => $result, "result_message" => "创建房间失败");
            }

            $num_updateSql   = 'update ' . Room . ' set room_number="' . $room_number . '" where room_id=' . $room_id;
            $num_updateQuery = $MMYSQL->query($num_updateSql);
        } else {
            $room_id     = $room_query[0]['room_id'];
            $room_number = $room_query[0]['room_number'];
            $room_aid    = $room_query[0]['account_id'];
            $is_close    = $room_query[0]['is_close'];
        }

        //添加房间信息到redis
        $Redis_Model = Redis_Model::getModelObject();

        $replyArr = array("[roomid]" => $room_id);
        $Room_Key = strtr(Redis_Const::Room_Key, $replyArr);

        $r_mkv[Redis_Const::Room_Field_Number]          = $room_number;              //房间号
        $r_mkv[Redis_Const::Room_Field_GameRound]       = 1;                         //游戏轮数
        $r_mkv[Redis_Const::Room_Field_GameNum]         = 0;                         //游戏第几局
        $r_mkv[Redis_Const::Room_Field_TotalNum]        = $total_num;
        $r_mkv[Redis_Const::Room_Field_Status]          = Game::RoomStatus_Waiting;  //房间状态，1等待、2进行中、3关闭
        $r_mkv[Redis_Const::Room_Field_Firstlossrate]   = $first_lossrate;            //赔率设置:龙,虎,出,入
        $r_mkv[Redis_Const::Room_Field_Secondlossrate]  = $second_lossrate;            //赔率设置:同,粘
        $r_mkv[Redis_Const::Room_Field_Threelossrate]   = $three_lossrate;            //赔率设置:角,串
        $r_mkv[Redis_Const::Room_Field_TicketCount]     = $spend_ticket_count;
        $r_mkv[Redis_Const::Room_Field_ChipType]        = implode(",", $chip_type);
        $r_mkv[Redis_Const::Room_Field_UpperLimit]      = $upper_limit;
        $r_mkv[Redis_Const::Room_Field_Creator]         = $account_id;               //房间创建者
        $r_mkv[Redis_Const::Room_Field_StartTime]       = -1;
        $r_mkv[Redis_Const::Room_Field_GameType]        = $game_type;
        $r_mkv[Redis_CONST::Room_Field_CountDown_ready] = $countDown_ready;
        $r_mkv[Redis_CONST::Room_Field_CountDown_Grab]  = $countDown_grab;
        $r_mkv[Redis_CONST::Room_Field_CountDown_Put]   = $countDown_put;
        $r_mkv[Redis_CONST::Room_Field_CountDown_Bet]   = $countDown_bet;
        $r_mkv[Redis_CONST::Room_Field_CountDown_Show]  = $countDown_show;
        $r_mkv[Redis_CONST::Room_Field_BankerMode]      = $banker_mode;

        $mset_result = $Redis_Model->hmsetField($Room_Key, $r_mkv);

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
        用房卡激活房间，TODO
    */
    public function activateRoom($arrData) {
        $timestamp = time();
        $result    = array();

        $data       = $arrData['data'];
        $operation  = $arrData['operation'];
        $account_id = $arrData['account_id'];

        $session            = $arrData['session'];
        $Verification_Model = Verification_Model::getModelObject();
        if (FALSE == $Verification_Model->checkRequestSession($account_id, $session)) {
            return OPT_CONST::NO_RETURN;
        }

        if (!(isset($data['room_number']) && $data['room_number'] > 0)) {
            $this->logMessage('error', "function(activateRoom):lack of room_number" . " in file" . __FILE__ . " on Line " . __LINE__);
            return $this->_missingPrameterArr($operation, "room_number");
        }
        if (!isset($data['game_type'])) {
            $this->logMessage('error', "function(activateRoom):lack of game_type" . " in file" . __FILE__ . " on Line " . __LINE__);
            return $this->_missingPrameterArr($operation, "game_type");
        }
        if (!isset($data['ticket_count'])) {
            $this->logMessage('error', "function(activateRoom):lack of ticket_count" . " in file" . __FILE__ . " on Line " . __LINE__);
            return $this->_missingPrameterArr($operation, "ticket_count");
        }
        if (!isset($data['chip_type'])) {
            $this->logMessage('error', "function(activateRoom):lack of chip_type" . " in file" . __FILE__ . " on Line " . __LINE__);
            return $this->_missingPrameterArr($operation, "chip_type");
        }
        if (!isset($data['upper_limit']) || !in_array($data['upper_limit'], [0, 500, 1000, 2000])) {
            $this->logMessage('error', "function(activateRoom):lack of upper_limit" . " in file" . __FILE__ . " on Line " . __LINE__);
            return $this->_missingPrameterArr($operation, "upper_limit");
        }
        if (!isset($data['first_lossrate']) || !in_array($data['first_lossrate'], [3, 2.9, 2.8, 2.7])) {
            $this->logMessage('error', "function(activateRoom):lack of first_lossrate" . " in file" . __FILE__ . " on Line " . __LINE__);
            return $this->_missingPrameterArr($operation, "extraRewards");
        }

        if (!isset($data['second_lossrate']) || !in_array($data['second_lossrate'], [2, 1.9, 1.8])) {
            $this->logMessage('error', "function(activateRoom):lack of second_lossrate" . " in file" . __FILE__ . " on Line " . __LINE__);
            return $this->_missingPrameterArr($operation, "extraRewards");
        }
        if (!isset($data['three_lossrate']) || !in_array($data['three_lossrate'], [1, 0.9])) {
            $this->logMessage('error', "function(activateRoom):lack of first_lossrate" . " in file" . __FILE__ . " on Line " . __LINE__);
            return $this->_missingPrameterArr($operation, "extraRewards");
        }

        if (!isset($data["countDown"])) {
            $this->logMessage('error', "function(createRoom):lack of countDown " . " in file" . __FILE__ . " on Line " . __LINE__);
            return $this->_missingPrameterArr($operation, "countDown");
        }

        $room_number = $data['room_number'];

        $MMYSQL = $this->initMysql();
        //判断房间申请记录是否存在
        $room_where = 'room_number=' . $room_number;
        $room_sql   = 'select room_id,account_id,is_close from ' . Room . ' where ' . $room_where;
        $room_query = $MMYSQL->query($room_sql);
        if (!is_array($room_query) || count($room_query) == 0) {
            $this->writeLog("function(activateRoom):room($room_number) not exist" . " in file" . __FILE__ . " on Line " . __LINE__);
            return array("result" => OPT_CONST::FAILED, "operation" => $operation, "data" => $result, "result_message" => "房间不存在");
        } else {
            $room_id = $room_query[0]['room_id'];
        }

        $data_key           = $data['data_key'];
        $spend_ticket_count = $data['ticket_count'];
        $total_num          = Config::GameNum_EachRound * $spend_ticket_count;
        $chip_type          = $data['chip_type'];
        $upper_limit        = $data['upper_limit'];
        $game_type          = $data['game_type'];
        $first_lossrate     = $data['first_lossrate'];//tiger dragon exit ,enter loss rate
        $second_lossrate    = $data['second_lossrate'];// same，stick  loss rate
        $three_lossrate     = $data['three_lossrate']; //angle gorge  loss rate
        $countDown_ready    = $data["countDown"][0] <= 30 ? $data["countDown"][0] : Game::LimitTime_Ready;
        $countDown_bet      = $data["countDown"][1] <= 30 ? $data["countDown"][1] : Game::LimitTime_Betting;
        $countDown_grab     = $data["countDown"][2] <= 30 ? $data["countDown"][0] : Game::LimitTime_Grab;
        $countDown_put      = $data["countDown"][3] <= 10 ? $data["countDown"][1] : Game::LimitTime_puting;
        $countDown_show     = $data["countDown"][4] <= 10 ? $data["countDown"][1] : Game::LimitTime_Show;

        $room_status = $this->queryRoomStatus($room_id);
        if ($room_status != Game::RoomStatus_Closed) {
            return array("result" => OPT_CONST::FAILED, "operation" => $operation, "data" => $result, "result_message" => "房间已经被激活");
        }

        $my_ticket_count = $MMYSQL->select("ticket_count")->from("room_ticket")->where("account_id=" . $account_id)->single();
        $this->writeLog("[$room_id] ($account_id) 牌券:" . $my_ticket_count);
        if ($my_ticket_count >= $spend_ticket_count) {

        } else {
            $this->writeLog("[$room_id] ($account_id) 牌券不足");
            $result['alert_type'] = 1;    //1房卡不足
            return array("result" => "1", "operation" => $operation, "data" => $result, "result_message" => "房卡不足");
        }

        //添加房间信息到redis
        $Redis_Model = Redis_Model::getModelObject();
        $replyArr    = array("[roomid]" => $room_id);
        $Room_Key    = strtr(Redis_Const::Room_Key, $replyArr);

        $r_mkv[Redis_Const::Room_Field_Number]          = $room_number;              //房间号
        $r_mkv[Redis_Const::Room_Field_GameRound]       = 1;                         //游戏轮数
        $r_mkv[Redis_Const::Room_Field_GameNum]         = 0;                         //游戏第几局
        $r_mkv[Redis_Const::Room_Field_TotalNum]        = $total_num;
        $r_mkv[Redis_Const::Room_Field_Status]          = Game::RoomStatus_Waiting;  //房间状态，1等待、2进行中、3关闭
        $r_mkv[Redis_Const::Room_Field_Firstlossrate]   = $first_lossrate;            //赔率设置:龙,虎,出,入
        $r_mkv[Redis_Const::Room_Field_Secondlossrate]  = $second_lossrate;            //赔率设置:同,粘
        $r_mkv[Redis_Const::Room_Field_Threelossrate]   = $three_lossrate;            //赔率设置:角,串
        $r_mkv[Redis_Const::Room_Field_TicketCount]     = $spend_ticket_count;
        $r_mkv[Redis_Const::Room_Field_ChipType]        = implode(",", $chip_type);
        $r_mkv[Redis_Const::Room_Field_UpperLimit]      = $upper_limit;
        $r_mkv[Redis_Const::Room_Field_Creator]         = $account_id;               //房间创建者
        $r_mkv[Redis_Const::Room_Field_StartTime]       = -1;
        $r_mkv[Redis_Const::Room_Field_GameType]        = $game_type;
        $r_mkv[Redis_CONST::Room_Field_CountDown_ready] = $countDown_ready;
        $r_mkv[Redis_CONST::Room_Field_CountDown_Grab]  = $countDown_grab;
        $r_mkv[Redis_CONST::Room_Field_CountDown_Put]   = $countDown_put;
        $r_mkv[Redis_CONST::Room_Field_CountDown_Bet]   = $countDown_bet;
        $r_mkv[Redis_CONST::Room_Field_CountDown_Show]  = $countDown_show;

        $mset_result = $Redis_Model->hmsetField($Room_Key, $r_mkv);

        //扣除房卡  房主扣卡模式
        if (Config::Ticket_Mode == 2) {
            $this->balanceTicket($room_id, $account_id, $spend_ticket_count);
        }

        $this->writeLog("[$room_id] ($account_id) 激活房间");
        $result['room_id'] = $room_id;
        return array("result" => OPT_CONST::SUCCESS, "operation" => $operation, "data" => $result, "result_message" => "创建房间");
    }


    /*
        进入房间
    */
    public function joinRoom($arrData) {
        $timestamp = time();
        $result    = array();
        $return    = array();

        $data       = $arrData['data'];
        $operation  = $arrData['operation'];
        $account_id = $arrData['account_id'];
        $client_id  = $arrData['client_id'];

        $session            = $arrData['session'];
        $Verification_Model = Verification_Model::getModelObject();
        if (FALSE == $Verification_Model->checkRequestSession($account_id, $session)) {
            return OPT_CONST::NO_RETURN;
        }

        if (!(isset($data['room_number']) && $data['room_number'] > 0)) {
            $this->logMessage('error', "function(joinRoom):lack of room_number" . " in file" . __FILE__ . " on Line " . __LINE__);
            return $this->_missingPrameterArr($operation, "room_number");
        }

        $room_number = $data['room_number'];

        $MMYSQL      = $this->initMysql();
        $Redis_Model = Redis_Model::getModelObject();
        //判断房间申请记录是否存在

        $room_where = 'room_number=' . $room_number;
        $room_sql   = 'select room_id,account_id,is_close, box_number from ' . Room . ' where ' . $room_where;
        $room_query = $MMYSQL->query($room_sql);
        if (!is_array($room_query) || count($room_query) == 0) {
            $this->logMessage('error', "function(joinRoom):room($room_number) not exist" . " in file" . __FILE__ . " on Line " . __LINE__);
            return array("result" => OPT_CONST::FAILED, "operation" => $operation, "data" => $result, "result_message" => "房间不存在");
        } else {
            $room_id = $room_query[0]['room_id'];
            if ($room_query[0]['is_close']) {
                $result['room_status'] = 4;
                if ($room_query[0]['box_number']) {
                    //如果是包厢房间关闭，清除包厢空房记录
                    $BoxEmptyRoom_Key = strtr(Redis_CONST::BoxEmptyRoom_Key, array("[boxnumber]" => $room_query[0]['box_number']));
                    $Redis_Model->sremSet($BoxEmptyRoom_Key, array("$room_id"));
                }
                return array("result" => "0", "operation" => $operation, "data" => $result, "result_message" => "房间已关闭");
            }
        }

        $replyArr = array("[roomid]" => $room_id, "[accountid]" => $account_id);

        $RoomSequence_Key = strtr(Redis_Const::RoomSequence_Key, $replyArr);
        $RoomAudience_Key = strtr(Redis_Const::RoomAudience_Key, $replyArr);

        //总分数
        $RoomScore_Key        = strtr(Redis_Const::RoomScore_Key, $replyArr);
        $RoomScore_Field_User = strtr(Redis_Const::RoomScore_Field_User, $replyArr);

        //更新观战座位
        $rsAud_score = $Redis_Model->getZscore($RoomAudience_Key, $account_id);
        if (Redis_CONST::DATA_NONEXISTENT !== $rsAud_score) {
            $this->updateAudienceInfo($room_id, $account_id, Game::AudienceStatus_off);
        }

        $rsSeq_score = $Redis_Model->getZscore($RoomSequence_Key, $account_id);

        $setting   = $this->queryRoomSetting($room_id);
        $game_type = isset($setting[Redis_Const::Room_Field_GameType]) ? $setting[Redis_Const::Room_Field_GameType] : Game::Game_DarkPo10_Type;

        //判断用户是否已加入房间
        if (Redis_CONST::DATA_NONEXISTENT !== $rsSeq_score)        //已加入
        {
            //获取分数
            $rScore_score = $Redis_Model->hgetField($RoomScore_Key, $RoomScore_Field_User);
            //获取用户所在位置
            $serial_num     = $rsSeq_score;
            $account_status = $this->queryAccountStatus($room_id, $account_id);
            if ($account_status == Game::AccountStatus_Watch) {
                $account_status = Game::AccountStatus_Notready;
                $this->updateAccountStatus($room_id, $account_id, $account_status);
            }
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

            $maxCount = $this->getRoomMaxMember($game_type);

            if ($user_count >= $maxCount) {
                $this->writeLog("function(joinRoom):room($room_number) 人数已满" . " in file" . __FILE__ . " on Line " . __LINE__);
                $result['alert_type'] = 2;    //2人数已满
                return array("result" => "1", "operation" => $operation, "data" => $result, "result_message" => "房间人数已满");
            }

            $serial_num = -1;
            for ($i = 1; $i <= $maxCount; $i++) {
                if (array_search($i, $gamer_query) === FALSE) {
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

        Gateway::bindUid($client_id, $RoomUser_UID);
        Gateway::joinGroup($client_id, $room_id);

        //获取房间当前局数
        $game_num                   = $this->queryGameNumber($room_id);
        $room_status                = $this->queryRoomStatus($room_id);
        $room_ary['room_id']        = $room_id;
        $room_ary['room_number']    = $room_number;
        $room_ary['room_status']    = $room_status;
        $room_ary['account_score']  = $rScore_score;
        $room_ary['account_status'] = $account_status;
        $room_ary['online_status']  = $this->queryOnlineStatus($room_id, $account_id);

        $room_ary['serial_num'] = $serial_num;
        $room_ary['game_num']   = $game_num;
        $room_ary['total_num']  = $this->queryTotalNum($room_id);

        $room_ary['ticket_checked'] = $this->queryTicketChecked($room_id, $account_id);
        $room_ary['scoreboard']     = $this->queryScoreboard($room_id);

        $room_ary['pool_score'] = $this->queryPoolScore($room_id);

        $setting                  = $this->queryRoomSetting($room_id);
        $room_ary['ticket_count'] = isset($setting[Redis_Const::Room_Field_TicketCount]) ? $setting[Redis_Const::Room_Field_TicketCount] : 1;
        $room_ary['chip_type']    = isset($setting[Redis_Const::Room_Field_ChipType]) ? explode(",", $setting[Redis_Const::Room_Field_ChipType]) : array(2, 4, 5, 8);

        $room_ary['upper_limit'] = isset($setting[Redis_Const::Room_Field_UpperLimit]) ? $setting[Redis_Const::Room_Field_UpperLimit] : 1000;
        $room_ary['game_type']   = isset($setting[Redis_Const::Room_Field_GameType]) ? $setting[Redis_Const::Room_Field_GameType] : 1;

        // 当局下注总分
        $room_ary['chip'] = $this->queryChip($room_id, $account_id);

        //推送房间信息
        $room_return = array("result" => OPT_CONST::SUCCESS, "operation" => $operation, "data" => $room_ary, "result_message" => "加入房成功");
        $this->pushMessageToCurrentClient($room_return);
        $banker_mode = $this->queryBankerMode($room_id);
        if ( $banker_mode == Game::BankerMode_RoomownerGrab) {
            $owner_id = $this->queryCreator($room_id);
            $this->updateBanker($room_id, $owner_id);
        }
        //返回所有玩家状态给进房玩家
        $allGamer    = $this->getGamerInfo($room_id);
        $allAudience = $this->getAudienceInfo($room_id);
        $chipAll = $this->getChipArray($room_id);
        //$timeid = $this->queryRoomTimerId($room_id);

        if (is_array($allGamer) && is_array($allAudience)) {
            $currentGamer_return = array("result" => OPT_CONST::SUCCESS,
                "operation" => "AllGamerInfo", "data" => $allGamer, "audience" => $allAudience,"chipAll"=>$chipAll,
                "result_message" => "所有玩家状态");
            $this->pushMessageToCurrentClient($currentGamer_return);
        }


        //推送当前玩家状态给其他玩家
        $currentGamer   = $this->getGamerInfo($room_id, $account_id);
        $curentAudience = $this->getAudienceInfo($room_id, $account_id);
        if (is_array($currentGamer) && is_array($curentAudience)) {
            $currentGamer_return = array("result" => OPT_CONST::SUCCESS, "operation" => "UpdateGamerInfo", "data" => $currentGamer, "audience" => $curentAudience, "result_message" => "某玩家状态");
            $this->pushMessageToGroup($room_id, $currentGamer_return, $client_id);
        }

        //推送之前对局结果给进房的用户
        $game_result = $this->queryRoomGameEndData($room_id, $game_type);
        if (is_array($game_result)) {
            $push_return = array("result" => OPT_CONST::SUCCESS, "operation" => "AllGameEndData", "data" => $game_result, "result_message" => "进入房间对局结果");
            $this->pushMessageToCurrentClient($push_return);
        }

        //显示房间目前的倒计时
        $limit = isset($setting[Redis_Const::Room_Field_CountDown_ready]) ? $setting[Redis_Const::Room_Field_CountDown_ready] : Game::LimitTime_Ready;

        if ($room_status == Game::RoomStatus_Waiting && ($countdown = $this->queryCountdown($room_id, $limit)) > 0) {
            $arr = array("result" => 0, "operation" => "StartLimitTime", "data" => array('limit_time' => $countdown), "result_message" => "开始倒计时");
            $this->pushMessageToCurrentClient($arr);
        }
        $this->writeLog("joinRoom [$room_status]  房屋状态和准备倒计时");
        //保存用户当前房间,用户ID
        $_SESSION['gaming_roomid'] = $room_id;
        $_SESSION['account_id']    = $account_id;

        $this->writeLog("[$room_id] $account_id 进入房间");
        return OPT_CONST::NO_RETURN;
    }


    /**
     * 加入观战
     * @param $arrData
     */
    public function audience($arrData) {
        $result     = array();
        $data       = $arrData['data'];
        $operation  = $arrData['operation'];
        $account_id = $arrData['account_id'];
        $client_id  = $arrData['client_id'];

        $session            = $arrData['session'];
        $Verification_Model = Verification_Model::getModelObject();
        if (FALSE == $Verification_Model->checkRequestSession($account_id, $session)) {
            return OPT_CONST::NO_RETURN;
        }

        if (!(isset($data['room_number']) && $data['room_number'] > 0)) {
            $this->logMessage('error', "function(audience):lack of room_number" . " in file" . __FILE__ . " on Line " . __LINE__);
            return $this->_missingPrameterArr($operation, "room_number");
        }
        $room_number = $data['room_number'];

        //判断房间是否存在
        $MMYSQL   = $this->initMysql();
        $room_sql = 'select room_id,account_id,is_close from ' . Room . ' where room_number=' . $room_number;
        $room_row = $MMYSQL->row($room_sql);
        if (!$room_row) {
            $this->logMessage('error', "function(audience):room($room_number) not exist" . " in file" . __FILE__ . " on Line " . __LINE__);
            return array("result" => OPT_CONST::FAILED, "operation" => $operation, "data" => $result, "result_message" => "房间不存在");
        } else {
            $room_id = $room_row['room_id'];
            if ($room_row['is_close']) {
                $result = array('room_status' => 4);
                return array("result" => 0, "operation" => $operation, "data" => $result, "result_message" => "房间已关闭");
            }
        }

        $Redis_Model = Redis_Model::getModelObject();

        $replyArr         = array("[roomid]" => $room_id, "[accountid]" => $account_id);
        $RoomSequence_Key = strtr(Redis_Const::RoomSequence_Key, $replyArr);

        $rsSeq_score = $Redis_Model->getZscore($RoomSequence_Key, $account_id);
        $seat_num    = 1;

        //获取房间庄家
        $banker_id = $this->queryBanker($room_id);
        if ($banker_id == $account_id) {
            return array("result" => 0, "operation" => $operation, "data" => array('room_status' => 4, 'to_joinRoom' => 1), "result_message" => "庄家不允许加入观战");
        }

        $ticketStatus = $this->queryTicketChecked($room_id, $account_id);
        if (Redis_CONST::DATA_NONEXISTENT !== $rsSeq_score)        //已加入游戏
        {
            //没开局玩过的用户离线，自动从房间中退出
            if ($ticketStatus == 0) {
                $RoomSequence_Key = strtr(Redis_Const::RoomSequence_Key, $replyArr);
                $zrem_result      = $Redis_Model->zremSet($RoomSequence_Key, array($account_id));
                $account_status   = Game::AccountStatus_Initial;
                $seat_num         = $rsSeq_score;
                $this->updateAccountStatus2($room_id, $account_id, $account_status, 0);
                //判断是否为包厢房间
                $Room_Key = strtr(Redis_CONST::Room_Key, $replyArr);
                $box_number = $Redis_Model->hgetField($Room_Key, Redis_CONST::Room_field_box);
                if($box_number && $box_number != Game::ORDINARY_ROOM){
                    $replyArr["[boxnumber]"] = $box_number;
                    $BoxAccount_Key = strtr(Redis_CONST::BoxAccount_Key, $replyArr);
                    $BoxEmptyRoom_Key = strtr(Redis_CONST::BoxEmptyRoom_Key, $replyArr);
                    $hdel_result = $Redis_Model->hdelFiled($BoxAccount_Key, $box_number);
                    if(!$hdel_result){
                        $this->logMessage('error', "function(joinRoom):delete BoxAccount_Key box:[$box_number] rid:[$room_id] aid:[$account_id];" . " in file " . __FILE__ . " on Line " . __LINE__);
                    }
                    $rsRoom_set = $Redis_Model->saddSet($BoxEmptyRoom_Key, array("$room_id"));
                    if($rsRoom_set == OPT_CONST::DATA_NONEXISTENT){
                        $this->logMessage('error', "function(audienceWatch):set BoxEmptyRoom_Key  box:[$box_number] rid:[$room_id] fail;" . " in file " . __FILE__ . " on Line " . __LINE__);
                    }
                }
            } else {
                $account_status = $this->queryAccountStatus($room_id, $account_id);
                if ($account_status != Game::AccountStatus_Notready && $account_status != Game::AccountStatus_Initial && $account_status != Game::AccountStatus_Ready) {
                    return array("result" => 0, "operation" => $operation, "data" => array('room_status' => 4, 'to_joinRoom' => 1), "result_message" => "游戏中不允许加入观战");
                }
                $account_status = Game::AccountStatus_Watch;
                $seat_num       = $rsSeq_score;

                //设置用户状态
                $this->updateAccountStatus2($room_id, $account_id, $account_status, 1);
                $this->writeLog("[$room_id] ($account_id) 离线");
            }
        }

        //获取玩家信息
        $account_info  = array();
        $account_where = 'account_id="' . $account_id . '"';
        $account_sql   = 'select nickname,headimgurl from ' . WX_Account . ' where ' . $account_where;
        $account_query = $MMYSQL->query($account_sql);
        if (!is_array($account_query) || count($account_query) == 0) {
            $this->logMessage('error', "function(getGamerInfo):account($account_id) not exist" . " in file" . __FILE__ . " on Line " . __LINE__);
            return FALSE;
        }
        $account_info['account_id'] = $account_id;
        $account_info['nickname']   = $account_query[0]['nickname'];
        $account_info['headimgurl'] = $account_query[0]['headimgurl'];
        $account_info['seat_num']   = $seat_num;
        $account_info['status']     = Game::AudienceStatus_on;

        //加入观战
        $user_count               = 0;
        $RoomAudience_Key         = strtr(Redis_Const::RoomAudience_Key, $replyArr);
        $RoomAudienceInfo_Key     = strtr(Redis_Const::RoomAudienceInfo_Key, $replyArr);
        $sset_array['key']        = $RoomAudience_Key;
        $sset_array['WITHSCORES'] = "WITHSCORES";

        $gamer_query = $Redis_Model->getSortedSetLimitByAry($sset_array);
        if (Redis_CONST::DATA_NONEXISTENT !== $gamer_query) {
            $user_count = count($gamer_query);
        }
        if ($user_count >= Game::GameAudience_maxCount) {
            $this->writeLog("function(audience):room($room_number) 人数已满" . " in file" . __FILE__ . " on Line " . __LINE__);
            $result['alert_type'] = 2;    //2 表示人数已满
            return array("result" => "1", "operation" => $operation, "data" => $result, "result_message" => "观战人数已满");
        }

        $serial_num = -1;
        for ($i = 1; $i <= Game::GameAudience_maxCount; $i++) {
            if (array_search($i, $gamer_query) === FALSE) {
                $serial_num = $i;
                break;
            }
        }

        if ($serial_num == -1) {
            $this->logMessage('error', "function(audience):serial_num($serial_num) 无法找到空观战位" . " in file" . __FILE__ . " on Line " . __LINE__);
            return array("result" => OPT_CONST::FAILED, "operation" => $operation, "data" => $result, "result_message" => "数据错误");
        }

        $allAudience = $this->getAudienceInfo($room_id);
        if (is_array($allAudience)) {
            $audienceset = $this->getAudienceIDSet($allAudience, $account_id);
            if (FALSE == $audienceset) {
                $account_info['serial_num'] = $serial_num;
                $rAud_mkv[$serial_num]      = $account_id;
                $rAudInfo_mkv[$serial_num]  = json_encode($account_info);
                $zadd_result = $Redis_Model->zaddSet($RoomAudience_Key, $rAud_mkv);
                $zadd_result = $Redis_Model->zaddSet($RoomAudienceInfo_Key, $rAudInfo_mkv);
                $this->writeLog("function(audienceWatch): add XXXXXXX  account_id " . $account_id);
            }

        } else {
            $this->writeLog("function(audienceWatch): not add XXXXXXX" . " in file" . __FILE__);
        }


        //绑定用户UID
        $RoomUser_UID = strtr(Game::RoomUser_UID, $replyArr);
        Gateway::bindUid($client_id, $RoomUser_UID);
        Gateway::joinGroup($client_id, $room_id);

        //获取房间当前局数
        $game_num                = $this->queryGameNumber($room_id);
        $room_status             = $this->queryRoomStatus($room_id);
        $room_ary['room_id']     = $room_id;
        $room_ary['seat_num']    = $seat_num;
        $room_ary['room_number'] = $room_number;
        $room_ary['room_status'] = $room_status;

        $room_ary['game_num']  = $game_num;
        $room_ary['total_num'] = $this->queryTotalNum($room_id);

        $room_ary['ticket_checked'] = $this->queryTicketChecked($room_id, $account_id);
        $room_ary['scoreboard']     = $this->queryScoreboard($room_id);
        $room_ary['pool_score']     = $this->queryPoolScore($room_id);


        $setting = $this->queryRoomSetting($room_id);
        $room_ary['ticket_count'] = isset($setting[Redis_Const::Room_Field_TicketCount]) ? $setting[Redis_Const::Room_Field_TicketCount] : 1;
        $room_ary['chip_type']    = isset($setting[Redis_Const::Room_Field_ChipType]) ? explode(",", $setting[Redis_Const::Room_Field_ChipType]) : array(2, 4, 5, 8);

        $room_ary['upper_limit'] = isset($setting[Redis_Const::Room_Field_UpperLimit]) ? $setting[Redis_Const::Room_Field_UpperLimit] : 1000;
        $room_ary['game_type']   = isset($setting[Redis_Const::Room_Field_GameType]) ? $setting[Redis_Const::Room_Field_GameType] : 1;

        // 当局下注总分
        $room_ary['chip'] = $this->queryChip($room_id, $account_id);

        //推送房间信息
        $room_return = array("result" => OPT_CONST::SUCCESS, "operation" => $operation, "data" => $room_ary, "result_message" => "加入观战成功");
        $this->pushMessageToCurrentClient($room_return);

        //返回所有玩家状态给进房玩家
        $allGamer    = $this->getGamerInfo($room_id);
        $allAudience = $this->getAudienceInfo($room_id);
        if (is_array($allGamer) && is_array($allAudience)) {
            $currentGamer_return = array("result" => OPT_CONST::SUCCESS, "operation" => "AllGamerInfo", "data" => $allGamer, "audience" => $allAudience, "result_message" => "所有玩家状态");
            $this->pushMessageToCurrentClient($currentGamer_return);
        }

        //$this->notyfyPlayingStatus($room_id, $account_id);

        //推送当前观战玩家状态给其他玩家
        $currentGamer   = $ticketStatus ? $this->getGamerInfo($room_id, $account_id) : array();
        $curentAudience = $this->getAudienceInfo($room_id, $account_id);
        if (is_array($currentGamer) && is_array($curentAudience)) {
            $currentGamer_return = array("result" => OPT_CONST::SUCCESS, "operation" => "UpdateAudienceInfo", "audience" => $curentAudience, "result_message" => "某玩家状态");
            count($currentGamer) > 0 && $currentGamer_return['data'] = $currentGamer;
            $this->pushMessageToGroup($room_id, $currentGamer_return, $client_id);
        }

        //推送之前对局结果给进房的用户
        $game_result = $this->queryRoomGameEndData($room_id, $room_ary['game_type']);
        if (is_array($game_result)) {
            $push_return = array("result" => OPT_CONST::SUCCESS, "operation" => "AllGameEndData", "data" => $game_result, "result_message" => "房间对局结果");
            $this->pushMessageToCurrentClient($push_return);
        }

        //获取房间状态
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


    /*
        拉取房间信息
    */
    public function pullRoomInfo($arrData) {
        $result = array();

        $data       = $arrData['data'];
        $operation  = $arrData['operation'];
        $account_id = $arrData['account_id'];
        $client_id  = $arrData['client_id'];

        $session            = $arrData['session'];
        $Verification_Model = Verification_Model::getModelObject();
        if (FALSE == $Verification_Model->checkRequestSession($account_id, $session)) {
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
        $room_ary['account_status'] = $account_status;
        $room_ary['online_status']  = $this->queryOnlineStatus($room_id, $account_id);

        $room_ary['serial_num'] = $serial_num;

        $room_ary['ticket_checked'] = $this->queryTicketChecked($room_id, $account_id);

        $setting = $this->queryRoomSetting($room_id);

        $room_ary['pool_score'] = $this->queryPoolScore($room_id);
        $room_ary['chip_type']  = isset($setting[Redis_Const::Room_Field_ChipType]) ? explode(",", $setting[Redis_Const::Room_Field_ChipType]) : array(10, 20, 30, 50, 100);
        $room_ary['game_type']  = isset($setting[Redis_Const::Room_Field_GameType]) ? $setting[Redis_Const::Room_Field_GameType] : 61;

        //返回所有玩家状态
        $allGamer = $this->getGamerInfo($room_id);
        if (!is_array($allGamer)) {
            $this->logMessage('error', "function(pullRoomInfo):room($room_id) no player" . " in file" . __FILE__ . " on Line " . __LINE__);
            return array("result" => OPT_CONST::FAILED, "operation" => $operation, "data" => $result, "result_message" => "房间没有其他用户");
        }

        $pull_return = array("result" => OPT_CONST::SUCCESS, "operation" => $operation, "data" => $room_ary, "result_message" => "拉取房间信息", "all_gamer_info" => $allGamer);

        $this->pushMessageToCurrentClient($pull_return);

        //$this->notyfyPlayingStatus($room_id, $account_id);

        $this->logMessage('error', "function(pullRoomInfo):用户$account_id 拉取房间$room_id 信息" . " in file" . __FILE__ . " on Line " . __LINE__);
        $this->logMessage('error', "function(pullRoomInfo):pull_return:" . json_encode($pull_return) . " in file" . __FILE__ . " on Line " . __LINE__);

        return OPT_CONST::NO_RETURN;
    }


    /*
        获取房间所有用户
    */
    protected function getGamerInfo($room_id, $account_id = -1) {
        $result = array();

        $Redis_Model = Redis_Model::getModelObject();
        $MMYSQL      = $this->initMysql();

        $replyArr = array("[roomid]" => $room_id, "[accountid]" => $account_id);

        //房间玩家集合
        $RoomSequence_Key = strtr(Redis_Const::RoomSequence_Key, $replyArr);
        $banker_id = $this->queryBanker($room_id);

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
                    return FALSE;
                }
                $info['serial_num'] = $serial_num;
                $info['account_id'] = $gamer_id;
                $info['nickname']   = $account_query[0]['nickname'];
                $info['headimgurl'] = $account_query[0]['headimgurl'];

                //获取玩家当前积分
                $rScore_score = $this->queryAccountScore($room_id, $gamer_id);
                if (Redis_CONST::DATA_NONEXISTENT === $rScore_score) {
                    $this->logMessage('error', "function(getGamerInfo):account($gamer_id) score not exist" . " in file" . __FILE__ . " on Line " . __LINE__);
                    return FALSE;
                }
                $info['account_score'] = $rScore_score;

                //获取玩家当前状态
                $rStatus = $this->queryAccountStatus($room_id, $gamer_id);
                if (Redis_CONST::DATA_NONEXISTENT === $rStatus) {
                    $this->logMessage('error', "function(getGamerInfo):account($gamer_id) status not exist" . " in file" . __FILE__ . " on Line " . __LINE__);
                    return FALSE;
                }
                $info['account_status'] = $rStatus;
                $info['online_status']  = $this->queryOnlineStatus($room_id, $gamer_id);
                $info['ticket_checked'] = $this->queryTicketChecked($room_id, $gamer_id);

                //获取玩家当前是否庄家
                $is_banker = G_CONST::IS_FALSE;
                if ($banker_id == $gamer_id) {
                    $is_banker = G_CONST::IS_TRUE;
                }
                $info['is_banker'] = $is_banker;
                $leftTime = $this->getTimerLeft($room_id,$rStatus);
                $info['leftTime'] = $leftTime;
                //获取玩家的下注总量
                $info['chips'] = $this->queryChip($room_id, $gamer_id);
                if ($account_id == $gamer_id) {
                    return $info;
                }

                $result[] = $info;

            }
        }

        return $result;
    }

    //获取观战用户的信息
    protected function getAudienceIDSet($allAudience, $account_id) {
        foreach ($allAudience as $audienceInfo) {
            if ($account_id == $audienceInfo->account_id) {
                return TRUE;
            }
        }
        return FALSE;
    }


    protected function getAudienceInfo($room_id, $account_id = -1) {
        $result = array();

        $Redis_Model = Redis_Model::getModelObject();
        $MMYSQL      = $this->initMysql();

        $replyArr = array("[roomid]" => $room_id, "[accountid]" => $account_id);

        //房间玩家集合
        $RoomAudience_Key     = strtr(Redis_Const::RoomAudience_Key, $replyArr);
        $RoomAudienceInfo_Key = strtr(Redis_Const::RoomAudienceInfo_Key, $replyArr);

        //获取房间所有用户
        $sset_array['key']        = $RoomAudience_Key;
        $sset_array['WITHSCORES'] = "WITHSCORES";
        $audience_query           = $Redis_Model->getSortedSetLimitByAry($sset_array);

        $sset_array['key']        = $RoomAudienceInfo_Key;
        $sset_array['WITHSCORES'] = "WITHSCORES";
        $audienceInfo_query       = $Redis_Model->getSortedSetLimitByAry($sset_array);

        if (Redis_CONST::DATA_NONEXISTENT !== $audience_query && Redis_CONST::DATA_NONEXISTENT !== $audienceInfo_query) {
            if ($account_id == -1) {
                foreach ($audienceInfo_query as $audienceInfo => $serial_num) {
                    $result[] = json_decode($audienceInfo);
                }
            } else if (isset($audience_query[$account_id])) {
                $serial = $audience_query[$account_id];
                foreach ($audienceInfo_query as $audienceInfo => $serial_num) {
                    if ($serial == $serial_num) {
                        $result = json_decode($audienceInfo, TRUE);
                        break;
                    }
                }
            }
        }

        return $result;
    }

    /*
        进房之前的查询
    */
    public function prepareJoinRoom($arrData) {
        $result = array();

        $data       = $arrData['data'];
        $operation  = $arrData['operation'];
        $account_id = $arrData['account_id'];

        $session            = $arrData['session'];
        $Verification_Model = Verification_Model::getModelObject();
        if (FALSE == $Verification_Model->checkRequestSession($account_id, $session)) {
            return OPT_CONST::NO_RETURN;
        }

        if (!(isset($data['room_number']) && $data['room_number'] > 0)) {
            $this->logMessage('error', "function(PrepareJoinRoom):lack of room_number" . " in file" . __FILE__ . " on Line " . __LINE__);
            return $this->_missingPrameterArr($operation, "room_number");
        }

        $room_number = $data['room_number'];

        //判断房间是否存在
        $MMYSQL   = $this->initMysql();
        $room_sql = 'select room_id,account_id,is_close from ' . Room . ' where room_number=' . $room_number;
        $room_row = $MMYSQL->row($room_sql);
        if (!$room_row) {
            $this->logMessage('error', "function(joinRoom):room($room_number) not exist" . " in file" . __FILE__ . " on Line " . __LINE__);
            return array("result" => OPT_CONST::FAILED, "operation" => $operation, "data" => $result, "result_message" => "房间不存在");
        } else {
            $room_id = $room_row['room_id'];
            if ($room_row['is_close']) {
                $result = array('room_status' => 4);
                return array("result" => 0, "operation" => $operation, "data" => $result, "result_message" => "房间已关闭");
            }
        }

        //房间参数
        $user_count      = 0;
        $alert_text      = "";
        $room_status     = $this->queryRoomStatus($room_id);
        $setting         = $this->queryRoomSetting($room_id);
        $ticket_count    = isset($setting[Redis_Const::Room_Field_TicketCount]) ? $setting[Redis_Const::Room_Field_TicketCount] : 1;
        $chip_type       = isset($setting[Redis_Const::Room_Field_ChipType]) ? explode(",", $setting[Redis_Const::Room_Field_ChipType]) : Game::Chip_Array;
        $upper_limit     = isset($setting[Redis_Const::Room_Field_UpperLimit]) ? $setting[Redis_Const::Room_Field_UpperLimit] : Game::Default_UpperLimit_Score;
        $game_type       = isset($setting[Redis_Const::Room_Field_GameType]) ? $setting[Redis_Const::Room_Field_GameType] : Game::Game_DarkPo10_Type;
        $first_lossrate  = isset($setting[Redis_Const::Room_Field_Firstlossrate]) ? $setting[Redis_Const::Room_Field_Firstlossrate] : 3;
        $second_lossrate = isset($setting[Redis_Const::Room_Field_Secondlossrate]) ? $setting[Redis_Const::Room_Field_Secondlossrate] : 2;
        $three_lossrate  = isset($setting[Redis_Const::Room_Field_Threelossrate]) ? $setting[Redis_Const::Room_Field_Threelossrate] : 1;
        $banker_mode     = isset($setting[Redis_Const::Room_Field_BankerMode]) ? $setting[Redis_Const::Room_Field_BankerMode] : Game::BankerMode_FreeGrab;
        $room_array      = array(
            'user_count' => $user_count,
            'alert_text' => $alert_text,
            'room_status' => $room_status,
            'ticket_count' => $ticket_count,
            'chip_type' => $chip_type,
            'upper_limit' => $upper_limit,
            'first_lossrate' => $first_lossrate,
            'second_lossrate' => $second_lossrate,
            'three_lossrate' => $three_lossrate,
            'banker_mode' => $banker_mode,
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
        $is_member  = FALSE;
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
                        $is_member = TRUE;
                    }
                }
            }
        }
        if ($room_array['user_count'] == 0 || $is_member) {
            $room_array['alert_text'] = "是否重新加入房间？";
        } else {
            $maxCount = $this->getRoomMaxMember($game_type);

            if ($room_array['user_count'] >= $maxCount) {
                $this->writeLog("[$room_id] ($account_id)  PrepareJoinRoom 人数已满");
                $room_array['alert_type'] = 2;    //2人数已满
                return array("result" => "1", "operation" => $operation, "data" => $room_array, "result_message" => "房间人数已满");
            }
            $user_str                 = implode("、", $room_users);
            $room_array['alert_text'] = "房间中有" . $user_str . "，是否加入？";
        }
        return array("result" => "0", "operation" => $operation, "data" => $room_array, "result_message" => "进房询问");
    }

    protected function getGameAnnouncement($account_id, $game_type) {

        $timestamp = time();

        $MMYSQL = $this->initMysql();

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


    /*
        准备操作
    */
    public function readyStart($arrData) {
        $timestamp = time();
        $result    = array();
        $return    = array();

        $data       = $arrData['data'];
        $operation  = $arrData['operation'];
        $account_id = $arrData['account_id'];
        $client_id  = $arrData['client_id'];

        $session            = $arrData['session'];
        $Verification_Model = Verification_Model::getModelObject();
        if (FALSE == $Verification_Model->checkRequestSession($account_id, $session)) {
            return OPT_CONST::NO_RETURN;
        }

        $this->logMessage('error', "readyStart :accountid " . $account_id);
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

        //更新用户状态
        $rStatus = Game::AccountStatus_Ready;
        $this->updateAccountStatus($room_id, $account_id, $rStatus);

        $this->startGame($room_id);

        return OPT_CONST::NO_RETURN;
    }

    /*
        取消准备操作
    */
    public function readyCancel($arrData) {
        $timestamp = time();
        $result    = array();
        $return    = array();

        $data       = $arrData['data'];
        $operation  = $arrData['operation'];
        $account_id = $arrData['account_id'];
        $client_id  = $arrData['client_id'];
        $this->writeLog("this is here XXXXXXXX");
        $return; //todo

        $session            = $arrData['session'];
        $Verification_Model = Verification_Model::getModelObject();
        if (FALSE == $Verification_Model->checkRequestSession($account_id, $session)) {
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
    public function historyScoreboard($arrData) {
        $result = array();

        $data       = $arrData['data'];
        $operation  = $arrData['operation'];
        $account_id = $arrData['account_id'];
        $client_id  = $arrData['client_id'];
        $session    = $arrData['session'];

        $Verification_Model = Verification_Model::getModelObject();
        if (FALSE == $Verification_Model->checkRequestSession($account_id, $session)) {
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
        $game_type   = $this->queryGameType($room_id);

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
    public function lastScoreboard($arrData) {
        $result     = array();
        $data       = $arrData['data'];
        $operation  = $arrData['operation'];
        $account_id = $arrData['account_id'];
        $client_id  = $arrData['client_id'];

        $session            = $arrData['session'];
        $Verification_Model = Verification_Model::getModelObject();
        if (FALSE == $Verification_Model->checkRequestSession($account_id, $session)) {
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

        $game_type   = $this->queryGameType($room_id);
        $scoreboards = new stdClass();
        $sql         = 'select board,create_time,game_num,rule_text from room_scoreboard where room_id=' . $room_id . ' and game_type=' . $game_type . ' order by create_time desc limit 1';
        $query       = $MMYSQL->query($sql);
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
