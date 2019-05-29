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
            'host'      =>  "game_redis" ,
            'port'      =>  6379 ,
            'database'  =>  10
        );
        $client = new \Predis\Client($server);
		return $client;
	}

    public function hgetall($key){
        return self::$redis->hgetall($key);
    }

    public function zrange($key, $s_index, $e_index){
        return self::$redis->zRange($key,$s_index,$e_index);
    }

	public function zard($key){
		return self::$redis->zcard($key);
	}

	public function scard($key){
		return self::$redis->scard($key);
	}
}