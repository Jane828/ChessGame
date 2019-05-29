var audioOn = false;
var ws;
var screenData = {
    width: window.innerWidth,
    height: window.innerHeight,
    initialize: function() {
        if (screenData.height > screenData.width) {
            $(".main").width(screenData.height);
            $(".main").height(screenData.width);
            $(".main").css("top", (screenData.height - screenData.width) / 2 + "px");
            $(".main").css("left", -(screenData.height - screenData.width) / 2 + "px");
            $(".main").css("background-size", screenData.height + "px " + screenData.width + "px");

            if (screenData.height > screenData.width * 1.6267) {
                $(".playGround").width(screenData.width * 1.6267);
                $(".playGround").height(screenData.width);
                $(".playGround").css("margin-top", -screenData.width / 2 + "px");
                $(".playGround").css("margin-left", -screenData.width * 0.8133 + "px");
            } else {
                $(".playGround").width(screenData.height);
                $(".playGround").height(screenData.height * 0.6148);
                $(".playGround").css("margin-top", -screenData.height * 0.3074 + "px");
                $(".playGround").css("margin-left", -screenData.height / 2 + "px");
            }
            if (globalData.room_status != 4) {
                $("#loading").hide();
                $(".main").show();
                $(".outPart").show();
            }

        } else {
            alert("请关闭旋转后刷新页面。");
        }
    }
}

var editAudioInfo = {
    isShow: false,
    backMusic: 1,
    messageMusic: 1,
};

