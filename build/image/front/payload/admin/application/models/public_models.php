<?php

require_once('db_models.php');

class Public_Models extends Db_Models {
    public function __construct() {
        header('Content-Type: text/html; charset=utf-8');
        parent::__construct();
        $this->load->helper('url');
    }


    public function checkIPAddress() {
        return 1;
        $client_ip = $_SERVER["REMOTE_ADDR"];

        $jsonString = file_get_contents("http://ip.taobao.com/service/getIpInfo.php?ip=" . $client_ip);

        $jsonArray = $this->splitJsonString($jsonString);
        if (isset($jsonArray['code']) && $jsonArray['code'] == "0") {
            if (isset($jsonArray['data']['country_id']) && $jsonArray['data']['country_id'] != "CN") {
                return 2;    //英文
            }
        }

        return 1;    //中文
    }


    /*
        检查用户是否已登录
    */
    protected function missingPrameterArr($prameter) {
        return array('result' => OPT_CONST::MISSING_PARAMETER, 'data' => array("missing_parameter" => $prameter), 'result_message' => "缺少参数");
    }


    /*
        拆解接收的json字符串
    */
    protected function splitJsonString($jsonString) {
        if (empty($jsonString)) {
            return FALSE;
        }
        //判断是否为JSON格式
        if (is_null(json_decode($jsonString))) {
            log_message('error', "function(splitJsonString):jsonString :" . $jsonString . " in file" . __FILE__ . " on Line " . __LINE__);
            //不是json格式
            return FALSE;
        } else {
            //分拆JSON字符串
            //return json_decode($jsonString,true);
            $jsonArray = json_decode($jsonString, TRUE);
            if (isset($jsonArray['u_id']) && trim($jsonArray['u_id']) === G_CONST::EMPTY_STRING) {
                $jsonArray['u_id'] = 0;
            }
            return $jsonArray;
        }
    }


    /*
        数组转JSON格式
    */
    public function JSON($array) {
        $this->arrayRecursive($array, 'urlencode', TRUE);
        $json = json_encode($array);
        return urldecode($json);
    }

    private function arrayRecursive(&$array, $function, $apply_to_keys_also = FALSE) {
        static $recursive_counter = 0;
        if (++$recursive_counter > 1000) {
            die('possible deep recursion attack');
        }
        foreach ($array as $key => $value) {
            if (is_array($value)) {
                $this->arrayRecursive($array[$key], $function, $apply_to_keys_also);
            } else {
                $array[$key] = $function($value);
            }

            if ($apply_to_keys_also && is_string($key)) {
                $new_key = $function($key);
                if ($new_key != $key) {
                    $array[$new_key] = $array[$key];
                    unset($array[$key]);
                }
            }
        }
        $recursive_counter--;
    }


