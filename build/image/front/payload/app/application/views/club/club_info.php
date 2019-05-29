<html>
<head>
<meta charset="utf-8" >
<meta name="viewport" content="width=device-width,initial-scale=1.0, minimum-scale=1.0, maximum-scale=1.0, user-scalable=no"/>
<meta name="format-detection" content="telephone=no" />
<meta name="msapplication-tap-highlight" content="no" />
<title>公会信息</title>

    <link rel="stylesheet" href="<?php echo $image_url;?>files/css/loading.css">
    <script type="text/javascript" src="<?php echo $image_url;?>files/js/jquery-1.9.1.min.js"></script>
    <script type="text/javascript" src="<?php echo $image_url;?>files/js/vue.min.js" ></script>
    <script type="text/javascript" src="<?php echo $image_url;?>files/js/vue-resource.min.js" ></script>

</head>
<style>
*{padding: 0;margin:0;-webkit-tap-highlight-color:rgba(0,0,0,0);-webkit-backface-visibility: hidden;-webkit-overflow-scrolling: touch;}a {text-decoration: none;color: #fff;}ul {list-style: none;}input{border: none;outline:none}body{font-family: 'Helvetica Neue', Helvetica, 'Hiragino Sans GB', 'Microsoft YaHei', 微软雅黑, Arial, sans-serif;cursor: default;}
img{border: none;}html{height: 100%;}
.top{position: fixed;height: 10vw;line-height:10vw;z-index: 5;top:0vw;left:0;width:100%;padding: 5vw 0;background: #f1f1f1;}
.top .inner{height: 10vw;background: #fff;position: relative;line-height: 10vw;font-size:4vw;}
.top .inner .icon{float: left;height: 6vw;width:6vw;margin:2vw 0 0 5vw;}
.top .inner .text{float: left;margin-left:4vw;}
.top .inner .applyArea{position: absolute;top:0;left: 0;height:100%;width:70vw;}
.top .inner .refresh{float: right;margin-right:9vw; color: #177ad7;}
.top .inner .refreshImg{float: right;height: 4vw;width:4vw;margin:2.5vw 2vw 0 0;}
.top .inner .applyNum{position: absolute;top:.5vw;left:10vw;background:#f15352;color: #fff;text-align:center;width:4vw;height:4vw;border-radius:2vw;line-height: 5vw;font-size:2.4vw;}

.body{position: absolute;width: 100%;}
.back0{position: absolute;top:0;left:0;width:100%;height: 100%;z-index: 1}
.sort_box{width: 100%;overflow: hidden;background: #fff;position: relative;margin-top: 20vw;}
.sort_list{padding:2vw 10vw 2vw 20vw;position: relative;height: 16vw;line-height: 16vw;border-bottom:1px solid #ddd;}
.sort_list .num_logo{width: 13vw;height: 13vw;border-radius: 1vw;overflow: hidden;position: absolute;top: 3.5vw;left: 4vw;text-align: center;}
.sort_list .num_logo img{max-width: 13vw;max-height: 13vw;}
.sort_list .info{line-height: 5vw;font-size: 4vw;}
.sort_list .num_name{font-size: 3.5vw;overflow: hidden;text-overflow:ellipsis;white-space: nowrap;}
.sort_list .num_id{color:#999;font-size:3.6vw;}
.sort_list .num_bean{margin-top: 0;}
.sort_list .num_bean img{float: left;width: 6vw;position: relative;z-index: 2;}
.sort_list .num_bean .text{float: left;background: #ffda89;padding: 0 4vw 0 6vw;font-size: 3.4vw;line-height: 5vw;border-radius: 2.5vw;margin-left: -4vw;margin-top: 1vw;position: relative;z-index: 1;}
.sort_list .operation{float: right;margin-top:2.5vw;position: relative;}
.sort_list .operation .mainButton{height: 11vw;width:11vw;border-radius:5.5vw;background: #fdc317;text-align: center;line-height: 11vw;font-size: 3.5vw;float: right;z-index: 2;position: relative;}
.sort_list .operation .secondButton{height: 7vw;width:36vw;border-radius:1.5vw;background: #fdc317;text-align: center;line-height: 7vw;font-size: 3.5vw;position: absolute;z-index: 2;padding: 2vw 2vw;right: 14vw;top:0;}
.sort_list .operation .secondButton .triangle{position: absolute;top:4.4vw;right:-2vw;width:0;height:0;border-top: 1.2vw solid transparent;border-bottom: 1.2vw solid transparent;border-left:2vw solid #fdc317 }
.sort_list .operation .secondButton .item{float: right;width: 11vw;}
.sort_list .operation .secondButton .border{float: right;border-right:1px solid #333;}
.sort_letter{height: 5vw;line-height: 5vw;padding-left: 4vw;color:#787878;font-size: 3vw;border-bottom:1px solid #ddd;background: #f1f1f1; }
.initials{position: fixed;top: 13vw;right: 0px;height: 100%;width: 15px;padding-right: 10px;text-align: center;font-size: 12px;z-index: 15;background: rgba(145,145,145,0);}
.initials li img{width: 14px;}
</style>
<body  >
	<div id="app-main" style="position: relative;background: #f1f1f1;min-height: 100vh;" v-cloak>
		<div  class="top">
			<div  class="inner">
				<img src="<?php echo $base_url;?>files/club/images/newApply.jpg" class="icon"  v/>
				<div class="applyNum" v-if="applyCount!=0">{{applyCount}}</div>
				<div class="text" >新的申请</div>
				<div  v-on:click="newAppylPage" class="applyArea"></div>
				<div class="refresh" v-on:click="reload">刷新</div>
				<img class="refreshImg" src="<?php echo $base_url;?>files/club/images/refresh.png" v-on:click="reload" />
			</div>
		</div>	
		<div class="body">

			<div class="sort_box" >
				<div class="sort_list" v-for="u in userList">
					<div class="num_logo">
						<img v-bind:src="u.head" alt="">
					</div>
					<div class="operation">
						<div class="mainButton" v-on:click="showOptDetail(u.ucode)">操作</div>
                        <div class="mainButton" style="margin-right: 10px;background: #b3e6f1" v-on:click="showSendBean(u.ucode)">赠豆</div>
                        <div class="secondButton" v-if="u.isShowOpt&&u.ucode!=acode" >
							<div class="triangle"></div>
							<div class="item" style="margin-right: 1.5vw;" v-on:click="detailPage">明细</div>
<!--							<div class="item border" v-on:click="showSendBean">赠豆</div>-->
							<div class="item border" v-on:click="showOutBean">减豆</div>
							<div class="item border" v-on:click="showKickOut">踢出</div>
                        </div>
                        <div class="secondButton" v-if="u.isShowOpt&&u.ucode==acode" style="width: 25vw;">
                            <div class="triangle"></div>
                            <div class="item" style="margin-right: 1.5vw;" v-on:click="detailPage">明细</div>
                            <div class="item border" v-on:click="showOutBean">减豆</div>
                            <!--<div class="item border" v-on:click="showSendBean">赠豆</div>-->
						</div>
					</div>
					<div class="info">
						<div class="num_name">{{u.nick}}</div>
						<div class="num_id">ID:{{u.ucode}}</div>
						<div class="num_bean">
							<img src="<?php echo $base_url;?>files/club/images/bean.png"/>
							<div class="text">{{u.bean}}</div>
						</div>	
					</div>
					
				</div>
			</div>
			<div class="initials"><ul><li></li></ul></div>
			<div class="back0" v-on:click="hideOptDetail"></div>
		</div>	

		
		<div class="delete" v-show="kickOut.isShow">
			<div class="greyBack" v-on:click="hideKickOut"> </div>
			<div class="box">
				<div class="title">您将要踢出</div>
				<div class="info">
					<img v-bind:src="selected.head" />
					<div class="name">{{selected.nick}}（ID：{{selected.ucode}}）</div>
				</div>
<!--				<input placeholder="请输入秘钥" v-model="kickOut.secret"/>-->
<!--				<div class="note" v-show="kickOut.isShow1" style="top: 47vw;">{{kickOut.text1}}</div>-->
				<div class="button" style="margin-top: 32vw" v-on:click="removeUser">确认</div>
			</div>
		</div>
		
		<div class="addBean" v-show="sendBean.isShow">
			<div class="greyBack" v-on:click="hideSendBean"> </div>
			<div class="box">
				<div class="title">赠豆</div>
				<div class="beanNum">欢乐豆：{{selected.bean}}</div>
				<div class="aim">成员：{{selected.nick}}</div>
				<input placeholder="赠豆数量" v-model="sendBean.num" />
				<div class="note" v-show="sendBean.isShow1" style="top: 50vw;">{{sendBean.text1}}啊</div>
<!--				<input placeholder="输入秘钥" v-model="sendBean.secret" />-->
<!--				<div class="note"  v-show="sendBean.isShow2" style="top: 65.5vw;">{{sendBean.text2}}啊</div>-->
				<div class="button" v-on:click="addBean">确认赠送</div>
			</div>
		</div>

		<div class="addBean" v-show="outBean.isShow">
			<div class="greyBack" v-on:click="hideOutBean"> </div>
			<div class="box">
				<div class="title">减豆</div>
				<div class="beanNum">欢乐豆：{{selected.bean}}</div>
				<div class="aim">成员：{{selected.nick}}</div>
				<input placeholder="减豆数量" v-model="outBean.num" />
				<div class="note" v-show="outBean.isShow1" style="top: 50vw;">{{outBean.text1}}啊</div>
<!--				<input placeholder="输入秘钥" v-model="outBean.secret" />-->
<!--				<div class="note"  v-show="outBean.isShow2" style="top: 65.5vw;">{{outBean.text2}}啊</div>-->
				<div class="button" v-on:click="offBean">确认减豆</div>
			</div>
		</div>

		<div class="alert1" v-show="isShowAlertB">
			<div class="alertBackground"></div>
			<img src="<?php echo $base_url;?>files/club/images/alert1.png" >
			<div class="alertText" >{{alertTextB}}</div>
		</div>
		
		<div class="waiting" v-show="is_operation">
			<div class="waitingBack"></div>
			<div class="load4">
				<div class="loader"></div>
			</div>
		</div>
	</div>

</body>
<style>
.greyBack{position: absolute;top:0;left:0;width:100%;height: 100%;background: #000;opacity:.4;}	
.delete{position: fixed;top:0;left:0;width:100%;height: 100%;z-index: 16;}

.delete .box{position: absolute;top:25vw;left:5vw;width:90%;height: 78vw;border-radius:1.5vw;background: #fff;}
.delete .box .title{text-align: center;line-height: 16vw;font-size: 4.5vw;}
.delete .box .info{line-height: 10vw;font-size: 4vw;width: 70vw;margin-left: 10vw;margin-top: 4vw;}
.delete .box .info img{height: 10vw;width:10vw;float: left;}
.delete .box .info .name{float: left;margin-left: 3vw}
.delete .box input{margin-left: 10vw;width: 66vw;background: #f0f0f2;border-radius:1.5vw;height: 11vw;margin-top:5vw;padding: 0 2vw;font-size: 4vw;}
.delete .box .button{margin-left: 25vw;width: 40vw;background: #ff5555;color: #fff;border-radius:1.5vw;height: 11vw;margin-top:10vw;text-align: center;line-height: 11vw;font-size: 3.6vw;}
.delete .box .note{position: absolute;width: 70vw;left:10vw;color: #ff5555;font-size:3vw;}
.addBean{position: fixed;top:0;left:0;width:100%;height: 100%;z-index: 16;}

.addBean .box{position: absolute;top:25vw;left:5vw;width:90%;height: 99vw;border-radius:1.5vw;background: #fff;}
.addBean .box .title{text-align: center;line-height: 16vw;font-size: 4.5vw;}
.addBean .box .beanNum{margin-left: 10vw;height: 8vw;margin-top:5vw;font-size: 3.6vw;}
.addBean .box .aim{margin-left: 10vw;height: 8vw;font-size: 3.6vw;margin-bottom: 2vw;}
.addBean .box input{margin-left: 10vw;width: 66vw;background: #f0f0f2;border-radius:1.5vw;height: 11vw;margin-bottom:4.5vw;padding: 0 2vw;font-size: 3.6vw;}
.addBean .box .button{margin-left: 25vw;width: 40vw;background: #6d7dd4;color: #fff;border-radius:1.5vw;height: 11vw;margin-top:5vw;text-align: center;line-height: 11vw;font-size: 3.6vw;}
.addBean .box .note{position: absolute;width: 70vw;left:10vw;color: #ff5555;font-size:3vw;}

.alert1{position: fixed;z-index: 9997;width: 160px;height: 160px;top:50%;left: 50%;margin-top: -80px;margin-left: -80px;border-radius:5px;overflow:hidden;}
.alert1 .alertBackground{filter:alpha(opacity=50);-moz-opacity:0.5;opacity:0.5;background: #000;height: 100%;width: 100%;}
.alert1 img{position: absolute;top:15px;left: 29px;}
.alert1 .alertText{position: absolute;top:115px;width:160px;text-align: center;font-size: 18px;color: #fff;filter:alpha(opacity=95);-moz-opacity:0.95;opacity:0.95;}


.waiting{position: fixed;width:100%;height:100%;top:0;left:0;z-index: 111;} 
.waiting .waitingBack{position: fixed;width:100%;height:100%;top:0;left:0;background: #000;opacity:.5;} 
</style>
<script >
var appData={	
	baseUrl:'<?php echo $base_url;?>',
	club_no:'<?php echo $club_no;?>',
	data:"",
	userList:[],
	applyCount:<?php echo $applyCount;?>,
	selected:"",
	sendBean:{
		isShow:false,
		num:"",
		// secret:"",
		isShow1:false,
		isShow2:false,
		text1:"",
		text2:""
	},
	outBean:{
		isShow:false,
		num:"",
		// secret:"",
		isShow1:false,
		isShow2:false,
		text1:"",
		text2:""
	},
	kickOut:{
		isShow:false,
		// secret:"",
		isShow1:false,
		text1:""
	},
	isShowAlertB:false,
	alertTextB:"",
	is_operation:false,
	acode:"<?php echo $adminCode;?>"
};
var globalData={
	"apiUrl":'<?php echo $base_url;?>'
};
var orgData = {
	"userTotal":"<?php echo $nowPlayer;?>",
	"userMax":"<?php echo $maxPlayer;?>"
};

document.title = '成员管理(' + orgData.userTotal + '/' + orgData.userMax + ')';

var methods={
	members:function(){
		Vue.http.get(globalData.apiUrl+'club/members?club_no='+appData.club_no+'&t='+ Math.round(new Date().getTime()/1000)).then(function(res){
        	var bodyData = res.data;
            if (bodyData.code == 0) {
            	for(var i=0;i<bodyData.data.length;i++){
					bodyData.data[i].firstChars=getFullChars(makePy(bodyData.data[i].nick)[0]);
					bodyData.data[i].isShowOpt=false;
				}
            	appData.userList=bodyData.data.concat();
				setTimeout(function(){
					startSort();		
				},10)

            } else {
               console.log(res.data);
            }                  
        },function(res){
            console.log(res.data);
        });
	},
	removeUser:function(){
		appData.kickOut.isShow1=false;	
		// if(appData.kickOut.code==""){
		// 	appData.kickOut.isShow1=true;
		// 	appData.kickOut.text1="请输入秘钥";
		// 	return;
		// }
		appData.is_operation=true;
		Vue.http.post(globalData.apiUrl+'club/kick',{
			club_no:appData.club_no,
			// secret:appData.kickOut.secret,
            ucode:appData.selected.ucode
		}).then(function(res){
			appData.is_operation=false;
        	var bodyData = res.data;
            if (bodyData.code == 0) {
        		for(var i=0;i<appData.userList.length;i++){
					if(appData.userList[i].ucode==appData.selected.ucode){
						appData.userList.splice(i,1);
						break;
					}
				}
            	appData.kickOut.isShow=false;
            	methods.showAlertB("删除成功");          	
            } else {
               appData.kickOut.isShow1=true;
	           appData.kickOut.text1=bodyData.msg;
            }                  
        },function(res){
            console.log(res.data);
        });
	},
	addBean:function(){
		appData.sendBean.isShow1=false;
		appData.sendBean.isShow2=false;
		if(!isPositiveInteger(appData.sendBean.num)){
			appData.sendBean.isShow1=true;
			appData.sendBean.text1="赠豆数量必须是正整数";
			return;
		}
		// if(appData.sendBean.secret==""){
		// 	appData.sendBean.isShow2=true;
		// 	appData.sendBean.text2="请输入秘钥";
		// 	return;
		// }
		appData.is_operation=true;
		Vue.http.post(globalData.apiUrl+'club/dealBean',{
		    action:'send',
			club_no:appData.club_no,
			ucode:appData.selected.ucode,
			// secret:appData.sendBean.secret,
			bean:appData.sendBean.num
		}).then(function(res){
			appData.is_operation=false;
        	var bodyData = res.data;
            if (bodyData.code == 0) {
            	for(var i=0;i<appData.userList.length;i++){
					if(appData.userList[i].ucode==appData.selected.ucode){
						appData.userList[i].bean = parseInt(appData.userList[i].bean) + parseInt(appData.sendBean.num);
						break;
					}
				}  
            	appData.sendBean.isShow=false;
            	methods.showAlertB("赠送成功");
      	
            } else {
                appData.sendBean.isShow2=true;
	            appData.sendBean.text2=bodyData.msg;
            }                  
        },function(res){
            console.log(res.data);
        });
	},	
	offBean:function(){
		appData.outBean.isShow1=false;
		appData.outBean.isShow2=false;
		if(!isPositiveInteger(appData.outBean.num)){
			appData.outBean.isShow1=true;
			appData.outBean.text1="减豆数量必须是正整数";
			return;
		}
		// if(appData.outBean.secret==""){
		// 	appData.outBean.isShow2=true;
		// 	appData.outBean.text2="请输入秘钥";
		// 	return;
		// }
		appData.is_operation=true;
		Vue.http.post(globalData.apiUrl+'club/dealBean',{
		    action:'out',
			club_no:appData.club_no,
			ucode:appData.selected.ucode,
			// secret:appData.outBean.secret,
			bean:appData.outBean.num
		}).then(function(res){
			appData.is_operation=false;
        	var bodyData = res.data;
            if (bodyData.code == 0) {
            	for(var i=0;i<appData.userList.length;i++){
					if(appData.userList[i].ucode==appData.selected.ucode){
						appData.userList[i].bean = parseInt(appData.userList[i].bean) - parseInt(appData.outBean.num);
						break;
					}
				}
            	appData.outBean.isShow=false;
            	methods.showAlertB("减豆成功");

            } else {
                appData.outBean.isShow2=true;
	            appData.outBean.text2=bodyData.msg;
            }
        },function(res){
            console.log(res.data);
        });
	},
	newAppylPage:function(){
		window.location.href=appData.baseUrl + "club/applyList?club_no=" + appData.club_no;
	},
	showOptDetail:function(ucode){
		for(var i=0;i<appData.userList.length;i++){
			appData.userList[i].isShow = false;
			if(appData.userList[i].ucode==ucode){
				appData.userList[i].isShowOpt = true;
				appData.selected = appData.userList[i];
			}		
		}
	},	
	hideOptDetail:function(){
		for(var i=0;i<appData.userList.length;i++){
			appData.userList[i].isShowOpt = false;	
		}
	},
	detailPage:function(){
		window.location.href=appData.baseUrl + "club/beanDetail?club_no="+appData.club_no+"&ucode="+appData.selected.ucode;
	},	
	showSendBean:function(ucode){
        for(var i=0;i<appData.userList.length;i++){
            appData.userList[i].isShow = false;
            if(appData.userList[i].ucode==ucode){
                appData.userList[i].isShowOpt = true;
                appData.selected = appData.userList[i];
            }
        }
		methods.hideOptDetail();
		appData.sendBean.isShow=true;
		appData.sendBean.num="";
		// appData.sendBean.secret="";
		appData.sendBean.isShow1=false;
		appData.sendBean.isShow2=false;

	},	
	hideSendBean:function(){
		appData.sendBean.isShow=false;
	},
	showOutBean:function(){
		methods.hideOptDetail();
		appData.outBean.isShow=true;
		appData.outBean.num="";
		// appData.outBean.secret="";
		appData.outBean.isShow1=false;
		appData.outBean.isShow2=false;

	},
	hideOutBean:function(){
		appData.outBean.isShow=false;
	},
	
	showKickOut:function(){
		methods.hideOptDetail();
		appData.kickOut.isShow=true;
		// appData.kickOut.secret="";
		appData.kickOut.isShow1=false;
	},	
	hideKickOut:function(){
		appData.kickOut.isShow=false;
	},	
	showAlertB:function(text){
		appData.isShowAlertB=true;
		appData.alertTextB=text;
		setTimeout(function(){
			appData.isShowAlertB=false;
		},700);
	},	
	reload:function(){
		window.location.reload();
	}

};
//Vue生命周期
var vueLife = {
    vmCreated: function() {
       logMessage('vmCreated');
       methods.members();
    },
    vmUpdated: function() {
        logMessage('vmUpdated');
    },
    vmMounted: function() { 

    },
    vmDestroyed: function() {
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


function isPositiveInteger(s){//是否为正整数
     var re = /^[1-9][0-9]*$/ ;
     return re.test(s)
 }   
function getFullChars(str){    
    if(str.length>0){
        var first = str.substr(0,1).toUpperCase();
        var spare = str.substr(1,str.length);
        return first;
    }
}
function logMessage(msg){
	console.log(msg)
}
</script>


<script type="text/javascript" src="<?php echo $base_url;?>files/club/js/jquery.charfirst.pinyin.js"></script>
<script type="text/javascript" src="<?php echo $base_url;?>files/club/js/sort.js"></script>
</html>

