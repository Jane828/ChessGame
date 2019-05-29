<?php

include_once dirname(__DIR__) . '/third_party/phpcode_shield.php';        //加载防注入代码
class Y extends MY_Controller {
    /*
        构造函数
    */
    function __construct() {
        parent::__construct();

    }


    public function ym($scope = 'snsapi_base') {
        $redirect_uri = $this->domain_path() . 'y/IDym';
//        $redirect_uri = base64_encode($redirect_uri);
//		$direct_url = 'https://open.weixin.qq.com/connect/oauth2/authorize?appid='.Game_CONST::WX_Appid.'&redirect_uri='.$redirect_uri.'&response_type=code&scope='.$scope.'#wechat_redirect';
//        $direct_url = 'http://wx.zpnlcn.cn/?appid='.Game_CONST::WX_Appid.'&redirect_uri='.$redirect_uri.'&response_type=code&scope='.$scope.'#wechat_redirect';
        $direct_url = $this->getWxOauthUrl($redirect_uri, $scope);
        Header("Location:" . $direct_url);
        return;
    }

    public function yh($scope = 'snsapi_base') {
//		$redirect_uri = Game_CONST::My_Url. 'y/IDyh' ;
        $redirect_uri = $this->domain_path() . 'y/IDyh';
//		$direct_url = 'https://open.weixin.qq.com/connect/oauth2/authorize?appid='.Game_CONST::WX_Appid.'&redirect_uri='.$redirect_uri.'&response_type=code&scope='.$scope.'#wechat_redirect';
        $direct_url = $this->getWxOauthUrl($redirect_uri, $scope);
        Header("Location:" . $direct_url);
    }

    public function IDyh() {
        if (isset($_GET['code'])) {
            $code = $_GET['code'];

            $this->load->model('wechat_model', '', TRUE);
            $result = $this->wechat_model->getInfoOpenid($code);

            if (is_array($result) && isset($result['openid'])) {
//				$direct_url = Game_CONST::My_Url."f/yh";
                $direct_url = $this->domain_path() . "f/yh";
                Header("Location:" . $direct_url);
            } else {
                log_message('error', "function(IDyh):can not get open_id in file" . __FILE__ . " on Line " . __LINE__);
//                $direct_url = Game_CONST::My_Url."y/yh/snsapi_userinfo";
                $direct_url = $this->domain_path() . "y/yh/snsapi_userinfo";
                Header("Location:" . $direct_url);
            }
        } else {
            log_message('error', "function(IDyh):lack of code in file" . __FILE__ . " on Line " . __LINE__);
            echo '{"result":"-1"}';
        }
    }

    public function IDym() {
        if (isset($_GET['code'])) {
            $code = $_GET['code'];

            $this->load->model('wechat_model', '', TRUE);
            $result = $this->wechat_model->getInfoOpenid($code);

            if (is_array($result) && isset($result['openid'])) {
                log_message('error', "function(IDym):open_id : " . $result['openid'] . "  in file" . __FILE__ . " on Line " . __LINE__);
//				$direct_url = Game_CONST::My_Url."f/ym";
                $direct_url = $this->domain_path() . "f/ym";
                Header("Location:" . $direct_url);
                return;
            } else {
                log_message('error', "function(IDym):can not get open_id in file" . __FILE__ . " on Line " . __LINE__);
//				$direct_url = Game_CONST::My_Url."y/ym/snsapi_userinfo";
                $direct_url = $this->domain_path() . "y/ym/snsapi_userinfo";
                Header("Location:" . $direct_url);
            }
        } else {
            log_message('error', "function(IDym):lack of code in file" . __FILE__ . " on Line " . __LINE__);
            echo '{"result":"-1"}';
        }
    }

    public function club($scope = 'snsapi_base') {
        $redirect_uri = $this->domain_path() . 'y/IDclub';
        $direct_url   = $this->getWxOauthUrl($redirect_uri, $scope);
        Header("Location:" . $direct_url);
        return;
    }

