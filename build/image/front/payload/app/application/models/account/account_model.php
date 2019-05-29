<?php

include_once 'common_model.php';        //加载数据库操作类
include_once dirname(__DIR__) . '/ali_sms.php';

class Account_Model extends Account_Common_Model
{
    public function __construct()
    {
        header('Content-Type: text/html; charset=utf-8');
        parent::__construct();
    }

    /************************************************
     * common function
     *************************************************/

    /*
        获取用户信息
    */
    public function getUserInfo($arrData)
    {
        $timestamp = time();
        $result = array();

        if (!isset($arrData['open_id']) || $arrData['open_id'] == G_CONST::EMPTY_STRING) {
            log_message('error', "function(getUserInfo):lack of open_id" . " in file" . __FILE__ . " on Line " . __LINE__);
            return $this->missingPrameterArr("open_id");
        }

        $open_id = $arrData['open_id'];

        //判断open_id是否存在
        $account_where = 'open_id="' . $open_id . '"';
        $account_sql = 'select account_id,nickname,headimgurl,phone,is_manage_on from ' . WX_Account . ' where ' . $account_where . '';
        $dealerDB = Game_CONST::DBConst_Name;
        $account_query = $this->getDataBySql($dealerDB, 1, $account_sql);
        if (DB_CONST::DATA_NONEXISTENT == $account_query) {
            log_message('error', "function(getUserInfo):lack of account_id" . " in file" . __FILE__ . " on Line " . __LINE__);
            return array('result' => OPT_CONST::FAILED, 'data' => $result, 'result_message' => "账号不存在");
        } else {
            $account_id = $account_query['account_id'];
            $nickname = $account_query['nickname'];
            $headimgurl = $account_query['headimgurl'];
            $phone = $account_query['phone'] ? $account_query['phone'] : "";
        }

        $my_user_code = "";

        $user_code = isset($arrData['user_code']) ? $arrData['user_code'] : "";

        $bind_request['user_code'] = $user_code;
        $bind_request['account_id'] = $account_id;
        $my_user_code = $this->bindUserCode($bind_request);

        //$this->tryJoinGuild($bind_request);


        $result['account_id'] = $account_id;
        $result['nickname'] = $nickname;
        $result['headimgurl'] = $headimgurl;
        $result['open_id'] = $open_id;
        $result['phone'] = $phone;
        $result['user_code'] = $my_user_code;
        $result['is_manage_on'] = isset($account_query['is_manage_on']) ? $account_query['is_manage_on'] : 0;

        return array('result' => OPT_CONST::SUCCESS, 'data' => $result, 'result_message' => "获取用户信息");
    }


    /*
        获取用户信息
    */
    protected function bindUserCode($arrData)
    {
        $timestamp = time();
        $user_code = $arrData['user_code'];
        $account_id = $arrData['account_id'];
        $dealerDB = Game_CONST::DBConst_Name;

        $my_user_code = "";
        //获取用户user_code
        $dist_where = 'account_id=' . $account_id . ' and is_delete=0';
        $dist_sql = 'select user_code,recommend_code from ' . WX_Account . ' where ' . $dist_where . '';
        $account_query = $this->getDataBySql($dealerDB, 1, $dist_sql);
        if (DB_CONST::DATA_NONEXISTENT != $account_query)    //老用户，直接取分销码
        {
            $my_user_code = $account_query['user_code'];
            $recommend_code = $account_query['recommend_code'];
            if ($recommend_code == -1) {
                if ($user_code > 0) {
                    $update_str = 'recommend_code="' . $user_code . '",update_time=' . $timestamp;
                    $update_where = 'account_id=' . $account_id;
                    $update_query = $this->changeNodeValue($dealerDB, WX_Account, $update_str, $update_where);
                }
            }
        } else {
            log_message('error', "function(bindUserCode):account not exist" . " in file" . __FILE__ . " on Line " . __LINE__);
        }
        return $my_user_code;
    }

