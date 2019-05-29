<?php
/**
 * Created by PhpStorm.
 * User: nszxyu
 * Date: 2017/11/19
 * Time: 21:21
 */

class MY_Controller extends CI_Controller {
    /*
		构造函数
	*/
    function __construct() {
        header('Content-Type: text/html; charset=utf-8');
        parent::__construct();
        $this->load->helper('url');
        $this->config->load('game', TRUE);
    }

    protected function domain_path() {
        return "http://" . $_SERVER["HTTP_HOST"] . "/";
        /* 若使用转发域名+落地域名则使用下面的代码
        $domain_path = config_item("domain_path");

        $domain_file = $domain_path . "domain.txt";
        if (!file_exists($domain_file)) {
            return base_url();
        }
        $now_domain = file_get_contents($domain_file);
        if (!$now_domain) {
            return base_url();
        }
        return "http://" . $now_domain . "/";
        */
    }

    /*
		判断是否微信的
	*/
    protected function is_weixin() {
        if (strpos($_SERVER['HTTP_USER_AGENT'], 'MicroMessenger') !== FALSE) {
            return TRUE;
        }
        return FALSE;
    }

    protected function jsonEncodeOptions($array) {
        $return_json = json_encode($array, JSON_HEX_TAG | JSON_HEX_APOS | JSON_HEX_QUOT | JSON_HEX_AMP | JSON_UNESCAPED_UNICODE);
        return str_replace("'", "`", $return_json);
    }

    // 温馨提示
    public function warm() {
        $data = [
            'image_url' => Game_Const::ImageUrl,
            'base_url'  => $this->domain_path()
        ];

        $this->load->view("warm.php", $data);
    }

    public function checkIsAgree() {
        if (!isset($_SESSION['isAgree']) || !$_SESSION['isAgree']) {
            return FALSE;
        }
        return TRUE;
    }


    /*
        提示在微信浏览器打开
    */
    protected function warmingWX() {
        $data['base_url'] = $this->domain_path();
        $this->load->view("warming_wx", $data);
    }

    /**
     * 检查登录状态
     */
    protected function checkLogin() {
        if (isset($_GET['code'])) {
            $this->getWxInfo();
            return;
        }
        if (!isset($_SESSION['WxOpenID']) || !isset($_SESSION['AccountID'])) {
            $request_url = trim($_SERVER['REQUEST_URI'], '/');
            $path = strstr($request_url, "?", TRUE) ?strstr($request_url, "?", TRUE): $request_url;
            $url      = $this->domain_path() . $path;
            $state    = $this->getWxState();
            $redirect = $this->getWxOauthUrl($url, 'snsapi_base', $state);
            Header("Location:" . $redirect);
            exit;
        }
    }

    /**
     * 获取微信的认证url
     * @param        $callback_url
     * @param string $scope
     * @param string $state
     * @return string
     */
    protected function getWxOauthUrl($callback_url, $scope = 'snsapi_base', $state = '') {
        $middle_url   = config_item('middle_url');
        $middle_url   = $middle_url ? $middle_url : "http://wx.zpnlcn.cn/";
        $redirect_uri = $middle_url . base64_encode($callback_url);

//        $url = 'https://open.weixin.qq.com/connect/oauth2/authorize?'
//            .'appid='.Game_CONST::WX_Appid
//            .'&redirect_uri='.$redirect_uri
//            .'&response_type=code'
//            .'&scope='.$scope
//            .'&state='.$state
//            .'#wechat_redirect1';
        $url = $this->getMiddleWebsite($callback_url, $scope, $state);
        return $url;
    }


