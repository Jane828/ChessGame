<?php

include_once dirname(__DIR__) . '/public_models.php';        //加载数据库操作类
include_once dirname(__DIR__) . "/../libraries/Redis.php";

class Manage_Model extends Public_Models {
    public function __construct() {
        header('Content-Type: text/html; charset=utf-8');
        parent::__construct();
    }


    /*
        开启/关闭管理功能
    */
    public function setManageSwitch($arrData) {
        $result    = array();
        $timestamp = time();

        if (!isset($arrData['is_on']) || $arrData['is_on'] === "") {
            log_message('error', "function(setManageSwitch):lack of is_on" . " in file" . __FILE__ . " on Line " . __LINE__);
            return $this->missingPrameterArr("is_on");
        }
        if (!isset($arrData['account_id']) || $arrData['account_id'] === "") {
            log_message('error', "function(setManageSwitch):lack of account_id" . " in file" . __FILE__ . " on Line " . __LINE__);
            return $this->missingPrameterArr("account_id");
        }

        $is_on      = $arrData['is_on'];
        $account_id = $arrData['account_id'];

        $dealerDB = Game_CONST::DBConst_Name;

        $update_array['update_time']  = $timestamp;
        $update_array['update_appid'] = "aid_" . $account_id;
        $update_array['is_manage_on'] = $is_on;
        $update_query                 = $this->updateFunc($dealerDB, "account_id", $account_id, WX_Account, $update_array);

        //扣除房卡
        if ($is_on) {
            //将房卡添加到自己账户
            $updateTicket_str   = 'update_time=' . $timestamp . ',update_appid="aid_' . $account_id . '",ticket_count=ticket_count-' . Game_CONST::Manage_cost;
            $updateTicket_where = 'account_id=' . $account_id;
            $updateTicket_query = $this->changeNodeValue($dealerDB, Room_Ticket, $updateTicket_str, $updateTicket_where);
            //房卡流水账
            $journal_ary['journal_type'] = Game_CONST::JournalType_Disburse;
            $journal_ary['account_id']   = $account_id;
            $journal_ary['object_type']  = Game_CONST::ObjectType_Manage;
            $journal_ary['object_id']    = -1;
            $journal_ary['ticket_count'] = 10;
            $journal_ary['extra']        = "";
            $this->updateRoomTicketJournal($journal_ary, $dealerDB);

        }

        $result['is_on'] = $is_on;
        return array('result' => OPT_CONST::SUCCESS, 'data' => $result, 'result_message' => "设置成功");
    }

    public function getManageSwitch($arrData) {
        $result    = array();
        $timestamp = time();

        if (!isset($arrData['account_id']) || $arrData['account_id'] === "") {
            log_message('error', "function(setManageSwitch):lack of account_id" . " in file" . __FILE__ . " on Line " . __LINE__);
            return $this->missingPrameterArr("account_id");
        }

        $account_id      = $arrData['account_id'];
        $dealerDB        = Game_CONST::DBConst_Name;
        $result['is_on'] = 0;

        $where = "account_id = " . $account_id . " and is_delete = 0";
        $sql   = "select is_manage_on from " . WX_Account . " where " . $where;
        $query = $this->getDataBySql($dealerDB, 1, $sql);
        if (DB_CONST::DATA_NONEXISTENT != $query) {
            $result['is_on'] = $query["is_manage_on"];
        }

        return array('result' => OPT_CONST::SUCCESS, 'data' => $result, 'result_message' => "管理开关状态");
    }

