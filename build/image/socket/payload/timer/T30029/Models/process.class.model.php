<?php

require_once __DIR__ . '/public.class.model.php';
class Process_Model extends Public_Model
{
	
	/*
		单例，返回当前类的静态对象
	*/
	public static function getModelObject()
	{
		static $modelObject;
		if(is_object($modelObject))
		{
			return $modelObject;
		}
		else
		{
			$modelObject = new self();
			return $modelObject;
		}
	}
	
	
	
	/*
	*	检查推送格式
	*
	*
	*
	*/
	public function checkupServerFormat($jsonString)
	{
		$timestamp = time();
		$resultType = "";
		//解析json字符串为数组
		$jsonArray = $this->_splitJsonString($jsonString);
		if($jsonArray === OPT_CONST::JSON_FALSE)	//判断是否为json格式
		{
			$result = OPT_CONST::CORRECT_FORMAT;
			$operation = "Error-1";
			$opcode = "Error-1";
		}
		else
		{
			$result = OPT_CONST::POSTARRAY_TRUE;
			
			//判断数据格式是否正确
			if(isset($jsonArray['module']) && $jsonArray['module'] == "GW_Push")
			{
				if(!isset($jsonArray['type']))
				{
					$result = OPT_CONST::CORRECT_FORMAT;
				}
				
				if(!isset($jsonArray['object_array']))
				{
					$result = OPT_CONST::CORRECT_FORMAT;
				}
				
				if(!isset($jsonArray['content']))
				{
					$result = OPT_CONST::CORRECT_FORMAT;
				}
			}
			else if(isset($jsonArray['module']) && $jsonArray['module'] == "GW_Server")
			{
				if(!isset($jsonArray['operation']))
				{
					$result = OPT_CONST::CORRECT_FORMAT;
				}
			}
			else
			{
				$result = OPT_CONST::CORRECT_FORMAT;
			}
			
			
			if($result === OPT_CONST::POSTARRAY_TRUE)
			{
				return $jsonArray;
			}
		}
		
		return $result;
	}
	
	
	
	/*
	*	检查格式
	*
	*
	*
	*/
	public function checkupFormat($jsonString)
	{
		$timestamp = time();
		$resultType = "";
		//解析json字符串为数组
		$jsonArray = $this->_splitJsonString($jsonString);
		if($jsonArray === OPT_CONST::JSON_FALSE)	//判断是否为json格式
		{
			$result = OPT_CONST::CORRECT_FORMAT;
			$operation = "Error-1";
			$opcode = "Error-1";
		}
		else
		{
			//判断数据格式是否正确
			$result = $this->_checkPostArray($jsonArray);
			if($result === OPT_CONST::POSTARRAY_TRUE)
			{
				return $jsonArray;	
			}
		}
		
		return $result;
	}
	
	
	/*
	*	组装返回值
	*
	*
	*
	*/
	public function formartReturn($result)
	{
		$resultArray = $result;
		
		return $this->_JSON($resultArray);		//编译为JSON格式,返回结果
	}
	
	
}