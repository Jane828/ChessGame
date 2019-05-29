<?php

$uri = ltrim(parse_url($_SERVER['REQUEST_URI'], PHP_URL_PATH), '/');

if ($uri == '') {
    header('HTTP/1.1 403 Forbidden');
    exit();
}
$code = '';
$state='';
if (isset($_GET['state'])) {
    $state = $_GET['state'];
}

if (isset($_GET['code'])) {
    $code = $_GET['code'];
}

if (empty($code)) {
    header('HTTP/1.1 403 Forbidden');
    exit();
} else {
    $back_url = base64_decode($uri);
    header('Location: ' . implode('', [
                $back_url,
                strpos($back_url, '?') ? '&' : '?',
                'code=' . $code,
                '&state=' . $state
            ]));
    exit;
}