    /*
        尝试加入加入公会
    */
    public function tryJoinGuild($arrData)
    {
        $timestamp = time();
        $user_code = $arrData['user_code'];
        $account_id = $arrData['account_id'];
        $dealerDB = Game_CONST::DBConst_Name;

        //获取邀请函公会账号信息
        $member_where = 'code="' . $user_code . '" and level=2 and is_delete=0';
        $member_sql = 'select member_id,account_id,group_id,level from ' . Guild_Member . ' where ' . $member_where . ' limit 1';
        $member_query = $this->getDataBySql($dealerDB, 1, $member_sql);
        if (DB_CONST::DATA_NONEXISTENT == $member_query) {
            return false; //不是会长直接返回
        }
        $group_id = $member_query['group_id'];

        //获取用户是否加入公会
        $join_where = 'account_id=' . $account_id . ' and is_delete=0';
        $join_sql = 'select member_id from ' . Guild_Member . ' where ' . $join_where . ' limit 1';
        $join_query = $this->getDataBySql($dealerDB, 1, $join_sql);
        if (DB_CONST::DATA_NONEXISTENT == $join_query)  //未加入公会的用户
        {
            $insert_array['create_time'] = $timestamp;
            $insert_array['create_appid'] = "aid_" . $account_id;
            $insert_array['update_time'] = $timestamp;
            $insert_array['update_appid'] = "aid_" . $account_id;
            $insert_array['is_delete'] = 0;
            $insert_array['account_id'] = $account_id;
            $insert_array['group_id'] = $group_id;
            $insert_array['level'] = 0;
            $insert_array['vice_president'] = -1;
            $insert_array['code'] = -1;
            $member_id = $this->getInsertID($dealerDB, Guild_Member, $insert_array);
            return true;
        }
    }

    public function httpGet($url)
    {
        $curl = curl_init();
        curl_setopt($curl, CURLOPT_RETURNTRANSFER, true);
        curl_setopt($curl, CURLOPT_TIMEOUT, 500);
        // 为保证第三方服务器与微信服务器之间数据传输的安全性，所有微信接口采用https方式调用，必须使用下面2行代码打开ssl安全校验。
        // 如果在部署过程中代码在此处验证失败，请到 http://curl.haxx.se/ca/cacert.pem 下载新的证书判别文件。
        curl_setopt($curl, CURLOPT_SSL_VERIFYPEER, true);
        curl_setopt($curl, CURLOPT_SSL_VERIFYHOST, 0);
        curl_setopt($curl, CURLOPT_URL, $url);

        $res = curl_exec($curl);
        curl_close($curl);

        return $res;
    }


    public function getTicketGoodsList()
    {
        $timestamp = time();
        $result = array();

        $dealerDB = Game_CONST::DBConst_Name;

        //获取商品明细
        $goods_where = 'is_delete=' . G_CONST::IS_FALSE;
        $goods_sql = 'select goods_id,title,price,ticket_count from ' . Payment_Goods . ' where ' . $goods_where . ' order by ticket_count asc';
        $goods_query = $this->getDataBySql($dealerDB, 0, $goods_sql);
        if ($goods_query != DB_CONST::DATA_NONEXISTENT) {
            $result = $goods_query;

        }

        return array('result' => OPT_CONST::SUCCESS, 'data' => $result, 'result_message' => "获取商品列表");
    }

