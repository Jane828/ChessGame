<?php

include_once __DIR__.'/common_model.php';		//加载数据库操作类
class Guild_Model extends Guild_Common_Model
{
	public function __construct()
	{
		header('Content-Type: text/html; charset=utf-8'); 
		parent::__construct();
	}

	

	/*
		获取副会长结算记录
	*/
	public function getViceBalanceHistory($arrData)
	{
		$result = array();
		$timestamp = time();

		if(!isset($arrData['dealer_num']) || $arrData['dealer_num'] === "" )
		{
			log_message('error', "function(getViceBalanceHistory):lack of dealer_num"." in file".__FILE__." on Line ".__LINE__);
			return $this->missingPrameterArr("dealer_num");
		}
		if(!isset($arrData['group_id']) || $arrData['group_id'] === "" )
		{
			log_message('error', "function(getViceBalanceHistory):lack of group_id"." in file".__FILE__." on Line ".__LINE__);
			return $this->missingPrameterArr("group_id");
		}
		if(!isset($arrData['account_id']) || $arrData['account_id'] === "" )
		{
			log_message('error', "function(getViceBalanceHistory):lack of account_id"." in file".__FILE__." on Line ".__LINE__);
			return $this->missingPrameterArr("account_id");
		}
		if(!isset($arrData['page']))
		{
			log_message('error', "function(getViceBalanceHistory):lack of page"." in file".__FILE__." on Line ".__LINE__);
			return $this->missingPrameterArr("page");
		}
		

		$dealer_num = $arrData['dealer_num'];
		$group_id = $arrData['group_id'];
		$account_id = $arrData['account_id'];
		$DelaerConst = "Dealer_".$dealer_num;
		$dealerDB = $DelaerConst::DBConst_Name;	

		$page = $arrData['page'];
		if($page == 0 || $page == "")
		{
			$page = 1;
		}
		$limit = 20;
		$offset = ($page - 1) * $limit;
		$sum_page = 1;
		$sum_count = 0;

		$where = 'group_id='.$group_id.' and vice_president='.$account_id.' and object_type=2 and is_delete=0';
		$list_sql = 'select * from '.Guild_ViceBalance.' where '.$where.' order by create_time desc limit '.$offset.','.$limit;

		$list_query = $this->getDataBySql($dealerDB,0,$list_sql);
		if($list_query != DB_CONST::DATA_NONEXISTENT)
		{
			$count_sql = 'select count(balance_id) as count from '.Guild_ViceBalance.' where '.$where;
			$count_query = $this->getDataBySql($dealerDB,1,$count_sql);
			$sum_page = ceil($count_query['count'] / $limit);
			$sum_count = $count_query['count'];

			foreach($list_query as $list_item)
			{
				$create_time = $list_item['create_time'];
				$amount = $list_item['disburse'];
				$vice_president = $list_item['vice_president'];

				//$sql = 'select nickname from '.WX_Account.' where account_id='.$vice_president;
				//$query = $this->getDataBySql($dealerDB,1,$sql);

				//$array['nickname'] = $query['nickname'];
				$array['amount'] = $amount;
				$array['create_time'] = date('Y-m-d H:i:s',$create_time);

				$result[] = $array;
			}
		}
		return array('result'=>OPT_CONST::SUCCESS,'data'=>$result, 'sum_page'=>$sum_page,'sum_count'=>$sum_count,'page'=>$page,'result_message'=>"获取副会长结算记录");
	}
		

	/*
		清零副会长余额
	*/
	public function clearViceBalanceOpt($arrData)
	{
		$result = array();
		$timestamp = time();

		if(!isset($arrData['dealer_num']) || $arrData['dealer_num'] === "" )
		{
			log_message('error', "function(withdrawOpt):lack of dealer_num"." in file".__FILE__." on Line ".__LINE__);
			return $this->missingPrameterArr("dealer_num");
		}
		if(!isset($arrData['my_aid'])|| $arrData['my_aid'] === "")
		{
			log_message('error', "function(withdrawOpt):lack of my_aid"." in file".__FILE__." on Line ".__LINE__);
			return $this->missingPrameterArr("my_aid");
		}
		if(!isset($arrData['group_id'])|| $arrData['group_id'] === "")
		{
			log_message('error', "function(withdrawOpt):lack of group_id"." in file".__FILE__." on Line ".__LINE__);
			return $this->missingPrameterArr("group_id");
		}
		if(!isset($arrData['vice_president'])|| $arrData['vice_president'] === "")
		{
			log_message('error', "function(withdrawOpt):lack of vice_president"." in file".__FILE__." on Line ".__LINE__);
			return $this->missingPrameterArr("vice_president");
		}

		$group_id = $arrData['group_id'];
		$my_aid = (int)$arrData['my_aid'];
		$vice_president = $arrData['vice_president'];
	
		$dealer_num = $arrData['dealer_num'];
		$DelaerConst = "Dealer_".$dealer_num;

		if($dealer_num == "-1" || !class_exists($DelaerConst))
		{
			return array('result'=>OPT_CONST::FAILED,'data'=>$result,'result_message'=>"代理商数据库未建立");
		}

        $dealerDB = $DelaerConst::DBConst_Name;	

        $sql = 'select balance from '.Guild_ViceBalance.' where group_id = '.$group_id.' and vice_president='.$vice_president.' and is_delete=0 order by create_time desc';
        $query = $this->getDataBySql($dealerDB,1,$sql);
        $amount = $query['balance'];
        $result = array('amount'=>$amount);
		if($amount > 0){
	        //平衡
	        $balance_array['create_time'] = $timestamp;
	        $balance_array['create_appid'] = "president".$my_aid;
	        $balance_array['update_time'] = $timestamp;
	        $balance_array['update_appid'] = "president".$my_aid;
	        $balance_array['is_delete'] = 0;
			$balance_array['group_id'] = $group_id;
			$balance_array['vice_president'] = $vice_president;
			$balance_array['object_type'] = 2;	//余额清零结算
			$balance_array['object_id'] = -1;
			$balance_array['disburse'] = $amount;
			$balance_array['balance'] = 0;
			$balance_array['abstract'] = "清零副会长余额";
			$balance_id = $this->getInsertID($dealerDB,Guild_ViceBalance, $balance_array);
			return array('result'=>OPT_CONST::SUCCESS,'data'=>$result,'result_message'=>"副会长余额清零成功");
		} else {
			return array('result'=>OPT_CONST::FAILED,'data'=>$result,'result_message'=>"副会长余额过少");
		}
    }

