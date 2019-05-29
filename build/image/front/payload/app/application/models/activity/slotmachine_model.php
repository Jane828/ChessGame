<?php

include_once 'common_model.php';		//加载数据库操作类
class SlotMachine_Model extends Activity_Common_Model
{
	public function __construct()
	{
		header('Content-Type: text/html; charset=utf-8'); 
		parent::__construct();
	}
	
	/************************************************
					logic function
	*************************************************/

	//mode 1 贪婪收分
	public function greed($bet_array)
	{
		$multiple = [10, 15, 50, 120, 5, 3,
					 10, 20,  3,   0, 5, 3,
					 10, 15,  3,  40, 5, 3,
					 10, 30,  3,   0, 5, 3
			];

		for ($i=0; $i < 24; $i++) { 
			switch ($i) {
				case '0':
				case '11':
				case '12':
					$rewards[$i] = $multiple[$i] * $bet_array[6];
					break;

				case '1':
				case '13':
				case '23':
					$rewards[$i] = $multiple[$i] * $bet_array[4];
					break;

				case '2':
				case '3':
					$rewards[$i] = $multiple[$i] * $bet_array[0];
					break;

				case '4':
				case '5':
				case '10':
				case '16':
				case '22':
					$rewards[$i] = $multiple[$i] * $bet_array[7];
					break;

				case '6':
				case '17':
				case '18':
					$rewards[$i] = $multiple[$i] * $bet_array[5];
					break;

				case '7':
				case '8':
					$rewards[$i] = $multiple[$i] * $bet_array[3];
					break;

				case '14':
				case '15':
					$rewards[$i] = $multiple[$i] * $bet_array[1];
					break;

				case '19':
				case '20':
					$rewards[$i] = $multiple[$i] * $bet_array[2];
					break;
				
				default:   //9  21  luck
					$rewards[$i] = 0;
					break;
			}
		}

		$bet_count = 0;
		for ($i=0; $i < 8; $i++) { 
			$bet_count += $bet_array[$i];
		}
		//echo "bet_count:".$bet_count.PHP_EOL;

		asort($rewards);	

		$win_total_reward = 0;
		$lose_total_reward = 0;
		for ($i=0; $i < 24; $i++) { 
			if($rewards[$i] > $bet_count){
				$win_indexs[$i] = $rewards[$i];
				$win_total_reward += $rewards[$i];
			} else {
				$lose_indexs[$i] = $rewards[$i];
				$lose_total_reward += $rewards[$i];
			}
		}
		//echo "win_total_reward:".$win_total_reward.PHP_EOL;
		//print_r($win_indexs);
		//echo "lose_total_reward:".$lose_total_reward.PHP_EOL;
		//print_r($lose_indexs);

		$win_numerator = 0;	 //分子
		$values = array_values($win_indexs);
		sort($values);
		$cnt = count($values);

		//echo "cnt:".$cnt.PHP_EOL;
		for ($i=0; $i < $cnt; $i++) { 
			$win_numerator += ($values[$i] * $values[$cnt-1 - $i]);
		}

		$win_denominator = $win_total_reward; //分母
		$win_bet = $win_numerator / $win_denominator;

		$lose_numerator = $lose_total_reward;	 //分子
		$lose_denominator = count($lose_indexs); //分母
		$lose_bet = $lose_numerator / $lose_denominator;


		$lose_rate =  ($win_bet-$lose_bet!=0) ? ($win_bet - 0.7 * $bet_count)/($win_bet-$lose_bet) : 1;

		//echo "lose_rate:".$lose_rate.PHP_EOL;

		$rand_num = mt_rand(0, 10000 - 1);

		if($rand_num < $lose_rate * 10000){	//输
			//echo "输－－－－－－－".PHP_EOL;
			$index = array_rand($lose_indexs);
		} else { //赢
			//echo "赢－－－－－－－".PHP_EOL;
			$index = $this->randomDescWeightIndex($win_indexs);
		}

		return $index;
	}

	//按权重取随机值
	public function randomWeightIndex($index_arr) 
	{
		$choose_index = -1;
		if(empty($index_arr)){
			return $choose_index;
		}
		$cnt = count($index_arr);
		$total = 0;
		foreach ($index_arr as $weight) {
			$total += $weight;
		}

		if(0==$total){
			return array_rand($index_arr);
		}

		$rand_num = mt_rand(0, $total-1);
		$critical = 0;
		foreach ($index_arr as $key => $value) {
			$critical += $value;
			if($rand_num < $critical){
				$choose_index = $key;
				break;
			}
		}
		return $choose_index;
	}
	//按权重倒序取随机值
	public function randomDescWeightIndex($index_arr) 
	{
		$choose_index = -1;
		if(empty($index_arr)){
			return $choose_index;
		}

		asort($index_arr);  //升序
		$indexes = array_keys($index_arr);
		$weights = array_reverse($index_arr, false);  //降序

		$cnt = count($index_arr);
		$total = 0;
		foreach ($index_arr as $weight) {
			$total += $weight;
		}

		if(0==$total){
			return array_rand($index_arr);
		}
		
		$rand_num = mt_rand(0, $total-1);
		$critical = 0;
		for ($i=0; $i < $cnt; $i++) { 
			$critical += $weights[$i];
			if($rand_num < $critical){
				$choose_index = $indexes[$i];
				break;
			}
		}

		return $choose_index;
	}

