<html>
<head>
<meta charset="utf-8" >
<meta name="viewport" content="width=device-width,initial-scale=1.0, minimum-scale=1.0, maximum-scale=1.0, user-scalable=no"/>
<meta name="format-detection" content="telephone=no" />
<meta name="msapplication-tap-highlight" content="no" />
<title>消耗设置</title>

<script type="text/javascript" src="<?php echo $base_url;?>files/js/fastclick.js"></script>

<link rel="stylesheet" type="text/css" href="<?php echo $base_url;?>files/css/bullalert.css">
<link rel="stylesheet" type="text/css" href="<?php echo $base_url;?>files/d_30/css/common/alert.css">
<link rel="stylesheet" type="text/css" href="<?php echo $base_url;?>files/d_30/css/person/person1-1.0.0.css">
<script type="text/javascript">

	window.addEventListener('load', function() {
		FastClick.attach(document.body);
	}, false);

	var newNum = "";
	var per = window.innerWidth / 530;
	var globalData = {
		"baseUrl": "<?php echo $base_url;?>",
        "dealerNum": "1",
        "session":"YTI2NDQ5ZmViOTNkY2VhZWRlNDMxZjlkZDcyZGVlNGY=",
        "apiUrl": "<?php echo $base_url;?>",
        "orgId":"263",
        "orgGames":'[{"game_type":"1","consume_rule":{"firstWinner":{"init":"5","max":"20","setup":"3"},"secondWinner":{"init":"2","max":"10","setup":"0"},"thirdWinner":{"init":"1","max":"10","setup":"0"}}},{"game_type":"5","consume_rule":{"firstWinner":{"init":"5","max":"20","setup":"3"},"secondWinner":{"init":"2","max":"10","setup":"0"},"thirdWinner":{"init":"1","max":"10","setup":"0"}}},{"game_type":"9","consume_rule":{"firstWinner":{"init":"5","max":"20","setup":"3"},"secondWinner":{"init":"2","max":"10","setup":"0"},"thirdWinner":{"init":"1","max":"10","setup":"0"}}},{"game_type":"14","consume_rule":{"firstWinner":{"init":"5","max":"20","setup":"3"},"secondWinner":{"init":"2","max":"10","setup":"0"},"thirdWinner":{"init":"1","max":"10","setup":"0"}}},{"game_type":"16","consume_rule":{"firstWinner":{"init":"5","max":"20","setup":"3"},"secondWinner":{"init":"2","max":"10","setup":"0"},"thirdWinner":{"init":"1","max":"10","setup":"0"}}}]',
	};

	globalData.orgGames = eval('(' + globalData.orgGames + ')');
    
	
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
	[v-cloak] {
		display:none !important;
	}
</style>

</head>

