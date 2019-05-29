<?php

include_once __DIR__.'/common_model.php';		//加载数据库操作类
class Account_Model extends Game_Common_Model
{
	public function __construct()
	{
		header('Content-Type: text/html; charset=utf-8'); 
		parent::__construct();
	}
	



	/*
		获取活跃用户数量
	*/
	public function getActiveCount($arrData)
	{
		$result = array();
		$timestamp = time();

		if(!isset($arrData['dealer_num']) || $arrData['dealer_num'] === "" )
		{
			log_message('error', "function(getPlayCount):lack of dealer_num"." in file".__FILE__." on Line ".__LINE__);
			return $this->missingPrameterArr("dealer_num");
		}

		$result['total_count'] = 0;
		$result['day_count'] = 0;
		$result['week_count'] = 0;
		$result['month_count'] = 0;

		if($arrData['dealer_num'] == "-1")
		{
			return array('result'=>OPT_CONST::SUCCESS,'data'=>$result,'result_message'=>"获取活跃用户数量");
		}
		$dealer_num = $arrData['dealer_num'];
		$DelaerConst = "Dealer_".$dealer_num;
		if($dealer_num == "-1" || !class_exists($DelaerConst))
		{
			return array('result'=>OPT_CONST::SUCCESS,'data'=>$result,'result_message'=>"获取活跃用户数量");
		}
        $dealerDB = $DelaerConst::DBConst_Name;	

		//获取总用户
		$total_sql = 'select count(account_id) as count from '.WX_Account.' where is_delete=0';
		$total_query = $this->getDataBySql($dealerDB,1,$total_sql);
		if($total_query != DB_CONST::DATA_NONEXISTENT)
		{
			$result['total_count'] = $total_query['count'];
		}

		//获取日活数
		//$from_timestamp = $timestamp - (86400 * 1);
		$from_timestamp = strtotime(date("Y-m-d",time()));
		$day_sql = 'select count(account_id) as count from '.WX_Account.' where update_time>='.$from_timestamp.' and is_delete=0';
		$day_query = $this->getDataBySql($dealerDB,1,$day_sql);
		if($day_query != DB_CONST::DATA_NONEXISTENT)
		{
			$result['day_count'] = $day_query['count'];
		}

		//获取周活数
		$from_timestamp = $timestamp - (86400 * 7);
		$week_sql = 'select count(account_id) as count from '.WX_Account.' where update_time>='.$from_timestamp.' and is_delete=0';
		$week_query = $this->getDataBySql($dealerDB,1,$week_sql);
		if($week_query != DB_CONST::DATA_NONEXISTENT)
		{
			$result['week_count'] = $week_query['count'];
		}

		//获取月活数
		$from_timestamp = $timestamp - (86400 * 30);
		$month_sql = 'select count(account_id) as count from '.WX_Account.' where update_time>='.$from_timestamp.' and is_delete=0';
		$month_query = $this->getDataBySql($dealerDB,1,$month_sql);
		if($month_query != DB_CONST::DATA_NONEXISTENT)
		{
			$result['month_count'] = $month_query['count'];
		}

		return array('result'=>OPT_CONST::SUCCESS,'data'=>$result,'result_message'=>"获取活跃用户数量");
	}