    /*
		获取邀请函内容
	*/
    public function getInviteData($arrData) {
        $timestamp = time();
        $result    = array();

        if (!isset($arrData['code']) || $arrData['code'] == G_CONST::EMPTY_STRING) {
            log_message('error', "function(getInviteData):lack of code" . " in file" . __FILE__ . " on Line " . __LINE__);
            return $this->missingPrameterArr("code");
        }
        if (!isset($arrData['account_id']) || $arrData['account_id'] == G_CONST::EMPTY_STRING) {
            log_message('error', "function(getInviteData):lack of account_id" . " in file" . __FILE__ . " on Line " . __LINE__);
            return $this->missingPrameterArr("account_id");
        }
        $dealerDB = Game_CONST::DBConst_Name;

        $code       = $arrData['code'];
        $account_id = $arrData['account_id'];


        //用户邀请用户信息
        $account_where = 'user_code="' . $code . '"';
        $account_sql   = 'select account_id,nickname,headimgurl,phone from ' . WX_Account . ' where ' . $account_where . '';
        $account_query = $this->getDataBySql($dealerDB, 1, $account_sql);
        if (DB_CONST::DATA_NONEXISTENT == $account_query) {
            log_message('error', "function(getUserInfo):account not exist:" . $invite_aid . " in file" . __FILE__ . " on Line " . __LINE__);
            return array('result' => OPT_CONST::FAILED, 'data' => $result, 'result_message' => "邀请用户不存在");
        } else {
            $invite_aid = $account_query['account_id'];
            $nickname   = $account_query['nickname'];
            $headimgurl = $account_query['headimgurl'];
        }

        $result['nickname']   = $nickname;
        $result['headimgurl'] = $headimgurl;

        //是否本人
        $is_owner = ($invite_aid == $account_id) ? 1 : 0;

        //获取用户是否加入公会
        $invite_status = 0;
        $join_where    = 'account_id=' . $account_id . ' and manager_id=' . $invite_aid . ' and is_delete=0';
        $join_sql      = 'select member_id,status from ' . Manage_Member . ' where ' . $join_where . ' limit 1';
        $join_query    = $this->getDataBySql($dealerDB, 1, $join_sql);
        if (DB_CONST::DATA_NONEXISTENT != $join_query) {
            if ($join_query['status'] == 0) {
                $invite_status = 1;
            } else if ($join_query['status'] == 1) {
                $invite_status = 2;
            } else {
                $invite_status = 0;
            }

        }

        $result['is_owner']      = $is_owner;
        $result['invite_status'] = $invite_status;

        return array('result' => OPT_CONST::SUCCESS, 'data' => $result, 'result_message' => "获取邀请函内容");
    }


    /*
        好友申请
    */
    public function joinGroup($arrData) {
        $timestamp = time();
        $result    = array();

        if (!isset($arrData['user_code']) || $arrData['user_code'] == G_CONST::EMPTY_STRING) {
            log_message('error', "function(joinGroup):lack of user_code" . " in file" . __FILE__ . " on Line " . __LINE__);
            return $this->missingPrameterArr("user_code");
        }
        if (!isset($arrData['account_id']) || $arrData['account_id'] == G_CONST::EMPTY_STRING) {
            log_message('error', "function(joinGroup):lack of account_id" . " in file" . __FILE__ . " on Line " . __LINE__);
            return $this->missingPrameterArr("account_id");
        }
        $dealerDB = Game_CONST::DBConst_Name;

        $user_code  = $arrData['user_code'];
        $account_id = $arrData['account_id'];
        if ($user_code == $account_id + G_CONST::USRECODE_ACCOUNTID_SUB) {
            return array('result' => OPT_CONST::FAILED, 'data' => $result, 'result_message' => "不能添加自己为好友");
        }

        //获取邀请函管理员信息
        $where = 'user_code="' . $user_code . '"  and is_delete=0';
        $sql   = 'select account_id from ' . WX_Account . ' where ' . $where . ' limit 1';
        $query = $this->getDataBySql($dealerDB, 1, $sql);
        if (DB_CONST::DATA_NONEXISTENT == $query) {
            return array('result' => OPT_CONST::FAILED, 'data' => $result, 'result_message' => "邀请用户不存在");
        }
        $manager_id = $query['account_id'];


        //获取用户是否加入公会
        $join_where = 'account_id=' . $account_id . ' and manager_id=' . $manager_id;
        $join_sql   = 'select member_id,status,is_delete from ' . Manage_Member . ' where ' . $join_where . ' limit 1';
        $join_query = $this->getDataBySql($dealerDB, 1, $join_sql);
        if (DB_CONST::DATA_NONEXISTENT != $join_query) {
            if (1 == $join_query['status'] && $join_query['is_delete'] == 0) {
                $result['status'] = 1;
                return array('result' => OPT_CONST::SUCCESS, 'data' => $result, 'result_message' => "已加入该群组");
            } else if (3 == $join_query['status'] && $join_query['is_delete'] == 0) {
                $result['status'] = 3;
                return array('result' => OPT_CONST::SUCCESS, 'data' => $result, 'result_message' => "对方禁止加为好友");
            } else {
                $member_id          = $join_query['member_id'];
                $updateMember_str   = 'update_time=' . $timestamp . ',update_appid="aid_' . $account_id . '",status=0,is_delete=0';
                $updateMember_where = 'member_id=' . $member_id;
                $updateMember_query = $this->changeNodeValue($dealerDB, Manage_Member, $updateMember_str, $updateMember_where);
                $result['status']   = 0;
                return array('result' => OPT_CONST::SUCCESS, 'data' => $result, 'result_message' => "等待对方确认");
            }
        } else {
            $insert_array['create_time']  = $timestamp;
            $insert_array['create_appid'] = "aid_" . $account_id;
            $insert_array['update_time']  = $timestamp;
            $insert_array['update_appid'] = "aid_" . $account_id;
            $insert_array['is_delete']    = 0;
            $insert_array['account_id']   = $account_id;
            $insert_array['manager_id']   = $manager_id;
            $insert_array['user_code']    = $user_code;
            $insert_array['status']       = 0;
            $member_id                    = $this->getInsertID($dealerDB, Manage_Member, $insert_array);

            $result['status'] = 0;
            return array('result' => OPT_CONST::SUCCESS, 'data' => $result, 'result_message' => "等待管理员确认");
        }
    }