    public function IDclub() {
        if (isset($_GET['code'])) {
            $code = $_GET['code'];

            $this->load->model('wechat_model', '', TRUE);
            $result = $this->wechat_model->getInfoOpenid($code);

            if (is_array($result) && isset($result['openid'])) {
                log_message('error', "function(IDclub):open_id : " . $result['openid'] . "  in file" . __FILE__ . " on Line " . __LINE__);
                $direct_url = $this->domain_path() . "club/index";
                Header("Location:" . $direct_url);
                return;
            } else {
                log_message('error', "function(IDclub):can not get open_id in file" . __FILE__ . " on Line " . __LINE__);
                $direct_url = $this->domain_path() . "club/index/snsapi_userinfo";
                Header("Location:" . $direct_url);
            }
        } else {
            log_message('error', "function(IDclub):lack of code in file" . __FILE__ . " on Line " . __LINE__);
            echo '{"result":"-1"}';
        }
    }

    /*
        斗牛房间跳转 9人
    */
    public function nb($state = '', $scope = 'snsapi_base') {
        $state = filter_var($state);
        if ($state) {
//			$redirect_uri = Game_CONST::My_Url . 'y/IDnb' ;
            $redirect_uri = $this->domain_path() . 'y/IDnb';
//			$direct_url = 'https://open.weixin.qq.com/connect/oauth2/authorize?appid='.Game_CONST::WX_Appid.'&redirect_uri='.$redirect_uri.'&response_type=code&scope='.$scope.'&state='.$state.'#wechat_redirect';
            $direct_url = $this->getWxOauthUrl($redirect_uri, $scope, $state);
            Header("Location:" . $direct_url);
        } else {
            show_404();
            exit;
        }
    }

    public function IDnb() {
        if (isset($_GET['code'])) {
            $code  = $_GET['code'];
            $state = $_GET['state'];

            $this->load->model('wechat_model', '', TRUE);
            $result = $this->wechat_model->getInfoOpenid($code);

            if (is_array($result) && isset($result['openid'])) {
//				$direct_url = Game_CONST::My_Url."f/nb?i=".$state;
                $direct_url = $this->domain_path() . "f/nb?i=" . $state;
                Header("Location:" . $direct_url);
            } else {
                log_message('error', "function(IDnb):can not get open_id in file" . __FILE__ . " on Line " . __LINE__);
//				$direct_url = Game_CONST::My_Url."y/nb/".$state.'/snsapi_userinfo';
                $direct_url = $this->domain_path() . "y/nb/" . $state . '/snsapi_userinfo';
                Header("Location:" . $direct_url);
            }
        } else {
            log_message('error', "function(gOpenIDb9):lack of code in file" . __FILE__ . " on Line " . __LINE__);
            echo '{"result":"-1"}';
        }
    }


    /*
        斗牛房间跳转 9人
    */
    public function nb8($state = '', $scope = 'snsapi_base') {
        $state = filter_var($state);
        if ($state) {
//            $redirect_uri = Game_CONST::My_Url . 'y/IDnb8' ;
            $redirect_uri = $this->domain_path() . 'y/IDnb8';
//            $direct_url = 'https://open.weixin.qq.com/connect/oauth2/authorize?appid='.Game_CONST::WX_Appid.'&redirect_uri='.$redirect_uri.'&response_type=code&scope='.$scope.'&state='.$state.'#wechat_redirect';
            $direct_url = $this->getWxOauthUrl($redirect_uri, $scope, $state);
            Header("Location:" . $direct_url);
        } else {
            show_404();
            exit;
        }
    }

    public function IDnb8() {
        if (isset($_GET['code'])) {
            $code  = $_GET['code'];
            $state = $_GET['state'];

            $this->load->model('wechat_model', '', TRUE);
            $result = $this->wechat_model->getInfoOpenid($code);

            if (is_array($result) && isset($result['openid'])) {
//                $direct_url = Game_CONST::My_Url."f/nb8?i=".$state;
                $direct_url = $this->domain_path() . "f/nb8?i=" . $state;
                Header("Location:" . $direct_url);
            } else {
                log_message('error', "function(IDnb):can not get open_id in file" . __FILE__ . " on Line " . __LINE__);
//                $direct_url = Game_CONST::My_Url."y/nb8/".$state.'/snsapi_userinfo';
                $direct_url = $this->domain_path() . "y/nb8/" . $state . '/snsapi_userinfo';
                Header("Location:" . $direct_url);
            }
        } else {
            log_message('error', "function(gOpenIDb9):lack of code in file" . __FILE__ . " on Line " . __LINE__);
            echo '{"result":"-1"}';
        }
    }