	/*
		获取用户明细
	*/
	public function getAccountListBak($arrData)
	{
		$result = array();
		$timestamp = time();

		if(!isset($arrData['dealer_num']) || $arrData['dealer_num'] === "" )
		{
			log_message('error', "function(getAccountList):lack of dealer_num"." in file".__FILE__." on Line ".__LINE__);
			return $this->missingPrameterArr("dealer_num");
		}
		if(!isset($arrData['page']))
		{
			log_message('error', "function(getAccountList):lack of page"." in file".__FILE__." on Line ".__LINE__);
			return $this->missingPrameterArr("page");
		}
		if(!isset($arrData['keyword']))
		{
			log_message('error', "function(getAccountList):lack of keyword"." in file".__FILE__." on Line ".__LINE__);
			return $this->missingPrameterArr("keyword");
		}
		if(!isset($arrData['uid']))
		{
			log_message('error', "function(getAccountList):lack of keyword"." in file".__FILE__." on Line ".__LINE__);
			return $this->missingPrameterArr("uid");
		}

		if($arrData['dealer_num'] == "-1")
		{
			return array('result'=>OPT_CONST::SUCCESS,'data'=>$result,'result_message'=>"获取用户明细",'sum_page'=>1);
		}
		$dealer_num = $arrData['dealer_num'];
		$DelaerConst = "Dealer_".$dealer_num;
		if($dealer_num == "-1" || !class_exists($DelaerConst))
		{
			return array('result'=>OPT_CONST::SUCCESS,'data'=>$result,'result_message'=>"获取用户明细",'sum_page'=>1);
		}
        $dealerDB = $DelaerConst::DBConst_Name;	

		$keyword = $arrData['keyword'];
		$page = $arrData['page'];
		$uid = $arrData['uid'];
		if($page == 0 || $page == "")
		{
			$page = 1;
		}

		$limit = 10;
		$offset = ($page - 1) * $limit;
		$sum_page = 1;


		$game_list_str = "";
		$score_list = array();
		$account_score_array = array();
		$list_sql = 'select game_type,game_title from '.Game_List.' where is_delete=0 order by game_type asc';
		$list_query = $this->getDataBySql($dealerDB,0,$list_sql);
		if($list_query != DB_CONST::DATA_NONEXISTENT)
		{
			foreach($list_query as $list_item)
			{
				$game_type = $list_item['game_type'];
				$game_list_str .= $game_type.",";
				$score_list[$game_type] = 0;

				$score_ary = array("score"=>0,"game_type"=>$game_type,"game_title"=>$list_item['game_title']);
				$account_score_array[] = $score_ary;
				unset($score_ary);
			}
		}
		if($game_list_str == "")
		{
			$game_list_str = "-1";
		}
		else
		{
			$game_list_str = substr($game_list_str,0,strlen($game_list_str)-1); 
		}

		$account_where = 'is_delete=0';
		if($keyword != "")
		{
			$account_where .= ' and nickname like "%'.$keyword.'%"';
		}
		if ($uid != "") {
		    $account_where .= ' and user_code = '.$uid;
        }
		
		$account_sql = 'select account_id,nickname,user_code from '.WX_Account.' where '.$account_where.' order by create_time desc limit '.$offset.','.$limit;
		$account_query = $this->getDataBySql($dealerDB,0,$account_sql);
		if($account_query != DB_CONST::DATA_NONEXISTENT)
		{
			$count_sql = 'select count(account_id) as count from '.WX_Account.' where '.$account_where;
			$count_query = $this->getDataBySql($dealerDB,1,$count_sql);
			$sum_page = ceil($count_query['count'] / $limit);

			foreach($account_query as $account_item)
			{
				$account_id = $account_item['account_id'];
				$user_code = $account_item['user_code'];
				$nickname = $account_item['nickname'];

				$ticket_count = 0;
				//获取房卡数
				$ticket_sql = 'select ticket_count from '.Room_Ticket.' where account_id='.$account_id;
				$ticket_query = $this->getDataBySql($dealerDB,1,$ticket_sql);
				if($ticket_query != DB_CONST::DATA_NONEXISTENT)
				{
					$ticket_count = $ticket_query['ticket_count'];
				}

				$sum_score = 0;
				$score_sql = 'select total_score,game_type from '.Summary_Account.' where account_id='.$account_id.' and game_type in('.$game_list_str.') and is_delete=0';
				$score_query = $this->getDataBySql($dealerDB,0,$score_sql);
				if($score_query != DB_CONST::DATA_NONEXISTENT)
				{
					foreach($score_query as $score_item)
					{
						$g_type = $score_item['game_type'];
						
						$score_list[$g_type] += $score_item["total_score"];
						$sum_score += $score_item["total_score"];
					}
				}


				// if($DelaerConst::Is_GameScore == 1)
				// {
				// 	$score_sql = 'select * from (select data_id,game_type,total_score from '.Room_GameScore.' where account_id='.$account_id.' and game_type in('.$game_list_str.') and is_delete=0 order by data_id desc)a group by game_type';
				// 	$score_query = $this->getDataBySql($dealerDB,0,$score_sql);
				// 	if($score_query != DB_CONST::DATA_NONEXISTENT)
				// 	{
				// 		foreach($score_query as $score_item)
				// 		{
				// 			$g_type = $score_item['game_type'];
							
				// 			$score_list[$g_type] += $score_item["total_score"];
				// 			$sum_score += $score_item["total_score"];
				// 		}
				// 	}
				// }
				// else
				// {
				// 	//游戏类型 ：1炸金花  2斗地主  3梭哈  4德州  5斗牛 6广东麻将
				// 	$score_sql = 'select game_type,board from '.Room_Scoreboard.' where game_type in('.$game_list_str.') and board like "%\"'.$account_id.'\":%" and is_delete=0';
				// 	$score_query = $this->getDataBySql($dealerDB,0,$score_sql);
				// 	if($score_query != DB_CONST::DATA_NONEXISTENT)
				// 	{
				// 		foreach($score_query as $score_item)
				// 		{
				// 			$g_type = $score_item['game_type'];
							
				// 			$board_ary = json_decode($score_item['board'],true);
				// 			if(is_array($board_ary) && isset($board_ary[$account_id]))
				// 			{
				// 				$score_list[$g_type] += $board_ary[$account_id];
				// 				$sum_score += $board_ary[$account_id];
				// 			}
				// 		}
				// 	}
				// }

				foreach($account_score_array as $key=>$account_score_item)
				{
					$account_score_array[$key]['score'] = $score_list[$account_score_item['game_type']];
					$score_list[$account_score_item['game_type']] = 0;
				}

				$array['account_id'] = $account_id;
				$array['user_code'] = $user_code;
				$array['nickname'] = $nickname;
				$array['sum_score'] = $sum_score;
				$array['ticket_count'] = $ticket_count;
				$array['score_array'] = $account_score_array;

				$result[] = $array;

				unset($array);
			}
		}
		return array('result'=>OPT_CONST::SUCCESS,'data'=>$result,'result_message'=>"获取用户明细",'sum_page'=>$sum_page);
	}

