<?php

 include_once dirname(__DIR__). '/http.php';					//加载数据库操作类
 include_once 'common_model.php';	//加载数据库操作类
 class Wxpay_Model extends Payment_Common_Model
 {
	 
	public function __construct()
	{
		header('Content-Type: text/html; charset=utf-8'); 
		parent::__construct();
		$this->load->helper('url');
	}
	 
	
	
	
	/**************************************************
						common
	***************************************************/
	
	
	
	
	/*
		生成微信订单
	*/ 
	private function createWxpayPayment($arrData)
	{
		$out_trade_no = $arrData['order_no'];
		$total_price = $arrData['total_price'];
		$subject = $arrData['subject'];
		$body = $arrData['body'];
		$openid = $arrData['openid'];
		$wx_app_id = $arrData['wx_app_id'];
		$wx_mch_id = $arrData['wx_mch_id'];
		$wx_api_key = $arrData['wx_api_key'];
		$dealer_num = $arrData['dealer_num'];

		$DelaerConst = "Dealer_".$dealer_num;

		$notify_url = $DelaerConst::WX_Notify_Url;
		$notify_url = str_replace("{dealer_num}",$dealer_num,$notify_url);

		
		$parameters["appid"] = $wx_app_id;										//公众账号ID
		$parameters["attach"] = $dealer_num;
		$parameters["body"] = $body;											//商品描述
		$parameters["device_info"] = $DelaerConst::WX_Device_Info;       		//设备号
		$parameters["mch_id"] = $wx_mch_id;										//商户号
		$parameters["nonce_str"] = $this->createNonceStr();						//随机字符串
		$parameters["notify_url"] = base_url().$notify_url;    					//通知地址
		$parameters["openid"] = $openid;       									//用户标识
		$parameters["out_trade_no"] = $out_trade_no;       						//商户订单号
		$parameters["product_id"] = $out_trade_no;       						//商品ID
		$parameters["spbill_create_ip"] = $_SERVER['REMOTE_ADDR'];				//终端ip  
		$parameters["total_fee"] = $total_price * 100;       					//总金额
		$parameters["trade_type"] = "JSAPI";       								//通知地址
		
		$parameters["sign"] = $this->getSign($parameters,$wx_api_key);						//签名
		
		$xml_obj = $this->arrtoxml($parameters);

		return $xml_obj;
	}
	
	
	
	/**************************************************
						opt
	***************************************************/

	/*
		创建订单
	*/
	public function getPaymentOpt($data)
	{
		$timestamp = time();
		$result = array();
		
		if(!isset($data['account_id']))
		{
			log_message('error', "function(getPaymentOpt): lack of account_id"." in file".__FILE__." on Line ".__LINE__);
			return array("result"=>OPT_CONST::FAILED,"data"=>$result,"result_message"=>"缺少参数");
		}
		if(!isset($data['open_id']))
		{
			log_message('error', "function(getPaymentOpt): lack of open_id"." in file".__FILE__." on Line ".__LINE__);
			return array("result"=>OPT_CONST::FAILED,"data"=>$result,"result_message"=>"缺少参数");
		}
		if(!isset($data['goods_id']) || trim($data['goods_id']) == G_CONST::EMPTY_STRING)
		{
			log_message('error', "function(getPaymentOpt): lack of opt_type:"." in file".__FILE__." on Line ".__LINE__);
			return array("result"=>OPT_CONST::FAILED,"data"=>$result,"result_message"=>"缺少参数");
		}
		if(!isset($data['dealer_num']) || trim($data['dealer_num']) == G_CONST::EMPTY_STRING)
		{
			log_message('error', "function(getPaymentOpt): lack of dealer_num:"." in file".__FILE__." on Line ".__LINE__);
			return array("result"=>OPT_CONST::FAILED,"data"=>$result,"result_message"=>"缺少参数");
		}
		
		$dealer_num = $data['dealer_num'];
		$account_id = $data['account_id'];
		$open_id = $data['open_id'];
		$goods_id = $data['goods_id'];
		$count = 1;
		$discount = 0;

		$DelaerConst = "Dealer_".$dealer_num;
        $dealerDB = $DelaerConst::DBConst_Name;	
		
		//获取商品明细
		$goods_where = 'goods_id="'.$goods_id.'" and is_delete='.G_CONST::IS_FALSE;
		$goods_sql = 'select title,price,ticket_count from '.Payment_Goods.' where '.$goods_where;
		$goods_query = $this->getDataBySql($dealerDB,1,$goods_sql);
		if($goods_query == DB_CONST::DATA_NONEXISTENT)
		{
			//return -6001;	//已下架
			log_message('error', "function(getPaymentOpt): 商品已下架($goods_id)"." in file".__FILE__." on Line ".__LINE__);
			return array("result"=>OPT_CONST::FAILED,"data"=>$result,"result_message"=>"商品已下架");
		}
		$ticket_count = $goods_query['ticket_count'];

		//库存数量
		$inventory_count = $this->getTicketInventory($dealerDB);
		if($ticket_count > $inventory_count)
		{
			log_message('error', "function(getPaymentOpt): 房卡库存不足($inventory_count)"." in file".__FILE__." on Line ".__LINE__);
			return array("result"=>OPT_CONST::FAILED,"data"=>$result,"result_message"=>"房卡库存不足");
		}

		$order_no = $this->_buildOrderNO("G");
		
		$orderGoods_array['order_id'] = -1;
		$orderGoods_array['order_no'] = $order_no;
		$orderGoods_array['title'] = $goods_query['title'];
		$orderGoods_array['price'] = $goods_query['price'];
		$orderGoods_array['ticket_count'] = $goods_query['ticket_count'];
		$orderGoods_array['count'] = $count;
		$orderGoods_id = $this->getInsertID($dealerDB,Payment_OrderGoods, $orderGoods_array);
		if($orderGoods_id == DB_CONST::INSERT_FAILED)	//添加失败
		{
			log_message('error', "function(getPaymentOpt):save order goods error"." in file".__FILE__." on Line ".__LINE__);
			return array('result'=>OPT_CONST::FAILED,'data'=>$result,'result_message'=>"生成订单失败");
		}
		
		$original_price = $goods_query['price'] * $count;

		$total_price = $original_price - $discount;
		
		$order_array['create_appid'] = $open_id;
		$order_array['create_time'] = $timestamp;
		$order_array['update_appid'] = $open_id;
		$order_array['update_time'] = $timestamp;
		$order_array['is_delete'] = G_CONST::IS_FALSE;
		$order_array['account_id'] = $account_id;
		$order_array['order_no'] = $order_no;
		$order_array['original_price'] = $original_price;
		$order_array['total_price'] = $total_price;
		$order_array['status'] = Payment_CONST::OrderStatus_WaitForPay;
		$order_array['is_pay'] = G_CONST::IS_FALSE;
		$order_array['timeout'] = -1;
		$order_array['is_audit'] = G_CONST::IS_FALSE;
		$order_array['total_discount'] = 0;
		$order_array['remark'] = G_CONST::EMPTY_STRING;
		$order_array['pay_time'] = -1;
		$order_array['payment_type'] = 2;	//支付类型，1支付宝，2微信
		$order_array['discount'] = $discount;		//折扣价
		
		$order_id = $this->getInsertID($dealerDB,Payment_Order, $order_array);
		if($order_id == DB_CONST::INSERT_FAILED)	//添加失败
		{
			log_message('error', "function(createWxpayOrder):save order error"." in file".__FILE__." on Line ".__LINE__);
			return array('result'=>OPT_CONST::FAILED,'data'=>$result,'result_message'=>"生成订单失败");
		}
		
		$update_array['order_id'] = $order_id;
		$update_query = $this->updateFunc($dealerDB,"goods_id",$orderGoods_id,Payment_OrderGoods,$update_array);
		
		
		
		$wx_app_id = $DelaerConst::WX_Appid;
		$wx_mch_id = $DelaerConst::WX_Mch_ID;
		$wx_api_key = $DelaerConst::WX_API_Key;
		
		//生成支付订单
		$wxpay_payment['order_no'] = $order_no;
		$wxpay_payment['total_price'] = $total_price;
		$wxpay_payment['subject'] = $goods_query['title'];
		$wxpay_payment['body'] = $goods_query['title'];
		$wxpay_payment['openid'] = $open_id;
		$wxpay_payment['wx_app_id'] = $wx_app_id;
		$wxpay_payment['wx_mch_id'] = $wx_mch_id;
		$wxpay_payment['wx_api_key'] = $wx_api_key;

		$wxpay_payment['dealer_num'] = $dealer_num;
		
		$payment_obj = $this->createWxpayPayment($wxpay_payment);
		
		log_message('error', "function(getPaymentOpt)payment_obj: ".$payment_obj." in file".__FILE__." on Line ".__LINE__);
		
		$api_url = "https://api.mch.weixin.qq.com/pay/unifiedorder";
		$post_data = $payment_obj;
		
		$ch = curl_init();
		curl_setopt($ch, CURLOPT_URL, $api_url);
		curl_setopt ( $ch, CURLOPT_RETURNTRANSFER, 1 );
		curl_setopt($ch, CURLOPT_POST, 1);
		curl_setopt($ch, CURLOPT_POSTFIELDS, $post_data);
		$output = curl_exec($ch);
		curl_close($ch);
		
		log_message('error', "function(getSign): ".$output." in file".__FILE__." on Line ".__LINE__);
		
        $payment_result = $this->xmlToArray($output);

		if(isset($payment_result['return_code']) && $payment_result['return_code'] == "SUCCESS")
		{
			//$result['paySign'] = $payment_result['sign'];
			
			$jspay_result['appId'] = $payment_result['appid'];
			$jspay_result['nonceStr'] = $this->createNonceStr();
			$jspay_result['package'] = "prepay_id=".$payment_result['prepay_id'];
			$jspay_result['signType'] = "MD5";
			$jspay_result['timeStamp'] = '"'.$timestamp.'"';
			
			$jspay_result['paySign'] = $this->getSign($jspay_result,$wx_api_key);
			$jspay_result['prepay_id'] = $payment_result['prepay_id'];
			$jspay_result['order_id'] = $order_id;
			//log_message('error', "function(getSign): ".$jspay_result['paySign']." in file".__FILE__." on Line ".__LINE__);
				
			return array('result'=>OPT_CONST::SUCCESS,'data'=>$jspay_result,'result_message'=>"生成订单");
		}
		else
		{
			log_message('error', "function(createWxpayOrder):request wechat order error"." in file".__FILE__." on Line ".__LINE__);
			return array('result'=>OPT_CONST::FAILED,'data'=>$result,'result_message'=>"生成订单失败");
		}
	}
	 
	 
	 
	/*
	*	支付宝回调操作
	*
	*
	*
	*/
	public function wxpayCallBack($arrData,$dealer_num)
	{
		$json = json_encode($arrData);
		log_message('error', "function(wxpayCallBack):$json"." in file".__FILE__." on Line ".__LINE__);
		
		
		if(!isset($arrData['return_code']) || trim($arrData['return_code']) === "")
		{
			log_message('error', "function(wxpayCallBack):lack of return_code"." in file".__FILE__." on Line ".__LINE__);
			$return_array = array("return_code"=>"FAIL","return_msg"=>"参数格式校验错误");
			return $this->arrtoxml($return_array);
		}
		
		$return_code = $arrData['return_code'];
		$return_msg = "";
		if(isset($arrData['return_msg']))
		{
			$return_msg = $arrData['return_msg'];
		}
		if($return_code == "FAIL")
		{
			log_message('error', "function(wxpayCallBack):return_code($return_code) return_msg($return_msg)"." in file".__FILE__." on Line ".__LINE__);
			$return_array = array("return_code"=>"SUCCESS","return_msg"=>"OK");
			return $this->arrtoxml($return_array);
		}
		
		$DelaerConst = "Dealer_".$dealer_num;
        $dealerDB = $DelaerConst::DBConst_Name;	
		
		$timestamp = time();
		$resultType = "";
		
		//判断参数是否齐全
		if(!isset($arrData['appid']) || trim($arrData['appid']) === "")
		{
			log_message('error', "function(wxpayCallBack):lack of appid"." in file".__FILE__." on Line ".__LINE__);
			$return_array = array("return_code"=>"FAIL","return_msg"=>"参数格式校验错误");
			return $this->arrtoxml($return_array);
		}
		if(!isset($arrData['mch_id']) || trim($arrData['mch_id']) === "")
		{
			log_message('error', "function(wxpayCallBack):lack of mch_id"." in file".__FILE__." on Line ".__LINE__);
			$return_array = array("return_code"=>"FAIL","return_msg"=>"参数格式校验错误");
			return $this->arrtoxml($return_array);
		}
		if(!isset($arrData['nonce_str']) || trim($arrData['nonce_str']) === "")
		{
			log_message('error', "function(wxpayCallBack):lack of nonce_str"." in file".__FILE__." on Line ".__LINE__);
			$return_array = array("return_code"=>"FAIL","return_msg"=>"参数格式校验错误");
			return $this->arrtoxml($return_array);
		}
		if(!isset($arrData['sign']) || trim($arrData['sign']) === "")
		{
			log_message('error', "function(wxpayCallBack):lack of sign"." in file".__FILE__." on Line ".__LINE__);
			$return_array = array("return_code"=>"FAIL","return_msg"=>"参数格式校验错误");
			return $this->arrtoxml($return_array);
		}
		if(!isset($arrData['result_code']) || trim($arrData['result_code']) === "")
		{
			log_message('error', "function(wxpayCallBack):lack of appid"." in file".__FILE__." on Line ".__LINE__);
			$return_array = array("return_code"=>"FAIL","return_msg"=>"参数格式校验错误");
			return $this->arrtoxml($return_array);
		}
		if(!isset($arrData['openid']) || trim($arrData['openid']) === "")
		{
			log_message('error', "function(wxpayCallBack):lack of openid"." in file".__FILE__." on Line ".__LINE__);
			$return_array = array("return_code"=>"FAIL","return_msg"=>"参数格式校验错误");
			return $this->arrtoxml($return_array);
		}
		if(!isset($arrData['trade_type']) || trim($arrData['trade_type']) === "")
		{
			log_message('error', "function(wxpayCallBack):lack of trade_type"." in file".__FILE__." on Line ".__LINE__);
			$return_array = array("return_code"=>"FAIL","return_msg"=>"参数格式校验错误");
			return $this->arrtoxml($return_array);
		}
		if(!isset($arrData['bank_type']) || trim($arrData['bank_type']) === "")
		{
			log_message('error', "function(wxpayCallBack):lack of bank_type"." in file".__FILE__." on Line ".__LINE__);
			$return_array = array("return_code"=>"FAIL","return_msg"=>"参数格式校验错误");
			return $this->arrtoxml($return_array);
		}
		if(!isset($arrData['total_fee']) || trim($arrData['total_fee']) === "")
		{
			log_message('error', "function(wxpayCallBack):lack of total_fee"." in file".__FILE__." on Line ".__LINE__);
			$return_array = array("return_code"=>"FAIL","return_msg"=>"参数格式校验错误");
			return $this->arrtoxml($return_array);
		}
		if(!isset($arrData['cash_fee']) || trim($arrData['cash_fee']) === "")
		{
			log_message('error', "function(wxpayCallBack):lack of cash_fee"." in file".__FILE__." on Line ".__LINE__);
			$return_array = array("return_code"=>"FAIL","return_msg"=>"参数格式校验错误");
			return $this->arrtoxml($return_array);
		}
		if(!isset($arrData['transaction_id']) || trim($arrData['transaction_id']) === "")
		{
			log_message('error', "function(wxpayCallBack):lack of transaction_id"." in file".__FILE__." on Line ".__LINE__);
			$return_array = array("return_code"=>"FAIL","return_msg"=>"参数格式校验错误");
			return $this->arrtoxml($return_array);
		}
		if(!isset($arrData['out_trade_no']) || trim($arrData['out_trade_no']) === "")
		{
			log_message('error', "function(wxpayCallBack):lack of out_trade_no"." in file".__FILE__." on Line ".__LINE__);
			$return_array = array("return_code"=>"FAIL","return_msg"=>"参数格式校验错误");
			return $this->arrtoxml($return_array);
		}
		if(!isset($arrData['time_end']) || trim($arrData['time_end']) === "")
		{
			log_message('error', "function(wxpayCallBack):lack of time_end"." in file".__FILE__." on Line ".__LINE__);
			$return_array = array("return_code"=>"FAIL","return_msg"=>"参数格式校验错误");
			return $this->arrtoxml($return_array);
		}
		
		if(!isset($arrData['device_info']))
		{
			$arrData['device_info'] = "";
		}
		if(!isset($arrData['err_code']))
		{
			$arrData['err_code'] = "";
		}
		if(!isset($arrData['err_code_des']))
		{
			$arrData['err_code_des'] = "";
		}
		if(!isset($arrData['is_subscribe']))
		{
			$arrData['is_subscribe'] = "";
		}
		if(!isset($arrData['settlement_total_fee']))
		{
			$arrData['settlement_total_fee'] = "";
		}
		if(!isset($arrData['fee_type']))
		{
			$arrData['fee_type'] = "";
		}
		if(!isset($arrData['cash_fee_type']))
		{
			$arrData['cash_fee_type'] = "";
		}
		if(!isset($arrData['attach']))
		{
			$arrData['attach'] = "";
		}
		
		$appid = $arrData['appid'];	//公众账号ID
		$mch_id = $arrData['mch_id'];	//商户号
		$device_info = $arrData['device_info'];	//设备号
		$nonce_str = $arrData['nonce_str'];	//随机字符串
		$sign = $arrData['sign'];	//随机字符串
		$result_code = $arrData['result_code'];	//业务结果
		$err_code = $arrData['err_code'];	//错误代码
		$err_code_des = $arrData['err_code_des'];	//错误代码描述
		$openid = $arrData['openid'];	//用户标识
		$is_subscribe = $arrData['is_subscribe'];	//是否关注公众账号	Y-关注，N-未关注
		$trade_type = $arrData['trade_type'];	//交易类型 JSAPI、NATIVE、APP
		$bank_type = $arrData['bank_type'];	//付款银行
		$total_fee = $arrData['total_fee'];	//订单总金额，单位为分
		$settlement_total_fee = $arrData['settlement_total_fee'];	//应结订单金额
		$fee_type = $arrData['fee_type'];	//货币种类
		$cash_fee = $arrData['cash_fee'];	//现金支付金额
		$cash_fee_type = $arrData['cash_fee_type'];	//现金支付货币类型
		$transaction_id = $arrData['transaction_id'];	//微信支付订单号
		$out_trade_no = $arrData['out_trade_no'];	//商户订单号
		$attach = $arrData['attach'];	//商家数据包
		$time_end = $arrData['time_end'];	//支付完成时间
		
		
		//判断订单号是否已支付
		$order_where = 'order_no="'.$out_trade_no.'" and status='.Payment_CONST::OrderStatus_Paid.' and is_pay='.G_CONST::IS_TRUE.' limit 1';
		$order_sql = 'select account_id,order_id,total_price from '.Payment_Order.' where '.$order_where;
		$order_query = $this->getDataBySql($dealerDB,1,$order_sql);
		if($order_query != DB_CONST::DATA_NONEXISTENT)
		{
			log_message('error', "function(wxpayCallBack):order_no($out_trade_no) is paid"." in file".__FILE__." on Line ".__LINE__);
			$return_array = array("return_code"=>"SUCCESS","return_msg"=>"订单已处理");
			return $this->arrtoxml($return_array);
		}
		
		//支付信息入库
		$payment_ary['time'] = $timestamp;
		$payment_ary['time_end'] = $time_end;
		$payment_ary['transaction_id'] = $transaction_id;
		$payment_ary['out_trade_no'] = $out_trade_no;
		$payment_ary['result_code'] = $result_code;
		$payment_ary['err_code'] = $err_code;
		$payment_ary['err_code_des'] = $err_code_des;
		$payment_ary['openid'] = $openid;
		$payment_ary['trade_type'] = $trade_type;
		$payment_ary['bank_type'] = $bank_type;
		$payment_ary['total_fee'] = $total_fee;
		$payment_ary['cash_fee'] = $cash_fee;
		$payment_ary['attach'] = $attach;
		$payment_ary['data'] = $json;
		$insert_query = $this->getInsertID($dealerDB,PaymentDetail_Wxpay, $payment_ary);
		
		
		$wx_app_id = $DelaerConst::WX_Appid;
		$wx_mch_id = $DelaerConst::WX_Mch_ID;
		$wx_api_key = $DelaerConst::WX_API_Key;
		
		
		//校验$appid是正确
		if($appid != $wx_app_id)
		{
			log_message('error', "function(wxpayCallBack):appid($appid) error"." in file".__FILE__." on Line ".__LINE__);
			$return_array = array("return_code"=>"FAIL","return_msg"=>"appid错误");
			return $this->arrtoxml($return_array);
		}
		//校验$mch_id是正确
		if($mch_id != $wx_mch_id)
		{
			log_message('error', "function(wxpayCallBack): mch_id($mch_id) error"." in file".__FILE__." on Line ".__LINE__);
			$return_array = array("return_code"=>"FAIL","return_msg"=>"mch_id错误");
			return $this->arrtoxml($return_array);
		}
		
		if($result_code  == "FAIL")
		{
			log_message('error', "function(wxpayCallBack):result_code($result_code) err_code($err_code) err_code_des($err_code_des)"." in file".__FILE__." on Line ".__LINE__);
			$return_array = array("return_code"=>"SUCCESS","return_msg"=>"result_code is fail");
			return $this->arrtoxml($return_array);
		}
		
		//验证是否是支付宝发来的通知
		$is_wx = $this->checkSign($arrData);
		if($is_wx && $result_code  == "SUCCESS")
		{
			$info_array['out_trade_no'] = $out_trade_no;
			$info_array['total_fee'] = $total_fee/100;
			$info_array['dealer_num'] = $dealer_num;

			$update_result = $this->updatePaymentInfo($info_array,$dealerDB);
			
			if($update_result)
			{
				log_message('error', "function(wxpayCallBack):updatePaymentInfo success"." in file".__FILE__." on Line ".__LINE__);
				$return_array = array("return_code"=>"SUCCESS","return_msg"=>"订单处理成功");
				return $this->arrtoxml($return_array);
			}
			else
			{
				log_message('error', "function(wxpayCallBack):updatePaymentInfo false"." in file".__FILE__." on Line ".__LINE__);
				$return_array = array("return_code"=>"FAIL","return_msg"=>"修改订单信息失败");
				return $this->arrtoxml($return_array);
			}
		}
		else
		{
			log_message('error', "function(wxpayCallBack):not alipay post"." in file".__FILE__." on Line ".__LINE__);
			$return_array = array("return_code"=>"FAIL","return_msg"=>"签名校验失败");
			return $this->arrtoxml($return_array);
		}
	}
	 
	 
	 
	 
	
	/*
		校验签名
	*/
	private function checkSign($arrData)
	{
		return true;
	}
	 
 }
 
 
 
 
 
 ?>