    public function getScoreStatistics($arrData)
    {
        $timestamp = time();
        $result = array();

        if (!isset($arrData['account_id']) || $arrData['account_id'] == G_CONST::EMPTY_STRING) {
            log_message('error', "function(getScoreStatistics):lack of account_id" . " in file" . __FILE__ . " on Line " . __LINE__);
            return $this->missingPrameterArr("account_id");
        }
        if (!isset($arrData['from'])) {
            log_message('error', "function(getScoreStatistics):lack of from" . " in file" . __FILE__ . " on Line " . __LINE__);
            return $this->missingPrameterArr("from");
        }
        if (!isset($arrData['to']) || $arrData['to'] == G_CONST::EMPTY_STRING) {
            log_message('error', "function(getScoreStatistics):lack of to" . " in file" . __FILE__ . " on Line " . __LINE__);
            return $this->missingPrameterArr("to");
        }

        $dealerDB = Game_CONST::DBConst_Name;


        $account_id = $arrData['account_id'];
        $from_timestamp = $arrData['from'];
        $to_timestamp = $arrData['to'];

        $account_id = $arrData['account_id'];
        $from = $arrData['from'];
        $to = $arrData['to'];

        $from_timestamp = 0;
        if ($from != "") {
            $from_timestamp = strtotime($from);
        }
        $to_timestamp = strtotime($to) + 86399;

        //游戏类型 ：1炸金花  2斗地主  3梭哈  4德州  5斗牛 6广东麻将
        $type_array = array("1" => 0, "2" => 0, "5" => 0, "6" => 0, "9" => 0);
        $sql = 'select game_type,board from ' . Room_Scoreboard . ' where create_time>=' . $from_timestamp . ' and create_time<=' . $to_timestamp . ' and board like "%\"' . $account_id . '\":%" and is_delete=0';
        $query = $this->getDataBySql($dealerDB, 0, $sql);
        if ($query != DB_CONST::DATA_NONEXISTENT) {
            foreach ($query as $item) {
                $game_type = $item['game_type'];

                if (!isset($type_array[$game_type])) {
                    $type_array[$game_type] = 0;
                }

                $board_ary = json_decode($item['board'], true);
                if (is_array($board_ary) && isset($board_ary[$account_id])) {
                    $type_array[$game_type] += $board_ary[$account_id];
                }
            }
        }

        foreach ($type_array as $game_type => $score) {
            $result[] = array("game_type" => $game_type, "score" => $score);
        }


        return array('result' => OPT_CONST::SUCCESS, 'data' => $result, 'result_message' => "获取积分列表");
    }


    /*
        个人主页(带公会系统)
    */
    public function getUserInfoGuild($arrData)
    {
        $timestamp = time();
        $result = array();

        if (!isset($arrData['open_id']) || $arrData['open_id'] == G_CONST::EMPTY_STRING) {
            log_message('error', "function(getUserInfoGuild):lack of open_id" . " in file" . __FILE__ . " on Line " . __LINE__);
            return $this->missingPrameterArr("open_id");
        }

        $open_id = $arrData['open_id'];

        $dealerDB = Game_CONST::DBConst_Name;

        //判断open_id是否存在
        $account_where = 'open_id="' . $open_id . '"';
        $account_sql = 'select account_id,nickname,headimgurl,phone from ' . WX_Account . ' where ' . $account_where . '';
        $account_query = $this->getDataBySql($dealerDB, 1, $account_sql);
        if (DB_CONST::DATA_NONEXISTENT == $account_query) {
            log_message('error', "function(getUserInfoGuild):账号不存在" . " in file" . __FILE__ . " on Line " . __LINE__);
            return array('result' => OPT_CONST::FAILED, 'data' => $result, 'result_message' => "账号不存在");
        } else {
            $account_id = $account_query['account_id'];
            $nickname = $account_query['nickname'];
            $headimgurl = $account_query['headimgurl'];
            $phone = $account_query['phone'];
            if ($phone == null) {
                $phone = "";
            }
        }

        //获取是否公会会长
        $level = -1;
        $invite_code = "";
        $group_id = -1;

        $member_where = 'account_id=' . $account_id . ' and is_delete=0';
        $member_sql = 'select group_id,level,code from ' . Guild_Member . ' where ' . $member_where . ' limit 1';
        $member_query = $this->getDataBySql($dealerDB, 1, $member_sql);
        if (DB_CONST::DATA_NONEXISTENT != $member_query) {
            $level = $member_query['level'];
            $group_id = $member_query['group_id'];
            $invite_code = $member_query['code'];
        }

        $result['level'] = $level;
        $result['group_id'] = $group_id;
        $result['invite_code'] = $invite_code;

        $result['account_id'] = $account_id;
        $result['nickname'] = $nickname;
        $result['headimgurl'] = $headimgurl;
        $result['open_id'] = $open_id;
        $result['phone'] = $phone;

        return array('result' => OPT_CONST::SUCCESS, 'data' => $result, 'result_message' => "获取用户信息");
    }


