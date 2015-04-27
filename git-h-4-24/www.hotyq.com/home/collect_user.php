<?php
	header("content-type: text/html; charset=utf-8");
	require_once ('../includes/common_home_inc.php');
	require_once (COMMON_PATH.'/userprofile.class.php');
	require_once (COMMON_PATH.'/base.class.php');
	require_once (COMMON_PATH.'/collect.class.php');
	$user = new user();
	$base = new base();
	$userprofile = new userprofile();
	$collect = new collect();
	$recruit = new recruit();
	$collect_users =  $collect -> get_collect_list_by_user_type($user_info["id"],'user');
	$collect_user_list = array();
	isset($_REQUEST['role_parent']) ? $role_parent = intval($_REQUEST['role_parent']) :  $role_parent = '0';
	if($collect_users){
		foreach($collect_users as $collect_user){
			$userinfo = $user -> get_userinfo($collect_user['dynamic_id']);
			$user_profile = $userprofile -> get_user_profile($collect_user['dynamic_id']);
			$address_info = $base -> get_address_info($user_profile['province_id'],$user_profile['city_id'],$user_profile['district_id']);
			is_array($user_role_list = $userprofile -> get_role_list_by_user($collect_user['dynamic_id'],$flash)) ? null : $user_role_list = array();
			$role_data_parent = array();
			$user_role = array(); 
			foreach($user_role_list as $role_info){
				$user_role[] = $role_info['name'];
				if($role_info['parent_id'] == '0'){
					$role_data_parent[] = $role_info['id'];
				}else{
					$role_data_parent[] = $role_info['parent_id'];
				}
			}
			$collect_user_list['0'][] = array(
				'collect_id' => $collect_user['id'],
				'dynamic_id' => $collect_user['dynamic_id'],
				'icon_url' => $userinfo['icon_server_url'].$userinfo['icon_path_url'],
				'nickname' => $userinfo['nickname'],
				'level' =>  $userinfo['level'],
				'email_status' => $userinfo['email_status'],
				'mobile_status' => $userinfo['mobile_status'],
				'identity_card_status' => $userinfo['identity_card_status'],
				'address' => $address_info['address'],
				'user_role' => $user_role,
				'role_data_parent' => $role_data_parent
			);
			$role_data_parent = array_unique($role_data_parent);			
			foreach($role_data_parent as $role_1_id){
				$collect_user_list[$role_1_id][] = array(
					'collect_id' => $collect_user['id'],
					'dynamic_id' => $collect_user['dynamic_id'],
					'icon_url' => $userinfo['icon_server_url'].$userinfo['icon_path_url'],
					'nickname' => $userinfo['nickname'],
					'level' =>  $userinfo['level'],
					'email_status' => $userinfo['email_status'],
					'mobile_status' => $userinfo['mobile_status'],
					'identity_card_status' => $userinfo['identity_card_status'],
					'address' => $address_info['address'],
					'user_role' => $user_role,
					'role_data_parent' => $role_data_parent
				);				
			}
		}
	}
	if(!array_key_exists($role_parent, $collect_user_list)){
		 $collect_user_list[$role_parent] = array();
	}
	//分页
	$recruit_num = count($collect_user_list[$role_parent]);#招募总数
	$pagesize = 10;
	$sum_page =  ceil($recruit_num/$pagesize);	#分页总数
	isset($_REQUEST['page']) ? $page = intval($_REQUEST["page"]) : $page = 1;
	if($page < 1){
		$page = 1;
	}else if($page > 1 && $page > $sum_page){
		$page = $sum_page;
	}
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
	$smarty -> assign('page_status',$page_status);
	$page_first_url = "{$url}1";
	$page_last_url = "{$url}".$sum_page;
	($page > 1) ? $page_pre_url = $url.($page - 1) : $page_pre_url = $page_first_url;
	($page < $sum_page) ? $page_next_url = $url.($page + 1) : $page_next_url = $page_last_url;
	
	$smarty -> assign('page_list', $page_list);		#页数数组
	$smarty -> assign('page_first_url', $page_first_url);
	$smarty -> assign('page_last_url', $page_last_url);
	$smarty -> assign('page_pre_url', $page_pre_url);
	$smarty -> assign('page_next_url', $page_next_url);
	$smarty -> assign('goto_url', $url);
	$smarty -> assign('sum_page', $sum_page);
	//2 在根据当前分页page  显示数据
	$from_rows = ($page - 1) * $pagesize;
	$collect_user_show = array();
	$collect_user_show = array_slice($collect_user_list[$role_parent],$from_rows,$pagesize);
	//别乱动  
	$recruit_list = $recruit -> get_recruit_list_by_user_for_invite($user_info["id"]);
	if(!$recruit_list){
		$smarty -> assign('hasRecruit','');
	}else{
		$smarty -> assign('hasRecruit','OK');
	}

	$smarty -> assign('collect_user_list',$collect_user_show);
	$smarty -> assign('user_info',$user_info);
	$smarty -> assign("role_parent",$role_parent);
	$smarty -> assign('collect_type','user');
	$smarty -> display("home/collect_user.html");

?>