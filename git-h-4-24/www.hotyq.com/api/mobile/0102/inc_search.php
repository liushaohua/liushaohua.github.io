<?php
	//搜索红人
	function search_reds(){
		global $COMMON_CONFIG;
		$user = new user();
		$base = new base();
		$service = new service();
		$search_user = new search_user();
		$userprofile = new userprofile();
		isset($_POST['uid']) ? $uid = intval($_POST['uid']) : $uid = '';
		isset($_POST['app_token']) ? $token = clear_gpq($_POST['app_token']) : $token = '';
		//_check_login($uid,$token);
		if(empty($_POST['keywords'])) return get_state_info(1099);
		$keywords = clear_gpq($_POST['keywords']);
		isset($_POST['page']) ? $page = intval($_POST['page']) : $page = '';
		if($page < 1){
			$page = 1;
		}
		$pagesize = 10;
		$search_user_res =  $search_user -> get_user_list($keywords,$page,$pagesize);
		$user_list = $search_user_res['list'];
		$search_user_list = array();
		$service_list = $base -> get_service_list();
		foreach($user_list as $user_arr){
			$userinfo = $user -> get_userinfo($user_arr['id']);
			$user_profile = $userprofile -> get_user_profile($user_arr['id']);
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
			$service_array = $service -> get_e_service_by_user($user_arr['id']);
			$service_2_list = array();
			if($service_array){
				foreach($service_array as $key=>$value){
					$service_2_list[] = $service_list[$value['service_2_id']];
				}	
			}
			$city = $base -> get_city_info($user_profile['city_id'],$flash);
			$search_user_list[] = array(
				'uid' => $user_arr['id'],
				'icon_img' => $userinfo['icon_server_url'].$userinfo['icon_path_url'],
				'nickname' => $userinfo['nickname'],
				'sex' => $COMMON_CONFIG["SEX"][$user_profile['sex']],
				'has_authentication' => $has_authentication,
				'has_verify_mobile' => $has_verify_mobile,
				'city' => $city['cname'],
				'service' => implode(array_unique($service_2_list),'/')
			);		
		}
		$ret_arr = get_state_info(1000);
		if($search_user_list){
			$ret_arr['data']['number'] = count($search_user_list);
			$ret_arr['data']['list'] = $search_user_list;
		}
		return $ret_arr;
	}

	//搜索机构
	function search_orgs(){
		$user = new user();
		$base = new base();
		$service = new service();
		$search_org = new search_org();
		$orgprofile = new orgprofile();
		isset($_POST['uid']) ? $uid = intval($_POST['uid']) : $uid = '';
		isset($_POST['app_token']) ? $token = clear_gpq($_POST['app_token']) : $token = '';
		//_check_login($uid,$token);
		if(empty($_POST['keywords'])) return get_state_info(1099);
		$keywords = clear_gpq($_POST['keywords']);
		isset($_POST['page']) ? $page = intval($_POST['page']) : $page = '';
		if($page < 1){
			$page = 1;
		}
		$pagesize = 10;
		$search_org_res =  $search_org -> get_org_list($keywords,$page,$pagesize);
		$org_list = $search_org_res['list'];
		$search_org_list = array();
		$service_list = $base -> get_service_list();
		foreach($org_list as $org_arr){
			$userinfo = $user -> get_userinfo($org_arr['id']);
			$org_profile = $orgprofile -> get_org_profile($org_arr['id']);
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
			$service_array = $service -> get_e_service_by_user($org_arr['id']);
			$service_2_list = array();
			if($service_array){
				foreach($service_array as $key=>$value){
					$service_2_list[] = $service_list[$value['service_2_id']];
				}	
			}
			$org_type = $base -> get_org_type_info($org_profile['type']);
			$city = $base -> get_city_info($org_profile['city_id'],$flash);
			$search_org_list[] = array(
				'uid' => $org_arr['id'],
				'icon_img' => $userinfo['icon_server_url'].$userinfo['icon_path_url'],
				'nickname' => $userinfo['nickname'],
				'org_type' => $org_type['name'],
				'has_authentication' => $has_authentication,
				'has_verify_mobile' => $has_verify_mobile,
				'city' => $city['cname'],
				'service' => implode(array_unique($service_2_list),'/')
			);		
		}
		$ret_arr = get_state_info(1000);
		if($search_org_list){
			$ret_arr['data']['number'] = count($search_org_list);
			$ret_arr['data']['list'] = $search_org_list;
		}
		return $ret_arr;
	}

	//搜索招募
	function search_recruit(){
		$base = new base();
		$recruit = new recruit();
		$search_recruit = new search_recruit();
		isset($_POST['uid']) ? $uid = intval($_POST['uid']) : $uid = '';
		isset($_POST['app_token']) ? $token = clear_gpq($_POST['app_token']) : $token = '';
		//_check_login($uid,$token);
		if(empty($_POST['keywords'])) return get_state_info(1099);
		$keywords = clear_gpq($_POST['keywords']);
		isset($_POST['page']) ? $page = intval($_POST['page']) : $page = '';
		if($page < 1){
			$page = 1;
		}
		$pagesize = 10;
		$search_recruit_res =  $search_recruit -> get_recruit_list($keywords,$page,$pagesize);
		$recruit_list = $search_recruit_res['list'];
		$search_recruit_list = array();
		foreach($recruit_list as $recruit_arr){
			$recruit_info = $recruit -> get_recruit_info($recruit_arr['id']);
			$service_2_list = '';
			if(is_array($service_2_arr = $recruit -> get_service_list_by_recruit($recruit_arr['id']))){
				foreach($service_2_arr as $v2){
					if($service_2_info = $base -> get_service_info($v2['service_2_id'])){
						$service_2_list .= $service_2_info['name'].'/';
					}
				}
			}
			$city_info = $base -> get_city_info($recruit_info['city_id'],$flash);		
			$search_recruit_list[] = array(
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
		if($search_recruit_list){
			$ret_arr['data']['number'] = count($search_recruit_list);
			$ret_arr['data']['list'] = $search_recruit_list;
		}
		return $ret_arr;
	}
	
?>	
