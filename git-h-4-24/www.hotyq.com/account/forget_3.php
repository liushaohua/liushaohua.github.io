<?php
	session_start();
	header("content-type: text/html; charset=utf-8");
	require_once ('../includes/common_inc.php');
	//接收传递的值 判断是邮箱还是手机  显示不同的div
	//var_dump($_SESSION);
	//$_GET['account'] = clear_gpq($_GET['account']);
	if(!isset($_GET['login_type'])){
		error_tips('10991');exit;
	}
	if(!isset($_SESSION['mobile'])){
		error_tips('10992');exit;
	}
	if($_GET['login_type'] != 'mobile' && $_GET['login_type'] != 'email'){
		error_tips('10993');exit;
	}
	$_GET['login_type'] = clear_gpq($_GET['login_type']);
    $smarty -> assign('account_info',$_GET);
    $smarty -> assign('mobile_session_info',$_SESSION);
	$smarty -> display("account/forget_3.html")
?>