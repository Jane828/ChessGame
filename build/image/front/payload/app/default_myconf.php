<?php
$callback_host = file_get_contents("/data/conf/callback.txt");

define('MY_WS', "ws://".$callback_host);
define('MY_FILE_URL', "http://".$callback_host."/all/");
define('MY_IMAGE_URL', "http://".$callback_host."/all/");
