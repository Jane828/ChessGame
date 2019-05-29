<?php

include_once 'common_model.php';		//加载数据库操作类
class Activity_Model extends Activity_Common_Model
{
    public function __construct()
    {
        header('Content-Type: text/html; charset=utf-8'); 
        parent::__construct();
    }
    
    /************************************************
                    common function
    *************************************************/
        
    
    /*
        获取活动记录
        
        参数：
            account_id : account_id
        
        返回结果：
            
        
    */
    public function getActivityInfo($arrData)
    {
        $result = array();

        if(!isset($arrData['account_id']) || $arrData['account_id'] == G_CONST::EMPTY_STRING)
        {
            log_message('error', "function(getActivityInfo):lack of account_id"." in file".__FILE__." on Line ".__LINE__);
            return $this->missingPrameterArr("account_id");
        }

        $account_id = $arrData['account_id'];
        $room_number = $arrData['room_number'];
        $game_type = $arrData['game_type'];

        $dealerDB = Game_CONST::DBConst_Name;

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
            case 36:
            case 37:
            case 38:
                $table = 'sangong_room';
                break;
            case 61:
            case 62:
            case 63:
                $table = 'dark_room';
                break;
            default:
                break;
        }

        $where = 'room_number='.$room_number.' and is_delete=0';
        $sql = 'select account_id, box_number from '.$table.' where '.$where;
        $query = $this->getDataBySql($dealerDB,1,$sql);
        if(DB_CONST::DATA_NONEXISTENT != $query) {
            $owner_id = $query['account_id'];
            $box_number = $query['box_number'];
            if($owner_id != $account_id && !in_array($game_type, [91, 92])){
                $where = 'account_id='.$owner_id.' and is_delete=0';
                $sql = 'select is_manage_on from '.WX_Account.' where '.$where;
                $query = $this->getDataBySql($dealerDB,1,$sql);
                if(DB_CONST::DATA_NONEXISTENT != $query) {
                    $is_manage_on = $query['is_manage_on'];
                    if($is_manage_on || $box_number != G_CONST::ORDINARY_ROOM){
                        //用户是否是房主的好友
                        $join_where = 'account_id='.$account_id.' and manager_id='.$owner_id.' and status=1 and is_delete=0';
                        $join_sql = 'select member_id from '.Manage_Member.' where '.$join_where.' limit 1';
                        $join_query = $this->getDataBySql($dealerDB,1,$join_sql);
                        if(DB_CONST::DATA_NONEXISTENT == $join_query && $box_number == G_CONST::ORDINARY_ROOM)
                        {
                            return array('result'=>OPT_CONST::FAILED,'data'=>[],'result_message'=>"房主开启了管理功能，非房主好友无法进入房间");
                        }elseif(DB_CONST::DATA_NONEXISTENT == $join_query && $box_number != G_CONST::ORDINARY_ROOM){
                            return array('result'=>OPT_CONST::FAILED,'data'=>[],'result_message'=>"非房主好友无法进入包厢房间");
                        }
                    }
                }
            }
        }

