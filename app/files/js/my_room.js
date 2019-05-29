var width = window.innerWidth;
var height = window.innerHeight;
var roomType = getRequest();
var roomType = roomType?roomType.t:"";

var httpModule = {
    getRoomRecord : function (type,page) {
        var data = {type : type};
        data.page = page ? page : 1;
        var url = (roomType == "m")?'get_my_room':'get_room_record';
        Vue.http.post(globalData.baseUrl  + 'record/'+ url , data).then(function(response) {
            var data = response.body;
            if (data.result === 0) {
                if(data.data.curpage === 1){
                    appData.gameRecord = data.data.data;
                }else{
                    var record = data.data.data;
                    for(var k in record){
                        appData.gameRecord.push(record[k]);
                    }
                }
                if(data.data.curpage  < data.data.total_page){
                    appData.cur_room_page = data.data.curpage + 1 ;
                    appData.room_is_last = false;
                }else{
                    appData.room_is_last = true;
                    appData.cur_room_page = data.data.curpage;
                }

                globalData.currentGame = type;
            } else {
                viewMethods.clickShowAlert(7,data.result_message);
            }

        }, function(response) {
            logMessage(response.body);
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
    
    checkGameRecord : function (type) {
        httpModule.getRoomRecord(type,1);
    },
};

var appData = {
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
    'gameRecord' : [],
    'cur_room_page' : 1,
    'room_is_last' : false,
};

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

    checkGameRecord : viewMethods.checkGameRecord,
    
    checkRoomDetail : function (room) {
        Vue.http.post(globalData.baseUrl + 'record/room_detail_check', {'id': room.room_number, 'type': room.game_type})
            .then(function (res){
                var data = res.body;
                if (data.result == 0) {
                    if(room.is_close != 1){
                        viewMethods.clickShowAlert(7,'该房间还没有战绩哦');
                        return false;
                    }
                    viewMethods.clickShowAlert(7,data.result_message);
                }else{
                    var url = "/record/room_detail";
                    url += "?id=" + room.room_number;
                    url += "&type=" + room.game_type;
                    location.href = url;
                }
            });
    },

    getMoreRoom : function () {
        httpModule.getRoomRecord(globalData.currentGame,appData.cur_room_page);
    }

};

//Vue生命周期
var vueLife = {
	vmCreated: function () {
		logMessage('vmCreated')
        $("#loading").hide();
        $("#app-main").show();

        httpModule.getRoomRecord(globalData.currentGame);
        
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

var vmT = new Vue({
    el: '#pageTitle',
    data: {text: (roomType == 'm')?'开房查询':'战绩查询'},
    // methods: methods,
    // created: vueLife.vmCreated,
    // updated: vueLife.vmUpdated,
    // mounted: vueLife.vmMounted,
    // destroyed: vueLife.vmDestroyed,
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
function getRequest() { //获取url中的参数
    let url = window.location.search;
    let theRequest = new Object(); 
    if (url.indexOf("?") != -1) { 
        let urlParam = url.split('?');
        let strs = urlParam[1].split("&");
        for(var i = 0; i < strs.length; i ++) { 
            theRequest[strs[i].split("=")[0]] = unescape(strs[i].split("=")[1]); 
        } 
    } 
    return theRequest; 
} 
