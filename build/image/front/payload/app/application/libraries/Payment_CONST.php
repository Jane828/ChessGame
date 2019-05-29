<?php 

class Payment_CONST
{
	
	
	
	//订单状态
	const OrderStatus_Canceled				= -2;	//已取消
	const OrderStatus_Canceling				= -1;	//申请取消
	const OrderStatus_WaitForPay			= 1;	//等待支付
	const OrderStatus_Paid					= 2;	//已经支付
	

	//流水账类型
	const JournalType_Commission			= 1;	//提成
}


/* End of file constants.php */
/* Location: ./application/config/constants.php */