	/*
		会长提现
	*/
	public function withdrawOpt($arrData)
	{
		$result = array();
		$timestamp = time();

		if(!isset($arrData['dealer_num']) || $arrData['dealer_num'] === "" )
		{
			log_message('error', "function(withdrawOpt):lack of dealer_num"." in file".__FILE__." on Line ".__LINE__);
			return $this->missingPrameterArr("dealer_num");
		}
		if(!isset($arrData['my_aid']) || $arrData['my_aid'] === "" )
		{
			log_message('error', "function(withdrawOpt):lack of my_aid"." in file".__FILE__." on Line ".__LINE__);
			return $this->missingPrameterArr("my_aid");
		}
		if(!isset($arrData['group_id'])|| $arrData['group_id'] === "")
		{
			log_message('error', "function(withdrawOpt):lack of group_id"." in file".__FILE__." on Line ".__LINE__);
			return $this->missingPrameterArr("group_id");
		}
		if(!isset($arrData['amount'])|| $arrData['amount'] === "")
		{
			log_message('error', "function(withdrawOpt):lack of amount"." in file".__FILE__." on Line ".__LINE__);
			return $this->missingPrameterArr("amount");
		}
		if(!isset($arrData['weixin_account'])|| $arrData['weixin_account'] === "")
		{
			log_message('error', "function(withdrawOpt):lack of weixin_account"." in file".__FILE__." on Line ".__LINE__);
			return $this->missingPrameterArr("weixin_account");
		}
		if(!isset($arrData['phone'])|| $arrData['phone'] === "")
		{
			log_message('error', "function(withdrawOpt):lack of phone"." in file".__FILE__." on Line ".__LINE__);
			return $this->missingPrameterArr("phone");
		}

		$group_id = $arrData['group_id'];
		$my_aid = $arrData['my_aid'];
		$amount = (int)$arrData['amount'];
		$weixin_account = $arrData['weixin_account'];
		$phone = $arrData['phone'];
	
		$dealer_num = $arrData['dealer_num'];
		$DelaerConst = "Dealer_".$dealer_num;

		//$account_id = $_SESSION['LoginAdminID'];

		if($dealer_num == "-1" || !class_exists($DelaerConst))
		{
			return array('result'=>OPT_CONST::FAILED,'data'=>$result,'result_message'=>"代理商数据库未建立");
		}

        $dealerDB = $DelaerConst::DBConst_Name;	

        $result = array('amount'=>$amount);
		if($amount < 2)
		{
			return array('result'=>OPT_CONST::FAILED,'data'=>$result,'result_message'=>"提现金额太少");
		} else {
			$sql = 'select balance from '.Guild_Balance.' where group_id = '.$group_id.' and is_delete=0 order by create_time desc';
			$query = $this->getDataBySql($dealerDB,1,$sql);
			$balance = $query['balance'];
			if($amount > $balance){
				return array('result'=>OPT_CONST::FAILED,'data'=>$result,'result_message'=>"提现金额超过余额");
			}
		}

        //$account_id = -1;

        //添加提现记录
        $withdraw_array['create_time'] = $timestamp;
        $withdraw_array['create_appid'] = "president".$my_aid;
        $withdraw_array['update_time'] = $timestamp;
        $withdraw_array['update_appid'] = "president".$my_aid;
        $withdraw_array['is_delete'] = 0;
        $withdraw_array['group_id'] = $group_id;
        $withdraw_array['amount'] = $amount;
        $withdraw_array['weixin_account'] = $weixin_account;
        $withdraw_array['phone'] = $phone;
        $withdraw_array['status'] = 0;

        $withdraw_id = $this->getInsertID($dealerDB,Guild_Withdraw, $withdraw_array);

        //平衡
        $balance_array['create_time'] = $timestamp;
        $balance_array['create_appid'] = "president".$my_aid;
        $balance_array['update_time'] = $timestamp;
        $balance_array['update_appid'] = "president".$my_aid;
        $balance_array['is_delete'] = 0;
		$balance_array['group_id'] = $group_id;
		$balance_array['object_type'] = 2;	//会长提现
		$balance_array['object_id'] = $withdraw_id;
		$balance_array['disburse'] = $amount;
		$balance_array['balance'] = $balance - $amount;
		$balance_array['abstract'] = "会长提现";
		$balance_id = $this->getInsertID($dealerDB,Guild_Balance, $balance_array);

		return array('result'=>OPT_CONST::SUCCESS,'data'=>$result,'result_message'=>"会长提现");
    }


