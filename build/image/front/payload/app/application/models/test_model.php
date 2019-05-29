<?php

require_once('modules/mysql.class.php');
include_once('public_models.php');		//加载数据库操作类
class Test_Model extends Public_Models
{
	public function __construct()
	{
		header('Content-Type: text/html; charset=utf-8'); 
		parent::__construct();
	}
	
	/************************************************
					common function
	*************************************************/
		
	/*
		更新用户信息
	*/
	public function updateWechatInfo($arrData)
	{
		$timestamp = time();
		$result = array();
		
		if(!isset($arrData['open_id']) || $arrData['open_id'] == G_CONST::EMPTY_STRING)
		{
			log_message('error', "function(updateWechatInfo):lack of open_id"." in file".__FILE__." on Line ".__LINE__);
			return $this->missingPrameterArr("open_id");
		}
		if(!isset($arrData['nickname']))
		{
			log_message('error', "function(updateWechatInfo):lack of nickname"." in file".__FILE__." on Line ".__LINE__);
			return $this->missingPrameterArr("nickname");
		}
		if(!isset($arrData['headimgurl']))
		{
			log_message('error', "function(updateWechatInfo):lack of headimgurl"." in file".__FILE__." on Line ".__LINE__);
			return $this->missingPrameterArr("headimgurl");
		}
		if(!isset($arrData['is_refresh']))
		{
			log_message('error', "function(updateWechatInfo):lack of is_refresh"." in file".__FILE__." on Line ".__LINE__);
			return $this->missingPrameterArr("is_refresh");
		}
		
		$open_id = $arrData['open_id'];
		$nickname = $arrData['nickname'];
		$headimgurl = $arrData['headimgurl'];
		if($headimgurl == G_CONST::EMPTY_STRING)
		{
			$headimgurl = "oss.zht66.com/default_avatar.png";
		}
		$is_refresh = $arrData['is_refresh'];
		
		if($is_refresh == 1)
		{
			$update_array['update_time'] = $timestamp;
			$update_array['update_appid'] = $open_id;
			$update_array['is_refresh'] = 1;
			$update_query = $this->updateFunc("open_id",$open_id,WX_Account,$update_array);

			return array('result'=>OPT_CONST::SUCCESS,'data'=>$result,'result_message'=>"修改成功"); 
		}

		
		//判断open_id是否存在
		$account_where = 'open_id="'.$open_id.'"';
		$account_sql = 'select account_id from '.WX_Account.' where '.$account_where.'';
		$account_query = $this->getDataBySql(1,$account_sql);
		if(DB_CONST::DATA_NONEXISTENT != $account_query)
		{
			$account_id = $account_query['account_id'];
			
			$update_array['update_time'] = $timestamp;
			$update_array['update_appid'] = $open_id;
			$update_array['nickname'] = $nickname;
			$update_array['headimgurl'] = $headimgurl;
			$update_array['is_refresh'] = 0;
			$update_query = $this->updateFunc("account_id",$account_id,WX_Account,$update_array);
		}
		else
		{
			$insert_array['create_time'] = $timestamp;
			$insert_array['create_appid'] = $open_id;
			$insert_array['update_time'] = $timestamp;
			$insert_array['update_appid'] = $open_id;
			$insert_array['is_delete'] = G_CONST::IS_FALSE;
			$insert_array['open_id'] = $open_id;
			$insert_array['nickname'] = $nickname;
			$insert_array['headimgurl'] = $headimgurl;
			$insert_array['is_refresh'] = 0;
			$account_id = $this->getInsertID(WX_Account, $insert_array);
			
			//默认添加房卡
			$ticket_array['create_time'] = $timestamp;
			$ticket_array['create_appid'] = $open_id;
			$ticket_array['update_time'] = $timestamp;
			$ticket_array['update_appid'] = $open_id;
			$ticket_array['is_delete'] = G_CONST::IS_FALSE;
			$ticket_array['account_id'] = $account_id;
			$ticket_array['ticket_count'] = 0;
			
			$ticket_id = $this->getInsertID(Room_Ticket, $ticket_array);
		}
		
		return array('result'=>OPT_CONST::SUCCESS,'data'=>$result,'result_message'=>"修改成功");
	}
	
	
	
