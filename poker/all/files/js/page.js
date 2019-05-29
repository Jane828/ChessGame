
var httpModule = {
	getRoomCardInfo: function () {
        var data = {"dealer_num": globalData.dealerNum};

		Vue.http.post(globalData.baseUrl + 'f/roomCardInfo', data).then(function(response) {
			logMessage(response.body);
			var bodyData = response.body;

			if (bodyData.result == 0) {
				appData.roomCardInfo = bodyData.data.concat();

				for (var i = 0; i < appData.roomCardInfo.length; i++) {
					appData.roomCardInfo[i].num = i + 1;
					appData.roomCardInfo[i].price = Math.ceil(appData.roomCardInfo[i].price);
				}
                
                if (appData.roomCardInfo.length >= 1) {
                    appData.select = appData.roomCardInfo[0].goods_id;
                };
				
			} else {
				alert(bodyData.result_message);
			}

		}, function(response) {
			logMessage(response.body);
		});
	},
    getGameScore: function () {
        
        var data = {"account_id": userData.accountId, "from":dtStartDate, "to":dtEndDate, "dealer_num": globalData.dealerNum};

        Vue.http.post(globalData.baseUrl + 'activity/getGameScore', data).then(function(response) {
            logMessage(response.body);
            var bodyData = response.body;
            if (bodyData.result == 0) {
                appData.gameItems = [];
                var resultData = bodyData.data;
                for (var i = 0; i < resultData.length; i++) {
                    var temp = resultData[i];
                    var type = temp['game_type'];
                    var score = temp['score'];
                    if (score > 0) {
                        score = '+' + score;
                    }
                    appData.gameItems.push({"avatar":gameIcons[type], "name":gameNames[type],"score":score});
                }
            } else {
                alert(bodyData.result_message);
            }

        }, function(response) {
            logMessage(response.body);
        });
    },
	getActivityInfo: function () {
        var data = {"account_id": userData.accountId, "dealer_num": globalData.dealerNum};

		Vue.http.post(globalData.baseUrl  + 'f/getActivityInfo', data).then(function(response) {
			logMessage(response.body);
			var bodyData = response.body;

			if (bodyData.result == 0) {
				if(bodyData.data.length == 0) {
					if (appData.roomCard <= 0) {
						clickShowAlert(1, "房卡不足");
					} else{
						reconnectSocket();
						appData.is_connect = true;
					}
				} else {
					appData.activity = bodyData.data.concat();
					clickShowAlert(5, appData.activity[0].content);
				}
			} else {
				alert(bodyData.result_message);
			}

		}, function(response) {
			logMessage(response.body);
		});
	},
	getCards: function () {
		if (appData.activity.length < 1) {
			logMessage('activity length less than 1');
			return;
		}
        
        var data = {"account_id": userData.accountId, "activity_id": appData.activity[0].activity_id, "dealer_num": globalData.dealerNum};

		Vue.http.post(globalData.baseUrl + 'f/updateActivityOpt', data).then(function (response) {
			logMessage(response.body);
			var bodyData = response.body;

			if (bodyData.result == 0) {
				appData.roomCard = appData.roomCard + Math.ceil(appData.activity[0].ticket_count); 
				appData.activity.splice(0,1);

				if (appData.activity.length == 0) {
					reconnectSocket();
					appData.is_connect = true;
					viewMethods.clickCloseAlert();
				} else {
					clickShowAlert(5, appData.activity[0].content);
				}
			} else {
				alert(bodyData.result_message);
			}

		}, function(response) {
			logMessage(response.body);
		});
	},
	buyCard: function (goodsId) {
        var data = {"account_id": userData.accountId, "open_id": globalData.openId, "goods_id": goodsId, "dealer_num": globalData.dealerNum};
		Vue.http.post(globalData.baseUrl + 'index.php/wxpay/flower/getPaymentOpt', data).then(function (response) {
			logMessage(response.body);
			var bodyData = response.body;

			if (typeof bodyData.result == "undefined") {
				alert("购买失败，请重新操作");
				appData.isShowShopLoading = false;
			} else if (bodyData.result == "-1") {
				alert(bodyData.result_message);
				appData.isShowShopLoading = false;
			} else {
				var obj_data = bodyData.data;
				WeixinJSBridge.invoke("getBrandWCPayRequest", {
					appId: obj_data.appId,  
					timeStamp: obj_data.timeStamp,
					nonceStr: obj_data.nonceStr,
					"package": "prepay_id=" + obj_data.prepay_id,
					signType: obj_data.signType,
					paySign: obj_data.paySign
				}, function(res) {
					if (res.err_msg == "get_brand_wcpay_request:ok")  {
						alert("购买成功");
						appData.isShowShopLoading = false;
						appData.roomCard = parseInt(appData.roomCard) + parseInt(appData.ticket_count);
						viewMethods.clickHideShop();
						return 0;
					} else {
						alert("购买失败，请重新操作");
						appData.isShowShopLoading = false;
					}
				});
			}

		}, function(response) {
			alert("error");
			appData.isShowShopLoading = false;
		});
    },
    getAuthcode: function (phone) {
        var data = {"dealer_num": globalData.dealerNum, "phone":phone};

		Vue.http.post(globalData.baseUrl + 'account/getMobileSms', data).then(function(response) {
			logMessage(response.body);
			var bodyData = response.body;

			if (bodyData.result == 0) {
                appData.authcodeTime = 60;
			    authcodeTimer();
			    appData.authcodeType = 2;

			} else {
				viewMethods.clickShowAlert(7,bodyData.result_message);
			}

		}, function(response) {
			viewMethods.clickShowAlert(7,'获取验证码失败');
		});
    },
	bindPhone: function (phone,authcode) {
        var data = {"dealer_num": globalData.dealerNum, "phone":phone, "code":authcode};

		Vue.http.post(globalData.baseUrl + 'account/checkSmsCode', data).then(function(response) {
			
			var bodyData = response.body;
            
			if (bodyData.result == 0) {
                appData.isShowBindPhone = false;
                appData.isPhone = true;
                appData.isAuthPhone = 0;
                appData.phone = appData.sPhone;

				if (bodyData.data.card_count != null && bodyData.data.card_count != undefined && bodyData.data.card_count != '') {
					appData.roomCard = parseInt(appData.roomCard) + parseInt(bodyData.data.card_count);
				}
                
                if (bodyData.data.account_id != userData.accountId) {
                    viewMethods.clickShowAlert(23,bodyData.result_message);
                } else {
                    viewMethods.clickShowAlert(7,bodyData.result_message);
                }

                appData.sPhone = '';
                appData.sAuthcode = '';
				
			} else {
                viewMethods.clickShowAlert(7,bodyData.result_message);
			}

		}, function(response) {
            appData.authcodeTime = 0;
			viewMethods.clickShowAlert(7,"绑定失败");
		});
    },
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
            viewMethods.clickShowShop();
            if (!appData.is_connect) {
                reconnectSocket();
                appData.is_connect = true;
            }
        }
	},
	clickShowShop: function () {
		appData.select = 1;
        appData.ticket_count = 20;
        $(".shop .shopBody").animate({
            height:appData.width * 1.541 + "px"
        });
        appData.isShowShop = true;
	},
	clickHideShop: function () {
		$(".shop .shopBody").animate({
            height:0
        }, function() {
            appData.isShowShop = false;
        });
	},
    selectCard: function (num, count) {
        appData.select = num;
        appData.ticket_count = count;
    },
	clickGetCards: function () {
		httpModule.getCards();
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
	shopBuy: function () {
		if (appData.select > 0) {
			appData.isShowShopLoading = true;
            var goods_id = appData.select;
            httpModule.buyCard(goods_id);
		}
	},
    clickRedpackageRecord: function () {
        window.location.href = globalData.baseUrl + "ay/myRP";
    },
    clickSendRedPackage: function () {
        window.location.href = globalData.baseUrl + "ay/rp";
    },
    changeStartDate : function () {
        logMessage('start date：' + appData.startDate);
        var date = new Date(appData.startDate);
        var timestamp = convertTimestamp(date);
        
        //alert(timestamp);
        logMessage(timestamp);
        logMessage(dtEndTimestamp);
        if (timestamp > dtEndTimestamp) {
            appData.startDate = dtStartDate;
            //alert('开始时间不能大于结束时间');
            return;
        } else {
            dtStartDate = appData.startDate;
            dtStartTimestamp = timestamp;

            httpModule.getGameScore();
        }
    },
    changeEndDate : function () {
        logMessage('end date：' + appData.endDate);
        var date = new Date(appData.endDate);
        var timestamp = convertTimestamp(date);
        timestamp = timestamp + 86399;

        //alert(timestamp);

        if (timestamp > todayTimestamp) {
            appData.endDate = dtEndDate;
            //alert('结束时间不能大于今天');
            return;
        } else {
            dtEndDate = appData.endDate;
            dtEndTimestamp = timestamp;
            httpModule.getGameScore();
        }
    },
};

