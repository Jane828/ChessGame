<?php
/**
 * Created by PhpStorm.
 * User: oujiaxuan
 * Date: 2019/02/16
 * Time: 15:47
 */
include_once __DIR__ ."/common_model.php";
include_once dirname(__DIR__) . "/../libraries/Redis.php";
class Box_Model extends Public_Models
{
    public function __construct()
    {
        header('Content-Type: text/html; charset=utf-8');
        parent::__construct();
    }

    /**
     * 创建包厢
     * @param $arrData
     * @return array
     */
    public function addBox($arrData){
        if(!isset($arrData) || empty($arrData["account_id"])){
            log_message('error', "function(addBox): parameter error arrData:". json_encode($arrData) ." in file ".__FILE__." on Line ".__LINE__);
            return $this->missingPrameterArr("account_id");
        }

        if(empty($arrData["box_name"]) || empty($arrData["data"]) || empty($arrData["game_type"])){
            log_message('error', "function(addBox): parameter error arrData:". json_encode($arrData) ." in file ".__FILE__." on Line ".__LINE__);
            return $this->missingPrameterArr("box_name or game_type or data");
        }

        $result = array();
        $dealerDB = Game_CONST::DBConst_Name;
        $account_id = intval($arrData["account_id"]);
        $game_type  = intval($arrData["game_type"]);
        $box_name   = $arrData["box_name"];
        $box_config = $arrData["data"];

        $ticket_query = $this->getAccountTicket($account_id);
        if($ticket_query == DB_CONST::DATA_NONEXISTENT)
        {
            return array('result'=>OPT_CONST::FAILED,'data'=>$result,'result_message'=>"房卡不足");
        }

        if(G_CONST::Ticket_limit > $ticket_query['ticket_count'])
        {
            return array('result'=>OPT_CONST::FAILED,'data'=>$result,'result_message'=>"房卡不足".G_CONST::Ticket_limit."张");
        }

        //判断包厢创建数量，生成包厢号
        $query_sql = 'select box_number from '.Box_Info.' where account_id = '.$account_id;
        $box_list = $this->getDataBySql($dealerDB, 0, $query_sql);
        $number_list = array();
        if($box_list != DB_CONST::DATA_NONEXISTENT){
            if(count($box_list) >= G_CONST::Box_limit){
                return array('result'=>OPT_CONST::FAILED,'data'=>$result,'result_message'=>"创建包厢数量达最大限制".G_CONST::Box_limit."个");
            }else{
                foreach($box_list as $key=>$value){
                    array_push($number_list, $value);
                }
            }
        }
        $box_number = $account_id * 10000 + rand(1,10000);
        if(!empty($number_list) && in_array($box_number, $number_list) ){
            for($x=0;$x<10;$x++){
                $box_number = $account_id * 10000 + rand(1,10000);
                if(!in_array($box_number, $number_list)){
                    break;
                }
            }
        }

        //校验包厢参数
        $box_config = $this->checkBoxConfig($box_config, $box_number);
        if($box_config == OPT_CONST::FAILED){
            return array('result'=>OPT_CONST::FAILED,'data'=>$result,'result_message'=>"包厢配置不正确");
        }

        //创建包厢
        $box_array["account_id"] = $account_id;
        $box_array["box_number"] = $box_number;
        $box_array["game_type"]  = $game_type;
        $box_array["box_name"] = $box_name;
        $box_array["config"] = $box_config;
        $box_id = $this->getInsertID($dealerDB, Box_Info, $box_array);
        if($box_id == DB_CONST::INSERT_FAILED){
            log_message("error", "func(addBox) add box fail;" ." in file ".__FILE__." on line ".__LINE__);
            return array('result'=>OPT_CONST::FAILED,'data'=>$result,'result_message'=>"新建包厢失败");
        }
        $result = $box_array;
        $result["box_id"] = $box_id;
        return array('result'=>OPT_CONST::SUCCESS,'data'=>$result,'result_message'=>"新建包厢成功");
    }

