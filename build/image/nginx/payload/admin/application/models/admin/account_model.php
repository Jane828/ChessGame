<?php

include_once __DIR__.'/common_model.php';		//加载数据库操作类
class Account_Model extends Admin_Common_Model
{
	public function __construct()
	{
		header('Content-Type: text/html; charset=utf-8'); 
		parent::__construct();
	}
	
	public function statisticsAccount($dealer_num,$game_type,$account_id)
	{
		$DelaerConst = "Dealer_".$dealer_num;
		$dealerDB = $DelaerConst::DBConst_Name;	

		$win_score = 0;
		$lose_score = 0;
		$count = 0;

		$date_ary = array();


		echo "用户$account_id:";
		echo "<br><br>";

		$account_where = 'game_type='.$game_type.' and is_delete=0';
		$account_sql = 'select board,create_time,room_id from room_scoreboard where '.$account_where.'';
		$account_query = $this->getDataBySql($dealerDB,0,$account_sql);
		if(is_array($account_query) && count($account_query) > 0 )
		{
			foreach($account_query as $item)
			{
				$create_time = $item['create_time'];
				$room_id = $item['room_id'];

				$board_ary = json_decode($item['board'],true);

				if(!is_array($board_ary))
				{
					continue;
				}
				foreach($board_ary as $a_id => $value)
				{
					if($a_id == $account_id)
					{
						echo "".date("m-d H:i:s",$create_time).' 房间'.$room_id.' 胜负：'.$value;
						echo "<br>";

						$date = date("Y-m-d",$create_time);
						if(!isset($date_ary[$date]))
						{
							$date_ary[$date] = 0;
						}
						$date_ary[$date] += $value;

						$count ++;
						if($value > 0)
						{
							$win_score += $value;
						}
						else
						{
							$lose_score += $value;
						}
						continue;
					}
				}
			}
		}

		echo "<br><br>";
		foreach($date_ary as $d=>$v)
		{
			echo $d." 胜负：".$v;
			echo "<br>";
		}


		echo "<br><br>";
		echo "参与局数:".$count;
		echo "<br>";
		echo "赢了:".$win_score;
		echo "<br>";
		echo "输了:".abs($lose_score);
		echo "<br>";
		echo "总计:".($win_score - abs($lose_score));

		return ;
	}