	/*
		获取用户信息
	*/
	public function getUserInfo($arrData)
	{
		$timestamp = time();
		$result = array();
		
		if(!isset($arrData['open_id']) || $arrData['open_id'] == G_CONST::EMPTY_STRING)
		{
			log_message('error', "function(getUserInfo):lack of open_id"." in file".__FILE__." on Line ".__LINE__);
			return $this->missingPrameterArr("open_id");
		}
		
		$open_id = $arrData['open_id'];
		
		//判断open_id是否存在
		$account_where = 'open_id="'.$open_id.'"';
		$account_sql = 'select account_id,nickname,headimgurl from '.WX_Account.' where '.$account_where.'';
		$account_query = $this->getDataBySql(1,$account_sql);
		if(DB_CONST::DATA_NONEXISTENT == $account_query)
		{
			
			$userData_url = "http://wap.uzhan123.com/_wxauth/getWechatUserData/".$open_id;
			
			$userData_result = $this->httpGet($userData_url);
			
			if($userData_result == false)
			{
				return array('result'=>OPT_CONST::FAILED,'data'=>$result,'result_message'=>"账号不存在");
			}
			
			log_message('error', "function(getUserInfo):userData_result:".$userData_result." in file".__FILE__." on Line ".__LINE__);
			
			$userData_array = $this->splitJsonString($userData_result);
			
			$nickname = $userData_array['nickname'];
			$headimgurl = $userData_array['headimgurl'];
			
			$insert_array['create_time'] = $timestamp;
			$insert_array['create_appid'] = $open_id;
			$insert_array['update_time'] = $timestamp;
			$insert_array['update_appid'] = $open_id;
			$insert_array['is_delete'] = G_CONST::IS_FALSE;
			$insert_array['open_id'] = $open_id;
			$insert_array['nickname'] = $nickname;
			$insert_array['headimgurl'] = $headimgurl;
			
			$account_id = $this->getInsertID(WX_Account, $insert_array);
			
			//默认添加房卡
			$ticket_array['create_time'] = $timestamp;
			$ticket_array['create_appid'] = $open_id;
			$ticket_array['update_time'] = $timestamp;
			$ticket_array['update_appid'] = $open_id;
			$ticket_array['is_delete'] = G_CONST::IS_FALSE;
			$ticket_array['account_id'] = $account_id;
			$ticket_array['ticket_count'] = 10;
			
			$ticket_id = $this->getInsertID(Room_Ticket, $ticket_array);
		}
		else
		{
			$account_id = $account_query['account_id'];
			$nickname = $account_query['nickname'];
			$headimgurl = $account_query['headimgurl'];
		}
		
		$result['account_id'] = $account_id;
		$result['nickname'] = $nickname;
		$result['headimgurl'] = $headimgurl;
		$result['open_id'] = $open_id;
		
		return array('result'=>OPT_CONST::SUCCESS,'data'=>$result,'result_message'=>"获取用户信息");
	}
	
	
	public function httpGet($url) {
		$curl = curl_init();
		curl_setopt($curl, CURLOPT_RETURNTRANSFER, true);
		curl_setopt($curl, CURLOPT_TIMEOUT, 500);
		// 为保证第三方服务器与微信服务器之间数据传输的安全性，所有微信接口采用https方式调用，必须使用下面2行代码打开ssl安全校验。
		// 如果在部署过程中代码在此处验证失败，请到 http://curl.haxx.se/ca/cacert.pem 下载新的证书判别文件。
		curl_setopt($curl, CURLOPT_SSL_VERIFYPEER, true);
		curl_setopt($curl, CURLOPT_SSL_VERIFYHOST, 0);
		curl_setopt($curl, CURLOPT_URL, $url);
		
		$res = curl_exec($curl);
		curl_close($curl);
		
		return $res;
	}
	
	
	
	public function updateUserTicketCount()
	{
		return true;
		$ticket_where = 'is_delete=0';
		$ticket_sql = 'select account_id,ticket_count from '.Room_Ticket.' where '.$ticket_where.'';
		$ticket_query = $this->getDataBySql(0,$ticket_sql);
		foreach($ticket_query as $ticket_item)
		{
			$account_id = $ticket_item['account_id'];
			$ticket_count = $ticket_item['ticket_count'];

			//房卡流水账
			$journal_ary['journal_type'] = Game_CONST::JournalType_Income;
			$journal_ary['account_id'] = $account_id;
			$journal_ary['object_type'] = Game_CONST::ObjectType_Newuser;
			$journal_ary['object_id'] = -1;
			$journal_ary['ticket_count'] = $ticket_count;
			$journal_ary['extra'] = "";
			$this->updateRoomTicketJournal($journal_ary);
		}

		return true;
	}



