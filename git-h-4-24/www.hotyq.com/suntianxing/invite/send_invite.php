<?php
	header("content-type: text/html; charset=utf-8");
	session_start();
	require_once ('../../includes/common_inc.php');
	//require_once COMMON_PATH."/invite.class.php";
	require_once ("./invite.class.php");
	
	$smarty -> display("suntianxing/invite/send_invite.html");