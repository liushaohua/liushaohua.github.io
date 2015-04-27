<?php
	header("Content-type:text/html;charset=utf-8");
	include "../includes/common_inc.php";
	session_start();
	if(isset($_REQUEST['sns_username'])){
		$smarty->assign('sns_username',clear_gpq($_REQUEST['sns_username']));
		$smarty->display('account/binding.html');
	}else{
		error_tips(1099);//非法操作
		exit;
	}
	/*if(isset($_REQUEST['action'])){
		$action = clear_gpq($_REQUEST['action']);
	}else{
		error_tips(1099);//非法操作
		exit;
	}
	$user = new user();
	if($action == "bind_account"){
		$login_type = clear_gpq($_REQUEST['login_type']);
		$account = clear_gpq($_REQUEST['account']);
		$password = clear_gpq($_REQUEST['password']);
		$sns_username = $_SESSION['sns_username'];
		$face = $_SESSION['face'];
		$openid = $_SESSION['openid'];
		$sns_type = $_SESSION['sns_type'];
		if(empty($login_type)){
			error_tips(1099);
			exit;
		}
		if(empty($account)){
			error_tips(1099);
			exit;
		}
		if(empty($password)){
			error_tips(1099);
			exit;
		}
		if(empty($sns_username)){
			error_tips(1099);
			exit;
		}
		if(empty($face)){
			error_tips(1099);
			exit;
		}
		if(empty($openid)){
			error_tips(1099);
			exit;
		}if(empty($sns_type)){
			error_tips(1099);
			exit;
		}
		$state_code = $user -> sns_bind_user($account,$login_type,$password,$openid,$sns_type,$sns_username,$face);
		if($state_code == 1000){
			$smarty->assign('action',$action);
			$smarty->assign('account',$account);
			$smarty->assign('login_type',$login_type);
			$smarty->assign('sns_username',$sns_username);
			$smarty->assign('sns_type',$sns_type);
			$smarty->display('account/binding_tips.html');
		}else{
			error_tips($state_code);
			exit;
		}
	}elseif($action == "create_email_bind_account"){
		$email = clear_gpq($_REQUEST['account']);
		$password = clear_gpq($_REQUEST['password']);
		$re_password = clear_gpq($_REQUEST['re_password']);
		$user_type = clear_gpq($_REQUEST['user_type']);
		$login_type = "email";
		$source = "web";
		$sns_username = $_SESSION['sns_username'];
		$face = $_SESSION['face'];
		$openid = $_SESSION['openid'];
		$sns_type = $_SESSION['sns_type'];
		if(empty($sns_username)){
			error_tips(1099);
			exit;
		}
		if(empty($face)){
			error_tips(1099);
			exit;
		}
		if(empty($openid)){
			error_tips(1099);
			exit;
		}if(empty($sns_type)){
			error_tips(1099);
			exit;
		}
		if(empty($user_type)){
			error_tips(1099);
			exit;
		}
		if(empty($email)){
			error_tips(1099);
			exit;
		}
		if(empty($password)){
			error_tips(1099);
			exit;
		}
		if(empty($re_password)){
			error_tips(1099);
			exit;
		}
		if($password !== $re_password){
			error_tips(1099);
			exit;				
		}
		$state_code = $user->check_email($email);
		if($state_code !== 1000){
			error_tips($state_code);
			exit;
		}
		$state_code = $user -> email_exist($email);
		if($state_code == 1000){
			error_tips(1010);
			exit;	
		}
		$state_code = $user -> check_password($password);
		if($state_code !== 1000){
			error_tips($state_code);
			exit;
		}
		$state_code = $user->add_email_user($user_type,$email,$password,$source);
		if($state_code == 1000){
			$state_code = $user -> sns_bind_user($email,$login_type,$password,$openid,$sns_type,$sns_username,$face);
			if($state_code == 1000){
				$smarty->assign('action',$action);
				$smarty->assign('account',$email);
				$smarty->assign('login_type',$login_type);
				$smarty->assign('sns_username',$sns_username);
				$smarty->assign('sns_type',$sns_type);
				$smarty->display('account/binding_tips.html');
			}else{
				error_tips($state_code);
				exit;
			}
		}else{
			error_tips($state_code);
			exit;
		}
	
	}*/
?>