var width = window.innerWidth;
var height = window.innerHeight;
var isTimeLimitShow = false;
var viewOffset = 16;
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

var viewStyle = {
    bg: {
        position: 'absolute',
        top: '0px',
        left: '0px',
        width: width + 'px',
        height: '2280px',
        overflow: 'hidden',
        'background-color': '#0e0226'
    },
	user: {
		position: 'absolute',
		top: '0px',
		left: '0px',
		width: width + 'px',
		height: userViewHeight + 'px',
		overflow: 'hidden',
        'background-color': '#291c4d'
	},
    userAvatar: {
        position: 'absolute',
        top: avatarY + 'px',
        left: leftOffset + 'px',
        width: avatarWidth + 'px',
        height: avatarWidth + 'px',
        'background-color': '#0e0226',
        'border-radius': '4px',
        'border-width': '8px',
        'border-color': 'red'
    },
    userAvatarImg: {
        position: 'absolute',
        top:  2 + 'px',
        left: 2 + 'px',
        width: avatarWidth - 4 + 'px',
        height: avatarWidth - 4 + 'px',
        'border-radius': '4px'
    },
    userName: {
        position: 'absolute',
        top: avatarY + 'px',
        left: leftOffset * 2 + avatarWidth + 'px',
        width: width - avatarWidth * 2 + 'px',
        height: avatarWidth / 2 + 'px',
        'line-height': avatarWidth / 2 + 'px',
        'font-size': '13pt',
        color: 'white'
    },
    userCardIcon: {
        position: 'absolute',
        top:  avatarY + avatarWidth - 30 / 320 * width + 'px',
        left: leftOffset * 2 + avatarWidth + 'px',
        width:  17 / 320 * width + 'px',
        height:  23 / 320 * width + 'px',
    },
    userCardView: {
        position: 'absolute',
        top:  avatarY + avatarWidth - 27 / 320 * width + 'px',
        left: leftOffset * 2 + avatarWidth + 17 / 320 * width + 'px',
        width:  0.1 * width + 'px',
        height:  20 / 320 * width + 'px',
        'border-color': 'white',
        'border-width': '2px',
        color: 'white',
        'background-color': '#0e0226'
    },
    userCard: {
        position: 'absolute',
        top:  avatarY + avatarWidth - 27 / 320 * width + 'px',
        left: leftOffset * 2 + avatarWidth + 20 / 320 * width + 'px',
        width:  0.2 * width + 'px',
        height:  20 / 320 * width + 'px',
        'line-height': 20 / 320 * width + 'px',
        'border-radius': 10 / 320 * width + 'px',
        'border-color': 'white',
        'border-width': '2px',
        color: 'white',
        'background-color': '#0e0226'
    },
    userRecharge: {
        position: 'absolute',
        top:  avatarY + avatarWidth - 35 / 320 * width + 'px',
        left: width - leftOffset - 94 / 320 * width + 'px',
        width:  94 / 320 * width + 'px',
        height:  34 / 320 * width + 'px',
    },
    sendRedpackage: {
        position: 'absolute',
        top: width * 0.25 + viewOffset + 'px',
        left: '0px',
        width: width + 'px',
        height: width * 0.1375 + 'px' ,
        overflow: 'hidden',
        'background-color': '#291c4d'
    },
	redpackage: {
		position: 'absolute',
		top: width * 0.25 + viewOffset * 2 + 44 / 320 * width + 'px',
		left: '0px',
		width: width + 'px',
		height: width * 0.1375 + 'px' ,
        overflow: 'hidden',
        'background-color': '#291c4d'
	},
    rcIcon: {
        position: 'absolute',
        top: (width * 0.1375 - 30 / 320 * width) / 2 + 'px',
        left: leftOffset + 'px',
        width: 30 / 320 * width + 'px',
        height: 30 / 320 * width + 'px' ,
    },
    rcContent: {
        position: 'absolute',
        top: '0px',
        left: leftOffset * 2 + 30 / 320 * width + 'px',
        width: width / 2 + 'px',
        height: width * 0.1375 + 'px',
        'line-height': width * 0.1375 + 'px',
        'color': 'white',
        'font-size': '12pt',
    },
    rcArrow: {
        position: 'absolute',
        top: (width * 0.1375 - 18 / 320 * width) / 2 + 'px',
        left: width - leftOffset - 10 / 320 * width + 'px',
        width: 10 / 320 * width + 'px',
        height: 18 / 320 * width + 'px' ,
    },
	datepicker: {
		position: 'absolute',
		top: (80 + 44 * 2) / 320 * width + viewOffset * 3,
		left: '0px',
		width: width + 'px',
		height: width * 0.125 + 'px',
        overflow: 'hidden',
        'background-color': '#291c4d'
	},
    dtInputStart: {
        position: 'absolute',
        top: (width * 0.125 - 28 / 320 * width) / 2 + 'px',
        left: leftOffset + 'px',
        height: 28 / 320 * width + 'px',
        width: width * 0.4 + 'px',
        'font-size': '12pt',
    },
    dtInputEnd: {
        position: 'absolute',
        top: (width * 0.125 - 28 / 320 * width) / 2 + 'px',
        left: width - leftOffset - 0.4 * width + 'px',
        height: 28 / 320 * width + 'px',
        width: width * 0.4 + 'px',
        'font-size': '12pt',
    },
    dtContent: {
        position: 'absolute',
        top: '0px',
        left: '0px',
        height: width * 0.125 + 'px',
        width: width + 'px',
        'line-height': width * 0.125 + 'px',
        'font-size': '13pt',
        'text-align': 'center',
        color: 'white',
    },
    itemIcon: {
        position: 'absolute',
        top: (itemHeight - 50 / 320 * width) / 2 + 'px',
        left: leftOffset + 'px',
        height: 50 / 320 * width + 'px',
        width: 50 / 320 * width + 'px',
    },
    itemName: {
        position: 'absolute',
        top: '0px',
        left: leftOffset * 2 + 50 / 320 * width + 'px',
        height: itemHeight + 'px',
        width: 0.5 * width + 'px',
        'line-height': itemHeight + 'px',
        'font-size': '14pt',
        'text-align': 'left',
        color: 'white',
    },
    itemScore: {
        position: 'absolute',
        top: '0px',
        left: width - leftOffset - 0.5 * width + 'px',
        height: itemHeight + 'px',
        width: 0.5 * width + 'px',
        'line-height': itemHeight + 'px',
        'font-size': '15pt',
        'text-align': 'right',
        color: 'white',
    },
};

