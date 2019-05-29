<?php

include_once dirname(__DIR__) . '/third_party/phpcode_shield.php';        //加载防注入代码
class Manage extends MY_Controller {
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
        if (strpos($_SERVER['HTTP_USER_AGENT'], 'MicroMessenger') !== FALSE) {
            return TRUE;
        }

        return FALSE;
    }

    /**
     * 函数描述：发送消息给websocket的服务端，即时推送需要
     * @param $operation
     * @param $data 数组数据
     * author 黄欣仕
     * date 2019/2/28
     */
    public function sendToFrontWebsocket($operation, $data) {
        $client = stream_socket_client(G_CONST::FRONT_WEBSOCKET_SERVER);
        $req    = array(
            "operation" => $operation,
            "data" => $data,
        );

        fwrite($client, json_encode($req));
        fwrite($client, "\r\n");

        fclose($client);
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

    /*
        提示在微信浏览器打开
    */
    public function warmingWX() {
        $data['base_url'] = $this->domain_path();
        $this->load->view("warming_wx", $data);
    }


    /*
        开启/关闭管理功能
    */
    public function setManageSwitch() {
        $this->checkLogin();

        $params = json_decode(file_get_contents('php://input'), TRUE);
        if (isset($params['is_on'])) {
            $open_id    = $_SESSION['WxOpenID'];
            $account_id = $this->getAccountIdByOpenid($open_id);
            if ($account_id <= 0) {
                show_404();
                return;
            }

            $request_array['is_on']      = $params['is_on'];
            $request_array['account_id'] = $account_id;
            $this->load->model('manage/manage_model', '', TRUE);
            $result = $this->manage_model->setManageSwitch($request_array);

            print_r(json_encode($result));
        } else {
            show_404();
        }
    }

    public function getManageSwitch() {
        $params = json_decode(file_get_contents('php://input'), TRUE);
        if (isset($_SESSION['WxOpenID'])) {
            $open_id    = $_SESSION['WxOpenID'];
            $account_id = $this->getAccountIdByOpenid($open_id);
            if ($account_id <= 0) {
                show_404();
                return;
            }

            $request_array['account_id'] = $account_id;
            $this->load->model('manage/manage_model', '', TRUE);
            $result = $this->manage_model->getManageSwitch($request_array);

            print_r(json_encode($result));
        } else {
            show_404();
        }
    }

    //成员view
    public function groupmember() {

        $is_weixin = $this->is_weixin();

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
            $request_ary['account_id'] = $userinfo_result['data']['account_id'];

            $data['base_url'] = $this->domain_path();

            $data['user'] = $userinfo_result['data'];

            $data['open_id']    = "";
            $data['account_id'] = $userinfo_result['data']['account_id'];

            $data['room_url']   = base_url("y/yh");
            $data['share_url']  = base_url("y/yh");
            $data['share_icon'] = base_url("files/images/game/home.jpg");
            $data['file_url']   = Game_Const::ImageUrl;
            $data['image_url']  = Game_Const::ImageUrl;

            $this->load->model('wechat_model', '', TRUE);
            $config_ary         = $this->wechat_model->getWxConfig();
            $data['config_ary'] = $config_ary;

            $this->load->view("groupmember.php", $data);
        } else {
            $direct_url = base_url("y/yh");
            Header("Location:" . $direct_url);
        }
    }


    //邀请函view
    public function invite() {

        $is_weixin = $this->is_weixin();

        if (isset($_SESSION['WxOpenID']) && isset($_GET['code'])) {
            $open_id = $_SESSION['WxOpenID'];

            $request_ary['open_id'] = $open_id;
            $this->load->model('account/account_model', '', TRUE);
            $userinfo_result = $this->account_model->getUserInfo($request_ary);
            if (!isset($userinfo_result['data']['account_id']) || $userinfo_result['data']['account_id'] == "") {
                $direct_url = base_url("y/invite/" . $_GET['code']);
                Header("Location:" . $direct_url);
                return;
            }
            if (!$this->checkIsAgree()) {
                return $this->warm();
            }
            $request_ary['account_id'] = $userinfo_result['data']['account_id'];

            $data['base_url'] = $this->domain_path();

            $data['user'] = $userinfo_result['data'];

            $data['open_id']    = "";
            $data['account_id'] = $userinfo_result['data']['account_id'];
            $data['phone']      = $userinfo_result['data']['phone'];

            $data['room_url']   = base_url("y/invite/" . $_GET['code']);
            $data['share_url']  = base_url("y/invite/" . $_GET['code']);
            $data['share_icon'] = base_url("files/images/guild/invitation.png");
            $data['file_url']   = Game_Const::ImageUrl;
            $data['image_url']  = Game_Const::ImageUrl;
			$data['front_version'] = Game_CONST::Front_Version;

            $request_ary['code']       = $_GET['code'];
            $request_ary['account_id'] = $userinfo_result['data']['account_id'];
            $this->load->model('manage/manage_model', '', TRUE);
            $result2 = $this->manage_model->getInviteData($request_ary);
            if (!isset($result2['result']) || $result2['result'] != "0") {
                log_message('error', "function(invite):getInviteData :" . " in file" . __FILE__ . " on Line " . __LINE__);
                show_404();
                exit;
            }

            $this->load->model('wechat_model', '', TRUE);
            $config_ary         = $this->wechat_model->getWxConfig();
            $data['config_ary'] = $config_ary;


            $data['code']              = $_GET['code'];
            $data['invite_nickname']   = $result2['data']['nickname'];
            $data['invite_headimgurl'] = $result2['data']['headimgurl'];
            $data['is_owner']          = $result2['data']['is_owner'];
            $data['invite_status']     = $result2['data']['invite_status'];

            $this->load->view("inviteDetail.php", $data);
        } else {
            $direct_url = base_url("y/invite/" . $_GET['code']);
            Header("Location:" . $direct_url);
            return;
        }
    }


    /**
     * 函数描述：发起好友请求
     */
    public function joinGroup() {
        $this->checkLogin();

        $params = json_decode(file_get_contents('php://input'), TRUE);
        if (isset($params['user_code'])) {
            $open_id    = $_SESSION['WxOpenID'];
            $account_id = $this->getAccountIdByOpenid($open_id);
            if ($account_id <= 0) {
                show_404();
                return;
            }

            $request_array['user_code']  = $params['user_code'];
            $request_array['account_id'] = $account_id;
            $this->load->model('manage/manage_model', '', TRUE);
            $result = $this->manage_model->joinGroup($request_array);

            $data = array("account_id"=> $params['user_code'] - G_CONST::USRECODE_ACCOUNTID_SUB);
            $this->sendToFrontWebsocket("JoinGroupNotify", $data);
            print_r(json_encode($result));
        } else {
            show_404();
        }
    }

    /*
        管理员处理成员
    */
    /*public function dealMember() {
        $params = file_get_contents('php://input');
        $params = json_decode($params, TRUE);

        if (isset($params['manager_id']) && isset($params['member_id']) && isset($params['type'])) {
            $request_array['manager_id'] = $params['manager_id'];
            $request_array['member_id']  = $params['member_id'];
            $request_array['type']       = $params['type'];

            $this->load->model('manage/manage_model', '', TRUE);
            $result = $this->manage_model->dealMember($request_array);

            $result = json_encode($result);
            print_r($result);
            exit;
        } else {
            show_404();
        }
    }*/

    /**
     * 函数描述：好友请求通过或恢复好友
     * author 黄欣仕
     * date 2019/3/1
     */
    public function setFriendList() {
        $this->checkLogin();

        $params = file_get_contents('php://input');
        $params = json_decode($params, TRUE);

        if (isset($params['member_id'])) {
            $account_id = $this->getAccountIdByOpenid($_SESSION["WxOpenID"]);
            if ($account_id <= 0) {
                show_404();
                return;
            }

            $request_array['manager_id'] = $account_id;
            $request_array['member_id']  = $params['member_id'];
            $request_array['type']       = G_CONST::MEMBER_RELATION_FRIND;

            $this->load->model('manage/manage_model', '', TRUE);
            $result = $this->manage_model->dealMember($request_array);

            $result = json_encode($result);
            print_r($result);
            exit;
        } else {
            show_404();
        }
    }

    /**
     * 函数描述：将好友移到黑名单里
     * author 黄欣仕
     * date 2019/3/1
     */
    public function setBlacklist() {
        $this->checkLogin();
        $params = file_get_contents('php://input');
        $params = json_decode($params, TRUE);

        if (isset($params['member_id'])) {
            $account_id = $this->getAccountIdByOpenid($_SESSION["WxOpenID"]);
            if ($account_id <= 0) {
                show_404();
                return;
            }

            $request_array['manager_id'] = $account_id;
            $request_array['member_id']  = $params['member_id'];
            $request_array['type']       = G_CONST::MEMBER_RELATION_BLACK;

            $this->load->model('manage/manage_model', '', TRUE);
            $result = $this->manage_model->dealMember($request_array);

            $result = json_encode($result);
            print_r($result);
            exit;
        } else {
            show_404();
        }
    }

    /**
     * 函数描述：解除关系
     * author 黄欣仕
     * date 2019/2/22
     */
    public function deleteMember() {
        $this->checkLogin();

        $params = file_get_contents('php://input');
        $params = json_decode($params, TRUE);

        if (isset($params['member_id'])) {
            $account_id = $this->getAccountIdByOpenid($_SESSION["WxOpenID"]);
            if ($account_id <= 0) {
                show_404();
                return;
            }

            $request_array['manager_id'] = $account_id;
            $request_array['member_id']  = $params['member_id'];

            $this->load->model('manage/manage_model', '', TRUE);
            $result = $this->manage_model->deleteMember($request_array);

            $result = json_encode($result);
            print_r($result);
            exit;
        } else {
            show_404();
        }
    }

    /**
     * 函数描述：获取好友列表
     * author 黄欣仕
     * date 2019/3/1
     */
    public function searchGroupMember() {
        $params = file_get_contents('php://input');
        $params = json_decode($params, TRUE);

        if (isset($params['page']) && isset($params['nickname']) && isset($_SESSION["WxOpenID"])) {
            $account_id = $this->getAccountIdByOpenid($_SESSION["WxOpenID"]);
            if ($account_id <= 0) {
                show_404();
                return;
            }

            $request_array['nickname']   = $params['nickname'];
            $request_array['page']       = $params['page'];
            $request_array['manager_id'] = $account_id;
            $request_array['status']     = G_CONST::MEMBER_RELATION_FRIND;

            $this->load->model('manage/manage_model', '', TRUE);
            $result = $this->manage_model->searchMember($request_array);

            $result = json_encode($result);
            print_r($result);
            exit;
        } else {
            show_404();
        }

    }

    /**
     * 函数描述：获取黑名单列表
     * author 黄欣仕
     * date 2019/3/1
     */
    public function getBlacklist() {
        $params = file_get_contents('php://input');
        $params = json_decode($params, TRUE);

        if (isset($params['page']) && isset($_SESSION["WxOpenID"])) {
            $account_id = $this->getAccountIdByOpenid($_SESSION["WxOpenID"]);
            if ($account_id <= 0) {
                show_404();
                return;
            }

            $request_array['nickname']   = "";
            $request_array['page']       = $params['page'];
            $request_array['manager_id'] = $account_id;
            $request_array['status']     = G_CONST::MEMBER_RELATION_BLACK;

            $this->load->model('manage/manage_model', '', TRUE);
            $result = $this->manage_model->searchMember($request_array);

            $result = json_encode($result);
            print_r($result);
            exit;
        } else {
            show_404();
        }

    }

    /**
     * 函数描述：获取请求列表
     * author 黄欣仕
     * date 2019/3/1
     */
    public function getApplylist() {
        $params = file_get_contents('php://input');
        $params = json_decode($params, TRUE);

        if (isset($params['page']) && isset($_SESSION["WxOpenID"])) {
            $account_id = $this->getAccountIdByOpenid($_SESSION["WxOpenID"]);
            if ($account_id <= 0) {
                show_404();
                return;
            }

            $request_array['nickname']   = "";
            $request_array['page']       = $params['page'];
            $request_array['manager_id'] = $account_id;
            $request_array['status']     = G_CONST::MEMBER_RELATION_NONE;

            $this->load->model('manage/manage_model', '', TRUE);
            $result = $this->manage_model->searchMember($request_array);

            $result = json_encode($result);
            print_r($result);
            exit;
        } else {
            show_404();
        }

    }

    /**
     * 函数描述：获取关注其创建的房间的用户列表
     * author 黄欣仕
     * date 2019/3/1
     */
    public function getManagerUser() {

        if (isset($_SESSION["WxOpenID"]) and isset($_GET["page"])) {
            $account_id = $this->getAccountIdByOpenid($_SESSION["WxOpenID"]);
            if ($account_id <= 0) {
                show_404();
                return;
            }

            $request_array['account_id'] = $account_id;
            $request_array['page']       = $_GET["page"];

            $this->load->model('manage/manage_model', '', TRUE);
            $result = $this->manage_model->getManagerUser($request_array);

            $result = json_encode($result);
            print_r($result);
            exit;
        } else {
            show_404();
        }

    }

    /**
     * 函数描述：是否关注对方创建的房间
     * author 黄欣仕
     * date 2019/3/1
     */
    public function setManagerUser() {
        $this->checkLogin();

        $params = file_get_contents('php://input');
        $params = json_decode($params, TRUE);
        if (isset($params["attention"]) and isset($params["manager_id"])) {
            $account_id = $this->getAccountIdByOpenid($_SESSION["WxOpenID"]);
            if ($account_id <= 0) {
                show_404();
                return;
            }

            $request_array['account_id'] = $account_id;
            $request_array['attention']  = $params["attention"];
            $request_array['manager_id'] = $params["manager_id"];

            $this->load->model('manage/manage_model', '', TRUE);
            $result = $this->manage_model->setManagerUser($request_array);

            $result = json_encode($result);
            print_r($result);
            exit;
        } else {
            show_404();
        }
    }

    /**
     * 函数描述：获取包厢/房间列表，最多返回10条，包厢的优先
     * author 黄欣仕
     * date 2019/3/1
     */
    public function getRoomList() {

        if (isset($_SESSION["WxOpenID"]) && isset($_GET["game_category"])) {
            $account_id = $this->getAccountIdByOpenid($_SESSION["WxOpenID"]);
            if ($account_id <= 0) {
                show_404();
                return;
            }

            $request_array['account_id'] = $account_id;
            $request_array['game_category']  = $_GET["game_category"];
            $this->load->model('manage/manage_model', '', TRUE);
            $result = $this->manage_model->getRoomList($request_array);

            $result = json_encode($result);
            print_r($result);
            exit;
        } else {
            show_404();
        }
    }

    /**
     * 函数描述：获取房间的创建信息。获取包厢信息的接口由包厢模块提供。
     * author 黄欣仕
     * date 2019/3/1
     */
    public function getRoomInfo() {
        if (isset($_SESSION["WxOpenID"]) && isset($_GET["room_id"]) && isset($_GET["game_type"])) {
            $account_id = $this->getAccountIdByOpenid($_SESSION["WxOpenID"]);
            if ($account_id <= 0) {
                show_404();
                return;
            }

            $request_array['account_id'] = $account_id;
            $request_array['room_id']    = $_GET["room_id"];
            $request_array['game_type']  = $_GET["game_type"];
            $this->load->model('manage/manage_model', '', TRUE);
            $result = $this->manage_model->GetRoomInfo($request_array);

            $result = json_encode($result);
            print_r($result);
            exit;
        } else {
            show_404();
        }
    }

    /**
     * 函数描述：修改备注
     * author 黄欣仕
     * date 2019/2/28
     */
    public function SetAliases() {
        $params = file_get_contents('php://input');
        $params = json_decode($params, TRUE);
        $this->checkLogin();

        if (isset($params['member_id'])) {
            $account_id = $this->getAccountIdByOpenid($_SESSION["WxOpenID"]);
            if ($account_id <= 0) {
                show_404();
                return;
            }

            $request_array['account_id'] = $account_id;
            $request_array['member_id']  = $params['member_id'];
            $request_array['aliases']    = htmlspecialchars($params['aliases']);

            $this->load->model('manage/manage_model', '', TRUE);
            $result = $this->manage_model->SetAliases($request_array);

            $result = json_encode($result);
            print_r($result);
            exit;
        } else {
            show_404();
        }
    }
}
