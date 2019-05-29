<?php

require_once __DIR__ . '/../../../../define.php';

class Config
{
	//redis logs
    const Redis_HostPort_Logs	= REDIS_HOST;
    const Redis_Password_Logs	= REDIS_PWD;
    const Redis_Database_Logs	= 1;
    const Redis_HostPort		= REDIS_HOST;
    const Redis_Password		= REDIS_PWD;
    const Redis_Database		= 10;

	//db
    const DB_Host			= DB_HOST;
    const DB_Name			= DB_NAME;
    const DB_User			= DB_USER;
    const DB_Password		= DB_PWD;
	const DB_Port			= "3306";
	const DB_Charset		= "utf8";

	const Processor_Port = "12002";
	const Processor_Address =  "127.0.0.1";
	const Timer_Port =  "20079";
	const Timer_Address =  "127.0.0.1";

	const GameClient_Port =  "20072";
	const GameServer_Port =  "20071";
	const GameWeb_Port    =  "20070";
	
	const Ticket_Mode	= 2;	//房卡支付形式，1AA，2房主
	const Room_IsReuse	= 0;	//房间是否重复使用，0否1是
	const GameNum_EachRound = 3;	//每轮局数
	const Room_AutoBreak	= 1;	//房间是否自动解散，0否1是
    const Log_Dir	=	 LOG_DIR . "vflower10/";

	const Is_SendGameScore     = 0;
	const Dealer_Num   	= 2;
	const Verification_Code = "987654321098765432109876543210zz";

	const Bean_Type_1 = 50;
	const Bean_Type_2 = 100;
	const Bean_Type_3 = 300;
	const Bean_Type_4 = 500;
}
