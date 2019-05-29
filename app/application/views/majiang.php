<html >
<head>
<meta charset="utf-8" >
<meta name="viewport" content="width=device-width,initial-scale=1.0, minimum-scale=1.0, maximum-scale=1.0, user-scalable=no"/>
<meta name="format-detection" content="telephone=no" />
<meta name="msapplication-tap-highlight" content="no" />
<title>麻将房间<?php echo $room_number;?></title>

<link rel="stylesheet" href="<?php echo $file_url;?>files/css/loading.css">
<link rel="stylesheet" href="<?php echo $file_url;?>files/css/majiang.css">
<link rel="stylesheet" href="<?php echo $file_url;?>files/css/alert_majiang.css">
<script type="text/javascript" src="<?php echo $file_url;?>files/js/fastclick.js"></script>


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
		"room_status": "<?php echo $room_status;?>",
		"balance_scoreboard": '<?php echo $balance_scoreboard;?>',
		"session": '<?php echo $session;?>',
		"httpUrl":'<?php echo "$http_url"?>',
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

<body  id="body" >
	<style>
	    body.modal-show {position: fixed;width: 100%;}
	</style>
<div style="position: fixed;width:100%;height:100%;top:0;left:0;background: #000;z-index: 139;" id="loading">
	<div class="load4">
		<div class="loader">Loading...</div>
	</div>
</div>
<div id="app-main">

