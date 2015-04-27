<?php
header("content-type: text/html; charset=utf-8");
require_once('../includes/common_home_inc.php');
$user = new user();
$cookie = $user -> get_cookie_user_info();
//session userid usernick   cookie  id|user_type|nickname|level|data_percent|cipher
	if($cookie[1] == 'user'){
		//跳转到红名片
		header("location:/user/home/user_card"); 
		exit;
	}elseif($cookie[1] == 'org'){
		//跳转机构红名片
		header("location:/user/home/org_card"); 
		exit;
	}else{
		error_tips('1099');exit;
	}
?>