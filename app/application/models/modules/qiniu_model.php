<?php

 include_once dirname(__DIR__).'/public_models.php';	//加载数据库操作类

//require_once dirname(__FILE__).'/qiniuSDK/vendor/autoload.php';
require_once dirname(dirname(__DIR__)).'/third_party/qiniuSDK/vendor/autoload.php';

use Qiniu\Auth;
use Qiniu\Storage\UploadManager;
use Qiniu\Http\Response;
//use Qiniu\Storage\BucketManager;

//include_once dirname(__FILE__).'/Qiniu/Auth.php';
//include_once dirname(__FILE__).'/Qiniu/Storage/BucketManager.php';		//
class Qiniu_model extends Public_Models
{
	public function __construct()
	{
		header('Content-Type: text/html; charset=utf-8'); 
		parent::__construct();
	}
	
	/*-------------通用方法--------------*/
		
	/*
		实例化七牛授权类
	*/
	public function qiniuAuth($bucket = "")
	{
		if($bucket == "")
		{
			log_message('error', "function(uploadFile):lack of bucket"." in file".__FILE__." on Line ".__LINE__);
			
			return false;
		}
		else
		{
			//获取七牛账号密钥
			//$accessKey = $this->getDataValue(Server_Parameter,"value",array("key"=>"qiniu_".$bucket."_accessKey"));
			//$secretKey = $this->getDataValue(Server_Parameter,"value",array("key"=>"qiniu_".$bucket."_secretKey"));
			$accessKey = Qiniu::AccessKey;
			$secretKey = Qiniu::SecretKey;
			
			
			if($accessKey == -2 || $secretKey == -2)
			{
				log_message('error', "function(qiniuAuth):can not get accessKey or secretKey"." in file".__FILE__." on Line ".__LINE__);
				
				return false;
			}
			else
			{
				
				//实例化
				$auth = new Auth($accessKey, $secretKey);
				
				return $auth;
			}
		}
	}
	
	
	
