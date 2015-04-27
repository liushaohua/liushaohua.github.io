<?php
	header("content-type: text/html; charset=utf-8");
	require_once('../includes/common_inc.php');
	require_once(COMMON_PATH.'/find_user.class.php');
	require_once(COMMON_PATH.'/redis.class.php');
	require_once(COMMON_PATH.'/redis_find.class.php');
	require_once(COMMON_PATH.'/userprofile.class.php');
	require_once(COMMON_PATH.'/page.class.php');
	$user = new user();
	$userprofile = new userprofile();
	$base = new base();
	$find_user = new find_user();
	//服务列表
	
	$service = new service();
	$service_list = $service -> get_service($flash);
	foreach ($service_list as $k=>$v) {
		$service_arr[$v['id']] = $v['name'];
		if($v['parent_id'] == 0){
			$service_1_arr[] = $v;
			$service_1_ids[] = $v['id'];
		} 
	}
	foreach ($service_1_arr as $k=>$v){
		$service_1_id = $v['id'];
		$r = $service -> get_children_service($service_1_id, $flash);
		if(is_array($r) && $r){	
			foreach ($r as $kk=>$vv){
				$service_2_ids[$service_1_id][] = $vv['id'];
				$service_all[$service_1_id][$kk]['id'] = $vv['id'];
				$service_all[$service_1_id][$kk]['name'] = $vv['name'];
				$service_all[$service_1_id][$kk]['sort'] = $vv['sort'];
				$service_all[$service_1_id][$kk]['name_r'] = urlencode($vv['name']);
				$service_all[$service_1_id][$kk]['parent_id'] = $service_1_id;
				$rr = $service -> get_children_service($vv['id'], $flash);
				if(is_array($rr) && $rr){
					foreach ($rr as $kkk=>$vvv){
						$service_3_ids[$vv['id']][] = $vvv['id']; 
						$service_all[$service_1_id][$kk]['children'][$kkk]['id'] = $vvv['id'];
						$service_all[$service_1_id][$kk]['children'][$kkk]['name'] = $vvv['name'];
						$service_all[$service_1_id][$kk]['children'][$kkk]['name_r'] = urlencode($vvv['name']);
						$service_all[$service_1_id][$kk]['children'][$kkk]['parent_id'] = $vv['id'];
					}
				}
			}
		}
	}
	
	
	
	//角色列表
	$parent_role_list_all = $base -> get_parent_role_list();
	foreach($parent_role_list_all as $role_info){
		if($role_info['name'] != '其他'){
			$parent_role_list[] = $role_info;
			$parent_role_ids[] = $role_info['id']; 	
		}
	}
	$sys_role_list = $base ->  get_sys_role_list();
	foreach($sys_role_list as $sys_role){
		if($sys_role['parent_id'] != '0'){
			$role_2_list[$sys_role['parent_id']][$sys_role['id']] = $sys_role;
			$role_2_ids[$sys_role['parent_id']][] = $sys_role['id'];
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


	//未选择条件
	$find_user_key[] = $find_user -> get_find_key('all','');
	if(isset($_REQUEST['city'])){
		$city = intval($_REQUEST['city']);
		if($city > 0){
			$find_user_key[] = $find_user -> get_find_key('city',$city);
		}
	}else{
		$city = 0;	
	}
	if($city == 0 || array_key_exists($city,$chief_city_list)){
		$other_color = "";
	}else{
		$other_color = "red";	
	}
	//一级角色选择
	isset($_REQUEST['role_1_limit']) ? $role_1_limit = clear_gpq($_REQUEST['role_1_limit']) : $role_1_limit = 'no';
	if($role_1_limit == 'no'){
		$role_1_option = '0';
	}else if($role_1_limit == 'yes'){
		if(isset($_REQUEST['role_1_option']) && in_array($_REQUEST['role_1_option'],$parent_role_ids)){
			$role_1_option = intval($_REQUEST['role_1_option']);
			$find_user_key[] = $find_user -> get_find_key('level_1_role',$role_1_option);	
		}else{
			$role_1_option = '0';
		}	
	}else{
		$role_1_option = '0';
	}
	//二级角色选择
	$role_2_option = array();
	if($role_1_option > 0 && isset($_REQUEST['role_2_option']) && is_array($_REQUEST['role_2_option'])){
		foreach($_REQUEST['role_2_option'] as $v){
			if(in_array($v,$role_2_ids[$role_1_option])){
				$role_2_option[] = intval($v);
				$find_user_key[] = $find_user -> get_find_key('level_2_role',intval($v));
			}
		}
	}
	
	//一级服务选择
	isset($_REQUEST['service_1_limit']) ? $service_1_limit = clear_gpq($_REQUEST['service_1_limit']) : $service_1_limit = 'no';
	if($service_1_limit == 'no'){
		$service_1_option = '0';
	}else if($service_1_limit == 'yes'){
		if(isset($_REQUEST['service_1_option']) && in_array($_REQUEST['service_1_option'],$service_1_ids)){
			$service_1_option = intval($_REQUEST['service_1_option']);
			$find_user_key[] = $find_user -> get_find_key('level_1_service',$service_1_option);	
		}else{
			$service_1_option = '0';
		}	
	}else{
		$service_1_option = '0';
	}
	if($service_1_option > 0){
		$service_2_list = $service_2_ids[$service_1_option];
	}else{
		$service_2_list = array();
	}
	//二级服务选择
	$service_2_option = '';
	if($service_1_option > 0 && isset($_REQUEST['service_2_option'])){
		if(in_array($_REQUEST['service_2_option'],$service_2_ids[$service_1_option])){
			$service_2_option = intval($_REQUEST['service_2_option']);
			$find_user_key[] = $find_user -> get_find_key('level_2_service',$service_2_option);	
		}
	}
	if($service_2_option > 0){
		$service_3_list = $service_3_ids[$service_2_option];
	}else{
		$service_3_list = array();
	}
	//三级服务选择
	$service_3_option = array();
	if($service_2_option > 0 && isset($_REQUEST['service_3_option']) && is_array($_REQUEST['service_3_option'])){
		foreach($_REQUEST['service_3_option'] as $v){
			if(in_array($v,$service_3_ids[$service_2_option])){
				$service_3_option[] = intval($v);
				$find_user_key[] = $find_user -> get_find_key('level_3_service',intval($v));
			}
		}
	}
	
	
	//年龄选项
	isset($_REQUEST['age_option']) ? $age_option = clear_gpq($_REQUEST['age_option']) : $age_option = 'nolimit';
	$age_option_arr = explode('-',$age_option);
	if(array_key_exists($age_option,$COMMON_CONFIG['AGE']['OPTION'])){
		$age_i = intval(array_shift($age_option_arr));
		do{
			$age_key_arr[] = $find_user -> get_find_key('age',$age_i);
			$age_i ++;
		}while($age_i <= intval(array_pop($age_option_arr)));
		$age_tmp_key = $find_user -> get_key_by_sunion($age_key_arr);
		$find_user_key[] = $age_tmp_key;
	}else{
		$age_option = 'nolimit';
	}
	
	//身高选项
	isset($_REQUEST['height_option']) ? $height_option = clear_gpq($_REQUEST['height_option']) : $height_option = 'nolimit';
	$height_option_arr = explode('-',$height_option);
	if(array_key_exists($height_option,$COMMON_CONFIG['HEIGHT']['OPTION'])){
		$height_i = intval(array_shift($height_option_arr));
		do {
			$height_key_arr[] = $find_user -> get_find_key('height',$height_i);
			$height_i ++;	
		} while($height_i <= intval(array_pop($height_option_arr)));
		$height_tmp_key = $find_user -> get_key_by_sunion($height_key_arr);
		$find_user_key[] = $height_tmp_key;
	}else{
		$height_option = 'nolimit';	
	}
	
	//星座选项
	isset($_REQUEST['star_option']) ? $star_option = clear_gpq($_REQUEST['star_option']) : $star_option = 'nolimit';
	if(array_key_exists($star_option,$COMMON_CONFIG['STAR'])){
		$find_user_key[] = $find_user -> get_find_key('star',$star_option);	
	}else{
		$star_option = 'nolimit';	
	}
	//性别选项
	isset($_REQUEST['sex_option']) ? $sex_option = clear_gpq($_REQUEST['sex_option']) : $sex_option = 'nolimit';
	if(array_key_exists($sex_option,$COMMON_CONFIG['SEX'])){
		$find_user_key[] = $find_user -> get_find_key('sex',$sex_option);	
	}else{
		$sex_option = 'nolimit';
	}
	//身份验证选项
	isset($_REQUEST['verify_option']) ? $verify_option = clear_gpq($_REQUEST['verify_option']) : $verify_option = 'nolimit';
	if($verify_option == 'yes' || $verify_option == 'no'){
		$find_user_key[] = $find_user -> get_find_key('verify',$verify_option);	
	}else{
		$verify_option = 'nolimit';	
	}
	$district_list = array();
	$city_list = array();
	
	//分页开始
	if(!empty($find_user_key)){
		$find_user_res = $find_user -> get_user_list_by_sinter($find_user_key,'1','10');
		$find_user_id = $find_user_res['list'];
		$find_user_count = $find_user_res['count'];
	}else{
		$find_user_id = array();
		$find_user_count = 0;	
	}
	$pagesize = 50;
	$sum_page =  ceil($find_user_count/$pagesize);
	isset($_REQUEST['page']) ? $page = intval($_REQUEST["page"]) : $page = 1;
	if($page < 1){
		$page = 1;
	}else if($page > $sum_page){
		$page = $sum_page;
	}


	//1 获取当前url
	$url = $_SERVER['REQUEST_URI'];
	if(strstr($url,"?page")){
		//包含?page  干掉 换？page= 
		$arr = explode('?page',$url);
		$url = $arr[0]."?page=";
	}elseif(strstr($url,"&page")){
		//有&page  干掉  换&page=
		$arr = explode('&page',$url);
		$url = $arr[0]."&page=";
	}elseif(strstr($url,"?")){
		// 直接加？page
		$url = $url."&page=";
	}else{
		$url = $url."?page=";
	}
	$page_list = $base -> getPaging($url.'$page',$page, $sum_page, 2);
	// 一个分页不显示标志
	if(count($page_list) < 2){
		$page_status = 'false';
	}else{
		$page_status = 'true';
	}
	$page_first_url = "{$url}1";
	$page_last_url = "{$url}".$sum_page;
	($page > 1) ? $page_pre_url = $url.($page - 1) : $page_pre_url = $page_first_url;
	($page < $sum_page) ? $page_next_url = $url.($page + 1) : $page_next_url = $page_last_url;

	$smarty -> assign('page_status',$page_status);
	$smarty -> assign('page_list', $page_list);		#页数数组
	$smarty -> assign('page_first_url', $page_first_url);	
	$smarty -> assign('page_last_url', $page_last_url);		
	$smarty -> assign('page_pre_url', $page_pre_url);		
	$smarty -> assign('page_next_url', $page_next_url);
	//$smarty -> assign('goto_url', $page_prefix_url);
	$smarty -> assign('goto_url', $url);
	$smarty -> assign('sum_page', $sum_page);
	if(!empty($find_user_key)){
		$find_user_res = $find_user -> get_user_list_by_sinter($find_user_key,$page,$pagesize);
		$find_user_id = $find_user_res['list'];
		$find_user_count = $find_user_res['count'];
	}else{
		$find_user_id = array();
		$find_user_count = 0;	
	}
	$find_user_list = array();
	foreach($find_user_id as $uid){
		$userinfo = $user -> get_userinfo($uid);
		$user_profile = $userprofile -> get_user_profile($uid);
		is_array($user_role_list = $userprofile -> get_role_list_by_user($uid,$flash)) ? null : $user_role_list = array();
		$role_str = '';
		foreach($user_role_list as $role_info){
			$role_str .= $role_info['name'].'/'; 
		}
		$role_str = rtrim($role_str,'/');
		if(array_key_exists($user_profile['state'],$COMMON_CONFIG['STATE'])){
			$state = $COMMON_CONFIG["STATE"][$user_profile['state']];	
		}else{
			$state = '';
		}
		$address_info = $base -> get_address_info($user_profile['province_id'],$user_profile['city_id'],$user_profile['district_id']);
		$find_user_list[$uid] = array(
			'id' => $uid,
			'icon_url' => $userinfo['icon_server_url'].$userinfo['icon_path_url'],
			'nickname' => $userinfo['nickname'],
			'level' =>  $userinfo['level'],
			'email_status' => $userinfo['email_status'],
			'mobile_status' => $userinfo['mobile_status'],
			'identity_card_status' => $userinfo['identity_card_status'],
			'address' => $address_info['address'],
			'state' => $state,
			'role_str' => $role_str
		);		
	}	

	//删除tmp_key
	if(isset($age_tmp_key)) $find_user -> del_key($age_tmp_key);
	if(isset($height_tmp_key)) $find_user -> del_key($height_tmp_key);
	//所有已选项
	$all_option_list = array();
	foreach($citys as $city_spell){
		foreach($city_spell as $cid => $city_info){
			if($city == $cid){
				$city_name = $city_info['cname'];
				$all_option_list[] = array('city',$city,$city_name);
			}
		}
	}
	foreach($parent_role_list as $role_1_list){
		if($role_1_option == $role_1_list['id']){
			$role_1_name = $role_1_list['name'];
		}
	}	
	if($role_1_option != '0') $all_option_list[] = array('role_1_option',$role_1_option,$role_1_name);
	if(!in_array('nolimit',$role_2_option)){
		foreach($role_2_option as $role_2_info){
			$all_option_list[] = array('role_2_option[]',$role_2_info,$role_2_list[$role_1_option][$role_2_info]['name']);	
		}
	}
	//服务已选择
	
	if($service_1_option > 0) $all_option_list[] = array('service_1_option',$service_1_option,$service_arr[$service_1_option]);
	if($service_2_option > 0) $all_option_list[] = array('service_2_option',$service_2_option,$service_arr[$service_2_option]);
	foreach($service_3_option as $service_3_info){
		$all_option_list[] = array('service_3_option[]',$service_3_info,$service_arr[$service_3_info]);	
	}

	if($age_option != 'nolimit') $all_option_list[] = array('age_option',$age_option,$COMMON_CONFIG['AGE']['OPTION'][$age_option].'岁');
	if($height_option != 'nolimit') $all_option_list[] = array('height_option',$height_option,$COMMON_CONFIG['HEIGHT']['OPTION'][$height_option].'CM');
	if($star_option != 'nolimit') $all_option_list[] =  array('star_option',$star_option,$COMMON_CONFIG['STAR'][$star_option]);
	if($sex_option != 'nolimit') $all_option_list[] = array('sex_option',$sex_option,$COMMON_CONFIG['SEX'][$sex_option]);
	if($verify_option == 'yes'){
		$all_option_list[] = array('verify_option',$verify_option,"已验证");
	}else if($verify_option == 'no'){
		$all_option_list[] = array('verify_option',$verify_option,"未验证");	
	} 
	$smarty -> assign('common_config',$COMMON_CONFIG);
	$smarty -> assign('find_user_list',$find_user_list);
	$smarty -> assign('role_1_option',$role_1_option);
	$smarty -> assign('role_2_option',$role_2_option);
	$smarty -> assign('service_1_option',$service_1_option);
	$smarty -> assign('service_2_option',$service_2_option);
	$smarty -> assign('service_3_option',$service_3_option);
	$smarty -> assign('age_option',$age_option);
	$smarty -> assign('height_option',$height_option);
	$smarty -> assign('star_option',$star_option);
	$smarty -> assign('sex_option',$sex_option);
	$smarty -> assign('verify_option',$verify_option);
	$smarty -> assign('city_option',$city);
	$smarty -> assign('parent_role_list',$parent_role_list);
	$smarty -> assign('service_1_list',$service_1_ids);
	$smarty -> assign('service_2_list',$service_2_list);
	$smarty -> assign('service_3_list',$service_3_list);
	$smarty -> assign('service_names',$service_arr);
	$smarty -> assign('role_2_list',$role_2_list);
	$smarty -> assign('citys',$citys);
	$smarty -> assign('all_option_list',$all_option_list);
	//var_dump($all_option_list);
	$smarty -> assign('chief_city_list',$chief_city_list);
	$smarty -> assign('other_color',$other_color);
	$smarty -> display("list/user_list.html");
     
?>   