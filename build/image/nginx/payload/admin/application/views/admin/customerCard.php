<!DOCTYPE html>
<html v-app="app">
<head>
	<meta charset="utf-8">
	<title>代理商房卡查询</title>

	<link href="<?php echo $base_url;?>files/css/perfect-scrollbar.css" rel="stylesheet">
	<link rel="stylesheet" href="<?php echo $base_url;?>files/css/loading.css">
    <link rel="stylesheet" type="text/css" media="all" href="<?php echo $base_url;?>files/css/daterangepicker-bs3.css" />

    <script src="<?php echo $base_url;?>files/js/jquery-1.9.1.min.js"></script>
    <script type="text/javascript" src="<?php echo $base_url;?>files/js/moment.js"></script>
    <script type="text/javascript" src="<?php echo $base_url;?>files/js/daterangepicker.js"></script>
	<script type="text/javascript" src="<?php echo $base_url;?>files/js/vue.min.js" ></script>
	<script type="text/javascript" src="<?php echo $base_url;?>files/js/vue-resource.min.js" ></script>

	<script src="<?php echo $base_url;?>files/js/jquery.page.js"></script>		
	
<style>
*{padding: 0;margin:0;-webkit-tap-highlight-color:rgba(0,0,0,0);-webkit-backface-visibility: hidden;}a {text-decoration: none;}li,ul {list-style: none;}input{border: none;outline:none}
body{font-family:"微软雅黑";cursor: default;font-size: 16px;}a,input,select{font-family:"微软雅黑";font-size: 16px;}
img{border: none;}

