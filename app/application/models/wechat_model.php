<?php
error_reporting(E_ALL ^ E_NOTICE);

include_once('public_models.php');        //加载数据库操作类
require_once('pinyin/pinyin_model.php');

class Wechat_Model extends Public_Models {
    public function __construct() {
        header('Content-Type: text/html; charset=utf-8');
        parent::__construct();
    }

    /*
         生成随机字符串
     */
    private function createNonceStr() {
        $mtime      = explode(' ', microtime());
        $mTimestamp = $mtime[1] . substr($mtime[0], 2, 3);

        $order_code = $mTimestamp;

        for ($i = 0; $i < 6; $i++) {
            $order_code .= rand(0, 9);
        }

        return md5($order_code);
    }

    /*
         生成签名
    */
    private function getSign($parameters) {
        $stringA = "";

        $i = 0;
        foreach ($parameters as $key => $value) {
            if ($i == 0) {
                $stringA .= $key . "=" . $value;
            } else {
                $stringA .= "&" . $key . "=" . $value;
            }
            $i++;
        }

        $stringSignTemp = $stringA;
        $sign           = sha1($stringSignTemp);
        return $sign;
    }

    /*
        判断是否微信的
    */
    public function is_weixin() {
        if (strpos($_SERVER['HTTP_USER_AGENT'], 'MicroMessenger') !== FALSE) {
            return TRUE;
        }
        return FALSE;
    }

    private function httpGet($url) {
        $curl = curl_init();
        curl_setopt($curl, CURLOPT_RETURNTRANSFER, TRUE);
        curl_setopt($curl, CURLOPT_TIMEOUT, 500);
        // 为保证第三方服务器与微信服务器之间数据传输的安全性，所有微信接口采用https方式调用，必须使用下面2行代码打开ssl安全校验。
        // 如果在部署过程中代码在此处验证失败，请到 http://curl.haxx.se/ca/cacert.pem 下载新的证书判别文件。
        curl_setopt($curl, CURLOPT_SSL_VERIFYPEER, TRUE);
        curl_setopt($curl, CURLOPT_SSL_VERIFYHOST, 0);
        curl_setopt($curl, CURLOPT_URL, $url);

        $res = curl_exec($curl);
        curl_close($curl);

        return $res;
    }

    /*
        fsocketopen 请求
    */
    protected function doRequestWX($host, $path, $port, $param) {
        $query = isset($param) ? http_build_query($param) : '';

        $errno   = 0;
        $errstr  = '';
        $timeout = 10;

        $fp = fsockopen("ssl://" . $host, $port, $errno, $errstr, $timeout);

        $out = "POST " . $path . " HTTP/1.1\r\n";
        $out .= "host:" . $host . "\r\n";
        $out .= "content-length:" . strlen($query) . "\r\n";
        $out .= "content-type:application/x-www-form-urlencoded\r\n";
        $out .= "connection:close\r\n\r\n";
        $out .= $query;

        fputs($fp, $out);
        fclose($fp);

        return TRUE;
    }

    /********************************
     * function
     *********************************/
    /*
        jsapi config
    */
    public function getWxConfig() {
        if (!empty($_SERVER["REQUEST_URI"])) {
            $scrtName = $_SERVER["REQUEST_URI"];
            $nowurl   = ltrim($scrtName, "/");

            $nowurl = base_url($nowurl);
        } else {
            $scrtName = $_SERVER["PHP_SELF"];
            if (empty($_SERVER["QUERY_STRING"])) {
                $nowurl = $scrtName;
            } else {
                $nowurl = $scrtName . "?" . $_SERVER["QUERY_STRING"];
            }

            log_message('error', "createWxSignature:PHP_SELF:" . $nowurl . " in file" . __FILE__ . " on Line " . __LINE__);

            $nowurl = base_url($nowurl);
        }

        if ($this->is_weixin()) {
            $jsapi_ticket = $this->getJsapiTicket();
        } else {
            $jsapi_ticket = OPT_CONST::DATA_NONEXISTENT;
        }

        if (OPT_CONST::DATA_NONEXISTENT == $jsapi_ticket) {
            $jsapi_ticket = "";
        }

        $parameters['jsapi_ticket'] = $jsapi_ticket;
        $parameters['noncestr']     = $this->createNonceStr();
        $parameters['timestamp']    = time();
        $parameters['url']          = $nowurl;

        $signature = $this->getSign($parameters);

        //log_message('error', "createWxSignature:sgin:".$signature." in file".__FILE__." on Line ".__LINE__);

        $parameters['signature'] = $signature;
        $parameters['appId']     = Game_CONST::WX_Appid;
        $parameters['debug']     = "false";
        $parameters['nonceStr']  = $parameters['noncestr'];

        return $parameters;
    }

