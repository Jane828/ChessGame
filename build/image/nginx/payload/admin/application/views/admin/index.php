<!DOCTYPE html>
<html v-app="app">
<head>
	<meta charset="utf-8">
	<title>太古游戏</title>
	<link rel="stylesheet" type="text/css" href="<?php echo $base_url;?>files/css/index.css" />
	<link href="<?php echo $base_url;?>files/css/perfect-scrollbar.css" rel="stylesheet">
	<link rel="stylesheet" type="text/css" media="all" href="<?php echo $base_url;?>files/css/daterangepicker-bs3.css" />
	<link rel="stylesheet" href="<?php echo $base_url;?>files/css/loading.css">

	<script src="<?php echo $base_url;?>files/js/jquery-1.9.1.min.js"></script>
	<script type="text/javascript" src="<?php echo $base_url;?>files/js/moment.js"></script>
    <script type="text/javascript" src="<?php echo $base_url;?>files/js/daterangepicker.js"></script>
    
    


	<script type="text/javascript" src="<?php echo $base_url;?>files/js/vue.min.js" ></script>
	<script type="text/javascript" src="<?php echo $base_url;?>files/js/vue-resource.min.js" ></script>

	<script src="<?php echo $base_url;?>files/js/jquery.page.js"></script>		
	<script type="text/javascript" src="<?php echo $base_url;?>files/js/md5.js"></script>

<script type="text/javascript">
	var globalData = {
		"baseUrl":"<?php echo $base_url;?>",
		"fromDate":"<?php echo $from;?>",
		"toDate":"<?php echo $to;?>",
		"today":"<?php echo date('Y-m-d');?>"
	};


</script>

<style>
    .testswitch {
        position: relative;
        width: 90px;
        margin: 0;
        -webkit-user-select:none;
        -moz-user-select:none;
        -ms-user-select: none;
    }

    .testswitch-checkbox {
        display: none;
    }

    .testswitch-label {
        display: block;
        overflow: hidden;
        cursor: pointer;
        border: 2px solid #999999;
        border-radius: 20px;
    }

    .testswitch-inner {
        display: block;
        width: 200%;
        margin-left: -100%;
        transition: margin 0.3s ease-in 0s;
    }

    .testswitch-inner::before, .testswitch-inner::after {
        display: block;
        float: right;
        width: 50%;
        height: 30px;
        padding: 0;
        line-height: 30px;
        font-size: 14px;
        color: white;
        font-family:
                Trebuchet, Arial, sans-serif;
        font-weight: bold;
        box-sizing: border-box;
    }

    .testswitch-inner::after {
        content: attr(data-on);
        padding-left: 10px;
        background-color: #00e500;
        color: #FFFFFF;
    }

    .testswitch-inner::before {
        content: attr(data-off);
        padding-right: 10px;
        background-color: #EEEEEE;
        color: #999999;
        text-align: right;
    }

    .testswitch-switch {
        position: absolute;
        display: block;
        width: 22px;
        height: 22px;
        margin: 4px;
        background: #FFFFFF;
        top: 0;
        bottom: 0;
        right: 56px;
        border: 2px solid #999999;
        border-radius: 20px;
        transition: all 0.3s ease-in 0s;
    }

    .testswitch-checkbox:checked + .testswitch-label .testswitch-inner {
        margin-left: 0;
    }

    .testswitch-checkbox:checked + .testswitch-label .testswitch-switch {
        right: 0px;
    }
</style>


