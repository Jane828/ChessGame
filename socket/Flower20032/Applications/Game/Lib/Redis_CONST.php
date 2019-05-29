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
    /*
        Room

        hash
    */
    const Room_Key                = "Room:[roomid]";
    const Room_Field_Number       = "number";                   //房间号
    const Room_field_box       = "box_number";                  //包厢房间
    const Room_Field_GameRound    = "ground";                   //当前轮数
    const Room_Field_GameNum      = "gnum";                     //当前局数
    const Room_Field_TotalNum     = "totalnum";                 //每轮总局数
    const Room_Field_Max_Player= "player_max_num";              //游戏最大人数
    const Room_Field_Status       = "stat";                     //房间状态，1等待、2进行中、3关闭
    const Room_Field_DefaultScore = "dfltScr";                  //开局默认分数
    const Room_Field_Banker       = "banker";                   //庄家 account_id

    const Room_Field_Scoreboard = "scoreboard";                 //每局积分榜

    const Room_Field_Creator            = "creator";            //房间创建者 account_id
    const Room_Field_TicketCount        = "ticketcnt";          //每轮消耗房卡数量 1   2
    const Room_Field_ChipType           = "chiptype";           //筹码组  1   2    3
    const Room_Field_DisablePkMen       = "dispkmen";           // 不能比牌
    const Room_Field_First_Round_Ban_PK = "First_Round_Ban_PK"; // 首轮禁止比牌, false禁止，true允许
    const Room_Field_MinScore_PK        = "min_score_pk";       // 比牌最低分
    const Room_Field_MinScore_Seen      = "min_score_seen";     // 看牌最低分
    const Room_Field_Extra_Rewards      = "extra_rewards";      // 喜牌的额外分数
    const Room_Field_235GTPanther       = "allow_235GTPanther"; // 235吃豹子 0不可以 1可以
    const Room_Field_CountDown_ready    = "countDown_ready";    // 准备倒计时
    const Room_Field_CountDown_Bet      = "countDown_bet";      // 下注倒计时

    const Room_Field_GameType = "gameType"; // 1, 110, 111

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
    const Chip_Field_User = "[accountid]";

    /*
        是否已经看牌  0尚未看牌   1已经看牌
        hash
    */
    const SeenCard_Key = "Seen:[roomid]";

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
