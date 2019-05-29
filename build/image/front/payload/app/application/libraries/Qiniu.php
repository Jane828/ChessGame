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
	//const Avatar_Host 				= "http://oss.fexteam.com/";				//http://7xs7g7.com1.z0.glb.clouddn.com/
	const OSS_Bucket 					= "zht66";		//
	const OSS_Host 						= "http://oss.zht66.com/";		//http://7xs7g7.com1.z0.glb.clouddn.com/

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
	
	
	const Default_Service_Url			= "default_product.jpg";
	const Default_Logo_Url				= "default_logo.png";
	const Default_Banner_Url			= "1600.jpg";
	
	const Default_ProductImg			= "default_product.jpg";
	const Product_SmallSize				= "?imageView2/1/w/360/h/360";
	const Product_DetailImg				= "?imageView2/1/w/420/h/420";
	const Product_IntruductionlImg		= "?imageView2/2/w/420";
	const Product_EPSize				= "?imageView2/0/w/420/h/420";
	
	const Product_VideoAvatarSize		= "?vframe/jpg/offset/1/w/460/h/260";
	
}


/* End of file constants.php */
/* Location: ./application/config/constants.php */