	/*
		添加代理商
	*/
	public function addDealerOpt($arrData)
	{
		$result = array();
		$timestamp = time();

		if(!isset($arrData['dealer_id']) || $arrData['dealer_id'] === "" )
		{
			log_message('error', "function(addDealerOpt):lack of dealer_id"." in file".__FILE__." on Line ".__LINE__);
			return $this->missingPrameterArr("dealer_id");
		}
		if(!isset($arrData['name']) || $arrData['name'] === "" )
		{
			log_message('error', "function(addDealerOpt):lack of name"." in file".__FILE__." on Line ".__LINE__);
			return $this->missingPrameterArr("name");
		}
		if(!isset($arrData['account'])|| $arrData['account'] === "")
		{
			log_message('error', "function(addDealerOpt):lack of account"." in file".__FILE__." on Line ".__LINE__);
			return $this->missingPrameterArr("account");
		}
		if(!isset($arrData['passwd'])|| $arrData['passwd'] === "")
		{
			log_message('error', "function(addDealerOpt):lack of passwd"." in file".__FILE__." on Line ".__LINE__);
			return $this->missingPrameterArr("passwd");
		}
		if(!isset($arrData['payment_type'])|| $arrData['payment_type'] === "")
		{
			log_message('error', "function(addDealerOpt):lack of payment_type"." in file".__FILE__." on Line ".__LINE__);
			return $this->missingPrameterArr("payment_type");
		}

		$dealerDB = "admin";
		$account_id = $_SESSION['LoginAdminID'];

		$dealer_id = $arrData['dealer_id'];
		$name = $arrData['name'];
		$account = $arrData['account'];
		$clear_pwd = $arrData['passwd'];
		$passwd = md5($arrData['passwd']);
		$payment_type = $arrData['payment_type'];	//1代收费，2自运营

		if($dealer_id == -1)
		{
			$dealer_where = 'account="'.$account.'" and is_delete=0';
			$dealer_sql = 'select dealer_num,name,account,passwd,payment_type from '.D_Dealer_Account.' where '.$dealer_where;
			$dealer_query = $this->getDataBySql($dealerDB,1,$dealer_sql);
			if($dealer_query != DB_CONST::DATA_NONEXISTENT)
			{
				return array('result'=>OPT_CONST::FAILED,'data'=>$result,'result_message'=>"账号已存在");
			}

			//添加充值记录
	        $account_array['create_time'] = $timestamp;
	        $account_array['create_appid'] = "admin".$account_id;
	        $account_array['update_time'] = $timestamp;
	        $account_array['update_appid'] = "admin".$account_id;
	        $account_array['is_delete'] = 0;
	        $account_array['name'] = $name;
	        $account_array['account'] = $account;
	        $account_array['clear_pwd'] = $clear_pwd;
	        $account_array['passwd'] = $passwd;
	        $account_array['payment_type'] = $payment_type;
	        $account_array['dealer_num'] = "-1";

	        $dealer_id = $this->getInsertID($dealerDB,D_Dealer_Account, $account_array);
        }
        else
        {
        	$dealer_where = 'account="'.$account.'" and is_delete=0 and dealer_id!='.$dealer_id;
			$dealer_sql = 'select dealer_num,name,account,passwd,payment_type from '.D_Dealer_Account.' where '.$dealer_where;
			$dealer_query = $this->getDataBySql($dealerDB,1,$dealer_sql);
			if($dealer_query != DB_CONST::DATA_NONEXISTENT)
			{
				return array('result'=>OPT_CONST::FAILED,'data'=>$result,'result_message'=>"账号已存在");
			}

			$account_array['update_time'] = $timestamp;
	        $account_array['update_appid'] = "admin";
	        $account_array['is_delete'] = 0;
	        $account_array['name'] = $name;
	        $account_array['account'] = $account;
	        $account_array['clear_pwd'] = $clear_pwd;
	        $account_array['passwd'] = $passwd;
	        $account_array['payment_type'] = $payment_type;

	        $query = $this->updateFunc($dealerDB,"dealer_id",$dealer_id,D_Dealer_Account,$account_array);
        }

	    return array('result'=>OPT_CONST::SUCCESS,'data'=>$result,'result_message'=>"操作成功");
	}

	/*
		删除代理商
	*/
	public function delDealerOpt($arrData)
	{
		$result = array();
		$timestamp = time();

		if(!isset($arrData['dealer_id']) || $arrData['dealer_id'] === "" )
		{
			log_message('error', "function(delDealerOpt):lack of dealer_id"." in file".__FILE__." on Line ".__LINE__);
			return $this->missingPrameterArr("dealer_id");
		}

		$dealerDB = "admin";
		$account_id = $_SESSION['LoginAdminID'];

		$dealer_id = $arrData['dealer_id'];

		$updateTicket_str = 'update_time='.$timestamp.',update_appid="adminid_'.$account_id.'",is_delete=1';
		$updateTicket_where = 'dealer_id="'.$dealer_id.'"';
		$updateTicket_query = $this->changeNodeValue($dealerDB,D_Account,$updateTicket_str,$updateTicket_where);

		return array('result'=>OPT_CONST::SUCCESS,'data'=>$result,'result_message'=>"操作成功");	
	}

	
	/*
		获取代理商列表
	*/
	public function getDealerList($arrData)
	{
		$result = array();
		$timestamp = time();

		$dealerDB = "admin";

		if(!isset($arrData['keyword']))
		{
			log_message('error', "function(getDealerList):lack of keyword"." in file".__FILE__." on Line ".__LINE__);
			return $this->missingPrameterArr("keyword");
		}

		$keyword = $arrData['keyword'];
		

		$dealer_where = 'is_delete=0';
		if($keyword != "")
		{
			$dealer_where .= ' and name like "%'.$keyword.'%"';
		}

		$dealer_sql = 'select dealer_id,dealer_num,name,account,passwd,clear_pwd from '.D_Dealer_Account.' where '.$dealer_where.' order by create_time desc';
		$dealer_query = $this->getDataBySql($dealerDB,0,$dealer_sql);
		if($dealer_query != DB_CONST::DATA_NONEXISTENT)
		{
			$result = $dealer_query;
		}

		return array('result'=>OPT_CONST::SUCCESS,'data'=>$result,'result_message'=>"获取代理商列表");
	}
	

