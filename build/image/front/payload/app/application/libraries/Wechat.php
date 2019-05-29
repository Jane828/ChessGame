<?php  

class Wechat
{	
	
	
	//微信服务器请求地址
	const WX_Url_AccessToken		= 'https://api.weixin.qq.com/cgi-bin/token?grant_type=client_credential';
	const WX_Url_DeviceQrcode 		= 'https://api.weixin.qq.com/device/create_qrcode';
	const WX_Url_AuthorizeDevice 	= 'https://api.weixin.qq.com/device/authorize_device';
	const WX_Url_SendCustomMsg		= 'https://api.weixin.qq.com/cgi-bin/message/custom/send?access_token=';
	
	//用户信息
	//const WX_Url_Userinfo			= 'https://api.weixin.qq.com/cgi-bin/user/info';
	const WX_Url_Userinfo			= 'https://api.weixin.qq.com/sns/userinfo';
	
	const WX_Url_Menu				= 'https://api.weixin.qq.com/cgi-bin/menu/create?access_token=';
	
	//获取二维码(新接口)
	const WX_Url_DeviceQrcode_2 	= 'https://api.weixin.qq.com/device/getqrcode';  
	
	//模板消息接口
	const WX_Url_TemplateMsg 		= 'https://api.weixin.qq.com/cgi-bin/message/template/send';
	
	//发送信息给设备
	const WX_Url_TransMsg 			= 'https://api.weixin.qq.com/device/transmsg';
	
	//补充资料
	const Temple_ReplenishUserinfo 	= '1s2Sm2dReC7RCOnvQT3dvtyaMYRhhZSNajQIR4RlL20';
	//测量数据
	const Temple_MeasureResult 		= 'hlprZpi5QeUVTQunFEFbYxw2V1UH2Jey5Asv7RBUAp8';



}


/* End of file constants.php */
/* Location: ./application/config/constants.php */