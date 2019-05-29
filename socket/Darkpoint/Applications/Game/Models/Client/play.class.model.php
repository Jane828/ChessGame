<?php

use \GatewayWorker\Lib\Gateway;

require_once dirname(__DIR__) . '/public.class.model.php';

class Play_Model extends Public_Model {

    /*
		抢庄
	*/
    public function grabBanker($arrData) {
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
            $this->logMessage('error', "function(grabBanker):lack of room_id" . " in file" . __FILE__ . " on Line " . __LINE__);
            return $this->_missingPrameterArr($operation, "room_id");
        }
        if (!isset($data['is_grab']) || $data['is_grab'] === "") {
            $this->logMessage('error', "function(grabBanker):lack of is_grab" . " in file" . __FILE__ . " on Line " . __LINE__);
            return $this->_missingPrameterArr($operation, "is_grab");
        }
        $room_id = $data['room_id'];
        $is_grab = $data['is_grab'];

        $this->writeLog("roomid[$room_id]-> $account_id grab" . "$is_grab");

        //获取当前游戏回合
        $circle = $this->queryCircle($room_id);
        if ($circle != Game::Circle_Grab) {
            $this->logMessage('error', "function(grabBanker):account($account_id) circle($circle) error " . " in file" . __FILE__ . " on Line " . __LINE__);
            return array("result" => OPT_CONST::FAILED, "operation" => $operation, "data" => $result, "result_message" => "用户状态异常");
        }


        $room_data   = $this->queryRoomData($room_id);
        $game_num    = $room_data['gnum'];
        $room_status = $room_data['stat'];
        $banker_mode = $room_data['bankermode'];

        //获取房间庄家模式
        if (($banker_mode == Game::BankerMode_FixedBanker && $game_num > 1) || $banker_mode == Game::BankerMode_RoomownerGrab) {
            $result['banker_mode'] = $banker_mode;
            $result['game_num']    = $game_num;
            $this->logMessage('error', "function(grabBanker): banker_mode($banker_mode) can not grab " . " in file" . __FILE__ . " on Line " . __LINE__);
            return array("result" => OPT_CONST::FAILED, "operation" => $operation, "data" => $result, "result_message" => "该模式不能抢庄");
        }

        $account_status = $this->queryAccountStatus($room_id, $account_id);
        if (Redis_CONST::DATA_NONEXISTENT === $account_status || $account_status != Game::AccountStatus_Choose) {
            $this->logMessage('error', "xxxxxxfunction(grabBanker):roomid($room_id) account($account_id) status($account_status) error " . " in file" . __FILE__ . " on Line " . __LINE__);
            return array("result" => OPT_CONST::FAILED, "operation" => $operation, "data" => $result, "result_message" => "用户状态异常");
        }

        if ($is_grab == G_CONST::IS_FALSE) {
            //不抢庄
            $status = Game::AccountStatus_Notgrab;
        } else if ($is_grab == G_CONST::IS_TRUE) {
            //抢庄
            $status = Game::AccountStatus_Grab;
        } else {
            $this->logMessage('error', "function(grabBanker):is grab error " . " in file" . __FILE__ . " on Line " . __LINE__);
            return array("result" => OPT_CONST::FAILED, "operation" => $operation, "data" => $result, "result_message" => "参数异常");
        }
        //设置用户状态
        $this->updateAccountStatus($room_id, $account_id, $status);

        //是否开始庄家放宝
        $is_put = G_CONST::IS_TRUE;
        //获取所有游戏用户
        $player_array = $this->queryPlayMember($room_id);
        //获取游戏用户状态
        $account_array = $this->queryAccountStatusArray($room_id);
        foreach ($player_array as $player_id) {
            if (!isset($account_array[$player_id])) {
                $is_put = G_CONST::IS_FALSE;
                break;
            }
            $player_status = $account_array[$player_id];
            //获取游戏用户状态
            if (!in_array($player_status, [Game::AccountStatus_Notgrab, Game::AccountStatus_Grab])) {
                $is_put = G_CONST::IS_FALSE;
                break;
            }
        }

        if ($is_put == G_CONST::IS_TRUE) {
            $this->deleteRoomTimer($room_id);
            //选择庄家，开始庄家放宝回合
            $this->startPutRound($room_id);

        }

