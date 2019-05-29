<html ng-app="app">
<head>
<meta charset="utf-8" >
<meta name="viewport" content="width=device-width,initial-scale=1.0, minimum-scale=1.0, maximum-scale=1.0, user-scalable=no"/>
<meta name="format-detection" content="telephone=no" />
<meta name="msapplication-tap-highlight" content="no" />
<title>房卡包</title>

<script type="text/javascript" src="<?php echo $image_url;?>files/js/jquery-1.9.1.min.js"></script>
<script type="text/javascript" src="<?php echo $image_url;?>files/js/jquery.copy.js"></script>
<script type="text/javascript" src="<?php echo $image_url;?>files/js/angular.min.js"></script>
<script src="https://res.wx.qq.com/open/js/jweixin-1.0.0.js" ></script>
<script type="text/javascript" src="<?php echo $image_url;?>files/js/fastclick.js"></script>
<!--
<script type="text/javascript" src="<?php echo $image_url;?>files/cjs/roomRedPackage.js"></script>
-->
<script type="text/javascript">


$(function () {
    wx.config({    
            debug: false,    
            appId: "<?php echo $config_ary['appId'];?>",    
            timestamp: "<?php echo $config_ary['timestamp'];?>",    
            nonceStr:"<?php echo $config_ary['nonceStr'];?>",    
            signature: "<?php echo $config_ary['signature'];?>",    
            jsApiList: [     
                'onMenuShareTimeline',    
                'onMenuShareAppMessage',
                'hideMenuItems',
         ]    
    }); 

    wx.ready(function () {
        wx.hideMenuItems({
            menuList: [
                "menuItem:copyUrl",
                "menuItem:share:qq",
                "menuItem:share:weiboApp",
                "menuItem:share:facebook",
                "menuItem:share:QZone",
                "menuItem:editTag",
                "menuItem:copyUrl",
                "menuItem:share:email",
            ] // 要隐藏的菜单项，只能隐藏“传播类”和“保护类”按钮，所有menu项见附录3
        });
            
        wx.onMenuShareTimeline({    
            title: "房卡包",    
            desc: "<?php echo $user['nickname'];?>给你发了一个房卡包",    
            link: "<?php echo $share_url;?>",        
            imgUrl: "<?php echo $share_icon;?>",    
            success: function () { 
                // 用户确认分享后执行的回调函数
            },
            cancel: function () { 
                // 用户取消分享后执行的回调函数
            }   
        });

        wx.onMenuShareAppMessage({    
            title: "房卡包",    
            desc: "<?php echo $user['nickname'];?>给你发了一个房卡包",    
            link: "<?php echo $share_url;?>",       
            imgUrl: "<?php echo $share_icon;?>",    
            success: function () { 
                // 用户确认分享后执行的回调函数
            },
            cancel: function () { 
                // 用户取消分享后执行的回调函数
            }   
        });     
    });
    $('.copy_link').copy({
        copy: function(_this){
            return window.location.href;
        },
        afterCopy: function(res){
            if(res==true){
                alert('链接复制成功！');
            }else{
                alert('链接复制失败！');
            }
        }
    });
});

var app = angular.module('app',[])

