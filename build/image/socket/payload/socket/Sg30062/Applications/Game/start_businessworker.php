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
use \Workerman\Worker;
use \Workerman\WebServer;
use \GatewayWorker\Gateway;
use \GatewayWorker\BusinessWorker;
use \Workerman\Autoloader;
require_once __DIR__ . '/Config/Config.php';

/*	IM接口	*/
// bussinessWorker 进程
$worker = new BusinessWorker();
// worker名称
$worker->name = 'W';
// bussinessWorker进程数量
$worker->count = 2;
// 服务注册地址
$worker->registerAddress = '127.0.0.1:' . getenv("REGISTER_PORT"); // 3001


// 设置业务超时时间10秒
$worker->processTimeout = 60;
// 业务超时回调，可以把超时日志保存到自己想要的地方
$worker->processTimeoutHandler = function($trace_str, $exeption)
{
    print_r($trace_str);return false;
	$log_filename = 'timeout_'.date("Y-m-d").'.log';
	
	$log_dir = Config::Log_Dir.$log_filename;
	
    file_put_contents($log_dir, $trace_str, FILE_APPEND);
    // 返回假，让进程重启，避免进程继续无限阻塞
    return false;
};



// 如果不是在根目录启动，则运行runAll方法
if(!defined('GLOBAL_START'))
{
    Worker::runAll();
}

