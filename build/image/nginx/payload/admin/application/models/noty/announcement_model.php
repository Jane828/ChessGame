<?php

include_once __DIR__.'/common_model.php';		//加载数据库操作类
class Announcement_Model extends Noty_Common_Model
{
	public function __construct()
	{
		header('Content-Type: text/html; charset=utf-8'); 
		parent::__construct();
	}


	//获取公告列表
	public function getAnnList($arrData)
	{
		$result = array();
		$timestamp = time();

		if(!isset($arrData['page']) || $arrData['page'] === "" )
		{
			log_message('error', "function(sendMessage):lack of page"." in file".__FILE__." on Line ".__LINE__);
			return $this->missingPrameterArr("page");
		}

		$page = $arrData['page'];

		$limit = 10;
		$offset = ($page - 1) * $limit;
		$sum_page = 1;

		$ann_sql = 'select data_id,start_time,end_time,second,content,type,is_delete from '.Announcement_Detail.' where 1 order by create_time desc limit '.$offset.','.$limit;
		$ann_sql = $this->getDataBySql("admin",0,$ann_sql);
		if($ann_sql != DB_CONST::DATA_NONEXISTENT)
		{
			$count_sql = 'select count(1) as count from '.Announcement_Detail.' where 1';
			$count_query = $this->getDataBySql("admin",1,$count_sql);
			$sum_page = ceil($count_query['count'] / $limit);

			foreach($ann_sql as $item)
			{
				$array['data_id'] = $item['data_id'];
				$array['start_time'] = date("Y-m-d H:i",$item['start_time']);
				$array['end_time'] = date("Y-m-d H:i",$item['end_time']);
				$array['second'] = $item['second'];
				$array['content'] = base64_encode($item['content']);
				$array['status'] = 1;	//1正常，2关闭，3已完成
				if($item['is_delete'] == 1)
				{
					$array['status'] = 2;
				}
				else if($timestamp >= $item['end_time'])
				{
					$array['status'] = 3;
				}

				$result[] = $array;
			}
		}
		return array('result'=>OPT_CONST::SUCCESS,'data'=>$result,'sum_page'=>$sum_page,'result_message'=>"获取公告列表");
	}


	public function sendAnnOpt($arrData)
	{
		$result = array();
		$timestamp = time();

		if(!isset($arrData['start_time']) || $arrData['start_time'] === "" )
		{
			log_message('error', "function(sendMessage):lack of start_time"." in file".__FILE__." on Line ".__LINE__);
			return $this->missingPrameterArr("start_time");
		}
		if(!isset($arrData['end_time']) || $arrData['end_time'] === "" )
		{
			log_message('error', "function(sendMessage):lack of end_time"." in file".__FILE__." on Line ".__LINE__);
			return $this->missingPrameterArr("end_time");
		}
		if(!isset($arrData['second']) || $arrData['second'] === "" )
		{
			log_message('error', "function(sendMessage):lack of second"." in file".__FILE__." on Line ".__LINE__);
			return $this->missingPrameterArr("second");
		}
		if(!isset($arrData['content']) || $arrData['content'] === "" )
		{
			log_message('error', "function(sendMessage):lack of content"." in file".__FILE__." on Line ".__LINE__);
			return $this->missingPrameterArr("content");
		}

		$start_time = $arrData['start_time'];
		$end_time = $arrData['end_time'];
		$second = $arrData['second'];
		$content = base64_decode($arrData['content']);
		
		if($second <= 0)
		{
			return array('result'=>OPT_CONST::FAILED,'data'=>$result,'result_message'=>"播放时间不能小于0");
		}

		$array['create_time'] = $timestamp;
		$array['create_appid'] = "dealernum";
		$array['update_time'] = $timestamp;
		$array['update_appid'] = "dealernum";
		$array['is_delete'] = 0;
		$array['start_time'] = $start_time;
		$array['end_time'] = $end_time;
		$array['second'] = $second;
		$array['content'] = $content;
		$dealer_id = $this->getInsertID("admin",Announcement_Detail,$array);
		unset($array);

		return array('result'=>OPT_CONST::SUCCESS,'data'=>$result,'result_message'=>"发布成功");
	}


	public function updateAnnStatusOpt($arrData)
	{
		$result = array();
		$timestamp = time();

		if(!isset($arrData['data_id']) || $arrData['data_id'] === "" )
		{
			log_message('error', "function(sendMessage):lack of data_id"." in file".__FILE__." on Line ".__LINE__);
			return $this->missingPrameterArr("data_id");
		}
		if(!isset($arrData['status']) || $arrData['status'] === "" )
		{
			log_message('error', "function(sendMessage):lack of status"." in file".__FILE__." on Line ".__LINE__);
			return $this->missingPrameterArr("status");
		}

		$data_id = $arrData['data_id'];
		$status = 2;

		$updateTicket_str = 'update_time='.$timestamp.',update_appid="admin'.'",is_delete=1';
		$updateTicket_where = 'data_id="'.$data_id.'"';
		$updateTicket_query = $this->changeNodeValue("admin",Announcement_Detail,$updateTicket_str,$updateTicket_where);

		return array('result'=>OPT_CONST::SUCCESS,'data'=>$result,'result_message'=>"修改成功");
	}


}