    /*
        查找成员
    */
    public function searchMember($arrData) {
        $result    = array();
        $timestamp = time();

        if (!isset($arrData['manager_id']) || $arrData['manager_id'] === "") {
            log_message('error', "function(searchMember):lack of manager_id" . " in file" . __FILE__ . " on Line " . __LINE__);
            return $this->missingPrameterArr("manager_id");
        }
        if (!isset($arrData['page'])) {
            log_message('error', "function(searchMember):lack of page" . " in file" . __FILE__ . " on Line " . __LINE__);
            return $this->missingPrameterArr("page");
        }

        $manager_id = $arrData['manager_id'];
        $nickname   = isset($arrData['nickname']) ? $arrData['nickname'] : '';
        $dealerDB   = Game_CONST::DBConst_Name;
        $status     = $arrData['status'];

        $page = $arrData['page'];
        if ($page == 0 || $page == "") {
            $page = 1;
        }
        $limit     = 20;
        $offset    = ($page - 1) * $limit;
        $sum_page  = 1;
        $sum_count = 0;
        $limit2    = 20;

        $where = WX_Account . '.account_id =' . Manage_Member . '.account_id and manager_id =' . $manager_id . ' and status = ' . $status . ' and ' . Manage_Member . '.is_delete=0 ';
        if ($nickname) {
            $where .= ' and nickname like "%' . $nickname . '%" ';
        }

        $sql        = 'select ' . WX_Account . '.user_code,nickname,headimgurl,capital,' . Manage_Member . '.create_time,member_id,status,aliases  from ' . WX_Account . ',' . Manage_Member . ' where ' . $where . ' and capital >= "A" order by `capital`' . ' limit ' . $offset . ',' . $limit;
        $list_query = $this->getDataBySql($dealerDB, 0, $sql);

        if ($list_query != DB_CONST::DATA_NONEXISTENT) {
            $count_sql   = 'select count(1) as count from ' . WX_Account . ',' . Manage_Member . ' where ' . $where;
            $count_query = $this->getDataBySql($dealerDB, 1, $count_sql);
            $sum_page    = ceil($count_query['count'] / $limit);
            $sum_count   = $count_query['count'];

            foreach ($list_query as $list_item) {
                $array['nickname']               = $list_item['nickname'];
                $array['user_code']              = $list_item['user_code'];
                $array['avatar_url']             = $list_item['headimgurl'];
                $array['member_id']              = $list_item['member_id'];
                $array['aliases']                = $list_item['aliases'];
                $array['status']                 = $list_item['status'];
                $result[$list_item['capital']][] = $array;
            }
            $limit2 -= count($list_query);
        }
        if ($limit2 > 0) {
            if ($offset > 0) {
                $count_sql   = 'select count(1) as count  from ' . WX_Account . ',' . Manage_Member . ' where ' . $where . ' and capital >= "A"';
                $count_query = $this->getDataBySql($dealerDB, 1, $count_sql);
                if ($offset > $count_query['count']) {
                    $offset -= $count_query['count'];
                }
            }

            $sql         = 'select ' . WX_Account . '.user_code,nickname,headimgurl,' . Manage_Member . '.create_time,member_id,status, aliases  from ' . WX_Account . ',' . Manage_Member . ' where ' . $where . ' and capital < "A" order by `capital`' . ' desc limit ' . $offset . ',' . $limit2;
            $other_query = $this->getDataBySql($dealerDB, 0, $sql);

            if ($other_query != DB_CONST::DATA_NONEXISTENT) {
                foreach ($other_query as $list_item) {
                    $array['nickname']   = $list_item['nickname'];
                    $array['user_code']  = $list_item['user_code'];
                    $array['avatar_url'] = $list_item['headimgurl'];
                    $array['member_id']  = $list_item['member_id'];
                    $array['aliases']    = $list_item['aliases'];
                    $array['status']     = $list_item['status'];
                    $result['other'][]   = $array;
                }
            }
        }

        return array('result' => OPT_CONST::SUCCESS, 'data' => $result, 'page' => $page, 'sum_page' => $sum_page, 'sum_count' => $sum_count, 'result_message' => "查找成员");
    }


