<?php

include_once dirname(__DIR__) . '/third_party/phpcode_shield.php';        //加载防注入代码
// header("Content-Security-Policy: upgrade-insecure-requests");
class F extends MY_Controller {
    /*
        构造函数
    */
    function __construct() {
        parent::__construct();
    }

    private function getAccountIdByOpenid($open_id) {
        $request_ary['open_id'] = $open_id;
        $this->load->model('account/account_model', '', TRUE);
        $userinfo_result = $this->account_model->getUserInfo($request_ary);
        if (!isset($userinfo_result['data']['account_id']) || $userinfo_result['data']['account_id'] == "") {
            return -1;
        }
        $account_id = $userinfo_result['data']['account_id'];
        return $account_id;
    }

    public function agree() {
        if (!isset($_SESSION['WxOpenID'])) {
            $direct_url = base_url("y/ym");
            Header("Location:" . $direct_url);
            return;
        }

        $open_id                = $_SESSION['WxOpenID'];
        $request_ary['open_id'] = $open_id;
        $this->load->model('account/account_model', '', TRUE);
        $userinfo_result = $this->account_model->getUserInfo($request_ary);
        if (!isset($userinfo_result['data']['account_id']) || $userinfo_result['data']['account_id'] == "") {
            $direct_url = base_url("y/ym");
            Header("Location:" . $direct_url);
            return;
        }

        $_SESSION['isAgree'] = TRUE;
    }

    public function ym() {
        if (isset($_SESSION['WxOpenID'])) {
            $open_id                = $_SESSION['WxOpenID'];
            $request_ary['open_id'] = $open_id;
            $this->load->model('account/account_model', '', TRUE);
            $userinfo_result = $this->account_model->getUserInfo($request_ary);
            if (!isset($userinfo_result['data']['account_id']) || $userinfo_result['data']['account_id'] == "") {
                $direct_url = base_url("y/ym");
                Header("Location:" . $direct_url);
                return;
            }
            if (!$this->checkIsAgree()) {
                return $this->warm();
            }
            $request_ary['account_id'] = $userinfo_result['data']['account_id'];
            $this->load->model('flower/room_model', '', TRUE);
            $result = $this->room_model->getRoomTicket($request_ary);

            $session_request['dealer_num'] = 2;
            $session_request['account_id'] = $userinfo_result['data']['account_id'];
            $session_request['type']       = '';
            $this->load->model('verification_model', '', TRUE);
            $sessionResult   = $this->verification_model->createRequestSession($session_request);
            $data['session'] = $sessionResult;

            $this->load->model('wechat_model', '', TRUE);
            $config_ary         = $this->wechat_model->getWxConfig();
            $data['config_ary'] = $config_ary;

            $data['base_url'] = $this->domain_path();

            $data['socket1']          = Game_Const::WebSocket_Host . "/f6";
            $data['socket2']          = Game_Const::WebSocket_Landlord;
            $data['socket3']          = Game_Const::WebSocket_Host . "/bu";
            $data['socket4']          = Game_Const::WebSocket_Gdmj;
            $data['socket5']          = Game_Const::WebSocket_Host . "/nb";
            $data['socket_tbull']     = Game_CONST::WebSocket_Host . "/tb";
            $data['socket_fbull']     = Game_CONST::WebSocket_Host . "/fb";
            $data['socket_tflower']   = Game_CONST::WebSocket_Host . "/f10";
            $data['socket_sangong']   = Game_CONST::WebSocket_Host . "/sg6";
            $data['socket_nsangong']  = Game_CONST::WebSocket_Host . "/sg9";
            $data['socket_tsangong']  = Game_CONST::WebSocket_Host;
            $data['socket_lbull']     = Game_CONST::WebSocket_Host . "/lb";
            $data['socket_vbull6']    = Game_CONST::WebSocket_Host . "/vbu";
            $data['socket_vbull9']    = Game_CONST::WebSocket_Host . "/vnb";
            $data['socket_vbull12']   = Game_CONST::WebSocket_Host . "/vtb";
            $data['socket_vflower6']  = Game_CONST::WebSocket_Host . "/vf6";
            $data['socket_vflower10'] = Game_CONST::WebSocket_Host . "/vf10";

            $data['user']       = $userinfo_result['data'];
            $data['card']       = $result['data']['ticket_count'];
            $data['account_id'] = $userinfo_result['data']['account_id'];
            $data['room_url']   = base_url("y/ym");

            $data['file_url']      = Game_Const::ImageUrl;
            $data['image_url']     = Game_Const::ImageUrl;
            $data['http_url']      = "https://connwss.fairyland.xin/";
            $data['dealer_num']    = 2;
            $data['isAuthPhone']   = 0;
            $data['authCardCount'] = 10;
            $data['phone']         = '';
            $data['open_id']       = '';
            $data['front_version'] = Game_CONST::Front_Version;

            $this->load->model('activity/activity_model', '', TRUE);
            $broadcast         = $this->activity_model->getBroadcastInfo(1000);
            $data['broadcast'] = $broadcast;

            $data['create_info'] = $this->account_model->getCreateInfo($userinfo_result['data']['account_id']);

            $this->load->view("hall.php", $data);
        } else {
            $direct_url = base_url("y/ym");
            Header("Location:" . $direct_url);
        }
    }

    public function ci() {
        $params = json_decode(file_get_contents('php://input'), TRUE);
        if (isset($params['account_id']) && isset($params['create_info']) && isset($_SESSION['WxOpenID'])) {
            $request_ary['open_id'] = $_SESSION['WxOpenID'];
            $this->load->model('account/account_model', '', TRUE);
            $userinfo_result = $this->account_model->getUserInfo($request_ary);
            if ($userinfo_result['result'] == OPT_CONST::SUCCESS
                && $params['account_id'] == $userinfo_result['data']['account_id']
                && !empty($params['create_info'])
            ) {
                $result = $this->account_model->storeCreateInfo($params['account_id'], $params['create_info']);
                echo json_encode($result);
                exit();
            }
        }
    }

    public function nb() {

        if (isset($_SESSION['WxOpenID']) && isset($_GET['i'])) {
            $open_id = $_SESSION['WxOpenID'];

            $state = $_GET['i'];
            if (strpos($state, "_") !== FALSE) {
                $state_array = explode("_", $state);
                $room_number = $state_array[0];
                $user_code   = $state_array[1];
            } else {
                log_message('error', "function(nb):state" . " in file" . __FILE__ . " on Line " . __LINE__);
                show_404();
                exit;
            }

            $request_ary['open_id']   = $open_id;
            $request_ary['user_code'] = $user_code;
            $this->load->model('account/account_model', '', TRUE);
            $userinfo_result = $this->account_model->getUserInfo($request_ary);
            if (!isset($userinfo_result['data']['account_id']) || $userinfo_result['data']['account_id'] == "") {
                //				log_message('error', "function(nb):state"." in file".__FILE__." on Line ".__LINE__);
                //				show_404();exit;

                $redirect_uri = $this->domain_path() . 'y/IDnb';
                $direct_url   = $this->getWxOauthUrl($redirect_uri, 'snsapi_userinfo');
                Header("Location:" . $direct_url);
                exit;
            }
            if (!$this->checkIsAgree()) {
                return $this->warm();
            }
            $request_ary['account_id'] = $userinfo_result['data']['account_id'];
            $this->load->model('flower/room_model', '', TRUE);
            $result = $this->room_model->getRoomTicket($request_ary);

            $session_request['dealer_num'] = 2;
            $session_request['account_id'] = $userinfo_result['data']['account_id'];
            $session_request['type']       = '';
            $this->load->model('verification_model', '', TRUE);
            $sessionResult   = $this->verification_model->createRequestSession($session_request);
            $data['session'] = $sessionResult;

            //获取关闭房间积分榜
            $request_ary['game_type']   = 9;
            $request_ary['room_number'] = $room_number;
            $score_result               = $this->room_model->getRoomScoreboard($request_ary);
            if (isset($score_result['data']['room_status']) && isset($score_result['data']['balance_scoreboard'])) {
                $data['room_status']        = $score_result['data']['room_status'];
                $data['balance_scoreboard'] = $this->jsonEncodeOptions($score_result['data']['balance_scoreboard']);
            } else {
                $data['room_status']        = -1;
                $data['balance_scoreboard'] = "";
            }

            $this->load->model('wechat_model', '', TRUE);
            $config_ary         = $this->wechat_model->getWxConfig();
            $data['config_ary'] = $config_ary;

            $data['user']        = $userinfo_result['data'];
            $data['socket']      = Game_Const::WebSocket_Host . "/nb";
            $data['base_url']    = $this->domain_path();
            $data['card']        = $result['data']['ticket_count'];
            $data['room_number'] = $room_number;
            $data['room_url']    = $data['base_url'] . "y/nb/" . $room_number . "_" . $userinfo_result['data']['user_code'];
            $data['file_url']    = Game_Const::ImageUrl;
            $data['image_url']   = Game_Const::ImageUrl;
            $data['http_url']    = "https://connwss.fairyland.xin/";
            $data['front_version'] = Game_CONST::Front_Version;

            $data['dealer_num']    = 2;
            $data['open_id']       = '';
            $data['isAuthPhone']   = 0;
            $data['authCardCount'] = 10;
            $data['phone']         = '';

            $this->load->model('activity/activity_model', '', TRUE);
            $broadcast         = $this->activity_model->getBroadcastInfo(9);
            $data['broadcast'] = $broadcast;

            $this->load->view("nbull.php", $data);
        } else if (isset($_GET['i'])) {
            $direct_url = base_url("y/nb/" . $_GET['i']);
            Header("Location:" . $direct_url);
        }
    }

