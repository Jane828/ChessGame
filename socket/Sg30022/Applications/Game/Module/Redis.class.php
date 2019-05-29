<?php

require_once dirname(__DIR__) . '/base.class.model.php';

require_once(__DIR__.'/../../../../vendor/predis/predis/autoload.php');
predis\autoloader::register();//从上面的文件直接引用下来的方法

class Redis_Model extends Base_Model
{
	
	private static $__redisAuth;
	private static $__redisAuthLogs;
	
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

	public static function getModelObjectLogs()
	{
		static $logObject;
		if(is_object($logObject))
		{
			return $logObject;
		}
		else
		{
			$logObject = new self();
			
			return $logObject;
		}
	}
	
	public function __construct()
	{
		$redisDB = Config::Redis_Database;
		self::$__redisAuth = $this->redisAuth($redisDB);

		//实例化日志对象
		$redisDBLogs = Config::Redis_Database_Logs;
		self::$__redisAuthLogs = $this->redisAuthLogs($redisDBLogs);
	}
	
	
	
	/*-------------通用方法--------------*/
	
	/*
		实例化redis
	*/
	public function redisAuth($redisDB = G_CONST::EMPTY_STRING)
	{
		if($redisDB === G_CONST::EMPTY_STRING)
		{
			$this->logMessage('error', "function(redisAuth):redisDB is empty string"." in file".__FILE__." on Line ".__LINE__);
			return OPT_CONST::DATA_NONEXISTENT;
		}
		
		$Client = new Predis\Client(Config::Redis_HostPort);

		if(Config::Redis_Password){
			$pwdAuth = $Client->auth(Config::Redis_Password);
		}
		
		
		$dbAuth = $Client->select($redisDB);
		
		return $Client;
	}
	/*
		实例化redis 日志
	*/
	public function redisAuthLogs($redisDB = G_CONST::EMPTY_STRING)
	{
		if($redisDB === G_CONST::EMPTY_STRING)
		{
			$this->logMessage('error', "function(redisAuthLogs):redisDB is empty string"." in file".__FILE__." on Line ".__LINE__);
			return OPT_CONST::DATA_NONEXISTENT;
		}
	
		$Client = new Predis\Client(Config::Redis_HostPort_Logs);
		
		if(Config::Redis_Password_Logs){
			$pwdAuth = $Client->auth(Config::Redis_Password_Logs);
		}
		$dbAuth = $Client->select($redisDB);
		return $Client;
	}
	
	public function pingRedisAuth()
	{
		$redisAuth = self::$__redisAuth;
		
		if(!is_object($redisAuth))
		{
			$this->logMessage('error', "function(pingRedisAuth):redisAuth is not an object"." in file".__FILE__." on Line ".__LINE__);
			
			$redisDB = Config::Redis_Database;
			$redisAuth = $this->redisAuth($redisDB);
			self::$__redisAuth = $redisAuth;
		}
		
		$redisResult = $redisAuth->ping();
		if(is_object($redisResult) && $redisResult->getPayload() == "PONG")
		{
			//$this->logMessage('error', "function(pingRedisAuth):redisAuth ping success"." in file".__FILE__." on Line ".__LINE__);
		}
		else
		{
			$this->logMessage('error', "function(pingRedisAuth):redisAuth is not connect"." in file".__FILE__." on Line ".__LINE__);
			
			$redisDB = Config::Redis_Database;
			self::$__redisAuth = $this->redisAuth($redisDB);
		}
		
		return $redisAuth;
	}

	public function pingRedisAuthLogs()
	{
		$redisAuth = self::$__redisAuthLogs;

		return $redisAuth;
		
		if(!is_object($redisAuth))
		{
			$this->logMessage('error', "function(pingRedisAuthLogs):redisAuth is not an object"." in file".__FILE__." on Line ".__LINE__);
			
			$redisDB = Config::Redis_Database_Logs;
			$redisAuth = $this->redisAuthLogs($redisDB);
			self::$__redisAuth = $redisAuth;
		}
		
		$redisResult = $redisAuth->ping();
		if(is_object($redisResult) && $redisResult->getPayload() == "PONG")
		{
			//$this->logMessage('error', "function(pingRedisAuth):redisAuth ping success"." in file".__FILE__." on Line ".__LINE__);
		}
		else
		{
			$this->logMessage('error', "function(pingRedisAuthLogs):redisAuth is not connect"." in file".__FILE__." on Line ".__LINE__);
			
			$redisDB = Config::Redis_Database_Logs;
			self::$__redisAuth = $this->redisAuthLogs($redisDB);
		}
		
		return $redisAuth;
	}
	
	/*
		key exist
		
		判断key是否存在
	*/
	public function existsKey($key = G_CONST::EMPTY_STRING)
	{
		$redisAuth = $this->pingRedisAuth();
		
		if($redisAuth === G_CONST::EMPTY_STRING || !is_object($redisAuth))
		{
			$this->logMessage('error', "function(existsKey):redisAuth is empty string"." in file".__FILE__." on Line ".__LINE__);
			return OPT_CONST::DATA_NONEXISTENT;
		}
		if($key === G_CONST::EMPTY_STRING)
		{
			$this->logMessage('error', "function(existsKey):key is empty string"." in file".__FILE__." on Line ".__LINE__);
			return OPT_CONST::DATA_NONEXISTENT;
		}
		
		$redisResult = $redisAuth->exists($key);
		if($redisResult)
		{
			return Redis_CONST::Key_Exists;
		}
		else
		{
			return Redis_CONST::Key_Nonexistent;
		}
	}
	
	/*
		key delete
		
		删除KEY
	*/
	public function deleteKey($key = G_CONST::EMPTY_STRING)
	{
		$redisAuth = $this->pingRedisAuth();
		
		if($redisAuth === G_CONST::EMPTY_STRING || !is_object($redisAuth))
		{
			$this->logMessage('error', "function(deleteKey):redisAuth is empty string"." in file".__FILE__." on Line ".__LINE__);
			return OPT_CONST::DATA_NONEXISTENT;
		}
		if($key === G_CONST::EMPTY_STRING)
		{
			$this->logMessage('error', "function(deleteKey):key is empty string"." in file".__FILE__." on Line ".__LINE__);
			return OPT_CONST::DATA_NONEXISTENT;
		}
		
		$redisResult = $redisAuth->del($key);
			
		//$this->logMessage('error', "function(deleteKey):$redisResult"." in file".__FILE__." on Line ".__LINE__);
		return $redisResult;
	}
	
	
	/*
		key expire
		
		设置KEY有效期
	*/
	public function expireKey($key = G_CONST::EMPTY_STRING,$time = 0)
	{
		$redisAuth = $this->pingRedisAuth();
		
		if($redisAuth === G_CONST::EMPTY_STRING || !is_object($redisAuth))
		{
			$this->logMessage('error', "function(expireKey):redisAuth is empty string"." in file".__FILE__." on Line ".__LINE__);
			return OPT_CONST::DATA_NONEXISTENT;
		}
		if($key === G_CONST::EMPTY_STRING)
		{
			$this->logMessage('error', "function(expireKey):key is empty string"." in file".__FILE__." on Line ".__LINE__);
			return OPT_CONST::DATA_NONEXISTENT;
		}
		
		$redisResult = $redisAuth->expire($key,$time); 

		return $redisResult;
	}
	
	
	/*-------------string方法--------------*/
	