    /*
        管理员处理成员
    */
    public function dealMember($arrData) {
        $timestamp = time();
        $result    = array();

        if (!isset($arrData['member_id']) || $arrData['member_id'] == G_CONST::EMPTY_STRING) {
            log_message('error', "function(dealMember):lack of member_id" . " in file" . __FILE__ . " on Line " . __LINE__);
            return $this->missingPrameterArr("member_id");
        }
        if (!isset($arrData['manager_id']) || $arrData['manager_id'] == G_CONST::EMPTY_STRING) {
            log_message('error', "function(dealMember):lack of manager_id" . " in file" . __FILE__ . " on Line " . __LINE__);
            return $this->missingPrameterArr("manager_id");
        }
        if (!isset($arrData['type']) || $arrData['type'] == G_CONST::EMPTY_STRING) {
            log_message('error', "function(dealMember):lack of type" . " in file" . __FILE__ . " on Line " . __LINE__);
            return $this->missingPrameterArr("type");
        }
        $dealerDB = Game_CONST::DBConst_Name;

        $member_id  = $arrData['member_id'];
        $manager_id = $arrData['manager_id'];

        $type = $arrData['type'];

        //获取用户关系
        $join_where = 'member_id=' . $member_id . ' and is_delete=0';
        $join_sql   = 'select manager_id,account_id,status from ' . Manage_Member . ' where ' . $join_where . ' limit 1';
        $join_query = $this->getDataBySql($dealerDB, 1, $join_sql);
        if (DB_CONST::DATA_NONEXISTENT != $join_query) {

            $updateMember_str = 'update_time=' . $timestamp . ',update_appid="aid_' . $manager_id . '",status=' . $type;
            if ($type == 2) {
                $updateMember_str .= ",is_delete=1";
            }

            $updateMember_where = 'member_id=' . $member_id;

            if ($join_query["status"] != 3 && $type == 1) {
                $agree_where = 'account_id=' . $manager_id . ' and manager_id=' . $join_query["account_id"];
                $agree_sql   = 'select member_id,status,is_delete from ' . Manage_Member . ' where ' . $agree_where . ' limit 1';
                $agree_query = $this->getDataBySql($dealerDB, 1, $agree_sql);
                if (DB_CONST::DATA_NONEXISTENT != $agree_query) {
                    $updateMember_where = 'member_id in (' . $member_id . ", " . $agree_query["member_id"] . ")";
                } else {
                    $insert_array['create_time']  = $timestamp;
                    $insert_array['create_appid'] = "aid_" . $member_id;
                    $insert_array['update_time']  = $timestamp;
                    $insert_array['update_appid'] = "aid_" . $member_id;
                    $insert_array['is_delete']    = 0;
                    $insert_array['account_id']   = $manager_id;
                    $insert_array['manager_id']   = $join_query["account_id"];
                    $insert_array['user_code']    = $join_query["account_id"] + G_CONST::USRECODE_ACCOUNTID_SUB;
                    $insert_array['status']       = 0;
                    $member_id2                   = $this->getInsertID($dealerDB, Manage_Member, $insert_array);
                    $updateMember_where           = 'member_id in (' . $member_id . ", " . $member_id2 . ")";
                }
            }

            $updateMember_str   .= ",is_delete=0";
            $updateMember_query = $this->changeNodeValue($dealerDB, Manage_Member, $updateMember_str, $updateMember_where);
            $result['status']   = $type;
            return array('result' => OPT_CONST::SUCCESS, 'data' => $result, 'result_message' => "操作成功");
        } else {
            return array('result' => OPT_CONST::FAILED, 'data' => $result, 'result_message' => "该成员不存在");
        }
    }