var appData = {
	'viewStyle': viewStyle,
	'width': window.innerWidth,
	'height': window.innerHeight,
	'roomCard': Math.ceil(globalData.card),
	'user': userData,
	'activity': [],
	'isShowInvite': false,
	'isShowAlert': false,
	'isShowShop': false,
	'isShowMessage': false,
	'alertType': 0,
	'alertText': '',
	'roomCardInfo': [],
    'select': 1,
    'ticket_count': 0,
    'isDealing': false,
    isShowShopLoading: false,
    'gameItems':[],
    itemY:itemY,
    itemHeight: 66 / 320 * width,
    itemOffset: itemOffset,
    startDate: '',
    endDate: '',
    isPhone:false,
    isShowBindPhone:false,
    'isAuthPhone':userData.isAuthPhone,
	'authCardCount':userData.authCardCount,
	'phone':userData.phone,
	'sPhone':'',
	'sAuthcode':'',
	'authcodeType':1,
	'authcodeText':'发送验证码',
	'authcodeTime':60,
	'phoneType':1,
	'phoneText':'绑定手机',
};


if (userData.phone != undefined && userData.phone.length >= 1) {
    logMessage(userData.phone);
    appData.isPhone = true;
    appData.phone = userData.phone;
    appData.phoneText = '修改手机号';
}

