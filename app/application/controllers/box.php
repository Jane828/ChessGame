<?php
/**
 * Created by PhpStorm.
 * User: oujiaxuan
 * Date: 2019/02/20
 * Time: 10:35
 */
class Box extends MY_Controller{
    function __construct()
    {
        parent::__construct();
    }

    /**
     * 创建包厢
     * @return array|void
     */
    public function addBox(){
        $this->checkLogin();
        if (! $this->checkIsAgree()) {
            return $this->warm();
        }
        $params = getRequest();
        log_message('error', "function(addBox): kk test:".json_encode($params)." in file ".__FILE__." on Line ".__LINE__);

        if(empty($_SESSION['AccountID']) || !$params['box_name'] || !$params['game_type'] || !$params['data']){
            log_message('error', "function(getBoxInfo): AccountID or params empty:". $_SESSION['AccountID']. json_encode($params) ." in file ".__FILE__." on Line ".__LINE__);
            //return $this->ajaxSuccess($params);
            show_404();
        }
        $params['account_id'] = $_SESSION['AccountID'];
        $this->load->model('box/box_model','',true);
	$result = $this->box_model->addBox($params);
        $this->ajaxReturn($result);
    }

    /**
     * 获取包厢列表
     */
    public function getBoxList(){
        $this->checkLogin();

        if (! $this->checkIsAgree()) {
            return $this->warm();
        }
        $params = getRequest();
        if(empty($_SESSION['AccountID'])){
            log_message('error', "function(getBoxInfo): AccountID or params empty:". $_SESSION['AccountID']. json_encode($params) ." in file ".__FILE__." on Line ".__LINE__);
            show_404();
        }
        $params['account_id'] = $_SESSION['AccountID'];
        $this->load->model('box/box_model','',true);
        $result = $this->box_model->getBoxList($params);
	log_message("Info", "liuyonggetBoxList=====".json_encode($result));
        $this->ajaxReturn($result);
    }

    /**
     * 获取包厢信息
     */
    public function getBoxInfo(){
        $this->checkLogin();
        if (! $this->checkIsAgree()) {
            return $this->warm();
        }

        $params = getRequest();
        if(empty($params['account_id'])|| empty($params['box_id']) || empty($params['box_number'])){
            log_message('error', "function(getBoxInfo): AccountID or params empty:". $_SESSION['AccountID']. json_encode($params) ." in file ".__FILE__." on Line ".__LINE__);
            show_404();
        }
        $this->load->model('box/box_model','',true);
        $result = $this->box_model->getBoxInfo($params);
	log_message("info", "liuyonggetboxinfo====".json_encode($result));
        $this->ajaxReturn($result);
    }

    /**
     * 修改包厢配置
     */
    public function setBoxInfo(){
	log_message("info", "liuyongboxinfo======".json_encode(getRequest()));
        $this->checkLogin();
        if (! $this->checkIsAgree()) {
            return $this->warm();
        }
        $params = getRequest();
        if(empty($_SESSION['AccountID']) || empty($params['box_name']) || empty($params['box_id']) || empty($params['data'])){
            log_message('error', "function(getBoxInfo): AccountID or params empty:". $_SESSION['AccountID']. json_encode($params) ." in file ".__FILE__." on Line ".__LINE__);
            show_404();
        }
        $params['account_id'] = $_SESSION['AccountID'];
        $this->load->model('box/box_model','',true);
        $result = $this->box_model->setBoxInfo($params);
        $this->ajaxReturn($result);
    }

    /**
     * 修改包厢状态
     */
    public function setBoxStatus(){
        $this->checkLogin();
        if (! $this->checkIsAgree()) {
            return $this->warm();
        }
        $params = getRequest();
        if(empty($_SESSION['AccountID']) || empty($params['box_id']) || empty($params['box_number']) || !isset($params['status'])){
            log_message('error', "function(getBoxInfo): AccountID or params empty:". $_SESSION['AccountID']. json_encode($params) ." in file ".__FILE__." on Line ".__LINE__);
            show_404();
        }
        $params['account_id'] = $_SESSION['AccountID'];
        $this->load->model('box/box_model','',true);
        $result = $this->box_model->setBoxStatus($params);
        $this->ajaxReturn($result);
    }

    /**
     * 获取包厢牌桌战况
     */
    public function boxCondition(){
        $this->checkLogin();
        if (! $this->checkIsAgree()) {
            return $this->warm();
        }
        $params = getRequest();
        if(empty($_SESSION['AccountID']) || empty($params['box_id']) || empty($params['box_number'])){
            log_message('error', "function(getBoxInfo): AccountID or params empty:". $_SESSION['AccountID']. json_encode($params) ." in file ".__FILE__." on Line ".__LINE__);
            show_404();
        }
        $params['account_id'] = $_SESSION['AccountID'];
        $this->load->model('box/box_model','',true);
        $result = $this->box_model->boxCondition($params);
        $this->ajaxReturn($result);
    }

    /**
     * 解散包厢
     */
    public function delBox(){
        $this->checkLogin();
        if (! $this->checkIsAgree()) {
            return $this->warm();
        }
        $params = getRequest();
        if(empty($_SESSION['AccountID']) || empty($params['box_id']) || empty($params['box_number'])){
            log_message('error', "function(getBoxInfo): AccountID or params empty:". $_SESSION['AccountID']. json_encode($params) ." in file ".__FILE__." on Line ".__LINE__);
            show_404();
        }
        $params['account_id'] = $_SESSION['AccountID'];
        $this->load->model('box/box_model','',true);
        $result = $this->box_model->delBox($params);
        $this->ajaxReturn($result);
    }
}