    /**
     * 函数描述：解除关系
     * @param $arrData
     * @return array
     * author 黄欣仕
     * date 2019/2/22
     */
    public function deleteMember($arrData) {
        $result = array();

        if (!isset($arrData['member_id']) || $arrData['member_id'] == G_CONST::EMPTY_STRING) {
            log_message('error', "function(dealMember):lack of member_id" . " in file" . __FILE__ . " on Line " . __LINE__);
            return $this->missingPrameterArr("member_id");
        }
        if (!isset($arrData['manager_id']) || $arrData['manager_id'] == G_CONST::EMPTY_STRING) {
            log_message('error', "function(dealMember):lack of manager_id" . " in file" . __FILE__ . " on Line " . __LINE__);
            return $this->missingPrameterArr("manager_id");
        }
        $dealerDB = Game_CONST::DBConst_Name;

        $member_id  = $arrData['member_id'];
        $manager_id = $arrData['manager_id'];
        //获取用户关系
        $join_where = 'member_id=' . $member_id . ' and is_delete=0';
        $join_sql   = 'select manager_id,account_id,status from ' . Manage_Member . ' where ' . $join_where . ' limit 1';
        $join_query = $this->getDataBySql($dealerDB, 1, $join_sql);
        if (DB_CONST::DATA_NONEXISTENT != $join_query) {
            $updateMember_str   = "is_delete = 1";
            $updateMember_where = "member_id = " . $member_id . " or account_id = " . $manager_id . " and manager_id = " . $join_query["account_id"];
            $updateMember_query = $this->changeNodeValue($dealerDB, Manage_Member, $updateMember_str, $updateMember_where);
            return array('result' => OPT_CONST::SUCCESS, 'data' => $result, 'result_message' => "关系解除成功");
        } else {
            return array('result' => OPT_CONST::FAILED, 'data' => $result, 'result_message' => "不存在该关系");
        }

    }