if (appData.isAuthPhone == 1) {
    appData.isShowBindPhone = true;
}

var gameIcons = {
    "1": globalData.baseUrl + 'files/images/activity/rc_icon_flowerwap.png',
    "2": globalData.baseUrl + 'files/images/activity/rc_icon_landlord.png',
    "6": globalData.baseUrl + 'files/images/activity/rc_icon_majiang.png',
    "5": globalData.baseUrl + 'files/images/activity/rc_icon_bull.png',
    "4": globalData.baseUrl + 'files/images/activity/rc_icon_texaspoker.png',
    "3": globalData.baseUrl + 'files/images/activity/rc_icon_showha.png',
    "9": globalData.baseUrl + 'files/images/activity/rc_icon_bull9.png',
    "10": globalData.baseUrl + 'files/images/activity/rc_icon_bullfight.png',
    "11": globalData.baseUrl + 'files/images/activity/rc_icon_roller.png',
};

var gameNames = {
    "1": '炸金花',
    "2": '斗地主',
    "6": '广东麻将',
    "5": '六人斗牛',
    "4": '德州扑克',
    "3": '梭哈',
    "9": '九人斗牛',
    "10": '斗公牛',
    "11": '滚轮子',
};

Date.prototype.format = function(fmt) { 
 var o = { 
        "M+" : this.getMonth()+1,                 //月份 
        "d+" : this.getDate(),                    //日 
        "h+" : this.getHours(),                   //小时 
        "m+" : this.getMinutes(),                 //分 
        "s+" : this.getSeconds(),                 //秒 
        "q+" : Math.floor((this.getMonth()+3)/3), //季度 
        "S"  : this.getMilliseconds()             //毫秒 
    }; 
    if(/(y+)/.test(fmt)) {
        fmt=fmt.replace(RegExp.$1, (this.getFullYear()+"").substr(4 - RegExp.$1.length)); 
    }
    for(var k in o) {
        if(new RegExp("("+ k +")").test(fmt)){
         fmt = fmt.replace(RegExp.$1, (RegExp.$1.length==1) ? (o[k]) : (("00"+ o[k]).substr((""+ o[k]).length)));
     }
 }
 return fmt; 
};