    public function getAccountList($param)
    {
        $page = isset($param['page']) ? $param['page'] : 1;
        $keyword = isset($param['keyword']) ? $param['keyword'] : '';
        $uid = isset($param['uid']) ? $param['uid'] : 0; // 对应 wechat_account.user_code

        $where = 'w.is_delete = 0';
        if (!empty($keyword)) {
            $where .= ' and w.nickname like "%'.$keyword.'%"';
        }
        if (0 < $uid) {
            $where .= ' and w.user_code = '.$uid;
        }

        $limit = 10;

        $count = $this->db()->from(WX_Account.' as w')
            ->join(Room_Ticket.' as t', 't.account_id = w.account_id', 'left')
            ->where($where)
            ->count_all_results();
        $sum_page = ceil($count/$limit);

        $accounts = $this->db()->from(WX_Account.' as w')
            ->join(Room_Ticket.' as t', 't.account_id = w.account_id', 'left')
            ->where($where)
            ->order_by('w.account_id', 'desc')
            ->select('w.account_id, w.nickname, w.user_code, t.ticket_count')
            ->limit($limit, $limit*($page-1))
            ->get()
            ->result_array();

        foreach ($accounts as $k => $account) {
            $accounts[$k]['sum_score'] = $this->getSumScore($account['account_id']);
        }

        return array('result'=>OPT_CONST::SUCCESS,'data'=>$accounts,'result_message'=>"获取用户明细",'sum_page'=>$sum_page);
    }

