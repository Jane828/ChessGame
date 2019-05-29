var ws;
var game = {
    "room": 0,
    "room_number": globalData.roomNumber,
    "room_url": 0,
    'score': 0,
    "status": 0,
    "time": -1,
    "round": 0,
    "total_num": 10,
    "currentScore": 0,
    "cardDeal": 0,
    'can_open': 0,
    "current_win": 0,
    "is_play": false,
    "show_card": false,
    "show_coin": false,
    "base_score": 0,
    "show_score": false,
    "show_bettext": false,
    "autoReady":false,
    "timeLimit": '',
};

var message = [
    { "num": 0, "text": "玩游戏，请先进群" },
    { "num": 1, "text": "群内游戏，切勿转发" },
    { "num": 2, "text": "别磨蹭，快点打牌" },
    { "num": 3, "text": "我出去叫人" },
    { "num": 4, "text": "你的牌好靓哇"},
    { "num": 5, "text": "我当年横扫澳门五条街"},
    { "num": 6, "text": "算你牛逼" },
    { "num": 7, "text": "别跟我抢庄" },
    { "num": 8, "text": "输得裤衩都没了" },
    { "num": 9, "text": "我给你们送温暖了" },
    { "num": 10, "text": "谢谢老板" }
];

var wsOperation = {
    JoinRoom: "JoinRoom",
    Audience: "Audience",
    UpdateAudienceInfo: "UpdateAudienceInfo",
    ReadyStart: "ReadyStart",
    PrepareJoinRoom: "PrepareJoinRoom",
    AllGamerInfo: "AllGamerInfo",
    UpdateGamerInfo: "UpdateGamerInfo",
    UpdateAccountStatus: "UpdateAccountStatus",
    StartLimitTime: "StartLimitTime",
    CancelStartLimitTime: "CancelStartLimitTime",
    GameStart: "GameStart",
    NotyChooseChip: "NotyChooseChip",
    CardInfo: "CardInfo",
    UpdateAccountScore: "UpdateAccountScore",
    OpenCard: "OpenCard",
    Win: "Win",
    BroadcastVoice: "BroadcastVoice",
    ClickToLook: "ClickToLook",
    ChooseChip: "ChooseChip",
    GrabBanker: "GrabBanker",
    PlayerMultiples: "PlayerMultiples",
    ShowCard: "ShowCard",
    UpdateAccountShow: "UpdateAccountShow",
    UpdateAccountMultiples: "UpdateAccountMultiples",
    StartBet: "StartBet",
    StartShow: "StartShow",
    RefreshRoom: "PullRoomInfo",
    ActiveRoom: "ActivateRoom",
    MyCards: "MyCards",
    GameOver: "GameOver",
    BreakRoom: "BreakRoom",
    AllGameEndData: "AllGameEndData",
    GameEndData: "GameEndData"
};

