<?php
	header("content-type: text/html; charset=utf-8");
	session_start();
	require_once ('../includes/common_inc.php');
    $user = new user();
	$info =  $user -> get_cookie_user_info();
	//session userid usernick   cookie  id|user_type|nickname|level|data_percent|cipher
	if($info['user_type'] == 'mobile'){
		//判断是否为手机用户
		header("location:/"); 
		exit;
	}
	//判断是否登录
	if(empty($info['userid']) || empty($info['user_token'])){
		header("location:/account/login"); 
		exit;
    }
	if(!empty($info["usernick"])){
    	header("location:http://www.hotyq.com");
		exit;        
    }    
	$userid = $info['userid'];
	$usertype = $info['user_type'];	
	$result = $user -> get_userinfo($userid);
	$account = $result['email'];
	$smarty->assign('userid',$userid);
	$smarty->assign('usertype',$usertype);
	$smarty->assign('account',$account);
	$smarty->display('account/success.html');

?>
