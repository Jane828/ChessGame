<?php


class Game {

    /*****************************
     * 参数
     *****************************/


    //游戏类型
    const Game_DarkPo10_Type = 61;    //10人暗宝
    const Game_DarkPo13_Type = 62;    //13人暗宝
    const Game_DarkPo16_Type = 63;    //16人暗宝

    /*	房间用户uid	*/
    const RoomUser_UID = "[roomid]:[accountid]";

    /*  普通房间 - 包厢房间*/
    const ORDINARY_ROOM				= 0;	//普通房间
    const Enable_Box				= 1;	//启用包厢

    /*	开局默认分数	*/
    const Default_Score = 0;


    /*	游戏人数范围	*/
    const GameUser_MinCount = 2;

    /*	游戏人数支持10,13,16 ，最大数据需要从房间配置参数中读取	*/
    const GameUser_MaxCount16 = 16;
    const GameUser_MaxCount13 = 13;
    const GameUser_MaxCount10 = 10;

    /* 观战人数 */
    const GameAudience_maxCount = 100;


    /*	用户状态	*/
    const AccountStatus_Initial  = 0;    //首次进房初始状态
    const AccountStatus_Notready = 1;    //未准备
    const AccountStatus_Ready    = 2;    //已准备
    const AccountStatus_Choose   = 3;    //选择抢庄
    const AccountStatus_Notgrab  = 4;    //不抢庄
    const AccountStatus_Grab     = 5;    //抢庄
    const AccountStatus_Put      = 6;       //选择放宝
    const AccountStatus_Notput   = 7;    //未放宝
    const AccountStatus_Input    = 8;    //放宝
    const AccountStatus_Bet      = 9;    //下注
    const AccountStatus_StopBet  = 10;   //停止下注
    const AccountStatus_Notshow  = 11;    //未开奖
    const AccountStatus_Show     = 12;    //开奖
    const AccountStatus_Watch    = 13;    //观战


    /* 观众状态 */
    const AudienceStatus_on  = 1;    //加入观战
    const AudienceStatus_off = 0;    //离开观战


    /*	游戏中状态	*/
    const PlayingStatus_Waiting = 1;    //等待别人中
    const PlayingStatus_Betting = 2;    //下注中
    const PlayingStatus_puting  = 3;    //放暗宝中


    /*	游戏回合	*/
    const Circle_Grab = 1;    //叫庄
    const Circle_Put  = 2;    //放宝
    const Circle_Bet  = 3;    //下注
    const Circle_Show = 4;    //开奖

    /*	房间状态	*/
    const RoomStatus_Waiting = 1;    //等待中
    const RoomStatus_Playing = 2;    //游戏中
    const RoomStatus_Closed  = 3;    //已关闭

    /*	房间类型	*/
    const BankerMode_FreeGrab      = 1;    //自由抢庄
    const BankerMode_FixedBanker   = 2;    //固定庄家
    const BankerMode_RoomownerGrab = 3;    //开房当庄

    /*	操作超时时间	*/
    const LimitTime_StartGame = 20;    //第一局开局时限
    const LimitTime_Ready     = 30;    //准备时限
    const LimitTime_Grab      = 10;    //抢庄时限
    const LimitTime_Betting   = 30;    //下注时限
    const LimitTime_puting    = 30;    //放宝时限
    const LimitTime_Show      = 10;    //摊牌时限
    const LimitTime_ClearRoom = 600;    //时限

    const Default_Firstlossrate  = 3;
    const Default_Secondlossrate = 2;
    const Default_Threelossrate  = 1;


    //暗宝游戏规则参数
    const Rule_Enter  = 0;
    const Rule_Dragon = 1;
    const Rule_Exit   = 2;
    const Rule_Tiger  = 3;

    const Rule_Center     = 0;
    const Rule_LeftAngle  = 1;
    const Rule_RightAngle = 2;
    const Rule_Bunch      = 3;
    const Rule_LeftStick  = 4;
    const Rule_RightStick = 5;
    const Rule_Same       = 6;

    const Rule_Step = 2; //四个方向，对方步长

    const Default_UpperLimit_Score = 500;

    const Chip_Array = array(10, 20, 30, 50, 100);
    const Areas = array(0,1,2,3);
    const SubAreas = array(0, 1, 2, 3, 4,5,6);
    const  Xpoint = 0.0291 ; //筹码相对大小


}


/* End of file constants.php */
/* Location: ./application/config/constants.php */
