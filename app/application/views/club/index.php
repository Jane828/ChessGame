<html >
    <head>

        <meta charset="utf-8">

        <meta name="viewport" content="width=device-width, initial-scale=1.0 user-scalable=no">

        <meta name="format-detection" content="telephone=no" />
        <style>
            body {
                max-width: 750px; 
                margin:auto;
            }
        </style>

    </head>
<style>
	.js-marquee{height: 7vh; line-height: 7vh;}

</style>

	<title>太古</title>
	 <meta name="viewport" content="width=device-width,user-scalable=no,initial-scale=1, minimum-scale=1,maximum-scale=1"/>

	<link rel="stylesheet" href="<?php echo $image_url;?>files/css/loading.css">
	<link rel="stylesheet" type="text/css" href="<?php echo $base_url;?>files/club/css/common-1.0.0.css">

	<script type="text/javascript" src="<?php echo $image_url;?>files/js/fastclick.js"></script>
    <script type="text/javascript" src="<?php echo $image_url;?>files/js/jquery-1.9.1.min.js"></script>
    <script type="text/javascript" src="<?php echo $image_url;?>files/js/vue.min.js" ></script>
    <script type="text/javascript" src="<?php echo $image_url;?>files/js/vue-resource.min.js" ></script>
    <script type="text/javascript" src="<?php echo $base_url;?>files/club/js/bscroll.min.js" ></script>
<style>
[v-cloak] {
	display:none !important;
}	