	/*
		充值
	*/
	public function dealerRechargeOpt($arrData)
	{
		$result = array();
		$timestamp = time();

		if(!isset($arrData['dealer_num']) || $arrData['dealer_num'] === "" )
		{
			log_message('error', "function(dealerRechargeOpt):lack of dealer_num"." in file".__FILE__." on Line ".__LINE__);
			return $this->missingPrameterArr("dealer_num");
		}
		if(!isset($arrData['ticket_count'])|| $arrData['ticket_count'] === "")
		{
			log_message('error', "function(dealerRechargeOpt):lack of ticket_count"." in file".__FILE__." on Line ".__LINE__);
			return $this->missingPrameterArr("ticket_count");
		}
	
	
		$dealer_num = $arrData['dealer_num'];
		$DelaerConst = "Dealer_".$dealer_num;

		$account_id = $_SESSION['LoginAdminID'];

		if(!is_numeric($arrData['ticket_count']))
		{
			return array('result'=>OPT_CONST::FAILED,'data'=>$result,'result_message'=>"充值数量必须为正整数");
		}

		if($dealer_num == "-1" || !class_exists($DelaerConst))
		{
			return array('result'=>OPT_CONST::FAILED,'data'=>$result,'result_message'=>"代理商数据库未建立");
		}

        $dealerDB = $DelaerConst::DBConst_Name;	

        //$account_id = -1;
        $ticket_count = (int)$arrData['ticket_count'];
        

        //添加充值记录
        $recharge_array['create_time'] = $timestamp;
        $recharge_array['create_appid'] = "admin".$account_id;
        $recharge_array['update_time'] = $timestamp;
        $recharge_array['update_appid'] = "admin".$account_id;
        $recharge_array['is_delete'] = 0;
        $recharge_array['ticket_count'] = $ticket_count;
        $recharge_array['account_id'] = $account_id;
        $recharge_id = $this->getInsertID($dealerDB,D_Recharge, $recharge_array);

        $from_timestamp = strtotime(date("Y-m",time()));
        $sql = 'select sum(income) as pre_recharge_amount from '.D_Journal.' where object_type='.Game_CONST::ObjectType_Recharge.' and create_time >'.$from_timestamp.' and is_delete=0';

        $pre_recharge_amount = 0;
        $query = $this->getDataBySql($dealerDB,1,$sql);
        if($query != DB_CONST::DATA_NONEXISTENT)
        {
        	$pre_recharge_amount = $query['pre_recharge_amount'];
        }

        //房卡库存流水账
		$i_journal_ary['journal_type'] = Game_CONST::JournalType_Income;
		$i_journal_ary['account_id'] = $account_id;
		$i_journal_ary['object_type'] = Game_CONST::DObjectType_Recharge;
		$i_journal_ary['object_id'] = $recharge_id;
		$i_journal_ary['ticket_count'] = $ticket_count;
		$i_journal_ary['extra'] = "";
		$this->updateInventoryJournal($i_journal_ary,$dealerDB);

		$updateTicket_str = 'update_time='.$timestamp.',update_appid="adminid_'.$account_id.'",inventory_count=inventory_count+'.$ticket_count;
		$updateTicket_where = 'dealer_num="'.$dealer_num.'"';
		$updateTicket_query = $this->changeNodeValue($dealerDB,D_Account,$updateTicket_str,$updateTicket_where);


        //赠送条件
        /*
		$total_reward = 0;
		$rule_sql = 'select recharge_amount,reward_amount from '.D_Reward_Rule.' where is_delete=0';
		$rule_query = $this->getDataBySql("admin",0,$rule_sql);
		if($rule_query != DB_CONST::DATA_NONEXISTENT)
		{
			foreach ($rule_query as $item) {
				if($item['recharge_amount'] > $pre_recharge_amount && $pre_recharge_amount + $ticket_count >= $item['recharge_amount']){
					$total_reward += $item['reward_amount'];
				}
			}
		}
		if($total_reward > 0){
	        //房卡库存流水账
			$i_journal_ary['journal_type'] = Game_CONST::JournalType_Income;
			$i_journal_ary['account_id'] = $account_id;
			$i_journal_ary['object_type'] = Game_CONST::DObjectType_Reward;
			$i_journal_ary['object_id'] = $recharge_id;
			$i_journal_ary['ticket_count'] = $total_reward;
			$i_journal_ary['extra'] = "房卡赠送";
			$this->updateInventoryJournal($i_journal_ary,$dealerDB);

			$updateTicket_str = 'update_time='.$timestamp.',update_appid="adminid_'.$account_id.'",inventory_count=inventory_count+'.$total_reward;
			$updateTicket_where = 'dealer_num="'.$dealer_num.'"';
			$updateTicket_query = $this->changeNodeValue($dealerDB,D_Account,$updateTicket_str,$updateTicket_where);
		}
		*/
		
		return array('result'=>OPT_CONST::SUCCESS,'data'=>$result,'result_message'=>"代理商充值记录");
    }