    /*
        获取getJsapiTicket
    */
    public function getJsapiTicket() {
        $timestamp = time();

        $wx_appid     = Game_CONST::WX_Appid;
        $wx_appsecret = Game_CONST::WX_AppSecret;
        $dealerDB     = Game_CONST::DBConst_Name;

        $jsapi_ticket_sql   = 'select update_time,wx_value from ' . WX_Parameter . ' where  `wx_key`="jsapi_ticket"';
        $jsapi_ticket_query = $this->getDataBySql($dealerDB, 1, $jsapi_ticket_sql);
        if (DB_CONST::DATA_NONEXISTENT == $jsapi_ticket_query) {
            $insert_array['is_delete']   = 0;
            $insert_array['update_time'] = 0;
            $insert_array['wx_key']      = "jsapi_ticket";
            $insert_array['wx_value']    = "";
            $data_id                     = $this->getInsertID($dealerDB, WX_Parameter, $insert_array);
            unset($insert_array);

            log_message('error', "function(getJsapiTicket):data_id:" . $data_id . " in file" . __FILE__ . " on Line " . __LINE__);

            $jsapi_ticket      = G_CONST::EMPTY_STRING;
            $jsapi_ticket_time = 0;
        } else {
            $jsapi_ticket      = $jsapi_ticket_query['wx_value'];
            $jsapi_ticket_time = $jsapi_ticket_query['update_time'];
        }

        if ($jsapi_ticket == G_CONST::EMPTY_STRING || $timestamp >= ($jsapi_ticket_time + 3600)) {
            log_message('error', "function(getJsapiTicket): jsapi_ticket:" . $jsapi_ticket . "   jsapi_ticket_time:" . $jsapi_ticket_time . " in file" . __FILE__ . " on Line " . __LINE__);

            $accessToken = $this->getAccessToken();
            // 如果是企业号用以下 URL 获取 ticket
            $url = "https://api.weixin.qq.com/cgi-bin/ticket/getticket?type=jsapi&access_token=$accessToken";

            $json_result = $this->httpGet($url);
            $result      = $this->splitJsonString($json_result);
            if ($result === FALSE) {    //返回结果不是json
                log_message('error', "function(getAccessToken):result is not a jsonstring" . " in file" . __FILE__ . " on Line " . __LINE__);
                return OPT_CONST::DATA_NONEXISTENT;
            } else if (isset($result['errcode']) && $result['errcode'] != 0) {
                $access_token = $this->getAccessToken($is_refresh = 1);
                $url         = "https://api.weixin.qq.com/cgi-bin/ticket/getticket?type=jsapi&access_token=$accessToken";
                $json_result = $this->httpGet($url);

                $result = $this->splitJsonString($json_result);
                if ($result === FALSE) {    //返回结果不是json
                    log_message('error', "function(getAccessToken):result is not a jsonstring" . " in file" . __FILE__ . " on Line " . __LINE__);
                    return OPT_CONST::DATA_NONEXISTENT;
                } else if (isset($result['errcode']) && $result['errcode'] != 0) {
                    //获取access_token失败
                    log_message('error', "function(getAccessToken):can not get accesstoken:" . $json_result . " in file" . __FILE__ . " on Line " . __LINE__);
                    return OPT_CONST::DATA_NONEXISTENT;
                } else {
                    $jsapi_ticket = $result["ticket"];
                }
            } else {
                $jsapi_ticket = $result["ticket"];
            }

            $update_str   = 'wx_value="' . $jsapi_ticket . '",update_time=' . $timestamp;
            $update_where = 'wx_key="jsapi_ticket"';
            $update_query = $this->changeNodeValue($dealerDB, WX_Parameter, $update_str, $update_where);
        }

        //返回access_token
        return $jsapi_ticket;
    }


