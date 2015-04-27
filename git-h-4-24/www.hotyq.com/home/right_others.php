<?php
	header("content-type: text/html; charset=utf-8");
  	require_once('../includes/common_inc.php');
	require_once (COMMON_PATH.'/page.class.php');
	require_once (COMMON_PATH.'/album.class.php');
  	session_start();
    $user = new user();	#get_userinfo
    $album = new album();
    $orgprofile = new orgprofile();
    $userprofile = new userprofile();
    $base = new base();
    $recruit = new recruit();    
    //猜你喜欢右侧 招募的右侧
    $r_uid_list = Array('13','14','15','16','18','17');
    //$result = $recruit -> get_recruit_info(52);
    //var_dump($id_list);
    foreach ($r_uid_list as $k => $v) {
    	$recruit_info = $recruit -> get_recruit_info($v);
    	$recruit_r_list[$k]['rid'] = $recruit_info['id'];
    	$recruit_r_list[$k]['name'] = $recruit_info['name'];
    	$recruit_r_list[$k]['cover_server_url'] = $recruit_info['cover_server_url'];   
    	$recruit_r_list[$k]['cover_path_url'] = $recruit_info['cover_path_url'];
    	$user_info_result = $user -> get_userinfo($recruit_info['uid']);
    	$recruit_r_list[$k]['u_name'] = $user_info_result['nickname'];
    	$recruit_r_list[$k]['interview_end_time'] = date( "m月d日", strtotime($recruit_info['interview_end_time']));

    }
    //var_dump($recruit_r_list);
    $smarty -> assign('recruit_r_list',$recruit_r_list);
    //彩泥喜欢右侧   红人
	$u_uid_list = Array('45724','45770','45803','45661');
	foreach ($u_uid_list as $k => $v) {
		$user_info_res = $user -> get_userinfo($v);
		$user_r_list[$k]['uid'] = $user_info_res['id'];
		$user_r_list[$k]['name'] = $user_info_res['nickname'];
		$user_r_list[$k]['icon_server_url'] = $user_info_res['icon_server_url'];   
    	$user_r_list[$k]['icon_path_url'] = $user_info_res['icon_path_url'];
	}
	//var_dump($user_r_list);
	$smarty -> assign('user_r_list',$user_r_list);

	//红人榜	三个榜
	//周榜
	$week_red_list = $user -> get_user_toplist('week');	
	foreach($week_red_list as $k => $v){
		$week_red_list[$k] = $v['des'];
	}
	foreach($week_red_list as $k => $v){
		$week_red_list[$k] = $user -> get_userinfo($v);
	}	
	//var_dump($week_red_list);
	foreach ($week_red_list as $k => $v) {
		$week_list[$k]['id'] = $v['id'];
		$week_list[$k]['key'] = $k + 1;
		$week_list[$k]['user_type'] = $v['user_type'];
		$week_list[$k]['nickname'] = $v['nickname'];
		$week_list[$k]['level'] = $v['level'];
		$week_list[$k]['icon_server_url'] = $v['icon_server_url'];
		$week_list[$k]['icon_path_url'] = $v['icon_path_url'];
		$userlist =	$userprofile ->get_user_profile($week_list[$k]['id']);
		$address_info =  $base -> get_address_info($userlist['province_id'], $userlist['city_id'], $userlist['district_id'], $flash);
		$week_list[$k]['address'] = $address_info['city_info']['cname'];
		$week_list[$k]['role_name'] = $userprofile -> get_role_list_by_user($week_list[$k]['id']);
	} 
	$smarty -> assign('week_list',$week_list);
	//var_dump($address_info);
	//月榜
	$mouth_red_list = $user -> get_user_toplist('mouth');	
	foreach($mouth_red_list as $k => $v){
		$mouth_red_list[$k] = $v['des'];
	}
	foreach($mouth_red_list as $k => $v){
		$mouth_red_list[$k] = $user -> get_userinfo($v);
	}	
	//var_dump($week_red_list);
	foreach ($mouth_red_list as $k => $v) {
		$mouth_list[$k]['id'] = $v['id'];
		$mouth_list[$k]['key'] = $k + 1;
		$mouth_list[$k]['user_type'] = $v['user_type'];
		$mouth_list[$k]['nickname'] = $v['nickname'];
		$mouth_list[$k]['level'] = $v['level'];
		$mouth_list[$k]['icon_server_url'] = $v['icon_server_url'];
		$mouth_list[$k]['icon_path_url'] = $v['icon_path_url'];
		$userlist =	$userprofile ->get_user_profile($mouth_list[$k]['id']);
		$address_info =  $base -> get_address_info($userlist['province_id'], $userlist['city_id'], $userlist['district_id'], $flash);
		$mouth_list[$k]['address'] = $address_info['city_info']['cname'];
		$mouth_list[$k]['role_name'] = $userprofile -> get_role_list_by_user($mouth_list[$k]['id']);
	} 
	//var_dump($mouth_list);
	$smarty -> assign('mouth_list',$mouth_list);	
	//new榜
	$new_red_list = $user -> get_user_toplist('new');	
	foreach($new_red_list as $k => $v){
		$new_red_list[$k] = $v['des'];
	}
	foreach($new_red_list as $k => $v){
		$new_red_list[$k] = $user -> get_userinfo($v);
	}	
	//var_dump($week_red_list);
	foreach ($new_red_list as $k => $v) {
		$new_list[$k]['id'] = $v['id'];
		$new_list[$k]['key'] = $k + 1;
		$new_list[$k]['user_type'] = $v['user_type'];
		$new_list[$k]['nickname'] = $v['nickname'];
		$new_list[$k]['level'] = $v['level'];
		$new_list[$k]['icon_server_url'] = $v['icon_server_url'];
		$new_list[$k]['icon_path_url'] = $v['icon_path_url'];
		$userlist =	$userprofile ->get_user_profile($new_list[$k]['id']);
		$address_info =  $base -> get_address_info($userlist['province_id'], $userlist['city_id'], $userlist['district_id'], $flash);
		$new_list[$k]['address'] = $address_info['city_info']['cname'];
		$new_list[$k]['role_name'] = $userprofile -> get_role_list_by_user($new_list[$k]['id']);
	} 
	$smarty -> assign('new_list',$new_list);		



	$smarty -> display("home/right_others.html");

?>
