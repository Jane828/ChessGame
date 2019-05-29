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

/**
 * 用于检测业务代码死循环或者长时间阻塞等问题
 * 如果发现业务卡死，可以将下面declare打开（去掉//注释），并执行php start.php reload
 * 然后观察一段时间workerman.log看是否有process_timeout异常
 */
declare(ticks=1);

use \GatewayWorker\Lib\Gateway;

/**
 * 主逻辑
 * 主要是处理 onConnect onMessage onClose 三个方法
 * onConnect 和 onClose 如果不需要可以不用实现并删除
 */
require_once 'Controllers/process.class.controller.php';		
//class Event
class Events
{
    /**
     * 当客户端连接时触发
     * 如果业务不需此回调可以删除onConnect
     * 
     * @param int $client_id 连接id
     * @link http://gatewayworker-doc.workerman.net/gateway-worker-development/onconnect.html
     */
    public static function onConnect($client_id)
    {
	    Process::processOnConnect($client_id);
    }
    
   /**
    * 当客户端发来消息时触发
    * @param int $client_id 连接id
    * @param string $message 具体消息
    * @link http://gatewayworker-doc.workerman.net/gateway-worker-development/onmessage.html
    */
   public static function onMessage($client_id, $message)
   {
	   $port = $_SERVER['GATEWAY_PORT'];
	   
	   //Gateway::sendToClient("7f0000010b8900000012", $client_id.":".$port);
	   
	   Process::processOnMessage($client_id, $message, $port);
   }
   
   /**
    * 当用户断开连接时触发
    * @param int $client_id 连接id
    */
   public static function onClose($client_id)
   {
	   $port = $_SERVER['GATEWAY_PORT'];
	   
	   $data = array();
	   if(isset($_SESSION['gaming_roomid']))
	   {
		   $data['gaming_roomid'] = $_SESSION['gaming_roomid'];
	   }
	   if(isset($_SESSION['account_id']))
	   {
		   $data['account_id'] = $_SESSION['account_id'];
	   }
	   
	   Process::processOnClose($client_id , $port , $data);
   }
   
}



