<html ng-app="app">
<head>
<meta charset="utf-8" >
<meta name="viewport" content="width=device-width,initial-scale=1.0, minimum-scale=1.0, maximum-scale=1.0, user-scalable=no"/>
<meta name="format-detection" content="telephone=no" />
<meta name="msapplication-tap-highlight" content="no" />
<title>我的房卡</title>

<script type="text/javascript" src="<?php echo $image_url;?>files/js/jquery-1.9.1.min.js"></script>
<script type="text/javascript" src="<?php echo $image_url;?>files/js/bscroll.js"></script>
<script type="text/javascript" src="<?php echo $image_url;?>files/js/angular.min.js"></script>
<script src="https://res.wx.qq.com/open/js/jweixin-1.0.0.js" ></script>
<script type="text/javascript" src="<?php echo $image_url;?>files/js/fastclick.js"></script>

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
        wx.hideOptionMenu();
    });;

});

var app = angular.module('app',[])

app.controller("myCtrl", function($scope,$http) {
	$scope.width = window.innerWidth;
	$scope.height = window.innerHeight;
  //  $scope.baseURL = '<?php echo $base_url;?>';
$scope.baseURL = '/';
	$scope.userInfo = {
		"id":"<?php echo $user['account_id'];?>",
		"name":"<?php echo $user['nickname'];?>",
		"avatar":"<?php echo $user['headimgurl'];?>",
		"card":0,
	}

	$scope.userInfo.card = "<?php echo $card;?>";

	$scope.outRedPackages = new Array();
    $scope.outCodes = new Array();
	$scope.receiveRedPackages = new Array();
    $scope.receiveCodes = new Array();
    $scope.outBScroll = null;
    $scope.canLoadOut = true;
    $scope.receiveBScroll = null;
    $scope.canLoadReceive = true;

    $scope.outPage = 0 ;
    $scope.outTotalPage = 1;
    $scope.receivePage = 0;
    $scope.receiveTotalPage = 1;

	$(".main").show();
	$("#loading").hide();

    $scope.initOutScroll = function () {
        setTimeout(function() {
            $scope.$apply();

            if (!$scope.outBScroll) {
                $scope.outBScroll = new BScroll(document.getElementById('out-box'), {
                    startX: 0,
                    startY: 0,
                    scrollY: true,
                    scrollX: false,
                    click: true,
                    probeType: 1,
                    bounce:false,
                });

                $scope.outBScroll.on('touchend', function (position) {
                    if(position.y <= (this.maxScrollY + 30) && $scope.canLoadOut) {
                        $scope.canLoadOut= false;
                        $scope.loadMoreOut();
                    }
                });
            } else {
                $scope.outBScroll.refresh();
            }
        }, 10);
    }

    $scope.initReceiveScroll = function () {
        setTimeout(function() {
            $scope.$apply();

            if (!$scope.receiveBScroll) {
                $scope.receiveBScroll = new BScroll(document.getElementById('receive-box'), {
                    startX: 0,
                    startY: 0,
                    scrollY: true,
                    scrollX: false,
                    click: true,
                    probeType: 1,
                    bounce:false,
                });


                $scope.receiveBScroll.on('touchend', function (position) {
                    if(position.y <= (this.maxScrollY + 30) && $scope.canLoadReceive) {
                        $scope.canLoadReceive = false;
                        $scope.loadMoreReceive();
                    }
                });

            } else {
                $scope.receiveBScroll.refresh();
            }
        }, 10);
    }

	var formatShowTime = function (time) {

    	var newDate = new Date();
        newDate.setTime(time * 1000);
        var year = newDate.getFullYear();
        var month = (newDate.getMonth() + 1 < 10 ? '0' + (newDate.getMonth() + 1) : newDate.getMonth() + 1);
        var day = newDate.getDate() < 10 ? '0' + newDate.getDate() : newDate.getDate() ;
        var hour = newDate.getHours() < 10 ? '0' + newDate.getHours() : newDate.getHours() ;
        var min = newDate.getMinutes() < 10 ? '0' + newDate.getMinutes() : newDate.getMinutes();

        var showTime = year + '-' + month + '-' + day + '        ' + hour +':' + min ;
        return showTime;
    }

    $scope.loadMoreOut = function () {
        if ($scope.outPage >= $scope.outTotalPage) {
            $scope.canLoadOut = true;
            return;
        }

        $scope.getOutRedPackage();
    	console.log('~~~~~loadMoreOut~~~~~');
    }

    $scope.loadMoreReceive = function () {
        if ($scope.receivePage >= $scope.receiveTotalPage) {
            $scope.canLoadReceive = true;
            return;
        }

        $scope.getReceiveRedPackage();
        console.log('~~~~~loadMoreReceive~~~~~');
    }

	$scope.getOutRedPackage = function () {
		$http({
			url:$scope.baseURL + 'ay/sRP',
			method:'POST',
			header:{'Content-Type':'application/x-www-form-urlencoded'},
			data:{
				'account_id':$scope.userInfo.id,
				'page':$scope.outPage + 1,
			}})
		.success(function (data, header, config, status) {

            //请求成功页面加1
            $scope.outTotalPage = data.sum_page;
            $scope.outPage = $scope.outPage + 1;
			for (x in data.data) {
                var code = data.data[x].code;

                if ($scope.outCodes.indexOf(code) < 0) {
                    var tmpTime = formatShowTime(data.data[x].create_time);
                    var content = '未领取';
                    var color = 'orange';
                    if (data.data[x].is_receive == 1) {
                        content = '已领取';
                        color = 'lightgray';

                        if (data.data[x].is_return == 1) {
                            content = '已过期退回';
                        }
                    }

                    var value = {
                        'name': '房卡包',
                        'content':content,
                        'time':tmpTime,
                        'count':data.data[x].ticket_count,
                        'origin':data.data[x],
                    }

                    $scope.outCodes.push(code);
                    $scope.outRedPackages.push(value);
                }

			}

            setTimeout(function () {
                $(".outDiv").show();
            }, 1);

            $scope.initOutScroll();

			console.log($scope.outRedPackages);
		}).error( function (data, header, config, status) {
			console.log(data.result_message);
		}) ;
	}

	$scope.getReceiveRedPackage = function () {
		$http({
			url:$scope.baseURL + 'ay/rRP',
			method:'POST',
			header:{'Content-Type':'application/x-www-form-urlencoded'},
			data:{
				'account_id':$scope.userInfo.id,
				'page':$scope.receivePage + 1,
			}})
		.success(function (data, header, config, status) {

            //请求成功页数加1
            $scope.receivePage = $scope.receivePage + 1;
            $scope.receiveTotalPage = data.sum_page;

			for (x in data.data) {
                var code = data.data[x].code;

                if ($scope.receiveCodes.indexOf(code) < 0) {
                    var tmpTime = formatShowTime(data.data[x].receive_time);
                    var content = '已领取';
                    var value = {
                    'name': data.data[x].nickname,
                    'content':content,
                    'time':tmpTime,
                    'count':data.data[x].ticket_count,
                    'origin':data.data[x],
                    }

                    $scope.receiveCodes.push(code);
                    $scope.receiveRedPackages.push(value);
                }

			}

            $scope.initReceiveScroll();
            $scope.canLoadReceive = true;

			console.log($scope.receiveRedPackages);
		}).error( function (data, header, config, status) {
            $scope.canLoadReceive = true;
			console.log(data.result_message);
		}) ;
	}

	$scope.getOutRedPackage();
	$scope.getReceiveRedPackage();

    //切换到发出记录
    $scope.clickOutRedPackage = function () {
    	$(".outDiv").show();
    	$(".receiveDiv").hide();

        $("#selectTab").css({"left": $scope.width * 0.2 + 'px',"border-radius": "23px 0 0 23px"});
        // $("#outRP").css("color", "white");
    	// $("#reveiveRP").css("color", "black");

        $scope.initOutScroll();
    }

    //切换到收取记录
    $scope.clickReceiveRedPackage = function () {
    	$(".receiveDiv").show();
    	$(".outDiv").hide();

        $("#selectTab").css({"left": $scope.width * 0.5 + 'px',"border-radius": "0 23px 23px 0",});
        // $("#outRP").css("color","black");
    	// $("#reveiveRP").css("color","white");

        $scope.initReceiveScroll();
    }

     $scope.clickCell = function (data) {
        window.location.href = "<?php echo $base_url;?>ay/rpD?red_code=" + data.code;
     }

})

