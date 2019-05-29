var app=angular.module('app',[]);

app.directive('ngInput', [function () {
    return {
        restrict: 'A',
        require: '?ngModel',
        link: function(scope, element, attrs) {
            element.on('input',oninput);
            scope.$on('$destroy',function(){//销毁的时候取消事件监听
                element.off('input',oninput);
            });
            function oninput(event){
                scope.$evalAsync(attrs['ngInput'],{$event:event,$value:this.value});
            }
        }
    }
}]).directive("ngTouchstart", function () {
    return {
        controller: ["$scope", "$element", function ($scope, $element) {

            $element.bind("touchstart", onTouchStart);
            function onTouchStart(event) {
                var method = $element.attr("ng-touchstart");
                $scope.$event = event;
                $scope.$apply(method);
            }

        }]
    }
})
.directive("ngTouchmove", function () {
    return {
        controller: ["$scope", "$element", function ($scope, $element) {
            $element.bind("touchstart", onTouchStart);
            function onTouchStart(event) {
                event.preventDefault();
                $element.bind("touchmove", onTouchMove);
                $element.bind("touchend", onTouchEnd);
            }
            function onTouchMove(event) {
                var method = $element.attr("ng-touchmove");
                $scope.$event = event;
                $scope.$apply(method);
            }
            function onTouchEnd(event) {
                event.preventDefault();
                $element.unbind("touchmove", onTouchMove);
                $element.unbind("touchend", onTouchEnd);
            }

        }]
    }
})
.directive("ngTouchend", function () {
    return {
        controller: ["$scope", "$element", function ($scope, $element) {
            $element.bind("touchend", onTouchEnd);
            function onTouchEnd(event) {
                var method = $element.attr("ng-touchend");
                $scope.$event = event;
                $scope.$apply(method);
            }

        }]
    }
})
.directive("ngTap", function () {
    return {
        controller: ["$scope", "$element", function ($scope, $element) {
            var moved = false;
            $element.bind("touchstart", onTouchStart);
            function onTouchStart(event) {
                $element.bind("touchmove", onTouchMove);
                $element.bind("touchend", onTouchEnd);
            }
            function onTouchMove(event) {
                moved = true;
            }
            function onTouchEnd(event) {
                $element.unbind("touchmove", onTouchMove);
                $element.unbind("touchend", onTouchEnd);
                if (!moved) {
                    var method = $element.attr("ng-tap");
                    $scope.$apply(method);
                }
            }
        }]
    }
});;

