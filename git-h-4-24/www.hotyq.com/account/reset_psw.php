<?php
	header("content-type: text/html; charset=utf-8");
	require_once ('../includes/common_inc.php');
	//接收传递的值 判断是邮箱还是手机  显示不同的div
	
	//1点击邮件 验证  验证码是否匹配
	//根据传递过来的uid获取 用户的 login_type account
	if(!isset($_GET['uid']) || !is_numeric($_GET['uid']) || $_GET['uid'] <= 0){
		error_tips('1099');exit;
	}
	if(!isset($_GET['forget_code'])){
		error_tips('1099');exit;
	}
	$uid = intval($_GET['uid']);
	$forget_code = clear_gpq($_GET['forget_code']);
	$user = new user;
	$userinfo = $user->get_userinfo($uid);
	$login_type = $userinfo['login_type'];
	$login_type = 'email';
	if(!isset($login_type)){
		error_tips('1099');exit;
	}
	$account = $userinfo[$login_type];
	
	$_GET['uid'] = $uid; 
	$_GET['forget_code'] = $forget_code; 
	$_GET['login_type'] = $login_type; 
	$_GET['account'] = $account; 
	
	//$user->update_psw_email($uid,$new_password,$email_forget_code);
	//2匹配的话  呈现页面
	
	//3submit发送修改后的密码
	$smarty -> assign('account_info',$_GET);
	$smarty -> display("account/forget_3.html")
?>