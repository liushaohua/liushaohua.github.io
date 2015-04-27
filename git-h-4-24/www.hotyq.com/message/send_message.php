<?php
	header("content-type: text/html; charset=utf-8");
	require('../includes/common_inc.php');
	session_start();
	
	
	$smarty -> display("message/send_message.html");