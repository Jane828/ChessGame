<?php

//require_once('http.php');
class SMS extends CI_Model
{

	public function __construct()
	{
		header('Content-Type: text/html; charset=utf-8');
		parent::__construct();
		$this->load->helper('url');
	}
	
	
	
	/*
		发送sms(创蓝)
		
		
	*/
	public static function sendSMS_CL($mobile,$content)
	{
		if($mobile == G_CONST::EMPTY_STRING || !preg_match("/^1[356789]\d{9}$/", $mobile))
		{
			log_message('error', "function(sendSMS_CL):mobile is wrong"." in file".__FILE__." on Line ".__LINE__);
			return OPT_CONST::FAILED;
		}
		if($content == G_CONST::EMPTY_STRING)
		{
			log_message('error', "function(sendSMS_CL):content is empty"." in file".__FILE__." on Line ".__LINE__);
			return OPT_CONST::FAILED;
		}
		
		$needstatus = 'true';
		$product = '';
		$extno = '';
		
		//创蓝接口参数
		$postArr = array (
				          'account' => Server::API_ACCOUNT,
				          'pswd' => Server::API_PASSWORD,
				          'msg' => $content,
				          'mobile' => $mobile,
				          'needstatus' => $needstatus,
				          'product' => $product,
				          'extno' => $extno
                     );
		
		$url = Server::API_SEND_URL;
		
		$postFields = http_build_query($postArr);
		$ch = curl_init ();
		curl_setopt ( $ch, CURLOPT_POST, 1 );
		curl_setopt ( $ch, CURLOPT_HEADER, 0 );
		curl_setopt ( $ch, CURLOPT_RETURNTRANSFER, 1 );
		curl_setopt ( $ch, CURLOPT_URL, $url );
		curl_setopt ( $ch, CURLOPT_POSTFIELDS, $postFields );
		$exec_result = curl_exec ( $ch );
		curl_close ( $ch );
		
		log_message('error', "function(sendSMS_DXT):MOBILE($mobile) : ".$exec_result." in file".__FILE__." on Line ".__LINE__);
		
		$result=preg_split("/[,\n]/",$exec_result);
		
		if(isset($result[1]) && $result[1] == 0)
        {
            return OPT_CONST::SUCCESS;
        } else {
            log_message('error', "function(sendSMS_DXT):failed($mobile) : ".$exec_result." in file".__FILE__." on Line ".__LINE__);
			return OPT_CONST::FAILED;
        }
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
