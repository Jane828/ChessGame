<?php


namespace  Module\DB;

require_once ( dirname(dirname(__DIR__)) . '/Config/Config.php');
class DbConfig{
    public static $DB = array(
        'host'    	=> \Config::DB_Host,
        'port'    	=> \Config::DB_Port,
        'user'    	=> \Config::DB_User,
        'password' 	=> \Config::DB_Password,
        'dbname'  	=> \Config::DB_Name,
        'charset'   => \Config::DB_Charset,
    );
}