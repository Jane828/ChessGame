<?php

include_once __DIR__.'/common_model.php';		//加载数据库操作类
class Business_Model extends Business_Common_Model
{
	public function __construct()
	{
		header('Content-Type: text/html; charset=utf-8'); 
		parent::__construct();
	}

	/*
		提现
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
		if(!isset($arrData['account_id']) || $arrData['account_id'] === "" )
		{
			log_message('error', "function(withdrawOpt):lack of account_id"." in file".__FILE__." on Line ".__LINE__);
			return $this->missingPrameterArr("account_id");
		}
		if(!isset($arrData['withdraw_id'])|| $arrData['withdraw_id'] === "")
		{
			log_message('error', "function(withdrawOpt):lack of withdraw_id"." in file".__FILE__." on Line ".__LINE__);
			return $this->missingPrameterArr("withdraw_id");
		}
	

		$withdraw_id = $arrData['withdraw_id'];
		$account_id = $arrData['account_id'];
	
		$dealer_num = $arrData['dealer_num'];
		$DelaerConst = "Dealer_".$dealer_num;

		if($dealer_num == "-1" || !class_exists($DelaerConst))
		{
			return array('result'=>OPT_CONST::FAILED,'data'=>$result,'result_message'=>"代理商数据库未建立");
		}

        $dealerDB = $DelaerConst::DBConst_Name;	

        $sql = 'select amount,recommend_id,supply_id from '.Business_Withdraw.' where withdraw_id = '.$withdraw_id.' and is_delete=0 order by create_time desc';
        $query = $this->getDataBySql($dealerDB,1,$sql);

        $amount = $query['amount'];

        if($query['recommend_id'] != $account_id){
        	return array('result'=>OPT_CONST::FAILED,'data'=>$result,'result_message'=>"非本人操作");
        }

		if($amount > 0){

			$update_array['update_time'] = $timestamp;
			$update_array['update_appid'] = "aid_".$account_id;
			$update_array['status'] = 1;
    		$update_query = $this->updateFunc($dealerDB,"withdraw_id",$withdraw_id,Business_Withdraw,$update_array);
	       
			return array('result'=>OPT_CONST::SUCCESS,'data'=>$result,'result_message'=>"申请提现");
		} else {
			return array('result'=>OPT_CONST::FAILED,'data'=>$result,'result_message'=>"提现金额太少");
		}
    }

	/*
		确认支付
	*/
	public function payOpt($arrData)
	{
		$result = array();
		$timestamp = time();

		if(!isset($arrData['dealer_num']) || $arrData['dealer_num'] === "" )
		{
			log_message('error', "function(payOpt):lack of dealer_num"." in file".__FILE__." on Line ".__LINE__);
			return $this->missingPrameterArr("dealer_num");
		}
		if(!isset($arrData['account_id']) || $arrData['account_id'] === "" )
		{
			log_message('error', "function(payOpt):lack of account_id"." in file".__FILE__." on Line ".__LINE__);
			return $this->missingPrameterArr("account_id");
		}
		if(!isset($arrData['withdraw_id'])|| $arrData['withdraw_id'] === "")
		{
			log_message('error', "function(payOpt):lack of withdraw_id"." in file".__FILE__." on Line ".__LINE__);
			return $this->missingPrameterArr("withdraw_id");
		}
	

		$withdraw_id = $arrData['withdraw_id'];
		$account_id = $arrData['account_id'];
	
		$dealer_num = $arrData['dealer_num'];
		$DelaerConst = "Dealer_".$dealer_num;

		if($dealer_num == "-1" || !class_exists($DelaerConst))
		{
			return array('result'=>OPT_CONST::FAILED,'data'=>$result,'result_message'=>"代理商数据库未建立");
		}

        $dealerDB = $DelaerConst::DBConst_Name;	

        $sql = 'select amount,recommend_id,supply_id,status from '.Business_Withdraw.' where withdraw_id = '.$withdraw_id.' and is_delete=0 order by create_time desc';
        $query = $this->getDataBySql($dealerDB,1,$sql);

        $amount = $query['amount'];

        if($query['supply_id'] != $account_id){
        	return array('result'=>OPT_CONST::FAILED,'data'=>$result,'result_message'=>"非本人操作");
        }
	    if($query['status'] != 1){
	    	return array('result'=>OPT_CONST::FAILED,'data'=>$result,'result_message'=>"确认支付状态错误");
	    }

		if($amount > 0){

			$update_array['update_time'] = $timestamp;
			$update_array['update_appid'] = "aid_".$account_id;
			$update_array['status'] = 2;
    		$update_query = $this->updateFunc($dealerDB,"withdraw_id",$withdraw_id,Business_Withdraw,$update_array);
	       
			return array('result'=>OPT_CONST::SUCCESS,'data'=>$result,'result_message'=>"确认支付");
		} else {
			return array('result'=>OPT_CONST::FAILED,'data'=>$result,'result_message'=>"确认支付金额太少");
		}
    }


