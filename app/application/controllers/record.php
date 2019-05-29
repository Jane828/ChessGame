<?php
/**
 * Created by PhpStorm.
 * User: nszxyu
 * Date: 2017/11/22
 * Time: 15:47
 */

class Record extends MY_Controller{
    function __construct(){
        parent::__construct();
    }

    public function get_room_record(){
        $this->ajaxCheckLogin();
        $params = getRequest();
        $where = [];

        $request_ary['open_id'] = $_SESSION['WxOpenID'];
        $this->load->model('account/account_model','',true);
        $userinfo_result = $this->account_model->getUserInfo($request_ary);
        if(!isset($userinfo_result['data']['account_id']) || $userinfo_result['data']['account_id'] == "")
        {
            return $this->ajaxReturn($userinfo_result);
        }

        $where['account_id'] = $userinfo_result['data']['account_id'];

        if(isset($params['type'])){
            $type = $params['type'];
            $game_list = $this->config->item('game');
            if(isset($game_list[$type])){
                $where['game_type'] = $game_list[$type]['type'];
                $game_table = $game_list[$type]['table'];
            }else{
                return $this->ajaxFailed('不存在该游戏');
            }
        } else {
            return $this->ajaxFailed('参数错误');
        }

        $page = $params['page'] ? $params['page'] : 1;

        $this->load->model('game/detail_model','',true);
        $result = $this->detail_model->getAccountRoomRecord($where,$page,20);

        foreach ($result['data'] as &$v){
            $v->room_number = $this->detail_model->getRoomNumberByRoomId($v->room_id,$game_table);
            $v->over_time = date('m-d H:i',$v->over_time);
        }

        $this->ajaxSuccess($result);
    }


    public function my_room(){
        $this->checkLogin();

        $open_id = $_SESSION['WxOpenID'];

        $request_ary['open_id'] = $open_id;
        $this->load->model('account/account_model','',true);
        $userinfo_result = $this->account_model->getUserInfo($request_ary);
        if(!isset($userinfo_result['data']['account_id']) || $userinfo_result['data']['account_id'] == "")
        {
            $direct_url = base_url("y/yh");
            Header("Location:".$direct_url);
            return;
        }
        if (! $this->checkIsAgree()) {
            return $this->warm();
        }
        $data['base_url'] = $this->domain_path();

        $data['account_id'] = $userinfo_result['data']['account_id'];

        $data['image_url'] = Game_Const::ImageUrl;

        $this->load->model('wechat_model','',true);
        $config_ary = $this->wechat_model->getWxConfig();
        $data['config_ary']=$config_ary;

        $game_list = $this->config->item('game');
        $data['game_list'] = $game_list;
        $game_tags = array_keys($game_list);
        $data['default_game'] = isset($game_tags[0]) ? $game_tags[0] : '';

        $this->load->view("my_room", $data);
    }


    public function get_my_room(){
        $this->ajaxCheckLogin();
        $params = getRequest();
        $where = [];

        $request_ary['open_id'] = $_SESSION['WxOpenID'];
        $this->load->model('account/account_model','',true);
        $userinfo_result = $this->account_model->getUserInfo($request_ary);
        if(!isset($userinfo_result['data']['account_id']) || $userinfo_result['data']['account_id'] == "")
        {
            return $this->ajaxReturn($userinfo_result);
        }

        $where['account_id'] = $userinfo_result['data']['account_id'];

        if(isset($params['type'])){
            $type = $params['type'];
            $game_list = $this->config->item('game');
            if(isset($game_list[$type])){
                $game_table = $game_list[$type]['table'];
                $where['game_type'] = $game_list[$type]['type'];
            }else{
                return $this->ajaxFailed('不存在该游戏');
            }
        } else {
            return $this->ajaxFailed('参数错误');
        }

//        $where['is_delete']  = 0;

        $page = $params['page'] ? $params['page'] : 1;

        $this->load->model('game/detail_model','',true);
        $result = $this->detail_model->getOpenRoomRecord($game_table, $where, $page, 20);

        foreach ($result['data'] as &$v){
            $v->create_time = date('m-d H:i',$v->create_time);
        }

        $this->ajaxSuccess($result);
    }

