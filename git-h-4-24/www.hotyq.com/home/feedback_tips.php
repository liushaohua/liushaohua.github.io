<?php
	header("content-type: text/html; charset=utf-8");
	session_start();
	require_once ('../includes/common_home_inc.php');
	$base = new base();
	$message = new message;
	$uid = $user_info["id"];
	
	//echo '意见反馈页'; 
	$smarty -> display("home/feedback_tips.html");
?>