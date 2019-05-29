<?php

require_once __DIR__ . '/timer.class.model.php';
require_once dirname(dirname(__DIR__)) . '/base.class.model.php';
class Dispatcher_Timer
{
	
	/*
		根据操作码分发操作
	*/
	public static function dispatcherOpt($req_ary)
	{
		//$Base_Model = new Base_Model();
		
		if(isset($req_ary['operation']))
		{
			switch($req_ary['operation'])
			{
				/*************
					server模块
				**************/
				case 'BuildTimer':		//创建timer
					$Timer_Model = new Timer_Model();
					$result = $Timer_Model->buildTimer($req_ary);
					break;
				case 'DeleteTimer':		//释放timer
					$Timer_Model = new Timer_Model();
					$result = $Timer_Model->deleteTimer($req_ary);
					break;
					
				default:
					$result = OPT_CONST::MISSING_OPERATION;	//数据格式不正确，缺少操作码
					break;
			}
			
			//关闭数据库
			//@$Base_Model->closeMysql();
			
			//self::writeLog($req_ary['operation']);
			
			return $result;
		}
		else
		{
			return OPT_CONST::MISSING_OPERATION;	//缺少操作码	
		}
	}
	
	/*
		写日志
	*/
	public static function writeLog($content)
	{
		$content = date("Y-m-d H:i:s").' '.$content;
		
		$log_filename = 'operation_'.date("Y-m-d").'.log';
        $file = Config::Log_Dir.$log_filename;

        file_put_contents($file, $content,FILE_APPEND);
		 
		file_put_contents($file, "\n",FILE_APPEND);
	}
	
	
}