    /*
		修改商城
	*/
	public function updateGoodsList($arrData)
	{
		$result = array();
		$timestamp = time();

		if(!isset($arrData['dealer_num']) || $arrData['dealer_num'] === "" )
		{
			log_message('error', "function(updateGoodsList):lack of dealer_num"." in file".__FILE__." on Line ".__LINE__);
			return $this->missingPrameterArr("dealer_num");
		}
		if(!isset($arrData['goodsList'])|| !is_array($arrData['goodsList']))
		{
			log_message('error', "function(updateGoodsList):lack of goodsList"." in file".__FILE__." on Line ".__LINE__);
			return $this->missingPrameterArr("goodsList");
		}
	
		$dealer_num = $arrData['dealer_num'];
		$DelaerConst = "Dealer_".$dealer_num;
		$dealerDB = $DelaerConst::DBConst_Name;	

		$account_id = $_SESSION['LoginAdminID'];

		$goodsList = $arrData['goodsList'];

		//var_dump($goodsList);exit;

		$update_goodsAry = array();
		foreach($goodsList as $goodsItem)
		{
			if( $goodsItem['title'] == "" && $goodsItem['price'] == "" && $goodsItem['ticket_count'] == "")
			{
				continue;
			}
			if($goodsItem['title'] == "")
			{
				return array('result'=>OPT_CONST::FAILED,'data'=>$result,'result_message'=>"请填写商品内容");
			}
			else if($goodsItem['price'] == "")
			{
				return array('result'=>OPT_CONST::FAILED,'data'=>$result,'result_message'=>"请填写商品价格");
			}
			else if($goodsItem['ticket_count'] == "")
			{
				return array('result'=>OPT_CONST::FAILED,'data'=>$result,'result_message'=>"请填写商品数量");
			}
			else
			{
				$update_goodsAry[] = $goodsItem;
			}
		}


		//修改商品
        $del_array['is_delete'] = 1;
        $del_query = $this->updateFunc($dealerDB,"is_delete","0",Payment_Goods,$del_array);

        foreach($update_goodsAry as $item)
        {
        	$goods_where = 'is_delete=1';
			$goods_sql = 'select goods_id from '.Payment_Goods.' where '.$goods_where.' order by goods_id asc';
			$goods_query = $this->getDataBySql($dealerDB,1,$goods_sql);
			if($goods_query == DB_CONST::DATA_NONEXISTENT)
			{
				$insert_array['is_delete'] = 0;
				$insert_array['title'] = $item['title'];
				$insert_array['price'] = $item['price'];
				$insert_array['ticket_count'] = $item['ticket_count'];

				$goods_id = $this->getInsertID($dealerDB,Payment_Goods, $insert_array);
			}
			else
			{
				$goods_id = $goods_query['goods_id'];

				$update_array['is_delete'] = 0;
				$update_array['title'] = $item['title'];
				$update_array['price'] = $item['price'];
				$update_array['ticket_count'] = $item['ticket_count'];
        		$update_query = $this->updateFunc($dealerDB,"goods_id",$goods_id,Payment_Goods,$update_array);
			}
        }

		return array('result'=>OPT_CONST::SUCCESS,'data'=>$result,'result_message'=>"修改成功");
    }