	/*
		获取交易详情
	*/
	public function getCommissionDetail($arrData)
	{
		$result = array();
		$timestamp = time();

		if(!isset($arrData['dealer_num']) || $arrData['dealer_num'] === "" )
		{
			log_message('error', "function(getCommissionDetail):lack of dealer_num"." in file".__FILE__." on Line ".__LINE__);
			return $this->missingPrameterArr("dealer_num");
		}
		if(!isset($arrData['withdraw_id']) || $arrData['withdraw_id'] === "" )
		{
			log_message('error', "function(getCommissionDetail):lack of withdraw_id"." in file".__FILE__." on Line ".__LINE__);
			return $this->missingPrameterArr("withdraw_id");
		}
		if(!isset($arrData['page']))
		{
			log_message('error', "function(getCommissionDetail):lack of page"." in file".__FILE__." on Line ".__LINE__);
			return $this->missingPrameterArr("page");
		}

		if($arrData['dealer_num'] == "-1")
		{
			return array('result'=>OPT_CONST::SUCCESS,'data'=>$result,'result_message'=>"获取交易详情");
		}
		$dealer_num = $arrData['dealer_num'];
		$withdraw_id = $arrData['withdraw_id'];
		$DelaerConst = "Dealer_".$dealer_num;
		if($dealer_num == "-1" || !class_exists($DelaerConst))
		{
			return array('result'=>OPT_CONST::SUCCESS,'data'=>$result,'result_message'=>"获取交易详情");
		}

		$page = $arrData['page'];
		if($page == 0 || $page == "")
		{
			$page = 1;
		}
		$limit = 20;
		$offset = ($page - 1) * $limit;
		$sum_page = 1;

		$where = 'withdraw_id='.$withdraw_id.' and is_delete=0';
		$list_sql = 'select * from '.Business_Detail.' where '.$where.' order by create_time asc limit '.$offset.','.$limit;

        $dealerDB = $DelaerConst::DBConst_Name;	
		
		$list_query = $this->getDataBySql($dealerDB,0,$list_sql);
		if($list_query != DB_CONST::DATA_NONEXISTENT)
		{
			$count_sql = 'select count(detail_id) as count from '.Business_Detail.' where '.$where;
			$count_query = $this->getDataBySql($dealerDB,1,$count_sql);
			$sum_page = ceil($count_query['count'] / $limit);

			foreach($list_query as $list_item)
			{
				$account_id = $list_item['customer_id'];
				$create_time = $list_item['create_time'];

				$array['account_id'] = $account_id;
				$array['price'] =$list_item['price'];
				$array['create_time'] = date('Y-m-d H:i:s',$create_time);
				$array['commission'] =  $list_item['commission'];

				$sql = 'select nickname,headimgurl from '.WX_Account.' where account_id='.$account_id;
				$query = $this->getDataBySql($dealerDB,1,$sql);
				$array['nickname'] = $query['nickname'];

				$result[] = $array;
			}
		}
		return array('result'=>OPT_CONST::SUCCESS,'data'=>$result, 'sum_page'=>$sum_page,'page'=>$page,'result_message'=>"获取交易详情");
	}
	


	/*
		获取我要付的钱
	*/
	public function getPayList($arrData)
	{
		$result = array();
		$timestamp = time();

		if(!isset($arrData['dealer_num']) || $arrData['dealer_num'] === "" )
		{
			log_message('error', "function(getPayList):lack of dealer_num"." in file".__FILE__." on Line ".__LINE__);
			return $this->missingPrameterArr("dealer_num");
		}
		if(!isset($arrData['account_id']) || $arrData['account_id'] === "" )
		{
			log_message('error', "function(getPayList):lack of account_id"." in file".__FILE__." on Line ".__LINE__);
			return $this->missingPrameterArr("account_id");
		}
		if(!isset($arrData['page']))
		{
			log_message('error', "function(getPayList):lack of page"." in file".__FILE__." on Line ".__LINE__);
			return $this->missingPrameterArr("page");
		}

		$dealer_num = $arrData['dealer_num'];
		$DelaerConst = "Dealer_".$dealer_num;
		if($dealer_num == "-1" || !class_exists($DelaerConst))
		{
			return array('result'=>OPT_CONST::SUCCESS,'data'=>$result,'result_message'=>"获取我要付的钱");
		}

		$account_id = $arrData['account_id'];

		$page = $arrData['page'];
		if($page == 0 || $page == "")
		{
			$page = 1;
		}
		$limit = 20;
		$offset = ($page - 1) * $limit;
		$sum_page = 1;

		$where = 'supply_id='.$account_id.' and is_delete=0';
		
		$list_sql = 'select withdraw_id,recommend_id,create_time,amount,status,ABS(status-0.8) as weight from '.Business_Withdraw.' where '.$where.' order by weight asc,create_time desc limit '.$offset.','.$limit;

        $dealerDB = $DelaerConst::DBConst_Name;	
		
		$list_query = $this->getDataBySql($dealerDB,0,$list_sql);
		if($list_query != DB_CONST::DATA_NONEXISTENT)
		{
			$count_sql = 'select count(withdraw_id) as count from '.Business_Withdraw.' where '.$where;
			$count_query = $this->getDataBySql($dealerDB,1,$count_sql);
			$sum_page = ceil($count_query['count'] / $limit);

			foreach($list_query as $list_item)
			{
				$recommend_id = $list_item['recommend_id'];
				$create_time = $list_item['create_time'];

				$array['withdraw_id'] =$list_item['withdraw_id'];
				$array['account_id'] = $recommend_id;
				$array['create_time'] = date('Y-m-d H:i:s',$create_time);
				$array['amount'] = $list_item['amount'];
				$array['status'] = $list_item['status'];

				$sql = 'select nickname,headimgurl from '.WX_Account.' where account_id='.$recommend_id;
				$query = $this->getDataBySql($dealerDB,1,$sql);
				$array['nickname'] = $query['nickname'];
				$array['headimgurl'] = $query['headimgurl'];

				$result[] = $array;
			}
		}

		return array('result'=>OPT_CONST::SUCCESS,'data'=>$result, 'sum_page'=>$sum_page,'page'=>$page,'result_message'=>"获取我要付的钱");
	}

	/*
		获取我要收的钱
	*/
	public function getIncomeList($arrData)
	{
		$result = array();
		$timestamp = time();

		if(!isset($arrData['dealer_num']) || $arrData['dealer_num'] === "" )
		{
			log_message('error', "function(getIncomeList):lack of dealer_num"." in file".__FILE__." on Line ".__LINE__);
			return $this->missingPrameterArr("dealer_num");
		}
		if(!isset($arrData['account_id']) || $arrData['account_id'] === "" )
		{
			log_message('error', "function(getIncomeList):lack of account_id"." in file".__FILE__." on Line ".__LINE__);
			return $this->missingPrameterArr("account_id");
		}
		if(!isset($arrData['page']))
		{
			log_message('error', "function(getIncomeList):lack of page"." in file".__FILE__." on Line ".__LINE__);
			return $this->missingPrameterArr("page");
		}

		$dealer_num = $arrData['dealer_num'];
		$DelaerConst = "Dealer_".$dealer_num;
		if($dealer_num == "-1" || !class_exists($DelaerConst))
		{
			return array('result'=>OPT_CONST::SUCCESS,'data'=>$result,'result_message'=>"获取我要收的钱");
		}

		$account_id = $arrData['account_id'];

		$page = $arrData['page'];
		if($page == 0 || $page == "")
		{
			$page = 1;
		}
		$limit = 20;
		$offset = ($page - 1) * $limit;
		$sum_page = 1;

		$where = 'recommend_id='.$account_id.' and is_delete=0';
		
		$list_sql = 'select * from '.Business_Withdraw.' where '.$where.' order by status asc,create_time desc limit '.$offset.','.$limit;

        $dealerDB = $DelaerConst::DBConst_Name;	
		
		$list_query = $this->getDataBySql($dealerDB,0,$list_sql);
		if($list_query != DB_CONST::DATA_NONEXISTENT)
		{
			$count_sql = 'select count(withdraw_id) as count from '.Business_Withdraw.' where '.$where;
			$count_query = $this->getDataBySql($dealerDB,1,$count_sql);
			$sum_page = ceil($count_query['count'] / $limit);

			foreach($list_query as $list_item)
			{
				$supply_id = $list_item['supply_id'];
				$create_time = $list_item['create_time'];

				$array['withdraw_id'] =$list_item['withdraw_id'];
				$array['account_id'] = $supply_id;
				$array['create_time'] = date('Y-m-d H:i:s',$create_time);
				$array['amount'] = $list_item['amount'];
				$array['status'] = $list_item['status'];

				$sql = 'select nickname,headimgurl from '.WX_Account.' where account_id='.$supply_id;
				$query = $this->getDataBySql($dealerDB,1,$sql);
				$array['nickname'] = $query['nickname'];
				$array['headimgurl'] = $query['headimgurl'];

				$result[] = $array;
			}
		}

		return array('result'=>OPT_CONST::SUCCESS,'data'=>$result, 'sum_page'=>$sum_page,'page'=>$page,'result_message'=>"获取我要收的钱");
	}


