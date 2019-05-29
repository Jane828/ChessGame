<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="utf-8" >
    <meta name="viewport" content="width=device-width,initial-scale=1.0, minimum-scale=1.0, maximum-scale=1.0, user-scalable=no"/>
    <meta name="format-detection" content="telephone=no" />
    <meta name="msapplication-tap-highlight" content="no" />
    <meta http-equiv="Pragma" content="public" />
    <meta http-equiv="Cache-Control" content="public" />
    <title>我的包厢</title>
    <link rel="stylesheet" href="<?php echo $base_url;?>files/css/box/box.css?_version=<?php echo $front_version;?>">
    <link rel="stylesheet" href="<?php echo $base_url;?>files/css/box/bullP.css">
    <link rel="stylesheet" href="<?php echo $base_url;?>files/css/box/sgP.css">
    <link rel="stylesheet" href="<?php echo $base_url;?>files/css/box/anbaoP.css">
    <link rel="stylesheet" href="<?php echo $base_url;?>files/css/box/porkerTable.css">
    <script>
        var baseUrl = "<?php echo $base_url;?>";
        var imageUrl = "<?php echo $image_url;?>";
        var accountId = "<?php echo $user['account_id'];?>";
        var wsUrl = 'ws://wx.kuaijistore.com:20032/';
        var gameWsUrl = {
            "1":"<?php echo $socket1;?>",
            "111":"<?php echo $socket1;?>",
            "110":"<?php echo $socket_tflower;?>",
            "5":"<?php echo $socket3;?>",
            "9":"<?php echo $socket5;?>",
            '12' : "<?php echo $socket_tbull;?>",
            '13' : "<?php echo $socket_fbull;?>",
            '36' : "<?php echo $socket_sangong?>",
            '37' : "<?php echo $socket_nsangong?>",
            '71' : "<?php echo $socket_lbull?>",
            '61' : "<?php echo $socket_treasure?>",
        };
//	console.log(<?php echo $socket_treasure?>);
        var roomName = {
            '1': '飘三叶',
            '111': '大牌飘三叶',
            '110': '十人飘三叶',
            '5': '六人牛牛',
            '9': '九人牛牛',
            '12': '十二人牛牛',
            '13': '十三人牛牛',
            '71': '十人癞子牛',
            '36': '六人三公',
            '37': '九人三公',
            '61': '十人暗宝',
        }
        console.log(gameWsUrl['1']);
        var userData = {
            "id":"<?php echo $user['account_id'];?>",
            "name":"<?php echo $user['nickname'];?>",
            "avatar":"<?php echo $user['headimgurl'];?>",
            "card":"<?php echo $card;?>",
            "phone":"<?php echo $user['phone'];?>",
        };
        var session = '<?php echo $session;?>';
        // 游戏类别对象
        var gameTypeObj = {
            'sg': 36,
            'nsg': 37,
            'flower': 1,
            'tflower': 110,
            'bflower': 111,
            'bull': 5,
            'nbull': 9,
            'twbull': 12,
            'thbull':13,
            'tbull':71,
            'anbao':61,
        }
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
        // 返回游戏类别
        function returnGameType(game_type) {
            return gameTypeObj[game_type];
        }
        function returnGameWsUrl(game_type){
            return gameWsUrl[game_type.toString()];
        }
        function returnGameName(game_type){
            return roomName[game_type.toString()];
        }
    </script>
    <script type="text/javascript" src="<?php echo $base_url;?>files/js/box/flower-property.js"></script>
    <script type="text/javascript" src="<?php echo $base_url;?>files/js/box/flowerP.js"></script>
    <script type="text/javascript" src="<?php echo $base_url;?>files/js/box/flowerDetail.js"></script>
    <script type="text/javascript" src="<?php echo $base_url;?>files/js/box/bull-property.js"></script>
    <script type="text/javascript" src="<?php echo $base_url;?>files/js/box/bullP.js"></script>
    <script type="text/javascript" src="<?php echo $base_url;?>files/js/box/bullDetail.js"></script>
    <script type="text/javascript" src="<?php echo $base_url;?>files/js/box/sg-property.js"></script>
    <script type="text/javascript" src="<?php echo $base_url;?>files/js/box/sgP.js"></script>
    <script type="text/javascript" src="<?php echo $base_url;?>files/js/box/sgDetail.js"></script>
    <script type="text/javascript" src="<?php echo $base_url;?>files/js/box/anbao-property.js"></script>
    <script type="text/javascript" src="<?php echo $base_url;?>files/js/box/anbaoP.js"></script>
    <script type="text/javascript" src="<?php echo $base_url;?>files/js/box/anbaoDetail.js"></script>
    <script type="text/javascript" src="<?php echo $base_url;?>files/js/box/main.js"></script>
    <script type="text/javascript" src="<?php echo $base_url;?>files/js/box/bottomtab.js"></script>
    <script type="text/javascript" src="<?php echo $base_url;?>files/js/box/detail.js"></script>
    <script type="text/javascript" src="<?php echo $base_url;?>files/js/box/porkerTable.js"></script>
    <script type="text/javascript" src="<?php echo $base_url;?>files/js/box/detailWrap.js"></script>
    <script type="text/javascript" src="<?php echo $base_url;?>files/js/box/boxWrap.js"></script>
    <style>
        .bottom-menu {height: 10vh;position: fixed;left: 0;bottom: 0;width: 100vw;z-index:100;background:url("<?php echo $image_url;?>files/images/hall/menu.png")no-repeat;background-size:100% 100%;}
        .bottom-menu ul {width:100vw;}
        .bottom-menu .menu-item{position: relative;width: 25vw;float: left;height: 10vh;text-align: center;padding-top: 1vh;}
        .bottom-menu .menu-item a {display:inline-block;}
        .bottom-menu .menu-item img{height: 5vh;}
        .bottom-menu .menu-item p {color: #F0D582;font-weight: 600;}
        .bottom-menu .menu-item span {position: absolute;right: 0;top: 2vh;width: 2px;height: 6vh;background:url("<?php echo $image_url;?>files/images/hall/border.png")no-repeat;background-size:100% 180%;}
        .bottom-menu .menu-item-selected{background:url("<?php echo $image_url;?>files/images/hall/active.png")no-repeat;background-size:100% 100%;}
    </style>
</head>
<body>
    <div id="box">
        <box-wrap></box-wrap>
        <bottom-tab></bottom-tab>
    </div>
</body>
<script type="text/javascript" src="<?php echo $image_url;?>files/js/vue-resource.min.js" ></script>
<script type="text/javascript" src="<?php echo $image_url;?>files/js/vue.min.js"></script>
<script type="text/javascript" src="<?php echo $base_url;?>files/js/box/box.js?_version=<?php echo $front_version;?>"></script>
</html>
