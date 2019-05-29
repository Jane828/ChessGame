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
}]);

app.controller("myCtrl", function($scope,$http,$interval) {
    FastClick.attach(document.body);
    $scope.width=window.innerWidth;
    $scope.userInfo= userData;
    $scope.socket= socketData;
    $scope.dealerNum = dealerNum;

    $scope.isShowBindPhone = false;
    $scope.sPhone='';
    $scope.sAuthcode='';
    $scope.authcodeType=1;
    $scope.authcodeTime=59;
    $scope.authcodeText='发送验证码';
    if (userData.card>=30&&userData.phone.length<1) {
        $scope.isShowBindPhone=true;
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
    $scope.homeImgRight=10.5;
    $scope.lenCard=$scope.userInfo.card.length;
    $scope.homeImgRight+=10*$scope.lenCard;

    var socketStatus=0;
    $(".main").show();
    $("#loading").hide();
    $scope.activity=new Array();
    $scope.isShowAlert=false;
    $scope.alertType=0;
    $scope.alertText="";
    $scope.showAlert=function(type,text){
        $(".alertText").css("top","90px")
        $scope.alertType=type;
        $scope.alertText=text;
        $scope.isShowAlert=true;

        setTimeout(function() {
            $scope.$apply();
        }, 0);

        setTimeout(function(){
            var wHeight = window.innerHeight;
            var alertHeight = $(".alertText").height();
            var textHeight = $(".alertText").height();

            if (alertHeight < wHeight * 0.15) {
                alertHeight = wHeight * 0.15;
            }

            if (alertHeight > wHeight * 0.8) {
                alertHeight = wHeight * 0.8;
            }

            var mainHeight = alertHeight + wHeight * (0.022 + 0.034) * 2 + wHeight * 0.022 + wHeight * 0.056;
            if (type == 8) {
                mainHeight = mainHeight - wHeight * 0.022 - wHeight * 0.056
            }

            var blackHeight = alertHeight + wHeight * 0.034 * 2;
            var alertTop = wHeight * 0.022 + (blackHeight - textHeight) / 2;

            $(".alert .mainPart").css('height', mainHeight + 'px');
            $(".alert .mainPart").css('margin-top', '-' + mainHeight / 2 + 'px');
            $(".alert .mainPart .backImg .blackImg").css('height', blackHeight + 'px');
            $(".alert .mainPart .alertText").css('top', alertTop + 'px');

            $scope.$apply();
        },0)
    }
    $scope.closeAlert=function(){
        if($scope.alertType==1){
            $scope.isShowAlert=false;
            $scope.showShop();
            if(!$scope.is_connect){
                $scope.is_connect=true;
            }
        }
        else{
            $scope.isShowAlert=false;
        }
    }

    setTimeout(function() {
        $scope.$apply();
    }, 100);

    $scope.reloadView = function () {
        window.location.href=window.location.href+"&id="+10000*Math.random();
    };

    $scope.is_operation=false;
    $scope.waiting=function(){
        $scope.is_operation=true;
        setTimeout(function(){
            if($scope.is_operation){
                $scope.is_operation=false;
                $scope.showAlert(6,"创建房间失败，请重新创建")
            }
        },15000)
    };

    $scope.socket_url="";
    $scope.socket_type="";
    $scope.connectSocket=function(socket,type){
        $scope.socket_url=socket;
        $scope.socket_type=type;
        $scope.ws = new WebSocket(socket);
        $scope.ws.onopen = function(){
            $scope.is_operation=true;
            var tiao=setInterval(function(){
                socketStatus=socketStatus+1;
                $scope.ws.send("@");
                if(socketStatus>3||socketStatus>3){
                    window.location.reload();
                }
            },4000);
            console.log("socketOpen");
            if(type==1){
                $scope.ws.send(JSON.stringify({
                    "operation":"CreateRoom",
                    "account_id":accountId,
                    "session":session,
                    "data":{
                        "data_key":Date.parse(new Date())+randomString(5),
                        'chip_type': $scope.createInfo.flower.chip_type,
                        'ticket_count':$scope.createInfo.flower.ticket_count,
                        'disable_pk_100':$scope.createInfo.flower.pkvalue1,
                        'disable_pk_men':$scope.createInfo.flower.pkvalue2,
                        'upper_limit':$scope.createInfo.flower.upper_limit,
                        'seen':$scope.createInfo.flower.seen,
                        'game_type': type
                    }
                }));
            }
            else if(type==2){
                $scope.ws.send(JSON.stringify({
                    "operation":"CreateRoom",
                    "account_id":accountId,
                    "session":session,
                    "data":{
                        "data_key":Date.parse(new Date())+randomString(5),
                        "ticket_count":$scope.createInfo.landlord.ticket_count,
                        "base_score":$scope.createInfo.landlord.base_score,
                        "ask_mode":$scope.createInfo.landlord.ask_mode,
                    }
                }));
            }
            else if(type==3){
                console.log($scope.createInfo.bull);
                $scope.ws.send(JSON.stringify({
                    "operation":"CreateRoom",
                    "account_id":accountId,
                    "session":session,
                    "data":{
                        "data_key":Date.parse(new Date())+randomString(5),
                        "ticket_type":$scope.createInfo.bull.ticket_type,
                        "score_type":$scope.createInfo.bull.score_type,
                        "rule_type":$scope.createInfo.bull.rule_type,
                        "is_cardfour":$scope.createInfo.bull.is_cardfour,
                        "is_cardfive":$scope.createInfo.bull.is_cardfive,
                        "is_cardbomb":$scope.createInfo.bull.is_cardbomb,
                        "is_cardtiny":$scope.createInfo.bull.is_cardtiny,
                        "banker_mode":$scope.createInfo.bull.banker_mode,
                        "banker_score_type":$scope.createInfo.bull.banker_score,
                    }
                }));
            }
            else if(type==4){
                $scope.ws.send(JSON.stringify({
                    "operation":"CreateRoom",
                    "account_id":accountId,
                    "session":session,
                    "data":{
                        "data_key":Date.parse(new Date())+randomString(5),
                        "joker":$scope.createInfo.majiang.joker,
                        "horse_count":$scope.createInfo.majiang.horse_count,
                        "qianggang":$scope.createInfo.majiang.qianggang,
                        "chengbao":$scope.createInfo.majiang.chengbao,
                        "ticket_count":$scope.createInfo.majiang.ticket_count,
                    }
                }));
            }
            else if(type==5){
                console.log($scope.createInfo.bull9);
                $scope.ws.send(JSON.stringify({
                    "operation":"CreateRoom",
                    "account_id":accountId,
                    "session":session,
                    "data":{
                        "data_key":Date.parse(new Date())+randomString(5),
                        "ticket_type":$scope.createInfo.bull9.ticket_type,
                        "score_type":$scope.createInfo.bull9.score_type,
                        "rule_type":$scope.createInfo.bull9.rule_type,
                        "is_cardfour":$scope.createInfo.bull9.is_cardfour,
                        "is_cardfive":$scope.createInfo.bull9.is_cardfive,
                        "is_cardbomb":$scope.createInfo.bull9.is_cardbomb,
                        "is_cardtiny":$scope.createInfo.bull9.is_cardtiny,
                        "banker_mode":$scope.createInfo.bull9.banker_mode,
                        "banker_score_type":$scope.createInfo.bull9.banker_score,
                    }
                }));
            }
            else if(type==6){
                console.log($scope.createInfo.bull8x);
                $scope.ws.send(JSON.stringify({
                    "operation":"CreateRoom",
                    "account_id":accountId,
                    "session":session,
                    "data":{
                        "data_key":Date.parse(new Date())+randomString(5),
                        "ticket_type":$scope.createInfo.bull8x.ticket_type,
                        "score_type":$scope.createInfo.bull8x.score_type,
                        "rule_type":$scope.createInfo.bull8x.rule_type,
                        "is_cardfive":$scope.createInfo.bull8x.is_cardfive,
                        "is_cardbomb":$scope.createInfo.bull8x.is_cardbomb,
                        "is_cardtiny":$scope.createInfo.bull8x.is_cardtiny,
                        "is_cardfour":$scope.createInfo.bull8x.is_cardfour,
                        "banker_mode":$scope.createInfo.bull8x.banker_mode,
                        "banker_score_type":$scope.createInfo.bull8x.banker_score,
                    }
                }));
            }
            else if(type==7){
                console.log($scope.createInfo.nbull8x);
                $scope.ws.send(JSON.stringify({
                    "operation":"CreateRoom",
                    "account_id":accountId,
                    "session":session,
                    "data":{
                        "data_key":Date.parse(new Date())+randomString(5),
                        "ticket_type":$scope.createInfo.nbull8x.ticket_type,
                        "score_type":$scope.createInfo.nbull8x.score_type,
                        "rule_type":$scope.createInfo.nbull8x.rule_type,
                        "is_cardfour":$scope.createInfo.nbull8x.is_cardfour,
                        "is_cardfive":$scope.createInfo.nbull8x.is_cardfive,
                        "is_cardbomb":$scope.createInfo.nbull8x.is_cardbomb,
                        "is_cardtiny":$scope.createInfo.nbull8x.is_cardtiny,
                        "banker_mode":$scope.createInfo.nbull8x.banker_mode,
                        "banker_score_type":$scope.createInfo.nbull8x.banker_score,
                    }
                }));
            }
            else if(type==8){
                console.log($scope.createInfo.tbull);
                $scope.ws.send(JSON.stringify({
                    "operation":"CreateRoom",
                    "account_id":accountId,
                    "session":session,
                    "data":{
                        "data_key":Date.parse(new Date())+randomString(5),
                        "ticket_type":$scope.createInfo.tbull.ticket_type,
                        "score_type":$scope.createInfo.tbull.score_type,
                        "rule_type":$scope.createInfo.tbull.rule_type,
                        "is_cardfour":$scope.createInfo.tbull.is_cardfour,
                        "is_cardfive":$scope.createInfo.tbull.is_cardfive,
                        "is_cardbomb":$scope.createInfo.tbull.is_cardbomb,
                        "is_cardtiny":$scope.createInfo.tbull.is_cardtiny,
                        "banker_mode":$scope.createInfo.tbull.banker_mode,
                        "banker_score_type":$scope.createInfo.tbull.banker_score,
                    }
                }));
            }

            else if(type==9){
                console.log($scope.createInfo.tbull8x);
                $scope.ws.send(JSON.stringify({
                    "operation":"CreateRoom",
                    "account_id":accountId,
                    "session":session,
                    "data":{
                        "data_key":Date.parse(new Date())+randomString(5),
                        "ticket_type":$scope.createInfo.tbull8x.ticket_type,
                        "score_type":$scope.createInfo.tbull8x.score_type,
                        "rule_type":$scope.createInfo.tbull8x.rule_type,
                        "is_cardfour":$scope.createInfo.tbull8x.is_cardfour,
                        "is_cardfive":$scope.createInfo.tbull8x.is_cardfive,
                        "is_cardbomb":$scope.createInfo.tbull8x.is_cardbomb,
                        "is_cardtiny":$scope.createInfo.tbull8x.is_cardtiny,
                        "banker_mode":$scope.createInfo.tbull8x.banker_mode,
                        "banker_score_type":$scope.createInfo.tbull8x.banker_score,
                    }
                }));
            }
            else if(type==110){
                $scope.ws.send(JSON.stringify({
                    "operation":"CreateRoom",
                    "account_id":accountId,
                    "session":session,
                    "data":{
                        "data_key":Date.parse(new Date())+randomString(5),
                        'chip_type': $scope.createInfo.tflower.chip_type,
                        'ticket_count':$scope.createInfo.tflower.ticket_count,
                        'disable_pk_100':$scope.createInfo.tflower.pkvalue1,
                        'disable_pk_men':$scope.createInfo.tflower.pkvalue2,
                        'upper_limit':$scope.createInfo.tflower.upper_limit,
                        'seen':$scope.createInfo.tflower.seen,
                        'game_type':type
                    }
                }));
            }
            else if(type==111){
                $scope.ws.send(JSON.stringify({
                    "operation":"CreateRoom",
                    "account_id":accountId,
                    "session":session,
                    "data":{
                        "data_key":Date.parse(new Date())+randomString(5),
                        'chip_type': $scope.createInfo.bflower.chip_type,
                        'ticket_count':$scope.createInfo.bflower.ticket_count,
                        'disable_pk_100':$scope.createInfo.bflower.pkvalue1,
                        'disable_pk_men':$scope.createInfo.bflower.pkvalue2,
                        'upper_limit':$scope.createInfo.bflower.upper_limit,
                        'seen':$scope.createInfo.bflower.seen,
                        'game_type':type
                    }
                }));
            }
            else if(type==36){
                $scope.ws.send(JSON.stringify({
                    "operation":"CreateRoom",
                    "account_id":accountId,
                    "session":session,
                    "data":{
                        "data_key":Date.parse(new Date())+randomString(5),
                        "banker_mode":$scope.createInfo.sangong.banker_mode,
                        "score_type":$scope.createInfo.sangong.score_type,
                        "is_joker":$scope.createInfo.sangong.is_joker,
                        "is_bj":$scope.createInfo.sangong.is_bj,
                        "ticket_type":$scope.createInfo.sangong.ticket_type
                    }
                }));
            }
            else if(type==37){
                $scope.ws.send(JSON.stringify({
                    "operation":"CreateRoom",
                    "account_id":accountId,
                    "session":session,
                    "data":{
                        "data_key":Date.parse(new Date())+randomString(5),
                        "banker_mode":$scope.createInfo.nsangong.banker_mode,
                        "score_type":$scope.createInfo.nsangong.score_type,
                        "is_joker":$scope.createInfo.nsangong.is_joker,
                        "is_bj":$scope.createInfo.nsangong.is_bj,
                        "ticket_type":$scope.createInfo.nsangong.ticket_type
                    }
                }));
            }
            else if(type==38){
                $scope.ws.send(JSON.stringify({
                    "operation":"CreateRoom",
                    "account_id":accountId,
                    "session":session,
                    "data":{
                        "data_key":Date.parse(new Date())+randomString(5),
                        "banker_mode":$scope.createInfo.tsangong.banker_mode,
                        "score_type":$scope.createInfo.tsangong.score_type,
                        "is_joker":$scope.createInfo.tsangong.is_joker,
                        "is_bj":$scope.createInfo.tsangong.is_bj,
                        "ticket_type":$scope.createInfo.tsangong.ticket_type
                    }
                }));
            }
            else if(type==71){
                console.log($scope.createInfo.lbull);
                $scope.ws.send(JSON.stringify({
                    "operation":"CreateRoom",
                    "account_id":accountId,
                    "session":session,
                    "data":{
                        "data_key":Date.parse(new Date())+randomString(5),
                        "ticket_type":$scope.createInfo.lbull.ticket_type,
                        "score_type":$scope.createInfo.lbull.score_type,
                        "rule_type":$scope.createInfo.lbull.rule_type,
                        "is_cardfour":$scope.createInfo.lbull.is_cardfour,
                        "is_cardfive":$scope.createInfo.lbull.is_cardfive,
                        "is_cardbomb":$scope.createInfo.lbull.is_cardbomb,
                        "is_cardtiny":$scope.createInfo.lbull.is_cardtiny,

                        "is_straight":$scope.createInfo.lbull.is_straight,
                        "is_flush":$scope.createInfo.lbull.is_flush,
                        "is_hulu":$scope.createInfo.lbull.is_hulu,

                        "banker_mode":$scope.createInfo.lbull.banker_mode,
                        "banker_score_type":$scope.createInfo.lbull.banker_score,
                    }
                }));
            }
        };
        $scope.ws.onmessage = function(evt){
            if(evt.data=="@"){
                socketStatus=0;
                return 0;
            }
            var obj = eval('(' + evt.data + ')');
            console.log(obj);
            if (obj.result==1){
                $scope.is_operation=false;
                $scope.showAlert(1,obj.result_message);
            } else if (obj.result == 0){
                if(type==1)
                    window.location.href = baseUrl + "f/yf?i="+obj.data.room_number+"_";
                else if(type==2)
                    window.location.href = baseUrl + "f/l?i="+obj.data.room_number+"_";
                else if(type==3)
                    window.location.href = baseUrl + "f/b?i="+obj.data.room_number+"_";
                else if(type==4){
                    window.location.href = baseUrl + "f/ma?i="+obj.data.room_number+"_";
                } else if(type==5){
                    window.location.href = baseUrl + "f/nb?i="+obj.data.room_number+"_";
                } else if(type==6){
                    window.location.href = baseUrl + "f/b8?i="+obj.data.room_number+"_";
                } else if(type==7){
                    window.location.href = baseUrl + "f/nb8?i="+obj.data.room_number+"_";
                } else if(type==8){
                    window.location.href = baseUrl + "f/tb?i="+obj.data.room_number+"_";
                } else if(type==9){
                    window.location.href = baseUrl + "f/tb8?i="+obj.data.room_number+"_";
                } else if(type==110){
                    window.location.href = baseUrl + "f/tf?i="+obj.data.room_number+"_";
                } else if(type==111){
                    window.location.href = baseUrl + "f/bf?i="+obj.data.room_number+"_";
                } else if(type==36){
                    window.location.href = baseUrl + "f/sg?i="+obj.data.room_number+"_";
                } else if(type==37){
                    window.location.href = baseUrl + "f/nsg?i="+obj.data.room_number+"_";
                } else if(type==38){
                    window.location.href = baseUrl + "f/tsg?i="+obj.data.room_number+"_";
                } else if(type==71){
                    window.location.href = baseUrl + "f/lb?i="+obj.data.room_number+"_";
                }

            }  else if (obj.result == -201){
                $scope.is_operation=false;
                $scope.showAlert(31,obj.result_message);
            }  else {
                $scope.is_operation=false;
                $scope.showAlert(6,obj.result_message);
            }
        };
        $scope.ws.onclose = function(evt){
            // errorAPI("connectFailed");
            if($scope.is_operation){
                $scope.connectSocket($scope.socket_url,$scope.socket_type);
            }
            else
                return 0;
            //	window.location.reload();
        }
        $scope.ws.onerror = function(evt){console.log("WebSocketError!");};
    }

    $scope.createInfo={
        "isShow":0,
        "flower":{
            'chip_type': 1,
            'ticket_count': 1,
            'pkvalue1': 0,
            'pkvalue2': 0,
            'upper_limit': 1000,
            'seen': 0
        },
        "bflower":{
            'chip_type': 1,
            'ticket_count': 1,
            'pkvalue1': 0,
            'pkvalue2': 0,
            'upper_limit': 1000,
            'seen': 0
        },
        "landlord":{
            "ticket_count":1,
            "base_score":1,
            "ask_mode":1,
        },
        "majiang":{
            "joker":0,
            "horse_count":0,
            "qianggang":0,
            "ticket_count":1,
            "chengbao":0,
        },
        //六人斗牛默认建房选项
        "bull":{
            "ticket_type":1,
            "score_type":1,
            "rule_type":2,   //规则 1: 牛牛x3牛九x2牛八x2      2: 牛牛x4牛九x3牛八x2牛七x2
            "is_cardfour":1, //牌型 四花牛(4倍)  1表示默认勾选
            "is_cardfive":1, //牌型 五花牛(5倍)  1表示默认勾选
            "is_cardbomb":1, //牌型 炸弹牛(6倍)
            "is_cardtiny":1, //牌型 五小牛(8倍)
            "banker_mode":2, //模式 2 明牌抢庄
            "banker_score":4,
            "banker1":"unselected",
            "banker2":"selected",
            "banker3":"unselected",
            "banker4":"unselected",
            "banker5":"unselected",
        },
        //九人斗牛 默认建房选项
        "bull9":{
            "ticket_type":1,
            "score_type":1,
            "rule_type":2,   //规则 1: 牛牛x3牛九x2牛八x2      2: 牛牛x4牛九x3牛八x2牛七x2
            "is_cardfour":1, //牌型 四花牛(4倍)  1表示默认勾选
            "is_cardfive":1, //牌型 五花牛(5倍)  1表示默认勾选
            "is_cardbomb":1, //牌型 炸弹牛(6倍)
            "is_cardtiny":1, //牌型 五小牛(8倍)
            "banker_mode":2, //模式 2 明牌抢庄
            "banker_score":4,
            "banker1":"unselected",
            "banker2":"selected",
            "banker3":"unselected",
            "banker4":"unselected",
            "banker5":"unselected",
        },
        "bull8x":{
            "ticket_type":1,
            "score_type":1,
            "rule_type":2,   //规则 1: 牛牛x3牛九x2牛八x2      2: 牛牛x4牛九x3牛八x2牛七x2
            "is_cardfour":1, //牌型 四花牛(4倍)  1表示默认勾选
            "is_cardfive":1, //牌型 五花牛(5倍)  1表示默认勾选
            "is_cardbomb":1, //牌型 炸弹牛(6倍)
            "is_cardtiny":1, //牌型 五小牛(8倍)
            "banker_mode":2, //模式 2 明牌抢庄
            "banker_score":4,
            "banker1":"unselected",
            "banker2":"selected",
            "banker3":"unselected",
            "banker4":"unselected",
            "banker5":"unselected",
        },
        "nbull8x":{
            "ticket_type":1,
            "score_type":1,
            "rule_type":2,   //规则 1: 牛牛x3牛九x2牛八x2      2: 牛牛x4牛九x3牛八x2牛七x2
            "is_cardfour":1, //牌型 四花牛(4倍)  1表示默认勾选
            "is_cardfive":1, //牌型 五花牛(5倍)  1表示默认勾选
            "is_cardbomb":1, //牌型 炸弹牛(6倍)
            "is_cardtiny":1, //牌型 五小牛(8倍)
            "banker_mode":2, //模式 2 明牌抢庄
            "banker_score":4,
            "banker1":"unselected",
            "banker2":"selected",
            "banker3":"unselected",
            "banker4":"unselected",
            "banker5":"unselected",
        },

        "tbull":{
            "ticket_type":1,
            "score_type":1,
            "rule_type":2,   //规则 1: 牛牛x3牛九x2牛八x2      2: 牛牛x4牛九x3牛八x2牛七x2
            "is_cardfour":1, //牌型 四花牛(4倍)  1表示默认勾选
            "is_cardfive":1, //牌型 五花牛(5倍)  1表示默认勾选
            "is_cardbomb":1, //牌型 炸弹牛(6倍)
            "is_cardtiny":1, //牌型 五小牛(8倍)
            "banker_mode":2, //模式 2 明牌抢庄
            "banker_score":4,
            "banker1":"unselected",
            "banker2":"selected",
            "banker3":"unselected",
            "banker4":"unselected",
            "banker5":"unselected",
        },
        "tbull8x":{
            "ticket_type":1,
            "score_type":1,
            "rule_type":2,   //规则 1: 牛牛x3牛九x2牛八x2      2: 牛牛x4牛九x3牛八x2牛七x2
            "is_cardfour":1, //牌型 四花牛(4倍)  1表示默认勾选
            "is_cardfive":1, //牌型 五花牛(5倍)  1表示默认勾选
            "is_cardbomb":1, //牌型 炸弹牛(6倍)
            "is_cardtiny":1, //牌型 五小牛(8倍)
            "banker_mode":2, //模式 2 明牌抢庄
            "banker_score":4,
            "banker1":"unselected",
            "banker2":"selected",
            "banker3":"unselected",
            "banker4":"unselected",
            "banker5":"unselected"
        },
        //十人炸金花
        "tflower":{
            "chip_type": 1,
            "ticket_count": 2, // 局数 2: 10局X2张房卡 4: 20局X4张房卡 默认2
            "pkvalue1": 0,
            "pkvalue2": 0,
            "upper_limit": 1000,
            "seen": 0
        },
        // 六人三公
        "sangong":{
            "ticket_type":1,
            "score_type":1,
            "is_joker":0,
            "is_bj":0,
            "banker_mode":1,
            "banker1":"selected",
            "banker2":"unselected"
        },
        // 九人三公
        "nsangong":{
            "ticket_type":1,
            "score_type":1,
            "is_joker":0,
            "is_bj":0,
            "banker_mode":1,
            "banker1":"selected",
            "banker2":"unselected"
        },
        // 12人三公
        "tsangong":{
            "ticket_type":1,
            "score_type":1,
            "is_joker":0,
            "is_bj":0,
            "banker_mode":1,
            "banker1":"selected",
            "banker2":"unselected"
        },
        "lbull":{
            "ticket_type":1,
            "score_type":1,
            "rule_type":2,   //规则 1: 牛牛x3牛九x2牛八x2      2: 牛牛x4牛九x3牛八x2牛七x2
            "is_cardfour":1, //牌型 四花牛(4倍)  1表示默认勾选
            "is_cardfive":1, //牌型 五花牛(5倍)  1表示默认勾选
            "is_cardbomb":1, //牌型 炸弹牛(6倍)
            "is_cardtiny":1, //牌型 五小牛(8倍)

            "is_straight":1, //牌型 顺子牛(5倍)  1表示默认勾选
            "is_flush":1, //牌型 同花牛(6倍)
            "is_hulu":1, //牌型 葫芦牛(7倍)

            "banker_mode":2, //模式 2 明牌抢庄
            "banker_score":4,
            "banker1":"unselected",
            "banker2":"selected",
            "banker3":"unselected",
            "banker4":"unselected",
            "banker5":"unselected"
        }
    };

    $scope.createInfo = getSetting($scope.createInfo);
    $scope.createInfo.flower.pkvalue2=0;
    $scope.createInfo.bflower.pkvalue2=0;
    $scope.createInfo.tflower.pkvalue2=0;

    $scope.selectChange=function(type,num){
        if($scope.createInfo.isShow==1){
            if(type==1){
                $scope.createInfo.flower.chip_type=num;
            }
            else if(type==2){

                if (num == 1) {
                    if ($scope.createInfo.flower.pkvalue1 == 0) {
                        $scope.createInfo.flower.pkvalue1 = 1;
                    } else {
                        $scope.createInfo.flower.pkvalue1 = 0;
                    }
                } else if (num == 2) {
                    if ($scope.createInfo.flower.pkvalue2 == 0) {
                        $scope.createInfo.flower.pkvalue2 = 1;
                    } else {
                        $scope.createInfo.flower.pkvalue2 = 0;
                    }
                }

            }
            else if(type==3){
                $scope.createInfo.flower.ticket_count=num;
            }
            else if(type==4){
                $scope.createInfo.flower.upper_limit=num;
            }
            else if(type==5){
                $scope.createInfo.flower.seen=num;
            }
        }
        else if($scope.createInfo.isShow==111){
            if(type==1){
                $scope.createInfo.bflower.chip_type=num;
            }
            else if(type==2){

                if (num == 1) {
                    if ($scope.createInfo.bflower.pkvalue1 == 0) {
                        $scope.createInfo.bflower.pkvalue1 = 1;
                    } else {
                        $scope.createInfo.bflower.pkvalue1 = 0;
                    }
                } else if (num == 2) {
                    if ($scope.createInfo.bflower.pkvalue2 == 0) {
                        $scope.createInfo.bflower.pkvalue2 = 1;
                    } else {
                        $scope.createInfo.bflower.pkvalue2 = 0;
                    }
                }
            }
            else if(type==3){
                $scope.createInfo.bflower.ticket_count=num;
            }
            else if(type==4){
                $scope.createInfo.bflower.upper_limit=num;
            }
            else if(type==5){
                $scope.createInfo.bflower.seen=num;
            }
        }
        else if($scope.createInfo.isShow==2){
            if (type==1) {
                $scope.createInfo.landlord.base_score = num;
            } else if (type==2) {
                $scope.createInfo.landlord.ask_mode = num;
            } else if(type==3) {
                $scope.createInfo.landlord.ticket_count = num;
            }
        }
        else if($scope.createInfo.isShow==3){
            if(type==1){
                $scope.createInfo.bull.score_type=num;
            }
            else if(type==2){
                $scope.createInfo.bull.rule_type=num;
            }
            else if(type==3){
                if(num==1)
                    $scope.createInfo.bull.is_cardfive=($scope.createInfo.bull.is_cardfive+1)%2;
                else if(num==2)
                    $scope.createInfo.bull.is_cardbomb=($scope.createInfo.bull.is_cardbomb+1)%2;
                else if(num==3)
                    $scope.createInfo.bull.is_cardtiny=($scope.createInfo.bull.is_cardtiny+1)%2;
                else if(num==9)
                    $scope.createInfo.bull.is_cardfour=($scope.createInfo.bull.is_cardfour+1)%2;
            }
            else if(type==4){
                $scope.createInfo.bull.ticket_type=num;
            } else if (type == 5) {
                $scope.createInfo.bull.banker_score=num;
            }
        }
        else if($scope.createInfo.isShow==4){
            if(type==1){
                $scope.createInfo.majiang.joker=num;
            }
            else if(type==2){
                $scope.createInfo.majiang.horse_count=num;
            }
            else if(type==3){
                $scope.createInfo.majiang.qianggang=($scope.createInfo.majiang.qianggang+1)%2;
            }
            else if(type==4){
                $scope.createInfo.majiang.ticket_count=num;
            }
            else if(type==5){
                $scope.createInfo.majiang.chengbao=($scope.createInfo.majiang.chengbao+1)%2;
            }
        }
        else if($scope.createInfo.isShow==5){
            if(type==1){
                $scope.createInfo.bull9.score_type=num;
            }
            else if(type==2){
                $scope.createInfo.bull9.rule_type=num;
            }
            else if(type==3){
                if(num==1)
                    $scope.createInfo.bull9.is_cardfive=($scope.createInfo.bull9.is_cardfive+1)%2;
                else if(num==2)
                    $scope.createInfo.bull9.is_cardbomb=($scope.createInfo.bull9.is_cardbomb+1)%2;
                else if(num==3)
                    $scope.createInfo.bull9.is_cardtiny=($scope.createInfo.bull9.is_cardtiny+1)%2;
                else if(num==9)
                    $scope.createInfo.bull9.is_cardfour=($scope.createInfo.bull9.is_cardfour+1)%2;
            }
            else if(type==4){
                $scope.createInfo.bull9.ticket_type=num;
            } else if (type == 5) {
                $scope.createInfo.bull9.banker_score=num;
            }
        }
        else if($scope.createInfo.isShow==6){
            if(type==1){
                $scope.createInfo.bull8x.score_type=num;
            } else if(type==2){
                $scope.createInfo.bull8x.rule_type=num;
            } else if(type==3){
                if(num==1) {
                    $scope.createInfo.bull8x.is_cardfive=($scope.createInfo.bull8x.is_cardfive+1)%2;
                } else if(num==2) {
                    $scope.createInfo.bull8x.is_cardbomb=($scope.createInfo.bull8x.is_cardbomb+1)%2;
                } else if(num==3) {
                    $scope.createInfo.bull8x.is_cardtiny=($scope.createInfo.bull8x.is_cardtiny+1)%2;
                } else if (num == 9) {
                    $scope.createInfo.bull8x.is_cardfour=($scope.createInfo.bull8x.is_cardfour+1)%2;
                }
            } else if(type==4){
                $scope.createInfo.bull8x.ticket_type=num;
            } else if (type == 5) {
                $scope.createInfo.bull8x.banker_score=num;
            }
        }
        else if($scope.createInfo.isShow==7){
            if(type==1){
                $scope.createInfo.nbull8x.score_type=num;
            }
            else if(type==2){
                $scope.createInfo.nbull8x.rule_type=num;
            }
            else if(type==3){
                if(num==1)
                    $scope.createInfo.nbull8x.is_cardfive=($scope.createInfo.nbull8x.is_cardfive+1)%2;
                else if(num==2)
                    $scope.createInfo.nbull8x.is_cardbomb=($scope.createInfo.nbull8x.is_cardbomb+1)%2;
                else if(num==3)
                    $scope.createInfo.nbull8x.is_cardtiny=($scope.createInfo.nbull8x.is_cardtiny+1)%2;
                else if(num==9)
                    $scope.createInfo.nbull8x.is_cardfour=($scope.createInfo.nbull8x.is_cardfour+1)%2;
            }
            else if(type==4){
                $scope.createInfo.nbull8x.ticket_type=num;
            } else if (type == 5) {
                $scope.createInfo.nbull8x.banker_score=num;
            }
        }
        else if($scope.createInfo.isShow==8){
            if(type==1){
                $scope.createInfo.tbull.score_type=num;
            }
            else if(type==2){
                $scope.createInfo.tbull.rule_type=num;
            }
            else if(type==3){
                if(num==1)
                    $scope.createInfo.tbull.is_cardfive=($scope.createInfo.tbull.is_cardfive+1)%2;
                else if(num==2)
                    $scope.createInfo.tbull.is_cardbomb=($scope.createInfo.tbull.is_cardbomb+1)%2;
                else if(num==3)
                    $scope.createInfo.tbull.is_cardtiny=($scope.createInfo.tbull.is_cardtiny+1)%2;
                else if(num==9)
                    $scope.createInfo.tbull.is_cardfour=($scope.createInfo.tbull.is_cardfour+1)%2;
            }
            else if(type==4){
                $scope.createInfo.tbull.ticket_type=num;
            } else if (type == 5) {
                $scope.createInfo.tbull.banker_score=num;
            }
        }
        else if($scope.createInfo.isShow==9){
            if(type==1){
                $scope.createInfo.tbull8x.score_type=num;
            }
            else if(type==2){
                $scope.createInfo.tbull8x.rule_type=num;
            }
            else if(type==3){
                if(num==1)
                    $scope.createInfo.tbull8x.is_cardfive=($scope.createInfo.tbull8x.is_cardfive+1)%2;
                else if(num==2)
                    $scope.createInfo.tbull8x.is_cardbomb=($scope.createInfo.tbull8x.is_cardbomb+1)%2;
                else if(num==3)
                    $scope.createInfo.tbull8x.is_cardtiny=($scope.createInfo.tbull8x.is_cardtiny+1)%2;
                else if(num==9)
                    $scope.createInfo.tbull8x.is_cardfour=($scope.createInfo.tbull8x.is_cardfour+1)%2;
            }
            else if(type==4){
                $scope.createInfo.tbull8x.ticket_type=num;
            } else if (type == 5) {
                $scope.createInfo.tbull8x.banker_score=num;
            }
        }
        else if($scope.createInfo.isShow==71){
            if(type==1){
                $scope.createInfo.lbull.score_type=num;
            }
            else if(type==2){
                $scope.createInfo.lbull.rule_type=num;
            }
            else if(type==3){
                if(num==1)
                    $scope.createInfo.lbull.is_straight=($scope.createInfo.lbull.is_straight+1)%2;
                else if(num==2)
                    $scope.createInfo.lbull.is_cardfive=($scope.createInfo.lbull.is_cardfive+1)%2;
                else if(num==3)
                    $scope.createInfo.lbull.is_flush=($scope.createInfo.lbull.is_flush+1)%2;
                else if(num==4)
                    $scope.createInfo.lbull.is_hulu=($scope.createInfo.lbull.is_hulu+1)%2;
                else if(num==5)
                    $scope.createInfo.lbull.is_cardbomb=($scope.createInfo.lbull.is_cardbomb+1)%2;
                else if(num==6)
                    $scope.createInfo.lbull.is_cardtiny=($scope.createInfo.lbull.is_cardtiny+1)%2;
                else if(num==9)
                    $scope.createInfo.lbull.is_cardfour=($scope.createInfo.lbull.is_cardfour+1)%2;
            }
            else if(type==4){
                $scope.createInfo.lbull.ticket_type=num;
            } else if (type == 5) {
                $scope.createInfo.lbull.banker_score=num;
            }
        }
        else if($scope.createInfo.isShow==110){
            if(type==1){
                $scope.createInfo.tflower.chip_type=num;
            }
            else if(type==2){

                if (num == 1) {
                    if ($scope.createInfo.tflower.pkvalue1 == 0) {
                        $scope.createInfo.tflower.pkvalue1 = 1;
                    } else {
                        $scope.createInfo.tflower.pkvalue1 = 0;
                    }
                } else if (num == 2) {
                    if ($scope.createInfo.tflower.pkvalue2 == 0) {
                        $scope.createInfo.tflower.pkvalue2 = 1;
                    } else {
                        $scope.createInfo.tflower.pkvalue2 = 0;
                    }
                }

            }
            else if(type==3){
                $scope.createInfo.tflower.ticket_count=num;
            }
            else if(type==4){
                $scope.createInfo.tflower.upper_limit=num;
            }
            else if(type==5){
                $scope.createInfo.tflower.seen=num;
            }
            console.log($scope.createInfo.tflower)
        }
        else if($scope.createInfo.isShow==36){
            if(type==1){
                $scope.createInfo.sangong.score_type=num;
            }
            else if(type==2){
                if (num==1) {
                    $scope.createInfo.sangong.is_joker = Math.abs($scope.createInfo.sangong.is_joker - 1);
                }
                if (num==2) {
                    $scope.createInfo.sangong.is_bj = Math.abs($scope.createInfo.sangong.is_bj - 1);
                }
            }
            else if(type==3){
                $scope.createInfo.sangong.ticket_type=num;
            }
            console.log(JSON.stringify($scope.createInfo.sangong));
        }
        else if($scope.createInfo.isShow==37){
            if(type==1){
                $scope.createInfo.nsangong.score_type=num;
            }
            else if(type==2){
                if (num==1) {
                    $scope.createInfo.nsangong.is_joker = Math.abs($scope.createInfo.nsangong.is_joker - 1);
                }
                if (num==2) {
                    $scope.createInfo.nsangong.is_bj = Math.abs($scope.createInfo.nsangong.is_bj - 1);
                }
            }
            else if(type==3){
                $scope.createInfo.nsangong.ticket_type=num;
            }
            console.log(JSON.stringify($scope.createInfo.nsangong));
        }
        else if($scope.createInfo.isShow==38){
            if(type==1){
                $scope.createInfo.tsangong.score_type=num;
            }
            else if(type==2){
                if (num==1) {
                    $scope.createInfo.tsangong.is_joker = Math.abs($scope.createInfo.tsangong.is_joker - 1);
                }
                if (num==2) {
                    $scope.createInfo.tsangong.is_bj = Math.abs($scope.createInfo.tsangong.is_bj - 1);
                }
            }
            else if(type==3){
                $scope.createInfo.tsangong.ticket_type=num;
            }
            console.log(JSON.stringify($scope.createInfo.tsangong));
        }
    };

    $scope.createFlower=function(){
        $(".createRoom .mainPart").css('height','');
        $(".createRoom .mainPart .blueBack").css('height','');
        $scope.createInfo.isShow=1;
        var $chip_type = Math.ceil($scope.createInfo.flower.chip_type);
        if ($chip_type != 1 && $chip_type != 2 && $chip_type != 4) {
            $scope.createInfo.flower.chip_type = 1;
        }
        var $seen = Math.ceil($scope.createInfo.flower.seen);
        if ($seen != 0 && $seen != 20 && $seen != 50 && $seen != 100) {
            $scope.createInfo.flower.seen = 0;
        }
    };

    $scope.createBFlower=function(){
        $(".createRoom .mainPart").css('height','');
        $(".createRoom .mainPart .blueBack").css('height','');
        $scope.createInfo.isShow=111;
        var $chip_type = Math.ceil($scope.createInfo.bflower.chip_type);
        if ($chip_type != 1 && $chip_type != 2 && $chip_type != 4) {
            $scope.createInfo.bflower.chip_type = 1;
        }
        var $seen = Math.ceil($scope.createInfo.bflower.seen);
        if ($seen != 0 && $seen != 20 && $seen != 50 && $seen != 100) {
            $scope.createInfo.bflower.seen = 0;
        }
    };
    $scope.createTenFlower=function(){
        $(".createRoom .mainPart").css('height','');
        $(".createRoom .mainPart .blueBack").css('height','');
        $scope.createInfo.isShow=110;
        var $chip_type = Math.ceil($scope.createInfo.tflower.chip_type);
        if ($chip_type != 1 && $chip_type != 2 && $chip_type != 4) {
            $scope.createInfo.tflower.chip_type = 1;
        }
        var $seen = Math.ceil($scope.createInfo.tflower.seen);
        if ($seen != 0 && $seen != 20 && $seen != 50 && $seen != 100) {
            $scope.createInfo.tflower.seen = 0;
        }
    };
    $scope.createLandlord=function(){
        $(".createRoom .mainPart").css('height','');
        $(".createRoom .mainPart .blueBack").css('height','');
        $scope.createInfo.isShow=2;
    }
    $scope.createBull=function(){
        $(".createRoom .mainPart").css('height','71vh');
        $(".createRoom .mainPart .blueBack").css('height','50vh');
        $scope.createInfo.isShow=3;
    }
    $scope.createMajiang=function(){
        $(".createRoom .mainPart").css('height','');
        $(".createRoom .mainPart .blueBack").css('height','');
        $scope.createInfo.isShow=4;
    }
    $scope.createBull9=function(){
        $(".createRoom .mainPart").css('height','71vh');
        $(".createRoom .mainPart .blueBack").css('height','50vh');
        $scope.createInfo.isShow=5;
    }

    $scope.createBull8x=function(){
        $(".createRoom .mainPart").css('height','71vh');
        $(".createRoom .mainPart .blueBack").css('height','50vh');
        $scope.createInfo.isShow=6;
    }

    $scope.createNBull8x=function(){
        $(".createRoom .mainPart").css('height','71vh');
        $(".createRoom .mainPart .blueBack").css('height','50vh');
        $scope.createInfo.isShow=7;
    }

    $scope.createBull12=function(){
        $(".createRoom .mainPart").css('height','71vh');
        $(".createRoom .mainPart .blueBack").css('height','50vh');
        $scope.createInfo.isShow=8;
    }

    $scope.createTBull8x=function(){
        $(".createRoom .mainPart").css('height','71vh');
        $(".createRoom .mainPart .blueBack").css('height','50vh');
        $scope.createInfo.isShow=9;
    };

    $scope.createSangong = function(){
        $(".createRoom .mainPart").css('height','50.5vh');
        $(".createRoom .mainPart .blueBack").css('height','30vh');
        $scope.createInfo.isShow = 36;
    };

    $scope.createNSangong = function(){
        $(".createRoom .mainPart").css('height','50.5vh');
        $(".createRoom .mainPart .blueBack").css('height','30vh');
        $scope.createInfo.isShow = 37;
    };

    $scope.createTSangong = function(){
        $(".createRoom .mainPart").css('height','50.5vh');
        $(".createRoom .mainPart .blueBack").css('height','30vh');
        $scope.createInfo.isShow = 38;
    };

    $scope.createLBull=function(){
        $(".createRoom .mainPart").css('height','71vh');
        $(".createRoom .mainPart .blueBack").css('height','50vh');
        $scope.createInfo.isShow=71;
    }

    $scope.selectBankerMode = function (type) {
        if (type == 1) {
            $scope.createInfo.bull.score_type = 1;
            $scope.createInfo.bull.banker1 = "selected";
            $scope.createInfo.bull.banker2 = "unselected";
            $scope.createInfo.bull.banker3 = "unselected";
            $scope.createInfo.bull.banker4 = "unselected";
            $scope.createInfo.bull.banker5 = "unselected";
        } else if (type == 2) {
            $scope.createInfo.bull.score_type = 1;
            $scope.createInfo.bull.banker1 = "unselected";
            $scope.createInfo.bull.banker2 = "selected";
            $scope.createInfo.bull.banker3 = "unselected";
            $scope.createInfo.bull.banker4 = "unselected";
            $scope.createInfo.bull.banker5 = "unselected";
        } else if (type == 3) {
            $scope.createInfo.bull.score_type = 1;
            $scope.createInfo.bull.banker1 = "unselected";
            $scope.createInfo.bull.banker2 = "unselected";
            $scope.createInfo.bull.banker3 = "selected";
            $scope.createInfo.bull.banker4 = "unselected";
            $scope.createInfo.bull.banker5 = "unselected";
        } else if (type == 4) {
            $scope.createInfo.bull.score_type = 4;
            $scope.createInfo.bull.banker1 = "unselected";
            $scope.createInfo.bull.banker2 = "unselected";
            $scope.createInfo.bull.banker3 = "unselected";
            $scope.createInfo.bull.banker4 = "selected";
            $scope.createInfo.bull.banker5 = "unselected";
        } else if (type == 5) {
            $scope.createInfo.bull.score_type = 1;
            $scope.createInfo.bull.banker1 = "unselected";
            $scope.createInfo.bull.banker2 = "unselected";
            $scope.createInfo.bull.banker3 = "unselected";
            $scope.createInfo.bull.banker4 = "unselected";
            $scope.createInfo.bull.banker5 = "selected";
        }

        $scope.createInfo.bull.banker_mode = type;
    };

    $scope.selectBankerMode9 = function (type) {
        if (type == 1) {
            $scope.createInfo.bull9.score_type = 1;
            $scope.createInfo.bull9.banker1 = "selected";
            $scope.createInfo.bull9.banker2 = "unselected";
            $scope.createInfo.bull9.banker3 = "unselected";
            $scope.createInfo.bull9.banker4 = "unselected";
            $scope.createInfo.bull9.banker5 = "unselected";
        } else if (type == 2) {
            $scope.createInfo.bull9.score_type = 1;
            $scope.createInfo.bull9.banker1 = "unselected";
            $scope.createInfo.bull9.banker2 = "selected";
            $scope.createInfo.bull9.banker3 = "unselected";
            $scope.createInfo.bull9.banker4 = "unselected";
            $scope.createInfo.bull9.banker5 = "unselected";
        } else if (type == 3) {
            $scope.createInfo.bull9.score_type = 1;
            $scope.createInfo.bull9.banker1 = "unselected";
            $scope.createInfo.bull9.banker2 = "unselected";
            $scope.createInfo.bull9.banker3 = "selected";
            $scope.createInfo.bull9.banker4 = "unselected";
            $scope.createInfo.bull9.banker5 = "unselected";
        } else if (type == 4) {
            $scope.createInfo.bull9.score_type = 4;
            $scope.createInfo.bull9.banker1 = "unselected";
            $scope.createInfo.bull9.banker2 = "unselected";
            $scope.createInfo.bull9.banker3 = "unselected";
            $scope.createInfo.bull9.banker4 = "selected";
            $scope.createInfo.bull9.banker5 = "unselected";
        } else if (type == 5) {
            $scope.createInfo.bull9.score_type = 1;
            $scope.createInfo.bull9.banker1 = "unselected";
            $scope.createInfo.bull9.banker2 = "unselected";
            $scope.createInfo.bull9.banker3 = "unselected";
            $scope.createInfo.bull9.banker4 = "unselected";
            $scope.createInfo.bull9.banker5 = "selected";
        }

        $scope.createInfo.bull9.banker_mode = type;
    };


    $scope.selectBankerBull8x = function (type) {
        if (type == 1) {
            $scope.createInfo.bull8x.score_type = 1;
            $scope.createInfo.bull8x.banker1 = "selected";
            $scope.createInfo.bull8x.banker2 = "unselected";
            $scope.createInfo.bull8x.banker3 = "unselected";
            $scope.createInfo.bull8x.banker4 = "unselected";
            $scope.createInfo.bull8x.banker5 = "unselected";
        } else if (type == 2) {
            $scope.createInfo.bull8x.score_type = 1;
            $scope.createInfo.bull8x.banker1 = "unselected";
            $scope.createInfo.bull8x.banker2 = "selected";
            $scope.createInfo.bull8x.banker3 = "unselected";
            $scope.createInfo.bull8x.banker4 = "unselected";
            $scope.createInfo.bull8x.banker5 = "unselected";
        } else if (type == 3) {
            $scope.createInfo.bull8x.score_type = 1;
            $scope.createInfo.bull8x.banker1 = "unselected";
            $scope.createInfo.bull8x.banker2 = "unselected";
            $scope.createInfo.bull8x.banker3 = "selected";
            $scope.createInfo.bull8x.banker4 = "unselected";
            $scope.createInfo.bull8x.banker5 = "unselected";
        } else if (type == 4) {
            $scope.createInfo.bull8x.score_type = 4;
            $scope.createInfo.bull8x.banker1 = "unselected";
            $scope.createInfo.bull8x.banker2 = "unselected";
            $scope.createInfo.bull8x.banker3 = "unselected";
            $scope.createInfo.bull8x.banker4 = "selected";
            $scope.createInfo.bull8x.banker5 = "unselected";
        } else if (type == 5) {
            $scope.createInfo.bull8x.score_type = 1;
            $scope.createInfo.bull8x.banker1 = "unselected";
            $scope.createInfo.bull8x.banker2 = "unselected";
            $scope.createInfo.bull8x.banker3 = "unselected";
            $scope.createInfo.bull8x.banker4 = "unselected";
            $scope.createInfo.bull8x.banker5 = "selected";
        }

        $scope.createInfo.bull8x.banker_mode = type;
    };

    $scope.selectBankerNBull8x = function (type) {
        if (type == 1) {
            $scope.createInfo.nbull8x.score_type = 1;
            $scope.createInfo.nbull8x.banker1 = "selected";
            $scope.createInfo.nbull8x.banker2 = "unselected";
            $scope.createInfo.nbull8x.banker3 = "unselected";
            $scope.createInfo.nbull8x.banker4 = "unselected";
            $scope.createInfo.nbull8x.banker5 = "unselected";
        } else if (type == 2) {
            $scope.createInfo.nbull8x.score_type = 1;
            $scope.createInfo.nbull8x.banker1 = "unselected";
            $scope.createInfo.nbull8x.banker2 = "selected";
            $scope.createInfo.nbull8x.banker3 = "unselected";
            $scope.createInfo.nbull8x.banker4 = "unselected";
            $scope.createInfo.nbull8x.banker5 = "unselected";
        } else if (type == 3) {
            $scope.createInfo.nbull8x.score_type = 1;
            $scope.createInfo.nbull8x.banker1 = "unselected";
            $scope.createInfo.nbull8x.banker2 = "unselected";
            $scope.createInfo.nbull8x.banker3 = "selected";
            $scope.createInfo.nbull8x.banker4 = "unselected";
            $scope.createInfo.nbull8x.banker5 = "unselected";
        } else if (type == 4) {
            $scope.createInfo.nbull8x.score_type = 4;
            $scope.createInfo.nbull8x.banker1 = "unselected";
            $scope.createInfo.nbull8x.banker2 = "unselected";
            $scope.createInfo.nbull8x.banker3 = "unselected";
            $scope.createInfo.nbull8x.banker4 = "selected";
            $scope.createInfo.nbull8x.banker5 = "unselected";
        } else if (type == 5) {
            $scope.createInfo.nbull8x.score_type = 1;
            $scope.createInfo.nbull8x.banker1 = "unselected";
            $scope.createInfo.nbull8x.banker2 = "unselected";
            $scope.createInfo.nbull8x.banker3 = "unselected";
            $scope.createInfo.nbull8x.banker4 = "unselected";
            $scope.createInfo.nbull8x.banker5 = "selected";
        }

        $scope.createInfo.nbull8x.banker_mode = type;
    };

    $scope.selectBankerTBull = function (type) {
        if (type == 1) {
            $scope.createInfo.tbull.score_type = 1;
            $scope.createInfo.tbull.banker1 = "selected";
            $scope.createInfo.tbull.banker2 = "unselected";
            $scope.createInfo.tbull.banker3 = "unselected";
            $scope.createInfo.tbull.banker4 = "unselected";
            $scope.createInfo.tbull.banker5 = "unselected";
        } else if (type == 2) {
            $scope.createInfo.tbull.score_type = 1;
            $scope.createInfo.tbull.banker1 = "unselected";
            $scope.createInfo.tbull.banker2 = "selected";
            $scope.createInfo.tbull.banker3 = "unselected";
            $scope.createInfo.tbull.banker4 = "unselected";
            $scope.createInfo.tbull.banker5 = "unselected";
        } else if (type == 3) {
            $scope.createInfo.tbull.score_type = 1;
            $scope.createInfo.tbull.banker1 = "unselected";
            $scope.createInfo.tbull.banker2 = "unselected";
            $scope.createInfo.tbull.banker3 = "selected";
            $scope.createInfo.tbull.banker4 = "unselected";
            $scope.createInfo.tbull.banker5 = "unselected";
        } else if (type == 4) {
            $scope.createInfo.tbull.score_type = 4;
            $scope.createInfo.tbull.banker1 = "unselected";
            $scope.createInfo.tbull.banker2 = "unselected";
            $scope.createInfo.tbull.banker3 = "unselected";
            $scope.createInfo.tbull.banker4 = "selected";
            $scope.createInfo.tbull.banker5 = "unselected";
        } else if (type == 5) {
            $scope.createInfo.tbull.score_type = 1;
            $scope.createInfo.tbull.banker1 = "unselected";
            $scope.createInfo.tbull.banker2 = "unselected";
            $scope.createInfo.tbull.banker3 = "unselected";
            $scope.createInfo.tbull.banker4 = "unselected";
            $scope.createInfo.tbull.banker5 = "selected";
        }

        $scope.createInfo.tbull.banker_mode = type;
    };

    $scope.selectBankerTBull8x = function (type) {
        if (type == 1) {
            $scope.createInfo.tbull8x.score_type = 1;
            $scope.createInfo.tbull8x.banker1 = "selected";
            $scope.createInfo.tbull8x.banker2 = "unselected";
            $scope.createInfo.tbull8x.banker3 = "unselected";
            $scope.createInfo.tbull8x.banker4 = "unselected";
            $scope.createInfo.tbull8x.banker5 = "unselected";
        } else if (type == 2) {
            $scope.createInfo.tbull8x.score_type = 1;
            $scope.createInfo.tbull8x.banker1 = "unselected";
            $scope.createInfo.tbull8x.banker2 = "selected";
            $scope.createInfo.tbull8x.banker3 = "unselected";
            $scope.createInfo.tbull8x.banker4 = "unselected";
            $scope.createInfo.tbull8x.banker5 = "unselected";
        } else if (type == 3) {
            $scope.createInfo.tbull8x.score_type = 1;
            $scope.createInfo.tbull8x.banker1 = "unselected";
            $scope.createInfo.tbull8x.banker2 = "unselected";
            $scope.createInfo.tbull8x.banker3 = "selected";
            $scope.createInfo.tbull8x.banker4 = "unselected";
            $scope.createInfo.tbull8x.banker5 = "unselected";
        } else if (type == 4) {
            $scope.createInfo.tbull8x.score_type = 4;
            $scope.createInfo.tbull8x.banker1 = "unselected";
            $scope.createInfo.tbull8x.banker2 = "unselected";
            $scope.createInfo.tbull8x.banker3 = "unselected";
            $scope.createInfo.tbull8x.banker4 = "selected";
            $scope.createInfo.tbull8x.banker5 = "unselected";
        } else if (type == 5) {
            $scope.createInfo.tbull8x.score_type = 1;
            $scope.createInfo.tbull8x.banker1 = "unselected";
            $scope.createInfo.tbull8x.banker2 = "unselected";
            $scope.createInfo.tbull8x.banker3 = "unselected";
            $scope.createInfo.tbull8x.banker4 = "unselected";
            $scope.createInfo.tbull8x.banker5 = "selected";
        }

        $scope.createInfo.tbull8x.banker_mode = type;
    };

    $scope.selectBankerSangong = function (type) {
        if (type == 1) {
            $scope.createInfo.sangong.banker1 = "selected";
            $scope.createInfo.sangong.banker2 = "unselected";
        } else if (type == 2) {
            $scope.createInfo.sangong.banker1 = "unselected";
            $scope.createInfo.sangong.banker2 = "selected";
        }
        $scope.createInfo.sangong.banker_mode = type;
    };
    $scope.selectBankerNSangong = function (type) {
        if (type == 1) {
            $scope.createInfo.nsangong.banker1 = "selected";
            $scope.createInfo.nsangong.banker2 = "unselected";
        } else if (type == 2) {
            $scope.createInfo.nsangong.banker1 = "unselected";
            $scope.createInfo.nsangong.banker2 = "selected";
        }
        $scope.createInfo.nsangong.banker_mode = type;
    };
    $scope.selectBankerTSangong = function (type) {
        if (type == 1) {
            $scope.createInfo.tsangong.banker1 = "selected";
            $scope.createInfo.tsangong.banker2 = "unselected";
        } else if (type == 2) {
            $scope.createInfo.tsangong.banker1 = "unselected";
            $scope.createInfo.tsangong.banker2 = "selected";
        }
        $scope.createInfo.tsangong.banker_mode = type;
    };

    $scope.selectBankerLBull = function (type) {
        if (type == 1) {
            $scope.createInfo.lbull.score_type = 1;
            $scope.createInfo.lbull.banker1 = "selected";
            $scope.createInfo.lbull.banker2 = "unselected";
            $scope.createInfo.lbull.banker3 = "unselected";
            $scope.createInfo.lbull.banker4 = "unselected";
            $scope.createInfo.lbull.banker5 = "unselected";
        } else if (type == 2) {
            $scope.createInfo.lbull.score_type = 1;
            $scope.createInfo.lbull.banker1 = "unselected";
            $scope.createInfo.lbull.banker2 = "selected";
            $scope.createInfo.lbull.banker3 = "unselected";
            $scope.createInfo.lbull.banker4 = "unselected";
            $scope.createInfo.lbull.banker5 = "unselected";
        } else if (type == 3) {
            $scope.createInfo.lbull.score_type = 1;
            $scope.createInfo.lbull.banker1 = "unselected";
            $scope.createInfo.lbull.banker2 = "unselected";
            $scope.createInfo.lbull.banker3 = "selected";
            $scope.createInfo.lbull.banker4 = "unselected";
            $scope.createInfo.lbull.banker5 = "unselected";
        } else if (type == 4) {
            $scope.createInfo.lbull.score_type = 4;
            $scope.createInfo.lbull.banker1 = "unselected";
            $scope.createInfo.lbull.banker2 = "unselected";
            $scope.createInfo.lbull.banker3 = "unselected";
            $scope.createInfo.lbull.banker4 = "selected";
            $scope.createInfo.lbull.banker5 = "unselected";
        } else if (type == 5) {
            $scope.createInfo.lbull.score_type = 1;
            $scope.createInfo.lbull.banker1 = "unselected";
            $scope.createInfo.lbull.banker2 = "unselected";
            $scope.createInfo.lbull.banker3 = "unselected";
            $scope.createInfo.lbull.banker4 = "unselected";
            $scope.createInfo.lbull.banker5 = "selected";
        }

        $scope.createInfo.lbull.banker_mode = type;
    };

    $scope.createCommit = function () {
        if ($scope.userInfo.card>0){
            if($scope.is_operation){
                return 0;
            }

            $scope.waiting();
            //$scope.createHttpRoom();
            storeSetting($scope.createInfo);
            $http({
                method:'POST',
                url:'/f/ci',
                data: {
                    'account_id': accountId,
                    'create_info': $scope.createInfo
                }
            }).then(function (res) {
                console.log(res);
            });

            //socket创建房间，暂时废弃
            if($scope.createInfo.isShow==1){
                $scope.connectSocket($scope.socket.flower,1);
            }
            else if($scope.createInfo.isShow==111){
                $scope.connectSocket($scope.socket.bflower,111);
            }
            else if($scope.createInfo.isShow==2){
                $scope.connectSocket($scope.socket.landlord,2);
            }
            else if($scope.createInfo.isShow==3){
                $scope.connectSocket($scope.socket.bull,3);
            }
            else if($scope.createInfo.isShow==4){
                $scope.connectSocket($scope.socket.majiang,4);
            }
            else if($scope.createInfo.isShow==5){
                $scope.connectSocket($scope.socket.bull9,5);
            }
            else if($scope.createInfo.isShow==6){
                $scope.connectSocket($scope.socket.bull8x,6);
            }
            else if($scope.createInfo.isShow==7){
                $scope.connectSocket($scope.socket.nbull8x,7);
            }
            else if($scope.createInfo.isShow==8){
                $scope.connectSocket($scope.socket.tbull,8);
            }
            else if($scope.createInfo.isShow==9){
                $scope.connectSocket($scope.socket.tbull8x,9);
            }
            else if($scope.createInfo.isShow==110){
                $scope.connectSocket($scope.socket.tflower,110);
            }
            else if($scope.createInfo.isShow==36){
                $scope.connectSocket($scope.socket.sangong,36);
            }
            else if($scope.createInfo.isShow==37){
                $scope.connectSocket($scope.socket.nsangong,37);
            }
            else if($scope.createInfo.isShow==38){
                $scope.connectSocket($scope.socket.tsangong,38);
            }
            else if($scope.createInfo.isShow==71){
                $scope.connectSocket($scope.socket.lbull,71);
            }

        }
        else{
            $scope.showAlert(1,"房卡不足");
        }
    };
    $scope.cancelCreate=function(){
        $scope.createInfo.isShow=0;
    };
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

function storeSetting(createInfo) {
    localStorage.createInfo = JSON.stringify(createInfo);
}

function getSetting(default_setting) {
    var createInfo = localStorage.createInfo;
    if(createInfo){
        createInfo = JSON.parse(createInfo);
        if(typeof(createInfo.tbull) === "undefined"){
            createInfo.tbull = default_setting.tbull;
        }
        if(typeof(createInfo.tbull8x) === "undefined"){
            createInfo.tbull8x = default_setting.tbull8x;
        }
        if(typeof(createInfo.tflower) === "undefined"){
            createInfo.tflower = default_setting.tflower;
        }
        if(typeof(createInfo.sangong) === "undefined"){
            createInfo.sangong = default_setting.sangong;
        }
        if(typeof(createInfo.nsangong) === "undefined"){
            createInfo.nsangong = default_setting.nsangong;
        }
        if(typeof(createInfo.tsangong) === "undefined"){
            createInfo.tsangong = default_setting.tsangong;
        }
        if(typeof(createInfo.lbull) === "undefined"){
            createInfo.lbull = default_setting.lbull;
        }
        if(typeof(createInfo.flower) === "undefined"){
            createInfo.flower = default_setting.flower;
        }
        if(typeof(createInfo.bflower) === "undefined"){
            createInfo.bflower = default_setting.bflower;
        }
        createInfo.isShow = 0;
        return createInfo;
    }
    return default_setting;
}
