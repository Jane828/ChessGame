<?php

class Game extends CI_Controller {
    /**
     * 构造函数
     */
    function __construct() {
        header('Content-Type: text/html; charset=utf-8');
        parent::__construct();
        $this->load->helper('url');
    }

    private function checkViewLogin() {
        if (!isset($_SESSION['LoginDealerID']) || $_SESSION['LoginDealerID'] == "" || !isset($_SESSION['LoginDealerNum']) || $_SESSION['LoginDealerNum'] == "") {
            $direct_url = base_url("account/login");
            Header("Location:" . $direct_url);
            exit();
        }
        return TRUE;
    }

    private function checkAdmin() {
        if (!isset($_SESSION['LoginUser']) || $_SESSION['LoginUser'] != G_CONST::SUPER_ADMIN) {
            echo '{"result":"-3"}';
            exit();
        }
        return TRUE;
    }

    private function checkOptLogin() {
        if (!isset($_SESSION['LoginAdminID']) || $_SESSION['LoginAdminID'] == "") {
            if (!isset($_SESSION['LoginDealerID']) || $_SESSION['LoginDealerID'] == "" || !isset($_SESSION['LoginDealerNum']) || $_SESSION['LoginDealerNum'] == "") {
                echo '{"result":"-3"}';
                exit();
            }
            return TRUE;
        } else {
            $_SESSION['LoginDealerID'] = $_SESSION['LoginAdminID'];
        }
    }


    /**
     * 获取开局明细统计
     */
    public function getGameList() {
        $this->checkOptLogin();

        $params     = file_get_contents('php://input');
        $params_ary = json_decode($params, TRUE);

        $dealer_num = $params_ary['dealer_num'];

        $request_array['dealer_num'] = $dealer_num;
        $this->load->model('game/detail_model', 'detail_model', TRUE);
        $result = $this->detail_model->getGameList($request_array);
        $result = json_encode($result);
        echo $result;
    }

    /**
     * 获取开局明细统计
     */
    public function getPlayCount() {
        $this->checkOptLogin();

        $params     = file_get_contents('php://input');
        $params_ary = json_decode($params, TRUE);

        $dealer_num = $params_ary['dealer_num'];

        $request_array['dealer_num'] = $dealer_num;
        $request_array['game_type']  = 0;
        $request_array['from']       = $params_ary['from'];
        $request_array['to']         = $params_ary['to'];
//		$request_array['game_type'] = $params_ary['game_type'];
//		$request_array['dealer_num'] = $params_ary['dealer_num'];


        $this->load->model('game/detail_model', 'detail_model', TRUE);
        $result = $this->detail_model->getPlayCount($request_array);
        $result = json_encode($result);
        echo $result;
    }

    /**
     * 获取开局明细统计
     */
    public function getPlayDetailList() {
        $this->checkOptLogin();

        $params     = file_get_contents('php://input');
        $params_ary = json_decode($params, TRUE);

        $dealer_num = $params_ary['dealer_num'];

        $request_array['dealer_num'] = $dealer_num;
        $request_array['from']       = $params_ary['from'];
        $request_array['to']         = $params_ary['to'];
        $request_array['game_type']  = $params_ary['game_type'];
        $request_array['page']       = $params_ary['page'];
//		$request_array['dealer_num'] = $params_ary['dealer_num'];

        $this->load->model('game/detail_model', 'detail_model', TRUE);
        $result = $this->detail_model->getPlayDetailList($request_array);
        $result = json_encode($result);
        echo $result;
    }


    /**
     * 获取活跃用户数量
     */
    public function getActiveCount() {
        $this->checkOptLogin();

        $params     = file_get_contents('php://input');
        $params_ary = json_decode($params, TRUE);

        $dealer_num = $params_ary['dealer_num'];

        $request_array['dealer_num'] = $params_ary['dealer_num'];

        $this->load->model('game/account_model', 'account_model', TRUE);
        $result = $this->account_model->getActiveCount($request_array);
        $result = json_encode($result);
        echo $result;
    }


    /**
     * 获取用户积分统计
     */
    public function getAccountList() {
//		$this->checkOptLogin();

        $params     = file_get_contents('php://input');
        $params_ary = json_decode($params, TRUE);

        $dealer_num = $params_ary['dealer_num'];

        $request_array['dealer_num'] = $dealer_num;
        $request_array['page']       = $params_ary['page'];
        $request_array['keyword']    = $params_ary['keyword'];
        $request_array['uid']        = $params_ary['uid'];

        //		$request_array['dealer_num'] = $params_ary['dealer_num'];
        $this->load->model('game/account_model', 'account_model', TRUE);
        $result = $this->account_model->getAccountList($request_array);
        $result = json_encode($result);
        echo $result;
    }

    public function handleRoomScoreBoard() {
        $this->load->model('game/account_model', 'account_model', TRUE);
        $this->account_model->handleScoreBoard();
    }


    /**
     * 获取游戏房间轮数
     */
    public function getRoomRound() {
        $this->checkOptLogin();

        $params     = file_get_contents('php://input');
        $params_ary = json_decode($params, TRUE);

        $dealer_num = $params_ary['dealer_num'];

        $request_array['dealer_num'] = $dealer_num;

        $request_array['game_type']   = $params_ary['game_type'];
        $request_array['room_number'] = $params_ary['room_number'];
        //		$request_array['dealer_num'] = $params_ary['dealer_num'];
        $this->load->model('game/result_model', 'result_model', TRUE);
        $result = $this->result_model->getRoomRound($request_array);
        $result = json_encode($result);
        echo $result;
    }

