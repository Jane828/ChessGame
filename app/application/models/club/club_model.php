<?php

include_once dirname(__DIR__) . '/public_models.php';        //加载数据库操作类

class Club_Model extends Public_Models
{
    // 公会表
    const Ticket_Create = 10;// 创建需要房卡数
    const Max_Players = 200;// 最大成员数

    // 公会动态表
    const Club_Create = 1; // 创建公会
    const Club_Rename = 2; // 重命名公会
    const Club_Join = 3; // 加入公会
    const Club_Quit = 4; // 退出公会
    const Club_Kick = 5; // 踢出公会

    // 申请表 申请状态
    const Apply_Wait = 0; // 待处理
    const Apply_Agree = 1; // 已同意
    const Apply_Refuse = 2; // 已拒绝

    // 成员表：成员状态
    const Player_Join = 1; // 已经加入
    const Player_Quit = 2; // 主动退出
    const Player_Kick = 3; // 被动踢出

    // 欢乐豆明细表
    const Bean_Buy = 1; // 玩家购买
    const Bean_Cash = 2; // 玩家兑换
    const Bean_Win = 3; // 游戏赢豆
    const Bean_Lose = 4; // 游戏输豆
    const Bean_Push = 5; // 游戏提成

    const Page_Num = 10;// 每页条数

    public function __construct()
    {
        header('Content-Type: text/html; charset=utf-8');
        parent::__construct();
    }

    public function clubs($open_id)
    {
        $user = $this->db()->where('open_id', $open_id)->where('is_delete', 0)->get(WX_Account)->row();
        if (empty($user)) {
            return ['code' => -1, 'msg' => '操作失败'];
        }

        $clubPlayer = $this->db()->where('player_id', $user->account_id)
            ->where('player_status', self::Player_Join)
            ->select('club_id, is_last')
            ->get(Club_Players)
            ->result();

        if (0 == count($clubPlayer)) {
            return ['code' => -1, 'msg' => '未加入任何公会'];
        }

        $lastClubId = 0;
        $clubId     = [];
        foreach ($clubPlayer as $item) {
            $clubId[] = $item->club_id;
            if (1 == $item->is_last) {
                $lastClubId = $item->club_id;
            }
        }

        if (0 == $lastClubId) {
            $lastClubId = $item->club_id;
        }

        if (empty($clubId)) {
            return ['code' => -1, 'msg' => '没有公会'];
        }

        $clubs = $this->db()
            ->where_in('club_id', $clubId)
            ->order_by('club_id', 'DESC')
            ->get(Clubs)
            ->result();

        if (0 == count($clubs)) {
            return ['code' => -1, 'msg' => '没有公会'];
        }

        $data = [];
        foreach ($clubs as $item) {
            $data[] = [
                'name'     => $item->club_name,
                'club_no'  => $item->club_no,
                'time'     => date('Y年m月d日', strtotime($item->created_at)),
                'nick'     => $item->admin_nick,
                'head'     => $item->admin_head,
                'is_admin' => $user->account_id == $item->admin_id,
                'is_last'  => $item->club_id == $lastClubId ? 1 : 0,
                'avatar'   => '/files/club/images/orgAvatar.jpg',
            ];
        }

        return ['code' => 0, 'data' => $data, 'msg' => '获取成功'];
    }

    public function clubInfo($club_no)
    {
        $club = $this->db()->where('club_no', $club_no)->get(Clubs)->row();
        if (empty($club)) {
            return ['code' => -1, 'msg' => '公会不存在'];
        }
        $applyCount = $this->db()->where('club_id', $club->club_id)
            ->where('apply_status', self::Apply_Wait)->get(Club_Applies)->num_rows();
        $nowPlayer  = $this->db()->where('club_id', $club->club_id)
            ->where('player_status', self::Player_Join)->get(Club_Players)->num_rows();

        $data = [
            'applyCount' => $applyCount,
            'nowPlayer'  => $nowPlayer,
            'maxPlayer'  => $club->max_player,
            'adminCode'  => $club->admin_code
        ];

        return ['code' => 0, 'data' => $data];
    }

