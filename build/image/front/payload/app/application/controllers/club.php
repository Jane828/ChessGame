<?php
/**
 * User: leoxml
 * DateTime: 2018-01-26 22:28
 */
class Club extends MY_Controller
{
    public function index()
    {
        if (!isset($_SESSION['WxOpenID'])) {
            $redirect_uri = $this->domain_path().'y/club';
            $direct_url = $this->getWxOauthUrl($redirect_uri,'snsapi_userinfo');
            Header("Location:".$direct_url);
            exit;
        }
        $open_id = $_SESSION['WxOpenID'];
        $request_ary['open_id'] = $open_id;
        $this->load->model('account/account_model','',true);
        $userinfo_result = $this->account_model->getUserInfo($request_ary);
        if(!isset($userinfo_result['data']['account_id']) || $userinfo_result['data']['account_id'] == "")
        {
            $redirect_uri = $this->domain_path().'y/club';
            $direct_url = $this->getWxOauthUrl($redirect_uri,'snsapi_userinfo');
            Header("Location:".$direct_url);
            exit;
        }
        if (! $this->checkIsAgree()) {
            return $this->warm();
        }

        $data = [
            'image_url' => Game_CONST::ImageUrl,
            'base_url' => $this->domain_path(),
        ];

        $this->load->model('wechat_model','',true);
        $config_ary = $this->wechat_model->getWxConfig();
        $data['config_ary']=$config_ary;

        $this->load->model('club/club_model', '', true);

        $res = $this->club_model->clubs($open_id);
        $clubs = 0 == $res['code'] ? $res['data'] : [];

        $data['org_list'] = json_encode($clubs);

        $this->load->view('club/index', $data);
    }

    // 页面-公会动态
    public function trend()
    {
        $club_no = isset($_GET['club_no']) ? $_GET['club_no'] : 0;

        if (!isset($_SESSION['WxOpenID'])) {
            $redirect_uri = $this->domain_path().'y/club';
            $direct_url = $this->getWxOauthUrl($redirect_uri,'snsapi_userinfo', $club_no);
            Header("Location:".$direct_url);
            exit;
        }

        $data = [
            'base_url'  => $this->domain_path(),
            'image_url' => Game_CONST::ImageUrl,
            'club_no'   => $club_no
        ];

        $this->load->view('club/trend', $data);
    }

    // 页面-欢乐豆明细
    public function beanDetail()
    {
        if (!isset($_SESSION['WxOpenID'])) {
            $redirect_uri = $this->domain_path().'y/club';
            $direct_url = $this->getWxOauthUrl($redirect_uri,'snsapi_userinfo');
            Header("Location:".$direct_url);
            exit;
        }

        $club_no = isset($_GET['club_no']) ? $_GET['club_no'] : 0;
        $ucode = isset($_GET['ucode']) ? $_GET['ucode'] : 0;

        $data = [
            'base_url'  => $this->domain_path(),
            'image_url' => Game_CONST::ImageUrl,
            'club_no'   => $club_no,
            'ucode'     => $ucode
        ];

        $this->load->view('club/bean_detail', $data);
    }

    // 页面-新的申请
    public function applyList()
    {
        if (!isset($_SESSION['WxOpenID'])) {
            $redirect_uri = $this->domain_path().'y/club';
            $direct_url = $this->getWxOauthUrl($redirect_uri,'snsapi_userinfo');
            Header("Location:".$direct_url);
            exit;
        }

        $club_no = isset($_GET['club_no']) ? $_GET['club_no'] : 0;

        $data = [
            'base_url'  => $this->domain_path(),
            'image_url' => Game_CONST::ImageUrl,
            'club_no'   => $club_no,
        ];

        $this->load->view('club/applies', $data);
    }

    // 页面-成员列表
    public function players()
    {
        if (!isset($_SESSION['WxOpenID'])) {
            $redirect_uri = $this->domain_path().'y/club';
            $direct_url = $this->getWxOauthUrl($redirect_uri,'snsapi_userinfo');
            Header("Location:".$direct_url);
            exit;
        }

        $club_no = isset($_GET['club_no']) ? $_GET['club_no'] : 0;

        $this->load->model('club/club_model', '', true);

        $data = [
            'base_url'  => $this->domain_path(),
            'image_url' => Game_CONST::ImageUrl,
            'club_no'   => $club_no,
        ];

        $result = $this->club_model->clubInfo($club_no);
        if (0 == $result['code']) {
            $data = array_merge($data, $result['data']);
        } else {
            show_404();
            exit();
        }

        $this->load->view('club/club_info', $data);
    }

