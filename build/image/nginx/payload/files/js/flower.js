var ws;
var game = {
    "room": 0,
    "room_number": globalData.roomNumber,
    "room_url": 0,
    "score": 0,
    "status": 0,
    "time": -1,
    "round": 0,
    "total_num": 10,
    "currentScore": 0,
    "cardDeal": 0,
    "can_open": 0,
    "is_play": false,
    "autoReady": false,
    "autoBet": false,
    "autoBetTimeOut": ''
};
var message = [
    { "num": 0, "text": "玩游戏，请先进群" },
    { "num": 1, "text": "群内游戏，切勿转发" },
    { "num": 2, "text": "别磨蹭，快点打牌" },
    { "num": 3, "text": "我出去叫人" },
    { "num": 4, "text": "你的牌好靓哇"},
    { "num": 5, "text": "我当年横扫澳门五条街"},
    { "num": 6, "text": "算你牛逼" },
    { "num": 7, "text": "别吹牛逼，有本事干到底" },
    { "num": 8, "text": "输得裤衩都没了" },
    { "num": 9, "text": "我给你们送温暖了" },
    { "num": 10, "text": "谢谢老板" }
];

var wsOperation = {
    JoinRoom: "JoinRoom",
    Audience: "Audience",
    UpdateAudienceInfo: 'UpdateAudienceInfo',
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
    PkCard: "PkCard",
    UpdateAccountScore: "UpdateAccountScore",
    OpenCard: "OpenCard",
    Win: "Win",
    Discard: "Discard",
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
    RefreshRoom: "PullRoomInfo"
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
                    appData.activity = bodyData.data.concat();
                    viewMethods.clickShowAlert(5, appData.activity[0].content);
                }
            } else {
                viewMethods.clickShowAlert(8, bodyData.result_message);
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
                is_grab: "1"
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
                is_grab: "0"
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
    sendChooseChip: function(num) {
        socketModule.sendData({
            operation: wsOperation.ChooseChip,
            account_id: userData.accountId,
            session: globalData.session,
            data: {
                room_id: appData.game.room,
                score: num,
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
        if (obj.data.chip_type) {

            appData.ruleInfo.chip_type = obj.data.chip_type;
            appData.ruleInfo.disable_pk_men = obj.data.disable_pk_men;
            appData.ruleInfo.disable_pk_100 = obj.data.disable_pk_100;
            appData.ruleInfo.ticket_count = obj.data.ticket_count;
            appData.ruleInfo.upper_limit = obj.data.upper_limit;
            appData.ruleInfo.mode = obj.data.mode;
            appData.ruleInfo.seenProgress = obj.data.seenProgress;
            appData.ruleInfo.compareProgress = obj.data.seenProgress;
            appData.ruleInfo.raceCard = obj.data.raceCard;
            appData.ruleInfo.extraRewards = obj.data.extraRewards;
            if (appData.ruleInfo.disable_pk_100 == 1 && appData.ruleInfo.disable_pk_men == 1) {
                appData.ruleInfo.rule_height = 60;
            } else {
                appData.ruleInfo.rule_height = 30;
            }

            appData.scoreList2 = appData.ruleInfo.chip_type;
            appData.scoreList1 = [];

            for(var i = 0, len = appData.scoreList2.length; i < len; i++){
                appData.scoreList1.push(appData.scoreList2[i]*2);
            }

            // if (appData.ruleInfo.chip_type == 2) {
            //     appData.scoreList1 = [4, 10, 20, 40];
            //     appData.scoreList2 = [2, 5, 10, 20];
            // } else if (appData.ruleInfo.chip_type == 4) {
            //     appData.scoreList1 = [10, 20, 40, 80];
            //     appData.scoreList2 = [5, 10, 20, 40];
            // } else {
            //     appData.scoreList1 = [4, 8, 16, 20];
            //     appData.scoreList2 = [2, 4, 8, 10];
            // }

            if (Math.ceil(obj.data.mode) == 2) {
                document.title = '大牌飘三叶房间'+game.room_number;
                globalData.shareTitle = '大牌飘三叶';
            }
        }
    },
    processPrepareJoinRoom: function(obj) {
        if (obj.data.room_status == 4) {
            appData.roomStatus = obj.data.room_status;
            viewMethods.clickShowAlert(8, obj.result_message);
            return;
        }

        appData.joinChoose.isShow = true;

        if (obj.data.chip_type) {

            appData.ruleInfo.chip_type = obj.data.chip_type;
            appData.ruleInfo.disable_pk_men = obj.data.disable_pk_men;
            appData.ruleInfo.disable_pk_100 = obj.data.disable_pk_100;
            appData.ruleInfo.ticket_count = obj.data.ticket_count;
            appData.ruleInfo.upper_limit = obj.data.upper_limit;
            appData.ruleInfo.mode = obj.data.mode;
            // appData.ruleInfo.seen = obj.data.seen;
            appData.ruleInfo.compareProgress = obj.data.compareProgress;
            appData.ruleInfo.seenProgress = obj.data.seenProgress;
            appData.ruleInfo.raceCard = obj.data.raceCard;
            appData.ruleInfo.extraRewards = obj.data.extraRewards;
            appData.ruleInfo.default_score = obj.data.default_score;
            if (appData.ruleInfo.disable_pk_100 == 1 && appData.ruleInfo.disable_pk_men == 1) {
                appData.ruleInfo.rule_height = 60;
            } else {
                appData.ruleInfo.rule_height = 30;
            }

            appData.scoreList2 = appData.ruleInfo.chip_type;
            appData.scoreList1 = [];

            for(var i = 0, len = appData.scoreList2.length; i < len; i++){
                appData.scoreList1.push(appData.scoreList2[i]*2);
            }

            // if (appData.ruleInfo.chip_type == 2) {
            //     appData.scoreList1 = [4, 10, 20, 40];
            //     appData.scoreList2 = [2, 5, 10, 20];
            // } else if (appData.ruleInfo.chip_type == 4) {
            //     appData.scoreList1 = [10, 20, 40, 80];
            //     appData.scoreList2 = [5, 10, 20, 40];
            // } else {
            //     appData.scoreList1 = [4, 8, 16, 20];
            //     appData.scoreList2 = [2, 4, 8, 10];
            // }
            appData.base_score = obj.data.default_score;
            if (Math.ceil(obj.data.mode) == 2) {
                document.title = '大牌飘三叶房间'+game.room_number;
                globalData.shareTitle = '大牌飘三叶';
            }
        }

        wxModule.config();

        if (obj.data.alert_text != "" && obj.data.user_count != 0) {
            appData.alertText = obj.data.alert_text;
            appData.joinChoose.isShow = true;
        }
    },
    processJoinRoom: function(obj) {
        if (obj.data.room_status == 4) {
            appData.roomStatus = obj.data.room_status;
            viewMethods.clickShowAlert(8, obj.result_message);
            return;
        }
        appData.isAudience = false;

        for (var i = 0; i < 6; i++) {
            var obj_player = {
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
                "win_type": 0,
                "limit_time": 0,
                "current_win": 0,
                "is_operation": false,
                "win_show": false,
                "card": [],
                "is_showCard": false,
                "is_pk": false,
                "is_readyPK": false,
                "card_type": 0,
                "messageOn": false,
                "messageText": "我们来血拼吧",
                "can_seen": false
            };
            var scoreObj = {
                "account_id": 0,
                "nickname": "",
                "account_score": 0,
                "isBigWinner": 0
            };
            objUpdate(appData.player[i], obj_player);
            objUpdate(appData.playerBoard.score[i], scoreObj);
        }

        appData.game.room = obj.data.room_id;
        appData.game.room_url = obj.data.room_url;
        appData.game.currentScore = Math.ceil(obj.data.benchmark);
        appData.game.score = Math.ceil(obj.data.pool_score);
        appData.game.round = Math.ceil(obj.data.game_num);
        appData.game.total_num = Math.ceil(obj.data.total_num);

        if (obj.data.limit_time == -1) {
            appData.game.time = Math.ceil(obj.data.limit_time);
            viewMethods.timeCountDown();
        }

        appData.player[0].serial_num = Math.ceil(obj.data.serial_num);

        for (var i = 0; i < 6; i++) {
            if (i <= 6 - Math.ceil(obj.data.serial_num)) {
                appData.player[i].serial_num = i + Math.ceil(obj.data.serial_num);
            } else {
                appData.player[i].serial_num = i + Math.ceil(obj.data.serial_num) - 6;
            }
        }

        appData.player[0].account_status = Math.ceil(obj.data.account_status);
        appData.player[0].account_score = Math.ceil(obj.data.account_score);
        appData.player[0].nickname = userData.nickname;
        appData.player[0].headimgurl = userData.headimgurl;
        appData.player[0].account_id = userData.accountId;
        appData.player[0].card = obj.data.cards.concat();
        appData.player[0].card_type = obj.data.card_type;
        appData.player[0].ticket_checked = obj.data.ticket_checked;
        appData.game.status = Math.ceil(obj.data.room_status);

        if (appData.game.status == 2) {
            appData.game.cardDeal = 3;

            if (appData.player[0].account_status == 4) {
                viewMethods.cardOver(0);
            }
        }



        appData.scoreboard = obj.data.scoreboard;
    },
    processAudience: function (obj) {
        if (obj.data.room_status == 4) {
            viewMethods.clickShowAlert(8, obj.result_message);
            if ( obj.data.to_joinRoom == 1) {
                socketModule.sendJoinRoom();
            }
            return;
        }
        appData.isAudience = true;

        for (var i = 0; i < 6; i++) {
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
                "win_type": 0,
                "limit_time": 0,
                "current_win": 0,
                "is_operation": false,
                "win_show": false,
                "card": [],
                "is_showCard": false,
                "is_pk": false,
                "is_readyPK": false,
                "card_type": 0,
                "messageOn": false,
                "messageText": "我们来血拼吧",
                "can_seen": false
            };
            var scoreObj = {
                "account_id": 0,
                "nickname": "",
                "account_score": 0,
                "isBigWinner": 0,
            };
            objUpdate(appData.player[i], obj_player);
            objUpdate(appData.playerBoard.score[i], scoreObj);
        }

        appData.game.room = obj.data.room_id;
        appData.game.room_url = obj.data.room_url;
        appData.game.currentScore = Math.ceil(obj.data.benchmark);
        appData.game.score = Math.ceil(obj.data.pool_score);
        appData.game.round = Math.ceil(obj.data.game_num);
        appData.game.total_num = Math.ceil(obj.data.total_num);

        if (obj.data.limit_time == -1) {
            appData.game.time = Math.ceil(obj.data.limit_time);
            viewMethods.timeCountDown();
        }

        appData.game.status = Math.ceil(obj.data.room_status);

        var obSeatNum = Math.ceil(obj.data.seat_num); // 观战的游戏玩家位置
        for (var i = 0; i < 6; i++) {
            if (i <= 6 - obSeatNum) {
                appData.player[i].serial_num = i + obSeatNum;
            } else {
                appData.player[i].serial_num = i + obSeatNum - 6;
            }
        }

        if (appData.game.status == 2) {
            appData.game.cardDeal = 3;

            if (appData.player[0].account_status == 4) {
                viewMethods.cardOver(0);
            }
        }

        appData.scoreboard = obj.data.scoreboard;
    },
    processUpdateAudienceInfo: function (obj) {
        if (obj.audience.status == 1) {
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
                        if (appData.player[j].account_id == obj.data.account_id){
                            objUpdate(appData.player[j], obj.data)
                        }
                    }
                    break;
                }
            }
        }
    },
    processRefreshRoom: function(obj) {
        appData.player = [];

        objUpdate(appData.playerBoard, {
            "score": [],
            "round": 0,
            "record": ""
        });

        for (var i = 0; i < 6; i++) {
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
                "win_type": 0,
                "limit_time": 0,
                "is_operation": false,
                "win_show": false,
                "card": [],
                "is_showCard": false,
                "is_pk": false,
                "is_readyPK": false,
                "card_type": 0,
                "messageOn": false,
                "messageText": "我们来血拼吧",
                "can_seen": false
            });

            appData.playerBoard.score.push({
                "account_id": 0,
                "nickname": "",
                "account_score": 0,
                "isBigWinner": 0
            });
        }

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
                }
            }
        }

        appData.player[0].card = obj.data.cards.concat();
        appData.player[0].card_type = obj.data.card_type;

    },
    processAllGamerInfo: function(obj) {
        appData.audiences = obj.audience;

        for (var i = 0; i < 6; i++) {
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
                "win_type": 0,
                "limit_time": 0,
                "current_win": 0,
                "is_operation": false,
                "win_show": false,
                "card": [],
                "is_showCard": false,
                "is_pk": false,
                "is_readyPK": false,
                "card_type": 0,
                "messageOn": false,
                "messageText": "我们来血拼吧",
                "can_seen": false
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
                    break;
                }
            }
            if (!isInGame) {
                objUpdate(appData.player[i], obj_player);
            }
        }
        if (appData.scoreboard != "") {
            for (var i = 0; i < 6; i++) {
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

        if (appData.player[0].account_status > 2 && appData.player[0].account_status != 8 && !appData.isAudience) {
            setTimeout(function() {
                appData.player[0].is_showCard = true;
            }, 500);
        }

    },
    processUpdateGamerInfo: function(obj) {
        for (var i = 0, len = appData.audiences.length; i < len; i += 1) {
            if (obj.data.account_id == appData.audiences[i].account_id) {
                appData.audiences.splice(i, 1);
                break;
            }
        }
        for (var i = 0; i < 6; i++) {
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
    },

    processUpdateAccountStatus: function(obj) {
        for (var i = 0; i < 6; i++) {
            if (appData.player[i].account_id == obj.data.account_id) {
                if (obj.data.online_status == 1) {
                    appData.player[i].account_status = Math.ceil(obj.data.account_status);

                    if (i != 0) {
                        if (appData.player[i].account_status == 4)
                            m4aAudioPlay("audio3 ");
                        else if (appData.player[i].account_status == 5)
                            m4aAudioPlay("audio4");
                        else if (appData.player[i].account_status == 6)
                            m4aAudioPlay("audio5");
                    } else {
                        appData.player[0].is_operation = false;
                    }
                } else if (obj.data.online_status == 0 && appData.player[i].account_status == 0) {
                    appData.player[i].account_id = 0;
                    appData.player[i].account_status = 0;
                    appData.player[i].playing_status = 0;
                    appData.player[i].online_status = 0;
                    appData.player[i].nickname = "";
                    appData.player[i].headimgurl = "";
                    appData.player[i].account_score = 0;
                } else if (obj.data.online_status == 0 && appData.player[i].account_status > 0) {
                    appData.player[i].account_status = Math.ceil(obj.data.account_status);
                    appData.player[i].online_status = 0;
                }
            }
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
        for (var i = 0; i < 6; i++) {
            appData.player[i].is_operation = false;
            appData.player[i].is_showCard = false;

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
                    appData.player[i].account_score = appData.player[i].account_score - obj.data[j].chip;
                    appData.player[i].limit_time = Math.ceil(obj.data[j].limit_time);
                    if(obj.data[j].can_look == 1) {
                        appData.player[i].can_seen = true;
                    }

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
        viewMethods.reDeal();

        if (appData.game.autoBet && appData.player[0].account_id == currentPlayerAccountId) {
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
    processNotyChooseChip: function(obj) {
        appData.game.is_play = true;

        currentPlayerNum = -1;

        if (appData.game.status == 2) {
            for (var i = 0; i < 6; i++) {
                appData.player[i].playing_status = 1;

                if (appData.player[i].account_id == obj.data.account_id) {
                    appData.player[i].is_showCard = true;
                    appData.player[i].is_operation = false;
                    appData.player[i].playing_status = Math.ceil(obj.data.playing_status);
                    appData.player[i].limit_time = Math.ceil(obj.data.limit_time);
                    appData.game.can_open = obj.data.can_open;
                    console.log(typeof obj.data.can_look);
                    if (obj.data.can_look == 1) {
                        appData.player[i].can_seen = true;
                    }
                }

                if (appData.player[i].playing_status > 1) {
                    currentPlayerNum = i;
                }
            }
        }

        appData.pkPeople = obj.data.pk_user.concat();

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
    processCardInfo: function(obj) {
        appData.player[0].card = obj.data.cards.concat();
        appData.player[0].card_type = obj.data.card_type;
        viewMethods.cardOver(0);
        viewMethods.cardTest();
    },
    processPKCard: function(obj) {
        var num1 = 0,
            num2 = 0;


        for (var i = 0; i < 6; i++) {
            if (appData.player[i].account_id == obj.data.account_id) {
                appData.player[i].account_score = appData.player[i].account_score - Math.ceil(obj.data.score);
                viewMethods.throwCoin(appData.player[i].num, obj.data.score);
            }

            if (appData.player[i].account_id == obj.data.loser_id) {
                appData.player[i].account_status = 7;
                appData.player[i].is_pk = true;
                num1 = i;
            }

            if (appData.player[i].account_id == obj.data.winner_id) {
                appData.player[i].is_pk = true;
                num2 = i;
            }
        }

        appData.game.score = parseInt(appData.game.score) + Math.ceil(obj.data.score);

        viewMethods.playerPK(num1, num2);
    },
    processBroadcastVoice: function(obj) {
        for (var i = 0; i < 6; i++) {
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
        for (var i = 0; i < 6; i++) {
            if (appData.player[i].account_id == obj.data.account_id) {

                appData.player[i].account_score = appData.player[i].account_score - Math.ceil(obj.data.score);

                if (appData.player[i].account_status == 5) {
                    appData.game.currentScore = Math.ceil(obj.data.score) * 2;
                } else {
                    appData.game.currentScore = Math.ceil(obj.data.score);
                }

                appData.game.score = parseInt(appData.game.score) + Math.ceil(obj.data.score);
                if (i != 0 || appData.isAudience) {
                    viewMethods.throwCoin(appData.player[i].num, obj.data.score);
                    m4aAudioPlay(obj.data.score + "f");
                }
            }
        }
    },
    processOpenCard: function(obj) {
        if (!appData.game.is_play) {
            return 0;
        }
        for (var i = 0; i < 6; i++) {
            if (appData.player[i].account_id == obj.data.account_id) {
                appData.player[i].account_score = appData.player[i].account_score - Math.ceil(obj.data.score);
                appData.game.score = parseInt(appData.game.score) + Math.ceil(obj.data.score);
                viewMethods.throwCoin(appData.player[i].num, obj.data.score)
            }
        }
    },
    processWin: function(obj) {
        appData.game.is_play = false;
        appData.game.round = Math.ceil(obj.data.game_num);
        appData.game.total_num = Math.ceil(obj.data.total_num);
        appData.playerBoard.round = Math.ceil(obj.data.game_num);

        for (var i = 0; i < 6; i++) {
            appData.player[i].playing_status = 1;

            if (obj.data.card_type == 0) {

            } else {
                for (var j = 0; j < obj.data.player_cards.length; j++) {
                    if (appData.player[i].account_id == obj.data.player_cards[j].account_id) {
                        appData.player[i].card = obj.data.player_cards[j].cards.concat();
                    }
                }
            }

            for (j in obj.data.winner_score_dict) {
                if (appData.player[i].account_id == j) {
                    appData.player[i].is_win = true;
                    appData.player[i].win_type = obj.data.card_type;
                    appData.player[i].current_win = obj.data.winner_score_dict[j];
                }
            }
        }

        if (obj.data.card_type == 0) {
            viewMethods.gameOver(obj.data.winner_score_dict, obj.data.score_board, obj.data.joycard_score_dict, 1000, 2000);
        } else {

            viewMethods.cardOver(1);

            if (obj.data.total_num == obj.data.game_num) {
                viewMethods.gameOver(obj.data.winner_score_dict, obj.data.score_board, obj.data.joycard_score_dict, 2500, 1000);
            } else {
                viewMethods.gameOver(obj.data.winner_score_dict, obj.data.score_board, obj.data.joycard_score_dict, 2500, 2500);
            }
        }

        if (obj.data.total_num == obj.data.game_num) {
            viewMethods.roundEnd();
        }
    },
    processDiscard: function(obj) {
        appData.player[0].account_status = 6;
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
    }
};

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

            $("#valert .mainPart").css('height', mainHeight + 'px');
            $("#valert .mainPart").css('margin-top', '-' + mainHeight / 2 + 'px');
            $("#valert .mainPart .backImg .blackImg").css('height', blackHeight + 'px');
            $("#valert .mainPart .alertText").css('top', alertTop + 'px');
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
    autoReadyStatus: function() {
        appData.game.autoReady = !appData.game.autoReady;
    },
    autoBetStatus: function () {
        appData.game.autoBet = !appData.game.autoBet;
    },
    reDeal: function() {
        m4aAudioPlay('audio1');
        appData.game.cardDeal = 1;

        setTimeout(function() {
            appData.game.cardDeal = 2;
            setTimeout(function() {
                appData.game.cardDeal = 3;
                setTimeout(function() {
                    appData.player[0].is_showCard = true;
                }, 400);
            }, 250);
        }, 250);
    },
    cardOver: function(num) {
        // if (appData.isAudience) {
        //     return false;
        // }

        if (num == 0) {
            $(".myCards .card0").velocity({ left: 0 }, { duration: 450 });
            $(".myCards .card1").velocity({ left: 0 }, { duration: 450 });
            $(".myCards .card2").velocity({ left: 0 }, {
                duration: 450,
                complete: function() {
                    $(".myCards .cards").addClass("card-flipped");
                    $(".myCards .card0").velocity({ left: "0" }, { duration: 550 })
                    $(".myCards .card1").velocity({ left: "50%" }, { duration: 550 })
                    $(".myCards .card2").velocity({ left: "100%" }, { duration: 550 })
                }
            });
        } else {
            appData.game.cardDeal = -1;

            $(".cardOver .card0").velocity({ left: 0 }, { duration: 250 });
            $(".cardOver .card1").velocity({ left: 0 }, { duration: 250 });
            $(".cardOver .card2").velocity({ left: 0 }, {
                duration: 250,
                complete: function() {
                    $(".cardOver .cards").addClass("card-flipped");
                    $(".cardOver .card0").velocity({ left: "0" }, { duration: 500 })
                    $(".cardOver .card1").velocity({ left: "25%" }, { duration: 500 })
                    $(".cardOver .card2").velocity({ left: "50%" }, { duration: 500 })
                }
            });

            if (appData.player[0].account_status == 5) {
                appData.player[0].account_status = 4;
                viewMethods.cardOver(0);
            }
        }
    },
    cardTest: function() {
        if (appData.player[0].account_status == 4 && appData.player[0].card.length == 0) {
            socketModule.sendRefreshRoom();
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
        for (var i = 0; i < 6; i++) {
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
                    appData.player[i].win_show = true;
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
            // appData.playerBoard.record=str+" 前"+appData.playerBoard.round+"局";
            appData.playerBoard.record = str;

            setTimeout(function() {
                viewMethods.tableReset(1);
            }, time2);
        }, time1);
    },

    showMessage: function() {
        // $(".message .textPart").animate({
        //     height:"400px"
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
        //     height:0
        // }, function() {
        //     appData.isShowMessage = false;
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
        if (isTimeLimitShow == true) {
            return;
        }
        if (appData.game.time <= 0) {
            isTimeLimitShow = false;
            return 0;
        } else {
            isTimeLimitShow = true;
            appData.game.time--;
            timeLimit = setTimeout(function() {
                isTimeLimitShow = false;
                viewMethods.timeCountDown();
            }, 1e3);
        }
    },
    playerTimeCountDown: function() {
        if (isPlayerTimeLimitShow == true) {
            return;
        }
        if (appData.player[currentPlayerNum].limit_time <= 0 || currentPlayerNum < 0) {
            isPlayerTimeLimitShow = false;
            return 0;
        } else {
            isPlayerTimeLimitShow = true;
            appData.player[currentPlayerNum].limit_time--;
            setTimeout(function() {
                isPlayerTimeLimitShow = false;
                viewMethods.playerTimeCountDown();
            }, 1e3);
        }
    },
    tableReset: function(type) {
        for (var i = 0; i < 6; i++) {

            if (appData.player[i].account_status > 1 && type == 1 && appData.player[i].account_status != 8) {
                appData.player[i].account_status = 1;
            }

            appData.player[i].playing_status = 0;
            appData.player[i].is_win = false;
            appData.player[i].is_operation = false;
            appData.player[i].win_type = 0;
            appData.player[i].win_show = false;
            appData.player[i].card = [];
            appData.player[i].card_type = 0;
            appData.player[i].is_showCard = false;
            appData.player[i].is_readyPK = false;
            appData.player[i].is_pk = false;
            appData.player[i].can_seen = false;
        }

        appData.game.can_open = 0;
        appData.game.score = 0;
        appData.game.cardDeal = 0;
        appData.game.currentScore = 0;
        appData.game.status = 1;

        $(".cards").removeClass("card-flipped");
        $(".scoresArea").empty();
        if(appData.game.autoReady && !appData.isAudience && appData.game.round >0&&appData.game.round < appData.game.total_num){
            socketModule.sendReadyStart();
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
                            for (var i = 0; i < appData.player.length; i++) {
                                appData.player[i].is_pk = false;
                            }

                        }, 2000)
                    }, 800)
                }
            });
        }, 0);
    },
    choose: function(type, num, auto) {
        //type:1，看牌闷牌；2，下注 3,弃牌 4，开牌/比牌 5，比牌

        if (!auto && appData.game.autoBetTimeOut) {
            clearTimeout(appData.game.autoBetTimeOut);
        }

        if (appData.player[0].is_operation) {
            return 0;
        }

        if (type == 1) {
            socketModule.sendClickToLook();
            m4aAudioPlay("audio3");
        } else if (type == 2) {
            socketModule.sendChooseChip(num);
            m4aAudioPlay(num + "f");
            viewMethods.throwCoin(1, num);
            appData.player[0].is_operation = true;
        } else if (type == 3) {
            socketModule.sendDiscard();
            m4aAudioPlay("audio5");
            appData.player[0].is_operation = true;
        } else if (type == 4) {
            var peopleNum = 0;

            for (var i = 0; i < appData.player.length; i++) {
                if (appData.player[i].account_status == 4 || appData.player[i].account_status == 5) {
                    peopleNum++;
                }
            }

            if (peopleNum == 2) {
                socketModule.sendOpenCard();
                m4aAudioPlay("audio2");
                appData.player[0].is_operation = true;
            } else {
                for (var i = 0; i < appData.player.length; i++) {
                    appData.player[i].is_readyPK = false;

                    for (var k = 0; k < appData.pkPeople.length; k++) {
                        if (appData.player[i].account_id == appData.pkPeople[k]) {
                            appData.player[i].is_readyPK = true;

                        }
                    }
                }
                appData.pk.round = 1;
            }

        } else if (type == 5) {
            socketModule.sendPkCard(num);
            appData.player[0].is_operation = true;
        }
    },
    quitPk: function() {
        appData.pk.round = 0;
    }
};

