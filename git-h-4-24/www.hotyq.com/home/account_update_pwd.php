<?php

	header("content-type: text/html; charset=utf-8");
	require_once('../includes/common_home_inc.php');
	session_start();
	$user = new user();

    $account_update = 'pwd';		
	$smarty -> assign('account_update',$account_update);
	$smarty -> display("home/account_update_pwd.html");

?>