var audioInfo = {
    backMusic: 1,
    messageMusic: 1,
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

var httpModule = {
    getAuthcode: function(phone) {
        var data = { "dealer_num": globalData.dealerNum, "phone": phone ,'session':globalData.session};

        Vue.http.post(globalData.baseUrl + 'account/getMobileSms', data).then(function(response) {
            logMessage(response.body);
            var bodyData = response.body;

            if (bodyData.result == 0) {
                appData.authcodeTime = 60;
                authcodeTimer();
                appData.authcodeType = 2;

            } else {
                controlMethod.showAlert(21, bodyData.result_message);
            }

        }, function(response) {
            controlMethod.showAlert(21, '获取验证码失败');
        });
    },
    bindPhone: function(phone, authcode) {
        var data = { "dealer_num": globalData.dealerNum, "phone": phone, "code": authcode ,'session':globalData.session};

        Vue.http.post(globalData.baseUrl + 'account/checkSmsCode', data).then(function(response) {

            var bodyData = response.body;

            if (bodyData.result == 0) {
                appData.isAuthPhone = 0;
                appData.phone = appData.sPhone;

                if (bodyData.data.card_count != null && bodyData.data.card_count != undefined && bodyData.data.card_count != '') {
                    appData.roomCard = parseInt(appData.roomCard) + parseInt(bodyData.data.card_count);
                }

                if (bodyData.data.account_id != userData.accountId) {
                    controlMethod.showAlert(23, bodyData.result_message);
                } else {
                    controlMethod.showAlert(22, bodyData.result_message);
                }

                appData.sPhone = '';
                appData.sAuthcode = '';

            } else {
                controlMethod.showAlert(21, bodyData.result_message);
            }

        }, function(response) {
            appData.authcodeTime = 0;
            controlMethod.showAlert(21, "绑定失败");
        });
    },
    getActivityInfo: function() {
        Vue.http.post(globalData.baseUrl + 'f/getActivityInfo', { "account_id": userData.accountId, "dealer_num": globalData.dealerNum,'session':globalData.session }).then(function(response) {
            console.log(response.body);
            var bodyData = response.body;
            if (bodyData.result == 0) {
                if (bodyData.data.length == 0) {
                    reconnectSocket();
                    appData.is_connect = true;
                } else {
                    appData.activity = bodyData.data.concat();
                    controlMethod.showAlert(5, appData.activity[0].content)
                }
            } else {
                alert(bodyData.result_message);
            }

        }, function(response) {

        });
    },

}

var appData = {
    isAA: false, //是否AA房卡
    isAutoActive: true, //是否自动激活
    isShop: false, //是否有商城
    game_staus: globalData.room_status,
    player: [],
    playerBoard: { score: [], record: "" },
    game: {
        light: 0,
        room: 0,
        room_number: globalData.roomNumber,
        room_url: 0,
        status: 0,
        limit_time: 0,
        time: { time: 0, firstNum: 10, lastNum: 10, isPlaying: false },
        round: 0,
        total_num: "",
        horse_count: 0,
        ticket_count: 1,
        joker: 0,
        qianggang: 0,
        chengbao: 0,
        scoreboard: "",
        joker_card: 0,
        flip_card: 0,
        qianggang_card: 0,
        remain_count: 0,
        countdown: 0,
        maxWin: -1,
        last_user: -1,
        last_discard: -1,
        ma: [],
        endStep: 0,
        isShowEnd: false,
        base_score: 10,
        showGangScore: false,
        positionList1: [],
        positionList2: [],
    },
    userInfo: { card: Math.ceil(globalData.card) },
    alertType: 0,
    alertText: "",
    returnCard: { show: false, from: -1, to: -1, card: "" },
    fromX: 0,
    fromY: 0,
    animate: { animate1: 1, animate2: 0, animate3: 0, },
    position: { positionReady: false, longitude: "", latitude: "" },
    rullInfo: { horse_count: 0, ticket_count: 1, joker: 0, qianggang: 0, chengbao: 0 },
    createInfo: { isShow: false, horse_count: 0, ticket_count: 1, newRoom: false },
    isShowAlert: false,
    isShowInvite: false,
    isShowRecord: false,
    isShowRull: false,
    select: -1,
    ticket_count: -1,
    isShowShop: false,
    isShowShopLoading: false,
    socketStatus: 0,
    heartbeat: null,
    connectOrNot: true,
    wsocket: ws,
    activity: [],
    recordList: [],
    roomCardInfo: [],
    isShowMessage: false,
    message: [
        { "num": 0, "text": "please快点打牌" },
        { "num": 1, "text": "我出去叫人" },
        { "num": 2, "text": "你的牌好靓哇" },
        { "num": 3, "text": "我当年横扫澳门五条街" },
        { "num": 4, "text": "算你牛逼" },
        { "num": 5, "text": "别吹牛逼，有本事干到底" },
        { "num": 6, "text": "输得裤衩都没了" },
        { "num": 7, "text": "我给你们送温暖了" },
        { "num": 8, "text": "谢谢老板" }
    ],
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
    bScroll: null,
}
var controlMethod = {
    initialize: function() {
        screenData.initialize();

        appData.player = [];
        for (var i = 0; i < 4; i++) {
            appData.player.push({
                "num": i + 1
            });
        }
        if (appData.isAuthPhone != 1) {
            httpModule.getActivityInfo();
        }

    },
    showRecord: function() {
        sendMethod.sendHistoryScoreboard();
    },
    closeRecord: function() {
        appData.isShowRecord = false;
    },
    showRull: function() {
        appData.isShowRull = true;
    },
    closeRull: function() {
        appData.isShowRull = false;
    },
    showInvite: function() {
        appData.isShowInvite = true;
    },
    closeInvite: function() {
        appData.isShowInvite = false;
    },
    showAlert: function(type, text) {
        $(".alertText").css("top", "90px")
        appData.alertText = text;
        appData.alertType = type;
        appData.isShowAlert = true;
        setTimeout(function() {
            $(".alertText").css("top", 102 - ($(".alertText").height() / 2) + "px")
        }, 0)
    },
    closeAlert: function() {
        if (appData.alertType == 1) {
            controlMethod.showShop();
            if (!appData.is_connect) {
                setTimeout(function() {
                    reconnectSocket();
                    appData.is_connect = true;
                }, 300)
            }
        } else if (appData.alertType == 6) {
            appData.isShowAlert = false;
            sendMethod.sendJoinRoom();
        } else if (appData.alertType == 8) {
            appData.isShowAlert = false;
            operationMethod.endRound();
        } else if (appData.alertType == 22) {
            appData.isShowAlert = false;
            httpModule.getActivityInfo();
        } 
		else if (appData.alertType == 31) {
			window.location.href=window.location.href+"?id="+10000*Math.random();
		} 
         else {
            appData.isShowAlert = false;
        }
    },
    sitDown: function() {
        appData.isShowAlert = false;
        sendMethod.sendJoinRoom();
    },
    /////////创建房间		
    home: function() {
        window.location.href = globalData.baseUrl + "f/ym";
    },
   
    /////////////////语音    
    messageSay: function(num1, num2) {
        appData.player[num1].messageOn = true;
        appData.player[num1].messageText = appData.message[num2].text;
        setTimeout(function() {
            appData.player[num1].messageOn = false;
        }, 2500);
    },
    m4aAudioPlay: function(a) {
        if (!audioOn) {
            return 0;
        }
        if (a == "backMusic") {
            playSound(a, "loop");
        } else {
            playSound(a);
        }
    },
    mp3AudioPlay: function(a) {
        if (!audioOn) {
            return 0;
        }
        playSound(a);
    },
    stopAudio: function(name) {
        stopSound(name);
    },
    showMessage: function() {
        appData.isShowMessage = true;
        disable_scroll();
    },
    hideMessage: function() {
        appData.isShowMessage = false;
        enable_scroll();
    },
    messageOn: function(num) {
        sendMethod.sendBroadcastVoice(num);
        controlMethod.m4aAudioPlay("message" + num);
        controlMethod.messageSay(0, num);
        controlMethod.hideMessage();
    },
}



var operationMethod = {
    click: function(num1, num2) {
        if (appData.player[0].is_operation) {
        	setTimeout(function(){appData.player[0].is_operation=false;},500)
            console.log("is_operation")
            return 0;
        }
        if (num1 == 0) {
            sendMethod.sendReadyStart();
            appData.player[0].is_operation = true;
        } else if (num1 == 1) {
            sendMethod.sendChooseCard(num2);
            controlMethod.m4aAudioPlay(num2 % 100);
            appData.player[0].is_operation = true;
        } else if (num1 == 4) {
            if (num2 == 5) {
                sendMethod.sendQiangGangHu(0);
            } else {
                sendMethod.sendPassCard();
            }
            console.log("过");
            appData.player[0].is_operation = true;
        } else if (num1 == 5) {
            console.log("碰");
            sendMethod.sendPengCard();
            appData.player[0].is_operation = true;
        } else if (num1 == 6) {
            console.log("杠");
            if (num2 == 1) {
                sendMethod.sendAnGang();
            } else if (num2 == 2) {
                sendMethod.sendJiaGang();
            } else if (num2 == 3) {
                sendMethod.sendBaoGang();
            }

            appData.player[0].is_operation = true;
        } else if (num1 == 7) {
            console.log("胡");
            sendMethod.sendHuCard();
            appData.player[0].is_operation = true;
        } else if (num1 == 8) {
            console.log("抢杠胡");
            sendMethod.sendQiangGangHu(1, appData.game.qianggang_card);
            appData.player[0].is_operation = true;
        }
    },
    chooseCard: function(num) {
        if (appData.animate.animate1 != 1)
            return 0;
        if (num == -1) {
            if (!appData.player[0].cardNew.isSelect) {
                for (var i = 0; i < appData.player[0].card.length; i++) {
                    appData.player[0].card[i].isSelect = false;
                }
                appData.player[0].cardNew.isSelect = true;
            } else {
                if (!appData.player[0].is_operation && appData.player[0].playing_status == 2) {
                    appData.player[0].cardNew.isShow = false;
                    appData.player[0].cardSet = appData.player[0].cardNew.card;
                    operationMethod.dicard(0);
                    operationMethod.click(1, appData.player[0].cardNew.card)
                } else {
                    appData.player[0].cardNew.isSelect = false;
                }
            }
        } else {
            if (!appData.player[0].card[num].isSelect) {
                for (var i = 0; i < appData.player[0].card.length; i++) {
                    appData.player[0].card[i].isSelect = false;
                }
                appData.player[0].cardNew.isSelect = false;
                appData.player[0].card[num].isSelect = true;
            } else {
                if (!appData.player[0].is_operation && appData.player[0].playing_status == 2) {
                    appData.player[0].cardSet = appData.player[0].card[num].card;
                    operationMethod.click(1, appData.player[0].card[num].card);
                    appData.player[0].card.splice(num, 1);
                    operationMethod.cardMove(num);
                    operationMethod.dicard(0);
                } else {
                    appData.player[0].card[num].isSelect = false;
                }
            }
        }
    }, /////选牌并出牌	
    cardMove: function(num0) {
        var num = appData.player[0].card.length;
        if (!appData.player[0].cardNew.isShow) {
            appData.player[0].card.splice(num, 1);
            for (var i = 0; i < appData.player[0].card.length; i++) {
                appData.player[0].card[i].num = i;
            }
            return 0;
        }
        for (var i = 0; i < appData.player[0].card.length; i++) {
            if (appData.player[0].card[i].card <= appData.player[0].cardNew.card) {
                num = i;
                break;
            }
        }
        if (num != 0) {
            $(".mine .cardNew").addClass("rotate");
            $(".mine .cardNew").animate({ "margin-top": "-12%" }, 350, function() {
                $(".mine .cardNew").animate({ "right": 6 * num + 12 + "%" }, 300, function() {
                    $(".mine .cardNew").addClass("reRotate");
                    $(".mine .cardNew").animate({ "margin-top": "0" }, 350, function() {
                        operationMethod.insertCard(num);
                    });
                    $(".mine .myCard .card").eq(num).animate({ "margin-right": "6%" }, 200);
                });
            });
        } else {
            $(".mine .cardNew").animate({ "right": "12%" }, 400, function() { operationMethod.insertCard(num); });
            /*	if(num0==0)
            		$(".mine .myCard .card").eq(1).css("margin-right","6%");
            	else		*/
            $(".mine .myCard .card").eq(0).css("margin-right", "6%");
            console.log(num0)
        }
    }, /////自己的牌插入
    insertCard: function(num) {
        appData.player[0].card.splice(num, 0, { "card": appData.player[0].cardNew.card, "isSelect": false });
        for (var i = 0; i < appData.player[0].card.length; i++) {
            appData.player[0].card[i].num = i;
        }
        appData.player[0].cardNew.isShow = false;
        $(".mine .myCard .card").css("margin-right", "0")
    }, /////自己的牌插入替换
    dicard: function(player, card) {
        if (typeof(card) != "undefined") {
            appData.player[player].cardSet = card;
        }
        appData.player[player].discard.push({
            "card": appData.player[player].cardSet,
            "num": appData.player[player].discard.length,
            "show": false,
        });
        var length = appData.player[player].discard.length - 1;
        if (player == 0) {
            setTimeout(function() {
                $(".cardList .player1 .discard").show();
                if (length < 12) {
                    $(".cardList .player1 .discard").css("margin-left", length * 3.1 + "%");
                    $(".cardList .player1 .discard").css("margin-top", 0);
                    $(".cardList .player1 .discard").css("width", "3.1%");
                } else {
                    $(".cardList .player1 .discard").css("width", "3.1%");
                    $(".cardList .player1 .discard").css("margin-left", (length - 12) * 3.1 + "%");
                    $(".cardList .player1 .discard").css("margin-top", "3.8%");
                }
            }, 10)
        } else if (player == 1) {
            setTimeout(function() {
                $(".cardList .player2 .discard").show();
                if (length < 12) {
                    $(".cardList .player2 .discard").css("margin-left", 0);
                    $(".cardList .player2 .discard").css("margin-top", -length * 2.5 + "%");
                } else {
                    $(".cardList .player2 .discard").css("margin-left", "4%");
                    $(".cardList .player2 .discard").css("margin-top", -(length - 12) * 2.5 + "%");
                }
            }, 10)
        } else if (player == 2) {
            setTimeout(function() {
                $(".cardList .player3 .discard").show();
                if (length < 12) {
                    $(".cardList .player3 .discard").css("width", "3.1%");
                    $(".cardList .player3 .discard").css("margin-right", length * 3.1 + "%");
                    $(".cardList .player3 .discard").css("margin-bottom", "0");
                } else {
                    $(".cardList .player3 .discard").css("width", "3.1%");
                    $(".cardList .player3 .discard").css("margin-right", (length - 12) * 3.1 + "%");
                    $(".cardList .player3 .discard").css("margin-bottom", "3.8%");
                }
            }, 10)
        } else if (player == 3) {
            setTimeout(function() {
                $(".cardList .player4 .discard").show();
                if (length < 12) {
                    $(".cardList .player4 .discard").css("margin-left", "-4%");
                    $(".cardList .player4 .discard").css("margin-top", length * 2.5 + "%");
                } else {
                    $(".cardList .player4 .discard").css("margin-left", "-8%");
                    $(".cardList .player4 .discard").css("margin-top", (length - 12) * 2.5 + "%");
                }
            }, 10)
        }
        setTimeout(function() {
            appData.player[player].discard[appData.player[player].discard.length - 1].show = true;
            appData.player[player].cardSet = "";
        }, 600)
    }, /////出牌显示
    gang: function(num, type, card) {
        for (var i = 0; i < appData.player.length; i++) {
            if (i == num) {
                if (type == 1) {
                    appData.player[i].gangScore = appData.game.base_score * 6;
                    appData.player[i].account_score = appData.player[i].account_score + appData.game.base_score * 6;
                } else if (type == 2 && appData.game.qianggang == 0) {
                    appData.player[i].gangScore = appData.game.base_score * 3;
                    appData.player[i].account_score = appData.player[i].account_score + appData.game.base_score * 3;
                } else if (type == 3) {
                    appData.player[i].gangScore = appData.game.base_score * 3;
                    appData.player[i].account_score = appData.player[i].account_score + appData.game.base_score * 3;
                }
            } else {
                if (type == 1) {
                    appData.player[i].gangScore = -appData.game.base_score * 2;
                    appData.player[i].account_score = appData.player[i].account_score - appData.game.base_score * 2;
                } else if (type == 2 && appData.game.qianggang == 0) {
                    appData.player[i].gangScore = -appData.game.base_score;
                    appData.player[i].account_score = appData.player[i].account_score - appData.game.base_score;
                } else if (type == 3) {
                    if (appData.player[i].account_id == appData.game.last_user) {
                        appData.player[i].gangScore = -appData.game.base_score * 3;
                        appData.player[i].account_score = appData.player[i].account_score - appData.game.base_score * 3;
                    }
                }
            }
        }
        if (type == 3) {
            operationMethod.peng(num, 2);
        } else if (type == 2) {
            var n = 0;
            appData.player[num].cardNew.card = "";
            appData.player[num].cardNew.isShow = false;
            appData.player[num].cardSet = card;
            if (num == 0) {
                for (var i = 0; i < appData.player[num].card.length; i++) {
                    if (appData.player[num].card[i].card == card) {
                        appData.player[num].card.splice(i, 1);
                    }
                }
                for (var i = 0; i < appData.player[num].card.length; i++) {
                    appData.player[num].card[i].num = i;
                }
            } else {
                appData.player[num].card.splice(appData.player[num].card.length, 1);
            }
            setTimeout(function() {
                for (var i = 0; i < appData.player[num].pengGang.length; i++) {
                    if (appData.player[num].pengGang[i].card == card) {
                        appData.player[num].pengGang[i].type = 2;
                    }
                }
                appData.player[num].cardSet = "";
            }, 700)
            for (var i = 0; i < appData.player[num].pengGang.length; i++) {
                if (appData.player[num].pengGang[i].card == card) {
                    n = i;
                }
            }
            setTimeout(function() {
                $(".discard").show();
                if (num == 0) {
                    $(".cardList .player1 .discard").css("margin-left", -24 + (n * 18) + "%");
                    $(".cardList .player1 .discard").css("margin-top", "9.2%");
                    $(".cardList .player1 .discard").css("width", "5.5%");
                } else if (num == 1) {
                    $(".cardList .player2 .discard").css("z-index", 40);
                    $(".cardList .player2 .discard").css("margin-left", "9.7%");
                    $(".cardList .player2 .discard").css("margin-top", 1.9 - (n * 8.4) - (5 / 1.6267) + "%");
                } else if (num == 2) {
                    $(".cardList .player3 .discard").css("width", "3.3%");
                    $(".cardList .player3 .discard").css("margin-right", -0.6 + (n * 11) + "%");
                    $(".cardList .player3 .discard").css("margin-bottom", "11.5%");
                } else if (num == 3) {
                    $(".cardList .player4 .discard").css("z-index", 21);
                    $(".cardList .player4 .discard").css("margin-left", "-14.2%");
                    $(".cardList .player4 .discard").css("margin-top", 14.1 + (n * 8.4) - (33 / 1.6267) + "%");
                }
            }, 10)
        } else if (type == 1) {
            appData.player[num].pengGang.push({ "card": card, "num": appData.player[num].pengGang.length, "step": 1, "type": 3 });
            if (num == 0) {
                var cardnum = 0;
                appData.player[num].card.push({ "num": 0, "isSelect": false, "card": appData.player[num].cardNew.card });
                appData.player[num].cardNew.card = "";
                for (var j = appData.player[num].card.length - 1; j >= 0; j--) {
                    if (cardnum == 4) {
                        break;
                    } else {
                        if (appData.player[num].card[j].card == card) {
                            appData.player[num].card.splice(j, 1);
                            cardnum++;
                        }
                    }
                }
                appData.player[num].card.sort(by("card"));
                for (var j = 0; j < appData.player[num].card.length; j++) {
                    appData.player[num].card[j].num = j;
                }
            } else {
                appData.player[num].card.splice(appData.player[num].card.length - 1, 1);
                appData.player[num].card.splice(appData.player[num].card.length - 1, 1);
                appData.player[num].card.splice(appData.player[num].card.length - 1, 1);
            }
        }
        if (appData.game.qianggang == 0 || type != 2) {
            setTimeout(function() {
                appData.game.showGangScore = true;
                setTimeout(function() {
                    appData.game.showGangScore = false;
                    for (var i = 0; i < appData.player.length; i++) {
                        appData.player[i].gangScore = 0;
                    }
                }, 3000)
            }, 700)
        }

    },
    qianggang: function(num) {
        for (var i = 0; i < appData.player.length; i++) {
            if (i == num) {
                appData.player[i].gangScore = appData.game.base_score * 3;
                appData.player[i].account_score = appData.player[i].account_score + appData.game.base_score * 3;
            } else {
                appData.player[i].gangScore = -appData.game.base_score;
                appData.player[i].account_score = appData.player[i].account_score - appData.game.base_score;
            }
        }
        setTimeout(function() {
            appData.game.showGangScore = true;
            setTimeout(function() {
                appData.game.showGangScore = false;
                for (var i = 0; i < appData.player.length; i++) {
                    appData.player[i].gangScore = 0;
                }
            }, 2400)
        }, 100)
    },
    peng: function(num, type) {
        var cardnum = 0;
        for (var i = 0; i < appData.player.length; i++) {
            if (appData.player[i].account_id == appData.game.last_user) {
                var card = appData.player[i].discard[appData.player[i].discard.length - 1].card;
                appData.player[num].pengGang.push({ "card": card, "num": appData.player[num].pengGang.length, "step": 0, "type": type });
                operationMethod.pengMove(num, appData.player[num].pengGang.length - 1, i, appData.player[i].discard.length - 1, card);
                if (num == 0) {
                    for (var j = appData.player[num].card.length - 1; j >= 0; j--) {
                        if (cardnum == 1 + type) {
                            break;
                        } else {
                            if (appData.player[num].card[j].card == card) {
                                appData.player[num].card.splice(j, 1);
                                cardnum++;
                            }
                        }
                    }
                    for (var j = 0; j < appData.player[num].card.length; j++) {
                        appData.player[num].card[j].num = j;
                    }
                } else {
                    appData.player[num].card.splice(appData.player[num].card.length - 1, 1);
                    appData.player[num].card.splice(appData.player[num].card.length - 1, 1);
                    if (type == 2)
                        appData.player[num].card.splice(appData.player[num].card.length - 1, 1);
                }
                break;
            }
        }
        appData.game.last_user = -1;
    },
    pengMove: function(to, num1, from, num2, card) {
        if (from == 0) {
            appData.fromX = 3.1 * num2 + 31.5;
            if (num2 < 12)
                appData.fromY = 64.3;
            else
                appData.fromY = 64.3 + (3.8 * 1.6267);
        } else if (from == 1) {
            if (num2 < 12)
                appData.fromX = 73.3;
            else
                appData.fromX = 77.3;
            appData.fromY = -2.5 * num2 * 1.6267 + 69;
        } else if (from == 2) {
            appData.fromX = -3.1 * num2 + 65.6;
            if (num2 < 12)
                appData.fromY = 28.2;
            else
                appData.fromY = 20.7;
        } else if (from == 3) {
            if (num2 < 12)
                appData.fromX = 22.7;
            else
                appData.fromX = 18.7;
            appData.fromY = 2.5 * num2 * 1.6267 + 23;
        }

        setTimeout(function() {
            appData.player[to].pengGang[num1].step = 1;
            appData.player[from].discard.splice(num2, 1);
            if (to == 0) {
                $(".mine .cardReturn").eq(num1).css("top", appData.fromY + "%");
                $(".mine .cardReturn").eq(num1).css("left", appData.fromX - 11 - (num1 * 18) + "%");
                $(".mine .cardReturn").eq(num1).css("width", "3.1%");
                $(".mine .cardReturn").eq(num1).animate({ "width": "5.5%", "margin-top": (82 - appData.fromY) / 1.6267 + "%", "margin-left": 24 - appData.fromX + (num1 * 36) + "%" }, 300)
            } else if (to == 1) {
                $(".others .player2 .cardReturn").eq(num1).css("top", appData.fromY + (num1 * 8.4 * 1.6267) + "%");
                $(".others .player2 .cardReturn").eq(num1).css("left", appData.fromX + "%");
                $(".others .player2 .cardReturn").eq(num1).css("width", "3.9%");
                $(".others .player2 .cardReturn").eq(num1).animate({ "margin-top": (64 - appData.fromY) / 1.6267 - (num1 * 16.8) + "%", "margin-left": 83 - appData.fromX + "%" }, 300)
            } else if (to == 2) {
                $(".others .player3 .cardReturn").eq(num1).css("top", appData.fromY + "%");
                $(".others .player3 .cardReturn").eq(num1).css("left", appData.fromX - 6.6 + (num1 * 11) + "%");
                $(".others .player3 .cardReturn").eq(num1).css("width", "3.1%");
                $(".others .player3 .cardReturn").eq(num1).animate({ "width": "3.3%", "margin-top": (10.6 - appData.fromY) / 1.6267 + "%", "margin-left": 75.7 - appData.fromX - (num1 * 22) + "%" }, 300)
            } else if (to == 3) {
                $(".others .player4 .cardReturn").eq(num1).css("top", -(num1 * 8.4 + 5) * 1.6267 + appData.fromY + "%");
                $(".others .player4 .cardReturn").eq(num1).css("left", appData.fromX + "%");
                $(".others .player4 .cardReturn").eq(num1).css("width", "3.9%");
                $(".others .player4 .cardReturn").eq(num1).animate({ "margin-top": (10 - appData.fromY) / 1.6267 + 10 + (num1 * 16.8) + "10%", "margin-left": 12.5 - appData.fromX + "%" }, 300)
            }
        }, 400)
    },
    ///其他效果
    createForm: function() {
        appData.game.maxWin = Math.max(appData.player[0].account_score, appData.player[1].account_score, appData.player[2].account_score, appData.player[3].account_score);
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
        appData.playerBoard.record = str + "   前" + appData.game.round + "局";
        $(".roundPause").show();
        var target = document.getElementById("roundPause");
        html2canvas(target, {
            allowTaint: true,
            taintTest: false,
            onrendered: function(canvas) {
                canvas.id = "mycanvas";
                var dataUrl = canvas.toDataURL('image/png', 0.3);
                $(".roundPause").hide();
                $("#roundPause2").attr("src", dataUrl);
                $(".roundPause1").show();
                appData.game.last_user = -1;
                if (appData.game.countdown > 0) {
                    operationMethod.countdown();
                }
            }
        });
    },
    countdown: function() {
        if (appData.game.countdown <= 0) {
            return 0;
        }
        setTimeout(function() {
            appData.game.countdown--;
            operationMethod.countdown()
        }, 1000)
    },
    lightRun: function(num, time, finalNum) {
        setTimeout(function() {
            if (appData.game.light < 4)
                appData.game.light++;
            else
                appData.game.light = 1;
            num++;
            if (num > 20) {
                time = time + 50;
                if (num > 25 && num % 4 == finalNum) {
                    return 0;
                }
            } else {
                if (time > 200)
                    time = time - 50;
            }
            operationMethod.lightRun(num, time, finalNum);
        }, time)
    },
    timeLimit: function(time) {
        appData.game.time.time = time;
        appData.game.isPlaying = true;
        appData.game.time.firstNum = Math.floor(time / 10);
        appData.game.time.lastNum = time % 10;
        if (appData.game.time.time > 0) {
            setTimeout(function() {
                operationMethod.timeLimit(appData.game.time.time - 1)
            }, 1000)
        } else {
            appData.game.isPlaying = false;
        }
    },
    myCardAnimate1: function(type) {
        if (type == 1) {
            appData.animate.animate1 = 6;
            setTimeout(function() {
                appData.animate.animate1 = 5;
                setTimeout(function() {
                    appData.animate.animate1 = 4;
                }, 150)
            }, 150)
        } else if (type == 2) {
            setTimeout(function() {
                appData.animate.animate1 = 2;
                setTimeout(function() {
                    appData.animate.animate1 = 3;
                    setTimeout(function() {
                        appData.animate.animate1 = 2;
                        setTimeout(function() {
                            appData.player[0].card.sort(by("card"));
                            for (var i = 0; i < appData.player[0].card.length; i++) {
                                appData.player[0].card[i].num = i;
                            }
                            appData.animate.animate1 = 1;
                            setTimeout(function() {
                                operationMethod.myCardAnimate3();
                            }, 400)
                        }, 100)
                    }, 100)
                }, 100)
            }, 500)
        }
    },
    myCardAnimate2: function() {
        appData.animate.animate2 = 8;
        operationMethod.myCardAnimate1(1);
        setTimeout(function() {
            appData.animate.animate2 = 4;
            operationMethod.myCardAnimate1(1);
            setTimeout(function() {
                appData.animate.animate2 = 0;
                operationMethod.myCardAnimate1(1);
                setTimeout(function() {
                    operationMethod.myCardAnimate1(2);
                }, 320)
            }, 320)
        }, 320)
    },
    myCardAnimate3: function() {
        if (appData.game.joker != 1) {
            $(".startBack").hide();
            appData.game.time.time = appData.game.limit_time;
            operationMethod.timeLimit(appData.game.time.time);
            return 0;
        }
        appData.animate.animate3 = 1;
        setTimeout(function() {
            appData.animate.animate3 = 2;
            setTimeout(function() {
                appData.animate.animate3 = 3;
                setTimeout(function() {
                    appData.animate.animate3 = 4;
                    setTimeout(function() {
                        appData.animate.animate3 = 5;
                        setTimeout(function() {
                            appData.animate.animate3 = 6;
                            for (var i = 0; i < appData.player.length; i++) {
                                if (appData.player[i].account_id == appData.game.banker_id) {
                                    appData.player[(i + 3) % 4].discard.push({
                                        card: appData.game.flip_card,
                                        num: 0,
                                    });
                                }
                            }
                            setTimeout(function() {
                                appData.animate.animate3 = 7;
                                setTimeout(function() {
                                    appData.animate.animate3 = 8;
                                    for (var i = 0; i < appData.player[0].card.length; i++) {
                                        if (appData.player[0].card[i].card == appData.game.joker_card)
                                            appData.player[0].card[i].card = appData.player[0].card[i].card - 100;
                                    }
                                    if (appData.player[0].cardNew.card == appData.game.joker_card)
                                        appData.player[0].cardNew.card = appData.player[0].cardNew.card - 100;
                                    appData.player[0].card.sort(by("card"));
                                    for (var i = 0; i < appData.player[0].card.length; i++) {
                                        appData.player[0].card[i].num = i;
                                    }
                                    $(".startBack").hide();
                                    appData.game.time.time = appData.game.limit_time;
                                    operationMethod.timeLimit(appData.game.time.time);
                                }, 1000)
                            }, 2000)
                        }, 1000)
                    }, 100)
                }, 100)
            }, 100)
        }, 800)
    },
    endAnimate: function() {
        appData.game.isShowEnd = true;
        if (appData.game.endStep == 0) {
            setTimeout(function() {
                appData.game.endStep = 1;
                setTimeout(function() {
                    appData.game.endStep = 2;
                    setTimeout(function() {
                        appData.game.endStep = 3;
                        setTimeout(function() {
                            appData.game.endStep = 4;
                            setTimeout(function() {
                                appData.game.endStep = 5;
                                setTimeout(function() {
                                    appData.game.endStep = 6;
                                    for (var i = 0; i < appData.player.length; i++) {
                                        appData.player[i].account_score = appData.playerBoard.score[i].account_score;
                                    }
                                    if (appData.game.round == appData.game.total_num) {
                                        setTimeout(function() {
                                            operationMethod.endRound();
                                        }, 1500)
                                    } else {
                                        operationMethod.createForm();
                                    }
                                }, 3000)
                            }, 1000)
                        }, 80)
                    }, 80)
                }, 80)
            }, 2000)
        } else {
            for (var i = 0; i < appData.player.length; i++) {
                appData.player[i].account_score = appData.playerBoard.score[i].account_score;
            }
            if (appData.game.endStep == 6 && appData.game.round == appData.game.total_num) {
                setTimeout(function() {
                    operationMethod.endRound();
                }, 1500)
            } else {
                operationMethod.createForm();
            }
        }
    },
    showZi: function(player, type) {
        appData.player[player].zi = type;
        setTimeout(function() {
            appData.player[player].zi = 0;
        }, 2500)
    },
    nextRound: function() {
        appData.animate.animate3 = 0;
        appData.game.isShowEnd = false;
        appData.game.endStep = 0;
        appData.game.light = 0;
        appData.game.status = 1;
        appData.game.last_user = -1;
        appData.game.banker_id = -1;
        appData.game.joker_card = 0;
        for (var i = 0; i < appData.player.length; i++) {
            appData.player[i].playing_status = 0;
            appData.player[i].is_operation = false;
            appData.player[i].win_type = 0;
            appData.player[i].gang_flag = 0;
            appData.player[i].hu_flag = 0;
            appData.player[i].zi = 0;
            appData.player[i].tempScore = 0;
            appData.player[i].cardSet = "";
            appData.player[i].end_show = false;
            appData.player[i].card = [];
            appData.player[i].discard = [];
            appData.player[i].pengGang = [];
            appData.player[i].cardNew = { "card": "", "isSelect": false, "isShow": false };
        }
        operationMethod.click(0);
    },
    nextRoundSet: function() {
        appData.animate.animate3 = 0;
        appData.game.isShowEnd = false;
        appData.game.endStep = 0;
        appData.game.light = 0;
        appData.game.status = 1;
        appData.game.last_user = -1;
        appData.game.banker_id = -1;
        appData.game.joker_card = 0;
        for (var i = 0; i < appData.player.length; i++) {
            appData.player[i].is_operation = false;
            appData.player[i].win_type = 0;
            appData.player[i].gang_flag = 0;
            appData.player[i].hu_flag = 0;
            appData.player[i].zi = 0;
            appData.player[i].tempScore = 0;
            appData.player[i].cardSet = "";
            appData.player[i].end_show = false;
            appData.player[i].card = [];
            appData.player[i].discard = [];
            appData.player[i].pengGang = [];
            appData.player[i].cardNew = { "card": "", "isSelect": false, "isShow": false };
        }
    },
    endRound: function() {
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
        appData.playerBoard.record = str + "   前" + appData.game.round + "局";
        $(".ranking").show();
        setTimeout(function() {
            operationMethod.canvas();
        }, 100)

    },
    canvas: function() {
        var target = document.getElementById("ranking");
        html2canvas(target, {
            allowTaint: true,
            taintTest: false,
            onrendered: function(canvas) {
                canvas.id = "mycanvas";
                var dataUrl = canvas.toDataURL('image/jpeg', 0.5);
                $("#end").attr("src", dataUrl);
                $(".ranking").hide();
                $(".end").show();
            }
        });
    },
    boardSet: function(board, score_summary) {
        for (var i = 0; i < appData.player.length; i++) {
            for (s in board) {
                if (appData.player[i].account_id == s) {
                    appData.playerBoard.score[i].num = appData.player[i].num;
                    appData.playerBoard.score[i].account_id = appData.player[i].account_id;
                    appData.playerBoard.score[i].nickname = appData.player[i].nickname;
                    appData.playerBoard.score[i].account_score = Math.ceil(board[s]);
                }
            }
            for (s in score_summary) {
                if (appData.player[i].account_id == s) {
                    appData.player[i].tempScore = Math.ceil(score_summary[s]);
                }
            }
        }
        for (var i = 0; i < appData.playerBoard.score.length; i++) {
            appData.playerBoard.score[i].score_summary = 0;
            for (s in score_summary) {
                if (appData.playerBoard.score[i].account_id == s) {
                    appData.playerBoard.score[i].score_summary = Math.ceil(score_summary[s]);
                }
            }
        }
        appData.game.maxWin = Math.max(appData.playerBoard.score[0].account_score, appData.playerBoard.score[1].account_score, appData.playerBoard.score[2].account_score, appData.playerBoard.score[3].account_score);
    },
    positionReset: function() {
        var j = 0;
        var idReg = /^(-)?\d+(\.\d+)?$/;
        appData.game.positionList1 = new Array();
        appData.game.positionList2 = new Array();
        for (var i = 0; i < appData.player.length; i++) {
            if (i != 0 && appData.player[i].account_id > 0) {
                if (idReg.test(appData.player[i].longitude)) {
                    appData.game.positionList2.push({
                        "nickname": appData.player[i].nickname,
                        "headimgurl": appData.player[i].headimgurl,
                        "position": 1,
                    });
                    j++;
                } else {
                    appData.game.positionList2.push({
                        "nickname": appData.player[i].nickname,
                        "headimgurl": appData.player[i].headimgurl,
                        "position": 0,
                    });
                }
            }
        }
        if (j == 3) {
            appData.game.positionList1.push({
                "nickname1": appData.player[1].nickname,
                "headimgurl1": appData.player[1].headimgurl,
                "nickname2": appData.player[2].nickname,
                "headimgurl2": appData.player[2].headimgurl,
                "position": LantitudeLongitudeDist(appData.player[1].longitude, appData.player[1].latitude, appData.player[2].longitude, appData.player[2].latitude),
            });
            appData.game.positionList1.push({
                "nickname1": appData.player[1].nickname,
                "headimgurl1": appData.player[1].headimgurl,
                "nickname2": appData.player[3].nickname,
                "headimgurl2": appData.player[3].headimgurl,
                "position": LantitudeLongitudeDist(appData.player[1].longitude, appData.player[1].latitude, appData.player[3].longitude, appData.player[3].latitude),
            });
            appData.game.positionList1.push({
                "nickname1": appData.player[2].nickname,
                "headimgurl1": appData.player[2].headimgurl,
                "nickname2": appData.player[3].nickname,
                "headimgurl2": appData.player[3].headimgurl,
                "position": LantitudeLongitudeDist(appData.player[2].longitude, appData.player[2].latitude, appData.player[3].longitude, appData.player[3].latitude),
            });
        } else if (j == 2) {
            if (!idReg.test(appData.player[1].longitude)) {
                appData.game.positionList1.push({
                    "nickname1": appData.player[2].nickname,
                    "headimgurl1": appData.player[2].headimgurl,
                    "nickname2": appData.player[3].nickname,
                    "headimgurl2": appData.player[3].headimgurl,
                    "position": LantitudeLongitudeDist(appData.player[2].longitude, appData.player[2].latitude, appData.player[3].longitude, appData.player[3].latitude),
                });
            } else if (!idReg.test(appData.player[2].longitude)) {
                appData.game.positionList1.push({
                    "nickname1": appData.player[1].nickname,
                    "headimgurl1": appData.player[1].headimgurl,
                    "nickname2": appData.player[3].nickname,
                    "headimgurl2": appData.player[3].headimgurl,
                    "position": LantitudeLongitudeDist(appData.player[1].longitude, appData.player[1].latitude, appData.player[3].longitude, appData.player[3].latitude),
                });
            } else if (!idReg.test(appData.player[3].longitude)) {
                appData.game.positionList1.push({
                    "nickname1": appData.player[1].nickname,
                    "headimgurl1": appData.player[1].headimgurl,
                    "nickname2": appData.player[2].nickname,
                    "headimgurl2": appData.player[2].headimgurl,
                    "position": LantitudeLongitudeDist(appData.player[1].longitude, appData.player[1].latitude, appData.player[2].longitude, appData.player[2].latitude),
                });
            }
        } else if (j == 1) {
            for (var i = 0; i < appData.game.positionList2.length; i++) {
                if (appData.game.positionList2[i].position == 1) {
                    appData.game.positionList2[i].position = 2;
                    break;
                }
            }
        }
    },
}
var sendMethod = {
    closeSocket: function() {
        if (ws) {
            try {
                ws.close();
            } catch (error) {
                console.log(error);
            }
        }
    },
    sendPrepareJoinRoom: function() {
        ws.send(JSON.stringify({
            operation: "PrepareJoinRoom",
            account_id: userData.accountId,
            session: globalData.session,
            data: {
                room_number: globalData.roomNumber
            }
        }));
    },
    sendJoinRoom: function() {
        ws.send(JSON.stringify({
            operation: "JoinRoom",
            account_id: userData.accountId,
            session: globalData.session,
            data: {
                room_number: globalData.roomNumber
            }
        }));
    },
    sendReadyStart: function() {
        ws.send(JSON.stringify({
            operation: "ReadyStart",
            account_id: userData.accountId,
            session: globalData.session,
            data: {
                room_id: appData.game.room
            }
        }));
    },
    sendBroadcastVoice: function(num) {
        ws.send(JSON.stringify({
            operation: "BroadcastVoice",
            account_id: userData.accountId,
            session: globalData.session,
            data: {
                room_id: appData.game.room,
                voice_num: num
            }
        }));
    },
    sendChooseCard: function(num) {
        ws.send(JSON.stringify({
            operation: "ChooseCard",
            account_id: userData.accountId,
            session: globalData.session,
            data: {
                room_id: appData.game.room,
                card: num
            }
        }));
    },
    sendQiangGangHu: function(type, num) {
        ws.send(JSON.stringify({
            operation: "QiangGangHu",
            account_id: userData.accountId,
            session: globalData.session,
            data: {
                room_id: appData.game.room,
                qiang: type,
                card: num,
            }
        }));
    },
    sendPassCard: function() {
        ws.send(JSON.stringify({
            operation: "PassCard",
            account_id: userData.accountId,
            session: globalData.session,
            data: {
                room_id: appData.game.room,
            }
        }));
    },
    sendPengCard: function() {
        ws.send(JSON.stringify({
            operation: "PengCard",
            account_id: userData.accountId,
            session: globalData.session,
            data: {
                room_id: appData.game.room,
            }
        }));
    },
    sendAnGang: function() {
        ws.send(JSON.stringify({
            operation: "AnGang",
            session: globalData.session,
            account_id: userData.accountId,
            data: {
                room_id: appData.game.room,
                card: "",
            }
        }));
    },
    sendJiaGang: function() {
        ws.send(JSON.stringify({
            operation: "JiaGang",
            account_id: userData.accountId,
            session: globalData.session,
            data: {
                room_id: appData.game.room,
            }
        }));
    },
    sendBaoGang: function() {
        ws.send(JSON.stringify({
            operation: "BaoGang",
            account_id: userData.accountId,
            session: globalData.session,
            data: {
                room_id: appData.game.room,
            }
        }));
    },
    sendHuCard: function() {
        ws.send(JSON.stringify({
            operation: "HuCard",
            account_id: userData.accountId,
            session: globalData.session,
            data: {
                room_id: appData.game.room,
            }
        }));
    },
    sendPullRoomInfo: function() {
        ws.send(JSON.stringify({
            operation: "PullRoomInfo",
            account_id: userData.accountId,
            session: globalData.session,
            data: {
                room_id: appData.game.room,
            }
        }));
    },
    sendUploadGeo: function(longitude, latitude) {
        ws.send(JSON.stringify({
            operation: "UploadGeo",
            account_id: userData.accountId,
            session: globalData.session,
            data: {
                room_id: appData.game.room,
                longitude: longitude,
                latitude: latitude,
            }
        }));
    },

}
var receiveMethod = {
    receiveLastScoreboard: function(obj) {
        console.log(obj);

        if (typeof(obj) != "undefined" && obj != "") {

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
            appData.playerBoard.record = str + " 前" + appData.playerBoard.round + "局";
            //		appData.playerBoard.record = str;
            appData.playerBoard.score = [];
            appData.game.maxWin = 0;
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


                if (parseInt(scores[s].score) > appData.game.maxWin) {
                    appData.game.maxWin = scores[s].score;
                }
            }
        }
        if (globalData.room_status == 4) {
            $(".main").show();
            $(".outPart").show();
        }
        $(".ranking").show();
        operationMethod.canvas();
    },
    receivePrepareJoinRoom: function(obj) {
        appData.rullInfo.horse_count = Math.ceil(obj.data.horse_count);
        appData.rullInfo.ticket_count = Math.ceil(obj.data.ticket_count);
        appData.rullInfo.qianggang = Math.ceil(obj.data.qianggang);
        appData.rullInfo.chengbao = Math.ceil(obj.data.chengbao);
        appData.rullInfo.joker = Math.ceil(obj.data.joker);
        appData.game.horse_count = Math.ceil(obj.data.horse_count);
        appData.game.ticket_count = Math.ceil(obj.data.ticket_count);
        appData.game.qianggang = Math.ceil(obj.data.qianggang);
        appData.game.chengbao = Math.ceil(obj.data.chengbao);
        appData.game.joker = Math.ceil(obj.data.joker);
        appData.game.status = obj.data.room_status;
        wxModule.config();
        if (obj.data.room_status == 3) {
            if (appData.isAutoActive == true) {
                sendMethod.sendActiveRoom();
            } else {
                appData.createInfo.isShow = true;
                appData.createInfo.newRoom = false;
            }
        } else if (obj.data.room_status == 4) {
            controlMethod.showAlert(2, obj.result_message);
        } else {
            if (obj.data.user_count == 0) {
                sendMethod.sendJoinRoom();
            } else {
                if (obj.data.alert_text != "" && obj.data.alert_text.replace(/\ +/g, "") != "") {
                    controlMethod.showAlert(4, obj.data.alert_text)
                } else {
                    sendMethod.sendJoinRoom();
                }
            }
        }
    },
    receiveJoinRoom: function(obj) {
        appData.playerBoard = {
            "score": [],
            "record": "",
        }
        appData.player = [];
        for (var i = 0; i < 4; i++) {
            appData.player.push({
                "num": i + 1,
                "serial_num": i + 1,
                "account_id": 0,
                "nickname": "",
                "headimgurl": "",
                "account_status": 0,
                "playing_status": 0,
                "online_status": 0,
                "account_score": 0,
                "ticket_checked": 0,
                "is_win": false,
                "win_type": 0,
                "is_operation": false,
                "end_show": false,
                "cardNew": { "card": "", "isSelect": false, "isShow": false },
                "cardSet": "",
                "card": [],
                "pengGang": [],
                "gang_flag": 0,
                "hu_flag": 0,
                "discard": [],
                "card_type": 0,
                "messageOn": false,
                "messageText": "",
                "messageTime": 0,
                "zi": 0,
                "tempScore": 0,
                "gangScore": 0,
                "longitude": "",
                "latitude": "",
            })
            appData.playerBoard.score.push({
                "account_id": 0,
                "nickname": "",
                "account_score": 0,
            })
        }
        appData.game.room = obj.data.room_id;
        appData.game.room_status = obj.data.room_status;
        appData.game.joker_card = Math.ceil(obj.data.joker_card);
        if (appData.game.joker_card > 0) {
            appData.animate.animate3 = 8;
        }
        appData.game.horse_count = Math.ceil(obj.data.horse_count);
        appData.game.joker = Math.ceil(obj.data.joker);
        appData.game.qianggang = obj.data.qianggang;
        appData.game.ticket_count = Math.ceil(obj.data.ticket_count);
        appData.game.round = Math.ceil(obj.data.game_num);
        appData.game.total_num = Math.ceil(obj.data.total_num);
        appData.game.banker_id = Math.ceil(obj.data.banker_id);
        appData.game.countdown = Math.ceil(obj.data.countdown);
        wxModule.config();
        appData.player[0].serial_num = Math.ceil(obj.data.serial_num);
        for (var i = 0; i < appData.player.length; i++) {
            if (i <= appData.player.length - Math.ceil(obj.data.serial_num)) {
                appData.player[i].serial_num = i + Math.ceil(obj.data.serial_num);
            } else {
                appData.player[i].serial_num = i + Math.ceil(obj.data.serial_num) - appData.player.length;
            }
        }
        appData.player[0].account_status = Math.ceil(obj.data.account_status);
        appData.player[0].account_score = Math.ceil(obj.data.account_score);
        appData.player[0].nickname = userData.nickname;
        appData.player[0].headimgurl = userData.avatar;
        appData.player[0].account_id = userData.accountId;
        for (var i = 0; i < obj.data.my_card.length; i++) {
            appData.player[0].card.unshift({
                "num": obj.data.my_card.length - 1 - i,
                "isSelect": false,
                "card": Math.ceil(obj.data.my_card[i]),
            })
        }
        appData.player[0].ticket_checked = obj.data.ticket_checked;
        appData.game.status = Math.ceil(obj.data.room_status);
        appData.game.last_user = obj.data.last_user;
        appData.game.last_discard = Math.ceil(obj.data.last_discard);
        appData.game.scoreboard = obj.data.scoreboard;
        appData.game.score_summary = obj.data.score_summary;
        if (appData.position.positionReady) {
            sendMethod.sendUploadGeo(appData.position.longitude, appData.position.latitude);
            appData.position.positionReady = false;
        }
    },
    receiveUpdateAccountStatus: function(obj) {
        for (var i = 0; i < appData.player.length; i++) {
            if (appData.player[i].account_id == obj.data.account_id) {
                if (obj.data.online_status == 1) {
                    appData.player[i].account_status = Math.ceil(obj.data.account_status);
                } else if (obj.data.online_status == 0 && appData.player[i].account_status == 0) {
                    appData.player[i].account_id = 0;
                    appData.player[i].account_status = 0;
                    appData.player[i].playing_status = 0;
                    appData.player[i].online_status = 0;
                    appData.player[i].nickname = "";
                    appData.player[i].headimgurl = "";
                    appData.player[i].account_score = 0;
                    appData.player[i].longitude = "";
                    appData.player[i].latitude = "";
                    operationMethod.positionReset();
                } else if (obj.data.online_status == 0 && appData.player[i].account_status == 1) {
                    appData.player[i].account_status = Math.ceil(obj.data.account_status);
                    appData.player[i].online_status = 0;
                }
            }
        }
    },
    receiveAllGamerInfo: function(obj) {
        for (var i = 0; i < appData.player.length; i++) {
            for (var j = 0; j < obj.data.length; j++) {
                if (appData.player[i].serial_num == obj.data[j].serial_num) {
                    appData.player[i].nickname = obj.data[j].nickname;
                    appData.player[i].headimgurl = obj.data[j].headimgurl;
                    appData.player[i].account_id = obj.data[j].account_id;
                    appData.player[i].longitude = obj.data[j].longitude;
                    appData.player[i].latitude = obj.data[j].latitude;
                    appData.player[i].account_score = Math.ceil(obj.data[j].account_score);
                    appData.player[i].account_status = Math.ceil(obj.data[j].account_status);
                    appData.player[i].online_status = Math.ceil(obj.data[j].online_status);
                    appData.player[i].ticket_checked = Math.ceil(obj.data[j].ticket_checked);
                    if (i != 0) {
                        appData.player[i].card = new Array();
                        for (var k = 0; k < obj.data[j].card_count; k++) {
                            appData.player[i].card.push({
                                "num": k,
                                "card": "",
                            })
                        }
                    }
                    appData.player[i].discard = new Array();
                    for (var k = 0; k < obj.data[j].discard.length; k++) {
                        appData.player[i].discard.push({
                            "num": k,
                            "card": Math.ceil(obj.data[j].discard[k]),
                            "show": true,
                        })
                    }
                    appData.player[i].pengGang = new Array();
                    for (var k = 0; k < obj.data[j].peng_card.length; k++) {
                        appData.player[i].pengGang.push({
                            "num": appData.player[i].pengGang.length,
                            "card": Math.ceil(obj.data[j].peng_card[k]),
                            "step": 1,
                            "type": 1
                        })
                    }
                    for (var k = 0; k < obj.data[j].ming_gang.length; k++) {
                        appData.player[i].pengGang.push({
                            "num": appData.player[i].pengGang.length,
                            "card": Math.ceil(obj.data[j].ming_gang[k]),
                            "step": 1,
                            "type": 2
                        })
                    }
                    for (var k = 0; k < obj.data[j].an_gang.length; k++) {
                        appData.player[i].pengGang.push({
                            "num": appData.player[i].pengGang.length,
                            "card": Math.ceil(obj.data[j].an_gang[k]),
                            "step": 1,
                            "type": 3
                        })
                    }
                }
            }
        }
        if (appData.game.scoreboard != "") {
            for (var i = 0; i < appData.player.length; i++) {
                for (s in appData.game.scoreboard) {
                    if (appData.player[i].account_id == s) {
                        appData.playerBoard.score[i].num = appData.player[i].num;
                        appData.playerBoard.score[i].account_id = appData.player[i].account_id;
                        appData.playerBoard.score[i].nickname = appData.player[i].nickname;
                        appData.playerBoard.score[i].headimgurl = appData.player[i].headimgurl;
                        appData.playerBoard.score[i].account_score = Math.ceil(appData.game.scoreboard[s]);
                    }
                }
            }
        }
        if (appData.game.score_summary != "") {
            for (var i = 0; i < appData.playerBoard.score.length; i++) {
                for (s in appData.game.score_summary) {
                    if (appData.playerBoard.score[i].account_id == s) {
                        appData.playerBoard.score[i].score_summary = Math.ceil(appData.game.score_summary[s]);
                    }
                }
            }
        }
        if (appData.game.round > 0 && appData.game.room_status == 1) {
            operationMethod.createForm()
        }
        if (appData.game.round == 0) {
            operationMethod.positionReset();
        }
    },
    receiveUpdateGamerInfo: function(obj) {
        for (var i = 0; i < appData.player.length; i++) {
            if (appData.player[i].serial_num == obj.data.serial_num) {
                appData.player[i].nickname = obj.data.nickname;
                appData.player[i].headimgurl = obj.data.headimgurl;
                appData.player[i].account_id = obj.data.account_id;
                appData.player[i].longitude = obj.data.longitude;
                appData.player[i].latitude = obj.data.latitude;

                appData.player[i].account_score = Math.ceil(obj.data.account_score);
                appData.player[i].account_status = Math.ceil(obj.data.account_status);
                appData.player[i].online_status = Math.ceil(obj.data.online_status);
                appData.player[i].ticket_checked = Math.ceil(obj.data.ticket_checked);
                if (i != 0) {
                    appData.player[i].card = new Array();
                    for (var k = 0; k < obj.data.card_count; k++) {
                        appData.player[i].card.push({
                            "num": k,
                            "card": "",
                        })
                    }
                }

            } else if (appData.player[i].serial_num != obj.data.serial_num) {
                if (appData.player[i].account_id == obj.data.account_id) {
                    sendMethod.sendPullRoomInfo();
                    return 0;
                }
            }
        }
        if (appData.game.round == 0)
            operationMethod.positionReset();
    },
    receiveAccountGeo: function(obj) {
        for (var i = 0; i < appData.player.length; i++) {
            if (appData.player[i].account_id == obj.data.account_id) {
                appData.player[i].longitude = obj.data.longitude;
                appData.player[i].latitude = obj.data.latitude;
            }
        }
        operationMethod.positionReset();
    },
    receivePullRoomInfo: function(obj) {
        appData.player = [];
        for (var i = 0; i < 4; i++) {
            appData.player.push({
                "num": i + 1,
                "serial_num": i + 1,
                "account_id": 0,
                "nickname": "",
                "headimgurl": "",
                "account_status": 0,
                "playing_status": 0,
                "online_status": 0,
                "account_score": 0,
                "ticket_checked": 0,
                "is_win": false,
                "win_type": 0,
                "is_operation": false,
                "end_show": false,
                "cardNew": { "card": "", "isSelect": false, "isShow": false },
                "cardSet": "",
                "card": [],
                "pengGang": [],
                "gang_flag": 0,
                "hu_flag": 0,
                "discard": [],
                "card_type": 0,
                "messageOn": false,
                "messageText": "我们来血拼吧",
                "messageTime": 0,
                "zi": 0,
                "tempScore": 0,
                "gangScore": 0,
                "longitude": "",
                "latitude": "",
            })
        }
        for (var i = 0; i < obj.data.my_card.length; i++) {
            appData.player[0].card.unshift({
                "num": obj.data.my_card.length - 1 - i,
                "isSelect": false,
                "card": Math.ceil(obj.data.my_card[i]),
            })
        }
        appData.game.remain_count = Math.ceil(obj.data.remain_count);
        appData.game.last_user = Math.ceil(obj.data.last_user);
        appData.game.last_discard = Math.ceil(obj.data.last_discard);
        appData.game.banker_id = Math.ceil(obj.data.banker_id);
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
                    appData.player[i].longitude = obj.all_gamer_info[j].longitude;
                    appData.player[i].latitude = obj.all_gamer_info[j].latitude;
                    appData.player[i].account_score = Math.ceil(obj.all_gamer_info[j].account_score);
                    appData.player[i].account_status = Math.ceil(obj.all_gamer_info[j].account_status);
                    appData.player[i].online_status = Math.ceil(obj.all_gamer_info[j].online_status);
                    appData.player[i].ticket_checked = Math.ceil(obj.all_gamer_info[j].ticket_checked);
                    if (i != 0) {
                        appData.player[i].card = [];
                        for (var k = 0; k < obj.all_gamer_info[j].card_count; k++) {
                            appData.player[i].card.push({
                                "num": k,
                                "card": "",
                            })
                        }
                    }
                    appData.player[i].discard = [];
                    for (var k = 0; k < obj.all_gamer_info[j].discard.length; k++) {
                        appData.player[i].discard.push({
                            "num": k,
                            "card": Math.ceil(obj.all_gamer_info[j].discard[k]),
                            "show": true,
                        })
                    }
                    appData.player[i].pengGang = [];
                    for (var k = 0; k < obj.all_gamer_info[j].peng_card.length; k++) {
                        appData.player[i].pengGang.push({
                            "num": appData.player[i].pengGang.length,
                            "card": Math.ceil(obj.all_gamer_info[j].peng_card[k]),
                            "step": 1,
                            "type": 1
                        })
                    }
                    for (var k = 0; k < obj.all_gamer_info[j].ming_gang.length; k++) {
                        appData.player[i].pengGang.push({
                            "num": appData.player[i].pengGang.length,
                            "card": Math.ceil(obj.all_gamer_info[j].ming_gang[k]),
                            "step": 1,
                            "type": 2
                        })
                    }
                    for (var k = 0; k < obj.all_gamer_info[j].an_gang.length; k++) {
                        appData.player[i].pengGang.push({
                            "num": appData.player[i].pengGang.length,
                            "card": Math.ceil(obj.all_gamer_info[j].an_gang[k]),
                            "step": 1,
                            "type": 3
                        })
                    }
                }
            }
        }
        if (appData.game.round == 0)
            operationMethod.positionReset();

    },
    receiveMyCard: function(obj) {
        appData.animate.animate1 = 0;
        appData.animate.animate2 = 14;
        appData.player[0].card = new Array();
        for (var i = 0; i < obj.data.my_card.length; i++) {
            obj.data.my_card[i] = Math.ceil(obj.data.my_card[i])
            if (obj.data.my_card[i] < 100 && appData.game.joker == 1)
                obj.data.my_card[i] = obj.data.my_card[i] + 100;
            appData.player[0].card.unshift({
                "num": obj.data.my_card.length - 1 - i,
                "isSelect": false,
                "card": obj.data.my_card[i],
            })
        }
        $(".startBack").show();
        operationMethod.myCardAnimate2();
    },
    receiveGameStart: function(obj) {
        appData.game.countdown = 0;
        $(".roundPause1").hide();
        operationMethod.nextRoundSet();
        appData.game.is_play = true;
        appData.game.round = obj.data.game_num;
        appData.game.flip_card = Math.ceil(obj.data.flip_card);
        appData.game.joker_card = Math.ceil(obj.data.joker_card);
        appData.game.banker_id = Math.ceil(obj.data.banker_id);
        appData.game.status = 2;
        for (var i = 0; i < appData.player.length; i++) {
            if (appData.player[i].ticket_checked == 0 && i == 0) {
                if (appData.isAA) {
                    appData.roomCard = appData.roomCard - appData.game.ticket_count;
                }
            }
            if (i != 0) {
                for (var j = 0; j < 13; j++) {
                    appData.player[i].card.push({
                        "num": j,
                        "card": "",
                    })
                }
            }
            appData.player[i].account_status = 4;
            appData.player[i].ticket_checked = 1;
        }
        controlMethod.m4aAudioPlay("start");
    },
    receiveNotyChooseCard: function(obj) {
        if (appData.game.status == 2) {
            for (var i = 0; i < appData.player.length; i++) {
                appData.player[i].playing_status = 1;
                if (appData.player[i].account_id == obj.data.account_id) {
                    appData.player[i].playing_status = Math.ceil(obj.data.playing_status);
                    appData.player[i].gang_flag = Math.ceil(obj.data.gang_flag);
                    appData.player[i].hu_flag = Math.ceil(obj.data.hu_flag);
                    if (Trim(obj.data.new_card) != "") {
                        if (appData.player[0].card.length + (appData.player[0].pengGang.length * 3) != 13 && i == 0) {
                            sendMethod.sendPullRoomInfo();
                            return 0;
                        }

                        appData.player[i].cardNew.card = Math.ceil(obj.data.new_card);
                        if (appData.player[i].cardNew.card < 100 && (obj.data.remain_count == 83 && appData.game.joker == 1)) {
                            appData.player[i].cardNew.card = appData.player[i].cardNew.card + 100;
                        }
                        appData.player[i].cardNew.isSelect = false;
                        appData.player[i].cardNew.isShow = true;
                        appData.game.remain_count = Math.ceil(obj.data.remain_count) - 1;
                    } else {
                        if (i == 0 && appData.player[0].card.length + (appData.player[0].pengGang.length * 3) != 14) {
                            sendMethod.sendPullRoomInfo();
                            return 0;
                        }
                        appData.game.remain_count = Math.ceil(obj.data.remain_count);
                    }
                    appData.game.light = i + 1;

                }
            }
            appData.game.limit_time = Math.ceil(obj.data.limit_time);
            appData.game.time.time = Math.ceil(obj.data.limit_time);
        }
        if (!appData.game.isPlaying) {
            operationMethod.timeLimit(appData.game.time.time);
        }
        if (appData.animate.animate2 != 0) {
            appData.game.time.time = 0;
            if (appData.game.joker == 1) {
                setTimeout(function() {
                    for (var i = 0; i < appData.player.length; i++) {
                        if (appData.player[i].playing_status > 1 && i == 0) {
                            appData.player[i].is_operation = false;
                            if (appData.player[i].cardNew.card < 100)
                                appData.player[i].cardNew.card = appData.player[i].cardNew.card + 100;
                        }
                    }
                }, 6000)

            } else {
                setTimeout(function() {
                    for (var i = 0; i < appData.player.length; i++) {
                        if (appData.player[i].playing_status > 1 && i == 0) {
                            appData.player[i].is_operation = false;
                        }
                    }
                }, 2000)
            }
        } else {
            if (Trim(obj.data.new_card) == "") {
                for (var i = 0; i < appData.player.length; i++) {
                    if (appData.player[i].playing_status > 1 && i == 0) {
                        appData.player[i].is_operation = false;
                    }
                }
            } else {
                setTimeout(function() {
                    for (var i = 0; i < appData.player.length; i++) {
                        if (appData.player[i].playing_status > 1 && i == 0) {
                            appData.player[i].is_operation = false;
                        }
                    }
                }, 750)
            }
        }
    },
    receiveThrowOutCard: function(obj) {
        for (var i = 0; i < appData.player.length; i++) {
            if (appData.player[i].account_id == obj.data.account_id) {
                if (!appData.player[i].cardNew.isShow && i != 0)
                    appData.player[i].card.splice(appData.player[i].card.length - 1, 1);
                appData.game.last_user = obj.data.account_id;
                appData.game.last_discard = Math.ceil(obj.data.card);
                if (i != 0) {
                    controlMethod.m4aAudioPlay(Math.ceil(obj.data.card) % 100);
                    operationMethod.dicard(i, obj.data.card);
                    appData.player[i].cardNew.isShow = false;
                } else if (obj.data.is_passive == 1 && i == 0) {
                    if (appData.player[0].cardNew.isShow && appData.player[0].cardNew.card == obj.data.card) {
                        appData.player[0].cardNew.isShow = false;
                        appData.player[0].cardSet = appData.player[0].cardNew.card;
                        operationMethod.dicard(0);
                    } else {
                        for (j = 0; j < appData.player[0].card.length; j++) {
                            if (appData.player[0].card[j].card == obj.data.card) {
                                appData.player[0].cardSet = appData.player[0].card[j].card;
                                appData.player[0].card.splice(j, 1);
                                operationMethod.cardMove(j);
                                operationMethod.dicard(0);
                            }
                        }
                    }
                    controlMethod.m4aAudioPlay(Math.ceil(obj.data.card) % 100);
                    appData.player[0].is_operation = true;
                }
                break;
            }
        }
    },
    receiveNotyPengGang: function(obj) {
        if (i == 0 && appData.player[0].card.length + (appData.player[0].pengGang.length * 3) != 13) {
            sendMethod.sendPullRoomInfo();
            return 0;
        }
        appData.player[0].is_operation = true;
        for (var i = 0; i < appData.player.length; i++) {
            appData.player[i].playing_status = 1;
        }
        appData.player[0].playing_status = Math.ceil(obj.data.playing_status);
        if (appData.player[0].playing_status > 1) {
            setTimeout(function() {
                appData.player[0].is_operation = false;
            }, 750)
        }
        appData.game.limit_time = Math.ceil(obj.data.limit_time);
        appData.game.time.time = Math.ceil(obj.data.limit_time);
        if (!appData.game.isPlaying)
            operationMethod.timeLimit(appData.game.time.time);
    },
    receiveNotyQiangGang: function(obj) {
        appData.player[0].playing_status = Math.ceil(obj.data.playing_status);
        if (appData.player[0].playing_status > 1) {
            setTimeout(function() {
                appData.player[0].is_operation = false;
            }, 700)
        }
        appData.game.limit_time = Math.ceil(obj.data.limit_time);
        appData.game.time.time = Math.ceil(obj.data.limit_time);
        appData.game.qianggang_card = Math.ceil(obj.data.card);
        if (!appData.game.isPlaying)
            operationMethod.timeLimit(appData.game.time.time);
    },
    receiveQiangGangHu: function(obj) {
        for (var i = 0; i < appData.player.length; i++) {
            if (appData.player[i].account_id == obj.data.account_id) {
                operationMethod.showZi(i, 3);
                controlMethod.m4aAudioPlay("opHu");
                appData.player[i].gangScore = obj.data.score;
            } else if (appData.player[i].account_id == obj.data.loser_id) {
                appData.player[i].gangScore = -obj.data.score;
            }
        }
        setTimeout(function() {
            appData.game.showGangScore = true;
            setTimeout(function() {
                appData.game.showGangScore = false;
                for (var i = 0; i < appData.player.length; i++) {
                    appData.player[i].gangScore = 0;
                }
            }, 2400)
        }, 100)
    },
    receivePeng: function(obj) {
        for (var i = 0; i < appData.player.length; i++) {
            if (appData.player[i].account_id == obj.data.account_id) {
                operationMethod.peng(i, 1);
                operationMethod.showZi(i, 1);
                controlMethod.m4aAudioPlay("opPeng");
                appData.player[i].is_operation = true;
            }
        }
    },
    receiveGang: function(obj) {
        for (var i = 0; i < appData.player.length; i++) {
            if (appData.player[i].account_id == obj.data.account_id) {
                operationMethod.gang(i, obj.data.type, obj.data.card);
                operationMethod.showZi(i, 2);
                controlMethod.m4aAudioPlay("opGang");
            }
        }
    },
    receiveJiaGangScore: function(obj) {
        for (var i = 0; i < appData.player.length; i++) {
            if (appData.player[i].account_id == obj.data.account_id) {
                operationMethod.qianggang(i);
                controlMethod.m4aAudioPlay("opGang");
                operationMethod.showZi(i, 2);
            }
        }
    },
    receiveWin: function(obj) {
        for (var i = 0; i < appData.player.length; i++) {
            if (appData.player[i].cardNew.card != "") {
                appData.player[i].cardNew.card = Math.ceil(obj.data.win_card);
            }
            appData.player[i].end_show = true;
            if (appData.player[i].account_id == obj.data.winner_id) {
                appData.player[i].temp_score = Math.ceil(obj.data.score) * 3;
                operationMethod.showZi(i, 3);
                controlMethod.stopAudio("backMusic");
                controlMethod.m4aAudioPlay("opHu");
                if (i == 0) {
                    setTimeout(function() {
                        controlMethod.m4aAudioPlay("win");
                    }, 2000)
                } else {
                    setTimeout(function() {
                        controlMethod.m4aAudioPlay("lose");
                    }, 2000)
                }
                setTimeout(function() {
                    controlMethod.m4aAudioPlay("backMusic");
                }, 6000)
            } else {
                appData.player[i].temp_score = -Math.ceil(obj.data.score);
            }
            appData.player[i].playing_status = 1;
            if (i != 0) {
                appData.player[i].card = [];
                for (var j = 0; j < obj.data.player_cards.length; j++) {
                    if (appData.player[i].account_id == obj.data.player_cards[j].account_id) {
                        for (var k = 0; k < obj.data.player_cards[j].cards.length; k++) {
                            appData.player[i].card.unshift({
                                "num": obj.data.player_cards[j].cards.length - 1 - k,
                                "isSelect": false,
                                "card": Math.ceil(obj.data.player_cards[j].cards[k]),
                            })
                        }
                        break;
                    }
                }
            }
        }
        operationMethod.boardSet(obj.data.score_board, obj.data.score_summary);
        appData.game.round = Math.ceil(obj.data.game_num);
        appData.game.total_num = Math.ceil(obj.data.total_num);
        appData.game.time.time = 0;
        appData.game.countdown = 180;

        if (obj.data.winner_id == -1 || obj.data.horse_cards.length == 0) {
            appData.game.endStep = 6;
        } else {
            appData.game.endStep = 0;
            appData.game.ma = [];
            for (h = 0; h < obj.data.horse_cards.length; h++) {
                appData.game.ma.push({ "card": Math.ceil(obj.data.horse_cards[h].card), "isMa": obj.data.horse_cards[h].win, "num": h })
            }
        }
        setTimeout(function() {
            operationMethod.endAnimate();
        }, 2200)

    },
    receiveBroadcastVoice: function(obj) {
        for (var i = 0; i < appData.player.length; i++) {
            if (appData.player[i].account_id == obj.data.account_id && i != 0) {
                controlMethod.m4aAudioPlay("message" + obj.data.voice_num);
                controlMethod.messageSay(i, obj.data.voice_num);
                break;
            }
        }
    },

}

