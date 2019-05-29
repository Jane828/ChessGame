<?php

class Redis_CONST {

    /*****************************
     * 参数
     *****************************/
    const SUCCESS            = 0;        //操作成功
    const FAILED             = -1;        //操作失败
    const Key_Nonexistent    = -1;
    const Key_Exists         = TRUE;
    const Member_Nonexistent = FALSE;
    const Member_Exists      = TRUE;
    const DATA_NONEXISTENT   = FALSE;        //数据不存在


    /*
        Logs
    */
    const GameResult_Key = "GR:[dealernum]:[gametype]";
    const GameScore_Key  = "GS:[dealernum]:[gametype]";


    const Room_Field_ActiveTimer = "actTimer";                  //当前计时器ID，默认-1

    /*
        Room

        hash
    */
    const Room_Key                  = "Room:[roomid]";
    const Room_Field_Number         = "number";                   //房间号
    const Room_field_box       = "box_number";                  //包厢房间
    const Room_Field_GameRound      = "ground";                   //当前轮数
    const Room_Field_GameNum        = "gnum";                     //当前局数
    const Room_Field_Max_Player     = "player_max_num";              //游戏最大人数
    const Room_Field_TotalNum       = "totalnum";                 //每轮总局数
    const Room_Field_Status         = "stat";                     //房间状态，1等待、2进行中、3关闭
    const Room_Field_Firstlossrate  = "mlossrate";                  //赔率设置:龙虎出入
    const Room_Field_Secondlossrate = "slossrate";                  //赔率设:同粘
    const Room_Field_Threelossrate  = "tlossrate";                  //赔率设置:角串


    const Room_Field_Banker = "banker";                   //庄家 account_id
    const Play_Field_Circle = "circle";                   //本局第几圈 1叫庄，2放宝 3下注，4开奖

    const Room_Field_Scoreboard = "scoreboard";                 //每局积分榜

    const Room_Field_Creator         = "creator";            //房间创建者 account_id
    const Room_Field_TicketCount     = "ticketcnt";          //每轮消耗房卡数量 1   2
    const Room_Field_ChipType        = "chiptype";           //筹码组  1   2    3
    const Room_Field_CountDown_ready = "countDown_ready";    // 准备倒计时
    const Room_Field_CountDown_Grab  = "countDown_grab";      // 抢庄倒计时
    const Room_Field_CountDown_Put   = "countDown_put";      // 放宝倒计时
    const Room_Field_CountDown_Bet   = "countDown_bet";      // 下注倒计时
    const Room_Field_CountDown_Show  = "countDown_show";      // 开奖倒计时
    const Room_Field_BankerMode      = "bankermode";            //庄家类型，1自由叫庄，2固定庄家，3开房当庄，

    const LimitTime_StartGame = 15;    //第一局开局时限
    const LimitTime_Ready     = 9;    //每局之间时限
    const LimitTime_Grab      = 10;    //抢庄时限
    const LimitTime_Betting   = 15;    //下注时限
    const LimitTime_Show      = 8;    //摊牌时限
    const LimitTime_ClearRoom = 300;    //清理房间时间

    const Room_Field_GameType = "gameType"; // 61, 62, 63

    const Room_Field_UpperLimit = "upperlimit";         // 封顶上限

    const Room_Field_StartTime = "startTime";           //开局时间

    /*
        Room User Score
        总积分
        hash
    */
    const RoomScore_Key        = "RoomScore:[roomid]";
    const RoomScore_Field_User = "[accountid]";


    /*
        Room Account User Status
        用户状态
        hash
    */
    const AccountStatus_Key = "AccStatus:[roomid]";

    const AccountStatus_Field_User = "[accountid]";


    /*
        用户是否扣了房卡  hash
    */
    const TicketChecked_Key = "TicketChecked:[roomid]";

    /*
       用户抢庄  hash
   */
    const Grab_Key = "Grab:[roomid]";


    /*
        Room Join Sequence
        有序集合
        score	:	seat number 座号
        value	:	account_id
    */
    const RoomSequence_Key = "RoomSeq:[roomid]";

    /*
        已经下注的筹码
        hash
    */
    const Chip_Key        = "Chip:[roomid]";
    const Chip_Array_Key        = "ChipArray:[roomid]";
    const Chip_Field_User = "[accountid]";

    /*
        已经下注的筹码的区域
        hash
    */
    //区分到每个账户
    const Enter_Chip_Area_Key = "EnterChip:[roomid]";
    const Exit_Chip_Area_Key  = "ExitChip:[roomid]";
    const Tiger_Chip_Area_Key = "TigerChip:[roomid]";
    //const Dragon_Chip_Area_Key        = "DragonChip:[roomid]:[accountid]";
    const Dragon_Chip_Area_Key = "DragonChip:[roomid]";

    //所有人下注信息
    const Enter_All_Key = "EnterAllChip:[roomid]";
    const Exit_All_Key  = "ExitAllChip:[roomid]";
    const Tiger_All_Key = "TigerAllChip:[roomid]";
    const Dragon_All_Key = "DragonAllChip:[roomid]";

    /*
        手牌
        hash
    */
    const Card_Key        = "Card:[roomid]";
    const Card_Field_User = "[accountid]";

    /*
        当前游戏局参数
        hash
    */
    const Play_Key             = "Play:[roomid]";
    const Play_Field_PoolScore = "poolScr";             //分数池

    const Play_Field_Benchmark = "mark";                //当前叫分基准

    const Play_Field_ActiveUser = "actUser";            //当前操作用户，默认-1
    const Play_Field_TimerId    = "timerId";            //当前计时器ID，默认-1
    const Play_Field_TimerTime  = "timerTime";          //自动开局计时器设置时间，默认-1表示没有倒计时
    const Play_Field_Prize      = "Prize";   //当前局庄家选择放宝的结果

    /*
        游戏局玩家队列
    */
    const PlayMember_Key = "PlayMem:[roomid]";


    /*
        Room audience Sequence
        有序集合
        score	:	account_id
        values	:	{nickname:xxx,headimgurl:xxx}
    */
    const RoomAudience_Key     = "RoomAud:[roomid]";
    const RoomAudienceInfo_Key = "RoomAudInfo:[roomid]";

    /*
     * box redis
     * 包厢信息
     */
    const BoxAccount_Key = "BoxAccount:[accountid]";           //玩家加入的包厢+房间id：哈希表
    const BoxEmptyRoom_Key = "BoxEmptyRoom:[boxnumber]";       //包厢存在空位房间集合：集合
    const BoxCurrentRoom_Key = "BoxCurrentRoom:[boxnumber]";   //包厢正在游戏的房间集合：集合
}


/* End of file constants.php */
/* Location: ./application/config/constants.php */
