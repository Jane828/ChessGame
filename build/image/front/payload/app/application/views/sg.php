<html>
<head>
<meta charset="utf-8" >
<meta name="viewport" content="width=device-width,initial-scale=1.0, minimum-scale=1.0, maximum-scale=1.0, user-scalable=no"/>
<meta name="format-detection" content="telephone=no" />
<meta name="msapplication-tap-highlight" content="no" />
<meta http-equiv="Pragma" content="public" />
<meta http-equiv="Cache-Control" content="public" />
<title>六人三公房间<?php echo $room_number;?></title>

<link rel="stylesheet" type="text/css" href="<?php echo $base_url;?>files/css/sg.css?_version=<?php echo $front_version;?>">
<link rel="stylesheet" type="text/css" href="<?php echo $image_url;?>files/css/alert.css?_version=<?php echo $front_version;?>">
<script type="text/javascript" src="<?php echo $image_url;?>files/js/fastclick.js?_version=<?php echo $front_version;?>"></script>

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
		"session":'<?php echo $session;?>',
		"httpUrl":'<?php echo $http_url;?>',
		"shareTitle":"六人三公"
	};
	var userData = {
		"accountId":"<?php echo $user['account_id'];?>",
		"nickname":"<?php echo $user['nickname'];?>",
		"avatar":"<?php echo $user['headimgurl'];?>",
		"isAuthPhone":"<?php echo $isAuthPhone;?>",
		"authCardCount":"<?php echo $authCardCount;?>",
		"phone":"<?php echo $phone;?>"
	};
	var configData = {
		"appId":"<?php echo $config_ary['appId'];?>",
		"timestamp":"<?php echo $config_ary['timestamp'];?>",
		"nonceStr":"<?php echo $config_ary['nonceStr'];?>",
		"signature":"<?php echo $config_ary['signature'];?>"
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

        /*layerGameScore
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
		layerGameScore over*/
		.layerGameScore{position: fixed;width: 100%;height:100%;top:0;left:0;z-index: 109;}
        .layerGameScore .gameScoreBack{width: 100%;height:100%;background: #000;opacity:0.6;  }
		.layerGameScore .mainPart {width: 90vw;height: 80vh;position: absolute; top:50%; left:50%;transform:translate(-50%, -50%); background:rgba(255,255,255,0.1); border-radius:5px; padding:4px 5px; box-shadow: inset 0 0 8px rgba(255,255,255, 0.8);}
		.layerGameScore .mainPart .showPart{ width:100%; height:calc(100% - 40px); background:rgba(255, 244, 220, 0.9); padding-top:40px; border-radius: 5px;   box-shadow: inset 0  -2px 0 0 #b97c56;
		}
		.layerGameScore .mainPart .gameStoreTitle{ width:55vw; height:45px; text-align:center; padding-top:5px; position:absolute; top:0; left:50%; transform:translateX(-50%); border-radius:5px; background-image:url("<?php echo $image_url;?>files/images/common/storetitle.png"); background-size:contain; background-repeat:no-repeat;}
		.layerGameScore .mainPart .gameStoreTitle span{ color:#7D2F00; font-size:5.4vw;   font-weight:bold; position:relative; z-index:10;}
		.layerGameScore .mainPart .gameStoreTitle span::before{ content:attr(data-text); position:absolute; z-index:-1; -webkit-text-stroke:2px white;left:0; }
		.layerGameScore .mainPart .showPart .storeList{height:calc(100% - 60px); overflow-y:scroll; }
		.layerGameScore .mainPart .showPart .storeList .noData{ color:#A8651F; text-align:center;  margin-top:20vh}
		.layerGameScore .mainPart .showPart .storeHeader{ height:34px;background: linear-gradient(to bottom, #DBB272, #F6DFB3); display:flex; font-size:4.5vw;  border-top:1px solid #d9b571;  border-bottom:1px solid #d9b571;}
		.layerGameScore .mainPart .showPart .storeHeader .common{ display:inline-block; flex:1; text-align:center; line-height:34px; text-shadow: -1px 0 #a8651f, 0 1px #a8651f,
		1px 0 #a8651f, 0 -1px #a8651f; color:white; font-size:4vw; }
		.layerGameScore .mainPart .showPart .storeHeader .cardUserName{ flex:2; }
	    .layerGameScore .mainPart .showPart .storeHeader .cardTitle{flex:2; }   
	    .layerGameScore .mainPart .closeImg{ width:10vw; height:10vw; position: absolute; right:-2.5vw; top:-2.5vh; }
		.layerGameScore .mainPart .showPart .storeList .storeRound .roundNum{ height:30px; border-bottom: 1px solid #D9B572; font-size:15px; line-height:31px; color:#A8651F; padding-left:5px;}
		.layerGameScore .mainPart .showPart .storeList .playerMenu{ height:40px; background:#F9E8C6; padding: 0 5px 0 5px; display: flex;border-bottom:1px solid #D9B572;}
		.layerGameScore .mainPart .showPart .storeList .playerMenu .bankerImg{ height:30px; width:30px; vertical-align:middle; }
		.layerGameScore .mainPart .showPart .storeList .playerMenu .realName{ padding-left: 10px; text-align:left; flex: 2;  overflow:hidden; text-overflow:ellipsis; white-space:noWrap; line-height:40px; font-size:4vw; color:#714D29;}
		.layerGameScore .mainPart .showPart .storeList .playerMenu .cardType { flex:2;text-align:left;  overflow:hidden; text-overflow:ellipsis; white-space:noWrap; line-height:40px; font-size:4vw; color:#714D29;}
		.layerGameScore .mainPart .showPart .storeList .playerMenu .getStore{text-align:center; flex: 1;  overflow:hidden; text-overflow:ellipsis; white-space:noWrap;line-height:40px; font-size:4vw; color:#714D29;}
		.layerGameScore .mainPart .showPart .storeList .playerMenu .storeChip{text-align:center; flex: 1;  overflow:hidden; text-overflow:ellipsis; white-space:noWrap;line-height:40px; font-size:4vw; color:#714D29;} 	
		.layerGameScore .mainPart .showPart .storeList .playerMenu .cardType .storeCardType{ display:inline-block; vertical-align:middle; font-size:4vw; color:#714D29; line-height:initial; text-align:right; margin-right:5px;}
		.layerGameScore .mainPart .showPart .storeList .playerMenu .cardType span:nth-child(2){ background-image:url("<?php echo $image_url;?>files/images/common/bullCard.jpg"); background-size:325px 150px; display:inline-block; width:25px;height:30px;  vertical-align:middle; }
		.layerGameScore .mainPart .showPart .storeList .playerMenu .cardType span:nth-child(3){ background-image:url("<?php echo $image_url;?>files/images/common/bullCard.jpg"); background-size:325px 150px; display:inline-block; width:25px;height:30px;  vertical-align:middle;margin-left:-18px; }
		.layerGameScore .mainPart .showPart .storeList .playerMenu .cardType span:nth-child(4){ background-image:url("<?php echo $image_url;?>files/images/common/bullCard.jpg"); background-size:325px 150px; display:inline-block; width:25px;height:30px;  vertical-align:middle; margin-left:-18px;}
		.layerGameScore .mainPart .showPart .storeList .playerMenu .cardType span:nth-child(5){ background-image:url("<?php echo $image_url;?>files/images/common/bullCard.jpg"); background-size:325px 150px; display:inline-block; width:25px;height:30px;  vertical-align:middle; margin-left:-18px;}
		.layerGameScore .mainPart .showPart .storeList .playerMenu .cardType span:nth-child(6){ background-image:url("<?php echo $image_url;?>files/images/common/bullCard.jpg"); background-size:325px 150px; display:inline-block; width:25px;height:30px;  vertical-align:middle; margin-left:-18px;}
	</style>

	<div style="position: fixed;width:100%;height:100%;top:0;left:0;background: #000;z-index:115;" id="loading">
		<div class="load4">
			<div class="loader">Loading...</div>
		</div>
	</div>

	<div class="main" id="app-main" style="position: relative; width: 100%;margin: 0 auto; background: #fff; display: none;">

        <?php if ($broadcast) { ?>
            <div style="position:fixed;width: 100%;color: white;background: rgba(0,0,0,0.5);font-size: 20px;z-index: 100;padding: 10px">
                <div class='marquee' style="float: left;margin-left:35px;width: 100%;overflow: hidden">
                    <?php echo $broadcast; ?>
                </div>
            </div>

            <div style="position: fixed;top: 0vw;z-index: 101;padding: 5px 10px 10px 10px">
                <img style="width: 30px;height: 30px" src="<?php echo $image_url; ?>files/images/common/alert_icon.png" />
            </div>
        <?php } ?>

	    <!-- 顶部房卡数，首页，加人等按钮 -->
		<!-- <div class="roomCard">
			<img  src="<?php echo $image_url;?>files/images/common/ticket.png" />
			<div class="num">
				<div style="position: absolute;top:0;left: 0;width: 100%;height: 100%;background: #fff;opacity: .2;border-radius:10px;"></div>
				<div style="position: relative;padding: 0 10px 0 35px;margin-left:10px;">{{roomCard}}张</div>
			</div>
		</div> -->
		<div class="footPoint">
		    <div><img src="<?php echo $image_url;?>files/images/common/endPoint.png" alt="底分" class="footImg"><span class="baseScore">{{base_score}}分</span></div>
	    </div>
		<div class="round">{{game.round}}&nbsp/&nbsp{{game.total_num}}&nbsp局</div>
		<div class="audienceLook"  @click="showWatch" style="position: fixed;"><img class="lookImg" src="<?php echo $image_url;?>files/images/common/toVisit.png" alt="观战"><span class="lookFont">{{appData.audiences.length}}</span></div>
		<img class="bottom"  src="<?php echo $image_url;?>files/images/bull/toFoot.png"  usemap="#planetmap" />
        <!-- <img class="bottomGamePlay" src="<?php echo $base_url;?>files/images/common/icon_play.png" @click="showGamePlay"> -->
        <img class="bottomGameRule" src="<?php echo $image_url;?>files/images/common/toRule.png" @click="showGameRule">
        <img class="bottomGameAudio" src="<?php echo $image_url;?>files/images/common/toSound.png" @click="showAudioSetting">
        <img class="bottomBackIndex" src="<?php echo $image_url;?>files/images/common/toIndex.png" @click="backHome">
        <!-- <img class="bottomGameWatch" src="<?php echo $image_url;?>files/images/watch/visit.png" @click="showWatch"> -->
        <img class="bottomGameScore" src="<?php echo $base_url;?>files/images/common/toScore.png" @click="showGameScore">
        <img class="bottomGameMessage" src="<?php echo $image_url;?>files/images/common/toChat.png" @click="showMessage">
		<div class="autoReady">
			<img src="<?php echo $image_url;?>files/images/common/tobg2.png" @click="autoReady" v-show="!game.autoReady">
			<img src="<?php echo $image_url;?>files/images/common/tobg1.png" @click="autoReady" v-show="game.autoReady">
		</div>
		<div class="disconnect" v-show="!connectOrNot" style="position: fixed;top:45%;left: 0;width: 100%;text-align: center;z-index: 101"><div style="width: 250px;height:27px;position: absolute;top:-2px;left: 50%;margin-left: -125px;background: #000;opacity: .5;border-radius:15px;"></div><a style="font-size: 16px;color: #fff;padding: 5px 14px;position:relative;">已断开连接，正在重新连接...</a></div>

        <!-- 底部显示底注 -->
		<!-- <div class="myCardType">
		    <div style="font-size: 10pt;">底分：{{base_score}}分</div>
		</div> -->

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

        <!-- 提示框 -->
		<div class="alert" id="valert" v-show="isShowAlert">
			<div class="alertBack" @click="closeAlert"></div>
			<div class="mainPart" style="height:28vh;">
				<div class="backImg">
					<div class="blackImg"></div>
				</div>
				<div class="alertText">{{alertText}}</div>

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
				<div v-show="alertType==9">
					<div class="buttonMiddle" @click="showBreakRoom">确定</div>
				</div>
				<div v-show="alertType==10">
				    <div class="buttonLeft" @click="closeAlert">取消</div>
					<div class="buttonRight" @click="confirmBreakRoom">确定</div>
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

        <!-- 游戏桌子 -->
		<div class="table" id="table" style="position: relative; width: 100%; height: 100%; z-index: 80; overflow: hidden;">
			<img class="tableBack"  src="<?php echo $base_url;?>files/images/sangong/table6.jpg" style="position: absolute;top:0;left: 0;width: 100vw;height: 100vh" />

			<!-- 发牌 ||p.account_status==6||p.account_status==7 -->
			<div class="cardDeal" style="position: absolute; top: 19.5%; left: 19%; width: 62%; height: 55.5%;">
			<img v-if="isAudience" style="width: 160px; position: absolute; top: 150px; left: calc(50% - 80px);" src="<?php echo $image_url;?>files/images/watch/visiting.png" />
			    <!-- p.account_id>0&&p.account_status>2&&p.account_status<8&&(p.num!=1||(p.account_status!=7&&p.account_status!=8&&!player[0].is_showCard))&&(game.cardDeal>0) -->
				<div v-for="p in player" v-if="ruleInfo.banker_mode!=5" v-show="p.account_id>0&&p.account_status>2&&p.account_status<8&&(p.num!=1||(p.account_status!=7&&p.account_status!=8&&!player[0].is_showCard))&&(game.cardDeal>0)">
					<div class="card" :class="'card' + p.num + '1'" style="z-index: 14;" v-show="game.cardDeal>0"></div>
					<div class="card" :class="'card' + p.num + '2'" style="z-index: 13;" v-show="game.cardDeal>1"></div>
					<div class="card" :class="'card' + p.num + '3'" style="z-index: 12;" v-show="game.cardDeal>2"></div>
<!--					<div class="card" :class="'card' + p.num + '4'" style="z-index: 11;" v-show="game.cardDeal>3"></div>-->
<!--					<div class="card" :class="'card' + p.num + '5'" style="z-index: 10;" v-show="game.cardDeal>4"></div>-->
				</div>

				<div v-for="p in player" v-if="ruleInfo.banker_mode==5" v-show="p.account_id>0&&p.account_status>2&&p.account_status<8&&(p.num!=1||(p.account_status!=7&&p.account_status!=8&&!player[0].is_showCard))&&(game.cardDeal>0)">
					<div class="card" :class="'card' + p.num + '1'" style="z-index: 14;" v-show="game.cardDeal>0"></div>
					<div class="card" :class="'card' + p.num + '2'" style="z-index: 13;" v-show="game.cardDeal>1"></div>
					<div class="card" :class="'card' + p.num + '3'" style="z-index: 12;" v-show="game.cardDeal>2"></div>
<!--					<div class="card" :class="'card' + p.num + '4'" style="z-index: 11;" v-show="game.cardDeal>3"></div>-->
<!--					<div class="card" :class="'card' + p.num + '5'" style="z-index: 10;" v-show="game.cardDeal>4"></div>-->
				</div>

                <!-- 玩家1发完牌之后  -->
				<div class="myCards" v-show="player[0].is_showCard&&player[0].account_status>2&&player[0].account_status<=7&&game.show_card">
                    <div class="cards3D">
                        <div class="cards card0" v-show="player[0].account_status >2 && player[0].account_status <= 7">
                            <div class="face front" ></div>
                            <div class="face back" :class="'card' + player[0].card[0]"></div>
                        </div>

                        <div class="cards card1" v-show="player[0].account_status >2 && player[0].account_status <= 7">
                            <div class="face front" ></div>
                            <div class="face back" :class="'card' + player[0].card[1]"></div>
                        </div>

                        <div class="cards card2" v-show="player[0].account_status >2 && player[0].account_status <= 7">
                            <div class="face front" @click="seeMyCard2"></div>
                            <div class="face back" :class="'card' + player[0].card[2]"></div>
                        </div>
					</div>
				</div>

                <!-- 玩家1摊牌看牌  -->
				<div class="myCards" v-show="player[0].is_showCard&&player[0].account_status==8&&game.show_card" >
					<div class="cards card00">
						<div class="face back" :class="'card' + player[0].card[0]" style="-webkit-transform: rotateY(0deg);"></div>
					</div>

					<div class="cards card01">
						<div class="face back" :class="'card' + player[0].card[1]" style="-webkit-transform: rotateY(0deg);"></div>
					</div>

					<div class="cards card02">
						<div class="face back" :class="'card' + player[0].card[2]" style="-webkit-transform: rotateY(0deg);"></div>
					</div>
				</div>
			</div>

			<!-- game.cardDeal==-1&&p.account_status>1&&p.account_status!=6&&p.account_status!=7&&p.card.length>0-->

			<div class="cardOver" style="position: absolute; top: 19.5%; left: 19%; width: 62%; height: 55.5%; overflow:hidden;">
				<div v-for="p in player" v-if="p.num!=1" v-show="p.account_status>=8&&p.card.length>0&&game.show_card">
					<div class="cards" :class="'card' + p.num + ' card' + p.num + '11'" style="z-index: 14;">
						<div class="face front"></div>
						<div class="face back" :class="'card' + p.card[2]"></div>
					</div>

					<div class="cards" :class="'card' + p.num + ' card' + p.num + '21'" style="z-index: 13">
						<div class="face front"></div>
						<div class="face back" :class="'card' + p.card[1]"></div>
					</div>

					<div class="cards" :class="'card' + p.num + ' card' + p.num + '31'" style="z-index: 12">
						<div class="face front"></div>
						<div class="face back" :class="'card' + p.card[0]"></div>
					</div>
				</div>
			</div>

			<div v-for="p in player" v-if="p.num!=1&&ruleInfo.banker_mode!=2" :class="{true:'memberGoText' + p.num,false:'memberRobText' + p.num}[ruleInfo.banker_mode==5]" v-show="p.account_status==4||p.account_status==5">
				<img :src="p.robImg" style="position: absolute; top: 0px; left: 0px; width: 30px; height:16px">
			</div>

			<div v-for="p in player" v-if="p.num!=1&&ruleInfo.banker_mode==2" :class="'memberFreeRobText' + p.num" v-show="p.account_status==4||p.account_status==5">
				<img :src="p.robImg" style="position: absolute; top: 0px; left: 0px; width: 30px; height:16px">
			</div>

			<div v-for="p in player" v-if="p.num!=1&&ruleInfo.banker_mode==2" :class="'memberGoTimesText' + p.num" v-show="p.account_status==5&&p.bankerMultiples>=1">
				<img :src="p.bankerTimesImg" style="position: absolute; top: 0px; left: 0px; width: 15px; height:15px">
			</div>

            <!-- 倍数文字图片 -->
            <div class="memberTimesText1" v-if="ruleInfo.banker_mode!=4" v-show="player[0].account_status>=6&&game.show_card&&!player[0].is_banker&&player[0].multiples>0">
            	<img :src="player[0].timesImg" style="position: absolute; top: 0px; left: 0px; width: 20px; height:20px">
            </div>
			<div v-for="p in player" v-if="p.num!=1&&ruleInfo.banker_mode!=4" :class="'memberTimesText' + p.num" v-show="p.account_status>=6&&game.show_card&&!p.is_banker&&p.multiples>0">
			    <img :src="p.timesImg" style="position: absolute; top: 0px; left: 0px; width: 18px; height:18px">
			</div>

            <!-- 庄家倍数文字图片 -->
			<div class="memberTimesText1" v-if="ruleInfo.banker_mode==2" v-show="player[0].account_status>=5&&game.show_card&&player[0].is_banker&&player[0].bankerMultiples>0">
            	<img :src="player[0].bankerTimesImg" style="position: absolute; top: 0px; left: 0px; width: 20px; height:20px">
            </div>
			<div v-for="p in player" v-if="p.num!=1&&ruleInfo.banker_mode==2" :class="'memberTimesText' + p.num" v-show="p.account_status>=5&&game.show_card&&p.is_banker&&p.bankerMultiples>0">
			    <img :src="p.bankerTimesImg" style="position: absolute; top: 0px; left: 0px; width: 18px; height:18px">
			</div>

            <!-- 牛几图片 -->
			<div v-for="p in player"  :class="'memberBull' + p.num" v-show="p.account_status==8&&game.show_card&&p.bullImg.length>=1">
			    <img :src="p.bullImg" style="position: absolute; top: 0px; left: 0px; width: 100%; height:100%">
			</div>

            <!-- 每局得分 -->

            <!-- 玩家1得分 -->
            <div  class="memberScoreText1" v-show="game.show_score" style="display: none;">
			    <label v-show="player[0].single_score<0&&player[0].account_status>=8"  style="position: absolute; line-height: 40px; width: 100%; height: 40px; text-align: center;color: white; font-size: 16pt;;font-family:'Helvetica 微软雅黑'; ::-webkit-text-stroke-width-width: 1.3px;text-shadow: 1px 2px 2px #022055; ">{{player[0].single_score}}</label>
			    <label v-show="player[0].single_score>0&&player[0].account_status>=8"  style="position: absolute; line-height: 40px; width: 100%; height: 40px; text-align: center;color: rgb(234,171,55); font-size: 16pt;font-family:'Helvetica 微软雅黑'; :-webkit-text-stroke-width: 1.3px;text-shadow: 1px 2px 2px #782a00;">+{{player[0].single_score}}</label>
			    <label v-show="player[0].single_score==0&&player[0].account_status>=8"  style="position: absolute; line-height: 40px; width: 100%; height: 40px; text-align: center;color: rgb(234,171,55); font-size: 16pt;font-family:'Helvetica 微软雅黑'; :-webkit-text-stroke-width: 1.3px;text-shadow: 1px 2px 2px #782a00;">0</label>
			</div>

			<div v-for="p in player"  v-if="p.num==2||p.num==3" :class="'memberScoreText' + p.num" v-show="game.show_score" style="display: none;">
			    <p v-show="p.single_score<0&&p.account_status>=8" style="position: absolute; line-height: 40px; width:100%;height: 40px; text-align: right;color: white; font-size: 16pt;font-family:'Helvetica 微软雅黑'; :-webkit-text-stroke-width: 1.3px;text-shadow: 1px 2px 2px #022055; ">{{p.single_score}}</p>
			    <p v-show="p.single_score>0&&p.account_status>=8" style="position: absolute; line-height: 40px; width:100%;height: 40px; text-align: right;color: rgb(234,171,55); font-size: 16pt;font-family:'Helvetica 微软雅黑'; :-webkit-text-stroke-width: 1.3px;text-shadow: 1px 2px 2px #782a00;">+{{p.single_score}}</p>
			    <p v-show="p.single_score==0&&p.account_status>=8" style="position: absolute; line-height: 40px; width:100%;height: 40px; text-align: right;color: rgb(234,171,55); font-size: 16pt;;font-family:'Helvetica 微软雅黑'; :-webkit-text-stroke-width: 1.3px;text-shadow: 1px 2px 2px #782a00; ">0</p>
			</div>

			<div v-for="p in player"  v-if="p.num==5||p.num==6" :class="'memberScoreText' + p.num" v-show="game.show_score" style="display: none;">
			    <label v-show="p.single_score<0&&p.account_status>=8" style="position: absolute; line-height: 40px; height: 40px; text-align: left;color: white; font-size: 16pt;font-family:'Helvetica 微软雅黑'; :-webkit-text-stroke-width: 1.3px;text-shadow: 1px 2px 2px #022055; ">{{p.single_score}}</label>
			    <label v-show="p.single_score>0&&p.account_status>=8" style="position: absolute; line-height: 40px; height: 40px; text-align: left;color: rgb(234,171,55); font-size: 16pt;font-family:'Helvetica 微软雅黑'; :-webkit-text-stroke-width: 1.3px;text-shadow: 1px 2px 2px #782a00;">+{{p.single_score}}</label>
			    <label v-show="p.single_score==0&&p.account_status>=8" style="position: absolute; line-height: 40px; height: 40px; text-align: left;color: rgb(234,171,55); font-size: 16pt;;font-family:'Helvetica 微软雅黑'; :-webkit-text-stroke-width: 1.3px;text-shadow: 1px 2px 2px #782a00;">0</label>
			</div>

			<!-- 玩家4得分 -->
			<div  class="memberScoreText4" style="display: none;" v-show="game.show_score">
			    <label v-show="player[3].single_score<0&&player[3].account_status>=8" style="position: absolute; line-height: 40px; width: 100%; height: 40px; text-align: center; color: white; font-size: 16pt;font-family:'Helvetica 微软雅黑'; :-webkit-text-stroke-width: 1.3px;text-shadow: 1px 2px 2px #022055; ">{{player[3].single_score}}</label>
			    <label v-show="player[3].single_score>0&&player[3].account_status>=8"  style="position: absolute; line-height: 40px; width: 100%; height: 40px; text-align: center; color: rgb(234,171,55); font-size: 16pt;font-family:'Helvetica 微软雅黑'; :-webkit-text-stroke-width: 1.3px;text-shadow: 1px 2px 2px #782a00;">+{{player[3].single_score}}</label>
			    <label v-show="player[3].single_score==0&&player[3].account_status>=8" style="position: absolute; line-height: 40px; width: 100%; height: 40px; text-align: center; color: rgb(234,171,55); font-size: 16pt;font-family:'Helvetica 微软雅黑'; :-webkit-text-stroke-width: 1.3px;text-shadow: 1px 2px 2px #782a00;">0</label>
			</div>


			<!-- 玩家  -->
			<div  v-for="p in player" key="p.num" class="member" :class="'member' + p.num" v-show="p.account_id>0" >
			    <!-- 玩家背景框 -->
				<img src="<?php echo $image_url;?>files/images/common/player_bg.png" class="background">
                <img src="<?php echo $image_url;?>files/images/common/player_selected.png" class="background" v-show="p.is_banker">

                <!-- 玩家昵称 -->
				<div class="title">{{p.nickname}}</div>
				<!-- 玩家头像 -->
                <div class="avatar">
                    <img src="<?php echo $image_url;?>files/images/watch/watching.png" class="watching" v-show="p.account_status==9">
                    <img :src="p.headimgurl" class="headImg">
                </div>
				<!-- 玩家积分 -->
				<div class="score">{{p.account_score}}</div>

				<img :id="'banker' + p.account_id" src="<?php echo $image_url;?>files/images/bull/banker_bg.png" class="background" style="position: absolute; top: 10%;left: 10%;width: 80%; height: 81%; display: none;" />

				<img src="<?php echo $image_url;?>files/images/bull/banker_icon.png" class="background" style="position: absolute; top: 3%;left: 3%;width: 18px; height: 18px;" v-show="p.is_banker">

				<img :id="'bankerAnimate' + p.num" src="<?php echo $image_url;?>files/images/bull/banker_animate.png" style="position: absolute; top: 10%; left: 10%; width: 80%; height: 80%; display: none;">
				<img :id="'bankerAnimate1' + p.num" src="<?php echo $image_url;?>files/images/bull/banker_animate.png" style="position: absolute; top: 10%; left: 10%; width: 80%; height: 80%; display: none;">

				<!-- 玩家离开或不在线 -->
                <div class="quitBack" v-show="p.num>1&&p.online_status==0" ></div>

                <!-- 准备状态 game.round!=game.total_num-->
				<div class="isReady" v-show="game.round!=game.total_num">
					<img src="<?php echo $image_url;?>files/images/common/ready_button.png" class="unready" v-show="(p.account_status==1||p.account_status==0)&&p.num==1&&game.status==1&&ruleInfo.banker_mode!=5" @click="imReady" />
					<img src="<?php echo $image_url;?>files/images/common/ready_button.png" class="unready" v-show="(p.account_status==1||p.account_status==0)&&p.num==1&&game.status==1&&ruleInfo.banker_mode==5&&canBreak!=1" @click="imReady" />
					<img src="<?php echo $image_url;?>files/images/common/ready_text.png" class="ready" v-show="p.account_status==2" />
				</div>
			</div>

            <!-- 玩家金币 -->
            <div id="playerCoins" style="display: none;">
            	<div v-for='p in player'>
            		<div v-for='coin in p.coins' class="memberCoin" :class="coin">
            			<img src="<?php echo $image_url;?>files/images/common/coin.png" style="position: absolute; width: 100%; height: 100%">
            		</div>
            	</div>
            </div>

            <!-- 显示玩家消息文本 -->
			<div v-for="p in player">
				<div class="messageSay" :class="'messageSay' + p.num" v-show="p.messageOn">
					<div>{{p.messageText}}</div>
					<div class="triangle"> </div>
				</div>
			</div>

            <!-- 游戏状态:选择抢庄 player[0].account_status==3 -->
            <div id="divRobBankerText" v-show="showClockRobText&&ruleInfo.banker_mode!=4" :style="'position: absolute;top: 33%; left: 0px; width:' + width + 'px; height: 30px;'">
            	<p style="color: white; font-size: 10pt;width: 100%;height: 30px; line-height: 30px; text-align: center;font-family:'Helvetica 微软雅黑';">{{ruleInfo.bankerText}}</p>
            </div>
            <!-- 游戏状态:闲家下注 player[0].account_status==6&&game.show_coin -->
            <div id="divBetText" v-show="showClockBetText" :style="'position: absolute;top: 33%; left: 0px; width:' + width + 'px; height: 30px;'">
            	<p style="color: white; font-size: 10pt; width: 100%;height: 30px; line-height: 30px; text-align: center;font-family:'Helvetica 微软雅黑';">闲家下注</p>
            </div>
            <!-- 游戏状态:等待摊牌 -->
            <div id="divBetText" v-show="showClockShowCard" :style="'position: absolute;top: 33.5%; left: 0px; width:' + width + 'px; height: 30px;'">
            	<p style="color: white; font-size: 10pt; width: 100%;height: 30px; line-height: 30px; text-align: center;font-family:'Helvetica 微软雅黑';">等待摊牌</p>
            </div>

            <!-- 倒计时时钟 position: absolute; top: 38%; left: 44%; width: 40px; height: 42px; -->
			<div id="" class="clock" v-show="game.time>-1" :style="'position: absolute; top: 38%; left:' + (width - 40) / 2 + 'px; width: 40px; height: 42px;'">
			    <img src="<?php echo $image_url;?>files/images/common/clock.png" style="position: absolute; width: 100%; height: 100%;" />
			    <p style="position: absolute; top: 0px; left: 0px; width: 40px; height: 42px; color: white; text-align: center; line-height: 42px;">
			        {{game.time}}
			    </p>
			</div>

            <!-- 按钮操作区域 -->
            <div id="operationButton" :style="viewStyle.button">

                <!-- 抢庄 -->
            	<div v-show="showRob&&ruleInfo.banker_mode==1" @click="robBanker(1)" :style="viewStyle.rob">
            		<img src="<?php echo $image_url;?>files/images/bull/button_orange.png" style="position: absolute; top: 0px; left: 0px; width: 100%; height: 100%">
            		<div :style="viewStyle.rob1">抢庄</div>
            	</div>

                <!-- 不抢 -->
            	<div v-show="showRob&&ruleInfo.banker_mode==1" @click="notRobBanker" :style="viewStyle.notRob">
            		<img src="<?php echo $image_url;?>files/images/bull/button_blue.png" style="position: absolute; top: 0px; left: 0px; width: 100%; height: 100%">
            		<div :style="viewStyle.notRob1">不抢</div>
            	</div>

            	<!-- 上庄 -->
            	<div v-show="showRob&&ruleInfo.banker_mode==5&&game.round==1" @click="robBanker(1)" :style="viewStyle.rob">
            		<img src="<?php echo $image_url;?>files/images/bull/button_orange.png" style="position: absolute; top: 0px; left: 0px; width: 100%; height: 100%">
            		<div :style="viewStyle.rob1">上庄</div>
            	</div>

                <!-- 不上庄 -->
            	<div v-show="showRob&&ruleInfo.banker_mode==5&&game.round==1" @click="notRobBanker" :style="viewStyle.notRob">
            		<img src="<?php echo $image_url;?>files/images/bull/button_blue.png" style="position: absolute; top: 0px; left: 0px; width: 100%; height: 100%">
            		<div :style="viewStyle.notRob1">不上庄</div>
            	</div>

            	<!-- 准备 -->
            	<div v-show="(player[0].account_status==1||player[0].account_status==0)&&player[0].num==1&&ruleInfo.banker_mode==5&&canBreak==1&&game.round!=game.total_num&&player[0].is_banker" @click="clickGameOver" :style="viewStyle.rob">
            		<img src="<?php echo $image_url;?>files/images/bull/button_blue.png" style="position: absolute; top: 0px; left: 0px; width: 100%; height: 100%">
            		<div :style="viewStyle.notRob1">下庄</div>
            	</div>

                <!-- 下庄 -->
            	<div v-show="(player[0].account_status==1||player[0].account_status==0)&&player[0].num==1&&ruleInfo.banker_mode==5&&canBreak==1&&game.round!=game.total_num&&player[0].is_banker" @click="imReady"  :style="viewStyle.notRob">
            		<img src="<?php echo $image_url;?>files/images/bull/button_orange.png" style="position: absolute; top: 0px; left: 0px; width: 100%; height: 100%">
            		<div :style="viewStyle.rob1">准备</div>
            	</div>

            	<!-- 1倍 -->
            	<div class="divCoin" v-show="showRob&&ruleInfo.banker_mode==2" @click="robBanker(1)" :style="viewStyle.times1">
            		<img src="<?php echo $image_url;?>files/images/bull/button_times.png" style="position: absolute; top: 0px; left: 0px; width: 100%; height: 100%">
            		<div :style="viewStyle.timesText">1倍</div>
            	</div>

                <!-- 2倍 -->
            	<div class="divCoin" v-show="showRob&&ruleInfo.banker_mode==2" @click="robBanker(2)" :style="viewStyle.times2">
            		<img src="<?php echo $image_url;?>files/images/bull/button_times.png" style="position: absolute; top: 0px; left: 0px; width: 100%; height: 100%">
            		<div :style="viewStyle.timesText">2倍</div>
            	</div>

                <!-- 4倍 -->
            	<div class="divCoin" v-show="showRob&&ruleInfo.banker_mode==2" @click="robBanker(4)" :style="viewStyle.times3">
            		<img src="<?php echo $image_url;?>files/images/bull/button_times.png" style="position: absolute; top: 0px; left: 0px; width: 100%; height: 100%">
            		<div :style="viewStyle.timesText">4倍</div>
            	</div>

                <!-- 不抢 -->
            	<div class="divCoin" v-show="showRob&&ruleInfo.banker_mode==2" @click="notRobBanker" :style="viewStyle.times4">
            		<img src="<?php echo $image_url;?>files/images/bull/button_times_blue.png" style="position: absolute; top: 0px; left: 0px; width: 100%; height: 100%">
            		<div :style="viewStyle.timesText">不抢</div>
            	</div>

                <!-- 摊牌 -->
            	<div id="showShowCardButton" v-show="showShowCardButton" @click="showCard" :style="viewStyle.showCard">
            		<img src="<?php echo $image_url;?>files/images/bull/button_blue.png" style="position: absolute; top: 0px; left: 0px; width: 100%; height: 100%">
            		<div :style="viewStyle.showCard1">摊牌</div>
            	</div>

                <!-- 1倍 -->
            	<div class="divCoin" v-show="showTimesCoin&&!player[0].multiples>0 && !player[0].is_banker&&player[0].account_status>=6" @click="selectTimesCoin(1)" :style="viewStyle.times1">
            		<img src="<?php echo $image_url;?>files/images/bull/button_times.png" style="position: absolute; top: 0px; left: 0px; width: 100%; height: 100%">
            		<div :style="viewStyle.timesText">1倍</div>
            	</div>

                <!-- 2倍 -->
            	<div class="divCoin" v-show="showTimesCoin&&!player[0].multiples>0&& !player[0].is_banker&&player[0].account_status>=6" @click="selectTimesCoin(2)" :style="viewStyle.times2">
            		<img src="<?php echo $image_url;?>files/images/bull/button_times.png" style="position: absolute; top: 0px; left: 0px; width: 100%; height: 100%">
            		<div :style="viewStyle.timesText">2倍</div>
            	</div>

                <!-- 4倍 -->
            	<div class="divCoin" v-show="showTimesCoin&&!player[0].multiples>0&& !player[0].is_banker&&player[0].account_status>=6" @click="selectTimesCoin(4)" :style="viewStyle.times3">
            		<img src="<?php echo $image_url;?>files/images/bull/button_times.png" style="position: absolute; top: 0px; left: 0px; width: 100%; height: 100%">
            		<div :style="viewStyle.timesText">4倍</div>
            	</div>

                <!-- 5倍 -->
            	<div class="divCoin" v-show="showTimesCoin&&!player[0].multiples>0&& !player[0].is_banker&&player[0].account_status>=6" @click="selectTimesCoin(5)" :style="viewStyle.times4">
            		<img src="<?php echo $image_url;?>files/images/bull/button_times.png" style="position: absolute; top: 0px; left: 0px; width: 100%; height: 100%">
            		<div :style="viewStyle.timesText">5倍</div>
            	</div>

                <!-- 抢庄文字 -->
            	<div v-if="showRobText&&ruleInfo.banker_mode!=5&&ruleInfo.banker_mode!=2" :style="viewStyle.robText" >
            		<img src="<?php echo $image_url;?>files/images/bull/text_rob.png" style="position: absolute; top: 0px; left: 0px; width: 100%; height: 100%">
            	</div>

            	<div v-if="showRobText&&ruleInfo.banker_mode==2" :style="viewStyle.robText2" >
            		<img src="<?php echo $image_url;?>files/images/bull/text_rob.png" style="position: absolute; top: 0px; left: 0px; width: 100%; height: 100%">
            	</div>

            	<div v-if="showRobText&&ruleInfo.banker_mode==2&&player[0].bankerMultiples>0" :style="viewStyle.robTimesText" >
            		<img :src="player[0].bankerTimesImg" style="position: absolute; top: 0px; left: 0px; width: 100%; height: 100%">
            	</div>

                <!-- 不抢庄文字 -->
            	<div v-if="showNotRobText&&ruleInfo.banker_mode!=5" :style="viewStyle.notRobText">
            		<img src="<?php echo $image_url;?>files/images/bull/text_notrob.png" style="position: absolute; top: 0px; left: 0px; width: 100%; height: 100%">
            	</div>

            	<!-- 上庄文字 -->
            	<div v-if="showRobText&&ruleInfo.banker_mode==5&&game.round<2" :style="viewStyle.robText" >
            		<img src="<?php echo $image_url;?>files/images/bull/text_go.png" style="position: absolute; top: 0px; left: 0px; width: 100%; height: 100%">
            	</div>

                <!-- 不上庄文字 -->
            	<div v-if="showNotRobText&&ruleInfo.banker_mode==5&&game.round<2" :style="viewStyle.notRobText">
            		<img src="<?php echo $image_url;?>files/images/bull/text_notgo.png" style="position: absolute; top: 0px; left: 0px; width: 100%; height: 100%">
            	</div>

            	<!-- 等待玩家下注 -->
            	<div v-show="showBankerCoinText" :style="viewStyle.coinText">
            		<p :style="viewStyle.coinText1">等待闲家下注</p>
            	</div>

            	<!-- 点击看牌-->
            	<div v-show="showClickShowCard" :style="viewStyle.showCardText">
            		<p :style="viewStyle.showCardText1">点击牌面看牌</p>
            	</div>
            </div>
		</div>

        <div class="ranking hideRanking" id="ranking" style="z-index: 110">
			<div class="rankBack">
				<img   src="<?php echo $image_url;?>files/images/sangong/rank_bg.png" style="position: absolute;top: 0;left: 0;width: 100%">
			</div>

			<div class="rankText" style="position: absolute;top: 4%;">
				<img   src="<?php echo $image_url;?>files/images/sangong/rank_frame.png" style="position: absolute;top: 0%;left: 25vw;width: 150vw; height:300vw;">
				<div class="time" v-show="playerBoard.round>0" style="position: absolute;top: 48vw;width: 100%;">
					<a style="background-color: rgba(251, 240, 214, 0.6);font-size: 6vw;">房间号:{{game.room_number}}&nbsp&nbsp&nbsp&nbsp{{playerBoard.record}}&nbsp&nbsp&nbsp&nbsp{{game.total_num}}局</a>
				</div>
				<div style="height: 68vw;"></div>
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

		<!-- 消息  -->
		<div class="message" v-show="isShowMessage" >
        	<div class="messageBack" @click="hideMessage"></div>
        	<div class="textPartOuter"></div>
        	<div id="message-box" class="textPart" :style="'height: ' + 0.39 * height + 'px;'">
        		<!-- <div class="outline"></div> -->
        		<div id="scroll-box" class="textList" style="width: 100%;">
        			<div class="textItem" v-for="m in message" @click="messageOn(m.num)">{{m.text}}</div>
        			<!-- <div class="textItem" style="height: 5px;background: #434547;"></div> -->
        		</div>
        	</div>
        </div>

		<!-- end图片  -->
		<div id="endCreateRoom" class="end" style="position: fixed;width: 100%;height:100%;top:0;left:0;z-index: 120;display: none;">
			<img src="" style="width: 100vw;position: absolute;top:0;left: 0;height: 100vh;" id="end"  usemap="#planetmap1" />
			<a href="/f/ym" style="position: absolute;top:10px;display: block;width:34vw;height:11vw;margin-right: 10%;left: 10px" >
			<img src="<?php echo $image_url;?>files/images/common/back.png" style="width:12vw;"></img>
	    </a>
		</div>

        <img class="play-rule-img" id="playRuleImg" v-show="playRule.isShow" @click="cancelGamePlay" src="<?php echo $base_url;?>/files/images/sangong/ruleImg.png" alt="">

        <!-- 积分数据-->
        <div class="layerGameScore" id="vGameScore" v-show="scoreInfo.isShow" @click="cancelGameScore">
		<div class="gameScoreBack"></div>
        <div class="mainPart">
            <div class="showPart">
				<div class="storeHeader">
					<span class="common cardUserName">用户名字</span>
					<span class="common cardTitle">牌型</span>
					<span class="common">倍数</span>
					<span class="common">得分</span>
				</div>
				<div class="storeList">
					<div v-if="appData.storeList.length ===0" class="noData">暂无数据</div>
					<div v-else-if="appData.storeList.length !== 0" class="storeRound" v-for="round in appData.storeList" :key="round['game_num']">
						<div class="roundNum">{{round['game_num']+'/'+round['total_num']}}</div>
						<div class="playerMenu" v-for="player in round.players" :key="player.name">
							<span class="realName common">
							<img v-if="player.is_banker === '1'" class="bankerImg" src="<?php echo $image_url;?>files/images/common/banker.png" alt="庄家图">
								{{player.name}}
							</span>
							<span class="cardType common">
							    <span class="storeCardType">{{player.card_text}}<br/>x{{player.card_times}}</span>
								<span v-for="card in player.cards" class="storeCard" :style="{backgroundPosition: card.x+'px'+ ' ' +card.y+'px',}"></span>
							</span>
							<span class="storeChip common">{{player.mutiple}}倍</span>
							<span class="getStore common">{{player.score}}</span>
						</div>
					</div>
				</div>
			</div>
			<div class="gameStoreTitle">
				<span data-text='六人三公战绩'>六人三公战绩</span>
			</div>
			<img src="<?php echo $image_url;?>files/images/common/closeStore.png" alt="关闭" class="closeImg"  @click="cancelGameScore">
        </div>
        </div>

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

                    <div class="selectPart" v-if="ruleInfo.isJoker==1||ruleInfo.jsBj==1" style="line-height:4vh;padding:0.8vh 0;overflow: hidden;">
                        <div class="selectTitle">规则：</div>
                        <div class="selectList">
                            <div class="selectItem" style="margin-left:10px; height: 4vh" v-if="ruleInfo.isJoker==1">
                                <div class="selectText" >天公x10-雷公x7-地公x5</div>
                            </div>
                            <div class="selectItem" style="margin-left:10px; height: 4vh" v-if="ruleInfo.isBj==1">
                                <div class="selectText" >暴玖x9</div>
                            </div>
                        </div>
                    </div>

                    <div class="selectPart" style="height:4vh;line-height:4vh;padding:0.8vh 0;">
                        <div class="selectTitle">局数：</div>
                        <div class="selectList">
                            <div class="selectItem" style="margin-left:10px;" v-if="ruleInfo.ticket==1">
                                <div class="selectText" >10局X1房卡</div>
                            </div>
                            <div class="selectItem" style="margin-left:10px;" v-if="ruleInfo.ticket==2">
                                <div class="selectText" >20局X2房卡</div>
                            </div>
                        </div>
                    </div>
                </div>
                <div class="operate">
                    <img class="leftBtn" @click="chooseJoin" src="<?php echo $image_url;?>files/images/watch/joinGame.png" />
                    <img class="rightBtn" @click="chooseWatch" src="<?php echo $image_url;?>files/images/watch/joinWatch.png" />
                </div>
            </div>
        </div>

		<!-- 游戏规则 -->
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
                    <!-- <div class="selectPart" style="top: 0px;height:4vh;line-height:4.1vh;">
						<div class="selectTitle" style="width: 100%;font-size: 2vh; text-align: center;color: #7dd9ff; background-color: #143948;opacity: 1.0">创建房间,游戏未进行,不消耗房卡</div>
					</div> -->

                    <div class="selectPart" style="height:4vh;line-height:4vh;padding:0.8vh 0;">
						<div class="selectTitle">模式：</div>
						<div class="selectList">
							<div class="selectItem" style="margin-left:10px;" v-if="ruleInfo.banker_mode==1">
								<div class="selectText" >自由抢庄</div>
							</div>
							<div class="selectItem" style="margin-left:10px;" v-if="ruleInfo.banker_mode==2">
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

					<div class="selectPart" v-if="ruleInfo.isJoker==1||ruleInfo.jsBj==1" style="line-height:4vh;padding:0.8vh 0;overflow: hidden;">
						<div class="selectTitle">规则：</div>
						<div class="selectList">
							<div class="selectItem" style="margin-left:10px; height: 4vh" v-if="ruleInfo.isJoker==1">
								<div class="selectText" >天公x10-雷公x7-地公x5</div>
							</div>
							<div class="selectItem" style="margin-left:10px; height: 4vh" v-if="ruleInfo.isBj==1">
								<div class="selectText" >暴玖x9</div>
							</div>
						</div>
					</div>

					<div class="selectPart" style="height:4vh;line-height:4vh;padding:0.8vh 0;">
						<div class="selectTitle">局数：</div>
						<div class="selectList">
							<div class="selectItem" style="margin-left:10px;" v-if="ruleInfo.ticket==1">
								<div class="selectText" >10局X1房卡</div>
							</div>
							<div class="selectItem" style="margin-left:10px;" v-if="ruleInfo.ticket==2">
								<div class="selectText" >20局X2房卡</div>
							</div>
						</div>
					</div>

                <!-- <div class="createCommit" @click="cancelGameRule" >确定</div> -->

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

		<script type="text/javascript" src="<?php echo $image_url;?>files/js/canvas_old.js"></script>
	</div>

</body>

<script src="https://res.wx.qq.com/open/js/jweixin-1.0.0.js" ></script>
<script type="text/javascript" src="<?php echo $image_url;?>files/js/jquery-1.9.1.min.js"></script>
<script type="text/javascript" src="<?php echo $image_url;?>files/js/bscroll.js"></script>
<script type="text/javascript" src="<?php echo $image_url;?>files/js/vue.min.js" ></script>
<script type="text/javascript" src="<?php echo $image_url;?>files/js/vue-resource.min.js" ></script>
<script type="text/javascript" src="<?php echo $base_url;?>files/js/sg.js?_version=<?php echo $front_version;?>"></script>

<script type="text/javascript" src="<?php echo $image_url;?>files/js/jquery.marquee.min.js"></script>
<script type="application/javascript">
    $('.marquee').marquee({
        duration: 5000,
        delayBeforeStart: 0,
        direction: 'left',
    });
</script>

</html>