    /*
        获取access_token
    */
    public function getAccessToken($is_refresh = 0) {
        $timestamp    = time();
        $wx_appid     = Game_CONST::WX_Appid;
        $wx_appsecret = Game_CONST::WX_AppSecret;
        $dealerDB     = Game_CONST::DBConst_Name;

        $access_token_sql   = 'select update_time,wx_value from ' . WX_Parameter . ' where  `wx_key`="access_token"';
        $access_token_query = $this->getDataBySql($dealerDB, 1, $access_token_sql);
        if ($access_token_query == DB_CONST::DATA_NONEXISTENT) {
            $insert_array['is_delete']   = 0;
            $insert_array['update_time'] = 0;
            $insert_array['wx_key']      = "access_token";
            $insert_array['wx_value']    = "";
            $data_id                     = $this->getInsertID($dealerDB, WX_Parameter, $insert_array);
            unset($insert_array);

            log_message('error', "function(getAccessToken):data_id:" . $data_id . " in file" . __FILE__ . " on Line " . __LINE__);

            $access_token      = G_CONST::EMPTY_STRING;
            $access_token_time = 0;
        } else {
            $access_token      = $access_token_query['wx_value'];
            $access_token_time = $access_token_query['update_time'];
        }
        if ($is_refresh == 1 || $access_token == G_CONST::EMPTY_STRING || $timestamp >= ($access_token_time + 3600)) {
            log_message('error', "function(getAccessToken):access_token:" . $access_token . "  access_token_time:" . $access_token_time . " in file" . __FILE__ . " on Line " . __LINE__);

            $http_url    = "https://api.weixin.qq.com/cgi-bin/token?grant_type=client_credential&appid=" . $wx_appid . "&secret=" . $wx_appsecret;
		log_message("info", "liuyong======".$http_url);
	    $json_result = $this->httpGet($http_url);
            $result      = $this->splitJsonString($json_result);
            if ($result === FALSE) {
                //返回结果不是json
                log_message('error', "function(getAccessToken):result is not a jsonstring" . " in file" . __FILE__ . " on Line " . __LINE__);
                return OPT_CONST::DATA_NONEXISTENT;
            } else if (isset($result['errcode'])) {
                //获取access_token失败
                log_message('error', "function(getAccessToken):can not get accesstoken:" . $json_result . " in file" . __FILE__ . " on Line " . __LINE__);
                return OPT_CONST::DATA_NONEXISTENT;
            } else {
                $access_token = $result["access_token"];
            }

            $update_str   = 'wx_value="' . $access_token . '",update_time=' . $timestamp;
            $update_where = 'wx_key="access_token"';
            $update_query = $this->changeNodeValue($dealerDB, WX_Parameter, $update_str, $update_where);
        }

        //返回access_token
        return $access_token;
    }

