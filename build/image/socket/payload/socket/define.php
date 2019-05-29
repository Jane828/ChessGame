<?php
/**
 * User: leoxml
 * DateTime: 2018-01-24 19:44
 */
/*
define('DB_HOST', 'db.game.com');
define('DB_USER', 'poker');
define('DB_PWD', 'KaB6reIL9AQS');
define('DB_NAME', 'poker');

define('REDIS_HOST', 'r-wz9ecb3e0c3f6094.redis.rds.aliyuncs.com:6379');
define('REDIS_PWD', 'Poker@123');

define('LOG_DIR', '/wwwroot/logs/');
*/

define('DB_HOST', getenv("DB_HOST")); // '127.0.0.1'
define('DB_USER', getenv("DB_USER")); // 'root');
define('DB_PWD', getenv("DB_PWD")); // 'admin');
define('DB_NAME', getenv("DB_NAME")); // 'poker');
define('DB_PORT', getenv("DB_PORT")); 
define('DB_CHARSET', getenv("DB_CHARSET")); 

define('REDIS_HOST', getenv("REDIS_HOST")); // '127.0.0.1:6379');
define('REDIS_PWD', getenv("REDIS_PWD")); // '');

define('LOG_DIR', getenv("LOG_DIR"));
define('LOG_DIR2', LOG_DIR . "flower6/");

define('GAME_CLIENT_PORT', getenv("GAME_CLIENT_PORT"));
define('GAME_SERVER_PORT', getenv("GAME_SERVER_PORT"));
define('GAME_WEB_PORT', getenv("GAME_WEB_PORT"));

define('TIMER_ADDR', getenv("TIMER_ADDR"));
define('TIMER_PORT', getenv("TIMER_PORT"));

define('PROCESSOR_ADDR', getenv("PROCESSOR_ADDR"));
define('PROCESSOR_PORT', getenv("PROCESSOR_PORT"));

define('REDIS_DB_LOGS', getenv("REDIS_DB_LOGS"));
define('REDIS_DB', getenv("REDIS_DB"));

define('TICKET_MODE', getenv("TICKET_MODE"));
define('ROOM_IS_REUSE', getenv("ROOM_IS_REUSE"));
define('GAME_NUM_REACH_ROUND', getenv("GAME_NUM_REACH_ROUND"));
define('ROOM_AUTO_BREAK', getenv("ROOM_AUTO_BREAK"));

define('IS_SEND_GAME_SCORE', getenv("IS_SEND_GAME_SCORE"));
define('DEALER_NUM', getenv("DEALER_NUM"));
define('VERIFICATION_CODE', getenv("VERIFICATION_CODE"));
