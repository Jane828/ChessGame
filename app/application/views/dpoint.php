<html>
<head>
<meta charset="utf-8" >
<meta name="viewport" content="width=device-width,initial-scale=1.0, minimum-scale=1.0, maximum-scale=1.0, user-scalable=no"/>
<meta name="format-detection" content="telephone=no" />
<meta name="msapplication-tap-highlight" content="no" />
<title><?php echo $game_title;?>房间<?php echo $room_number;?></title>

<link rel="stylesheet" type="text/css" href="<?php echo $base_url;?>files/css/dpoint.css?_=<?php echo time();?>">
<link rel="stylesheet" type="text/css" href="<?php echo $image_url;?>files/css/alert.css">
<script type="text/javascript" src="<?php echo $image_url;?>files/js/fastclick.js"></script>
<script>

	window.addEventListener('load', function() {
		FastClick.attach(document.body);
	}, false);

	var newNum = "";
	var per = window.innerWidth / 530;
	var globalData = {
		"card":"<?php echo $card;?>",
		"roomNumber":"<?php echo $room_number;?>",
		//	"baseUrl":"<?php echo $base_url;?>",
		"baseUrl":"/",
		"openId":"<?php echo $open_id;?>",
		"socket":"<?php echo $socket;?>",
		"roomUrl":"<?php echo $room_url;?>",
		"dealerNum": "<?php echo $dealer_num;?>",
		"fileUrl": "<?php echo $file_url;?>",
		"imageUrl": "<?php echo $image_url;?>",
		"roomStatus":"<?php echo $room_status;?>",
		"scoreboard":'<?php echo $balance_scoreboard;?>',
		"session":'<?php echo "$session"?>',
		"httpUrl":'<?php echo "$http_url"?>',
		"shareTitle":"<?php echo $game_title;?>",
		"game_type":"<?php echo $game_type;?>"
	};
	var userData = {
		"accountId":"<?php echo $user['account_id'];?>",
		"nickname":"<?php echo $user['nickname'];?>",
		"avatar":"<?php echo $user['headimgurl'];?>",
		"isAuthPhone":"<?php echo $isAuthPhone;?>",
		"authCardCount":"<?php echo $authCardCount;?>",
		"phone":"<?php echo $phone;?>",
	};
	var configData = {
		"appId":"<?php echo $config_ary['appId'];?>",
		"timestamp":"<?php echo $config_ary['timestamp'];?>",
		"nonceStr":"<?php echo $config_ary['nonceStr'];?>",
		"signature":"<?php echo $config_ary['signature'];?>",
	};

</script>
</head>