    // 页面-消耗设置
    public function consume()
    {
        if (!isset($_SESSION['WxOpenID'])) {
            $redirect_uri = $this->domain_path().'y/club';
            $direct_url = $this->getWxOauthUrl($redirect_uri,'snsapi_userinfo');
            Header("Location:".$direct_url);
            exit;
        }

        $this->load->model('club/club_model', '', true);

        $user = $this->club_model->user($_SESSION['WxOpenID']);
        if (empty($user)) {
            $direct_url = base_url("y/club");
            Header("Location:".$direct_url);
            return false;
        }

        $club_no = isset($_GET['club_no']) ? $_GET['club_no'] : 0;
        $consume = $this->club_model->getConsume($club_no);

        $data = [
            'base_url'  => $this->domain_path(),
            'image_url' => Game_CONST::ImageUrl,
            'club_no'   => $club_no,
            'consume'   => $consume
        ];

        $this->load->view('club/set_consume', $data);
    }

    // 页面-设置密钥
    public function setSecret()
    {
        if (!isset($_SESSION['WxOpenID'])) {
            $redirect_uri = $this->domain_path().'y/club';
            $direct_url = $this->getWxOauthUrl($redirect_uri,'snsapi_userinfo');
            Header("Location:".$direct_url);
            exit;
        }

        $this->load->model('club/club_model', '', true);

        $user = $this->club_model->user($_SESSION['WxOpenID']);
        if (empty($user)) {
            $direct_url = base_url("y/yh");
            Header("Location:".$direct_url);
            return false;
        }

        $data = [
            'base_url'  => $this->domain_path(),
            'image_url' => Game_CONST::ImageUrl,
            'phone'     => $user['phone'],
        ];

        $this->load->view('club/set_secret', $data);
    }

    // 页面-创建公会
    public function addClub()
    {
        if (!isset($_SESSION['WxOpenID'])) {
            $redirect_uri = $this->domain_path().'y/club';
            $direct_url = $this->getWxOauthUrl($redirect_uri,'snsapi_userinfo');
            Header("Location:".$direct_url);
            exit;
        }

        $this->load->model('club/club_model', '', true);

        $user = $this->club_model->user($_SESSION['WxOpenID']);
        if (empty($user)) {
            $redirect_uri = $this->domain_path().'y/club';
            $direct_url = $this->getWxOauthUrl($redirect_uri,'snsapi_userinfo');
            Header("Location:".$direct_url);
            exit;
        }

        $data = [
            'base_url'  => $this->domain_path(),
            'image_url' => Game_CONST::ImageUrl,
            'needCard'  => 10
        ];

        $this->load->view('club/create_club', $data);
    }

    public function orgs()
    {
        if (!isset($_SESSION['WxOpenID']) || empty($_SESSION['WxOpenID'])) {
            echo json_encode(['code'=>-1, 'msg'=>'操作失败']);
            exit();
        }

        $this->load->model('club/club_model', '', true);

        $result = $this->club_model->clubs($_SESSION['WxOpenID']);

        echo json_encode($result);
        exit();
    }

    public function info()
    {
        if (!isset($_SESSION['WxOpenID']) || empty($_SESSION['WxOpenID'])) {
            echo json_encode(['code'=>-1, 'msg'=>'操作失败']);
            exit();
        }

        if (!isset($_GET['club_no']) && empty($_GET['club_no'])) {
            exit(json_encode(['code'=>-1, 'msg'=>'缺少参数']));
        }

        if (0 == $_GET['club_no']) {
            exit(json_encode(['code'=>-1, 'msg'=>'未加入公会']));
        }

        $this->load->model('club/club_model', '', true);

        $result = $this->club_model->info($_SESSION['WxOpenID'], $_GET['club_no']);

        echo json_encode($result);
        exit();
    }