    /**
     * 获取包厢列表
     * @param $arrData
     * @return array
     */
    public function getBoxList($arrData){
        if(!isset($arrData) || empty($arrData["account_id"])){
            log_message('error', "func(getBoxList): parameter error arrData:". json_encode($arrData) ." in file ".__FILE__." on Line ".__LINE__);
            return $this->missingPrameterArr("account_id");
        }

        $dealerDB = Game_CONST::DBConst_Name;
        $result = array();
        $account_id = intval($arrData["account_id"]);
        $query_sql = "select A.box_id, A.account_id, A.box_number, A.game_type, A.status, A.room_count, A.box_name, B.nickname, config,0 as player_num from ". Box_Info . ' A, '.WX_Account." B where A.account_id = $account_id and A.account_id=B.account_id";
	$box_list = $this->getDataBySql($dealerDB, 0, $query_sql);
        if($box_list == DB_CONST::DATA_NONEXISTENT || !is_array($box_list)){
            return array('result'=>OPT_CONST::FAILED,'data'=>$result,'result_message'=>"未找到相关数据");
        }
        $time = date('Y-m-d H:i:s', time() - 3600);
        foreach($box_list as $key=>$value){
            $box_id = $value['box_id'];
            $game_type = $value['game_type'];
            $table = $this->getGameTable($game_type);

            $query_sql = ' select count(id) as num, box_id from '.Box_Room." A, $table as B where A.box_id = $box_id and A.create_time > '$time' and A.room_id=B.room_id and B.is_close=0 ";
            $room_count = $this->getDataBySql(Game_CONST::DBConst_Name, 1, $query_sql);
	    log_message("Info", "liuyongroom_count===========".json_encode($room_count));
            if($room_count['num']!=DB_CONST::DATA_EXIST){
                $box_list[$key]['room_count'] = $room_count['num'];
                $flag = true;
                if($flag){
                    $redis = Redis::getInstance();
                    log_message("error", "1111111" ." in file ".__FILE__." on line ".__LINE__);
                    $BoxCurrentRoom_Key = strtr(G_CONST::BoxCurrentRoom_Key, array("[boxnumber]"=>$value['box_number']));
                    log_message("error", "2222222：$BoxCurrentRoom_Key" ." in file ".__FILE__." on line ".__LINE__);
                    $rsRoom_count = $redis->scard($BoxCurrentRoom_Key);
                    log_message("error", "func(getBoxList) redis room_count : $rsRoom_count;" ." in file ".__FILE__." on line ".__LINE__);
                }
            }
        }
	log_message("Info", "liuyongcount==");
	log_message("Info", "liuyongboxlist=======".json_encode($box_list));
        return array('result'=>OPT_CONST::SUCCESS,'data'=>$box_list,'result_message'=>"查询包厢成功");
    }

    /**
     * 获取包厢信息
     * @param $arrData
     * @return array
     */
    public function getBoxInfo($arrData){
        if(!isset($arrData) || empty($arrData["account_id"])){
            log_message('error', "func(getBoxInfo): parameter error arrData:". json_encode($arrData) ." in file ".__FILE__." on Line ".__LINE__);
            return $this->missingPrameterArr("account_id");
        }
        if(empty($arrData["box_id"]) || empty($arrData["box_number"])){
            log_message('error', "func(getBoxInfo): parameter error arrData:". json_encode($arrData) ." in file ".__FILE__." on Line ".__LINE__);
            return $this->missingPrameterArr("bix_id or bix_number");
        }

        $dealerDB = Game_CONST::DBConst_Name;
        $account_id = intval($arrData["account_id"]);
        $box_number = intval($arrData["box_number"]);
        $box_id = intval($arrData["box_id"]);

        $query_sql = "select config from ".Box_Info." where box_id = '$box_id' and account_id = '$account_id' and box_number = '$box_number' limit 1";
        $box_info = $this->getDataBySql($dealerDB, 1, $query_sql);
        if($box_info == DB_CONST::DATA_NONEXISTENT){
            return array('result'=>OPT_CONST::FAILED,'data'=>array(),'result_message'=>"未找到相应包厢");
        }
        return array('result'=>OPT_CONST::SUCCESS,'data'=>$box_info,'box_id'=>$box_id, 'account_id'=>$account_id,'result_message'=>"查找包厢信息成功");
    }

