<html ng-app="app">
<head>
<meta charset="utf-8" >
<meta name="viewport" content="width=device-width,initial-scale=1.0, minimum-scale=1.0, maximum-scale=1.0, user-scalable=no"/>
<meta name="format-detection" content="telephone=no" />
<meta name="msapplication-tap-highlight" content="no" />
<title>房卡包</title>

<script type="text/javascript" src="<?php echo $image_url;?>files/js/jquery-1.9.1.min.js"></script>
<script type="text/javascript" src="<?php echo $image_url;?>files/js/angular.min.js"></script>
<script src="https://res.wx.qq.com/open/js/jweixin-1.0.0.js" ></script>
<script type="text/javascript" src="<?php echo $image_url;?>files/js/fastclick.js"></script>
<!--
<script type="text/javascript" src="<?php echo $image_url;?>files/cjs/roomRedPackage.js"></script>
-->
<style>
    input::-webkit-input-placeholder{
        color: #fff;
    }
    input::-moz-placeholder{   /* Mozilla Firefox 19+ */
        color: #fff;
    }
    input:-moz-placeholder{    /* Mozilla Firefox 4 to 18 */
        color: #fff;
    }
    input:-ms-input-placeholder{  /* Internet Explorer 10-11 */
        color: #fff;
    }
    .createPackage{
        height: 45px;
        width: 90%;
        position: fixed;
        top: 300px;
        right: 5%;
        background: url('../../files/images/me/mark_package.png') no-repeat center;
        background-size: auto 45px;
    }
    .back{
        position: fixed;
        right: 20px;
        top: 20px;
        width: 45px;
        height: 45px;
        background: url("../../files/images/me/back.png") no-repeat center;
        background-size: 100%;
    }
</style>
<script type="text/javascript">

$(function () {
	$("#redpackage_bg").css('height',$(window).height())
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
    	wx.hideOptionMenu();
    });
     
});

var app = angular.module('app',[])

app.controller("myCtrl", function($scope,$http) {	
	$scope.width = window.innerWidth;
	$scope.height = window.innerHeight;

	$scope.userInfo = {
		"id":"<?php echo $user['account_id'];?>",
		"name":"<?php echo $user['nickname'];?>",
		"avatar":"<?php echo $user['headimgurl'];?>",
		"card":0,
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

    //向服务器请求房卡数量
	$scope.getRoomTicket = function() {
		$http({
    		url:'<?php echo $base_url;?>f/getRoomTicket', 
    		method:'POST',
    		header:{'Content-Type':'application/x-www-form-urlencoded'},
    		data:{'account_id':"<?php echo $user['account_id'];?>"}})
    	.success(function(data, header, config, status) {
    		if (data.data.ticket_count === null ||
    			data.data.ticket_count === undefined) {
    			alert(data.data.ticket_count);
    	    } else {
    	    	$scope.cardNum = Number(data.data.ticket_count);
    	    }
    		
    	})
    	.error(function(data, header, config, status) {
    		alert(data.result_message);
    	});
	}

	//输入框数字改变
	$scope.changeNumber = function() {
		if ($scope.inputNumber > $scope.cardNum) {
			$scope.inputNumber = $scope.cardNum;
		}

		$scope.number = $scope.inputNumber;

        console.log($scope.inputNumber);

		if ($scope.number === undefined || $scope.number === null) {
			$scope.number = 0;
		} else {
			//$scope.inputNumber = $scope.number;
		}
	}

    $scope.createRedPackage = function() {

    	if ($scope.number === undefined 
    		|| $scope.number === null 
    		|| isNaN($scope.number) 
    		|| $scope.number <= 0) {
    		alert('请输入正确的房卡数');
    	} else {
    		$http({
    			url:'<?php echo $base_url;?>ay/cRP',
    			method:'POST',
    			header:{'Content-Type':'application/x-www-form-urlencoded'},
    			data:{
    				'account_id':$scope.userInfo.id,
    				'ticket_count':$scope.number,
    				'content':'恭喜发财',
    			}
    		}).success(function(data, header, config, status) {
    			//var rpCode = data.result_message;
    			if (data.result == 0 ) {
    				$scope.cardNum = $scope.cardNum - $scope.inputNumber;
    				//alert(data.result_message + ' ' + data.data.code + '即将为你跳转到界面');
    				window.location.href = "<?php echo $base_url;?>ay/rpD?red_code=" + data.data.code;
    			} else {
    				alert(data.result_message);
    			}
    			
    		}).error(function(data, header, config, status) {
    			alert(data.result_message);
    		});
    	}
    	
    }

    $scope.gotoMyRedPackage = function() {
    	//alert('你点击了我的');
    	window.location.href = "<?php echo $base_url;?>ay/myRP";
    }

    $scope.goBack = function() {
	    window.history.go(-1);
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
	
	<img id="redpackage_bg" src="<?php echo $image_url;?>files/images/redpackage/redpackage_bg.jpg" style="width: 100vw;position: relative;height:100vh" usemap="#planetmap" /> 
	
	<div style="height: 20px; position: fixed; top: 45px; left: 20px;">
		<p style="font-size: 16px; line-height: 20px; color: orange">你的房卡：{{cardNum}} 张</p>
	</div>
    <div style="height: 44px; position: fixed; top: 100px; left: 20px; right: 20px; color: #fff; background-color: #6E477E; border: 1px solid #fff; border-radius: 4px; overflow: hidden; ">
        <label style="top: 10px; left: 10px; width: 80px;height: 100%; font-size: 13pt; text-align: left; position: absolute;">放入房卡</label>
        <input id="pnumber" type="number" ng-maxlength=9 name="packagenumber" placeholder=0
        ng-model="inputNumber" ng-change="changeNumber()" style=" background: #6E477E; color: #fff; left: 90px; width: {{width - 170}}px; height: 100%; position: absolute; font-size: 13pt;text-align: right;"></input>
        <label style="position: absolute; top: 10px; height: 100%; right: 10px;width: 25px; font-size: 13pt; text-align: right;">张</label>
    </div>
    <div style="position: fixed; top: 194px; left: 22px; right: 22px; height: 80px;">
        <p style="text-align: center;"><span style="color: orange; font-size: 40pt">{{number}}</span><span style="color: orange; font-size: 13pt">张</span></p>
    </div>

    <div class="createPackage" ng-click="createRedPackage()"></div>
    <div class="back" ng-click="goBack()"></div>
	<div style="position: fixed; left: 22px; right: 22px; top: {{height - 60}}px; text-align: center;" ng-click="gotoMyRedPackage()">
		<p style="color: #6a94fc; font-size: 12pt;text-align: center; width: 100%;">我的房卡记录</p>
	</div>
</div>

</body>
</html>
