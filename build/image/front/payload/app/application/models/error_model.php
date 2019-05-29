<?php

include_once('public_models.php');		//加载数据库操作类
class Error_Model extends Public_Models
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
		写日志
	*/
	public function writeLog($content)
	{
		$content = date("Y-m-d H:i:s").' '.$content;
		
		$log_filename = "abnormal".date("Y-m-d")."-".date("H").'.log';
		 
		$file = $this->config->item('log_path').$log_filename;
		 
		file_put_contents($file, $content,FILE_APPEND);
		 
		file_put_contents($file, "\n",FILE_APPEND);
	}







}