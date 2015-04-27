<?php
	header("content-type: text/html; charset=utf-8");
	require('../includes/common_home_inc.php');
	session_start();
	
	$message = new message;
	$user = new user;
	$user_msg_total = new user_msg_total;
	//当前用户的id
	$user_id = $user_info['id'];
	@$friend_id = intval($_REQUEST['to']);
	if($friend_id < 1){
		exit('系统错误!ID不能空!!!');
	}
	$uinfo = $user -> get_userinfo($user_id,$flash = 1);
	$finfo = $user -> get_userinfo($friend_id,$flash = 1);
	$friend_arr[0]['id'] = $friend_id;
	$friend_arr[0]['username'] = $finfo['nickname'];
	$friend_arr[0]['portrait'] = $finfo['icon_server_url'].$finfo['icon_path_url'];
	
	$smarty -> assign('fid',$friend_id);
	$smarty -> assign('fname',$finfo['nickname']);
	$smarty -> assign('fface',$finfo['icon_server_url'].$finfo['icon_path_url']);
	$smarty -> assign('flist',json_encode($friend_arr));
	
	$smarty -> assign('uid',$uinfo['id']);
	$smarty -> assign('uface',$uinfo['icon_server_url'].$uinfo['icon_path_url']);
	$smarty -> assign('uname',$uinfo['nickname']);
	$smarty -> assign('utoken',$uinfo['rongyun_token']);
	$smarty -> display("home/private_message.html");


	