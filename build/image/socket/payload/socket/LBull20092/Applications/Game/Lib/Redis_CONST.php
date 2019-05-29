<?php 

class Redis_CONST
{	
	
	/*****************************
				参数
	*****************************/
	const SUCCESS 						= 0;		//操作成功
	const FAILED  						= -1;		//操作失败
	const Key_Nonexistent				= -1;
	const Key_Exists					= true;
	const Member_Nonexistent			= false;
	const Member_Exists					= true;
	const DATA_NONEXISTENT 				= false;		//数据不存在
	
	/*
		Logs
	*/
	const GameResult_Key 				= "GR:[dealernum]:[gametype]";
	const GameScore_Key 				= "GS:[dealernum]:[gametype]";

	/*
		Room
		
		hash
	*/
	const Room_Key						= "9:Room:[roomid]";		
	const Room_Field_Number				= "number";				//房间号
	const Room_Field_GameRound			= "ground";				//当前轮数
	const Room_Field_GameNum			= "gnum";				//当前局数
	//const Room_Field_PeopleNum			= "pnum";				//房间人数
	const Room_Field_Status				= "stat";				//房间状态，1等待、2进行中、3关闭
	const Room_Field_DefaultScore		= "dfltScr";			//开局默认分数
	const Room_Field_ActiveUser			= "actUser";			//当前操作用户，默认-1

	const Room_Field_ActiveTimer		= "actTimer";			//当前计时器ID，默认-1
	const Room_Field_ReadyTime			= "readyTime";			//自动开局计时器设置时间，默认-1表示没有倒计时

	//const Room_Field_ClearId			= "clearId";			//当前扫房ID，默认-1
	//const Room_Field_ClearTime			= "clearTime";			//扫房ID，默认-1 表示没有


	const Room_Field_Creator			= "creator";			//房间创建者 account_id
	const Room_Field_Paytype			= "paytype";			//1AA,2房主扣卡
	const Room_Field_Scoreboard			= "scoreboard";			//每局积分榜
    const Room_Field_IsCustomScore	    = "isCustomScore";	    //自定义当前底分,默认0表示不是自定义选择
	const Room_Field_BaseScore			= "baseScore";			//当前底分
	const Room_Field_TicketCount		= "ticketcnt";			//每轮消耗房卡数量
	const Room_Field_TotalNum			= "totalnum";			//每轮总局数

	const Room_Field_RuleType			= "ruleType";		//规则类型
	const Room_Field_Card7				= "card7";			//牛7倍率
	const Room_Field_Card8				= "card8";			//牛8倍率
	const Room_Field_Card9				= "card9";			//牛9倍率
	const Room_Field_Card10				= "card10";			//牛牛倍率
	const Room_Field_CardFour           = "cardfour";       //四花牛倍率	
	const Room_Field_CardFive			= "cardfive";		//五花牛倍率
	const Room_Field_CardBomb			= "cardbomb";		//炸弹牛倍率
	const Room_Field_CardTiny			= "cardtiny";		//五小牛倍率
    const Room_Field_CardStraight		= "cardstraight";	//顺子牛倍率
    const Room_Field_CardFlush			= "cardflush";		//同花牛倍率
    const Room_Field_CardHulu			= "cardhulu";		//葫芦牛倍率
    const Room_Field_CardStraightflush	= "cardstraightflush";  //同花顺倍率

	const Room_Field_BankerMode			= "bankermode";		//庄家类型，1自由叫庄，2明牌抢庄，3牛牛上庄，4通比牛牛，5固定庄家
	const Room_Field_BankerScoreType	= "bankerscoretype";	//庄家上庄类型
	const Room_Field_BankerScore		= "bankerscore";	//庄家上庄类型

	const Room_Field_StartTime			= "startTime";			//开局时间
    const Room_Field_TimesType			= "timesType";			//倍数规则 1 2 3

    /*
        Room User Score
        总积分
        hash
    */
	const RoomScore_Key					= "9:RoomScore:[roomid]";
	const RoomScore_Field_User			= "[accountid]";
	
	
	/*
		Room Account User Status
		用户状态
		hash
	*/
	const AccountStatus_Key				= "9:AccStatus:[roomid]";
	
	const AccountStatus_Field_User			= "[accountid]";


	/*
		用户是否扣了房卡  hash
	*/
	const TicketChecked_Key				= "9:TicketChecked:[roomid]";
	
	/*
		Room Join Sequence  
		
		有序集合
			score	:	timestamp
			value	:	account_id
	*/
	const RoomSequence_Key				= "9:RoomSeq:[roomid]";
	
	/*
		叫分
		hash
	*/
	const Multiples_Key					= "9:Multiples:[roomid]";
	const Multiples_Field_User			= "[accountid]";

	/*
		是否已经摊牌  0尚未摊牌   1已经摊牌
		hash
	*/
	const ShowCard_Key				= "9:Show:[roomid]";
	
	/*
		手牌
		hash
	*/
	const Card_Key				= "9:Card:[roomid]";
	const Card_Field_User		= "[accountid]";

	/*
		当前游戏局参数
		hash
	*/
	const Play_Key						= "9:Play:[roomid]";		
	const Play_Field_Banker				= "banker";			//庄家 account_id
	const Play_Field_Circle 			= "circle";			//本局第几圈 1叫庄，2下注，3摊牌

	const Play_Field_BankerMult 		= "bankermult";		//庄家叫分倍数

	//const Play_Field_TimerId			= "timerId";			//当前计时器ID，默认-1
	//const Play_Field_TimerTime			= "timerTime";			//自动开局计时器设置时间，默认-1表示没有倒计时
	
	/*
		游戏局玩家队列  
	*/
	const PlayMember_Key				= "9:PlayMem:[roomid]";


	/*
		抢庄
		hash
	*/
	const Grab_Key				= "9:Grab:[roomid]";
	const Grab_Field_User		= "[accountid]";

    /*
        audience redis
        观战
    */
    const RoomAudience_Key		= "9:RoomAud:[roomid]";
    const RoomAudienceInfo_Key  = "9:RoomAudInfo:[roomid]";
}


/* End of file constants.php */
/* Location: ./application/config/constants.php */
