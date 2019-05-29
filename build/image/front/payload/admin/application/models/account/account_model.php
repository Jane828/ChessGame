<?php

include_once 'common_model.php';        //加载数据库操作类
include_once dirname(__DIR__) . '/sms_tool.php';

class Account_Model extends Account_Common_Model {
    public function __construct() {
        header('Content-Type: text/html; charset=utf-8');
        parent::__construct();
    }

    /************************************************
     * common function
     *************************************************/

    /*
        管理员登陆
    */
    public function adminLoginOpt($arrData) {
        $result    = array();
        $timestamp = time();

        if (!isset($arrData['account']) || $arrData['account'] === "") {
            log_message('error', "function(adminLoginOpt):lack of adminLoginOpt" . " in file" . __FILE__ . " on Line " . __LINE__);
            return $this->missingPrameterArr("account");
        }
        if (!isset($arrData['pwd']) || $arrData['pwd'] === "") {
            log_message('error', "function(adminLoginOpt):lack of pwd" . " in file" . __FILE__ . " on Line " . __LINE__);
            return $this->missingPrameterArr("pwd");
        }

        if (!isset($arrData['phone']) || $arrData['phone'] === "") {
            log_message('error', "function(loginOpt):lack of phone" . " in file" . __FILE__ . " on Line " . __LINE__);
            return $this->missingPrameterArr("phone");
        }

        if (!isset($arrData['auth_code']) || $arrData['auth_code'] === "") {
            log_message('error', "function(loginOpt):lack of authCode" . " in file" . __FILE__ . " on Line " . __LINE__);
            return $this->missingPrameterArr("authCode");
        }

        $account = $arrData['account'];
        $pwd     = $arrData['pwd'];
        $phone    = $arrData['phone'];
        $authCode = $arrData['auth_code'];

        $dealerDB = "admin";

        $dealer_where = 'account="' . $account . '" and is_delete=0';
        $dealer_sql   = 'select dealer_id, passwd, phone from ' . D_Dealer_Account . ' where ' . $dealer_where;
        $dealer_query = $this->getDataBySql($dealerDB, 1, $dealer_sql);
        if ($dealer_query == DB_CONST::DATA_NONEXISTENT) {
            $result["err_type"] = -1;
            return array('result' => OPT_CONST::FAILED, 'data' => $result, 'result_message' => "账号错误");
        }

        if ($pwd != $dealer_query["passwd"]){
            $result["err_type"] = -2;
            return array('result' => OPT_CONST::FAILED, 'data' => $result, 'result_message' => "密码错误");
        }

        if ($phone != $dealer_query["phone"]) {
            $result["err_type"] = -3;
            return array('result' => OPT_CONST::FAILED, 'data' => $result, 'result_message' => "账号跟手机号不匹配");
        }
        $checkData   = array(
            "phone" => $dealer_query["phone"],
            "type"  => G_CONST::SMS_Type_Admin_Login,
            "code"  => $authCode,
        );
        $checkResult = $this->checkSmsCode($checkData);
        if ($checkResult["result"] == OPT_CONST::FAILED) {
            $checkResult["data"]["err_type"] = -4;
            return $checkResult;
        }

        $_SESSION['LoginAdminID'] = $dealer_query['dealer_id'];
        $_SESSION['LoginUser']    = $account;

        return array('result' => OPT_CONST::SUCCESS, 'data' => $result, 'result_message' => "登陆成功");
    }


