var ws;
var wsctop = 0;

var width = window.innerWidth;
var height = window.innerHeight;
var shareContent = '';

var isBankerWin = false;
var isTimeLimitShow = false;
var isLoadAudioFile = false;

var viewStyle = {
    button: {
        position: 'absolute',
        top: '53%',
        left: '5%',
        width: width * 0.9 + 'px',
        height: width * 0.2 + 'px',
        overflow: 'hidden'
    },
    rob: {
        position: 'absolute',
        top: (width * 0.2 - width * 0.09) / 2 + 'px',
        left: (width * 0.9 - width * 0.09 / 0.375 * 2 - 20) / 2 + 'px',
        width: width * 0.09 / 0.375 + 'px',
        height: width * 0.09 + 'px',
    },
    rob1: {
        position: 'absolute',
        top: '0px',
        left: '0px',
        width: width * 0.09 / 0.375 + 'px',
        height: width * 0.09 + 'px',
        'line-height': width * 0.09 + 'px',
        'text-align': 'center',
        color: 'white',
        'font-size': '12pt',
        'font-weight': 'bold'
    },
    notRob: {
        position: 'absolute',
        top: (width * 0.2 - width * 0.09) / 2 + 'px',
        left: (width * 0.9 - width * 0.09 / 0.375 * 2 - 20) / 2 + width * 0.09 / 0.375 + 20 + 'px',
        width: width * 0.09 / 0.375 + 'px',
        height: width * 0.09 + 'px'
    },
    notRob1: {
        position: 'absolute',
        top: '0px',
        left: '0px',
        width: width * 0.09 / 0.375 + 'px',
        height: width * 0.09 + 'px',
        'line-height': width * 0.09 + 'px',
        'text-align': 'center',
        color: 'white',
        'font-size': '12pt',
        'font-weight': 'bold'
    },
    showCard: {
        position: 'absolute',
        top: (width * 0.2 - width * 0.09) / 2 + 'px',
        left: (width * 0.9 - width * 0.09 / 0.375) / 2 + 'px',
        width: width * 0.09 / 0.375 + 'px',
        height: width * 0.09 + 'px'
    },
    showCard1: {
        position: 'absolute',
        top: '0px',
        left: '0px',
        width: width * 0.09 / 0.375 + 'px',
        height: width * 0.09 + 'px',
        'line-height': width * 0.09 + 'px',
        'text-align': 'center',
        color: 'white',
        'font-size': '12pt',
        'font-weight': 'bold'
    },
    times1: {
        position: 'absolute',
        top: (width * 0.2 - width * 0.16 / 2) / 2 + 'px',
        left: width * 0.1 + 'px',
        width: width * 0.16 + 'px',
        height: width * 0.16 / 2 + 'px'
    },
    timesText: {
        position: 'absolute',
        width: width * 0.16 + 'px',
        height: width * 0.16 / 2 + 'px',
        'line-height': width * 0.16 / 2 + 'px',
        'text-align': 'center',
        color: 'white',
        'font-size': '12pt',
        'font-weight': 'bold'
    },
    times2: {
        position: 'absolute',
        top: (width * 0.2 - width * 0.16 / 2) / 2 + 'px',
        left: width * 0.1 + width * 0.02 + width * 0.16 + 'px',
        width: width * 0.16 + 'px',
        height: width * 0.16 / 2 + 'px'
    },
    times3: {
        position: 'absolute',
        top: (width * 0.2 - width * 0.16 / 2) / 2 + 'px',
        left: width * 0.1 + width * 0.02 * 2 + width * 0.16 * 2 + 'px',
        width: width * 0.16 + 'px',
        height: width * 0.16 / 2 + 'px'
    },
    times4: {
        position: 'absolute',
        top: (width * 0.2 - width * 0.16 / 2) / 2 + 'px',
        left: width * 0.1 + width * 0.02 * 3 + width * 0.16 * 3 + 'px',
        width: width * 0.16 + 'px',
        height: width * 0.16 / 2 + 'px'
    },
    robText2: {
        position: 'absolute',
        top: (width * 0.2 - 21) / 2 + 'px',
        left: (width * 0.9 - 39) / 2 - 16 + 'px',
        width: '39px',
        height: '21px'
    },
    robText: {
        position: 'absolute',
        top: (width * 0.2 - 21) / 2 + 'px',
        left: (width * 0.9 - 39) / 2 + 'px',
        width: '39px',
        height: '21px'
    },
    robTimesText: {
        position: 'absolute',
        top: (width * 0.2 - 21) / 2 + 'px',
        left: (width * 0.9 - 39) / 2 + 30 + 'px',
        width: '21px',
        height: '21px'
    },
    notRobText: {
        position: 'absolute',
        top: (width * 0.2 - 21) / 2 + 'px',
        left: (width * 0.9 - 39) / 2 + 'px',
        width: '39px',
        height: '21px'
    },
    showCardText: {
        position: 'absolute',
        top: '10%',
        left: '10%',
        width: '80%',
        height: width * 0.2 + 'px',
    },
    showCardText1: {
        position: 'absolute',
        width: '100%',
        height: '100%',
        color: 'white',
        'font-size': '11pt',
        'text-align': 'center',
        'line-height': width * 0.2 + 'px',
        'font-family': 'Helvetica 微软雅黑'
    },
    coinText: {
        position: 'absolute',
        top: '10%',
        left: '10%',
        width: '80%',
        height: width * 0.2 + 'px',
    },
    coinText1: {
        position: 'absolute',
        width: '100%',
        height: '100%',
        color: 'white',
        'font-size': '11pt',
        'text-align': 'center',
        'line-height': width * 0.2 + 'px',
        'font-family': 'Helvetica 微软雅黑'
    }
};

var playRule = {isShow: false};

var scoreInfo = {
    one: '',
    three: '',
    week: '',
    month: '',
    total: '',
    isShow: false
};

