<?php

class Redis_CONST {

    /*****************************
     * 参数
     *****************************/
    const SUCCESS            = 0;           //操作成功
    const FAILED             = -1;          //操作失败
    const Key_Nonexistent    = -1;
    const Key_Exists         = TRUE;
    const Member_Nonexistent = FALSE;
    const Member_Exists      = TRUE;
    const DATA_NONEXISTENT   = FALSE;       //数据不存在

    /*
        Logs
    */
    const GameResult_Key = "GR:[dealernum]:[gametype]";
    const GameScore_Key  = "GS:[dealernum]:[gametype]";

    /*
        Room hash
    */
    const Room_Key             = "36:Room:[roomid]";
    const Room_Field_Number    = "number";                      //房间号
    const Room_field_box       = "box_number";                  //包厢房间
    const Room_Field_GameRound = "ground";                      //当前轮数
    const Room_Field_GameNum   = "gnum";                        //当前局数
    //const Room_Field_PeopleNum= "pnum";                       //房间人数
    const Room_Field_Max_Player= "player_max_num";              //游戏最大人数
    const Room_Field_Status       = "stat";                     //房间状态，1等待、2进行中、3关闭
    const Room_Field_DefaultScore = "dfltScr";                  //开局默认分数
    const Room_Field_ActiveUser   = "actUser";                  //当前操作用户，默认-1

    const Room_Field_ActiveTimer = "actTimer";                  //当前计时器ID，默认-1
    const Room_Field_ReadyTime   = "readyTime";                 //自动开局计时器设置时间，默认-1表示没有倒计时

    //const Room_Field_ClearId		= "clearId";            //当前扫房ID，默认-1
    //const Room_Field_ClearTime	= "clearTime";          //扫房ID，默认-1 表示没有


    const Room_Field_Creator         = "creator";               //房间创建者 account_id
    const Room_Field_Paytype         = "paytype";               //1AA,2房主扣卡
    const Room_Field_Scoreboard      = "scoreboard";            //每局积分榜
    const Room_Field_BaseScore       = "baseScore";             //当前底分
    const Room_Field_TicketCount     = "ticketcnt";             //每轮消耗房卡数量
    const Room_Field_TotalNum        = "totalnum";              //每轮总局数
    const Room_Field_CountDown_Ready = "countDown_ready";       // 准备倒计时
    const Room_Field_CountDown_Bet   = "countDown_bet";         // 下注倒计时
    const Room_Field_CountDown_Grab  = "countDown_grab";        // 抢庄倒计时
    const Room_Field_CountDown_Show  = "countDown_show";        // 摊牌倒计时
    const Room_Field_Is_Joker        = "isJoker";
    const Room_Field_Is_Bj           = "isBj";
    const Room_Field_BankerMode      = "bankermode";            //庄家类型，1自由叫庄，2明牌抢庄，3牛牛上庄，4通比牛牛，5固定庄家
    const Room_Field_BankerScoreType = "bankerscoretype";       //庄家上庄类型
    const Room_Field_BankerScore     = "bankerscore";           //庄家上庄类型

    const Room_Field_StartTime = "startTime";                   //开局时间

    /*
        Room User Score
        总积分
        hash
    */
    const RoomScore_Key        = "36:RoomScore:[roomid]";
    const RoomScore_Field_User = "[accountid]";

    /*
        Room Account User Status
        用户状态
        hash
    */
    const AccountStatus_Key        = "36:AccStatus:[roomid]";
    const AccountStatus_Field_User = "[accountid]";

    /*
        用户是否扣了房卡  hash
    */
    const TicketChecked_Key = "36:TicketChecked:[roomid]";

    /*
        Room Join Sequence

        有序集合
            score	:	timestamp
            value	:	account_id
    */
    const RoomSequence_Key = "36:RoomSeq:[roomid]";

    /*
        叫分
        hash
    */
    const Multiples_Key        = "36:Multiples:[roomid]";
    const Multiples_Field_User = "[accountid]";

    /*
        是否已经摊牌  0尚未摊牌   1已经摊牌
        hash
    */
    const ShowCard_Key = "36:Show:[roomid]";

    /*
        手牌
        hash
    */
    const Card_Key        = "36:Card:[roomid]";
    const Card_Field_User = "[accountid]";

    /*
        当前游戏局参数
        hash
    */
    const Play_Key              = "36:Play:[roomid]";
    const Play_Field_Banker = "banker";                         //庄家 account_id
    const Play_Field_Circle = "circle";                         //本局第几圈 1叫庄，2下注，3摊牌

    const Play_Field_BankerMult = "bankermult";                 //庄家叫分倍数

    //const Play_Field_TimerId	= "timerId";                    //当前计时器ID，默认-1
    //const Play_Field_TimerTime= "timerTime";                  //自动开局计时器设置时间，默认-1表示没有倒计时

    /*
        游戏局玩家队列
    */
    const PlayMember_Key = "36:PlayMem:[roomid]";


    /*
        抢庄
        hash
    */
    const Grab_Key        = "36:Grab:[roomid]";
    const Grab_Field_User = "[accountid]";

    /*
        audience redis
        观战
    */
    const RoomAudience_Key     = "36:RoomAud:[roomid]";
    const RoomAudienceInfo_Key = "36:RoomAudInfo:[roomid]";

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
