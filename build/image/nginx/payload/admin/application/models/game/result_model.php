<?php

include_once __DIR__.'/common_model.php';		//加载数据库操作类
class Result_Model extends Game_Common_Model
{
	public function __construct()
	{
		header('Content-Type: text/html; charset=utf-8'); 
		parent::__construct();
	}
	

	
	/*
		获取房间进行轮数
	*/
	public function getRoomRound($arrData)
	{
		$result = array();
		$timestamp = time();

		if(!isset($arrData['dealer_num']) || $arrData['dealer_num'] === "" )
		{
			log_message('error', "function(getRoomRound):lack of dealer_num"." in file".__FILE__." on Line ".__LINE__);
			return $this->missingPrameterArr("dealer_num");
		}
		if(!isset($arrData['room_number'])|| $arrData['room_number'] === "")
		{
			log_message('error', "function(getRoomRound):lack of room_number"." in file".__FILE__." on Line ".__LINE__);
			return $this->missingPrameterArr("room_number");
		}
		if(!isset($arrData['game_type'])|| $arrData['game_type'] === "")
		{
			log_message('error', "function(getRoomRound):lack of game_type"." in file".__FILE__." on Line ".__LINE__);
			return $this->missingPrameterArr("game_type");
		}

		if($arrData['dealer_num'] == "-1")
		{
			return array('result'=>OPT_CONST::SUCCESS,'data'=>$result,'result_message'=>"获取房间进行轮数");
		}

		$dealer_num = $arrData['dealer_num'];
		$DelaerConst = "Dealer_".$dealer_num;
		if($dealer_num == "-1" || !class_exists($DelaerConst))
		{
			return array('result'=>OPT_CONST::SUCCESS,'data'=>$result,'result_message'=>"获取房间进行轮数");
		}
        $dealerDB = $DelaerConst::DBConst_Name;	

		$room_number = $arrData['room_number'];
		$game_type = $arrData['game_type'];

		$room_id = $this->getRoomIDByRoomNumber($dealerDB,$room_number,$game_type);

		$round_sql = 'select round from '.Room_ScoreBoard.' where room_id='.$room_id.' and game_type='.$game_type.' group by round order by round desc';
		//$round_sql = 'select round from '.Room_GameResult.' where room_id='.$room_id.' group by round order by round desc';
		$round_query = $this->getDataBySql($dealerDB,0,$round_sql);
		if($round_query != DB_CONST::DATA_NONEXISTENT)
		{
			foreach($round_query as $round_item)
			{
				$array['round'] = $round_item['round'];
				if($array['round'] <= 0)
				{
					$array['round'] = 1;
				}
				$result[] = $array;
			}
		}
		return array('result'=>OPT_CONST::SUCCESS,'data'=>$result,'result_message'=>"获取房间进行轮数");
	}


