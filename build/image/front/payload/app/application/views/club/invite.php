<html ng-app="app">
<head>
    <meta charset="utf-8" >
    <meta name="viewport" content="width=device-width,initial-scale=1.0, minimum-scale=1.0, maximum-scale=1.0, user-scalable=no"/>
    <meta name="format-detection" content="telephone=no" />
    <meta name="msapplication-tap-highlight" content="no" />
    <title>邀请函</title>

    <script type="text/javascript" src="<?php echo $image_url;?>files/js/fastclick.js"></script>
    <link rel="stylesheet" type="text/css" href="<?php echo $base_url;?>files/club/css/activity.css">
    <link rel="stylesheet" type="text/css" href="<?php echo $base_url;?>files/club/css/alert.css">

    <script type="text/javascript">

        window.addEventListener('load', function() {
            FastClick.attach(document.body);
        }, false);

        var newNum = "";
        var per = window.innerWidth / 530;
        var globalData = {
            "baseUrl": "<?php echo $base_url;?>",
            "dealerNum": "1",
            "fileUrl": "<?php echo $image_url;?>",
            "imageUrl": "<?php echo $image_url;?>",
            "shareIcon": "",
            "shareUrl": "",
            "inviteName":'<?php echo $invite_nick;?>',
            "inviteCode":'263',
            "inviteAccountCode":'',
            "inviteAvatar":'<?php echo $base_url;?>files/club/images/orgAvatar.jpg',
            "isJoin":'<?php echo $is_join;?>',
            "club_no":'<?php echo $club_no;?>',
            "orgName":'<?php echo $club_name;?>',
            "apiUrl": "<?php echo $base_url;?>",
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

<style type="text/css">
    #alertCommon .alertMask{position: fixed;z-index: 998;top: 0;left: 0;width: 100%;height: 100%;background-color: rgba(0,0,0,0.5);}
    #alertCommon .alertFrame{position: fixed;z-index: 999;width: 90vw;max-width: 90vw; top: 45%; left: 50%;-webkit-transform:translate(-50%,-60%); background-color: #fff; text-align: center; border-radius: 8px; overflow: hidden;opacity: 1; color: white;}
    #alertCommon .text{position: relative;margin-top: 15vw;margin-bottom: 15vw;margin-left: 8vw;margin-right: 8vw;word-wrap: break-word;word-break: break-all;color: #000;background-color: white;border-top: solid;border-color: #e6e6e6;border-width: 0px;}
    #alertCommon .buttonFrame{position: relative;width: 100%;height: 11vw;line-height: 11vw;text-align: center;color: #fff;margin-bottom: 9vw;text-align: center;font-size: 4vw;}
    #alertCommon .buttonFrame .button{position: relative;width: 32vw;height: 11vw;line-height: 11vw;background: #6d7dd4;color:#fff;border-radius: 1.5vw;}
    #alertCommon .buttonFrame .buttonMiddle{position: absolute;left: 50%;margin-left: -16vw;}
    #alertCommon .buttonFrame .buttonLeft{position: absolute;left: 10vw;}
    #alertCommon .buttonFrame .buttonRight{position: absolute;right: 10vw;background: #ff5555;}
</style>


<div style="position: fixed;width:100%;height:100%;top:0;left:0;background: #000" id="loading">
    <img src="<?php echo $image_url;?>files/images/common/loading.gif" style="top: 40%;position: absolute;left: 50%;margin-left: -45px;margin-top: -45px;" />
</div>
<div class="top_btn">
    <div class="goback" onClick="window.history.go(-1);"></div>
    <!-- <div class="goback_home" ng-click="goHall()"></div> -->
    <div class="copy_link" data-clipboard-text="Copy Me!"></div> 
</div>
<div class="main" style="display: none;" ng-cloak>

    <img src="<?php echo $base_url;?>files/club/images/union_bg.jpg"  style="top: 0;left: 0;width: 100%;height: 100%;position: relative;">

    <div id="alertCommon" ng-show="isShowAlert">
        <div class="alertMask" ></div>
        <div class="alertFrame" >
            <div class="text">
                {{alertText}}
            </div>

            <div class="buttonFrame" ng-show="alertType==3">
                <div class="button buttonLeft" ng-click="closeAlert()">取消</div>
                <div class="button buttonRight" ng-click="closeAlert()">确定</div>
            </div>

            <div class="buttonFrame" ng-show="alertType==7">
                <div class="button buttonMiddle" ng-click="closeAlert()">确定</div>
            </div>

        </div>
    </div>

    <div style="position: absolute;top: 10vh;left: 50%;margin-left: -25vh;height: 80vh;width: 50vh;" ng-if="showType==1">
        <img src="<?php echo $base_url;?>files/club/images/union_card.jpg" style="position: absolute;top: 0;left: 1vh;width: 48vh;height: 100%;">
        <img src="<?php echo $base_url;?>files/club/images/union_card_bottom.png" style="position: absolute;bottom: 0;width: 50vh;height: 35.25vh;">
        <div class="imgOpen" style="position: absolute;bottom: 10vh;left: 50%;margin-left: -10vh;width: 20vh;height: 20vh;backface-visibility:hidden;" ng-click="clickJoin()">
            <img  ng-src="{{joinImg}}" style="position: absolute;width: 20vh;height: 20vh;transform: rotate({{img.rotate}}deg);" >
        </div>

        <img ng-src="{{inviteAvatar}}" style="position: absolute;top: 12vh;left: 50%;margin-left: -6vh;width: 12vh;height: 12vh;border-radius: 6vh;">
        <div style="position: absolute;top: 25vh;width: 100%;height: 5vh;line-height: 5vh;text-align: center;color: orange;font-size: 2.5vh">
            {{inviteName}}
        </div>
        <div style="position: absolute;top: 32vh;width: 100%;height: 5vh;line-height: 5vh;text-align: center;color: orange;font-size: 3vh;">
            邀请你加入{{orgName}}
        </div>
    </div>

    <div style="position: absolute;top: 10vh;left: 50%;margin-left: -25vh;height: 80vh;width: 50vh;" ng-if="showType==2">
        <img  src="<?php echo $base_url;?>files/club/images/union_card.jpg" style="position: absolute;top: 0;left: 0;width: 50vh;height: 100%;">
        <img src="<?php echo $base_url;?>files/club/images/union_finish.png" style="position: absolute;top: 29vh;left: 50%;margin-left: -6vh;width: 12vh;height: 12vh;">
        <div style="position: absolute;top: 43vh;width: 100%;height: 5vh;text-align: center;font-size: 3vh;color: orange;font-weight: bold;">
            {{resultText}}
        </div>
    </div>


</div>

</body>

<script type="text/javascript" src="<?php echo $image_url;?>files/js/jquery-1.9.1.min.js"></script>
<script type="text/javascript" src="<?php echo $image_url;?>files/js/jquery.copy.js"></script>
<script type="text/javascript" src="<?php echo $base_url;?>files/club/js/angular.min.js"></script>
<script src="https://res.wx.qq.com/open/js/jweixin-1.0.0.js" ></script>
<script type="text/javascript" src="<?php echo $base_url;?>files/club/js/inviteDetail-1.0.2.js"></script>
<script>
    $(function(){
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
    })
</script>

</html>
