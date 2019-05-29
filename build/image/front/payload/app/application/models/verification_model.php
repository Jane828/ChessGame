<?php

include_once('public_models.php');		//加载数据库操作类
class Verification_Model extends Public_Models
{
	public function __construct()
	{
		header('Content-Type: text/html; charset=utf-8'); 
		parent::__construct();
	}
	
	
    /*
        生成请求校验码
    */
    public function createRequestSession($arrData)
    {
		$session = "";
		
		if(!isset($arrData['account_id']) || $arrData['account_id'] == G_CONST::EMPTY_STRING)
		{
			return $session;
		}
        if(!isset($arrData['dealer_num']) || $arrData['dealer_num'] == G_CONST::EMPTY_STRING)
		{
			return $session;
		}

        $account_id = $arrData['account_id'];
        $dealer_num = $arrData['dealer_num'];
        $type = "";
        if(isset($arrData['type']))
		{
			$type = $arrData['type'];
		}

        $DelaerConst = "Dealer_".$dealer_num;
		if(!class_exists($DelaerConst))
		{
			return $session;
		}
        $ver_code = $DelaerConst::Verification_Code;    //32位

        $session = base64_encode(md5($account_id.$dealer_num.$ver_code.$type));

        return $session;
    }


    /*
        检查请求校验码
    */
     public function checkRequestSession($arrData)
    {
        $result = array();

		if(!isset($arrData['account_id']) || $arrData['account_id'] == G_CONST::EMPTY_STRING)
		{
            return array('result'=>"-201",'data'=>$result,'result_message'=>"检查请求校验码");
		}
        if(!isset($arrData['dealer_num']) || $arrData['dealer_num'] == G_CONST::EMPTY_STRING)
		{
			return array('result'=>"-201",'data'=>$result,'result_message'=>"检查请求校验码");
		}
        if(!isset($arrData['session']) || $arrData['session'] == G_CONST::EMPTY_STRING)
		{
			return array('result'=>"-201",'data'=>$result,'result_message'=>"检查请求校验码");
		}

        $account_id = $arrData['account_id'];
        $dealer_num = $arrData['dealer_num'];
        $session = $arrData['session'];
        $type = "";
        if(isset($arrData['type']))
		{
			$type = $arrData['type'];
		}

        $DelaerConst = "Dealer_".$dealer_num;
		if(!class_exists($DelaerConst))
		{
			return array('result'=>"-201",'data'=>$result,'result_message'=>"检查请求校验码");
		}
        $ver_code = $DelaerConst::Verification_Code;

        $check_session = base64_encode(md5($account_id.$dealer_num.$ver_code.$type));
        if($session != $check_session)
        {
            return array('result'=>"-201",'data'=>$result,'result_message'=>"检查请求校验码");
        }
        //return array('result'=>"-201",'data'=>$result,'result_message'=>"检查请求校验码");
        return array('result'=>OPT_CONST::SUCCESS,'data'=>$result,'result_message'=>"检查请求校验码");
    }


}