    // 创建公会
    public function create()
    {
        if (!isset($_SESSION['WxOpenID']) || empty($_SESSION['WxOpenID'])) {
            echo json_encode(['code'=>-1, 'msg'=>'操作失败']);
            exit();
        }

        $this->load->model('club/club_model', '', true);

        $user = $this->club_model->user($_SESSION['WxOpenID']);
        if (empty($user)) {
            echo json_encode(['code'=>-1, 'msg'=>'操作失败']);
            exit();
        }

        $params = json_decode(file_get_contents('php://input'),true);

        if (!isset($params['name']) || empty($params['name'])) {
            echo json_encode(['code'=>-1, 'msg'=>'请输入公会名称']);
            exit();
        }

        $ary = [
            'name'  => $params['name'],
            'user'  => $user
        ];
        $result = $this->club_model->createClub($ary);
        echo json_encode($result);
        exit();
    }

    // 修改公会名称
    public function rename()
    {
        if (!isset($_SESSION['WxOpenID']) || empty($_SESSION['WxOpenID'])) {
            echo json_encode(['code'=>-1, 'msg'=>'操作失败']);
            exit();
        }

        $this->load->model('club/club_model', '', true);

        $user = $this->club_model->user($_SESSION['WxOpenID']);
        if (empty($user)) {
            echo json_encode(['code'=>-1, 'msg'=>'操作失败']);
            exit();
        }

        $params = json_decode(file_get_contents('php://input'),true);

        if (!isset($params['club_no']) || empty($params['club_no'])) {
            echo json_encode(['code'=>-1, 'msg'=>'缺少参数：club_no']);
            exit();
        }

        if (!isset($params['name']) || empty($params['name'])) {
            echo json_encode(['code'=>-1, 'msg'=>'请输入公会名称']);
            exit();
        }

        $ary = [
            'name'      => $params['name'],
            'club_no'   => $params['club_no'],
            'user'      => $user
        ];
        $result = $this->club_model->rename($ary);

        echo json_encode($result);
        exit();
    }

    // 获取设置密钥验证码
    public function vcode()
    {
        if (!isset($_SESSION['WxOpenID']) || empty($_SESSION['WxOpenID'])) {
            echo json_encode(['code'=>-1, 'msg'=>'操作失败']);
            exit();
        }

        $this->load->model('club/club_model', '', true);

        if (!isset($_GET['phone']) || empty($_GET['phone'])) {
            echo json_encode(['code'=>-1, 'msg'=>'缺少参数：phone']);
            exit();
        }
        if (!preg_match('/^1\d{10}$/', $_GET['phone'])) {
            return ['code'=>-1, 'msg'=>'请输入正确的手机号'];
        }
        $result = $this->club_model->vcode($_SESSION['WxOpenID'], $_GET['phone']);

        echo json_encode($result);
        exit();
    }

    // 设置密钥
    public function secret()
    {
        if (!isset($_SESSION['WxOpenID']) || empty($_SESSION['WxOpenID'])) {
            echo json_encode(['code'=>-1, 'msg'=>'操作失败']);
            exit();
        }

        $this->load->model('club/club_model', '', true);

        $params = json_decode(file_get_contents('php://input'),true);

        if (!isset($params['phone']) || empty($params['phone'])) {
            echo json_encode(['code'=>-1, 'msg'=>'缺少参数：phone']);
            exit();
        }
        if (!isset($params['vcode']) || empty($params['vcode'])) {
            echo json_encode(['code'=>-1, 'msg'=>'缺少参数：vcode']);
            exit();
        }
        if (!isset($params['secret']) || empty($params['secret'])) {
            echo json_encode(['code'=>-1, 'msg'=>'缺少参数：secret']);
            exit();
        }
        if (!preg_match('/^\d{4}$/', $params['vcode'])) {
            return ['code'=>-1, 'msg'=>'请输入正确的手机号'];
        }
        if (!preg_match('/^1\d{10}$/', $params['phone'])) {
            return ['code'=>-1, 'msg'=>'请输入正确的手机号'];
        }
        $len = strlen($params['secret']);
        if ($len < 6 || $len > 20) {
            echo json_encode(['code'=>-1, 'msg'=>'请输入6-20位密钥']);
            exit();
        }
        $ary = [
            'secret'=> $params['secret'],
            'phone' => $params['phone'],
            'vcode' => $params['vcode'],
            'openid'=> $_SESSION['WxOpenID']
        ];
        $result = $this->club_model->setSecret($ary);

        echo json_encode($result);
        exit();
    }