    public function b() {
        if (isset($_SESSION['WxOpenID']) && isset($_GET['i'])) {
            $open_id = $_SESSION['WxOpenID'];

            $state = $_GET['i'];
            if (strpos($state, "_") !== FALSE) {
                $state_array = explode("_", $state);
                $room_number = $state_array[0];
                $user_code   = $state_array[1];
            } else {
                log_message('error', "function(b):state" . " in file" . __FILE__ . " on Line " . __LINE__);
                show_404();
                exit;
            }

            $request_ary['open_id']   = $open_id;
            $request_ary['user_code'] = $user_code;
            $this->load->model('account/account_model', '', TRUE);
            $userinfo_result = $this->account_model->getUserInfo($request_ary);
            if (!isset($userinfo_result['data']['account_id']) || $userinfo_result['data']['account_id'] == "") {
                //				log_message('error', "function(b):state"." in file".__FILE__." on Line ".__LINE__);
                //				show_404();exit;

                $redirect_uri = $this->domain_path() . 'y/IDb';
                $direct_url   = $this->getWxOauthUrl($redirect_uri, 'snsapi_userinfo');
                Header("Location:" . $direct_url);
                exit;
            }
            if (!$this->checkIsAgree()) {
                return $this->warm();
            }
            $request_ary['account_id'] = $userinfo_result['data']['account_id'];
            $this->load->model('flower/room_model', '', TRUE);
            $result = $this->room_model->getRoomTicket($request_ary);

            $session_request['dealer_num'] = 2;
            $session_request['account_id'] = $userinfo_result['data']['account_id'];
            $session_request['type']       = '';
            $this->load->model('verification_model', '', TRUE);
            $sessionResult   = $this->verification_model->createRequestSession($session_request);
            $data['session'] = $sessionResult;

            //获取关闭房间积分榜
            $request_ary['game_type']   = 5;
            $request_ary['room_number'] = $room_number;
            $score_result               = $this->room_model->getRoomScoreboard($request_ary);
            if (isset($score_result['data']['room_status']) && isset($score_result['data']['balance_scoreboard'])) {
                $data['room_status']        = $score_result['data']['room_status'];
                $data['balance_scoreboard'] = $this->jsonEncodeOptions($score_result['data']['balance_scoreboard']);
            } else {
                $data['room_status']        = -1;
                $data['balance_scoreboard'] = "";
            }

            $this->load->model('wechat_model', '', TRUE);
            $config_ary         = $this->wechat_model->getWxConfig();
            $data['config_ary'] = $config_ary;

            $data['user']        = $userinfo_result['data'];
            $data['socket']      = Game_Const::WebSocket_Host . "/bu";
            $data['base_url']    = $this->domain_path();
            $data['card']        = $result['data']['ticket_count'];
            $data['room_number'] = $room_number;
            $data['room_url']    = $data['base_url'] . "y/b/" . $room_number . "_" . $userinfo_result['data']['user_code'];
            $data['file_url']    = Game_Const::ImageUrl;
            $data['image_url']   = Game_Const::ImageUrl;
            $data['http_url']    = "https://connwss.fairyland.xin/";
            $data['front_version'] = Game_CONST::Front_Version;

            $data['dealer_num']    = 2;
            $data['open_id']       = '';
            $data['isAuthPhone']   = 0;
            $data['authCardCount'] = 10;
            $data['phone']         = '';

            $this->load->model('activity/activity_model', '', TRUE);
            $broadcast         = $this->activity_model->getBroadcastInfo(5);
            $data['broadcast'] = $broadcast;

            $this->load->view("bull.php", $data);
        } else if (isset($_GET['i'])) {
            $direct_url = base_url("y/b/" . $_GET['i']);
            Header("Location:" . $direct_url);
        }
    }

    public function tb() {
        $this->checkLogin();

        if (isset($_GET['i'])) {
            $open_id = $_SESSION['WxOpenID'];

            $state = $_GET['i'];
            if (strpos($state, "_") !== FALSE) {
                $state_array = explode("_", $state);
                $room_number = $state_array[0];
                $user_code   = $state_array[1];
            } else {
                log_message('error', "function(nb):state" . " in file" . __FILE__ . " on Line " . __LINE__);
                show_404();
                exit;
            }

            $request_ary['open_id']   = $open_id;
            $request_ary['user_code'] = $user_code;
            $this->load->model('account/account_model', '', TRUE);
            $userinfo_result = $this->account_model->getUserInfo($request_ary);
            if (!isset($userinfo_result['data']['account_id']) || $userinfo_result['data']['account_id'] == "") {
                //				log_message('error', "function(nb):state"." in file".__FILE__." on Line ".__LINE__);
                //				show_404();exit;

                $redirect_uri = $this->domain_path() . 'f/tb';
                $direct_url   = $this->getWxOauthUrl($redirect_uri, 'snsapi_userinfo');
                Header("Location:" . $direct_url);
                exit;
            }
            if (!$this->checkIsAgree()) {
                return $this->warm();
            }
            $request_ary['account_id'] = $userinfo_result['data']['account_id'];
            $this->load->model('flower/room_model', '', TRUE);
            $result = $this->room_model->getRoomTicket($request_ary);

            $session_request['dealer_num'] = 2;
            $session_request['account_id'] = $userinfo_result['data']['account_id'];
            $session_request['type']       = '';
            $this->load->model('verification_model', '', TRUE);
            $sessionResult   = $this->verification_model->createRequestSession($session_request);
            $data['session'] = $sessionResult;

            //获取关闭房间积分榜
            $request_ary['game_type']   = 12;
            $request_ary['room_number'] = $room_number;
            $score_result               = $this->room_model->getRoomScoreboard($request_ary);

            if (isset($score_result['data']['room_status']) && isset($score_result['data']['balance_scoreboard'])) {
                $data['room_status']        = $score_result['data']['room_status'];
                $data['balance_scoreboard'] = $this->jsonEncodeOptions($score_result['data']['balance_scoreboard']);
            } else {
                $data['room_status']        = -1;
                $data['balance_scoreboard'] = "";
            }

            $this->load->model('wechat_model', '', TRUE);
            $config_ary         = $this->wechat_model->getWxConfig();
            $data['config_ary'] = $config_ary;

            $data['user']        = $userinfo_result['data'];
            $data['socket']      = Game_Const::WebSocket_Host . "/tb";
            $data['base_url']    = $this->domain_path();
            $data['card']        = $result['data']['ticket_count'];
            $data['room_number'] = $room_number;
            $data['room_url']    = $data['base_url'] . "f/tb?i=" . $room_number . "_" . $userinfo_result['data']['user_code'];
            $data['file_url']    = Game_Const::ImageUrl;
            $data['image_url']   = Game_Const::ImageUrl;
            $data['http_url']    = "https://connwss.fairyland.xin/";
            $data['front_version'] = Game_CONST::Front_Version;

            $data['dealer_num']    = 2;
            $data['open_id']       = '';
            $data['isAuthPhone']   = 0;
            $data['authCardCount'] = 10;
            $data['phone']         = '';

            $this->load->model('activity/activity_model', '', TRUE);
            $broadcast         = $this->activity_model->getBroadcastInfo(12);
            $data['broadcast'] = $broadcast;

            $this->load->view("tbull.php", $data);
        }
    }
	
	public function fb() {
        $this->checkLogin();

        if (isset($_GET['i'])) {
            $open_id = $_SESSION['WxOpenID'];

            $state = $_GET['i'];
            if (strpos($state, "_") !== FALSE) {
                $state_array = explode("_", $state);
                $room_number = $state_array[0];
                $user_code   = $state_array[1];
            } else {
                log_message('error', "function(nb):state" . " in file" . __FILE__ . " on Line " . __LINE__);
                show_404();
                exit;
            }

            $request_ary['open_id']   = $open_id;
            $request_ary['user_code'] = $user_code;
            $this->load->model('account/account_model', '', TRUE);
            $userinfo_result = $this->account_model->getUserInfo($request_ary);
            if (!isset($userinfo_result['data']['account_id']) || $userinfo_result['data']['account_id'] == "") {
                //				log_message('error', "function(nb):state"." in file".__FILE__." on Line ".__LINE__);
                //				show_404();exit;

                $redirect_uri = $this->domain_path() . 'f/fb';
                $direct_url   = $this->getWxOauthUrl($redirect_uri, 'snsapi_userinfo');
                Header("Location:" . $direct_url);
                exit;
            }
            if (!$this->checkIsAgree()) {
                return $this->warm();
            }
            $request_ary['account_id'] = $userinfo_result['data']['account_id'];
            $this->load->model('flower/room_model', '', TRUE);
            $result = $this->room_model->getRoomTicket($request_ary);

            $session_request['dealer_num'] = 2;
            $session_request['account_id'] = $userinfo_result['data']['account_id'];
            $session_request['type']       = '';
            $this->load->model('verification_model', '', TRUE);
            $sessionResult   = $this->verification_model->createRequestSession($session_request);
            $data['session'] = $sessionResult;

            //获取关闭房间积分榜
            $request_ary['game_type']   = 12;
            $request_ary['room_number'] = $room_number;
            $score_result               = $this->room_model->getRoomScoreboard($request_ary);

            if (isset($score_result['data']['room_status']) && isset($score_result['data']['balance_scoreboard'])) {
                $data['room_status']        = $score_result['data']['room_status'];
                $data['balance_scoreboard'] = $this->jsonEncodeOptions($score_result['data']['balance_scoreboard']);
            } else {
                $data['room_status']        = -1;
                $data['balance_scoreboard'] = "";
            }

            $this->load->model('wechat_model', '', TRUE);
            $config_ary         = $this->wechat_model->getWxConfig();
            $data['config_ary'] = $config_ary;

            $data['user']        = $userinfo_result['data'];
            $data['socket']      = Game_Const::WebSocket_Host . "/fb";
            $data['base_url']    = $this->domain_path();
            $data['card']        = $result['data']['ticket_count'];
            $data['room_number'] = $room_number;
            $data['room_url']    = $data['base_url'] . "f/fb?i=" . $room_number . "_" . $userinfo_result['data']['user_code'];
            $data['file_url']    = Game_Const::ImageUrl;
            $data['image_url']   = Game_Const::ImageUrl;
            $data['http_url']    = "https://connwss.fairyland.xin/";
            $data['front_version'] = Game_CONST::Front_Version;

            $data['dealer_num']    = 2;
            $data['open_id']       = '';
            $data['isAuthPhone']   = 0;
            $data['authCardCount'] = 10;
            $data['phone']         = '';

            $this->load->model('activity/activity_model', '', TRUE);
            $broadcast         = $this->activity_model->getBroadcastInfo(12);
            $data['broadcast'] = $broadcast;

            $this->load->view("fbull.php", $data);
        }
    }

