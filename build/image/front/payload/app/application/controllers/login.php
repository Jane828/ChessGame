<?php
/**
 * Created by PhpStorm.
 * User: nszxyu
 * Date: 2017/12/7
 * Time: 13:54
 */

class Login extends MY_Controller{
    /*
            构造函数
        */
    function __construct(){
        parent::__construct();
//        exit("error");
    }

    public function sim(){
        $login_sim = [
            'open_id'   => 'oHwwYxD5alesj7WIegqxDK12345',
            'nickname'  => 'T001',
            'head'      => 'http://wx.qlogo.cn/mmopen/vi_32/b3M6fUvPxpxJ6x014JEiabibPmScbrdLGaZd7MSbyiaoVTBkYktlXDYPicKdOCpjvw27HxDNQJ3IsnmYrulCOYe7Fw/0'
        ];

        $this->load->model('wechat_model','',true);
        $account_data = $this->wechat_model->getGameAccountData($login_sim['open_id']);

        if(!is_array($account_data)){
            $this->wechat_model->addUser($login_sim['open_id'],$login_sim['nickname'],$login_sim['head']);
        }

        $_SESSION['WxOpenID'] = $login_sim['open_id'];

        $direct_url = $this->domain_path()."f/ym";
        Header("Location:".$direct_url);
    }

    public function sim2(){
        $login_sim = [
            'open_id'   => 'oHwwYxD5alesj7WIegqxDKsima2',
            'nickname'  => 'T002',
            'head'      => 'http://wx.qlogo.cn/mmopen/vi_32/b3M6fUvPxpxJ6x014JEiabibPmScbrdLGaZd7MSbyiaoVTBkYktlXDYPicKdOCpjvw27HxDNQJ3IsnmYrulCOYe7Fw/0'
        ];

        $this->load->model('wechat_model','',true);
        $account_data = $this->wechat_model->getGameAccountData($login_sim['open_id']);

        if(!is_array($account_data)){
            $this->wechat_model->addUser($login_sim['open_id'],$login_sim['nickname'],$login_sim['head']);
        }

        $_SESSION['WxOpenID'] = $login_sim['open_id'];

        $direct_url = $this->domain_path()."f/ym";
        Header("Location:".$direct_url);
    }

    public function sim3(){
        $login_sim = [
            'open_id'   => 'oHwwYxD5alesj7WIegqxDKsima3',
            'nickname'  => 'T003',
            'head'      => 'http://wx.qlogo.cn/mmopen/vi_32/b3M6fUvPxpxJ6x014JEiabibPmScbrdLGaZd7MSbyiaoVTBkYktlXDYPicKdOCpjvw27HxDNQJ3IsnmYrulCOYe7Fw/0'
        ];

        $this->load->model('wechat_model','',true);
        $account_data = $this->wechat_model->getGameAccountData($login_sim['open_id']);

        if(!is_array($account_data)){
            $this->wechat_model->addUser($login_sim['open_id'],$login_sim['nickname'],$login_sim['head']);
        }

        $_SESSION['WxOpenID'] = $login_sim['open_id'];

        $direct_url = $this->domain_path()."f/ym";
        Header("Location:".$direct_url);
    }

    public function sim4(){
        $login_sim = [
            'open_id'   => 'oHwwYxD5alesj7WIegqxDKsima4',
            'nickname'  => 'T004',
            'head'      => 'http://wx.qlogo.cn/mmopen/vi_32/b3M6fUvPxpxJ6x014JEiabibPmScbrdLGaZd7MSbyiaoVTBkYktlXDYPicKdOCpjvw27HxDNQJ3IsnmYrulCOYe7Fw/0'
        ];

        $this->load->model('wechat_model','',true);
        $account_data = $this->wechat_model->getGameAccountData($login_sim['open_id']);

        if(!is_array($account_data)){
            $this->wechat_model->addUser($login_sim['open_id'],$login_sim['nickname'],$login_sim['head']);
        }

        $_SESSION['WxOpenID'] = $login_sim['open_id'];

        $direct_url = $this->domain_path()."f/ym";
        Header("Location:".$direct_url);
    }

