<?php

include_once __DIR__.'/common_model.php';		//加载数据库操作类
class Detail_Model extends Game_Common_Model
{
	public function __construct()
	{
		header('Content-Type: text/html; charset=utf-8'); 
		parent::__construct();
	}
	

	/*
		获取游戏列表
	*/
	public function getGameList($arrData)
	{
		$result = array();
		$timestamp = time();

		if(!isset($arrData['dealer_num']) || $arrData['dealer_num'] === "" )
		{
			log_message('error', "function(getGameList):lack of dealer_num"." in file".__FILE__." on Line ".__LINE__);
			return $this->missingPrameterArr("dealer_num");
		}

		if($arrData['dealer_num'] == "-1")
		{
			return array('result'=>OPT_CONST::SUCCESS,'data'=>$result,'result_message'=>"获取游戏列表");
		}
		$dealer_num = $arrData['dealer_num'];
		$DelaerConst = "Dealer_".$dealer_num;
		if($dealer_num == "-1" || !class_exists($DelaerConst))
		{
			return array('result'=>OPT_CONST::SUCCESS,'data'=>$result,'result_message'=>"获取游戏列表");
		}
        $dealerDB = $DelaerConst::DBConst_Name;	

		$list_sql = 'select game_type,game_title from '.Game_List.' where is_delete=0 order by game_type asc';
		$list_query = $this->getDataBySql($dealerDB,0,$list_sql);
		if($list_query != DB_CONST::DATA_NONEXISTENT)
		{
			foreach($list_query as $list_item)
			{
				$game_type = $list_item['game_type'];
				$game_title = $list_item['game_title'];

				$array['game_type'] = $game_type;
				$array['game_title'] = $game_title;

				$result[] = $array;
			}
		}
		return array('result'=>OPT_CONST::SUCCESS,'data'=>$result,'result_message'=>"获取游戏列表");
	}