	/*
		查找成员
	*/
	public function searchCustomer($arrData)
	{
		$result = array();
		$timestamp = time();

		if(!isset($arrData['dealer_num']) || $arrData['dealer_num'] === "" )
		{
			log_message('error', "function(searchCustomer):lack of dealer_num"." in file".__FILE__." on Line ".__LINE__);
			return $this->missingPrameterArr("dealer_num");
		}
		if(!isset($arrData['type']) || $arrData['type'] === "" )
		{
			log_message('error', "function(searchCustomer):lack of type"." in file".__FILE__." on Line ".__LINE__);
			return $this->missingPrameterArr("type");
		}
		if(!isset($arrData['account_id']) || $arrData['account_id'] === "" )
		{
			log_message('error', "function(searchCustomer):lack of account_id"." in file".__FILE__." on Line ".__LINE__);
			return $this->missingPrameterArr("account_id");
		}
		if(!isset($arrData['page']))
		{
			log_message('error', "function(searchCustomer):lack of page"." in file".__FILE__." on Line ".__LINE__);
			return $this->missingPrameterArr("page");
		}

		$dealer_num = $arrData['dealer_num'];
		$account_id = $arrData['account_id'];
		$type = $arrData['type'];
		$nickname = isset($arrData['nickname']) ? $arrData['nickname'] : '' ;
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

		$where = WX_Account.'.account_id ='.Business_Account.'.account_id ';
		if(1 == $type){
			$where .=  ' and '.Business_Account.'.supply_id ='.$account_id;
		} else {
			$where .=  ' and '.Business_Account.'.recommend_id ='.$account_id;
		}
		if($nickname){
			$where .= ' and nickname like "%'.$nickname.'%" ';
		}
		$where .= ' and '.Business_Account.'.is_delete=0 ';

		$sql = 'select nickname,phone,'.Business_Account.'.account_id as account_id,'.Business_Account.'.create_time  from '.WX_Account.','.Business_Account.' where '.$where.' order by '.Business_Account.'.create_time asc limit '.$offset.','.$limit;
		$list_query = $this->getDataBySql($dealerDB,0,$sql);
		if($list_query != DB_CONST::DATA_NONEXISTENT)
		{
			$count_sql = 'select count(1) as count from '.WX_Account.','.Business_Account.' where '.$where;
			$count_query = $this->getDataBySql($dealerDB,1,$count_sql);
			$sum_page = ceil($count_query['count'] / $limit);
			$sum_count = $count_query['count'];

			foreach($list_query as $list_item)
			{
				$customer_id = $list_item['account_id'];
				$create_time = $list_item['create_time'];
				$array['nickname'] = $list_item['nickname'];
				$array['customer_id'] = $customer_id;

				$array['level'] = "";
				$sql = 'select level from '.Business_Account.' where account_id='.$customer_id.' and is_delete=0 order by create_time desc limit 1';
				$query = $this->getDataBySql($dealerDB,1,$sql);
				if($query != DB_CONST::DATA_NONEXISTENT)
				{
					$array['level'] = $query['level'];
				}

				$result[] = $array;
			}
		}

		return array('result'=>OPT_CONST::SUCCESS,'data'=>$result, 'page'=>$page,'sum_page'=>$sum_page, 'sum_count'=>$sum_count,'result_message'=>"查找客户");
	}