    public function getManagerUser($arrData) {
        $result = array();

        if (!isset($arrData['account_id']) || $arrData['account_id'] == G_CONST::EMPTY_STRING) {
            log_message('error', "function(dealMember):lack of account_id" . " in file" . __FILE__ . " on Line " . __LINE__);
            return $this->missingPrameterArr("account_id");
        }
        if (!isset($arrData['page'])) {
            log_message('error', "function(searchMember):lack of page" . " in file" . __FILE__ . " on Line " . __LINE__);
            return $this->missingPrameterArr("page");
        }

        $dealerDB   = Game_CONST::DBConst_Name;
        $account_id = $arrData['account_id'];
        $page       = $arrData['page'];
        if ($page == 0 || $page == "") {
            $page = 1;
        }

        $limit     = 10;
        $offset    = ($page - 1) * $limit;
        $sum_page  = 1;
        $sum_count = 0;

        $where       = Manage_Member . '.account_id = ' . $account_id . " and " . Manage_Member . ".is_delete = 0 and manager_id = " . WX_Account . ".account_id and " . Manage_Member . ".status = " . G_CONST::MEMBER_RELATION_FRIND;
        $atten_sql   = "select nickname, headimgurl, member_id, manager_id, attention from " . WX_Account . "," . Manage_Member . " where " . $where . " order by manager_id limit " . $offset . ',' . $limit;
        log_message("error", $atten_sql);
        $atten_query = $this->getDataBySql($dealerDB, 0, $atten_sql);
        if (DB_CONST::DATA_NONEXISTENT != $atten_query) {
            $count_sql   = 'select count(1) as count from ' . WX_Account . ',' . Manage_Member . ' where ' . $where;
            $count_query = $this->getDataBySql($dealerDB, 1, $count_sql);
            $sum_page    = ceil($count_query['count'] / $limit);
            $sum_count   = $count_query['count'];

            foreach ($atten_query as $atten_item) {
                $tmp_data["nickname"]   = $atten_item["nickname"];
                $tmp_data["head_img"]   = $atten_item["headimgurl"];
                $tmp_data["member_id"]  = $atten_item["member_id"];
                $tmp_data["manager_id"] = $atten_item["manager_id"];
                $tmp_data["attention"]  = $atten_item["attention"];
                $result[]               = $tmp_data;
            }
        }

        return array('result' => OPT_CONST::SUCCESS, 'data' => $result, 'page' => $page, 'sum_page' => $sum_page, 'sum_count' => $sum_count, 'result_message' => "查找成员");
    }

    public function setManagerUser($arrData) {
        $result = array();

        if (!isset($arrData['account_id']) || $arrData['account_id'] == G_CONST::EMPTY_STRING) {
            log_message('error', "function(dealMember):lack of account_id" . " in file" . __FILE__ . " on Line " . __LINE__);
            return $this->missingPrameterArr("account_id");
        }
        if (!isset($arrData['manager_id']) || $arrData['manager_id'] == G_CONST::EMPTY_STRING) {
            log_message('error', "function(dealMember):lack of manager_id" . " in file" . __FILE__ . " on Line " . __LINE__);
            return $this->missingPrameterArr("manager_id");
        }
        if (!isset($arrData['attention'])) {
            log_message('error', "function(dealMember):lack of attention" . " in file" . __FILE__ . " on Line " . __LINE__);
            return $this->missingPrameterArr("attention");
        }

        $dealerDB   = Game_CONST::DBConst_Name;
        $account_id = $arrData['account_id'];
        $manager_id = $arrData['manager_id'];
        $attention  = $arrData['attention'];
        if ($attention != 0 && $attention != 1) {
            $result["attention"] = $attention;
            return array('result' => OPT_CONST::FAILED, 'data' => $result, 'result_message' => "非法参数");
        }

        $update_str   = "attention = " . $attention;
        $update_where = "manager_id = " . $manager_id . " and account_id = " . $account_id . " and is_delete = 0";
        $update_query = $this->changeNodeValue($dealerDB, Manage_Member, $update_str, $update_where);
        return array('result' => OPT_CONST::SUCCESS, 'data' => $result, 'result_message' => "设置成功");
    }