    public function ma() {

        if (isset($_SESSION['WxOpenID']) && isset($_GET['i'])) {
            $open_id = $_SESSION['WxOpenID'];

            $state = $_GET['i'];
            if (strpos($state, "_") !== FALSE) {
                $state_array = explode("_", $state);
                $room_number = $state_array[0];
                $user_code   = $state_array[1];
            } else {
                log_message('error', "function(b):state" . " in file" . __FILE__ . " on Line " . __LINE__);
                show_404();
                exit;
            }

            $request_ary['open_id']   = $open_id;
            $request_ary['user_code'] = $user_code;
            $this->load->model('account/account_model', '', TRUE);
            $userinfo_result = $this->account_model->getUserInfo($request_ary);
            if (!isset($userinfo_result['data']['account_id']) || $userinfo_result['data']['account_id'] == "") {
                log_message('error', "function(b):state" . " in file" . __FILE__ . " on Line " . __LINE__);
                show_404();
                exit;
            }

            $request_ary['account_id'] = $userinfo_result['data']['account_id'];
            $this->load->model('flower/room_model', '', TRUE);
            $result = $this->room_model->getRoomTicket($request_ary);

            $session_request['dealer_num'] = 2;
            $session_request['account_id'] = $userinfo_result['data']['account_id'];
            $session_request['type']       = '';
            $this->load->model('verification_model', '', TRUE);
            $sessionResult   = $this->verification_model->createRequestSession($session_request);
            $data['session'] = $sessionResult;

            //获取关闭房间积分榜
            $request_ary['game_type']   = 6;
            $request_ary['room_number'] = $room_number;
            $score_result               = $this->room_model->getRoomScoreboard($request_ary);
            if (isset($score_result['data']['room_status']) && isset($score_result['data']['balance_scoreboard'])) {
                $data['room_status']        = $score_result['data']['room_status'];
                $data['balance_scoreboard'] = $this->jsonEncodeOptions($score_result['data']['balance_scoreboard']);
            } else {
                $data['room_status']        = -1;
                $data['balance_scoreboard'] = "";
            }

            $this->load->model('wechat_model', '', TRUE);
            $config_ary         = $this->wechat_model->getWxConfig();
            $data['config_ary'] = $config_ary;

            $data['user']        = $userinfo_result['data'];
            $data['socket']      = Game_Const::WebSocket_Gdmj;
            $data['base_url']    = $this->domain_path();
            $data['card']        = $result['data']['ticket_count'];
            $data['room_number'] = $room_number;
            $data['room_url']    = $data['base_url'] . "y/ma/" . $room_number . "_" . $userinfo_result['data']['user_code'];
            $data['file_url']    = Game_Const::ImageUrl;
            $data['image_url']   = Game_Const::ImageUrl;
            $data['http_url']    = "https://connwss.fairyland.xin/";
            $data['front_version'] = Game_CONST::Front_Version;

            $data['dealer_num']    = 2;
            $data['open_id']       = '';
            $data['isAuthPhone']   = 0;
            $data['authCardCount'] = 10;
            $data['phone']         = '';

            $this->load->view("majiang.php", $data);
        } else if (isset($_GET['i'])) {
            $direct_url = base_url("y/ma/" . $_GET['i']);
            Header("Location:" . $direct_url);
        }
    }

    //斗地主
    public function l() {

        if (isset($_SESSION['WxOpenID']) && isset($_GET['i'])) {
            $open_id = $_SESSION['WxOpenID'];

            $state = $_GET['i'];
            if (strpos($state, "_") !== FALSE) {
                $state_array = explode("_", $state);
                $room_number = $state_array[0];
                $user_code   = $state_array[1];
            } else {
                log_message('error', "function(l):state" . " in file" . __FILE__ . " on Line " . __LINE__);
                show_404();
                exit;
            }

            $request_ary['open_id']   = $open_id;
            $request_ary['user_code'] = $user_code;
            $this->load->model('account/account_model', '', TRUE);
            $userinfo_result = $this->account_model->getUserInfo($request_ary);
            if (!isset($userinfo_result['data']['account_id']) || $userinfo_result['data']['account_id'] == "") {
                log_message('error', "function(l):state" . " in file" . __FILE__ . " on Line " . __LINE__);
                show_404();
                exit;
            }

            $request_ary['account_id'] = $userinfo_result['data']['account_id'];
            $this->load->model('flower/room_model', '', TRUE);
            $result = $this->room_model->getRoomTicket($request_ary);

            $session_request['dealer_num'] = 2;
            $session_request['account_id'] = $userinfo_result['data']['account_id'];
            $session_request['type']       = '';
            $this->load->model('verification_model', '', TRUE);
            $sessionResult   = $this->verification_model->createRequestSession($session_request);
            $data['session'] = $sessionResult;

            //获取关闭房间积分榜
            $request_ary['game_type']   = 2;
            $request_ary['room_number'] = $room_number;
            $score_result               = $this->room_model->getRoomScoreboard($request_ary);
            if (isset($score_result['data']['room_status']) && isset($score_result['data']['balance_scoreboard'])) {
                $data['room_status']        = $score_result['data']['room_status'];
                $data['balance_scoreboard'] = $this->jsonEncodeOptions($score_result['data']['balance_scoreboard']);
            } else {
                $data['room_status']        = -1;
                $data['balance_scoreboard'] = "";
            }

            $this->load->model('wechat_model', '', TRUE);
            $config_ary         = $this->wechat_model->getWxConfig();
            $data['config_ary'] = $config_ary;

            $data['user']        = $userinfo_result['data'];
            $data['socket']      = Game_Const::WebSocket_Landlord;
            $data['base_url']    = $this->domain_path();
            $data['card']        = $result['data']['ticket_count'];
            $data['room_number'] = $room_number;
            $data['room_url']    = $data['base_url'] . "y/l/" . $room_number . "_" . $userinfo_result['data']['user_code'];
            $data['file_url']    = Game_Const::ImageUrl;
            $data['image_url']   = Game_Const::ImageUrl;
            $data['http_url']    = "https://connwss.fairyland.xin/";
            $data['front_version'] = Game_CONST::Front_Version;

            $data['dealer_num']    = 2;
            $data['open_id']       = '';
            $data['isAuthPhone']   = 0;
            $data['authCardCount'] = 10;
            $data['phone']         = '';

            $this->load->view("landlord.php", $data);
        } else if (isset($_GET['i'])) {
            $direct_url = base_url("y/l/" . $_GET['i']);
            Header("Location:" . $direct_url);
        }
    }


    //炸金花
    public function yf() {
        if (isset($_SESSION['WxOpenID']) && isset($_GET['i'])) {
            $open_id = $_SESSION['WxOpenID'];

            $state     = $_GET['i'];
            $user_code = "";
            if (strpos($state, "_") !== FALSE) {
                $state_array = explode("_", $state);
                $room_number = $state_array[0];
                $user_code   = $state_array[1];
            } else {
                log_message('error', "function(yf):state" . " in file" . __FILE__ . " on Line " . __LINE__);
                show_404();
                exit;
            }
            $request_ary['open_id']   = $open_id;
            $request_ary['user_code'] = $user_code;
            $this->load->model('account/account_model', '', TRUE);
            $userinfo_result = $this->account_model->getUserInfo($request_ary);
            if (!isset($userinfo_result['data']['account_id']) || $userinfo_result['data']['account_id'] == "") {
                //				log_message('error', "function(nb):state"." in file".__FILE__." on Line ".__LINE__);
                //				show_404();exit;

                $redirect_uri = $this->domain_path() . 'y/IDyf';
                $direct_url   = $this->getWxOauthUrl($redirect_uri, 'snsapi_userinfo');
                Header("Location:" . $direct_url);
                exit;
            }
            if (!$this->checkIsAgree()) {
                return $this->warm();
            }
            $request_ary['account_id'] = $userinfo_result['data']['account_id'];
            $this->load->model('flower/room_model', '', TRUE);
            $result = $this->room_model->getRoomTicket($request_ary);

            $session_request['dealer_num'] = 2;
            $session_request['account_id'] = $userinfo_result['data']['account_id'];
            $session_request['type']       = '';
            $this->load->model('verification_model', '', TRUE);
            $sessionResult   = $this->verification_model->createRequestSession($session_request);
            $data['session'] = $sessionResult;

            //获取关闭房间积分榜
            $request_ary['game_type']   = 1;
            $request_ary['room_number'] = $room_number;
            $score_result               = $this->room_model->getRoomScoreboard($request_ary);
            if (isset($score_result['data']['room_status']) && isset($score_result['data']['balance_scoreboard'])) {
                $data['room_status']        = $score_result['data']['room_status'];
                $data['balance_scoreboard'] = $this->jsonEncodeOptions($score_result['data']['balance_scoreboard']);
            } else {
                $data['room_status']        = -1;
                $data['balance_scoreboard'] = "";
            }

            $this->load->model('wechat_model', '', TRUE);
            $config_ary         = $this->wechat_model->getWxConfig();
            $data['config_ary'] = $config_ary;

            $data['user']        = $userinfo_result['data'];
            $data['socket']      = Game_Const::WebSocket_Host . "/f6";
            $data['base_url']    = $this->domain_path();
            $data['card']        = $result['data']['ticket_count'];
            $data['room_number'] = $room_number;
            $data['room_url']    = $data['base_url'] . "y/yf/" . $room_number . "_" . $userinfo_result['data']['user_code'];
            $data['file_url']    = Game_Const::ImageUrl;
            $data['image_url']   = Game_Const::ImageUrl;
            $data['http_url']    = "https://connwss.fairyland.xin/";
            $data['front_version'] = Game_CONST::Front_Version;

            $data['dealer_num']    = 2;
            $data['open_id']       = '';
            $data['isAuthPhone']   = 0;
            $data['authCardCount'] = 10;
            $data['phone']         = '';

            $this->load->model('activity/activity_model', '', TRUE);
            $broadcast          = $this->activity_model->getBroadcastInfo(1);
            $data['broadcast']  = $broadcast;
            $data['game_type']  = 1;
            $data['game_title'] = '飘三叶';

            $this->load->view("flower.php", $data);

        } else if (isset($_GET['i'])) {
            $direct_url = base_url("y/yf/" . $_GET['i']);
            Header("Location:" . $direct_url);
        }
    }

