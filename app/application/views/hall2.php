<html ng-app="app">
<head>
<meta charset="utf-8" >
<meta name="viewport" content="width=device-width,initial-scale=1.0, minimum-scale=1.0, maximum-scale=1.0, user-scalable=no"/>
<meta name="format-detection" content="telephone=no" />
<meta name="msapplication-tap-highlight" content="no" />
<title>太古休闲大厅</title>
<link rel="stylesheet" href="<?php echo $image_url;?>files/css/loading.css">
<link rel="stylesheet" href="<?php echo $base_url;?>files/css/hall.css">
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
.header {position: fixed;left: 0;top: 0;height: 8vh;width:100vw;z-index:100;background:url("<?php echo $image_url;?>files/images/hall/header.png")no-repeat;background-size:100% 100%;}
.header .header-avatar{height:5vh;width:5vh;display:inline-block;background:url("<?php echo $image_url;?>files/images/hall/avatar.png")no-repeat;background-size:100% 100%;padding: 2%;margin-left: 6vw;vertical-align: middle;}
.bottom-menu {height: 10vh;position: fixed;left: 0;bottom: 0;width: 100vw;z-index:100;background:url("<?php echo $image_url;?>files/images/hall/menu.png")no-repeat;background-size:100% 100%;}
.bottom-menu .menu-item span {position: absolute;right: 0;top: 2vh;width: 2px;height: 6vh;background:url("<?php echo $image_url;?>files/images/hall/border.png")no-repeat;background-size:100% 180%;}
.bottom-menu .menu-item-selected{background:url("<?php echo $image_url;?>files/images/hall/active.png")no-repeat;background-size:100% 100%;}
.alert .mainPart .buttonMiddle{position: absolute;width:100%;line-height: 6vh;height: 6vh;font-size: 2.5vh;width: 18vh;left:50%;margin-left:-9vh;bottom:2.2vh;text-align: center;background:url("<?php echo $image_url;?>files/images/common/button2.png");background-size:100%;}
.alert .mainPart .buttonLeft{position: absolute;width:100%;height: 6.2vh;font-size: 2.5vh;width: 18vh;left:3vh;bottom:2.2vh;text-align: center;background:url("<?php echo $image_url;?>files/images/common/button2.png");background-size:cover;}
.alert .mainPart .buttonRight{position: absolute;width:100%;height: 6.2vh;font-size: 2.5vh;width: 18vh;right:3vh;bottom:2.2vh;text-align: center;background:url("<?php echo $image_url;?>files/images/common/button1.png");background-size:cover;}
.alert .mainPart .backImg .blackImg{position: absolute;width:42vh;height:25vh;top:2.2vh;left:1.5vh; background:url("../files/images/common/backImg.png") no-repeat; background-size:contain;}
.alert .mainPart .btnCancel{display: inline-block;width: 100px;height: 40px;background: url("../images/common/cancelRoom.png") no-repeat;background-size: cover;position: absolute;bottom: 0;left: 4vw;}
.alert .mainPart .buttonWatch{display: inline-block;width: 100px;height: 40px;background: url("../images/watch/joinWatch.png") no-repeat;background-size: cover;position: absolute;bottom: 0;right: 4vw;}
.createRoom .mainPart  .createTitle{padding-top:5px; text-align: center; background:url(<?php echo $image_url;?>files/images/common/storetitle.png) no-repeat; background-size:contain; width:50vw; height:40px; position: absolute; top:0; left:50%; transform:translateX(-50%);}
.createRoom .mainPart .showPart .createCommit{ width:20vw;  text-align: center;background:url("<?php echo $image_url;?>files/images/common/createRoom.png");background-size:100%;}
.createRoom .mainPart .showPart .btnOkCancel .btnCancel{ display:inline-block; width:100px; height:40px;  background:url("<?php echo $image_url;?>files/images/common/cancelRoom.png") no-repeat;background-size:cover;  position:absolute; top:50%; transform:translateY(-50%); left:5vw;}
.createRoom .mainPart .showPart .btnOkCancel .btnOk{ display:inline-block; width:100px; height:40px;  background:url("<?php echo $image_url;?>files/images/common/createRoom.png") no-repeat;background-size:cover; position:absolute; top:50%; transform:translateY(-50%); right:5vw;}
.createRoom .mainPart .showPart .blueBack{width: 45vh;background-image:url("<?php echo $image_url;?>files/images/common/innerborder.png");background-size:contain; margin:0 auto;position: relative; margin-top:0.6vh; background-repeat: no-repeat; }
.createRoom .mainPart .blueBack .selectPart .selectList .selectItem .seenProgressReduce{float: left;height:3vh;width:3vh;border-radius:50%; background:url("<?php echo $image_url;?>files/images/common/reduce.png") no-repeat;background-size:contain;}
.createRoom .mainPart .blueBack .selectPart .selectList .selectItem .seenProgressAdd{ float: left;height:3vh;width:3vh;border-radius:50%;background:url("<?php echo $image_url;?>files/images/common/add.png") no-repeat;background-size:contain; }
.createRoom .mainPart .blueBack .selectPart .selectList .selectItem .seenProgress .seenRange::-webkit-slider-thumb{  -webkit-appearance:none; height:4vh; width:4vh; background-image:url(<?php echo $image_url;?>files/images/common/slider.png);background-size:cover; border-radius:50%; margin-top:-0.5vh;}
.createRoom .mainPart .blueBack .selectPart .selectList .selectItem .compareProgressReduce{float: left;height:3vh;width:3vh;border-radius:50%; background:url("<?php echo $image_url;?>files/images/common/reduce.png")no-repeat;background-size:contain;}
.createRoom .mainPart .blueBack .selectPart .selectList .selectItem .compareProgressAdd{ float: left;height:3vh;width:3vh;border-radius:50%;background:url("<?php echo $image_url;?>files/images/common/add.png")no-repeat;background-size:contain; }
.createRoom .mainPart .blueBack .selectPart .selectList .selectItem .compareProgress .compareRange::-webkit-slider-thumb{  -webkit-appearance:none; height:4vh; width:4vh; background-image:url(<?php echo $image_url;?>files/images/common/slider.png);background-size:cover; border-radius:50%; margin-top:-0.5vh;}
.createRoom .mainPart .blueBack .selectPart .selectList .selectItem .limitChooseProgressReduce{float: left;height:3vh;width:3vh;border-radius:50%; background:url("<?php echo $image_url;?>files/images/common/reduce.png")no-repeat;background-size:contain;}
.createRoom .mainPart .blueBack .selectPart .selectList .selectItem .limitChooseProgressAdd{ float: left;height:3vh;width:3vh;border-radius:50%;background:url("<?php echo $image_url;?>files/images/common/add.png")no-repeat;background-size:contain; }
.createRoom .mainPart .blueBack .selectPart .selectList .selectItem .limitChooseRange::-webkit-slider-thumb{  -webkit-appearance:none; height:4vh; width:4vh; background-image:url(<?php echo $image_url;?>files/images/common/slider.png);background-size:cover; border-radius:50%; margin-top:-0.5vh; }

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
            <ul ng-repeat="(key, game) in gameList">
                <li class="list-item" ng-if="createInfo.gameList==game.category" ng-click="createSetting($event, key)">
                    <img src="<?php echo $image_url;?>files/images/hall/list-{{game.title}}.png" class="game">
                </li>
            </ul>
        </div>
    </div>


