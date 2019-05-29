<?php

use \GatewayWorker\Lib\Gateway;

require_once dirname(__DIR__) . '/base.class.model.php';
class Verification_Model extends Base_Model
{
	

	/*
		单例，返回当前类的静态对象
	*/
	public static function getModelObject()
	{
		static $modelObject;
		if(is_object($modelObject))
		{
			return $modelObject;
		}
		else
		{
			$modelObject = new self();
			
			return $modelObject;
		}
	}



	public function __construct()
	{
		//self::getSocketObject($socket_name,$is_init=1);
	}

	
    /**
     * 获取实例
     * @param string $config_name
     * @throws \Exception
     */
    public static function checkRequestSession($account_id=-1,$session="")
    {   
	return true;
        $dealer_num = Config::Dealer_Num;
        //$type = Game::Game_Type;
        $type = "";
        $ver_code = Config::Verification_Code;

        $check_session = base64_encode(md5($account_id.$dealer_num.$ver_code.$type));

        //var_dump($session);
        //var_dump($check_session);

        if($session != $check_session)
        {
            self::writeSessionLog("function(checkRequestSession):session error: account:".$account_id.";session:".$session.";checksession:".$check_session." in file".__FILE__." on Line ".__LINE__);

            $msg = json_encode(array("result"=>"-201","result_message"=>"请求错误")); 
            Gateway::sendToCurrentClient($msg);

            //关闭当前链接
            //Gateway::closeCurrentClient();
            return false;
        }

        return true;
    }

    /*
		写日志
	*/
	public static function writeSessionLog($content)
	{
      
		$content = date("Y-m-d H:i:s").' '.$content;
		  print_r($content);return;
		$log_filename = "session_".date("Y-m-d").'.log';
		 
		$file = Config::Log_Dir.$log_filename;
		 
		file_put_contents($file, $content,FILE_APPEND);
		 
		file_put_contents($file, "\n",FILE_APPEND);
	}
}