var fileDealerNum = 'd_' + globalData.dealerNum;
var width = window.innerWidth;
var height = window.innerHeight;
var numD = [];
var isTimeLimitShow = false;
var isPlayerTimeLimitShow = false;
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
    // 'seen': 0,
    'compareProgress': 0,
    'seenProgress': 0,
    'raceCard': 0,
    'extraRewards': 0,
    "default_score": 2,
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
    isAudience: false,
    hasXiPai: false, // 是否有喜牌
    audiences: [],
    isShowErweima: false,
    roomStatus: globalData.roomStatus,
    scoreList1: scoreList1,
    scoreList2: scoreList2,
    'width': window.innerWidth,
    'height': window.innerHeight,
    'roomCard': Math.ceil(globalData.card),
    'gameTitle': '炸金花',
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
    ruleInfo: ruleInfo,
    watchInfo: watchInfo,
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

    for (var i = 0; i < 6; i++) {
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
            "win_type": 0,
            "limit_time": 0,
            "current_win": 0,
            "is_operation": false,
            "win_show": false,
            "card": [],
            "is_showCard": false,
            "is_pk": false,
            "is_readyPK": false,
            "card_type": 0,
            "messageOn": false,
            "messageText": "我们来血拼吧",
            "can_seen": false
        });

        appData.playerBoard.score.push({
            "account_id": 0,
            "nickname": "",
            "account_score": 0,
            "isBigWinner": 0
        });
    }

    if (appData.isAuthPhone != 1) {
        httpModule.getActivityInfo();
    }
    if (appData.roomStatus != 4) {
        httpModule.getScoreInfo();
    }
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
    appData.game.cardDeal = 0;
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
            appData.player[i].is_operation = false;
            appData.player[i].win_type = 0;
            appData.player[i].win_show = false;
            appData.player[i].card = [];
            appData.player[i].card_type = 0;
            appData.player[i].ticket_checked = 0;
            appData.player[i].account_score = 0;
            appData.player[i].current_win = 0;
            appData.player[i].is_showCard = false;
            appData.player[i].is_readyPK = false;
            appData.player[i].is_pk = false;
            appData.player[i].can_seen = false;
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
                "win_type": 0,
                "ticket_checked": 0,
                "limit_time": 0,
                "current_win": 0,
                "is_operation": false,
                "win_show": false,
                "card": [],
                "is_showCard": false,
                "is_pk": false,
                "is_readyPK": false,
                "card_type": 0,
                "can_seen": false
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
            } else if (obj.result == -1) {
                viewMethods.clickShowAlert(7, obj.result_message);
            } else {
                viewMethods.clickShowAlert(7, obj.result_message);
            }
        } else if(obj.operation == wsOperation.ClickToLook){
            if (obj.result == -1) {
                viewMethods.clickShowAlert(1, obj.result_message);
            }
        }else if(obj.operation == wsOperation.PkCard){
            if (obj.result == -1) {
                viewMethods.clickShowAlert(1, obj.result_message);
            }
        }else if (obj.operation == wsOperation.ReadyStart) {
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
        } else if (obj.operation == wsOperation.UpdateAccountScore) {
            socketModule.processUpdateAccountScore(obj);
        } else if (obj.operation == wsOperation.OpenCard) {
            socketModule.processOpenCard(obj);
        } else if (obj.operation == wsOperation.Win) {
            socketModule.processWin(obj);
        } else if (obj.operation == wsOperation.Discard) {
            socketModule.processDiscard(obj);
        } else if (obj.operation == wsOperation.BroadcastVoice) {
            socketModule.processBroadcastVoice(obj);
        } else if (obj.operation == wsOperation.CreateRoom) {
            socketModule.processCreateRoom(obj);
        } else if (obj.operation == wsOperation.StartBet) {
            socketModule.processStartBet(obj);
        } else if (obj.operation == wsOperation.StartShow) {
            socketModule.processStartShow(obj);
        } else if (obj.operation == wsOperation.PkCard) {
            socketModule.processPKCard(obj);
        } else if (obj.operation == wsOperation.CardInfo) {
            socketModule.processCardInfo(obj);
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


        var audioUrl = ["5f.m4a", "40f.m4a", "80f.m4a", "2f.m4a", "4f.m4a", "8f.m4a", "16f.m4a", "10f.m4a", "20f.m4a", "50f.mp3", "100f.mp3", "200f.mp3", "audio1.m4a", "audio2.m4a", "audio3.m4a", "audio4.m4a", "audio5.m4a", "com.m4a", "lose.mp3", "win.mp3","back.mp3"];
        var audioName = ["5f", "40f", "80f", "2f", "4f", "8f", "16f", "10f", "20f", "50f", "100f", "200f", "audio1", "audio2", "audio3", "audio4", "audio5", "com", "lose", "win", "backMusic"];
        for (var i = 0; i < audioUrl.length; i++) {
            this.loadAudioFile(this.baseUrl + 'files/audio/flower/' + audioUrl[i], audioName[i]);
        }

        var audioMessageUrl = ["message9.m4a", "message10.m4a", "message11.m4a", "message1.m4a", "message2.m4a", "message3.m4a", "message4.m4a", "message5.m4a", "message6.m4a", "message7.m4a", "message8.m4a"];
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
        var divs = ['table', 'vinvite', 'valert', 'vmessage', 'vshop', 'vcreateRoom', 'vroomRule', 'endCreateRoom', 'endCreateRoomBtn'];
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

    // if (appData.ruleInfo.chip_type == 1) {
    //     shareContent += '筹码：2/4，4/8，8/16，10/20';
    // } else if (appData.ruleInfo.chip_type == 2) {
    //     shareContent += '筹码：2/4，5/10，10/20，20/40';
    // } else if (appData.ruleInfo.chip_type == 4) {
    //     shareContent += '筹码：5/10，10/20，20/40, 40/80';
    // }

    for (var i = 0, len = appData.ruleInfo.chip_type.length; i < len; i++){
        if(i !== 0){
            shareContent += ',';
        }
        shareContent += appData.ruleInfo.chip_type[i]/(appData.ruleInfo.chip_type[i]*2);
    }

    if (appData.ruleInfo.disable_pk_100 == 1 || appData.ruleInfo.disable_pk_men == 1) {
        shareContent += ' 规则：';
        if (appData.ruleInfo.disable_pk_100 == 1) {
            shareContent += '100分以下不能比牌';
        }

        if (appData.ruleInfo.disable_pk_men == 1) {
            shareContent += '闷牌，全场禁止比牌';
        }
    }

  

    if (appData.ruleInfo.ticket_count == 1) {
        shareContent += '  局数：10局x1张房卡';
    } else {
        shareContent += '  局数：20局x2张房卡';
    }
    if (appData.ruleInfo.upper_limit == 0) {
        shareContent += '  上限：无';
    } else {
        shareContent += '  上限：' + appData.ruleInfo.upper_limit;
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