    /*
        获取openid
        scope为snsapi_base
    */
    public function getInfoOpenid($code = G_CONST::EMPTY_STRING) {
        $result    = array();
        $timestamp = time();
        if (!isset($code) || trim($code) == G_CONST::EMPTY_STRING) {
            log_message('error', "function(getInfoOpenid):lack of code" . " in file" . __FILE__ . " on Line " . __LINE__);
            return OPT_CONST::FAILED;
        }

        //获取appid和appsecret
        $app_id    = Game_CONST::WX_Appid;
        $appsecret = Game_CONST::WX_AppSecret;
        $dealerDB  = Game_CONST::DBConst_Name;

        //根据code模拟授权，获取accesstoken、openid等
        $http_url    = "https://api.weixin.qq.com/sns/oauth2/access_token?appid=" . $app_id . "&secret=" . $appsecret . "&code=" . $code . "&grant_type=authorization_code";
        $json_result = $this->httpGet($http_url);

        //返回结果
        //eg.:
        /*
            {
                "access_token": "OezXcEiiBSKSxW0eoylIeNs1KBPjUUvNUEDZ1xjXmIiHHHLgDmAbi2gPYFKtXn0VQZ5fekbbOgDlSVDaWfoyNswBSMM37odubh3mNlChgKGz9P2L8fVXu9f-PY2mtsktO2ZPeKgZEmuaoOYHP5WfSA",
                "expires_in": 7200,
                "refresh_token": "OezXcEiiBSKSxW0eoylIeNs1KBPjUUvNUEDZ1xjXmIiHHHLgDmAbi2gPYFKtXn0VuSLTfiLG21YnYfeSwhPC9Pg-QwqxsTDTDfovrAax7cTkYsknJAnvEtK-nkYM0NO6kOxnfiB7bSF-N5NqiNCVtQ",
                "openid": "oDDecs1VjfJiSqFyY2XF7noBKLNg",
                "scope": "snsapi_base"
            }
        */
        log_message('error', "getInfoOpenid:" . $json_result . " in file" . __FILE__ . " on Line " . __LINE__);

        //拆解json成数组
        $result = $this->splitJsonString($json_result);
        if ($result === FALSE || isset($result['errcode'])) {
            //获取access_token失败
            //log_message('error', "getInfoOpenid: ".$json_result." in file".__FILE__." on Line ".__LINE__);

            $json_result = $this->httpGet($http_url);
            $result      = $this->splitJsonString($json_result);
            if ($result === FALSE || isset($result['errcode'])) {
                //返回结果不是json
                log_message('error', "getInfoOpenid: " . $json_result . " in file" . __FILE__ . " on Line " . __LINE__);
                return OPT_CONST::FAILED;
            }
        }
        $open_id = $result['openid'];
        $unionid = $result['unionid'];

        log_message('error', "open id : " . $open_id . " in file" . __FILE__ . " on Line " . __LINE__);

        $access_token  = $result['access_token'];
        $refresh_token = $result['refresh_token'];
        $scope         = $result['scope'];

        $userinfo_ary['openid']        = $open_id;
        $userinfo_ary['code']          = $code;
        $userinfo_ary['access_token']  = $access_token;
        $userinfo_ary['refresh_token'] = $refresh_token;

        $account_data = $this->getGameAccountData($open_id);

        if (is_array($account_data)) {
            if ($scope == "snsapi_base" && $account_data['is_refresh'] == 1) {
                log_message('error', "getInfoOpenid:is_refresh:1" . " in file" . __FILE__ . " on Line " . __LINE__);
                return -2;
            } else {
                if ($account_data['is_refresh'] == 1 || $account_data['union_id'] == '' || $timestamp >= ($account_data['update_time'] + 86400)) {
                    log_message('error', "getInfoOpenid:更新用户信息 in file" . __FILE__ . " on Line " . __LINE__);
                    $param         = array("open_id" => $open_id, 'union_id' => $unionid, "access_token" => $access_token);
                    $update_result = $this->updateUserInfo($param);
                    if ($update_result === -2) {
                        return -2;
                    }
                }

                //$host = Game_CONST::My_Host;
                //$path = "/wxauth/updateUserInfo";
                //$port = 443;

                //异步更新用户信息
                //$param = array("open_id"=>$open_id,"access_token"=>$access_token);
                //$this->doRequestWX($host,$path,$port,$param);
            }
        } else {
            $param         = array("open_id" => $open_id, "access_token" => $access_token);
            $update_result = $this->updateUserInfo($param);
            if ($update_result === -2) {
                return -2;
            }
        }
        $_SESSION['WxOpenID'] = $open_id;
        return $userinfo_ary;
    }


    public function getGameAccountData($open_id) {
        $dealerDB = Game_CONST::DBConst_Name;
        //判断open_id是否存在
        $account_where = 'open_id="' . $open_id . '"';
        $account_sql   = 'select account_id,is_refresh,update_time from ' . WX_Account . ' where ' . $account_where;
        $account_query = $this->getDataBySql($dealerDB, 1, $account_sql);
        if (DB_CONST::DATA_NONEXISTENT != $account_query) {
            $account_id  = $account_query['account_id'];
            $is_refresh  = $account_query['is_refresh'];
            $update_time = $account_query['update_time'];
            return array("account_id" => $account_id, "is_refresh" => $is_refresh, "update_time" => $update_time);
        } else {
            log_message('error', "getGameAccountData: account not exist :" . " in file" . __FILE__ . " on Line " . __LINE__);
            return FALSE;
        }
    }


