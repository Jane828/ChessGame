<?php


class Server
{	
	
	
	/*****************************
				dir
	*****************************/
	const LOG_FILE_DIR		= "/mnt/log/find/development/gateway.log";
	
	/*****************************
				参数
	*****************************/
	
	/*
		client type
	*/
	const Client_Type_IOS			= 0;
	const Client_Type_Android		= 1;
	const Client_Type_Web			= 2;
	const Client_Type_PC			= 3;
	
	
	/*	server push type	*/
	const Server_PushType_Single	= 1;
	const Server_PushType_Group		= 2;
	const Server_PushType_Client	= 3;
	
	/*
		服务器推送通知控制
	*/
	const GroupNoty_Type_Add			= 2;
	const GroupNoty_Type_Remove			= 3;
	const GroupNoty_Type_Admin			= 4;
	const GroupNoty_Type_Name			= 5;
	
	
}


/* End of file constants.php */
/* Location: ./application/config/constants.php */