	/*
		发货记录
	*/
	public function getRedEnvelopList($arrData)
	{
		$timestamp = time();
		$result = array();
		
		if(!isset($arrData['account_id']) || $arrData['account_id'] == G_CONST::EMPTY_STRING)
		{
			log_message('error', "function(getRedEnvelopList):lack of account_id"." in file".__FILE__." on Line ".__LINE__);
			return $this->missingPrameterArr("code");
		}
		if(!isset($arrData['page']) || $arrData['page'] == G_CONST::EMPTY_STRING)
		{
			log_message('error', "function(getRedEnvelopList):lack of page"." in file".__FILE__." on Line ".__LINE__);
			return $this->missingPrameterArr("page");
		}
		if(!isset($arrData['dealer_num']) || $arrData['dealer_num'] == G_CONST::EMPTY_STRING)
        {
            log_message('error', "function(getRedEnvelopList):lack of dealer_num"." in file".__FILE__." on Line ".__LINE__);
            return $this->missingPrameterArr("dealer_num");
        }
        $dealer_num = $arrData['dealer_num'];
		$DelaerConst = "Dealer_".$dealer_num;
        $dealerDB = $DelaerConst::DBConst_Name;	
		
		$account_id = $arrData['account_id'];		
		$page = $arrData['page'];
		
		$limit = 20;
		$offset = ($page - 1) * $limit;
		
		$sum_page = 1;
		
		//获取红包记录
		$redenvelop_where = 'account_id="'.$account_id.'" and type='.Game_CONST::RedenvelopType_Business;
		$redenvelop_sql = 'select create_time,redenvelop_id,account_id,redenvelop_count,price,is_receive,level,is_return,code,receive_type from '.Act_Redenvelop.' where '.$redenvelop_where.' order by create_time desc limit '.$offset.','.$limit;
		$redenvelop_query = $this->getDataBySql($dealerDB,0,$redenvelop_sql);
		if(DB_CONST::DATA_NONEXISTENT != $redenvelop_query)
		{
			$count_sql = 'select count(redenvelop_id) as sum_count from '.Act_Redenvelop.' where '.$redenvelop_where;
			$count_query = $this->getDataBySql($dealerDB,1,$count_sql);
			$sum_count = $count_query['sum_count'];
			$sum_page = ceil($sum_count/$limit);
			
			foreach($redenvelop_query as $item)
			{
				$redenvelop_id = $item['redenvelop_id'];
				$array['redenvelop_count'] = $item['redenvelop_count'];
				$array['price'] = $item['price'];
				$array['level'] = $item['level'];
				$array['code'] = $item['code'];

				$array['status'] = 0;  // 0未领取  1已领取   2过期退回

				if($item['is_receive']){
					if(!$item['is_return']){
						$array['status'] = 1;
					} else {
						$array['status'] = 2;
					}
				}
				
				$array['receive_nickname'] = G_CONST::EMPTY_STRING;
				$array['receive_time'] = G_CONST::EMPTY_STRING;
				
				if($item['is_receive'] == G_CONST::IS_TRUE)
				{
					$receive_type = $item['receive_type'];
					if(1 == $receive_type ){
						$receive_sql = 'select account_id,create_time from '.Act_RedenvelopReceive.' where redenvelop_id='.$redenvelop_id.' and is_delete=0';
						$receive_query = $this->getDataBySql($dealerDB,1,$receive_sql);
						$receive_aid = $receive_query['account_id'];
					} else {
						$receive_sql = 'select customer_id,create_time from '.Business_Detail.' where redenvelop_id='.$redenvelop_id.' and is_delete=0';
						$receive_query = $this->getDataBySql($dealerDB,1,$receive_sql);
						$receive_aid = $receive_query['customer_id'];
					}

					$array['receive_time'] =  date('Y-m-d H:i:s', $receive_query['create_time']);
					
					$account_where = 'account_id='.$receive_aid;
					$account_sql = 'select account_id,nickname from '.WX_Account.' where '.$account_where.'';
					$account_query = $this->getDataBySql($dealerDB,1,$account_sql);
					$array['receive_nickname'] = $account_query['nickname'];
				}
				
				$result[] = $array;
			}
		}
		
		return array('result'=>OPT_CONST::SUCCESS,'data'=>$result,'result_message'=>"出货记录","sum_page"=>$sum_page);
	}


	/*
		收货记录
	*/
	public function getReceiveList($arrData)
	{
		$timestamp = time();
		$result = array();
		
		if(!isset($arrData['account_id']) || $arrData['account_id'] == G_CONST::EMPTY_STRING)
		{
			log_message('error', "function(getReceiveList):lack of account_id"." in file".__FILE__." on Line ".__LINE__);
			return $this->missingPrameterArr("code");
		}
		if(!isset($arrData['page']) || $arrData['page'] == G_CONST::EMPTY_STRING)
		{
			log_message('error', "function(getReceiveList):lack of page"." in file".__FILE__." on Line ".__LINE__);
			return $this->missingPrameterArr("page");
		}
		if(!isset($arrData['dealer_num']) || $arrData['dealer_num'] == G_CONST::EMPTY_STRING)
        {
            log_message('error', "function(getReceiveList):lack of dealer_num"." in file".__FILE__." on Line ".__LINE__);
            return $this->missingPrameterArr("dealer_num");
        }
        $dealer_num = $arrData['dealer_num'];
		$DelaerConst = "Dealer_".$dealer_num;
        $dealerDB = $DelaerConst::DBConst_Name;	
		
		$account_id = $arrData['account_id'];		
		$page = $arrData['page'];
		
		$limit = 20;
		$offset = ($page - 1) * $limit;
		
		$sum_page = 1;

		//获取收货列表
		$receive_where = 'customer_id="'.$account_id.'" and is_delete=0';
		$receive_sql = 'select create_time,redenvelop_count,price,supply_id,redenvelop_id from '.Business_Detail.' where '.$receive_where.' order by create_time desc limit '.$offset.','.$limit;
		$receive_query = $this->getDataBySql($dealerDB,0,$receive_sql);
		if(DB_CONST::DATA_NONEXISTENT != $receive_query)
		{
			$count_sql = 'select count(1) as sum_count from '.Business_Detail.' where '.$receive_where;
			$count_query = $this->getDataBySql($dealerDB,1,$count_sql);
			$sum_count = $count_query['sum_count'];
			$sum_page = ceil($sum_count/$limit);
			
			foreach($receive_query as $item)
			{
				$redenvelop_id = $item['redenvelop_id'];

				$where = 'redenvelop_id='.$redenvelop_id;
				$account_sql = 'select level,code from '.Act_Redenvelop.' where '.$where.'';
				$query = $this->getDataBySql($dealerDB,1,$account_sql);
				$array['level'] = $query['level'];
				$array['code']  = $query['code'];

				$array['redenvelop_count'] = $item['redenvelop_count'];
				$array['price'] = $item['price'];
				$array['receive_time'] = date('Y-m-d H:i:s', $item['create_time']);	//收货时间

				$account_where = 'account_id='.$item['supply_id'];
				$account_sql = 'select account_id,nickname from '.WX_Account.' where '.$account_where.'';
				$account_query = $this->getDataBySql($dealerDB,1,$account_sql);
				$array['sender_nickname'] = $account_query['nickname'];
				
				$result[] = $array;
			}
		}
		
		return array('result'=>OPT_CONST::SUCCESS,'data'=>$result,'result_message'=>"收货记录","sum_page"=>$sum_page);
	}


	protected function createRedEnvelopCode($account_id)
	{
		$mtime=explode(' ',microtime());  
		$mTimestamp = $mtime[1] . substr($mtime[0],2,3);
		
		$code = $account_id.':'.$mTimestamp;
		
		for($i=0;$i<4;$i++)
		{
			$code .= rand(0,9);
		}
		
		return md5($code);
	}