// app.directive('whenScrolled', function() {
//   return function(scope, elm, attr) {
//     var raw = elm[0];
//     elm.bind('scroll', function() {
//       if (raw.scrollTop + raw.offsetHeight >= raw.scrollHeight) {
//         scope.$apply(attr.whenScrolled);
//       }
//     });
//   };
// });

</script>

</head>
<link rel="stylesheet" type="text/css" href="<?php echo $image_url;?>files/css/activity.css">

<body ng-controller="myCtrl" style="background: #111548;" >
<div style="position: fixed;width:100%;height:100%;top:0;left:0;background: #000" id="loading">
	<img src="<?php echo $image_url;?>files/images/common/loading.gif" style="top: 40%;position: absolute;left: 50%;margin-left: -45px;margin-top: -45px;" />
</div>
<div class="main" style="display: none;">

    <div id="out-box" class="outDiv" style="position: fixed;top: 74px; width: 100%; height: {{height - 84}}px; overflow: scroll; display: none;">
        <div id="out-scroll" style="position: relative;width: 100%;">
            <div ng-model="outRedPackages" ng-repeat="record in outRedPackages" style="overflow: hidden; height: 80px;" ng-click="clickCell(record.origin)">
                <div style="height: 80px;background: #281C4E;margin-top:12px;">
                    <label style="position: relative; margin-left: {{width * 0.08}}px; top: 10px; width: {{width * 0.42}}px; color: #F3EFFC; overflow: hidden; display: block; height: 24px;"> {{record.name}}</label>
                    <label style="position: relative; margin-left: {{width * 0.5}}px; margin-top: -14px; width: {{width * 0.42}}px; text-align: right; color: #F3EFFC; overflow: hidden; display: block; height: 24px;"> {{record.count}}张</label>
                    <label style="position: relative; top: 5px; margin-left: {{width * 0.08}}px; width: {{width * 0.42}}px; color: #C8B9EE; overflow: hidden; display: block; height: 24px;"> {{record.time}}</label>
                    <label ng-if="record.content == '未领取'" style="position: relative; top: -20px; margin-left: {{width * 0.5}}px; width: {{width * 0.42}}px; text-align: right; color: #EFC51F; overflow: hidden; display: block; height: 24px;"> {{record.content}}</label>
                    <label ng-if="record.content == '已领取' || record.content == '已过期退回'" style="position: relative; top: -20px; margin-left: {{width * 0.5}}px; width: {{width * 0.42}}px; text-align: right; color: #C8B9EE; overflow: hidden; display: block; height: 24px;"> {{record.content}}</label>
                    <!-- <label style="position: relative; margin-left: 0px; top: -11; background-color: lightgray; height: 0.8px; width: 100%; overflow: hidden; display: block;"></label> -->
                </div>
            </div>
        </div>
    </div>

    <div id="receive-box" class="receiveDiv" style="position: fixed;top: 74px; width: 100%; height: {{height - 84}}px; overflow: scroll; display: none;"  when-scrolled="loadMoreReceive()">
        <div id="receive-box" style="position: relative;width: 100%">
            <div  ng-model="receiveRedPackages" ng-repeat="value in receiveRedPackages" style="overflow: hidden; height: 80px;">
                <div style="height: 80px;background: #281C4E;margin-top:12px;">
                    <label style="position: relative; margin-left: {{width * 0.08}}px; top: 10px; width: {{width * 0.5}}px; color: #F3EFFC; overflow: hidden; display: block; height: 24px;text-overflow:ellipsis;white-space:nowrap"> {{value.name}}</label>
                    <label style="position: relative; margin-left: {{width * 0.5}}px; margin-top: -14px; width: {{width * 0.42}}px; text-align: right; color: #F3EFFC; overflow: hidden; display: block; height: 24px;"> {{value.count}}张</label>
                    <label style="position: relative; top: 5px; margin-left: {{width * 0.08}}px; width: {{width * 0.42}}px; color: #C8B9EE; overflow: hidden; display: block; height: 24px;"> {{value.time}}</label>
                    <label style="position: relative; top: -20px; margin-left: {{width * 0.5}}px; width: {{width * 0.42}}px; text-align: right; color:#C8B9EE; overflow: hidden; display: block; height: 24px;"> {{value.content}}</label>
                    <!-- <label style="position: relative; margin-left: 0px; top: -11; background-color: lightgray; height: 0.8px; width: 100%; overflow: hidden; display: block;"></label> -->
                </div>
            </div>
        </div>
    </div>

    <div style="position: fixed; height: 44px; top: 20px; left: {{width * 0.2}}px; width: {{width * 0.6}}px; border-color: #281C4E; border-style: solid; border-radius: 30px; border-width: 2px; overflow: hidden;">
	</div>

	<div id="selectTab" style="position: fixed; top: 20px; left:{{width * 0.2}}px; width: {{width * 0.3}}px; height: 46px; background-color: orange;border-radius: 23px 0 0 23px; z-index: 1"></div>
	<label id="outRP" style="font-size: 13pt; text-align: center; color: white; position: fixed; top: 32px; left: {{width * 0.2}}px; width: {{width * 0.3}}px; z-index: 2" ng-click="clickOutRedPackage()">发出房卡</label>
    <label id="reveiveRP" style="font-size: 13pt; text-align: center; color: white; position: fixed; top: 32px; left: {{width * 0.2 + width * 0.3}}px; width: {{width * 0.3}}px; z-index: 2" ng-click="clickReceiveRedPackage()">收到房卡</label>

    <div class="swiper-pagination" style="display: none;"></div>
    <div class="top_btn" style="top:20px;padding: 0 14px;z-index:100;width:auto;">
        <div class="goback" onClick="window.history.go(-1);"></div>
    </div>

</div>

</body>
</html>