<div class="main" style="display: none;z-index: 1;position: fixed;">
	<!--长按屏蔽区-->
	<div  id="prevent"> </div>
	<!--首页 加人 局数-->
	<div class="rightTop">
		{{game.round}}/{{game.total_num}}局
	</div>
	<div class="createRoom" v-show="isShowRull"  v-on:click="closeRull()">
		<div class="createRoomBack"></div>
		<div class="mainPart" >
			<div  class="createB"></div>
			<div class="createTitle">
				<img src="<?php echo $image_url;?>files/images/common/txt_rule.png" />
			</div>
			<div class="blueBack">
				<div class="selectPart"style="height:32px;margin-top: 6px;">
					<div class="selectTitle">鬼牌：</div>
					<div class="selectList" >
						<div class="selectItem" style="margin-left:5px;" v-show="rullInfo.joker==0">
							<div class="selectText">无鬼牌</div>
						</div>
						<div class="selectItem"style="margin-left:5px;" v-show="rullInfo.joker==1">
							<div class="selectText">翻牌当鬼</div>
						</div>
						<div class="selectItem"style="margin-left:5px;" v-show="rullInfo.joker==2" >
							<div class="selectText">红中当鬼</div>
						</div>
					</div>
				</div>
				<div class="selectPart" style="height:32px;">
					<div class="selectTitle">抓马：</div>
					<div class="selectList">
						<div>
							<div class="selectItem" style="margin-left:5px;"v-show="rullInfo.horse_count==0">
								<div class="selectText">不跑马</div>
							</div>
							<div class="selectItem"style="margin-left:5px;" v-show="rullInfo.horse_count==2">
								<div class="selectText">2匹</div>
							</div>
							<div class="selectItem"style="margin-left:5px;" v-show="rullInfo.horse_count==4">
								<div class="selectText">4匹</div>
							</div>
							<div class="selectItem"style="margin-left:5px;" v-show="rullInfo.horse_count==6">
								<div class="selectText">6匹</div>
							</div>
						</div>
						<div>
							<div class="selectItem" style="margin-left:5px;" v-show="rullInfo.horse_count==8" >
								<div class="selectText">8匹</div>
							</div>
							<div class="selectItem" style="margin-left:5px;" v-show="rullInfo.horse_count==1">
								<div class="selectText">爆炸马</div>
							</div>
						</div>
					</div>
				</div>
				<div class="selectPart"style="height:32px;"v-show="rullInfo.qianggang==1||rullInfo.chengbao==1">
					<div class="selectTitle">规则：</div>
					<div class="selectList">
						<div class="selectItem" style="margin-left:5px;" v-show="rullInfo.qianggang==1" >
							<div class="selectText">抢杠全包</div>
						</div>
						<div class="selectItem" style="margin-left:5px;" v-show="rullInfo.chengbao==1" >
							<div class="selectText">杠爆全包</div>
						</div>
					</div>
				</div>
				<div class="selectPart"style="height:32px;">
					<div class="selectTitle">房卡：</div>
					<div class="selectList">
						<div class="selectItem" style="margin-left:5px;" v-show="rullInfo.ticket_count==1">
							<div class="selectText">8局X1张房卡</div>
						</div>
						<div class="selectItem"style="margin-left:5px;" v-show="rullInfo.ticket_count==2" >
							<div class="selectText">16局X2张房卡</div>
						</div>
					</div>
				</div>
			</div>

		</div>
	</div>
	<!--房卡-->
	<div class="roomCard" v-on:click="showShop()">
		<img  src="<?php echo $image_url;?>files/images/common/ticket.png" />
		<div class="num">
			<div class="back" ></div>
			<div class="text">{{userInfo.card}} 张</div>
		</div>
	</div>
	<!--跑马-->
	<div class="endMa" v-show="game.isShowEnd">
		<div class="back"></div>
		<div v-show="game.endStep<6&&game.horse_count>1">
			<img src="<?php echo $image_url;?>files/images/majiang/paoma.png" class="title" />
			<div class="card" v-for="m in game.ma" v-if="m.num<4" v-bind:style="'margin-left: '+(12*m.num)+'%;'">
				<div class="animate">
					<img src="<?php echo $image_url;?>files/images/majiang/majiang5.png" class="outer" v-show="game.endStep==0"/>
					<img src="<?php echo $image_url;?>files/images/majiang/ma2.png" class="outer" v-show="game.endStep==1"/>
					<img src="<?php echo $image_url;?>files/images/majiang/ma3.png" class="outer" v-show="game.endStep==2"/>
					<img src="<?php echo $image_url;?>files/images/majiang/ma4.png" class="outer" v-show="game.endStep==3"/>
					<img src="<?php echo $image_url;?>files/images/majiang/majiang1.png" class="outer" v-show="game.endStep==4||game.endStep==5"/>
					<img v-bind:src="'<?php echo $image_url;?>files/images/majiang/cards'+(m.card%100)+'.png'" class="inner" v-if="game.endStep==4||game.endStep==5" />
					<div class="backInner"  v-show="game.endStep==5&&m.isMa==0"></div>
					<div class="lightInner" v-show="game.endStep==5&&m.isMa==1"></div>
				</div>
			</div>
			<div class="card" v-for="m in game.ma" v-if="m.num>3" v-bind:style="'margin-left:' +(12*(m.num-4))+'%;margin-top: 17%;'">
				<div class="animate">
					<img src="<?php echo $image_url;?>files/images/majiang/majiang5.png" class="outer" v-show="game.endStep==0"/>
					<img src="<?php echo $image_url;?>files/images/majiang/ma2.png" class="outer" v-show="game.endStep==1"/>
					<img src="<?php echo $image_url;?>files/images/majiang/ma3.png" class="outer" v-show="game.endStep==2"/>
					<img src="<?php echo $image_url;?>files/images/majiang/ma4.png" class="outer" v-show="game.endStep==3"/>
					<img src="<?php echo $image_url;?>files/images/majiang/majiang1.png" class="outer" v-show="game.endStep==4||game.endStep==5"/>
					<img v-bind:src="'<?php echo $image_url;?>files/images/majiang/cards'+(m.card%100)+'.png'" class="inner" v-if="game.endStep==4||game.endStep==5" />
					<div class="backInner" v-show="game.endStep==5&&m.isMa==0"></div>
					<div class="lightInner" v-show="game.endStep==5&&m.isMa==1"></div>
				</div>
			</div>
		</div>
		<div v-if="game.endStep<6&&game.horse_count==1">
			<img src="<?php echo $image_url;?>files/images/majiang/paoma.png" class="title" />
			<div class="card baozha" >
				<div class="animate">
					<img src="<?php echo $image_url;?>files/images/majiang/zifront.png" class="rotatelight" v-show="game.endStep==5" />
					<img src="<?php echo $image_url;?>files/images/majiang/majiang5.png" class="outer" v-show="game.endStep==0"/>
					<img src="<?php echo $image_url;?>files/images/majiang/ma2.png" class="outer" v-show="game.endStep==1"/>
					<img src="<?php echo $image_url;?>files/images/majiang/ma3.png" class="outer" v-show="game.endStep==2"/>
					<img src="<?php echo $image_url;?>files/images/majiang/ma4.png" class="outer" v-show="game.endStep==3"/>
					<img src="<?php echo $image_url;?>files/images/majiang/majiang1.png" class="outer donghua" v-show="game.endStep==4||game.endStep==5"/>
					<img v-bind:src="'<?php echo $image_url;?>files/images/majiang/cards'+(game.ma[0].card%100)+'.png'" class="inner donghua" v-if="game.endStep==4||game.endStep==5" />
					<div class="lightOuter" v-show="game.endStep==5"></div>
				</div>
			</div>
			<div class="beishu" v-if="game.endStep==5&&game.ma.length>0">×{{game.ma[0].isMa}}</div>
		</div>
	</div>
	<!--游戏规则,位置-->
	<div class="readyBox" v-show="player[0].ticket_checked==0">
		<div class="back"></div>
		<div class="blueBack">
			<img src="<?php echo $image_url;?>files/images/majiang/createroom1.png" class="imgBack">
			<div class="infoPart">
				<div class="rule">
					<div class="guize">规则:</div>
					<div v-show="game.joker==0">无鬼牌,</div>
					<div v-show="game.joker==1">翻牌当鬼,</div>
					<div v-show="game.joker==2">红中当鬼,</div>
					<div v-show="game.horse_count==0">不跑马,</div>
					<div v-show="game.horse_count==4">4匹马,</div>
					<div v-show="game.horse_count==6">6匹马,</div>
					<div v-show="game.horse_count==8">8匹马,</div>
					<div v-show="game.horse_count==1">爆炸马,</div>
					<div v-show="game.qianggang==1">抢杠全包,</div>
					<div v-show="game.chengbao==1">杠爆全包,</div>
					<div v-show="game.ticket_count==1">8局×1张房卡</div>
					<div v-show="game.ticket_count==2">16局×2张房卡</div>
				</div>
				<div class="position">
					<div class="positionList" v-for="p in game.positionList2" v-if="p.position==2">
						<img v-bind:src="p.headimgurl" />
						<div class="name" >{{p.nickname}}</div>
						<div style="margin-left: 15px;">进入了房间</div>
					</div>
					<div class="positionList" v-for="p in game.positionList1">
						<img v-bind:src="p.headimgurl1" />
						<div class="name" >{{p.nickname1}}</div>
						<div style="margin-left: 7px;margin-right: 4px;">相距</div>
						<img v-bind:src="p.headimgurl2" />
						<div class="name" >{{p.nickname2}}</div>
						<div style="margin-left: 15px;">{{p.position}}</div>
					</div>
					<div class="positionList" v-for="p in game.positionList2" v-if="p.position==0">
						<img v-bind:src="p.headimgurl" />
						<div class="name" >{{p.nickname}}</div>
						<div style="margin-left: 15px;">地理位置未知</div>
					</div>
				</div>
			</div>
			<div class="ready01" v-on:click="click(0)" v-show="player[0].account_status==0||player[0].account_status==1" >
				<img src="<?php echo $image_url;?>files/images/majiang/rank_btn1.png"  />
				<div style="position: relative;">准备</div>
			</div>
			<img src="<?php echo $image_url;?>files/images/common/ready_text.png" class="ready02" v-show="player[0].account_status==2"/>
		</div>
	</div>
	<!--功能按钮-->
	<div class="spbutton rullButton" v-on:click="showRull()"><img src="<?php echo $image_url;?>files/images/common/icon_rule.png"></div>
	<div class="spbutton sphome" v-on:click="showAlert(3,'确认返回主页？')"><img src="<?php echo $image_url;?>files/images/common/icon_home.png"></div>

	<div class="spbutton recordButton" v-on:click="showAudioSetting()"><img src="<?php echo $image_url;?>files/images/common/icon_sound.png"></div>

	<div class="spbutton spaudio"  v-on:click="showMessage()"><img src="<?php echo $image_url;?>files/images/common/icon_message.png"></div>

	<div class="shop" v-show="isShowShop">
		<div class="shopBack" v-on:click="hideShop()"></div>
	</div>


	<div class="startBack"></div>
	<!--游戏区域-->
	<div class="playGround">
		<!--中间指示-->
		<div class="center" v-show="game.joker!=1||(animate.animate3==8)">
			<div class="greenLight greenLight1" v-show="game.light==1"></div>
			<div class="greenLight greenLight2" v-show="game.light==2"></div>
			<div class="greenLight greenLight3" v-show="game.light==3"></div>
			<div class="greenLight greenLight4" v-show="game.light==4"></div>
			<div class="back">
				<div class="up" v-show="game.status==2">
					<div v-bind:class="'digital digital'+game.time.firstNum" style="margin-left: -14px;left: 50%;"></div>
					<div v-bind:class="'digital digital'+game.time.lastNum" style="margin-right: -14px;right: 50%;"></div>
				</div>
				<div class="down" v-show="game.status==2">
					<img src="<?php echo $image_url;?>files/images/majiang/majiang2.1.png"  />X {{game.remain_count}}
				</div>
			</div>
		</div>
		<!--玩家信息-->
		<div class="playerPart">
			<div v-bind:class="'player player'+p.num" v-for="p in player" v-if="p.account_id>0" >
				<div class="avatar">
					<img src="<?php echo $image_url;?>files/images/majiang/avatarback.png" class="avatar1"/>
					<img v-bind:src="p.headimgurl" class="avatar2"/>
					<div class="zhuang" v-show="p.account_id==game.banker_id">庄</div>
					<div class="quitBack" v-show="p.online_status==0" ></div>
				</div>
				<div class="info">
					<div class="name">{{p.nickname}}</div>
					<div class="score">{{p.account_score}}</div>
				</div>
				<img src="<?php echo $image_url;?>files/images/common/ready_text.png" class="ready"v-show="p.account_status==2&&p.num!=1" />
			</div>
		</div>
		<div v-bind:class="'messageSay messageSay'+p.num" v-show="p.messageOn" v-for="p in player" >
			<div class="text">{{p.messageText}}</div>
			<div class="triangle"> </div>
		</div>


		<!--翻鬼牌-->
		<div class="fanguipai" v-if="game.joker==1">
			<div class="guipaifront">
				<img src="<?php echo $image_url;?>files/images/majiang/majiang5.png" class="outer" v-show="animate.animate3==1"/>
				<img src="<?php echo $image_url;?>files/images/majiang/ma2.png" class="outer" v-show="animate.animate3==2"/>
				<img src="<?php echo $image_url;?>files/images/majiang/ma3.png" class="outer" v-show="animate.animate3==3"/>
				<img src="<?php echo $image_url;?>files/images/majiang/ma4.png" class="outer" v-show="animate.animate3==4"/>
				<img src="<?php echo $image_url;?>files/images/majiang/majiang1.png" class="outer" v-show="animate.animate3==5"/>
				<img v-bind:src="'<?php echo $image_url;?>files/images/majiang/cards'+(game.flip_card%100)+'.png'" class="inner" v-if="animate.animate3==5&&game.flip_card>0" />
			</div>
			<div class="guipai1" v-show="animate.animate3==6">
				<img src="<?php echo $image_url;?>files/images/majiang/zifront.png" class="lightRotate" />
				<img src="<?php echo $image_url;?>files/images/majiang/majiang1.png" class="outer donghua" />
				<img v-bind:src="'<?php echo $image_url;?>files/images/majiang/cards'+(game.joker_card%100)+'.png'" class="inner donghua" v-if="game.joker_card>0" />

			</div>
			<div class="guipai2Back"  v-show="animate.animate3>6"></div>
			<div class="guipai2" v-show="animate.animate3==7">
				<img src="<?php echo $image_url;?>files/images/majiang/majiang1.png" class="outer" />
				<img v-bind:src="'<?php echo $image_url;?>files/images/majiang/cards'+(game.joker_card%100)+'.png'" class="inner" v-if="game.joker_card>0" />
			</div>
			<div class="guipai3" v-show="animate.animate3==8">
				<img v-bind:src="'<?php echo $image_url;?>files/images/majiang/cards'+(game.joker_card%100)+'.png'" class="inner" v-if="game.joker_card>0" style="top:10%;height:85%;left:3%;width:94%;"/>
			</div>
			<div class="title" v-show="animate.animate3>0&&animate.animate3!=8">翻鬼牌</div>
		</div>

		<!--我的麻将-->
		<div class="mine">
			<div class="ready" v-show="player[0].ticket_checked==1">
				<img src="<?php echo $image_url;?>files/images/common/ready_button.png" class="readyButton" v-show="player[0].account_status==0||player[0].account_status==1" v-on:click="click(0)"/>
				<img src="<?php echo $image_url;?>files/images/common/ready_text.png" class="isReady" v-show="player[0].account_status==2"/>
			</div>
			<div class="myCard" v-show="player[0].account_status==4">
				<div v-for="c in player[0].card" class="card"  v-on:click="chooseCard(c.num)" v-show="!player[0].end_show" style="height: 94%;">
					<div v-show="c.num>=animate.animate2" style="position: absolute;top:0;left:0;width: 100%;height:100%;" v-bind:class="{true: 'isSelect', false: 'notSelect'}[c.isSelect]">
						<img src="<?php echo $image_url;?>files/images/majiang/animate2.png" class="outer" v-show="animate.animate1==2||(animate.animate1==5&&(c.num<animate.animate2+4||animate.animate2==8))" style="margin-top:20%;"/>
						<img src="<?php echo $image_url;?>files/images/majiang/animate1.png" class="outer" v-show="animate.animate1==3||(animate.animate1==6&&(c.num<animate.animate2+4||animate.animate2==8))"/>
						<img src="<?php echo $image_url;?>files/images/majiang/majiang1.png" class="outer" v-show="animate.animate1==0||animate.animate1==1||(animate.animate1==4||(c.num>=animate.animate2+4&&animate.animate2!=8&&animate.animate1>4))" />

						<img v-bind:src="'<?php echo $image_url;?>files/images/majiang/cards'+(c.card%100)+'.png'" class="inner" v-if="(animate.animate1==0||animate.animate1==1||animate.animate1==4||(c.num>=animate.animate2+4&&animate.animate2!=8&&animate.animate1>4))&&c.card>0" />
						<img src="<?php echo $image_url;?>files/images/majiang/lai.png" class="lai" v-show="(animate.animate1==0||animate.animate1==1||animate.animate1==4||(c.num>=animate.animate2+4&&animate.animate2!=8&&animate.animate1>4))&&c.card<100" />
					</div>
					<div v-show="c.num<animate.animate2" style="width: 100%;height:1px;position: relative;"></div>
				</div>
				<div v-for="c in player[0].card" class="endCard" v-if="player[0].end_show">
					<img src="<?php echo $image_url;?>files/images/majiang/majiang6.png" class="outer" />
					<img v-bind:src="'<?php echo $image_url;?>files/images/majiang/cards'+(c.card%100)+'.png'" class="inner" />
					<img src="<?php echo $image_url;?>files/images/majiang/lai.png" class="lai" v-show="c.card<100" />
				</div>
			</div>
			<div class="pengGang" v-for="p in player[0].pengGang">
				<div class="card"  v-bind:style="'margin-left: '+(0+(p.num*18))+'%;'" v-show="p.type==1||p.type==2">
					<img src="<?php echo $image_url;?>files/images/majiang/majiang6.png" class="outer" />
					<img v-bind:src="'<?php echo $image_url;?>files/images/majiang/cards'+(p.card%100)+'.png'" class="inner" />
				</div>
				<div class="card"  v-bind:style="'margin-left: '+(0+(p.num*18))+'%;'" v-show="p.type==3">
					<img src="<?php echo $image_url;?>files/images/majiang/majiang2.1.png" class="outer" />
				</div>

				<div class="card"  v-bind:style="'margin-left: '+(5.5+(p.num*18))+'%;'" v-show="p.type==1||p.type==2">
					<img src="<?php echo $image_url;?>files/images/majiang/majiang6.png" class="outer" />
					<img v-bind:src="'<?php echo $image_url;?>files/images/majiang/cards'+(p.card%100)+'.png'" class="inner" />
				</div>
				<div class="card"  v-bind:style="'margin-left: '+(5.5+(p.num*18))+'%;'" v-show="p.type==3">
					<img src="<?php echo $image_url;?>files/images/majiang/majiang2.1.png" class="outer" />
				</div>

				<div class="card cardReturn"   v-bind:style="'margin-left: '+(11+(p.num*18))+'%;z-index: 31;'" v-show="p.step==1&&(p.type==1||p.type==2)">
					<img src="<?php echo $image_url;?>files/images/majiang/majiang6.png" class="outer" />
					<img v-bind:src="'<?php echo $image_url;?>files/images/majiang/cards'+(p.card%100)+'.png'" class="inner" />
				</div>
				<div class="card"   v-bind:style="'margin-left: '+(11+(p.num*18))+'%;z-index: 31;'" v-show="p.type==3">
					<img src="<?php echo $image_url;?>files/images/majiang/majiang2.1.png" class="outer" />
				</div>

				<div class="cardUp" v-show="p.type==2||p.type==3" v-bind:style="'margin-left: '+(5.5+(p.num*18))+'%;z-index: 31;'">
					<img src="<?php echo $image_url;?>files/images/majiang/majiang6.png" class="outer" />
					<img v-bind:src="'<?php echo $image_url;?>files/images/majiang/cards'+(p.card%100)+'.png'" class="inner" />
				</div>
			</div>
			<div class="cardNew" v-bind:class="{true: 'isSelect', false: 'notSelect'}[player[0].cardNew.isSelect]" v-on:click="chooseCard(-1)" v-if="player[0].account_status==4&&player[0].cardNew.isShow&&!player[0].end_show&&animate.animate2==0" >
				<img src="<?php echo $image_url;?>files/images/majiang/majiang1.png" class="outer" />
				<img v-bind:src="'<?php echo $image_url;?>files/images/majiang/cards'+(player[0].cardNew.card%100)+'.png'" class="inner" />
				<img src="<?php echo $image_url;?>files/images/majiang/lai.png" class="lai" v-show="player[0].cardNew.card<100" />
			</div>
			<div class="cardNew1" v-if="player[0].end_show&&player[0].cardNew.isShow&&player[0].cardNew.card!=-1" >
				<img src="<?php echo $image_url;?>files/images/majiang/majiang6.png" class="outer" />
				<img v-bind:src="'<?php echo $image_url;?>files/images/majiang/cards'+(player[0].cardNew.card%100)+'.png'" class="inner" />
				<img src="<?php echo $image_url;?>files/images/majiang/lai.png" class="lai" v-show="player[0].cardNew.card<100" />
			</div>
			<div class="operation" v-show="!player[0].is_operation&&animate.animate1==1&&(player[0].playing_status>2||((player[0].hu_flag>0||player[0].gang_flag>0)&&player[0].playing_status==2))">
				<img src="<?php echo $image_url;?>files/images/majiang/operation1.png" class="bian" />
				<div class="button" v-on:click="click(4,player[0].playing_status)" v-show="player[0].playing_status>2" >
					<div class="back"></div>
					<img src="<?php echo $image_url;?>files/images/majiang/operation4.png" class="quanGuo" />
					<img src="<?php echo $image_url;?>files/images/majiang/guo.png" class="textGuo" />
				</div>
				<div class="button" v-on:click="click(5)" v-show="player[0].playing_status==3||player[0].playing_status==4">
					<div class="back"></div>
					<img src="<?php echo $image_url;?>files/images/majiang/operation3.png" class="quan" />
					<img src="<?php echo $image_url;?>files/images/majiang/peng.png" class="text" />
					<img src="<?php echo $image_url;?>files/images/majiang/zib1.png" class="text1" />
				</div>
				<div class="button" v-on:click="click(6,3)" v-show="player[0].playing_status==4">
					<div class="back"></div>
					<img src="<?php echo $image_url;?>files/images/majiang/operation3.png" class="quan" />
					<img src="<?php echo $image_url;?>files/images/majiang/gang.png" class="text" />
					<img src="<?php echo $image_url;?>files/images/majiang/zib1.png" class="text1" />
				</div>

				<div class="button" v-on:click="click(6,player[0].gang_flag)" v-show="player[0].playing_status==2&&player[0].gang_flag>0">
					<div class="back"></div>
					<img src="<?php echo $image_url;?>files/images/majiang/operation3.png" class="quan" />
					<img src="<?php echo $image_url;?>files/images/majiang/gang.png" class="text" />
					<img src="<?php echo $image_url;?>files/images/majiang/zib1.png" class="text1" />

				</div>

				<div class="button" v-on:click="click(7)" v-show="player[0].playing_status==2&&player[0].hu_flag>0">
					<div class="back"></div>
					<img src="<?php echo $image_url;?>files/images/majiang/hu.png" class="text"/>
					<img src="<?php echo $image_url;?>files/images/majiang/operation3.png" class="quan" />
					<img src="<?php echo $image_url;?>files/images/majiang/zib1.png" class="text1" />
				</div>
				<div class="button" v-on:click="click(8)" v-show="player[0].playing_status==5">
					<div class="back"></div>
					<img src="<?php echo $image_url;?>files/images/majiang/hu.png" class="text"/>
					<img src="<?php echo $image_url;?>files/images/majiang/operation3.png" class="quan" />
					<img src="<?php echo $image_url;?>files/images/majiang/zib1.png" class="text1" />
				</div>

				<img src="<?php echo $image_url;?>files/images/majiang/operation2.png" class="bian" />
				<div class="card" v-show="player[0].playing_status==5">
					<img src="<?php echo $image_url;?>files/images/majiang/majiang1.png" class="outer" />
					<img v-bind:src="'<?php echo $image_url;?>files/images/majiang/cards'+(game.qianggang_card%100)+'.png'" class="inner" v-if="game.qianggang_card>0" />
				</div>
			</div><!---->
		</div>
		<!--别人的麻将-->
		<div class="others" >
			<div class="player2"  v-if="player[1].account_status==4">
				<div class="cardNew" v-show="player[1].cardNew.isShow&&!player[1].end_show" v-bind:style="'top:'+(-player[1].pengGang.length*4.5+27)+'%;'">
					<img src="<?php echo $image_url;?>files/images/majiang/majiang3.png" class="outer" />
				</div>
				<div class="cardNew1" v-if="player[1].end_show&&player[1].cardNew.isShow&&player[1].cardNew.card!=-1"  v-bind:style="'top:'+(-player[1].pengGang.length*1.3+20)+'%;'">
					<img src="<?php echo $image_url;?>files/images/majiang/majiang8.png" class="outer" />
					<img v-bind:src="'<?php echo $image_url;?>files/images/majiang/cards'+(player[1].cardNew.card%100)+'.png'" class="inner" />
					<img src="<?php echo $image_url;?>files/images/majiang/lai.png" class="lai" v-show="player[1].cardNew.card<100" />
				</div>
				<div v-for="c in player[1].card" class="card"  v-bind:style="'margin-top:'+(1.8*(c.num+1))+'%;top:'+(-player[1].pengGang.length*4.5+27)+'%;'" v-show="!player[1].end_show">
					<img src="<?php echo $image_url;?>files/images/majiang/majiang3.png" class="outer"/>
				</div>
				<div v-for="c in player[1].card" class="endCard"  v-bind:style="'margin-top:'+(2.5*(c.num+1))+'%;top:'+(-(player[1].card.length*0.7*1.6267)-(player[1].pengGang.length*4.5)+32)+'%;'" v-if="player[1].end_show">
					<img src="<?php echo $image_url;?>files/images/majiang/majiang8.png" class="outer"/>
					<img v-bind:src="'<?php echo $image_url;?>files/images/majiang/cards'+(c.card%100)+'.png'" class="inner" />
					<img src="<?php echo $image_url;?>files/images/majiang/lai.png" class="lai" v-show="c.card<100" />
				</div>

				<div class="pengGang" v-for="p in player[1].pengGang">
					<div class="card0" v-bind:style="'margin-top: '+(5-(p.num*8.4))+'%;z-index: 33;'"  v-show="p.type==1||p.type==2">
						<img src="<?php echo $image_url;?>files/images/majiang/majiang8.png" class="outer" />
						<img v-bind:src="'<?php echo $image_url;?>files/images/majiang/cards'+(p.card%100)+'.png'" class="inner" />
					</div>
					<div class="card0" v-bind:style="'margin-top: '+(5-(p.num*8.4))+'%;z-index: 33;'"  v-show="p.type==3">
						<img src="<?php echo $image_url;?>files/images/majiang/majiang10.png" class="outer" />
					</div>
					<div class="card0" v-bind:style="'margin-top: '+(2.5-(p.num*8.4))+'%;z-index: 32;'"  v-show="p.type==1||p.type==2">
						<img src="<?php echo $image_url;?>files/images/majiang/majiang8.png" class="outer" />
						<img v-bind:src="'<?php echo $image_url;?>files/images/majiang/cards'+(p.card%100)+'.png'" class="inner" />
					</div>
					<div class="card0" v-bind:style="'margin-top: '+(2.5-(p.num*8.4))+'%;z-index: 32;'"  v-show="p.type==3">
						<img src="<?php echo $image_url;?>files/images/majiang/majiang10.png" class="outer" />
					</div>
					<div class="card0 cardReturn" v-bind:style="'margin-top: '+(0-(p.num*8.4))+'%;z-index: 31;'" v-show="p.step==1&&(p.type==1||p.type==2)">
						<img src="<?php echo $image_url;?>files/images/majiang/majiang8.png" class="outer" />
						<img v-bind:src="'<?php echo $image_url;?>files/images/majiang/cards'+(p.card%100)+'.png'" class="inner" />
					</div>
					<div class="card0" v-bind:style="'margin-top: '+(0-(p.num*8.4))+'%;z-index: 31;'"  v-show="p.type==3">
						<img src="<?php echo $image_url;?>files/images/majiang/majiang10.png" class="outer" />
					</div>
					<div class="cardUp" v-show="p.type==2||p.type==3"v-bind:style="'margin-top: '+(1.9-(p.num*8.4))+'%;z-index: 34;'">
						<img src="<?php echo $image_url;?>files/images/majiang/majiang8.png" class="outer" />
						<img v-bind:src="'<?php echo $image_url;?>files/images/majiang/cards'+(p.card%100)+'.png'" class="inner" />
					</div>
				</div>
			</div>
			<div class="player3"  v-if="player[2].account_status==4">
				<div class="cardNew" v-if="player[2].cardNew.isShow&&!player[2].end_show" style="margin-left:-4.5%;">
					<img src="<?php echo $image_url;?>files/images/majiang/majiang5.png" class="outer" />
				</div>
				<div class="cardNew1" v-if="player[2].end_show&&player[2].cardNew.isShow&&player[2].cardNew.card!=-1" style="margin-left:-4.5%;">
					<img src="<?php echo $image_url;?>files/images/majiang/majiang6.png" class="outer" />
					<img v-bind:src="'<?php echo $image_url;?>files/images/majiang/cards'+(player[2].cardNew.card%100)+'.png'" class="inner" />
					<img src="<?php echo $image_url;?>files/images/majiang/lai.png" class="lai" v-show="player[2].cardNew.card<100" />
				</div>

				<div v-for="c in player[2].card" class="card" v-bind:style="'margin-left:'+(3.55*(c.num))+'%;'"  v-show="!player[2].end_show">
					<img src="<?php echo $image_url;?>files/images/majiang/majiang5.png" class="outer"/>
				</div>
				<div v-for="c in player[2].card" class="endCard"  v-bind:style="'margin-left:'+(3.55*(c.num))+'%;'" v-if="player[2].end_show">
					<img src="<?php echo $image_url;?>files/images/majiang/majiang6.png" class="outer" />
					<img v-bind:src="'<?php echo $image_url;?>files/images/majiang/cards'+(c.card%100)+'.png'" class="inner" />
					<img src="<?php echo $image_url;?>files/images/majiang/lai.png" class="lai" v-show="c.card<100" />
				</div>

				<div class="pengGang" v-for="p in player[2].pengGang">
					<div class="card" v-bind:style="'margin-left:'+(0-(p.num*11))+'%;'">
						<img src="<?php echo $image_url;?>files/images/majiang/majiang6.png" class="outer" v-show="p.type==1||p.type==2"/>
						<img v-bind:src="'<?php echo $image_url;?>files/images/majiang/cards'+(p.card%100)+'.png'" class="inner" />
					</div>
					<div class="card" v-bind:style="'margin-left:'+(0-(p.num*11))+'%;'" v-show="p.type==3">
						<img src="<?php echo $image_url;?>files/images/majiang/majiang2.1.png" class="outer" />
					</div>
					<div class="card" v-bind:style="'margin-left:'+(3.3-(p.num*11))+'%;'" v-show="p.type==1||p.type==2">
						<img src="<?php echo $image_url;?>files/images/majiang/majiang6.png" class="outer" />
						<img v-bind:src="'<?php echo $image_url;?>files/images/majiang/cards'+(p.card%100)+'.png'" class="inner" />
					</div>
					<div class="card" v-bind:style="'margin-left:'+(3.3-(p.num*11))+'%;'" v-show="p.type==3">
						<img src="<?php echo $image_url;?>files/images/majiang/majiang2.1.png" class="outer" />
					</div>
					<div class="card cardReturn" v-bind:style="'margin-left:'+(6.6-(p.num*11))+'%;z-index: 31;'" v-show="p.step==1&&(p.type==1||p.type==2)">
						<img src="<?php echo $image_url;?>files/images/majiang/majiang6.png" class="outer" />
						<img v-bind:src="'<?php echo $image_url;?>files/images/majiang/cards'+(p.card%100)+'.png'" class="inner" />
					</div>
					<div class="card " v-bind:style="'margin-left:'+(6.6-(p.num*11))+'%;z-index: 31;'" v-show="p.type==3">
						<img src="<?php echo $image_url;?>files/images/majiang/majiang2.1.png" class="outer" />
					</div>
					<div class="cardUp" v-show="p.type==2||p.type==3" v-bind:style="'margin-left:'+(3.3-(p.num*11))+'%;'">
						<img src="<?php echo $image_url;?>files/images/majiang/majiang6.png" class="outer" />
						<img v-bind:src="'<?php echo $image_url;?>files/images/majiang/cards'+(p.card%100)+'.png'" class="inner" />
					</div>
				</div>
			</div>
			<div class="player4"  v-if="player[3].account_status==4">
				<div v-for="c in player[3].card" class="card"  v-bind:style="'margin-top:-'+(1.8*(c.num))+'%;z-index:'+(20-c.num)+';top:'+(51+player[3].pengGang.length*4.5)+'%;'" v-show="!player[3].end_show">
					<img src="<?php echo $image_url;?>files/images/majiang/majiang4.1.png" class="outer"/>
				</div>
				<div v-for="c in player[3].card" class="endCard"  v-bind:style="'margin-top:-'+(2.4*(c.num))+'%;z-index:'+(20-c.num)+';top:'+((player[3].card.length*0.7*1.6267)+45+(player[3].pengGang.length*4.5))+'%;'" v-if="player[3].end_show">
					<img src="<?php echo $image_url;?>files/images/majiang/majiang8.png" class="outer"/>
					<img v-bind:src="'<?php echo $image_url;?>files/images/majiang/cards'+(c.card%100)+'.png'" class="inner" />
					<img src="<?php echo $image_url;?>files/images/majiang/lai.png" class="lai" v-show="c.card<100" />
				</div>
				<div class="cardNew" v-if="player[3].cardNew.isShow&&!player[3].end_show" v-bind:style="'top:'+(51+player[3].pengGang.length*4.5)+'%;'">
					<img src="<?php echo $image_url;?>files/images/majiang/majiang4.1.png" class="outer" />
				</div>
				<div class="cardNew1" v-if="player[3].end_show&&player[3].cardNew.isShow&&player[3].cardNew.card!=-1" v-bind:style="'top:'+(61+player[3].pengGang.length*1.3)+'%;'">
					<img src="<?php echo $image_url;?>files/images/majiang/majiang8.png" class="outer" />
					<img v-bind:src="'<?php echo $image_url;?>files/images/majiang/cards'+(player[3].cardNew.card%100)+'.png'" class="inner" />
					<img src="<?php echo $image_url;?>files/images/majiang/lai.png" class="lai" v-show="player[3].cardNew.card<100" />
				</div>

				<div class="pengGang" v-for="p in player[3].pengGang">
					<div class="card0" v-bind:style="'margin-top: '+(p.num*8.4)+'%;'"v-show="p.type==1||p.type==2">
						<img src="<?php echo $image_url;?>files/images/majiang/majiang8.png" class="outer"  />
						<img v-bind:src="'<?php echo $image_url;?>files/images/majiang/cards'+(p.card%100)+'.png'" class="inner" />
					</div>
					<div class="card0" v-bind:style="'margin-top: '+(p.num*8.4)+'%;'" v-show="p.type==3">
						<img src="<?php echo $image_url;?>files/images/majiang/majiang10.png" class="outer"  />
					</div>
					<div class="card0" v-bind:style="'margin-top: '+(2.5+(p.num*8.4))+'%;'" v-show="p.type==1||p.type==2">
						<img src="<?php echo $image_url;?>files/images/majiang/majiang8.png" class="outer"  />
						<img v-bind:src="'<?php echo $image_url;?>files/images/majiang/cards'+(p.card%100)+'.png'" class="inner" />
					</div>
					<div class="card0" v-bind:style="'margin-top: '+(2.5+(p.num*8.4))+'%;'" v-show="p.type==3">
						<img src="<?php echo $image_url;?>files/images/majiang/majiang10.png" class="outer"  />
					</div>
					<div class="card0 cardReturn" v-bind:style="'margin-top: '+(5+(p.num*8.4))+'%;z-index: 31;'" v-show="p.step==1&&(p.type==1||p.type==2)">
						<img src="<?php echo $image_url;?>files/images/majiang/majiang8.png" class="outer" />
						<img v-bind:src="'<?php echo $image_url;?>files/images/majiang/cards'+(p.card%100)+'.png'" class="inner" />
					</div>
					<div class="card0" v-bind:style="'margin-top: '+(5+(p.num*8.4))+'%;z-index: 31;'" v-show="p.type==3">
						<img src="<?php echo $image_url;?>files/images/majiang/majiang10.png" class="outer" />
					</div>
					<div class="cardUp" v-show="p.type==2||p.type==3" v-bind:style="'margin-top: '+(1.9+(p.num*8.4))+'%;'">
						<img src="<?php echo $image_url;?>files/images/majiang/majiang8.png" class="outer" />
						<img v-bind:src="'<?php echo $image_url;?>files/images/majiang/cards'+(p.card%100)+'.png'" class="inner" />
					</div>
				</div>
			</div>
		</div>

		<!--打出去的麻将-->
		<div class="cardList" v-show="game.status==2">
			<div class="player1">
				<div v-for="c in player[0].discard" class="card" v-bind:style="'margin-left:'+((c.num)*3.1)+'%'"  v-if="c.num<=11&&c.show">
					<img src="<?php echo $image_url;?>files/images/majiang/majiang6.png" class="outer"/>
					<img v-bind:src="'<?php echo $image_url;?>files/images/majiang/cards'+(c.card%100)+'.png'" class="inner" />
				</div>
				<div v-for="c in player[0].discard" class="card" v-bind:style="'margin-left:'+((c.num-12)*3.1)+'%;margin-top: 3.8%;'" v-if="c.num>11&&c.show">
					<img src="<?php echo $image_url;?>files/images/majiang/majiang6.png" class="outer"/>
					<img v-bind:src="'<?php echo $image_url;?>files/images/majiang/cards'+(c.card%100)+'.png'" class="inner" />
				</div>
				<div class="discard" v-if="player[0].cardSet>0">
					<img src="<?php echo $image_url;?>files/images/majiang/majiang6.png" class="outer" />
					<img v-bind:src="'<?php echo $image_url;?>files/images/majiang/cards'+(player[0].cardSet%100)+'.png'" class="inner" />
				</div>
			</div>
			<div class="player2">
				<div v-for="c in player[1].discard" class="card" v-bind:style="'z-index: '+(30-c.num)+';margin-top:-'+((c.num)*2.5)+'%;'"  v-if="c.num<=11&&c.show">
					<img src="<?php echo $image_url;?>files/images/majiang/majiang8.png" class="outer"/>
					<img v-bind:src="'<?php echo $image_url;?>files/images/majiang/cards'+(c.card%100)+'.png'" class="inner" />
				</div>

				<div v-for="c in player[1].discard" class="card" v-bind:style="'z-index: '+(30-c.num)+';margin-top:-'+((c.num-12)*2.5)+'%;margin-left:4%;'" v-if="c.num>11&&c.show">
					<img src="<?php echo $image_url;?>files/images/majiang/majiang8.png" class="outer"/>
					<img v-bind:src="'<?php echo $image_url;?>files/images/majiang/cards'+(c.card%100)+'.png'" class="inner" />
				</div>

				<div class="discard" v-if="player[1].cardSet>0">
					<img src="<?php echo $image_url;?>files/images/majiang/majiang8.png" class="outer" />
					<img v-bind:src="'<?php echo $image_url;?>files/images/majiang/cards'+(player[1].cardSet%100)+'.png'" class="inner" />
				</div>
			</div>
			<div class="player3">
				<div v-for="c in player[2].discard" class="card" v-if="c.num<=11&&c.show" v-bind:style="'margin-right:'+((c.num)*3.1)+'%;z-index: 4;'">
					<img src="<?php echo $image_url;?>files/images/majiang/majiang6.png" class="outer"/>
					<img v-bind:src="'<?php echo $image_url;?>files/images/majiang/cards'+(c.card%100)+'.png'" class="inner" />
				</div>
				<div v-for="c in player[2].discard" class="card" v-if="c.num>11&&c.show" v-bind:style="'margin-right:'+((c.num-12)*3.1)+'%;margin-bottom: 3.8%;z-index:3;'">
					<img src="<?php echo $image_url;?>files/images/majiang/majiang6.png" class="outer"/>
					<img v-bind:src="'<?php echo $image_url;?>files/images/majiang/cards'+(c.card%100)+'.png'" class="inner" />
				</div>
				<div class="discard" v-if="player[2].cardSet>0">
					<img src="<?php echo $image_url;?>files/images/majiang/majiang6.png" class="outer" />
					<img v-bind:src="'<?php echo $image_url;?>files/images/majiang/cards'+(player[2].cardSet%100)+'.png'" class="inner" />
				</div>
			</div>
			<div class="player4">
				<div v-for="c in player[3].discard" class="card"v-if="c.num<=11&&c.show" v-bind:style="'margin-top:'+((c.num)*2.5)+'%;margin-left:-4%;'">
					<img src="<?php echo $image_url;?>files/images/majiang/majiang8.png" class="outer"/>
					<img v-bind:src="'<?php echo $image_url;?>files/images/majiang/cards'+(c.card%100)+'.png'" class="inner" />
				</div>
				<div v-for="c in player[3].discard" class="card" v-if="c.num>11&&c.show" v-bind:style="'margin-top:'+((c.num-12)*2.5)+'%;margin-left:-8%;'">
					<img src="<?php echo $image_url;?>files/images/majiang/majiang8.png" class="outer"/>
					<img v-bind:src="'<?php echo $image_url;?>files/images/majiang/cards'+(c.card%100)+'.png'" class="inner" />
				</div>
				<div class="discard" v-if="player[3].cardSet>0">
					<img src="<?php echo $image_url;?>files/images/majiang/majiang8.png" class="outer" />
					<img v-bind:src="'<?php echo $image_url;?>files/images/majiang/cards'+(player[3].cardSet%100)+'.png'" class="inner" />
				</div>
			</div>
		</div>
		<!--最后一张牌标记-->
		<div class="currentCard"  >
			<div  class="player0" v-bind:style="'margin-left:'+((player[0].discard.length-1)%12*3.1)+'%;z-index: 31;margin-top: '+(player[0].discard.length>12?3.8:0)+'%;'" v-if="game.last_user==player[0].account_id&&player[0].discard.length>0">
				<img src="<?php echo $image_url;?>files/images/majiang/currentCard.gif" class="currentSign" v-if="player[0].discard[player[0].discard.length-1].show" />
			</div>
			<div  class="player1" v-bind:style="'margin-top:-'+((player[1].discard.length-1)%12*2.5)+'%;z-index: 31;margin-left: '+(player[1].discard.length>12?4:0)+'%;'" v-if="game.last_user==player[1].account_id&&player[1].discard.length>0">
				<img src="<?php echo $image_url;?>files/images/majiang/currentCard.gif" class="currentSign" v-if="player[1].discard[player[1].discard.length-1].show"/>
			</div>
			<div  class="player2" v-bind:style="'margin-right:'+((player[2].discard.length-1)%12*3.1)+'%;z-index: 31;margin-bottom: '+(player[2].discard.length>12?7.6:3.8)+'%;'" v-if="game.last_user==player[2].account_id&&player[2].discard.length>0">
				<img src="<?php echo $image_url;?>files/images/majiang/currentCard.gif" class="currentSign" v-if="player[2].discard[player[2].discard.length-1].show"/>
			</div>
			<div  class="player3" v-bind:style="'margin-top:'+((player[3].discard.length-1)%12*2.5)+'%;z-index: 31;margin-left: '+(player[3].discard.length>12?-8:-4)+'%;'" v-if="game.last_user==player[3].account_id&&player[3].discard.length>0">
				<img src="<?php echo $image_url;?>files/images/majiang/currentCard.gif" class="currentSign" v-if="player[3].discard[player[3].discard.length-1].show"/>
			</div>
		</div>
		<!--碰杠胡-->
		<div class="wordShow">
			<div v-for="p in player">
				<img src="<?php echo $image_url;?>files/images/majiang/zifront.png" v-bind:class="'zi'+p.num+' animate1'" v-if="p.zi==1" />
				<img src="<?php echo $image_url;?>files/images/majiang/peng2.png" v-bind:class="'zi'+p.num+' animate'" v-if="p.zi==1" />
				<img src="<?php echo $image_url;?>files/images/majiang/zifront.png" v-bind:class="'zi'+p.num+' animate1'" v-if="p.zi==2" />
				<img src="<?php echo $image_url;?>files/images/majiang/gang2.png" v-bind:class="'zi'+p.num+' animate'" v-if="p.zi==2" />
				<img src="<?php echo $image_url;?>files/images/majiang/zifront.png" v-bind:class="'zi'+p.num+' animate1'" v-if="p.zi==3" />
				<img src="<?php echo $image_url;?>files/images/majiang/hu2.png" v-bind:class="'zi'+p.num+' animate'" v-if="p.zi==3" />
				<div v-bind:class="'gangScore gangScore'+p.num" v-if="game.showGangScore&&p.gangScore!=0">
					<a v-if="p.gangScore>0" style="color: #fedb4d;text-shadow: 2px 2px 2px #9c3600;">+{{p.gangScore}}</a>
					<a v-if="p.gangScore<0" style="color:#b3dbfe;text-shadow: 2px 2px 2px #103a5f;">{{p.gangScore}}</a>
				</div>
			</div>
		</div>


	<!-- 消息  -->
		<div class="message" v-show="isShowMessage" >
			<div class="messageBack" v-on:click="hideMessage()"></div>
			<div class="textPart">
				<div class="outline"></div>
				<div  class="textList" id="message">
					<div class="textItem" v-for="m in message" v-on:click="messageOn(m.num)">{{m.text}}</div>
					<div class="textItem" style="height: 5px;background: #434547;"></div>
				</div>
			</div>
		</div>
	</div>

	<!-- 临时积分榜  -->
	<div class="roundPause1" id="roundPause1" style="z-index: 70;display: none;">
		<img    src="" style="width: 100%;position: absolute;top:0;left: 0;height: 100%;" id="roundPause2"  />
		<div v-for="p in player"  v-bind:class="'playerStatus playerStatus' + p.num">
			<img    src="<?php echo $image_url;?>files/images/common/ready_text.png" v-show="p.account_status==2"/>
			<img    src="<?php echo $image_url;?>files/images/common/offline_text.png" v-show="p.online_status==0"/>
		</div>
		<div class="release">三分钟未开局，房间自动解散<a>{{game.countdown}}s</a></div>
		<div class="button buttonRight" >
			<img    src="<?php echo $image_url;?>files/images/majiang/rank_btn2.png"/>
			<div class="text" v-show="player[0].account_status!=2&&game.round!=game.total_num" v-on:click="nextRound(0)">下一局</div>
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

	<!-- 提示  -->
	<div class="alert" v-show="isShowAlert">
		<div class="alertBack"></div>
		<div class="mainPart">
			<div class="backImg">
				<div class="blackImg"></div>
			</div>
			<div class="alertText" >{{alertText}}</div>
			<div v-show="alertType==1">
				<div class="buttonMiddle" v-on:click="closeAlert()">确定</div>
			</div>
			<div v-show="alertType==2">
				<div class="buttonMiddle" v-on:click="home()">创建房间</div>
			</div>
			<div v-show="alertType==3">
				<div class="buttonLeft" v-on:click="home()">返回首页</div>
				<div class="buttonRight" v-on:click="closeAlert()">取消</div>
			</div>
			<div v-show="alertType==4">
				<div class="buttonLeft" v-on:click="home()">创建房间</div>
				<div class="buttonRight" v-on:click="sitDown()">加入游戏</div>
			</div>
			<div v-show="alertType==5">
				<div class="buttonMiddle" v-on:click="getCards()">领取</div>
			</div>
			<div v-show="alertType==6">
				<div class="buttonMiddle" v-on:click="closeAlert()">确定</div>
			</div>
			<div v-show="alertType==7">
				<div class="buttonMiddle" v-on:click="home()">返回首页</div>
			</div>
			<div v-show="alertType==8">
				<div class="buttonMiddle" v-on:click="closeAlert()">确定</div>
			</div>
			<div v-show="alertType==11">
				<div class="buttonMiddle" v-on:click="closeAlert()">知道了</div>
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
				<div class="buttonMiddle" v-on:click="closeAlert()">确定</div>
			</div>
			<div v-show="alertType==32">
				<div class="buttonMiddle" v-on:click="reloadView()">重新登录</div>
			</div>
		</div>
	</div>

