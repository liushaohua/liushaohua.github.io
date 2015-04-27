<?php
	header("content-type: text/html; charset=utf-8");
	session_start();
	require_once ('../includes/common_home_inc.php');
	require_once ('../../common/userprofile.class.php');
	require_once ('../../common/album.class.php');
	$user = new user();
	$base = new base();
	$album = new album();
	$userprofile = new userprofile();
	$userid = $user_info['id'];
	//--------------------------------------wangyifan  start----------
	//读取该用户已存入的角色  组成json  分配过去  改变js初始
	$role_list['roles'] = $userprofile -> get_role_list($flash);#角色字典
	$sys_role = $userprofile -> get_role_list_by_user($userid,$flash);#所选系统
	//var_dump($sys_role);
	$self_role = $userprofile -> get_self_role_by_user($userid);#自定义
	//var_dump($self_role);
	//拆分成两种json
	$role_name = array();
	if( empty($sys_role) ){
		$role_list['userRoles'] = null;
	}else{
		foreach($sys_role as $v){
			$role_list['userRoles'][] = $v['id'];#用户所选系统  只id
			$role_name[] = $v['name'];#浏览模式要分配的
		}
	}
	if( empty($self_role) ){
		$role_list['userCustomRoles'][] = null;
	}else{
		foreach($self_role as $k => $v){
			$result = $userprofile -> get_role_info($v['role_id']);
			$self_role[$k]['name'] = $result['name'];
		}
		$role_list['userCustomRoles'][] = $self_role[0]['name'];#自定义  只name
		$role_name[] = $self_role[0]['name'];
	}
	//var_dump($role_list['userRoles']);
	$init_data = json_encode($role_list);
	$json_role_name = json_encode($role_name);
	$smarty -> assign('init_data',$init_data);#初始化json
	$smarty -> assign('role_name',$role_name);#浏览模式
	$smarty -> assign('json_role_name',$json_role_name);#json  name
	$smarty -> assign("edit_part","role");#左侧栏 处理变红
	
	$smarty -> assign("userid",$userid);#uid
	//--------------------------------------wangyifan   end---------------
	$smarty -> display("home/user_profile_role.html");
?>