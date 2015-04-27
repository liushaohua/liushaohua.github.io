<?php

	header("content-type: text/html; charset=utf-8");
	session_start();
	require_once ('../../includes/common_inc.php');
	require_once COMMON_PATH."/userprofile.class.php";	
	$user = new user();
	$userprofile = new userprofile();
	$userprofile = $userprofile ->	get_self_role_by_user(10);
	$re = $user ->get_userinfo(10);
	var_dump($userprofile);
	echo '<hr>';	
	$pwd =  $user ->get_user_password(45769);
	var_dump($pwd);
	echo $pwd;
	//$smarty -> display("suntianxing/zzh/3.html");