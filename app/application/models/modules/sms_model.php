<?php
/**
 *作   者：zhen
 * 
 *创建时间：2015-11-30
 * 
 *类   名：公共基础类  SMS Class
 * 
 *@function 公共方法操作类
 * 有      
 ***/
//require_once('http.php');
class SMS extends CI_Model
{
	public function __construct()
	{
		header('Content-Type: text/html; charset=utf-8'); 
		parent::__construct();
	}
	
	
	/*
		发送sms(短信通)
		
		$url = 'http://apis.baidu.com/kingtto_media/106sms/106sms?mobile=13632361366&content=%E3%80%90%E7%9F%AD%E4%BF%A1%E9%80%9A%E3%80%91%E6%82%A8%E7%9A%84%E9%AA%8C%E8%AF%81%E7%A0%81%EF%BC%9A888888';
		
	*/
	public static function sendSMS_DXT($mobile,$content)
	{
		if($mobile == G_CONST::EMPTY_STRING || !preg_match("/^1[34578]\d{9}$/", $mobile))
		{
			log_message('error', "function(sendSMS_DXT):mobile is wrong"." in file".__FILE__." on Line ".__LINE__);
			return OPT_CONST::FAILED;
		}
		if($content == G_CONST::EMPTY_STRING)
		{
			log_message('error', "function(sendSMS_DXT):content is empty"." in file".__FILE__." on Line ".__LINE__);
			return OPT_CONST::FAILED;
		}
		
		$api_url = Server::DXT_Api_Url;
		$content = urlencode($content);
		
		//log_message('error', "function(sendSMS_DXT):failed($mobile) : ".$content." in file".__FILE__." on Line ".__LINE__);
		
		$url = $api_url.'?mobile='.$mobile.'&content='.$content;
	    $header = array(
	        'apikey:'.Server::DXT_Api_Key,
	    );
	    
	    //log_message('error', "function(sendSMS_DXT):failed($mobile) : ".$url." in file".__FILE__." on Line ".__LINE__);
	    
	    // 添加apikey到header
	    $ch = curl_init();
	    curl_setopt($ch, CURLOPT_HTTPHEADER  , $header);
	    curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
	    // 执行HTTP请求
	    curl_setopt($ch , CURLOPT_URL , $url);
	    $res = curl_exec($ch);
		
		//log_message('error', "function(sendSMS_DXT):failed($mobile) : ".$res." in file".__FILE__." on Line ".__LINE__);
		
		//拆解XML内容
		$xmlObj = simplexml_load_string($res, 'SimpleXMLElement', LIBXML_NOCDATA);
		$xmlArray = get_object_vars($xmlObj);
		
		if(isset($xmlArray['returnstatus']) && $xmlArray['returnstatus'] == "Success")
		{
			return OPT_CONST::SUCCESS;
		}
		else
		{
			log_message('error', "function(sendSMS_DXT):failed($mobile) : ".$res." in file".__FILE__." on Line ".__LINE__);
			return OPT_CONST::FAILED;
		}
	}
}
