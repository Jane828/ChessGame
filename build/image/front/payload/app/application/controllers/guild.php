<?php

include_once dirname(__DIR__).'/third_party/phpcode_shield.php';		//加载防注入代码
class Guild extends MY_Controller
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
	
	

	//公会成员view
	public function unionmember() {

		$is_weixin = $this->is_weixin();
		
		//判断代理商编号是否存在
		if(!isset($_GET['dealer_num']))
		{
			log_message('error', "function(home):lack of dealer_num:".$dealer_num." in file".__FILE__." on Line ".__LINE__);
			show_404();exit;
		}

		$dealer_num = filter_var($_GET['dealer_num']);
		$DelaerConst = "Dealer_".$dealer_num;
		if(!class_exists($DelaerConst))
		{
			log_message('error', "function(home):dealer not exist:".$dealer_num." in file".__FILE__." on Line ".__LINE__);
			show_404();exit;
		}

		if(isset($_SESSION['WxOpenID']))
		{
			$open_id = $_SESSION['WxOpenID'];
			
			$request_ary['dealer_num'] = $dealer_num;
			$request_ary['open_id'] = $open_id;
			$this->load->model('account/account_model','',true);
			$userinfo_result = $this->account_model->getUserInfoGuild($request_ary);
			if(!isset($userinfo_result['data']['account_id']) || $userinfo_result['data']['account_id'] == "")
			{
				$direct_url = base_url("d/relist/".$dealer_num);
				Header("Location:".$direct_url);
				return;
			}
			
			$request_ary['account_id'] =$userinfo_result['data']['account_id'];
		
			$data['base_url'] = $this->domain_path();
			
			$data['user'] = $userinfo_result['data'];

			$data['open_id'] = "";
			$data['account_id'] = $userinfo_result['data']['account_id'];
			$data['group_id'] = $userinfo_result['data']['group_id'];

			$data['room_url'] = base_url("d/home/".$dealer_num);
			$data['share_url'] =  base_url("d/relist/".$dealer_num);
			$data['share_icon'] = base_url("files/images/game/home.jpg");
			$data['dealer_num'] = $dealer_num;
			$data['file_url'] = "https://gameoss.fexteam.com/";
			$data['image_url'] = "http://goss.fexteam.com/";

			$this->load->model('dwechat_model','',true);
			$config_ary = $this->dwechat_model->getWxConfig($dealer_num);
			$data['config_ary']=$config_ary;

			$this->load->view("d_".$dealer_num."/guild/unionmember.php", $data);
		} else {
			$direct_url = base_url("d/relist/".$dealer_num);
			Header("Location:".$direct_url);
		}
	}

    //我的成员view
	public function mymember() {

		$is_weixin = $this->is_weixin();
		
		//判断代理商编号是否存在
		if(!isset($_GET['dealer_num']))
		{
			log_message('error', "function(home):lack of dealer_num:".$dealer_num." in file".__FILE__." on Line ".__LINE__);
			show_404();exit;
		}

		$dealer_num = filter_var($_GET['dealer_num']);
		$DelaerConst = "Dealer_".$dealer_num;
		if(!class_exists($DelaerConst))
		{
			log_message('error', "function(home):dealer not exist:".$dealer_num." in file".__FILE__." on Line ".__LINE__);
			show_404();exit;
		}

		if(isset($_SESSION['WxOpenID']))
		{
			$open_id = $_SESSION['WxOpenID'];
			
			$request_ary['dealer_num'] = $dealer_num;
			$request_ary['open_id'] = $open_id;
			$this->load->model('account/account_model','',true);
			$userinfo_result = $this->account_model->getUserInfoGuild($request_ary);
			if(!isset($userinfo_result['data']['account_id']) || $userinfo_result['data']['account_id'] == "")
			{
				$direct_url = base_url("d/relist/".$dealer_num);
				Header("Location:".$direct_url);
				return;
			}
			
			$request_ary['account_id'] =$userinfo_result['data']['account_id'];
		
			$data['base_url'] = $this->domain_path();
			
			$data['user'] = $userinfo_result['data'];
			$data['group_id'] = $userinfo_result['data']['group_id'];

			$data['open_id'] = "";
			$data['account_id'] = $userinfo_result['data']['account_id'];

			$data['room_url'] = base_url("d/home/".$dealer_num);
			$data['share_url'] =  base_url("d/relist/".$dealer_num);
			$data['share_icon'] = base_url("files/images/game/home.jpg");
			$data['dealer_num'] = $dealer_num;
			$data['file_url'] = "https://gameoss.fexteam.com/";
			$data['image_url'] = "http://goss.fexteam.com/";


			$this->load->model('dwechat_model','',true);
			$config_ary = $this->dwechat_model->getWxConfig($dealer_num);
			$data['config_ary']=$config_ary;

			$this->load->view("d_".$dealer_num."/guild/mymember.php", $data);
		} else {
			$direct_url = base_url("d/relist/".$dealer_num);
			Header("Location:".$direct_url);
		}
	}
    
	//公会收益view
	public function unionearnings() {

		$is_weixin = $this->is_weixin();
		
		//判断代理商编号是否存在
		if(!isset($_GET['dealer_num']))
		{
			log_message('error', "function(home):lack of dealer_num:".$dealer_num." in file".__FILE__." on Line ".__LINE__);
			show_404();exit;
		}

		$dealer_num = filter_var($_GET['dealer_num']);
		$DelaerConst = "Dealer_".$dealer_num;
		if(!class_exists($DelaerConst))
		{
			log_message('error', "function(home):dealer not exist:".$dealer_num." in file".__FILE__." on Line ".__LINE__);
			show_404();exit;
		}

		if(isset($_SESSION['WxOpenID']))
		{
			$open_id = $_SESSION['WxOpenID'];
			
			$request_ary['dealer_num'] = $dealer_num;
			$request_ary['open_id'] = $open_id;
			$this->load->model('account/account_model','',true);
			$userinfo_result = $this->account_model->getUserInfoGuild($request_ary);
			if(!isset($userinfo_result['data']['account_id']) || $userinfo_result['data']['account_id'] == "")
			{
				$direct_url = base_url("d/relist/".$dealer_num);
				Header("Location:".$direct_url);
				return;
			}
			
			$request_ary['account_id'] =$userinfo_result['data']['account_id'];
		
			$data['base_url'] = $this->domain_path();

			$data['user'] = $userinfo_result['data'];

			$data['open_id'] = "";
			$data['account_id'] = $userinfo_result['data']['account_id'];
			$data['group_id'] = $userinfo_result['data']['group_id'];

			$data['room_url'] = base_url("d/home/".$dealer_num);
			$data['share_url'] =  base_url("d/relist/".$dealer_num);
			$data['share_icon'] = base_url("files/images/game/home.jpg");
			$data['dealer_num'] = $dealer_num;
			$data['file_url'] = "https://gameoss.fexteam.com/";
			$data['image_url'] = "http://goss.fexteam.com/";

			$this->load->model('dwechat_model','',true);
			$config_ary = $this->dwechat_model->getWxConfig($dealer_num);
			$data['config_ary']=$config_ary;

			$this->load->view("d_".$dealer_num."/guild/unionearnings.php", $data);
		} else {
			$direct_url = base_url("d/relist/".$dealer_num);
			Header("Location:".$direct_url);
		}
	}

    //我的收益view
	public function myearnings() {

		$is_weixin = $this->is_weixin();
		
		//判断代理商编号是否存在
		if(!isset($_GET['dealer_num']))
		{
			log_message('error', "function(home):lack of dealer_num:".$dealer_num." in file".__FILE__." on Line ".__LINE__);
			show_404();exit;
		}

		$dealer_num = filter_var($_GET['dealer_num']);
		$DelaerConst = "Dealer_".$dealer_num;
		if(!class_exists($DelaerConst))
		{
			log_message('error', "function(home):dealer not exist:".$dealer_num." in file".__FILE__." on Line ".__LINE__);
			show_404();exit;
		}

		if(isset($_SESSION['WxOpenID']))
		{
			$open_id = $_SESSION['WxOpenID'];
			
			$request_ary['dealer_num'] = $dealer_num;
			$request_ary['open_id'] = $open_id;
			$this->load->model('account/account_model','',true);
			$userinfo_result = $this->account_model->getUserInfoGuild($request_ary);
			if(!isset($userinfo_result['data']['account_id']) || $userinfo_result['data']['account_id'] == "")
			{
				$direct_url = base_url("d/relist/".$dealer_num);
				Header("Location:".$direct_url);
				return;
			}
			
			$request_ary['account_id'] =$userinfo_result['data']['account_id'];
		
			$data['base_url'] = $this->domain_path();
			
			$data['user'] = $userinfo_result['data'];

			$data['open_id'] = "";
			$data['account_id'] = $userinfo_result['data']['account_id'];
			$data['group_id'] = $userinfo_result['data']['group_id'];

			$data['room_url'] = base_url("d/home/".$dealer_num);
			$data['share_url'] =  base_url("d/relist/".$dealer_num);
			$data['share_icon'] = base_url("files/images/game/home.jpg");
			$data['dealer_num'] = $dealer_num;
			$data['file_url'] = "https://gameoss.fexteam.com/";
			$data['image_url'] = "http://goss.fexteam.com/";

			$this->load->model('dwechat_model','',true);
			$config_ary = $this->dwechat_model->getWxConfig($dealer_num);
			$data['config_ary']=$config_ary;

			$this->load->view("d_".$dealer_num."/guild/myearnings.php", $data);
		} else {
			$direct_url = base_url("d/relist/".$dealer_num);
			Header("Location:".$direct_url);
		}
	}
    
	//副会长结算记录view
	public function earningsRecord() {

		$is_weixin = $this->is_weixin();
		
		//判断代理商编号是否存在
		if(!isset($_GET['dealer_num']))
		{
			log_message('error', "function(home):lack of dealer_num:".$dealer_num." in file".__FILE__." on Line ".__LINE__);
			show_404();exit;
		}

		$dealer_num = filter_var($_GET['dealer_num']);
		$DelaerConst = "Dealer_".$dealer_num;
		if(!class_exists($DelaerConst))
		{
			log_message('error', "function(home):dealer not exist:".$dealer_num." in file".__FILE__." on Line ".__LINE__);
			show_404();exit;
		}

		if(isset($_SESSION['WxOpenID']))
		{
			$open_id = $_SESSION['WxOpenID'];
			
			$request_ary['dealer_num'] = $dealer_num;
			$request_ary['open_id'] = $open_id;
			$this->load->model('account/account_model','',true);
			$userinfo_result = $this->account_model->getUserInfoGuild($request_ary);
			if(!isset($userinfo_result['data']['account_id']) || $userinfo_result['data']['account_id'] == "")
			{
				$direct_url = base_url("d/relist/".$dealer_num);
				Header("Location:".$direct_url);
				return;
			}
			
			$request_ary['account_id'] =$userinfo_result['data']['account_id'];
		
			$data['base_url'] = $this->domain_path();
			
			$data['user'] = $userinfo_result['data'];

			$data['open_id'] = "";
			$data['account_id'] = $userinfo_result['data']['account_id'];
			$data['group_id'] = $userinfo_result['data']['group_id'];

			$data['room_url'] = base_url("d/home/".$dealer_num);
			$data['share_url'] =  base_url("d/relist/".$dealer_num);
			$data['share_icon'] = base_url("files/images/game/home.jpg");
			$data['dealer_num'] = $dealer_num;
			$data['file_url'] = "https://gameoss.fexteam.com/";
			$data['image_url'] = "http://goss.fexteam.com/";

			$this->load->model('dwechat_model','',true);
			$config_ary = $this->dwechat_model->getWxConfig($dealer_num);
			$data['config_ary']=$config_ary;

			$this->load->view("d_".$dealer_num."/guild/earningsRecord.php", $data);
		} else {
			$direct_url = base_url("d/relist/".$dealer_num);
			Header("Location:".$direct_url);
		}
	}
    

	//公会邀请函view
	public function unioninvite() {

		$is_weixin = $this->is_weixin();
		
		//判断代理商编号是否存在
		if(!isset($_GET['dealer_num']))
		{
			log_message('error', "function(unioninvite):lack of dealer_num:".$dealer_num." in file".__FILE__." on Line ".__LINE__);
			show_404();exit;
		}

		$dealer_num = filter_var($_GET['dealer_num']);
		$DelaerConst = "Dealer_".$dealer_num;
		if(!class_exists($DelaerConst))
		{
			log_message('error', "function(unioninvite):dealer not exist:".$dealer_num." in file".__FILE__." on Line ".__LINE__);
			show_404();exit;
		}

		if(isset($_SESSION['WxOpenID']) && isset($_GET['invite_code']))
		{
			$open_id = $_SESSION['WxOpenID'];
			
			$request_ary['dealer_num'] = $dealer_num;
			$request_ary['open_id'] = $open_id;
			$this->load->model('account/account_model','',true);
			$userinfo_result = $this->account_model->getUserInfo($request_ary);
			if(!isset($userinfo_result['data']['account_id']) || $userinfo_result['data']['account_id'] == "")
			{
				$direct_url = base_url("d/invite/".$dealer_num."/".$_GET['invite_code']);
				Header("Location:".$direct_url);
				return;
			}

			$request_ary['account_id'] =$userinfo_result['data']['account_id'];
		
			$data['base_url'] = $this->domain_path();
			
			$data['user'] = $userinfo_result['data'];

			$data['open_id'] = "";
			$data['account_id'] = $userinfo_result['data']['account_id'];
			$data['phone'] = $userinfo_result['data']['phone'];

			$data['room_url'] = base_url("d/invite/".$dealer_num."/".$_GET['invite_code']);
			$data['share_url'] =  base_url("d/invite/".$dealer_num."/".$_GET['invite_code']);
			$data['share_icon'] = base_url("files/images/guild/invitation.png");
			$data['dealer_num'] = $dealer_num;
			$data['file_url'] = "https://gameoss.fexteam.com/";
			$data['image_url'] = "http://goss.fexteam.com/";

			$request_ary['invite_code'] = $_GET['invite_code'];
			$request_ary['account_id'] = $userinfo_result['data']['account_id'];
			$request_ary['dealer_num'] = $dealer_num;
			$this->load->model('guild/invite_model','invite_model',true);
			$result2 = $this->invite_model->getInviteData($request_ary);
			if(!isset($result2['result']) || $result2['result'] != "0")
			{
				log_message('error', "function(unioninvite):getInviteData :"." in file".__FILE__." on Line ".__LINE__);
				show_404();exit;
			}
			
			$this->load->model('dwechat_model','',true);
			$config_ary = $this->dwechat_model->getWxConfig($dealer_num);
			$data['config_ary']=$config_ary;

            			
			$data['invite_code'] = $_GET['invite_code'];
			$data['invite_nickname'] = $result2['data']['nickname'];
			$data['invite_headimgurl'] = $result2['data']['headimgurl'];
			$data['guild_name'] = $result2['data']['guild_name'];
			$data['guild_profile'] = $result2['data']['guild_profile'];
			$data['is_owner'] = $result2['data']['is_owner'];
			$data['is_join'] = $result2['data']['is_join'];
			$data['qr_url'] = $result2['data']['qr_url'];
			$data['wx_name'] = $result2['data']['wx_name'];

		
			$this->load->view("d_".$dealer_num."/guild/inviteDetail.php", $data);
		} else {
			$direct_url = base_url("d/invite/".$dealer_num."/".$_GET['invite_code']);
			Header("Location:".$direct_url);
			return;
		}
	}

	
	/*
		加入公会
	*/
	public function joinGuild()
	{
		$params = json_decode(file_get_contents('php://input'),true);
		if(isset($params['account_id']) && isset($params['invite_code']) && isset($params['dealer_num']))
		{
			$open_id = $_SESSION['WxOpenID'];
			$dealer_num = $params['dealer_num'];
			$account_id = $this->getAccountIdByOpenid($dealer_num,$open_id);
			if($account_id <= 0)
			{
				show_404();	return;
			}

			$request_array['dealer_num'] = $params['dealer_num'];
			$request_array['invite_code'] = $params['invite_code'];
			$request_array['account_id'] = $account_id;
			$this->load->model('guild/invite_model','invite_model',true);
			$result = $this->invite_model->joinGuild($request_array);
			
			print_r(json_encode($result));
		}
		else
		{
			show_404();	
		}
	}
	
	/*
		清零副会长余额
	*/
	public function clearGuildViceBalance()
	{
		$params = file_get_contents('php://input');
		$params = json_decode($params,true);
        
		if(isset($params['my_aid']) && isset($params['dealer_num']) && isset($params['group_id'])&& isset($params['vice_president']))
		{
			$request_array['my_aid'] = $params['my_aid'];
		    $request_array['dealer_num'] = $params['dealer_num'];
		    $request_array['group_id'] = $params['group_id'];
		    $request_array['vice_president'] = $params['vice_president'];

		    $this->load->model('guild/guild_model','guild_model',true);
		    $result = $this->guild_model->clearViceBalanceOpt($request_array);
            
			$result = json_encode($result);
		    print_r($result);
			exit;
		}
		else
		{
			show_404();	
		}
	}

	/*
		获取副会长结算记录
	*/
	public function getGuildViceBalanceHistory()
	{
		$params = file_get_contents('php://input');
		$params = json_decode($params,true);
        
		if(isset($params['dealer_num']) && isset($params['group_id']) && isset($params['page'])&& isset($params['account_id']))
		{
			$request_array['dealer_num'] = $params['dealer_num'];
		    $request_array['group_id'] = $params['group_id'];
		    $request_array['page'] = $params['page'];
			$request_array['account_id'] = $params['account_id'];
			
		    $this->load->model('guild/guild_model','guild_model',true);
		    $result = $this->guild_model->getViceBalanceHistory($request_array);
        
		    $result = json_encode($result);
		    print_r($result);
			exit;
		}
		else
		{
			show_404();	
		}
	}

	/*
		副会长的收益
	*/
	public function getMemberCommissionList()
	{
		$params = file_get_contents('php://input');
		$params = json_decode($params,true);
        
		if(isset($params['dealer_num']) && isset($params['group_id']) && isset($params['page'])&& isset($params['my_aid']))
		{
			$request_array['dealer_num'] = $params['dealer_num'];
		    $request_array['group_id'] = $params['group_id'];
		    $request_array['page'] = $params['page'];
			$request_array['my_aid'] = $params['my_aid'];
			
		    $this->load->model('guild/guild_model','guild_model',true);
		    $result = $this->guild_model->getMemberCommissionList($request_array);
        
		    $result = json_encode($result);
		    print_r($result);
			exit;
		}
		else
		{
			show_404();	
		}
	}

	/*
		公会收益
	*/
	public function getGuildViceCommission()
	{
		$params = file_get_contents('php://input');
		$params = json_decode($params,true);
        

		if(isset($params['dealer_num']) && isset($params['group_id']) && isset($params['page']) && isset($params['account_id']))
		{
			$request_array['dealer_num'] = $params['dealer_num'];
		    $request_array['group_id'] = $params['group_id'];
		    $request_array['page'] = $params['page'];
			$request_array['account_id'] = $params['account_id'];
			
		    $this->load->model('guild/guild_model','guild_model',true);
		    $result = $this->guild_model->getViceCommission($request_array);
        
		    $result = json_encode($result);
		    print_r($result);
			exit;
		}
		else
		{
			show_404();	
		}
	}


	/*
		搜索公会成员
	*/
	public function searchGuildMember()
	{
		$params = file_get_contents('php://input');
		$params = json_decode($params,true);
        
		if(isset($params['dealer_num']) && isset($params['group_id']) && isset($params['page'])&& isset($params['type'])&& isset($params['nickname'])&& isset($params['account_id']))
		{
			$request_array['dealer_num'] = $params['dealer_num'];
			$request_array['group_id'] = $params['group_id'];
			$request_array['nickname'] = $params['nickname'];
			$request_array['page'] = $params['page'];
			$request_array['type'] = $params['type'];
			$request_array['account_id'] = $params['account_id'];

			$this->load->model('guild/guild_model','guild_model',true);
			$result = $this->guild_model->searchMember($request_array);
			
			$result = json_encode($result);
			print_r($result);
			exit;
		}
		else
		{
			show_404();	
		}
		
	}
	
}
