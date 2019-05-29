<?php  

class OPT_CONST
{
	//操作结果常量
	
	const NO_RETURN				= 5;		//不需返回结果
	const LOGIN_TIMEOUT 		= 4;		//登陆信息已过期
	const LOGIN_INCORRECT		= 3;		//登陆信息不正确
	const NULL_DATA   			= 2;		//data数组为空
	const LASTEST_DATA 			= 1;		//已是最新数据，不需下载
	
	const SUCCESS 				= 0;		//操作成功
	const FAILED  				= -1;		//操作失败
	
	const DATA_NONEXISTENT 		= -2;		//数据不存在
	const CORRECT_FORMAT	 	= -3;		//JSON格式不正确
	const FAILED_REDISAUTH		= -4;		//redis连接失败
	const DATAKEY_EXISTS		= -5;		//data_key已存在
	const REQUEST_TOOFAST		= -6;		//请求频率太高
	const REQUEST_REJECT_STRANGER = -7;		//对方拒绝接收陌生人消息
        const CHIP_AMOUNT_LIMIT      = -8;     //下注达到上限
	const MISSING_PARAMETER 	= -20;		//缺少参数
	const MISSING_OPERATION 	= -100;		//缺少操作码
	const MISSING_MODULE 		= -101;		//缺少模块码
	const USER_NONEXISTENT		= -201;		//用户不存在
	const PERMISSION_DENIED		= -202;		//用户无权限操作
	const LEAVE_GROUP			= -203;		//用户已离开讨论组
	const ACCOUNT_NONEXISTENT	= -301;		//账号不存在
	const USER_BEBLACKED		= -302;		//用户被拉黑
	const NOTE_DELETEED			= -303;		//内容被删除
	const APPID_BEFREEZED		= -401;		//APPID被冻结
	const UID_BEFREEZED			= -402;		//UID被冻结
	
	const OPERATION_TRUE				= true;		//操作结果，是
	const OPERATION_FALSE				= false;	//操作结果，否
	
	//json
	const JSON_TRUE				= true;		//是否JSON格式——正确
	const JSON_FALSE			= false;	//是否JSON格式——不正确
	
	//post_array
	const POSTARRAY_TRUE		= true;		//数据格式正确
	const POSTARRAY_FALSE		= false;	//数据格式不正确
	
	//login session
	const LOGIN_TRUE			= true;		//是否已登录——正确
	const LOGIN_FALSE			= false;	//是否已登录——不正确
	
	//qiniu token
	const QT_TRUE				= true;		//正确
	const QT_FALSE				= false;	//不正确
	
	//默认返回值
	const Req_Json_False_Ret	= '{"result":"-3","result_message":"JSON格式不正确"}';	//request json错误返回值
}


/* End of file constants.php */
/* Location: ./application/config/constants.php */