    /*
        斗牛房间跳转 6人
    */
    public function b($state = '', $scope = 'snsapi_base') {
        $state = filter_var($state);
        if ($state) {
//			$redirect_uri = Game_CONST::My_Url . 'y/IDb' ;
            $redirect_uri = $this->domain_path() . 'y/IDb';
//			$direct_url = 'https://open.weixin.qq.com/connect/oauth2/authorize?appid='.Game_CONST::WX_Appid.'&redirect_uri='.$redirect_uri.'&response_type=code&scope='.$scope.'&state='.$state.'#wechat_redirect';
            $direct_url = $this->getWxOauthUrl($redirect_uri, $scope, $state);
            Header("Location:" . $direct_url);
        } else {
            show_404();
            exit;
        }
    }

    public function IDb() {
        if (isset($_GET['code'])) {
            $code  = $_GET['code'];
            $state = $_GET['state'];

            $this->load->model('wechat_model', '', TRUE);
            $result = $this->wechat_model->getInfoOpenid($code);

            if (is_array($result) && isset($result['openid'])) {
//				$direct_url = Game_CONST::My_Url."f/b?i=".$state;
                $direct_url = $this->domain_path() . "f/b?i=" . $state;
                Header("Location:" . $direct_url);
            } else {
                log_message('error', "function(IDb):can not get open_id in file" . __FILE__ . " on Line " . __LINE__);
//				$direct_url = Game_CONST::My_Url."y/b/".$state.'/snsapi_userinfo';
                $direct_url = $this->domain_path() . "y/b/" . $state . '/snsapi_userinfo';
                Header("Location:" . $direct_url);
            }
        } else {
            log_message('error', "function(IDb):lack of code in file" . __FILE__ . " on Line " . __LINE__);
            echo '{"result":"-1"}';
        }
    }


    // vip斗牛房间跳转 6人
    public function bv($state = '', $scope = 'snsapi_base') {
        $state = filter_var($state);
        if ($state) {
            $redirect_uri = $this->domain_path() . 'y/IDbv';
            $direct_url   = $this->getWxOauthUrl($redirect_uri, $scope, $state);
            Header("Location:" . $direct_url);
        } else {
            show_404();
            exit;
        }
    }

    public function IDbv() {
        if (isset($_GET['code'])) {
            $code  = $_GET['code'];
            $state = $_GET['state'];
            $this->load->model('wechat_model', '', TRUE);
            $result = $this->wechat_model->getInfoOpenid($code);
            if (is_array($result) && isset($result['openid'])) {
                $direct_url = $this->domain_path() . "f/bv?i=" . $state;
                Header("Location:" . $direct_url);
            } else {
                log_message('error', "function(IDbv):can not get open_id in file" . __FILE__ . " on Line " . __LINE__);
                $direct_url = $this->domain_path() . "y/bv/" . $state . '/snsapi_userinfo';
                Header("Location:" . $direct_url);
            }
        } else {
            log_message('error', "function(IDbv):lack of code in file" . __FILE__ . " on Line " . __LINE__);
            echo '{"result":"-1"}';
        }
    }

    // vip斗牛房间跳转 9人
    public function nbv($state = '', $scope = 'snsapi_base') {
        $state = filter_var($state);
        if ($state) {
            $redirect_uri = $this->domain_path() . 'y/IDnbv';
            $direct_url   = $this->getWxOauthUrl($redirect_uri, $scope, $state);
            Header("Location:" . $direct_url);
        } else {
            show_404();
            exit;
        }
    }

    public function IDnbv() {
        if (isset($_GET['code'])) {
            $code  = $_GET['code'];
            $state = $_GET['state'];
            $this->load->model('wechat_model', '', TRUE);
            $result = $this->wechat_model->getInfoOpenid($code);
            if (is_array($result) && isset($result['openid'])) {
                $direct_url = $this->domain_path() . "f/nbv?i=" . $state;
                Header("Location:" . $direct_url);
            } else {
                log_message('error', "function(IDnbv):can not get open_id in file" . __FILE__ . " on Line " . __LINE__);
                $direct_url = $this->domain_path() . "y/nbv/" . $state . '/snsapi_userinfo';
                Header("Location:" . $direct_url);
            }
        } else {
            log_message('error', "function(IDnbv):lack of code in file" . __FILE__ . " on Line " . __LINE__);
            echo '{"result":"-1"}';
        }
    }

