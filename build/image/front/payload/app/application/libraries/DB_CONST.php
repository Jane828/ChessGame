<?php  if ( ! defined('BASEPATH')) exit('No direct script access allowed');

/*	Common	*/
define("Server_Parameter", 					"server_parameter");				//微信账号表
define("WX_Account", 					"wechat_account");				//微信账号表
define("Room_Ticket", 					"room_ticket");				//房票
define("Room_TicketJournal", 			"room_ticket_journal");				//房票
define("Room_Scoreboard", 					"room_scoreboard");				//
define("Room_Create_Info",              "room_create_info");
define("Room_Account",                  "room_account");

define("Sms_Detail", 					"sms_detail");				//账号短信表

define("Payment_Goods", 					"payment_goods");				//商品列表
define("Payment_Order", 					"payment_order");				//支付订单
define("Payment_OrderGoods", 				"payment_order_goods");			//订单商品
define("PaymentDetail_Wxpay", 				"payment_detail_wxpay");		//支付信息，微信

define("Act_Detail", 					"activity_detail");				//
define("Act_Partake", 					"activity_partake");				//

define("Act_Redenvelop", 					"activity_redenvelop");				//红包记录
define("Act_RedenvelopReceive", 			"activity_redenvelop_receive");		//红包收取记录

define("Act_Sign", 						"activity_sign");						//每周签到
define("Act_SignPartake", 				"activity_sign_partake");				//签到记录

define("Act_Fortunewheel", 				"activity_fortunewheel");						//每周签到

define("Act_SlotMachine", 				"activity_slotmachine");
define("Act_SlotMachine_Mode", 			"activity_slotmachine_mode");

define('Clubs', 'clubs');
define('Club_Applies', 'club_applies');
define('Club_Consumes', 'club_consumes');
define('Club_ConsumeSets', 'club_consume_sets');
define('Club_Logs', 'club_logs');
define('Club_Players', 'club_players');
define('Club_Beans', 'club_beans');


/*	Gameroom	*/
define("Room_Flower", 					"flower_room");
define("Room_Bull", 					"bull_room");
define("Room_Sangong", 					"sangong_room");



/*	Dealer	*/
define("D_Account", 					"dealer_account");
define("D_Bind", 						"dealer_bind");
define("D_Commission", 					"dealer_commission");
define("D_Journal", 					"dealer_journal");

define("D_Redenvelop", 					"dealer_redenvelop");
define("D_RedenvelopReceive", 			"dealer_redenvelop_receive");


/*	wxauth */
define("WX_Parameter", 			"wxauth_parameter");


/*	dist */
define("Dist_Account", 				"dist_account");
define("Dist_Commission", 			"dist_commission");
define("Dist_CommissionRecord", 	"dist_commission_record");


/*	game	*/
define("Game_List", 				"game_list");

/*	guild	*/
define("Guild_Group", 				"guild_group");
define("Guild_Member", 				"guild_member");
define("Guild_CommissionRecord", 	"guild_commission_record");
define("Guild_Balance", 			"guild_balance");
define("Guild_Withdraw", 			"guild_withdraw");
define("Guild_CommissionRate", 	"guild_commission_rate");
define("Guild_ViceBalance", 	"guild_vice_balance");

/*	business	*/
define("Business_Account", 				"business_account");
define("Business_Detail", 				"business_detail");
define("Business_Withdraw", 			"business_withdraw");

/*	渠道	*/
define("Channel_Bind", 				"channel_bind");

define("Summary_Account", 					"summary_account");
define("Summary_AccountDaily", 				"summary_account_daily");
define("Summary_Dealer", 					"summary_dealer");

/*	房卡兑换	*/
define("Exchange_Ticket", 				"exchange_ticket");

define("Manage_Member", "manage_member");	//好友成员表
define("Box_Info", "box_info");				//包厢信息表
define("Box_Room", "box_room");				//包厢-房间关系表

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