<body >
	<div id="loading" style="position: fixed;width:100%;height:100%;top:0;left:0;background: #000" >
		<img src="<?php echo $base_url;?>files/images/loading.gif" style="top: 40%;position: absolute;left: 50%;margin-left: -45px;margin-top: -45px;" />
	</div>

	<div class="main" id="app-main"  v-cloak>
		
		<div class="alert" id="valert" v-show="isShowAlert">
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
				<div v-show="alertType==23">
					<div class="buttonMiddle" v-on:click="finishBindPhone()">确定</div>
				</div>				
			</div>
		</div>


		<div class="gameScoreTitle" style="margin-top: 0vw;">
			<div style="position: absolute;left: 4vw;font-size: 12pt;color: black;text-align: center;width: 48vw;">
				游戏
			</div>
			<div style="position: absolute;right: 4vw;font-size: 12pt;color: black;text-align: center;width: 55vw;">
				消耗
			</div>
			<div style="position: absolute;top: 12vw;width: 100vw;height: 0.5px;background-color: rgb(212,212,213);"></div>
		</div>

		<div style="position: relative;margin-top:0vw;">
			<div style="position: relative;">
				<div v-for="(item, index) in gameScoreList" style="position: relative;width: 100%;height: 24vw;line-height: 24vw;text-align: center;margin-top: 2px;color: white;overflow:hidden;" >
					<div style="position: absolute;left: 0vw;width: 14vw;height: 100%;" v-on:click="selecteGame(item)">
						<img v-show="item.checked==1" src="<?php echo $base_url;?>files/images/team/ug_game_checked.png" style="position: absolute;left: 6vw;top: 50%;margin-top: -3vw;width: 6vw;height: 6vw;">
						<img v-show="item.checked!=1" src="<?php echo $base_url;?>files/images/team/ug_game_unchecked.png" style="position: absolute;left: 6vw;top: 50%;margin-top: -3vw;width: 6vw;height: 6vw;">
					</div>
					<img v-bind:src="item.game_icon" style="position: absolute;top: 50%;margin-top: -9vw;left: 18vw;width: 18vw;height: 18vw;">
                    
                    <div style="position: absolute;top: 6vw;left: 38vw;width: 20vw;height: 5vw;line-height: 5vw;font-size: 4vw;color: gray;">{{item.firstSetup}}%</div>
					<div style="position: absolute;top: 6vw;left: 58vw;width: 20vw;height: 5vw;line-height: 5vw;font-size: 4vw;color: gray;">{{item.secondSetup}}%</div>
					<div style="position: absolute;top: 6vw;left: 78vw;width: 20vw;height: 5vw;line-height: 5vw;font-size: 4vw;color: gray;">{{item.thirdSetup}}%</div>

					<div style="position: absolute;top: 14vw;left: 38vw;width: 20vw;height: 5vw;line-height: 5vw;font-size: 4vw;color: gray;">大赢家</div>
					<div style="position: absolute;top: 14vw;left: 58vw;width: 20vw;height: 5vw;line-height: 5vw;font-size: 4vw;color: gray;">二赢家</div>
					<div style="position: absolute;top: 14vw;left: 78vw;width: 20vw;height: 5vw;line-height: 5vw;font-size: 4vw;color: gray;">三赢家</div>

					<div style="position: absolute;top: 23.9vw;left: 4vw;width: 92vw;height: 0.5px;background-color: rgb(212,212,213);"></div>
					<div style="position: absolute;top: 0;left: 16vw;height: 100%;width: 96vw;" v-on:click="clickScoreItem(item)"></div>
				</div>
				<div id="moretext" style="position: relative;margin-top: 4px;color: #000;height: 13vw;text-align: center;line-height: 13vw;font-size: 2.2vh;display: none;" v-on:click="clickMore">
				<div class="backcolor"></div>
					点击加载更多
				</div>
			</div>
		</div>

	</div>
	
</body>

<script type="text/javascript" src="<?php echo $base_url;?>files/js/jquery-1.9.1.min.js"></script>
<script type="text/javascript" src="<?php echo $base_url;?>files/js/bscroll.min.js" ></script>
<script type="text/javascript" src="<?php echo $base_url;?>files/js/picker.min.js" ></script>
<script type="text/javascript" src="<?php echo $base_url;?>files/js/vue.min.js" ></script>
<script type="text/javascript" src="<?php echo $base_url;?>files/js/vue-resource.min.js" ></script>
<script type="text/javascript" src="<?php echo $base_url;?>files/js/team/gameList/gameList-1.0.2.js"></script>

