<?php
/**
 * Created by PhpStorm.
 * User: Shuai
 * Date: 2016/12/15
 * Time: 16:06
 */
//namespace models;
//use think\Log;

class SMSTool
{
    private static $sendUrl="http://v.juhe.cn/sms/send";
    private static $smsKey = '11074a5b573f4e992789c72c604114d8';
    private static $tplId = 48868;

    //网易云短信服务
    private static $OpenApiUrl = "https://open.c.163.com/api/v1/token";
    private static $AccessKey = "a21b68c87e8342bf85b798ef29eb5587";
    private static $AccessSecret = "f3fe4e43164241ada9fa1bc5f339e978";
    private static $SendCodeUrl = "https://api.netease.im/sms/sendcode.action";
    public static $AppKey = "10631c7971a4e9cc33ab8bbf7ce06526";                //开发者平台分配的AppKey
    public static $AppSecret = "5c129fd9d6fc";             //开发者平台分配的AppSecret,可刷新
    const HEX_DIGITS = "0123456789abcdef";

    public static $Whether = array(
        'Yes' => 1,
        'No' => 0
    );


    public static function SendAuthCode($strMobile,$authCode){

        $param = array(
            'mobile' => $strMobile,
            'authCode'=>$authCode
        );

        $ret = self::$Whether["No"];
        $msg = "发送失败";
        if($strMobile){
//            $content =self::juhecurl(self::$sendUrl,'mobile='.$strMobile.'&tpl_id='.self::$tplId.'&tpl_value='.urlencode('#code#='.$authCode).'&key='.self::$smsKey,0);
            $content = self::postDataCurl(self::$SendCodeUrl, $param, $authCode);
            if($content){
                $result = json_decode($content,true);
                $error_code = $result['code'];
                if($error_code == 200){
                    //状态为0，说明短信发送成功
                    $ret = self::$Whether["Yes"];
                    $msg = "发送成功";
//                    Log::record("$msg content: $content ", "debug");
                }else{
                    //状态非200，说明失败
                    $msg = $result['msg'];
//                    Log::record("$msg content: $content ", "debug");
                    log_message('error', "function(SendAuthCode):SMS fail"." in file".__FILE__." on Line ".__LINE__);
                }
            }else{
                //返回内容异常
                $msg = "请求发送短信失败";
//                Log::record("SendAuthCode:请求发送短信失败", "debug");
                log_message('error', "function(SendAuthCode):SMS abnormal"." in file ".__FILE__." on Line ".__LINE__);
            }
        }

        return array("ret"=>$ret,"msg"=>$msg);
    }

    /**
     * 使用CURL方式发送post请求
     * @param  $url     [请求地址]
     * @param  $data    [array格式数据]
     * @return $请求返回结果(array)
     */
    public static function postDataCurl($url, $data)
    {
//      $this->checkSumBuilder($authCode);       //发送请求前需先生成checkSum
        $hex_digits = self::HEX_DIGITS;
        $Nonce=null;
        for ($i = 0; $i < 128; $i++) {            //随机字符串最大128个字符，也可以小于该数
            $Nonce .= $hex_digits[rand(0, 15)];
        }
        $CurTime = (string)(time());    //当前时间戳，以秒为单位
        $join_string = self::$AppSecret . $Nonce . $CurTime;
        $CheckSum = sha1($join_string);

        $timeout = 5000;
        $http_header = array(
            'AppKey:' . self::$AppKey,
            'Nonce:' . $Nonce,
            'CurTime:' . $CurTime,
            'CheckSum:' . $CheckSum,
            'Content-Type:application/x-www-form-urlencoded;charset=utf-8'
        );

        $postdataArray = array();
        foreach ($data as $key => $value) {
            array_push($postdataArray, $key . '=' . urlencode($value));
        }
        $postdata = join('&', $postdataArray);

        $ch = curl_init();
        curl_setopt($ch, CURLOPT_URL, $url);
        curl_setopt($ch, CURLOPT_POST, 1);
        curl_setopt($ch, CURLOPT_POSTFIELDS, $postdata);
        curl_setopt($ch, CURLOPT_HEADER, false);
        curl_setopt($ch, CURLOPT_HTTPHEADER, $http_header);
        curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, false); //处理http证书问题
        curl_setopt($ch, CURLOPT_CONNECTTIMEOUT, $timeout);
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);

        $result = curl_exec($ch);
        if (false === $result) {
            $result = curl_errno($ch);
        }
        curl_close($ch);
        return $result;
    }

    /**
     * 请求接口返回内容
     * @param  string $url [请求的URL地址]
     * @param  string $params [请求的参数]
     * @param  int $ipost [是否采用POST形式]
     * @return  string
     */
    private static function juhecurl($url,$params=false,$ispost=0){
        $httpInfo = array();
        $ch = curl_init();

        curl_setopt( $ch, CURLOPT_HTTP_VERSION , CURL_HTTP_VERSION_1_1 );
        curl_setopt( $ch, CURLOPT_USERAGENT , 'Mozilla/5.0 (Windows NT 5.1) AppleWebKit/537.22 (KHTML, like Gecko) Chrome/25.0.1364.172 Safari/537.22' );
        curl_setopt( $ch, CURLOPT_CONNECTTIMEOUT , 30 );
        curl_setopt( $ch, CURLOPT_TIMEOUT , 30);
        curl_setopt( $ch, CURLOPT_RETURNTRANSFER , true );
        if( $ispost )
        {
            curl_setopt( $ch , CURLOPT_POST , true );
            curl_setopt( $ch , CURLOPT_POSTFIELDS , $params );
            curl_setopt( $ch , CURLOPT_URL , $url );
        }
        else
        {
            if($params){
                curl_setopt( $ch , CURLOPT_URL , $url.'?'.$params );
            }else{
                curl_setopt( $ch , CURLOPT_URL , $url);
            }
        }
        $response = curl_exec( $ch );
        if ($response === FALSE) {
            return false;
        }
        $httpCode = curl_getinfo( $ch , CURLINFO_HTTP_CODE );
        $httpInfo = array_merge( $httpInfo , curl_getinfo( $ch ) );
        curl_close( $ch );
        return $response;
    }
}