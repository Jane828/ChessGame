<?php

class Noty extends CI_Controller 
{
	/*
		构造函数
	*/
	function __construct()
	{
		header('Content-Type: text/html; charset=utf-8'); 
		parent::__construct();
		$this->load->helper('url');
	}
	
	private function checkViewLogin()
	{
		if(!isset($_SESSION['LoginDealerID']) || $_SESSION['LoginDealerID']=="" || !isset($_SESSION['LoginDealerNum']) || $_SESSION['LoginDealerNum']=="")
		{
			$direct_url = base_url("account/login");
			Header("Location:".$direct_url);
			exit();
		}
		return true;
	}
	private function checkOptLogin()
	{
		return true;
		if(!isset($_SESSION['LoginDealerID']) || $_SESSION['LoginDealerID']=="" || !isset($_SESSION['LoginDealerNum']) || $_SESSION['LoginDealerNum']=="")
		{
			echo '{"result":"-3"}';
			exit();
		}
		
	}


	
//获取公告
	public function getAnnList()
	{
		$this->checkOptLogin();

		$params = file_get_contents('php://input');
		$params_ary = json_decode($params,true);


		$request_array['page'] = $params_ary['page'];
		
		$this->load->model('noty/announcement_model','',true);
		$result = $this->announcement_model->getAnnList($request_array);
		$result = json_encode($result);

		echo $result;
	}	
//发送公告	
	public function sendAnnOpt()
	{
		$this->checkOptLogin();

		$params = file_get_contents('php://input');
		$params_ary = json_decode($params,true);


		$request_array['start_time'] = $params_ary['start_time'];
		$request_array['end_time'] = $params_ary['end_time'];
		$request_array['second'] = $params_ary['second'];
		$request_array['content'] = $params_ary['content'];
		
		$this->load->model('noty/announcement_model','',true);
		$result = $this->announcement_model->sendAnnOpt($request_array);
		$result = json_encode($result);

		echo $result;
	}	
//取消公告
	public function updateAnnStatusOpt()
	{
		$this->checkOptLogin();

		$params = file_get_contents('php://input');
		$params_ary = json_decode($params,true);


		$request_array['data_id'] = $params_ary['data_id'];
		$request_array['status'] = $params_ary['status'];
		
		$this->load->model('noty/announcement_model','',true);
		$result = $this->announcement_model->updateAnnStatusOpt($request_array);
		$result = json_encode($result);

		echo $result;
	}
			
//发送消息			
	public function sendMessage()
	{
		$this->checkOptLogin();

		$params = file_get_contents('php://input');
		$params_ary = json_decode($params,true);


		$request_array['dealer_num'] = $params_ary['dealer_num'];
		$request_array['account_id'] = $params_ary['account_id'];
		$request_array['content'] = $params_ary['content'];
		
		$this->load->model('noty/message_model','',true);
		$result = $this->message_model->sendMessage($request_array);
		$result = json_encode($result);

		echo $result;
	}
		

}
