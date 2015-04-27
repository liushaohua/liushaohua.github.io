<?php
	$PAGE_TYPE = "org";
	header("content-type: text/html; charset=utf-8");
	require_once ('../includes/common_home_inc.php');
	require_once (COMMON_PATH.'/orgprofile.class.php');
	require_once (COMMON_PATH.'/userprofile.class.php');
	$user = new user();
	$base = new base();
	$message = new message();
	$orgprofile = new orgprofile();
	$userprofile = new userprofile();
	$user_msg_total = new user_msg_total();
	$org_profile = $orgprofile -> get_org_profile($user_info["id"]);
	$org_type = $base -> get_org_type_info($org_profile['type']);
	$address_info = $base -> get_address_info($org_profile['province_id'],$org_profile['city_id'],$org_profile['district_id']);
	$recommend_users = $user -> get_new_user_list('8');
	if($recommend_users){
		foreach($recommend_users as $recommend_user){
			$user_profile = $userprofile -> get_user_profile($recommend_user['id']);
			is_array($user_role_list = $userprofile -> get_role_list_by_user($recommend_user['id'],$flash)) ? null : $user_role_list = array();
			$role_str = '';
			$user_role = array();
			foreach($user_role_list as $role_info){
				$role_str .= $role_info['name'].'/'; 
			}
			$role_str = rtrim($role_str,'/');
			//var_dump($user_profile);
			if($user_profile['province_id'] && $user_profile['city_id'] && $user_profile['district_id']){
				$recommend_address_info = $base -> get_address_info($user_profile['province_id'],$user_profile['city_id'],$user_profile['district_id']);
			}else{
				$recommend_address_info['address'] = '';	
			}
			if(!$user_profile['state']){
				$user_profile['state'] = 'error';	
			}
			$recommend_user_list[$recommend_user['id']] = array(
				'icon_url' => $recommend_user['icon_server_url'].$recommend_user['icon_path_url'],
				'nickname' => $recommend_user['nickname'],
				'level' =>  $recommend_user['level'],
				'email_status' => $recommend_user['email_status'],
				'mobile_status' => $recommend_user['mobile_status'],
				'identity_card_status' => $recommend_user['identity_card_status'],
				'address' => $recommend_address_info['address'],
				'role_str' => $role_str,
			);				
		}	
	}else{
		$recommend_user_list = array();
	}
	
	//获取未读私信总数
	$unread_mes_num = $message -> get_unread_message_num($user_info["id"]);
	$smarty -> assign('unread_mes_num',$unread_mes_num);
	
	
	$smarty -> assign('user_info',$user_info);
	$smarty -> assign('address_info',$address_info);
	$smarty -> assign('org_profile',$org_profile);
	$smarty -> assign('org_type',$org_type['name']);
	$smarty -> assign('org_state',(!$org_profile['state']) ?: $COMMON_CONFIG["STATE"][$org_profile['state']]);
	$smarty -> assign('recommend_user_list',$recommend_user_list);
	
	//获取 用户中心的未读信息系 提示 
	$unread_mes_num = $user_msg_total -> get_user_msg_total_info($user_info["id"]);
	//获取我的工作信息 未读提示
	$mywork_info_num = $unread_mes_num['recruit_apply'] + $unread_mes_num['recruit_invite'] + $unread_mes_num['reply_apply'] + $unread_mes_num['reply_invite'];
	$smarty -> assign('mywork_info_num',$mywork_info_num);
	//获取我的未读私信 未读提示
	$smarty -> assign('message',$unread_mes_num['message']);

	$smarty -> display("home/org_index.html");

?>