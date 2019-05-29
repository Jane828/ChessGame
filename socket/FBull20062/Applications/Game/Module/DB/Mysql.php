<?php
/**
 * This file is part of workerman.
 *
 * Licensed under The MIT License
 * For full copyright and license information, please see the MIT-LICENSE.txt
 * Redistributions of files must retain the above copyright notice.
 *
 * @author walkor<walkor@workerman.net>
 * @copyright walkor<walkor@workerman.net>
 * @link http://www.workerman.net/
 * @license http://www.opensource.org/licenses/mit-license.php MIT License
 */
namespace Module\DB;

use \Module\DB\DbConfig;
use \Module\DB\DbConnection;

/**
 * 数据库类
 */
class Mysql
{
    /**
     * 实例数组
     * @var array
     */
    protected static $instance = array();

    /**
     * 获取实例
     * @param string $config_name
     * @throws \Exception
     */
    public static function instance($config_name)
    {   
        //echo '===========================MySQL__start======',date('Y-m-n H:i:s',time()),"\n";
        if(!isset(\Module\DB\DbConfig::$$config_name))
        {
            //echo "\\Module\DB\\DbConfig::$config_name not set\n";
            throw new \Exception("\\Module\DB\\DbConfig::$config_name not set\n");
        }

        if(empty(self::$instance[$config_name]))
        {
            $config = \Module\DB\DbConfig::$$config_name;
            self::$instance[$config_name] = new \Module\DB\DbConnection($config['host'], $config['port'], $config['user'], $config['password'], $config['dbname'], $config['charset']);
            //echo '===========================MySQL__succeed======',date('Y-m-n H:i:s',time()),"\n";
        }
        return self::$instance[$config_name];
    }

    /**
     * 关闭数据库实例
     * @param string $config_name
     */
    public static function close($config_name)
    {
        if(isset(self::$instance[$config_name]))
        {
            self::$instance[$config_name]->closeConnection();
            self::$instance[$config_name] = null;
        }
    }

    /**
     * 关闭所有数据库实例
     */
    public static function closeAll()
    {
        foreach(self::$instance as $connection)
        {
            $connection->closeConnection();
        }
        self::$instance = array();
    }
}