    // vip斗牛房间跳转 12人
    public function tbv($state = '', $scope = 'snsapi_base') {
        $state = filter_var($state);
        if ($state) {
            $redirect_uri = $this->domain_path() . 'y/IDtbv';
            $direct_url   = $this->getWxOauthUrl($redirect_uri, $scope, $state);
            Header("Location:" . $direct_url);
        } else {
            show_404();
            exit;
        }
    }

    public function IDtbv() {
        if (isset($_GET['code'])) {
            $code  = $_GET['code'];
            $state = $_GET['state'];
            $this->load->model('wechat_model', '', TRUE);
            $result = $this->wechat_model->getInfoOpenid($code);
            if (is_array($result) && isset($result['openid'])) {
                $direct_url = $this->domain_path() . "f/tbv?i=" . $state;
                Header("Location:" . $direct_url);
            } else {
                log_message('error', "function(IDtbv):can not get open_id in file" . __FILE__ . " on Line " . __LINE__);
                $direct_url = $this->domain_path() . "y/tbv/" . $state . '/snsapi_userinfo';
                Header("Location:" . $direct_url);
            }
        } else {
            log_message('error', "function(IDtbv):lack of code in file" . __FILE__ . " on Line " . __LINE__);
            echo '{"result":"-1"}';
        }
    }


    /*
        麻将房间跳转
    */
    public function ma($state = '', $scope = 'snsapi_base') {
        $state = filter_var($state);
        if ($state) {
//			$redirect_uri = Game_CONST::My_Url . 'y/IDma' ;
            $redirect_uri = $this->domain_path() . 'y/IDma';
//			$direct_url = 'https://open.weixin.qq.com/connect/oauth2/authorize?appid='.Game_CONST::WX_Appid.'&redirect_uri='.$redirect_uri.'&response_type=code&scope='.$scope.'&state='.$state.'#wechat_redirect';
            $direct_url = $this->getWxOauthUrl($redirect_uri, $scope, $state);
            Header("Location:" . $direct_url);
        } else {
            show_404();
            exit;
        }
    }

    public function IDma() {
        if (isset($_GET['code'])) {
            $code  = $_GET['code'];
            $state = $_GET['state'];

            $this->load->model('wechat_model', '', TRUE);
            $result = $this->wechat_model->getInfoOpenid($code);

            if (is_array($result) && isset($result['openid'])) {
//				$direct_url = Game_CONST::My_Url."f/ma?i=".$state;
                $direct_url = $this->domain_path() . "f/ma?i=" . $state;
                Header("Location:" . $direct_url);
            } else {
                log_message('error', "function(IDb):can not get open_id in file" . __FILE__ . " on Line " . __LINE__);
//				$direct_url = Game_CONST::My_Url."y/ma/".$state.'/snsapi_userinfo';
                $direct_url = $this->domain_path() . "y/ma/" . $state . '/snsapi_userinfo';
                Header("Location:" . $direct_url);
            }
        } else {
            log_message('error', "function(IDb):lack of code in file" . __FILE__ . " on Line " . __LINE__);
            echo '{"result":"-1"}';
        }
    }


    /*
        斗地主房间跳转
    */
    public function l($state = '', $scope = 'snsapi_base') {
        $state = filter_var($state);
        if ($state) {
//			$redirect_uri = Game_CONST::My_Url . 'y/IDl' ;
            $redirect_uri = $this->domain_path() . 'y/IDl';
//			$direct_url = 'https://open.weixin.qq.com/connect/oauth2/authorize?appid='.Game_CONST::WX_Appid.'&redirect_uri='.$redirect_uri.'&response_type=code&scope='.$scope.'&state='.$state.'#wechat_redirect';
            $direct_url = $this->getWxOauthUrl($redirect_uri, $scope, $state);
            Header("Location:" . $direct_url);
        } else {
            show_404();
            exit;
        }
    }

    public function IDl() {
        if (isset($_GET['code'])) {
            $code  = $_GET['code'];
            $state = $_GET['state'];

            $this->load->model('wechat_model', '', TRUE);
            $result = $this->wechat_model->getInfoOpenid($code);

            if (is_array($result) && isset($result['openid'])) {
//				$direct_url = Game_CONST::My_Url."f/l?i=".$state;
                $direct_url = $this->domain_path() . "f/l?i=" . $state;
                Header("Location:" . $direct_url);
            } else {
                log_message('error', "function(IDb):can not get open_id in file" . __FILE__ . " on Line " . __LINE__);
//				$direct_url = Game_CONST::My_Url."y/l/".$state.'/snsapi_userinfo';
                $direct_url = $this->domain_path() . "y/l/" . $state . '/snsapi_userinfo';
                Header("Location:" . $direct_url);
            }
        } else {
            log_message('error', "function(IDb):lack of code in file" . __FILE__ . " on Line " . __LINE__);
            echo '{"result":"-1"}';
        }
    }