    //VIP六人炸金花
    public function vf() {
        if (isset($_SESSION['WxOpenID']) && isset($_GET['i'])) {
            $open_id = $_SESSION['WxOpenID'];

            $state = $_GET['i'];
            if (strpos($state, "_") !== FALSE) {
                $state_array = explode("_", $state);
                $room_number = $state_array[0];
                $user_code   = $state_array[1];
            } else {
                log_message('error', "function(vf):state" . " in file" . __FILE__ . " on Line " . __LINE__);
                show_404();
                exit;
            }
            $request_ary['open_id']   = $open_id;
            $request_ary['user_code'] = $user_code;
            $this->load->model('account/account_model', '', TRUE);
            $userinfo_result = $this->account_model->getUserInfo($request_ary);
            if (!isset($userinfo_result['data']['account_id']) || $userinfo_result['data']['account_id'] == "") {
                $redirect_uri = $this->domain_path() . 'y/IDvf';
                $direct_url   = $this->getWxOauthUrl($redirect_uri, 'snsapi_userinfo');
                Header("Location:" . $direct_url);
                exit;
            }
            if (!$this->checkIsAgree()) {
                return $this->warm();
            }
            $request_ary['account_id'] = $userinfo_result['data']['account_id'];
            $this->load->model('flower/room_model', '', TRUE);
            $result = $this->room_model->getRoomTicket($request_ary);

            $session_request['dealer_num'] = 2;
            $session_request['account_id'] = $userinfo_result['data']['account_id'];
            $session_request['type']       = '';
            $this->load->model('verification_model', '', TRUE);
            $sessionResult   = $this->verification_model->createRequestSession($session_request);
            $data['session'] = $sessionResult;

            //获取关闭房间积分榜
            $request_ary['game_type']   = 92;
            $request_ary['room_number'] = $room_number;
            $score_result               = $this->room_model->getRoomScoreboard($request_ary);
            if (isset($score_result['data']['room_status']) && isset($score_result['data']['balance_scoreboard'])) {
                $data['room_status']        = $score_result['data']['room_status'];
                $data['balance_scoreboard'] = $this->jsonEncodeOptions($score_result['data']['balance_scoreboard']);
            } else {
                $data['room_status']        = -1;
                $data['balance_scoreboard'] = "";
            }

            $this->load->model('wechat_model', '', TRUE);
            $config_ary         = $this->wechat_model->getWxConfig();
            $data['config_ary'] = $config_ary;

            $data['user']        = $userinfo_result['data'];
            $data['socket']      = Game_Const::WebSocket_Host . "/vf6";
            $data['base_url']    = $this->domain_path();
            $data['card']        = $result['data']['ticket_count'];
            $data['room_number'] = $room_number;
            $data['room_url']    = $data['base_url'] . "y/vf/" . $room_number . "_" . $userinfo_result['data']['user_code'];
            $data['file_url']    = Game_Const::ImageUrl;
            $data['image_url']   = Game_Const::ImageUrl;
            $data['http_url']    = "https://connwss.fairyland.xin/";
            $data['front_version'] = Game_CONST::Front_Version;

            $data['dealer_num']    = 2;
            $data['open_id']       = '';
            $data['isAuthPhone']   = 0;
            $data['authCardCount'] = 10;
            $data['phone']         = '';

            $this->load->model('activity/activity_model', '', TRUE);
            $broadcast          = $this->activity_model->getBroadcastInfo(1);
            $data['broadcast']  = $broadcast;
            $data['game_title'] = 'VIP6人炸金花';

            $this->load->view("vflower6.php", $data);

        } else if (isset($_GET['i'])) {
            $direct_url = base_url("y/vf/" . $_GET['i']);
            Header("Location:" . $direct_url);
        }
    }

    //VIP十人炸金花
    public function vtf() {
        if (isset($_SESSION['WxOpenID']) && isset($_GET['i'])) {
            $open_id = $_SESSION['WxOpenID'];

            $state = $_GET['i'];
            if (strpos($state, "_") !== FALSE) {
                $state_array = explode("_", $state);
                $room_number = $state_array[0];
                $user_code   = $state_array[1];
            } else {
                log_message('error', "function(vtf):state" . " in file" . __FILE__ . " on Line " . __LINE__);
                show_404();
                exit;
            }
            $request_ary['open_id']   = $open_id;
            $request_ary['user_code'] = $user_code;
            $this->load->model('account/account_model', '', TRUE);
            $userinfo_result = $this->account_model->getUserInfo($request_ary);
            if (!isset($userinfo_result['data']['account_id']) || $userinfo_result['data']['account_id'] == "") {
                $redirect_uri = $this->domain_path() . 'y/IDvtf';
                $direct_url   = $this->getWxOauthUrl($redirect_uri, 'snsapi_userinfo');
                Header("Location:" . $direct_url);
                exit;
            }
            if (!$this->checkIsAgree()) {
                return $this->warm();
            }
            $request_ary['account_id'] = $userinfo_result['data']['account_id'];
            $this->load->model('flower/room_model', '', TRUE);
            $result = $this->room_model->getRoomTicket($request_ary);

            $session_request['dealer_num'] = 2;
            $session_request['account_id'] = $userinfo_result['data']['account_id'];
            $session_request['type']       = '';
            $this->load->model('verification_model', '', TRUE);
            $sessionResult   = $this->verification_model->createRequestSession($session_request);
            $data['session'] = $sessionResult;

            //获取关闭房间积分榜
            $request_ary['game_type']   = 95;
            $request_ary['room_number'] = $room_number;
            $score_result               = $this->room_model->getRoomScoreboard($request_ary);
            if (isset($score_result['data']['room_status']) && isset($score_result['data']['balance_scoreboard'])) {
                $data['room_status']        = $score_result['data']['room_status'];
                $data['balance_scoreboard'] = $this->jsonEncodeOptions($score_result['data']['balance_scoreboard']);
            } else {
                $data['room_status']        = -1;
                $data['balance_scoreboard'] = "";
            }

            $this->load->model('wechat_model', '', TRUE);
            $config_ary         = $this->wechat_model->getWxConfig();
            $data['config_ary'] = $config_ary;

            $data['user']        = $userinfo_result['data'];
            $data['socket']      = Game_Const::WebSocket_Host . "/vf10";
            $data['base_url']    = $this->domain_path();
            $data['card']        = $result['data']['ticket_count'];
            $data['room_number'] = $room_number;
            $data['room_url']    = $data['base_url'] . "y/vf/" . $room_number . "_" . $userinfo_result['data']['user_code'];
            $data['file_url']    = Game_Const::ImageUrl;
            $data['image_url']   = Game_Const::ImageUrl;
            $data['http_url']    = "https://connwss.fairyland.xin/";
            $data['front_version'] = Game_CONST::Front_Version;

            $data['dealer_num']    = 2;
            $data['open_id']       = '';
            $data['isAuthPhone']   = 0;
            $data['authCardCount'] = 10;
            $data['phone']         = '';

            $this->load->model('activity/activity_model', '', TRUE);
            $broadcast          = $this->activity_model->getBroadcastInfo(1);
            $data['broadcast']  = $broadcast;
            $data['game_title'] = 'VIP十人炸金花';

            $this->load->view("vflower10.php", $data);

        } else if (isset($_GET['i'])) {
            $direct_url = base_url("y/vtf/" . $_GET['i']);
            Header("Location:" . $direct_url);
        }
    }

    //大牌炸金花
    public function bf() {
        //        $_SESSION['WxOpenID'] = 'oHwwYxBwW90_x7dgDw0mpCjciHvU';
        if (isset($_SESSION['WxOpenID']) && isset($_GET['i'])) {
            $open_id = $_SESSION['WxOpenID'];

            $state     = $_GET['i'];
            $user_code = "";
            if (strpos($state, "_") !== FALSE) {
                $state_array = explode("_", $state);
                $room_number = $state_array[0];
                $user_code   = $state_array[1];
            } else {
                log_message('error', "function(yf):state" . " in file" . __FILE__ . " on Line " . __LINE__);
                show_404();
                exit;
            }
            $request_ary['open_id']   = $open_id;
            $request_ary['user_code'] = $user_code;
            $this->load->model('account/account_model', '', TRUE);
            $userinfo_result = $this->account_model->getUserInfo($request_ary);
            if (!isset($userinfo_result['data']['account_id']) || $userinfo_result['data']['account_id'] == "") {
                //				log_message('error', "function(nb):state"." in file".__FILE__." on Line ".__LINE__);
                //				show_404();exit;

                $redirect_uri = $this->domain_path() . 'y/IDyf';
                $direct_url   = $this->getWxOauthUrl($redirect_uri, 'snsapi_userinfo');
                Header("Location:" . $direct_url);
                exit;
            }
            if (!$this->checkIsAgree()) {
                return $this->warm();
            }
            $request_ary['account_id'] = $userinfo_result['data']['account_id'];
            $this->load->model('flower/room_model', '', TRUE);
            $result = $this->room_model->getRoomTicket($request_ary);

            $session_request['dealer_num'] = 2;
            $session_request['account_id'] = $userinfo_result['data']['account_id'];
            $session_request['type']       = '';
            $this->load->model('verification_model', '', TRUE);
            $sessionResult   = $this->verification_model->createRequestSession($session_request);
            $data['session'] = $sessionResult;

            //获取关闭房间积分榜
            $request_ary['game_type']   = 111;
            $request_ary['room_number'] = $room_number;
            $score_result               = $this->room_model->getRoomScoreboard($request_ary);
            if (isset($score_result['data']['room_status']) && isset($score_result['data']['balance_scoreboard'])) {
                $data['room_status']        = $score_result['data']['room_status'];
                $data['balance_scoreboard'] = $this->jsonEncodeOptions($score_result['data']['balance_scoreboard']);
            } else {
                $data['room_status']        = -1;
                $data['balance_scoreboard'] = "";
            }

            $this->load->model('wechat_model', '', TRUE);
            $config_ary         = $this->wechat_model->getWxConfig();
            $data['config_ary'] = $config_ary;

            $data['user']        = $userinfo_result['data'];
            $data['socket']      = Game_Const::WebSocket_Host . "/f6";
            $data['base_url']    = $this->domain_path();
            $data['card']        = $result['data']['ticket_count'];
            $data['room_number'] = $room_number;
            $data['room_url']    = $data['base_url'] . "y/yf/" . $room_number . "_" . $userinfo_result['data']['user_code'];
            $data['file_url']    = Game_Const::ImageUrl;
            $data['image_url']   = Game_Const::ImageUrl;
            $data['http_url']    = "https://connwss.fairyland.xin/";
            $data['front_version'] = Game_CONST::Front_Version;

            $data['dealer_num']    = 2;
            $data['open_id']       = '';
            $data['isAuthPhone']   = 0;
            $data['authCardCount'] = 10;
            $data['phone']         = '';

            $this->load->model('activity/activity_model', '', TRUE);
            $broadcast          = $this->activity_model->getBroadcastInfo(1);
            $data['broadcast']  = $broadcast;
            $data['game_type']  = 111;
            $data['game_title'] = '大牌飘三叶';

            $this->load->view("flower.php", $data);

        } else if (isset($_GET['i'])) {
            $direct_url = base_url("y/bf/" . $_GET['i']);
            Header("Location:" . $direct_url);
        }
    }

