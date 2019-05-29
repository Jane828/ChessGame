var ws;
var game = {
    "room": 0,
    "room_number": globalData.roomNumber,
    "room_url": 0,
    "score": 0,
    "status": 0,
    "time": -1,
    "round": 0,
    "total_num": 12,
    "currentScore": 0,
    "can_open": 0,
    "is_play": false,
    "autoReady": false,
    "autoBet": false,
    "autoBetTimeOut": '',
    'game_type': globalData.game_type,
    'timeLimit': ''
};
var message = [
    { "num": 0, "text": "玩游戏，请先进群" },
    { "num": 1, "text": "群内游戏，切勿转发" },
    { "num": 2, "text": "我出去叫人" },
    { "num": 3, "text": "别吹牛逼，有本事干到底" },
    { "num": 4, "text": "我当年横扫澳门五条街"},
    { "num": 5, "text": "算你牛逼" },
    { "num": 6, "text": "别跟我抢庄" },
    { "num": 7, "text": "输得裤衩都没了" },
    { "num": 8, "text": "我给你们送温暖了" },
    { "num": 9, "text": "谢谢老板" }
];
var wsOperation = {
    JoinRoom: "JoinRoom",
    Audience: "Audience",
    UpdateAudienceInfo: 'UpdateAudienceInfo',
    ReadyStart: "ReadyStart",
    PutPrize: "PutPrize",
    ChooseChip: "ChooseChip",
    ShowChips: "ShowChips",
    StopBet: "StopBet",
    ShowPrize: "ShowPrize",
    PrepareJoinRoom: "PrepareJoinRoom",
    AllGamerInfo: "AllGamerInfo",
    UpdateGamerInfo: "UpdateGamerInfo",
    UpdateAccountStatus: "UpdateAccountStatus",
    StartLimitTime: "StartLimitTime",
    CancelStartLimitTime: "CancelStartLimitTime",
    GameStart: "GameStart",
    NotyChooseChip: "NotyChooseChip",
    CardInfo: "CardInfo",
    PkCard: "PkCard",
    UpdateAccountScore: "UpdateAccountScore",
    OpenCard: "OpenCard",
    StopShow: "StopShow",
    Win: "Win",
    Discard: "Discard",
    BroadcastVoice: "BroadcastVoice",
    ClickToLook: "ClickToLook",
    GrabBanker: "GrabBanker",
    PlayerMultiples: "PlayerMultiples",
    ShowCard: "ShowCard",
    UpdateAccountShow: "UpdateAccountShow",
    UpdateAccountMultiples: "UpdateAccountMultiples",
    StartPut: "StartPut",
    StartBet: "StartBet",
    StartShow: "StartShow",
    RefreshRoom: "PullRoomInfo",
    AllGameEndData: "AllGameEndData",
    GameEndData: "GameEndData"
};

var httpModule = {
    getActivityInfo: function() {
        Vue.http.post(globalData.baseUrl + 'f/getActivityInfo', { "account_id": userData.accountId, "dealer_num": globalData.dealerNum,'room_number' : globalData.roomNumber ,'game_type' : globalData.game_type }).then(function(response) {
            logMessage(response.body);
            var bodyData = response.body;

            if (bodyData.result == 0) {
                if (bodyData.data.length == 0) {
                    if (appData.roomCard <= 0) {
                        reconnectSocket();
                        appData.is_connect = true;
                    } else {
                        reconnectSocket();
                        appData.is_connect = true;
                    }
                } else {
                    viewMethods.clickShowAlert(5, appData.activity[0].content);
                }
            } else {
                viewMethods.clickShowAlert(88, bodyData.result_message);
            }

        }, function(response) {
            logMessage(response.body);
        });
    },
    getScoreInfo: function () {
        Vue.http.post(globalData.baseUrl + 'f/scoreStat', {'type':globalData.game_type}).then(function (response) {
            var bodyData = response.body;
            if (0 == bodyData.result) {
                scoreInfo.one = bodyData.data.one;
                scoreInfo.three = bodyData.data.three;
                scoreInfo.week = bodyData.data.week;
                scoreInfo.month = bodyData.data.month;
            }
        })
    },
    getAuthcode: function(phone) {
        var data = { "dealer_num": globalData.dealerNum, "phone": phone };
        Vue.http.post(globalData.baseUrl + 'account/getMobileSms', data).then(function(response) {
            logMessage(response.body);
            var bodyData = response.body;

            if (bodyData.result == 0) {
                appData.authcodeTime = 60;
                authcodeTimer();
                appData.authcodeType = 2;
            } else {
                viewMethods.clickShowAlert(21, bodyData.result_message);
            }
        }, function(response) {
            viewMethods.clickShowAlert(21, '获取验证码失败');
        });
    },
    bindPhone: function(phone, authcode) {
        var data = { "dealer_num": globalData.dealerNum, "phone": phone, "code": authcode };
        Vue.http.post(globalData.baseUrl + 'account/checkSmsCode', data).then(function(response) {
            var bodyData = response.body;
            if (bodyData.result == 0) {
                appData.isAuthPhone = 0;
                appData.phone = appData.sPhone;
                if (bodyData.data.card_count != null && bodyData.data.card_count != undefined && bodyData.data.card_count != '') {
                    appData.roomCard = parseInt(appData.roomCard) + parseInt(bodyData.data.card_count);
                }
                if (bodyData.data.account_id != userData.accountId) {
                    viewMethods.clickShowAlert(23, bodyData.result_message);
                } else {
                    viewMethods.clickShowAlert(22, bodyData.result_message);
                }
                appData.sPhone = '';
                appData.sAuthcode = '';
            } else {
                viewMethods.clickShowAlert(21, bodyData.result_message);
            }
        }, function(response) {
            appData.authcodeTime = 0;
            viewMethods.clickShowAlert(21, "绑定失败");
        });
    },
};