<div class="createRoom" ng-show="selectGame">
	<div class="createRoomBack"></div>
	<div class="mainPart">
        <div class="createTitle">
            <span data-text="房间设置">房间设置</span>
        </div>
       <div class='showPart'>
                   <div class="selectPartTitle">
				    创建房间,游戏未进行不消耗房卡呦
		</div>

		<!-- 斗牛规则 -->
		<div ng-if="createInfo.gameList=='bulls'">
			<div style="width: 45vh; margin: auto; position: relative; height: 5.4vh;line-height: 2.7vh;" >
				<div ng-class="{true: 'bankerSelected', false: 'bankerUnSelected'}[createInfo[selectGame].banker_mode==3]" style="left: 1.35vh;" ng-click="selectBankerMode(3)">
					<img ng-src="<?php echo $image_url;?>files/images/common/mode_{{createInfo[selectGame].banker3}}.png" class="img" style="border-top-left-radius:4px;" v-if="createInfo[selectGame].banker_mode!=3">
					<p style="position: absolute;top: 0.4vh;left: 0; width: 100%;height: 2.7vh;text-align: center;">牛牛</p>
					<p style="position: absolute;top: 2.7vh;left: 0; width: 100%;height: 2.7vh;text-align: center;">上庄</p>
				</div>
				<div ng-class="{true: 'bankerSelected', false: 'bankerUnSelected'}[createInfo[selectGame].banker_mode==5]" style="left: 9.9vh;" ng-click="selectBankerMode(5)">
					<img ng-src="<?php echo $image_url;?>files/images/common/mode_{{createInfo[selectGame].banker5}}.png" class="img" v-if="createInfo[selectGame].banker_mode!=5">
					<p style="position: absolute;top: 0.4vh;left: 0; width: 100%;height: 2.7vh;text-align: center;">固定</p>
					<p style="position: absolute;top: 2.7vh;left: 0; width: 100%;height: 2.7vh;text-align: center;">庄家</p>
				</div>
				<div ng-class="{true: 'bankerSelected', false: 'bankerUnSelected'}[createInfo[selectGame].banker_mode==1]" style="left: 18.45vh;" ng-click="selectBankerMode(1)">
					<img ng-src="<?php echo $image_url;?>files/images/common/mode_{{createInfo[selectGame].banker1}}.png" class="img" v-if="createInfo[selectGame].banker_mode!=1">
					<p style="position: absolute;top: 0.4vh;left: 0; width: 100%;height: 2.7vh;text-align: center;">自由</p>
					<p style="position: absolute;top: 2.7vh;left: 0; width: 100%;height: 2.7vh;text-align: center;">抢庄</p>
				</div>
				<div ng-class="{true: 'bankerSelected', false: 'bankerUnSelected'}[createInfo[selectGame].banker_mode==2]" style="left: 27vh;" ng-click="selectBankerMode(2)">
					<img ng-src="<?php echo $image_url;?>files/images/common/mode_{{createInfo[selectGame].banker2}}.png" class="img" v-if="createInfo[selectGame].banker_mode!=2">
					<p style="position: absolute;top: 0.4vh;left: 0; width: 100%;height: 2.7vh;text-align: center;">明牌</p>
					<p style="position: absolute;top: 2.7vh;left: 0; width: 100%;height: 2.7vh;text-align: center;">抢庄</p>
				</div>
				<div ng-class="{true: 'bankerSelected', false: 'bankerUnSelected'}[createInfo[selectGame].banker_mode==4]" style="left: 35.55vh;" ng-click="selectBankerMode(4)">
					<img ng-src="<?php echo $image_url;?>files/images/common/mode_{{createInfo[selectGame].banker4}}.png" class="img" style="border-top-right-radius:4px;" v-if="createInfo[selectGame].banker_mode!=4">
					<p style="position: absolute;top: 0.4vh;left: 0; width: 100%;height: 2.7vh;text-align: center;">通比</p>
					<p style="position: absolute;top: 2.7vh;left: 0; width: 100%;height: 2.7vh;text-align: center;">牛牛</p>
				</div>
			</div>

			<div class="blueBack"">
                <div class="selectPart">
                    <div class="selectTitle">底分：</div>
                    <div class="selectList" >
                        <div class="selectItem" ng-click="selectBullChange('score_type',1)" ng-show="createInfo[selectGame].banker_mode!=4">
                            <div class="selectBox" ><img src="<?php echo $image_url;?>files/images/common/tick.png" ng-show="createInfo[selectGame].score_type==1"/></div>
                            <div class="selectText">1分</div>
                        </div>
                        <div class="selectItem" ng-click="selectBullChange('score_type',6)" ng-show="createInfo[selectGame].banker_mode!=4">
                            <div class="selectBox" ><img src="<?php echo $image_url;?>files/images/common/tick.png" ng-show="createInfo[selectGame].score_type==6"/></div>
                            <div class="selectText">2分</div>
                        </div>
                        <div class="selectItem" ng-click="selectBullChange('score_type',2)" ng-show="createInfo[selectGame].banker_mode!=4">
                            <div class="selectBox" ><img src="<?php echo $image_url;?>files/images/common/tick.png" ng-show="createInfo[selectGame].score_type==2"/></div>
                            <div class="selectText">3分</div>
                        </div>
                        <div class="selectItem" ng-click="selectBullChange('score_type',3)" >
                            <div class="selectBox" ><img src="<?php echo $image_url;?>files/images/common/tick.png" ng-show="createInfo[selectGame].score_type==3" /></div>
                            <div class="selectText">5分</div>
                        </div>
                        <div class="selectItem" ng-click="selectBullChange('score_type',4)" ng-show="createInfo[selectGame].banker_mode==4">
                            <div class="selectBox" ><img src="<?php echo $image_url;?>files/images/common/tick.png" ng-show="createInfo[selectGame].score_type==4" /></div>
                            <div class="selectText">10分</div>
                        </div>
                        <div class="selectItem" ng-click="selectBullChange('score_type',5)" ng-show="createInfo[selectGame].banker_mode==4">
                            <div class="selectBox" ><img src="<?php echo $image_url;?>files/images/common/tick.png" ng-show="createInfo[selectGame].score_type==5" /></div>
                            <div class="selectText">20分</div>
                        </div>
                        <div class="selectItem changeWidth" ng-click="selectBullChange('score_type',7)">
                            <div class="selectBox" ><img src="<?php echo $image_url;?>files/images/common/tick.png" ng-show="createInfo[selectGame].score_type==7"/></div>
                            <div class="bullfootSelectIcon">
                                    <div class="footSanJiao"></div>
                            </div>
                        </div>
                        <select id="selectList" ng-model="selectValue" ng-change="selectBullChange('score_value', selectValue)"
                        ng-disabled="selectBull" ng-options="item for item in selectArr">
                        </select>

                    </div>
                </div>

                <div class="selectPart timeItems" style="height:9vh;">
                    <div class="selectTitle">时间：</div>
                    <div class="selectList" >
                        <label class="selectItem">
                            <span style="color:#633201;">准备</span>
                            <select class="bullSelect" ng-model="createInfo[selectGame].countDown[0]" ng-options="x for x in defaultTime5To10">
                            </select>
                            <div class="bullSelectIcon">
                                    <div class="sanjiao"></div>
                            </div>
                        </label>
                        <label class="selectItem">
                            <span>抢庄</span>
                            <select class="bullSelect" ng-model="createInfo[selectGame].countDown[1]" ng-options="x for x in defaultTime5To10">
                            </select>
                            <div class="bullSelectIcon">
                                    <div class="sanjiao"></div>
                            </div>
                        </label>
                        <label class="selectItem">
                            <span>下注</span>
                            <select  class="bullSelect" ng-model="createInfo[selectGame].countDown[2]" ng-options="x for x in defaultTime5To10">
                            </select>
                            <div class="bullSelectIcon">
                                    <div class="sanjiao"></div>
                            </div>
                        </label>
                        <label class="selectItem">
                            <span>摊牌</span>
                            <select class="bullSelect" ng-model="createInfo[selectGame].countDown[3]" ng-options="x for x in defaultTime5To10">
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
						<div class="selectItem"  ng-click="selectBullChange('rule_type',1)">
							<div class="selectBox" ><img src="<?php echo $image_url;?>files/images/common/tick.png" ng-show="createInfo[selectGame].rule_type==1" /></div>
							<div class="selectText" >牛牛×3牛九×2牛八×2</div>
						</div>
						<div class="selectItem"  ng-click="selectBullChange('rule_type',2)">
							<div class="selectBox" ><img src="<?php echo $image_url;?>files/images/common/tick.png" ng-show="createInfo[selectGame].rule_type==2" /></div>
							<div class="selectText" >牛牛×4牛九×3牛八×2牛七×2</div>
						</div>
					</div>
				</div>

				<div class="selectPart" style="height:25vh;">
				<div class="selectTitle">牌型：</div>
					<div class="selectList">
                    <div class="selectItem"  ng-click="selectBullChange('card_type',8)" ng-if='createInfo[selectGame].has_ghost'>
                        <div class="selectBox" ><img src="<?php echo $image_url;?>files/images/common/tick.png" ng-show="createInfo[selectGame].has_ghost==1" /></div>
                        <div class="selectText" >有癞子</div>
                    </div>
                    <div class="selectItem"  ng-click="selectBullChange('card_type',9)">
                        <div class="selectBox" ><img src="<?php echo $image_url;?>files/images/common/tick.png" ng-show="createInfo[selectGame].is_cardfour==1" /></div>
                        <div class="selectText" >四花牛(4倍)</div>
                    </div>
                    <div class="selectItem"  ng-click="selectBullChange('card_type',1)">
                        <div class="selectBox" ><img src="<?php echo $image_url;?>files/images/common/tick.png" ng-show="createInfo[selectGame].is_cardfive==1" /></div>
                        <div class="selectText" >五花牛(5倍)</div>
                    </div>
					<div class="selectItem" ng-click="selectBullChange('card_type',2)">
						<div class="selectBox" ><img src="<?php echo $image_url;?>files/images/common/tick.png" ng-show="createInfo[selectGame].is_straight==1" /></div>
						<div class="selectText" >顺子牛(6倍)</div>
					</div>
					<div class="selectItem" ng-click="selectBullChange('card_type',3)">
						<div class="selectBox" ><img src="<?php echo $image_url;?>files/images/common/tick.png" ng-show="createInfo[selectGame].is_flush==1" /></div>
						<div class="selectText" >同花牛(6倍)</div>
					</div>
					<div class="selectItem" ng-click="selectBullChange('card_type',4)">
						<div class="selectBox" ><img src="<?php echo $image_url;?>files/images/common/tick.png" ng-show="createInfo[selectGame].is_hulu==1" /></div>
						<div class="selectText" >葫芦牛(6倍)</div>
					</div>
					<div class="selectItem" ng-click="selectBullChange('card_type',5)">
						<div class="selectBox" ><img src="<?php echo $image_url;?>files/images/common/tick.png" ng-show="createInfo[selectGame].is_cardbomb==1" /></div>
						<div class="selectText" >炸弹牛(6倍)</div>
					</div>
                    <div class="selectItem" ng-click="selectBullChange('card_type',7)">
						<div class="selectBox" ><img src="<?php echo $image_url;?>files/images/common/tick.png" ng-show="createInfo[selectGame].is_straightflush==1" /></div>
						<div class="selectText" >同花顺(7倍)</div>
					</div>
					<div class="selectItem" ng-click="selectBullChange('card_type',6)">
						<div class="selectBox" ><img src="<?php echo $image_url;?>files/images/common/tick.png" ng-show="createInfo[selectGame].is_cardtiny==1" /></div>
						<div class="selectText" >五小牛(8倍)</div>
					</div>
					</div>
				</div>

				<div class="selectPart" style="height:5.5vh;">
					<div class="selectTitle">局数：</div>
					<div class="selectList">
						<div class="selectItem" ng-if="selectGame=='bull'" ng-click="selectBullChange('ticket_type',1)">
							<div class="selectBox" ><img src="<?php echo $image_url;?>files/images/common/tick.png" ng-show="createInfo[selectGame].ticket_type==1" /></div>
							<div class="selectText" >10局×1房卡</div>
						</div>
						<div class="selectItem" ng-if="selectGame=='bull'" ng-click="selectBullChange('ticket_type',2)">
							<div class="selectBox" ><img src="<?php echo $image_url;?>files/images/common/tick.png" ng-show="createInfo[selectGame].ticket_type==2" /></div>
							<div class="selectText" >20局×2房卡</div>
						</div>
						<div class="selectItem" ng-if="selectGame!='bull'" ng-click="selectBullChange('ticket_type',1)">
							<div class="selectBox" ><img src="<?php echo $image_url;?>files/images/common/tick.png" ng-show="createInfo[selectGame].ticket_type==1" /></div>
							<div class="selectText" >12局×2房卡</div>
						</div>
						<div class="selectItem" ng-if="selectGame!='bull'" ng-click="selectBullChange('ticket_type',2)">
							<div class="selectBox" ><img src="<?php echo $image_url;?>files/images/common/tick.png" ng-show="createInfo[selectGame].ticket_type==2" /></div>
							<div class="selectText" >24局×4房卡</div>
						</div>
					</div>
				</div>

                <div class="selectPart" style="height:10vh;">
                    <div class="selectTitle">倍数：</div>
                    <div class="selectList">
                        <div class="selectItem"  ng-click="selectBullChange('times_type',1)">
                            <div class="selectBox" ><img src="<?php echo $image_url;?>files/images/common/tick.png" ng-show="createInfo[selectGame].times_type==1" /></div>
                            <div class="selectText" >1，2，4，5</div>
                        </div>
                        <div class="selectItem"  ng-click="selectBullChange('times_type',2)">
                            <div class="selectBox" ><img src="<?php echo $image_url;?>files/images/common/tick.png" ng-show="createInfo[selectGame].times_type==2" /></div>
                            <div class="selectText" >1，3，5，8</div>
                        </div>
                        <div class="selectItem"  ng-click="selectBullChange('times_type',3)">
                            <div class="selectBox" ><img src="<?php echo $image_url;?>files/images/common/tick.png" ng-show="createInfo[selectGame].times_type==3" /></div>
                            <div class="selectText" >2，4，6，10</div>
                        </div>
                    </div>
                </div>

				<div class="selectPart" style="height:10vh;" ng-if="createInfo[selectGame].banker_mode==5">
					<div class="selectTitle">上庄：</div>
					<div class="selectList">
						<div class="selectItem"  ng-click="selectBullChange('banker_score',1)">
							<div class="selectBox" ><img src="<?php echo $image_url;?>files/images/common/tick.png" ng-show="createInfo[selectGame].banker_score==1" /></div>
							<div class="selectText" >无</div>
						</div>
						<div class="selectItem" ng-click="selectBullChange('banker_score',2)">
							<div class="selectBox" ><img src="<?php echo $image_url;?>files/images/common/tick.png" ng-show="createInfo[selectGame].banker_score==2" /></div>
							<div class="selectText" >100</div>
						</div>
						<div class="selectItem" ng-click="selectBullChange('banker_score',3)">
							<div class="selectBox" ><img src="<?php echo $image_url;?>files/images/common/tick.png" ng-show="createInfo[selectGame].banker_score==3" /></div>
							<div class="selectText" >300</div>
						</div>
						<div class="selectItem" ng-click="selectBullChange('banker_score',4)">
							<div class="selectBox" ><img src="<?php echo $image_url;?>files/images/common/tick.png" ng-show="createInfo[selectGame].banker_score==4" /></div>
							<div class="selectText" >500</div>
						</div>
					</div>
				</div>
			</div>

			</div>
			
			<!-- 金花规则 -->
			<div ng-if="createInfo.gameList=='flowers'">
			<div class="blueBack">
                <div class="selectPart chipItems" style="height:15vh;">
                    <div class="selectTitle">底分：</div>
                    <div class="selectList" >
                        <div class="selectItem" ng-repeat="score_value in [2, 4, 10, 20, 40, 100, 200]" ng-click="selectFlowerChange('default_score',score_value)">
                            <div class="selectBox">
                                <img src="<?php echo $image_url;?>files/images/common/tick.png" ng-show="createInfo[selectGame].default_score == score_value"/>
                            </div>
                            <div class="selectText" >{{score_value}}</div>
                        </div>

                        <div class="selectItem"  ng-click="selectFlowerChange('default_score',0)">
                            <div class="selectBox">
                                <img src="<?php echo $image_url;?>files/images/common/tick.png" ng-show="createInfo[selectGame].default_score == 0"/>
                            </div>
                            <div class="selectText" style="position: relative;">
                                <select class="flowerSelect" ng-model='createInfo[selectGame].default_score_select' ng-options="x for x in defaultScores">
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
                        <div class="selectItem" ng-repeat="chip_value in [2, 4, 5, 8, 10, 20, 40, 50, 100]"  ng-click="selectFlowerChange('chip_type',chip_value)">
                            <div class="selectBox">
                                <img src="<?php echo $image_url;?>files/images/common/tick.png" ng-show="createInfo[selectGame].chip_type.indexOf(chip_value) !== -1"/>
                            </div>
                            <div class="selectText" >{{chip_value}}/{{chip_value*2}}</div>
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
                            <select class="flowerReadySelect" ng-model="createInfo[selectGame].countDown[0]" ng-options="x for x in defaultTime5To10">
                            </select>
                            <div class="readySelectIcon">
                                    <div class="sanjiao"></div>
                            </div>
                        </label>
                        <label class="selectItem" style="position: relative;">
                            <span style="color:#633201;">下注</span>
                            <select class="flowerChipSelect" ng-model="createInfo[selectGame].countDown[1]" ng-options="x for x in defaultTime5To20">
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
							<div class="seenProgressText" >{{createInfo[selectGame].seenProgress}}</div>
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
						<div class="selectItem"  ng-click="selectFlowerChange('raceCard')">
							<div class="selectBox"><img src="<?php echo $image_url;?>files/images/common/tick.png" ng-show="createInfo[selectGame].raceCard==false"/></div>
							<div class="selectText" >首轮禁止比牌</div>
						</div>
                        <div class="selectItem">
							<div class="selectText"  style="color:#54A802;">低于积分池将不能比牌</div>
						</div>
                        <div class="selectItem compareProgressItem">
							<div class="compareProgressReduce" ng-click="compareReduce()"></div>
							<div class="compareProgressText">{{createInfo[selectGame].compareProgress}}</div>
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
						<div class="selectItem" ng-if= "selectGame!='tflower'" ng-click="selectFlowerChange('ticket_count',1)">
							<div class="selectBox" ><img src="<?php echo $image_url;?>files/images/common/tick.png" ng-show="createInfo[selectGame].ticket_count==1" /></div>
							<div class="selectText" >10局X1房卡</div>
						</div>
						<div class="selectItem" ng-if= "selectGame!='tflower'" ng-click="selectFlowerChange('ticket_count',2)">
							<div class="selectBox" ><img src="<?php echo $image_url;?>files/images/common/tick.png" ng-show="createInfo[selectGame].ticket_count==2" /></div>
							<div class="selectText">20局X2房卡</div>
						</div>
						<div class="selectItem" ng-if= "selectGame=='tflower'" ng-click="selectFlowerChange('ticket_count',2)">
							<div class="selectBox" ><img src="<?php echo $image_url;?>files/images/common/tick.png" ng-show="createInfo[selectGame].ticket_count==2" /></div>
							<div class="selectText" >10局X2房卡</div>
						</div>
						<div class="selectItem" ng-if= "selectGame=='tflower'" ng-click="selectFlowerChange('ticket_count',4)">
							<div class="selectBox" ><img src="<?php echo $image_url;?>files/images/common/tick.png" ng-show="createInfo[selectGame].ticket_count==4" /></div>
							<div class="selectText">20局X4房卡</div>
						</div>
					</div>
				</div>

				<div class="selectPart">
					<div class="selectTitle">上限：</div>
					<div class="selectList">
						<div class="limitChoose selectItem" style="height: 2.5vh;display: flex; align-items: center; justify-content: space-between">
						<div class="limitChooseProgressReduce" ng-click="selectFlowerChange('upper_limit', 'down')"></div>
						<div class="limitChooseProgressText" style="height: auto !important;">{{createInfo[selectGame].upper_limit == 0? '无上限': createInfo[selectGame].upper_limit}}</div>
						<div class="limitChooseProgress">
							<input type="range" min="0" max="2000" step="100"
							class="limitChooseRange"
							ng-style="{'background-size': (createInfo[selectGame].upper_limit)/20+ '% 100%'}"
							ng-model="createInfo[selectGame].upper_limit" />
						</div>
						<div class="limitChooseProgressAdd" ng-click="selectFlowerChange('upper_limit', 'up')"></div>
					</div>
				</div>
		</div>
                <div class="selectPart">
                    <div class="selectTitle">喜牌：</div>
                    <div class="selectList">
                        <div class="selectItem">
                            <div class="selectXiPaiText" >豹子，同花顺为喜牌，获得玩家奖励</div>
                        </div>
                        <div class="selectItem selectXiPaiItem" ng-repeat="rewardValue in [0, 5, 10, 20, 40]" ng-click="selectFlowerChange('extraRewards',rewardValue)">
                            <div class="selectBox" >
                                <img src="<?php echo $image_url;?>files/images/common/tick.png" ng-show="createInfo[selectGame].extraRewards==rewardValue" />
                            </div>
                            <div class="selectText">{{rewardValue}}</div>
                        </div>

                    </div>
                </div>
                <div class="selectPart" ng-if="selectGame!='bflower'">
                    <div class="selectTitle">特殊：</div>
                    <div class="selectList">
                        <div class="selectItem selectXiPaiItem" ng-click="selectFlowerChange('allow235GTPanther')">
                            <div class="selectBox" >
                                <img src="<?php echo $image_url;?>files/images/common/tick.png" ng-show="createInfo[selectGame].allow235GTPanther==1" />
                            </div>
                            <div class="selectText">235吃豹子</div>
                        </div>
                    </div>
                </div>
            </div>
			
			</div>
			
			<!-- 三公规则-->
			<div ng-if="createInfo.gameList=='sgs'">
				<div style="width: 45vh; margin: auto; position: relative; height: 5.4vh;line-height: 2.7vh;">
			            <div ng-class="{true: 'bankerSelected', false: 'bankerUnSelected'}[createInfo[selectGame].banker_mode==1]" style="left: 1.35vh;" ng-click="selectBankerSangong(1)">
			                <img ng-src="<?php echo $image_url;?>files/images/common/mode_{{createInfo[selectGame].banker1}}.png" class="img" v-if="createInfo[selectGame].banker_mode!=1">
			                <p style="position: absolute;top: 0.4vh;left: 0; width: 100%;height: 2.7vh;text-align: center;">自由</p>
			                <p style="position: absolute;top: 2.7vh;left: 0; width: 100%;height: 2.7vh;text-align: center;">抢庄</p>
			            </div>
			            <div ng-class="{true: 'bankerSelected', false: 'bankerUnSelected'}[createInfo[selectGame].banker_mode==2]" style="left: 9.9vh;" ng-click="selectBankerSangong(2)">
			                <img ng-src="<?php echo $image_url;?>files/images/common/mode_{{createInfo[selectGame].banker2}}.png" class="img" v-if="createInfo[selectGame].banker_mode!=2">
			                <p style="position: absolute;top: 0.4vh;left: 0; width: 100%;height: 2.7vh;text-align: center;">明牌</p>
			                <p style="position: absolute;top: 2.7vh;left: 0; width: 100%;height: 2.7vh;text-align: center;">抢庄</p>
			            </div>
				</div>
			    
			    <div class="blueBack">
		                <div class="selectPart">
		                    <div class="selectTitle">底分：</div>
		                    <div class="selectList" >
		                        <div class="selectItem" ng-repeat="scoreValue in [1, 2, 3, 5]" ng-click="selectSangongChange('score_type', scoreValue)">
		                            <div class="selectBox" ><img src="<?php echo $image_url;?>files/images/common/tick.png" ng-show="createInfo[selectGame].score_type==scoreValue"/></div>
		                            <div class="selectText">{{scoreValue}}分</div>
		                        </div>
		                    </div>
		                </div>

		                <div class="selectPart timeItems" style="height:9vh;">
		                    <div class="selectTitle">时间：</div>
		                    <div class="selectList" >
		                        <label class="selectItem" style="position:relative;">
		                            <span style="color:#633201;">准备</span>
		                            <select class="sgSelect" ng-model="createInfo[selectGame].countDown[0]" ng-options="x for x in defaultTime5To10">
		                            </select>
		                            <div class="sgSelectIcon">
		                                    <div class="sanjiao"></div>
		                            </div>
		                        </label>
		                        <label class="selectItem" style="position:relative;">
		                            <span style="color:#633201;">抢庄</span>
		                            <select class="sgSelect" ng-model="createInfo[selectGame].countDown[1]" ng-options="x for x in defaultTime5To10">
		                            </select>
		                            <div class="sgSelectIcon">
		                                    <div class="sanjiao"></div>
		                            </div>
		                        </label>
		                        <label class="selectItem" style="position:relative;">
		                            <span style="color:#633201;">下注</span>
		                            <select class="sgSelect" ng-model="createInfo[selectGame].countDown[2]" ng-options="x for x in defaultTime5To10">
		                            </select>
		                            <div class="sgSelectIcon">
		                                    <div class="sanjiao"></div>
		                            </div>
		                        </label>
		                        <label class="selectItem" style="position:relative;">
		                            <span style="color:#633201;">摊牌</span>
		                            <select class="sgSelect" ng-model="createInfo[selectGame].countDown[3]" ng-options="x for x in defaultTime5To10">
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
		                        <div class="selectItem"  ng-click="selectSangongChange('is_joker')">
		                            <div class="selectBox" ><img src="<?php echo $image_url;?>files/images/common/tick.png" ng-show="createInfo[selectGame].is_joker==1" /></div>
		                            <div class="selectText" >天公X10-雷公X7-地公X5</div>
		                        </div>
		                        <div class="selectItem"  ng-click="selectSangongChange('is_bj')">
		                            <div class="selectBox" ><img src="<?php echo $image_url;?>files/images/common/tick.png" ng-show="createInfo[selectGame].is_bj==1" /></div>
		                            <div class="selectText" >暴玖X9</div>
		                        </div>
		                    </div>
		                </div>

		                <div class="selectPart" style="height:4.5vh;">
		                    <div class="selectTitle">局数：</div>
		                    <div class="selectList" >
		                        <div class="selectItem" ng-if= "selectGame=='sangong'" ng-click="selectSangongChange('ticket_type', 1)">
		                            <div class="selectBox" ><img src="<?php echo $image_url;?>files/images/common/tick.png" ng-show="createInfo[selectGame].ticket_type==1" /></div>
		                            <div class="selectText" >10局X1房卡</div>
		                        </div>
		                        <div class="selectItem" ng-if= "selectGame=='sangong'" ng-click="selectSangongChange('ticket_type', 2)">
		                            <div class="selectBox" ><img src="<?php echo $image_url;?>files/images/common/tick.png" ng-show="createInfo[selectGame].ticket_type==2" /></div>
		                            <div class="selectText" >20局X2房卡</div>
		                        </div>
		                        <div class="selectItem" ng-if= "selectGame=='nsangong'" ng-click="selectSangongChange('ticket_type', 1)">
		                            <div class="selectBox" ><img src="<?php echo $image_url;?>files/images/common/tick.png" ng-show="createInfo[selectGame].ticket_type==1" /></div>
		                            <div class="selectText" >10局X2房卡</div>
		                        </div>
		                        <div class="selectItem" ng-if= "selectGame=='nsangong'" ng-click="selectSangongChange('ticket_type', 2)">
		                            <div class="selectBox" ><img src="<?php echo $image_url;?>files/images/common/tick.png" ng-show="createInfo[selectGame].ticket_type==2" /></div>
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
<script type="text/javascript" src="<?php echo $base_url;?>files/js/hall2.js?_version=1111<?php echo $front_version;?>"></script>
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
    function initialHeight() {
        var clientHeight =  document.documentElement.clientHeight;
        // alert(clientHeight);
        $(".bottom-menu").css('height',(clientHeight*0.1)+'px');
        $(".bottom-menu .menu-item").css({'height':(clientHeight*0.1)+'px','padding-top':(clientHeight*0.01)+'px'});
        $(".bottom-menu .menu-item img").css('height',(clientHeight*0.05)+'px');
        $(".bottom-menu .menu-item span").css({'height':(clientHeight*0.06)+'px','top':(clientHeight*0.02)+'px'});
        $("#bodyBackground,body").css("min-height",clientHeight+'px');
    }
    initialHeight();
</script>
</body>
</html>