convertTimestamp = function (date) {
    var timestamp = Date.parse(date);
    timestamp = timestamp / 1000;
    return timestamp;
}

function funDate(aa){
    var date1 = new Date(),
    time1 = date1.getFullYear()+"-"+(date1.getMonth()+1)+"-"+date1.getDate();
    var date2 = new Date(date1);
    date2.setDate(date1.getDate()+aa);

    var year = date2.getFullYear();
    var month = date2.getMonth() + 1;
    var day = date2.getDate();
    var time2 = year + '-';

    var monthS = month + '-';
    
    if (monthS.length < 3) {
        time2 = time2 + '0' + month + '-';
    } else {
        time2 = time2 + month + '-';
    }
    
    var dayS = day + '-';
    if (dayS.length < 3) {
        time2 = time2 + '0' + day;
    } else {
        time2 = time2 + day;
    }

    return time2;
}

//Vue方法
var methods = {
	showShop: viewMethods.clickShowShop,
	hideShop: viewMethods.clickHideShop,
	shopBuy: viewMethods.shopBuy,
	showInvite: viewMethods.clickShowInvite,
	showAlert: viewMethods.clickShowAlert,
	showMessage: viewMethods.showMessage,
	closeInvite: viewMethods.clickCloseInvite,
	closeAlert: viewMethods.clickCloseAlert,
	getCards: viewMethods.clickGetCards,
	hideMessage: viewMethods.hideMessage,
	selectCard: viewMethods.selectCard,
    showRedpackageRecord:viewMethods.clickRedpackageRecord,
    showSendRedpackage:viewMethods.clickSendRedPackage,
    startDateChange: viewMethods.changeStartDate,
    endDateChange: viewMethods.changeEndDate,
    clickPhone:function () {
        appData.phoneText = '绑定手机';
        appData.phoneType = 1;
        appData.authcodeTime = 0;
        appData.authcodeText = '发送验证码';
        appData.authcodeType = 1;
        appData.isShowBindPhone = true;
    },
    hideBindPhone: function () {
        if (appData.phoneType == 1) {
            return;
        }
        
        appData.isShowBindPhone = false;
    },
    clickEditPhone:function () {
        appData.phoneText = '修改手机号';
        appData.phoneType = 2;
        appData.authcodeTime = 0;
        appData.authcodeText = '发送验证码';
        appData.authcodeType = 1;
        appData.isShowBindPhone = true;
    },
    bindPhone:function () {
        var validPhone = checkPhone(appData.sPhone);
		var validAuthcode = checkAuthcode(appData.sAuthcode);

		if (validPhone == false) {
            viewMethods.clickShowAlert(7,'手机号码有误，请重填'); 
			return;
		} 

		if (validAuthcode == false) {
            viewMethods.clickShowAlert(7,'验证码有误，请重填');
			return;
		} 
        
        httpModule.bindPhone(appData.sPhone,appData.sAuthcode);
    },
    getAuthcode:function () {
        if (appData.authcodeType != 1) {
			return;
		}

		var color = $('#authcode').css('background-color');
        if (color != 'rgb(64, 112, 251)') {
            return;
        }

        var validPhone = checkPhone(appData.sPhone);

		if (validPhone == false) {
            viewMethods.clickShowAlert(7,'手机号码有误，请重填'); 
			return;
		} 
        
        httpModule.getAuthcode(appData.sPhone);
    },
	phoneChangeValue:function () {
		var result = checkPhone(appData.sPhone);
        if (result) {
            $('#authcode').css('background-color','rgb(64,112,251)');
        } else {
            $('#authcode').css('background-color','lightgray');
        }
    },
    finishBindPhone:function () {
        window.location.href=window.location.href+"&id="+10000*Math.random();
    },
};