var socketModule = {
    closeSocket: function() {
        if (ws) {
            try {
                ws.close();
            } catch (error) {
                console.log(error);
            }
        }
    },
    sendData: function(obj) {
        try {
            console.log('websocket state：' + ws.readyState);
            if (ws.readyState == WebSocket.CLOSED) {
                //socket关闭，重新连接
                reconnectSocket();
                return;
            }
            if (ws.readyState == WebSocket.OPEN) {
                ws.send(JSON.stringify(obj));
            } else if (ws.readyState == WebSocket.CONNECTING) {
                //如果还在连接中，1秒后重新发送请求
                setTimeout(function() {
                    socketModule.sendData(obj);
                }, 1000);
            } else {
                console.log('websocket state：' + ws.readyState);
                errorSocket(obj.operation, JSON.stringify(obj));
            }
        } catch (err) {
            console.log(err);
        }
    },
    sendPrepareJoinRoom: function() {
        socketModule.sendData({
            operation: wsOperation.PrepareJoinRoom,
            account_id: userData.accountId,
            session: globalData.session,
            data: {
                room_number: globalData.roomNumber
            }
        });
    },
    sendJoinRoom: function() {
        socketModule.sendData({
            operation: wsOperation.JoinRoom,
            account_id: userData.accountId,
            session: globalData.session,
            data: {
                room_number: globalData.roomNumber
            }
        });
    },
    sendAudience: function() {
        socketModule.sendData({
            operation:wsOperation.Audience,
            account_id: userData.accountId,
            session: globalData.session,
            data: {
                room_number: globalData.roomNumber
            }
        });
    },
    sendRefreshRoom: function() {
        socketModule.sendData({
            operation: wsOperation.RefreshRoom,
            account_id: userData.accountId,
            session: globalData.session,
            data: {
                room_id: appData.game.room
            }
        });
    },
    sendReadyStart: function() {
        socketModule.sendData({
            operation: wsOperation.ReadyStart,
            account_id: userData.accountId,
            session: globalData.session,
            data: {
                room_id: appData.game.room
            }
        });
    },
    sendPutPrize: function() {
        socketModule.sendData({
            operation: wsOperation.PutPrize,
            account_id: userData.accountId,
            session: globalData.session,
            data: {
                room_id: appData.game.room,
                area: appData.prizeArea
            }
        });
    },
    sendChooseChip: function(x,y,score) {
        socketModule.sendData({
            operation: wsOperation.ChooseChip,
            account_id: userData.accountId,
            session: globalData.session,
            data: {
                room_id: appData.game.room,
                score: score, 
                x:x,
                y:y
            }
        });
    },
    sendShowChips: function() {
        socketModule.sendData({
            operation: wsOperation.ShowChips,
            account_id: userData.accountId,
            session: globalData.session,
            data: {
                room_id: appData.game.room,
            }
        });
    },
    sendStopBet: function(){
        socketModule.sendData({
            operation: wsOperation.StopBet,
            account_id: userData.accountId,
            session: globalData.session,
            data: {
                room_id: appData.game.room,
            }
        });
    },
    sendShowPrize: function() {
        socketModule.sendData({
            operation: wsOperation.ShowPrize,
            account_id: userData.accountId,
            session: globalData.session,
            data: {
                room_id: appData.game.room,
            }
        });
    },
    sendBroadcastVoice: function(num) {
        socketModule.sendData({
            operation: wsOperation.BroadcastVoice,
            account_id: userData.accountId,
            session: globalData.session,
            data: {
                room_id: appData.game.room,
                voice_num: num
            }
        });
    },
    sendGrabBanker: function() {
        socketModule.sendData({
            operation: wsOperation.GrabBanker,
            account_id: userData.accountId,
            session: globalData.session,
            data: {
                room_id: appData.game.room,
                is_grab: "1",
            }
        });
    },
    sendNotGrabBanker: function() {
        socketModule.sendData({
            operation: wsOperation.GrabBanker,
            account_id: userData.accountId,
            session: globalData.session,
            data: {
                room_id: appData.game.room,
                is_grab: "0",
                multiples: "1",
            }
        });
    },
    sendPlayerMultiples: function(times) {
        socketModule.sendData({
            operation: wsOperation.PlayerMultiples,
            account_id: userData.accountId,
            session: globalData.session,
            data: {
                room_id: appData.game.room,
                multiples: times
            }
        });
    },
    sendShowCard: function() {
        socketModule.sendData({
            operation: wsOperation.ShowCard,
            account_id: userData.accountId,
            session: globalData.session,
            data: {
                room_id: appData.game.room
            }
        });
    },
    sendClickToLook: function() {
        socketModule.sendData({
            operation: wsOperation.ClickToLook,
            account_id: userData.accountId,
            session: globalData.session,
            data: {
                room_id: appData.game.room
            }
        });
    },
    sendDiscard: function() {
        socketModule.sendData({
            operation: wsOperation.Discard,
            account_id: userData.accountId,
            session: globalData.session,
            data: {
                room_id: appData.game.room,
            }
        });
    },
    sendOpenCard: function() {
        socketModule.sendData({
            operation: wsOperation.OpenCard,
            account_id: userData.accountId,
            session: globalData.session,
            data: {
                room_id: appData.game.room,
            }
        });
    },
    sendPkCard: function(num) {
        socketModule.sendData({
            operation: wsOperation.PkCard,
            account_id: userData.accountId,
            session: globalData.session,
            data: {
                room_id: appData.game.room,
                'other_account_id': num,
            }
        });
    },
    processGameRule: function(obj) {
        console.log('processGameRule-------------')
        console.log(obj)
        if (obj.data.chip_type) {
            appData.ruleInfo.chip_type = obj.data.chip_type;
            appData.ruleInfo.ticket_count = obj.data.ticket_count;
            appData.ruleInfo.upper_limit = obj.data.upper_limit;
            appData.ruleInfo.mode = obj.data.mode;
            appData.ruleInfo.first_lossrate = obj.data.first_lossrate;
            appData.ruleInfo.second_lossrate = obj.data.second_lossrate;
            appData.ruleInfo.three_lossrate = obj.data.three_lossrate;
            appData.ruleInfo.banker_mode = Math.ceil(obj.data.banker_mode);
        }
        if (appData.ruleInfo.banker_mode == 1) {
            appData.ruleInfo.bankerText = '抢庄';
        } else if (appData.ruleInfo.banker_mode == 2) {
            appData.ruleInfo.bankerText = '抢庄';
        } else if (appData.ruleInfo.banker_mode == 3) {
            appData.ruleInfo.bankerText = '';
        } else {
            appData.ruleInfo.bankerText = '';
        }
    },
    processPrepareJoinRoom: function(obj) {
        console.log('processPrepareJoinRoom-------------')
        console.log(obj)
        if (obj.data.room_status == 4) {
            appData.roomStatus = obj.data.room_status;
            viewMethods.clickShowAlert(88, obj.result_message);
            return;
        }
        appData.joinChoose.isShow = true;
        if (obj.data.chip_type) {
            appData.ruleInfo.chip_type = obj.data.chip_type;
            appData.ruleInfo.ticket_count = obj.data.ticket_count;
            appData.ruleInfo.upper_limit = obj.data.upper_limit;
            appData.ruleInfo.mode = obj.data.mode;
            appData.ruleInfo.first_lossrate = obj.data.first_lossrate;
            appData.ruleInfo.second_lossrate = obj.data.second_lossrate;
            appData.ruleInfo.three_lossrate = obj.data.three_lossrate;
            appData.ruleInfo.banker_mode = Math.ceil(obj.data.banker_mode);
        
            if (appData.ruleInfo.banker_mode == 1) {
                appData.ruleInfo.bankerText = '抢庄';
            } else if (appData.ruleInfo.banker_mode == 2) {
                appData.ruleInfo.bankerText = '抢庄';
            } else if (appData.ruleInfo.banker_mode == 3) {
                appData.ruleInfo.bankerText = '';
            } else {
                appData.ruleInfo.bankerText = '';
            }
        }

        wxModule.config();
        if (obj.data.room_status == 3) {
            if (appData.isAutoActive == true) {
                socketModule.sendActiveRoom();
            } else {
                $('.createRoom .mainPart').css('height', '65vh');
                $('.createRoom .mainPart .blueBack').css('height', '46vh');
            }
            return;
        }
        if (obj.data.alert_text != "" && obj.data.user_count != 0) {
            appData.alertText = obj.data.alert_text;
            appData.joinChoose.isShow = true;
        }
    },
    processJoinRoom: function(obj) {
        if (obj.data.room_status == 4) {
            appData.roomStatus = obj.data.room_status;
            viewMethods.clickShowAlert(88, obj.result_message);
            return;
        }
        appData.isAudience = false;

        appData.game.room = obj.data.room_id;
        appData.game.room_url = obj.data.room_url;
        appData.game.currentScore = Math.ceil(obj.data.benchmark);
        appData.game.score = Math.ceil(obj.data.pool_score);
        appData.game.round = Math.ceil(obj.data.game_num);
        appData.game.total_num = Math.ceil(obj.data.total_num);
        resetAllPlayerData();
        if (obj.data.limit_time == -1) {
            appData.game.time = Math.ceil(obj.data.limit_time);
            viewMethods.timeCountDown();
        }

        appData.player[0].serial_num = Math.ceil(obj.data.serial_num);

        for (var i = 0; i < 10; i++) {
            if (i <= 10 - Math.ceil(obj.data.serial_num)) {
                appData.player[i].serial_num = i + Math.ceil(obj.data.serial_num);
            } else {
                appData.player[i].serial_num = i + Math.ceil(obj.data.serial_num) - 10;
            }
        }

        appData.player[0].account_status = Math.ceil(obj.data.account_status);
        appData.player[0].account_score = Math.ceil(obj.data.account_score);
        appData.player[0].nickname = userData.nickname;
        appData.player[0].headimgurl = userData.headimgurl;
        appData.player[0].account_id = userData.accountId;
        appData.player[0].ticket_checked = obj.data.ticket_checked;
        appData.game.status = Math.ceil(obj.data.room_status);
        appData.scoreboard = obj.data.scoreboard;
    },
    processAudience: function (obj) {
        if (obj.data.room_status == 4) {
            if ( obj.data.to_joinRoom == 1) {
                viewMethods.clickShowAlert(8, obj.result_message);
                socketModule.sendJoinRoom();
            }else {
                viewMethods.clickShowAlert(88, obj.result_message);
            }
            return;
        }
        appData.isAudience = true;
        appData.game.room = obj.data.room_id;
        appData.game.score = Math.ceil(obj.data.pool_score);
        appData.game.round = Math.ceil(obj.data.game_num);
        appData.game.total_num = Math.ceil(obj.data.total_num);
        resetAllPlayerData();
        appData.game.status = Math.ceil(obj.data.room_status);

        var obSeatNum = Math.ceil(obj.data.seat_num); // 观战的游戏玩家位置
        for (var i = 0; i < 10; i++) {
            if (i <= 10 - obSeatNum) {
                appData.player[i].serial_num = i + obSeatNum;
            } else {
                appData.player[i].serial_num = i + obSeatNum - 10;
            }
        }

        appData.scoreboard = obj.data.scoreboard;
    },
    processUpdateAudienceInfo: function (obj) {
        obj.data = obj.data || obj.audience;
        if (obj.audience.status == 1) {
            for (var i = 0, len = appData.audiences.length; i < len; i += 1) {
                if (appData.audiences[i] && (obj.audience.account_id == appData.audiences[i].account_id)){
                    appData.audiences.splice(i, 1);  
                }
            }
            appData.audiences.push(obj.audience);
            if (obj.data && obj.data.account_id) {
                for (var j = 0, len = appData.player.length; j < len; j += 1) { // 玩过游戏的玩家，再次加入时选择观战，其它桌面玩家显示该玩家的状态为在线
                    if (appData.player[j].account_id == obj.data.account_id){
                        if(obj.data.is_banker == 0) obj.data.is_banker = false;
                        else obj.data.is_banker = true;
                        objUpdate(appData.player[j], obj.data)
                    }
                }
            }
        } else {
            for (var i = 0, len = appData.audiences.length; i < len; i += 1) {
                if (obj.audience.account_id == appData.audiences[i].account_id){
                    appData.audiences.splice(i, 1);
                    for (var j = 0, len = appData.player.length; j < len; j += 1) { // 玩过游戏的玩家，再次加入时选择观战，其它桌面玩家显示该玩家的状态为在线
                        if (appData.player[j].account_id == obj.data.account_id){
                            if(obj.data.is_banker == 0) obj.data.is_banker = false;
                            else obj.data.is_banker = true;
                            objUpdate(appData.player[j], obj.data)
                        }
                    }
                    break;
                }
            }
        }
    },
    processRefreshRoom: function(obj) {
        resetAllPlayerData();
        appData.game.currentScore = Math.ceil(obj.data.benchmark);
        appData.game.score = Math.ceil(obj.data.pool_score);

        for (var i = 0; i < appData.player.length; i++) {
            if (i <= appData.player.length - Math.ceil(obj.data.serial_num)) {
                appData.player[i].serial_num = i + Math.ceil(obj.data.serial_num);
            } else {
                appData.player[i].serial_num = i + Math.ceil(obj.data.serial_num) - appData.player.length;
            }
        }

        for (var i = 0; i < appData.player.length; i++) {
            for (var j = 0; j < obj.all_gamer_info.length; j++) {
                if (appData.player[i].serial_num == obj.all_gamer_info[j].serial_num) {
                    appData.player[i].nickname = obj.all_gamer_info[j].nickname;
                    appData.player[i].headimgurl = obj.all_gamer_info[j].headimgurl;
                    appData.player[i].account_id = obj.all_gamer_info[j].account_id;
                    appData.player[i].account_score = Math.ceil(obj.all_gamer_info[j].account_score);
                    appData.player[i].account_status = Math.ceil(obj.all_gamer_info[j].account_status);
                    appData.player[i].online_status = Math.ceil(obj.all_gamer_info[j].online_status);
                    appData.player[i].ticket_checked = obj.all_gamer_info[j].ticket_checked;
                    if (obj.all_gamer_info[j].is_banker == 1) {
                        appData.player[i].is_banker = true;
                        appData.bankerAccountId = obj.all_gamer_info[j].account_id;
                        appData.bankerPlayer = appData.player[i];
                    } else {
                        appData.player[i].is_banker = false;
                    }
                }
            }
        }
        if (appData.player[0].account_status == 3) {

            if (appData.ruleInfo.banker_mode == 2 && appData.game.round == 1) {

            } else {
                appData.showClockRobText = true;
            }
            setTimeout(function() {
                appData.showRob = true;
            }, 500);
        }
        if (appData.player[0].account_status == 6) {
            appData.showClockPutText = true;
            viewMethods.goPutPrize(Math.floor(Math.random()*4));
            viewMethods.brightInciteIn();
            if (appData.player[0].is_banker == true) {
                appData.showRob = false;
                appData.showRobText = false;
                appData.showNotRobBankerText = false;
                appData.showShowCardButton = false;
                appData.showClickShowCard = false;
                appData.showBankerCoinText = true;
                appData.showTimesCoin = false;
                appData.showClockBetText = false;
            } else {
                appData.showRob = false;
                appData.showRobText = false;
                appData.showNotRobBankerText = false;
                appData.showShowCardButton = false;
                appData.showClickShowCard = false;
                appData.showBankerCoinText = false;
                appData.showTimesCoin = true;
                appData.showClockBetText = false;
            }
        }
        if (appData.player[0].account_status == 7) {
            appData.showClockBetText = true;
            if (appData.player[0].is_banker == true) {
                appData.showRob = false;
                appData.showRobText = false;
                appData.showNotRobBankerText = false;
                appData.showShowCardButton = false;
                appData.showClickShowCard = false;
                appData.showBankerCoinText = true;
                appData.showTimesCoin = false;
                appData.showClockPutText = false;
            } else {
                appData.showRob = false;
                appData.showRobText = false;
                appData.showNotRobBankerText = false;
                appData.showShowCardButton = false;
                appData.showClickShowCard = false;
                appData.showBankerCoinText = false;
                appData.showTimesCoin = true;
                appData.showClockPutText = false;
            }
        }

        if (appData.player[0].account_status == 6) {
            appData.isFinishBankerAnimate = true;
        }

        viewMethods.resetMyAccountStatus();
        viewMethods.updateAllPlayerStatus();

    },
    processAllGamerInfo: function(obj) {
        appData.audiences = obj.audience;
        for (var i = 0; i < 10; i++) {
            var obj_player = {
                "num": i + 1,
                "serial_num": appData.player[i].serial_num,
                "account_id": 0,
                "account_status": 0,
                "playing_status": 0,
                "online_status": 0,
                "nickname": "",
                "headimgurl": "",
                "account_score": 0,
                "ticket_checked": 0,
                "is_win": false,
                "limit_time": 0,
                "chips": 0,
                "haveBet": 0,
                "current_win": 0,
                "is_operation": false,
                "is_banker": false,
                "messageOn": false,
                "messageText": "我们来血拼吧",
                "coins": []
            };
            var isInGame = false;
            for (var j = 0; j < obj.data.length; j++) {
                if (appData.player[i].serial_num == obj.data[j].serial_num) {
                    appData.player[i].nickname = obj.data[j].nickname;
                    appData.player[i].headimgurl = obj.data[j].headimgurl;
                    appData.player[i].account_id = obj.data[j].account_id;
                    appData.player[i].account_score = Math.ceil(obj.data[j].account_score);
                    appData.player[i].account_status = Math.ceil(obj.data[j].account_status);
                    appData.player[i].online_status = Math.ceil(obj.data[j].online_status);
                    appData.player[i].ticket_checked = obj.data[j].ticket_checked;
                    isInGame = true;
                    if (obj.data[j].is_banker == 1) {
                        appData.player[i].is_banker = true;
                        appData.bankerAccountId = obj.data[j].account_id;
                        appData.bankerPlayer = appData.player[i];
                        if(obj.data[j].account_status == 6){
                            viewMethods.brightInciteIn();
                            appData.showClockPutText = true;
                        }
                    } else {
                        appData.player[i].is_banker = false;
                        if(obj.data[j].account_status == 11)appData.showClockShowLottery = true;
                    }
                    if(obj.data[j].leftTime && Math.ceil(obj.data[j].leftTime)){
                        appData.player[i].limit_time = Math.ceil(obj.data[j].leftTime);
                        // console.log(appData.player[i].limit_time + '--1')
                        appData.game.time = Math.ceil(obj.data[j].leftTime);
                        viewMethods.timeCountDown();
                    }
                    if(obj.data[j].chips){
                        appData.player[i].chips = Math.ceil(obj.chips);
                    }
                    break;
                }
            }
            if (!isInGame) {
                objUpdate(appData.player[i], obj_player);
            }
        }
        if(obj.chipAll && obj.chipAll.length){
            viewMethods.allInfoBet(obj.chipAll);
        }
        if (appData.scoreboard != "") {
            for (var i = 0; i < 10; i++) {
                for (s in appData.scoreboard) {
                    if (appData.player[i].account_id == s) {
                        appData.playerBoard.score[i].num = appData.player[i].num;
                        appData.playerBoard.score[i].account_id = appData.player[i].account_id;
                        appData.playerBoard.score[i].nickname = appData.player[i].nickname;
                        appData.playerBoard.score[i].account_score = Math.ceil(appData.scoreboard[s]);
                    }
                }
            }

            if (appData.game.status == 2) {
                appData.playerBoard.round = appData.game.round - 1;
            } else {
                appData.playerBoard.round = appData.game.round;
            }

            // appData.playerBoard.record = "前" + appData.playerBoard.round + "局";		
        }
        $("#playerCoins").hide();
        viewMethods.resetMyAccountStatus();
        viewMethods.updateAllPlayerStatus();
    },
    processUpdateGamerInfo: function(obj) {
        for (var i = 0, len = appData.audiences.length; i < len; i += 1) {
            if (obj.data.account_id == appData.audiences[i].account_id) {
                appData.audiences.splice(i, 1);
                break;
            }
        }
        for (var i = 0; i < 10; i++) {
            if (appData.player[i].serial_num == obj.data.serial_num) {
                appData.player[i].nickname = obj.data.nickname;
                appData.player[i].headimgurl = obj.data.headimgurl;
                appData.player[i].account_id = obj.data.account_id;
                appData.player[i].account_score = Math.ceil(obj.data.account_score);
                appData.player[i].account_status = Math.ceil(obj.data.account_status);
                appData.player[i].online_status = Math.ceil(obj.data.online_status);
                appData.player[i].ticket_checked = obj.data.ticket_checked;
            } else if (appData.player[i].account_id == obj.data.account_id) {
                socketModule.sendRefreshRoom();
            }
        }
        for (var i = 0; i < appData.player.length; i++) {
            appData.player[i].coins = [];
            if(appData.player[i].account_id>0 && appData.player[i].account_status !=13){
                for (var j = 0; j <= 7; j++) {
                    appData.player[i].coins.push("memberCoin" + appData.player[i].num + j);
                }
            } 
        }
    },

    processUpdateAccountStatus: function(obj) {
        appData.player[0].is_operation = false;
        for (var i = 0; i < 10; i++) {
            if (appData.player[i].account_id == obj.data.account_id) {
                if (appData.player[i].account_status >= 12) {
                    appData.player[i].online_status = obj.data.online_status;
                    return;
                }
                if (obj.data.online_status == 1) {
                    appData.player[i].account_status = Math.ceil(obj.data.account_status);
                } else if (obj.data.online_status == 0 && appData.player[i].account_status == 0) {
                    appData.player[i].account_id = 0;
                    appData.player[i].is_banker = false;
                    appData.player[i].playing_status = 0;
                    appData.player[i].online_status = 0;
                    appData.player[i].nickname = "";
                    appData.player[i].headimgurl = "";
                    appData.player[i].account_score = 0;
                } else if (obj.data.online_status == 0 && appData.player[i].account_status > 0) {
                    appData.player[i].account_status = Math.ceil(obj.data.account_status);
                    appData.player[i].online_status = 0;
                } else {
                    logMessage("~~~~~~~!!!!!!" + obj);
                }
                if (i != 0) {
                    if (appData.player[i].account_status == 4) {
                        setTimeout(function() {
                            mp3AudioPlay("audioNoBanker");
                        }, 100);
                    } else if (appData.player[i].account_status == 5) {
                        setTimeout(function() {
                            mp3AudioPlay("audioRobBanker");
                        }, 100);
                    }
                }
                break;
            }
        }
        if (appData.player[0].account_status == 3) {
            viewMethods.showRobBankerText();
        } else if (appData.player[0].account_status == 4) {
            viewMethods.showNotRobBankerTextFnc();
        }

        if (!appData.isFinishBankerAnimate || !appData.isFinishWinAnimate) {
            setTimeout(function() {
                viewMethods.resetMyAccountStatus();
                viewMethods.updateAllPlayerStatus();
            }, 3e3);
        } else {
            viewMethods.resetMyAccountStatus();
            viewMethods.updateAllPlayerStatus();
        }
    },
    processStartLimitTime: function(obj) {
        if (obj.data.limit_time > 1) {
            appData.game.time = Math.ceil(obj.data.limit_time);
            viewMethods.timeCountDown();
        }
    },
    processCancelStartLimitTime: function(obj) {
        appData.game.time = -1;
    },
    processGameStart: function(obj) {
        viewMethods.tableReset(0);

        appData.game.score = 0;
        appData.game.time = -1;
        appData.game.is_play = true;
        appData.game.round = appData.game.round + 1;
        appData.game.currentScore = appData.scoreList1[0];
        currentPlayerNum = -1;
        var currentPlayerAccountId;
        for (var i = 0; i < 10; i++) {
            appData.player[i].is_operation = false;

            for (var j = 0; j < obj.data.length; j++) {
                if (appData.player[i].account_id == obj.data[j].account_id) {
                    if (appData.player[i].ticket_checked == 0 && i == 0) {
                        if (appData.isAA == true) {
                            if (appData.ruleInfo.ticket_count == 2) {
                                appData.roomCard = appData.roomCard - 2;
                            } else {
                                appData.roomCard = appData.roomCard - 1;
                            }
                        }

                    }

                    appData.player[i].ticket_checked = 1;
                    appData.player[i].account_status = Math.ceil(obj.data[j].account_status);
                    appData.player[i].playing_status = Math.ceil(obj.data[j].playing_status);
                    appData.player[i].online_status = Math.ceil(obj.data[j].online_status);
                    appData.player[i].account_score = appData.player[i].account_score;
                    appData.player[i].limit_time = Math.ceil(obj.data[j].limit_time);
                    // console.log(appData.player[i].limit_time + '--2(抢庄倒计时)')

                    if (appData.player[i].playing_status > 1) {
                        currentPlayerNum = i;
                        currentPlayerAccountId = appData.player[i].account_id;
                    }

                    appData.game.score = parseInt(appData.game.score) + parseInt(obj.data[j].chip);
                    viewMethods.throwCoin(0, appData.ruleInfo.default_score);
                }
            }
        }

        appData.game.status = 2;
        if (appData.game.round == 1 && appData.ruleInfo.banker_mode == 2) {
            //固定庄家的第一回合
            appData.game.time = Math.ceil(obj.limit_time);
            viewMethods.timeCountDown();
            viewMethods.resetMyAccountStatus();
            appData.showClockRobText = true;
        } else if(appData.ruleInfo.banker_mode == 2 || appData.ruleInfo.banker_mode == 3) {
            appData.game.time = -1;
        } else {
            appData.game.time = Math.ceil(obj.limit_time);
            viewMethods.timeCountDown();
        }
    },
    processNotyChooseChip: function(obj) {
        appData.game.is_play = true;
        currentPlayerNum = -1;

        if (appData.game.status == 2) {
            for (var i = 0; i < 10; i++) {
                appData.player[i].playing_status = 1;
                if (appData.player[i].account_id == obj.data.account_id) {
                    appData.player[i].is_operation = false;
                    appData.player[i].playing_status = Math.ceil(obj.data.playing_status);
                    appData.player[i].limit_time = Math.ceil(obj.data.limit_time);
                    // console.log(appData.player[i].limit_time + '--3')
                    appData.game.can_open = obj.data.can_open;
                }

                if (appData.player[i].playing_status > 1) {
                    currentPlayerNum = i;
                }
            }
        }

        if (appData.game.autoBet && appData.player[0].account_id == obj.data.account_id) {
            var score = appData.game.currentScore;
            if (appData.player[0].account_status == 4) {
                score = score > 0 ? score : appData.scoreList2[0];
            } else {
                score = score/2;
                score = score > 0 ? score : appData.scoreList1[0];
            }
            clearTimeout(appData.game.autoBetTimeOut);
            appData.game.autoBetTimeOut = setTimeout(function () {
                viewMethods.choose(2, score, true);
            }, 1000);
            return false;
        }

        if (currentPlayerNum >= 0) {
            viewMethods.playerTimeCountDown();
        }
    },
    processShowChips: function(obj){
        appData.chipsLists = obj.data.chips;
    },
    processStartShow: function(obj) {
        appData.player[0].is_operation = false;
        var delay = 0;
        appData.prizeArea = Math.ceil(obj.prize);
        setTimeout(function() {
            for (var i = 0; i < 10; i++) {
                for (var j = 0; j < obj.data.length; j++) {
                    if (appData.player[i].account_id == obj.data[j].account_id) {
                        appData.player[i].multiples = obj.data[j].multiples;
                        appData.player[i].account_status = Math.ceil(obj.data[j].account_status);
                        appData.player[i].online_status = Math.ceil(obj.data[j].online_status);
                        appData.player[i].combo_point = obj.data[j].combo_point;
                        appData.player[i].limit_time = obj.data[j].limit_time;
                        // console.log(appData.player[i].limit_time + '--4(开奖倒计时)')
                    }
                }
            }
            appData.showClockBetText = false;
            appData.showClockPutText = false;
            appData.showClockRobText = false;
            appData.showClockShowLottery = true;

            viewMethods.resetMyAccountStatus();
            viewMethods.updateAllPlayerStatus();

            appData.game.time = Math.ceil(obj.limit_time);
            viewMethods.timeCountDown();
        }, delay);

    },
 
    processStartPut: function(obj) {
        // console.log('******' + Date.now().toLocaleString())
        var delay = 0;
        if (appData.ruleInfo.banker_mode == 2 && appData.game.round > 1) {
            delay = 1200;
        }
        
        setTimeout(function() {
            for (var i = 0; i < 10; i++) {
                for (var j = 0; j < obj.data.length; j++) {
                    if (appData.player[i].account_id == obj.data[j].account_id) {
                        appData.player[i].account_status = Math.ceil(obj.data[j].account_status);
                        appData.player[i].online_status = Math.ceil(obj.data[j].online_status);
                        appData.player[i].limit_time = Math.ceil(obj.data[j].limit_time);
                        // console.log(appData.player[i].limit_time + '--5（放宝倒计时）')
                        appData.player[i].multiples = 0;
                        if (obj.data[j].is_banker == 1 || obj.data[j].is_banker == '1') {
                            appData.player[i].is_banker = true;
                            appData.bankerAccountId = obj.data[j].account_id;
                            appData.bankerPlayer = appData.player[i];
                        } else {
                            appData.player[i].is_banker = false;
                        }
                    }
                }
            }
            appData.bankerArray = obj.grab_array.concat();
            appData.showRob = false;
            appData.showClockPutText = false;
            appData.showClockBetText = false;
            appData.showClockRobText = false;
            appData.showClockShowLottery = false;
            appData.bankerAnimateIndex = 0;

            appData.game.time = -1;
            viewMethods.goPutPrize(Math.floor(Math.random()*4));
            if (appData.ruleInfo.banker_mode == 2 && appData.game.round > 1) {
                viewMethods.robBankerWithoutAnimate(Math.ceil(obj.limit_time));
                viewMethods.brightInciteIn();
            }else if(appData.ruleInfo.banker_mode == 3){
                viewMethods.robBankerWithoutAnimate(Math.ceil(obj.limit_time));
                viewMethods.brightInciteIn();
            } else {
                viewMethods.clearBanker();
                viewMethods.robBankerAnimate(obj);
            }
           
        }, delay);

    },
    processStartBet: function(obj) {
        var delay = 0;

        if (appData.ruleInfo.banker_mode == 2 && appData.game.round > 1) {
            delay = 1200;
        }

        setTimeout(function() {
            for (var i = 0; i < 10; i++) {
                for (var j = 0; j < obj.data.length; j++) {
                    if (appData.player[i].account_id == obj.data[j].account_id) {
                        appData.player[i].account_status = Math.ceil(obj.data[j].account_status);
                        appData.player[i].online_status = Math.ceil(obj.data[j].online_status);
                        appData.player[i].limit_time = Math.ceil(obj.data[j].limit_time);
                        // console.log(appData.player[i].limit_time + '--6')
                        appData.player[i].multiples = 0;
                    }
                }
            }
            appData.showRob = false;
            appData.showClockPutText = false;
            appData.showClockBetText = false;
            appData.showClockRobText = false;
            appData.showClockShowLottery = false;

            viewMethods.resetMyAccountStatus();
            viewMethods.updateAllPlayerStatus();
            appData.game.time = Math.ceil(obj.limit_time);
            viewMethods.timeCountDown();
        }, delay);
    },
    processCardInfo: function(obj) {
    },
    processPKCard: function(obj) {
        var num1 = 0,
            num2 = 0;
        for (var i = 0; i < 10; i++) {
            if (appData.player[i].account_id == obj.data.account_id) {
                appData.player[i].account_score = appData.player[i].account_score - Math.ceil(obj.data.score);
                viewMethods.throwCoin(appData.player[i].num, obj.data.score);
            }

            if (appData.player[i].account_id == obj.data.loser_id) {
                appData.player[i].account_status = 7;
                num1 = i;
            }

            if (appData.player[i].account_id == obj.data.winner_id) {
                num2 = i;
            }
        }
        appData.game.score = appData.game.score + Math.ceil(obj.data.score);

        viewMethods.playerPK(num1, num2);
    },
    processBroadcastVoice: function(obj) {
        for (var i = 0; i < 10; i++) {
            if (appData.player[i].account_id == obj.data.account_id && i != 0) {
                m4aAudioPlay("message" + obj.data.voice_num);
                viewMethods.messageSay(i, obj.data.voice_num);
            }
        }
    },
    processCreateRoom: function(obj) {
        //window.location.href = globalData.baseUrl + "game/main?room_number=" + obj.data.room_number + '&dealer_num=' + globalData.dealerNum;
    },
    processUpdateAccountScore: function(obj) {
        console.log('obj:'+ JSON.stringify(obj))
        for (var i = 0; i < 10; i++) {
            if (appData.player[i].account_id == obj.data.account_id) {
                appData.player[i].haveBet = Math.ceil(obj.data.chips);

                appData.game.score = appData.game.score + Math.ceil(obj.data.chip.score);
                if (i != 0 || appData.isAudience) {
                    viewMethods.throwCoin(appData.player[i].num, obj.data.chip.score);
                    m4aAudioPlay(obj.data.chip.score + "f");
                }
                viewMethods.playingBet(appData.player[i].num, obj.data.chip.x, obj.data.chip.y, obj.data.chip.score);
            }
        }
        appData.player[0].is_operation = false;
    },
    processOpenCard: function(obj) {
        if (!appData.game.is_play) {
            return 0;
        }
        for (var i = 0; i < 10; i++) {
            if (appData.player[i].account_id == obj.data.account_id) {
                appData.player[i].account_score = appData.player[i].account_score - Math.ceil(obj.data.score);
                appData.game.score = appData.game.score + Math.ceil(obj.data.score);
                viewMethods.throwCoin(appData.player[i].num, obj.data.score)
            }
        }
    },
    processStopShow: function(obj){
        appData.game.time = -1;
        for(var i=0;i<appData.player.length;i++){
            if(appData.player[i].account_id>0){
                appData.player[i].account_status = Math.ceil(obj.account_status);
            }
        }
        $("#prize-prize-hide").animate({
            left: "150%"
        }, 1000 * appData.openSpeed,'linear',function(){
            $("#prize-prize-hide").css({"left": "3%","display": "none"});
        })
    },
    processWin: function(obj) {
        setTimeout(()=>{
            appData.showWinWaver = true;
            viewMethods.winWaverIn();

            appData.game.is_play = false;
            appData.game.round = Math.ceil(obj.data.game_num);
            appData.game.total_num = Math.ceil(obj.data.total_num);
            appData.playerBoard.round = Math.ceil(obj.data.game_num);
            appData.game.current_win = obj.data.win_score;

            appData.game.show_score = false;
            appData.showClockShowLottery = false;
            appData.showShowCardButton = false;
            appData.showClickShowCard = false;
            appData.showClockBetText = false;
            appData.showClockPutText = false;
            appData.showClockRobText = false;

            if (appData.ruleInfo.banker_mode == 2) {
                if (appData.player[0].is_banker) {
                    appData.canBreak = Math.ceil(obj.data.can_break);
                }

                if (obj.data.is_break != null || obj.data.is_break != undefined) {
                    appData.isBreak = Math.ceil(obj.data.is_break);
                }
            }
            viewMethods.showMemberScore(false);  //是否得分的动画函数

            for (var i = 0; i < appData.player.length; i++) {
                if (appData.player[i].account_status == 7) {
                    appData.player[i].account_status = 8;
                }
                
                for (var j = 0; j < obj.data.loser_array.length; j++) {
                    if (appData.player[i].account_id == obj.data.loser_array[j].account_id) {
                        appData.player[i].single_score = obj.data.loser_array[j].score;
                        break;
                    }
                }
                for (var k = 0; k < obj.data.winner_array.length; k++) {
                    if (appData.player[i].account_id == obj.data.winner_array[k].account_id) {
                        appData.player[i].single_score = obj.data.winner_array[k].score;
                        break;
                    }
                }
            }
            appData.game.time = -1;              //关于倒计时
            viewMethods.updateAllPlayerStatus(); //判断牌

            setTimeout(function() {
                viewMethods.resetMyAccountStatus();
            }, 200);
            setTimeout(function() {
                viewMethods.winAnimate(obj);
            }, 3e3);
        },1000 * appData.openSpeed) 

        setTimeout(()=>{
            appData.player.forEach((ele)=>{
                ele.single_score = ""
            })
            // console.log("----single_score等于零:")
        },1000 * appData.openSpeed+7000);
        for (var i = 0; i < appData.player.length; i++) {
            if (appData.player[i].account_status == 7) {
                appData.player[i].account_status = 8;
            }
            
            for (var j = 0; j < obj.data.loser_array.length; j++) {
                if (appData.player[i].account_id == obj.data.loser_array[j].account_id) {
                    appData.player[i].single_score = obj.data.loser_array[j].score;
                    break;
                }
            }
            for (var k = 0; k < obj.data.winner_array.length; k++) {
                if (appData.player[i].account_id == obj.data.winner_array[k].account_id) {
                    appData.player[i].single_score = obj.data.winner_array[k].score;
                    break;
                }
            }
        }
    },
    processDiscard: function(obj) {
        appData.player[0].account_status = 6;
    },
    processBalanceScoreboard: function(obj) {
        var data = new Date(parseInt(obj.time) * 1000);
        var N = data.getFullYear() + "-";
        var Y = data.getMonth() + 1 + "-";
        var R = data.getDate() + " ";
        var H = data.getHours();
        var M = data.getMinutes();
        var Z = ":";
        if (M < 10)
            Z = Z + 0;
        var str = N + Y + R + H + Z + M;

        appData.playerBoard.round = obj.game_num;
        appData.playerBoard.room = '房间号:' + globalData.roomNumber;
        // appData.playerBoard.record = str + " 前" + appData.playerBoard.round + "局";
        appData.playerBoard.record = str;
        appData.playerBoard.score = [];

        var scores = obj.scoreboard;
        for (s in scores) {
            var num = 0;
            var name = scores[s].name;

            if (userData.accountId == scores[s].account_id) {
                num = 1;
            }

            appData.playerBoard.score.push({
                "account_id": scores[s].account_id,
                "nickname": name,
                "account_score": Math.ceil(scores[s].score),
                "num": num,
            });
        }
        //对积分榜排序
        appData.playerBoard.score.sort(function(a,b){
            return b.account_score - a.account_score;
        });
    },
    processLastScoreboard: function(obj) {
        if (obj == undefined) {
            return;
        }

        try {
            var data = new Date(parseInt(obj.time) * 1000);
            var N = data.getFullYear() + "-";
            var Y = data.getMonth() + 1 + "-";
            var R = data.getDate() + " ";
            var H = data.getHours();
            var M = data.getMinutes();
            var Z = ":";
            if (M < 10)
                Z = Z + 0;
            var str = N + Y + R + H + Z + M;

            appData.playerBoard.round = obj.game_num;
            // appData.playerBoard.record = str + " 前" + appData.playerBoard.round + "局";
            appData.playerBoard.record = str;
            appData.playerBoard.score = [];

            if (obj.total_num != undefined && obj.total_num != null && obj.total_num != '') {
                appData.game.total_num = obj.total_num;
            }

            var scores = obj.scoreboard;
            for (s in scores) {
                var num = 0;
                if (userData.accountId == scores[s].account_id) {
                    num = 1;
                }

                appData.playerBoard.score.push({
                    "account_id": scores[s].account_id,
                    "nickname": scores[s].name,
                    "account_score": Math.ceil(scores[s].score),
                    "num": num
                });
            }

            chooseBigWinner();
            $(".ranking .rankBack").css("opacity", "1");
            $(".roundEndShow").show();

            $(".ranking").show();
            canvas();
            $('#endCreateRoomBtn').show();
        } catch (error) {
            console.log(error);
        }
    },
    processAllGameEndData(obj){
        if(obj.data.length !== 0){
        }
        appData.storeList = obj.data;
    },
    processGameEndData(obj){
        obj.data.players.map((player) => {
            let cards = [];
        })
        appData.storeList.push(obj.data);
    }
};

