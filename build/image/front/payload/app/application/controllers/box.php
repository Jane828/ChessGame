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

        if(!$params['account_id'] || !$params['box_name'] || !$params['game_type'] || !$params['data']){
            return $this->ajaxSuccess($params);
            //show_404();
        }
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
        if(empty($params['account_id'])){
            show_404();
        }
        $this->load->model('box/box_model','',true);
        $result = $this->box_model->getBoxList($params);
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
        if(empty($params['account_id']) || empty($params['box_id']) || empty($params['box_number'])){
            show_404();
        }
        $this->load->model('box/box_model','',true);
        $result = $this->box_model->getBoxInfo($params);
        $this->ajaxReturn($result);
    }

    /**
     * 修改包厢配置
     */
    public function setBoxInfo(){
        $this->checkLogin();
        if (! $this->checkIsAgree()) {
            return $this->warm();
        }
        $params = getRequest();
        if(empty($params['account_id']) || empty($params['box_name']) || empty($params['box_id']) || empty($params['data'])){
            show_404();
        }
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
        if(empty($params['account_id']) || empty($params['box_id']) || empty($params['box_number']) || !isset($params['status'])){
            show_404();
        }
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
        if(empty($params['account_id']) || empty($params['box_id']) || empty($params['box_number'])){
            show_404();
        }
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
        if(empty($params['account_id']) || empty($params['box_id']) || empty($params['box_number'])){
            show_404();
        }
        $this->load->model('box/box_model','',true);
        $result = $this->box_model->delBox($params);
        $this->ajaxReturn($result);
    }
}