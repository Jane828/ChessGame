<?php

class Error extends CI_Controller 
{
	/*
		构造函数
	*/
	function __construct()
	{
		header('Content-Type: text/html; charset=utf-8'); 
		parent::__construct();
		$this->load->helper('url');

		
		//检查是否已登陆
		//$this->load->model('public_models','',true);
		//$this->public_models->is_mobile_request();
	}
	
	/**
	 * 	index
	 */
	public function index()
	{
		show_404();
	}
	
	
	/************************************************
					company function
	*************************************************/
	public function onError()
	{      
		if(isset($_POST['level'])&&isset($_POST['part'])&&isset($_POST['time'])&&isset($_POST['err_message'])){		

			$browser_info	= $_SERVER["HTTP_USER_AGENT"];	//浏览器信息
			$level			= $_POST['level'];							//日志等级，1：debug		2：error
			$module			= $_POST['part'];						//所属模块，例如Chat-2等操作码，或者connection等前端逻辑模块
			$WxOpenID	= "";						//错误信息
			if(isset($_SESSION['WxOpenID']))
				$WxOpenID	= $_SESSION['WxOpenID'];						//错误信息
			$time	= $_POST['time'];						//错误信息
			$err_message	= $_POST['err_message'];						//错误信息
			
			if($level == 1)
			{
				log_message('debug', "onError($browser_info):: module($module):: message($err_message)");
			}
			else if($level == 2)
			{
				log_message('error', "onError($browser_info):: module($module):: time($time):: WxOpenID($WxOpenID):: message($err_message)");
			}			

		}	
	}	
	


	
	
}
