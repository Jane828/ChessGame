<?php


class Game
{	
	
	/*****************************
				参数
	*****************************/


	//游戏类型
	const Game_Type = 95;	//vip10人炸金花

	/*	房间用户uid	*/
	const RoomUser_UID			= "[roomid]:[accountid]";
	
	/*	开局默认分数	*/
	const Default_Score			= 4;
	
	
	/*	游戏人数范围	*/
	const GameUser_MinCount		= 2;
	const GameUser_MaxCount		= 6;
	
	
	/*	用户状态	*/
	const AccountStatus_Initial		= 0;	//首次进房初始状态
	const AccountStatus_Notready	= 1;	//未准备
	const AccountStatus_Ready		= 2;	//已准备
	//const AccountStatus_Waiting		= 3;	//已开局未选择
	const AccountStatus_Visible		= 4;	//看牌
	const AccountStatus_Invisible	= 5;	//闷牌
	const AccountStatus_Giveup		= 6;	//弃牌
	const AccountStatus_Lost		= 7;	//比牌输

	/*	游戏中状态	*/
	const PlayingStatus_Waiting		= 1;	//等待别人中
	const PlayingStatus_Betting		= 2;	//下注中
	
	/*	房间状态	*/
	const RoomStatus_Waiting		= 1;	//等待中
	const RoomStatus_Playing		= 2;	//游戏中
	const RoomStatus_Closed			= 3;	//已关闭
	
	/*	操作超时时间	*/
	const LimitTime_StartGame		= 20;	//第一局开局时限
	const LimitTime_Ready			= 10;	//准备时限
	const LimitTime_Betting			= 20;	//下注时限
	const LimitTime_ClearRoom		= 600;	//时限

	/*	牌型概率	千分之几  */
	const Probability_Santiao		= 8;
	const Probability_Tonghuashun	= 7;
	const Probability_Tonghua   	= 60;
	const Probability_Shunzi   	 	= 60;
	const Probability_Duizi  	 	= 270;
	const Probability_Gaopai  	 	= 595;
}


/* End of file constants.php */
/* Location: ./application/config/constants.php */