var connectSocket = function connectSocket(url, openCallback, messageCallback, closeCallback, errorCallback) {
    ws = new WebSocket(url);
    ws.onopen = openCallback;
    ws.onmessage = messageCallback;
    ws.onclose = closeCallback;
    ws.onerror = errorCallback;
}

var wsOpenCallback = function wsOpenCallback(data) {
    console.log('websocket is opened');
    appData.connectOrNot = true;
    if (appData.heartbeat) {
        clearInterval(appData.heartbeat);
    }
    appData.heartbeat = setInterval(function() {
        appData.socketStatus = appData.socketStatus + 1;
        if (appData.socketStatus > 2) {
            appData.connectOrNot = false;
        } else if (appData.socketStatus > 4) {
            if (appData.isReconnect) {
                window.location.href = window.location.href + "&id=" + 10000 * Math.random();
            }
        }
        ws.send('@');
    }, 3000);
    sendMethod.sendPrepareJoinRoom();
}

var wsMessageCallback = function wsMessageCallback(evt) {
    appData.connectOrNot = true;

    if (evt.data == '@') {
        appData.socketStatus = 0;
        return 0;
    }

    var obj = eval('(' + evt.data + ')');

    if (obj.result == -201) {
        controlMethod.showAlert(31, obj.result_message);
    } else if (obj.result == -202) {
        appData.isReconnect = false;
        sendMethod.closeSocket();
        controlMethod.showAlert(32, obj.result_message);
    } else if (obj.result == -203) {
        methods.reloadView();
    }
    
    if (obj.result != 0) {
        if (obj.operation == "JoinRoom") {

            if (obj.result == 1) {
                if (obj.data.alert_type == 1) {
                    controlMethod.showAlert(1, obj.result_message);
                } else if (obj.data.alert_type == 2) {
                    controlMethod.showAlert(2, obj.result_message);
                } else if (obj.data.alert_type == 3) {
                    controlMethod.showAlert(11, obj.result_message);
                } else {
                    controlMethod.showAlert(7, obj.result_message);
                }
            } else if (obj.result == -1) {
                controlMethod.showAlert(7, obj.result_message);
            } else {
                controlMethod.showAlert(7, obj.result_message);
            }

        } else if (obj.operation == "ReadyStart") {
            if (obj.result == 1) {
                controlMethod.showAlert(1, obj.result_message);
            }
        } else if (obj.operation == "PrepareJoinRoom") {

            if (obj.result == 1) {
                if (obj.data.alert_type == 1) {
                    controlMethod.showAlert(1, obj.result_message);
                } else if (obj.data.alert_type == 2) {
                    controlMethod.showAlert(2, obj.result_message);
                } else if (obj.data.alert_type == 3) {
                    controlMethod.showAlert(11, obj.result_message);
                } else {
                    controlMethod.showAlert(7, obj.result_message);
                }
            } else if (obj.result == -1) {
                controlMethod.showAlert(7, obj.result_message);
            } else {
                controlMethod.showAlert(7, obj.result_message);
            }
        } else if (obj.operation == "PullRoomInfo") {
            window.location.href = window.location.href + "?id=" + 10000 * Math.random();
        } else if (obj.operation == "ChooseCard" || obj.operation == "Peng" || obj.operation == "Gang") {
            window.location.href = window.location.href + "?id=" + 10000 * Math.random();
        } else if (obj.operation == "CreateRoom") {
            if (obj.result == 1) {
                controlMethod.showAlert(1, obj.result_message);
            }
        } else if (obj.operation == "ActivateRoom") {
            if (obj.result == 1) {
                controlMethod.showAlert(1, obj.result_message);
            } else if (obj.result == -1) {
                sendMethod.sendPrepareJoinRoom();
            }
        } else {
            errorSocket(obj.operation, JSON.stringify(obj))
        }
        if (appData.player.length > 0)
            appData.player[0].is_operation = false;
    } else {
        if (obj.operation == "PrepareJoinRoom") {
            receiveMethod.receivePrepareJoinRoom(obj);
        } else if (obj.operation == "JoinRoom") {
            receiveMethod.receiveJoinRoom(obj);
        } else if (obj.operation == "UpdateAccountStatus") {
            receiveMethod.receiveUpdateAccountStatus(obj);
        } else if (obj.operation == "AllGamerInfo") {
            receiveMethod.receiveAllGamerInfo(obj);
        } else if (obj.operation == "UpdateGamerInfo") {
            receiveMethod.receiveUpdateGamerInfo(obj);
        } else if (obj.operation == "AccountGeo") {
            receiveMethod.receiveAccountGeo(obj);
        } else if (obj.operation == "PullRoomInfo") {
            receiveMethod.receivePullRoomInfo(obj);
        } else if (obj.operation == "MyCard") {
            receiveMethod.receiveMyCard(obj);
        } else if (obj.operation == "GameStart") {
            receiveMethod.receiveGameStart(obj);
        } else if (obj.operation == "NotyChooseCard") {
            receiveMethod.receiveNotyChooseCard(obj);
        } else if (obj.operation == "ThrowOutCard") {
            receiveMethod.receiveThrowOutCard(obj);
        } else if (obj.operation == "NotyPengGang") {
            receiveMethod.receiveNotyPengGang(obj);
        } else if (obj.operation == "NotyQiangGang") {
            receiveMethod.receiveNotyQiangGang(obj);
        } else if (obj.operation == "QiangGangHu") {
            receiveMethod.receiveQiangGangHu(obj);
        } else if (obj.operation == "Peng") {
            receiveMethod.receivePeng(obj);
        } else if (obj.operation == "Gang") {
            receiveMethod.receiveGang(obj);
        } else if (obj.operation == "JiaGangScore") {
            receiveMethod.receiveJiaGangScore(obj);
        } else if (obj.operation == "Win") {
            receiveMethod.receiveWin(obj);
        } else if (obj.operation == "BroadcastVoice") {
            receiveMethod.receiveBroadcastVoice(obj);
        } else if (obj.operation == "BreakRoom") {
            controlMethod.showAlert(7, "三分钟未开局，房间已自动结算")
        }
    };
}