	/*
		获取房间游戏结果
	*/
	public function getRoomGameResult($arrData)
	{
		$result = array();
		$timestamp = time();

		if(!isset($arrData['dealer_num']) || $arrData['dealer_num'] === "" )
		{
			log_message('error', "function(getRoomGameResult):lack of dealer_num"." in file".__FILE__." on Line ".__LINE__);
			return $this->missingPrameterArr("dealer_num");
		}
		if(!isset($arrData['room_number'])|| $arrData['room_number'] === "")
		{
			log_message('error', "function(getRoomGameResult):lack of room_number"." in file".__FILE__." on Line ".__LINE__);
			return $this->missingPrameterArr("room_number");
		}
		if(!isset($arrData['game_type'])|| $arrData['game_type'] === "")
		{
			log_message('error', "function(getRoomGameResult):lack of game_type"." in file".__FILE__." on Line ".__LINE__);
			return $this->missingPrameterArr("game_type");
		}
		if(!isset($arrData['round'])|| $arrData['round'] === "")
		{
			log_message('error', "function(getRoomGameResult):lack of round"." in file".__FILE__." on Line ".__LINE__);
			return $this->missingPrameterArr("round");
		}

		if($arrData['dealer_num'] == "-1")
		{
			return array('result'=>OPT_CONST::SUCCESS,'data'=>$result,'result_message'=>"获取房间游戏结果");
		}
		$dealer_num = $arrData['dealer_num'];
		$DelaerConst = "Dealer_".$dealer_num;
		if($dealer_num == "-1" || !class_exists($DelaerConst))
		{
			return array('result'=>OPT_CONST::SUCCESS,'data'=>$result,'result_message'=>"获取房间游戏结果");
		}
        $dealerDB = $DelaerConst::DBConst_Name;	

		$room_number = $arrData['room_number'];
		$game_type = $arrData['game_type'];
		$round = $arrData['round'];

		$room_id = $this->getRoomIDByRoomNumber($dealerDB,$room_number,$game_type);


		$result['start_time'] = "";
		$result['end_time'] = "";
		$result['rule_text'] = "";
		$result['player_array'] = array();

		//获取游戏房间开局信息
		$board_sql = 'select create_time,start_time,rule_text,balance_board from '.Room_ScoreBoard.' where room_id='.$room_id.' and game_type='.$game_type.' and round='.$round;
		$board_query = $this->getDataBySql($dealerDB,1,$board_sql);
		if($board_query == DB_CONST::DATA_NONEXISTENT)
		{
			return array('result'=>OPT_CONST::SUCCESS,'data'=>$result,'result_message'=>"获取房间游戏结果");
		}

		$result['balance_board'] = json_decode($board_query['balance_board'],TRUE);

		$result['start_time'] = "";
		if($board_query['start_time'] != "" && $board_query['start_time'] > 0)
		{
			$result['start_time'] = date("Y-m-d H:i:s",$board_query['start_time']);
		}
		
		$result['end_time'] = date("Y-m-d H:i:s",$board_query['create_time']);
		$result['rule_text'] = $board_query['rule_text'];

		$player_array = array();
		$gameresult_sql = 'select game_result from '.Room_GameResult.' where room_id='.$room_id.' and game_type='.$game_type.' and round='.$round.' order by create_time asc';
		$gameresult_query = $this->getDataBySql($dealerDB,0,$gameresult_sql);
		if($gameresult_query != DB_CONST::DATA_NONEXISTENT)
		{
			foreach($gameresult_query as $item)
			{
				$game_result_str = $item['game_result'];
				$game_result_array = json_decode($game_result_str,TRUE);
				if(!is_array($game_result_array))
				{
					continue;
				}
				switch($game_type)
				{
                    case 12:
					case 9:
					case 91:
					case 93:
					case 94:
					case 5:
						$deal_result = $this->dealResultBull6($game_result_array);
						break;
                    case 71:
                        $deal_result = $this->dealResultLBull($game_result_array);
                        break;
					case 1:
                    case 110:
                    case 111:
                    case 92:
                    case 95:
						$deal_result = $this->dealResultFlower($game_result_array);
						break;
                    case 36:
                    case 37:
                    case 38:
                        $deal_result = $this->dealResultSangong($game_result_array);
                        break;
					default:
						continue;
						break;
				}


				$player_array[] = $deal_result;
			}
		}
		$result['player_array'] = $player_array;

		return array('result'=>OPT_CONST::SUCCESS,'data'=>$result,'result_message'=>"获取房间游戏结果");
	}


