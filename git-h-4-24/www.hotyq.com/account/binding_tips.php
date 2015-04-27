<?php
	header("Content-type:text/html;charset=utf-8");
	require_once("../includes/common_inc.php");
	$user = new user();
	$user_info =  $user -> get_cookie_user_info();
	session_start();
	if(isset($_REQUEST['action'])){
		$action = clear_gpq($_REQUEST['action']);
	}else{
		error_tips(1099);//非法操作
		exit;
	}
	if(isset($_REQUEST['account'])){
		$account = clear_gpq($_REQUEST['account']);
	}else{
		error_tips(1099);//非法操作
		exit;
	}
	if(isset($_REQUEST['login_type'])){
		$login_type = clear_gpq($_REQUEST['login_type']);
	}else{
		error_tips(1099);//非法操作
		exit;
	}
	if(isset($_SESSION['sns_username'])){
		$sns_username = $_SESSION['sns_username'];
	}else{
		error_tips(1099);//非法操作
		exit;
	}
	if(isset($_SESSION['sns_username'])){
		$sns_type = $_SESSION['sns_type'];
	}else{
		error_tips(1099);//非法操作
		exit;
	}
	$smarty->assign('action',$action);
	$smarty->assign('account',$account);
	$smarty->assign('nickname',$user_info['usernick']);
	$smarty->assign('login_type',$login_type);
	$smarty->assign('user_type',$user_info['user_type']);
	$smarty->assign('userid',$user_info['userid']);
	$smarty->assign('sns_username',$sns_username);
	$smarty->assign('sns_type',$sns_type);
	$smarty->display('account/binding_tips.html');
?>