</head>
<body  style="float: left;"id="body" >

	<div id="app-main">
	<div style="position: fixed;width:100%;height:100%;top:0;left:0;background: #fff;z-index:100;filter:alpha(Opacity=60);-moz-opacity:0.3;opacity: 0.3" id="loading" v-show="request_loading==1||request_1==1||request_2==1||request_3==1">
		<div class="load4">
			<div class="loader">Loading...</div>
		</div>
	</div>
		<div class="scrollStop" >
			<div style="height: 86px;margin-bottom:40px;background: #fff;">
				<div style="width: 1226px;margin:0 auto;">
					<img src="<?php echo $base_url;?>files/images/logo.png" style="float: left;height: 50px;margin-top: 18px;">
					<div style="float: left;width:100px;text-align: center;font-size:18px;height:24px;margin-top:31px; ">太古游戏</div>
					<div style="float: right;">
						<div class="click" style="float: right;width:100px;text-align: center;margin-right:15px;color:#ac1f1f;font-size:18px;height:24px;margin-top:31px; "  v-on:click="showQuit()" >退出</div>
					</div>
				</div>
			</div>
			<div class="outline" v-show="1">
				<div class="outline1" style="min-height: 100%;" >
					<div class="leftPart">					
						<div class="accountInput" v-show="0">	
							<div  class="adBtn" ><div class="addBtnText" v-on:click="editAd()">轮播广告</div></div>				
						</div>
						<div v-for="d in dealerList" class="dealerListMenu" >
							<div v-show="selectDealerID!=d.dealer_id" v-on:click="selectDealer(d)" class="unselectedDealer"><div class="nameText">{{d.name}}</div></div>
							<div v-show="selectDealerID==d.dealer_id" v-on:click="selectDealer(d)" class="selectedDealer">
								<div class="nameText">{{d.name}}</div>
							</div>
						</div>
					</div>	

					<div class="rightPart" style="background:#ffffff;min-height: 800px;">
						<div class="devicePart" v-show="selectPart==0">
							<div class="dealerTitle">
								<div style="margin-left: 50px;" class="dealerName">
									<div class="dealerNameText" >{{selectDealerName}}</div>
									<div class="paymentType_1" v-show="selectDealerPaymentType==1">代收费</div>
									<div class="paymentType_2" v-show="selectDealerPaymentType==2">自收费</div>
								</div>
								<div style="margin-left: 50px;" class="ticketCount" v-show="selectDealerID!=-1">
									房卡库存：{{inventoryCount}}张  <span class="rechargeBtn" v-on:click="openRechargePart()">充值</span>
									  
								</div>

							</div>
							<div class="ad" v-show="0&&ad.isShow">
								<div class="adSet">
									<div class="title">轮播广告</div>
									<div class="timeSelect">
										<div class="wordRight">时间选择：</div>
										
										<div class="control-group" style="float: left;">
							                <div class="controls">
								                <div class="input-prepend input-group">
								               		<input type="text" readonly style="width: 350px;height:38px;cursor:pointer;border:1px solid #e2e2e2;padding: 0 5px;background: url('<?php echo $base_url;?>files/images/cms/down.png') no-repeat scroll right center transparent;" name="reservation" id="reservationAd" class="form-control" value="<?php echo date("Y-m-d");?> - <?php echo date("Y-m-d");?>" /> 
								                </div>
							                </div>
						                </div>
						                <input class="minute" style="margin-left: 15px;" v-model="ad.start_time.hour" />点
						                <input class="minute" v-model="ad.start_time.minute" />分 到  
						                <input class="minute" v-model="ad.end_time.hour" />点
						                <input class="minute" v-model="ad.end_time.minute" />分				            					               
						               
									</div>	
									<div class="timeSelect">
										<div class="wordRight">时&nbsp&nbsp&nbsp&nbsp&nbsp&nbsp长:</div>
										<input class="lastTime" v-model="ad.second" />秒
									</div>	
									<div class="adInfo">
										<div class="wordRight">内&nbsp&nbsp&nbsp&nbsp&nbsp&nbsp容:</div>
										<textarea v-model="ad.content1" placeholder="请输入播放内容"></textarea>
									</div>	
									<div class="adSend">
										<div class="sendButton" v-on:click="sendNoty()">发送</div>
									</div>	
								</div>
								<div class="adRecord">
									<div class="title">广播记录</div>
									
									
									<table class="adList" cellspacing="0" >
										<tr style="height: 40px;background: #f5f5f5;">
											<td style="width: 4%;"></td>
											<td style="width: 60%;">时间</td>
											<td style="width: 22%;">时长</td>
											<td style="width: 14%;">状态</td>
										</tr>	
				
										<tr class="item" v-for="a in ad.data" >
											<td ></td>
											<td >
												<div  class="time">{{a.start_time}} 至 {{a.end_time}}</div>
												<div  class="info">{{a.content}}</div>
											</td>
											<td ><div  class="time">{{a.second}}秒</div></td>
											<td >
												<div class="quit" v-if="a.status==1" v-on:click="deleteCommit(5,a.data_id)">关闭</div>
												<div v-if="a.status==2">已关闭</div>
												<img v-if="a.status==3" src="../files/images/cms/end.png" />
											</td>
										</tr>					
									</table>
										
									<div style="width:100%;" v-show="ad.total_page>1">
									   <div class="tcdPageCode tcdPageCodeAd" ></div>
									</div>
									
								</div>
							</div>
							
							<div v-show="selectDealerID!=-1&&!ad.isShow">							
							<div class="selectList">
								<div class="selectItem"  v-on:click="partSelect(2)"v-show="part!=2">开局明细</div>
								<div class="selectItem selected"  v-show="part==2">开局明细</div>
								<div class="selectItem"  v-on:click="partSelect(4)"v-show="part!=4">对战明细</div>
								<div class="selectItem selected"  v-show="part==4">对战明细</div>
								<div class="selectItem"  v-on:click="partSelect(1)" v-show="part!=1">房卡明细</div>
								<div class="selectItem selected"  v-show="part==1">房卡明细</div>
								<div class="selectItem"  v-on:click="partSelect(3)"v-show="part!=3">用户明细</div>
								<div class="selectItem selected"  v-show="part==3">用户明细</div>
								<div class="selectItem"  v-on:click="partSelect(9)"v-show="part!=9">房卡查询</div>
                                <div class="selectItem selected"  v-show="part==9">房卡查询</div>
								
								<div class="selectItem"  v-on:click="partSelect(11)"v-show="part!=11">直营代理</div>
								<div class="selectItem selected"  v-show="part==11">直营代理</div>
									
								<div v-show="is_exchange==1">
									<div class="selectItem"  v-on:click="partSelect(10)"v-show="part!=10">房卡管理</div>
									<div class="selectItem selected"  v-show="part==10">房卡管理</div>	
								</div>
							
								<div v-show="selectedDealerNum==26">
									<div class="selectItem"  v-on:click="partSelect(12)"v-show="part!=12">水果机</div>
									<div class="selectItem selected"  v-show="part==12">水果机</div>	
								</div>

                                <div class="selectItem"  v-on:click="partSelect(13)"v-show="part!=13">链接盒子</div>
                                <div class="selectItem selected"  v-show="part==13">链接盒子</div>

                                <div class="selectItem" v-on:click="partSelect(14)" v-bind:class="part==14 ? 'selected' : ''" >维护&公告</div>

							</div>
							<div style="height: 20px;"></div>
							<div class="roomCard mainPart" v-show="part==1">							
								<div style="position: fixed;width:100%;height:100%;top:0;left:0;background: #fff;z-index:100;filter:alpha(Opacity=60);-moz-opacity:0.3;opacity: 0.3" id="loading" v-show="request_1==1||request_2==1||request_3==1">
									<div class="load4">
										<div class="loader">Loading...</div>
									</div>
								</div>


								<div class="saleTable">
									<div class="selectDate">

										<div class="datePicker">
											<form class="form-horizontal">
								                <fieldset>
								                	<div class="control-group">
									                    <div class="controls">
										                     <div class="input-prepend input-group">
										                       	<input type="text" style="width: 300px;height:32px;border:none;font-size:17px;padding:0 5px;margin-top:5px;background-color: #e7e8ec;cursor:pointer;border:0px solid #e2e2e2;" name="reservation" id="reservation" class="form-control" value="<?php echo $from;?> - <?php echo $to;?>"   readonly/> 
										                     </div>
									                     </div>
								                	</div>
							                	</fieldset>
						          			</form>
										</div>

									</div>
									<div style="text-align: center;height: 60px;padding:30px 0;">
										<div   class="saleData saleDataL">

											<div class="saleDetail">
												<li class="saleText">商城销售</li>
												<li class="saleCount">{{mallSaleCount}}张</li>
											</div>
										</div>							
										<div class="saleData saleDataR">
											<div class="saleDetail">
												<li class="saleText">房卡红包</li>
												<li class="saleCount">{{redSaleCount}}张</li>

											</div>
										</div>	
									</div>
								</div>


								<table class="saleList" border="0" cellspacing="0" cellpadding="0" align="center">				
									<tr class="saleListTh" >
										<td style="width: 25%">时间</td>
										<td style="width: 35%">用户</td>
										<td style="width: 20%">操作</td>
										<td style="width: 20%">房卡</td>
									</tr>
									<tr class="saleListTr" v-for="j in journalList">
										<td >{{j.time}}</td>
										<td >{{j.user}}</td>
										<td >{{j.content}}</td>
										<td >{{j.ticket_count}}张</td>
									</tr>
								</table>

								<div style="width:100%;" v-show="totalPage>1">
								   <div class="tcdPageCode tcdPageCode1" ></div>
								</div>	

							</div>	
							<div class="mainPart allGame" v-show="part==2">	
								<div class="infoSelect">
									<div style="float: left;">
										日期选择:
									</div>	
									<div class="control-group" style="float: left;margin-left: 5px;">
						                <div class="controls">
							                <div class="input-prepend input-group">
							               		<input type="text" readonly style="width: 350px;height:38px;cursor:pointer;border:1px solid #e2e2e2;padding: 0 5px;background: url('<?php echo $base_url;?>files/images/cms/down.png') no-repeat scroll right center transparent;" name="reservation" id="reservation1" class="form-control" value="<?php echo date("Y-m-d");?> - <?php echo date("Y-m-d");?>" /> 
							                </div>
						                </div>
					                </div>
					                <div style="float: left;margin-left: 20px;">
										游戏选择:
									</div>
									<select style="float: left;margin-left: 5px;width: 160px;height:40px;cursor:pointer;border:1px solid #e2e2e2;" onchange="gameChange(this)" id="gameSelect1">
										<option value="0">全部</option>
										<option v-for="g in gameList" v-bind:value="g.game_type">{{g.game_title}}</option>								
									</select>
									<div style="float: left;margin-left: 50px;">
										房卡消耗:{{balance_count}}
									</div>
								</div>		
								<table class="games"  cellspacing="0" >
									<tr>
										<td v-for="(g,index) in gameCount" v-if="index<4">
											<div class="gameName">{{g.game_title}}</div>
											<div class="round">{{g.count}}</div>
											<div class="title">开局数</div>
										</td>
									</tr>
									<tr>
										<td v-for="(g,index) in gameCount" v-if="index>=4&&index<8">
											<div class="gameName">{{g.game_title}}</div>
											<div class="round">{{g.count}}</div>
											<div class="title">开局数</div>
										</td>
									</tr>
									<tr>
										<td v-for="(g,index) in gameCount" v-if="index>=8&&index<12">
											<div class="gameName">{{g.game_title}}</div>
											<div class="round">{{g.count}}</div>
											<div class="title">开局数</div>
										</td>
									</tr>
								</table>
								<table class="roomInfo" cellspacing="0" >
									<tr class="title">
										<td style="width: 16%;">房间号</td>
										<td style="width: 16%;">游戏</td>
										<td style="width: 34%;">开局时间</td>
										<td style="width: 34%;">结束时间</td>
									</tr>
									<tr class="item" v-for="p in playDetail.data">
										<td>{{p.room_number}}</td>
										<td>{{p.game_title}}</td>
										<td>{{p.start_time}}</td>
										<td>{{p.end_time}}</td>
									</tr>
								</table>	
								<div style="width:100%;" v-show="playDetail.total_page>1">
								   <div class="tcdPageCode tcdPageCode2" ></div>
								</div>
							</div>	
							<div class="mainPart user" v-show="part==3">
								<table class="total" cellspacing="0">
									<tr class="title">
										<td>总用户数</td>
										<td>日活跃数</td>
										<td>周活跃数</td>
										<td>月活跃数</td>
									</tr>	
									<tr class="num">
										<td>{{userCount.total_count}}</td>
										<td>{{userCount.day_count}}</td>
										<td>{{userCount.week_count}}</td>
										<td>{{userCount.month_count}}</td>
									</tr>
								</table>
								<div class="search">
									<input placeholder="输入用户名称" v-model="userSearch.keyword" onkeyup="wordsTest(event)"/>
									<div class="button" v-show="userSearch.is_null" title="请输入关键词">查询</div>
									<div class="button1" v-show="!userSearch.is_null" title="点击查询" v-on:click="searchUser()">查询</div>
									<input placeholder="输入用户ID" v-model="userSearch.uid" onkeyup="uidTest(event)"/>
									<div class="button" v-show="userSearch.uid_null" title="请输入用户ID">查询</div>
									<div class="button1" v-show="!userSearch.uid_null" title="点击查询" v-on:click="searchUser()">查询</div>
								</div>
								<table class="result" cellspacing="0">
									<tr class="title">
										<td style="width: 18%;">用户ID</td>
										<td style="width: 50%;">玩家名称</td>
										<td style="width: 24%;">积分</td>
										<td style="width: 18%;">房卡</td>
									</tr>
									<tr class="item" v-for="(u,index) in userSearch.data" v-on:click="showDetail(u.account_id)">
										<td>{{u.user_code}}</td>
										<td>{{u.nickname}}</td>
										<td>{{u.sum_score}}</td>
										<td>{{u.ticket_count}}</td>
									</tr>
								</table>
								<div style="width:100%;" v-show="userSearch.total_page>1">
								   <div class="tcdPageCode tcdPageCode3" ></div>
								</div>
							</div>	
							<div class="mainPart eachGame"  v-show="part==4">
								<div class="search">
									<div class="title">游戏选择:</div>
									<select id="gameResultList" style="margin-left: 8px;" onchange="gameChange1(this)">
										<option value="-1">请选择</option>
										<option v-for="gr in gameList" v-bind:value="gr.game_type">{{gr.game_title}}</option>