    // 创建公会
    public function createClub($param)
    {
        $user = $param['user'];
        // 房卡是否足够
        $row = $this->db()->where('account_id', $user['aid'])->limit(1)->get(Room_Ticket)->row();
        if (empty($row)) {
            return ['code' => -1, 'msg' => '房卡不足'];
        }
        if ($row->ticket_count < self::Ticket_Create) {
            return ['code' => -1, 'msg' => '房卡不足'];
        }
        $ticket = $row->ticket_count - self::Ticket_Create;

        $this->db()->trans_start();

        // 创建
        $t    = time();
        $ts   = date('Y-m-d H:i:s', $t);
        $data = [
            'club_name'   => $param['name'],
            'admin_id'    => $user['aid'],
            'admin_code'  => $user['code'],
            'admin_head'  => $user['head'],
            'admin_nick'  => $user['nick'],
            'club_status' => 1,
            'create_card' => self::Ticket_Create,
            'max_player'  => self::Max_Players,
            'created_at'  => $ts,
            'updated_at'  => $ts,
        ];
        $db   = $this->db();
        $db->insert(Clubs, $data);

        if ($db->affected_rows() <= 0) {
            return ['code' => -1, 'msg' => '创建失败1'];
        }

        $club_id = $db->insert_id();
        $club_no = $club_id + 1000;
        $this->db()->where('club_id', $club_id)->update(Clubs, ['club_no' => $club_no]);

        // 扣房卡
        $this->db()->where('account_id', $user['aid'])->update(Room_Ticket, [
            'ticket_count' => $ticket,
            'update_time'  => $t,
            'update_appid' => 'aid_' . $user['aid']
        ]);
        // 房卡流水日志
        $data = [
            'create_time'  => $t,
            'update_time'  => $t,
            'create_appid' => 'aid_' . $user['aid'],
            'update_appid' => 'aid_' . $user['aid'],
            'account_id'   => $user['aid'],
            'is_delete'    => 0,
            'object_id'    => -1,
            'object_type'  => 9,
            'disburse'     => self::Ticket_Create,
            'balance'      => $ticket,
            'abstract'     => '创建公会',
            'journal_type' => Game_CONST::JournalType_Disburse
        ];
        $this->db()->insert(Room_TicketJournal, $data);

        // 公会动态
        $data = [
            'log_type'   => self::Club_Create,
            'club_id'    => $club_id,
            'club_name'  => $param['name'],
            'created_at' => $ts,
            'updated_at' => $ts,
        ];
        $this->db()->insert(Club_Logs, $data);

        // 会长本人加入
        $data = [
            'club_id'       => $club_id,
            'player_id'     => $user['aid'],
            'player_code'   => $user['code'],
            'player_head'   => $user['head'],
            'player_nick'   => $user['nick'],
            'player_status' => self::Player_Join,
            'player_bean'   => 0,
            'created_at'    => $ts,
            'updated_at'    => $ts,
        ];
        $this->db()->insert(Club_Players, $data);

        $this->db()->trans_complete();

        return ['code' => 0, 'msg' => '创建成功'];
    }