    //获取手机验证码
    public function _getMobileSms($arrData)
    {
        $timestamp = time();
        $result = array();

        if (!isset($arrData['open_id']) || $arrData['open_id'] === G_CONST::EMPTY_STRING) {
            log_message('error', "function(getMobileSms):lack of open_id" . " in file" . __FILE__ . " on Line " . __LINE__);
            return $this->missingPrameterArr("open_id");
        }
        if (!isset($arrData['phone']) || $arrData['phone'] === G_CONST::EMPTY_STRING) {
            log_message('error', "function(getMobileSms):lack of phone" . " in file" . __FILE__ . " on Line " . __LINE__);
            return $this->missingPrameterArr("phone");
        }
        $open_id = $arrData['open_id'];
        $mobile = $arrData['phone'];

        $dealerDB = Game_CONST::DBConst_Name;

        //判断open_id是否存在
        $account_phone = "";
        $account_where = 'open_id="' . $open_id . '"';
        $account_sql = 'select account_id,phone from ' . WX_Account . ' where ' . $account_where . '';
        $account_query = $this->getDataBySql($dealerDB, 1, $account_sql);
        if (DB_CONST::DATA_NONEXISTENT == $account_query) {
            log_message('error', "function(getMobileSms):account not exist" . " in file" . __FILE__ . " on Line " . __LINE__);
            return array('result' => OPT_CONST::FAILED, 'data' => $result, 'result_message' => "账号不存在");
        } else {
            $account_id = $account_query['account_id'];
            $account_phone = $account_query['phone'];
        }

        if ($mobile == $account_phone) {
            return array('result' => OPT_CONST::FAILED, 'data' => $result, 'result_message' => "你已绑定该手机号码");
        }

        //判断手机是否已被绑定
        $mobile_where = 'phone="' . $mobile . '" and is_delete=0';
        $mobile_sql = 'select account_id from ' . WX_Account . ' where ' . $mobile_where . '';
        $mobile_query = $this->getDataBySql($dealerDB, 1, $mobile_sql);
        if (DB_CONST::DATA_NONEXISTENT != $mobile_query) {
            //不能绑定已绑定的手机
            log_message('error', "function(getMobileSms):phone had bind" . " in file" . __FILE__ . " on Line " . __LINE__);
            return array('result' => OPT_CONST::FAILED, 'data' => $result, 'result_message' => "该手机号码已被占用，请用非代理商账号同步");
        }

        //判断验证码
        $sms_sql = 'select create_time from ' . Sms_Detail . ' where mobile="' . $mobile . '" and is_delete=0';
        $sms_query = $this->getDataBySql($dealerDB, 1, $sms_sql);
        if (DB_CONST::DATA_NONEXISTENT != $sms_query) {
            if ($sms_query['create_time'] + 900 >= $timestamp) {
                log_message('error', "function(getMobileSms):获取验证码频率太高：" . $mobile . " in file" . __FILE__ . " on Line " . __LINE__);
                return array('result' => OPT_CONST::FAILED, 'data' => $result, 'result_message' => "获取验证码频率太高");
            }
        }

        //生成验证码
        $identifyingCode = $this->createIdentifyingCode();
        //$identifyingCode = "55555";

        $array['mobile'] = $mobile;
        $array['identifying_code'] = $identifyingCode;
        $array['type'] = 1;    //1绑定
        $array['create_time'] = $timestamp;
        $array['invaild_time'] = $timestamp + 900;
        $array['is_delete'] = G_CONST::IS_FALSE;
        $array['extra'] = $open_id;

        $sms_id = $this->getInsertID($dealerDB, Sms_Detail, $array);

        //发送短信
        $content = "您的验证码是：%identifyingCode%。有效时间15分钟";
        $replaceArr = array("%identifyingCode%" => $identifyingCode);
        $content = strtr($content, $replaceArr);
        //$smsResult = SMS::sendSMS_CL($mobile,$content);

        //AliSms::sendSMS('相识相聚', 'SMS_145295401', $mobile, ['code' => $identifyingCode]);
        SMSTool::SendAuthCode($mobile, $identifyingCode);

        return array('result' => OPT_CONST::SUCCESS, 'data' => $result, 'result_message' => "获取手机验证码");
    }

