<html>
<head>
<meta charset="utf-8" >
<meta name="viewport" content="width=device-width,initial-scale=1.0, minimum-scale=1.0, maximum-scale=1.0, user-scalable=no"/>
<meta name="format-detection" content="telephone=no" />
<meta name="msapplication-tap-highlight" content="no" />
<title>房间<?php echo $room_number;?></title>

<link rel="stylesheet" type="text/css" href="<?php echo $base_url;?>files/css/vflower6.css">
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
        .layerGameScore .mainPart {width: 230px;height: 305px;top: 44%;left: 58%;margin-top: -165px;margin-left: -150px;position: absolute;}
        .layerGameScore .mainPart .createB{width: 100%;height:100%;top:0%;left:0%;position: absolute;background:#634fa6;border:1px solid #a684f2;border-radius:10px; }
        .layerGameScore .mainPart .createTitle{position:relative;height:36px;text-align: center;}
        .layerGameScore .mainPart .createTitle img{position:relative;height:20px;margin-top: 8px;}
        .layerGameScore .mainPart .cancelCreate{width: 36px;height:36px;top:-16px;right:-16px;position: absolute;}
        .layerGameScore .mainPart .blueBack {width: 200px;height: 235px;background: #111431;border: 1px solid #a684f2;border-radius: 4px;margin: 0 auto;position: relative;}
        .layerGameScore .mainPart .blueBack .selectPart{width:100%;margin-top:4px;line-height:36px;font-size:14px;position: relative;color:#111431;background:#bbbff1;border-radius:0px;font-family:simHei; }
        .layerGameScore .mainPart .blueBack .selectPart .selectTitle{width: 100% !important; text-indent: 15px;text-align: left;}

        .bottomGameScore{position: fixed;bottom:5;right: 70px;width: 30px; height: 30px;z-index:90;}
        /*layerGameScore over*/
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

	<div class="roomCard">
		<img  src="<?php echo $image_url;?>files/images/common/ticket.png" />
		<div class="num">
			<div style="position: absolute;top:0;left: 0;width: 100%;height: 100%;background: #fff;opacity: .2;border-radius:10px;"></div>
			<div style="position: relative;padding: 0 10px 0 35px;">{{roomCard}}张</div>
		</div>
	</div>

	<div class="round">{{game.round}}&nbsp/&nbsp{{game.total_num}}&nbsp局</div>

	<div class="disconnect" v-show="!connectOrNot" style="position: fixed;top:45%;left: 0;width: 100%;text-align: center;z-index: 101">
		<div style="width: 250px;height:27px;position: absolute;top:-2;left: 50%;margin-left: -125px;background: #000;opacity: .5;border-radius:15px;">
		</div>
		<a style="font-size: 16px;color: #fff;padding: 5px 14px;position:relative;">已断开连接，正在重新连接...</a>
	</div>

	<img class="bottom"  src="<?php echo $image_url;?>files/images/flower/footer.png"  usemap="#planetmap" />
    <img class="bottomGameScore" src="<?php echo $base_url;?>files/images/common/icon_score.png" v-on:click="showGameScore" style="z-index: 110">
    <img class="bottomGameRule" src="<?php echo $image_url;?>files/images/common/icon_rule.png" v-on:click="showGameRule" style="z-index: 110">
	<img class="bottomGameHistory" src="<?php echo $image_url;?>files/images/common/icon_sound.png" v-on:click="showAudioSetting" style="z-index: 110">
	<img class="bottomGameMessage" src="<?php echo $image_url;?>files/images/common/icon_message.png" v-on:click="showMessage">

	<div class="myCardType">
		<div v-show="player[0].card_type==1">高牌</div>
		<div v-show="player[0].card_type==2">对子</div>
		<div v-show="player[0].card_type==3">顺子</div>
		<div v-show="player[0].card_type==4">同花</div>
		<div v-show="player[0].card_type==5">同花顺</div>
		<div v-show="player[0].card_type==6">三条</div>
	</div>

	<div  class="table" id="table">
		<img class="tableBack"  src="<?php echo $image_url;?>files/images/flower/table.jpg" />

        <!-- 筹码  -->
        <div class="place" v-show="game.status==2">
        	<div class="totalScore" >
        		<div class="scores">{{game.score}}</div>
        		<img src="<?php echo $image_url;?>files/images/flower/totalScore.png" />
        	</div>
        	<div class="scoresArea"></div>
        </div>

        <!-- 发牌  -->
		<div class="cardDeal">

			<div v-for="p in player" v-show="p.account_id>0&&p.account_status>2&&(p.num!=1||(p.account_status!=4&&p.account_status!=6&&p.account_status!=7&&!player[0].is_showCard))&&(game.cardDeal>0||p.account_status==6||p.account_status==7)">

				<div class="card" v-bind:class="'card' + p.num + '1'" v-show="game.cardDeal>0||p.account_status==6||p.account_status==7"></div>
				<div class="card" v-bind:class="'card' + p.num + '2'" v-show="game.cardDeal>1||p.account_status==6||p.account_status==7"></div>
				<div class="card" v-bind:class="'card' + p.num + '3'" v-show="game.cardDeal>2||p.account_status==6||p.account_status==7"></div>
				<div class="isSeen" v-bind:class="'isSeen' + p.num" v-show="p.num>1&&p.account_status==4"></div>
				<div class="isQuit" v-bind:class="'isQuit' + p.num" v-show="p.account_status==6"></div>
				<div class="isLose" v-bind:class="'isLose' + p.num" v-show="p.account_status==7&&!p.is_pk"></div>
			</div>

            <!-- 自己的牌 -->
			<div class="myCards" v-show="player[0].is_showCard&&player[0].account_status>2" >
				<div v-show="player[0].is_win" class="winText" >
					<img src="<?php echo $image_url;?>files/images/flower/text_win.png" v-show="!player[0].win_show" />
					<div class="winScore" v-show="player[0].win_show" style="width: 100%;left: 0;text-align: center;font-size: 16px;">
						+{{player[0].current_win}}
					</div>
				</div>

				<div class="cards card0">
					<div class="face front"></div>
					<div class="face back" v-bind:class="'card' + player[0].card[0]"></div>
					<div class="myQuitBack" v-show="player[0].account_status==6||player[0].account_status==7"></div>
				</div>

				<div class="cards card1">
					<div class="face front"></div>
					<div class="face back" v-bind:class="'card' + player[0].card[1]"></div>
					<div class="myQuitBack" v-show="player[0].account_status==6||player[0].account_status==7"></div>
				</div>

				<div class="cards card2">
					<div class="face front"></div>
					<div class="face back" v-bind:class="'card' + player[0].card[2]"></div>
					<div class="myQuitBack" v-show="player[0].account_status==6||player[0].account_status==7"></div>
				</div>

                <!-- width: 70px;height:20px;position: absolute;top:17px;left:50%;margin-left: -40px;z-index: 95;text-align: center;color: #fff;font-size: 15px;padding: 1px 5px;-->
				<div style="width: 70px;height:20px;position: absolute;top:17px;left:50%;margin-left: -40px;z-index: 95;text-align: center;color: #fff;font-size: 15px;padding: 1px 5px;" v-show="player[0].account_status==5&&player[0].playing_status==2&&player[0].can_seen" v-on:click="choose(1)">
					<!-- <div style="position: absolute;top:0;left: 0;border-radius: 11px;opacity: 0.3;width: 100%;height: 100%;background: #000;"></div>
					<div style="position: relative;opacity: 0.9;">点击看牌</div> -->
					<img src="<?php echo $image_url;?>files/images/flower/text_click2look.png" style="width: 70px;height: 20px">
				</div>

			    <div class="isQuit isQuit1" v-show="player[0].account_status==6"></div>
			    <div class="isLose isLose1" v-show="player[0].account_status==7"></div>
			</div>

		</div>

        <!-- 玩家  	-->
		<div  v-for="p in player" key="p.num" class="member" v-bind:class="'member' + p.num" v-show="p.account_id>0&&!p.is_pk">
			<img src="<?php echo $image_url;?>files/images/common/player_bg.png" class="background">
			<img src="<?php echo $image_url;?>files/images/common/player_selected.png" class="background" v-show="p.is_win&&p.num==1">
			<div class="title">{{p.nickname}}</div>
			<!--
			<img v-bind:src="p.headimgurl" class="avatar">
			-->

			<div class="avatar">
				<img v-bind:src="p.headimgurl" style="position: absolute;left:25%;width: 100%;border-radius: 3px;">

				<img src="<?php echo $image_url;?>files/images/flower/avatar_timer.jpg" style="position: absolute;left:25%;width: 100%;border-radius: 3px;" v-show="p.playing_status>1">
				<div style="position: absolute; top: 0px;left: 25%;width: 100%; line-height: 42px;text-align: center;color: #602603;font-size: 20px;" v-if="p.num!=1" v-show="p.playing_status>1">{{p.limit_time}}</div>
				<div style="position: absolute; top: 0px;left: 25%;width: 100%; line-height: 52px;text-align: center;color: #602603;font-size: 20px;" v-if="p.num==1" v-show="p.playing_status>1">{{p.limit_time}}</div>
			</div>

			<div class="score">{{p.account_bean}}</div>
			<div class="quitBack" v-show="((p.account_status==6||p.account_status==7)&&p.num==1)||(p.num>1&&p.online_status==0)" ></div>

			<!--  头像计时动画
			<div class="colorBorder" v-bind:class="'colorBorder' + p.num" v-show="p.playing_status>1" v-bind:style="'animation:mycolor1 ' + p.limit_time + 's linear;-webkit-animation:mycolor1 ' + p.limit_time + 's linear;'">
				<div class="backColor"  v-bind:style="'animation:mycolor2 '+ p.limit_time + 's linear;-webkit-animation:mycolor2 ' + p.limit_time + 's linear;'">
				</div>
			</div>
			-->

			<div class="isReady" v-show="game.round!=game.total_num">
				<img src="<?php echo $image_url;?>files/images/common/ready_button.png" class="unready" v-show="(p.account_status==1||p.account_status==0)&&p.num==1&&game.status==1" v-on:click="imReady" />
				<img src="<?php echo $image_url;?>files/images/common/ready_text.png" class="ready" v-show="p.account_status==2" />
			</div>

			<div class="cardOver" v-bind:class="'cardOver' + p.num" v-if="p.num!=1" v-show="game.cardDeal==-1&&p.account_status>1&&p.account_status!=6&&p.account_status!=7&&p.card.length>0">
				<img src="<?php echo $image_url;?>files/images/flower/cardBox.png" class="cardResult" v-show="!p.is_win" />
				<img src="<?php echo $image_url;?>files/images/flower/cardBoxWin.png" class="cardResult" v-show="p.is_win" />
				<div class="name">{{p.nickname}}</div>
				<div class="openCard">
					<div class="cards card0">
			            <div class="face front"></div>
			            <div class="face back" v-bind:class="'card' + p.card[0]"></div>
			        </div>
			        <div class="cards card1">
				        <div class="face front"></div>
				        <div class="face back" v-bind:class="'card' + p.card[1]"></div>
				    </div>
				   	<div class="cards card2">
				        <div class="face front"></div>
				        <div class="face back" v-bind:class="'card' + p.card[2]"></div>
				    </div>
				</div>
				<div v-show="p.is_win&&p.win_show" class="winText">
					<div class="winType" v-bind:class="'winType' + p.win_type"></div>
					<div class="winScore" v-bind:class="'winType' + p.win_type">+{{p.current_win}}</div>
				</div>
			</div>
			<div class="PKbox" v-show="pk.round==1&&pkPeople.length>0&&p.is_readyPK&&(player[0].account_status==5||player[0].account_status==4)" v-on:click="choose(5,p.account_id)">
				<div class="PKboxBack"></div>
			</div>
		</div>

        <!-- 玩家消息  -->
		<div v-for="p in player">
			<div class="messageSay" v-bind:class="'messageSay' + p.num" v-show="p.messageOn">
				<div>{{p.messageText}}</div>
				<div class="triangle"> </div>
			</div>
		</div>

		<div class="clock" v-show="game.time>-1">
			{{game.time}}
		</div>

        <!-- 按键  	-->
		<div class="myButton" v-if="player[0].playing_status==2&&pk.round==0">
			<div class="scoreList" >
				<div v-show="player[0].account_status==4">
					<div class="scoreItem scoreItemText scoreItem11"  v-show="game.currentScore<=scoreList1[0]" v-on:click="choose(2,scoreList1[0])">{{scoreList1[0]}}</div>
					<div class="scoreItem scoreItemText scoreItem50"  v-show="game.currentScore>scoreList1[0]">{{scoreList1[0]}}</div>
					<div class="scoreItem scoreItemText scoreItem21"  v-show="game.currentScore<=scoreList1[1]" v-on:click="choose(2,scoreList1[1])">{{scoreList1[1]}}</div>
					<div class="scoreItem scoreItemText scoreItem50"  v-show="game.currentScore>scoreList1[1]">{{scoreList1[1]}}</div>
					<div class="scoreItem scoreItemText scoreItem31"  v-show="game.currentScore<=scoreList1[2]" v-on:click="choose(2,scoreList1[2])">{{scoreList1[2]}}</div>
					<div class="scoreItem scoreItemText scoreItem50"  v-show="game.currentScore>scoreList1[2]">{{scoreList1[2]}}</div>
					<div class="scoreItem scoreItemText scoreItem41"  v-show="game.currentScore<=scoreList1[3]" v-on:click="choose(2,scoreList1[3])">{{scoreList1[3]}}</div>
				</div>

				<!-- 闷牌 -->
				<div v-show="player[0].account_status==5">
					<div class="scoreItem scoreItemText scoreItem51"  v-show="game.currentScore<=scoreList1[0]" v-on:click="choose(2,scoreList2[0])">{{scoreList2[0]}}</div>
					<div class="scoreItem scoreItemText scoreItem50"  v-show="game.currentScore>scoreList1[0]">{{scoreList2[0]}}</div>
					<div class="scoreItem scoreItemText scoreItem11"  v-show="game.currentScore<=scoreList1[1]" v-on:click="choose(2,scoreList2[1])">{{scoreList2[1]}}</div>
					<div class="scoreItem scoreItemText scoreItem50"  v-show="game.currentScore>scoreList1[1]">{{scoreList2[1]}}</div>
					<div class="scoreItem scoreItemText scoreItem21"  v-show="game.currentScore<=scoreList1[2]" v-on:click="choose(2,scoreList2[2])">{{scoreList2[2]}}</div>
					<div class="scoreItem scoreItemText scoreItem50"  v-show="game.currentScore>scoreList1[2]">{{scoreList2[2]}}</div>
					<div class="scoreItem scoreItemText scoreItem61"  v-show="game.currentScore<=scoreList1[3]" v-on:click="choose(2,scoreList2[3])">{{scoreList2[3]}}</div>
				</div>
			</div>
			<div class="doubleButton">
				<div class="isOver">
					<div class="leftButton button" v-on:click="choose(3,1)"></div>
					<div class="rightButton button" v-show="game.can_open==1&&pkPeople.length>0" v-on:click="choose(4,1)"></div>
					<div class="rightButton1 button" v-show="game.can_open==0||pkPeople.length==0" ></div>
				</div>
			</div>
		</div>

		<div class="pkBackground" v-show="pk.round==1&&pkPeople.length>0&&(player[0].account_status==5||player[0].account_status==4)" v-on:click="quitPk">
			<div class="pkBackText">选择比牌对象</div>
		</div>
	</div>

	<!-- 提示  -->
	<div class="alert" id="valert" v-show="isShowAlert">
		<div class="alertBack"></div>
		<div class="mainPart">
			<div class="backImg">
				<div class="blackImg"></div>
			</div>
			<div class="alertText" >{{alertText}}</div>

			<div v-show="alertType==1">
				<div class="buttonLeft" v-on:click="home">返回首页</div>
				<div class="buttonRight" v-on:click="home">创建房间</div>
			</div>

			<div v-show="alertType==2">
				<div class="buttonLeft" v-on:click="home">返回首页</div>
				<div class="buttonRight" v-on:click="home">创建房间</div>
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

	<!--  PK  -->
	<div class="playerPK" v-show="pk.round>1">
		<div class="pkBack" ></div>
		<div class="pk1" >
			<img src="<?php echo $image_url;?>files/images/flower/comLeft.png" style="position: absolute;top:0;left:0;width:95%;height:140px;">
			<div class="pkPlayer">
				<img src="<?php echo $image_url;?>files/images/flower/comB.png" class="background" v-show="pk.round>1">
				<div class="title">{{pk1.nickname}}</div>
				<img v-bind:src="pk1.headimgurl" class="avatar">
				<div class="score">{{pk1.account_bean}}</div>
				<div class="quitBack"></div>
			</div>
			<img src="<?php echo $image_url;?>files/images/flower/comLoser.png" v-show="pk.round==4&&pk1.account_status==7" class="pkLoser" />
		</div>
		<div class="pk2">
			<img src="<?php echo $image_url;?>files/images/flower/comRight.png" style="position: absolute;top:0;right:0;width:95%;height:140px;">
			<div class="pkPlayer">
				<img src="<?php echo $image_url;?>files/images/flower/comB.png" class="background" v-show="pk.round>1">
				<div class="title">{{pk2.nickname}}</div>
				<img v-bind:src="pk2.headimgurl" class="avatar">
				<div class="score">{{pk2.account_bean}}</div>
				<div class="quitBack"></div>
			</div>
			<img src="<?php echo $image_url;?>files/images/flower/comLoser.png" v-show="pk.round==4&&pk2.account_status==7" class="pkLoser" />
		</div>
		<img src="<?php echo $image_url;?>files/images/flower/comE.gif" v-show="pk.round==3" class="pkE" />
		<img src="<?php echo $image_url;?>files/images/flower/comV.png" v-show="pk.round>2" class="pkV" />
		<img src="<?php echo $image_url;?>files/images/flower/comS.png" v-show="pk.round>2" class="pkS" />
	</div>

    <!-- 排行 -->
	<div class="ranking hideRanking" id="ranking" style="z-index: 1">
		<div class="rankBack">
		<img    src="<?php echo $image_url;?>files/images/flower/rank_bg.jpg" style="position: absolute;top: 0;left: 0;width: 100%">
		</div>

		<div class="rankText" style="position: absolute;top: 4%;">
			<img    src="<?php echo $image_url;?>files/images/flower/rank_frame.png" style="position: absolute;top: 0%;left: 5vw;width: 190vw;">
			<div class="time" v-show="playerBoard.round>0" style="position: absolute;top: 45.5vw;width: 100%;">
				<a style="border-color: #ce6eff;background-color: #382b5e;font-size: 6vw;">房间号:{{game.room_number}}&nbsp&nbsp&nbsp&nbsp{{playerBoard.record}}&nbsp&nbsp&nbsp&nbsp{{game.total_num}}局</a>
			</div>
			<div style="height: 63.5vw;"></div>

            <div class="scoresItem scoresItemWhite">
                <div class="name">昵称</div>
                <div class="currentScores">得分</div>
                <div class="consumeScores">消耗</div>
            </div>

			<div v-for="p in playerBoard.score" class="scoresItem" v-bind:class="{true: 'scoresItemYellow', false: 'scoresItemWhite'}[p.account_score>0]" v-show="p.account_id>0">
				<img    src="<?php echo $image_url;?>files/images/common/rank_bigwinner.png" style="top: 0; margin-top: -2.5vw; left: 4px;height: 100%" v-show="p.isBigWinner==1">
				<div class="name">{{p.nickname}}</div>
				<div class="currentScores"><a v-show="p.account_score>0">+</a>{{p.account_score}}</div>
				<div class="consumeScores">{{p.consume_score}}</div>
			</div>
		</div>
		<div class="button roundEndShow" v-if="roomStatus!=4||1">
            <img src="<?php echo $image_url;?>files/images/common/rank_save.png" style="float: left;margin-left: 10%;" />
            <img src="<?php echo $image_url;?>files/images/common/score_search.png" style="float: right;margin-right: 10%" />
		</div>
	</div>

	<!-- 消息  	-->
	<div class="message" v-show="isShowMessage" >
        	<div class="messageBack" v-on:click="hideMessage"></div>
        	<div class="textPartOuter"></div>
        	<div id="message-box" class="textPart" v-bind:style="'height: ' + 0.39 * height + 'px;'">
        		<!-- <div class="outline"></div> -->
        		<div id="scroll-box" class="textList" style="width: 100%;">
        			<div class="textItem" v-for="m in message" v-on:click="messageOn(m.num)">{{m.text}}</div>
        			<!-- <div class="textItem" style="height: 5px;background: #434547;"></div> -->
        		</div>
        	</div>
        </div>

	<!-- end图片  -->
	<div id="endCreateRoom" class="end" style="position: fixed;width: 100%;height:100%;top:0;left:0;z-index: 120;display: none;overflow: hidden;">
		<img src="" style="width: 100vw;position: absolute;top:0;left: 0;height: 100vh;" id="end"  usemap="#planetmap1" />
        <a href="/f/yh" style="position: absolute;bottom:12%;display: block;width:34vw;height:11vw;margin-right: 10%;left: 56vw" ></a>
	</div>

    <!-- 积分数据-->
    <div class="layerGameScore" id="vGameScore" v-show="scoreInfo.isShow" v-on:click="cancelGameScore">
        <div class="gameScoreBack"></div>
        <div class="mainPart" style="height: 232px;">
            <div class="createB"></div>
            <div class="createTitle" style="line-height: 36px;color: #f2ae4a">您的<?php echo $game_title;?>战绩</div>
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

	<!-- 游戏规则-->
	<div class="createRoom" id="vroomRule" v-show="ruleInfo.isShowRule" v-on:click="cancelGameRule">
		<div class="createRoomBack"></div>
		<div class="mainPart" >
			<div  class="createB"></div>
			<div class="createTitle">
				<img src="<?php echo $image_url;?>files/images/common/txt_rule.png" />
			</div>

            <img src="<?php echo $image_url;?>files/images/common/cancel.png" class="cancelCreate" v-on:click="cancelGameRule"/>

			<div class="blueBack">
				<div class="selectPart" style="top: 0px;height:28px;line-height:28px;">
					<div class="selectTitle" style="width: 100%;font-size: 2vh; text-align: center;color: #7dd9ff; background-color: #143948;opacity: 1.0">创建房间,游戏未进行,不消耗房卡</div>
				</div>

				<div class="selectPart" style="height:30px;line-height:30px;padding:6px 0;">
					<div class="selectTitle">筹码：</div>
					<div class="selectList" >
						<div class="selectItem" style="margin-left:12px;" v-if="ruleInfo.chip_type==1">
							<div class="selectText" >2/4,4/8,8/16,10/20</div>
						</div>
						<div class="selectItem" style="margin-left:12px;" v-if="ruleInfo.chip_type==2">
							<div class="selectText" >2/4,5/10,10/20,20/40</div>
						</div>
						<div class="selectItem" style="margin-left:12px;" v-if="ruleInfo.chip_type==4">
							<div class="selectText" >5/10,10/20,20/40,40/80</div>
						</div>
					</div>
				</div>

				<div class="selectPart" style="'height:30px;line-height:30px;padding:6px 0;'" v-if="ruleInfo.disable_pk_100==1">
					<div class="selectTitle">规则：</div>
					<div class="selectList">
						<div class="selectItem" style="margin-left:12px;">
							<div class="selectText" >100分以下不能比牌</div>
						</div>
					</div>
				</div>

                <div class="selectPart" style="height:30px;line-height:30px;padding:6px 0;">
                    <div class="selectTitle" style="width: 17vh;text-align: left;margin-left:13px;">看牌需下注：</div>
                    <div class="selectList" style="width: 3vh;margin-left: -5vh">
                        <div class="selectItem" v-if="ruleInfo.seen==0">
                            <div class="selectText" >无</div>
                        </div>
                        <div class="selectItem" v-if="ruleInfo.seen==20">
                            <div class="selectText" >20</div>
                        </div>
                        <div class="selectItem" v-if="ruleInfo.seen==50">
                            <div class="selectText" >50</div>
                        </div>
                        <div class="selectItem" v-if="ruleInfo.seen==100">
                            <div class="selectText" >100</div>
                        </div>
                    </div>
                </div>

				<div class="selectPart" style="height:30px;line-height:30px;padding:6px 0;">
					<div class="selectTitle">局数：</div>
					<div class="selectList">
						<div class="selectItem" style="margin-left:12px;" v-if="ruleInfo.ticket_count==1">
							<div class="selectText" >10局X1张房卡</div>
						</div>
						<div class="selectItem" style="margin-left:12px;" v-if="ruleInfo.ticket_count==2">
							<div class="selectText" >20局X2张房卡</div>
						</div>
					</div>
				</div>
				<div class="selectPart" style="height:30px;line-height:30px;padding:6px 0;">
					<div class="selectTitle">上限：</div>
					<div class="selectList">
						<div class="selectItem" style="margin-left:12px;" v-if="ruleInfo.upper_limit==0">
							<div class="selectText" >无</div>
						</div>
						<div class="selectItem" style="margin-left:12px;" v-if="ruleInfo.upper_limit==500">
							<div class="selectText" >500</div>
						</div>
						<div class="selectItem" style="margin-left:12px;" v-if="ruleInfo.upper_limit==1000">
							<div class="selectText" >1000</div>
						</div>
						<div class="selectItem" style="margin-left:12px;" v-if="ruleInfo.upper_limit==2000">
							<div class="selectText" >2000</div>
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

    <script type="text/javascript" src="<?php echo $image_url;?>files/js/canvas.js" ></script>
</div>


</body>

<script src="https://res.wx.qq.com/open/js/jweixin-1.0.0.js" ></script>
<script type="text/javascript" src="<?php echo $image_url;?>files/js/jquery-1.9.1.min.js"></script>
<script type="text/javascript" src="<?php echo $image_url;?>files/js/bscroll.js"></script>
<script type="text/javascript" src="<?php echo $image_url;?>files/js/velocity.js"></script>
<script type="text/javascript" src="<?php echo $image_url;?>files/js/vue.min.js" ></script>
<script type="text/javascript" src="<?php echo $image_url;?>files/js/vue-resource.min.js" ></script>
<script type="text/javascript" src="<?php echo $base_url;?>files/js/vflower6.js?t=<?php echo time();?>"></script>

<script type="text/javascript" src="<?php echo $image_url;?>files/js/jquery.marquee.min.js"></script>
<script type="application/javascript">
    $('.marquee').marquee({
        duration: 5000,
        delayBeforeStart: 0,
        direction: 'left'
    });
</script>

</html>
