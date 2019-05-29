var httpModule = {
    searchGroupMember: function () {
        
        var data = {
            "manager_id": userData.accountId,
            "page": appData.page,
            "nickname": appData.searchText,
        };

        Vue.http.post(globalData.baseUrl + 'manage/searchGroupMember', data).then(function(response) {
            
            var bodyData = response.body;
            if (bodyData.result == 0) {
                
                appData.memberCount = bodyData.sum_count;
                appData.page = bodyData.page;
                appData.sumPage = bodyData.sum_page;

                for (var i = 0; i < bodyData.data.length;i++) {
                    var item = bodyData.data[i];
                    appData.members.push({"name":item.nickname,"avatarUrl":item.avatar_url,"member_id":item.member_id,"status":item.status });
                }

            } else {
                logMessage(bodyData.result_message);
            }

            refreshBScroll();
			appData.canLoadMore = true;
			if (appData.page < appData.sumPage) {
				$('#moretext').text('上拉加载更多');
				$('#moretext').show();
			} else {
				$('#moretext').text('没有更多内容');
				$('#moretext').hide();
			}

        }, function(response) {
            appData.canLoadMore = true;
            logMessage(response.body);
        });
    },

    dealMember: function (item, type) {

        var data = {
            "manager_id": userData.accountId,
            "member_id": item.member_id,
            "type": type
        };

        logMessage(item);

        Vue.http.post(globalData.baseUrl + 'manage/dealMember', data).then(function(response) {
            
            var bodyData = response.body;
            if (bodyData.result == 0) {
                if(bodyData.data.status == 1){
                    logMessage("已经同意");
                    appData.selectAccount.status = 1;
                } else if(bodyData.data.status == 2){
                    appData.selectAccount.status = 2;
                    logMessage("已经删除");
                }  else if(bodyData.data.status == 3){
                    appData.selectAccount.status = 3;
                    logMessage("已经踢出");
                } else{
                    appData.selectAccount.status = 0;
                    // window.location.reload();
                }

                
            } else {
                logMessage(bodyData.result_message);
            }

        }, function(response) {
            logMessage(response.body);
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
var viewOffset = 16;
var itemOffset = 4;
var itemHeight = 66 / 320 * width;
var leftOffset = 8 / 320 * width;
var userViewHeight = 0.25 * width;
var avatarWidth = 0.21875 * width;
var avatarY = (userViewHeight - avatarWidth) / 2;
var itemY = (80 + 44 * 2 + 40) / 320 * width + viewOffset * 3 + itemOffset;
itemY = 0.965 * width + itemOffset;
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
		height: '25vw',
		overflow: 'hidden',
        'background-color': '#291c4d'
	},
    userAvatar: {
        position: 'absolute',
        top: '1.5vw',
        left: '2.5vw',
        width: '22vw',
        height: '22vw',
        'background-color': '#0e0226',
        'border-radius': '4px',
        'border-width': '8px',
        'border-color': 'red'
    },
    userAvatarImg: {
        position: 'absolute',
        top:  '1vw',
        left: '1vw',
        width: '21vw',
        height: '21vw',
        'border-radius': '4px'
    },
    userName: {
        position: 'absolute',
        top: '1.5vw',
        left:  '27vw',
        width: '50vw',
        height: '11vw',
        'line-height': '11vw',
        'font-size': '2.5vh',
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
		top: '84vw',
		left: '0px',
		width: width + 'px',
		height: '12.5vw',
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
    'isShop':true,  //是否有商城
    isShowAlert:false,
    alertType:1,
    alertText:'',
    userType:2,  //1：会长  2：副会长  3:成员
    phone:'',
    phoneText:'手机认证', //手机认证  修改手机号码
    isPhone:false,
    isShowBindPhone:false,
    sPhone:'',
    sAuthcode:'',
    authcodeType:1,
    authcodeText:'发送验证码',
    authcodeTime:10,
    inputPhone:'',
    inputAuthcode:'',
    memberCount:10232,
    members:[],
    searchText:'',
    page:1,
    sumPage:2,
    bScroll:null,
	canLoadMore:true,
    selectAccount:'',
};

appData.members = [];

function searchMember() {
    appData.members = [];
    appData.page = 1;
    httpModule.searchGroupMember();
};

appData.gameItems = [];

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
	hideMessage: viewMethods.hideMessage,
	selectCard: viewMethods.selectCard,
    showRedpackageRecord:viewMethods.clickRedpackageRecord,
    showSendRedpackage:viewMethods.clickSendRedPackage,
    startDateChange: viewMethods.changeStartDate,
    endDateChange: viewMethods.changeEndDate,
    closeAlert:viewMethods.clickCloseAlert,
    clickPhone:function () {
        //viewMethods.clickShowAlert(7,'没有绑定手机号');
        appData.isShowBindPhone = true;
    },
    hideBindPhone: function () {
        appData.isShowBindPhone = false;
    },
    clickEditPhone:function () {
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
        
        viewMethods.clickShowAlert(7,'绑定成功!');
        appData.isShowBindPhone = false;
    },
    getAuthcode:function () {
        if (appData.authcodeType != 1) {
			return;
		}
        
		authcodeTimer();

		appData.authcodeType = 2;
    },
    clickSearch:function () {
        searchMember();
    },
    clickDealMember:function (item, type) {
        appData.selectAccount = item;
        httpModule.dealMember(item, type);
    }
};


function checkPhone(phone) {
    if (!(/^1(3|4|5|7|8)\d{9}$/.test(phone))) {
        return false;
    } else {
        return true;
    }
}

function checkAuthcode(code) {
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
        appData.authcodeTime = 10;
        appData.authcodeType = 1;
        return;
    }

    appData.authcodeTime = appData.authcodeTime - 1;
    appData.authcodeText = appData.authcodeTime + 's';

    setTimeout(function () {
        authcodeTimer();
    }, 1000);
};

//Vue生命周期
var vueLife = {
	vmCreated: function () {
		logMessage('vmCreated')
        $("#loading").hide();
		$(".main").show();
        
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

var scrollDisable = false;

//原生写法
var memberDiv = document.querySelector('#memberDiv');
// memberDiv.addEventListener('scroll', function () {
//     var scrollTop = memberDiv.scrollTop;
//     if (scrollTop + memberDiv.offsetHeight >= memberDiv.scrollHeight) {
//         if (appData.page < appData.sumPage) {
//             appData.page = appData.page + 1;
//             httpModule.searchGroupMember();
//         }
//     }
// });

function loadMoreData() {
    if (appData.page < appData.sumPage) {
        appData.page = appData.page + 1;
        httpModule.searchGroupMember();
        $('#moretext').show();
        $('#moretext').text('加载中...');
    } else {
        $('#moretext').hide();
        $('#moretext').text('上拉加载更多');
    }
};

function refreshBScroll() {
	Vue.nextTick(function () {
		if (!appData.bScroll) {
			appData.bScroll = new BScroll(document.getElementById('memberDiv'), {
				startX: 0,
				startY: 0,
				scrollY: true,
				scrollX: false,
				click: true,
				bounceTime: 500,
			});

			appData.bScroll.on('touchend', function (position) {
				if (position.y < this.maxScrollY - 30 && appData.canLoadMore) {
					appData.canLoadMore = false;
					loadMoreData();
				}
			});
		} else {
			appData.bScroll.refresh();
		}
	});
};


httpModule.searchGroupMember();

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
