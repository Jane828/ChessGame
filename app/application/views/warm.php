<html>
<head>
    <meta charset="utf-8">
    <meta name="viewport"
          content="width=device-width,initial-scale=1.0, minimum-scale=1.0, maximum-scale=1.0, user-scalable=no"/>
    <meta name="format-detection" content="telephone=no"/>
    <meta name="msapplication-tap-highlight" content="no"/>
    <script type="text/javascript" src="<?php echo $image_url;?>files/js/jquery-1.9.1.min.js"></script>

    <title>温馨提示</title>
    <script>

        window.onpageshow = function (event) {
            if (event.persisted) {
                window.location.reload()
            }
        };
        $(function () {
            $(".buttonMiddle").click(function () {
                $.get("<?php echo $base_url;?>/f/agree", {}, function (data) {
                    if (window.location.href.indexOf("?") > 0) {
                        window.location.href += "&" + 10000 * Math.random();
                    }
                    else {
                        window.location.href += "?" + 10000 * Math.random();
                    }
                });
            })
        })

    </script>
    <style type="text/css">
        .alert {
            position: fixed;
            width: 100%;
            height: 100%;
            top: 0;
            left: 0;
            z-index: 111;
            color: #fff;
            font-family: simHei;
        }

        .alert .alertBack {
            width: 100%;
            height: 100%;
            background: #000;
            opacity: 0.8;
            position: absolute;
        }

        .alert .mainPart {
            position: relative;
            top: 45%;
            left: 50%;
            margin-top: -50vw;
            margin-left: -40vw;
            width: 80vw;
            height: calc(100vw - 50px);
            background:#FFF4DC;
	    padding-top:50px;
	    border-radius:5px;
        }
        .alert .mainBg{
            position: absolute;
            top: calc(45% - 5px);
            left: calc(50% - 5px);
            margin-top: -50vw;
            margin-left: -40vw;
            width: calc(80vw + 10px);
            height: calc(100vw + 10px);
            background:rgba(255,255,255,0.3);
	    border-radius:5px;
        }
        .alert .mainPart .showPart {
            width:75vw;
            height:70vw;
            background:url("<?php echo $image_url;?>/files/images/common/userInner.png") no-repeat;
	    background-size:contain;
            margin: 0 auto;
	    color:#714D29;
	    font-size:14px;
	    padding-top:10px;
        }
	.alert .mainPart .showPart .text{
                   width:65vw;
		   margin: 0 auto;

	}

        .alert .mainPart .buttonMiddle {
            background:url("<?php echo $image_url;?>/files/images/common/button2.png") no-repeat;
            background-size: cover;
            width:35vw;
            height:40px;
	    margin: 0 auto;
        }
        .alert .mainPart .createTitle{padding-top:5px; margin:0 auto;margin-top:-55px; text-align: center; background:url("<?php echo $image_url;?>/files/images/common/storetitle.png") no-repeat; background-size:contain; width:50vw; height:40px;}
        .alert .mainPart  .createTitle span{ color:#7D2F00; text-shadow: 2px 2px 2px white; font-weight:bold; font-size:20px; }
    </style>


</head>

<body style="background-color: #0e0226">
<div class="main" id="app-main" style="position: relative; width: 100%;margin: 0 auto; background: #0e0226;">

    <div class="alert">
        <div class="alertBack"></div>
        <div class="mainBg"></div>
        <div class="mainPart">
        <div class="createTitle">
                <!--<img src="<?php echo $image_url;?>files/images/common/txt_rule.png" /> -->
               <span >用户协议</span>
            </div>
            <div class="showPart">
	    <div class="text">
	    
                            本游戏仅供娱乐，严禁赌博，如发现有赌博行为，将封停账号并向公安机关举报。<br/>
游戏中使用到的房卡为游戏道具，不具有任何财产性功能，只限用户本人在游戏中使用，本公司对于用户所拥有的房卡不提供任何形式官方回购、直接或变相兑换现金或实物，相互赠予转让等服务或相关功能。<br/>
游戏仅供休闲娱乐使用，游戏中出现问题请联系客服。
</div>
                        </div>
            <div class="buttonMiddle"></div>
        </div>
    </div>

</div>
</body>

</html>
