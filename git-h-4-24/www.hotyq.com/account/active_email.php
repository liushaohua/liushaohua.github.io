<?php
	header("Content-type:text/html;charset=utf-8");
	include "../includes/common_inc.php";
	//获取用户id 和 激活码 uid  email_check_code
	if( !isset($_GET['uid']) || empty($_GET['uid']) ){
		die('page:404');
	}
	if( !isset($_GET['email_check_code']) || empty($_GET['email_check_code']) ){
		die('page:404');
	}
	$uid = clear_gpq($_GET['uid']);
	$email_check_code = clear_gpq($_GET['email_check_code']);
	//调用user类里的email_active
	$user = new user;
	$result = $user->email_active($uid,$email_check_code);
	$smarty -> assign('result',$result);
	//var_dump($result);
	$smarty -> display("account/active_email.html");
?>