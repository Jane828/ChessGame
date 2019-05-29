<?php

include_once dirname(__DIR__).'/public_models.php';
class Detail_Model extends Public_Models
{

	public function __construct()
	{
		header('Content-Type: text/html; charset=utf-8'); 
		parent::__construct();
	}

    /**
     * 获取用户房间记录
     * @param array $where
     * @param int $cur
     * @param int $page
     * @return array
     */
	public function getAccountRoomRecord($where = [], $cur = 1 , $page = 20){
	    if(empty($where)){
	        return [];
        }
        $table = 'room_account';

	    $cur = $cur > 0 ? $cur : 1;
	    $cur = $cur - 1;

        $total_num = $this->db()
            ->where($where)
            ->count_all_results($table);

        $total_page = ceil($total_num/$page);

        $offset = $cur * $page;

	    $query = $this->db()
            ->where($where)
            ->limit($page, $offset)
            ->order_by('over_time','desc')
            ->get($table);

	    $result = [];
	    $result['data'] = $query->result();
	    $result['total_num'] = $total_num;
	    $result['total_page'] = $total_page;
	    $result['curpage']  = $cur+1;

	    return $result;
    }

    public function getOpenRoomRecord($table, $where = [], $cur = 1 , $page = 20){
        if(empty($where)){
            return [];
        }

        $cur = $cur > 0 ? $cur : 1;
        $cur = $cur - 1;

        $total_num = $this->db()
            ->where($where)
            ->count_all_results($table);

        $total_page = ceil($total_num/$page);

        $offset = $cur * $page;

        $query = $this->db()
            ->where($where)
            ->limit($page, $offset)
            ->order_by('create_time','desc')
            ->get($table);

        $result = [];
        $result['data'] = $query->result();
        $result['total_num'] = $total_num;
        $result['total_page'] = $total_page;
        $result['curpage']  = $cur+1;

        return $result;
    }


    /**
     * 获取房间编码
     * @param $room_id
     * @param $table
     * @return string
     */
    public function getRoomNumberByRoomId($room_id, $table){
	    $room_info = $this->db()
            ->where('room_id',$room_id)
            ->get($table)->row();
	    if(empty($room_info)){
	        return "0";
        }
        return $room_info->room_number;
    }

    /**
     * 根据房间编码获取房间ID
     * @param $room_number
     * @param $table
     * @return string
     */
    public function getRoomIdByRoomNumber($room_number, $table){
        $room_info = $this->db()->where('room_number',$room_number)->get($table)->row();
        if(empty($room_info)){
            return "0";
        }
        return $room_info->room_id;
    }

    /**
     * 根据房间id获取房间积分榜
     * @param $room_id
     * @return mixed
     */
    public function getRoomBoardById($room_id,$type){
        $board_info = $this->db()
            ->where('room_id',$room_id)
            ->where('game_type',$type)
            ->where('is_delete',0)
            ->get('room_scoreboard')->row();
        if(empty($board_info)){
            return $board_info;
        }

        //get the the owner of room
        $room_info = $this->db()
            ->where('room_id',$room_id)
            ->get('room_account')->row();
        $board_info->board = json_decode($board_info->board,true);
        $board_info->balance_board = json_decode($board_info->balance_board,true);
        $board_info->room_own = $room_info->account_id;
        return $board_info;
    }

    public function getRoomDetail($room_id,$type){
        $detail_list = $this->db()
            ->where('room_id',$room_id)
            ->where('game_type',$type)
            ->get('room_game_result')->result();
        return $detail_list;
    }