    /**
     * 修改包厢配置
     * @param $arrData
     * @return array
     */
    public function setBoxInfo($arrData){
        if(!isset($arrData) || empty($arrData["account_id"]) || empty($arrData["box_id"]) || empty($arrData["box_name"]) || empty($arrData["data"])){
            log_message('error', "func(getBoxInfo): parameter error arrData:". json_encode($arrData) ." in file ".__FILE__." on Line ".__LINE__);
            return $this->missingPrameterArr("parameter");
        }

        $deakerDB = Game_CONST::DBConst_Name;
        $account_id = intval($arrData["account_id"]);
        $box_name = addslashes($arrData["box_name"]);
        $box_config = $arrData["data"];
        $box_id = intval($arrData["box_id"]);
        $query_sql = ' select box_number from '.Box_Info.' where account_id ='. $account_id.' and box_id = '. $box_id;
        $box_number = $this->getDataBySql(Game_CONST::DBConst_Name, 1, $query_sql);
        if($box_number == DB_CONST::DATA_NONEXISTENT){
            log_message('error', 'function(setBoxStatus): '."aid:$account_id bid:$box_id" .' in file '.__FILE__.' on Line '.__LINE__);
            return array('result'=>OPT_CONST::FAILED,'data'=>array(),'result_message'=>"更新包厢配置失败");
        }
	log_message("info", "liuyongsetboxinfoconfig====".json_encode($box_config));
        $box_config = $this->checkBoxConfig($box_config, $box_number['box_number']);
        if($box_config == OPT_CONST::FAILED){
            return array('result'=>OPT_CONST::FAILED,'data'=>array(),'result_message'=>"包厢配置不正确");
        }

        $str = " box_name = '$box_name', config = '$box_config'";
        $where = " box_id = $box_id and account_id = $account_id";
        $update_query = $this->changeNodeValue($deakerDB, Box_Info, $str, $where);
        if($update_query == DB_CONST::UPDATE_FAILED){
            return array('result'=>OPT_CONST::FAILED,'data'=>array(),'result_message'=>"更新包厢配置失败");
        }
        return array('result'=>OPT_CONST::SUCCESS,'data'=>array(),'result_message'=>"更新包厢配置成功");
    }

    /**
     * 修改包厢状态
     * @param $arrData
     * @return array
     */
    public function setBoxStatus($arrData){
        if(!isset($arrData) || empty($arrData['account_id']) || empty($arrData['box_id']) || !isset($arrData['status'])){
            log_message('error', 'function(setBoxStatus): parameter error arrData:'. json_encode($arrData) .' in file '.__FILE__.' on Line '.__LINE__);
            return $this->missingPrameterArr("parameter");
        }

        $deakerDB = Game_CONST::DBConst_Name;
        $account_id = intval($arrData['account_id']);
        $box_id = intval($arrData['box_id']);
        $status = intval($arrData['status']);

        $str = " status = $status";
        $where = " box_id = $box_id and account_id = $account_id";
        $update_query = $this->changeNodeValue($deakerDB, Box_Info, $str, $where);
        if($update_query == DB_CONST::UPDATE_FAILED){
            return array('result'=>OPT_CONST::FAILED,'data'=>array(),'result_message'=>'更新包厢状态失败');
        }
        return array('result'=>OPT_CONST::SUCCESS,'data'=>array(), 'status'=>$status, 'result_message'=>'更新包厢状态成功');
    }

