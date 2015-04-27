<?php
	header("content-type: text/html; charset=utf-8");
	session_start();
	require_once('../includes/common_home_inc.php');

	$user = new user;
	$apply = new apply;
	$recruit = new recruit;

	//$cookie = $user -> get_cookie_user_info();
	//var_dump($cookie);
	$uid = $user_info["id"];
	$to_userid = intval($_REQUEST['to']);
	$info = $user -> get_userinfo($to_userid);
	if($info){
		$to_name = $info['nickname'];
	}else{
		$to_name = '';
	}
	
	$smarty -> assign('user_name',$to_name);
	$smarty -> assign('receiver_id',$to_userid);
	$smarty -> display("home/message_box_iframe.html");



  

	