var storeMethods = {
    setPosition(type){
        switch(type){
            case "A":{
                return 3;
            }
            case "B":{
                return 2;
            }
            case "C":{
                return 0;
            }
            case "D":{
                return 1;
            }
            default:{
                return '没有该牌';
            }
        }
    }
}

var viewMethods = {
    clickHome: function() {
        window.location.href = globalData.baseUrl + "f/ym";
	// window.location.href = "http://version2.tt-cool.com/f/ym?openid=" + sessionStorage.getItem('openid');
    },
    clickShowAlert: function(type, text) {
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
        }, 0);
    },
    clickCloseAlert: function() {
        if (appData.alertType == 22) {
            appData.isShowAlert = false;
            httpModule.getActivityInfo();
        } else if (appData.alertType == 31) {
            window.location.href = window.location.href + "&id=" + 10000 * Math.random();
        } else {
            appData.isShowAlert = false;
        }
    },
    clickSitDown: function() {
        appData.isShowAlert = false;
        socketModule.sendJoinRoom();
    },
    clickReady: function() {
        if (appData.player[0].is_operation || appData.game.status != 1) {
            return 0;
        }

        socketModule.sendReadyStart();
        appData.player[0].is_operation = true;
    },
    clickPutPrize: function() {
        if (appData.player[0].is_operation) {
            return 0;
        }
        socketModule.sendPutPrize();
        appData.player[0].is_operation = true;
    },
    clickChooseChip: function(event,num) {
        $(".chip-type").removeClass("chip-active");
        $(event.target).addClass("chip-active");
        appData.currentChip = num;
    },
    clickPlayBet: function(event){
        var upper_limit = Math.ceil(appData.ruleInfo.upper_limit)
        var currentChip = Math.ceil(appData.currentChip)
        if(!appData.currentChip || appData.player[0].is_operation || appData.upperLimit || ((upper_limit > appData.player[0].haveBet) && ((appData.player[0].haveBet + currentChip) > upper_limit))) return false;
        var e = event || window.event;

        var coordX = e.clientX;
        var coordY = e.clientY;
        var originX = $("#checkerboard-area").offset().left;
        var originY = $("#checkerboard-area").offset().top;
        var originW = $("#checkerboard-area").width();
        var originH = $("#checkerboard-area").height();

        var relatX =  ((coordX - originX)/ originW).toFixed(5);
        var relatY =  ((coordY - originY)/ originH).toFixed(5);

        socketModule.sendChooseChip(relatX,relatY,appData.currentChip);
        // viewMethods.judgeCoordPeak(relatX,relatY);
        appData.player[0].is_operation = true;
    },
    judgeCoordPeak: function(relatX,relatY){//验证某坐标是否在多边形内
        // nvert: //多边形的顶点数
        // vertx, verty: //顶点X坐标和Y坐标分别组成的数组
        // testx, testy: //需要测试的点的X坐标和Y坐标
        // var nvert = 4;
        var vertx = [0.021,0.140,0.375,0.375],
        verty = [0.021,0.140,0.140,0.021],
        testx = parseFloat(relatX),
        testy = parseFloat(relatY);

        var i, j, c = 0;
        for (i=0, j=3; i < 4; j=i++) {
            if ( ((verty[i]>testy) != (verty[j]>testy)) && (testx < (vertx[j]-vertx[i]) * (testy-verty[i]) / (verty[j]-verty[i]) + vertx[i]) )
            c = !c;
        }
        console.log(c);
    },
    playingBet: function(num,x,y,score){
        var originX = $("#checkerboard-area").offset().left;
        var originY = $("#checkerboard-area").offset().top;
        var originW = $("#checkerboard-area").width();
        var originH = $("#checkerboard-area").height();
        var len = $(".chipBet"+num).length;
        var html = '<div class="chipBet chipBet'+ num +'" id="chipBet'+ num + (len +1) +'"><img src="'+ globalData.imageUrl +'files/images/dp/chip-'+score+'.png" /></div>';
        $("#playerBet").append(html);
        $("#chipBet"+ num +(len +1)).animate({
            top: (originY + originH*y - 10) + "px",
            left: (originX + originW*x - 10) + "px"
        }, 100);
        console.log("originX:"+ originX)
        console.log("originY:"+ originY)
        console.log("originW:"+ originW)
        console.log("originH:"+ originH)
        console.log("len:"+ len)
        console.log("html:"+ html)
        console.log("num:"+ num)
        console.log("x:"+ x)
        console.log("y:"+ y)
        console.log("score:"+ score)
    },
    allInfoBet: function(chips){
        var originX = $("#checkerboard-area").offset().left;
        var originY = $("#checkerboard-area").offset().top;
        var originW = $("#checkerboard-area").width();
        var originH = $("#checkerboard-area").height();
        var html = '';
        for(var i=0;i<chips.length;i++){
            html += '<div class="chipBet" style="left:'+ (originX + originW*chips[i].x - 10) +'px;top:'+ (originY + originH*chips[i].y -10) + 'px;"><img src="'+ globalData.imageUrl +'files/images/dp/chip-'+chips[i].score+'.png" /></div>';
        }
        $("#playerBet").html(html);
    },
    clickShowChips: function() {
        appData.chipsLists = [];
        socketModule.sendShowChips();
        appData.showChips.isShow = true;
    },
    clickStopBet: function() {
        if (appData.player[0].is_operation) {
            return 0;
        }
        socketModule.sendStopBet();
        appData.player[0].is_operation = true;
    },
    goPutPrize: function(type){
        appData.prizeArea = type;
    },
    brightInciteOut: function(){
        if(appData.player[0].account_status == 6 && appData.player[0].is_banker){
            $(".prize-bright").fadeOut(500)
            setTimeout(()=>{
                viewMethods.brightInciteIn();
            },500)
        }else{
            $(".prize-bright").show();
        }
    },
    brightInciteIn: function(){
        if(appData.player[0].account_status == 6 && appData.player[0].is_banker){
            $(".prize-bright").fadeIn(500)
            setTimeout(()=>{
                viewMethods.brightInciteOut();
            },500)
        }else{
            $(".prize-bright").show();
        }
    },
    winWaverOut: function(){
        if(appData.player[0].account_status == 12){
            $(".open-prize").fadeOut(500)
            setTimeout(()=>{
                viewMethods.winWaverIn();
            },500)
        }else{
            $(".open-prize").hide();
        }
    },
    winWaverIn: function(){
        if(appData.player[0].account_status == 12){
            $(".open-prize").fadeIn(500)
            setTimeout(()=>{
                viewMethods.winWaverOut();
            },500)
        }else{
            $(".open-prize").hide();
        }
    },
    clickShowPrize: function(num) {
        if (appData.player[0].is_operation) {
            return 0;
        }
        appData.openSpeed = num;
        socketModule.sendShowPrize();
        appData.player[0].is_operation = true;
    },
    clearBanker: function() {
        for (var i = 0; i < appData.player.length; i++) {
            appData.player[i].is_banker = false;
        }
        appData.isFinishBankerAnimate = false;
        var totalCount = appData.bankerArray.length * 4;
        appData.bankerAnimateDuration = parseInt(3e3 / totalCount);
    },
    autoReadyStatus: function() {
        appData.game.autoReady = !appData.game.autoReady;
    },
    autoBetStatus: function () {
        appData.game.autoBet = !appData.game.autoBet;
    },
    resetShowButton: function() {
        appData.showTimesCoin = false;
        appData.showRob = false;
        appData.showShowCardButton = false;
        appData.showClickShowCard = false;
        appData.showNotRobText = false;
        appData.showRobText = false;
        appData.showBankerCoinText = false;
        appData.upperLimit = false;
    },
    robBankerWithoutAnimate: function(tim) {
        for (var i = 0; i < appData.player.length; i++) {
            if (appData.player[i].account_id == appData.bankerAccountId) {
                appData.player[i].is_banker = true;
                bankerNum = appData.player[i].num;
            } else {
                appData.player[i].is_banker = false;
            }

            $("#avatar-banker-animate-" + appData.player[i].num).hide();
            $("#avatar-banker-animate1-" + appData.player[i].num).hide();
        }

        setTimeout(function() {
            appData.showClockPutText = true;
            appData.isFinishBankerAnimate = true;
            viewMethods.resetMyAccountStatus();
            viewMethods.updateAllPlayerStatus();
        }, 10);

        appData.game.time = tim;
        if (appData.game.time > 0) {
            viewMethods.timeCountDown();
        }
    },
    robBankerAnimate: function(obj) {
        for (var i = 0; i < appData.bankerArray.length; i++) {
            var imgId = "#avatar-banker-" + appData.bankerArray[i];
            $(imgId).hide();
        }
        var totalCount = appData.bankerArray.length * 4;
        if (appData.bankerAnimateCount >= totalCount || appData.bankerAnimateIndex < 0 || appData.bankerArray.length < 2) {
            appData.bankerAnimateCount = 0;
            appData.bankerAnimateIndex = -1;
            var imgId = "#avatar-banker-" + appData.bankerAccountId;
            $(imgId).show();

            var bankerNum = '';

            for (var i = 0; i < appData.player.length; i++) {
                if (appData.player[i].account_id == appData.bankerAccountId) {
                    appData.player[i].is_banker = true;
                    bankerNum = appData.player[i].num;
                } else {
                    appData.player[i].is_banker = false;
                }

                $("#avatar-banker-animate-" + appData.player[i].num).hide();
                $("#avatar-banker-animate1-" + appData.player[i].num).hide();
            }

            $(imgId).hide();

            $("#avatar-banker-animate-" + bankerNum).css({
                top: "0",
                left: "0",
                width: "100%",
                height: "100%"
            });

            $("#avatar-banker-animate1-" + bankerNum).css({
                top: "-5%",
                left: "-5%",
                width: "110%",
                height: "110%"
            });

            $("#avatar-banker-animate-" + bankerNum).show();
            $("#avatar-banker-animate1-" + bankerNum).show();

            $("#avatar-banker-animate1-" + bankerNum).animate({
                top: "-5%",
                left: "-5%",
                width: "110%",
                height: "110%"
            }, 400, function() {
                $("#avatar-banker-animate1-" + bankerNum).animate({
                    top: "0",
                    left: "0",
                    width: "100%",
                    height: "100%"
                }, 400, function() {
                    $("#avatar-banker-animate1-" + bankerNum).hide();
                });
            });

            $("#avatar-banker-animate-" + bankerNum).animate({
                top: "-10%",
                left: "-10%",
                width: "120%",
                height: "120%"
            }, 400, function() {
                $("#avatar-banker-animate-" + bankerNum).animate({
                    top: "-5%",
                    left: "-5%",
                    width: "110%",
                    height: "110%"
                }, 400, function() {
                    $("#avatar-banker-animate-" + bankerNum).hide();
                    setTimeout(function() {
                        // console.log('1803: resetMyAccountStatus');
                        appData.showClockRobText = false;
                        appData.showClockPutText = true;
                        appData.isFinishBankerAnimate = true;

                        if (appData.ruleInfo.banker_mode == 2) {
                            for (var i = 0; i < obj.data.length; i++) {
                                for (var j = 0; j < appData.player.length; j++) {
                                    if (appData.player[j].account_id == obj.data[i].account_id) {
                                        appData.player[j].account_score = obj.data[i].account_score;
                                    }
                                }
                            }
                            if (appData.game.round != 1) {
                                viewMethods.resetMyAccountStatus();
                                viewMethods.updateAllPlayerStatus();
                            }
                        } else {
                            viewMethods.resetMyAccountStatus();
                            viewMethods.updateAllPlayerStatus();
                        }
                        viewMethods.brightInciteIn();
                    }, 10);

                    appData.game.time = Math.ceil(obj.limit_time);
                    if (appData.game.time > 0) {
                        viewMethods.timeCountDown();
                    }
                });
            });

            return;
        }

        var accountId = appData.bankerArray[appData.bankerAnimateIndex];
        var imgId = "#avatar-banker-" + accountId;

        $(imgId).show();

        appData.lastBankerImgId = imgId;
        appData.bankerAnimateCount++;
        appData.bankerAnimateIndex++;

        if (appData.bankerAnimateIndex >= appData.bankerArray.length) {
            appData.bankerAnimateIndex = 0;
        }

        setTimeout(function() {
            viewMethods.robBankerAnimate(obj);
        }, appData.bankerAnimateDuration);
    },
    showMemberScore: function(isShow) {
        // console.log("-------isShow---1683")
        if (isShow) {
            $(".memberScoreText1").show();
            $(".memberScoreText2").show();
            $(".memberScoreText3").show();
            $(".memberScoreText4").show();
            $(".memberScoreText5").show();
            $(".memberScoreText6").show();
            $(".memberScoreText7").show();
            $(".memberScoreText8").show();
            $(".memberScoreText9").show();
            $(".memberScoreText10").show();
        } else {
            $(".memberScoreText1").hide();
            $(".memberScoreText2").hide();
            $(".memberScoreText3").hide();
            $(".memberScoreText4").hide();
            $(".memberScoreText5").hide();
            $(".memberScoreText6").hide();
            $(".memberScoreText7").hide();
            $(".memberScoreText8").hide();
            $(".memberScoreText9").hide();
            $(".memberScoreText10").hide();
        }
    },
    // 胜利方金币动画
    winAnimate: function(obj) {
        appData.isFinishWinAnimate = false;
        var winnerNums = new Array();
        var loserNums = new Array();
        appData.bankerPlayerNum = appData.bankerPlayer.num;

        for (var i = 0; i < obj.data.winner_array.length; i++) {
            for (var j = 0; j < appData.player.length; j++) {
                if (obj.data.winner_array[i].account_id == appData.player[j].account_id) {
                    if (appData.player[j].num == appData.bankerPlayer.num) {
                        isBankerWin = true;
                        appData.bankerPlayerNum = appData.player[j].num;
                    } else {
                        winnerNums.push(appData.player[j].num);
                    }
                }
            }
        }

        for (var i = 0; i < obj.data.loser_array.length; i++) {
            for (var j = 0; j < appData.player.length; j++) {
                if (obj.data.loser_array[i].account_id == appData.player[j].account_id) {
                    if (appData.player[j].num != appData.bankerPlayerNum) {
                        loserNums.push(appData.player[j].num);
                    }
                }
            }
        }

        viewMethods.resetCoinsPosition();
        $("#playerCoins").show();
        for (var i = 1; i < 11; i++) {
            viewMethods.showCoins(i, false);
        }
        var clientWidth =  document.documentElement.clientWidth;
        var clientHeight = document.documentElement.clientHeight;
        var leftUser = 0.02 * clientWidth + 0.025 * clientHeight + 'px';
        var rightUser = 0.98 * clientWidth - 0.05 * clientHeight + 'px';
        //把赢家玩家金币暂时放到庄家位置
        for (var i = 0; i < winnerNums.length; i++) {
            for (var j = 0; j < 8; j++) {
                if (appData.bankerPlayerNum == 1) {
                    $(".memberCoin" + winnerNums[i] + j).css("top", "84vh");
                    $(".memberCoin" + winnerNums[i] + j).css("left", leftUser);
                } else if (appData.bankerPlayerNum == 2) {
                    $(".memberCoin" + winnerNums[i] + j).css("top", "66vh");
                    $(".memberCoin" + winnerNums[i] + j).css("left", leftUser);
                } else if (appData.bankerPlayerNum == 3) {
                    $(".memberCoin" + winnerNums[i] + j).css("top", "46vh");
                    $(".memberCoin" + winnerNums[i] + j).css("left", leftUser);
                } else if (appData.bankerPlayerNum == 4) {
                    $(".memberCoin" + winnerNums[i] + j).css("top", "28vh");
                    $(".memberCoin" + winnerNums[i] + j).css("left", leftUser);
                } else if (appData.bankerPlayerNum == 5) {
                    $(".memberCoin" + winnerNums[i] + j).css("top", "13vh");
                    $(".memberCoin" + winnerNums[i] + j).css("left", leftUser);
                } else if (appData.bankerPlayerNum == 6) {
                    $(".memberCoin" + winnerNums[i] + j).css("top", "13vh");
                    $(".memberCoin" + winnerNums[i] + j).css("left", rightUser);
                }else if (appData.bankerPlayerNum == 7) {
                    $(".memberCoin" + winnerNums[i] + j).css("top", "28vh");
                    $(".memberCoin" + winnerNums[i] + j).css("left", rightUser);
                }else if (appData.bankerPlayerNum == 8) {
                    $(".memberCoin" + winnerNums[i] + j).css("top", "46vh");
                    $(".memberCoin" + winnerNums[i] + j).css("left", rightUser);
                }else if (appData.bankerPlayerNum == 9) {
                    $(".memberCoin" + winnerNums[i] + j).css("top", "66vh");
                    $(".memberCoin" + winnerNums[i] + j).css("left", rightUser);
                }else if (appData.bankerPlayerNum == 10) {
                    $(".memberCoin" + winnerNums[i] + j).css("top", "84vh");
                    $(".memberCoin" + winnerNums[i] + j).css("left", rightUser);
                }
            }
        }

        //显示输家金币
        for (var i = 0; i < loserNums.length; i++) {
            viewMethods.showCoins(loserNums[i], true);
        }
        //输家金币给庄家
        for (var i = 0; i < loserNums.length; i++) {
            var playerNum = loserNums[i];
            for (var j = 0; j < 8; j++) {
                if (appData.bankerPlayerNum == 1) {
                    $(".memberCoin" + loserNums[i] + j).animate({
                        top: "84vh",
                        left: leftUser
                    }, 150 + 150 * j);
                } else if (appData.bankerPlayerNum == 2) {
                    $(".memberCoin" + loserNums[i] + j).animate({
                        top: "66vh",
                        left: leftUser
                    }, 150 + 150 * j);
                } else if (appData.bankerPlayerNum == 3) {
                    $(".memberCoin" + loserNums[i] + j).animate({
                        top: "46vh",
                        left: leftUser
                    }, 150 + 150 * j);
                } else if (appData.bankerPlayerNum == 4) {
                    $(".memberCoin" + loserNums[i] + j).animate({
                        top: "28vh",
                        left: leftUser
                    }, 150 + 150 * j);
                } else if (appData.bankerPlayerNum == 5) {
                    $(".memberCoin" + loserNums[i] + j).animate({
                        top: "13vh",
                        left: leftUser
                    }, 150 + 150 * j);
                } else if (appData.bankerPlayerNum == 6) {
                    $(".memberCoin" + loserNums[i] + j).animate({
                        top: "13vh",
                        left: rightUser
                    }, 150 + 150 * j);
                }else if (appData.bankerPlayerNum == 7) {
                    $(".memberCoin" + loserNums[i] + j).animate({
                        top: "28vh",
                        left: rightUser
                    }, 150 + 150 * j);
                }else if (appData.bankerPlayerNum == 8) {
                    $(".memberCoin" + loserNums[i] + j).animate({
                        top: "46vh",
                        left: rightUser
                    }, 150 + 150 * j);
                }else if (appData.bankerPlayerNum == 9) {
                    $(".memberCoin" + loserNums[i] + j).animate({
                        top: "66vh",
                        left: rightUser
                    }, 150 + 150 * j);
                }else if (appData.bankerPlayerNum == 10) {
                    $(".memberCoin" + loserNums[i] + j).animate({
                        top: "84vh",
                        left: rightUser
                    }, 150 + 150 * j);
                }
            }
            setTimeout(function() {
                mp3AudioPlay("audioCoin");
            }, 10);
        }

        var winnerTime = 100;
        var totalTime = 100;
        if (loserNums.length >= 1) {
            winnerTime = 1800;
            if (winnerNums.length >= 1) {
                totalTime = 3600;
            } else {
                totalTime = 1800;
            }
        } else {
            if (winnerNums.length >= 1) {
                totalTime = 1800;
            }
        }

        if (ruleInfo.banker_mode == 4) {
            totalTime = 1800;
            winnerTime = 1800;
        }

        if (winnerNums.length >= 1) {
            setTimeout(function() {
                //显示赢家金币
                for (var i = 0; i < loserNums.length; i++) {
                    viewMethods.showCoins(loserNums[i], false);
                }
                for (var i = 0; i < winnerNums.length; i++) {
                    viewMethods.showCoins(winnerNums[i], true);
                }
                for (var i = 0; i < winnerNums.length; i++) {
                    for (var j = 0; j < 8; j++) {
                        if (winnerNums[i] == 1) {
                            $(".memberCoin" + winnerNums[i] + j).animate({
                                top: "84vh",
                                left: leftUser
                            }, 150 + 150 * j);
                        } else if (winnerNums[i] == 2) {
                            $(".memberCoin" + winnerNums[i] + j).animate({
                                top: "66vh",
                                left: leftUser
                            }, 150 + 150 * j);
                        } else if (winnerNums[i] == 3) {
                            $(".memberCoin" + winnerNums[i] + j).animate({
                                top: "46vh",
                                left: leftUser
                            }, 150 + 150 * j);
                        } else if (winnerNums[i] == 4) {
                            $(".memberCoin" + winnerNums[i] + j).animate({
                                top: "28vh",
                                left: leftUser
                            }, 150 + 150 * j);
                        } else if (winnerNums[i] == 5) {
                            $(".memberCoin" + winnerNums[i] + j).animate({
                                top: "13vh",
                                left: leftUser
                            }, 150 + 150 * j);
                        } else if (winnerNums[i] == 6) {
                            $(".memberCoin" + winnerNums[i] + j).animate({
                                top: "13vh",
                                left: rightUser
                            }, 150 + 150 * j);
                        }else if (winnerNums[i] == 7) {
                            $(".memberCoin" + winnerNums[i] + j).animate({
                                top: "28vh",
                                left: rightUser
                            }, 150 + 150 * j);
                        }else if (winnerNums[i] == 8) {
                            $(".memberCoin" + winnerNums[i] + j).animate({
                                top: "46vh",
                                left: rightUser
                            }, 150 + 150 * j);
                        }else if (winnerNums[i] == 9) {
                            $(".memberCoin" + winnerNums[i] + j).animate({
                                top: "66vh",
                                left: rightUser
                            }, 150 + 150 * j);
                        }else if (winnerNums[i] == 10) {
                            $(".memberCoin" + winnerNums[i] + j).animate({
                                top: "84vh",
                                left: rightUser
                            }, 150 + 150 * j);
                        }
                    }
                }
                setTimeout(function() {
                    mp3AudioPlay("audioCoin");
                }, 10);
            }, winnerTime);
            setTimeout(function() {
                viewMethods.finishWinAnimate(obj);
            }, totalTime);
        } else {
            setTimeout(function() {
                viewMethods.finishWinAnimate(obj);
            }, totalTime);
        }
    },
    finishWinAnimate: function(obj) {
        // console.log("------finishWinAnimate---1947")
        var a = JSON.stringify(obj)
        var jsonData = JSON.stringify(appData)
        // console.log('obj:'+a);
        // console.log('appData:'+jsonData);
        $("#playerCoins").hide();
        appData.game.show_score = true;
        appData.showWinWaver = false;
        
        $(".memberScoreText1").fadeIn(200);
        $(".memberScoreText2").fadeIn(200);
        // console.log("------finishWinAnimate---1958")
        $(".memberScoreText3").fadeIn(200);
        $(".memberScoreText4").fadeIn(200);
        $(".memberScoreText5").fadeIn(200);
        $(".memberScoreText6").fadeIn(200);
        $(".memberScoreText7").fadeIn(200);
        $(".memberScoreText8").fadeIn(200);
        $(".memberScoreText9").fadeIn(200);
        $(".memberScoreText10").fadeIn(200, function() {

            if (appData.ruleInfo.banker_mode == 2) {
                if (appData.isBreak != 1) {
                    viewMethods.gameOverNew(obj.data.score_board, obj.data.balance_scoreboard);
                } else {
                    for (var i = 0; i < 10; i++) {
                        for (s in obj.data.score_board) {
                            if (appData.player[i].account_id == s) {
                                appData.player[i].account_score = Math.ceil(obj.data.score_board[s]);
                            }
                        }
                    }
                }
            } else {
                viewMethods.gameOverNew(obj.data.score_board, obj.data.balance_scoreboard);
            }

            setTimeout(function() {
                // console.log("-----setTimeout---1980")
                $(".memberScoreText1").fadeOut("slow");
                $(".memberScoreText2").fadeOut("slow");
                $(".memberScoreText3").fadeOut("slow");
                $(".memberScoreText4").fadeOut("slow");
                $(".memberScoreText5").fadeOut("slow");
                $(".memberScoreText6").fadeOut("slow");
                $(".memberScoreText7").fadeOut("slow");
                $(".memberScoreText8").fadeOut("slow");
                $(".memberScoreText9").fadeOut("slow");
                $(".memberScoreText10").fadeOut("slow");
                $("#prize-prize-hide").show();

                appData.roomStatus = 1;
                if (appData.ruleInfo.banker_mode == 2 && appData.isBreak == 1) {
                    appData.overType = 2;
                    setTimeout(function() {
                        viewMethods.clickShowAlert(9, '庄家分数不足，提前下庄，点击确定查看结算');
                    }, 1000);
                } else {
                    for (var i = 0; i < 10; i++) {
                        if (appData.player[i].account_status >= 6 && ruleInfo.banker_mode == 1) {
                            appData.player[i].is_banker = false;
                            if (appData.player[i].account_id == appData.bankerID) {
                                appData.player[i].is_banker = true;
                            }
                        }
                        if (appData.player[i].account_status != 9) {
                            appData.player[i].account_status = 1;
                        }
                    }
                    if(appData.game.autoReady && !appData.isAudience && appData.game.round >0&&appData.game.round < appData.game.total_num){
                        socketModule.sendReadyStart();
                        appData.player[0].is_operation = true;
                    }
                }
            }, 2e3);
            $("#playerBet").html('');
            appData.isFinishWinAnimate = true;

            if (appData.ruleInfo.banker_mode == 2) {
                if (appData.isBreak == 1) {
                } else {
                    if (obj.data.total_num == obj.data.game_num) {
                        setTimeout(function() {
                            viewMethods.roundEnd();
                            newNum = obj.data.room_number;
                        }, 1e3);
                    }
                }
                return;
            }

            if (obj.data.total_num == obj.data.game_num) {
                setTimeout(function() {
                    viewMethods.roundEnd();
                    newNum = obj.data.room_number;
                }, 1e3);
            }

        })
    },
    resetCoinsPosition: function() {
        var clientWidth =  document.documentElement.clientWidth;
        var clientHeight = document.documentElement.clientHeight;
        var leftUser = 0.02 * clientWidth + 0.025 * clientHeight + 'px';
        var rightUser = 0.98 * clientWidth - 0.05 * clientHeight + 'px';
        for (var i = 1; i < 11; i++) {
            for (var j = 0; j < 8; j++) {
                if (i == 1) {
                    $(".memberCoin" + i + j).css({
                        top: "84vh",
                        left: leftUser
                    });
                } else if (i == 2) {
                    $(".memberCoin" + i + j).css({
                        top: "66vh",
                        left: leftUser
                    });
                } else if (i == 3) {
                    $(".memberCoin" + i + j).css({
                        top: "46vh",
                        left: leftUser
                    });
                } else if (i == 4) {
                    $(".memberCoin" + i + j).css({
                        top: "28vh",
                        left: leftUser
                    });
                } else if (i == 5) {
                    $(".memberCoin" + i + j).css({
                        top: "13vh",
                        left: leftUser
                    });
                } else if (i == 6) {
                    $(".memberCoin" + i + j).css({
                        top: "13vh",
                        left: rightUser
                    });
                }else if (i == 7) {
                    $(".memberCoin" + i + j).css({
                        top: "28vh",
                        left: rightUser
                    });
                }else if (i == 8) {
                    $(".memberCoin" + i + j).css({
                        top: "46vh",
                        left: rightUser
                    });
                }else if (i == 9) {
                    $(".memberCoin" + i + j).css({
                        top: "66vh",
                        left: rightUser
                    });
                }else if (i == 10) {
                    $(".memberCoin" + i + j).css({
                        top: "84vh",
                        left: rightUser
                    });
                }
            }
        }
    },
    resetMyAccountStatus: function() {
        if (appData.player[0].account_status == 6) {
            if (!appData.isFinishBankerAnimate) {
                return;
            }
        }
        viewMethods.resetShowButton();

        if (appData.player[0].account_status == 3) {
            appData.showRob = true;
        } else if (appData.player[0].account_status == 4) {
            appData.showNotRobText = true;
        } else if (appData.player[0].account_status == 5) {
            appData.showRobText = true;
        } else if (appData.player[0].account_status == 9) {
            if (appData.player[0].is_banker == true) {
                appData.showBankerCoinText = true;
            } else {
                if (appData.isFinishBankerAnimate) {
                    appData.showTimesCoin = true;
                }
            }
        }
    },
    updateAllPlayerStatus: function() {
        for (var i = 0; i < appData.player.length; i++) {
            if (appData.player[i].account_status == 4) {

            } else if (appData.player[i].account_status == 5) {

            } else if (appData.player[i].account_status == 6) {
                //下注
                if (appData.player[i].multiples > 0) {}
            } else if (appData.player[i].account_status == 7) {

            } else if (appData.player[i].account_status == 8) {

            }
        }
    },
    gameOver: function(winner, board, joycard, time1, time2) {
        var xiPaiTime = 0;
        var tim = 0;
        for (var userId in joycard) {
            if (joycard[userId] < 0) {
                xiPaiTime = 3000;
                appData.hasXiPai = true;
                for (var j = 0; j < appData.player.length; j++) {
                    if (userId == appData.player[j].account_id) {
                        var num = parseInt(appData.player[j].num);
                        var coin = Math.abs(joycard[userId]);
                        viewMethods.throwCoin(num, coin, 2000+tim);
                        tim += 400;
                    }
                }
            }
        }
        if (xiPaiTime === 0){
            viewMethods.gameOverEnd(winner, board, joycard, time1, time2);
        } else {
            setTimeout(function() {
                setTimeout(function() {
                    appData.hasXiPai = false;
                }, 2000);
                viewMethods.gameOverEnd(winner, board, joycard, time1, time2);
            }, xiPaiTime)
        }
    },

    gameOverEnd: function(winner, board, joycard, time1, time2) {
        for (var i = 0; i < 10; i++) {
            for (var s in board) {
                if (appData.player[i].account_id == s) {
                    appData.player[i].account_score = Math.ceil(board[s]);
                    appData.playerBoard.score[i].num = appData.player[i].num;
                    appData.playerBoard.score[i].account_id = appData.player[i].account_id;
                    appData.playerBoard.score[i].nickname = appData.player[i].nickname;
                    appData.playerBoard.score[i].account_score = appData.player[i].account_score;
                }
            }
        }

        setTimeout(function() {
            numD = [];
            for (var i = 0; i < appData.player.length; i++) {
                if (appData.player[i].is_win) {
                    if (i == 0) {
                        mp3AudioPlay("win")
                    }
                    numD.push(appData.player[i].num);
                }
            }
            setTimeout(function() {
                viewMethods.selectCoin(numD, board)
            }, 1500);

            var d = new Date(),
                str = '';
            str += d.getFullYear() + '-';
            str += d.getMonth() + 1 + '-';
            str += d.getDate() + '  ';
            str += d.getHours() + ':';
            if (d.getMinutes() >= 10)
                str += d.getMinutes();
            else
                str += "0" + d.getMinutes();
            appData.playerBoard.record = str;

            setTimeout(function() {
                viewMethods.tableReset(1);
            }, time2);
        }, time1);
    },
    gameOverNew: function(board, balance_scoreboard) {

        for (var i = 0; i < appData.playerBoard.score.length; i++) {
            appData.playerBoard.score[i].num = 0;
            appData.playerBoard.score[i].account_id = 0;
            appData.playerBoard.score[i].nickname = '';
            appData.playerBoard.score[i].account_score = 0;
            appData.playerBoard.score[i].isBigWinner = 0;
        }

        for (var i = 0; i < 10; i++) {
            for (s in board) {
                if (appData.player[i].account_id == s) {
                    appData.player[i].account_score = Math.ceil(board[s]);
                    appData.playerBoard.score[i].num = appData.player[i].num;
                    appData.playerBoard.score[i].account_id = appData.player[i].account_id;
                    appData.playerBoard.score[i].nickname = appData.player[i].nickname;
                    appData.playerBoard.score[i].account_score = appData.player[i].account_score;
                }
            }
        }

        var d = new Date(),str = "";
        str += d.getFullYear() + "-";
        str += d.getMonth() + 1 + "-";
        str += d.getDate() + "  ";
        str += d.getHours() + ":";

        if (d.getMinutes() >= 10) {
            str += d.getMinutes();
        } else {
            str += "0" + d.getMinutes();
        }

        appData.playerBoard.room = '房间号:' + globalData.roomNumber;
        appData.playerBoard.record = str;
        appData.base_score = appData.game.base_score;

        if (balance_scoreboard != undefined && balance_scoreboard != "-1") {
            socketModule.processBalanceScoreboard(balance_scoreboard);
        }

        for (var i = 0; i < 10; i++) {
            appData.player[i].playing_status = 0;
            appData.player[i].is_win = false;
            appData.player[i].is_operation = false;
            appData.player[i].card_open = new Array();
            appData.player[i].multiples = 0;
            appData.player[i].bankerMultiples = 0;
            appData.player[i].is_bull = false;
            appData.player[i].is_showbull = false;
            appData.player[i].is_audiobull = false;
            appData.player[i].haveBet = 0;
        }
        appData.game.score = 0;
        appData.game.currentScore = 0;
        appData.game.status = 1;
        appData.showClockRobText = false;
        appData.showClockBetText = false;
        appData.showClockPutText = false;
        appData.showClockShowLottery = false;
        appData.showWinWaver = false;
    },
    showMessage: function() {
        if (appData.isAudience) {
            return false;
        }
        appData.isShowMessage = true;
        disable_scroll();

        setTimeout(function() {
            if (!appData.bScroll) {
                appData.bScroll = new BScroll(document.getElementById('message-box'), {
                    startX: 0,
                    startY: 0,
                    scrollY: true,
                    scrollX: false,
                    click: true,
                });
            } else {
                appData.bScroll.refresh();
            }
        }, 10);
    },
    hideMessage: function() {
        appData.isShowMessage = false;
        enable_scroll();
    },
    messageOn: function(num) {
        socketModule.sendBroadcastVoice(num);
        m4aAudioPlay("message" + num);
        viewMethods.messageSay(0, num);
        viewMethods.hideMessage();
    },
    messageSay: function(num1, num2) {
        appData.player[num1].messageOn = true;
        appData.player[num1].messageText = appData.message[num2].text;
        setTimeout(function() {
            appData.player[num1].messageOn = false;
        }, 2500);
    },
    closeEnd: function() {
        $(".ranking .rankBack").css("opacity", "0.7");
        $(".end").hide();
        $(".roundEndShow").hide();
        $(".ranking").hide();

        window.location.href = window.location.href + "&id=" + 10000 * Math.random();
    },
    selectCard: function(num, count) {
        appData.select = num;
        appData.ticket_count = count;
    },
    roundEnd: function() {
        for (var i = 0; i < appData.player.length; i++) {
            if (appData.player[i].online_status == 0) {
                appData.player[i].account_id = 0;
                appData.player[i].playing_status = 0;
                appData.player[i].online_status = 0;
                appData.player[i].nickname = "";
                appData.player[i].headimgurl = "";
                appData.player[i].account_score = 0;
            }
            appData.player[i].ticket_checked = 0;
        }
        chooseBigWinner();

        $(".ranking .rankBack").css("opacity", "1");
        $(".roundEndShow").show();

        setTimeout(function() {
            $(".ranking").show();
            canvas();
        }, 3500);
    },
    timeCountDown: function() {
        clearInterval(appData.game.timeLimit);
        if (appData.game.time <= 0) {
            return;
        }
        appData.game.timeLimit = setInterval(function() {
            if (appData.game.time <= 0) {
                clearInterval(appData.game.timeLimit);
                return;
            }
            appData.game.time--;
        }, 1e3);
    },
    clickRobBanker: function(multiples) {
        viewMethods.showRobBankerText();
        socketModule.sendGrabBanker();
        setTimeout(function() {
            mp3AudioPlay("audioRobBanker");
        }, 10);
    },
    clickNotRobBanker: function() {
        viewMethods.showNotRobBankerTextFnc();
        socketModule.sendNotGrabBanker();
        setTimeout(function() {
            mp3AudioPlay("audioNoBanker");
        }, 10);
    },
    showRobBankerText: function() {
        appData.showTimesCoin = false;
        appData.showRob = false;
        appData.showShowCardButton = false;
        appData.showClickShowCard = false;
        appData.showNotRobText = false;
        appData.showRobText = true;
        appData.showBankerCoinText = false;
    },
    showNotRobBankerTextFnc: function() {
        appData.showTimesCoin = false;
        appData.showRob = false;
        appData.showShowCardButton = false;
        appData.showClickShowCard = false;
        appData.showNotRobText = true;
        appData.showRobText = false;
        appData.showBankerCoinText = false;
    },
    playerTimeCountDown: function() {
        clearInterval(appData.game.timeLimit);
        if (appData.player[currentPlayerNum].limit_time <= 0 || currentPlayerNum < 0) {
            return;
        }
        appData.game.timeLimit = setInterval(function() {
            if (appData.player[currentPlayerNum].limit_time <= 0) {
                clearInterval(appData.game.timeLimit);
                return;
            }
            appData.player[currentPlayerNum].limit_time--;
        }, 1e3);
    },
    tableReset: function(type) {
        for (var i = 0; i < 10; i++) {
            if (appData.player[i].account_status > 1 && type == 1 && appData.player[i].account_status != 8) {
                appData.player[i].account_status = 1;
            }

            appData.player[i].playing_status = 0;
            appData.player[i].is_win = false;
            appData.player[i].is_operation = false;
            if (appData.ruleInfo.banker_mode == 2 && appData.game.round > 1) {

            } else {
                if (appData.ruleInfo.banker_mode == 3 && appData.game.round > 1) {

                } else {
                    appData.player[i].is_banker = false;
                }
            }
        }

        appData.game.can_open = 0;
        appData.game.score = 0;
        appData.game.currentScore = 0;
        appData.game.status = 1;

        $(".cards").removeClass("card-flipped");
        $(".scoresArea").empty();
        $(".chip-type").removeClass("chip-active");
        appData.currentChip = 0;
        if(appData.game.autoReady && !appData.isAudience && appData.game.round >0&&appData.game.round < appData.game.total_num){
            appData.player[0].is_operation = true;
        }
    },
    throwCoin: function(num, score, tim) {
        if (tim) {
            setTimeout(function() {
                $(".scoresArea").append("<div class='coin coin" + num + " coinType" + score + "' ></div>");
                $(".coin" + num).velocity({ top: (per * 140 - 28) * Math.random(), left: (per * 140 - 28) * Math.random() }, {
                    duration: 300,
                    complete: function() {
                        $(".coin").removeClass("coin" + num);
                    }
                });
            },tim)
            return false;
        }
        if (num == 0) {
            $(".scoresArea").append("<div class='coin coinTypeBg' style='top:" + (per * 140 - 28) * Math.random() + "px;left:" + (per * 140 - 28) * Math.random() + "px;' >" +  score + "</div>");
            return 0;
        }

        if(score == 50 || score == 100 || score == 200){
            $(".scoresArea").append("<div class='coin coin" + num + "  coinTypeBg'>" +  score + "</div>");
        } else {
            $(".scoresArea").append("<div class='coin coin" + num + " coinType" + score + "' ></div>");
        }

        $(".coin" + num).velocity({ top: (per * 140 - 28) * Math.random(), left: (per * 140 - 28) * Math.random() }, {
            duration: 300,
            complete: function() {
                $(".coin").removeClass("coin" + num);
            }
        });
    },
    selectCoin: function(arr, board) {
        var len = arr.length;
        var positionArr = [
            {
                top: 280,
                left: 40,
            },
            {
                top: 70,
                left: 160,
            },
            {
                top: -20,
                left: 160,
            },
            {
                top: -60,
                left: 40,
            },
            {
                top: -20,
                left: -80,
            },
            {
                top: 70,
                left: -80,
            }
        ];
        var nodes = $(".coin");
        for (var i = 0, length = nodes.length; i < length; i++){
            nodes.eq(i).addClass('coin' + arr[i%len]);
        }
        for (var i = 0, length = arr.length; i < length; i++) {
            $('.coin' + arr[i]).velocity({ top: positionArr[arr[i]-1].top, left: positionArr[arr[i]-1].left});
        }
    },
    playerPK: function(num1, num2) {
        $(".pk1").css("left", "-60%");
        $(".pk2").css("right", "-60%");
        $(".playerPK .quitBack").hide();
        $(".playerPK .background").attr("src", globalData.imageUrl + "files/images/flower/comB.png");

        if (num1 == 0) {
            if (num2 < 3) {
                appData.turn = 0;
            } else {
                appData.turn = 1;
            }
        } else {
            if (num2 < num1) {
                appData.turn = 0;
            } else {
                appData.turn = 1;
            }
        }

        logMessage(num1, num2);

        if (appData.turn == 0) {
            appData.pk1.nickname = appData.player[num1].nickname;
            appData.pk1.headimgurl = appData.player[num1].headimgurl;
            appData.pk1.account_score = appData.player[num1].account_score;
            appData.pk1.account_status = appData.player[num1].account_status;

            appData.pk2.nickname = appData.player[num2].nickname;
            appData.pk2.headimgurl = appData.player[num2].headimgurl;
            appData.pk2.account_score = appData.player[num2].account_score;
            appData.pk2.account_status = appData.player[num2].account_status;
        } else {
            appData.pk1.nickname = appData.player[num2].nickname;
            appData.pk1.headimgurl = appData.player[num2].headimgurl;
            appData.pk1.account_score = appData.player[num2].account_score;
            appData.pk1.account_status = appData.player[num2].account_status;

            appData.pk2.nickname = appData.player[num1].nickname;
            appData.pk2.headimgurl = appData.player[num1].headimgurl;
            appData.pk2.account_score = appData.player[num1].account_score;
            appData.pk2.account_status = appData.player[num1].account_status;
        }

        appData.pk.round = 2;
        setTimeout(function() {
            m4aAudioPlay("com");

            $(".pk1").velocity({ left: 0 }, { duration: 500 });
            $(".pk2").velocity({ right: 0 }, {
                duration: 500,
                complete: function() {

                    appData.pk.round = 3;
                    setTimeout(function() {

                        appData.pk.round = 4;

                        if (appData.pk1.account_status == 7) {
                            $(".pk1 .quitBack").fadeIn();
                            $(".pk1 .background").attr("src", globalData.imageUrl + "files/images/common/player_bg.png");
                        } else {
                            $(".pk1 .background").attr("src", globalData.imageUrl + "files/images/common/player_selected.png");
                        }

                        if (appData.pk2.account_status == 7) {
                            $(".pk2 .quitBack").fadeIn();
                            $(".pk2 .background").attr("src", globalData.imageUrl + "files/images/common/player_bg.png");
                        } else {
                            $(".pk2 .background").attr("src", globalData.imageUrl + "files/images/common/player_selected.png");
                        }

                        setTimeout(function() {
                            appData.pk.round = 0;
                        }, 2000)
                    }, 800)
                }
            });
        }, 0);
    },
    quitPk: function() {
        appData.pk.round = 0;
    },
    showCoins: function(num, isShow) {
        if (isShow) {
            for (var i = 0; i < 8; i++) {
                $(".memberCoin" + num + i).show();
            }
        } else {
            for (var i = 0; i < 8; i++) {
                $(".memberCoin" + num + i).hide();
            }
        }
    },
};

