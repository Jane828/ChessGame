<html>
<head>
<meta charset="utf-8" >
<meta name="viewport" content="width=device-width,initial-scale=1.0, minimum-scale=1.0, maximum-scale=1.0, user-scalable=no"/>
<meta name="format-detection" content="telephone=no" />
<meta name="msapplication-tap-highlight" content="no" />
<title>斗地主房间<?php echo $room_number;?></title>
<link rel="stylesheet" href="<?php echo $image_url;?>files/css/landlord.css">
<link rel="stylesheet" href="<?php echo $image_url;?>files/css/alert.css">
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
		"room_status": "<?php echo $room_status;?>",
		"balance_scoreboard": '<?php echo $balance_scoreboard;?>',
		"session":'<?php echo "$session"?>',
		"httpUrl":'<?php echo "$http_url"?>',
		"shareTitle":"斗地主",
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


<body id="body">

	<style type="text/css">
		body.modal-show {position: fixed;width: 100%;}
	</style>

    <!-- loading -->
	<div style="position: fixed;width:100%;height:100%;top:0;left:0;background: #000;z-index: 139;" id="loading">
		<div class="load4">
			<div class="loader">Loading...</div>
		</div>
	</div>

	<div class="main" id="app-main"  style="display: none;">
	    <!-- 背景图片 -->
		<img class="tableBack"  style="top: 0;left: 0;width: 100vw;height: 100vh;" src="<?php echo $image_url;?>files/images/landlord/table.jpg"  />

		<!-- 我的房卡 -->
		<div class="roomCard" >
			<img  src="<?php echo $image_url;?>files/images/common/ticket.png" />
			<div class="num">
				<div class="back"></div>
				<div class="text">{{userInfo.card}}张</div>
			</div>
		</div>

		<img class="landlordTitle"  style="top: 40vh;left: 50vw;" src="<?php echo $image_url;?>files/images/landlord/table_title.png" />

		<div class="disconnect" v-show="!connectOrNot" style="position: fixed;top:45%;left: 0;width: 100%;text-align: center;z-index: 101">
			<div style="width: 250px;height:27px;position: absolute;top:-2;left: 50%;margin-left: -125px;background: #000;opacity: .5;border-radius:15px;">
			</div>
			<a style="font-size: 16px;color: #fff;padding: 5px 14px;position:relative;">已断开连接，正在重新连接...</a>
		</div>

		<!-- footer -->
		<div class="bottom">
			<img class="bottomBack"  src="<?php echo $image_url;?>files/images/landlord/footer.jpg"  />
			<img class="bottomButton bottomGameMessage" src="<?php echo $image_url;?>files/images/common/icon_message.png" v-on:click="showMessage">
			<div class="mine">
				<img v-bind:src="player[0].headimgurl" v-show="player[0].account_status!=4&&player[0].account_status!=5" />
				<img src="<?php echo $image_url;?>files/images/landlord/avatar_farmer1.png"  v-show="player[0].account_status==4"/>
				<img src="<?php echo $image_url;?>files/images/landlord/avatar_landlord1.png"  v-show="player[0].account_status==5"/>
				<div class="name">{{player[0].nickname}}</div>
				<div class="score">{{player[0].account_score}}</div>
			</div>
		</div>

		<img class="bottomButton bottomGameRule" src="<?php echo $image_url;?>files/images/common/icon_rule.png" v-on:click="showRull">
		<img class="bottomButton bottomGameHistory" src="<?php echo $image_url;?>files/images/common/icon_sound.png" v-on:click="showAudioSetting">

		<div v-show="cardNumShow>0" class="cardsTurnBack"></div>

		<!-- 桌子 -->
		<div  class="table">

			<!-- 地主牌 -->
			<div class="round">
				<div class="playRound">{{game.round}}&nbsp/&nbsp{{game.total_num}}&nbsp局</div>
				<div class="cardOver" v-show="game.landlord_card.length>0">
					<div class="cards card0">
						<div class="face front"></div>
						<div class="face back" v-bind:class="'card' + game.landlord_card[0]"></div>
					</div>
					<div class="cards card1">
						<div class="face front"></div>
						<div class="face back" v-bind:class="'card' + game.landlord_card[1]"></div>
					</div>
					<div class="cards card2">
						<div class="face front"></div>
						<div class="face back" v-bind:class="'card' + game.landlord_card[2]"></div>
					</div>

				</div>
				<div class="magnification" v-show="game.landlord_card.length>0">{{rullInfo.base_score}}X{{game.multiple}}</div>
			</div>

			<div class="restPart" v-on:click="cardsDown"></div>

			<!-- 玩家  -->
			<div  v-for="p in player" class="member" v-bind:class="'member' + p.num" v-if="p.account_id>0&&p.num!=1">

				<!-- 玩家信息 -->
				<div class="memberInfo">
					<img v-bind:src="p.headimgurl" v-show="p.account_status!=4&&p.account_status!=5"/>
					<img src="<?php echo $image_url;?>files/images/landlord/avatar_farmer1.png"  v-if="p.account_status==4&&p.num==3"/>
					<img src="<?php echo $image_url;?>files/images/landlord/avatar_landlord1.png"  v-if="p.account_status==5&&p.num==3"/>
					<img src="<?php echo $image_url;?>files/images/landlord/avatar_farmer2.png"  v-if="p.account_status==4&&p.num==2"/>
					<img src="<?php echo $image_url;?>files/images/landlord/avatar_landlord2.png"  v-if="p.account_status==5&&p.num==2"/>

					<div class="quitBack" v-show="p.account_status>0&&p.online_status==0" ></div>
					<div class="text">
						<div class="back"></div>
						<div class="name">{{p.nickname}}</div>
						<div class="score">{{p.account_score}}</div>
					</div>
				</div>

				<!-- 玩家牌数 -->
				<div class="cardsNum" v-show="p.cardsNum>0&&p.account_status!=6">
					<img src="<?php echo $image_url;?>files/images/common/card1.png"  />
					<div class="text" >{{p.cardsNum}}</div>
				</div>

				<!-- 准备 -->
				<img src="<?php echo $image_url;?>files/images/common/ready_text.png" class="imReady" v-show="p.account_status==2&&game.round==0" />

				<!-- 倒计时 -->
				<div class="clock" v-show="p.playing_status==2||p.playing_status==3">
					{{p.limit_time}}
				</div>

				<div class="cardShow" v-show="p.tempCards.length>0&&((p.playing_status==1&&p.tempCards[0]!=0&&p.account_status!=6)||p.cardsNum==0)">
					<div v-for="(t,index) in p.tempCards" class="card" v-bind:class="'card' + t"  v-if="index<7"></div>
					<div v-for="(t,index) in p.tempCards" class="card" v-bind:class="'card' + t" v-if="index>6&&index<14" style="margin-top: -14px;"></div>
					<div v-for="(t,index) in p.tempCards" class="card" v-bind:class="'card' + t" v-if="index>13" style="margin-top: -14px;"></div>
					<img  v-bind:src="'<?php echo $image_url;?>files/images/landlord/feiji' + p.num + '.png'" class="feiji" v-bind:class="'feiji' + p.num" />
				</div>

				<!-- 不要 -->
				<div class="cardPass" v-show="p.tempCards.length==0&&p.playing_status==1&&p.account_status!=6">
					<img src="<?php echo $image_url;?>files/images/landlord/text_buyao.png" />
				</div>


				<div class="cardShow" v-show="p.account_status==6&&p.cardsNum>0" >
					<div style="height:30px;width:126px;float: left;">
						<div v-for="(t,index) in p.cards" v-bind:class="'card card' + t + '  row1'"  v-if="index<7" style="margin-left: -36px;"></div>
					</div>
					<div style="height:30px;width:126px;float: left;">
						<div v-for="(t,index) in p.cards" v-bind:class="'card card' + t + ' row2'"  v-if="index>6&&index<14" style="margin-left: -36px;"></div>
					</div>
					<div style="width:126px;float: left;">
						<div v-for="(t,index) in p.cards" v-bind:class="'card card' + t + '  row3'"  v-if="index>13" style="margin-left: -36px;"></div>
					</div>
					</div>
			</div>

			<!--  按钮提示  -->
			<div class="buttonAndCards" >

				<div class="cardShow" v-show="(player[0].account_status>3&&player[0].playing_status==1)||player[0].account_status==6">
					<a v-for="t in player[0].tempCards"  v-bind:class="'card card' + t" ></a>
					<a class="cardPass" v-show="player[0].tempCards.length==0" >
						<img src="<?php echo $image_url;?>files/images/landlord/text_buyao.png" />
					</a>
					<img  src="<?php echo $image_url;?>files/images/landlord/feiji3.png" class="feiji feiji1" />
				</div>

				<!-- 按钮 -->
				<div class="buttonParts" v-show="timeOut==0">
					<div class="clock" v-show="player[0].playing_status==2||player[0].playing_status==3">
						{{player[0].limit_time}}
					</div>

					<div class="landlordsPart" v-show="player[0].playing_status==2">
						<div class="buttonType1" v-on:click="sendMessage(0,1)">
							<img src="<?php echo $image_url;?>files/images/landlord/button_buyao.png" />
						</div>
						<div class="buttonType1" style="float: right" v-on:click="sendMessage(1,1)">
							<img src="<?php echo $image_url;?>files/images/landlord/button_yaodizhu.png" />
						</div>
					</div>

					<div class="operatePart" v-show="player[0].playing_status==3">
						<div v-if="player[0].tips.length>0&&game.current_card_user!=player[0].account_id&&game.current_card_user!=-1">
							<div class="buttonType2" v-on:click="sendMessage(0,2)">
								<img src="<?php echo $image_url;?>files/images/landlord/button_buchu.png" />
							</div>
							<div class="buttonType2" style="margin-left: 2vh" v-on:click="noteCards">
								<img src="<?php echo $image_url;?>files/images/landlord/button_tishi.png" />
							</div>
							<div class="buttonType2" style="margin-left: 2vh;" v-on:click="sendMessage(1,2)">
								<img src="<?php echo $image_url;?>files/images/landlord/button_chu.png" />
							</div>
						</div>
						<div v-if="player[0].tips.length==0&&game.current_card_user!=player[0].account_id">
							<div class="buttonType2" v-on:click="sendMessage(0,2)" style="margin-left: 15.5vh;">
								<img src="<?php echo $image_url;?>files/images/landlord/button_pass.png" />
							</div>
						</div>
						<div v-if="game.current_card_user==player[0].account_id || game.current_card_user==-1">
							<div class="buttonType2" style="margin-left: 17.3vh;" v-on:click="sendMessage(1,2)">
								<img src="<?php echo $image_url;?>files/images/landlord/button_chu.png" />
							</div>
						</div>
					</div>

					<div class="readyPart" v-show="player[0].account_status==1||player[0].account_status==0||player[0].account_status==2">
						<div class="buttonType3"  v-show="(player[0].account_status==1||player[0].account_status==0)&&game.round==0" v-on:click="imReady">
							<img src="<?php echo $image_url;?>files/images/landlord/button_ready.png" />
						</div>
						<div class="buttonType3"  v-show="player[0].account_status==2" >
							<img src="<?php echo $image_url;?>files/images/common/ready_text.png" style="height: 3.5vh;" />
						</div>
					</div>
				</div>
			</div>

			<!--  我的手牌  -->
			<div class="myCards" id="myCards" style="height:0;" v-show="cardList.length>0">

				<div style="width:292px;height:0;padding-left:28px;margin-bottom: 92px;" >
					<div style="float: right;" >
						<div v-for="c in cardList" class="cardItem myCard"  v-bind:style="'z-index: ' + c.z_index +';'"  v-if="cardList.length>10&&c.num<(cardList.length-10)" v-bind:class="{true: 'isSelect  myCard' + c.card, false: 'notSelect myCard' + c.card}[c.isSelect==true]" v-show="c.num>=cardNumShow-10" v-bind:data-num="c.num">
							<div  v-bind:class="{true: 'isChoose', false: 'notChoose'}[c.isChoose]"></div>
						</div>
					</div>
				</div>

                <!-- {true: 'isSelect', false: 'notSelect'}[c.isSelect] -->
				<div v-bind:style="'width: ' + (cardList.length>10?292:cardList.length*30) + 'px;height:0;padding-left:28px;margin: 0 auto;margin-top:18px;'">
					<div style="float: right;">
						<div class="cardItem myCard" v-for="c in cardList" v-bind:style="'z-index: ' + c.z_index" v-if="c.num>(cardList.length-11)"  v-bind:class="{true: 'isSelect myCard' + c.card, false: 'notSelect myCard' + c.card}[c.isSelect==true]"  v-show="c.num>=cardNumShow"  v-bind:data-num="c.num">
							<div  v-bind:class="{true: 'isChoose', false: 'notChoose'}[c.isChoose==true]"></div>
						</div>
					</div>
				</div>
				<div class="cardText">{{game.cardText}}</div>
			</div>



			<!--  消息  -->
			<div v-for="p in player">
				<div v-bind:class="'messageSay messageSay' + p.num" v-show="p.messageOn">
					<div class="text">{{p.messageText}}</div>
					<div class="triangle"> </div>
				</div>
			</div>
		</div>

		<!-- 排行  -->
		<div  class="ranking" id="ranking">
			<img   src="<?php echo $image_url;?>files/images/landlord/rank_bg.jpg" class="rankBack" />
			<div class="rankText">
				<img   src="<?php echo $image_url;?>files/images/landlord/rank_frame.png" class="title" />
				<div class="time" ><a style="font-size: 6vw;background: #243756;border-radius: 6vw;border:2px solid #7dfffd;">房间号：<?php echo $room_number;?>&nbsp&nbsp&nbsp&nbsp{{playerBoard.record}}</a></div>

				<div style="height: 2.5vw;"></div>
				<div v-for="p in playerBoard.score" class="scoresItem" v-bind:class="{true: 'scoresItemYellow', false: 'scoresItemWhite'}[p.account_score>0]" v-show="p.account_id>0">
					<img   src="<?php echo $image_url;?>files/images/common/rank_bigwinner.png" class="bigwinner" v-show="game.maxWin==p.account_score">
					<div class="name">{{p.nickname}}</div>
					<div class="currentScores" ><a v-show="p.account_score>0" style="color:#f7d92b;">+</a>{{p.account_score}}</div>
				</div>
			</div>
			<img   src="<?php echo $image_url;?>files/images/common/rank_save.png" class="button" />
		</div>

		<!-- 规则 -->
		<div class="createRoom" v-show="isShowRull"  v-on:click="closeRull">
			<div class="createRoomBack"></div>
			<div class="mainPart" >
				<div  class="createB"></div>
				<div class="createTitle">
					<img src="<?php echo $image_url;?>files/images/common/txt_rule.png" />
				</div>
				<div class="blueBack">
					<div class="selectPart" style="top: 0px;height:28px;line-height:28px;">
						<div class="selectTitle" style="width: 100%;font-size: 13px; text-align: center;color: #efd0a4; background-color: #4e3f79;opacity: 1.0">游戏未进行,房卡退还</div>
					</div>
					<div class="landlordRull">
						<div class="selectPart" style="height:36px;line-height:36px;padding:6px 0;">
							<div class="selectTitle">底分：</div>
							<div class="selectList" >
								<div class="selectItem"  v-show="rullInfo.base_score==1">
									<div class="selectText">1分</div>
								</div>
								<div class="selectItem" v-show="rullInfo.base_score==5" >
									<div class="selectText">5分</div>
								</div>
								<div class="selectItem" v-show="rullInfo.base_score==10">
									<div class="selectText">10分</div>
								</div>
							</div>
						</div>
						<div class="selectPart" style="height:36px;line-height:36px;padding:6px 0;">
							<div class="selectTitle">规则：</div>
							<div class="selectList">
								<div class="selectItem" v-show="rullInfo.ask_mode==1">
									<div class="selectText">轮流问地主</div>
								</div>
								<div class="selectItem" v-show="rullInfo.ask_mode==2" >
									<div class="selectText">随机问地主</div>
								</div>
							</div>
						</div>
						<div class="selectPart" style="height:36px;line-height:36px;padding:6px 0;">
							<div class="selectTitle">局数：</div>
							<div class="selectList">
								<div class="selectItem"  v-show="rullInfo.ticket_count==1">
									<div class="selectText" >6局X2张房卡</div>
								</div>
								<div class="selectItem" v-show="rullInfo.ticket_count==2">
									<div class="selectText" >12局X4张房卡</div>
								</div>
							</div>
						</div>
					</div>
				</div>
			</div>
		</div>

		<!-- 春天 -->
		<div class="spring" v-show="isShowSpring">
			<div class="springBack"></div>
			<div class="springMain">
				<img  src="<?php echo $image_url;?>files/images/landlord/spring.png" style="width: 100%;" />
				<img class="springFall4" src="<?php echo $image_url;?>files/images/landlord/spring3.png" />
				<img class="springFall1" src="<?php echo $image_url;?>files/images/landlord/spring1.png" />
				<img class="springFall2" src="<?php echo $image_url;?>files/images/landlord/spring2.png" />
				<img class="springFall3" src="<?php echo $image_url;?>files/images/landlord/spring1.png" />
			</div>
		</div>

		<!-- 炸弹 -->
		<div class="bomb" v-show="isShowBomb">
			<img class="bomb1" src="<?php echo $image_url;?>files/images/landlord/bomb1.png" />
			<img class="bomb2" src="<?php echo $image_url;?>files/images/landlord/bomb2.png"  />
			<img class="bomb3" src="<?php echo $image_url;?>files/images/landlord/bomb3.png"  />
		</div>

		<!--  提示框  -->
		<div class="alert" v-show="isShowAlert">
			<div class="alertBack"></div>
			<div class="mainPart">
				<div class="backImg">
					<div class="blackImg"></div>
				</div>
				<div class="alertText" >{{alertText}}</div>

				<div v-show="alertType==1">
					<div class="buttonMiddle" v-on:click="home">返回首页</div>
				</div>
				<div v-show="alertType==6">
					<div class="buttonMiddle" v-on:click="closeAlert">确定</div>
				</div>
				<div v-show="alertType==2">
					<div class="buttonMiddle" v-on:click="home">创建房间</div>
				</div>
				<div v-show="alertType==3">
					<div class="buttonLeft" v-on:click="home">返回首页</div>
					<div class="buttonRight" v-on:click="closeAlert">取消</div>
				</div>
				<div v-show="alertType==4">
					<div class="buttonLeft" v-on:click="home">创建房间</div>
					<div class="buttonRight" v-on:click="sitDown">加入游戏</div>
				</div>
				<div v-show="alertType==5">
					<div class="buttonMiddle" v-on:click="getCards">领取</div>
				</div>
				<div v-show="alertType==7">
					<div class="buttonMiddle" v-on:click="home">返回首页</div>
				</div>
				<div v-show="alertType==8">
					<div class="buttonMiddle" v-on:click="closeAlert">确定</div>
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
        <div class="end" style="position: fixed;width: 100vw;height:100vh;top:0;left:0;z-index: 140;background: #000;display: none;">
        	<img src="" style="width: 100%;position: absolute;top:0;left: 0;height: 100%;" id="end" />
        </div>

		<!-- 临时积分榜 -->
		<div class="roundPause1" id="roundPause1" style="z-index: 100;display: none;">
			<img  src="" style="width: 100%;position: absolute;top:0;left: 0;height: 100%;" id="roundPause2"  />
			<div class="mainPart">
				<div v-for="p in player" v-bind:class="'playerStatus playerStatus' + p.num">
					<img   src="<?php echo $image_url;?>files/images/common/ready_text.png" v-show="p.account_status==2"/>
					<img   src="<?php echo $image_url;?>files/images/common/offline_text.png" v-show="p.online_status==0"/>
				</div>
				<div class="release">三分钟未开局，房间自动解散<a>{{game.countdown}}s</a></div>
				<div class="button buttonRight" v-show="player[0].account_status!=2&&game.round!=game.total_num"  >
					<img  src="<?php echo $image_url;?>files/images/common/button2.png"/>
					<div class="text" v-on:click="newReady">下一局</div>
				</div>
			</div>
		</div>

		<div class="roundPause" id="roundPause" style="z-index:0;display: none;">
			<div class="lvBack"></div>
			<div class="mainPart">
				<img  src="<?php echo $image_url;?>files/images/landlord/rank_frame2.png" class="box">
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
						<div style="width: 15%;"><img  src="<?php echo $image_url;?>files/images/common/rank_bigwinner.png" v-if="game.maxWin==p.account_score"></div>
						<div style="width: 24%;text-align: left;overflow: hidden;word-break: keep-all;white-space:nowrap; ">{{p.nickname}}</div>
						<div>{{p.account_score}}</div>
						<div>{{p.score_summary}}</div>
						<div></div>
					</div>
				</div>
				<div class="button buttonLeft">
					<img  src="<?php echo $image_url;?>files/images/common/button1.png"/>
					<div class="text">长按发送</div>
				</div>
				<div class="button buttonRight" style="display: none;">
					<img  src="<?php echo $image_url;?>files/images/common/button2.png"/>
					<div class="text">继续</div>
				</div>
			</div>
			<img  src="<?php echo $image_url;?>files/images/landlord/rank_landlord1.png" v-if="playerBoard.score[0].score_summary>0&&tempStatus==5" class="title" />
			<img  src="<?php echo $image_url;?>files/images/landlord/rank_landlord2.png" v-if="playerBoard.score[0].score_summary>0&&tempStatus==4" class="title"/>

			<img  src="<?php echo $image_url;?>files/images/landlord/rank_landlord3.png" v-if="playerBoard.score[0].score_summary<0&&tempStatus==5" class="title" />
			<img  src="<?php echo $image_url;?>files/images/landlord/rank_landlord4.png" v-if="playerBoard.score[0].score_summary<0&&tempStatus==4" class="title" />
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

	</div>
	<div class="outer" style="display: none;">

	</div>
</body>

<script type="text/javascript" src="<?php echo $image_url;?>files/js/canvas.js"></script>


<script src="https://res.wx.qq.com/open/js/jweixin-1.0.0.js" ></script>
<script type="text/javascript" src="<?php echo $image_url;?>files/js/jquery-1.9.1.min.js"></script>
<script type="text/javascript" src="<?php echo $image_url;?>files/js/bscroll.js"></script>
<script type="text/javascript" src="<?php echo $image_url;?>files/js/velocity.js"></script>
<script type="text/javascript" src="<?php echo $image_url;?>files/js/vue.min.js" ></script>
<script type="text/javascript" src="<?php echo $image_url;?>files/js/vue-resource.min.js" ></script>
<script type="text/javascript" src="<?php echo $base_url;?>files/js/landlord.js"></script>

</html>
