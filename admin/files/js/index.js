

var ws;



$(document).ready(function() {

	
	
  	$('#reservationAd').daterangepicker(null, function(start, end, label) {                               
		var s=start._d;
		s = new Date(s);
		var y=s.getFullYear(),m=s.getMonth()+1,d=s.getDate();
		if(m<10)
		    m="0"+m;
		if(d<10)
		    d="0"+d;
		start=y+"-"+m+"-"+d;
		end=end.toISOString().slice(0,10);
		
		appData.ad.start_time.date=start;
        appData.ad.end_time.date=end;

    });    
   
   	$('#reservation').daterangepicker(null, function(start, end, label) {                               
		var s=start._d;
		s = new Date(s);
		var y=s.getFullYear(),m=s.getMonth()+1,d=s.getDate();
		if(m<10)
		    m="0"+m;
		if(d<10)
		    d="0"+d;
		start=y+"-"+m+"-"+d;
		end=end.toISOString().slice(0,10);
		viewMethods.chooseDate(start,end);
    });  
    
   	$('#reservation1').daterangepicker(null, function(start, end, label) {                          
		var s=start._d;
		s = new Date(s);
		var y=s.getFullYear(),m=s.getMonth()+1,d=s.getDate();
		if(m<10)
		    m="0"+m;
		if(d<10)
		    d="0"+d;
		start=y+"-"+m+"-"+d;
		end=end.toISOString().slice(0,10);
		
		appData.gameSearch.from=start;
        appData.gameSearch.to=end;
        
		appData.playDetail.page=1;
		appData.playDetail.total_page=1;
		httpModule.getPlayCount();
		httpModule.getPlayDetailList();
    });
   	$('#reservation12').daterangepicker(null, function(start, end, label) {
		var s=start._d;
		s = new Date(s);
		var y=s.getFullYear(),m=s.getMonth()+1,d=s.getDate();
		if(m<10)
		    m="0"+m;
		if(d<10)
		    d="0"+d;
		start=y+"-"+m+"-"+d;
		end=end.toISOString().slice(0,10);

		appData.gameSearch.from=start;
        appData.gameSearch.to=end;

		appData.playDetail.page=1;
		appData.playDetail.total_page=1;
		httpModule.getPlayCount();
		httpModule.getPlayDetailList();
    });
   	$('#reservation13').daterangepicker(null, function(start, end, label) {
		var s=start._d;
		s = new Date(s);
		var y=s.getFullYear(),m=s.getMonth()+1,d=s.getDate();
		if(m<10)
		    m="0"+m;
		if(d<10)
		    d="0"+d;
		start=y+"-"+m+"-"+d;
		end=end.toISOString().slice(0,10);

		appData.scoreStat.from=start;
        appData.scoreStat.to=end;

		httpModule.getGameScoreStat();
    });
    $('#reservation6').daterangepicker(null, function(start, end, label) {                          
		var s=start._d;
		s = new Date(s);
		var y=s.getFullYear(),m=s.getMonth()+1,d=s.getDate();
		if(m<10)
		    m="0"+m;
		if(d<10)
		    d="0"+d;
		start=y+"-"+m+"-"+d;
		end=end.toISOString().slice(0,10);
		
		appData.withdrawRange.from=start;
        appData.withdrawRange.to=end;
        
		appData.withdrawRange.page=1;
		appData.withdrawRange.total_page=1;
		
		httpModule.getGuildWithdrawList();
    });
	$('#reservation8').daterangepicker(null, function(start, end, label) {                          
		var s=start._d;
		s = new Date(s);
		var y=s.getFullYear(),m=s.getMonth()+1,d=s.getDate();
		if(m<10)
		    m="0"+m;
		if(d<10)
		    d="0"+d;
		start=y+"-"+m+"-"+d;
		end=end.toISOString().slice(0,10);
		
		appData.channelUser.from1=start;
        appData.channelUser.to1=end;
		
    });
    
    $('#reservation12').daterangepicker(null, function(start, end, label) {                               
		var s=start._d;
		s = new Date(s);
		var y=s.getFullYear(),m=s.getMonth()+1,d=s.getDate();
		if(m<10)
		    m="0"+m;
		if(d<10)
		    d="0"+d;
		start=y+"-"+m+"-"+d;
		end=end.toISOString().slice(0,10);
		
		appData.fruit.page=1;
		appData.fruit.total_page=1;
		appData.fruit.from=start;
        appData.fruit.to=end;
		httpModule.getSlotSummary();
		httpModule.getSlotList();
    });   
    
	appData.fromDate  = globalData.fromDate;
	appData.toDate  = globalData.toDate;

	
});