var fileDealerNum = 'd_' + globalData.dealerNum;
var width = window.innerWidth;
var height = window.innerHeight;
var numD = [];
var currentPlayerNum = 0; //当前活动用户

var scoreInfo = {
    'one': '',
    'three': '',
    'week': '',
    'month': '',
    'isShow': false
};

var watchInfo = {
    'isShow': false
};
var showChips = {
    'isShow': false
}; 

var backInfo = {
    'isShow': false
};

var ruleInfo = {
    'type': 0,
    'isShow': false,
    'isShowRule': false,
    'chip_type': [],
    'ticket_count': 1,
    'disable_pk_100': 0,
    'disable_pk_men': 0,
    'upper_limit': 0,
    'rule_height': 60,
    'mode': 1,
    'compareProgress': 0,
    'seenProgress': 0,
    'raceCard': 0,
    'extraRewards': 0,
    "default_score": 2,
    'allow235GTPanther': 0,
};

var editAudioInfo = {
    isShow: false,
    backMusic: 1,
    messageMusic: 1
};

var audioInfo = {
    backMusic: 1,
    messageMusic: 1
};

var joinChoose = {
    'isShow': false
};

if (localStorage.backMusic) {
    editAudioInfo.backMusic = localStorage.backMusic;
    audioInfo.backMusic = localStorage.backMusic;
} else {
    localStorage.backMusic = 1;
}

