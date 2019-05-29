<html>
<head>
<meta charset="utf-8" >
<meta name="viewport" content="width=device-width,initial-scale=1.0, minimum-scale=1.0, maximum-scale=1.0, user-scalable=no"/>
<meta name="format-detection" content="telephone=no" />
<meta name="msapplication-tap-highlight" content="no" />
<title>转移公会</title>

<script type="text/javascript" src="https://gameoss.fexteam.com/files/js/fastclick.js"></script>

<link rel="stylesheet" type="text/css" href="https://gameoss.fexteam.com/files/css/bull_vue-1.0.0.css">
<link rel="stylesheet" type="text/css" href="https://gameoss.fexteam.com/files/css/common/alert.css">
<link rel="stylesheet" type="text/css" href="https://gameoss.fexteam.com/files/css/bullshop.css">

<script type="text/javascript">

	window.addEventListener('load', function() {
		FastClick.attach(document.body);
	}, false);

	var newNum = "";
	var per = window.innerWidth / 530;
	var globalData = {
		"baseUrl": "http://fy.one168.com/",
        "dealerNum": "1",
		"fileUrl": "https://gameoss.fexteam.com/",
		"imageUrl": "http://goss.fexteam.com/",
		"phoneUsers":'[{"account_id":"2619","name":"6Im96Im9","head_img_url":"http:\/\/thirdwx.qlogo.cn\/mmopen\/vi_32\/Q3auHgzwzM6y8YiaHN7aqYiaoV7AelfibTticSf7BHKXyLhfaeib4RrFRwICbxmX485w99XvAxibQViaYVicSbLTcZyXrQ\/132","account_code":"12619","org_count":"0","is_cantransfer":0}]',
		"apiUrl":"http://fyweb.fexteam.com/",
	};
	var userData = {
		"accountId": "2619",
		"nickname": "艽艽",
		"avatar": "http://thirdwx.qlogo.cn/mmopen/vi_32/Q3auHgzwzM6y8YiaHN7aqYiaoV7AelfibTticSf7BHKXyLhfaeib4RrFRwICbxmX485w99XvAxibQViaYVicSbLTcZyXrQ/132",
		"s": "27714b4bb3a4c671f8653fd8166314ea",
		"aid": "2619",
	};
	var configData = {
		"appId": "wxa3511dd346d407c1",
		"timestamp": "1521903002",
		"nonceStr": "0092b6a46d444f0ea6a337905caa6216",
		"signature": "798ea681f50eb603d208def711c387c69dfda0de",
	};

	globalData.phoneUsers = eval('(' + globalData.phoneUsers + ')');

</script>