var httpModule = {

    getdealerList: function () {

        Vue.http.post(globalData.baseUrl + 'admin/dealerList', {
            "keyword":appData.searchKeyword
        }).then(function(response) {
            logMessage(response.body);
            var bodyData = response.body;

            if (bodyData.result == 0) {
                appData.dealerList = bodyData.data.concat();

                for (var i = 0; i < appData.dealerList.length; i++) {
                    //appData.dealerList[i].num = i + 1;
                    //appData.dealerList[i].name = "123";
                }

                viewMethods.selectDealer(appData.dealerList[0]);
                
            }
            else if(bodyData.result == -3)
            {
                window.location.reload();
            } 
            else {
                alert(bodyData.result_message);
            }

        }, function(response) {
            logMessage(response.body);
        });
    },

    getDealerInfo: function () {
        Vue.http.post(globalData.baseUrl + 'admin/dealerInfo', {
            "dealer_num":appData.selectedDealerNum,
            "dealer_id":appData.selectDealerID
        }).then(function(response) {
            logMessage(response.body);
            var bodyData = response.body;

            if (bodyData.result == 0) {
                var dealerInfo = bodyData.data;

                appData.goodsList = dealerInfo.goods_array.concat();
                appData.inventoryCount = dealerInfo.inventory_count;
                appData.is_guild = dealerInfo.is_guild;
                appData.is_channel = dealerInfo.is_channel;
                appData.is_exchange  = dealerInfo.is_exchange;
                appData.gamescore_array = dealerInfo.gamescore_array;

                console.log(appData.goodsList);

                appData.request_1 = 0;
                     
            } else if(bodyData.result == -3)
            {
                window.location.reload();
            } else {
                alert(bodyData.result_message);
                appData.request_1 = 0;
            }

        }, function(response) {
            logMessage(response.body);
            appData.request_1 = 0;
        });
    },
    getDealerSaleInfo: function () {

        Vue.http.post(globalData.baseUrl + 'admin/dealerSaleInfo', {
            "dealer_num":appData.selectedDealerNum,
            "from":appData.fromDate,
            "to":appData.toDate
        }).then(function(response) {
            logMessage(response.body);
            var bodyData = response.body;

            if (bodyData.result == 0) {

                appData.mallSaleCount = bodyData.data.sum_ticketCount;
                appData.redSaleCount = bodyData.data.sum_redenvelop;

                
                appData.request_2 = 0;
                     
            } else if(bodyData.result == -3)
            {
                window.location.reload();
            } else {
                alert(bodyData.result_message);
                appData.request_2 = 0;
            }

        }, function(response) {
            logMessage(response.body);
            appData.request_2 = 0;
        });
    },
    getDealerJournal: function () {
        logMessage("currentPage : "+appData.currentPage);

        Vue.http.post(globalData.baseUrl + 'admin/dealerJournal', {
            "dealer_id": appData.selectDealerID,
            "dealer_num":appData.selectedDealerNum,
            "from":appData.fromDate,
            "to":appData.toDate,
            "page":appData.currentPage
        }).then(function(response) {
            logMessage(response.body);
            var bodyData = response.body;

            if (bodyData.result == 0) {

                
                appData.journalList = bodyData.data.concat();
                appData.totalPage = bodyData.sum_page;


                var pageCount = Math.ceil(appData.totalPage);
                var current = Math.ceil(appData.currentPage);


                $(".tcdPageCode1").createPage({
                    pageCount:pageCount,
                    current:current,
                    backFn:function(p){
                        appData.currentPage =p;
                        httpModule.getDealerJournal();
                    }
                });



                appData.request_3 = 0;
                     
            } else if(bodyData.result == -3)
            {
                window.location.reload();
            } else {
                alert(bodyData.result_message);
                appData.request_3 = 0;
            }

        }, function(response) {
            logMessage(response.body);
            appData.request_3 = 0;
        });
    },
    getDealerManualRechargeJournal: function () {
        logMessage("currentPage : "+appData.currentPage);

        Vue.http.post(globalData.baseUrl + 'admin/dealerManualRechargeJournal', {
            "dealer_id": appData.selectDealerID,
            "dealer_num":appData.selectedDealerNum,
            "from":appData.fromDate,
            "to":appData.toDate,
            "page":appData.currentPage
        }).then(function(response) {
            logMessage(response.body);
            var bodyData = response.body;

            if (bodyData.result == 0) {


                appData.ManualRechargeList = bodyData.data.concat();
                appData.totalPage = bodyData.sum_page;


                var pageCount = Math.ceil(appData.totalPage);
                var current = Math.ceil(appData.currentPage);


                $(".tcdPageCode10").createPage({
                    pageCount:pageCount,
                    current:current,
                    backFn:function(p){
                        appData.currentPage =p;
                        httpModule.getDealerManualRechargeJournal();
                    }
                });



                appData.request_3 = 0;

            } else if(bodyData.result == -3)
            {
                window.location.reload();
            } else {
                alert(bodyData.result_message);
                appData.request_3 = 0;
            }

        }, function(response) {
            logMessage(response.body);
            appData.request_3 = 0;
        });
    },
    saveDealerPart: function () {

        if($.trim(appData.editDealerName) == "")
        {
            alert("请填写代理商名称");
        }
        else if($.trim(appData.editDealerAccount) == "")
        {
            alert("请填写代理商账号");
        }
        else if($.trim(appData.editDealerPwd) == "")
        {
            alert("请填写代理商密码");
        }
        else if($.trim(appData.editDealerPhone) == "" || appData.editDealerPhone.length != 11)
        {
            alert("输入正确的手机号码");
        }
        /*else if($.trim(appData.editDealerPermission) == "-1")
        {
            alert("请选择用户充值权限类型");
        }*/
        else
        {
            Vue.http.post(globalData.baseUrl + 'admin/updateDealerOpt', {
                "dealer_id":appData.editDealerId,
                "name":appData.editDealerName,
                "account":appData.editDealerAccount,
                "passwd":appData.editDealerPwd,
                "phone":appData.editDealerPhone,
                //"permission": appData.editDealerPermission,
            }).then(function(response) {
                logMessage(response.body);
                var bodyData = response.body;

                if (bodyData.result == 0) {

                    alert("操作成功");
                    httpModule.getdealerList();
                    appData.editDealerPartShow = false;
                         
                } else if(bodyData.result == -3)
                {
                    window.location.reload();
                } else {
                    alert(bodyData.result_message);
                }

            }, function(response) {
                logMessage(response.body);
            });
        }
	},
	delDealerOpt: function(){
		Vue.http.post(globalData.baseUrl + 'admin/delDealerOpt', {
			"dealer_id": appData.selectDealerID
		}).then(function(response) {
			logMessage(response.body);
			var bodyData = response.body;

			if (bodyData.result == 0) {

				alert("操作成功");
				httpModule.getdealerList();
				appData.deleteInfo.isShow = false;
					 
			} else if(bodyData.result == -3)
			{
				window.location.reload();
			} else {
				alert(bodyData.result_message);
			}

		}, function(response) {
			logMessage(response.body);
		});
	},
    saveRechargeOpt: function () {

        if($.trim(appData.selectedDealerNum) == "" || $.trim(appData.selectDealerID) == "0")
        {
            alert("请选择代理商");
        }
        else if($.trim(appData.rechargeCount) == "")
        {
            alert("请输入充值数值");
        }
        else
        {
            if(window.confirm('确定要给该代理商充值'+appData.rechargeCount+'张房卡？')){
                 
                Vue.http.post(globalData.baseUrl + 'admin/dealerRechargeOpt', {
                    "dealer_id": appData.selectDealerID,
                    "dealer_num":appData.selectedDealerNum,
                    "ticket_count":appData.rechargeCount,
                    "secret":"",
                }).then(function(response) {
                    logMessage(response.body);
                    var bodyData = response.body;

                    if (bodyData.result == 0) {

                        alert("操作成功");
                        httpModule.getDealerInfo();
                        httpModule.getDealerSaleInfo();
                        httpModule.getDealerJournal();
                        appData.rechargeShow = false;
                             
                    } else if(bodyData.result == -3)
                    {
                        window.location.reload();
                    } else {
                        alert(bodyData.result_message);
                    }

                }, function(response) {
                    logMessage(response.body);
                });
            }else{
                //alert("取消");
                return false;
            }
        }
    },

    saveUserRechargeOpt: function () {

        if($.trim(appData.userRechargeName) == "" || $.trim(appData.userRechargeAid) == "")
        {
            alert("请选择要充值的用户");
        }
        else if($.trim(appData.userRechargeCount) == "")
        {
            alert("请输入充值数值");
        }
        else
        {
            if(window.confirm('确定要给该用户:' + appData.userRechargeName+ "[用户id:" + appData.userRechargeId+ ']充值' + appData.userRechargeCount+'张房卡？')){

                Vue.http.post(globalData.baseUrl + 'game/increaseAccountRoomCard', {
                    "dealer_num":appData.selectedDealerNum,
                    "account_id": appData.userRechargeAid,
                    "ticket_count":appData.userRechargeCount,
                }).then(function(response) {
                    logMessage(response.body);
                    var bodyData = response.body;

                    if (bodyData.result == 0) {

                        alert("操作成功");
                        httpModule.getActiveCount();
                        httpModule.getAccountList();
                        appData.userRechargeShow = false;

                    } else if(bodyData.result == -3)
                    {
                        window.location.reload();
                    } else {
                        alert(bodyData.result_message);
                    }

                }, function(response) {
                    logMessage(response.body);
                });
            }else{
                //alert("取消");
                return false;
            }
        }
    },

    updateGoodsList: function () {

        Vue.http.post(globalData.baseUrl + 'admin/updateGoodsList', {
            "dealer_num":appData.selectedDealerNum,
            "goodsList":appData.goodsList,
        }).then(function(response) {
            logMessage(response.body);
            var bodyData = response.body;

            if (bodyData.result == 0) {

                alert("操作成功");
                httpModule.getDealerInfo();
                appData.goodsShow = false;
                     
            } else if(bodyData.result == -3)
            {
                window.location.reload();
            } else {
                alert(bodyData.result_message);
            }

        }, function(response) {
            logMessage(response.body);
        });
    },


//////////    开局明细
	getGameList: function () {
        Vue.http.post(globalData.baseUrl + 'game/getGameList', {
        	"dealer_num":appData.selectedDealerNum
        }).then(function(response) {
            logMessage(response.body);
            var bodyData = response.body;
            if (bodyData.result == 0) {
                appData.gameList=bodyData.data.concat();
                //appData.gameDetail.type=appData.gameList[0].game_type;
        
            } else if(bodyData.result == -3)
            {
                window.location.reload();
            } else {
                alert(bodyData.result_message);
            }

        }, function(response) {
            logMessage(response.body);
        });
    },	
	getPlayCount: function () {
        Vue.http.post(globalData.baseUrl + 'game/getPlayCount', {
        	"game_type":appData.gameSearch.type,
        	"from":appData.gameSearch.from,
        	"to":appData.gameSearch.to,
        	"dealer_num":appData.selectedDealerNum,
        }).then(function(response) {
            logMessage(response.body);
            var bodyData = response.body;
            if (bodyData.result == 0) {
				appData.gameCount=bodyData.data.concat();  
				appData.balance_count= bodyData.balance;
                appData.request_3 = 0;             
            } else if(bodyData.result == -3)
            {
                appData.request_3 = 0;
                window.location.reload();
            } else {
                appData.request_3 = 0;
                alert(bodyData.result_message);
            }

        }, function(response) {
            appData.request_3 = 0;
            logMessage(response.body);
        });     
    },    
    getPlayDetailList: function () {  
        Vue.http.post(globalData.baseUrl + 'game/getPlayDetailList', {
        	"game_type":appData.gameSearch.type,
        	"from":appData.gameSearch.from,
        	"to":appData.gameSearch.to,
        	"page":appData.playDetail.page,
        	"dealer_num":appData.selectedDealerNum,
        }).then(function(response) {
            logMessage(response.body);
            var bodyData = response.body;
            if (bodyData.result == 0) {
                appData.request_2 = 0;
                appData.playDetail.data=bodyData.data.concat();                 
                appData.playDetail.total_page=parseInt(bodyData.sum_page);  
                if(appData.playDetail.total_page>1){
					 $(".tcdPageCode2").createPage({
	                    pageCount:appData.playDetail.total_page,
	                    current:appData.playDetail.page,
	                    backFn:function(p){
	                        appData.playDetail.page =p;
	                        httpModule.getPlayDetailList();
	                    }
	                });
				}               
            } else if(bodyData.result == -3)
            {
                appData.request_2 = 0;
                window.location.reload();
            } else {
                appData.request_2 = 0;
                alert(bodyData.result_message);
            }

        }, function(response) {
            logMessage(response.body);
        });
    },

    getGameScoreStat: function () {
        Vue.http.post(globalData.baseUrl + 'game/getGameScoreStat', {
        	"from":appData.scoreStat.from,
        	"to":appData.scoreStat.to,
            "aid":appData.scoreStat.aid
        }).then(function(response) {
            var bodyData = response.body;
            if (bodyData.result == 0) {
                appData.scoreStat.data=bodyData.data.concat();
                appData.scoreStat.sum=bodyData.sum;
            } else {
                alert(bodyData.msg);
            }

        }, function(response) {
            logMessage(response.body);
        });
    },
	
	getActiveCount: function () {  
        appData.request_loading = 1;
        Vue.http.post(globalData.baseUrl + 'game/getActiveCount', {
        	"dealer_num":appData.selectedDealerNum,
        }).then(function(response) {
            logMessage(response.body);
            var bodyData = response.body;
            if (bodyData.result == 0) {
            	appData.userCount.day_count=bodyData.data.day_count;
            	appData.userCount.month_count=bodyData.data.month_count;
            	appData.userCount.week_count=bodyData.data.week_count;
            	appData.userCount.total_count=bodyData.data.total_count;      
                appData.request_loading = 0;
            } else if(bodyData.result == -3)
            {
                partSelect
                window.location.reload();
            } else {
                appData.request_loading = 0;
                alert(bodyData.result_message);
            }

        }, function(response) {
            appData.request_loading = 0;
            logMessage(response.body);
        });
    },	
    getAccountList: function () {  
        appData.request_loading = 1;
        Vue.http.post(globalData.baseUrl + 'game/getAccountList', {
        	"dealer_num":appData.selectedDealerNum,
        	"keyword":appData.userSearch.searchword,
        	"page":appData.userSearch.page,
            "uid":appData.userSearch.searchid
        }).then(function(response) {
            logMessage(response.body);
            appData.request_loading = 0;
            var bodyData = response.body;
            if (bodyData.result == 0) {
            	appData.userSearch.data=bodyData.data.concat();
            	appData.userSearch.total_page=parseInt(bodyData.sum_page);
            	if(appData.userSearch.total_page>1){
					 $(".tcdPageCode3").createPage({
	                    pageCount:appData.userSearch.total_page,
	                    current:appData.userSearch.page,
	                    backFn:function(p){
	                        appData.userSearch.page =p;
	                        httpModule.getAccountList();
	                    }
	                });
				}           	   
            } else if(bodyData.result == -3)
            {
                appData.request_loading = 0;
                window.location.reload();
            } else {
                appData.request_loading = 0;
                alert(bodyData.result_message);
            }

        }, function(response) {
            appData.request_loading = 0;
            logMessage(response.body);
        });
    },    

	getRoomRound: function () {  
        Vue.http.post(globalData.baseUrl + 'game/getRoomRound', {
        	"dealer_num":appData.selectedDealerNum,
        	"room_number":appData.gameDetail.room_number1,
        	"game_type":appData.gameDetail.type1,
        }).then(function(response) {
            logMessage(response.body);
            var bodyData = response.body;
            if (bodyData.result == 0) {
            	appData.gameDetail.allRound=bodyData.data.concat();
            	if(appData.gameDetail.allRound.length>0){
					httpModule.getRoomGameResult(appData.gameDetail.allRound[0].round);
				}   
				else{
					viewMethods.showAlert(1,"房间无数据");
				}	   
            } else if(bodyData.result == -3)
            {
                window.location.reload();
            } else {
                alert(bodyData.result_message);
            }

        }, function(response) {
            logMessage(response.body);
        });
    },	

    getRoomGameResult: function (num) {  
        Vue.http.post(globalData.baseUrl + 'game/getRoomGameResult', {
        	"dealer_num":appData.selectedDealerNum,
        	"room_number":appData.gameDetail.room_number1,
        	"game_type":appData.gameDetail.type1,
        	"round":num,
        }).then(function(response) {
            logMessage(response.body);
            var bodyData = response.body;
            if (bodyData.result == 0) {
            	appData.gameDetail.eachRound=bodyData.data; 
            } else if(bodyData.result == -3)
            {
                window.location.reload();
            } else {
                alert(bodyData.result_message);
            }

        }, function(response) {
            logMessage(response.body);
        });
    },    

	queryAllGroup: function (num) {  
        Vue.http.post(globalData.baseUrl + 'guild/queryAllGroup', {
        	"dealer_num":appData.selectedDealerNum,
        	"page":appData.team.page,
        }).then(function(response) {
            var bodyData = response.body;
            if (bodyData.result == 0) {
            	appData.team.data=bodyData.data; 
            	if(appData.team.total_page>1){
					 $(".tcdPageCode5").createPage({
	                    pageCount:appData.team.total_page,
	                    current:appData.team.page,
	                    backFn:function(p){
	                        appData.team.page =p;
	                        httpModule.queryAllGroup();
	                    }
	                });
				}               
            } else if(bodyData.result == -3)
            {
                window.location.reload();
            } else {
                viewMethods.showAlert(1,bodyData.result_message);
            }

        }, function(response) {
            logMessage(response.body);
        });
    },	
	searchUser: function () {  
        Vue.http.post(globalData.baseUrl + 'guild/searchUser', {
        	"dealer_num":appData.selectedDealerNum,
        	"page":appData.team.page,
        	"phone":appData.team.phone,
        }).then(function(response) {
            var bodyData = response.body;
            if (bodyData.result == 0) {
            	appData.team.search="";
               	if(bodyData.data.length==0){
					viewMethods.showAlert(1,"未查到成员");
				}
                else{
					appData.team.search=bodyData.data[0];
					appData.team.turn=true;
				}           
            } else if(bodyData.result == -3)
            {
                window.location.reload();
            } else {
                viewMethods.showAlert(1,bodyData.result_message);
            }

        }, function(response) {
            logMessage(response.body);
        });
    },
	creatGroup: function () {  
        Vue.http.post(globalData.baseUrl + 'guild/creatGroup', {
        	"dealer_num":appData.selectedDealerNum,
        	"my_aid":globalData.my_aid,
        	
        	"president":appData.team.tempTeam.user_id,
        	"name":appData.team.tempTeam.name,
        	"profile":appData.team.tempTeam.profile,
        	"account":appData.team.tempTeam.account,
        	"password":appData.team.tempTeam.password,

        }).then(function(response) {
            var bodyData = response.body;
            if (bodyData.result == 0) {
            	appData.team.search.level=2;
               	viewMethods.hideInitTeam();
          		viewMethods.showAlert(2,"新建公会成功")
          	//	viewMethods.showAlert(2,"修改成功")
            } else if(bodyData.result == -3)
            {
                window.location.reload();
            } else {
                viewMethods.showAlert(1,bodyData.result_message);
            }

        }, function(response) {
            logMessage(response.body);
        });
    },
	editGroup: function () {  
        Vue.http.post(globalData.baseUrl + 'guild/editGroup', {
        	"dealer_num":appData.selectedDealerNum,
        	"my_aid":globalData.my_aid,
        	
        	"group_id":appData.team.tempTeam.group_id,
        	"name":appData.team.tempTeam.name,
        	"profile":appData.team.tempTeam.profile,
        	"password":appData.team.tempTeam.password,

        }).then(function(response) {
            var bodyData = response.body;
            if (bodyData.result == 0) {
            	for(var i=0;i<appData.team.data.length;i++){
					if(appData.team.tempTeam.group_id==appData.team.data[i].group_id){
						appData.team.data[i].name=appData.team.tempTeam.name;
						appData.team.data[i].profile=appData.team.tempTeam.profile;
						appData.team.data[i].password=appData.team.tempTeam.password;
					}
				}
               	viewMethods.hideInitTeam();
          		viewMethods.showAlert(2,"修改成功")
          	//	viewMethods.showAlert(2,"修改成功")
            } else if(bodyData.result == -3)
            {
                window.location.reload();
            } else {
                viewMethods.showAlert(1,bodyData.result_message);
            }

        }, function(response) {
            logMessage(response.body);
        });
    },
	deleteGroup: function () {  
        Vue.http.post(globalData.baseUrl + 'guild/deleteGroup', {
        	"dealer_num":appData.selectedDealerNum,
        	"my_aid":globalData.my_aid,   	
        	"group_id":appData.deleteInfo.id,

        }).then(function(response) {
            var bodyData = response.body;
            if (bodyData.result == 0) {
            	for(var i=0;i<appData.team.data.length;i++){
					if(appData.deleteInfo.id==appData.team.data[i].group_id){
						appData.team.data.splice(i,1);					
					}
				}
               	viewMethods.hideInitTeam();
               	viewMethods.quitDelete();
          		viewMethods.showAlert(2,"删除成功");
          	//	viewMethods.showAlert(2,"修改成功")
            } else if(bodyData.result == -3)
            {
                window.location.reload();
            } else {
                viewMethods.showAlert(1,bodyData.result_message);
            }

        }, function(response) {
            logMessage(response.body);
        });
    },

	getGuildWithdrawList: function (num) {  
        Vue.http.post(globalData.baseUrl + 'guild/getGuildWithdrawList', {
        	"dealer_num":appData.selectedDealerNum,
        	"group_id":-1,
        	"page":appData.withdrawRange.page,
        	"from":appData.withdrawRange.from,
        	"to":appData.withdrawRange.to,
        }).then(function(response) {
            var bodyData = response.body;
            if (bodyData.result == 0) {
            	appData.withdrawRange.data=bodyData.data; 
            	if(appData.withdrawRange.total_page>1){
					 $(".tcdPageCode6").createPage({
	                    pageCount:appData.withdrawRange.total_page,
	                    current:appData.withdrawRange.page,
	                    backFn:function(p){
	                        appData.withdrawRange.page =p;
	                        httpModule.getGuildWithdrawList();
	                    }
	                });
				}               
            } else if(bodyData.result == -3)
            {
                window.location.reload();
            } else {
                viewMethods.showAlert(1,bodyData.result_message);
            }

        }, function(response) {
            logMessage(response.body);
        });
    },	

	dealGuildWithdraw: function (num) {  
        Vue.http.post(globalData.baseUrl + 'guild/dealGuildWithdraw', {
        	"dealer_num":appData.selectedDealerNum,
        	"my_aid":globalData.my_aid,
        	"group_id":appData.deleteInfo.group_id,
        	"withdraw_id":appData.deleteInfo.id,
        }).then(function(response) {
            var bodyData = response.body;
            if (bodyData.result == 0) {
            	for(var i=0;i<appData.withdrawRange.data.length;i++){
					if(appData.withdrawRange.data[i].withdraw_id==appData.deleteInfo.id){
						appData.withdrawRange.data[i].status=1;
					}
				}
				viewMethods.quitDelete();
				viewMethods.showAlert(2,"处理完毕")
            //	appData.withdrawRange.data=bodyData.data; 
           
            } else if(bodyData.result == -3)
            {
                window.location.reload();
            } else {
                viewMethods.showAlert(1,bodyData.result_message);
            }

        }, function(response) {
            logMessage(response.body);
        });
    },	

	queryAllQudao: function (num) {  
        Vue.http.post(globalData.baseUrl + 'qudao/queryAllQudao', {
        	"dealer_num":appData.selectedDealerNum,
        	"page":appData.channel.page,
        }).then(function(response) {
        	appData.request_2 = 0;
            var bodyData = response.body;
            if (bodyData.result == 0) {
            	appData.channel.data=bodyData.data; 
            	if(appData.channel.total_page>1){
					 $(".tcdPageCode7").createPage({
	                    pageCount:appData.channel.total_page,
	                    current:appData.channel.page,
	                    backFn:function(p){
	                        appData.channel.page =p;
	                        httpModule.queryAllQudao();
	                    }
	                });
				}               
            } else if(bodyData.result == -3)
            {
                window.location.reload();
            } else {
                viewMethods.showAlert(1,bodyData.result_message);
            }

        }, function(response) {
            logMessage(response.body);
        });
    },	
	searchUserC: function () {  
		appData.request_3 = 1;
        Vue.http.post(globalData.baseUrl + 'qudao/searchUser', {
        	"dealer_num":appData.selectedDealerNum,
        	"page":appData.channel.page,
        	"phone":appData.channel.phone,
        }).then(function(response) {
        	appData.request_3 = 0;
            var bodyData = response.body;
            if (bodyData.result == 0) {
            	appData.channel.search="";
               	if(bodyData.data.length==0){
					viewMethods.showAlert(1,"未查到成员");
				}
                else{
					appData.channel.search=bodyData.data[0];
					appData.channel.turn=true;
				}           
            } else if(bodyData.result == -3)
            {
                window.location.reload();
            } else {
                viewMethods.showAlert(1,bodyData.result_message);
            }

        }, function(response) {
            logMessage(response.body);
        });
    },

	creatChannel: function () {  
		appData.request_3 = 1;
        Vue.http.post(globalData.baseUrl + 'qudao/creatQudao', {
        	"dealer_num":appData.selectedDealerNum,
        	
        	"account_id":appData.channel.tempChannel.account_id,
        	"name":appData.channel.tempChannel.name,
        	"account":appData.channel.tempChannel.account,
        	"password":appData.channel.tempChannel.password,

        }).then(function(response) {
        	appData.request_3 = 0;
            var bodyData = response.body;
            if (bodyData.result == 0) {
            	appData.channel.search.is_qudao=1;
               	viewMethods.hideInitChannel();
          		viewMethods.showAlert(2,"新建渠道成功")
          	//	viewMethods.showAlert(2,"修改成功")
            } else if(bodyData.result == -3)
            {
                window.location.reload();
            } else {
                viewMethods.showAlert(1,bodyData.result_message);
            }

        }, function(response) {
            logMessage(response.body);
        });
    },
	editChannel: function () {  
		appData.request_3 = 1;
		if(!appData.deleteInfo.isShow){
	        Vue.http.post(globalData.baseUrl + 'qudao/editQudao', {
	        	"dealer_num":appData.selectedDealerNum,
	        	
	        	"qudao_id":appData.channel.tempChannel.qudao_id,
	        	"name":appData.channel.tempChannel.name,
	        	"password":appData.channel.tempChannel.password,
	        	"is_delete":0,
	        }).then(function(response) {
	        	appData.request_3 = 0;
	            var bodyData = response.body;
	            if (bodyData.result == 0) {
	            	for(var i=0;i<appData.channel.data.length;i++){
						if(appData.channel.tempChannel.qudao_id==appData.channel.data[i].qudao_id){
							appData.channel.data[i].name=appData.channel.tempChannel.name;
							appData.channel.data[i].password=appData.channel.tempChannel.password;
						}
					}
	               	viewMethods.hideInitChannel();
	          		viewMethods.showAlert(2,"修改成功")
	          	//	viewMethods.showAlert(2,"修改成功")
	            } else if(bodyData.result == -3)
	            {
	                window.location.reload();
	            } else {
	                viewMethods.showAlert(1,bodyData.result_message);
	            }

	        }, function(response) {
	            logMessage(response.body);
	        });			
		}
		else{
			 Vue.http.post(globalData.baseUrl + 'qudao/editQudao', {
	        	"dealer_num":appData.selectedDealerNum, 	
	        	"qudao_id":appData.deleteInfo.id,
	        	"name":"",
	        	"password":"",
	        	"is_delete":1,

	        }).then(function(response) {
	        	appData.request_3 = 0;
	            var bodyData = response.body;
	            if (bodyData.result == 0) {
	            	for(var i=0;i<appData.channel.data.length;i++){
						if(appData.deleteInfo.id==appData.channel.data[i].qudao_id){
							appData.channel.data.splice(i,1);					
						}
					}
	               	viewMethods.hideInitChannel();
	               	viewMethods.quitDelete();
	          		viewMethods.showAlert(2,"删除成功");
	          	//	viewMethods.showAlert(2,"修改成功")
	            } else if(bodyData.result == -3)
	            {
	                window.location.reload();
	            } else {
	                viewMethods.showAlert(1,bodyData.result_message);
	            }

	        }, function(response) {
	            logMessage(response.body);
	        });
		}

    },
	
	getQudaolist: function () {  
        Vue.http.post(globalData.baseUrl + 'qudao/getQudaolist', {
        	"dealer_num":appData.selectedDealerNum,
        }).then(function(response) {
        	appData.request_2 = 0;
            var bodyData = response.body;
            if (bodyData.result == 0) {
            	appData.channelUser.qudaoList=bodyData.data.concat();
				setTimeout(function(){
					$("#qudaoList").val(-1);
				},1)
            } else if(bodyData.result == -3)
            {
                window.location.reload();
            } else {
                viewMethods.showAlert(1,bodyData.result_message);
            }

        }, function(response) {
            logMessage(response.body);
        });
    },	

    getQudaoInfo: function () {  
    	appData.request_3 = 1;
        Vue.http.post(globalData.baseUrl + 'qudao/getQudaoInfo', {
        	"dealer_num":appData.selectedDealerNum,
        	"qudao_id":$("#qudaoList").val(),
        	"from":appData.channelUser.from,
        	"to":appData.channelUser.to,
        	"page":appData.channelUser.page,
        	"amount_condition":appData.channelUser.recharge,
        	"count_condition":appData.channelUser.round,
        }).then(function(response) {
        	appData.request_3 = 0;
            var bodyData = response.body;
            if (bodyData.result == 0) {
            	appData.channelUser.data=[];          	
            	appData.channelUser.day_count=0;
            	appData.channelUser.month_count=0;
            	appData.channelUser.total_count=0;
            	appData.channelUser.week_count=0;	
            	
            	
            	appData.channelUser.data=bodyData.data.detail_array.concat();          	
            	appData.channelUser.day_count=bodyData.data.day_user_count;
            	appData.channelUser.month_count=bodyData.data.month_user_count;
            	appData.channelUser.total_count=bodyData.data.user_count;
            	appData.channelUser.week_count=bodyData.data.week_user_count;	
            		
            	appData.channelUser.total_page=bodyData.sum_page;		
            	
            	 if(appData.channelUser.total_page>1){
					 $(".tcdPageCode8").createPage({
	                    pageCount:appData.channelUser.total_page,
	                    current:appData.channelUser.page,
	                    backFn:function(p){
	                        appData.channelUser.page =p;
	                        httpModule.getQudaoInfo();
	                    }
	                });
				}    
            } else if(bodyData.result == -3)
            {
                window.location.reload();
            } else {
                viewMethods.showAlert(1,bodyData.result_message);
            }

        }, function(response) {
            logMessage(response.body);
        });
    },    


	searchAccountRoomCard: function () {  
    	appData.request_3 = 1;
        Vue.http.post(globalData.baseUrl + 'game/searchAccountRoomCard', {
        	"dealer_num":appData.selectedDealerNum,
        	"page":appData.roomCard.page,
        	"keyword":appData.roomCard.search,
        }).then(function(response) {
        	appData.request_3 = 0;
            var bodyData = response.body;
            if (bodyData.result == 0) {
            	appData.roomCard.data=[];          	
				appData.roomCard.total_page=bodyData.sum_page;
				appData.roomCard.data=bodyData.data.concat();
            	if(appData.roomCard.total_page>1){
					 $(".tcdPageCode9").createPage({
	                    pageCount:appData.roomCard.total_page,
	                    current:appData.roomCard.page,
	                    backFn:function(p){
	                        appData.roomCard.page =p;
	                        httpModule.searchAccountRoomCard();
	                    }
	                });
				}    
            } else if(bodyData.result == -3)
            {
                window.location.reload();
            } else {
                viewMethods.showAlert(1,bodyData.result_message);
            }

        }, function(response) {
            logMessage(response.body);
        });
    },	
    deductAccountRoomCard: function () {  
    	appData.request_3 = 1;
        Vue.http.post(globalData.baseUrl + 'game/deductAccountRoomCard', {
        	"dealer_num":appData.selectedDealerNum,
        	"account_id":appData.roomCard.deleteInfo.id,
        	"count":appData.roomCard.deleteInfo.deleteNum,
        }).then(function(response) {
        	appData.request_3 = 0;
            var bodyData = response.body;
            if (bodyData.result == 0) {
            	for(var i=0;i<appData.roomCard.data.length;i++){
					if(appData.roomCard.data[i].account_id==appData.roomCard.deleteInfo.id){
						appData.roomCard.data[i].ticket_count=appData.roomCard.data[i].ticket_count-appData.roomCard.deleteInfo.deleteNum;
						if(appData.roomCard.data[i].ticket_count<0)
							appData.roomCard.data[i].ticket_count=0;
					}
				}	
				viewMethods.hideCancelCard();
				viewMethods.showAlert(2,"扣除成功");
            } else if(bodyData.result == -3)
            {
                window.location.reload();
            } else {
                viewMethods.showAlert(1,bodyData.result_message);
            }

        }, function(response) {
            logMessage(response.body);
        });
    },    


///公告和消息
	getAnnList: function () {  
    	appData.request_3 = 1;
        Vue.http.post(globalData.baseUrl + 'noty/getAnnList', {
        	"page":appData.ad.page,       	
        }).then(function(response) {
        	appData.request_3 = 0;
            var bodyData = response.body;
            if (bodyData.result == 0) {
				appData.ad.data=[];
            	appData.ad.total_page=bodyData.sum_page;
            	appData.ad.data=bodyData.data.concat();
            	for(var i=0;i<appData.ad.data.length;i++){
					appData.ad.data[i].content=utf8to16(base64_decode(appData.ad.data[i].content));
				}
            	
            	if(appData.ad.total_page>1){
					 $(".tcdPageCodeAd").createPage({
	                    pageCount:appData.ad.total_page,
	                    current:appData.ad.page,
	                    backFn:function(p){
	                        appData.ad.page =p;
	                        httpModule.getAnnList();
	                    }
	                });
				}
            } else if(bodyData.result == -3)
            {
                window.location.reload();
            } else {
                viewMethods.showAlert(1,bodyData.result_message);
            }
        }, function(response) {
            logMessage(response.body);
        });
    },	
    sendAnnOpt: function () {  
    	appData.request_3 = 1;
        Vue.http.post(globalData.baseUrl + 'noty/sendAnnOpt', {
        	"content":appData.ad.content,
        	"second":appData.ad.second,
        	"start_time":appData.ad.start_time.format,
        	"end_time":appData.ad.end_time.format,

        }).then(function(response) {
        	appData.request_3 = 0;
            var bodyData = response.body;
            if (bodyData.result == 0) {
            	viewMethods.showAlert(2,"发送成功");
				httpModule.getAnnList();
            } else if(bodyData.result == -3)
            {
                window.location.reload();
            } else {
                viewMethods.showAlert(1,bodyData.result_message);
            }
        }, function(response) {
            logMessage(response.body);
        });
    },	
    updateAnnStatusOpt: function () {  
    	appData.request_3 = 1;
        Vue.http.post(globalData.baseUrl + 'noty/updateAnnStatusOpt', {
        	"data_id":appData.deleteInfo.id,      	
        	"status":2,      	
        }).then(function(response) {
        	appData.request_3 = 0;
            var bodyData = response.body;
            if (bodyData.result == 0) {
				viewMethods.quitDelete();
				viewMethods.showAlert(2,"关闭成功");
				httpModule.getAnnList();
            } else if(bodyData.result == -3)
            {
                window.location.reload();
            } else {
                viewMethods.showAlert(1,bodyData.result_message);
            }
        }, function(response) {
            logMessage(response.body);
        });
    },

    sendMessage: function () {  
    	appData.request_3 = 1;
        Vue.http.post(globalData.baseUrl + 'noty/sendMessage', {
        	"dealer_num":appData.selectedDealerNum,
        	"account_id":appData.noty.id,      	
        	"content":appData.noty.content,      	
        }).then(function(response) {
        	appData.request_3 = 0;
            var bodyData = response.body;
            if (bodyData.result == 0) {
            	viewMethods.hideNoty();
				viewMethods.showAlert(2,"发送成功");
            } else if(bodyData.result == -3)
            {
                window.location.reload();
            } else {
                viewMethods.showAlert(1,bodyData.result_message);
            }
        }, function(response) {
            logMessage(response.body);
        });
    },

///代理商
	searchAgentList: function () {  
        Vue.http.post(globalData.baseUrl + 'agent/searchAgentList', {
        	"dealer_num":appData.selectedDealerNum,
        	"page":appData.agent.page,
        	"keyword":appData.agent.search,
        }).then(function(response) {
            var bodyData = response.body;
            if (bodyData.result == 0) {
            	appData.agent.data=[];          	
				appData.agent.total_page=bodyData.sum_page;
				appData.agent.data=bodyData.data.concat();
            	if(appData.agent.total_page>1){
					 $(".tcdPageCode11").createPage({
	                    pageCount:appData.agent.total_page,
	                    current:appData.agent.page,
	                    backFn:function(p){
	                        appData.agent.page =p;
	                        httpModule.searchAgentList();
	                    }
	                });
				}    	
            } else if(bodyData.result == -3)
            {
                window.location.reload();
            } else {
                viewMethods.showAlert(1,bodyData.result_message);
            }

        }, function(response) {
            logMessage(response.body);
        });
    },	
	bindAgentOpt: function () {  
        Vue.http.post(globalData.baseUrl + 'agent/bindAgentOpt', {
        	"dealer_num":appData.selectedDealerNum,
        	"account_id":appData.deleteInfo.id,
        }).then(function(response) {
            var bodyData = response.body;
            if (bodyData.result == 0) {
            	for(var i=0;i<appData.agent.data.length;i++){
					if(appData.agent.data[i].account_id==appData.deleteInfo.id){
						appData.agent.data[i].is_agent=1;
					}
				}	
				viewMethods.showAlert(2,"绑定成功");
				viewMethods.quitDelete();
            } else if(bodyData.result == -3)
            {
                window.location.reload();
            } else {
                viewMethods.showAlert(1,bodyData.result_message);
            }

        }, function(response) {
            logMessage(response.body);
        });
    },		
    unbindAgentOpt: function () {  
        Vue.http.post(globalData.baseUrl + 'agent/unbindAgentOpt', {
        	"dealer_num":appData.selectedDealerNum,
        	"account_id":appData.deleteInfo.id,
        }).then(function(response) {
            var bodyData = response.body;
            if (bodyData.result == 0) {
            	for(var i=0;i<appData.agent.data.length;i++){
					if(appData.agent.data[i].account_id==appData.deleteInfo.id){
						appData.agent.data[i].is_agent=0;
					}
				}	
				viewMethods.showAlert(2,"解除绑定成功");
				viewMethods.quitDelete();
            } else if(bodyData.result == -3)
            {
                window.location.reload();
            } else {
                viewMethods.showAlert(1,bodyData.result_message);
            }

        }, function(response) {
            logMessage(response.body);
        });
    },	    


	getSlotSummary: function () {  
        Vue.http.post(globalData.baseUrl + 'fruit/getSlotSummary', {
        	"dealer_num":appData.selectedDealerNum,
        	"from":appData.fruit.from,
        	"to":appData.fruit.to,
        }).then(function(response) {
            var bodyData = response.body;
            if (bodyData.result == 0) {
				appData.fruit.balance=bodyData.data.balance;
				appData.fruit.sum_bet=bodyData.data.sum_bet;
				appData.fruit.sum_reward=bodyData.data.sum_reward;

            } else if(bodyData.result == -3)
            {
                window.location.reload();
            } else {
                viewMethods.showAlert(1,bodyData.result_message);
            }

        }, function(response) {
            logMessage(response.body);
        });
    },		
    getSlotList: function () {  
        Vue.http.post(globalData.baseUrl + 'fruit/getSlotList', {
        	"dealer_num":appData.selectedDealerNum,
        	"from":appData.fruit.from,
        	"to":appData.fruit.to,
        	"page":appData.fruit.page,
        }).then(function(response) {
            var bodyData = response.body;
            if (bodyData.result == 0) {
            	appData.fruit.data=[];          	
				appData.fruit.total_page=bodyData.sum_page;
				appData.fruit.data=bodyData.data.concat();
            	if(appData.fruit.total_page>1){
					 $(".tcdPageCode12").createPage({
	                    pageCount:appData.fruit.total_page,
	                    current:appData.fruit.page,
	                    backFn:function(p){
	                        appData.fruit.page =p;
	                        httpModule.getSlotList();
	                    }
	                });
				}    
            } else if(bodyData.result == -3)
            {
                window.location.reload();
            } else {
                viewMethods.showAlert(1,bodyData.result_message);
            }

        }, function(response) {
            logMessage(response.body);
        });
    },

    getDomainInfo : function () {
        Vue.http.post(globalData.baseUrl + 'domain/get_info', {

        }).then(function(response) {
            var data = response.body;
            appData.domainInfo.now = data.now;
            appData.domainInfo.list = data.list;
            appData.domainInfo.domains = data.domains;
            appData.domainInfo.black   = data.black;
            appData.domainInfo.callback = data.callback;
            appData.domainInfo.auto    = data.auto;
        }, function(response) {
            logMessage(response.body);
        });
    },

    getNotice : function () {
        Vue.http.post(globalData.baseUrl + 'notice/info', {})
            .then(function(response) {
            var data = response.body;
            appData.maintainList = data.maintain_list;
            appData.broadcastList = data.broadcast_list;

        }, function(response) {
            logMessage(response.body);
        });
    }
}