        return array('result'=>OPT_CONST::SUCCESS,'data'=>$result,'result_message'=>"获取活动记录");
    }
    
    
    
    protected function getPartakeTimestamp($type,$refresh_timestamp)
    {
        $timestamp = time();
        
        if($type == 1)	//一天
        {
            $partake_timestamp = strtotime(date("Y-m-d",$timestamp)) + $refresh_timestamp;
        }
        else if($type == 2)	//一周
        {
            $partake_timestamp = strtotime(date("Y-m-d H:i:s",mktime(0, 0 , 0,date("m"),date("d")-date("w")+1,date("Y")))) + $refresh_timestamp;
        }
        else if($type == 3)	//一个月
        {
            $partake_timestamp = strtotime(date("Y-m-d H:i:s",mktime(0, 0 , 0,date("m"),1,date("Y")))) + $refresh_timestamp;
        }
        else	//永久
        {
            $partake_timestamp = 0;
        }
        
        return $partake_timestamp;
    }
    
    
    
    /*
        领取活动奖励
        
        参数：
            activity_id : 活动ID
            account_id : account_id
        
        返回结果：
            
        
    */
    public function updateActivityOpt($arrData)
    {
        $timestamp = time();
        $result = array();
        
        if(!isset($arrData['account_id']) || $arrData['account_id'] == G_CONST::EMPTY_STRING)
        {
            log_message('error', "function(updateActivityOpt):lack of account_id"." in file".__FILE__." on Line ".__LINE__);
            return $this->missingPrameterArr("account_id");
        }
        if(!isset($arrData['activity_id']) || $arrData['activity_id'] == G_CONST::EMPTY_STRING)
        {
            log_message('error', "function(updateActivityOpt):lack of activity_id"." in file".__FILE__." on Line ".__LINE__);
            return $this->missingPrameterArr("activity_id");
        }
     
        $dealerDB = Game_CONST::DBConst_Name; 
        
        $account_id = $arrData['account_id'];
        $activity_id = $arrData['activity_id'];
        
        //获取当前活动
        $activity_where = 'activity_id='.$activity_id.' and is_delete=0';
        $activity_sql = 'select activity_id,start_timestamp,end_timestamp,refresh_timestamp,type,content,ticket_count from '.Act_Detail.' where '.$activity_where.' order by start_timestamp asc';
        $activity_query = $this->getDataBySql($dealerDB,1,$activity_sql);
        if(DB_CONST::DATA_NONEXISTENT == $activity_query)
        {
            return array('result'=>OPT_CONST::FAILED,'data'=>$result,'result_message'=>"活动不存在");
        }
        
        $activity_id = $activity_query['activity_id'];
        $start_timestamp = $activity_query['start_timestamp'];
        $end_timestamp = $activity_query['end_timestamp'];
        $refresh_timestamp = $activity_query['refresh_timestamp'];
        $type = $activity_query['type'];
        $content = $activity_query['content'];
        $ticket_count = $activity_query['ticket_count'];
        
        if($timestamp < $start_timestamp)
        {
            return array('result'=>OPT_CONST::FAILED,'data'=>$result,'result_message'=>"活动尚未开始");
        }
        if($timestamp >= $end_timestamp)
        {
            return array('result'=>OPT_CONST::FAILED,'data'=>$result,'result_message'=>"活动已结束");
        }
        
        $partake_timestamp = $this->getPartakeTimestamp($type,$refresh_timestamp);
        
        //判断是否已参加该活动
        $partake_where = 'account_id='.$account_id.' and activity_id='.$activity_id.' and partake_timestamp='.$partake_timestamp.' and is_delete=0';
        $partake_sql = 'select partake_id from '.Act_Partake.' where '.$partake_where.'';
        $partake_query = $this->getDataBySql($dealerDB,1,$partake_sql);
        if(DB_CONST::DATA_NONEXISTENT != $partake_query)
        {
            return array('result'=>OPT_CONST::FAILED,'data'=>$result,'result_message'=>"你已参与该活动");
        }
        
        $array['create_time'] = $timestamp;
        $array['create_appid'] = $timestamp;
        $array['update_time'] = $timestamp;
        $array['update_appid'] = $timestamp;
        $array['is_delete'] = 0;
        $array['account_id'] = $account_id;
        $array['activity_id'] = $activity_id;
        $array['ticket_count'] = $ticket_count;
        $array['partake_timestamp'] = $partake_timestamp;
        
        $partake_id = $this->getInsertID($dealerDB,Act_Partake, $array);
        
        
        $update_where = 'account_id='.$account_id;
        $update_str = 'update_time='.$timestamp.',update_appid="aid_'.$account_id.'",ticket_count=ticket_count+'.$ticket_count;
        $update_query = $this->changeNodeValue($dealerDB,Room_Ticket,$update_str,$update_where);
        //房卡流水账
        $journal_ary['journal_type'] = Game_CONST::JournalType_Income;
        $journal_ary['account_id'] = $account_id;
        $journal_ary['object_type'] = Game_CONST::ObjectType_Newuser;
        $journal_ary['object_id'] = $partake_id;
        $journal_ary['ticket_count'] = $ticket_count;
        $journal_ary['extra'] = "";
        $this->updateRoomTicketJournal($journal_ary,$dealerDB);
        
        
        return array('result'=>OPT_CONST::SUCCESS,'data'=>$result,'result_message'=>"领取活动奖励");
    }


    public function getBroadcastInfo($type){
        $info = $this->db()
            ->where('type',$type)
            ->where('state',1)
            ->get('game_broadcast')
            ->row();

        return empty($info) ? '' : $info->content;
    }
    
    
}