	/*
		string	get
		
		根据key获取value
	*/
	public function getStringValue($key = G_CONST::EMPTY_STRING)
	{
		$redisAuth = $this->pingRedisAuth();
		
		if($redisAuth === G_CONST::EMPTY_STRING || !is_object($redisAuth))
		{
			$this->logMessage('error', "function(getStringValue):redisAuth is empty string"." in file".__FILE__." on Line ".__LINE__);
			return OPT_CONST::DATA_NONEXISTENT;
		}
		if($key === G_CONST::EMPTY_STRING)
		{
			$this->logMessage('error', "function(getStringValue):key is empty string"." in file".__FILE__." on Line ".__LINE__);
			return OPT_CONST::DATA_NONEXISTENT;
		}
		
		//$redisAuth = self::redisAuth($redisDB);
		
		//if(!is_object($redisAuth))
		//{
		//	return OPT_CONST::DATA_NONEXISTENT;
		//}
		if($this->existsKey($key) == Redis_CONST::Key_Exists)
		{
			$value = $redisAuth->get($key);
		
			return $value;
		}
		else
		{
			return Redis_CONST::FAILED;
		}
	}
	
	
	/*
		string	mget
		
		根据key获取value
	*/
	public function mgetStringValue($mkv = G_CONST::EMPTY_STRING)
	{
		$redisAuth = $this->pingRedisAuth();
		
		if($redisAuth === G_CONST::EMPTY_STRING || !is_object($redisAuth))
		{
			$this->logMessage('error', "function(msetStringValue):redisAuth is empty string"." in file".__FILE__." on Line ".__LINE__);
			return OPT_CONST::DATA_NONEXISTENT;
		}
		if($mkv == G_CONST::EMPTY_STRING || !is_array($mkv))
		{
			$this->logMessage('error', "function(msetStringValue):mkv is not an array"." in file".__FILE__." on Line ".__LINE__);
			return OPT_CONST::DATA_NONEXISTENT;
		}
		
		$value = $redisAuth -> mget ( $mkv ) ;  //存储多个key对应的value
		
		return $value;
	}
	
	
	/*
		string	mset
		
		根据key设置value
	*/
	public function msetStringValue($mkv = G_CONST::EMPTY_STRING)
	{
		$redisAuth = $this->pingRedisAuth();
		
		if($redisAuth === G_CONST::EMPTY_STRING || !is_object($redisAuth))
		{
			$this->logMessage('error', "function(msetStringValue):redisAuth is empty string"." in file".__FILE__." on Line ".__LINE__);
			return OPT_CONST::DATA_NONEXISTENT;
		}
		if($mkv === G_CONST::EMPTY_STRING || !is_array($mkv))
		{
			$this->logMessage('error', "function(msetStringValue):mkv is not an array"." in file".__FILE__." on Line ".__LINE__);
			return OPT_CONST::DATA_NONEXISTENT;
		}
		
		$redisAuth->mset($mkv);  //存储多个key对应的value
		
		return OPT_CONST::SUCCESS;
	}
	
	
	/*
		string	incrby
		
		将 key 所储存的值加上增量 increment 
	*/
	public function incrbyStringValue($key = G_CONST::EMPTY_STRING,$increment = 0)
	{
		$redisAuth = $this->pingRedisAuth();
		
		if($redisAuth === G_CONST::EMPTY_STRING || !is_object($redisAuth))
		{
			$this->logMessage('error', "function(msetStringValue):redisAuth is empty string"." in file".__FILE__." on Line ".__LINE__);
			return OPT_CONST::DATA_NONEXISTENT;
		}
		if($key == G_CONST::EMPTY_STRING)
		{
			$this->logMessage('error', "function(msetStringValue):mkv is not an array"." in file".__FILE__." on Line ".__LINE__);
			return OPT_CONST::DATA_NONEXISTENT;
		}
		
		$result_result = $redisAuth->incrby($key,$increment); //foo为59
		return $result_result;
	}
	
	
	/*-------------list方法--------------*/
	
	/*
		list  llen
		
		获取队列值的数量
	*/
	public function llenList($list = G_CONST::EMPTY_STRING)
	{
		$redisAuth = $this->pingRedisAuth();
		
		if($redisAuth === G_CONST::EMPTY_STRING || !is_object($redisAuth))
		{
			$this->logMessage('error', "function(llenList):redisAuth is empty string"." in file".__FILE__." on Line ".__LINE__);
			return OPT_CONST::DATA_NONEXISTENT;
		}
		if($list === G_CONST::EMPTY_STRING)
		{
			$this->logMessage('error', "function(llenList):list is empty string"." in file".__FILE__." on Line ".__LINE__);
			return OPT_CONST::DATA_NONEXISTENT;
		}
		
		
		$redisResult =  $redisAuth->llen($list);
		
		return $redisResult;
	}
	
	
	/*
		list  lpushx/lpush/rpushx/rpush
		
		在队列左方插入数据
	*/
	public function pushList($is_rpush=G_CONST::IS_TRUE,$is_pushx=G_CONST::IS_TRUE,$list = G_CONST::EMPTY_STRING,$mkv = G_CONST::EMPTY_STRING)
	{
		$redisAuth = $this->pingRedisAuth();
		
		if($redisAuth === G_CONST::EMPTY_STRING || !is_object($redisAuth))
		{
			$this->logMessage('error', "function(pushList):redisDB is empty string"." in file".__FILE__." on Line ".__LINE__);
			return OPT_CONST::DATA_NONEXISTENT;
		}
		if($list === G_CONST::EMPTY_STRING)
		{
			$this->logMessage('error', "function(pushList):list is empty string"." in file".__FILE__." on Line ".__LINE__);
			return OPT_CONST::DATA_NONEXISTENT;
		}
		if($mkv === G_CONST::EMPTY_STRING || !is_array($mkv))
		{
			$this->logMessage('error', "function(pushList):mkv is not an array"." in file".__FILE__." on Line ".__LINE__);
			return OPT_CONST::DATA_NONEXISTENT;
		}
		
		foreach($mkv as $value)
		{
			if(trim($value) === G_CONST::EMPTY_STRING)
			{
				continue;
			}
			if($is_pushx == G_CONST::IS_TRUE)
			{
				if($is_rpush == G_CONST::IS_TRUE)
				{
					$redisResult = $redisAuth->rpushx($list,$value) ;  //在(已存在)队列右方插入值
				}
				else
				{
					$redisResult = $redisAuth->lpushx($list,$value) ;  //在(已存在)队列左方插入值	
				}
			}
			else
			{
				if($is_rpush == G_CONST::IS_TRUE)
				{
					$redisResult = $redisAuth->rpush($list,$value) ;  //在(不存在则新建)列右方插入值
				}
				else
				{
					$redisResult = $redisAuth->lpush($list,$value) ;  //在(不存在则新建)队列左方插入值	
				}
			}
		}
		return OPT_CONST::SUCCESS;
	}
	
	
	/*
		list  rpop/lpop
		
		在队列右(左)方弹出并删除值
	*/
	public function popList($is_rpop=G_CONST::IS_TRUE,$list = G_CONST::EMPTY_STRING,$count = G_CONST::EMPTY_STRING)
	{
		$redisAuth = $this->pingRedisAuth();
		
		if($redisAuth === G_CONST::EMPTY_STRING || !is_object($redisAuth))
		{
			$this->logMessage('error', "function(popList):redisAuth is empty string"." in file".__FILE__." on Line ".__LINE__);
			return OPT_CONST::DATA_NONEXISTENT;
		}
		if($list === G_CONST::EMPTY_STRING)
		{
			$this->logMessage('error', "function(popList):list is empty string"." in file".__FILE__." on Line ".__LINE__);
			return OPT_CONST::DATA_NONEXISTENT;
		}
		if($count === G_CONST::EMPTY_STRING || !is_numeric($count))
		{
			$this->logMessage('error', "function(popList):count is not an number"." in file".__FILE__." on Line ".__LINE__);
			return OPT_CONST::DATA_NONEXISTENT;
		}
		
		$result = array();
		
		for($i=0;$i<$count;$i++)
		{
			if($is_rpop == G_CONST::IS_TRUE)
			{
				$redisResult = $redisAuth->rpop($list);	//右方弹出	
			}
			else
			{
				$redisResult = $redisAuth->lpop($list);	//左方弹出
			}
			$result[] = $redisResult;
		}
		
		return $result;
	}


