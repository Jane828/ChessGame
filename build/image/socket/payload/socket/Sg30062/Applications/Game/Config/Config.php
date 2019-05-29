<?php

require_once __DIR__ . '/../../../../define.php';

class Config{

	//------------------ pe ------------------
	//redis logs
    const Redis_HostPort_Logs	= REDIS_HOST;
    const Redis_Password_Logs	= REDIS_PWD;
    const Redis_Database_Logs	= REDIS_DB_LOGS;// 1;
    const Redis_HostPort		= REDIS_HOST;
    const Redis_Password		= REDIS_PWD;
    const Redis_Database		= REDIS_DB; //10;

	//db
    const DB_Host			= DB_HOST;
    const DB_Name			= DB_NAME;
    const DB_User			= DB_USER;
    const DB_Password		= DB_PWD;
	const DB_Port			= DB_PORT;//"3306";
	const DB_Charset		= DB_CHARSET;//"utf8mb4";

	const Processor_Port = PROCESSOR_PORT;//"12002";
	const Processor_Address =  PROCESSOR_ADDR;//"127.0.0.1";
	const Timer_Port =  TIMER_PORT;//"30019";
	const Timer_Address =  TIMER_ADDR;//"127.0.0.1";

	const GameClient_Port =  GAME_CLIENT_PORT;//"30012";
	const GameServer_Port =  GAME_SERVER_PORT;//"30011";
	const GameWeb_Port    =  GAME_WEB_PORT;//"30010";
	
	const Ticket_Mode	= TICKET_MODE;//2;	//房卡支付形式，1AA，2房主
	const Room_IsReuse	= ROOM_IS_REUSE;//0;	//房间是否重复使用，0否1是
	const GameNum_EachRound = GAME_NUM_REACH_ROUND;//10;	//每轮局数
	const Room_AutoBreak	= ROOM_AUTO_BREAK;//1;	//房间是否自动解散，0否1是
    const Log_Dir	=	 LOG_DIR2;//LOG_DIR . "sg6/";

	const Is_SendGameScore     = IS_SEND_GAME_SCORE;//0;
	const Dealer_Num   	= DEALER_NUM;//2;
	const Verification_Code = VERIFICATION_CODE;//"987654321098765432109876543210zz";

	const Can_BreakRoom         = 1;	//是否能主动下庄

	//底分类型
	const Rule_ScoreType_1		= 1;
	const Rule_ScoreType_2		= 2;
	const Rule_ScoreType_3		= 3;
	const Rule_ScoreType_4		= 4;
	const Rule_ScoreType_5		= 5;
	const Rule_ScoreType_6      = 6;

	/*	上庄分数	*/
	const BankerScore_1		= 0;
	const BankerScore_2		= 100;
	const BankerScore_3		= 300;
	const BankerScore_4		= 500;
	
	/*	游戏人数范围	*/
	const GameUser_MinCount		= 2;
	const GameUser_MaxCount		= 6;

	/*	明牌抢庄倍数选择	*/
	const GrabCanBet_1          = 1;
	const GrabCanBet_2          = 2;
	const GrabCanBet_3          = 4;

	/*	闲家下注倍数选择	*/
	const PlayerCanBet_1          = 1;
	const PlayerCanBet_2          = 2;
	const PlayerCanBet_3          = 4;
	const PlayerCanBet_4          = 5;
	
}