	/*
		生成红包
		
		参数：
			account_id : account_id
			ticket_count : 
		
		返回结果：
			
		
	*/
	public function createRedEnvelopOpt($arrData)
	{
		$timestamp = time();
		$result = array();
		
		if(!isset($arrData['account_id']) || $arrData['account_id'] == G_CONST::EMPTY_STRING)
		{
			log_message('error', "function(createRedEnvelopOpt):lack of account_id"." in file".__FILE__." on Line ".__LINE__);
			return $this->missingPrameterArr("account_id");
		}
		if(!isset($arrData['redenvelop_count']) || $arrData['redenvelop_count'] == G_CONST::EMPTY_STRING)
		{
			log_message('error', "function(createRedEnvelopOpt):lack of redenvelop_count"." in file".__FILE__." on Line ".__LINE__);
			return $this->missingPrameterArr("redenvelop_count");
		}
		if(!isset($arrData['dealer_num']) || $arrData['dealer_num'] == G_CONST::EMPTY_STRING)
		{
			log_message('error', "function(createRedEnvelopOpt):lack of dealer_num"." in file".__FILE__." on Line ".__LINE__);
			return $this->missingPrameterArr("dealer_num");
		}
	
		if(!isset($arrData['level']))
		{
			log_message('error', "function(createRedEnvelopOpt):lack of level"." in file".__FILE__." on Line ".__LINE__);
			return $this->missingPrameterArr("level");
		}
		
		$account_id = $arrData['account_id'];	
		$redenvelop_count = $arrData['redenvelop_count'];	
		$level = $arrData['level'];	
		$dealer_num = $arrData['dealer_num'];
		
		$DelaerConst = "Dealer_".$dealer_num;
        $dealerDB = $DelaerConst::DBConst_Name;


		//获取用户剩余房卡
		$inventory = 0;
		$my_level = 0;
		$account_where = 'account_id="'.$account_id.'" and is_delete=0';
		$account_sql = 'select inventory,level from '.Business_Account.' where '.$account_where.'';
		$account_query = $this->getDataBySql($dealerDB,1,$account_sql);
		if(DB_CONST::DATA_NONEXISTENT != $account_query)
		{
			$inventory = $account_query['inventory'];
			$my_level  = $account_query['level'];
			if(5 == $my_level){	//代理商
				$account_where = 'dealer_num="'.$dealer_num.'" and is_delete=0';
				$account_sql = 'select inventory_count from '.D_Account.' where '.$account_where;
				$account_query = $this->getDataBySql($dealerDB,1,$account_sql);
				if(DB_CONST::DATA_NONEXISTENT != $account_query)
				{
					$inventory = $account_query['inventory_count'];
				}
			}
		}

		switch ($level) {
			case '1':
				$ticket_count = $DelaerConst::SingleCount_Bronze * $redenvelop_count;
				$price = $DelaerConst::TicketPrice_Bronze * $ticket_count;
				$content = "青铜红包";
				break;
			case '2':
				$ticket_count = $DelaerConst::SingleCount_Silver * $redenvelop_count;
				$price = $DelaerConst::TicketPrice_Silver * $ticket_count;
				$content = "白银红包";
				break;
			case '3':
				$ticket_count = $DelaerConst::SingleCount_Gold * $redenvelop_count;
				$price = $DelaerConst::TicketPrice_Gold * $ticket_count;
				$content = "黄金红包";
				break;
			case '4':
				$ticket_count = $DelaerConst::SingleCount_Diamond * $redenvelop_count;
				$price = $DelaerConst::TicketPrice_Diamond * $ticket_count;
				$content = "钻石红包";
				break;
			
			default:
				$ticket_count = 0;
				$price = 0;
				break;
		}

		if($level >= $my_level){
			return array('result'=>OPT_CONST::FAILED,'data'=>$result,'result_message'=>"发包权限不足");
		}

		if($ticket_count > $inventory){
			return array('result'=>OPT_CONST::FAILED,'data'=>$result,'result_message'=>"账户库存不足");
		}
		
		$code = $this->createRedEnvelopCode($account_id);
		
		//添加红包记录
		$insert_array['create_time'] = $timestamp;
		$insert_array['create_appid'] = "aid_".$account_id;
		$insert_array['update_time'] = $timestamp;
		$insert_array['update_appid'] = "aid_".$account_id;
		$insert_array['is_delete'] = G_CONST::IS_FALSE;
		$insert_array['account_id'] = $account_id;
		$insert_array['ticket_count'] = $ticket_count;
		$insert_array['redenvelop_count'] = $redenvelop_count;
		$insert_array['code'] = $code;
		$insert_array['content'] = $content;
		$insert_array['type'] = Game_CONST::RedenvelopType_Business;

		$insert_array['level'] = $level;
		$insert_array['price'] = $price;
		$insert_array['receive_type'] = 0;  //领取类型

		$redenvelop_id = $this->getInsertID($dealerDB,Act_Redenvelop, $insert_array);

		if(5 == $my_level){	//代理商
			//房卡库存流水账
			$i_journal_ary['journal_type'] = Game_CONST::JournalType_Disburse;
			$i_journal_ary['account_id'] = $account_id;
			$i_journal_ary['object_type'] = Game_CONST::DObjectType_RedEnvelop;
			$i_journal_ary['object_id'] = $redenvelop_id;
			$i_journal_ary['ticket_count'] = $ticket_count;
			$i_journal_ary['extra'] = "";
			$this->updateInventoryJournal($i_journal_ary,$dealerDB);
			//减少代理商库存房卡
			$updateTicket_str = 'update_time='.$timestamp.',update_appid="aid_'.$account_id.'",inventory_count=inventory_count-'.$ticket_count;
			$updateTicket_where = 'dealer_num="'.$dealer_num.'"';
			$updateTicket_query = $this->changeNodeValue($dealerDB,D_Account,$updateTicket_str,$updateTicket_where);
		} else {
			//减少自己账户上的房卡
			$updateTicket_str = 'update_time='.$timestamp.',update_appid="aid_'.$account_id.'",inventory=inventory-'.$ticket_count;
			$updateTicket_where = 'account_id='.$account_id;
			$updateTicket_query = $this->changeNodeValue($dealerDB,Business_Account,$updateTicket_str,$updateTicket_where);
		}

		$result['code'] = $code;
		
		return array('result'=>OPT_CONST::SUCCESS,'data'=>$result,'result_message'=>"制作微商红包成功");
	}	


