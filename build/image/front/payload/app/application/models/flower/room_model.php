<?php

include_once 'common_model.php';		//加载数据库操作类
class Room_Model extends Game_Common_Model
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
		获取用户房票
	*/
	public function getRoomTicket($arrData)
	{
		$timestamp = time();
		$result = array();
		
		if(!isset($arrData['account_id']) || $arrData['account_id'] == G_CONST::EMPTY_STRING)
		{
			log_message('error', "function(getRoomTicket):lack of account_id"." in file".__FILE__." on Line ".__LINE__);
			return $this->missingPrameterArr("account_id");
		}
	
		
		$account_id = $arrData['account_id'];

		$dealerDB = Game_Const::DBConst_Name;
		
		$ticket_where = 'account_id='.$account_id.'';
		$ticket_sql = 'select ticket_count from '.Room_Ticket.' where '.$ticket_where.'';
		$ticket_query = $this->getDataBySql($dealerDB,1,$ticket_sql);
		if(DB_CONST::DATA_NONEXISTENT == $ticket_query)
		{
			$ticket_count = 0;
			
			//默认添加房卡
			$ticket_array['create_time'] = $timestamp;
			$ticket_array['create_appid'] = $account_id;
			$ticket_array['update_time'] = $timestamp;
			$ticket_array['update_appid'] = $account_id;
			$ticket_array['is_delete'] = G_CONST::IS_FALSE;
			$ticket_array['account_id'] = $account_id;
			$ticket_array['ticket_count'] = $ticket_count;
			
			$ticket_id = $this->getInsertID($dealerDB,Room_Ticket, $ticket_array);
		}
		else
		{
			$ticket_count = $ticket_query['ticket_count'];
		}
		
		$result['ticket_count'] = $ticket_count;
		
		return array('result'=>OPT_CONST::SUCCESS,'data'=>$result,'result_message'=>"获取用户房票");
	}
	
	
	/*
		获取商品
	*/
	public function getGoodsList()
	{
		$timestamp = time();
		$result = array();
		
		$goods_where = 'is_delete=0';
		$goods_sql = 'select goods_id,title,price,ticket_count from '.Payment_Goods.' where '.$goods_where.'';
		$goods_query = $this->getDataBySql(0,$goods_sql);
		if(DB_CONST::DATA_NONEXISTENT != $goods_query)
		{
			foreach($goods_query as $goods_item)
			{
				$array['goods_id'] = $goods_item['goods_id'];
				$array['title'] = $goods_item['title'];
				$array['price'] = $goods_item['price'];
				$array['ticket_count'] = $goods_item['ticket_count'];
				
				$result[] = $array;
			}
		}
		
		return array('result'=>OPT_CONST::SUCCESS,'data'=>$result,'result_message'=>"获取商品");
	}
	
	

	/*
		获取关闭房间积分榜房间
	*/
	public function getRoomScoreboard($arrData)
	{
		$timestamp = time();
		$result = array();
		
		if(!isset($arrData['room_number']) || $arrData['room_number'] == G_CONST::EMPTY_STRING)
		{
			log_message('error', "function(getRoomScoreboard):lack of room_number"." in file".__FILE__." on Line ".__LINE__);
			return $this->missingPrameterArr("room_number");
		}

		if(!isset($arrData['account_id']) || $arrData['account_id'] == G_CONST::EMPTY_STRING)
		{
			log_message('error', "function(getRoomScoreboard):lack of account_id"." in file".__FILE__." on Line ".__LINE__);
			return $this->missingPrameterArr("account_id");
		}
		
		if(!isset($arrData['game_type']) || $arrData['game_type'] === G_CONST::EMPTY_STRING)
		{
			log_message('error', "function(getRoomScoreboard):lack of game_type"." in file".__FILE__." on Line ".__LINE__);
			return $this->missingPrameterArr("game_type");
		}
		
		$room_number = $arrData['room_number'];
		$account_id = $arrData['account_id'];
		$game_type = $arrData['game_type'];

		$dealerDB = "dealer_2";

		$room_status = -1;
		$balance_scoreboard = "";

		//游戏类型 ：1炸金花  2斗地主  3梭哈  4德州  5六人斗牛 6广东麻将   9九人斗牛，10轮庄
		switch($game_type)
		{
			case 1:
            case 110:
            case 111:
            case 92:
            case 95:
				$room_table = "flower_room";
				break;
			case 2:
				$room_table = "landlord_room";
				break;
			case 5:
			case 9:
			case 12:
			case 71:
			case 91:
			case 93:
			case 94:
				$room_table = "bull_room";
				break;
			case 6:
				$room_table = "gd_mahjong_room";
				break;
			case 10:
				$room_table = "bullturn_room";
				break;
            case 36:
            case 37:
            case 38:
                $room_table = "sangong_room";
                break;
			default :
				$room_table = "";
				break;
		}
		if($room_table != "")
		{
			//获取房间id
			$room_sql = 'select room_id,is_close from '.$room_table.' where room_number="'.$room_number.'" limit 1';
			$room_query = $this->getDataBySql($dealerDB,1,$room_sql);
			if(DB_CONST::DATA_NONEXISTENT != $room_query)
			{
				if($room_query['is_close'] == 1)
				{
					$room_id = $room_query['room_id'];

					//获取积分榜
					$score_sql = 'select create_time,board,balance_board,game_num,rule_text from room_scoreboard where room_id='.$room_id.' and game_type='.$game_type.' order by create_time desc limit 1';
					$score_query = $this->getDataBySql($dealerDB,1,$score_sql);
					if(DB_CONST::DATA_NONEXISTENT != $score_query)
					{
					    $board = json_decode($score_query['board'], true);
//					    if (is_array($board) && in_array($account_id, array_keys($board))) {
					    if (is_array($board)) {
                            $room_status = 4;	//已关闭
                            $total_num = "";
                            $rule_text = $score_query['rule_text'];
                            $rule_text_array = explode('局/',$rule_text);
                            if(is_array($rule_text_array) && count($rule_text_array) > 0)
                            {
                                $total_num = $rule_text_array[0];
                            }

                            $name_board = json_decode($score_query['balance_board'],TRUE);

                            $balance_scoreboard = array(
                                'time'=>$score_query['create_time'],
                                'scoreboard'=>$name_board,
                                'game_num'=>$score_query['game_num'],
                                'total_num'=>$total_num
                            );
                        }
					}
				}
			}
		}
		$result['room_status'] = $room_status;
		$result['balance_scoreboard'] = $balance_scoreboard;

		return array('result'=>OPT_CONST::SUCCESS,'data'=>$result,'result_message'=>"获取关闭房间积分榜");

	}
	

	/*
		获取代理商游戏socket列表
	*/
	public function getGameSocketList($arrData)
	{
		$timestamp = time();
		$result = array();
		
		if(!isset($arrData['dealer_num']) || $arrData['dealer_num'] == G_CONST::EMPTY_STRING)
		{
			log_message('error', "function(getGameSocketList):lack of dealer_num"." in file".__FILE__." on Line ".__LINE__);
			return $this->missingPrameterArr("dealer_num");
		}
		
		$dealer_num = $arrData['dealer_num'];
		$dealerDB = "dealer_".$dealer_num;

		$game_type = -1;
		if(isset($arrData['game_type']) && $arrData['game_type'] != G_CONST::EMPTY_STRING)
		{
			$game_type = $arrData['game_type'];
		}

		//获取游戏列表
		$game_where = 'is_delete=0';
		if($game_type > 0)
		{
			$game_where .= '';
		}

		$game_sql = 'select game_type,domain_host,domain_port from '.Game_List.' where '.$game_where.'';
		$game_query = $this->getDataBySql($dealerDB,0,$game_sql);
		if(DB_CONST::DATA_NONEXISTENT != $game_query)
		{
			foreach($game_query as $game_item)
			{
				$result[$game_item['game_type']] = $game_item['domain_host'].":".$game_item['domain_port'];
			}
		}

		if(count($result) == 0)
		{
			$result = new stdClass;
		}

		return array('result'=>OPT_CONST::SUCCESS,'data'=>$result,'result_message'=>"获取代理商游戏socket列表");
	}

	
	
}