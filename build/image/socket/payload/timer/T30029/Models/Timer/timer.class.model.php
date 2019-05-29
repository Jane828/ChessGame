<?php

use \GatewayWorker\Lib\Gateway;  
use \Workerman\Worker;
use \Workerman\Lib\Timer;
require_once dirname(__DIR__) . '/public.class.model.php';
class Timer_Model extends Public_Model
{	
	/***************************
			logic function	
	***************************/
	/*
		添加timer
	*/
	public function buildTimer($arrData)
	{
		$timestamp = time();
		$result = array();
		
		$operation = $arrData['operation'];
		$data = $arrData['data'];
		$room_id = $arrData['room_id'];
		
		if(!isset($data['limit_time']) || trim($data['limit_time']) === G_CONST::EMPTY_STRING || $data['limit_time'] <= 0)
		{
			$this->logMessage('error', "function(buildTimer):lack of limit_time"." in file".__FILE__." on Line ".__LINE__);
			return $this->_missingPrameterArr($operation,"limit_time");
		}
		// if(!isset($data['account_id']) || trim($data['account_id']) === G_CONST::EMPTY_STRING)
		// {
		// 	$this->logMessage('error', "function(buildTimer):lack of account_id"." in file".__FILE__." on Line ".__LINE__);
		// 	return $this->_missingPrameterArr($operation,"account_id");
		// }
		if(! (isset($data['callback_array']['operation']) && isset($data['callback_array']['room_id']) && isset($data['callback_array']['data']) ) )
		{
			$this->logMessage('error', "function(buildTimer):lack of callback_array or invalid"." in file".__FILE__." on Line ".__LINE__);
			return $this->_missingPrameterArr($operation,"callback_array");
		}
		
		$limit_time = (int)$data['limit_time'];
		//$account_id = $data['account_id'];
		$callback_array = $data['callback_array'];
		
		$timer_id = Timer::add($limit_time, array($this, 'timeTrigger'), array(&$timer_id,$callback_array,), false);

		//推送给管理端
		$send_array = array(
			'operation' => "ReturnTimerid",
			'room_id' => $room_id,
			'data' => array(
				//'account_id' => $account_id,
				'timer_id' => $timer_id
		    	)
			);
		@$this->sendServerMgnt($send_array);
		
		return OPT_CONST::NO_RETURN;
	}
	
	
	
	public function timeTrigger($timer_id,$callback_array)
	{
		//推送给管理端
		$callback_array['data']['timer_id'] = $timer_id;
		@$this->sendServerMgnt($callback_array);
		
		return true;
	}
	
	
	
	/*
		推给游戏端
	*/
	protected function sendServerMgnt($msg_array = "")
	{
		$message = $this->_JSON($msg_array);

		$socket_name = "GameServer";
		$Socket_Model = Socket_Model::getModelObject($socket_name);
		$Socket_Model->sendMessageToSocket($socket_name,$message);

		return true;

	}
	
	
	/*
		释放timer
	*/
	public function deleteTimer($arrData)
	{
		$timestamp = time();
		$result = array();
		
		$operation = $arrData['operation'];
		$data = $arrData['data'];
		$room_id = $arrData['room_id'];
		
		if(!isset($data['timer_id']) || trim($data['timer_id']) === G_CONST::EMPTY_STRING || $data['timer_id'] <= 0)
		{
			$this->logMessage('error', "function(releaseTimer):lack of timer_id"." in file".__FILE__." on Line ".__LINE__);
			return $this->_missingPrameterArr($operation,"timer_id");
		}
		
		$timer_id = (int)$data['timer_id'];
		
		Timer::del($timer_id);
		
		return OPT_CONST::NO_RETURN;
	}
	
}