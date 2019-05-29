<?php

include_once 'common_model.php';		//加载数据库操作类
class Invite_Model extends Guild_Common_Model
{
    public function __construct()
    {
        header('Content-Type: text/html; charset=utf-8'); 
        parent::__construct();
    }

    

    /*
		获取邀请函内容
		
		参数：
		
		返回结果：
	*/
	public function getInviteData($arrData)
	{
		$timestamp = time();
		$result = array();
		
		if(!isset($arrData['dealer_num']) || $arrData['dealer_num'] == G_CONST::EMPTY_STRING)
        {
            log_message('error', "function(getCommissionList):lack of dealer_num"." in file".__FILE__." on Line ".__LINE__);
            return $this->missingPrameterArr("dealer_num");
        }
		if(!isset($arrData['invite_code']) || $arrData['invite_code'] == G_CONST::EMPTY_STRING)
        {
            log_message('error', "function(getCommissionList):lack of invite_code"." in file".__FILE__." on Line ".__LINE__);
            return $this->missingPrameterArr("invite_code");
        }
		if(!isset($arrData['account_id']) || $arrData['account_id'] == G_CONST::EMPTY_STRING)
        {
            log_message('error', "function(getCommissionList):lack of account_id"." in file".__FILE__." on Line ".__LINE__);
            return $this->missingPrameterArr("account_id");
        }
        $dealer_num = $arrData['dealer_num'];
		$DelaerConst = "Dealer_".$dealer_num;
        $dealerDB = $DelaerConst::DBConst_Name;	

		$invite_code = $arrData['invite_code'];		
		$account_id = $arrData['account_id'];
		
		//获取邀请函公会账号信息
		$member_where = 'code="'.$invite_code.'" and code!="-1" and level>0 and is_delete=0';
		$member_sql = 'select member_id,account_id,group_id,level from '.Guild_Member.' where '.$member_where.' limit 1';
		$member_query = $this->getDataBySql($dealerDB,1,$member_sql);
		if(DB_CONST::DATA_NONEXISTENT == $member_query)
		{
			return array('result'=>OPT_CONST::FAILED,'data'=>$result,'result_message'=>"邀请用户不存在");
		}
		$member_id = $member_query['member_id'];
		$invite_aid = $member_query['account_id'];
		$group_id = $member_query['group_id'];
		$level = $member_query['level'];

		//获取公会信息
		$group_where = 'group_id='.$group_id.' and is_delete=0';
		$group_sql = 'select name,profile from '.Guild_Group.' where '.$group_where.' limit 1';
		$group_query = $this->getDataBySql($dealerDB,1,$group_sql);
		if(DB_CONST::DATA_NONEXISTENT == $group_query)
		{
			return array('result'=>OPT_CONST::FAILED,'data'=>$result,'result_message'=>"公会不存在");
		}
		$guild_name = $group_query['name'];
		$guild_profile = $group_query['profile'];

		//是否本人
		$is_owner = 0;
		if($invite_aid == $account_id)
		{
			$is_owner = 1;
		}

		//获取用户是否加入公会
		$is_join = 0;
		$join_where = 'account_id='.$account_id.' and group_id='.$group_id.' and is_delete=0';
		$join_sql = 'select member_id from '.Guild_Member.' where '.$join_where.' limit 1';
		$join_query = $this->getDataBySql($dealerDB,1,$join_sql);
		if(DB_CONST::DATA_NONEXISTENT != $join_query)
		{
			$is_join = 1;
		}

		//用户邀请用户信息
		$account_where = 'account_id="'.$invite_aid.'"';
		$account_sql = 'select account_id,nickname,headimgurl,phone from '.WX_Account.' where '.$account_where.'';
		$account_query = $this->getDataBySql($dealerDB,1,$account_sql);
		if(DB_CONST::DATA_NONEXISTENT == $account_query)
		{
			log_message('error', "function(getUserInfo):account not exist:".$invite_aid." in file".__FILE__." on Line ".__LINE__);
			return array('result'=>OPT_CONST::FAILED,'data'=>$result,'result_message'=>"邀请用户不存在");
		}
		else
		{
			$nickname = $account_query['nickname'];
			$headimgurl = $account_query['headimgurl'];
		}

		$result['nickname'] = $nickname;
		$result['headimgurl'] = $headimgurl;
		$result['guild_name'] = $guild_name;
		$result['guild_profile'] = $guild_profile;
		$result['is_owner'] = $is_owner;
		$result['is_join'] = $is_join;
		$result['qr_url'] = $DelaerConst::QR_Url;
		$result['wx_name'] = $DelaerConst::WX_Name;
		
		return array('result'=>OPT_CONST::SUCCESS,'data'=>$result,'result_message'=>"获取邀请函内容");
	}


