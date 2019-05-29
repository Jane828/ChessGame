<?php

use \GatewayWorker\Lib\Gateway;

require_once dirname(__DIR__) . '/Models/process.class.model.php';				//处理类

require_once dirname(__DIR__) . '/Models/Timer/Dispatcher.class.model.php';	//server调度文件

class Process
{

    /**
     * 当客户端连接时触发
     * 如果业务不需此回调可以删除onConnect
     *
     * @param int $client_id 连接id
     */
    public static function processOnConnect($client)
    {
        //设备登陆
        //$login_return_msg = "";
        //Gateway::sendToClient($client_id, '{"result":"-1"}');
        //var_dump($client_id);
    }


    /**
    * 当用户断开连接时触发
    *
    * @param int $client_id 连接id
    */
    public static function processOnClose($client)
    {
        //断开
    }


    /**
    * 数据处理入口
    *
    * @param int $client_id 连接id
    * @param string $message 具体消息
    */
    public static function processOnMessage($client, $message)
    {
        if ($message == "@") {
            $client->send("@");
        } elseif (false != $message && $message != "") {
            //  	$callback_array = array(
            //  		'operation' => 'Callback_OP',
            //  		'room_id' => 10001,
            //  		'data' => array(
            //  			'account_id' => 1
            //  			)
            //  		);

            //   $build_timer_arr = array(
            //  		'operation' => 'BuildTimer',
            //  		'room_id' => 10001,
            //  		'data' => array(
            //  			'account_id' => 1,
            //  			'limit_time'=>5,
            //  			'callback_array' => $callback_array
            //  			)
            //  		);


            // $delete_timer_arr = array(
            //  		'operation' => 'DeleteTimer',
            //  		'room_id' => 10001,
            //  		'data' => array(
            //  			'timer_id' => 1
            //  			)
            //  		);

            //   $message = json_encode($build_timer_arr);
            //判断request格式是否正确
            $req_ary = Process_Model::getModelObject()->checkupFormat($message);
            $operation = G_CONST::EMPTY_STRING;
            $request_params = G_CONST::EMPTY_STRING;
            if (is_array($req_ary) && !isset($req_ary['result'])) {
                //  $req_ary['client_id'] = $client_id;

                $result = Dispatcher_Timer::dispatcherOpt($req_ary);
            } else {
                $result = $req_ary;
            }

            if ($result != OPT_CONST::NO_RETURN) {
                //格式化返回结果
                //$ret_result = Process_Model::getModelObject()->formartReturn($result,$request_params);

                self::writeLog($message);
                //发送返回结果给client
                //Gateway::sendToClient($client_id, $ret_result);
            }
        }
        return true;
    }



    /*
        写日志
    */
    public static function writeLog($content)
    {
        $content = date("Y-m-d H:i:s").' '.$content;

        $log_filename = "gw_".date("Y-m-d").'.log';

        $file = Config::Log_Dir.$log_filename;

        file_put_contents($file, $content, FILE_APPEND);

        file_put_contents($file, "\n", FILE_APPEND);
    }
}
