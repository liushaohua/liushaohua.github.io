<?php
	header("content-type: text/html; charset=utf-8");
	session_start();
	require_once ('../includes/common_home_inc.php');

	$user = new user;
	$base = new base;
	$userprofile = new userprofile;
	$orgprofile = new orgprofile;
	$invite = new invite;
	$recruit = new recruit;
	$cookie = $user -> get_cookie_user_info();
	//var_dump($cookie);
	$uid = $user_info["id"];
	$usertype = $user_info['user_type'];
	var_dump($uid);
	var_dump($usertype);
	if($usertype == 'user'){
		$user_msn = $userprofile -> get_user_profile($uid);
	}else if($usertype == 'org'){
		$user_msn = $orgprofile -> get_org_profile($uid);
	}
	$smarty -> assign('user_msn',$user_msn);
	//$invite_list = $invite -> get_invite_list_by_user(45678);
	//$recruit_list = $recruit -> get_recruit_list_by_user(45678);
	$where = array('uid'=>"{$uid}");
	$recruit_list = $recruit -> get_recruit_list_by_where($where);
	//var_dump($recruit_list);
	foreach($recruit_list as $k => $v){
	
			$recruit_invite_list[$k]['rid'] = $recruit_list[$k]['id'];
			$recruit_invite_list[$k]['recruit_name'] = $recruit_list[$k]['name'];
			$recruit_invite_list[$k]['r_uid'] = $recruit_list[$k]['uid'];	
	
	}
	$smarty -> assign('recruit_invite_list',$recruit_invite_list);
	//var_dump($recruit_invite_list);
	//$re = $recruit -> get_service_3_list_by_eid(5);

	$re = $base -> get_service_info(20);
	$ree = $base -> get_service_list_by_parentid(20);
	$smarty -> display("home/invite_box1.html");



  

	