    public function getRoomList($arrData) {
        $result = array();

        if (!isset($arrData['account_id']) || $arrData['account_id'] == G_CONST::EMPTY_STRING) {
            log_message('error', "function(dealMember):lack of account_id" . " in file" . __FILE__ . " on Line " . __LINE__);
            return $this->missingPrameterArr("account_id");
        }

        if (!isset($arrData['game_category'])) {
            log_message('error', "function(dealMember):lack of game_category" . " in file" . __FILE__ . " on Line " . __LINE__);
            return $this->missingPrameterArr("game_category");
        }

        $dealerDB   = Game_CONST::DBConst_Name;
        $account_id = $arrData['account_id'];
        $limit      = 10;

        $gettype = function ($v1, $v2) {
            $v1 = $this->config->item($v1, "game")["type"] ? $this->config->item($v1, "game")["type"] : $v1;
            return $v1 . "," . $this->config->item($v2, "game")["type"];
        };
        switch ($arrData['game_category']) {
            case "sangong":
                $room_db   = $this->config->item("sangong", "game")["table"];
                $game_type = array_reduce(array("nsangong"), $gettype, "sangong");
                break;
            case "flower":
                $room_db   = $this->config->item("flower", "game")["table"];
                $game_type = array_reduce(array("tflower", "bflower"), $gettype, "flower");
                break;
            case "bull":
                $room_db   = $this->config->item("bull", "game")["table"];
                $game_type = array_reduce(array("nbull", "tbull", "fbull", "lbull"), $gettype, "bull");
                break;
            default:
                return array('result' => OPT_CONST::FAILED, 'data' => $result, 'result_message' => "请选择正确的游戏类型");
        }

        $manager_sql   = "select manager_id from " . Manage_Member . " where account_id = " . $account_id . " and attention = 1 and is_delete = 0 and status=" . G_CONST::MEMBER_RELATION_FRIND;
        $manager_query = $this->getDataBySql($dealerDB, 0, $manager_sql);

        $mid_arr = array();
        foreach ($manager_query as $manege) {
            $mid_arr[] = $manege["manager_id"];
        }
        $mid_arr[] = $account_id;
        $mid_str   = implode(",", $mid_arr);

        $box_where = "  bi.status = 1 and bi.account_id in (" . $mid_str . ") and game_type in (" . $game_type . ") and bi.account_id = acc.account_id";
        $box_sql   = "select bi.box_id, bi.box_number, bi.box_name, bi.game_type, acc.nickname, acc.account_id from " . Box_Info . " as bi," . WX_Account . " as acc where " . $box_where . " order by bi.create_time desc " . " limit " . $limit;
        $box_query = $this->getDataBySql($dealerDB, 0, $box_sql);
        if (DB_CONST::DATA_NONEXISTENT != $box_query) {
            foreach ($box_query as $box_item) {
                $tmp_data["info_type"]  = 1;
                $tmp_data["box_number"] = $box_item["box_number"];
                $tmp_data["box_id"]     = $box_item["box_id"];
                $tmp_data["box_name"]   = $box_item["box_name"];
                $tmp_data["game_type"]  = $box_item["game_type"];
                $tmp_data["nickname"]   = $box_item["nickname"];
                $tmp_data["account_id"] = $box_item["account_id"];
                $result[]               = $tmp_data;
            }
            unset($tmp_data);
        }

        if (count($result) < $limit) {
            $time_stamp = time() - 86400;
            $room_where = " room.is_close = 0 and room.account_id in (" . $mid_str . ") and room.box_number= 0  and room.account_id = acc.account_id and room.create_time > " . $time_stamp;
            $room_sql   = "select acc.account_id, acc.nickname, room.game_type, room.room_id, room.room_number, room.room_status, room.create_time from " . $room_db . " as room," . WX_Account . " as acc where " . $room_where . " order by room.create_time desc " . " limit " . (10 - count($result));
            $room_query = $this->getDataBySql($dealerDB, 0, $room_sql);
            if (DB_CONST::DATA_NONEXISTENT != $room_query) {
                $redis      = Redis::getInstance();
                $prefix_arr = array(5 => 6, 13 => 9, 71 => 9, 9 => 9, 12 => 9, 36 => 36, 37 => 37);//TODO 待优化，socket 里的Redis_CONST.php key值命名规则统一
                foreach ($room_query as $room_item) {
                    $tmp_data["info_type"]   = 0;
                    $tmp_data["account_id"]  = $room_item["account_id"];
                    $tmp_data["nickname"]    = $room_item["nickname"];
                    $tmp_data["game_type"]   = $room_item["game_type"];
                    $tmp_data["room_number"] = $room_item["room_number"];
                    $tmp_data["room_id"]     = $room_item["room_id"];
                    $tmp_data["room_status"] = $room_item["room_status"];
                    $tmp_data["create_time"] = date("H:i:s", $room_item["create_time"]);
                    $prefix                  = isset($prefix_arr[$room_item["game_type"]]) ? $prefix_arr[$room_item["game_type"]] . ":" : "";

                    $redis_data = $redis->hgetall($prefix . "Room:" . $room_item["room_id"]);
                    if (count($redis_data) == 0) {
                        continue;
                    }

                    $tmp_data["total_num"]      = $redis_data["totalnum"];
                    $tmp_data["gnum"]           = $redis_data["gnum"];
                    $tmp_data["player_max_num"] = $redis_data["player_max_num"];

                    $player_data            = $redis->zrange($prefix . "RoomSeq:" . $room_item["room_id"], 0, -1);
                    $tmp_data["user_count"] = count($player_data);

                    $result[] = $tmp_data;
                }
            }
        }

        return array('result' => OPT_CONST::SUCCESS, 'data' => $result, 'result_message' => "查找房间");
    }

