<?php  

class DGame_CONST
{	
	
	const WXauthUrl_Home	= 'http://wap.uzhan123.com/dwechat/wxauth_{dealer_number}/getGameOpenID';
	//const WXauthUrl_Home	= 'http://game.fexteam.com/wxauth/getGameOpenID';
	
	const WXauthUrl_Room	= 'http://wap.uzhan123.com/dwechat/wxauth_{dealer_number}/getGameOpenIDRoom';
	//const WXauthUrl_Room	= 'http://game.fexteam.com/wxauth/getGameOpenIDRoom';
	
	const WXauthUrl_RedEnvelop	= 'http://wap.uzhan123.com/dwechat/wxauth_{dealer_number}/getGameOpenIDRedEnvelop';
	//const WXauthUrl_RedEnvelop	= 'http://game.fexteam.com/wxauth/getGameOpenIDRedEnvelop';
	
	const WXauthUrl_RedEnvelopList	= 'http://wap.uzhan123.com/dwechat/wxauth_{dealer_number}/getGameOpenIDRedEnvelopList';
	//const WXauthUrl_RedEnvelopList	= 'http://game.fexteam.com/wxauth/getGameOpenIDRedEnvelopList';
	
	const WXauthUrl_CreateRedEnvelop	= 'http://wap.uzhan123.com/dwechat/wxauth_{dealer_number}/getGameOpenIDCreateRedEnvelop';
	//const WXauthUrl_CreateRedEnvelop	= 'http://game.fexteam.com/wxauth/getGameOpenIDCreateRedEnvelop';
	
	const WXauthUrl_Sign	= 'http://wap.uzhan123.com/dwechat/wxauth_{dealer_number}/getGameOpenIDSign';
	//const WXauthUrl_Sign	= 'http://game.fexteam.com/wxauth/getGameOpenIDSign';
	
	const WXauthUrl_Dial	= 'http://wap.uzhan123.com/dwechat/wxauth_{dealer_number}/getGameOpenIDRedDial';
	//const WXauthUrl_Dial	= 'http://game.fexteam.com/wxauth/getGameOpenIDRedDial';
	
	const WXauthUrl_Slot	= 'http://wap.uzhan123.com/dwechat/wxauth_{dealer_number}/getGameOpenIDSlot';
	//const WXauthUrl_Slot	= 'http://game.fexteam.com/wxauth/getGameOpenIDSlot';
	
	
	const WXauthUrl_LandlordHome	= 'http://wap.uzhan123.com/dwechat/wxauth_{dealer_number}/getGameOpenIDLandlord';
	//const WXauthUrl_Home	= 'http://game.fexteam.com/wxauth/getGameOpenIDLandlord';
	
	const WXauthUrl_LandlordRoom	= 'http://wap.uzhan123.com/dwechat/wxauth_{dealer_number}/getGameOpenIDRoomLandlord';
	//const WXauthUrl_Room	= 'http://game.fexteam.com/wxauth/getGameOpenIDRoomLandlord';
	
	const WXauthUrl_TexasHome	= 'http://wap.uzhan123.com/dwechat/wxauth_{dealer_number}/getGameOpenIDTexas';
	//const WXauthUrl_Home	= 'http://game.fexteam.com/wxauth/getGameOpenID';
	
	const WXauthUrl_TexasRoom	= 'http://wap.uzhan123.com/dwechat/wxauth_{dealer_number}/getGameOpenIDRoomTexas';
	//const WXauthUrl_Room	= 'http://game.fexteam.com/wxauth/getGameOpenIDRoom';
	
	
	const WXauthUrl_BullHome	= 'http://wap.uzhan123.com/dwechat/wxauth_{dealer_number}/getGameOpenIDBull';
	//const WXauthUrl_Home	= 'http://game.fexteam.com/wxauth/getGameOpenIDBull';
	
	const WXauthUrl_BullRoom	= 'http://wap.uzhan123.com/dwechat/wxauth_{dealer_number}/getGameOpenIDRoomBull';
	//const WXauthUrl_Room	= 'http://game.fexteam.com/wxauth/getGameOpenIDRoomBull';
	
	
	const WXauthUrl_GDMJHome	= 'http://wap.uzhan123.com/dwechat/wxauth_{dealer_number}/getGameOpenIDGDMJ';
	//const WXauthUrl_GDMJHome	= 'http://game.fexteam.com/wxauth/getGameOpenIDGDMJ';
	
	const WXauthUrl_GDMJRoom	= 'http://wap.uzhan123.com/dwechat/wxauth_{dealer_number}/getGameOpenIDRoomGDMJ';
	//const WXauthUrl_GDMJRoom	= 'http://game.fexteam.com/wxauth/getGameOpenIDRoomGDMJ';
	
	
	
	
	//更新用户状态
	const UpdateWxUserinfo_HostDev 	= 'game.zht66.com';
	const UpdateWxUserinfo_Host 	= 'game.fexteam.com';


	//
	const AsynRequest_Host			= 'wap.uzhan123.com';
	//const AsynRequest_Host			= 'game.fexteam.com';
	
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
}


/* End of file constants.php */
/* Location: ./application/config/constants.php */