	/*
		6人斗牛结果处理
	*/
	protected function dealResultBull6($game_result)
	{
		/*
			{"pAry":[{"p":"4","s":40,"c":"B4,B12,A5,B7,A10","m":"8","n":"\u4f59\u6c5f"},{"p":"15","s":-40,"c":"D7,C7,C2,D12,C12","m":0,"n":"Mike\u4e18"}],"gData":{"time":1497875462,"rid":"460","rnd":"6","gnum":"1","tnum":"2","bid":"15","bmult":"1"}}
		*/
		$pAry = $game_result['pAry'];
		$gData = $game_result['gData'];

		$game_num = $gData['gnum'];
		$total_num = $gData['tnum'];
		$banker_id = $gData['bid'];

		$play_array = array();
		foreach($pAry as $item)
		{
			$player_id = $item['p'];
			$score = $item['s'];
			$card_str = $item['c'];
			$mult = $item['m']."倍";
			$name = $item['n'];
			$card_type = -1;
			if(isset($item['ct']))
			{
				$card_type = $item['ct'];
			}
			$combo_point = 0;
			if(isset($item['cp']))
			{
				$combo_point = $item['cp'];
			}
			$is_banker = 0;
			if($banker_id == $player_id)
			{
				$is_banker = 1;
				$mult = $gData['bmult']."倍";
			}

			if($card_str == "" && $mult == -1)
			{
				$is_join = 0;
				$player_cards = "未参与该局游戏";
				$card_type_str = "";
				$mult = "";
				$score = "";
			}
			else
			{
				$is_join = 1;
				////牌型  1无牛，2牛1-6，3牛7-9，4牛牛，5五花，6炸弹,7五小牛
				switch($card_type)
				{
					case 7:
						$card_type_str = "五小牛";
						break;
					case 6:
						$card_type_str = "炸弹牛";
						break;
					case 5:
						$card_type_str = "五花牛";
						break;
					case 4:
						$card_type_str = "牛牛";
						break;
					case 3:
					case 2:
						$card_type_str = "牛".$combo_point;
						break;
					case 1:
						$card_type_str = "无牛";
						break;
					default :
						$card_type_str = "";
						break;
				}
				
				$player_cards = array();
				if($card_str != "")
				{
					$cards = explode(",", $card_str);
					foreach($cards as $card)
					{
						$suit = substr($card, 0, 1);
						$point = substr($card, 1);
						if($point > 0){
                            switch($suit)
                            {
                                case "A":
                                    $suit = 4;
                                    break;
                                case "B":
                                    $suit = 3;
                                    break;
                                case "C":
                                    $suit = 2;
                                    break;
                                case "D":
                                    $suit = 1;
                                    break;
                            }
                            if($point == "11")
                            {
                                $point = "J";
                            }
                            if($point == "12")
                            {
                                $point = "Q";
                            }
                            if($point == "13")
                            {
                                $point = "K";
                            }
                        }
						$card_ary['suit'] = $suit;
						$card_ary['point'] = $point;
						$player_cards[] = $card_ary;
					}
				}
			}
			
			$array['name'] = $name;
			$array['chip'] = $mult;
			$array['score'] = $score;
			$array['is_banker'] = $is_banker;
			$array['card_type_str'] = $card_type_str;
			$array['player_cards'] = $player_cards;
			$array['is_join'] = $is_join;

			$play_array[] = $array;
		}

		$result['game_num'] = $game_num;
		$result['total_num'] = $total_num;
		$result['player_cards'] = $play_array;

		return $result;

	}

