<?php

class Qiniu
{	
	/*	office web 365	*/
	//const Preview_URL					= 'http://officeweb365.com/o/?i=9703&n=1&furl=';
	const Preview_URL					= 'http://officeweb365.com/o/?i=9703&furl=';
	
	/*	common	*/
	const Timeout						= 2592000;
	const AccessKey 					= "pVklQKZyTHJ2Zx-oeFPK_hkSSVWbraM0QuTrOE9k";		//
	const SecretKey 					= "12DjNf96fph88CHK7_hSCeV9F_dE6SjUTngYfZyv";		//
	
	//const Avatar_Bucket 				= "ldyfexdev";		//
	//const Avatar_Host 				= "http://oss.fexteam.com/";		//http://7xs7g7.com1.z0.glb.clouddn.com/
	const OSS_Bucket 					= "zht";		//
	const OSS_Host 						= "http://oss.zht66.com/";		//http://7xs7g7.com1.z0.glb.clouddn.com/
	
	const Default_ProductImg			= "default_product.jpg";
	const Default_AvatarImg				= "default_avatar.png";
	
	const LongImg_Width_Ratio			= 4.83;		//长图比，宽
	const LongImg_Height_Ratio			= 2.67;		//长图比，高
	const LongImg_EP_Px					= 400;
	
	const LongImg_Crop_Size				= "imageMogr2/crop/[MaxSize]x[MaxSize]";	//长图的裁剪尺寸
	
	const Small_Size					= "?imageView2/1/w/170/h/170";
	const EP_Size						= "?imageMogr2/thumbnail/!200x200r";
	
	const Record_EP_Size				= "?imageView2/0/w/400/h/400";
	const Topic_Small_Size				= "?imageView2/1/w/170/h/170";
	
	//const Private_Small_Size			= "?imageView2/0/w/300/h/300";
	const Private_Small_Size			= "?imageView2/0/q/30";
	
	const Video_Thumbnail				= "?imageMogr2/crop/383x287";
	const Default_Video_Thumbnail		= "5e59796e94f94d37394cb5395588901e.png?imageView2/1/w/170/h/170";
	
	//主题默认图片
	const Chat_Img_Size					= "?imageView2/1/w/640";
	
	const Chat_Theme_DefaultImg			= "5e59796e94f94d37394cb5395588901e.png?imageView2/1/w/170/h/170";
	
	//默认头像
	const Chatlist_Helper_Avatar		= "team_helper_2.png?imageView2/1/w/170/h/170";
	const Chatlist_All_Avatar			= "team_all.png?imageView2/1/w/170/h/170";
	
	//文件类型
	const File_Img_Unknow				= 'fexdev/chat_files_unknow@2x.png';
	const File_Img_Image				= '';
	const File_Img_Word					= 'fexdev/chat_files_word@2x.png';
	const File_Img_PDF					= 'fexdev/chat_files_pdf@2x.png';
	const File_Img_PPT					= 'fexdev/chat_files_ppt@2x.png';
	const File_Img_Excel				= 'fexdev/chat_files_excel@2x.png';
	const File_Img_Video				= 'fexdev/chat_files_video@2x.png';
	const File_Img_Music				= 'fexdev/chat_files_music@2x.png';	


}


/* End of file constants.php */
/* Location: ./application/config/constants.php */