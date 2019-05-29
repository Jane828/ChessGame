<!DOCTYPE html>
<html >
<head>
<meta name="viewport" content="width=device-width,initial-scale=1.0, minimum-scale=1.0, maximum-scale=1.0, user-scalable=no"/>
<title>优展</title>
    <script type="text/javascript" src="<?php echo $image_url;?>files/js/jquery-1.9.1.min.js"></script>
</head>
<script>
    var socket_url = "<?php echo $socket1;?>";
    var session = "<?php echo $session;?>";
    function randomString(len) {
        len = len || 32;
        var $chars = 'ABCDEFGHJKMNPQRSTWXYZabcdefhijkmnprstwxyz2345678';    /****默认去掉了容易混淆的字符oOLl,9gq,Vv,Uu,I1****/
        var maxPos = $chars.length;
        var pwd = '';
        for (i = 0; i < len; i++) {
            pwd += $chars.charAt(Math.floor(Math.random() * maxPos));
        }
        return pwd;
    }
    function connects(socket){
        var is_operation = false;
        var socketStatus;
        web = new WebSocket(socket);
        web.onopen = function () {
            is_operation = true;

            var tiao=setInterval(function(){
                socketStatus=socketStatus+1;
                web.send("@");
                if(socketStatus>3||socketStatus>3){
                    window.location.reload();
                }
            },4000);

            web.send(JSON.stringify({
                "operation":"InitConnect",
                "account_id":20039,
                "session":session,
                "data":{
                    "data_key":Date.parse(new Date())+randomString(5),
                    "test": "123",
                }
            }));
        }
        web.onmessage = function (evt) {
            is_operation = false;
            if(evt.data=="@"){
                socketStatus=0;
                return 0;
            }
            console.log(evt.data);
        }
        web.onerror = function (evt) {
            console.log("WebSocketError!");
        }
        web.onclose = function () {
            if(is_operation){
                connects($scope.socket_url,$scope.socket_type);
            }else
                return 0;
        }
    }
    connects(socket_url);

</script>
<body>
<div style="position: fixed;top:45%;left:0;height: 50px;margin-top: -25px;width: 100%;text-align: center;font-size: 18px;font-family: 微软雅黑;line-height: 50px;">
<img src="http://oss.zht66.com/f69b6cf239c3619814ee7d264dcba999.jpg?imageView2/1/w/170/h/170" style="width: 50px;vertical-align: middle;border-radius: 10px;">
请用电脑浏览器打开网页
</div>
</body>
</html>