    // 切换公会并返回公会信息
    public function info($open_id, $club_no)
    {
        $user = $this->db()->where('open_id', $open_id)->get(WX_Account)->row();
        if (empty($user)) {
            return ['code' => -1, 'msg' => '刷新'];
        }

        $club = $this->db()->where('club_no', $club_no)->get(Clubs)->row();
        if (empty($club)) {
            return ['code' => -1, 'msg' => '公会不存在'];
        }

        $row = $this->db()->where('club_id', $club->club_id)
            ->where('player_id', $user->account_id)
            ->where('player_status', self::Player_Join)
            ->get(Club_Players)
            ->row();

        if (empty($row)) {
            return ['code' => -1, 'msg' => '你不在公会中'];
        }

        $this->db()->where('player_id', $user->account_id)->where('player_status', self::Player_Join)->update(Club_Players, ['is_last' => 0]);
        $this->db()->where('data_id', $row->data_id)->update(Club_Players, ['is_last' => 1]);

        $now = $this->db()->where('club_id', $club->club_id)
            ->where('player_status', self::Player_Join)
            ->get(Club_Players)
            ->num_rows();

        $applyNum = $this->db()->where('club_id', $club->club_id)
            ->where('apply_status', self::Apply_Wait)
            ->get(Club_Applies)
            ->num_rows();

        $data = [
            'name'     => $club->club_name,
            'club_no'  => $club_no,
            'head'     => $club->admin_head,
            'nick'     => $club->admin_nick,
            'isOwner'  => $club->admin_id == $user->account_id,
            'now'      => $now,
            'max'      => self::Max_Players,
            'balance'  => $row->player_bean,
            'time'     => date('Y年m月d日', strtotime($club->created_at)),
            'avatar'   => '/files/club/images/orgAvatar.jpg',
            'applyNum' => $applyNum
        ];

        return ['code' => 0, 'data' => $data, 'msg' => '成功'];
    }

    // 重命名公会名称
    public function rename($param)
    {
        $user = $param['user'];

        $row = $this->db()->where('club_no', $param['club_no'])->where('admin_id', $user['aid'])->get(Clubs)->row();
        if (empty($row)) {
            return ['code' => -1, 'msg' => '公会不存在'];
        }

        $ts = date('Y-m-d H:i:s');

        $this->db()->trans_start();

        $this->db()->where('club_id', $row->club_id)->update(Clubs, [
            'club_name'  => $param['name'],
            'updated_at' => $ts
        ]);

        $data = [
            'log_type'   => self::Club_Rename,
            'club_id'    => $row->club_id,
            'club_name'  => $param['name'],
            'created_at' => $ts,
            'updated_at' => $ts,
        ];
        $this->db()->insert(Club_Logs, $data);

        $this->db()->trans_complete();

        return ['code' => 0, 'msg' => '修改成功'];
    }

    // 设置密钥时获取验证码
    public function vcode($open_id = '', $mobile = '')
    {
        $time = time();

        $user = $this->db()->where('open_id', $open_id)->where('is_delete', 0)->get(WX_Account)->row();
        if (empty($user)) {
            return ['code' => -1, 'msg' => '操作失败'];
        }
        if (!empty($user->phone)) {
            $mobile = $user->phone;
        }

        // 60秒内重复请求
        $sms = $this->db()->where('mobile', $mobile)->where('type', 3)->where('is_delete', 0)
            ->order_by('sms_id', 'DESC')
            ->limit(1)
            ->get(Sms_Detail)
            ->row();
        if (!empty($sms) && $time < $sms->create_time + 60) {
            return ['code' => -1, 'msg' => '验证码请求过于频繁'];
        }

        $vcode = rand(1000, 9999);
        $data  = [
            'mobile'           => $mobile,
            'identifying_code' => $vcode,
            'type'             => 3,
            'create_time'      => $time,
            'invaild_time'     => $time + 900,
            'is_delete'        => 0,
            'session'          => '',
            'extra'            => $open_id
        ];
        $this->db()->insert(Sms_Detail, $data);


        return ['code' => 0, 'msg' => '操作成功'];
    }

    // 设置密钥
    public function setSecret($param)
    {
        $user = $this->db()->where('open_id', $param['openid'])->where('is_delete', 0)->get(WX_Account)->row();
        if (empty($user)) {
            return ['code' => -1, 'msg' => '操作失败'];
        }
        $phone = empty($user) ? $param['phone'] : $user->phone;
        $sms   = $this->db()
            ->where('mobile', $phone)
            ->where('type', 3)
            ->where('identifying_code', $param['vcode'])
            ->where('is_delete', 0)
            ->order_by('sms_id', 'DESC')
            ->limit(1)
            ->get(Sms_Detail)
            ->row();
        if (empty($sms)) {
            return ['code' => -1, 'msg' => '验证码错误'];
        }
        if (time() > $sms->invaild_time) {
            return ['code' => -1, 'msg' => '验证码过期'];
        }

        $this->db()->where('sms_id', $sms->sms_id)->update(Sms_Detail, ['is_delete' => 1]);

        $upData = ['secret' => md5($param['secret'])];
        if (empty($user->phone)) {
            $upData['phone'] = $param['phone'];
        }
        $this->db()->where('account_id', $user->account_id)->update(WX_Account, $upData);

        return ['code' => 0, 'msg' => '操作成功'];
    }

