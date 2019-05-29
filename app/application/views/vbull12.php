<html>
<head>
<meta charset="utf-8" >
<meta name="viewport" content="width=device-width,initial-scale=1.0, minimum-scale=1.0, maximum-scale=1.0, user-scalable=no"/>
<meta name="format-detection" content="telephone=no" />
<meta name="msapplication-tap-highlight" content="no" />
<title>VIP房间<?php echo $room_number;?></title>

<link rel="stylesheet" type="text/css" href="<?php echo $base_url;?>files/css/vbull12.css">
<link rel="stylesheet" type="text/css" href="<?php echo $image_url;?>files/css/alert.css">
<script type="text/javascript" src="<?php echo $image_url;?>files/js/fastclick.js"></script>


<script type="text/javascript">

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
		"shareTitle":"VIP十二人斗牌",
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

<body >

	<style>
		body.modal-show {position: fixed;width: 100%;}
		.cards3D .cards{overflow:hidden;}
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

		.erweima{position: fixed;width: 100%;height:100%;top:0;left:0;z-index: 109;}
		.erweima .inviteBack{width: 100%;height:100%;background: #000;opacity:0.8;position: absolute;}
		.erweima .inviteText{margin: 0 auto;margin-top:30%;width:300px;line-height: 40px;font-size: 18px;color: #fff;font-family: simHei;position: relative;}
		.erweima .inviteText .invite1{width: 60px;position: absolute; top:240px;left: 140px;}

        /*layerGameScore*/
        .layerGameScore{position: fixed;width: 100%;height:100%;top:0;left:0;z-index: 109;}
        .layerGameScore .gameScoreBack{width: 100%;height:100%;background: #000;opacity:0.6;}
        .layerGameScore .mainPart {width: 230px;height: 305px;top: 44%;left: 58%;margin-top: -165px;margin-left: -150px;position: absolute;}
        .layerGameScore .mainPart .createB{width: 100%;height:100%;top:0%;left:0%;position: absolute;background:#634fa6;border:1px solid #a684f2;border-radius:10px; }
        .layerGameScore .mainPart .createTitle{position:relative;height:36px;text-align: center;}
        .layerGameScore .mainPart .createTitle img{position:relative;height:20px;margin-top: 8px;}
        .layerGameScore .mainPart .cancelCreate{width: 36px;height:36px;top:-16px;right:-16px;position: absolute;}
        .layerGameScore .mainPart .blueBack {width: 200px;height: 235px;background: #111431;border: 1px solid #a684f2;border-radius: 4px;margin: 0 auto;position: relative;}
        .layerGameScore .mainPart .blueBack .selectPart{width:100%;margin-top:4px;line-height:36px;font-size:14px;position: relative;color:#111431;background:#bbbff1;border-radius:0px;font-family:simHei; }
        .layerGameScore .mainPart .blueBack .selectPart .selectTitle{width: 100% !important; text-indent: 15px;text-align: left;}

        .bottomGameScore{position: fixed;bottom:5;right: 22.5vh;width: 30px; height: 30px;z-index:90;}
        /*layerGameScore over*/
	</style>

	<div style="position: fixed;width:100%;height:100%;top:0;left:0;background: #000;z-index:115;" id="loading">
		<div class="load4">
			<div class="loader">Loading...</div>
		</div>
	</div>

	<div class="main" id="app-main" style="position: relative; width: 100%;margin: 0 auto; background: #fff; display: none;">

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

	    <!-- 顶部房卡数，首页，加人等按钮 -->
		<div class="roomCard">
			<img  src="<?php echo $image_url;?>files/images/common/ticket.png" />
			<div class="num">
				<div style="position: absolute;top:0;left: 0;width: 100%;height: 100%;background: #000;opacity: .6;border-radius:1.5vh;"></div>
				<div style="position: relative;padding: 0 1.5vh 0 5vh;">{{roomCard}}张</div>
			</div>
		</div>

		<img class="tabBottom" src="<?php echo $image_url;?>files/images/nbull/bottom.png" alt="">
		<div class="round" style="position: fixed;">{{game.round}}&nbsp/&nbsp{{game.total_num}}&nbsp局</div>
		<img class="return" src="<?php echo $image_url;?>files/images/common/icon_rule.png" v-on:click="showGameRule" />
        <img class="bottomGameScore" src="<?php echo $base_url;?>files/images/common/icon_score.png" v-on:click="showGameScore" style="z-index: 110">

        <div class="disconnect" v-show="!connectOrNot" style="position: fixed;top:45%;left: 0;width: 100%;text-align: center;z-index: 101"><div style="width: 250px;height:27px;position: absolute;top:-2;left: 50%;margin-left: -125px;background: #000;opacity: .5;border-radius:15px;"></div><a style="font-size: 16px;color: #fff;padding: 5px 14px;position:relative;">已断开连接，正在重新连接...</a></div>

        <!-- 底部显示底注 -->
        <div class="myCardTypeBG">
        </div>
		<div class="myCardType">
		    <div>底分：{{base_score}}分</div>
		</div>

        <div class="bottomMessage" v-on:click="showMessage">
        	<img src="<?php echo $image_url;?>files/images/common/icon_message.png" style="position: absolute; width: 100%; height: 100%">
        </div>
        <div class="bottomHistory" style="right: 15.5vh;" v-on:click="showAudioSetting">
        	<img src="<?php echo $image_url;?>files/images/common/icon_sound.png" style="position: absolute; width: 100%; height: 100%">
        </div>

        <!-- 积分榜，消息按钮 -->
		<map name="planetmap" id="planetmap">
			<area shape="rect" v-bind:coords="'0,0,' + width/3.2 + ',' + width/8"  class="showRanking" />
			<area shape="rect" v-bind:coords="width/3*2 + ',0,' + width + ',' + width/8" v-on:click="showMessage" />
		</map>

        <!-- 提示框 -->
		<div class="alert" id="valert" v-show="isShowAlert">
			<div class="alertBack"></div>
			<div class="mainPart">
				<div class="backImg">
					<div class="blackImg"></div>
				</div>
				<div class="alertText">{{alertText}}</div>
				<div v-show="alertType==1" v-if="isShop">
					<div class="buttonMiddle" v-on:click="closeAlert">购买房卡</div>
				</div>
				<div v-show="alertType==1" v-if="!isShop">
					<div class="buttonMiddle" v-on:click="home">返回首页</div>
				</div>
				<div v-show="alertType==2">
					<div class="buttonMiddle" v-on:click="home">返回首页</div>
				</div>
				<div v-show="alertType==3">
					<div class="buttonLeft" v-on:click="home">返回首页</div>
					<div class="buttonRight" v-on:click="closeAlert">取消</div>
				</div>
				<div v-show="alertType==4">
					<div class="buttonLeft" v-on:click="home">创建房间</div>
					<div class="buttonRight" v-on:click="sitDown">加入游戏</div>
				</div>

				<div v-show="alertType==7">
					<div class="buttonMiddle" v-on:click="home">返回首页</div>
				</div>
				<div v-show="alertType==8">
				</div>
				<div v-show="alertType==9">
					<div class="buttonMiddle" v-on:click="showBreakRoom">确定</div>
				</div>
				<div v-show="alertType==10">
				    <div class="buttonLeft" v-on:click="closeAlert">取消</div>
					<div class="buttonRight" v-on:click="confirmBreakRoom">确定</div>
				</div>
				<div v-show="alertType==11">
					<div class="buttonMiddle" v-on:click="closeAlert">知道了</div>
				</div>
				<div v-show="alertType==21">
					<div class="buttonMiddle" v-on:click="closeAlert">确定</div>
				</div>
				<div v-show="alertType==22">
					<div class="buttonMiddle" v-on:click="closeAlert">确定</div>
				</div>
				<div v-show="alertType==23">
                    <div class="buttonMiddle" v-on:click="finishBindPhone()">确定</div>
                </div>
                <div v-show="alertType==31">
                    <div class="buttonMiddle" v-on:click="reloadView()">确定</div>
                </div>
                <div v-show="alertType==32">
					<div class="buttonMiddle" v-on:click="reloadView()">重新登录</div>
				</div>
			</div>
		</div>

        <!-- 游戏桌子 -->
		<div class="table" id="table" style="position: relative; width: 100vh; height: 104vh; top: -20px; z-index: 80; overflow: hidden;">
			<img class="tableBack"  src="<?php echo $image_url;?>files/images/tbull/back.jpg" style="position: absolute;top:0;left: 0;width: 100vw;height: 100vh" />

			<!-- 发牌 ||p.account_status==6||p.account_status==7 -->
			<div class="cardDeal">
			    <!-- p.account_id>0&&p.account_status>2&&p.account_status<8&&(p.num!=1||(p.account_status!=7&&p.account_status!=8&&!player[0].is_showCard))&&(game.cardDeal>0) -->
				<div v-for="p in player" v-if="ruleInfo.banker_mode!=5" v-show="p.account_id>0&&p.account_status>2&&p.account_status<8&&(p.num!=1||(p.account_status!=7&&p.account_status!=8&&!player[0].is_showCard))&&(game.cardDeal>0)">
					<div class="card" v-bind:class="'card' + p.num + '1'" style="z-index: 14;" v-show="game.cardDeal>0"></div>
					<div class="card" v-bind:class="'card' + p.num + '2'" style="z-index: 13;" v-show="game.cardDeal>1"></div>
					<div class="card" v-bind:class="'card' + p.num + '3'" style="z-index: 12;" v-show="game.cardDeal>2"></div>
					<div class="card" v-bind:class="'card' + p.num + '4'" style="z-index: 11;" v-show="game.cardDeal>3"></div>
					<div class="card" v-bind:class="'card' + p.num + '5'" style="z-index: 10;" v-show="game.cardDeal>4"></div>
				</div>

				<div v-for="p in player" v-if="ruleInfo.banker_mode==5" v-show="p.account_id>0&&p.account_status>2&&p.account_status<8&&(p.num!=1||(p.account_status!=7&&p.account_status!=8&&!player[0].is_showCard))&&(game.cardDeal>0)">
					<div class="card" v-bind:class="'card' + p.num + '1'" style="z-index: 14;" v-show="game.cardDeal>0"></div>
					<div class="card" v-bind:class="'card' + p.num + '2'" style="z-index: 13;" v-show="game.cardDeal>1"></div>
					<div class="card" v-bind:class="'card' + p.num + '3'" style="z-index: 12;" v-show="game.cardDeal>2"></div>
					<div class="card" v-bind:class="'card' + p.num + '4'" style="z-index: 11;" v-show="game.cardDeal>3"></div>
					<div class="card" v-bind:class="'card' + p.num + '5'" style="z-index: 10;" v-show="game.cardDeal>4"></div>
				</div>

                <!-- 玩家1发完牌之后  -->
				<div class="myCards" v-show="player[0].is_showCard&&player[0].account_status>2&&player[0].account_status<=7&&game.show_card">
                    <div class="cards3D">
					<div class="cards card0" v-show="player[0].account_status >2 && player[0].account_status <= 7">
						<div class="face front" ></div>
						<div class="face back" v-bind:class="'card' + player[0].card[0]"></div>
					</div>

					<div class="cards card1" v-show="player[0].account_status >2 && player[0].account_status <= 7">
						<div class="face front" ></div>
						<div class="face back" v-bind:class="'card' + player[0].card[1]"></div>
					</div>

					<div class="cards card2" v-show="player[0].account_status >2 && player[0].account_status <= 7">
						<div class="face front" ></div>
						<div class="face back" v-bind:class="'card' + player[0].card[2]"></div>
					</div>

					<div class="cards card3" v-show="player[0].account_status >2 && player[0].account_status <= 7">
						<div class="face front" v-on:click="seeMyCard4"></div>
						<div class="face back" v-bind:class="'card' + player[0].card[3]"></div>
					</div>

					<div class="cards card4" v-show="player[0].account_status >2 && player[0].account_status <= 7">
						<div class="face front" v-on:click="seeMyCard5"></div>
						<div class="face back" v-bind:class="'card' + player[0].card[4]"></div>
					</div>
					</div>
				</div>

                <!-- 玩家1摊牌看牌  -->
				<div class="myCards" v-show="player[0].is_showCard&&player[0].account_status==8&&game.show_card&&player[0].card_open.length>0" >
					<div class="cards card00">
						<div class="face back" v-bind:class="'card' + player[0].card_open[0]" style="-webkit-transform: rotateY(0deg);"></div>
					</div>

					<div class="cards card01">
						<div class="face back" v-bind:class="'card' + player[0].card_open[1]" style="-webkit-transform: rotateY(0deg);"></div>
					</div>

					<div class="cards card02">
						<div class="face back" v-bind:class="'card' + player[0].card_open[2]" style="-webkit-transform: rotateY(0deg);"></div>
					</div>

					<div class="cards card03">
						<div class="face back" v-bind:class="'card' + player[0].card_open[3]" style="-webkit-transform: rotateY(0deg);"></div>
					</div>

					<div class="cards card04">
						<div class="face back" v-bind:class="'card' + player[0].card_open[4]" style="-webkit-transform: rotateY(0deg);"></div>
					</div>
				</div>
			</div>

			<!-- game.cardDeal==-1&&p.account_status>1&&p.account_status!=6&&p.account_status!=7&&p.card.length>0-->

			<div class="cardOver" style="position: absolute; width: 100%; height: 100%; overflow:hidden;">
				<div v-for="p in player" v-if="p.num!=1" v-show="p.account_status>=8&&p.card_open.length>0&&game.show_card">
					<div class="cards" v-bind:class="'cards' + p.num + ' card' + p.num + '11'" style="z-index: 14;">
						<div class="face front"></div>
						<div class="face back" v-bind:class="'card' + p.card_open[4]"></div>
					</div>

					<div class="cards" v-bind:class="'cards' + p.num + ' card' + p.num + '21'" style="z-index: 13">
						<div class="face front"></div>
						<div class="face back" v-bind:class="'card' + p.card_open[3]"></div>
					</div>

					<div class="cards" v-bind:class="'cards' + p.num + ' card' + p.num + '31'" style="z-index: 12">
						<div class="face front"></div>
						<div class="face back" v-bind:class="'card' + p.card_open[2]"></div>
					</div>

					<div class="cards" v-bind:class="'cards' + p.num + ' card' + p.num + '41'" style="z-index: 11">
						<div class="face front"></div>
						<div class="face back" v-bind:class="'card' + p.card_open[1]"></div>
					</div>

					<div class="cards" v-bind:class="'cards' + p.num + ' card' + p.num + '51'" style="z-index: 10">
						<div class="face front"></div>
						<div class="face back" v-bind:class="'card' + p.card_open[0]"></div>
					</div>
				</div>
			</div>

            <!-- 抢庄文字图片 "{true: 'bankerSelected', false: 'bankerUnSelected'}[createInfo.bull.banker_mode==3]"   'memberRobText' + p.num-->
			<div v-for="p in player" v-if="p.num!=1&&ruleInfo.banker_mode!=2" v-bind:class="{true:'memberGoText' + p.num,false:'memberRobText' + p.num}[ruleInfo.banker_mode==5]" v-show="p.account_status==4||p.account_status==5">
				<img v-bind:src="p.robImg" style="position: absolute; width: 80%;">
			</div>

			<div v-for="p in player" v-if="p.num!=1&&ruleInfo.banker_mode==2" v-bind:class="'memberFreeRobText' + p.num" v-show="p.account_status==4||p.account_status==5">
				<img v-bind:src="p.robImg" style="position: absolute;  width: 80%;">
			</div>

			<div v-for="p in player" v-if="p.num!=1&&ruleInfo.banker_mode==2" v-bind:class="'memberGoTimesText' + p.num" v-show="p.account_status==5&&p.bankerMultiples>=1">
				<img v-bind:src="p.bankerTimesImg" style="position: absolute; width: 80%;">
			</div>

            <!-- 倍数文字图片 -->
            <div class="memberTimesText1" v-if="ruleInfo.banker_mode!=4" v-show="player[0].account_status>=6&&game.show_card&&!player[0].is_banker&&player[0].multiples>0">
            	<img v-bind:src="player[0].timesImg" style="position: absolute; width: 100%;">
            </div>
			<div v-for="p in player" v-if="p.num!=1&&ruleInfo.banker_mode!=4" v-bind:class="'memberTimesText' + p.num" v-show="p.account_status>=6&&game.show_card&&!p.is_banker&&p.multiples>0">
			    <img v-bind:src="p.timesImg" style="position: absolute; width: 100%;">
			</div>

            <!-- 庄家倍数文字图片 -->
			<div class="memberTimesText1" v-if="ruleInfo.banker_mode==2" v-show="player[0].account_status>=5&&game.show_card&&player[0].is_banker&&player[0].bankerMultiples>0">
            	<img v-bind:src="player[0].bankerTimesImg" style="position: absolute; width: 100%;">
            </div>
			<div v-for="p in player" v-if="p.num!=1&&ruleInfo.banker_mode==2" v-bind:class="'memberTimesText' + p.num" v-show="p.account_status>=5&&game.show_card&&p.is_banker&&p.bankerMultiples>0">
			    <img v-bind:src="p.bankerTimesImg" style="position: absolute; width: 100%;">
			</div>

            <!-- 牛几图片 -->
			<div v-for="p in player"  v-bind:class="'memberBull' + p.num" v-show="p.account_status==8&&game.show_card&&p.bullImg.length>=1">
			    <img v-bind:src="p.bullImg" style="position: absolute; top: 0px; left: 0px; width: 100%; height:100%">
			</div>

            <!-- 每局得分 -->

            <!-- 玩家1得分 -->
            <div  class="memberScoreText1" v-show="game.show_score" style="display: none;">
			    <label v-show="player[0].single_score<0&&player[0].account_status>=8"  style="position: absolute; line-height: 4vh; width: 100%; height: 4vh; text-align: center;color: white; font-size: 3vh;;font-family:'Helvetica 微软雅黑'; -webkit-text-stroke: 1.3px;text-shadow: 1px 2px 2px #022055; ">{{player[0].single_score}}</label>
			    <label v-show="player[0].single_score>0&&player[0].account_status>=8"  style="position: absolute; line-height: 4vh; width: 100%; height: 4vh; text-align: center;color: rgb(234,171,55); font-size: 3vh;font-family:'Helvetica 微软雅黑'; -webkit-text-stroke: 1.3px;text-shadow: 1px 2px 2px #782a00;">+{{player[0].single_score}}</label>
			    <label v-show="player[0].single_score==0&&player[0].account_status>=8"  style="position: absolute; line-height: 4vh; width: 100%; height: 4vh; text-align: center;color: rgb(234,171,55); font-size: 3vh;font-family:'Helvetica 微软雅黑'; -webkit-text-stroke: 1.3px;text-shadow: 1px 2px 2px #782a00;">0</label>
			</div>

			<div v-for="p in player"  v-if="p.num==2||p.num==3||p.num==4||p.num==5||p.num==6" v-bind:class="'memberScoreText' + p.num" v-show="game.show_score" style="display: none;">
			    <p v-show="p.single_score<0&&p.account_status>=8" style="position: absolute; line-height: 4vh; width:100%;height: 4vh; text-align: right;color: white; font-size: 3vh;font-family:'Helvetica 微软雅黑'; -webkit-text-stroke: 1.3px;text-shadow: 1px 2px 2px #022055; ">{{p.single_score}}</p>
			    <p v-show="p.single_score>0&&p.account_status>=8" style="position: absolute; line-height: 4vh; width:100%;height: 4vh; text-align: right;color: rgb(234,171,55); font-size: 3vh;font-family:'Helvetica 微软雅黑'; -webkit-text-stroke: 1.3px;text-shadow: 1px 2px 2px #782a00;">+{{p.single_score}}</p>
			    <p v-show="p.single_score==0&&p.account_status>=8" style="position: absolute; line-height: 4vh; width:100%;height: 4vh; text-align: right;color: rgb(234,171,55); font-size: 3vh;;font-family:'Helvetica 微软雅黑'; -webkit-text-stroke: 1.3px;text-shadow: 1px 2px 2px #782a00; ">0</p>
			</div>

			<div v-for="p in player"  v-if="p.num==8||p.num==9||p.num==10||p.num==11||p.num==12" v-bind:class="'memberScoreText' + p.num" v-show="game.show_score" style="display: none;">
			    <label v-show="p.single_score<0&&p.account_status>=8" style="position: absolute; line-height: 4vh; height: 4vh; text-align: left;color: white; font-size: 3vh;font-family:'Helvetica 微软雅黑'; -webkit-text-stroke: 1.3px;text-shadow: 1px 2px 2px #022055; ">{{p.single_score}}</label>
			    <label v-show="p.single_score>0&&p.account_status>=8" style="position: absolute; line-height: 4vh; height: 4vh; text-align: left;color: rgb(234,171,55); font-size: 3vh;font-family:'Helvetica 微软雅黑'; -webkit-text-stroke: 1.3px;text-shadow: 1px 2px 2px #782a00;">+{{p.single_score}}</label>
			    <label v-show="p.single_score==0&&p.account_status>=8" style="position: absolute; line-height: 4vh; height: 4vh; text-align: left;color: rgb(234,171,55); font-size: 3vh;;font-family:'Helvetica 微软雅黑'; -webkit-text-stroke: 1.3px;text-shadow: 1px 2px 2px #782a00;">0</label>
			</div>

			<!-- 玩家4得分 -->
			<div  v-for="p in player" v-if="p.num==7" v-bind:class="'memberScoreText' + p.num"  v-show="game.show_score" style="display: none;">
			    <label v-show="p.single_score<0&&p.account_status>=8" style="position: absolute; line-height: 4vh; width: 100%; height: 4vh; text-align: center; color: white; font-size: 3vh;font-family:'Helvetica 微软雅黑'; -webkit-text-stroke: 1.3px;text-shadow: 1px 2px 2px #022055; ">{{p.single_score}}</label>
			    <label v-show="p.single_score>0&&p.account_status>=8"  style="position: absolute; line-height: 4vh; width: 100%; height: 4vh; text-align: center; color: rgb(234,171,55); font-size: 3vh;font-family:'Helvetica 微软雅黑'; -webkit-text-stroke: 1.3px;text-shadow: 1px 2px 2px #782a00;">+{{p.single_score}}</label>
			    <label v-show="p.single_score==0&&p.account_status>=8" style="position: absolute; line-height: 4vh; width: 100%; height: 4vh; text-align: center; color: rgb(234,171,55); font-size: 3vh;font-family:'Helvetica 微软雅黑'; -webkit-text-stroke: 1.3px;text-shadow: 1px 2px 2px #782a00;">0</label>
			</div>


			<!-- 玩家  -->
            <div key="player[0].num" class="member" v-bind:class="'member' + player[0].num" v-show="player[0].account_id>0 && player[0].num==1">
            	<!-- 玩家头像 -->
				<img v-bind:src="player[0].headimgurl" class="avatar">
				<img src="<?php echo $image_url;?>files/images/nbull/player_selected.png" class="banker" v-show="player[0].is_banker">

				<div class="bottom" style="background-color: rgba(0, 0, 0, 0.6); border-radius: 2px;overflow: hidden;">
					<div class="bname" style="text-align: center;overflow: hidden;">{{player[0].nickname}}</div>
					<div class="bscore" style="text-align: center;overflow: hidden;">{{player[0].account_score}}</div>
				</div>

				<img v-bind:id="'banker' + player[0].account_id" src="<?php echo $image_url;?>files/images/nbull/banker_bg.png" class="background" style="position: absolute; top: -0.2vh;left: -0.2vh;width: 6.4vh; height: 6.4vh; display: none;" />

				<img src="<?php echo $image_url;?>files/images/bull/banker_icon.png" class="background" style="position: absolute; top: -6%;left: -6%;width: 2.6vh; height: 2.6vh;" v-show="player[0].is_banker">

				<img v-bind:id="'bankerAnimate' + player[0].num" src="<?php echo $image_url;?>files/images/nbull/banker_animate.png" style="position: absolute; top: -0.2vh; left: -0.2vh; width: 7.86vh; height: 7.86vh; display: none;">
				<img v-bind:id="'bankerAnimate1' + player[0].num" src="<?php echo $image_url;?>files/images/nbull/banker_animate.png" style="position: absolute; top: -0.2vh; left: -0.2vh; width: 7.86vh; height: 7.86vh; display: none;">

				<!-- 玩家离开或不在线 -->
                <div class="quitBack" v-show="player[0].num>1&&p.online_status==0" ></div>

            </div>

			<div  v-for="p in player" key="p.num" class="member" v-bind:class="'member' + p.num" v-show="p.account_id>0 && p.num!=1" >
				<!-- 玩家头像 -->
				<img v-bind:src="p.headimgurl" class="avatar">
				<img src="<?php echo $image_url;?>files/images/nbull/player_selected.png" class="banker" v-show="p.is_banker">

				<div class="bottom" style="background-color: rgba(0, 0, 0, 0.6); border-radius: 2px;overflow: hidden;">
					<div class="bname" style="text-align: center;overflow: hidden;">{{p.nickname}}</div>
					<div class="bscore" style="text-align: center;overflow: hidden;">{{p.account_score}}</div>
				</div>

				<img v-bind:id="'banker' + p.account_id" src="<?php echo $image_url;?>files/images/nbull/banker_bg.png" class="background" style="position: absolute; top: -0.2vh;left: -0.2vh;width: 6.4vh; height: 6.4vh; display: none;" />

				<img src="<?php echo $image_url;?>files/images/bull/banker_icon.png" class="background" style="position: absolute; top: -6%;left: -6%;width: 2.6vh; height: 2.6vh;" v-show="p.is_banker">

				<img v-bind:id="'bankerAnimate' + p.num" src="<?php echo $image_url;?>files/images/nbull/banker_animate.png" style="position: absolute; top: -0.2vh; left: -0.2vh; width: 7.86vh; height: 7.86vh; display: none;">
				<img v-bind:id="'bankerAnimate1' + p.num" src="<?php echo $image_url;?>files/images/nbull/banker_animate.png" style="position: absolute; top: -0.2vh; left: -0.2vh; width: 7.86vh; height: 7.86vh; display: none;">

				<!-- 玩家离开或不在线 -->
                <div class="quitBack" v-show="p.num>1&&p.online_status==0" ></div>

                <!-- 准备状态 game.round!=game.total_num -->
				<div class="isReady" v-show="game.round!=game.total_num">
					<img src="<?php echo $image_url;?>files/images/common/ready_button.png" class="unready" v-show="(p.account_status==1||p.account_status==0)&&p.num==1" v-on:click="imReady" />
					<img src="<?php echo $image_url;?>files/images/common/ready_text.png" class="ready" v-show="p.account_status==2" />
				</div>

			</div>

            <!-- 玩家金币 -->

            <div id="playerCoins" style="display: none;">
            	<div v-for='p in player'>
            		<div v-for='coin in p.coins' class="memberCoin" v-bind:class="coin">
            			<img src="<?php echo $image_url;?>files/images/common/coin.png" style="position: absolute; width: 100%; height: 100%">
            		</div>
            	</div>
            </div>

            <!-- 显示玩家消息文本 p.messageOn-->
			<div v-for="p in player">
				<div class="messageSay" v-bind:class="'messageSay' + p.num" v-show="p.messageOn">
					<div>{{p.messageText}}</div>
					<div class="triangle"> </div>
				</div>
			</div>

            <!-- 游戏状态:选择抢庄 player[0].account_status==3 -->
            <div id="divRobBankerText" v-show="showClockRobText&&ruleInfo.banker_mode!=4" v-bind:style="'position: absolute;top: 33%; left: 0px; width:' + width + 'px; height: 30px;'">
            	<p style="color: white; font-size: 2vh;width: 100%;height: 30px; line-height: 30px; text-align: center;font-family:'Helvetica 微软雅黑';">{{ruleInfo.bankerText}}</p>
            </div>
            <!-- 游戏状态:闲家下注 player[0].account_status==6&&game.show_coin -->
            <div id="divBetText" v-show="showClockBetText" v-bind:style="'position: absolute;top: 33%; left: 0px; width:' + width + 'px; height: 30px;'">
            	<p style="color: white; font-size: 2vh; width: 100%;height: 30px; line-height: 30px; text-align: center;font-family:'Helvetica 微软雅黑';">闲家下注</p>
            </div>
            <!-- 游戏状态:等待摊牌 -->
            <div id="divBetText" v-show="showClockShowCard" v-bind:style="'position: absolute;top: 33.5%; left: 0px; width:' + width + 'px; height: 30px;'">
            	<p style="color: white; font-size: 2vh; width: 100%;height: 30px; line-height: 30px; text-align: center;font-family:'Helvetica 微软雅黑';">等待摊牌</p>
            </div>

            <!-- 倒计时时钟 position: absolute; top: 38%; left: 44%; width: 40px; height: 42px; -->
			<div id="" class="clock" v-show="game.time>-1" v-bind:style="'position: absolute; top: 38%; left:' + (width - 40) / 2 + 'px; width: 40px; height: 42px;'">
			    <img src="<?php echo $image_url;?>files/images/common/clock.png" style="position: absolute; width: 100%; height: 100%;" />
			    <p style="position: absolute; top: 0px; left: 0px; width: 40px; height: 42px; color: white; text-align: center; line-height: 42px;">
			        {{game.time}}
			    </p>
			</div>

            <!-- 按钮操作区域 -->
            <div id="operationButton" v-bind:style="viewStyle.button">

				<div v-show="game.round!=game.total_num" v-bind:style="viewStyle.readyText">
					<img src="<?php echo $image_url;?>files/images/common/ready_text.png" style="position: absolute; width: 100%; height: 100%;" v-show="player[0].account_status==2" />
				</div>

				<div  v-show="game.round!=game.total_num" v-bind:style="viewStyle.readyButton" >
					<img src="<?php echo $image_url;?>files/images/common/ready_button.png" style="position: absolute; width: 100%; height: 100%;" v-show="(player[0].account_status==1||player[0].account_status==0)&&player[0].num==1&&player[0].account_id>0&&canBreak!=1" />
					<div style="position: absolute;width: 100%;height: 100%" v-on:click="imReady"></div>
				</div>

                <!-- 抢庄 -->
            	<div v-show="showRob&&ruleInfo.banker_mode==1" v-on:click="robBanker(1)" v-bind:style="viewStyle.rob">
            		<img src="<?php echo $image_url;?>files/images/bull/button_orange.png" style="position: absolute; top: 0px; left: 0px; width: 100%; height: 100%">
            		<div v-bind:style="viewStyle.rob1">抢庄</div>
            	</div>

                <!-- 不抢 -->
            	<div v-show="showRob&&ruleInfo.banker_mode==1" v-on:click="notRobBanker" v-bind:style="viewStyle.notRob">
            		<img src="<?php echo $image_url;?>files/images/bull/button_blue.png" style="position: absolute; top: 0px; left: 0px; width: 100%; height: 100%">
            		<div v-bind:style="viewStyle.notRob1">不抢</div>
            	</div>

                <!-- 准备 -->
            	<div v-show="(player[0].account_status==1||player[0].account_status==0)&&player[0].num==1&&ruleInfo.banker_mode==5&&canBreak==1&&game.round!=game.total_num&&player[0].is_banker" v-on:click="imReady"  v-bind:style="viewStyle.notRob">
            		<img src="<?php echo $image_url;?>files/images/bull/button_orange.png" style="position: absolute; top: 0px; left: 0px; width: 100%; height: 100%">
            		<div v-bind:style="viewStyle.rob1">准备</div>
            	</div>

            	<!-- 1倍 -->
            	<div class="divCoin" v-show="showRob&&ruleInfo.banker_mode==2" v-on:click="robBanker(1)" v-bind:style="viewStyle.times1">
            		<img src="<?php echo $image_url;?>files/images/bull/button_times.png" style="position: absolute; top: 0px; left: 0px; width: 100%; height: 100%">
            		<div v-bind:style="viewStyle.timesText">1倍</div>
            	</div>

                <!-- 2倍 -->
            	<div class="divCoin" v-show="showRob&&ruleInfo.banker_mode==2" v-on:click="robBanker(2)" v-bind:style="viewStyle.times2">
            		<img src="<?php echo $image_url;?>files/images/bull/button_times.png" style="position: absolute; top: 0px; left: 0px; width: 100%; height: 100%">
            		<div v-bind:style="viewStyle.timesText">2倍</div>
            	</div>

                <!-- 4倍 -->
            	<div class="divCoin" v-show="showRob&&ruleInfo.banker_mode==2" v-on:click="robBanker(4)" v-bind:style="viewStyle.times3">
            		<img src="<?php echo $image_url;?>files/images/bull/button_times.png" style="position: absolute; top: 0px; left: 0px; width: 100%; height: 100%">
            		<div v-bind:style="viewStyle.timesText">4倍</div>
            	</div>

                <!-- 不抢 -->
            	<div class="divCoin" v-show="showRob&&ruleInfo.banker_mode==2" v-on:click="notRobBanker" v-bind:style="viewStyle.times4">
            		<img src="<?php echo $image_url;?>files/images/bull/button_times_blue.png" style="position: absolute; top: 0px; left: 0px; width: 100%; height: 100%">
            		<div v-bind:style="viewStyle.timesText">不抢</div>
            	</div>

                <!-- 摊牌 -->
            	<div id="showShowCardButton" v-show="showShowCardButton" v-on:click="showCard" v-bind:style="viewStyle.showCard">
            		<img src="<?php echo $image_url;?>files/images/bull/button_blue.png" style="position: absolute; top: 0px; left: 0px; width: 100%; height: 100%">
            		<div v-bind:style="viewStyle.showCard1">摊牌</div>
            	</div>

                <!-- 第1个倍数 -->
                <div class="divCoin" v-show="showTimesCoin&&!player[0].multiples>0 && !player[0].is_banker&&player[0].account_status>=6" v-on:click="selectTimesCoin(ruleInfo.times1)" v-bind:style="viewStyle.times1">
                    <img src="<?php echo $image_url;?>files/images/bull/button_times.png" style="position: absolute; top: 0px; left: 0px; width: 100%; height: 100%">
                    <div v-bind:style="viewStyle.timesText">{{ruleInfo.times1}}倍</div>
                </div>

                <!-- 第2个倍数 -->
                <div class="divCoin" v-show="showTimesCoin&&!player[0].multiples>0&& !player[0].is_banker&&player[0].account_status>=6" v-on:click="selectTimesCoin(ruleInfo.times2)" v-bind:style="viewStyle.times2">
                    <img src="<?php echo $image_url;?>files/images/bull/button_times.png" style="position: absolute; top: 0px; left: 0px; width: 100%; height: 100%">
                    <div v-bind:style="viewStyle.timesText">{{ruleInfo.times2}}倍</div>
                </div>

                <!-- 第3个倍数 -->
                <div class="divCoin" v-show="showTimesCoin&&!player[0].multiples>0&& !player[0].is_banker&&player[0].account_status>=6" v-on:click="selectTimesCoin(ruleInfo.times3)" v-bind:style="viewStyle.times3">
                    <img src="<?php echo $image_url;?>files/images/bull/button_times.png" style="position: absolute; top: 0px; left: 0px; width: 100%; height: 100%">
                    <div v-bind:style="viewStyle.timesText">{{ruleInfo.times3}}倍</div>
                </div>

                <!-- 第4个倍数 -->
                <div class="divCoin" v-show="showTimesCoin&&!player[0].multiples>0&& !player[0].is_banker&&player[0].account_status>=6" v-on:click="selectTimesCoin(ruleInfo.times4)" v-bind:style="viewStyle.times4">
                    <img src="<?php echo $image_url;?>files/images/bull/button_times.png" style="position: absolute; top: 0px; left: 0px; width: 100%; height: 100%">
                    <div v-bind:style="viewStyle.timesText">{{ruleInfo.times4}}倍</div>
                </div>

                <!-- 抢庄文字 -->
            	<div v-if="showRobText&&ruleInfo.banker_mode!=5&&ruleInfo.banker_mode!=2" v-bind:style="viewStyle.robText" >
            		<img src="<?php echo $image_url;?>files/images/bull/text_rob.png" style="position: absolute; top: 0px; left: 0px; width: 100%; height: 100%">
            	</div>

            	<div v-if="showRobText&&ruleInfo.banker_mode==2" v-bind:style="viewStyle.robText2" >
            		<img src="<?php echo $image_url;?>files/images/bull/text_rob.png" style="position: absolute; top: 0px; left: 0px; width: 100%; height: 100%">
            	</div>

            	<div v-if="showRobText&&ruleInfo.banker_mode==2&&player[0].bankerMultiples>0" v-bind:style="viewStyle.robTimesText" >
            		<img v-bind:src="player[0].bankerTimesImg" style="position: absolute; top: 0px; left: 0px; width: 100%; height: 100%">
            	</div>

                <!-- 不抢庄文字 -->
            	<div v-if="showNotRobText&&ruleInfo.banker_mode!=5" v-bind:style="viewStyle.notRobText">
            		<img src="<?php echo $image_url;?>files/images/bull/text_notrob.png" style="position: absolute; top: 0px; left: 0px; width: 100%; height: 100%">
            	</div>

            	<!-- 上庄文字 -->
            	<div v-if="showRobText&&ruleInfo.banker_mode==5&&game.round<2" v-bind:style="viewStyle.robText" >
            		<img src="<?php echo $image_url;?>files/images/bull/text_go.png" style="position: absolute; top: 0px; left: 0px; width: 100%; height: 100%">
            	</div>

                <!-- 不上庄文字 -->
            	<div v-if="showNotRobText&&ruleInfo.banker_mode==5&&game.round<2" v-bind:style="viewStyle.notRobText">
            		<img src="<?php echo $image_url;?>files/images/bull/text_notgo.png" style="position: absolute; top: 0px; left: 0px; width: 100%; height: 100%">
            	</div>

            	<!-- 等待玩家下注 -->
            	<div v-show="showBankerCoinText" v-bind:style="viewStyle.coinText">
            		<p v-bind:style="viewStyle.coinText1">等待闲家下注</p>
            	</div>

            	<!-- 点击看牌-->
            	<div v-show="showClickShowCard" v-bind:style="viewStyle.showCardText">
            		<p v-bind:style="viewStyle.showCardText1">点击牌面看牌</p>
            	</div>
            </div>
		</div>

        <div class="ranking hideRanking" id="ranking" style="z-index: 1">
			<div class="rankBack">
				<img   src="<?php echo $image_url;?>files/images/tbull/rank_bg.jpg" style="position: absolute;top: 0;left: 0;width: 100%">
			</div>

			<div class="rankText" style="position: absolute;top: 4%;">
				<img   src="<?php echo $image_url;?>files/images/tbull/ranking.png" style="position: absolute;top: 0;left: 25vw;width: 150vw;">
				<div class="time" v-show="playerBoard.round>0" style="position: absolute;top: 44vw;width: 100%;">
					<a style="border-color: #fffcd5;background-color: #56492c;font-size: 6vw;">房间号:{{game.room_number}}&nbsp&nbsp&nbsp&nbsp{{playerBoard.record}}&nbsp&nbsp&nbsp&nbsp{{game.total_num}}局</a>
				</div>
				<div style="height: 68vw;"></div>
                <div class="scoresItem scoresItemWhite">
                    <div class="name">昵称</div>
                    <div class="currentScores">得分</div>
                    <div class="consumeScores">消耗</div>
                </div>
				<div v-for="p in playerBoard.score" class="scoresItem" v-bind:class="{true: 'scoresItemYellow', false: 'scoresItemWhite'}[p.account_score>0]" v-show="p.account_id>0">
				    <img   src="<?php echo $image_url;?>files/images/common/rank_bigwinner.png" style="top: 0; margin-top: -1.5vw; left: 2vw;height: 100%" v-show="p.isBigWinner==1">
                    <div class="name">{{p.nickname}}</div>
					<div class="currentScores"><a v-show="p.account_score>0">+</a>{{p.account_score}}</div>
                    <div class="consumeScores">{{p.consume_score}}</div>
                </div>
			</div>
			<!--<div class="button roundEndShow" v-if="roomStatus!=4">-->
			<div class="button roundEndShow" >
                <img src="<?php echo $image_url;?>files/images/common/rank_save.png" style="float: left;margin-left: 10%;" />
                <img src="<?php echo $image_url;?>files/images/common/score_search.png" style="float: right;margin-right: 10%" />
			</div>
		</div>

		<!-- 消息  -->
		<div class="message" v-show="isShowMessage" >
        	<div class="messageBack" style="z-index: 999" v-on:click="hideMessage"></div>
        	<div class="textPartOuter" style="z-index: 999"></div>
        	<div id="message-box" class="textPart" v-bind:style="'height: ' + 0.39 * height + 'px;z-index: 999'">
        		<!-- <div class="outline"></div> -->
        		<div id="scroll-box" class="textList" style="width: 100%;">
        			<div class="textItem" v-for="m in message" v-on:click="messageOn(m.num)">{{m.text}}</div>
        			<!-- <div class="textItem" style="height: 5px;background: #434547;"></div> -->
        		</div>
        	</div>
        </div>

		<!-- end图片  -->
		<div id="endCreateRoom" class="end" style="position: fixed;width: 100%;height:100%;top:0;left:0;z-index: 120;display: none;">
			<img src="" style="width: 100vw;position: absolute;top:0;left: 0;height: 100vh;" id="end"  usemap="#planetmap1" />

            <div style="width:100%;position: absolute;bottom:3%;display: block"  >
                <a href="/f/yh" style="width:34vw;height:11vw;float: right;margin-right: 10%" ></a>
            </div>
		</div>

        <!-- 积分数据-->
        <div class="layerGameScore" id="vGameScore" v-show="scoreInfo.isShow" v-on:click="cancelGameScore">
            <div class="gameScoreBack"></div>
            <div class="mainPart" style="height: 232px;">
                <div class="createB"></div>
                <div class="createTitle" style="line-height: 36px;color: #f2ae4a">您的VIP十二人斗牌战绩</div>
                <img src="<?php echo $image_url;?>files/images/common/cancel.png" class="cancelCreate" v-on:click="cancelGameScore"/>
                <div class="blueBack" style="height: 184px;">
                    <div class="selectPart" style="height:30px;line-height:30px;padding:6px 0;">
                        <div class="selectTitle">最近一天：{{scoreInfo.one}}</div>
                    </div>
                    <div class="selectPart" style="height:30px;line-height:30px;padding:6px 0;">
                        <div class="selectTitle">最近三天：{{scoreInfo.three}}</div>
                    </div>
                    <div class="selectPart" style="height:30px;line-height:30px;padding:6px 0;">
                        <div class="selectTitle" style="width: 85px;">最近一周：{{scoreInfo.week}}</div>
                    </div>
                    <div class="selectPart" style="height:30px;line-height:30px;padding:6px 0;">
                        <div class="selectTitle">最近一月：{{scoreInfo.month}}</div>
                    </div>
                </div>
            </div>
        </div>

		<!-- 游戏规则 -->
        <div class="createRoom" id="vroomRule" v-show="ruleInfo.isShowRule" v-on:click="cancelGameRule">
			<div class="createRoomBack"></div>
			<div class="mainPart" >
				<div  class="createB"></div>
				<div class="createTitle">
					<img src="<?php echo $image_url;?>files/images/common/txt_rule.png" />
				</div>

                <img src="<?php echo $image_url;?>files/images/common/cancel.png" class="cancelCreate" v-on:click="cancelGameRule"/>

				<div class="blueBack">
                    <div class="selectPart" style="top: 0px;height:4vh;line-height:4.1vh;">
						<div class="selectTitle" style="width: 100%;font-size: 2vh; text-align: center;color: #7dd9ff; background-color: #143948;opacity: 1.0">创建房间,游戏未进行,不消耗房卡</div>
					</div>

                    <div class="selectPart" style="height:4vh;line-height:4vh;padding:0.8vh 0;">
						<div class="selectTitle">模式：</div>
						<div class="selectList">
							<div class="selectItem" style="margin-left:10px;">
								<div class="selectText" >明牌抢庄</div>
							</div>
						</div>
					</div>

					<div class="selectPart" style="height:4vh;line-height:4vh;padding:0.8vh 0;">
						<div class="selectTitle">底分：</div>
						<div class="selectList" >
                            <div class="selectItem" style="margin-left:10px;" v-if="ruleInfo.baseScore==1">
                                <div class="selectText">1分</div>
                            </div>
                            <div class="selectItem" style="margin-left:10px;" v-if="ruleInfo.baseScore==2">
                                <div class="selectText">2分</div>
                            </div>
                            <div class="selectItem" style="margin-left:10px;" v-if="ruleInfo.baseScore==3">
                                <div class="selectText">3分</div>
                            </div>
                            <div class="selectItem" style="margin-left:10px;" v-if="ruleInfo.baseScore==5">
                                <div class="selectText">5分</div>
                            </div>
						</div>
					</div>

					<div class="selectPart" style="height:4vh;line-height:4vh;padding:0.8vh 0;">
						<div class="selectTitle">规则：</div>
						<div class="selectList">
							<div class="selectItem" style="margin-left:10px;" v-if="ruleInfo.ruleType==1">
								<div class="selectText" >牛牛x3牛九x2牛八x2</div>
							</div>
							<div class="selectItem" style="margin-left:10px;" v-if="ruleInfo.ruleType==2">
								<div class="selectText" >牛牛x4牛九x3牛八x2牛七x2</div>
							</div>
						</div>
					</div>

					<div class="selectPart" v-bind:style="'height:' + ruleInfo.rule_height + ';line-height:4vh;padding:0.8vh 0;'" v-if="ruleInfo.isCardbomb==1 || ruleInfo.isCardtiny==1 || ruleInfo.isCardfive==1 || ruleInfo.isCardfour==1">
						<div class="selectTitle">牌型：</div>
						<div class="selectList">
							<div class="selectItem" style="margin-left:10px;" v-if="ruleInfo.isCardfour==1">
								<div class="selectText" >四花牛(4倍)</div>
							</div>
							<div class="selectItem" style="margin-left:10px;" v-if="ruleInfo.isCardfive==1">
								<div class="selectText" >五花牛(5倍)</div>
							</div>
							<div class="selectItem" style="margin-left:10px;" v-if="ruleInfo.isCardbomb==1">
								<div class="selectText" >炸弹牛(6倍)</div>
							</div>
							<div class="selectItem" style="margin-left:10px;" v-if="ruleInfo.isCardtiny==1">
								<div class="selectText" >五小牛(8倍)</div>
							</div>
						</div>
					</div>

					<div class="selectPart" style="height:4vh;line-height:4vh;padding:0.8vh 0;">
						<div class="selectTitle">局数：</div>
						<div class="selectList">
							<div class="selectItem" style="margin-left:10px;" v-if="ruleInfo.ticket==1">
								<div class="selectText" >12局X2房卡</div>
							</div>
							<div class="selectItem" style="margin-left:10px;" v-if="ruleInfo.ticket==2">
								<div class="selectText" >24局X4房卡</div>
							</div>
						</div>
					</div>

                    <div class="selectPart" style="height:4vh;line-height:4vh;padding:0.8vh 0;">
                        <div class="selectTitle">倍数：</div>
                        <div class="selectList">
                            <div class="selectItem" style="margin-left:10px;" v-if="ruleInfo.timesType==1">
                                <div class="selectText" >1，2，4，5</div>
                            </div>
                            <div class="selectItem" style="margin-left:10px;" v-if="ruleInfo.timesType==2">
                                <div class="selectText" >1，3，5，8</div>
                            </div>
                            <div class="selectItem" style="margin-left:10px;" v-if="ruleInfo.timesType==3">
                                <div class="selectText" >2，4，6，10</div>
                            </div>
                        </div>
                    </div>

                    <div class="selectPart" style="height:4vh;line-height:4vh;padding:0.8vh 0;">
                        <div class="selectTitle">准入：</div>
                        <div class="selectList">
                            <div class="selectItem" style="margin-left:10px;" v-if="ruleInfo.bean_type==1">
                                <div class="selectText">≥50豆</div>
                            </div>
                            <div class="selectItem" style="margin-left:8px;" v-if="ruleInfo.bean_type==2">
                                <div class="selectText">≥100豆</div>
                            </div>
                            <div class="selectItem" style="margin-left:8px;" v-if="ruleInfo.bean_type==3">
                                <div class="selectText">≥300豆</div>
                            </div>
                            <div class="selectItem" style="margin-left:8px;" v-if="ruleInfo.bean_type==4">
                                <div class="selectText">≥500豆</div>
                            </div>
                        </div>
                    </div>

                <!-- <div class="createCommit" v-on:click="cancelGameRule" >确定</div> -->

				</div>
			</div>
		</div>

		<!-- 设置音频 -->
		<div class="audioRoom" id="vaudioRoom" v-show="editAudioInfo.isShow">
			<div class="audioRoomBack" v-on:click="cancelAudioSetting"></div>
			<div class="mainPart" >
				<div  class="createB"></div>
				<div class="createTitle" style="height:4vh;">
				</div>

				<img src="<?php echo $image_url;?>files/images/common/cancel.png" class="cancelCreate" v-on:click="cancelAudioSetting"/>

				<div class="blueBack">
					<!--<div class="selectPart" style="top: 0px;height:4vh;line-height:4.1vh;">
						<div class="selectTitle" style="width: 100%;font-size: 2vh; text-align: center;color: #7dd9ff; background-color: #143948;opacity: 1.0">点击确定后生效</div>
					</div>	-->
					<div style="height:0.5vh;"></div>

					<div class="selectPart" style="height:4vh;line-height:4vh;padding:0.8vh 0;">
						<div class="selectTitle">背景音乐：</div>
						<div class="selectList" >
							<div class="selectItem" style="margin-left:10px;" v-on:click="setBackMusic" >
								<div class="selectBox"><img src="<?php echo $image_url;?>files/images/common/tick.png" v-show="editAudioInfo.backMusic==1"/></div>
								<div class="selectText">开启</div>
							</div>
						</div>
					</div>

					<div class="selectPart" style="height:4vh;line-height:4vh;padding:0.8vh 0;">
						<div class="selectTitle">游戏音效：</div>
						<div class="selectList" >
							<div class="selectItem" style="margin-left:10px;" v-on:click="setMessageMusic">
								<div class="selectBox"><img src="<?php echo $image_url;?>files/images/common/tick.png" v-show="editAudioInfo.messageMusic==1"/></div>
								<div class="selectText">开启</div>
							</div>
						</div>
					</div>

					<div class="createCommit" v-on:click="confirmAudioSetting" >确定</div>

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
					<input  v-on:input="phoneChangeValue()" v-model="sPhone" type="number" name="phone" placeholder="输入手机号" style="padding:0 12px 0 12px;position: absolute;top:  2.5vw;left: 4vw;width: 48vw;height: 11vw;line-height: 6.5vw;border-style: solid;border-width: 1px;border-radius: 0.5vh;border-color: #e6e6e6;font-size: 4vw;-webkit-appearance: none;">
					<div id="authcode" v-on:click="getAuthcode()" style="position: absolute;top:  2.5vw;right: 4vw; width: 22vw;height: 10vw;line-height: 10vw;background-color: rgb(211,211,211);font-size: 3.5vw;border-radius: 0.5vh;color: white;">
						{{authcodeText}}
					</div>
				</div>
				<div style="position: relative;height: 15vw;word-wrap: break-word;word-break: break-all;color: #000;background-color: white;border-top: solid;border-color: #e6e6e6;border-width: 0px;">
					<input  v-model="sAuthcode" type="number" name="phone1" placeholder="输入验证码" style="padding:0 12px 0 12px;position: absolute;top: 1vw;left: 4vw;width: 72vw;height: 11vw;line-height: 6.5vw;border-style: solid;border-width: 1px;border-radius: 0.5vh;border-color: #e6e6e6;font-size: 4vw;-webkit-appearance: none;">

				</div>
				<div style="height: 2.2vw;"></div>
				<div style="position: relative; left: 4vw;width: 72vw;line-height: 10vw; font-size: 4vw;display: flex;border-radius: 2vw;" v-on:click="bindPhone()">
					<div style="display: block;-webkit-box-flex:1;flex: 1;text-decoration: none;-webkit-tap-highlight-color:transparent;position: relative;margin-bottom: 0;color: rgb(255,255,255);border-top: solid;border-color: #e6e6e6;border-width: 0px;background-color: rgb(64,112,251);border-radius: 1vw;">立即绑定</div>
				</div>
				<div style="height:4vw;"></div>
			</div>
		</div>

		<script type="text/javascript" src="<?php echo $image_url;?>files/js/canvas.js"></script>
	</div>

</body>

<script src="https://res.wx.qq.com/open/js/jweixin-1.0.0.js" ></script>
<script type="text/javascript" src="<?php echo $image_url;?>files/js/jquery-1.9.1.min.js"></script>
<script type="text/javascript" src="<?php echo $image_url;?>files/js/bscroll.js"></script>
<script type="text/javascript" src="<?php echo $image_url;?>files/js/vue.min.js" ></script>
<script type="text/javascript" src="<?php echo $image_url;?>files/js/vue-resource.min.js" ></script>
<script type="text/javascript" src="<?php echo $base_url;?>files/js/vbull12.js?<?php echo time();?>"></script>

<script type="text/javascript" src="<?php echo $image_url;?>files/js/jquery.marquee.min.js"></script>
<script type="application/javascript">
    $('.marquee').marquee({
        duration: 5000,
        delayBeforeStart: 0,
        direction: 'left',
    });
</script>

</html>