    /**
     * 获取包厢牌桌战况
     * @param $arrData
     * @return array
     */
    public function boxCondition($arrData){
        if(!isset($arrData) || empty($arrData['account_id']) || empty($arrData['box_id'])){
            log_message('error', 'function(boxCondition): parameter error arrData:'. json_encode($arrData) .' in file '.__FILE__.' on Line '.__LINE__);
            return $this->missingPrameterArr('account_id or box_id');
        }

        $account_id = intval($arrData['account_id']);
        $box_id = intval($arrData['box_id']);
        $time = date('Y-m-d H:i:s', time() - (3600 * 6));
        $end_time = date('Y-m-d H:i:s', time() - 1800);
        $query_sql = ' select game_type from '.Box_Info.' where account_id ='.$account_id.' and box_id = '. $box_id;
        $box_info = $this->getDataBySql(Game_CONST::DBConst_Name, 1, $query_sql);
        if($box_info == DB_CONST::DATA_NONEXISTENT){
            return array('result'=>OPT_CONST::FAILED,'data'=>array(),'result_message'=>'未找到包厢信息');
        }
        $game_type = $box_info['game_type'];
        $table = $this->getGameTable($game_type);

        $query_sql = ' select room_id, create_time from '.Box_Room.' where box_id ='.$box_id." and create_time > '$time' order by room_id desc";
        $room_id = $this->getDataBySql(Game_CONST::DBConst_Name, 0, $query_sql);
        if($room_id == DB_CONST::DATA_NONEXISTENT){
            return array('result'=>OPT_CONST::FAILED,'data'=>array(),'result_message'=>'未找到包厢房间');
        }

        $room_list = array();
        $exec_room = array();
        if(!is_array($room_id)) {
            log_message('error', 'fucntion(boxCondition) not found room record '.$room_id.'; in file '.__FILE__.' on line '.__LINE__);
            return array('result'=>OPT_CONST::FAILED,'data'=>array(),'result_message'=>"未找到包厢牌桌记录");
        }
        foreach($room_id as $key=>$value){
            array_push($room_list, $value['room_id']);
            if($end_time <= $value['create_time']){
                $exec_room[$value['room_id']] = true;
            }
        }

        $room_list_str = "'".implode("','",$room_list)."'";
        //$query_sql = ' select A.room_id, A.create_time,A.room_number,A.account_id,A.is_close,A.room_status,A.game_type,A.times_type,B.game_num,B.player_num from '."$table A left join in ".Room_Scoreboard." B where A.room_id in($room_list_str) and A.room_id = B.room_id";
        $query_sql = " select A.*, B.game_num,B.player_num from (select room_id, create_time,room_number,account_id,is_close,room_status,game_type,player_max_num,total_num from $table where room_id in($room_list_str)) as A left join ".Room_Scoreboard." as B on A.room_id = B.room_id order by room_id desc";
        log_message('error', "sql:$query_sql");
        $box_condition = $this->getDataBySql(Game_CONST::DBConst_Name, 0, $query_sql);
        if($box_condition == DB_CONST::DATA_NONEXISTENT && !is_array($box_condition)){
            return array('result'=>OPT_CONST::FAILED,'data'=>array(),'result_message'=>'未找到包厢牌桌记录');
        }
        log_message('error', 'fucntion(boxCondition) not found box_condition '.json_encode($box_condition).'; in file '.__FILE__.' on line '.__LINE__);
        $exec_room = $this->getRoomExec($exec_room, $game_type);
        log_message('error', 'fucntion(boxCondition) not found exec_room '.json_encode($exec_room).'; in file '.__FILE__.' on line '.__LINE__);
        foreach($box_condition as $key=>$value){
            $value['create_time'] = date('Y-m-d H:i:s', $value['create_time']);
            if(isset($exec_room[$value['room_id']])){
                $room_info = $exec_room[$value['room_id']];
                $box_condition[$key]['total_num'] = $room_info['total_num'];
                $box_condition[$key]['game_num'] = $room_info['game_num'];
                $box_condition[$key]['player_num'] = $room_info['player_num'];
                $box_condition[$key]['player_max_num'] = $room_info['player_max_num'];
            }else{
                if(empty($value['game_num'])){
                    $box_condition[$key]['game_num'] = 0;
                }
                if(empty($value['player_num'])){
                    $box_condition[$key]['player_num'] = 0;
                }
            }

        }

        return array('result'=>OPT_CONST::SUCCESS,'data'=>$box_condition,'result_message'=>'查询牌桌状况成功');
    }