    // 公会动态
    public function dynamics($param)
    {
        $club = $this->db()->where('club_no', $param['club_no'])->get(Clubs)->row();
        if (empty($club)) {
            return ['code' => -1, 'msg' => '公会不存在'];
        }
        $page = 1 < $param['page'] ? $param['page'] : 1;

        $where = ['club_id' => $club->club_id];

        $total     = $this->db()->where($where)->get(Club_Logs)->num_rows();
        $totalPage = ceil($total / self::Page_Num);

        $data   = $this->db()->where($where)
            ->order_by('log_id', 'DESC')
            ->limit(self::Page_Num, self::Page_Num * ($page - 1))
            ->get(Club_Logs)
            ->result_array();
        $result = [];
        foreach ($data as $item) {
            $result[] = [
                'type' => $item['log_type'],
                'time' => date('m-d H:i', strtotime($item['created_at'])),
                'nick' => $item['player_nick'],
                'bean' => $item['player_bean'],
                'name' => $item['club_name']
            ];
        }

        return ['code' => 0, 'data' => $result, 'totalPage' => $totalPage, 'msg' => '获取公会动态成功'];
    }

    // 申请记录
    public function applies($param)
    {
        $user = $param['user'];

        $club = $this->db()->where('club_no', $param['club_no'])->where('admin_id', $user['aid'])->get(Clubs)->row();
        if (empty($club)) {
            return ['code' => -1, 'msg' => '公会不存在'];
        }
        $page = 1 < $param['page'] ? $param['page'] : 1;

        $count = $this->db()->where('club_id', $club->club_id)->get(Club_Applies)->num_rows();
        if (0 == $count) {
            return ['code' => -1, 'msg' => '无数据'];
        }
        $totalPage = ceil($count / self::Page_Num);

        $data = $this->db()->where('club_id', $club->club_id)
            ->order_by('data_id', 'DESC')
            ->limit(self::Page_Num, self::Page_Num * ($page - 1))
            ->select('player_code as ucode, player_nick as nick, player_head as head, apply_status as status')
            ->get(Club_Applies)
            ->result_array();

        return ['code' => 0, 'data' => $data, 'msg' => '获取成功', 'totalPage' => $totalPage];
    }

    // 成员列表
    public function members($param)
    {
        $user = $param['user'];

        $club = $this->db()->where('club_no', $param['club_no'])->where('admin_id', $user['aid'])->get(Clubs)->row();
        if (empty($club)) {
            return ['code' => -1, 'msg' => '公会不存在'];
        }

        $data = $this->db()->where('club_id', $club->club_id)
            ->where('player_status', self::Player_Join)
            ->select('player_code as ucode, player_nick as nick, player_head as head, player_bean as bean')
            ->get(Club_Players)
            ->result_array();

        return ['code' => 0, 'data' => $data, 'msg' => '获取成功'];
    }

