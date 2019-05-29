<?php  

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
	
	const IS_FORWARD_FALSE				= 0;		//非转发
	const IS_FORWARD_TRUE				= 1;		//转发
	const IS_FORWARD_DELETE				= 2;		//转发已删除
	const DELETE_FORWARD_CONTENT		= "内容已被删除";		//转发内容被删除
	const CAN_FORWARD_FALSE				= 0;		//不可转发
	const CAN_FORWARD_TRUE				= 1;		//可转发
	
	const SQL_IN_NONEXISTENT			= "0";		//sql语句，where xxx in (null)
	
	const Return_Type_Original			= "0";
	const Return_Type_String			= "1";
	const Return_Type_Array 			= "2";
	
	//socketk开始符和结束符
	const SOCKET_START_CHAR 			= "\n";
	const SOCKET_END_CHAR 				= "\n";
	
}


/* End of file constants.php */
/* Location: ./application/config/constants.php */