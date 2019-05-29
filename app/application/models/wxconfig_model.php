<?php

require_once('modules/mysql.class.php');
include_once('public_models.php');		//加载数据库操作类
include_once('http.php');		//加载数据库操作类
class Wxconfig_Model extends Public_Models
{
	public function __construct()
	{
		header('Content-Type: text/html; charset=utf-8'); 
		parent::__construct();
	}
	
	
	
	
	
	/*
		jsapi config
	*/
	public function getWxConfig($dealer_num)
	{
		if(!empty($_SERVER["REQUEST_URI"])) 
		{ 
			$scrtName = $_SERVER["REQUEST_URI"]; 
			$nowurl = $scrtName; 
			$nowurl = base_url($nowurl);
		} 
		else 
		{ 
			$scrtName = $_SERVER["PHP_SELF"]; 
			if(empty($_SERVER["QUERY_STRING"])) 
			{ 
				$nowurl = $scrtName; 
			} 
			else 
			{ 
				$nowurl = $scrtName."?".$_SERVER["QUERY_STRING"]; 
			} 
			
			log_message('error', "createWxSignature:PHP_SELF:".$nowurl." in file".__FILE__." on Line ".__LINE__);
			
			$nowurl = base_url($nowurl);
		} 
		
		if($this->is_weixin())
		{
			$jsapi_ticket = $this->getJsapiTicket($dealer_num);
		}
		else
		{
			$jsapi_ticket = OPT_CONST::DATA_NONEXISTENT;
		}
		
		if(OPT_CONST::DATA_NONEXISTENT == $jsapi_ticket)
		{
			$jsapi_ticket = "";
		}
		
		$parameters['jsapi_ticket'] = $jsapi_ticket;
		$parameters['noncestr'] = $this->createNonceStr();
		$parameters['timestamp'] = time();
		$parameters['url'] = $nowurl;
		
		$signature =  $this->getSign($parameters);
		
		//log_message('error', "createWxSignature:sgin:".$signature." in file".__FILE__." on Line ".__LINE__);
		
		$DelaerConst = "Dealer_".$dealer_num;

		$parameters['signature'] = $signature;
		$parameters['appId'] = $DelaerConst::WX_Appid;
		$parameters['debug'] = "false";
		$parameters['nonceStr'] = $parameters['noncestr'];
		
		return $parameters;
	}




