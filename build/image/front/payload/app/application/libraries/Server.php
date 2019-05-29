<?php  if ( ! defined('BASEPATH')) exit('No direct script access allowed');

class Server
{	
	//默认头像
	const Default_Headphoto_Url			= '123.jpg';
	//默认视频缩略图
	const Default_Video_Thumbnail		= "5e59796e94f94d37394cb5395588901e.png?imageView2/1/w/170/h/170";
	//匿名图片
	const Default_Anonymous_Headphoto	= '123.jpg';
	//默认菜单图片
	const Default_Square_menu_photo		= '123.jpg';
	
	const Default_Anonymous_Img			= 'default_anonymous_img.jpeg';
	
	
	//话题可发送条数
	const CAN_APPERA_TOPIC_COUNT		= 100;
	const TOPIC_REVIEW_SWITCH			= 2;		//话题审核开关	 0不审核，2开启审核
	
	
	//下载地址
	const DOWNLOAD_URL_IOS				= 'http://a.app.qq.com/o/simple.jsp?pkgname=com.our.doing&g_f=991653';
	const DOWNLOAD_URL_ANDROID			= 'http://a.app.qq.com/o/simple.jsp?pkgname=com.our.doing';
	
	
	//个推推送参数
	const IGETUI_NODEJS_URL				= "http://127.0.0.1:13308/";
	const IGETUI_APPSECRET				= "ROs77exNJq6El10XhCGkM6";
	const IGETUI_APPID					= "7TXgC7xwCB66p9sOVaZG65";
	const IGETUI_APPKEY					= "ggVOpDJvcw78A4qKVGosR3";
	const IGETUI_MASTERSECRET			= "r4poEzicbN8ZYLrXF3AmVA";
	
	//apns pem
	const Apns_Nodejs_URL				= "http://127.0.0.1:13310/";
	
	const Apns_Cert_Pem					= "aps_development_cert.pem";					//20150821,有效期一年
	const Apns_Key_Pem					= "aps_development_key.pem";					//20150821,有效期一年
	
	const Apns_Gateway_Url				= "gateway.sandbox.push.apple.com";
	const Apns_Gateway_Port				= 2195;
	
	//redis
	const Redis_HostPort				= "tcp://127.0.0.1:7100/";		
	const Redis_Password				= "907af03099401cbe453eef13600c8e0f";
	
	//官方账号ID
	const OA_10000						= 88;			//10;
	
	
	/*--------短信通--------*/
	const DXT_Api_Url					= "http://apis.baidu.com/kingtto_media/106sms/106sms";
	const DXT_Api_Key					= "6052d854945fd1e51969c5d6950860fd";
	
	//创蓝发送短信接口URL, 如无必要，该参数可不用修改
	const API_SEND_URL = 'http://222.73.117.158/msg/HttpBatchSendSM';
	//创蓝短信余额查询接口URL, 如无必要，该参数可不用修改
	const API_BALANCE_QUERY_URL = 'http://222.73.117.158/msg/QueryBalance';
	//创蓝账号 替换成你自己的账号
	const API_ACCOUNT	= 'szldy88';
	//创蓝密码 替换成你自己的密码
	const API_PASSWORD	= 'Tch20160520';
	
	
	
	
	
	//默认关注企业
	const RegisterFollow_DefaultCID		= 1;		//
}


/* End of file constants.php */
/* Location: ./application/config/constants.php */