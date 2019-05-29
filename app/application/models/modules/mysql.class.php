<?php
// +----------------------------------------------------------------------
// |MySQL操作类
// +----------------------------------------------------------------------
// | Author: 
// +----------------------------------------------------------------------
class MySQL{
     
    private $db_mysql_hostname;
    private $db_mysql_username;
    private $db_mysql_password;
    private $db_mysql_database;
    private $db_mysql_port;
    private $db_mysql_charset;
     
    private $query_list = array();
     
    //查询次数
    public $query_count = 0;
    //查询开始时间
    public $query_start_time;
     
    //当前查询ID
    protected $queryID;
    //当前连接
    protected $conn;
    // 事务指令数
    protected $transTimes = 0;
    // 返回或者影响记录数
    protected $numRows    = 0;
    // 错误信息
    protected $error      = '';
     
    public function __construct($hostname_or_conf,$username,$password,$database,$port = '3306',$char = 'utf8'){
        if(is_array($hostname_or_conf)){
            $this->db_mysql_hostname = $hostname_or_conf['hostname'];
            $this->db_mysql_username = $hostname_or_conf['username'];
            $this->db_mysql_password = $hostname_or_conf['password'];
            $this->db_mysql_database = $hostname_or_conf['database'];
            $this->db_mysql_port = isset($hostname_or_conf['port'])?$hostname_or_conf['port']:'3306';
            $this->db_mysql_charset = isset($hostname_or_conf['charset'])?$hostname_or_conf['charset']:'utf8';
             
        }elseif(!empty($hostname_or_conf)||!empty($username)||!empty($password)||!empty($database))
        {
             $this->db_mysql_hostname = $hostname_or_conf;
             $this->db_mysql_username = $username;
             $this->db_mysql_password = $password;
             $this->db_mysql_database = $database;
             $this->db_mysql_port = $port;
             $this->db_mysql_charset = $char;
              
        }else{
            die('configuration error.');
        }
        $this->connect();
    }
     
    private function connect(){
        $server = $this->db_mysql_hostname.':'.$this->db_mysql_port;
        $this->conn = mysqli_connect($server,$this->db_mysql_username, $this->db_mysql_password ,$this->db_mysql_database) or die('Connect MySQL DB error!');
        // $this->conn = mysql_connect($server,$this->db_mysql_username,$this->db_mysql_password,true) or die('Connect MySQL DB error!');
        // mysql_select_db($this->db_mysql_database,$this->conn) or die('select db error!');
        mysqli_query($this->conn, "set names " . $this->db_mysql_charset);
    }
    /**
     +----------------------------------------------------------
     * 设置数据对象值
     +----------------------------------------------------------
     * @access public
     +----------------------------------------------------------
     *table,where,order,limit,data,field,join,group,having
     +----------------------------------------------------------
     */
    public function table($table){
        $this->query_list['table'] = $table;
        return $this;
    }
     
    public function where($where){
        $this->query_list['where'] = $where;
        return $this;
    }
     
    public function order($order){
        $this->query_list['order'] = $order;
        return $this;
    }
     
    public function limit($offset,$length){
        if(!isset($length)){
            $length = $offset;
            $offset = 0;
        }
        $this->query_list['limit'] = 'limit '.$offset.','.$length;
        return $this;
    }
     
