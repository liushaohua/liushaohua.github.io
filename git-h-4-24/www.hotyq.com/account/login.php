<?php
	session_start();
	header("content-type: text/html; charset=utf-8");
	require('../includes/common_inc.php');
	if(!isset($_SESSION['error_login_count'])){
		$_SESSION['error_login_count'] = 0;
	}
	$user = new user;
	$user -> delete_cookie_user_info();
    $smarty -> assign('error_login_count',$_SESSION['error_login_count']);
	
	$url = '';
	if(isset($_GET['url'])){
		$url = $_GET['url'];
	}
    $smarty -> assign('url',$url);
	
	$smarty -> display("account/login.html");
?>