    /*
        更新用户信息
    */
    public function updateUserInfo($userinfo_ary) {
        $timestamp = time();
        $dealerDB  = Game_CONST::DBConst_Name;

        $open_id      = $userinfo_ary['open_id'];
        $access_token = $userinfo_ary['access_token'];

        if ($open_id == G_CONST::EMPTY_STRING) {
            log_message('error', "updateWechatAccount:缺少openid in file" . __FILE__ . " on Line " . __LINE__);
            return -1;
        }

        $wechat_userinfo = $this->getWechatUserinfo($userinfo_ary);
        if (!is_array($wechat_userinfo)) {
            $update_str   = 'update_time=' . $timestamp . ',update_appid="' . $open_id . '",is_refresh=1';
            $update_where = 'open_id="' . $open_id . '"';
            $update_query = $this->changeNodeValue($dealerDB, WX_Account, $update_str, $update_where);

            log_message('error', "updateWechatAccount:can not getWechatUserinfo:" . $open_id . " in file" . __FILE__ . " on Line " . __LINE__);
            return -1;
        }

        if (!isset($wechat_userinfo['nickname']) || !isset($wechat_userinfo['headimgurl'])) {
            log_message('error', "updateWechatAccount:can not get nickname headimgurl:" . $open_id . " in file" . __FILE__ . " on Line " . __LINE__);
            return -2;
        }

        $nickname = addslashes(htmlspecialchars($wechat_userinfo['nickname']));
        $capital  = "";

        $nickname_json = json_encode($nickname);
        $unicode_arr   = explode("\u", $nickname_json);

        if (isset($unicode_arr[1]) && $unicode_arr[0] == "\"") {
            if (preg_match('/^[\x{4e00}-\x{9fa5}]+/u', $nickname)) {
                $pinyin = new Pinyin_Model();

                $capital = strtoupper(substr($pinyin->abbr($nickname), 0, 1));
            } else {
                $capital = "@";
            }
        }
        if ($capital == "") {
            $capital = substr($nickname, 0, 1);
            if (preg_match("/[a-z]/", $capital)) {
                $capital = strtoupper($capital);
            }
        }

        $headimgurl = $wechat_userinfo['headimgurl'];
        $unionid    = $wechat_userinfo['unionid'];
        log_message('error', "updateWechatAccount:nickname:" . $nickname . " in file" . __FILE__ . " on Line " . __LINE__ . "capital" . $capital);
        //log_message('error', "updateWechatAccount:headimgurl:".$headimgurl." in file".__FILE__." on Line ".__LINE__);

        //判断open_id是否存在
        $account_where = 'open_id="' . $open_id . '"';
        $account_sql   = 'select account_id from ' . WX_Account . ' where ' . $account_where . '';
        $account_query = $this->getDataBySql($dealerDB, 1, $account_sql);
        if (DB_CONST::DATA_NONEXISTENT != $account_query) {
            $account_id = $account_query['account_id'];
            $update_str = sprintf("update_time=%s, union_id='%s', update_appid='%s', nickname='%s', headimgurl='%s', is_refresh=0",
                                  $timestamp, $unionid, $open_id, $nickname,  $headimgurl);
            $update_where = 'account_id="' . $account_id . '"';
		//echo $update_str;
            $update_query = $this->changeNodeValue($dealerDB, WX_Account, $update_str, $update_where);
            $_SESSION['AccountID'] = $account_id;
        } else {
            //添加账号
            $insert_array['create_time']  = $timestamp;
            $insert_array['update_time']  = $timestamp;
            $insert_array['create_appid'] = $open_id;
            $insert_array['update_appid'] = $open_id;
            $insert_array['is_delete']    = 0;
            $insert_array['open_id']      = $open_id;
            $insert_array['union_id']     = $unionid;
            $insert_array['nickname']     = $nickname;
            $insert_array['capital']      = $capital;
            $insert_array['headimgurl']   = $headimgurl;
            $insert_array['is_refresh']   = 0;
            $account_id                   = $this->getInsertID($dealerDB, WX_Account, $insert_array);
            unset($insert_array);

            if ($account_id > 0) {
                $update_str   = 'user_code=account_id+10000';
                $update_where = 'account_id=' . $account_id;
                $update_query = $this->changeNodeValue($dealerDB, WX_Account, $update_str, $update_where);

                //添加默认房卡
                $insert_array['create_time']  = $timestamp;
                $insert_array['update_time']  = $timestamp;
                $insert_array['create_appid'] = $open_id;
                $insert_array['update_appid'] = $open_id;
                $insert_array['is_delete']    = 0;
                $insert_array['account_id']   = $account_id;
                $insert_array['ticket_count'] = 0;
                $this->getInsertID($dealerDB, Room_Ticket, $insert_array);
                unset($insert_array);
                $_SESSION['AccountID'] = $account_id;
            }
        }

        return TRUE;
    }