    public function getSumScore($aid = 0)
    {
        if (empty($aid)) {
            return 0;
        }

        // 查询有无未统计的游戏
        $row = $this->db()
            ->where('account_id', $aid)
            ->where('is_stat', 0)
            ->select('data_id')
            ->order_by('data_id', 'desc')
            ->get(Room_Account)
            ->row();

        // 没有，则直接查询总分和
        if (empty($row)) {
            $row = $this->db()->where('account_id', $aid)->select_sum('total_score')->get(Summary_Account)->row();
            return $row->total_score ? : 0;
        }

        // 有，则如下处理
        $mapRA = [
            'account_id ='  => $aid,
            'is_stat ='     => 0,
            'data_id <='    => $row->data_id
        ];

        // 查询未统计的各游戏总分及游戏局数
        $pendingScores = $this->db()
            ->where($mapRA)
            ->group_by('game_type')
            ->select('game_type, sum(score) as game_score, count(data_id) as game_count')
            ->get(Room_Account)
            ->result();

        // 所有游戏总分
        $sum_score = 0;

        foreach ($pendingScores as $pendingScore) {
            // 累加 未统计的总分
            $sum_score += $pendingScore->game_score;

            // 查询该游戏有无历史总分，有则更新，无则添加
            $summary = $this->db()
                ->where('game_type', $pendingScore->game_type)
                ->where('account_id', $aid)
                ->select('data_id, total_score, total_count')
                ->get(Summary_Account)
                ->row();

            $time = time();

            if (empty($summary)) {
                $saData = [
                    'create_time' => $time,
                    'create_appid'=> '',
                    'update_time' => $time,
                    'update_appid'=> '',
                    'is_delete'   => 0,
                    'account_id'  => $aid,
                    'game_type'   => $pendingScore->game_type,
                    'total_score' => $pendingScore->game_score,
                    'total_count' => $pendingScore->game_count,
                ];
                // 该游戏 总分保存
                $this->db()->insert(Summary_Account, $saData);
            }
            else {
                // 累加 历史总分
                $sum_score += $summary->total_score;

                // 该游戏 新的总分
                $new_total_score = $pendingScore->game_score + $summary->total_score;
                $new_total_count = $pendingScore->game_count + $summary->total_count;

                // summary_account 总分更新
                $this->db()
                    ->where('data_id', $summary->data_id)
                    ->update(Summary_Account, [
                        'total_score' => $new_total_score,
                        'total_count' => $new_total_count,
                        'update_time' => $time
                    ]);
            }
        }

        // room_account 更新为已统计
        $this->db()->where($mapRA)->update(Room_Account, ['is_stat'=>1]);

        return $sum_score;
    }