	/*
		获取getJsapiTicket
	*/
	public function getJsapiTicket($dealer_num)
	{
		$timestamp = time();



		

		$jsapi_ticket_sql = 'select value from '.Server_Parameter.' where `key`="wx_jsapi_ticket"';
		$jsapi_ticket_query = $this->execMysql($dealer_num,$jsapi_ticket_sql);
		if($jsapi_ticket_query == DB_CONST::DATA_NONEXISTENT)
		{
			$insert_sql = 'insert into '.Server_Parameter.' set value="",is_delete=0 where `key`="wx_jsapi_ticket"';
		 	$insert_query = $this->execMysql($dealer_num,$insert_sql);
		 	$jsapi_ticket = G_CONST::EMPTY_STRING;
		}
		else
		{
			$jsapi_ticket = $jsapi_ticket_query[0]['value'];
		}

		$jsapi_ticket_time_sql = 'select value from '.Server_Parameter.' where `key`="wx_jsapi_ticket_time"';
		$jsapi_ticket_time_query = $this->execMysql($dealer_num,$jsapi_ticket_time_sql);
		if($jsapi_ticket_time_query == DB_CONST::DATA_NONEXISTENT)
		{
			$insert_sql = 'insert into '.Server_Parameter.' set value="",is_delete=0 where `key`="wx_jsapi_ticket_time"';
		 	$insert_query = $this->execMysql($dealer_num,$insert_sql);
		 	$jsapi_ticket_time = 0;
		}
		else
		{
			$jsapi_ticket_time = $jsapi_ticket_time_query[0]['value'];
		}
		
		//log_message('error', "function(getJsapiTicket):jsapi_ticket:".$jsapi_ticket." in file".__FILE__." on Line ".__LINE__);
		//log_message('error', "function(getJsapiTicket):jsapi_ticket_time:".$jsapi_ticket_time." in file".__FILE__." on Line ".__LINE__);
		
		
		if($jsapi_ticket == G_CONST::EMPTY_STRING || $timestamp >= ($jsapi_ticket_time+3600))
		{
			log_message('error', "function(getJsapiTicket):jsapi_ticket:".$jsapi_ticket." in file".__FILE__." on Line ".__LINE__);
			log_message('error', "function(getJsapiTicket):jsapi_ticket_time:".$jsapi_ticket_time." in file".__FILE__." on Line ".__LINE__);
			log_message('error', "function(getJsapiTicket):update jsapiticket:"." in file".__FILE__." on Line ".__LINE__);
			
			$accessToken = $this->getAccessToken($dealer_num);
			// 如果是企业号用以下 URL 获取 ticket
			$url = "https://api.weixin.qq.com/cgi-bin/ticket/getticket?type=jsapi&access_token=$accessToken";
			
			$json_result = $this->httpGet($url);
			$result = $this->splitJsonString($json_result);
			if($result === false)
			{
				//返回结果不是json
				log_message('error', "function(getAccessToken):result is not a jsonstring"." in file".__FILE__." on Line ".__LINE__);
				return OPT_CONST::DATA_NONEXISTENT;
			}
			else if(isset($result['errcode']) && $result['errcode'] != 0)
			{
				$access_token = $this->getAccessToken($dealer_num,$is_refresh = 1);
				
				$url = "https://api.weixin.qq.com/cgi-bin/ticket/getticket?type=jsapi&access_token=$accessToken";
				$json_result = $this->httpGet($url);
				
				$result = $this->splitJsonString($json_result);
				if($result === false)
				{
					//返回结果不是json
					log_message('error', "function(getAccessToken):result is not a jsonstring"." in file".__FILE__." on Line ".__LINE__);
					return OPT_CONST::DATA_NONEXISTENT;
				}
				else if(isset($result['errcode']) && $result['errcode'] != 0)
				{
					//获取access_token失败
					log_message('error', "function(getAccessToken):can not get accesstoken:".$json_result." in file".__FILE__." on Line ".__LINE__);
					return OPT_CONST::DATA_NONEXISTENT;
				}
				else
				{
					//返回access_token
					$jsapi_ticket = $result["ticket"];
				}
			}
			else
			{
				//返回access_token
				$jsapi_ticket = $result["ticket"];
			}
			
			$update_sql = 'update '.Server_Parameter.' set value="'.$jsapi_ticket.'" where `key`="wx_jsapi_ticket"';
			$update_query = $this->execMysql($dealer_num,$update_sql);

			$update_sql = 'update '.Server_Parameter.' set value="'.$timestamp.'" where `key`="wx_jsapi_ticket_time"';
			$update_query = $this->execMysql($dealer_num,$update_sql);
		}
		
		//返回access_token
		return $jsapi_ticket;
	}
	
	
	/*
		获取access_token
	*/
	public function getAccessToken($dealer_num,$is_refresh = 0)
	{

		$DelaerConst = "Dealer_".$dealer_num;

		$http_url = Wechat::WX_Url_AccessToken."&appid=".$DelaerConst::WX_Appid."&secret=".$DelaerConst::WX_AppSecret;
		$json_result = $this->httpGet($http_url);
		$result = $this->splitJsonString($json_result);
		if($result === false)
		{
			//返回结果不是json
			log_message('error', "function(getAccessToken):result is not a jsonstring"." in file".__FILE__." on Line ".__LINE__);
			return OPT_CONST::DATA_NONEXISTENT;
		}
		else if(isset($result['errcode']))
		{
			//获取access_token失败
			log_message('error', "function(getAccessToken):can not get accesstoken:".$json_result." in file".__FILE__." on Line ".__LINE__);
			return OPT_CONST::DATA_NONEXISTENT;
		}
		else
		{
			//返回access_token
			$access_token = $result["access_token"];
		}
		
		//返回access_token
		return $access_token;
	}
	
	
	
	/*
		 生成随机字符串
	 */
	 private function createNonceStr()
	 {
		$mtime=explode(' ',microtime());  
		$mTimestamp = $mtime[1] . substr($mtime[0],2,3);
		
		$order_code = $mTimestamp;
		
		
		for($i=0;$i<6;$i++)
		{
			$order_code .= rand(0,9);
		}
		
		return md5($order_code);
	 }
	
	
	
	
	/*
		 生成签名
	*/
	private function getSign($parameters)
	{
		$stringA = "";
		
		$i = 0;
		foreach($parameters as $key=>$value)
		{
			if($i == 0)
			{
				$stringA .= $key."=".$value;
			}
			else
			{
				$stringA .= "&".$key."=".$value;
			}
			$i++;
		}
		$stringSignTemp = $stringA;
		$sign = sha1($stringSignTemp);
		return $sign;
	}
	
	
	/*
		判断是否微信的
	*/
	public function is_weixin()
	{ 
		if ( strpos($_SERVER['HTTP_USER_AGENT'], 'MicroMessenger') !== false ) {
				return true;
		}	
		return false;
	}
	
	private function httpGet($url) {
		$curl = curl_init();
		curl_setopt($curl, CURLOPT_RETURNTRANSFER, true);
		curl_setopt($curl, CURLOPT_TIMEOUT, 500);
		// 为保证第三方服务器与微信服务器之间数据传输的安全性，所有微信接口采用https方式调用，必须使用下面2行代码打开ssl安全校验。
		// 如果在部署过程中代码在此处验证失败，请到 http://curl.haxx.se/ca/cacert.pem 下载新的证书判别文件。
		curl_setopt($curl, CURLOPT_SSL_VERIFYPEER, true);
		curl_setopt($curl, CURLOPT_SSL_VERIFYHOST, 0);
		curl_setopt($curl, CURLOPT_URL, $url);
		
		$res = curl_exec($curl);
		curl_close($curl);
		
		return $res;
	}
	
	
}