app.controller("myCtrl", function($scope,$http) {	
	$scope.width = window.innerWidth;
	$scope.height = window.innerHeight;
    $scope.rpHeight = $scope.width * 1.02;
    $scope.rpWidth = $scope.width * 0.76
    $scope.rpLeft = $scope.width * 0.12;
    $scope.rpX = $scope.rpWidth * 0.08;
    $scope.rpTop = ($scope.height - $scope.rpHeight) / 2 - 30;
    $scope.lineTop = $scope.rpTop + $scope.rpHeight / 2;
    $scope.receiveNameX = $scope.rpX * 1.5 + $scope.width * 0.1;
    $scope.receiveNameWidth = ($scope.rpWidth - $scope.rpX * 3 - $scope.width * 0.1 ) / 1.5;
    $scope.receiveNameOffset = 2;
    $scope.receiveCountWidth = $scope.rpWidth - $scope.receiveNameX - $scope.receiveNameWidth - $scope.receiveNameOffset - $scope.rpX * 0.7;

	$scope.userInfo = {
		"id":"<?php echo $user['account_id'];?>",
		"name":"<?php echo $user['nickname'];?>",
		"avatar":"<?php echo $user['headimgurl'];?>",
		"card":0,
	}

	$scope.redPackage = {
		"isReceive":"<?php echo $red_package['is_receive'];?>",
		"content":"<?php echo $red_package['content'];?>",
		"count":"<?php echo $red_package['ticket_count'];?>",
		"sendName":"<?php echo $red_package['nickname'];?>",
		"sendAvatar":"<?php echo $red_package['headimgurl'];?>",
		"receiveName":"<?php echo $red_package['receive_nickname'];?>",
		"receiveAvatar":"<?php echo $red_package['receive_headimgurl'];?>",
		"receiveTime":"<?php echo $red_package['receive_time'];?>",
		"showTime":"",
		"code":"<?php echo $red_code;?>",
	}

	if ($scope.redPackage.isReceive == 0) {
		$scope.redPackage.receiveAvatar = $scope.userInfo.avatar;
		$scope.redPackage.receiveName = $scope.userInfo.name;
	}
    
	$scope.userInfo.card = "<?php echo $card;?>";
	
	var socketStatus = 0;
	$(".main").show();			
	$("#loading").hide();
	
	
	$scope.activity = new Array();	
	$scope.isShowAlert = false;
	$scope.alertType = 0;
	$scope.alertText = "";
	
	$scope.cardNum = Number($scope.userInfo.card);
	$scope.number = 0; //输入数量
	$scope.inputNumber = null;  //输入框数字

	if ($scope.cardNum === null 
		|| $scope.cardNum === undefined
		|| isNaN($scope.cardNum)) {
		$scope.cardNum = 0;
	}

	setTimeout(function () {
		if ($scope.redPackage.isReceive == 1) {
			$("#notopen,.copy_link,.goback").hide();
			$("#ropen,.goback_home").show();
		} else {
			$("#notopen,.copy_link,.goback").show();
			$("#ropen,.goback_home").hide();
		}
    	
    }, 1);

    $scope.formatShowTime = function () {

    	var newDate = new Date($scope.redPackage.receiveTime);
        newDate.setTime($scope.redPackage.receiveTime * 1000);
        var Month = (newDate.getMonth() + 1 < 10 ? '0' + (newDate.getMonth() + 1) : newDate.getMonth() + 1);
        var Day = newDate.getDate() + ' ';
        var Day = newDate.getDate() < 10 ? '0' + newDate.getDate() : newDate.getDate();
        var hour = newDate.getHours() < 10 ? '0' + newDate.getHours() : newDate.getHours();
        var min = newDate.getMinutes() < 10 ? '0' + newDate.getMinutes() : newDate.getMinutes();

        $scope.redPackage.showTime = Month + '-' + Day + '    ' + hour + ':' + min ;
    }

    $scope.formatShowTime();

	$scope.receiveRedPackage = function () {
		$http({
    		url:'<?php echo $base_url;?>ay/receiveRP', 
    		method:'POST',
    		header:{'Content-Type':'application/x-www-form-urlencoded'},
    		data:{
    			'account_id':"<?php echo $user['account_id'];?>",
    			'red_code':$scope.redPackage.code,
    		}})
    	.success(function(data, header, config, status) {

            var timestamp = Date.parse(new Date());
            timestamp = timestamp / 1000;
    	    $scope.redPackage.receiveTime = timestamp;
    	    $scope.formatShowTime();

    		setTimeout(function () {
    			$(".btnOpen").removeClass('transf');

    			if (data.result == 0) {
                    $("#ropen,.goback_home").show();
                    $("#notopen,.copy_link,.goback").hide();
    	        } else {
                    window.location.reload();
    	    	    //alert(data.result_message);
    	        }
    		}, 500);
    	})
    	.error(function(data, header, config, status) {
    		$(".btnOpen").removeClass('transf');
            window.location.reload();
    		//alert(data.result_message);
    	});
	}
    
    // <div  style="position: absolute; height: {{width * 1.02 / 1.8 / 5.1}}px; width: {{width * 1.02 / 1.8}}px; left: {{(rpWidth - width * 1.02 / 1.8) / 2}}px; top: {{rpHeight * 0.45}}px;" ng-click="clickOpenRedPackage()">
    //         <img src="<?php echo $base_url;?>files/images/redpackage/redpackage_get_btn.png" style="width: 100%;position: relative;height:100%; " />
    //     </div> 

    setTimeout(function() {
            var tempDiv = document.getElementById('openImg');
            if (tempDiv) {
                tempDiv.addEventListener('touchstart', function(event) {
                    $('#openImg').animate({top:"-10%",left:"-10%",width:"120%",height:"120%"},30);
                }, false);

                tempDiv.addEventListener('touchend', function(event) {
                    $('#openImg').animate({top:"0%",left:"0%",width:"100%",height:"100%"},30);
                }, false);
            }
        }, 100);

    $scope.clickOpenRedPackage = function() {
        $(".btnOpen").addClass('transf');
        //$('#openImg').animate({top:"-10%",left:"-10%",width:"120%",height:"120%"},30);
        $scope.receiveRedPackage();
        setTimeout(function() {
            //$('#openImg').animate({top:"0%",left:"0%",width:"100%",height:"100%"},30);
        }, 0);
    }
    $scope.goback = function() {
        window.history.go(-1);
    }
    $scope.goHall = function () {
	    window.location.href = '/f/ym';
    }
})


</script>

</head>
<link rel="stylesheet" type="text/css" href="<?php echo $image_url;?>files/css/activity.css">

<body ng-controller="myCtrl" style="background: #000;" >
<div style="position: fixed;width:100%;height:100%;top:0;left:0;background: #000" id="loading">
	<img src="<?php echo $image_url;?>files/images/common/loading.gif" style="top: 40%;position: absolute;left: 50%;margin-left: -45px;margin-top: -45px;" />