	/*
		获取公会提现列表
	*/
	public function getWithdrawList($arrData)
	{
		$result = array();
		$timestamp = time();

		if(!isset($arrData['dealer_num']) || $arrData['dealer_num'] === "" )
		{
			log_message('error', "function(getWithdrawList):lack of dealer_num"." in file".__FILE__." on Line ".__LINE__);
			return $this->missingPrameterArr("dealer_num");
		}
		if(!isset($arrData['group_id']) || $arrData['group_id'] === "" )
		{
			log_message('error', "function(getWithdrawList):lack of group_id"." in file".__FILE__." on Line ".__LINE__);
			return $this->missingPrameterArr("group_id");
		}
		if(!isset($arrData['page']))
		{
			log_message('error', "function(getWithdrawList):lack of page"." in file".__FILE__." on Line ".__LINE__);
			return $this->missingPrameterArr("page");
		}

		if($arrData['dealer_num'] == "-1")
		{
			return array('result'=>OPT_CONST::SUCCESS,'data'=>$result,'result_message'=>"获取公会提现列表");
		}
		$dealer_num = $arrData['dealer_num'];
		$group_id = $arrData['group_id'];
		$DelaerConst = "Dealer_".$dealer_num;
		if($dealer_num == "-1" || !class_exists($DelaerConst))
		{
			return array('result'=>OPT_CONST::SUCCESS,'data'=>$result,'result_message'=>"获取公会提现列表");
		}

		$page = $arrData['page'];
		if($page == 0 || $page == "")
		{
			$page = 1;
		}
		$limit = 20;
		$offset = ($page - 1) * $limit;
		$sum_page = 1;

		$where = 'group_id='.$group_id.' and is_delete=0';
		$list_sql = 'select * from '.Guild_Withdraw.' where '.$where.' order by status desc,create_time desc limit '.$offset.','.$limit;

        $dealerDB = $DelaerConst::DBConst_Name;	
		
		$list_query = $this->getDataBySql($dealerDB,0,$list_sql);
		if($list_query != DB_CONST::DATA_NONEXISTENT)
		{
			$count_sql = 'select count(withdraw_id) as count from '.Guild_Withdraw.' where '.$where;
			$count_query = $this->getDataBySql($dealerDB,1,$count_sql);
			$sum_page = ceil($count_query['count'] / $limit);

			foreach($list_query as $list_item)
			{
				$create_time = $list_item['create_time'];
				$weixin_account = $list_item['weixin_account'];
				$phone = $list_item['phone'];
				$status = $list_item['status'];
				$amount = $list_item['amount'];

				$array['amount'] = $amount;
				$array['create_time'] = date('Y-m-d H:i:s',$create_time);
				$array['phone'] =  $phone;
				$array['weixin_account'] = $weixin_account;
				$array['status'] = $status;

				$result[] = $array;
			}
		}
		return array('result'=>OPT_CONST::SUCCESS,'data'=>$result, 'sum_page'=>$sum_page,'page'=>$page,'result_message'=>"获取公会提现列表");
	}
	

	/*
		获公会成员列表
	*/
	public function getMember($arrData)
	{
		$result = array();
		$timestamp = time();

		if(!isset($arrData['dealer_num']) || $arrData['dealer_num'] === "" )
		{
			log_message('error', "function(getMember):lack of dealer_num"." in file".__FILE__." on Line ".__LINE__);
			return $this->missingPrameterArr("dealer_num");
		}
		if(!isset($arrData['group_id']) || $arrData['group_id'] === "" )
		{
			log_message('error', "function(getIncome):lack of group_id"." in file".__FILE__." on Line ".__LINE__);
			return $this->missingPrameterArr("group_id");
		}
		if(!isset($arrData['vice_president']) )
		{
			log_message('error', "function(getMember):lack of vice_president"." in file".__FILE__." on Line ".__LINE__);
			return $this->missingPrameterArr("vice_president");
		}
		if(!isset($arrData['page']))
		{
			log_message('error', "function(getIncome):lack of page"." in file".__FILE__." on Line ".__LINE__);
			return $this->missingPrameterArr("page");
		}

		if($arrData['dealer_num'] == "-1")
		{
			return array('result'=>OPT_CONST::SUCCESS,'data'=>$result,'result_message'=>"获公会成员列表");
		}
		$dealer_num = $arrData['dealer_num'];
		$group_id = $arrData['group_id'];
		$DelaerConst = "Dealer_".$dealer_num;
		if($dealer_num == "-1" || !class_exists($DelaerConst))
		{
			return array('result'=>OPT_CONST::SUCCESS,'data'=>$result,'result_message'=>"获公会成员列表");
		}

		$page = $arrData['page'];
		if($page == 0 || $page == "")
		{
			$page = 1;
		}
		$limit = 20;
		$offset = ($page - 1) * $limit;
		$sum_page = 1;

		$vice_president = $arrData['vice_president'];

		if($vice_president > 0){
			$where = 'group_id='.$group_id.' and vice_president='.$vice_president.' and is_delete=0';
		} else {
			$where = 'group_id='.$group_id.' and is_delete=0';
		}
		$list_sql = 'select * from '.Guild_Member.' where '.$where.' order by create_time asc limit '.$offset.','.$limit;

        $dealerDB = $DelaerConst::DBConst_Name;	
		
		$list_query = $this->getDataBySql($dealerDB,0,$list_sql);
		if($list_query != DB_CONST::DATA_NONEXISTENT)
		{
			$count_sql = 'select count(member_id) as count from '.Guild_Member.' where '.$where;
			$count_query = $this->getDataBySql($dealerDB,1,$count_sql);
			$sum_page = ceil($count_query['count'] / $limit);

			foreach($list_query as $list_item)
			{
				$account_id = $list_item['account_id'];
				$create_time = $list_item['create_time'];
				$vice_president = $list_item['vice_president'];

				$array['account_id'] = $account_id;
				$array['create_time'] = date('Y-m-d H:i:s',$create_time);
				$array['vice_president'] = $vice_president;

				$sql = 'select nickname from '.WX_Account.' where account_id='.$account_id;
				$query = $this->getDataBySql($dealerDB,1,$sql);
				$array['nickname'] = $query['nickname'];

				$sql = 'select ticket_count from '.Room_Ticket.' where account_id='.$account_id;
				$query = $this->getDataBySql($dealerDB,1,$sql);
				$array['ticket_count'] = $query['ticket_count'];

				$sql = 'select sum(price) as total_amount from '.Guild_CommissionRecord.' where group_id='.$group_id.' and customer='.$account_id. ' and is_delete=0';
				$query = $this->getDataBySql($dealerDB,1,$sql);
				$array['total_amount'] = $query['total_amount'] > 0 ? $query['total_amount'] : 0;


				$array['total_score'] = 0;
				$game_list_sql = 'select game_type,game_title from '.Game_List.' where is_delete=0 order by game_type asc';
				$game_list_query = $this->getDataBySql($dealerDB,0,$game_list_sql);
				if($game_list_query != DB_CONST::DATA_NONEXISTENT)
				{
					foreach($game_list_query as $list_item)
					{
						$game_type = $list_item['game_type'];

						$sql = 'select total_score from '.Room_GameScore.' where account_id='.$account_id.' and game_type='.$game_type. ' and is_delete=0 order by create_time desc';
						$query = $this->getDataBySql($dealerDB,1,$sql);
						$array['total_score'] += $query['total_score'] > 0 ? $query['total_score'] : 0;
					}
				}

				$result[] = $array;
			}
		}
		return array('result'=>OPT_CONST::SUCCESS,'data'=>$result, 'sum_page'=>$sum_page,'page'=>$page,'result_message'=>"获取公会成员列表");
	}


