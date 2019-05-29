<?php

require_once('modules/mysql.class.php');
include_once('public_models.php');		//加载数据库操作类
include_once('http.php');		//加载数据库操作类
class DWechat_Model extends Public_Models
{
	public function __construct()
	{
		header('Content-Type: text/html; charset=utf-8'); 
		parent::__construct();
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
	
	
	
	/*
		获取access_token
	*/
	public function getAccessToken($dealer_num,$is_refresh = 0)
	{

		$DelaerConst = "Dealer_".$dealer_num;

		$http_url = "https://api.weixin.qq.com/cgi-bin/token?grant_type=client_credential&appid=".$DelaerConst::WX_Appid."&secret=".$DelaerConst::WX_AppSecret;
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
		
		//$api_key = Payment_CONST::WX_API_Key;
		
		//$stringA="appid=".$appid."&body=".$body."&device_info=".$device_info."&fee_type=".$fee_type."&mch_id=".$mch_id."&nonce_str=".$nonce_str."";
		$stringSignTemp = $stringA;
		//$sign = strtoupper(MD5($stringSignTemp));
		
		//log_message('error', "getSign:stringSignTemp:".$stringSignTemp." in file".__FILE__." on Line ".__LINE__);
		
		$sign = sha1($stringSignTemp);
		return $sign;
	}
	
	
	
	
	
	/*
		获取openid
		
		scope为snsapi_base
	*/
	public function getInfoOpenid($dealer_num = G_CONST::EMPTY_STRING,$code = G_CONST::EMPTY_STRING)
	{	
		$result = array();
		$timestamp = time();
				
		if(!isset($dealer_num) || trim($dealer_num) == G_CONST::EMPTY_STRING)
		{
			log_message('error', "function(getInfoOpenid):lack of dealer_num"." in file".__FILE__." on Line ".__LINE__);
			return OPT_CONST::FAILED;
		}
		if(!isset($code) || trim($code) == G_CONST::EMPTY_STRING)
		{
			log_message('error', "function(getInfoOpenid):lack of code"." in file".__FILE__." on Line ".__LINE__);
			return OPT_CONST::FAILED;
		}


		$DelaerConst = "Dealer_".$dealer_num;
		if(!class_exists($DelaerConst))
		{
			log_message('error', "function(getInfoOpenid):DelaerConst not exist"." in file".__FILE__." on Line ".__LINE__);
			return OPT_CONST::FAILED;
		}

		
		//获取appid和appsecret
		$app_id = $DelaerConst::WX_Appid;
		$appsecret = $DelaerConst::WX_AppSecret;
		
		//根据code模拟授权，获取accesstoken、openid等
		$http_url = "https://api.weixin.qq.com/sns/oauth2/access_token?appid=".$app_id."&secret=".$appsecret."&code=".$code."&grant_type=authorization_code";
		$json_result = $this->httpGet($http_url);
		
		//返回结果
		//eg.:
		/*
			{
			    "access_token": "OezXcEiiBSKSxW0eoylIeNs1KBPjUUvNUEDZ1xjXmIiHHHLgDmAbi2gPYFKtXn0VQZ5fekbbOgDlSVDaWfoyNswBSMM37odubh3mNlChgKGz9P2L8fVXu9f-PY2mtsktO2ZPeKgZEmuaoOYHP5WfSA",
			    "expires_in": 7200,
			    "refresh_token": "OezXcEiiBSKSxW0eoylIeNs1KBPjUUvNUEDZ1xjXmIiHHHLgDmAbi2gPYFKtXn0VuSLTfiLG21YnYfeSwhPC9Pg-QwqxsTDTDfovrAax7cTkYsknJAnvEtK-nkYM0NO6kOxnfiB7bSF-N5NqiNCVtQ",
			    "openid": "oDDecs1VjfJiSqFyY2XF7noBKLNg",
			    "scope": "snsapi_base"
			}		
		*/
		log_message('error', "getAccessToken:httpGet:".$json_result." in file".__FILE__." on Line ".__LINE__);
		
		//拆解json成数组
		$result = $this->splitJsonString($json_result);
		if($result === false || isset($result['errcode']))
		{
			//获取access_token失败
			log_message('error', "getAccessToken:can not get openid:".$json_result." in file".__FILE__." on Line ".__LINE__);

			$json_result = $this->httpGet($http_url);
			$result = $this->splitJsonString($json_result);
			
			if($result === false || isset($result['errcode']))
			{
				//返回结果不是json
				log_message('error', "getAccessToken:result is not a jsonstring : ".$json_result." in file".__FILE__." on Line ".__LINE__);
				return OPT_CONST::FAILED;
			}
		}
		$open_id = $result['openid'];
		$access_token = $result['access_token'];
		$refresh_token = $result['refresh_token'];
		$scope = $result['scope'];
		
		$userinfo_ary['openid'] = $open_id;
		$userinfo_ary['code'] = $code;
		$userinfo_ary['access_token'] = $access_token;
		$userinfo_ary['refresh_token'] = $refresh_token;
		
		

		$account_data = $this->getGameAccountData($dealer_num,$open_id);
		if(is_array($account_data))
		{
			if($scope == "snsapi_base" && isset($account_data['is_refresh']) && $account_data['is_refresh'] == 1)
			{
				log_message('error', "getInfoOpenid:is_refresh:1"." in file".__FILE__." on Line ".__LINE__);
				return -2;
			}
			else
			{
				$host = $DelaerConst::AsynRequest_Host;
				$path = "/dwechat/wxauth_".$dealer_num."/updateUserInfo";
				$port = 80;

				//异步更新用户信息
				$param = array("open_id"=>$open_id,"access_token"=>$access_token,"dealer_num"=>$dealer_num);
				$this->doRequestWX($host,$path,$port,$param);
			}
		}
		else
		{
			$param = array("open_id"=>$open_id,"access_token"=>$access_token,"dealer_num"=>$dealer_num);
			$update_result = $this->updateUserInfo($param);
			if($update_result === -2)
			{
				return -2;
			}
		}
		
		$_SESSION['WxOpenID'] = $open_id;
		
		return $userinfo_ary;
	}
	


	
	public function updateUserInfo($userinfo_ary)
	{
		$timestamp = time();
		
		$open_id = $userinfo_ary['open_id'];
		$access_token = $userinfo_ary['access_token'];
		$dealer_num = $userinfo_ary['dealer_num'];
		
		if($open_id == G_CONST::EMPTY_STRING)
		{
			log_message('error', "updateWechatAccount:缺少openid:".$json_result." in file".__FILE__." on Line ".__LINE__);
			return -1;
		}
		
		$wechat_userinfo = $this->getWechatUserinfo($userinfo_ary);
		if(!is_array($wechat_userinfo))
		{
			$update_sql = 'update '.WX_Account.' set update_time='.$timestamp.',update_appid="'.$open_id.'",is_refresh=1 where open_id="'.$open_id.'"';
			$update_query = $this->execMysql($dealer_num,$update_sql);

			// $update_array['update_time'] = $timestamp;
			// $update_array['update_appid'] = $open_id;
			// $update_array['is_refresh'] = 1;
			// $update_query = $this->updateFunc("open_id",$open_id,WX_Account,$update_array);

			log_message('error', "updateWechatAccount:can not getWechatUserinfo:".$open_id." in file".__FILE__." on Line ".__LINE__);
			return -1;
		}
		
		if(!isset($wechat_userinfo['nickname']) || !isset($wechat_userinfo['headimgurl']))
		{
			log_message('error', "updateWechatAccount:can not get nickname headimgurl:".$open_id." in file".__FILE__." on Line ".__LINE__);
			return -2;
		}
		
		$nickname = $wechat_userinfo['nickname'];
		$headimgurl = $wechat_userinfo['headimgurl'];
		
		log_message('error', "updateWechatAccount:nickname:".$nickname." in file".__FILE__." on Line ".__LINE__);
		log_message('error', "updateWechatAccount:headimgurl:".$headimgurl." in file".__FILE__." on Line ".__LINE__);
		
		
		//判断open_id是否存在
		$account_where = 'open_id="'.$open_id.'"';
		$account_sql = 'select account_id from '.WX_Account.' where '.$account_where.'';
		$account_query = $this->execMysql($dealer_num,$account_sql);
		if(DB_CONST::DATA_NONEXISTENT != $account_query)
		{
			$account_id = $account_query[0]['account_id'];

			$update_sql = 'update '.WX_Account.' set update_time='.$timestamp.',update_appid="'.$open_id.'",nickname="'.$nickname.'",headimgurl="'.$headimgurl.'",is_refresh=0 where account_id="'.$account_id.'"';
			$update_query = $this->execMysql($dealer_num,$update_sql);
		}
		else
		{
			
			$insert_sql = 'insert into '.WX_Account.' set create_time='.$timestamp.',create_appid="'.$open_id.'",update_time="'.$timestamp.'",update_appid="'.$open_id.'",is_delete=0,open_id="'.$open_id.'",nickname="'.$nickname.'",headimgurl="'.$headimgurl.'",is_refresh=0';
		 	$insert_query = $this->execMysql($dealer_num,$insert_sql);
		 	
		 	$account_where = 'open_id="'.$open_id.'"';
			$account_sql = 'select account_id from '.WX_Account.' where '.$account_where.'';
			$account_query = $this->execMysql($dealer_num,$account_sql);
		 	$account_id = $account_query[0]['account_id'];

		 	$ticket_sql = 'insert into '.Room_Ticket.' set create_time='.$timestamp.',create_appid="'.$open_id.'",update_time="'.$timestamp.'",update_appid="'.$open_id.'",is_delete=0,account_id="'.$account_id.'",ticket_count=0';
		 	$ticket_query = $this->execMysql($dealer_num,$ticket_sql);
		}

		return true;
	}
	
	
	/*
		fsocketopen 请求
	*/
	protected function doRequestWX($host,$path,$port,$param)
	{ 
		//$param = array("open_id"=>$open_id,"wx_app_id"=>$app_id,"wx_app_secret"=>$appsecret);
	    //$host = "127.0.0.1";  
	    // $path = "/_wx_auth/updateWechatAccount";  
	    $query = isset($param)? http_build_query($param) : '';  
	  
	    //$port = 9505;  
	    $errno = 0;  
	    $errstr = '';  
	    $timeout = 10;  
	  
	    $fp = fsockopen($host, $port, $errno, $errstr, $timeout);  
	  
	    $out = "POST ".$path." HTTP/1.1\r\n";  
	    $out .= "host:".$host."\r\n";  
	    $out .= "content-length:".strlen($query)."\r\n";  
	    $out .= "content-type:application/x-www-form-urlencoded\r\n";  
	    $out .= "connection:close\r\n\r\n";  
	    $out .= $query;  
	  
	    fputs($fp, $out);  
	    fclose($fp);
	    
	    return true;
	}
	
	
	protected function getWechatUserData($open_id)
	{
		if($open_id == G_CONST::EMPTY_STRING)
		{
			log_message('error', "updateWechatAccount:缺少openid:".$json_result." in file".__FILE__." on Line ".__LINE__);
			return false;
		}
		
		$open_id = $arrData['open_id'];
		$access_token = $arrData['access_token'];
		
		$wechat_userinfo = $this->getWechatUserinfo($userinfo_ary);
		if(!is_array($wechat_userinfo))
		{
			log_message('error', "updateWechatAccount:can not getWechatUserinfo:".$open_id." in file".__FILE__." on Line ".__LINE__);
			return false;
		}
		
		$param = array("open_id"=>$open_id,"nickname"=>$wechat_userinfo['nickname'],"headimgurl"=>$wechat_userinfo['headimgurl']);
		
		return $this->JSON($param);
	}
	
	
	
	/*
		获取用户参数
	*/
	public function getWechatUserinfo($userinfo_ary)
	{
		
		$open_id = $userinfo_ary['open_id'];
		$access_token = $userinfo_ary['access_token'];
		$dealer_num = $userinfo_ary['dealer_num'];
		
		//$access_token = $this->getAccessTokenUserInfo($is_refresh = 1);

		log_message('error', "getAccessToken:access_token:".$access_token." in file".__FILE__." on Line ".__LINE__);

		if($access_token != OPT_CONST::DATA_NONEXISTENT)
		{
			//$http_url = Wechat::WX_Url_Userinfo."?access_token=".$access_token."&openid=".$open_id."&lang=zh_CN";
			$http_url = "https://api.weixin.qq.com/sns/userinfo?access_token=".$access_token."&openid=".$open_id."&lang=zh_CN";
			$json_result = $this->httpGet($http_url);

			log_message('error', "function(getWechatUserinfo):1st : $json_result"." in file".__FILE__." on Line ".__LINE__);

			$result = $this->splitJsonString($json_result);
			if($result === false)
			{
				//返回结果不是json
				log_message('error', "getAccessToken:result is not a jsonstring"." in file".__FILE__." on Line ".__LINE__);
				return OPT_CONST::DATA_NONEXISTENT;
			}
			else if(isset($result['errcode']))
			{
				$access_token = $this->getAccessTokenUserInfo($dealer_num,$is_refresh = 1);

				//$http_url = "https://api.weixin.qq.com/sns/userinfo?access_token=".$access_token."&openid=".$open_id."&lang=zh_CN";

				$http_url = "https://api.weixin.qq.com/cgi-bin/user/info?access_token=".$access_token."&openid=".$open_id."";

				$json_result = $this->httpGet($http_url);
				
				log_message('error', "function(getWechatUserinfo):2nd : $json_result"." in file".__FILE__." on Line ".__LINE__);
				$result = $this->splitJsonString($json_result);
				
				if($result === false)
				{
					//返回结果不是json
					log_message('error', "function(getWechatUserinfo):result is not a jsonstring"." in file".__FILE__." on Line ".__LINE__);
					return OPT_CONST::DATA_NONEXISTENT;
				}
				else if(isset($result['errcode']))
				{
					log_message('error', "function(getWechatUserinfo):can not get userinfo:".$json_result." in file".__FILE__." on Line ".__LINE__);
					return OPT_CONST::DATA_NONEXISTENT;
				}
				else
				{
					//返回access_token
					return $result;
				}
			}
			else
			{
				//返回access_token
				return $result;
			}
		}
		else
		{
			return OPT_CONST::DATA_NONEXISTENT;
		}
	}
	
	
	
	
	/*
		获取access_token
	*/
	public function getAccessTokenUserInfo($dealer_num,$is_refresh = 0)
	{
		$timestamp = time();
		
		$DelaerConst = "Dealer_".$dealer_num;

		//获取appid和appsecret
		$app_id = $DelaerConst::WX_Appid;
		$appsecret = $DelaerConst::WX_AppSecret;


		//$http_url = "https://api.weixin.qq.com/sns/oauth2/access_token?appid=".$app_id."&secret=".$appsecret."&code=".$code."&grant_type=authorization_code";
		$http_url = "https://api.weixin.qq.com/cgi-bin/token?grant_type=client_credential&appid=".$app_id."&secret=".$appsecret;
		
		log_message('error', "function(getAccessToken):http_url:".$http_url." in file".__FILE__." on Line ".__LINE__);

		$json_result = $this->httpGet($http_url);
		$result = $this->splitJsonString($json_result);
		if($result === false || isset($result['errcode']))
		{
			//返回结果不是json
			log_message('error', "function(getAccessToken):result is not a jsonstring"." in file".__FILE__." on Line ".__LINE__);
			
			$json_result = $this->httpGet($http_url);
			$result = $this->splitJsonString($json_result);
			if($result === false || isset($result['errcode']))
			{
				log_message('error', "function(getAccessToken):result is not a jsonstring"." in file".__FILE__." on Line ".__LINE__);
				return OPT_CONST::DATA_NONEXISTENT;
			}
			else
			{
				//返回access_token
				$access_token = $result["access_token"];
			}
		}
		else
		{
			//返回access_token
			$access_token = $result["access_token"];
		}

		return $access_token;


		

		// $at_sql = 'select value from '.Server_Parameter.' where key="wx_AccessTokenGame"';
		// $at_query = $this->execMysql($dealer_num,$at_sql);
		// if($at_query == DB_CONST::DATA_NONEXISTENT)
		// {
		// 	$insert_at_sql = 'insert into '.Server_Parameter.' set value="",is_delete=0 where key="wx_AccessTokenGame"';
		// 	$insert_at_query = $this->execMysql($dealer_num,$insert_at_sql);
		// 	$access_token = G_CONST::EMPTY_STRING;
		// }
		// else
		// {
		// 	$access_token = $at_query[0]["value"];
		// }

		// $atct_sql = 'select value from '.Server_Parameter.' where key="wx_AccessTokenGame_CreateTime"';
		// $atct_query = $this->execMysql($dealer_num,$atct_sql);
		// if($atct_query == DB_CONST::DATA_NONEXISTENT)
		// {
		// 	$insert_atct_sql = 'insert into '.Server_Parameter.' set value="",is_delete=0 where key="wx_AccessTokenGame_CreateTime"';
		// 	$insert_atct_query = $this->execMysql($dealer_num,$insert_atct_sql);
		// 	$token_createTime = 0;
		// }
		// else
		// {
		// 	$token_createTime = $at_query[0]["value"];
		// }

		// if($is_refresh == 1 || $access_token == G_CONST::EMPTY_STRING || $timestamp >= ($token_createTime+3600))
		// {




		// $access_token =  $this->getDataValue($database,Server_Parameter,"value",array("key"=>"wx_AccessTokenGame"));
		// if($access_token == DB_CONST::DATA_NONEXISTENT)
		// {
		// 	$p_id = $this->getInsertID($database,Server_Parameter, array("key"=>"wx_AccessTokenGame","value"=>G_CONST::EMPTY_STRING,"is_delete"=>G_CONST::IS_FALSE));
		// 	$access_token = G_CONST::EMPTY_STRING;
		// }
		// $token_createTime = $this->getDataValue($database,Server_Parameter,"value",array("key"=>"wx_AccessTokenGame_CreateTime"));
		// if($token_createTime == DB_CONST::DATA_NONEXISTENT)
		// {
		// 	$p_id = $this->getInsertID($database,Server_Parameter, array("key"=>"wx_AccessTokenGame_CreateTime","value"=>G_CONST::EMPTY_STRING,"is_delete"=>G_CONST::IS_FALSE));
		// 	$token_createTime = 0;
		// }

		// $access_token = G_CONST::EMPTY_STRING;
		// $token_createTime = 0;
		// if($is_refresh == 1 || $access_token == G_CONST::EMPTY_STRING || $timestamp >= ($token_createTime+3600))
		// {
		// 	//$http_url = "https://api.weixin.qq.com/sns/oauth2/access_token?appid=".$app_id."&secret=".$appsecret."&code=".$code."&grant_type=authorization_code";
		// 	$http_url = "https://api.weixin.qq.com/cgi-bin/token?grant_type=client_credential&appid=".$app_id."&secret=".$appsecret;
			
		// 	log_message('error', "function(getAccessToken):http_url:".$http_url." in file".__FILE__." on Line ".__LINE__);

		// 	$json_result = $this->httpGet($http_url);
		// 	$result = $this->splitJsonString($json_result);
		// 	if($result === false || isset($result['errcode']))
		// 	{
		// 		//返回结果不是json
		// 		log_message('error', "function(getAccessToken):result is not a jsonstring"." in file".__FILE__." on Line ".__LINE__);
				
		// 		$json_result = $this->httpGet($http_url);
		// 		$result = $this->splitJsonString($json_result);
		// 		if($result === false || isset($result['errcode']))
		// 		{
		// 			log_message('error', "function(getAccessToken):result is not a jsonstring"." in file".__FILE__." on Line ".__LINE__);
		// 			return OPT_CONST::DATA_NONEXISTENT;
		// 		}
		// 		else
		// 		{
		// 			//返回access_token
		// 			$access_token = $result["access_token"];
		// 		}
		// 	}
		// 	else
		// 	{
		// 		//返回access_token
		// 		$access_token = $result["access_token"];
		// 	}
		// 	$query_1 = $this->updateFunc($database,"key","wx_AccessTokenGame",Server_Parameter,array("value"=>$access_token));
		// 	$query_2 = $this->updateFunc($database,"key","wx_AccessTokenGame_CreateTime",Server_Parameter,array("value"=>$timestamp));
		// }
		
		// //返回access_token
		// return $access_token;
	}



	public function getGameAccountData($dealer_num,$open_id)
	{
		//判断open_id是否存在
		$account_where = 'open_id="'.$open_id.'"';
		$account_sql = 'select account_id,is_refresh from '.WX_Account.' where '.$account_where.'';
		$account_query = $this->execMysql($dealer_num,$account_sql);
		if(DB_CONST::DATA_NONEXISTENT != $account_query)
		{
			$account_id = $account_query[0]['account_id'];
			$is_refresh = $account_query[0]['is_refresh'];

			log_message('error', "getGameAccountData: account exist :".$account_id.",is_refresh:".$is_refresh." in file".__FILE__." on Line ".__LINE__);

			return array("account_id"=>$account_id,"is_refresh"=>$is_refresh);
		}
		else
		{
			log_message('error', "getGameAccountData: account not exist :"." in file".__FILE__." on Line ".__LINE__);
			return false;
		}
	}




	/*
		操作数据库
	*/
	protected function execMysql($dealer_num,$sql)
	{
		$DelaerConst = "Dealer_".$dealer_num;


		$hostname = $DelaerConst::MYSQL_Host;
        $username = $DelaerConst::MYSQL_Username;
        $password = $DelaerConst::MYSQL_Pwd;
        $database = $DelaerConst::MYSQL_DB;

		$MMYSQL = new MySQL($hostname,$username,$password,$database,3306,"utf8mb4");

		$query = $MMYSQL->query($sql);

		if(is_array($query) && count($query) > 0 )
		{
			return $query;
		}
		else
		{
			return DB_CONST::DATA_NONEXISTENT;
		}
	}



	public function checkGameOpenID($dealer_num,$open_id)
	{
		$database = "dealer_".$dealer_num;


		//判断open_id是否存在
		$account_where = 'open_id="'.$open_id.'"';
		$account_sql = 'select account_id from '.WX_Account.' where '.$account_where.'';
		$account_query = $this->getDataBySql($database,1,$account_sql);
		if(DB_CONST::DATA_NONEXISTENT != $account_query)
		{
			$account_id = $account_query['account_id'];
			log_message('error', "checkGameOpenID: account exist :".$account_id." in file".__FILE__." on Line ".__LINE__);
			return true;
		}
		else
		{
			log_message('error', "checkGameOpenID: account not exist :"." in file".__FILE__." on Line ".__LINE__);
			return false;
		}

		/*
		$hostname = "rds7u8lb6ypu7ef35e8lq.mysql.rds.aliyuncs.com";
        $username = "doing_admin";
        $password = "doing123";
        $database = "game_dev";

		$MMYSQL = new MySQL($hostname,$username,$password,$database,3306,"utf8mb4");

		$account_where = 'open_id="'.$open_id.'"';
		$account_sql = 'select account_id from wechat_account where '.$account_where.'';
		$account_query = $MMYSQL->query($account_sql);

		if(is_array($account_query) && count($account_query) > 0 )
		{
			$account_id = $account_query[0]['account_id'];

			log_message('error', "checkGameOpenID: account exist :".$account_id." in file".__FILE__." on Line ".__LINE__);

			return true;
		}
		else
		{
			log_message('error', "checkGameOpenID: account not exist :"." in file".__FILE__." on Line ".__LINE__);

			return false;
		}
		*/
	}
	
	
	
}