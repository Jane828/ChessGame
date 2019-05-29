<html ng-app="app">
<head>
<meta charset="utf-8" >
<meta name="viewport" content="width=device-width,initial-scale=1.0, minimum-scale=1.0, maximum-scale=1.0, user-scalable=no"/>
<meta name="format-detection" content="telephone=no" />
<meta name="msapplication-tap-highlight" content="no" />
<title><?php echo $invite_nickname;?>的邀请函</title>

<script type="text/javascript" src="<?php echo $file_url;?>files/js/fastclick.js?_version=<?php echo $front_version;?>"></script>
<link rel="stylesheet" type="text/css" href="<?php echo $file_url;?>files/css/activity.css?_version=<?php echo $front_version;?>">
<link rel="stylesheet" type="text/css" href="<?php echo $file_url;?>files/css/alert.css?_version=<?php echo $front_version;?>">

<script type="text/javascript">

    window.addEventListener('load', function() {
        FastClick.attach(document.body);
    }, false);

    var newNum = "";
    var per = window.innerWidth / 530;
    var globalData = {
      //	"baseUrl":"<?php echo $base_url;?>",
      "baseUrl":"/",
        "openId": "<?php echo $open_id;?>",
        "fileUrl": "<?php echo $file_url;?>",
		"imageUrl": "<?php echo $image_url;?>",
        "shareIcon": "<?php echo $share_icon;?>",
        "shareUrl": "<?php echo $share_url;?>",
        "inviteName":'<?php echo $invite_nickname;?>',
        "userCode":'<?php echo $code;?>',
        "inviteAvatar":'<?php echo $invite_headimgurl;?>',
        "isOwner":'<?php echo $is_owner;?>',
        "inviteStatus":'<?php echo $invite_status;?>',
        "qrUrl":'<?php echo "www.baidu.com";?>',
        "wxName":'<?php echo "nana";?>',
    };
    var userData = {
        "accountId": "<?php echo $user['account_id'];?>",
        "nickname": "<?php echo $user['nickname'];?>",
        "avatar": "<?php echo $user['headimgurl'];?>",
    };
    var configData = {
        "appId": "<?php echo $config_ary['appId'];?>",
        "timestamp": "<?php echo $config_ary['timestamp'];?>",
        "nonceStr": "<?php echo $config_ary['nonceStr'];?>",
        "signature": "<?php echo $config_ary['signature'];?>",
    };

</script>

</head>

<body ng-controller="myCtrl" style="background: #000;" >
<div style="position: fixed;width:100%;height:100%;top:0;left:0;background: #000" id="loading">
	<img src="<?php echo $image_url;?>files/images/common/loading.gif" style="top: 40%;position: absolute;left: 50%;margin-left: -45px;margin-top: -45px;" />
</div>
<div class="top_btn" style="z-index:10;">
    <!-- <div class="goback" onClick="window.history.go(-1);"></div> -->
    <div class="goback" onClick="window.location.href = globalData.baseUrl + 'f/fri'"></div>
    <div class="copy_link" data-clipboard-text="Copy Me!"></div> 
