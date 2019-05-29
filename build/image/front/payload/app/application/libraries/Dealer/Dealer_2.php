<?php  

class Dealer_2
{	

	const MYSQL_Host  		= "kaigong1.mysql.rds.aliyuncs.com";
	const MYSQL_Username  	= "you";
	const MYSQL_Pwd  		= "Peter0324";
	const MYSQL_DB 			= "fairy";

	const DBConst_Name   	= "dealer_2";

	//微信公众号
	//公共账号app_id和app_secret
	const WX_Appid	 			= "wx8ba8af1ed5a43c2d";
	const WX_AppSecret 			= "32d3a0064496c163541b3302bfad3de5";
	const WX_Account 			= "gh_b468aa03018b";						//原始ID

	//wxpay
	const WX_Mch_ID							= "";
	const WX_Device_Info					= "WEB";
	const WX_API_Key						= "";
	const WX_Notify_Url						= "dwechat/wxauth_{dealer_num}/wxpayCallback";


	const WebSocket_Host	= 'wss://game.fairyland.xin:20002';
	
	const WebSocket_Landlord= 'wss://game.fairyland.xin:20012';
	
	const WebSocket_Bull	= 'wss://game.fairyland.xin:20022';
	
	const WebSocket_Gdmj	= '';
	
	const WebSocket_Bull9	= 'wss://game.fairyland.xin:20042';
	
	const WebSocket_BullFight	= '';


	
	//授权token 
	const Wxauth_Num		= 2;
	const Is_Dist  			= 0;
	
	//获取open跳转地址
	const DWxAuth_SkipHost			= "game.fairyland.xin";
	
	
	//更新用户状态
	const UpdateWxUserinfo_Host 	= 'game.fairyland.xin';
	

	//
	const AsynRequest_Host			= 'game.fairyland.xin';
	
	
	const WXauthUrl_Host	= 'https://game.fairyland.xin/';
	
	const WXauthUrl_Main	= 'dwechat/wxauth_{dealer_number}/getMainHomeOpenID';
	
	const WXauthUrl_Room	= 'dwechat/wxauth_{dealer_number}/getGameOpenIDRoom';
	
	const WXauthUrl_RedEnvelop	= 'dwechat/wxauth_{dealer_number}/getGameOpenIDRedEnvelop';
	
	const WXauthUrl_RedEnvelopList	= 'dwechat/wxauth_{dealer_number}/getGameOpenIDRedEnvelopList';
	
	const WXauthUrl_CreateRedEnvelop	= 'dwechat/wxauth_{dealer_number}/getGameOpenIDCreateRedEnvelop';
	
	const WXauthUrl_Sign	= 'dwechat/wxauth_{dealer_number}/getGameOpenIDSign';
	
	const WXauthUrl_Dial	= 'dwechat/wxauth_{dealer_number}/getGameOpenIDRedDial';
	
	const WXauthUrl_Slot	= 'dwechat/wxauth_{dealer_number}/getGameOpenIDSlot';

	const WXauthUrl_LandlordRoom	= 'dwechat/wxauth_{dealer_number}/getGameOpenIDRoomLandlord';
	
	const WXauthUrl_TexasRoom	= 'dwechat/wxauth_{dealer_number}/getGameOpenIDRoomTexas';
	
	const WXauthUrl_BullRoomFight	= 'dwechat/wxauth_{dealer_number}/getGameOpenIDRoomBullFight';

	const WXauthUrl_BullRoom9	= 'dwechat/wxauth_{dealer_number}/getGameOpenIDRoomBull9';
	
	const WXauthUrl_BullRoom	= 'dwechat/wxauth_{dealer_number}/getGameOpenIDRoomBull';
	
	const WXauthUrl_GDMJRoom	= 'dwechat/wxauth_{dealer_number}/getGameOpenIDRoomGDMJ';

	const WXauthUrl_CommissionList	= 'dwechat/wxauth_{dealer_number}/getOpenIDCommissionList';

	const WXauthUrl_DistList	= 'dwechat/wxauth_{dealer_number}/getOpenIDDistList';


	/*
		房间状态
	*/
	const RoomStatus_NotStarted		= 0;	//未开始
	const RoomStatus_Playing		= 1;	//游戏进行中


	//流水账
	const JournalType_Income			= 1;	//入账
	const JournalType_Disburse			= 2;	//出账

	//流水类型	
	const ObjectType_Newuser			= 1;	//新用户
	const ObjectType_Recharge			= 2;	//充值
	const ObjectType_Game				= 3;	//游戏
	const ObjectType_Dealer				= 4;	//代理商码兑换
	const ObjectType_Sign				= 5;	//签到
	const ObjectType_RedEnvelop			= 6;	//红包
	const ObjectType_Luckdraw			= 7;	//转盘
	const ObjectType_SlotMachine		= 8;	//老虎机
	const ObjectType_Commission			= 9;	//提成

	/*
		公会模式
	*/
	const Is_Guild          = 0;
	const QR_Url   			= "";
	const WX_Name   		= "";
	const WXauthUrl_Invite		= 'dwechat/wxauth_{dealer_number}/getOpenIDInvite';

	/*
		绑定手机
	*/
	const Is_SyncCard       = 1;	//是否同步旧绑定手机房卡
	const AuthPhone_CardCount = 1000000000;

	/*
		微商模式
	*/
	const WXauthUrl_Business	= 'dwechat/wxauth_{dealer_number}/getOpenIDBusiness';
	const WXauthUrl_SendBRe	= 'dwechat/wxauth_{dealer_number}/getOpenIDSendBredpackage';
	const WXauthUrl_Bredpackage	= 'dwechat/wxauth_{dealer_number}/getOpenIDBredpackage';
	const Is_Business      = 0;

	/*
		渠道
	*/
	const Is_Channel  		= 0;

	/*
		代理商校验码
	*/
	const Verification_Code = "987654321098765432109876543210zz";
}


/* End of file constants.php */
/* Location: ./application/config/constants.php */