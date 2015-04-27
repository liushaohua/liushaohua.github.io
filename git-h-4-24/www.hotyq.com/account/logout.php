<?php
	session_start();
	header("content-type: text/html; charset=utf-8");
	require('../includes/common_inc.php');
	
	//1 清除cookie session
	$user = new user;
	$user -> delete_cookie_user_info();
	//2 跳回上一页面   判断是否是home页面
	if(!isset($_SERVER['HTTP_REFERER']) || empty($_SERVER['HTTP_REFERER']) ){
		header("location:http://www.hotyq.com");
	}else{
		$pos  =  strpos($_SERVER['HTTP_REFERER'],'/home');
		if($pos){
			//需要登陆页面
			//header("location:/account/login");
			//跳到网站首页
			header("location:http://www.hotyq.com");
		}else{
			//echo "<script>location.href = {$_SERVER['HTTP_REFERER']}</script>";
			header("location:{$_SERVER['HTTP_REFERER']}");
		}
	}
?>