</div>


<div class="outPart" style="display: none;">

<!-- 临时积分榜  -->
	<div class="roundPause" id="roundPause" style="z-index: 0;display: none;">
		<img    src="<?php echo $image_url;?>files/images/majiang/rank_frame2.png" class="title">
		<div class="time" ><a >{{playerBoard.record}}</a></div>
		<div class="infoPart">
			<div class="rule">
				<div style="width: 15%;"></div>
				<div style="width: 24%;text-align: left;overflow: hidden;">名称</div>
				<div>总积分</div>
				<div>本局积分</div>
				<div>状态</div>
			</div>
			<div class="positionList" v-for="p in playerBoard.score" v-bind:class="{true: 'scoresItemWin', false: 'scoresItemLose'}[p.account_score>0]">
				<div style="width: 12%;"><img    src="<?php echo $image_url;?>files/images/common/rank_bigwinner.png" v-show="game.maxWin==p.account_score"></div>
				<div style="width: 27%;text-align: left;overflow: hidden;word-break: keep-all;white-space:nowrap;">{{p.nickname}}</div>
				<div>{{p.account_score}}</div>
				<div>{{p.score_summary}}</div>
				<div></div>
			</div>
		</div>
		<div class="button buttonLeft">
			<img    src="<?php echo $image_url;?>files/images/majiang/rank_btn1.png"/>
			<div class="text">长按发送</div>
		</div>
	</div>