    /**
     * 用户游戏积分统计
     *
     * @param $game_type
     * @param $account_id
     */
    public function scoreStat($game_type, $account_id)
    {
        $data = [];

        $game = $this->db()->where('game_type', $game_type)->select('game_title')->from(Game_List)->get()->row();
        if (empty($game)) {
            return ['result'=>OPT_CONST::FAILED, 'data'=>$data, 'result_message'=>"游戏类型错误"];
        }

        $data = [
            'title' => sprintf('您的%s战绩', $game->game_title)
        ];

        // 最近一天，即今天
        $data['one'] = $this->periodScore($game_type, $account_id, strtotime('today'));

        $dt = new DateTime();

        // 最近三天
        $d = $dt->sub(new DateInterval('P2D'));
        $t = strtotime($d->format('Y-m-d'));
        $data['three'] = $this->periodScore($game_type, $account_id, $t);

        // 最近一周
        $d = $dt->sub(new DateInterval('P6D'));
        $t = strtotime($d->format('Y-m-d'));
        $data['week'] = $this->periodScore($game_type, $account_id, $t);

        // 最近一月
        $d = $dt->sub(new DateInterval('P1M'));
        $t = strtotime($d->format('Y-m-d'));
        $data['month'] = $this->periodScore($game_type, $account_id, $t);

        return ['result'=>OPT_CONST::SUCCESS, 'data'=>$data, 'msg'=>$data['title']];
    }

    /**
     * 用户玩某个游戏指定时间段游戏得分
     *
     * @param int $type 游戏类型
     * @param int $uid  用户ID
     * @param int $time 时间段起点
     * @return int
     */
    private function periodScore($type, $uid, $time)
    {
        $score = 0;
        $row = $this->db()
            ->where('game_type', $type)
            ->where('account_id', $uid)
            ->where('over_time >=', $time)
            ->where('over_time <', time())
            ->select_sum('score')
            ->get(Room_Account)
            ->row();
        if (!empty($row)) {
            $score = $row->score;
        }
        return (int)$score;
    }

    /**
     * 玩家某游戏总分
     *
     * @param int $type 游戏类型
     * @param int $aid  玩家ID
     */
    public function getTotalScoreByType($type, $aid)
    {
        $time = time();
        $total_score = 0;
        $total_count = 0;

        // 查询该游戏历史总分，无则添加记录
        $summary = $this->db()
            ->where('game_type', $type)
            ->where('account_id', $aid)
            ->select('data_id, total_score, total_count')
            ->get(Summary_Account)
            ->row();

        if (empty($summary)) {
            $saData = [
                'create_time' => $time,
                'create_appid'=> '',
                'update_time' => $time,
                'update_appid'=> '',
                'is_delete'   => 0,
                'account_id'  => $aid,
                'game_type'   => $type,
                'total_score' => 0,
                'total_count' => 0,
            ];
            $this->db()->insert(Summary_Account, $saData);
        } else {
            $total_score = $summary->total_score;
            $total_count = $summary->total_count;
        }

        // 查询有无 未统计的数据
        $row = $this->db()
            ->where('account_id', $aid)
            ->where('game_type', $type)
            ->where('is_stat', 0)
            ->select('data_id')
            ->order_by('data_id', 'desc')
            ->get(Room_Account)
            ->row();

        if (empty($row)) {
            return $total_score;
        }

        // 有，则如下处理
        $mapRA = [
            'account_id ='  => $aid,
            'game_type ='   => $type,
            'is_stat ='     => 0,
            'data_id <='    => $row->data_id
        ];

        // 查询未统计的游戏总分及游戏局数
        $pendingScore = $this->db()
            ->where($mapRA)
            ->select('sum(score) as game_score, count(data_id) as game_count')
            ->get(Room_Account)
            ->row();

        // 该游戏 新的总分
        $total_score += $pendingScore->game_score;
        $total_count += $pendingScore->game_count;

        // summary_account 总分更新
        $this->db()
            ->where('account_id', $aid)
            ->where('game_type', $type)
            ->update(Summary_Account, [
                'total_score' => $total_score,
                'total_count' => $total_count,
                'update_time' => $time
            ]);

        $this->db()->where($mapRA)->update(Room_Account, ['is_stat'=>1]);

        return $total_score;
    }

    public function getRoomInfo($game_type, $room_number)
    {
        $result = [];
        switch($game_type)
        {
            case 1:
            case 110:
            case 111:
            case 92:
                $room_table = "flower_room";
                break;
            case 5:
            case 9:
            case 12:
            case 71:
            case 91:
                $room_table = "bull_room";
                break;
            case 36:
            case 37:
            case 38:
                $room_table = "sangong_room";
                break;
            case 61:
            case 62:
            case 63:
                $room_table = "dark_room";
                break;
            default:
                return $result;
        }
        return $this->db()->where('room_number', $room_number)->limit(1)->get($room_table)->row_array();
    }
}