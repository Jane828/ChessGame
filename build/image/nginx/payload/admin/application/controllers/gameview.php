<?php

class Gameview extends CI_Controller 
{
	/*
		构造函数
	*/
	function __construct()
	{
		header('Content-Type: text/html; charset=utf-8'); 
		parent::__construct();
		$this->load->helper('url');
	}
	
	private function checkViewLogin()
	{
		if(!isset($_SESSION['LoginDealerID']) || $_SESSION['LoginDealerID']=="" || !isset($_SESSION['LoginDealerNum']) || $_SESSION['LoginDealerNum']=="")
		{
			$direct_url = base_url("account/login");
			Header("Location:".$direct_url);
			exit();
		}
		return true;
	}
	private function checkOptLogin()
	{
		if(!isset($_SESSION['LoginDealerID']) || $_SESSION['LoginDealerID']=="" || !isset($_SESSION['LoginDealerNum']) || $_SESSION['LoginDealerNum']=="")
		{
			echo '{"result":"-3"}';
			exit();
		}
		return true;
	}


	

	/**
	 * 	index
	 */
	public function index()
	{

	//	$this->checkViewLogin();
		
		$data['base_url'] = base_url();

		$data['dealer_num'] = $_GET['dealer_num'];
	//	$data['dealer_num'] = '26';
		$data['room_number'] = $_GET['room_number'];
	//	$data['room_number'] = '66666';
		$data['game_type'] = $_GET['game_type'];
	//	$data['game_type'] = '9';
		$data['round'] = $_GET['round'];
	//	$data['round'] = '1';

		$this->load->view('gameView/gameView',$data);
	}
	
	


	
	
}
