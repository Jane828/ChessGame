<?php if ( ! defined('BASEPATH')) exit('No direct script access allowed');


//include_once dirname(dirname(__DIR__)).'/third_party/phpcode_shield.php';		//加载防注入代码
class Wxauth extends CI_Controller {
	
	function __construct(){
		header('Content-Type: text/html; charset=utf-8'); 
		parent::__construct();
	}
	
	public function index()
	{
		show_404();
	}

	public function updateUserInfo()
	{
		$this->load->model('wechat_model','',true);
		$update_result = $this->wechat_model->updateUserInfo($_POST);
		return true;
	}
	
}