	/*
		获取公会收入信息
	*/
	public function getIncome($arrData)
	{
		$result = array();
		$timestamp = time();

		if(!isset($arrData['dealer_num']) || $arrData['dealer_num'] === "" )
		{
			log_message('error', "function(getIncome):lack of dealer_num"." in file".__FILE__." on Line ".__LINE__);
			return $this->missingPrameterArr("dealer_num");
		}
		if(!isset($arrData['group_id']) || $arrData['group_id'] === "" )
		{
			log_message('error', "function(getIncome):lack of group_id"." in file".__FILE__." on Line ".__LINE__);
			return $this->missingPrameterArr("group_id");
		}
		if(!isset($arrData['from'])|| $arrData['from'] === "")
		{
			log_message('error', "function(getIncome):lack of from"." in file".__FILE__." on Line ".__LINE__);
			return $this->missingPrameterArr("from");
		}
		if(!isset($arrData['to'])|| $arrData['to'] === "")
		{
			log_message('error', "function(getIncome):lack of to"." in file".__FILE__." on Line ".__LINE__);
			return $this->missingPrameterArr("to");
		}
		if(!isset($arrData['page']))
		{
			log_message('error', "function(getIncome):lack of page"." in file".__FILE__." on Line ".__LINE__);
			return $this->missingPrameterArr("page");
		}

		if($arrData['dealer_num'] == "-1")
		{
			return array('result'=>OPT_CONST::SUCCESS,'data'=>$result,'result_message'=>"获取公会收入信息");
		}
		$dealer_num = $arrData['dealer_num'];
		$group_id = $arrData['group_id'];
		$DelaerConst = "Dealer_".$dealer_num;
		if($dealer_num == "-1" || !class_exists($DelaerConst))
		{
			return array('result'=>OPT_CONST::SUCCESS,'data'=>$result,'result_message'=>"获取公会收入信息");
		}
        $dealerDB = $DelaerConst::DBConst_Name;	

        $page = $arrData['page'];
        if($page == 0 || $page == "")
        {
        	$page = 1;
        }

        $from_timestamp = strtotime($arrData['from']);
        $to_timestamp = strtotime($arrData['to']) + 86400;

        $limit = 20;
        $offset = ($page - 1) * $limit;
        $sum_page = 1;

        $result_dict = array();

        $sql = 'select sum(guild_commission) as total_income from '.Guild_CommissionRecord.' where group_id = '.$group_id.' and is_delete=0';
        $query = $this->getDataBySql($dealerDB,1,$sql);
        $result_dict['total_income'] = $query['total_income'];

        $sql = 'select balance from '.Guild_Balance.' where group_id = '.$group_id.' and is_delete=0 order by create_time desc';
        $query = $this->getDataBySql($dealerDB,1,$sql);
        $result_dict['balance'] = $query['balance'];


        $income_where = 'create_time>='.$from_timestamp.' and create_time<'.$to_timestamp.' and group_id = '.$group_id.' and is_delete=0';
       
        $income_sql = 'select * from '.Guild_CommissionRecord.' where '.$income_where.' order by create_time desc limit '.$offset.','.$limit;
        $game_query = $this->getDataBySql($dealerDB,0,$income_sql);
        if($game_query != DB_CONST::DATA_NONEXISTENT)
        {
        	$count_sql = 'select count(record_id) as count, sum(guild_commission) as income from '.Guild_CommissionRecord.' where '.$income_where;
        	$count_query = $this->getDataBySql($dealerDB,1,$count_sql);
        	$sum_page = ceil($count_query['count'] / $limit);

        	$result_dict['income'] = $count_query['income'];

        	$result_dict['detail_array'] = array();
        	foreach($game_query as $game_item)
        	{
        		$customer = $game_item['customer'];
        		$create_time = $game_item['create_time'];
        		$price = $game_item['price'];
        		$guild_commission = $game_item['guild_commission'];


        		$nickname = "";
        		$list_sql = "select nickname from ".WX_Account.' where account_id='.$customer;
        		$list_query = $this->getDataBySql($dealerDB,1,$list_sql);
        		if($list_query != DB_CONST::DATA_NONEXISTENT)
        		{
        			$nickname = $list_query['nickname'];
        		}

        		$array['nickname'] = $nickname;
        		$array['create_time'] = date('Y-m-d H:i:s',$create_time);
        		$array['price'] = $price;
        		$array['guild_commission'] = $guild_commission;

        		$result_dict['detail_array'][] = $array;
        	}
        }
        $result_dict['sum_page'] = $sum_page;
		$result_dict['sum_page'] = $page;

		return array('result'=>OPT_CONST::SUCCESS,'data'=>$result_dict,'result_message'=>"获取公会收入信息");
	}