    /*
        添加房卡流水记录
    */
    protected function updateRoomTicketJournal($arrData, $dealerDB) {
        $timestamp = time();

        $journal_type = $arrData['journal_type'];    //1入账，2出账
        $account_id   = $arrData['account_id'];
        $object_type  = $arrData['object_type'];
        $object_id    = $arrData['object_id'];
        $ticket_count = $arrData['ticket_count'];
        $extra        = $arrData['extra'];
        $dealer_id    = $arrData['dealer_id'];

        //获取流水账越
        $balance       = 0;
        $balance_where = 'account_id=' . $account_id . ' and is_delete=0';
        $balance_sql   = 'select balance from ' . Room_TicketJournal . ' where ' . $balance_where . ' order by journal_id desc';
        $balance_query = $this->getDataBySql($dealerDB, 1, $balance_sql);
        if (DB_CONST::DATA_NONEXISTENT != $balance_query) {
            $balance = $balance_query['balance'];
        }

        switch ($object_type) {
            case Game_CONST::ObjectType_Newuser:
                $abstract = "用户首次登陆";
                break;
            case Game_CONST::ObjectType_Recharge:
                $abstract = "购买房卡";
                break;
            case Game_CONST::ObjectType_Game:
                $abstract = "游戏消耗";
                break;
            case Game_CONST::ObjectType_Dealer:
                $abstract = "绑定代理商";
                break;
            case Game_CONST::ObjectType_Sign:
                $abstract = "每日签到";
                break;
            case Game_CONST::ObjectType_RedEnvelop:
                $abstract = "红包活动";
                break;
            case Game_CONST::ObjectType_Luckdraw:
                $abstract = "幸运转盘";
                break;
            case Game_CONST::ObjectType_SlotMachine:
                $abstract = "老虎机抽奖";
                break;
            case Game_CONST::ObjectType_Commission:
                $abstract = "提成";
                break;
            case Game_CONST::ObjectType_BindAccount:
                $abstract = "绑定新账号转移房卡";
                break;
            case Game_CONST::ObjectType_Adjustment:
                $abstract = "调整房卡";
                break;
            default :
                log_message('error', "function(updateRoomTicketJournal):object_type error : " . $object_type . " in file" . __FILE__ . " on Line " . __LINE__);
                return FALSE;
                break;
        }


        //添加到流水账
        $journal_array['create_time'] = $timestamp;
        $journal_array['update_time'] = $timestamp;
        if ($dealer_id > 0) {
            $journal_array['create_appid'] = "dealer_" . $dealer_id;
            $journal_array['update_appid'] = "dealer_" . $dealer_id;
        } else {
            $journal_array['create_appid'] = "aid_" . $account_id;
            $journal_array['update_appid'] = "aid_" . $account_id;
        }

        $journal_array['is_delete']   = G_CONST::IS_FALSE;
        $journal_array['account_id']  = $account_id;
        $journal_array['object_id']   = $object_id;
        $journal_array['object_type'] = $object_type;

        $journal_array['extra']    = $extra;
        $journal_array['abstract'] = $abstract;        //摘要

        if ($journal_type == Game_CONST::JournalType_Income) {
            $journal_array['income']  = $ticket_count;
            $journal_array['balance'] = $balance + $ticket_count;
        } else if ($journal_type == Game_CONST::JournalType_Disburse) {
            $journal_array['disburse'] = $ticket_count;
            $journal_array['balance']  = $balance - $ticket_count;
            if ($journal_array['balance'] < 0) {
                log_message('error', "function(updateRoomTicketJournal):balance negative balance: " . $balance . " in file" . __FILE__ . " on Line " . __LINE__);
                log_message('error', "function(updateRoomTicketJournal):balance negative account_id: " . $account_id . " in file" . __FILE__ . " on Line " . __LINE__);
                log_message('error', "function(updateRoomTicketJournal):balance negative object_type: " . $object_type . " in file" . __FILE__ . " on Line " . __LINE__);
                log_message('error', "function(updateRoomTicketJournal):balance negative object_id: " . $object_id . " in file" . __FILE__ . " on Line " . __LINE__);
                log_message('error', "function(updateRoomTicketJournal):balance negative ticket_count: " . $ticket_count . " in file" . __FILE__ . " on Line " . __LINE__);
                $journal_array['balance'] = 0;
            }
        } else {
            log_message('error', "function(updateRoomTicketJournal):journal_type error : " . $journal_type . " in file" . __FILE__ . " on Line " . __LINE__);
            return FALSE;
        }
        $journal_id = $this->getInsertID($dealerDB, Room_TicketJournal, $journal_array);

        return TRUE;
    }


    protected function getAccountByOpenid($open_id, $dealerDB) {
        $account_where = 'open_id="' . $open_id . '"';
        $account_sql   = 'select account_id,open_id,nickname,headimgurl from ' . WX_Account . ' where ' . $account_where . '';
        $account_query = $this->getDataBySql($dealerDB, 1, $account_sql);
        return $account_query;
    }


    protected function getAccountByAccountid($account_id, $dealerDB) {
        $account_where = 'account_id=' . $account_id;
        $account_sql   = 'select account_id,open_id,nickname,headimgurl from ' . WX_Account . ' where ' . $account_where . '';
        $account_query = $this->getDataBySql($dealerDB, 1, $account_sql);
        return $account_query;
    }