	/*
		list rotation  rpoplpush
		右边出   左边进		
	*/
	public function rotationList($list = G_CONST::EMPTY_STRING)
	{
		$redisAuth = $this->pingRedisAuth();
		
		if($redisAuth === G_CONST::EMPTY_STRING || !is_object($redisAuth))
		{
			$this->logMessage('error', "function(pushList):redisDB is empty string"." in file".__FILE__." on Line ".__LINE__);
			return OPT_CONST::DATA_NONEXISTENT;
		}
		if($list === G_CONST::EMPTY_STRING)
		{
			$this->logMessage('error', "function(pushList):list is empty string"." in file".__FILE__." on Line ".__LINE__);
			return OPT_CONST::DATA_NONEXISTENT;
		}
	
		$redisResult = $redisAuth->rpoplpush($list,$list) ;  //右边出   左边进
		
		return $redisResult;
	}
	
	
	/*
		list  lrange
		
		在队列左方返回区间元素
	*/
	public function lrangeList($list = G_CONST::EMPTY_STRING,$start = 0,$stop = -1)
	{
		$redisAuth = $this->pingRedisAuth();
		
		if($redisAuth === G_CONST::EMPTY_STRING || !is_object($redisAuth))
		{
			$this->logMessage('error', "function(popList):redisAuth is empty string"." in file".__FILE__." on Line ".__LINE__);
			return OPT_CONST::DATA_NONEXISTENT;
		}
		if($list === G_CONST::EMPTY_STRING)
		{
			$this->logMessage('error', "function(popList):list is empty string"." in file".__FILE__." on Line ".__LINE__);
			return OPT_CONST::DATA_NONEXISTENT;
		}
		
		//$result = array();
		
		$redisResult = $redisAuth->lrange($list,$start,$stop);
		
		return $redisResult;
	}
	
	/*
		list  ltrim
		
		队列修改，保留左边起若干元素，其余删除
	*/
	public function ltrimList($list = G_CONST::EMPTY_STRING,$start = 0,$stop = -1)
	{
		$redisAuth = $this->pingRedisAuth();
		
		if($redisAuth === G_CONST::EMPTY_STRING || !is_object($redisAuth))
		{
			$this->logMessage('error', "function(popList):redisAuth is empty string"." in file".__FILE__." on Line ".__LINE__);
			return OPT_CONST::DATA_NONEXISTENT;
		}
		if($list === G_CONST::EMPTY_STRING)
		{
			$this->logMessage('error', "function(popList):list is empty string"." in file".__FILE__." on Line ".__LINE__);
			return OPT_CONST::DATA_NONEXISTENT;
		}
		
		//ltrim 队列修改，保留左边起若干元素，其余删除
		$redisResult = $redisAuth->ltrim($list,$start,$stop); //保留左边起第0个至第1个元素

		return $redisResult;
	}
	
	
	/*
		list  lrem
		
		队列修改，保留左边起若干元素，其余删除
	*/
	public function lremList($list = G_CONST::EMPTY_STRING,$count = 0,$values = G_CONST::EMPTY_STRING)
	{
		$redisAuth = $this->pingRedisAuth();
		
		if($redisAuth === G_CONST::EMPTY_STRING || !is_object($redisAuth))
		{
			$this->logMessage('error', "function(popList):redisAuth is empty string"." in file".__FILE__." on Line ".__LINE__);
			return OPT_CONST::DATA_NONEXISTENT;
		}
		if($list === G_CONST::EMPTY_STRING)
		{
			$this->logMessage('error', "function(popList):list is empty string"." in file".__FILE__." on Line ".__LINE__);
			return OPT_CONST::DATA_NONEXISTENT;
		}
		if( !is_array($values)){
			$values = array($values);
		}

		$remove_count = 0; 
		foreach ($values as $value) {
			$cnt = $redisAuth->lrem($list,$count,$value);
			$remove_count += $cnt;
		}
		return $remove_count;
	}
	
	
	
	/*-------------sortedset方法--------------*/
	/*
		sortedset zadd
		
		有序集合 增加元素,并设置序号
	*/
	public function zaddSet($set = G_CONST::EMPTY_STRING,$mkv = G_CONST::EMPTY_STRING)
	{
		$redisAuth = $this->pingRedisAuth();
		
		if($redisAuth === G_CONST::EMPTY_STRING || !is_object($redisAuth))
		{
			$this->logMessage('error', "function(zaddSet):redisDB is empty string"." in file".__FILE__." on Line ".__LINE__);
			return OPT_CONST::DATA_NONEXISTENT;
		}
		if($set === G_CONST::EMPTY_STRING)
		{
			$this->logMessage('error', "function(zaddSet):set is empty string"." in file".__FILE__." on Line ".__LINE__);
			return OPT_CONST::DATA_NONEXISTENT;
		}
		if($mkv === G_CONST::EMPTY_STRING || !is_array($mkv))
		{
			$this->logMessage('error', "function(zaddSet):mkv is not an array"." in file".__FILE__." on Line ".__LINE__);
			return OPT_CONST::DATA_NONEXISTENT;
		}
		
		foreach($mkv as $score=>$value)
		{
			$redisResult = $redisAuth->zadd($set,$score,$value);
		}
		
		return OPT_CONST::SUCCESS;
	}
	
	/*
		sortedset zrem
		
		有序集合 删除元素
	*/
	public function zremSet($set = G_CONST::EMPTY_STRING,$mkv = G_CONST::EMPTY_STRING)
	{
		$redisAuth = $this->pingRedisAuth();
		
		if($redisAuth === G_CONST::EMPTY_STRING || !is_object($redisAuth))
		{
			$this->logMessage('error', "function(zremSet):redisDB is empty string"." in file".__FILE__." on Line ".__LINE__);
			return OPT_CONST::DATA_NONEXISTENT;
		}
		if($set === G_CONST::EMPTY_STRING)
		{
			$this->logMessage('error', "function(zremSet):set is empty string"." in file".__FILE__." on Line ".__LINE__);
			return OPT_CONST::DATA_NONEXISTENT;
		}
		if($mkv === G_CONST::EMPTY_STRING || !is_array($mkv))
		{
			$this->logMessage('error', "function(zremSet):mkv is not an array"." in file".__FILE__." on Line ".__LINE__);
			return OPT_CONST::DATA_NONEXISTENT;
		}
		
		foreach($mkv as $zrem)
		{
			$redisResult = $redisAuth->zrem($set,$zrem);
		}
		
		return OPT_CONST::SUCCESS;
	}