if (localStorage.messageMusic) {
    editAudioInfo.messageMusic = localStorage.messageMusic;
    audioInfo.messageMusic = localStorage.messageMusic;
} else {
    localStorage.messageMusic = 1;
}

var scoreList1 = [4, 8, 16, 20];
var scoreList2 = [2, 4, 8, 10];
var appData = {
    storeList: [],
    isAudience: false,
    chipAll: [],
    audiences: [],
    chipsLists: [],
    prizeArea: 0,
    openSpeed: 4,
    upperLimit: false,
    isShowErweima: false,
    roomStatus: globalData.roomStatus,
    scoreList1: scoreList1,
    scoreList2: scoreList2,
    chipType: '10、20、30、50、100',
    currentChip: 0,
    'width': window.innerWidth,
    'height': window.innerHeight,
    'roomCard': Math.ceil(globalData.card),
    'gameTitle': '暗宝',
    'is_connect': false,
    'player': [],
    'scoreboard': '',
    'activity': [],
    'isShowAlert': false,
    'isShowMessage': false,
    'isBackHome': false,
    'alertType': 0,
    'alertText': '',
    'base_score': 0,
    'playerBoard': {
        score: [],
        round: 0,
        record: ""
    },
    'game': game,
    'roomCardInfo': [],
    'wsocket': ws,
    'connectOrNot': true,
    'socketStatus': 0,
    'heartbeat': null,
    'select': 1,
    'ticket_count': 0,
    'isDealing': false,
    bankerAnimateCount: 0,
    bankerAnimateIndex: 0,
    message: message,
    pkPeople: [],
    turn: 0,
    pk: {
        "turn": 0,
        "round": 0
    },
    pk1: {
        "nickname": "",
        "headimgurl": "",
        "account_score": 0,
        "account_status": 0
    },
    pk2: {
        "nickname": "",
        "headimgurl": "",
        "account_score": 0,
        "account_status": 0
    },
    isShowRecord: false,
    recordList: [],
    scoreInfo: scoreInfo,
    showRob: false,
    showRobText: false,
    showNotRobText: false,
    showClockRobText: false,
    showClockPutText: false,
    showBankerCoinText: false,
    showClockBetText: false,
    showWinWaver: false,
    showClockShowLottery: false,
    ruleInfo: ruleInfo,
    watchInfo: watchInfo,
    showChips: showChips,
    backInfo: backInfo,
    joinChoose: joinChoose,
    editAudioInfo: editAudioInfo,
    audioInfo: audioInfo,
    isAuthPhone: userData.isAuthPhone,
    authCardCount: userData.authCardCount,
    phone: userData.phone,
    sPhone: '',
    sAuthcode: '',
    authcodeType: 1,
    authcodeText: '发送验证码',
    authcodeTime: 60,
    phoneType: 1,
    phoneText: '绑定手机',
    isReconnect: true,
    bScroll: null
};

