<html>
<head>
<meta charset="utf-8" >
<meta name="viewport" content="width=device-width,initial-scale=1.0, minimum-scale=1.0, maximum-scale=1.0, user-scalable=no"/>
<meta name="format-detection" content="telephone=no" />
<meta name="msapplication-tap-highlight" content="no" />
<title>欢乐豆统计</title>
<link rel="shortcut icon" type="image/x-icon" href="http://fy.one168.com/files/images/card/logo.png" /> 

<script src="https://gameoss.fexteam.com/files/js/jquery-1.9.1.min.js"></script>
<script type="text/javascript" src="https://gameoss.fexteam.com/files/js/vue.min.js" ></script>
<script type="text/javascript" src="https://gameoss.fexteam.com/files/js/vue-resource.min.js" ></script> 



</head>
<style>
*{padding: 0;margin:0;-webkit-tap-highlight-color:rgba(0,0,0,0);-webkit-backface-visibility: hidden;-webkit-overflow-scrolling: touch;}a {text-decoration: none;color: #fff;}ul {list-style: none;}input{border: none;outline:none}body{font-family: 'Helvetica Neue', Helvetica, 'Hiragino Sans GB', 'Microsoft YaHei', 微软雅黑, Arial, sans-serif;cursor: default;}
img{border: none;}html{height: 100%;}

.item{height: 32vw;line-height: 12vw;width: 92%;margin-left:4%;border-bottom:1px solid #d9d9d9;font-size: 4vw;padding: 4vw 0;}
.item .dot{float: left;width: 3vw;height:3vw;border-radius:1.5vw;margin-right: 2vw;margin-top: 4.5vw;background: #feb91d;}
.item .time{margin-right: 2vw;}
.item .beanList{width: 92vw;}
.item .beanList .beanItem{float: left;width: 40vw;margin-left: 5vw;line-height: 10vw;}
.item .beanList .beanItem a{color:#feb91d;}
.addMore{height: 12vw;line-height: 12vw;text-align: center;}
</style>
<body  >
	<div id="app-main"  v-cloak>
		<div style="position: fixed;width: 100%;height: 100%;background-color: white;z-index: 9999;" v-show="beanList.length==0">
        	<img src="https://gameoss.fexteam.com/files/images/info_norecords.png" style="position: absolute;top: 20vh;left:50%;margin-left: -40vw;width: 80vw;" >
        </div>
		<div  class="item" v-for="b in beanList">
			<div class="dot" ></div>
			<div class="time">{{b.statistics_day1}}</div>
			<div class="beanList">
				<div class="beanItem"><a>流通：</a>{{b.circulate}}</div>
				<div class="beanItem" v-on:click="gameBeanInfo(b.statistics_day)"><a>游戏消耗：</a>{{b.game_consumption}}</div>
				<div class="beanItem" v-on:click="addBeanInfo(b.statistics_day)"><a>新增：</a>{{b.increment}}</div>
				<div class="beanItem" v-on:click="exchangeBeanInfo(b.statistics_day)"><a>兑换消耗：</a>{{b.exchange_consumption}}</div>	
			</div>
		</div>	
	
		<div class="addMore" v-if="page!=totalPage&&totalPage!=0" v-on:click="statisticsData">点击加载更多</div>
		<div style="height:6vw;"></div>
		
	</div>
</body>

<script >



var appData={
	orgId:'263',
	page:0,
	pageSize:10,
	totalPage:1,
	beanList:[],
	baseUrl:'http://fy.one168.com/',
};
var userData = {
	"accountId":"2619",
	"s":"27714b4bb3a4c671f8653fd8166314ea",
};
var globalData={
	"apiUrl":'http://fyweb.fexteam.com/'
}
var methods={
	statisticsData:function(){
		appData.page=parseInt(appData.page)+1;
		Vue.http.get(globalData.apiUrl+'org/assert/statisticsData?orgId='+appData.orgId+"&page="+appData.page+"&pageSize="+appData.pageSize+'&aid='+userData.accountId+'&s='+userData.s).then(function(res){
        	var bodyData = res.data;
            if (bodyData.result == 0) {           	
            	appData.beanList=appData.beanList.concat(bodyData.data.balanceStatistics);   
            	appData.page=bodyData.data.page;
            	appData.totalPage=bodyData.data.totalPage;
            	for(var i=0;i<appData.beanList.length;i++){
					appData.beanList[i].statistics_day1=timestampToTime(appData.beanList[i].statistics_day);
				}
            } else {
               console.log(res.data);
            }                  
        },function(res){
            console.log(res.data);
        });
	},
	gameBeanInfo:function(date){	
		window.location.href=appData.baseUrl + "main/gameDis?orgId=" + appData.orgId+"&date="+date;
	},	
	addBeanInfo:function(date){
		window.location.href=appData.baseUrl + "main/sendBean?orgId=" + appData.orgId+"&date="+date;
	},	
	exchangeBeanInfo:function(date){
		window.location.href=appData.baseUrl + "main/exchangeDis?orgId=" + appData.orgId+"&date="+date;
	},	
};
//Vue生命周期
var vueLife = {
    vmCreated: function() {
       logMessage('vmCreated');     
       methods.statisticsData();   
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




function timestampToTime(timestamp) {
    var date = new Date(timestamp * 1000);//时间戳为10位需*1000，时间戳为13位的话不需乘1000
    Y = date.getFullYear() + '年';
    M = (date.getMonth()+1 < 10 ? (date.getMonth()+1) : date.getMonth()+1) + '月';
    D = date.getDate() + '日 ';
    h = date.getHours() + ':';
    m = date.getMinutes() + ':';
    s = date.getSeconds();
    return Y+M+D;
}

function logMessage(msg){
	console.log(msg)
}
</script>


</html>

