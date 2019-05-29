<?php
$ret = getenv("ROOM_IS_REUSE");
echo $ret."\n";

$kk = 123;
$arr = array("$kk");
echo json_encode($arr)."\n";
echo $arr[0]."\n";


