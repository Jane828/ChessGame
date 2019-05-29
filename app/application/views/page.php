<html>
<head>
<meta charset="utf-8" >
<meta name="viewport" content="width=device-width,initial-scale=1.0, minimum-scale=1.0, maximum-scale=1.0, user-scalable=no"/>
<meta name="format-detection" content="telephone=no" />
<meta name="msapplication-tap-highlight" content="no" />
<title>太古主页</title>
<script type="text/javascript" src="<?php echo $image_url;?>files/js/jquery-1.9.1.min.js"></script>
<script type="text/javascript" src="<?php echo $image_url;?>files/js/fastclick.js?_version=<?php echo $front_version;?>"></script>
<script src="https://res.wx.qq.com/open/js/jweixin-1.0.0.js" ></script>
<script type="text/javascript" src="<?php echo $image_url;?>files/js/vue.min.js" ></script>
<script type="text/javascript" src="<?php echo $image_url;?>files/js/vue-resource.min.js" ></script>

<link rel="stylesheet" type="text/css" href="<?php echo $image_url;?>files/css/alert.css?_version=<?php echo $front_version;?>" >
<link rel="stylesheet" href="../../files/css/page.css?_version=<?php echo $front_version;?>">

<script type="text/javascript">

	$(function() {
		FastClick.attach(document.body);
	});

	var newNum = "";
	var per = window.innerWidth / 530;
	var globalData = {
		"card": "<?php echo $card;?>",
		//	"baseUrl":"<?php echo $base_url;?>",
		"baseUrl":"/",
		"openId": "",
        "dealerNum": "<?php echo $dealer_num;?>",
        'manageCost' : "<?php echo $manage_cost;?>",
        'currentGame' : "<?php echo $default_game;?>",
	};
	var userData = {
		"accountId": "<?php echo $user['account_id'];?>",
		"nickname": "<?php echo $user['nickname'];?>",
		"avatar": "<?php echo $user['headimgurl'];?>",
		// "isManageOn": <?php echo $user['is_manage_on'];?>,
		"userCode":"<?php echo $user['user_code'];?>",
		"card": "<?php echo $card;?>",
		"isAuthPhone":"<?php echo $isAuthPhone;?>",
		"authCardCount":"<?php echo $authCardCount;?>",
		"phone":"<?php echo $phone;?>",

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
    .panel {width: 100%;background-color: #291c4d}
    .gamePanel {margin-top: 16px;overflow-x: scroll;white-space: nowrap;}
    .gameItem {display: inline-block;text-align: center;padding: 10px;opacity: 0.5}
    .gameItem .gameImg {width: 80px;height: 80px}
    .gameItem .gameName {color: white;line-height: 0px}
    .gameItem.active {opacity: 1}

    .recordPanel {width: 100%;margin-top: 10px;color: white;font-weight:bold;font-size: 12pt;border: none;cellspacing:0;cellpadding:0;border-collapse: collapse}
    .recordPanel td {padding:15px;text-align: center;border: none;margin: 0}
    .recordHead {border-bottom:10px solid #0e0226}
    .recordRoom {color: #ffa614}

</style>

</head>

<body style="background-color: #0e0226">

	<div id="loading" style="position: fixed;width:100%;height:100%;top:0;left:0;background: #000;z-index: 10;" >
		<img src="<?php echo $image_url;?>files/images/common/loading.gif" style="top: 40%;position: absolute;left: 50%;margin-left: -45px;margin-top: -45px;" />
	</div>

   

    <div id="app-main" style="display: none">
         <!-- 提示框 -->
        <div class="alert" id="valert" v-show="isShowAlert" style="display: none">
            <div class="alertBack"></div>
            <div class="mainPart">
                <div class="backImg">
                    <div class="blackImg"></div>
                </div>
                <div class="alertText">{{alertText}}</div>
                <div v-show="alertType==3">
                    <div class="buttonLeft" @click="closeAlert"></div>
                    <div class="buttonRight" @click="closeAlert"></div>
                </div>
                <div v-show="alertType==7">
                    <div class="buttonMiddle" @click="closeAlert"></div>
                </div>
                <div v-show="alertType==8">
                </div>
                <div v-show="alertType==23">
                    <div class="buttonMiddle" @click="finishBindPhone()"></div>
                </div>
                <div v-show="alertType==24">
                    <div class="buttonLeft" @click="finishManageOn()"></div>
                    <div class="buttonRight" @click="closeAlert"></div>
                </div>
            </div>
        </div>
        <div class="headInfo">
            <div class="headImg">
                <img :src="user.avatar" alt="头像">
                <div class="uid">ID:{{user.userCode}}</div>
            </div>
            <div class="headUser">
                <div class="nickname">{{user.nickname}}</div>
                <div class="roomCard">
                    <div>{{roomCard || 0}}张</div>
                    <img src="../../files/images/me/card_icon.png" alt="">
                </div>
            </div>
            <div class="backHall" @click="goHall">
                <div>返</div><div>回</div><div>大</div><div>厅</div>
            </div>
        </div>
        <div class="mainItem" v-for="item in operateItems" :key="item.text" @click="item.doClick" >
            <img :src="item.src" alt="">
            <span>{{item.text}}</span>
            <div class="doClick"></div>
        </div>
        <!-- <div class="mainItem">
            <img src="../../files/images/me/group_operate_icon.png" alt="">
            <span>管理功能</span>
            <i @click="manageOperate" :class="{ 'closeI': !user.isManageOn }"></i>
        </div>
        <div class="manageItems" v-show="user.isManageOn">
            <div class="inviteMember"@click="clickInvite">邀请成员</div>
            <div class="showMember" @click="clickGroupMember">群组成员</div>
        </div> -->

        <!-- 绑定手机号码 -->
        <div id="validePhone" style="display: none;" v-show="isShowBindPhone">
            <div class="phoneMask" style="position: fixed;z-index: 98;top: 0;left: 0;width: 100%;height: 100%;background-color: rgba(0,0,0,0.5);" @click="hideBindPhone"></div>
            <div class="phoneFrame" style="position: fixed;z-index: 99;width: 80vw;max-width: 80vw; top: 50%; left: 50%;-webkit-transform:translate(-50%,-60%); background-color: #fff; text-align: center; border-radius: 8px; overflow: hidden;opacity: 1; color: white;">
                <div style="height: 2.2vw;"></div>
                <div style="padding: 1vw;font-size: 4vw; line-height: 5vw; word-wrap: break-word;word-break: break-all;color: #000;background-color: white;">
                    {{phoneText}}
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
    </div>

<!--	<div class="main" id="app-main" style="position: relative; width: 100%;margin: 0 auto; background: #0D0127; ">-->
<!---->
           <!-- 用户头像 -->
<!--        <div :style="viewStyle.user">-->
<!--        	<div :style="viewStyle.userAvatar">-->
<!--        		<img :src="user.avatar" :style="viewStyle.userAvatarImg">-->
<!--                <div class="uid">ID:{{user.userCode}}</div>-->
<!--            </div>-->
<!--        	<div :style="viewStyle.userName">-->
<!--        		{{user.nickname}}-->
<!--        	</div>-->
<!---->
<!--        	<img src="--><?php //echo $image_url;?><!--files/images/activity/homepage_phone.png" style="position: absolute;left: 27vw; bottom: 2vw;width: 27vw;height: 8vw;" @click="clickPhone" v-show="!isPhone">-->
<!---->
<!--			<div class="roomCard" @click="clickEditPhone">-->
<!--                <div>{{roomCard || 0}}张</div>-->
<!--                <img src="../../files/images/me/card_icon.png" alt="">-->
<!--            </div>-->
<!---->
<!--			<div style="position: absolute;bottom: 2vw;right: 4vw;width: 24vw;height: 18vw;background-color: rgb(13,6,42);border-style: solid;border-color: orange;border-width: 0.1vh;border-radius: 0.5vh;" >-->
<!--				<div  @click="clickEditPhone" style="position: absolute;top: 1vw;width: 100%;height: 9vw;line-height: 9vw;font-size: 2.5vh;color: white;text-align: center;overflow: hidden;">-->
<!--                    {{phone}}&nbsp&nbsp修改-->
<!--				</div>-->
<!--				<div style="position: absolute;top: 8vw;width: 100%;height: 9vw;line-height: 9vw;font-size: 2.3vh;color: orange;text-align: center;overflow: hidden;">-->
<!--					房卡-->
<!--				</div>-->
<!--			</div>-->
<!---->
<!--        </div>-->
<!---->
		    <!-- 发送记录 -->
<!--        <div :style="viewStyle.menu1" @click="showSendRedpackage">-->
<!--            <img src="--><?php //echo $image_url;?><!--files/images/activity/rc_icon_sendredpackage.png" :style="viewStyle.rcIcon">-->
<!--            <img src="--><?php //echo $image_url;?><!--files/images/activity/rc_icon_rightarrow.png" :style="viewStyle.rcArrow">-->
<!--            <p :style="viewStyle.rcContent">发送房卡</p>-->
<!--        </div>-->
<!---->
<!--        <div :style="viewStyle.menu2" @click="clubManage">-->
<!--            <img src="--><?php //echo $image_url;?><!--files/images/activity/rc_club.jpeg" :style="viewStyle.rcIcon">-->
<!--            <img src="--><?php //echo $image_url;?><!--files/images/activity/rc_icon_rightarrow.png" :style="viewStyle.rcArrow">-->
<!--            <p :style="viewStyle.rcContent">公会管理</p>-->
<!--        </div>-->
<!---->
            <!-- 转移房卡 -->
<!--        <div :style="viewStyle.menu3" @click="showTransferTicket">-->
<!--            <img src="--><?php //echo $image_url;?><!--files/images/activity/member_union.png" :style="viewStyle.rcIcon">-->
<!--            <img src="--><?php //echo $image_url;?><!--files/images/activity/rc_icon_rightarrow.png" :style="viewStyle.rcArrow">-->
<!--            <p :style="viewStyle.rcContent">转移房卡</p>-->
<!--        </div>-->
<!---->
            <!-- 查看记录 -->
<!--        <div :style="viewStyle.menu4" @click="showRedpackageRecord">-->
<!--        	<img src="--><?php //echo $image_url;?><!--files/images/activity/rc_icon_redpackage.png" :style="viewStyle.rcIcon">-->
<!--        	<img src="--><?php //echo $image_url;?><!--files/images/activity/rc_icon_rightarrow.png" :style="viewStyle.rcArrow">-->
<!--        	<p :style="viewStyle.rcContent">房卡记录</p>-->
<!--        </div>-->
<!---->
<!--        <div :style="viewStyle.menu5" @click="checkOpenRoom">-->
<!--            <img src="--><?php //echo $image_url;?><!--files/images/activity/rc_room_search.png" :style="viewStyle.rcIcon">-->
<!--            <img src="--><?php //echo $image_url;?><!--files/images/activity/rc_icon_rightarrow.png" :style="viewStyle.rcArrow">-->
<!--            <p :style="viewStyle.rcContent">开房查询</p>-->
<!--        </div>-->
<!---->
<!--        <div :style="viewStyle.panel">-->
               <!-- 管理功能 -->
<!--            <div :style="viewStyle.manage" >-->
<!--                <img src="--><?php //echo $image_url;?><!--files/images/activity/rc_member_group.png" :style="viewStyle.rcIcon">-->
<!--                <img src="--><?php //echo $image_url;?><!--files/images/activity/btn_off.png" :style="viewStyle.btnOnOff"  @click="openManage" v-show="!userData.isManageOn">-->
<!--                <img src="--><?php //echo $image_url;?><!--files/images/activity/btn_on.png"  :style="viewStyle.btnOnOff"  @click="closeManage" v-show="userData.isManageOn">-->
<!--                <p :style="viewStyle.rcContent">群主功能</p>-->
<!--            </div>-->
<!---->
<!--            <div style="margin-top : 10px;width: 100%;background-color: #291c4d" v-show="userData.isManageOn">-->
<!--                <div style="margin-left: 10px;width: 45%;display: inline-block;text-align: center;padding-top: 10px;padding-bottom: 10px;" @click="clickInvite">-->
<!--                    <img src="--><?php //echo $image_url;?><!--files/images/activity/homepage_invite.png" style="width: 10vw;height: 10vw;">-->
<!--                    <div style="line-height: 10vw;font-size: 2vh;text-align: center;color: white;">-->
<!--                        邀请成员-->
<!--                    </div>-->
<!--                </div>-->
<!--                <div style="margin-right : 10px;width: 45%;display: inline-block;text-align: center;padding-top: 10px;padding-bottom: 10px;" @click="clickGroupMember">-->
<!--                    <img src="--><?php //echo $image_url;?><!--files/images/activity/member_union.png" style="width: 10vw;height: 10vw;">-->
<!--                    <div style="line-height: 10vw;font-size: 2vh;text-align: center;color: white;">-->
<!--                        群组成员-->
<!--                    </div>-->
<!--                </div>-->
<!--            </div>-->
<!---->
<!--            <div class="panel gamePanel">-->
<!---->
<!--                --><?php //foreach ($game_list as $key=>$game) {
//    ?>
<!--                    <div class="gameItem" @click="checkGameRecord('--><?php //echo $key; ?><!--')" :class="globalData.currentGame === '--><?php //echo $key; ?><!--' ? 'active' : ''">-->
<!--                        <img src="/files/images/rc_icon_--><?php //echo $key; ?><!--.png" class="gameImg">-->
<!--                        <p class="gameName">--><?php //echo $game['name'] ?><!--</p>-->
<!--                    </div>-->
<!--                --><?php
//} ?>
<!---->
<!--            </div>-->
<!---->
<!--            <table class="recordPanel">-->
<!--                <tr class="panel recordHead">-->
<!--                    <td>房间号</td>-->
<!--                    <td>结束时间</td>-->
<!--                    <td>当局积分</td>-->
<!--                </tr>-->
<!---->
<!--                <tr class="panel" v-for="item in gameRecord" @click="checkRoomDetail(item)">-->
<!--                    <td class="recordRoom">{{item.room_number}}</td>-->
<!--                    <td>{{item.over_time}}</td>-->
<!--                    <td>{{item.score}}</td>-->
<!--                </tr>-->
<!---->
<!--                <tr v-show="!room_is_last">-->
<!--                    <td colspan="3" @click="getMoreRoom()">点击加载更多</td>-->
<!--                </tr>-->
<!---->
<!--            </table>-->
<!---->
<!--        </div>-->
<!---->
    		<!-- 绑定手机号码 -->
<!--		<div id="validePhone" style="display: none;" v-show="isShowBindPhone">-->
<!--			<div class="phoneMask" style="position: fixed;z-index: 98;top: 0;left: 0;width: 100%;height: 100%;background-color: rgba(0,0,0,0.5);" @click="hideBindPhone"></div>-->
<!--			<div class="phoneFrame" style="position: fixed;z-index: 99;width: 80vw;max-width: 80vw; top: 50%; left: 50%;-webkit-transform:translate(-50%,-60%); background-color: #fff; text-align: center; border-radius: 8px; overflow: hidden;opacity: 1; color: white;">-->
<!--			    <div style="height: 2.2vw;"></div>-->
<!--				<div style="padding: 1vw;font-size: 4vw; line-height: 5vw; word-wrap: break-word;word-break: break-all;color: #000;background-color: white;">-->
<!--					{{phoneText}}-->
<!--				</div>-->
<!--				<div style="height: 2.2vw;"></div>-->
<!--				<div style="position: relative;height: 15vw;word-wrap: break-word;word-break: break-all;color: #000;background-color: white;border-top: solid;border-color: #e6e6e6;border-width: 0px;">-->
<!--					<input  @input="phoneChangeValue()" v-model="sPhone" type="number" name="phone" placeholder="输入手机号" style="padding:0 12px 0 12px;position: absolute;top:  2.5vw;left: 4vw;width: 48vw;height: 11vw;line-height: 6.5vw;border-style: solid;border-width: 1px;border-radius: 0.5vh;border-color: #e6e6e6;font-size: 4vw;-webkit-appearance: none;">-->
<!--					<div id="authcode" @click="getAuthcode()" style="position: absolute;top:  2.5vw;right: 4vw; width: 22vw;height: 10vw;line-height: 10vw;background-color: rgb(211,211,211);font-size: 3.5vw;border-radius: 0.5vh;color: white;">-->
<!--						{{authcodeText}}-->
<!--					</div>-->
<!--				</div>-->
<!--				<div style="position: relative;height: 15vw;word-wrap: break-word;word-break: break-all;color: #000;background-color: white;border-top: solid;border-color: #e6e6e6;border-width: 0px;">-->
<!--					<input  v-model="sAuthcode" type="number" name="phone1" placeholder="输入验证码" style="padding:0 12px 0 12px;position: absolute;top: 1vw;left: 4vw;width: 72vw;height: 11vw;line-height: 6.5vw;border-style: solid;border-width: 1px;border-radius: 0.5vh;border-color: #e6e6e6;font-size: 4vw;-webkit-appearance: none;">-->
<!---->
<!--				</div>-->
<!--				<div style="height: 2.2vw;"></div>-->
<!--				<div style="position: relative; left: 4vw;width: 72vw;line-height: 10vw; font-size: 4vw;display: flex;border-radius: 2vw;" @click="bindPhone()">-->
<!--					<div style="display: block;-webkit-box-flex:1;flex: 1;text-decoration: none;-webkit-tap-highlight-color:transparent;position: relative;margin-bottom: 0;color: rgb(255,255,255);border-top: solid;border-color: #e6e6e6;border-width: 0px;background-color: rgb(64,112,251);border-radius: 1vw;">立即绑定</div>-->
<!--				</div>-->
<!--				<div style="height:4vw;"></div>-->
<!--			</div>-->
<!--		</div>-->
<!---->
<!--	</div>-->

</body>

<script type="text/javascript" src="/files/js/page.js?_version=<?php echo $front_version;?>"></script>

</html>