    // 成员欢乐豆明细列表
    public function beans($param)
    {
        $club = $this->db()->where('club_no', $param['club_no'])->get(Clubs)->row();
        if (empty($club)) {
            return ['code' => -1, 'msg' => '公会不存在'];
        }

        $clubPlayer = $this->db()->where('club_id', $club->club_id)
            ->where('player_code', $param['code'])
            ->where('player_status', self::Player_Join)
            ->get(Club_Players)
            ->row();
        if (empty($clubPlayer)) {
            return ['code' => -1, 'msg' => '成员不存在'];
        }

        $where = ['club_id' => $club->club_id, 'player_id' => $clubPlayer->player_id];

        $total     = $this->db()->where($where)->get(Club_Beans)->num_rows();
        $totalPage = ceil($total / self::Page_Num);

        $page = 1 < $param['page'] ? $param['page'] : 1;

        $data   = $this->db()->where('club_id', $club->club_id)
            ->where('player_id', $clubPlayer->player_id)
            ->order_by('log_id', 'DESC')
            ->limit(self::Page_Num, self::Page_Num * ($page - 1))
            ->get(Club_Beans)
            ->result();
        $result = [];
        foreach ($data as $item) {
            $result[] = [
                'type'    => $item->change_bean >= 0 ? 1 : 2,
                'content' => $item->content,
                'balance' => $item->after_bean,
                'time'    => date('m-d H:i', strtotime($item->created_at))
            ];
        }

        return ['code' => 0, 'data' => $result, 'totalPage' => $totalPage, 'msg' => '获取成功'];
    }

    // 我的欢乐豆明细列表
    public function myBeans($param)
    {
        $user = $param['user'];
        $club = $this->db()->where('club_no', $param['club_no'])->get(Clubs)->row();
        if (empty($club)) {
            return ['code' => -1, 'msg' => '公会不存在'];
        }

        $clubPlayer = $this->db()->where('club_id', $club->club_id)
            ->where('player_id', $user['aid'])
            ->where('player_status', self::Player_Join)
            ->get(Club_Players)
            ->row();
        if (empty($clubPlayer)) {
            return ['code' => -1, 'msg' => '您已不在公会中'];
        }

        $page = 1 < $param['page'] ? $param['page'] : 1;

        $data   = $this->db()->where('club_id', $club->club_id)
            ->where('player_id', $clubPlayer->player_id)
            ->order_by('log_id', 'DESC')
            ->limit(self::Page_Num, self::Page_Num * ($page - 1))
            ->get(Club_Beans)
            ->result();
        $result = [];
        foreach ($data as $item) {
            $result[] = [
                'type'    => $item->log_type,
                'code'    => $item->player_code,
                'nick'    => $item->player_nick,
                'head'    => $item->player_head,
                'bean'    => $item->change_bean,
                'balance' => $item->after_bean,
                'time'    => date('m-d H:i', strtotime($item->created_at))
            ];
        }

        return ['code' => 0, 'data' => $result, 'msg' => '获取成功'];
    }

    // 制作邀请函
    public function inviteData($param)
    {
        $club = $this->db()->where('club_no', $param['club_no'])->get(Clubs)->row();
        if (empty($club)) {
            return ['code' => -1, 'msg' => '公会不存在'];
        }

        $numPlayer = $this->db()->where('club_id', $club->club_id)
            ->where('player_status', self::Player_Join)
            ->count_all_results(Club_Players);
        if ($numPlayer >= self::Max_Players) {
            return ['code' => -1, 'msg' => '公会成员已达上限'];
        }

        $is_join = $this->db()->where('club_id', $club->club_id)
            ->where('player_id', $param['aid'])
            ->where('player_status', self::Player_Join)
            ->get(Club_Players)
            ->num_rows();

        $data = [
            'club_no'     => $club->club_no,
            'club_name'   => $club->club_name,
            'invite_nick' => $club->admin_nick,
            'is_join'     => $is_join ? 1 : 0
        ];

        return ['code' => 0, 'data' => $data, 'msg' => '成功'];
    }

    // 获取消耗设置
    public function getConsume($club_no)
    {
        $result  = [
            'winner1' => 0,
            'winner2' => 0,
            'winner3' => 0,
        ];

        $consume = $this->db()->select('winner1,winner2,winner3')->where('club_no', $club_no)->get(Club_ConsumeSets)->row();

        if (empty($consume)) {
            return $result;
        }

        $result['winner1'] = $consume->winner1;
        $result['winner2'] = $consume->winner2;
        $result['winner3'] = $consume->winner3;

        return $result;
    }

