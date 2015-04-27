<?php
// 红名片页面
	$PAGE_TYPE = "user";
	header("content-type: text/html; charset=utf-8");
	session_start();
	require_once ('../../includes/common_home_inc.php');
	require_once ('../../../common/userprofile.class.php');


	$base = new base();
	$userprofile = new userprofile();
	//var_dump($user_info);
	//获取cookie中的id 和 type

	$week_red_list = $userprofile -> get_similar_user_list();
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
		$week_list[$k]['address'] = $address_info['address'];
		$week_list[$k]['role_name'] = $userprofile -> get_role_list_by_user($week_list[$k]['id']);
	}  

	//var_dump($week_list);
	$smarty -> assign('week_list',$week_list);
	//-------------------------------------   end---------------
	$smarty -> display("suntianxing/red_toplist.html");
?>