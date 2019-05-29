<html>
<head>
<meta charset="utf-8" >
<meta name="viewport" content="width=device-width,initial-scale=1.0, minimum-scale=1.0, maximum-scale=1.0, user-scalable=no"/>
<meta name="format-detection" content="telephone=no" />
<meta name="msapplication-tap-highlight" content="no" />
<title>欢乐豆明细</title>
<link rel="shortcut icon" type="image/x-icon" href="http://fy.one168.com/files/images/card/logo.png" />

    <script type="text/javascript" src="<?php echo $image_url;?>files/js/jquery-1.9.1.min.js"></script>
    <script type="text/javascript" src="<?php echo $image_url;?>files/js/vue.min.js" ></script>
    <script type="text/javascript" src="<?php echo $image_url;?>files/js/vue-resource.min.js" ></script>

</head>
<style>
*{padding: 0;margin:0;-webkit-tap-highlight-color:rgba(0,0,0,0);-webkit-backface-visibility: hidden;-webkit-overflow-scrolling: touch;}a {text-decoration: none;}ul {list-style: none;}input{border: none;outline:none}body{font-family: 'Helvetica Neue', Helvetica, 'Hiragino Sans GB', 'Microsoft YaHei', 微软雅黑, Arial, sans-serif;cursor: default;}
img{border: none;}html{height: 100%;}

.item{line-height: 10vw;width: 92%;margin-left:4%;border-bottom:1px solid #d9d9d9;font-size: 4vw;padding: 3vw 0;}
.item .dot{float: left;width: 3vw;height:3vw;border-radius:1.5vw;margin-right: 2vw;margin-top: 3.5vw;}
.item .time{float: left;margin-right: 2vw;}
.item .inner{width: 64vw;margin-left: 28vw;}
.item .green1{background:#06a98c; }
.item .yellow1{background:#feb91d; }
.item .green2{color:#06a98c; }
.item .yellow2{color:#feb91d; }

.addMore{height: 12vw;line-height: 12vw;text-align: center;}
</style>
<body  >
	<div id="app-main"  v-cloak>
		<div style="position: fixed;width: 100%;height: 100%;background-color: white;z-index: 9999;" v-show="beanList.length==0">
        	<img src="<?php echo $base_url;?>files/club/images/info_norecords.png" style="position: absolute;top: 20vh;left:50%;margin-left: -40vw;width: 80vw;" >
        </div>
		<div  class="item" v-for="b in beanList">
			<div class="dot green1" v-if="b.type==1"></div>
			<div class="dot yellow1" v-if="b.type==2"></div>
			<div class="time">{{b.time}}</div>
			<div class="inner">
				<a class="green2" v-if="b.type==1">{{b.content}}</a>
				<a class="yellow2" v-if="b.type==2">{{b.content}}</a>
				<a class="restBean">,余额{{b.balance}}豆.</a>	
			</div>
			
		</div>	
	
		<div class="addMore" v-if="page!=totalPage&&totalPage!=0" v-on:click="listJournals">点击加载更多</div>
		<div style="height:6vw;"></div>
		
	</div>
</body>

<script >
var appData={
	beanList:[],
	club_no:'<?php echo $club_no;?>',
	page:0,
	totalPage:0
};
var userData = {
	"user_code":"<?php echo $ucode;?>"
};
var globalData={
	"apiUrl":'<?php echo $base_url;?>'
};
var methods={
	listJournals:function(){
		appData.page=appData.page+1;
		Vue.http.get(globalData.apiUrl+'club/beans?club_no='+appData.club_no+"&page="+appData.page+'&ucode='+userData.user_code).then(function(res){
        	var bodyData = res.data;
            if (bodyData.code == 0) {
            	appData.beanList = appData.beanList.concat(bodyData.data);
				appData.totalPage = bodyData.totalPage;
            } else {
               console.log(res.data);
            }
        },function(res){
            console.log(res.data);
        });
	}
};
//Vue生命周期
var vueLife = {
    vmCreated: function() {
       methods.listJournals();
    },
    vmUpdated: function() {
    },
    vmMounted: function() {
    },
    vmDestroyed: function() {
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
</script>



</html>