    /**
     * 根据分数删除有序集合元素
     * @param string $set
     * @param string $mkv
     * @return int
     */
    public function zremSetbyscore($set = G_CONST::EMPTY_STRING,$min = G_CONST::EMPTY_STRING,$max = G_CONST::EMPTY_STRING)
    {
        $redisAuth = $this->pingRedisAuth();

        if($redisAuth === G_CONST::EMPTY_STRING || !is_object($redisAuth))
        {
            $this->logMessage('error', "function(zremSetbyscore):redisDB is empty string"." in file".__FILE__." on Line ".__LINE__);
            return OPT_CONST::DATA_NONEXISTENT;
        }
        if($set === G_CONST::EMPTY_STRING)
        {
            $this->logMessage('error', "function(zremSetbyscore):set is empty string"." in file".__FILE__." on Line ".__LINE__);
            return OPT_CONST::DATA_NONEXISTENT;
        }
        if($min === G_CONST::EMPTY_STRING)
        {
            $this->logMessage('error', "function(zremSetbyscore):min is empty string"." in file".__FILE__." on Line ".__LINE__);
            return OPT_CONST::DATA_NONEXISTENT;
        }
        if($max === G_CONST::EMPTY_STRING)
        {
            $this->logMessage('error', "function(zremSetbyscore):max is empty string"." in file".__FILE__." on Line ".__LINE__);
            return OPT_CONST::DATA_NONEXISTENT;
        }

        $redisResult = $redisAuth->zremrangebyscore($set, $min, $max);

        return OPT_CONST::SUCCESS;
    }

	/*
		sortedset zcount
		
		有序集合 获取元素总数
	*/
	public function zcountSet($set = G_CONST::EMPTY_STRING,$min = G_CONST::EMPTY_STRING,$max = G_CONST::EMPTY_STRING)
	{
		$redisAuth = $this->pingRedisAuth();
		
		if($redisAuth === G_CONST::EMPTY_STRING || !is_object($redisAuth))
		{
			$this->logMessage('error', "function(zcountSet):redisDB is empty string"." in file".__FILE__." on Line ".__LINE__);
			return OPT_CONST::DATA_NONEXISTENT;
		}
		if($set === G_CONST::EMPTY_STRING)
		{
			$this->logMessage('error', "function(zcountSet):set is empty string"." in file".__FILE__." on Line ".__LINE__);
			return OPT_CONST::DATA_NONEXISTENT;
		}
		if($min === G_CONST::EMPTY_STRING)
		{
			$this->logMessage('error', "function(zcountSet):min is empty string"." in file".__FILE__." on Line ".__LINE__);
			return OPT_CONST::DATA_NONEXISTENT;
		}
		if($max === G_CONST::EMPTY_STRING)
		{
			$this->logMessage('error', "function(zcountSet):max is empty string"." in file".__FILE__." on Line ".__LINE__);
			return OPT_CONST::DATA_NONEXISTENT;
		}
		
		$redisResult = $redisAuth->zcount($set,$min,$max);
		
		return $redisResult;
	}
	
	/*
		sortedset zunionstore
		
		合集
		
		'aggregate' => 'max'或'min'表示并集后相同的元素是取大值或是取小值
	*/
	public function zunionstoreSet(
								   $mkv = G_CONST::EMPTY_STRING,
								   $objKey = G_CONST::EMPTY_STRING,
								   $aggregate = G_CONST::EMPTY_STRING
								   )
	{
		$redisAuth = $this->pingRedisAuth();
		
		if($redisAuth === G_CONST::EMPTY_STRING || !is_object($redisAuth))
		{
			$this->logMessage('error', "function(zunionstoreSet):redisDB is empty string"." in file".__FILE__." on Line ".__LINE__);
			return Redis_CONST::DATA_NONEXISTENT;
		}
		if($mkv === G_CONST::EMPTY_STRING || !is_array($mkv) || count($mkv) == 0)
		{
			$this->logMessage('error', "function(zunionstoreSet):mkv is not an array"." in file".__FILE__." on Line ".__LINE__);
			return Redis_CONST::DATA_NONEXISTENT;
		}
		if($objKey === G_CONST::EMPTY_STRING)
		{
			$this->logMessage('error', "function(zunionstoreSet):objKey is empty string"." in file".__FILE__." on Line ".__LINE__);
			return Redis_CONST::DATA_NONEXISTENT;
		}
		
		$array = array();
		
		if($aggregate != G_CONST::EMPTY_STRING)
		{
			$array['aggregate'] = $aggregate;
			$redisResult = $redisAuth->zunionstore($objKey,$mkv,$array);
		}
		else
		{
			$redisResult = $redisAuth->zunionstore($objKey,$mkv);
		}
		
		//$this->logMessage('error', "function(zunionstoreSet):objKey : ".$objKey." in file".__FILE__." on Line ".__LINE__);
		
		
		if($redisResult >= 0)
		{
			return Redis_CONST::SUCCESS;
		}
		else
		{
			return Redis_CONST::DATA_NONEXISTENT;
		}
	}
	
	
	/*
		sortedset zrangebyscore / zrevrangebyscord
		
		获取区间内容 （）
		
		max/min  : +inf or -inf
		limit	: 每组数据大小
		offset	: 每组数据开始位置
		
		desc	: 是否倒序
		WITHSCORES	: 是否需要权重值
		
	*/
	public function getSortedSetLimit(
								     $sortedSet = G_CONST::EMPTY_STRING,
								     $min = "-inf",
								     $max = "+inf",
								     $limit = G_CONST::EMPTY_STRING,
								     $offset = G_CONST::EMPTY_STRING,
								     $desc = G_CONST::IS_TRUE,
								     $WITHSCORES = FALSE
								     )
	{
		$redisAuth = $this->pingRedisAuth();
		
		if($redisAuth === G_CONST::EMPTY_STRING || !is_object($redisAuth))
		{
			$this->logMessage('error', "function(getSortedSetLimit):redisDB is empty string"." in file".__FILE__." on Line ".__LINE__);
			return Redis_CONST::DATA_NONEXISTENT;
		}
		if($sortedSet === G_CONST::EMPTY_STRING)
		{
			$this->logMessage('error', "function(getSortedSetLimit):sortedSet is not an array"." in file".__FILE__." on Line ".__LINE__);
			return Redis_CONST::DATA_NONEXISTENT;
		}
		if($min === G_CONST::EMPTY_STRING)
		{
			$this->logMessage('error', "function(getSortedSetLimit):min is empty string"." in file".__FILE__." on Line ".__LINE__);
			return Redis_CONST::DATA_NONEXISTENT;
		}
		if($max === G_CONST::EMPTY_STRING)
		{
			$this->logMessage('error', "function(getSortedSetLimit):max is empty string"." in file".__FILE__." on Line ".__LINE__);
			return Redis_CONST::DATA_NONEXISTENT;
		}
		
		if($offset === G_CONST::EMPTY_STRING || $limit === G_CONST::EMPTY_STRING)
		{
			$where = array('withscores'=>$WITHSCORES);
		}
		else
		{
			$where = array('withscores'=>$WITHSCORES,"limit"=>array($offset,$limit));
		}
		
		
		if($desc === G_CONST::IS_TRUE)
		{
			//$this->logMessage('error', "function(getSortedSetLimit):sortedSet : ".$sortedSet." in file".__FILE__." on Line ".__LINE__);
			//倒序
			$redisResult = $redisAuth->zrevrangebyscore($sortedSet,$max,$min,$where);
			//$this->logMessage('error', "function(getSortedSetLimit):redisResult : ".$redisResult." in file".__FILE__." on Line ".__LINE__);
		}
		else
		{
			//顺序
			$redisResult = $redisAuth->zrangebyscore($sortedSet,$min,$max,$where);
		}
		
		if(is_array($redisResult))
		{
			return $redisResult;
		}
		else
		{
			return Redis_CONST::DATA_NONEXISTENT;
		}
	}
	
	
	
