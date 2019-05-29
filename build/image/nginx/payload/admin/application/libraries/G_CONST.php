<?php  if ( ! defined('BASEPATH')) exit('No direct script access allowed');

class G_CONST
{	
	
	//常用参数
	const SUCCESS 						= 0;		//操作成功
	const FAILED 						= -1;		//操作失败
	const EMPTY_STRING					= "";		//空字符串
	//const EMPTY_ARRAY					= array();	//空数组
	const DATA_EXIST					= 0;		//数据存在
	const DATA_NONEXISTENT				= 1;		//数据不存在
	const DEFAULT_COUNT					= 0;		//默认数字，0

		//数据是否
	const IS_TRUE						= 1;		//是
	const IS_FALSE						= 0;		//否
	
	//短信内容
	const SMS_Type_Register				= 0;	//注册
	const SMS_Type_Retrieve				= 1;	//找回
	const SMS_Type_Bind					= 2;	//绑定
	
	const SMS_Invaild_Time				= 600;	//验证码过期时间
	const SMS_Interval_Time				= 60;	//短信间隔
	const SMS_Daily_LimitTimes			= 20;	//每日发送条数显示
	
	//同步参数
	const DEFAULT_SYNC_LIMIT			= 20;		//记录每次同步条数
	const DEFAULT_SYNC_DONE				= 2;		//同步完成标识符
	
	//登陆过期时间
	const LOGIN_SESSION_TIMEOUT 		= 604800;
		
	//账号类型
	const Account_Find					= 0;	//find
	const Account_QQ					= 1;	//qq
	const Account_Weibo					= 2;	//weibo
	const Account_Mobile				= 3;	//mobile
	const Account_WX					= 4;	//wx
	
	//性别
	const MALE 							= "男";
	const FEMALE 						= "女";
	
	//数据修改符号
	const ADDCOUNT						= "+";
	const REDUCECOUNT					= "-";
	
	//删除
	const CAN_DELETE_TRUE 				= 1;		//可删除
	const CAN_DELETE_FALSE 				= 0;		//不可删除
	
	
}


/* End of file constants.php */
/* Location: ./application/config/constants.php */