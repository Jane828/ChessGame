<?php

include_once dirname(__DIR__).'/third_party/phpcode_shield.php';		//加载防注入代码
class Dealer extends MY_Controller
{
	/*
		构造函数
	*/
	function __construct() {
		header('Content-Type: text/html; charset=utf-8'); 
		parent::__construct();
		$this->load->helper('url');
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
	
	private function getAccountIdByOpenid($dealer_num,$open_id)
	{
		$request_ary['dealer_num'] = $dealer_num;
		$request_ary['open_id'] = $open_id;
		$this->load->model('account/account_model','',true);
		$userinfo_result = $this->account_model->getUserInfo($request_ary);
		if(!isset($userinfo_result['data']['account_id']) || $userinfo_result['data']['account_id'] == "")
		{
			return -1;
		}
		$account_id = $userinfo_result['data']['account_id'];
		return $account_id;
	}
	
	/*
		提示在微信浏览器打开
	*/
	public function warmingWX() {
		$data['base_url'] = $this->domain_path();
		$this->load->view("warming_wx",$data);
	}
	

	public function createRedPackage() {
		$params = json_decode(file_get_contents('php://input'),true);
		
		if (isset($params['account_id']) && isset($params['ticket_count'])&& isset($params['content'])&& isset($params['dealer_num'])&& isset($params['dealer_screct'])) {

			$open_id = $_SESSION['WxOpenID'];
			$dealer_num = $params['dealer_num'];
			$account_id = $this->getAccountIdByOpenid($dealer_num,$open_id);
			if($account_id <= 0)
			{
				show_404();	return;
			}

			//$account_id = $params['account_id'];
			$ticket_count = $params['ticket_count'];
			$content = $params['content'];
			$dealer_num = $params['dealer_num'];
			

			$request_ary['dealer_screct'] = $params['dealer_screct'];
			$request_ary['dealer_num'] = $dealer_num;
			$request_ary['account_id'] = $account_id;
			$request_ary['ticket_count'] = $ticket_count;
			$request_ary['content'] = $content;
			$this->load->model('activity/redenvelop_model','',true);
			$result = $this->redenvelop_model->createRedEnvelopOpt($request_ary);
			
			print_r(json_encode($result));
			
		} else {
			show_404();	
		}
	}

	
}
