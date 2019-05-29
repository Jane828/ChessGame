<?php
	
include_once dirname(dirname(__DIR__)).'/third_party/phpcode_shield.php';		//加载防注入代码	
class Flower extends CI_Controller 
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
		获取支付订单
		
		参数：
			account_id : account_id
		
		返回结果：
			
		
	*/
	public function getPaymentOpt()
	{
		$params = json_decode(file_get_contents('php://input'),true);
		
		if(isset($_SESSION['WxOpenID']) && $_SESSION['WxOpenID'] != "" && isset($params['account_id']) && isset($params['goods_id'])  && isset($params['dealer_num']))
		{
			$account_id = $params['account_id'];
			$open_id = $_SESSION['WxOpenID'];
			
			$request_ary['open_id'] = $open_id;
			$request_ary['account_id'] = $params['account_id'];
			$request_ary['goods_id'] = $params['goods_id'];
			$request_ary['dealer_num'] = $params['dealer_num'];
			$this->load->model('payment/wxpay_model', '', true);
			$result = $this->wxpay_model->getPaymentOpt($request_ary);
			
			$result = json_encode($result);
			echo $result;
		}
		else
		{
			log_message('error', "function(getPaymentOpt):lack of open_id:"." in file".__FILE__." on Line ".__LINE__);
			show_404();	
		}
	}
	
	
	
	
	
	public function wxpayCallback()
	{
		log_message('error', "function(wxpayCallback):get  : "." in file".__FILE__." on Line ".__LINE__);
		
		$fileContent = file_get_contents("php://input");
		$postObj = simplexml_load_string($fileContent, 'SimpleXMLElement', LIBXML_NOCDATA);
		$postArray = get_object_vars($postObj);
		
		//var_dump($postArray);
		$this->load->model('payment/wxpay_model', '', true);
		$result = $this->wxpay_model->wxpayCallBack($postArray);
		//log_message('error', "function(wxpayCallback):result:".$result." in file".__FILE__." on Line ".__LINE__);
		echo $result;
	}
	
	
	
	
	
	
	
}