    /*
        癞子牛牛结果处理
    */
    protected function dealResultLBull($game_result)
    {
        /*
            {"pAry":[{"p":"4","s":40,"c":"B4,B12,A5,B7,A10","m":"8","n":"\u4f59\u6c5f"},{"p":"15","s":-40,"c":"D7,C7,C2,D12,C12","m":0,"n":"Mike\u4e18"}],"gData":{"time":1497875462,"rid":"460","rnd":"6","gnum":"1","tnum":"2","bid":"15","bmult":"1"}}
        */
        $pAry = $game_result['pAry'];
        $gData = $game_result['gData'];

        $game_num = $gData['gnum'];
        $total_num = $gData['tnum'];
        $banker_id = $gData['bid'];

        $play_array = array();
        foreach($pAry as $item)
        {
            $player_id = $item['p'];
            $score = $item['s'];
            $card_str = $item['c'];
            $mult = $item['m']."倍";
            $name = $item['n'];
            $card_type = -1;
            if(isset($item['ct']))
            {
                $card_type = $item['ct'];
            }
            $combo_point = 0;
            if(isset($item['cp']))
            {
                $combo_point = $item['cp'];
            }
            $is_banker = 0;
            if($banker_id == $player_id)
            {
                $is_banker = 1;
                $mult = $gData['bmult']."倍";
            }

            if($card_str == "" && $mult == -1)
            {
                $is_join = 0;
                $player_cards = "未参与该局游戏";
                $card_type_str = "";
                $mult = "";
                $score = "";
            }
            else
            {
                $is_join = 1;
                ////牌型  1无牛，2牛1-6，3牛7-9，4牛牛，5五花，6炸弹,7五小牛
                switch($card_type)
                {
                    case 10:
                        $card_type_str = "五小牛";
                        break;
                    case 9:
                        $card_type_str = "炸弹牛";
                        break;
                    case 8:
                        $card_type_str = "葫芦牛";
                        break;
                    case 7:
                        $card_type_str = "同花牛";
                        break;
                    case 6:
                        $card_type_str = "顺子牛";
                        break;
                    case 5:
                        $card_type_str = "五花牛";
                        break;
                    case 4:
                        $card_type_str = "牛牛";
                        break;
                    case 3:
                    case 2:
                        $card_type_str = "牛".$combo_point;
                        break;
                    case 1:
                        $card_type_str = "无牛";
                        break;
                    default :
                        $card_type_str = "";
                        break;
                }

                $player_cards = array();
                if($card_str != "")
                {
                    $cards = explode(",", $card_str);
                    foreach($cards as $card)
                    {
                        $suit = substr($card, 0, 1);
                        $point = substr($card, 1);
                        switch($suit)
                        {
                            case "A":
                                $suit = 4;
                                break;
                            case "B":
                                $suit = 3;
                                break;
                            case "C":
                                $suit = 2;
                                break;
                            case "D":
                                $suit = 1;
                                break;
                        }
                        if($point == "11")
                        {
                            $point = "J";
                        }
                        if($point == "12")
                        {
                            $point = "Q";
                        }
                        if($point == "13")
                        {
                            $point = "K";
                        }
                        if($suit == 'J' && $point == 1){
                            $suit = 'Y';
                        }
                        if($suit == 'J' && $point == 2){
                            $suit = 'X';
                        }

                        $card_ary['suit'] = $suit;
                        $card_ary['point'] = $point;
                        $player_cards[] = $card_ary;
                    }
                }
            }

            $array['name'] = $name;
            $array['chip'] = $mult;
            $array['score'] = $score;
            $array['is_banker'] = $is_banker;
            $array['card_type_str'] = $card_type_str;
            $array['player_cards'] = $player_cards;
            $array['is_join'] = $is_join;

            $play_array[] = $array;
        }

        $result['game_num'] = $game_num;
        $result['total_num'] = $total_num;
        $result['player_cards'] = $play_array;

        return $result;

    }