    /**
     * 解散包厢
     * @param $arrData
     * @return array
     */
    public function delBox($arrData){
        if(!isset($arrData) || empty($arrData['account_id']) || empty($arrData['box_id'])){
            log_message('error', 'function(delBox): parameter error arrData:'. json_encode($arrData) .' in file '.__FILE__.' on Line '.__LINE__);
            return $this->missingPrameterArr('account_id or box_id');
        }

        $account_id = intval($arrData['account_id']);
        $box_id = intval($arrData['box_id']);
        $whereArr = array(
            'account_id' => $account_id,
            'box_id' => $box_id
        );
        $delete_query = $this->deleteFunc(Game_CONST::DBConst_Name, Box_Info, $whereArr);
        if($delete_query == DB_CONST::UPDATE_FAILED){
            return array('result'=>OPT_CONST::FAILED,'data'=>array(),'result_message'=>"解散包厢失败");
        }
        return array('result'=>OPT_CONST::SUCCESS,'data'=>array(),'result_message'=>"解散包厢成功");
    }

    /**
     * 检查包厢配置参数
     * @param $config
     * @return int|string
     */
    public function checkBoxConfig($config, $box_number){
        if(empty($config) || empty($box_number)){
            log_message('error', "function(checkBoxConfig): parameter error arrData:". json_encode($config) ." in file ".__FILE__." on Line ".__LINE__);
            return OPT_CONST::FAILED;
        }

        $config = is_array($config)?$config:json_decode($config,true);
        if(empty($config)){
            log_message('error', "function(checkBoxConfig): box_config is empty"." in file ".__FILE__." on Line ".__LINE__);
            return OPT_CONST::FAILED;
        }
        $game_type = isset($config['game_type'])?$config['game_type']:0;
        if(empty($game_type)){
            log_message('error', "function(checkBoxConfig): box_config->game_type is empty"." in file ".__FILE__." on Line ".__LINE__);
            return OPT_CONST::FAILED;
        }
        $mould_bu = array("data_key","ticket_type","score_type","score_value","rule_type","is_cardfour","is_cardfive",
            "is_cardbomb","is_cardtiny","is_straight","is_flush","is_hulu","is_straightflush",
            "banker_mode","times_type","countDown", "game_type","banker_score_type");

        $mould_fl = array("data_key","chip_type","ticket_count","disable_pk_men","upper_limit","game_type",
            "raceCard","seenProgress","compareProgress","extraRewards","default_score","countDown","allow235GTPanther");

        $mould_sg = array("data_key","banker_mode","score_type","game_type","is_joker","is_bj","ticket_type","countDown");
	$mould_ab = array("data_key","chip_type","ticket_count","banker_mode","upper_limit","first_lossrate","second_lossrate","three_lossrate","game_type","countDown");
        $mould = array();
        switch ($game_type){
            case '5':
            case '9':
            case '71':
            case '91':
            case '12':
            case '13':
                $mould = $mould_bu;
                break;
            case '1':
            case '110':
            case '111':
            case '92':
                $mould = $mould_fl;
                break;
            case '36':
            case '37':
            case '38':
                $mould = $mould_sg;
                break;
	    case '61':
	    case '62':
	    case '63':
		$mould = $mould_ab;
		break;
            default:
                break;
        }

        foreach($mould as $key=>$value){
            if(!isset($config[$value])){
                log_message('error', "function(checkBoxConfig): parameter error mould:". json_encode($mould) .' config'.json_encode($config)." in file ".__FILE__." on Line ".__LINE__);
                return OPT_CONST::FAILED;
            }
        }
        $config["box_number"] = $box_number;
        //$box_config = addslashes(json_encode($config));
        $box_config = json_encode($config);
        return $box_config;
    }