    /*
        获取代理商信息
    */
    public function getDealerInfo($arrData) {
        $result    = array();
        $timestamp = time();

        if (!isset($arrData['dealer_num']) || $arrData['dealer_num'] === "") {
            log_message('error', "function(getDealerInfo):lack of dealer_num" . " in file" . __FILE__ . " on Line " . __LINE__);
            return $this->missingPrameterArr("dealer_num");
        }

        if (!isset($arrData['dealer_id']) || $arrData['dealer_id'] === "") {
            log_message('error', "function(getDealerInfo):lack of dealer_id" . " in file" . __FILE__ . " on Line " . __LINE__);
            return $this->missingPrameterArr("dealer_id");
        }

        $dealer_num  = $arrData['dealer_num'];
        $dealer_id   = $arrData['dealer_id'];
        $DelaerConst = "Dealer_" . $dealer_num;


        $result['inventory_count'] = 0;
        $result['goods_array']     = array();


        if ($dealer_num == "-1" || !class_exists($DelaerConst)) {
            for ($i = count($result['goods_array']); $i < 4; $i++) {
                $result['goods_array'][] = array("title" => "", "price" => "", "ticket_count" => "");
            }
            return array('result' => OPT_CONST::SUCCESS, 'data' => $result, 'result_message' => "获取代理商信息");
        }


        $dealerDB = $DelaerConst::DBConst_Name;

        if ($dealer_num == 6) {
            $dealer_num = 1;
        }

        //获取代理商信息
        $account_where = 'dealer_num=' . $dealer_num . ' and dealer_id = ' . $dealer_id . ' and is_delete=0';
        $account_sql   = 'select inventory_count from ' . D_Account . ' where ' . $account_where . '';
        $account_query = $this->getDataBySql($dealerDB, 1, $account_sql);
        if ($account_query == DB_CONST::DATA_NONEXISTENT) {
            for ($i = count($result['goods_array']); $i < 4; $i++) {
                $result['goods_array'][] = array("title" => "", "price" => "", "ticket_count" => "");
            }
            log_message('error', "function(getDealerInfo):can not find dealer account" . " in file" . __FILE__ . " on Line " . __LINE__);
            return array('result' => OPT_CONST::SUCCESS, 'data' => $result, 'result_message' => "获取代理商信息");
        }

        $goods_array = array();
        //获取代理商销售价格
        $goods_where = 'is_delete=0';
        $goods_sql   = 'select title,price,ticket_count from ' . Payment_Goods . ' where ' . $goods_where . '';
        $goods_query = $this->getDataBySql($dealerDB, 0, $goods_sql);
        if ($goods_query != DB_CONST::DATA_NONEXISTENT) {
            $goods_array = $goods_query;
        }
        for ($i = count($goods_array); $i < 4; $i++) {
            $goods_array[] = array("title" => "", "price" => "", "ticket_count" => "");
        }

        $result['inventory_count'] = $account_query['inventory_count'];
        $result['goods_array']     = $goods_array;
        $result['is_guild']        = $DelaerConst::Is_Guild;
        $result['is_channel']      = $DelaerConst::Is_Channel;
        $result['is_exchange']     = $DelaerConst::Is_Exchange;

        $result['gamescore_array'][] = array("game_type" => "1", "game_title" => "诈金花", "is_show" => $DelaerConst::GameResult_Flower);
        $result['gamescore_array'][] = array("game_type" => "110", "game_title" => "10人诈金花", "is_show" => $DelaerConst::GameResult_TFlower);
        $result['gamescore_array'][] = array("game_type" => "5", "game_title" => "6人斗牛", "is_show" => $DelaerConst::GameResult_Bull6);
        $result['gamescore_array'][] = array("game_type" => "9", "game_title" => "9人斗牛", "is_show" => $DelaerConst::GameResult_Bull9);
        $result['gamescore_array'][] = array("game_type" => "12", "game_title" => "12人斗牛", "is_show" => $DelaerConst::GameResult_Bull12);

        return array('result' => OPT_CONST::SUCCESS, 'data' => $result, 'result_message' => "获取代理商信息");
    }


    /*
        获取代理商销售概况
    */
    public function getDealerSaleInfo($arrData) {
        $result    = array();
        $timestamp = time();

        if (!isset($arrData['dealer_num']) || $arrData['dealer_num'] === "") {
            log_message('error', "function(getDealerSaleInfo):lack of dealer_num" . " in file" . __FILE__ . " on Line " . __LINE__);
            return $this->missingPrameterArr("dealer_num");
        }
        if (!isset($arrData['from']) || $arrData['from'] === "") {
            log_message('error', "function(getDealerSaleInfo):lack of from" . " in file" . __FILE__ . " on Line " . __LINE__);
            return $this->missingPrameterArr("from");
        }
        if (!isset($arrData['to']) || $arrData['to'] === "") {
            log_message('error', "function(getDealerSaleInfo):lack of to" . " in file" . __FILE__ . " on Line " . __LINE__);
            return $this->missingPrameterArr("to");
        }

        $sum_price       = 0;
        $sum_ticketCount = 0;
        $sum_redenvelop  = 0;

        $dealer_num  = $arrData['dealer_num'];
        $DelaerConst = "Dealer_" . $dealer_num;


        if ($dealer_num == "-1" || !class_exists($DelaerConst)) {
            $result['sum_price']       = $sum_price;
            $result['sum_ticketCount'] = $sum_ticketCount;
            $result['sum_redenvelop']  = $sum_redenvelop;
            return array('result' => OPT_CONST::SUCCESS, 'data' => $result, 'result_message' => "获取代理商信息");
        }


        $dealerDB = $DelaerConst::DBConst_Name;

        $from = $arrData['from'];
        $to   = $arrData['to'];

        $from_timestamp = strtotime($arrData['from']);
        $to_timestamp   = strtotime($arrData['to']) + 86400;

        //商城销售
        $payment_where = 'create_time>=' . $from_timestamp . ' and create_time<' . $to_timestamp . ' and is_delete=0 and object_type=' . Game_CONST::DObjectType_Sale;
        $payment_sql   = 'select sum(disburse) as sum_ticketCount from ' . D_Journal . ' where ' . $payment_where . '';
        $payment_query = $this->getDataBySql($dealerDB, 1, $payment_sql);
        if ($payment_query != DB_CONST::DATA_NONEXISTENT && $payment_query['sum_ticketCount'] != NULL) {
            $sum_ticketCount = $payment_query['sum_ticketCount'];
        }


        //商城销售
        $redenvelop_where = 'create_time>=' . $from_timestamp . ' and create_time<' . $to_timestamp . ' and is_delete=0 and object_type=' . Game_CONST::DObjectType_RedEnvelop;
        $redenvelop_sql   = 'select sum(disburse) as sum_redenvelop from ' . D_Journal . ' where ' . $redenvelop_where . '';
        $redenvelop_query = $this->getDataBySql($dealerDB, 1, $redenvelop_sql);
        if ($redenvelop_query != DB_CONST::DATA_NONEXISTENT && $redenvelop_query['sum_redenvelop'] != NULL) {
            $sum_redenvelop = $redenvelop_query['sum_redenvelop'];
        }


        // //商城销售
        // $payment_where = 'o.pay_time>='.$from_timestamp.' and o.pay_time<'.$to_timestamp.' and o.order_id=goods.order_id and o.is_delete=0 and o.is_pay=1';
        // $payment_sql = 'select sum(o.total_price) as sum_price,sum(goods.ticket_count) as sum_ticketCount from '.Payment_Order.' as o,'.Payment_OrderGoods.' as goods where '.$payment_where.'';
        // $payment_query = $this->getDataBySql($dealerDB,1,$payment_sql);
        // if($payment_query != DB_CONST::DATA_NONEXISTENT && $payment_query['sum_price'] != null && $payment_query['sum_ticketCount'] != null)
        // {
        // 	$sum_price = $payment_query['sum_price'];
        // 	$sum_ticketCount = $payment_query['sum_ticketCount'];
        // }

        // //代理商红包销售
        // $redenvelop_where = 'type=2 and receive.create_time>='.$from_timestamp.' and receive.create_time<'.$to_timestamp.' and red.redenvelop_id=receive.redenvelop_id and red.is_delete=0 and red.is_receive=1 and is_return=0';
        // $redenvelop_sql = 'select sum(red.ticket_count) as sum_redenvelop from '.Act_Redenvelop.' as red,'.Act_RedenvelopReceive.' as receive where '.$redenvelop_where.'';
        // $redenvelop_query = $this->getDataBySql($dealerDB,1,$redenvelop_sql);
        // if($redenvelop_query != DB_CONST::DATA_NONEXISTENT && $redenvelop_query['sum_redenvelop'] != null)
        // {
        // 	$sum_redenvelop = $redenvelop_query['sum_redenvelop'];
        // }

        //$result['sum_price'] = $sum_price;
        $result['sum_ticketCount'] = $sum_ticketCount;
        $result['sum_redenvelop']  = $sum_redenvelop;


        return array('result' => OPT_CONST::SUCCESS, 'data' => $result, 'result_message' => "获取代理商销售概况");
    }

