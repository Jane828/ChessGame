<?php

include_once __DIR__.'/common_model.php';		//加载数据库操作类
class Message_Model extends Noty_Common_Model
{
	public function __construct()
	{
		header('Content-Type: text/html; charset=utf-8'); 
		parent::__construct();
	}


	/*
		发送消息
	*/
	public function sendMessage($arrData)
	{
		$result = array();
		$timestamp = time();

		if(!isset($arrData['dealer_num']) || $arrData['dealer_num'] === "" )
		{
			log_message('error', "function(sendMessage):lack of dealer_num"." in file".__FILE__." on Line ".__LINE__);
			return $this->missingPrameterArr("dealer_num");
		}
		if(!isset($arrData['account_id']) || $arrData['account_id'] === "" )
		{
			log_message('error', "function(sendMessage):lack of account_id"." in file".__FILE__." on Line ".__LINE__);
			return $this->missingPrameterArr("account_id");
		}
		if(!isset($arrData['content']) || $arrData['content'] === "" )
		{
			log_message('error', "function(sendMessage):lack of content"." in file".__FILE__." on Line ".__LINE__);
			return $this->missingPrameterArr("content");
		}

		if($arrData['dealer_num'] == "-1")
		{
			return array('result'=>OPT_CONST::FAILED,'data'=>$result,'result_message'=>"代理商信息错误");
		}
		$dealer_num = $arrData['dealer_num'];
		$DelaerConst = "Dealer_".$dealer_num;
		if($dealer_num == "-1" || !class_exists($DelaerConst))
		{
			return array('result'=>OPT_CONST::FAILED,'data'=>$result,'result_message'=>"代理商信息错误");
		}
		$dealerDB = $DelaerConst::DBConst_Name;	
		
		if($dealer_num == 2 || $dealer_num == 9)
		{
			return array('result'=>OPT_CONST::SUCCESS,'data'=>$result,'result_message'=>"发送成功");
		}



		$account_id = $arrData['account_id'];
		$content = base64_decode($arrData['content']);

		$array['create_time'] = $timestamp;
		$array['create_appid'] = "dealernum_".$dealer_num;
		$array['update_time'] = $timestamp;
		$array['update_appid'] = "dealernum_".$dealer_num;
		$array['is_delete'] = 0;
		$array['account_id'] = $account_id;
		$array['msg_content'] = $content;
		$array['is_read'] = 0;
		$dealer_id = $this->getInsertID($dealerDB,Noty_Message,$array);
		unset($array);

		return array('result'=>OPT_CONST::SUCCESS,'data'=>$result,'result_message'=>"发送成功");
	}


}