    public function room_detail_check(){
        $this->checkLogin();
        if (! $this->checkIsAgree()) {
            return $this->warm();
        }
        $params = getRequest();
        log_message('error', "function(room_detail_check): kk test:".json_encode($params)." in file ".__FILE__." on Line ".__LINE__);
        $room_number = $params['id'];
        $type    = $params['type'];
        if(!$room_number || !$type){
            show_404();
        }
        $this->load->model('game/detail_model','',true);

        if($type == 1 || $type == 110 || $type == 111 || $type == 92){
            $table = 'flower_room';
        } elseif ($type == 36 || $type == 37 || $type == 38) {
            $table = 'sangong_room';
        } else if ($type == 61 || $type == 62 || $type == 63) {
            $table = 'dark_room';
        } else {
            $table = 'bull_room';
        }

        $room_id = $this->detail_model->getRoomIdByRoomNumber($room_number,$table);

        $board_info = $this->detail_model->getRoomBoardById($room_id,$type);
        if(empty($board_info)){
            $result = array("result" => 0, "data" => array(), "result_message" => "该局游戏不存在");
        }else{
            $result = array("result" => 1, "data" => array(), "result_message" => "该局游戏存在");
        }

        $this->ajaxReturn($result);
    }

    public function room_detail(){
        $this->checkLogin();
        if (! $this->checkIsAgree()) {
            return $this->warm();
        }
        if(!isset($_GET['id'])){
            show_404();
        }
        $room_number = $_GET['id'];
        $type    = $_GET['type'];
        if(!$room_number || !$type){
            show_404();
        }
        $data = [];

        $this->load->model('game/detail_model','',true);

        if($type == 1 || $type == 110 || $type == 111 || $type == 92){
            $table = 'flower_room';
        } elseif ($type == 36 || $type == 37 || $type == 38) {
            $table = 'sangong_room';
        } else if ($type == 61 || $type == 62 || $type == 63) {
            $table = 'dark_room';
        } else {
            $table = 'bull_room';
        }

        $room_id = $this->detail_model->getRoomIdByRoomNumber($room_number,$table);

        $board_info = $this->detail_model->getRoomBoardById($room_id,$type);
        if(empty($board_info)){
            show_404();
        }

        $board_info->start_time = date('m-d H:i',$board_info->start_time);
        $board_info->over_time  = date('m-d H:i',$board_info->create_time);

        $this->load->model('account/account_model','',true);

        foreach ($board_info->balance_board as &$user){
            $user_info = $this->account_model->getUserInfoById($user['account_id']);
            $user['head'] = $user_info->headimgurl;
            $user['user_code'] = $user_info->user_code;
            if($user_info->account_id == $board_info->room_own){
                $board_info->room_own = $user_info->nickname;
            }
        }
        $data['board'] = $board_info;
        $game_type = $board_info->game_type;

        $detail_list = $this->detail_model->getRoomDetail($room_id,$type);
        $round_list = [];
        foreach ($detail_list as $detail){
            $result_list = json_decode($detail->game_result,true);
            // print_r($result_list);
            if($game_type == 1 || $game_type == 110 || $game_type == 111 || $game_type == 92 || $game_type == 95){
                $round_list[] = $this->dealResultFlower($result_list);
            }else if($game_type == 36 || $game_type == 37 || $game_type == 38){
                $round_list[] = $this->dealResultSangong($result_list);
            }else if($game_type == 71){
                $round_list[] = $this->dealResultLBull($result_list);
            }else if($game_type == 13){
                $round_list[] = $this->dealResultFBull($result_list);
            }else if ($game_type == 61 || $game_type == 62 || $game_type == 63 ) {
                //todo
                $round_list[] = $this->dealResultdark($result_list);
            }
            else {
                $round_list[] = $this->dealResultBull6($result_list);
            }
        }
        $data['rounds'] = $round_list;
        $data['room_id'] = $room_id;
        $data['game_type'] = $type;
        $data['room_number'] = $room_number;

        $data['image_url'] = Game_CONST::ImageUrl;
        // print_r($data);
        $this->load->view("room_detail", $data);
    }