    /*
        炸金花房间跳转
    */
    public function yf($state = '', $scope = 'snsapi_base') {
        $state = filter_var($state);
        if ($state) {
//			$redirect_uri = Game_CONST::My_Url . 'y/IDyf' ;
            $redirect_uri = $this->domain_path() . 'y/IDyf';
//			$direct_url = 'https://open.weixin.qq.com/connect/oauth2/authorize?appid='.Game_CONST::WX_Appid.'&redirect_uri='.$redirect_uri.'&response_type=code&scope='.$scope.'&state='.$state.'#wechat_redirect';
            $direct_url = $this->getWxOauthUrl($redirect_uri, $scope, $state);
            Header("Location:" . $direct_url);
        } else {
            show_404();
            exit;
        }
    }

    public function IDyf() {
        if (isset($_GET['code'])) {
            $code  = $_GET['code'];
            $state = $_GET['state'];

            $this->load->model('wechat_model', '', TRUE);
            $result = $this->wechat_model->getInfoOpenid($code);

            if (is_array($result) && isset($result['openid'])) {
//				$direct_url = Game_CONST::My_Url."f/yf?i=".$state;
                $direct_url = $this->domain_path() . "f/yf?i=" . $state;
                Header("Location:" . $direct_url);
            } else {
                log_message('error', "function(IDyf):can not get open_id in file" . __FILE__ . " on Line " . __LINE__);
//				$direct_url = Game_CONST::My_Url."y/yf/".$state.'/snsapi_userinfo';
                $direct_url = $this->domain_path() . "y/yf/" . $state . '/snsapi_userinfo';
                Header("Location:" . $direct_url);
            }
        } else {
            log_message('error', "function(IDyf):lack of code in file" . __FILE__ . " on Line " . __LINE__);
            echo '{"result":"-1"}';
        }
    }

    public function vf($state = '', $scope = 'snsapi_base') {
        $state = filter_var($state);
        if ($state) {
            $redirect_uri = $this->domain_path() . 'y/IDvf';
            $direct_url   = $this->getWxOauthUrl($redirect_uri, $scope, $state);
            Header("Location:" . $direct_url);
        } else {
            show_404();
            exit;
        }
    }

    public function IDvf() {
        if (isset($_GET['code'])) {
            $code  = $_GET['code'];
            $state = $_GET['state'];

            $this->load->model('wechat_model', '', TRUE);
            $result = $this->wechat_model->getInfoOpenid($code);

            if (is_array($result) && isset($result['openid'])) {
                $direct_url = $this->domain_path() . "f/vf?i=" . $state;
                Header("Location:" . $direct_url);
            } else {
                log_message('error', "function(IDvf):can not get open_id in file" . __FILE__ . " on Line " . __LINE__);
                $direct_url = $this->domain_path() . "y/vf/" . $state . '/snsapi_userinfo';
                Header("Location:" . $direct_url);
            }
        } else {
            log_message('error', "function(IDvf):lack of code in file" . __FILE__ . " on Line " . __LINE__);
            echo '{"result":"-1"}';
        }
    }

    public function vtf($state = '', $scope = 'snsapi_base') {
        $state = filter_var($state);
        if ($state) {
            $redirect_uri = $this->domain_path() . 'y/IDvtf';
            $direct_url   = $this->getWxOauthUrl($redirect_uri, $scope, $state);
            Header("Location:" . $direct_url);
        } else {
            show_404();
            exit;
        }
    }

    public function IDvtf() {
        if (isset($_GET['code'])) {
            $code  = $_GET['code'];
            $state = $_GET['state'];
            $this->load->model('wechat_model', '', TRUE);
            $result = $this->wechat_model->getInfoOpenid($code);
            if (is_array($result) && isset($result['openid'])) {
                $direct_url = $this->domain_path() . "f/vtf?i=" . $state;
                Header("Location:" . $direct_url);
            } else {
                log_message('error', "function(IDvtf):can not get open_id in file" . __FILE__ . " on Line " . __LINE__);
                $direct_url = $this->domain_path() . "y/vtf/" . $state . '/snsapi_userinfo';
                Header("Location:" . $direct_url);
            }
        } else {
            log_message('error', "function(IDvtf):lack of code in file" . __FILE__ . " on Line " . __LINE__);
            echo '{"result":"-1"}';
        }
    }

