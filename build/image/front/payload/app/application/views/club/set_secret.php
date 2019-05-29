<html>
<head>
    <meta charset="utf-8" >
    <meta name="viewport" content="width=device-width,initial-scale=1.0, minimum-scale=1.0, maximum-scale=1.0, user-scalable=no"/>
    <meta name="format-detection" content="telephone=no" />
    <meta name="msapplication-tap-highlight" content="no" />
    <title>密钥设置</title>

    <script type="text/javascript" src="<?php echo $image_url;?>files/js/fastclick.js"></script>

    <link rel="stylesheet" type="text/css" href="<?php echo $base_url;?>files/club/css/bullalert.css">
    <link rel="stylesheet" type="text/css" href="<?php echo $base_url;?>files/club/css/alert.css">
    <link rel="stylesheet" type="text/css" href="<?php echo $base_url;?>files/club/css/person1-1.0.0.css">
    <script type="text/javascript">

        window.addEventListener('load', function() {
            FastClick.attach(document.body);
        }, false);

        var newNum = "";
        var per = window.innerWidth / 530;
        var globalData = {
            "baseUrl": "<?php echo $base_url;?>",
            "dealerNum": "1",
            "cardText":"房卡",
            "session":"YTI2NDQ5ZmViOTNkY2VhZWRlNDMxZjlkZDcyZGVlNGY=",
            "apiUrl": "<?php echo $base_url;?>",
            "mhUrl": "<?php echo $base_url;?>f/yh"
        };
        var userData = {
            "accountId": "",
            "nickname": "",
            "avatar": "",
            "s": "",
            "aid": "",
            "phone": "<?php echo $phone;?>"
        };
    </script>

    <style type="text/css">
        *{padding: 0;margin:0;-webkit-tap-highlight-color:rgba(0,0,0,0);-webkit-backface-visibility: hidden;}a {text-decoration: none;color: #fff;}ul {list-style: none;}input{border: none;outline:none}body{font-family: 'Helvetica Neue', Helvetica, 'Hiragino Sans GB', 'Microsoft YaHei', 微软雅黑, Arial, sans-serif;cursor: default;}
        img{border: none;}
        .main{position: relative; width: 100%;margin: 0 auto;}
        .head{position: relative;width: 100%;height: 25vw;overflow: hidden;}
        .head .avatar{position: absolute;top: 2vw;left: 3vw;width: 21vw;height: 21vw;border-radius: 4px;}
        .head .avatar .id{position: absolute;bottom: 0;width: 100%;height: 6vw;line-height: 6vw;font-size: 12pt;text-align: center;color: white;background-color: rgba(0,0,0,0.7);}
        .head .avatar img{position: absolute;border-radius: 4px;width: 100%;height: 100%;}
        .head .name{position: absolute;top: 2vw;left: 27vw;width: 60vw;height: 10.5vw;line-height: 10.5vw;font-size: 13pt;color: white;}
        .phone{position: absolute;left: 27vw; bottom: 2vw;width: 27vw;height: 8vw;}
        .changePhone{position: absolute;left: 27vw; bottom: 2vw;width: 40vw;height: 7vw;font-size: 2.2vh;color: #39d6fe;}
        .roomcard{position: absolute;bottom: 2vw;right: 4vw;width: 24vw;height: 18vw;border-style: solid;border-color: orange;border-width: 0.1vh;border-radius: 0.5vh;}
        .roomcard .num{position: absolute;top: 1vw;width: 100%;height: 9vw;line-height: 9vw;font-size: 2.5vh;color: white;text-align: center;overflow: hidden;}
        .roomcard .text{position: absolute;top: 8vw;width: 100%;height: 9vw;line-height: 9vw;font-size: 2.3vh;color: orange;text-align: center;overflow: hidden;}

        .transf{transform-style: preserve-3d;animation:transf .5s infinite linear;-webkit-animation:transf .5s infinite linear;}
        @keyframes transf{from {-webkit-transform: rotateY(0deg);} to{-webkit-transform: rotateY(360deg)}}
        @-webkit-keyframes transf {from {-webkit-transform: rotateY(0deg);} to{-webkit-transform: rotateY(360deg)}}

        .rcIcon{position: absolute;top: 2vw;left: 3vw;width: 9.375vw;height: 9.375vw;}
        .rcContent{position: absolute;left: 15.375vw;width: 50vw;height: 13.75vw;line-height: 13.75vw;font-size: 12pt;color: white;}
        .rcArrow{position: absolute;right: 3vw;top: 4.0625vw;width: 3.125vw;height: 5.625vw;}
        .sendRedpackage{position: relative;height: 13.75vw;overflow: hidden;margin-top: 5vw;}
        .userList{position: relative;height: 13.75vw;overflow: hidden;margin-top: 1vw;}
        .groupMenuDetail{position: relative;height: 27.5vw;margin-top: 1vw;overflow: hidden;}
        .gameMenu{position: relative;height: 25vw;text-align: center;overflow: hidden;margin-top: 5vw;}
        .gameListItem{position: absolute;width: 18vw;height: 25vw;font-size: 12pt;color: white;text-align: center;}
        .gameScoreTitle{position: relative;height: 13vw;line-height: 13vw;font-size: 12pt;color: white;text-align: center;margin-top: 1vw;}

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

<body >
<div id="loading" style="position: fixed;width:100%;height:100%;top:0;left:0;background: #000" >
    <img src="<?php echo $image_url;?>files/images/common/loading.gif" style="top: 40%;position: absolute;left: 50%;margin-left: -45px;margin-top: -45px;" />
</div>

<div class="main" id="app-main"  v-cloak>

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

        </div>
    </div>

    <div id="validePhone">
        <div class="phoneMask" style="position: fixed;z-index: 98;top: 0;left: 0;width: 100%;height: 100%;background-color: rgba(255,255,255,1);" v-on:click="hideBindPhone"></div>
        <div class="phoneFrame" style="position: fixed;z-index: 99;width: 100vw;max-width: 100vw; top: 2%; left: 0%;background-color: #fff; text-align: center; overflow: hidden;opacity: 1; color: white;">

            <div style="height: 8vw;"></div>

            <div style="position: relative;height: 15vw;word-wrap: break-word;word-break: break-all;color: black;background-color: white;border-top: solid;border-color: #e6e6e6;border-width: 0px;" v-show="userPhone.length>1">
                <div style="position: absolute;top: 1vw;left: 4vw;width: 92vw;height: 11vw;line-height: 11vw;font-size: 4vw;-webkit-appearance:none;background-color: rgba(240,240,242,0);">
                    <div style="position: absolute;left: 4vw;width: 80vw;text-align: left;color: black;">
                        手机号：{{userPhone}}
                    </div>
                </div>
            </div>

            <div style="position: relative;height: 15vw;word-wrap: break-word;word-break: break-all;color: #000;background-color: white;border-top: solid;border-color: #e6e6e6;border-width: 0px;" v-show="userPhone.length<1">
                <div style="position: absolute;top: 1vw;left: 4vw;width: 92vw;height: 11vw;line-height: 11vw;border-style: solid; border-color: rgba(240,240,242,1);border-width: 1px;border-radius: 0.5vh;font-size: 4vw;-webkit-appearance:none;background-color: rgba(240,240,242,1);">
                    <div style="position: absolute;left: 4vw;width: 20vw;text-align: left;color: gray;">
                        手机号：
                    </div>
                    <input  v-on:input="phoneChangeValue()" v-model="sPhone" type="number" name="phone" placeholder="" style="padding:0 0px 0 0px;position: absolute;top: 0vw;left: 20vw;width: 72vw;height: 11vw;line-height: 6vw;font-size: 4vw;-webkit-appearance: none;background-color: rgba(0,0,0,0);">
                </div>
            </div>

            <div style="height: 2vw;"></div>

            <div style="position: relative;height: 15vw;word-wrap: break-word;word-break: break-all;color: #000;background-color: white;border-top: solid;border-color: #e6e6e6;border-width: 0px;">
                <div style="position: absolute;top: 1vw;left: 4vw;width: 62vw;height: 11vw;line-height: 11vw;border-style: solid; border-color: rgba(240,240,242,1);border-width: 1px;border-radius: 0.5vh;font-size: 4vw;-webkit-appearance:none;background-color: rgba(240,240,242,1);">
                    <div style="position: absolute;left: 4vw;width: 20vw;text-align: left;color: gray;">
                        验证码：
                    </div>
                    <input  v-model="sAuthcode" type="number" name="phone1" placeholder="" style="padding:0 0px 0 0px;position: absolute;top: 0vw;left: 20vw;width: 41vw;height: 11vw;line-height: 6vw;font-size: 4vw;-webkit-appearance: none;background-color: rgba(0,0,0,0);">
                </div>
                <div id="authcode" v-on:click="getAuthcode()" style="position: absolute;top:  1vw;right: 4vw; width: 25vw;height: 11vw;line-height: 11vw;background-color: rgb(211,211,211);font-size: 3.5vw;border-radius: 0.5vh;color: white;">
                    {{authcodeText}}
                </div>
            </div>

            <div style="height: 2vw;"></div>

            <div style="position: relative;height: 15vw;word-wrap: break-word;word-break: break-all;color: #000;background-color: white;border-top: solid;border-color: #e6e6e6;border-width: 0px;">
                <div style="position: absolute;top: 1vw;left: 4vw;width: 92vw;height: 11vw;line-height: 11vw;border-style: solid; border-color: rgba(240,240,242,1);border-width: 1px;border-radius: 0.5vh;font-size: 4vw;-webkit-appearance:none;background-color: rgba(240,240,242,1);">
                    <div style="position: absolute;left: 4vw;;width: 20vw;text-align: left;color: gray;">
                        密&nbsp&nbsp&nbsp&nbsp钥：
                    </div>
                </div>
                <input  id="sPwd" v-model="sPwd" type="number" name="pwd1" placeholder="" style="padding:0 0px 0 0px;position: absolute;top: 1.5vw;left: 25vw;width: 72vw;height: 11vw;line-height: 6vw;font-size: 4vw;-webkit-appearance: none;background-color: rgba(0,0,0,0);">

            </div>

            <div style="height: 6vw;"></div>

            <div style="position: relative; left: 50%; width: 72vw; margin-left: -36vw;line-height: 10vw; font-size: 4vw;display: flex;border-radius: 2vw;" v-on:click="modifyPwd()">
                <div style="display: block;-webkit-box-flex:1;flex: 1;text-decoration: none;-webkit-tap-highlight-color:transparent;position: relative;margin-bottom: 0;color: rgb(255,255,255);border-top: solid;border-color: #e6e6e6;border-width: 0px;background-color: rgb(109,125,212);border-radius: 1vw;">确认</div>
            </div>
            <div style="height:4vw;"></div>
        </div>
    </div>

</div>

</body>

<script type="text/javascript" src="<?php echo $image_url;?>files/js/jquery-1.9.1.min.js"></script>
<script type="text/javascript" src="<?php echo $image_url;?>files/js/vue.min.js" ></script>
<script type="text/javascript" src="<?php echo $image_url;?>files/js/vue-resource.min.js" ></script>
<script type="text/javascript" src="<?php echo $base_url;?>files/club/js/bscroll.min.js" ></script>
<script type="text/javascript" src="<?php echo $base_url;?>files/club/js/modifyPwd-1.0.2.js"></script>

</html>