var httpModule = {
    getActivityInfo: function() {
        var postData = {"account_id": userData.accountId, "dealer_num": globalData.dealerNum, "session": globalData.session,'room_number' : globalData.roomNumber ,'game_type' : 37 };
        Vue.http.post(globalData.baseUrl + 'f/getActivityInfo', postData).then(function(response) {
            console.log(response.body);
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
                    appData.activity = bodyData.data.concat();
                    viewMethods.clickShowAlert(5, appData.activity[0].content);
                }
            } else {
                viewMethods.clickShowAlert(88, bodyData.result_message);
            }

        }, function(response) {
            console.log(response.body);
        });
    },
    getScoreInfo: function () {
        Vue.http.post(globalData.baseUrl + 'f/scoreStat', {'type':37}).then(function (response) {
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
        var data = { "dealer_num": globalData.dealerNum, "phone": phone, "session": globalData.session };

        Vue.http.post(globalData.baseUrl + 'account/getMobileSms', data).then(function(response) {
            console.log(response.body);
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
        var data = { "dealer_num": globalData.dealerNum, "phone": phone, "code": authcode, "session": globalData.session };

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
    sendGameOver: function() {
        socketModule.sendData({
            operation: wsOperation.GameOver,
            account_id: userData.accountId,
            session: globalData.session,
            data: {
                room_id: appData.game.room
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
    sendGrabBanker: function(multiples) {
        socketModule.sendData({
            operation: wsOperation.GrabBanker,
            account_id: userData.accountId,
            session: globalData.session,
            data: {
                room_id: appData.game.room,
                is_grab: "1",
                "multiples": multiples,
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
                "multiples": "1",
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
    processGameRule: function(obj) {
        if (obj.data.ticket_type) {
            appData.ruleInfo.ticket = obj.data.ticket_type;
            appData.ruleInfo.baseScore = obj.data.score_type;
            appData.ruleInfo.isJoker = obj.data.is_joker;
            appData.ruleInfo.isBj = obj.data.is_bj;
            appData.ruleInfo.banker_mode = Math.ceil(obj.data.banker_mode);
            appData.ruleInfo.banker_score = Math.ceil(obj.data.banker_score_type);
        }

        if (appData.ruleInfo.banker_mode == 1) {
            appData.ruleInfo.bankerText = '抢庄';
        } else if (appData.ruleInfo.banker_mode == 2) {
            appData.ruleInfo.bankerText = '抢庄';
        }
    },
    processPrepareJoinRoom: function(obj) {
        if (obj.data.room_status == 4) {
            viewMethods.clickShowAlert(88, obj.result_message);
            return;
        }
        appData.joinChoose.isShow = true;

        if (obj.data.ticket_type) {
            appData.ruleInfo.ticket = obj.data.ticket_type;
            appData.ruleInfo.baseScore = obj.data.score_type;
            appData.ruleInfo.isJoker = obj.data.is_joker;
            appData.ruleInfo.isBj = obj.data.is_bj;
            appData.ruleInfo.banker_mode = Math.ceil(obj.data.banker_mode);
            appData.ruleInfo.banker_score = Math.ceil(obj.data.banker_score_type);
        }

        if (appData.ruleInfo.banker_mode == 1) {
            appData.ruleInfo.bankerText = '抢庄';
        } else if (appData.ruleInfo.banker_mode == 2) {
            appData.ruleInfo.bankerText = '抢庄';
        }

        wxModule.config();

        if (obj.data.room_status == 3) {
            if (appData.isAutoActive) {
                socketModule.sendActiveRoom();
            } else {
                $('.createRoom .mainPart').css('height', '65vh');
                $('.createRoom .mainPart .blueBack').css('height', '46vh');
            }
            return;
        }

        if (obj.data.user_count != 0 && obj.data.alert_text != "") {
            appData.alertText = obj.data.alert_text;
        }
    },
    processJoinRoom: function(obj) {
        appData.roomStatus = obj.data.room_status;
        if (obj.data.room_status == 4) {
            viewMethods.clickShowAlert(88, obj.result_message);
            return false;
        }
        appData.isAudience = false;

        appData.game.room = obj.data.room_id;
        appData.game.room_url = obj.data.room_url;
        appData.game.currentScore = Math.ceil(obj.data.benchmark);
        appData.game.score = Math.ceil(obj.data.pool_score);
        appData.game.round = Math.ceil(obj.data.game_num);
        appData.game.total_num = Math.ceil(obj.data.total_num);
        appData.game.base_score = Math.ceil(obj.data.base_score);
        appData.base_score = appData.game.base_score;
        appData.canBreak = Math.ceil(obj.data.can_break);

        resetAllPlayerData();

        if (obj.data.limit_time == -1) {
            appData.game.time = Math.ceil(obj.data.limit_time);
            viewMethods.timeCountDown();
        }

        appData.player[0].serial_num = obj.data.serial_num;
        for (var i = 0; i < 9; i++) {
            if (i <= 9 - obj.data.serial_num) {
                appData.player[i].serial_num = i + Math.ceil(obj.data.serial_num);
            } else {
                appData.player[i].serial_num = i + Math.ceil(obj.data.serial_num) - 9;
            }
        }

        appData.player[0].account_status = Math.ceil(obj.data.account_status);
        appData.player[0].account_score = Math.ceil(obj.data.account_score);
        appData.player[0].nickname = userData.nickname;
        appData.player[0].headimgurl = userData.avatar;
        appData.player[0].account_id = userData.accountId;
        appData.player[0].card = obj.data.cards.concat();
        appData.player[0].card_type = obj.data.card_type;
        appData.player[0].ticket_checked = obj.data.ticket_checked;
        appData.game.status = Math.ceil(obj.data.room_status);

        if (appData.ruleInfo.banker_mode == 5) {
            if (appData.game.round == 1) {
                if (appData.player[0].account_status > 5) {
                    appData.game.cardDeal = 3;
                } else {

                }
            } else {
                if (appData.game.status == 2) {
                    appData.game.cardDeal = 3;
                }
            }
        } else {
            if (appData.game.status == 2) {
                appData.game.cardDeal = 3;
            }
        }


        appData.scoreboard = obj.data.scoreboard;
        console.log('451: resetMyAccountStatus');
        viewMethods.resetMyAccountStatus();
    },
    processAudience: function(obj) {
        appData.roomStatus = obj.data.room_status;
        if (obj.data.room_status == 4) {
            if (obj.data && obj.data.to_joinRoom == 1) {
                viewMethods.clickShowAlert(8, obj.result_message);
                socketModule.sendJoinRoom();
            }else {
                viewMethods.clickShowAlert(88, obj.result_message);
            }
            return false;
        }

        appData.isAudience = true;

        appData.game.room = obj.data.room_id;
        appData.game.room_url = obj.data.room_url;
        appData.game.currentScore = Math.ceil(obj.data.benchmark);
        appData.game.score = Math.ceil(obj.data.pool_score);
        appData.game.round = Math.ceil(obj.data.game_num);
        appData.game.total_num = Math.ceil(obj.data.total_num);
        appData.game.base_score = Math.ceil(obj.data.base_score);
        appData.base_score = appData.game.base_score;
        appData.canBreak = Math.ceil(obj.data.can_break);

        resetAllPlayerData();

        if (obj.data.limit_time == -1) {
            appData.game.time = Math.ceil(obj.data.limit_time);
            viewMethods.timeCountDown();
        }

        for (var i = 0; i < 9; i++) {
            if (i <= 9 - obj.data.seat_num) {
                appData.player[i].serial_num = i + Math.ceil(obj.data.seat_num);
            } else {
                appData.player[i].serial_num = i + Math.ceil(obj.data.seat_num) - 9;
            }
        }

        appData.game.status = Math.ceil(obj.data.room_status);

        if (appData.ruleInfo.banker_mode == 5) {
            if (appData.game.round == 1) {
                if (appData.player[0].account_status > 5) {
                    appData.game.cardDeal = 3;
                }
            } else {
                if (appData.game.status == 2) {
                    appData.game.cardDeal = 3;
                }
            }
        } else {
            if (appData.game.status == 2) {
                appData.game.cardDeal = 3;
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
                        objUpdate(appData.player[j], obj.data)
                    }
                }
            }
        } else {
            for (var i = 0, len = appData.audiences.length; i < len; i += 1) {
                if (obj.audience.account_id == appData.audiences[i].account_id){
                    appData.audiences.splice(i, 1);
                    for (var j = 0, len = appData.player.length; j < len; j += 1) { // 玩过游戏的玩家，再次加入时选择观战，其它桌面玩家显示该玩家的状态为在线
                        if (obj.data && appData.player[j].account_id == obj.data.account_id){
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

        for (var i = 0; i < 9; i++) {
            if (i <= 9 - obj.data.serial_num) {
                appData.player[i].serial_num = i + Math.ceil(obj.data.serial_num);
            } else {
                appData.player[i].serial_num = i + Math.ceil(obj.data.serial_num) - 9;
            }
        }

        appData.player[0].account_status = Math.ceil(obj.data.account_status);
        appData.player[0].account_score = Math.ceil(obj.data.account_score);
        appData.player[0].nickname = userData.nickname;
        appData.player[0].headimgurl = userData.avatar;
        appData.player[0].account_id = userData.accountId;
        appData.player[0].card = obj.data.cards.concat();
        appData.player[0].card_type = obj.data.card_type;
        appData.player[0].ticket_checked = obj.data.ticket_checked;

        if (appData.ruleInfo.banker_mode == 5) {
            if (appData.game.round == 1) {
                if (appData.player[0].account_status > 5) {
                    appData.game.cardDeal = 3;
                } else {

                }
            } else {
                if (appData.game.status == 2) {
                    appData.game.cardDeal = 3;
                }
            }
        } else {
            if (appData.game.status == 2) {
                appData.game.cardDeal = 3;
            }
        }

        for (var i = 0; i < 9; i++) {
            for (var j = 0; j < obj.all_gamer_info.length; j++) {
                if (appData.player[i].serial_num == obj.all_gamer_info[j].serial_num) {
                    appData.player[i].nickname = obj.all_gamer_info[j].nickname;
                    appData.player[i].headimgurl = obj.all_gamer_info[j].headimgurl;
                    appData.player[i].account_id = obj.all_gamer_info[j].account_id;
                    appData.player[i].account_score = Math.ceil(obj.all_gamer_info[j].account_score);
                    appData.player[i].account_status = Math.ceil(obj.all_gamer_info[j].account_status);
                    appData.player[i].online_status = Math.ceil(obj.all_gamer_info[j].online_status);
                    appData.player[i].ticket_checked = obj.all_gamer_info[j].ticket_checked;
                    appData.player[i].multiples = obj.all_gamer_info[j].multiples;
                    appData.player[i].bankerMultiples = obj.all_gamer_info[j].banker_multiples;
                    appData.player[i].card = obj.all_gamer_info[j].cards.concat();
                    appData.player[i].card_type = obj.all_gamer_info[j].card_type;
                    appData.player[i].is_showbull = false;
                    if (obj.all_gamer_info[j].is_banker == 1) {
                        appData.player[i].is_banker = true;
                        appData.bankerAccountId = obj.all_gamer_info[j].account_id;
                        appData.bankerPlayer = appData.player[i];
                    } else {
                        appData.player[i].is_banker = false;
                    }
                    if (appData.player[i].account_status == 8) {
                        appData.player[i].is_showCard = true;
                    }
                }
            }
        }

        if (appData.player[0].account_status >= 7) {
            if (!appData.isAudience) {
                appData.player[0].is_showCard = true;
            } else if (appData.player[0].account_status == 8) {
                appData.player[0].is_showCard = true;
            }
        }

        if (appData.player[0].account_status > 2) {
            setTimeout(function() {
                if (appData.ruleInfo.banker_mode == 5 && appData.game.round == 1) {

                } else if(!appData.isAudience || appData.player[0].account_status == 8) {
                    appData.player[0].is_showCard = true;
                }

            }, 500);
        }
        if (appData.player[0].account_status == 3) {

            if (appData.ruleInfo.banker_mode == 5 && appData.game.round == 1) {

            } else {
                appData.showClockRobText = true;
            }
            setTimeout(function() {
                appData.showRob = true;
            }, 500);
        }
        if (appData.player[0].account_status == 6) {
            appData.showClockBetText = true;
            if (appData.player[0].is_banker == true) {
                appData.showRob = false;
                appData.showRobText = false;
                appData.showNotRobBankerText = false;
                appData.showShowCardButton = false;
                appData.showClickShowCard = false;
                appData.showBankerCoinText = true;
                appData.showTimesCoin = false;
            } else {
                appData.showRob = false;
                appData.showRobText = false;
                appData.showNotRobBankerText = false;
                appData.showShowCardButton = false;
                appData.showClickShowCard = false;
                appData.showBankerCoinText = false;
                appData.showTimesCoin = true;
            }
        }

        if (appData.player[0].account_status == 6) {
            console.log('~~~~~~~~~~~~~~~~~~~~~~~');
            appData.isFinishBankerAnimate = true;
        }

        console.log('723: resetMyAccountStatus');
        viewMethods.resetMyAccountStatus();
        viewMethods.updateAllPlayerStatus();

        if (appData.player[0].account_status > 2 && appData.player[0].account_status < 7 && appData.ruleInfo.banker_mode == 2) {
            viewMethods.seeMyCard();
        }
    },
    processStartShow: function(obj) {
        var delay = 0;
        if (appData.ruleInfo.banker_mode == 4) {
            delay = 1200;
        }

        setTimeout(function() {
            for (var i = 0; i < 9; i++) {
                for (var j = 0; j < obj.data.length; j++) {
                    if (appData.player[i].account_id == obj.data[j].account_id) {
                        appData.player[i].multiples = obj.data[j].multiples;
                        appData.player[i].account_status = Math.ceil(obj.data[j].account_status);
                        appData.player[i].online_status = Math.ceil(obj.data[j].online_status);
                        appData.player[i].card = obj.data[j].cards.concat();
                        appData.player[i].card_type = obj.data[j].card_type;
                        appData.player[i].limit_time = obj.data[j].limit_time;
                    }
                }
            }
            appData.showClockBetText = false;
            appData.showClockRobText = false;
            appData.showClockShowCard = true;
            console.log('581: resetMyAccountStatus');
            viewMethods.resetMyAccountStatus();
            viewMethods.updateAllPlayerStatus();

            appData.game.time = Math.ceil(obj.limit_time);
            viewMethods.timeCountDown();
        }, delay);

    },
    processMyCards: function(obj) {
        if (appData.ruleInfo.banker_mode == 2) {
            if (appData.player[0].account_id == obj.data.account_id) {
                appData.player[0].card = obj.data.cards.concat();
            }
            viewMethods.seeMyCard();
        }
    },
    processBreakRoom: function(obj) {
        appData.breakData = obj;

        if (appData.ruleInfo.banker_mode != 5) {
            return;
        }

        if (appData.game.round == appData.game.total_num) {
            return;
        }

        if (obj == null || obj == undefined) {
            appData.overType = 2;
            viewMethods.clickShowAlert(9, '庄家分数不足，提前下庄，点击确定查看结算');
            return;
        }

        if (obj.data.type == 1) {
            if (appData.player[0].is_banker) {
                viewMethods.clickCloseAlert();
                if (appData.breakData != null && appData.breakData != undefined) {
                    viewMethods.gameOverNew(appData.breakData.data.score_board, appData.breakData.data.balance_scoreboard);
                }
                chooseBigWinner();
                $(".ranking .rankBack").css("opacity", "1");
                $(".roundEndShow").show();

                $(".ranking").show();
                canvas();
            } else {
                appData.overType = 1;
                viewMethods.clickShowAlert(9, '庄家主动下庄,点击确定查看结算');
            }

        } else {
            appData.overType = obj.data.type;
            // return;
            // viewMethods.clickShowAlert(9, '庄家分数不足，点击确定查看结算');
        }
    },
    processStartBet: function(obj) {
        var delay = 0;
        if (appData.ruleInfo.banker_mode == 3) {
            delay = 1500;
        }

        if (appData.ruleInfo.banker_mode == 5 && appData.game.round > 1) {
            delay = 1200;
        }


        if (appData.game.round == 1 && appData.ruleInfo.banker_mode == 5) {
            //viewMethods.reDeal();
        }

        setTimeout(function() {
            for (var i = 0; i < 9; i++) {
                for (var j = 0; j < obj.data.length; j++) {
                    if (appData.player[i].account_id == obj.data[j].account_id) {
                        appData.player[i].account_status = Math.ceil(obj.data[j].account_status);
                        appData.player[i].online_status = Math.ceil(obj.data[j].online_status);
                        appData.player[i].limit_time = Math.ceil(obj.data[j].limit_time);
                        appData.player[i].multiples = 0;
                        if (obj.data[j].is_banker == 1) {
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
            appData.showClockBetText = false;
            appData.showClockRobText = false;
            appData.showClockShowCard = false;
            appData.bankerAnimateIndex = 0;

            appData.game.time = -1;

            if (appData.ruleInfo.banker_mode == 5 && appData.game.round > 1) {
                viewMethods.robBankerWithoutAnimate(Math.ceil(obj.limit_time));
            } else {
                if (appData.ruleInfo.banker_mode == 3 && appData.game.round > 1) {
                    viewMethods.robBankerWithoutAnimate(Math.ceil(obj.limit_time));
                } else {
                    viewMethods.clearBanker();
                    viewMethods.robBankerAnimate(obj);
                }
            }

        }, delay);

    },
    processAllGamerInfo: function(obj) {
        appData.audiences = obj.audience;

        appData.game.show_card = true;
        appData.game.show_coin = true;
        appData.clickCard2 = false;

        for (var i = 0; i < 9; i++) {
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
                    appData.player[i].multiples = obj.data[j].multiples;
                    appData.player[i].bankerMultiples = obj.data[j].banker_multiples;
                    appData.player[i].card_type = obj.data[j].card_type;
                    appData.player[i].is_showbull = false;
                    if (obj.data[j].cards.length > 0) {
                        appData.player[i].card = obj.data[j].cards.concat();
                    }
                    if (obj.data[j].is_banker == 1) {
                        appData.player[i].is_banker = true;
                        appData.bankerAccountId = obj.data[j].account_id;
                        appData.bankerPlayer = appData.player[i];
                    } else {
                        appData.player[i].is_banker = false;
                    }
                    if (appData.player[i].account_status == 8) {
                        appData.player[i].is_showCard = true;
                    }
                    isInGame = true;
                    break;
                }
            }
            if (!isInGame) {
                var play_copy = {
                    num: i + 1,
                    serial_num: appData.player[i].serial_num,
                    account_id: 0,
                    account_status: 0,
                    playing_status: 0,
                    online_status: 0,
                    nickname: "",
                    headimgurl: "",
                    account_score: 0,
                    ticket_checked: 0,
                    is_win: false,
                    win_type: 0,
                    limit_time: 0,
                    is_operation: false,
                    win_show: false,
                    card: new Array(),
                    'is_showCard': false,
                    is_pk: false,
                    is_readyPK: false,
                    card_type: 0,
                    is_banker: false,
                    multiples: 0,
                    bankerMultiples: 0,
                    timesImg: "",
                    bankerTimesImg: "",
                    robImg: "",
                    bullImg: "",
                    single_score: 0,
                    messageOn: false,
                    is_showbull: false,
                    is_audiobull: false,
                    messageText: "我们来血拼吧",
                    coins: []
                };
                objUpdate(appData.player[i], play_copy)
            }
        }
        if (appData.player[0].account_status >= 7) {
            if (!appData.isAudience) {
                appData.player[0].is_showCard = true;
            } else if (appData.player[0].account_status == 8) {
                appData.player[0].is_showCard = true;
            }
        }
        if (appData.scoreboard != "") {
            for (var i = 0; i < 9; i++) {
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
        if (appData.player[0].account_status > 2) {
            setTimeout(function() {
                if (appData.ruleInfo.banker_mode == 5 && appData.game.round == 1) {

                } else {
                    appData.player[0].is_showCard = true;
                }

            }, 500);
        }
        if (appData.player[0].account_status == 3) {

            if (appData.ruleInfo.banker_mode == 5 && appData.game.round == 1) {

            } else {
                appData.showClockRobText = true;
            }
            setTimeout(function() {
                appData.showRob = true;
            }, 500);
        }
        if (appData.player[0].account_status == 6) {
            appData.showClockBetText = true;
            if (appData.player[0].is_banker == true) {
                appData.showRob = false;
                appData.showRobText = false;
                appData.showNotRobBankerText = false;
                appData.showShowCardButton = false;
                appData.showClickShowCard = false;
                appData.showBankerCoinText = true;
                appData.showTimesCoin = false;
            } else {
                appData.showRob = false;
                appData.showRobText = false;
                appData.showNotRobBankerText = false;
                appData.showShowCardButton = false;
                appData.showClickShowCard = false;
                appData.showBankerCoinText = false;
                appData.showTimesCoin = true;
            }
        }

        if (appData.player[0].account_status == 6) {
            console.log('~~~~~~~~~~~~~~~~~~~~~~~');
            appData.isFinishBankerAnimate = true;
        }

        console.log('723: resetMyAccountStatus');
        viewMethods.resetMyAccountStatus();
        viewMethods.updateAllPlayerStatus();

        if (appData.player[0].account_status > 2 && appData.player[0].account_status < 7 && appData.ruleInfo.banker_mode == 2) {
            viewMethods.seeMyCard();
        }
    },
    processUpdateGamerInfo: function(obj) {
        for (var i = 0, len = appData.audiences.length; i < len; i += 1) {
            if (obj.data.account_id == appData.audiences[i].account_id) {
                appData.audiences.splice(i, 1);
                break;
            }
        }
        for (var i = 0; i < 9; i++) {
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
            for (var j = 0; j <= 7; j++) {
                appData.player[i].coins.push("memberCoin" + appData.player[i].num + j);
            }
        }
    },
    processUpdateAccountStatus: function(obj) {

        for (var i = 0; i < 9; i++) {
            if (appData.player[i].account_id == obj.data.account_id) {

                if (appData.ruleInfo.banker_mode == 2 && obj.data.account_status == 5) {
                	appData.player[i].bankerMultiples = obj.data.banker_multiples;
                }

                if (appData.player[i].account_status >= 8) {
                    appData.player[i].online_status = obj.data.online_status;
                    return;
                }

                if (obj.data.online_status == 1) {
                    appData.player[i].account_status = Math.ceil(obj.data.account_status);
                } else if (obj.data.online_status == 0 && appData.player[i].account_status == 0) {
                    appData.player[i].account_id = 0;
                    appData.player[i].playing_status = 0;
                    appData.player[i].online_status = 0;
                    appData.player[i].nickname = "";
                    appData.player[i].headimgurl = "";
                    appData.player[i].account_score = 0;
                } else if (obj.data.online_status == 0 && appData.player[i].account_status > 0) {
                    appData.player[i].account_status = Math.ceil(obj.data.account_status);
                    appData.player[i].online_status = 0;
                } else {
                    console.log("~~~~~~~!!!!!!" + obj);
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
                console.log('797: resetMyAccountStatus');
                viewMethods.resetMyAccountStatus();
                viewMethods.updateAllPlayerStatus();
            }, 3e3);
        } else {
            console.log('802: resetMyAccountStatus');
            viewMethods.resetMyAccountStatus();
            viewMethods.updateAllPlayerStatus();
        }
    },
    processUpdateAccountShow: function(obj) {
        for (var i = 0; i < appData.player.length; i++) {
            if (appData.player[i].account_id == obj.data.account_id) {
                appData.player[i].card_type = obj.data.card_type;
                appData.player[i].card = obj.data.cards.concat();
                appData.player[i].cards = obj.data.cards.concat();
                appData.player[i].account_status = 8;
                if (appData.player[i].is_audiobull == false && appData.player[i].account_status >= 8) {
                    audio = "audioPoint" + appData.player[i].card_type;
                    setTimeout(function() {
                        mp3AudioPlay(audio);
                    }, 100);
                    appData.player[i].is_audiobull = true;
                }
                break;
            }
        }

        if (obj.data.account_id == appData.player[0].account_id && !appData.isAudience) {
            console.log('841: resetMyAccountStatus');
            viewMethods.resetMyAccountStatus();
        }

        viewMethods.updateAllPlayerStatus();
    },
    processUpdateAccountMultiples: function(obj) {
        for (var i = 0; i < appData.player.length; i++) {
            if (appData.player[i].account_id == obj.data.account_id) {
                appData.player[i].multiples = obj.data.multiples;
                if (i == 0) {
                    return;
                }
                if (appData.player[i].multiples >= 1) {
                    var multiples = appData.player[i].multiples;
                    setTimeout(function() {
                        mp3AudioPlay("audioTimes" + multiples);
                    }, 100);
                }
                break;
            }
        }

        console.log('864: resetMyAccountStatus');
        viewMethods.resetMyAccountStatus();
        viewMethods.updateAllPlayerStatus();
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
        var sTime = 0;
        $(".memberCoin").stop(true);
        appData.isFinishWinAnimate = true;
        appData.isFinishBankerAnimate = true;
        appData.game.can_open = 0;
        appData.game.cardDeal = 0;
        appData.game.currentScore = 0;
        appData.game.status = 1;
        appData.game.show_card = true;
        appData.game.score = 0;
        appData.game.time = -1;
        appData.game.is_play = true;
        appData.game.round = appData.game.round + 1;
        appData.game.round = Math.ceil(obj.game_num);
        appData.player[0].is_showCard = false;
        appData.showClockRobText = false;
        appData.showClockBetText = false;
        appData.showClockShowCard = false;
        appData.clickCard2 = false;
        appData.showClickShowCard = false;
        appData.breakData = null;

        for (var i = 0; i < 9; i++) {
            appData.player[i].is_operation = false;
            appData.player[i].is_showCard = false;
            appData.player[i].is_showbull = false;

            if (appData.ruleInfo.banker_mode == 5 && appData.game.round > 1) {

            } else {
                if (appData.ruleInfo.banker_mode == 3 && appData.game.round > 1) {

                } else {
                    appData.player[i].is_banker = false;
                }
            }

            appData.player[i].bullImg = "";

            if (appData.player[i].online_status == 0) {
                appData.player[i].account_status = 1;
            }

            for (var j = 0; j < obj.data.length; j++) {
                if (appData.player[i].account_id == obj.data[j].account_id) {
                    if (appData.player[i].ticket_checked == 0 && i == 0) {
                        if (appData.isAA == true) {
                            if (appData.ruleInfo.ticket == 2) {
                                appData.roomCard = appData.roomCard - 4;
                            } else {
                                appData.roomCard = appData.roomCard - 2;
                            }
                        }
                    }
                    appData.player[i].ticket_checked = 1;
                    appData.player[i].account_status = Math.ceil(obj.data[j].account_status);
                    appData.player[i].playing_status = Math.ceil(obj.data[j].playing_status);
                    appData.player[i].online_status = Math.ceil(obj.data[j].online_status);
                    appData.player[i].account_score = appData.player[i].account_score;
                    appData.player[i].limit_time = Math.ceil(obj.data[j].limit_time);
                    appData.game.score = appData.game.score;
                }
            }
        }

        appData.game.status = 2;

        if (appData.game.round == 1 && appData.ruleInfo.banker_mode == 5) {
            //固定庄家的第一回合
            appData.game.time = -1;
            viewMethods.resetMyAccountStatus();

            //appData.showClockRobText = true;
        } else {
            appData.game.time = Math.ceil(obj.limit_time);
            viewMethods.timeCountDown();
            viewMethods.reDeal();
        }

    },
    processBroadcastVoice: function(obj) {
        for (var i = 0; i < 9; i++) {
            if (appData.player[i].account_id == obj.data.account_id && i != 0) {
                m4aAudioPlay("message" + obj.data.voice_num);
                viewMethods.messageSay(i, obj.data.voice_num);
            }
        }
    },
    processWin: function(obj) {
        appData.game.is_play = false;
        appData.game.current_win = obj.data.win_score;
        appData.game.round = Math.ceil(obj.data.game_num);
        appData.game.total_num = Math.ceil(obj.data.total_num);
        appData.playerBoard.round = Math.ceil(obj.data.game_num);
        appData.game.show_score = false;
        appData.showClockShowCard = false;
        appData.showShowCardButton = false;
        appData.showClickShowCard = false;
        appData.showClockBetText = false;
        appData.showClockRobText = false;

        if (appData.ruleInfo.banker_mode == 3) {
            appData.bankerID = Math.ceil(obj.data.banker_id);
            appData.bankerAccountId = appData.bankerID;
            console.log(appData.bankerID);
        }

        if (appData.ruleInfo.banker_mode == 5) {
            if (appData.player[0].is_banker) {
                appData.canBreak = Math.ceil(obj.data.can_break);
            }

            if (obj.data.is_break != null || obj.data.is_break != undefined) {
                appData.isBreak = Math.ceil(obj.data.is_break);
            }
        }


        viewMethods.showMemberScore(false);

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
        appData.game.time = -1;
        viewMethods.updateAllPlayerStatus();

        setTimeout(function() {
            console.log('983: resetMyAccountStatus');
            viewMethods.resetMyAccountStatus();
        }, 200);

        if (appData.player[0].account_status == 8 && appData.player[0].is_audiobull == false) {
            var point = appData.player[0].card_type;
            setTimeout(function() {
                mp3AudioPlay("audioBull" + point);
            }, 200);

            appData.player[0].is_audiobull = true;
        }
        setTimeout(function() {
            appData.game.show_card = false;
            viewMethods.winAnimate(obj);
        }, 3e3);
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

        console.log(obj);
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
                    "num": num,
                });
            }
            //对积分榜排序
            appData.playerBoard.score.sort(function(a,b){
                return b.account_score - a.account_score;
            });

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
    processGameEndData(obj){

        obj.data.players.map((player) => {
            let cards = [];
            let cardArr = player.card.split(',');
            cardArr.map( (value) => {
                let row, col, x, y;
                if(value.length === 2){
                     row = Number(value[1]);
                     col = value[0];
                }else{
                    row = Number(value[1]+value[2]);
                    col = value[0];
                }
                console.log(row)
                y = -storeMethods.setPosition(col)*30;
                if(col==='X'){
                    x = -(row+1)*25;
                }else if(col === 'Y'){
                    x = -(row)*25;
                }else{
                    x = -(row-1)*25;
                }
                cards.push({
                    x:x,
                    y:y
                })
            })
            player.cards = cards;
        })
        appData.storeList.push(obj.data);
    },
    processAllGameEndData(obj){
        if(obj.data.length !== 0){
            obj.data.map((round) => {
                round.players.map((player) => {
                    let cards = [];
                    let cardArr = player.card.split(',');
                    cardArr.map( (value) => {
                        let row, col, x, y;
                        if(value.length === 2){
                            row = Number(value[1]);
                            col = value[0];
                        }else{
                            row = Number(value[1]+value[2]);
                            col = value[0];
                        }
                        console.log(typeof row);
                        console.log(typeof col);
                        y = -storeMethods.setPosition(col)*30;
                        if(col==='X'){
                            x = -(row+1)*25;
                        }else if(col === 'Y'){
                            x = -(row)*25;
                        }else{
                            x = -(row-1)*25;
                        }
                        cards.push({
                            x:x,
                            y:y
                        })
                    })
                    console.log(cards)
                    player.cards = cards;
                })
            })
        }
        console.log(obj.data)
        appData.storeList = obj.data;
    },
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
            case "X":{
                return 4;
            }
            case "Y":{
                return 4;
            }
            default:{
                return '没有该牌';
            }
        }
    }
}
var viewMethods = {
    clickGameOver: function() {
        viewMethods.clickShowAlert(10, '下庄之后，将以当前战绩进行结算。是否确定下庄？');
        //socketModule.sendGameOver();
    },
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

            // $("#valert .mainPart").css('height', mainHeight + 'px');
            // $("#valert .mainPart").css('margin-top', '-' + mainHeight / 2 + 'px');
            // $("#valert .mainPart .backImg .blackImg").css('height', blackHeight + 'px');
            //$("#valert .mainPart .alertText").css('top', alertTop + 'px');
        }, 0);
    },
    clickCloseAlert: function() {
        if (appData.alertType == 22) {
            appData.isShowAlert = false;
            httpModule.getActivityInfo();
        } else {
            appData.isShowAlert = false;
        }
    },
    clickSitDown: function() {
        appData.isShowAlert = false;
        socketModule.sendJoinRoom();
    },
    clickReady: function() {
        socketModule.sendReadyStart();
        appData.player[0].is_operation = true;
    },
    autoReadyStatus: function() {
        appData.game.autoReady = !appData.game.autoReady;
    },
    reDeal: function() {
        if (appData.isDealing) {
            return;
        }
        console.log('~~~~reDeal~~~~~');
        appData.isDealing = true;
        m4aAudioPlay("audio1");
        appData.game.cardDeal = 1;
        setTimeout(function() {
            appData.game.cardDeal = 2;
            setTimeout(function() {
                appData.game.cardDeal = 3;
                setTimeout(function() {
                    console.log('1139: resetMyAccountStatus');
                    viewMethods.resetMyAccountStatus();
                    appData.player[0].is_showCard = true;
                    appData.showClockRobText = true;
                    appData.isDealing = false;
                    if (appData.ruleInfo.banker_mode == 5 && appData.game.round == 1) {
                        viewMethods.updateAllPlayerStatus();
                    }
                }, 150);
            }, 150);
        }, 150);
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
        } else if (appData.player[0].account_status == 6) {
            if (appData.player[0].is_banker == true) {
                appData.showBankerCoinText = true;
            } else {
                if (appData.isFinishBankerAnimate) {
                    appData.showTimesCoin = true;
                }
            }
        } else if (appData.player[0].account_status == 7) {
            appData.player[0].is_showCard = true;
            if (appData.clickCard4 == true && appData.clickCard5 == true) {
                appData.showShowCardButton = true;
            } else {
                appData.showClickShowCard = true;
            }
        } else if (appData.player[0].account_status == 8) {
            appData.player[0].is_showCard = true;
        }
    },
    resetShowButton: function() {
        appData.showTimesCoin = false;
        appData.showRob = false;
        appData.showShowCardButton = false;
        appData.showClickShowCard = false;
        appData.showNotRobText = false;
        appData.showRobText = false;
        appData.showBankerCoinText = false;
    },
    seeMyCard: function() {
        if (appData.ruleInfo.banker_mode == 2) { //明牌抢庄
            setTimeout(function() {
                $(".myCards .card0").addClass("card-flipped");
                $(".myCards .card1").addClass("card-flipped");
                appData.clickCard4 = true;

                setTimeout(function() {
                    if (appData.clickCard2 != true) {
                        if (appData.player[0].account_status >= 7) {
                            appData.showClickShowCard = true;
                        }
                    }

                }, 500);
            }, 1000);
        } else {
            setTimeout(function() {
                $(".myCards .card0").addClass("card-flipped");
                $(".myCards .card1").addClass("card-flipped");

                setTimeout(function() {
                    if (appData.clickCard2 != true) {
                        appData.showClickShowCard = true;
                    }

                }, 500);
            }, 350);
        }

    },
    seeMyCard2: function() {
        if (appData.isAudience) {
            return false;
        }
        if (appData.player[0].account_status >= 7) {
            $(".myCards .card2").addClass("card-flipped");
            appData.clickCard2 = true;
            if (appData.clickCard2 == true) {
                setTimeout(function() {
                    appData.showShowCardButton = true;
                    appData.showClickShowCard = false;
                }, 100)
            }
        }
    },
    resetCardOver: function(num) {
        if (num == 1) {
            $(".myCards .card00").css("left", "33%");
            $(".myCards .card01").css("left", "51%");
            $(".myCards .card02").css("left", "69%");
        } else if (num == 2 || num == 3 || num == 4) {
            $(".cardOver .card" + num + "11").css("right", "10.5vh");
            $(".cardOver .card" + num + "21").css("right", "12.5vh");
            $(".cardOver .card" + num + "31").css("right", "14.5vh");
        } else if (num == 5) {
            $(".cardOver .card" + num + "11").css("right", "12.63vh");
            $(".cardOver .card" + num + "21").css("right", "14.63vh");
            $(".cardOver .card" + num + "31").css("right", "16.63vh");
        }  else if (num == 6) {
            $(".cardOver .card" + num + "31").css("left", "12.63vh");
            $(".cardOver .card" + num + "21").css("left", "14.63vh");
            $(".cardOver .card" + num + "11").css("left", "16.63vh");
        } else if (num == 7 || num == 8 || num == 9) {
            $(".cardOver .card" + num + "31").css("left", "11vh");
            $(".cardOver .card" + num + "21").css("left", "13vh");
            $(".cardOver .card" + num + "11").css("left", "15vh");
        }
    },
    myCardOver: function() {
        if (appData.player[0].is_showbull == true) {
            return;
        }

        viewMethods.resetCardOver(1);

        setTimeout(function() {
            $(".myCards .card00").animate({left: "33%"}, 400);
            $(".myCards .card01").animate({left: "51%"}, 400);
            $(".myCards .card02").animate({left: "69%"}, 400);
        }, 0);

        appData.player[0].is_showbull = true;
    },
    cardOver: function(num) {
        if (num <= 1 && !appData.isAudience) {
            return;
        }

        if (appData.player[num - 1].is_showbull == true) {
            return;
        }

        appData.player[num - 1].is_showbull = true;
        viewMethods.resetCardOver(num);

        setTimeout(function() {
            if (num == 2 || num == 3 || num == 4) {
                $(".cardOver .card" + num + "11").animate({right: "10.5vh"}, 250);
                $(".cardOver .card" + num + "21").animate({right: "10.5vh"}, 250);
                $(".cardOver .card" + num + "31").animate({right: "10.5vh"}, 250);
                
                setTimeout(function() {
                    $(".cardOver .card" + num).addClass("card-flipped");
                    $(".cardOver .card" + num + "11").animate({right: "10.5vh"}, 250);
                    $(".cardOver .card" + num + "21").animate({right: "12.5vh"}, 400);
                    $(".cardOver .card" + num + "31").animate({right: "14.5vh"}, 400);
                }, 250);
            } else if (num == 7 || num == 8 || num == 9) {
                $(".cardOver .card" + num + "11").animate({left: "11vh"}, 250);
                $(".cardOver .card" + num + "21").animate({left: "11vh"}, 250);
                $(".cardOver .card" + num + "31").animate({left: "11vh"}, 250);
                
                setTimeout(function() {
                    $(".cardOver .card" + num).addClass("card-flipped");
                    $(".cardOver .card" + num + "31").animate({left: "11vh"}, 400);
                    $(".cardOver .card" + num + "21").animate({left: "13vh"}, 400);
                    $(".cardOver .card" + num + "11").animate({left: "15vh"}, 400);
                }, 250);
            } else if (num == 5) {
                $(".cardOver .card" + num + "11").animate({right: "12.63vh"}, 250);
                $(".cardOver .card" + num + "21").animate({right: "12.63vh"}, 250);
                $(".cardOver .card" + num + "31").animate({right: "12.63vh"}, 250);
                
                setTimeout(function() {
                    $(".cardOver .card" + num).addClass("card-flipped");
                    $(".cardOver .card" + num + "11").animate({right: "12.63vh"}, 400);
                    $(".cardOver .card" + num + "21").animate({right: "14.63vh"}, 400);
                    $(".cardOver .card" + num + "31").animate({right: "16.63vh"}, 400);
                }, 250);
            } else if (num == 6) {
                $(".cardOver .card" + num + "11").animate({left: "12.63vh"}, 250);
                $(".cardOver .card" + num + "21").animate({left: "12.63vh"}, 250);
                $(".cardOver .card" + num + "31").animate({left: "12.63vh"}, 250);

                setTimeout(function() {
                    $(".cardOver .card" + num).addClass("card-flipped");
                    $(".cardOver .card" + num + "31").animate({left: "12.63vh"}, 400);
                    $(".cardOver .card" + num + "21").animate({left: "14.63vh"}, 400);
                    $(".cardOver .card" + num + "11").animate({left: "16.63vh"}, 400);
                }, 250);
            }
        }, 1);
    },
    gameOverNew: function(board, balance_scoreboard) {
        appData.game.show_coin = false;

        for (var i = 0; i < appData.playerBoard.score.length; i++) {
            appData.playerBoard.score[i].num = 0;
            appData.playerBoard.score[i].account_id = 0;
            appData.playerBoard.score[i].nickname = '';
            appData.playerBoard.score[i].account_score = 0;
            appData.playerBoard.score[i].isBigWinner = 0;
        }

        console.log(appData.playerBoard);

        for (var i = 0; i < 9; i++) {
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

        // appData.playerBoard.record = str + " 前" + appData.playerBoard.round + "局";

        appData.playerBoard.record = str;
        appData.base_score = appData.game.base_score;

        if (balance_scoreboard != undefined && balance_scoreboard != "-1") {
            console.log(balance_scoreboard);
            socketModule.processBalanceScoreboard(balance_scoreboard);
        }

        for (var i = 0; i < 9; i++) {
            appData.player[i].playing_status = 0;
            appData.player[i].is_win = false;
            appData.player[i].is_operation = false;
            appData.player[i].win_type = 0;
            appData.player[i].win_show = false;
            appData.player[i].card = new Array();
            appData.player[i].card_type = 0;
            appData.player[i].is_showCard = false;
            appData.player[i].is_readyPK = false;
            appData.player[i].is_pk = false;
            //appData.player[i].is_banker = false;
            appData.player[i].multiples = 0;
            appData.player[i].bankerMultiples = 0;
            appData.player[i].is_showbull = false;
            appData.player[i].is_audiobull = false;
        }
        appData.game.can_open = 0;
        appData.game.score = 0;
        appData.game.cardDeal = 0;
        appData.game.currentScore = 0;
        appData.game.status = 1;
        appData.player[0].is_showCard = false;
        appData.showClockRobText = false;
        appData.showClockBetText = false;
        appData.showClockShowCard = false;
    },
    showMessage: function() {
        if (appData.isAudience) {
            return false;
        }
        // $(".message .textPart").animate({
        // 	height: "400px"
        // });
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
        // $(".message .textPart").animate({
        // 	height: 0
        // }, function () {
        // 	appData.isShowMessage = false;
        // });
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
        return;
        // $(".ranking .rankBack").css("opacity", "0.7");
        // $(".end").hide();
        // $(".roundEndShow").hide();
        // $(".ranking").hide();
        // window.location.reload();
    },
    selectCard: function(num, count) {
        appData.select = num;
        appData.ticket_count = count;
    },
    roundEnd: function() {
        chooseBigWinner();
        $(".ranking .rankBack").css("opacity", "1");
        $(".roundEndShow").show();

        setTimeout(function() {
            $(".ranking").show();
            canvas();
        }, 2500);
    },
    updateAllPlayerStatus: function() {
        for (var i = 0; i < appData.player.length; i++) {
            if (appData.player[i].multiples > 0) {
                appData.player[i].timesImg = globalData.imageUrl + "files/images/bull/text_times" + appData.player[i].multiples + ".png";
            }

            if (appData.player[i].bankerMultiples > 0) {
                appData.player[i].bankerTimesImg = globalData.imageUrl + "files/images/bull/text_times" + appData.player[i].bankerMultiples + ".png";
            }

            if (appData.player[i].card_type >= 0) {
                var imgIndex = parseInt(appData.player[i].card_type);
                appData.player[i].bullImg = globalData.baseUrl + "files/images/sangong/point" + imgIndex + ".png";
            }

            if (appData.player[i].account_status == 4) {

                if (appData.ruleInfo.banker_mode == 5) {
                    appData.player[i].robImg = globalData.imageUrl + "files/images/bull/text_notgo.png";
                } else {
                    //不抢庄
                    appData.player[i].robImg = globalData.imageUrl + "files/images/bull/text_notrob.png";
                }
            } else if (appData.player[i].account_status == 5) {

                if (appData.ruleInfo.banker_mode == 5) {
                    appData.player[i].robImg = globalData.imageUrl + "files/images/bull/text_go.png";
                } else {
                    appData.player[i].robImg = globalData.imageUrl + "files/images/bull/text_rob.png";
                }
            } else if (appData.player[i].account_status == 6) {
                //下注
                if (appData.player[i].multiples > 0) {}
            } else if (appData.player[i].account_status == 7) {
                //未摊牌
                if (i == 0) {
                    viewMethods.seeMyCard();
                }
            } else if (appData.player[i].account_status == 8) {
                //摊牌
                if (i == 0) {
                    viewMethods.myCardOver();
                } else {
                    viewMethods.cardOver(appData.player[i].num);
                }
            }
        }
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
            } else {
                appData.game.time--;
            }
        }, 1e3);
    },
    clickRobBanker: function(multiples) {
        viewMethods.showRobBankerText();
        socketModule.sendGrabBanker(multiples);

        if (appData.ruleInfo.banker_mode == 2) {
            appData.player[0].bankerMultiples = multiples;

            if (appData.player[0].bankerMultiples > 0) {
                appData.player[0].bankerTimesImg = globalData.imageUrl + "files/images/bull/text_times" + appData.player[0].bankerMultiples + ".png";
            }
        }

        setTimeout(function() {
            mp3AudioPlay("audioRobBanker");
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
    clickNotRobBanker: function() {
        viewMethods.showNotRobBankerTextFnc();
        socketModule.sendNotGrabBanker();
        setTimeout(function() {
            mp3AudioPlay("audioNoBanker");
        }, 10);
    },
    clickSelectTimesCoin: function(times) {
        //appData.base_score = parseInt(appData.game.base_score) * parseInt(times);

        appData.player[0].multiples = times;
        appData.showTimesCoin = false;

        if (appData.player[0].multiples > 0) {
            appData.player[0].timesImg = globalData.imageUrl + "files/images/bull/text_times" + appData.player[0].multiples + ".png";
        }

        socketModule.sendPlayerMultiples(times);
        setTimeout(function() {
            mp3AudioPlay("audioTimes" + times);
        }, 50);
    },
    clickShowCard: function() {
        appData.showShowCardButton = false;
        appData.showClickShowCard = false;
        socketModule.sendShowCard();
    },
    clearBanker: function() {
        for (var i = 0; i < appData.player.length; i++) {
            appData.player[i].is_banker = false;
        }
        appData.isFinishBankerAnimate = false;
        var totalCount = appData.bankerArray.length * 4;
        appData.bankerAnimateDuration = totalCount < 24 ? parseInt(3000 / totalCount) : parseInt(5000 / totalCount);
    },
    robBankerWithoutAnimate: function(tim) {
        for (var i = 0; i < appData.player.length; i++) {
            if (appData.player[i].account_id == appData.bankerAccountId) {
                appData.player[i].is_banker = true;
                bankerNum = appData.player[i].num;
            } else {
                appData.player[i].is_banker = false;
            }

            $("#bankerAnimate" + appData.player[i].num).hide();
            $("#bankerAnimate1" + appData.player[i].num).hide();
        }

        setTimeout(function() {
            appData.game.show_coin = true;
            appData.showClockRobText = false;
            appData.showClockBetText = true;
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

        if (appData.ruleInfo.banker_mode == 5) {
            appData.showRob = false;
        }

        for (var i = 0; i < appData.bankerArray.length; i++) {
            var imgId = "#banker" + appData.bankerArray[i];
            $(imgId).hide();
        }
        var totalCount = appData.bankerArray.length * 4;
        if (appData.bankerAnimateCount >= totalCount || appData.bankerAnimateIndex < 0 || appData.bankerArray.length < 2) {
            appData.bankerAnimateCount = 0;
            appData.bankerAnimateIndex = -1;
            var imgId = "#banker" + appData.bankerAccountId;
            $(imgId).show();

            var bankerNum = '';

            for (var i = 0; i < appData.player.length; i++) {
                if (appData.player[i].account_id == appData.bankerAccountId) {
                    appData.player[i].is_banker = true;
                    bankerNum = appData.player[i].num;
                } else {
                    appData.player[i].is_banker = false;
                }

                $("#bankerAnimate" + appData.player[i].num).hide();
                $("#bankerAnimate1" + appData.player[i].num).hide();
            }

            $(imgId).hide();

            $("#bankerAnimate" + bankerNum).css({
                top: "-0.1vh",
                left: "-0.1vh",
                width: "7.46vh",
                height: "7.46vh"
            });

            $("#bankerAnimate1" + bankerNum).css({
                top: "-1vh",
                left: "-1vh",
                width: "9.26vh",
                height: "9.26vh"
            });

            $("#bankerAnimate" + bankerNum).show();
            $("#bankerAnimate1" + bankerNum).show();

            $("#bankerAnimate1" + bankerNum).animate({
                top: "-1vh",
                left: "-1vh",
                width: "9.26vh",
                height: "9.26vh",
            }, 400, function() {
                $("#bankerAnimate1" + bankerNum).animate({
                    top: "-0.1vh",
                    left: "-0.1vh",
                    width: "7.46vh",
                    height: "7.46vh"
                }, 400, function() {
                    $("#bankerAnimate1" + bankerNum).hide();
                });
            });

            $("#bankerAnimate" + bankerNum).animate({
                top: "-1.5vh",
                left: "-1.5vh",
                width: "10.26vh",
                height: "10.26vh"
            }, 400, function() {
                $("#bankerAnimate" + bankerNum).animate({
                    top: "-0.1vh",
                    left: "-0.1vh",
                    width: "7.46vh",
                    height: "7.46vh"
                }, 400, function() {
                    $("#bankerAnimate" + bankerNum).hide();

                    setTimeout(function() {
                        console.log('1803: resetMyAccountStatus');
                        appData.game.show_coin = true;
                        appData.showClockRobText = false;
                        appData.showClockBetText = true;
                        appData.isFinishBankerAnimate = true;

                        if (appData.ruleInfo.banker_mode == 5) {
                            for (var i = 0; i < obj.data.length; i++) {
                                for (var j = 0; j < appData.player.length; j++) {
                                    if (appData.player[j].account_id == obj.data[i].account_id) {
                                        appData.player[j].account_score = obj.data[i].account_score;
                                    }
                                }
                            }

                            setTimeout(function() {
                                viewMethods.reDeal();
                            }, 1000);

                            if (appData.game.round != 1) {
                                viewMethods.resetMyAccountStatus();
                                viewMethods.updateAllPlayerStatus();
                            }
                        } else {
                            viewMethods.resetMyAccountStatus();
                            viewMethods.updateAllPlayerStatus();
                        }

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
        var imgId = "#banker" + accountId;

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
        }
    },
    winAnimate: function(obj) {
        appData.isFinishWinAnimate = false;
        $(".cards").removeClass("card-flipped");
        $(".myCards").removeClass("card-flipped");
        var winnerNums = new Array();
        var loserNums = new Array();

        appData.bankerPlayerNum = appData.bankerPlayer.num;

        if (appData.ruleInfo.banker_mode == 4) {
            for (var i = 0; i < obj.data.winner_array.length; i++) {
                for (var j = 0; j < appData.player.length; j++) {
                    if (obj.data.winner_array[i].account_id == appData.player[j].account_id) {
                        appData.bankerPlayerNum = appData.player[j].num;
                        winnerNums.push(appData.player[j].num);
                    }
                }
            }
        } else {
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
        for (var i = 1; i < 10; i++) {
            viewMethods.showCoins(i, false);
        }

        //把赢家玩家金币暂时放到庄家位置
        for (var i = 0; i < winnerNums.length; i++) {
            for (var j = 0; j < 8; j++) {
                if (appData.bankerPlayerNum == 1) {
                    $(".memberCoin" + winnerNums[i] + j).css("top", "82%");
                    $(".memberCoin" + winnerNums[i] + j).css("left", coinLeft1);
                } else if (appData.bankerPlayerNum == 2) {
                    $(".memberCoin" + winnerNums[i] + j).css("top", "59%");
                    $(".memberCoin" + winnerNums[i] + j).css("left", coinLeft2);
                } else if (appData.bankerPlayerNum == 3) {
                    $(".memberCoin" + winnerNums[i] + j).css("top", "45%");
                    $(".memberCoin" + winnerNums[i] + j).css("left", coinLeft3);
                } else if (appData.bankerPlayerNum == 4) {
                    $(".memberCoin" + winnerNums[i] + j).css("top", "31%");
                    $(".memberCoin" + winnerNums[i] + j).css("left", coinLeft4);
                } else if (appData.bankerPlayerNum == 5) {
                    $(".memberCoin" + winnerNums[i] + j).css("top", "9%");
                    $(".memberCoin" + winnerNums[i] + j).css("left", coinLeft5);
                } else if (appData.bankerPlayerNum == 6) {
                    $(".memberCoin" + winnerNums[i] + j).css("top", "9%");
                    $(".memberCoin" + winnerNums[i] + j).css("left", coinLeft6);
                } else if (appData.bankerPlayerNum == 7) {
                    $(".memberCoin" + winnerNums[i] + j).css("top", "31%");
                    $(".memberCoin" + winnerNums[i] + j).css("left", coinLeft7);
                } else if (appData.bankerPlayerNum == 8) {
                    $(".memberCoin" + winnerNums[i] + j).css("top", "45%");
                    $(".memberCoin" + winnerNums[i] + j).css("left", coinLeft8);
                } else if (appData.bankerPlayerNum == 9) {
                    $(".memberCoin" + winnerNums[i] + j).css("top", "59%");
                    $(".memberCoin" + winnerNums[i] + j).css("left", coinLeft9);
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
                        top: "82%",
                        left: coinLeft1
                    }, 150 + 150 * j);
                } else if (appData.bankerPlayerNum == 2) {
                    $(".memberCoin" + loserNums[i] + j).animate({
                        top: "59%",
                        left: coinLeft2
                    }, 150 + 150 * j);
                } else if (appData.bankerPlayerNum == 3) {
                    $(".memberCoin" + loserNums[i] + j).animate({
                        top: "45%",
                        left: coinLeft3
                    }, 150 + 150 * j);
                } else if (appData.bankerPlayerNum == 4) {
                    $(".memberCoin" + loserNums[i] + j).animate({
                        top: "31%",
                        left: coinLeft4
                    }, 150 + 150 * j);
                } else if (appData.bankerPlayerNum == 5) {
                    $(".memberCoin" + loserNums[i] + j).animate({
                        top: "9%",
                        left: coinLeft5
                    }, 150 + 150 * j);
                } else if (appData.bankerPlayerNum == 6) {
                    $(".memberCoin" + loserNums[i] + j).animate({
                        top: "9%",
                        left: coinLeft6
                    }, 150 + 150 * j);
                } else if (appData.bankerPlayerNum == 7) {
                    $(".memberCoin" + loserNums[i] + j).animate({
                        top: "31%",
                        left: coinLeft7
                    }, 150 + 150 * j);
                } else if (appData.bankerPlayerNum == 8) {
                    $(".memberCoin" + loserNums[i] + j).animate({
                        top: "45%",
                        left: coinLeft8
                    }, 150 + 150 * j);
                } else if (appData.bankerPlayerNum == 9) {
                    $(".memberCoin" + loserNums[i] + j).animate({
                        top: "59%",
                        left: coinLeft9
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
                                top: "82%",
                                left: coinLeft1
                            }, 150 + 150 * j);
                        } else if (winnerNums[i] == 2) {
                            $(".memberCoin" + winnerNums[i] + j).animate({
                                top: "59%",
                                left: coinLeft2
                            }, 150 + 150 * j);
                        } else if (winnerNums[i] == 3) {
                            $(".memberCoin" + winnerNums[i] + j).animate({
                                top: "45%",
                                left: coinLeft3
                            }, 150 + 150 * j);
                        } else if (winnerNums[i] == 4) {
                            $(".memberCoin" + winnerNums[i] + j).animate({
                                top: "31%",
                                left: coinLeft4
                            }, 150 + 150 * j);
                        } else if (winnerNums[i] == 5) {
                            $(".memberCoin" + winnerNums[i] + j).animate({
                                top: "9%",
                                left: coinLeft5
                            }, 150 + 150 * j);
                        } else if (winnerNums[i] == 6) {
                            $(".memberCoin" + winnerNums[i] + j).animate({
                                top: "9%",
                                left: coinLeft6
                            }, 150 + 150 * j);
                        } else if (winnerNums[i] == 7) {
                            $(".memberCoin" + winnerNums[i] + j).animate({
                                top: "31%",
                                left: coinLeft7
                            }, 150 + 150 * j);
                        } else if (winnerNums[i] == 8) {
                            $(".memberCoin" + winnerNums[i] + j).animate({
                                top: "45%",
                                left: coinLeft8
                            }, 150 + 150 * j);
                        } else if (winnerNums[i] == 9) {
                            $(".memberCoin" + winnerNums[i] + j).animate({
                                top: "59%",
                                left: coinLeft9
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
        $("#playerCoins").hide();

        appData.game.show_score = true;

        $(".memberScoreText1").fadeIn(200);
        $(".memberScoreText2").fadeIn(200);
        $(".memberScoreText3").fadeIn(200);
        $(".memberScoreText4").fadeIn(200);
        $(".memberScoreText5").fadeIn(200);
        $(".memberScoreText6").fadeIn(200);
        $(".memberScoreText7").fadeIn(200);
        $(".memberScoreText8").fadeIn(200);
        $(".memberScoreText9").fadeIn(200, function() {

            if (appData.ruleInfo.banker_mode == 5) {
                if (appData.isBreak != 1) {
                    viewMethods.gameOverNew(obj.data.score_board, obj.data.balance_scoreboard);
                } else {
                    for (var i = 0; i < 9; i++) {
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
                $(".memberScoreText1").fadeOut("slow");
                $(".memberScoreText2").fadeOut("slow");
                $(".memberScoreText3").fadeOut("slow");
                $(".memberScoreText4").fadeOut("slow");
                $(".memberScoreText5").fadeOut("slow");
                $(".memberScoreText6").fadeOut("slow");
                $(".memberScoreText7").fadeOut("slow");
                $(".memberScoreText8").fadeOut("slow");
                $(".memberScoreText9").fadeOut("slow");

                appData.roomStatus = 1;

                if (appData.ruleInfo.banker_mode == 5 && appData.isBreak == 1) {
                    appData.overType = 2;
                    setTimeout(function() {
                        viewMethods.clickShowAlert(9, '庄家分数不足，提前下庄，点击确定查看结算');
                    }, 1000);
                } else {
                    for (var i = 0; i < 9; i++) {

                        if (appData.player[i].account_status >= 6 && ruleInfo.banker_mode != 5) {
                            appData.player[i].is_banker = false;
                            if (appData.player[i].account_id == appData.bankerID) {
                                appData.player[i].is_banker = true;
                            }
                        }
                        if (appData.player[i].account_status != 9) {
                            appData.player[i].account_status = 1;
                        }
                    }
                    setTimeout(function() {
                        if(appData.game.autoReady && appData.game.round >0&&appData.game.round < appData.game.total_num){
                            socketModule.sendReadyStart();
                            appData.player[0].is_operation = true;
                        }
                    }, 1000);
                }
            }, 2e3);

            appData.isFinishWinAnimate = true;

            if (appData.ruleInfo.banker_mode == 5) {
                if (appData.isBreak == 1) {
                    // appData.overType = 2;
                    // setTimeout(function () {
                    // 	viewMethods.clickShowAlert(9,'庄家分数不足，提前下庄，点击确定查看结算');
                    // }, 1000);
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

        });
    },
    resetCoinsPosition: function() {
        for (var i = 1; i < 10; i++) {
            for (var j = 0; j < 8; j++) {
                if (i == 1) {
                    $(".memberCoin" + i + j).css({top: "82%",left: coinLeft1});
                } else if (i == 2) {
                    $(".memberCoin" + i + j).css({top: "59%",left: coinLeft2});
                } else if (i == 3) {
                    $(".memberCoin" + i + j).css({top: "45%",left: coinLeft3});
                } else if (i == 4) {
                    $(".memberCoin" + i + j).css({top: "31%",left: coinLeft4});
                } else if (i == 5) {
                    $(".memberCoin" + i + j).css({top: "9%",left: coinLeft5});
                } else if (i == 6) {
                    $(".memberCoin" + i + j).css({top: "9%",left: coinLeft6});
                } else if (i == 7) {
                    $(".memberCoin" + i + j).css({top: "31%",left: coinLeft7});
                } else if (i == 8) {
                    $(".memberCoin" + i + j).css({top: "45%",left: coinLeft8});
                } else if (i == 9) {
                    $(".memberCoin" + i + j).css({top: "59%",left: coinLeft9});
                }
            }
        }
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
var numD = 0;
var isTimeLimitShow = false;
var isBankerWin = false;
var timesOffset = (width * 0.9 - height * 0.088 * 4 - width * 0.02 * 3) / 2;

var coinLeft1 = height * 0.045 + 'px';
var coinLeft2 = (width - height * 0.06) + 'px';
var coinLeft3 = (width - height * 0.06) + 'px';
var coinLeft4 = (width - height * 0.06) + 'px';
var coinLeft5 = (width - height * 0.18) + 'px';
var coinLeft6 = height * 0.15 + 'px';
var coinLeft7 = height * 0.045 + 'px';
var coinLeft8 = height * 0.045 + 'px';
var coinLeft9 = height * 0.045 + 'px';

var viewStyle = {
    readyButton: {
        position: 'absolute',
        top: (height * 0.11 - height * 0.0495) / 2 + 'px',
        left: (width * 0.9 - height * 0.0495 * 3.078) / 2 + 'px',
        width: height * 0.0495 * 3.078 + 'px',
        height: height * 0.0495 + 'px',
    },
    readyText: {
        position: 'absolute',
        top: '50%',
        left: '50%',
        width: '6vh',
        height: '3vh',
        'margin-top': '-1.5vh',
        'margin-left': '-3vh',
    },
    button: {
        position: 'absolute',
        top: '68%',
        left: '5%',
        width: '90%',
        height: '11vh',
        overflow: 'hidden'
    },
    rob: {
        position: 'absolute',
        top: (height * 0.11 - height * 0.0495) / 2 + 'px',
        left: (width * 0.9 - height * 0.0495 / 0.375 * 2 - 20) / 2 + 'px',
        width: height * 0.0495 / 0.375 + 'px',
        height: height * 0.0495 + 'px',
    },
    rob1: {
        position: 'absolute',
        top: '0px',
        left: '0px',
        width: height * 0.0495 / 0.375 + 'px',
        height: height * 0.0495 + 'px',
        'line-height': height * 0.0495 + 'px',
        'text-align': 'center',
        color: 'white',
        'font-size': '2.2vh',
        'font-weight': 'bold'
    },
    notRob: {
        position: 'absolute',
        top: (height * 0.11 - height * 0.0495) / 2 + 'px',
        left: (width * 0.9 - height * 0.0495 / 0.375 * 2 - 20) / 2 + height * 0.0495 / 0.375 + 20 + 'px',
        width: height * 0.0495 / 0.375 + 'px',
        height: height * 0.0495 + 'px'
    },
    notRob1: {
        position: 'absolute',
        top: '0px',
        left: '0px',
        width: height * 0.0495 / 0.375 + 'px',
        height: height * 0.0495 + 'px',
        'line-height': height * 0.0495 + 'px',
        'text-align': 'center',
        color: 'white',
        'font-size': '2.2vh',
        'font-weight': 'bold'
    },
    showCard: {
        position: 'absolute',
        top: (height * 0.11 - height * 0.0495) / 2 + 'px',
        left: (width * 0.9 - height * 0.0495 / 0.375) / 2 + 'px',
        width: height * 0.0495 / 0.375 + 'px',
        height: height * 0.0495 + 'px'
    },
    showCard1: {
        position: 'absolute',
        top: '0px',
        left: '0px',
        width: height * 0.0495 / 0.375 + 'px',
        height: height * 0.0495 + 'px',
        'line-height': height * 0.0495 + 'px',
        'text-align': 'center',
        color: 'white',
        'font-size': '2.2vh',
        'font-weight': 'bold'
    },
    times1: {
        position: 'absolute',
        top: (height * 0.11 - height * 0.088 / 2) / 2 + 'px',
        left: timesOffset + 'px',
        width: height * 0.088 + 'px',
        height: height * 0.088 / 2 + 'px',
        'line-height': height * 0.088 / 2 + 'px',
    },
    timesText: {
        position: 'absolute',
        width: height * 0.088 + 'px',
        height: height * 0.088 / 2 + 'px',
        'line-height': height * 0.088 / 2 + 'px',
        'text-align': 'center',
        color: 'white',
        'font-size': '2.2vh',
        'font-weight': 'bold'
    },
    times2: {
        position: 'absolute',
        top: (height * 0.11 - height * 0.088 / 2) / 2 + 'px',
        left: timesOffset + width * 0.02 + height * 0.088 + 'px',
        width: height * 0.088 + 'px',
        height: height * 0.088 / 2 + 'px',
        'line-height': height * 0.088 / 2 + 'px',
    },
    times3: {
        position: 'absolute',
        top: (height * 0.11 - height * 0.088 / 2) / 2 + 'px',
        left: timesOffset + width * 0.02 * 2 + height * 0.088 * 2 + 'px',
        width: height * 0.088 + 'px',
        height: height * 0.088 / 2 + 'px',
        'line-height': height * 0.088 / 2 + 'px',
    },
    times4: {
        position: 'absolute',
        top: (height * 0.11 - height * 0.088 / 2) / 2 + 'px',
        left: timesOffset + width * 0.02 * 3 + height * 0.088 * 3 + 'px',
        width: height * 0.088 + 'px',
        height: height * 0.088 / 2 + 'px',
        'line-height': height * 0.088 / 2 + 'px',
    },
    robText2: {
        position: 'absolute',
        top: (height * 0.11 - height * 0.03) / 2 + 'px',
        left: (width * 0.9 - height * 0.0557 - height * 0.03 - height * 0.005) / 2 + 'px',
        width: height * 0.0557 + 'px',
        height: height * 0.03 + 'px',
    },
    robText: {
        position: 'absolute',
        top: (height * 0.11 - height * 0.03) / 2 + 'px',
        left: (width * 0.9 - height * 0.0557) / 2 + 'px',
        width: height * 0.0557 + 'px',
        height: height * 0.03 + 'px',
    },
    robTimesText: {
        position: 'absolute',
        top: (height * 0.11 - height * 0.03) / 2 + 'px',
        left: (width * 0.9 - height * 0.0557 - height * 0.03 - height * 0.002) / 2 + height * 0.0557 + height * 0.005 + 'px',
        width: height * 0.03 + 'px',
        height: height * 0.03 + 'px',
    },
    notRobText: {
        position: 'absolute',
        top: (height * 0.11 - height * 0.03) / 2 + 'px',
        left: (width * 0.9 - height * 0.0557) / 2 + 'px',
        width: height * 0.0557 + 'px',
        height: height * 0.03 + 'px',
    },
    showCardText: {
        position: 'absolute',
        top: '10%',
        left: '10%',
        width: '80%',
        height: '11vh',
        'font-size': '2.2vh',
    },
    showCardText1: {
        position: 'absolute',
        width: '100%',
        height: '100%',
        color: 'white',
        'font-size': '2.2vh',
        'text-align': 'center',
        'line-height': '11vh',
        'font-family': 'Helvetica 微软雅黑'
    },
    coinText: {
        position: 'absolute',
        top: '10%',
        left: '10%',
        width: '80%',
        height: '11vh',
        'font-size': '2.2vh',
    },
    coinText1: {
        position: 'absolute',
        width: '100%',
        height: '100%',
        color: 'white',
        'font-size': '2.2vh',
        'text-align': 'center',
        'line-height': '11vh',
        'font-family': 'Helvetica 微软雅黑'
    }
};

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

var playRule = {
    isShow: false
};

var ruleInfo = {
    type: -1,
    isShow: false,
    'isShowRule': false,
    baseScore: 1,
    timesType: 1,
    isJoker: 0,
    isBj: 0,
    ticket: 1,
    'rule_height': '4vh',
    'banker_mode': 1,
    'banker_score': 1,
    'bankerText': '抢庄'
};

var editAudioInfo = {
    isShow: false,
    backMusic: 1,
    messageMusic: 1,
};

var audioInfo = {
    backMusic: 1,
    messageMusic: 1,
};

var joinChoose = {
    'isshow': false,
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

var appData = {
    storeList: [],
    isAudience: false,
    audiences: [],
    'viewStyle': viewStyle,
    roomStatus: globalData.roomStatus,
    'isAA': false, //是否AA房卡
    'isAutoActive': false, //是否自动激活
    'isShop': false, //是否有商城
    'width': window.innerWidth,
    'height': window.innerHeight,
    'roomCard': Math.ceil(globalData.card),
    'is_connect': false,
    'player': [],
    'scoreboard': '',
    'activity': [],
    'isShowInvite': false,
    'isShowAlert': false,
    'isShowShop': false,
    'isShowMessage': false,
    'isBackHome': false,
    'alertType': 0,
    'alertText': '',
    'showRob': false,
    'showShowCardButton': false,
    'showRobText': false,
    'showNotRobText': false,
    'showClockRobText': false,
    'showClockBetText': false,
    'showClockShowCard': false,
    'showTimesCoin': false,
    'showClickShowCard': false,
    'showBankerCoinText': false,
    'clickCard2': false,
    'base_score': 0,
    'playerBoard': {
        score: new Array(),
        round: 0,
        record: "",
        room: ""
    },
    'game': game,
    'wsocket': ws,
    'connectOrNot': true,
    'socketStatus': 0,
    'heartbeat': null,
    'select': 1,
    'ticket_count': 0,
    'isDealing': false,
    message: message,
    isShowShopLoading: false,
    bankerArray: [],
    bankerAccountId: '',
    bankerPlayer: '',
    bankerPlayerNum: -1,
    bankerAnimateCount: 0,
    bankerAnimateIndex: 0,
    lastBankerImgId: '',
    bankerAnimateDuration: 120,
    isFinishWinAnimate: false,
    isFinishBankerAnimate: false,
    isShowErweima: false,
    isShowRecord: false,
    recordList: [],
    scoreInfo: scoreInfo,
    ruleInfo: ruleInfo,
    watchInfo: watchInfo,
    joinChoose: joinChoose,
    playRule: playRule,
    "canBreak": 0,
    "overType": 1,
    "isBreak": 0,
    "breakData": '',
    'bankerID': 0,
    editAudioInfo: editAudioInfo,
    audioInfo: audioInfo,
    'isAuthPhone': userData.isAuthPhone,
    'authCardCount': userData.authCardCount,
    'phone': userData.phone,
    'sPhone': '',
    'sAuthcode': '',
    'authcodeType': 1,
    'authcodeText': '发送验证码',
    'authcodeTime': 60,
    'phoneType': 1,
    'phoneText': '绑定手机',
    isReconnect: true,
    bScroll: null
};

var resetState = function resetState() {
    appData.game.show_score = false;
    appData.game.show_bettext = false;
    appData.clickCard2 = false;

    for (var i = 0; i < 9; i++) {
        appData.player.push({
            num: i + 1,
            'serial_num': i + 1,
            'account_id': 0,
            account_status: 0,
            playing_status: 0,
            online_status: 0,
            nickname: "",
            headimgurl: "",
            account_score: 0,
            ticket_checked: 1,
            is_win: false,
            win_type: 0,
            limit_time: 0,
            is_operation: false,
            win_show: false,
            card: [],
            is_showCard: false,
            is_pk: false,
            is_readyPK: false,
            card_type: 0,
            is_banker: false,
            multiples: 0,
            bankerMultiples: 0,
            timesImg: '',
            bankerTimesImg: "",
            robImg: '',
            bullImg: '',
            single_score: 0,
            messageOn: false,
            is_showbull: false,
            is_audiobull: false,
            messageText: "我们来血拼吧",
            coins: []
        });

        appData.playerBoard.score.push({
            account_id: 0,
            nickname: "",
            account_score: 0,
            isBigWinner: 0
        });
    }

    for (var i = 0; i < appData.player.length; i++) {
        appData.player[i].coins = [];
        for (var j = 0; j <= 7; j++) {
            appData.player[i].coins.push("memberCoin" + appData.player[i].num + j);
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
    appData.player = [];

    for (var i = 0; i < 9; i++) {
        appData.player.push({
            num: i + 1,
            serial_num: i + 1,
            account_id: 0,
            account_status: 0,
            playing_status: 0,
            online_status: 0,
            nickname: "",
            headimgurl: "",
            account_score: 0,
            ticket_checked: 0,
            is_win: false,
            win_type: 0,
            limit_time: 0,
            is_operation: false,
            win_show: false,
            card: new Array(),
            is_showCard: false,
            is_pk: false,
            is_readyPK: false,
            card_type: 0,
            is_banker: false,
            multiples: 0,
            bankerMultiples: 0,
            timesImg: "",
            bankerTimesImg: "",
            robImg: "",
            bullImg: "",
            single_score: 0,
            messageOn: false,
            is_showbull: false,
            is_audiobull: false,
            messageText: "我们来血拼吧",
            coins: []
        });
    }

    for (var i = 0; i < appData.player.length; i++) {
        appData.player[i].coins = [];
        for (var j = 0; j <= 7; j++) {
            appData.player[i].coins.push("memberCoin" + appData.player[i].num + j);
        }
    }
};

var newGame = function newGame() {
    appData.playerBoard = {
        score: new Array(),
        round: 0,
        record: "",

    };

    appData.game.round = 0;
    appData.game.status = 1;
    appData.game.score = 0;
    appData.game.currentScore = 0;
    appData.game.cardDeal = 0;
    appData.game.can_open = 0;
    appData.game.current_win = 0;
    appData.game.is_play = false;
    appData.game.show_score = false;
    appData.game.show_bettext = false;
    appData.clickCard2 = false;

    for (var i = 0; i < appData.player.length; i++) {
        appData.playerBoard.score.push({
            account_id: 0,
            nickname: "",
            account_score: 0,
            isBigWinner: 0,
        });

        if (appData.player[i].online_status == 1) {
            appData.player[i].account_status = 0;
            appData.player[i].playing_status = 0;
            appData.player[i].is_win = false;
            appData.player[i].is_operation = false;
            appData.player[i].win_type = 0;
            appData.player[i].win_show = false;
            appData.player[i].card = new Array();
            appData.player[i].card_type = 0;
            appData.player[i].ticket_checked = 0;
            appData.player[i].account_score = 0;
            appData.player[i].is_showCard = false;
            appData.player[i].is_readyPK = false;
            appData.player[i].is_pk = false;
            appData.player[i].is_banker = false;
            appData.player[i].multiples = 0;
            appData.player[i].bankerMultiples = 0,
            appData.player[i].timesImg = "";
            appData.player[i].bankerTimesImg = "",
            appData.player[i].robImg = "";
            appData.player[i].bullImg = "";
            appData.player[i].single_score = 0;
            appData.player[i].num = i + 1;
            appData.player[i].is_showbull = false;
            appData.player[i].is_audiobull = false;
        } else {
            appData.player[i] = {
                num: i + 1,
                serial_num: appData.player[i].serial_num,
                account_id: 0,
                account_status: 0,
                playing_status: 0,
                online_status: 0,
                nickname: "",
                headimgurl: "",
                account_score: 0,
                is_win: false,
                win_type: 0,
                ticket_checked: 0,
                limit_time: 0,
                is_operation: false,
                win_show: false,
                card: new Array(),
                is_showCard: false,
                is_pk: false,
                is_readyPK: false,
                card_type: 0,
                is_banker: false,
                multiples: 0,
                bankerMultiples: 0,
                timesImg: "",
                bankerTimesImg: "",
                robImg: "",
                bullImg: "",
                single_score: 0,
                is_showbull: false,
                is_audiobull: false
            };
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
    console.log('websocket is opened');
    appData.connectOrNot = true;

    if (appData.heartbeat) {
        clearInterval(appData.heartbeat);
    }

    appData.heartbeat = setInterval(function() {
        appData.socketStatus = appData.socketStatus + 1;

        if (appData.socketStatus > 1) {
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

    var obj = eval('(' + evt.data + ')');
    console.log(obj);

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
            } else if (obj.result == -1) {
                viewMethods.clickShowAlert(7, obj.result_message);
            } else {
                viewMethods.clickShowAlert(7, obj.result_message);
            }
        } else if (obj.operation == wsOperation.ReadyStart) {
            if (obj.result == 1) {
                viewMethods.clickShowAlert(1, obj.result_message);
            }
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
            } else if (obj.result == -1) {
                viewMethods.clickShowAlert(7, obj.result_message);
            } else {
                viewMethods.clickShowAlert(7, obj.result_message);
            }
        }   else if (obj.operation == wsOperation.RefreshRoom) {
            window.location.href = window.location.href + "&id=" + 10000 * Math.random();
        }

        appData.player[0].is_operation = false;
    } else {
        if (obj.operation == wsOperation.PrepareJoinRoom) {
            socketModule.processPrepareJoinRoom(obj);
        } else if (obj.operation == wsOperation.JoinRoom) {
            socketModule.processJoinRoom(obj);
        } else if (obj.operation == wsOperation.Audience) {
            socketModule.processAudience(obj);
        } else if (obj.operation == wsOperation.UpdateAudienceInfo) {
            socketModule.processUpdateAudienceInfo(obj);
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
        } else if (obj.operation == wsOperation.Win) {
            socketModule.processWin(obj);
        } else if (obj.operation == wsOperation.BroadcastVoice) {
            socketModule.processBroadcastVoice(obj);
        } else if (obj.operation == wsOperation.StartBet) {
            socketModule.processStartBet(obj);
        } else if (obj.operation == wsOperation.StartShow) {
            socketModule.processStartShow(obj);
        } else if (obj.operation == wsOperation.MyCards) {
            socketModule.processMyCards(obj);
        } else if (obj.operation == wsOperation.BreakRoom) {
            socketModule.processBreakRoom(obj);
        }else if (obj.operation === wsOperation.AllGameEndData){
            socketModule.processAllGameEndData(obj);
        } else if (obj.operation === wsOperation.GameEndData){
            console.log('12234567899')
            socketModule.processGameEndData(obj);
        } else {
            console.log(obj.operation);
        }
    }
};

var wsCloseCallback = function wsCloseCallback(data) {
    console.log("websocket closed：");
    console.log(data);
    appData.connectOrNot = false;
    reconnectSocket();
};

var wsErrorCallback = function wsErrorCallback(data) {
    console.log("websocket onerror：");
    console.log(data);
};

var reconnectSocket = function reconnectSocket() {
    
    if (!appData.isReconnect) {
        return;
    }

    if (globalData.roomStatus == 4) {
        return;
    }

    if (ws) {
        console.log(ws.readyState);
        if (ws.readyState == 1) { //websocket已经连接
            return;
        }

        ws = null;
    }
    console.log('reconnectSocket');
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

            }
        }
    },
    initSound: function(arrayBuffer, name) {
        this.audioContext.decodeAudioData(arrayBuffer, function(buffer) {
            audioModule.audioBuffers[name] = {"name": name, "buffer": buffer, "source": null};

            if (name == "backMusic") {
                audioModule.audioOn = true;
                audioModule.playSound(name, true);
            }

        }, function(e) {
            console.log('Error decoding file', e);
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

        this.loadAudioFile(this.baseUrl + 'files/audio/bull9/background3.mp3', "backMusic");

        var pointUrl = ["point0.m4a", "point1.m4a", "point2.m4a", "point3.m4a", "point4.m4a", "point5.m4a", "point6.m4a", "point7.m4a", "point8.m4a", "point9.m4a", "point10.m4a", "point11.m4a", "point12.m4a", "point13.m4a", "point14.m4a", "point15.m4a", "point16.m4a"];
        var pointName = ["audioPoint0", "audioPoint1", "audioPoint2", "audioPoint3", "audioPoint4", "audioPoint5", "audioPoint6", "audioPoint7", "audioPoint8", "audioPoint9", "audioPoint10", "audioPoint11", "audioPoint12", "audioPoint13", "audioPoint14", "audioPoint15", "audioPoint16"];
        for (i = 0; i < pointUrl.length; i++) {
            this.loadAudioFile(this.baseUrl + 'files/audio/sangong/' + pointUrl[i], pointName[i]);
        }

        var audioTimesUrl = ["times1.m4a", "times2.m4a", "times3.m4a", "times4.m4a", "times5.m4a", "times8.m4a", "times10.m4a"];
        var audioTimesName = ["audioTimes1", "audioTimes2", "audioTimes3", "audioTimes4", "audioTimes5", "audioTimes8", "audioTimes10"];
        for (i = 0; i < audioTimesUrl.length; i++) {
            this.loadAudioFile(this.baseUrl + 'files/audio/sound/' + audioTimesUrl[i], audioTimesName[i]);
        }

        var audioUrl = ["nobanker.m4a", "robbanker.m4a", "coin.mp3", "audio1.m4a"];
        var audioName = ["audioNoBanker", "audioRobBanker", "audioCoin", "audio1"];
        for (var i = 0; i < audioUrl.length; i++) {
            this.loadAudioFile(this.baseUrl + 'files/audio/bull/' + audioUrl[i], audioName[i]);
        }

        var audioMessageUrl = ["message9.m4a", "message10.m4a", "message11.m4a", "message1.m4a", "message2.m4a", "message3.m4a", "message4.m4a", "message12.m4a", "message6.m4a", "message7.m4a", "message8.m4a"];
        var audioMessageName = ["message0", "message1", "message2", "message3", "message4", "message5", "message6", "message7", "message8", "message9", "message10"];
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
    };
};

//Vue方法
var methods = {
    clickGameOver: viewMethods.clickGameOver,
    showInvite: viewMethods.clickShowInvite,
    showAlert: viewMethods.clickShowAlert,
    showMessage: viewMethods.showMessage,
    closeInvite: viewMethods.clickCloseInvite,
    closeAlert: viewMethods.clickCloseAlert,
    sitDown: viewMethods.clickSitDown,
    seeMyCard2: viewMethods.seeMyCard2,
    imReady: viewMethods.clickReady,
    autoReady:viewMethods.autoReadyStatus,
    robBanker: viewMethods.clickRobBanker,
    showCard: viewMethods.clickShowCard,
    selectTimesCoin: viewMethods.clickSelectTimesCoin,
    hideMessage: viewMethods.hideMessage,
    closeEnd: viewMethods.closeEnd,
    messageOn: viewMethods.messageOn,
    home: viewMethods.clickHome,
    notRobBanker: viewMethods.clickNotRobBanker,
    selectCard: viewMethods.selectCard,
    showGameScore: function() {
        if (appData.roomStatus == 4) {
            return;
        }
        appData.scoreInfo.isShow = true;
    },
    cancelGameScore: function() {
        appData.scoreInfo.isShow = false;
    },
    showGamePlay: function() {
        if (appData.roomStatus == 4) {
            return;
        }
        appData.playRule.isShow = true;
    },
    cancelGamePlay: function() {
        appData.playRule.isShow = false;
    },
    showWatch: function () {
        if (appData.roomStatus == 4) {
            return;
        }
        appData.watchInfo.isShow = true;
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

        // $('.createRoom .mainPart').css('height', '60vh');
        // $('.createRoom .mainPart .blueBack').css('height', '51vh');
        appData.ruleInfo.isShowRule = true;
    },
    cancelGameRule: function() {
        appData.ruleInfo.isShowRule = false;
        //$('.createRoom .mainPart').css('height', '65vh');
        //$('.createRoom .mainPart .blueBack').css('height', '46vh');
    },

    showBreakRoom: function() {
        if (appData.breakData != null && appData.breakData != undefined) {
            viewMethods.gameOverNew(appData.breakData.data.score_board, appData.breakData.data.balance_scoreboard);
        }
        chooseBigWinner();
        $(".ranking .rankBack").css("opacity", "1");
        $(".roundEndShow").show();

        $(".ranking").show();
        canvas();
    },
    confirmBreakRoom: function() {
        socketModule.sendGameOver();
        viewMethods.clickCloseAlert();
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
    return /^1(3|4|5|7|8)\d{9}$/.test(phone);
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
        console.log('vmCreated')
        resetState();
        //reconnectSocket();
        initView();
        if (globalData.roomStatus != 4) {
            $("#loading").hide();
        }

        $(".main").show();
    },
    vmUpdated: function() {
        console.log('vmUpdated');
    },
    vmMounted: function() {
        console.log('vmMounted');
    },
    vmDestroyed: function() {
        console.log('vmDestroyed');
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

var shareContent = '';

function getShareContent() {
    shareContent = "\n";

    if (appData.ruleInfo.banker_mode == 1) {
        shareContent += '模式：自由抢庄 ';
    } else if (appData.ruleInfo.banker_mode == 2) {
        shareContent += '模式：明牌抢庄 ';
    }

    shareContent += '底分：'+ appData.ruleInfo.baseScore +'分';

    if (appData.ruleInfo.isJoker == 1) {
        shareContent += '  规则：天公x9-雷公x7-地公x7';
    }
    if (appData.ruleInfo.isBj == 1) {
        shareContent += '  规则：暴玖x9';
    }

    if (appData.ruleInfo.ticket == 2) {
        shareContent = shareContent + '  局数：10局x2张房卡';
    } else {
        shareContent = shareContent + '  局数：20局x4张房卡';
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
            imgUrl: globalData.imageUrl + "files/images/bull/share_icon.jpg",
            success: function() {},
            cancel: function() {}
        });
        wx.onMenuShareAppMessage({
            title: globalData.shareTitle + '(房间号:' + globalData.roomNumber + ')',
            desc: shareContent,
            link: globalData.roomUrl,
            imgUrl: globalData.imageUrl + "files/images/bull/share_icon.jpg",
            success: function() {},
            cancel: function() {}
        });

    }
};

//微信配置
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

    wx.onMenuShareTimeline({
        title: globalData.shareTitle + '(房间号:' + globalData.roomNumber + ')',
        desc: shareContent,
        link: globalData.roomUrl,
        imgUrl: globalData.imageUrl + "files/images/bull/share_icon.jpg",
        success: function() {},
        cancel: function() {}
    });
    wx.onMenuShareAppMessage({
        title: globalData.shareTitle + '(房间号:' + globalData.roomNumber + ')',
        desc: shareContent,
        link: globalData.roomUrl,
        imgUrl: globalData.imageUrl + "files/images/bull/share_icon.jpg",
        success: function() {},
        cancel: function() {}
    });
});

wx.error(function(a) {});

//画布
function canvas() {
    var target = document.getElementById("ranking");
    html2canvas(target, {
        allowTaint: true,
        taintTest: false,
        onrendered: function(canvas) {
            canvas.id = "mycanvas";
            var dataUrl = canvas.toDataURL('image/jpeg', 0.5);
            $("#end").attr("src", dataUrl);
            $(".end").show();
            $('.ranking').hide();
            newGame();
        }
    });
}

function objUpdate(player, data) {
    for (var item in player) {
        player[item] = data[item] !== undefined ? data[item] : player[item];
    }
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

//积分榜
$(function() {
    //$(".main").css("height", window.innerWidth * 1.621);
    $(".place").css("width", per * 140);
    $(".place").css("height", per * 140);
    $(".place").css("top", per * 270);
    $(".place").css("left", per * 195);

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
            console.log('play backMusic');
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
});