    /**
     * 获取微信用户信息并且入库
     */
    protected function getWxInfo() {
        $code  = $_GET['code'];
        $state = $_GET['state'];

        $this->load->model('wechat_model', '', TRUE);
        $result = $this->wechat_model->getInfoOpenid($code);

        if (is_array($result) && isset($result['openid'])) {
            //获取openid成功并且用户已经存在,跳转到原地址
            $params   = $this->getFromState();
            $redirect = $this->domain_path() . strstr(trim($_SERVER['REQUEST_URI'], '/'), "?", TRUE) . ($params ? '?' . $params : '');
            Header("Location:" . $redirect);
        } else {
            //获取openid成并且用户不存在，跳转到userinfo方式获取用户信息
            log_message('error', "function(gOpenIDinvite):can not get open_id in file" . __FILE__ . " on Line " . __LINE__);
            $url      = $this->domain_path() . strstr(trim($_SERVER['REQUEST_URI'], '/'), "?", TRUE);
            $redirect = $this->getWxOauthUrl($url, 'snsapi_userinfo', $state);
            Header("Location:" . $redirect);
        }
        exit;
    }

    /**
     * 从get请求参数中获取微信state参数
     * @return string
     */
    protected function getWxState() {
        $param = $_GET;
        unset($param['code']);
        $params = [];
        foreach ($param as $k => $v) {
            $params[] = implode('|', [$k, $v]);
        }
        return implode(',', $params);
    }

    /**
     * 从微信state参数中获取参数
     * @return string
     */
    protected function getFromState() {
        if (!isset($_GET['state'])) {
            return '';
        }
        $state  = urldecode($_GET['state']);
        $params = explode(',', $state);
        $result = [];
        foreach ($params as $p) {
            $param           = explode('|', $p);
            $_GET[$param[0]] = $param[1];
            $result[]        = $param[0] . '=' . $param[1];
        }
        return implode('&', $result);
    }


    /**
     * ajax时检查登录状态
     */
    protected function ajaxCheckLogin() {
        if (isset($_SESSION['WxOpenID'])&&isset($_SESSION['AccountID'])) {
            return;
        }

        $result = array('result' => OPT_CONST::LOGIN_TIMEOUT, 'data' => [], 'result_message' => "请登录");
        $this->ajaxReturn($result);
    }

    /**
     * 返回json
     * @param $result
     */
    protected function ajaxReturn($result) {
        echo json_encode($result);
        exit;
    }


    /**
     * 返回结果数组
     * @param        $result
     * @param array  $data
     * @param string $message
     * @return array
     */
    protected function resultArray($result, $data = [], $message = "") {
        $res                   = [];
        $res['result']         = $result;
        $res['data']           = $data;
        $res['result_message'] = $message;
        $this->ajaxReturn($res);
    }

    /**
     * 返回成功数组
     * @param array  $data
     * @param string $message
     * @return array
     */
    protected function ajaxSuccess($data = [], $message = "成功") {
        $this->resultArray(OPT_CONST::SUCCESS, $data, $message);
    }

    /**
     * 返回失败数组
     * @param string $message
     * @param array  $data
     * @param int    $result
     * @return array
     */
    protected function ajaxFailed($message = "", $data = [], $result = OPT_CONST::FAILED) {
        $this->resultArray($result, $data, $message);
    }

    /**
     * 获取中间件链接
     * @param        $redirect_uri
     * @param string $scope
     * @return string
     */
    protected function getMiddleWebsite($redirect_uri, $scope = 'snsapi_base', $state = '') {
        $domain_path = config_item("domain_path");

        $domain_file = $domain_path . "callback.txt";
        if (!file_exists($domain_file)) {
            $middle_url = config_item('middle_url');
        } else {
            $now_domain = file_get_contents($domain_file);
            if (!$now_domain) {
                $middle_url = config_item('middle_url');
            } else {
                $middle_url = 'http://' . $now_domain . '/';
            }
        }

        $middle_url   = $middle_url ? $middle_url : "http://wx.zpnlcn.cn/";
        $redirect_uri = base64_encode($redirect_uri);
        $direct_url   = $middle_url . '?appid=' . Game_CONST::WX_Appid . '&redirect_uri=' . $redirect_uri . '&response_type=code&scope=' . $scope . '&state=' . $state . '#wechat_redirect';

        log_message('error', $direct_url);
        return $direct_url;
    }

}