    /*
        获取代理商充值概况
    */
    public function getDealerRechargeInfo($arrData) {
        $result = array();

        if (!isset($arrData['dealer_num']) || $arrData['dealer_num'] === "") {
            log_message('error', "function(getDealerRechargeInfo):lack of dealer_num" . " in file" . __FILE__ . " on Line " . __LINE__);
            return $this->missingPrameterArr("dealer_num");
        }
        if (!isset($arrData['from']) || $arrData['from'] === "") {
            log_message('error', "function(getDealerRechargeInfo):lack of from" . " in file" . __FILE__ . " on Line " . __LINE__);
            return $this->missingPrameterArr("from");
        }
        if (!isset($arrData['to']) || $arrData['to'] === "") {
            log_message('error', "function(getDealerRechargeInfo):lack of to" . " in file" . __FILE__ . " on Line " . __LINE__);
            return $this->missingPrameterArr("to");
        }

        $sum_price    = 0;
        $sum_recharge = 0;
        $sum_reward   = 0;

        $dealer_num  = $arrData['dealer_num'];
        $DelaerConst = "Dealer_" . $dealer_num;

        if ($dealer_num == "-1" || !class_exists($DelaerConst)) {
            $result['sum_price']    = $sum_price;
            $result['sum_recharge'] = $sum_recharge;
            $result['sum_reward']   = $sum_reward;
            return array('result' => OPT_CONST::SUCCESS, 'data' => $result, 'result_message' => "获取代理商信息");
        }

        $dealerDB = $DelaerConst::DBConst_Name;

        $from = $arrData['from'];
        $to   = $arrData['to'];

        $from_timestamp = strtotime($arrData['from']);
        $to_timestamp   = strtotime($arrData['to']) + 86400;

        //房卡充值
        $where = 'create_time>=' . $from_timestamp . ' and create_time<' . $to_timestamp . ' and is_delete=0 and object_type=' . Game_CONST::DObjectType_Recharge;
        $sql   = 'select sum(income) as sum_recharge from ' . D_Journal . ' where ' . $where . '';
        $query = $this->getDataBySql($dealerDB, 1, $sql);
        if ($query != DB_CONST::DATA_NONEXISTENT && $query['sum_recharge'] != NULL) {
            $sum_recharge = $query['sum_recharge'];
        }

        //房卡赠送
        $where = 'create_time>=' . $from_timestamp . ' and create_time<' . $to_timestamp . ' and is_delete=0 and object_type=' . Game_CONST::DObjectType_Reward;
        $sql   = 'select sum(income) as sum_reward from ' . D_Journal . ' where ' . $where . '';
        $query = $this->getDataBySql($dealerDB, 1, $sql);
        if ($query != DB_CONST::DATA_NONEXISTENT && $query['sum_reward'] != NULL) {
            $sum_reward = $query['sum_reward'];
        }

        $result['sum_recharge'] = $sum_recharge;
        $result['sum_reward']   = $sum_reward;
        return array('result' => OPT_CONST::SUCCESS, 'data' => $result, 'result_message' => "获取代理商充值概况");
    }