var viewMethods = {
	sendNoty:function(){
		
		if(appData.ad.start_time.hour==""||appData.ad.start_time.minute==""||appData.ad.end_time.hour==""||appData.ad.end_time.minute==""){
			viewMethods.showAlert(1,"时间未填完整");
		}
		else if (  !isNum(appData.ad.start_time.hour) || appData.ad.start_time.hour>24||!isNum(appData.ad.start_time.minute) || appData.ad.start_time.minute>60||!isNum(appData.ad.end_time.hour) || appData.ad.end_time.hour>24||!isNum(appData.ad.end_time.minute) || appData.ad.end_time.minute>60){
			viewMethods.showAlert(1,"时间不正确");
		}
		else if(!isNum(appData.ad.second)||appData.ad.second==0){
			viewMethods.showAlert(1,"时长须为正整数");
		}
		else if(appData.ad.content1==""){
			viewMethods.showAlert(1,"请填写公告内容");
		}
		else{
			appData.ad.content=base64_encode(utf16to8(appData.ad.content1));
			appData.ad.start_time.format=timeChange(appData.ad.start_time.date+" "+appData.ad.start_time.hour+":"+appData.ad.start_time.minute);
			appData.ad.end_time.format=timeChange(appData.ad.end_time.date+" "+appData.ad.end_time.hour+":"+appData.ad.end_time.minute);			
			httpModule.sendAnnOpt();
		}
	},
	editAd:function(){
		httpModule.getAnnList();
		appData.ad.isShow=true;
	},
	
	showNoty:function(id){
		appData.noty.id=id;		
		appData.noty.detail1="";		
		appData.noty.isShow=true;		
	},	
	hideNoty:function(){
		appData.noty.isShow=false;		
	},	
	sendMessage:function(){
		if(appData.noty.content1==""){
			viewMethods.showAlert(1,"请编辑通知内容");
		}
		else{
			appData.noty.content=base64_encode(utf16to8(appData.noty.content1));
			httpModule.sendMessage();	
		}	
	},
	

    showQuitTable: function () {
        window.location.href = globalData.baseUrl + "admin/logout";
        
    },
    selectDealer: function (obj) {
		appData.ad.isShow=false;


        appData.request_1 = 1;
        appData.request_2 = 1;
        appData.request_3 = 1;

        appData.selectDealerID=obj.dealer_id;
        appData.selectedDealerNum = obj.dealer_num;
        appData.selectDealerName = obj.name;
        appData.selectedDealerAccount=obj.account;
        appData.selectedDealerPhone=obj.phone;
        appData.selectedDealerIsAdmin=obj.is_admin;

 //       httpModule.getDealerInfo();
 //       httpModule.getDealerSaleInfo();
 //       httpModule.getDealerJournal();

		viewMethods.partSelect(2);	
		
		httpModule.getGameList();		
		$("#gameSelect1").val(0);
		appData.playDetail.data=[];
		appData.playDetail.page=1;
		appData.playDetail.total_page=1;

		
		appData.userSearch.keyword="";
		appData.userSearch.is_null=true;
		appData.userSearch.page=1;
		appData.userSearch.total_page=1;
		appData.userSearch.data=[];
		
		appData.gameDetail.room_number="";
		appData.gameDetail.is_num=false;
		appData.gameDetail.allRound=[];
		appData.gameDetail.eachRound="";
		appData.gameDetail.type=-1;
        setTimeout(function() {
            $("#gameResultList").val(-1);
        }, 1);
        
		
		appData.team.data=[];
		appData.team.page=1;
		appData.team.total_page=1;
		appData.team.turn=false;
		appData.team.phone="";
		appData.team.search="";
		
		appData.withdrawRange.data=[];
		appData.withdrawRange.page=1;
		appData.withdrawRange.total_page=1;
		
		appData.channel.data=[];
		appData.channel.page=1;
		appData.channel.total_page=1;
		appData.channel.turn=false;
		appData.channel.phone="";
		appData.channel.search="";
		
		appData.channelUser.turn=false;
		appData.channelUser.page=1;
		appData.channelUser.total_page=1;
		appData.channelUser.data=[];
		appData.channelUser.qudaoList=[];
		setTimeout(function() {
            $("#qudaoList").val(-1);
        }, 1);

		appData.roomCard.data=[];
		appData.roomCard.page=1;
		appData.roomCard.total_page=1;
		appData.roomCard.search="";
		appData.roomCard.search1="";		
		
		appData.agent.data=[];
		appData.agent.page=1;
		appData.agent.total_page=1;
		appData.agent.search="";
		appData.agent.search1="";
		
	
        logMessage(appData.selectedDealerNum);


		appData.cardManage.type=1;
		appData.cardManage.search="";
		appData.cardManage.search1="";
		appData.cardManage.page=1;
		appData.cardManage.total_page=1;
		appData.cardManage.data=[];
    },

    searchDealerOpt: function () {
        appData.searchKeyword=$("#searchKeyword").val();
        httpModule.getdealerList();
    },
    editDealerAccountBtn: function () {
        appData.editDealerTitle = "编辑代理商账号";
        appData.editDealerId = appData.selectDealerID;
        appData.editDealerName = appData.selectDealerName;
        appData.editDealerAccount = appData.selectedDealerAccount;
        appData.editDealerPhone = appData.selectedDealerPhone;
        appData.editDealerPwd = "";
        //appData.editDealerPermission = appData.editDealerPermission;
        
        //$("#permissionType").find("option[value='"+appData.editDealerPermission+"']").attr("selected",true);

    		appData.editDealerPartShow = true;
    		if(+appData.selectedDealerIsAdmin){
    			$('.infoItem:eq(1) input').attr('disabled', true);
    		} else {
          $('.infoItem:eq(1) input').attr('disabled', false);
        }
    },
    addDealerOpt: function () {
        appData.editDealerTitle = "添加代理商账号";
        appData.editDealerId = "-1";
        appData.editDealerName = "";
        appData.editDealerAccount = "";
        appData.editDealerPwd = "";
        appData.editDealerPhone = "";
       //appData.editDealerPermission = -1;
        appData.editDealerPartShow = true;
        $('.infoItem:eq(1) input').attr('disabled', false);
    },
    closeDealerPart:function () {
        appData.editDealerPartShow = false;
    },
    saveDealerPart:function () {
        httpModule.saveDealerPart();
    },

    saveRechargeOpt:function () {
        httpModule.saveRechargeOpt();
    },
    openRechargePart:function () {
        appData.rechargeCount = 0;
        appData.rechargeSecret = "";
        appData.rechargeShow = true;
    },
    closeRechargePart:function () {
        appData.rechargeCount = 0;
        appData.rechargeSecret = "";
        appData.rechargeShow = false;
    },

    saveUserRechargeOpt:function () {
        httpModule.saveUserRechargeOpt();
    },
    openUserRechargePart:function (user, user_code, account_id) {
        appData.userRechargeCount = 0;
        appData.userRechargeName = user;
        appData.userRechargeId = user_code;
        appData.userRechargeAid = account_id;

        appData.userRechargeShow = true;
    },
    closeUserRechargePart:function () {
        appData.userRechargeCount = 0;
        appData.userRechargeName = "";
        appData.userRechargeId = 0;
        appData.userRechargeAid = 0;
        appData.userRechargeShow = false;
    },

    chooseDate:function (from,to) {
        logMessage(from);
        logMessage(to);

        appData.request_2 = 1;
        appData.request_3 = 1;

        appData.fromDate  = from;
        appData.toDate  = to;

        if(appData.selectedDealerNum != "")
        {
            httpModule.getDealerSaleInfo();
            httpModule.getDealerJournal();
        }
        
    },


    openGoodsList:function () {
        appData.goodsShow = true;
    },
    closeGoodsList:function () {
        appData.goodsShow = false;
    },
    updateGoodsList:function () {
        httpModule.updateGoodsList();
    },



    showAlert:function(type,text){
		appData.alert.text=text;
		appData.alert.type=type;
		appData.alert.isShow=true;
		setTimeout(function(){appData.alert.isShow=false;},1500)
	},
	
	partSelect:function(num){
		appData.part = num;
        httpModule.getDealerInfo();
		if(num==1){
	        httpModule.getDealerSaleInfo();
          appData.currentPage = 1;
	        if (appData.roomCardPart == 1){
                httpModule.getDealerJournal();
            }else{
	            httpModule.getDealerManualRechargeJournal();
            }

		}
		else if(num==2){
			httpModule.getPlayCount();
			httpModule.getPlayDetailList();
           
		}
		else if(num==3){
            appData.userSearch.searchword = '';
            appData.userSearch.page = 1;
			httpModule.getActiveCount();
             httpModule.getAccountList();
		}
		else if(num==4){
			
		}
		else if(num==5){
			httpModule.queryAllGroup();
		}
		else if(num==6){
			httpModule.getGuildWithdrawList();
		}
		else if(num==7){
			appData.channel.page=1;
			appData.request_2 = 1;
			httpModule.queryAllQudao();
		}
		else if(num==8){
			appData.request_2 = 1;
			appData.channelUser.data=[];          	
        	appData.channelUser.day_count=0;
        	appData.channelUser.month_count=0;
        	appData.channelUser.total_count=0;
        	appData.channelUser.week_count=0;	
			httpModule.getQudaolist();
		}		
		else if(num==10){
			httpModule.getExchangeSummary();
			httpModule.getExchangeTicketList();
		}
		else if(num==11){
			httpModule.searchAgentList();
		}
		else if(num==12){
			httpModule.getSlotSummary();
			httpModule.getSlotList();
		}
		else if(num == 13){
		    httpModule.getDomainInfo();
        }else if(num == 14){
		    httpModule.getNotice();
        }

	},

    roomCardPartSelect:function(num){
        appData.roomCardPart = num;
        httpModule.getDealerInfo();
        if(num==1){
            appData.currentPage = 1;
            httpModule.getDealerSaleInfo();
            httpModule.getDealerJournal();
        }
        else if(num==2){
            appData.currentPage = 1;
            httpModule.getDealerSaleInfo();
            httpModule.getDealerManualRechargeJournal();

        }

    },
	
	searchUser:function(){
		appData.userSearch.page=1;
		appData.userSearch.total_page=1;
		appData.userSearch.searchword=appData.userSearch.keyword;
        appData.userSearch.searchid=appData.userSearch.uid;
		httpModule.getAccountList();
	},
	
	showDetail:function(aid){
	    appData.scoreStat.aid = aid;
	    httpModule.getGameScoreStat();
		appData.userSearch.isShowDetail=true;
	},	
	hideDetail:function(type){
	    type = type || false;
        if (type) return;
		appData.userSearch.isShowDetail=false;		
	},

	searchGame:function(){
		appData.gameDetail.room_number1=appData.gameDetail.room_number;
		appData.gameDetail.type1=appData.gameDetail.type;
		appData.gameDetail.allRound=[];
		appData.gameDetail.eachRound="";
		
		httpModule.getRoomRound();
	},	
	
	showInitTeam:function(id,user,name){
		appData.team.isInitTeam=true;
		if(id>0){
			for(var i=0;i<appData.team.data.length;i++){
				if(id==appData.team.data[i].group_id){
					appData.team.tempTeam.name=appData.team.data[i].name;
					appData.team.tempTeam.account=appData.team.data[i].account;
					appData.team.tempTeam.password=appData.team.data[i].password;
					appData.team.tempTeam.profile=appData.team.data[i].profile;
					appData.team.tempTeam.group_id=appData.team.data[i].group_id;
					appData.team.tempTeam.president_name=appData.team.data[i].president_name;
					appData.team.tempTeam.user_id=0;
				}
			}
		}
		else{
			appData.team.tempTeam.name="";
			appData.team.tempTeam.account="";
			appData.team.tempTeam.password="";
			appData.team.tempTeam.profile="";
			appData.team.tempTeam.group_id=0;
			appData.team.tempTeam.president_name=name;
			appData.team.tempTeam.user_id=user;
		}
		
	},	
	hideInitTeam:function(){
		appData.team.isInitTeam=false;
	},
	editTeam:function(id){
		var reg = /^[0-9a-zA-Z]+$/;

		if(appData.team.tempTeam.name==""){
			viewMethods.showAlert(1,"请填写公会名");
		}
		else if(appData.team.tempTeam.account==""){
			viewMethods.showAlert(1,"请填写公账号");
		}			
		else if(appData.team.tempTeam.password.length<6||appData.team.tempTeam.password.length>18){
			viewMethods.showAlert(1,"请输入6~18位密码");
		}

		else{
			if(id>0){
				httpModule.editGroup();
			}
			else{
				httpModule.creatGroup();
			}	
		}

	},	
	deleteCommit:function(type,id,group_id){
		appData.deleteInfo.id=id;
		appData.deleteInfo.group_id=group_id;
		appData.deleteInfo.type=type;
		appData.deleteInfo.isShow=true;
		if(type==1){
			appData.deleteInfo.text="是否删除公会？";
		}			
		else if(type==2){
			appData.deleteInfo.text="是否处理完毕？";
		}			
		else if(type==3){
			appData.deleteInfo.text="是否删除代理商？";
		}	
		else if(type==4){
			appData.deleteInfo.text="是否删除渠道？";
		}	
		else if(type==5){
			appData.deleteInfo.text="是否关闭通知？";
		}			
		else if(type==6){
			appData.deleteInfo.text="是否绑定直营代理？";
		}			
		else if(type==7){
			appData.deleteInfo.text="是否取消绑定？";
		}	
	},		
	deleteSubmit:function(){
		if(appData.deleteInfo.type==1){
			httpModule.deleteGroup();
		}
		else if(appData.deleteInfo.type==2){
			httpModule.dealGuildWithdraw();
		}		
		else if(appData.deleteInfo.type==3){
			httpModule.delDealerOpt();
		}
		else if(appData.deleteInfo.type==4){
			httpModule.editChannel();
		}
		else if(appData.deleteInfo.type==5){
			httpModule.updateAnnStatusOpt();
		}		
		else if(appData.deleteInfo.type==6){
			httpModule.bindAgentOpt();
		}		
		else if(appData.deleteInfo.type==7){
			httpModule.unbindAgentOpt();
		}
	},	
	quitDelete:function(){
		appData.deleteInfo.isShow=false;
	},	
	searchPerson:function(type){
		if(type==1){
			if(!(/^1[3|4|5|7|8]\d{9}$/.test(appData.team.phone))){
				viewMethods.showAlert(1,"手机号错误")
			}
			else{
				httpModule.searchUser();
			}		
		}
		else if(type==2){
			if(!(/^1[3|4|5|7|8]\d{9}$/.test(appData.channel.phone))){
				viewMethods.showAlert(1,"手机号错误")
			}
			else{
				httpModule.searchUserC();
			}
		}
	},
	turn:function(type){
		if(type==5){
			appData.team.phone="";
			appData.team.turn=false;
			httpModule.queryAllGroup();			
		}
		else if(type==7){
			appData.channel.phone="";
			appData.channel.turn=false;
			httpModule.queryAllQudao();			
		}		
	},


	showInitChannel:function(id,user,name){
		appData.channel.isInitChannel=true;
		if(id>0){
			for(var i=0;i<appData.channel.data.length;i++){
				if(id==appData.channel.data[i].qudao_id){
					appData.channel.tempChannel.name=appData.channel.data[i].name;
					appData.channel.tempChannel.account=appData.channel.data[i].account;
					appData.channel.tempChannel.password=appData.channel.data[i].password;

					appData.channel.tempChannel.qudao_id=appData.channel.data[i].qudao_id;
					appData.channel.tempChannel.header=appData.channel.data[i].header;
					appData.channel.tempChannel.account_id=0;
				}
			}
		}
		else{
			appData.channel.tempChannel.name="";
			appData.channel.tempChannel.account="";
			appData.channel.tempChannel.password="";
			appData.channel.tempChannel.qudao_id=0;
			appData.channel.tempChannel.header=name;
			appData.channel.tempChannel.account_id=user;
		}
		
	},	
	hideInitChannel:function(){
		appData.channel.isInitChannel=false;
	},
	editChannel:function(id){
		var reg = /^[0-9a-zA-Z]+$/;

		if(appData.channel.tempChannel.name==""){
			viewMethods.showAlert(1,"请填写渠道名");
		}
		else if(appData.channel.tempChannel.account==""){
			viewMethods.showAlert(1,"请填写公账号");
		}			
		else if(appData.channel.tempChannel.password.length<6||appData.channel.tempChannel.password.length>18){
			viewMethods.showAlert(1,"请输入6~18位密码");
		}

		else{
			if(id>0){
				httpModule.editChannel();
			}
			else{
				httpModule.creatChannel();
			}	
		}

	},	

	selectUser:function(){
		if(!(isNum(appData.channelUser.recharge1))){
			viewMethods.showAlert(1,"充值须为非负整数")
		}
		else if(!(isNum(appData.channelUser.round1))){
			viewMethods.showAlert(1,"局数须为非负整数")
		}
		else{
			appData.channelUser.from=appData.channelUser.from1;
			appData.channelUser.to=appData.channelUser.to1;
			appData.channelUser.recharge=appData.channelUser.recharge1;
			appData.channelUser.round=appData.channelUser.round1;
			appData.channelUser.page=1;
			appData.channelUser.total_page=1;
			httpModule.getQudaoInfo();
		}
	},
	
	
	cardCancel:function(id,total){
		appData.roomCard.deleteInfo.is_show=true;
		appData.roomCard.deleteInfo.id=id;
		appData.roomCard.deleteInfo.totalNum=total;
		appData.roomCard.deleteInfo.deleteNum=0;
	},		
	hideCancelCard:function(){
		appData.roomCard.deleteInfo.is_show=false;
	},	
	cardFrom:function(id,name){
		window.open(globalData.baseUrl + "admin/roomCard?dealer_num="+appData.selectedDealerNum+"&id="+id+"&name="+base64_encode(utf16to8(name)))
	},		
	cardTo:function(id,name){
		window.open(globalData.baseUrl + "admin/customerCard?dealer_num="+appData.selectedDealerNum+"&id="+id+"&name="+base64_encode(utf16to8(name)))
	},	
	searchCardInfo:function(){
		if(appData.roomCard.search1==""){
			viewMethods.showAlert(1,"请填写关键词");
		}
		else{
			appData.roomCard.search=appData.roomCard.search1;
			appData.roomCard.page=1;
			appData.roomCard.total_page=1;	
			httpModule.searchAccountRoomCard();
		}	
	},
	cancelCardCommit:function(){
		if(!(isNum(appData.roomCard.deleteInfo.deleteNum))||appData.roomCard.deleteInfo.deleteNum==0){
			viewMethods.showAlert(1,"扣除数需为正整数");
		}
//		else if(appData.roomCard.deleteInfo.deleteNum>appData.roomCard.deleteInfo.totalNum){
//			viewMethods.showAlert(1,"扣除房卡数过多");
//		}
		else{
			httpModule.deductAccountRoomCard();
		}
	},	


	cardManageSelect:function(num){
		appData.cardManage.type=num;
		appData.cardManage.page=1;
		appData.cardManage.total_page=1;
		httpModule.getExchangeTicketList();
	},	
	cardManageSearch:function(){
		appData.cardManage.page=1;
		appData.cardManage.total_page=1;
		appData.cardManage.search=appData.cardManage.search1;
		httpModule.getExchangeTicketList();
	},

	showCreateCode:function(){
		appData.cardManage.is_show=true;
		appData.cardManage.tempInfo.num=1;
		appData.cardManage.tempInfo.pwd="";
		$("#createCodeSelect").val("-1");
	},	
	closeCreateCode:function(){
		appData.cardManage.is_show=false;
	},	
	createCodeCommit:function(){
		if($("#createCodeSelect").val()==-1){
			viewMethods.showAlert(1,"请选择套餐类型");
		}
		else if(!(isNum(appData.cardManage.tempInfo.num))||appData.cardManage.tempInfo.num==0){
			viewMethods.showAlert(1,"数量需为正整数");
		}
		else if(appData.cardManage.tempInfo.pwd==""){
			viewMethods.showAlert(1,"请输入密码");
		}
		else{
			httpModule.createExchangeCode();
		}
	},

	gameResult:function(){
		window.open("../gameview/index?dealer_num="+appData.selectedDealerNum+"&room_number="+appData.gameDetail.room_number1+"&game_type="+appData.gameDetail.type1+"&round=1")
	},

	searchAgent:function(){
		appData.agent.search=appData.agent.search1;
		appData.agent.page=1;
		appData.agent.total_page=1;	
		httpModule.searchAgentList();
	},

    changeDomain : function (domain) {
        Vue.http.post(globalData.baseUrl + 'domain/change_domain', {
            "domain":domain,
        }).then(function(response) {
            var data = response.body;
            if(typeof(data) === "object"){
                appData.domainInfo.now = data.domain;
                viewMethods.showAlert(1,'成功');
            }else{
                viewMethods.showAlert(1,data);
            }
        }, function(response) {
            logMessage(response.body);
        });
    }
	
}

