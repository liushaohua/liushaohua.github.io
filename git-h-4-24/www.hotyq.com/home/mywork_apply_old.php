<?php
	header("content-type: text/html; charset=utf-8");
	session_start();
	require_once ('../includes/common_home_inc.php');

	$user = new user;
	$userprifile = new userprofile;
	$apply = new apply;
	$recruit = new recruit;

	$cookie = $user -> get_cookie_user_info();
	//var_dump($cookie);
	$uid = $user_info["id"];
	//$uid = 45618;
	$apply_list = $apply -> get_apply_list_by_user($uid);
	if($apply_list){
		foreach($apply_list as $k=>$v){
			$rid = $v['recruit_id'];
			$role_id = $v['role_id'];
			$recruit_info = $recruit -> get_recruit_info($rid);
			/*  错误处理   */
			if($recruit_info){
				$uid = $recruit_info['uid'];
				$apply_list[$k]['recruit_name'] = $recruit_info['name'];
				$apply_list[$k]['status'] = $recruit_info['status'];
				$user_info = $user -> get_userinfo($uid);
				/*  错误处理   */
				$apply_list[$k]['user_type'] = $user_info['user_type'];
				$u_face = $user_info['icon_server_url'].$user_info['icon_path_url'];
				$apply_list[$k]['u_face'] = $u_face;
				$role_info = $userprifile -> get_role_info($role_id);
				/*  错误处理   */
				$apply_list[$k]['role_name'] = $role_info['name'];
			}
		}
		$smarty -> assign('list',$apply_list);
	}else{
		$apply_list = '';
		$smarty -> assign('list',$apply_list);
	}
	$work_type = 'mywork_apply'; 
	$smarty -> assign('work_type',$work_type);
	//print_r($apply_list);
	//exit;
	$smarty -> assign('uid',$uid);
	$smarty -> display("home/mywork_apply.html");



  

	