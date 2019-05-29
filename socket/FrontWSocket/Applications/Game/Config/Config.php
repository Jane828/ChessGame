<?php

require_once __DIR__ . '/../../../../define.php';

class Config {

    //------------------ pe ------------------
    //redis logs
    const Redis_HostPort_Logs = REDIS_HOST;
    const Redis_Password_Logs = REDIS_PWD;
    const Redis_Database_Logs = REDIS_DB_LOGS;// 1;
    const Redis_HostPort      = REDIS_HOST;
    const Redis_Password      = REDIS_PWD;
    const Redis_Database      = REDIS_DB; //10;

    //db
    const DB_Host     = DB_HOST;
    const DB_Name     = DB_NAME;
    const DB_User     = DB_USER;
    const DB_Password = DB_PWD;
    const DB_Port     = DB_PORT;//"3306";
    const DB_Charset  = DB_CHARSET;

    const Processor_Port    = PROCESSOR_PORT;
    const Processor_Address = PROCESSOR_ADDR;

    const GameClient_Port = GAME_CLIENT_PORT;
    const GameServer_Port = GAME_SERVER_PORT;
    const GameWeb_Port    = GAME_WEB_PORT;
    const Log_Dir           = LOG_DIR2;

    const Dealer_Num        = DEALER_NUM;
    const Verification_Code = VERIFICATION_CODE;//"987654321098765432109876543210zz";
}
