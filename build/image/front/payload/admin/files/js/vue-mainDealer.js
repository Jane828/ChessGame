$(document).ready(function() {
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
      
	appData.request_1 = 1;
    appData.request_2 = 1;
    appData.request_3 = 1;

	appData.fromDate  = globalData.fromDate;
	appData.toDate  = globalData.toDate;

	// httpModule.getGameList();
	// httpModule.getPlayCount();
	// httpModule.getPlayDetailList();
		
	// httpModule.getActiveCount();

	
	// httpModule.getDealerInfo();
	// httpModule.getDealerSaleInfo();
	// httpModule.getDealerJournal();


	httpModule.getDealerInfo();
	httpModule.getPlayCount();
	httpModule.getPlayDetailList();
    httpModule.getGameList();
		
		
	httpModule.getExchangeTypeList();
	httpModule.getExchangeSummary();
	httpModule.getExchangeTicketList();	
	//httpModule.getActiveCount();
	//httpModule.getDealerSaleInfo();
	//httpModule.getDealerJournal();
	$("#gameResultList").val(-1);

});
var httpModule = {
    
    getDealerInfo: function () {
        Vue.http.post(globalData.baseUrl + 'dealer/dealerInfo', {
            "dealer_num":appData.selectedDealerNum
        }).then(function(response) {
            logMessage(response.body);
            var bodyData = response.body;

            if (bodyData.result == 0) {
                var dealerInfo = bodyData.data;

                appData.goodsList = dealerInfo.goods_array.concat();
                appData.inventoryCount = dealerInfo.inventory_count;
				appData.is_guild = dealerInfo.is_guild;
				appData.is_exchange  = dealerInfo.is_exchange ;
                appData.gamescore_array = dealerInfo.gamescore_array;

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
    /*getDealerSaleInfo: function () {
        Vue.http.post(globalData.baseUrl + 'dealer/dealerSaleInfo', {
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
    },*/
    getDealerRechargeInfo: function () {
        Vue.http.post(globalData.baseUrl + 'dealer/dealerRechargeInfo', {
            "dealer_num":appData.selectedDealerNum,
            "from":appData.fromDate,
            "to":appData.toDate
        }).then(function(response) {
            logMessage(response.body);
            var bodyData = response.body;

            if (bodyData.result == 0) {
                appData.ticketRechargeCount = bodyData.data.sum_recharge;
                appData.ticketRewardCount = bodyData.data.sum_reward;
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

        Vue.http.post(globalData.baseUrl + 'dealer/dealerJournal', {
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
			appData.request_3 = 0;  
            var bodyData = response.body;
            if (bodyData.result == 0) {
				appData.gameCount=bodyData.data.concat();   
				appData.balance_count= bodyData.balance;              
            } else if(bodyData.result == -3)
            {
                window.location.reload();
            } else {
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
			appData.request_2 = 0;
            var bodyData = response.body;
            if (bodyData.result == 0) {
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
			appData.request_2 = 0;
            logMessage(response.body);
        });
    },   
	
	getActiveCount: function () {  
        Vue.http.post(globalData.baseUrl + 'game/getActiveCount', {
        	"dealer_num":appData.selectedDealerNum,
        }).then(function(response) {
			appData.request_2 = 0;
            logMessage(response.body);
            var bodyData = response.body;
            if (bodyData.result == 0) {
            	appData.userCount.day_count=bodyData.data.day_count;
            	appData.userCount.month_count=bodyData.data.month_count;
            	appData.userCount.week_count=bodyData.data.week_count;
            	appData.userCount.total_count=bodyData.data.total_count;      
            } else if(bodyData.result == -3)
            {
                window.location.reload();
            } else {
                alert(bodyData.result_message);
            }

        }, function(response) {
			appData.request_2 = 0;
            logMessage(response.body);
        });
    },	
    getAccountList: function () {  
        Vue.http.post(globalData.baseUrl + 'game/getAccountList', {
        	"dealer_num":appData.selectedDealerNum,
        	"keyword":appData.userSearch.searchword,
        	"page":appData.userSearch.page,
        }).then(function(response) {
            logMessage(response.body);
			appData.request_3 = 0;
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
                window.location.reload();
            } else {
                alert(bodyData.result_message);
            }

        }, function(response) {
			appData.request_3 = 0;
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
        	appData.request_2 = 0;
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
		appData.request_3 = 1;
        Vue.http.post(globalData.baseUrl + 'guild/searchUser', {
        	"dealer_num":appData.selectedDealerNum,
        	"page":appData.team.page,
        	"phone":appData.team.phone,
        }).then(function(response) {
        	appData.request_3 = 0;
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
		appData.request_3 = 1;
        Vue.http.post(globalData.baseUrl + 'guild/creatGroup', {
        	"dealer_num":appData.selectedDealerNum,
        	"my_aid":globalData.my_aid,
        	
        	"president":appData.team.tempTeam.user_id,
        	"name":appData.team.tempTeam.name,
        	"profile":appData.team.tempTeam.profile,
        	"account":appData.team.tempTeam.account,
        	"password":appData.team.tempTeam.password,

        }).then(function(response) {
        	appData.request_3 = 0;
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
		appData.request_3 = 1;
        Vue.http.post(globalData.baseUrl + 'guild/editGroup', {
        	"dealer_num":appData.selectedDealerNum,
        	"my_aid":globalData.my_aid,
        	
        	"group_id":appData.team.tempTeam.group_id,
        	"name":appData.team.tempTeam.name,
        	"profile":appData.team.tempTeam.profile,
        	"password":appData.team.tempTeam.password,

        }).then(function(response) {
        	appData.request_3 = 0;
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
		appData.request_3 = 1;
        Vue.http.post(globalData.baseUrl + 'guild/deleteGroup', {
        	"dealer_num":appData.selectedDealerNum,
        	"my_aid":globalData.my_aid,   	
        	"group_id":appData.deleteInfo.id,

        }).then(function(response) {
        	appData.request_3 = 0;
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
        	appData.request_2 = 0;
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
		appData.request_3 = 1;
        Vue.http.post(globalData.baseUrl + 'guild/dealGuildWithdraw', {
        	"dealer_num":appData.selectedDealerNum,
        	"my_aid":globalData.my_aid,
        	"group_id":appData.deleteInfo.group_id,
        	"withdraw_id":appData.deleteInfo.id,
        }).then(function(response) {
        	appData.request_3 = 0;
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
        Vue.http.post(globalData.baseUrl + 'qudao/getQudaoData', {
        	"dealer_num":appData.selectedDealerNum,
        	"qudao_id":$("#qudaoList").val(),
        	"from":appData.channelUser.from,
        	"to":appData.channelUser.to,
        }).then(function(response) {
        	appData.request_3 = 0;
            var bodyData = response.body;
            if (bodyData.result == 0) {
            	appData.channelUser.data=[];          	
            	appData.channelUser.user_count=bodyData.data.user_count;
                appData.channelUser.spend_count=bodyData.data.spend_count;
            	
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

	getExchangeTypeList: function () {  
    	appData.request_3 = 1;
        Vue.http.post(globalData.baseUrl + 'exchange/getExchangeTypeList', {
        	"dealer_num":appData.selectedDealerNum,
        }).then(function(response) {
        	appData.request_3 = 0;
            var bodyData = response.body;
            if (bodyData.result == 0) {
            	appData.cardManage.ticketList=bodyData.data.concat();
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
	getExchangeSummary: function () {  
    	appData.request_3 = 1;
        Vue.http.post(globalData.baseUrl + 'exchange/getExchangeSummary', {
        	"dealer_num":appData.selectedDealerNum,
        }).then(function(response) {
        	appData.request_3 = 0;
            var bodyData = response.body;
            if (bodyData.result == 0) {
            	appData.cardManage.exchange_count=bodyData.data.exchange_count;
            	appData.cardManage.inventory_count=bodyData.data.inventory_count;
            	appData.cardManage.unexchange_count=bodyData.data.unexchange_count;
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
    getExchangeTicketList: function () {  
    	appData.request_3 = 1;
        Vue.http.post(globalData.baseUrl + 'exchange/getExchangeTicketList', {
        	"dealer_num":appData.selectedDealerNum,
        	"type":appData.cardManage.type,
        	"page":appData.cardManage.page,
        	"code":appData.cardManage.search,
        }).then(function(response) {
        	appData.request_3 = 0;
            var bodyData = response.body;
            if (bodyData.result == 0) {
            	appData.cardManage.data=[];
            	appData.cardManage.total_page=bodyData.sum_page;
            	appData.cardManage.data=bodyData.data.concat();

            	if(appData.cardManage.total_page>1){
					 $(".tcdPageCode10").createPage({
	                    pageCount:appData.cardManage.total_page,
	                    current:appData.cardManage.page,
	                    backFn:function(p){
	                        appData.cardManage.page =p;
	                        httpModule.getExchangeTicketList();
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

	createExchangeCode: function () {  
    	appData.request_3 = 1;
        Vue.http.post(globalData.baseUrl + 'exchange/createExchangeCode', {
        	"dealer_num":appData.selectedDealerNum,
        	"dealer_id":appData.selectDealerID,
        	"type":$("#createCodeSelect").val(),
        	"count":appData.cardManage.tempInfo.num,
        	"secret":hex_md5(appData.cardManage.tempInfo.pwd),
        	
        }).then(function(response) {
        	appData.request_3 = 0;
            var bodyData = response.body;
            if (bodyData.result == 0) {
				viewMethods.closeCreateCode();
				
				appData.cardManage.type=1;
				appData.cardManage.page=1;
				appData.cardManage.total_page=1;
				appData.cardManage.data=[];
				httpModule.getExchangeSummary();
				httpModule.getExchangeTicketList();
				appData.cardManage.showCreateSuccess=true;
			//	viewMethods.showAlert(2,"生成成功");
				$("#createExcel").attr("href","../exchange/exportNewTicketExcel/"+bodyData.data.success_count);

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
}


var viewMethods = {
    showAlert:function(type,text){
		appData.alert.text=text;
		appData.alert.type=type;
		appData.alert.isShow=true;
		setTimeout(function(){appData.alert.isShow=false;},1500)
	},
	
	partSelect:function(num){
		appData.part = num;
		if(num==1){
            appData.request_2 = 1;
            appData.request_3 = 1;
	        httpModule.getDealerRechargeInfo();
	        httpModule.getDealerJournal();
		}
		else if(num==2){
            appData.request_2 = 1;
            appData.request_3 = 1;
			httpModule.getPlayCount();
			httpModule.getPlayDetailList();
           
		}
		else if(num==3){
            appData.request_2 = 1;
            appData.request_3 = 1;
			httpModule.getActiveCount();
             httpModule.getAccountList();
		}
		else if(num==4){
			
		}
		else if(num==5){
			appData.team.page=1;
			appData.request_2 = 1;
			httpModule.queryAllGroup();
		}
		else if(num==6){
			appData.request_2 = 1;
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
        	appData.channelUser.user_count=0;
            appData.channelUser.spend_count=0;
			httpModule.getQudaolist();
		}
		else if(num==11){
			httpModule.searchAgentList();
		}
		else if(num==12){
			httpModule.getSlotSummary();
			httpModule.getSlotList();
		}
	},
	
	searchUser:function(){
		appData.userSearch.page=1;
		appData.userSearch.total_page=1;
		appData.userSearch.searchword=appData.userSearch.keyword;
		httpModule.getAccountList();
	},		

	showDetail:function(num){
		appData.userSearch.detail=appData.userSearch.data[num];		
		appData.userSearch.isShowDetail=true;		
	},	
	hideDetail:function(){
		appData.userSearch.isShowDetail=false;		
	},

	searchGame:function(){
		appData.gameDetail.room_number1=appData.gameDetail.room_number;
		appData.gameDetail.type1=appData.gameDetail.type;
		appData.gameDetail.allRound=[];
		appData.gameDetail.eachRound="";
		
		httpModule.getRoomRound();
	},	
	

    showQuit: function () {

        //logMessage("showQuitTable");
        window.location.href = globalData.baseUrl + "account/logout";
        
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
            httpModule.getDealerRechargeInfo();
            httpModule.getDealerJournal();
        }
        
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
		else if(!(reg.test(appData.team.tempTeam.password))){
			viewMethods.showAlert(1,'密码只能由数字和字母组成');
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
			appData.deleteInfo.text="是否删除渠道？";
		}	
		else if(type==6){
			appData.deleteInfo.text="是否绑定直营代理？";
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
			httpModule.editChannel();
		}
		else if(appData.deleteInfo.type==6){
			httpModule.bindAgentOpt();
		}
	},	
	quitDelete:function(){
		appData.deleteInfo.isShow=false;
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
		else if(!(reg.test(appData.channel.tempChannel.password))){
			viewMethods.showAlert(1,'密码只能由数字和字母组成');
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
		
			appData.channelUser.from=appData.channelUser.from1;
			appData.channelUser.to=appData.channelUser.to1;
			httpModule.getQudaoInfo();
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
		window.open(globalData.baseUrl + "dealer/roomCard?id="+id+"&name="+base64_encode(utf16to8(name)))
	},	
	cardTo:function(id,name){
		window.open(globalData.baseUrl + "dealer/customerCard?dealer_num="+appData.selectedDealerNum+"&id="+id+"&name="+base64_encode(utf16to8(name)))
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
	//	else if(appData.roomCard.deleteInfo.deleteNum>appData.roomCard.deleteInfo.totalNum){
	//		viewMethods.showAlert(1,"扣除房卡数过多");
	//	}
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

	closeCreateSuccess:function(){
		appData.cardManage.showCreateSuccess=false;		
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
}


var appData = {
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
		showCreateSuccess:false,
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
	},

	channelUser:{
		data:[],
		qudaoList:[],
		user_count:0,
        spend_count:0,
		turn:false,
		channel_id:0,
		from:globalData.today,
		from1:globalData.today,
		to:globalData.today,
		to1:globalData.today,
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
	withdrawRange:{
		data:[],
		from:"",
		to:"",
		page:1,
		total_page:1,
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
	
	part:2,
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
		page:1,
		total_page:1,
		is_null:true,
		isShowDetail:false,
		detail:"",
	},
	
	gameDetail:{
		// type:0,
		// type1:0,
        type:1,
		type1:1,
		room_number:0,
		room_number1:0,
		round:0,
		is_num:false,
		allRound:[],
		eachRound:"",
	},
	
    'searchKeyword':"",
    'dealerList': [],
    'selectPart' : 0,
    'selectDealerID': "-1",
    'selectDealerName': globalData.name,
    'selectedDealerNum': globalData.num,
    'selectDealerPaymentType':globalData.type,
    'selectDealerClearPwd':"",
    'selectedDealerAccount':"",

    'goodsList': [],
	'is_guild': 0,
	'is_exchange ': 0,
    'gamescore_array': [],
    'inventoryCount': 0,
    'mallSaleCount': 0,
    'redSaleCount': 0,
    'ticketRechargeCount': 0,
    'ticketRewardCount': 0,
    'journalList': [],

    'editDealerPartShow' : false,
    'editDealerId' : "-1",
    'editDealerTitle' : "",
    'editDealerName' : "",
    'editDealerAccount' : "",
    'editDealerPwd' : "",
    'editDealerPaymentType' : "-1",

    'rechargeShow' : false,
    'rechargeCount' : 0,
    'rechargeSecret' : "",

    'fromDate':"",
    'toDate':"",

    'request_1':0,
    'request_2':0,
    'request_3':0,
	'request_loading':0,

    'totalPage' : 0,
	'currentPage' : 1,
	
	'balance_count' : 0,
};


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
    methods: viewMethods,
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
