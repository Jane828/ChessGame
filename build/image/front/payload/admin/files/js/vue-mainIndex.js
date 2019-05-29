

var ws;



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

  appData.fromDate  = globalData.fromDate;
  appData.toDate  = globalData.toDate;


});



var httpModule = {

    getdealerList: function () {

        logMessage(appData.searchKeyword);

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
            "dealer_num":appData.selectedDealerNum
        }).then(function(response) {
            logMessage(response.body);
            var bodyData = response.body;

            if (bodyData.result == 0) {
                var dealerInfo = bodyData.data;

                appData.goodsList = dealerInfo.goods_array.concat();
                appData.inventoryCount = dealerInfo.inventory_count;

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


                $(".tcdPageCode").createPage({
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
        else if($.trim(appData.editDealerPaymentType) == "-1")
        {
            alert("请选择代理商收款类型");
        }
        else
        {
            Vue.http.post(globalData.baseUrl + 'admin/updateDealerOpt', {
                "dealer_id":appData.editDealerId,
                "name":appData.editDealerName,
                "account":appData.editDealerAccount,
                "passwd":appData.editDealerPwd,
                "payment_type":appData.editDealerPaymentType,
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
    saveRechargeOpt: function () {

        if($.trim(appData.selectedDealerNum) == "")
        {
            alert("请选择代理商");
        }
        else if($.trim(appData.rechargeCount) == "")
        {
            alert("请输入充值数值");
        }
        else if($.trim(appData.rechargeSecret) == "")
        {
            alert("请输入充值秘钥");
        }
        else
        {
            if(window.confirm('确定要给该代理商充值'+appData.rechargeCount+'张房卡？')){
                 
                Vue.http.post(globalData.baseUrl + 'admin/dealerRechargeOpt', {
                    "dealer_num":appData.selectedDealerNum,
                    "ticket_count":appData.rechargeCount,
                    "secret":appData.rechargeSecret,
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

}






var viewMethods = {


    showQuitTable: function () {
        window.location.href = globalData.baseUrl + "admin/logout";
        
    },
    selectDealer: function (obj) {

        appData.request_1 = 1;
        appData.request_2 = 1;
        appData.request_3 = 1;

        appData.selectDealerID=obj.dealer_id;
        appData.selectedDealerNum = obj.dealer_num;
        appData.selectDealerName = obj.name;
        appData.selectDealerPaymentType = obj.payment_type;
        appData.selectDealerClearPwd=obj.clear_pwd;
        appData.selectedDealerAccount=obj.account;

        httpModule.getDealerInfo();
        httpModule.getDealerSaleInfo();
        httpModule.getDealerJournal();


        logMessage(appData.selectedDealerNum);
        logMessage(appData.selectDealerPaymentType);



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
        appData.editDealerPwd = appData.selectDealerClearPwd;
        appData.editDealerPaymentType = appData.selectDealerPaymentType;
        
        $("#paymentType").find("option[value='"+appData.editDealerPaymentType+"']").attr("selected",true);

        appData.editDealerPartShow = true;
    },
    addDealerOpt: function () {
        appData.editDealerTitle = "添加代理商账号";
        appData.editDealerId = "-1";
        appData.editDealerName = "";
        appData.editDealerAccount = "";
        appData.editDealerPwd = "";
        appData.editDealerPaymentType = "-1";
        appData.editDealerPartShow = true;
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
}

var appData = {
    'searchKeyword':"",
    'dealerList': [],
    'selectPart' : 0,
    'selectDealerID': "-1",
    'selectDealerName': "请选择代理商",
    'selectedDealerNum':"",
    'selectDealerPaymentType':"-1",
    'selectDealerClearPwd':"",
    'selectedDealerAccount':"",

    'goodsList': [],
    'inventoryCount': 0,
    'mallSaleCount': 0,
    'redSaleCount': 0,
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

    'totalPage' : 0,
    'currentPage' : 1,

    'goodsShow' : false,

};



var methods = {
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

    openGoodsList:viewMethods.openGoodsList,
    updateGoodsList:viewMethods.updateGoodsList,
    closeGoodsList:viewMethods.closeGoodsList,

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