<!--										<option v-for="gr in gamescore_array" v-bind:value="gr.game_type" v-if="gr.is_show==1">{{gr.game_title}}</option>-->
										<!--<option value="1">诈金花</option>
										<option value="5">6人斗牛</option>-->
									</select>
									<div class="title" style="margin-left: 30px;">房间号:</div>
									<input placeholder="输入房间号"  v-model="gameDetail.room_number" onkeyup="numTest(event)"/>
									<div class="button" v-show="!gameDetail.is_num">查询</div>						
									<div class="button button1" v-show="gameDetail.is_num" v-on:click="searchGame()">查询</div>						
									<select style="margin-left: 50px;" v-show="gameDetail.allRound.length>0" onchange="roundChange(this)">
										<option v-for="g in gameDetail.allRound" v-bind:value="g.round">第{{g.round}}轮</option>
									</select>
								</div>
								<div class="rule" v-show="gameDetail.allRound.length>0">
									开局时间：{{gameDetail.eachRound.start_time}}&nbsp&nbsp&nbsp&nbsp结束时间：{{gameDetail.eachRound.end_time}}<br>
									房间规则：{{gameDetail.eachRound.rule_text}}
								</div>
								<div style="margin-top: 20px;"  v-show="gameDetail.allRound.length>0">最终积分榜  <a style="float: right;color: #509fe2;margin-right: 30px;cursor: pointer;margin-top: 5px;" v-on:click="gameResult()">查看图片</a></div>
								<table class="result" cellspacing="0" v-show="gameDetail.allRound.length>0">
									<tr class="title">
										<td style="width: 4%;"></td>
										<td style="width: 56%;">玩家名称</td>
										<td style="width: 40%;">总分</td>
									</tr>	
									<tr class="item" v-for="b in gameDetail.eachRound.balance_board">
										<td></td>
										<td>{{b.name}}</td>
										<td>{{b.score}}</td>
									</tr>
								</table>

								<div class="round" v-for="g in gameDetail.eachRound.player_array">
									<div>{{g.game_num}}/{{g.total_num}}局</div>
									<table class="eachRound" cellspacing="0">
										<tr class="title">
											<td style="width: 4%;"></td>
											<td style="width: 23%;">玩家名称</td>
											<td v-if="gameDetail.type!=1 && gameDetail.type!=110"style="width: 10%;">押注</td>
											<td v-if="gameDetail.type==1 || gameDetail.type==110"style="width: 20%;">下注</td>
											<td style="width: 10%;">得分</td>
											<td style="width: 12%;">牌型</td>
											<td style="width: 41%;">具体牌型</td>
										</tr>	
										<tr class="item" v-for="p in g.player_cards">
											<td></td>
											<td>{{p.name}}<img src="../files/images/cms/banker.png" v-if="p.is_banker==1" style="height: 20px;vertical-align: sub;"></td>
											<td v-if="gameDetail.type!=1 && gameDetail.type!=110">{{p.chip}}</td>
											<td v-if="gameDetail.type==1 || gameDetail.type==110">{{p.chip}}</td>
											<td>{{p.score}}</td>
											<td>{{p.card_type_str}}</td>
											<td>
												<div v-if="p.is_join==1" class="card" v-for="c in p.player_cards" >
                                                    <div v-if="c.suit=='X'">大王</div>
                                                    <div v-if="c.suit=='Y'">小王</div>
                                                    <div v-if="c.suit=='A'">冬&nbsp;{{ c.point }}</div>
                                                    <div v-if="c.suit=='B'">秋&nbsp;{{ c.point }}</div>
                                                    <div v-if="c.suit=='C'">春&nbsp;{{ c.point }}</div>
                                                    <div v-if="c.suit=='D'">夏&nbsp;{{ c.point }}</div>
                                                    <div v-if="c.suit=='E'">梅&nbsp;{{ c.point }}</div>
                                                    <div v-if="c.suit=='F'">兰&nbsp;{{ c.point }}</div>
                                                    <div v-if="c.suit=='G'">竹&nbsp;{{ c.point }}</div>
                                                    <div v-if="c.suit=='H'">菊&nbsp;{{ c.point }}</div>
													<div v-if="c.suit==1 || c.suit==2 || c.suit==3 || c.suit==4"><img v-bind:src="'../files/images/cms/card'+c.suit+'.png'">{{c.point}}</div>
												</div>
												<div v-if="p.is_join==0">{{p.player_cards}}</div>
											</td>
										</tr>
									</table>
								</div>
							</div>	

							<div class="mainPart roomCard"  v-show="part==9">
								<div class="search">
									<input placeholder="搜索玩家名字"  v-model="roomCard.search1"/>				
									<div class="button"  v-on:click="searchCardInfo()">查询</div>	
								</div>
								<div v-show="!roomCard.turn">
									<table class="roomCardList" cellspacing="0" >
										<tr style="height: 40px">
											<td style="width: 4%;"></td>
											<td style="width: 8%;"></td>
											<td style="width: 24%;">玩家名称</td>
											<td style="width: 12%;">房卡数量</td>
											<td style="width: 12%;"></td>
											<td style="width: 12%;"></td>
											<td style="width: 14%;"></td>
											<td style="width: 14%;"></td>
										</tr>
										<tr class="item" v-for="c in roomCard.data" >
											<td ></td>
											<td >
												<div style="position: relative;width:40px;height:40px;">
													<img v-bind:src="c.headimgurl" />
													<div style="position: absolute;top:-10px;left:-16px;height: 18px;width: 40px;border-radius: 12px;background: rgb(250, 1, 0);color: yellow;font-size: 12px;text-align: center;"  v-if="c.is_agent==1">直营</div>
												</div>
											</td>
											<td >{{c.nickname}}</td>
											<td >{{c.ticket_count}}</td>
											<td ><div class="noty" v-on:click="showNoty(c.account_id)">通知</div></td>
											<td ><div class="cardCancel" v-on:click="cardCancel(c.account_id,c.ticket_count)">注销房卡</div></td>
											<td ><div class="cardFrom" v-on:click="cardFrom(c.account_id,c.nickname)">房卡来源</div></td>
											<td ><div class="cardFrom" v-on:click="cardTo(c.account_id,c.nickname)">房卡去向</div></td>
										</tr>						
									</table>	
									<div style="width:100%;" v-show="roomCard.total_page>1">
									   <div class="tcdPageCode tcdPageCode9" ></div>
									</div>	
								</div>
							</div>


							<div class="mainPart agent"  v-show="part==11">
								<div class="search">
									<input placeholder="搜索玩家名字"  v-model="agent.search1"/>				
									<div class="button"  v-on:click="searchAgent()">查询</div>	
								</div>
								<div v-show="!agent.turn">
									<table class="roomCardList" cellspacing="0" >
										<tr style="height: 40px">
											<td style="width: 4%;"></td>
											<td style="width: 8%;"></td>
											<td style="width: 24%;">玩家名称</td>
											<td style="width: 12%;">房卡数量</td>											
											<td style="width: 14%;"></td>
										</tr>
										<tr class="item" v-for="c in agent.data" >
											<td ></td>
											<td >
												<div style="position: relative;width:40px;height:40px;">
													<img v-bind:src="c.headimgurl" />
													<div style="position: absolute;top:-10px;left:-16px;height: 18px;width: 40px;border-radius: 12px;background: rgb(250, 1, 0);color: yellow;font-size: 12px;text-align: center;"  v-if="c.is_agent==1">直营</div>
												</div>
											</td>
											<td >{{c.nickname}}</td>
											<td >{{c.ticket_count}}</td>
											<td >
												<div class="operation" v-show="c.is_agent==1" v-on:click="deleteCommit(7,c.account_id)">取消绑定</div>
												<div class="operation" v-show="c.is_agent==0" v-on:click="deleteCommit(6,c.account_id)">绑定代理</div>
											</td>
										</tr>						
									</table>	
									<div style="width:100%;" v-show="agent.total_page>1">
									   <div class="tcdPageCode tcdPageCode11" ></div>
									</div>	
								</div>
							</div>
							
							<div class="mainPart fruit" v-show="part==12">	
								<div class="infoSelect">
									<div style="float: left;">
										日期选择:
									</div>	
									<div class="control-group" style="float: left;margin-left: 5px;">
						                <div class="controls">
							                <div class="input-prepend input-group">
							               		<input type="text" readonly style="width: 350px;height:38px;cursor:pointer;border:1px solid #e2e2e2;padding: 0 5px;background: url('<?php echo $base_url;?>files/images/cms/down.png') no-repeat scroll right center transparent;" name="reservation" id="reservation12" class="form-control" value="<?php echo date("Y-m-d");?> - <?php echo date("Y-m-d");?>" /> 
							                </div>
						                </div>
					                </div>
								</div>		
								<table class="games"  cellspacing="0" >
									<tr>
										<td>
											<div class="round">{{fruit.sum_bet}}</div>
											<div class="gameName">投注额</div>
										</td>									
										<td>
											<div class="round">{{fruit.sum_reward}}</div>
											<div class="gameName">投注结果</div>
										</td>								
										<td>	
											<div class="round">{{fruit.balance}}</div>
											<div class="gameName">总计</div>
										</td>
									</tr>
								</table>
								<table class="roomInfo" cellspacing="0" >
									<tr class="title">
										<td style="width: 20%;">时间</td>
										<td style="width: 40%;">用户</td>
										<td style="width: 13%;">投注额</td>
										<td style="width: 13%;">投注结果</td>
										<td style="width: 14%;">总计</td>
									</tr>
									<tr class="item" v-for="p in fruit.data">
										<td>{{p.time}}</td>
										<td>{{p.nickname}}</td>
										<td>{{p.bet_count}}</td>
										<td>{{p.reward}}</td>
										<td>{{p.balance}}</td>
									</tr>
								</table>	
								<div style="width:100%;" v-show="fruit.total_page>1">
								   <div class="tcdPageCode tcdPageCode12" ></div>
								</div>
							</div>


                                <div class="mainPart agent"  v-show="part==13">
                                    <div style="width: 1010px ;border:1px solid #d9d9d9">
                                        <div style="padding: 10px">
