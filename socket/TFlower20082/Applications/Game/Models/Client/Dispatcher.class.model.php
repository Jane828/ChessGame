<?php


require_once dirname(dirname(__DIR__)) . '/base.class.model.php';
require_once __DIR__ . '/play.class.model.php';
require_once __DIR__ . '/room.class.model.php';

class Dispatcher_Client {

    /*
        根据操作码分发操作
    */
    public static function dispatcherOpt($req_ary) {
        $result     = OPT_CONST::NO_RETURN;
        $Base_Model = new Base_Model();

        if (isset($req_ary['operation'])) {
            switch ($req_ary['operation']) {
                /*************
                 * room操作
                 **************/
                 case 'JoinBox':
					echo "------onMessage---JoinBox---:".json_encode($req_ary)."\n";
					$Room_Model = new Room_Model();
					$result = $Room_Model->joinBox($req_ary);
					break;
                case 'CreateRoom':        //创建房间
                    $Room_Model = new Room_Model();
                    $result     = $Room_Model->createRoom($req_ary);
                    break;
                case 'ActivateRoom':        //激活房间
                    $Room_Model = new Room_Model();
                    $result     = $Room_Model->activateRoom($req_ary);
                    break;
                case 'JoinRoom':        //进入房间
                    $Room_Model = new Room_Model();
                    $result     = $Room_Model->joinRoom($req_ary);
                    break;
                case 'Audience':        //加入观战
                    $Room_Model = new Room_Model();
                    $result     = $Room_Model->audience($req_ary);
                    break;
                case 'ReadyStart':        //用户准备操作
                    $Room_Model = new Room_Model();
                    $result     = $Room_Model->readyStart($req_ary);
                    break;
                case 'ReadyCancel':        //用户取消准备操作
                    $Room_Model = new Room_Model();
                    $result     = $Room_Model->readyCancel($req_ary);
                    break;
                case 'PrepareJoinRoom':        //进房之前的查询
                    $Room_Model = new Room_Model();
                    $result     = $Room_Model->prepareJoinRoom($req_ary);
                    break;
                case 'PullRoomInfo':        //拉取房间信息
                    $Room_Model = new Room_Model();
                    $result     = $Room_Model->pullRoomInfo($req_ary);
                    break;
                case 'HistoryScoreboard':        //历史积分榜
                    $Room_Model = new Room_Model();
                    $result     = $Room_Model->historyScoreboard($req_ary);
                    break;
                case 'LastScoreboard':        //历史战绩
                    $Room_Model = new Room_Model();
                    $result     = $Room_Model->lastScoreboard($req_ary);
                    break;

                case 'TestDouble':        //拉取房间信息
                    $Room_Model = new Room_Model();
                    $result     = $Room_Model->testDouble($req_ary);
                    break;

                case 'ClickToLook':        //点击看牌
                    $Room_Model = new Play_Model();
                    $result     = $Room_Model->clickToLook($req_ary);
                    break;
                case 'ChooseChip':        //选择筹码
                    $Room_Model = new Play_Model();
                    $result     = $Room_Model->ChooseChip($req_ary);
                    break;

                case 'Discard':        //弃牌
                    $Room_Model = new Play_Model();
                    $result     = $Room_Model->discard($req_ary);
                    break;
                case 'OpenCard':        //开牌
                    $Room_Model = new Play_Model();
                    $result     = $Room_Model->openCard($req_ary);
                    break;

                case 'PkCard':        //开牌
                    $Room_Model = new Play_Model();
                    $result     = $Room_Model->pkCard($req_ary);
                    break;
                case 'BroadcastVoice':        //发送声音
                    $Room_Model = new Play_Model();
                    $result     = $Room_Model->broadcastVoice($req_ary);
                    break;

                case 'SpeakVoice':        //发送声音2
                    $Room_Model = new Play_Model();
                    $result     = $Room_Model->speakVoice($req_ary);
                    break;

                case 'Observer':        //观察员用于调试收取房间消息
                    $Room_Model = new Play_Model();
                    $result     = $Room_Model->observer($req_ary);
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
        $Room_Model = new Room_Model();
        $result     = $Room_Model->userDisconnected($array);
        return $result;
    }

}
