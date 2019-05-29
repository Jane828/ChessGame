<?php if (!defined('BASEPATH'))
    exit('No direct script access allowed');

class G_CONST {

    //常用参数
    const SUCCESS          = 0;         //操作成功
    const FAILED           = -1;        //操作失败
    const EMPTY_STRING     = "";        //空字符串
    const DATA_EXIST       = 0;         //数据存在
    const DATA_NONEXISTENT = 1;         //数据不存在
    const DEFAULT_COUNT    = 0;         //默认数字，0

    const ORDINARY_ROOM		= 0;		//普通房间

    //数据是否
    const IS_TRUE  = 1;                 //是
    const IS_FALSE = 0;                 //否

    const IS_FORWARD_FALSE       = 0;   //非转发
    const IS_FORWARD_TRUE        = 1;   //转发
    const IS_FORWARD_DELETE      = 2;   //转发已删除
    const DELETE_FORWARD_CONTENT = "内容已被删除";        //转发内容被删除
    const CAN_FORWARD_FALSE      = 0;   //不可转发
    const CAN_FORWARD_TRUE       = 1;   //可转发

    const SQL_IN_NONEXISTENT = "0";     //sql语句，where xxx in (null)

    const Return_Type_Original = "0";
    const Return_Type_String   = "1";
    const Return_Type_Array    = "2";

    //推送开关
    const Push_Topic_Message = TRUE;    //频道消息是否推送通知


    //短信内容
    const SMS_Type_Register    = 0;     //注册
    const SMS_Type_Retrieve    = 1;     //找回
    const SMS_Type_Bind        = 2;     //绑定
    const SMS_Type_Admin_Login = 3;     //管理平台登录

    const SMS_Invaild_Time     = 600;   //验证码过期时间
    const SMS_Interval_Time    = 60;    //短信间隔
    const SMS_Daily_LimitTimes = 20;    //每日发送条数显示

    //同步参数
    const DEFAULT_SYNC_LIMIT = 20;      //记录每次同步条数
    const DEFAULT_SYNC_DONE  = 2;       //同步完成标识符

    //登陆过期时间
    const LOGIN_SESSION_TIMEOUT = 604800;


    //位置距离
    const DISTANCE_ZERO       = "";     //距离
    const NEARBY_LNG_DISTANCE = 0.1;    //附近的人经度差值
    const NEARBY_LAT_DISTANCE = 0.1;    //附近的人维度差值

    //账号类型
    const Account_Find   = 0;    //find
    const Account_QQ     = 1;    //qq
    const Account_Weibo  = 2;    //weibo
    const Account_Mobile = 3;    //mobile
    const Account_WX     = 4;    //wx

    //大厅参数
    const Ticket_limit  = 50;    //创建包厢房卡最低限制
    const Box_limit = 300;        //创建包厢数量最大限制

    //性别
    const MALE   = "男";
    const FEMALE = "女";

    //数据修改符号
    const ADDCOUNT    = "+";
    const REDUCECOUNT = "-";

    //删除
    const CAN_DELETE_TRUE  = 1;         //可删除
    const CAN_DELETE_FALSE = 0;         //不可删除

    //成员关系
    const MEMBER_RELATION_NONE = 0;
    const MEMBER_RELATION_FRIND = 1;
    const MEMBER_RELATION_REFUSE = 2;
    const MEMBER_RELATION_BLACK = 3;

    const USRECODE_ACCOUNTID_SUB = 10000;

    const FRONT_WEBSOCKET_SERVER = "tcp://game_front_wsocket:20031";
    const FRONT_WEBSOCKET_CLIENT = "tcp://game_front_wsocket:20032";
}


/* End of file constants.php */
/* Location: ./application/config/constants.php */