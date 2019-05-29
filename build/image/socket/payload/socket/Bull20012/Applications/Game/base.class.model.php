<?php

use \GatewayWorker\Lib\Gateway;
use \Module\DB;

require_once (__DIR__ . '/Config/Config.php');

require_once (__DIR__ . '/Lib/Server.php');
require_once (__DIR__ . '/Lib/G_CONST.php');
require_once (__DIR__ . '/Lib/DB_CONST.php');
require_once (__DIR__ . '/Lib/OPT_CONST.php');
require_once (__DIR__ . '/Lib/Account_CONST.php');
require_once (__DIR__ . '/Lib/Qiniu.php');
require_once (__DIR__ . '/Lib/Redis_CONST.php');
require_once (__DIR__ . '/Lib/Game.php');


class Base_Model
{
	
	/*
		实例化mmsql
	*/
	public function initMysql()
	{
		return \Module\DB\Mysql::instance("DB");
	}
	
	/*
		close mmsql
	*/
	public function closeMysql()
	{
		return \Module\DB\Mysql::close("DB");
	}
	
	
	/*
		写日志
	*/
	public function logMessage($level,$content)
	{
		$this->writeLog($content);
	}
	/*
		写日志
	*/
	public function writeLog($content)
	{
		$file = Config::Log_Dir. date("Y-m-d").'.log';
	    print_r($content);return;	
		file_put_contents($file, '['.date("H:i:s").'] '. $content . "\n",FILE_APPEND);
	}
}

?>