    public function setConsume($param)
    {
        $consume = $this->db()->select('data_id')->where('club_no', $param['club_no'])->get(Club_ConsumeSets)->row();

        $dt = date('Y-m-d H:i:s');

        $data = [
            'winner1'    => $param['winner1'],
            'winner2'    => $param['winner2'],
            'winner3'    => $param['winner3'],
            'updated_at' => $dt,
        ];

        if (empty($consume)) {
            $club = $this->db()->select('club_id')->where('club_no', $param['club_no'])->get(Clubs)->row();
            $data['club_id']    = empty($club) ? 0 : $club->club_id;
            $data['club_no']    = $param['club_no'];
            $data['created_at'] = $dt;
            $this->db()->insert(Club_ConsumeSets, $data);
        } else {
            $this->db()->where('data_id', $consume->data_id)->update(Club_ConsumeSets, $data);
        }

        return ['code'=>0, 'msg'=>'操作成功'];
    }

    // 申请加入
    public function applyJoin($param)
    {
        // 1st club is exist
        $club = $this->db()->where('club_no', $param['club_no'])->get(Clubs)->row();
        if (empty($club)) {
            return ['code' => -1, 'msg' => '公会不存在'];
        }
        $user = $param['user'];

        // 2nd cur-user is creator
        if ($club->admin_id == $user['aid']) {
            return ['code' => -1, 'msg' => '您是会长，已在公会中'];
        }

        // 3nd cur-user is in
        $row = $this->db()->where('club_id', $club->club_id)
            ->where('player_id', $user['aid'])
            ->where('player_status', self::Player_Join)
            ->get(Club_Players)
            ->row();
        if (!empty($row)) {
            return ['code' => -1, 'msg' => '您已在公会中'];
        }

        // 4th cur-user is applied
        $row = $this->db()->where('club_id', $club->club_id)
            ->where('player_id', $user['aid'])
            ->where('apply_status', self::Apply_Wait)
            ->get(Club_Applies)
            ->row();
        if (!empty($row)) {
            return ['code' => -1, 'msg' => '申请成功，等待会长审核'];
        }

        // 5th club is over person
        $numPlayer = $this->db()->where('club_id', $club->club_id)
            ->where('player_status', self::Player_Join)
            ->count_all_results(Club_Players);
        if ($numPlayer >= self::Max_Players) {
            return ['code' => -1, 'msg' => '公会成员数已达上限'];
        }

        // 6th do insert
        $ts = date('Y-m-d H:i:s');

        $data = [
            'club_id'      => $club->club_id,
            'player_id'    => $user['aid'],
            'player_code'  => $user['code'],
            'player_head'  => $user['head'],
            'player_nick'  => $user['nick'],
            'apply_status' => self::Apply_Wait,
            'created_at'   => $ts,
            'updated_at'   => $ts,
        ];
        $this->db()->insert(Club_Applies, $data);

        return ['code' => 0, 'msg' => '申请成功，等待会长审核'];
    }

