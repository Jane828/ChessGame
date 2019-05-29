<html>
<head>
<meta charset="utf-8" >
<meta name="viewport" content="width=375,height=667,initial-scale=1.0, minimum-scale=1.0, maximum-scale=1.0, user-scalable=no"/>
<meta name="format-detection" content="telephone=no" />
<meta name="msapplication-tap-highlight" content="no" />
<meta http-equiv="Pragma" content="public" />
<meta http-equiv="Cache-Control" content="public" />
<title>
	<?php echo $room_number;?>号<?php 
		if($game_type==1)
			echo "六人金花房间";
		else if($game_type==5)	
			echo "六人牛牛房间";
		else if($game_type==9)	
			echo "九人牛牛房间";
		else if($game_type==91)
			echo "VIP九人牛牛房间";
		else if($game_type==93)
			echo "VIP六人牛牛房间";
		else if($game_type==94)
			echo "VIP十二人牛牛房间";
		else if($game_type==92)
			echo "VIP六人金花房间";
		else if($game_type==95)
			echo "VIP十人金花房间";
		else if($game_type==12)
			echo "十二人牛牛房间";
        else if($game_type==71)
            echo "癞子牛牛房间";
		else if($game_type==110)
		    echo "十人金花房间";
		else if($game_type==111)
		    echo "大牌炸金花房间";
		else if($game_type==36)
		    echo "六人三公房间";
		else if($game_type==37)
		    echo "九人三公房间";
		else if($game_type==38)
		    echo "十二人三公房间";
	?>
</title>

<script src="<?php echo $base_url;?>files/js/jquery-1.9.1.min.js"></script>
<script type="text/javascript" src="<?php echo $base_url;?>files/js/vue.min.js" ></script>
<script type="text/javascript" src="<?php echo $base_url;?>files/js/vue-resource.min.js" ></script>