	/*
		sortedset zrangebyscore / zrevrangebyscord
		
		获取区间内容 （）
		
		max/min  : +inf or -inf
		limit	: 每组数据大小
		offset	: 每组数据开始位置
		
		desc	: 是否倒序
		WITHSCORES	: 是否需要权重值
		
	*/
	public function getSortedSetLimitByAry($arrData)
	{
		$redisAuth = $this->pingRedisAuth();
		
		if($redisAuth === G_CONST::EMPTY_STRING || !is_object($redisAuth))
		{
			$this->logMessage('error', "function(getSortedSetLimitByAry):redisDB is empty string"." in file".__FILE__." on Line ".__LINE__);
			return Redis_CONST::DATA_NONEXISTENT;
		}
		
		if(!isset($arrData['key']) || $arrData['key'] === G_CONST::EMPTY_STRING)
		{
			$this->logMessage('error', "function(getSortedSetLimitByAry):sortedSet is not an array"." in file".__FILE__." on Line ".__LINE__);
			return Redis_CONST::DATA_NONEXISTENT;
		}
		
		$min = "-inf";
		$max = "+inf";
		if(isset($arrData['min']))
		{
			$min = $arrData['min'];
		}
		if(isset($arrData['max']))
		{
			$max = $arrData['max'];
		}
		
		$where = array();
		if(isset($arrData['WITHSCORES']))
		{
			$where['WITHSCORES'] = $arrData['WITHSCORES'];
		}
		
		$offset = G_CONST::EMPTY_STRING;
		$limit = G_CONST::EMPTY_STRING;
		if(isset($arrData['offset']))
		{
			$offset = $arrData['offset'];
		}
		if(isset($arrData['limit']))
		{
			$limit = $arrData['limit'];
		}
		if($offset !== G_CONST::EMPTY_STRING && $limit !== G_CONST::EMPTY_STRING)
		{
			$where['limit'] = array($offset,$limit);
		}
		
		$desc =G_CONST::IS_FALSE;
		if(isset($arrData['desc']))
		{
			$desc = $arrData['desc'];
		}
		
		$sortedSet = $arrData['key'];
		
		if($desc === G_CONST::IS_TRUE)
		{
			//$this->logMessage('error', "function(getSortedSetLimit):sortedSet : ".$sortedSet." in file".__FILE__." on Line ".__LINE__);
			//倒序
			$redisResult = $redisAuth->zrevrangebyscore($sortedSet,$max,$min,$where);
			//$this->logMessage('error', "function(getSortedSetLimit):redisResult : ".$redisResult." in file".__FILE__." on Line ".__LINE__);
		}
		else
		{
			//顺序
			$redisResult = $redisAuth->zrangebyscore($sortedSet,$min,$max,$where);
		}
		
		if(is_array($redisResult))
		{
			return $redisResult;
		}
		else
		{
			return Redis_CONST::DATA_NONEXISTENT;
		}
	}
	
	
	/*
		sortedset ZREMRANGEBYRANK 
		
		删除区间内容 （）
		
		
	*/
	public function zemrangebyrankLimit(
								     $sortedSet = G_CONST::EMPTY_STRING,
								     $stop = -1,
								     $start = 0
								     )
	{
		$redisAuth = $this->pingRedisAuth();
		
		if($redisAuth === G_CONST::EMPTY_STRING || !is_object($redisAuth))
		{
			$this->logMessage('error', "function(zemrangebyrankLimit):redisDB is empty string"." in file".__FILE__." on Line ".__LINE__);
			return Redis_CONST::DATA_NONEXISTENT;
		}
		if($sortedSet === G_CONST::EMPTY_STRING)
		{
			$this->logMessage('error', "function(zemrangebyrankLimit):sortedSet is not an array"." in file".__FILE__." on Line ".__LINE__);
			return Redis_CONST::DATA_NONEXISTENT;
		}
		
		$redisResult = $redisAuth->zremrangebyrank($sortedSet,$start,$stop); //删除位置为0-10的元素,返回删除的元素个数2
		//$this->logMessage('error', "function(zemrangebyrankLimit):$redisResult"." in file".__FILE__." on Line ".__LINE__);
		
		return OPT_CONST::SUCCESS;
	}
	
	/*
		sortedset ZSCORE
		
		根据value 获取 score
	*/
	public function getZscore($sortedSet = G_CONST::EMPTY_STRING,$value = G_CONST::EMPTY_STRING)
	{
		$redisAuth = $this->pingRedisAuth();
		
		if($redisAuth === G_CONST::EMPTY_STRING || !is_object($redisAuth))
		{
			$this->logMessage('error', "function(zemrangebyrankLimit):redisDB is empty string"." in file".__FILE__." on Line ".__LINE__);
			return Redis_CONST::DATA_NONEXISTENT;
		}
		if($sortedSet === G_CONST::EMPTY_STRING)
		{
			$this->logMessage('error', "function(zemrangebyrankLimit):sortedSet is not an array"." in file".__FILE__." on Line ".__LINE__);
			return Redis_CONST::DATA_NONEXISTENT;
		}
		
		$redisResult = $redisAuth->zscore($sortedSet,$value);
		
		if($redisResult == null)
		{
			return Redis_CONST::DATA_NONEXISTENT;
		}
		else
		{
			return $redisResult;
		}
	}


    /*
        sortedset ZREVRANGE ZRANGE

        返回有序集 key 中，指定区间内的成员
    */
	public function zrangeSet($arrData)
	{
		$redisAuth = $this->pingRedisAuth();
		
		if($redisAuth === G_CONST::EMPTY_STRING || !is_object($redisAuth))
		{
			$this->logMessage('error', "function(zrevrangeSet):redisDB is empty string"." in file".__FILE__." on Line ".__LINE__);
			return Redis_CONST::DATA_NONEXISTENT;
		}
		
		$is_zrange = $arrData['is_zrange'];
		$sortedSet = $arrData['key'];
		$start = $arrData['start'];
		$stop = $arrData['stop'];
		
		if($is_zrange)
		{
			if(isset($arrData['WITHSCORES']))
			{
				$redisResult = $redisAuth->zrange($sortedSet,$start,$stop,"WITHSCORES");
			}
			else
			{
				$redisResult = $redisAuth->zrange($sortedSet,$start,$stop);
			}
		}
		else
		{
			if(isset($arrData['WITHSCORES']))
			{
				$redisResult = $redisAuth->zrevrange($sortedSet,$start,$stop,"WITHSCORES");
			}
			else
			{
				$redisResult = $redisAuth->zrevrange($sortedSet,$start,$stop);
			}
		}
		
		if($redisResult == null)
		{
			return Redis_CONST::DATA_NONEXISTENT;
		}
		else
		{
			return $redisResult;
		}
	}
	
	
	
