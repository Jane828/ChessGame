
var app = angular.module('app', []);


app.controller("myCtrl", function ($scope, $http) {
    $scope.userInfo = userData;
    $scope.width = window.innerWidth;
    $scope.height = window.innerHeight;
    $scope.roomCard = Math.ceil(globalData.card);
    $scope.user = userData;
    $scope.activity = [];
    $scope.isShowInvite = false;
    $scope.isShowShop = false;
    $scope.isShowMessage = false;
    $scope.alertType = 0;
    $scope.alertText = '';
    $scope.roomCardInfo = [];
    $scope.select = 1;
    $scope.ticket_count = 0;
    $scope.isDealing = false;
    $scope.isShowShopLoading = false;
    $scope.gameItems = [];
    $scope.isShop = false;
    $scope.isShowAlert = false;
    $scope.alertType = 1;
    $scope.alertText = '';
    $scope.inviteStatus = globalData.inviteStatus;  //0未申请   1等待同意    2已经同意    
    $scope.inviteName = globalData.inviteName;
    $scope.inviteAvatar = globalData.inviteAvatar;
    $scope.unionName = globalData.guildName;
    $scope.publicName = globalData.wxName;
    $scope.unionIntroduce = globalData.guildProfile;
    $scope.joinImg = globalData.imageUrl + 'files/images/activity/union_join.png';
    $scope.qrImg = globalData.qrUrl;

    $(".main").show();
    $("#loading").hide();

    $scope.httpModule = {
        joinGroup: function () {
            $http({
                url: globalData.baseUrl + 'manage/joinGroup',
                method: 'POST',
                header: { 'Content-Type': 'application/x-www-form-urlencoded' },
                data: {
                    "user_code": globalData.userCode,
                }
            }).success(function (data, header, config, status) {
                    
                    if (data.result == 0) {
                        setTimeout(function () {
                            $(".imgOpen").removeClass('transf');
                            $scope.inviteStatus = data.data.status + 1;
                            $scope.$apply();
                        }, 1500);

                    } else {
                        setTimeout(function () {
                            $(".imgOpen").removeClass('transf');
                            $scope.$apply();
                            console.log(data);
                            $scope.showAlert(7,data.result_message);
                        }, 10);
                        
                        
                    }

                }).error(function (data, header, config, status) {
                    //window.location.reload();
                });
        },
    };

    $scope.clickJoin = function () {
        //$scope.joinImg = globalData.imageUrl + 'files/images/activity/union_join_active.png';
        $(".imgOpen").addClass('transf');
        $scope.httpModule.joinGroup();
        // setTimeout(function () {
        //     $(".imgOpen").removeClass('transf');
        //     $scope.showType = 2;
        //     $scope.$apply();
        // }, 1500);
    };

    $scope.showAlert = function (type, text) {
        $scope.alertType = type;
        $scope.alertText = text;
        $scope.isShowAlert = true;

        setTimeout(function () {
            $scope.$apply();
        }, 0)

        setTimeout(function () {
            var alertHeight = $(".alertText").height();
            var textHeight = alertHeight;
            var height = window.innerHeight;

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
            // $(".alert .mainPart .alertText").css('top', alertTop + 'px');

            $scope.$apply();
        }, 0);
    };

    $scope.closeAlert = function () {
        setTimeout(function () {
            $scope.isShowAlert = false;
            $scope.$apply();
        }, 0);

    };

});


wx.config({
    debug:false,
    appId:configData.appId,
    timestamp:configData.timestamp,
    nonceStr:configData.nonceStr,
    signature:configData.signature,
    jsApiList:[ "onMenuShareTimeline", "onMenuShareAppMessage", "hideMenuItems" ]
});

wx.ready(function () {
    wx.hideMenuItems({
        menuList: [
            "menuItem:copyUrl",
            "menuItem:share:qq",
            "menuItem:share:weiboApp",
            "menuItem:share:facebook",
            "menuItem:share:QZone",
            "menuItem:editTag",
            "menuItem:copyUrl",
            "menuItem:share:email",
        ] // 要隐藏的菜单项，只能隐藏“传播类”和“保护类”按钮，所有menu项见附录3
    });

    wx.onMenuShareTimeline({
        title: globalData.inviteName + '的邀请函',
        desc: "给你发了一份邀请函",
        link: globalData.shareUrl,
        imgUrl: globalData.shareIcon,
        success: function () {
            // 用户确认分享后执行的回调函数
        },
        cancel: function () {
            // 用户取消分享后执行的回调函数
        }
    });

    wx.onMenuShareAppMessage({
        title: globalData.inviteName + '的邀请函',
        desc: "给你发了一份邀请函",
        link: globalData.shareUrl,
        imgUrl: globalData.shareIcon,
        success: function () {
            // 用户确认分享后执行的回调函数
        },
        cancel: function () {
            // 用户取消分享后执行的回调函数
        }
    });
});