	/*
		加入公会
		
		参数：
		
		返回结果：
	*/
	public function joinGuild($arrData)
	{
		$timestamp = time();
		$result = array();
		
		if(!isset($arrData['dealer_num']) || $arrData['dealer_num'] == G_CONST::EMPTY_STRING)
        {
            log_message('error', "function(getCommissionList):lack of dealer_num"." in file".__FILE__." on Line ".__LINE__);
            return $this->missingPrameterArr("dealer_num");
        }
		if(!isset($arrData['invite_code']) || $arrData['invite_code'] == G_CONST::EMPTY_STRING)
        {
            log_message('error', "function(getCommissionList):lack of invite_code"." in file".__FILE__." on Line ".__LINE__);
            return $this->missingPrameterArr("invite_code");
        }
		if(!isset($arrData['account_id']) || $arrData['account_id'] == G_CONST::EMPTY_STRING)
        {
            log_message('error', "function(getCommissionList):lack of account_id"." in file".__FILE__." on Line ".__LINE__);
            return $this->missingPrameterArr("account_id");
        }
        $dealer_num = $arrData['dealer_num'];
		$DelaerConst = "Dealer_".$dealer_num;
        $dealerDB = $DelaerConst::DBConst_Name;	

		$invite_code = $arrData['invite_code'];		
		$account_id = $arrData['account_id'];
		
		//获取邀请函公会账号信息
		$member_where = 'code="'.$invite_code.'" and level>0 and is_delete=0';
		$member_sql = 'select member_id,account_id,group_id,level from '.Guild_Member.' where '.$member_where.' limit 1';
		$member_query = $this->getDataBySql($dealerDB,1,$member_sql);
		if(DB_CONST::DATA_NONEXISTENT == $member_query)
		{
			return array('result'=>OPT_CONST::FAILED,'data'=>$result,'result_message'=>"邀请用户不存在");
		}
		$member_id = $member_query['member_id'];
		$invite_aid = $member_query['account_id'];
		$group_id = $member_query['group_id'];
		$level = $member_query['level'];

		$vice_president = -1;
		if($level == 1)
		{
			$vice_president = $invite_aid;
		}

		//获取用户是否加入公会
		$join_where = 'account_id='.$account_id.' and is_delete=0';
		$join_sql = 'select member_id,group_id,level from '.Guild_Member.' where '.$join_where.' limit 1';
		$join_query = $this->getDataBySql($dealerDB,1,$join_sql);
		if(DB_CONST::DATA_NONEXISTENT != $join_query)
		{
			if($group_id == $join_query['group_id'])
			{
				return array('result'=>OPT_CONST::SUCCESS,'data'=>$result,'result_message'=>"已加入该公会");
			}
			else
			{
				if($join_query['level'] > 1)
				{
					return array('result'=>OPT_CONST::FAILED,'data'=>$result,'result_message'=>"会长不能加入其它公会");
				}

				//绑定新账号
				$updateMember_str = 'update_time='.$timestamp.',update_appid="aid_'.$account_id.'",group_id='.$group_id.',level=0,vice_president='.$vice_president.'';
				$updateMember_where = 'account_id='.$account_id.' and is_delete=0';
				$updateMember_query = $this->changeNodeValue($dealerDB,Guild_Member,$updateMember_str,$updateMember_where);

				if($join_query['level'] == 1){
					//清理副会长绑定记录
					$updateVice_str = 'update_time='.$timestamp.',update_appid="aid_'.$account_id.'",vice_president=-1';
					$updateVice_where = 'vice_president='.$account_id.' and is_delete=0';
					$updateVice_query = $this->changeNodeValue($dealerDB,Guild_Member,$updateVice_str,$updateVice_where);
				}

				return array('result'=>OPT_CONST::SUCCESS,'data'=>$result,'result_message'=>"加入公会成功");
			}
		}
		else
		{
			$insert_array['create_time'] = $timestamp;
			$insert_array['create_appid'] = "aid_".$account_id;
			$insert_array['update_time'] = $timestamp;
			$insert_array['update_appid'] = "aid_".$account_id;
			$insert_array['is_delete'] = 0;
			$insert_array['account_id'] = $account_id;
			$insert_array['group_id'] = $group_id;
			$insert_array['level'] = 0;
			$insert_array['vice_president'] = $vice_president;
			$insert_array['code'] = -1;
			$member_id = $this->getInsertID($dealerDB,Guild_Member, $insert_array);

			return array('result'=>OPT_CONST::SUCCESS,'data'=>$result,'result_message'=>"加入公会成功");
		}
	}

}