var appData = {
    adminshow:globalData.super==1?true:false,
	fruit:{
		data:[],
		page:1,
		total_page:1,
		from:globalData.today,
		to:globalData.today,
		balance:0,
		sum_bet:0,
		sum_reward:0,
	},
	
	agent:{
		data:[],
		page:1,
		total_page:1,
		turn:false,
		search:"",
		search1:"",
	},
	
	
	ad:{
		isShow:false,	
		content:"",	
		content1:"",	
		second:0,	
		start_time:{
			date:globalData.today,
			hour:"",
			minute:"",
			format:"", 
		},	
		end_time:{
			date:globalData.today,
			hour:"",
			minute:"",
			format:"", 
		},	
		page:1,
		total_page:1,
		data:[],
	},
	noty:{
		isShow:false,		
		content:"",		
		content1:"系统检测到您的乱价行为，扣除掉500张房卡！请规范销售行为。",	
		id:0	
	},
	
	
	cardManage:{
		ticketList:[],
		data:[],
		page:1,
		total_page:1,
		search:"",
		search1:"",
		type:1,
		is_show:false,
		exchange_count:0,
		inventory_count:0,
		unexchange_count:0,
		tempInfo:{num:1,pwd:""},
	},	
	
	roomCard:{
		data:[],
		page:1,
		total_page:1,
		search:"",
		search1:"",
		deleteInfo:{
			is_show:false,
			id:0,
			totalNum:0,
			deleteNum:0,
		},
		turn:false,
	},

	channelUser:{
		data:[],
		qudaoList:[],
		day_count:0,
		month_count:0,
		total_count:0,
		week_count:0,
		page:1,
		total_page:1,
		turn:false,
		channel_id:0,
		from:globalData.today,
		from1:globalData.today,
		to:globalData.today,
		to1:globalData.today,
		recharge:0,
		recharge1:0,
		round:0,
		round1:0,
	},
	channel:{
		data:[],
		tempChannel:{name:"",account:"",password:"",qudao_id:"",header:"",account_id:""},
		isInitChannel:false,
		page:1,
		total_page:1,
		turn:false,
		phone:"",
		search:"",
	},
	
	team:{
		data:[],
		tempTeam:{name:"",account:"",password:"",profle:"",group_id:"",president_name:"",user_id:""},
		isInitTeam:false,
		page:1,
		total_page:1,
		turn:false,
		phone:"",
		search:"",
	},	

	deleteInfo:{
		isShow:false,	
		type:0,
		text:"",	
		id:0,	
	},
	
	withdrawRange:{
		data:[],
		from:globalData.today,
		to:globalData.today,
		page:1,
		total_page:1,
	},
	
	part:1,
    roomCardPart :1,
	gameSearch:{
		type:0,	
		from:globalData.today,	
		to:globalData.today,
	},	
    alert:{
		text:"",
		isShow:false,
	},
	playDetail:{
		data:[],	
		page:1,	
		total_page:1,	
	},
	scoreStat:{
		from: '',
        to: '',
        aid: 0,
        data: [],
        sum: 0
	},
	gameList:[],
	gameCount:[],
	
	userCount:{
		day_count:0,
		month_count:0,
		total_count:0,
		week_count:0,
	},	
	userSearch:{
		data:[],
		keyword:"",
		searchword:"",
		uid:"",
		searchid:"",
		page:1,
		total_page:1,
		is_null:true,
		isShowDetail:false,
		detail:"",
        uid_null:true
	},
	
	gameDetail:{
        type:1,
		type1:1,
		room_number:0,
		room_number1:0,
		round:0,
		is_num:false,
		allRound:[],
		eachRound:"",
	},

    domainInfo : {
	    now : '',
        callback : '',
        list : [],
        domains : '',
        black : '',
        auto : false
    },

    maintainList : [],
    broadcastList : [],



    'searchKeyword':"",
    'dealerList': [],
    'selectPart' : 0,
    'selectDealerID': "-1",
    'selectDealerName': "请选择代理商",
    'selectedDealerNum':"",
    'selectedDealerPhone':"",
    'selectedDealerAccount':"",
    'selectedDealerIsAdmin': 0,

    'goodsList': [],
    'inventoryCount': 0,
    'is_guild': 0,
    'is_channel': 0,
    'is_exchange': 0,
    'gamescore_array': [],
    'mallSaleCount': 0,
    'redSaleCount': 0,
    'journalList': [],
    'ManualRechargeList' : [],

    'editDealerPartShow' : false,
    'editDealerId' : "-1",
    'editDealerTitle' : "",
    'editDealerName' : "",
    'editDealerAccount' : "",
    'editDealerPwd' : "",
    'editDealerPhone' : "",
    //'editDealerPermission': -1,

    'rechargeShow' : false,
    'rechargeCount' : 0,
    'rechargeSecret' : "",

    'userRechargeShow': false,
    'userRechargeCount':0,
    'userRechargeId' : 0,
    'userRechargeAid': 0,
    'userRechargeName' : "",

    'fromDate':"",
    'toDate':"",

    'request_1':0,
    'request_2':0,
    'request_3':0,
    'request_loading':0,

    'totalPage' : 0,
    'currentPage' : 1,

	'goodsShow' : false,
	
	'balance_count' : 0,

};



