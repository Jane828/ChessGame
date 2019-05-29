<?php

include_once __DIR__.'/common_model.php';		//加载数据库操作类
class Direct_Model extends Agent_Common_Model
{
	public function __construct()
	{
		header('Content-Type: text/html; charset=utf-8'); 
		parent::__construct();
	}

	
	public function searchAgentList($arrData)
	{
		$result = array();
		$timestamp = time();

		if(!isset($arrData['dealer_num']) || $arrData['dealer_num'] === "" )
		{
			log_message('error', "function(searchAgentList):lack of dealer_num"." in file".__FILE__." on Line ".__LINE__);
			return $this->missingPrameterArr("dealer_num");
		}
		if(!isset($arrData['page']))
		{
			log_message('error', "function(searchAgentList):lack of page"." in file".__FILE__." on Line ".__LINE__);
			return $this->missingPrameterArr("page");
		}
		if(!isset($arrData['keyword']))
		{
			log_message('error', "function(searchAgentList):lack of keyword"." in file".__FILE__." on Line ".__LINE__);
			return $this->missingPrameterArr("keyword");
		}
		
		if($arrData['dealer_num'] == "-1")
		{
			return array('result'=>OPT_CONST::FAILED,'data'=>$result,'result_message'=>"代理商尚未部署成功");
		}
		$dealer_num = $arrData['dealer_num'];
		$DelaerConst = "Dealer_".$dealer_num;
		if($dealer_num == "-1" || !class_exists($DelaerConst))
		{
			return array('result'=>OPT_CONST::FAILED,'data'=>$result,'result_message'=>"代理商尚未部署成功");
		}
        $dealerDB = $DelaerConst::DBConst_Name;	

		$keyword = $arrData['keyword'];
		$page = $arrData['page'];
		if($page == 0 || $page == "")
		{
			$page = 1;
		}

		$limit = 30;
		$offset = ($page - 1) * $limit;
		$sum_page = 1;

		if($keyword != "")
		{
			$account_where = 'nickname like "%'.$keyword.'%" and is_delete=0';
			
			$account_sql = 'select account_id,nickname,headimgurl from '.WX_Account.' where '.$account_where.' order by create_time desc limit '.$offset.','.$limit;
			$account_query = $this->getDataBySql($dealerDB,0,$account_sql);
			if($account_query != DB_CONST::DATA_NONEXISTENT)
			{
				$count_sql = 'select count(account_id) as count from '.WX_Account.' where '.$account_where;
				$count_query = $this->getDataBySql($dealerDB,1,$count_sql);
				$sum_page = ceil($count_query['count'] / $limit);

				foreach($account_query as $item)
				{
					$array['account_id'] = $item['account_id'];
					$array['nickname'] = $item['nickname'];
					$array['headimgurl'] = $item['headimgurl'];

					//判断是否绑定直营代理
					$array['is_agent'] = 0;
					$bind_sql = 'select data_id from '.Agent_Bind.' where account_id='.$array['account_id'].' and is_delete=0';
					$bind_query = $this->getDataBySql($dealerDB,1,$bind_sql);
					if($bind_query != DB_CONST::DATA_NONEXISTENT)
					{
						$array['is_agent'] = 1;
					}

					$array['ticket_count'] = 0;
					//获取房卡数
					$ticket_sql = 'select ticket_count from '.Room_Ticket.' where account_id='.$array['account_id'];
					$ticket_query = $this->getDataBySql($dealerDB,1,$ticket_sql);
					if($ticket_query != DB_CONST::DATA_NONEXISTENT)
					{
						$array['ticket_count'] = $ticket_query['ticket_count'];
					}

					$result[] = $array;
				}
			}
		}
		else
		{
			$account_where = 'acc.account_id=ab.account_id and ab.is_delete=0 and acc.is_delete=0';
			$account_sql = 'select acc.account_id as account_id,nickname,headimgurl from '.WX_Account.' as acc,'.Agent_Bind.' as ab where '.$account_where.' order by ab.create_time desc limit '.$offset.','.$limit;
			$account_query = $this->getDataBySql($dealerDB,0,$account_sql);
			if($account_query != DB_CONST::DATA_NONEXISTENT)
			{
				foreach($account_query as $item)
				{
					$array['account_id'] = $item['account_id'];
					$array['nickname'] = $item['nickname'];
					$array['headimgurl'] = $item['headimgurl'];
					$array['is_agent'] = 1;

					$array['ticket_count'] = 0;
					//获取房卡数
					$ticket_sql = 'select ticket_count from '.Room_Ticket.' where account_id='.$array['account_id'];
					$ticket_query = $this->getDataBySql($dealerDB,1,$ticket_sql);
					if($ticket_query != DB_CONST::DATA_NONEXISTENT)
					{
						$array['ticket_count'] = $ticket_query['ticket_count'];
					}

					$result[] = $array;
				}
			}
		}
		return array('result'=>OPT_CONST::SUCCESS,'data'=>$result,'result_message'=>"获取用户明细",'sum_page'=>$sum_page);
	}