<script>
    var httpModule = {
        loadMoreScoreList: function () {
            // var data = {
            //     "account_id": userData.accountId,
            //     "page": appData.page,
            //     "dealer_num": globalData.dealerNum,
            //     "game_type": appData.selectedGame.type,
            // };

            Vue.http.get(globalData.apiUrl + '/allGames?page=' + appData.page + '&orgId=' + globalData.orgId + '&aid=' + userData.aid + '&s=' + userData.s).then(function(response) {
                var bodyData = response.body;

                appData.isHttpRequest = false;

                console.log(bodyData);

                if (bodyData.result == 0) {
                    appData.page = bodyData.data.page;
                    appData.sumPage = bodyData.data.totalPage;

                    for (var i = 0; i < bodyData.data.games.length;i++) {
                        var item = bodyData.data.games[i];
                        resetGameValue(item);
                        appData.gameScoreList.push(item);
                    }

                } else {
                    console.log(bodyData.msg);
                }

                appData.canLoadMore = true;
                if (appData.page < appData.sumPage) {
                    $('#moretext').text('点击加载更多');
                    $('#moretext').show();
                } else {
                    appData.canLoadMore = false;
                    $('#moretext').text('没有更多内容');
                    $('#moretext').hide();
                }

            }, function(response) {
                appData.canLoadMore = true;
                appData.isHttpRequest = false;
            });
        },
        deleteGame: function (game) {

            var data = {
                "orgId": globalData.orgId,
                "gameType": game.game_type.toString(),
                "aid":userData.aid,
                "s":userData.s
            };

            Vue.http.post(globalData.apiUrl + '/org/game/delete', data).then(function(response) {
                var bodyData = response.body;

                console.log(bodyData);

            }, function(response) {
                appData.isHttpRequest = false;
                console.log(response);
            });
        },
        addGame: function (game) {

            var rule = {"firstWinner":{"init":game.firstInit.toString(),"max":game.firstMax.toString(),"setup":game.firstSetup.toString()}, "secondWinner":{"init":game.secondInit.toString(),"max":game.secondMax.toString(),"setup":game.secondSetup.toString()}, "thirdWinner":{"init":game.thirdInit.toString(),"max":game.thirdMax.toString(),"setup":game.thirdSetup.toString()}};
            //var ruleStr = JSON.stringify(rule);
            //console.log(ruleStr);

            var data = {
                "orgId": globalData.orgId,
                "gameType": game.game_type.toString(),
                "consumeRule": rule,
                "aid":userData.aid,"s":userData.s
            };

            Vue.http.post(globalData.apiUrl + '/org/game/add', data).then(function(response) {
                var bodyData = response.body;
                appData.isHttpRequest = false;

                console.log(bodyData);

            }, function(response) {
                appData.isHttpRequest = false;
                console.log(response);
            });
        },
        updateGame: function (game) {
            var rule = {
                "firstWinner":{
                    "init":game.firstInit.toString(),
                    "max":game.firstMax.toString(),
                    "setup":game.firstSetup.toString()
                },
                "secondWinner":{
                    "init":game.secondInit.toString(),
                    "max":game.secondMax.toString(),
                    "setup":game.secondSetup.toString()
                },
                "thirdWinner":{
                    "init":game.thirdInit.toString(),
                    "max":game.thirdMax.toString(),
                    "setup":game.thirdSetup.toString()
                }
            };
            //var ruleStr = JSON.stringify(rule);
            //console.log(ruleStr);

            var data = {
                "orgId": globalData.orgId,
                "gameType": game.game_type.toString(),
                "consumeRule": rule,
                "aid":userData.aid,
                "s":userData.s
            };

            Vue.http.post(globalData.apiUrl + '/org/game/updateRule', data).then(function(response) {
                var bodyData = response.body;
                appData.isHttpRequest = false;

                console.log(bodyData);

            }, function(response) {
                appData.isHttpRequest = false;
                console.log(response);
            });
        }
    };

    var viewMethods = {
        clickShowAlert: function (type, text) {
            appData.alertType = type;
            appData.alertText = text;
            appData.isShowAlert = true;
            setTimeout(function() {
                var alertHeight = $(".alertText").height();
                var textHeight = alertHeight;
                if (alertHeight < height * 0.15) {
                    alertHeight = height * 0.15;
                }

                if (alertHeight > height * 0.8) {
                    alertHeight = height * 0.8;
                }

                var mainHeight = alertHeight + height * (0.022 + 0.034) * 2 + height * 0.022 + height * 0.056;
                if (type == 8) {
                    mainHeight = mainHeight - height * 0.022 - height * 0.056
                }

                var blackHeight = alertHeight + height * 0.034 * 2;
                var alertTop = height * 0.022 + (blackHeight - textHeight) / 2;

                $(".alert .mainPart").css('height', mainHeight + 'px');
                $(".alert .mainPart").css('margin-top', '-' + mainHeight / 2 + 'px');
                $(".alert .mainPart .backImg .blackImg").css('height', blackHeight + 'px');
                $(".alert .mainPart .alertText").css('top', alertTop + 'px');
            }, 0);
        },
        clickCloseAlert: function () {
            appData.isShowAlert = false;
            if (appData.alertType == 1) {
                if (!appData.is_connect) {
                    reconnectSocket();
                    appData.is_connect = true;
                }
            }
        },
        showMessage: function () {
            $(".message .textPart").animate({
                height:"400px"
            });
            appData.isShowMessage = true;
        },
        hideMessage: function () {
            $(".message .textPart").animate({
                height:0
            }, function() {
                appData.isShowMessage = false;
            });
        },
    };

    var width = window.innerWidth;
    var height = window.innerHeight;
    var isTimeLimitShow = false;
    var viewOffset = 4;
    var itemOffset = 4;
    var itemHeight = 66 / 320 * width;
    var leftOffset = 8 / 320 * width;
    var userViewHeight = 0.25 * width;
    var avatarWidth = 0.21875 * width;
    var avatarY = (userViewHeight - avatarWidth) / 2;
    var itemY = (80 + 44 * 2 + 40) / 320 * width + viewOffset * 3 + itemOffset;
    var dtStartDate = '';
    var dtEndDate = '';
    var dtStartTimestamp = '0';
    var dtEndTimestamp = '0';
    var todayTimestamp = '0';
    var groupOffset = 20;


    var selectedGames = [
        {"game_type":1,"first":"5","second":"3","third":"10"},
        {"game_type":5,"first":"5","second":"3","third":"10"},
        {"game_type":9,"first":"10","second":"4","third":"10"},
    ];

    selectedGames = [];

    if (globalData.orgGames.length >= 1) {
        for(var i = 0; i < globalData.orgGames.length; i++) {
            var game = globalData.orgGames[i];
            selectedGames.push({"game_type":game.game_type,"first":game.consume_rule.firstWinner.setup,"second":game.consume_rule.secondWinner.setup,"third":game.consume_rule.thirdWinner.setup});
        }
    }


    var appData = {
        'width': window.innerWidth,
        'height': window.innerHeight,
        'roomCard': Math.ceil(globalData.card),
        'user': userData,
        'activity': [],
        'isShowInvite': false,
        'isShowAlert': false,
        'isShowMessage': false,
        'alertType': 0,
        'alertText': '',
        'isDealing': false,
        'gameItems':[],
        itemY:itemY,
        itemHeight: 66 / 320 * width,
        itemOffset: itemOffset,
        startDate: '',
        endDate: '',
        'isShowGroupMenu':globalData.isShowGroupMenu,
        'gameScoreList':[],
        bScroll:null,
        page:1,
        sumPage:1,
        canLoadMore:true,
        selectedGame:null,
        isHttpRequest:false,
        cardText:globalData.cardText
    };

    function loadMoreScoreList() {
        if (appData.page < appData.sumPage) {
            appData.page = parseInt(appData.page) + 1;
            console.log(appData.page);
            httpModule.loadMoreScoreList();
            $('#moretext').show();
            $('#moretext').text('加载中...');
        } else {
            $('#moretext').hide();
            $('#moretext').text('上拉加载更多');
        }
    }

    function resetGameValue(item) {
        item.checked = 0;
        item.consume_rule = eval('(' + item.consume_rule + ')');
        item.firstInit = item.consume_rule.firstWinner.init;
        item.secondInit = item.consume_rule.secondWinner.init;
        item.thirdInit = item.consume_rule.thirdWinner.init;
        item.firstSetup = item.consume_rule.firstWinner.init;
        item.secondSetup = item.consume_rule.secondWinner.init;
        item.thirdSetup = item.consume_rule.thirdWinner.init;
        item.firstMax = item.consume_rule.firstWinner.max;
        item.secondMax = item.consume_rule.secondWinner.max;
        item.thirdMax = item.consume_rule.thirdWinner.max;

        for(var i = 0; i < selectedGames.length;i++) {
            var game = selectedGames[i];
            if (item.game_type == game.game_type) {
                item.checked = 1;
                item.firstSetup = game.first;
                item.secondSetup = game.second;
                item.thirdSetup = game.third;
                break;
            }
        }
    }


    //Vue方法
    var methods = {
        showInvite: viewMethods.clickShowInvite,
        showAlert: viewMethods.clickShowAlert,
        showMessage: viewMethods.showMessage,
        closeInvite: viewMethods.clickCloseInvite,
        closeAlert: viewMethods.clickCloseAlert,
        getCards: viewMethods.clickGetCards,
        hideMessage: viewMethods.hideMessage,
        showRedpackageRecord:viewMethods.clickRedpackageRecord,
        showSendRedpackage:viewMethods.clickSendRedPackage,
        startDateChange: viewMethods.changeStartDate,
        endDateChange: viewMethods.changeEndDate,
        finishBindPhone:function () {
            window.location.href=window.location.href;
        },
        clickMore:function () {
            if (appData.canLoadMore) {
                $('#moretext').text('加载中...');
                $('#moretext').show();
                appData.canLoadMore = false;
                setTimeout(function() {
                    appData.canLoadMore = true;
                    $('#moretext').text('点击加载更多');
                }, 5000);

                loadMoreScoreList();
            }
        },
        clickScoreItem: function (item) {
            appData.selectedGame = item;

            var data1 = [], data2 = [], data3 = [];
            var index1, index2, index3;

            index1 = item.firstSetup;
            index2 = item.secondSetup;
            index3 = item.thirdSetup;

            for (var i = 0; i <= item.firstMax; i++) {
                data1.push({text:i+'%',value:i});
            }

            for (var i = 0; i <= item.secondMax; i++) {
                data2.push({text:i+'%',value:i});
            }

            for (var i = 0; i <= item.thirdMax; i++) {
                data3.push({text:i+'%',value:i});
            }

            $(".picker").remove();
            picker = null;

            var picker = new Picker({
                data: [data1, data2, data3],
                selectedIndex: [index1, index2, index3],
                title: ''
            });

            picker.on('picker.select', function (selectedVal, selectedIndex) {
                //console.log(selectedVal);

                appData.selectedGame.firstSetup = selectedVal[0];
                appData.selectedGame.secondSetup = selectedVal[1];
                appData.selectedGame.thirdSetup = selectedVal[2];

                if (appData.selectedGame.checked == 1) {
                    httpModule.updateGame(appData.selectedGame);
                } else {
                    appData.selectedGame.checked = 1;
                    httpModule.addGame(appData.selectedGame);
                }

                //console.log(selectedIndex);
            })

            picker.on('picker.change', function (index, selectedIndex) {
                //console.log(index);
                //console.log(selectedIndex);
            });

            picker.on('picker.valuechange', function (selectedVal, selectedIndex) {
                //console.log(selectedVal);
                //console.log(selectedIndex);
            });

            $('.picker .picker-panel .picker-choose .cancel').text('取消');
            $('.picker .picker-panel .picker-choose .confirm').text('确定');

            $('.picker .picker-panel .picker-choose').after('<div style="position: relative;width:100vw;height: 36px;line-height:36px;font-size:4vw;background-color: rgb(255,255,255);color:gray;"><span style="position: absolute;left:0;width:33vw;">大赢家</span><span style="position: absolute;left:33vw;width:33vw;">二赢家</span><span style="position: absolute;left:66vw;width:33vw;">三赢家</span></div>');

            picker.show();

            // console.log($('.picker .picker-panel .picker-choose'));
        },
        selecteGame: function (item) {
            appData.selectedGame = item;
            if (item.checked == 1) {
                item.checked = 0;
                httpModule.deleteGame(item);
            } else {
                item.checked = 1;
                httpModule.addGame(item);
            }
        }
    };

    //Vue生命周期
    var vueLife = {
        vmCreated: function () {
            console.log('vmCreated')
            $("#loading").hide();
            $(".main").show();
        },
        vmUpdated: function () {
            console.log('vmUpdated');
        },
        vmMounted: function () {
            console.log('vmMounted');
        },
        vmDestroyed: function () {
            console.log('vmDestroyed');
        }
    };

    httpModule.loadMoreScoreList();

    //Vue实例
    var vm = new Vue({
        el: '#app-main',
        data: appData,
        methods: methods,
        created: vueLife.vmCreated,
        updated: vueLife.vmUpdated,
        mounted: vueLife.vmMounted,
        destroyed: vueLife.vmDestroyed,
    });
</script>

</html>