    // 十人炸金花
    public function tf() {
        if (isset($_SESSION['WxOpenID']) && isset($_GET['i'])) {
            $open_id = $_SESSION['WxOpenID'];

            $state = $_GET['i'];
            if (strpos($state, "_") !== FALSE) {
                $state_array = explode("_", $state);
                $room_number = $state_array[0];
                $user_code   = $state_array[1];
            } else {
                log_message('error', "function(tf):state" . " in file" . __FILE__ . " on Line " . __LINE__);
                show_404();
                exit;
            }
            $request_ary['open_id']   = $open_id;
            $request_ary['user_code'] = $user_code;
            $this->load->model('account/account_model', '', TRUE);
            $userinfo_result = $this->account_model->getUserInfo($request_ary);
            if (!isset($userinfo_result['data']['account_id']) || $userinfo_result['data']['account_id'] == "") {
                $redirect_uri = $this->domain_path() . 'y/IDtf';
                $direct_url   = $this->getWxOauthUrl($redirect_uri, 'snsapi_userinfo');
                Header("Location:" . $direct_url);
                exit;
            }
            if (!$this->checkIsAgree()) {
                return $this->warm();
            }
            $request_ary['account_id'] = $userinfo_result['data']['account_id'];
            $this->load->model('flower/room_model', '', TRUE);
            $result = $this->room_model->getRoomTicket($request_ary);

            $session_request['dealer_num'] = 2;
            $session_request['account_id'] = $userinfo_result['data']['account_id'];
            $session_request['type']       = '';
            $this->load->model('verification_model', '', TRUE);
            $sessionResult   = $this->verification_model->createRequestSession($session_request);
            $data['session'] = $sessionResult;

            //获取关闭房间积分榜
            $request_ary['game_type']   = 110;
            $request_ary['room_number'] = $room_number;
            $score_result               = $this->room_model->getRoomScoreboard($request_ary);
            if (isset($score_result['data']['room_status']) && isset($score_result['data']['balance_scoreboard'])) {
                $data['room_status']        = $score_result['data']['room_status'];
                $data['balance_scoreboard'] = $this->jsonEncodeOptions($score_result['data']['balance_scoreboard']);
            } else {
                $data['room_status']        = -1;
                $data['balance_scoreboard'] = "";
            }

            $this->load->model('wechat_model', '', TRUE);
            $config_ary         = $this->wechat_model->getWxConfig();
            $data['config_ary'] = $config_ary;

            $data['user']        = $userinfo_result['data'];
            $data['socket']      = Game_Const::WebSocket_Host . "/f10";
            $data['base_url']    = $this->domain_path();
            $data['card']        = $result['data']['ticket_count'];
            $data['room_number'] = $room_number;
            $data['room_url']    = $data['base_url'] . "y/tf/" . $room_number . "_" . $userinfo_result['data']['user_code'];
            $data['file_url']    = Game_Const::ImageUrl;
            $data['image_url']   = Game_Const::ImageUrl;
            $data['http_url']    = "https://connwss.fairyland.xin/";
            $data['front_version'] = Game_CONST::Front_Version;

            $data['dealer_num']    = 2;
            $data['open_id']       = '';
            $data['isAuthPhone']   = 0;
            $data['authCardCount'] = 10;
            $data['phone']         = '';

            $this->load->model('activity/activity_model', '', TRUE);
            $broadcast         = $this->activity_model->getBroadcastInfo(110);
            $data['broadcast'] = $broadcast;

            $this->load->view("tflower.php", $data);
        } else if (isset($_GET['i'])) {
            $direct_url = base_url("y/tf/" . $_GET['i']);
            Header("Location:" . $direct_url);
        }
    }

    //癞子牛牛
    public function lb() {
        $this->checkLogin();

        if (isset($_GET['i'])) {
            $open_id = $_SESSION['WxOpenID'];

            $state = $_GET['i'];
            if (strpos($state, "_") !== FALSE) {
                $state_array = explode("_", $state);
                $room_number = $state_array[0];
                $user_code   = $state_array[1];
            } else {
                log_message('error', "function(nb):state" . " in file" . __FILE__ . " on Line " . __LINE__);
                show_404();
                exit;
            }

            $request_ary['open_id']   = $open_id;
            $request_ary['user_code'] = $user_code;
            $this->load->model('account/account_model', '', TRUE);
            $userinfo_result = $this->account_model->getUserInfo($request_ary);
            if (!isset($userinfo_result['data']['account_id']) || $userinfo_result['data']['account_id'] == "") {
                //				log_message('error', "function(nb):state"." in file".__FILE__." on Line ".__LINE__);
                //				show_404();exit;

                $redirect_uri = $this->domain_path() . 'f/lb';
                $direct_url   = $this->getWxOauthUrl($redirect_uri, 'snsapi_userinfo');
                Header("Location:" . $direct_url);
                exit;
            }
            if (!$this->checkIsAgree()) {
                return $this->warm();
            }
            $request_ary['account_id'] = $userinfo_result['data']['account_id'];
            $this->load->model('flower/room_model', '', TRUE);
            $result = $this->room_model->getRoomTicket($request_ary);

            $session_request['dealer_num'] = 2;
            $session_request['account_id'] = $userinfo_result['data']['account_id'];
            $session_request['type']       = '';
            $this->load->model('verification_model', '', TRUE);
            $sessionResult   = $this->verification_model->createRequestSession($session_request);
            $data['session'] = $sessionResult;

            //获取关闭房间积分榜
            $request_ary['game_type']   = 71;
            $request_ary['room_number'] = $room_number;
            $score_result               = $this->room_model->getRoomScoreboard($request_ary);

            if (isset($score_result['data']['room_status']) && isset($score_result['data']['balance_scoreboard'])) {
                $data['room_status']        = $score_result['data']['room_status'];
                $data['balance_scoreboard'] = $this->jsonEncodeOptions($score_result['data']['balance_scoreboard']);
            } else {
                $data['room_status']        = -1;
                $data['balance_scoreboard'] = "";
            }

            $this->load->model('wechat_model', '', TRUE);
            $config_ary         = $this->wechat_model->getWxConfig();
            $data['config_ary'] = $config_ary;

            $data['user']        = $userinfo_result['data'];
            $data['socket']      = Game_Const::WebSocket_Host . "/lb";
            $data['base_url']    = $this->domain_path();
            $data['card']        = $result['data']['ticket_count'];
            $data['room_number'] = $room_number;
            $data['room_url']    = $data['base_url'] . "f/lb?i=" . $room_number . "_" . $userinfo_result['data']['user_code'];
            $data['file_url']    = Game_Const::ImageUrl;
            $data['image_url']   = Game_Const::ImageUrl;
            $data['http_url']    = "https://connwss.fairyland.xin/";
            $data['front_version'] = Game_CONST::Front_Version;

            $data['dealer_num']    = 2;
            $data['open_id']       = '';
            $data['isAuthPhone']   = 0;
            $data['authCardCount'] = 10;
            $data['phone']         = '';

            $this->load->model('activity/activity_model', '', TRUE);
            $broadcast         = $this->activity_model->getBroadcastInfo(71);
            $data['broadcast'] = $broadcast;

            $this->load->view("lbull.php", $data);
        }
    }

    // vip 6人牛牛
    public function bv() {
        $this->checkLogin();

        if (isset($_GET['i'])) {
            $open_id = $_SESSION['WxOpenID'];

            $state = $_GET['i'];
            if (strpos($state, "_") !== FALSE) {
                $state_array = explode("_", $state);
                $room_number = $state_array[0];
                $user_code   = $state_array[1];
            } else {
                log_message('error', "function(bv):state" . " in file" . __FILE__ . " on Line " . __LINE__);
                $redirect_uri = $this->domain_path() . 'y/bv';
                Header('Location:' . $redirect_uri);
                exit;
            }

            $request_ary['open_id']   = $open_id;
            $request_ary['user_code'] = $user_code;
            $this->load->model('account/account_model', '', TRUE);
            $userinfo_result = $this->account_model->getUserInfo($request_ary);
            if (!isset($userinfo_result['data']['account_id']) || $userinfo_result['data']['account_id'] == "") {
                $redirect_uri = $this->domain_path() . 'y/bv';
                $direct_url   = $this->getWxOauthUrl($redirect_uri, 'snsapi_userinfo');
                Header("Location:" . $direct_url);
                exit;
            }
            if (!$this->checkIsAgree()) {
                return $this->warm();
            }
            $request_ary['account_id'] = $userinfo_result['data']['account_id'];
            $this->load->model('flower/room_model', '', TRUE);
            $result = $this->room_model->getRoomTicket($request_ary);

            $session_request['dealer_num'] = 2;
            $session_request['account_id'] = $userinfo_result['data']['account_id'];
            $session_request['type']       = '';
            $this->load->model('verification_model', '', TRUE);
            $sessionResult   = $this->verification_model->createRequestSession($session_request);
            $data['session'] = $sessionResult;

            //获取关闭房间积分榜
            $request_ary['game_type']   = 93;
            $request_ary['room_number'] = $room_number;
            $score_result               = $this->room_model->getRoomScoreboard($request_ary);

            if (isset($score_result['data']['room_status']) && isset($score_result['data']['balance_scoreboard'])) {
                $data['room_status']        = $score_result['data']['room_status'];
                $data['balance_scoreboard'] = $this->jsonEncodeOptions($score_result['data']['balance_scoreboard']);
            } else {
                $data['room_status']        = -1;
                $data['balance_scoreboard'] = "";
            }

            $this->load->model('wechat_model', '', TRUE);
            $config_ary         = $this->wechat_model->getWxConfig();
            $data['config_ary'] = $config_ary;

            $data['user']        = $userinfo_result['data'];
            $data['socket']      = Game_Const::WebSocket_Host . "/vbu";
            $data['base_url']    = $this->domain_path();
            $data['card']        = $result['data']['ticket_count'];
            $data['room_number'] = $room_number;
            $data['room_url']    = $data['base_url'] . "f/bv?i=" . $room_number . "_" . $userinfo_result['data']['user_code'];
            $data['file_url']    = Game_Const::ImageUrl;
            $data['image_url']   = Game_Const::ImageUrl;
            $data['http_url']    = "https://connwss.fairyland.xin/";
            $data['front_version'] = Game_CONST::Front_Version;

            $data['dealer_num']    = 2;
            $data['open_id']       = '';
            $data['isAuthPhone']   = 0;
            $data['authCardCount'] = 10;
            $data['phone']         = '';

            $this->load->model('activity/activity_model', '', TRUE);
            $broadcast         = $this->activity_model->getBroadcastInfo(93);
            $data['broadcast'] = $broadcast;

            $this->load->view("vbull6.php", $data);
        }
    }

