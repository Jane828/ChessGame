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

$context = array(
    'ssl' => array(
        'local_cert'  => '/wwwroot/ca/gw.pem', // 或者crt文件
        'local_pk'    => '/wwwroot/ca/gw.key',
        'verify_peer' => false
    )
);

/*	客户端接口	*/
// gateway 进程
// $gateway_C = new Gateway("websocket://0.0.0.0:20092");
$gateway_C = new Gateway("websocket://0.0.0.0:".getenv("GATEWAY_CLIENT_PORT"));
//$gateway_C->transport = 'ssl';
// gateway名称，status方便查看
$gateway_C->name = 'G';
// gateway进程数
$gateway_C->count = 2;
// 本机ip，分布式部署时使用内网ip
$gateway_C->lanIp = '127.0.0.1';
// 内部通讯起始端口，假如$gateway->count=4，起始端口为4000
// 则一般会使用4000 4001 4002 4003 4个端口作为内部通讯端口 
$gateway_C->startPort = intval(getenv("GATEWAY_CLIENT_START_PORT")); // 25092;
// 服务注册地址
$gateway_C->registerAddress = '127.0.0.1:'.getenv("REGISTER_PORT"); // 2009

/*	Server模块接口	*/
// gateway 进程
$gateway_S = new Gateway("text://0.0.0.0:".getenv("GATEWAY_SERVER_PORT")); // 20091
// gateway名称，status方便查看
$gateway_S->name = 'G';
// gateway进程数
$gateway_S->count = 1;
// 本机ip，分布式部署时使用内网ip
$gateway_S->lanIp = '127.0.0.1';
// 内部通讯起始端口，假如$gateway->count=4，起始端口为4000
// 则一般会使用4000 4001 4002 4003 4个端口作为内部通讯端口 
$gateway_S->startPort = intval(getenv("GATEWAY_SERVER_START_PORT")); // 25091;
// 服务注册地址
$gateway_S->registerAddress = '127.0.0.1:'.getenv("REGISTER_PORT"); // 2009


//前端需要加上心跳

//心跳间隔
 $gateway_C->pingInterval = 5;
 $gateway_C->pingNotResponseLimit = 2;
 $gateway_C->pingData = '';


/* 
// 当客户端连接上来时，设置连接的onWebSocketConnect，即在websocket握手时的回调
$gateway->onConnect = function($connection)
{
    $connection->onWebSocketConnect = function($connection , $http_header)
    {
        // 可以在这里判断连接来源是否合法，不合法就关掉连接
        // $_SERVER['HTTP_ORIGIN']标识来自哪个站点的页面发起的websocket链接
        if($_SERVER['HTTP_ORIGIN'] != 'http://kedou.workerman.net')
        {
            $connection->close();
        }
        // onWebSocketConnect 里面$_GET $_SERVER是可用的
        // var_dump($_GET, $_SERVER);
    };
}; 
*/

// 如果不是在根目录启动，则运行runAll方法
if(!defined('GLOBAL_START'))
{
    Worker::runAll();
}