    /*
        获取代理商销售列表
    */
    public function getDealerJournal($arrData) {
        $result = array();

        if (!isset($arrData['dealer_id']) || $arrData['dealer_id'] === "") {
            log_message('error', "function(getDealerJournal):lack of dealer_id" . " in file" . __FILE__ . " on Line " . __LINE__);
            return $this->missingPrameterArr("dealer_id");
        }
        if (!isset($arrData['dealer_num']) || $arrData['dealer_num'] === "") {
            log_message('error', "function(getDealerJournal):lack of dealer_num" . " in file" . __FILE__ . " on Line " . __LINE__);
            return $this->missingPrameterArr("dealer_num");
        }
        if (!isset($arrData['from']) || $arrData['from'] === "") {
            log_message('error', "function(getDealerJournal):lack of from" . " in file" . __FILE__ . " on Line " . __LINE__);
            return $this->missingPrameterArr("from");
        }
        if (!isset($arrData['to']) || $arrData['to'] === "") {
            log_message('error', "function(getDealerJournal):lack of to" . " in file" . __FILE__ . " on Line " . __LINE__);
            return $this->missingPrameterArr("to");
        }
        if (!isset($arrData['page']) && $arrData['page'] === "") {
            log_message('error', "function(getDealerJournal):lack of page" . " in file" . __FILE__ . " on Line " . __LINE__);
            return $this->missingPrameterArr("page");
        }

        if ($arrData['dealer_num'] == "-1") {
            return array('result' => OPT_CONST::SUCCESS, 'data' => $result, 'result_message' => "获取代理商销售概况", 'sum_page' => 0);
        }

        $dealer_num  = $arrData['dealer_num'];
        $dealer_id   = $arrData['dealer_id'];
        $DelaerConst = "Dealer_" . $dealer_num;

        if ($dealer_num == "-1" || !class_exists($DelaerConst)) {
            return array('result' => OPT_CONST::SUCCESS, 'data' => $result, 'result_message' => "获取代理商销售概况", 'sum_page' => 0);
        }

        $dealerDB = $DelaerConst::DBConst_Name;

        $from = $arrData['from'];
        $to   = $arrData['to'];
        $page = $arrData['page'];

        $from_timestamp = strtotime($arrData['from']);
        $to_timestamp   = strtotime($arrData['to']) + 86400;

        $limit    = 10;
        $offset   = ($page - 1) * $limit;
        $sum_page = 1;

        $journal_where = ' dealer_id = ' . $dealer_id . ' and create_time>=' . $from_timestamp . ' and create_time<' . $to_timestamp . ' and object_type in (' . Game_CONST::DObjectType_Recharge . ',' . Game_CONST::DObjectType_Reward . ') and is_delete=0';
        $journal_sql   = 'select create_time as time,object_id,object_type,income,disburse from ' . D_Journal . ' where ' . $journal_where . ' order by time desc limit ' . $offset . ',' . $limit;

        $journal_query = $this->getDataBySql($dealerDB, 0, $journal_sql);
        if ($journal_query != DB_CONST::DATA_NONEXISTENT) {
            $count_sql   = 'select count(journal_id) as count from ' . D_Journal . ' where ' . $journal_where;
            $count_query = $this->getDataBySql($dealerDB, 1, $count_sql);
            $sum_page    = ceil($count_query['count'] / $limit);

            foreach ($journal_query as $journal) {
                switch ($journal['object_type']) {
                    case Game_CONST::DObjectType_Recharge:
                        $object_content = "后台充值";
                        $object_user    = "后台充值";
                        break;
                    case Game_CONST::DObjectType_Reward:
                        $object_content = "后台充值";
                        $object_user    = "后台充值";
                        break;
                    default:
                        continue;
                }

                if ($object_user == FALSE) {
                    continue;
                }

                $array['time'] = date('Y/m/d H:i', $journal['time']);

                $array['ticket_count'] = 0;
                if ($journal['income'] != "") {
                    $array['ticket_count'] = $journal['income'];
                    $array['journal_type'] = "入账";
                } else if ($journal['disburse'] != "") {
                    $array['ticket_count'] = $journal['disburse'];
                    $array['journal_type'] = "出账";
                }

                $array['content'] = $object_content;
                $array['user']    = $object_user;

                $result[] = $array;
            }
        }
        return array('result' => OPT_CONST::SUCCESS, 'data' => $result, 'result_message' => "获取代理商销售列表", 'page' => $page, 'sum_page' => $sum_page);
    }

