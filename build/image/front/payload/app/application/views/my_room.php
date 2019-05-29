<html>
<head>
<meta charset="utf-8" >
<meta name="viewport" content="width=device-width,initial-scale=1.0, minimum-scale=1.0, maximum-scale=1.0, user-scalable=no"/>
<meta name="format-detection" content="telephone=no" />
<meta name="msapplication-tap-highlight" content="no" />
<title id="pageTitle">{{text}}</title>
<script type="text/javascript" src="<?php echo $image_url;?>files/js/jquery-1.9.1.min.js"></script>
<script type="text/javascript" src="<?php echo $image_url;?>files/js/fastclick.js?_version=<?php echo $front_version;?>"></script>
<script src="https://res.wx.qq.com/open/js/jweixin-1.0.0.js" ></script>
<script type="text/javascript" src="<?php echo $image_url;?>files/js/vue.min.js" ></script>
<script type="text/javascript" src="<?php echo $image_url;?>files/js/vue-resource.min.js" ></script>

<link rel="stylesheet" type="text/css" href="<?php echo $base_url;?>files/transfer/alert.css?_version=<?php echo $front_version;?>" >

<script type="text/javascript">

	$(function() {
		FastClick.attach(document.body);
	});

	var globalData = {
		//	"baseUrl":"<?php echo $base_url;?>",
		"baseUrl":"/",
        'currentGame' : "<?php echo $default_game;?>",
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

	<div class="main" id="app-main" style="position: relative; width: 100%;margin: 0 auto; background: #0e0226;display:none;">

		<!-- 提示框 -->
		<div class="alert" id="valert" v-show="isShowAlert" style="display: none">
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
				<div v-show="alertType==23">
					<div class="buttonMiddle" v-on:click="finishBindPhone()"></div>
				</div>
				<div v-show="alertType==24">
					<div class="buttonLeft" v-on:click="finishManageOn()"></div>
					<div class="buttonRight" v-on:click="closeAlert"></div>
				</div>
			</div>
		</div>

        <div class="panel gamePanel" style="padding-left: 64px;box-sizing: border-box;">
			<div style="width: 60px;height: 132px;top: 0;position: absolute;left: 0;z-index: 10;background-color: #291c4d;" onClick="window.history.go(-1);">
				<img src="<?php echo $image_url;?>files/images/common/back_text.png" style="height: 64px;width: auto;margin-top: 32px;margin-left: 8px;" />
			</div>
            <?php foreach ($game_list as $key=>$game) {
    ?>
                <div class="gameItem" v-on:click="checkGameRecord('<?php echo $key; ?>')" v-bind:class="globalData.currentGame === '<?php echo $key; ?>' ? 'active' : ''">
                    <img src="/files/images/rc_icon_<?php echo $key; ?>.png" class="gameImg">
                    <p class="gameName"><?php echo $game['name'] ?></p>
                </div>
            <?php
} ?>

        </div>

        <table class="recordPanel">
            <tr class="panel recordHead">
                <td>房间号</td>
                <td>创建时间</td>
                <td v-show="<?php echo ($_GET['t']=='m') ?>">房间状态</td>
				<td v-show="<?php echo ($_GET['t']!='m') ?>">当局积分</td>
            </tr>

            <tr class="panel" v-for="item in gameRecord" v-on:click="checkRoomDetail(item)" style="border-top: 1px solid #0D0328;">
                <td class="recordRoom">{{item.room_number}}</td>
                <td>{{item.create_time || item.over_time }}</td>
                <td v-show="item.is_close">{{item.is_close == '1' ? '已完成' : '已创建'}}</td>
				<td v-show="item.score">{{item.score}}</td>
            </tr>

            <tr v-show="!room_is_last">
                <td colspan="3" v-on:click="getMoreRoom()">点击加载更多</td>
            </tr>

        </table>

	</div>

</body>

<script type="text/javascript" src="/files/js/my_room.js?_version=<?php echo $front_version;?>"></script>

</html>
