<?php
	header("content-type: text/html; charset=utf-8");
	session_start();
	require_once('../includes/common_inc.php');
	$user = new user();
	$user -> delete_cookie_user_info();
	$smarty -> display("account/register.html");
	