var methods = {
	searchAgent : viewMethods.searchAgent,
	
	
	
    //getdealerList: viewMethods.clickShowShop,
    showQuit : viewMethods.showQuitTable,
    selectDealer:viewMethods.selectDealer,
    searchDealerOpt : viewMethods.searchDealerOpt,
    selectTimesCoin: viewMethods.clickSelectTimesCoin,

    addDealerOpt:viewMethods.addDealerOpt,
    closeDealerPart:viewMethods.closeDealerPart,
    saveDealerPart:viewMethods.saveDealerPart,
    editDealerAccountBtn:viewMethods.editDealerAccountBtn,

    openRechargePart:viewMethods.openRechargePart,
    closeRechargePart:viewMethods.closeRechargePart,
    saveRechargeOpt:viewMethods.saveRechargeOpt,

    openUserRechargePart:viewMethods.openUserRechargePart,
    closeUserRechargePart:viewMethods.closeUserRechargePart,
    saveUserRechargeOpt:viewMethods.saveUserRechargeOpt,

    openGoodsList:viewMethods.openGoodsList,
    updateGoodsList:viewMethods.updateGoodsList,
    closeGoodsList:viewMethods.closeGoodsList,
   
    
    
	showAlert : viewMethods.showAlert,
	partSelect : viewMethods.partSelect,
    roomCardPartSelect : viewMethods.roomCardPartSelect,
	searchUser : viewMethods.searchUser,
	showDetail : viewMethods.showDetail,
	hideDetail : viewMethods.hideDetail,
	searchGame : viewMethods.searchGame,

	showInitTeam : viewMethods.showInitTeam,
	hideInitTeam : viewMethods.hideInitTeam,
	editTeam : viewMethods.editTeam,
	deleteCommit : viewMethods.deleteCommit,
	quitDelete : viewMethods.quitDelete,
	deleteSubmit : viewMethods.deleteSubmit,
	searchPerson : viewMethods.searchPerson,
	turn : viewMethods.turn,
	
	showInitChannel : viewMethods.showInitChannel,
	hideInitChannel : viewMethods.hideInitChannel,
	editChannel : viewMethods.editChannel,
	selectUser : viewMethods.selectUser,
	cardCancel : viewMethods.cardCancel,
	hideCancelCard : viewMethods.hideCancelCard,
	cardFrom : viewMethods.cardFrom,
	cardTo : viewMethods.cardTo,
	searchCardInfo : viewMethods.searchCardInfo,
	cancelCardCommit : viewMethods.cancelCardCommit,
	
	cardManageSelect : viewMethods.cardManageSelect,
	cardManageSearch : viewMethods.cardManageSearch,
	showCreateCode : viewMethods.showCreateCode,
	closeCreateCode : viewMethods.closeCreateCode,
	createCodeCommit : viewMethods.createCodeCommit,

    changeDomain : viewMethods.changeDomain,
	
	
	
	//广告和通知
	editAd : viewMethods.editAd,
	sendNoty : viewMethods.sendNoty,
	
	showNoty : viewMethods.showNoty,
	hideNoty : viewMethods.hideNoty,
	sendMessage : viewMethods.sendMessage,
	
	
	gameResult : viewMethods.gameResult,


    changeBroadcast : function (item) {
        Vue.http.post(globalData.baseUrl + 'notice/edit_broadcast', {
            id : item.broadcast_id,
            content : item.content
        }).then(function(response) {
            var data = response.body;
            appData.domainInfo.now = data.domain;
            viewMethods.showAlert(1,'成功');
        }, function(response) {
            logMessage(response.body);
        });
    },

    changeBroadcastState : function (item) {
	    var state = item.state == 1 ? 2 : 1;
        Vue.http.post(globalData.baseUrl + 'notice/edit_broadcast', {
            id : item.broadcast_id,
            state : state
        }).then(function(response) {
            var data = response.body;
            item.state_text = state === 1 ? '开启' : '关闭';
            item.state_other_text = 1 === state ? '关闭' : '开启';
            item.state = state;
            appData.domainInfo.now = data.domain;
            viewMethods.showAlert(1,'成功');
        }, function(response) {
            logMessage(response.body);
        });
    },

    changeMaintain : function (item) {
        Vue.http.post(globalData.baseUrl + 'notice/edit_maintain', {
            game_type : item.game_type,
            service_text : item.service_text
        }).then(function(response) {
            var data = response.body;
            appData.domainInfo.now = data.domain;
            viewMethods.showAlert(1,'成功');
        }, function(response) {
            logMessage(response.body);
        });
    },

    changeMaintainState : function (item) {
        var state = item.is_delete == 1 ? 0 : 1;
        Vue.http.post(globalData.baseUrl + 'notice/edit_maintain', {
            game_type : item.game_type,
            state : state
        }).then(function(response) {
            var data = response.body;
            item.state_text = state === 1 ? '关闭' : '开启';
            item.state_other_text = 1 === state ? '开启' : '关闭';
            item.is_delete = state;
            appData.domainInfo.now = data.domain;
            viewMethods.showAlert(1,'成功');
        }, function(response) {
            logMessage(response.body);
        });
    },

    changeDomains : function () {
        Vue.http.post(globalData.baseUrl + 'domain/change_domains', {
            "domains":appData.domainInfo.domains,
        }).then(function(response) {
            var data = response.body;
            appData.domainInfo.list = data.list;
            viewMethods.showAlert(1,'成功');
        }, function(response) {
            logMessage(response.body);
        });
    },

    changeCallback : function (domain) {
        Vue.http.post(globalData.baseUrl + 'domain/change_callback', {
            "domain":domain,
        }).then(function(response) {
            var data = response.body;
            if(typeof(data) === "object"){
                appData.domainInfo.callback = data.callback;
                viewMethods.showAlert(1,'成功');
            }else{
                viewMethods.showAlert(1,data);
            }
        }, function(response) {
            logMessage(response.body);
        });
    },

    changeAutoState : function () {
        Vue.http.post(globalData.baseUrl + 'domain/change_auto_state', {
        }).then(function(response) {
            viewMethods.showAlert(1,'成功');
        }, function(response) {
            logMessage(response.body);
        });
    }
};



