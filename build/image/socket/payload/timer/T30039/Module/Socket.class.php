<?php

require_once dirname(__DIR__) . '/base.class.model.php';

class Socket_Model extends Base_Model
{
	
	/**
     * 实例数组
     * @var array
     */
    protected static $_socketObject = array();


	/*
		单例，返回当前类的静态对象
	*/
	public static function getModelObject($socket_name)
	{
		static $modelObject;
		if(is_object($modelObject))
		{
			return $modelObject;
		}
		else
		{
			$modelObject = new self($socket_name);
			
			return $modelObject;
		}
	}



	public function __construct($socket_name)
	{
//		self::getSocketObject($socket_name,$is_init=1);
	}

	
    /**
     * 获取实例
     * @param string $config_name
     * @throws \Exception
     */
    public static function getSocketObject($socket_name,$is_init=0)
    {   
		//var_dump(self::$_socketObject);
        if(empty(self::$_socketObject[$socket_name]) || $is_init == 1)
        {
            if($socket_name == "Timer")
			{
				$host = Config::Timer_Address;
				$port = Config::Timer_Port;

				//实例化游戏对象
				self::$_socketObject[$socket_name] = self::initSocket($host,$port);
			}
			else if($socket_name == "Processor")
			{
				$host = Config::Processor_Address;
				$port = Config::Processor_Port;

				//实例化游戏对象
				self::$_socketObject[$socket_name] = self::initSocket($host,$port);
			}
			else if($socket_name == "GameServer")
			{
				$host = Config::GameServer_Address;
				$port = Config::GameServer_Port;

				//实例化游戏对象
				self::$_socketObject[$socket_name] = self::initSocket($host,$port);
			}
        }
        return self::$_socketObject[$socket_name];
    }


	
	/*-------------通用方法--------------*/
	
	/*
		实例化socket
	*/
	public static function initSocket($host = G_CONST::EMPTY_STRING,$port = G_CONST::EMPTY_STRING)
	{
		//var_dump("initSocket:".$host.":".$port);
		if($host === G_CONST::EMPTY_STRING)
		{
			self::writeSocketLog('error', "function(initSocket):host is empty string"." in file".__FILE__." on Line ".__LINE__);
			return false;
		}
		if($port === G_CONST::EMPTY_STRING)
		{
			self::writeSocketLog('error', "function(initSocket):port is empty string"." in file".__FILE__." on Line ".__LINE__);
			return false;
		}
		
		$socket = socket_create(AF_INET, SOCK_STREAM, SOL_TCP);
		if ($socket === false) 
		{
			self::writeSocketLog('error', "function(initSocket):socket_create() failed:".socket_strerror(socket_last_error())." in file".__FILE__." on Line ".__LINE__);
			return false;
		}
		else
		{
			$result = socket_connect($socket, $host, $port);
			if($result === false) 
			{
                self::writeSocketLog('error', "function(initSocket):socket_connect() failed.\nReason: () " . socket_strerror(socket_last_error($socket)) ." in file".__FILE__." on Line ".__LINE__);
                unset($socket);
                return false;
			}
			else
			{
				self::writeSocketLog('error', "function(initSocket):socket connect: port:".$port." in file".__FILE__." on Line ".__LINE__);
				return $socket;
			}  
		}
	}
	
	
	/*
		send message
	*/
	public function sendMessageToSocket($socket_name,$message)
	{
		//var_dump($message);
		$message .= "\n";
		
		$socketObject = $this->getSocketObject($socket_name);
		$result = socket_write($socketObject, $message, strlen($message));
		if($result == false)
		{
			$this->logMessage('error', "function(sendMessageToSocket):socket_write 1st failed :"." in file".__FILE__." on Line ".__LINE__);
			$socketObject = $this->getSocketObject($socket_name,$is_init=1);
			$result = socket_write($socketObject, $message, strlen($message));
			if($result == false)
			{
				$this->logMessage('error', "function(sendMessageToSocket):socket_write 2nd failed :"." in file".__FILE__." on Line ".__LINE__);
				return false;
			}
		}
		return true;
	}


	/*
		close socket
	*/
	public function closeSocket($socket_name)
	{
		if(isset(self::$_socketObject[$socket_name]))
        {
            socket_close(self::$_socketObject[$socket_name]);
        }
		return true;
	}


	/*
		写日志
	*/
	public static function writeSocketLog($level,$content)
	{
		$content = date("Y-m-d H:i:s").' '.$content;
		
		$log_filename = "socket_".date("Y-m-d").'.log';

        $file = Config::Log_Dir.$log_filename;

        file_put_contents($file, $content,FILE_APPEND);
		 
		file_put_contents($file, "\n",FILE_APPEND);
	}

}