	/*
		红包信息
	*/
	public function getRedEnvelopData($arrData)
	{
		$timestamp = time();
		$result = array();
		
		if(!isset($arrData['code']) || $arrData['code'] == G_CONST::EMPTY_STRING)
		{
			log_message('error', "function(getRedEnvelopData):lack of code"." in file".__FILE__." on Line ".__LINE__);
			return $this->missingPrameterArr("code");
		}
		if(!isset($arrData['dealer_num']) || $arrData['dealer_num'] == G_CONST::EMPTY_STRING)
		{
			log_message('error', "function(getRedEnvelopData):lack of dealer_num"." in file".__FILE__." on Line ".__LINE__);
			return $this->missingPrameterArr("dealer_num");
		}
		
		$code = $arrData['code'];	
		$dealer_num = $arrData['dealer_num'];

		$DelaerConst = "Dealer_".$dealer_num;
        $dealerDB = $DelaerConst::DBConst_Name;
		
		//获取红包记录
		$redenvelop_query = $this->getRedEnvelopByCode($code,$dealerDB);
		if(DB_CONST::DATA_NONEXISTENT == $redenvelop_query)
		{
			return array('result'=>OPT_CONST::FAILED,'data'=>$result,'result_message'=>"红包不存在");
		}
		
		$account_id = $redenvelop_query['account_id'];
		$redenvelop_id = $redenvelop_query['redenvelop_id'];
		$is_receive = $redenvelop_query['is_receive'];
		$level = $redenvelop_query['level'];
		$price = $redenvelop_query['price'];
		
		$account_where = 'account_id='.$account_id;
		$account_sql = 'select account_id,nickname,headimgurl from '.WX_Account.' where '.$account_where.'';
		$account_query = $this->getDataBySql($dealerDB,1,$account_sql);
		if(DB_CONST::DATA_NONEXISTENT == $account_query)
		{
			return array('result'=>OPT_CONST::FAILED,'data'=>$result,'result_message'=>"用户不存在");
		}
		
		//$result['redenvelop_id'] = $redenvelop_id;
		$result['is_receive'] = $is_receive;
		$result['content'] = $redenvelop_query['content'];
		$result['ticket_count'] = $redenvelop_query['ticket_count'];
		$result['redenvelop_count'] = $redenvelop_query['redenvelop_count'];

		$result['nickname'] = $account_query['nickname'];
		$result['headimgurl'] = $account_query['headimgurl'];
		
		$result['receive_nickname'] = G_CONST::EMPTY_STRING;
		$result['receive_headimgurl'] = G_CONST::EMPTY_STRING;
		$result['receive_time'] = G_CONST::EMPTY_STRING;
			
		if($is_receive == G_CONST::IS_TRUE)
		{
			$receive_type = $redenvelop_query['receive_type'];

			if(1 == $receive_type ){
				$receive_sql = 'select account_id,create_time from '.Act_RedenvelopReceive.' where redenvelop_id='.$redenvelop_id.' and is_delete=0';
				$receive_query = $this->getDataBySql($dealerDB,1,$receive_sql);
				$receive_aid = $receive_query['account_id'];
			} else {
				$receive_sql = 'select customer_id,create_time from '.Business_Detail.' where redenvelop_id='.$redenvelop_id.' and is_delete=0';
				$receive_query = $this->getDataBySql($dealerDB,1,$receive_sql);
				$receive_aid = $receive_query['customer_id'];
			}
			
			$result['receive_time'] = date('m-d H:i', $receive_query['create_time']);
			
			$account_where = 'account_id="'.$receive_aid.'"';
			$account_sql = 'select account_id,nickname,headimgurl from '.WX_Account.' where '.$account_where;
			$account_query = $this->getDataBySql($dealerDB,1,$account_sql);
			
			$result['receive_nickname'] = $account_query['nickname'];
			$result['receive_headimgurl'] = $account_query['headimgurl'];

			return array('result'=>OPT_CONST::SUCCESS,'data'=>$result,'result_message'=>"红包已被领取");
		}
		else
		{
			return array('result'=>OPT_CONST::SUCCESS,'data'=>$result,'result_message'=>"点击领取红包");
		}
	}

	protected function getRedEnvelopByCode($code,$dealerDB)
	{
		//获取红包记录
		$redenvelop_where = 'code="'.$code.'"';
		$redenvelop_sql = 'select redenvelop_id,account_id,ticket_count,redenvelop_count,is_receive,content,is_return,level,price,receive_type from '.Act_Redenvelop.' where '.$redenvelop_where;
		$redenvelop_query = $this->getDataBySql($dealerDB,1,$redenvelop_sql);

		return $redenvelop_query;
	}