    public function sim5(){
        $login_sim = [
            'open_id'   => 'oHwwYxD5alesj7WIegqxDKsima5',
            'nickname'  => 'T005',
            'head'      => 'http://wx.qlogo.cn/mmopen/vi_32/b3M6fUvPxpxJ6x014JEiabibPmScbrdLGaZd7MSbyiaoVTBkYktlXDYPicKdOCpjvw27HxDNQJ3IsnmYrulCOYe7Fw/0'
        ];

        $this->load->model('wechat_model','',true);
        $account_data = $this->wechat_model->getGameAccountData($login_sim['open_id']);

        if(!is_array($account_data)){
            $this->wechat_model->addUser($login_sim['open_id'],$login_sim['nickname'],$login_sim['head']);
        }

        $_SESSION['WxOpenID'] = $login_sim['open_id'];

        $direct_url = $this->domain_path()."f/ym";
        Header("Location:".$direct_url);
    }

    public function sim6(){
        $login_sim = [
            'open_id'   => 'oHwwYxD5alesj7WIegqxDKsima6',
            'nickname'  => 'T006',
            'head'      => 'http://wx.qlogo.cn/mmopen/vi_32/b3M6fUvPxpxJ6x014JEiabibPmScbrdLGaZd7MSbyiaoVTBkYktlXDYPicKdOCpjvw27HxDNQJ3IsnmYrulCOYe7Fw/0'
        ];

        $this->load->model('wechat_model','',true);
        $account_data = $this->wechat_model->getGameAccountData($login_sim['open_id']);

        if(!is_array($account_data)){
            $this->wechat_model->addUser($login_sim['open_id'],$login_sim['nickname'],$login_sim['head']);
        }

        $_SESSION['WxOpenID'] = $login_sim['open_id'];

        $direct_url = $this->domain_path()."f/ym";
        Header("Location:".$direct_url);
    }

    public function sim7(){
        $login_sim = [
            'open_id'   => 'oHwwYxD5alesj7WIegqxDKsima7',
            'nickname'  => 'T007',
            'head'      => 'http://wx.qlogo.cn/mmopen/vi_32/b3M6fUvPxpxJ6x014JEiabibPmScbrdLGaZd7MSbyiaoVTBkYktlXDYPicKdOCpjvw27HxDNQJ3IsnmYrulCOYe7Fw/0'
        ];

        $this->load->model('wechat_model','',true);
        $account_data = $this->wechat_model->getGameAccountData($login_sim['open_id']);

        if(!is_array($account_data)){
            $this->wechat_model->addUser($login_sim['open_id'],$login_sim['nickname'],$login_sim['head']);
        }

        $_SESSION['WxOpenID'] = $login_sim['open_id'];

        $direct_url = $this->domain_path()."f/ym";
        Header("Location:".$direct_url);
    }

    public function sim8(){
        $login_sim = [
            'open_id'   => 'oHwwYxD5alesj7WIegqxDKsima8',
            'nickname'  => 'T008',
            'head'      => 'http://wx.qlogo.cn/mmopen/vi_32/b3M6fUvPxpxJ6x014JEiabibPmScbrdLGaZd7MSbyiaoVTBkYktlXDYPicKdOCpjvw27HxDNQJ3IsnmYrulCOYe7Fw/0'
        ];

        $this->load->model('wechat_model','',true);
        $account_data = $this->wechat_model->getGameAccountData($login_sim['open_id']);

        if(!is_array($account_data)){
            $this->wechat_model->addUser($login_sim['open_id'],$login_sim['nickname'],$login_sim['head']);
        }

        $_SESSION['WxOpenID'] = $login_sim['open_id'];

        $direct_url = $this->domain_path()."f/ym";
        Header("Location:".$direct_url);
    }

