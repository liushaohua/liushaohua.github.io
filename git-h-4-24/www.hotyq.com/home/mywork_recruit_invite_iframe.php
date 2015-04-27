<?php
	header("content-type: text/html; charset=utf-8");
	session_start();
	require_once('../includes/common_home_inc.php');	
	//require_once ("../../common/invite.class.php");	
	$invite = new invite();
	$user = new user();
	$userprofile = new userprofile();
	$recruit = new recruit;

	
	$rid = intval($_REQUEST['id']);
	
	$recruit_info = $recruit -> get_recruit_info($rid);
	$invite_list = $invite -> get_invite_list_by_recruit($rid);
	if($invite_list){
		foreach($invite_list as $k => $v){
			$uid = $v['uid'];
			$user_info = $user -> get_userinfo($uid);
			$role_id = $v['role_id'];
			$u_face = $user_info['icon_server_url'].$user_info['icon_path_url'];
			$invite_list[$k]['u_face'] = $u_face;
			$invite_list[$k]['u_name'] = $user_info['nickname'];
			$role_info = $userprofile -> get_role_info($role_id);
			$invite_list[$k]['role_name'] = $role_info['name'];
			$invite_list[$k]['uid'] = $user_info['id'];
			$invite_list[$k]['user_type'] = $user_info['user_type'];
		}
		$smarty -> assign('list',$invite_list);
	}else{
		$invite_list = '';
		$smarty -> assign('list',$invite_list);
	}		
	
	$smarty -> assign('rid',$rid);
	$smarty -> assign('recruit_info',$recruit_info);
	$smarty -> display("home/mywork_recruit_invite_iframe.html");