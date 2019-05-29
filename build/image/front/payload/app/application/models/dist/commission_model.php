<?php

include_once 'common_model.php';		//加载数据库操作类
class Commission_Model extends Dist_Common_Model
{
    public function __construct()
    {
        header('Content-Type: text/html; charset=utf-8'); 
        parent::__construct();
    }

    /*
		获取总销售提成
		
		参数：
		
		返回结果：
			
		
	*/
	public function getTotalCommission($arrData)
	{
		$timestamp = time();
		$result = array();
		
		if(!isset($arrData['account_id']) || $arrData['account_id'] == G_CONST::EMPTY_STRING)
		{
			log_message('error', "function(getTotalCommission):lack of account_id"." in file".__FILE__." on Line ".__LINE__);
			return $this->missingPrameterArr("account_id");
		}
        if(!isset($arrData['dealer_num']) || $arrData['dealer_num'] == G_CONST::EMPTY_STRING)
		{
			log_message('error', "function(getTotalCommission):lack of dealer_num"." in file".__FILE__." on Line ".__LINE__);
			return $this->missingPrameterArr("dealer_num");
		}
		
		$account_id = $arrData['account_id'];	
		$dealer_num = $arrData['dealer_num'];

		$DelaerConst = "Dealer_".$dealer_num;
        $dealerDB = $DelaerConst::DBConst_Name;
		
        $record_where = 'account_id='.$account_id;
        $record_sql = 'select sum(commission_count) as sum from '.Dist_CommissionRecord.' where '.$record_where.'';
        $record_query = $this->getDataBySql($dealerDB,1,$record_sql);

        $sum_commission = $record_query['sum'];

        $result['sum_commission'] = $sum_commission;

        return array('result'=>OPT_CONST::SUCCESS,'data'=>$result,'result_message'=>"获取总销售提成");
	}

    /*
		销售提成列表
		
		参数：
			account_id : account_id
		
		返回结果：
			
		
	*/
	public function getCommissionList($arrData)
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
        $dealer_num = $arrData['dealer_num'];
		$DelaerConst = "Dealer_".$dealer_num;
        $dealerDB = $DelaerConst::DBConst_Name;	
		
		$account_id = $arrData['account_id'];		
		$page = $arrData['page'];
		
		$limit = 20;
		$offset = ($page - 1) * $limit;
		
		$sum_page = 1;
		
        //获取提成记录
        $record_where = 'account_id='.$account_id.' and is_delete=0';
        $record_sql = 'select create_time,object_type,object_id,object_aid,ticket_count,commission_count from '.Dist_CommissionRecord.' where '.$record_where.' order by create_time desc limit '.$offset.','.$limit;
        $record_query = $this->getDataBySql($dealerDB,0,$record_sql);
        if(DB_CONST::DATA_NONEXISTENT != $record_query)
		{
            $count_sql = 'select count(record_id) as sum_count from '.Dist_CommissionRecord.' where '.$record_where;
			$count_query = $this->getDataBySql($dealerDB,1,$count_sql);
			$sum_count = $count_query['sum_count'];
			$sum_page = ceil($sum_count/$limit);

            foreach($record_query as $item)
			{
                $object_aid = $item['object_aid'];

                $array['create_time'] = $item['create_time'];
                $array['ticket_count'] = $item['ticket_count'];
                $array['commission_count'] = $item['commission_count'];

                if($item['object_type'] == Game_CONST::CommissionType_Order)
                {
                    $array['object_text'] = "购买房卡";
                }
                else {
                    $array['object_text'] = "新用户注册";
                }

                $account_where = 'account_id='.$object_aid;
                $account_sql = 'select nickname from '.WX_Account.' where '.$account_where.'';
                $account_query = $this->getDataBySql($dealerDB,1,$account_sql);
                $array['nickname'] = $account_query['nickname'];

				$result[] = $array;
			}
        }
		return array('result'=>OPT_CONST::SUCCESS,'data'=>$result,'result_message'=>"销售提成列表","sum_page"=>$sum_page);
	}


}