<style type="text/css">
	.gameItem{position: absolute;background-color: #291c4d;}
	.alert1{position: fixed;z-index: 11;width: 160px;height: 160px;top:50%;left: 50%;margin-top: -80px;margin-left: -80px;border-radius:5px;overflow:hidden;}
	.alert1 .alertBackground{filter:alpha(opacity=50);-moz-opacity:0.5;opacity:0.5;background: #000;height: 100%;width: 100%;}
	.alert1 img{position: absolute;top:15px;left: 29px;}
	.alert1 .alertText{position: absolute;top:115px;width:160px;text-align: center;font-size: 18px;color: #fff;filter:alpha(opacity=95);-moz-opacity:0.95;opacity:0.95;}

	#alertCommon .alertMask{position: fixed;z-index: 998;top: 0;left: 0;width: 100%;height: 100%;background-color: rgba(0,0,0,0.5);}
	#alertCommon .alertFrame{position: fixed;z-index: 999;width: 90vw;max-width: 90vw; top: 45%; left: 50%;-webkit-transform:translate(-50%,-60%); background-color: #fff; text-align: center; border-radius: 8px; overflow: hidden;opacity: 1; color: white;}
	#alertCommon .text{position: relative;margin-top: 15vw;margin-bottom: 15vw;margin-left: 8vw;margin-right: 8vw;word-wrap: break-word;word-break: break-all;color: #000;background-color: white;border-top: solid;border-color: #e6e6e6;border-width: 0px;}
	#alertCommon .buttonFrame{position: relative;width: 100%;height: 11vw;line-height: 11vw;text-align: center;color: #fff;margin-bottom: 9vw;text-align: center;font-size: 4vw;}
	#alertCommon .buttonFrame .button{position: relative;width: 32vw;height: 11vw;line-height: 11vw;background: #6d7dd4;color:#fff;border-radius: 1.5vw;}
	#alertCommon .buttonFrame .buttonMiddle{position: absolute;left: 50%;margin-left: -16vw;}
    #alertCommon .buttonFrame .buttonLeft{position: absolute;left: 10vw;}
    #alertCommon .buttonFrame .buttonRight{position: absolute;right: 10vw;background: #ff5555;}

    [v-cloak] {
		display:none !important;
	} 
</style>

</head>

<body style="background-color: white">

	<div id="loading" style="position: fixed;width:100%;height:100%;top:0;left:0;background: #000" >
		<img src="http://goss.fexteam.com/files/images/loading.gif" style="top: 40%;position: absolute;left: 50%;margin-left: -45px;margin-top: -45px;" />
	</div>

	<div class="main" id="app-main" style="position: relative; width: 100%;margin: 0 auto; background: white; display: none;" ng-cloak>
        
        <div style="position: fixed;width: 100%;height: 100%;background-color: white;z-index: 9999;" v-show="noInfo==1">
        	<img src="http://goss.fexteam.com/files/images/info_norecords.png" style="position: absolute;top: 20vh;left:50%;margin-left: -40vw;width: 80vw;" >
        </div>


        <div id="alertCommon" v-show="isShowAlert">
			<div class="alertMask" ></div>
			<div class="alertFrame" >
				<div class="text">
					{{alertText}}
				</div>

				<div class="buttonFrame" v-show="alertType==3">
					<div class="button buttonLeft" v-on:click="closeAlert">取消</div>
					<div class="button buttonRight" v-on:click="closeAlert">确定</div>
				</div>

				<div class="buttonFrame" v-show="alertType==7">
					<div class="button buttonMiddle" v-on:click="closeAlert">确定</div>
				</div>

				<div class="buttonFrame" v-show="alertType==17">
					<div class="button buttonLeft" v-on:click="closeAlert">取消</div>
					<div class="button buttonRight" v-on:click="confirmTranfer">确定</div>
				</div>

			</div>
		</div>

		<!-- 提示框 -->
		<!-- <div class="alert" id="valert" v-show="isShowAlert" style="display: none;">
			<div class="alertBack"></div>
			<div class="mainPart">
				<div class="backImg">
					<div class="blackImg"></div>
				</div>
				<div class="alertText">{{alertText}}</div>				
				<div v-show="alertType==3">
					<div class="buttonLeft" v-on:click="closeAlert">确定</div>
					<div class="buttonRight" v-on:click="closeAlert">取消</div>
				</div>			
				<div v-show="alertType==7">
					<div class="buttonMiddle" v-on:click="closeAlert">确定</div>
				</div>	
				<div v-show="alertType==8">
				</div>
				<div v-show="alertType==17">
				    <div class="buttonRight" v-on:click="confirmTranfer">确定</div>
					<div class="buttonLeft" v-on:click="closeAlert">取消</div>
				</div>				
			</div>
		</div> -->

		<div class="alert1" v-show="alert.isShow" style="z-index: 9999;">
			<div class="alertBackground"></div>
			<img src="https://gameoss.fexteam.com/files/images/activity/alert1.png" v-show="alert.type==1">
			<img src="https://gameoss.fexteam.com/files/images/activity/alert2.png" v-show="alert.type==2">
			<div class="alertText" >{{alert.text}}</div>
		</div>


		<div style="top: 4vw;position: absolute;width: 100%;height: 15vw;line-height: 15vw;background-color:white;text-align: center;color: white;overflow:hidden;" >
			<div style="position: absolute;left: 4vw;color: black;">玩家昵称</div>
			<div style="position: absolute;left: 55vw;color: black;">ID</div>
			<div style="position: absolute;left: 0;top: 14.9vw;width: 100vw;height: 4px;background-color: lightgray;"></div>
		</div>

		<div id="memberDiv" v-bind:style="'position: absolute;top: 20vw;left: 0;width: 100%;height: ' + (height - 0.04 * width) + 'px;overflow: auto;'" >
			<!-- <div style="position: relative;"> -->
				<div v-for="item in members" style="position: relative;width: 100%;height: 15vw;line-height: 15vw;background-color: #white;text-align: center;margin-top: 0vh;color: white;overflow:hidden;" >
					<img v-bind:src="item.avatar" style="position: absolute;top: 3vw;left: 4vw; width: 10vw; height: 10vw;">
					<div style="position: absolute;top:0vw;left: 18vw;width: 79vw;height: 15vw;line-height: 15vw;font-size: 2.5vh;text-align: left;color:black;">
						{{item.name}}
					</div>
					<div style="position: absolute;top:0vw;left: 55vw;width: 79vw;height: 15vw;line-height: 15vw;font-size: 2.5vh;text-align: left;color: orange;">
						{{item.account_code}}
					</div>
					<!-- <div style="position: absolute;top: 2vw;right: 8vw;height: 10vw;width: 10vw;border-radius: 5.5vw;border-style: solid;border-color: rgb(111,180,245);border-width: 0.5vw;" v-show="item.is_cantransfer==1" v-on:click="clickShowTranfer(item)">
						<div style="position: absolute;top: 1vw;height: 5vw;line-height: 5vw;font-size: 3vw;width: 10vw;text-align: center;color: rgb(111,180,245)">转移</div>
						<div style="position: absolute;top: 5vw;height: 5vw;line-height: 5vw;width: 10vw;font-size: 3vw;text-align: center;color: rgb(111,180,245)">房卡</div>
					</div> -->
					<div style="position: absolute;top: 3.5vw;right: 4vw;height: 7vw;line-height: 7vw;width: 24vw;text-align: center;border-radius: 5.5vw;border-style: solid;border-color: orange;border-width: 0.5vw;color:black;background-color: orange;" v-show="item.is_cantransfer==1" v-on:click="clickShowTranfer(item)">
						转移公会
					</div>

					<div style="position: absolute;top: 14.9vw;left: 4vw;width: 92vw;height: 4px;background-color: lightgray;"></div>
	
				</div>
			<!-- </div> -->
		</div>

		<div style="top: 0;left: 0;width: 100vw;height: 100vh;z-index: 999;" v-show="showTranfer">
			<div style="position: absolute;width: 100%;height: 100%;background-color: black;opacity: 0.6" v-on:click="hideTranfer">
			</div>
			<div style="position: absolute;top: 22.5vh;left: 10vw;width: 80vw;height: 55vh;border-radius: 1vh;background-color: white;overflow: hidden;">
				<div style="position: absolute;left: 6vw;height: 8vh;line-height: 8vh;color: black;font-size: 2.5vh;">
					将{{selectedItem.name}}的公会转移至：
				</div>
				<div style="position: absolute;top: 8vh;width: 100%;height: 1px;background-color: lightgray;"></div>
                 
				<div id="tMemberDiv" v-bind:style="'position: absolute;top: 8vh;width:80vw;height: ' + 0.39 * height + ';overflow: auto;overflow-x: hidden;'">
					<div v-for="item in tMembers" style="position: relative;width: 100%;height: 15vw;line-height: 15vw;" v-on:click="clickTMember(item)">
					    <img src="https://gameoss.fexteam.com/files/images/activity/icon_checked.png" style="position: absolute;top: 5vw;left: 6vw; width: 6vw; height: 6vw;" v-show="item.checked">
					    <img src="https://gameoss.fexteam.com/files/images/activity/icon_unchecked.png" style="position: absolute;top: 5vw;left: 6vw; width: 6vw; height: 6vw;" v-show="!item.checked">
						<img v-bind:src="item.avatar" style="position: absolute;top: 3vw;left: 16vw; width: 10vw; height: 10vw;">
						<div style="position: absolute;top:0vw;left: 30vw;width: 79vw;height: 15vw;line-height: 15vw;font-size: 2.5vh;text-align: left;">
							{{item.name}}
						</div>
						<div style="position: absolute;top: 14.9vw;left: 6vw;width: 74vw;height: 1px;background-color: lightgray;"></div>
					</div>
				</div>

				<div style="position: absolute;top: 47vh;width: 100%;height: 1px;background-color: lightgray;">
				</div>
				<div style="position: absolute;top: 47vh;width: 100%;height: 8vh;line-height: 8vh;font-size: 2.5vh;text-align: center;color: rgb(41,97,250);" v-on:click="clickTranfer()">
					确认转移
				</div>
			</div>
		</div>

	</div>

</body>

<script type="text/javascript" src="https://gameoss.fexteam.com/files/js/base64.js"></script>
<script type="text/javascript" src="https://gameoss.fexteam.com/files/js/jquery-1.9.1.min.js"></script>
<script type="text/javascript" src="https://gameoss.fexteam.com/files/js/bscroll.min.js" ></script>
<script src="https://res.wx.qq.com/open/js/jweixin-1.0.0.js" ></script>
<script type="text/javascript" src="https://gameoss.fexteam.com/files/js/vue.min.js" ></script>
<script type="text/javascript" src="https://gameoss.fexteam.com/files/js/vue-resource.min.js" ></script>
<script type="text/javascript" src="https://gameoss.fexteam.com/files/js/user/transferTeam/transferTeam-1.0.2.js"></script>

</html>
