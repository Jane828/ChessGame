<?php

include_once dirname(__DIR__).'/third_party/phpcode_shield.php';		//加载防注入代码
class Ay extends MY_Controller
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
	

	private function getAccountIdByOpenid($open_id)
	{
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

    //本地设置测试账号
	private function testSetupOpenID() {
		return;
		
		if (isset($_GET['open_id'])) {
			$_SESSION['WxOpenID'] = $_GET['open_id'];
		} else {
			$_SESSION['WxOpenID'] = "oo0vEw6rHixmZZHTZ5lAniEd-coM";
		}
	}


	public function cRP() {
		$params = json_decode(file_get_contents('php://input'),true);
		
		if (
			isset($params['ticket_count'])&& 
			isset($params['content'])
			) {
			
			$open_id = $_SESSION['WxOpenID'];
			$account_id = $this->getAccountIdByOpenid($open_id);
			if($account_id <= 0)
			{
				show_404();	return;
			}

			if(isset($params['account_id']) && $params['account_id'] != $account_id)
			{
				$log_text = "function(createRedPackage):有人在偷刷红包啊啊啊啊啊啊！！！！！！！accountid:$account_id : ".json_encode($params)."  in file".__FILE__." on Line ".__LINE__;
				$this->load->model('error_model','',true);
				$this->error_model->writeLog($log_text);
				return;
			}

			$ticket_count = $params['ticket_count'];
			$content = $params['content'];

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


	//跳转到房卡红包view
	public function rp() {

		$is_weixin = $this->is_weixin();

		if (isset($_SESSION['WxOpenID'])) {
			$open_id = $_SESSION['WxOpenID'];
			
			$request_ary['open_id'] = $open_id;
			$this->load->model('account/account_model','',true);
			$userinfo_result = $this->account_model->getUserInfo($request_ary);
			if(!isset($userinfo_result['data']['account_id']) || $userinfo_result['data']['account_id'] == "")
			{
				$direct_url = base_url("y/yh");
				Header("Location:".$direct_url);
				return;
			}
            if (! $this->checkIsAgree()) {
                return $this->warm();
            }
			$request_ary['account_id'] =$userinfo_result['data']['account_id'];
			$this->load->model('flower/room_model','',true);
			$result = $this->room_model->getRoomTicket($request_ary);

			$this->load->model('dealer/dealer_model','',true);
			$dealerResult = $this->dealer_model->getDealerData($request_ary);

            $data['is_agent'] = '0';

			if (isset($dealerResult) && $dealerResult['result'] == 0) {
            	$data['is_agent'] = '1';
            	$data['dealer_screct'] = $dealerResult['data']['dealer_screct'];
			    $data['inventory_count'] = $dealerResult['data']['inventory_count'];
            }
		
			$data['base_url'] = $this->domain_path();
			
			$data['user'] = $userinfo_result['data'];
			$data['card'] = $result['data']['ticket_count'];

			$data['open_id'] = "";
			$data['account_id'] = $userinfo_result['data']['account_id'];

			$data['room_url'] =base_url("y/yh");

            $data['file_url'] = $data['base_url']; 
			$data['image_url']= Game_Const::ImageUrl; 

			$this->load->model('wechat_model','',true);
			$config_ary = $this->wechat_model->getWxConfig();
			$data['config_ary']=$config_ary;

            $this->load->view("package.php", $data);

		} else {
			$direct_url = base_url("y/yh");
			Header("Location:".$direct_url);
		}
	}

    //红包详情view
	public function rpD() {

		$is_weixin = $this->is_weixin();

		if (isset($_SESSION['WxOpenID']) && isset($_GET['red_code'])) {
			$open_id = $_SESSION['WxOpenID'];
			$red_code = $_GET['red_code'];

			$request_ary['open_id'] = $open_id;
			$request_ary['code'] = $red_code;

			$this->load->model('account/account_model','',true);
			$userinfo_result = $this->account_model->getUserInfo($request_ary);
			if(!isset($userinfo_result['data']['account_id']) || $userinfo_result['data']['account_id'] == "")
			{
				$direct_url = base_url("y/yh");
				Header("Location:".$direct_url);
				return;
			}
            if (! $this->checkIsAgree()) {
                return $this->warm();
            }
			$request_ary['account_id'] =$userinfo_result['data']['account_id'];
			$this->load->model('flower/room_model','',true);
			$result = $this->room_model->getRoomTicket($request_ary);

            $this->load->model('activity/redenvelop_model','',true);
			$redenvelop_result = $this->redenvelop_model->getRedEnvelopData($request_ary);
		
			$data['base_url'] = $this->domain_path();
			
			$data['user'] = $userinfo_result['data'];
			$data['card'] = $result['data']['ticket_count'];

			$data['open_id'] = "";
			$data['account_id'] = $userinfo_result['data']['account_id'];

			$data['red_package'] = $redenvelop_result['data'];
			$data['red_code'] = $red_code;

			$data['room_url'] =base_url("y/yh");
			$shareURL = "y/re/".$red_code;
			$data['share_url'] =  base_url($shareURL);
			$data['share_icon'] = Game_Const::ImageUrl."files/images/redpackage/share_icon.jpg";

            $data['file_url'] = $data['base_url']; 
			$data['image_url']= Game_Const::ImageUrl; 
			
			$this->load->model('wechat_model','',true);
			$config_ary = $this->wechat_model->getWxConfig();
			$data['config_ary']=$config_ary;

			$this->load->view("packageDetail.php", $data);
		}
		else if(isset($_GET['red_code']))
		{
			$direct_url = base_url("y/re/".$_GET['red_code']);
			Header("Location:".$direct_url);
		}
		else {
			$direct_url = base_url("y/yh");
			Header("Location:".$direct_url);
		}
	}

	//领取红包
	public function receiveRP() {
		$params = json_decode(file_get_contents('php://input'),true);

		if (isset($params['red_code']) ) {

			$open_id = $_SESSION['WxOpenID'];
			$account_id = $this->getAccountIdByOpenid($open_id);
			if($account_id <= 0)
			{
				show_404();	return;
			}
			$red_code = $params['red_code'];
			$request_ary['account_id'] = $account_id;
			$request_ary['code'] = $red_code;
			$this->load->model('activity/redenvelop_model','',true);
			$result = $this->redenvelop_model->receiveRedEnvelopOpt($request_ary);
            
			print_r(json_encode($result));
			
		} else {
			show_404();	
		}
	}

    //我的红包
	public function myRP() {

		$is_weixin = $this->is_weixin();
		if (isset($_SESSION['WxOpenID'])) {
			$open_id = $_SESSION['WxOpenID'];

			$request_ary['open_id'] = $open_id;
			$this->load->model('account/account_model','',true);
			$userinfo_result = $this->account_model->getUserInfo($request_ary);
			if(!isset($userinfo_result['data']['account_id']) || $userinfo_result['data']['account_id'] == "")
			{
				$direct_url = base_url("y/yh");
				Header("Location:".$direct_url);
				return;
			}
			
			$request_ary['account_id'] =$userinfo_result['data']['account_id'];
			$this->load->model('flower/room_model','',true);
			$result = $this->room_model->getRoomTicket($request_ary);
		
			$data['base_url'] = $this->domain_path();
			
			$data['user'] = $userinfo_result['data'];
			$data['card'] = $result['data']['ticket_count'];

			$data['open_id'] = "";
			$data['account_id'] = $userinfo_result['data']['account_id'];

			$data['room_url'] =base_url("y/yh");

            $data['file_url'] = $data['base_url']; 
			$data['image_url']= Game_Const::ImageUrl; 
			
			$this->load->model('wechat_model','',true);
			$config_ary = $this->wechat_model->getWxConfig();
			$data['config_ary']=$config_ary;

			$this->load->view("packageRecord.php", $data);
		} else {
			$direct_url = base_url("d/relist/".$dealer_num);
			Header("Location:".$direct_url);
		}
	}

    //获取发出红包的记录
	public function sRP() {
		$params = json_decode(file_get_contents('php://input'),true);
		
		if (isset($params['page']) ) {

			$open_id = $_SESSION['WxOpenID'];
			$account_id = $this->getAccountIdByOpenid($open_id);
			if($account_id <= 0)
			{
				show_404();	return;
			}

			$page = $params['page'];
			$request_ary['account_id'] = $account_id;
			$request_ary['page'] = $page;
			$this->load->model('activity/redenvelop_model','',true);
			$result = $this->redenvelop_model->getSendRedList($request_ary);
            
			print_r(json_encode($result));
			
		} else { 
			show_404();
		}	
	}

    //获取收取的红包记录
	public function rRP() {
		$params = json_decode(file_get_contents('php://input'),true);

		if (isset($params['page']) ) {
			
			$open_id = $_SESSION['WxOpenID'];
			$account_id = $this->getAccountIdByOpenid($open_id);
			if($account_id <= 0)
			{
				show_404();	return;
			}

			$page = $params['page'];
			$request_ary['account_id'] = $account_id;
			$request_ary['page'] = $page;
			$this->load->model('activity/redenvelop_model','',true);
			$result = $this->redenvelop_model->getReceiveRedList($request_ary);
            
			print_r(json_encode($result));
			
		} else { 
			show_404();
		}
	}

	// 跳转到 房卡转移 页面
    public function tt() {

        $is_weixin = $this->is_weixin();

        if (isset($_SESSION['WxOpenID'])) {
            $request_ary['open_id'] = $_SESSION['WxOpenID'];

            $this->load->model('account/account_model','',true);
            $userinfo_result = $this->account_model->getUserInfo($request_ary);
            if(!isset($userinfo_result['data']['account_id']) || $userinfo_result['data']['account_id'] == "")
            {
                $direct_url = base_url("y/yh");
                Header("Location:".$direct_url);
                return;
            }
            if (! $this->checkIsAgree()) {
                return $this->warm();
            }
            $request_ary['account_id'] =$userinfo_result['data']['account_id'];
            $this->load->model('flower/room_model','',true);
            $result = $this->room_model->getRoomTicket($request_ary);


            $this->load->model('dealer/dealer_model','',true);
            $dealerResult = $this->dealer_model->getDealerData($request_ary);

            $data['is_agent'] = '0';

            if (isset($dealerResult) && $dealerResult['result'] == 0) {
                $data['is_agent'] = '1';
                $data['dealer_screct'] = $dealerResult['data']['dealer_screct'];
                $data['inventory_count'] = $dealerResult['data']['inventory_count'];
            }

            $data['base_url'] = $this->domain_path();

            $data['user'] = $userinfo_result['data'];
            $data['card'] = $result['data']['ticket_count'];

            $data['open_id'] = "";
            $data['account_id'] = $userinfo_result['data']['account_id'];

            $data['room_url'] =base_url("y/yh");

            $data['file_url'] = $data['base_url'];
            $data['image_url']= Game_Const::ImageUrl;
            $data['dealer_num'] = 2;

            $this->load->model('wechat_model','',true);
            $config_ary = $this->wechat_model->getWxConfig();
            $data['config_ary']=$config_ary;

            $this->load->view("pUserList.php", $data);

        } else {
            $direct_url = base_url("y/yh");
            Header("Location:".$direct_url);
        }
    }

    // 获取当前用户的手机号绑定的账号列表
    public function getPhoneAccountList()
    {
        /// param {account_id: "21827", dealer_num: "2", page: 1}，没有使用它，用的session中openid

        $this->is_weixin();

        if (!isset($_SESSION['WxOpenID'])) {
            $direct_url = base_url("y/yh");
            Header("Location:" . $direct_url);
        }
        $request_ary['open_id'] = $_SESSION['WxOpenID'];

        $this->load->model('account/account_model','',true);
        $userinfo_result = $this->account_model->getUserInfo($request_ary);
        if(!isset($userinfo_result['data']['account_id']) || $userinfo_result['data']['account_id'] == "")
        {
            $direct_url = base_url("y/yh");
            Header("Location:".$direct_url);
            return;
        }
        $user = $userinfo_result['data'];
        if($user['phone']){
            $ret  = $this->account_model->getAccountsByMobile($user['phone'], $user['account_id']);

            echo json_encode($ret);
        }else{
            echo json_encode([]);
        }

    }

    public function transferBindAccountTicket()
    {
        // {account_id: "21827", dealer_num: "2", transfer_aid: "4", receive_aid: "2295"}

        $this->is_weixin();

        if (!isset($_SESSION['WxOpenID'])) {
            $direct_url = base_url("y/yh");
            Header("Location:" . $direct_url);
        }

        $params = json_decode(file_get_contents('php://input'),true);
        if (! isset($params['transfer_aid']) || $params['transfer_aid'] == G_CONST::EMPTY_STRING) {
            show_404();
            return;
        }
        if (! isset($params['receive_aid']) || $params['receive_aid'] == G_CONST::EMPTY_STRING) {
            show_404();
            return;
        }

        $request_ary['open_id'] = $_SESSION['WxOpenID'];

        $this->load->model('account/account_model','',true);
        $userinfo_result = $this->account_model->getUserInfo($request_ary);
        if(!isset($userinfo_result['data']['account_id']) || $userinfo_result['data']['account_id'] == "")
        {
            $direct_url = base_url("y/yh");
            Header("Location:".$direct_url);
            return;
        }

        $user = $userinfo_result['data'];

        $ret  = $this->account_model->transferRoomCard($user, $params['transfer_aid'], $params['receive_aid']);

        echo json_encode($ret);
    }


//********************* 分界线 ***********************

	public function slotmachine () {

		//$this->testSetupOpenID();

		$is_weixin = $this->is_weixin();

        //判断代理商编号是否存在
		if(!isset($_GET['dealer_num']))
		{
			log_message('error', "function(slotmachine):lack of dealer_num:"." in file".__FILE__." on Line ".__LINE__);
			show_404();exit;
		}

		$dealer_num = filter_var($_GET['dealer_num']);
		$DelaerConst = "Dealer_".$dealer_num;
		if(!class_exists($DelaerConst))
		{
			log_message('error', "function(slotmachine):dealer not exist:".$dealer_num." in file".__FILE__." on Line ".__LINE__);
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
				$direct_url = base_url("d/slot/".$dealer_num);
				Header("Location:".$direct_url);
				return;
			}
            if (! $this->checkIsAgree()) {
                return $this->warm();
            }
			$request_ary['account_id'] =$userinfo_result['data']['account_id'];
			$this->load->model('flower/room_model','',true);
			$result = $this->room_model->getRoomTicket($request_ary);
		
			$data['base_url'] = $this->domain_path();
			
			$data['socket'] = $DelaerConst::WebSocket_Host;
			$data['user'] = $userinfo_result['data'];
			$data['card'] = $result['data']['ticket_count'];

			$data['open_id'] = "";
			$data['account_id'] = $userinfo_result['data']['account_id'];

			$data['room_url'] =base_url("d/home");
			$data['share_url'] =  base_url("d/slot");
			$data['share_icon'] = base_url("files/images/tiger/home.jpg");
			$data['dealer_num'] = $dealer_num;
			
			$this->load->model('wechat_model','',true);
			$config_ary = $this->wechat_model->getWxConfig($dealer_num);
			$data['config_ary']=$config_ary;

			$this->load->view("d_".$dealer_num."/slotmachine.php", $data);
		} else {
			$direct_url = base_url("d/slot/".$dealer_num);
			Header("Location:".$direct_url);
		}
	}

	public function getSlotResult () {
		$params = json_decode(file_get_contents('php://input'),true);
        
		if (isset($_SESSION['WxOpenID']) && isset($params['bet_array']) && isset($params['dealer_num'])) {
			$openID = $_SESSION['WxOpenID'];
		    $bets = $params['bet_array'];
		    $dealer_num = $params['dealer_num'];

			$request_ary['open_id'] = $openID;
			$request_ary['bet_array'] = $bets;
			$request_ary['dealer_num'] = $dealer_num;

			$this->load->model('activity/slotmachine_model','',true);
			$result = $this->slotmachine_model->slotMachineOpt($request_ary);
            
			print_r(json_encode($result));
			
		} else { 
			show_404();
		}
	}


	public function homeSign() {

		//$this->testSetupOpenID();

		$is_weixin = $this->is_weixin();

        //判断代理商编号是否存在
		if(!isset($_GET['dealer_num']))
		{
			log_message('error', "function(homeSign):lack of dealer_num:"." in file".__FILE__." on Line ".__LINE__);
			show_404();exit;
		}

		$dealer_num = filter_var($_GET['dealer_num']);
		$DelaerConst = "Dealer_".$dealer_num;
		if(!class_exists($DelaerConst))
		{
			log_message('error', "function(homeSign):dealer not exist:".$dealer_num." in file".__FILE__." on Line ".__LINE__);
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
				$direct_url = base_url("d/sign/".$dealer_num);
				Header("Location:".$direct_url);
				return;
			}
            if (! $this->checkIsAgree()) {
                return $this->warm();
            }
			$request_ary['account_id'] =$userinfo_result['data']['account_id'];
			$this->load->model('flower/room_model','',true);
			$result = $this->room_model->getRoomTicket($request_ary);

			$data['base_url'] = $this->domain_path();
			
			$data['socket'] = $DelaerConst::WebSocket_Host;
			$data['user'] = $userinfo_result['data'];
			$data['card'] = $result['data']['ticket_count'];

			$data['open_id'] = "";
			$data['account_id'] = $userinfo_result['data']['account_id'];

			$data['room_url'] =base_url("d/home/".$dealer_num);
			$data['share_url'] =  base_url("d/sign/".$dealer_num);
			$data['share_icon'] = base_url("files/images/game/home.jpg");
			$data['dealer_num'] = $dealer_num;
			
			$this->load->model('wechat_model','',true);
			$config_ary = $this->wechat_model->getWxConfig($dealer_num);
			$data['config_ary']=$config_ary;

			$this->load->view("d_".$dealer_num."/homeSign.php", $data);
		} else {
			$direct_url = base_url("d/sign/".$dealer_num);
			Header("Location:".$direct_url);
		}
	}

	public function getSignList () {
		$params = json_decode(file_get_contents('php://input'),true);

		if (isset($params['account_id']) && isset($params['dealer_num'])) {
			$accountID = $params['account_id'];
		    $dealer_num = $params['dealer_num'];

			$request_ary['account_id'] = $accountID;
			$request_ary['dealer_num'] = $dealer_num;

			$this->load->model('activity/sign_model','',true);
			$result = $this->sign_model->getSignList($request_ary);
            
			print_r(json_encode($result));
			
		} else { 
			show_404();
		}
	}

	public function signIn () {
		$params = json_decode(file_get_contents('php://input'),true);

		if (isset($params['account_id']) && isset($params['dealer_num'])) {
			$accountID = $params['account_id'];
		    $dealer_num = $params['dealer_num'];

			$request_ary['account_id'] = $accountID;
			$request_ary['dealer_num'] = $dealer_num;

			$this->load->model('activity/sign_model','',true);
			$result = $this->sign_model->signInOpt($request_ary);
            
			print_r(json_encode($result));
			
		} else { 
			show_404();
		}
	}

	public function getGameScore () {
		$params = json_decode(file_get_contents('php://input'),true);
	
		if (isset($params['account_id']) && isset($params['from']) && isset($params['to']) && isset($params['dealer_num'])) {
			$request_ary['account_id'] = $params['account_id'];
		    $request_ary['from'] = $params['from'];
		    $request_ary['to'] = $params['to'];
		    $request_ary['dealer_num'] = $params['dealer_num'];

			$this->load->model('account/account_model','',true);
		    $result = $this->account_model->getScoreStatistics($request_ary);
            
			print_r(json_encode($result));
			
		} else { 
			show_404();
		}
	}

	//代理商红包
	public function agentRedPackage() {

		//$this->testSetupOpenID();

		$is_weixin = $this->is_weixin();
     
		//判断代理商编号是否存在
		if(!isset($_GET['dealer_num']))
		{
			log_message('error', "function(agentRedPackage):lack of dealer_num:"." in file".__FILE__." on Line ".__LINE__);
			show_404();exit;
		}

		$dealer_num = filter_var($_GET['dealer_num']);
		$DelaerConst = "Dealer_".$dealer_num;
		if(!class_exists($DelaerConst))
		{
			log_message('error', "function(agentRedPackage):dealer not exist:".$dealer_num." in file".__FILE__." on Line ".__LINE__);
			show_404();exit;
		}

		if (isset($_SESSION['WxOpenID'])) {
			$open_id = $_SESSION['WxOpenID'];

			$request_ary['dealer_num'] = $dealer_num;
			$request_ary['open_id'] = $open_id;

			$this->load->model('account/account_model','',true);
			$userinfo_result = $this->account_model->getUserInfo($request_ary);
			if(!isset($userinfo_result['data']['account_id']) || $userinfo_result['data']['account_id'] == "")
			{
				$direct_url = base_url("d/relist/".$dealer_num);
				Header("Location:".$direct_url);
				return;
			}
            if (! $this->checkIsAgree()) {
                return $this->warm();
            }
			$request_ary['account_id'] =$userinfo_result['data']['account_id'];
			$this->load->model('flower/room_model','',true);
			$result = $this->room_model->getRoomTicket($request_ary);
		
			$data['base_url'] = $this->domain_path();
			
			$data['socket'] = $DelaerConst::WebSocket_Host;
			$data['user'] = $userinfo_result['data'];
			$data['card'] = $result['data']['ticket_count'];

			$data['open_id'] = "";
			$data['account_id'] = $userinfo_result['data']['account_id'];

			$data['room_url'] =base_url("d/home/".$dealer_num);
			$data['dealer_num'] = $dealer_num;
			
			$this->load->model('wechat_model','',true);
			$config_ary = $this->wechat_model->getWxConfig($dealer_num);
			$data['config_ary']=$config_ary;

			$this->load->view("d_".$dealer_num."/agentRedPackageRecord.php", $data);
		} else {
			$direct_url = base_url("d/relist/".$dealer_num);
			Header("Location:".$direct_url);
		}
	}

	//代理商红包详情v
	public function agentRedPackageDetail() {

		//$this->testSetupOpenID();

		$is_weixin = $this->is_weixin();

		//判断代理商编号是否存在
		if(!isset($_GET['dealer_num']))
		{
			log_message('error', "function(agentRedPackageDetail):lack of dealer_num:"." in file".__FILE__." on Line ".__LINE__);
			show_404();exit;
		}

		$dealer_num = filter_var($_GET['dealer_num']);
		$DelaerConst = "Dealer_".$dealer_num;
		if(!class_exists($DelaerConst))
		{
			log_message('error', "function(agentRedPackageDetail):dealer not exist:".$dealer_num." in file".__FILE__." on Line ".__LINE__);
			show_404();exit;
		}

		if (isset($_SESSION['WxOpenID']) && isset($_GET['red_code'])) {
			$open_id = $_SESSION['WxOpenID'];
			$red_code = $_GET['red_code'];

			$request_ary['dealer_num'] = $dealer_num;
			$request_ary['open_id'] = $open_id;
			$request_ary['code'] = $red_code;

			$this->load->model('account/account_model','',true);
			$userinfo_result = $this->account_model->getUserInfo($request_ary);
			if(!isset($userinfo_result['data']['account_id']) || $userinfo_result['data']['account_id'] == "")
			{
				$direct_url = base_url("d/re/".$dealer_num."/".$_GET['red_code']);
				Header("Location:".$direct_url);
				return;
			}

            if (! $this->checkIsAgree()) {
                return $this->warm();
            }
			$request_ary['account_id'] =$userinfo_result['data']['account_id'];
			$this->load->model('flower/room_model','',true);
			$result = $this->room_model->getRoomTicket($request_ary);

            $this->load->model('activity/redenvelop_model','',true);
			$redenvelop_result = $this->redenvelop_model->getRedEnvelopData($request_ary);
		
			$data['base_url'] = $this->domain_path();
			
			$data['socket'] = $DelaerConst::WebSocket_Host;
			$data['user'] = $userinfo_result['data'];
			$data['card'] = $result['data']['ticket_count'];

			$data['open_id'] = "";
			$data['account_id'] = $userinfo_result['data']['account_id'];

			$data['red_package'] = $redenvelop_result['data'];
			$data['red_code'] = $red_code;

			$data['room_url'] =base_url("d/home/".$dealer_num);
			$shareURL = "d/re/".$dealer_num."/".$red_code;
			$data['share_url'] =  base_url($shareURL);
			$data['share_icon'] = base_url("files/images/redpackage/share_icon.jpg");
			$data['dealer_num'] = $dealer_num;
			
			$this->load->model('wechat_model','',true);
			$config_ary = $this->wechat_model->getWxConfig($dealer_num);
			$data['config_ary']=$config_ary;

			$this->load->view("d_".$dealer_num."/agentRedPackageDetail.php", $data);
		}
		else if(isset($_GET['red_code']))
		{
			$direct_url = base_url("d/re/".$dealer_num."/".$_GET['red_code']);
			Header("Location:".$direct_url);
		}
		 else {
			$direct_url = base_url("d/relist/".$dealer_num);
			Header("Location:".$direct_url);
		}
	}

	public function createAgentRedPackage() {
		$params = json_decode(file_get_contents('php://input'),true);
		
		if ( 
			isset($params['ticket_count']) && 
			isset($params['content']) && 
			isset($params['dealer_num']) && 
			isset($params['dealer_screct'])) {

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
			$dealer_screct = $params['dealer_screct'];

			$request_ary['dealer_num'] = $dealer_num;
			$request_ary['account_id'] = $account_id;
			$request_ary['ticket_count'] = $ticket_count;
			$request_ary['content'] = $content;
			$request_ary['dealer_screct'] = $dealer_screct;
			$this->load->model('dealer/dealer_model','',true);
			$result = $this->dealer_model->createRedEnvelopOpt($request_ary);
			
			print_r(json_encode($result));
			
		} else {
			show_404();	
		}
	}

	//获取代理商发出红包的记录
	public function getAgentOutRedPackage() {
		$params = json_decode(file_get_contents('php://input'),true);
		
		if (
			isset($params['page']) && 
			isset($params['dealer_num'])) {

			$open_id = $_SESSION['WxOpenID'];
			$dealer_num = $params['dealer_num'];
			$account_id = $this->getAccountIdByOpenid($dealer_num,$open_id);
			if($account_id <= 0)
			{
				show_404();	return;
			}
			
			//$account_id = $params['account_id'];
			$page = $params['page'];
			$dealer_num = $params['dealer_num'];

			$request_ary['dealer_num'] = $dealer_num;
			$request_ary['account_id'] = $account_id;
			$request_ary['page'] = $page;
			$this->load->model('dealer/dealer_model','',true);
			$result = $this->dealer_model->getRedEnvelopList($request_ary);
            
			print_r(json_encode($result));
			
		} else { 
			show_404();
		}	
	}
	
}
