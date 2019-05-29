<?php

class Db_Models extends CI_Model
{
	public function __construct()
	{
		header('Content-Type: text/html; charset=utf-8'); 
		parent::__construct();
	}
	
	
	
	/*
	 * 摘要：获得一条记录
	 * 
	 * 类型：protected
	 *
	 * 入口参数:
	 *		$sql:数据库操作字符串
	 *		$type：返回结果类型，0返回所有记录，1返回第一条记录
	 *
	 * 返回值:
	 *       -2:没有数据
	 */
	protected function getDataBySql($database,$type,$sql)
    {
        if(empty($sql))
        {
           return DB_CONST::DATA_NONEXISTENT; 
        } 
		else
        {
           $sqlString = $sql; 
        }

        $db_obj = $this->load->database($database, TRUE);

        $query = $db_obj->query($sqlString);
        if($query && $query->num_rows() > 0)
		{
			if($type == 0)
			{
				$result = $query->result_array();	//所有记录
			}
            else
			{
				$result = $query->row_array();		//一条记录
			}
		}
		else
		{
           return DB_CONST::DATA_NONEXISTENT; 
		}
        $query->free_result();
        return $result; 
    }
	
	
	
	/**
     * @function 通过sql语句update数据
     * @param string $table 操作表名
     * @param string $str 搜索内容 'id,name'
     * @param array $where 限制操作条件 str
     * 
     * @return 0 操作成功
     *		   -2 操作成功
     * **/ 
	protected function changeNodeValue($database,$table,$str,$where)
	{
		$db_obj = $this->load->database($database, TRUE);
		$db_obj->query("UPDATE ".$table." SET ".$str." WHERE ".$where);
		//资源回收
		unset($table);
		unset($str);
		unset($where);
		$affected_rows = $db_obj->affected_rows();
		if($affected_rows>0)
		{
			//return DB_CONST::UPDATE_SUCCESS;
			return $affected_rows;
		}
		else
		{
			return DB_CONST::UPDATE_FAILED;
		}
	}
	
	/**
	 * @function 添加数据的方法
     * @param $db 查询表名,如有跨库操作请指定数据名以及表名如：DatbaseName.TableName
     * @param $arrData 插入的数据，如：$idArray = array('id'=>1);
     * @return -1 操作失败
     *		   0  操作成功
	 */
	protected function getInsertID($database,$table, $arrData)
	{
		$db_obj = $this->load->database($database, TRUE);

		$db_obj->insert($table, $arrData);
		//判断数据库里面是否引起变化
		if($db_obj->affected_rows() > 0)
		{
			unset($db);//资源回收
			unset($arrData);//资源回收
			return $db_obj->insert_id();//操作成功
		}
		else
		{
			unset($db);//资源回收
			unset($arrData);//资源回收
			return DB_CONST::INSERT_FAILED;//操作失败
		}
	}
	
	
	
	/*
	 * 摘要：更改数据库的内容
	 * 
	 * 类型：private
	 *
	 * 入口参数：
	 *		$key:where中表的字段
	 *		$value:where中表的字段值
	 *		$db:数据库表名
	 *		$arrData:需要更改信息的数据
	 *
	 * 返回值:
	 *       0：操作成功
	 *       -1：操作失败
	 */
	protected function updateFunc($database,$key,$value,$table,$arrData)
	{
		$db_obj = $this->load->database($database, TRUE);

		$db_obj->where($key,$value);
		$db_obj->update($table,$arrData);
		if($db_obj->affected_rows()>0)
		{
			//资源回收
			unset($key);
			unset($value);
			unset($table);
			unset($arrData);
			
			return DB_CONST::SUCCESS;
		}
		else
		{
			//资源回收
			unset($key);
			unset($value);
			unset($table);
			unset($arrData);
			
			return DB_CONST::UPDATE_FAILED;
		}
	}
	
	/**
     * @function 多条件限定更新操作
     * @param $tableName 表名
     * @param $whereArr 更新的限制条件 数据类型 array 如:$whereArr = array('id' => $id);
     * @param $updataData 更新数据数组 数据类型 array 如:$updataData = array('a'=>'b')
     * 
     *****/
    public function updateMultiFunc($database,$tableName, $whereArr, $updataData)
    {
    	$db_obj = $this->load->database($database, TRUE);

        $db_obj->update($tableName, $updataData, $whereArr);
        $affNum = $db_obj->affected_rows();
        if($affNum > 0){
            $result = $affNum;
        } else {
            $result = DB_CONST::UPDATE_FAILED;
        }
        return $result;
    }
    
    
    
    
    
}