	//mode 2 蓄分
	public function deposit() 
	{
		$index_arr = array( 9,6, 2, 1,10,13,
							9,9,16,11,10,17,
							9,6,13, 5,10,17,
							9,6,16,11,10,15
					);
		$index = $this->randomWeightIndex($index_arr);
		return $index;
	}

	//mode 3 吐分
	protected function withdraw()
	{
		$index_arr = array( 10,7, 3, 1,10,14,
							10,10,15,9,10,15,
							10,7,12, 6,10,15,
							10,7,15, 9,10,15
					);
		$index = $this->randomWeightIndex($index_arr);
		return $index;
	}

	//mode 4 慷慨大送分  期望 23/24  33/24   43/24   170/24    大致是1-7
	protected function generous()
	{
		$index = mt_rand(0,23);
		return $index;
	}

	public function getReward($bet_array, $index){
		$multiple = [10, 15, 50, 120, 5, 3,
					 10, 20,  3,   0, 5, 3,
					 10, 15,  3,  40, 5, 3,
					 10, 30,  3,   0, 5, 3
			];
		switch ($index) {
			case '0':
			case '11':
			case '12':
				$reward = $multiple[$index] * $bet_array[6];
				break;

			case '1':
			case '13':
			case '23':
				$reward = $multiple[$index] * $bet_array[4];
				break;

			case '2':
			case '3':
				$reward = $multiple[$index] * $bet_array[0];
				break;

			case '4':
			case '5':
			case '10':
			case '16':
			case '22':
				$reward = $multiple[$index] * $bet_array[7];
				break;

			case '6':
			case '17':
			case '18':
				$reward = $multiple[$index] * $bet_array[5];
				break;

			case '7':
			case '8':
				$reward = $multiple[$index] * $bet_array[3];
				break;

			case '14':
			case '15':
				$reward = $multiple[$index] * $bet_array[1];
				break;

			case '19':
			case '20':
				$reward = $multiple[$index] * $bet_array[2];
				break;
			
			default:   //9  21  luck
				$reward = 0;
				break;
		}
		return $reward;
	}
	