var wsCloseCallback = function wsCloseCallback(data) {
    console.log("websocket closed：");
    //  console.log(data);
    appData.connectOrNot = false;
    reconnectSocket();
}

var wsErrorCallback = function wsErrorCallback(data) {
    console.log("websocket onerror：");
    //   console.log(data);
    appData.connectOrNot = false;
    //reconnectSocket();
}

var reconnectSocket = function reconnectSocket() {
    if (!appData.isReconnect) {
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
    if (globalData.room_status == 4) {
        return 0;
    }

    connectSocket(globalData.socket, wsOpenCallback, wsMessageCallback, wsCloseCallback, wsErrorCallback);
}

//Vue方法
var methods = {
    showMessage: controlMethod.showMessage,
    hideMessage: controlMethod.hideMessage,
    messageOn: controlMethod.messageOn,
    showShop: controlMethod.showShop,
    hideShop: controlMethod.hideShop,
    shopBuy: controlMethod.shopBuy,
    showInvite: controlMethod.showInvite,
    showAlert: controlMethod.showAlert,
    closeInvite: controlMethod.closeInvite,
    closeAlert: controlMethod.closeAlert,
    createRoom: controlMethod.createRoom,
    createNewRoom: controlMethod.createNewRoom,
    sitDown: controlMethod.sitDown,
    getCards: controlMethod.getCards,
    cancelCreate: controlMethod.cancelCreate,
    selectChange: controlMethod.selectChange,
    createNew: controlMethod.createNew,
    createCommit: controlMethod.createCommit,
    closeRull: controlMethod.closeRull,
    showRull: controlMethod.showRull,
    showRecord: controlMethod.showRecord,
    closeRecord: controlMethod.closeRecord,
    home: controlMethod.home,
    selectCard: controlMethod.selectCard,
    click: operationMethod.click,
    nextRound: operationMethod.nextRound,
    chooseCard: operationMethod.chooseCard,
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
            stopSound('backMusic');
            playSound('backMusic', 'loop');
        } else {
            stopSound('backMusic');
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
    bindPhone: function() {
        var validPhone = checkPhone(appData.sPhone);
        var validAuthcode = checkAuthcode(appData.sAuthcode);

        if (validPhone == false) {
            controlMethod.showAlert(21, '手机号码有误，请重填');
            return;
        }

        if (validAuthcode == false) {
            controlMethod.showAlert(21, '验证码有误，请重填');
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
            controlMethod.showAlert(21, '手机号码有误，请重填');
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
    if (!(/^1(3|4|5|7|8)\d{9}$/.test(phone))) {
        return false;
    } else {
        return true;
    }
}

function checkAuthcode(code) {
    if (code == '' || code == undefined) {
        return false;
    }

    var reg = new RegExp("^[0-9]*$");
    if (!reg.test(code)) {
        return false;
    } else {
        return true;
    }
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

var wsctop = 0;

function disable_scroll() {
    wsctop = $(window).scrollTop(); //记住滚动条的位置
    $("body").addClass("modal-show");
}

function enable_scroll() {
    $("body").removeClass("modal-show");
    $(window).scrollTop(wsctop); //弹框关闭时，启动滚动条，并滚动到原来的位置
}

var shareContent = '';
var wxReady = false;

function getShareContent() {
    shareContent = "\n";
    if (appData.rullInfo.joker == 0) {
        shareContent = shareContent + '鬼牌：无鬼牌';
    } else if (appData.rullInfo.joker == 1) {
        shareContent = shareContent + '鬼牌：翻牌当鬼';
    } else {
        shareContent = shareContent + '鬼牌：红中当鬼';
    }

    if (appData.rullInfo.horse_count == 0) {
        shareContent = shareContent + '  抓马：不跑马';
    } else if (appData.rullInfo.horse_count == 4) {
        shareContent = shareContent + '  抓马：4匹';
    } else if (appData.rullInfo.horse_count == 6) {
        shareContent = shareContent + '  抓马：6匹';
    } else if (appData.rullInfo.horse_count == 8) {
        shareContent = shareContent + '  抓马：8匹';
    } else if (appData.rullInfo.horse_count == 1) {
        shareContent = shareContent + '  抓马：爆炸马';
    }
    if (appData.rullInfo.qianggang == 1 || appData.rullInfo.chengbao == 1) {
        var cardContent = '  规则：';
        if (appData.rullInfo.qianggang == 1) {
            cardContent = cardContent + ' 抢杠全包';
        }

        if (appData.rullInfo.chengbao == 1) {
            cardContent = cardContent + ' 杠爆全包';
        }
        shareContent = shareContent + cardContent;
    }

    if (appData.rullInfo.ticket_count == 1) {
        shareContent = shareContent + '  房卡：8局x1张房卡';
    } else {
        shareContent = shareContent + '  房卡：16局x2张房卡';
    }
};
var wxModule = {
    config: function() {
        if (!wxReady)
            return 0;
        getShareContent();

        //alert('wx.ready');
        //userData.nickname + "邀请你加入游戏，房间" + globalData.roomNumber + 
        wx.onMenuShareTimeline({
            title: "广东麻将" + '(房间号:' + globalData.roomNumber + ')',
            desc: shareContent,
            link: globalData.roomUrl,
            imgUrl: globalData.imageUrl + "files/images/majiang/share_icon.jpg",
            success: function() {},
            cancel: function() {}
        });
        wx.onMenuShareAppMessage({
            title: "广东麻将" + '(房间号:' + globalData.roomNumber + ')',
            desc: shareContent,
            link: globalData.roomUrl,
            imgUrl: globalData.imageUrl + "files/images/majiang/share_icon.jpg",
            success: function() {},
            cancel: function() {}
        });

    },
};



//Vue生命周期
var vueLife = {
    vmCreated: function() {
        console.log('vmCreated')
        controlMethod.initialize();
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
    destroyed: vueLife.vmDestroyed,
});

if (globalData.room_status == 4) {

    try {
        var obj = eval('(' + globalData.balance_scoreboard + ')');

        console.log(obj)
        setTimeout(function() {
            receiveMethod.receiveLastScoreboard(obj);
        }, 0)
    } catch (error) {
        setTimeout(function() {
            receiveMethod.receiveLastScoreboard("");
        }, 0)
    }
}


$(function() {
    var body = document.getElementById("body");
    body.addEventListener("touchmove", touchMovePre, false);

    function touchMovePre(event) {
        if (!appData.isShowRecord && !appData.isShowMessage) {
            event.preventDefault();
        }
    }
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
            audioOn = false;
            stopSound("backMusic");
        } else if (sessionStorage.isPaused !== "true") {
            audioOn = true;
            playSound("backMusic", "loop");
        }
    }
    if (typeof document.addEventListener === "undefined" || typeof hidden === "undefined") {
        alert("This demo requires a browser such as Google Chrome that supports the Page Visibility API.");
    } else {
        document.addEventListener(visibilityChange, handleVisibilityChange, false);
    }

    //////////////////长按蒙板
    var cardPart = document.getElementById("prevent");
    cardPart.addEventListener("touchstart", touchStart, false);

    function touchStart(event) {
        event.preventDefault();
    }

})



wx.config({
    debug: false,
    appId: configData.appId,
    timestamp: configData.timestamp,
    nonceStr: configData.nonceStr,
    signature: configData.signature,
    jsApiList: [
        'onMenuShareTimeline', 'onMenuShareAppMessage', 'hideMenuItems', 'getLocation'
    ]
});
wx.ready(function() {
    wxReady = true;
    wx.hideMenuItems({
        menuList: [
                "menuItem:copyUrl",
                "menuItem:share:qq",
                "menuItem:share:weiboApp",
                "menuItem:favorite",
                "menuItem:share:facebook",
                "menuItem:share:QZone",
                "menuItem:refresh"

            ] // 要隐藏的菜单项，只能隐藏“传播类”和“保护类”按钮，所有menu项见附录3
    });
    wx.onMenuShareTimeline({
        title: "广东麻将",
        desc: "朋友间PK神器,房间" + globalData.roomNumber,
        link: globalData.roomUrl,
        imgUrl: globalData.imageUrl + 'files/images/majiang/share_icon.jpg',
        success: function() {
            // 用户确认分享后执行的回调函数
        },
        cancel: function() {
            // 用户取消分享后执行的回调函数
        }
    });
    wx.onMenuShareAppMessage({
        title: "广东麻将",
        desc: "朋友间PK神器,房间" + globalData.roomNumber,
        link: globalData.roomUrl,
        imgUrl: globalData.imageUrl + 'files/images/majiang/share_icon.jpg',
        success: function() {
            // 用户确认分享后执行的回调函数
        },
        cancel: function() {
            // 用户取消分享后执行的回调函数
        }
    });
    if (globalData.room_status != 4) {
        loadAudioFile(globalData.fileUrl + 'files/audio/majiang/back.mp3', "backMusic");
        var audioList1 = ["21", "22", "23", "24", "25", "26", "27", "28", "29", "41", "42", "43", "44", "45", "46", "47", "48", "49", "61", "62", "63", "64", "65", "66", "67", "68", "69", "80", "83", "86", "89", "93", "96", "99", "opHu", "opGang", "opPeng", "start", "win", "lose"];
        for (var i = 0; i < audioList1.length; i++) {
            loadAudioFile(globalData.fileUrl + 'files/audio/majiang/' + audioList1[i] + '.m4a', audioList1[i]);
        }
        var audioList2 = ["message1", "message2", "message3", "message4", "message5", "message6", "message7", "message8", "message0"];
        for (var i = 0; i < audioList2.length; i++) {
            loadAudioFile(globalData.fileUrl + 'files/audio/sound/' + audioList2[i] + '.m4a', audioList2[i]);
        }
    }

    wx.getLocation({
        type: 'wgs84', // 默认为wgs84的gps坐标，如果要返回直接给openLocation用的火星坐标，可传入'gcj02'
        success: function(res) {
            if (appData.connectOrNot && appData.game.room != 0) {
                sendMethod.sendUploadGeo(res.longitude, res.latitude);
            } else {
                appData.position.positionReady = true;
                appData.position.longitude = res.longitude;
                appData.position.latitude = res.latitude;
            }
        }
    });
});
wx.error(function(res) {
    //     alert("error: " + res.errMsg);  
});



var per = window.innerWidth / 530;
window.AudioContext = window.AudioContext || window.webkitAudioContext || window.mozAudioContext || window.msAudioContext;
var context = new window.AudioContext();
var audioBuffer = new Array();

function stopSound(name) {
    if (audioBuffer == undefined) {
        return;
    }

    for (var i = 0; i < audioBuffer.length; i++) {
        if (audioBuffer[i].name == name && audioBuffer[i].source) {
            audioBuffer[i].source.stop(0);
            audioBuffer[i].source = null;
        }
    }
}

function playSound(name, loop) {

    if (name == "backMusic") {
        if (audioInfo.backMusic == 0) {
            return;
        }
    } else {
        if (audioInfo.messageMusic == 0) {
            return;
        }
    }

    try {
        if (WeixinJSBridge != undefined) {
            WeixinJSBridge.invoke('getNetworkType', {}, function(e) {
                for (var i = 0; i < audioBuffer.length; i++) {
                    if (audioBuffer[i].name == name) {
                        audioBuffer[i].source = null;
                        audioBuffer[i].source = context.createBufferSource();
                        audioBuffer[i].source.buffer = audioBuffer[i].buffer;
                        audioBuffer[i].source.loop = false;
                        if (loop == "loop") {
                            audioBuffer[i].source.loop = true;
                            var gainNode = context.createGain();
                            audioBuffer[i].source.connect(gainNode);
                            gainNode.connect(context.destination);
                            gainNode.gain.value = 0.3;

                        } else
                            audioBuffer[i].source.connect(context.destination);
                        audioBuffer[i].source.start(0);
                        break;
                    }
                }
            });
        }

    } catch (err) {

    }
}

function initSound(arrayBuffer, name) {
    context.decodeAudioData(arrayBuffer, function(buffer) { //解码成功时的回调函数
        audioBuffer.push({ "name": name, "buffer": buffer, "source": null });
        if (name == "backMusic") {
            audioOn = true;
            playSound(name, "loop");
        }
    }, function(e) { //解码出错时的回调函数
        console.log('Error decoding file', e);
    });
}

function loadAudioFile(url, name) {
    var xhr = new XMLHttpRequest(); //通过XHR下载音频文件
    xhr.open('GET', url, true);
    xhr.responseType = 'arraybuffer';
    xhr.onload = function(e) { //下载完成
        initSound(this.response, name);
    };
    xhr.send();
}

function Trim(str) {
    return str.replace(/(^\s*)|(\s*$)/g, "");
}

function by(new1) {
    return function(o, p) {
        var a, b;
        if (typeof o === "object" && typeof p === "object" && o && p) {
            a = o[new1];
            b = p[new1];
            if (a === b) {
                return false;
            }
            if (typeof a === typeof b) {
                return a < b ? 1 : -1;
            }
            return typeof a < typeof b ? 1 : -1;
        } else {
            throw ("error");
        }
    }
}

function rad(d) {
    return d * Math.PI / 180.0;
}

function logMessage(d) {
    console.log(d);
}

function LantitudeLongitudeDist(lon1, lat1, lon2, lat2) {
    var EARTH_RADIUS = 6378137;
    var radLat1 = rad(lat1);
    var radLat2 = rad(lat2);
    var radLon1 = rad(lon1);
    var radLon2 = rad(lon2);

    if (radLat1 < 0)
        radLat1 = Math.PI / 2 + Math.abs(radLat1); // south  
    if (radLat1 > 0)
        radLat1 = Math.PI / 2 - Math.abs(radLat1); // north  
    if (radLon1 < 0)
        radLon1 = Math.PI * 2 - Math.abs(radLon1); // west  
    if (radLat2 < 0)
        radLat2 = Math.PI / 2 + Math.abs(radLat2); // south  
    if (radLat2 > 0)
        radLat2 = Math.PI / 2 - Math.abs(radLat2); // north  
    if (radLon2 < 0)
        radLon2 = Math.PI * 2 - Math.abs(radLon2); // west  
    var x1 = EARTH_RADIUS * Math.cos(radLon1) * Math.sin(radLat1);
    var y1 = EARTH_RADIUS * Math.sin(radLon1) * Math.sin(radLat1);
    var z1 = EARTH_RADIUS * Math.cos(radLat1);

    var x2 = EARTH_RADIUS * Math.cos(radLon2) * Math.sin(radLat2);
    var y2 = EARTH_RADIUS * Math.sin(radLon2) * Math.sin(radLat2);
    var z2 = EARTH_RADIUS * Math.cos(radLat2);

    var d = Math.sqrt((x1 - x2) * (x1 - x2) + (y1 - y2) * (y1 - y2) + (z1 - z2) * (z1 - z2));
    //余弦定理求夹角  
    var theta = Math.acos((EARTH_RADIUS * EARTH_RADIUS + EARTH_RADIUS * EARTH_RADIUS - d * d) / (2 * EARTH_RADIUS * EARTH_RADIUS));
    var dist = theta * EARTH_RADIUS;
    if (dist >= 1000) {
        return Math.round(dist / 1000) + "公里";
    } else {
        return Math.round(dist) + "米";
    }
}