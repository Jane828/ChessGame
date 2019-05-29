<?php

//include_once dirname(__DIR__).'/third_party/phpcode_shield.php';		//加载防注入代码
class Dist extends MY_Controller
{
	/*
		构造函数
	*/
	function __construct() {
		header('Content-Type: text/html; charset=utf-8'); 
		parent::__construct();
		$this->load->helper('url');
	}


	public function online() {
		$data['base_url'] = $this->domain_path();
		$this->load->model('verification_model','',true);
		$session_request['account_id'] = 99999;
		$session_request['dealer_num'] = 2;
		$sessionResult = $this->verification_model->createRequestSession($session_request);
		$data['session'] = $sessionResult;		
		$this->load->view("online.php",$data);
	}
	
	
	/*
		判断是否微信的
	*/
	public function is_weixin() { 

		if ( strpos($_SERVER['HTTP_USER_AGENT'], 'MicroMessenger') !== false ) {
				return true;
		}	
		return false;
	}
	
	
	/*
		提示在微信浏览器打开
	*/
	public function warmingWX() {
		$data['base_url'] = $this->domain_path();
		$this->load->view("warming_wx",$data);
	}
	

	/*
		提成金额
	*/
	public function commissionList() {
		$is_weixin = $this->is_weixin();

        //判断代理商编号是否存在
		if(!isset($_GET['dealer_num']))
		{
			log_message('error', "function(dial):lack of dealer_num:"." in file".__FILE__." on Line ".__LINE__);
			show_404();exit;
		}

		$dealer_num = filter_var($_GET['dealer_num']);
		$DelaerConst = "Dealer_".$dealer_num;
		if(!class_exists($DelaerConst))
		{
			log_message('error', "function(dial):dealer not exist:".$dealer_num." in file".__FILE__." on Line ".__LINE__);
			show_404();exit;
		}
        
		if (isset($_SESSION['WxOpenID'])) {
			
			$open_id = $_SESSION['WxOpenID'];

			$request_ary['open_id'] = $open_id;
			$request_ary['dealer_num'] = $dealer_num;
			$this->load->model('account/account_model','',true);
			$userinfo_result = $this->account_model->getUserInfo($request_ary);
			if(!isset($userinfo_result['data']['account_id']) || $userinfo_result['data']['account_id'] == "")
			{
				$direct_url = base_url("d/clist/".$dealer_num);
				Header("Location:".$direct_url);
				return;
			}
			$request_ary['account_id'] = $userinfo_result['data']['account_id'];
			$this->load->model('dist/commission_model','',true);
			$result = $this->commission_model->getTotalCommission($request_ary);

			$data['user'] = $userinfo_result['data'];
			$data['sum_commission'] = 0;
			if(isset($result['data']['sum_commission'])&&$result['data']['sum_commission']>0){
				$data['sum_commission'] = $result['data']['sum_commission'];
			}
			
			$data['base_url'] = $this->domain_path();
			$data['dealer_num'] = $dealer_num;

			$this->load->model('wechat_model','',true);
			$config_ary = $this->wechat_model->getWxConfig($dealer_num);
			$data['config_ary']=$config_ary;
			
			
			$this->load->view("d_".$dealer_num."/dist/commissionView.php",$data);
		}
		else
		{
			$direct_url = base_url("d/clist/".$dealer_num);
			Header("Location:".$direct_url);
		}
	}



	 //获取销售提成
	public function getCommission() {

		$params = json_decode(file_get_contents('php://input'),true);

		if (isset($params['account_id']) && isset($params['page']) && isset($params['dealer_num'])) {
			$account_id = $params['account_id'];
			$page = $params['page'];
			$dealer_num = $params['dealer_num'];


			$request_ary['dealer_num'] = $dealer_num;
			$request_ary['account_id'] = $account_id;
			$request_ary['page'] = $page;
			$this->load->model('dist/commission_model','',true);
			$result = $this->commission_model->getCommissionList($request_ary);
            
			print_r(json_encode($result));
			
		} else { 
			show_404();
		}	
	}

	public function distList() {
		$is_weixin = $this->is_weixin();

        //判断代理商编号是否存在
		if(!isset($_GET['dealer_num']))
		{
			log_message('error', "function(dial):lack of dealer_num:"." in file".__FILE__." on Line ".__LINE__);
			show_404();exit;
		}

		$dealer_num = filter_var($_GET['dealer_num']);
		$DelaerConst = "Dealer_".$dealer_num;
		if(!class_exists($DelaerConst))
		{
			log_message('error', "function(dial):dealer not exist:".$dealer_num." in file".__FILE__." on Line ".__LINE__);
			show_404();exit;
		}
        
		if (isset($_SESSION['WxOpenID'])) {
			
			$open_id = $_SESSION['WxOpenID'];

			$request_ary['open_id'] = $open_id;
			$request_ary['dealer_num'] = $dealer_num;
			$this->load->model('account/account_model','',true);
			$userinfo_result = $this->account_model->getUserInfo($request_ary);
			$data['user'] = $userinfo_result['data'];
			if(!isset($userinfo_result['data']['account_id']) || $userinfo_result['data']['account_id'] == "")
			{
				$direct_url = base_url("d/clist/".$dealer_num);
				Header("Location:".$direct_url);
				return;
			}


			$data['base_url'] = $this->domain_path();
			$data['dealer_num'] = $dealer_num;

			$this->load->model('wechat_model','',true);
			$config_ary = $this->wechat_model->getWxConfig($dealer_num);
			$data['config_ary']=$config_ary;
			
			
			$this->load->view("d_".$dealer_num."/dist/distView.php",$data);
		}
		else
		{
			$direct_url = base_url("d/clist/".$dealer_num);
			Header("Location:".$direct_url);
		}
	}

	//获取销售提成
	public function getDistList() {
		$params = json_decode(file_get_contents('php://input'),true);
		if (isset($params['account_id']) && isset($params['page']) && isset($params['dealer_num']) && isset($params['type'])) {
			$account_id = $params['account_id'];
			$page = $params['page'];
			$dealer_num = $params['dealer_num'];
			$type = $params['type'];	//1:一级代理，2二级代理

			$request_ary['dealer_num'] = $dealer_num;
			$request_ary['account_id'] = $account_id;
			$request_ary['page'] = $page;
			$request_ary['type'] = $type;
			$this->load->model('dist/distribution_model','',true);
			$result = $this->distribution_model->getDistList($request_ary);
            
			print_r(json_encode($result));
			
		} else { 
			show_404();
		}	
	}
	
}
