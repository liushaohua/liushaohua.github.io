<?php
	header("content-type: text/html; charset=utf-8");
	require_once ('../includes/common_inc.php');
	require_once (COMMON_PATH.'/userprofile.class.php');
	require_once (COMMON_PATH.'/orgprofile.class.php');
	require_once (COMMON_PATH.'/album.class.php');
	require_once (COMMON_PATH.'/recruit.class.php');
	require_once (COMMON_PATH.'/collect.class.php');
	require_once (COMMON_PATH.'/message.class.php');
	require_once(COMMON_PATH.'/find_user.class.php');
	require_once(COMMON_PATH.'/redis.class.php');
	require_once(COMMON_PATH.'/redis_find.class.php');
	$user = new user();
	$find_user = new find_user();
	$userprofile = new userprofile();
	$orgprofile = new orgprofile();
	if(!isset($_REQUEST['city_id'])){
		$city_id = 0;
	}else{
		$city_id = intval($_REQUEST['city_id']);
	}
	//角色列表
	$base = new base();
	$parent_role_list_all = $base -> get_parent_role_list($flash);
	foreach($parent_role_list_all as $role_info){
		if($role_info['name'] != '其他'){
			$parent_role_list[] = $role_info;	
		}
	}
	$sys_role_list = $base ->  get_sys_role_list($flash);
	foreach($sys_role_list as $sys_role){
		if($sys_role['parent_id'] !== '0'){
			$role_2_list[$sys_role['parent_id']][] = $sys_role;
		}
	}
	$city_all = $base -> get_city_list($flash);
	$chief_city = $base -> get_chief_city_list($flash);
	foreach($chief_city as $des_info){
		$chief_city_list[$des_info['des']] = $base -> get_city_info($des_info['des']);
	}
	$citys['ABCDEF'] = array();
	$citys['GHIJ'] = array();
	$citys['KLMN'] = array();
	$citys['OPQR'] = array();
	$citys['STUV'] = array();
	$citys['WXYZ'] = array();
	foreach($city_all as $city_info){
		if(in_array($city_info['spell'][0],array('a','b','c','d','e','f'))){
			$citys['ABCDEF'][$city_info['id']] = $city_info;
		}elseif(in_array($city_info['spell'][0],array('g','h','i','j'))){
			$citys['GHIJ'][$city_info['id']] = $city_info;
		}elseif(in_array($city_info['spell'][0],array('k','l','m','n'))){
			$citys['KLMN'][$city_info['id']] = $city_info;
		}elseif(in_array($city_info['spell'][0],array('o','p','q','r'))){
			$citys['OPQR'][$city_info['id']] = $city_info;
		}elseif(in_array($city_info['spell'][0],array('s','t','u','v'))){
			$citys['STUV'][$city_info['id']] = $city_info;
		}elseif(in_array($city_info['spell'][0],array('w','x','y','z'))){
			$citys['WXYZ'][$city_info['id']] = $city_info;
		}
	}
	$user_list = array();
	foreach($parent_role_list as $parent_role){
		$role_1_list[$parent_role['id']]['name'] = $parent_role['name'];
		$role_1_list[$parent_role['id']]['english'] = $parent_role['english'];
		$find_user_key[$parent_role['id']] = $find_user -> get_find_key('level_1_role',$parent_role['id']);
		if(!empty($find_user_key[$parent_role['id']])){
			$find_user_res = $find_user -> get_user_list_by_sinter(array($find_user_key[$parent_role['id']]),'1','16');
			$find_user_id = $find_user_res['list'];
		}else{
			$find_user_id =array();	
		}
		foreach($find_user_id as $uid){
			$user_info = $user -> get_userinfo($uid);
			if($user_info['data_percent'] > 0){
				is_array($user_role_list = $userprofile -> get_role_list_by_user($uid,$flash)) ? null : $user_role_list = array();
				$role_str = '';
				$user_role = array();
				foreach($user_role_list as $role_info){
					$role_str .= $role_info['name'].'/'; 
				}
				$role_str = rtrim($role_str,'/');
				$user_profile = $userprofile -> get_user_profile($uid);
				$address_info = $base -> get_address_info($user_profile['province_id'],$user_profile['city_id'],$user_profile['district_id']);
				$user_info['role_str'] = $role_str;
				$user_info['address_arr'] = $address_info;
				$user_info['icon'] = $user_info['icon_server_url'].$user_info['icon_path_url'];
				$user_info['age'] = $user_profile['age'];
				$user_info['sex'] = $user_profile['sex'];
				$user_list[$parent_role['id']][$uid] = $user_info;	
			}	
		}
	}

	$org_type_list = $base -> get_org_type_list();
	$org_list_ranks = $user -> get_rank_org_list(16);
	if($org_list_ranks){
		foreach($org_list_ranks as $org_list_rank){
			$org_profile = $orgprofile -> get_org_profile($org_list_rank['id']); 
			$type_info = $base -> get_org_type_info($org_profile['type']);
			$address_info = $base -> get_address_info($org_profile['province_id'],$org_profile['city_id'],$org_profile['district_id']);
			$org_list_rank['type'] = $type_info['name'];
			$org_list_rank['address_arr'] = $address_info;
			$org_list_rank['icon'] = $org_list_rank['icon_server_url'].$org_list_rank['icon_path_url'];
			$org_list_rank['create_time'] = $org_profile['create_time'];
			$org_list[$org_list_rank['id']] = $org_list_rank;
		}	
	}else{
		$org_list = array();	
	}
	
	// 右侧栏  与你相关   默认给最新的招募（未过期 排好序 最新）------wangyifan--start--
	$recruit = new recruit();
	$where = array('status' => '1');
	$new_recruit_list = $recruit -> get_recruit_list_by_where($where,0,10);
	//处理招募
	if($new_recruit_list){
		foreach($new_recruit_list as $k=>$v){
			$new_recruit_list[$k]['recruit_city_name'] = $base -> get_city_info($v['city_id']);
			//$new_recruit_list[$k]['sys_start_time'] = date('m.d',strtotime($v['sys_start_time']));
			$new_recruit_list[$k]['type_info'] = $recruit -> get_recruit_type_info($v['type_id']);
		}	
	}else{
		$new_recruit_list = array();
	}
	
	$smarty -> assign('new_recruit_list',$new_recruit_list);
	//-------------------wangyifan  end----------		
	$recommend_user_list = $user -> get_new_user_list(20);
	$smarty -> assign('citys',$citys);
	$smarty -> assign('recommend_user_list',$recommend_user_list);
	$smarty -> assign('org_type_list',$org_type_list);
	$smarty -> assign('org_list',$org_list);
	$smarty -> assign('role_1_list',$role_1_list);
	$smarty -> assign('role_2_list',$role_2_list);
	$smarty -> assign('user_list',$user_list);
	$smarty -> assign('sys_role_list',$sys_role_list);
	$smarty -> assign('nav_main','user');
	$smarty -> assign('active_status','user_index');
	$smarty -> assign('chief_city_list',$chief_city_list);
	$smarty -> display('user/index.html');

?>