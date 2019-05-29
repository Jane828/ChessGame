<?php

function is_HTTPS()
{
    if (!isset($_SERVER['HTTPS'])) {
        return false;
    }
    if ($_SERVER['HTTPS'] === 1) {  //Apache
        return true;
    } elseif ($_SERVER['HTTPS'] === 'on') { //IIS
        return true;
    } elseif ($_SERVER['SERVER_PORT'] == 443) { //其他
        return true;
    }
    return false;
}




if(!is_weixin()){
  header("Expires:-1");
header("Cache-Control:no_cache");
header("Pragma:no-cache");  
 // header('HTTP/1.1 403 Forbidden');
  exit();
}



function is_weixin(){ 
	if ( strpos($_SERVER['HTTP_USER_AGENT'], 'MicroMessenger') !== false ) {
			return true;
	}	
	return false;
}


function getDomain()
{
    $server_name = $_SERVER['HTTP_HOST'];

    if (strpos($server_name, 'www.') !== false) {
        return substr($server_name, 4);
    }

    return $server_name;
}



function encode($string = '', $skey = 'cxphp')
{
    $strArr = str_split(base64_encode($string));
    $strCount = count($strArr);
    foreach (str_split($skey) as $key => $value) {
        $key < $strCount && $strArr[$key].=$value;
    }
    return str_replace(array('=', '+', '/'), array('O0O0O', 'o000o', 'oo00o'), join('', $strArr));
}


function decode($string = '', $skey = 'cxphp')
{
    $strArr = str_split(str_replace(array('O0O0O', 'o000o', 'oo00o'), array('=', '+', '/'), $string), 2);
    $strCount = count($strArr);
    foreach (str_split($skey) as $key => $value) {
        $key <= $strCount  && isset($strArr[$key]) && $strArr[$key][1] === $value && $strArr[$key] = $strArr[$key][0];
    }
    return base64_decode(join('', $strArr));
}




$appid = '';
$scope = 'snsapi_userinfo';
$state = '';
$code = '';
$redirect_uri = '';
$device = '';
$protocol = '';

if (is_HTTPS()) {
    $protocol = 'https';
} else {
    $protocol = 'http';
}

if (isset($_GET['device'])) {
    $device = $_GET['device'];
}

if (isset($_GET['appid'])) {
    $appid = $_GET['appid'];
}
if (isset($_GET['state'])) {
    $state = $_GET['state'];
}
if (isset($_GET['redirect_uri'])) {
    $redirect_uri = $_GET['redirect_uri'];
}
if (isset($_GET['code'])) {
    $code = $_GET['code'];
}
if (isset($_GET['scope'])) {
    $scope = $_GET['scope'];
}

if ($code == 'test') {
    exit;
}

if (empty($code)) {
    $authUrl = '';
    if ($device == 'pc') {
        $authUrl = 'https://open.weixin.qq.com/connect/qrconnect';
    } else {
        $authUrl = 'https://open.weixin.qq.com/connect/oauth2/authorize';
    }

    $options = [
        $authUrl,
        '?appid=' . $appid,
        '&redirect_uri=' . urlencode($protocol . '://' . $_SERVER['HTTP_HOST'] . '/'),
        '&response_type=code',
        '&scope=' . $scope,
        '&state=' . $state,
        '#wechat_redirect'
    ];

    //把redirect_uri先写到cookie
    header(implode('', [
        "Set-Cookie: redirect_uri=",
        encode($redirect_uri),
        "; path=/; domain=",
        getDomain(),
        "; expires=" . gmstrftime("%A, %d-%b-%Y %H:%M:%S GMT", time() + 60),
        "; Max-Age=" + 60,
        "; httponly"
    ]));

    //  header('Location: ' . implode('', $options));


    echo "<html>
<head>
<title>正在跳转...</title>
<meta http-equiv=\"Content-Language\" content=\"zh-CN\">
<meta HTTP-EQUIV=\"Content-Type\" CONTENT=\"text/html; charset=utf-8\">
<meta http-equiv=\"refresh\" content=\"0.0;url=".implode('', $options)."\">
</head>
<body>
</body>
</html>";
    exit;
} else {
    if (isset($_COOKIE['redirect_uri'])) {
        $back_url = base64_decode(decode($_COOKIE['redirect_uri']));
        header('Location: ' . implode('', [
                $back_url,
                strpos($back_url, '?') ? '&' : '?',
                'code=' . $code,
                '&state=' . $state
            ]));
    }
}