	 //统计获取用户该游戏总积分
	public function statisticsAccountGameSumScore1($arrData)
	{
		$result = array();
		$timestamp = time();

		if(!isset($arrData['dealer_num']) || $arrData['dealer_num'] === "" )
		{
			log_message('error', "function(getAccountList):lack of dealer_num"." in file".__FILE__." on Line ".__LINE__);
			return $this->missingPrameterArr("dealer_num");
		}

		if($arrData['dealer_num'] == "-1")
		{
			return array('result'=>OPT_CONST::SUCCESS,'data'=>$result,'result_message'=>"统计获取用户该游戏总积分");
		}
		$dealer_num = $arrData['dealer_num'];
		$DelaerConst = "Dealer_".$dealer_num;
		if($dealer_num == "-1" || !class_exists($DelaerConst))
		{
			return array('result'=>OPT_CONST::SUCCESS,'data'=>$result,'result_message'=>"统计获取用户该游戏总积分");
		}
        $dealerDB = $DelaerConst::DBConst_Name;	

		$game_list_str = "";
		$score_list = array();
		$account_score_array = array();
		$list_sql = 'select game_type,game_title from '.Game_List.' where is_delete=0 order by game_type asc';
		$list_query = $this->getDataBySql($dealerDB,0,$list_sql);
		if($list_query != DB_CONST::DATA_NONEXISTENT)
		{
			foreach($list_query as $list_item)
			{
				$game_type = $list_item['game_type'];
				$game_list_str .= $game_type.",";
				$score_list[$game_type] = 0;

				$score_ary = array("score"=>0,"game_type"=>$game_type,"game_title"=>$list_item['game_title']);
				$account_score_array[] = $score_ary;
				unset($score_ary);
			}
		}
		if($game_list_str == "")
		{
			$game_list_str = "-1";
		}
		else
		{
			$game_list_str = substr($game_list_str,0,strlen($game_list_str)-1); 
		}

		$account_where = 'is_delete=0';
		$account_sql = 'select account_id,nickname from '.WX_Account.' where '.$account_where.' order by create_time desc';
		$account_query = $this->getDataBySql($dealerDB,0,$account_sql);
		if($account_query != DB_CONST::DATA_NONEXISTENT)
		{
			foreach($account_query as $account_item)
			{
				$account_id = $account_item['account_id'];

				$sum_score = 0;
				//游戏类型 ：1炸金花  2斗地主  3梭哈  4德州  5斗牛 6广东麻将  
				$score_sql = 'select game_type,board from '.Room_Scoreboard.' where game_type in('.$game_list_str.') and board like "%\"'.$account_id.'\":%" and is_delete=0';
				$score_query = $this->getDataBySql($dealerDB,0,$score_sql);
				if($score_query != DB_CONST::DATA_NONEXISTENT)
				{
					foreach($score_query as $score_item)
					{
						$g_type = $score_item['game_type'];
						
						$board_ary = json_decode($score_item['board'],true);
						if(is_array($board_ary) && isset($board_ary[$account_id]))
						{
							$score_list[$g_type] += $board_ary[$account_id];
							$sum_score += $board_ary[$account_id];
						}
					}
				}

				foreach($score_list as $type=>$score)
				{
					//判断是否存在记录
					$data_sql = 'select data_id from '.Room_GameScore.' where account_id='.$account_id.' and game_type='.$type.' and is_delete=0 order by create_time desc limit 1';
					$data_query = $this->getDataBySql($dealerDB,1,$data_sql);
					if($data_query != DB_CONST::DATA_NONEXISTENT)
					{
						echo "用户".$account_id." gametype:".$type." 数据已存在";
						echo "<br>";
						continue;
					}
					else
					{
						$array['create_time'] = $timestamp;
						$array['game_time'] = $timestamp;
						$array['game_type'] = $type;
						$array['room_id'] = -1;
						$array['round'] = -1;
						$array['account_id'] = $account_id;
						$array['score'] = 0;
						$array['total_score'] = $score;
						$dealer_id = $this->getInsertID($dealerDB,Room_GameScore,$array);
						unset($array);
						echo "用户".$account_id." gametype:".$type." 总积分:".$score;
						echo "<br>";
					}
				}
			}
		}
		return array('result'=>OPT_CONST::SUCCESS,'data'=>$result,'result_message'=>"统计获取用户该游戏总积分");
	}