    public function data($data){
        /*
        if(is_object($data)){
            $data   =   get_object_vars($data);
        }elseif (is_string($data)){
            parse_str($data,$data);
        }elseif(!is_array($data)){
            //Log:DATA_TYPE_INVALID
        }
        */
        $this->query_list['data'] = $data;
        return $this;
    }
    public function field($fields){
        $this->query_list['fields'] = $fields;
        return $this;
    }
    public function join($join){
        $this->query_list['join'] = $join;
        return $this;
    }
    public function group($group){
        $this->query_list['group'] = $group;
        return $this;
    }
    public function having($having){
        $this->query_list['having'] = $having;
        return $this;
    }
    /**
     +----------------------------------------------------------
     * 查询
     +----------------------------------------------------------
     * @access public
     +----------------------------------------------------------
     * @param 
     +----------------------------------------------------------
     */
    public function select(){
        $select_sql = 'select ';
        $fields = isset($this->query_list['fields'])?$this->query_list['fields']:'*';
        $select_sql.=$fields;
        $select_sql.= ' from `'.$this->query_list['table'].'` ';
         
        isset($this->query_list['join'])?($select_sql.=$this->query_list['join']):'';
        isset($this->query_list['where'])?($select_sql.=' where '.$this->query_list['where']):'';
        isset($this->query_list['group'])?($select_sql.=' group by'.$this->query_list['group']):'';
        isset($this->query_list['having'])?($select_sql.=' mysql having '.$this->query_list['having']):'';
        isset($this->query_list['order'])?($select_sql.=' order by '.$this->query_list['order']):'';
        isset($this->query_list['limit'])?($select_sql.=' '.$this->query_list['limit']):'';
         
        return $this->query($select_sql);
    }
    /**
     +----------------------------------------------------------
     * 增加
     +----------------------------------------------------------
     * @access public
     +----------------------------------------------------------
     * @param 
     +----------------------------------------------------------
     */
    public function add(){
        $add_sql = 'insert into `'.$this->query_list['table'].'` (';
         
        $data = $this->query_list['data'];
        $value = $field = '';
        foreach($data as $k=>$v){
            $field .= '`'.$k.'`,';
            if(is_numeric($v))
                $value .= $v.',';
            else
                $value .= '\''.$v.'\',';
        }
        $add_sql .= rtrim($field,',').') values ('.rtrim($value,',').')';
 
        return $this->execute($add_sql);
    }
    /**
     +----------------------------------------------------------
     * 删除
     +----------------------------------------------------------
     * @access public
     +----------------------------------------------------------
     * @param 
     +----------------------------------------------------------
     */
    public function delete(){
        $del_sql = 'delete from `'.$this->query_list['table'].'` where '.$this->query_list['where'];
         
        if(isset($this->query_list['order']))
            $del_sql .= 'order by '.$this->query_list['order'];
        if(isset($this->query_list['limit']))
            $del_sql .= ' '.$this->query_list['limit'];
             
        return $this->execute($del_sql);
         
    }
    /**
     +----------------------------------------------------------
     * 更新
     +----------------------------------------------------------
     * @access public
     +----------------------------------------------------------
     * @param 
     +----------------------------------------------------------
     */
    public function update(){
        $update_sql = 'update `'.$this->query_list['table'].'` set ';
        $data = $this->query_list['data'];
         
        foreach($data as $k=>$v){
            if(is_numeric($v))
                $update_sql .= '`'.$k.'` ='.$v.',';
            else
                $update_sql .= '`'.$k.'` =\''.$v.'\',';
        }
        $update_sql = rtrim($update_sql,',');
        if(isset($this->query_list['where']))
            $update_sql .= ' where '.$this->query_list['where'];
        if(isset($this->query_list['order']))
            $update_sql .= ' order by '.$this->query_list['order'];
        if(isset($this->query_list['limit']))
            $update_sql .= ' '.$this->query_list['limit'];
         
        return $this->execute($update_sql);
         
    }
     /**
     +----------------------------------------------------------
     * 执行查询 返回数据集
     +----------------------------------------------------------
     * @access public
     +----------------------------------------------------------
     * @param string $sql  sql指令
     */
    public function query($sql) {
        if ( !$this->conn ) return false;
        $this->queryStr = $sql;
        //释放前次的查询结果
        if ( $this->queryID ) {    $this->free();    }
         
        $this->query_start_time = microtime(true);
         
        $this->queryID = mysqli_query($this->conn, $sql);
        $this->query_count++;
        
        if ( false === $this->queryID || true === $this->queryID ) {
            $this->error();
            return false;
        } else {
            $this->numRows = mysqli_num_rows($this->queryID);
            return $this->getAll();
        }
    }
    /**
     +----------------------------------------------------------
     * 执行语句
     +----------------------------------------------------------
     * @access public
     +----------------------------------------------------------
     * @param string $sql  sql指令
     +----------------------------------------------------------
     */
    public function execute($sql) {
        if ( !$this->conn ) return false;
        $this->queryStr = $sql;
        //释放前次的查询结果
        if ( $this->queryID ) {    $this->free();    }
         
        $this->query_start_time = microtime(true);
         
        $result =   mysqli_query($this->conn, $sql) ;
        $this->query_count++;
        if ( false === $result) {
            $this->error();
            return false;
        } else {
            $this->numRows = mysqli_affected_rows($this->conn);
            return $this->numRows;
        }
    }
    /**
     +----------------------------------------------------------
     * 获得所有的查询数据
     +----------------------------------------------------------
     * @access private
     +----------------------------------------------------------
     * @return array
     */
    private function getAll() {
        //返回数据集
        $result = array();
        if($this->numRows >0) {
            while($row = mysqli_fetch_assoc($this->queryID)){
                $result[]   =   $row;
            }
            mysqli_data_seek($this->queryID,0);
        }
        return $result;
    }
    /**
     +----------------------------------------------------------
     * 取得数据表的字段信息
     +----------------------------------------------------------
     * @access public
     +----------------------------------------------------------
     */
    public function getFields($tableName) {
        $result =   $this->query('SHOW COLUMNS FROM `'.$tableName.'`');
        $info   =   array();
        if($result) {
            foreach ($result as $key => $val) {
                $info[$val['Field']] = array(
                    'name'    => $val['Field'],
                    'type'    => $val['Type'],
                    'notnull' => (bool) ($val['Null'] === ''), // not null is empty, null is yes
                    'default' => $val['Default'],
                    'primary' => (strtolower($val['Key']) == 'pri'),
                    'autoinc' => (strtolower($val['Extra']) == 'auto_increment'),
                );
            }
        }
        return $info;
    }
    /**
     +----------------------------------------------------------
     * 取得数据库的表信息
     +----------------------------------------------------------
     * @access public
     +----------------------------------------------------------
     */
    public function getTables($dbName='') {
        if(!empty($dbName)) {
           $sql    = 'SHOW TABLES FROM '.$dbName;
        }else{
           $sql    = 'SHOW TABLES ';
        }
        $result =   $this->query($sql);
        $info   =   array();
        foreach ($result as $key => $val) {
            $info[$key] = current($val);
        }
        return $info;
    }
 