	/*
		获取开局明细统计
	*/
	public function getPlayCount($arrData)
	{
		$result = array();
		$timestamp = time();

		if(!isset($arrData['dealer_num']) || $arrData['dealer_num'] === "" )
		{
			log_message('error', "function(getPlayCount):lack of dealer_num"." in file".__FILE__." on Line ".__LINE__);
			return $this->missingPrameterArr("dealer_num");
		}
		if(!isset($arrData['from'])|| $arrData['from'] === "")
		{
			log_message('error', "function(getPlayCount):lack of from"." in file".__FILE__." on Line ".__LINE__);
			return $this->missingPrameterArr("from");
		}
		if(!isset($arrData['to'])|| $arrData['to'] === "")
		{
			log_message('error', "function(getPlayCount):lack of to"." in file".__FILE__." on Line ".__LINE__);
			return $this->missingPrameterArr("to");
		}
		if(!isset($arrData['game_type']))
		{
			log_message('error', "function(getPlayCount):lack of game_type"." in file".__FILE__." on Line ".__LINE__);
			return $this->missingPrameterArr("game_type");
		}

		if($arrData['dealer_num'] == "-1")
		{
			return array('result'=>OPT_CONST::SUCCESS,'data'=>$result,'result_message'=>"获取开局明细统计");
		}
		$dealer_num = $arrData['dealer_num'];
		$DelaerConst = "Dealer_".$dealer_num;
		if($dealer_num == "-1" || !class_exists($DelaerConst))
		{
			return array('result'=>OPT_CONST::SUCCESS,'data'=>$result,'result_message'=>"获取开局明细统计");
		}
        $dealerDB = $DelaerConst::DBConst_Name;	

		$from = $arrData['from'];
		$to = $arrData['to'];
		$game_type = $arrData['game_type'];

		$from_timestamp = strtotime($arrData['from']);
		$to_timestamp = strtotime($arrData['to']) + 86400;

		$list_where = 'is_delete=0';
		if($game_type != "" && $game_type > 0)
		{
			$list_where .= ' and game_type='.$game_type;
		}
		$list_sql = 'select game_type,game_title from '.Game_List.' where '.$list_where.' order by game_type asc';
		$list_query = $this->getDataBySql($dealerDB,0,$list_sql);
		if($list_query != DB_CONST::DATA_NONEXISTENT)
		{
			foreach($list_query as $list_item)
			{
				$game_type = $list_item['game_type'];
				$game_title = $list_item['game_title'];
				$count = 0;

				$count_sql = 'select sum(total_count) as count from '.Summary_Dealer.' where day_timestamp>='.$from_timestamp.' and day_timestamp<'.$to_timestamp.' and game_type='.$game_type.' and is_delete=0';
				$count_query = $this->getDataBySql($dealerDB,1,$count_sql);
				if($count_query != DB_CONST::DATA_NONEXISTENT)
				{
					$count = $count_query['count'];
				}
				if($count == null)
				{
					$count = 0;
				}
					
				if($count == 0 || $dealer_num == 2 )
				{
					log_message('debug', "function(getPlayCount):update summary dealer count"." in file".__FILE__." on Line ".__LINE__);

					$sd_data_id = -1;
					if($from == $to)
					{
						$countData_sql = 'select data_id from '.Summary_Dealer.' where day_timestamp='.$from_timestamp.' and game_type='.$game_type.' and is_delete=0';
						$countData_query = $this->getDataBySql($dealerDB,1,$countData_sql);
						if($countData_query == DB_CONST::DATA_NONEXISTENT)
						{
					        $sd_array['create_time'] = $timestamp;
					        $sd_array['create_appid'] = "admincms";
					        $sd_array['update_time'] = $timestamp;
					        $sd_array['update_appid'] = "admincms";
					        $sd_array['is_delete'] = 0;
					        $sd_array['day_timestamp'] = $from_timestamp;
					        $sd_array['game_type'] = $game_type;
					        $sd_array['total_count'] = 0;
					        $sd_data_id = $this->getInsertID($dealerDB,Summary_Dealer, $sd_array);
				        }
				        else
				        {
					        $sd_data_id = $countData_query['data_id'];
				        }
			        }
					
					//获取游戏开局数量
					$count_sql = 'select count(board_id) as count from '.Room_ScoreBoard.' where create_time>='.$from_timestamp.' and create_time<'.$to_timestamp.' and game_type='.$game_type.' and is_delete=0';
					$count_query = $this->getDataBySql($dealerDB,1,$count_sql);
					if($count_query != DB_CONST::DATA_NONEXISTENT)
					{
						$count = $count_query['count'];
						
						if($from == $to && $count > 0)
						{
							$updateSD_str = 'total_count='.$count;
							$updateSD_where = 'data_id='.$sd_data_id.'';
							$updateSD_query = $this->changeNodeValue($dealerDB,Summary_Dealer,$updateSD_str,$updateSD_where);
						}
						
					}
				}
				
				$array['game_type'] = $game_type;
				$array['game_title'] = $game_title;
				$array['count'] = $count;


				$result[] = $array;
			}
		}

		$income = 0;
		$disburse = 0;

		$roomcard_sql = 'SELECT sum(income) as income,sum(disburse) as disburse FROM `room_ticket_journal` WHERE create_time>='.$from_timestamp.' and create_time<'.$to_timestamp.' and object_type=3';
		$query = $this->getDataBySql($dealerDB,1,$roomcard_sql);
		if($query != DB_CONST::DATA_NONEXISTENT)
		{
			if($query['income'] != NULL)
			{
				$income = $query['income'];
			}
			if($query['disburse'] != NULL)
			{
				$disburse = $query['disburse'];
			}
			
		}
		$balance = $disburse - $income;



		return array('result'=>OPT_CONST::SUCCESS,'data'=>$result,'result_message'=>"获取开局明细统计",'balance'=>$balance);
	}
	

