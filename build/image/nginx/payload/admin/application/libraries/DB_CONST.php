<?php  if ( ! defined('BASEPATH')) exit('No direct script access allowed');

define('D_Dealer_Account', 			'dealer_account');					//代理商列表

/*	Common	*/
define("WX_Account", 					"wechat_account");				//微信账号表
define("Room_Ticket", 					"room_ticket");				//房票
define("Room_TicketJournal", 			"room_ticket_journal");				//房票
define("Room_Scoreboard", 					"room_scoreboard");				//
define("Room_Account",                      "room_account");

define("Payment_Goods", 					"payment_goods");				//商品列表
define("Payment_Order", 					"payment_order");				//支付订单
define("Payment_OrderGoods", 				"payment_order_goods");			//订单商品
define("PaymentDetail_Wxpay", 				"payment_detail_wxpay");		//支付信息，微信

define("Act_Detail", 					"activity_detail");				//
define("Act_Partake", 					"activity_partake");				//

define("Act_Redenvelop", 					"activity_redenvelop");				//红包记录
define("Act_RedenvelopReceive", 			"activity_redenvelop_receive");		//红包收取记录

define("Act_SlotMachine", 				"activity_slotmachine");
define("Act_SlotMachine_Mode", 			"activity_slotmachine_mode");

/*	Game	*/
define("Room", 					"flower_room");				//
define("L_Room", 					"landlord_room");				//
define("GLZ_Room", 					"gunlunzi_room");				//

/*	Dealer	*/
define("D_Account", 					"dealer_account");
define("D_Bind", 						"dealer_bind");
define("D_Journal", 					"dealer_journal");

define("D_Redenvelop", 					"dealer_redenvelop");
define("D_RedenvelopReceive", 			"dealer_redenvelop_receive");

define("D_Recharge", 					"dealer_recharge");

define("Game_List", 					"game_list");
define("Game_Announcement",             "game_announcement");
define("Game_Broadcast",             "game_broadcast");

define("Room_GameResult", 					"room_game_result");
define("Room_ScoreBoard", 					"room_scoreboard");
define("Room_GameScore", 					"room_game_score");

define("Room_Bull", 					"bull_room");
define("Room_Flower", 					"flower_room");
define("Room_GDMJ", 					"gd_mahjong_room");
define("Room_Landlord", 				"landlord_room");
define("Room_Sangong", 				    "sangong_room");

define("Room_TicketAdjustment", 		"room_ticket_adjustment");

define("Summary_Account", 					"summary_account");
define("Summary_AccountDaily", 				"summary_account_daily");
define("Summary_Dealer", 					"summary_dealer");

define("Noty_Message", 			"noty_message");
define("Announcement_Detail", 			"announcement_detail");

/*	直营代理	*/
define("Agent_Bind", 			"agent_bind");

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