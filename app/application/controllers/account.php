<?php

include_once dirname(__DIR__).'/third_party/phpcode_shield.php';		//加载防注入代码
class Account extends CI_Controller 
{
	/*
		构造函数
	*/
	function __construct()
	{
		header("Access-Control-Allow-Origin: *");
		header('Content-Type: text/html; charset=utf-8'); 
		parent::__construct();
		$this->load->helper('url');
	}
	

	/*
		判断是否微信的
	*/
	private function is_weixin()
	{ 
		if ( strpos($_SERVER['HTTP_USER_AGENT'], 'MicroMessenger') !== false ) {
				return true;
		}	
		return false;
	}
	
	
	/**
	 * 	index
	 */
	public function index()
	{
		show_404();
	}
	
	
	/*
		获取七牛上传token
		
		参数：
			bucket : zht66
		返回结果：
			
		
	*/
	public function getQiniuUploadToken()
	{
		$this->load->model('modules/qiniu_model','',true);
		$qiniu_token = $this->qiniu_model->getUploadToken(Qiniu::Default_Bucket);
		
		echo '{"uptoken":"'.$qiniu_token.'"}';
	}
	
	
	
	
	/************************************************
					Account function
	*************************************************/
	
	/*
		获取用户信息
		
		参数：
			bucket : zht66
		返回结果：
			
		
	*/
	public function getUserInfo()
	{
		$params = json_decode(file_get_contents('php://input'),true);

		//$_SESSION['WxOpenID'] = "oIQW5w3pGUeSQDLzsYUAX4Yl6TqQ";
		//$params['phone'] = "13632361366";
		//$params['dealer_num'] = "2";

		if (isset($_SESSION['WxOpenID']) && isset($params['dealer_num'])) {
			$open_id = $_SESSION['WxOpenID'];
			$dealer_num = $params['dealer_num'];

			$request_ary['dealer_num'] = $dealer_num;
			$request_ary['open_id'] = $open_id;
			$this->load->model('account/account_model','',true);
			$userinfo_result = $this->account_model->getUserInfo($request_ary);
            
			echo json_encode($userinfo_result);
			
		} else {
			echo '{"result":"10001","result_message":"登陆失效，请重新操作"}';
		}
	}



	/*
		获取手机验证码
		
		参数：
			bucket : zht66
		返回结果：
			
		
	*/
	public function getMobileSms()
	{
		$params = json_decode(file_get_contents('php://input'),true);

		//$_SESSION['WxOpenID'] = "oIQW5w3pGUeSQDLzsYUAX4Yl6TqQ";
		//$params['phone'] = "13632361366";
		//$params['dealer_num'] = "2";


		if (isset($_SESSION['WxOpenID']) && isset($params['phone']) && isset($params['dealer_num'])) {
			$open_id = $_SESSION['WxOpenID'];
			$phone = $params['phone'];
			$dealer_num = $params['dealer_num'];

			$request_ary['dealer_num'] = $dealer_num;
			$request_ary['open_id'] = $open_id;
			$request_ary['phone'] = $phone;
			$this->load->model('account/account_model','',true);
			$sms_result = $this->account_model->getMobileSms($request_ary);
            
			print_r(json_encode($sms_result));
			
		} else {
			echo '{"result":"10001","result_message":"登陆失效，请重新操作"}';
		}
	}


	/*
		获取手机验证码
		
		参数：
			bucket : zht66
		返回结果：
			
		
	*/
	public function checkSmsCode()
	{
		$params = json_decode(file_get_contents('php://input'),true);

		//$_SESSION['WxOpenID'] = "oIQW5w3pGUeSQDLzsYUAX4Yl6TqQ";
		//$params['dealer_num'] = "2";
		//$params['phone'] = "13632361366";
		//$params['code'] = "6666";


		if (isset($_SESSION['WxOpenID']) && isset($params['phone']) && isset($params['code']) && isset($params['dealer_num'])) {
			$open_id = $_SESSION['WxOpenID'];
			$phone = $params['phone'];
			$dealer_num = $params['dealer_num'];
			$code = $params['code'];

			$request_ary['dealer_num'] = $dealer_num;
			$request_ary['open_id'] = $open_id;
			$request_ary['phone'] = $phone;
			$request_ary['code'] = $code;
			$this->load->model('account/account_model','',true);
			$sms_result = $this->account_model->checkSmsCode($request_ary);
            
			print_r(json_encode($sms_result));
			
		} else {
			echo '{"result":"10001","result_message":"登陆失效，请重新操作"}';
		}
	}
	
	
	
}