    /**
     * 获取游戏结果
     */
    public function getRoomGameResult() {
        $this->checkOptLogin();

        $params     = file_get_contents('php://input');
        $params_ary = json_decode($params, TRUE);

        $dealer_num = $params_ary['dealer_num'];

        $request_array['dealer_num'] = $dealer_num;

        $request_array['room_number'] = $params_ary['room_number'];
        $request_array['round']       = $params_ary['round'];
        $request_array['game_type']   = $params_ary['game_type'];
        //		$request_array['dealer_num'] = $params_ary['dealer_num'];
        $this->load->model('game/result_model', 'result_model', TRUE);
        $result = $this->result_model->getRoomGameResult($request_array);
        $result = json_encode($result);
        echo $result;
    }


    /**
     * 查询用户房卡记录
     */
    public function searchAccountRoomCard() {
        $this->checkOptLogin();

        $params     = file_get_contents('php://input');
        $params_ary = json_decode($params, TRUE);

        $request_array['dealer_num'] = $params_ary['dealer_num'];
        $request_array['page']       = $params_ary['page'];
        $request_array['keyword']    = $params_ary['keyword'];
        $this->load->model('roomcard/source_model', 'source_model', TRUE);
        $result = $this->source_model->searchAccountRoomCard($request_array);
        $result = json_encode($result);
        echo $result;
    }


    /**
     * 扣除用户房卡
     */
    public function deductAccountRoomCard() {
        $this->checkOptLogin();
        $this->checkAdmin();

        $params     = file_get_contents('php://input');
        $params_ary = json_decode($params, TRUE);

        $request_array['dealer_id'] = $_SESSION['LoginDealerID'];


        $request_array['dealer_num'] = $params_ary['dealer_num'];
        $request_array['account_id'] = $params_ary['account_id'];
        $request_array['count']      = $params_ary['count'];
        $this->load->model('roomcard/source_model', 'source_model', TRUE);
        $result = $this->source_model->deductAccountRoomCard($request_array);
        $result = json_encode($result);
        echo $result;
    }

    /**
     * 后台充值用户房卡
     */
    public function increaseAccountRoomCard() {
        $this->checkOptLogin();

        $params     = file_get_contents('php://input');
        $params_ary = json_decode($params, TRUE);

        $request_array['dealer_id'] = $_SESSION['LoginDealerID'];


        $request_array['dealer_num'] = $params_ary['dealer_num'];
        $request_array['account_id'] = $params_ary['account_id'];
        $request_array['count']      = $params_ary['ticket_count'];

        $this->load->model('roomcard/source_model', 'source_model', TRUE);
        $result = $this->source_model->increaseAccountRoomCard($request_array);
        $result = json_encode($result);
        echo $result;
    }

    /**
     * 查询房卡来源
     */
    public function getRoomCardSource() {
        $this->checkOptLogin();


        $params     = file_get_contents('php://input');
        $params_ary = json_decode($params, TRUE);

        $request_array['dealer_num'] = $params_ary['dealer_num'];
        $request_array['account_id'] = $params_ary['account_id'];
        $request_array['page']       = $params_ary['page'];

        $request_array['from'] = $params_ary['from'];
        $request_array['to']   = $params_ary['to'];

        $this->load->model('roomcard/source_model', 'source_model', TRUE);
        $result = $this->source_model->getRoomCardSource($request_array);
        $result = json_encode($result);
        echo $result;
    }

    /**
     * 查询房卡去向
     */
    public function getRoomCardGone() {
        $this->checkOptLogin();


        $params     = file_get_contents('php://input');
        $params_ary = json_decode($params, TRUE);

        $request_array['dealer_num'] = $params_ary['dealer_num'];
        $request_array['account_id'] = $params_ary['account_id'];
        $request_array['page']       = $params_ary['page'];

        $request_array['from'] = $params_ary['from'];
        $request_array['to']   = $params_ary['to'];

        $this->load->model('roomcard/source_model', 'source_model', TRUE);
        $result = $this->source_model->getRoomCardGone($request_array);
        $result = json_encode($result);
        echo $result;
    }

    /**
     * 查询对方收红包明细
     */
    public function getReceiveRecord() {
        $this->checkOptLogin();

        $params     = file_get_contents('php://input');
        $params_ary = json_decode($params, TRUE);

        $request_array['dealer_num']         = $params_ary['dealer_num'];
        $request_array['account_id']         = $params_ary['account_id'];
        $request_array['page']               = $params_ary['page'];
        $request_array['receive_account_id'] = $params_ary['receive_account_id'];

        $this->load->model('roomcard/source_model', 'source_model', TRUE);
        $result = $this->source_model->getReceiveRecord($request_array);
        $result = json_encode($result);
        echo $result;
    }

    /**
     * 根据id查询昵称
     */
    public function getNameById() {
        $this->checkOptLogin();


        $params     = file_get_contents('php://input');
        $params_ary = json_decode($params, TRUE);

        $request_array['dealer_num'] = $params_ary['dealer_num'];
        $request_array['account_id'] = $params_ary['account_id'];

        $this->load->model('game/account_model', 'account_model', TRUE);
        $result = $this->account_model->getNameById($request_array);
        $result = json_encode($result);
        echo $result;
    }

    // 按游戏类型统计历史总分
    public function getGameScoreStat() {
        $this->checkOptLogin();

        $params = file_get_contents('php://input');
        $params = json_decode($params, TRUE);

        $aid  = isset($params['aid']) && !empty($params['aid']) ? $params['aid'] : 0;
        $from = isset($params['from']) && !empty($params['from']) ? $params['from'] : date('Y-m-d');
        $to   = isset($params['to']) && !empty($params['to']) ? $params['to'] : date('Y-m-d');

        $this->load->model('game/account_model', 'account_model', TRUE);
        $result = $this->account_model->getGameScoreStat($aid, $from, $to);
        $result = json_encode($result);
        echo $result;
    }
}