app.controller("myCtrl", function($scope,$http,$interval) {
    FastClick.attach(document.body);
    $scope.width=window.innerWidth;
    $scope.userInfo= userData;
    $scope.currentMenu = 'list';
    $scope.menuDatas=[
        {url:currentUrl+'f/ym',img:'game.png',name:'游戏',end: false},
        {url:'',img:'friend.png',name:'好友',end: false},
        {url:currentUrl+'f/box',img:'box.png',name:'包厢',end: false},
        {url:currentUrl+'f/yh',img:'user.png',name:'个人',end: true}
    ];
    $scope.settingCheck = false;
    // $scope.socket= socketData;
    $scope.dealerNum = dealerNum;
    // $scope.selectArr = [];
    // $scope.defaultScores = [];
    // $scope.defaultTime5To10 = [];
    // $scope.defaultTime5To20 = [];
    $scope.isShowBindPhone = false;
    $scope.sPhone='';
    $scope.sAuthcode='';
    $scope.authcodeType=1;
    $scope.authcodeTime=59;
    $scope.authcodeText='发送验证码';
    $scope.userID = '';
    $scope.userAliases = '';
    $scope.memberID='';
    $scope.curUrl='';
    $scope.userListData={};
    $scope.blacklistData={};
    $scope.NewsListData={}
    $scope.timeOutEvent = 0;
    $scope.tocuhTimes = 0;
    $scope.has_fri_req = has_fri_req;
    $scope.page = 1;
    $scope.pageBlack = 1;
    $scope.pagenews = 1;
    $scope.listMore='hide';
    $scope.blacklistMore='hide';
    $scope.isNews = false;
    // $scope.page = 1;
    
    

    $scope.getToggle = function(){
        $http({
            method:'get', //get请求方式
            url: currentUrl + 'manage/getManageSwitch'   //请求地址
        }).then(function(response){
            var data = response.data;
            if(data.result == 0){
                $scope.settingCheck = (data.data.is_on == '1')?true:false; 
            }
        },function(response){
            //失败时执行 
            console.log(response);
        });
    } 
    $scope.getUserList = function(){
        $http({
            method:'post', //post请求方式
            url: currentUrl + 'manage/searchGroupMember',   //请求地址
            data: {page:$scope.page,nickname:''}
        }).then(function(response){
            var data = response.data;
            if(data.result == 0){
                for(var k in data.data){
                    $scope.userListData[k] = data.data[k];
                }
                if($scope.page == data.sum_page){ 
                    $scope.listMore='none';
                    setTimeout(function(){
                        $scope.listMore='hide';
                    },3000)
                }else{
                    $scope.page = data.page + 1;
                    $scope.listMore='more';
                    
                }
            }   
        },function(response){
            //失败时执行 
            console.log(response);
        });
    }
    $scope.getBlacklis = function(){
        $http({
            method:'post', //post请求方式
            url: currentUrl + 'manage/getBlacklist',   //请求地址
            data: {page:$scope.pageBlack}
        }).then(function(response){
            var data = response.data;
            if(data.result == 0){
                console.log($scope.blacklistData);
                for(var k in data.data){
                    $scope.blacklistData[k] = data.data[k];
                }
                console.log($scope.blacklistData);
                if($scope.pageBlack == data.sum_page){ 
                    $scope.blacklistMore='none';
                    
                    if($scope.blacklistData && JSON.stringify($scope.blacklistData) != "{}"){
                        setTimeout(function(){
                            $scope.blacklistMore='hide';
                            $scope.$apply();
                        },3000)
                    }
                }else{
                    $scope.pageBlack = data.pageBlack + 1;
                    $scope.blacklistMore='more';
                    
                }
            }   
        },function(response){
            //失败时执行 
            console.log(response);
        });
    }
    $scope.getNewList = function() {
        $http({
            method:'post', //post请求方式
            url: currentUrl + 'manage/getApplylist',   //请求地址
            data: {page:$scope.pagenews}
        }).then(function(response){
            var data = response.data;
            if(data.result == 0){
                $scope.NewsListData = data.data;
                if(!data.sum_count) $scope.has_fri_req = 0;
                if($scope.pagenews == data.sum_page){ 
                    $scope.listMore='none';
                    setTimeout(function(){
                        $scope.listMore='hide';
                    },3000)
                }else{
                    $scope.pagenews = data.page + 1;
                    $scope.listMore='more';
                    
                }
            }   
        },function(response){
            //失败时执行 
            console.log(response);
        });
    }
    $scope.getMoreList = function(){
        $scope.listMore='loading';
        $scope.getUserList();
    }
    $scope.getMoreBlacklist = function(){
        $scope.blacklistMore='loading';
        $scope.getBlacklis();
    }

    $scope.phoneChangeValue=function () {
        var result = checkPhone($scope.sPhone);
        if (result) {
            $('#authcode').css('background-color','rgb(64,112,251)');
        } else {
            $('#authcode').css('background-color','lightgray');
        }
    };
    $scope.getAuthcode=function () {
        if ($scope.authcodeType != 1) {
            return;
        }
        var color = $('#authcode').css('background-color');
        if (color != 'rgb(64, 112, 251)') {
            return;
        }
        var validPhone = checkPhone($scope.sPhone);
        if (validPhone == false) {
            $scope.showAlert(1,'手机号码有误，请重填');
            return;
        }
        $scope.DoGetAuthcode($scope.sPhone);
    };
    $scope.DoGetAuthcode= function (phone) {
        $http({
            method:'POST',
            url:'/account/getMobileSms',
            data: {
                'phone': phone,
                'dealer_num': dealerNum
            }
        }).then(function (res) {
            if (res.data.result == 0) {
                var timerHandler = $interval(function () {
                    if ($scope.authcodeTime<=0) {
                        $interval.cancel(timerHandler);
                        $scope.authcodeTime=59;
                        $scope.authcodeText='获取验证码';
                        $scope.authcodeType=1;
                    }else{
                        $scope.authcodeText=$scope.authcodeTime+'s';
                        $scope.authcodeTime--;
                    }
                }, 1000);
                $scope.authcodeType = 2;
            } else {
                $scope.showAlert(1,res.data.result_message);
            }
        }, function() {
            $scope.showAlert(1,'获取验证码失败');
        });
    };
    $scope.bindPhone=function () {
        var validPhone = checkPhone($scope.sPhone);
        var validAuthcode = checkAuthcode($scope.sAuthcode);
        if (validPhone == false) {
            $scope.showAlert(1,'手机号码有误，请重填');
            return;
        }
        if (validAuthcode == false) {
            $scope.showAlert(1,'验证码有误，请重填');
            return;
        }
        $scope.DoBindPhone($scope.sPhone,$scope.sAuthcode);
    };
    $scope.DoBindPhone= function (phone, code) {
        $http({
            method:'POST',
            url:'/account/checkSmsCode',
            data:{"phone":phone, "code":code, "dealer_num":$scope.dealerNum}
        }).then(function(response) {
            var bodyData = response.data;
            if (bodyData.result == 0) {
                $scope.isShowBindPhone = false;
                $scope.isPhone = true;
                $scope.isAuthPhone = 0;
                $scope.phone = $scope.sPhone;

                if (bodyData.data.account_id != userData.id) {
                    $scope.showAlert(1,bodyData.result_message);
                } else {
                    $scope.showAlert(1,bodyData.result_message);
                }

                $scope.sPhone = '';
                $scope.sAuthcode = '';

            } else {
                $scope.showAlert(1,bodyData.result_message);
            }

        }, function() {
            $scope.authcodeTime = 59;
            $scope.showAlert(1,"绑定失败");
        });
    };
    $scope.menuChoose = function(type){
        $scope.currentMenu = type;
        if(type == 'invite') window.location.href = baseUrl + "manage/invite?code=" + userData.userCode;
        if(type == 'apply') $scope.showAlertOperate(9,'','添加好友');
        if(type == 'list'){
            $scope.page=1;
            $scope.userListData={};
            $scope.getUserList();
        } 
        if(type == 'blacklist'){
            $scope.pageBlack=1;
            $scope.blacklistData={};
            $scope.getBlacklis();
        }
        if(type == 'news'){
            $scope.getNewList();
            $scope.showAlertOperate(8,'','申请列表');
        } 
    }
    $scope.toggle = function(){
        $http({
            method:'post', //post请求方式
            url: currentUrl + 'manage/setManageSwitch',   //请求地址
            cache:true,
            data:{is_on:$scope.settingCheck?0:1}
        }).then(function(response){
            var data = response.data;
            if(data.result == 0){
                $scope.getToggle();
            }
        },function(response){
            //失败时执行 
            console.log(response);
        });
    }
    $scope.addFriend = function(){
        if($scope.$$childTail && $scope.$$childTail.userID){
            var userID = $scope.$$childTail.userID;
            var reg = /^[1-9][0-9]{3,31}/;
            if(reg.test(userID)){
                $http({
                    method:'post', //post请求方式
                    url: currentUrl + 'manage/joinGroup',   //请求地址
                    data:{user_code: $scope.$$childTail.userID}
                }).then(function(response){
                    var data = response.data;
                    if(data.result == 0){
                        $scope.showAlert(7,data.result_message,'温馨提示'); 
                    }else {
                        $scope.showAlert(6,data.result_message,'温馨提示'); 
                    }
                    
                },function(response){
                    console.log(response);
                });
            }else{
                $scope.showAlert(6,'请输入正确的好友游戏ID','温馨提示');
            }
        }else {
            $scope.showAlert(6,'请输入好友游戏ID','温馨提示');
        }
        
    }
    $scope.newsConsent = function($event){
        $scope.memberID=$event.target.id;
        $scope.curUrl='setFriendList';
        $scope.isNews = true;
        $scope.showAlert(11,'确认添加对方为好友？','同意添加');
    }
    $scope.recoverList = function(userId){
        $scope.memberID=userId;
        $scope.curUrl='setFriendList';
        $scope.isNews = false;
        $scope.showAlert(11,'确认恢复对方为好友？','恢复好友');
    }
    $scope.newsReject = function($event){
        $scope.memberID=$event.target.id;
        $scope.curUrl='deleteMember';
        $scope.isNews = true;
        $scope.showAlert(11,'确认拒绝该好友申请？','拒绝好友');
    }
    $scope.userRemark = function(userId){
        $scope.memberID=userId;
        $scope.curUrl='SetAliases';
        $scope.showAlertOperate(12,'','备注');
    }
    $scope.joinBlacklist = function(userId){
        console.log('我只想把你关进小黑屋');
        $scope.memberID=userId;
        $scope.curUrl='setBlacklist';
        $scope.showAlert(11,'确认将该好友移到小黑屋吗？','移到小黑屋');
    }
    $scope.deleteUser = function(userId){
        console.log('我只想删了你');
        $scope.memberID=userId;
        $scope.curUrl='deleteMember';
        $scope.isNews = false;
        $scope.showAlert(11,'确认将该好友删除吗？','删除好友');
    }
    $scope.doOperate = function(){
        var obj = {
            member_id: $scope.memberID
        }
        if($scope.curUrl == 'SetAliases') obj.aliases = $scope.$$childTail.userAliases;
        $http({
            method:'post', //get请求方式
            url: currentUrl + 'manage/' + $scope.curUrl,   //请求地址
            cache:true,
            data:obj
        }).then(function(response){
            var data = response.data;
            if(data.result == 0){
                if($scope.currentMenu == "blacklist") {
                    $scope.pageBlack=1;
                    $scope.blacklistData={};
                    $scope.getBlacklis();
                }else{
                    $scope.page=1;
                    $scope.userListData={};
                    $scope.getUserList();
                }
                if($scope.isNews){
                    $scope.showAlert(6,data.result_message,'温馨提示');
                    $scope.getNewList();
                }else $scope.showAlert(7,data.result_message,'温馨提示');
            }else{
                $scope.showAlert(6,data.result_message,'温馨提示'); 
            }
        },function(response){
            //失败时执行 
            console.log(response);
        });
    }
    $scope.connectSocket=function(){
        $scope.ws = new WebSocket(userData.socket_news);
        $scope.ws.onopen = function(){
            $scope.is_operation=true;
            var tiao=setInterval(function(){
                socketStatus=socketStatus+1;
                $scope.ws.send("@");
                if(socketStatus>3||socketStatus>3){
                    window.location.reload();
                }
            },4000);
            $scope.ws.send(JSON.stringify({
                "operation":"InitConnect",
                "account_id":userData.id,
                "session":session,
                "data":{
                    "data_key":Date.parse(new Date())+randomString(5),
                    "group": "friend",
                }
            }))
        }
        $scope.ws.onmessage = function(evt){
            if(evt.data=="@"){
                socketStatus=0;
                return 0;
            }
            var obj = eval('(' + evt.data + ')');
            if(obj.operation == 'JoinGroupNotify'){
                $scope.has_fri_req = 1;
                $scope.$apply();
            } 
            $scope.is_operation = false;
        }
        $scope.ws.onclose = function(evt){
            if($scope.is_operation){
                $scope.connectSocket();
            }
            else return 0;
        }
        $scope.ws.onerror = function(evt){console.log("WebSocketError!");};
    }
    

    var socketStatus=0;
    $(".main").show();
    $("#loading").hide();
    $scope.activity=new Array();
    $scope.isShowAlert=false;
    $scope.alertType=0;
    $scope.alertText="";
    $scope.alertTitle="";
    $scope.showAlert=function(type,text,title){
        //$(".alertText").css("top","90px")
        $scope.alertType=type;
        $scope.alertText=text;
        $scope.alertTitle=title;
        $scope.isShowAlert=true;

        setTimeout(function() {
            $scope.$apply();
        }, 0);

        setTimeout(function(){
            // var wHeight = window.innerHeight;
            // var alertHeight = $(".alertText").height();
            // var textHeight = $(".alertText").height();

            // if (alertHeight < wHeight * 0.15) {
            //     alertHeight = wHeight * 0.15;
            // }

            // if (alertHeight > wHeight * 0.8) {
            //     alertHeight = wHeight * 0.8;
            // }

            // var mainHeight = alertHeight + wHeight * (0.022 + 0.034) * 2 + wHeight * 0.022 + wHeight * 0.056;
            // if (type == 8) {
            //     mainHeight = mainHeight - wHeight * 0.022 - wHeight * 0.056
            // }

            // var blackHeight = alertHeight + wHeight * 0.034 * 2;
            $scope.$apply();
        },0)
    }
    $scope.closeAlert=function(){
        if($scope.alertType==1){
            $scope.isShowAlert=false;
            // $scope.showShop();
            if(!$scope.is_connect){
                $scope.is_connect=true;
            }
        }
        else{
            $scope.isShowAlert=false;
        }
        if($scope.currentMenu == 'apply' || $scope.currentMenu == 'news') $scope.currentMenu = 'list';
        
        // if($scope.$$childTail && $scope.$$childTail.userID)$scope.$$childTail.userID = '';
    }
    $scope.closeAlertAll = function(){
        $scope.isShowAlert=false;
        $scope.isShowAlertOperate=false;
        if($scope.currentMenu == 'apply' || $scope.currentMenu == 'news') $scope.currentMenu = 'list';
        if($scope.$$childTail && $scope.$$childTail.userID)$scope.$$childTail.userID = '';
    }
    $scope.isShowAlertOperate=false;
    $scope.alertTypeOperate=0;
    $scope.alertTextOperate="";
    $scope.alertTitleOperate="";
    $scope.showAlertOperate=function(type,text,title){
        //$(".alertText").css("top","90px")
        $scope.alertTypeOperate=type;
        $scope.alertTextOperate=text;
        $scope.alertTitleOperate=title;
        $scope.isShowAlertOperate=true;

        setTimeout(function() {
            $scope.$apply();
        }, 0);

        setTimeout(function(){
            // var wHeight = window.innerHeight;
            // var alertHeight = $(".alertText").height();
            // var textHeight = $(".alertText").height();

            // if (alertHeight < wHeight * 0.15) {
            //     alertHeight = wHeight * 0.15;
            // }

            // if (alertHeight > wHeight * 0.8) {
            //     alertHeight = wHeight * 0.8;
            // }

            // var mainHeight = alertHeight + wHeight * (0.022 + 0.034) * 2 + wHeight * 0.022 + wHeight * 0.056;
            // if (type == 8) {
            //     mainHeight = mainHeight - wHeight * 0.022 - wHeight * 0.056
            // }

            // var blackHeight = alertHeight + wHeight * 0.034 * 2;
            $scope.$apply();
        },0)
    }
    $scope.closeAlertOperate=function(){
        if($scope.alertType==1){
            $scope.isShowAlertOperate=false;
            // $scope.showShop();
            if(!$scope.is_connect){
                $scope.is_connect=true;
            }
        }
        else{
            $scope.isShowAlertOperate=false;
        }
        if($scope.currentMenu == 'apply' || $scope.currentMenu == 'news') $scope.currentMenu = 'list';
        
        if($scope.$$childTail && $scope.$$childTail.userID)$scope.$$childTail.userID = '';
        // $scope.$apply();
    }
    // $scope.closeCurrent = function(){
    //     $scope.showAlert(9,'','添加好友');
    // }
    $scope.touchStart = function($event){
        $scope.tocuhTimes = 0
        $scope.timeOutEvent = setTimeout(function(){
            //此处为长按事件-----在此显示遮罩层及删除按钮
            $scope.tocuhTimes += 1;
            var $index = $($event.target);
            if(!$index.attr("data-id")){
                $index = $($event.target).closest('.user-li');
            }
            $(".user-operation").hide();
            $('.user-li').removeClass("user-li-selected");
            if($index.find(".user-operation").length) $index.addClass("user-li-selected").find(".user-operation").show();
            },500);
    }
    $scope.touchMove = function($event){
        clearTimeout($scope.timeOutEvent);
        $scope.timeOutEvent = 0;
        $event.preventDefault();
    }
    $scope.touchEnd = function($event){
        clearTimeout($scope.timeOutEvent);
        var $index = $($event.target);
        if($scope.tocuhTimes==0){//点击
            //此处为点击事件----在此处添加跳转详情页
            setTimeout(()=>{
                $('.user-li').removeClass("user-li-selected");
                $(".user-operation").hide();
                return false;
            },50)
        }else{
            return false;
        }
    }

    setTimeout(function() {
        $scope.$apply();
    }, 100);

    $scope.reloadView = function () {
        window.location.href=window.location.href+"&id="+10000*Math.random();
    };
    $scope.getToggle();
    $scope.getUserList();
    $scope.connectSocket();
});
//手机绑定******
function checkPhone(phone) {
    if (!(/^1\d{10}$/.test(phone))) {
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
function showCloseImg(type){
    console.log('11111111111111111111111111')
}

//******手机绑定
function randomString(len) {
    len = len || 32;
    var $chars = 'ABCDEFGHJKMNPQRSTWXYZabcdefhijkmnprstwxyz2345678';    /****默认去掉了容易混淆的字符oOLl,9gq,Vv,Uu,I1****/
    var maxPos = $chars.length;
    var pwd = '';
    for (i = 0; i < len; i++) {
        pwd += $chars.charAt(Math.floor(Math.random() * maxPos));
    }
    return pwd;
}

// function storeSetting(createInfo) {
//     localStorage.createInfo = JSON.stringify(createInfo);
// }

// 将obj里面属性值赋值给createInfo, 不直接赋值给createInfo防止添加新配置的时候，创建不了房间
// function cloneObj (createInfo, obj) {
//     for (var key in createInfo) {
//         if (createInfo[key] instanceof Object && obj[key] instanceof Object) {
//             cloneObj(createInfo[key], obj[key])
//         } else {
//             createInfo[key] = obj[key] !== undefined? obj[key]: createInfo[key];
//         }
//     }
// }