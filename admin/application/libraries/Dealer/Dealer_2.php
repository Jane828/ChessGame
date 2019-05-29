<?php  

class Dealer_2
{	

	const DBConst_Name   	= "dealer_2";

	


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
	const Is_SyncCard       = 0;	//是否同步旧绑定手机房卡
	const AuthPhone_CardCount = 10;

	/*
		游戏战绩列表
	*/
	const Is_GameScore = 1;
	const GameResult_Flower  = 1;
	const GameResult_TFlower = 1;
	const GameResult_Bull6   = 1;
	const GameResult_Bull9   = 1;
	const GameResult_Bull12  = 1;
	/*
		渠道
	*/
	const Is_Channel  		= 0;

	/*
		兑换房卡
	*/
	const Is_Exchange  		= 0;
}


/* End of file constants.php */
/* Location: ./application/config/constants.php */