    public function board(){
        $this->checkLogin();
        if (! $this->checkIsAgree()) {
            return $this->warm();
        }
        if(!isset($_GET['id'])){
            show_404();
        }
        $room_number = $_GET['id'];
        $type    = $_GET['type'];
        if(!$room_number || !$type){
            show_404();
        }
        $data = [];

        $this->load->model('game/detail_model','',true);
        $this->load->model('account/account_model','',true);
        if($type == 1 || $type == 110 || $type == 111 || $type == 92 || $type == 95){
            $table = 'flower_room';
        } elseif ($type == 36 || $type == 37 || $type == 38) {
            $table = 'sangong_room';
        } else if ($type == 61 || $type == 62 || $type == 63) {
            $table = 'dark_room';
        } else {
            $table = 'bull_room';
        }

        $room_id = $this->detail_model->getRoomIdByRoomNumber($room_number,$table);

        $board_info = $this->detail_model->getRoomBoardById($room_id,$type);
        if(empty($board_info)){
            show_404();
        }

        $board_info->over_time  = date('Y-m-d H:i',$board_info->create_time);

        $board_info->total_round = substr($board_info->rule_text, 0, strpos($board_info->rule_text,'局/'));

        $big_winner = 0;
        $max_score = 0;

        foreach ($board_info->balance_board as $key => $player){
            $board_info->balance_board[$key]['big_winner'] = 0;
            // print_r($player);
            if($player['score'] > $max_score){
                $big_winner = $key;
                $max_score =$player['score'];
            }
            $user_info = $this->account_model->getUserInfoById($player['account_id']);
            $board_info->balance_board[$key]['head']= $user_info->headimgurl;
            if($user_info->account_id == $board_info->room_own){
                $board_info->room_own = $user_info->nickname;
            }
        }
        $board_info->balance_board[$big_winner]['big_winner'] = 1;

        $data['board'] = $board_info;
        $data['room_id'] = $room_id;
        $data['room_own'] = $board_info->room_own;
        $data['room_number'] = $room_number;
        $data['game_type']  = $type;

        $data['base_url'] = $this->domain_path();

        $data['image_url'] = Game_CONST::ImageUrl;
        // print_r($data);
        $this->load->view("room_board", $data);
    }