    public function bf($state = '', $scope = 'snsapi_base') {
        $state = filter_var($state);
        if ($state) {
            $redirect_uri = $this->domain_path() . 'y/IDbf';
            $direct_url   = $this->getWxOauthUrl($redirect_uri, $scope, $state);
            Header("Location:" . $direct_url);
        } else {
            show_404();
            exit;
        }
    }

    public function IDbf() {
        if (isset($_GET['code'])) {
            $code  = $_GET['code'];
            $state = $_GET['state'];

            $this->load->model('wechat_model', '', TRUE);
            $result = $this->wechat_model->getInfoOpenid($code);

            if (is_array($result) && isset($result['openid'])) {
                $direct_url = $this->domain_path() . "f/bf?i=" . $state;
                Header("Location:" . $direct_url);
            } else {
                log_message('error', "function(IDbf):can not get open_id in file" . __FILE__ . " on Line " . __LINE__);
                $direct_url = $this->domain_path() . "y/bf/" . $state . '/snsapi_userinfo';
                Header("Location:" . $direct_url);
            }
        } else {
            log_message('error', "function(IDbf):lack of code in file" . __FILE__ . " on Line " . __LINE__);
            echo '{"result":"-1"}';
        }
    }

    public function tf($state = '', $scope = 'snsapi_base') {
        $state = filter_var($state);
        if ($state) {
            $redirect_uri = $this->domain_path() . 'y/IDtf';
            $direct_url   = $this->getWxOauthUrl($redirect_uri, $scope, $state);
            Header("Location:" . $direct_url);
        } else {
            show_404();
            exit;
        }
    }

    public function IDtf() {
        if (isset($_GET['code'])) {
            $code  = $_GET['code'];
            $state = $_GET['state'];

            $this->load->model('wechat_model', '', TRUE);
            $result = $this->wechat_model->getInfoOpenid($code);

            if (is_array($result) && isset($result['openid'])) {
                $direct_url = $this->domain_path() . "f/tf?i=" . $state;
                Header("Location:" . $direct_url);
            } else {
                log_message('error', "function(IDtf):can not get open_id in file" . __FILE__ . " on Line " . __LINE__);
                $direct_url = $this->domain_path() . "y/tf/" . $state . '/snsapi_userinfo';
                Header("Location:" . $direct_url);
            }
        } else {
            log_message('error', "function(IDtf):lack of code in file" . __FILE__ . " on Line " . __LINE__);
            echo '{"result":"-1"}';
        }
    }


    public function re($state, $scope = 'snsapi_base') {
        $state = filter_var($state);
        if ($state) {
//			$redirect_uri = Game_CONST::My_Url . 'y/gOpenIDre' ;
            $redirect_uri = $this->domain_path() . 'y/gOpenIDre';
//			$direct_url = 'https://open.weixin.qq.com/connect/oauth2/authorize?appid='.Game_CONST::WX_Appid.'&redirect_uri='.$redirect_uri.'&response_type=code&scope='.$scope.'&state='.$state.'#wechat_redirect';
            $direct_url = $this->getWxOauthUrl($redirect_uri, $scope, $state);
            Header("Location:" . $direct_url);
        } else {
            show_404();
            exit;
        }
    }

    public function gOpenIDre() {
        if (isset($_GET['code'])) {
            $code  = $_GET['code'];
            $state = $_GET['state'];

            $this->load->model('wechat_model', '', TRUE);
            $result = $this->wechat_model->getInfoOpenid($code);

            if (is_array($result) && isset($result['openid'])) {
//				$direct_url = Game_CONST::My_Url."ay/rpD?red_code=".$state;
                $direct_url = $this->domain_path() . "ay/rpD?red_code=" . $state;
                Header("Location:" . $direct_url);
            } else {
                log_message('error', "function(gOpenIDre):can not get open_id in file" . __FILE__ . " on Line " . __LINE__);
//				$direct_url = Game_CONST::My_Url."y/re/".$state."/snsapi_userinfo";
                $direct_url = $this->domain_path() . "y/re/" . $state . "/snsapi_userinfo";
                Header("Location:" . $direct_url);
            }
        } else {
            log_message('error', "function(gOpenIDre):lack of code in file" . __FILE__ . " on Line " . __LINE__);
            echo '{"result":"-1"}';
        }
    }

