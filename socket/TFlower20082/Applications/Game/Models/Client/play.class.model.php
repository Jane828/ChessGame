<?php

use \GatewayWorker\Lib\Gateway;

require_once dirname(__DIR__) . '/public.class.model.php';

class Play_Model extends Public_Model {
    /*
        点击看牌
    */
    public function clickToLook($arrData) {
        $timestamp = time();
        $result    = array();

        $data       = $arrData['data'];
        $operation  = $arrData['operation'];
        $account_id = $arrData['account_id'];
        $client_id  = $arrData['client_id'];

        $session            = $arrData['session'];
        $Verification_Model = Verification_Model::getModelObject();
        if (FALSE == $Verification_Model->checkRequestSession($account_id, $session)) {
            return OPT_CONST::NO_RETURN;
        }

        if (!isset($data['room_id']) || $data['room_id'] <= 0) {
            $this->logMessage('error', "function(chosePlayType):lack of room_id" . " in file" . __FILE__ . " on Line " . __LINE__);
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
        $replyArr    = array("[roomid]" => $room_id);

        // TODO 是否满足全局看牌条件
        $seen_condition = $this->seenChipConditon($room_id);
        if ($seen_condition == 0) {
            return array("result" => OPT_CONST::FAILED, "operation" => $operation, "data" => $result, "result_message" => "不满足看牌条件");
        }

        //获取玩家当前状态 只有处于合理的状态才能看到牌
        $rStatus = $this->queryAccountStatus($room_id, $account_id);
        if (Redis_CONST::DATA_NONEXISTENT === $rStatus || !in_array($rStatus, [Game::AccountStatus_Visible, Game::AccountStatus_Invisible])) {
            $this->writeLog("function(clickToLook):account($account_id) status($rStatus) invalid" . " in file" . __FILE__ . " on Line " . __LINE__);
            return array("result" => OPT_CONST::FAILED, "operation" => $operation, "data" => $result, "result_message" => "看牌状态异常");
        }


        //设置已经看牌的标识
        $SeenCard_Key     = strtr(Redis_Const::SeenCard_Key, $replyArr);
        $mkv[$account_id] = 1; //已经看牌
        $Redis_Model->hmsetField($SeenCard_Key, $mkv);
	$Redis_Model->expireKey($SeenCard_Key, G_CONST::REDIS_EXPIRE_SECOND);

        //设置用户状态
        $status = Game::AccountStatus_Visible;
        $this->updateAccountStatus($room_id, $account_id, $status);

        $card_info = $this->queryCardInfo($room_id, $account_id);
        $value     = $this->caculateCardValue($room_id, $account_id);
        $card_type = substr($value, 0, 1);
        $this->writeLog("[$room_id] ($account_id) 牌型:" . $card_type);
        $msg_arr = array("result" => 0, "operation" => "CardInfo", "data" => array("cards" => $card_info, "card_type" => $card_type), "result_message" => "牌");
        //$msg_arr = array("result"=>0,"operation"=>"CardInfo","data"=>$card_info,"result_message"=>"牌");
        $this->pushMessageToCurrentClient($msg_arr);

        return OPT_CONST::NO_RETURN;
    }

    /*
        选择下注筹码
    */
    public function chooseChip($arrData) {
        $timestamp = time();
        $result    = array();

        $data       = $arrData['data'];
        $operation  = $arrData['operation'];
        $account_id = $arrData['account_id'];
        $client_id  = $arrData['client_id'];

        $session            = $arrData['session'];
        $Verification_Model = Verification_Model::getModelObject();
        if (FALSE == $Verification_Model->checkRequestSession($account_id, $session)) {
            return OPT_CONST::NO_RETURN;
        }

        if (!isset($data['score']) || !in_array($data['score'], [2, 4, 8, 16, 5, 10, 20, 40, 80, 50, 100, 200])) {
            $this->logMessage('error', "function(chooseChip):lack of score" . " in file" . __FILE__ . " on Line " . __LINE__);
            return $this->_missingPrameterArr($operation, "score");
        }
        if (!isset($data['room_id']) || $data['room_id'] <= 0) {
            $this->logMessage('error', "function(chooseChip):lack of room_id" . " in file" . __FILE__ . " on Line " . __LINE__);
            return $this->_missingPrameterArr($operation, "room_id");
        }

        $score   = $data['score'];
        $room_id = $data['room_id'];

        $is_legal = $this->checkRequestClientLegal($client_id, $room_id, $account_id);
        if (!$is_legal) {
            $this->logMessage('error', "function(checkRequestClientLegal):illegal opt: account_id($account_id) room_id($room_id)" . " in file" . __FILE__ . " on Line " . __LINE__);
            //推送强制下线
            $illegal_message = $this->_JSON(array("result" => "-203", "result_message" => "illegal opt"));
            Gateway::sendToCurrentClient($illegal_message);
            return OPT_CONST::NO_RETURN;
        }

        $active_user = $this->queryActiveUser($room_id);
        if ($active_user != $account_id) {
            return array("result" => OPT_CONST::FAILED, "operation" => $operation, "data" => array('active_user' => $active_user), "result_message" => "未轮到你操作");
        }

        $Redis_Model = Redis_Model::getModelObject();
        $benchmark   = $this->queryBenchmark($room_id);
        if ($benchmark < 0) {
            return array("result" => OPT_CONST::FAILED, "operation" => $operation, "data" => array('benchmark' => $benchmark), "result_message" => "本局还未设置叫分基准");
        }

        $this->deleteRoomTimer($room_id);

        $setting   = $this->queryRoomSetting($room_id);
        $chip_type = isset($setting[Redis_Const::Room_Field_ChipType]) ? explode(",", $setting[Redis_Const::Room_Field_ChipType]) : array(2, 4, 5, 8);
        asort($chip_type);
        $chip_upper_limit = 2 * $chip_type[3];
        $status           = $this->queryAccountStatus($room_id, $account_id);
        $is_invisible     = $status == Game::AccountStatus_Invisible ? 1 : 0;
        if ($is_invisible) {
            $benchmark        = $benchmark / 2;
            $chip_upper_limit = $chip_upper_limit / 2;
        }

        if ($score < $benchmark || $score > $chip_upper_limit) {
            return array("result" => OPT_CONST::FAILED, "operation" => $operation, "data" => $result, "result_message" => "筹码不在合理区间");
        }

        $this->balanceScore($room_id, $account_id, $score);


        //更新叫分基准
        if ($score > $benchmark) {
            if ($is_invisible) {
                $benchmark = $score * 2;
            } else {
                $benchmark = $score;
            }
            $this->updateBenchmark($room_id, $benchmark);
        }

        $msg_arr = array(
            'result'         => 0,
            'operation'      => 'UpdateAccountScore',
            'result_message' => "下注",
            'data'           => array(
                'account_id' => $account_id,
                'score'      => $score
            )
        );
        $this->pushMessageToGroup($room_id, $msg_arr);
        $this->writeLog("[$room_id] ($account_id) 下注" . $score . "分");

        //上限封顶
        $setting     = $this->queryRoomSetting($room_id);
        $upper_limit = isset($setting[Redis_Const::Room_Field_UpperLimit]) ? $setting[Redis_Const::Room_Field_UpperLimit] : 1000;
        $pool_score  = $this->queryPoolScore($room_id);
        if ($upper_limit > 0 && $pool_score >= $upper_limit) {
            $this->showWin($room_id, $account_id, -1, 3);   //win_type==3 封顶开牌
        } else {
            $next_account_id = $this->takeTurns($room_id);

            if ($account_id == $next_account_id) {
                //显示本局输赢
                $this->showWin($room_id, $account_id, $next_account_id, 2);  //win_type==2 弃牌而剩
            }

            $this->notyUserToBet($room_id, $next_account_id);
        }

        return OPT_CONST::NO_RETURN;
    }

    /*
        弃牌
    */
    public function discard($arrData) {
        $timestamp = time();
        $result    = array();

        $data       = $arrData['data'];
        $operation  = $arrData['operation'];
        $account_id = $arrData['account_id'];
        $client_id  = $arrData['client_id'];

        $session            = $arrData['session'];
        $Verification_Model = Verification_Model::getModelObject();
        if (FALSE == $Verification_Model->checkRequestSession($account_id, $session)) {
            return OPT_CONST::NO_RETURN;
        }

        if (!isset($data['room_id']) || $data['room_id'] <= 0) {
            $this->logMessage('error', "function(discard):lack of room_id" . " in file" . __FILE__ . " on Line " . __LINE__);
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

        $active_user = $this->queryActiveUser($room_id);
        if ($active_user != $account_id) {
            return array("result" => OPT_CONST::FAILED, "operation" => $operation, "data" => array('active_user' => $active_user), "result_message" => "未轮到你操作");
        }

        $this->deleteRoomTimer($room_id);

        $Redis_Model = Redis_Model::getModelObject();

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
        return array("result" => 0, "operation" => $operation, "data" => $result, "result_message" => "你弃牌了");
        return OPT_CONST::NO_RETURN;
    }


    /*
        开牌
    */
    public function openCard($arrData) {
        $data       = $arrData['data'];
        $operation  = $arrData['operation'];
        $account_id = $arrData['account_id'];
        $client_id  = $arrData['client_id'];

        $session            = $arrData['session'];
        $Verification_Model = Verification_Model::getModelObject();
        if (FALSE == $Verification_Model->checkRequestSession($account_id, $session)) {
            return OPT_CONST::NO_RETURN;
        }

        if (!isset($data['room_id']) || $data['room_id'] <= 0) {
            $this->logMessage('error', "function(discard):lack of room_id" . " in file" . __FILE__ . " on Line " . __LINE__);
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

        $active_user = $this->queryActiveUser($room_id);
        if ($active_user != $account_id) {
            return array("result" => OPT_CONST::FAILED, "operation" => $operation, "data" => array('active_user' => $active_user), "result_message" => "未轮到你操作");
        }

        $this->deleteRoomTimer($room_id);


        $benchmark = $this->queryBenchmark($room_id);
        if ($benchmark < 0) {
            return array("result" => OPT_CONST::FAILED, "operation" => $operation, "data" => array('benchmark' => $benchmark), "result_message" => "本局还未设置叫分基准");
        } else {
            $this->writeLog("[$room_id] ($account_id) 开牌 分数基准:" . $benchmark);
        }
        $status = $this->queryAccountStatus($room_id, $account_id);
        if ($status == Game::AccountStatus_Invisible) {   //闷牌
            $benchmark = $benchmark / 2;
        }
        $this->balanceScore($room_id, $account_id, $benchmark);  //开牌 基准

        $msg_arr = array(
            'result'         => 0,
            'operation'      => $operation,
            'result_message' => "开牌",
            'data'           => array(
                'account_id' => $account_id,
                'score'      => $benchmark
            )
        );
        $this->pushMessageToGroup($room_id, $msg_arr);

        //显示本局输赢
        $Redis_Model    = Redis_Model::getModelObject();
        $replyArr       = array("[roomid]" => $room_id);
        $PlayMember_Key = strtr(Redis_Const::PlayMember_Key, $replyArr);
        $list           = $Redis_Model->lrangeList($PlayMember_Key, -2, -2);  //队列右边第二位是下一个操作用户
        if ($list) {
            $next_account_id = $list[0];
        } else {
            $next_account_id = -1;
        }

        $this->showWin($room_id, $account_id, $next_account_id, 1);   //win_type==1 开牌而剩

        return OPT_CONST::NO_RETURN;
    }

    /*
        比牌
    */
    public function pkCard($arrData) {
        $timestamp = time();
        $result    = array();

        $data       = $arrData['data'];
        $operation  = $arrData['operation'];
        $account_id = $arrData['account_id'];
        $client_id  = $arrData['client_id'];

        $session            = $arrData['session'];
        $Verification_Model = Verification_Model::getModelObject();
        if (FALSE == $Verification_Model->checkRequestSession($account_id, $session)) {
            return OPT_CONST::NO_RETURN;
        }


        if (!isset($data['room_id']) || $data['room_id'] <= 0) {
            $this->logMessage('error', "function(discard):lack of room_id" . " in file" . __FILE__ . " on Line " . __LINE__);
            return $this->_missingPrameterArr($operation, "room_id");
        }
        $room_id          = $data['room_id'];
        $other_account_id = $data['other_account_id'];

        $is_legal = $this->checkRequestClientLegal($client_id, $room_id, $account_id);
        if (!$is_legal) {
            $this->logMessage('error', "function(checkRequestClientLegal):illegal opt: account_id($account_id) room_id($room_id)" . " in file" . __FILE__ . " on Line " . __LINE__);
            //推送强制下线
            $illegal_message = $this->_JSON(array("result" => "-203", "result_message" => "illegal opt"));
            Gateway::sendToCurrentClient($illegal_message);
            return OPT_CONST::NO_RETURN;
        }

        // TODO 是否满足全局比牌条件
        $pk_condition = $this->pkChipConditon($room_id, $account_id);
        if ($pk_condition == 0) {
            return array("result" => OPT_CONST::FAILED, "operation" => $operation, "data" => $result, "result_message" => "不满足比牌条件");
        }

        $active_user = $this->queryActiveUser($room_id);
        if ($active_user != $account_id) {
            return array("result" => OPT_CONST::FAILED, "operation" => $operation, "data" => array('active_user' => $active_user), "result_message" => "未轮到你操作");
        }

        $this->deleteRoomTimer($room_id);

        $Redis_Model = Redis_Model::getModelObject();
        $replyArr    = array("[roomid]" => $room_id);

        $benchmark = $this->queryBenchmark($room_id);
        if ($benchmark < 0) {
            return array("result" => OPT_CONST::FAILED, "operation" => $operation, "data" => array('benchmark' => $benchmark), "result_message" => "本局还未设置叫分基准");
        } else {
            $this->writeLog("[$room_id] ($account_id) 比牌 当前基准:" . $benchmark);
        }
        $status = $this->queryAccountStatus($room_id, $account_id);
        if ($status == Game::AccountStatus_Invisible) {   //闷牌
            $benchmark = $benchmark / 2;
        }
        $this->balanceScore($room_id, $account_id, $benchmark);  //开牌 基准

        $value1    = $this->caculateCardValue($room_id, $account_id);
        $value2    = $this->caculateCardValue($room_id, $other_account_id);
        $winner_id = $value1 > $value2 ? $account_id : $other_account_id;

        if ($value1 > $value2) {
            $winner_id = $account_id;
            $loser_id  = $other_account_id;
        } else {
            $winner_id = $other_account_id;
            $loser_id  = $account_id;
        }

        $msg_arr = array(
            'result'         => 0,
            'operation'      => $operation,
            'result_message' => "比牌",
            'data'           => array(
                'account_id'       => $account_id,
                'other_account_id' => $other_account_id,
                'score'            => $benchmark,
                'winner_id'        => $winner_id,
                'loser_id'         => $loser_id
            )
        );
        $this->pushMessageToGroup($room_id, $msg_arr);

        $this->writeLog("[$room_id] ($account_id) 比牌 vs" . $other_account_id . "   胜:" . $winner_id);

        //设置用户状态
        $status = Game::AccountStatus_Lost;
        $this->updateAccountStatus($room_id, $loser_id, $status);

        $next_account_id = $this->removeFromPlayMember($room_id, $loser_id);

        if ($account_id == $next_account_id) {
            $next_account_id = $this->takeTurns($room_id);
        }
        $this->notyUserToBet($room_id, $next_account_id);

        return OPT_CONST::NO_RETURN;
    }

    /*
        发送声音
    */
    public function broadcastVoice($arrData) {
        $data       = $arrData['data'];
        $operation  = $arrData['operation'];
        $account_id = $arrData['account_id'];
        $client_id  = $arrData['client_id'];

        $session            = $arrData['session'];
        $Verification_Model = Verification_Model::getModelObject();
        if (FALSE == $Verification_Model->checkRequestSession($account_id, $session)) {
            return OPT_CONST::NO_RETURN;
        }

        if (!isset($data['room_id']) || $data['room_id'] <= 0) {
            $this->logMessage('error', "function(broadcastVoice):lack of room_id" . " in file" . __FILE__ . " on Line " . __LINE__);
            return $this->_missingPrameterArr($operation, "room_id");
        }

        $room_id   = $data['room_id'];
        $voice_num = $data['voice_num'];

        $is_legal = $this->checkRequestClientLegal($client_id, $room_id, $account_id);
        if (!$is_legal) {
            $this->logMessage('error', "function(checkRequestClientLegal):illegal opt: account_id($account_id) room_id($room_id)" . " in file" . __FILE__ . " on Line " . __LINE__);
            //推送强制下线
            $illegal_message = $this->_JSON(array("result" => "-203", "result_message" => "illegal opt"));
            Gateway::sendToCurrentClient($illegal_message);
            return OPT_CONST::NO_RETURN;
        }

        $msg_arr = array("result" => "0", "operation" => $operation, "data" => array(
            'account_id' => $account_id,
            'voice_num'  => $voice_num
        ), "result_message"       => "发送声音");
        $this->pushMessageToGroup($room_id, $msg_arr, $client_id);
        return OPT_CONST::NO_RETURN;
    }


    /*
        发送声音
    */
    public function speakVoice($arrData) {
        $data       = $arrData['data'];
        $operation  = $arrData['operation'];
        $account_id = $arrData['account_id'];
        $client_id  = $arrData['client_id'];

        $session            = $arrData['session'];
        $Verification_Model = Verification_Model::getModelObject();
        if (FALSE == $Verification_Model->checkRequestSession($account_id, $session)) {
            return OPT_CONST::NO_RETURN;
        }

        if (!isset($data['room_id']) || $data['room_id'] <= 0) {
            $this->writeLog("function(speakVoice):lack of room_id" . " in file" . __FILE__ . " on Line " . __LINE__);
            return $this->_missingPrameterArr($operation, "room_id");
        }
        if (!isset($data['local_id'])) {
            $this->writeLog("function(speakVoice):lack of local_id" . " in file" . __FILE__ . " on Line " . __LINE__);
            return $this->_missingPrameterArr($operation, "local_id");
        }
        if (!isset($data['time'])) {
            $this->writeLog("function(speakVoice):lack of time" . " in file" . __FILE__ . " on Line " . __LINE__);
            return $this->_missingPrameterArr($operation, "time");
        }
        if (!isset($data['server_id'])) {
            $this->writeLog("function(speakVoice):lack of server_id" . " in file" . __FILE__ . " on Line " . __LINE__);
            return $this->_missingPrameterArr($operation, "server_id");
        }

        $room_id   = $data['room_id'];
        $local_id  = $data['local_id'];
        $time      = $data['time'];
        $server_id = $data['server_id'];

        $is_legal = $this->checkRequestClientLegal($client_id, $room_id, $account_id);
        if (!$is_legal) {
            $this->logMessage('error', "function(checkRequestClientLegal):illegal opt: account_id($account_id) room_id($room_id)" . " in file" . __FILE__ . " on Line " . __LINE__);
            //推送强制下线
            $illegal_message = $this->_JSON(array("result" => "-203", "result_message" => "illegal opt"));
            Gateway::sendToCurrentClient($illegal_message);
            return OPT_CONST::NO_RETURN;
        }

        $msg_arr = array("result" => "0", "operation" => $operation, "data" => array(
            'account_id' => $account_id,
            'local_id'   => $local_id,
            'time'       => $time,
            'server_id'  => $server_id
        ), "result_message"       => "说话声音");
        $this->pushMessageToGroup($room_id, $msg_arr, $client_id);
        return OPT_CONST::NO_RETURN;
    }

    /*
             观察员用于调试收取房间消息
    */
    public function observer($arrData) {
        $data         = $arrData['data'];
        $operation    = $arrData['operation'];
        $client_count = Gateway::getAllClientCount();
        return array("operation" => $operation, "data" => array('client_count' => $client_count), "result_message" => "当前在线连接总数");
    }

}
