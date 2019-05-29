<html ng-app="app">
<head>
<meta charset="utf-8" >
<meta name="viewport" content="width=device-width,initial-scale=1.0, minimum-scale=1.0, maximum-scale=1.0, user-scalable=no"/>
<meta name="format-detection" content="telephone=no" />
<meta name="msapplication-tap-highlight" content="no" />
<title>好友管理</title>
<link rel="stylesheet" href="<?php echo $image_url;?>files/css/loading.css?_version=<?php echo $front_version;?>">
<script type="text/javascript" src="<?php echo $image_url;?>files/js/jquery-1.9.1.min.js"></script>
<script type="text/javascript" src="<?php echo $image_url;?>files/js/jquery.marquee.min.js"></script>

<script type="text/javascript" src="<?php echo $image_url;?>files/js/angular.min.js"></script>
<script src="https://res.wx.qq.com/open/js/jweixin-1.0.0.js"></script>
<script type="text/javascript" src="<?php echo $image_url;?>files/js/fastclick.js?_version=<?php echo $front_version;?>"></script>
<script>
     var image_url = "<?php echo $image_url;?>";
</script>

</head>
<style>
*{padding: 0;margin:0;}a {text-decoration: none;color: #fff;}ul {list-style: none;}input{border: none;outline:none}body{font-family: 'Helvetica Neue', Helvetica, 'Hiragino Sans GB', 'Microsoft YaHei', 微软雅黑, Arial, sans-serif;cursor: default;}
img{border: none;}
.main{width: 100%;position:relative;margin:0 auto;-webkit-touch-callout:none;-webkit-user-select:none;-khtml-user-select:none;-moz-user-select:none;-ms-user-select:none;user-select:none;}

.header {position: fixed;left: 0;top: 0;width:100vw;z-index:100;background:url("<?php echo $image_url;?>files/images/friend/header.png")no-repeat;background-size:100% 100%;}
.header .header-menu {padding:0 3vw;}
.header .header-menu .menu-item{display:inline-block;position: relative;width:13vw;font-size: 0;}
.header .header-menu .menu-item-selected{background:url("<?php echo $image_url;?>files/images/friend/activeH.png")no-repeat;background-size:100% 100%;}
.header .menu-item img{width:100%;margin:3px 1vw;vertical-align: middle;}
.header .menu-item .icon-news{width: 7px;height: 7px;background-color: #FA2E2E;position: absolute;top: 7px;right: 15px;border-radius: 50%;}
.header .header-menu .menu-item.menu-item-checked{float:right;text-align: center;width:18vw;}
.header .menu-item-checked p{color:#F0D582;margin-top: 1vw;font-size:14px;}
.header .menu-item-checked .label-text{display: inline-block;width: 17vw;position: relative;color: #ffffff;}
.header .menu-item-checked .label-text span{display: inline-block;width: 17vw;box-sizing: border-box;}
.header .menu-item-checked .label-checked span {padding-left: 14px;text-align: left;font-size:16px;}
.header .menu-item-checked .label-no-checked span {padding-right: 14px;text-align: right;font-size:16px;}
.header .menu-item-checked .icon-btn{width: 17vw;position: absolute;left: 0;top: 0;z-index: -1;}
.header .menu-item-checked .icon-btn img{margin:0;}
/* .header .menu-item-checked .icon-checked {background:url("<?php echo $image_url;?>files/images/friend/checked.png")no-repeat;background-size:100% 100%;}
.header .menu-item-checked .icon-no-checked {background:url("<?php echo $image_url;?>files/images/friend/no-checked.png")no-repeat;background-size:100% 100%;} */

.bottom-menu {height: 10vh;position: fixed;left: 0;bottom: 0;width: 100vw;z-index:100;background:url("<?php echo $image_url;?>files/images/hall/menu.png")no-repeat;background-size:100% 100%;}
.bottom-menu ul {width:100vw;}
.bottom-menu .menu-item{position: relative;width: 25vw;float: left;height: 10vh;text-align: center;padding-top: 1vh;}
.bottom-menu .menu-item a {display:inline-block;}
.bottom-menu .menu-item img{height: 5vh;}
.bottom-menu .menu-item p {color: #F0D582;font-weight: 600;}
.bottom-menu .menu-item span {position: absolute;right: 0;top: 2vh;width: 2px;height: 6vh;background:url("<?php echo $image_url;?>files/images/hall/border.png")no-repeat;background-size:100% 180%;}
.bottom-menu .menu-item-selected{background:url("<?php echo $image_url;?>files/images/hall/active.png")no-repeat;background-size:100% 100%;}

.list-search{position: fixed;top: 0;left: 0;width: 100%;height: 18vw;}
.list-search .search-item{position: absolute;bottom: -40px;left: 0;}
.list-search .search-item-left{width: 70vw;float: left;text-align: right;background: rgba(39,32,92,0.2);}
.list-search .search-item-left input{height: 40px;width: 70%;color: #f7a16d;background: rgba(39, 32, 92, 0.5);border: 1px solid rgba(255,255,255,0.6);padding-left: 5px;border-right: 0;border-radius: 5px 0 0 5px;}
.list-search .search-item-right{float:left;}
.list-search .search-item-right button{height: 40px;width: 14vw;background: rgba(30, 8, 37, 0.6);border: 1px solid rgba(255,255,255,0.6);color: #ffffff;border-radius: 0 5px 5px 0;}

.friends-list{position: fixed;top: 18vw;left: 3vw;margin-top: 55px;max-height: 68vh;overflow: auto;background-color: rgba(255,255,255,0.1);border: 1px solid rgba(255,255,255,0.5);width: 94vw;border-radius: 5px;margin-bottom: 14vh;z-index: 80;}
.friends-list .list-ul{margin: 0 2vw 6px 2vw;-webkit-touch-callout:none;-webkit-user-select:none;-khtml-user-select:none;-moz-user-select:none;-ms-user-select:none;user-select:none;}
.friends-list .list-ul .alif-li{color:#F3EFFC;margin-top: 6px;padding-left:3vw;}
.friends-list .list-ul .user-li {position: relative;background-color:rgba(39, 32, 92,0.5);height: 54px;margin-top: 6px;padding-left: 3vw;border: 1px solid rgba(13, 1, 39,0.2);border-radius: 3px;}
.friends-list .list-ul .user-li-selected{background-color:rgba(39, 32, 92,1);border: 1px solid rgba(255,255,255,0.5);}
.friends-list .list-ul .user-li .list-li-left{float:left;}
.friends-list .list-ul .list-li-left img{height: 42px;width: auto;border-radius: 50% 50%;margin: 6px 0;}
.friends-list .list-ul .list-li-right{height: 100%;float: left;line-height: 54px;margin-left: 2vw;}
.friends-list .list-ul .list-li-right .list-li-name{color:#F3EFFC;height: 30px;line-height: 30px;}
.friends-list .list-ul .list-li-right .list-nickname{display: inline-block;max-width: 25vw;overflow: hidden;text-overflow: ellipsis;white-space: nowrap;vertical-align: middle;}
.friends-list .list-ul .list-li-right .list-aliases{vertical-align: middle;}
.friends-list .list-ul .list-li-right .list-aliases span{display: inline-block;max-width: 25vw;overflow: hidden;text-overflow: ellipsis;white-space: nowrap;vertical-align: middle;}
.friends-list .list-ul .list-li-right .list-li-id{color:#CECAF5;line-height: 24px;}
.friends-list .list-ul .list-li-img{margin-top:3vw}
.friends-list .list-ul .list-li-img > img{width: 60px; height: 30px; vertical-align: middle; float: right;}
.friends-list .user-operation{position: absolute;width: 30vw;top: -18px;right: 42px;height: 90px;border: 1px solid #FFDFAE;background-color: #FDECC1;color: #714D29;border-radius: 5px;display:none;z-index:1;}
.friends-list .user-operation a{color: #714D29;display: inline-block;width: 100%;text-align: center;height: 30px;line-height: 30px;}
.friends-list .user-operation p{height: 1px;width: 100%;background:url("<?php echo $image_url;?>files/images/friend/cut-off.png")no-repeat;background-size:100% 180%;}
.friends-list .list-more {text-align: center;color: #ffffff;height: 36px;line-height: 36px;}

.blacklist-list{margin-top: 0;max-height: 76vh;}

.alert{position: fixed;width: 100%;height:100%;top:0;left:0;z-index: 111;color: #fff;font-family: simHei;}
.alert .alertBack{width: 100%;height:100%;background: #000;opacity:0.8;position: absolute;}
.alert .mainPart{position: relative;top: 50%;left: 11vw;margin-top:-110px ;width: 78vw;height:220px; }
.alert .mainPart .alertText{position: absolute;width:100%;line-height: 8.5vh;font-size: 2.5vh;width: 36vh;left:50%;margin-left:-18vh;top:10.6vh;text-align: center; color:#714D29;}
.alert .mainPart .alertInput{position: absolute;width: 54vw;left: 12vw;top: 78px;text-align: center;}
.alert .mainPart .alertInput input{background-color: #EDD8B4;border: 1px solid #DAB674;height: 38px;width: 100%;padding-left: 10px;color: #7d2f00;}
.alert .mainPart .buttonMiddle{position: absolute;height: 40px;width: 28vw;left:24vw;bottom:10px;text-align: center;background:url("<?php echo $image_url;?>files/images/common/button2.png");background-size:100%;}
.alert .mainPart .buttonLeft{position: absolute;height: 40px;width: 28vw;left:5vw;bottom:10px;text-align: center;}
.alert .mainPart .buttonRight{position: absolute;height: 40px;width: 28vw;right:5vw;bottom:10px;text-align: center;}
.alert .mainPart .backImg{position: absolute;width:100%;height:100%;border-radius: 10px;top:0;left:0;background: #FFF4DC;}
.alert .mainPart .backImg .blackImg{position: absolute;width:72vw;height:168px;top:48px;left:3vw; background:url("../files/images/common/backImg.png") no-repeat; background-size:contain;} 
.alert .mainPart .alertTitle{position: absolute;left: 0;top: 0;width: 100%;height: 40px;background: url("<?php echo $image_url;?>files/images/common/storetitle.png") no-repeat top center;z-index: 1;font-size: 20px;text-align: center;line-height: 34px;color: #7D2F00;}
/* .alert .mainPart .btnCancel{display: inline-block;width: 100px;height: 40px;background: url("../images/common/cancelRoom.png") no-repeat;background-size: cover;position: absolute;bottom: 0;left: 4vw;} */
/* .alert .mainPart .buttonWatch{display: inline-block;width: 100px;height: 40px;background: url("../images/watch/joinWatch.png") no-repeat;background-size: cover;position: absolute;bottom: 0;right: 4vw;} */
.alert .mainPart .btnCancel{background:url("<?php echo $image_url;?>files/images/friend/cancel.png") no-repeat; background-size:100% 100%;}
.alert .mainPart .btnSend{background:url("<?php echo $image_url;?>files/images/friend/send.png") no-repeat; background-size:100% 100%;}
.alert .mainPart .btnConfirm{background:url("<?php echo $image_url;?>files/images/friend/confirm.png") no-repeat; background-size:100% 100%;}
.alert .mainPart .backImg .blackImgNews{position: absolute;width: 94%;height: 87%;top: 6.5vh;left: 3%;background: url(../files/images/common/bullInnerborder.png) no-repeat;background-size: 100% 100%;}
.alert .mainPartNews{width: 80vw;height: 430px;margin: 0;top: 120px;left: 10vw;}
.alert .mainPartNews .topClose{position: absolute;right: -3vw;top: -20px;z-index: 1;}
.alert .mainPartNews .topClose img{width: 10vw;height: auto;}
.alert .mainPartNews .alertList{position: absolute;z-index: 1;color: #714D29;top: 74px;left: 7vw;height: 310px;width: 66vw;overflow: auto;}
.alert .mainPartNews .alertList .newsList{overflow:auto;}
.alert .mainPartNews .alertList .newsListItem{height: 48px;line-height: 48px;font-size: 14px;border-bottom:1px solid #DAB674;}
.alert .mainPartNews .alertList .newItemLeft{float:left;width: 30vw;overflow: hidden;text-overflow: ellipsis;white-space: nowrap;}
.alert .mainPartNews .alertList .newItemRight{float:right;}
.alert .mainPartNews .alertList .consentBtn{width: 14vw;vertical-align: middle;}
.alert .mainPartNews .alertList .rejectBtn{width: 14vw;vertical-align: middle;}

input::-webkit-input-placeholder {color: #886956;}
::-webkit-input-placeholder {color:#886956;} /* WebKit, Blink, Edge */
:-moz-placeholder {color:#886956;}/* Mozilla Firefox 4 to 18 */
::-moz-placeholder {color:#886956;}/* Mozilla Firefox 19+ */
:-ms-input-placeholder {color:#886956;}/* Internet Explorer 10-11 */
</style>
<script>
</script>
<body style="background: #000;" >
<?php if ($broadcast) {
    ?>
    <div style="position:fixed;width: 100%;color: white;background: rgba(0,0,0,0.5);font-size: 15px;z-index: 100;padding: 10px">
        <div class='marquee' style="float: left;margin-left:35px;width: 100%;overflow: hidden">
            <?php echo $broadcast; ?>
        </div>
    </div>

    <div style="position: fixed;top: 0vw;z-index: 101;padding: 5px 10px 10px 10px">
        <img style="width: 30px;height: 30px" src="<?php echo $image_url; ?>files/images/common/alert_icon.png" />
    </div>
<?php
}?>
<div style="position: fixed;width:100%;height:100%;top:0;left:0;background: #000;z-index:120;" id="loading">
	<img src="<?php echo $image_url;?>files/images/common/loading.gif" style="top: 40%;position: absolute;left: 50%;margin-left: -45px;margin-top: -45px;" />
</div>
<div class="main" ng-controller="myCtrl" style="display: none;">

	<div class="alert" ng-show="isShowAlert" style="z-index:112;">
		<div class="alertBack"></div>
		<div class="mainPart" ng-class="alertType==8?'mainPartNews':''">
            <div class="alertTitle">
                {{alertTitle}}
            </div>
			<div class="backImg">
				<div class="blackImg"></div>
			</div>
			<div class="alertText">{{alertText}}</div>
			<div ng-show="alertType==1">
				<div class="buttonMiddle" ng-click="closeAlert()"></div>
			</div>
			<div ng-show="alertType==6">
				<div class="buttonMiddle" ng-click="closeAlert()"></div>
			</div>
            <div ng-show="alertType==7">
				<div class="buttonMiddle" ng-click="closeAlertAll()"></div>
			</div>
            <div ng-show="alertType==11">
                <div class="buttonLeft btnCancel" ng-click="closeAlert()"></div>
                <div class="buttonRight btnConfirm" ng-click="doOperate()"></div>
			</div>
		</div>
	</div>
    <div class="alert" ng-show="isShowAlertOperate">
		<div class="alertBack"></div>
		<div class="mainPart" ng-class="alertTypeOperate==8?'mainPartNews':''">
            <div class="alertTitle">
                {{alertTitleOperate}}
            </div>
            <div class="topClose" ng-if="alertTypeOperate==8" ng-click="closeAlertOperate()">
                <img src="<?php echo $image_url;?>files/images/friend/close.png" />
            </div>
			<div class="backImg">
				<div class="blackImg" ng-if="alertTypeOperate!=8"></div>
                <div class="blackImgNews" ng-if="alertTypeOperate==8"></div>
			</div>
			<div class="alertText" ng-if="alertTypeOperate!=9 && alertTypeOperate!=12 && alertTypeOperate!=8">{{alertTextOperate}}</div>
            <div class="alertInput" ng-if="alertTypeOperate==9">
                <input type="text" placeholder="请输入好友游戏ID" id="userID" />
            </div>
            <div class="alertList" ng-if="alertTypeOperate==8">
                <ul class="newsList" ng-repeat="listDatas in NewsListData">
                    <li class="newsListItem" ng-repeat="listData in listDatas">
                        <div class="newItemLeft">{{listData.nickname}}</div>
                        <div class="newItemRight">
                            <img class="consentBtn" src="<?php echo $image_url;?>files/images/friend/consent.png" id="{{listData.member_id}}" ng-click="newsConsent($event)" />
                            <img class="rejectBtn" src="<?php echo $image_url;?>files/images/friend/reject.png" id="{{listData.member_id}}" ng-click="newsReject($event)" />
                        </div>
                    </li>
                </ul>
            </div>
            <div class="alertInput" ng-if="alertTypeOperate==12">
                <input type="text" placeholder="请输入备注" id="userAliases" />
            </div>
			<div ng-show="alertTypeOperate==1">
				<div class="buttonMiddle" ng-click="closeAlertOperate()"></div>
			</div>
			<div ng-show="alertTypeOperate==6">
				<div class="buttonMiddle" ng-click="closeAlertOperate()"></div>
			</div>
            <div ng-show="alertTypeOperate==9">
				<div class="buttonLeft btnCancel" ng-click="closeAlertOperate()"></div>
                <div class="buttonRight btnSend" ng-click="addFriend()"></div>
			</div>
            <div ng-show="alertTypeOperate==11 || alertTypeOperate==12">
                <div class="buttonLeft btnCancel" ng-click="closeAlertOperate()"></div>
                <div class="buttonRight btnConfirm" ng-click="doOperate()"></div>
			</div>
		</div>
	</div>

	<img id="bodyBackground" src="<?php echo $image_url;?>files/images/hall/body.png" ng-click="operationHide()"  style="width: 100%;height: auto;position: fixed;left: 0;top: 0;">
    <div class="header">
        <ul class="header-menu">
            <li class="menu-item" ng-class="(currentMenu=='list')?'menu-item-selected':''" ng-click="menuChoose('list')">
                <img src="<?php echo $image_url;?>files/images/friend/list.png" />
            </li>
            <li class="menu-item" ng-class="(currentMenu=='blacklist')?'menu-item-selected':''" ng-click="menuChoose('blacklist')">
                <img src="<?php echo $image_url;?>files/images/friend/blacklist.png" />
            </li>
            <li class="menu-item" ng-class="(currentMenu=='invite')?'menu-item-selected':''" ng-click="menuChoose('invite')">
                <img src="<?php echo $image_url;?>files/images/friend/invite.png" />
            </li>
            <li class="menu-item" ng-class="(currentMenu=='apply')?'menu-item-selected':''" ng-click="menuChoose('apply')">
                <img src="<?php echo $image_url;?>files/images/friend/apply.png" />
            </li>
            <li class="menu-item" ng-class="(currentMenu=='news')?'menu-item-selected':''" ng-click="menuChoose('news')">
                <img src="<?php echo $image_url;?>files/images/friend/news.png" />
                <i ng-if="has_fri_req" class="icon-news"></i>
            </li>
            <li class="menu-item menu-item-checked" ng-click="toggle()">
                <p>管理功能</p>
                <label class="label-text label-checked" ng-if="settingCheck == '1'"><span>开</span><i class="icon-btn icon-checked"><img src="<?php echo $image_url;?>files/images/friend/checked.png" /></i></label>
                <label class="label-text label-no-checked" ng-if="settingCheck == '0'"><span>关</span><i class="icon-btn icon-no-checked"><img src="<?php echo $image_url;?>files/images/friend/no-checked.png" /></i></label>
            </li>
        </ul>
    </div>
    <div class="bottom-menu">
        <ul>
            <li class="menu-item" ng-repeat="menuData in menuDatas">
                <a ng-href="{{menuData.url}}">
                    <img ng-src="<?php echo $image_url;?>files/images/hall/{{menuData.img}}" />
                    <p>{{menuData.name}}</p>
                    <span ng-if="!menuData.end"></span>
                </a>
            </li>
        </ul>
    </div>
    <!---好友列表start---->
    <div class="list-search" ng-if="currentMenu=='list'" ng-click="operationHide()">
        <div class="search-item">
            <div class="search-item-left">
                <input type="text" id="search" placeholder="请输入成员昵称" />
            </div>
            <div class="search-item-right">
                <button type="button" ng-click="userSearch()">搜索</button>
            </div>
        </div>
    </div>
    <div class="friends-list" ng-if="currentMenu=='list'" ng-click="operationHide()">
        <div ng-repeat="(key,listDatas) in userListData">
            <ul class="list-ul">
                <li class="alif-li">{{key}}</li>
            </ul>
            <ul class="list-ul" ng-repeat="(key,listData) in listDatas">
                <li class="user-li" ng-touchstart="touchStart($event)" ng-touchend="touchEnd($event)">
                    <div class="list-li-left">
                        <img ng-src="{{listData.avatar_url}}" />
                    </div>
                    <div class="list-li-right">
                        <p class="list-li-name">
                            <label class="list-nickname">{{listData.nickname}}</label>
                            <label class="list-aliases" ng-if="listData.aliases">(<span>{{listData.aliases}}</span>)</label>
                        </p>
                        <p class="list-li-id"><span>ID:</span>{{listData.user_code}}</p>
                    </div>
                    <div class="list-li-img" ng-click="changeAttention(listData.member_id,listData.attention)">
                        <img ng-show="listData.attention == '0'" src="<?php echo $base_url;?>files/images/box/off.png" />
                        <img ng-show="listData.attention == '1'" src="<?php echo $base_url;?>files/images/box/on.png" />
                    </div>
                    <div class="user-operation">
                        <a ng-touchstart="userRemark('{{listData.member_id}}')">备注</a>
                        <p></p>
                        <a ng-touchstart="joinBlacklist('{{listData.member_id}}')">移到小黑屋</a>
                        <p></p>
                        <a ng-touchstart="deleteUser('{{listData.member_id}}')">删除好友</a>
                    </div>
                </li>
            </ul>
        </div>
        <div class="list-more" ng-if="listMore == 'more'" ng-click="getMoreList()">&darr;点击加载更多</div>
        <div class="list-more" ng-if="listMore == 'loading'">加载中……</div>
        <div class="list-more" ng-if="listMore == 'none'">暂无更多数据</div>
    </div>   
    <!---好友列表end---->
    <!---小黑屋列表start---->
    <div class="friends-list blacklist-list" ng-if="currentMenu=='blacklist'" ng-click="operationHide()">
        <div ng-repeat="(key,listDatas) in blacklistData">
            <ul class="list-ul">
                <li class="alif-li">{{key}}</li>
            </ul>
            <ul class="list-ul" ng-repeat="(key,listData) in listDatas">
                <li class="user-li" ng-touchstart="touchStart($event)" ng-touchend="touchEnd($event)">
                    <div class="list-li-left">
                        <img ng-src="{{listData.avatar_url}}" />
                    </div>
                    <div class="list-li-right">
                        <p class="list-li-name">
                            <label class="list-nickname">{{listData.nickname}}</label>
                            <label class="list-aliases" ng-if="listData.aliases">(<span>{{listData.aliases}}</span>)</label>
                        </p>
                        <p class="list-li-id"><span>ID:</span>{{listData.user_code}}</p>
                    </div>
                    <div class="user-operation">
                        <a ng-touchstart="userRemark('{{listData.member_id}}')">备注</a>
                        <p></p>
                        <a ng-touchstart="recoverList('{{listData.member_id}}')">恢复好友</a>
                        <p></p>
                        <a ng-touchstart="deleteUser('{{listData.member_id}}')">删除好友</a>
                    </div>
                </li>
            </ul>
        </div>
        <div class="list-more" ng-if="blacklistMore == 'more'" ng-click="getMoreBlacklist()">&darr;点击加载更多</div>
        <div class="list-more" ng-if="blacklistMore == 'loading'">加载中……</div>
        <div class="list-more" ng-if="blacklistMore == 'none'">暂无更多数据</div>
    </div>   
    <!---小黑屋列表end---->  
    </div>
<!-- 绑定手机号码 -->
<div id="validePhone" ng-show="isShowBindPhone">
    <div class="phoneMask" style="position: fixed;z-index: 98;top: 0;left: 0;width: 100%;height: 100%;background-color: rgba(0,0,0,0.5);"></div>
    <div class="phoneFrame" style="position: fixed;z-index: 99;width: 80vw;max-width: 80vw; top: 50%; left: 50%;-webkit-transform:translate(-50%,-60%); background-color: #fff; text-align: center; border-radius: 8px; overflow: hidden;opacity: 1; color: white;">
        <div style="height: 2.2vw;"></div>
        <div style="padding: 1vw;font-size: 4vw; line-height: 5vw; word-wrap: break-word;word-break: break-all;color: #000;background-color: white;">防止房卡数据丢失，请绑定手机</div>
        <div style="height: 2.2vw;"></div>
        <div style="position: relative;height: 15vw;word-wrap: break-word;word-break: break-all;color: #000;background-color: white;border-top: solid;border-color: #e6e6e6;border-width: 0px;">
            <input  ng-input="phoneChangeValue()" ng-model="sPhone" type="number" name="phone" placeholder="输入手机号" style="padding:0 12px 0 12px;position: absolute;top:  2.5vw;left: 4vw;width: 48vw;height: 11vw;line-height: 6.5vw;border-style: solid;border-width: 1px;border-radius: 0.5vh;border-color: #e6e6e6;font-size: 4vw;-webkit-appearance: none;">
            <div id="authcode" ng-click="getAuthcode()" style="position: absolute;top:  2.5vw;right: 4vw; width: 22vw;height: 10vw;line-height: 10vw;background-color: rgb(211,211,211);font-size: 3.5vw;border-radius: 0.5vh;color: white;">
                {{authcodeText}}
            </div>
        </div>
        <div style="position: relative;height: 15vw;word-wrap: break-word;word-break: break-all;color: #000;background-color: white;border-top: solid;border-color: #e6e6e6;border-width: 0px;">
            <input ng-model="sAuthcode" type="number" name="phone1" placeholder="输入验证码" style="padding:0 12px 0 12px;position: absolute;top: 1vw;left: 4vw;width: 72vw;height: 11vw;line-height: 6.5vw;border-style: solid;border-width: 1px;border-radius: 0.5vh;border-color: #e6e6e6;font-size: 4vw;-webkit-appearance: none;">

        </div>
        <div style="height: 2.2vw;"></div>
        <div style="position: relative; left: 4vw;width: 72vw;line-height: 10vw; font-size: 4vw;display: flex;border-radius: 2vw;" ng-click="bindPhone()">
            <div style="display: block;-webkit-box-flex:1;flex: 1;text-decoration: none;-webkit-tap-highlight-color:transparent;position: relative;margin-bottom: 0;color: rgb(255,255,255);border-top: solid;border-color: #e6e6e6;border-width: 0px;background-color: rgb(64,112,251);border-radius: 1vw;">立即绑定</div>
        </div>
        <div style="height:4vw;"></div>
    </div>
</div>

<style>
.waiting{position: fixed;width:100%;height:100%;top:0;left:0;z-index: 111;}
.waiting .waitingBack{position: fixed;width:100%;height:100%;top:0;left:0;background: #000;opacity:.5;}
</style>
<div class="waiting" ng-show="is_operation">
	<div class="waitingBack"></div>
	<div class="load4">
		<div class="loader">Loading...</div>
	</div>
</div>
<script>
    <?php if (!empty($create_info)) {
        ; ?>
    localStorage.createInfo = <?php echo $create_info; ?>;
    <?php
    };?>
    var userData = {
        "id":"<?php echo $user['account_id'];?>",
        "name":"<?php echo $user['nickname'];?>",
        "avatar":"<?php echo $user['headimgurl'];?>",
        "card":"<?php echo $card;?>",
        "phone":"<?php echo $user['phone'];?>",
        "userCode":"<?php echo $user['user_code'];?>",
        "socket_news": "<?php echo $socket;?>"
    };
    var has_fri_req = <?php echo $has_fri_req; ?>;
    var dealerNum = "<?php echo $dealer_num;?>";
    var accountId = "<?php echo $user['account_id'];?>";
    var session   = "<?php echo $session;?>";
    var baseUrl = "/";
    var currentUrl = "<?php echo $base_url;?>";
</script>
<script type="text/javascript" src="<?php echo $base_url;?>files/js/friend.js?15445511_version=<?php echo $front_version;?>"></script>
<script>
	wx.config({
        debug: false,
        appId: "<?php echo $config_ary['appId'];?>",
        timestamp: "<?php echo $config_ary['timestamp'];?>",
        nonceStr:"<?php echo $config_ary['nonceStr'];?>",
        signature: "<?php echo $config_ary['signature'];?>",
        jsApiList: [
            'onMenuShareTimeline',
            'onMenuShareAppMessage',
            'hideMenuItems'
        ]
    });
	wx.ready(function () {
		wx.hideMenuItems({
			menuList: [
			    "menuItem:copyUrl",
			    "menuItem:share:qq",
			    "menuItem:share:weiboApp",
			    "menuItem:favorite",
			    "menuItem:share:facebook",
			    "menuItem:share:QZone"
			] // 要隐藏的菜单项，只能隐藏“传播类”和“保护类”按钮，所有menu项见附录3
		});
		wx.onMenuShareTimeline({
	        title: "太古大厅",
	        desc: "朋友之间娱乐",
	        link: "<?php echo $room_url;?>",
	        imgUrl: '<?php echo $image_url;?>files/images/hall/share_icon_hall.jpg',
	        success: function (){
		        // 用户确认分享后执行的回调函数
		    },
		    cancel: function (){
		        // 用户取消分享后执行的回调函数
		    }
		});
		wx.onMenuShareAppMessage({
	        title: "太古大厅",
	        desc: "朋友之间娱乐",
	        link: "<?php echo $room_url;?>",
	        imgUrl: '<?php echo $image_url;?>files/images/hall/share_icon_hall.jpg',
	        success: function (){
		        // 用户确认分享后执行的回调函数
		    },
		    cancel: function (){
		        // 用户取消分享后执行的回调函数
		    }
		});
	});
	wx.error(function (res) {
   //     alert("error: " + res.errMsg);
    });

    $('.marquee').marquee({
        duration: 5000,
        delayBeforeStart: 0,
        direction: 'left',
    });
    function initialHeight() {
        var clientHeight =  document.documentElement.clientHeight;
        $(".bottom-menu").css('height',(clientHeight*0.1)+'px');
        $(".bottom-menu .menu-item").css({'height':(clientHeight*0.1)+'px','padding-top':(clientHeight*0.01)+'px'});
        $(".bottom-menu .menu-item img").css('height',(clientHeight*0.05)+'px');
        $(".bottom-menu .menu-item span").css({'height':(clientHeight*0.06)+'px','top':(clientHeight*0.02)+'px'});
        $("#bodyBackground,body").css("min-height",clientHeight+'px');
    }
    initialHeight();

</script>
</body>
</html>