    // 公会动态
    public function dynamics()
    {
        if (!isset($_SESSION['WxOpenID']) || empty($_SESSION['WxOpenID'])) {
            echo json_encode(['code'=>-1, 'msg'=>'获取失败']);
            exit();
        }

        $this->load->model('club/club_model', '', true);

        $user = $this->club_model->user($_SESSION['WxOpenID']);
        if (empty($user)) {
            echo json_encode(['code'=>-1, 'msg'=>'获取失败']);
            exit();
        }

        if (!isset($_GET['club_no']) || empty($_GET['club_no'])) {
            echo json_encode(['code'=>-1, 'msg'=>'缺少参数']);
            exit();
        }
        if (!isset($_GET['page']) || empty($_GET['page'])) {
            $page = 1;
        } else {
            $page = $_GET['page'];
        }

        $ary = [
            'club_no'   => $_GET['club_no'],
            'user'      => $user,
            'page'      => $page
        ];
        $result = $this->club_model->dynamics($ary);

        echo json_encode($result);
        exit();
    }

    // 申请列表
    public function applies()
    {
        if (!isset($_SESSION['WxOpenID']) || empty($_SESSION['WxOpenID'])) {
            echo json_encode(['code'=>-1, 'msg'=>'获取失败']);
            exit();
        }

        $this->load->model('club/club_model', '', true);

        $user = $this->club_model->user($_SESSION['WxOpenID']);
        if (empty($user)) {
            echo json_encode(['code'=>-1, 'msg'=>'获取失败']);
            exit();
        }

        if (!isset($_GET['club_no']) || empty($_GET['club_no'])) {
            echo json_encode(['code'=>-1, 'msg'=>'缺少参数：club_no']);
            exit();
        }
        if (!isset($_GET['page']) || empty($_GET['page'])) {
            $page = 1;
        } else {
            $page = $_GET['page'];
        }

        $ary = [
            'club_no'   => $_GET['club_no'],
            'user'      => $user,
            'page'      => $page
        ];
        $result = $this->club_model->applies($ary);

        echo json_encode($result);
        exit();
    }

    // 成员列表
    public function members()
    {
        if (!isset($_SESSION['WxOpenID']) || empty($_SESSION['WxOpenID'])) {
            echo json_encode(['code'=>-1, 'msg'=>'获取失败']);
            exit();
        }

        $this->load->model('club/club_model', '', true);

        $user = $this->club_model->user($_SESSION['WxOpenID']);
        if (empty($user)) {
            echo json_encode(['code'=>-1, 'msg'=>'获取失败']);
            exit();
        }

        if (!isset($_GET['club_no']) || empty($_GET['club_no'])) {
            echo json_encode(['code'=>-1, 'msg'=>'缺少参数']);
            exit();
        }

        $ary = [
            'club_no'   => $_GET['club_no'],
            'user'      => $user,
        ];
        $result = $this->club_model->members($ary);

        echo json_encode($result);
        exit();
    }

    // 欢乐豆明细列表
    public function beans()
    {
        if (!isset($_SESSION['WxOpenID']) || empty($_SESSION['WxOpenID'])) {
            echo json_encode(['code'=>-1, 'msg'=>'获取失败']);
            exit();
        }

        $this->load->model('club/club_model', '', true);

        $user = $this->club_model->user($_SESSION['WxOpenID']);
        if (empty($user)) {
            echo json_encode(['code'=>-1, 'msg'=>'获取失败']);
            exit();
        }

        if (!isset($_GET['club_no']) || empty($_GET['club_no'])) {
            echo json_encode(['code'=>-1, 'msg'=>'缺少参数：club_no']);
            exit();
        }
        // 要查的成员编号
        if (!isset($_GET['ucode']) || empty($_GET['ucode'])) {
            $_GET['ucode'] = $user['code'];
        }
        if (!isset($_GET['page']) || empty($_GET['page'])) {
            $page = 1;
        } else {
            $page = $_GET['page'];
        }

        $ary = [
            'club_no'   => $_GET['club_no'],
            'code'      => $_GET['ucode'],
            'page'      => $page
        ];
        $result = $this->club_model->beans($ary);

        echo json_encode($result);
        exit();
    }