    /*
        代理商登陆
    */
    public function loginOpt($arrData) {
        $result    = array();
        $timestamp = time();

        if (!isset($arrData['account']) || $arrData['account'] === "") {
            log_message('error', "function(loginOpt):lack of account" . " in file" . __FILE__ . " on Line " . __LINE__);
            return $this->missingPrameterArr("account");
        }
        if (!isset($arrData['pwd']) || $arrData['pwd'] === "") {
            log_message('error', "function(loginOpt):lack of pwd" . " in file" . __FILE__ . " on Line " . __LINE__);
            return $this->missingPrameterArr("pwd");
        }

        if (!isset($arrData['phone']) || $arrData['phone'] === "") {
            log_message('error', "function(loginOpt):lack of phone" . " in file" . __FILE__ . " on Line " . __LINE__);
            return $this->missingPrameterArr("phone");
        }

        if (!isset($arrData['auth_code']) || $arrData['auth_code'] === "") {
            log_message('error', "function(loginOpt):lack of authCode" . " in file" . __FILE__ . " on Line " . __LINE__);
            return $this->missingPrameterArr("authCode");
        }

        $account  = $arrData['account'];
        $pwd      = $arrData['pwd'];
        $phone    = $arrData['phone'];
        $authCode = $arrData['auth_code'];

        $dealerDB = "admin";

        $dealer_where = 'account="' . $account . '" and passwd="' . $pwd . '" and is_delete=0';
        $dealer_sql   = 'select dealer_id,dealer_num, phone from ' . D_Dealer_Account . ' where ' . $dealer_where;
        $dealer_query = $this->getDataBySql($dealerDB, 1, $dealer_sql);
        if ($dealer_query == DB_CONST::DATA_NONEXISTENT) {
            return array('result' => OPT_CONST::FAILED, 'data' => $result, 'result_message' => "账号或密码错误");
        }

        if ($phone != $dealer_query["phone"]) {
            return array('result' => OPT_CONST::FAILED, 'data' => $result, 'result_message' => "账号跟手机号不匹配");
        }
        $checkData   = array(
            "phone" => $dealer_query["phone"],
            "type"  => G_CONST::SMS_Type_Admin_Login,
            "code"  => $authCode,
        );
        $checkResult = $this->checkSmsCode($checkData);
        if ($checkResult["result"] == OPT_CONST::FAILED) {
            $checkResult["data"]["err_type"] = -4;
            return $checkResult;
        }

        $_SESSION['LoginDealerID']  = $dealer_query['dealer_id'];
        $_SESSION['LoginDealerNum'] = $dealer_query['dealer_num'];

        return array('result' => OPT_CONST::SUCCESS, 'data' => $result, 'result_message' => "登陆成功");
    }

    /**
     * 函数描述：获取登录用的验证码
     * @param $arrData
     * @return array
     * author 黄欣仕
     * date 2019/1/24
     */
    public function getMobileSmsForLogin($arrData) {
        $result = array();
        if (!isset($arrData['account']) || $arrData['account'] === G_CONST::EMPTY_STRING) {
            log_message('error', "function(getMobileSms):lack of account" . " in file" . __FILE__ . " on Line " . __LINE__);
            return $this->missingPrameterArr("account");
        }

        if (!isset($arrData['phone']) || $arrData['phone'] === G_CONST::EMPTY_STRING) {
            log_message('error', "function(getMobileSms):lack of phone" . " in file" . __FILE__ . " on Line " . __LINE__);
            return $this->missingPrameterArr("phone");
        }

        $account  = $arrData['account'];
        $phone    = $arrData['phone'];
        $dealerDB = "admin";

        $dealer_where = 'account="' . $account . '"  and is_delete=0';
        $dealer_sql   = 'select dealer_id,dealer_num,phone from ' . D_Dealer_Account . ' where ' . $dealer_where;
        $dealer_query = $this->getDataBySql($dealerDB, 1, $dealer_sql);
        if ($dealer_query == DB_CONST::DATA_NONEXISTENT) {
            log_message('error', "not found user:" . $account . " in file" . __FILE__ . " on Line " . __LINE__);
            return array('result' => OPT_CONST::FAILED, 'data' => $result, 'result_message' => "账号错误");
        }

        if ($phone != $dealer_query["phone"]) {
            log_message('error', "mismatching:" . $account . " and " . $phone . " in file" . __FILE__ . " on Line " . __LINE__);
            return array('result' => OPT_CONST::FAILED, 'data' => $result, 'result_message' => "账号跟手机号不匹配");
        }

        $arrData["phone"] = $dealer_query["phone"];
        $arrData["type"]  = G_CONST::SMS_Type_Admin_Login;
        $arrData["extra"] = $account;

        return $this->getMobileSms($arrData);

    }