    /**
     * 获取手动充值列表
     */
    public function getDealerManualRechargeJournal($arrData) {
        $result = array();

        if (!isset($arrData['dealer_id']) || $arrData['dealer_id'] === "") {
            log_message('error', "function(getDealerJournal):lack of dealer_id" . " in file" . __FILE__ . " on Line " . __LINE__);
            return $this->missingPrameterArr("dealer_id");
        }
        if (!isset($arrData['dealer_num']) || $arrData['dealer_num'] === "") {
            log_message('error', "function(getDealerJournal):lack of dealer_num" . " in file" . __FILE__ . " on Line " . __LINE__);
            return $this->missingPrameterArr("dealer_num");
        }
        if (!isset($arrData['from']) || $arrData['from'] === "") {
            log_message('error', "function(getDealerJournal):lack of from" . " in file" . __FILE__ . " on Line " . __LINE__);
            return $this->missingPrameterArr("from");
        }
        if (!isset($arrData['to']) || $arrData['to'] === "") {
            log_message('error', "function(getDealerJournal):lack of to" . " in file" . __FILE__ . " on Line " . __LINE__);
            return $this->missingPrameterArr("to");
        }
        if (!isset($arrData['page']) && $arrData['page'] === "") {
            log_message('error', "function(getDealerJournal):lack of page" . " in file" . __FILE__ . " on Line " . __LINE__);
            return $this->missingPrameterArr("page");
        }

        if ($arrData['dealer_num'] == "-1") {
            return array('result' => OPT_CONST::SUCCESS, 'data' => $result, 'result_message' => "获取手动充值列表", 'sum_page' => 0);
        }

        $dealer_num  = $arrData['dealer_num'];
        $dealer_id   = $arrData['dealer_id'];
        $DelaerConst = "Dealer_" . $dealer_num;

        if ($dealer_num == "-1" || !class_exists($DelaerConst)) {
            return array('result' => OPT_CONST::SUCCESS, 'data' => $result, 'result_message' => "获取手动充值列表", 'sum_page' => 0);
        }

        $dealerDB = $DelaerConst::DBConst_Name;

        $from = $arrData['from'];
        $to   = $arrData['to'];
        $page = $arrData['page'];

        $from_timestamp = strtotime($arrData['from']);
        $to_timestamp   = strtotime($arrData['to']) + 86400;

        $limit    = 10;
        $offset   = ($page - 1) * $limit;
        $sum_page = 1;

        $journal_where = ' jou.create_appid = "dealer_' . $dealer_id . '" and jou.create_time>=' . $from_timestamp . ' and jou.create_time<' . $to_timestamp . ' and object_type in (' . Game_CONST::DObjectType_Recharge . ',' . Game_CONST::DObjectType_Reward . ') and jou.is_delete=0 and jou.account_id = acc.account_id';
        $journal_sql   = 'select jou.create_time as time,jou.object_id,jou.object_type,jou.income,jou.disburse, acc.nickname, acc.user_code from ' . Room_TicketJournal . ' as jou, ' .WX_Account . ' as acc  where ' . $journal_where . ' order by time desc limit ' . $offset . ',' . $limit;

        $journal_query = $this->getDataBySql($dealerDB, 0, $journal_sql);
        if ($journal_query != DB_CONST::DATA_NONEXISTENT) {
            $count_sql   = 'select count(journal_id) as count from ' . Room_TicketJournal . ' as jou, ' .WX_Account . ' as acc  where ' . $journal_where;
            $count_query = $this->getDataBySql($dealerDB, 1, $count_sql);
            $sum_page    = ceil($count_query['count'] / $limit);

            foreach ($journal_query as $journal) {
                switch ($journal['object_type']) {
                    case Game_CONST::DObjectType_Recharge:
                        $object_content = "后台充值";
//                        $object_user    = "后台充值";
                        break;
                    case Game_CONST::DObjectType_Reward:
                        $object_content = "后台充值";
//                        $object_user    = "后台充值";
                        break;
                    default:
                        continue;
                }

                $object_user = $journal['nickname'];


                if ($object_user == FALSE) {
                    continue;
                }

                $array['time'] = date('Y/m/d H:i', $journal['time']);

                $array['ticket_count'] = 0;
                if ($journal['income'] != "") {
                    $array['ticket_count'] = $journal['income'];
                    $array['journal_type'] = "入账";
                } else if ($journal['disburse'] != "") {
                    $array['ticket_count'] = $journal['disburse'];
                    $array['journal_type'] = "出账";
                }

                $array['content'] = $object_content;
                $array['user']    = $object_user;
                $array["uid"] = $journal["user_code"];

                $result[] = $array;
            }
        }
        return array('result' => OPT_CONST::SUCCESS, 'data' => $result, 'result_message' => "获取代理商销售列表", 'page' => $page, 'sum_page' => $sum_page);
    }

    //获取购买用户
    protected function getOrderUser($dealerDB, $order_id) {
        $order_where = 'order_id=' . $order_id . ' and is_delete=0';
        $order_sql   = 'select account_id from ' . Payment_Order . ' where ' . $order_where . '';
        $order_query = $this->getDataBySql($dealerDB, 1, $order_sql);
        if ($order_query != DB_CONST::DATA_NONEXISTENT) {
            $account_id = $order_query['account_id'];

            //获取用户名称
            $account_query = $this->getAccountByAccountid($account_id, $dealerDB);
            if ($account_query != DB_CONST::DATA_NONEXISTENT) {
                return $account_query['nickname'];
            }
        }

        return G_CONST::EMPTY_STRING;
    }