    /*
        获取用户参数
    */
    public function getWechatUserinfo($userinfo_ary) {
        $open_id      = $userinfo_ary['open_id'];
        $access_token = $userinfo_ary['access_token'];
        //log_message('error', "getAccessToken:access_token:".$access_token." in file".__FILE__." on Line ".__LINE__);

        if ($access_token != OPT_CONST::DATA_NONEXISTENT) {
            $http_url    = "https://api.weixin.qq.com/sns/userinfo?access_token=" . $access_token . "&openid=" . $open_id . "&lang=zh_CN";
            $json_result = $this->httpGet($http_url);

            log_message('error', "function(getWechatUserinfo):1st : $json_result" . " in file" . __FILE__ . " on Line " . __LINE__);

            $result = $this->splitJsonString($json_result);
            if ($result === FALSE || isset($result['errcode'])) {
                $access_token = $this->getAccessToken($is_refresh = 0);

                $http_url    = "https://api.weixin.qq.com/cgi-bin/user/info?access_token=" . $access_token . "&openid=" . $open_id . "";
                $json_result = $this->httpGet($http_url);

                log_message('error', "function(getWechatUserinfo):2nd : $json_result" . " in file" . __FILE__ . " on Line " . __LINE__);

                $result = $this->splitJsonString($json_result);
                if ($result === FALSE || isset($result['errcode'])) {
                    $access_token = $this->getAccessToken($is_refresh = 1);

                    $http_url    = "https://api.weixin.qq.com/cgi-bin/user/info?access_token=" . $access_token . "&openid=" . $open_id . "";
                    $json_result = $this->httpGet($http_url);

                    log_message('error', "function(getWechatUserinfo):3th : $json_result" . " in file" . __FILE__ . " on Line " . __LINE__);

                    $result = $this->splitJsonString($json_result);
                    if ($result === FALSE || isset($result['errcode'])) {
                        log_message('error', "function(getWechatUserinfo):can not get userinfo:" . $json_result . " in file" . __FILE__ . " on Line " . __LINE__);
                        return OPT_CONST::DATA_NONEXISTENT;
                    } else {
                        //返回access_token
                        return $result;
                    }
                } else {
                    //返回access_token
                    return $result;
                }
            } else {
                //返回access_token
                return $result;
            }
        } else {
            return OPT_CONST::DATA_NONEXISTENT;
        }
    }

    public function addUser($open_id, $nickname, $head, $union_id = '') {
        $dealerDB = Game_CONST::DBConst_Name;
        //添加账号
        $insert_array['create_time']  = time();
        $insert_array['update_time']  = time();
        $insert_array['create_appid'] = $open_id;
        $insert_array['update_appid'] = $open_id;
        $insert_array['is_delete']    = 0;
        $insert_array['open_id']      = $open_id;
        $insert_array['union_id']     = $union_id;
        $insert_array['nickname']     = $nickname;
        $insert_array['headimgurl']   = $head;
        $insert_array['is_refresh']   = 0;
        $account_id                   = $this->getInsertID($dealerDB, WX_Account, $insert_array);
        unset($insert_array);

        if ($account_id > 0) {
            $update_str   = 'user_code=account_id+10000';
            $update_where = 'account_id=' . $account_id;
            $update_query = $this->changeNodeValue($dealerDB, WX_Account, $update_str, $update_where);

            //添加默认房卡
            $insert_array['create_time']  = time();
            $insert_array['update_time']  = time();
            $insert_array['create_appid'] = $open_id;
            $insert_array['update_appid'] = $open_id;
            $insert_array['is_delete']    = 0;
            $insert_array['account_id']   = $account_id;
            $insert_array['ticket_count'] = 0;
            $this->getInsertID($dealerDB, Room_Ticket, $insert_array);
            unset($insert_array);
        }
    }

