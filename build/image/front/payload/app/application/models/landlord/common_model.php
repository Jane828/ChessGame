<?php

include_once dirname(__DIR__).'/public_models.php';		//加载数据库操作类
class Landlord_Common_Model extends Public_Models
{
	public function __construct()
	{
		header('Content-Type: text/html; charset=utf-8'); 
		parent::__construct();
	}
	
	
	
}