	public function updateUserTicketJournal()
	{
		$timestamp = time();

		$ticket_where = 'is_delete=0';
		$ticket_sql = 'select account_id,ticket_count from '.Room_Ticket.' where '.$ticket_where.'';
		$ticket_query = $this->getDataBySql(0,$ticket_sql);
		foreach($ticket_query as $ticket_item)
		{
			$account_id = $ticket_item['account_id'];
			$ticket_count = $ticket_item['ticket_count'];

			//获取流水账号
			$balance = 0;
			$balance_where = 'account_id='.$account_id.' and is_delete=0';
			$balance_sql = 'select balance from '.Room_TicketJournal.' where '.$balance_where.' order by journal_id desc';
			$balance_query = $this->getDataBySql(1,$balance_sql);
			if(DB_CONST::DATA_NONEXISTENT != $balance_query)
			{
				$balance = $balance_query['balance'];
			}

			if($balance != $ticket_count)
			{
				$difference = $ticket_count - $balance;
				log_message('error', "function(getUserInfo):difference:".$difference." in file".__FILE__." on Line ".__LINE__);

				if($difference > 0)
				{
					//补加
					$extra = "补加差额";
					$abstract = "调整房卡差额";

					$journal_array['income'] = $difference;
					$journal_array['balance'] = $balance + $difference;

				}
				else
				{
					//补扣
					$extra = "补扣差额";
					$abstract = "调整房卡差额";

					$difference = -$difference;

					$journal_array['disburse'] = $difference;
					$journal_array['balance'] = $balance - $difference;
				}

				//添加到流水账
				$journal_array['create_time'] = $timestamp;
				$journal_array['create_appid'] = "aid_".$account_id;
				$journal_array['update_time'] = $timestamp;
				$journal_array['update_appid'] = "aid_".$account_id;
				$journal_array['is_delete'] = G_CONST::IS_FALSE;
				$journal_array['account_id'] = $account_id;
				$journal_array['object_id'] = -1;
				$journal_array['object_type'] = 99;
				
				$journal_array['extra'] = $extra;
				$journal_array['abstract'] = $abstract;		//摘要

				$journal_id = $this->getInsertID(Room_TicketJournal, $journal_array);

				unset($journal_array);
			}
		}

		return true;
	}
	
	
	
	public function getTicketGoodsList()
	{
		$timestamp = time();
		$result = array();
		
		//获取商品明细
		$goods_where = 'is_delete='.G_CONST::IS_FALSE;
		$goods_sql = 'select goods_id,title,price,ticket_count from '.Payment_Goods.' where '.$goods_where.' order by ticket_count asc';
		$goods_query = $this->getDataBySql(0,$goods_sql);
		if($goods_query != DB_CONST::DATA_NONEXISTENT)
		{
			$result = $goods_query;
			
		}
		
		return array('result'=>OPT_CONST::SUCCESS,'data'=>$result,'result_message'=>"获取商品列表");
	}
	



	public function statisticsAccount($account_id)
	{
		
		$hostname = "rds7u8lb6ypu7ef35e8lq.mysql.rds.aliyuncs.com";
        $username = "doing_admin_pe";
        $password = "Ldy@2016";
        $database = "game";

		$MMYSQL = new MySQL($hostname,$username,$password,$database,3306,"utf8mb4");

		$win_score = 0;
		$lose_score = 0;
		$count = 0;

		$date_ary = array();


		echo "用户$account_id:";
		echo "<br><br>";

		$account_where = 'is_delete=0';
		$account_sql = 'select board,create_time,room_id from room_scoreboard where '.$account_where.'';
		$account_query = $MMYSQL->query($account_sql);

		if(is_array($account_query) && count($account_query) > 0 )
		{
			foreach($account_query as $item)
			{
				$create_time = $item['create_time'];
				$room_id = $item['room_id'];

				$board_ary = json_decode($item['board']);

				foreach($board_ary as $a_id => $value)
				{
					if($a_id == $account_id)
					{
						echo "".date("m-d H:i:s",$create_time).' 房间'.$room_id.' 胜负：'.$value;
						echo "<br>";

						$date = date("Y-m-d",$create_time);
						if(!isset($date_ary[$date]))
						{
							$date_ary[$date] = 0;
						}
						$date_ary[$date] += $value;

						$count ++;
						if($value > 0)
						{
							$win_score += $value;
						}
						else
						{
							$lose_score += $value;
						}
						continue;
					}
				}
			}
		}

		echo "<br><br>";
		foreach($date_ary as $d=>$v)
		{
			echo $d." 胜负：".$v;
			echo "<br>";
		}


		echo "<br><br>";
		echo "参与局数:".$count;
		echo "<br>";
		echo "赢了:".$win_score;
		echo "<br>";
		echo "输了:".abs($lose_score);
		echo "<br>";
		echo "总计:".($win_score - abs($lose_score));

		return ;
	}

	







}