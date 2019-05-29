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

	//游戏类型 ：1炸金花 2斗地主  3梭哈  4德州  5六人斗牛 6广东麻将   9九人斗牛，10轮庄,12 12人牛牛，13 13人牛牛
	const Game_Type	= 13;
	
	/*	房间用户uid	*/
	const RoomUser_UID			= "[roomid]:[accountid]";

	/*  普通房间 - 包厢房间*/
	const ORDINARY_ROOM				= 0;	//普通房间
	const Enable_Box				= 1;	//启用包厢
	
	/*	开局默认分数	*/
	const Default_Score				= 5;
	const Default_SpendTicketCount	= 1;



	/*	规则		*/
	//底分类型
	// const Rule_ScoreType_1			= 1;
	// const Rule_ScoreType_2			= 3;
	// const Rule_ScoreType_3			= 5;

	//房卡类型
	const Rule_TicketType_1			= 2;
	const Rule_TicketType_2			= 4;

	//特殊牌型
        const Rule_CardFour_Multiple		= 4;	//四花牛4倍
	const Rule_CardFive_Multiple		= 5;	//五花牛5倍
	const Rule_CardBomb_Multiple		= 6;	//炸弹牛6倍
	const Rule_CardTiny_Multiple		= 8;	//五小牛8倍

    const Rule_CardStraight_Multiple	= 6;	//顺子牛6倍
    const Rule_CardFlush_Multiple		= 6;	//同花牛6倍
    const Rule_CardHulu_Multiple		= 6;	//葫芦牛6倍
    const Rule_CardStraightflush_Multiple = 7;	//同花顺7倍

	//牛牛3倍，牛九3倍，牛八2倍
	const Rule_Card7_Multiple_1		= 1;	//牛7
	const Rule_Card8_Multiple_1		= 2;	//牛8
	const Rule_Card9_Multiple_1		= 2;	//牛9
	const Rule_Card10_Multiple_1	= 3;	//牛10

	//牛牛4倍，牛九3倍，牛八牛七2倍
	const Rule_Card7_Multiple_2		= 2;	//牛7
	const Rule_Card8_Multiple_2		= 2;	//牛8
	const Rule_Card9_Multiple_2		= 3;	//牛9
	const Rule_Card10_Multiple_2	= 4;	//牛10

	

	
	/*	游戏人数范围	*/
	const GameUser_MinCount		= 2;
	const GameUser_MaxCount		= 13;
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
	const LimitTime_Ready			= 11;	//每局之间时限
	const LimitTime_Grab			= 10;	//抢庄时限
	const LimitTime_Betting			= 15;	//下注时限
	const LimitTime_Show			= 30;	//摊牌时限
	const LimitTime_ClearRoom		= 300;	//清理房间时间


	/*	倍率	*/
	const Cardtype_Boom				= 6;	//炸弹
	const Cardtype_Five				= 5;	//五花牛
	const Cardtype_Zero				= 3;	//牛牛
	const Cardtype_Double			= 2;	//7到9点
	const Cardtype_Default			= 1;	//1到6点

	/*	游戏回合	*/
	const Circle_Grab				= 1;	//叫庄
	const Circle_Bet				= 2;	//下注
	const Circle_Show				= 3;	//摊牌



	/*	房间类型	*/
	const BankerMode_FreeGrab			= 1;	//自由抢庄
	const BankerMode_SeenGrab			= 2;	//明牌抢庄
	const BankerMode_TenGrab			= 3;	//牛牛上庄
	const BankerMode_NoBanker			= 4;	//通比牛牛
	const BankerMode_FixedBanker		= 5;	//固定庄家
	
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

    const TimesTypes = [
        1 => [1, 2, 4, 5],
        2 => [1, 3, 5, 8],
        3 => [2, 4, 6, 10]
    ];

}


/* End of file constants.php */
/* Location: ./application/config/constants.php */