	/*-------------set方法--------------*/
	/*
		set sadd
		
		集合 增加元素
	*/
	public function saddSet($set = G_CONST::EMPTY_STRING,$mkv = G_CONST::EMPTY_STRING)
	{
		$redisAuth = $this->pingRedisAuth();
		
		if($redisAuth === G_CONST::EMPTY_STRING || !is_object($redisAuth))
		{
			$this->logMessage('error', "function(saddSet):redisDB is empty string"." in file".__FILE__." on Line ".__LINE__);
			return OPT_CONST::DATA_NONEXISTENT;
		}
		if($set === G_CONST::EMPTY_STRING)
		{
			$this->logMessage('error', "function(saddSet):set is empty string"." in file".__FILE__." on Line ".__LINE__);
			return OPT_CONST::DATA_NONEXISTENT;
		}
		if($mkv === G_CONST::EMPTY_STRING || !is_array($mkv))
		{
			$this->logMessage('error', "function(saddSet):mkv is not an array"." in file".__FILE__." on Line ".__LINE__);
			return OPT_CONST::DATA_NONEXISTENT;
		}
		
		foreach($mkv as $value)
		{
			$redisResult = $redisAuth->sadd($set,$value);
			
			//$this->logMessage('error', "function(saddSet):set : ".$set." in file".__FILE__." on Line ".__LINE__);
			//$this->logMessage('error', "function(saddSet):value : ".$value." in file".__FILE__." on Line ".__LINE__);
		}
		
		return OPT_CONST::SUCCESS;
	}
	
	
	/*
		set srem
		
		集合 移除指定元素
	*/
	public function sremSet($set = G_CONST::EMPTY_STRING,$mkv = G_CONST::EMPTY_STRING)
	{
		$redisAuth = $this->pingRedisAuth();
		
		if($redisAuth === G_CONST::EMPTY_STRING || !is_object($redisAuth))
		{
			$this->logMessage('error', "function(sremSet):redisDB is empty string"." in file".__FILE__." on Line ".__LINE__);
			return OPT_CONST::DATA_NONEXISTENT;
		}
		if($set === G_CONST::EMPTY_STRING)
		{
			$this->logMessage('error', "function(sremSet):set is empty string"." in file".__FILE__." on Line ".__LINE__);
			return OPT_CONST::DATA_NONEXISTENT;
		}
		if($mkv === G_CONST::EMPTY_STRING || !is_array($mkv))
		{
			$this->logMessage('error', "function(sremSet):mkv is not an array"." in file".__FILE__." on Line ".__LINE__);
			return OPT_CONST::DATA_NONEXISTENT;
		}
		
		foreach($mkv as $value)
		{
			$redisResult = $redisAuth->srem($set,$value);
		}
		
		return OPT_CONST::SUCCESS;
	}
	
	
	
	/*
		set smembers
		
		集合 获取集合所有元素
	*/
	public function smembersSet($set = G_CONST::EMPTY_STRING)
	{
		$redisAuth = $this->pingRedisAuth();
		
		if($redisAuth === G_CONST::EMPTY_STRING || !is_object($redisAuth))
		{
			$this->logMessage('error', "function(smembersSet):redisDB is empty string"." in file".__FILE__." on Line ".__LINE__);
			return OPT_CONST::DATA_NONEXISTENT;
		}
		if($set === G_CONST::EMPTY_STRING)
		{
			$this->logMessage('error', "function(smembersSet):set is empty string"." in file".__FILE__." on Line ".__LINE__);
			return OPT_CONST::DATA_NONEXISTENT;
		}
		
		$redisResult = $redisAuth->smembers($set);
		
		return $redisResult;
	}

	/*
		scard
		返回集合的基数(集合中元素的数量)。
	*/
	public function scard($set = G_CONST::EMPTY_STRING)
	{
		$redisAuth = $this->pingRedisAuth();
		
		if($redisAuth === G_CONST::EMPTY_STRING || !is_object($redisAuth))
		{
			$this->logMessage('error', "function(smembersSet):redisDB is empty string"." in file".__FILE__." on Line ".__LINE__);
			return OPT_CONST::DATA_NONEXISTENT;
		}
		$redisResult = $redisAuth->scard($set);
		return $redisResult;
	}
	
	
	/*
		set sismember
		
		集合 是否集合的元素
	*/
	public function sismemberSet($set = G_CONST::EMPTY_STRING,$member = G_CONST::EMPTY_STRING)
	{
		$redisAuth = $this->pingRedisAuth();
		
		if($redisAuth === G_CONST::EMPTY_STRING || !is_object($redisAuth))
		{
			$this->logMessage('error', "function(sismemberSet):redisDB is empty string"." in file".__FILE__." on Line ".__LINE__);
			return Redis_CONST::DATA_NONEXISTENT;
		}
		if($set === G_CONST::EMPTY_STRING)
		{
			$this->logMessage('error', "function(sismemberSet):set is empty string"." in file".__FILE__." on Line ".__LINE__);
			return Redis_CONST::DATA_NONEXISTENT;
		}
		if($set === G_CONST::EMPTY_STRING)
		{
			$this->logMessage('error', "function(sismemberSet):member is empty string"." in file".__FILE__." on Line ".__LINE__);
			return Redis_CONST::DATA_NONEXISTENT;
		}
		
		$redisResult = $redisAuth->sismember($set,$member);		//true or false
		
		return $redisResult;
	}
	
	
	/*
		set smove
		
		集合 移动当前set表的指定元素到另一个set表
	*/
	public function smoveSet($fromSet = G_CONST::EMPTY_STRING,$toSet = G_CONST::EMPTY_STRING,$mkv = G_CONST::EMPTY_STRING)
	{
		$redisAuth = $this->pingRedisAuth();
		
		if($redisAuth === G_CONST::EMPTY_STRING || !is_object($redisAuth))
		{
			$this->logMessage('error', "function(smoveSet):redisDB is empty string"." in file".__FILE__." on Line ".__LINE__);
			return OPT_CONST::DATA_NONEXISTENT;
		}
		if($fromSet === G_CONST::EMPTY_STRING)
		{
			$this->logMessage('error', "function(smoveSet):fromSet is empty string"." in file".__FILE__." on Line ".__LINE__);
			return OPT_CONST::DATA_NONEXISTENT;
		}
		if($toSet === G_CONST::EMPTY_STRING)
		{
			$this->logMessage('error', "function(smoveSet):toSet is empty string"." in file".__FILE__." on Line ".__LINE__);
			return OPT_CONST::DATA_NONEXISTENT;
		}
		if($mkv === G_CONST::EMPTY_STRING || !is_array($mkv))
		{
			$this->logMessage('error', "function(saddSet):mkv is not an array"." in file".__FILE__." on Line ".__LINE__);
			return OPT_CONST::DATA_NONEXISTENT;
		}
		
		foreach($mkv as $member)
		{
			$redisResult = $redisAuth->smove($fromSet,$toSet,$member);
		}
		
		return OPT_CONST::SUCCESS;
	}
	
	
	/*
		set sinter、sunion、sdiff
		
		交集、合集、差集
	*/
	public function mutualSet($type = G_CONST::EMPTY_STRING,$set_1 = G_CONST::EMPTY_STRING,$set_2 = G_CONST::EMPTY_STRING)
	{
		$redisAuth = $this->pingRedisAuth();
		
		if($redisAuth === G_CONST::EMPTY_STRING || !is_object($redisAuth))
		{
			$this->logMessage('error', "function(mutualSet):redisDB is empty string"." in file".__FILE__." on Line ".__LINE__);
			return OPT_CONST::DATA_NONEXISTENT;
		}
		if($type === G_CONST::EMPTY_STRING)
		{
			$this->logMessage('error', "function(mutualSet):type is empty string"." in file".__FILE__." on Line ".__LINE__);
			return OPT_CONST::DATA_NONEXISTENT;
		}
		if($set_1 === G_CONST::EMPTY_STRING)
		{
			$this->logMessage('error', "function(mutualSet):set_1 is empty string"." in file".__FILE__." on Line ".__LINE__);
			return OPT_CONST::DATA_NONEXISTENT;
		}
		if($set_2 === G_CONST::EMPTY_STRING)
		{
			$this->logMessage('error', "function(mutualSet):set_2 is empty string"." in file".__FILE__." on Line ".__LINE__);
			return OPT_CONST::DATA_NONEXISTENT;
		}
		
		
		if($type == Redis_CONST::UserRel_TempSet_Type_Sinter)
		{
			$redisResult = $redisAuth->sinter($set_1,$set_2) ;  //返回array('ab')
		}
		else if($type == Redis_CONST::UserRel_TempSet_Type_Sunion)
		{
			$redisResult = $redisAuth->sunion($set_1,$set_2) ;  //返回array('ab')	
		}
		else if($type == Redis_CONST::UserRel_TempSet_Type_Sdiff)
		{
			$redisResult = $redisAuth->sdiff($set_1,$set_2) ;  //返回array('ab')	以set_1为准
		}
		else
		{
			$this->logMessage('error', "function(mutualSet):lack of type"." in file".__FILE__." on Line ".__LINE__);
			$redisResult = array();
		}
		
		return $redisResult;
	}
	
