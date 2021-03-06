<?php

/*	Common	*/
define("WX_Account",            "wechat_account");

/*	Game	*/
define("Room",                  "sangong_room");

define("Room_Ticket_Journal",   "room_ticket_journal");
define("Room_Ticket", 			"room_ticket");

define("Room_Scoreboard", 		"room_scoreboard");

define("Game_Announcement", 	"game_announcement");
define("Game_Whilelist", 		"game_whilelist");

define("Room_GameResult", 		"room_game_result");
define("Room_GameScore", 		"room_game_score");

define("Room_Account", 			"room_account");
define("Manage_Member",			"manage_member");
define("Box_Info",				"box_info");			//包厢信息表
define("Box_Room",				"box_room");			//包厢-房间表

class DB_CONST
{
	//数据库查询结果
	const SUCCESS 				= 0;		//操作成功
	const FAILED 				= -1;		//操作失败
	
	const INSERT_FAILED 		= -1;		//添加失败
	
	const UPDATE_SUCCESS		= 0;		//修改成功
	const UPDATE_FAILED		    = -2;		//修改失败
	
	const DATA_NONEXISTENT		= -2;		//数据不存在
	const DATA_EXIST 			= 0;		//数据存在
	
	
}


/* End of file constants.php */
/* Location: ./application/config/constants.php */