	/*
		诈金花结果处理
	*/
	protected function dealResultFlower($game_result)
	{
		/*
			{"pAry":[{"p":"15","s":"84","c":"C13,A1,B10","ct":"1"},{"p":"4","s":"129","c":"B13,B2,C8","ct":"1"}],"gData":{"time":1498136713,"rid":"1214","rnd":"2","gnum":"2","tnum":"2"}}	
		*/
		$pAry = $game_result['pAry'];
		$gData = $game_result['gData'];

		$game_num = $gData['gnum'];
		$total_num = $gData['tnum'];

		$play_array = array();
		foreach($pAry as $item)
		{
			$player_id = $item['p'];
			$card_str = $item['c'];
			$name = $item['n'];
			$chip = $item['s'];
			$card_type = -1;
			if(isset($item['ct']))
			{
				$card_type = $item['ct'];
			}

			$is_banker = 0;
			
			if($card_str == "")
			{
				$is_join = 0;
				$player_cards = "未参与该局游戏";
				$card_type_str = "";
			}
			else
			{
				$is_join = 1;
				////牌型  1高牌 2对子 3顺子 4同花 5同花顺 6三条
				switch($card_type)
				{
					case 6:
						$card_type_str = "三条";
						break;
					case 5:
						$card_type_str = "同花顺";
						break;
					case 4:
						$card_type_str = "同花";
						break;
					case 3:
						$card_type_str = "顺子";
						break;
					case 2:
						$card_type_str = "对子";
						break;
					case 1:
						$card_type_str = "高牌";
						break;
					default :
						$card_type_str = "";
						break;
				}
				
				$player_cards = array();
				if($card_str != "")
				{
					$cards = explode(",", $card_str);
					foreach($cards as $card)
					{
						$suit = substr($card, 0, 1);
						$point = substr($card, 1);
						switch($suit)
						{
							case "A":
								$suit = 4;
								break;
							case "B":
								$suit = 3;
								break;
							case "C":
								$suit = 2;
								break;
							case "D":
								$suit = 1;
								break;
						}
						if($point == "11")
						{
							$point = "J";
						}
						if($point == "12")
						{
							$point = "Q";
						}
						if($point == "13")
						{
							$point = "K";
						}
						$card_ary['suit'] = $suit;
						$card_ary['point'] = $point;
						$player_cards[] = $card_ary;
					}
				}
			}
			
			$array['name'] = $name;
			$array['chip'] = $chip;
			$array['score'] = isset($item['w']) ? $item['w'] : "";
			$array['is_banker'] = $is_banker;
			$array['card_type_str'] = $card_type_str;
			$array['player_cards'] = $player_cards;
			$array['is_join'] = $is_join;

			$play_array[] = $array;
		}

		$result['game_num'] = $game_num;
		$result['total_num'] = $total_num;
		$result['player_cards'] = $play_array;

		return $result;

	}

	protected function dealResultSangong($game_result)
    {
        $pAry = $game_result['pAry'];
        $gData = $game_result['gData'];

        $game_num = $gData['gnum'];
        $total_num = $gData['tnum'];
        $banker_id = $gData['bid'];

        $play_array = array();
        foreach($pAry as $item)
        {
            $player_id = $item['p'];
            $score = $item['s'];
            $card_str = $item['c'];
            $mult = $item['m']."倍";
            $name = $item['n'];
            $is_banker = 0;
            if($banker_id == $player_id)
            {
                $is_banker = 1;
                $mult = $gData['bmult']."倍";
            }

            if($card_str == "" && $mult == -1)
            {
                $is_join = 0;
                $player_cards = "未参与该局游戏";
                $card_type_str = "";
                $mult = "";
                $score = "";
            }
            else
            {
                $is_join = 1;
                $card_type_str = $item['card_text'] . ' x' . $item['card_times'];
                $player_cards = array();
                if($card_str != "")
                {
                    $cards = explode(",", $card_str);
                    foreach($cards as $card)
                    {
                        $suit = substr($card, 0, 1);
                        $point = substr($card, 1);
                        switch($suit)
                        {
                            case "A":
                                $suit = 4;
                                break;
                            case "B":
                                $suit = 3;
                                break;
                            case "C":
                                $suit = 2;
                                break;
                            case "D":
                                $suit = 1;
                                break;
                        }
                        if($point == "11")
                        {
                            $point = "J";
                        }
                        if($point == "12")
                        {
                            $point = "Q";
                        }
                        if($point == "13")
                        {
                            $point = "K";
                        }
                        $card_ary['suit'] = $suit;
                        $card_ary['point'] = $point;
                        $player_cards[] = $card_ary;
                    }
                }
            }

            $array['name'] = $name;
            $array['chip'] = $mult;
            $array['score'] = $score;
            $array['is_banker'] = $is_banker;
            $array['card_type_str'] = $card_type_str;
            $array['player_cards'] = $player_cards;
            $array['is_join'] = $is_join;

            $play_array[] = $array;
        }

        $result['game_num'] = $game_num;
        $result['total_num'] = $total_num;
        $result['player_cards'] = $play_array;

        return $result;
    }
}