<?php

class Fruit extends CI_Controller 
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
		if(!isset($_SESSION['DealerNum']) || $_SESSION['DealerNum']=="" || !isset($_SESSION['LoginQudaoID']) || $_SESSION['LoginQudaoID']=="" )
		{
			$direct_url = base_url("qudao/login");
			Header("Location:".$direct_url);
			exit();
		}
		return true;
	}
	private function checkOptLogin()
	{
		if(!isset($_SESSION['DealerNum']) || $_SESSION['DealerNum']=="" || !isset($_SESSION['LoginGroupID']) || $_SESSION['LoginGroupID']=="" || !isset($_SESSION['LoginPresident']) || $_SESSION['LoginPresident']=="")
		{
			echo '{"result":"-3"}';
			exit();
		}
		return true;
	}

	/*
		获取水果机概况数据
	*/
	public function getSlotSummary()
	{
		//$this->checkOptLogin();
		$params = file_get_contents('php://input');
		$params_ary = json_decode($params,true);

		$request_array['dealer_num'] = $params_ary['dealer_num'];	
		$request_array['from'] = $params_ary['from'];
		$request_array['to'] = $params_ary['to'];


		$this->load->model('roomcard/slot_model','slot_model',true);
		$result = $this->slot_model->getSlotSummary($request_array);

		$result = json_encode($result);
		echo $result;
	}	

	/*
		获取水果机概况数据
	*/
	public function getSlotList()
	{
		//$this->checkOptLogin();
		$params = file_get_contents('php://input');
		$params_ary = json_decode($params,true);

		$request_array['dealer_num'] = $params_ary['dealer_num'];		
		$request_array['from'] = $params_ary['from'];
		$request_array['to'] = $params_ary['to'];
		$request_array['page'] = $params_ary['page'];

		$this->load->model('roomcard/slot_model','slot_model',true);
		$result = $this->slot_model->getSlotList($request_array);

		$result = json_encode($result);
		echo $result;
	}	
	
}
