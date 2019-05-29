<?php


require_once dirname(dirname(__DIR__)) . '/base.class.model.php';
require_once __DIR__ . '/room.class.model.php';

class Dispatcher_Client {

    /*
        根据操作码分发操作
    */
    public static function dispatcherOpt($req_ary) {
        $result     = OPT_CONST::NO_RETURN;
        $Base_Model = new Base_Model();

		var_dump($req_ary['operation']);

        if (isset($req_ary['operation'])) {
            switch ($req_ary['operation']) {
                /*************
                 * room操作
                 **************/
                case 'InitConnect':
                    echo "------messgae------".json_encode($req_ary)."\n";
                    $Front_Model = new Front_Model();
                    $result     = $Front_Model->initConnect($req_ary);
                    break;

                default:
                    $result = OPT_CONST::MISSING_OPERATION;    //数据格式不正确，缺少操作码
                    break;
            }

            //关闭数据库
            @$Base_Model->closeMysql();

            return $result;
        } else {
            return OPT_CONST::MISSING_OPERATION;    //缺少操作码
        }
    }


    /*
        写日志
    */
    public static function writeLog($content) {
        $content = date("Y-m-d H:i:s") . ' ' . $content;
        print_r($content);
        return;
        $log_filename = 'operation_' . date("Y-m-d") . '.log';

        $file = Config::Log_Dir . $log_filename;

        file_put_contents($file, $content, FILE_APPEND);

        file_put_contents($file, "\n", FILE_APPEND);
    }


    /*
        断开操作
    */
    public static function socketClosed($array) {
        $Front_Model = new Front_Model();
        $result     = $Front_Model->userDisconnected($array);
        return $result;
    }

}