    // vip 9人牛牛
    public function nbv() {
        $this->checkLogin();

        if (isset($_GET['i'])) {
            $open_id = $_SESSION['WxOpenID'];

            $state = $_GET['i'];
            if (strpos($state, "_") !== FALSE) {
                $state_array = explode("_", $state);
                $room_number = $state_array[0];
                $user_code   = $state_array[1];
            } else {
                log_message('error', "function(nbv):state" . " in file" . __FILE__ . " on Line " . __LINE__);
                $redirect_uri = $this->domain_path() . 'y/nbv';
                Header('Location:' . $redirect_uri);
                exit;
            }

            $request_ary['open_id']   = $open_id;
            $request_ary['user_code'] = $user_code;
            $this->load->model('account/account_model', '', TRUE);
            $userinfo_result = $this->account_model->getUserInfo($request_ary);
            if (!isset($userinfo_result['data']['account_id']) || $userinfo_result['data']['account_id'] == "") {
                $redirect_uri = $this->domain_path() . 'y/nbv';
                $direct_url   = $this->getWxOauthUrl($redirect_uri, 'snsapi_userinfo');
                Header("Location:" . $direct_url);
                exit;
            }
            if (!$this->checkIsAgree()) {
                return $this->warm();
            }
            $request_ary['account_id'] = $userinfo_result['data']['account_id'];
            $this->load->model('flower/room_model', '', TRUE);
            $result = $this->room_model->getRoomTicket($request_ary);

            $session_request['dealer_num'] = 2;
            $session_request['account_id'] = $userinfo_result['data']['account_id'];
            $session_request['type']       = '';
            $this->load->model('verification_model', '', TRUE);
            $sessionResult   = $this->verification_model->createRequestSession($session_request);
            $data['session'] = $sessionResult;

            //获取关闭房间积分榜
            $request_ary['game_type']   = 91;
            $request_ary['room_number'] = $room_number;
            $score_result               = $this->room_model->getRoomScoreboard($request_ary);

            if (isset($score_result['data']['room_status']) && isset($score_result['data']['balance_scoreboard'])) {
                $data['room_status']        = $score_result['data']['room_status'];
                $data['balance_scoreboard'] = $this->jsonEncodeOptions($score_result['data']['balance_scoreboard']);
            } else {
                $data['room_status']        = -1;
                $data['balance_scoreboard'] = "";
            }

            $this->load->model('wechat_model', '', TRUE);
            $config_ary         = $this->wechat_model->getWxConfig();
            $data['config_ary'] = $config_ary;

            $data['user']        = $userinfo_result['data'];
            $data['socket']      = Game_Const::WebSocket_Host . "/vnb";
            $data['base_url']    = $this->domain_path();
            $data['card']        = $result['data']['ticket_count'];
            $data['room_number'] = $room_number;
            $data['room_url']    = $data['base_url'] . "f/nbv?i=" . $room_number . "_" . $userinfo_result['data']['user_code'];
            $data['file_url']    = Game_Const::ImageUrl;
            $data['image_url']   = Game_Const::ImageUrl;
            $data['http_url']    = "https://connwss.fairyland.xin/";
            $data['front_version'] = Game_CONST::Front_Version;

            $data['dealer_num']    = 2;
            $data['open_id']       = '';
            $data['isAuthPhone']   = 0;
            $data['authCardCount'] = 10;
            $data['phone']         = '';

            $this->load->model('activity/activity_model', '', TRUE);
            $broadcast         = $this->activity_model->getBroadcastInfo(91);
            $data['broadcast'] = $broadcast;

            $this->load->view("vbull9.php", $data);
        }
    }

    // vip 12人牛牛
    public function tbv() {
        $this->checkLogin();

        if (isset($_GET['i'])) {
            $open_id = $_SESSION['WxOpenID'];

            $state = $_GET['i'];
            if (strpos($state, "_") !== FALSE) {
                $state_array = explode("_", $state);
                $room_number = $state_array[0];
                $user_code   = $state_array[1];
            } else {
                log_message('error', "function(nbv):state" . " in file" . __FILE__ . " on Line " . __LINE__);
                $redirect_uri = $this->domain_path() . 'y/tbv';
                Header('Location:' . $redirect_uri);
                exit;
            }

            $request_ary['open_id']   = $open_id;
            $request_ary['user_code'] = $user_code;
            $this->load->model('account/account_model', '', TRUE);
            $userinfo_result = $this->account_model->getUserInfo($request_ary);
            if (!isset($userinfo_result['data']['account_id']) || $userinfo_result['data']['account_id'] == "") {
                $redirect_uri = $this->domain_path() . 'y/tbv';
                $direct_url   = $this->getWxOauthUrl($redirect_uri, 'snsapi_userinfo');
                Header("Location:" . $direct_url);
                exit;
            }
            if (!$this->checkIsAgree()) {
                return $this->warm();
            }
            $request_ary['account_id'] = $userinfo_result['data']['account_id'];
            $this->load->model('flower/room_model', '', TRUE);
            $result = $this->room_model->getRoomTicket($request_ary);

            $session_request['dealer_num'] = 2;
            $session_request['account_id'] = $userinfo_result['data']['account_id'];
            $session_request['type']       = '';
            $this->load->model('verification_model', '', TRUE);
            $sessionResult   = $this->verification_model->createRequestSession($session_request);
            $data['session'] = $sessionResult;

            //获取关闭房间积分榜
            $request_ary['game_type']   = 94;
            $request_ary['room_number'] = $room_number;
            $score_result               = $this->room_model->getRoomScoreboard($request_ary);

            if (isset($score_result['data']['room_status']) && isset($score_result['data']['balance_scoreboard'])) {
                $data['room_status']        = $score_result['data']['room_status'];
                $data['balance_scoreboard'] = $this->jsonEncodeOptions($score_result['data']['balance_scoreboard']);
            } else {
                $data['room_status']        = -1;
                $data['balance_scoreboard'] = "";
            }

            $this->load->model('wechat_model', '', TRUE);
            $config_ary         = $this->wechat_model->getWxConfig();
            $data['config_ary'] = $config_ary;

            $data['user']        = $userinfo_result['data'];
            $data['socket']      = Game_Const::WebSocket_Host . "/vtb";
            $data['base_url']    = $this->domain_path();
            $data['card']        = $result['data']['ticket_count'];
            $data['room_number'] = $room_number;
            $data['room_url']    = $data['base_url'] . "f/nbv?i=" . $room_number . "_" . $userinfo_result['data']['user_code'];
            $data['file_url']    = Game_Const::ImageUrl;
            $data['image_url']   = Game_Const::ImageUrl;
            $data['http_url']    = "https://connwss.fairyland.xin/";
            $data['front_version'] = Game_CONST::Front_Version;

            $data['dealer_num']    = 2;
            $data['open_id']       = '';
            $data['isAuthPhone']   = 0;
            $data['authCardCount'] = 10;
            $data['phone']         = '';

            $this->load->model('activity/activity_model', '', TRUE);
            $broadcast         = $this->activity_model->getBroadcastInfo(94);
            $data['broadcast'] = $broadcast;

            $this->load->view("vbull12.php", $data);
        }
    }

    //我的主页
    public function yh() {

        if (isset($_SESSION['WxOpenID'])) {
            $open_id = $_SESSION['WxOpenID'];

            $request_ary['open_id'] = $open_id;
            $this->load->model('account/account_model', '', TRUE);
            $userinfo_result = $this->account_model->getUserInfo($request_ary);
            if (!isset($userinfo_result['data']['account_id']) || $userinfo_result['data']['account_id'] == "") {
                $direct_url = base_url("y/yh");
                Header("Location:" . $direct_url);
                return;
            }
            if (!$this->checkIsAgree()) {
                return $this->warm();
            }
            // $data['invite_code'] = $userinfo_result['data']['invite_code'];
            // $data['level'] = $userinfo_result['data']['level'];
            // $data['group_id'] = $userinfo_result['data']['group_id'];

            $request_ary['account_id'] = $userinfo_result['data']['account_id'];
            $this->load->model('flower/room_model', '', TRUE);
            $result = $this->room_model->getRoomTicket($request_ary);

            $data['isAuthPhone']   = '0';
            $data['authCardCount'] = '0';
            $data['phone']         = $userinfo_result['data']['phone'];

            $session_request['dealer_num'] = 2;
            $session_request['account_id'] = $userinfo_result['data']['account_id'];
            $session_request['type']       = '';
            $this->load->model('verification_model', '', TRUE);
            $sessionResult   = $this->verification_model->createRequestSession($session_request);
            $data['session'] = $sessionResult;

            $data['base_url'] = $this->domain_path();

            $data['socket'] = Game_Const::WebSocket_Host . "/f6";
            $data['user']   = $userinfo_result['data'];
            $data['card']   = $result['data']['ticket_count'];

            $data['account_id'] = $userinfo_result['data']['account_id'];

            $data['room_url']    = base_url("y/yh");
            $data['share_url']   = base_url("y/yh");
            $data['share_icon']  = base_url("files/images/game/home.jpg");
            $data['dealer_num']  = 2;
            $data['manage_cost'] = Game_CONST::Manage_cost;

            $data['file_url']  = Game_Const::ImageUrl;
            $data['image_url'] = Game_Const::ImageUrl;
            //$data['http_url']  = "https://connwss.fairyland.xin/";
            $data['front_version'] = Game_CONST::Front_Version;

            $this->load->model('wechat_model', '', TRUE);
            $config_ary         = $this->wechat_model->getWxConfig();
            $data['config_ary'] = $config_ary;

            $game_list         = $this->config->item('game');
            $data['game_list'] = $game_list;

            $game_tags            = array_keys($game_list);
            $data['default_game'] = isset($game_tags[0]) ? $game_tags[0] : '';

            $this->load->view("page.php", $data);

        } else {
            $direct_url = base_url("y/yh");
            Header("Location:" . $direct_url);
        }
    }

