<?php
/**
 * run with command
 * php start.php start
 */

ini_set('display_errors', 'on');
use Workerman\Worker;

if (strpos(strtolower(PHP_OS), 'win') === 0) {
    exit("start.php not support windows, please use start_for_win.bat\n");
}

// 检查扩展
if (!extension_loaded('swoole')) {
    exit("Please install swoole extension.\n");
}


// 标记是全局启动
define('GLOBAL_START', 1);

require_once __DIR__ . '/../vendor/autoload.php';
require_once 'Controllers/process.class.controller.php';


$worker = new Worker("text://0.0.0.0:" . getenv("TIMER_PORT")); // 20079
$worker::$pidFile = '/var/run/'.basename(__DIR__).'.pid';

// 4 processes
$worker->count = 2;

// Emitted when new connection come
$worker->onConnect = function ($connection) {
    Process::processOnConnect($connection->id);
};

// Emitted when data received
$worker->onMessage = function ($connection, $data) {
    Process::processOnMessage($connection, $data);
};

// Emitted when new connection come
$worker->onClose = function ($connection) {
    Process::processOnClose($connection);
};
// 运行所有服务
Worker::runAll();