	/*
		获取token授权码
	*/
	public function getUploadToken_byBucket($bucket = "",$token_timeout = 0)
	{
		if($bucket == "")
		{
			log_message('error', "function(uploadFile):lack of bucket"." in file".__FILE__." on Line ".__LINE__);
			
			return OPT_CONST::QT_FALSE;
		}
		
		if($token_timeout == 0)
		{
			log_message('error', "function(uploadFile):lack of token_timeout"." in file".__FILE__." on Line ".__LINE__);
			
			return OPT_CONST::QT_FALSE;
		}
		
		
		//实例化七牛授权类
		$auth = $this->qiniuAuth($bucket);
		if(is_object($auth))
		{	
			//生成token
			//$token = $auth->uploadToken($bucket);
			$token = $auth->uploadToken($bucket, null, $token_timeout);
			
			if($token === false)
			{
				return OPT_CONST::QT_FALSE;
			}
			else
			{
				//保存到数据库
				return $token;
			}
		}
		else
		{
			log_message('error', "function(getToken):auth is not an object"." in file".__FILE__." on Line ".__LINE__);
			
			return OPT_CONST::QT_FALSE;
		}
	}
	
	
	
	
	/*
		获取token授权码
	*/
	public function getUploadToken($bucket = "")
	{
		
		//log_message('error', "function(uploadFile):bucket".$bucket." in file".__FILE__." on Line ".__LINE__);
		
		if($bucket == "")
		{
			log_message('error', "function(uploadFile):lack of bucket"." in file".__FILE__." on Line ".__LINE__);
			
			return OPT_CONST::QT_FALSE;
		}
		else
		{
			$time = time();
			//$token_timeout = $this->getDataValue(Server_Parameter,"value",array("key"=>"qiniu_".$bucket."_uploadtoken_timeout"));
			$token_timeout = Qiniu::Timeout;
			$token_createtime = $this->getDataValue(Server_Parameter,"value",array("key"=>"qiniu_".$bucket."_uploadtoken_createtime"));
			
			//log_message('error', "function(uploadFile):token_createtime".$token_createtime." in file".__FILE__." on Line ".__LINE__);
			
			//判断记录是否存在
			if($token_createtime == DB_CONST::DATA_NONEXISTENT)
			{
				$insert_id = $this->getInsertID(Server_Parameter, array("key"=>"qiniu_".$bucket."_uploadtoken_createtime","value"=>G_CONST::EMPTY_STRING));
				
				$token_createtime = G_CONST::EMPTY_STRING;
			}
			
			if($token_createtime != G_CONST::EMPTY_STRING && $token_timeout != G_CONST::EMPTY_STRING && $time < ($token_createtime + ($token_timeout/2)))
			{
				$token = $this->getDataValue(Server_Parameter,"value",array("key"=>"qiniu_".$bucket."_uploadtoken"));
				
				//log_message('error', "function(uploadFile):token".$token." in file".__FILE__." on Line ".__LINE__);
				//判断记录是否存在
				if($token == DB_CONST::DATA_NONEXISTENT)
				{
					$insert_id = $this->getInsertID(Server_Parameter, array("key"=>"qiniu_".$bucket."_uploadtoken","value"=>G_CONST::EMPTY_STRING));
					
					log_message('error', "function(uploadFile):insert_id".$insert_id." in file".__FILE__." on Line ".__LINE__);
					$token = G_CONST::EMPTY_STRING;
				}
				
				if($token != G_CONST::EMPTY_STRING)
				{
					return $token;
				}
				
			}
			
			//实例化七牛授权类
			$auth = $this->qiniuAuth($bucket);
			if(is_object($auth))
			{	
				//生成token
				//$token = $auth->uploadToken($bucket);
				$token = $auth->uploadToken($bucket, null, $token_timeout);
				
				if($token === false)
				{
					return OPT_CONST::QT_FALSE;
				}
				else
				{
					//保存到数据库
					$this->updateFunc("key","qiniu_".$bucket."_uploadtoken",Server_Parameter,array("value"=>$token));
					$this->updateFunc("key","qiniu_".$bucket."_uploadtoken_createtime",Server_Parameter,array("value"=>$time));
					
					return $token;
				}
			}
			else
			{
				log_message('error', "function(getToken):auth is not an object"." in file".__FILE__." on Line ".__LINE__);
				
				return OPT_CONST::QT_FALSE;
			}
		}
		
	}
	
	
	
	
	
	
	/*-------------操作方法--------------*/
	
	
	/*
		upload文件(直接上传)
	*/
	public function uploadFile($bucket = "",$file_path = "",$file_key = "")
	{
		if($file_path == "" || $file_key == "" || $bucket == "")
		{
			log_message('error', "function(uploadFile):lack of bucket/file_path/file_key"." in file".__FILE__." on Line ".__LINE__);
			
			return false;
		}
		else
		{
			//实例化七牛授权类
			$auth = $this->qiniuAuth($bucket);
			//设置参数
			$opts = array(
			            
			        );
			
			$token_timeout = $this->getDataValue(Server_Parameter,"value",array("key"=>"qiniu_".$bucket."_uploadtoken_timeout"));
			
			$token = $auth->uploadToken($bucket, null, $token_timeout, $opts);
			//$token = "lTtnz5SeTx-B89CEeFH70w_xExx5FqvQGywG-rux:XOYkvaMkDIp2KuOgwdlCTMlsFPs=:eyJzY29wZSI6IndlbnpoZW4iLCJkZWFkbGluZSI6MTQyNzk2MTY1NH0=1";
			
			//var_dump($token);echo "<br><br>";
			//exit;
			$uploadMgr = New UploadManager();
	
			list($ret, $err) = $uploadMgr->putFile($token, $file_key, $file_path);
			
			
			if ($err !== null) 
			{
				$error_code = $err->code();
				
				log_message('error', "function(uploadFile):can not upload file , code = ".$error_code." in file".__FILE__." on Line ".__LINE__);
				
				return false;
			} 
			else 
			{
			    return $ret;
			}
		}
		
	}
	
	
}