    //获取红包用户
    protected function getReceiveUser($dealerDB, $redenvelop_id) {
        //判断红包是否被领取
        $redenvelop_where = 'redenvelop_id=' . $redenvelop_id . ' and is_delete=0';
        $redenvelop_sql   = 'select is_receive,is_return from ' . Act_Redenvelop . ' where ' . $redenvelop_where . '';
        $redenvelop_query = $this->getDataBySql($dealerDB, 1, $redenvelop_sql);
        if ($redenvelop_query != DB_CONST::DATA_NONEXISTENT) {
            if ($redenvelop_query['is_receive'] == 0 || $redenvelop_query['is_return'] == 1) {
                log_message('error', "function(getReceiveUser):is_receive/is_return" . " in file" . __FILE__ . " on Line " . __LINE__);
                return FALSE;
            }

            $receive_where = 'redenvelop_id=' . $redenvelop_id . ' and is_delete=0';
            $receive_sql   = 'select account_id from ' . Act_RedenvelopReceive . ' where ' . $receive_where . '';
            $receive_query = $this->getDataBySql($dealerDB, 1, $receive_sql);
            if ($receive_query != DB_CONST::DATA_NONEXISTENT) {
                $account_id = $receive_query['account_id'];

                //获取用户名称
                $account_query = $this->getAccountByAccountid($account_id, $dealerDB);
                if ($account_query != DB_CONST::DATA_NONEXISTENT) {
                    return $account_query['nickname'];
                }
            } else {
                log_message('error', "function(getReceiveUser):no receive" . " in file" . __FILE__ . " on Line " . __LINE__);
                return FALSE;
            }

        } else {
            log_message('error', "function(getReceiveUser):no redenvelop" . " in file" . __FILE__ . " on Line " . __LINE__);
            return FALSE;
        }
    }


    //根据room_id game_type 获取room_number
    protected function getRoomNumberByRoomID($dealerDB, $room_id, $game_type) {
        $room_number = "";
        $room_sql    = "";

        ////游戏类型 ：1炸金花  2斗地主  3梭哈  4德州  5六人斗牛 6广东麻将   9九人斗牛，10轮庄
        switch ($game_type) {
            case 1:
            case 110:// 10人炸金花
            case 111:// 10人炸金花
            case 92:// vip6人炸金花
            case 95:// vip10人炸金花
                $room_sql = 'select room_number from ' . Room_Flower . ' where room_id=' . $room_id;
                break;
            case 2:
                $room_sql = 'select room_number from ' . Room_Landlord . ' where room_id=' . $room_id;
                break;
            case 4:
                $room_sql = 'select room_number from ' . Room_Texas . ' where room_id=' . $room_id;
                break;
            case 5:
            case 9:
            case 91:
            case 93:
            case 94:
            case 12:
            case 71:
                $room_sql = 'select room_number from ' . Room_Bull . ' where room_id=' . $room_id;
                break;
            case 6:
                $room_sql = 'select room_number from ' . Room_GDMJ . ' where room_id=' . $room_id;
                break;
            case 10:
                $room_sql = 'select room_number from ' . Room_BullTurn . ' where room_id=' . $room_id;
                break;
            case 11:
                $room_sql = 'select room_number from ' . Room_Glz . ' where room_id=' . $room_id;
                break;
            case 36:
            case 37:
            case 38:
                $room_sql = 'select room_number from ' . Room_Sangong . ' where room_id=' . $room_id;
                break;
        }
        if ($room_sql != "") {
            $room_query = $this->getDataBySql($dealerDB, 1, $room_sql);
            if ($room_query != DB_CONST::DATA_NONEXISTENT) {
                $room_number = $room_query['room_number'];
            }
        }

        return $room_number;
    }

    //根据room_id game_type 获取room_number
    protected function getRoomIDByRoomNumber($dealerDB, $room_number, $game_type) {
        $room_id  = -1;
        $room_sql = "";

        ////游戏类型 ：1炸金花  2斗地主  3梭哈  4德州  5六人斗牛 6广东麻将   9九人斗牛，10轮庄
        switch ($game_type) {
            case 1:
            case 110:
            case 111:
            case 92:
            case 95:
                $room_sql = 'select room_id from ' . Room_Flower . ' where room_number="' . $room_number . '"';
                break;
            case 2:
                $room_sql = 'select room_id from ' . Room_Landlord . ' where room_number="' . $room_number . '"';
                break;
            case 4:
                $room_sql = 'select room_id from ' . Room_Texas . ' where room_number="' . $room_number . '"';
                break;
            case 5:
            case 9:
            case 91:
            case 93:
            case 94:
            case 71:
            case 12:
                $room_sql = 'select room_id from ' . Room_Bull . ' where room_number="' . $room_number . '"';
                break;
            case 6:
                $room_sql = 'select room_id from ' . Room_GDMJ . ' where room_number="' . $room_number . '"';
                break;
            case 10:
                $room_sql = 'select room_id from ' . Room_Texas . ' where room_number="' . $room_number . '"';
                break;
            case 36:
            case 37:
            case 38:
                $room_sql = 'select room_id from ' . Room_Sangong . ' where room_number="' . $room_number . '"';
                break;
        }
        if ($room_sql != "") {
            $room_query = $this->getDataBySql($dealerDB, 1, $room_sql);
            if ($room_query != DB_CONST::DATA_NONEXISTENT) {
                $room_id = $room_query['room_id'];
            }
        }

        return $room_id;

    }