var resetState = function resetState() {
    appData.player = [];
    appData.playerBoard = {
        "score": [],
        "round": 0,
        "record": ""
    };

    for (var i = 0; i < 10; i++) {
        appData.player.push({
            "num": i + 1,
            "serial_num": 0,
            "account_id": 0,
            "account_status": 0,
            "playing_status": 0,
            "online_status": 0,
            "nickname": "",
            "headimgurl": "",
            "account_score": 0,
            "ticket_checked": 0,
            "is_win": false,
            "limit_time": 0,
            "chips": 0,
            "haveBet": 0,
            "current_win": 0,
            "is_operation": false,
            "is_banker": false,
            "messageOn": false,
            "messageText": "我们来血拼吧",
            "coins": []
        });

        appData.playerBoard.score.push({
            "account_id": 0,
            "nickname": "",
            "account_score": 0,
            "isBigWinner": 0
        });
    }

    for (var i = 0; i < appData.player.length; i++) {
        appData.player[i].coins = [];
        if(appData.player[i].account_id>0 && appData.player[i].account_status !=13){
            for (var j = 0; j <= 7; j++) {
                appData.player[i].coins.push("memberCoin" + appData.player[i].num + j);
            }
        }
    }

    if (appData.isAuthPhone != 1) {
        httpModule.getActivityInfo();
    }
    if (appData.roomStatus != 4) {
        httpModule.getScoreInfo();
    }
};
var resetAllPlayerData = function resetAllPlayerData() {

    for (var i = 0; i < 10; i++) {
        var obj_player = {
            "num": i + 1,
            "serial_num": i+1,
            "account_id": 0,
            "account_status": 0,
            "playing_status": 0,
            "online_status": 0,
            "nickname": "",
            "headimgurl": "",
            "account_score": 0,
            "ticket_checked": 0,
            "is_win": false,
            "is_banker": false,
            "limit_time": 0,
            "chips": 0,
            "haveBet": 0,
            "current_win": 0,
            "is_operation": false,
            "messageOn": false,
            "messageText": "我们来血拼吧",
            "coins": []
        };
        var scoreObj = {
            "account_id": 0,
            "nickname": "",
            "account_score": 0,
            "isBigWinner": 0
        };
        objUpdate(appData.playerBoard.score[i], scoreObj);
        objUpdate(appData.player[i], obj_player);
    }
    setTimeout(()=>{
        for (var i = 0; i < appData.player.length; i++) {
            appData.player[i].coins = [];
            if(appData.player[i].account_id>0 && appData.player[i].account_status !=13){
                for (var j = 0; j <= 7; j++) {
                    appData.player[i].coins.push("memberCoin" + appData.player[i].num + j);
                }
            }    
        }
    },10)
    
};
var newGame = function newGame() {
    appData.playerBoard = {
        "score": [],
        "round": 0,
        "record": ""
    };

    appData.game.round = 0;
    appData.game.status = 1;
    appData.game.score = 0;
    appData.game.currentScore = 0;
    appData.game.can_open = 0;
    appData.game.is_play = false;

    for (var i = 0; i < appData.player.length; i++) {
        appData.playerBoard.score.push({
            "account_id": 0,
            "nickname": "",
            "account_score": 0,
            "isBigWinner": 0
        });

        if (appData.player[i].online_status == 1) {
            appData.player[i].account_status = 0;
            appData.player[i].playing_status = 0;
            appData.player[i].is_win = false;
            appData.player[i].is_banker = false;
            appData.player[i].is_operation = false;
            appData.player[i].haveBet = 0;
            appData.player[i].ticket_checked = 0;
            appData.player[i].account_score = 0;
            appData.player[i].current_win = 0;
        } else {
            appData.player[i] = {
                "num": i + 1,
                "serial_num": appData.player[i].serial_num,
                "account_id": 0,
                "account_status": 0,
                "playing_status": 0,
                "online_status": 0,
                "nickname": "",
                "headimgurl": "",
                "account_score": 0,
                "is_win": false,
                "is_banker": false,
                "haveBet": 0,
                "ticket_checked": 0,
                "limit_time": 0,
                "current_win": 0,
                "is_operation": false,
            }
        }
    }
};