<style>
*{padding: 0;margin:0;-webkit-tap-highlight-color:rgba(0,0,0,0);-webkit-backface-visibility: hidden;-webkit-overflow-scrolling: touch;}a {text-decoration: none;color: #fff;}ul {list-style: none;}input{border: none;outline:none}body{font-family: 'Helvetica Neue', Helvetica, 'Hiragino Sans GB', 'Microsoft YaHei', 微软雅黑, Arial, sans-serif;cursor: default;}img{border: none;}


.flower .ranking{position: absolute;width: 800px;height:1400px;top:0;left:0;z-index: 110;}  
.flower .ranking .roundEndShow{display: none;}  
.flower .ranking .rankBack{width: 100%;height:100%;background: #000;opacity:1.0}
.flower .ranking .rankBack .bg{position: absolute;width: 100%;height:100%;background: #000;opacity:1.0}  
.flower .ranking .rankText{width: 100%;position: absolute;top:10%;left: 0;}   
.flower .ranking .rankText .title{width: 100%;} 
.flower .ranking .rankText .time{text-align: center;margin-top: 24px;margin-bottom: 20px;}
.flower .ranking .rankText .time a{border-radius: 20px;border: 2px solid #ce6eff;color:#f7d92b;padding: 4px 16px;width: 400px;font-size: 24px;}
.flower .ranking .rankText .scoresItem{width:516px;margin:0 auto; margin-top: 10px;height:96px;line-height:96px;font-size:32px;position: relative;background-color: #160f2b; }
.flower .ranking .rankText .scoresItemWhite{color:#fff; }
.flower .ranking .rankText .scoresItemWhite a{color:#fff; }
.flower .ranking .rankText .scoresItemYellow{color:#f7d92b;}
.flower .ranking .rankText .scoresItemYellow a{color:#f7d92b;}
.flower .ranking .rankText .scoresItem .name{left: 16%;width: 52%;overflow: hidden;word-break:break-all;position: absolute;top:0;}
.flower .ranking .rankText .scoresItem .currentScores{width:25%;text-align: left;position: absolute;right: 0;top:0;}
.flower .ranking  .button{width:100%;position: absolute;bottom:12%; }
.flower .ranking  .button img{width:33%;}

.vflower6 .ranking{position: absolute;width: 800px;height:1400px;top:0;left:0;z-index: 110;}
.vflower6 .ranking .roundEndShow{display: none;}
.vflower6 .ranking .rankBack{width: 100%;height:100%;background: #000;opacity:1.0}
.vflower6 .ranking .rankBack .bg{position: absolute;width: 100%;height:100%;background: #000;opacity:1.0}
.vflower6 .ranking .rankText{width: 100%;position: absolute;top:10%;left: 0;}
.vflower6 .ranking .rankText .title{width: 100%;}
.vflower6 .ranking .rankText .time{text-align: center;margin-top: 24px;margin-bottom: 20px;}
.vflower6 .ranking .rankText .time a{border-radius: 20px;border: 2px solid #ce6eff;color:#f7d92b;padding: 4px 16px;width: 400px;font-size: 24px;}
.vflower6 .ranking .rankText .scoresItem{width:516px;margin:0 auto; margin-top:2px;height:86px;line-height:86px;font-size:32px;position: relative;background-color: #160f2b; }
.vflower6 .ranking .rankText .scoresItemWhite{color:#fff; }
.vflower6 .ranking .rankText .scoresItemWhite a{color:#fff; }
.vflower6 .ranking .rankText .scoresItemYellow{color:#f7d92b;}
.vflower6 .ranking .rankText .scoresItemYellow a{color:#f7d92b;}
.vflower6 .ranking .rankText .scoresItem .name{left: 14%;width: 42%;overflow: hidden;word-break:break-all;position: absolute;top:0;}
.vflower6 .ranking .rankText .scoresItem .currentScores{left: 56%;text-align: left;position: absolute;right: 0;top:0;}
.vflower6 .ranking .rankText .scoresItem .consumeScores{left: 84%;text-align: left;position: absolute;right: 0;top:0;}
.vflower6 .ranking  .button{width:100%;position: absolute;bottom:12%; }
.vflower6 .ranking  .button img{width:33%;}

.vflower10 .ranking{position: absolute;width: 800px;height:1400px;top:0;left:0;z-index: 110;}
.vflower10 .ranking .roundEndShow{display: none;}
.vflower10 .ranking .rankBack{width: 100%;height:100%;background: #000;opacity:1.0}
.vflower10 .ranking .rankBack .bg{position: absolute;width: 100%;height:100%;background: #000;opacity:1.0}
.vflower10 .ranking .rankText{width: 100%;position: absolute;top:10%;left: 0;}
.vflower10 .ranking .rankText .title{width: 100%;}
.vflower10 .ranking .rankText .time{text-align: center;margin-top: 24px;margin-bottom: 20px;}
.vflower10 .ranking .rankText .time a{border-radius: 20px;border: 2px solid #ce6eff;color:#f7d92b;padding: 4px 16px;width: 400px;font-size: 24px;}
.vflower10 .ranking .rankText .scoresItem{width:516px;margin:0 auto; margin-top:2px;height:66px;line-height:66px;font-size:32px;position: relative;background-color: #160f2b; }
.vflower10 .ranking .rankText .scoresItemWhite{color:#fff; }
.vflower10 .ranking .rankText .scoresItemWhite a{color:#fff; }
.vflower10 .ranking .rankText .scoresItemYellow{color:#f7d92b;}
.vflower10 .ranking .rankText .scoresItemYellow a{color:#f7d92b;}
.vflower10 .ranking .rankText .scoresItem .name{left: 14%;width: 42%;overflow: hidden;word-break:break-all;position: absolute;top:0;}
.vflower10 .ranking .rankText .scoresItem .currentScores{left: 56%;text-align: left;position: absolute;right: 0;top:0;}
.vflower10 .ranking .rankText .scoresItem .consumeScores{left: 84%;text-align: left;position: absolute;right: 0;top:0;}
.vflower10 .ranking  .button{width:100%;position: absolute;bottom:12%; }
.vflower10 .ranking  .button img{width:33%;}


.flower10 .ranking{position: absolute;width: 800px;height:1400px;top:0;left:0;z-index: 110;}
.flower10 .ranking .roundEndShow{display: none;}
.flower10 .ranking .rankBack{width: 100%;height:200%;background: #000;opacity:1.0}
.flower10 .ranking .rankBack .bg{position: absolute;width: 100%;height:100%;background: #000;opacity:1.0}
.flower10 .ranking .rankText{width: 100%;position: absolute;top:10%;left: 0;}
.flower10 .ranking .rankText .title{width: 100%;}
.flower10 .ranking .rankText .time{text-align: center;margin-top: 24px;margin-bottom: 20px;}
.flower10 .ranking .rankText .time a{border-radius: 20px;border: 2px solid #ce6eff;color:#f7d92b;padding: 4px 16px;width: 400px;font-size: 24px;}
.flower10 .ranking .rankText .scoresItem{width:516px;margin:0 auto; margin-top: 6px;height:66px;line-height:66px;font-size:32px;position: relative;background-color: #160f2b; }
.flower10 .ranking .rankText .scoresItemWhite{color:#fff; }
.flower10 .ranking .rankText .scoresItemWhite a{color:#fff; }
.flower10 .ranking .rankText .scoresItemYellow{color:#f7d92b;}
.flower10 .ranking .rankText .scoresItemYellow a{color:#f7d92b;}
.flower10 .ranking .rankText .scoresItem .name{left: 16%;width: 52%;overflow: hidden;word-break:break-all;position: absolute;top:0;}
.flower10 .ranking .rankText .scoresItem .currentScores{width:25%;text-align: left;position: absolute;right: 0;top:0;}
.flower10 .ranking  .button{width:100%;position: absolute;bottom:12%; }
.flower10 .ranking  .button img{width:33%;}


.bull .ranking{position: absolute;width: 800px;height:1400px;top:0;left:0;z-index: 110;}  
.bull .ranking .roundEndShow{display: none;}  
.bull .ranking .rankBack{width: 100%;height:100%;background: #000;opacity:1.0}
.bull .ranking .rankBack .bg{position: absolute;width: 100%;height:100%;background: #000;opacity:1.0}  
.bull .ranking .rankText{width: 100%;position: absolute;top:10%;left: 0;}   
.bull .ranking .rankText .title{width: 100%;} 
.bull .ranking .rankText .time{text-align: center;margin-top: 24px;margin-bottom: 20px;}
.bull .ranking .rankText .time a{border-radius: 20px;border: 2px solid #f7d92b;color:#f7d92b;padding: 4px 16px;width: 400px;font-size: 24px;}
.bull .ranking .rankText .scoresItem{width:516px;margin:0 auto; margin-top: 10px;height:96px;line-height:96px;font-size:32px;position: relative;background-color: #1b160c; }
.bull .ranking .rankText .scoresItemWhite{color:#fff; }
.bull .ranking .rankText .scoresItemWhite a{color:#fff; }
.bull .ranking .rankText .scoresItemYellow{color:#f7d92b;}
.bull .ranking .rankText .scoresItemYellow a{color:#f7d92b;}
.bull .ranking .rankText .scoresItem .name{left: 16%;width: 50%;height: 96px;overflow: hidden;word-break:break-all;position: absolute;top:0;}
.bull .ranking .rankText .scoresItem .currentScores{width:25%;text-align: left;position: absolute;right: 0;top:0;}
.bull .ranking  .button{width:100%;position: absolute;bottom:12%; }
.bull .ranking  .button img{width:33%;}


.bull9 .ranking{position: absolute;width: 800px;height:1400px;top:0;left:0;z-index: 110;}  
.bull9 .ranking .roundEndShow{display: none;}  
.bull9 .ranking .rankBack{width: 100%;height:100%;background: #000;opacity:1.0}
.bull9 .ranking .rankBack .bg{position: absolute;width: 100%;height:100%;background: #000;opacity:1.0}  
.bull9 .ranking .rankText{width: 100%;position: absolute;top:10%;left: 0;}   
.bull9 .ranking .rankText .title{width: 100%;} 
.bull9 .ranking .rankText .time{text-align: center;margin-top: 24px;margin-bottom: 20px;}
.bull9 .ranking .rankText .time a{border-radius: 20px;border: 2px solid #f7d92b;color:#f7d92b;padding: 4px 16px;width: 400px;font-size: 24px;}
.bull9 .ranking .rankText .scoresItem{width:516px;margin:0 auto; margin-top: 6px;height:72px;line-height:72px;font-size:32px;position: relative;background-color: #1b160c; }
.bull9 .ranking .rankText .scoresItemWhite{color:#fff; }
.bull9 .ranking .rankText .scoresItemWhite a{color:#fff; }
.bull9 .ranking .rankText .scoresItemYellow{color:#f7d92b;}
.bull9 .ranking .rankText .scoresItemYellow a{color:#f7d92b;}
.bull9 .ranking .rankText .scoresItem .name{left: 16%;width: 50%;height: 72px;overflow: hidden;word-break:break-all;position: absolute;top:0;}
.bull9 .ranking .rankText .scoresItem .currentScores{width:25%;text-align: left;position: absolute;right: 0;top:0;}
.bull9 .ranking  .button{width:100%;position: absolute;bottom:8%; }
.bull9 .ranking  .button img{width:33%;}

.bull12 .ranking{position: absolute;width: 800px;height:1400px;top:0;left:0;z-index: 110;}
.bull12 .ranking .roundEndShow{display: none;}
.bull12 .ranking .rankBack{width: 100%;height:100%;background: #000;opacity:1.0}
.bull12 .ranking .rankBack .bg{position: absolute;width: 100%;height:100%;background: #000;opacity:1.0}
.bull12 .ranking .rankText{width: 100%;position: absolute;top:10%;left: 0;}
.bull12 .ranking .rankText .title{width: 100%;}
.bull12 .ranking .rankText .time{text-align: center;margin-top: 24px;margin-bottom: 20px;}
.bull12 .ranking .rankText .time a{border-radius: 20px;border: 2px solid #f7d92b;color:#f7d92b;padding: 4px 16px;width: 400px;font-size: 24px;}
.bull12 .ranking .rankText .scoresItem{width:516px;margin:0 auto; margin-top: 6px;height:65px;line-height:72px;font-size:32px;position: relative;background-color: #1b160c; }
.bull12 .ranking .rankText .scoresItemWhite{color:#fff; }
.bull12 .ranking .rankText .scoresItemWhite a{color:#fff; }
.bull12 .ranking .rankText .scoresItemYellow{color:#f7d92b;}
.bull12 .ranking .rankText .scoresItemYellow a{color:#f7d92b;}
.bull12 .ranking .rankText .scoresItem .name{left: 16%;width: 50%;height: 72px;overflow: hidden;word-break:break-all;position: absolute;top:0;}
.bull12 .ranking .rankText .scoresItem .currentScores{width:25%;text-align: left;position: absolute;right: 0;top:0;}
.bull12 .ranking  .button{width:100%;position: absolute;bottom:8%; }
.bull12 .ranking  .button img{width:33%;}

.sangong .ranking{position: absolute;width: 800px;height:1400px;top:0;left:0;z-index: 110;}
.sangong .ranking .roundEndShow{display: none;}
.sangong .ranking .rankBack{width: 100%;height:100%;background: #000;opacity:1.0}
.sangong .ranking .rankBack .bg{position: absolute;width: 100%;height:100%;background: #000;opacity:1.0}
.sangong .ranking .rankText{width: 100%;position: absolute;top:10%;left: 0;}
.sangong .ranking .rankText .title{width: 100%;}
.sangong .ranking .rankText .time{text-align: center;margin-top: 24px;margin-bottom: 20px;}
.sangong .ranking .rankText .time a{border-radius: 20px;border: 2px solid #f7d92b;color:#f7d92b;padding: 4px 16px;width: 400px;font-size: 24px;}
.sangong .ranking .rankText .scoresItem{width:516px;margin:0 auto; margin-top: 10px;height:96px;line-height:96px;font-size:32px;position: relative;background-color: #1b160c; }
.sangong .ranking .rankText .scoresItemWhite{color:#fff; }
.sangong .ranking .rankText .scoresItemWhite a{color:#fff; }
.sangong .ranking .rankText .scoresItemYellow{color:#f7d92b;}
.sangong .ranking .rankText .scoresItemYellow a{color:#f7d92b;}
.sangong .ranking .rankText .scoresItem .name{left: 16%;width: 50%;height: 96px;overflow: hidden;word-break:break-all;position: absolute;top:0;}
.sangong .ranking .rankText .scoresItem .currentScores{width:25%;text-align: left;position: absolute;right: 0;top:0;}
.sangong .ranking  .button{width:100%;position: absolute;bottom:12%; }
.sangong .ranking  .button img{width:33%;}

.sangong9 .ranking{position: absolute;width: 800px;height:1400px;top:0;left:0;z-index: 110;}
.sangong9 .ranking .roundEndShow{display: none;}
.sangong9 .ranking .rankBack{width: 100%;height:100%;background: #000;opacity:1.0}
.sangong9 .ranking .rankBack .bg{position: absolute;width: 100%;height:100%;background: #000;opacity:1.0}
.sangong9 .ranking .rankText{width: 100%;position: absolute;top:10%;left: 0;}
.sangong9 .ranking .rankText .title{width: 100%;}
.sangong9 .ranking .rankText .time{text-align: center;margin-top: 24px;margin-bottom: 20px;}
.sangong9 .ranking .rankText .time a{border-radius: 20px;border: 2px solid #f7d92b;color:#f7d92b;padding: 4px 16px;width: 400px;font-size: 24px;}
.sangong9 .ranking .rankText .scoresItem{width:516px;margin:0 auto; margin-top: 10px;height:96px;line-height:96px;font-size:32px;position: relative;background-color: #1b160c; }
.sangong9 .ranking .rankText .scoresItemWhite{color:#fff; }
.sangong9 .ranking .rankText .scoresItemWhite a{color:#fff; }
.sangong9 .ranking .rankText .scoresItemYellow{color:#f7d92b;}
.sangong9 .ranking .rankText .scoresItemYellow a{color:#f7d92b;}
.sangong9 .ranking .rankText .scoresItem .name{left: 16%;width: 50%;height: 96px;overflow: hidden;word-break:break-all;position: absolute;top:0;}
.sangong9 .ranking .rankText .scoresItem .currentScores{width:25%;text-align: left;position: absolute;right: 0;top:0;}
.sangong9 .ranking  .button{width:100%;position: absolute;bottom:12%; }
.sangong9 .ranking  .button img{width:33%;}

.sangong12 .ranking{position: absolute;width: 800px;height:1400px;top:0;left:0;z-index: 110;}
.sangong12 .ranking .roundEndShow{display: none;}
.sangong12 .ranking .rankBack{width: 100%;height:100%;background: #000;opacity:1.0}
.sangong12 .ranking .rankBack .bg{position: absolute;width: 100%;height:100%;background: #000;opacity:1.0}
.sangong12 .ranking .rankText{width: 100%;position: absolute;top:10%;left: 0;}
.sangong12 .ranking .rankText .title{width: 100%;}
.sangong12 .ranking .rankText .time{text-align: center;margin-top: 24px;margin-bottom: 20px;}
.sangong12 .ranking .rankText .time a{border-radius: 20px;border: 2px solid #f7d92b;color:#f7d92b;padding: 4px 16px;width: 400px;font-size: 24px;}
.sangong12 .ranking .rankText .scoresItem{width:516px;margin:0 auto; margin-top: 10px;height:96px;line-height:96px;font-size:32px;position: relative;background-color: #1b160c; }
.sangong12 .ranking .rankText .scoresItemWhite{color:#fff; }
.sangong12 .ranking .rankText .scoresItemWhite a{color:#fff; }
.sangong12 .ranking .rankText .scoresItemYellow{color:#f7d92b;}
.sangong12 .ranking .rankText .scoresItemYellow a{color:#f7d92b;}
.sangong12 .ranking .rankText .scoresItem .name{left: 16%;width: 50%;height: 96px;overflow: hidden;word-break:break-all;position: absolute;top:0;}
.sangong12 .ranking .rankText .scoresItem .currentScores{width:25%;text-align: left;position: absolute;right: 0;top:0;}
.sangong12 .ranking  .button{width:100%;position: absolute;bottom:12%; }
.sangong12 .ranking  .button img{width:33%;}
	
</style>
</head>

<body >
<div id="app-main">
	<div id="endCreateRoom" class="end" style="position: fixed;width: 400px;height:700px;top:0;left:0;z-index: 120;display: none;overflow: hidden;">
		<img src="" style="width: 400px;position: absolute;top:0;left: 0;height: 700px;" id="end"  usemap="#planetmap1" />
	</div>
	<div style="position: fixed;top:0;left:0;width:100%;height:100%;background: #fff;z-index: 2;"></div>
	
	<div class="flower" v-if="game_type==1||game_type==111">
		<div class="ranking hideRanking" id="ranking" style="z-index: 1">
			<div class="rankBack">
			<img src="../files/images/game/f_rank_bg.jpg" style="position: absolute;top: 0;left: 0;width: 100%">
			</div>
			<div class="rankText" style="position: absolute;top: 4%;">
				<img src="../files/images/game/f_rank_frame.png" style="position: absolute;top: 0%;left: 20px;width: 760px;">
				<div class="time"  style="position: absolute;top: 182px;width: 100%;">
					<a style="border-color: #ce6eff;background-color: #382b5e;font-size: 24px;">房间号:{{room_number}}&nbsp&nbsp&nbsp&nbsp{{gameDetail.start_time}}&nbsp&nbsp&nbsp&nbsp{{gameDetail.rule_text}}局</a>
				</div>
				<div style="height: 254px;"></div>			
				<div v-for="p in gameDetail.balance_board" class="scoresItem" v-bind:class="{true: 'scoresItemYellow', false: 'scoresItemWhite'}[p.score>0]" v-show="p.account_id>0">
					<img src="../files/images/game/rank_bigwinner2.png" style="top: 0; margin-top: -10px; left: 4px;height: 100%" v-show="p.isBigWinner==1">
					<div class="name">{{p.name}}</div>
					<div class="currentScores"><a v-show="p.score>0">+</a>{{p.score}}</div>
				</div>
			</div>
			<div class="button roundEndShow" >
				<img src="../files/images/game/scoresRank3.png" style="float: left;margin-left: 34%;" />
			</div>
		</div>
	</div>

	<div class="vflower6" v-if="game_type==92">
		<div class="ranking hideRanking" id="ranking" style="z-index: 1">
			<div class="rankBack">
			<img src="../files/images/game/f_rank_bg.jpg" style="position: absolute;top: 0;left: 0;width: 100%">
			</div>
			<div class="rankText" style="position: absolute;top: 4%;">
				<img src="../files/images/game/f_rank_frame.png" style="position: absolute;top: 0%;left: 20px;width: 760px;">
				<div class="time"  style="position: absolute;top: 182px;width: 100%;">
					<a style="border-color: #ce6eff;background-color: #382b5e;font-size: 24px;">房间号:{{room_number}}&nbsp&nbsp&nbsp&nbsp{{gameDetail.start_time}}&nbsp&nbsp&nbsp&nbsp{{gameDetail.rule_text}}局</a>
				</div>
				<div style="height: 254px;"></div>

				<div class="scoresItem scoresItemWhite">
					<div class="name">昵称</div>
					<div class="currentScores">得分</div>
					<div class="consumeScores">消耗</div>
				</div>

				<div v-for="p in gameDetail.balance_board" class="scoresItem" v-bind:class="{true: 'scoresItemYellow', false: 'scoresItemWhite'}[p.score>0]" v-show="p.account_id>0">
					<img src="../files/images/game/rank_bigwinner2.png" style="top: 0; margin-top: -10px; left: 4px;height: 100%" v-show="p.isBigWinner==1">
					<div class="name">{{p.name}}</div>
					<div class="currentScores"><a v-show="p.score>0">+</a>{{p.score}}</div>
					<div class="consumeScores">{{p.consume}}</div>
				</div>
			</div>
			<div class="button roundEndShow" >
				<img src="../files/images/game/scoresRank3.png" style="float: left;margin-left: 34%;" />
			</div>
		</div>
	</div>

	<div class="vflower10" v-if="game_type==95">
		<div class="ranking hideRanking" id="ranking" style="z-index: 1">
			<div class="rankBack">
			<img src="../files/images/game/f_rank_bg.jpg" style="position: absolute;top: 0;left: 0;width: 100%">
			</div>
			<div class="rankText" style="position: absolute;top: 4%;">
				<img src="../files/images/game/f_rank_frame.png" style="position: absolute;top: 0%;left: 20px;width: 760px;">
				<div class="time"  style="position: absolute;top: 182px;width: 100%;">
					<a style="border-color: #ce6eff;background-color: #382b5e;font-size: 24px;">房间号:{{room_number}}&nbsp&nbsp&nbsp&nbsp{{gameDetail.start_time}}&nbsp&nbsp&nbsp&nbsp{{gameDetail.rule_text}}局</a>
				</div>
				<div style="height: 254px;"></div>

				<div class="scoresItem scoresItemWhite">
					<div class="name">昵称</div>
					<div class="currentScores">得分</div>
					<div class="consumeScores">消耗</div>
				</div>

				<div v-for="p in gameDetail.balance_board" class="scoresItem" v-bind:class="{true: 'scoresItemYellow', false: 'scoresItemWhite'}[p.score>0]" v-show="p.account_id>0">
					<img src="../files/images/game/rank_bigwinner2.png" style="top: 0; margin-top: -10px; left: 4px;height: 100%" v-show="p.isBigWinner==1">
					<div class="name">{{p.name}}</div>
					<div class="currentScores"><a v-show="p.score>0">+</a>{{p.score}}</div>
					<div class="consumeScores">{{p.consume}}</div>
				</div>
			</div>
			<div class="button roundEndShow" >
				<img src="../files/images/game/scoresRank3.png" style="float: left;margin-left: 34%;" />
			</div>
		</div>
	</div>

	<div class="flower10" v-if="game_type==110">
		<div class="ranking hideRanking" id="ranking" style="z-index: 1">
			<div class="rankBack">
			<img src="../files/images/game/f_rank_bg.jpg" style="position: absolute;top: 0;left: 0;width: 100%">
			</div>
			<div class="rankText" style="position: absolute;top: 4%;">
				<img src="../files/images/game/f_rank_frame.png" style="position: absolute;top: 0%;left: 20px;width: 760px; height: 1086px;">
				<div class="time"  style="position: absolute;top: 182px;width: 100%;">
					<a style="border-color: #ce6eff;background-color: #382b5e;font-size: 24px;">房间号:{{room_number}}&nbsp&nbsp&nbsp&nbsp{{gameDetail.start_time}}&nbsp&nbsp&nbsp&nbsp{{gameDetail.rule_text}}局</a>
				</div>
				<div style="height: 254px;"></div>
				<div v-for="p in gameDetail.balance_board" class="scoresItem" v-bind:class="{true: 'scoresItemYellow', false: 'scoresItemWhite'}[p.score>0]" v-show="p.account_id>0">
					<img src="../files/images/game/rank_bigwinner2.png" style="top: 0; margin-top: -10px; left: 4px;height: 100%" v-show="p.isBigWinner==1">
					<div class="name">{{p.name}}</div>
					<div class="currentScores"><a v-show="p.score>0">+</a>{{p.score}}</div>
				</div>
			</div>
			<div class="button roundEndShow" >
				<img src="../files/images/game/scoresRank3.png" style="float: left;margin-left: 34%;" />
			</div>
		</div>
	</div>
	
	
	<div class="bull" v-if="game_type==5">      
        <div class="ranking hideRanking" id="ranking" style="z-index: 1">
			<div class="rankBack">
				<img src="../files/images/game/b_rank_bg.jpg" style="position: absolute;top: 0;left: 0;width: 100%">
			</div>
			<div class="rankText" style="position: absolute;top: 4%;">
				<img src="../files/images/game/b_rank_frame62.png" style="position: absolute;top: 0%;left: 120px;width: 560px;">
				<div class="time" style="position: absolute;top: 192px;width: 100%;">
					<a style="border-color: #fffcd5;background-color: #56492c;font-size: 24px;">房间号:{{room_number}}&nbsp&nbsp&nbsp&nbsp{{gameDetail.end_time}}&nbsp&nbsp&nbsp&nbsp{{gameDetail.rule_text}}局</a>
				</div>
				<div style="height: 268px;"></div>			
				<div v-for="p in gameDetail.balance_board" class="scoresItem" v-bind:class="{true: 'scoresItemYellow', false: 'scoresItemWhite'}[p.score>0]" v-show="p.account_id>0">
				    <img src="../files/images/game/rank_bigwinner2.png" style="top: 0; margin-top: -10px; left: 4px;height: 100%" v-show="p.isBigWinner==1">
					<div class="name">{{p.name}}</div>
					<div class="currentScores"><a v-show="p.score>0">+</a>{{p.score}}</div>
				</div>
			</div>
			<div class="button roundEndShow" >
				<img src="../files/images/game/scoresRank3.png" style="float: left;margin-left: 34%;" />
			</div>			
		</div>	
	</div>

	<div class="bull" v-if="game_type==93">
        <div class="ranking hideRanking" id="ranking" style="z-index: 1">
			<div class="rankBack">
				<img src="../files/images/game/b_rank_bg.jpg" style="position: absolute;top: 0;left: 0;width: 100%">
			</div>
			<div class="rankText" style="position: absolute;top: 4%;">
				<img src="../files/images/game/b_rank_frame62.png" style="position: absolute;top: 0%;left: 120px;width: 560px;">
				<div class="time" style="position: absolute;top: 192px;width: 100%;">
					<a style="border-color: #fffcd5;background-color: #56492c;font-size: 24px;">房间号:{{room_number}}&nbsp&nbsp&nbsp&nbsp{{gameDetail.end_time}}&nbsp&nbsp&nbsp&nbsp{{gameDetail.rule_text}}局</a>
				</div>
				<div style="height: 268px;"></div>
				<div v-for="p in gameDetail.balance_board" class="scoresItem" v-bind:class="{true: 'scoresItemYellow', false: 'scoresItemWhite'}[p.score>0]" v-show="p.account_id>0">
				    <img src="../files/images/game/rank_bigwinner2.png" style="top: 0; margin-top: -10px; left: 4px;height: 100%" v-show="p.isBigWinner==1">
					<div class="name">{{p.name}}</div>
					<div class="currentScores"><a v-show="p.score>0">+</a>{{p.score}}</div>
				</div>
			</div>
			<div class="button roundEndShow" >
				<img src="../files/images/game/scoresRank3.png" style="float: left;margin-left: 34%;" />
			</div>
		</div>
	</div>
	
	<div class="bull9" v-if="game_type==9">
        <div class="ranking hideRanking" id="ranking" style="z-index: 1">
			<div class="rankBack">
				<img src="../files/images/game/b9_rank_bg.jpg" style="position: absolute;top: 0;left: 0;width: 100%">
			</div>

			<div class="rankText" style="position: absolute;top: 4%;">
				<img src="../files/images/game/b9_rank_frame92.png" style="position: absolute;top: 0%;left: 120px;width: 560px;">
				<div class="time"  style="position: absolute;top: 192px;width: 100%;">
					<a style="border-color: #fffcd5;background-color: #56492c;font-size: 24px;">房间号:{{room_number}}&nbsp&nbsp&nbsp&nbsp{{gameDetail.end_time}}&nbsp&nbsp&nbsp&nbsp{{gameDetail.rule_text}}局</a>
				</div>
				<div style="height: 268px;"></div>			
				<div v-for="p in gameDetail.balance_board" class="scoresItem" v-bind:class="{true: 'scoresItemYellow', false: 'scoresItemWhite'}[p.score>0]" v-show="p.account_id>0">
				    <img src="../files/images/game/rank_bigwinner2.png" style="top: 0; margin-top: -10px; left: 4px;height: 100%" v-show="p.isBigWinner==1">
					<div class="name">{{p.name}}</div>
					<div class="currentScores"><a v-show="p.score>0">+</a>{{p.score}}</div>
				</div>
			</div>
			<div class="button roundEndShow" >
				<img src="../files/images/game/scoresRank3.png" style="float: left;margin-left: 34%;" />
			</div>
		</div>
	</div>

	<div class="bull9" v-if="game_type==91">
        <div class="ranking hideRanking" id="ranking" style="z-index: 1">
			<div class="rankBack">
				<img src="../files/images/game/b9_rank_bg.jpg" style="position: absolute;top: 0;left: 0;width: 100%">
			</div>

			<div class="rankText" style="position: absolute;top: 4%;">
				<img src="../files/images/game/b9_rank_frame92.png" style="position: absolute;top: 0%;left: 120px;width: 560px;">
				<div class="time"  style="position: absolute;top: 192px;width: 100%;">
					<a style="border-color: #fffcd5;background-color: #56492c;font-size: 24px;">房间号:{{room_number}}&nbsp&nbsp&nbsp&nbsp{{gameDetail.end_time}}&nbsp&nbsp&nbsp&nbsp{{gameDetail.rule_text}}局</a>
				</div>
				<div style="height: 268px;"></div>
				<div v-for="p in gameDetail.balance_board" class="scoresItem" v-bind:class="{true: 'scoresItemYellow', false: 'scoresItemWhite'}[p.score>0]" v-show="p.account_id>0">
				    <img src="../files/images/game/rank_bigwinner2.png" style="top: 0; margin-top: -10px; left: 4px;height: 100%" v-show="p.isBigWinner==1">
					<div class="name">{{p.name}}</div>
					<div class="currentScores"><a v-show="p.score>0">+</a>{{p.score}}</div>
				</div>
			</div>
			<div class="button roundEndShow" >
				<img src="../files/images/game/scoresRank3.png" style="float: left;margin-left: 34%;" />
			</div>
		</div>
	</div>

	<div class="bull12" v-if="game_type==12">
        <div class="ranking hideRanking" id="ranking" style="z-index: 1">
			<div class="rankBack">
				<img src="../files/images/game/b12_rank_bg.jpg" style="position: absolute;top: 0;left: 0;width: 100%">
			</div>

			<div class="rankText" style="position: absolute;top: 4%;">
				<img src="../files/images/game/b12_rank_frame122.png" style="position: absolute;top: 0%;left: 120px;width: 560px;">
				<div class="time"  style="position: absolute;top: 192px;width: 100%;">
					<a style="border-color: #fffcd5;background-color: #56492c;font-size: 24px;">房间号:{{room_number}}&nbsp&nbsp&nbsp&nbsp{{gameDetail.end_time}}&nbsp&nbsp&nbsp&nbsp{{gameDetail.rule_text}}局</a>
				</div>
				<div style="height: 268px;"></div>
				<div v-for="p in gameDetail.balance_board" class="scoresItem" v-bind:class="{true: 'scoresItemYellow', false: 'scoresItemWhite'}[p.score>0]" v-show="p.account_id>0">
				    <img src="../files/images/game/rank_bigwinner2.png" style="top: 0; margin-top: -10px; left: 4px;height: 100%" v-show="p.isBigWinner==1">
					<div class="name">{{p.name}}</div>
					<div class="currentScores"><a v-show="p.score>0">+</a>{{p.score}}</div>
				</div>
			</div>
			<div class="button roundEndShow" >
				<img src="../files/images/game/scoresRank3.png" style="float: left;margin-left: 34%;" />
			</div>
		</div>
	</div>

    <div class="bull12" v-if="game_type==71">
        <div class="ranking hideRanking" id="ranking" style="z-index: 1">
            <div class="rankBack">
                <img src="../files/images/game/b12_rank_bg.jpg" style="position: absolute;top: 0;left: 0;width: 100%">
            </div>

            <div class="rankText" style="position: absolute;top: 4%;">
                <img src="../files/images/game/b12_rank_frame122.png" style="position: absolute;top: 0%;left: 120px;width: 560px;">
                <div class="time"  style="position: absolute;top: 192px;width: 100%;">
                    <a style="border-color: #fffcd5;background-color: #56492c;font-size: 24px;">房间号:{{room_number}}&nbsp&nbsp&nbsp&nbsp{{gameDetail.end_time}}&nbsp&nbsp&nbsp&nbsp{{gameDetail.rule_text}}局</a>
                </div>
                <div style="height: 268px;"></div>
                <div v-for="p in gameDetail.balance_board" class="scoresItem" v-bind:class="{true: 'scoresItemYellow', false: 'scoresItemWhite'}[p.score>0]" v-show="p.account_id>0">
                    <img src="../files/images/game/rank_bigwinner2.png" style="top: 0; margin-top: -10px; left: 4px;height: 100%" v-show="p.isBigWinner==1">
                    <div class="name">{{p.name}}</div>
                    <div class="currentScores"><a v-show="p.score>0">+</a>{{p.score}}</div>
                </div>
            </div>
            <div class="button roundEndShow" >
                <img src="../files/images/game/scoresRank3.png" style="float: left;margin-left: 34%;" />
            </div>
        </div>
    </div>

    <div class="bull12" v-if="game_type==94">
        <div class="ranking hideRanking" id="ranking" style="z-index: 1">
            <div class="rankBack">
                <img src="../files/images/game/b12_rank_bg.jpg" style="position: absolute;top: 0;left: 0;width: 100%">
            </div>

            <div class="rankText" style="position: absolute;top: 4%;">
                <img src="../files/images/game/b12_rank_frame122.png" style="position: absolute;top: 0%;left: 120px;width: 560px;">
                <div class="time"  style="position: absolute;top: 192px;width: 100%;">
                    <a style="border-color: #fffcd5;background-color: #56492c;font-size: 24px;">房间号:{{room_number}}&nbsp&nbsp&nbsp&nbsp{{gameDetail.end_time}}&nbsp&nbsp&nbsp&nbsp{{gameDetail.rule_text}}局</a>
                </div>
                <div style="height: 268px;"></div>
                <div v-for="p in gameDetail.balance_board" class="scoresItem" v-bind:class="{true: 'scoresItemYellow', false: 'scoresItemWhite'}[p.score>0]" v-show="p.account_id>0">
                    <img src="../files/images/game/rank_bigwinner2.png" style="top: 0; margin-top: -10px; left: 4px;height: 100%" v-show="p.isBigWinner==1">
                    <div class="name">{{p.name}}</div>
                    <div class="currentScores"><a v-show="p.score>0">+</a>{{p.score}}</div>
                </div>
            </div>
            <div class="button roundEndShow" >
                <img src="../files/images/game/scoresRank3.png" style="float: left;margin-left: 34%;" />
            </div>
        </div>
    </div>


    <div class="sangong" v-if="game_type==36">
        <div class="ranking hideRanking" id="ranking" style="z-index: 1">
            <div class="rankBack">
                <img src="../files/images/game/sg_rank_bg.jpg" style="position: absolute;top: 0;left: 0;width: 100%">
            </div>
            <div class="rankText" style="position: absolute;top: 4%;">
                <img src="../files/images/game/sg_rank_frame.png" style="position: absolute;top: 0%;left: 120px;width: 560px;">
                <div class="time" style="position: absolute;top: 192px;width: 100%;">
                    <a style="border-color: #fffcd5;background-color: #56492c;font-size: 24px;">房间号:{{room_number}}&nbsp&nbsp&nbsp&nbsp{{gameDetail.end_time}}&nbsp&nbsp&nbsp&nbsp{{gameDetail.rule_text}}局</a>
                </div>
                <div style="height: 268px;"></div>
                <div v-for="p in gameDetail.balance_board" class="scoresItem" v-bind:class="{true: 'scoresItemYellow', false: 'scoresItemWhite'}[p.score>0]" v-show="p.account_id>0">
                    <img src="../files/images/game/rank_bigwinner2.png" style="top: 0; margin-top: -10px; left: 4px;height: 100%" v-show="p.isBigWinner==1">
                    <div class="name">{{p.name}}</div>
                    <div class="currentScores"><a v-show="p.score>0">+</a>{{p.score}}</div>
                </div>
            </div>
            <div class="button roundEndShow" >
                <img src="../files/images/game/scoresRank3.png" style="float: left;margin-left: 34%;" />
            </div>
        </div>
    </div>


    <div class="sangong9" v-if="game_type==37">
        <div class="ranking hideRanking" id="ranking" style="z-index: 1">
            <div class="rankBack">
                <img src="../files/images/game/sg_rank_bg.jpg" style="position: absolute;top: 0;left: 0;width: 100%">
            </div>
            <div class="rankText" style="position: absolute;top: 4%;">
                <img src="../files/images/game/sg9_rank_frame.png" style="position: absolute;top: 0%;left: 120px;width: 560px;">
                <div class="time" style="position: absolute;top: 192px;width: 100%;">
                    <a style="border-color: #fffcd5;background-color: #56492c;font-size: 24px;">房间号:{{room_number}}&nbsp&nbsp&nbsp&nbsp{{gameDetail.end_time}}&nbsp&nbsp&nbsp&nbsp{{gameDetail.rule_text}}局</a>
                </div>
                <div style="height: 268px;"></div>
                <div v-for="p in gameDetail.balance_board" class="scoresItem" v-bind:class="{true: 'scoresItemYellow', false: 'scoresItemWhite'}[p.score>0]" v-show="p.account_id>0">
                    <img src="../files/images/game/rank_bigwinner2.png" style="top: 0; margin-top: -10px; left: 4px;height: 100%" v-show="p.isBigWinner==1">
                    <div class="name">{{p.name}}</div>
                    <div class="currentScores"><a v-show="p.score>0">+</a>{{p.score}}</div>
                </div>
            </div>
            <div class="button roundEndShow" >
                <img src="../files/images/game/scoresRank3.png" style="float: left;margin-left: 34%;" />
            </div>
        </div>
    </div>


    <div class="sangong12" v-if="game_type==38">
        <div class="ranking hideRanking" id="ranking" style="z-index: 1">
            <div class="rankBack">
                <img src="../files/images/game/sg_rank_bg.jpg" style="position: absolute;top: 0;left: 0;width: 100%">
            </div>
            <div class="rankText" style="position: absolute;top: 4%;">
                <img src="../files/images/game/sg9_rank_frame.png" style="position: absolute;top: 0%;left: 120px;width: 560px;">
                <div class="time" style="position: absolute;top: 192px;width: 100%;">
                    <a style="border-color: #fffcd5;background-color: #56492c;font-size: 24px;">房间号:{{room_number}}&nbsp&nbsp&nbsp&nbsp{{gameDetail.end_time}}&nbsp&nbsp&nbsp&nbsp{{gameDetail.rule_text}}局</a>
                </div>
                <div style="height: 268px;"></div>
                <div v-for="p in gameDetail.balance_board" class="scoresItem" v-bind:class="{true: 'scoresItemYellow', false: 'scoresItemWhite'}[p.score>0]" v-show="p.account_id>0">
                    <img src="../files/images/game/rank_bigwinner2.png" style="top: 0; margin-top: -10px; left: 4px;height: 100%" v-show="p.isBigWinner==1">
                    <div class="name">{{p.name}}</div>
                    <div class="currentScores"><a v-show="p.score>0">+</a>{{p.score}}</div>
                </div>
            </div>
            <div class="button roundEndShow" >
                <img src="../files/images/game/scoresRank3.png" style="float: left;margin-left: 34%;" />
            </div>
        </div>
    </div>

</div> 
</body>
<script type="text/javascript">
var dealer_num="<?php echo $dealer_num;?>";
var room_number="<?php echo $room_number;?>";
var game_type="<?php echo $game_type;?>";
var round="<?php echo $round;?>";
var methods = {
    getRoomGameResult: function (num) {  
        Vue.http.post('../game/getRoomGameResult', {
        	"dealer_num":dealer_num,
        	"room_number":room_number,
        	"game_type":game_type,
        	"round":round,
        }).then(function(response) {
            var bodyData = response.body;
            if (bodyData.result == 0) {           
           		appData.gameDetail=bodyData.data; 
           		for(var i=0;i<appData.gameDetail.balance_board.length;i++){
					appData.gameDetail.balance_board[i].score = parseInt(appData.gameDetail.balance_board[i].score);
					appData.gameDetail.balance_board[i].isBigWinner=0;
				}
				appData.gameDetail.rule_text = appData.gameDetail.rule_text.substring(0,appData.gameDetail.rule_text.indexOf("局/"));
				chooseBigWinner()
				setTimeout(function(){
					canvas();
				},500)
            } else if(bodyData.result == -3)
            {
                window.location.reload();
            } else {
                alert(bodyData.result_message);
            }

        }, function(response) {

        });
    },    
}


var appData={
	gameDetail:{
		balance_board:[			

		],
		end_time:"",
		rule_text:"",
		isBigWinner:0
	},
	game_type:game_type,
	room_number:room_number
}

//Vue生命周期
var vueLife = {
    vmCreated: function() {
       logMessage('vmCreated');
       methods.getRoomGameResult(); 

    },
    vmUpdated: function() {
        logMessage('vmUpdated');
    },
    vmMounted: function() {
        logMessage('vmMounted');
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

function chooseBigWinner() {
    var length = appData.gameDetail.balance_board.length;
    var maxScore = 1;
    for (var i = 0; i < length; i++) {
        appData.gameDetail.isBigWinner = 0;
        if (appData.gameDetail.balance_board[i].score > maxScore) {
            maxScore = appData.gameDetail.balance_board[i].score;
        }
    }

    for (var i = 0; i < length; i++) {
        if (appData.gameDetail.balance_board[i].score == maxScore) {
            appData.gameDetail.balance_board[i].isBigWinner = 1;
        }
    }
};

function canvas() {
    var target = document.getElementById("ranking");
    html2canvas(target, {
        allowTaint: true,
        taintTest: false,
        onrendered: function(canvas) {
            canvas.id = "mycanvas";
            var dataUrl = canvas.toDataURL('image/jpeg', 0.3);
            $("#end").attr("src", dataUrl);
            $(".end").show();
            $('.ranking').hide();
        }
    });
};

function logMessage(msg){
	console.log(msg)
}
</script>
<script type="text/javascript" src="<?php echo $base_url;?>files/js/canvas.js"></script>
</html>
