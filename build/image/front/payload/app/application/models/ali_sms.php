<?php
/**
 * Created by PhpStorm.
 * User: nszxyu
 * Date: 2017/11/9
 * Time: 0:37
 */

require_once dirname(__DIR__) . '/libraries/ali_sms/vendor/autoload.php';

use Aliyun\Core\Config;
use Aliyun\Core\Profile\DefaultProfile;
use Aliyun\Core\DefaultAcsClient;
use Aliyun\Api\Sms\Request\V20170525\SendSmsRequest;

Config::load();
class AliSms{
    static $acsClient = null;

    public static function getAcsClient() {
        //产品名称:云通信流量服务API产品,开发者无需替换
        $product = "Dysmsapi";

        //产品域名,开发者无需替换
        $domain = "dysmsapi.aliyuncs.com";

        // TODO 此处需要替换成开发者自己的AK (https://ak-console.aliyun.com/)
        $accessKeyId = "LTAIMhosQYq1k2Sj"; // AccessKeyId

        $accessKeySecret = "iJE6DrkjrTOUZd2PnOZuqtJjASQ9ac"; // AccessKeySecret


        // 暂时不支持多Region
        $region = "cn-hangzhou";

        // 服务结点
        $endPointName = "cn-hangzhou";


        if(static::$acsClient == null) {

            //初始化acsClient,暂不支持region化
            $profile = DefaultProfile::getProfile($region, $accessKeyId, $accessKeySecret);

            // 增加服务结点
            DefaultProfile::addEndpoint($endPointName, $region, $product, $domain);

            // 初始化AcsClient用于发起请求
            static::$acsClient = new DefaultAcsClient($profile);
        }
        return static::$acsClient;
    }


    public static function sendSMS($signName, $templateCode, $phoneNumbers, $templateParam = null, $outId = null) {
        // 初始化SendSmsRequest实例用于设置发送短信的参数
        $request = new SendSmsRequest();
        // 必填，设置雉短信接收号码
        $request->setPhoneNumbers($phoneNumbers);
        // 必填，设置签名名称
        $request->setSignName($signName);
        // 必填，设置模板CODE
        $request->setTemplateCode($templateCode);
        // 可选，设置模板参数
        if($templateParam) {
            $request->setTemplateParam(json_encode($templateParam));
        }
        // 可选，设置流水号
        if($outId) {
            $request->setOutId($outId);
        }
        // 发起访问请求
        $acsResponse = static::getAcsClient()->getAcsResponse($request);
        // 打印请求结果
        // var_dump($acsResponse);
        return $acsResponse;
    }
}
