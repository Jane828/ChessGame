<?php

use \GatewayWorker\Lib\Gateway;
use \Module\DB;

require_once (__DIR__ . '/Config/Config.php');

require_once (__DIR__ . '/Lib/G_CONST.php');
require_once (__DIR__ . '/Lib/OPT_CONST.php');


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
		$content = date("Y-m-d H:i:s").' '.$content;
		
		$log_filename = date("Y-m-d").'.log';
		 
        $file = Config::Log_Dir.$log_filename;

        file_put_contents($file, $content,FILE_APPEND);
		 
		file_put_contents($file, "\n",FILE_APPEND);
	}
	
	
}
?>