body{background: #e3e3e3;}	
.head{position: relative;width: 100%;min-width: 1280px;height: 70px;box-shadow:0px 2px 8px #aaa;background: #fff;}	
.head .center{position: relative;width: 1226px;margin: 0 auto;}	
.head .center img{position: relative;height: 50px;float:left;margin-top:10px}	


.body{position: relative;padding:30px 0 50px 0;}	
.box{position: relative;width: 1226px;margin: 0 auto;background: #fff;box-shadow:0px 0px 8px 2px #aaa;min-height: 800px;}	


.mainPart{margin: 0 auto;padding: 20px;width:1000px;}
.mainPart .headTitle{font-size: 20px;font-weight: bold;line-height: 60px;}
.mainPart .search{height: 40px;margin-top: 10px;line-height: 40px;}

.mainPart .search .item{float:left;height: 36px;width: 220px;line-height:36px;text-align: center;background:#f5f5f5;position: relative;margin-right:30px;margin-bottom:20px; }
.mainPart .search .item .name{height: 36px;overflow: hidden;}
.mainPart .search .item .delete{position: absolute;width: 16px;height: 16px;top:-8px;right:-8px;cursor: pointer;display: none;}
.mainPart .search .item .from{position: absolute;width: 20px;height: 20px;top:8px;right:-25px;}
.mainPart .search .select{background: #397abe;color: #fff;}

.mainPart .roomCardList{width:1010px;border:1px solid #d9d9d9;margin-top: 15px;}	
.mainPart .roomCardList tr{height: 60px;}
.mainPart .roomCardList .item td{border-top: 1px solid #d9d9d9;cursor: pointer;}
.mainPart .roomCardList .item td img{max-height: 40px;max-width: 40px;}
.mainPart .roomCardList .item td .cardFrom{cursor: pointer;color:#397abe;}
.mainPart .roomCardList .item td .cardCancel{cursor: pointer;color: #f15352;}
.mainPart .roomCardList .item td .noty{cursor: pointer;color: #f15352;}

.alert{position: fixed;z-index: 11;width: 160px;height: 160px;top:50%;left: 50%;margin-top: -80px;margin-left: -80px;border-radius:5px;overflow:hidden;}
.alert .alertBackground{filter:alpha(opacity=50);-moz-opacity:0.5;opacity:0.5;background: #000;height: 100%;width: 100%;}
.alert img{position: absolute;top:15px;left: 29px;}
.alert .alertText{position: absolute;top:115px;width:160px;text-align: center;font-size: 18px;color: #fff;filter:alpha(opacity=95);-moz-opacity:0.95;opacity:0.95;}


.cancelCard{width:100%;height:100%;position: fixed;top: 0; left: 0;z-index: 9;}
.cancelCard .back{width:100%;height:100%;position: fixed;top: 0; left: 0;background: #000;opacity:.3;}
.cancelCard .box1{width:500px;height:300px;background:#fff;position: fixed;top: 50%; left: 50%;margin: -150px 0 0 -250px;}
.cancelCard .box1 .title{line-height: 40px;height:40px;background: #f3f3f7;}
.cancelCard .box1 .title a{margin-left: 20px;}
.cancelCard .box1 .title img{float:right;margin-right:25px;width:20px;margin-top:10px;cursor:pointer;}
.cancelCard .box1 .main{margin: 0 auto;margin-top:20px;width: 400px;line-height: 40px;font-size: 14px;}
.cancelCard .box1 .main input{width: 300px;height: 34px;border: 1px solid #e2e2e2;padding:0 5px;font-size: 14px;}
.cancelCard .box1 .text {position: relative;height:32px;line-height: 32px;color: #f15352;font-size: 14px;text-align: center;margin-top: 30px;}
.cancelCard .box1 .bottomButton{background: #f15352;height: 32px;width:100px;margin:0 auto;margin-top:10px;text-align: center;border-radius:3px;cursor:pointer;color:#fff;line-height: 32px;}

.noteBox{width:100%;height:100%;position: fixed;top: 0; left: 0;z-index: 9;}
.noteBox .back{width:100%;height:100%;position: fixed;top: 0; left: 0;background: #000;opacity:.3;}
.noteBox .box1{width:500px;height:300px;background:#fff;position: fixed;top: 50%; left: 50%;margin: -150px 0 0 -250px;}
.noteBox .box1 .title{line-height: 40px;height:40px;background: #f3f3f7;}
.noteBox .box1 .title a{margin-left: 20px;}
.noteBox .box1 .title img{float:right;margin-right:25px;width:20px;margin-top:10px;cursor:pointer;}
.noteBox .box1 .main{margin: 0 auto;margin-top:20px;width: 400px;line-height: 40px;font-size: 14px;}
.noteBox .box1 .main textarea{width: 390px;height: 100px;line-height: 30px;font-size: 14px;padding: 5px;border:1px solid #d9d9d9;}
.noteBox .box1 .bottomButton{background: #f15352;height: 32px;width:100px;margin:0 auto;margin-top:10px;text-align: center;border-radius:3px;cursor:pointer;color:#fff;line-height: 32px;}

</style>

</head>
<body id="body" >

	<div id="app-main">
		<div style="position: fixed;width:100%;height:100%;top:0;left:0;background: #fff;z-index:100;filter:alpha(Opacity=60);-moz-opacity:0.3;opacity: 0.3" id="loading" v-show="request_1==1||request_2==1||request_3==1">
			<div class="load4">
				<div class="loader">Loading...</div>
			</div>
		</div>
		<div class="head">
			<div class="center">
				<!--<img src="../files/images/cms/title.png">-->
				<!--<div class="click" style="float: right;width:100px;text-align: center;margin-right:-15px;color:#ac1f1f;font-size:18px;height:24px;margin-top:24px;"   v-on:click="showQuit()" >退出</div>-->
			</div>		
		</div>
		<div class="body">
			<div class="box">
				<div class="mainPart"  >
<!--					<div class="headTitle">房卡去向</div>-->
                    <div class="headTitle" style="height: 60px;">
                        <div style="float: left;margin-right:15px; ">房卡去向</div>
                        <div class="control-group" style="float: left;">
                            <div class="controls">
                                <div class="input-prepend input-group">
                                    <input type="text" readonly style="width: 340px;height:38px;cursor:pointer;border:1px solid #e2e2e2;padding: 0 5px;margin-left:0;background: url('<?php echo $base_url;?>files/images/cms/down.png') no-repeat scroll right center transparent;" name="reservation" id="reservation" class="form-control" value="<?php echo date('Y-m-d');?> - <?php echo date('Y-m-d') ;?>" />
                                </div>
                            </div>
                        </div>
                    </div>

					<div class="search">
						<div class="item" v-for="(u,index) in roomCard.userList" v-bind:class="{true: 'select', false: 'notselect'}[u.isSelect]">
							<div class="name">{{u.nickname}}</div>
							<img class="delete" src="<?php echo $base_url;?>files/images/cms/delete2.png" v-if="index!=0" v-on:click="listBreak(u.account_id)"/>
							<img class="from" src="<?php echo $base_url;?>files/images/cms/adPre.png" v-show="index!=roomCard.userList.length-1"/>
						</div>
					</div>

					<table class="roomCardList" cellspacing="0" >
						<tr style="height: 40px">
							<td style="width: 4%;"></td>
							<td style="width: 8%;"></td>
							<td style="width: 22%;">玩家名称</td>
							<td style="width: 12%;">时间段</td>
							<td style="width: 10%;">发送房卡</td>
							<td style="width: 10%;">剩余房卡</td>
							<td style="width: 10%;"></td>
							<td style="width: 10%;"></td>
							<td style="width: 10%;"></td>
							
						</tr>
						<tr class="item" v-for="c in roomCard.data">
							<td ></td>
							<td >
								<div style="position: relative;width:40px;height:40px;">
									<img v-bind:src="c.headimgurl" />
									<div style="position: absolute;top:-10px;left:-16px;height: 18px;width: 40px;border-radius: 12px;background: rgb(250, 1, 0);color: yellow;font-size: 12px;text-align: center;"  v-if="c.is_agent==1">直营</div>
								</div>
							</td>
							<td >{{c.nickname}}</td>
							<td >{{c.from_date}}至{{c.to_date}}</td>
							<td >{{c.sum_count}}</td>
							<td >{{c.ticket_count}}</td>
							<td ><div class="noty" v-on:click="showNoty(c.account_id)">通知</div></td>
							<td ><div class="cardCancel" v-on:click="cardCancel(c.account_id,c.ticket_count)">注销房卡</div></td>
							<td ><div class="cardFrom" v-on:click="cardTo(c.account_id,c.nickname)">房卡去向</div></td>
						</tr>					
					</table>	
					<div style="width:100%;" v-show="roomCard.total_page>1">
					   <div class="tcdPageCode tcdPageCode" ></div>
					</div>	
				</div>
			</div>
		</div>	
		
		<div class="alert" v-show="alert.isShow" style="z-index: 11;">
			<div class="alertBackground"></div>
			<img src="../files/images/cms/alert1.png" v-show="alert.type==1">
			<img src="../files/images/cms/alert1.png" v-show="alert.type==2">
			<div class="alertText" >{{alert.text}}</div>
		</div>
		

		<div  class="cancelCard" v-show="roomCard.deleteInfo.is_show">
			<div class="back"></div>
			<div class="box1">
				<div class="title">
					<a >注销房卡</a>
					<img src="<?php echo $base_url;?>files/images/back4.png"  v-on:click="hideCancelCard()">
				</div>
				<div class="main">
					<div style="height: 50px;" >
						房卡数量：{{roomCard.deleteInfo.totalNum}} 张					
					</div>
					<div style="height: 50px;" >
						输入数量：<input  v-model="roomCard.deleteInfo.deleteNum"  />						
					</div>
				</div>

				<div class="text">注销后将无法恢复，确认注销？</div>
				<div class="bottomButton" v-on:click="cancelCardCommit()">确定注销</div>

			</div>		
		</div>
		
		<div  class="noteBox" v-show="noty.isShow">
			<div class="back"></div>
			<div class="box1">
				<div class="title">
					<a >通知</a>
					<img src="<?php echo $base_url;?>files/images/back4.png"  v-on:click="hideNoty()">
				</div>
				<div class="main">
					<div style="height: 40px;" >
						通知内容				
					</div>
					<div>
						<textarea v-model="noty.content1" placeholder="请输入通知内容"></textarea>					
					</div>
				</div>
				<div class="bottomButton" v-on:click="sendMessage()">确定发送</div>
			</div>		
		</div>

	</div>
</body>
<script>
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

        appData.roomCard.from=start;
        appData.roomCard.to=end;
        appData.roomCard.page=1;
        appData.roomCard.total_page=1;
        httpModule.getRoomCardGone(globalData.account_id);
    });

    appData.request_1 = 0;
    appData.request_2 = 0;
    appData.request_3 = 0;
    
    $(document).on("mouseover",".search .item",function(){
	    $(this).find(".delete").show();
	});	
	$(document).on("mouseout",".search .item",function(){
	     $(this).find(".delete").hide();
	});	
	appData.roomCard.userList.push({
		account_id:globalData.account_id,
		isSelect:true,
		nickname:utf8to16(base64_decode(globalData.nickname)),
	}),
	httpModule.getRoomCardGone(globalData.account_id);
});

var globalData = {
	"baseUrl":"<?php echo $base_url;?>",
	"num":"<?php echo $num;?>",
	"account_id":"<?php echo $account_id;?>",
	"nickname":"<?php echo $nickname;?>",
    "today":"<?php echo date('Y-m-d');?>"
};

var httpModule = {
	getRoomCardGone: function (id) {  
    	appData.request_3 = 1;
        Vue.http.post(globalData.baseUrl + 'game/getRoomCardGone', {
        	"dealer_num":globalData.num,
        	"account_id":id,
        	"page":appData.roomCard.page,
            "from":appData.roomCard.from,
            "to":appData.roomCard.to
        }).then(function(response) {
        	appData.request_3 = 0;
            var bodyData = response.body;
            if (bodyData.result == 0) {       	      	
            	appData.roomCard.data=[];          	
				appData.roomCard.total_page=bodyData.sum_page;
				appData.roomCard.data=bodyData.data.concat();
				
				
            	if(appData.roomCard.total_page>1){
					 $(".tcdPageCode").createPage({
	                    pageCount:appData.roomCard.total_page,
	                    current:appData.roomCard.page,
	                    backFn:function(p){
	                        appData.roomCard.page =p;
	                        httpModule.getRoomCardGone();
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
        	"dealer_num":globalData.num,
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

	sendMessage: function () {  
    	appData.request_3 = 1;
        Vue.http.post(globalData.baseUrl + 'noty/sendMessage', {
        	"dealer_num":globalData.num,
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
}


var viewMethods = {
    showAlert:function(type,text){
		appData.alert.text=text;
		appData.alert.type=type;
		appData.alert.isShow=true;
		setTimeout(function(){appData.alert.isShow=false;},1500)
	},
	
    showQuit: function () {

        //logMessage("showQuitTable");
        window.location.href = globalData.baseUrl + "account/logout";
        
    },    

	listBreak: function (id) {
		for(var i=0;i<appData.roomCard.userList.length;i++){
			if(appData.roomCard.userList[i].account_id==id){
				appData.roomCard.userList.splice(i,appData.roomCard.userList.length-i);
				appData.roomCard.userList[i-1].isSelect=true;
			}
		}    
		httpModule.getRoomCardGone(appData.roomCard.userList[appData.roomCard.userList.length-1].account_id);
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
	cardTo:function(id,name){
		for(var i=0;i<appData.roomCard.userList.length;i++){
			appData.roomCard.userList[i].isSelect=false;
		};
		appData.roomCard.userList.push({
			account_id:id,
			isSelect:true,
			nickname:name,
		});
		appData.roomCard.page=1;
		httpModule.getRoomCardGone(id);
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
}

var appData = {
	noty:{
		isShow:false,		
		content:"",		
		content1:"系统检测到您的乱价行为，扣除掉500张房卡！请规范销售行为。",	
		id:0	
	},
	roomCard:{
		page:1,
		total_page:1,
		userList:[],
		data:[],
        from:globalData.today,
        to:globalData.today,
		deleteInfo:{
			is_show:false,
			id:0,
			totalNum:0,
			deleteNum:0,
		},
	},
	request_1:0,
	request_2:0,
	request_3:0,
	
	alert:{
		text:"",
		isShow:false,
	},
};


//Vue生命周期
var vueLife = {
    vmCreated: function () {
        logMessage('vmCreated');       
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
    methods: viewMethods,
    created: vueLife.vmCreated,
    updated: vueLife.vmUpdated,
    mounted: vueLife.vmMounted,
    destroyed: vueLife.vmDestroyed,
});



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
</script>
</html>