	/*
		获取开局明细
	*/
	public function getPlayDetailList($arrData)
	{
		$result = array();
		$timestamp = time();

		if(!isset($arrData['dealer_num']) || $arrData['dealer_num'] === "" )
		{
			log_message('error', "function(getPlayDetailList):lack of dealer_num"." in file".__FILE__." on Line ".__LINE__);
			return $this->missingPrameterArr("dealer_num");
		}
		if(!isset($arrData['from'])|| $arrData['from'] === "")
		{
			log_message('error', "function(getPlayDetailList):lack of from"." in file".__FILE__." on Line ".__LINE__);
			return $this->missingPrameterArr("from");
		}
		if(!isset($arrData['to'])|| $arrData['to'] === "")
		{
			log_message('error', "function(getPlayDetailList):lack of to"." in file".__FILE__." on Line ".__LINE__);
			return $this->missingPrameterArr("to");
		}
		if(!isset($arrData['game_type']))
		{
			log_message('error', "function(getPlayDetailList):lack of game_type"." in file".__FILE__." on Line ".__LINE__);
			return $this->missingPrameterArr("game_type");
		}
		if(!isset($arrData['page']))
		{
			log_message('error', "function(getPlayDetailList):lack of page"." in file".__FILE__." on Line ".__LINE__);
			return $this->missingPrameterArr("page");
		}

		if($arrData['dealer_num'] == "-1")
		{
			return array('result'=>OPT_CONST::SUCCESS,'data'=>$result,'result_message'=>"获取开局明细统计",'sum_page'=>1);
		}
		$dealer_num = $arrData['dealer_num'];
		$DelaerConst = "Dealer_".$dealer_num;
		if($dealer_num == "-1" || !class_exists($DelaerConst))
		{
			return array('result'=>OPT_CONST::SUCCESS,'data'=>$result,'result_message'=>"获取开局明细统计",'sum_page'=>1);
		}
        $dealerDB = $DelaerConst::DBConst_Name;	

		$from = $arrData['from'];
		$to = $arrData['to'];
		$page = $arrData['page'];
		if($page == 0 || $page == "")
		{
			$page = 1;
		}
		$game_type = $arrData['game_type'];

		$from_timestamp = strtotime($arrData['from']);
		$to_timestamp = strtotime($arrData['to']) + 86400;

		$limit = 10;
		$offset = ($page - 1) * $limit;
		$sum_page = 1;


		$game_where = 'create_time>='.$from_timestamp.' and create_time<'.$to_timestamp;
		if($game_type != "" && $game_type > 0)
		{
			$game_where .= ' and game_type='.$game_type;
		}
		$game_where .= ' and is_delete=0';

		$game_sql = 'select game_type,room_id,create_time,start_time,round from '.Room_ScoreBoard.' where '.$game_where.' order by create_time desc limit '.$offset.','.$limit;
		$game_query = $this->getDataBySql($dealerDB,0,$game_sql);
		if($game_query != DB_CONST::DATA_NONEXISTENT)
		{
			$count_sql = 'select sum(total_count) as count from '.Summary_Dealer.' where day_timestamp>='.$from_timestamp.' and day_timestamp<'.$to_timestamp;
			if($game_type != "" && $game_type > 0)
			{
				$count_sql .= ' and game_type='.$game_type;
			}
			$count_sql .= ' and is_delete=0';
			// $count_sql = 'select count(board_id) as count from '.Room_ScoreBoard.' where '.$game_where;
			$count_query = $this->getDataBySql($dealerDB,1,$count_sql);
			$sum_page = ceil($count_query['count'] / $limit);

			foreach($game_query as $game_item)
			{
				$game_type = $game_item['game_type'];
				$room_id = $game_item['room_id'];
				$start_time = $game_item['start_time'];
				$end_time = $game_item['create_time'];
				$round = $game_item['round'];

				$game_title = "";
				$list_sql = "select game_title from ".Game_List.' where game_type='.$game_type;
				$list_query = $this->getDataBySql($dealerDB,1,$list_sql);
				if($list_query != DB_CONST::DATA_NONEXISTENT)
				{
					$game_title = $list_query['game_title'];
				}

				$room_number = $this->getRoomNumberByRoomID($dealerDB,$room_id,$game_type);
				if($room_number == "")
				{
					continue;
				}

				$array['room_number'] = $room_number;
				$array['game_title'] = $game_title;
				if($start_time == "" || $start_time <= 1000)
				{
					$array['start_time'] = "";
				}
				else
				{
					$array['start_time'] = date('Y-m-d H:i:s',$start_time);
				}
				
				$array['end_time'] = date('Y-m-d H:i:s',$end_time);
				$array['round'] = $round;

				$result[] = $array;
			}
		}

		return array('result'=>OPT_CONST::SUCCESS,'data'=>$result,'result_message'=>"获取开局明细",'sum_page'=>$sum_page);
	}


}