    /*
		6人斗牛结果处理
	*/
    protected function dealResultBull6($game_result)
    {
        $pAry = $game_result['pAry'];
        $gData = $game_result['gData'];

        $game_num = $gData['gnum'];
        $total_num = $gData['tnum'];
        $banker_id = $gData['bid'];

        $play_array = array();
        foreach($pAry as $item)
        {
            $player_id = $item['p'];
            $score = $item['s'];
            $card_str = $item['c'];
            $mult = $item['m']."倍";
            $name = $item['n'];
            $card_type = -1;
            if(isset($item['ct']))
            {
                $card_type = $item['ct'];
            }
            $combo_point = 0;
            if(isset($item['cp']))
            {
                $combo_point = $item['cp'];
            }
            $is_banker = 0;
            if($banker_id == $player_id)
            {
                $is_banker = 1;
                $mult = $gData['bmult']."倍";
            }

            if($card_str == "" && $mult == -1)
            {
                $is_join = 0;
//                $player_cards = "未参与该局游戏";
                $player_cards = [];
                $card_type_str = "";
                $mult = "";
                $score = "";
            }
            else
            {
                $is_join = 1;
                //牌型  1无牛，2牛1-6，3牛7-9，4牛牛，5五花，6顺子牛,7同花牛,8葫芦牛,9炸弹牛,10五小牛,11同花顺，12四花牛
                switch($card_type)
                {
                    case 12:
                        $card_type_str = "四花牛";
                        break;
                    case 11:
                        $card_type_str = "同花顺";
                        break;
                    case 10:
                        $card_type_str = "五小牛";
                        break;
                    case 9:
                        $card_type_str = "炸弹牛";
                        break;
                    case 8:
                        $card_type_str = "葫芦牛";
                        break;
                    case 7:
                        $card_type_str = "同花牛";
                        break;
                    case 6:
                        $card_type_str = "顺子牛";
                        break;
                    case 5:
                        $card_type_str = "五花牛";
                        break;
                    case 4:
                        $card_type_str = "牛牛";
                        break;
                    case 3:
                    case 2:
                        $card_type_str = "牛".$combo_point;
                        break;
                    case 1:
                        $card_type_str = "无牛";
                        break;
                    default :
                        $card_type_str = "";
                        break;
                }

                $player_cards = array();
                if($card_str != "")
                {
                    $cards = explode(",", $card_str);
                    foreach($cards as $card)
                    {
                        $suit = substr($card, 0, 1);
                        $point = substr($card, 1);
                        switch($suit)
                        {
                            case "A":
                                $suit = 4;
                                break;
                            case "B":
                                $suit = 3;
                                break;
                            case "C":
                                $suit = 1;
                                break;
                            case "D":
                                $suit = 2;
                                break;
                            case "E":
                                $suit = 5;
                                break;
                            case "F":
                                $suit = 6;
                                break;
                            case "G":
                                $suit = 7;
                                break;
                            case "H":
                                $suit = 8;
                                break;
                            case "J":
                                $suit = 9;
                                break;
                        }
                        $card_ary['suit'] = $suit;
                        $card_ary['point'] = $point;
                        $player_cards[] = $card_ary;
                    }
                }
            }

            $array['name'] = $name;
            $array['chip'] = $mult;
            $array['score'] = $score;
            $array['is_banker'] = $is_banker;
            $array['card_type_str'] = $card_type_str;
            $array['player_cards'] = $player_cards;
            $array['is_join'] = $is_join;

            $play_array[] = $array;
        }

        $result['game_num'] = $game_num;
        $result['total_num'] = $total_num;
        $result['player_cards'] = $play_array;

        return $result;
    }
    /*
        13人牛牛处理结果 start
    */
    protected function dealResultFBull($game_result)
    {
        $pAry = $game_result['pAry'];
        $gData = $game_result['gData'];

        $game_num = $gData['gnum'];
        $total_num = $gData['tnum'];
        $banker_id = $gData['bid'];

        $play_array = array();
        foreach($pAry as $item)
        {
            $player_id = $item['p'];
            $score = $item['s'];
            $card_str = $item['c'];
            $mult = $item['m']."倍";
            $name = $item['n'];
            $card_type = -1;
            if(isset($item['ct']))
            {
                $card_type = $item['ct'];
            }
            $combo_point = 0;
            if(isset($item['cp']))
            {
                $combo_point = $item['cp'];
            }
            $is_banker = 0;
            if($banker_id == $player_id)
            {
                $is_banker = 1;
                $mult = $gData['bmult']."倍";
            }

            if($card_str == "" && $mult == -1)
            {
                $is_join = 0;
//                $player_cards = "未参与该局游戏";
                $player_cards = [];
                $card_type_str = "";
                $mult = "";
                $score = "";
            }
            else
            {
                $is_join = 1;
                //牌型  1无牛，2牛1-6，3牛7-9，4牛牛，5五花，6顺子牛,7同花牛,8葫芦牛,9炸弹牛,10五小牛,11同花顺，12四花牛
                switch($card_type)
                {
                    case 12:
                        $card_type_str = "四花牛";
                        break;
                    case 11:
                        $card_type_str = "同花顺";
                        break;
                    case 10:
                        $card_type_str = "五小牛";
                        break;
                    case 9:
                        $card_type_str = "炸弹牛";
                        break;
                    case 8:
                        $card_type_str = "葫芦牛";
                        break;
                    case 7:
                        $card_type_str = "同花牛";
                        break;
                    case 6:
                        $card_type_str = "顺子牛";
                        break;
                    case 5:
                        $card_type_str = "五花牛";
                        break;
                    case 4:
                        $card_type_str = "牛牛";
                        break;
                    case 3:
                    case 2:
                        $card_type_str = "牛".$combo_point;
                        break;
                    case 1:
                        $card_type_str = "无牛";
                        break;
                    default :
                        $card_type_str = "";
                        break;
                }

                $player_cards = array();
                if($card_str != "")
                {
                    $cards = explode(",", $card_str);
                    foreach($cards as $card)
                    {
                        $suit = substr($card, 0, 1);
                        $point = substr($card, 1);
                        switch($suit)
                        {
                            case "A":
                                $suit = 4;
                                break;
                            case "B":
                                $suit = 3;
                                break;
                            case "C":
                                $suit = 1;
                                break;
                            case "D":
                                $suit = 2;
                                break;
                            case "E":
                                $suit = 6;
                                break;
                        }
                        $card_ary['suit'] = $suit;
                        $card_ary['point'] = $point;
                        $player_cards[] = $card_ary;
                    }
                }
            }

            $array['name'] = $name;
            $array['chip'] = $mult;
            $array['score'] = $score;
            $array['is_banker'] = $is_banker;
            $array['card_type_str'] = $card_type_str;
            $array['player_cards'] = $player_cards;
            $array['is_join'] = $is_join;

            $play_array[] = $array;
        }

        $result['game_num'] = $game_num;
        $result['total_num'] = $total_num;
        $result['player_cards'] = $play_array;

        return $result;
    }
    /*
        13人牛牛处理结果 end
    */