    public function getUnionidByOpenid() {
        $access_token = $this->getAccessToken();
        if (empty($access_token)) {
            return FALSE;
        }

        $table = WX_Account;

        // 批量获取，每次最多100个
        $users = $this->db()
            ->where('union_id is null')
            ->order_by('account_id', 'DESC')
            ->limit(100)
            ->get($table)
            ->result_array();

        $count = count($users);

        if (0 == $count) {
            return FALSE;
        }

        $user_list = [];
        foreach ($users as $user) {
            $user_list[] = [
                'openid' => $user['open_id'],
                'lang'   => 'zh_CN'
            ];
        }
        $postData = json_encode(['user_list' => $user_list]);

        $url = 'https://api.weixin.qq.com/cgi-bin/user/info/batchget?access_token=' . $access_token;
        $ch  = curl_init();//初始化curl
        curl_setopt($ch, CURLOPT_URL, $url);//抓取指定网页
        curl_setopt($ch, CURLOPT_HEADER, 0);//设置header
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);//要求结果为字符串且输出到屏幕上
        curl_setopt($ch, CURLOPT_POST, 1);//post提交方式
        curl_setopt($ch, CURLOPT_POSTFIELDS, $postData);
        $res = curl_exec($ch);//运行curl
        curl_close($ch);
        /*
array(4) {
    ["subscribe"]=>
    int(0)
    ["openid"]=>
    string(28) "oHwwYxN0ZLuWODixjzZjpbPoD8Ck"
    ["unionid"]=>
    string(28) "otqop00tR0CIpo8zn-Og4JouKwgE"
    ["tagid_list"]=>
    array(0) {
    }
}
         */
        $res = $this->splitJsonString($res);
        if (is_array($res) && isset($res['user_info_list'])) {
            foreach ($res['user_info_list'] as $item) {
                if (isset($item['openid']) && isset($item['unionid'])) {
                    $this->db()->where('open_id', $item['openid'])->update($table, ['union_id' => $item['unionid']]);
                }
            }
        }

        return TRUE;
    }

    /**
     * 函数描述：修正所有用户的首字符，该函数只有在数据库新增首字符字段(capital)的时候需要执行
     * author 黄欣仕
     * date 2019/2/28
     */
    public function ChangeAllCapital() {
        $dealerDB = Game_CONST::DBConst_Name;
        $sql      = "select account_id, nickname, capital from " . WX_Account;
        $users    = $this->getDataBySql($dealerDB, 0, $sql);
        if (DB_CONST::DATA_NONEXISTENT != $users) {

            foreach ($users as $user) {
                $capital       = "";
                $nickname      = $user["nickname"];
                $nickname_json = json_encode($user["nickname"]);
                $unicode_arr   = explode("\u", $nickname_json);

                if (isset($unicode_arr[1]) && $unicode_arr[0] == "\"") {
                    if (preg_match('/^[\x{4e00}-\x{9fa5}]+/u', $nickname)) {
                        $pinyin = new Pinyin_Model();

                        $capital = strtoupper(substr($pinyin->abbr($nickname), 0, 1));
                    } else { //if ($uincode_arr1> "d000" &&$uincode_arr1<"efff")
                        $capital = "@";
                    }
                }

                if ($capital == "") {
                    $capital = substr($nickname, 0, 1);
                    if (preg_match("/[a-z]/", $capital)) {
                        $capital = strtoupper($capital);
                    }
                }

                if ($capital != $user["capital"]) {
                    $update_where = ' account_id = ' . $user["account_id"];
                    $update_str   = ' capital = "' . $capital . '"';
                    $this->changeNodeValue($dealerDB, WX_Account, $update_str, $update_where);
                }
            }
        }
    }

}
