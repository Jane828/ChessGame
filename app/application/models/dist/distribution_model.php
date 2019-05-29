<?php

include_once 'common_model.php';		//加载数据库操作类
class Distribution_Model extends Dist_Common_Model
{
    public function __construct()
    {
        header('Content-Type: text/html; charset=utf-8'); 
        parent::__construct();
    }

    

    /*
		销售代理列表
		
		参数：
			account_id : account_id
		
		返回结果：
			
		
	*/
	public function getDistList($arrData)
	{
		$timestamp = time();
		$result = array();
		
		if(!isset($arrData['account_id']) || $arrData['account_id'] == G_CONST::EMPTY_STRING)
		{
			log_message('error', "function(getCommissionList):lack of account_id"." in file".__FILE__." on Line ".__LINE__);
			return $this->missingPrameterArr("code");
		}
		if(!isset($arrData['page']) || $arrData['page'] == G_CONST::EMPTY_STRING)
		{
			log_message('error', "function(getCommissionList):lack of page"." in file".__FILE__." on Line ".__LINE__);
			return $this->missingPrameterArr("page");
		}
		if(!isset($arrData['dealer_num']) || $arrData['dealer_num'] == G_CONST::EMPTY_STRING)
        {
            log_message('error', "function(getCommissionList):lack of dealer_num"." in file".__FILE__." on Line ".__LINE__);
            return $this->missingPrameterArr("dealer_num");
        }
		if(!isset($arrData['type']) || $arrData['type'] == G_CONST::EMPTY_STRING)
        {
            log_message('error', "function(getCommissionList):lack of type"." in file".__FILE__." on Line ".__LINE__);
            return $this->missingPrameterArr("type");
        }
        $dealer_num = $arrData['dealer_num'];
		$DelaerConst = "Dealer_".$dealer_num;
        $dealerDB = $DelaerConst::DBConst_Name;	
		
		$account_id = $arrData['account_id'];		
		$page = $arrData['page'];
		$type = $arrData['type'];
		
		$limit = 20;
		$offset = ($page - 1) * $limit;
		
		$sum_page = 1;
		
		//获取代理列表
		if($type==1)
		{
			$dist_where = 'intro_aid_1='.$account_id.' and is_delete=0';
		}
		else if($type==2)
		{
			$dist_where = 'intro_aid_2='.$account_id.' and is_delete=0';
		}
		else
		{
			$dist_where = 'iis_delete=2';
		}
		$dist_sql = 'select account_id from '.Dist_Account.' where '.$dist_where.' order by create_time desc,data_id desc limit '.$offset.','.$limit;
		$dist_query = $this->getDataBySql($dealerDB,0,$dist_sql);
		if(DB_CONST::DATA_NONEXISTENT != $dist_query)
		{
            $count_sql = 'select count(account_id) as sum_count from '.Dist_Account.' where '.$dist_where;
			$count_query = $this->getDataBySql($dealerDB,1,$count_sql);
			$sum_count = $count_query['sum_count'];
			$sum_page = ceil($sum_count/$limit);

			foreach($dist_query as $dist_item)
			{
				$account_where = 'account_id='.$dist_item['account_id'];
				$account_sql = 'select account_id,nickname,headimgurl from '.WX_Account.' where '.$account_where.'';
				$account_query = $this->getDataBySql($dealerDB,1,$account_sql);
				if($account_query == DB_CONST::DATA_NONEXISTENT)
				{
					continue;
				}
				$array['nickname'] = $account_query['nickname'];
				$array['headimgurl'] = $account_query['headimgurl'];

				$result[] = $array;
			}
		}

		return array('result'=>OPT_CONST::SUCCESS,'data'=>$result,'result_message'=>"销售代理列表","sum_page"=>$sum_page);
	}


}