<!-- 排行  -->
	<div class="ranking" id="ranking" style="display: none;">
		<img    src="<?php echo $image_url;?>files/images/majiang/rank_bg.jpg" class="rankBack" />
		<div class="rankText">
			<img    src="<?php echo $image_url;?>files/images/majiang/rank_frame.png" class="title" />
			<div class="time" ><a style="font-size: 6vw;background: #6b3f13;border-radius: 6vw;">房间号：<?php echo $room_number;?>&nbsp&nbsp&nbsp&nbsp{{playerBoard.record}}</a></div>

			<div v-for="p in playerBoard.score" class="scoresItem" v-bind:class="{true: 'backB', false: 'backY'}[p.account_score>0]" v-show="p.account_id>0">
				<img    src="<?php echo $image_url;?>files/images/common/rank_bigwinner.png" v-show="game.maxWin==p.account_score">
				<div class="name">{{p.nickname}}</div>
				<div class="currentScores" ><a v-show="p.account_score>0" style="color:#000;">+</a>{{p.account_score}}</div>
			</div>
		</div>
		<img    src="<?php echo $image_url;?>files/images/common/rank_save.png" class="button" />
	</div>
<!-- end图片  -->
	<div class="end" style="position: fixed;width: 100%;height:100%;top:0;left:0;z-index: 140;display: none;">
		<img src="" style="width: 100%;position: absolute;top:0;left: 0;height: 100%;" id="end"  />
	</div>

	 <!-- 绑定手机号码 -->
		<div id="validePhone" style="display: none;" v-show="isAuthPhone==1">
			<div class="phoneMask" style="position: fixed;z-index: 98;top: 0;left: 0;width: 100%;height: 100%;background-color: rgba(0,0,0,0.5);" ></div>
			<div class="phoneFrame" style="position: fixed;z-index: 99;width: 80vw;max-width: 80vw; top: 50%; left: 50%;-webkit-transform:translate(-50%,-60%); background-color: #fff; text-align: center; border-radius: 8px; overflow: hidden;opacity: 1; color: white;">
			    <div style="height: 2.2vw;"></div>
				<!-- <div style="padding: 1vw;font-size: 4vw; line-height: 5vw; word-wrap: break-word;word-break: break-all;color: #000;background-color: white;">
					{{phoneText}}
				</div> -->
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
	<div style="display: none;" v-if="game_staus!=4">
	<img src="<?php echo $image_url;?>files/images/majiang/cards21.png">
	<img src="<?php echo $image_url;?>files/images/majiang/cards22.png">
	<img src="<?php echo $image_url;?>files/images/majiang/cards23.png">
	<img src="<?php echo $image_url;?>files/images/majiang/cards24.png">
	<img src="<?php echo $image_url;?>files/images/majiang/cards25.png">
	<img src="<?php echo $image_url;?>files/images/majiang/cards26.png">
	<img src="<?php echo $image_url;?>files/images/majiang/cards27.png">
	<img src="<?php echo $image_url;?>files/images/majiang/cards28.png">
	<img src="<?php echo $image_url;?>files/images/majiang/cards29.png">

	<img src="<?php echo $image_url;?>files/images/majiang/cards41.png">
	<img src="<?php echo $image_url;?>files/images/majiang/cards42.png">
	<img src="<?php echo $image_url;?>files/images/majiang/cards43.png">
	<img src="<?php echo $image_url;?>files/images/majiang/cards44.png">
	<img src="<?php echo $image_url;?>files/images/majiang/cards45.png">
	<img src="<?php echo $image_url;?>files/images/majiang/cards46.png">
	<img src="<?php echo $image_url;?>files/images/majiang/cards47.png">
	<img src="<?php echo $image_url;?>files/images/majiang/cards48.png">
	<img src="<?php echo $image_url;?>files/images/majiang/cards49.png">

	<img src="<?php echo $image_url;?>files/images/majiang/cards61.png">
	<img src="<?php echo $image_url;?>files/images/majiang/cards62.png">
	<img src="<?php echo $image_url;?>files/images/majiang/cards63.png">
	<img src="<?php echo $image_url;?>files/images/majiang/cards64.png">
	<img src="<?php echo $image_url;?>files/images/majiang/cards65.png">
	<img src="<?php echo $image_url;?>files/images/majiang/cards66.png">
	<img src="<?php echo $image_url;?>files/images/majiang/cards67.png">
	<img src="<?php echo $image_url;?>files/images/majiang/cards68.png">
	<img src="<?php echo $image_url;?>files/images/majiang/cards69.png">

	<img src="<?php echo $image_url;?>files/images/majiang/cards80.png">
	<img src="<?php echo $image_url;?>files/images/majiang/cards83.png">
	<img src="<?php echo $image_url;?>files/images/majiang/cards86.png">
	<img src="<?php echo $image_url;?>files/images/majiang/cards89.png">
	<img src="<?php echo $image_url;?>files/images/majiang/cards93.png">
	<img src="<?php echo $image_url;?>files/images/majiang/cards96.png">
	<img src="<?php echo $image_url;?>files/images/majiang/cards99.png">
</div>
</div>

</div>

<script type="text/javascript" src="<?php echo $file_url;?>files/js/canvas.js" ></script>

<script src="https://res.wx.qq.com/open/js/jweixin-1.0.0.js" ></script>
<script type="text/javascript" src="<?php echo $file_url;?>files/js/jquery-1.9.1.min.js"></script>
<script type="text/javascript" src="<?php echo $file_url;?>files/js/vue.min.js" ></script>
<script type="text/javascript" src="<?php echo $file_url;?>files/js/vue-resource.min.js" ></script>
<script type="text/javascript" src="<?php echo $base_url;?>files/js/majiang.js" defer></script>

</body>
</html>
