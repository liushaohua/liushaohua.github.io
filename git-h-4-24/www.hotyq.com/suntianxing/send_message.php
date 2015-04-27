<?php
	header("content-type: text/html; charset=utf-8");
	require_once('../includes/common_inc_test.php');
	require_once('./message.class.php');
	session_start();
	
	
	$smarty -> display("suntianxing/send_message.html");