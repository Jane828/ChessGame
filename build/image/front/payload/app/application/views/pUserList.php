<html>
<head>
<meta charset="utf-8" >
<meta name="viewport" content="width=device-width,initial-scale=1.0, minimum-scale=1.0, maximum-scale=1.0, user-scalable=no"/>
<meta name="format-detection" content="telephone=no" />
<meta name="msapplication-tap-highlight" content="no" />
<title>用户列表</title>

<script type="text/javascript" src="<?php echo $base_url;?>files/transfer/fastclick.js?_version=<?php echo $front_version;?>"></script>

<link rel="stylesheet" type="text/css" href="<?php echo $base_url;?>files/transfer/bull_vue-1.0.0.css">
<link rel="stylesheet" type="text/css" href="<?php echo $base_url;?>files/transfer/alert.css?_version=<?php echo $front_version;?>">
<link rel="stylesheet" type="text/css" href="<?php echo $base_url;?>files/transfer/bullshop.css">

<script type="text/javascript">

	window.addEventListener('load', function() {
		FastClick.attach(document.body);
	}, false);

	var newNum = "";
	var per = window.innerWidth / 530;
	var globalData = {
		//	"baseUrl":"<?php echo $base_url;?>",
		"baseUrl":"/",
		"openId": "",
        "dealerNum": "<?php echo $dealer_num;?>",
		"fileUrl": "<?php echo $image_url;?>",
		"imageUrl": "<?php echo $image_url;?>",
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

	//#291c4d
</script>

<style type="text/css">
	.gameItem{position: absolute;background-color: #291c4d;}
	.alert1{position: fixed;z-index: 11;width: 160px;height: 160px;top:50%;left: 50%;margin-top: -80px;margin-left: -80px;border-radius:5px;overflow:hidden;}
	.alert1 .alertBackground{filter:alpha(opacity=50);-moz-opacity:0.5;opacity:0.5;background: #000;height: 100%;width: 100%;}
	.alert1 img{position: absolute;top:15px;left: 29px;}
	.alert1 .alertText{position: absolute;top:115px;width:160px;text-align: center;font-size: 18px;color: #fff;filter:alpha(opacity=95);-moz-opacity:0.95;opacity:0.95;}
</style>

</head>

<body style="background-color: #0e0226">

	<div id="loading" style="position: fixed;width:100%;height:100%;top:0;left:0;background: #000" >
		<img src="<?php echo $base_url;?>files/transfer/loading.gif" style="top: 40%;position: absolute;left: 50%;margin-left: -45px;margin-top: -45px;" />
	</div>

	<div class="main" id="app-main" style="position: relative; width: 100%;margin: 0 auto; background: #0e0226; display: none;">

		<!-- 提示框 -->
		<div class="alert" id="valert" v-show="isShowAlert" style="display: none;">
			<div class="alertBack"></div>
			<div class="mainPart">
				<div class="backImg">
					<div class="blackImg"></div>
				</div>
				<div class="alertText">{{alertText}}</div>
				<div v-show="alertType==3">
					<div class="buttonLeft" v-on:click="closeAlert"></div>
					<div class="buttonRight" v-on:click="closeAlert"></div>
				</div>
				<div v-show="alertType==7">
					<div class="buttonMiddle" v-on:click="closeAlert"></div>
				</div>
				<div v-show="alertType==8">
					<div class="buttonMiddle" v-on:click="closeAlert"></div>
				</div>
				<div v-show="alertType==17">
					<div class="buttonRight" v-on:click="confirmTranfer"></div>
					<div class="buttonLeft" v-on:click="closeAlert"></div>
				</div>
			</div>
		</div>

		<div class="alert1" v-show="alert.isShow" style="z-index: 9999;">
			<div class="alertBackground"></div>
			<img src="<?php echo $base_url;?>files/transfer/alert1.png" v-show="alert.type==1">
			<img src="<?php echo $base_url;?>files/transfer/alert2.png" v-show="alert.type==2">
			<div class="alertText" >{{alert.text}}</div>
		</div>


		<div class="list_title" style="top: 4vw;position: absolute;width: 100%;height: 15vw;line-height: 15vw;background-color: #291c4d;text-align: center;margin-top: 2.8vw;color: white;overflow:hidden;" >
			<div style="position: absolute;left: 4vw;color: #FFE198;text-shadow: 0px 0px 1px #FFE198;">玩家昵称</div>
			<div style="position: absolute;left: 55vw;color: #FFE198;text-shadow: 0px 0px 1px #FFE198;">房卡</div>
		</div>

		<div id="memberDiv" v-bind:style="'position: absolute;top: 21.8vw;left: 0;width: 100%;height: ' + (height - 0.04 * width) + 'px;overflow: auto;'" >
			<!-- <div style="position: relative;"> -->
				<div v-for="item in members" style="position: relative;width: 100%;height: 15vw;line-height: 15vw;background-color: #291c4d;text-align: center;margin-top: 2.7vh;color: white;overflow:hidden;" >
					<img v-bind:src="item.avatar" style="position: absolute;top: 3vw;left: 4vw; width: 10vw; height: 10vw;">
					<div style="position: absolute;top:0vw;left: 18vw;width: 32vw;height: 15vw;line-height: 15vw;font-size: 2.5vh;text-align: left;overflow:hidden;text-overflow:ellipsis;white-space:nowrap;">
						{{item.name}}
					</div>
					<div style="position: absolute;top:0vw;left: 55vw;width: 29vw;height: 15vw;line-height: 15vw;font-size: 2.5vh;text-align: left;overflow:hidden;text-overflow:ellipsis;white-space:nowrap;">
						{{item.ticket}}
					</div>
					<div style="position: absolute;top: 1.8vw;right: 3vw;" v-show="item.is_cantransfer==1" v-on:click="clickShowTranfer(item)">
						<img src="<?php echo $image_url;?>files/images/common/move_card.png" style="width: 12.3vw; height: 12.3vw;">
					</div>

				</div>
			<!-- </div> -->
		</div>

		<div style="top: 0;left: 0;width: 100vw;height: 100vh;z-index: 999;" v-show="showTranfer">
			<div style="position: absolute;width: 100%;height: 100%;background-color: black;opacity: 0.6" >
			</div>
			<div style="position: absolute;top: 20vh;left: 8vw;width: 84vw;height: 59vh;border-radius: 1vh;background-color: #FFF4DC;background-color: rgba(255,255,255,0.3);border: 1px solid rgba(255,255,255,0.2);padding: 2vw;box-sizing: border-box;">
				<div style="position: absolute;right: -4vw;top: -4vw;" v-on:click="hideTranfer">
					<img src="<?php echo $image_url;?>files/images/common/close.png" style="width: 12vw; height: 12vw;">
				</div>
				<div style="background-color: rgb(255, 244, 220);width: 100%;height: 100%;">
					<div style="position: absolute;height: 8vh;width: calc(80vw - 2px);line-height: 8vh;color: #7D2F00;font-size: 2.8vh;text-align: center;">
						<span style="display: inline-block;border-top: 1px solid transparent;border-right: 39px solid #A04A19;border-bottom: 1px solid transparent;vertical-align: middle;"></span>
						将{{selectedItem.name}}的房卡转移至：
						<span style="display: inline-block;border-top: 1px solid transparent;border-left: 39px solid #A04A19;border-bottom: 1px solid transparent;vertical-align: middle;"></span>
					</div>
					<div style="position: absolute;top: 8vh;width: calc(80vw - 2px);height: 1px;background-color: lightgray;"></div>

					<div id="tMemberDiv" v-bind:style="'position: absolute;top: 8vh;width:calc(80vw - 2px);height: ' + 0.39 * height + ';overflow: auto;overflow-x: hidden;background: url(<?php echo $image_url;?>files/images/common/bullInnerborder.png)no-repeat;background-size: 100% 100%;'">
						<div v-for="item in tMembers" style="position: relative;width: 100%;height: 15vw;line-height: 15vw;" v-on:click="clickTMember(item)">
							<img src="<?php echo $image_url;?>files/images/common/checked.png" style="position: absolute;top: 3.6vw;right: 6vw; width: 8vw; height: 8vw;" v-show="item.checked">
							<img src="<?php echo $image_url;?>files/images/common/unchecked.png" style="position: absolute;top: 3.6vw;right: 6vw; width: 8vw; height: 8vw;" v-show="!item.checked">
							<img v-bind:src="item.avatar" style="position: absolute;top: 3vw;left: 8vw; width: 10vw; height: 10vw;">
							<div style="position: absolute;top:0vw;left: 22vw;width: 79vw;height: 15vw;line-height: 15vw;font-size: 2.5vh;text-align: left;">
								{{item.name}}
							</div>
							<div style="position: absolute;top: 14.9vw;left: 3vw;width: 74vw;height: 1px;background-color: #DAB674;"></div>
						</div>
					</div>

					<div style="position: absolute;top: 47vh;width: calc(80vw - 2px);height: 1px;background-color: lightgray;">
					</div>
					<div style="position: absolute;top: 47vh;width: 100%;height: 11vh;line-height: 11vh;font-size: 2.5vh;text-align: center;color: rgb(41,97,250);" v-on:click="clickTranfer()">
						<!-- 确认转移 -->
						<img src="<?php echo $image_url;?>files/images/common/conform.png" style="height: 7vh;width: auto;margin-top: 2vh;" />
					</div>
				</div>
			</div>
		</div>

	</div>

</body>

<script type="text/javascript" src="<?php echo $base_url;?>files/transfer/pUserList17-1.0.2.min.js"></script>

</html>