    // 同意|拒绝加入
    public function dealJoin($param)
    {
        $club = $this->db()->where('club_no', $param['club_no'])->where('admin_id', $param['user']['aid'])->get(Clubs)->row();
        if (empty($club)) {
            return ['code' => -1, 'msg' => '公会不存在'];
        }

        $apply = $this->db()->where('club_id', $club->club_id)
            ->where('player_code', $param['code'])
            ->where('apply_status', self::Apply_Wait)
            ->get(Club_Applies)->row();

        if (empty($apply)) {
            return ['code' => -1, 'msg' => '申请不存在'];
        }

        $ts     = date('Y-m-d H:i:s');
        $upData = [
            'updated_at' => $ts,
        ];

        $this->db()->trans_start();

        if ('agree' == $param['action']) {
            $numPlayer = $this->db()->where('club_id', $club->club_id)
                ->where('player_status', self::Player_Join)
                ->count_all_results(Club_Players);

            if ($numPlayer >= self::Max_Players) {
                return ['code' => -1, 'msg' => '成员数已达上限'];
            }

            $clubPlayer = $this->db()->where('club_id', $club->club_id)->where('player_id', $apply->player_id)->get(Club_Players)->row();
            if (empty($clubPlayer)) {
                $data = [
                    'club_id'       => $club->club_id,
                    'player_id'     => $apply->player_id,
                    'player_code'   => $apply->player_code,
                    'player_head'   => $apply->player_head,
                    'player_nick'   => $apply->player_nick,
                    'player_bean'   => 0,
                    'player_status' => self::Player_Join
                ];
                $this->db()->insert(Club_Players, $data);
            } else {
                $data = [
                    'player_status' => self::Player_Join,
                    'updated_at'    => $ts,
                ];
                $this->db()->where('data_id', $clubPlayer->data_id)->update(Club_Players, $data);
            }

            $data = [
                'log_type'    => self::Club_Join,
                'club_id'     => $club->club_id,
                'player_id'   => $apply->player_id,
                'player_code' => $apply->player_code,
                'player_head' => $apply->player_head,
                'player_nick' => $apply->player_nick,
                'player_bean' => empty($clubPlayer) ? 0 : $clubPlayer->player_bean,
                'club_name'   => $club->club_name,
                'created_at'  => $ts,
                'updated_at'  => $ts,
            ];
            $this->db()->insert(Club_Logs, $data);

            $upData['apply_status'] = self::Apply_Agree;
        } elseif ('refuse' == $param['action']) {
            $upData['apply_status'] = self::Apply_Refuse;
        } else {
            return ['code' => -1, 'msg' => '参数错误'];
        }

        $this->db()->where('data_id', $apply->data_id)->update(Club_Applies, $upData);

        $this->db()->trans_complete();

        return ['code' => 0, 'msg' => '操作成功', 'status' => $upData['apply_status']];
    }

    // 赠豆（任何时候均可）|减豆（需不在游戏中）
    public function dealBean($param)
    {
        $club = $this->db()->where('club_no', $param['club_no'])->where('admin_id', $param['user']['aid'])->get(Clubs)->row();
        if (empty($club)) {
            return ['code' => -1, 'msg' => '公会不存在'];
        }
        $clubPlayer = $this->db()->where('club_id', $club->club_id)
            ->where('player_code', $param['code'])
            ->where('player_status', self::Player_Join)
            ->get(Club_Players)
            ->row();
        if (empty($clubPlayer)) {
            return ['code' => -1, 'msg' => '成员不存在'];
        }

        $ts = date('Y-m-d H:i:s');

        $this->db()->trans_start();

        if ('send' == $param['action']) {
            $this->db()->where('data_id', $clubPlayer->data_id)->update(Club_Players, [
                'player_bean' => $clubPlayer->player_bean + $param['bean']
            ]);

            $data = [
                'log_type'    => self::Bean_Buy,
                'content'     => sprintf('赠送%d豆', $param['bean']),
                'club_id'     => $club->club_id,
                'player_id'   => $clubPlayer->player_id,
                'player_code' => $clubPlayer->player_code,
                'player_head' => $clubPlayer->player_head,
                'player_nick' => $clubPlayer->player_nick,
                'before_bean' => $clubPlayer->player_bean,
                'change_bean' => $param['bean'],
                'after_bean'  => $clubPlayer->player_bean + $param['bean'],
                'created_at'  => $ts,
                'updated_at'  => $ts,
            ];
            $this->db()->insert(Club_Beans, $data);
        } elseif ('out' == $param['action']) {
            // 是否在游戏中
//            if ($clubPlayer->is_gaming) {
//                return ['code' => -1, 'msg' => '成员游戏中不可操作'];
//            }
            $balance = $clubPlayer->player_bean - $param['bean'];
            if ($balance < 0) {
                return ['code' => -1, 'msg' => '余豆不足'];
            }
            $this->db()->where('data_id', $clubPlayer->data_id)->update(Club_Players, [
                'player_bean' => $balance
            ]);

            $data = [
                'log_type'    => self::Bean_Cash,
                'content'     => sprintf('兑换%d豆', $param['bean']),
                'club_id'     => $club->club_id,
                'player_id'   => $clubPlayer->player_id,
                'player_code' => $clubPlayer->player_code,
                'player_head' => $clubPlayer->player_head,
                'player_nick' => $clubPlayer->player_nick,
                'before_bean' => $clubPlayer->player_bean,
                'change_bean' => -$param['bean'],
                'after_bean'  => $balance,
                'created_at'  => $ts,
                'updated_at'  => $ts,
            ];
            $this->db()->insert(Club_Beans, $data);
        } else {
            return ['code' => -1, 'msg' => '参数错误'];
        }

        $this->db()->trans_complete();

        return ['code' => 0, 'msg' => '操作成功'];
    }