</div>
<div class="main" style="display: none;">

	<img src="<?php echo $image_url;?>files/images/activity/union_bg.jpg"  style="top: 0;left: 0;width: 100%;height: 100%;position: relative;">

    <!-- 提示框 -->
        <div class="alert" id="valert" ng-show="isShowAlert" >
            <div class="alertBack"></div>
            <div class="mainPart">
                <div class="backImg">
                    <div class="blackImg"></div>
                </div>
                <div class="alertText">{{alertText}}</div>
                <div ng-show="alertType==3">
                    <div class="buttonLeft" ng-click="closeAlert()"></div>
                    <div class="buttonRight" ng-click="closeAlert()"></div>
                </div>
                <div ng-show="alertType==7">
                    <div class="buttonMiddle" ng-click="closeAlert()"></div>
                </div>
                <div ng-show="alertType==8">
                </div>
            </div>
        </div>

        <div style="position: absolute;top: 13vh;left: 50%;margin-left: -25vh;height: 80vh;width: 50vh;" ng-if="inviteStatus==0">
            <img src="<?php echo $image_url;?>files/images/activity/union_card.jpg" style="position: absolute;top: 0;left: 1vh;width: 48vh;height: 100%;">
            <img src="<?php echo $image_url;?>files/images/activity/union_card_bottom.png" style="position: absolute;bottom: 0;width: 50vh;height: 35.25vh;">
            <div class="imgOpen" style="position: absolute;bottom: 10vh;left: 50%;margin-left: -10vh;width: 20vh;height: 20vh;backface-visibility:hidden;" ng-click="clickJoin()">
                <img  ng-src="{{joinImg}}" style="position: absolute;width: 20vh;height: 20vh;transform: rotate({{img.rotate}}deg);" >
            </div>

            <img ng-src="{{inviteAvatar}}" style="position: absolute;top: 6vh;left: 50%;margin-left: -6vh;width: 12vh;height: 12vh;border-radius: 6vh;">
            <div style="position: absolute;top: 19vh;width: 100%;height: 5vh;line-height: 5vh;text-align: center;">
                <p style="position: absolute;width: 100%;font-size: 2.5vh">
                    <span style="color: orange;">{{inviteName}}</span>
                    <!-- <span style="color: black">邀请你加入</span> -->
                </p>
            </div>

            <div style="position: absolute;top: 25vh;width: 100%;height: 8vh;line-height: 8vh;font-size: 3vh;color: orange;text-align: center;">
                邀请你添加好友
            </div>

            <div style="position: absolute;top: 35vh;left: 4vh;width: 42vh;height: 14vh;line-height: 3.5vh;font-size: 2.5vh;text-align: center;">
            </div>

        </div>

        <div style="position: absolute;top: 13vh;left: 50%;margin-left: -25vh;height: 80vh;width: 50vh;" ng-if="inviteStatus==1">
            <img  src="<?php echo $image_url;?>files/images/activity/union_card.jpg" style="position: absolute;top: 0;left: 0;width: 50vh;height: 100%;">
            <img src="<?php echo $image_url;?>files/images/activity/union_finish.png" style="position: absolute;top: 12vh;left: 50%;margin-left: -6vh;width: 12vh;height: 12vh;">
            <div style="position: absolute;top: 25vh;width: 100%;height: 5vh;text-align: center;font-size: 3vh;color: orange;font-weight: bold;">
                申请已发出，待好友同意
            </div>
        </div>
        <div style="position: absolute;top: 13vh;left: 50%;margin-left: -25vh;height: 80vh;width: 50vh;" ng-if="inviteStatus==2">
            <img  src="<?php echo $image_url;?>files/images/activity/union_card.jpg" style="position: absolute;top: 0;left: 0;width: 50vh;height: 100%;">
            <img src="<?php echo $image_url;?>files/images/activity/union_finish.png" style="position: absolute;top: 12vh;left: 50%;margin-left: -6vh;width: 12vh;height: 12vh;">
            <div style="position: absolute;top: 25vh;width: 100%;height: 5vh;text-align: center;font-size: 3vh;color: orange;font-weight: bold;">
                加入成功
            </div>
        </div>


</div>

</body>

<script type="text/javascript" src="<?php echo $file_url;?>files/js/jquery-1.9.1.min.js"></script>
<script type="text/javascript" src="<?php echo $image_url;?>files/js/jquery.copy.js"></script>
<script type="text/javascript" src="<?php echo $file_url;?>files/js/angular.min.js"></script>
<script src="https://res.wx.qq.com/open/js/jweixin-1.0.0.js" ></script>
<script type="text/javascript" src="<?php echo $base_url;?>files/js/inviteDetail.js?_version=<?php echo $front_version;?>"></script>

<!-- script type="text/javascript" src="<?php echo $file_url;?>files/js/guild/inviteDetail/inviteDetail-1.0.1.js"></script -->
<script>
    $(function(){
        $('.copy_link').copy({
            copy: function(_this){
                return window.location.href;
            },
            afterCopy: function(res){
                var appElement = document.querySelector('[ng-controller=myCtrl]');
                //获取$scope变量
                var $scope = angular.element(appElement).scope();
                if(res==true){
                    $scope.showAlert(7,'请前往微信粘贴并发送链接！');
                }else{
                    $scope.showAlert(7,'链接复制失败！');
                }
            }
        });
    })
</script>

</html>