	/*
		获取副会长名单
	*/
	public function getViceList($arrData)
	{
		$result = array();
		$timestamp = time();

		if(!isset($arrData['dealer_num']) || $arrData['dealer_num'] === "" )
		{
			log_message('error', "function(getViceList):lack of dealer_num"." in file".__FILE__." on Line ".__LINE__);
			return $this->missingPrameterArr("dealer_num");
		}
		if(!isset($arrData['group_id']) || $arrData['group_id'] === "" )
		{
			log_message('error', "function(getViceList):lack of group_id"." in file".__FILE__." on Line ".__LINE__);
			return $this->missingPrameterArr("group_id");
		}
	
		if($arrData['dealer_num'] == "-1")
		{
			return array('result'=>OPT_CONST::SUCCESS,'data'=>$result,'result_message'=>"获取副会长名单");
		}
		$dealer_num = $arrData['dealer_num'];
		$group_id = $arrData['group_id'];
		$DelaerConst = "Dealer_".$dealer_num;
		if($dealer_num == "-1" || !class_exists($DelaerConst))
		{
			return array('result'=>OPT_CONST::SUCCESS,'data'=>$result,'result_message'=>"获取副会长名单");
		}

		$where = 'group_id='.$group_id.' and level>=1 and is_delete=0';
		
		$list_sql = 'select * from '.Guild_Member.' where '.$where.' order by level desc,create_time asc ';

        $dealerDB = $DelaerConst::DBConst_Name;	
		
		$list_query = $this->getDataBySql($dealerDB,0,$list_sql);
		if($list_query != DB_CONST::DATA_NONEXISTENT)
		{
			foreach($list_query as $list_item)
			{
				$account_id = $list_item['account_id'];
				$array['level'] = $list_item['level'];
				$array['account_id'] = $account_id;
				$sql = 'select nickname from '.WX_Account.' where account_id='.$account_id;
				$query = $this->getDataBySql($dealerDB,1,$sql);
				$array['nickname'] = $query['nickname'];

				if($array['level']==2){
					$account_id = -1;
				}
				$sql = 'select count(member_id) as member_count from '.Guild_Member.' where group_id='.$group_id.' and vice_president='.$account_id. ' and is_delete=0';
				$query = $this->getDataBySql($dealerDB,1,$sql);
				$array['member_count'] = $query['member_count'] > 0 ? $query['member_count'] : 0;

				$result[] = $array;
			}
		}
		return array('result'=>OPT_CONST::SUCCESS,'data'=>$result, 'result_message'=>"获取副会长名单");
	}


	/*
		获取副会长列表
	*/
	public function getVicePresident($arrData)
	{
		$result = array();
		$timestamp = time();

		if(!isset($arrData['dealer_num']) || $arrData['dealer_num'] === "" )
		{
			log_message('error', "function(getVicePresident):lack of dealer_num"." in file".__FILE__." on Line ".__LINE__);
			return $this->missingPrameterArr("dealer_num");
		}
		if(!isset($arrData['group_id']) || $arrData['group_id'] === "" )
		{
			log_message('error', "function(getVicePresident):lack of group_id"." in file".__FILE__." on Line ".__LINE__);
			return $this->missingPrameterArr("group_id");
		}
		if(!isset($arrData['page']))
		{
			log_message('error', "function(getVicePresident):lack of page"." in file".__FILE__." on Line ".__LINE__);
			return $this->missingPrameterArr("page");
		}

		if($arrData['dealer_num'] == "-1")
		{
			return array('result'=>OPT_CONST::SUCCESS,'data'=>$result,'result_message'=>"获取副会长列表");
		}
		$dealer_num = $arrData['dealer_num'];
		$group_id = $arrData['group_id'];
		$DelaerConst = "Dealer_".$dealer_num;
		if($dealer_num == "-1" || !class_exists($DelaerConst))
		{
			return array('result'=>OPT_CONST::SUCCESS,'data'=>$result,'result_message'=>"获取副会长列表");
		}

		$page = $arrData['page'];
		if($page == 0 || $page == "")
		{
			$page = 1;
		}
		$limit = 20;
		$offset = ($page - 1) * $limit;
		$sum_page = 1;

		$where = 'group_id='.$group_id.' and level=1 and is_delete=0';
		
		$list_sql = 'select * from '.Guild_Member.' where '.$where.' order by create_time asc limit '.$offset.','.$limit;

        $dealerDB = $DelaerConst::DBConst_Name;	
		
		$list_query = $this->getDataBySql($dealerDB,0,$list_sql);
		if($list_query != DB_CONST::DATA_NONEXISTENT)
		{
			$count_sql = 'select count(member_id) as count from '.Guild_Member.' where '.$where;
			$count_query = $this->getDataBySql($dealerDB,1,$count_sql);
			$sum_page = ceil($count_query['count'] / $limit);

			foreach($list_query as $list_item)
			{
				$account_id = $list_item['account_id'];
				$create_time = $list_item['create_time'];

				$array['account_id'] = $account_id;
				$array['create_time'] = date('Y-m-d H:i:s',$create_time);

				$sql = 'select nickname from '.WX_Account.' where account_id='.$account_id;
				$query = $this->getDataBySql($dealerDB,1,$sql);
				$array['nickname'] = $query['nickname'];

				$sql = 'select count(member_id) as member_count from '.Guild_Member.' where group_id='.$group_id.' and vice_president='.$account_id. ' and is_delete=0';
				$query = $this->getDataBySql($dealerDB,1,$sql);
				$array['member_count'] = $query['member_count'] > 0 ? $query['member_count'] : 0;

				$result[] = $array;
			}
		}
		return array('result'=>OPT_CONST::SUCCESS,'data'=>$result, 'sum_page'=>$sum_page,'page'=>$page,'result_message'=>"获取副会长列表");
	}