//WebSocket
var connectSocket = function connectSocket(url, openCallback, messageCallback, closeCallback, errorCallback) {
    ws = new WebSocket(url);
    ws.onopen = openCallback;
    ws.onmessage = messageCallback;
    ws.onclose = closeCallback;
    ws.onerror = errorCallback;
};

var wsOpenCallback = function wsOpenCallback(data) {
    logMessage('websocket is opened');
    appData.connectOrNot = true;

    if (appData.heartbeat) {
        clearInterval(appData.heartbeat);
    }
    appData.heartbeat = setInterval(function() {
        appData.socketStatus = appData.socketStatus + 1;

        if (appData.socketStatus > 2) {
            appData.connectOrNot = false;
        }
        if (appData.socketStatus > 3) {
            if (appData.isReconnect) {
                window.location.href = window.location.href + "&id=" + 10000 * Math.random();
            }
        }
        if (ws.readyState == WebSocket.OPEN) {
            ws.send('@');
        }
    }, 10000);

    socketModule.sendPrepareJoinRoom();
};

var wsMessageCallback = function wsMessageCallback(evt) {
    appData.connectOrNot = true;

    if (evt.data == '@') {
        appData.socketStatus = 0;
        return 0;
    }

    var obj;
    if (typeof evt.data === 'string') {
        obj = JSON.parse(evt.data);
    } else {
        obj =eval('(' + evt.data + ')');
    }

    if (obj.result == -201) {
        viewMethods.clickShowAlert(31, obj.result_message);
    } else if (obj.result == -202) {
        appData.isReconnect = false;
        socketModule.closeSocket();
        viewMethods.clickShowAlert(32, obj.result_message);
    } else if (obj.result == -203) {
        viewMethods.reloadView();
    }

    if (obj.result != 0) {
        if (obj.operation == wsOperation.JoinRoom) {
            if (obj.result == 1) {
                if (obj.data.alert_type == 1) {
                    viewMethods.clickShowAlert(1, obj.result_message);
                } else if (obj.data.alert_type == 2) {
                    viewMethods.clickShowAlert(2, obj.result_message);
                } else if (obj.data.alert_type == 3) {
                    viewMethods.clickShowAlert(11, obj.result_message);
                } else {
                    viewMethods.clickShowAlert(7, obj.result_message);
                }
            } else {
                viewMethods.clickShowAlert(7, obj.result_message);
            }
        } else if(obj.operation == wsOperation.ClickToLook){
            if (obj.result == -1) {
                viewMethods.clickShowAlert(1, obj.result_message);
            }
        }else if (obj.operation == wsOperation.ReadyStart) {
            viewMethods.clickShowAlert(1, obj.result_message);
        }else if (obj.operation == wsOperation.PutPrize) {
            viewMethods.clickShowAlert(1, obj.result_message);
        }else if (obj.operation == wsOperation.ChooseChip) {
            // viewMethods.clickShowAlert(1, obj.result_message);
            if(obj.result == -8) appData.upperLimit = true;
        }else if (obj.operation == wsOperation.StopBet) {
            viewMethods.clickShowAlert(1, obj.result_message);
        }else if (obj.operation == wsOperation.ShowPrize) {
            viewMethods.clickShowAlert(1, obj.result_message);
        } else if (obj.operation == wsOperation.PrepareJoinRoom) {
            if (obj.result > 0) {
                socketModule.processGameRule(obj);
            }

            if (obj.result == 1) {
                if (obj.data.alert_type == 1) {
                    viewMethods.clickShowAlert(1, obj.result_message);
                } else if (obj.data.alert_type == 2) {
                    viewMethods.clickShowAlert(2, obj.result_message);
                } else if (obj.data.alert_type == 3) {
                    viewMethods.clickShowAlert(11, obj.result_message);
                } else {
                    viewMethods.clickShowAlert(7, obj.result_message);
                }
            } else {
                viewMethods.clickShowAlert(7, obj.result_message);
            }
        }   else if (obj.operation == wsOperation.CreateRoom) {
            if (obj.result == -1) {
                window.location.href = window.location.href + "&id=" + 10000 * Math.random();
            } else if (obj.result == 1) {
                viewMethods.clickShowAlert(1, obj.result_message);
            }

        } else if (obj.operation == wsOperation.RefreshRoom) {
            window.location.href = window.location.href + "&id=" + 10000 * Math.random();
        }

        appData.player[0].is_operation = false;
    } else {
        if (obj.operation == wsOperation.PrepareJoinRoom) {
            socketModule.processPrepareJoinRoom(obj);
        } else if (obj.operation == wsOperation.Audience) {
            socketModule.processAudience(obj);
        } else if (obj.operation == wsOperation.UpdateAudienceInfo) {
            socketModule.processUpdateAudienceInfo(obj);
        } else if (obj.operation == wsOperation.JoinRoom) {
            socketModule.processJoinRoom(obj);
        } else if (obj.operation == wsOperation.RefreshRoom) {
            socketModule.processRefreshRoom(obj);
        } else if (obj.operation == wsOperation.AllGamerInfo) {
            socketModule.processAllGamerInfo(obj);
        } else if (obj.operation == wsOperation.UpdateGamerInfo) {
            socketModule.processUpdateGamerInfo(obj);
        } else if (obj.operation == wsOperation.UpdateAccountStatus) {
            socketModule.processUpdateAccountStatus(obj);
        } else if (obj.operation == wsOperation.UpdateAccountShow) {
            socketModule.processUpdateAccountShow(obj);
        } else if (obj.operation == wsOperation.UpdateAccountMultiples) {
            socketModule.processUpdateAccountMultiples(obj);
        } else if (obj.operation == wsOperation.StartLimitTime) {
            socketModule.processStartLimitTime(obj);
        } else if (obj.operation == wsOperation.CancelStartLimitTime) {
            socketModule.processCancelStartLimitTime(obj);
        } else if (obj.operation == wsOperation.GameStart) {
            socketModule.processGameStart(obj);
        } else if (obj.operation == wsOperation.NotyChooseChip) {
            socketModule.processNotyChooseChip(obj);
        } else if (obj.operation == wsOperation.ShowChips) {
            socketModule.processShowChips(obj);
        }else if (obj.operation == wsOperation.UpdateAccountScore) {
            socketModule.processUpdateAccountScore(obj);
        } else if (obj.operation == wsOperation.OpenCard) {
            socketModule.processOpenCard(obj);
        } else if (obj.operation == wsOperation.StopShow) {
            socketModule.processStopShow(obj);
        }else if (obj.operation == wsOperation.Win) {
            socketModule.processWin(obj);
        } else if (obj.operation == wsOperation.Discard) {
            socketModule.processDiscard(obj);
        } else if (obj.operation == wsOperation.BroadcastVoice) {
            socketModule.processBroadcastVoice(obj);
        } else if (obj.operation == wsOperation.CreateRoom) {
            socketModule.processCreateRoom(obj);
        } else if (obj.operation == wsOperation.StartPut) {
            //  console.log('-------StartPut' + new Date().getTime())
            socketModule.processStartPut(obj);
            // 点击不抢庄的时候打印'-------StartPut'并执行processStartPut()方法
        }else if (obj.operation == wsOperation.StartBet) {
            //  console.log('+++++++StartBet' + new Date().getTime())
            socketModule.processStartBet(obj);
            // 倒计时结束后打印'+++++++StartBet'并执行processStartBet()方法
        } else if (obj.operation == wsOperation.StartShow) {
            socketModule.processStartShow(obj);
        } else if (obj.operation == wsOperation.PkCard) {
            socketModule.processPKCard(obj);
        } else if (obj.operation == wsOperation.CardInfo) {
            socketModule.processCardInfo(obj);
        } else if (obj.operation === wsOperation.AllGameEndData){
            socketModule.processAllGameEndData(obj);
        } else if (obj.operation === wsOperation.GameEndData){
            socketModule.processGameEndData(obj);
        }
    }
}

var wsCloseCallback = function wsCloseCallback(data) {
    logMessage("websocket closed：");
    logMessage(data);
    appData.connectOrNot = false;
    reconnectSocket();
};

var wsErrorCallback = function wsErrorCallback(data) {
    logMessage("websocket onerror：");
    logMessage(data);
    appData.connectOrNot = false;
    //reconnectSocket();
};

var reconnectSocket = function reconnectSocket() {

    if (!appData.isReconnect) {
        return;
    }
    if (globalData.roomStatus == 4) {
        return;
    }
    if (ws) {
        logMessage(ws.readyState);
        if (ws.readyState == 1) { //websocket已经连接
            return;
        }
        ws = null;
    }

    logMessage('reconnectSocket');
    connectSocket(globalData.socket, wsOpenCallback, wsMessageCallback, wsCloseCallback, wsErrorCallback);
};

//音频播放
var m4aAudioPlay = function m4aAudioPlay(a) {
    if (!audioModule.audioOn) {
        return 0;
    }
    audioModule.playSound(a);
};

var mp3AudioPlay = function mp3AudioPlay(a) {
    if (!audioModule.audioOn) {
        return 0;
    }
    audioModule.playSound(a);
};

var audioModule = {
    audioOn: false,
    audioContext: null,
    audioBuffers: [],
    baseUrl: '',
    initModule: function(baseUrl) {
        this.baseUrl = baseUrl;
        this.audioBuffers = [];
        window.AudioContext = window.AudioContext || window.webkitAudioContext || window.mozAudioContext || window.msAudioContext;
        this.audioContext = new window.AudioContext();
    },
    stopSound: function(name) {
        var buffer = this.audioBuffers[name];

        if (buffer) {
            if (buffer.source) {
                buffer.source.stop(0);
                buffer.source = null;
            }
        }
    },
    playSound: function(name, isLoop) {
        if (name == "backMusic") {
            if (audioInfo.backMusic == 0) {
                return;
            }
        } else {
            if (audioInfo.messageMusic == 0) {
                return;
            }
        }
        var buffer = this.audioBuffers[name];

        if (buffer) {
            try {
                if (WeixinJSBridge != undefined) {
                    WeixinJSBridge.invoke('getNetworkType', {}, function(e) {
                        buffer.source = null;
                        buffer.source = audioModule.audioContext.createBufferSource();
                        buffer.source.buffer = buffer.buffer;
                        buffer.source.loop = false;

                        var gainNode = audioModule.audioContext.createGain();
                        if (isLoop == true) {
                            buffer.source.loop = true;
                            gainNode.gain.value = 0.7;
                        } else {
                            gainNode.gain.value = 1.0;
                        }
                        buffer.source.connect(gainNode);
                        gainNode.connect(audioModule.audioContext.destination);

                        buffer.source.start(0);
                    });
                }
            } catch (err) {
                logMessage(err);
                buffer.source.start(0);
            }
        }
    },
    initSound: function(arrayBuffer, name) {
        this.audioContext.decodeAudioData(arrayBuffer, function(buffer) {
            audioModule.audioBuffers[name] = { "name": name, "buffer": buffer, "source": null };
            if (name == "backMusic") {
                audioModule.audioOn = true;
                audioModule.playSound(name, true);
            }
        }, function(e) {
            logMessage('Error decoding file', e);
        });
    },
    loadAudioFile: function(url, name) {
        var xhr = new XMLHttpRequest();
        xhr.open('GET', url, true);
        xhr.responseType = 'arraybuffer';
        xhr.onload = function(e) {
            audioModule.initSound(xhr.response, name);
        };
        xhr.send();
    },
    loadAllAudioFile: function() {
        if (globalData.roomStatus == 4) {
            return;
        }
        if (isLoadAudioFile == true) {
            return;
        }

        isLoadAudioFile = true;
        this.loadAudioFile(this.baseUrl + 'files/audio/bull/background3.mp3', "backMusic");
        var audioUrl = ["nobanker.m4a", "robbanker.m4a", "times1.m4a", "times2.m4a", "times3.m4a", "times4.m4a", "times5.m4a", "times6.m4a", "times8.m4a", "times10.m4a", "coin.mp3", "audio1.m4a"];
        var audioName = ["audioNoBanker", "audioRobBanker","audioTimes1", "audioTimes2", "audioTimes3", "audioTimes4", "audioTimes5", "audioTimes6", "audioTimes8", "audioTimes10", "audioCoin", "audio1"];
        for (var i = 0; i < audioUrl.length; i++) {
            this.loadAudioFile(this.baseUrl + 'files/audio/bull/' + audioUrl[i], audioName[i]);
        }

        var audioMessageUrl = ["message9.m4a", "message10.m4a", "message1.m4a", "message5.m4a", "message3.m4a", "message4.m4a", "message12.m4a", "message6.m4a", "message7.m4a", "message8.m4a"];
        var audioMessageName = ["message0", "message1", "message2", "message3", "message4", "message5", "message6", "message7", "message8", "message9"];
        for (var i = 0; i < audioMessageUrl.length; i++) {
            this.loadAudioFile(this.baseUrl + 'files/audio/sound/' + audioMessageUrl[i], audioMessageName[i]);
        }
    }
};