var ruleInfo = {
    ticket_type: -1,
    rule_type: -1,
    isShow: false,
    isShowRule: false,
    base_score: 1,
    timesType: 1,
    ticket: 1,
    rule_height: 30,
    is_joker: 0,
    is_bj: 0,
    banker_mode: 1,
    bankerText: '抢庄'
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

var game = {
    room: 0,
    room_number: globalData.roomNumber,
    room_url: 0,
    score: 0,
    status: 0,
    time: -1,
    round: 0,
    total_num: 10,
    currentScore: 0,
    cardDeal: 0,
    can_open: 0,
    current_win: 0,
    is_play: false,
    show_card: false,
    show_coin: false,
    base_score: 0,
    show_score: false,
    show_bettext: false
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

var appData = {
    viewStyle: viewStyle,
    roomStatus: globalData.roomStatus,
    isAA: false, //是否AA房卡
    isAutoActive: false, //是否自动激活
    width: window.innerWidth,
    height: window.innerHeight,
    roomCard: Math.ceil(globalData.card),
    is_connect: false,
    player: [],
    scoreboard: '',
    activity: [],
    isShowAlert: false,
    isShowMessage: false,
    alertType: 0,
    alertText: '',
    showRob: false,
    showShowCardButton: false,
    showRobText: false,
    showNotRobText: false,
    showClockRobText: false,
    showClockBetText: false,
    showClockShowCard: false,
    showTimesCoin: false,
    showClickShowCard: false,
    showBankerCoinText: false,
    clickCard2: false,
    base_score: 0,
    playerBoard: {
        score: [],
        round: 0,
        record: "",
        room: ""
    },
    game: game,
    connectOrNot: true,
    socketStatus: 0,
    heartbeat: null,
    select: 1,
    ticket_count: 0,
    isDealing: false,
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
    scoreInfo: scoreInfo,
    ruleInfo: ruleInfo,
    playRule: playRule,
    editAudioInfo: editAudioInfo,
    audioInfo: audioInfo,
    isReconnect: true,
    bScroll: null
};

var wsOperation = {
    JoinRoom: "JoinRoom",
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
    BroadcastVoice: "BroadcastVoice",
    ActiveRoom: "ActivateRoom",
    MyCards: "MyCards",
    GameOver: "GameOver"
};

var httpModule = {
    getActivityInfo: function() {
        var postData = { "account_id": userData.accountId, "dealer_num": globalData.dealerNum, "session": globalData.session,'room_number' : globalData.roomNumber ,'game_type' : 36 };
        Vue.http.post(globalData.baseUrl + 'f/getActivityInfo', postData).then(function(response) {
            
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
            console.log(response.body);
        });
    },
    getScoreInfo: function () {
        Vue.http.post(globalData.baseUrl + 'f/scoreStat', {'type':36}).then(function (response) {
            var bodyData = response.body;
            if (0 == bodyData.result) {
                scoreInfo.one = bodyData.data.one;
                scoreInfo.three = bodyData.data.three;
                scoreInfo.week = bodyData.data.week;
                scoreInfo.month = bodyData.data.month;
                scoreInfo.total = bodyData.data.total;
            }
        })
    }
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
        console.log('%csocketModule.sendData(): ' + JSON.stringify(obj), "background: #11da83;");
        try {
            if (ws.readyState == WebSocket.CLOSED) {
                reconnectSocket();
                return;
            }

            if (ws.readyState == WebSocket.OPEN) {
                ws.send(JSON.stringify(obj));
            } else if (ws.readyState == WebSocket.CONNECTING) {
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
                is_grab: 1,
                multiples: multiples
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
                is_grab: 0,
                multiples: 1,
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
            appData.ruleInfo.ticket_type = obj.data.ticket_type;
            appData.ruleInfo.base_score = obj.data.score_type;
            appData.ruleInfo.rule_type = obj.data.rule_type;
            appData.ruleInfo.is_joker = Math.ceil(obj.data.is_joker);
            appData.ruleInfo.is_bj = Math.ceil(obj.data.is_bj);
            appData.ruleInfo.banker_mode = Math.ceil(obj.data.banker_mode);
        }

        if (appData.ruleInfo.banker_mode == 1) {
            appData.ruleInfo.bankerText = '自由抢庄';
        } else if (appData.ruleInfo.banker_mode == 2) {
            appData.ruleInfo.bankerText = '经典三公';
        }
    },
    processPrepareJoinRoom: function(obj) {
        if (obj.data.room_status == 4) {
            appData.roomStatus = obj.data.room_status;
            viewMethods.clickShowAlert(8, obj.result_message);
            return;
        }

        if (obj.data.ticket_type) {
            appData.ruleInfo.ticket_type = obj.data.ticket_type;
            appData.ruleInfo.base_score = obj.data.score_type;
            appData.ruleInfo.rule_type = obj.data.rule_type;
            appData.ruleInfo.is_joker = Math.ceil(obj.data.is_joker);
            appData.ruleInfo.is_bj = Math.ceil(obj.data.is_bj);
            appData.ruleInfo.banker_mode = Math.ceil(obj.data.banker_mode);
        }

        if (appData.ruleInfo.banker_mode == 1) {
            appData.ruleInfo.bankerText = '自由抢庄';
        } else if (appData.ruleInfo.banker_mode == 2) {
            appData.ruleInfo.bankerText = '经典三公';
        }

        wxModule.config();

        if (obj.data.room_status == 3) {
            if (appData.isAutoActive == true) {
                socketModule.sendActiveRoom();
            } else {
                $('.createRoom .mainPart').css('height', '36vh');
                $('.createRoom .mainPart .blueBack').css('height', '30vh');
            }
            return;
        }

        if (obj.data.user_count == 0) {
            socketModule.sendJoinRoom();
        } else {
            if (obj.data.alert_text != "") {
                viewMethods.clickShowAlert(4, obj.data.alert_text);
            } else {
                socketModule.sendJoinRoom();
            }
        }
    },
    processJoinRoom: function(obj) {
        appData.game.room = obj.data.room_id;
        appData.game.room_url = obj.data.room_url;
        appData.game.currentScore = Math.ceil(obj.data.benchmark);
        appData.game.score = Math.ceil(obj.data.pool_score);
        appData.game.round = Math.ceil(obj.data.game_num);
        appData.game.total_num = Math.ceil(obj.data.total_num);
        appData.game.base_score = Math.ceil(obj.data.base_score);
        appData.base_score = appData.game.base_score;

        console.log('processJoinRoom: ' + JSON.stringify(obj.data));

        resetAllPlayerData();

        if (obj.data.limit_time > 1) {
            appData.game.time = Math.ceil(obj.data.limit_time);
            viewMethods.timeCountDown();
        }

        appData.player[0].serial_num = obj.data.serial_num;
        for (var i = 0; i < 6; i++) {
            if (i <= 6 - obj.data.serial_num) {
                appData.player[i].serial_num = i + Math.ceil(obj.data.serial_num);
            } else {
                appData.player[i].serial_num = i + Math.ceil(obj.data.serial_num) - 6;
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

        if (appData.game.status == 2) {
            appData.game.cardDeal = 3;
        }

        appData.scoreboard = obj.data.scoreboard;

        console.log('processJoinRoom: resetMyAccountStatus');

        viewMethods.resetMyAccountStatus();
    },
    processRefreshRoom: function(obj) {
        resetAllPlayerData();

        appData.player[0].serial_num = obj.data.serial_num;

        for (var i = 0; i < 6; i++) {
            if (i <= 6 - obj.data.serial_num) {
                appData.player[i].serial_num = i + Math.ceil(obj.data.serial_num);
            } else {
                appData.player[i].serial_num = i + Math.ceil(obj.data.serial_num) - 6;
            }
        }

        appData.player[0].account_status = Math.ceil(obj.data.account_status);
        appData.player[0].account_score = Math.ceil(obj.data.account_score);
        appData.player[0].nickname = userData.nickname;
        appData.player[0].headimgurl = userData.avatar;
        appData.player[0].account_id = userData.accountId;
        appData.player[0].serial_num = obj.data.serial_num;
        appData.player[0].card = obj.data.cards.concat();
        appData.player[0].card_type = obj.data.card_type;
        appData.player[0].ticket_checked = obj.data.ticket_checked;

        if (appData.game.status == 2) {
            appData.game.cardDeal = 3;
        }

        for (i = 0; i < 6; i++) {
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
                    appData.player[i].card_type = obj.all_gamer_info[j].card_type;
                    appData.player[i].is_showbull = false;
                    if (obj.all_gamer_info[j].is_banker == 1) {
                        appData.player[i].is_banker = true;
                        appData.bankerAccountId = obj.all_gamer_info[j].account_id;
                        appData.bankerPlayer = appData.player[i];
                    } else {
                        appData.player[i].is_banker = false;
                    }
                    if (appData.player[i].account_status >= 8) {
                        appData.player[i].is_showCard = true;
                    }

                    if (appData.player[i].card_open.length < 1 || appData.player[i].card_open == undefined) {
                        appData.player[i].card_open = obj.all_gamer_info[j].cards.concat();
                    }

                    if (appData.player[i].card_open.length < 1 || appData.player[i].card_open == undefined) {
                        appData.player[i].card_open = [-1, -1, -1];
                    }
                }
            }
        }

        if (appData.player[0].account_status >= 7) {
            appData.player[0].is_showCard = true;
        }

        if (appData.player[0].account_status > 2) {
            setTimeout(function() {
                appData.player[0].is_showCard = true;
            }, 500);
        }
        if (appData.player[0].account_status == 3) {
            appData.showClockRobText = true;
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
            for (var i = 0; i < 6; i++) {
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
    processStartBet: function(obj) {
        var delay = 0;

        setTimeout(function() {
            for (var i = 0; i < 6; i++) {
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
            appData.game.time = Math.ceil(obj.limit_time);
            appData.bankerAnimateIndex = 0;

            appData.game.time = -1;

            viewMethods.clearBanker();
            viewMethods.robBankerAnimate(obj);

        }, delay);

    },
    processAllGamerInfo: function(obj) {

        appData.game.show_card = true;
        appData.game.show_coin = true;
        appData.clickCard2 = false;

        for (i = 0; i < 6; i++) {
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
                    if (obj.data[j].is_banker == 1) {
                        appData.player[i].is_banker = true;
                        appData.bankerAccountId = obj.data[j].account_id;
                        appData.bankerPlayer = appData.player[i];
                    } else {
                        appData.player[i].is_banker = false;
                    }
                    if (appData.player[i].account_status >= 8) {
                        appData.player[i].is_showCard = true;
                    }
                }
            }
        }
        if (appData.player[0].account_status >= 7) {
            appData.player[0].is_showCard = true;
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
        }
        if (appData.player[0].account_status > 2) {
            setTimeout(function() {
                appData.player[0].is_showCard = true;
            }, 500);
        }
        if (appData.player[0].account_status == 3) {
            appData.showClockRobText = true;
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
        console.log(appData.player);
        for (var i = 0; i < 6; i++) {
            if (appData.player[i].serial_num == obj.data.serial_num) {
                appData.player[i].nickname = obj.data.nickname;
                appData.player[i].headimgurl = obj.data.headimgurl;
                appData.player[i].account_id = obj.data.account_id;
                appData.player[i].account_score = Math.ceil(obj.data.account_score);
                appData.player[i].account_status = Math.ceil(obj.data.account_status);
                appData.player[i].online_status = Math.ceil(obj.data.online_status);
                appData.player[i].ticket_checked = obj.data.ticket_checked;
            } else {
                if (appData.player[i].account_id == obj.data.account_id) {
                    socketModule.sendRefreshRoom();
                }
            }
        }
    },
    processUpdateAccountStatus: function(obj) {

        for (var i = 0; i < 6; i++) {
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
                appData.player[i].cards = obj.data.cards.concat();
                appData.player[i].account_status = 8;
                if (appData.player[i].is_audio_point == false && appData.player[i].account_status >= 8) {
                    var audio = "audioCardType" + appData.player[i].card_type;
                    setTimeout(function() {
                        mp3AudioPlay(audio);
                    }, 100);
                    appData.player[i].is_audio_point = true;
                }
                break;
            }
        }

        if (obj.data.account_id == appData.player[0].account_id) {
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

        for (var i = 0; i < 6; i++) {
            appData.player[i].is_operation = false;
            appData.player[i].is_showCard = false;
            appData.player[i].is_showbull = false;
            appData.player[i].is_banker = false;
            appData.player[i].bullImg = "";

            if (appData.player[i].online_status == 0) {
                appData.player[i].account_status = 1;
            }

            for (var j = 0; j < obj.data.length; j++) {
                if (appData.player[i].account_id == obj.data[j].account_id) {
                    if (appData.player[i].ticket_checked == 0 && i == 0) {
                        if (appData.isAA == true) {
                            if (appData.ruleInfo.ticket_type == 2) {
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
                }
            }
        }

        appData.game.status = 2;
        appData.game.time = Math.ceil(obj.limit_time);
        viewMethods.timeCountDown();
        viewMethods.reDeal();

    },
    processBroadcastVoice: function(obj) {
        for (var i = 0; i < 6; i++) {
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

        viewMethods.showMemberScore(false);

        for (var i = 0; i < appData.player.length; i++) {
            if (appData.player[i].account_status >= 7) {
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

        if (appData.player[0].account_status >= 8 && appData.player[0].is_audio_point == false) {
            setTimeout(function() {
                mp3AudioPlay("audioCardType"+cardType);
            }, 200);

            appData.player[0].is_audio_point = true;
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
                "num": num
            });
        }
        //对积分榜排序
        appData.playerBoard.score.sort(function(a, b) {
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
                    "num": num
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
    }
};

var viewMethods = {
    clickGameOver: function() {
        viewMethods.clickShowAlert(10, '下庄之后，将以当前战绩进行结算。是否确定下庄？');
        //socketModule.sendGameOver();
    },
    clickHome: function() {
        window.location.href = globalData.baseUrl + "f/ym";
    },
    clickShowAlert: function(type, text) {
        //$(".alertText").css("top", "90px");
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
                    console.log('1365: resetMyAccountStatus');
                    viewMethods.resetMyAccountStatus();
                    appData.player[0].is_showCard = true;
                    appData.showClockRobText = true;
                    appData.isDealing = false;
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
            if (appData.clickCard2 == true) {
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
                $(".myCards .card2").addClass("card-flipped");
                appData.clickCard2 = true;

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
                $(".myCards .card2").addClass("card-flipped");

                setTimeout(function() {
                    if (appData.clickCard2 != true) {
                        appData.showClickShowCard = true;
                    }
                }, 500);
            }, 350);
        }
    },
    resetCardOver: function(num) {
        if (num == 1) {
            $(".myCards .card00").css("left", "2%");
            $(".myCards .card01").css("left", "26%");
            $(".myCards .card02").css("left", "50%");
        } else if (num == 2) {
            $(".cardOver .card211").css("right", "0%");
            $(".cardOver .card221").css("right", "4%");
            $(".cardOver .card231").css("right", "8%");
        } else if (num == 3) {
            $(".cardOver .card311").css("right", "0%");
            $(".cardOver .card321").css("right", "4%");
            $(".cardOver .card331").css("right", "8%");
        } else if (num == 4) {
            $(".cardOver .card431").css("left", "48%");
            $(".cardOver .card421").css("left", "52%");
            $(".cardOver .card411").css("left", "56%");
        } else if (num == 5) {
            $(".cardOver .card531").css("left", "8%");
            $(".cardOver .card521").css("left", "12%");
            $(".cardOver .card511").css("left", "16%");
        } else if (num == 6) {
            $(".cardOver .card631").css("left", "8%");
            $(".cardOver .card621").css("left", "12%");
            $(".cardOver .card611").css("left", "16%");
        }
    },
    myCardOver: function(is_seen) {
        if (appData.player[0].is_showbull == true) {
            return;
        }

        viewMethods.resetCardOver(1);

        if (is_seen) {
            setTimeout(function() {
                $(".myCards .card00").animate({left: "22%"}, 400);
                $(".myCards .card01").animate({left: "32%"}, 400);
                $(".myCards .card02").animate({left: "42%"}, 400);
            }, 0);
        } else {
            setTimeout(function() {
                $(".myCards .card00").animate({left: "30%"}, 400);
                $(".myCards .card01").animate({left: "40%"}, 400);
                $(".myCards .card02").animate({left: "50%"}, 400);
            }, 0);
        }

        appData.player[0].is_showbull = true;
    },
    cardOver: function(num, is_seen) {
        if (num <= 1) {
            return;
        }

        if (appData.player[num - 1].is_showbull == true) {
            return;
        }

        appData.player[num - 1].is_showbull = true;
        viewMethods.resetCardOver(num);

        setTimeout(function() {
            if (num == 2 || num == 3) {
                $(".cardOver .card" + num + "11").animate({right: "0"}, 250);
                $(".cardOver .card" + num + "21").animate({right: "0"}, 250);
                $(".cardOver .card" + num + "31").animate({right: "0"}, 250);
                if (!is_seen) {
                    setTimeout(function() {
                        $(".cardOver .card" + num).addClass("card-flipped");
                        $(".cardOver .card" + num + "11").animate({right: "0"}, 400);
                        $(".cardOver .card" + num + "21").animate({right: "6%"}, 400);
                        $(".cardOver .card" + num + "31").animate({right: "12%"}, 400);
                    }, 250);
                } else {
                    setTimeout(function() {
                        $(".cardOver .card" + num).addClass("card-flipped");
                        $(".cardOver .card" + num + "11").animate({right: "0"}, 400);
                        $(".cardOver .card" + num + "21").animate({right: "6%"}, 400);
                        $(".cardOver .card" + num + "31").animate({right: "16%"}, 400);
                    }, 250);
                }
            } else if (num == 4) {
                $(".cardOver .card" + num + "31").animate({left: "38%"}, 250);
                $(".cardOver .card" + num + "21").animate({left: "38%"}, 250);
                $(".cardOver .card" + num + "11").animate({left: "38%"}, 250);
                if (!is_seen) {
                    setTimeout(function() {
                        $(".cardOver .card" + num).addClass("card-flipped");
                        $(".cardOver .card" + num + "31").animate({left: "49%"}, 400);
                        $(".cardOver .card" + num + "21").animate({left: "55%"}, 400);
                        $(".cardOver .card" + num + "11").animate({left: "61%"}, 400);
                    }, 250);
                } else {
                    setTimeout(function() {
                        $(".cardOver .card" + num).addClass("card-flipped");
                        $(".cardOver .card" + num + "31").animate({left: "47%"}, 400);
                        $(".cardOver .card" + num + "21").animate({left: "57%"}, 400);
                        $(".cardOver .card" + num + "11").animate({left: "63%"}, 400);
                    }, 250);
                }
            } else if (num == 5 || num == 6) {
                $(".cardOver .card" + num + "31").animate({left: "0"}, 250);
                $(".cardOver .card" + num + "21").animate({left: "0"}, 250);
                $(".cardOver .card" + num + "11").animate({left: "0"}, 250);
                if (!is_seen) {
                    setTimeout(function() {
                        $(".cardOver .card" + num).addClass("card-flipped");
                        $(".cardOver .card" + num + "31").animate({left: "12%"}, 400);
                        $(".cardOver .card" + num + "21").animate({left: "18%"}, 400);
                        $(".cardOver .card" + num + "11").animate({left: "24%"}, 400);
                    }, 250);
                } else {
                    setTimeout(function() {
                        $(".cardOver .card" + num).addClass("card-flipped");
                        $(".cardOver .card" + num + "31").animate({left: "12%"}, 400);
                        $(".cardOver .card" + num + "21").animate({left: "22%"}, 400);
                        $(".cardOver .card" + num + "11").animate({left: "28%"}, 400);
                    }, 250);
                }
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

        for (var i = 0; i < 6; i++) {
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
        appData.playerBoard.room = '房间号:' + globalData.roomNumber;
        appData.playerBoard.record = str;
        appData.base_score = appData.game.base_score;

        if (balance_scoreboard != undefined && balance_scoreboard != "-1") {
            console.log(balance_scoreboard);
            socketModule.processBalanceScoreboard(balance_scoreboard);
        }

        for (i = 0; i < 6; i++) {
            appData.player[i].playing_status = 0;
            appData.player[i].is_win = false;
            appData.player[i].is_operation = false;
            appData.player[i].win_type = 0;
            appData.player[i].win_show = false;
            appData.player[i].card = [];
            appData.player[i].card_type = 0;
            appData.player[i].is_showCard = false;
            appData.player[i].multiples = 0;
            appData.player[i].bankerMultiples = 0;
            appData.player[i].is_audio_point = false;
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
        // $(".message .textPart").animate({
        //     height:"400px"
        // });
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
            //判断倍数图片
            if (appData.player[i].multiples > 0) {
                appData.player[i].timesImg = globalData.imageUrl + "files/images/sangong/text_times" + appData.player[i].multiples + ".png";
            }

            if (appData.player[i].bankerMultiples > 0) {
                appData.player[i].bankerTimesImg = globalData.imageUrl + "files/images/sangong/text_times" + appData.player[i].bankerMultiples + ".png";
            }

            //判断图片
            if (appData.player[i].card_type >= 0) {
                var cardType = parseInt(appData.player[i].card_type);
                appData.player[i].bullImg = globalData.baseUrl + "files/images/sangong/point" + cardType + ".png";
            }

            if (appData.player[i].account_status == 4) {
                appData.player[i].robImg = globalData.imageUrl + "files/images/sangong/text_notrob.png";
            } else if (appData.player[i].account_status == 5) {
                appData.player[i].robImg = globalData.imageUrl + "files/images/sangong/text_rob.png";
            } else if (appData.player[i].account_status == 7) {
                //未摊牌
                if (i == 0) {
                    viewMethods.seeMyCard();
                }
            } else if (appData.player[i].account_status == 8) {
                //摊牌
                if (i == 0) {
                    viewMethods.myCardOver(appData.player[i].is_seen);
                } else {
                    viewMethods.cardOver(appData.player[i].num, appData.player[i].is_seen);
                }
            }
        }
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
    clickRobBanker: function(multiples) {
        viewMethods.showRobBankerText();
        socketModule.sendGrabBanker(multiples);
        if (appData.ruleInfo.banker_mode == 2) {
            appData.player[0].bankerMultiples = multiples;
            if (appData.player[0].bankerMultiples > 0) {
                appData.player[0].bankerTimesImg = globalData.imageUrl + "files/images/sangong/text_times" + appData.player[0].bankerMultiples + ".png";
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
            appData.player[0].timesImg = globalData.imageUrl + "files/images/sangong/text_times" + appData.player[0].multiples + ".png";
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
        appData.bankerAnimateDuration = parseInt(3e3 / totalCount);
    },
    robBankerWithoutAnimate: function() {

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

        appData.game.time = 11;
        if (appData.game.time > 0) {
            viewMethods.timeCountDown();
        }
    },
    robBankerAnimate: function(obj) {
        for (i = 0; i < appData.bankerArray.length; i++) {
            imgId = "#banker" + appData.bankerArray[i];
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
                top: "10%",
                left: "10%",
                width: "80%",
                height: "80%"
            });

            $("#bankerAnimate1" + bankerNum).css({
                top: "5%",
                left: "5%",
                width: "90%",
                height: "90%"
            });

            $("#bankerAnimate" + bankerNum).show();
            $("#bankerAnimate1" + bankerNum).show();

            $("#bankerAnimate1" + bankerNum).animate({
                top: "5%",
                left: "5%",
                width: "90%",
                height: "90%"
            }, 400, function() {
                $("#bankerAnimate1" + bankerNum).animate({
                    top: "10%",
                    left: "10%",
                    width: "80%",
                    height: "80%"
                }, 400, function() {
                    $("#bankerAnimate1" + bankerNum).hide();
                });
            });

            $("#bankerAnimate" + bankerNum).animate({
                top: "-10%",
                left: "-10%",
                width: "120%",
                height: "120%"
            }, 400, function() {
                $("#bankerAnimate" + bankerNum).animate({
                    top: "10%",
                    left: "10%",
                    width: "80%",
                    height: "80%"
                }, 400, function() {
                    $("#bankerAnimate" + bankerNum).hide();

                    setTimeout(function() {
                        console.log('1803: resetMyAccountStatus');
                        appData.game.show_coin = true;
                        appData.showClockRobText = false;
                        appData.showClockBetText = true;
                        appData.isFinishBankerAnimate = true;
                        viewMethods.resetMyAccountStatus();
                        viewMethods.updateAllPlayerStatus();
                    }, 10);

                    appData.game.time = 11;
                    if (appData.game.time > 0) {
                        viewMethods.timeCountDown();
                    }
                });
            });

            return;
        }

        var accountId = appData.bankerArray[appData.bankerAnimateIndex];
        imgId = "#banker" + accountId;

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
        } else {
            $(".memberScoreText1").hide();
            $(".memberScoreText2").hide();
            $(".memberScoreText3").hide();
            $(".memberScoreText4").hide();
            $(".memberScoreText5").hide();
            $(".memberScoreText6").hide();
        }
    },
    winAnimate: function(obj) {
        appData.isFinishWinAnimate = false;
        $(".cards").removeClass("card-flipped");
        $(".myCards").removeClass("card-flipped");
        var winnerNums = [];
        var loserNums = [];

        appData.bankerPlayerNum = appData.bankerPlayer.num;

        for (i = 0; i < obj.data.winner_array.length; i++) {
            for (j = 0; j < appData.player.length; j++) {
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

        for (i = 0; i < obj.data.loser_array.length; i++) {
            for (j = 0; j < appData.player.length; j++) {
                if (obj.data.loser_array[i].account_id == appData.player[j].account_id) {
                    if (appData.player[j].num != appData.bankerPlayerNum) {
                        loserNums.push(appData.player[j].num);
                    }
                }
            }
        }

        viewMethods.resetCoinsPosition();
        $("#playerCoins").show();
        for (i = 1; i < 7; i++) {
            viewMethods.showCoins(i, false);
        }

        //把赢家玩家金币暂时放到庄家位置
        for (i = 0; i < winnerNums.length; i++) {
            for (j = 0; j < 8; j++) {
                if (appData.bankerPlayerNum == 1) {
                    $(".memberCoin" + winnerNums[i] + j).css("top", "82%");
                    $(".memberCoin" + winnerNums[i] + j).css("left", "48%");
                } else if (appData.bankerPlayerNum == 2) {
                    $(".memberCoin" + winnerNums[i] + j).css("top", "46%");
                    $(".memberCoin" + winnerNums[i] + j).css("left", "87%");
                } else if (appData.bankerPlayerNum == 3) {
                    $(".memberCoin" + winnerNums[i] + j).css("top", "31%");
                    $(".memberCoin" + winnerNums[i] + j).css("left", "87%");
                } else if (appData.bankerPlayerNum == 4) {
                    $(".memberCoin" + winnerNums[i] + j).css("top", "11%");
                    $(".memberCoin" + winnerNums[i] + j).css("left", "48%");
                } else if (appData.bankerPlayerNum == 5) {
                    $(".memberCoin" + winnerNums[i] + j).css("top", "31%");
                    $(".memberCoin" + winnerNums[i] + j).css("left", "9%");
                } else if (appData.bankerPlayerNum == 6) {
                    $(".memberCoin" + winnerNums[i] + j).css("top", "46%");
                    $(".memberCoin" + winnerNums[i] + j).css("left", "9%");
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
                        left: "48%"
                    }, 150 + 150 * j);
                } else if (appData.bankerPlayerNum == 2) {
                    $(".memberCoin" + loserNums[i] + j).animate({
                        top: "46%",
                        left: "87%"
                    }, 150 + 150 * j);
                } else if (appData.bankerPlayerNum == 3) {
                    $(".memberCoin" + loserNums[i] + j).animate({
                        top: "31%",
                        left: "87%"
                    }, 150 + 150 * j);
                } else if (appData.bankerPlayerNum == 4) {
                    $(".memberCoin" + loserNums[i] + j).animate({
                        top: "11%",
                        left: "48%"
                    }, 150 + 150 * j);
                } else if (appData.bankerPlayerNum == 5) {
                    $(".memberCoin" + loserNums[i] + j).animate({
                        top: "31%",
                        left: "9%"
                    }, 150 + 150 * j);
                } else if (appData.bankerPlayerNum == 6) {
                    $(".memberCoin" + loserNums[i] + j).animate({
                        top: "46%",
                        left: "9%"
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
                                left: "48%"
                            }, 150 + 150 * j);
                        } else if (winnerNums[i] == 2) {
                            $(".memberCoin" + winnerNums[i] + j).animate({
                                top: "46%",
                                left: "87%"
                            }, 150 + 150 * j);
                        } else if (winnerNums[i] == 3) {
                            $(".memberCoin" + winnerNums[i] + j).animate({
                                top: "31%",
                                left: "87%"
                            }, 150 + 150 * j);
                        } else if (winnerNums[i] == 4) {
                            $(".memberCoin" + winnerNums[i] + j).animate({
                                top: "11%",
                                left: "48%"
                            }, 150 + 150 * j);
                        } else if (winnerNums[i] == 5) {
                            $(".memberCoin" + winnerNums[i] + j).animate({
                                top: "31%",
                                left: "9%"
                            }, 150 + 150 * j);
                        } else if (winnerNums[i] == 6) {
                            $(".memberCoin" + winnerNums[i] + j).animate({
                                top: "46%",
                                left: "9%"
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
        $(".memberScoreText6").fadeIn(200, function() {
            viewMethods.gameOverNew(obj.data.score_board, obj.data.balance_scoreboard);
            setTimeout(function() {
                $(".memberScoreText1").fadeOut("slow");
                $(".memberScoreText2").fadeOut("slow");
                $(".memberScoreText3").fadeOut("slow");
                $(".memberScoreText4").fadeOut("slow");
                $(".memberScoreText5").fadeOut("slow");
                $(".memberScoreText6").fadeOut("slow");
                for (var i = 0; i < 6; i++) {
                    if (appData.player[i].account_status >= 6) {
                        appData.player[i].is_banker = appData.player[i].account_id == appData.bankerID;
                    }
                    appData.player[i].account_status = 1;
                }
            }, 2e3);

            appData.isFinishWinAnimate = true;

            if (obj.data.total_num == obj.data.game_num) {
                setTimeout(function() {
                    viewMethods.roundEnd();
                    newNum = obj.data.room_number;
                }, 1e3);
            }

        });
    },
    resetCoinsPosition: function() {
        for (var i = 1; i < 7; i++) {
            for (var j = 0; j < 8; j++) {
                if (i == 1) {
                    $(".memberCoin" + i + j).css({
                        top: "82%",
                        left: "48%"
                    });
                } else if (i == 2) {
                    $(".memberCoin" + i + j).css({
                        top: "46%",
                        left: "87%"
                    });
                } else if (i == 3) {
                    $(".memberCoin" + i + j).css({
                        top: "31%",
                        left: "87%"
                    });
                } else if (i == 4) {
                    $(".memberCoin" + i + j).css({
                        top: "11%",
                        left: "48%"
                    });
                } else if (i == 5) {
                    $(".memberCoin" + i + j).css({
                        top: "31%",
                        left: "9%"
                    });
                } else if (i == 6) {
                    $(".memberCoin" + i + j).css({
                        top: "46%",
                        left: "9%"
                    });
                }
            }
        }
    },
    showCoins: function(num, isShow) {
        if (isShow) {
            for (i = 0; i < 8; i++) {
                $(".memberCoin" + num + i).show();
            }
        } else {
            for (i = 0; i < 8; i++) {
                $(".memberCoin" + num + i).hide();
            }
        }
    }
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
            audioModule.audioBuffers[name] = { "name": name, "buffer": buffer, "source": null };
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
        this.loadAudioFile(this.baseUrl + 'files/audio/sangong/background3.mp3', "backMusic");

        var audioUrl = ["nobanker.m4a", "robbanker.m4a", "point0.m4a", "point1.m4a", "point2.m4a", "point3.m4a", "point4.m4a", "point5.m4a", "point6.m4a", "point7.m4a", "point8.m4a", "point9.m4a", "point10.m4a", "point11.m4a", "point12.m4a", "point13.m4a", "point14.m4a", "point15.m4a", "point16.m4a", "coin.mp3", "audio1.m4a"];
        var audioName = ["audioNoBanker", "audioRobBanker", "audioPoint0", "audioPoint1", "audioPoint2", "audioPoint3", "audioPoint4", "audioPoint5", "audioPoint6", "audioPoint7", "audioPoint8", "audioPoint9", "audioPoint10", "audioPoint11", "audioPoint12", "audioPoint13", "audioPoint14", "audioPoint15", "audioPoint16", "audioCoin", "audio1"];
        for (i = 0; i < audioUrl.length; i++) {
            this.loadAudioFile(this.baseUrl + 'files/audio/sangong/' + audioUrl[i], audioName[i]);
        }
        var audioTimesUrl = ["times1.m4a", "times2.m4a", "times3.m4a", "times4.m4a", "times5.m4a", "times8.m4a", "times10.m4a"];
        var audioTimesName = ["audioTimes1", "audioTimes2", "audioTimes3", "audioTimes4", "audioTimes5", "audioTimes8", "audioTimes10"];
        for (i = 0; i < audioTimesUrl.length; i++) {
            this.loadAudioFile(this.baseUrl + 'files/audio/sound/' + audioTimesUrl[i], audioTimesName[i]);
        }

        var audioMessageUrl = ["message9.m4a", "message10.m4a", "message11.m4a", "message1.m4a", "message2.m4a", "message3.m4a", "message4.m4a", "message12.m4a", "message6.m4a", "message7.m4a", "message8.m4a"];
        var audioMessageName = ["message0", "message1", "message2", "message3", "message4", "message5", "message6", "message7", "message8", "message9", "message10"];
        for (var i = 0; i < audioMessageUrl.length; i++) {
            this.loadAudioFile(this.baseUrl + 'files/audio/sound/' + audioMessageUrl[i], audioMessageName[i]);
        }
    }
};

audioModule.initModule(globalData.fileUrl);

//Vue生命周期
var vueLife = {
    vmCreated: function() {
        console.log('vmCreated');
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

//Vue方法
var methods = {
    clickGameOver: viewMethods.clickGameOver,
    showInvite: viewMethods.clickShowInvite,
    showAlert: viewMethods.clickShowAlert,
    showMessage: viewMethods.showMessage,
    closeInvite: viewMethods.clickCloseInvite,
    closeAlert: viewMethods.clickCloseAlert,
    sitDown: viewMethods.clickSitDown,
    seeMyCard3: viewMethods.seeMyCard3,
    imReady: viewMethods.clickReady,
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
    showPlayRule: function() {
        if (appData.roomStatus == 4) {
            return;
        }
        appData.playRule.isShow = true;
    },
    cancelPlayRule: function() {
        appData.playRule.isShow = false;
    },
    showGameRule: function() {
        if (appData.roomStatus == 4) {
            return;
        }
        $('.createRoom .mainPart').css('height', '36vh');
        $('.createRoom .mainPart .blueBack').css('height', '30vh');
        appData.ruleInfo.isShowRule = true;
    },
    cancelGameRule: function() {
        appData.ruleInfo.isShowRule = false;
        $('.createRoom .mainPart').css('height', '36vh');
        $('.createRoom .mainPart .blueBack').css('height', '30vh');
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
    reloadView: function() {
        window.location.href = window.location.href + "&id=" + 1000 * Math.random();
    }
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

var resetState = function resetState() {
    appData.game.show_score = false;
    appData.game.show_bettext = false;
    appData.clickCard2 = false;

    for (i = 0; i < 6; i++) {
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
            card: [],
            is_showCard: false,
            card_type: 0,
            is_banker: false,
            multiples: 0,
            bankerMultiples: 0,
            timesImg: "",
            bankerTimesImg: "",
            robImg: "",
            pointImg: "",
            single_score: 0,
            messageOn: false,
            is_seen: false,
            is_audio_point: false,
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

    for (i = 0; i < 6; i++) {
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
            card: [],
            is_showCard: false,
            card_type: 0,
            is_banker: false,
            multiples: 0,
            bankerMultiples: 0,
            timesImg: "",
            bankerTimesImg: "",
            robImg: "",
            pointImg: "",
            single_score: 0,
            messageOn: false,
            is_seen: false,
            is_audio_point: false,
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
        score: [],
        round: 0,
        record: "",
        room: ""
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
            isBigWinner: 0
        });

        if (appData.player[i].online_status == 1) {
            appData.player[i] = {
                account_status: 0,
                playing_status: 0,
                is_win: false,
                is_operation: false,
                win_type: 0,
                win_show: false,
                card: [],
                card_type: 0,
                ticket_checked: 0,
                account_score: 0,
                is_showCard: false,
                is_banker: false,
                multiples: 0,
                bankerMultiples: 0,
                timesImg: "",
                bankerTimesImg: "",
                robImg: "",
                pointImg: "",
                single_score: 0,
                num: i + 1,
                is_seen: false,
                is_audio_point: false
            };
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
                card: [],
                is_showCard: false,
                card_type: 0,
                is_banker: false,
                multiples: 0,
                bankerMultiples: 0,
                timesImg: "",
                bankerTimesImg: "",
                robImg: "",
                pointImg: "",
                single_score: 0,
                is_audio_point: false,
                is_seen: false,
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
    }, 3000);

    socketModule.sendPrepareJoinRoom();
};

var wsMessageCallback = function wsMessageCallback(evt) {
    appData.connectOrNot = true;

    if (evt.data == '@') {
        appData.socketStatus = 0;
        return 0;
    }

    var obj = eval('(' + evt.data + ')');
    console.log('wsMessageCallback: ' + JSON.stringify(obj));

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
        } else if (obj.operation == wsOperation.RefreshRoom) {
            window.location.href = window.location.href + "&id=" + 10000 * Math.random();
        }

        appData.player[0].is_operation = false;
    }
    else {
        if (obj.operation == wsOperation.PrepareJoinRoom) {
            socketModule.processPrepareJoinRoom(obj);
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


function getShareContent() {
    shareContent = "\n";

    if (appData.ruleInfo.banker_mode == 1) {
        shareContent += '模式：自由抢庄 ';
    } else if (appData.ruleInfo.banker_mode == 2) {
        shareContent += '模式：经典三公 ';
    }

    if (appData.ruleInfo.baseScore == 1) {
        shareContent += '底分：1分';
    } else if (appData.ruleInfo.baseScore == 3) {
        shareContent += '底分：3分';
    } else if (appData.ruleInfo.baseScore == 5) {
        shareContent += '底分：5分';
    }

    if (appData.ruleInfo.rule_type == 1) {
        shareContent += '  规则：天公X10-雷公X7-地公X5';
    } else {
        shareContent += '  规则：暴玖X9';
    }

    if (appData.ruleInfo.ticket_type == 1) {
        shareContent += '  局数：10局x1张房卡';
    } else {
        shareContent += '  局数：20局x2张房卡';
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
            imgUrl: globalData.imageUrl + "files/images/sangong/share_icon.jpg",
            success: function() {},
            cancel: function() {}
        });
        wx.onMenuShareAppMessage({
            title: globalData.shareTitle + '(房间号:' + globalData.roomNumber + ')',
            desc: shareContent,
            link: globalData.roomUrl,
            imgUrl: globalData.imageUrl + "files/images/sangong/share_icon.jpg",
            success: function() {},
            cancel: function() {}
        });

    },
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
        imgUrl: globalData.imageUrl + "files/images/sangong/share_icon.jpg",
        success: function() {},
        cancel: function() {}
    });
    wx.onMenuShareAppMessage({
        title: globalData.shareTitle + '(房间号:' + globalData.roomNumber + ')',
        desc: shareContent,
        link: globalData.roomUrl,
        imgUrl: globalData.imageUrl + "files/images/sangong/share_icon.jpg",
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
        taintTest: true,
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
};

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