    /**
     * 癞子牛牛结果处理
     * @param $game_result
     * @return mixed
     */
    protected function dealResultLBull($game_result)
    {
        $pAry = $game_result['pAry'];
        $gData = $game_result['gData'];

        $game_num = $gData['gnum'];
        $total_num = $gData['tnum'];
        $banker_id = $gData['bid'];

        $play_array = array();
        foreach($pAry as $item)
        {
            $player_id = $item['p'];
            $score = $item['s'];
            $card_str = $item['c'];
            $mult = $item['m']."倍";
            $name = $item['n'];
            $card_type = -1;
            if(isset($item['ct']))
            {
                $card_type = $item['ct'];
            }
            $combo_point = 0;
            if(isset($item['cp']))
            {
                $combo_point = $item['cp'];
            }
            $is_banker = 0;
            if($banker_id == $player_id)
            {
                $is_banker = 1;
                $mult = $gData['bmult']."倍";
            }

            if($card_str == "" && $mult == -1)
            {
                $is_join = 0;
//                $player_cards = "未参与该局游戏";
                $player_cards = [];
                $card_type_str = "";
                $mult = "";
                $score = "";
            }
            else
            {
                $is_join = 1;
                //牌型  1无牛，2牛1-6，3牛7-9，4牛牛，5五花，6顺子牛,7同花牛,8葫芦牛,9炸弹牛,10五小牛,11同花顺，12四花牛
                switch($card_type)
                {
                    case 12:
                        $card_type_str = "四花牛";
                        break;
                    case 11:
                        $card_type_str = "同花顺";
                        break;
                    case 10:
                        $card_type_str = "五小牛";
                        break;
                    case 9:
                        $card_type_str = "炸弹牛";
                        break;
                    case 8:
                        $card_type_str = "葫芦牛";
                        break;
                    case 7:
                        $card_type_str = "同花牛";
                        break;
                    case 6:
                        $card_type_str = "顺子牛";
                        break;
                    case 5:
                        $card_type_str = "五花牛";
                        break;
                    case 4:
                        $card_type_str = "牛牛";
                        break;
                    case 3:
                    case 2:
                        $card_type_str = "牛".$combo_point;
                        break;
                    case 1:
                        $card_type_str = "无牛";
                        break;
                    default :
                        $card_type_str = "";
                        break;
                }

                $player_cards = array();
                if($card_str != "")
                {
                    $cards = explode(",", $card_str);
                    foreach($cards as $card)
                    {
                        $suit = substr($card, 0, 1);
                        $point = substr($card, 1);
                        switch($suit)
                        {
                            case "A":
                                $suit = 4;
                                break;
                            case "B":
                                $suit = 3;
                                break;
                            case "C":
                                $suit = 1;
                                break;
                            case "D":
                                $suit = 2;
                                break;
                            case "J":
                                $suit = 5;
                                break;
                        }
                        $card_ary['suit'] = $suit;
                        $card_ary['point'] = $point;
                        $player_cards[] = $card_ary;
                    }
                }
            }

            $array['name'] = $name;
            $array['chip'] = $mult;
            $array['score'] = $score;
            $array['is_banker'] = $is_banker;
            $array['card_type_str'] = $card_type_str;
            $array['player_cards'] = $player_cards;
            $array['is_join'] = $is_join;

            $play_array[] = $array;
        }

        $result['game_num'] = $game_num;
        $result['total_num'] = $total_num;
        $result['player_cards'] = $play_array;

        return $result;
    }


