<?php
	header("content-type: text/html; charset=utf-8");
	session_start();
	require_once ('../../includes/common_inc_test.php');
	//require_once COMMON_PATH."/apply.class.php";
	require_once ("./apply.class.php");



	$smarty -> display("suntianxing/apply/send_apply.html");