<body>
	<style>
		body.modal-show {position: fixed;width: 100%;}
		.record{position: fixed;top:0;left:0;height:100%;width:100%; z-index: 150;}
		.record .recordBack{position: fixed;top:0;left:0;height:100%;width:100%;background: #0d0a12;opacity:.7;}
		.record .leftLine{position: fixed;left: 0%;margin-left:20px;height:100%;width: 0;border-left:1px solid #ecb700;}
		.record .mainPart{position: absolute;top:0;left:0;height:100%;width:100%;overflow: auto;}
		.record .mainPart .recordList{position: relative;margin-left: 40px;margin-top: 20px;}
		.record .mainPart .recordList .recordTime{color:#ecb700;font-size: 14px;}
		.record .mainPart .recordList .yellowPoint{position: absolute;width:30px;height:30px;top:10px;left:-35px; }
		.record .mainPart .recordList .yellowPoint .point{background:#ecb700;position: absolute;height:6px;width:6px;border-radius:3px;top:12px;left:12px; }

		.record .mainPart .recordList .recordInfo{background:#cfced0;position: relative;width:80%;border-radius:6px;margin-top:5px;padding:5px 15px; }
		.record .mainPart .recordList .recordInfo .recordItem{height:40px;font-family:simHei; border-top:1px solid #ababab;line-height: 40px;font-size: 16px;}
		.record .mainPart .recordList .recordInfo .recordItem .name{float: left;height: 40px;width:120px;overflow: hidden;margin-left: 15px;}
		.record .mainPart .recordList .recordInfo .recordItem .score{float: right;width:80px;overflow: hidden;right: 15px;}
		.record .mainPart .recordList .recordInfo .borderNone{border: none;}

        /*layerGameScore*/
        .layerGameScore{position: fixed;width: 100%;height:100%;top:0;left:0;z-index: 109;}
        .layerGameScore .gameScoreBack{width: 100%;height:100%;background: #000;opacity:0.6;}
		.layerGameScore .mainPart {width: 90vw;height: 80vh;position: absolute; top:50%; left:50%;transform:translate(-50%, -50%); background:rgba(255,255,255,0.3); border-radius:5px; padding:4px 5px;}
		.layerGameScore .mainPart .showPart{ width:100%; height:calc(100% - 40px); background:#FFF4DC; padding-top:40px; }
		.layerGameScore .mainPart .gameStoreTitle{ width:55vw; height:45px; text-align:center; padding-top:5px; position:absolute; top:0; left:50%; transform:translateX(-50%); border-radius:5px; background-image:url("<?php echo $image_url;?>files/images/common/storetitle.png"); background-size:contain; background-repeat:no-repeat;}
		.layerGameScore .mainPart .gameStoreTitle span{ color:#7D2F00; font-size:6vw;   font-weight:bold; position:relative; z-index:10;}
		.layerGameScore .mainPart .gameStoreTitle span::before{ content:attr(data-text); position:absolute; z-index:-1; -webkit-text-stroke:3px white;left:0; }
		.layerGameScore .mainPart .showPart .storeList{height:calc(100% - 60px); overflow-y:scroll; }
		.layerGameScore .mainPart .showPart .storeList .noData{ color:#A8651F; text-align:center;  margin-top:20vh}
		.layerGameScore .mainPart .showPart .storeHeader{ height:40px;background: linear-gradient(to bottom, #DBB272, #F6DFB3); display:flex; font-size:4.5vw;  border-top:1px solid #d9b571;  border-bottom:1px solid #d9b571;}
		.layerGameScore .mainPart .showPart .storeHeader .common{ display:inline-block; flex:1; text-align:center; line-height:40px; text-shadow: -1px 0 #a8651f, 0 1px #a8651f,1px 0 #a8651f, 0 -1px #a8651f; color:white; font-size:4vw; }
	  	.layerGameScore .mainPart .showPart .storeHeader .cardTitle{flex:2; }   
	    .layerGameScore .mainPart .closeImg{ width:10vw; height:10vw; position: absolute; right:-2.5vw; top:-2.5vh; }
		.layerGameScore .mainPart .showPart .storeList .storeRound .roundNum{ height:30px; border-bottom: 1px solid #D9B572; font-size:15px; line-height:31px; color:#A8651F; padding-left:5px;}
		.layerGameScore .mainPart .showPart .storeList .playerMenu{ height:40px; background:#F9E8C6; padding: 0 5px 0 5px; display: flex;border-bottom:1px solid #D9B572;}
		.layerGameScore .mainPart .showPart .storeList .playerMenu .realName{ text-align:left; flex: 1;  overflow:hidden; text-overflow:ellipsis; white-space:noWrap; line-height:40px; font-size:4vw; color:#714D29;}
		.layerGameScore .mainPart .showPart .storeList .playerMenu .cardType { flex:2;text-align:center;  overflow:hidden; text-overflow:ellipsis; white-space:noWrap; line-height:40px; font-size:4vw; color:#714D29;}
		.layerGameScore .mainPart .showPart .storeList .playerMenu .getStore{text-align:center; flex: 1;  overflow:hidden; text-overflow:ellipsis; white-space:noWrap;line-height:40px; font-size:4vw; color:#714D29;}
		.layerGameScore .mainPart .showPart .storeList .playerMenu .storeChip{text-align:center; flex: 1;  overflow:hidden; text-overflow:ellipsis; white-space:noWrap;line-height:40px; font-size:4vw; color:#714D29;}
		.layerGameScore .mainPart .showPart .storeList .playerMenu .storePrize{text-align:center; flex: 1;  overflow:hidden; text-overflow:ellipsis; white-space:noWrap;line-height:40px; font-size:4vw; color:#714D29;} 	
		.layerGameScore .mainPart .showPart .storeList .playerMenu .cardType .storeCardType{ display:inline-block; vertical-align:middle; font-size:4vw; color:#714D29;}
		.layerGameScore .mainPart .showPart .storeList .playerMenu .cardType span:nth-child(2){ background-image:url("<?php echo $image_url;?>files/images/common/cards.jpg"); background-size:325px 120px; display:inline-block; width:25px;height:30px;  vertical-align:middle; }
		.layerGameScore .mainPart .showPart .storeList .playerMenu .cardType span:nth-child(3){ background-image:url("<?php echo $image_url;?>files/images/common/cards.jpg"); background-size:325px 120px; display:inline-block; width:25px;height:30px;  vertical-align:middle;margin-left:-10px; }
		.layerGameScore .mainPart .showPart .storeList .playerMenu .cardType span:nth-child(4){ background-image:url("<?php echo $image_url;?>files/images/common/cards.jpg"); background-size:325px 120px; display:inline-block; width:25px;height:30px;  vertical-align:middle; margin-left:-10px;}

        .bottomGameScore{position: fixed;bottom:0.75vh;left: 18vh;width: 6vh; height: 6vh;z-index:90;}
        .bottomGameWatch{position: fixed;bottom:5px;right: 50px;width: 30px; height: 30px;z-index:90;}
        .autoBet{position: fixed;bottom:9vh; right: 0.5vh;height: 4vh;z-index:90;width: 12vh;}
		.autoBet img{display: block;float:right;height: 4vh;}
		.autoReady{position: fixed;bottom:2vh; right:12vh; height: 4vh;z-index:90;width: 12vh;}
        .autoReady img{display: block;float:right;height: 4vh;}
        
	</style>

<div style="position: fixed;width:100%;height:100%;top:0;left:0;background: #000;z-index:115;" id="loading">
	<div class="load4">
		<div class="loader">Loading...</div>
	</div>
</div>

<!-- position: relative; width: 100%;margin: 0 auto; background: #fff; display: none; -->
<div class="main" id="app-main" style="display: none;'">

    <?php if ($broadcast) {
    ?>
        <div style="position:fixed;width: 100%;color: white;background: rgba(0,0,0,0.5);font-size: 20px;z-index: 100;padding: 10px">
            <div class='marquee' style="float: left;margin-left:35px;width: 100%;overflow: hidden">
                <?php echo $broadcast; ?>
            </div>
        </div>

        <div style="position: fixed;top: 0vw;z-index: 101;padding: 5px 10px 10px 10px">
            <img style="width: 30px;height: 30px" src="<?php echo $image_url; ?>files/images/common/alert_icon.png" />
        </div>
    <?php
}?>
	<div class="main-bg">
		<img src="<?php echo $image_url;?>files/images/dp/bg.png" alt="背景"/>
	</div>
	<div class="footPoint">
		<div><img src="<?php echo $image_url;?>files/images/common/number.png" alt="局数" class="footImg"><span class="baseScore">{{game.round}}&nbsp/&nbsp{{game.total_num}}&nbsp局</span></div>
	</div>

	<!-- <div class="round">{{game.round}}&nbsp/&nbsp{{game.total_num}}&nbsp局</div> -->
    <div class="audienceLook" @click="showWatch" style="position: fixed;"><img class="lookImg" src="<?php echo $image_url;?>files/images/common/toVisit.png" alt="观战"><span class="lookFont">{{appData.audiences.length}}</span></div>
	<div class="disconnect" v-show="!connectOrNot" style="position: fixed;top:45%;left: 0;width: 100%;text-align: center;z-index: 101">
		<div style="width: 250px;height:27px;position: absolute;top:-2px;left: 50%;margin-left: -125px;background: #000;opacity: .5;border-radius:15px;">
		</div>
		<a style="font-size: 16px;color: #fff;padding: 5px 14px;position:relative;">已断开连接，正在重新连接...</a>
	</div>
	
	<!-- <img class="bottom"  src="<?php echo $image_url;?>files/images/flower/toFoot.png"  usemap="#planetmap" /> -->
	<div class="bottom" style="background:url(<?php echo $image_url;?>files/images/dp/bottom-menu.png);">
		<img class="bottomGameRule" src="<?php echo $image_url;?>files/images/common/toRule.png" @click="showGameRule">
		<img class="bottomGameHistory" src="<?php echo $image_url;?>files/images/common/toSound.png" @click="showAudioSetting">
		<img class="bottomGameScore" src="<?php echo $image_url;?>files/images/common/toScore.png" @click="showGameScore">
		<img class="bottomGameMessage" src="<?php echo $image_url;?>files/images/common/toChat.png" @click="showMessage">
	</div>
	
	<img class="bottomBackIndex" src="<?php echo $image_url;?>files/images/common/toIndex.png" @click="backHome">
	

    <div class="autoReady" v-if="!isAudience">
        <img src="<?php echo $image_url;?>files/images/common/tobg2.png"  @click="autoReady" v-show="!game.autoReady">
        <img src="<?php echo $image_url;?>files/images/common/tobg1.png"  @click="autoReady" v-show="game.autoReady">
    </div>
    <!--返回首页提示-->
    <div class="alert" v-show="isBackHome">
        <div class="alertBack"  @click="closeBack"></div>
        <div class="mainPart" style="height: 28vh;">
            <div class="backImg">
                <div class="blackImg" style="height: 15vh;"></div>
            </div>
            <div class="alertText" >确定返回主页？</div>
            <div>
                <div class="buttonLeft" @click="home"></div>
                <div class="buttonRight" @click="closeBack"></div>
            </div>
        </div>
    </div>

	<!-- 提示  -->
	<div class="alert" id="valert" v-show="isShowAlert">
		<div class="alertBack"  @click="closeAlert"></div>
		<div class="mainPart" style="height:28vh;">
			<div class="backImg">
				<div class="blackImg"></div>
			</div>
			<div class="alertText" >{{alertText}}</div>

			<div v-show="alertType==2">
				<div class="btnCancel" @click="home"></div>
				<div class="buttonWatch" @click="chooseWatch"></div>
			</div>
			<div v-show="alertType==3">
				<div class="buttonLeft" @click="home">返回首页</div>
				<div class="buttonRight" @click="closeAlert">取消</div>
			</div>
			<div v-show="alertType==4">
				<div class="buttonLeft" @click="home">创建房间</div>
				<div class="buttonRight" @click="sitDown">加入游戏</div>
			</div>

			<div v-show="alertType==7">
				<div class="buttonMiddle" @click="home">返回首页</div>
			</div>
			<div v-show="alertType==8">
				<div class="buttonMiddle" @click="closeAlert"></div>
			</div>
			<div v-show="alertType==88">
				<div class="buttonMiddle" @click="home"></div>
			</div>
			<div v-show="alertType==11">
				<div class="buttonMiddle" @click="closeAlert">知道了</div>
			</div>
			<div v-show="alertType==21">
				<div class="buttonMiddle" @click="closeAlert">确定</div>
			</div>
			<div v-show="alertType==22">
				<div class="buttonMiddle" @click="closeAlert">确定</div>
			</div>
			<div v-show="alertType==23">
				<div class="buttonMiddle" @click="finishBindPhone()">确定</div>
			</div>
			<div v-show="alertType==31">
                <div class="buttonMiddle" @click="reloadView()">确定</div>
            </div>
            <div v-show="alertType==32">
					<div class="buttonMiddle" @click="reloadView()">重新登录</div>
			</div>
		</div>
	</div>

	<!---玩家信息start--->
	<div class="user-list" v-for="p in player" :class="'user-list' + p.num">
		<div class="no-user" v-show="p.account_id<=0">
			<img src="<?php echo $image_url;?>files/images/dp/no-user.png" />
		</div>
		<div class="user-avatar" style="display:none;" v-show="p.account_id>0">
			<img class="avatar-img" :src="p.headimgurl" :class="p.is_banker?'avatar-img-banker':''" />
			<img class="banker-img" v-if="p.is_banker" src="<?php echo $image_url;?>files/images/dp/banker.png" />
			<img class="banker-bg" :id="'avatar-banker-'+p.account_id" src="<?php echo $image_url;?>files/images/dp/banker-bg.png"/>
			<img class="banker-bg" :id="'avatar-banker-animate-'+p.num" src="<?php echo $image_url;?>files/images/dp/banker-animate.png"/>
			<img class="banker-bg" :id="'avatar-banker-animate1-'+p.num" src="<?php echo $image_url;?>files/images/dp/banker-animate.png"/>
			<img class="audience-bg" v-if="p.account_status == 13" src="<?php echo $image_url;?>files/images/dp/audience.png"/>
		</div>
		<div class="user-msg" v-show="p.account_id>0">
			<p class="user-name">{{p.nickname}}</p>
			<p class="user-score">{{p.account_score}}</p>
		</div>
		<div class="user-status" v-show="p.account_status==2 || p.account_status==4 || p.account_status==5 || p.account_status>=8">
			<p class="user-type" v-show="p.account_status==2">准备</p>
			<p class="user-type" v-show="p.account_status==4">不抢庄</p>
			<p class="user-type" v-show="p.account_status==5">抢庄</p>
			<div :class="'memberScoreText'+p.num" v-show="game.show_score">
				<p class="score-win" v-show="p.single_score>0&&p.account_status>=8">+{{p.single_score}}</p>
				<p class="score-lose" v-show="p.single_score<0&&p.account_status>=8">{{p.single_score}}</p>
				<p class="score-win" v-show="p.single_score==0&&p.account_status>=8">0</p>
			</div>
			
		</div>
		<div class="user-speak" v-show="p.messageOn" :class="'user-speak' + p.num">
			<div class="speak-content">{{p.messageText}}</div>
			<div class="triangle"></div>
		</div>
	</div>
	<!---玩家信息end--->
	<!-- 玩家金币start -->
	<div id="playerCoins" style="display: none;">
		<div v-for='p in player'>
			<div v-for='coin in p.coins' class="memberCoin" :class="coin">
				<img src="<?php echo $image_url;?>files/images/common/coin.png" style="position: absolute; width: 100%; height: 100%">
			</div>
		</div>
	</div>
	<!-- 玩家金币end -->
	<!-- 玩家筹码显示start -->
	<div id="playerBet"></div>
	<!-- 玩家筹码显示end -->
	<!---玩家操作start--->
	<div class="user-audience" v-if="isAudience">
		<img class="audience-bg" src="<?php echo $image_url;?>files/images/dp/audiencing.png" />
	</div>
	<div class="user-action" v-for="p in player" v-if="p.num==1 && !isAudience">
		<div class="user-clock action-top" v-show="game.time>-1">
			<img src="<?php echo $image_url;?>files/images/dp/clock.png">
			<p>{{game.time}}</p>
		</div>
		<div class="user-ready action-center" v-show="(p.account_status==1||p.account_status==0)&&game.status==1">
			<img src="<?php echo $image_url;?>files/images/dp/ready.png" class="unready" @click="imReady" />
		</div>
		<div class="user-putPrize action-center" v-show="p.account_status==6&&p.is_banker&&showClockPutText">
			<img src="<?php echo $image_url;?>files/images/dp/btn-confirm.png" @click="putPrize" />
		</div>
		<div class="user-bet-banker action-center" v-show="p.account_status==9&&p.is_banker">
			<div class="btn-area"  @click="goShowChips">
				<img src="<?php echo $image_url;?>files/images/dp/btn-yellow.png"/>
				<p>下注统计</p>
			</div>
			
		</div>
		<div class="user-bet-nobanker action-center" v-show="(p.account_status==9 || p.account_status==10) && !p.is_banker">	
			<div class="btn-area"  @click="goShowChips">
				<img src="<?php echo $image_url;?>files/images/dp/btn-yellow.png"/>
				<p v-text="upperLimit?'已达上限':p.haveBet"></p>
			</div>
			<div class="btn-area" v-show="p.account_status==9" @click="stopBet">
				<img src="<?php echo $image_url;?>files/images/dp/stop-bet.png"/>
			</div>
		</div>
		<div class="user-rob action-bottom action-two-img" v-show="p.account_status==3&&(ruleInfo.banker_mode==1 || ruleInfo.banker_mode==2)">
			<img src="<?php echo $image_url;?>files/images/dp/rob-btn.png" @click="robBanker(1)">
			<img src="<?php echo $image_url;?>files/images/dp/rob-no.png" @click="notRobBanker">
		</div>
		<div class="user-lottery action-bottom action-two-img" v-show="p.account_status==11&&p.is_banker&&showClockShowLottery">
			<img src="<?php echo $image_url;?>files/images/dp/open-fast.png" @click="openPrize(3)">
			<img src="<?php echo $image_url;?>files/images/dp/open-slow.png" @click="openPrize(5)">
		</div>
		<div class="user-chip action-bottom action-bottom-chip" v-show="p.account_status==9 && !p.is_banker">
			<img v-for="chipN in ruleInfo.chip_type" class="chip-type" @click="chooseChip($event,chipN)" :src="'<?php echo $image_url;?>files/images/dp/chip-'+chipN+'.png'"/>
		</div>
		<div class="user-ready-tips action-bottom-tips" v-show="(p.account_status==0 || p.account_status==1)&&game.status==1">点击准备开始</div>
		<div class="user-putPrize-tips action-bottom-tips" v-show="p.account_status==6 && !p.is_banker && showClockPutText">等待庄家放宝</div>
		<div class="user-putPrize-tips action-bottom-tips" v-show="p.account_status==6 && p.is_banker && showClockPutText">请选择放宝方向</div>
		<div class="user-lottery-tips action-bottom-tips" v-show="p.account_status==11 && !p.is_banker && showClockShowLottery">等待庄家开奖</div>
		<div class="user-chip-tips action-bottom-tips" v-show="p.account_status==9 && p.is_banker && showBankerCoinText">等待闲家下注</div>
	</div>
	<!---玩家操作end--->

	<!---暗宝棋盘start--->
	<div class="checkerboard-area" id="checkerboard-area">
		<div @click="playBet($event)" class="checkerboard-bet" v-show="player[0].account_status==9 && !player[0].is_banker"></div>
		<img class="checkerboard-bg" src="<?php echo $image_url;?>files/images/dp/checkerboard.png" />
		<img class="open-prize" :class="'open-prize' +prizeArea" v-if="player[0].account_status==12 && showWinWaver" src="<?php echo $image_url;?>files/images/dp/open-prize.png" />
		<div class="checkerboard-put-prize" v-show="player[0].account_status==6 && player[0].is_banker && showClockPutText">
			<img class="put-prize-bg" src="<?php echo $image_url;?>files/images/dp/checkerboard-small.png" />
			<div class="put-prize-area" :class="'prize-area' + prizeArea">
				<div class="prize-in" @click="goPutPrize(0)"></div>
				<div class="prize-serpent" @click="goPutPrize(1)"></div>
				<div class="prize-out" @click="goPutPrize(2)"></div>
				<div class="prize-tiger" @click="goPutPrize(3)"></div>
				<img class="prize-bright" src="<?php echo $image_url;?>files/images/dp/put-prize.png" />
			</div>
		</div>
		<div class="prize-center" :class="'prize-center' + prizeArea" style="background-image:url('<?php echo $image_url;?>files/images/dp/checkerboard-case.png');">
			<img class="prize-prize" src="<?php echo $image_url;?>files/images/dp/prize.png" v-if="(player[0].account_status==6 && player[0].is_banker && showClockPutText) || player[0].account_status==11 || player[0].account_status==12" />
			<img id="prize-prize-hide" class="prize-prize-hide" src="<?php echo $image_url;?>files/images/dp/checkerboard-center.png" v-show="(player[0].account_status==6 && (!player[0].is_banker) || !showClockPutText) || (player[0].account_status!=12 && player[0].account_status!=6)" />
		</div>
	</div>
	<!---暗宝棋盘end--->

	<!-- 消息  	-->
	<div class="message" v-show="isShowMessage" >
        	<div class="messageBack" @click="hideMessage"></div>
        	<div class="textPartOuter"></div>
        	<div id="message-box" class="textPart" :style="'height: ' + 0.39 * height + 'px;'">
        		<div id="scroll-box" class="textList" style="width: 100%;">
        			<div class="textItem" v-for="m in message" @click="messageOn(m.num)">{{m.text}}</div>
        		</div>
        	</div>
        </div>

	<!-- end图片  -->
	<div id="endCreateRoom" class="end" style="position: fixed;width: 100%;height:100%;top:0;left:0;z-index: 120;display: none;overflow: hidden;">
		<img src="" style="width: 100vw;position: absolute;top:0;left: 0;height: 100vh;" id="end"  usemap="#planetmap1" />
		<a href="/f/ym" style="position: absolute;top:10px;display: block;width:34vw;height:11vw;margin-right: 10%;left: 10px" >
			<img src="<?php echo $image_url;?>files/images/common/back.png" style="width:12vw;" />
	    </a>
	</div>

    <!-- 积分数据-->
    <div class="layerGameScore" id="vGameScore" v-show="scoreInfo.isShow">
		<div class="gameScoreBack"></div>
        <div class="mainPart">
            <div class="showPart">
				<div class="storeHeader">
					<span class="common">用户名字</span>
					<span class="common">下注</span>
					<span class="common">开奖</span>
					<span class="common">得分</span>
				</div>
				<div class="storeList">
					<div v-if="appData.storeList.length ===0" class="noData">暂无数据</div>
					<div v-else-if="appData.storeList.length !== 0" class="storeRound" v-for="round in appData.storeList" :key="round['game_num']">
						<div class="roundNum">{{round['game_num']+'/'+round['total_num']}}</div>
						<div class="playerMenu" v-for="player in round.players" :key="player.name">
							<span class="realName common">
								{{player.name}}
							</span>
							<span class="storeChip common">{{player.chip}}</span>
							<span class="storePrize common" v-text="(round.prize==1)?'龙':((round.prize==2)?'出':((round.prize==3)?'虎':'入'))">{{round.prize}}</span>
							<span class="getStore common">{{player.score}}</span>
						</div>
					</div>
				</div>
			</div>
			<div class="gameStoreTitle">
				<span data-text='暗宝战绩'>暗宝战绩</span>
			</div>
			<img src="<?php echo $image_url;?>files/images/common/closeStore.png" alt="关闭" class="closeImg"  @click="cancelGameScore">
        </div>
    </div>
	<!--积分榜start-->
	<div class="ranking hideRanking" id="ranking" style="z-index: 110">
		<div class="rankBack">
			<img   src="<?php echo $image_url;?>files/images/tbull/rank_bg.png" style="position: absolute;top: 0;left: 0;width: 100%">
		</div>

		<div class="rankText" style="position: absolute;top: 4%;">
			<img   src="<?php echo $image_url;?>files/images/dp/ranking.png" style="position: absolute;top: 0%;left: 25vw;width: 150vw; height: 300vw;">
			<div class="time" v-show="playerBoard.round>0" style="position: absolute;top: 48vw;width: 100%;">
				<a style="background-color: rgba(251, 240, 214, 0.6);font-size: 6vw;">房间号:{{game.room_number}}&nbsp&nbsp&nbsp&nbsp{{playerBoard.record}}&nbsp&nbsp&nbsp&nbsp{{game.total_num}}局</a>
			</div>
			<div style="height: 67vw;"></div>
			<div class="scoresHeader">
				<div class="headName">名称</div>
				<div class="headScores">分数</div>
			</div>
			<div v-for="p in playerBoard.score" class="scoresItem" :class="{true: 'scoresItemYellow', false: 'scoresItemWhite'}[p.account_score>0]" v-show="p.account_id>0">
				<img   src="<?php echo $image_url;?>files/images/common/rank_bigwinner.png" style="top: 0; margin-top: -2.5vw; left: 4px;height: 100%" v-show="p.isBigWinner==1">
				<div class="name">{{p.nickname}}</div>
				<div class="currentScores"><a v-show="p.account_score>0">+</a>{{p.account_score}}</div>
			</div>
		</div>
		<!--<div class="button roundEndShow" v-if="roomStatus!=4">-->
		<div class="button roundEndShow" >
			<img src="<?php echo $image_url;?>files/images/common/rank_save.png" style="float: left;margin-left: 15%;" />
			<img src="<?php echo $image_url;?>files/images/common/score_search.png" style="float: right;margin-right: 15%" />
		</div>
	</div>
	<!--积分榜end-->

    <!--观战列表-->
    <div class="watchList" v-show="watchInfo.isShow">
		<div class="watchClose" @click="cancelWatch"></div>
		<div  class="createB"></div>
        <div class="mainPart">
		<div class="createTitle">
                <!--<img src="<?php echo $image_url;?>files/images/common/txt_rule.png" /> -->
               <span >观战列表</span>
            </div>
            <img src="<?php echo $image_url;?>files/images/common/closeStore.png" class="cancelCreate" @click="cancelWatch"/>
            <div class="blueBack">
                <div v-for="audience in audiences">
                    <span>{{audience.nickname}}</span>
                    <img :src="audience.headimgurl" alt="头像">
                </div>
            </div>
            <div class="watchOperate">
                <img v-if="!isAudience" @click="goToWatch" src="<?php echo $image_url;?>files/images/watch/joinWatch.png" />
                <img v-if="isAudience" @click="goJoinGame" src="<?php echo $image_url;?>files/images/watch/joinGame.png" />
            </div>
        </div>
	</div>

	<!--下注列表start-->
    <div class="ShowChipsList" v-show="showChips.isShow">
		<div class="ChipsClose" @click="cancelChips"></div>
		<div  class="createB"></div>
        <div class="mainPart">
            <div class="blueBack">
				<img class="blueBack-bg" src="<?php echo $image_url;?>files/images/common/userInner.png" />
				<div class="chips-area">
					<ul class="chips-list">
						<li class="list-item" v-for="chipsList in chipsLists">
							<div class="list-item-left">{{chipsList.name}}</div>
							<div class="list-item-right">{{chipsList.chip}}</div>
						</li>
					</ul>
				</div>
				
            </div>
        </div>
	</div>
	<!--下注列表end-->

    <!-- 创建房间的时候，弹出创建房间还是加入观战的提示 -->
    <div class="joinOrWatch" v-show="joinChoose.isShow">
		<div class="createRoomBack" @click="cancelChoose"></div>
		<div  class="createB"></div>
        <div class="mainPart" >
            <div class="blueBack">
				<div class="selectPart" style="height:4vh;line-height:4vh;padding:0.8vh 0;">
					<div class="selectTitle">模式：</div>
					<div class="selectList">
						<div class="selectItem" style="margin-left:10px;" v-if="ruleInfo.banker_mode==1">
							<div class="selectText" >自由抢庄</div>
						</div>
						<div class="selectItem" style="margin-left:10px;" v-if="ruleInfo.banker_mode==2">
							<div class="selectText" >固定庄家</div>
						</div>
						<div class="selectItem" style="margin-left:10px;" v-if="ruleInfo.banker_mode==3">
							<div class="selectText" >开房做庄</div>
						</div>
					</div>
				</div>
                <div class="selectPart" style="height:30px;line-height:30px;padding:6px 0; letter-spacing: -1px;">
                    <div class="selectTitle">筹码：</div>
                    <div class="selectList" >
                        <div class="selectItem" style="margin-left:12px;">
                            <div class="selectText" >
                                {{chipType}},
                            </div>
                        </div>
                    </div>
                </div>
               	<div class="selectPart" style="height:30px;line-height:30px;padding:6px 0;">
                    <div class="selectTitle">上限：</div>
                    <div class="selectList">
                        <div class="selectItem" style="margin-left:12px;">
                            <div class="selectText" >{{parseFloat(ruleInfo.upper_limit) || '无上限'}}</div>
                        </div>
                    </div>
                </div>
				<div class="selectPart" style="height:94px;line-height:22px;padding:6px 0;">
                    <div class="selectTitle">赔率：</div>
					<br />
                    <div class="selectList" style="margin:0;">
						<div class="selectItem" style="font-weight: 700;color: #714D29;">
							<p>龙、虎、出、入：</p>
                        </div>
                        <div class="selectItem" style="margin-left:12px;">
                            <div class="selectText" >1:{{ruleInfo.first_lossrate}}</div>
                        </div>
                    </div>
					<div class="selectList" style="margin:0;">
						<div class="selectItem" style="font-weight: 700;color: #714D29;">
							<p>同、粘：</p>
                        </div>
						<div class="selectItem" style="margin-left:12px;">
                            <div class="selectText" >1:{{ruleInfo.second_lossrate}}</div>
                        </div>
                    </div>
					<div class="selectList" style="margin:0;">
						<div class="selectItem" style="font-weight: 700;color: #714D29;">
							<p>角、串：</p>
                        </div>
						<div class="selectItem" style="margin-left:12px;">
                            <div class="selectText" >1:{{ruleInfo.three_lossrate}}</div>
                        </div>
                    </div>
                </div>
                <div class="selectPart" style="height:30px;line-height:30px;padding:6px 0;">
                    <div class="selectTitle">局数：</div>
                    <div class="selectList">
                        <div class="selectItem" style="margin-left:12px;" v-if="ruleInfo.ticket_count==2">
                            <div class="selectText" >12局X2张房卡</div>
                        </div>
                        <div class="selectItem" style="margin-left:12px;" v-if="ruleInfo.ticket_count==4">
                            <div class="selectText" >24局X4张房卡</div>
                        </div>
                    </div>
                </div>
                <div class="selectPart" v-if="alertText !== ''" style="min-height:30px;line-height:30px;padding:6px 0;text-align: center">{{alertText}}</div>
            </div>

            <div class="operate">
                <img class="leftBtn" @click="chooseJoin" src="<?php echo $image_url;?>files/images/watch/joinGame.png" />
                <img class="rightBtn" @click="chooseWatch" src="<?php echo $image_url;?>files/images/watch/joinWatch.png" />
            </div>
        </div>
    </div>

	<!-- 游戏规则-->
	<div class="createRoom" id="vroomRule" v-show="ruleInfo.isShowRule" @click="cancelGameRule">
		<div class="createRoomBack"></div>
		<div  class="createB"></div>
		<div class="mainPart" >
		<div class="createTitle">
                <!--<img src="<?php echo $image_url;?>files/images/common/txt_rule.png" /> -->
               <span >游戏规则</span>
            </div>

            <img src="<?php echo $image_url;?>files/images/common/closeStore.png" class="cancelCreate" @click="cancelGameRule"/>
			<div style="text-align:center; margin-bottom:15px; color:#A04A19;"> 创建房间,游戏未进行,不消耗房卡 </div>
			<div class="blueBack">
				<div class="selectPart" style="height:4vh;line-height:4vh;padding:0.8vh 0;">
					<div class="selectTitle">模式：</div>
					<div class="selectList">
						<div class="selectItem" style="margin-left:10px;" v-if="ruleInfo.banker_mode==1">
							<div class="selectText" >自由抢庄</div>
						</div>
						<div class="selectItem" style="margin-left:10px;" v-if="ruleInfo.banker_mode==2">
							<div class="selectText" >固定庄家</div>
						</div>
						<div class="selectItem" style="margin-left:10px;" v-if="ruleInfo.banker_mode==3">
							<div class="selectText" >开房做庄</div>
						</div>
					</div>
				</div>
				<div class="selectPart" style="height:30px;line-height:30px;padding:6px 0;letter-spacing:-1px;">
					<div class="selectTitle">筹码：</div>
					<div class="selectList" >
						<div class="selectItem" >
							<div class="selectText" >{{chipType}}</div>
						</div>
					</div>
				</div>
				<div class="selectPart" style="height:30px;line-height:30px;padding:6px 0;">
					<div class="selectTitle">上限：</div>
					<div class="selectList">
						<div class="selectItem">
							<div class="selectText" >{{parseFloat(ruleInfo.upper_limit) || '无上限'}}</div>
						</div>
					</div>
				</div>
				<div class="selectPart" style="height:94px;line-height:22px;padding:6px 0;">
                    <div class="selectTitle">赔率：</div>
					<br />
                    <div class="selectList" style="margin:0;">
						<div class="selectItem" style="font-weight: 700;color: #714D29;">
							<p>龙、虎、出、入：</p>
                        </div>
                        <div class="selectItem" style="margin-left:12px;">
                            <div class="selectText" >1:{{ruleInfo.first_lossrate}}</div>
                        </div>
                    </div>
					<div class="selectList" style="margin:0;">
						<div class="selectItem" style="font-weight: 700;color: #714D29;">
							<p>同、粘：</p>
                        </div>
						<div class="selectItem" style="margin-left:12px;">
                            <div class="selectText" >1:{{ruleInfo.second_lossrate}}</div>
                        </div>
                    </div>
					<div class="selectList" style="margin:0;">
						<div class="selectItem" style="font-weight: 700;color: #714D29;">
							<p>角、串：</p>
                        </div>
						<div class="selectItem" style="margin-left:12px;">
                            <div class="selectText" >1:{{ruleInfo.three_lossrate}}</div>
                        </div>
                    </div>
                </div>
				<div class="selectPart" style="height:30px;line-height:30px;padding:6px 0;">
					<div class="selectTitle">局数：</div>
					<div class="selectList">
						<div class="selectItem"  v-if="ruleInfo.ticket_count==2">
							<div class="selectText" >12局X2张房卡</div>
						</div>
						<div class="selectItem" v-if="ruleInfo.ticket_count==4">
							<div class="selectText" >24局X4张房卡</div>
						</div>
					</div>
				</div>
			</div>
		</div>
	</div>

    <!--退出游戏-->
    <div class="backIndex" v-show="backInfo.isShow" @click="cancelBackIndex">
        <div class="cancelBackIndex"></div>
        <div class="mainPart" >
            <div  class="createB"></div>
            <div class="createTitle">
                <img src="<?php echo $image_url;?>files/images/common/cancel.png" class="cancelBack" @click="cancelGameRule"/>
            </div>

            <div class="blueBack">确定返回主页吗？</div>

            <div class="backOperate">
                <img src="<?php echo $image_url;?>files/images/common/btn-confirm.png" />
            </div>
        </div>
    </div>

	<!-- 设置音频 -->
	<div class="audioRoom" id="vaudioRoom" v-show="editAudioInfo.isShow">
		<div class="audioRoomBack" @click="cancelAudioSetting"></div>
		<div  class="createB"></div>
		<div class="mainPart" >
			<div class="createTitle" style="height:4vh;">
			</div>

			<img src="<?php echo $image_url;?>files/images/common/closeStore.png" class="cancelCreate" @click="cancelAudioSetting"/>

			<div class="blueBack">
				<!--<div class="selectPart" style="top: 0px;height:4vh;line-height:4.1vh;">
					<div class="selectTitle" style="width: 100%;font-size: 2vh; text-align: center;color: #7dd9ff; background-color: #143948;opacity: 1.0">点击确定后生效</div>
				</div>	-->
				<div style="height:0.5vh;"></div>

				<div class="selectPart" style="height:4vh;line-height:4vh;padding:0.8vh 0;">
					<div class="selectTitle">背景音乐：</div>
					<div class="selectList" >
						<div class="selectItem" style="margin-left:10px;" @click="setBackMusic" >
							<div class="selectBox"><img src="<?php echo $image_url;?>files/images/common/tick.png" v-show="editAudioInfo.backMusic==1"/></div>
							<div class="selectText">开启</div>
						</div>
					</div>
				</div>

				<div class="selectPart" style="height:4vh;line-height:4vh;padding:0.8vh 0;">
					<div class="selectTitle">游戏音效：</div>
					<div class="selectList" >
						<div class="selectItem" style="margin-left:10px;" @click="setMessageMusic">
							<div class="selectBox"><img src="<?php echo $image_url;?>files/images/common/tick.png" v-show="editAudioInfo.messageMusic==1"/></div>
							<div class="selectText">开启</div>
						</div>
					</div>
				</div>

				<div class="createCommit" @click="confirmAudioSetting" ></div>

			</div>
		</div>
	</div>


    <!-- 绑定手机号码 -->
    <div id="validePhone" style="display: none;" v-show="isAuthPhone==1">
    	<div class="phoneMask" style="position: fixed;z-index: 98;top: 0;left: 0;width: 100%;height: 100%;background-color: rgba(0,0,0,0.5);" ></div>
    	<div class="phoneFrame" style="position: fixed;z-index: 99;width: 80vw;max-width: 80vw; top: 50%; left: 50%;-webkit-transform:translate(-50%,-60%); background-color: #fff; text-align: center; border-radius: 8px; overflow: hidden;opacity: 1; color: white;">
    		<div style="height: 2.2vw;"></div>

    		<div style="padding: 0vh;font-size: 3.5vw; line-height: 8vw; word-wrap: break-word;word-break: break-all;color: #000;background-color: white;" >
    			验证手机号，房卡可找回。
    		</div>
    		<div style="height: 2.2vw;"></div>
    		<div style="position: relative;height: 15vw;word-wrap: break-word;word-break: break-all;color: #000;background-color: white;border-top: solid;border-color: #e6e6e6;border-width: 0px;">
    			<input  @input="phoneChangeValue()" v-model="sPhone" type="number" name="phone" placeholder="输入手机号" style="padding:0 12px 0 12px;position: absolute;top:  2.5vw;left: 4vw;width: 48vw;height: 11vw;line-height: 6.5vw;border-style: solid;border-width: 1px;border-radius: 0.5vh;border-color: #e6e6e6;font-size: 4vw;-webkit-appearance: none;">
    			<div id="authcode" @click="getAuthcode()" style="position: absolute;top:  2.5vw;right: 4vw; width: 22vw;height: 10vw;line-height: 10vw;background-color: rgb(211,211,211);font-size: 3.5vw;border-radius: 0.5vh;color: white;">
    				{{authcodeText}}
    			</div>
    		</div>
    		<div style="position: relative;height: 15vw;word-wrap: break-word;word-break: break-all;color: #000;background-color: white;border-top: solid;border-color: #e6e6e6;border-width: 0px;">
    			<input  v-model="sAuthcode" type="number" name="phone1" placeholder="输入验证码" style="padding:0 12px 0 12px;position: absolute;top: 1vw;left: 4vw;width: 72vw;height: 11vw;line-height: 6.5vw;border-style: solid;border-width: 1px;border-radius: 0.5vh;border-color: #e6e6e6;font-size: 4vw;-webkit-appearance: none;">

    		</div>
    		<div style="height: 2.2vw;"></div>
    		<div style="position: relative; left: 4vw;width: 72vw;line-height: 10vw; font-size: 4vw;display: flex;border-radius: 2vw;" @click="bindPhone()">
    			<div style="display: block;-webkit-box-flex:1;flex: 1;text-decoration: none;-webkit-tap-highlight-color:transparent;position: relative;margin-bottom: 0;color: rgb(255,255,255);border-top: solid;border-color: #e6e6e6;border-width: 0px;background-color: rgb(64,112,251);border-radius: 1vw;">立即绑定</div>
    		</div>
    		<div style="height:4vw;"></div>
    	</div>
    </div>

    <script type="text/javascript" src="<?php echo $image_url;?>files/js/canvas.js" ></script>
</div>

</body>

<script src="https://res.wx.qq.com/open/js/jweixin-1.0.0.js" ></script>
<script type="text/javascript" src="<?php echo $image_url;?>files/js/jquery-1.9.1.min.js"></script>
<script type="text/javascript" src="<?php echo $image_url;?>files/js/bscroll.js"></script>
<script type="text/javascript" src="<?php echo $image_url;?>files/js/velocity.js"></script>
<script type="text/javascript" src="<?php echo $image_url;?>files/js/vue.min.js" ></script>
<script type="text/javascript" src="<?php echo $image_url;?>files/js/vue-resource.min.js" ></script>
<script type="text/javascript" src="<?php echo $base_url;?>files/js/dpoint.js?<?php echo time();?>"></script>

<script type="text/javascript" src="<?php echo $image_url;?>files/js/jquery.marquee.min.js"></script>
<script type="application/javascript">
    $('.marquee').marquee({
        duration: 5000,
        delayBeforeStart: 0,
        direction: 'left',
    });
</script>

</html>