    /**
     * 根据游戏类型获取房间对应的表
     * @param $game_type
     * @return bool|string
     */
    public function getGameTable($game_type){
        switch ($game_type) {
            case '5':
            case '9':
            case '71':
            case '12':
            case '91':
            case '13':
                $table = 'bull_room';
                break;
            case '1':
            case '110':
            case '111':
            case '92':
                $table = 'flower_room';
                break;
            case '2':
                $table = 'landlord_room';
                break;
            case '36':
            case '37':
            case '38':
                $table = 'sangong_room';
                break;
	    case '61':
		$table = 'dark_room';
		break;
            default:
                $table = OPT_CONST::OPERATION_FALSE;
                break;
        }

        return $table;
    }

    /**
     * 根据游戏类型获取房间对应的表
     * @param $game_type
     * @return bool|string
     */
    public function getRoomMenber($game_type){
        switch ($game_type) {
            case '1':
            case '5':
            case '36':
            case '92':
            case '111':
                $menber = 6;
                break;
            case '9':
            case '37':
            case '91':
                $menber = 9;
                break;
            case '2':
	    case '61':
            case '71':
            case '110':
                $menber = 10;
                break;
            case '12':
                $menber = 12;
                break;
            case '13':
            case '38':
                $menber = 13;
                break;
            default:
                $menber = OPT_CONST::OPERATION_FALSE;
                break;
        }
        return $menber;
    }


    public function getRoomExec($room_list, $game_type){
        log_message('error', 'fucntion(getRoomExec) first: '.json_encode($room_list).'; in file '.__FILE__.' on line '.__LINE__);
        if(empty($room_list) || !array($room_list)){
            return array();
        }
        //创建redis连接
        $redis = Redis::getInstance();
        $prefix_arr = array(5 => 6, 13 => 9, 71 => 9, 9 => 9, 12 => 9, 36 => 36, 37 => 37);
        $prefix  = isset($prefix_arr[$game_type]) ? $prefix_arr[$game_type] . ":" : "";
        log_message('error', 'fucntion(getRoomExec) room_list: '.json_encode($room_list).'; in file '.__FILE__.' on line '.__LINE__);
        foreach($room_list as $room_id=>$value){
            $kk = $prefix . 'Room:' . $room_id;
            log_message('error', 'fucntion(getRoomExec) kk111: '.$kk.'; in file '.__FILE__.' on line '.__LINE__);
            $redis_data = $redis->hgetall($prefix . 'Room:' . $room_id);
            if (count($redis_data) == 0) {
                unset($room_list[$room_id]);
                continue;
            }
            log_message('error', 'fucntion(getRoomExec) kk2222: '.$prefix . "RoomSeq:" . $room_id.'; in file '.__FILE__.' on line '.__LINE__);
            $player_num           = $redis->zard($prefix . "RoomSeq:" . $room_id);
            log_message('error', 'fucntion(getRoomExec) kk3333: '.$player_num.'; in file '.__FILE__.' on line '.__LINE__);
            $room_list[$room_id] = array(
                'total_num'      => $redis_data['totalnum'],
                'game_num'       => empty($redis_data['gnum'])?0:$redis_data['gnum'],
                'player_num'    => empty($player_num)?0:$player_num,
                'player_max_num' => $redis_data['player_max_num']
            );
        }
        return $room_list;

    }
}