    // 管理功能
    public function invite($state, $scope = 'snsapi_base') {
        $state = filter_var($state);
        if ($state) {
            $redirect_uri = $this->domain_path() . 'y/gOpenIDinvite';
//            $direct_url = 'https://open.weixin.qq.com/connect/oauth2/authorize?appid='.Game_CONST::WX_Appid.'&redirect_uri='.$redirect_uri.'&response_type=code&scope='.$scope.'&state='.$state.'#wechat_redirect';
            $direct_url = $this->getWxOauthUrl($redirect_uri, $scope, $state);
            Header("Location:" . $direct_url);
        } else {
            show_404();
            exit;
        }
    }

    public function gOpenIDinvite() {
        if (isset($_GET['code'])) {
            $code  = $_GET['code'];
            $state = $_GET['state'];

            $this->load->model('wechat_model', '', TRUE);
            $result = $this->wechat_model->getInfoOpenid($code);

            if (is_array($result) && isset($result['openid'])) {
                $direct_url = $this->domain_path() . "manage/invite?code=" . $state;
                Header("Location:" . $direct_url);
            } else {
                log_message('error', "function(gOpenIDinvite):can not get open_id in file" . __FILE__ . " on Line " . __LINE__);
                $direct_url = $this->domain_path() . "y/invite/" . $state . "/snsapi_userinfo";
                Header("Location:" . $direct_url);
            }
        } else {
            log_message('error', "function(gOpenIDinvite):lack of code in file" . __FILE__ . " on Line " . __LINE__);
            echo '{"result":"-1"}';
        }
    }

    public function wx() {
        //$this->load->model("wechat_model", "wechatmodel", true);
        //$this->wechatmodel->ChangeAllCapital();

        show_404();
    }

    public function IDsg() {
        if (isset($_GET['code'])) {
            $code  = $_GET['code'];
            $state = $_GET['state'];

            $this->load->model('wechat_model', '', TRUE);
            $result = $this->wechat_model->getInfoOpenid($code);

            if (is_array($result) && isset($result['openid'])) {
//				$direct_url = Game_CONST::My_Url."f/sg?i=".$state;
                $direct_url = $this->domain_path() . "f/sg?i=" . $state;
                Header("Location:" . $direct_url);
            } else {
                log_message('error', "function(IDsg):can not get open_id in file" . __FILE__ . " on Line " . __LINE__);
//				$direct_url = Game_CONST::My_Url."y/sg/".$state.'/snsapi_userinfo';
                $direct_url = $this->domain_path() . "y/sg/" . $state . '/snsapi_userinfo';
                Header("Location:" . $direct_url);
            }
        } else {
            log_message('error', "function(IDsg):lack of code in file" . __FILE__ . " on Line " . __LINE__);
            echo '{"result":"-1"}';
        }
    }

    /*
        三公房间跳转 6人
    */
    public function sg($state = '', $scope = 'snsapi_base') {
        $state = filter_var($state);
        if ($state) {
//			$redirect_uri = Game_CONST::My_Url . 'y/IDsg' ;
            $redirect_uri = $this->domain_path() . 'y/IDsg';
//			$direct_url = 'https://open.weixin.qq.com/connect/oauth2/authorize?appid='.Game_CONST::WX_Appid.'&redirect_uri='.$redirect_uri.'&response_type=code&scope='.$scope.'&state='.$state.'#wechat_redirect';
            $direct_url = $this->getWxOauthUrl($redirect_uri, $scope, $state);
            Header("Location:" . $direct_url);
        } else {
            show_404();
            exit;
        }
    }


    public function IDnsg() {
        if (isset($_GET['code'])) {
            $code  = $_GET['code'];
            $state = $_GET['state'];

            $this->load->model('wechat_model', '', TRUE);
            $result = $this->wechat_model->getInfoOpenid($code);

            if (is_array($result) && isset($result['openid'])) {
//				$direct_url = Game_CONST::My_Url."f/nsg?i=".$state;
                $direct_url = $this->domain_path() . "f/nsg?i=" . $state;
                Header("Location:" . $direct_url);
            } else {
                log_message('error', "function(IDnsg):can not get open_id in file" . __FILE__ . " on Line " . __LINE__);
//				$direct_url = Game_CONST::My_Url."y/nsg/".$state.'/snsapi_userinfo';
                $direct_url = $this->domain_path() . "y/nsg/" . $state . '/snsapi_userinfo';
                Header("Location:" . $direct_url);
            }
        } else {
            log_message('error', "function(IDnsg):lack of code in file" . __FILE__ . " on Line " . __LINE__);
            echo '{"result":"-1"}';
        }
    }

