<?php

$callback_host = file_get_contents("/data/conf/callback.txt");

define('MY_WS', "ws://" . $callback_host);
define('MY_FILE_URL', "http://" . $callback_host . "/all/");
define('MY_IMAGE_URL', "http://" . $callback_host . "/all/");
define('FRONT_VERSION', str_replace('.' , "_", getenv("FRONT_VERSION")));

require_once __DIR__ . '/../../wechat_config.php';

class Game_CONST {
    //公众号原始ID
    const WX_Account = WX_ACCOUNT;
    //公众号开发者ID  AppID
    const WX_Appid = WX_APPID;
    //公众号开发者密码 AppSecret
    const WX_AppSecret = WX_APPSECRET;

    //wxpay
    const WX_Mch_ID      = "";
    const WX_Device_Info = "WEB";
    const WX_API_Key     = "";
    const WX_Notify_Url  = "dwechat/wxauth_{dealer_num}/wxpayCallback";

    //链接域名
    //	const My_Host = MY_HOST;
    //	const My_Url  = MY_URL;

    // config/database.php 对应的数据库的标识
    const DBConst_Name = "dealer_2";

    //游戏后端接口的域名和端口
    const WS = MY_WS;

    const WebSocket_Host     = self::WS . ':20032'; // websocket通用url
    const WebSocket_Landlord = self::WS . ':20062';
    const WebSocket_Gdmj     = self::WS . ':20072';

    //文件和图片的存储地址
    const FileUrl  = MY_FILE_URL;
    const ImageUrl = MY_IMAGE_URL;

    const Verification_Code = "xxxxzzzz";


    //授权token
    const Wxauth_Num = 2;
    const Is_Dist    = 0;


    /*
        房间状态
    */
    const RoomStatus_NotStarted = 0;    //未开始
    const RoomStatus_Playing    = 1;    //游戏进行中


    //流水账
    const JournalType_Income   = 1;    //入账
    const JournalType_Disburse = 2;    //出账

    //流水类型
    const ObjectType_Newuser     = 1;    //新用户
    const ObjectType_Recharge    = 2;    //充值
    const ObjectType_Game        = 3;    //游戏
    const ObjectType_Dealer      = 4;    //代理商码兑换
    const ObjectType_Sign        = 5;    //签到
    const ObjectType_RedEnvelop  = 6;    //红包
    const ObjectType_Luckdraw    = 7;    //转盘
    const ObjectType_SlotMachine = 8;    //老虎机
    const ObjectType_Commission  = 9;    //提成
    const ObjectType_BindAccount = 10;    //绑定新账号转移房卡
    const ObjectType_Adjustment  = 11;    //代理商调整房卡
    const ObjectType_Exchange    = 12;    //房卡兑换
    const ObjectType_Manage      = 13;    //管理功能
    const ObjectType_Transfer    = 14;    //房卡转移

    //代理商流水类型
    const DObjectType_Balance    = 1;    //调整房卡
    const DObjectType_Recharge   = 2;    //后台充值
    const DObjectType_Sale       = 3;    //商城销售
    const DObjectType_RedEnvelop = 4;    //房卡红包


    //红包类型
    const RedenvelopType_User     = 1;    //用户红包
    const RedenvelopType_Dealer   = 2;    //代理商红包
    const RedenvelopType_Business = 3;    //微商红包


    //提成类型
    const CommissionType_Order     = 1;    //订单提成
    const CommissionType_Introduce = 2;    //推荐提成

    const Manage_cost = 0;   //房卡消耗

    const Front_Version = FRONT_VERSION;
}


/* End of file constants.php */
/* Location: ./application/config/constants.php */
