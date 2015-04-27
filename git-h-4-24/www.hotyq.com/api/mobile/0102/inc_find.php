<?php
	//指定二级服务下的红人
	function find_category_reds(){
		global $COMMON_CONFIG;
		$user = new user();
		$base = new base();
		$service = new service();
		$find_user = new find_user();
		$userprofile = new userprofile();
		isset($_POST['uid']) ? $uid = intval($_POST['uid']) : $uid = '';
		isset($_POST['app_token']) ? $token = clear_gpq($_POST['app_token']) : $token = '';
		//_check_login($uid,$token);
		if(empty($_POST['sid'])) return get_state_info(1099);
		$sid = intval($_POST['sid']);
		isset($_POST['page']) ? $page = intval($_POST['page']) : $page = '';
		if($page < 1){
			$page = 1;
		}
		$pagesize = 10;
		$find_user_key[] = $find_user -> get_find_key('level_2_service',$sid);	
		$find_user_res = $find_user -> get_user_list_by_sinter($find_user_key,$page,$pagesize);
		$find_user_id = $find_user_res['list'];
		$find_user_count = $find_user_res['count'];
		$find_user_list = array();
		$service_list = $base -> get_service_list();
		foreach($find_user_id as $uid){
			$userinfo = $user -> get_userinfo($uid);
			$user_profile = $userprofile -> get_user_profile($uid);
			if($userinfo['identity_card_status'] == 'yes'){
				$has_authentication = '1';
			}else{
				$has_authentication = '0';		
			}
			if($userinfo['mobile_status'] == 'yes'){
				$has_verify_mobile = '1';
			}else{
				$has_verify_mobile = '0';		
			}
			//获取用户的所选的二级服务
			$service_array = $service -> get_e_service_by_user($uid);
			$service_2_list = array();
			if($service_array){
				foreach($service_array as $key=>$value){
					$service_2_list[] = $service_list[$value['service_2_id']];
				}	
			}
			$address_info = $base -> get_address_info($user_profile['province_id'],$user_profile['city_id'],$user_profile['district_id']);
			$find_user_list[] = array(
				'uid' => $uid,
				'icon_img' => $userinfo['icon_server_url'].$userinfo['icon_path_url'],
				'nickname' => $userinfo['nickname'],
				'sex' => $COMMON_CONFIG["SEX"][$user_profile['sex']],
				'has_authentication' => $has_authentication,
				'has_verify_mobile' => $has_verify_mobile,
				'city' => $address_info['city_info']['cname'],
				'service' => implode(array_unique($service_2_list),'/')
			);		
		}
		$ret_arr = get_state_info(1000);
		if($find_user_list){
			$ret_arr['data']['number'] = $find_user_count;
			$ret_arr['data']['list'] = $find_user_list;
		}
		return $ret_arr;
	}

	//指定二级服务下的机构
	function find_category_orgs(){
		$user = new user();
		$base = new base();
		$service = new service();
		$find_org = new find_org();
		$orgprofile = new orgprofile();
		isset($_POST['uid']) ? $uid = intval($_POST['uid']) : $uid = '';
		isset($_POST['app_token']) ? $token = clear_gpq($_POST['app_token']) : $token = '';
		//_check_login($uid,$token);
		if(empty($_POST['sid'])) return get_state_info(1099);
		$sid = intval($_POST['sid']);
		isset($_POST['page']) ? $page = intval($_POST['page']) : $page = '';
		if($page < 1){
			$page = 1;
		}
		$pagesize = 10;
		$find_org_key[] = $find_org -> get_find_key('level_2_service',$sid);	
		$find_org_res = $find_org -> get_org_list_by_sinter($find_org_key,$page,$pagesize);
		$find_org_id = $find_org_res['list'];
		$find_org_count = $find_org_res['count'];
		$find_org_list = array();
		$service_list = $base -> get_service_list();
		foreach($find_org_id as $uid){
			$userinfo = $user -> get_userinfo($uid);
			$org_profile = $orgprofile -> get_org_profile($uid);
			if($userinfo['identity_card_status'] == 'yes' || $userinfo['business_card_status'] == 'yes'){
				$has_authentication = '1';
			}else{
				$has_authentication = '0';		
			}
			if($userinfo['mobile_status'] == 'yes'){
				$has_verify_mobile = '1';
			}else{
				$has_verify_mobile = '0';		
			}
			//获取用户的所选的二级服务
			$service_array = $service -> get_e_service_by_user($uid);
			$service_2_list = array();
			if($service_array){
				foreach($service_array as $key=>$value){
					$service_2_list[] = $service_list[$value['service_2_id']];
				}	
			}
			$org_type = $base -> get_org_type_info($org_profile['type']);
			$address_info = $base -> get_address_info($org_profile['province_id'],$org_profile['city_id'],$org_profile['district_id']);
			$find_org_list[] = array(
				'uid' => $uid,
				'icon_img' => $userinfo['icon_server_url'].$userinfo['icon_path_url'],
				'nickname' => $userinfo['nickname'],
				'org_type' => $org_type['name'],
				'has_authentication' => $has_authentication,
				'has_verify_mobile' => $has_verify_mobile,
				'city' => $address_info['city_info']['cname'],
				'service' => implode(array_unique($service_2_list),'/')
			);		
		}
		$ret_arr = get_state_info(1000);
		if($find_org_list){
			$ret_arr['data']['number'] = $find_org_count;
			$ret_arr['data']['list'] = $find_org_list;
		}
		return $ret_arr;
	}

	//指定二级服务下的招募
	function find_category_recruit(){
		$base = new base();
		$find_recruit = new find_recruit();
		$recruit = new recruit();
		isset($_POST['uid']) ? $uid = intval($_POST['uid']) : $uid = '';
		isset($_POST['app_token']) ? $token = clear_gpq($_POST['app_token']) : $token = '';
		//_check_login($uid,$token);
		if(empty($_POST['sid'])) return get_state_info(1099);
		$sid = intval($_POST['sid']);
		isset($_POST['page']) ? $page = intval($_POST['page']) : $page = '';
		if($page < 1){
			$page = 1;
		}
		$pagesize = 10;
		$find_recruit_key[] = $find_recruit -> get_find_key('service_2',$sid);	
		$find_recruit_res = $find_recruit -> get_recruit_list_by_sinter($find_recruit_key,$page,$pagesize);
		$find_recruit_ids = $find_recruit_res['list'];
		$find_recruit_count = $find_recruit_res['count'];
		$find_recruit_list = array();
		foreach($find_recruit_ids as $rid){
			$recruit_info = $recruit -> get_recruit_info($rid);
			$service_2_list = '';
			if(is_array($service_2_arr = $recruit -> get_service_list_by_recruit($rid))){
				foreach($service_2_arr as $v2){
					if($service_2_info = $base -> get_service_info($v2['service_2_id'])){
						$service_2_list .= $service_2_info['name'].'/';
					}
				}
			}
			$city_info = $base -> get_city_info($recruit_info['city_id'],$flash);		
			$find_recruit_list[] = array(
				'rid' => $recruit_info['id'],
				'ruid' => $recruit_info['uid'],
				'icon_img' => $recruit_info['cover_server_url'].$recruit_info['cover_path_url'],
				'title' => $recruit_info['name'],
				'status' => $recruit_info['status'],
				'city' => $city_info['cname'],
				'deadline' => strtotime($recruit_info['interview_end_time']),	
				'service' => rtrim($service_2_list,'/')
			);
		}
		$ret_arr = get_state_info(1000);
		if($find_recruit_list){
			$ret_arr['data']['number'] = $find_recruit_count;
			$ret_arr['data']['list'] = $find_recruit_list;
		}
		return $ret_arr;
	}
?>