    //包厢
    public function box() {

        if (isset($_SESSION['WxOpenID'])) {
            $open_id = $_SESSION['WxOpenID'];

            $request_ary['open_id'] = $open_id;
            $this->load->model('account/account_model', '', TRUE);
            $userinfo_result = $this->account_model->getUserInfo($request_ary);
            if (!isset($userinfo_result['data']['account_id']) || $userinfo_result['data']['account_id'] == "") {
                $direct_url = base_url("y/box");
                Header("Location:" . $direct_url);
                return;
            }
            if (!$this->checkIsAgree()) {
                return $this->warm();
            }

            $request_ary['account_id'] = $userinfo_result['data']['account_id'];
            $this->load->model('flower/room_model', '', TRUE);
            $result = $this->room_model->getRoomTicket($request_ary);

            $data['isAuthPhone']   = '0';
            $data['authCardCount'] = '0';
            $data['phone']         = $userinfo_result['data']['phone'];

            $session_request['dealer_num'] = 2;
            $session_request['account_id'] = $userinfo_result['data']['account_id'];
            $session_request['type']       = '';
            $this->load->model('verification_model', '', TRUE);
            $sessionResult   = $this->verification_model->createRequestSession($session_request);
            $data['session'] = $sessionResult;

            $data['base_url'] = $this->domain_path();

            // $data['socket'] = Game_Const::WebSocket_Host . "/f6";
            $data['user']   = $userinfo_result['data'];
            $data['card']   = $result['data']['ticket_count'];

            $data['account_id'] = $userinfo_result['data']['account_id'];

            $data['room_url']    = base_url("y/box");
            $data['share_url']   = base_url("y/box");
            $data['share_icon']  = base_url("files/images/game/home.jpg");
            $data['dealer_num']  = 2;
            $data['manage_cost'] = Game_CONST::Manage_cost;

            $data['file_url']  = Game_Const::ImageUrl;
            $data['image_url'] = Game_Const::ImageUrl;
            $data['http_url']  = "https://connwss.fairyland.xin/";
            $data['front_version'] = Game_CONST::Front_Version;

            $this->load->model('wechat_model', '', TRUE);
            $config_ary         = $this->wechat_model->getWxConfig();
            $data['config_ary'] = $config_ary;

            $game_list         = $this->config->item('game');
            $data['game_list'] = $game_list;

            $game_tags            = array_keys($game_list);
            $data['default_game'] = isset($game_tags[0]) ? $game_tags[0] : '';

            $this->load->view("box.php", $data);

        } else {
            $direct_url = base_url("y/box");
            Header("Location:" . $direct_url);
        }
    }
    //好友
    public function fri() {

        if (isset($_SESSION['WxOpenID'])) {
            $open_id = $_SESSION['WxOpenID'];

            $request_ary['open_id'] = $open_id;
            $this->load->model('account/account_model', '', TRUE);
            $userinfo_result = $this->account_model->getUserInfo($request_ary);
            if (!isset($userinfo_result['data']['account_id']) || $userinfo_result['data']['account_id'] == "") {
                $direct_url = base_url("y/fri");
                Header("Location:" . $direct_url);
                return;
            }
            if (!$this->checkIsAgree()) {
                return $this->warm();
            }

            $request_ary['account_id'] = $userinfo_result['data']['account_id'];
            $this->load->model('flower/room_model', '', TRUE);
            $result = $this->room_model->getRoomTicket($request_ary);

            $data['isAuthPhone']   = '0';
            $data['authCardCount'] = '0';
            $data['phone']         = $userinfo_result['data']['phone'];

            $session_request['dealer_num'] = 2;
            $session_request['account_id'] = $userinfo_result['data']['account_id'];
            $session_request['type']       = '';
            $this->load->model('verification_model', '', TRUE);
            $sessionResult   = $this->verification_model->createRequestSession($session_request);
            $data['session'] = $sessionResult;

            $data['base_url'] = $this->domain_path();

            $data['socket'] = Game_Const::WebSocket_Host . "/front";
            $data['user']   = $userinfo_result['data'];
            $data['card']   = $result['data']['ticket_count'];

            $data['account_id'] = $userinfo_result['data']['account_id'];

            $data['room_url']    = base_url("y/fri");
            $data['share_url']   = base_url("y/fri");
            $data['share_icon']  = base_url("files/images/game/home.jpg");
            $data['dealer_num']  = 2;
            $data['manage_cost'] = Game_CONST::Manage_cost;

            $data['file_url']  = Game_Const::ImageUrl;
            $data['image_url'] = Game_Const::ImageUrl;
            //$data['http_url']  = "https://connwss.fairyland.xin/";
            $data['front_version'] = Game_CONST::Front_Version;

            $this->load->model('wechat_model', '', TRUE);
            $config_ary         = $this->wechat_model->getWxConfig();
            $data['config_ary'] = $config_ary;

            $game_list         = $this->config->item('game');
            $data['game_list'] = $game_list;

            $game_tags            = array_keys($game_list);
            $data['default_game'] = isset($game_tags[0]) ? $game_tags[0] : '';

            $this->load->model("manage/manage_model", "manage", TRUE);
            $arrData = array("account_id"=>$data['account_id']);
            $data["has_fri_req"] = $this->manage->hasFriendRequest($arrData) ? 1 : 0;

            $this->load->view("friend.php", $data);

        } else {
            $direct_url = base_url("y/fri");
            Header("Location:" . $direct_url);
        }
    }

    /*
        获取我的游戏房票

        参数：
            account_id : account_id

        返回结果：


    */
    public function getRoomTicket() {
        $open_id    = $_SESSION['WxOpenID'];
        $account_id = $this->getAccountIdByOpenid($open_id);
        if ($account_id <= 0) {
            show_404();
            return;
        }

        $request_ary['account_id'] = $account_id;
        $this->load->model('flower/room_model', '', TRUE);
        $result = $this->room_model->getRoomTicket($request_ary);

        print_r(json_encode($result));

    }

    public function getActivityInfo() {
        $params = json_decode(file_get_contents('php://input'), TRUE);

        $open_id    = $_SESSION['WxOpenID'];
        $account_id = $this->getAccountIdByOpenid($open_id);
        if ($account_id <= 0) {
            show_404();
            return;
        }

        $request_ary['account_id'] = $account_id;

        $request_ary['room_number'] = $params['room_number'];
        $request_ary['game_type']   = $params['game_type'];
        $this->load->model('activity/activity_model', '', TRUE);
        $result = $this->activity_model->getActivityInfo($request_ary);

        print_r(json_encode($result));

    }


    /*
        领取活动奖励

        参数：
            activity_id : 活动ID
            account_id : account_id

        返回结果：


    */
    public function updateActivityOpt() {
        $params = json_decode(file_get_contents('php://input'), TRUE);
        if (isset($params['activity_id'])) {
            $open_id    = $_SESSION['WxOpenID'];
            $account_id = $this->getAccountIdByOpenid($open_id);
            if ($account_id <= 0) {
                show_404();
                return;
            }

            $activity_id = $params['activity_id'];

            $request_ary['account_id']  = $account_id;
            $request_ary['activity_id'] = $activity_id;
            $this->load->model('activity/activity_model', '', TRUE);
            $result = $this->activity_model->updateActivityOpt($request_ary);

            print_r(json_encode($result));

        } else {
            show_404();
        }
    }

    public function roomCardInfo() {

        $params = json_decode(file_get_contents('php://input'), TRUE);

        $this->load->model('account/account_model', '', TRUE);
        $roomcard_result = $this->account_model->getTicketGoodsList();
        print_r(json_encode($roomcard_result));
    }

    public function scoreStat() {
        $params = json_decode(file_get_contents('php://input'), TRUE);
        if (!isset($params['type'])) {
            show_404();
            return;
        }
        $game_type  = $params['type'];
        $openid     = $_SESSION['WxOpenID'];
        $account_id = $this->getAccountIdByOpenid($openid);
        if (0 >= $account_id) {
            show_404();
            return;
        }
        $this->load->model('game/detail_model', '', TRUE);
        $result = $this->detail_model->scoreStat($game_type, $account_id);
        print_r(json_encode($result));
    }

