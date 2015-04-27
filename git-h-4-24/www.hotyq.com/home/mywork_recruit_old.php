<?php
	header("content-type: text/html; charset=utf-8");
	session_start();
//	require_once ('../includes/common_home_inc.php');
	require_once('../includes/common_home_inc.php');
	
	
	$user = new user;
	$apply = new apply;
	$recruit = new recruit;
	$invite = new invite;
	
	$userid = $user_info['id'];
	//$userid = 45618;
	$usertype = $user_info['user_type'];
	if($usertype == 'user'){
		$userprofile = new userprofile();
		$info = $userprofile -> get_user_profile($userid);
	}else{
		$orgprofile = new orgprofile();
		$info = $orgprofile -> get_org_profile($userid);
	}
	$smarty -> assign('connect',$info);
	
	//获取我发布的招募列表
	$recruit_list = $recruit -> get_recruit_list_by_user($userid);
	if($recruit_list){
		$smarty -> assign('recruit_list',$recruit_list);
	}else{
		$recruit_list = '';
		$smarty -> assign('recruit_list',$recruit_list);
	}
	
	
	
	
	$work_type = 'mywork_recruit'; 
	$smarty -> assign('work_type',$work_type);
	$smarty -> display("home/mywork_recruit.html");