    //获取手机验证码
    public function getMobileSms($arrData)
    {
        $timestamp = time();
        $result = array();

        if (!isset($arrData['open_id']) || $arrData['open_id'] === G_CONST::EMPTY_STRING) {
            log_message('error', "function(getMobileSms):lack of open_id" . " in file" . __FILE__ . " on Line " . __LINE__);
            return $this->missingPrameterArr("open_id");
        }
        if (!isset($arrData['phone']) || $arrData['phone'] === G_CONST::EMPTY_STRING) {
            log_message('error', "function(getMobileSms):lack of phone" . " in file" . __FILE__ . " on Line " . __LINE__);
            return $this->missingPrameterArr("phone");
        }
        $open_id = $arrData['open_id'];
        $mobile = $arrData['phone'];

        $dealerDB = Game_CONST::DBConst_Name;

        //判断open_id是否存在
        $account_where = 'open_id="' . $open_id . '"';
        $account_sql = 'select account_id,phone from ' . WX_Account . ' where ' . $account_where . '';
        $account_query = $this->getDataBySql($dealerDB, 1, $account_sql);
        if (DB_CONST::DATA_NONEXISTENT == $account_query) {
            log_message('error', "function(getMobileSms):account not exist" . " in file" . __FILE__ . " on Line " . __LINE__);
            return array('result' => OPT_CONST::FAILED, 'data' => $result, 'result_message' => "账号不存在");
        }

        if ($mobile == $account_query['phone']) {
            // 当前账号已经绑定该手机号
            return array('result' => OPT_CONST::FAILED, 'data' => $result, 'result_message' => "你已绑定该手机号码");
        }

        //判断验证码
        $sms_sql = 'select create_time from ' . Sms_Detail . ' where mobile="' . $mobile . '" and is_delete=0';
        $sms_query = $this->getDataBySql($dealerDB, 1, $sms_sql);
        if (DB_CONST::DATA_NONEXISTENT != $sms_query) {
            if ($sms_query['create_time'] + 60 >= $timestamp) {
                log_message('error', "function(getMobileSms):获取验证码频率太高：" . $mobile . " in file" . __FILE__ . " on Line " . __LINE__);
                return array('result' => OPT_CONST::FAILED, 'data' => $result, 'result_message' => "获取验证码频率太高");
            }
        }

        //生成验证码
        $identifyingCode = $this->createIdentifyingCode();

        $array['mobile'] = $mobile;
        $array['identifying_code'] = $identifyingCode;
        $array['type'] = 1;    //1绑定
        $array['create_time'] = $timestamp;
        $array['invaild_time'] = $timestamp + 900;
        $array['is_delete'] = G_CONST::IS_FALSE;
        $array['extra'] = $open_id;

        $this->getInsertID($dealerDB, Sms_Detail, $array);

        //发送短信
        AliSms::sendSMS('相识相聚', 'SMS_145295401', $mobile, ['code' => $identifyingCode]);

        return array('result' => OPT_CONST::SUCCESS, 'data' => $result, 'result_message' => "获取手机验证码");
    }