<!--                                            <div style="display: inline-block">-->
<!--                                                <span>链接：</span>-->
<!--                                                <a href="http://{{domainInfo.now}}">http://{{domainInfo.now}}</a>-->
<!--                                            </div>-->
<!---->
<!--                                            <a class="button" style="display: inline-block" href="https://mp.weixin.qq.com/" target="_blank">公众号设置</a>-->
<!--                                            <div class="button" style="display: inline-block">刷新</div>-->

                                            <div>
                                                <button class="button" v-on:click="changeDomains()">配置链接库</button>
                                            </div>
                                            <div style="margin-top: 10px">
                                                <textarea style="width:400px;height:200px;border: 1px solid rgb(109, 109, 109);" v-model="domainInfo.domains"></textarea>
                                                <textarea style="width:400px;height:200px;border: 1px solid rgb(109, 109, 109);" disabled v-model="domainInfo.black"></textarea>
                                            </div>

                                        </div>

                                        <div style="margin-top : 10px;border-top: 1px solid #d9d9d9;padding: 10px">
                                            <div style="display: inline-block;">
                                                <span>自动切换域名：</span>
                                            </div>
                                            <div style="display: inline-block;">
                                                <div class="testswitch">
                                                    <input class="testswitch-checkbox" id="onoffswitch" v-on:change="changeAutoState()" type="checkbox" checked v-if="!domainInfo.auto">
                                                    <input class="testswitch-checkbox" id="onoffswitch" v-on:change="changeAutoState()" type="checkbox" v-if="domainInfo.auto">
                                                    <label class="testswitch-label" for="onoffswitch">
                                                        <span class="testswitch-inner" data-on="ON" data-off="OFF"></span>
                                                        <span class="testswitch-switch"></span>
                                                    </label>
                                                </div>
                                            </div>
                                        </div>

                                        <div style="margin-top : 10px;border-top: 1px solid #d9d9d9;padding: 10px">
                                            <div style="width:500px;display: inline-block;border-right: 1px solid #d9d9d9">
                                                <span>大厅：</span>
                                                <b>http://{{domainInfo.now}}/y/ym</b>
                                            </div>
                                            <div style="display: inline-block;">
                                                <span>个人主页：</span>
                                                <b>http://{{domainInfo.now}}/y/yh</b>
                                            </div>
                                        </div>

                                        <div style="margin-top : 10px;border-top: 1px solid #d9d9d9;padding: 10px">
                                            <div style="width:500px;display: inline-block;border-right: 1px solid #d9d9d9">
                                                <span>回调：</span>
                                                <b>http://{{domainInfo.callback}}</b>
                                            </div>
                                        </div>

                                    </div>
                                    <div>
                                        <table class="roomCardList" cellspacing="0" >
                                            <tr style="height: 40px">
                                                <td style="width: 2%"></td>
                                                <td style="width: 24%;">链接</td>
                                                <td style="width: 12%;">配置</td>
                                            </tr>
                                            <tr class="item" v-for="d in domainInfo.list" >
                                                <td></td>
                                                <td >{{ d }}</td>
                                                <td >
                                                    <div class="operation" style="display: inline-block" v-on:click="changeDomain(d)">配置跳转</div> |
                                                    <div class="operation" style="display: inline-block" v-on:click="changeCallback(d)">配置回调</div>
                                                </td>
                                            </tr>
                                        </table>
                                        <div style="width:100%;" v-show="roomCard.total_page>1">
                                            <div class="tcdPageCode tcdPageCode9" ></div>
                                        </div>
                                    </div>
                                </div>

                                <div class="mainPart agent"  v-show="part==14">
                                    <div>
                                        <h3>维护：</h3>
                                        <table class="roomCardList" cellspacing="0" >
                                            <tr style="height: 40px">
                                                <td style="width: 2%"></td>
                                                <td style="width: 10%;">游戏</td>
                                                <td style="width: 24%;">提示信息</td>
                                                <td style="width: 10%;">状态</td>
                                                <td style="width: 12%;">配置</td>
                                            </tr>
                                            <tr class="item" v-for="item in maintainList" >
                                                <td></td>
                                                <td >{{ item.game_title }}</td>
                                                <td ><input v-model="item.service_text" v-on:change="changeMaintain(item)" style="width: 80%;border: 1px solid #6d6d6d" /></td>
                                                <td >{{ item.state_text }}</td>
                                                <td ><div class="operation" v-on:click="changeMaintainState(item)">{{ item.state_other_text }}</div></td>
                                            </tr>
                                        </table>
                                    </div>

                                    <div>
                                        <h3>公告：</h3>
                                        <table class="roomCardList" cellspacing="0" >
                                            <tr style="height: 40px">
                                                <td style="width: 2%"></td>
                                                <td style="width: 10%;">页面</td>
                                                <td style="width: 24%;">信息</td>
                                                <td style="width: 10%;">状态</td>
                                                <td style="width: 12%;">配置</td>
                                            </tr>
                                            <tr class="item" v-for="item in broadcastList" >
                                                <td></td>
                                                <td >{{ item.introl }}</td>
                                                <td ><input v-model="item.content" v-on:change="changeBroadcast(item)" style="width: 80%;border: 1px solid #6d6d6d" /></td>
                                                <td >{{ item.state_text }}</td>
                                                <td ><div class="operation" v-on:click="changeBroadcastState(item)">{{ item.state_other_text }}</div></td>
                                            </tr>
                                        </table>
                                    </div>
                                </div>
						</div>	
					</div>
				</div>
			</div>
		</div>

		<!-- 编辑代理商账号信息 -->
		<div class="editDealerPart" v-show="editDealerPartShow">
			<div class="titleText">{{editDealerTitle}}</div>
			<div class="infoPart">
				<div class="infoItem" >名&nbsp&nbsp&nbsp&nbsp&nbsp&nbsp称：<input v-model="editDealerName" /></div>
				<div class="infoItem" >账&nbsp&nbsp&nbsp&nbsp&nbsp&nbsp户：<input v-model="editDealerAccount" /></div>
				<div class="infoItem" >密&nbsp&nbsp&nbsp&nbsp&nbsp&nbsp码：<input v-model="editDealerPwd" /></div>
				<div class="infoItem" >收&nbsp&nbsp&nbsp&nbsp&nbsp&nbsp款：
					<select id="paymentType" v-model="editDealerPaymentType" >
						<option class="selectedPaymentType" value="-1">请选择</option>
						<option class="selectedPaymentType" value="1">代收费</option>
						<option class="selectedPaymentType" value="2">自收费</option>
					</select>
				</div>
			</div>
			<div class="buttonPart" >
				<div class="left click" v-on:click="saveDealerPart()">确定</div>
				<div class="right click" v-on:click="closeDealerPart()">取消</div>
			</div>
		</div>

		<!-- 编辑代理商账号信息 -->
		<div class="editDealerPart" v-show="rechargeShow">
			<div class="titleText">房卡充值</div>
			<div class="infoPart">
				<div class="infoItem" >房&nbsp&nbsp卡&nbsp&nbsp数：<input v-model="rechargeCount" onkeyup="this.value=this.value.replace(/[^0-9]/g,'')" onafterpaste="this.value=this.value.replace(/[^0-9]/g,'')"/></div>
			</div>
			<div class="buttonPart" >
				<div class="left click" v-on:click="saveRechargeOpt()">确定</div>
				<div class="right click" v-on:click="closeRechargePart()">取消</div>
			</div>
		</div>

		<!-- 编辑代理商商品信息 -->
		<div class="editGoodsPart" v-show="goodsShow">
			<div class="titleText">商城设置</div>
			<div class="infoPart">
				<table cellspacing="10px" cellpadding="0">
					<tr>
						<td></td>
						<td>内容</td>
						<td>数量</td>
						<td>价格</td>
					</tr>
					<tr v-for="g in goodsList">
						<td width="12%">商品</td>
						<td width="48%"><input type="text"  v-model="g.title"></td>
						<td width="20%"><input type="text"  v-model="g.ticket_count" onkeyup="this.value=this.value.replace(/[^0-9]/g,'')" onafterpaste="this.value=this.value.replace(/[^0-9]/g,'')"></td>
						<td width="20%"><input type="text"  v-model="g.price"></td>
					</tr>
					<!-- <tr>
						<td>商品2</td>
						<td><input type="text"></td>
						<td><input type="text"></td>
						<td><input type="text"></td>
					</tr>
					<tr>
						<td>商品3</td>
						<td><input type="text"></td>
						<td><input type="text"></td>
						<td><input type="text"></td>
					</tr>
					<tr>
						<td>商品4</td>
						<td><input type="text"></td>
						<td><input type="text"></td>
						<td><input type="text"></td>
					</tr> -->
				</table>
			</div>
			<div class="buttonPart" >
				<div class="left click" v-on:click="updateGoodsList()">确定</div>
				<div class="right click" v-on:click="closeGoodsList()">取消</div>
			</div>
		</div>


		<div class="alert" v-show="alert.isShow" style="z-index: 11;">
			<div class="alertBackground"></div>
			<img src="../files/images/cms/alert1.png" v-show="alert.type==1">
			<img src="../files/images/cms/alert2.png" v-show="alert.type==2">
			<div class="alertText" >{{alert.text}}</div>
		</div>
		
		<div class="personDetail" v-show="userSearch.isShowDetail" v-on:click="hideDetail()">
			<div class="mainPart" style="width: 610px;left: 36%;" v-on:click.stop="hideDetail(true)">
                <div style="float: left;">
                    日期选择:
                </div>
                <div class="control-group" style="float: left;margin-left: 5px;">
                    <div class="controls">
                        <div class="input-prepend input-group">
                            <input type="text" readonly style="width: 350px;height:38px;cursor:pointer;border:1px solid #e2e2e2;padding: 0 5px;background: url('<?php echo $base_url;?>files/images/cms/down.png') no-repeat scroll right center transparent;" name="reservation" id="reservation13" class="form-control" value="<?php echo date("Y-m-d");?> - <?php echo date("Y-m-d");?>" />
                        </div>
                    </div>
                </div>
                <br><br>
				<div class="name">{{userSearch.detail.nickname}}</div>
				<table class="detail" cellspacing="0">
					<tr class="title">
						<td style="width: 20%;"></td>
						<td style="width: 50%;" >游戏</td>
						<td style="width: 30%;">积分</td>
					</tr>				
					<tr class="item" v-for="s in scoreStat.data">
						<td></td>
						<td>{{s.game_title}}</td>
						<td>{{s.game_score}}</td>
					</tr>	
					<tr class="item">
						<td></td>
						<td style="font-weight: bold;">总积分</td>
						<td>{{scoreStat.sum}}</td>
					</tr>				
				</table>	
			</div>
			
		</div>

	
		
		<div class="deleteAlert"  v-show="deleteInfo.isShow">
			<div class="back"></div>
			<div class="box1">
				<div class="deleteText bold" >{{deleteInfo.text}}</div>
				<div class="buttonPart">
					<div class="button button1" v-on:click="quitDelete()">取消</div>
					<div class="button button2" v-on:click="deleteSubmit()">确定</div>
				</div>	
			</div>			
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
						注销数量：<input  v-model="roomCard.deleteInfo.deleteNum"  />						
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
<script type="text/javascript" src="<?php echo $base_url;?>files/js/index.js" ></script>

</html>

