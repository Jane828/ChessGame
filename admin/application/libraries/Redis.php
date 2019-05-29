<?php

require_once(__DIR__.'/predis/autoload.php');

class Redis
{

	private static $redis;

	/*
		单例，返回当前类的静态对象
	*/
	public static function getInstance()
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
		self::$redis = $this->redisAuth();
	}



	/*-------------通用方法--------------*/

	/*
		实例化redis
	*/
	public function redisAuth(){
//	    $redis_config =
        $server  =  array (
            'host'      =>  '192.168.85.100' ,
            'port'      =>  6379 ,
            'database'  =>  0
        );
        $client = new \Predis\Client($server);
		return $client;
	}


	public function setHash($key, $value = ""){
	    self::$redis->set($key,$value);
    }
	
}