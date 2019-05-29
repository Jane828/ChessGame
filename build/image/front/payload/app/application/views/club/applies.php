<html>
<head>
  <meta charset="utf-8" >
  <meta name="viewport" content="width=device-width,initial-scale=1.0, minimum-scale=1.0, maximum-scale=1.0, user-scalable=no"/>
  <meta name="format-detection" content="telephone=no" />
  <meta name="msapplication-tap-highlight" content="no" />
  <title>新的申请</title>
    <script type="text/javascript" src="<?php echo $image_url;?>files/js/fastclick.js"></script>
    <link rel="stylesheet" type="text/css" href="<?php echo $base_url;?>files/club/css/bull_vue-1.0.0.css">
  <link rel="stylesheet" type="text/css" href="<?php echo $base_url;?>files/club/css/bullalert.css">
  <link rel="stylesheet" type="text/css" href="<?php echo $base_url;?>files/club/css/bullshop.css">
  <link rel="stylesheet" type="text/css" href="<?php echo $base_url;?>files/club/css/alert.css">
  <script type="text/javascript">
    window.addEventListener('load', function() {
    		FastClick.attach(document.body);
    	}, false);
    	var newNum = "";
    	var per = window.innerWidth / 530;
    	var globalData = {
    		"baseUrl": "<?php echo $base_url;?>",
            "club_no": "<?php echo $club_no;?>",
            "apiUrl": "<?php echo $base_url;?>",
    	};
  </script>
  <style type="text/css">
    .gameItem{position: absolute;background-color: #291c4d;}
    	.rcIcon{position: absolute;top: 2vw;left: 3vw;width: 9.375vw;height: 9.375vw;}
    	.rcContent{position: absolute;left: 15.375vw;width: 50vw;height: 13.75vw;line-height: 13.75vw;font-size: 12pt;color: white;}
    	.rcArrow{position: absolute;right: 3vw;top: 4.0625vw;width: 3.125vw;height: 5.625vw;}
    	.sendRedpackage{position: absolute;width: 100%;height: 13.75vw;overflow: hidden;background-color: #291c4d;}
    	.redpackage{position: absolute;width: 100%;height: 13.75vw;overflow: hidden;background-color: #291c4d;}
    	.userList{position: absolute;width: 100%;height: 13.75vw;overflow: hidden;background-color: #291c4d;}
    	.datepicker{position: absolute;width: 100%;height: 12.5vw;overflow: hidden;background-color: #291c4d;}
    	.groupMenuDetail{position: absolute;width: 100%;height: 27.5vw;overflow: hidden;background-color: #291c4d;}
    	.gameMenu{position: absolute;width: 100%;height: 25vw;background-color: #291c4d;text-align: center;overflow: hidden;}
    	.gameListItem{position: absolute;width: 18vw;height: 25vw;font-size: 12pt;color: white;text-align: center;}
    	.gameScoreTitle{position: absolute;width: 100%;height: 13vw;line-height: 13vw;font-size: 12pt;color: white;text-align: center;background-color: #291c4d;}
    	[v-cloak] {
    		display:none !important;
    	}
    	#alertCommon .alertMask{position: fixed;z-index: 98;top: 0;left: 0;width: 100%;height: 100%;background-color: rgba(0,0,0,0.5);}
    	#alertCommon .alertFrame{position: fixed;z-index: 99;width: 90vw;max-width: 90vw; top: 45%; left: 50%;-webkit-transform:translate(-50%,-60%); background-color: #fff; text-align: center; border-radius: 8px; overflow: hidden;opacity: 1; color: white;}
    	#alertCommon .text{position: relative;margin-top: 15vw;margin-bottom: 15vw;margin-left: 8vw;margin-right: 8vw;word-wrap: break-word;word-break: break-all;color: #000;background-color: white;border-top: solid;border-color: #e6e6e6;border-width: 0px;}
    	#alertCommon .buttonFrame{position: relative;width: 100%;height: 11vw;line-height: 11vw;text-align: center;color: #fff;margin-bottom: 9vw;text-align: center;font-size: 4vw;}
    	#alertCommon .buttonFrame .button{position: relative;width: 32vw;height: 11vw;line-height: 11vw;background: #6d7dd4;color:#fff;border-radius: 1.5vw;}
    	#alertCommon .buttonFrame .buttonMiddle{position: absolute;left: 50%;margin-left: -16vw;}
        #alertCommon .buttonFrame .buttonLeft{position: absolute;left: 10vw;}
        #alertCommon .buttonFrame .buttonRight{position: absolute;right: 10vw;background: #ff5555;}
  </style>
</head>
<body style="background-color: rgb(241,241,241);">
  <div id="loading" style="position: fixed;width:100%;height:100%;top:0;left:0;background: #000" >
    <img src="<?php echo $base_url;?>files/images/common/loading.gif" style="top: 40%;position: absolute;left: 50%;margin-left: -45px;margin-top: -45px;" />
  </div>
  <div class="main" id="app-main" style="position: relative; width: 100%;margin: 0 auto; background: rgb(241,241,241);" v-cloak>
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
        <div class="buttonFrame" v-show="alertType==23">
          <div class="button buttonMiddle" v-on:click="finishBindPhone">确定</div>
        </div>
        <div class="buttonFrame" v-show="alertType==41">
          <div class="button buttonLeft" v-on:click="closeAlert">取消</div>
          <div class="button buttonRight" v-on:click="confirmAgree">确定</div>
        </div>
        <div class="buttonFrame" v-show="alertType==42">
          <div class="button buttonLeft" v-on:click="closeAlert">取消</div>
          <div class="button buttonRight" v-on:click="confirmDisagree">确定</div>
        </div>
      </div>
    </div>
    <div style="position: fixed;width: 100%;height: 100%;background-color: white;z-index: 9999;" v-show="noInfo==1">
      <img src="http://goss.fexteam.com/files/images/info_norecords.png" style="position: absolute;top: 20vh;left:50%;margin-left: -40vw;width: 80vw;" >
    </div>
    <div style="'position: absolute;top: 14vw;left: 0;width: 100%;'" >
      <div style="position: relative;top: 4vw;">
        <div v-for="(item, index) in gameScoreList" style="position: relative;width: 100%;height: 18vw;background-color: white;text-align: center;margin-top: 2px;color: white;overflow:hidden;" >
          <img v-bind:src="item.avatar" style="position: absolute;top: 3vw;left: 3vw;width: 12vw;height: 12vw;">
          <div style="position: absolute;top:3vw;width: 100%;left: 18vw;font-size: 12pt;color: black;text-align: left;">
            {{item.nick}}
          </div>
          <div style="position: absolute;top: 10vw;width: 100%;left: 18vw;font-size: 12pt;color: black;text-align: left;">
            ID:{{item.ucode}}
          </div>
          <div style="position: absolute;right: 28vw;top: 5vw;width: 18vw;height: 8vw;line-height: 8vw;border-radius: 0.5vw;background-color: rgb(95,135,217);text-align: center;font-size: 2.5vh;" v-show="item.status==0" v-on:click="clickAgree(item)">
            同意
          </div>
          <div style="position: absolute;right: 5vw;top: 5vw;width: 18vw;height: 8vw;line-height: 8vw;border-radius: 0.5vw;background-color: rgb(243,184,75);text-align: center;font-size: 2.5vh;" v-show="item.status==0" v-on:click="clickDisagree(item)">
            拒绝
          </div>
          <div style="position: absolute;right: 5vw;top: 5vw;width: 18vw;height: 8vw;line-height: 8vw;border-radius: 0.5vw;background-color: rgba(198,146,53,0);text-align: center;font-size: 2.5vh;color: gray;" v-show="item.status==1">
            已同意
          </div>
          <div style="position: absolute;right: 5vw;top: 5vw;width: 18vw;height: 8vw;line-height: 8vw;border-radius: 0.5vw;background-color: rgba(198,146,53,0);text-align: center;font-size: 2.5vh;color: rgb(220,99,93);" v-show="item.status==2">
            已拒绝
          </div>
        </div>
        <div id="moretext" style="position: relative;margin-top: 2px;color: #000;height: 14vw;text-align: center;line-height: 14vw;font-size: 2.2vh;background-color: white;display: none;" v-on:click="clickMore">
          点击加载更多
        </div>
      </div>
    </div>
  </div>
</body>

<script type="text/javascript" src="<?php echo $image_url;?>files/js/fastclick.js"></script>
<script type="text/javascript" src="<?php echo $image_url;?>files/js/jquery-1.9.1.min.js"></script>
<script type="text/javascript" src="<?php echo $image_url;?>files/js/vue.min.js" ></script>
<script type="text/javascript" src="<?php echo $image_url;?>files/js/vue-resource.min.js" ></script>
<script type="text/javascript" src="<?php echo $base_url;?>files/club/js/bscroll.min.js" ></script>
<script type="text/javascript" src="<?php echo $base_url;?>files/club/js/applyList-1.0.4.js"></script>
</html>