    /*
		诈金花结果处理
	*/
    protected function dealResultFlower($game_result)
    {
        $pAry = $game_result['pAry'];
        $gData = $game_result['gData'];

        $game_num = $gData['gnum'];
        $total_num = $gData['tnum'];

        $play_array = array();
        foreach($pAry as $item)
        {
            $card_str = $item['c'];
            $name = $item['n'];
            $chip = $item['s'];
            $score = isset($item['w']) ? $item['w'] : '';
            $card_type = -1;
            if(isset($item['ct']))
            {
                $card_type = $item['ct'];
            }

            $is_banker = 0;

            if($card_str == "")
            {
                $is_join = 0;
                $player_cards = [];
                $card_type_str = "";
            }
            else
            {
                $is_join = 1;
                ////牌型  1高牌 2对子 3顺子 4同花 5同花顺 6三条
                switch($card_type)
                {
                    case 6:
                        $card_type_str = "三条";
                        break;
                    case 5:
                        $card_type_str = "同花顺";
                        break;
                    case 4:
                        $card_type_str = "同花";
                        break;
                    case 3:
                        $card_type_str = "顺子";
                        break;
                    case 2:
                        $card_type_str = "对子";
                        break;
                    case 1:
                        $card_type_str = "高牌";
                        break;
                    default :
                        $card_type_str = "";
                        break;
                }

                $player_cards = array();
                if($card_str != "")
                {
                    $cards = explode(",", $card_str);
                    foreach($cards as $card)
                    {
                        $suit = substr($card, 0, 1);
                        $point = substr($card, 1);
                        switch($suit)
                        {
                            case "A":
                                $suit = 4;
                                break;
                            case "B":
                                $suit = 3;
                                break;
                            case "C":
                                $suit = 1;
                                break;
                            case "D":
                                $suit = 2;
                                break;
                        }
                        $card_ary['suit'] = $suit;
                        $card_ary['point'] = $point;
                        $player_cards[] = $card_ary;
                    }
                }
            }

            $array['name'] = $name;
            $array['chip'] = $chip;
            $array['score'] = $score;
            $array['is_banker'] = $is_banker;
            $array['card_type_str'] = $card_type_str;
            $array['player_cards'] = $player_cards;
            $array['is_join'] = $is_join;

            $play_array[] = $array;
        }

        $result['game_num'] = $game_num;
        $result['total_num'] = $total_num;
        $result['player_cards'] = $play_array;

        return $result;

    }

