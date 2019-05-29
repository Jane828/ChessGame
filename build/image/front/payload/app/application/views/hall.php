<html ng-app="app">
<head>
<meta charset="utf-8" >
<meta name="viewport" content="width=device-width,initial-scale=1.0, minimum-scale=1.0, maximum-scale=1.0, user-scalable=no"/>
<meta name="format-detection" content="telephone=no" />
<meta name="msapplication-tap-highlight" content="no" />
<title>太古休闲大厅</title>
<link rel="stylesheet" href="<?php echo $image_url;?>files/css/loading.css">
<script type="text/javascript" src="<?php echo $image_url;?>files/js/jquery-1.9.1.min.js"></script>
<script type="text/javascript" src="<?php echo $image_url;?>files/js/jquery.marquee.min.js"></script>

<script type="text/javascript" src="<?php echo $image_url;?>files/js/angular.min.js"></script>
<script src="https://res.wx.qq.com/open/js/jweixin-1.0.0.js"></script>
<script type="text/javascript" src="<?php echo $image_url;?>files/js/fastclick.js?_version=<?php echo $front_version;?>"></script>
<script>
     var image_url = "<?php echo $image_url;?>";
</script>

</head>
<style>
*{padding: 0;margin:0;}a {text-decoration: none;color: #fff;}ul {list-style: none;}input{border: none;outline:none}body{font-family: 'Helvetica Neue', Helvetica, 'Hiragino Sans GB', 'Microsoft YaHei', 微软雅黑, Arial, sans-serif;cursor: default;}
img{border: none;}
html{height:100%;overflow: hidden;}
body{height:100%;overflow: hidden;}
.main{width: 100%;position:relative;margin:0 auto;}
.roomCard{position: absolute;top:0;right:0;}
/* .roomCard .img1{position: relative;float: left;z-index: 50;} */
.roomCard .img2{position: absolute;top:0;z-index: 51}
.roomCard .num{position: relative;background: #273151;color:#fff;font-size: 3.5vw;border-radius:4vw;padding: 1vw 4vw;float: left;margin-left: -3vw;z-index: 49;}

.recharge{position: absolute;top:5vw;right:30%;width: 22vw;height: 8vw;z-index: 51;}

/* .user{position: absolute;top:0.8vw;left:0;width: 50%;}
.user .user_left {display: inline-block;background:url("<?php echo $image_url;?>files/images/hall/user_border.png");background-size:100% 100%;padding:7px;}
.user .user_right {display: inline-block;vertical-align: top;}
.user .user_right .roomCard{top: initial;right: initial;margin-left: -2vw;}
.user .user_right .roomCard .img1{margin-top: 0;vertical-align: middle;margin-right: -5vw;float: none;}
.user .user_right .roomCard .num{float: none;margin-left: 0;}
.user .avatar{border: 1px solid #b495d8;border-radius:5px;float: left;position: relative;z-index: 50;}
.user .name{color: #fff;font-size: 3.5vw;position: relative;} */

.header {position: fixed;left: 0;top: 0;height: 8vh;width:100vw;z-index:100;background:url("<?php echo $image_url;?>files/images/hall/header.png")no-repeat;background-size:100% 100%;}
.header .header-left{height:100%;line-height: 8vh;float:left;white-space:nowrap;}
.header .header-avatar{height:5vh;width:5vh;display:inline-block;background:url("<?php echo $image_url;?>files/images/hall/avatar.png")no-repeat;background-size:100% 100%;padding: 2%;margin-left: 6vw;vertical-align: middle;}
.header .header-avatar img {width:96%;height:96%;}
.header .header-left .name{display: inline-block;width: 30vw;overflow: hidden;text-overflow: ellipsis;white-space: nowrap;vertical-align: middle;color:#ffffff;}
.header .header-right{float:right;height: 8vh;margin-right: 8vw;}
.header .header-right img {height: 8vh;vertical-align: middle;z-index: 1;position: relative;}
.header .header-right span {background-color: #333333;color: #ffffff;border: 1px solid #333333;border-radius: 30vw;display: inline-block;vertical-align: middle;padding: 1px 10px 1px 4vw;margin-left: -6vw;max-width: 19vw;overflow: hidden;text-overflow: ellipsis;white-space: nowrap;}

.bottom-menu {height: 10vh;position: fixed;left: 0;bottom: 0;width: 100vw;z-index:100;background:url("<?php echo $image_url;?>files/images/hall/menu.png")no-repeat;background-size:100% 100%;}
.bottom-menu ul {width:100vw;}
.bottom-menu .menu-item{position: relative;width: 25vw;float: left;height: 10vh;text-align: center;padding-top: 1vh;}
.bottom-menu .menu-item a {display:inline-block;}
.bottom-menu .menu-item img{height: 5vh;}
.bottom-menu .menu-item p {color: #F0D582;font-weight: 600;}
.bottom-menu .menu-item span {position: absolute;right: 0;top: 2vh;width: 2px;height: 6vh;background:url("<?php echo $image_url;?>files/images/hall/border.png")no-repeat;background-size:100% 180%;}
.bottom-menu .menu-item-selected{background:url("<?php echo $image_url;?>files/images/hall/active.png")no-repeat;background-size:100% 100%;}

.game-set{width: 100vw;position: absolute;left: 0;top: 0;height: 83vh;padding-top: 10vh;overflow: auto;}
.game-set .set-item{float: left;width: 40vw;margin-left: 7vw;margin-bottom: 4vh;}
.game-set .set-item img{width:100%;}

.game-list {height: 100vh;width: 100vw;position: fixed;left: 0;top: 0;z-index: 110;}
.game-list .game-list-bg{width: 100%;height: 100%;background-color: #000000;opacity: 0.4;position: absolute;left: 0;top: 0;}
.game-list .game-list-box {width: 100%;height: 86vh;text-align: center;position: absolute;left: 0;top: 14vh;background-color: rgba(255, 252, 232,0.8);border-radius: 10vw 10vw 0 0;overflow: scroll;}
/* .game-list .list-item{float: left;margin-left: 9vw;margin-top: 4vh;} */
.game-list .list-item{float: left;width:50vw;}
.game-list .list-item .game{width: 36vw;height:auto;margin-top: 4vh;}
.game-list .list-item:nth-child(2n) .game{margin-left:-3vw;}
.game-list .list-item:nth-child(2n+1) .game{margin-right:-3vw;}

.alert{position: fixed;width: 100%;height:100%;top:0;left:0;z-index: 111;color: #fff;font-family: simHei;}
.alert .alertBack{width: 100%;height:100%;background: #000;opacity:0.8;position: absolute;}
.alert .mainPart{position: relative;top: 45%;left: 50%;margin-top:-19vh ;margin-left:-22.5vh ;width: 45vh;height:38vh; }
.alert .mainPart .alertText{position: absolute;width:100%;line-height: 8.5vh;font-size: 2.5vh;width: 36vh;left:50%;margin-left:-18vh;top:5.6vh;text-align: center; color:#714D29;}
.alert .mainPart .buttonMiddle{position: absolute;width:100%;line-height: 6vh;height: 6vh;font-size: 2.5vh;width: 18vh;left:50%;margin-left:-9vh;bottom:2.2vh;text-align: center;background:url("<?php echo $image_url;?>files/images/common/button2.png");background-size:100%;}
.alert .mainPart .buttonLeft{position: absolute;width:100%;height: 6.2vh;font-size: 2.5vh;width: 18vh;left:3vh;bottom:2.2vh;text-align: center;background:url("<?php echo $image_url;?>files/images/common/button2.png");background-size:cover;}
.alert .mainPart .buttonRight{position: absolute;width:100%;height: 6.2vh;font-size: 2.5vh;width: 18vh;right:3vh;bottom:2.2vh;text-align: center;background:url("<?php echo $image_url;?>files/images/common/button1.png");background-size:cover;}
.alert .mainPart .backImg{position: absolute;width:100%;height:100%;border-radius: 1.5vh;top:0;left:0;background: #FFF4DC;}
.alert .mainPart .backImg .blackImg{position: absolute;width:42vh;height:25vh;top:2.2vh;left:1.5vh; background:url("../files/images/common/backImg.png") no-repeat; background-size:contain;} 
.alert .mainPart .btnCancel{display: inline-block;width: 100px;height: 40px;background: url("../images/common/cancelRoom.png") no-repeat;background-size: cover;position: absolute;bottom: 0;left: 4vw;}
.alert .mainPart .buttonWatch{display: inline-block;width: 100px;height: 40px;background: url("../images/watch/joinWatch.png") no-repeat;background-size: cover;position: absolute;bottom: 0;right: 4vw;}

.laizi .selectItem {margin-left: 0.8vh!important;}

/* .game{width: 46%;position: absolute;height:auto;border-radius: 2vw;z-index: 60;}
.game:nth-child(2n){
    right: 2vw;
}
.game:nth-child(2n+1){
    left: 2vw;
}
.game:nth-last-child(1) {
    margin-bottom: 20vh;
} */
</style>
<script>
    $(function() {
        $('.game').each(function (i, obj) {
            $(obj).addClass('game'+(i+1));
            // $(obj).css("top",(parseInt(i/2)*50 + 20) + 'vw');
        });
        // $('.game:last').css("margin-bottom","20vh");
    });
    $(window).bind("scroll", function () {
        var sTop = $(window).scrollTop();
        var sTop = parseInt(sTop);
        if(sTop > 50){
            $(".user,.hall-top,.roomCard").css("opacity",(1-(sTop-50)/200));
        }else {
            $(".user,.hall-top,.roomCard").css("opacity", 1);
        }
    });
</script>
<body style="background: #000;" >
<?php if ($broadcast) {
    ?>
    <div style="position:fixed;width: 100%;color: white;background: rgba(0,0,0,0.5);font-size: 15px;z-index: 100;padding: 10px">
        <div class='marquee' style="float: left;margin-left:35px;width: 100%;overflow: hidden">
            <?php echo $broadcast; ?>
        </div>
    </div>

    <div style="position: fixed;top: 0vw;z-index: 101;padding: 5px 10px 10px 10px">
        <img style="width: 30px;height: 30px" src="<?php echo $image_url; ?>files/images/common/alert_icon.png" />
    </div>
<?php
}?>
<div style="position: fixed;width:100%;height:100%;top:0;left:0;background: #000" id="loading">
	<img src="<?php echo $image_url;?>files/images/common/loading.gif" style="top: 40%;position: absolute;left: 50%;margin-left: -45px;margin-top: -45px;" />
</div>
<div class="main" ng-controller="myCtrl" style="display: none;">

	<div class="alert" ng-show="isShowAlert">
		<div class="alertBack"></div>
		<div class="mainPart" style="height:28vh;">
			<div class="backImg">
				<div class="blackImg"></div>
			</div>
			<div class="alertText" >{{alertText}}</div>
			<div ng-show="alertType==1">
				<div class="buttonMiddle" ng-click="closeAlert()"></div>
			</div>
			<div ng-show="alertType==2">
				<div class="buttonLeft" ng-click="home()">返回首页</div>
				<div class="buttonRight" ng-click="createRoom()">创建房间</div>
			</div>
			<div ng-show="alertType==3">
				<div class="buttonLeft" ng-click="home()">返回首页</div>
				<div class="buttonRight" ng-click="closeAlert()">取消</div>
			</div>
			<div ng-show="alertType==4">
				<div class="buttonLeft" ng-click="createRoom()">创建房间</div>
				<div class="buttonRight" ng-click="sitDown()">加入游戏</div>
			</div>
			<div ng-show="alertType==5">
				<div class="buttonMiddle" ng-click="getCards()">领取</div>
			</div>
			<div ng-show="alertType==6">
				<div class="buttonMiddle" ng-click="closeAlert()"></div>
			</div>
			<div ng-show="alertType==23">
				<div class="buttonMiddle" ng-click="finishBindPhone()">确定</div>
			</div>
			<div ng-show="alertType==31">
				<div class="buttonMiddle" ng-click="reloadView()">确定</div>
			</div>
		</div>
	</div>

	<img src="<?php echo $image_url;?>files/images/hall/body.png"  style="width: 100%;height: auto;position: fixed;left: 0;top: 0;">
    <div class="header">
        <div class="header-left">
            <div class="header-avatar">
                <img ng-src="{{userInfo.avatar}}"/>
            </div>
            <span class="name">{{userInfo.name}}</span>
        </div>
        <div class="header-right">
            <img src="<?php echo $image_url;?>files/images/common/ticket.png"/>
            <span class="num" style="">{{userInfo.card}}张</span>
        </div>
    </div>
    <div class="bottom-menu">
        <ul>
            <li class="menu-item" ng-repeat="menuData in menuDatas">
                <a ng-href="{{menuData.url}}">
                    <img ng-src="<?php echo $image_url;?>files/images/hall/{{menuData.img}}" />
                    <p>{{menuData.name}}</p>
                    <span ng-if="!menuData.end"></span>
                </a>
            </li>
        </ul>
        <!-- <ul>
            <li class="menu-item menu-item-selected">
                <a>
                    <img src="<?php echo $image_url;?>files/images/hall/game.png" />
                    <p>游戏</p>
                    <span></span>
                </a>
            </li>
            <li class="menu-item">
                <a href="<?php echo $base_url;?>f/fri">
                    <img src="<?php echo $image_url;?>files/images/hall/friend.png" />
                    <p>好友</p>
                    <span></span>
                </a> 
            </li>
            <li class="menu-item">
                <a href="<?php echo $base_url;?>f/box">
                    <img src="<?php echo $image_url;?>files/images/hall/box.png" />
                    <p>包厢</p>
                    <span></span>
                </a> 
            </li>
            <li class="menu-item">
                <a href="<?php echo $base_url;?>f/yh">
                    <img src="<?php echo $image_url;?>files/images/hall/user.png" />
                    <p>个人</p>
                </a>
            </li>
        </ul> -->
    </div>
    <div class="game-set">
        <ul>
            <li class="set-item" ng-click="showGameList('bulls')">
                <img src="<?php echo $image_url;?>files/images/hall/bulls.png">
            </li>
            <li class="set-item" ng-click="showGameList('flowers')">
                <img src="<?php echo $image_url;?>files/images/hall/flowers.png">
            </li>
            <li class="set-item" ng-click="showGameList('sgs')">
                <img src="<?php echo $image_url;?>files/images/hall/sgs.png">
            </li>
        </ul>
    </div>
    <div class="game-list" ng-show="createInfo.isShowGame">
        <div class="game-list-bg" ng-click="hideGameList()"></div>
        <div class="game-list-box" ng-click="hideGameList();">
            <ul>
                <li class="list-item" ng-if="createInfo.gameList=='flowers'" ng-click="createFlower($event)">
                    <img src="<?php echo $image_url;?>files/images/hall/list-flower.png" class="game">
                </li>
                <li class="list-item" ng-if="createInfo.gameList=='flowers'" ng-click="createTenFlower($event)">
                    <img src="<?php echo $image_url;?>files/images/hall/list-tflower.png" class="game">
                </li>
                <li class="list-item" ng-if="createInfo.gameList=='flowers'" ng-click="createBFlower($event)">
                    <img src="<?php echo $image_url;?>files/images/hall/list-bflower.png" class="game">
                </li>
                <li class="list-item" ng-if="createInfo.gameList=='bulls'" ng-click="createBull13($event)">
                    <img src="<?php echo $image_url;?>files/images/hall/list-fbull.png" class="game">
                </li>
                <li class="list-item" ng-if="createInfo.gameList=='bulls'" ng-click="createBull12($event)">
                    <img src="<?php echo $image_url;?>files/images/hall/list-tbull.png" class="game">
                </li>
                <li class="list-item" ng-if="createInfo.gameList=='bulls'" ng-click="createBull9($event)">
                    <img src="<?php echo $image_url;?>files/images/hall/list-nbull.png" class="game">
                </li>
                <li class="list-item" ng-if="createInfo.gameList=='bulls'" ng-click="createBull($event)">
                    <img src="<?php echo $image_url;?>files/images/hall/list-bull.png" class="game">
                </li>
                <li class="list-item" ng-if="createInfo.gameList=='bulls'" ng-click="createLBull($event)">
                    <img src="<?php echo $image_url;?>files/images/hall/list-lbull.png" class="game">
                </li>
                <li class="list-item" ng-if="createInfo.gameList=='sgs'" ng-click="createSangong($event)">
                    <img src="<?php echo $image_url;?>files/images/hall/list-sg.png" class="game">
                </li>
                <li class="list-item" ng-if="createInfo.gameList=='sgs'" ng-click="createNSangong($event)">
                    <img src="<?php echo $image_url;?>files/images/hall/list-nsg.png" class="game">
                </li>
            </ul>
        <div>
        
    </div>

	<!-- 20  55  90  125  160  195 230-->
    <!-- <img src="<?php echo $image_url;?>files/images/hall/create_flower.png"   class="game" ng-click="createFlower()">
    <img src="<?php echo $image_url;?>files/images/hall/create_tflower.png"   class="game" ng-click="createTenFlower()">
    <img src="<?php echo $image_url;?>files/images/hall/create_bflower.png"   class="game" ng-click="createBFlower()">
    <img src="<?php echo $image_url;?>files/images/hall/create_fbull.png"     class="game" ng-click="createBull13()">
    <img src="<?php echo $image_url;?>files/images/hall/create_tbull.png"     class="game" ng-click="createBull12()">
    <img src="<?php echo $image_url;?>files/images/hall/create_nbull.png"   class="game" ng-click="createBull9()">
    <img src="<?php echo $image_url;?>files/images/hall/create_bull.png"  class="game" ng-click="createBull()">
    <img src="<?php echo $image_url;?>files/images/hall/create_lbull.png"     class="game" ng-click="createLBull()">
    <img src="<?php echo $image_url;?>files/images/hall/create_sangong.png" class="game" ng-click="createSangong()" >
    <img src="<?php echo $image_url;?>files/images/hall/create_nsangong.png" class="game" ng-click="createNSangong()" > -->
    
<!--	<img src="--><?php //echo $image_url;?><!--files/images/hall/create_landlord.png"  class="game" ng-click="createLandlord()">-->
<!--	<img src="--><?php //echo $image_url;?><!--files/images/hall/create_gdmj.png"      class="game" ng-click="createMajiang()" >-->
<!--	<img src="--><?php //echo $image_url;?><!--files/images/hall/create_tsangong.png" class="game" ng-click="createTSangong()" >-->

<style>
	.createRoom{position: fixed;width: 100%;height:100%;top:0;left:0;z-index: 210;}
	.createRoom .createRoomBack{width: 100%;height:100%;background: #000;opacity:0.6;}
	.createRoom .mainPart{width: 47vh; position: absolute;top:50%;left:50%;-webkit-transform:translate(-50%,-50%);transform:translate(-50%,-50%); text-align:left;}
	.createRoom .mainPart .createB{width: 100%;height:100%;top:2%;left:2%; right:2%; bottom:2%;position: absolute;background:#634fa6;border:1px solid #a684f2;border-radius:10px; }
    .createRoom .mainPart .showPart{ background:#FFF4DC; border-radius:5px; padding-top:20px; height:calc(100% - 20px); overflow:hidden; }
	.createRoom .mainPart  .createTitle{padding-top:5px; text-align: center; background:url(<?php echo $image_url;?>files/images/common/storetitle.png) no-repeat; background-size:contain; width:50vw; height:40px; position: absolute; top:0; left:50%; transform:translateX(-50%);}
    .createRoom .mainPart  .createTitle span{ color:#7D2F00; position:relative;}
    .createRoom .mainPart  .createTitle span::before{ content:attr(data-text); position: absolute; left:0; z-index:-1; -webkit-text-stroke:3px white;}
	.createRoom .mainPart .showPart .cancelCreate{width: 5vh;height:5vh;top:-2.5vh;right:-2.5vh;position: absolute;}
	.createRoom .mainPart .showPart .createCommit{ width:20vw;  text-align: center;background:url("<?php echo $image_url;?>files/images/common/createRoom.png");background-size:100%;}
    .createRoom .mainPart .showPart .btnOkCancel{ height:calc(15.5vh - 40px);margin-top:5px; text-align:center; position:relative;}
    .createRoom .mainPart .showPart .btnOkCancel .btnCancel{ display:inline-block; width:100px; height:40px;  background:url("<?php echo $image_url;?>files/images/common/cancelRoom.png") no-repeat;background-size:cover;  position:absolute; top:50%; transform:translateY(-50%); left:5vw;}
    .createRoom .mainPart .showPart .btnOkCancel .btnOk{ display:inline-block; width:100px; height:40px;  background:url("<?php echo $image_url;?>files/images/common/createRoom.png") no-repeat;background-size:cover; position:absolute; top:50%; transform:translateY(-50%); right:5vw;}
	.createRoom .mainPart .showPart .blueBack{width: 45vh;background-image:url("<?php echo $image_url;?>files/images/common/innerborder.png");background-size:contain; margin:0 auto;position: relative; margin-top:0.6vh; background-repeat: no-repeat; }
	.createRoom .mainPart .showPart .selectPartTitle{ width:100%;line-height:4.5vh;font-size:2.1vh;color:#A04A19;border-radius:0px;font-family:simHei; padding:1vh 0;overflow: hidden; text-align:center; margin-top:1vh;}
    .createRoom .mainPart .showPart .blueBack .selectPart{width:100%;margin-top:0.5vh;line-height:4.5vh;font-size:2.1vh;position: relative;color:#111431;border-radius:0px;font-family:simHei; padding:1vh 0;overflow: hidden;}
	.createRoom .mainPart .blueBack .selectPart .selectTitle{float: left;width:8vh;text-align: right; color:#714D29; }
    .createRoom .mainPart .blueBack .selectPart .selectRoundTitle{width:12vh;}
	.createRoom .mainPart .blueBack .selectPart .selectList{float: left; width: 37vh}
    .createRoom .mainPart .blueBack .selectPart .selectRoundList{float: left; width: 32vh; padding-top:1.2vh;}
    .createRoom .mainPart .blueBack .selectPart .selectList .selectItem{float: left;position:relative;margin-left:1.6vh;min-width: 16.7vh; }
    .createRoom .mainPart .blueBack .selectPart .selectList .flowerList{float: left;position:relative;height:5vh; min-width:8vh;}
    .createRoom .mainPart .blueBack .selectPart .selectList .changeWidth{float: left;position:relative;margin-left:1.8vh;min-width: 5vh;}
    .createRoom .mainPart .blueBack .selectPart .selectList .selectFlowerItem{float: left;position:relative;margin-left:1.8vh;min-width: 10vh;}
    .createRoom .mainPart .blueBack .selectPart .selectList .selectRoundItem{float: left;position:relative;margin-left:1.8vh;min-width: 5vh;}
    .createRoom .mainPart .blueBack .selectPart .selectList .defineScore{float: left;position:relative;margin-left:1.8vh;min-width: 4vh;}
    .createRoom .mainPart .blueBack .chipItems .selectItem{min-width: 10vh !important;}
    .createRoom .mainPart .blueBack .chipItems select{
        display: block;
        margin-top: 0.6vh;
        height: 3.4vh;
        width: 9vh;
    }
    .createRoom .mainPart .blueBack .timeItems span,
    .createRoom .mainPart .blueBack .timeItems select{
        float: left;
    }
    /*修改下拉框样式*/
    .createRoom .mainPart .showPart .blueBack .timeItems .flowerReadySelect,.flowerChipSelect, .tbullSelect, .fbullSelect, .lbullSelect, .sgSelect, .nsgSelect,
    .tflowerReadySelect, .tflowerChipSelect, .bullSelect,.bflowerReadySelect, .bflowerChipSelect, .nbullSelect{  -webkit-appearance:none; background: linear-gradient(to bottom, #CDAD6F, #E9D4A1); border: 1px #B4822C solid; color:#633201; }
    .createRoom .mainPart .showPart .blueBack .timeItems .readySelectIcon{ width: 20px; height:20px;  position: absolute; top:1.5vh; right: 3vh; overflow: hidden; pointer-events: none; }
    .createRoom .mainPart .showPart .blueBack .timeItems .readySelectIcon .sanjiao{ width: 100%; height: 100%;  transform: rotate(60deg) skew(30deg) translate(-10px, -10px); background: linear-gradient(to right bottom, red, #4F990E 60%, #B8E485); pointer-events: none;    box-shadow: 0 0 4px green;}
    .createRoom .mainPart .showPart .blueBack .timeItems .bullSelectIcon{ width: 20px; height:20px;  position: absolute; top:1.5vh; right: 3vh; overflow: hidden; pointer-events: none; }
    .createRoom .mainPart .showPart .blueBack .timeItems .bullSelectIcon .sanjiao{width: 100%; height: 100%;  transform: rotate(60deg) skew(30deg) translate(-10px, -10px); background: linear-gradient(to right bottom, red, #4F990E 60%, #B8E485); pointer-events: none;    box-shadow: 0 0 4px green;}
    .createRoom .mainPart .showPart .blueBack .timeItems .nbullSelectIcon{ width: 20px; height:20px;  position: absolute; top:1.5vh; right: 3vh; overflow: hidden; pointer-events: none; }
    .createRoom .mainPart .showPart .blueBack .timeItems .nbullSelectIcon .sanjiao{width: 100%; height: 100%;  transform: rotate(60deg) skew(30deg) translate(-10px, -10px); background: linear-gradient(to right bottom, red, #4F990E 60%, #B8E485); pointer-events: none;    box-shadow: 0 0 4px green;}
    .createRoom .mainPart .showPart .blueBack .timeItems .tbullSelectIcon{ width: 20px; height:20px;  position: absolute; top:1.5vh; right: 3vh; overflow: hidden; pointer-events: none; }
    .createRoom .mainPart .showPart .blueBack .timeItems .tbullSelectIcon .sanjiao{width: 100%; height: 100%;  transform: rotate(60deg) skew(30deg) translate(-10px, -10px); background: linear-gradient(to right bottom, red, #4F990E 60%, #B8E485); pointer-events: none;    box-shadow: 0 0 4px green;}
    .createRoom .mainPart .showPart .blueBack .timeItems .fbullSelectIcon{ width: 20px; height:20px;  position: absolute; top:1.5vh; right: 3vh; overflow: hidden; pointer-events: none; }
    .createRoom .mainPart .showPart .blueBack .timeItems .fbullSelectIcon .sanjiao{width: 100%; height: 100%;  transform: rotate(60deg) skew(30deg) translate(-10px, -10px); background: linear-gradient(to right bottom, red, #4F990E 60%, #B8E485); pointer-events: none;    box-shadow: 0 0 4px green;}
    .createRoom .mainPart .showPart .blueBack .timeItems .lbullSelectIcon{ width: 20px; height:20px;  position: absolute; top:1.5vh; right: 3vh; overflow: hidden; pointer-events: none; }
    .createRoom .mainPart .showPart .blueBack .timeItems .lbullSelectIcon .sanjiao{width: 100%; height: 100%;  transform: rotate(60deg) skew(30deg) translate(-10px, -10px); background: linear-gradient(to right bottom, red, #4F990E 60%, #B8E485); pointer-events: none;    box-shadow: 0 0 4px green;}
    .createRoom .mainPart .showPart .blueBack .timeItems .sgSelectIcon{ width: 20px; height:20px;  position: absolute; top:1.5vh; right: 3vh; overflow: hidden; pointer-events: none; }
    .createRoom .mainPart .showPart .blueBack .timeItems .sgSelectIcon .sanjiao{width: 100%; height: 100%;  transform: rotate(60deg) skew(30deg) translate(-10px, -10px); background: linear-gradient(to right bottom, red, #4F990E 60%, #B8E485); pointer-events: none;    box-shadow: 0 0 4px green;}
    .createRoom .mainPart .showPart .blueBack .timeItems .nsgSelectIcon{ width: 20px; height:20px;  position: absolute; top:1.5vh; right: 3vh; overflow: hidden; pointer-events: none; }
    .createRoom .mainPart .showPart .blueBack .timeItems .nsgSelectIcon .sanjiao{width: 100%; height: 100%;  transform: rotate(60deg) skew(30deg) translate(-10px, -10px); background: linear-gradient(to right bottom, red, #4F990E 60%, #B8E485); pointer-events: none;    box-shadow: 0 0 4px green;}
    /*修改下拉框样式*/
    .createRoom .mainPart .blueBack .timeItems select{
        display: block;
        width: 9vh;
        margin: 0.6vh 0 0 0.5vh;
        height: 3.4vh;
        vertical-align: middle;
    }
    #selectList{ height:3.4vh; width:9vh;  margin-top: 0.6vh; -webkit-appearance:none; background: linear-gradient(to bottom, #CDAD6F, #E9D4A1); border: 1px #B4822C solid; color:#633201;}
    #flowerSelect{     position: absolute;transform: translateY(-50%);top: 44%; }
    .createRoom .mainPart .blueBack .selectPart .selectList .selectItem .nbullfootSelectIcon{ width: 20px; height:20px; position:absolute; top:1.5vh; left:10vh; overflow: hidden; pointer-events: none; }
    .createRoom .mainPart .blueBack .selectPart .selectList .selectItem .nbullfootSelectIcon .footSanJiao{ width: 100%; height: 100%;  transform: rotate(60deg) skew(30deg) translate(-10px, -10px); background: linear-gradient(to right bottom, red, #4F990E 60%, #B8E485); pointer-events: none;    box-shadow: 0 0 4px green; }
    .createRoom .mainPart .blueBack .selectPart .selectList .selectItem .bullfootSelectIcon{ width: 20px; height:20px; position:absolute; top:1.5vh; left:10vh; overflow: hidden; pointer-events: none; }
    .createRoom .mainPart .blueBack .selectPart .selectList .selectItem .bullfootSelectIcon .footSanJiao{ width: 100%; height: 100%;  transform: rotate(60deg) skew(30deg) translate(-10px, -10px); background: linear-gradient(to right bottom, red, #4F990E 60%, #B8E485); pointer-events: none;    box-shadow: 0 0 4px green; }
    .createRoom .mainPart .blueBack .selectPart .selectList .selectItem .fbullfootSelectIcon{ width: 20px; height:20px; position:absolute; top:1.5vh; left:10vh; overflow: hidden; pointer-events: none; }
    .createRoom .mainPart .blueBack .selectPart .selectList .selectItem .fbullfootSelectIcon .footSanJiao{ width: 100%; height: 100%;  transform: rotate(60deg) skew(30deg) translate(-10px, -10px); background: linear-gradient(to right bottom, red, #4F990E 60%, #B8E485); pointer-events: none;    box-shadow: 0 0 4px green; }
    .createRoom .mainPart .blueBack .selectPart .selectList .selectItem .tbullfootSelectIcon{ width: 20px; height:20px; position:absolute; top:1.5vh; left:10vh; overflow: hidden; pointer-events: none; }
    .createRoom .mainPart .blueBack .selectPart .selectList .selectItem .tbullfootSelectIcon .footSanJiao{ width: 100%; height: 100%;  transform: rotate(60deg) skew(30deg) translate(-10px, -10px); background: linear-gradient(to right bottom, red, #4F990E 60%, #B8E485); pointer-events: none;    box-shadow: 0 0 4px green; }
    .createRoom .mainPart .blueBack .selectPart .selectList .selectItem .lbullfootSelectIcon{ width: 20px; height:20px; position:absolute; top:1.5vh; left:10vh; overflow: hidden; pointer-events: none; }
    .createRoom .mainPart .blueBack .selectPart .selectList .selectItem .lbullfootSelectIcon .footSanJiao{ width: 100%; height: 100%;  transform: rotate(60deg) skew(30deg) translate(-10px, -10px); background: linear-gradient(to right bottom, red, #4F990E 60%, #B8E485); pointer-events: none;    box-shadow: 0 0 4px green; }
	.createRoom .mainPart .blueBack .selectPart .selectList .selectItem .selectBox{float: left;height:3.4vh;width:3.4vh;border:1px solid #B4822C;border-radius:2px;background:linear-gradient(to bottom, #C8A766, #F1DFB0);margin-top:0.5vh;position: relative; }
	.createRoom .mainPart .blueBack .selectPart .selectList .selectItem img{position: absolute;display: block;width: 2.7vh;height:2.7vh;left: 0.4vh;top:0.4vh;}
	.createRoom .mainPart .blueBack .selectPart .selectList .selectItem .selectText{float: left;line-height: 5vh; margin-left: 0.5vh;color:#714D29; }
    .createRoom .mainPart .blueBack .selectPart .selectList .selectItem .selectText .flowerSelect{ -webkit-appearance:none; background: linear-gradient(to bottom, #CDAD6F, #E9D4A1); border: 1px #B4822C solid; color:#633201; }
    .createRoom .mainPart .blueBack .selectPart .selectList .selectItem .selectText .selectIcon{ width: 20px; height:20px;  position: absolute; top:1.5vh; right: 2px; overflow: hidden; pointer-events: none;}
    .createRoom .mainPart .blueBack .selectPart .selectList .selectItem .selectText .selectIcon .sanjiao{ width: 100%; height: 100%;  transform: rotate(60deg) skew(30deg) translate(-10px, -10px); background: linear-gradient(to right bottom, red, #4F990E 60%, #B8E485); pointer-events: none;    box-shadow: 0 0 4px green; }
    .createRoom .mainPart .blueBack .selectPart .selectList .selectItem .selectProgressText{ float: left;line-height: 5vh; margin-left: 0.5vh; width:12vw; height:40px; font-size:1.8vh;}
    .createRoom .mainPart .blueBack .selectPart .selectList .selectItem .seenProgressText{ color:#633201; float: left;line-height: 3vh; margin-left: 0.5vh; width:12vw; height:40px; font-size:2.1vh; }
    .createRoom .mainPart .blueBack .selectPart .selectList .selectItem .selectProgress{ float:left; margin-top:1.5vh; margin-left:2vw;}
    .createRoom .mainPart .blueBack .selectPart .selectList .selectItem .selectProgress .range{ -webkit-appearance:none;  height:20px; border-radius:15px; width:20vw; background:-webkit-linear-gradient(#FFF949, #F08D02) no-repeat; background-size: 0% 100%; margin-top:-4px;}
    .createRoom .mainPart .blueBack .selectPart .selectList .selectItem .selectProgress .range::-webkit-slider-thumb{ -webkit-appearance:none; height:20px; width:20px;  border-radius:50%; background:red;}
    .createRoom .mainPart .blueBack .selectPart .selectList .selectItem .selectProgress .range::-webkit-slider-runnable-track { box-shadow: 0 1px #def3f8, inset 0  1px 1px #0d1112;  border-radius:15px;}
    .createRoom .mainPart .blueBack .selectPart .selectList .selectItem .selectReduce{ float: left;height:2.1vh;width:2.1vh;border:1px solid #1d1045;border-radius:2px;background:#78899d;margin-top:1vh;position: relative; }
    .createRoom .mainPart .blueBack .selectPart .selectList .selectItem .seenProgressReduce{float: left;height:3vh;width:3vh;border-radius:50%; background:url("<?php echo $image_url;?>files/images/common/reduce.png") no-repeat;background-size:contain;}
    .createRoom .mainPart .blueBack .selectPart .selectList .selectItem .seenProgressAdd{ float: left;height:3vh;width:3vh;border-radius:50%;background:url("<?php echo $image_url;?>files/images/common/add.png") no-repeat;background-size:contain; }
    .createRoom .mainPart .blueBack .selectPart .selectList .selectItem .seenProgress{ float:left; margin-left:-10px; margin-right:20px;}
    .createRoom .mainPart .blueBack .selectPart .selectList .selectItem .seenProgress .seenRange{ -webkit-appearance:none;  height:3vh; border-radius:15px; width:20vw;  background:-webkit-linear-gradient(#FFF949, #F08D02) no-repeat;  background-size: 0% 100%; }
    .createRoom .mainPart .blueBack .selectPart .selectList .selectItem .seenProgress .seenRange::-webkit-slider-thumb{  -webkit-appearance:none; height:4vh; width:4vh; background-image:url(<?php echo $image_url;?>files/images/common/slider.png);background-size:cover; border-radius:50%; margin-top:-0.5vh;}
    .createRoom .mainPart .blueBack .selectPart .selectList .selectItem .seenProgress .seenRange::-webkit-slider-runnable-track{ box-shadow: 0 1px #def3f8, inset 0  1px 1px #0d1112;  border-radius:15px; height:3vh;}
    .createRoom .mainPart .blueBack .selectPart .selectList .selectItem .selectAdd{ float: left;height:2.1vh;width:2.1vh;border:1px solid #1d1045;border-radius:2px;background:#78899d;margin-top:1vh;position: relative; margin-left:5vw;}
    .createRoom .mainPart .blueBack .selectPart .selectList .selectItem .selectXiPaiText{color:#54A802;}
    .createRoom .mainPart .blueBack .selectPart .selectList .selectItem .seenTip{ color:#54A802;line-height: 2.1vh;margin-bottom: 10px; }
    /**************************开始*****************炸金花的不能比牌的样式***************************************************/
    .createRoom .mainPart .blueBack .selectPart .selectList .selectItem .compareProgressReduce{float: left;height:3vh;width:3vh;border-radius:50%; background:url("<?php echo $image_url;?>files/images/common/reduce.png")no-repeat;background-size:contain;}
    .createRoom .mainPart .blueBack .selectPart .selectList .selectItem .compareProgressText{ color:#633201; float: left;line-height: 3vh; margin-left: 0.5vh; width:12vw; height:40px; font-size:2.1vh; }
    .createRoom .mainPart .blueBack .selectPart .selectList .selectItem .compareProgress{ float:left; margin-left:-10px; margin-right:20px; }
    .createRoom .mainPart .blueBack .selectPart .selectList .selectItem .compareProgressAdd{ float: left;height:3vh;width:3vh;border-radius:50%;background:url("<?php echo $image_url;?>files/images/common/add.png")no-repeat;background-size:contain; }
    .createRoom .mainPart .blueBack .selectPart .selectList .selectItem .compareProgress .compareRange{-webkit-appearance:none;  height:3vh; border-radius:15px; width:20vw;  background:-webkit-linear-gradient(#FFF949, #F08D02) no-repeat;  background-size: 0% 100%;}
    .createRoom .mainPart .blueBack .selectPart .selectList .selectItem .compareProgress .compareRange::-webkit-slider-thumb{  -webkit-appearance:none; height:4vh; width:4vh; background-image:url(<?php echo $image_url;?>files/images/common/slider.png);background-size:cover; border-radius:50%; margin-top:-0.5vh;}
    .createRoom .mainPart .blueBack .selectPart .selectList .selectItem .compareProgress .compareRange::-webkit-slider-runnable-track{ box-shadow: 0 1px #def3f8, inset 0  1px 1px #0d1112;  border-radius:15px; height:3vh;}
    .createRoom .mainPart .blueBack .selectPart .compareProgressItem{ float: left; width: 32vh; padding-top:1.2vh; }
    /**************************结束***************************************************************************************/
    /********************************开始*********看牌需下注的样式调整******************************************************/
    .createRoom .mainPart .blueBack .selectPart .seenPaiChessTitle{float: left;width:13vh;text-align: right;}
    .createRoom .mainPart .blueBack .selectPart .selectList .seenPaiChessItem{min-width:10vh;}
    .createRoom .mainPart .blueBack .selectPart .seenPaiChessList{width:30vh;}
    /*********************************结束********************************************************************************/
    /*******************************开始******************十人炸金花看牌样式***********************************************/
      .createRoom .mainPart .blueBack .selectPart .selectTenRoundTitle{width:12vh;}
   .createRoom .mainPart .blueBack .selectPart .selectTenRoundList{float: left; width: 32vh; padding-top:1.2vh;}
    .createRoom .mainPart .blueBack .selectPart .selectList .selectItem .seenTenTip{color:#54A802;line-height: 2.1vh;margin-bottom: 10px;}
    .createRoom .mainPart .blueBack .selectPart .selectList .selectItem .seenTenProgressReduce{float: left;height:3vh;width:3vh;border-radius:50%; background:url("<?php echo $image_url;?>files/images/common/reduce.png")no-repeat;background-size:contain;}
    .createRoom .mainPart .blueBack .selectPart .selectList .selectItem .seenTenProgressAdd{ float: left;height:3vh;width:3vh;border-radius:50%;background:url("<?php echo $image_url;?>files/images/common/add.png")no-repeat;background-size:contain; }
    .createRoom .mainPart .blueBack .selectPart .selectList .selectItem .seenTenProgressText{ color:#633201; float: left;line-height: 3vh; margin-left: 0.5vh; width:12vw; height:40px; font-size:2.1vh; }
    .createRoom .mainPart .blueBack .selectPart .selectList .selectItem .seenTenProgress{ float:left; margin-left:-10px; margin-right:20px; }
    .createRoom .mainPart .blueBack .selectPart .selectList .selectItem .seenTenProgress .seenTenRange{ -webkit-appearance:none;  height:3vh; border-radius:15px; width:20vw;  background:-webkit-linear-gradient(#FFF949, #F08D02) no-repeat;  background-size: 0% 100%; }
    .createRoom .mainPart .blueBack .selectPart .selectList .selectItem .seenTenProgress .seenTenRange::-webkit-slider-thumb{  -webkit-appearance:none; height:4vh; width:4vh;background-image:url(<?php echo $image_url;?>files/images/common/slider.png);background-size:cover;  border-radius:50%; margin-top:-0.5vh;}
    .createRoom .mainPart .blueBack .selectPart .selectList .selectItem .seenTenProgress .seenTenRange::-webkit-slider-runnable-track{ box-shadow: 0 1px #def3f8, inset 0  1px 1px #0d1112;  border-radius:15px; height:3vh;}
    /*******************************结束**********************************************************************************/
    /*******************************开始*******************十人炸金花比牌样式***********************************************/
    .createRoom .mainPart .blueBack .selectPart .selectList .selectItem .compareTenProgressReduce{float: left;height:3vh;width:3vh;border-radius:50%; background:url("<?php echo $image_url;?>files/images/common/reduce.png")no-repeat;background-size:contain;}
    .createRoom .mainPart .blueBack .selectPart .selectList .selectItem .compareTenProgressText{color:#633201; float: left;line-height: 3vh; margin-left: 0.5vh; width:12vw; height:40px; font-size:2.1vh; }
    .createRoom .mainPart .blueBack .selectPart .selectList .selectItem .compareTenProgress{ float:left; margin-left:-10px; margin-right:20px; }
    .createRoom .mainPart .blueBack .selectPart .selectList .selectItem .compareTenProgressAdd{ float: left;height:3vh;width:3vh;border-radius:50%;background:url("<?php echo $image_url;?>files/images/common/add.png")no-repeat;background-size:contain; }
    .createRoom .mainPart .blueBack .selectPart .selectList .selectItem .compareTenProgress .compareTenRange{-webkit-appearance:none;  height:3vh; border-radius:15px; width:20vw;  background:-webkit-linear-gradient(#FFF949, #F08D02) no-repeat;   background-size: 0% 100%;}
    .createRoom .mainPart .blueBack .selectPart .selectList .selectItem .compareTenProgress .compareTenRange::-webkit-slider-thumb{  -webkit-appearance:none; height:4vh; width:4vh; background-image:url(<?php echo $image_url;?>files/images/common/slider.png);background-size:cover; border-radius:50%; margin-top:-0.5vh;}
    .createRoom .mainPart .blueBack .selectPart .selectList .selectItem .compareTenProgress .compareTenRange::-webkit-slider-runnable-track{ box-shadow: 0 1px #def3f8, inset 0  1px 1px #0d1112;  border-radius:15px; height:3vh;}
    .createRoom .mainPart .blueBack .selectPart .compareProgressItem{ float: left; width: 32vh; padding-top:1.2vh; }
    /*上限样式，同比牌*/
    .createRoom .mainPart .blueBack .selectPart .selectList .selectItem .limitChooseProgressReduce{float: left;height:3vh;width:3vh;border-radius:50%; background:url("<?php echo $image_url;?>files/images/common/reduce.png")no-repeat;background-size:contain;}
    .createRoom .mainPart .blueBack .selectPart .selectList .selectItem .limitChooseProgressText{ color:#633201; float: left;line-height: 3vh; margin-left: 0.5vh; width:12vw; height:40px; font-size:2.1vh; }
    .createRoom .mainPart .blueBack .selectPart .selectList .selectItem .limitChooseProgress{ float:left; margin-left:-10px; margin-right:20px; }
    .createRoom .mainPart .blueBack .selectPart .selectList .selectItem .limitChooseProgressAdd{ float: left;height:3vh;width:3vh;border-radius:50%;background:url("<?php echo $image_url;?>files/images/common/add.png")no-repeat;background-size:contain; }
    .createRoom .mainPart .blueBack .selectPart .selectList .selectItem .limitChooseRange{-webkit-appearance:none;  height:3vh; border-radius:15px; width:20vw;  background:-webkit-linear-gradient(#FFF949, #F08D02) no-repeat;  background-size: 0% 100%;}
    .createRoom .mainPart .blueBack .selectPart .selectList .selectItem .limitChooseRange::-webkit-slider-thumb{  -webkit-appearance:none; height:4vh; width:4vh; background-image:url(<?php echo $image_url;?>files/images/common/slider.png);background-size:cover; border-radius:50%; margin-top:-0.5vh; }
    .createRoom .mainPart .blueBack .selectPart .selectList .selectItem .limitChooseRange::-webkit-slider-runnable-track{ box-shadow: 0 1px #def3f8, inset 0  1px 1px #0d1112;  border-radius:15px; height:3vh;}
    .createRoom .mainPart .blueBack .selectPart .limitChoose{ float: left; width: 32vh; padding-top:1.2vh; }
    /*******************************结束**********************************************************************************/
    /*******************************开始*******************大牌飘三叶的不能看牌样式*****************************************/
    .createRoom .mainPart .blueBack .selectPart .selectBigRoundTitle{width:12vh;}
   .createRoom .mainPart .blueBack .selectPart .selectBigRoundList{float: left; width: 32vh; padding-top:1.2vh;}
    .createRoom .mainPart .blueBack .selectPart .selectList .selectItem .seenBigTip{color:#54A802;line-height: 2.1vh;margin-bottom: 10px;}
    .createRoom .mainPart .blueBack .selectPart .selectList .selectItem .seenBigProgressReduce{float: left;height:3vh;width:3vh;border-radius:50%; background:url("<?php echo $image_url;?>files/images/common/reduce.png")no-repeat;background-size:contain;}
    .createRoom .mainPart .blueBack .selectPart .selectList .selectItem .seenBigProgressAdd{ float: left;height:3vh;width:3vh;border-radius:50%;background:url("<?php echo $image_url;?>files/images/common/add.png")no-repeat;background-size:contain;}
    .createRoom .mainPart .blueBack .selectPart .selectList .selectItem .seenBigProgressText{ float: left;line-height: 3vh; margin-left: 0.5vh; width:12vw; height:40px; font-size:2.1vh; color:#633201; }
    .createRoom .mainPart .blueBack .selectPart .selectList .selectItem .seenBigProgress{ float:left; margin-left:-10px; margin-right:20px; }
    .createRoom .mainPart .blueBack .selectPart .selectList .selectItem .seenBigProgress .seenBigRange{ -webkit-appearance:none;  height:3vh; border-radius:15px; width:20vw;  background:-webkit-linear-gradient(#FFF949, #F08D02) no-repeat;  background-size: 0% 100%; }
    .createRoom .mainPart .blueBack .selectPart .selectList .selectItem .seenBigProgress .seenBigRange::-webkit-slider-thumb{  -webkit-appearance:none; height:4vh; width:4vh; background-image:url(<?php echo $image_url;?>files/images/common/slider.png);background-size:cover; border-radius:50%;  margin-top:-0.5vh;}
    .createRoom .mainPart .blueBack .selectPart .selectList .selectItem .seenBigProgress .seenBigRange::-webkit-slider-runnable-track{ box-shadow: 0 1px #def3f8, inset 0  1px 1px #0d1112;  border-radius:15px; height:3vh;}
    /*******************************结束********************************************************************************/
    /*******************************开始********************大牌飘三叶比牌样式********************************************/
    .createRoom .mainPart .blueBack .selectPart .selectList .selectItem .compareBigProgressReduce{float: left;height:3vh;width:3vh;border-radius:50%; background:url("<?php echo $image_url;?>files/images/common/reduce.png")no-repeat;background-size:contain;}
    .createRoom .mainPart .blueBack .selectPart .selectList .selectItem .compareBigProgressText{ color:#633201; float: left;line-height: 3vh; margin-left: 0.5vh; width:12vw; height:40px; font-size:2.1vh; }
    .createRoom .mainPart .blueBack .selectPart .selectList .selectItem .compareBigProgress{ float:left; margin-left:-10px; margin-right:20px; }
    .createRoom .mainPart .blueBack .selectPart .selectList .selectItem .compareBigProgressAdd{ float: left;height:3vh;width:3vh;border-radius:50%;background:url("<?php echo $image_url;?>files/images/common/add.png")no-repeat;background-size:contain; }
    .createRoom .mainPart .blueBack .selectPart .selectList .selectItem .compareBigProgress .compareBigRange{-webkit-appearance:none;  height:3vh; border-radius:15px; width:20vw;  background:-webkit-linear-gradient(#FFF949, #F08D02) no-repeat;  background-size: 0% 100%;}
    .createRoom .mainPart .blueBack .selectPart .selectList .selectItem .compareBigProgress .compareBigRange::-webkit-slider-thumb{  -webkit-appearance:none; height:4vh; width:4vh; background-image:url(<?php echo $image_url;?>files/images/common/slider.png);background-size:cover; border-radius:50%; margin-top:-0.5vh;}
    .createRoom .mainPart .blueBack .selectPart .selectList .selectItem .compareBigProgress .compareBigRange::-webkit-slider-runnable-track{ box-shadow: 0 1px #def3f8, inset 0  1px 1px #0d1112;  border-radius:15px; height:3vh;}
    .createRoom .mainPart .blueBack .selectPart .compareProgressItem{ float: left; width: 32vh; padding-top:1.2vh; }
    /*******************************结束********************************************************************************/
    .createRoom .mainPart .blueBack .selectPart .selectList .selectXiPaiItem{float: left;position:relative;margin-left:1.8vh;min-width: 7vh;}
	.bankerSelected{position: absolute;width: 8vh;height: 5.4vh;color: #653604;text-align: center;font-size: 1.8vh;overflow: hidden;}
	.bankerSelected .img{position: absolute;top: 0;left: 0;width: 7.7vh;height: 5.4vh;}
	.bankerUnSelected{position: absolute;width: 8vh;height: 5.4vh;color: #9a6b3a;text-align: center;font-size: 1.8vh;overflow: hidden;}
	.bankerUnSelected .img{position: absolute;top: 0;left: 0;width: 7.7vh;height: 5.4vh;}
</style>

<div class="createRoom" ng-show="createInfo.isShow>0">
	<div class="createRoomBack"></div>
	<div class="mainPart">
        <div class="createTitle">
            <span data-text="房间设置">房间设置</span>
        </div>
       <div class='showPart'>
                   <div class="selectPartTitle">
				    创建房间,游戏未进行不消耗房卡呦
		</div>
		<div style="width: 45vh; margin: auto; position: relative; height: 5.4vh;line-height: 2.7vh;" ng-if="createInfo.isShow==3">
			<div ng-class="{true: 'bankerSelected', false: 'bankerUnSelected'}[createInfo.bull.banker_mode==3]" style="left: 1.35vh;" ng-click="selectBankerMode(3)">
				<img ng-src="<?php echo $image_url;?>files/images/common/mode_{{createInfo.bull.banker3}}.png" class="img" style="border-top-left-radius:4px;" v-if="createInfo.bull.banker_mode!=3">
				<p style="position: absolute;top: 0.4vh;left: 0; width: 100%;height: 2.7vh;text-align: center;">牛牛</p>
				<p style="position: absolute;top: 2.7vh;left: 0; width: 100%;height: 2.7vh;text-align: center;">上庄</p>
			</div>
			<div ng-class="{true: 'bankerSelected', false: 'bankerUnSelected'}[createInfo.bull.banker_mode==5]" style="left: 9.9vh;" ng-click="selectBankerMode(5)">
				<img ng-src="<?php echo $image_url;?>files/images/common/mode_{{createInfo.bull.banker5}}.png" class="img" v-if="createInfo.bull.banker_mode!=5">
				<p style="position: absolute;top: 0.4vh;left: 0; width: 100%;height: 2.7vh;text-align: center;">固定</p>
				<p style="position: absolute;top: 2.7vh;left: 0; width: 100%;height: 2.7vh;text-align: center;">庄家</p>
			</div>
			<div ng-class="{true: 'bankerSelected', false: 'bankerUnSelected'}[createInfo.bull.banker_mode==1]" style="left: 18.45vh;" ng-click="selectBankerMode(1)">
				<img ng-src="<?php echo $image_url;?>files/images/common/mode_{{createInfo.bull.banker1}}.png" class="img" v-if="createInfo.bull.banker_mode!=1">
				<p style="position: absolute;top: 0.4vh;left: 0; width: 100%;height: 2.7vh;text-align: center;">自由</p>
				<p style="position: absolute;top: 2.7vh;left: 0; width: 100%;height: 2.7vh;text-align: center;">抢庄</p>
			</div>
			<div ng-class="{true: 'bankerSelected', false: 'bankerUnSelected'}[createInfo.bull.banker_mode==2]" style="left: 27vh;" ng-click="selectBankerMode(2)">
				<img ng-src="<?php echo $image_url;?>files/images/common/mode_{{createInfo.bull.banker2}}.png" class="img" v-if="createInfo.bull.banker_mode!=2">
				<p style="position: absolute;top: 0.4vh;left: 0; width: 100%;height: 2.7vh;text-align: center;">明牌</p>
				<p style="position: absolute;top: 2.7vh;left: 0; width: 100%;height: 2.7vh;text-align: center;">抢庄</p>
			</div>
			<div ng-class="{true: 'bankerSelected', false: 'bankerUnSelected'}[createInfo.bull.banker_mode==4]" style="left: 35.55vh;" ng-click="selectBankerMode(4)">
				<img ng-src="<?php echo $image_url;?>files/images/common/mode_{{createInfo.bull.banker4}}.png" class="img" style="border-top-right-radius:4px;" v-if="createInfo.bull.banker_mode!=4">
				<p style="position: absolute;top: 0.4vh;left: 0; width: 100%;height: 2.7vh;text-align: center;">通比</p>
				<p style="position: absolute;top: 2.7vh;left: 0; width: 100%;height: 2.7vh;text-align: center;">牛牛</p>
			</div>
		</div>

		<div style="width: 45vh; margin: auto; position: relative; height: 5.4vh;line-height: 2.7vh;" ng-if="createInfo.isShow==5">
			<div ng-class="{true: 'bankerSelected', false: 'bankerUnSelected'}[createInfo.bull9.banker_mode==3]" style="left: 1.35vh;" ng-click="selectBankerMode9(3)">
				<img ng-src="<?php echo $image_url;?>files/images/common/mode_{{createInfo.bull9.banker3}}.png" class="img" style="border-top-left-radius:4px;" v-if="createInfo.bull9.banker_mode!=3">
				<p style="position: absolute;top: 0.4vh;left: 0; width: 100%;height: 2.7vh;text-align: center;">牛牛</p>
				<p style="position: absolute;top: 2.7vh;left: 0; width: 100%;height: 2.7vh;text-align: center;">上庄</p>
			</div>
			<div ng-class="{true: 'bankerSelected', false: 'bankerUnSelected'}[createInfo.bull9.banker_mode==5]" style="left: 9.9vh;" ng-click="selectBankerMode9(5)">
				<img ng-src="<?php echo $image_url;?>files/images/common/mode_{{createInfo.bull9.banker5}}.png" class="img" v-if="createInfo.bull9.banker_mode!=5">
				<p style="position: absolute;top: 0.4vh;left: 0; width: 100%;height: 2.7vh;text-align: center;">固定</p>
				<p style="position: absolute;top: 2.7vh;left: 0; width: 100%;height: 2.7vh;text-align: center;">庄家</p>
			</div>
			<div ng-class="{true: 'bankerSelected', false: 'bankerUnSelected'}[createInfo.bull9.banker_mode==1]" style="left: 18.45vh;" ng-click="selectBankerMode9(1)">
				<img ng-src="<?php echo $image_url;?>files/images/common/mode_{{createInfo.bull9.banker1}}.png" class="img" v-if="createInfo.bull9.banker_mode!=1">
				<p style="position: absolute;top: 0.4vh;left: 0; width: 100%;height: 2.7vh;text-align: center;">自由</p>
				<p style="position: absolute;top: 2.7vh;left: 0; width: 100%;height: 2.7vh;text-align: center;">抢庄</p>
			</div>
			<div ng-class="{true: 'bankerSelected', false: 'bankerUnSelected'}[createInfo.bull9.banker_mode==2]" style="left: 27vh;" ng-click="selectBankerMode9(2)">
				<img ng-src="<?php echo $image_url;?>files/images/common/mode_{{createInfo.bull9.banker2}}.png" class="img" v-if="createInfo.bull9.banker_mode!=2">
				<p style="position: absolute;top: 0.4vh;left: 0; width: 100%;height: 2.7vh;text-align: center;">明牌</p>
				<p style="position: absolute;top: 2.7vh;left: 0; width: 100%;height: 2.7vh;text-align: center;">抢庄</p>
			</div>
			<div ng-class="{true: 'bankerSelected', false: 'bankerUnSelected'}[createInfo.bull9.banker_mode==4]" style="left: 35.55vh;" ng-click="selectBankerMode9(4)">
				<img ng-src="<?php echo $image_url;?>files/images/common/mode_{{createInfo.bull9.banker4}}.png" class="img" style="border-top-right-radius:4px;" v-if="createInfo.bull9.banker_mode!=4">
				<p style="position: absolute;top: 0.4vh;left: 0; width: 100%;height: 2.7vh;text-align: center;">通比</p>
				<p style="position: absolute;top: 2.7vh;left: 0; width: 100%;height: 2.7vh;text-align: center;">牛牛</p>
			</div>
		</div>

		<div style="width: 45vh; margin: auto; position: relative; height: 5.4vh;line-height: 2.7vh;" ng-if="createInfo.isShow==90">
			<div ng-class="{true: 'bankerSelected', false: 'bankerUnSelected'}[createInfo.vbf.game==93]" style="left: 1.35vh;" ng-click="selectVBF(93)">
				<img ng-src="<?php echo $image_url;?>files/images/common/mode_{{createInfo.vbf.game93}}.png" class="img" v-if="createInfo.vbf.game!=93">
				<p style="position: absolute;top: 0.4vh;left: 0; width: 100%;height: 2.7vh;text-align: center;">六人</p>
				<p style="position: absolute;top: 2.7vh;left: 0; width: 100%;height: 2.7vh;text-align: center;">牛牛</p>
			</div>

			<div ng-class="{true: 'bankerSelected', false: 'bankerUnSelected'}[createInfo.vbf.game==91]" style="left: 9.9vh;" ng-click="selectVBF(91)">
				<img ng-src="<?php echo $image_url;?>files/images/common/mode_{{createInfo.vbf.game91}}.png" class="img" v-if="createInfo.vbf.game!=91">
				<p style="position: absolute;top: 0.4vh;left: 0; width: 100%;height: 2.7vh;text-align: center;">九人</p>
				<p style="position: absolute;top: 2.7vh;left: 0; width: 100%;height: 2.7vh;text-align: center;">牛牛</p>
			</div>

			<div ng-class="{true: 'bankerSelected', false: 'bankerUnSelected'}[createInfo.vbf.game==94]" style="left: 18.45vh;" ng-click="selectVBF(94)">
				<img ng-src="<?php echo $image_url;?>files/images/common/mode_{{createInfo.vbf.game94}}.png" class="img" v-if="createInfo.vbf.game!=94">
				<p style="position: absolute;top: 0.4vh;left: 0; width: 100%;height: 2.7vh;text-align: center;">十二人</p>
				<p style="position: absolute;top: 2.7vh;left: 0; width: 100%;height: 2.7vh;text-align: center;">牛牛</p>
			</div>

			<div ng-class="{true: 'bankerSelected', false: 'bankerUnSelected'}[createInfo.vbf.game==92]" style="left: 27vh;" ng-click="selectVBF(92)">
				<img ng-src="<?php echo $image_url;?>files/images/common/mode_{{createInfo.vbf.game92}}.png" class="img" style="border-top-right-radius:4px;" v-if="createInfo.vbf.game!=92">
				<p style="position: absolute;top: 0.4vh;left: 0; width: 100%;height: 2.7vh;text-align: center;">六人</p>
				<p style="position: absolute;top: 2.7vh;left: 0; width: 100%;height: 2.7vh;text-align: center;">金花</p>
			</div>

		</div>

        <div style="width: 45vh; margin: auto; position: relative; height: 5.4vh;line-height: 2.7vh;" ng-if="createInfo.isShow==8">
            <div ng-class="{true: 'bankerSelected', false: 'bankerUnSelected'}[createInfo.tbull.banker_mode==3]" style="left: 1.35vh;" ng-click="selectBankerTBull(3)">
                <img ng-src="<?php echo $image_url;?>files/images/common/mode_{{createInfo.tbull.banker3}}.png" class="img" style="border-top-left-radius:4px;" v-if="createInfo.tbull.banker_mode!=3">
                <p style="position: absolute;top: 0.4vh;left: 0; width: 100%;height: 2.7vh;text-align: center;">牛牛</p>
                <p style="position: absolute;top: 2.7vh;left: 0; width: 100%;height: 2.7vh;text-align: center;">上庄</p>
            </div>
            <div ng-class="{true: 'bankerSelected', false: 'bankerUnSelected'}[createInfo.tbull.banker_mode==5]" style="left: 9.9vh;" ng-click="selectBankerTBull(5)">
                <img ng-src="<?php echo $image_url;?>files/images/common/mode_{{createInfo.tbull.banker5}}.png" class="img" v-if="createInfo.tbull.banker_mode!=5">
                <p style="position: absolute;top: 0.4vh;left: 0; width: 100%;height: 2.7vh;text-align: center;">固定</p>
                <p style="position: absolute;top: 2.7vh;left: 0; width: 100%;height: 2.7vh;text-align: center;">庄家</p>
            </div>
            <div ng-class="{true: 'bankerSelected', false: 'bankerUnSelected'}[createInfo.tbull.banker_mode==1]" style="left: 18.45vh;" ng-click="selectBankerTBull(1)">
                <img ng-src="<?php echo $image_url;?>files/images/common/mode_{{createInfo.tbull.banker1}}.png" class="img" v-if="createInfo.tbull.banker_mode!=1">
                <p style="position: absolute;top: 0.4vh;left: 0; width: 100%;height: 2.7vh;text-align: center;">自由</p>
                <p style="position: absolute;top: 2.7vh;left: 0; width: 100%;height: 2.7vh;text-align: center;">抢庄</p>
            </div>
            <div ng-class="{true: 'bankerSelected', false: 'bankerUnSelected'}[createInfo.tbull.banker_mode==2]" style="left: 27vh;" ng-click="selectBankerTBull(2)">
                <img ng-src="<?php echo $image_url;?>files/images/common/mode_{{createInfo.tbull.banker2}}.png" class="img" v-if="createInfo.tbull.banker_mode!=2">
                <p style="position: absolute;top: 0.4vh;left: 0; width: 100%;height: 2.7vh;text-align: center;">明牌</p>
                <p style="position: absolute;top: 2.7vh;left: 0; width: 100%;height: 2.7vh;text-align: center;">抢庄</p>
            </div>
            <div ng-class="{true: 'bankerSelected', false: 'bankerUnSelected'}[createInfo.tbull.banker_mode==4]" style="left: 35.55vh;" ng-click="selectBankerTBull(4)">
                <img ng-src="<?php echo $image_url;?>files/images/common/mode_{{createInfo.tbull.banker4}}.png" class="img" style="border-top-right-radius:4px;" v-if="createInfo.tbull.banker_mode!=4">
                <p style="position: absolute;top: 0.4vh;left: 0; width: 100%;height: 2.7vh;text-align: center;">通比</p>
                <p style="position: absolute;top: 2.7vh;left: 0; width: 100%;height: 2.7vh;text-align: center;">牛牛</p>
            </div>
        </div>
        <!--13人牛牛模式选择 start-->
        <div style="width: 45vh; margin: auto; position: relative; height: 5.4vh;line-height: 2.7vh;" ng-if="createInfo.isShow==9">
            <div ng-class="{true: 'bankerSelected', false: 'bankerUnSelected'}[createInfo.fbull.banker_mode==3]" style="left: 1.35vh;" ng-click="selectBankerFBull(3)">
                <img ng-src="<?php echo $image_url;?>files/images/common/mode_{{createInfo.fbull.banker3}}.png" class="img" style="border-top-left-radius:4px;" v-if="createInfo.fbull.banker_mode!=3">
                <p style="position: absolute;top: 0.4vh;left: 0; width: 100%;height: 2.7vh;text-align: center;">牛牛</p>
                <p style="position: absolute;top: 2.7vh;left: 0; width: 100%;height: 2.7vh;text-align: center;">上庄</p>
            </div>
            <div ng-class="{true: 'bankerSelected', false: 'bankerUnSelected'}[createInfo.fbull.banker_mode==5]" style="left: 9.9vh;" ng-click="selectBankerFBull(5)">
                <img ng-src="<?php echo $image_url;?>files/images/common/mode_{{createInfo.fbull.banker5}}.png" class="img" v-if="createInfo.fbull.banker_mode!=5">
                <p style="position: absolute;top: 0.4vh;left: 0; width: 100%;height: 2.7vh;text-align: center;">固定</p>
                <p style="position: absolute;top: 2.7vh;left: 0; width: 100%;height: 2.7vh;text-align: center;">庄家</p>
            </div>
            <div ng-class="{true: 'bankerSelected', false: 'bankerUnSelected'}[createInfo.fbull.banker_mode==1]" style="left: 18.45vh;" ng-click="selectBankerFBull(1)">
                <img ng-src="<?php echo $image_url;?>files/images/common/mode_{{createInfo.fbull.banker1}}.png" class="img" v-if="createInfo.fbull.banker_mode!=1">
                <p style="position: absolute;top: 0.4vh;left: 0; width: 100%;height: 2.7vh;text-align: center;">自由</p>
                <p style="position: absolute;top: 2.7vh;left: 0; width: 100%;height: 2.7vh;text-align: center;">抢庄</p>
            </div>
            <div ng-class="{true: 'bankerSelected', false: 'bankerUnSelected'}[createInfo.fbull.banker_mode==2]" style="left: 27vh;" ng-click="selectBankerFBull(2)">
                <img ng-src="<?php echo $image_url;?>files/images/common/mode_{{createInfo.fbull.banker2}}.png" class="img" v-if="createInfo.fbull.banker_mode!=2">
                <p style="position: absolute;top: 0.4vh;left: 0; width: 100%;height: 2.7vh;text-align: center;">明牌</p>
                <p style="position: absolute;top: 2.7vh;left: 0; width: 100%;height: 2.7vh;text-align: center;">抢庄</p>
            </div>
            <div ng-class="{true: 'bankerSelected', false: 'bankerUnSelected'}[createInfo.fbull.banker_mode==4]" style="left: 35.55vh;" ng-click="selectBankerFBull(4)">
                <img ng-src="<?php echo $image_url;?>files/images/common/mode_{{createInfo.fbull.banker4}}.png" class="img" style="border-top-right-radius:4px;" v-if="createInfo.fbull.banker_mode!=4">
                <p style="position: absolute;top: 0.4vh;left: 0; width: 100%;height: 2.7vh;text-align: center;">通比</p>
                <p style="position: absolute;top: 2.7vh;left: 0; width: 100%;height: 2.7vh;text-align: center;">牛牛</p>
            </div>
        </div>
        <!--13人牛牛模式选择 end-->

        <div style="width: 45vh; margin: auto; position: relative; height: 5.4vh;line-height: 2.7vh;" ng-if="createInfo.isShow==36">
            <div ng-class="{true: 'bankerSelected', false: 'bankerUnSelected'}[createInfo.sangong.banker_mode==1]" style="left: 1.35vh;" ng-click="selectBankerSangong(1)">
                <img ng-src="<?php echo $image_url;?>files/images/common/mode_{{createInfo.sangong.banker1}}.png" class="img" v-if="createInfo.sangong.banker_mode!=1">
                <p style="position: absolute;top: 0.4vh;left: 0; width: 100%;height: 2.7vh;text-align: center;">自由</p>
                <p style="position: absolute;top: 2.7vh;left: 0; width: 100%;height: 2.7vh;text-align: center;">抢庄</p>
            </div>
            <div ng-class="{true: 'bankerSelected', false: 'bankerUnSelected'}[createInfo.sangong.banker_mode==2]" style="left: 9.9vh;" ng-click="selectBankerSangong(2)">
                <img ng-src="<?php echo $image_url;?>files/images/common/mode_{{createInfo.sangong.banker2}}.png" class="img" v-if="createInfo.sangong.banker_mode!=2">
                <p style="position: absolute;top: 0.4vh;left: 0; width: 100%;height: 2.7vh;text-align: center;">明牌</p>
                <p style="position: absolute;top: 2.7vh;left: 0; width: 100%;height: 2.7vh;text-align: center;">抢庄</p>
            </div>
        </div>

        <div style="width: 45vh; margin: auto; position: relative; height: 5.4vh;line-height: 2.7vh;" ng-if="createInfo.isShow==37">
            <div ng-class="{true: 'bankerSelected', false: 'bankerUnSelected'}[createInfo.nsangong.banker_mode==1]" style="left: 1.35vh;" ng-click="selectBankerNSangong(1)">
                <img ng-src="<?php echo $image_url;?>files/images/common/mode_{{createInfo.nsangong.banker1}}.png" class="img" v-if="createInfo.nsangong.banker_mode!=1">
                <p style="position: absolute;top: 0.4vh;left: 0; width: 100%;height: 2.7vh;text-align: center;">自由</p>
                <p style="position: absolute;top: 2.7vh;left: 0; width: 100%;height: 2.7vh;text-align: center;">抢庄</p>
            </div>
            <div ng-class="{true: 'bankerSelected', false: 'bankerUnSelected'}[createInfo.nsangong.banker_mode==2]" style="left: 9.9vh;" ng-click="selectBankerNSangong(2)">
                <img ng-src="<?php echo $image_url;?>files/images/common/mode_{{createInfo.nsangong.banker2}}.png" class="img" v-if="createInfo.nsangong.banker_mode!=2">
                <p style="position: absolute;top: 0.4vh;left: 0; width: 100%;height: 2.7vh;text-align: center;">明牌</p>
                <p style="position: absolute;top: 2.7vh;left: 0; width: 100%;height: 2.7vh;text-align: center;">抢庄</p>
            </div>
        </div>

        <div style="width: 45vh; margin: auto; position: relative; height: 5.4vh;line-height: 2.7vh;" ng-if="createInfo.isShow==38">
            <div ng-class="{true: 'bankerSelected', false: 'bankerUnSelected'}[createInfo.tsangong.banker_mode==1]" style="left: 1.35vh;" ng-click="selectBankerTSangong(1)">
                <img ng-src="<?php echo $image_url;?>files/images/common/mode_{{createInfo.tsangong.banker1}}.png" class="img" v-if="createInfo.tsangong.banker_mode!=1">
                <p style="position: absolute;top: 0.4vh;left: 0; width: 100%;height: 2.7vh;text-align: center;">自由</p>
                <p style="position: absolute;top: 2.7vh;left: 0; width: 100%;height: 2.7vh;text-align: center;">抢庄</p>
            </div>
            <div ng-class="{true: 'bankerSelected', false: 'bankerUnSelected'}[createInfo.tsangong.banker_mode==2]" style="left: 9.9vh;" ng-click="selectBankerTSangong(2)">
                <img ng-src="<?php echo $image_url;?>files/images/common/mode_{{createInfo.tsangong.banker2}}.png" class="img" v-if="createInfo.tsangong.banker_mode!=2">
                <p style="position: absolute;top: 0.4vh;left: 0; width: 100%;height: 2.7vh;text-align: center;">明牌</p>
                <p style="position: absolute;top: 2.7vh;left: 0; width: 100%;height: 2.7vh;text-align: center;">抢庄</p>
            </div>
        </div>

        <div style="width: 45vh; margin: auto; position: relative; height: 5.4vh;line-height: 2.7vh;" ng-if="createInfo.isShow==71">
            <div ng-class="{true: 'bankerSelected', false: 'bankerUnSelected'}[createInfo.lbull.banker_mode==3]" style="left: 1.35vh;" ng-click="selectBankerLBull(3)">
                <img ng-src="<?php echo $image_url;?>files/images/common/mode_{{createInfo.lbull.banker3}}.png" class="img" style="border-top-left-radius:4px;" v-if="createInfo.lbull.banker_mode!=3">
                <p style="position: absolute;top: 0.4vh;left: 0; width: 100%;height: 2.7vh;text-align: center;">牛牛</p>
                <p style="position: absolute;top: 2.7vh;left: 0; width: 100%;height: 2.7vh;text-align: center;">上庄</p>
            </div>
            <div ng-class="{true: 'bankerSelected', false: 'bankerUnSelected'}[createInfo.lbull.banker_mode==5]" style="left: 9.9vh;" ng-click="selectBankerLBull(5)">
                <img ng-src="<?php echo $image_url;?>files/images/common/mode_{{createInfo.lbull.banker5}}.png" class="img" v-if="createInfo.lbull.banker_mode!=5">
                <p style="position: absolute;top: 0.4vh;left: 0; width: 100%;height: 2.7vh;text-align: center;">固定</p>
                <p style="position: absolute;top: 2.7vh;left: 0; width: 100%;height: 2.7vh;text-align: center;">庄家</p>
            </div>
            <div ng-class="{true: 'bankerSelected', false: 'bankerUnSelected'}[createInfo.lbull.banker_mode==1]" style="left: 18.45vh;" ng-click="selectBankerLBull(1)">
                <img ng-src="<?php echo $image_url;?>files/images/common/mode_{{createInfo.lbull.banker1}}.png" class="img" v-if="createInfo.lbull.banker_mode!=1">
                <p style="position: absolute;top: 0.4vh;left: 0; width: 100%;height: 2.7vh;text-align: center;">自由</p>
                <p style="position: absolute;top: 2.7vh;left: 0; width: 100%;height: 2.7vh;text-align: center;">抢庄</p>
            </div>
            <div ng-class="{true: 'bankerSelected', false: 'bankerUnSelected'}[createInfo.lbull.banker_mode==2]" style="left: 27vh;" ng-click="selectBankerLBull(2)">
                <img ng-src="<?php echo $image_url;?>files/images/common/mode_{{createInfo.lbull.banker2}}.png" class="img" v-if="createInfo.lbull.banker_mode!=2">
                <p style="position: absolute;top: 0.4vh;left: 0; width: 100%;height: 2.7vh;text-align: center;">明牌</p>
                <p style="position: absolute;top: 2.7vh;left: 0; width: 100%;height: 2.7vh;text-align: center;">抢庄</p>
            </div>
            <div ng-class="{true: 'bankerSelected', false: 'bankerUnSelected'}[createInfo.lbull.banker_mode==4]" style="left: 35.55vh;" ng-click="selectBankerLBull(4)">
                <img ng-src="<?php echo $image_url;?>files/images/common/mode_{{createInfo.lbull.banker4}}.png" class="img" style="border-top-right-radius:4px;" v-if="createInfo.lbull.banker_mode!=4">
                <p style="position: absolute;top: 0.4vh;left: 0; width: 100%;height: 2.7vh;text-align: center;">通比</p>
                <p style="position: absolute;top: 2.7vh;left: 0; width: 100%;height: 2.7vh;text-align: center;">牛牛</p>
            </div>
        </div>

		<div class="blueBack">

			<div class="flowerRull" ng-if="createInfo.isShow==1">
                <div class="selectPart chipItems" style="height:15vh;">
                    <div class="selectTitle">底分：</div>
                    <div class="selectList" >
                        <div class="selectItem"  ng-click="selectChange(10,2)">
                            <div class="selectBox">
                                <img src="<?php echo $image_url;?>files/images/common/tick.png" ng-show="createInfo.flower.default_score == 2"/>
                            </div>
                            <div class="selectText" >2</div>
                        </div>
                        <div class="selectItem" ng-click="selectChange(10,4)" >
                            <div class="selectBox" >
                                <img src="<?php echo $image_url;?>files/images/common/tick.png" ng-show="createInfo.flower.default_score == 4"/>
                            </div>
                            <div class="selectText" >4</div>
                        </div>
                        <div class="selectItem" ng-click="selectChange(10,10)" >
                            <div class="selectBox" >
                                <img src="<?php echo $image_url;?>files/images/common/tick.png" ng-show="createInfo.flower.default_score == 10"/>
                            </div>
                            <div class="selectText" >10</div>
                        </div>
                        <div class="selectItem"  ng-click="selectChange(10,20)">
                            <div class="selectBox">
                                <img src="<?php echo $image_url;?>files/images/common/tick.png" ng-show="createInfo.flower.default_score == 20"/>
                            </div>
                            <div class="selectText" >20</div>
                        </div>
                        <div class="selectItem"  ng-click="selectChange(10,40)">
                            <div class="selectBox">
                                <img src="<?php echo $image_url;?>files/images/common/tick.png" ng-show="createInfo.flower.default_score == 40"/>
                            </div>
                            <div class="selectText" >40</div>
                        </div>
                        <div class="selectItem"  ng-click="selectChange(10,100)">
                            <div class="selectBox">
                                <img src="<?php echo $image_url;?>files/images/common/tick.png" ng-show="createInfo.flower.default_score == 100"/>
                            </div>
                            <div class="selectText" >100</div>
                        </div>
                        <div class="selectItem"  ng-click="selectChange(10,200)">
                            <div class="selectBox">
                                <img src="<?php echo $image_url;?>files/images/common/tick.png" ng-show="createInfo.flower.default_score == 200"/>
                            </div>
                            <div class="selectText" >200</div>
                        </div>
                        <div class="selectItem"  ng-click="selectChange(10,0)">
                            <div class="selectBox">
                                <img src="<?php echo $image_url;?>files/images/common/tick.png" ng-show="createInfo.flower.default_score == 0"/>
                            </div>
                            <div class="selectText" style="position: relative;">
                                <select class="flowerSelect" ng-model='createInfo.flower.default_score_select' ng-options="x for x in defaultScores">
                                </select>
                                <div class="selectIcon">
                                    <div class="sanjiao"></div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
				<div class="selectPart chipItems" style="height:20vh;">
                    <div class="selectTitle">筹码：</div>
                    <div class="selectList" >
                        <div class="selectItem"  ng-click="selectChange(1,2)">
                            <div class="selectBox">
                                <img src="<?php echo $image_url;?>files/images/common/tick.png" ng-show="createInfo.flower.chip_type.indexOf(2) !== -1"/>
                            </div>
                            <div class="selectText" >2/4</div>
                        </div>
                        <div class="selectItem" ng-click="selectChange(1,4)" >
                            <div class="selectBox" >
                                <img src="<?php echo $image_url;?>files/images/common/tick.png" ng-show="createInfo.flower.chip_type.indexOf(4) !== -1"/>
                            </div>
                            <div class="selectText" >4/8</div>
                        </div>
                        <div class="selectItem" ng-click="selectChange(1,5)" >
                            <div class="selectBox" >
                                <img src="<?php echo $image_url;?>files/images/common/tick.png" ng-show="createInfo.flower.chip_type.indexOf(5) !== -1"/>
                            </div>
                            <div class="selectText" >5/10</div>
						</div>
                        <div class="selectItem"  ng-click="selectChange(1,8)">
                            <div class="selectBox">
                                <img src="<?php echo $image_url;?>files/images/common/tick.png" ng-show="createInfo.flower.chip_type.indexOf(8) !== -1"/>
                            </div>
                            <div class="selectText" >8/16</div>
                        </div>
                        <div class="selectItem"  ng-click="selectChange(1,10)">
                            <div class="selectBox">
                                <img src="<?php echo $image_url;?>files/images/common/tick.png" ng-show="createInfo.flower.chip_type.indexOf(10) !== -1"/>
                            </div>
                            <div class="selectText" >10/20</div>
                        </div>
                        <div class="selectItem"  ng-click="selectChange(1,20)">
                            <div class="selectBox">
                                <img src="<?php echo $image_url;?>files/images/common/tick.png" ng-show="createInfo.flower.chip_type.indexOf(20) !== -1"/>
                            </div>
                            <div class="selectText" >20/40</div>
                        </div>
                        <div class="selectItem"  ng-click="selectChange(1,40)">
                            <div class="selectBox">
                                <img src="<?php echo $image_url;?>files/images/common/tick.png" ng-show="createInfo.flower.chip_type.indexOf(40) !== -1"/>
                            </div>
                            <div class="selectText" >40/80</div>
                        </div>
                        <div class="selectItem"  ng-click="selectChange(1,50)">
                            <div class="selectBox">
                                <img src="<?php echo $image_url;?>files/images/common/tick.png" ng-show="createInfo.flower.chip_type.indexOf(50) !== -1"/>
                            </div>
                            <div class="selectText" >50/100</div>
                        </div>
                        <div class="selectItem"  ng-click="selectChange(1,100)">
                            <div class="selectBox">
                                <img src="<?php echo $image_url;?>files/images/common/tick.png" ng-show="createInfo.flower.chip_type.indexOf(100) !== -1"/>
                            </div>
                            <div class="selectText" >100/200</div>
                        </div>
                        <div class="selectItem" style="width: 20vh;">
                            <div class="selectText" >请任选四组筹码</div>
                        </div>
                    </div>
                </div>
                <div class="selectPart timeItems" style="height:5.5vh;">
                    <div class="selectTitle">时间：</div>
                    <div class="selectList" >
                        <label class="selectItem" style="position: relative;">
                            <span style="color:#633201;">准备</span>
                            <select class="flowerReadySelect" ng-model="createInfo.flower.countDown[0]" ng-options="x for x in defaultTime5To10">
                            </select>
                            <div class="readySelectIcon">
                                    <div class="sanjiao"></div>
                            </div>
                        </label>
                        <label class="selectItem" style="position: relative;">
                            <span style="color:#633201;">下注</span>
                            <select class="flowerChipSelect" ng-model="createInfo.flower.countDown[1]" ng-options="x for x in defaultTime5To20">
                            </select>
                            <div class="readySelectIcon">
                                    <div class="sanjiao"></div>
                            </div>
                        </label>
                    </div>
                </div>
                <div class="selectPart" style="height:10vh;">
					<div class="selectTitle">看牌：</div>
					<div class="selectList selectRoundList" >
                    <div class="selectItem">
							<div class="selectText seenTip">低于积分池将不能看牌</div>
						</div>
                        <div class="selectItem">
							<div class="seenProgressReduce" ng-click="seenReduce()" ></div>
							<div class="seenProgressText" >{{createInfo.flower.seenProgress}}</div>
                            <div class="seenProgress">
                                <input type="range" min="0" max="2000" step="100" class="seenRange" ng-model="seenProgressValue"
                                ng-change="seenProgressChange(seenProgressValue)">
                            </div>
                            <div class="seenProgressAdd" ng-click="seenAdd()"></div>
						</div>
					</div>
				</div>
                <div class="selectPart" style="height:15vh;">
					<div class="selectTitle">比牌：</div>
					<div class="selectList" >
						<div class="selectItem"  ng-click="selectChange(7)">
							<div class="selectBox"><img src="<?php echo $image_url;?>files/images/common/tick.png" ng-show="createInfo.flower.raceCard==false"/></div>
							<div class="selectText" >首轮禁止比牌</div>
						</div>
                        <div class="selectItem">
							<div class="selectText"  style="color:#54A802;">低于积分池将不能比牌</div>
						</div>
                        <div class="selectItem compareProgressItem">
							<div class="compareProgressReduce" ng-click="compareReduce()"></div>
							<div class="compareProgressText">{{createInfo.flower.compareProgress}}</div>
                            <div class="compareProgress">
                                <input type="range" min="0" max="2000" step="100" class="compareRange" ng-model="compareProgressValue"
                                ng-change="compareProgressChange(compareProgressValue)">
                            </div>
                            <div class="compareProgressAdd" ng-click="compareAdd()"></div>
						</div>
					</div>
				</div>

				<div class="selectPart" style="height:5.5vh;">
					<div class="selectTitle">局数：</div>
					<div class="selectList">
						<div class="selectItem" ng-click="selectChange(3,1)">
							<div class="selectBox" ><img src="<?php echo $image_url;?>files/images/common/tick.png" ng-show="createInfo.flower.ticket_count==1" /></div>
							<div class="selectText" >10局X1房卡</div>
						</div>
						<div class="selectItem" ng-click="selectChange(3,2)">
							<div class="selectBox" ><img src="<?php echo $image_url;?>files/images/common/tick.png" ng-show="createInfo.flower.ticket_count==2" /></div>
							<div class="selectText">20局X2房卡</div>
						</div>
					</div>
				</div>

				<div class="selectPart">
					<div class="selectTitle">上限：</div>
					<div class="selectList">
						<div class="limitChoose selectItem" style="height: 2.5vh;display: flex; align-items: center; justify-content: space-between">
						<div class="limitChooseProgressReduce" ng-click="downUpperLimit()"></div>
						<div class="limitChooseProgressText" style="height: auto !important;">{{createInfo.flower.upper_limit == 0? '无上限': createInfo.flower.upper_limit}}</div>
						<div class="limitChooseProgress">
							<input type="range" min="0" max="2000" step="100"
							class="limitChooseRange"
							ng-style="{'background-size': (createInfo.flower.upper_limit)/20+ '% 100%'}"
							ng-model="createInfo.flower.upper_limit" />
						</div>
						<div class="limitChooseProgressAdd" ng-click="upUpperLimit()"></div>
					</div>
				</div>
		</div>
                <div class="selectPart">
                    <div class="selectTitle">喜牌：</div>
                    <div class="selectList">
                        <div class="selectItem">
                            <div class="selectXiPaiText" >豹子，同花顺为喜牌，获得玩家奖励</div>
                        </div>
                        <div class="selectItem selectXiPaiItem" ng-click="selectChange(9,0)">
                            <div class="selectBox" >
                                <img src="<?php echo $image_url;?>files/images/common/tick.png" ng-show="createInfo.flower.extraRewards==0" />
                            </div>
                            <div class="selectText">0</div>
                        </div>
                        <div class="selectItem selectXiPaiItem" ng-click="selectChange(9,5)">
                            <div class="selectBox" >
                                <img src="<?php echo $image_url;?>files/images/common/tick.png" ng-show="createInfo.flower.extraRewards==5" />
                            </div>
                            <div class="selectText">5</div>
                        </div>
                        <div class="selectItem selectXiPaiItem" ng-click="selectChange(9,10)">
                            <div class="selectBox" >
                                <img src="<?php echo $image_url;?>files/images/common/tick.png" ng-show="createInfo.flower.extraRewards==10" />
                            </div>
                            <div class="selectText">10</div>
                        </div>
                        <div class="selectItem selectXiPaiItem" ng-click="selectChange(9,20)">
                            <div class="selectBox" >
                                <img src="<?php echo $image_url;?>files/images/common/tick.png" ng-show="createInfo.flower.extraRewards==20" />
                            </div>
                            <div class="selectText">20</div>
                        </div>
                        <div class="selectItem selectXiPaiItem" ng-click="selectChange(9,40)">
                            <div class="selectBox" >
                                <img src="<?php echo $image_url;?>files/images/common/tick.png" ng-show="createInfo.flower.extraRewards==40" />
                            </div>
                            <div class="selectText">40</div>
                        </div>
                    </div>
                </div>
                <div class="selectPart">
                    <div class="selectTitle">特殊：</div>
                    <div class="selectList">
                        <div class="selectItem selectXiPaiItem" ng-click="selectChange(11)">
                            <div class="selectBox" >
                                <img src="<?php echo $image_url;?>files/images/common/tick.png" ng-show="createInfo.flower.allow235GTPanther==1" />
                            </div>
                            <div class="selectText">235吃豹子</div>
                        </div>
                    </div>
                </div>
            </div>

            <div class="flowerRull" ng-if="createInfo.isShow==90&&createInfo.vbf.game==92">
                <div class="selectPart" style="height:15vh;">
                    <div class="selectTitle">筹码：</div>
                    <div class="selectList" >
                        <div class="selectItem"  ng-click="selectChange(1,1)">
                            <div class="selectBox" ><img src="<?php echo $image_url;?>files/images/common/tick.png" ng-show="createInfo.vflower6.chip_type==1"/></div>
                            <div class="selectText" >2/4,4/8,8/16,10/20</div>
                        </div>
                        <div class="selectItem" ng-click="selectChange(1,2)" >
                            <div class="selectBox" ><img src="<?php echo $image_url;?>files/images/common/tick.png" ng-show="createInfo.vflower6.chip_type==2"/></div>
                            <div class="selectText" >2/4,5/10,10/20,20/40</div>
                        </div>
                        <div class="selectItem" ng-click="selectChange(1,4)" >
                            <div class="selectBox" ><img src="<?php echo $image_url;?>files/images/common/tick.png" ng-show="createInfo.vflower6.chip_type==4"/></div>
                            <div class="selectText" >5/10,10/20,20/40,40/80</div>
                        </div>
                    </div>
                </div>

                <div class="selectPart" style="height:5.5vh;">
                    <div class="selectTitle">规则：</div>
                    <div class="selectList">
                        <div class="selectItem" ng-click="selectChange(2,1)">
                            <div class="selectBox" ><img src="<?php echo $image_url;?>files/images/common/tick.png" ng-show="createInfo.vflower6.pkvalue1==1" /></div>
                            <div class="selectText" >100分以下不能比牌</div>
                        </div>
                    </div>
                </div>

                <div class="selectPart" style="height:4.5vh;">
                    <div class="selectTitle" style="width: 14vh;">看牌需下注：</div>
                    <div class="selectList" style="width: 28vh;">
                        <div class="selectItem" style="margin-left: 1vh" ng-click="selectChange(3,0)">
                            <div class="selectBox" ><img src="<?php echo $image_url;?>files/images/common/tick.png" ng-show="createInfo.vflower6.seen==0" /></div>
                            <div class="selectText" >无</div>
                        </div>
                        <div class="selectItem" style="margin-left: 1vh" ng-click="selectChange(3,20)">
                            <div class="selectBox" ><img src="<?php echo $image_url;?>files/images/common/tick.png" ng-show="createInfo.vflower6.seen==20" /></div>
                            <div class="selectText" >20</div>
                        </div>
                        <div class="selectItem" style="margin-left: 1vh;" ng-click="selectChange(3,50)">
                            <div class="selectBox" ><img src="<?php echo $image_url;?>files/images/common/tick.png" ng-show="createInfo.vflower6.seen==50" /></div>
                            <div class="selectText">50</div>
                        </div>
                        <div class="selectItem" style="margin-left: 1vh;" ng-click="selectChange(3,100)">
                            <div class="selectBox" ><img src="<?php echo $image_url;?>files/images/common/tick.png" ng-show="createInfo.vflower6.seen==100" /></div>
                            <div class="selectText">100</div>
                        </div>
                    </div>
                </div>

                <div class="selectPart" style="height:4.5vh;">
                    <div class="selectTitle">局数：</div>
                    <div class="selectList">
                        <div class="selectItem" ng-click="selectChange(4,1)">
                            <div class="selectBox" ><img src="<?php echo $image_url;?>files/images/common/tick.png" ng-show="createInfo.vflower6.ticket_count==1" /></div>
                            <div class="selectText" >10局X1房卡</div>
                        </div>
                        <div class="selectItem" ng-click="selectChange(4,2)">
                            <div class="selectBox" ><img src="<?php echo $image_url;?>files/images/common/tick.png" ng-show="createInfo.vflower6.ticket_count==2" /></div>
                            <div class="selectText">20局X2房卡</div>
                        </div>
                    </div>
                </div>

                <div class="selectPart" style="height:4.5vh;">
                    <div class="selectTitle">上限：</div>
                    <div class="selectList">
                        <div class="selectItem" ng-click="selectChange(5,500)">
                            <div class="selectBox" ><img src="<?php echo $image_url;?>files/images/common/tick.png" ng-show="createInfo.vflower6.upper_limit==500" /></div>
                            <div class="selectText">500</div>
                        </div>
                        <div class="selectItem" ng-click="selectChange(5,1000)">
                            <div class="selectBox" ><img src="<?php echo $image_url;?>files/images/common/tick.png" ng-show="createInfo.vflower6.upper_limit==1000" /></div>
                            <div class="selectText">1000</div>
                        </div>
                        <div class="selectItem" ng-click="selectChange(5,2000)">
                            <div class="selectBox" ><img src="<?php echo $image_url;?>files/images/common/tick.png" ng-show="createInfo.vflower6.upper_limit==2000" /></div>
                            <div class="selectText">2000</div>
                        </div>
                    </div>
                </div>

                <div class="selectPart" style="height:9vh;">
                    <div class="selectTitle">准入：</div>
                    <div class="selectList">
                        <div class="selectItem"  ng-click="selectChange(6,1)">
                            <div class="selectBox" ><img src="<?php echo $image_url;?>files/images/common/tick.png" ng-show="createInfo.vflower6.bean_type==1" /></div>
                            <div class="selectText" >≥50豆</div>
                        </div>
                        <div class="selectItem"  ng-click="selectChange(6,2)">
                            <div class="selectBox" ><img src="<?php echo $image_url;?>files/images/common/tick.png" ng-show="createInfo.vflower6.bean_type==2" /></div>
                            <div class="selectText" >≥100豆</div>
                        </div>
                        <div class="selectItem"  ng-click="selectChange(6,3)">
                            <div class="selectBox" ><img src="<?php echo $image_url;?>files/images/common/tick.png" ng-show="createInfo.vflower6.bean_type==3" /></div>
                            <div class="selectText" >≥300豆</div>
                        </div>
                        <div class="selectItem"  ng-click="selectChange(6,4)">
                            <div class="selectBox" ><img src="<?php echo $image_url;?>files/images/common/tick.png" ng-show="createInfo.vflower6.bean_type==4" /></div>
                            <div class="selectText" >≥500豆</div>
                        </div>
                    </div>
                </div>

            </div>

            <div class="flowerRull" ng-if="createInfo.isShow==90&&createInfo.vbf.game==95">
                <div class="selectPart" style="height:15vh;">
                    <div class="selectTitle">筹码：</div>
                    <div class="selectList" >
                        <div class="selectItem"  ng-click="selectChange(1,1)">
                            <div class="selectBox" ><img src="<?php echo $image_url;?>files/images/common/tick.png" ng-show="createInfo.vflower10.chip_type==1"/></div>
                            <div class="selectText" >2/4,4/8,8/16,10/20</div>
                        </div>
                        <div class="selectItem" ng-click="selectChange(1,2)" >
                            <div class="selectBox" ><img src="<?php echo $image_url;?>files/images/common/tick.png" ng-show="createInfo.vflower10.chip_type==2"/></div>
                            <div class="selectText" >2/4,5/10,10/20,20/40</div>
                        </div>
                        <div class="selectItem" ng-click="selectChange(1,4)" >
                            <div class="selectBox" ><img src="<?php echo $image_url;?>files/images/common/tick.png" ng-show="createInfo.vflower10.chip_type==4"/></div>
                            <div class="selectText" >5/10,10/20,20/40,40/80</div>
                        </div>
                    </div>
                </div>

                <div class="selectPart" style="height:4.5vh;">
                    <div class="selectTitle">规则：</div>
                    <div class="selectList">
                        <div class="selectItem" ng-click="selectChange(2,1)">
                            <div class="selectBox" ><img src="<?php echo $image_url;?>files/images/common/tick.png" ng-show="createInfo.vflower10.pkvalue1==1" /></div>
                            <div class="selectText" >100分以下不能比牌</div>
                        </div>
                    </div>
                </div>

                <div class="selectPart" style="height:4.5vh;">
                    <div class="selectTitle" style="width: 14vh;">看牌需下注：</div>
                    <div class="selectList" style="width: 28vh;">
                        <div class="selectItem" style="margin-left: 1vh" ng-click="selectChange(3,0)">
                            <div class="selectBox" ><img src="<?php echo $image_url;?>files/images/common/tick.png" ng-show="createInfo.vflower10.seen==0" /></div>
                            <div class="selectText" >无</div>
                        </div>
                        <div class="selectItem" style="margin-left: 1vh" ng-click="selectChange(3,20)">
                            <div class="selectBox" ><img src="<?php echo $image_url;?>files/images/common/tick.png" ng-show="createInfo.vflower10.seen==20" /></div>
                            <div class="selectText" >20</div>
                        </div>
                        <div class="selectItem" style="margin-left: 1vh;" ng-click="selectChange(3,50)">
                            <div class="selectBox" ><img src="<?php echo $image_url;?>files/images/common/tick.png" ng-show="createInfo.vflower10.seen==50" /></div>
                            <div class="selectText">50</div>
                        </div>
                        <div class="selectItem" style="margin-left: 1vh;" ng-click="selectChange(3,100)">
                            <div class="selectBox" ><img src="<?php echo $image_url;?>files/images/common/tick.png" ng-show="createInfo.vflower10.seen==100" /></div>
                            <div class="selectText">100</div>
                        </div>
                    </div>
                </div>

                <div class="selectPart" style="height:5.5vh;">
                    <div class="selectTitle">局数：</div>
                    <div class="selectList">
                        <div class="selectItem" ng-click="selectChange(4,1)">
                            <div class="selectBox" ><img src="<?php echo $image_url;?>files/images/common/tick.png" ng-show="createInfo.vflower10.ticket_count==1" /></div>
                            <div class="selectText" >10局X1房卡</div>
                        </div>
                        <div class="selectItem" ng-click="selectChange(4,2)">
                            <div class="selectBox" ><img src="<?php echo $image_url;?>files/images/common/tick.png" ng-show="createInfo.vflower10.ticket_count==2" /></div>
                            <div class="selectText">20局X2房卡</div>
                        </div>
                    </div>
                </div>

                <div class="selectPart" style="height:4.5vh;">
                    <div class="selectTitle">上限：</div>
                    <div class="selectList">
                        <div class="selectItem" ng-click="selectChange(5,500)">
                            <div class="selectBox" ><img src="<?php echo $image_url;?>files/images/common/tick.png" ng-show="createInfo.vflower10.upper_limit==500" /></div>
                            <div class="selectText">500</div>
                        </div>
                        <div class="selectItem" ng-click="selectChange(5,1000)">
                            <div class="selectBox" ><img src="<?php echo $image_url;?>files/images/common/tick.png" ng-show="createInfo.vflower10.upper_limit==1000" /></div>
                            <div class="selectText">1000</div>
                        </div>
                        <div class="selectItem" ng-click="selectChange(5,2000)">
                            <div class="selectBox" ><img src="<?php echo $image_url;?>files/images/common/tick.png" ng-show="createInfo.vflower10.upper_limit==2000" /></div>
                            <div class="selectText">2000</div>
                        </div>
                    </div>
                </div>

                <div class="selectPart" style="height:9vh;">
                    <div class="selectTitle">准入：</div>
                    <div class="selectList">
                        <div class="selectItem"  ng-click="selectChange(6,1)">
                            <div class="selectBox" ><img src="<?php echo $image_url;?>files/images/common/tick.png" ng-show="createInfo.vflower10.bean_type==1" /></div>
                            <div class="selectText" >≥50豆</div>
                        </div>
                        <div class="selectItem"  ng-click="selectChange(6,2)">
                            <div class="selectBox" ><img src="<?php echo $image_url;?>files/images/common/tick.png" ng-show="createInfo.vflower10.bean_type==2" /></div>
                            <div class="selectText" >≥100豆</div>
                        </div>
                        <div class="selectItem"  ng-click="selectChange(6,3)">
                            <div class="selectBox" ><img src="<?php echo $image_url;?>files/images/common/tick.png" ng-show="createInfo.vflower10.bean_type==3" /></div>
                            <div class="selectText" >≥300豆</div>
                        </div>
                        <div class="selectItem"  ng-click="selectChange(6,4)">
                            <div class="selectBox" ><img src="<?php echo $image_url;?>files/images/common/tick.png" ng-show="createInfo.vflower10.bean_type==4" /></div>
                            <div class="selectText" >≥500豆</div>
                        </div>
                    </div>
                </div>

            </div>

            <div class="flowerRull" ng-if="createInfo.isShow==111">
                <div class="selectPart chipItems" style="height:15vh;">
                    <div class="selectTitle">底分：</div>
                    <div class="selectList" >
                        <div class="selectItem"  ng-click="selectChange(10,2)">
                            <div class="selectBox">
                                <img src="<?php echo $image_url;?>files/images/common/tick.png" ng-show="createInfo.bflower.default_score == 2"/>
                            </div>
                            <div class="selectText" >2</div>
                        </div>
                        <div class="selectItem" ng-click="selectChange(10,4)" >
                            <div class="selectBox" >
                                <img src="<?php echo $image_url;?>files/images/common/tick.png" ng-show="createInfo.bflower.default_score == 4"/>
                            </div>
                            <div class="selectText" >4</div>
                        </div>
                        <div class="selectItem" ng-click="selectChange(10,10)" >
                            <div class="selectBox" >
                                <img src="<?php echo $image_url;?>files/images/common/tick.png" ng-show="createInfo.bflower.default_score == 10"/>
                            </div>
                            <div class="selectText" >10</div>
                        </div>
                        <div class="selectItem"  ng-click="selectChange(10,20)">
                            <div class="selectBox">
                                <img src="<?php echo $image_url;?>files/images/common/tick.png" ng-show="createInfo.bflower.default_score == 20"/>
                            </div>
                            <div class="selectText" >20</div>
                        </div>
                        <div class="selectItem"  ng-click="selectChange(10,40)">
                            <div class="selectBox">
                                <img src="<?php echo $image_url;?>files/images/common/tick.png" ng-show="createInfo.bflower.default_score == 40"/>
                            </div>
                            <div class="selectText" >40</div>
                        </div>
                        <div class="selectItem"  ng-click="selectChange(10,100)">
                            <div class="selectBox">
                                <img src="<?php echo $image_url;?>files/images/common/tick.png" ng-show="createInfo.bflower.default_score == 100"/>
                            </div>
                            <div class="selectText" >100</div>
                        </div>
                        <div class="selectItem"  ng-click="selectChange(10,200)">
                            <div class="selectBox">
                                <img src="<?php echo $image_url;?>files/images/common/tick.png" ng-show="createInfo.bflower.default_score == 200"/>
                            </div>
                            <div class="selectText" >200</div>
                        </div>
                        <div class="selectItem"  ng-click="selectChange(10,0)">
                            <div class="selectBox">
                                <img src="<?php echo $image_url;?>files/images/common/tick.png" ng-show="createInfo.bflower.default_score == 0"/>
                            </div>
                            <div class="selectText" style="position:relative;">
                                <select class="flowerSelect" ng-model='createInfo.bflower.default_score_select' ng-options="x for x in defaultScores">
                                </select>
                                <div class="selectIcon">
                                    <div class="sanjiao"></div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
                <div class="selectPart chipItems" style="height:20vh;">
                    <div class="selectTitle">筹码：</div>
                    <div class="selectList" >
                        <div class="selectItem"  ng-click="selectChange(1,2)">
                            <div class="selectBox">
                                <img src="<?php echo $image_url;?>files/images/common/tick.png" ng-show="createInfo.bflower.chip_type.indexOf(2) !== -1"/>
                            </div>
                            <div class="selectText" >2/4</div>
                        </div>
                        <div class="selectItem" ng-click="selectChange(1,4)" >
                            <div class="selectBox" >
                                <img src="<?php echo $image_url;?>files/images/common/tick.png" ng-show="createInfo.bflower.chip_type.indexOf(4) !== -1"/>
                            </div>
                            <div class="selectText" >4/8</div>
                        </div>
                        <div class="selectItem" ng-click="selectChange(1,5)" >
                            <div class="selectBox" >
                                <img src="<?php echo $image_url;?>files/images/common/tick.png" ng-show="createInfo.bflower.chip_type.indexOf(5) !== -1"/>
                            </div>
                            <div class="selectText" >5/10</div>
                        </div>
                        <div class="selectItem"  ng-click="selectChange(1,8)">
                            <div class="selectBox">
                                <img src="<?php echo $image_url;?>files/images/common/tick.png" ng-show="createInfo.bflower.chip_type.indexOf(8) !== -1"/>
                            </div>
                            <div class="selectText" >8/16</div>
                        </div>
                        <div class="selectItem"  ng-click="selectChange(1,10)">
                            <div class="selectBox">
                                <img src="<?php echo $image_url;?>files/images/common/tick.png" ng-show="createInfo.bflower.chip_type.indexOf(10) !== -1"/>
                            </div>
                            <div class="selectText" >10/20</div>
                        </div>
                        <div class="selectItem"  ng-click="selectChange(1,20)">
                            <div class="selectBox">
                                <img src="<?php echo $image_url;?>files/images/common/tick.png" ng-show="createInfo.bflower.chip_type.indexOf(20) !== -1"/>
                            </div>
                            <div class="selectText" >20/40</div>
                        </div>
                        <div class="selectItem"  ng-click="selectChange(1,40)">
                            <div class="selectBox">
                                <img src="<?php echo $image_url;?>files/images/common/tick.png" ng-show="createInfo.bflower.chip_type.indexOf(40) !== -1"/>
                            </div>
                            <div class="selectText" >40/80</div>
                        </div>
                        <div class="selectItem"  ng-click="selectChange(1,50)">
                            <div class="selectBox">
                                <img src="<?php echo $image_url;?>files/images/common/tick.png" ng-show="createInfo.bflower.chip_type.indexOf(50) !== -1"/>
                            </div>
                            <div class="selectText" >50/100</div>
                        </div>
                        <div class="selectItem"  ng-click="selectChange(1,100)">
                            <div class="selectBox">
                                <img src="<?php echo $image_url;?>files/images/common/tick.png" ng-show="createInfo.bflower.chip_type.indexOf(100) !== -1"/>
                            </div>
                            <div class="selectText" >100/200</div>
                        </div>
                        <div class="selectItem" style="width: 20vh;">
                            <div class="selectText" >请任选四组筹码</div>
                        </div>
                    </div>
                </div>
                <div class="selectPart timeItems" style="height:5.5vh;">
                    <div class="selectTitle">时间：</div>
                    <div class="selectList" >
                        <label class="selectItem" style="position:relative;">
                            <span style="color:#633201;">准备</span>
                            <select class="bflowerReadySelect" ng-model="createInfo.bflower.countDown[0]" ng-options="x for x in defaultTime5To10">
                            </select>
                            <div class="readySelectIcon">
                                    <div class="sanjiao"></div>
                            </div>
                        </label>
                        <label class="selectItem" style="position:relative;">
                            <span style="color:#633201;">下注</span>
                            <select class="bflowerChipSelect" ng-model="createInfo.bflower.countDown[1]" ng-options="x for x in defaultTime5To20">
                            </select>
                            <div class="readySelectIcon">
                                    <div class="sanjiao"></div>
                            </div>
                        </label>
                    </div>
                </div>
                <div class="selectPart" style="height:10vh;">
					<div class="selectTitle">看牌：</div>
					<div class="selectList selectBigRoundList" >
                    <div class="selectItem">
							<div class="selectText seenBigTip">低于积分池将不能看牌</div>
						</div>
                        <div class="selectItem">
							<div class="seenBigProgressReduce" ng-click="seenBigReduce()" ></div>
							<div class="seenBigProgressText" >{{createInfo.bflower.seenProgress}}</div>
                            <div class="seenBigProgress">
                                <input type="range" min="0" max="2000" step="100" class="seenBigRange" ng-model="seenBigProgressValue"
                                ng-change="seenBigProgressChange(seenBigProgressValue)">
                            </div>
                            <div class="seenBigProgressAdd" ng-click="seenBigAdd()"></div>
						</div>
					</div>
				</div>
                <div class="selectPart" style="height:15vh;">
					<div class="selectTitle">比牌：</div>
					<div class="selectList" >
						<div class="selectItem"  ng-click="selectChange(7)">
							<div class="selectBox"><img src="<?php echo $image_url;?>files/images/common/tick.png" ng-show="createInfo.bflower.raceCard==false"/></div>
							<div class="selectText" >首轮禁止比牌</div>
						</div>
                        <div class="selectItem">
							<div class="selectText"  style="color:#54A802;">低于积分池将不能比牌</div>
						</div>
                        <div class="selectItem compareBigProgressItem">
							<div class="compareBigProgressReduce" ng-click="compareBigReduce()"></div>
							<div class="compareBigProgressText">{{createInfo.bflower.compareProgress}}</div>
                            <div class="compareBigProgress">
                                <input type="range" min="0" max="2000" step="100" class="compareBigRange" ng-model="compareBigProgressValue"
                                ng-change="compareBigProgressChange(compareBigProgressValue)">
                            </div>
                            <div class="compareBigProgressAdd" ng-click="compareBigAdd()"></div>
						</div>
					</div>
				</div>

				<div class="selectPart" style="height:5.5vh;">
					<div class="selectTitle">局数：</div>
					<div class="selectList">
						<div class="selectItem" ng-click="selectChange(3,1)">
							<div class="selectBox" ><img src="<?php echo $image_url;?>files/images/common/tick.png" ng-show="createInfo.bflower.ticket_count==1" /></div>
							<div class="selectText" >10局X1房卡</div>
						</div>
						<div class="selectItem" ng-click="selectChange(3,2)">
							<div class="selectBox" ><img src="<?php echo $image_url;?>files/images/common/tick.png" ng-show="createInfo.bflower.ticket_count==2" /></div>
							<div class="selectText">20局X2房卡</div>
						</div>
					</div>
				</div>

                <div class="selectPart">
                    <div class="selectTitle">上限：</div>
                    <div class="selectList">
                        <div class="limitChoose selectItem" style="height: 2.5vh;display: flex; align-items: center; justify-content: space-between">
                            <div class="limitChooseProgressReduce" ng-click="downUpperLimit('b')"></div>
                            <div class="limitChooseProgressText" style="height: auto !important;">{{createInfo.bflower.upper_limit == 0? '无上限': createInfo.bflower.upper_limit}}</div>
                            <div class="limitChooseProgress">
                                <input type="range" min="0" max="2000" step="100"
                                       class="limitChooseRange"
                                       ng-style="{'background-size': (createInfo.bflower.upper_limit)/20+ '% 100%'}"
                                       ng-model="createInfo.bflower.upper_limit" />
                            </div>
                            <div class="limitChooseProgressAdd" ng-click="upUpperLimit('b')"></div>
                        </div>
                    </div>
                </div>

                <div class="selectPart">
                    <div class="selectTitle">喜牌：</div>
                    <div class="selectList">
                        <div class="selectItem">
                            <div class="selectXiPaiText" >豹子，同花顺为喜牌，获得玩家奖励</div>
                        </div>
                        <div class="selectItem selectXiPaiItem" ng-click="selectChange(9,0)">
                            <div class="selectBox" >
                                <img src="<?php echo $image_url;?>files/images/common/tick.png" ng-show="createInfo.bflower.extraRewards==0" />
                            </div>
                            <div class="selectText">0</div>
                        </div>
                        <div class="selectItem selectXiPaiItem" ng-click="selectChange(9,5)">
                            <div class="selectBox" >
                                <img src="<?php echo $image_url;?>files/images/common/tick.png" ng-show="createInfo.bflower.extraRewards==5" />
                            </div>
                            <div class="selectText">5</div>
                        </div>
                        <div class="selectItem selectXiPaiItem" ng-click="selectChange(9,10)">
                            <div class="selectBox" >
                                <img src="<?php echo $image_url;?>files/images/common/tick.png" ng-show="createInfo.bflower.extraRewards==10" />
                            </div>
                            <div class="selectText">10</div>
                        </div>
                        <div class="selectItem selectXiPaiItem" ng-click="selectChange(9,20)">
                            <div class="selectBox" >
                                <img src="<?php echo $image_url;?>files/images/common/tick.png" ng-show="createInfo.bflower.extraRewards==20" />
                            </div>
                            <div class="selectText">20</div>
                        </div>
                        <div class="selectItem selectXiPaiItem" ng-click="selectChange(9,40)">
                            <div class="selectBox" >
                                <img src="<?php echo $image_url;?>files/images/common/tick.png" ng-show="createInfo.bflower.extraRewards==40" />
                            </div>
                            <div class="selectText">40</div>
                        </div>
                    </div>
                </div>

			</div>

			<div class="landlordRull" ng-if="createInfo.isShow==2">
				<div class="selectPart" style="height:4.5vh;">
					<div class="selectTitle">底分：</div>
					<div class="selectList" >
					    <div class="selectItem" ng-click="selectChange(1,1)" >
							<div class="selectBox" ><img src="<?php echo $image_url;?>files/images/common/tick.png" ng-show="createInfo.landlord.base_score==1"/></div>
							<div class="selectText">1分</div>
						</div>
						<div class="selectItem" ng-click="selectChange(1,5)" >
							<div class="selectBox" ><img src="<?php echo $image_url;?>files/images/common/tick.png" ng-show="createInfo.landlord.base_score==5"/></div>
							<div class="selectText">5分</div>
						</div>
						<div class="selectItem" ng-click="selectChange(1,10)">
							<div class="selectBox" ><img src="<?php echo $image_url;?>files/images/common/tick.png" ng-show="createInfo.landlord.base_score==10" /></div>
							<div class="selectText">10分</div>
						</div>
					</div>
				</div>
				<div class="selectPart" style="height:9vh;">
					<div class="selectTitle">规则：</div>
					<div class="selectList">
						<div style="height:4.5vh;">
							<div class="selectItem" ng-click="selectChange(2,1)">
								<div class="selectBox"><img src="<?php echo $image_url;?>files/images/common/tick.png" ng-show="createInfo.landlord.ask_mode==1" /></div>
								<div class="selectText">轮流问地主</div>
							</div>
						</div>
						<div style="height:4.5vh;" >
							<div class="selectItem" ng-click="selectChange(2,2)">
								<div class="selectBox" ><img src="<?php echo $image_url;?>files/images/common/tick.png" ng-show="createInfo.landlord.ask_mode==2" /></div>
								<div class="selectText">随机问地主</div>
							</div>
						</div>
					</div>
				</div>
				<div class="selectPart" style="height:9vh;">
					<div class="selectTitle">局数：</div>
					<div class="selectList">
						<div class="selectItem"  ng-click="selectChange(3,1)">
							<div class="selectBox" ><img src="<?php echo $image_url;?>files/images/common/tick.png" ng-show="createInfo.landlord.ticket_count==1" /></div>
							<div class="selectText" >6局X2张房卡</div>
						</div>
						<div class="selectItem" ng-click="selectChange(3,2)">
							<div class="selectBox" ><img src="<?php echo $image_url;?>files/images/common/tick.png" ng-show="createInfo.landlord.ticket_count==2" /></div>
							<div class="selectText" >12局X4张房卡</div>
						</div>
					</div>
				</div>
			</div>

			<div class="bullRull" ng-if="createInfo.isShow==3">
                <div class="selectPart">
                    <div class="selectTitle">底分：</div>
                    <div class="selectList" >
                        <div class="selectItem" ng-click="selectChange(1,1)" ng-show="createInfo.bull.banker_mode!=4">
                            <div class="selectBox" ><img src="<?php echo $image_url;?>files/images/common/tick.png" ng-show="createInfo.bull.score_type==1"/></div>
                            <div class="selectText">1分</div>
                        </div>
                        <div class="selectItem" ng-click="selectChange(1,6)" ng-show="createInfo.bull.banker_mode!=4">
                            <div class="selectBox" ><img src="<?php echo $image_url;?>files/images/common/tick.png" ng-show="createInfo.bull.score_type==6"/></div>
                            <div class="selectText">2分</div>
                        </div>
                        <div class="selectItem" ng-click="selectChange(1,2)" ng-show="createInfo.bull.banker_mode!=4">
                            <div class="selectBox" ><img src="<?php echo $image_url;?>files/images/common/tick.png" ng-show="createInfo.bull.score_type==2"/></div>
                            <div class="selectText">3分</div>
                        </div>
                        <div class="selectItem" ng-click="selectChange(1,3)" >
                            <div class="selectBox" ><img src="<?php echo $image_url;?>files/images/common/tick.png" ng-show="createInfo.bull.score_type==3" /></div>
                            <div class="selectText">5分</div>
                        </div>
                        <div class="selectItem" ng-click="selectChange(1,4)" ng-show="createInfo.bull.banker_mode==4">
                            <div class="selectBox" ><img src="<?php echo $image_url;?>files/images/common/tick.png" ng-show="createInfo.bull.score_type==4" /></div>
                            <div class="selectText">10分</div>
                        </div>
                        <div class="selectItem" ng-click="selectChange(1,5)" ng-show="createInfo.bull.banker_mode==4">
                            <div class="selectBox" ><img src="<?php echo $image_url;?>files/images/common/tick.png" ng-show="createInfo.bull.score_type==5" /></div>
                            <div class="selectText">20分</div>
                        </div>
                        <div class="selectItem changeWidth" ng-click="selectChange(1,7)">
                            <div class="selectBox" ><img src="<?php echo $image_url;?>files/images/common/tick.png" ng-show="createInfo.bull.score_type==7"/></div>
                            <div class="bullfootSelectIcon">
                                    <div class="footSanJiao"></div>
                            </div>
                        </div>
                        <select id="selectList" ng-model="selectValue" ng-change="listChange(selectValue, 'bull')" 
                        ng-disabled="selectBull1" ng-init="selectValue=selectBullValue" ng-options="item for item in selectArr"
                        ng-show="createInfo.bull.banker1==='selected'">
                        </select>
                        <select id="selectList" ng-model="selectValue" ng-change="listChange(selectValue, 'bull')" 
                        ng-disabled="selectBull3" ng-init="selectValue=selectBullValue" ng-options="item for item in selectArr"
                        ng-show="createInfo.bull.banker3==='selected'">
                        </select>
                        <select id="selectList" ng-model="selectValue" ng-change="listChange(selectValue, 'bull')" 
                        ng-disabled="selectBull2" ng-init="selectValue=selectBullValue" ng-options="item for item in selectArr"
                        ng-show="createInfo.bull.banker2==='selected'">
                        </select>
                        <select id="selectList" ng-model="selectValue" ng-change="listChange(selectValue, 'bull')" 
                        ng-disabled="selectBull5" ng-init="selectValue=selectBullValue" ng-options="item for item in selectArr"
                        ng-show="createInfo.bull.banker5==='selected'">
                        </select>
                        <select id="selectList" ng-model="selectValue" ng-change="listChange(selectValue, 'bull')" 
                        ng-disabled="selectBull4" ng-init="selectValue=selectBullValue" ng-options="item for item in selectArr"
                        ng-show="createInfo.bull.banker4==='selected'">
                        </select>
                    </div>
                </div>

                <div class="selectPart timeItems" style="height:9vh;">
                    <div class="selectTitle">时间：</div>
                    <div class="selectList" >
                        <label class="selectItem">
                            <span style="color:#633201;">准备</span>
                            <select class="bullSelect" ng-model="createInfo.bull.countDown[0]" ng-options="x for x in defaultTime5To10">
                            </select>
                            <div class="bullSelectIcon">
                                    <div class="sanjiao"></div>
                            </div>
                        </label>
                        <label class="selectItem">
                            <span>抢庄</span>
                            <select class="bullSelect" ng-model="createInfo.bull.countDown[1]" ng-options="x for x in defaultTime5To10">
                            </select>
                            <div class="bullSelectIcon">
                                    <div class="sanjiao"></div>
                            </div>
                        </label>
                        <label class="selectItem">
                            <span>下注</span>
                            <select  class="bullSelect" ng-model="createInfo.bull.countDown[2]" ng-options="x for x in defaultTime5To10">
                            </select>
                            <div class="bullSelectIcon">
                                    <div class="sanjiao"></div>
                            </div>
                        </label>
                        <label class="selectItem">
                            <span>摊牌</span>
                            <select class="bullSelect" ng-model="createInfo.bull.countDown[3]" ng-options="x for x in defaultTime5To10">
                            </select>
                            <div class="bullSelectIcon">
                                    <div class="sanjiao"></div>
                            </div>
                        </label>
                    </div>
                </div>

                <div class="selectPart" style="height:10vh;">
					<div class="selectTitle">规则：</div>
					<div class="selectList">
						<div class="selectItem"  ng-click="selectChange(2,1)">
							<div class="selectBox" ><img src="<?php echo $image_url;?>files/images/common/tick.png" ng-show="createInfo.bull.rule_type==1" /></div>
							<div class="selectText" >牛牛×3牛九×2牛八×2</div>
						</div>
						<div class="selectItem"  ng-click="selectChange(2,2)">
							<div class="selectBox" ><img src="<?php echo $image_url;?>files/images/common/tick.png" ng-show="createInfo.bull.rule_type==2" /></div>
							<div class="selectText" >牛牛×4牛九×3牛八×2牛七×2</div>
						</div>
					</div>
				</div>

				<div class="selectPart" style="height:25vh;">
				<div class="selectTitle">牌型：</div>
					<div class="selectList">
                    <div class="selectItem"  ng-click="selectChange(3,8)">
                        <div class="selectBox" ><img src="<?php echo $image_url;?>files/images/common/tick.png" ng-show="createInfo.bull.has_ghost==1" /></div>
                        <div class="selectText" >有癞子</div>
                    </div>
                    <div class="selectItem"  ng-click="selectChange(3,9)">
                        <div class="selectBox" ><img src="<?php echo $image_url;?>files/images/common/tick.png" ng-show="createInfo.bull.is_cardfour==1" /></div>
                        <div class="selectText" >四花牛(4倍)</div>
                    </div>
                    <div class="selectItem"  ng-click="selectChange(3,1)">
                        <div class="selectBox" ><img src="<?php echo $image_url;?>files/images/common/tick.png" ng-show="createInfo.bull.is_cardfive==1" /></div>
                        <div class="selectText" >五花牛(5倍)</div>
                    </div>
					<div class="selectItem" ng-click="selectChange(3,2)">
						<div class="selectBox" ><img src="<?php echo $image_url;?>files/images/common/tick.png" ng-show="createInfo.bull.is_straight==1" /></div>
						<div class="selectText" >顺子牛(6倍)</div>
					</div>
					<div class="selectItem" ng-click="selectChange(3,3)">
						<div class="selectBox" ><img src="<?php echo $image_url;?>files/images/common/tick.png" ng-show="createInfo.bull.is_flush==1" /></div>
						<div class="selectText" >同花牛(6倍)</div>
					</div>
					<div class="selectItem" ng-click="selectChange(3,4)">
						<div class="selectBox" ><img src="<?php echo $image_url;?>files/images/common/tick.png" ng-show="createInfo.bull.is_hulu==1" /></div>
						<div class="selectText" >葫芦牛(6倍)</div>
					</div>
					<div class="selectItem" ng-click="selectChange(3,5)">
						<div class="selectBox" ><img src="<?php echo $image_url;?>files/images/common/tick.png" ng-show="createInfo.bull.is_cardbomb==1" /></div>
						<div class="selectText" >炸弹牛(6倍)</div>
					</div>
                    <div class="selectItem" ng-click="selectChange(3,7)">
						<div class="selectBox" ><img src="<?php echo $image_url;?>files/images/common/tick.png" ng-show="createInfo.bull.is_straightflush==1" /></div>
						<div class="selectText" >同花顺(7倍)</div>
					</div>
					<div class="selectItem" ng-click="selectChange(3,6)">
						<div class="selectBox" ><img src="<?php echo $image_url;?>files/images/common/tick.png" ng-show="createInfo.bull.is_cardtiny==1" /></div>
						<div class="selectText" >五小牛(8倍)</div>
					</div>
					</div>
				</div>

				<div class="selectPart" style="height:5.5vh;">
					<div class="selectTitle">局数：</div>
					<div class="selectList">
						<div class="selectItem"  ng-click="selectChange(4,1)">
							<div class="selectBox" ><img src="<?php echo $image_url;?>files/images/common/tick.png" ng-show="createInfo.bull.ticket_type==1" /></div>
							<div class="selectText" >10局×1房卡</div>
						</div>
						<div class="selectItem"  ng-click="selectChange(4,2)">
							<div class="selectBox" ><img src="<?php echo $image_url;?>files/images/common/tick.png" ng-show="createInfo.bull.ticket_type==2" /></div>
							<div class="selectText" >20局×2房卡</div>
						</div>
					</div>
				</div>

                <div class="selectPart" style="height:10vh;">
                    <div class="selectTitle">倍数：</div>
                    <div class="selectList">
                        <div class="selectItem"  ng-click="selectChange(6,1)">
                            <div class="selectBox" ><img src="<?php echo $image_url;?>files/images/common/tick.png" ng-show="createInfo.bull.times_type==1" /></div>
                            <div class="selectText" >1，2，4，5</div>
                        </div>
                        <div class="selectItem"  ng-click="selectChange(6,2)">
                            <div class="selectBox" ><img src="<?php echo $image_url;?>files/images/common/tick.png" ng-show="createInfo.bull.times_type==2" /></div>
                            <div class="selectText" >1，3，5，8</div>
                        </div>
                        <div class="selectItem"  ng-click="selectChange(6,3)">
                            <div class="selectBox" ><img src="<?php echo $image_url;?>files/images/common/tick.png" ng-show="createInfo.bull.times_type==3" /></div>
                            <div class="selectText" >2，4，6，10</div>
                        </div>
                    </div>
                </div>

				<div class="selectPart" style="height:10vh;" ng-if="createInfo.bull.banker_mode==5">
					<div class="selectTitle">上庄：</div>
					<div class="selectList">
						<div class="selectItem"  ng-click="selectChange(5,1)">
							<div class="selectBox" ><img src="<?php echo $image_url;?>files/images/common/tick.png" ng-show="createInfo.bull.banker_score==1" /></div>
							<div class="selectText" >无</div>
						</div>
						<div class="selectItem" ng-click="selectChange(5,2)">
							<div class="selectBox" ><img src="<?php echo $image_url;?>files/images/common/tick.png" ng-show="createInfo.bull.banker_score==2" /></div>
							<div class="selectText" >100</div>
						</div>
						<div class="selectItem" ng-click="selectChange(5,3)">
							<div class="selectBox" ><img src="<?php echo $image_url;?>files/images/common/tick.png" ng-show="createInfo.bull.banker_score==3" /></div>
							<div class="selectText" >300</div>
						</div>
						<div class="selectItem" ng-click="selectChange(5,4)">
							<div class="selectBox" ><img src="<?php echo $image_url;?>files/images/common/tick.png" ng-show="createInfo.bull.banker_score==4" /></div>
							<div class="selectText" >500</div>
						</div>
					</div>
				</div>

			</div>

			<div class="majiangRull" ng-if="createInfo.isShow==4">
					<div class="selectPart" style="height:9vh;">
						<div class="selectTitle">鬼牌：</div>
						<div class="selectList" >
							<div class="selectItem"  ng-click="selectChange(1,0)">
								<div class="selectBox" ><img src="<?php echo $image_url;?>files/images/common/tick.png" ng-show="createInfo.majiang.joker==0"/></div>
								<div class="selectText">无鬼牌</div>
							</div>
							<div class="selectItem" ng-click="selectChange(1,1)" >
								<div class="selectBox" ><img src="<?php echo $image_url;?>files/images/common/tick.png" ng-show="createInfo.majiang.joker==1"/></div>
								<div class="selectText">翻牌当鬼</div>
							</div>
							<div class="selectItem" ng-click="selectChange(1,2)" >
								<div class="selectBox" ><img src="<?php echo $image_url;?>files/images/common/tick.png" ng-show="createInfo.majiang.joker==2" /></div>
								<div class="selectText">红中当鬼</div>
							</div>
						</div>
					</div>
					<div class="selectPart" style="height:9vh;">
						<div class="selectTitle">抓马：</div>
						<div class="selectList">
							<div>
								<div class="selectItem"ng-click="selectChange(2,0)">
									<div class="selectBox" ><img src="<?php echo $image_url;?>files/images/common/tick.png" ng-show="createInfo.majiang.horse_count==0" /></div>
									<div class="selectText">不跑马</div>
								</div>
								<div class="selectItem" ng-click="selectChange(2,2)">
									<div class="selectBox" ><img src="<?php echo $image_url;?>files/images/common/tick.png" ng-show="createInfo.majiang.horse_count==2"/></div>
									<div class="selectText">2匹</div>
								</div>
								<div class="selectItem" ng-click="selectChange(2,4)">
									<div class="selectBox" ><img src="<?php echo $image_url;?>files/images/common/tick.png" ng-show="createInfo.majiang.horse_count==4"/></div>
									<div class="selectText">4匹</div>
								</div>
							</div>
							<div>
								<div class="selectItem" ng-click="selectChange(2,6)" >
									<div class="selectBox" ><img src="<?php echo $image_url;?>files/images/common/tick.png" ng-show="createInfo.majiang.horse_count==6"/></div>
									<div class="selectText">6匹</div>
								</div>
								<div class="selectItem" ng-click="selectChange(2,8)">
									<div class="selectBox" ><img src="<?php echo $image_url;?>files/images/common/tick.png" ng-show="createInfo.majiang.horse_count==8" /></div>
									<div class="selectText">8匹</div>
								</div>
								<div class="selectItem" ng-click="selectChange(2,1)">
									<div class="selectBox" ><img src="<?php echo $image_url;?>files/images/common/tick.png" ng-show="createInfo.majiang.horse_count==1" /></div>
									<div class="selectText">爆炸马</div>
								</div>
							</div>
						</div>
					</div>
					<div class="selectPart" style="height:4.5vh;">
						<div class="selectTitle">规则：</div>
						<div class="selectList">
								<div class="selectItem"  ng-click="selectChange(3)">
									<div class="selectBox" ><img src="<?php echo $image_url;?>files/images/common/tick.png" ng-show="createInfo.majiang.qianggang==1" /></div>
									<div class="selectText">抢杠全包</div>
								</div>
								<div class="selectItem" ng-click="selectChange(5)" >
									<div class="selectBox" ><img src="<?php echo $image_url;?>files/images/common/tick.png" ng-show="createInfo.majiang.chengbao==1" /></div>
									<div class="selectText">杠爆全包</div>
								</div>
						</div>

					</div>
					<div class="selectPart" style="height:9vh;">
					<div class="selectTitle">房卡：</div>
						<div class="selectList">
							<div class="selectItem"  ng-click="selectChange(4,1)">
								<div class="selectBox" ><img src="<?php echo $image_url;?>files/images/common/tick.png" ng-show="createInfo.majiang.ticket_count==1" /></div>
								<div class="selectText" >8局X1张房卡</div>
							</div>
							<div class="selectItem"  ng-click="selectChange(4,2)">
								<div class="selectBox" ><img src="<?php echo $image_url;?>files/images/common/tick.png" ng-show="createInfo.majiang.ticket_count==2" /></div>
								<div class="selectText" >16局X2张房卡</div>
							</div>
						</div>
					</div>
			</div>

			<div class="bullRull" ng-if="createInfo.isShow==5">
				<div class="selectPart">
					<div class="selectTitle">底分：</div>
					<div class="selectList" >
						<div class="selectItem" ng-click="selectChange(1,1)" ng-show="createInfo.bull9.banker_mode!=4">
							<div class="selectBox" ><img src="<?php echo $image_url;?>files/images/common/tick.png" ng-show="createInfo.bull9.score_type==1"/></div>
							<div class="selectText">1分</div>
						</div>

                        <div class="selectItem" ng-click="selectChange(1,6)" ng-show="createInfo.bull9.banker_mode!=4">
                            <div class="selectBox" ><img src="<?php echo $image_url;?>files/images/common/tick.png" ng-show="createInfo.bull9.score_type==6"/></div>
                            <div class="selectText">2分</div>
                        </div>

						<div class="selectItem" ng-click="selectChange(1,2)" ng-show="createInfo.bull9.banker_mode!=4">
							<div class="selectBox" ><img src="<?php echo $image_url;?>files/images/common/tick.png" ng-show="createInfo.bull9.score_type==2"/></div>
							<div class="selectText">3分</div>
						</div>
						<div class="selectItem" ng-click="selectChange(1,3)" >
							<div class="selectBox" ><img src="<?php echo $image_url;?>files/images/common/tick.png" ng-show="createInfo.bull9.score_type==3" /></div>
							<div class="selectText">5分</div>
						</div>
						<div class="selectItem" ng-click="selectChange(1,4)" ng-show="createInfo.bull9.banker_mode==4">
							<div class="selectBox" ><img src="<?php echo $image_url;?>files/images/common/tick.png" ng-show="createInfo.bull9.score_type==4" /></div>
							<div class="selectText">10分</div>
						</div>
						<div class="selectItem" ng-click="selectChange(1,5)" ng-show="createInfo.bull9.banker_mode==4">
							<div class="selectBox" ><img src="<?php echo $image_url;?>files/images/common/tick.png" ng-show="createInfo.bull9.score_type==5" /></div>
							<div class="selectText">20分</div>
						</div>
                        <div class="selectItem changeWidth" ng-click="selectChange(1,7)" >
                            <div class="selectBox" ><img src="<?php echo $image_url;?>files/images/common/tick.png" ng-show="createInfo.bull9.score_type==7"/></div>
                            <div class="nbullfootSelectIcon">
                                    <div class="footSanJiao"></div>
                            </div>
                        </div>
                        <select id="selectList" ng-model="selectValue" ng-change="listChange(selectValue, 'bull9')" 
                        ng-disabled="selectBull91" ng-init="selectValue=selectBull9Value" ng-options="item for item in selectArr"
                        ng-show="createInfo.bull9.banker1==='selected'">
                        </select>
                        <select id="selectList" ng-model="selectValue" ng-change="listChange(selectValue, 'bull9')" 
                        ng-disabled="selectBull93" ng-init="selectValue=selectBull9Value" ng-options="item for item in selectArr"
                        ng-show="createInfo.bull9.banker3==='selected'">
                        </select>
                        <select id="selectList" ng-model="selectValue" ng-change="listChange(selectValue, 'bull9')" 
                        ng-disabled="selectBull92" ng-init="selectValue=selectBull9Value" ng-options="item for item in selectArr"
                        ng-show="createInfo.bull9.banker2==='selected'">
                        </select>
                        <select id="selectList" ng-model="selectValue" ng-change="listChange(selectValue, 'bull9')" 
                        ng-disabled="selectBull95" ng-init="selectValue=selectBull9Value" ng-options="item for item in selectArr"
                        ng-show="createInfo.bull9.banker5==='selected'">
                        </select>
                        <select id="selectList" ng-model="selectValue" ng-change="listChange(selectValue, 'bull9')" 
                        ng-disabled="selectBull94" ng-init="selectValue=selectBull9Value" ng-options="item for item in selectArr"
                        ng-show="createInfo.bull9.banker4==='selected'">
                        </select>
					</div>
				</div>

                <div class="selectPart timeItems" style="height:9vh;">
                    <div class="selectTitle">时间：</div>
                    <div class="selectList" >
                        <label class="selectItem" style="position:relative;">
                            <span style="color:#633201;">准备</span>
                            <select class="nbullSelect" ng-model="createInfo.bull9.countDown[0]" ng-options="x for x in defaultTime5To10">
                            </select>
                            <div class="nbullSelectIcon">
                                    <div class="sanjiao"></div>
                            </div>
                        </label>
                        <label class="selectItem" style="position:relative;">
                            <span style="color:#633201;">抢庄</span>
                            <select class="nbullSelect" ng-model="createInfo.bull9.countDown[1]" ng-options="x for x in defaultTime5To10">
                            </select>
                            <div class="nbullSelectIcon">
                                    <div class="sanjiao"></div>
                            </div>
                        </label>
                        <label class="selectItem" style="position:relative;">
                            <span style="color:#633201;">下注</span>
                            <select class="nbullSelect" ng-model="createInfo.bull9.countDown[2]" ng-options="x for x in defaultTime5To10">
                            </select>
                            <div class="nbullSelectIcon">
                                    <div class="sanjiao"></div>
                            </div>
                        </label>
                        <label class="selectItem" style="position:relative;">
                            <span style="color:#633201;">摊牌</span>
                            <select class="nbullSelect" ng-model="createInfo.bull9.countDown[3]" ng-options="x for x in defaultTime5To10">
                            </select>
                            <div class="nbullSelectIcon">
                                    <div class="sanjiao"></div>
                            </div>
                        </label>
                    </div>
                </div>

				<div class="selectPart" style="height:10vh;">
					<div class="selectTitle">规则：</div>
					<div class="selectList">
						<div class="selectItem"  ng-click="selectChange(2,1)">
							<div class="selectBox" ><img src="<?php echo $image_url;?>files/images/common/tick.png" ng-show="createInfo.bull9.rule_type==1" /></div>
							<div class="selectText" >牛牛x3牛九x2牛八x2</div>
						</div>
						<div class="selectItem"  ng-click="selectChange(2,2)">
							<div class="selectBox" ><img src="<?php echo $image_url;?>files/images/common/tick.png" ng-show="createInfo.bull9.rule_type==2" /></div>
							<div class="selectText" >牛牛x4牛九x3牛八x2牛七x2</div>
						</div>
					</div>
				</div>

				<div class="selectPart" style="height:25vh;">
					<div class="selectTitle">牌型：</div>
					<div class="selectList">
                        <div class="selectItem"  ng-click="selectChange(3,8)">
                            <div class="selectBox" ><img src="<?php echo $image_url;?>files/images/common/tick.png" ng-show="createInfo.bull9.has_ghost==1" /></div>
                            <div class="selectText" >有癞子</div>
                        </div>
                        <div class="selectItem"  ng-click="selectChange(3,9)">
                            <div class="selectBox" ><img src="<?php echo $image_url;?>files/images/common/tick.png" ng-show="createInfo.bull9.is_cardfour==1" /></div>
                            <div class="selectText" >四花牛(4倍)</div>
                        </div>
                        <div class="selectItem"  ng-click="selectChange(3,1)">
                            <div class="selectBox" ><img src="<?php echo $image_url;?>files/images/common/tick.png" ng-show="createInfo.bull9.is_cardfive==1" /></div>
                            <div class="selectText" >五花牛(5倍)</div>
                        </div>
                        <div class="selectItem" ng-click="selectChange(3,2)">
                            <div class="selectBox" ><img src="<?php echo $image_url;?>files/images/common/tick.png" ng-show="createInfo.bull9.is_straight==1" /></div>
                            <div class="selectText" >顺子牛(6倍)</div>
                        </div>
                        <div class="selectItem" ng-click="selectChange(3,3)">
                            <div class="selectBox" ><img src="<?php echo $image_url;?>files/images/common/tick.png" ng-show="createInfo.bull9.is_flush==1" /></div>
                            <div class="selectText" >同花牛(6倍)</div>
                        </div>
                        <div class="selectItem" ng-click="selectChange(3,4)">
                            <div class="selectBox" ><img src="<?php echo $image_url;?>files/images/common/tick.png" ng-show="createInfo.bull9.is_hulu==1" /></div>
                            <div class="selectText" >葫芦牛(6倍)</div>
                        </div>
                        <div class="selectItem" ng-click="selectChange(3,5)">
                            <div class="selectBox" ><img src="<?php echo $image_url;?>files/images/common/tick.png" ng-show="createInfo.bull9.is_cardbomb==1" /></div>
                            <div class="selectText" >炸弹牛(6倍)</div>
                        </div>
                        <div class="selectItem" ng-click="selectChange(3,7)">
                            <div class="selectBox" ><img src="<?php echo $image_url;?>files/images/common/tick.png" ng-show="createInfo.bull9.is_straightflush==1" /></div>
                            <div class="selectText" >同花顺(7倍)</div>
                        </div>
                        <div class="selectItem" ng-click="selectChange(3,6)">
                            <div class="selectBox" ><img src="<?php echo $image_url;?>files/images/common/tick.png" ng-show="createInfo.bull9.is_cardtiny==1" /></div>
                            <div class="selectText" >五小牛(8倍)</div>
                        </div>
					</div>
				</div>

				<div class="selectPart" style="height:5.5vh;">
					<div class="selectTitle">局数：</div>
					<div class="selectList">
						<div class="selectItem"  ng-click="selectChange(4,1)">
							<div class="selectBox" ><img src="<?php echo $image_url;?>files/images/common/tick.png" ng-show="createInfo.bull9.ticket_type==1" /></div>
							<div class="selectText" >12局X2房卡</div>
						</div>
						<div class="selectItem"  ng-click="selectChange(4,2)">
							<div class="selectBox" ><img src="<?php echo $image_url;?>files/images/common/tick.png" ng-show="createInfo.bull9.ticket_type==2" /></div>
							<div class="selectText" >24局X4房卡</div>
						</div>
					</div>
				</div>

                <div class="selectPart" style="height:10vh;">
                    <div class="selectTitle">倍数：</div>
                    <div class="selectList">
                        <div class="selectItem"  ng-click="selectChange(6,1)">
                            <div class="selectBox" ><img src="<?php echo $image_url;?>files/images/common/tick.png" ng-show="createInfo.bull9.times_type==1" /></div>
                            <div class="selectText" >1，2，4，5</div>
                        </div>
                        <div class="selectItem"  ng-click="selectChange(6,2)">
                            <div class="selectBox" ><img src="<?php echo $image_url;?>files/images/common/tick.png" ng-show="createInfo.bull9.times_type==2" /></div>
                            <div class="selectText" >1，3，5，8</div>
                        </div>
                        <div class="selectItem"  ng-click="selectChange(6,3)">
                            <div class="selectBox" ><img src="<?php echo $image_url;?>files/images/common/tick.png" ng-show="createInfo.bull9.times_type==3" /></div>
                            <div class="selectText" >2，4，6，10</div>
                        </div>
                    </div>
                </div>

				<div class="selectPart" style="height:10vh;" ng-if="createInfo.bull9.banker_mode==5">
					<div class="selectTitle">上庄：</div>
					<div class="selectList">
						<div class="selectItem"  ng-click="selectChange(5,1)">
							<div class="selectBox" ><img src="<?php echo $image_url;?>files/images/common/tick.png" ng-show="createInfo.bull9.banker_score==1" /></div>
							<div class="selectText" >无</div>
						</div>
						<div class="selectItem" ng-click="selectChange(5,2)">
							<div class="selectBox" ><img src="<?php echo $image_url;?>files/images/common/tick.png" ng-show="createInfo.bull9.banker_score==2" /></div>
							<div class="selectText" >300</div>
						</div>
						<div class="selectItem" ng-click="selectChange(5,3)">
							<div class="selectBox" ><img src="<?php echo $image_url;?>files/images/common/tick.png" ng-show="createInfo.bull9.banker_score==3" /></div>
							<div class="selectText" >500</div>
						</div>
						<div class="selectItem" ng-click="selectChange(5,4)">
							<div class="selectBox" ><img src="<?php echo $image_url;?>files/images/common/tick.png" ng-show="createInfo.bull9.banker_score==4" /></div>
							<div class="selectText" >1000</div>
						</div>
					</div>
				</div>
			</div>

            <div class="bullRull" ng-if="createInfo.isShow==90&&createInfo.vbf.game==91">
                <div class="selectPart" style="height:4.5vh;">
                    <div class="selectTitle">底分：</div>
                    <div class="selectList" >
                        <div class="selectItem" ng-click="selectChange(1,1)">
                            <div class="selectBox" ><img src="<?php echo $image_url;?>files/images/common/tick.png" ng-show="createInfo.vbull9.score_type==1"/></div>
                            <div class="selectText">1分</div>
                        </div>

                        <div class="selectItem" ng-click="selectChange(1,2)">
                            <div class="selectBox" ><img src="<?php echo $image_url;?>files/images/common/tick.png" ng-show="createInfo.vbull9.score_type==2"/></div>
                            <div class="selectText">2分</div>
                        </div>

                        <div class="selectItem" ng-click="selectChange(1,3)">
                            <div class="selectBox" ><img src="<?php echo $image_url;?>files/images/common/tick.png" ng-show="createInfo.vbull9.score_type==3"/></div>
                            <div class="selectText">3分</div>
                        </div>
                        <div class="selectItem" ng-click="selectChange(1,5)">
                            <div class="selectBox" ><img src="<?php echo $image_url;?>files/images/common/tick.png" ng-show="createInfo.vbull9.score_type==5" /></div>
                            <div class="selectText">5分</div>
                        </div>
                    </div>
                </div>

                <div class="selectPart" style="height:9vh;">
                    <div class="selectTitle">规则：</div>
                    <div class="selectList">
                        <div class="selectItem"  ng-click="selectChange(2,1)">
                            <div class="selectBox" ><img src="<?php echo $image_url;?>files/images/common/tick.png" ng-show="createInfo.vbull9.rule_type==1" /></div>
                            <div class="selectText" >牛牛x3牛九x2牛八x2</div>
                        </div>
                        <div class="selectItem"  ng-click="selectChange(2,2)">
                            <div class="selectBox" ><img src="<?php echo $image_url;?>files/images/common/tick.png" ng-show="createInfo.vbull9.rule_type==2" /></div>
                            <div class="selectText" >牛牛x4牛九x3牛八x2牛七x2</div>
                        </div>
                    </div>
                </div>

                <div class="selectPart" style="height:9vh;">
                    <div class="selectTitle">牌型：</div>
                    <div class="selectList">
                        <div class="selectItem"  ng-click="selectChange(3,9)">
                            <div class="selectBox" ><img src="<?php echo $image_url;?>files/images/common/tick.png" ng-show="createInfo.vbull9.is_cardfour==1" /></div>
                            <div class="selectText" >四花牛(4倍)</div>
                        </div>
                        <div class="selectItem"  ng-click="selectChange(3,1)">
                            <div class="selectBox" ><img src="<?php echo $image_url;?>files/images/common/tick.png" ng-show="createInfo.vbull9.is_cardfive==1" /></div>
                            <div class="selectText" >五花牛(5倍)</div>
                        </div>
                        <div class="selectItem"  ng-click="selectChange(3,2)">
                            <div class="selectBox" ><img src="<?php echo $image_url;?>files/images/common/tick.png" ng-show="createInfo.vbull9.is_cardbomb==1" /></div>
                            <div class="selectText" >炸弹牛(6倍)</div>
                        </div>
                        <div class="selectItem"  ng-click="selectChange(3,3)">
                            <div class="selectBox" ><img src="<?php echo $image_url;?>files/images/common/tick.png" ng-show="createInfo.vbull9.is_cardtiny==1" /></div>
                            <div class="selectText" >五小牛(8倍)</div>
                        </div>
                    </div>
                </div>

                <div class="selectPart" style="height:4.5vh;">
                    <div class="selectTitle">局数：</div>
                    <div class="selectList">
                        <div class="selectItem"  ng-click="selectChange(4,1)">
                            <div class="selectBox" ><img src="<?php echo $image_url;?>files/images/common/tick.png" ng-show="createInfo.vbull9.ticket_type==1" /></div>
                            <div class="selectText" >12局X2房卡</div>
                        </div>
                        <div class="selectItem"  ng-click="selectChange(4,2)">
                            <div class="selectBox" ><img src="<?php echo $image_url;?>files/images/common/tick.png" ng-show="createInfo.vbull9.ticket_type==2" /></div>
                            <div class="selectText" >24局X4房卡</div>
                        </div>
                    </div>
                </div>

                <div class="selectPart" style="height:9vh;">
                    <div class="selectTitle">倍数：</div>
                    <div class="selectList">
                        <div class="selectItem"  ng-click="selectChange(5,1)">
                            <div class="selectBox" ><img src="<?php echo $image_url;?>files/images/common/tick.png" ng-show="createInfo.vbull9.times_type==1" /></div>
                            <div class="selectText" >1，2，4，5</div>
                        </div>
                        <div class="selectItem"  ng-click="selectChange(5,2)">
                            <div class="selectBox" ><img src="<?php echo $image_url;?>files/images/common/tick.png" ng-show="createInfo.vbull9.times_type==2" /></div>
                            <div class="selectText" >1，3，5，8</div>
                        </div>
                        <div class="selectItem"  ng-click="selectChange(5,3)">
                            <div class="selectBox" ><img src="<?php echo $image_url;?>files/images/common/tick.png" ng-show="createInfo.vbull9.times_type==3" /></div>
                            <div class="selectText" >2，4，6，10</div>
                        </div>
                    </div>
                </div>

                <div class="selectPart" style="height:9vh;">
                    <div class="selectTitle">准入：</div>
                    <div class="selectList">
                        <div class="selectItem"  ng-click="selectChange(6,1)">
                            <div class="selectBox" ><img src="<?php echo $image_url;?>files/images/common/tick.png" ng-show="createInfo.vbull9.bean_type==1" /></div>
                            <div class="selectText" >≥50豆</div>
                        </div>
                        <div class="selectItem"  ng-click="selectChange(6,2)">
                            <div class="selectBox" ><img src="<?php echo $image_url;?>files/images/common/tick.png" ng-show="createInfo.vbull9.bean_type==2" /></div>
                            <div class="selectText" >≥100豆</div>
                        </div>
                        <div class="selectItem"  ng-click="selectChange(6,3)">
                            <div class="selectBox" ><img src="<?php echo $image_url;?>files/images/common/tick.png" ng-show="createInfo.vbull9.bean_type==3" /></div>
                            <div class="selectText" >≥300豆</div>
                        </div>
                        <div class="selectItem"  ng-click="selectChange(6,4)">
                            <div class="selectBox" ><img src="<?php echo $image_url;?>files/images/common/tick.png" ng-show="createInfo.vbull9.bean_type==4" /></div>
                            <div class="selectText" >≥500豆</div>
                        </div>
                    </div>
                </div>
            </div>

            <div class="bullRull" ng-if="createInfo.isShow==90&&createInfo.vbf.game==93">
                <div class="selectPart" style="height:4.5vh;">
                    <div class="selectTitle">底分：</div>
                    <div class="selectList" >
                        <div class="selectItem" ng-click="selectChange(1,1)">
                            <div class="selectBox" ><img src="<?php echo $image_url;?>files/images/common/tick.png" ng-show="createInfo.vbull6.score_type==1"/></div>
                            <div class="selectText">1分</div>
                        </div>

                        <div class="selectItem" ng-click="selectChange(1,2)">
                            <div class="selectBox" ><img src="<?php echo $image_url;?>files/images/common/tick.png" ng-show="createInfo.vbull6.score_type==2"/></div>
                            <div class="selectText">2分</div>
                        </div>

                        <div class="selectItem" ng-click="selectChange(1,3)">
                            <div class="selectBox" ><img src="<?php echo $image_url;?>files/images/common/tick.png" ng-show="createInfo.vbull6.score_type==3"/></div>
                            <div class="selectText">3分</div>
                        </div>
                        <div class="selectItem" ng-click="selectChange(1,5)">
                            <div class="selectBox" ><img src="<?php echo $image_url;?>files/images/common/tick.png" ng-show="createInfo.vbull6.score_type==5" /></div>
                            <div class="selectText">5分</div>
                        </div>
                    </div>
                </div>

                <div class="selectPart" style="height:9vh;">
                    <div class="selectTitle">规则：</div>
                    <div class="selectList">
                        <div class="selectItem"  ng-click="selectChange(2,1)">
                            <div class="selectBox" ><img src="<?php echo $image_url;?>files/images/common/tick.png" ng-show="createInfo.vbull6.rule_type==1" /></div>
                            <div class="selectText" >牛牛x3牛九x2牛八x2</div>
                        </div>
                        <div class="selectItem"  ng-click="selectChange(2,2)">
                            <div class="selectBox" ><img src="<?php echo $image_url;?>files/images/common/tick.png" ng-show="createInfo.vbull6.rule_type==2" /></div>
                            <div class="selectText" >牛牛x4牛九x3牛八x2牛七x2</div>
                        </div>
                    </div>
                </div>

                <div class="selectPart" style="height:9vh;">
                    <div class="selectTitle">牌型：</div>
                    <div class="selectList">
                        <div class="selectItem"  ng-click="selectChange(3,9)">
                            <div class="selectBox" ><img src="<?php echo $image_url;?>files/images/common/tick.png" ng-show="createInfo.vbull6.is_cardfour==1" /></div>
                            <div class="selectText" >四花牛(4倍)</div>
                        </div>
                        <div class="selectItem"  ng-click="selectChange(3,1)">
                            <div class="selectBox" ><img src="<?php echo $image_url;?>files/images/common/tick.png" ng-show="createInfo.vbull6.is_cardfive==1" /></div>
                            <div class="selectText" >五花牛(5倍)</div>
                        </div>
                        <div class="selectItem"  ng-click="selectChange(3,2)">
                            <div class="selectBox" ><img src="<?php echo $image_url;?>files/images/common/tick.png" ng-show="createInfo.vbull6.is_cardbomb==1" /></div>
                            <div class="selectText" >炸弹牛(6倍)</div>
                        </div>
                        <div class="selectItem"  ng-click="selectChange(3,3)">
                            <div class="selectBox" ><img src="<?php echo $image_url;?>files/images/common/tick.png" ng-show="createInfo.vbull6.is_cardtiny==1" /></div>
                            <div class="selectText" >五小牛(8倍)</div>
                        </div>
                    </div>
                </div>

                <div class="selectPart" style="height:4.5vh;">
                    <div class="selectTitle">局数：</div>
                    <div class="selectList">
                        <div class="selectItem"  ng-click="selectChange(4,1)">
                            <div class="selectBox" ><img src="<?php echo $image_url;?>files/images/common/tick.png" ng-show="createInfo.vbull6.ticket_type==1" /></div>
                            <div class="selectText" >10局X1房卡</div>
                        </div>
                        <div class="selectItem"  ng-click="selectChange(4,2)">
                            <div class="selectBox" ><img src="<?php echo $image_url;?>files/images/common/tick.png" ng-show="createInfo.vbull6.ticket_type==2" /></div>
                            <div class="selectText" >20局X2房卡</div>
                        </div>
                    </div>
                </div>

                <div class="selectPart" style="height:9vh;">
                    <div class="selectTitle">倍数：</div>
                    <div class="selectList">
                        <div class="selectItem"  ng-click="selectChange(5,1)">
                            <div class="selectBox" ><img src="<?php echo $image_url;?>files/images/common/tick.png" ng-show="createInfo.vbull6.times_type==1" /></div>
                            <div class="selectText" >1，2，4，5</div>
                        </div>
                        <div class="selectItem"  ng-click="selectChange(5,2)">
                            <div class="selectBox" ><img src="<?php echo $image_url;?>files/images/common/tick.png" ng-show="createInfo.vbull6.times_type==2" /></div>
                            <div class="selectText" >1，3，5，8</div>
                        </div>
                        <div class="selectItem"  ng-click="selectChange(5,3)">
                            <div class="selectBox" ><img src="<?php echo $image_url;?>files/images/common/tick.png" ng-show="createInfo.vbull6.times_type==3" /></div>
                            <div class="selectText" >2，4，6，10</div>
                        </div>
                    </div>
                </div>

                <div class="selectPart" style="height:9vh;">
                    <div class="selectTitle">准入：</div>
                    <div class="selectList">
                        <div class="selectItem"  ng-click="selectChange(6,1)">
                            <div class="selectBox" ><img src="<?php echo $image_url;?>files/images/common/tick.png" ng-show="createInfo.vbull6.bean_type==1" /></div>
                            <div class="selectText" >≥50豆</div>
                        </div>
                        <div class="selectItem"  ng-click="selectChange(6,2)">
                            <div class="selectBox" ><img src="<?php echo $image_url;?>files/images/common/tick.png" ng-show="createInfo.vbull6.bean_type==2" /></div>
                            <div class="selectText" >≥100豆</div>
                        </div>
                        <div class="selectItem"  ng-click="selectChange(6,3)">
                            <div class="selectBox" ><img src="<?php echo $image_url;?>files/images/common/tick.png" ng-show="createInfo.vbull6.bean_type==3" /></div>
                            <div class="selectText" >≥300豆</div>
                        </div>
                        <div class="selectItem"  ng-click="selectChange(6,4)">
                            <div class="selectBox" ><img src="<?php echo $image_url;?>files/images/common/tick.png" ng-show="createInfo.vbull6.bean_type==4" /></div>
                            <div class="selectText" >≥500豆</div>
                        </div>
                    </div>
                </div>
            </div>

            <div class="bullRull" ng-if="createInfo.isShow==90&&createInfo.vbf.game==94">
                <div class="selectPart" style="height:4.5vh;">
                    <div class="selectTitle">底分：</div>
                    <div class="selectList" >
                        <div class="selectItem" ng-click="selectChange(1,1)">
                            <div class="selectBox" ><img src="<?php echo $image_url;?>files/images/common/tick.png" ng-show="createInfo.vbull12.score_type==1"/></div>
                            <div class="selectText">1分</div>
                        </div>

                        <div class="selectItem" ng-click="selectChange(1,2)">
                            <div class="selectBox" ><img src="<?php echo $image_url;?>files/images/common/tick.png" ng-show="createInfo.vbull12.score_type==2"/></div>
                            <div class="selectText">2分</div>
                        </div>

                        <div class="selectItem" ng-click="selectChange(1,3)">
                            <div class="selectBox" ><img src="<?php echo $image_url;?>files/images/common/tick.png" ng-show="createInfo.vbull12.score_type==3"/></div>
                            <div class="selectText">3分</div>
                        </div>
                        <div class="selectItem" ng-click="selectChange(1,5)">
                            <div class="selectBox" ><img src="<?php echo $image_url;?>files/images/common/tick.png" ng-show="createInfo.vbull12.score_type==5" /></div>
                            <div class="selectText">5分</div>
                        </div>
                    </div>
                </div>

                <div class="selectPart" style="height:9vh;">
                    <div class="selectTitle">规则：</div>
                    <div class="selectList">
                        <div class="selectItem"  ng-click="selectChange(2,1)">
                            <div class="selectBox" ><img src="<?php echo $image_url;?>files/images/common/tick.png" ng-show="createInfo.vbull12.rule_type==1" /></div>
                            <div class="selectText" >牛牛x3牛九x2牛八x2</div>
                        </div>
                        <div class="selectItem"  ng-click="selectChange(2,2)">
                            <div class="selectBox" ><img src="<?php echo $image_url;?>files/images/common/tick.png" ng-show="createInfo.vbull12.rule_type==2" /></div>
                            <div class="selectText" >牛牛x4牛九x3牛八x2牛七x2</div>
                        </div>
                    </div>
                </div>

                <div class="selectPart" style="height:9vh;">
                    <div class="selectTitle">牌型：</div>
                    <div class="selectList">
                        <div class="selectItem"  ng-click="selectChange(3,9)">
                            <div class="selectBox" ><img src="<?php echo $image_url;?>files/images/common/tick.png" ng-show="createInfo.vbull12.is_cardfour==1" /></div>
                            <div class="selectText" >四花牛(4倍)</div>
                        </div>
                        <div class="selectItem"  ng-click="selectChange(3,1)">
                            <div class="selectBox" ><img src="<?php echo $image_url;?>files/images/common/tick.png" ng-show="createInfo.vbull12.is_cardfive==1" /></div>
                            <div class="selectText" >五花牛(5倍)</div>
                        </div>
                        <div class="selectItem"  ng-click="selectChange(3,2)">
                            <div class="selectBox" ><img src="<?php echo $image_url;?>files/images/common/tick.png" ng-show="createInfo.vbull12.is_cardbomb==1" /></div>
                            <div class="selectText" >炸弹牛(6倍)</div>
                        </div>
                        <div class="selectItem"  ng-click="selectChange(3,3)">
                            <div class="selectBox" ><img src="<?php echo $image_url;?>files/images/common/tick.png" ng-show="createInfo.vbull12.is_cardtiny==1" /></div>
                            <div class="selectText" >五小牛(8倍)</div>
                        </div>
                    </div>
                </div>

                <div class="selectPart" style="height:4.5vh;">
                    <div class="selectTitle">局数：</div>
                    <div class="selectList">
                        <div class="selectItem"  ng-click="selectChange(4,1)">
                            <div class="selectBox" ><img src="<?php echo $image_url;?>files/images/common/tick.png" ng-show="createInfo.vbull12.ticket_type==1" /></div>
                            <div class="selectText" >12局X2房卡</div>
                        </div>
                        <div class="selectItem"  ng-click="selectChange(4,2)">
                            <div class="selectBox" ><img src="<?php echo $image_url;?>files/images/common/tick.png" ng-show="createInfo.vbull12.ticket_type==2" /></div>
                            <div class="selectText" >24局X4房卡</div>
                        </div>
                    </div>
                </div>

                <div class="selectPart" style="height:9vh;">
                    <div class="selectTitle">倍数：</div>
                    <div class="selectList">
                        <div class="selectItem"  ng-click="selectChange(5,1)">
                            <div class="selectBox" ><img src="<?php echo $image_url;?>files/images/common/tick.png" ng-show="createInfo.vbull12.times_type==1" /></div>
                            <div class="selectText" >1，2，4，5</div>
                        </div>
                        <div class="selectItem"  ng-click="selectChange(5,2)">
                            <div class="selectBox" ><img src="<?php echo $image_url;?>files/images/common/tick.png" ng-show="createInfo.vbull12.times_type==2" /></div>
                            <div class="selectText" >1，3，5，8</div>
                        </div>
                        <div class="selectItem"  ng-click="selectChange(5,3)">
                            <div class="selectBox" ><img src="<?php echo $image_url;?>files/images/common/tick.png" ng-show="createInfo.vbull12.times_type==3" /></div>
                            <div class="selectText" >2，4，6，10</div>
                        </div>
                    </div>
                </div>

                <div class="selectPart" style="height:9vh;">
                    <div class="selectTitle">准入：</div>
                    <div class="selectList">
                        <div class="selectItem"  ng-click="selectChange(6,1)">
                            <div class="selectBox" ><img src="<?php echo $image_url;?>files/images/common/tick.png" ng-show="createInfo.vbull12.bean_type==1" /></div>
                            <div class="selectText" >≥50豆</div>
                        </div>
                        <div class="selectItem"  ng-click="selectChange(6,2)">
                            <div class="selectBox" ><img src="<?php echo $image_url;?>files/images/common/tick.png" ng-show="createInfo.vbull12.bean_type==2" /></div>
                            <div class="selectText" >≥100豆</div>
                        </div>
                        <div class="selectItem"  ng-click="selectChange(6,3)">
                            <div class="selectBox" ><img src="<?php echo $image_url;?>files/images/common/tick.png" ng-show="createInfo.vbull12.bean_type==3" /></div>
                            <div class="selectText" >≥300豆</div>
                        </div>
                        <div class="selectItem"  ng-click="selectChange(6,4)">
                            <div class="selectBox" ><img src="<?php echo $image_url;?>files/images/common/tick.png" ng-show="createInfo.vbull12.bean_type==4" /></div>
                            <div class="selectText" >≥500豆</div>
                        </div>
                    </div>
                </div>
            </div>

            <div class="bullRull" ng-if="createInfo.isShow==8">
                <div class="selectPart">
                    <div class="selectTitle">底分：</div>
                    <div class="selectList" >
                        <div class="selectItem" ng-click="selectChange(1,1)" ng-show="createInfo.tbull.banker_mode!=4">
                            <div class="selectBox" ><img src="<?php echo $image_url;?>files/images/common/tick.png" ng-show="createInfo.tbull.score_type==1"/></div>
                            <div class="selectText">1分</div>
                        </div>

                        <div class="selectItem" ng-click="selectChange(1,6)" ng-show="createInfo.tbull.banker_mode!=4">
                            <div class="selectBox" ><img src="<?php echo $image_url;?>files/images/common/tick.png" ng-show="createInfo.tbull.score_type==6"/></div>
                            <div class="selectText">2分</div>
                        </div>

                        <div class="selectItem" ng-click="selectChange(1,2)" ng-show="createInfo.tbull.banker_mode!=4">
                            <div class="selectBox" ><img src="<?php echo $image_url;?>files/images/common/tick.png" ng-show="createInfo.tbull.score_type==2"/></div>
                            <div class="selectText">3分</div>
                        </div>
                        <div class="selectItem" ng-click="selectChange(1,3)" >
                            <div class="selectBox" ><img src="<?php echo $image_url;?>files/images/common/tick.png" ng-show="createInfo.tbull.score_type==3" /></div>
                            <div class="selectText">5分</div>
                        </div>
                        <div class="selectItem" ng-click="selectChange(1,4)" ng-show="createInfo.tbull.banker_mode==4">
                            <div class="selectBox" ><img src="<?php echo $image_url;?>files/images/common/tick.png" ng-show="createInfo.tbull.score_type==4" /></div>
                            <div class="selectText">10分</div>
                        </div>
                        <div class="selectItem" ng-click="selectChange(1,5)" ng-show="createInfo.tbull.banker_mode==4">
                            <div class="selectBox" ><img src="<?php echo $image_url;?>files/images/common/tick.png" ng-show="createInfo.tbull.score_type==5" /></div>
                            <div class="selectText">20分</div>
                        </div>
                        <div class="selectItem changeWidth" ng-click="selectChange(1,7)">
                            <div class="selectBox" ><img src="<?php echo $image_url;?>files/images/common/tick.png" ng-show="createInfo.tbull.score_type==7"/></div>
                            <div class="tbullfootSelectIcon">
                                    <div class="footSanJiao"></div>
                            </div>
                        </div>
                        <select id="selectList" ng-model="selectValue" ng-change="listChange(selectValue, 'tbull')" 
                        ng-disabled="selectTbull1" ng-init="selectValue=selectTbullValue" ng-options="item for item in selectArr"
                        ng-show="createInfo.tbull.banker1==='selected'">
                        </select>
                        <select id="selectList" ng-model="selectValue" ng-change="listChange(selectValue, 'tbull')" 
                        ng-disabled="selectTbull2" ng-init="selectValue=selectTbullValue" ng-options="item for item in selectArr"
                        ng-show="createInfo.tbull.banker2==='selected'">
                        </select>
                        <select id="selectList" ng-model="selectValue" ng-change="listChange(selectValue, 'tbull')" 
                        ng-disabled="selectTbull3" ng-init="selectValue=selectTbullValue" ng-options="item for item in selectArr"
                        ng-show="createInfo.tbull.banker3==='selected'">
                        </select>
                        <select id="selectList" ng-model="selectValue" ng-change="listChange(selectValue, 'tbull')" 
                        ng-disabled="selectTbull5" ng-init="selectValue=selectTbullValue" ng-options="item for item in selectArr"
                        ng-show="createInfo.tbull.banker5==='selected'">
                        </select>
                        <select id="selectList" ng-model="selectValue" ng-change="listChange(selectValue, 'tbull')" 
                        ng-disabled="selectTbull4" ng-init="selectValue=selectTbullValue" ng-options="item for item in selectArr"
                        ng-show="createInfo.tbull.banker4==='selected'">
                        </select>
                    </div>
                </div>

                <div class="selectPart timeItems" style="height:9vh;">
                    <div class="selectTitle">时间：</div>
                    <div class="selectList" >
                        <label class="selectItem" style="position:relative;">
                            <span style="color:#633201;">准备</span>
                            <select class="tbullSelect" ng-model="createInfo.tbull.countDown[0]" ng-options="x for x in defaultTime5To10">
                            </select>
                            <div class="tbullSelectIcon">
                                    <div class="sanjiao"></div>
                            </div>
                        </label>
                        <label class="selectItem" style="position:relative;">
                            <span style="color:#633201;">抢庄</span>
                            <select class="tbullSelect" ng-model="createInfo.tbull.countDown[1]" ng-options="x for x in defaultTime5To10">
                            </select>
                            <div class="tbullSelectIcon">
                                    <div class="sanjiao"></div>
                            </div>
                        </label>
                        <label class="selectItem" style="position:relative;">
                            <span style="color:#633201;">下注</span>
                            <select class="tbullSelect" ng-model="createInfo.tbull.countDown[2]" ng-options="x for x in defaultTime5To10">
                            </select>
                            <div class="tbullSelectIcon">
                                    <div class="sanjiao"></div>
                            </div>
                        </label>
                        <label class="selectItem" style="position:relative;">
                            <span style="color:#633201;">摊牌</span>
                            <select class="tbullSelect" ng-model="createInfo.tbull.countDown[3]" ng-options="x for x in defaultTime5To10">
                            </select>
                            <div class="tbullSelectIcon">
                                    <div class="sanjiao"></div>
                            </div>
                        </label>
                    </div>
                </div>

                <div class="selectPart" style="height:10vh;">
                    <div class="selectTitle">规则：</div>
                    <div class="selectList">
                        <div class="selectItem"  ng-click="selectChange(2,1)">
                            <div class="selectBox" ><img src="<?php echo $image_url;?>files/images/common/tick.png" ng-show="createInfo.tbull.rule_type==1" /></div>
                            <div class="selectText" >牛牛x3牛九x2牛八x2</div>
                        </div>
                        <div class="selectItem"  ng-click="selectChange(2,2)">
                            <div class="selectBox" ><img src="<?php echo $image_url;?>files/images/common/tick.png" ng-show="createInfo.tbull.rule_type==2" /></div>
                            <div class="selectText" >牛牛x4牛九x3牛八x2牛七x2</div>
                        </div>
                    </div>
                </div>

                <div class="selectPart" style="height:20vh;">
                    <div class="selectTitle">牌型：</div>
                    <div class="selectList">
                        <div class="selectItem"  ng-click="selectChange(3,9)">
                            <div class="selectBox" ><img src="<?php echo $image_url;?>files/images/common/tick.png" ng-show="createInfo.tbull.is_cardfour==1" /></div>
                            <div class="selectText" >四花牛(4倍)</div>
                        </div>
                        <div class="selectItem"  ng-click="selectChange(3,1)">
                            <div class="selectBox" ><img src="<?php echo $image_url;?>files/images/common/tick.png" ng-show="createInfo.tbull.is_cardfive==1" /></div>
                            <div class="selectText" >五花牛(5倍)</div>
                        </div>
                        <div class="selectItem" ng-click="selectChange(3,2)">
                            <div class="selectBox" ><img src="<?php echo $image_url;?>files/images/common/tick.png" ng-show="createInfo.tbull.is_straight==1" /></div>
                            <div class="selectText" >顺子牛(6倍)</div>
                        </div>
                        <div class="selectItem" ng-click="selectChange(3,3)">
                            <div class="selectBox" ><img src="<?php echo $image_url;?>files/images/common/tick.png" ng-show="createInfo.tbull.is_flush==1" /></div>
                            <div class="selectText" >同花牛(6倍)</div>
                        </div>
                        <div class="selectItem" ng-click="selectChange(3,4)">
                            <div class="selectBox" ><img src="<?php echo $image_url;?>files/images/common/tick.png" ng-show="createInfo.tbull.is_hulu==1" /></div>
                            <div class="selectText" >葫芦牛(6倍)</div>
                        </div>
                        <div class="selectItem" ng-click="selectChange(3,5)">
                            <div class="selectBox" ><img src="<?php echo $image_url;?>files/images/common/tick.png" ng-show="createInfo.tbull.is_cardbomb==1" /></div>
                            <div class="selectText" >炸弹牛(6倍)</div>
                        </div>
                        <div class="selectItem" ng-click="selectChange(3,7)">
                            <div class="selectBox" ><img src="<?php echo $image_url;?>files/images/common/tick.png" ng-show="createInfo.tbull.is_straightflush==1" /></div>
                            <div class="selectText" >同花顺(7倍)</div>
                        </div>
                        <div class="selectItem" ng-click="selectChange(3,6)">
                            <div class="selectBox" ><img src="<?php echo $image_url;?>files/images/common/tick.png" ng-show="createInfo.tbull.is_cardtiny==1" /></div>
                            <div class="selectText" >五小牛(8倍)</div>
                        </div>
                    </div>
                </div>

                <div class="selectPart" style="height:5.5vh;">
                    <div class="selectTitle">局数：</div>
                    <div class="selectList">
                        <div class="selectItem"  ng-click="selectChange(4,1)">
                            <div class="selectBox" ><img src="<?php echo $image_url;?>files/images/common/tick.png" ng-show="createInfo.tbull.ticket_type==1" /></div>
                            <div class="selectText" >12局X2房卡</div>
                        </div>
                        <div class="selectItem"  ng-click="selectChange(4,2)">
                            <div class="selectBox" ><img src="<?php echo $image_url;?>files/images/common/tick.png" ng-show="createInfo.tbull.ticket_type==2" /></div>
                            <div class="selectText" >24局X4房卡</div>
                        </div>
                    </div>
                </div>

                <div class="selectPart" style="height:10vh;">
                    <div class="selectTitle">倍数：</div>
                    <div class="selectList">
                        <div class="selectItem"  ng-click="selectChange(6,1)">
                            <div class="selectBox" ><img src="<?php echo $image_url;?>files/images/common/tick.png" ng-show="createInfo.tbull.times_type==1" /></div>
                            <div class="selectText" >1，2，4，5</div>
                        </div>
                        <div class="selectItem"  ng-click="selectChange(6,2)">
                            <div class="selectBox" ><img src="<?php echo $image_url;?>files/images/common/tick.png" ng-show="createInfo.tbull.times_type==2" /></div>
                            <div class="selectText" >1，3，5，8</div>
                        </div>
                        <div class="selectItem"  ng-click="selectChange(6,3)">
                            <div class="selectBox" ><img src="<?php echo $image_url;?>files/images/common/tick.png" ng-show="createInfo.tbull.times_type==3" /></div>
                            <div class="selectText" >2，4，6，10</div>
                        </div>
                    </div>
                </div>

                <div class="selectPart" style="height:10vh;" ng-if="createInfo.tbull.banker_mode==5">
                    <div class="selectTitle">上庄：</div>
                    <div class="selectList">
                        <div class="selectItem"  ng-click="selectChange(5,1)">
                            <div class="selectBox" ><img src="<?php echo $image_url;?>files/images/common/tick.png" ng-show="createInfo.tbull.banker_score==1" /></div>
                            <div class="selectText" >无</div>
                        </div>
                        <div class="selectItem" ng-click="selectChange(5,2)">
                            <div class="selectBox" ><img src="<?php echo $image_url;?>files/images/common/tick.png" ng-show="createInfo.tbull.banker_score==2" /></div>
                            <div class="selectText" >300</div>
                        </div>
                        <div class="selectItem" ng-click="selectChange(5,3)">
                            <div class="selectBox" ><img src="<?php echo $image_url;?>files/images/common/tick.png" ng-show="createInfo.tbull.banker_score==3" /></div>
                            <div class="selectText" >500</div>
                        </div>
                        <div class="selectItem" ng-click="selectChange(5,4)">
                            <div class="selectBox" ><img src="<?php echo $image_url;?>files/images/common/tick.png" ng-show="createInfo.tbull.banker_score==4" /></div>
                            <div class="selectText" >1000</div>
                        </div>
                    </div>
                </div>
            </div>

            <!---13人牛牛底分等规则设定 start--->
            <div class="bullRull" ng-if="createInfo.isShow==9">
                <div class="selectPart">
                    <div class="selectTitle">底分：</div>
                    <div class="selectList" >
                        <div class="selectItem" ng-click="selectChange(1,1)" ng-show="createInfo.fbull.banker_mode!=4">
                            <div class="selectBox" ><img src="<?php echo $image_url;?>files/images/common/tick.png" ng-show="createInfo.fbull.score_type==1"/></div>
                            <div class="selectText">1分</div>
                        </div>

                        <div class="selectItem" ng-click="selectChange(1,6)" ng-show="createInfo.fbull.banker_mode!=4">
                            <div class="selectBox" ><img src="<?php echo $image_url;?>files/images/common/tick.png" ng-show="createInfo.fbull.score_type==6"/></div>
                            <div class="selectText">2分</div>
                        </div>

                        <div class="selectItem" ng-click="selectChange(1,2)" ng-show="createInfo.fbull.banker_mode!=4">
                            <div class="selectBox" ><img src="<?php echo $image_url;?>files/images/common/tick.png" ng-show="createInfo.fbull.score_type==2"/></div>
                            <div class="selectText">3分</div>
                        </div>
                        <div class="selectItem" ng-click="selectChange(1,3)" >
                            <div class="selectBox" ><img src="<?php echo $image_url;?>files/images/common/tick.png" ng-show="createInfo.fbull.score_type==3" /></div>
                            <div class="selectText">5分</div>
                        </div>
                        <div class="selectItem" ng-click="selectChange(1,4)" ng-show="createInfo.fbull.banker_mode==4">
                            <div class="selectBox" ><img src="<?php echo $image_url;?>files/images/common/tick.png" ng-show="createInfo.fbull.score_type==4" /></div>
                            <div class="selectText">10分</div>
                        </div>
                        <div class="selectItem" ng-click="selectChange(1,5)" ng-show="createInfo.fbull.banker_mode==4">
                            <div class="selectBox" ><img src="<?php echo $image_url;?>files/images/common/tick.png" ng-show="createInfo.fbull.score_type==5" /></div>
                            <div class="selectText">20分</div>
                        </div>
                        <div class="selectItem changeWidth" ng-click="selectChange(1,7)">
                            <div class="selectBox" ><img src="<?php echo $image_url;?>files/images/common/tick.png" ng-show="createInfo.fbull.score_type==7"/></div>
                            <div class="fbullfootSelectIcon">
                                    <div class="footSanJiao"></div>
                            </div>
                        </div>
                        <select id="selectList" ng-model="selectValue" ng-change="listChange(selectValue, 'fbull')" 
                        ng-disabled="selectFbull1" ng-init="selectValue=selectFbullValue" ng-options="item for item in selectArr"
                        ng-show="createInfo.fbull.banker1==='selected'">
                        </select>
                        <select id="selectList" ng-model="selectValue" ng-change="listChange(selectValue, 'fbull')" 
                        ng-disabled="selectFbull2" ng-init="selectValue=selectFbullValue" ng-options="item for item in selectArr"
                        ng-show="createInfo.fbull.banker2==='selected'">
                        </select>
                        <select id="selectList" ng-model="selectValue" ng-change="listChange(selectValue, 'fbull')" 
                        ng-disabled="selectFbull3" ng-init="selectValue=selectFbullValue" ng-options="item for item in selectArr"
                        ng-show="createInfo.fbull.banker3==='selected'">
                        </select>
                        <select id="selectList" ng-model="selectValue" ng-change="listChange(selectValue, 'fbull')" 
                        ng-disabled="selectFbull5" ng-init="selectValue=selectFbullValue" ng-options="item for item in selectArr"
                        ng-show="createInfo.fbull.banker5==='selected'">
                        </select>
                        <select id="selectList" ng-model="selectValue" ng-change="listChange(selectValue, 'fbull')" 
                        ng-disabled="selectFbull4" ng-init="selectValue=selectFbullValue" ng-options="item for item in selectArr"
                        ng-show="createInfo.fbull.banker4==='selected'">
                        </select>
                    </div>
                </div>

                <div class="selectPart timeItems" style="height:9vh;">
                    <div class="selectTitle">时间：</div>
                    <div class="selectList" >
                        <label class="selectItem" style="position:relative;">
                            <span style="color:#633201;">准备</span>
                            <select class="fbullSelect" ng-model="createInfo.fbull.countDown[0]" ng-options="x for x in defaultTime5To10">
                            </select>
                            <div class="fbullSelectIcon">
                                    <div class="sanjiao"></div>
                            </div>
                        </label>
                        <label class="selectItem" style="position:relative;">
                            <span style="color:#633201;" >抢庄</span>
                            <select class="fbullSelect" ng-model="createInfo.fbull.countDown[1]" ng-options="x for x in defaultTime5To10">
                            </select>
                            <div class="fbullSelectIcon">
                                    <div class="sanjiao"></div>
                            </div>
                        </label>
                        <label class="selectItem" style="position:relative;">
                            <span style="color:#633201;">下注</span>
                            <select class="fbullSelect" ng-model="createInfo.fbull.countDown[2]" ng-options="x for x in defaultTime5To10">
                            </select>
                            <div class="fbullSelectIcon">
                                    <div class="sanjiao"></div>
                            </div>
                        </label>
                        <label class="selectItem" style="position:relative;">
                            <span style="color:#633201;">摊牌</span>
                            <select class="fbullSelect" ng-model="createInfo.fbull.countDown[3]" ng-options="x for x in defaultTime5To10">
                            </select>
                            <div class="fbullSelectIcon">
                                    <div class="sanjiao"></div>
                            </div>
                        </label>
                    </div>
                </div>

                <div class="selectPart" style="height:10vh;">
                    <div class="selectTitle">规则：</div>
                    <div class="selectList">
                        <div class="selectItem"  ng-click="selectChange(2,1)">
                            <div class="selectBox" ><img src="<?php echo $image_url;?>files/images/common/tick.png" ng-show="createInfo.fbull.rule_type==1" /></div>
                            <div class="selectText" >牛牛x3牛九x2牛八x2</div>
                        </div>
                        <div class="selectItem"  ng-click="selectChange(2,2)">
                            <div class="selectBox" ><img src="<?php echo $image_url;?>files/images/common/tick.png" ng-show="createInfo.fbull.rule_type==2" /></div>
                            <div class="selectText" >牛牛x4牛九x3牛八x2牛七x2</div>
                        </div>
                    </div>
                </div>

                <div class="selectPart" style="height:20vh;">
                    <div class="selectTitle">牌型：</div>
                    <div class="selectList">
                        <div class="selectItem"  ng-click="selectChange(3,9)">
                            <div class="selectBox" ><img src="<?php echo $image_url;?>files/images/common/tick.png" ng-show="createInfo.fbull.is_cardfour==1" /></div>
                            <div class="selectText" >四花牛(4倍)</div>
                        </div>
                        <div class="selectItem"  ng-click="selectChange(3,1)">
                            <div class="selectBox" ><img src="<?php echo $image_url;?>files/images/common/tick.png" ng-show="createInfo.fbull.is_cardfive==1" /></div>
                            <div class="selectText" >五花牛(5倍)</div>
                        </div>
                        <div class="selectItem" ng-click="selectChange(3,2)">
                            <div class="selectBox" ><img src="<?php echo $image_url;?>files/images/common/tick.png" ng-show="createInfo.fbull.is_straight==1" /></div>
                            <div class="selectText" >顺子牛(6倍)</div>
                        </div>
                        <div class="selectItem" ng-click="selectChange(3,3)">
                            <div class="selectBox" ><img src="<?php echo $image_url;?>files/images/common/tick.png" ng-show="createInfo.fbull.is_flush==1" /></div>
                            <div class="selectText" >同花牛(6倍)</div>
                        </div>
                        <div class="selectItem" ng-click="selectChange(3,4)">
                            <div class="selectBox" ><img src="<?php echo $image_url;?>files/images/common/tick.png" ng-show="createInfo.fbull.is_hulu==1" /></div>
                            <div class="selectText" >葫芦牛(6倍)</div>
                        </div>
                        <div class="selectItem" ng-click="selectChange(3,5)">
                            <div class="selectBox" ><img src="<?php echo $image_url;?>files/images/common/tick.png" ng-show="createInfo.fbull.is_cardbomb==1" /></div>
                            <div class="selectText" >炸弹牛(6倍)</div>
                        </div>
                        <div class="selectItem" ng-click="selectChange(3,7)">
                            <div class="selectBox" ><img src="<?php echo $image_url;?>files/images/common/tick.png" ng-show="createInfo.fbull.is_straightflush==1" /></div>
                            <div class="selectText" >同花顺(7倍)</div>
                        </div>
                        <div class="selectItem" ng-click="selectChange(3,6)">
                            <div class="selectBox" ><img src="<?php echo $image_url;?>files/images/common/tick.png" ng-show="createInfo.fbull.is_cardtiny==1" /></div>
                            <div class="selectText" >五小牛(8倍)</div>
                        </div>
                    </div>
                </div>

                <div class="selectPart" style="height:5.5vh;">
                    <div class="selectTitle">局数：</div>
                    <div class="selectList">
                        <div class="selectItem"  ng-click="selectChange(4,1)">
                            <div class="selectBox" ><img src="<?php echo $image_url;?>files/images/common/tick.png" ng-show="createInfo.fbull.ticket_type==1" /></div>
                            <div class="selectText" >12局X2房卡</div>
                        </div>
                        <div class="selectItem"  ng-click="selectChange(4,2)">
                            <div class="selectBox" ><img src="<?php echo $image_url;?>files/images/common/tick.png" ng-show="createInfo.fbull.ticket_type==2" /></div>
                            <div class="selectText" >24局X4房卡</div>
                        </div>
                    </div>
                </div>

                <div class="selectPart" style="height:10vh;">
                    <div class="selectTitle">倍数：</div>
                    <div class="selectList">
                        <div class="selectItem"  ng-click="selectChange(6,1)">
                            <div class="selectBox" ><img src="<?php echo $image_url;?>files/images/common/tick.png" ng-show="createInfo.fbull.times_type==1" /></div>
                            <div class="selectText" >1，2，4，5</div>
                        </div>
                        <div class="selectItem"  ng-click="selectChange(6,2)">
                            <div class="selectBox" ><img src="<?php echo $image_url;?>files/images/common/tick.png" ng-show="createInfo.fbull.times_type==2" /></div>
                            <div class="selectText" >1，3，5，8</div>
                        </div>
                        <div class="selectItem"  ng-click="selectChange(6,3)">
                            <div class="selectBox" ><img src="<?php echo $image_url;?>files/images/common/tick.png" ng-show="createInfo.fbull.times_type==3" /></div>
                            <div class="selectText" >2，4，6，10</div>
                        </div>
                    </div>
                </div>

                <div class="selectPart" style="height:10vh;" ng-if="createInfo.fbull.banker_mode==5">
                    <div class="selectTitle">上庄：</div>
                    <div class="selectList">
                        <div class="selectItem"  ng-click="selectChange(5,1)">
                            <div class="selectBox" ><img src="<?php echo $image_url;?>files/images/common/tick.png" ng-show="createInfo.fbull.banker_score==1" /></div>
                            <div class="selectText" >无</div>
                        </div>
                        <div class="selectItem" ng-click="selectChange(5,2)">
                            <div class="selectBox" ><img src="<?php echo $image_url;?>files/images/common/tick.png" ng-show="createInfo.fbull.banker_score==2" /></div>
                            <div class="selectText" >300</div>
                        </div>
                        <div class="selectItem" ng-click="selectChange(5,3)">
                            <div class="selectBox" ><img src="<?php echo $image_url;?>files/images/common/tick.png" ng-show="createInfo.fbull.banker_score==3" /></div>
                            <div class="selectText" >500</div>
                        </div>
                        <div class="selectItem" ng-click="selectChange(5,4)">
                            <div class="selectBox" ><img src="<?php echo $image_url;?>files/images/common/tick.png" ng-show="createInfo.fbull.banker_score==4" /></div>
                            <div class="selectText" >1000</div>
                        </div>
                    </div>
                </div>
            </div>
            <!---13人牛牛底分等规则设定 end--->

            <div class="bullRull laizi" ng-if="createInfo.isShow==71">
                <div class="selectPart">
                    <div class="selectTitle">底分：</div>
                    <div class="selectList" >
                        <div class="selectItem" ng-click="selectChange(1,1)" ng-show="createInfo.lbull.banker_mode!=4">
                            <div class="selectBox" ><img src="<?php echo $image_url;?>files/images/common/tick.png" ng-show="createInfo.lbull.score_type==1"/></div>
                            <div class="selectText">1分</div>
                        </div>

                        <div class="selectItem" ng-click="selectChange(1,6)" ng-show="createInfo.lbull.banker_mode!=4">
                            <div class="selectBox" ><img src="<?php echo $image_url;?>files/images/common/tick.png" ng-show="createInfo.lbull.score_type==6"/></div>
                            <div class="selectText">2分</div>
                        </div>

                        <div class="selectItem" ng-click="selectChange(1,2)" ng-show="createInfo.lbull.banker_mode!=4">
                            <div class="selectBox" ><img src="<?php echo $image_url;?>files/images/common/tick.png" ng-show="createInfo.lbull.score_type==2"/></div>
                            <div class="selectText">3分</div>
                        </div>
                        <div class="selectItem" ng-click="selectChange(1,3)" >
                            <div class="selectBox" ><img src="<?php echo $image_url;?>files/images/common/tick.png" ng-show="createInfo.lbull.score_type==3" /></div>
                            <div class="selectText">5分</div>
                        </div>
                        <div class="selectItem" ng-click="selectChange(1,4)" ng-show="createInfo.lbull.banker_mode==4">
                            <div class="selectBox" ><img src="<?php echo $image_url;?>files/images/common/tick.png" ng-show="createInfo.lbull.score_type==4" /></div>
                            <div class="selectText">10分</div>
                        </div>
                        <div class="selectItem" ng-click="selectChange(1,5)" ng-show="createInfo.lbull.banker_mode==4">
                            <div class="selectBox" ><img src="<?php echo $image_url;?>files/images/common/tick.png" ng-show="createInfo.lbull.score_type==5" /></div>
                            <div class="selectText">20分</div>
                        </div>
                        <div class="selectItem changeWidth" ng-click="selectChange(1,7)">
                            <div class="selectBox" ><img src="<?php echo $image_url;?>files/images/common/tick.png" ng-show="createInfo.lbull.score_type==7"/></div>
                            <div class="lbullfootSelectIcon">
                                    <div class="footSanJiao"></div>
                            </div>
                        </div>
                        <select id="selectList" ng-model="selectValue" ng-change="listChange(selectValue, 'lbull')" 
                        ng-disabled="selectLbull1" ng-init="selectValue=selectLbullValue" ng-options="item for item in selectArr"
                        ng-show="createInfo.lbull.banker1==='selected'">
                        </select>
                        <select id="selectList" ng-model="selectValue" ng-change="listChange(selectValue, 'lbull')" 
                        ng-disabled="selectLbull2" ng-init="selectValue=selectLbullValue" ng-options="item for item in selectArr"
                        ng-show="createInfo.lbull.banker2==='selected'">
                        </select>
                        <select id="selectList" ng-model="selectValue" ng-change="listChange(selectValue, 'lbull')" 
                        ng-disabled="selectLbull3" ng-init="selectValue=selectLbullValue" ng-options="item for item in selectArr"
                        ng-show="createInfo.lbull.banker3==='selected'">
                        </select>
                        <select id="selectList" ng-model="selectValue" ng-change="listChange(selectValue, 'lbull')" 
                        ng-disabled="selectLbull5" ng-init="selectValue=selectLbullValue" ng-options="item for item in selectArr"
                        ng-show="createInfo.lbull.banker5==='selected'">
                        </select>
                        <select id="selectList" ng-model="selectValue" ng-change="listChange(selectValue, 'lbull')" 
                        ng-disabled="selectLbull4" ng-init="selectValue=selectLbullValue" ng-options="item for item in selectArr"
                        ng-show="createInfo.lbull.banker4==='selected'">
                        </select>
                    </div>
                </div>

                <div class="selectPart timeItems" style="height:9vh;">
                    <div class="selectTitle">时间：</div>
                    <div class="selectList" >
                        <label class="selectItem" style="position:relative;">
                            <span style="color:#633201;">准备</span>
                            <select class="lbullSelect" ng-model="createInfo.lbull.countDown[0]" ng-options="x for x in defaultTime5To10">
                            </select>
                            <div class="lbullSelectIcon">
                                    <div class="sanjiao"></div>
                            </div>
                        </label>
                        <label class="selectItem" style="position:relative;">
                            <span style="color:#633201;">抢庄</span>
                            <select class="lbullSelect" ng-model="createInfo.lbull.countDown[1]" ng-options="x for x in defaultTime5To10">
                            </select>
                            <div class="lbullSelectIcon">
                                    <div class="sanjiao"></div>
                            </div>
                        </label>
                        <label class="selectItem" style="position:relative;">
                            <span style="color:#633201;">下注</span>
                            <select class="lbullSelect" ng-model="createInfo.lbull.countDown[2]" ng-options="x for x in defaultTime5To10">
                            </select>
                            <div class="lbullSelectIcon">
                                    <div class="sanjiao"></div>
                            </div>
                        </label>
                        <label class="selectItem" style="position:relative;">
                            <span style="color:#633201;">摊牌</span>
                            <select class="lbullSelect" ng-model="createInfo.lbull.countDown[3]" ng-options="x for x in defaultTime5To10">
                            </select>
                            <div class="lbullSelectIcon">
                                    <div class="sanjiao"></div>
                            </div>
                        </label>
                    </div>
                </div>

                <div class="selectPart" style="height:10vh;">
                    <div class="selectTitle">规则：</div>
                    <div class="selectList">
                        <div class="selectItem"  ng-click="selectChange(2,1)">
                            <div class="selectBox" ><img src="<?php echo $image_url;?>files/images/common/tick.png" ng-show="createInfo.lbull.rule_type==1" /></div>
                            <div class="selectText" >牛牛x3牛九x2牛八x2</div>
                        </div>
                        <div class="selectItem"  ng-click="selectChange(2,2)">
                            <div class="selectBox" ><img src="<?php echo $image_url;?>files/images/common/tick.png" ng-show="createInfo.lbull.rule_type==2" /></div>
                            <div class="selectText" >牛牛x4牛九x3牛八x2牛七x2</div>
                        </div>
                    </div>
                </div>

                <div class="selectPart" style="height:20vh;">
                    <div class="selectTitle">牌型：</div>
                    <div class="selectList">
                        <div class="selectItem"  ng-click="selectChange(3,9)">
                            <div class="selectBox" ><img src="<?php echo $image_url;?>files/images/common/tick.png" ng-show="createInfo.lbull.is_cardfour==1" /></div>
                            <div class="selectText" >四花牛(4倍)</div>
                        </div>
                        <div class="selectItem"  ng-click="selectChange(3,1)">
                            <div class="selectBox" ><img src="<?php echo $image_url;?>files/images/common/tick.png" ng-show="createInfo.lbull.is_cardfive==1" /></div>
                            <div class="selectText" >五花牛(5倍)</div>
                        </div>
                        <div class="selectItem" ng-click="selectChange(3,2)">
                            <div class="selectBox" ><img src="<?php echo $image_url;?>files/images/common/tick.png" ng-show="createInfo.lbull.is_straight==1" /></div>
                            <div class="selectText" >顺子牛(6倍)</div>
                        </div>
                        <div class="selectItem" ng-click="selectChange(3,3)">
                            <div class="selectBox" ><img src="<?php echo $image_url;?>files/images/common/tick.png" ng-show="createInfo.lbull.is_flush==1" /></div>
                            <div class="selectText" >同花牛(6倍)</div>
                        </div>
                        <div class="selectItem" ng-click="selectChange(3,4)">
                            <div class="selectBox" ><img src="<?php echo $image_url;?>files/images/common/tick.png" ng-show="createInfo.lbull.is_hulu==1" /></div>
                            <div class="selectText" >葫芦牛(6倍)</div>
                        </div>
                        <div class="selectItem" ng-click="selectChange(3,5)">
                            <div class="selectBox" ><img src="<?php echo $image_url;?>files/images/common/tick.png" ng-show="createInfo.lbull.is_cardbomb==1" /></div>
                            <div class="selectText" >炸弹牛(6倍)</div>
                        </div>
                        <div class="selectItem" ng-click="selectChange(3,7)">
                            <div class="selectBox" ><img src="<?php echo $image_url;?>files/images/common/tick.png" ng-show="createInfo.lbull.is_straightflush==1" /></div>
                            <div class="selectText" >同花顺(7倍)</div>
                        </div>
                        <div class="selectItem" ng-click="selectChange(3,6)">
                            <div class="selectBox" ><img src="<?php echo $image_url;?>files/images/common/tick.png" ng-show="createInfo.lbull.is_cardtiny==1" /></div>
                            <div class="selectText" >五小牛(8倍)</div>
                        </div>
                    </div>
                </div>

                <div class="selectPart" style="height:5.5vh;">
                    <div class="selectTitle">局数：</div>
                    <div class="selectList">
                        <div class="selectItem"  ng-click="selectChange(4,1)">
                            <div class="selectBox" ><img src="<?php echo $image_url;?>files/images/common/tick.png" ng-show="createInfo.lbull.ticket_type==1" /></div>
                            <div class="selectText" >12局X2房卡</div>
                        </div>
                        <div class="selectItem"  ng-click="selectChange(4,2)">
                            <div class="selectBox" ><img src="<?php echo $image_url;?>files/images/common/tick.png" ng-show="createInfo.lbull.ticket_type==2" /></div>
                            <div class="selectText" >24局X4房卡</div>
                        </div>
                    </div>
                </div>

                <div class="selectPart" style="height:10vh;">
                    <div class="selectTitle">倍数：</div>
                    <div class="selectList">
                        <div class="selectItem"  ng-click="selectChange(6,1)">
                            <div class="selectBox" ><img src="<?php echo $image_url;?>files/images/common/tick.png" ng-show="createInfo.lbull.times_type==1" /></div>
                            <div class="selectText" >1，2，4，5</div>
                        </div>
                        <div class="selectItem"  ng-click="selectChange(6,2)">
                            <div class="selectBox" ><img src="<?php echo $image_url;?>files/images/common/tick.png" ng-show="createInfo.lbull.times_type==2" /></div>
                            <div class="selectText" >1，3，5，8</div>
                        </div>
                        <div class="selectItem"  ng-click="selectChange(6,3)">
                            <div class="selectBox" ><img src="<?php echo $image_url;?>files/images/common/tick.png" ng-show="createInfo.lbull.times_type==3" /></div>
                            <div class="selectText" >2，4，6，10</div>
                        </div>
                    </div>
                </div>

                <div class="selectPart" style="height:10vh;" ng-if="createInfo.lbull.banker_mode==5">
                    <div class="selectTitle">上庄：</div>
                    <div class="selectList">
                        <div class="selectItem"  ng-click="selectChange(5,1)">
                            <div class="selectBox" ><img src="<?php echo $image_url;?>files/images/common/tick.png" ng-show="createInfo.lbull.banker_score==1" /></div>
                            <div class="selectText" >无</div>
                        </div>
                        <div class="selectItem" ng-click="selectChange(5,2)">
                            <div class="selectBox" ><img src="<?php echo $image_url;?>files/images/common/tick.png" ng-show="createInfo.lbull.banker_score==2" /></div>
                            <div class="selectText" >300</div>
                        </div>
                        <div class="selectItem" ng-click="selectChange(5,3)">
                            <div class="selectBox" ><img src="<?php echo $image_url;?>files/images/common/tick.png" ng-show="createInfo.lbull.banker_score==3" /></div>
                            <div class="selectText" >500</div>
                        </div>
                        <div class="selectItem" ng-click="selectChange(5,4)">
                            <div class="selectBox" ><img src="<?php echo $image_url;?>files/images/common/tick.png" ng-show="createInfo.lbull.banker_score==4" /></div>
                            <div class="selectText" >1000</div>
                        </div>
                    </div>
                </div>
            </div>

            <!--  ten-flower -->
            <div class="flowerRull" ng-if="createInfo.isShow==110">
                <div class="selectPart chipItems" style="height:15vh;">
                    <div class="selectTitle">底分：</div>
                    <div class="selectList" >
                        <div class="selectItem"  ng-click="selectChange(10,2)">
                            <div class="selectBox">
                                <img src="<?php echo $image_url;?>files/images/common/tick.png" ng-show="createInfo.tflower.default_score == 2"/>
                            </div>
                            <div class="selectText" >2</div>
                        </div>
                        <div class="selectItem" ng-click="selectChange(10,4)" >
                            <div class="selectBox" >
                                <img src="<?php echo $image_url;?>files/images/common/tick.png" ng-show="createInfo.tflower.default_score == 4"/>
                            </div>
                            <div class="selectText" >4</div>
                        </div>
                        <div class="selectItem" ng-click="selectChange(10,10)" >
                            <div class="selectBox" >
                                <img src="<?php echo $image_url;?>files/images/common/tick.png" ng-show="createInfo.tflower.default_score == 10"/>
                            </div>
                            <div class="selectText" >10</div>
                        </div>
                        <div class="selectItem"  ng-click="selectChange(10,20)">
                            <div class="selectBox">
                                <img src="<?php echo $image_url;?>files/images/common/tick.png" ng-show="createInfo.tflower.default_score == 20"/>
                            </div>
                            <div class="selectText" >20</div>
                        </div>
                        <div class="selectItem"  ng-click="selectChange(10,40)">
                            <div class="selectBox">
                                <img src="<?php echo $image_url;?>files/images/common/tick.png" ng-show="createInfo.tflower.default_score == 40"/>
                            </div>
                            <div class="selectText" >40</div>
                        </div>
                        <div class="selectItem"  ng-click="selectChange(10,100)">
                            <div class="selectBox">
                                <img src="<?php echo $image_url;?>files/images/common/tick.png" ng-show="createInfo.tflower.default_score == 100"/>
                            </div>
                            <div class="selectText" >100</div>
                        </div>
                        <div class="selectItem"  ng-click="selectChange(10,200)">
                            <div class="selectBox">
                                <img src="<?php echo $image_url;?>files/images/common/tick.png" ng-show="createInfo.tflower.default_score == 200"/>
                            </div>
                            <div class="selectText" >200</div>
                        </div>
                        <div class="selectItem"  ng-click="selectChange(10,0)">
                            <div class="selectBox">
                                <img src="<?php echo $image_url;?>files/images/common/tick.png" ng-show="createInfo.tflower.default_score == 0"/>
                            </div>
                            <div class="selectText" style="position:relative;">
                                <select class="flowerSelect" ng-model='createInfo.tflower.default_score_select' ng-options="x for x in defaultScores">
                                </select>
                                <div class="selectIcon">
                                    <div class="sanjiao"></div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
                <div class="selectPart chipItems" style="height:20vh;">
                    <div class="selectTitle">筹码：</div>
                    <div class="selectList" >
                        <div class="selectItem"  ng-click="selectChange(1,2)">
                            <div class="selectBox">
                                <img src="<?php echo $image_url;?>files/images/common/tick.png" ng-show="createInfo.tflower.chip_type.indexOf(2) !== -1"/>
                            </div>
                            <div class="selectText" >2/4</div>
                        </div>
                        <div class="selectItem" ng-click="selectChange(1,4)" >
                            <div class="selectBox" >
                                <img src="<?php echo $image_url;?>files/images/common/tick.png" ng-show="createInfo.tflower.chip_type.indexOf(4) !== -1"/>
                            </div>
                            <div class="selectText" >4/8</div>
                        </div>
                        <div class="selectItem" ng-click="selectChange(1,5)" >
                            <div class="selectBox" >
                                <img src="<?php echo $image_url;?>files/images/common/tick.png" ng-show="createInfo.tflower.chip_type.indexOf(5) !== -1"/>
                            </div>
                            <div class="selectText" >5/10</div>
                        </div>
                        <div class="selectItem"  ng-click="selectChange(1,8)">
                            <div class="selectBox">
                                <img src="<?php echo $image_url;?>files/images/common/tick.png" ng-show="createInfo.tflower.chip_type.indexOf(8) !== -1"/>
                            </div>
                            <div class="selectText" >8/16</div>
                        </div>
                        <div class="selectItem"  ng-click="selectChange(1,10)">
                            <div class="selectBox">
                                <img src="<?php echo $image_url;?>files/images/common/tick.png" ng-show="createInfo.tflower.chip_type.indexOf(10) !== -1"/>
                            </div>
                            <div class="selectText" >10/20</div>
                        </div>
                        <div class="selectItem"  ng-click="selectChange(1,20)">
                            <div class="selectBox">
                                <img src="<?php echo $image_url;?>files/images/common/tick.png" ng-show="createInfo.tflower.chip_type.indexOf(20) !== -1"/>
                            </div>
                            <div class="selectText" >20/40</div>
                        </div>
                        <div class="selectItem"  ng-click="selectChange(1,40)">
                            <div class="selectBox">
                                <img src="<?php echo $image_url;?>files/images/common/tick.png" ng-show="createInfo.tflower.chip_type.indexOf(40) !== -1"/>
                            </div>
                            <div class="selectText" >40/80</div>
                        </div>
                        <div class="selectItem"  ng-click="selectChange(1,50)">
                            <div class="selectBox">
                                <img src="<?php echo $image_url;?>files/images/common/tick.png" ng-show="createInfo.tflower.chip_type.indexOf(50) !== -1"/>
                            </div>
                            <div class="selectText" >50/100</div>
                        </div>
                        <div class="selectItem"  ng-click="selectChange(1,100)">
                            <div class="selectBox">
                                <img src="<?php echo $image_url;?>files/images/common/tick.png" ng-show="createInfo.tflower.chip_type.indexOf(100) !== -1"/>
                            </div>
                            <div class="selectText" >100/200</div>
                        </div>
                        <div class="selectItem" style="width: 20vh;">
                            <div class="selectText" >请任选四组筹码</div>
                        </div>
                    </div>
                </div>
                <div class="selectPart timeItems" style="height:5.5vh;">
                    <div class="selectTitle">时间：</div>
                    <div class="selectList" >
                        <label class="selectItem" style="position:relative;">
                            <span style="color:#633201;">准备</span>
                            <select class="tflowerReadySelect" ng-model="createInfo.tflower.countDown[0]" ng-options="x for x in defaultTime5To10">
                            </select>
                            <div class="readySelectIcon">
                                    <div class="sanjiao"></div>
                            </div>
                        </label>
                        <label class="selectItem" style="position: relative;">
                            <span style="color:#633201;">下注</span>
                            <select class="tflowerChipSelect" ng-model="createInfo.tflower.countDown[1]" ng-options="x for x in defaultTime5To20">
                            </select>
                            <div class="readySelectIcon">
                                    <div class="sanjiao"></div>
                            </div>
                        </label>
                    </div>
                </div>
                <div class="selectPart" style="height:10vh;">
					<div class="selectTitle">看牌：</div>
					<div class="selectList selectTenRoundList" >
                    <div class="selectItem">
							<div class="selectText seenTenTip">低于积分池将不能看牌</div>
						</div>
                        <div class="selectItem">
							<div class="seenTenProgressReduce" ng-click="seenTenReduce()" ></div>
							<div class="seenTenProgressText" >{{createInfo.tflower.seenProgress}}</div>
                            <div class="seenTenProgress">
                                <input type="range" min="0" max="2000" step="100" class="seenTenRange" ng-model="seenTenProgressValue"
                                ng-change="seenTenProgressChange(seenTenProgressValue)">
                            </div>
                            <div class="seenTenProgressAdd" ng-click="seenTenAdd()"></div>
						</div>
					</div>
				</div>
                <div class="selectPart" style="height:15vh;">
					<div class="selectTitle">比牌：</div>
					<div class="selectList" >
						<div class="selectItem"  ng-click="selectChange(7)">
							<div class="selectBox"><img src="<?php echo $image_url;?>files/images/common/tick.png" ng-show="createInfo.tflower.raceCard==false"/></div>
							<div class="selectText" >首轮禁止比牌</div>
						</div>
                        <div class="selectItem">
							<div class="selectText"  style="color:#54A802;">低于积分池将不能比牌</div>
						</div>
                        <div class="selectItem compareTenProgressItem">
							<div class="compareTenProgressReduce" ng-click="compareTenReduce()"></div>
							<div class="compareTenProgressText">{{createInfo.tflower.compareProgress}}</div>
                            <div class="compareTenProgress">
                                <input type="range" min="0" max="2000" step="100" class="compareTenRange" ng-model="compareTenProgressValue"
                                ng-change="compareTenProgressChange(compareTenProgressValue)">
                            </div>
                            <div class="compareTenProgressAdd" ng-click="compareTenAdd()"></div>
						</div>
					</div>
				</div>

                <div class="selectPart" style="height:5.5vh;">
                    <div class="selectTitle">局数：</div>
                    <div class="selectList">
                        <div class="selectItem" ng-click="selectChange(3,2)">
                            <div class="selectBox" ><img src="<?php echo $image_url;?>files/images/common/tick.png" ng-show="createInfo.tflower.ticket_count==2" /></div>
                            <div class="selectText" >10局X2房卡</div>
                        </div>
                        <div class="selectItem" ng-click="selectChange(3,4)">
                            <div class="selectBox" ><img src="<?php echo $image_url;?>files/images/common/tick.png" ng-show="createInfo.tflower.ticket_count==4" /></div>
                            <div class="selectText">20局X4房卡</div>
                        </div>
                    </div>
                </div>

                <div class="selectPart chipItems">
                    <div class="selectTitle">上限：</div>
                    <div class="selectList">

                        <div class="limitChoose selectItem" style="height: 2.5vh;display: flex; align-items: center; justify-content: space-between">
                            <div class="limitChooseProgressReduce" ng-click="downUpperLimit('t')"></div>
                            <div class="limitChooseProgressText" style="height: auto !important;">{{createInfo.tflower.upper_limit == 0? '无上限': createInfo.tflower.upper_limit}}</div>
                            <div class="limitChooseProgress">
                                <input type="range" min="0" max="2000" step="100"
                                       class="limitChooseRange"
                                       ng-style="{'background-size': (createInfo.tflower.upper_limit)/20+ '% 100%'}"
                                       ng-model="createInfo.tflower.upper_limit" />
                            </div>
                            <div class="limitChooseProgressAdd" ng-click="upUpperLimit('t')"></div>
                        </div>
                    </div>
                </div>
                <div class="selectPart">
                    <div class="selectTitle">喜牌：</div>
                    <div class="selectList">
                        <div class="selectItem">
                            <div class="selectXiPaiText" >豹子，同花顺为喜牌，获得玩家奖励</div>
                        </div>
                        <div class="selectItem selectXiPaiItem" ng-click="selectChange(9,0)">
                            <div class="selectBox" >
                                <img src="<?php echo $image_url;?>files/images/common/tick.png" ng-show="createInfo.tflower.extraRewards==0" />
                            </div>
                            <div class="selectText">0</div>
                        </div>
                        <div class="selectItem selectXiPaiItem" ng-click="selectChange(9,5)">
                            <div class="selectBox" >
                                <img src="<?php echo $image_url;?>files/images/common/tick.png" ng-show="createInfo.tflower.extraRewards==5" />
                            </div>
                            <div class="selectText">5</div>
                        </div>
                        <div class="selectItem selectXiPaiItem" ng-click="selectChange(9,10)">
                            <div class="selectBox" >
                                <img src="<?php echo $image_url;?>files/images/common/tick.png" ng-show="createInfo.tflower.extraRewards==10" />
                            </div>
                            <div class="selectText">10</div>
                        </div>
                        <div class="selectItem selectXiPaiItem" ng-click="selectChange(9,20)">
                            <div class="selectBox" >
                                <img src="<?php echo $image_url;?>files/images/common/tick.png" ng-show="createInfo.tflower.extraRewards==20" />
                            </div>
                            <div class="selectText">20</div>
                        </div>
                        <div class="selectItem selectXiPaiItem" ng-click="selectChange(9,40)">
                            <div class="selectBox" >
                                <img src="<?php echo $image_url;?>files/images/common/tick.png" ng-show="createInfo.tflower.extraRewards==40" />
                            </div>
                            <div class="selectText">40</div>
                        </div>
                    </div>
                </div>
                <div class="selectPart">
                    <div class="selectTitle">特殊：</div>
                    <div class="selectList">
                        <div class="selectItem selectXiPaiItem" ng-click="selectChange(11)">
                            <div class="selectBox" >
                                <img src="<?php echo $image_url;?>files/images/common/tick.png" ng-show="createInfo.tflower.allow235GTPanther==1" />
                            </div>
                            <div class="selectText">235吃豹子</div>
                        </div>
                    </div>
                </div>
            </div>

            <div class="sangongRull" ng-if="createInfo.isShow==36">
                <div class="selectPart">
                    <div class="selectTitle">底分：</div>
                    <div class="selectList" >
                        <div class="selectItem" ng-click="selectChange(1,1)">
                            <div class="selectBox" ><img src="<?php echo $image_url;?>files/images/common/tick.png" ng-show="createInfo.sangong.score_type==1"/></div>
                            <div class="selectText">1分</div>
                        </div>
                        <div class="selectItem" ng-click="selectChange(1,2)">
                            <div class="selectBox" ><img src="<?php echo $image_url;?>files/images/common/tick.png" ng-show="createInfo.sangong.score_type==2"/></div>
                            <div class="selectText">2分</div>
                        </div>
                        <div class="selectItem" ng-click="selectChange(1,3)">
                            <div class="selectBox" ><img src="<?php echo $image_url;?>files/images/common/tick.png" ng-show="createInfo.sangong.score_type==3"/></div>
                            <div class="selectText">3分</div>
                        </div>
                        <div class="selectItem" ng-click="selectChange(1,5)" >
                            <div class="selectBox" ><img src="<?php echo $image_url;?>files/images/common/tick.png" ng-show="createInfo.sangong.score_type==5" /></div>
                            <div class="selectText">5分</div>
                        </div>
                    </div>
                </div>

                <div class="selectPart timeItems" style="height:9vh;">
                    <div class="selectTitle">时间：</div>
                    <div class="selectList" >
                        <label class="selectItem" style="position:relative;">
                            <span style="color:#633201;">准备</span>
                            <select class="sgSelect" ng-model="createInfo.sangong.countDown[0]" ng-options="x for x in defaultTime5To10">
                            </select>
                            <div class="sgSelectIcon">
                                    <div class="sanjiao"></div>
                            </div>
                        </label>
                        <label class="selectItem" style="position:relative;">
                            <span style="color:#633201;">抢庄</span>
                            <select class="sgSelect" ng-model="createInfo.sangong.countDown[1]" ng-options="x for x in defaultTime5To10">
                            </select>
                            <div class="sgSelectIcon">
                                    <div class="sanjiao"></div>
                            </div>
                        </label>
                        <label class="selectItem" style="position:relative;">
                            <span style="color:#633201;">下注</span>
                            <select class="sgSelect" ng-model="createInfo.sangong.countDown[2]" ng-options="x for x in defaultTime5To10">
                            </select>
                            <div class="sgSelectIcon">
                                    <div class="sanjiao"></div>
                            </div>
                        </label>
                        <label class="selectItem" style="position:relative;">
                            <span style="color:#633201;">摊牌</span>
                            <select class="sgSelect" ng-model="createInfo.sangong.countDown[3]" ng-options="x for x in defaultTime5To10">
                            </select>
                            <div class="sgSelectIcon">
                                    <div class="sanjiao"></div>
                            </div>
                        </label>
                    </div>
                </div>

                <div class="selectPart" style="height:9vh;">
                    <div class="selectTitle">规则：</div>
                    <div class="selectList">
                        <div class="selectItem"  ng-click="selectChange(2,1)">
                            <div class="selectBox" ><img src="<?php echo $image_url;?>files/images/common/tick.png" ng-show="createInfo.sangong.is_joker==1" /></div>
                            <div class="selectText" >天公X10-雷公X7-地公X5</div>
                        </div>
                        <div class="selectItem"  ng-click="selectChange(2,2)">
                            <div class="selectBox" ><img src="<?php echo $image_url;?>files/images/common/tick.png" ng-show="createInfo.sangong.is_bj==1" /></div>
                            <div class="selectText" >暴玖X9</div>
                        </div>
                    </div>
                </div>

                <div class="selectPart" style="height:4.5vh;">
                    <div class="selectTitle">局数：</div>
                    <div class="selectList">
                        <div class="selectItem"  ng-click="selectChange(3,1)">
                            <div class="selectBox" ><img src="<?php echo $image_url;?>files/images/common/tick.png" ng-show="createInfo.sangong.ticket_type==1" /></div>
                            <div class="selectText" >10局X1房卡</div>
                        </div>
                        <div class="selectItem"  ng-click="selectChange(3,2)">
                            <div class="selectBox" ><img src="<?php echo $image_url;?>files/images/common/tick.png" ng-show="createInfo.sangong.ticket_type==2" /></div>
                            <div class="selectText" >20局X2房卡</div>
                        </div>
                    </div>
                </div>

            </div>


            <div class="sangongRull" ng-if="createInfo.isShow==37">
                <div class="selectPart" >
                    <div class="selectTitle">底分：</div>
                    <div class="selectList" >
                        <div class="selectItem" ng-click="selectChange(1,1)">
                            <div class="selectBox" ><img src="<?php echo $image_url;?>files/images/common/tick.png" ng-show="createInfo.nsangong.score_type==1"/></div>
                            <div class="selectText">1分</div>
                        </div>
                        <div class="selectItem" ng-click="selectChange(1,2)">
                            <div class="selectBox" ><img src="<?php echo $image_url;?>files/images/common/tick.png" ng-show="createInfo.nsangong.score_type==2"/></div>
                            <div class="selectText">2分</div>
                        </div>
                        <div class="selectItem" ng-click="selectChange(1,3)">
                            <div class="selectBox" ><img src="<?php echo $image_url;?>files/images/common/tick.png" ng-show="createInfo.nsangong.score_type==3"/></div>
                            <div class="selectText">3分</div>
                        </div>
                        <div class="selectItem" ng-click="selectChange(1,5)" >
                            <div class="selectBox" ><img src="<?php echo $image_url;?>files/images/common/tick.png" ng-show="createInfo.nsangong.score_type==5" /></div>
                            <div class="selectText">5分</div>
                        </div>
                    </div>
                </div>

                <div class="selectPart timeItems" style="height:9vh;">
                    <div class="selectTitle">时间：</div>
                    <div class="selectList" >
                        <label class="selectItem" style="position:relative;">
                            <span style="color:#633201;">准备</span>
                            <select class="nsgSelect" ng-model="createInfo.nsangong.countDown[0]" ng-options="x for x in defaultTime5To10">
                            </select>
                            <div class="nsgSelectIcon">
                                    <div class="sanjiao"></div>
                            </div>
                        </label>
                        <label class="selectItem" style="position:relative;">
                            <span style="color:#633201;">抢庄</span>
                            <select class="nsgSelect" ng-model="createInfo.nsangong.countDown[1]" ng-options="x for x in defaultTime5To10">
                            </select>
                            <div class="nsgSelectIcon">
                                    <div class="sanjiao"></div>
                            </div>
                        </label>
                        <label class="selectItem" style="position:relative;">
                            <span style="color:#633201;">下注</span>
                            <select class="nsgSelect" ng-model="createInfo.nsangong.countDown[2]" ng-options="x for x in defaultTime5To10">
                            </select>
                            <div class="nsgSelectIcon">
                                    <div class="sanjiao"></div>
                            </div>
                        </label>
                        <label class="selectItem" style="position:relative;">
                            <span style="color:#633201;">摊牌</span>
                            <select class="nsgSelect" ng-model="createInfo.nsangong.countDown[3]" ng-options="x for x in defaultTime5To10">
                            </select>
                            <div class="nsgSelectIcon">
                                    <div class="sanjiao"></div>
                            </div>
                        </label>
                    </div>
                </div>

                <div class="selectPart" style="height:9vh;">
                    <div class="selectTitle">规则：</div>
                    <div class="selectList">
                        <div class="selectItem"  ng-click="selectChange(2,1)">
                            <div class="selectBox" ><img src="<?php echo $image_url;?>files/images/common/tick.png" ng-show="createInfo.nsangong.is_joker==1" /></div>
                            <div class="selectText" >天公X10-雷公X7-地公X5</div>
                        </div>
                        <div class="selectItem"  ng-click="selectChange(2,2)">
                            <div class="selectBox" ><img src="<?php echo $image_url;?>files/images/common/tick.png" ng-show="createInfo.nsangong.is_bj==1" /></div>
                            <div class="selectText" >暴玖X9</div>
                        </div>
                    </div>
                </div>

                <div class="selectPart" style="height:4.5vh;">
                    <div class="selectTitle">局数：</div>
                    <div class="selectList">
                        <div class="selectItem"  ng-click="selectChange(3,1)">
                            <div class="selectBox" ><img src="<?php echo $image_url;?>files/images/common/tick.png" ng-show="createInfo.nsangong.ticket_type==1" /></div>
                            <div class="selectText" >10局X2房卡</div>
                        </div>
                        <div class="selectItem"  ng-click="selectChange(3,2)">
                            <div class="selectBox" ><img src="<?php echo $image_url;?>files/images/common/tick.png" ng-show="createInfo.nsangong.ticket_type==2" /></div>
                            <div class="selectText" >20局X4房卡</div>
                        </div>
                    </div>
                </div>

            </div>


            <div class="sangongRull" ng-if="createInfo.isShow==38">
                <div class="selectPart" style="height:4.5vh;">
                    <div class="selectTitle">底分：</div>
                    <div class="selectList" >
                        <div class="selectItem" ng-click="selectChange(1,1)">
                            <div class="selectBox" ><img src="<?php echo $image_url;?>files/images/common/tick.png" ng-show="createInfo.tsangong.score_type==1"/></div>
                            <div class="selectText">1分</div>
                        </div>
                        <div class="selectItem" ng-click="selectChange(1,2)">
                            <div class="selectBox" ><img src="<?php echo $image_url;?>files/images/common/tick.png" ng-show="createInfo.tsangong.score_type==2"/></div>
                            <div class="selectText">2分</div>
                        </div>
                        <div class="selectItem" ng-click="selectChange(1,3)">
                            <div class="selectBox" ><img src="<?php echo $image_url;?>files/images/common/tick.png" ng-show="createInfo.tsangong.score_type==3"/></div>
                            <div class="selectText">3分</div>
                        </div>
                        <div class="selectItem" ng-click="selectChange(1,5)" >
                            <div class="selectBox" ><img src="<?php echo $image_url;?>files/images/common/tick.png" ng-show="createInfo.tsangong.score_type==5" /></div>
                            <div class="selectText">5分</div>
                        </div>
                    </div>
                </div>

                <div class="selectPart" style="height:9vh;">
                    <div class="selectTitle">规则：</div>
                    <div class="selectList">
                        <div class="selectItem"  ng-click="selectChange(2,1)">
                            <div class="selectBox" ><img src="<?php echo $image_url;?>files/images/common/tick.png" ng-show="createInfo.tsangong.is_joker==1" /></div>
                            <div class="selectText" >天公X10-雷公X7-地公X5</div>
                        </div>
                        <div class="selectItem"  ng-click="selectChange(2,2)">
                            <div class="selectBox" ><img src="<?php echo $image_url;?>files/images/common/tick.png" ng-show="createInfo.tsangong.is_bj==1" /></div>
                            <div class="selectText" >暴玖X9</div>
                        </div>
                    </div>
                </div>

                <div class="selectPart" style="height:4.5vh;">
                    <div class="selectTitle">局数：</div>
                    <div class="selectList">
                        <div class="selectItem"  ng-click="selectChange(3,1)">
                            <div class="selectBox" ><img src="<?php echo $image_url;?>files/images/common/tick.png" ng-show="createInfo.tsangong.ticket_type==1" /></div>
                            <div class="selectText" >10局X2房卡</div>
                        </div>
                        <div class="selectItem"  ng-click="selectChange(3,2)">
                            <div class="selectBox" ><img src="<?php echo $image_url;?>files/images/common/tick.png" ng-show="createInfo.tsangong.ticket_type==2" /></div>
                            <div class="selectText" >20局X4房卡</div>
                        </div>
                    </div>
                </div>

            </div>
		</div>

		<div class="btnOkCancel" >
            <span class="btnCancel" ng-click="cancelCreate()"></span>
            <span class="btnOk" ng-click="createCommit()"></span>
        </div>
        </div>
	</div>
</div>

<!-- 绑定手机号码 -->
<div id="validePhone" ng-show="isShowBindPhone">
    <div class="phoneMask" style="position: fixed;z-index: 98;top: 0;left: 0;width: 100%;height: 100%;background-color: rgba(0,0,0,0.5);"></div>
    <div class="phoneFrame" style="position: fixed;z-index: 99;width: 80vw;max-width: 80vw; top: 50%; left: 50%;-webkit-transform:translate(-50%,-60%); background-color: #fff; text-align: center; border-radius: 8px; overflow: hidden;opacity: 1; color: white;">
        <div style="height: 2.2vw;"></div>
        <div style="padding: 1vw;font-size: 4vw; line-height: 5vw; word-wrap: break-word;word-break: break-all;color: #000;background-color: white;">防止房卡数据丢失，请绑定手机</div>
        <div style="height: 2.2vw;"></div>
        <div style="position: relative;height: 15vw;word-wrap: break-word;word-break: break-all;color: #000;background-color: white;border-top: solid;border-color: #e6e6e6;border-width: 0px;">
            <input  ng-input="phoneChangeValue()" ng-model="sPhone" type="number" name="phone" placeholder="输入手机号" style="padding:0 12px 0 12px;position: absolute;top:  2.5vw;left: 4vw;width: 48vw;height: 11vw;line-height: 6.5vw;border-style: solid;border-width: 1px;border-radius: 0.5vh;border-color: #e6e6e6;font-size: 4vw;-webkit-appearance: none;">
            <div id="authcode" ng-click="getAuthcode()" style="position: absolute;top:  2.5vw;right: 4vw; width: 22vw;height: 10vw;line-height: 10vw;background-color: rgb(211,211,211);font-size: 3.5vw;border-radius: 0.5vh;color: white;">
                {{authcodeText}}
            </div>
        </div>
        <div style="position: relative;height: 15vw;word-wrap: break-word;word-break: break-all;color: #000;background-color: white;border-top: solid;border-color: #e6e6e6;border-width: 0px;">
            <input ng-model="sAuthcode" type="number" name="phone1" placeholder="输入验证码" style="padding:0 12px 0 12px;position: absolute;top: 1vw;left: 4vw;width: 72vw;height: 11vw;line-height: 6.5vw;border-style: solid;border-width: 1px;border-radius: 0.5vh;border-color: #e6e6e6;font-size: 4vw;-webkit-appearance: none;">

        </div>
        <div style="height: 2.2vw;"></div>
        <div style="position: relative; left: 4vw;width: 72vw;line-height: 10vw; font-size: 4vw;display: flex;border-radius: 2vw;" ng-click="bindPhone()">
            <div style="display: block;-webkit-box-flex:1;flex: 1;text-decoration: none;-webkit-tap-highlight-color:transparent;position: relative;margin-bottom: 0;color: rgb(255,255,255);border-top: solid;border-color: #e6e6e6;border-width: 0px;background-color: rgb(64,112,251);border-radius: 1vw;">立即绑定</div>
        </div>
        <div style="height:4vw;"></div>
    </div>
</div>

<style>
.waiting{position: fixed;width:100%;height:100%;top:0;left:0;z-index: 111;}
.waiting .waitingBack{position: fixed;width:100%;height:100%;top:0;left:0;background: #000;opacity:.5;}
</style>
<div class="waiting" ng-show="is_operation">
	<div class="waitingBack"></div>
	<div class="load4">
		<div class="loader">Loading...</div>
	</div>
</div>
<script>
    <?php if (!empty($create_info)) {
        ; ?>
    localStorage.createInfo = <?php echo $create_info; ?>;
    <?php
    };?>
    var userData = {
        "id":"<?php echo $user['account_id'];?>",
        "name":"<?php echo $user['nickname'];?>",
        "avatar":"<?php echo $user['headimgurl'];?>",
        "card":"<?php echo $card;?>",
        "phone":"<?php echo $user['phone'];?>",
    };
    var socketData = {
        "flower":"<?php echo $socket1;?>",
        "bflower":"<?php echo $socket1;?>",
        "tflower":"<?php echo $socket_tflower;?>",
        "landlord":"<?php echo $socket2;?>",
        "bull":"<?php echo $socket3;?>",
        "bull9":"<?php echo $socket5;?>",
        "majiang":"<?php echo $socket4;?>",
        'tbull' : "<?php echo $socket_tbull;?>",
        'fbull' : "<?php echo $socket_fbull;?>",
        'sangong' : "<?php echo $socket_sangong?>",
        'nsangong' : "<?php echo $socket_nsangong?>",
        'tsangong' : "<?php echo $socket_tsangong?>",
        'lbull' : "<?php echo $socket_lbull?>",
        'vbull6' : "<?php echo $socket_vbull6?>",
        'vbull9' : "<?php echo $socket_vbull9?>",
        'vbull12' : "<?php echo $socket_vbull12?>",
        'vflower6' : "<?php echo $socket_vflower6?>",
        'vflower10' : "<?php echo $socket_vflower10?>"
    };
    var dealerNum = "<?php echo $dealer_num;?>";
    var accountId = "<?php echo $user['account_id'];?>";
    var session   = "<?php echo $session;?>";
    var	baseUrl = "/";
    var currentUrl = "<?php echo $base_url;?>";
</script>
<script type="text/javascript" src="<?php echo $base_url;?>files/js/hall.js?_version=<?php echo $front_version;?>"></script>
<script>
	wx.config({
        debug: false,
        appId: "<?php echo $config_ary['appId'];?>",
        timestamp: "<?php echo $config_ary['timestamp'];?>",
        nonceStr:"<?php echo $config_ary['nonceStr'];?>",
        signature: "<?php echo $config_ary['signature'];?>",
        jsApiList: [
            'onMenuShareTimeline',
            'onMenuShareAppMessage',
            'hideMenuItems'
        ]
    });
	wx.ready(function () {
		wx.hideMenuItems({
			menuList: [
			    "menuItem:copyUrl",
			    "menuItem:share:qq",
			    "menuItem:share:weiboApp",
			    "menuItem:favorite",
			    "menuItem:share:facebook",
			    "menuItem:share:QZone"
			] // 要隐藏的菜单项，只能隐藏“传播类”和“保护类”按钮，所有menu项见附录3
		});
		wx.onMenuShareTimeline({
	        title: "太古大厅",
	        desc: "朋友之间娱乐",
	        link: "<?php echo $room_url;?>",
	        imgUrl: '<?php echo $image_url;?>files/images/hall/share_icon_hall.jpg',
	        success: function (){
		        // 用户确认分享后执行的回调函数
		    },
		    cancel: function (){
		        // 用户取消分享后执行的回调函数
		    }
		});
		wx.onMenuShareAppMessage({
	        title: "太古大厅",
	        desc: "朋友之间娱乐",
	        link: "<?php echo $room_url;?>",
	        imgUrl: '<?php echo $image_url;?>files/images/hall/share_icon_hall.jpg',
	        success: function (){
		        // 用户确认分享后执行的回调函数
		    },
		    cancel: function (){
		        // 用户取消分享后执行的回调函数
		    }
		});
	});
	wx.error(function (res) {
   //     alert("error: " + res.errMsg);
    });

    $('.marquee').marquee({
        duration: 5000,
        delayBeforeStart: 0,
        direction: 'left',
    });
</script>
</body>
</html>
