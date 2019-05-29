<?php
function sc_send($text, $desp = '', $key = '1759-d6d8cd6d80710ed75dbfc7060b4dd4e8')
{
    $postdata = http_build_query(
    array(
        'sendkey' => $key,
        'text' => $text,
        'desp' => $desp
    )
);

    $opts = array('http' =>
    array(
        'method'  => 'POST',
        'header'  => 'Content-type: application/x-www-form-urlencoded',
        'content' => $postdata
    )
);
    $context  = stream_context_create($opts);
    return $result = file_get_contents('https://pushbear.ftqq.com/sub', false, $context);
}

echo sc_send('test'.time(),'php test'.time());
