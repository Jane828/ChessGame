<?php


class Game
{	
	
	/*****************************
				参数
	*****************************/
	
	/*
		分享房间链接
	*/
	const Share_RoomUrl			= "";

	
	const PaymentType_AA		= 1;	//1AA
	const PaymentType_Creator	= 2;	//1房主

	const Game_Type				= 37; // 9人三公
	
	/*	房间用户uid	*/
	const RoomUser_UID			= "[roomid]:[accountid]";

	
	/*	开局默认分数	*/
	const Default_Score				= 1;
	const Default_SpendTicketCount	= 2;

	//房卡类型
	const Rule_TicketType_1			= 2;
	const Rule_TicketType_2			= 4;

    // 倍数
    const Rule_TG_Multiple      = 10;
    const Rule_BJ_Multiple      = 9;
    const Rule_DSG_Multiple     = 9;
    const Rule_XSG_Multiple     = 7;
    const Rule_LG_Multiple      = 7;
    const Rule_SG_Multiple      = 5;
    const Rule_DG_Multiple      = 5;
    const Rule_Card9_Multiple   = 4;
    const Rule_Card8_Multiple   = 3;
    const Rule_Card7_Multiple   = 2;
    const Rule_Card0_Multiple   = 1; // 0-6点倍数
	
	/*	游戏人数范围	*/
	const GameUser_MinCount		= 2;
	const GameUser_MaxCount		= 9;

    /* 观战人数限制 */
    const GameAudience_MaxCount = 100;

	/*	游戏每轮局数 */
	//const GameNum_EachRound		= 2;
	
	
	/*	用户状态	*/
	const AccountStatus_Initial		= 0;	//首次进房初始状态
	const AccountStatus_Notready	= 1;	//未准备
	const AccountStatus_Ready		= 2;	//已准备
	const AccountStatus_Choose		= 3;	//选择抢庄
	const AccountStatus_Notgrab		= 4;	//不抢庄
	const AccountStatus_Grab		= 5;	//抢庄
	const AccountStatus_Bet			= 6;	//下注
	const AccountStatus_Notshow		= 7;	//未摊牌
	const AccountStatus_Show		= 8;	//摊牌
    const AccountStatus_Watch	    = 9;	//观战

	/*	在线状态	*/
	const OnlineStatus_Offline		= 0;	//离线
	const OnlineStatus_Online		= 1;	//在线

    /* 观众状态 */
    const AudienceStatus_on			= 1;	//加入观战
    const AudienceStatus_off		= 0;	//离开观战

	/*	游戏中状态	*/
	const PlayingStatus_Waiting		= 1;	//等待别人中
	const PlayingStatus_Betting		= 2;	//下注中
	const PlayingStatus_show		= 3;	//已摊牌
	
	/*	房间状态	*/
	const RoomStatus_Waiting		= 1;	//等待中
	const RoomStatus_Playing		= 2;	//游戏中
	const RoomStatus_Closed			= 3;	//已关闭
	
	/*	操作超时时间	*/
	const LimitTime_StartGame		= 15;	//第一局开局时限
	const LimitTime_Ready			= 9;	//每局之间时限
	const LimitTime_Grab			= 10;	//抢庄时限
	const LimitTime_Betting			= 15;	//下注时限
	const LimitTime_Show			= 8;	//摊牌时限
	const LimitTime_ClearRoom		= 600;	//清理房间时间

	/*	游戏回合	*/
	const Circle_Grab				= 1;	//叫庄
	const Circle_Bet				= 2;	//下注
	const Circle_Show				= 3;	//摊牌

	/*	房间类型	*/
	const BankerMode_FreeGrab		= 1;	//自由抢庄
	const BankerMode_SeenGrab		= 2;	//明牌抢庄
	const BankerMode_TenGrab		= 3;	//牛牛上庄
	const BankerMode_NoBanker		= 4;	//通比牛牛
	const BankerMode_FixedBanker	= 5;	//固定庄家
	
	const LimitTime_Grab_1			= 10;	//抢庄时限 自由叫庄
	const LimitTime_Betting_1		= 15;	//下注时限 自由叫庄

	const LimitTime_Grab_2			= 10;	//抢庄时限 明牌抢庄
	const LimitTime_Betting_2		= 15;	//下注时限 明牌抢庄

	const LimitTime_Grab_3			= 0;	//抢庄时限 牛牛上庄
	const LimitTime_Betting_3		= 15;	//下注时限 牛牛上庄

	const LimitTime_Grab_4			= 0;	//抢庄时限 通比牛牛
	const LimitTime_Betting_4		= 0;	//下注时限 通比牛牛

	const LimitTime_Grab_5			= 0;	//抢庄时限 固定庄家
	const LimitTime_Betting_5		= 15;	//下注时限 固定庄家
}
