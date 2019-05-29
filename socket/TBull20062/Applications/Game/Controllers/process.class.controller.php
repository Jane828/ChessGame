<?php

use \GatewayWorker\Lib\Gateway;

require_once dirname(__DIR__) . '/Models/process.class.model.php';				//处理类

require_once dirname(__DIR__) . '/Models/Client/Dispatcher.class.model.php';		//IM调度文件
require_once dirname(__DIR__) . '/Models/Server/Dispatcher.class.model.php';	//server调度文件

class Process
{
    /**
     * 当客户端连接时触发
     * 如果业务不需此回调可以删除onConnect
     * 
     * @param int $client_id 连接id
     */
    public static function processOnConnect($client_id)
    {
	    //设备登陆
	    //$login_return_msg = "";
	    //Gateway::sendToClient($client_id, '{"result":"-1"}');
	    //var_dump($client_id);
	   
    }
    
    
    /**
    * 当用户断开连接时触发
    *
    * @param int $client_id 连接id
    */
    public static function processOnClose($client_id , $port , $data)
    {
        if($port == Config::GameClient_Port)
		{
			$req_ary = $data;
	        $req_ary['client_id'] = $client_id;
	        
	        $result = Dispatcher_Client::socketClosed($req_ary);
        }
    }
	
	
	/**
    * 数据处理入口
    * 
    * @param int $client_id 连接id
    * @param string $message 具体消息
    */
    public static function processOnMessage($client_id, $message, $port)
    {
	    // var_dump($message);
	    // var_dump(Gateway::getSession($client_id));
	    if($message == "@")
	    {
		    Gateway::sendToClient($client_id, "@");
	    }
	    else if(false != $message && $message != "")
	    {
		    if($port == Config::GameClient_Port || $port == Config::GameWeb_Port)
		    {
			    //判断request格式是否正确
			    $req_ary = Process_Model::getModelObject()->checkupFormat($message);
			    $operation = G_CONST::EMPTY_STRING;
			    $opcode = G_CONST::EMPTY_STRING;
			    
			    if(is_array($req_ary) && !isset($req_ary['result']))
			    {
				    $req_ary['client_id'] = $client_id;
				    $operation = $req_ary['operation'];
				    
					//判断操作符
					$result = Dispatcher_Client::dispatcherOpt($req_ary);
			    }
			    else
			    {
				    $result = $req_ary;
			    }
			    
			    if($result != OPT_CONST::NO_RETURN)
			    {
				    //格式化返回结果
				    $ret_result = Process_Model::getModelObject()->formartReturn($result,$operation);
				    //发送返回结果给client
				    Gateway::sendToClient($client_id, $ret_result);
			    }
		    }
		    else if($port == Config::GameServer_Port)		//服务器推送模块
		    {
			    //判断request格式是否正确

			    //echo $message.PHP_EOL;
			    $req_ary = Process_Model::getModelObject()->checkupServerFormat($message);
			    
			    $operation = G_CONST::EMPTY_STRING;
			    $request_params = G_CONST::EMPTY_STRING;
			    if(is_array($req_ary))
			    {
				    $req_ary['client_id'] = $client_id;
				    
				    $result = Dispatcher_Server::dispatcherOpt($req_ary);
				    
			    }
			    else
			    {
				    $result = $req_ary;
			    }
			    if($result != OPT_CONST::NO_RETURN)
			    {
				    //格式化返回结果
				    //$ret_result = Process_Model::getModelObject()->formartReturn($result,$operation,$opcode,$request_params);
				    //发送返回结果给client
				    //Gateway::sendToClient($client_id, $ret_result);
			    }
		    }
	    }
	    return true;
	    
    }
	
	/*
		写日志
	*/
	public static function writeLog($content)
	{
		$content = date("Y-m-d H:i:s").' '.$content;
		print_r($content);return;
		$log_filename = "gw_".date("Y-m-d").'.log';

		$file = Config::Log_Dir.$log_filename;

		file_put_contents($file, $content,FILE_APPEND);
		 
		file_put_contents($file, "\n",FILE_APPEND);
	}
		
}	



	
?>