audioModule.initModule(globalData.fileUrl);

var initView = function initView() {
    $('#app-main').width(appData.width);
    $('#app-main').height(appData.height);
    $('#table').width(appData.width);
    $('#table').height(appData.height);

    $(".ranking").css("width", appData.width * 2);
    $(".ranking").css("height", appData.width * 2 * 1.621);

    window.onload = function() {
        var divs = ['table', 'vinvite', 'valert', 'vmessage', 'vshop', 'vcreateRoom', 'endCreateRoom', 'endCreateRoomBtn'];
        var divLength = divs.length;
        for (var i = 0; i < divLength; i++) {
            var tempDiv = document.getElementById(divs[i]);
            if (tempDiv) {
                tempDiv.addEventListener('touchmove', function(event) {
                    event.preventDefault();
                }, false);
            }
        }
        var member4Top = (window.innerHeight * 0.195 - 28 - 89) / 2 + 26;
        member4Top = (member4Top / window.innerHeight) * 100;

        $('.member4').css('top', member4Top + '%');
    };
};

//Vue方法
var methods = {
    showAlert: viewMethods.clickShowAlert,
    showMessage: viewMethods.showMessage,
    closeAlert: viewMethods.clickCloseAlert,
    createRoom: viewMethods.clickCreateRoom,
    sitDown: viewMethods.clickSitDown,
    seeMyCard4: viewMethods.seeMyCard4,
    seeMyCard5: viewMethods.seeMyCard5,
    imReady: viewMethods.clickReady,
    putPrize: viewMethods.clickPutPrize,
    goPutPrize: viewMethods.goPutPrize,
    chooseChip: viewMethods.clickChooseChip,
    playBet: viewMethods.clickPlayBet,
    goShowChips: viewMethods.clickShowChips,
    stopBet: viewMethods.clickStopBet,
    openPrize: viewMethods.clickShowPrize,
    autoReady: viewMethods.autoReadyStatus,
    autoBetStatus: viewMethods.autoBetStatus,
    robBanker: viewMethods.clickRobBanker,
    showCard: viewMethods.clickShowCard,
    selectTimesCoin: viewMethods.clickSelectTimesCoin,
    hideMessage: viewMethods.hideMessage,
    closeEnd: viewMethods.closeEnd,
    messageOn: viewMethods.messageOn,
    home: viewMethods.clickHome,
    notRobBanker: viewMethods.clickNotRobBanker,
    selectCard: viewMethods.selectCard,
    quitPk: viewMethods.quitPk,
    choose: viewMethods.choose,

    showGameScore: function() {
        if (appData.roomStatus == 4) {
            return;
        }
        appData.scoreInfo.isShow = true;
    },
    cancelGameScore: function() {
        appData.scoreInfo.isShow = false;
    },
    showWatch: function () {
        if (appData.roomStatus == 4) {
            return;
        }
        appData.watchInfo.isShow = true;
    },
    cancelChips: function () {
        appData.showChips.isShow = false;
    },
    cancelWatch: function () {
        appData.watchInfo.isShow = false;
    },
    backHome: function() {
        appData.isBackHome = true;
    },
    closeBack: function () {
        appData.isBackHome = false;
    },
    cancelBackIndex: function () {
        appData.isShowAlert = false;
    },
    showGameRule: function() {
        if (appData.roomStatus == 4) {
            return;
        }
        appData.ruleInfo.isShowRule = true;
    },
    cancelGameRule: function() {
        appData.ruleInfo.isShowRule = false;
    },
    showAudioSetting: function() {
        appData.editAudioInfo.backMusic = appData.audioInfo.backMusic;
        appData.editAudioInfo.messageMusic = appData.audioInfo.messageMusic;
        appData.editAudioInfo.isShow = true;
    },
    cancelAudioSetting: function() {
        appData.editAudioInfo.isShow = false;
    },
    confirmAudioSetting: function() {
        appData.editAudioInfo.isShow = false;
        appData.audioInfo.backMusic = appData.editAudioInfo.backMusic;
        appData.audioInfo.messageMusic = appData.editAudioInfo.messageMusic;
        localStorage.backMusic = appData.editAudioInfo.backMusic;
        localStorage.messageMusic = appData.editAudioInfo.messageMusic;
        if (appData.audioInfo.backMusic == 1) {
            audioModule.stopSound('backMusic');
            audioModule.playSound('backMusic', true);
        } else {
            audioModule.stopSound('backMusic');
        }
    },
    goToWatch: function () {
        appData.watchInfo.isShow = false;
        socketModule.sendAudience();
    },
    goJoinGame: function () {
        appData.watchInfo.isShow = false;
        socketModule.sendJoinRoom();
    },
    cancelChoose: function () {
        appData.joinChoose.isShow = false;
    },
    chooseJoin: function () {
        socketModule.sendJoinRoom();
        this.cancelChoose();
    },
    chooseWatch: function() {
        socketModule.sendAudience();
        this.cancelChoose();
        appData.isShowAlert = false;
    },
    setBackMusic: function() {
        if (appData.editAudioInfo.backMusic == 0) {
            appData.editAudioInfo.backMusic = 1;
        } else {
            appData.editAudioInfo.backMusic = 0;
        }
    },
    setMessageMusic: function() {
        if (appData.editAudioInfo.messageMusic == 0) {
            appData.editAudioInfo.messageMusic = 1;
        } else {
            appData.editAudioInfo.messageMusic = 0;
        }
    },
    bindPhone: function() {
        var validPhone = checkPhone(appData.sPhone);
        var validAuthcode = checkAuthcode(appData.sAuthcode);
        if (validPhone == false) {
            viewMethods.clickShowAlert(21, '手机号码有误，请重填');
            return;
        }
        if (validAuthcode == false) {
            viewMethods.clickShowAlert(21, '验证码有误，请重填');
            return;
        }
        httpModule.bindPhone(appData.sPhone, appData.sAuthcode);
    },
    getAuthcode: function() {
        if (appData.authcodeType != 1) {
            return;
        }
        var color = $('#authcode').css('background-color');
        if (color != 'rgb(64, 112, 251)') {
            return;
        }
        var validPhone = checkPhone(appData.sPhone);
        if (validPhone == false) {
            viewMethods.clickShowAlert(21, '手机号码有误，请重填');
            return;
        }
        httpModule.getAuthcode(appData.sPhone);
    },
    phoneChangeValue: function() {
        var result = checkPhone(appData.sPhone);
        if (result) {
            $('#authcode').css('background-color', 'rgb(64,112,251)');
        } else {
            $('#authcode').css('background-color', 'lightgray');
        }
    },
    finishBindPhone: function() {
        window.location.href = window.location.href + "&id=" + 10000 * Math.random();
    },
    reloadView: function() {
        window.location.href = window.location.href + "&id=" + 1000 * Math.random();
    },
};

//手机绑定******
function checkPhone(phone) {
    var reg = new RegExp('/^1(3|4|5|7|8|9)\d{9}$/');
    return reg.test(phone);
}

function checkAuthcode(code) {
    if (code == '' || code == undefined) {
        return false;
    }

    var reg = new RegExp("^[0-9]*$");
    return reg.test(code);
}

var authcodeTimer = function authcodeTimer() {
    if (appData.authcodeTime <= 0) {
        appData.authcodeText = '发送验证码';
        appData.authcodeTime = 60;
        appData.authcodeType = 1;
        return;
    }

    appData.authcodeTime = appData.authcodeTime - 1;
    appData.authcodeText = appData.authcodeTime + 's';

    setTimeout(function() {
        authcodeTimer();
    }, 1000);
};
//******手机绑定

//Vue生命周期
var vueLife = {
    vmCreated: function() {
        logMessage('vmCreated')
        resetState();
        //reconnectSocket();
        initView();
        if (globalData.roomStatus != 4) {
            $("#loading").hide();
        }

        $(".main").show();
    },
    vmUpdated: function() {
        logMessage('vmUpdated');
    },
    vmMounted: function() {
        logMessage('vmMounted');
    },
    vmDestroyed: function() {
        logMessage('vmDestroyed');
    }
};

//Vue实例
var vm = new Vue({
    el: '#app-main',
    data: appData,
    methods: methods,
    created: vueLife.vmCreated,
    updated: vueLife.vmUpdated,
    mounted: vueLife.vmMounted,
    destroyed: vueLife.vmDestroyed
});

function preventDefaultFn(event) {
    event.preventDefault();
}

var wsctop = 0;

function disable_scroll() {
    //wsctop = $(window).scrollTop(); //记住滚动条的位置
    //$("body").addClass("modal-show");
    $('body').on('touchmove', preventDefaultFn);
}

function enable_scroll() {
    //$("body").removeClass("modal-show");
    //$(window).scrollTop(wsctop); //弹框关闭时，启动滚动条，并滚动到原来的位置
    $('body').off('touchmove', preventDefaultFn);
}

//积分榜
$(function() {
    //$(".main").css("height",window.innerWidth * 1.621);
    $(".place").css("width", per * 140);
    $(".place").css("height", per * 140);
    $(".place").css("top", per * 270);
    $(".place").css("left", per * 195);

    $(".showRanking").click(function() {
        $(".Ranking").show();
    });

    $(".hideRanking").click(function() {
        $(".Ranking").hide();
    });

    sessionStorage.isPaused = "false";

    var hidden, visibilityChange;
    if (typeof document.hidden !== "undefined") {
        hidden = "hidden";
        visibilityChange = "visibilitychange";
    } else if (typeof document.webkitHidden !== "undefined") {
        hidden = "webkitHidden";
        visibilityChange = "webkitvisibilitychange";
    }

    function handleVisibilityChange() {
        if (document[hidden]) {
            audioModule.audioOn = false;
            audioModule.stopSound("backMusic");
        } else if (sessionStorage.isPaused !== "true") {
            audioModule.audioOn = true;
            audioModule.stopSound("backMusic");
            audioModule.playSound("backMusic", true);
        }
    }
    if (typeof document.addEventListener === "undefined" || typeof hidden === "undefined") {
        alert("This demo requires a browser such as Google Chrome that supports the Page Visibility API.");
    } else {
        document.addEventListener(visibilityChange, handleVisibilityChange, false);
    }

   var checkerboardW = $("#checkerboard-area").width();
   var putPrizeW = Math.sqrt(checkerboardW*checkerboardW + checkerboardW*checkerboardW)/2;

   $(".prize-out").css({"top": "-" + (putPrizeW + 3) + "px"});
   $(".prize-in").css({"bottom": "-" + (putPrizeW + 3) + "px"});
   $(".prize-serpent").css({"left": "-" + (putPrizeW + 3) + "px"});
   $(".prize-tiger").css({"right": "-" + (putPrizeW + 3) + "px"});
});

function canvas() {
    var target = document.getElementById("ranking");
    html2canvas(target, {
        allowTaint: true,
        taintTest: false,
        onrendered: function(canvas) {
            canvas.id = "mycanvas";
            var dataUrl = canvas.toDataURL('image/jpeg', 0.3);
            $("#end").attr("src", dataUrl);
            $(".end").show();
            $('.ranking').hide();
            newGame();
        }
    });
}

function chooseBigWinner() {
    var length = appData.playerBoard.score.length;
    var maxScore = 1;
    for (var i = 0; i < length; i++) {
        appData.playerBoard.score.isBigWinner = 0;
        if (appData.playerBoard.score[i].account_score > maxScore) {
            maxScore = appData.playerBoard.score[i].account_score;
        }
    }
    for (var j = 0; j < length; j++) {
        if (appData.playerBoard.score[j].account_score == maxScore) {
            appData.playerBoard.score[j].isBigWinner = 1;
        }
    }
    //对积分榜排序
    appData.playerBoard.score.sort(function(a,b){
        return b.account_score - a.account_score;
    });
}

function logMessage(message) {
    console.log(message);
}

function objUpdate(player, data) {
    for (var item in player) {
        player[item] = data[item] !== undefined ? data[item] : player[item];
    }
}

var shareContent = '';

function getShareContent() {
    shareContent = "\n筹码：";
    for (var i = 0, len = appData.ruleInfo.chip_type.length; i < len; i++){
        if(i !== 0){
            shareContent += ',';
        }
        shareContent += appData.ruleInfo.chip_type[i]/(appData.ruleInfo.chip_type[i]*2);
    }
}

if (globalData.roomStatus == 4) {
    try {
        var obj = eval('(' + globalData.scoreboard + ')');
        setTimeout(function() {
            socketModule.processLastScoreboard(obj);
        }, 0);
    } catch (error) {
        console.log(error);
        setTimeout(function() {
            socketModule.processLastScoreboard('');
        }, 0);
    }

}

var wxModule = {
    config: function() {
        wx.config({
            debug: false,
            appId: configData.appId,
            timestamp: configData.timestamp,
            nonceStr: configData.nonceStr,
            signature: configData.signature,
            jsApiList: ["onMenuShareTimeline", "onMenuShareAppMessage", "hideMenuItems"]
        });
        getShareContent();

        wx.onMenuShareTimeline({
            title: globalData.shareTitle + '(房间号:' + globalData.roomNumber + ')',
            desc: shareContent,
            link: globalData.roomUrl,
            imgUrl: globalData.imageUrl + "files/images/flower/share_icon.jpg",
            success: function() {},
            cancel: function() {}
        });
        wx.onMenuShareAppMessage({
            title: globalData.shareTitle + '(房间号:' + globalData.roomNumber + ')',
            desc: shareContent,
            link: globalData.roomUrl,
            imgUrl: globalData.imageUrl + "files/images/flower/share_icon.jpg",
            success: function() {},
            cancel: function() {}
        });
    }
};

wx.config({
    debug: false,
    appId: configData.appId,
    timestamp: configData.timestamp,
    nonceStr: configData.nonceStr,
    signature: configData.signature,
    jsApiList: ["onMenuShareTimeline", "onMenuShareAppMessage", "hideMenuItems"]
});

var isLoadAudioFile = false;

wx.ready(function() {

    audioModule.loadAllAudioFile();

    wx.hideMenuItems({
        menuList: ["menuItem:copyUrl", "menuItem:share:qq", "menuItem:share:weiboApp", "menuItem:favorite", "menuItem:share:facebook", "menuItem:share:QZone", "menuItem:refresh"]
    });

    getShareContent();

    //alert('wx.ready');

    wx.onMenuShareTimeline({
        title: globalData.shareTitle + '(房间号:' + globalData.roomNumber + ')',
        desc: shareContent,
        link: globalData.roomUrl,
        imgUrl: globalData.imageUrl + "files/images/flower/share_icon.jpg",
        success: function() {},
        cancel: function() {}
    });

    wx.onMenuShareAppMessage({
        title: globalData.shareTitle + '(房间号:' + globalData.roomNumber + ')',
        desc: shareContent,
        link: globalData.roomUrl,
        imgUrl: globalData.imageUrl + "files/images/flower/share_icon.jpg",
        success: function() {},
        cancel: function() {}
    });
});

wx.error(function(a) {});