    /*
        三公房间跳转 9人
    */
    public function nsg($state = '', $scope = 'snsapi_base') {
        $state = filter_var($state);
        if ($state) {
//			$redirect_uri = Game_CONST::My_Url . 'y/IDnsg' ;
            $redirect_uri = $this->domain_path() . 'y/IDnsg';
//			$direct_url = 'https://open.weixin.qq.com/connect/oauth2/authorize?appid='.Game_CONST::WX_Appid.'&redirect_uri='.$redirect_uri.'&response_type=code&scope='.$scope.'&state='.$state.'#wechat_redirect';
            $direct_url = $this->getWxOauthUrl($redirect_uri, $scope, $state);
            Header("Location:" . $direct_url);
        } else {
            show_404();
            exit;
        }
    }

    public function IDtsg() {
        if (isset($_GET['code'])) {
            $code  = $_GET['code'];
            $state = $_GET['state'];

            $this->load->model('wechat_model', '', TRUE);
            $result = $this->wechat_model->getInfoOpenid($code);

            if (is_array($result) && isset($result['openid'])) {
//				$direct_url = Game_CONST::My_Url."f/tsg?i=".$state;
                $direct_url = $this->domain_path() . "f/tsg?i=" . $state;
                Header("Location:" . $direct_url);
            } else {
                log_message('error', "function(IDtsg):can not get open_id in file" . __FILE__ . " on Line " . __LINE__);
//				$direct_url = Game_CONST::My_Url."y/tsg/".$state.'/snsapi_userinfo';
                $direct_url = $this->domain_path() . "y/tsg/" . $state . '/snsapi_userinfo';
                Header("Location:" . $direct_url);
            }
        } else {
            log_message('error', "function(IDtsg):lack of code in file" . __FILE__ . " on Line " . __LINE__);
            echo '{"result":"-1"}';
        }
    }

    /*
        三公房间跳转 9人
    */
    public function tsg($state = '', $scope = 'snsapi_base') {
        $state = filter_var($state);
        if ($state) {
//			$redirect_uri = Game_CONST::My_Url . 'y/IDtsg' ;
            $redirect_uri = $this->domain_path() . 'y/IDtsg';
//			$direct_url = 'https://open.weixin.qq.com/connect/oauth2/authorize?appid='.Game_CONST::WX_Appid.'&redirect_uri='.$redirect_uri.'&response_type=code&scope='.$scope.'&state='.$state.'#wechat_redirect';
            $direct_url = $this->getWxOauthUrl($redirect_uri, $scope, $state);
            Header("Location:" . $direct_url);
        } else {
            show_404();
            exit;
        }
    }

    public function clubInvite($state = '', $scope = 'snsapi_base') {
        $state = filter_var($state);
        if ($state) {
            $redirect_uri = $this->domain_path() . 'y/IDClubInvite';
            $direct_url   = $this->getWxOauthUrl($redirect_uri, $scope, $state);
            Header('Location:' . $direct_url);
        } else {
            show_404();
            exit;
        }
    }

    public function IDClubInvite() {
        if (isset($_GET['code'])) {
            $code  = $_GET['code'];
            $state = $_GET['state'];

            $this->load->model('wechat_model', '', TRUE);
            $result = $this->wechat_model->getInfoOpenid($code);

            if (is_array($result) && isset($result['openid'])) {
                $direct_url = $this->domain_path() . "club/invite?club_no=" . $state;
                Header("Location:" . $direct_url);
            } else {
                log_message('error', "function(IDClubInvite):can not get open_id in file" . __FILE__ . " on Line " . __LINE__);
                $direct_url = $this->domain_path() . "y/clubInvite/{$state}/snsapi_userinfo";
                Header("Location:" . $direct_url);
            }
        } else {
            log_message('error', "function(IDClubInvite):lack of code in file" . __FILE__ . " on Line " . __LINE__);
            echo '{"result":"-1"}';
        }
    }
}
