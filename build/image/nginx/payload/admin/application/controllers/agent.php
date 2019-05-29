<?php

class Agent extends CI_Controller 
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
		搜索玩家
	*/
	public function searchAgentList()
	{
		//$this->checkOptLogin();
		$params = file_get_contents('php://input');
		$params_ary = json_decode($params,true);

		$request_array['dealer_num'] = $params_ary['dealer_num'];
		
		$request_array['page'] = $params_ary['page'];
		$request_array['keyword'] = $params_ary['keyword'];


		$this->load->model('agent/direct_model','direct_model',true);
		$result = $this->direct_model->searchAgentList($request_array);

		$result = json_encode($result);
		echo $result;
	}	

	/*
		绑定代理商
	*/
	public function bindAgentOpt()
	{
		//$this->checkOptLogin();
		$params = file_get_contents('php://input');
		$params_ary = json_decode($params,true);

		$request_array['dealer_num'] = $params_ary['dealer_num'];		
		$request_array['account_id'] = $params_ary['account_id'];


		$this->load->model('agent/direct_model','direct_model',true);
		$result = $this->direct_model->bindAgentOpt($request_array);

		$result = json_encode($result);
		echo $result;
	}	
	
	/*
		取消绑定
	*/
	public function unbindAgentOpt()
	{
		//$this->checkOptLogin();
		$params = file_get_contents('php://input');
		$params_ary = json_decode($params,true);

		$request_array['dealer_num'] = $params_ary['dealer_num'];		
		$request_array['account_id'] = $params_ary['account_id'];


		$this->load->model('agent/direct_model','direct_model',true);
		$result = $this->direct_model->unbindAgentOpt($request_array);

		$result = json_encode($result);
		echo $result;
	}


}