httpModule.getdealerList();


//Vue生命周期
var vueLife = {
    vmCreated: function () {
        logMessage('vmCreated');
        //resetState();
        //reconnectSocket();
        
        //$("#loading").hide();
        //$(".main").show();
        //appData.isShowErweima = false;
    },
    vmUpdated: function () {
        logMessage('vmUpdated');
    },
    vmMounted: function () {
        setTimeout(function() {
            //var audioBack = document.getElementById("backMusic");
            //audioBack.volume = 1;
            //mp3AudioPlay("backMusic");
        }, 1e3);
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



function gameChange(e){
    appData.gameSearch.type=e.value;
    appData.playDetail.page=1;
    appData.playDetail.total_page=1;
//	httpModule.getPlayCount();
	httpModule.getPlayDetailList();
}
function wordsTest(e){
	if(trimStr(appData.userSearch.keyword)!=""){
		appData.userSearch.is_null=false;
	}
	else{
		appData.userSearch.is_null=true;
	}
	if(!appData.userSearch.is_null&&e.which==13&&e.keyCode==13){
		viewMethods.searchUser();
	}
//	httpModule.getPlayCount();
}
function uidTest(e){
	if(trimStr(appData.userSearch.uid)!=""){
		appData.userSearch.uid_null=false;
	}
	else{
		appData.userSearch.uid_null=true;
	}
	if(!appData.userSearch.uid_null&&e.which==13&&e.keyCode==13){
		viewMethods.searchUser();
	}
}


function gameChange1(e){
	appData.gameDetail.type=e.value;
}
function numTest(e){
	if(!trimStr(appData.gameDetail.room_number)){
		appData.gameDetail.is_num=false;
	//	viewMethods.showAlert("房间号须为正整数");
	}
	else{
		appData.gameDetail.is_num=true;
	}
	if(!appData.gameDetail.is_num&&e.which==13&&e.keyCode==13){
		console.log("search")
	//	viewMethods.searchUser();
	}
//	httpModule.getPlayCount();
}

function roundChange(e){
	httpModule.getRoomGameResult(e.value);
}

function isNum(s){//是否为正整数
	var re = /^[0-9]+$/ ;
	return re.test(s)
}   
function trimStr(str){
	return str.replace(/(^\s*)|(\s*$)/g,"");
}

function  timeChange(time){
	var stringTime = time;
	var timestamp2 = Date.parse(new Date(stringTime));
	timestamp2 = timestamp2 / 1000;
	return timestamp2;
}

function logMessage(message) {  
    console.log(message);
};

function base64_encode(str) {
    var base64EncodeChars = "ABCDEFGHIJKLMNOPQRSTUVWXYZabcdefghijklmnopqrstuvwxyz0123456789+/"; 
  var out, i, len;
  var c1, c2, c3;
  len = str.length;
  i = 0;
  out = "";
  while(i < len) {
  c1 = str.charCodeAt(i++) & 0xff;
  if(i == len)
  {
  out += base64EncodeChars.charAt(c1 >> 2);
  out += base64EncodeChars.charAt((c1 & 0x3) << 4);
  out += "==";
  break;
  }
  c2 = str.charCodeAt(i++);
  if(i == len)
  {
  out += base64EncodeChars.charAt(c1 >> 2);
  out += base64EncodeChars.charAt(((c1 & 0x3)<< 4) | ((c2 & 0xF0) >> 4));
  out += base64EncodeChars.charAt((c2 & 0xF) << 2);
  out += "=";
  break;
  }
  c3 = str.charCodeAt(i++);
  out += base64EncodeChars.charAt(c1 >> 2);
  out += base64EncodeChars.charAt(((c1 & 0x3)<< 4) | ((c2 & 0xF0) >> 4));
  out += base64EncodeChars.charAt(((c2 & 0xF) << 2) | ((c3 & 0xC0) >>6));
  out += base64EncodeChars.charAt(c3 & 0x3F);
  }
  return out;
 }
function base64_decode(str){  
                var c1, c2, c3, c4;
                var base64EncodeChars = "ABCDEFGHIJKLMNOPQRSTUVWXYZabcdefghijklmnopqrstuvwxyz0123456789+/"; 
               var base64DecodeChars = new Array(
                        -1, -1, -1, -1, -1, -1, -1, -1, -1, -1, -1, -1, -1, -1, -1, -1, -1, -1,
                        -1, -1, -1, -1, -1, -1, -1, -1, -1, -1, -1, -1, -1, -1, -1, -1, -1, -1,
                        -1, -1, -1, -1, -1, -1, -1, 62, -1, -1, -1, 63, 52, 53, 54, 55, 56, 57,
                        58, 59, 60, 61, -1, -1, -1, -1, -1, -1, -1, 0,  1,  2,  3,  4,  5,  6,
                        7,  8,  9,  10, 11, 12, 13, 14, 15, 16, 17, 18, 19, 20, 21, 22, 23, 24,
                        25, -1, -1, -1, -1, -1, -1, 26, 27, 28, 29, 30, 31, 32, 33, 34, 35, 36,
                        37, 38, 39, 40, 41, 42, 43, 44, 45, 46, 47, 48, 49, 50, 51, -1, -1, -1,
                        -1, -1
                );
     var i=0, len = str.length, string = '';

                while (i < len){
                        do{
                                c1 = base64DecodeChars[str.charCodeAt(i++) & 0xff]
                        } while (
                                i < len && c1 == -1
                        );

                        if (c1 == -1) break;

                        do{
                                c2 = base64DecodeChars[str.charCodeAt(i++) & 0xff]
                        } while (
                                i < len && c2 == -1
                        );

                        if (c2 == -1) break;

                        string += String.fromCharCode((c1 << 2) | ((c2 & 0x30) >> 4));

                        do{
                                c3 = str.charCodeAt(i++) & 0xff;
                                if (c3 == 61)
                                        return string;

                                c3 = base64DecodeChars[c3]
                        } while (
                                i < len && c3 == -1
                        );

                        if (c3 == -1) break;

                        string += String.fromCharCode(((c2 & 0XF) << 4) | ((c3 & 0x3C) >> 2));

                        do{
                                c4 = str.charCodeAt(i++) & 0xff;
                                if (c4 == 61) return string;
                                c4 = base64DecodeChars[c4]
                        } while (
                                i < len && c4 == -1
                        );

                        if (c4 == -1) break;

                        string += String.fromCharCode(((c3 & 0x03) << 6) | c4)
                }
                return string;
} 
function utf16to8(str) {
  var out, i, len, c;


  out = "";
  len = str.length;
  for(i = 0; i < len; i++) {
    c = str.charCodeAt(i);
    if ((c >= 0x0001) && (c <= 0x007F)) {
      out += str.charAt(i);
    } else if (c > 0x07FF) {
      out += String.fromCharCode(0xE0 | ((c >> 12) & 0x0F));
      out += String.fromCharCode(0x80 | ((c >>  6) & 0x3F));
      out += String.fromCharCode(0x80 | ((c >>  0) & 0x3F));
    } else {
      out += String.fromCharCode(0xC0 | ((c >>  6) & 0x1F));
      out += String.fromCharCode(0x80 | ((c >>  0) & 0x3F));
    }
  }
  return out;
}
function utf8to16(str) {
  var out, i, len, c;
  var char2, char3;


  out = "";
  len = str.length;
  i = 0;
  while(i < len) {
    c = str.charCodeAt(i++);
    switch(c >> 4) {
      case 0: case 1: case 2: case 3: case 4: case 5: case 6: case 7:
        // 0xxxxxxx
        out += str.charAt(i-1);
        break;
      case 12: case 13:
        // 110x xxxx   10xx xxxx
        char2 = str.charCodeAt(i++);
        out += String.fromCharCode(((c & 0x1F) << 6) | (char2 & 0x3F));
        break;
      case 14:
        // 1110 xxxx  10xx xxxx  10xx xxxx
        char2 = str.charCodeAt(i++);
        char3 = str.charCodeAt(i++);
        out += String.fromCharCode(((c & 0x0F) << 12) |
        ((char2 & 0x3F) << 6) |
        ((char3 & 0x3F) << 0));
        break;
    }
  }
  return out;
}
