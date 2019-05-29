<?php
/**
 * Created by PhpStorm.
 * User: nszxyu
 * Date: 2017/11/20
 * Time: 14:57
 */

class Test extends MY_Controller{
    public function t(){
        echo 'hello world';
    }

    // get unionid by openid
    public function u()
    {
        $this->load->model('wechat_model', '', true);
        $this->wechat_model->getUnionidByOpenid();
    }
}