    //绑定新手机
    public function checkSmsCode($arrData)
    {
        $timestamp = time();
        $result = array();

        if (!isset($arrData['open_id']) || $arrData['open_id'] === G_CONST::EMPTY_STRING) {
            log_message('error', "function(checkSmsCode):lack of open_id" . " in file" . __FILE__ . " on Line " . __LINE__);
            return $this->missingPrameterArr("open_id");
        }
        if (!isset($arrData['phone']) || $arrData['phone'] === G_CONST::EMPTY_STRING) {
            log_message('error', "function(checkSmsCode):lack of phone" . " in file" . __FILE__ . " on Line " . __LINE__);
            return $this->missingPrameterArr("phone");
        }
        if (!isset($arrData['code']) || $arrData['code'] === G_CONST::EMPTY_STRING) {
            log_message('error', "function(checkSmsCode):lack of code" . " in file" . __FILE__ . " on Line " . __LINE__);
            return $this->missingPrameterArr("code");
        }
        $open_id = $arrData['open_id'];
        $mobile = $arrData['phone'];
        $code = $arrData['code'];

        $dealerDB = Game_CONST::DBConst_Name;

        //判断open_id是否存在
        $account_where = 'open_id="' . $open_id . '"';
        $account_sql = 'select account_id from ' . WX_Account . ' where ' . $account_where . '';
        $account_query = $this->getDataBySql($dealerDB, 1, $account_sql);
        if (DB_CONST::DATA_NONEXISTENT == $account_query) {
            log_message('error', "function(checkSmsCode):account not exist" . " in file" . __FILE__ . " on Line " . __LINE__);
            return array('result' => OPT_CONST::FAILED, 'data' => $result, 'result_message' => "账号不存在");
        } else {
            $account_id = $account_query['account_id'];
        }

        //判断验证码
        $sms_sql = 'select sms_id,invaild_time from ' . Sms_Detail . ' where mobile="' . $mobile . '" and identifying_code="' . $code . '" and type=1 and is_delete=0 order by sms_id desc';
        $sms_query = $this->getDataBySql($dealerDB, 1, $sms_sql);
        if (DB_CONST::DATA_NONEXISTENT == $sms_query) {
            log_message('error', "function(checkSmsCode):sms not exist" . " in file" . __FILE__ . " on Line " . __LINE__);
            return array('result' => OPT_CONST::FAILED, 'data' => $result, 'result_message' => "验证码错误");
        }
        $sms_id = $sms_query['sms_id'];
        $invaild_time = $sms_query['invaild_time'];
        if ($timestamp > $invaild_time) {
            log_message('error', "function(checkSmsCode):sms timeout" . " in file" . __FILE__ . " on Line " . __LINE__);
            return array('result' => OPT_CONST::FAILED, 'data' => $result, 'result_message' => "验证码已过期");
        }

        $account_ticket_count = 0;
        //判断手机是否已被绑定
        $return_aid = -1;
        $bind_accountid = -1;
        $is_bind = 0;
        $mobile_where = 'phone="' . $mobile . '" and is_delete=0';
        $mobile_sql = 'select account_id from ' . WX_Account . ' where ' . $mobile_where . '';
        $mobile_query = $this->getDataBySql($dealerDB, 1, $mobile_sql);
        if (DB_CONST::DATA_NONEXISTENT != $mobile_query) {
            $bind_accountid = $mobile_query['account_id'];
            $is_bind = 1;
        }

        //新手机
        if ($is_bind == 0) {
            $return_aid = $account_id;
            //绑定新账号
            $bind_array['phone'] = $mobile;
            $this->updateFunc($dealerDB, "account_id", $account_id, WX_Account, $bind_array);
        } else {
            $return_aid = $bind_accountid;

            //修改当前账号绑定的手机
            $update_array['update_time'] = $timestamp;
            $update_array['update_appid'] = $open_id;
            $update_array['phone'] = $mobile;
            $this->updateFunc($dealerDB, "account_id", $account_id, WX_Account, $update_array);
        }

        //删除短信记录
        $updateSms_str = 'is_delete=1';
        $updateSms_where = 'mobile="' . $mobile . '" and type=1 and is_delete=0';
        $this->changeNodeValue($dealerDB, Sms_Detail, $updateSms_str, $updateSms_where);

        $result['card_count'] = 0;
        $result['account_id'] = $return_aid;

        return array('result' => OPT_CONST::SUCCESS, 'data' => $result, 'result_message' => "绑定成功");

    }