	/*
		接收红包
	*/
	public function receiveRedEnvelopOpt($arrData)
	{
		$timestamp = time();
		$result = array();
		
		if(!isset($arrData['account_id']) || $arrData['account_id'] == G_CONST::EMPTY_STRING)
		{
			log_message('error', "function(receiveRedEnvelopOpt):lack of account_id"." in file".__FILE__." on Line ".__LINE__);
			return $this->missingPrameterArr("code");
		}
		if(!isset($arrData['code']) || $arrData['code'] == G_CONST::EMPTY_STRING)
		{
			log_message('error', "function(receiveRedEnvelopOpt):lack of code"." in file".__FILE__." on Line ".__LINE__);
			return $this->missingPrameterArr("code");
		}
		if(!isset($arrData['dealer_num']) || $arrData['dealer_num'] == G_CONST::EMPTY_STRING)
        {
            log_message('error', "function(receiveRedEnvelopOpt):lack of dealer_num"." in file".__FILE__." on Line ".__LINE__);
            return $this->missingPrameterArr("dealer_num");
        }

		$account_id = $arrData['account_id'];	
		$code = $arrData['code'];
		$dealer_num = $arrData['dealer_num'];

		$DelaerConst = "Dealer_".$dealer_num;
        $dealerDB = $DelaerConst::DBConst_Name;	
		
		//获取红包记录
		$redenvelop_query = $this->getRedEnvelopByCode($code,$dealerDB);
		if(DB_CONST::DATA_NONEXISTENT == $redenvelop_query)
		{
			return array('result'=>OPT_CONST::FAILED,'data'=>$result,'result_message'=>"红包不存在");
		}
		
		$redenvelop_id = $redenvelop_query['redenvelop_id'];
		$ticket_count = $redenvelop_query['ticket_count'];
		$is_receive = $redenvelop_query['is_receive'];
		$redenvelop_level = $redenvelop_query['level'];
		$redenvelop_count = $redenvelop_query['redenvelop_count'];
		$sender_id = $redenvelop_query['account_id']; //红包发送者id
		
		if($is_receive == G_CONST::IS_TRUE)
		{
			return array('result'=>OPT_CONST::FAILED,'data'=>$result,'result_message'=>"红包已被领取");
		}

		$is_new_business_account = false;
		$source_array = array();

		//获取用户等级
		$account_where = 'account_id='.$account_id.' and is_delete=0';
		$account_sql = 'select level,supply_id,recommend_id,source_1,source_2,source_3 from '.Business_Account.' where '.$account_where;
		$account_query = $this->getDataBySql($dealerDB,1,$account_sql);

		if(DB_CONST::DATA_NONEXISTENT == $account_query){

			$is_new_business_account = true;
			$my_level = ($redenvelop_level>1 && $redenvelop_count>=4) ? $redenvelop_level : 1;

			$insert_array['create_time'] = $timestamp;
			$insert_array['create_appid'] = "aid_".$account_id;
			$insert_array['update_time'] = $timestamp;
			$insert_array['update_appid'] = "aid_".$account_id;
			$insert_array['is_delete'] = G_CONST::IS_FALSE;
			$insert_array['account_id'] = $account_id;
			$insert_array['inventory'] = 0;
			$insert_array['level'] = $my_level;

			$my_supply_id = $sender_id;
			$my_recommend_id = -1;
			
			$insert_array['recommend_id'] = -1;
			$insert_array['supply_id'] = $sender_id;

			$where = 'account_id='.$sender_id.' and is_delete=0';
			$sql = 'select supply_id,source_1,source_2 from '.Business_Account.' where '.$where;
			$query = $this->getDataBySql($dealerDB,1,$sql);
			if(DB_CONST::DATA_NONEXISTENT != $query){
				$insert_array['source_1'] = $query['supply_id'];
				$insert_array['source_2'] = $query['source_1'];
				$insert_array['source_3'] = $query['source_2'];
				if($insert_array['source_1'] > 0){
					$source_array[]= $insert_array['source_1'];
				}
				if($insert_array['source_2'] > 0){
					$source_array[]= $insert_array['source_2'];
				}
				if($insert_array['source_3'] > 0){
					$source_array[]= $insert_array['source_3'];
				}
			}

			$business_id = $this->getInsertID($dealerDB,Business_Account, $insert_array);

		} else {
			$my_level = $account_query['level'];
			$my_supply_id = $account_query['supply_id'];
			$my_recommend_id = $account_query['recommend_id'];

			if($account_query['source_1'] > 0){
				$source_array[]= $account_query['source_1'];
			}
			if($account_query['source_2'] > 0){
				$source_array[]= $account_query['source_2'];
			}
			if($account_query['source_3'] > 0){
				$source_array[]= $account_query['source_3'];
			}
		} 

		$receive_type = 0;
		$can_upgrade = 0;
		$is_from_self = 0;	//自发自收

		if($sender_id == $account_id){ //自己领取
			$is_from_self = 1;
			$receive_type = 2;
		} else if($sender_id==$my_supply_id){ //直属供应商
			$receive_type = 2;
			if($redenvelop_level>$my_level && $redenvelop_count>=4){
				$can_upgrade = 1;
			} else {
				$can_upgrade = 0;
			}

		} else if(in_array($sender_id, $source_array)){
			if($redenvelop_level>$my_level && $redenvelop_count>=4){
				$receive_type = 2;
				$can_upgrade = 1;
			} else {
				return array('result'=>OPT_CONST::FAILED,'data'=>array('is_source'=>1),'result_message'=>"只能领取供货商的红包");
			}
		} else {
			return array('result'=>OPT_CONST::FAILED,'data'=>array('is_source'=>0),'result_message'=>"只能领取供货商的红包");
		}

		if($can_upgrade){	//升级
			$updateAcc_str = 'update_time='.$timestamp.',update_appid="aid_'.$account_id.'",level='.$redenvelop_level;

			//供应链关系改变
			if($my_supply_id != $sender_id){
				
				$updateAcc_str .= ',supply_id='.$sender_id.',recommend_id='.$my_supply_id;
				$where = 'account_id='.$sender_id.' and is_delete=0';
				$sql = 'select supply_id,source_1,source_2 from '.Business_Account.' where '.$where;
				$query = $this->getDataBySql($dealerDB,1,$sql);
				if(DB_CONST::DATA_NONEXISTENT != $query){
					$updateAcc_str .=  ',source_1='.$query['supply_id'];
					$updateAcc_str .=  ',source_2='.$query['source_1'];
					$updateAcc_str .=  ',source_3='.$query['source_2'];
				}
				$my_recommend_id = $my_supply_id;
				$my_supply_id = $sender_id;
			}

			$update_where = 'account_id='.$account_id;
			$this->changeNodeValue($dealerDB,Business_Account,$updateAcc_str,$update_where);
			$my_level = $redenvelop_level; 
		}
	
		$price = $redenvelop_query['price'];

		if( $receive_type == 2 && $my_level > 1){ //微商领取

			$supply_id = $redenvelop_query['account_id'];

			//更新红包领取状态
			$updateRE_str = 'update_time='.$timestamp.',update_appid="aid_'.$account_id.'",is_receive='.G_CONST::IS_TRUE.',receive_type='.$receive_type;
			$updateRE_where = 'redenvelop_id='.$redenvelop_id;
			$updateRE_query = $this->changeNodeValue($dealerDB,Act_Redenvelop,$updateRE_str,$updateRE_where);
			
			//添加领取记录
			$detail_array['create_time'] = $timestamp;
			$detail_array['create_appid'] = "aid_".$account_id;
			$detail_array['update_time'] = $timestamp;
			$detail_array['update_appid'] = "aid_".$account_id;
			$detail_array['is_delete'] = G_CONST::IS_FALSE;

			$detail_array['redenvelop_id'] = $redenvelop_id;
			$detail_array['supply_id'] = $sender_id;
			$detail_array['recommend_id'] = $my_recommend_id;
			$detail_array['customer_id'] = $account_id;
			$detail_array['redenvelop_count'] = $redenvelop_count;
			$detail_array['price'] = $price;

			$detail_array['commission'] = $price * 0.1;

			//推广
			$withdraw_id = -1;
			if(!$is_from_self && $my_recommend_id > 0){

				$where = 'supply_id='.$sender_id.' and recommend_id='.$my_recommend_id.' and status=0 and is_delete=0';
				$sql = 'select withdraw_id from '.Business_Withdraw.' where '.$where.'';
				$withdraw_query = $this->getDataBySql($dealerDB,1,$sql);
				if(DB_CONST::DATA_NONEXISTENT == $withdraw_query){
					//新建记录
					$withdraw_arr['create_time'] = $timestamp;
					$withdraw_arr['create_appid'] = "aid_".$account_id;
					$withdraw_arr['update_time'] = $timestamp;
					$withdraw_arr['update_appid'] = "aid_".$account_id;
					$withdraw_arr['is_delete'] = G_CONST::IS_FALSE;

					$withdraw_arr['supply_id'] = $sender_id; 
					$withdraw_arr['recommend_id'] = $my_recommend_id; 
					$withdraw_arr['amount'] = $price * 0.1; 
					$withdraw_arr['status'] = 0; 
					$withdraw_id = $this->getInsertID($dealerDB,Business_Withdraw, $withdraw_arr);
				} else {
					//更新记录
					$withdraw_id = $withdraw_query['withdraw_id'];

					$update_str = 'update_time='.$timestamp.',update_appid="aid_'.$account_id.'",amount=amount+'.$price * 0.1;
					$update_where = 'withdraw_id='.$withdraw_id;
					$this->changeNodeValue($dealerDB,Business_Withdraw,$update_str,$update_where);
				}
			}
			
			$detail_array['withdraw_id'] = $withdraw_id;
			$detail = $this->getInsertID($dealerDB,Business_Detail, $detail_array);


			if(5 == $my_level){	//代理商
				//房卡库存流水账
				$i_journal_ary['journal_type'] = Game_CONST::JournalType_Income;
				$i_journal_ary['account_id'] = $account_id;
				$i_journal_ary['object_type'] = Game_CONST::DObjectType_RedEnvelop;
				$i_journal_ary['object_id'] = $redenvelop_id;
				$i_journal_ary['ticket_count'] = $ticket_count;
				$i_journal_ary['extra'] = "";
				$this->updateInventoryJournal($i_journal_ary,$dealerDB);
				//退回代理商库存房卡
				$updateTicket_str = 'update_time='.$timestamp.',update_appid="aid_'.$account_id.'",inventory_count=inventory_count+'.$ticket_count;
				$updateTicket_where = 'dealer_num="'.$dealer_num.'"';
				$updateTicket_query = $this->changeNodeValue($dealerDB,D_Account,$updateTicket_str,$updateTicket_where);
			} else {
				//将房卡添加到微商账户
				$updateTicket_str = 'update_time='.$timestamp.',update_appid="aid_'.$account_id.'",inventory=inventory+'.$ticket_count;
				$updateTicket_where = 'account_id='.$account_id;
				$updateTicket_query = $this->changeNodeValue($dealerDB,Business_Account,$updateTicket_str,$updateTicket_where);
			}

		} else {	//个人领取

			$where = 'account_id='.$account_id.' and is_delete=0';
			$sql = 'select ticket_count from '.Room_Ticket.' where '.$where.'';
			$query = $this->getDataBySql($dealerDB,1,$sql);
			//($query['ticket_count']+$ticket_count) <= 1000 
			if(DB_CONST::DATA_NONEXISTENT != $query && 1|| $is_new_business_account){
				$receive_type = 1;

				//更新红包领取状态
				$updateRE_str = 'update_time='.$timestamp.',update_appid="aid_'.$account_id.'",is_receive='.G_CONST::IS_TRUE.',receive_type=1';
				$updateRE_where = 'redenvelop_id='.$redenvelop_id;
				$updateRE_query = $this->changeNodeValue($dealerDB,Act_Redenvelop,$updateRE_str,$updateRE_where);
				
				//添加领取记录
				$insert_receive_array['create_time'] = $timestamp;
				$insert_receive_array['create_appid'] = "aid_".$account_id;
				$insert_receive_array['update_time'] = $timestamp;
				$insert_receive_array['update_appid'] = "aid_".$account_id;
				$insert_receive_array['is_delete'] = G_CONST::IS_FALSE;
				$insert_receive_array['redenvelop_id'] = $redenvelop_id;
				$insert_receive_array['account_id'] = $account_id;
				$insert_receive_array['ticket_count'] = $ticket_count;
				$insert_receive_array['redenvelop_count'] = 1;
				
				$receive_id = $this->getInsertID($dealerDB,Act_RedenvelopReceive, $insert_receive_array);
				
				//将房卡添加到自己账户
				$updateTicket_str = 'update_time='.$timestamp.',update_appid="aid_'.$account_id.'",ticket_count=ticket_count+'.$ticket_count;
				$updateTicket_where = 'account_id='.$account_id;
				$updateTicket_query = $this->changeNodeValue($dealerDB,Room_Ticket,$updateTicket_str,$updateTicket_where);

				//房卡流水账
				$journal_ary['journal_type'] = Game_CONST::JournalType_Income;
				$journal_ary['account_id'] = $account_id;
				$journal_ary['object_type'] = Game_CONST::ObjectType_RedEnvelop;
				$journal_ary['object_id'] = $receive_id;
				$journal_ary['ticket_count'] = $ticket_count;
				$journal_ary['extra'] = "";
				$this->updateRoomTicketJournal($journal_ary,$dealerDB);

				//同时在微商交易明细表添加收货记录
				$detail_array['create_time'] = $timestamp;
				$detail_array['create_appid'] = "aid_".$account_id;
				$detail_array['update_time'] = $timestamp;
				$detail_array['update_appid'] = "aid_".$account_id;
				$detail_array['is_delete'] = G_CONST::IS_FALSE;

				$detail_array['redenvelop_id'] = $redenvelop_id;
				$detail_array['supply_id'] = $sender_id;
				$detail_array['recommend_id'] = $my_recommend_id;
				$detail_array['customer_id'] = $account_id;
				$detail_array['redenvelop_count'] = $redenvelop_count;
				$detail_array['price'] = $price;

				$detail_array['commission'] = 0;
				$detail_array['withdraw_id'] = -1;
				$detail = $this->getInsertID($dealerDB,Business_Detail, $detail_array);

			} else {
				return array('result'=>OPT_CONST::FAILED,'data'=>array('is_source'=>0),'result_message'=>"个人红包超额");
			}
		} 

		return array('result'=>OPT_CONST::SUCCESS,'data'=>array('receive_type'=>$receive_type),'result_message'=>"领取成功");
	}

}