	/*
		获取副会长提成
	*/
	public function getViceCommission($arrData)
	{
		$result = array();
		$timestamp = time();

		if(!isset($arrData['dealer_num']) || $arrData['dealer_num'] === "" )
		{
			log_message('error', "function(getViceCommission):lack of dealer_num"." in file".__FILE__." on Line ".__LINE__);
			return $this->missingPrameterArr("dealer_num");
		}
		if(!isset($arrData['group_id']) || $arrData['group_id'] === "" )
		{
			log_message('error', "function(getViceCommission):lack of group_id"." in file".__FILE__." on Line ".__LINE__);
			return $this->missingPrameterArr("group_id");
		}
		if(!isset($arrData['page']))
		{
			log_message('error', "function(getViceCommission):lack of page"." in file".__FILE__." on Line ".__LINE__);
			return $this->missingPrameterArr("page");
		}

		if($arrData['dealer_num'] == "-1")
		{
			return array('result'=>OPT_CONST::SUCCESS,'data'=>$result,'result_message'=>"获取副会长列表");
		}
		$dealer_num = $arrData['dealer_num'];
		$group_id = $arrData['group_id'];
		$DelaerConst = "Dealer_".$dealer_num;
		if($dealer_num == "-1" || !class_exists($DelaerConst))
		{
			return array('result'=>OPT_CONST::SUCCESS,'data'=>$result,'result_message'=>"获取副会长列表");
		}

		$page = $arrData['page'];
		if($page == 0 || $page == "")
		{
			$page = 1;
		}
		$limit = 20;
		$offset = ($page - 1) * $limit;
		$sum_page = 1;

		$where = 'group_id='.$group_id.' and level=1 and is_delete=0';
		
		$list_sql = 'select * from '.Guild_Member.' where '.$where.' order by create_time asc limit '.$offset.','.$limit;

        $dealerDB = $DelaerConst::DBConst_Name;	
		
		$list_query = $this->getDataBySql($dealerDB,0,$list_sql);
		if($list_query != DB_CONST::DATA_NONEXISTENT)
		{
			$count_sql = 'select count(member_id) as count from '.Guild_Member.' where '.$where;
			$count_query = $this->getDataBySql($dealerDB,1,$count_sql);
			$sum_page = ceil($count_query['count'] / $limit);

			foreach($list_query as $list_item)
			{
				$account_id = $list_item['account_id'];
				$create_time = $list_item['create_time'];

				$array['account_id'] = $account_id;
				$array['create_time'] = date('Y-m-d H:i:s',$create_time);

				$sql = 'select balance from '.Guild_ViceBalance.' where group_id = '.$group_id.' and vice_president = '.$account_id.' and is_delete=0 order by create_time desc';
				$query = $this->getDataBySql($dealerDB,1,$sql);
				$array['balance'] = $query['balance'] > 0 ? $query['balance'] : 0;
				if($array['balance'] <= 0)
				{
					continue;
				}

				$sql = 'select nickname from '.WX_Account.' where account_id='.$account_id;
				$query = $this->getDataBySql($dealerDB,1,$sql);
				$array['nickname'] = $query['nickname'];

				// $sql = 'select sum(vice_commission) as vice_income from '.Guild_CommissionRecord.' where group_id='.$group_id.' and vice_president='.$account_id. ' and is_delete=0';
				// $query = $this->getDataBySql($dealerDB,1,$sql);
				// $array['vice_income'] = $query['vice_income'] > 0 ? $query['vice_income'] : 0;

				

				$result[] = $array;
			}
		}

		$today_income = 0;
		$guild_balance = 0;
		$total_income = 0;
		
		$today_timestamp = strtotime(date("Y-m-d",$timestamp));
		$today_sql = 'select sum(guild_commission) as today_income from '.Guild_CommissionRecord.' where group_id='.$group_id.' and create_time>='.$today_timestamp.' and is_delete=0';
		$today_query = $this->getDataBySql($dealerDB,1,$today_sql);
		$today_income = $today_query['today_income'] > 0 ? $today_query['today_income'] : 0;

		$balance_sql = 'select balance from '.Guild_Balance.' where group_id='.$group_id.' and is_delete=0 order by balance_id desc limit 1';
		$balance_query = $this->getDataBySql($dealerDB,1,$balance_sql);
		$guild_balance = $balance_query['balance'] > 0 ? $balance_query['balance'] : 0;

		$total_sql = 'select sum(guild_commission) as total_income from '.Guild_CommissionRecord.' where group_id='.$group_id.' and is_delete=0';
		$total_query = $this->getDataBySql($dealerDB,1,$total_sql);
		$total_income = $total_query['total_income'] > 0 ? $total_query['total_income'] : 0;

		$balance_data['today_income'] = $today_income;
		$balance_data['guild_balance'] = $guild_balance;
		$balance_data['total_income'] = $total_income;

		return array('result'=>OPT_CONST::SUCCESS,'data'=>$result, 'sum_page'=>$sum_page,'page'=>$page,'balance_data'=>$balance_data,'result_message'=>"获取副会长提成");
	}