</div>
<div class="main" style="display: none;">
	
	<img src="<?php echo $image_url;?>files/images/redpackage/redpackage_bg.jpg" style="width: 100%;position: relative;height:100%" usemap="#planetmap" /> 
    <div class="top_btn">
        <div class="goback" ng-click="goback()"></div>
        <div class="goback_home" ng-click="goHall()"></div>
        <div class="copy_link" data-clipboard-text="Copy Me!"></div> 
    </div>
    <div id="notopen" style="position: absolute; height: {{rpHeight}}px; width: {{rpWidth}}px; top: {{rpTop}}px; left: {{rpLeft}}px; display: none;">
        <img src="<?php echo $image_url;?>files/images/redpackage/redpackage_new.png" style="width: 100%; height:100%;" />
        
        <div class="user" style="position: absolute; height: {{width * 0.181}}; top: {{rpTop - rpTop * 0.76}}px; width: 100%; left: 0px;">
            <img ng-src="{{redPackage.sendAvatar}}" class="avatar" style="position: absolute; width:{{width*0.132}}px; height:{{width*0.132}}px; margin-left:{{(rpWidth - width * 0.132) / 2}}px; margin-top:{{width*0.02}}px; " />      
        </div>

        <p style="position: absolute; color: rgb(255,227,104); width: 90%; left: 5%; top: {{rpHeight * 0.70}}; height: auto; text-align: center; font-size: 11pt; word-wrap: break-word; word-break: break-all;">{{redPackage.sendName}}<br />给你发了一个房卡包
        </p>
        
        <div class="btnOpen"  style="position: absolute; height: {{width * 1.02 / 4.5}}px; width: {{width * 1.02 / 4.5}}px; left: {{(rpWidth - width * 1.02 / 4.5) / 2}}px; top: {{rpHeight * 0.42}}px;" ng-click="clickOpenRedPackage()">
            <img src="<?php echo $image_url;?>files/images/redpackage/rp_get.png" style="width: 100%;position: relative;height:100%; transform: rotate({{img.rotate}}deg);" />
        </div> 
        <!-- <div  style="position: absolute; height: {{width * 1.02 / 1.8 / 5.1}}px; width: {{width * 1.02 / 1.8}}px; left: {{(rpWidth - width * 1.02 / 1.8) / 2}}px; top: {{rpHeight * 0.45}}px;" ng-click="clickOpenRedPackage()">
            <img id="openImg" src="<?php echo $image_url;?>files/images/redpackage/redpackage_get_btn.png" style="width: 100%;position: relative;height:100%; " />
        </div>  -->

        <div style=" position: absolute; left: 22px; right: 22px; top: {{height - 160}}px;" >
        <p style="text-align: center; color: #fff; font-size: 11pt"></p>
        </div>
    </div>

    <div id="ropen" style="position: absolute; height: {{rpHeight}}px; width: {{rpWidth}}px; top: {{rpTop}}px; left: {{rpLeft}}px; display: none;">
        <img src="<?php echo $image_url;?>files/images/redpackage/redpackage_receive_new.png" style="width: 100%;height:100%" />

        <div class="user" style="position: absolute; height: {{width * 0.181}}; top: {{rpTop - rpTop * 0.76}}px; width: 100%; left: 0px;">
            <img ng-src="{{redPackage.sendAvatar}}" class="avatar" style="position: absolute; width:{{width*0.132}}px; height:{{width*0.132}}px; margin-left:{{(rpWidth - width * 0.132) / 2}}px; margin-top:{{width*0.02}}px; " />      
        </div>
        
        <p style="position: absolute; color: rgb(255,227,104); width: 90%; left: 5%;top: {{rpHeight * 0.62}} ; text-align: center; font-size: 11pt;">{{redPackage.sendName}}的房卡包</p>
        
       
        <img ng-src="{{redPackage.receiveAvatar}}" ng-model="redPackage.receiveAvatar" class="avatarReceiver" style="position: absolute; top: {{rpHeight * 0.8}}px; left: {{rpX}}px; width:{{width * 0.1}}; height:{{width * 0.1}};  border-radius: 4px" />
        <p style="font-size: 11pt; position: absolute; color: black; text-align: left; top: {{rpHeight * 0.8}}px; left: {{receiveNameX}}px; width: {{receiveNameWidth}}px; height: auto;" ng-model="redPackage.receiveName">
            <span style="color: black; word-wrap: break-word; word-break: break-all; width: {{receiveNameWidth}};">{{redPackage.receiveName}}</span>
            <br />
            <span style="color: lightgray">{{redPackage.showTime}}</span>
        </p>

        <p style="font-size: 11pt; position: absolute; color: black; top: {{rpHeight * 0.8}}px; left: {{receiveNameX + receiveNameWidth + receiveNameOffset}}px; width: {{receiveCountWidth}}px; text-align: right; word-wrap: break-word; word-break: break-all;">{{redPackage.count}}张房卡</p>
    </div>
</div>

</body>
</html>