        return OPT_CONST::NO_RETURN;
    }

    /*
        庄家选择放暗宝
    */
    public function putPrize($arrData) {

        $data               = $arrData['data'];
        $operation          = $arrData['operation'];
        $account_id         = $arrData['account_id'];
        $session            = $arrData['session'];
        $Verification_Model = Verification_Model::getModelObject();
        if (FALSE == $Verification_Model->checkRequestSession($account_id, $session)) {
            return OPT_CONST::NO_RETURN;
        }

        if (!isset($data['area']) || !in_array($data['area'], Game::Areas)) {
            $this->logMessage('error', "function(putPrize):lack of area" . " in file" . __FILE__ . " on Line " . __LINE__);
            return $this->_missingPrameterArr($operation, "area");
        }
        if (!isset($data['room_id']) || $data['room_id'] <= 0) {
            $this->logMessage('error', "function(putPrize):lack of room_id" . " in file" . __FILE__ . " on Line " . __LINE__);
            return $this->_missingPrameterArr($operation, "room_id");
        }

        $area    = $data['area'];
        $room_id = $data['room_id'];

        //查看是否庄家操作
        $banker_id = $this->queryBanker($room_id);
        if ($banker_id != $account_id) {
            $this->writeLog("function(putPrize): playid($account_id) can't putsize;banker($banker_id)");
            return array("result" => OPT_CONST::FAILED, "result_message" => "闲家不能放宝");
        }
        //放宝成功后，通知所有游戏者开始下注
        $this->deleteRoomTimer($room_id);
        $this->putPassiveOpt($room_id, $area);
    }


    public function StopBetProcess($room_id) {

        //查看是否所有都停止下注，如果是则删除开奖定时器，直接进入开奖流程
        //获取游戏用户状态
        $account_array = $this->queryAccountStatusArray($room_id);
        $player_array  = $this->queryPlayMember($room_id);
        $banker_id     = $this->queryBanker($room_id);

        $is_show = G_CONST::IS_TRUE;
        foreach ($player_array as $player_id) {
            if (!isset($account_array[$player_id])) {
                continue;
            }
            if ($banker_id == $player_id) {
                continue;
            }

            $player_status = $account_array[$player_id];
            if ($player_status != Game::AccountStatus_StopBet) {
                $is_show = G_CONST::IS_FALSE;
                break;
            }
        }

        if ($is_show) {
            $this->deleteRoomTimer($room_id);
            $this->betPassiveOpt($room_id);
        }
        return OPT_CONST::NO_RETURN;

    }

    /*
     *  处理长方向坐标的调整 ，同 串
     *      |-------—————|
     *      |  x         |
     *      |____________|
     *
     */
    public function adjustRectPoints($data,$Point){
        $xnert = array_unique($data['xArry']);
        sort($xnert);
        $ynert = array_unique($data['yArry']);
        sort($ynert);
        $this->writeLog("adjustRectPoints  x ".$data['x']."yPoint ".$data['y']);
        $result['x'] = $data['x'];
        $result['y'] = $data['y'];
        var_dump($xnert);
        var_dump($ynert);
        if(count($xnert) == 2 && count($ynert) == 2){
            if($data['x'] -$Point < $xnert[0])
            {
                $result['x'] = $data['x'] + $Point;
                $this->writeLog("adjustRectPoints points  1");
            }

            if($data['x'] + $Point > $xnert[1])
            {
                $result['x'] = $data['x'] - $Point;
                $this->writeLog("adjustRectPoints points  2");
            }

            if($data['y'] -$Point < $ynert[0])
            {
                $result['y'] = $data['y'] + $Point;
                $this->writeLog("adjustRectPoints points  3");
            }

            if($data['y'] + $Point > $ynert[1])
            {
                $result['y'] = $data['y'] - $Point;
                $this->writeLog("adjustRectPoints points  4");
            }
        } else {
            $this->writeLog("adjustRectPoints points error 1");
        }
        var_dump($result);
        return $result;
    }

    /*
    *  处理角坐标的调整
    */
    public function adjustAnglePoints($data,$Point){
        $xnert = array_unique($data['xArry']);
        sort($xnert);
        $ynert = array_unique($data['yArry']);
        sort($ynert);
        $result['x'] = $data['x'];
        $result['y'] = $data['y'];

        if(count($xnert) == 3 && count($ynert) == 3){

            if($data['x'] -$Point < $xnert[0]) //x左边界
            {
                $result['x'] = $data['x'] + $Point;
            }
            if($data['x'] + $Point > $xnert[2]) //x右边界
            {
                $result['x'] = $data['x'] - $Point;
            }

            if($data['y'] -$Point < $ynert[0]) //y上边界
            {
                $result['y'] = $data['y'] + $Point;
            }

            if($data['y'] + $Point > $ynert[2]) //y下边界
            {
                $result['y'] = $data['y'] - $Point;
            }

            if($data['area']== Game::Rule_Dragon) {

                if ($data['subarea'] == Game::Rule_LeftAngle){
                    if(($data['x'] + $Point > $xnert[1]) && $data['y'] > ($ynert[1] - $Point) )
                    {
                        $result['x'] = $data['x'] - $Point;
                    }
                    if(($data['y'] + $Point > $ynert[1]) &&  $data['x'] > ($xnert[1] -$Point))
                    {
                        $result['y'] = $data['y'] - $Point;
                    }
                }else {
                    if(($data['x'] + $Point > $xnert[1]) && $data['y'] < ($ynert[1] + $Point) )
                    {
                        $result['x'] = $data['x'] - $Point;
                    }
                    if(($data['y'] - $Point < $ynert[1]) &&  $data['x'] > ($xnert[1] -$Point))
                    {
                        $result['y'] = $data['y'] + $Point;
                    }
                }
            }else { //tiger

                if ($data['subarea'] == Game::Rule_LeftAngle){

                    if(($data['x'] - $Point < $xnert[1]) && $data['y'] < ($ynert[1] + $Point) )
                    {
                        $result['x'] = $data['x'] + $Point;
                    }
                    if(($data['y'] - $Point < $ynert[1]) &&  $data['x'] < ($xnert[1] + $Point))
                    {
                        $result['y'] = $data['y'] + $Point;
                    }

                }else {

                    if(($data['x'] - $Point < $xnert[1]) && $data['y'] > ($ynert[1] - $Point) )
                    {
                        $result['x'] = $data['x'] + $Point;
                    }
                    if(($data['y'] + $Point > $ynert[1]) &&  $data['x'] < ($xnert[1] + $Point))
                    {
                        $result['y'] = $data['y'] - $Point;
                    }
                }
            }
        }
        else {
            $this->writeLog("adjustPoints points error 1");
        }
        return $result;
    }

    /*
    *  处理粘坐标的调整
    */
    public function adjustStickPoints($data,$Point){
        $xnert = array_unique($data['xArry']);
        sort($xnert);
        $ynert = array_unique($data['yArry']);
        sort($ynert);
        $result['x'] = $data['x'];
        $result['y'] = $data['y'];
        $this->writeLog("adjustStickPoints  x ".$data['x']."yPoint ".$data['y']);
        //Rule_Enter and Rule_Exit
        if(count($xnert) == 3 && count($ynert) == 2){
            if(($data['y'] - $Point) < $ynert[0]){
                $result['y'] =  $data['y'] + $Point;
                $this->writeLog("adjustStickPoints  1 ");
            }

            if(($data['y'] + $Point) > $ynert[1]){
                $result['y'] =  $data['y'] - $Point;
                $this->writeLog("adjustStickPoints  2 ");
            }
            //入 左
            if($data['area'] == Game::Rule_Enter &&$data['subarea'] == Game::Rule_LeftStick ||
                $data['area'] == Game::Rule_Exit && $data['subarea'] == Game::Rule_RightStick){
                if($data['x']  < $xnert[1] ){
                    $result['x'] = $data['x'] + $Point * 2;
                    $this->writeLog("adjustStickPoints  3 ");
                }
                //入 左
                if($data['x'] + $Point  > $xnert[2] ){
                    $result['x'] = $data['x'] - $Point;
                    $this->writeLog("adjustStickPoints  4 ");
                }
            }else{
                if($data['x'] - $Point < $xnert[0] ){
                    $result['x'] = $data['x'] + $Point;
                    $this->writeLog("adjustStickPoints  5 ");
                }
                if($data['x'] + $Point > $xnert[1] ){
                    $result['x'] = $data['x'] - $Point * 2;
                    $this->writeLog("adjustStickPoints  6 ");
                }
            }

        }else {//Rule_Dragon and Rule_Tiger
            if($data['x'] - $Point< $xnert[0]){
                $result['x'] = $data['x'] + $Point;
                $this->writeLog("adjustStickPoints  7 ");
            }
            if($data['x'] + $Point > $xnert[1]){
                $result['x'] = $data['x'] - $Point;
                $this->writeLog("adjustStickPoints  8 ");
            }

            if($data['area'] == Game::Rule_Dragon && $data['subarea'] == Game::Rule_LeftStick ||
                $data['area'] == Game::Rule_Tiger && $data['subarea'] == Game::Rule_RightStick){
                if($data['y'] - $Point < $ynert[1] ){
                    $result['y'] = $data['y'] + $Point * 2;
                    $this->writeLog("adjustStickPoints  9 ");
                }
                if($data['y'] + $Point > $ynert[2] ){
                    $result['y'] = $data['y'] - $Point;
                    $this->writeLog("adjustStickPoints  10 ");
                }
            }else {
                if($data['y'] - $Point < $ynert[0] ){
                    $result['y'] = $data['y'] + $Point;
                    $this->writeLog("adjustStickPoints  11 ");
                }
                if($data['y'] + $Point > $ynert[1] ){
                    $result['y'] = $data['y'] - $Point * 2;
                    $this->writeLog("adjustStickPoints  12 ");
                }
            }
        }
        var_dump($result);
        return $result;
    }

    /*
    *  处理出入龙虎坐标的调整
    */
    public function adjustCentralPoints($data,$Point){
        $xnert = array_unique($data['xArry']);
        sort($xnert);
        $ynert = array_unique($data['yArry']);
        sort($ynert);
        $result['x'] = $data['x'];
        $result['y'] = $data['y'];

        if($data['area']== Game::Rule_Enter || $data['area']== Game::Rule_Exit){
            if(count($xnert) == 4 && count($ynert) == 2){
                if($data['x'] - $Point < $xnert[1]) {
                    $result['x'] = $data['x']  + $Point * 2;
                }

                if($data['x'] + $Point > $xnert[2]) {
                    $result['x'] = $data['x']  - $Point * 2;
                }

                if(($data['y'] - $Point) < $ynert[0]){
                    $result['y'] = $data['y'] + $Point;
                }

                if(($data['y'] + $Point) > $ynert[1]){
                    $result['y'] = $data['y'] - $Point ;
                }
            }
        }else {

            if($data['x'] -$Point < $xnert[0])
            {
                $result['x'] = $data['x'] + $Point;
            }
            if($data['x'] + $Point > $xnert[1])
            {
                $result['x'] = $data['x'] - $Point ;
            }

            if($data['y'] - $Point <  $ynert[1]){
                $result['y'] = $data['y'] + $Point * 2;
            }

            if($data['y'] + $Point > $ynert[2]){
                $result['y'] = $data['y'] - $Point * 2;
            }
        }
        return $result;
    }
    /*
     *  如果筹码离边界比较近，会压线，需要调整想，x,y的坐标,筹码占用多大比例的空间
     *
     */
    public function adjustPoints($data){
        //同串长方形
        $Point = Game::Xpoint;

        if($data['subarea'] == Game::Rule_Same ||$data['subarea'] == Game::Rule_Bunch){
           return $this->adjustRectPoints($data,$Point);
        }
        //角 六边形
        if(($data['subarea'] == Game::Rule_LeftAngle ||$data['subarea'] == Game::Rule_RightAngle)){
           return $this->adjustAnglePoints($data,$Point);
        }

        //粘
        if($data['subarea'] == Game::Rule_LeftStick ||$data['subarea'] == Game::Rule_RightStick){
            return $this->adjustStickPoints($data,$Point);
        }

        //出入和龙虎
        if($data['subarea'] == Game::Rule_Center) {
             return $this->adjustCentralPoints($data,$Point);
        }
    }

    /*
        计算筹码坐标是否在该多边形区域内或外:
    从目标点出发引一条射线，看这条射线和多边形所有边的交点数目。如果有奇数个交点，则说明在内部，如果有偶数个交点，则说明在外部。
       input : $nvert: 多边形的顶点数,按照顺序摆列
               $vertxArry, $vertyArry: 顶点X坐标和Y坐标分别组成的数组
                $testx, $testy: 需要测试的点的X坐标和Y坐标
    */

    public function pnpoly($nvert, $vertxArry, $vertyArry, $testx, $testy) {
        $result = FALSE;

        for ($i = 0, $j = $nvert - 1; $i < $nvert; $j = $i++) {
            if ((($vertyArry[$i] > $testy) != ($vertyArry[$j] > $testy)) &&
                ($testx < ($vertxArry[$j] - $vertxArry[$i]) * ($testy - $vertyArry[$i]) / ($vertyArry[$j] - $vertyArry[$i]) + $vertxArry[$i])) {
                $result = !$result;
            }
        }
        return $result;
    }

    /*
        依据筹码坐标点，判断是否合法，如果合法返回对应的区域和子区域

    */

    public function calAreaSubArea($x, $y) {
        $data['result'] = FALSE;
        $data['x'] = $x;
        $data['y'] = $y;
        //先构造多边形区域数组
        $PixelArray = array(array(0.3962, 0.60, 0.7077, 0.2885), array(0.6096, 0.6096, 0.7192, 0.7192), //入的x,y数组坐标
            array(0, 0, 0, 0, 0, 0), array(0, 0, 0, 0, 0, 0), array(0, 0, 0, 0, 0, 0), array(0, 0, 0, 0, 0, 0), //入的左右角无效数据
            array(0.3827, 0.6154, 0.6154, 0.3827), array(0.8673, 0.8673, 0.9788, 0.9788), //串的x,y坐标
            array(0.1365, 0.3731, 0.3731, 0.0231), array(0.8673, 0.8673, 0.9788, 0.9788), //左边粘x,y坐标
            array(0.6231, 0.6231, 0.8596, 0.9731), array(0.9788, 0.8673, 0.8673, 0.9788), //右边粘x,y坐标
            array(0.3827, 0.3827, 0.6154, 0.6154), array(0.8596, 0.7385, 0.7385, 0.8596), //同x,y坐标

            array(0.3942, 0.3942, 0.2808, 0.2808), array(0.4019, 0.6019, 0.7096, 0.2904), //龙的x,y数组坐标
            array(0.3731, 0.3731, 0.2615, 0.2615, 0.1423, 0.1423), array(0.1423, 0.2673, 0.2673, 0.3731, 0.3731, 0.1423),//龙出角数据
            array(0.1423, 0.2615, 0.2615, 0.3731, 0.3731, 0.1423), array(0.6231, 0.6231, 0.7385, 0.7385, 0.8577, 0.8577), //龙入角数据
            array(0.1308, 0.1308, 0.0192, 0.0192), array(0.3827, 0.6154, 0.6154, 0.3827), //龙串的数据
            array(0.1327, 0.1327, 0.0192, 0.0192), array(0.1365, 0.3731, 0.3731, 0.025), //龙粘龙1 x,y坐标
            array(0.0192, 0.1327, 0.1327, 0.0192), array(0.625, 0.625, 0.8615, 0.975), //龙粘龙2 x,y坐标
            array(0.2615, 0.1385, 0.1385, 0.2615), array(0.3827, 0.3827, 0.6154, 0.6154), //龙同x,y坐标

            array(0.2904, 0.7077, 0.5962, 0.4038), array(0.2846, 0.2846, 0.3981, 0.3981), //出的x,y数组坐标
            array(0, 0, 0, 0, 0, 0), array(0, 0, 0, 0, 0, 0), array(0, 0, 0, 0, 0, 0), array(0, 0, 0, 0, 0, 0), //出的左右角无效数据
            array(0.3788, 0.6192, 0.6192, 0.3788), array(0.0192, 0.0192, 0.1365, 0.1365), //出串的x,y数据
            array(0.6231, 0.9769, 0.8635, 0.6231), array(0.0192, 0.0192, 0.1365, 0.1365), //出粘出2 x,y坐标
            array(0.0192, 0.1365, 0.3769, 0.3769), array(0.0192, 0.1365, 0.1365, 0.0192), //出粘出1 x,y坐标 ??
            array(0.3788, 0.3788, 0.6192, 0.6192), array(0.1423, 0.2673, 0.2673, 0.1423), //出同x,y坐标

            array(0.6038, 0.6038, 0.7154, 0.7154), array(0.6019, 0.4019, 0.2904,0.7096,), //虎的x,y数组坐标
            array(0.7365, 0.8558, 0.8558, 0.6231, 0.6231, 0.7365), array(0.625, 0.625, 0.8577, 0.8577, 0.7385, 0.7385), //虎入角数据
            array(0.6231, 0.8558, 0.8558, 0.7365, 0.7365, 0.6231), array(0.1423, 0.1423, 0.3731, 0.3731, 0.2673, 0.2673), //虎出角数据
            array(0.8654, 0.8654, 0.9769, 0.9769), array(0.6154, 0.3827, 0.3827, 0.6154), //虎串的x,y数据
            array(0.8654, 0.9769, 0.9769, 0.8654), array(0.625, 0.625, 0.975, 0.8654), //虎粘虎2 x,y坐标
            array(0.8654, 0.9769, 0.9769, 0.8654), array(0.3731, 0.3731, 0.025, 0.1365), //虎粘虎1 x,y坐标
            array(0.7365, 0.8558, 0.8558, 0.7365), array(0.3827, 0.3827, 0.6154, 0.6154), //虎同x,y坐标
        );
        $arrCount   = count($PixelArray) - 1;

        for ($index = 0; $index < $arrCount;) {
            $tmp       = $index;
            $vertxArry = $PixelArray[$index++];
            $vertyArry = $PixelArray[$index++];
            $nvert     = 4; //四边形
            if ($tmp == 16 || $tmp == 18 || $tmp == 44 | $tmp == 46) //龙入角，龙出角，虎入角，虎出角
            {
                $nvert = 6; //6边形
            }
            $result = $this->pnpoly($nvert, $vertxArry, $vertyArry, $x, $y);
            if ($result == TRUE) {
                $data['result']  = TRUE;
                $data['area']    = floor($tmp / 14);
                $data['subarea'] = ($tmp / 2) % 7;
                $data['xArry'] = $vertxArry;
                $data['yArry'] = $vertyArry;
                $data['nvert'] = $nvert;
                break;
            }
        }

        if($data['result'] == TRUE)
        {
            $result = $this->adjustPoints($data);
            echo "###########################";
            var_dump($result);
            $data['x'] = $result['x'];
            $data['y'] = $result['y'];
        }

        return $data;
    }

    /*
        选择下注筹码
    */
    public function chooseChip($arrData) {
        $data       = $arrData['data'];
        $operation  = $arrData['operation'];
        $account_id = $arrData['account_id'];

        $session            = $arrData['session'];
        $Verification_Model = Verification_Model::getModelObject();
        if (FALSE == $Verification_Model->checkRequestSession($account_id, $session)) {
            return OPT_CONST::NO_RETURN;
        }

        if (!isset($data['room_id']) || $data['room_id'] <= 0) {
            $this->logMessage('error', "function(chooseChip):lack of room_id" . " in file" . __FILE__ . " on Line " . __LINE__);
            return $this->_missingPrameterArr($operation, "room_id");
        }

        //查看是否庄家操作
        $room_id   = $data['room_id'];
        $banker_id = $this->queryBanker($room_id);
        if ($banker_id == $account_id) {
            $this->logMessage('error', "  function(chooseChip): banker can't chooese chip " . " in file" . __FILE__ . " on Line " . __LINE__);
            return array("result" => OPT_CONST::FAILED, "result_message" => "庄家不能下注");
        }

//        //设置筹码的区域
//        if (!isset($data['area']) || !in_array($data['area'], Game::Areas)) {
//            $this->logMessage('error', "function(chooseChip): error area ");
//            return $this->_missingPrameterArr($operation, " error area");
//        }
//        //设置筹码的子区域
//        if (!isset($data['subarea']) || !in_array($data['subarea'], Game::SubAreas)) {
//            $this->logMessage('error', "function(chooseChip):error subarea");
//            return $this->_missingPrameterArr($operation, "room_id");
//        }

        if (!isset($data['score']) || !in_array($data['score'], Game::Chip_Array)) {
            $this->logMessage('error', "function(chooseChip):lack of score" . " in file" . __FILE__ . " on Line " . __LINE__);
            return $this->_missingPrameterArr($operation, "score");
        }

        //$area    = $data['area'];
        //$subarea = $data['subarea'];
        $score  = $data['score'];
        $xpoint = $data['x'];
        $ypoint = $data['y'];

        $result = $this->calAreaSubArea($xpoint, $ypoint);

        if ($result['result'] == FALSE) {
            return array("result" => OPT_CONST::FAILED, "operation" => $operation, "result_message" => "无效的X,y坐标点");
        }
        $area    = $result['area'];
        $subarea = $result['subarea'];
        $x = $result['x'];
        $y = $result['y'];
        $this->writeLog( "point: ".$xpoint."x: ".$x. "pointy: ". $ypoint." y: ".$y);
        //for test begin
        //$area    = rand(Game::Rule_Enter, Game::Rule_Tiger);
        //$subarea = rand(Game::Rule_Center, Game::Rule_Same);
        // test end

        //保持原有的筹码位置广播给其它用户或放置数组中
//        $chip = array("area" => $area, "subarea" => $subarea, "x" => $xpoint, "y" => $ypoint, "score" => $score);
        $chip = array("area" => $area, "subarea" => $subarea, "x" => $x, "y" => $y, "score" => $score);
        //角的放置位置只能放在虎和龙的区域
//        if (($subarea == Game::Rule_LeftAngle || $subarea == Game::Rule_RightAngle) && ($area == Game::Rule_Enter || $area == Game::Rule_Exit)) {
//            $this->logMessage('error', "function(chooseChip):area invalid with angle" . " in file" . __FILE__ . " on Line " . __LINE__);
//            return $this->_missingPrameterArr($operation, "area");
//        }

        //角的赌注都放在龙或者虎的四个角中，后端依据奖的结果重新调整位置
        $prize = $this->queryPrize($room_id);
        if ($subarea == Game::Rule_LeftAngle || $subarea == Game::Rule_RightAngle) {
            if ($prize != $area) {
                if ($prize == Game::Rule_Enter && $area == Game::Rule_Dragon && $subarea == Game::Rule_RightAngle) {
                    $area    = Game::Rule_Enter;
                    $subarea = Game::Rule_LeftAngle;
                }

                if ($prize == Game::Rule_Enter && $area == Game::Rule_Tiger && $subarea == Game::Rule_LeftAngle) {
                    $area    = Game::Rule_Enter;
                    $subarea = Game::Rule_RightAngle;
                }

                if ($prize == Game::Rule_Dragon && $area == Game::Rule_Enter && $subarea == Game::Rule_LeftAngle) {
                    $area    = Game::Rule_Dragon;
                    $subarea = Game::Rule_RightAngle;
                }

                if ($prize == Game::Rule_Dragon && $area == Game::Rule_Exit && $subarea == Game::Rule_RightAngle) {
                    $area    = Game::Rule_Dragon;
                    $subarea = Game::Rule_LeftAngle;
                }

                if ($prize == Game::Rule_Exit && $area == Game::Rule_Tiger && $subarea == Game::Rule_RightAngle) {
                    $area    = Game::Rule_Exit;
                    $subarea = Game::Rule_LeftAngle;
                }

                if ($prize == Game::Rule_Exit && $area == Game::Rule_Dragon && $subarea == Game::Rule_LeftAngle) {
                    $area    = Game::Rule_Exit;
                    $subarea = Game::Rule_RightAngle;
                }

                if ($prize == Game::Rule_Tiger && $area == Game::Rule_Enter && $subarea == Game::Rule_RightAngle) {
                    $area    = Game::Rule_Tiger;
                    $subarea = Game::Rule_LeftAngle;
                }

                if ($prize == Game::Rule_Tiger && $area == Game::Rule_Exit && $subarea == Game::Rule_LeftAngle) {
                    $area    = Game::Rule_Tiger;
                    $subarea = Game::Rule_RightAngle;
                }

            }

        }

        //$this->deleteRoomTimer($room_id);

        //查询筹码下注上限
        $setting     = $this->queryRoomSetting($room_id);
        $upper_limit = isset($setting[Redis_Const::Room_Field_UpperLimit]) ? $setting[Redis_Const::Room_Field_UpperLimit] : Default_UpperLimit_Score;
        $total       = $this->queryChip($room_id, $account_id);

        if ($upper_limit != 0) {
            if ($total + $score > $upper_limit) {
                return array("result" => OPT_CONST::CHIP_AMOUNT_LIMIT, "operation" => $operation, "result_message" => "您的下注已达上限");
            } else if ($total + $score == $upper_limit) {
                //更新状态为停止下注状态
                //update ueser account status to stop net
                $this->updateAccountStatus($room_id, $account_id, Game::AccountStatus_StopBet);

                $this->writeLog("[$room_id] ($account_id) 因为达到上限自动停止下注");
                $this->StopBetProcess($room_id);

            }
        }

        //通知前端下注成功
        $this->pushMessageToCurrentClient(array("result" => OPT_CONST::SUCCESS, "operation" => $operation,
                                              "chip" => $chip, "result_message" => "您的下注成功"));

        //把新的筹码放筹码数组中，不区分账号
        $this->updateChipArray($room_id, $chip);

        //更新个人总筹码数量
        $this->updateChip($room_id, $account_id, $score);
        //udpate user total and detail chips
        $this->updateChipArea($room_id, $account_id, $area, $subarea, $score);
        //不区分玩家，所有闲家的筹码
        $this->updateAllChipArea($room_id, $area, $subarea, $score);

        $chips   = $this->queryChip($room_id, $account_id);
        $msg_arr = array(
            'result' => 0,
            'operation' => 'UpdateAccountScore',
            'result_message' => "下注",
            'data' => array(
                'account_id' => $account_id,
                'chip' => $chip,
                'chips' => $chips,
            )
        );
        $this->pushMessageToGroup($room_id, $msg_arr);
        $this->writeLog("[$room_id] ($account_id) 下注" . $score . "分");

        return OPT_CONST::NO_RETURN;
    }

    public function StopBet($arrData) {
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
            $this->logMessage('error', "function(chooseChip):lack of room_id" . " in file" . __FILE__ . " on Line " . __LINE__);
            return $this->_missingPrameterArr($operation, "room_id");
        }

        //查看是否庄家操作
        $room_id   = $data['room_id'];
        $banker_id = $this->queryBanker($room_id);
        if ($banker_id == $account_id) {
            $this->logMessage('error', "  function(chooseChip): banker can't chooese chip " . " in file" . __FILE__ . " on Line " . __LINE__);
            return array("result" => OPT_CONST::FAILED, "result_message" => "庄家不能停止下注");
        }

        //update ueser account status to stop net
        $this->updateAccountStatus($room_id, $account_id, Game::AccountStatus_StopBet);
        $msg_arr = array(
            'result' => 0,
            'operation' => $operation,
            'result_message' => "停止下注",
            'data' => array(
                'account_id' => $account_id,
                'account_status' => Game::AccountStatus_StopBet
            )
        );
        $this->writeLog("[$room_id] ($account_id) 停止下注");

        $this->pushMessageToCurrentClient($msg_arr);

        $this->StopBetProcess($room_id);

        return OPT_CONST::NO_RETURN;
    }

    /*
        开奖
    */
    public function showPrize($arrData) {
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

        //查看是否庄家操作
        $banker_id = $this->queryBanker($room_id);
        if ($banker_id != $account_id) {
            $this->logMessage('error', "  function(showPrize): playid can't showPrize " . " in file" . __FILE__ . " on Line " . __LINE__);
            return array("result" => "-203", "result_message" => "闲家不能开奖");

        }

        $this->deleteRoomTimer($room_id);

        $this->startWinRound($room_id);
        $this->writeLog("showPrize");
        return OPT_CONST::NO_RETURN;
    }

    /*
       查看用户所有下注信息
   */
    public function showChips($arrData) {
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

        if (!isset($data['room_id']) || $data['room_id'] <= 0) {
            $this->logMessage('error', "function(showChips):lack of room_id" . " in file" . __FILE__ . " on Line " . __LINE__);
            return $this->_missingPrameterArr($operation, "room_id");
        }

        $room_id   = $data['room_id'];
        $banker_id = $this->queryBanker($room_id);

        if ($banker_id == $account_id)//查看所有用户下注信息
        {
            $chips     = $this->getAllPlayChip($room_id);
            $is_banker = 1;


        } else { //闲家查看自己下注信息
            $chips     = $this->getPlayChip($room_id, $account_id);
            $is_banker = 0;
        }

        $msg_arr = array(
            'result' => 0,
            'operation' => $operation,
            'result_message' => "下注",
            'data' => array(
                'account_id' => $account_id,
                'chips' => $chips,
                'is_banker' => $is_banker,
            )
        );
        $this->writeLog("[$room_id] ($account_id) 下注" . $chips);

        return $msg_arr;
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
            'voice_num' => $voice_num
        ), "result_message" => "发送声音");
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
            'local_id' => $local_id,
            'time' => $time,
            'server_id' => $server_id
        ), "result_message" => "说话声音");
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
