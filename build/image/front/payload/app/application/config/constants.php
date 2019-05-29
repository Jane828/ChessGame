<?php  if ( ! defined('BASEPATH')) exit('No direct script access allowed');

/*
|--------------------------------------------------------------------------
| File and Directory Modes
|--------------------------------------------------------------------------
|
| These prefs are used when checking and setting modes when working
| with the file system.  The defaults are fine on servers with proper
| security, but you may wish (or even need) to change the values in
| certain environments (Apache running a separate process for each
| user, PHP under CGI with Apache suEXEC, etc.).  Octal values should
| always be used to set the mode correctly.
|
*/
define('FILE_READ_MODE', 0644);
define('FILE_WRITE_MODE', 0666);
define('DIR_READ_MODE', 0755);
define('DIR_WRITE_MODE', 0777);

/*
|--------------------------------------------------------------------------
| File Stream Modes
|--------------------------------------------------------------------------
|
| These modes are used when working with fopen()/popen()
|
*/

define('FOPEN_READ',							'rb');
define('FOPEN_READ_WRITE',						'r+b');
define('FOPEN_WRITE_CREATE_DESTRUCTIVE',		'wb'); // truncates existing file data, use with care
define('FOPEN_READ_WRITE_CREATE_DESTRUCTIVE',	'w+b'); // truncates existing file data, use with care
define('FOPEN_WRITE_CREATE',					'ab');
define('FOPEN_READ_WRITE_CREATE',				'a+b');
define('FOPEN_WRITE_CREATE_STRICT',				'xb');
define('FOPEN_READ_WRITE_CREATE_STRICT',		'x+b');


/*
|--------------------------------------------------------------------------
| Server Parameter Modes
|--------------------------------------------------------------------------
|
|
*/



/*
|--------------------------------------------------------------------------
| Database Modes
|--------------------------------------------------------------------------
|
|
*/
/*-----------服务器模块-----------*/			
define('T_Server_Parameter', 	'tbl_server_parameter');		//服务器参数表
define('T_Logs_Client', 		'tbl_logs_client');				//客户端日志表
define('T_Version_Android', 	'tbl_version_android');			//安卓版本更新表
define('T_SMS', 				'tbl_sms');						//短信验证码表
define('T_Mobile_Parameter',	'tbl_mobile_parameter');		//手机号码参数表

/*-----------设备模块-----------*/
define('T_Device_Info', 		'tbl_device_info');		//设备信息表


/*-----------用户模块-----------*/
define('T_User_Account', 		'tbl_user_account');			//用户账号表
define('T_User_Info', 			'tbl_user_info');				//用户信息表
define('T_User_Headphoto', 		'tbl_user_headphoto');			//用户头像表
define('T_User_LoginStatus', 	'tbl_user_login_status');		//用户登陆状态表
define('T_User_Operation', 		'tbl_user_operation');			//用户操作表
define('T_User_Parameter',	 	'tbl_user_parameter');			//用户参数表

/*-----------记录模块-----------*/
//define('T_Record_Relation', 		'tbl_record_relation');			//记录回复表
define('T_Record_Photo', 			'tbl_record_photo');			//记录图片表
define('T_Record_Like', 			'tbl_record_like');				//记录点赞表
define('T_Record', 					'tbl_record');					//记录内容表
define('T_Record_Comment', 			'tbl_record_comment');			//记录回复表
define('T_Record_Comment_Second', 	'tbl_record_comment_second');	//二级回复表
define('T_Record_Anonymous', 		'tbl_record_anonymous');		//记录匿名表
define('T_Record_Shield',	 		'tbl_record_shield');			//记录屏蔽表
define('T_Record_Report',	 		'tbl_record_report');			//记录举报表
define('T_Record_Topic',	 		'tbl_record_topic');			//记录频道表



/*-----------推送模块-----------*/
define('T_Push_Igetui', 		'tbl_push_igetui');				//用户推送表（个推）

/*-----------消息模块-----------*/
define('T_Message_Notification', 		'tbl_message_notification');	//消息通知表
define('T_Message_Unread', 				'tbl_message_unread');			//未读消息表


/*-----------反馈模块-----------*/
define('T_Feedback_Customer', 		'tbl_feedback_customer');	//客户反馈表

/*-----------话题模块-----------*/
define('T_Topic_Content', 		'tbl_topic_content');	//话题内容表


/*-----------广场模块-----------*/
define('T_Square_Menu', 		'tbl_square_menu');		//广场菜单表

/*-----------商城模块-----------*/







/*-----------CMS模块-----------*/
define('C_Admin_Account', 		'cms_admin_account');		//管理员账号表	

/* End of file constants.php */
/* Location: ./application/config/constants.php */