    // 会长踢出成员
    public function kick($param)
    {
        $user = $param['user'];

        $club = $this->db()->where('club_no', $param['club_no'])->where('admin_id', $user['aid'])->get(Clubs)->row();
        if (empty($club)) {
            return ['code' => -1, 'msg' => '公会不存在'];
        }

        if ($club->admin_code == $param['code']) {
            return ['code' => -1, 'msg' => '会长不可踢出自己'];
        }

        $clubPlayer = $this->db()->where('club_id', $club->club_id)
            ->where('player_code', $param['code'])
            ->where('player_status', self::Player_Join)
            ->get(Club_Players)
            ->row();

        if (empty($clubPlayer)) {
            return ['code' => -1, 'msg' => '成员不存在'];
        }

        $ts = date('Y-m-d H:i:s');

        $data = [
            'player_status' => self::Player_Kick,
            'updated_at'    => $ts,
        ];
        $this->db()->where('data_id', $clubPlayer->data_id)->update(Club_Players, $data);

        $data = [
            'log_type'    => self::Club_Kick,
            'club_id'     => $club->club_id,
            'player_id'   => $clubPlayer->player_id,
            'player_code' => $clubPlayer->player_code,
            'player_head' => $clubPlayer->player_head,
            'player_nick' => $clubPlayer->player_nick,
            'player_bean' => $clubPlayer->player_bean,
            'club_name'   => $club->club_name,
            'created_at'  => $ts,
            'updated_at'  => $ts,
        ];

        $this->db()->insert(Club_Logs, $data);

        return ['code' => 0, 'msg' => '操作成功'];
    }

    // 成员主动退出
    public function quit($param)
    {
        $user = $param['user'];

        $club = $this->db()->where('club_no', $param['club_no'])->get(Clubs)->row();
        if (empty($club)) {
            return ['code' => -1, 'msg' => '公会不存在'];
        }

        if ($club->admin_id == $user['aid']) {
            return ['code' => -1, 'msg' => '会长不可退出'];
        }

        $clubPlayer = $this->db()->where('club_id', $club->club_id)
            ->where('player_id', $user['aid'])
            ->where('player_status', self::Player_Join)
            ->get(Club_Players)
            ->row();

        if (empty($clubPlayer)) {
            return ['code' => 0, 'msg' => '成功'];
        }

        $ts = date('Y-m-d H:i:s');

        $data = [
            'player_status' => self::Player_Quit,
            'updated_at'    => $ts,
        ];
        $this->db()->where('data_id', $clubPlayer->data_id)->update(Club_Players, $data);

        $data = [
            'log_type'    => self::Club_Quit,
            'club_id'     => $club->club_id,
            'player_id'   => $clubPlayer->player_id,
            'player_code' => $clubPlayer->player_code,
            'player_head' => $clubPlayer->player_head,
            'player_nick' => $clubPlayer->player_nick,
            'player_bean' => $clubPlayer->player_bean,
            'club_name'   => $club->club_name,
            'created_at'  => $ts,
            'updated_at'  => $ts,
        ];

        $this->db()->insert(Club_Logs, $data);

        return ['code' => 0, 'msg' => '操作成功'];
    }

    public function user($open_id)
    {
        if (empty($open_id)) {
            return [];
        }
        $user = $this->db()->select('account_id as aid, nickname as nick, user_code as code, headimgurl as head, phone, secret')
            ->where('open_id', $open_id)
            ->where('is_delete', 0)
            ->get(WX_Account)
            ->row_array();
        return $user;
    }
}
