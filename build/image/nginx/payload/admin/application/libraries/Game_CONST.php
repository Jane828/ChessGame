<?php  

class Game_CONST
{	
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
	const ObjectType_BindAccount		= 10;	//绑定新账号转移房卡
	const ObjectType_Adjustment			= 11;	//代理商调整房卡
	const ObjectType_Exchange			= 12;	//房卡兑换


	//代理商流水类型
	const DObjectType_Balance			= 1;	//调整房卡
	const DObjectType_Recharge			= 2;	//后台充值
	const DObjectType_Sale				= 3;	//商城销售
	const DObjectType_RedEnvelop		= 4;	//房卡红包
	const DObjectType_Exchange			= 5;	//制作兑换码

	const DObjectType_Reward			= 7;	//充卡赠送

	//红包类型
	const RedenvelopType_User			= 1;	//用户红包
	const RedenvelopType_Dealer			= 2;	//代理商红包

   	 const APP_PATH   = '/wwwroot/app/';

}


/* End of file constants.php */
/* Location: ./application/config/constants.php */