	/*
		set sinterstore、sunionstore、sdiffstore
		
		交集、合集、差集
	*/
	public function mutualStoreSet(
							  $type = G_CONST::EMPTY_STRING,
							  $mkv = G_CONST::EMPTY_STRING,
							  $set_temp = G_CONST::EMPTY_STRING)
	{
		$redisAuth = $this->pingRedisAuth();
		
		if($redisAuth === G_CONST::EMPTY_STRING || !is_object($redisAuth))
		{
			$this->logMessage('error', "function(mutualSet):redisDB is empty string"." in file".__FILE__." on Line ".__LINE__);
			return OPT_CONST::DATA_NONEXISTENT;
		}
		if($type === G_CONST::EMPTY_STRING)
		{
			$this->logMessage('error', "function(mutualSet):type is empty string"." in file".__FILE__." on Line ".__LINE__);
			return OPT_CONST::DATA_NONEXISTENT;
		}
		if($mkv === G_CONST::EMPTY_STRING || !is_array($mkv))
		{
			$this->logMessage('error', "function(saddSet):mkv is not an array"." in file".__FILE__." on Line ".__LINE__);
			return OPT_CONST::DATA_NONEXISTENT;
		}
		if($set_temp === G_CONST::EMPTY_STRING)
		{
			$this->logMessage('error', "function(mutualSet):set_temp is empty string"." in file".__FILE__." on Line ".__LINE__);
			return OPT_CONST::DATA_NONEXISTENT;
		}
		
		
		if($type == Redis_CONST::UserRel_TempSet_Type_Sinter)
		{
			$redisResult = $redisAuth->sinterstore($set_temp,$mkv) ;  //返回array('ab')
		}
		else if($type == Redis_CONST::UserRel_TempSet_Type_Sunion)
		{
			$redisResult = $redisAuth->sunionstore($set_temp,$mkv) ;  //返回array('ab')
		}
		else if($type == Redis_CONST::UserRel_TempSet_Type_Sdiff)
		{
			$redisResult = $redisAuth->sdiffstore($set_temp,$mkv) ;  //返回array('ab')
		}
		else
		{
			$this->logMessage('error', "function(mutualSet):lack of type"." in file".__FILE__." on Line ".__LINE__);
			$redisResult = array();
		}
		
		return $redisResult;
	}
	
	
		
	
	/*-------------hash方法--------------*/
	
	/*
		hash hexists
		
		哈希 设置域值
	*/
	public function hexistsField($hash = G_CONST::EMPTY_STRING,$field = G_CONST::EMPTY_STRING)
	{
		$redisAuth = $this->pingRedisAuth();
		
		if($redisAuth === G_CONST::EMPTY_STRING || !is_object($redisAuth))
		{
			$this->logMessage('error', "function(hexistsField):redisAuth is empty string"." in file".__FILE__." on Line ".__LINE__);
			return OPT_CONST::DATA_NONEXISTENT;
		}
		if($hash === G_CONST::EMPTY_STRING)
		{
			$this->logMessage('error', "function(hexistsField):hash is empty string"." in file".__FILE__." on Line ".__LINE__);
			return OPT_CONST::DATA_NONEXISTENT;
		}
		if($field === G_CONST::EMPTY_STRING)
		{
			$this->logMessage('error', "function(hexistsField):field is not an array"." in file".__FILE__." on Line ".__LINE__);
			return OPT_CONST::DATA_NONEXISTENT;
		}
		
		$redisResult = $redisAuth->hexists($hash, $field);
		
		if($redisResult)
		{
			return Redis_CONST::Key_Exists;
		}
		else
		{
			return Redis_CONST::Key_Nonexistent;
		}
	}
	
	/*
		hash hmsetField
		
		哈希 设置域值
		
		$mkv = array ( 'key3' => 'v3' , 'key4' => 'v4' ) 
		
	*/
	public function hmsetField($hash = G_CONST::EMPTY_STRING,$mkv = G_CONST::EMPTY_STRING)
	{
		$redisAuth = $this->pingRedisAuth();
		
		if($redisAuth === G_CONST::EMPTY_STRING || !is_object($redisAuth))
		{
			$this->logMessage('error', "function(hmsetField):redisAuth is empty string"." in file".__FILE__." on Line ".__LINE__);
			return OPT_CONST::DATA_NONEXISTENT;
		}
		if($hash === G_CONST::EMPTY_STRING)
		{
			$this->logMessage('error', "function(hmsetField):hash is empty string"." in file".__FILE__." on Line ".__LINE__);
			return OPT_CONST::DATA_NONEXISTENT;
		}
		if($mkv === G_CONST::EMPTY_STRING || !is_array($mkv))
		{
			$this->logMessage('error', "function(hmsetField):mkv is not an array"." in file".__FILE__." on Line ".__LINE__);
			return OPT_CONST::DATA_NONEXISTENT;
		}
		
		$redisResult = $redisAuth->hmset($hash, $mkv);
		
		return OPT_CONST::SUCCESS;
	}
	
	
	/*
		hash hmget
		
		哈希 设置域值
		
		$mkv = array ( 'key3' , 'key4' )
		
		返回array('v3','v4')
	*/
	public function hmgetField($hash = G_CONST::EMPTY_STRING,$mkv = G_CONST::EMPTY_STRING)
	{
		$redisAuth = $this->pingRedisAuth();
		
		if($redisAuth === G_CONST::EMPTY_STRING || !is_object($redisAuth))
		{
			$this->logMessage('error', "function(hmgetField):redisAuth is empty string"." in file".__FILE__." on Line ".__LINE__);
			return OPT_CONST::DATA_NONEXISTENT;
		}
		if($hash === G_CONST::EMPTY_STRING)
		{
			$this->logMessage('error', "function(hmgetField):hash is empty string"." in file".__FILE__." on Line ".__LINE__);
			return OPT_CONST::DATA_NONEXISTENT;
		}
		if($mkv === G_CONST::EMPTY_STRING || !is_array($mkv))
		{
			$this->logMessage('error', "function(hmgetField):mkv is not an array"." in file".__FILE__." on Line ".__LINE__);
			return OPT_CONST::DATA_NONEXISTENT;
		}
		
		$redisResult = $redisAuth->hmget($hash, $mkv);
		
		if(is_array($redisResult))
		{
			return $redisResult;
		}
		else
		{
			return OPT_CONST::DATA_NONEXISTENT;
		}
	}

	
	/*
		hash hget
		
		哈希 获取域值
		
	*/
	public function hgetField($hash = G_CONST::EMPTY_STRING,$field = G_CONST::EMPTY_STRING)
	{
		$redisAuth = $this->pingRedisAuth();
		
		if($redisAuth === G_CONST::EMPTY_STRING || !is_object($redisAuth))
		{
			$this->logMessage('error', "function(hgetField):redisAuth is empty string"." in file".__FILE__." on Line ".__LINE__);
			return Redis_CONST::DATA_NONEXISTENT;
		}
		if($hash === G_CONST::EMPTY_STRING)
		{
			$this->logMessage('error', "function(hgetField):hash is empty string"." in file".__FILE__." on Line ".__LINE__);
			return Redis_CONST::DATA_NONEXISTENT;
		}
		if($field === G_CONST::EMPTY_STRING)
		{
			$this->logMessage('error', "function(hgetField):field is not an array"." in file".__FILE__." on Line ".__LINE__);
			return Redis_CONST::DATA_NONEXISTENT;
		}
		
		$redisResult = $redisAuth->hget($hash, $field);
		
		if($redisResult == null || trim($redisResult) == G_CONST::EMPTY_STRING)
		{
			return Redis_CONST::DATA_NONEXISTENT;
		}
		else
		{
			return $redisResult;
		}
	}
	
	
	/*
		hash mutil opt
		
		哈希 pipe操作
	*/
	public function hincrbyField($hash = G_CONST::EMPTY_STRING,$field = G_CONST::EMPTY_STRING,$count = 0)
	{
		$redisAuth = $this->pingRedisAuth();
		
		if($redisAuth === G_CONST::EMPTY_STRING || !is_object($redisAuth))
		{
			$this->logMessage('error', "function(hincrbyField):redisAuth is empty string"." in file".__FILE__." on Line ".__LINE__);
			return Redis_CONST::DATA_NONEXISTENT;
		}
		if($hash === G_CONST::EMPTY_STRING)
		{
			$this->logMessage('error', "function(hincrbyField):hash is empty string"." in file".__FILE__." on Line ".__LINE__);
			return Redis_CONST::DATA_NONEXISTENT;
		}
		if($field === G_CONST::EMPTY_STRING)
		{
			$this->logMessage('error', "function(hincrbyField):field is not an array"." in file".__FILE__." on Line ".__LINE__);
			return Redis_CONST::DATA_NONEXISTENT;
		}
		
		$redisResult = $redisAuth->hincrby($hash,$field,$count); //返回结果
		
		// if($redisResult < 0)
		// {
		// 	$abs_count = abs($redisResult);
		// 	$redisResult = $redisAuth->hincrby($hash,$field,$abs_count); 
		// }

		return $redisResult;

	}
	
	
	/*
		hash hvals
		
		哈希 
	*/
	public function hvalsByKey($hash = G_CONST::EMPTY_STRING)
	{
		$redisAuth = $this->pingRedisAuth();
		
		if($redisAuth === G_CONST::EMPTY_STRING || !is_object($redisAuth))
		{
			$this->logMessage('error', "function(hvalsAll):hvalsByKey is empty string"." in file".__FILE__." on Line ".__LINE__);
			return Redis_CONST::DATA_NONEXISTENT;
		}
		if($hash === G_CONST::EMPTY_STRING)
		{
			$this->logMessage('error', "function(hvalsAll):hvalsByKey is empty string"." in file".__FILE__." on Line ".__LINE__);
			return Redis_CONST::DATA_NONEXISTENT;
		}
		
		
		$redisResult = $redisAuth->hvals($hash); //返回array('v1','v2','v3','v4',13)
		
		return $redisResult;
	}
	