    // 我的欢乐豆明细列表
    public function myBeans()
    {
        if (!isset($_SESSION['WxOpenID']) || empty($_SESSION['WxOpenID'])) {
            echo json_encode(['code'=>-1, 'msg'=>'获取失败']);
            exit();
        }

        $this->load->model('club/club_model', '', true);

        $user = $this->club_model->user($_SESSION['WxOpenID']);
        if (empty($user)) {
            echo json_encode(['code'=>-1, 'msg'=>'获取失败']);
            exit();
        }
        $params = json_decode(file_get_contents('php://input'),true);

        if (!isset($params['club_no']) || empty($params['club_no'])) {
            echo json_encode(['code'=>-1, 'msg'=>'缺少参数：club_no']);
            exit();
        }
        if (!isset($params['page']) || empty($params['page'])) {
            $page = 1;
        } else {
            $page = $params['page'];
        }

        $ary = [
            'club_no'   => $params['club_no'],
            'user'      => $user,
            'page'      => $page
        ];
        $result = $this->club_model->myBeans($ary);

        echo json_encode($result);
        exit();
    }

    //邀请函view
    public function invite()
    {
        if (isset($_SESSION['WxOpenID']) && isset($_GET['club_no']))
        {
            $open_id = $_SESSION['WxOpenID'];
            $state = $_GET['club_no'];
            $club_no = $state;
            if (empty($state)) {
                log_message('error', 'function(club/invite):state in file'.__FILE__.'on line:'.__LINE__);
                show_404();
                exit;
            }
            $request_ary['open_id'] = $open_id;
            $this->load->model('account/account_model','',true);
            $userinfo_result = $this->account_model->getUserInfo($request_ary);
            if(!isset($userinfo_result['data']['account_id']) || $userinfo_result['data']['account_id'] == "")
            {
                $redirect_uri = $this->domain_path().'y/IDClubInvite';
                $direct_url = $this->getWxOauthUrl($redirect_uri,'snsapi_userinfo');
                Header("Location:".$direct_url);
                exit;
            }
            if (! $this->checkIsAgree()) {
                return $this->warm();
            }
            $request_ary['account_id'] =$userinfo_result['data']['account_id'];

            $data['base_url'] = $this->domain_path();

            $data['user'] = $userinfo_result['data'];

            $data['open_id'] = "";
            $data['account_id'] = $userinfo_result['data']['account_id'];
            $data['share_url'] =  base_url("y/clubInvite/".$_GET['club_no']);
            $data['share_icon'] = base_url("files/images/guild/invitation.png");
            $data['file_url']  = Game_Const::ImageUrl;
            $data['image_url'] = Game_Const::ImageUrl;

            $request_ary['club_no'] = $_GET['club_no'];
            $request_ary['aid'] = $userinfo_result['data']['account_id'];
            $this->load->model('club/club_model','',true);
            $result = $this->club_model->inviteData($request_ary);

            $this->load->model('wechat_model','',true);
            $config_ary = $this->wechat_model->getWxConfig();
            $data['config_ary']=$config_ary;


            $data['club_no'] = $_GET['club_no'];
            $data['invite_nick'] = $result['data']['invite_nick'];
            $data['club_name'] = $result['data']['club_name'];
            $data['is_join'] = $result['data']['is_join'];

            $this->load->view("club/invite.php", $data);
        }
        else if (isset($_GET['club_no']))
        {
            $direct_url = base_url('y/clubInvite/'.$_GET['club_no']);
            Header('Location:'.$direct_url);
        }
    }

