<?php

include_once dirname(__DIR__).'/third_party/phpcode_shield.php';		//加载防注入代码
class Dispatch extends MY_Controller
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
	public function is_weixin()
	{ 
		if ( strpos($_SERVER['HTTP_USER_AGENT'], 'MicroMessenger') !== false ) {
				return true;
		}	
		return false;
	}
	
	/*
		提示在微信浏览器打开
	*/
	public function warmingWX()
	{
		$data['base_url'] = $this->domain_path();
		$this->load->view("warming_wx",$data);
	}
	
	

	public function gameOpt()
	{
		// $host = "127.0.0.1";
		// $port = "20003";

		$params = json_decode(file_get_contents('php://input'),true);		
		if (isset($params['dealer_num'])&&isset($params['game_type'])){	

			$dealer_num = $params['dealer_num'];
			$game_type = $params['game_type'];

			$request_ary['dealer_num'] = $dealer_num;
			$request_ary['game_type'] = $game_type;
			$this->load->model('dealer/dealer_model','',true);
			$socket_info = $this->dealer_model->getGameSocketInfo($request_ary);
			if(!is_array($socket_info) || $socket_info["result"]!="0")
			{
				log_message('error', "function(gameOpt):can not get socket info:dealernum:".$dealer_num."  gametype:".$game_type." in file".__FILE__." on Line ".__LINE__);
				$ret = array('result'=>'-1',"result_message"=>"创建房间失败");
				echo json_encode($ret);
			}

			$host = $socket_info['data']['ip_host'];
			$port = $socket_info['data']['ip_port'];

			$json = $params['json'];

			$DelaerConst = "Dealer_".$dealer_num;
			if(class_exists($DelaerConst))
			{
				$sock = fsockopen($host,$port);
				//socket_set_timeout($sock,5);
				stream_set_timeout($sock,5);
				
				fwrite($sock,$json."\n");

				$buf = "";
				$c = "";

				$buf.= fgets($sock, 4096);  //每次读取4096个字节内容，并用.连接符连接到$info中。
				echo $buf.PHP_EOL;

				if ($sock) {
				    fclose($sock);
				}
				return;

			} else {
				log_message('error', "function(gameOpt):lack of DelaerConst in file".__FILE__." on Line ".__LINE__);
				$ret = array('result'=>'-1',"result_message"=>"创建房间失败");
				echo json_encode($ret);
			}

		} else {
			log_message('error', "function(gameOpt):lack of params in file".__FILE__." on Line ".__LINE__);
			$ret = array('result'=>'-1',"result_message"=>"创建房间失败");
			echo json_encode($ret);
		}

	}



}