    public function getUserInfoById($account_id)
    {
        $user_info = $this->db()->where('account_id', $account_id)->get('wechat_account')->row();
        return $user_info;
    }

    public function getAccountsByMobile($phone)
    {
        $result = [];

        if ($phone == G_CONST::EMPTY_STRING) {
            return array('result' => OPT_CONST::FAILED, 'data' => $result, 'result_message' => "没有可转移的账号");
        }

        $where = ' and phone="' . $phone . '"';
        $sql = 'select account_id,nickname,headimgurl from ' . WX_Account . ' where is_delete = 0' . $where;
        $dealerDB = Game_CONST::DBConst_Name;
        $account_query = $this->getDataBySql($dealerDB, 0, $sql);
        if (DB_CONST::DATA_NONEXISTENT == $account_query) {
            log_message('error', "function(getUserInfo):lack of account_id" . " in file" . __FILE__ . " on Line " . __LINE__);
            return array('result' => OPT_CONST::FAILED, 'data' => $result, 'result_message' => "没有可转移的账号");
        }
        foreach ($account_query as $k => $account) {
            $ticket_count = $this->getRoomTicket($account['account_id']);
            $account_query[$k]['ticket_count'] = $ticket_count;
            $account_query[$k]['is_cantransfer'] = $ticket_count == 0 ? 0 : 1;
        }
        $result = $account_query;

        return ['result' => OPT_CONST::SUCCESS, 'data' => $result, 'result_message' => '绑定用户列表'];
    }

    /**
     * 转移房卡
     *
     * @param array $user 当前登录的用户
     * @param integer $from_id 从哪个用户转移
     * @param integer $to_id 转移到哪个用户
     * @return array
     */
    public function transferRoomCard($user, $from_id, $to_id)
    {
        $result = [];

        if ($user['phone'] == G_CONST::EMPTY_STRING) {
            return array('result' => OPT_CONST::FAILED, 'data' => $result, 'result_message' => "当前账户未绑定手机号");
        }

        $timestamp = time();
        $dealerDB = Game_CONST::DBConst_Name;

        // 转移人
        $account_where = 'is_delete = 0 and account_id = ' . $from_id . ' and phone = ' . $user['phone'];
        $account_sql = 'select nickname,headimgurl from ' . WX_Account . ' where ' . $account_where;
        $fromUser = $this->getDataBySql($dealerDB, 1, $account_sql);
        if (DB_CONST::DATA_NONEXISTENT == $fromUser) {
            return array('result' => OPT_CONST::FAILED, 'data' => $result, 'result_message' => "转移人账号不存在");
        }
        $transferTicket = $this->getRoomTicket($from_id);
        if (0 == $transferTicket) {
            return array('result' => OPT_CONST::FAILED, 'data' => $result, 'result_message' => "转移人没有房卡");
        }

        // 接收人
        $account_where = 'is_delete = 0 and account_id = ' . $to_id . ' and phone = ' . $user['phone'];
        $account_sql = 'select nickname,headimgurl from ' . WX_Account . ' where ' . $account_where;
        $toUser = $this->getDataBySql($dealerDB, 1, $account_sql);
        if (DB_CONST::DATA_NONEXISTENT == $toUser) {
            return array('result' => OPT_CONST::FAILED, 'data' => $result, 'result_message' => "接收人账号不存在");
        }

        // 转移人 房卡置0
        $updateTicket_str = 'update_time=' . $timestamp . ',update_appid="aid_' . $user['account_id'] . '",ticket_count=0';
        $updateTicket_where = 'account_id=' . $from_id;
        $this->changeNodeValue($dealerDB, Room_Ticket, $updateTicket_str, $updateTicket_where);

        $journal_from = array();
        $journal_from['journal_type'] = Game_CONST::JournalType_Disburse;
        $journal_from['account_id'] = $from_id;
        $journal_from['object_type'] = Game_CONST::ObjectType_Transfer;
        $journal_from['object_id'] = 0; // 产生对象，【发红包-收红包】时，为对应的发红包ID、收红包ID，此处先设为0
        $journal_from['ticket_count'] = $transferTicket;
        $journal_from['extra'] = $fromUser['nickname'] . ' ==> ' . $toUser['nickname'];
        $this->updateRoomTicketJournal($journal_from, $dealerDB);

        // 接收人 房卡增加
        $updateTicket_str = 'update_time=' . $timestamp . ',update_appid="aid_' . $user['account_id'] . '",ticket_count=ticket_count+' . $transferTicket;
        $updateTicket_where = 'account_id=' . $to_id;
        $this->changeNodeValue($dealerDB, Room_Ticket, $updateTicket_str, $updateTicket_where);

        //房卡流水账
        $journal_to = array();
        $journal_to['journal_type'] = Game_CONST::JournalType_Income;
        $journal_to['account_id'] = $to_id;
        $journal_to['object_type'] = Game_CONST::ObjectType_Transfer;
        $journal_to['object_id'] = 0;
        $journal_to['ticket_count'] = $transferTicket;
        $journal_to['extra'] = $fromUser['nickname'] . ' ==> ' . $toUser['nickname'];

        $this->updateRoomTicketJournal($journal_to, $dealerDB);

        return array('result' => OPT_CONST::SUCCESS, 'data' => $result, 'result_message' => "转移成功");
    }

