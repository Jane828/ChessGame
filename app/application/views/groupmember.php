<html>
<head>
<meta charset="utf-8" >
<meta name="viewport" content="width=device-width,initial-scale=1.0, minimum-scale=1.0, maximum-scale=1.0, user-scalable=no"/>
<meta name="format-detection" content="telephone=no" />
<meta name="msapplication-tap-highlight" content="no" />
<title>群组成员</title>

<script type="text/javascript" src="<?php echo $file_url;?>files/js/fastclick.js?_version=<?php echo $front_version;?>"></script>

<link rel="stylesheet" type="text/css" href="<?php echo $file_url;?>files/css/bull.css?_version=<?php echo $front_version;?>">
<link rel="stylesheet" type="text/css" href="<?php echo $file_url;?>files/css/alert.css?_version=<?php echo $front_version;?>">

<style type="text/css">

	.buttonLeft2{position: absolute;line-height: 9vw;height: 9vw;font-size: 14px;width: 17vw;right:23vw;bottom:4vw;text-align:center;background: #40A635;border-radius: 6vw;}
	.buttonRight2{position: absolute;line-height: 9vw;height: 9vw;font-size: 14px;width: 17vw;right:3vw;bottom:4vw;text-align: center;background: #A63535;border-radius: 6vw;}
	/* 返回按钮和复制按钮 */
	.top_btn {position: absolute;top: 3vw;left: 1vw;width: auto;height: 45px;padding: 0 10px;box-sizing: border-box;z-index:200;}
	.top_btn .goback{width: 45px;height: 45px;float: left;background: url("<?php echo $image_url;?>files/images/common/back.png") no-repeat;background-size: cover;}
	
</style>
<script type="text/javascript">

	window.addEventListener('load', function() {
		FastClick.attach(document.body);
	}, false);

	var newNum = "";
	var per = window.innerWidth / 530;
	var globalData = {
    //	"baseUrl":"<?php echo $base_url;?>",
    "baseUrl":"/",
		"openId": "<?php echo $open_id;?>",
		"fileUrl": "<?php echo $file_url;?>",
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
</style>

</head>

<body style="background-color: #0e0226">

	<div id="loading" style="position: fixed;width:100%;height:100%;top:0;left:0;background: #000" >
		<img src="<?php echo $image_url;?>files/images/common/loading.gif" style="top: 40%;position: absolute;left: 50%;margin-left: -45px;margin-top: -45px;" />
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
					<div class="buttonLeft" v-on:click="closeAlert">确定</div>
					<div class="buttonRight" v-on:click="closeAlert">取消</div>
				</div>
				<div v-show="alertType==7">
					<div class="buttonMiddle" v-on:click="closeAlert">确定</div>
				</div>
				<div v-show="alertType==8">
				</div>
			</div>
		</div>

		<div style="position: absolute;top: 0;left: 0; width: 100%;height: 18vw;">
			<div class="top_btn">
				<div class="goback" onClick="window.history.go(-1);"></div>
			</div>
        	<input v-model="searchText" type="text" name="mname" placeholder="输入玩家名字" style="padding:0 12px 0 12px;position: absolute;top: 4vw;left: 20vw;width: 60vw;height: 12vw;border-radius:  0.5vh 0 0 0.5vh;font-size: 14px;background: #dddddd;">
        	<div style="position: absolute;top: 3.9vw;left: 80vw;width: 15vw;height: 12.2vw;line-height: 12.2vw;text-align: center;background-color: #281C4E;color: #EFC51F;border-radius: 0 0.5vh 0.5vh 0;font-size: 14px;" v-on:click="clickSearch">
        		搜索
        	</div>
        </div>

        <div style="position: absolute;top: 21vw;left: 0;width: 100%;height: 15vw;background-color: #291c4d;font-size: 16px;line-height: 15vw;text-align: left;color: white;">
            <div style="position: absolute;left: 4vw;color:#FFE198;">
            	群组成员（{{memberCount}}人）
            </div>
        </div>

        <div id="memberDiv" v-bind:style="'position: absolute;top: 36vw;left: 0;width: 100%;height: ' + (height - 0.31 * width) + 'px;overflow: auto;'" >
        	<div style="position: relative;">
        		<div v-for="item in members" style="position: relative;width: 100%;height: 18vw;line-height: 18vw;background-color: #291c4d;text-align: center;color: white;overflow:hidden;margin-top: 3vw;">
        			<div style="position: absolute;top: 3vw;left: 4vw;" >
        				<img v-bind:src="item.avatarUrl" style="position: absolute;top: 0;left: 0;margin-left: 0;width: 12vw;height: 12vw;border-radius: 2vw;">
        			</div>

        			<div style="position: absolute;left: 22vw;width: 34vw;height: 18vw;line-height: 18vw;font-size: 13px;text-align: left; overflow:hidden;text-overflow:ellipsis;white-space:nowrap">
        				{{item.name}}
        			</div>

        			<div v-show="item.status==0" class="buttonLeft2"  v-on:click="clickDealMember(item, 1)">同意</div>
        			<div v-show="item.status==0" class="buttonRight2" v-on:click="clickDealMember(item, 2)">删除</div>
        			<div v-show="item.status==1" class="buttonRight2" style="background: #C89B2E;" v-on:click="clickDealMember(item, 3)">踢出</div>

        			<div v-show="item.status==2"  style="position: absolute;right: 0;width: 20%;height: 18vw;line-height: 18vw;font-size: 13px;text-align: left;color: #CCBDEE;">已删除</div>
        			<div v-show="item.status==3"  style="position: absolute;right: 0;width: 20%;height: 18vw;line-height: 18vw;font-size: 13px;text-align: left;color: #CCBDEE;">已踢出</div>
        		</div>
        		<div id="moretext" style="position: relative;background-color: white;color: black;height: 10vw;text-align: center;line-height: 10vw;font-size: 13px;display: none;" >
        			上拉加载更多
        		</div>
        	</div>
        </div>


	</div>

</body>

<script type="text/javascript" src="<?php echo $file_url;?>files/js/jquery-1.9.1.min.js"></script>
<script type="text/javascript" src="<?php echo $file_url;?>files/js/bscroll.js"></script>
<script src="https://res.wx.qq.com/open/js/jweixin-1.0.0.js" ></script>
<script type="text/javascript" src="<?php echo $file_url;?>files/js/vue.min.js" ></script>
<script type="text/javascript" src="<?php echo $file_url;?>files/js/vue-resource.min.js" ></script>
<script type="text/javascript" src="<?php echo $base_url;?>files/js/groupmember.js?_version=<?php echo $front_version;?>"></script>

<!-- script type="text/javascript" src="<?php echo $file_url;?>files/js/guild/unionmember/unionmember-1.0.2.min.js"></script ->

</html>