	/*
		幸运大转盘抽奖
		
		参数：
			open_id : 抽奖账号
			bet_array : 数组，各选项下注分数
		
		返回结果：
		
	*/
	public function slotmachineOpt($arrData)
	{
		$timestamp = time();
		$result = array();
		
		if(!isset($arrData['open_id']) || $arrData['open_id'] == G_CONST::EMPTY_STRING)
		{
			log_message('error', "function(fortuneWheelOpt):lack of open_id"." in file".__FILE__." on Line ".__LINE__);
			return $this->missingPrameterArr("open_id");
		}
		
		if(!isset($arrData['bet_array']) || !is_array($arrData['bet_array']) )
		{
			log_message('error', "function(receiveRedEnvelopOpt):lack of bet_array"." in file".__FILE__." on Line ".__LINE__);
			return $this->missingPrameterArr("bet_array");
		}
		if(!isset($arrData['dealer_num']) || $arrData['dealer_num'] == G_CONST::EMPTY_STRING)
        {
            log_message('error', "function(updateActivityOpt):lack of dealer_num"." in file".__FILE__." on Line ".__LINE__);
            return $this->missingPrameterArr("dealer_num");
        }
        $dealer_num = $arrData['dealer_num'];
        $DelaerConst = "Dealer_".$dealer_num;
        $dealerDB = $DelaerConst::DBConst_Name; 

		$open_id = $arrData['open_id'];	
		$bet_array = $arrData['bet_array'];

		$bet_count = 0;
		for ($i=0; $i < 8; $i++) { 
			$insert_array['option_'.$i] = $bet_array[$i];
			$bet_count += $bet_array[$i];
		}
		
		$account_query = $this->getAccountByOpenid($open_id,$dealerDB);
		if(DB_CONST::DATA_NONEXISTENT == $account_query)
		{
			return array('result'=>OPT_CONST::FAILED,'data'=>$result,'result_message'=>"用户不存在");
		}
		
		$account_id = $account_query['account_id'];
		
		//获取用户剩余房卡
		$ticket_where = 'account_id='.$account_id.' and is_delete=0';
		$ticket_sql = 'select ticket_count from '.Room_Ticket.' where '.$ticket_where;
		$ticket_query = $this->getDataBySql($dealerDB,1,$ticket_sql);
		if(DB_CONST::DATA_NONEXISTENT == $ticket_query)
		{
			return array('result'=>OPT_CONST::FAILED,'data'=>$result,'result_message'=>"房卡不足");
		}
		
		if($bet_count > $ticket_query['ticket_count'])
		{
			return array('result'=>OPT_CONST::FAILED,'data'=>$result,'result_message'=>"房卡不足");
		}
		
		//减少自己账户上的房卡
		$updateTicket_str = 'update_time='.$timestamp.',update_appid="aid_'.$account_id.'",ticket_count=ticket_count-'.$bet_count;
		$updateTicket_where = 'account_id='.$account_id;
		$updateTicket_query = $this->changeNodeValue($dealerDB,Room_Ticket,$updateTicket_str,$updateTicket_where);
		

		//开始抽奖

		$mode = $this->queryMachineMode($dealerDB);
		if($mode==1){
			$index = $this->greed($bet_array); //贪婪吸分
		} else if($mode==2){
			$index = $this->deposit(); //蓄分
		} else if($mode==3){
			$index = $this->withdraw(); //吐分
		} else {  //4
			$index = $this->generous(); //慷慨大送分
		}
		
		$reward = $this->getReward($bet_array, $index);
		
		//添加抽奖记录
		$insert_array['create_time'] = $timestamp;
		$insert_array['create_appid'] = "aid_".$account_id;
		$insert_array['update_time'] = $timestamp;
		$insert_array['update_appid'] = "aid_".$account_id;
		$insert_array['is_delete'] = G_CONST::IS_FALSE;
		
		$insert_array['account_id'] = $account_id;
		$insert_array['bet_count'] = $bet_count;
		$insert_array['result'] = $index;
		$insert_array['reward'] = $reward;
			
		$data_id = $this->getInsertID($dealerDB,Act_SlotMachine, $insert_array);

		//房卡流水账
		$journal_ary['journal_type'] = Game_CONST::JournalType_Disburse;
		$journal_ary['account_id'] = $account_id;
		$journal_ary['object_type'] = Game_CONST::ObjectType_SlotMachine;
		$journal_ary['object_id'] = $data_id;
		$journal_ary['ticket_count'] = $bet_count;
		$journal_ary['extra'] = "";
		$this->updateRoomTicketJournal($journal_ary,$dealerDB);
		
		if($reward > 0)
		{
			//将房卡添加到自己账户
			$updateTicket_str = 'update_time='.$timestamp.',update_appid="aid_'.$account_id.'",ticket_count=ticket_count+'.$reward;
			$updateTicket_where = 'account_id='.$account_id;
			$updateTicket_query = $this->changeNodeValue($dealerDB,Room_Ticket,$updateTicket_str,$updateTicket_where);

			//房卡流水账
			 $journal_ary['journal_type'] = Game_CONST::JournalType_Income;
			 $journal_ary['account_id'] = $account_id;
			 $journal_ary['object_type'] = Game_CONST::ObjectType_SlotMachine;
			 $journal_ary['object_id'] = $data_id;
			 $journal_ary['ticket_count'] = $reward;
			 $journal_ary['extra'] = "";
			 $this->updateRoomTicketJournal($journal_ary,$dealerDB);
		}
		
		$result['result'] = $index;
		$result['reward'] = $reward;
		$remain = $bet_count - $reward;
		$query = $this->changeNodeValue($dealerDB,Act_SlotMachine_Mode,'deposit=deposit+'.$bet_count.", reward=reward+".$reward.",last_remain=remain, remain=remain+".$remain ,'id=1');
		
		return array('result'=>OPT_CONST::SUCCESS,'data'=>$result,'result_message'=>"水果机抽奖");
	}

	public function queryMachineMode($dealerDB)
	{
		$sql = 'select deposit,reward,remain,last_remain,mode from  '.Act_SlotMachine_Mode.'  where id = 1';
		$query = $this->getDataBySql($dealerDB,1,$sql);
		if(is_array($query) && isset($query['last_remain']) && isset($query['remain'])){
			//print_r($query);
			$mode = $query['mode'];
			//$deposit = $query['deposit'];
			//$reward = $query['reward'];
			$remain = $query['remain'];
			$last_remain = $query['last_remain'];
			//$remain = $deposit - $reward;
			if($remain < -1000){
				if($mode != 1){
					$query = $this->changeNodeValue($dealerDB,Act_SlotMachine_Mode,'mode=1','id=1');
					return 1;
				}

			} else if($remain >1000){
				if($mode != 4){
					$query = $this->changeNodeValue($dealerDB,Act_SlotMachine_Mode,'mode=4','id=1');
					return 4;
				}
			}

			if($last_remain < 0 && 0 < $remain || $last_remain > 0 && 0 > $remain ){
				if($mode == 1 || $mode == 4){
					$new_mode = array_rand(array($mode=>7,2=>7,3=>7));
					if($mode != $new_mode){
						$query = $this->changeNodeValue($dealerDB,Act_SlotMachine_Mode,'mode='.$new_mode,'id=1');
						return $new_mode;
					}
				}
			}
			return $mode;
		} else {
			return mt_rand(2,3);
		}
	}

}