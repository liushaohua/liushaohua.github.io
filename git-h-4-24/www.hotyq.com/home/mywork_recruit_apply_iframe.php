<?php
	header("content-type: text/html; charset=utf-8");
	session_start();
	require_once('../includes/common_home_inc.php');	
	
	$user = new user;
	$userprofile = new userprofile;
	$apply = new apply;
	$recruit = new recruit;
	$cookie = $user -> get_cookie_user_info();
	
	$userid = $user_info['id'];
	
	$rid = intval($_REQUEST['id']);
	
	$recruit_info = $recruit -> get_recruit_info($rid);
	$apply_list = $apply -> get_apply_list_by_recruit($rid);
	if($apply_list){
		foreach($apply_list as $k=>$v){
			$uid = $v['uid'];
			$user_info = $user -> get_userinfo($uid);
			/*  错误处理   */
			$u_face = $user_info['icon_server_url'].$user_info['icon_path_url'];
			$v['u_face'] = $u_face;
			$v['u_name'] = $user_info['nickname'];
			$v['recruit_status'] = $recruit_info['status'];
			$v['recruit_end_time'] = $recruit_info['sys_end_time'];
			$v['user_type'] = $user_info['user_type'];
			$list[$v['role_1_id']]['parent_role_id'] = $v['role_1_id'];
			$role_info = $userprofile -> get_role_info($v['role_1_id']);
			/*  错误处理   */
			$list[$v['role_1_id']]['parent_role_name'] = $role_info['name'];
			@$list[$v['role_1_id']]['num'] += 1;
			$list[$v['role_1_id']]['apply_info'][] = $v;
		}
		$smarty -> assign('list',$list);
	}else{
		$list = '';
		$smarty -> assign('list',$list);
	}
	
	$smarty -> assign('rid',$rid);
	$smarty -> display("home/mywork_recruit_apply_iframe.html");