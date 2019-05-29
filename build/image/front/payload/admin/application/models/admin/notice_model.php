<?php

include_once __DIR__.'/common_model.php';		//加载数据库操作类

class Notice_Model extends Admin_Common_Model
{
	public function __construct()
	{
		header('Content-Type: text/html; charset=utf-8'); 
		parent::__construct();
	}

	public function getBroadcastList()
    {
        // 查询是否有新游戏未写入公告表，有则新增
        $rows = $this->db()->from(Game_List . ' as l')
            ->join(Game_Broadcast . ' as b', 'b.type = l.game_type', 'left')
            ->where('b.type is null')
            ->select('l.game_type, l.game_title')
            ->get()
            ->result();
        if (count($rows)) {
            $data = [];
            foreach ($rows as $game) {
                $data[] = [
                    'type' => $game->game_type,
                    'introl' => $game->game_title,
                    'state' => 2,
                    'content' => ''
                ];
            }
            $this->db()->insert_batch(Game_Broadcast, $data);
        }

        $list = $this->db()->get('game_broadcast')->result();

        return $list;
    }

    public function getMaintainList()
    {
	    $list = $this->db()
            ->from(Game_List . ' as l')
            ->join(Game_Announcement . ' as a', 'a.game_type = l.game_type', 'left')
            ->where('l.is_delete', 0)
            ->select('l.game_type, l.game_title, a.is_delete, a.service_text')
            ->get()
            ->result();
	    return $list;
    }

    public function editBroadcast($id,$data){
        return $this->db()
            ->where('broadcast_id',$id)
            ->update('game_broadcast',$data);
    }

    public function editMaintain($game_type, $param){
	    $time = time();
        $row = $this->db()->where('game_type', $game_type)->get(Game_Announcement)->row();
        $data['update_time'] = $time;
        $data['update_appid'] = 0;
        if (isset($param['service_text'])) {
            $data['service_text'] = $param['service_text'];
        }
        if (isset($param['is_delete'])) {
            $data['is_delete'] = 1 == $param['is_delete'] ? 1 : 0; // 等于1表示要关闭
        }
        if (empty($row)) {
            $data['game_type'] = $game_type;
            $data['create_time'] = $time;
            $data['announce_time'] = 0;
            $data['service_time'] = 0;
            $data['end_time'] = 2147483647;
            $data['create_appid'] = 0;
            $data['announce_text'] = '';
            return $this->db()->insert(Game_Announcement, $data);
        }
        return $this->db()->where('game_type', $game_type)->update(Game_Announcement, $data);
    }

    public function getGameList()
    {
        return $this->db()->select('game_type, game_title')->get(Game_List)->result();
    }
}