    // 申请加入
    public function apply()
    {
        if (!isset($_SESSION['WxOpenID']) || empty($_SESSION['WxOpenID'])) {
            echo json_encode(['code'=>-1, 'msg'=>'操作失败']);
            exit();
        }

        $this->load->model('club/club_model', '', true);

        $user = $this->club_model->user($_SESSION['WxOpenID']);
        if (empty($user)) {
            echo json_encode(['code'=>-1, 'msg'=>'操作失败']);
            exit();
        }

        $params = json_decode(file_get_contents('php://input'),true);

        if (!isset($params['club_no']) || empty($params['club_no'])) {
            echo json_encode(['code'=>-1, 'msg'=>'缺少参数：club_no']);
            exit();
        }

        $ary = [
            'club_no'   => $params['club_no'],
            'user'      => $user
        ];
        $result = $this->club_model->applyJoin($ary);
        echo json_encode($result);
        exit();
    }

    // 同意|拒绝 入会
    public function dealJoin()
    {
        if (!isset($_SESSION['WxOpenID']) || empty($_SESSION['WxOpenID'])) {
            echo json_encode(['code'=>-1, 'msg'=>'操作失败']);
            exit();
        }

        $this->load->model('club/club_model', '', true);

        $user = $this->club_model->user($_SESSION['WxOpenID']);
        if (empty($user)) {
            echo json_encode(['code'=>-1, 'msg'=>'操作失败']);
            exit();
        }

        $params = json_decode(file_get_contents('php://input'),true);

        if (!isset($params['club_no']) || empty($params['club_no'])) {
            echo json_encode(['code'=>-1, 'msg'=>'缺少参数：club_no']);
            exit();
        }
        if (!isset($params['action']) || empty($params['action'])) {
            echo json_encode(['code'=>-1, 'msg'=>'缺少参数：action']);
            exit();
        }
        if (!isset($params['ucode']) || empty($params['ucode'])) {
            echo json_encode(['code'=>-1, 'msg'=>'缺少参数：ucode']);
            exit();
        }

        $ary = [
            'club_no'   => $params['club_no'],
            'action'    => $params['action'],
            'code'      => $params['ucode'],
            'user'      => $user
        ];
        $result = $this->club_model->dealJoin($ary);
        echo json_encode($result);
        exit();
    }

    // 赠豆|减豆
    public function dealBean()
    {
        if (!isset($_SESSION['WxOpenID']) || empty($_SESSION['WxOpenID'])) {
            exit(json_encode(['code'=>-1, 'msg'=>'操作失败']));
        }

        $this->load->model('club/club_model', '', true);

        $user = $this->club_model->user($_SESSION['WxOpenID']);
        if (empty($user)) {
            exit(json_encode(['code'=>-1, 'msg'=>'操作失败']));
        }

//        if (empty($user['secret'])) {
//            exit(json_encode(['code'=>-2, 'msg'=>'请先设置密钥']));
//        }

        $params = json_decode(file_get_contents('php://input'),true);

        if (!isset($params['club_no']) || empty($params['club_no'])) {
            exit(json_encode(['code'=>-1, 'msg'=>'缺少参数：club_no']));
        }
        if (!isset($params['action']) || empty($params['action'])) {
            exit(json_encode(['code'=>-1, 'msg'=>'缺少参数：action']));
        }
        if (!isset($params['ucode']) || empty($params['ucode'])) {
            exit(json_encode(['code'=>-1, 'msg'=>'缺少参数：ucode']));
        }
        if (!isset($params['bean'])) {
            exit(json_encode(['code'=>-1, 'msg'=>'缺少参数：bean']));
        }
//        if (!isset($params['secret']) || empty($params['secret'])) {
//            exit(json_encode(['code'=>-1, 'msg'=>'缺少参数：secret']));
//        }
        if (!in_array($params['action'], ['send', 'out'])) {
            exit(json_encode(['code'=>-1, 'msg'=>'参数错误：action']));
        }
        if ($params['bean'] < 1 || $params['bean'] > 1e6) {
            exit(json_encode(['code'=>-1, 'msg'=>'请输入1-1000000豆数']));
        }
//        if (md5($params['secret']) != $user['secret']) {
//            exit(json_encode(['code'=>-1, 'msg'=>'密钥错误']));
//        }
        $ary = [
            'club_no'   => $params['club_no'],
            'action'    => $params['action'],
            'code'      => $params['ucode'],
            'bean'      => $params['bean'],
            'user'      => $user
        ];
        $result = $this->club_model->dealBean($ary);
        exit(json_encode($result));
    }

