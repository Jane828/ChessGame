<?php

include_once dirname(__DIR__).'/public_models.php';		//加载数据库操作类
class Account_Common_Model extends Public_Models
{
	public function __construct()
	{
		header('Content-Type: text/html; charset=utf-8'); 
		parent::__construct();
	}
	
	
	
	/*
		生成32位随机变量
	*/
	protected function createLoginSession($string = "")
	{
		//获取当前时间的微秒
		list($usec, $sec) = explode(" ", microtime());
		$microtime = ((float)$usec + (float)$sec);
		$microtime = str_replace(".","",$microtime);
		//将微秒时间家长一个0-1000的随机变量
		for($i=0;$i<19;$i++)
		{
			$microtime .= rand(0,9);
		}
		//md5加密后再base64编码
		$session = base64_encode(md5($string.$microtime,TRUE));
		
		return $session;
	}
	
	

	/*
		生成短信验证码
	*/
	protected function createIdentifyingCode()
	{
		$identifyingCode = G_CONST::EMPTY_STRING;
		
		for($i = 0; $i < 6; $i++)
		{
			$identifyingCode .= rand(0,9);
		}
		
		return $identifyingCode;
	}

}