    public function GetRoomInfo($arrData) {
        $result = array();

        if (!isset($arrData['room_id']) || $arrData['room_id'] == G_CONST::EMPTY_STRING) {
            log_message('error', "function(dealMember):lack of room_id" . " in file" . __FILE__ . " on Line " . __LINE__);
            return $this->missingPrameterArr("room_id");
        }

        if (!isset($arrData['game_type']) || $arrData['game_type'] == G_CONST::EMPTY_STRING) {
            log_message('error', "function(dealMember):lack of game_type" . " in file" . __FILE__ . " on Line " . __LINE__);
            return $this->missingPrameterArr("game_type");
        }

        $dealerDB = Game_CONST::DBConst_Name;
        $room_id  = $arrData['room_id'];

        foreach ($this->config->config["game"] as $game_cfg) {
            if ($game_cfg["type"] == $arrData['game_type']) {
                $room_db = $game_cfg["table"];
                break;
            }
        }

        $where = " room_id = " . $room_id;
        $sql   = "select room_config from " . $room_db . " where " . $where;
        $query = $this->getDataBySql($dealerDB, 1, $sql);

        $result[] = json_decode($query["room_config"]);

        return array('result' => OPT_CONST::SUCCESS, 'data' => $result, 'result_message' => "房间信息");
    }

    public function SetAliases($arrData) {
        $result = array();
        if (!isset($arrData['account_id']) || $arrData['account_id'] == G_CONST::EMPTY_STRING) {
            log_message('error', "function(dealMember):lack of account_id" . " in file" . __FILE__ . " on Line " . __LINE__);
            return $this->missingPrameterArr("account_id");
        }

        if (!isset($arrData['member_id']) || $arrData['member_id'] == G_CONST::EMPTY_STRING) {
            log_message('error', "function(dealMember):lack of member_id" . " in file" . __FILE__ . " on Line " . __LINE__);
            return $this->missingPrameterArr("member_id");
        }

        if (!isset($arrData['aliases'])) {
            log_message('error', "function(dealMember):lack of aliases" . " in file" . __FILE__ . " on Line " . __LINE__);
            return $this->missingPrameterArr("aliases");
        }

        $dealerDB   = Game_CONST::DBConst_Name;
        $account_id = $arrData['account_id'];
        $member_id  = $arrData['member_id'];
        $aliases    = $arrData['aliases'];

        $update_where = "manager_id = " . $account_id . " and member_id = " . $member_id . " and is_delete = 0";
        $update_str   = 'aliases = "' . $aliases . '"';
        $update_query = $this->changeNodeValue($dealerDB, Manage_Member, $update_str, $update_where);
        return array('result' => OPT_CONST::SUCCESS, 'data' => $result, 'result_message' => "备注修改成功");
    }

    public function hasFriendRequest($arrData) {
        if (!isset($arrData['account_id']) || $arrData['account_id'] == G_CONST::EMPTY_STRING) {
            log_message('error', "function(dealMember):lack of account_id" . " in file" . __FILE__ . " on Line " . __LINE__);
            return $this->missingPrameterArr("account_id");
        }

        $dealerDB   = Game_CONST::DBConst_Name;
        $account_id = $arrData['account_id'];

        $friend_where = "manager_id = " . $account_id . " and is_delete = 0 and status = 0 ";
        $friend_sql   = "select count(*) as counts from " . Manage_Member . " where " . $friend_where;
        $friend_query = $this->getDataBySql($dealerDB, 1, $friend_sql);
        if (DB_CONST::DATA_NONEXISTENT == $friend_query) {
            return FALSE;
        }

        if ($friend_query["counts"] == 0) {
            return FALSE;
        }

        return TRUE;
    }

}