    public function sg() {
        if (isset($_SESSION['WxOpenID']) && isset($_GET['i'])) {
            $open_id = $_SESSION['WxOpenID'];

            $state = $_GET['i'];
            if (strpos($state, "_") !== FALSE) {
                $state_array = explode("_", $state);
                $room_number = $state_array[0];
                $user_code   = $state_array[1];
            } else {
                log_message('error', "function(sg):state" . " in file" . __FILE__ . " on Line " . __LINE__);
                show_404();
                exit;
            }

            $request_ary['open_id']   = $open_id;
            $request_ary['user_code'] = $user_code;
            $this->load->model('account/account_model', '', TRUE);
            $userinfo_result = $this->account_model->getUserInfo($request_ary);
            if (!isset($userinfo_result['data']['account_id']) || $userinfo_result['data']['account_id'] == "") {
                //				log_message('error', "function(sg):state"." in file".__FILE__." on Line ".__LINE__);
                //				show_404();exit;

                $redirect_uri = $this->domain_path() . 'y/IDsg';
                $direct_url   = $this->getWxOauthUrl($redirect_uri, 'snsapi_userinfo');
                Header("Location:" . $direct_url);
                exit;
            }
            if (!$this->checkIsAgree()) {
                return $this->warm();
            }
            $request_ary['account_id'] = $userinfo_result['data']['account_id'];
            $this->load->model('flower/room_model', '', TRUE);
            $result = $this->room_model->getRoomTicket($request_ary);

            $session_request['dealer_num'] = 2;
            $session_request['account_id'] = $userinfo_result['data']['account_id'];
            $session_request['type']       = '';
            $this->load->model('verification_model', '', TRUE);
            $sessionResult   = $this->verification_model->createRequestSession($session_request);
            $data['session'] = $sessionResult;

            //获取关闭房间积分榜
            $request_ary['game_type']   = 36;
            $request_ary['room_number'] = $room_number;
            $score_result               = $this->room_model->getRoomScoreboard($request_ary);
            if (isset($score_result['data']['room_status']) && isset($score_result['data']['balance_scoreboard'])) {
                $data['room_status']        = $score_result['data']['room_status'];
                $data['balance_scoreboard'] = $this->jsonEncodeOptions($score_result['data']['balance_scoreboard']);
            } else {
                $data['room_status']        = -1;
                $data['balance_scoreboard'] = "";
            }

            $this->load->model('wechat_model', '', TRUE);
            $config_ary         = $this->wechat_model->getWxConfig();
            $data['config_ary'] = $config_ary;

            $data['user']        = $userinfo_result['data'];
            $data['socket']      = Game_Const::WebSocket_Host . "/sg6";
            $data['base_url']    = $this->domain_path();
            $data['card']        = $result['data']['ticket_count'];
            $data['room_number'] = $room_number;
            $data['room_url']    = $data['base_url'] . "y/sg/" . $room_number . "_" . $userinfo_result['data']['user_code'];
            $data['file_url']    = Game_Const::ImageUrl;
            $data['image_url']   = Game_Const::ImageUrl;
            $data['http_url']    = "https://connwss.fairyland.xin/";
            $data['front_version'] = Game_CONST::Front_Version;

            $data['dealer_num']    = 2;
            $data['open_id']       = '';
            $data['isAuthPhone']   = 0;
            $data['authCardCount'] = 10;
            $data['phone']         = '';

            $this->load->model('activity/activity_model', '', TRUE);
            $broadcast         = $this->activity_model->getBroadcastInfo(36);
            $data['broadcast'] = $broadcast;

            $this->load->view("sg.php", $data);
        } else if (isset($_GET['i'])) {
            $direct_url = base_url("y/sg/" . $_GET['i']);
            Header("Location:" . $direct_url);
        }
    }

    public function nsg() {
        if (isset($_SESSION['WxOpenID']) && isset($_GET['i'])) {
            $open_id = $_SESSION['WxOpenID'];

            $state = $_GET['i'];
            if (strpos($state, "_") !== FALSE) {
                $state_array = explode("_", $state);
                $room_number = $state_array[0];
                $user_code   = $state_array[1];
            } else {
                log_message('error', "function(nsg):state" . " in file" . __FILE__ . " on Line " . __LINE__);
                show_404();
                exit;
            }

            $request_ary['open_id']   = $open_id;
            $request_ary['user_code'] = $user_code;
            $this->load->model('account/account_model', '', TRUE);
            $userinfo_result = $this->account_model->getUserInfo($request_ary);
            if (!isset($userinfo_result['data']['account_id']) || $userinfo_result['data']['account_id'] == "") {
                //				log_message('error', "function(nsg):state"." in file".__FILE__." on Line ".__LINE__);
                //				show_404();exit;

                $redirect_uri = $this->domain_path() . 'y/IDnsg';
                $direct_url   = $this->getWxOauthUrl($redirect_uri, 'snsapi_userinfo');
                Header("Location:" . $direct_url);
                exit;
            }
            if (!$this->checkIsAgree()) {
                return $this->warm();
            }
            $request_ary['account_id'] = $userinfo_result['data']['account_id'];
            $this->load->model('flower/room_model', '', TRUE);
            $result = $this->room_model->getRoomTicket($request_ary);

            $session_request['dealer_num'] = 2;
            $session_request['account_id'] = $userinfo_result['data']['account_id'];
            $session_request['type']       = '';
            $this->load->model('verification_model', '', TRUE);
            $sessionResult   = $this->verification_model->createRequestSession($session_request);
            $data['session'] = $sessionResult;

            //获取关闭房间积分榜
            $request_ary['game_type']   = 37;
            $request_ary['room_number'] = $room_number;
            $score_result               = $this->room_model->getRoomScoreboard($request_ary);
            if (isset($score_result['data']['room_status']) && isset($score_result['data']['balance_scoreboard'])) {
                $data['room_status']        = $score_result['data']['room_status'];
                $data['balance_scoreboard'] = $this->jsonEncodeOptions($score_result['data']['balance_scoreboard']);
            } else {
                $data['room_status']        = -1;
                $data['balance_scoreboard'] = "";
            }

            $this->load->model('wechat_model', '', TRUE);
            $config_ary         = $this->wechat_model->getWxConfig();
            $data['config_ary'] = $config_ary;

            $data['user']        = $userinfo_result['data'];
            $data['socket']      = Game_Const::WebSocket_Host . "/sg9";
            $data['base_url']    = $this->domain_path();
            $data['card']        = $result['data']['ticket_count'];
            $data['room_number'] = $room_number;
            $data['room_url']    = $data['base_url'] . "y/nsg/" . $room_number . "_" . $userinfo_result['data']['user_code'];
            $data['file_url']    = Game_Const::ImageUrl;
            $data['image_url']   = Game_Const::ImageUrl;
            $data['http_url']    = "https://connwss.fairyland.xin/";
            $data['front_version'] = Game_CONST::Front_Version;

            $data['dealer_num']    = 2;
            $data['open_id']       = '';
            $data['isAuthPhone']   = 0;
            $data['authCardCount'] = 10;
            $data['phone']         = '';

            $this->load->model('activity/activity_model', '', TRUE);
            $broadcast         = $this->activity_model->getBroadcastInfo(37);
            $data['broadcast'] = $broadcast;

            $this->load->view("nsg.php", $data);
        } else if (isset($_GET['i'])) {
            $direct_url = base_url("y/nsg/" . $_GET['i']);
            Header("Location:" . $direct_url);
        }
    }

    public function tsg() {
        if (isset($_SESSION['WxOpenID']) && isset($_GET['i'])) {
            $open_id = $_SESSION['WxOpenID'];

            $state = $_GET['i'];
            if (strpos($state, "_") !== FALSE) {
                $state_array = explode("_", $state);
                $room_number = $state_array[0];
                $user_code   = $state_array[1];
            } else {
                log_message('error', "function(tsg):state" . " in file" . __FILE__ . " on Line " . __LINE__);
                show_404();
                exit;
            }

            $request_ary['open_id']   = $open_id;
            $request_ary['user_code'] = $user_code;
            $this->load->model('account/account_model', '', TRUE);
            $userinfo_result = $this->account_model->getUserInfo($request_ary);
            if (!isset($userinfo_result['data']['account_id']) || $userinfo_result['data']['account_id'] == "") {
                //				log_message('error', "function(tsg):state"." in file".__FILE__." on Line ".__LINE__);
                //				show_404();exit;

                $redirect_uri = $this->domain_path() . 'y/IDtsg';
                $direct_url   = $this->getWxOauthUrl($redirect_uri, 'snsapi_userinfo');
                Header("Location:" . $direct_url);
                exit;
            }
            if (!$this->checkIsAgree()) {
                return $this->warm();
            }
            $request_ary['account_id'] = $userinfo_result['data']['account_id'];
            $this->load->model('flower/room_model', '', TRUE);
            $result = $this->room_model->getRoomTicket($request_ary);

            $session_request['dealer_num'] = 2;
            $session_request['account_id'] = $userinfo_result['data']['account_id'];
            $session_request['type']       = '';
            $this->load->model('verification_model', '', TRUE);
            $sessionResult   = $this->verification_model->createRequestSession($session_request);
            $data['session'] = $sessionResult;

            //获取关闭房间积分榜
            $request_ary['game_type']   = 38;
            $request_ary['room_number'] = $room_number;
            $score_result               = $this->room_model->getRoomScoreboard($request_ary);
            if (isset($score_result['data']['room_status']) && isset($score_result['data']['balance_scoreboard'])) {
                $data['room_status']        = $score_result['data']['room_status'];
                $data['balance_scoreboard'] = $this->jsonEncodeOptions($score_result['data']['balance_scoreboard']);
            } else {
                $data['room_status']        = -1;
                $data['balance_scoreboard'] = "";
            }

            $this->load->model('wechat_model', '', TRUE);
            $config_ary         = $this->wechat_model->getWxConfig();
            $data['config_ary'] = $config_ary;

            $data['user']        = $userinfo_result['data'];
            $data['socket']      = Game_Const::WebSocket_Host;
            $data['base_url']    = $this->domain_path();
            $data['card']        = $result['data']['ticket_count'];
            $data['room_number'] = $room_number;
            $data['room_url']    = $data['base_url'] . "y/tsg/" . $room_number . "_" . $userinfo_result['data']['user_code'];
            $data['file_url']    = Game_Const::ImageUrl;
            $data['image_url']   = Game_Const::ImageUrl;
            $data['http_url']    = "https://connwss.fairyland.xin/";
            $data['front_version'] = Game_CONST::Front_Version;

            $data['dealer_num']    = 2;
            $data['open_id']       = '';
            $data['isAuthPhone']   = 0;
            $data['authCardCount'] = 10;
            $data['phone']         = '';

            $this->load->model('activity/activity_model', '', TRUE);
            $broadcast         = $this->activity_model->getBroadcastInfo(38);
            $data['broadcast'] = $broadcast;

            $this->load->view("tsg.php", $data);
        } else if (isset($_GET['i'])) {
            $direct_url = base_url("y/tsg/" . $_GET['i']);
            Header("Location:" . $direct_url);
        }
    }

}