	//统计获取用户该游戏总积分
	public function statisticsAccountGameSumScore($arrData)
	{
		$result = array();
		$timestamp = time();

		if(!isset($arrData['dealer_num']) || $arrData['dealer_num'] === "" )
		{
			log_message('error', "function(getAccountList):lack of dealer_num"." in file".__FILE__." on Line ".__LINE__);
			return $this->missingPrameterArr("dealer_num");
		}
		if(!isset($arrData['page']) || $arrData['page'] === "" )
		{
			log_message('error', "function(getAccountList):lack of page"." in file".__FILE__." on Line ".__LINE__);
			return $this->missingPrameterArr("page");
		}

		if($arrData['dealer_num'] == "-1")
		{
			return array('result'=>OPT_CONST::SUCCESS,'data'=>$result,'result_message'=>"统计获取用户该游戏总积分");
		}
		$dealer_num = $arrData['dealer_num'];
		$DelaerConst = "Dealer_".$dealer_num;
		if($dealer_num == "-1" || !class_exists($DelaerConst))
		{
			return array('result'=>OPT_CONST::SUCCESS,'data'=>$result,'result_message'=>"统计获取用户该游戏总积分");
		}
        $dealerDB = $DelaerConst::DBConst_Name;	

		$page = $arrData['page'];
		$limit = 2000;
		$offset = ($page -1) * $limit;
		
		
		//游戏类型 ：1炸金花  2斗地主  3梭哈  4德州  5斗牛 6广东麻将  
		$score_sql = 'select room_id,round,game_type,board,create_time from '.Room_Scoreboard.' where is_delete=0 order by board_id asc limit '.$offset.','.$limit;
		$score_query = $this->getDataBySql($dealerDB,0,$score_sql);
		if($score_query != DB_CONST::DATA_NONEXISTENT)
		{
			foreach($score_query as $score_item)
			{
				$room_id = $score_item['room_id'];
				$round = $score_item['round'];
				$game_type = $score_item['game_type'];
				$create_time = $score_item['create_time'];
				$board_ary = json_decode($score_item['board'],true);
				if(is_array($board_ary))
				{
					foreach($board_ary as $account_id=>$score)
					{
						$total_score = 0;
						//判断是否存在记录
						$data_sql = 'select total_score from '.Room_GameScore.' where account_id='.$account_id.' and game_type='.$game_type.' and is_delete=0 order by game_time desc limit 1';
						$data_query = $this->getDataBySql($dealerDB,1,$data_sql);
						if($data_query != DB_CONST::DATA_NONEXISTENT)
						{
							$total_score = 	$data_query['total_score'];
						}

						$total_score += $score;

						$array['create_time'] = $timestamp;
						$array['game_time'] = $create_time;
						$array['game_type'] = $game_type;
						$array['room_id'] = $room_id;
						$array['round'] = $round;
						$array['account_id'] = $account_id;
						$array['score'] = $score;
						$array['total_score'] = $total_score;
						$dealer_id = $this->getInsertID($dealerDB,Room_GameScore,$array);
						unset($array);
					}
				}
			}
		}
		return array('result'=>OPT_CONST::SUCCESS,'data'=>$result,'result_message'=>"统计获取用户该游戏总积分");
	}



	//获取用户输赢明细
	public function getAccountGameScoreDetail($arrData)
	{
		$result = array();
		$timestamp = time();

		if(!isset($arrData['dealer_num']) || $arrData['dealer_num'] === "" )
		{
			log_message('error', "function(getAccountList):lack of dealer_num"." in file".__FILE__." on Line ".__LINE__);
			return $this->missingPrameterArr("dealer_num");
		}
		if(!isset($arrData['page']) || $arrData['page'] === "" )
		{
			log_message('error', "function(getAccountList):lack of page"." in file".__FILE__." on Line ".__LINE__);
			return $this->missingPrameterArr("page");
		}

		

	}

	//根据id查询昵称
	public function getNameById($arrData)
	{
		$result = array();
		$timestamp = time();

		if(!isset($arrData['dealer_num']) || $arrData['dealer_num'] === "" )
		{
			log_message('error', "function(getNameById):lack of dealer_num"." in file".__FILE__." on Line ".__LINE__);
			return $this->missingPrameterArr("dealer_num");
		}
		if(!isset($arrData['account_id']) || $arrData['account_id'] === "" )
		{
			log_message('error', "function(getNameById):lack of account_id"." in file".__FILE__." on Line ".__LINE__);
			return $this->missingPrameterArr("account_id");
		}

		if($arrData['dealer_num'] == "-1")
		{
			return array('result'=>OPT_CONST::SUCCESS,'data'=>$result,'result_message'=>"根据id查询昵称");
		}
		$dealer_num = $arrData['dealer_num'];
		$account_id = $arrData['account_id'];
		$DelaerConst = "Dealer_".$dealer_num;
		if($dealer_num == "-1" || !class_exists($DelaerConst))
		{
			return array('result'=>OPT_CONST::SUCCESS,'data'=>$result,'result_message'=>"根据id查询昵称");
		}
        $dealerDB = $DelaerConst::DBConst_Name;	
        $account_where = 'account_id="'.$account_id.'"';
        $account_sql = 'select account_id,nickname,headimgurl from '.WX_Account.' where '.$account_where;
        $account_query = $this->getDataBySql($dealerDB,1,$account_sql);
        if($account_query != DB_CONST::DATA_NONEXISTENT)
        {
        	$result['account_id'] = $account_query['account_id'];
        	$result['nickname'] = $account_query['nickname'];
        	$result['headimgurl'] = $account_query['headimgurl'];
        	return array('result'=>OPT_CONST::SUCCESS,'data'=>$result,'result_message'=>"根据id查询昵称");
        }
        else
        {
        	return array('result'=>-1,'data'=>$result,'result_message'=>"用户不存在");
        }
	}