    // 成员退出
    public function quit()
    {
        if (!isset($_SESSION['WxOpenID']) || empty($_SESSION['WxOpenID'])) {
            exit(json_encode(['code'=>-1, 'msg'=>'操作失败']));
        }

        $this->load->model('club/club_model', '', true);

        $user = $this->club_model->user($_SESSION['WxOpenID']);
        if (empty($user)) {
            exit(json_encode(['code'=>-1, 'msg'=>'操作失败']));
        }

        $params = json_decode(file_get_contents('php://input'),true);

        if (!isset($params['club_no']) || empty($params['club_no'])) {
            exit(json_encode(['code'=>-1, 'msg'=>'缺少参数：club_no']));
        }
        $ary = [
            'club_no'   => $params['club_no'],
            'user'      => $user
        ];
        $result = $this->club_model->quit($ary);
        exit(json_encode($result));
    }

    // 设置消耗
    public function setConsume()
    {
        if (!isset($_SESSION['WxOpenID']) || empty($_SESSION['WxOpenID'])) {
            echo json_encode(['code'=>-1, 'msg'=>'操作失败']);
            exit();
        }

        $this->load->model('club/club_model', '', true);

        $user = $this->club_model->user($_SESSION['WxOpenID']);
        if (empty($user)) {
            echo json_encode(['code'=>-1, 'msg'=>'操作失败']);
            exit();
        }

        $params = json_decode(file_get_contents('php://input'),true);

        if (!isset($params['club_no']) || empty($params['club_no'])) {
            echo json_encode(['code'=>-1, 'msg'=>'缺少参数：club_no']);
            exit();
        }

        $ary = [
            'club_no'   => $params['club_no'],
            'winner1'   => isset($params['winner1']) ? (int)$params['winner1'] : 0,
            'winner2'   => isset($params['winner2']) ? (int)$params['winner2'] : 0,
            'winner3'   => isset($params['winner3']) ? (int)$params['winner3'] : 0,
        ];
        if ($ary['winner1']<0 || $ary['winner1']>20) {
            echo json_encode(['code'=>-1, 'msg'=>'请设置0-20']);
            exit();
        }
        if ($ary['winner2']<0 || $ary['winner2']>20) {
            echo json_encode(['code'=>-1, 'msg'=>'请设置0-20']);
            exit();
        }
        if ($ary['winner3']<0 || $ary['winner3']>20) {
            echo json_encode(['code'=>-1, 'msg'=>'请设置0-20']);
            exit();
        }
        $result = $this->club_model->setConsume($ary);
        echo json_encode($result);
        exit();
    }

    // 踢出成员
    public function kick()
    {
        if (!isset($_SESSION['WxOpenID']) || empty($_SESSION['WxOpenID'])) {
            exit(json_encode(['code'=>-1, 'msg'=>'操作失败']));
        }

        $this->load->model('club/club_model', '', true);

        $user = $this->club_model->user($_SESSION['WxOpenID']);
        if (empty($user)) {
            exit(json_encode(['code'=>-1, 'msg'=>'操作失败']));
        }

//        if (empty($user['secret'])) {
//            exit(json_encode(['code'=>-2, 'msg'=>'请先设置密钥']));
//        }

        $params = json_decode(file_get_contents('php://input'),true);

        if (!isset($params['club_no']) || empty($params['club_no'])) {
            exit(json_encode(['code'=>-1, 'msg'=>'缺少参数：club_no']));
        }
        if (!isset($params['ucode']) || empty($params['ucode'])) {
            exit(json_encode(['code'=>-1, 'msg'=>'缺少参数：ucode']));
        }
//        if (!isset($params['secret'])) {
//            exit(json_encode(['code'=>-1, 'msg'=>'缺少参数：secret']));
//        }
//        if (md5($params['secret']) != $user['secret']) {
//            exit(json_encode(['code'=>-1, 'msg'=>'密钥错误']));
//        }
        $ary = [
            'club_no'   => $params['club_no'],
            'code'      => $params['ucode'],
            'user'      => $user
        ];
        $result = $this->club_model->kick($ary);
        exit(json_encode($result));
    }
}