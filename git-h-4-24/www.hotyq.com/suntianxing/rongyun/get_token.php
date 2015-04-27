<?php
	header("content-type: text/html; charset=utf-8");
	session_start();
	require_once ('../../includes/common_inc.php');
	//require_once COMMON_PATH."/apply.class.php";
	require_once ("./rongyunApi.class.php");



	$smarty -> display("suntianxing/rongyun/get_token.html");