<?php

class Admin extends CI_Controller 
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
	
	/*
		生成房卡兑换码
	*/
	public function createExchangeCode()
	{
		if(isset($_GET['dealer_num']) && isset($_GET['type'])&& isset($_GET['count']) && isset($_GET['secret']))
		{
			$request_ary['dealer_num'] = $_GET['dealer_num'];
			$request_ary['type'] = $_GET['type'];
			$request_ary['count'] = $_GET['count'];
			$request_ary['secret'] = $_GET['secret'];

			$this->load->model('exchange/ticket_model','',true);
			$result = $this->ticket_model->createExchangeCode($request_ary);

			var_dump($result);
		}
		else
		{
			echo "error!";
		}
	}
	
	public function getScoreStatistics($dealer_num,$account_id,$game_type=0)
	{
		$request_ary['dealer_num'] = $dealer_num;
		$request_ary['account_id'] = $account_id;
		$request_ary['game_type'] = $game_type;
		$request_ary['from'] = "2017-01-01";
		$request_ary['to'] = "2017-12-01";

		$this->load->model('admin/account_model','',true);
		$result = $this->account_model->getScoreStatistics($request_ary);

		//var_dump($result);
	}


	public function getTicketUseCount($from="",$to="")
	{
		$request_ary['from'] = $from;
		$request_ary['to'] = $to;

		$this->load->model('admin/account_model','',true);
		$result = $this->account_model->getTicketUseCount($request_ary);

		//var_dump($result);
	}

	public function getOnlineData()
	{
		$this->load->model('admin/account_model','',true);
		$result = $this->account_model->getOnlineData();

		//var_dump($result);
	}


	public function getAccountTicketList($dealer_num="",$from="",$to="")
	{
		$request_ary['dealer_num'] = $dealer_num;
		$request_ary['from'] = $from;
		$request_ary['to'] = $to;

		$this->load->model('admin/account_model','',true);
		$result = $this->account_model->getAccountTicketList($request_ary);

		//var_dump($result);
	}


	public function getDealerDailyData($dealer_num,$from="",$to="")
	{
		$request_ary['dealer_num'] = $dealer_num;
		$request_ary['from'] = $from;
		$request_ary['to'] = $to;

		$this->load->model('admin/account_model','',true);
		$result = $this->account_model->getDealerDailyData($request_ary);

		//var_dump($result);
	}
	
	public function updateDealerDailyData($dealer_num,$from="",$to="")
	{
		$request_ary['dealer_num'] = $dealer_num;
		$request_ary['from'] = $from;
		$request_ary['to'] = $to;

		$this->load->model('admin/account_model','',true);
		$result = $this->account_model->updateDealerDailyData($request_ary);

		//var_dump($result);
	}
	
	
	private function checkViewLogin()
	{
		if(!isset($_SESSION['LoginAdminID']) || $_SESSION['LoginAdminID']=="")
		{
			$direct_url = base_url("admin/login");
			Header("Location:".$direct_url);
			exit();
		}
		return true;
	}
	private function checkOptLogin()
	{
		if(!isset($_SESSION['LoginAdminID']) || $_SESSION['LoginAdminID']=="")
		{
			echo '{"result":"-3"}';
			exit();
		}
		return true;
	}


	public function statisticsAccount($dealer_num,$type,$account_id)
	{
		$this->load->model('admin/account_model','',true);
		$result = $this->account_model->statisticsAccount($dealer_num,$type,$account_id);
	}



	/**
	 * 	index
	 */
	public function login()
	{
		
		$data['base_url'] = base_url();

		$this->load->view('admin/login',$data);
	}
		
	
	public function loginOpt()
	{
		if(isset($_POST['account'])&&isset($_POST['pwd'])){
			$arrData['account'] = $_POST['account'];
			$arrData['pwd'] = $_POST['pwd'];
			$this->load->model('account/account_model','',true);
			$result = $this->account_model->adminLoginOpt($arrData);
			print_r(json_encode($result));
		}

	}

	public function logout()
	{
		unset($_SESSION['LoginAdminID']);

		$direct_url = base_url("admin/login");
		Header("Location:".$direct_url);
		exit();
	}



	/**
	 * 	index
	 */
	public function index()
	{

		$this->checkViewLogin();

		$data['base_url'] = base_url();

		$data['from'] = date("Y-m-d",strtotime("-7 day"));
		$data['to'] = date("Y-m-d");


	//	$this->load->view('admin/mainIndex',$data);
		$this->load->view('admin/index',$data);
	}
	
	/*
		修改代理商
	*/
	public function updateDealerOpt()
	{
		$this->checkOptLogin();


		$params = file_get_contents('php://input');
		$params_ary = json_decode($params,true);

		$request_array['dealer_id'] = $params_ary['dealer_id'];
		$request_array['name'] = $params_ary['name'];
		$request_array['account'] = $params_ary['account'];
		$request_array['passwd'] = $params_ary['passwd'];
		$request_array['payment_type'] = $params_ary['payment_type'];
		$this->load->model('admin/account_model','admin_account_model',true);
		$result = $this->admin_account_model->addDealerOpt($request_array);
		$result = json_encode($result);

		echo $result;
	}


	
	/*
		获取代理商列表
	*/
	public function dealerList()
	{
		$this->checkOptLogin();

		$params = file_get_contents('php://input');
		$params_ary = json_decode($params,true);

		$request_array['keyword'] = $params_ary['keyword'];
		$this->load->model('admin/account_model','admin_account_model',true);
		$result = $this->admin_account_model->getDealerList($request_array);
		$result = json_encode($result);

		echo $result;
	}
	


	/*
		获取代理商信息
	*/
	public function dealerInfo()
	{
		$this->checkOptLogin();


		$params = file_get_contents('php://input');
		$params_ary = json_decode($params,true);

		$request_array['dealer_num'] = $params_ary['dealer_num'];
		$this->load->model('admin/account_model','admin_account_model',true);
		$result = $this->admin_account_model->getDealerInfo($request_array);
		$result = json_encode($result);
		echo $result;
	}
	
	
	/*
		获取代理商信息
	*/
	public function dealerSaleInfo()
	{
		$this->checkOptLogin();

		$params = file_get_contents('php://input');
		$params_ary = json_decode($params,true);

		$request_array['dealer_num'] = $params_ary['dealer_num'];
		$request_array['from'] = $params_ary['from'];
		$request_array['to'] = $params_ary['to'];
		$this->load->model('admin/account_model','admin_account_model',true);
		$result = $this->admin_account_model->getDealerSaleInfo($request_array);
		$result = json_encode($result);
		echo $result;
	}


	/*
		获取代理商信息
	*/
	public function dealerJournal()
	{
		$this->checkOptLogin();
		
		$params = file_get_contents('php://input');
		$params_ary = json_decode($params,true);
		
		$request_array['dealer_num'] = $params_ary['dealer_num'];

		$request_array['from'] = $params_ary['from'];
		$request_array['to'] = $params_ary['to'];
		$request_array['page'] = $params_ary['page'];
		$this->load->model('admin/account_model','admin_account_model',true);
		$result = $this->admin_account_model->getDealerJournal($request_array);
		$result = json_encode($result);

		echo $result;
	}
	
	
	

	/*
		充值
	*/
	public function dealerRechargeOpt()
	{
		$this->checkOptLogin();

		$params = file_get_contents('php://input');
		$params_ary = json_decode($params,true);

		$request_array['dealer_num'] = $params_ary['dealer_num'];
		$request_array['ticket_count'] = $params_ary['ticket_count'];
		$request_array['secret'] = $params_ary['secret'];
		$this->load->model('admin/account_model','admin_account_model',true);
		$result = $this->admin_account_model->dealerRechargeOpt($request_array);
		$result = json_encode($result);

		echo $result;
	}


	/*
		修改商城
	*/
	public function updateGoodsList()
	{
		$this->checkOptLogin();

		$params = file_get_contents('php://input');
		$params_ary = json_decode($params,true);

		$request_array['dealer_num'] = $params_ary['dealer_num'];
		$request_array['goodsList'] = $params_ary['goodsList'];
		$this->load->model('admin/account_model','admin_account_model',true);
		$result = $this->admin_account_model->updateGoodsList($request_array);
		$result = json_encode($result);

		echo $result;
	}


	/*
		删除代理商
	*/
	public function delDealerOpt()
	{
		$this->checkOptLogin();

		$params = file_get_contents('php://input');
		$params_ary = json_decode($params,true);

		$request_array['dealer_id'] = $params_ary['dealer_id'];
		$this->load->model('admin/account_model','admin_account_model',true);
		$result = $this->admin_account_model->delDealerOpt($request_array);
		$result = json_encode($result);

		echo $result;
	}
	



	public function roomCard()
	{

		$this->checkViewLogin();
		
		$data['base_url'] = base_url();

		$data['from'] = date("Y-m-d",strtotime("-7 day"));
		$data['to'] = date("Y-m-d");

		$data['num'] = $_GET['dealer_num'];
		$data['account_id'] = $_GET['id'];
		$data['nickname'] = $_GET['name'];

		$this->load->view('admin/roomCard',$data);
	}	
	
	public function customerCard()
	{

		$this->checkViewLogin();
		
		$data['base_url'] = base_url();

		$data['from'] = date("Y-m-d",strtotime("-7 day"));
		$data['to'] = date("Y-m-d");

		$data['num'] = $_GET['dealer_num'];
		$data['account_id'] = $_GET['id'];
		$data['nickname'] = $_GET['name'];

		$this->load->view('admin/customerCard',$data);
	}
	
}