	public function bindAgentOpt($arrData)
	{
		$result = array();
		$timestamp = time();

		if(!isset($arrData['dealer_num']) || $arrData['dealer_num'] === "" )
		{
			log_message('error', "function(bindAgentOpt):lack of dealer_num"." in file".__FILE__." on Line ".__LINE__);
			return $this->missingPrameterArr("dealer_num");
		}
		if(!isset($arrData['account_id']) || $arrData['account_id'] === "" )
		{
			log_message('error', "function(bindAgentOpt):lack of account_id"." in file".__FILE__." on Line ".__LINE__);
			return $this->missingPrameterArr("account_id");
		}

		if($arrData['dealer_num'] == "-1")
		{
			return array('result'=>OPT_CONST::FAILED,'data'=>$result,'result_message'=>"代理商尚未部署成功");
		}
		$dealer_num = $arrData['dealer_num'];
		$DelaerConst = "Dealer_".$dealer_num;
		if($dealer_num == "-1" || !class_exists($DelaerConst))
		{
			return array('result'=>OPT_CONST::FAILED,'data'=>$result,'result_message'=>"代理商尚未部署成功");
		}
        $dealerDB = $DelaerConst::DBConst_Name;	

		$account_id = $arrData['account_id'];


		$count_sql = 'select count(data_id) as count from '.Agent_Bind.' where is_delete=0';
		$count_query = $this->getDataBySql($dealerDB,1,$count_sql);
		if($count_query != DB_CONST::DATA_NONEXISTENT)
		{
			if($count_query['count'] >= 30)
			{
				return array('result'=>OPT_CONST::FAILED,'data'=>$result,'result_message'=>"30个直营代理商名额已满");
			}
		}

		$bind_sql = 'select data_id from '.Agent_Bind.' where account_id='.$account_id.' and is_delete=0';
		$bind_query = $this->getDataBySql($dealerDB,1,$bind_sql);
		if($bind_query != DB_CONST::DATA_NONEXISTENT)
		{
			//减少房卡
			$update_str = 'update_time='.$timestamp.',update_appid="dealernum_'.$dealer_num.'",is_delete=0';
			$update_where = 'data_id='.$bind_query['data_id'];
			$update_query = $this->changeNodeValue($dealerDB,Room_Ticket,$update_str,$update_where);
		}
		else
		{
			//添加扣除记录
			$insert_array['create_time'] = $timestamp;
			$insert_array['create_appid'] = "dealernum_".$dealer_num;
			$insert_array['update_time'] = $timestamp;
			$insert_array['update_appid'] = "dealernum_".$dealer_num;
			$insert_array['is_delete'] = G_CONST::IS_FALSE;
			$insert_array['account_id'] = $account_id;
			$data_id = $this->getInsertID($dealerDB,Agent_Bind, $insert_array);
		}

		return array('result'=>OPT_CONST::SUCCESS,'data'=>$result,'result_message'=>"设置成功");
	}


	public function unbindAgentOpt($arrData)
	{
		$result = array();
		$timestamp = time();

		if(!isset($arrData['dealer_num']) || $arrData['dealer_num'] === "" )
		{
			log_message('error', "function(unbindAgentOpt):lack of dealer_num"." in file".__FILE__." on Line ".__LINE__);
			return $this->missingPrameterArr("dealer_num");
		}
		if(!isset($arrData['account_id']) || $arrData['account_id'] === "" )
		{
			log_message('error', "function(unbindAgentOpt):lack of account_id"." in file".__FILE__." on Line ".__LINE__);
			return $this->missingPrameterArr("account_id");
		}

		if($arrData['dealer_num'] == "-1")
		{
			return array('result'=>OPT_CONST::FAILED,'data'=>$result,'result_message'=>"代理商尚未部署成功");
		}
		$dealer_num = $arrData['dealer_num'];
		$DelaerConst = "Dealer_".$dealer_num;
		if($dealer_num == "-1" || !class_exists($DelaerConst))
		{
			return array('result'=>OPT_CONST::FAILED,'data'=>$result,'result_message'=>"代理商尚未部署成功");
		}
        $dealerDB = $DelaerConst::DBConst_Name;	

		$account_id = $arrData['account_id'];

		$bind_sql = 'select data_id from '.Agent_Bind.' where account_id='.$account_id.' and is_delete=0';
		$bind_query = $this->getDataBySql($dealerDB,1,$bind_sql);
		if($bind_query != DB_CONST::DATA_NONEXISTENT)
		{
			$update_str = 'update_time='.$timestamp.',update_appid="dealernum_'.$dealer_num.'",is_delete=1';
			$update_where = 'data_id='.$bind_query['data_id'];
			$update_query = $this->changeNodeValue($dealerDB,Agent_Bind,$update_str,$update_where);
		}
		
		return array('result'=>OPT_CONST::SUCCESS,'data'=>$result,'result_message'=>"解绑成功");
	}

}