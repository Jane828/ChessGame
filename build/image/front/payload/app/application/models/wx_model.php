<?php

require_once('modules/mysql.class.php');
include_once('public_models.php');		//加载数据库操作类
include_once('http.php');		//加载数据库操作类
class Wx_Model extends Public_Models
{
	public function __construct()
	{
		header('Content-Type: text/html; charset=utf-8'); 
		parent::__construct();
	}
	
	
	
	
	
	/*
		获取getJsapiTicket
	*/
	public function getJsapiTicket()
	{
		$timestamp = time();
		
		$jsapi_ticket =  $this->getDataValue(Server_Parameter,"value",array("key"=>"wx_jsapi_ticket"));
		
		if($jsapi_ticket == DB_CONST::DATA_NONEXISTENT)
		{
			$p_id = $this->getInsertID(Server_Parameter, array("key"=>"wx_jsapi_ticket","value"=>G_CONST::EMPTY_STRING,"is_delete"=>G_CONST::IS_FALSE));
			$jsapi_ticket = G_CONST::EMPTY_STRING;
		}
		
		$jsapi_ticket_time = $this->getDataValue(Server_Parameter,"value",array("key"=>"wx_jsapi_ticket_time"));
		
		if($jsapi_ticket_time == DB_CONST::DATA_NONEXISTENT)
		{
			$p_id = $this->getInsertID(Server_Parameter, array("key"=>"wx_jsapi_ticket_time","value"=>G_CONST::EMPTY_STRING,"is_delete"=>G_CONST::IS_FALSE));
			$jsapi_ticket_time = 0;
		}
		
		if($jsapi_ticket == G_CONST::EMPTY_STRING || $timestamp >= ($jsapi_ticket_time+3600))
		{
			$accessToken = $this->getAccessToken();
			// 如果是企业号用以下 URL 获取 ticket
			// $url = "https://qyapi.weixin.qq.com/cgi-bin/get_jsapi_ticket?access_token=$accessToken";
			$url = "https://api.weixin.qq.com/cgi-bin/ticket/getticket?type=jsapi&access_token=$accessToken";
			
			//$httpClass = http::getInstance();
			//$json_result = $httpClass->showRequest($url);
			
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
				$access_token = $this->getAccessToken($is_refresh = 1);
				
				$url = "https://api.weixin.qq.com/cgi-bin/ticket/getticket?type=jsapi&access_token=$accessToken";
			
				//$httpClass = http::getInstance();
				//$json_result = $httpClass->showRequest($url);
				
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
			
			$query_1 = $this->updateFunc("key","wx_jsapi_ticket",Server_Parameter,array("value"=>$jsapi_ticket));
			$query_2 = $this->updateFunc("key","wx_jsapi_ticket_time",Server_Parameter,array("value"=>$timestamp));
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
	public function getAccessToken($is_refresh = 0)
	{
		$timestamp = time();
		
		$access_token =  $this->getDataValue(Server_Parameter,"value",array("key"=>"wx_AccessToken"));
		
		if($access_token == DB_CONST::DATA_NONEXISTENT)
		{
			$p_id = $this->getInsertID(Server_Parameter, array("key"=>"wx_AccessToken","value"=>G_CONST::EMPTY_STRING,"is_delete"=>G_CONST::IS_FALSE));
			$access_token = G_CONST::EMPTY_STRING;
		}
		
		$token_createTime = $this->getDataValue(Server_Parameter,"value",array("key"=>"wx_AccessToken_CreateTime"));
		
		if($token_createTime == DB_CONST::DATA_NONEXISTENT)
		{
			$p_id = $this->getInsertID(Server_Parameter, array("key"=>"wx_AccessToken_CreateTime","value"=>G_CONST::EMPTY_STRING,"is_delete"=>G_CONST::IS_FALSE));
			$token_createTime = 0;
		}
		
		if($is_refresh == 1 || $access_token == G_CONST::EMPTY_STRING || $timestamp >= ($token_createTime+3600))
		{
			$http_url = Wechat::WX_Url_AccessToken."&appid=".Wechat::WX_Appid."&secret=".Wechat::WX_AppSecret;
		    //$httpClass = http::getInstance();
			//$json_result = $httpClass->showRequest($http_url);
			
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
			
			$query_1 = $this->updateFunc("key","wx_AccessToken",Server_Parameter,array("value"=>$access_token));
			$query_2 = $this->updateFunc("key","wx_AccessToken_CreateTime",Server_Parameter,array("value"=>$timestamp));
		}
		
		//返回access_token
		return $access_token;
	}
	
	
	/*
		获取用户参数
	*/
	public function getWechatUserinfo($open_id)
	{
		$access_token = $this->getAccessToken();
		if($access_token != OPT_CONST::DATA_NONEXISTENT)
		{
			$http_url = Wechat::WX_Url_Userinfo."?access_token=".$access_token."&openid=".$open_id."&lang=zh_CN";
			//$httpClass = http::getInstance();
			//$json_result = $httpClass->showRequest($http_url);
			
			$json_result = $this->httpGet($http_url);
			
			log_message('error', "function(getWechatUserinfo):$json_result"." in file".__FILE__." on Line ".__LINE__);
			$result = $this->splitJsonString($json_result);
			
			if($result === false)
			{
				//返回结果不是json
				log_message('error', "getAccessToken:result is not a jsonstring"." in file".__FILE__." on Line ".__LINE__);
				return OPT_CONST::DATA_NONEXISTENT;
			}
			else if(isset($result['errcode']))
			{
				//获取access_token失败
				log_message('error', "getAccessToken:can not get accesstoken:".$json_result." in file".__FILE__." on Line ".__LINE__);
				//return OPT_CONST::DATA_NONEXISTENT;
				
				$access_token = $this->getAccessToken($is_refresh = 1);
				if($access_token != OPT_CONST::DATA_NONEXISTENT)
				{
					$http_url = Wechat::WX_Url_Userinfo."?access_token=".$access_token."&openid=".$open_id."&lang=zh_CN";
					//$httpClass = http::getInstance();
					//$json_result = $httpClass->showRequest($http_url);
					$json_result = $this->httpGet($http_url);
					
					log_message('error', "function(getWechatUserinfo):$json_result"." in file".__FILE__." on Line ".__LINE__);
					$result = $this->splitJsonString($json_result);
					
					if($result === false)
					{
						//返回结果不是json
						log_message('error', "getAccessToken:result is not a jsonstring"." in file".__FILE__." on Line ".__LINE__);
						return OPT_CONST::DATA_NONEXISTENT;
					}
					else if(isset($result['errcode']))
					{
						//获取access_token失败
						log_message('error', "getAccessToken:can not get accesstoken:".$json_result." in file".__FILE__." on Line ".__LINE__);
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
					return OPT_CONST::DATA_NONEXISTENT;
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
		获取openid
	*/
	public function getOpenid($code = G_CONST::EMPTY_STRING)
	{	
		$result = array();
		$timestamp = time();
				
		if(!isset($code) || trim($code) == G_CONST::EMPTY_STRING)
		{
			log_message('error', "function(getOpenid):lack of code"." in file".__FILE__." on Line ".__LINE__);
			return $this->missingPrameterArr($operation,"code");
		}
		
		//获取appid和appsecret
		$app_id = Wechat::WX_Appid;
		$appsecret = Wechat::WX_AppSecret;
		
		//根据code模拟授权，获取accesstoken、openid等
		$http_url = "https://api.weixin.qq.com/sns/oauth2/access_token?appid=".$app_id."&secret=".$appsecret."&code=".$code."&grant_type=authorization_code";
		
		//log_message('error', "getAccessToken:http_url : ".$http_url." in file".__FILE__." on Line ".__LINE__);
		
		//$httpClass = http::getInstance();
		//$json_result = $httpClass->showRequest($http_url);
		
		$json_result = $this->httpGet($http_url);
		
		//$json_result = $this->httpGet($http_url);
		
		//log_message('error', "getAccessToken:json_result : ".$json_result." in file".__FILE__." on Line ".__LINE__);
		
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
		
		//拆解json成数组
		$result = $this->splitJsonString($json_result);
		
		if($result === false)
		{
			//返回结果不是json
			log_message('error', "getAccessToken:result is not a jsonstring : ".$json_result." in file".__FILE__." on Line ".__LINE__);
			return OPT_CONST::FAILED;
		}
		else if(isset($result['errcode']))
		{
			//获取access_token失败
			log_message('error', "getAccessToken:can not get openid:".$json_result." in file".__FILE__." on Line ".__LINE__);
			return OPT_CONST::FAILED;
		}
		else
		{
			//绑定uid和openid
			//判断用户是否绑定open_id
			//$where = array("open_id"=>$open_id,"is_delete"=>G_CONST::IS_FALSE); 
			//$lease_price = $this->getDataValue(T_Mall_Goods_Lease,"u_id",$lease_where);
			//$uid_openid =
			
			//返回open_id
			//log_message('error', "getAccessToken:json_result:".$json_result." in file".__FILE__." on Line ".__LINE__);
			
			$open_id = $result['openid'];
			/*
			$update_result = @$this->updateWechatAccount($open_id);
			if($update_result == -1)
			{
				//获取access_token失败
				log_message('error', "getAccessToken:can not get userinfo:"." in file".__FILE__." on Line ".__LINE__);
				return OPT_CONST::FAILED;
			}
			*/
			//$result['is_subscribe'] = $update_result['is_subscribe'];
			
			return $result;
		}
	}
	
	
	
	
	
	public function updateWechatAccount($open_id)
	{
		/*
		$result['account_id'] = 1;
		$result['is_subscribe'] = 0;
		return $result;
		*/
		$timestamp = time();
		
		if($open_id == G_CONST::EMPTY_STRING)
		{
			log_message('error', "updateWechatAccount:缺少openid:".$json_result." in file".__FILE__." on Line ".__LINE__);
			return -1;
		}
		
		$wechat_userinfo = $this->getWechatUserinfo($open_id);
		if(!is_array($wechat_userinfo))
		{
			log_message('error', "updateWechatAccount:can not getWechatUserinfo:".$open_id." in file".__FILE__." on Line ".__LINE__);
			return -1;
		}
		
		$bool_subscribe = G_CONST::IS_FALSE;
		
		//判断用户是否已有记录
		$query_account = $this->getData(Wechat_Account,'','account_id,is_subscribe',array('open_id'=>$open_id),1);

		if($query_account == DB_CONST::DATA_NONEXISTENT)
		{
			//记录不存在，添加记录
			$account_array['create_time'] = $timestamp;
			$account_array['update_time'] = $timestamp;
			$account_array['is_delete'] = G_CONST::IS_FALSE;
			$account_array['open_id'] = $open_id;
			
			if(isset($wechat_userinfo['subscribe']))
			{
				$account_array['is_subscribe'] = $wechat_userinfo['subscribe'];
				if($wechat_userinfo['subscribe'] == 1)
				{
					$bool_subscribe = G_CONST::IS_TRUE;
				}
			}
			if(isset($wechat_userinfo['nickname']))
			{
				$account_array['nickname'] = $wechat_userinfo['nickname'];
			}
			if(isset($wechat_userinfo['sex']))
			{
				$account_array['sex'] = $wechat_userinfo['sex'];
			}
			if(isset($wechat_userinfo['language']))
			{
				$account_array['language'] = $wechat_userinfo['language'];
			}
			if(isset($wechat_userinfo['city']))
			{
				$account_array['city'] = $wechat_userinfo['city'];
			}
			if(isset($wechat_userinfo['province']))
			{
				$account_array['province'] = $wechat_userinfo['province'];
			}
			if(isset($wechat_userinfo['country']))
			{
				$account_array['country'] = $wechat_userinfo['country'];
			}
			if(isset($wechat_userinfo['headimgurl']))
			{
				$account_array['headimgurl'] = $wechat_userinfo['headimgurl'];
			}
			if(isset($wechat_userinfo['subscribe_time']))
			{
				$account_array['subscribe_time'] = $wechat_userinfo['subscribe_time'];
			}
			if(isset($wechat_userinfo['remark']))
			{
				$account_array['remark'] = $wechat_userinfo['remark'];
			}
			if(isset($wechat_userinfo['groupid']))
			{
				$account_array['groupid'] = $wechat_userinfo['groupid'];
			}
			/*
			$account_array['is_subscribe'] = $wechat_userinfo['subscribe'];
			$account_array['nickname'] = $wechat_userinfo['nickname'];
			$account_array['sex'] = $wechat_userinfo['sex'];
			$account_array['language'] = $wechat_userinfo['language'];
			$account_array['city'] = $wechat_userinfo['city'];
			$account_array['province'] = $wechat_userinfo['province'];
			$account_array['country'] = $wechat_userinfo['country'];
			$account_array['headimgurl'] = $wechat_userinfo['headimgurl'];
			$account_array['subscribe_time'] = $wechat_userinfo['subscribe_time'];
			$account_array['remark'] = $wechat_userinfo['remark'];
			$account_array['groupid'] = $wechat_userinfo['groupid'];
			*/
			$account_id = $this->getInsertID(Wechat_Account, $account_array);
			$is_subscribe = G_CONST::IS_FALSE;
			
			
			
		}
		else
		{			
			$account_array['update_time'] = $timestamp;
			$account_array['is_delete'] = G_CONST::IS_FALSE;
			$account_array['open_id'] = $open_id;
			
			if($query_account['is_subscribe'] == 1)	//已关注
			{
				if($wechat_userinfo['subscribe'] == 1)
				{
					$bool_subscribe = G_CONST::IS_TRUE;
				}
			}
			else
			{
				if(isset($wechat_userinfo['subscribe']))
				{
					if($wechat_userinfo['subscribe'] == 1)
					{
						$bool_refresh = G_CONST::IS_TRUE;
					}
				}
			}
			
			if(isset($wechat_userinfo['subscribe']))
			{
				$account_array['is_subscribe'] = $wechat_userinfo['subscribe'];
				if($wechat_userinfo['subscribe'] == 1)
				{
					$bool_subscribe = G_CONST::IS_TRUE;
				}
			}
			
			if(isset($wechat_userinfo['nickname']))
			{
				$account_array['nickname'] = $wechat_userinfo['nickname'];
			}
			if(isset($wechat_userinfo['sex']))
			{
				$account_array['sex'] = $wechat_userinfo['sex'];
			}
			if(isset($wechat_userinfo['language']))
			{
				$account_array['language'] = $wechat_userinfo['language'];
			}
			if(isset($wechat_userinfo['city']))
			{
				$account_array['city'] = $wechat_userinfo['city'];
			}
			if(isset($wechat_userinfo['province']))
			{
				$account_array['province'] = $wechat_userinfo['province'];
			}
			if(isset($wechat_userinfo['country']))
			{
				$account_array['country'] = $wechat_userinfo['country'];
			}
			if(isset($wechat_userinfo['headimgurl']))
			{
				$account_array['headimgurl'] = $wechat_userinfo['headimgurl'];
			}
			if(isset($wechat_userinfo['subscribe_time']))
			{
				$account_array['subscribe_time'] = $wechat_userinfo['subscribe_time'];
			}
			if(isset($wechat_userinfo['remark']))
			{
				$account_array['remark'] = $wechat_userinfo['remark'];
			}
			if(isset($wechat_userinfo['groupid']))
			{
				$account_array['groupid'] = $wechat_userinfo['groupid'];
			}
			
			$this->updateFunc("open_id",$open_id,Wechat_Account,$account_array);
			
			//记录已存在
			$account_id = $query_account['account_id'];
			//$is_subscribe = $query_account['is_subscribe'];
		}
		
		$result['account_id'] = $account_id;
		$result['is_subscribe'] = $bool_subscribe;
		
		return $result;
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
	public function getWxConfig()
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
		
		
		
		$jsapi_ticket = $this->getJsapiTicket();
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
		
		$parameters['signature'] = $signature;
		$parameters['appId'] = Wechat::WX_Appid;
		$parameters['debug'] = "true";
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
	public function getInfoOpenid($code = G_CONST::EMPTY_STRING,$host,$path,$port)
	{	
		$result = array();
		$timestamp = time();
				
		if(!isset($code) || trim($code) == G_CONST::EMPTY_STRING)
		{
			log_message('error', "function(getInfoOpenid):lack of code"." in file".__FILE__." on Line ".__LINE__);
			return $this->missingPrameterArr($operation,"code");
		}
		
		//获取appid和appsecret
		$app_id = Wechat::WX_Appid;
		$appsecret = Wechat::WX_AppSecret;
		
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
		
		$userinfo_ary['open_id'] = $open_id;
		$userinfo_ary['host'] = $host;
		$userinfo_ary['path'] = $path;
		$userinfo_ary['port'] = $port;
		$userinfo_ary['code'] = $code;
		$userinfo_ary['access_token'] = $access_token;
		$userinfo_ary['refresh_token'] = $refresh_token;
		
		//$this->updateUserInfo($userinfo_ary);
		$account_data = $this->getGameAccountData($open_id);
		if(is_array($account_data))
		{
			if($scope == "snsapi_base" && isset($account_data['is_refresh']) && $account_data['is_refresh'] == 1)
			{
				log_message('error', "getInfoOpenid:is_refresh:1"." in file".__FILE__." on Line ".__LINE__);
				return -2;
			}
			else
			{
				$host = Game_CONST::AsynRequest_Host;
				$path = "/wxauth/updateUserInfo";
				$port = 80;

				//异步更新用户信息
				$param = array("open_id"=>$open_id,"access_token"=>$access_token);
				$this->doRequestWX($host,$path,$port,$param);
			}
		}
		else
		{
			$param = array("open_id"=>$open_id,"access_token"=>$access_token);
			$update_result = $this->updateUserInfo($userinfo_ary);
			if($update_result == -2)
			{
				return -2;
			}
		}
		
		$_SESSION['WxOpenID'] = $open_id;
		
		return $result;
		
	}
	
	
	public function updateUserInfo($userinfo_ary)
	{
		$timestamp = time();
		
		$open_id = $userinfo_ary['open_id'];
		$access_token = $userinfo_ary['access_token'];
		
		if($open_id == G_CONST::EMPTY_STRING)
		{
			log_message('error', "updateWechatAccount:缺少openid:".$json_result." in file".__FILE__." on Line ".__LINE__);
			return -1;
		}
		
		$wechat_userinfo = $this->getWechatUserinfo_2($userinfo_ary);
		if(!is_array($wechat_userinfo))
		{
			$update_array['update_time'] = $timestamp;
			$update_array['update_appid'] = $open_id;
			$update_array['is_refresh'] = 1;
			$update_query = $this->updateFunc("open_id",$open_id,WX_Account,$update_array);

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
		
		//判断open_id是否存在
		$account_where = 'open_id="'.$open_id.'"';
		$account_sql = 'select account_id from '.WX_Account.' where '.$account_where.'';
		$account_query = $this->getDataBySql(1,$account_sql);
		if(DB_CONST::DATA_NONEXISTENT != $account_query)
		{
			$account_id = $account_query['account_id'];
			
			$update_array['update_time'] = $timestamp;
			$update_array['update_appid'] = $open_id;
			$update_array['nickname'] = $nickname;
			$update_array['headimgurl'] = $headimgurl;
			$update_array['is_refresh'] = 0;
			$update_query = $this->updateFunc("account_id",$account_id,WX_Account,$update_array);
		}
		else
		{
			$insert_array['create_time'] = $timestamp;
			$insert_array['create_appid'] = $open_id;
			$insert_array['update_time'] = $timestamp;
			$insert_array['update_appid'] = $open_id;
			$insert_array['is_delete'] = G_CONST::IS_FALSE;
			$insert_array['open_id'] = $open_id;
			$insert_array['nickname'] = $nickname;
			$insert_array['headimgurl'] = $headimgurl;
			$insert_array['is_refresh'] = 0;
			$account_id = $this->getInsertID(WX_Account, $insert_array);
			
			//默认添加房卡
			$ticket_array['create_time'] = $timestamp;
			$ticket_array['create_appid'] = $open_id;
			$ticket_array['update_time'] = $timestamp;
			$ticket_array['update_appid'] = $open_id;
			$ticket_array['is_delete'] = G_CONST::IS_FALSE;
			$ticket_array['account_id'] = $account_id;
			$ticket_array['ticket_count'] = 0;
			
			$ticket_id = $this->getInsertID(Room_Ticket, $ticket_array);
		}
		
		
		
		//$param = array("open_id"=>$open_id,"nickname"=>$wechat_userinfo['nickname'],"headimgurl"=>$wechat_userinfo['headimgurl']);
		//$this->doRequestWX($host,$path,$port,$param);
		
		
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
		
		$wechat_userinfo = $this->getWechatUserinfo_2($userinfo_ary);
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
	public function getWechatUserinfo_2($userinfo_ary)
	{
		
		$open_id = $userinfo_ary['open_id'];
		$access_token = $userinfo_ary['access_token'];
		
		//$access_token = $this->getAccessTokenUserInfo($is_refresh = 1);

		log_message('error', "getAccessToken:access_token:".$access_token." in file".__FILE__." on Line ".__LINE__);

		if($access_token != OPT_CONST::DATA_NONEXISTENT)
		{
			//$http_url = Wechat::WX_Url_Userinfo."?access_token=".$access_token."&openid=".$open_id."&lang=zh_CN";
			$http_url = "https://api.weixin.qq.com/sns/userinfo?access_token=".$access_token."&openid=".$open_id."&lang=zh_CN";
			$json_result = $this->httpGet($http_url);

			log_message('error', "function(getWechatUserinfo_2):1st : $json_result"." in file".__FILE__." on Line ".__LINE__);

			$result = $this->splitJsonString($json_result);
			if($result === false)
			{
				//返回结果不是json
				log_message('error', "getAccessToken:result is not a jsonstring"." in file".__FILE__." on Line ".__LINE__);
				return OPT_CONST::DATA_NONEXISTENT;
			}
			else if(isset($result['errcode']))
			{
				$access_token = $this->getAccessTokenUserInfo($is_refresh = 1);

				//$http_url = "https://api.weixin.qq.com/sns/userinfo?access_token=".$access_token."&openid=".$open_id."&lang=zh_CN";

				$http_url = "https://api.weixin.qq.com/cgi-bin/user/info?access_token=".$access_token."&openid=".$open_id."";

				$json_result = $this->httpGet($http_url);
				
				log_message('error', "function(getWechatUserinfo_2):2nd : $json_result"." in file".__FILE__." on Line ".__LINE__);
				$result = $this->splitJsonString($json_result);
				
				if($result === false)
				{
					//返回结果不是json
					log_message('error', "function(getWechatUserinfo_2):result is not a jsonstring"." in file".__FILE__." on Line ".__LINE__);
					return OPT_CONST::DATA_NONEXISTENT;
				}
				else if(isset($result['errcode']))
				{
					log_message('error', "function(getWechatUserinfo_2):can not get userinfo:".$json_result." in file".__FILE__." on Line ".__LINE__);
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
	public function getAccessTokenUserInfo($is_refresh = 0)
	{
		$timestamp = time();
		
		//获取appid和appsecret
		$app_id = Wechat::WX_Appid;
		$appsecret = Wechat::WX_AppSecret;
		
		
		$access_token =  $this->getDataValue(Server_Parameter,"value",array("key"=>"wx_AccessTokenGame"));
		if($access_token == DB_CONST::DATA_NONEXISTENT)
		{
			$p_id = $this->getInsertID(Server_Parameter, array("key"=>"wx_AccessTokenGame","value"=>G_CONST::EMPTY_STRING,"is_delete"=>G_CONST::IS_FALSE));
			$access_token = G_CONST::EMPTY_STRING;
		}
		$token_createTime = $this->getDataValue(Server_Parameter,"value",array("key"=>"wx_AccessTokenGame_CreateTime"));
		if($token_createTime == DB_CONST::DATA_NONEXISTENT)
		{
			$p_id = $this->getInsertID(Server_Parameter, array("key"=>"wx_AccessTokenGame_CreateTime","value"=>G_CONST::EMPTY_STRING,"is_delete"=>G_CONST::IS_FALSE));
			$token_createTime = 0;
		}

		$access_token = G_CONST::EMPTY_STRING;
		$token_createTime = 0;
		if($is_refresh == 1 || $access_token == G_CONST::EMPTY_STRING || $timestamp >= ($token_createTime+3600))
		{
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
			$query_1 = $this->updateFunc("key","wx_AccessTokenGame",Server_Parameter,array("value"=>$access_token));
			$query_2 = $this->updateFunc("key","wx_AccessTokenGame_CreateTime",Server_Parameter,array("value"=>$timestamp));
		}
		
		//返回access_token
		return $access_token;
	}



	public function getGameAccountData($open_id)
	{
		//判断open_id是否存在
		$account_where = 'open_id="'.$open_id.'"';
		$account_sql = 'select account_id,is_refresh from '.WX_Account.' where '.$account_where.'';
		$account_query = $this->getDataBySql(1,$account_sql);
		if(DB_CONST::DATA_NONEXISTENT != $account_query)
		{
			$account_id = $account_query['account_id'];
			$is_refresh = $account_query['is_refresh'];

			log_message('error', "getGameAccountData: account exist :".$account_id.",is_refresh:".$is_refresh." in file".__FILE__." on Line ".__LINE__);

			return array("account_id"=>$account_id,"is_refresh"=>$is_refresh);
		}
		else
		{
			log_message('error', "checkGameOpenID: account not exist :"." in file".__FILE__." on Line ".__LINE__);
			return false;
		}
	}


	public function checkGameOpenID($open_id)
	{
		//判断open_id是否存在
		$account_where = 'open_id="'.$open_id.'"';
		$account_sql = 'select account_id from '.WX_Account.' where '.$account_where.'';
		$account_query = $this->getDataBySql(1,$account_sql);
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