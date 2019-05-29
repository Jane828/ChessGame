<?php

include_once 'common_model.php';		//加载数据库操作类
class Account_Model extends Account_Common_Model
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
		管理员登陆
	*/
	public function adminLoginOpt($arrData)
	{
		$result = array();
		$timestamp = time();

		if(!isset($arrData['account']) || $arrData['account'] === "" )
		{
			log_message('error', "function(adminLoginOpt):lack of adminLoginOpt"." in file".__FILE__." on Line ".__LINE__);
			return $this->missingPrameterArr("account");
		}
		if(!isset($arrData['pwd']) || $arrData['pwd'] === "" )
		{
			log_message('error', "function(adminLoginOpt):lack of pwd"." in file".__FILE__." on Line ".__LINE__);
			return $this->missingPrameterArr("pwd");
		}
		
		$account = $arrData['account'];
		$pwd = $arrData['pwd'];

		$dealerDB = "admin";

		$dealer_where = 'account="'.$account.'" and passwd="'.$pwd.'" and is_delete=0';
		$dealer_sql = 'select dealer_id from '.D_Dealer_Account.' where '.$dealer_where;
		$dealer_query = $this->getDataBySql($dealerDB,1,$dealer_sql);
		if($dealer_query == DB_CONST::DATA_NONEXISTENT)
		{
			return array('result'=>OPT_CONST::FAILED,'data'=>$result,'result_message'=>"账号或密码错误");
		}

		$_SESSION['LoginAdminID'] = $dealer_query['dealer_id'];

		return array('result'=>OPT_CONST::SUCCESS,'data'=>$result,'result_message'=>"登陆成功");
	}
	

	/*
		代理商登陆
	*/
	public function loginOpt($arrData)
	{
		$result = array();
		$timestamp = time();

		if(!isset($arrData['account']) || $arrData['account'] === "" )
		{
			log_message('error', "function(loginOpt):lack of account"." in file".__FILE__." on Line ".__LINE__);
			return $this->missingPrameterArr("account");
		}
		if(!isset($arrData['pwd']) || $arrData['pwd'] === "" )
		{
			log_message('error', "function(loginOpt):lack of pwd"." in file".__FILE__." on Line ".__LINE__);
			return $this->missingPrameterArr("pwd");
		}
		
		$account = $arrData['account'];
		$pwd = $arrData['pwd'];

		$dealerDB = "admin";

		$dealer_where = 'account="'.$account.'" and passwd="'.$pwd.'" and is_delete=0';
		$dealer_sql = 'select dealer_id,dealer_num from '.D_Dealer_Account.' where '.$dealer_where;
		$dealer_query = $this->getDataBySql($dealerDB,1,$dealer_sql);
		if($dealer_query == DB_CONST::DATA_NONEXISTENT)
		{
			return array('result'=>OPT_CONST::FAILED,'data'=>$result,'result_message'=>"账号或密码错误");
		}

		$_SESSION['LoginDealerID'] = $dealer_query['dealer_id'];
		$_SESSION['LoginDealerNum'] = $dealer_query['dealer_num'];

		return array('result'=>OPT_CONST::SUCCESS,'data'=>$result,'result_message'=>"登陆成功");
	}




}