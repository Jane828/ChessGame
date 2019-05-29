<?php

class Notice extends CI_Controller
{
	/*
		构造函数
	*/
	function __construct()
	{
		header('Content-Type: text/html; charset=utf-8'); 
		parent::__construct();
	}
	
	function info(){
        $this->load->model('admin/notice_model','',true);
        $data = [];
        $maintain_list = $this->notice_model->getMaintainList();

        foreach ($maintain_list as &$value){
            $value->state_text = is_null($value->is_delete) || 1 == $value->is_delete ? '关闭' : '开启';
            $value->state_other_text = is_null($value->is_delete) || 1 == $value->is_delete ? '开启' : '关闭';
        }

        $data['maintain_list'] = $maintain_list;

        $broadcast_list = $this->notice_model->getBroadcastList();

        foreach ($broadcast_list as &$value){
            $value->state_text = $value->state == 1 ? '开启' : '关闭';
            $value->state_other_text = $value->state == 1 ? '关闭' : '开启';
        }

        $data['broadcast_list'] = $broadcast_list;

        $this->json($data);
    }

    function edit_broadcast(){
        $params = file_get_contents('php://input');
        $params_ary = json_decode($params,true);
        $id = isset($params_ary['id']) ? $params_ary['id'] : 0;
        if(!$id){
            $this->json([]);exit();
        }

        $update = [];
        $content = isset($params_ary['content']) ? $params_ary['content'] : '';
        if($content){
            $update['content'] = $content;
        }
        $state = isset($params_ary['state']) ? $params_ary['state'] : 0;
        if($state){
            $update['state'] = $state;
        }
        if(empty($update)){
            $this->json([]);exit();
        }
        $this->load->model('admin/notice_model','',true);
        $this->notice_model->editBroadcast($id,$update);
        $this->json($update);
    }

    function edit_maintain(){
        $params = file_get_contents('php://input');
        $params_ary = json_decode($params,true);
        $game_type = isset($params_ary['game_type']) ? $params_ary['game_type'] : 0;
        if(!$game_type){
            $this->json([]);exit();
        }

        $update = [];
        $service_text = isset($params_ary['service_text']) ? $params_ary['service_text'] : '';
        if($service_text){
            $update['service_text'] = $service_text;
        }

        if(isset($params_ary['state'])){
            $update['is_delete'] = $params_ary['state'];
        }
        if(empty($update)){
            $this->json([]);exit();
        }
        $this->load->model('admin/notice_model','',true);
        $this->notice_model->editMaintain($game_type, $update);
        $this->json($update);
    }

    private function json($result){
	    echo json_encode($result);
    }
	
}