	/*
		查找成员
	*/
	public function searchMember($arrData)
	{
		$result = array();
		$timestamp = time();

		if(!isset($arrData['dealer_num']) || $arrData['dealer_num'] === "" )
		{
			log_message('error', "function(searchMember):lack of dealer_num"." in file".__FILE__." on Line ".__LINE__);
			return $this->missingPrameterArr("dealer_num");
		}
		if(!isset($arrData['group_id']) || $arrData['group_id'] === "" )
		{
			log_message('error', "function(searchMember):lack of group_id"." in file".__FILE__." on Line ".__LINE__);
			return $this->missingPrameterArr("group_id");
		}
		if(!isset($arrData['type']) || $arrData['type'] === "" )
		{
			log_message('error', "function(searchMember):lack of type"." in file".__FILE__." on Line ".__LINE__);
			return $this->missingPrameterArr("type");
		}
		if(!isset($arrData['account_id']) || $arrData['account_id'] === "" )
		{
			log_message('error', "function(searchMember):lack of account_id"." in file".__FILE__." on Line ".__LINE__);
			return $this->missingPrameterArr("account_id");
		}
		if(!isset($arrData['page']))
		{
			log_message('error', "function(searchMember):lack of page"." in file".__FILE__." on Line ".__LINE__);
			return $this->missingPrameterArr("page");
		}

		$dealer_num = $arrData['dealer_num'];
		$group_id = $arrData['group_id'];
		$account_id = $arrData['account_id'];
		$type = $arrData['type'];
		$nickname = isset($arrData['nickname']) ? $arrData['nickname'] : '' ;
		$phone = isset($arrData['phone']) ? $arrData['phone'] : '' ;
		$DelaerConst = "Dealer_".$dealer_num;
		$dealerDB = $DelaerConst::DBConst_Name;	

		$page = $arrData['page'];
		if($page == 0 || $page == "")
		{
			$page = 1;
		}
		$limit = 20;
		$offset = ($page - 1) * $limit;
		$sum_page = 1;
		$sum_count = 0;

		$where = WX_Account.'.account_id ='.Guild_Member.'.account_id and '.Guild_Member.'.group_id ='.$group_id.' and '.Guild_Member.'.is_delete=0 ';
		if($nickname){
			$where .= ' and nickname like "%'.$nickname.'%" ';
		}
		if($phone){
			$where .= ' and phone = "'.$phone.'" ';
		}
		if($type==1)
		{
			$where .= ' and vice_president = "'.$account_id.'" ';
		}

		$sql = 'select '.WX_Account.'.account_id,nickname,phone,level,'.Guild_Member.'.create_time  from '.WX_Account.','.Guild_Member.' where '.$where.' order by '.Guild_Member.'.level desc limit '.$offset.','.$limit;
		$list_query = $this->getDataBySql($dealerDB,0,$sql);
		if($list_query != DB_CONST::DATA_NONEXISTENT)
		{
			$count_sql = 'select count(1) as count from '.WX_Account.','.Guild_Member.' where '.$where;
			$count_query = $this->getDataBySql($dealerDB,1,$count_sql);
			$sum_page = ceil($count_query['count'] / $limit);
			$sum_count = $count_query['count'];

			foreach($list_query as $list_item)
			{
				$account_id = $list_item['account_id'];
				$create_time = $list_item['create_time'];
				$array['nickname'] = $list_item['nickname'];
				$array['level'] = $list_item['level'];
				$array['account_id'] = $account_id;

				$result[] = $array;
			}
		}
		return array('result'=>OPT_CONST::SUCCESS,'data'=>$result, 'page'=>$page,'sum_page'=>$sum_page, 'sum_count'=>$sum_count,'result_message'=>"查找成员");
	}