    // 三公
    protected function dealResultSangong($game_result)
    {
        $pAry = $game_result['pAry'];
        $gData = $game_result['gData'];

        $game_num = $gData['gnum'];
        $total_num = $gData['tnum'];
        $banker_id = $gData['bid'];

        $play_array = array();
        foreach($pAry as $item)
        {
            $player_id = $item['p'];
            $score = $item['s'];
            $card_str = $item['c'];
            $mult = $item['m']."倍";
            $name = $item['n'];
            $is_banker = 0;
            if($banker_id == $player_id)
            {
                $is_banker = 1;
                $mult = $gData['bmult']."倍";
            }

            if($card_str == "" && $mult == -1)
            {
                $is_join = 0;
                $player_cards = "未参与该局游戏";
                $card_type_str = "";
                $mult = "";
                $score = "";
            }
            else
            {
                $is_join = 1;
                $card_type_str = $item['card_text'] . 'x' . $item['card_times'];
                $player_cards = array();
                if($card_str != "")
                {
                    $cards = explode(",", $card_str);
                    foreach($cards as $card)
                    {
                        $suit = substr($card, 0, 1);
                        $point = substr($card, 1);
                        switch($suit)
                        {
                            case 'X':
                                $suit  = 5;
                                $point = 2;
                                break;
                            case 'Y':
                                $suit  = 5;
                                $point = 1;
                                break;
                            case "A":
                                $suit = 4;
                                break;
                            case "B":
                                $suit = 3;
                                break;
                            case "C":
                                $suit = 2;
                                break;
                            case "D":
                                $suit = 1;
                                break;
                        }
                        $card_ary['suit'] = $suit;
                        $card_ary['point'] = $point;
                        $player_cards[] = $card_ary;
                    }
                }
            }

            $array['name'] = $name;
            $array['chip'] = $mult;
            $array['score'] = $score;
            $array['is_banker'] = $is_banker;
            $array['card_type_str'] = $card_type_str;
            $array['player_cards'] = $player_cards;
            $array['is_join'] = $is_join;

            $play_array[] = $array;
        }

        $result['game_num'] = $game_num;
        $result['total_num'] = $total_num;
        $result['player_cards'] = $play_array;

        return $result;
    }

    /*
     暗宝结果处理
 */
    protected function dealResultdark($game_result) {
        $pAry  = $game_result['pAry'];
        $gData = $game_result['gData'];

        $game_num  = $gData['gnum'];
        $total_num = $gData['tnum'];
        $banker_id = $gData['bid'];
        // $prize = $gData['prize'];
        if($gData['prize'] == 0) $prize = "入";
        else if($gData['prize'] == 1) $prize = "龙";
        else if($gData['prize'] == 2) $prize = "出";
        else $prize = "虎";

        $play_array = array();
        foreach ($pAry as $item) {
            $player_id = $item['p'];
            $score     = $item['s'];
            $betLists  = $item['a'];
            $bet_res  = $item['c'];
            $name      = $item['n'];

            $is_banker = 0;
            if ($banker_id == $player_id) {
                $is_banker = 1;
            }

            if ($bet_res) {
                $is_join = 1;
                $player_cards = array();
            } else {
                $is_join = 0;
                $player_cards  = [];
                // $card_type_str = "";
                $mult          = "";
                if(!$is_banker) $score = 0;
            }
            // foreach ($betLists as $betList) {
            //     // $bet_list_arr['name'] = $betList['name'];
            //     // $bet_list_arr['chip'] = $betList['chip'];
            //     $array['bet_list']['name'] = $betList['name'];
            //     $array['bet_list']['chip'] = $betList['chip'];
            // }

            $array['name']          = $name;
            $array['score']         = $score;
            $array['player_id']     = $player_id;
            $array['is_banker']     = $is_banker;
            $array['bet_list']     = $betLists;
            $array['bet_res']     = $bet_res;
            // $array['card_type_str'] = $card_type_str;
            // print_r($betLists);
            $array['player_cards']  = $player_cards;
            $array['is_join']       = $is_join;

            $play_array[] = $array;
        }

        $result['game_num']     = $game_num;
        $result['total_num']    = $total_num;
        $result['player_cards'] = $play_array;
        $result['prize'] = $prize;
        // print_r($result);
        return $result;
    }
}