	/*
		hash HDEL
		
		哈希 删除域 
	*/
	public function hdelFiled($hash = G_CONST::EMPTY_STRING,$field = G_CONST::EMPTY_STRING)
	{
		$redisAuth = $this->pingRedisAuth();
		
		if($redisAuth === G_CONST::EMPTY_STRING || !is_object($redisAuth))
		{
			$this->logMessage('error', "function(hdelFiled):hvalsByKey is empty string"." in file".__FILE__." on Line ".__LINE__);
			return Redis_CONST::DATA_NONEXISTENT;
		}
		if($hash === G_CONST::EMPTY_STRING)
		{
			$this->logMessage('error', "function(hdelFiled):hash is empty string"." in file".__FILE__." on Line ".__LINE__);
			return Redis_CONST::DATA_NONEXISTENT;
		}
		if($field === G_CONST::EMPTY_STRING)
		{
			$this->logMessage('error', "function(hdelFiled):field is not an array"." in file".__FILE__." on Line ".__LINE__);
			return Redis_CONST::DATA_NONEXISTENT;
		}

		$redisResult = $redisAuth->hdel($hash,$field); //返回true or false
		
		return $redisResult;
	}
	
	
	/*
		hash hgetall
		
		哈希 获取所有域和值
		
	*/
	public function hgetallField($hash = G_CONST::EMPTY_STRING)
	{
		$redisAuth = $this->pingRedisAuth();
		
		if($redisAuth === G_CONST::EMPTY_STRING || !is_object($redisAuth))
		{
			$this->logMessage('error', "function(hgetallField):redisAuth is empty string"." in file".__FILE__." on Line ".__LINE__);
			return Redis_CONST::DATA_NONEXISTENT;
		}
		if($hash === G_CONST::EMPTY_STRING)
		{
			$this->logMessage('error', "function(hgetallField):hash is empty string"." in file".__FILE__." on Line ".__LINE__);
			return Redis_CONST::DATA_NONEXISTENT;
		}
		
		$redisResult = $redisAuth->hgetall($hash); //返回array('key1'=>'v1','key2'=>'v2','key3'=>'v3','key4'=>'v4','key5'=>13)
		
		if($redisResult == null || count($redisResult) == 0)
		{
			return Redis_CONST::DATA_NONEXISTENT;
		}
		else
		{
			return $redisResult;
		}
	}
	
	/********************************
			日志redis function
	********************************/
	/*
		list  lpushx/lpush/rpushx/rpush
		
		在队列左方插入数据
	*/
	public function pushListLogs($is_rpush=G_CONST::IS_TRUE,$is_pushx=G_CONST::IS_TRUE,$list = G_CONST::EMPTY_STRING,$mkv = G_CONST::EMPTY_STRING)
	{
		$redisAuth = $this->pingRedisAuthLogs();
		
		if($redisAuth === G_CONST::EMPTY_STRING || !is_object($redisAuth))
		{
			$this->logMessage('error', "function(pushListLogs):redisDB is empty string"." in file".__FILE__." on Line ".__LINE__);
			return OPT_CONST::DATA_NONEXISTENT;
		}
		if($list === G_CONST::EMPTY_STRING)
		{
			$this->logMessage('error', "function(pushListLogs):list is empty string"." in file".__FILE__." on Line ".__LINE__);
			return OPT_CONST::DATA_NONEXISTENT;
		}
		if($mkv === G_CONST::EMPTY_STRING || !is_array($mkv))
		{
			$this->logMessage('error', "function(pushListLogs):mkv is not an array"." in file".__FILE__." on Line ".__LINE__);
			return OPT_CONST::DATA_NONEXISTENT;
		}
		
		foreach($mkv as $value)
		{
			if(trim($value) === G_CONST::EMPTY_STRING)
			{
				continue;
			}
			if($is_pushx == G_CONST::IS_TRUE)
			{
				if($is_rpush == G_CONST::IS_TRUE)
				{
					$redisResult = $redisAuth->rpushx($list,$value) ;  //在(已存在)队列右方插入值
				}
				else
				{
					$redisResult = $redisAuth->lpushx($list,$value) ;  //在(已存在)队列左方插入值	
				}
			}
			else
			{
				if($is_rpush == G_CONST::IS_TRUE)
				{
					$redisResult = $redisAuth->rpush($list,$value) ;  //在(不存在则新建)列右方插入值
				}
				else
				{
					$redisResult = $redisAuth->lpush($list,$value) ;  //在(不存在则新建)队列左方插入值	
				}
			}
		}
		return OPT_CONST::SUCCESS;
	}
	
}