    /*
		设置/取消 副会长
	*/
	public function setVicePresident($arrData)
	{
		$result = array();
		$timestamp = time();

		if(!isset($arrData['dealer_num']) || $arrData['dealer_num'] === "" )
		{
			log_message('error', "function(updateGoodsList):lack of dealer_num"." in file".__FILE__." on Line ".__LINE__);
			return $this->missingPrameterArr("dealer_num");
		}
		if(!isset($arrData['group_id']) || $arrData['group_id'] === "" )
		{
			log_message('error', "function(updateGoodsList):lack of group_id"." in file".__FILE__." on Line ".__LINE__);
			return $this->missingPrameterArr("group_id");
		}
		if(!isset($arrData['my_aid']) || $arrData['my_aid'] === "" )
		{
			log_message('error', "function(updateGoodsList):lack of my_aid"." in file".__FILE__." on Line ".__LINE__);
			return $this->missingPrameterArr("my_aid");
		}

		if(!isset($arrData['member_aid']) || $arrData['member_aid'] === "" )
		{
			log_message('error', "function(updateGoodsList):lack of member_aid"." in file".__FILE__." on Line ".__LINE__);
			return $this->missingPrameterArr("member_aid");
		}

		if(!isset($arrData['set']) || $arrData['set'] === "" )
		{
			log_message('error', "function(updateGoodsList):lack of set"." in file".__FILE__." on Line ".__LINE__);
			return $this->missingPrameterArr("set");
		}
		
	
		$dealer_num = $arrData['dealer_num'];
		$DelaerConst = "Dealer_".$dealer_num;
		$dealerDB = $DelaerConst::DBConst_Name;	

		$my_aid =  $arrData['my_aid'];

		$member_aid =  $arrData['member_aid'];
		$group_id =  $arrData['group_id'];
		$set =  $arrData['set'] == 1 ? 1 : 0;

    	$where = 'account_id='.$my_aid.' and group_id='.$group_id.' and level=2 and is_delete=0';
		$sql = 'select * from '.Guild_Member.' where '.$where.' order by member_id asc';
		$query = $this->getDataBySql($dealerDB,1,$sql);
		if($query == DB_CONST::DATA_NONEXISTENT)
		{
			return array('result'=>OPT_CONST::FAILED,'data'=>$result,'result_message'=>"会长权限");
		}


    	$where = 'account_id='.$member_aid.' and group_id='.$group_id.' and is_delete=0';
		$sql = 'select * from '.Guild_Member.' where '.$where.' order by member_id asc';
		$query = $this->getDataBySql($dealerDB,1,$sql);
		if($query == DB_CONST::DATA_NONEXISTENT)
		{
			return array('result'=>OPT_CONST::FAILED,'data'=>$result,'result_message'=>"非公会成员");
		}
		else
		{
			$member_id = $query['member_id'];

			$update_array['update_time'] = $timestamp;
			$update_array['update_appid'] = "president".$my_aid;

			$update_array['account_id'] = $member_aid;
			$update_array['group_id'] = $group_id;
			$update_array['level'] = $set;
    		$update_query = $this->updateFunc($dealerDB,"member_id",$member_id,Guild_Member,$update_array);

    		if(!$set){
    			$update_arr['update_time'] = $timestamp;
    			$update_arr['update_appid'] = "president".$my_aid;
		        $update_arr['vice_president'] = -1;
		        $update_query = $this->updateFunc($dealerDB,"vice_president",$member_aid,Guild_Member,$update_arr);
    		}
		}

		return array('result'=>OPT_CONST::SUCCESS,'data'=>$result,'result_message'=>"设置成功");
    }



	/*
		获取会员消费提成记录
	*/
	public function getMemberCommissionList($arrData)
	{
		$result = array();
		$timestamp = time();

		if(!isset($arrData['dealer_num']) || $arrData['dealer_num'] === "" )
		{
			log_message('error', "function(getMemberCommissionList):lack of dealer_num"." in file".__FILE__." on Line ".__LINE__);
			return $this->missingPrameterArr("dealer_num");
		}
		if(!isset($arrData['group_id']) || $arrData['group_id'] === "" )
		{
			log_message('error', "function(getMemberCommissionList):lack of group_id"." in file".__FILE__." on Line ".__LINE__);
			return $this->missingPrameterArr("group_id");
		}
		if(!isset($arrData['my_aid']) || $arrData['my_aid'] === "" )
		{
			log_message('error', "function(getMemberCommissionList):lack of my_aid"." in file".__FILE__." on Line ".__LINE__);
			return $this->missingPrameterArr("my_aid");
		}
		if(!isset($arrData['page']))
		{
			log_message('error', "function(getMemberCommissionList):lack of page"." in file".__FILE__." on Line ".__LINE__);
			return $this->missingPrameterArr("page");
		}
		
		$dealer_num = $arrData['dealer_num'];
		$group_id = $arrData['group_id'];
		$account_id = $arrData['my_aid'];
		$DelaerConst = "Dealer_".$dealer_num;
		$dealerDB = $DelaerConst::DBConst_Name;	

		$page = $arrData['page'];
		if($page == 0 || $page == "")
		{
			$page = 1;
		}
		$limit = 20;
		$offset = ($page - 1) * $limit;
		$sum_page = 1;
		$sum_count = 0;
		$balance_amount = 0;

		$balance_sql = 'select balance as balance_amount from '.Guild_ViceBalance.' where group_id='.$group_id.' and vice_president='.$account_id.' and is_delete=0 order by balance_id desc limit 1';
		$balance_query = $this->getDataBySql($dealerDB,1,$balance_sql);
		if($balance_sql != DB_CONST::DATA_NONEXISTENT)
		{
			$balance_amount = $balance_query['balance_amount'];
		}

		$where = 'group_id='.$group_id.' and vice_president='.$account_id.' and object_type=1 and is_delete=0';
		$list_sql = 'select create_time,customer,price,vice_commission from '.Guild_CommissionRecord.' where '.$where.' order by create_time desc limit '.$offset.','.$limit;
		$list_query = $this->getDataBySql($dealerDB,0,$list_sql);
		if($list_query != DB_CONST::DATA_NONEXISTENT)
		{
			$count_sql = 'select count(balance_id) as count from '.Guild_ViceBalance.' where '.$where;
			$count_query = $this->getDataBySql($dealerDB,1,$count_sql);
			$sum_page = ceil($count_query['count'] / $limit);
			$sum_count = $count_query['count'];

			foreach($list_query as $list_item)
			{
				$create_time = $list_item['create_time'];
				$amount = $list_item['price'];
				$commission = $list_item['vice_commission'];
				$customer = $list_item['customer'];

				$sql = 'select nickname from '.WX_Account.' where account_id='.$customer;
				$query = $this->getDataBySql($dealerDB,1,$sql);
				if($query == DB_CONST::DATA_NONEXISTENT)
				{
					continue;
				}

				$array['nickname'] = $query['nickname'];
				$array['amount'] = $amount;
				$array['commission'] = $commission;
				$array['create_time'] = date('Y-m-d H:i:s',$create_time);

				$result[] = $array;
			}
		}

		return array('result'=>OPT_CONST::SUCCESS,'data'=>$result, 'sum_page'=>$sum_page,'sum_count'=>$sum_count,'page'=>$page,'balance_amount'=>$balance_amount,'result_message'=>"获取会员消费提成记录");
	}

}