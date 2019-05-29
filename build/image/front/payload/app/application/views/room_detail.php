<html>
<head>
<meta charset="utf-8" >
<meta name="viewport" content="width=device-width,initial-scale=1.0, minimum-scale=1.0, maximum-scale=1.0, user-scalable=no"/>
<meta name="format-detection" content="telephone=no" />
<meta name="msapplication-tap-highlight" content="no" />
<title>我的主页</title>

<style type="text/css">
	.panel {margin-top : 15px;width: 100%;background-color: #291c4d;}
    .panel .text {text-align: center;font-size: 13pt;font-weight: bold;padding: 25px 15px 25px 15px;margin: 0}
    .panel .time {color: white}
    .panel .rule {color: #ff9e00}
    .panel .btn {color: white;font-size: 13pt;font-weight: bold;background-color: #fea500;text-align: center;padding: 15px 15px 15px 15px;display: block;text-decoration:none;}

    .playerPanel {margin-top : 15px;width: 100%}
    .playerPanel .player {margin-top : 2px;width: 100%;background-color: #291c4d;padding:10px;box-sizing: border-box; }
    .playerPanel .player:first-child {margin-top : 0;}

    .roundText {color: white;font-size: 12pt;font-weight: bold}
    .roundPanel {width: 100%;margin-top: 10px;color: white;font-weight:bold;font-size: 12pt;border: none;border-collapse: collapse}
    .roundPanel td {padding:15px 0 15px 0;text-align: center;border: none;margin: 0}
    .roundPanel .head {color: #ff9f00;background-color: #291c4d}
    .roundPanel .hand {border-top:2px solid #0e0226;background-color: #291c4d;font-size: 11pt}
    .roundPanel td.username {width: 25%;}
    .roundPanel td.handcard {width: 40%}
    .roundPanel td.times {width: 15%}
    .roundPanel td.score {width: 15%}

    .roundPanel td.username img {position:relative;top: 3px;width: 20px;height: 20px;}
    .roundPanel .handcards {position: relative;height: 50px;}
    .roundPanel .handcards .type {position: absolute;width:40px;top: 16px;}
    .roundPanel .handcards .card {margin-left:40px;position:absolute;width: 35px;height: 50px;border-radius: 5%;box-shadow: #0e0e0e -2px 2px 5px;display: inline-block}

</style>

<script type="text/javascript" src="<?php echo $image_url;?>files/js/jquery-1.9.1.min.js"></script>

</head>

<body style="background-color: #0e0226">

	<div class="main" id="app-main" style="position: relative; width: 100%;margin: 0 auto; background: #0e0226; ">
        <div style="width: 60px;height: 73px;top: 0;position: absolute;left: 0;z-index: 10;" onClick="window.history.go(-1);">
            <img src="<?php echo $image_url;?>files/images/common/back_text.png" style="height: 56px;width: auto;" />
        </div>
        <div class="panel" style="width: 87%;margin-left: 13%;">
            <p class="text time" style="padding:16px 15px;"><?php echo $board->start_time;?>&nbsp;至&nbsp;<?php echo $board->over_time;?></p>
        </div>

        <div class="panel">
            <p class="text rule"><?php echo $board->rule_text;?></p>
        </div>

        <div class="panel">
            <a href="/record/board?id=<?php echo $room_number;?>&type=<?php echo $game_type;?>" class="btn">生成积分榜</a>
        </div>

        <div class="playerPanel">
            <?php foreach ($board->balance_board as $player){ ?>
                <div class="player">
                    <div style="display: inline-block">
                        <img style="width: 60px;height: 60px" src="<?php echo $player['head'];?>">
                    </div>
                    <div style="display: inline-block;margin-left: 10px">
                        <p style="color: white;font-weight: bold;margin: 0;"><?php echo $player['name'];?></p>
                        <p style="color: #4cccfd;font-weight: bold;margin: 15px 0 0 0;">ID:<?php echo $player['user_code'];?></p>
                    </div>
                    <div style="display: inline-block;float: right;margin-right: 20px">
                        <p style="color: white;font-weight: bold"><?php echo $player['score'];?></p>
                    </div>
                </div>
            <?php }?>

        </div>

        <?php foreach ($rounds as $round){?>
            <div style="margin-top: 15px">
                <div class="roundText"><span><?php echo $round['game_num'];?>/<?php echo $round['total_num'];?>局</span></div>
                <div style="width: 100%;">
                    <table class="roundPanel">
                        <tr class="head">
                            <td class="username">用户名字</td>
                            <td class="handcard">牌型</td>
                            <?php if($game_type == 1 || $game_type == 110 || $game_type == 111){?>
                                <td class="times">下注</td>
                                <td class="score">得分</td>
                            <?php }else{?>
                                <td class="times">倍数</td>
                                <td class="score">得分</td>
                            <?php }?>
                        </tr>

                        <?php foreach ($round['player_cards'] as $p){ ?>
                            <tr class="hand">
                                <td class="username">
                                    <?php if($p['is_banker']){?>
                                        <img src="<?php echo $image_url;?>files/images/activity/banker_icon.png"/><span><?php echo $p['name'];?></span>
                                    <?php }else{?>
                                        <span><?php echo $p['name'];?></span>
                                    <?php }?>
                                </td>
                                <?php if(!empty($p['player_cards'])){ ?>
                                    <td class="handcard" style="text-align: left">
                                        <div style="display: inline-block" class="handcards">
                                            <?php if($p['is_banker']){?>
                                                <div class="type" style="top: 19px;"><?php echo $p['card_type_str']?></div>
                                            <?php }else{?>
                                                <div class="type"><?php echo $p['card_type_str']?></div>
                                            <?php }?>

                                            <?php if (is_array($p['player_cards']) && !empty($p['player_cards'])) {
                                                foreach ($p['player_cards'] as $key=>$card){?>
                                                <div class="card" type="card" index="<?php echo $key;?>" suit="<?php echo $card['suit'];?>" point="<?php echo $card['point'];?>"></div>
                                            <?php } ?>
                                            <?php } else if (is_string($p['player_cards'])) {
                                                echo $p['player_cards'];
                                            }?>

                                        </div>
                                    </td>
                                    <?php if($game_type == 1 || $game_type == 110){?>
                                        <td class="times"><?php echo $p['chip'];?></td>
                                        <td class="score"><?php echo $p['score'];?></td>
                                    <?php }else{?>
                                        <td class="times"><?php echo $p['chip'];?></td>
                                        <td class="score"><?php echo $p['score'];?></td>
                                    <?php }?>

                                <?php }else{ ?>
                                    <td colspan="3">未参与该局游戏</td>
                                <?php }?>
                            </tr>
                        <?php }?>

                    </table>
                </div>
            </div>
        <?php }?>
    </div>

</body>

<script type="application/javascript">
    $(function () {
        var card_image = '<?php echo $image_url;?>files/images/common/cards.jpg';
        var types = window.location.href.split('?')[1].split('&');
        var curType = '';
        for (var i = 0, len = types.length; i < len; i++) {
            if (types[i].indexOf('type') === 0) {
                curType = types[i].split('=')[1];
                break;
            }
        }
        if(curType == 13) card_image = '<?php echo $image_url;?>files/images/common/cards3.jpg';
        var styles = {
            'background-repeat' : 'no-repeat',
            'background-image'  : "url('" + card_image + "')",
            'background-size'   : (curType == 13)?'456px 250px':'456px 200px',
            'background-position' : '0px 0px'
        };

        $("[type='card']").each(function () {
            var suit = $(this).attr('suit');
            var point = $(this).attr('point');
            var index = $(this).attr('index');

            if(point == 0){
                var carImg = getSpCard(suit);
                $(this).css("background-image","url('" + carImg + "')");
                $(this).css("background-size",'35px 50px');
                $(this).css("margin-left",(40 + index*15) + 'px');
                return;
            }

            if((suit == '9' && (curType == 5 || curType == 9)) || (suit == '5')){
                var carImg = getJoker(point);
                $(this).css("background-image","url('" + carImg + "')");
                $(this).css("background-size",'35px 50px');
                $(this).css("margin-left",(40 + index*15) + 'px');
                return;
            }

            var x = -35 * (point-1);
            var y = -50 * (suit-1);
            if(curType == 13 && suit == 6) y = -50 * 4;
            styles['background-position'] = x+'px '+ y+"px";
            styles['margin-left']  = (40 + index*15) + 'px';
            $(this).css(styles);
        });
    });

    function getSpCard(suit) {
        var bkImg  = '<?php echo $image_url;?>/files/images/tbull/'+ suit + suit +".png";
        return bkImg;
    }
    
    function getJoker(point) {
        var bkImg = '<?php echo $image_url;?>/files/images/lbull/joker' + point +".png";
        return bkImg;
    }

</script>

</html>