    private function getRoomTicket($account_id)
    {
        $dealerDB = Game_Const::DBConst_Name;
        $timestamp = time();
        $ticket_count = 0;

        $ticket_where = 'account_id=' . $account_id . '';
        $ticket_sql = 'select ticket_count from ' . Room_Ticket . ' where ' . $ticket_where . '';
        $ticket_query = $this->getDataBySql($dealerDB, 1, $ticket_sql);
        if (DB_CONST::DATA_NONEXISTENT == $ticket_query) {
            //默认添加房卡
            $ticket_array['create_time'] = $timestamp;
            $ticket_array['create_appid'] = $account_id;
            $ticket_array['update_time'] = $timestamp;
            $ticket_array['update_appid'] = $account_id;
            $ticket_array['is_delete'] = G_CONST::IS_FALSE;
            $ticket_array['account_id'] = $account_id;
            $ticket_array['ticket_count'] = 0;

            $this->getInsertID($dealerDB, Room_Ticket, $ticket_array);
        } else {
            $ticket_count = $ticket_query['ticket_count'];
        }

        return $ticket_count;
    }

    public function getCreateInfo($account_id)
    {
        $create_info = '';
        $dealerDB = Game_CONST::DBConst_Name;
        $sql = 'select create_info from ' . Room_Create_Info . ' where account_id = ' . $account_id;
        $res = $this->getDataBySql($dealerDB, 1, $sql);
        if (DB_CONST::DATA_NONEXISTENT !== $res) {
            $create_info = json_encode($res['create_info']);
        }
        return $create_info;
    }

    public function storeCreateInfo($account_id, $create_info)
    {
        $dealerDB = Game_CONST::DBConst_Name;
        $sql = 'select info_id from ' . Room_Create_Info . ' where account_id = ' . $account_id;
        $res = $this->getDataBySql($dealerDB, 1, $sql);
        if (DB_CONST::DATA_NONEXISTENT == $res) {
            $data = [
                'account_id' => $account_id,
                'create_info' => json_encode($create_info),
                'create_time' => time()
            ];
            $this->getInsertID($dealerDB, Room_Create_Info, $data);
        } else {
            $data = [
                'create_info' => json_encode($create_info),
                'update_time' => time()
            ];
            $this->updateFunc($dealerDB, 'account_id', $account_id, Room_Create_Info, $data);
        }

        return ['result' => DB_CONST::SUCCESS, 'data' => [], 'result_message' => '开房设置保存成功'];
    }
}