//Vue生命周期
var vueLife = {
	vmCreated: function () {
		logMessage('vmCreated')
        $("#loading").hide();
		$(".main").show();
        //httpModule.getRoomCardInfo();

        appData.startDate = '';
        //funDate(-7);
        appData.startDate = funDate(-7);
        appData.endDate = new Date().format("yyyy-MM-dd");

        dtStartDate = appData.startDate;
        dtStartTimestamp = '0';

        dtEndDate = appData.endDate;
        dtEndTimestamp = convertTimestamp(appData.endDate);
        dtEndTimestamp = dtEndTimestamp + 86399;
        todayTimestamp = dtEndTimestamp;

        dtStartTimestamp = convertTimestamp(appData.startDate);

        //httpModule.getGameScore();
        
	},
	vmUpdated: function () {
		logMessage('vmUpdated');
	},
	vmMounted: function () {
		logMessage('vmMounted');
	},
	vmDestroyed: function () {
		logMessage('vmDestroyed');
	}
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

    setTimeout(function () {
        authcodeTimer();
    }, 1000);
};
//******手机绑定

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

//微信配置
wx.config({
	debug:false,
	appId:configData.appId,
	timestamp:configData.timestamp,
	nonceStr:configData.nonceStr,
	signature:configData.signature,
	jsApiList:[ "onMenuShareTimeline", "onMenuShareAppMessage", "hideMenuItems" ]
});
wx.ready(function() {
    wx.hideOptionMenu();
});
wx.error(function(a) {});

function logMessage(message) {	
	console.log(message);
};