    /**
     +----------------------------------------------------------
     * 最后次操作的ID
     +----------------------------------------------------------
     * @access public
     +----------------------------------------------------------
     * @param 
     +----------------------------------------------------------
     */
     public function last_insert_id(){
        return mysqli_insert_id($this->conn);
    }
    /**
     * 执行一条带有结果集计数的
     */
    public function count($sql){
        return $this->execute($sql);
    }
    /**
     +----------------------------------------------------------
     * 启动事务
     +----------------------------------------------------------
     * @access public
     +----------------------------------------------------------
     * @return void
     +----------------------------------------------------------
     */
    public function startTrans() {
        if ($this->transTimes == 0) {
            mysqli_query($this->conn, 'START TRANSACTION');
        }
        $this->transTimes++;
        return ;
    }
 
    /**
     +----------------------------------------------------------
     * 提交事务
     +----------------------------------------------------------
     * @access public
     +----------------------------------------------------------
     * @return boolen
     +----------------------------------------------------------
     */
    public function commit()
    {
        if ($this->transTimes > 0) {
            $result = mysqli_query($this->conn, 'COMMIT');
            $this->transTimes = 0;
            if(!$result){
                throw new Exception($this->error());
            }
        }
        return true;
    }
 
    /**
     +----------------------------------------------------------
     * 事务回滚
     +----------------------------------------------------------
     * @access public
     +----------------------------------------------------------
     * @return boolen
     +----------------------------------------------------------
     */
    public function rollback()
    {
        if ($this->transTimes > 0) {
            $result = mysqli_query( $this->conn, 'ROLLBACK');
            $this->transTimes = 0;
            if(!$result){
                throw new Exception($this->error());
            }
        }
        return true;
    }
    /**
     +----------------------------------------------------------
     * 错误信息
     +----------------------------------------------------------
     * @access public
     +----------------------------------------------------------
     * @param 
     +----------------------------------------------------------
     */
     public function error() {
        $this->error = mysqli_error($this->conn);
        if('' != $this->queryStr){
            $this->error .= "\n [ SQL语句 ] : ".$this->queryStr;
        }
        return $this->error;
    }
    /**
     +----------------------------------------------------------
     * 释放查询结果
     +----------------------------------------------------------
     * @access public
     +----------------------------------------------------------
     */
    public function free() {
        @mysqli_free_result($this->queryID);
        $this->queryID = 0;
        $this->query_list = null;
    }
    /**
     +----------------------------------------------------------
     * 关闭连接
     +----------------------------------------------------------
     * @access public
     +----------------------------------------------------------
     * @param 
     +----------------------------------------------------------
     */
    function close(){
        if ($this->conn && !mysqli_close($this->conn)){
            throw new Exception($this->error());
        }
        $this->conn = 0;
        $this->query_count = 0;
    }
    /**
     +----------------------------------------------------------
     * 析构方法
     +----------------------------------------------------------
     * @access public
     +----------------------------------------------------------
     */
    function __destruct(){
         $this->close();
    }
}