    /*
        添加代理商流水记录
    */
    protected function updateInventoryJournal($arrData, $dealerDB) {
        $timestamp = time();

        $journal_type = $arrData['journal_type'];    //1入账，2出账
        $account_id   = $arrData['account_id'];
        $object_type  = $arrData['object_type'];
        $object_id    = $arrData['object_id'];
        $ticket_count = $arrData['ticket_count'];
        $extra        = $arrData['extra'];
        $dealer_id    = $arrData['dealer_id'];

        //获取流水账越
        $balance       = 0;
        $balance_where = 'is_delete=0';
        $balance_sql   = 'select balance from ' . D_Journal . ' where ' . $balance_where . ' order by journal_id desc';
        $balance_query = $this->getDataBySql($dealerDB, 1, $balance_sql);
        if (DB_CONST::DATA_NONEXISTENT != $balance_query) {
            $balance = $balance_query['balance'];
        } else {
            // $balance = $this->getTicketInventory($dealerDB);
            $account_where = 'is_delete=0';
            $account_sql   = 'select inventory_count from ' . D_Account . ' where ' . $account_where . '';
            $account_query = $this->getDataBySql($dealerDB, 1, $account_sql);
            if ($account_query != DB_CONST::DATA_NONEXISTENT) {
                $balance = $account_query['inventory_count'];
            }
        }

        switch ($object_type) {
            case Game_CONST::DObjectType_Balance:
                $abstract = "调整房卡";
                break;
            case Game_CONST::DObjectType_Recharge:
                $abstract = "后台充值";
                break;
            case Game_CONST::DObjectType_Sale:
                $abstract = "购买房卡";
                break;
            case Game_CONST::DObjectType_RedEnvelop:
                $abstract = "房卡红包";
                break;
            case Game_CONST::DObjectType_Exchange:
                $abstract = "制作兑换码";
                break;
            default :
                log_message('error', "function(updateInventoryJournal):object_type error : " . $object_type . " in file" . __FILE__ . " on Line " . __LINE__);
                return FALSE;
                break;
        }

        //添加到流水账
        $journal_array['create_time']  = $timestamp;
        $journal_array['create_appid'] = "aid_" . $account_id;
        $journal_array['update_time']  = $timestamp;
        $journal_array['update_appid'] = "aid_" . $account_id;
        $journal_array['is_delete']    = G_CONST::IS_FALSE;
        //$journal_array['account_id'] = $account_id;
        $journal_array['dealer_id']   = $dealer_id;
        $journal_array['object_id']   = $object_id;
        $journal_array['object_type'] = $object_type;

        $journal_array['extra']    = $extra;
        $journal_array['abstract'] = $abstract;        //摘要

        if ($journal_type == Game_CONST::JournalType_Income) {
            $journal_array['income']  = $ticket_count;
            $journal_array['balance'] = $balance + $ticket_count;
        } else if ($journal_type == Game_CONST::JournalType_Disburse) {
            $journal_array['disburse'] = $ticket_count;
            $journal_array['balance']  = $balance - $ticket_count;
            if ($journal_array['balance'] < 0) {
                log_message('error', "function(updateInventoryJournal):balance negative balance: " . $balance . " in file" . __FILE__ . " on Line " . __LINE__);
                log_message('error', "function(updateInventoryJournal):balance negative account_id: " . $account_id . " in file" . __FILE__ . " on Line " . __LINE__);
                log_message('error', "function(updateInventoryJournal):balance negative object_type: " . $object_type . " in file" . __FILE__ . " on Line " . __LINE__);
                log_message('error', "function(updateInventoryJournal):balance negative object_id: " . $object_id . " in file" . __FILE__ . " on Line " . __LINE__);
                log_message('error', "function(updateInventoryJournal):balance negative ticket_count: " . $ticket_count . " in file" . __FILE__ . " on Line " . __LINE__);
                $journal_array['balance'] = 0;
            }
        } else {
            log_message('error', "function(updateInventoryJournal):journal_type error : " . $journal_type . " in file" . __FILE__ . " on Line " . __LINE__);
            return FALSE;
        }
        $journal_id = $this->getInsertID($dealerDB, D_Journal, $journal_array);

        return TRUE;
    }


    /**
     * 获得一个数据库对象
     * @return mixed
     */
    public function db() {
        return $this->load->database('admin', TRUE);
    }

}