    public function handleScoreBoard()
    {
        set_time_limit(0);

        $t0 = microtime(true);

        $db = $this->load->database(Dealer_2::DBConst_Name, true);

        $db->where('is_stat', 0)->order_by('board_id', 'ASC')->get(Room_Scoreboard);

        $query = $db->select('board_id, game_type, room_id, board, create_time')
            ->where('is_stat', 0)
            ->order_by('board_id', 'ASC')
            ->get(Room_Scoreboard, 500);

        $num = $query->num_rows;

        if (0 == $num) {
            $t1 = microtime(true);
            echo json_encode(['num'=>$num, 'time'=>$t1-$t0]);
            return;
        }

        $minId = 0;
        $maxId = 0;
        $items = [];

        foreach ($query->result() as $k => $row) {
            if (0 == $k) {
                $minId = $row->board_id;
            }
            $maxId = $row->board_id;
            $board = json_decode($row->board, true);
            if (empty($board)) {
                continue;
            }
            foreach ($board as $account_id => $score) {
                $item = [
                    'room_id'    => $row->room_id,
                    'game_type'  => $row->game_type,
                    'account_id' => $account_id,
                ];
                $ra = $db->where($item)->get(Room_Account);
                if (0 == $ra->num_rows) {
                    $item['is_stat'] = 0;
                    $item['score'] = $score;
                    $item['over_time'] = $row->create_time;
                    $items[] = $item;
                }
            }
        }
        $query->free_result();

        if (! empty($items)) {
            $db->insert_batch(Room_Account, $items);
        }

        $db->where('is_stat', 0)
            ->where('board_id >=', $minId)
            ->where('board_id <=', $maxId)
            ->update(Room_Scoreboard, [
                'is_stat'=>1
            ]);

        $t1 = microtime(true);
        echo json_encode(['num'=>$num, 'time'=>$t1-$t0]);
    }

    public function getGameScoreStat($aid = 0, $from = '', $to = '')
    {
        if (empty($aid)) {
            return ['result'=>-1, 'msg'=>'用户ID为空'];
        }

        $games = $this->db()->where('is_delete', 0)->select('game_type, game_title')->get(Game_List)->result_array();
        if (empty($games)) {
            return ['result'=>-1, 'msg'=>'游戏列表为空'];
        }

        $from = strtotime($from);
        $to = strtotime($to) + 86400;

        $stats = $this->db()->where('account_id', $aid)
            ->where('over_time >=', $from)
            ->where('over_time <', $to)
            ->group_by('game_type')
            ->select('game_type, SUM(score) as total')
            ->get(Room_Account)
            ->result_array();

        $sum = 0;
        $typeScore = [];

        if (!empty($stats)) {
            foreach ($stats as $stat) {
                $typeScore[$stat['game_type']] = $stat['total'];
                $sum += $stat['total'];
            }
        }

        foreach ($games as $k => $game) {
            $games[$k]['game_score'] = isset($typeScore[$game['game_type']]) ? $typeScore[$game['game_type']] : 0;
        }

        return ['result'=>0, 'data'=>$games, 'sum'=>$sum];
    }
}