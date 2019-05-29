<html>
<head>
<meta charset="utf-8" >
<meta name="viewport" content="width=device-width,initial-scale=1.0, minimum-scale=1.0, maximum-scale=1.0, user-scalable=no"/>
<meta name="format-detection" content="telephone=no" />
<meta name="msapplication-tap-highlight" content="no" />
<title>积分榜</title>

<style type="text/css">
    *{padding: 0;margin:0;-webkit-tap-highlight-color:rgba(0,0,0,0);-webkit-backface-visibility: hidden;-webkit-overflow-scrolling: touch;}a {text-decoration: none;color: #fff;}ul {list-style: none;}input{border: none;outline:none}body{font-family: 'Helvetica Neue', Helvetica, 'Hiragino Sans GB', 'Microsoft YaHei', 微软雅黑, Arial, sans-serif;cursor: default;}
    img{border: none;}

    .rankPanel {display: none}

    .flower .ranking{position: absolute;width: 800px;height:1400px;top:0;left:0;z-index: 110;}
    .flower .ranking .roundEndShow{display: none;}
    .flower .ranking .rankBack{width: 100%;height:100%;background: #000;opacity:1.0}
    .flower .ranking .rankBack .bg{position: absolute;width: 100%;height:100%;background: #000;opacity:1.0}
    .flower .ranking .rankText{width: 100%;position: absolute;top:10%;left: 0;}
    .flower .ranking .rankText .title{width: 100%;}
    .flower .ranking .rankText .time{text-align: center;margin-top: 24px;margin-bottom: 20px;}
    .flower .ranking .rankText .time a{border-radius: 8px;border: 2px solid #7f7e85;color:#FBF0D4;padding: 4px 16px;width: 400px;font-size: 24px;}
    .flower .ranking .rankText .scoresItem{width:91%;margin:0 auto;height:70px;line-height:70px;font-size:32px;position: relative;border-top: 1px solid #CB9564;}
    .flower .ranking .rankText .scoresItemTitle{color:#633201;background: #DBB272;width:91%;}
    .flower .ranking .rankText .scoresItemWhite{color:#5F4838; }
    .flower .ranking .rankText .scoresItemWhite a{color:#5F4838; }
    .flower .ranking .rankText .scoresItemYellow{color:#E07C21;}
    .flower .ranking .rankText .scoresItemYellow a{color:#E07C21;}
    .flower .ranking .rankText .room_own{width: 91%;margin: 0 auto;text-align: center;height: 80px;line-height: 80px;position: relative;background:url("<?php echo $image_url;?>files/images/common/room_own.png")no-repeat center center; background-size:80% 70%;}
    .flower .ranking .rankText .room_own p{font-size: 20px;position: absolute;width: 100%;left: 0;top: 0;letter-spacing: 4px;font-size: 18px;color: #5F3811;font-weight: 600;margin-top: -6px;}
    .flower .ranking .rankText .scoresItem .user_code{left: 2%;width: 25%;text-align: center;overflow: hidden;word-break:break-all;position: absolute;top:0;}
    .flower .ranking .rankText .scoresItem .name{left: 26%;width: 52%;text-align: center;overflow: hidden;word-break:break-all;position: absolute;top:0;text-overflow:ellipsis;white-space:nowrap;text-align: center;}
    .flower .ranking .rankText .scoresItem .currentScores{width:25%;text-align: center;position: absolute;right: 0;top:0;}
    .flower .ranking  .button{width:100%;position: absolute;bottom:12%; }
    .flower .ranking  .button img{width:33%;}

    .vflower6 .ranking{position: absolute;width: 800px;height:1400px;top:0;left:0;z-index: 110;}
    .vflower6 .ranking .roundEndShow{display: none;}
    .vflower6 .ranking .rankBack{width: 100%;height:100%;background: #000;opacity:1.0}
    .vflower6 .ranking .rankBack .bg{position: absolute;width: 100%;height:100%;background: #000;opacity:1.0}
    .vflower6 .ranking .rankText{width: 100%;position: absolute;top:10%;left: 0;}
    .vflower6 .ranking .rankText .title{width: 100%;}
    .vflower6 .ranking .rankText .time{text-align: center;margin-top: 24px;margin-bottom: 20px;}
    .vflower6 .ranking .rankText .time a{border-radius: 8px;border: 2px solid #ce6eff;color:#FBF0D4;padding: 4px 16px;width: 400px;font-size: 24px;}
    .vflower6 .ranking .rankText .scoresItem{width:516px;margin:0 auto; margin-top: 2px;height:20vw;line-height:20vw;font-size:8vw;position: relative;}
    .vflower6 .ranking .rankText .scoresItemWhite{color:#5F4838; }
    .vflower6 .ranking .rankText .scoresItemWhite a{color:#5F4838; }
    .vflower6 .ranking .rankText .scoresItemYellow{color:#E07C21;}
    .vflower6 .ranking .rankText .scoresItemYellow a{color:#E07C21;}
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
    .vflower10 .ranking .rankText .time a{border-radius: 8px;border: 2px solid #ce6eff;color:#FBF0D4;padding: 4px 16px;width: 400px;font-size: 24px;}
    .vflower10 .ranking .rankText .scoresItem{width:516px;margin:0 auto; margin-top: 1px;height:16vw;line-height:16vw;font-size:8vw;position: relative;background-color: #160f2b; }
    .vflower10 .ranking .rankText .scoresItemWhite{color:#5F4838; }
    .vflower10 .ranking .rankText .scoresItemWhite a{color:#5F4838; }
    .vflower10 .ranking .rankText .scoresItemYellow{color:#E07C21;}
    .vflower10 .ranking .rankText .scoresItemYellow a{color:#E07C21;}
    .vflower10 .ranking .rankText .scoresItem .name{left: 14%;width: 42%;overflow: hidden;word-break:break-all;position: absolute;top:0;}
    .vflower10 .ranking .rankText .scoresItem .currentScores{left: 56%;text-align: left;position: absolute;right: 0;top:0;}
    .vflower10 .ranking .rankText .scoresItem .consumeScores{left: 84%;text-align: left;position: absolute;right: 0;top:0;}
    .vflower10 .ranking  .button{width:100%;position: absolute;bottom:12%; }
    .vflower10 .ranking  .button img{width:33%;}

    .bull .ranking{position: absolute;width: 800px;height:1400px;top:0;left:0;z-index: 110;}
    .bull .ranking .roundEndShow{}
    .bull .ranking .rankBack{width: 100%;height:100%;background: #000;opacity:1.0}
    .bull .ranking .rankBack .bg{position: absolute;width: 100%;height:100%;background: #000;opacity:1.0}
    .bull .ranking .rankText{width: 100%;position: absolute;top:10%;left: 0;}
    .bull .ranking .rankText .title{width: 100%;}
    .bull .ranking .rankText .time{text-align: center;margin-top: 24px;margin-bottom: 20px;}
    .bull .ranking .rankText .time a{border-radius: 8px;border: 2px solid #7f7e85;color:#FBF0D4;padding: 4px 16px;width: 400px;font-size: 24px;}
    .bull .ranking .rankText .scoresItem{width:640px;margin:0 auto;height:96px;line-height:96px;font-size:32px;position: relative;border-top: 1px solid #CB9564;}
    .bull .ranking .rankText .scoresItemTitle{color:#633201;background: #DBB272;width:80%;}
    .bull .ranking .rankText .scoresItemWhite{color:#5F4838; }
    .bull .ranking .rankText .scoresItemWhite a{color:#5F4838; }
    .bull .ranking .rankText .scoresItemYellow{color:#E07C21;}
    .bull .ranking .rankText .scoresItemYellow a{color:#E07C21;}
    .bull .ranking .rankText .room_own{width: 91%;margin: 0 auto;text-align: center;height: 80px;line-height: 80px;position: relative;background:url("<?php echo $image_url;?>files/images/common/room_own.png")no-repeat center center; background-size:80% 70%;}
    .bull .ranking .rankText .room_own p{font-size: 20px;position: absolute;width: 100%;left: 0;top: 0;letter-spacing: 4px;font-size: 18px;color: #5F3811;font-weight: 600;margin-top: -6px;}
    .bull .ranking .rankText .scoresItem .user_code{left: 4%;width: 25%;text-align: center;overflow: hidden;word-break:break-all;position: absolute;top:0;}
    .bull .ranking .rankText .scoresItem .name{left: 38%;width: 30%;height: 96px;overflow: hidden;word-break:break-all;position: absolute;top:0;text-overflow:ellipsis;white-space:nowrap;text-align: center;}
    .bull .ranking .rankText .scoresItem .currentScores{width:25%;text-align: left;position: absolute;right: 0;top:0;}
    .bull .ranking  .button{width:100%;position: absolute;bottom:12%; }
    .bull .ranking  .button img{width:33%;}

    .vbull6 .ranking{position: absolute;width: 800px;height:1400px;top:0;left:0;z-index: 110;}
    .vbull6 .ranking .roundEndShow{}
    .vbull6 .ranking .rankBack{width: 100%;height:100%;background: #000;opacity:1.0}
    .vbull6 .ranking .rankBack .bg{position: absolute;width: 100%;height:100%;background: #000;opacity:1.0}
    .vbull6 .ranking .rankText{width: 100%;position: absolute;top:10%;left: 0;}
    .vbull6 .ranking .rankText .title{width: 100%;}
    .vbull6 .ranking .rankText .time{text-align: center;margin-top: 24px;margin-bottom: 20px;}
    .vbull6 .ranking .rankText .time a{border-radius: 8px;border: 2px solid #7f7e85;color:#FBF0D4;padding: 4px 16px;width: 400px;font-size: 24px;}
    .vbull6 .ranking .rankText .scoresItem{width:516px;margin:0 auto; margin-top: 10px;height:96px;line-height:96px;font-size:32px;position: relative;background-color: #1b160c; }
    .vbull6 .ranking .rankText .scoresItemWhite{color:#5F4838; }
    .vbull6 .ranking .rankText .scoresItemWhite a{color:#5F4838; }
    .vbull6 .ranking .rankText .scoresItemYellow{color:#E07C21;}
    .vbull6 .ranking .rankText .scoresItemYellow a{color:#E07C21;}
    .vbull6 .ranking .rankText .scoresItem .name{left: 10%;width: 42%;height: 96px;overflow: hidden;word-break:break-all;position: absolute;top:0;}
    .vbull6 .ranking .rankText .scoresItem .currentScores{left: 52%;text-align: left;position: absolute;right: 0;top:0;}
    .vbull6 .ranking .rankText .scoresItem .consumeScores{left: 85%;text-align: left;position: absolute;right: 0;top:0;}
    .vbull6 .ranking  .button{width:100%;position: absolute;bottom:12%; }
    .vbull6 .ranking  .button img{width:33%;}

    .bull9 .ranking{position: absolute;width: 800px;height:1400px;top:0;left:0;z-index: 110;}
    .bull9 .ranking .roundEndShow{display: none;}
    .bull9 .ranking .rankBack{width: 100%;height:100%;background: #000;opacity:1.0}
    .bull9 .ranking .rankBack .bg{position: absolute;width: 100%;height:100%;background: #000;opacity:1.0}
    .bull9 .ranking .rankText{width: 100%;position: absolute;top:10%;left: 0;}
    .bull9 .ranking .rankText .title{width: 100%;}
    .bull9 .ranking .rankText .time{text-align: center;margin-top: 24px;margin-bottom: 20px;}
    .bull9 .ranking .rankText .time a{border-radius: 8px;border: 2px solid #7f7e85;color:#FBF0D4;padding: 4px 16px;width: 400px;font-size: 24px;}
    .bull9 .ranking .rankText .scoresItem{width:656px;margin:0 auto;height:70px;line-height:70px;font-size:32px;position: relative;border-top: 1px solid #CB9564;}
    .bull9 .ranking .rankText .scoresItemTitle{color:#633201;background: #DBB272;width:82%;}
    .bull9 .ranking .rankText .scoresItemWhite{color:#5F4838; }
    .bull9 .ranking .rankText .scoresItemWhite a{color:#5F4838; }
    .bull9 .ranking .rankText .scoresItemYellow{color:#E07C21;}
    .bull9 .ranking .rankText .scoresItemYellow a{color:#E07C21;}
    .bull9 .ranking .rankText .room_own{width: 91%;margin: 0 auto;text-align: center;height: 80px;line-height: 80px;position: relative;background:url("<?php echo $image_url;?>files/images/common/room_own.png")no-repeat center center; background-size:80% 70%;}
    .bull9 .ranking .rankText .room_own p{font-size: 20px;position: absolute;width: 100%;left: 0;top: 0;letter-spacing: 4px;font-size: 18px;color: #5F3811;font-weight: 600;margin-top: -6px;}
    .bull9 .ranking .rankText .scoresItem .user_code{left: 3%;width: 25%;text-align: center;overflow: hidden;word-break:break-all;position: absolute;top:0;}
    .bull9 .ranking .rankText .scoresItem .name{left: 40%;width: 30%;height: 72px;overflow: hidden;word-break:break-all;position: absolute;top:0;text-overflow:ellipsis;white-space:nowrap;text-align: center;}
    .bull9 .ranking .rankText .scoresItem .currentScores{width:25%;text-align: left;position: absolute;right: 0;top:0;}
    .bull9 .ranking  .button{width:100%;position: absolute;bottom:8%; }
    .bull9 .ranking  .button img{width:33%;}


    .vbull9 .ranking{position: absolute;width: 200%;height:200%;top:0;left:0;z-index: 110;}
    .vbull9 .ranking .roundEndShow{display: none;}
    .vbull9 .ranking .rankBack{width: 100%;height:100%;background: #000;opacity:1.0}
    .vbull9 .ranking .rankBack .bg{position: absolute;width: 100%;height:100%;background: #000;opacity:1.0}
    .vbull9 .ranking .rankText{width: 100%;position: absolute;top:3%;left: 0;}
    .vbull9 .ranking .rankText .title{width: 100%;}
    .vbull9 .ranking .rankText .time{text-align: center;margin-top: 24px;margin-bottom: 20px;}
    .vbull9 .ranking .rankText .time a{border-radius: 5vw;border: 2px solid #7f7e85;color:#FBF0D4;padding: 1vw 4vw;width: 400px;font-size: 6vw;}
    .vbull9 .ranking .rankText .scoresItem{width:139vw;margin:0 auto; margin-top: 2px;height:18vw;line-height:18vw;font-size:8vw;position: relative;background-color: #1b160c; }
    .vbull9 .ranking .rankText .scoresItemWhite{color:#5F4838; }
    .vbull9 .ranking .rankText .scoresItemWhite a{color:#5F4838; }
    .vbull9 .ranking .rankText .scoresItemYellow{color:#E07C21;}
    .vbull9 .ranking .rankText .scoresItemYellow a{color:#E07C21;}
    .vbull9 .ranking .rankText .scoresItem .name{left: 10%;width: 42%;height: 15vw;overflow: hidden;word-break:break-all;position: absolute;top:0;}
    .vbull9 .ranking .rankText .scoresItem .currentScores{left: 52%;text-align: left;position: absolute;right: 0;top:0;}
    .vbull9 .ranking .rankText .scoresItem .consumeScores{left: 85%;text-align: left;position: absolute;right: 0;top:0;}
    .vbull9 .ranking  .button{width:100%;position: absolute;bottom:3%; }
    .vbull9 .ranking  .button img{width:33%;}


    .bull12 .ranking{position: absolute;width: 200%;height:200%;top:0;left:0;z-index: 110;}
    .bull12 .ranking .roundEndShow{display: none;}
    .bull12 .ranking .rankBack{width: 100%;height:100%;background: #000;opacity:1.0}
    .bull12 .ranking .rankBack .bg{position: absolute;width: 100%;height:100%;background: #000;opacity:1.0}
    .bull12 .ranking .rankText{width: 100%;position: absolute;top:3%;left: 0;}
    .bull12 .ranking .rankText .title{width: 100%;}
    .bull12 .ranking .rankText .time{text-align: center;margin-top: 24px;margin-bottom: 20px;}
    .bull12 .ranking .rankText .time a{border-radius: 1.5vw;border: 2px solid #7f7e85;color:#FBF0D4;padding: 1vw 4vw;width: 400px;font-size: 6vw;}
    .bull12 .ranking .rankText .scoresItem{width:178.2vw;margin:0 auto;height:14.6vw;line-height:14.6vw;font-size:7vw;position: relative;border-top: 1px solid #CB9564;}
    .bull12 .ranking .rankText .scoresItemTitle{color:#633201;background: #DBB272;width:89.2%;}
    .bull12 .ranking .rankText .scoresItemWhite{color:#5F4838; }
    .bull12 .ranking .rankText .scoresItemWhite a{color:#5F4838; }
    .bull12 .ranking .rankText .scoresItemYellow{color:#E07C21;}
    .bull12 .ranking .rankText .scoresItemYellow a{color:#E07C21;}
    .bull12 .ranking .rankText .room_own{width: 89.2%;margin: 0 auto;text-align: center;height: 80px;line-height: 80px;position: relative;background:url("<?php echo $image_url;?>files/images/common/room_own.png")no-repeat center center; background-size:80% 70%;}
    .bull12 .ranking .rankText .room_own p{font-size: 20px;position: absolute;width: 100%;left: 0;top: 0;letter-spacing: 4px;font-size: 18px;color: #5F3811;font-weight: 600;margin-top: -6px;}
    .bull12 .ranking .rankText .scoresItem .id{left: 10%;width: 25%;height: 15vw;overflow: hidden;word-break:break-all;position: absolute;top:0;}
    .bull12 .ranking .rankText .scoresItem .user_code{left: 1%;width: 25%;text-align: center;overflow: hidden;word-break:break-all;position: absolute;top:0;}
    .bull12 .ranking .rankText .scoresItem .name{left: 32%;width: 42%;height: 15vw;overflow: hidden;word-break:break-all;position: absolute;top:0;text-overflow:ellipsis;white-space:nowrap;text-align: center;}
    .bull12 .ranking .rankText .scoresItem .currentScores{width:18%;text-align: left;position: absolute;right: 0;top:0;}
    .bull12 .ranking  .button{width:100%;position: absolute;bottom:3%; }
    .bull12 .ranking  .button img{width:33%;}

    .vbull12 .ranking{position: absolute;width: 200%;height:200%;top:0;left:0;z-index: 110;}
    .vbull12 .ranking .roundEndShow{display: none;}
    .vbull12 .ranking .rankBack{width: 100%;height:100%;background: #000;opacity:1.0}
    .vbull12 .ranking .rankBack .bg{position: absolute;width: 100%;height:100%;background: #000;opacity:1.0}
    .vbull12 .ranking .rankText{width: 100%;position: absolute;top:3%;left: 0;}
    .vbull12 .ranking .rankText .title{width: 100%;}
    .vbull12 .ranking .rankText .time{text-align: center;margin-top: 24px;margin-bottom: 20px;}
    .vbull12 .ranking .rankText .time a{border-radius: 5vw;border: 2px solid #7f7e85;color:#FBF0D4;padding: 1vw 4vw;width: 400px;font-size: 6vw;}
    .vbull12 .ranking .rankText .scoresItem{width:139vw;margin:0 auto; margin-top: 2px;height:14.5vw;line-height:15vw;font-size:7vw;position: relative;background-color: #1b160c; }
    .vbull12 .ranking .rankText .scoresItemWhite{color:#fff; }
    .vbull12 .ranking .rankText .scoresItemWhite a{color:#fff; }
    .vbull12 .ranking .rankText .scoresItemYellow{color:#f7d92b;}
    .vbull12 .ranking .rankText .scoresItemYellow a{color:#f7d92b;}
    .vbull12 .ranking .rankText .scoresItem .name{left: 10%;width: 42%;height: 15vw;overflow: hidden;word-break:break-all;position: absolute;top:0;}
    .vbull12 .ranking .rankText .scoresItem .currentScores{left: 52%;text-align: left;position: absolute;right: 0;top:0;}
    .vbull12 .ranking .rankText .scoresItem .consumeScores{left: 85%;text-align: left;position: absolute;right: 0;top:0;}
    .vbull12 .ranking  .button{width:100%;position: absolute;bottom:3%; }
    .vbull12 .ranking  .button img{width:33%;}

    .bull13 .ranking{position: absolute;width: 200%;height:200%;top:0;left:0;z-index: 110;}
    .bull13 .ranking .roundEndShow{display: none;}
    .bull13 .ranking .rankBack{width: 100%;height:100%;background: #000;opacity:1.0}
    .bull13 .ranking .rankBack .bg{position: absolute;width: 100%;height:100%;background: #000;opacity:1.0}
    .bull13 .ranking .rankText{width: 100%;position: absolute;top:3%;left: 0;}
    .bull13 .ranking .rankText .title{width: 100%;}
    .bull13 .ranking .rankText .time{text-align: center;margin-top: 24px;margin-bottom: 20px;}
    .bull13 .ranking .rankText .time a{border-radius: 1.5vw;border: 2px solid #7f7e85;color:#FBF0D4;padding: 1vw 4vw;width: 400px;font-size: 6vw;}
    .bull13 .ranking .rankText .scoresItem{width:178.1vw;margin:0 auto;height:13.9vw;line-height:13.9vw;font-size:7vw;position: relative;border-top: 1px solid #CB9564;}
    .bull13 .ranking .rankText .scoresItemTitle{color:#633201;background: #DBB272;width:89.2%;}
    .bull13 .ranking .rankText .scoresItemWhite{color:#5F4838; }
    .bull13 .ranking .rankText .scoresItemWhite a{color:#5F4838; }
    .bull13 .ranking .rankText .scoresItemYellow{color:#E07C21;}
    .bull13 .ranking .rankText .scoresItemYellow a{color:#E07C21;}
    .bull13 .ranking .rankText .room_own{width: 91%;margin: 0 auto;text-align: center;height: 64px;line-height: 64px;position: relative;background:url("<?php echo $image_url;?>files/images/common/room_own.png")no-repeat center center; background-size:80% 70%;}
    .bull13 .ranking .rankText .room_own p{font-size: 20px;position: absolute;width: 100%;left: 0;top: 0;letter-spacing: 4px;font-size: 18px;color: #5F3811;font-weight: 600;margin-top: -6px;}
    .bull13 .ranking .rankText .scoresItem .id{left: 10%;width: 25%;height: 15vw;overflow: hidden;word-break:break-all;position: absolute;top:0;}
    .bull13 .ranking .rankText .scoresItem .user_code{left: 1%;width: 25%;text-align: center;overflow: hidden;word-break:break-all;position: absolute;top:0;}
    .bull13 .ranking .rankText .scoresItem .name{left: 32%;width: 42%;height: 15vw;overflow: hidden;word-break:break-all;position: absolute;top:0;text-overflow:ellipsis;white-space:nowrap;text-align: center;}
    .bull13 .ranking .rankText .scoresItem .currentScores{width:18%;text-align: left;position: absolute;right: 0;top:0;}
    .bull13 .ranking  .button{width:100%;position: absolute;bottom:3%; }
    .bull13 .ranking  .button img{width:33%;}

    .lbull .ranking{position: absolute;width: 200%;height:200%;top:0;left:0;z-index: 110;}
    .lbull .ranking .roundEndShow{display: none;}
    .lbull .ranking .rankBack{width: 100%;height:100%;background: #000;opacity:1.0}
    .lbull .ranking .rankBack .bg{position: absolute;width: 100%;height:100%;background: #000;opacity:1.0}
    .lbull .ranking .rankText{width: 100%;position: absolute;top:3%;left: 0;}
    .lbull .ranking .rankText .title{width: 100%;}
    .lbull .ranking .rankText .time{text-align: center;margin-top: 24px;margin-bottom: 20px;}
    .lbull .ranking .rankText .time a{border-radius: 1.5vw;border: 2px solid #7f7e85;color:#FBF0D4;padding: 1vw 4vw;width: 400px;font-size: 6vw;}
    .lbull .ranking .rankText .scoresItem{width:156vw;margin:0 auto;height:15vw;line-height:15vw;font-size:7vw;position: relative;border-top: 1px solid #CB9564;}
    .lbull .ranking .rankText .scoresItemTitle{color:#633201;background: #DBB272;width:78.4%;}
    .lbull .ranking .rankText .scoresItemWhite{color:#5F4838; }
    .lbull .ranking .rankText .scoresItemWhite a{color:#5F4838; }
    .lbull .ranking .rankText .scoresItemYellow{color:#E07C21;}
    .lbull .ranking .rankText .scoresItemYellow a{color:#E07C21;}
    .lbull .ranking .rankText .room_own{width: 91%;margin: 0 auto;text-align: center;height: 80px;line-height: 80px;position: relative;background:url("<?php echo $image_url;?>files/images/common/room_own.png")no-repeat center center; background-size:80% 70%;}
    .lbull .ranking .rankText .room_own p{font-size: 20px;position: absolute;width: 100%;left: 0;top: 0;letter-spacing: 4px;font-size: 18px;color: #5F3811;font-weight: 600;margin-top: -6px;}
    .lbull .ranking .rankText .scoresItem .id{left: 10%;width: 25%;height: 15vw;overflow: hidden;word-break:break-all;position: absolute;top:0;}
    .lbull .ranking .rankText .scoresItem .user_code{left: 2%;width: 25%;text-align: center;overflow: hidden;word-break:break-all;position: absolute;top:0;}
    .lbull .ranking .rankText .scoresItem .name{left: 29%;width: 42%;height: 15vw;overflow: hidden;word-break:break-all;position: absolute;top:0;text-overflow:ellipsis;white-space:nowrap;text-align: center;}
    .lbull .ranking .rankText .scoresItem .currentScores{width:18%;text-align: left;position: absolute;right: 0;top:0;}
    .lbull .ranking  .button{width:100%;position: absolute;bottom:3%; }
    .lbull .ranking  .button img{width:33%;}

    .sangong .ranking{position: absolute;width: 800px;height:1200px;top:0;left:0;z-index: 110;}
    .sangong .ranking .roundEndShow{}
    .sangong .ranking .rankBack{width: 100%;height:100%;background: #000;opacity:1.0}
    .sangong .ranking .rankBack .bg{position: absolute;width: 100%;height:100%;background: #000;opacity:1.0}
    .sangong .ranking .rankText{width: 100%;position: absolute;top:10%;left: 0;}
    .sangong .ranking .rankText .title{width: 100%;}
    .sangong .ranking .rankText .time{text-align: center;margin-top: 24px;margin-bottom: 20px;}
    .sangong .ranking .rankText .time a{border-radius: 8px;border: 2px solid #7f7e85;color:#FBF0D4;padding: 4px 16px;width: 400px;font-size: 24px;}
    .sangong .ranking .rankText .scoresItem{width:538px;margin:0 auto;height:82px;line-height:82px;font-size:32px;position: relative;border-top: 1px solid #CB9564;}
    .sangong .ranking .rankText .scoresItemTitle{color:#633201;background: #DBB272;width:67.2%;}
    .sangong .ranking .rankText .scoresItemWhite{color:#5F4838; }
    .sangong .ranking .rankText .scoresItemWhite a{color:#5F4838; }
    .sangong .ranking .rankText .scoresItemYellow{color:#E07C21;}
    .sangong .ranking .rankText .scoresItemYellow a{color:#E07C21;}
    .sangong .ranking .rankText .room_own{width: 67%;margin: 0 auto;text-align: center;height: 60px;line-height: 60px;position: relative;background:url("<?php echo $image_url;?>files/images/common/room_own.png")no-repeat center center; background-size:86% 65%;}
    .sangong .ranking .rankText .room_own p{font-size: 20px;position: absolute;width: 100%;left: 0;top: 0;letter-spacing: 4px;font-size: 18px;color: #5F3811;font-weight: 600;margin-top: -4px;}
    .sangong .ranking .rankText .scoresItem .user_code{left: 8%;width: 25%;text-align: center;overflow: hidden;word-break:break-all;position: absolute;top:0;}
    .sangong .ranking .rankText .scoresItem .name{left: 34%;width: 36%;height: 90px;overflow: hidden;word-break:break-all;position: absolute;top:0;text-overflow:ellipsis;white-space:nowrap;text-align: center;}
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
    .sangong9 .ranking .rankText .time a{border-radius: 8px;border: 2px solid #7f7e85;color:#FBF0D4;padding: 4px 16px;width: 400px;font-size: 24px;}
    .sangong9 .ranking .rankText .scoresItem{width:638px;margin:0 auto;height:66px;line-height:66px;font-size:32px;position: relative;border-top: 1px solid #CB9564;}
    .sangong9 .ranking .rankText .scoresItemTitle{color:#633201;background: #DBB272;width:79.6%;}
    .sangong9 .ranking .rankText .scoresItemWhite{color:#5F4838; }
    .sangong9 .ranking .rankText .scoresItemWhite a{color:#5F4838; }
    .sangong9 .ranking .rankText .scoresItemYellow{color:#E07C21;}
    .sangong9 .ranking .rankText .scoresItemYellow a{color:#E07C21;}
    .sangong9 .ranking .rankText .room_own{width: 91%;margin: 0 auto;text-align: center;height: 80px;line-height: 80px;position: relative;background:url("<?php echo $image_url;?>files/images/common/room_own.png")no-repeat center center; background-size:80% 70%;}
    .sangong9 .ranking .rankText .room_own p{font-size: 20px;position: absolute;width: 100%;left: 0;top: 0;letter-spacing: 4px;font-size: 18px;color: #5F3811;font-weight: 600;margin-top: -6px;}
    .sangong9 .ranking .rankText .scoresItem .user_code{left: 5%;width: 25%;text-align: center;overflow: hidden;word-break:break-all;position: absolute;top:0;}
    .sangong9 .ranking .rankText .scoresItem .name{left: 28%;width: 44%;height: 72px;overflow: hidden;word-break:break-all;position: absolute;top:0;text-overflow:ellipsis;white-space:nowrap;text-align: center;}
    .sangong9 .ranking .rankText .scoresItem .currentScores{width:25%;text-align: left;position: absolute;right: 0;top:0;}
    .sangong9 .ranking  .button{width:100%;position: absolute;bottom:8%; }
    .sangong9 .ranking  .button img{width:33%;}


    .sangong12 .ranking{position: absolute;width: 200%;height:200%;top:0;left:0;z-index: 110;}
    .sangong12 .ranking .roundEndShow{display: none;}
    .sangong12 .ranking .rankBack{width: 100%;height:100%;background: #000;opacity:1.0}
    .sangong12 .ranking .rankBack .bg{position: absolute;width: 100%;height:100%;background: #000;opacity:1.0}
    .sangong12 .ranking .rankText{width: 100%;position: absolute;top:3%;left: 0;}
    .sangong12 .ranking .rankText .title{width: 100%;}
    .sangong12 .ranking .rankText .time{text-align: center;margin-top: 24px;margin-bottom: 20px;}
    .sangong12 .ranking .rankText .time a{border-radius: 5vw;border: 2px solid #7f7e85;color:#FBF0D4;padding: 1vw 4vw;width: 400px;font-size: 6vw;}
    .sangong12 .ranking .rankText .scoresItem{width:139vw;margin:0 auto; margin-top: 2px;height:14.5vw;line-height:15vw;font-size:7vw;position: relative;background-color: #1b160c; }
    .sangong12 .ranking .rankText .scoresItemWhite{color:#fff; }
    .sangong12 .ranking .rankText .scoresItemWhite a{color:#fff; }
    .sangong12 .ranking .rankText .scoresItemYellow{color:#f7d92b;}
    .sangong12 .ranking .rankText .scoresItemYellow a{color:#f7d92b;}
    .sangong12 .ranking .rankText .scoresItem .id{left: 10%;width: 25%;height: 15vw;overflow: hidden;word-break:break-all;position: absolute;top:0;}
    .sangong12 .ranking .rankText .scoresItem .name{left: 36%;width: 42%;height: 15vw;overflow: hidden;word-break:break-all;position: absolute;top:0;}
    .sangong12 .ranking .rankText .scoresItem .currentScores{width:18%;text-align: left;position: absolute;right: 0;top:0;}
    .sangong12 .ranking  .button{width:100%;position: absolute;bottom:3%; }
    .sangong12 .ranking  .button img{width:33%;}

    .darkPoint .ranking{position: absolute;width: 200%;height:200%;top:0;left:0;z-index: 110;}
    .darkPoint .ranking .roundEndShow{display: none;}
    .darkPoint .ranking .rankBack{width: 100%;height:100%;background: #000;opacity:1.0}
    .darkPoint .ranking .rankBack .bg{position: absolute;width: 100%;height:100%;background: #000;opacity:1.0}
    .darkPoint .ranking .rankText{width: 100%;position: absolute;top:3%;left: 0;}
    .darkPoint .ranking .rankText .title{width: 100%;}
    .darkPoint .ranking .rankText .time{text-align: center;margin-top: 24px;margin-bottom: 20px;}
    .darkPoint .ranking .rankText .time a{border-radius: 1.5vw;border: 2px solid #7f7e85;color:#FBF0D4;padding: 1vw 4vw;width: 400px;font-size: 6vw;}
    .darkPoint .ranking .rankText .scoresItem{width:156vw;margin:0 auto;height:15vw;line-height:15vw;font-size:7vw;position: relative;border-top: 1px solid #CB9564;}
    .darkPoint .ranking .rankText .scoresItemTitle{color:#633201;background: #DBB272;width:78.4%;}
    .darkPoint .ranking .rankText .scoresItemWhite{color:#5F4838; }
    .darkPoint .ranking .rankText .scoresItemWhite a{color:#5F4838; }
    .darkPoint .ranking .rankText .scoresItemYellow{color:#E07C21;}
    .darkPoint .ranking .rankText .scoresItemYellow a{color:#E07C21;}
    .darkPoint .ranking .rankText .room_own{width: 91%;margin: 0 auto;text-align: center;height: 80px;line-height: 80px;position: relative;background:url("<?php echo $image_url;?>files/images/common/room_own.png")no-repeat center center; background-size:80% 70%;}
    .darkPoint .ranking .rankText .room_own p{font-size: 20px;position: absolute;width: 100%;left: 0;top: 0;letter-spacing: 4px;font-size: 18px;color: #5F3811;font-weight: 600;margin-top: -6px;}
    .darkPoint .ranking .rankText .scoresItem .id{left: 10%;width: 25%;height: 15vw;overflow: hidden;word-break:break-all;position: absolute;top:0;}
    .darkPoint .ranking .rankText .scoresItem .user_code{left: 2%;width: 25%;text-align: center;overflow: hidden;word-break:break-all;position: absolute;top:0;}
    .darkPoint .ranking .rankText .scoresItem .name{left: 29%;width: 42%;height: 15vw;overflow: hidden;word-break:break-all;position: absolute;top:0;text-overflow:ellipsis;white-space:nowrap;text-align: center;}
    .darkPoint .ranking .rankText .scoresItem .currentScores{width:18%;text-align: left;position: absolute;right: 0;top:0;}
    .darkPoint .ranking  .button{width:100%;position: absolute;bottom:3%; }
    .darkPoint .ranking  .button img{width:33%;}

    /* 返回按钮和复制按钮 */
    .top_btn {position: fixed;top: 0;left: 0;width: 100vw;height: 45px;padding: 0 10px;box-sizing: border-box;z-index:200;}
    .top_btn .goback{width: 45px;height: 45px;float: left;background: url("<?php echo $image_url;?>files/images/common/back.png") no-repeat;background-size: cover;}

</style>

<script type="text/javascript" src="<?php echo $image_url;?>files/js/jquery-1.9.1.min.js"></script>

</head>

<body>

<div id="endCreateRoom" class="end" style="position: fixed;width: 100%;height:100%;top:0;left:0;z-index: 120;display: none;overflow: hidden;">
    <img src="" style="width: 100%;height:100%;position: absolute;top:0;left: 0;" id="end"  usemap="#planetmap1" />
</div>
<div class="top_btn">
    <div class="goback" onClick="window.history.go(-1);"></div>
</div>
<div class="rankPanel">

    <?php if($board->game_type == 1 || $board->game_type == 110 || $board->game_type == 111){?>
        <div class="flower">
            <div class="ranking hideRanking" id="ranking" style="z-index: 1">
                <div class="rankBack">
                    <img src="<?php echo $image_url;?>files/images/flower/rank_bg.png" style="position: absolute;top: 0;left: 0;width: 100%">
                </div>
                <div class="rankText" style="position: absolute;top: 4%;">
                    <img src="<?php echo $image_url;?>files/images/flower/rank_frame.png" style="position: absolute;top: 0%;left: 20px;width: 760px;">
                    <div class="time"  style="position: absolute;top: 204px;width: 100%;">
                        <a style="border-color: #7f7e85;background-color: #7f7e85;font-size: 24px;">房间号:<?php echo $room_number;?>&nbsp&nbsp&nbsp&nbsp<?php echo $board->over_time;?>&nbsp&nbsp&nbsp&nbsp<?php echo $board->total_round;?>局</a>
                    </div>
                    <div style="height: 285px;"></div>
                    <div class="room_own">
                        <p>知己娱乐房主：<?php echo $room_own;?></p>
                    </div>
                    <div class="scoresItem scoresItemTitle">
                        <div class="user_code">ID</div>
                        <div class="name">名称</div>
                        <div class="currentScores">分数</div>
                    </div>
                    <?php foreach ($board->balance_board as $player){?>
                        <?php if($player['score'] > 0){?>
                            <div class="scoresItem scoresItemYellow">
                                <?php if($player['big_winner']){?>
                                    <img src="<?php echo $image_url;?>files/images/common/rank_bigwinner.png" style="top: 0; margin-top: -6px; left: 0;position: absolute;height: 100%">
                                <?php }?>
                                <div class="user_code"><?php echo $player['account_id']?></div>
                                <div class="name"><?php echo $player['name']?></div>
                                <div class="currentScores">+<?php echo $player['score']?></div>
                            </div>
                        <?php }else{?>
                            <div class="scoresItem scoresItemWhite">
                                <div class="user_code"><?php echo $player['account_id']?></div>
                                <div class="name"><?php echo $player['name']?></div>
                                <div class="currentScores"><?php echo $player['score']?></div>
                            </div>
                        <?php }?>
                    <?php }?>
                </div>
                <div class="button roundEndShow" >
                    <img src="<?php echo $image_url;?>files/images/common/rank_save.png" style="float: left;margin-left: 34%;" />
                </div>
            </div>
        </div>
    <?php }?>

    <?php if($board->game_type == 92){?>
        <div class="vflower6">
            <div class="ranking hideRanking" id="ranking" style="z-index: 1">
                <div class="rankBack">
                    <img src="<?php echo $image_url;?>files/images/flower/rank_bg.png" style="position: absolute;top: 0;left: 0;width: 100%">
                </div>
                <div class="rankText" style="position: absolute;top: 4%;">
                    <img src="<?php echo $image_url;?>files/images/flower/rank_frame.png" style="position: absolute;top: 0%;left: 20px;width: 760px;">
                    <div class="time"  style="position: absolute;top: 182px;width: 100%;">
                        <a style="border-color: #ce6eff;background-color: #382b5e;font-size: 24px;">房间号:<?php echo $room_number;?>&nbsp&nbsp&nbsp&nbsp<?php echo $board->over_time;?>&nbsp&nbsp&nbsp&nbsp<?php echo $board->total_round;?>局</a>
                    </div>
                    <div style="height: 254px;"></div>
                    <div class="scoresItem scoresItemYellow">
                        <div class="name">昵称</div>
                        <div class="currentScores">得分</div>
                        <div class="consumeScores">消耗</div>
                    </div>
                    <?php foreach ($board->balance_board as $player){?>
                        <?php if($player['score'] > 0){?>
                            <div class="scoresItem scoresItemYellow">
                                <?php if($player['big_winner']){?>
                                    <img src="<?php echo $image_url;?>files/images/common/rank_bigwinner.png" style="top: 0; margin-top: -10px; left: 4px;height: 100%">
                                <?php }?>
                                <div class="name"><?php echo $player['name']?></div>
                                <div class="currentScores">+<?php echo $player['score']?></div>
                                <div class="consumeScores"><?php echo $player['consume']?:''; ?></div>
                            </div>
                        <?php }else{?>
                            <div class="scoresItem scoresItemWhite">
                                <div class="name"><?php echo $player['name']?></div>
                                <div class="currentScores"><?php echo $player['score']?></div>
                                <div class="consumeScores"></div>
                            </div>
                        <?php }?>
                    <?php }?>
                </div>
                <div class="button roundEndShow" >
                    <img src="<?php echo $image_url;?>files/images/common/rank_save.png" style="float: left;margin-left: 34%;" />
                </div>
            </div>
        </div>
    <?php }?>

    <?php if($board->game_type == 95){?>
        <div class="vflower10">
            <div class="ranking hideRanking" id="ranking" style="z-index: 1">
                <div class="rankBack">
                    <img src="<?php echo $image_url;?>files/images/flower/rank_bg.png" style="position: absolute;top: 0;left: 0;width: 100%">
                </div>
                <div class="rankText" style="position: absolute;top: 4%;">
                    <img src="<?php echo $image_url;?>files/images/flower/rank_frame.png" style="position: absolute;top: 0%;left: 20px;width: 760px;">
                    <div class="time"  style="position: absolute;top: 182px;width: 100%;">
                        <a style="border-color: #ce6eff;background-color: #382b5e;font-size: 24px;">房间号:<?php echo $room_number;?>&nbsp&nbsp&nbsp&nbsp<?php echo $board->over_time;?>&nbsp&nbsp&nbsp&nbsp<?php echo $board->total_round;?>局</a>
                    </div>
                    <div style="height: 254px;"></div>
                    <div class="scoresItem scoresItemYellow">
                        <div class="name">昵称</div>
                        <div class="currentScores">得分</div>
                        <div class="consumeScores">消耗</div>
                    </div>
                    <?php foreach ($board->balance_board as $player){?>
                        <?php if($player['score'] > 0){?>
                            <div class="scoresItem scoresItemYellow">
                                <?php if($player['big_winner']){?>
                                    <img src="<?php echo $image_url;?>files/images/common/rank_bigwinner.png" style="top: 0; margin-top: -10px; left: 4px;height: 100%">
                                <?php }?>
                                <div class="name"><?php echo $player['name']?></div>
                                <div class="currentScores">+<?php echo $player['score']?></div>
                                <div class="consumeScores"><?php echo $player['consume']?:''; ?></div>
                            </div>
                        <?php }else{?>
                            <div class="scoresItem scoresItemWhite">
                                <div class="name"><?php echo $player['name']?></div>
                                <div class="currentScores"><?php echo $player['score']?></div>
                                <div class="consumeScores"></div>
                            </div>
                        <?php }?>
                    <?php }?>
                </div>
                <div class="button roundEndShow" >
                    <img src="<?php echo $image_url;?>files/images/common/rank_save.png" style="float: left;margin-left: 34%;" />
                </div>
            </div>
        </div>
    <?php }?>

    <?php if($board->game_type == 93){?>
        <div class="vbull6">
            <div class="ranking hideRanking" id="ranking" style="z-index: 1">
                <div class="rankBack">
                    <img src="<?php echo $image_url;?>files/images/tbull/rank_bg.png" style="position: absolute;top: 0;left: 0;width: 100%">
                </div>
                <div class="rankText" style="position: absolute;top: 4%;">
                    <img src="<?php echo $image_url;?>files/images/tbull/ranking.png" style="position: absolute;top: 0%;left: 120px;width: 560px;">
                    <div class="time" style="position: absolute;top: 192px;width: 100%;">
                        <a style="border-color: #7f7e85;background-color: #7f7e85;font-size: 24px;">房间号:<?php echo $room_number;?>&nbsp&nbsp&nbsp&nbsp<?php echo $board->over_time;?>&nbsp&nbsp&nbsp&nbsp<?php echo $board->total_round;?>局</a>
                    </div>
                    <div style="height: 268px;"></div>
                    <div class="scoresItem scoresItemWhite">
                        <div class="name">昵称</div>
                        <div class="currentScores">得分</div>
                        <div class="consumeScores">消耗</div>
                    </div>
                    <?php foreach ($board->balance_board as $player){?>
                        <?php if($player['score'] > 0){?>
                            <div class="scoresItem scoresItemYellow">
                                <?php if($player['big_winner']){?>
                                    <img src="<?php echo $image_url;?>files/images/common/rank_bigwinner.png" style="top: 0; margin-top: -10px; left: 4px;height: 100%">
                                <?php }?>
                                <div class="name"><?php echo $player['name']?></div>
                                <div class="currentScores">+<?php echo $player['score']?></div>
                                <div class="consumeScores"><?php echo $player['consume']?: ''; ?></div>
                            </div>
                        <?php }else{?>
                            <div class="scoresItem scoresItemWhite">
                                <div class="name"><?php echo $player['name']?></div>
                                <div class="currentScores"><?php echo $player['score']?></div>
                                <div class="consumeScores"></div>
                            </div>
                        <?php }?>
                    <?php }?>
                </div>
                <div class="button roundEndShow" >
                    <img src="<?php echo $image_url;?>files/images/common/rank_save.png" style="float: left;margin-left: 34%;" />
                </div>
            </div>
        </div>
    <?php }?>

    <?php if($board->game_type == 5){?>
        <div class="bull">
            <div class="ranking hideRanking" id="ranking" style="z-index: 1">
                <div class="rankBack">
                    <img src="<?php echo $image_url;?>files/images/tbull/rank_bg.png" style="position: absolute;top: 0;left: 0;width: 100%;height:100%;">
                </div>
                <div class="rankText" style="position: absolute;top: 4%;">
                    <img src="<?php echo $image_url;?>files/images/tbull/ranking.png" style="position: absolute;top: 0%;left: 66px;width: 668px;">
                    <div class="time" style="position: absolute;top: 174px;width: 100%;">
                        <a style="border-color: #7f7e85;background-color: #7f7e85;font-size: 24px;">房间号:<?php echo $room_number;?>&nbsp&nbsp&nbsp&nbsp<?php echo $board->over_time;?>&nbsp&nbsp&nbsp&nbsp<?php echo $board->total_round;?>局</a>
                    </div>
                    <div style="height: 250px;"></div>
                    <div class="room_own">
                        <p>知己娱乐房主：<?php echo $room_own;?></p>
                    </div>
                    <div class="scoresItem scoresItemTitle">
                        <div class="user_code">ID</div>
                        <div class="name">名称</div>
                        <div class="currentScores">分数</div>
                    </div>
                    <?php foreach ($board->balance_board as $player){?>
                        <?php if($player['score'] > 0){?>
                            <div class="scoresItem scoresItemYellow">
                                <?php if($player['big_winner']){?>
                                    <img src="<?php echo $image_url;?>files/images/common/rank_bigwinner.png" style="top: 0; margin-top: -10px; left: -10px;height: 100%;position: absolute;">
                                <?php }?>
                                <div class="user_code"><?php echo $player['account_id']?></div>
                                <div class="name"><?php echo $player['name']?></div>
                                <div class="currentScores">+<?php echo $player['score']?></div>
                            </div>
                        <?php }else{?>
                            <div class="scoresItem scoresItemWhite">
                                <div class="user_code"><?php echo $player['account_id']?></div>
                                <div class="name"><?php echo $player['name']?></div>
                                <div class="currentScores"><?php echo $player['score']?></div>
                            </div>
                        <?php }?>
                    <?php }?>
                </div>
                <div class="button roundEndShow" >
                    <img src="<?php echo $image_url;?>files/images/common/rank_save.png" style="float: left;margin-left: 34%;" />
                </div>
            </div>
        </div>
    <?php }?>

    <?php if($board->game_type == 9){?>
        <div class="bull9">
            <div class="ranking hideRanking" id="ranking" style="z-index: 1">
                <div class="rankBack">
                    <img src="<?php echo $image_url;?>files/images/tbull/rank_bg.png" style="position: absolute;top: 0;left: 0;width: 100%;height:100%;">
                </div>
                <div class="rankText" style="position: absolute;top: 4%;">
                    <img src="<?php echo $image_url;?>files/images/tbull/ranking.png" style="position: absolute;top: 0%;left: 57px;width: 686px;">
                    <div class="time" style="position: absolute;top: 178px;width: 100%;">
                        <a style="border-color: #7f7e85;background-color: #7f7e85;font-size: 24px;">房间号:<?php echo $room_number;?>&nbsp&nbsp&nbsp&nbsp<?php echo $board->over_time;?>&nbsp&nbsp&nbsp&nbsp<?php echo $board->total_round;?>局</a>
                    </div>
                    <div style="height: 256px;"></div>
                    <div class="room_own">
                        <p>知己娱乐房主：<?php echo $room_own;?></p>
                    </div>
                    <div class="scoresItem scoresItemTitle">
                        <div class="user_code">ID</div>
                        <div class="name">名称</div>
                        <div class="currentScores">分数</div>
                    </div>
                    <?php foreach ($board->balance_board as $player){?>
                        <?php if($player['score'] > 0){?>
                            <div class="scoresItem scoresItemYellow">
                                <?php if($player['big_winner']){?>
                                    <img src="<?php echo $image_url;?>files/images/common/rank_bigwinner.png" style="top: 0; margin-top: -6px; left: 4px;height: 100%">
                                <?php }?>
                                <div class="user_code"><?php echo $player['account_id']?></div>
                                <div class="name"><?php echo $player['name']?></div>
                                <div class="currentScores">+<?php echo $player['score']?></div>
                            </div>
                        <?php }else{?>
                            <div class="scoresItem scoresItemWhite">
                                <div class="user_code"><?php echo $player['account_id']?></div>
                                <div class="name"><?php echo $player['name']?></div>
                                <div class="currentScores"><?php echo $player['score']?></div>
                            </div>
                        <?php }?>
                    <?php }?>
                </div>
                <div class="button roundEndShow" >
                    <img src="<?php echo $image_url;?>files/images/common/rank_save.png" style="float: left;margin-left: 34%;" />
                </div>
            </div>
        </div>
    <?php }?>

    <?php if($board->game_type == 91){?>
        <div class="vbull9">
            <div class="ranking hideRanking" id="ranking" style="z-index: 1">
                <div class="rankBack">
                    <img src="<?php echo $image_url;?>files/images/tbull/rank_bg.png" style="position: absolute;top: 0;left: 0;width: 100%">
                </div>
                <div class="rankText" style="position: absolute;top: 4%;">
                    <img src="<?php echo $image_url;?>files/images/tbull/ranking.png" style="position: absolute;top: 0;left: 25vw;width: 150vw;">
                    <div class="time" style="position: absolute;top: 44vw;width: 100%;">
                        <a style="border-color: #7f7e85;background-color: #7f7e85;font-size: 6vw;">房间号:<?php echo $room_number;?>&nbsp&nbsp&nbsp&nbsp<?php echo $board->over_time;?>&nbsp&nbsp&nbsp&nbsp<?php echo $board->total_round;?>局</a>
                    </div>
                    <div style="height: 268px;"></div>
                    <div class="scoresItem scoresItemYellow">
                        <div class="name">昵称</div>
                        <div class="currentScores">得分</div>
                        <div class="consumeScores">消耗</div>
                    </div>
                    <?php foreach ($board->balance_board as $player){?>
                        <?php if($player['score'] > 0){?>
                            <div class="scoresItem scoresItemYellow">
                                <?php if($player['big_winner']){?>
                                    <img src="<?php echo $image_url;?>files/images/common/rank_bigwinner.png" style="top: 0; margin-top: -1.5vw; left: 2vw;height: 100%">
                                <?php }?>
                                <div class="name"><?php echo $player['name']?></div>
                                <div class="currentScores">+<?php echo $player['score']?></div>
                                <div class="consumeScores"><?php echo (isset($player['consume']) && $player['consume'] ? $player['consume']: ''); ?></div>
                            </div>
                        <?php }else{?>
                            <div class="scoresItem scoresItemWhite">
                                <div class="name"><?php echo $player['name']?></div>
                                <div class="currentScores"><?php echo $player['score']?></div>
                                <div class="consumeScores"></div>
                            </div>
                        <?php }?>
                    <?php }?>
                </div>
                <div class="button roundEndShow" >
                    <img src="<?php echo $image_url;?>files/images/common/rank_save.png" style="float: left;margin-left: 34%;" />
                </div>
            </div>
        </div>
    <?php }?>

    <?php if($board->game_type == 12){?>
        <div class="bull12">
            <div class="ranking hideRanking" id="ranking" style="z-index: 1">
                <div class="rankBack">
                    <img src="<?php echo $image_url;?>files/images/tbull/rank_bg.png" style="position: absolute;top: 0;left: 0;width: 100%">
                </div>
                <div class="rankText" style="position: absolute;top: 4%;">
                    <img src="<?php echo $image_url;?>files/images/tbull/ranking.png" style="position: absolute;top: 0;left: 7vw;width: 186vw;">
                    <div class="time" style="position: absolute;top: 48vw;width: 100%;">
                        <a style="border-color: #7f7e85;background-color: #7f7e85;font-size: 6vw;">房间号:<?php echo $room_number;?>&nbsp&nbsp&nbsp&nbsp<?php echo $board->over_time;?>&nbsp&nbsp&nbsp&nbsp<?php echo $board->total_round;?>局</a>
                    </div>
                    <div style="height: 69.7vw;"></div>
                    <div class="room_own">
                        <p>知己娱乐房主：<?php echo $room_own;?></p>
                    </div>
                    <div class="scoresItem scoresItemTitle">
                        <div class="user_code">ID</div>
                        <div class="name">名称</div>
                        <div class="currentScores">分数</div>
                    </div>
                    <?php foreach ($board->balance_board as $player){?>
                        <?php if($player['score'] > 0){?>
                            <div class="scoresItem scoresItemYellow">
                                <?php if($player['big_winner']){?>
                                    <img src="<?php echo $image_url;?>files/images/common/rank_bigwinner.png" style="top: 0; margin-top: -1.5vw; left: 2vw;height: 100%">
                                <?php }?>
                                <div class="user_code"><?php echo $player['account_id']?></div>
                                <div class="name"><?php echo $player['name']?></div>
                                <div class="currentScores">+<?php echo $player['score']?></div>
                            </div>
                        <?php }else{?>
                            <div class="scoresItem scoresItemWhite">
                                <div class="user_code"><?php echo $player['account_id']?></div>
                                <div class="name"><?php echo $player['name']?></div>
                                <div class="currentScores"><?php echo $player['score']?></div>
                            </div>
                        <?php }?>
                    <?php }?>
                </div>
                <div class="button roundEndShow" >
                    <img src="<?php echo $image_url;?>files/images/common/rank_save.png" style="float: left;margin-left: 34%;" />
                </div>
            </div>
        </div>
    <?php }?>

    <?php if($board->game_type == 13){?>
        <div class="bull13">
            <div class="ranking hideRanking" id="ranking" style="z-index: 1">
                <div class="rankBack">
                    <img src="<?php echo $image_url;?>files/images/tbull/rank_bg.png" style="position: absolute;top: 0;left: 0;width: 100%">
                </div>
                <div class="rankText" style="position: absolute;top: 4%;">
                    <img src="<?php echo $image_url;?>files/images/tbull/ranking.png" style="position: absolute;top: 0;left: 7vw;width: 186vw;">
                    <div class="time" style="position: absolute;top: 50vw;width: 100%;">
                        <a style="border-color: #7f7e85;background-color: #7f7e85;font-size: 6vw;">房间号:<?php echo $room_number;?>&nbsp&nbsp&nbsp&nbsp<?php echo $board->over_time;?>&nbsp&nbsp&nbsp&nbsp<?php echo $board->total_round;?>局</a>
                    </div>
                    <div style="height: 70.1vw;"></div>
                    <div class="room_own">
                        <p>知己娱乐房主：<?php echo $room_own;?></p>
                    </div>
                    <div class="scoresItem scoresItemTitle">
                        <div class="user_code">ID</div>
                        <div class="name">名称</div>
                        <div class="currentScores">分数</div>
                    </div>
                    <?php foreach ($board->balance_board as $player){?>
                        <?php if($player['score'] > 0){?>
                            <div class="scoresItem scoresItemYellow">
                                <?php if($player['big_winner']){?>
                                    <img src="<?php echo $image_url;?>files/images/common/rank_bigwinner.png" style="top: 0; margin-top: -1.5vw; left: 2vw;height: 100%">
                                <?php }?>
                                <div class="user_code"><?php echo $player['account_id']?></div>
                                <div class="name"><?php echo $player['name']?></div>
                                <div class="currentScores">+<?php echo $player['score']?></div>
                            </div>
                        <?php }else{?>
                            <div class="scoresItem scoresItemWhite">
                                <div class="user_code"><?php echo $player['account_id']?></div>
                                <div class="name"><?php echo $player['name']?></div>
                                <div class="currentScores"><?php echo $player['score']?></div>
                            </div>
                        <?php }?>
                    <?php }?>
                </div>
                <div class="button roundEndShow" >
                    <img src="<?php echo $image_url;?>files/images/common/rank_save.png" style="float: left;margin-left: 34%;" />
                </div>
            </div>
        </div>
    <?php }?>

    <?php if($board->game_type == 94){?>
        <div class="vbull12">
            <div class="ranking hideRanking" id="ranking" style="z-index: 1">
                <div class="rankBack">
                    <img src="<?php echo $image_url;?>files/images/tbull/rank_bg.png" style="position: absolute;top: 0;left: 0;width: 100%">
                </div>
                <div class="rankText" style="position: absolute;top: 4%;">
                    <img src="<?php echo $image_url;?>files/images/tbull/ranking.png" style="position: absolute;top: 0;left: 25vw;width: 150vw;">
                    <div class="time" style="position: absolute;top: 44vw;width: 100%;">
                        <a style="border-color: #7f7e85;background-color: #7f7e85;font-size: 6vw;">房间号:<?php echo $room_number;?>&nbsp&nbsp&nbsp&nbsp<?php echo $board->over_time;?>&nbsp&nbsp&nbsp&nbsp<?php echo $board->total_round;?>局</a>
                    </div>
                    <div style="height: 268px;"></div>
                    <div class="scoresItem scoresItemYellow">
                        <div class="name">昵称</div>
                        <div class="currentScores">得分</div>
                        <div class="consumeScores">消耗</div>
                    </div>
                    <?php foreach ($board->balance_board as $player){?>
                        <?php if($player['score'] > 0){?>
                            <div class="scoresItem scoresItemYellow">
                                <?php if($player['big_winner']){?>
                                    <img src="<?php echo $image_url;?>files/images/common/rank_bigwinner.png" style="top: 0; margin-top: -1.5vw; left: 2vw;height: 100%">
                                <?php }?>
                                <div class="name"><?php echo $player['name']?></div>
                                <div class="currentScores">+<?php echo $player['score']?></div>
                                <div class="consumeScores"><?php echo (isset($player['consume']) && $player['consume'] ? $player['consume']: ''); ?></div>
                            </div>
                        <?php }else{?>
                            <div class="scoresItem scoresItemWhite">
                                <div class="name"><?php echo $player['name']?></div>
                                <div class="currentScores"><?php echo $player['score']?></div>
                            </div>
                        <?php }?>
                    <?php }?>
                </div>
                <div class="button roundEndShow" >
                    <img src="<?php echo $image_url;?>files/images/common/rank_save.png" style="float: left;margin-left: 34%;" />
                </div>
            </div>
        </div>
    <?php }?>

    <?php if($board->game_type == 71){?>
        <div class="lbull">
            <div class="ranking hideRanking" id="ranking" style="z-index: 1">
                <div class="rankBack">
                    <img src="<?php echo $image_url;?>files/images/tbull/rank_bg.png" style="position: absolute;top: 0;left: 0;width: 100%">
                </div>
                <div class="rankText" style="position: absolute;top: 4%;">
                    <img src="<?php echo $image_url;?>files/images/tbull/ranking.png" style="position: absolute;top: 0;left: 18vw;width: 164vw;">
                    <div class="time" style="position: absolute;top: 43vw;width: 100%;">
                        <a style="border-color: #7f7e85;background-color: #7f7e85;font-size: 6vw;">房间号:<?php echo $room_number;?>&nbsp&nbsp&nbsp&nbsp<?php echo $board->over_time;?>&nbsp&nbsp&nbsp&nbsp<?php echo $board->total_round;?>局</a>
                    </div>
                    <div style="height: 62vw;"></div>
                    <div class="room_own">
                        <p>知己娱乐房主：<?php echo $room_own;?></p>
                    </div>
                    <div class="scoresItem scoresItemTitle">
                        <div class="user_code">ID</div>
                        <div class="name">名称</div>
                        <div class="currentScores">分数</div>
                    </div>
                    <?php foreach ($board->balance_board as $player){?>
                        <?php if($player['score'] > 0){?>
                            <div class="scoresItem scoresItemYellow">
                                <?php if($player['big_winner']){?>
                                    <img src="<?php echo $image_url;?>files/images/common/rank_bigwinner.png" style="top: 0; margin-top: -1.5vw; left: 2vw;height: 100%">
                                <?php }?>
                                <div class="user_code"><?php echo $player['account_id']?></div>
                                <div class="name"><?php echo $player['name']?></div>
                                <div class="currentScores">+<?php echo $player['score']?></div>
                            </div>
                        <?php }else{?>
                            <div class="scoresItem scoresItemWhite">
                                <div class="user_code"><?php echo $player['account_id']?></div>
                                <div class="name"><?php echo $player['name']?></div>
                                <div class="currentScores"><?php echo $player['score']?></div>
                            </div>
                        <?php }?>
                    <?php }?>
                </div>
                <div class="button roundEndShow" >
                    <img src="<?php echo $image_url;?>files/images/common/rank_save.png" style="float: left;margin-left: 34%;" />
                </div>
            </div>
        </div>
    <?php }?>
    
    <?php if($board->game_type == 36){?>
        <div class="sangong">
            <div class="ranking hideRanking" id="ranking" style="z-index: 1">
                <div class="rankBack">
                    <img src="<?php echo $image_url;?>files/images/sangong/rank_bg.png" style="position: absolute;top: 0;left: 0;width: 100%">
                </div>
                <div class="rankText" style="position: absolute;top: 4%;">
                    <img src="<?php echo $image_url;?>files/images/sangong/rank_frame.png" style="position: absolute;top: 0%;left: 120px;width: 560px;">
                    <div class="time" style="position: absolute;top: 140px;width: 100%;">
                        <a style="border-color: #7f7e85;background-color: #7f7e85;font-size: 24px;">房间号:<?php echo $room_number;?>&nbsp&nbsp&nbsp&nbsp<?php echo $board->over_time;?>&nbsp&nbsp&nbsp&nbsp<?php echo $board->total_round;?>局</a>
                    </div>
                    <div style="height: 210px;"></div>
                    <div class="room_own">
                        <p>知己娱乐房主：<?php echo $room_own;?></p>
                    </div>
                    <div class="scoresItem scoresItemTitle">
                        <div class="user_code">ID</div>
                        <div class="name">名称</div>
                        <div class="currentScores">分数</div>
                    </div>
                    <?php foreach ($board->balance_board as $player){?>
                        <?php if($player['score'] > 0){?>
                            <div class="scoresItem scoresItemYellow">
                                <?php if($player['big_winner']){?>
                                    <img src="<?php echo $image_url;?>files/images/common/rank_bigwinner.png" style="top: 0; margin-top: -10px; left: 4px;height: 100%">
                                <?php }?>
                                <div class="user_code"><?php echo $player['account_id']?></div>
                                <div class="name"><?php echo $player['name']?></div>
                                <div class="currentScores">+<?php echo $player['score']?></div>
                            </div>
                        <?php }else{?>
                            <div class="scoresItem scoresItemWhite">
                                <div class="user_code"><?php echo $player['account_id']?></div>
                                <div class="name"><?php echo $player['name']?></div>
                                <div class="currentScores"><?php echo $player['score']?></div>
                            </div>
                        <?php }?>
                    <?php }?>
                </div>
                <div class="button roundEndShow" >
                    <img src="<?php echo $image_url;?>files/images/common/rank_save.png" style="float: left;margin-left: 34%;" />
                </div>
            </div>
        </div>
    <?php }?>

    <?php if($board->game_type == 37){?>
        <div class="sangong9">
            <div class="ranking hideRanking" id="ranking" style="z-index: 1">
                <div class="rankBack">
                    <img src="<?php echo $image_url;?>files/images/sangong/rank_bg.png" style="position: absolute;top: 0;left: 0;width: 100%;height: 100%;">
                </div>
                <div class="rankText" style="position: absolute;top: 4%;">
                    <img src="<?php echo $image_url;?>files/images/sangong/rank_frame.png" style="position: absolute;top: 0%;left: 67px;width: 666px;">
                    <div class="time" style="position: absolute;top: 174px;width: 100%;">
                        <a style="border-color: #7f7e85;background-color: #7f7e85;font-size: 24px;">房间号:<?php echo $room_number;?>&nbsp&nbsp&nbsp&nbsp<?php echo $board->over_time;?>&nbsp&nbsp&nbsp&nbsp<?php echo $board->total_round;?>局</a>
                    </div>
                    <div style="height: 250px;"></div>
                    <div class="room_own">
                        <p>知己娱乐房主：<?php echo $room_own;?></p>
                    </div>
                    <div class="scoresItem scoresItemTitle">
                        <div class="user_code">ID</div>
                        <div class="name">名称</div>
                        <div class="currentScores">分数</div>
                    </div>
                    <?php foreach ($board->balance_board as $player){?>
                        <?php if($player['score'] > 0){?>
                            <div class="scoresItem scoresItemYellow">
                                <?php if($player['big_winner']){?>
                                    <img src="<?php echo $image_url;?>files/images/common/rank_bigwinner.png" style="top: 0; margin-top: -6px; left: 4px;height: 100%">
                                <?php }?>
                                <div class="user_code"><?php echo $player['account_id']?></div>
                                <div class="name"><?php echo $player['name']?></div>
                                <div class="currentScores">+<?php echo $player['score']?></div>
                            </div>
                        <?php }else{?>
                            <div class="scoresItem scoresItemWhite">
                                <div class="user_code"><?php echo $player['account_id']?></div>
                                <div class="name"><?php echo $player['name']?></div>
                                <div class="currentScores"><?php echo $player['score']?></div>
                            </div>
                        <?php }?>
                    <?php }?>
                </div>
                <div class="button roundEndShow" >
                    <img src="<?php echo $image_url;?>files/images/common/rank_save.png" style="float: left;margin-left: 34%;" />
                </div>
            </div>
        </div>
    <?php }?>

    <?php if($board->game_type == 38){?>
        <div class="sangong12">
            <div class="ranking hideRanking" id="ranking" style="z-index: 1">
                <div class="rankBack">
                    <img src="<?php echo $base_url;?>files/images/sangong/rank_bg.png" style="position: absolute;top: 0;left: 0;width: 100%">
                </div>
                <div class="rankText" style="position: absolute;top: 4%;">
                    <img src="<?php echo $base_url;?>files/images/sangong/rank_frame.png" style="position: absolute;top: 0;left: 25vw;width: 150vw;">
                    <div class="time" style="position: absolute;top: 44vw;width: 100%;">
                        <a style="border-color: #7f7e85;background-color: #7f7e85;font-size: 6vw;">房间号:<?php echo $room_number;?>&nbsp&nbsp&nbsp&nbsp<?php echo $board->over_time;?>&nbsp&nbsp&nbsp&nbsp<?php echo $board->total_round;?>局</a>
                    </div>
                    <div style="height: 268px;"></div>
                    <?php foreach ($board->balance_board as $player){?>
                        <?php if($player['score'] > 0){?>
                            <div class="scoresItem scoresItemYellow">
                                <?php if($player['big_winner']){?>
                                    <img src="<?php echo $image_url;?>files/images/common/rank_bigwinner.png" style="top: 0; margin-top: -1.5vw; left: 2vw;height: 100%">
                                <?php }?>
                                <div class="name"><?php echo $player['name']?></div>
                                <div class="currentScores">+<?php echo $player['score']?></div>
                            </div>
                        <?php }else{?>
                            <div class="scoresItem scoresItemWhite">
                                <div class="name"><?php echo $player['name']?></div>
                                <div class="currentScores"><?php echo $player['score']?></div>
                            </div>
                        <?php }?>
                    <?php }?>
                </div>
                <div class="button roundEndShow" >
                    <img src="<?php echo $image_url;?>files/images/common/rank_save.png" style="float: left;margin-left: 34%;" />
                </div>
            </div>
        </div>
    <?php }?>

    <?php if($board->game_type == 61){?>
        <div class="darkPoint">
            <div class="ranking hideRanking" id="ranking" style="z-index: 1">
                <div class="rankBack">
                    <img src="<?php echo $image_url;?>files/images/tbull/rank_bg.png" style="position: absolute;top: 0;left: 0;width: 100%">
                </div>
                <div class="rankText" style="position: absolute;top: 4%;">
                    <img src="<?php echo $image_url;?>files/images/dp/ranking.png" style="position: absolute;top: 0;left: 18vw;width: 164vw;">
                    <div class="time" style="position: absolute;top: 43vw;width: 100%;">
                        <a style="border-color: #7f7e85;background-color: #7f7e85;font-size: 6vw;">房间号:<?php echo $room_number;?>&nbsp&nbsp&nbsp&nbsp<?php echo $board->over_time;?>&nbsp&nbsp&nbsp&nbsp<?php echo $board->total_round;?>局</a>
                    </div>
                    <div style="height: 62vw;"></div>
                    <div class="room_own">
                        <p>知己娱乐房主：<?php echo $room_own;?></p>
                    </div>
                    <div class="scoresItem scoresItemTitle">
                        <div class="user_code">ID</div>
                        <div class="name">名称</div>
                        <div class="currentScores">分数</div>
                    </div>
                    <?php foreach ($board->balance_board as $player){?>
                        <?php if($player['score'] > 0){?>
                            <div class="scoresItem scoresItemYellow">
                                <?php if($player['big_winner']){?>
                                    <img src="<?php echo $image_url;?>files/images/common/rank_bigwinner.png" style="top: 0; margin-top: -1.5vw; left: 2vw;height: 100%">
                                <?php }?>
                                <div class="user_code"><?php echo $player['account_id']?></div>
                                <div class="name"><?php echo $player['name']?></div>
                                <div class="currentScores">+<?php echo $player['score']?></div>
                            </div>
                        <?php }else{?>
                            <div class="scoresItem scoresItemWhite">
                                <div class="user_code"><?php echo $player['account_id']?></div>
                                <div class="name"><?php echo $player['name']?></div>
                                <div class="currentScores"><?php echo $player['score']?></div>
                            </div>

                            <div class="scoresItem scoresItemWhite">
                                <div class="user_code"><?php echo $player['account_id']?></div>
                                <div class="name"><?php echo $player['name']?></div>
                                <div class="currentScores"><?php echo $player['score']?></div>
                            </div>
                            <div class="scoresItem scoresItemWhite">
                                <div class="user_code"><?php echo $player['account_id']?></div>
                                <div class="name"><?php echo $player['name']?></div>
                                <div class="currentScores"><?php echo $player['score']?></div>
                            </div>
                            <div class="scoresItem scoresItemWhite">
                                <div class="user_code"><?php echo $player['account_id']?></div>
                                <div class="name"><?php echo $player['name']?></div>
                                <div class="currentScores"><?php echo $player['score']?></div>
                            </div>
                            <div class="scoresItem scoresItemWhite">
                                <div class="user_code"><?php echo $player['account_id']?></div>
                                <div class="name"><?php echo $player['name']?></div>
                                <div class="currentScores"><?php echo $player['score']?></div>
                            </div>
                            <div class="scoresItem scoresItemWhite">
                                <div class="user_code"><?php echo $player['account_id']?></div>
                                <div class="name"><?php echo $player['name']?></div>
                                <div class="currentScores"><?php echo $player['score']?></div>
                            </div>
                            <div class="scoresItem scoresItemWhite">
                                <div class="user_code"><?php echo $player['account_id']?></div>
                                <div class="name"><?php echo $player['name']?></div>
                                <div class="currentScores"><?php echo $player['score']?></div>
                            </div>
                            <div class="scoresItem scoresItemWhite">
                                <div class="user_code"><?php echo $player['account_id']?></div>
                                <div class="name"><?php echo $player['name']?></div>
                                <div class="currentScores"><?php echo $player['score']?></div>
                            </div>
                            <div class="scoresItem scoresItemWhite">
                                <div class="user_code"><?php echo $player['account_id']?></div>
                                <div class="name"><?php echo $player['name']?></div>
                                <div class="currentScores"><?php echo $player['score']?></div>
                            </div>
                        <?php }?>
                    <?php }?>
                </div>
                <div class="button roundEndShow" >
                    <img src="<?php echo $image_url;?>files/images/common/rank_save.png" style="float: left;margin-left: 34%;" />
                </div>
            </div>
        </div>
    <?php }?>

</div>

</body>

<script type="text/javascript" src="<?php echo $image_url;?>files/js/canvas.js"></script>
<script type="application/javascript">
    $(document).ready(function () {
        $(".rankPanel").show();
        canvas();
//        $(".rankPanel").hide();
    });
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
</script>

</html>
