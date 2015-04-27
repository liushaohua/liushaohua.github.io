<?php
	header("content-type: text/html; charset=utf-8");
	require_once ('../includes/common_inc.php');
	//接收传递的值 判断是邮箱还是手机  显示不同的div
	if(!isset($_GET['account'])){
		error_tips('1099');exit;
	}
	if(!isset($_GET['login_type'])){
		error_tips('1099');exit;
	}
	if($_GET['login_type'] != 'mobile' && $_GET['login_type'] != 'email'){
		error_tips('1099');exit;
	}
    $smarty -> assign('user_info',$_GET);
	$smarty -> display("account/forget_2.html")
?>