    public function sim9(){
        $login_sim = [
            'open_id'   => 'oHwwYxD5alesj7WIegqxDKsima9',
            'nickname'  => 'T009',
            'head'      => 'http://wx.qlogo.cn/mmopen/vi_32/b3M6fUvPxpxJ6x014JEiabibPmScbrdLGaZd7MSbyiaoVTBkYktlXDYPicKdOCpjvw27HxDNQJ3IsnmYrulCOYe7Fw/0'
        ];

        $this->load->model('wechat_model','',true);
        $account_data = $this->wechat_model->getGameAccountData($login_sim['open_id']);

        if(!is_array($account_data)){
            $this->wechat_model->addUser($login_sim['open_id'],$login_sim['nickname'],$login_sim['head']);
        }

        $_SESSION['WxOpenID'] = $login_sim['open_id'];

        $direct_url = $this->domain_path()."f/ym";
        Header("Location:".$direct_url);
    }

    public function sim10(){
        $login_sim = [
            'open_id'   => 'oHwwYxD5alesj7WIegqxDKsim10',
            'nickname'  => 'T010',
            'head'      => 'http://wx.qlogo.cn/mmopen/vi_32/b3M6fUvPxpxJ6x014JEiabibPmScbrdLGaZd7MSbyiaoVTBkYktlXDYPicKdOCpjvw27HxDNQJ3IsnmYrulCOYe7Fw/0'
        ];

        $this->load->model('wechat_model','',true);
        $account_data = $this->wechat_model->getGameAccountData($login_sim['open_id']);

        if(!is_array($account_data)){
            $this->wechat_model->addUser($login_sim['open_id'],$login_sim['nickname'],$login_sim['head']);
        }

        $_SESSION['WxOpenID'] = $login_sim['open_id'];

        $direct_url = $this->domain_path()."f/ym";
        Header("Location:".$direct_url);
    }

    public function sim11(){
        $login_sim = [
            'open_id'   => 'oHwwYxD5alesj7WIegqxDKsim11',
            'nickname'  => 'T011',
            'head'      => 'http://wx.qlogo.cn/mmopen/vi_32/b3M6fUvPxpxJ6x014JEiabibPmScbrdLGaZd7MSbyiaoVTBkYktlXDYPicKdOCpjvw27HxDNQJ3IsnmYrulCOYe7Fw/0'
        ];

        $this->load->model('wechat_model','',true);
        $account_data = $this->wechat_model->getGameAccountData($login_sim['open_id']);

        if(!is_array($account_data)){
            $this->wechat_model->addUser($login_sim['open_id'],$login_sim['nickname'],$login_sim['head']);
        }

        $_SESSION['WxOpenID'] = $login_sim['open_id'];

        $direct_url = $this->domain_path()."f/ym";
        Header("Location:".$direct_url);
    }

    public function sim12(){
        $login_sim = [
            'open_id'   => 'oHwwYxD5alesj7WIegqxDKsim12',
            'nickname'  => 'T012',
            'head'      => 'http://wx.qlogo.cn/mmopen/vi_32/b3M6fUvPxpxJ6x014JEiabibPmScbrdLGaZd7MSbyiaoVTBkYktlXDYPicKdOCpjvw27HxDNQJ3IsnmYrulCOYe7Fw/0'
        ];

        $this->load->model('wechat_model','',true);
        $account_data = $this->wechat_model->getGameAccountData($login_sim['open_id']);

        if(!is_array($account_data)){
            $this->wechat_model->addUser($login_sim['open_id'],$login_sim['nickname'],$login_sim['head']);
        }

        $_SESSION['WxOpenID'] = $login_sim['open_id'];

        $direct_url = $this->domain_path()."f/ym";
        Header("Location:".$direct_url);
    }



}