    /**
     * 函数描述：获取手机验证码
     * @param $arrData 数组数据，包含phone(手机号)、type(验证码类型：3是后台登录码)、extra(可选、短信记录描述或额外数据)
     * @return array
     *                 author 黄欣仕
     *                 date 2019/1/24
     */
    public function getMobileSms($arrData) {
        $timestamp = time();
        $result    = array();

        if (!isset($arrData['phone']) || $arrData['phone'] === G_CONST::EMPTY_STRING) {
            log_message('error', "function(getMobileSms):lack of phone" . " in file" . __FILE__ . " on Line " . __LINE__);
            return $this->missingPrameterArr("phone");
        }

        if (!isset($arrData['type']) || $arrData['type'] === G_CONST::EMPTY_STRING) {
            log_message('error', "function(getMobileSms):lack of type" . " in file" . __FILE__ . " on Line " . __LINE__);
            return $this->missingPrameterArr("type");
        }
        $mobile = $arrData['phone'];
        $type   = $arrData['type'];
        $extra  = "";

        if (isset($arrData['extra']) && $arrData['extra'] != G_CONST::EMPTY_STRING) {
            $extra = $arrData['extra'];
        }

        $dealerDB = "admin";

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

        $array['mobile']           = $mobile;
        $array['identifying_code'] = $identifyingCode;
        $array['type']             = $type; // type 3后台登入
        $array['create_time']      = $timestamp;
        $array['invaild_time']     = $timestamp + 900;
        $array['is_delete']        = G_CONST::IS_FALSE;
        $array['extra']            = $extra;
        $this->getInsertID($dealerDB, Sms_Detail, $array);

        //发送短信
        SMSTool::SendAuthCode($mobile, $identifyingCode);
        //AliSms::sendSMS('相识相聚', 'SMS_145295401', $mobile, ['code' => $identifyingCode]);
        $result["mobile"] = $mobile;

        return array('result' => OPT_CONST::SUCCESS, 'data' => $result, 'result_message' => "获取手机验证码");
    }

    public function checkSmsCode($arrData) {
        $timestamp = time();
        $result    = array();

        if (!isset($arrData['phone']) || $arrData['phone'] === G_CONST::EMPTY_STRING) {
            log_message('error', "function(checkSmsCode):lack of phone" . " in file" . __FILE__ . " on Line " . __LINE__);
            return $this->missingPrameterArr("phone");
        }

        if (!isset($arrData['code']) || $arrData['code'] === G_CONST::EMPTY_STRING) {
            log_message('error', "function(checkSmsCode):lack of code" . " in file" . __FILE__ . " on Line " . __LINE__);
            return $this->missingPrameterArr("code");
        }

        if (!isset($arrData['type']) || $arrData['type'] === G_CONST::EMPTY_STRING) {
            log_message('error', "function(checkSmsCode):lack of type" . " in file" . __FILE__ . " on Line " . __LINE__);
            return $this->missingPrameterArr("type");
        }
        $mobile = $arrData['phone'];
        $code   = $arrData['code'];
        $type   = $arrData['type'];

        $dealerDB = "admin";

        //判断验证码
        $sms_sql   = 'select sms_id,invaild_time from ' . Sms_Detail . ' where mobile="' . $mobile . '" and identifying_code="' . $code . '" and type= ' . $type . ' and is_delete=0 order by sms_id desc';
        $sms_query = $this->getDataBySql($dealerDB, 1, $sms_sql);
        if (DB_CONST::DATA_NONEXISTENT == $sms_query) {
            log_message('error', "function(checkSmsCode):sms not exist" . " in file" . __FILE__ . " on Line " . __LINE__);
            return array('result' => OPT_CONST::FAILED, 'data' => $result, 'result_message' => "验证码错误");
        }
        $sms_id       = $sms_query['sms_id'];
        $invaild_time = $sms_query['invaild_time'];
        if ($timestamp > $invaild_time) {
            log_message('error', "function(checkSmsCode):sms timeout" . " in file" . __FILE__ . " on Line " . __LINE__);
            return array('result' => OPT_CONST::FAILED, 'data' => $result, 'result_message' => "验证码已过期");
        }

        //删除短信记录
        $updateSms_str   = 'is_delete=1';
        $updateSms_where = 'mobile="' . $mobile . '" and type= ' . $type . ' and is_delete=0';
        $this->changeNodeValue($dealerDB, Sms_Detail, $updateSms_str, $updateSms_where);

        return array('result' => OPT_CONST::SUCCESS, 'data' => $result, 'result_message' => "验证成功");

    }

}