.guiding .bottom{position: fixed;bottom:0;left:0;width:100%;height:14vw;line-height: 14vw;text-align: center;color:#b4b2bf;z-index: 10000;background: #fff;font-size: 4vw;background: url("<?php echo $base_url;?>files/club/images/bottom.png");background-size:100% 100%;}

.guiding .bottom .item{float: left;width: 33vw;height: 100%;}	
.guiding .bottom .item img{float: left;width: 12vw;height: 12vw;margin: 2vw 0 0 10.5vw;}	
.guiding .bottom .selected{color:#28125b}	
.guiding .bottom .selected img{float: left;width: 14vw;height: 14vw;margin: -1vw 0 0 9.5vw;}	

.guiding .alert1{position: fixed;z-index: 9997;width: 160px;height: 160px;top:50%;left: 50%;margin-top: -80px;margin-left: -80px;border-radius:5px;overflow:hidden;}
.guiding .alert1 .alertBackground{filter:alpha(opacity=50);-moz-opacity:0.5;opacity:0.5;background: #000;height: 100%;width: 100%;}
.guiding .alert1 img{position: absolute;top:15px;left: 29px;}
.guiding .alert1 .alertText{position: absolute;top:115px;width:160px;text-align: center;font-size: 18px;color: #fff;filter:alpha(opacity=95);-moz-opacity:0.95;opacity:0.95;}
</style>

<style>
.hidePart{position: absolute;top:0;left: 0;width: 100%;height: 100%;}
.part{z-index: 12;}
.partBack{z-index: 11;}
.topImg{z-index: 10;}	
</style>

<style>

#app-game .backImg{width: 100%;height: 100%;position: fixed;top:0;left:0;background: url("<?php echo $base_url;?>files/club/images/home.jpg");background-size:100vw 100vh; }
#app-game .topImg{width:72vw;position: fixed;height:20vw;top:2.5vw;left:4vw;z-index:99;background: url("<?php echo $base_url;?>files/club/images/info.png");background-size:72vw 20vw;}

#app-game .user{top:0;left:0;width: 70%;position: fixed;z-index:99;}
#app-game .user .avatar{border-radius:9vw;float: left;position: relative;z-index: 50;width:17vw;height:17vw;margin-left:5.3vw;margin-top:4.2vw;}
#app-game .user .name{color: #fcdbff;font-size: 4vw;float: left;position: relative;z-index: 49;margin-top:7.5vw;margin-left:3vw;max-width:30vw;overflow: hidden;height: 5vw;line-height: 5vw; }

#app-game .roomCard{top:0;left:24.3vw;width: 26%;position:fixed;z-index:99;}
#app-game .roomCard img{position: relative;float: left;z-index: 50;height:6.5vw;margin-top:14vw;}
#app-game .roomCard .num{position: relative;color:#ffb306;font-size: 3.4vw;border-radius:4vw;padding:.8vw 4vw .8vw 5vw;float: left;margin-left: -4vw;z-index: 49;margin-top:14.6vw; background: rgba(0,0,0,0.5);}


#app-game .game{height: 240vw;}
#app-game .game .img0{top:25vw;left:6vw;}
#app-game .game .img1{top:25vw;right:6vw;}
#app-game .game .img2{top:68vw;left:6vw;}
#app-game .game .img3{top:68vw;right:6vw;}
#app-game .game .img4{top:111vw;left:6vw;}
#app-game .game .img5{top:111vw;right:6vw;}
#app-game .game .img6{top:154vw;left:6vw;}
#app-game .game .img7{top:154vw;right:6vw;}
#app-game .game .img8{top:197vw;left:6vw;}
#app-game .game .img9{top:197vw;right:6vw;}
#app-game .game .img10{top:241vw;left:6vw;}
#app-game .game .img11{top:241vw;right:6vw;}
#app-game .game .img12{top:284vw;left:6vw;}
#app-game .game .img{width: 42vw;position: absolute;height:42vw;}


#app-group {min-height: 100vh;background:#f1f1f1 ;}
#app-group .background{position: fixed;top:0;left: :0;width: 100%;height:100%;background:#f1f1f1 ;}
#app-group .black{position: fixed;top:0;left:0;width: 100%;height:100%;background: #fff;opacity: .1;}
#app-group .backcolor{background: #fff;opacity: .85;position: absolute;top:0;left: 0;width:100%;height: 100%}
#app-group .head{position: relative;width: 100%;height: 20vw;overflow: hidden;font-size: 4vw;}
#app-group .head .avatar{position: absolute;top: 2vw;left: 5vw;width: 16vw;height: 16vw;}
#app-group .head .avatar img{position: absolute;border-radius: 1.2vw;width: 100%;height: 100%;}
#app-group .head .bean{position: absolute;bottom: 2vw;left:25vw ;height: 6vw;line-height: 5vw;font-size: 3.4vw;}
#app-group .head .bean img{width: 6vw;height: 6vw;position: relative;}
#app-group .head .bean .num{position: absolute;bottom: 0;left:1vw ;height: 5vw;background: #a0a0a0;color:#f9c742;padding: 0 4vw 0 6vw;border-radius: 3vw;}
#app-group .head .name{position: absolute;top: 2.5vw;left: 25vw;width: 60vw;height: 6vw;line-height: 6vw;font-size: 4.5vw;}
#app-group .head .switchText{position: absolute;right: 5vw; top: 5.5vw;height: 9vw;width: 22vw;}
#app-group .head .switchText img{width: 100%;height:100%;}


#app-group .item{position: relative;height: 13.75vw;overflow: hidden;font-size: 4vw;border-top: .2vw solid #d9d9d9;}
#app-group .groupInfo{display: none;}
#app-group .rcIcon{position: absolute;top: 3vw;left: 5vw;width: 8vw;height: 8vw;}
#app-group .rcContent{position: absolute;left:  15.375vw;;width: 50vw;height: 10vw;line-height: 13.75vw;}
#app-group .rcArrow{position: absolute;right: 3vw;top: 5.5vw;width: 3vw;height: 3vw;}
#app-group .rcArrow1{position: absolute;right: 3vw;top: 4.0625vw;width: 5.625vw;height: 5.625vw;}
#app-group .rightText{position: absolute;right: 9vw;top: 0;height: 13.75vw;line-height: 13.75vw;}
#app-group .rest{position: relative;height: 4.5vw;border-top: .2vw solid #d9d9d9;}
#app-group .rightText .owner_head_img{position: absolute;height:9.75vw;width: 9.75vw;top: 2vw;left: -12vw;}

#app-group .newApply{position: absolute;top:4.5vw;right:3vw;background:#f15352;color: #fff;text-align:center;width:5vw;height:5vw;border-radius:2.5vw;line-height: 5.5vw;font-size:3vw;}

.greyBack{position: absolute;top:0;left:0;width:100%;height: 100%;background: #000;opacity:.5;}	
#app-group .exchange{position: fixed;top:0;left:0;width:100%;height: 100%;z-index: 16;}
#app-group .exchange .box{position: absolute;top:21vw;left:5vw;width:90%;height: 89vw;border-radius:1.5vw;background: #fff;}
#app-group .exchange .box .title{text-align: center;line-height: 16vw;font-size: 4.5vw;}
#app-group .exchange .box .beanNum{margin-left: 10vw;height: 8vw;margin-top:4vw;font-size: 3.6vw;}
#app-group .exchange .box input{margin-left: 10vw;width: 69vw;background: #f0f0f2;border-radius:1.5vw;height: 11vw;margin-bottom:5vw;padding: 0 2vw;font-size: 3.6vw;margin-top: 1vw;}
#app-group .exchange .box .button{margin-left: 25vw;width: 40vw;background: #6d7dd4;color: #fff;border-radius:1.5vw;height: 11vw;margin-top:5vw;text-align: center;line-height: 11vw;font-size: 3.6vw;}
#app-group .exchange .box .note{position: absolute;width: 70vw;left:10vw;color: #f15352;font-size:3vw;}

#app-group .switch{position: fixed;top:0;left:0;width:100%;height: 100%;z-index: 16;}
#app-group .switch .box{position: absolute;top:12vw;left:5vw;width:90%;border-radius:1.5vw;background: #fff;}
#app-group .switch .box .title{text-align: center;line-height: 16vw;font-size: 4.5vw;}
#app-group .switch .box .groupList{width: 80vw;margin: 0 auto;border-radius:1.5vw;max-height: 90vw;overflow: scroll;}
#app-group .switch .box .groupList .groupItem{height: 14vw;margin-top: .5vw;background: #f1f1f1;font-size: 4vw;color:#177ad7;}
#app-group .switch .box .groupList .groupItem img{float: left;width: 11vw;height:11vw;border-radius:1.5vw;margin-left:4vw;margin-top: 1.5vw;}
#app-group .switch .box .groupList .groupItem .name{float: left;line-height: 14vw;margin-left:3vw;}

#app-group .quitOrg{position: fixed;top:0;left:0;width:100%;height: 100%;z-index: 16;}
#app-group .quitOrg .box{position: absolute;top:40vw;left:5vw;width:90%;height: 59vw;border-radius:1.5vw;background: #fff;}
#app-group .quitOrg .box .title{text-align: center;line-height: 16vw;font-size: 4.5vw;margin-top: 13.5vw;}
#app-group .quitOrg .box .button{margin-left: 9vw;width: 31.5vw;background: #6d7dd4;color: #fff;border-radius:1.5vw;height: 11vw;margin-top:9vw;text-align: center;line-height: 11vw;float: left;font-size: 4vw;}
#app-group .quitOrg .box .buttonRed{background: #ff5555;}

#app-group .changeName{position: fixed;top:0;left:0;width:100%;height: 100%;z-index: 16;}
#app-group .changeName .box{position: absolute;top:21vw;left:5vw;width:90%;height: 72vw;border-radius:1.5vw;background: #fff;}
#app-group .changeName .box .title{text-align: center;line-height: 16vw;font-size: 4.5vw;}
#app-group .changeName .box .beanNum{margin-left: 10vw;height: 8vw;margin-top:4vw;font-size: 3.6vw;}
#app-group .changeName .box input{margin-left: 10vw;width: 69vw;background: #f0f0f2;border-radius:1.5vw;height: 11vw;margin-bottom:5vw;padding: 0 2vw;font-size: 3.6vw;margin-top: 1vw;}
#app-group .changeName .box .button{margin-left: 25vw;width: 40vw;background: #6d7dd4;color: #fff;border-radius:1.5vw;height: 11vw;margin-top:5vw;text-align: center;line-height: 11vw;font-size: 3.6vw;}
#app-group .changeName .box .note{position: absolute;width: 70vw;left:10vw;color: #f15352;font-size:3vw;}



#app-person .background{position: fixed;top:0;left: :0;width: 100%;height:100%;background:#f1f1f1 ;}
#app-person .black{position: fixed;top:0;left:0;width: 100%;height:100%;background: #fff;opacity: .1;}
#app-person .backcolor{background: #fff;opacity: .85;position: absolute;top:0;left: 0;width:100%;height: 100%}

#app-person .head{position: relative;width: 100%;height: 20vw;overflow: hidden;font-size: 4vw;}
#app-person .head .avatar{position: absolute;top: 2vw;left: 5vw;width: 16vw;height: 16vw;}
#app-person .head .avatar img{position: absolute;border-radius: 1.2vw;width: 100%;height: 100%;}
#app-person .head .id{position: absolute;bottom: 1vw;left:25vw ;width: 60vw;height: 6vw;line-height: 6vw;font-size:3.5vw;}
#app-person .head .name{position: absolute;top: 2.5vw;left: 25vw;width: 60vw;height: 6vw;line-height: 6vw;font-size: 4.5vw;}

#app-person .phone{position: absolute;left: 27vw; bottom: 2vw;width: 27vw;height: 8vw;}
#app-person .changePhone{position: absolute;left: 27vw; bottom: 2vw;width: 40vw;height: 7vw;font-size: 2.2vh;color: #39d6fe;}


#app-person .rcIcon{position: absolute;top: 3vw;left: 5vw;width: 8vw;height: 8vw;}
#app-person .rcContent{position: absolute;left: 15.375vw;width: 50vw;height: 13.75vw;line-height: 13.75vw;font-size: 4vw;}
#app-person .rcArrow{position: absolute;right: 3vw;top: 4.0625vw;width: 5.625vw;height: 5.625vw;}
#app-person .rightText{position: absolute;right: 9vw;top: 0;height: 13.75vw;line-height: 13.75vw;font-size: 12pt;opacity:.8;}
#app-person .item{position: relative;height: 13.75vw;overflow: hidden;border-top: .2vw solid #d9d9d9;}
#app-person .rest{position: relative;height: 4.5vw;border-top: .2vw solid #d9d9d9;}
.rest1{position: relative;height: 4.5vw;border-bottom: .2vw solid #d9d9d9;}
</style>
<body  >
			
	<div id="app-group" style="position: relative; width: 100%;margin: 0 auto;"  v-cloak>
		<div v-if="org_list.length!=0">
			<div class="rest1"></div>
			<div class="head" >
				<div class="backcolor"></div>
				<div class="avatar">
					<img v-bind:src="group.avatar" >
				</div>
				<div class="name" >{{group.name}}</div>
				<div class="bean">
					<div class="num" >{{balance}}</div>	
					<img src="<?php echo $base_url;?>files/club/images/bean.png" />
				</div>
				<div class="switchText" v-on:click="showSwitchGroup"><img src="<?php echo $base_url;?>files/club/images/transfer.png"  ></div>
			</div>
			<div class="rest"></div>

			<div class="item"  v-on:click="groupInfoDetail">
				<div class="backcolor"></div>	
				<img src="<?php echo $base_url;?>files/club/images/o_orgInfo1.png" class="rcIcon" >
				<img src="<?php echo $base_url;?>files/club/images/downArrowGray.png" class="rcArrow" >
				<p class="rcContent">公会信息</p>
			</div>
			<div class="groupInfo" >
				<div class="item" v-on:click="showChangeName"  v-if="isOwner">
					<div class="backcolor"></div>
					<img src="<?php echo $base_url;?>files/club/images/arrowGray.png" class="rcArrow1">
					<p class="rcContent">公会名称</p>
					<p class="rightText">{{group.name}}</p>
				</div>
				<div class="item"   v-if="!isOwner">
					<div class="backcolor"></div>
					<p class="rcContent">公会名称</p>
					<p class="rightText">{{group.name}}</p>
				</div>
				<div class="item"  >
					<div class="backcolor"></div>
					<p class="rcContent">工会号</p>
					<p class="rightText">{{group.club_no}}</p>
				</div>	
				<div class="item"  >
					<div class="backcolor"></div>
					<p class="rcContent">创建时间</p>
					<p class="rightText">{{group.time}}</p>
				</div>
				<div class="item"  >
					<div class="backcolor"></div>
					<p class="rcContent">会长</p>
					
					<p class="rightText">
						{{group.owner}}
						<img v-bind:src="group.head" class="owner_head_img">
					</p>
				</div>	
			</div>
			<div class="item"  v-on:click="groupTrend">
				<div class="backcolor"></div>	
				<img src="<?php echo $base_url;?>files/club/images/trend.png" class="rcIcon" >
				<p class="rcContent">公会动态</p>
			</div>
            <div class="item"  v-on:click="beanDetail">
                <div class="backcolor"></div>
                <img src="<?php echo $base_url;?>files/club/images/o_beanDetail1.png" class="rcIcon" >
                <p class="rcContent">我的欢乐豆</p>
            </div>
            <div class="item"  v-on:click="consumeSetup" v-if="isOwner">
                <div class="backcolor"></div>
                <img src="<?php echo $base_url;?>files/club/images/o_gameList1.png" class="rcIcon" >
                <p class="rcContent">消耗设置</p>
            </div>

<!--			<div class="rest"></div>-->
<!--			<div class="item"  v-on:click="gameRecord">-->
<!--				<div class="backcolor"></div>-->
<!--				<img src="--><?php //echo $base_url;?><!--files/club/images/gameRecord.png" class="rcIcon" >-->
<!--				<p class="rcContent">游戏记录</p>-->
<!--			</div>			-->
			<div v-if="isOwner" >		
				<div class="rest"></div>		
<!--				<div class="item"  v-on:click="beanTotal">-->
<!--					<div class="backcolor"></div>-->
<!--					<img src="--><?php //echo $base_url;?><!--files/club/images/o_beanTotal1.png" class="rcIcon" >-->
<!--					<p class="rcContent">欢乐豆统计</p>-->
<!--				</div>-->
				<div class="item"  v-on:click="groupInfo">
					<div class="backcolor"></div>
					<img src="<?php echo $base_url;?>files/club/images/o_memberList1.png" class="rcIcon" >
					<div class="newApply" v-if="applyCount!=0">{{applyCount}}</div>
					<p class="rcContent">成员管理列表({{orgUserTotal}}/{{orgUserMax}})</p>
				</div>
				<div class="item"  v-on:click="invitation">
					<div class="backcolor"></div>
					<img src="<?php echo $base_url;?>files/club/images/o_invitation1.png" class="rcIcon" >
					<div class="rcContent">邀请函</div>
				</div>	
			</div>
			
			<div class="rest"></div>
			<div class="item"  v-on:click="createOrg">
				<div class="backcolor"></div>
				<img src="<?php echo $base_url;?>files/club/images/o_createOrg1.png" class="rcIcon" >
				<p class="rcContent">创建公会</p>
			</div>
			<div class="item"  v-on:click="showQuitOrg" v-if="!isOwner" >
				<div class="backcolor"></div>
				<img src="<?php echo $base_url;?>files/club/images/o_quitOrg1.png" class="rcIcon" >
				<p class="rcContent">退出公会</p>
			</div>	
		</div>
		<div v-if="org_list.length==0" >
			<div class="item"  v-on:click="createOrg">
				<div class="backcolor"></div>
				<img src="<?php echo $base_url;?>files/club/images/o_createOrg1.png" class="rcIcon" >
				<p class="rcContent">创建公会</p>
			</div>
		</div>
		<div class="rest"></div>
		<div  style="height: 20vw;"></div>

		<div class="changeName" v-show="changeName.isShow"  >
			<div class="greyBack"  v-on:click="hideChangeName"></div>
			<div class="box">
				<div class="title">更改公会名称</div>
				<div class="beanNum">原公会名: {{group.name}}</div>
				<input placeholder="新公会名称" v-model="changeName.name"  onblur="bottomShow()" onfocus="bottomHide()"/>
				<div class="note" v-show="changeName.isShow1" style="top: 40.5vw;">{{changeName.text1}}</div>
				<div class="button" v-on:click="commitChangeName">确认修改</div>
			</div>
		</div>		
		<div class="switch" v-show="isShowSwitchGroup">
			<div class="greyBack"  v-on:click="hideSwitchGroup"></div>
			<div class="box">
				<div class="title">切换公会</div>
				<div class="groupList" >
					<div class="groupItem" v-for="g in org_list" v-on:click="switchGroup(g.club_no)">
						<img v-bind:src="g.avatar" >
						<div class="name">{{g.name}}</div>
					</div>
				</div>
				<div style="height: 8vw;"></div>
			</div>
		</div>
		<div class="quitOrg" v-show="quitOrganization.isShow">
			<div class="greyBack"  v-on:click="hideQuitOrg"></div>
			<div class="box">
				<div class="title">确认退出公会</div>

				<div class="button" v-on:click="hideQuitOrg">取消</div>
				<div class="button buttonRed" v-on:click="quitOrg">确认</div>
			</div>
		</div>
		<div class="waiting" v-show="is_operation">
			<div class="waitingBack"></div>
			<div class="load4">
				<div class="loader"></div>
			</div>
		</div>
	</div>

<script type="text/javascript">

	window.addEventListener('load', function() {
		FastClick.attach(document.body);
	}, false);

	var globalData = {
		"baseUrl":"<?php echo $base_url;?>",
        "org_list":'<?php echo $org_list; ?>',
        "apiUrl":'<?php echo $base_url;?>',
        club_no:0
	};
			
	 globalData.org_list=eval('(' + globalData.org_list + ')');		
	 for(var i=0;i<globalData.org_list.length;i++){
	 	if(globalData.org_list[i].is_last==1)
	 		globalData.club_no=globalData.org_list[i].club_no;		 			 	
	 }
	var configData = {
		"appId":"<?php echo $config_ary['appId'];?>",
		"timestamp":"<?php echo $config_ary['timestamp'];?>",
		"nonceStr":"<?php echo $config_ary['nonceStr'];?>",
		"signature":"<?php echo $config_ary['signature'];?>"
	};

    var appData = {
        applyCount: 0,
        is_operation: !1,
        org_list: globalData.org_list.concat(),
        isOwner: !1,
        isShow: !1,
        balance: 0,
        group: {name: "", club_no: "", time: "", owner: "", avatar: ""},
        groupDetail: !1,
        changeName: {isShow: !1, name: "", text1: "", isShow1: !1},
        quitOrganization: {isShow: !1},
        isShowSwitchGroup: !1,
        orgUserTotal: 0,
        orgUserMax: 0
    },
    httpModule = {
        orgInfo: function () {
            Vue.http.get(globalData.apiUrl + "club/info?club_no=" + globalData.club_no).then(function (e) {
                var t = e.data;
                if (0==t.code) {
                    appData.group.name = t.data.name,
                    appData.group.club_no = t.data.club_no,
                    appData.group.head = t.data.head,
                    appData.group.avatar = t.data.avatar,
                    appData.group.time = t.data.time,
                    appData.group.owner = t.data.nick,
                    appData.isOwner = t.data.isOwner,
                    appData.orgUserTotal = t.data.now,
                    appData.orgUserMax = t.data.max,
                    appData.balance = t.data.balance,
                    appData.applyCount = t.data.applyNum,
                    document.title = appData.group.name
                } else {
                    console.log(e.data)
                }
            }, function (e) {
                console.log(e.data)
            })
        },
        commitChangeName: function () {
            appData.is_operation = !0,
                Vue.http.post(globalData.apiUrl + "club/rename", {
                club_no: globalData.club_no,
                name: appData.changeName.name
            }).then(function (e) {
                appData.is_operation = !1;
                var t = e.data;
                0 == t.code ? (appData.group.name = appData.changeName.name, document.title = appData.group.name, viewMethods.hideChangeName(), viewMethods.showAlertB("修改成功", 2)) : (appData.exchangeBean.isShow2 = !0, appData.exchangeBean.text2 = t.msg)
            }, function (e) {
                console.log(e.data)
            })
        },
        quitOrg: function () {
            appData.is_operation = !0,
                Vue.http.post(globalData.apiUrl + "club/quit", {
                club_no: globalData.club_no
            }).then(function (e) {
                appData.is_operation = !1, 0 == e.data.code && (viewMethods.hideQuitOrg(), viewMethods.showAlertB("退出成功", 2), viewMethods.reloadView())
            }, function (e) {
                console.log(e.data)
            })
        }
    },
    viewMethods = {
        reloadView: function() {
            window.location.href = window.location.href + "?id=" + 1000 * Math.random();
        },
        showAlertB: function (e, t) {
            appData.isShowAlertB = !0, appData.alertTextB = e, appData.alertTypeB = t, setTimeout(function () {
                appData.isShowAlertB = !1
            }, 700)
        },
        groupInfoDetail: function () {
            appData.groupDetail ? $(".groupInfo").slideUp() : $(".groupInfo").slideDown(), appData.groupDetail = !appData.groupDetail
        },
        showChangeName: function () {
            appData.changeName.isShow = !0, appData.changeName.name = "", appData.changeName.isShow1 = !1
        },
        hideChangeName: function () {
            appData.changeName.isShow = !1
        },
        showSwitchGroup: function () {
            appData.isShowSwitchGroup = !0
        },
        hideSwitchGroup: function () {
            appData.isShowSwitchGroup = !1
        },
        switchGroup: function (e) {
            globalData.club_no = e, httpModule.orgInfo(), appData.isShowSwitchGroup = !1
        },
        beanDetail: function () {
            window.location.href = globalData.baseUrl + "club/beanDetail?club_no=" + globalData.club_no
        },
        consumeSetup: function () {
            window.location.href = globalData.baseUrl + "club/consume?club_no=" + globalData.club_no
        },
        commitChangeName: function () {
            return appData.changeName.isShow1 = !1, "" == appData.changeName.name ? (appData.changeName.isShow1 = !0, void(appData.changeName.text1 = "请输入新公会名称")) : appData.changeName.name.length > 10 ? (appData.changeName.isShow1 = !0, void(appData.changeName.text1 = "新公会名称长度不能超过10位")) : void httpModule.commitChangeName()
        },
        beanTotal: function () {
            window.location.href = globalData.baseUrl + "club/beanTotal?club_no=" + globalData.club_no
        },
        groupInfo: function () {
            window.location.href = globalData.baseUrl + "club/players?club_no=" + globalData.club_no
        },
        groupTrend: function () {
            window.location.href = globalData.baseUrl + "club/trend?club_no=" + globalData.club_no
        },
        invitation: function () {
            window.location.href = globalData.baseUrl + "club/invite?club_no=" + globalData.club_no
        },
        gameRecord: function () {
            window.location.href = globalData.baseUrl + "gscore/gameRecord?club_no=" + globalData.club_no
        },
        createOrg: function () {
            window.location.href = globalData.baseUrl + "club/addClub"
        },
        showQuitOrg: function () {
            appData.quitOrganization.isShow = !0
        },
        hideQuitOrg: function () {
            appData.quitOrganization.isShow = !1
        },
        quitOrg: function () {
            httpModule.quitOrg()
        },
        showSetCode: function () {
            appData.setCode.isShow = !0
        },
        hideSetCode: function () {
            appData.setCode.isShow = !1
        },
        locateSetCode: function () {
            window.location.href = globalData.baseUrl + "user/modifyPwd"
        }
    },

    vueLife = {
        vmCreated: function () {
            httpModule.orgInfo();
        }, vmUpdated: function () {
        }, vmMounted: function () {
        }, vmDestroyed: function () {
        }
    },

    vm = new Vue({
        el: "#app-group",
        data: appData,
        methods: viewMethods,
        created: vueLife.vmCreated,
        updated: vueLife.vmUpdated,
        mounted: vueLife.vmMounted,
        destroyed: vueLife.vmDestroyed
    });
</script>
</body>
</html>