	public function updateDealerDailyData($arrData)
	{
		$timestamp = time();
		$result = array();
		
		if(!isset($arrData['from']) )
		{
			log_message('error', "function(getDealerDailyData):lack of from"." in file".__FILE__." on Line ".__LINE__);
			return $this->missingPrameterArr("from");
		}
		if(!isset($arrData['to']))
		{
			log_message('error', "function(getDealerDailyData):lack of to"." in file".__FILE__." on Line ".__LINE__);
			return $this->missingPrameterArr("to");
		}
		if(!isset($arrData['dealer_num']) || $arrData['dealer_num'] === G_CONST::EMPTY_STRING)
		{
			log_message('error', "function(getDealerDailyData):lack of dealer_num"." in file".__FILE__." on Line ".__LINE__);
			return $this->missingPrameterArr("dealer_num");
		}
		$dealer_num = $arrData['dealer_num'];
		
		$DelaerConst = "Dealer_".$dealer_num;
		$dealerDB = $DelaerConst::DBConst_Name;

		$from = $arrData['from'];
		$to = $arrData['to'];

		if($from == "")
		{
			$from = date("Y-m-d");
		}
		if($to == "")
		{
			$to = date("Y-m-d");
		}

		$from_timestamp = strtotime($from);
		$to_timestamp = strtotime($to);

		$day_count = (($to_timestamp - $from_timestamp)/86400) + 1;



		$list_where = 'is_delete=0';
		$list_sql = 'select game_type from '.Game_List.' where '.$list_where.' order by game_type asc';
		$list_query = $this->getDataBySql($dealerDB,0,$list_sql);
		if($list_query == DB_CONST::DATA_NONEXISTENT)
		{
			echo "无游戏";
			return;
		}

		for($i=1;$i<=$day_count;$i++)
		{
			$day_from = $from_timestamp + ($i - 1)*86400;
			$day_to = $day_from + 86400;

			echo "".date('Y-m-d',$day_from).":";
			echo "<br>";

			$day_timestamp = $day_from;

			foreach($list_query as $list_item)
			{
				$game_type = $list_item['game_type'];

				$count = 0;
				//获取游戏开局数量
				$count_sql = 'select count(board_id) as count from '.Room_ScoreBoard.' where create_time>='.$day_from.' and create_time<'.$day_to.' and game_type='.$game_type.' and is_delete=0';
				$count_query = $this->getDataBySql($dealerDB,1,$count_sql);
				if($count_query != DB_CONST::DATA_NONEXISTENT)
				{
					$count = $count_query['count'];
				}

				//判断是否存在
				$summary_sql = 'select data_id from '.Summary_Dealer.' where day_timestamp ='.$day_timestamp.' and game_type='.$game_type.' and is_delete=0 limit 1';
				$summary_query = $this->getDataBySql($dealerDB,1,$summary_sql);
				if($summary_query != DB_CONST::DATA_NONEXISTENT)
				{
					$data_id = $summary_query['data_id'];

					$update_array['update_time'] = $timestamp;
					$update_array['total_count'] = $count;
					$update_query = $this->updateFunc($dealerDB,"data_id",$data_id,Summary_Dealer,$update_array);
				}
				else if($count > 0)
				{
					$array['create_time'] = $timestamp;
					$array['create_appid'] = "gameprocessor";
					$array['update_time'] = $timestamp;
					$array['update_appid'] = "gameprocessor";
					$array['is_delete'] = 0;
					$array['day_timestamp'] = $day_timestamp;
					$array['game_type'] = $game_type;
					$array['total_count'] = $count;
					$this->getInsertID($dealerDB,Summary_Dealer, $array);
				}

				
				echo "游戏：".$game_type." 开局数：".$count;
				echo "<br>";
			}

			echo "<br>";
			echo "<br>";
		}
		return ;
	}




}