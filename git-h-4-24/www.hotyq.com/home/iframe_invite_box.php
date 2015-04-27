<?php
	header("content-type: text/html; charset=utf-8");
	session_start();
	require_once('../includes/common_home_inc.php');

	$user = new user;
	$apply = new invite;
	$recruit = new recruit;
	
	$uid = $user_info["id"];
	$usertype = $user_info['user_type'];

	$to_userid = intval($_REQUEST['to']);
	$info = $user -> get_userinfo($to_userid);
	if($info){
		$to_name = $info['nickname'];
		$to_usertype = $info['user_type'];
	}else{
		$to_name = '';
		$to_usertype = '';
	}
	

	$recruit_list = $recruit -> get_recruit_list_by_user_for_invite($uid);
	if($recruit_list){
		$smarty -> assign('recruit_list',$recruit_list);
	}else{
		$smarty -> assign('recruit_list','');
	}
	//我的联系方式
	if($usertype == 'user'){
		$userprofile = new userprofile;
		$info = $userprofile -> get_user_profile($uid);
	}else{
		$orgprofile = new orgprofile();
		$info = $orgprofile -> get_org_profile($uid);
	}
	$smarty -> assign('connect',$info);




	$smarty -> assign('user_name',$to_name);
	$smarty -> assign('user_type',$to_usertype);
	$smarty -> assign('receiver_id',$to_userid);
	$smarty -> display("home/invite_box_iframe.html");	